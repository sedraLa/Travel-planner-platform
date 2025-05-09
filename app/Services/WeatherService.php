<?php

namespace App\Services;

use GuzzleHttp\Client;

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

        $response = $this->client->get($url, [
            'query' => [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric'  
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
