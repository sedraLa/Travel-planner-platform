<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\TransportVehicle;
use App\Models\Transport;
use App\Models\TransportReservation;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\VehicleOrderRequest;
use App\Services\GeocodingService;


class TransportReservationController extends Controller
{
    public function create(Request $request, $vehicleId) {
        $vehicle = TransportVehicle::findOrFail($vehicleId);

        //recieve data
        $pickup_location = $request->query('pickup_location');
        $dropoff_location = $request->query('dropoff_location');
        $pickup_datetime = $request->query('pickup_datetime');
        $passengers = $request->query('passengers');
        $distance = $request->query('distance');
        $duration = $request->query('duration');

        // calculate price
        $pricePerKm = $vehicle->price_per_km;
        $basePrice  = $vehicle->base_price;

        $distance = (float) $request->query('distance', 0);

        $total_price = ($distance * $pricePerKm) + $basePrice;

        //coordinates
        $geocoding = app(GeocodingService::class);
        $pickupCoords = $geocoding->geocodeAddress($pickup_location);
        $dropoffCoords = $geocoding->geocodeAddress($dropoff_location);

        return view('vehicles.reservation', compact(
            'vehicle', 'pickup_location', 'dropoff_location',
            'pickup_datetime', 'passengers', 'distance', 'duration', 'total_price', 'pickupCoords', 'dropoffCoords'

        ));
    }

    public function store(VehicleOrderRequest $request, $transportId, $vehicleId) {
        $transport=Transport::findOrFail($transportId);
        $vehicle= TransportVehicle::findOrFail($vehicleId);

        // recieve data
        $pickup_location = $request->pickup_location;
        $dropoff_location = $request->dropoff_location;
        $pickup_datetime = $request->pickup_datetime;
        $passengers = $request->passengers;
        $distance = $request->distance;
        $duration = $request->duration;

        //price details
        $pricePerKm=$vehicle->price_per_km;
        $basePrice=$vehicle->base_price;

        $distance = (float) $request->distance;
        //calculate final price again for security
        $total_price = ($distance * $vehicle->price_per_km) + $vehicle->base_price;

        $reservation = TransportReservation::create([
            'transport_id'=> $transportId,
            'user_id'=> Auth::id(),
            'transport_vehicle_id' => $vehicleId,
            'pickup_location' => $pickup_location,
            'dropoff_location' => $dropoff_location,
            'pickup_datetime' => $pickup_datetime,
            'passengers' => $passengers,
            'status' => 'pending',
            'total_price' => $total_price,
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
