<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Destination;

class AmadeusFlightService
{
    private function getAccessToken(): string
    {
        $response = Http::asForm()->post(
            'https://test.api.amadeus.com/v1/security/oauth2/token',
            [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.amadeus.client_id'),
                'client_secret' => config('services.amadeus.client_secret'),
            ]
        );

        if ($response->failed()) {
            throw new \Exception('Amadeus authentication failed');
        }

        return $response->json()['access_token'];
    }

    //send request
    public function searchFlights(
        Destination $from,
        Destination $to,
        string $date,
        int $seats
    ): array {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)->get(
            'https://test.api.amadeus.com/v2/shopping/flight-offers',
            [
                'originLocationCode' => $from->iata_code,
                'destinationLocationCode' => $to->iata_code,
                'departureDate' => $date,
                'adults' => $seats,
                'currencyCode' => 'USD',
                'max' => 10,
            ]
        );

        //send data
        return $this->transformFlights($response->json()['data'] ?? []);
    }

    //format data
    private function transformFlights(array $data): array
    {
        $flights = [];

        foreach ($data as $offer) {
            $segment = $offer['itineraries'][0]['segments'][0] ?? [];

            $flights[] = [
                'carrierCode'   => $segment['carrierCode'] ?? 'Not available',
                'flightNumber'  => $segment['number'] ?? 'Not available',
                'from'          => $segment['departure']['iataCode'] ?? 'Not available',
                'departureTime' => $segment['departure']['at'] ?? 'Not available',
                'to'            => $segment['arrival']['iataCode'] ?? 'Not available',
                'arrivalTime'   => $segment['arrival']['at'] ?? 'Not available',
                'price'         => $offer['price']['total'] ?? 'Not available',
            ];
        }

        return $flights;
    }
}