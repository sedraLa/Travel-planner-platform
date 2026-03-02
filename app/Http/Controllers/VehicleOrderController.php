<?php

namespace App\Http\Controllers;
use App\Http\Requests\VehicleOrderRequest;
use App\Jobs\ProcessReservationDriverMatchingJob;
use App\Models\TransportReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleOrderController extends Controller
{
    public function create()
    {

        return view('vehicles.order');
    }





    //send request and filter vehicles
    public function store(VehicleOrderRequest $request)
    {
        $reservation = TransportReservation::create([
            'user_id' => Auth::id(),
            'pickup_location' => $request->pickup_location,
            'dropoff_location' => $request->dropoff_location,
            'pickup_datetime' => $request->pickup_datetime,
            'dropoff_datetime' => $request->pickup_datetime,
            'passengers' => $request->passengers,
            'total_price' => 0,
            'status' => 'pending_payment',
            'driver_status' => 'pending',
        ]);

        ProcessReservationDriverMatchingJob::dispatch($reservation->id);

        return redirect()->route('vehicle.searching', $reservation);
    }

    public function searching(TransportReservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);
        return view('vehicles.searching', compact('reservation'));
    }


    public function status(TransportReservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        $redirectUrl = null;
        if ($reservation->status === 'driver_assigned') {
            $redirectUrl = route('vehicle.assigned', $reservation);
        }
        if ($reservation->status === 'cancelled') {
            $redirectUrl = route('vehicle.order');
        }
        return response()->json([
            'status' => $reservation->status,
            'reservation_id' => $reservation->id,
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function assigned(TransportReservation $reservation, GeocodingService $geocoding)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        $reservation->load('driver.user', 'vehicle');

        if (!$reservation->vehicle) {
            return redirect()->route('vehicle.searching', $reservation)->withErrors('No assigned vehicle yet.');
        }

        $pickupCoords = $geocoding->geocodeAddress($reservation->pickup_location);
        $dropoffCoords = $geocoding->geocodeAddress($reservation->dropoff_location);

        return view('vehicles.index', [
            'availableVehicles' => collect([$reservation->vehicle]),
            'pickup_location' => $reservation->pickup_location,
            'dropoff_location' => $reservation->dropoff_location,
            'pickup_datetime' => optional($reservation->pickup_datetime)->format('Y-m-d\TH:i'),
            'passengers' => $reservation->passengers,
            'pickupCoords' => $pickupCoords,
            'dropoffCoords' => $dropoffCoords,
            'selectedCategory' => $reservation->vehicle->category,
            'selectedType' => $reservation->vehicle->type,
            'reservation' => $reservation,
        ]);
    }
}


