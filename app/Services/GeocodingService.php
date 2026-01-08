<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeocodingService
{
    protected $baseUrl = 'https://nominatim.openstreetmap.org/search';
    protected $userAgent;


    public function geocodeAddress(string $fullAddress) : ?array
    {
        // Generate a unique cache key for storing/retrieving geocoding results
        $cacheKey  = 'geocode_' . md5($fullAddress);

        //try to get data from cache first

        $cachedCoords = Cache::get($cacheKey);
        if($cachedCoords) {
            return $cachedCoords;   //return without new request if exist in cache
        }
            //If not exist in cache send request
        try {
            $this->userAgent = config('services.nominatim.user_agent', 'MyTravelPlanningApp (default@example.com)');
            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent
            ])->get($this->baseUrl, [
                'format' => 'json', //output format
                'q' => $fullAddress, //query parameter
                'limit' => 1
            ]);

            $data = $response->json(); //convert json to array


            if (! $data || count($data) === 0) {
                return null;
            }

            $coords = [
                'latitude' => $data[0]['lat'],
                'longitude' => $data[0]['lon'],
                'display_name' => $data[0]['display_name'] ?? $fullAddress
            ];

            //store data in cache for 30 days

            Cache::put($cacheKey,$coords,now()->addDays(30));
            return $coords;

        } catch (ConnectionException $e) {
            Log::error('Connection error: ' . $e->getMessage());
            return null;
        } catch (RequestException $e) {
            Log::error('Request error: ' . $e->getMessage());
            return null;
        }
    }
}
