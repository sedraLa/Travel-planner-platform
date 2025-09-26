<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transport;
use App\Http\Requests\VehicleOrderRequest;
use Carbon\Carbon;


class VehicleOrderController extends Controller
{
    public function create(string $id) {
        $transport=Transport::findOrFail($id);
        return view('vehicles.order',compact('transport'));

    }

    public function store(VehicleOrderRequest $request, $id) {
        //get transport vehicles
        $transport=Transport::with('vehicles')->findOrFail($id);

        //store required passengers from user request
        $requiredPassengers = $request->passengers;
        //store required date from user request
        $pickupDatetime = Carbon::parse($request->pickup_datetime ?? now())->format('Y-m-d\TH:i');

        $availableVehicles=$transport->vehicles()
        ->where('max_passengers', '>=', $requiredPassengers)
        ->whereDoesntHave('reservations', function($q) use ($pickupDatetime) {    
                $q->where('pickup_datetime', $pickupDatetime);
        })->get();

        if ($availableVehicles->isEmpty()) {
            
            return view('transport.index')
                ->with('error', 'No available vehicles for the selected time.');
        }
        
        return view('vehicles.index', [
            'availableVehicles' => $availableVehicles,
            'pickup_location' => $request->pickup_location,
            'dropoff_location' => $request->dropoff_location,
            'pickup_datetime' => $pickupDatetime,
            'passengers' => $requiredPassengers,
        ]);
    }

        /*$order= TransportReservation::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'transport_id' => $transport->id,
            'pickup_location'=>$request->pickup_location,
            'dropoff_location'=>$request->dropoff_location,
            'pickup_datetime'=>$request->pickup_datetime,
            'passengers'=>$request->passengers,
            'status'=>'pending',

        ]);*/


    }

