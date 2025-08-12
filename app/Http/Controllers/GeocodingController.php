<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Services\GeocodingService;

class GeocodingController extends Controller
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }   

    public function geocodeHotel($id)
    {
        $hotel = Hotel::findOrFail($id);

        $fullAddress = implode(', ', array_filter([
            $hotel->name,
            $hotel->address,
            $hotel->city,
            $hotel->country
        ]));

        $coords = $this->geocodingService->geocodeAddress($fullAddress);

        if (! $coords) {
            return response()->json(['error' => 'Location not found'], 404);
        }

        return response()->json($coords);
    }
}
