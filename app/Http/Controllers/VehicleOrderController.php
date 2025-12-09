<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transport;
use App\Http\Requests\VehicleOrderRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class VehicleOrderController extends Controller
{
    public function create(string $id)
    {
        $transport = Transport::findOrFail($id);
        return view('vehicles.order', compact('transport'));
    }

    public function store(VehicleOrderRequest $request, $id)
    {
        // get transport with vehicles
        $transport = Transport::with('vehicles')->findOrFail($id);

        // store required passengers
        $requiredPassengers = $request->passengers;

        // pickup datetime
        $pickupDatetime = Carbon::parse($request->pickup_datetime ?? now());

        // distance + duration from leaflet(form)
        $distance = $request->distance; // km
        $duration = $request->duration; // minutes

        // dropoff calculated from duration
        $dropoffDatetime = $pickupDatetime->copy()->addMinutes($duration);

        //vehicles filter(considering overlapped reservations )
        $availableVehicles = $transport->vehicles()
        ->where('max_passengers', '>=', $requiredPassengers)
        ->whereDoesntHave('reservations', function($q) use ($pickupDatetime, $dropoffDatetime) {
        $q->where(function($query) use ($pickupDatetime, $dropoffDatetime) {
            $query->whereBetween('pickup_datetime', [$pickupDatetime, $dropoffDatetime])
                  ->orWhereBetween('dropoff_datetime', [$pickupDatetime, $dropoffDatetime])
                  ->orWhere(function($sub) use ($pickupDatetime, $dropoffDatetime) {
                      $sub->where('pickup_datetime', '<', $pickupDatetime)
                          ->where('dropoff_datetime', '>', $dropoffDatetime);
                  });
        });
    })
    ->get();


        if ($availableVehicles->isEmpty()) {
            return view('transport.index')
                ->with('error', 'No available vehicles for the selected time.');
        }

        // geocoding service
        $geocoding = app(\App\Services\GeocodingService::class);
        $pickupCoords = $geocoding->geocodeAddress($request->pickup_location);
        $dropoffCoords = $geocoding->geocodeAddress($request->dropoff_location);

        return view('vehicles.index', [
            'availableVehicles' => $availableVehicles,
            'pickup_location'   => $request->pickup_location,
            'dropoff_location'  => $request->dropoff_location,
            'pickup_datetime'   => $pickupDatetime->format('Y-m-d\TH:i'),
            'passengers'        => $requiredPassengers,
            'pickupCoords'      => $pickupCoords,
            'dropoffCoords'     => $dropoffCoords,
            'distance'          => $distance,
            'duration'          => $duration,
        ]);
    }



}
