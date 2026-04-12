<?php

namespace App\Http\Controllers;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Services\GeocodingService;

class UserTripController extends Controller
{

public function index(){
     $trips = Trip::where('status', 'published')
        ->latest()
        ->get();

    return view('trips.user.index', compact('trips'));
   }


   public function show(string $id, GeocodingService $geo)
{
    $trip = Trip::with([
        'days.activities.activity',
        'days.hotel',
        'packages.highlights',
        'packages.includes',
        'packages.excludes',
        'packages.packageHotels.hotel',
        'packages.infos',
        'schedules',
        'assignedGuide',
        'assignments.guide',
        'transports',
        'primaryDestination',
        'images',
    ])->findOrFail($id);

    // ================================
    // Meeting point geocoding
    // ================================
    $fullAddress = implode(', ', array_filter([
        $trip->meeting_point_address,
        $trip->primaryDestination?->name,
        $trip->primaryDestination?->city,
        $trip->primaryDestination?->country,
    ]));

    $coords = null;

    if ($fullAddress) {
        $coords = $geo->geocodeAddress($fullAddress);
    }

    // fallback 1
    if (!$coords && $trip->primaryDestination) {
        $coords = $geo->geocodeAddress(
            $trip->primaryDestination->city . ', ' . $trip->primaryDestination->country
        );
    }

    // fallback 2
    if (!$coords && $trip->meeting_point_address) {
        $coords = $geo->geocodeAddress($trip->meeting_point_address);
    }

    // final fallback
    $coords = $coords ?? [
        'latitude' => null,
        'longitude' => null
    ];

    // ================================
    // lowest package price (لو بدك تستخدمه)
    // ================================
    $lowestPkg = $trip->packages->sortBy('price')->first();

    return view('trips.user.show', compact('trip','coords','lowestPkg'));
   }
}
