<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function show($city)
    {
        try {
            $weatherData = $this->weatherService->getWeather($city);
            return view('weather', [
                'weather' => $weatherData,
                'errorMessage' => null
            ]);
        } catch (\Exception $e) {
            return view('weather', [
                'weather' => null,
                'errorMessage' => $e->getMessage()
            ]);
        }
    }
    
}
