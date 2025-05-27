<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getWeather($city)
    {
        $url = env('WEATHER_API_URL');
        $apiKey = env('WEATHER_API_KEY');

        try {
            $response = $this->client->get($url, [
                'query' => [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]
            ]);

            return json_decode($response->getBody(), true);

        } catch(ConnectException $e) {
            // connection faild (no internet)
            Log::error('Connection Error: ' . $e->getMessage());
            throw new \Exception('Failed to connect to the weather service. Please check your internet connection.');

        } catch (RequestException $e) {
            // request error (404)
            Log::error('Request Error: ' . $e->getMessage());
            throw new \Exception('An error occurred while fetching weather data. Please try again later.');

    }   catch (GuzzleException $e) {
        // general quzzle errors
        Log::error('Guzzle Error: ' . $e->getMessage());
        throw new \Exception('Unable to retrieve weather information due to an internal error. Please try again.');
    }
  }
}