<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Flight;
use Illuminate\Support\Facades\Http;

class FlightController extends Controller
{
    public function showFlightForm() {
        $destinations = Destination::all();
        return view('flights.search',compact('destinations'));
    }

    public function searchFlights(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|exists:destinations,id',
            'to-country' => 'required|exists:destinations,id',
            'departure' => 'required|date',
            'return' => 'nullable|date|after_or_equal:departure',
            'seats' => 'required|integer|min:1',
            'trip-type' => 'required|in:one-way,round',
        ]);

        if ($validated['trip-type'] === 'round' && empty($validated['return'])) {
            return back()->withErrors(['return' => 'Return date is required for round trips.'])->withInput();
        }

        $from = Destination::find($validated['country']);
        $to = Destination::find($validated['to-country']);

        //get access token from amadeus
        $authResponse = Http::asForm()->post('https://test.api.amadeus.com/v1/security/oauth2/token', [
            'grant_type' => 'client_credentials',
            'client_id' => config('services.amadeus.client_id'),
            'client_secret' => config('services.amadeus.client_secret'),
        ]);

        if ($authResponse->failed()) {
            return back()->with('error', 'Failed to authenticate with Amadeus API.');
        }

        $accessToken = $authResponse->json()['access_token'];

        // Go to flights
        $outboundFlights = $this->transformFlights(
            Http::withToken($accessToken)
                ->get('https://test.api.amadeus.com/v2/shopping/flight-offers', [
                    'originLocationCode' => $from->iata_code,
                    'destinationLocationCode' => $to->iata_code,
                    'departureDate' => $validated['departure'],
                    'adults' => $validated['seats'],
                    'currencyCode' => 'USD',
                    'max' => 10,
                ])->json()['data'] ?? []
        );

        //return flights
        $inboundFlights = [];
        if ($validated['trip-type'] === 'round') {
            $inboundFlights = $this->transformFlights(
                Http::withToken($accessToken)
                    ->get('https://test.api.amadeus.com/v2/shopping/flight-offers', [
                        'originLocationCode' => $to->iata_code,
                        'destinationLocationCode' => $from->iata_code,
                        'departureDate' => $validated['return'],
                        'adults' => $validated['seats'],
                        'currencyCode' => 'USD',
                        'max' => 10,
                    ])->json()['data'] ?? []
            );
        }

        return view('flights.results', [
            'outboundFlights' => $outboundFlights,
            'inboundFlights' => $inboundFlights,
            'tripType' => $validated['trip-type'],
        ]);

    }

    private function transformFlights(array $data): array
    {
        $flights = [];

        foreach ($data as $offer) {
            $segment = $offer['itineraries'][0]['segments'][0];

            $flights[] = [
                'carrierCode' => $segment['carrierCode'] ?? 'Not available',   //Flight company code
                'flightNumber' => $segment['number'] ?? 'Not available',
                'from' => $segment['departure']['iataCode'] ?? 'Not available',
                'departureTime' => $segment['departure']['at'] ?? 'Not available',
                'to' => $segment['arrival']['iataCode'] ?? 'Not available',
                'arrivalTime' => $segment['arrival']['at'] ?? 'Not available',
                'price' => $offer['price']['total'] ?? 'Not available',
            ];
        }

        return $flights;
    }



    }

