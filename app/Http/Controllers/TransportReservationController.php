<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\TransportVehicle;
use App\Models\Transport;
use App\Models\TransportReservation;
use Illuminate\Support\Facades\Auth;;
use App\Http\Requests\VehicleOrderRequest;



class TransportReservationController extends Controller
{
    public function create(Request $request, $vehicleId) {
        $vehicle = TransportVehicle::findOrFail($vehicleId);
    
        $pickup_location = $request->query('pickup_location');
        $dropoff_location = $request->query('dropoff_location');
        $pickup_datetime = $request->query('pickup_datetime');
        $passengers = $request->query('passengers');
    
        return view('vehicles.reservation', compact(
            'vehicle', 'pickup_location', 'dropoff_location', 'pickup_datetime', 'passengers'
        ));
    }

    public function store(VehicleOrderRequest $request, $transportId, $vehicleId) {
        $transport=Transport::findOrFail($transportId);
        $vehicle= TransportVehicle::findOrFail($vehicleId);
        $reservation= TransportReservation::create([
            'transport_id'=> $transportId,
            'user_id'=> Auth::id(),
            'transport_vehicle_id' => $vehicleId,
            'pickup_location' => $request->pickup_location,
            'dropoff_location' => $request->dropoff_location,
            'pickup_datetime' => $request->pickup_datetime,
            'passengers' => $request->passengers,
            'status' => 'pending',
            'total_price'=>100,
        ]);
        return redirect()->route('vehicles.pay', $reservation->id)
        ->with('success', 'Reservation created successfully. Continue to payment.');
}

public function pay($reservationId)
{
    $reservation = TransportReservation::findOrFail($reservationId);
    if (Auth::id() !== $reservation->user_id) {
        abort(403,'Unauthorized action');
    }
    return view('vehicles.pay', compact('reservation'));
}
}
