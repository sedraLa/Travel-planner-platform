<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transport;
use App\Http\Requests\VehicleOrderRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Models\TransportVehicle;

class VehicleOrderController extends Controller
{
    public function create()
    {
       
        return view('vehicles.order');
    }




    
    //send request and filter vehicles
    public function store(VehicleOrderRequest $request)
    {
       

        // store request informations
        $requiredPassengers = $request->passengers;

        $pickupDatetime = Carbon::parse($request->pickup_datetime ?? now());

        // dropoff calculated later(frontend)
        $dropoffDatetime = $pickupDatetime->copy(); 

        //vehicles filter(considering overlapped reservations )
        $availableVehicles =TransportVehicle::where('max_passengers', '>=', $requiredPassengers)
            ->whereDoesntHave('reservations', function($q) use ($pickupDatetime, $dropoffDatetime) {
                $q->where(function($query) use ($pickupDatetime, $dropoffDatetime) {
                    //reservation exist during request time 
                    $query->whereBetween('pickup_datetime', [$pickupDatetime, $dropoffDatetime])
                    //reservation exist during dropoff time 8 -> 8:35 , request at 8:34 (not available)
                          ->orWhereBetween('dropoff_datetime', [$pickupDatetime, $dropoffDatetime])
                          //request inside reservations 8->9 , request at 8:15 -> 8:45 (not available)
                          ->orWhere(function($sub) use ($pickupDatetime, $dropoffDatetime) {
                              $sub->where('pickup_datetime', '<', $pickupDatetime)
                                  ->where('dropoff_datetime', '>', $dropoffDatetime);
                          });
                });
            })
            ->get();

        if ($availableVehicles->isEmpty()) {
            return redirect()
                ->route('vehicle.order')
                ->with('vehicle_error', 'No available vehicles for the required date and time.');
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
        ]);
    }
}
