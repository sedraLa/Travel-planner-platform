<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransportVehicle;
use App\Models\Transport;
use App\Models\TransportReservation;
use App\Services\PaypalPaymentService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\VehicleOrderRequest;
use App\Services\GeocodingService;
use Carbon\Carbon;

class TransportReservationController extends Controller
{
    /**
     * Show the reservation page for a vehicle.
     */
    public function create(Request $request, $vehicleId)
    {
        $vehicle = TransportVehicle::findOrFail($vehicleId);

        // Receive data from query parameters
        $pickup_location = $request->query('pickup_location');
        $dropoff_location = $request->query('dropoff_location');
        $pickup_datetime = Carbon::parse($request->query('pickup_datetime')); // Carbon object
        $passengers = $request->query('passengers');
        $distance = (float) $request->query('distance', 0);
        $duration = $request->query('duration');

        // Calculate total price
        $total_price = ($distance * $vehicle->price_per_km) + $vehicle->base_price;

        // Geocode pickup & dropoff locations
        $geocoding = app(GeocodingService::class);
        $pickupCoords = $geocoding->geocodeAddress($pickup_location);
        $dropoffCoords = $geocoding->geocodeAddress($dropoff_location);

        return view('vehicles.reservation', [
            'vehicle'          => $vehicle,
            'pickup_location'  => $pickup_location,
            'dropoff_location' => $dropoff_location,
            'pickup_datetime'  => $pickup_datetime->format('Y-m-d\TH:i'), // For datetime-local input
            'passengers'       => $passengers,
            'distance'         => $distance,
            'duration'         => $duration,
            'total_price'      => $total_price,
            'pickupCoords'     => $pickupCoords,
            'dropoffCoords'    => $dropoffCoords,
        ]);
    }

    /**
     * Store a new reservation.
     */
    public function store(VehicleOrderRequest $request, $transportId, $vehicleId)
    {
        $transport = Transport::findOrFail($transportId);
        $vehicle = TransportVehicle::findOrFail($vehicleId);

        // Receive form data
        $pickup_location = $request->pickup_location;
        $dropoff_location = $request->dropoff_location;
        $pickupDatetime = Carbon::parse($request->pickup_datetime);
        $durationMinutes = (int) $request->duration;
        $dropoffDatetime = $pickupDatetime->copy()->addMinutes($durationMinutes);
        $passengers = $request->passengers;
        $distance = (float) $request->distance;

        // Calculate total price
        $total_price = ($distance * $vehicle->price_per_km) + $vehicle->base_price;

        $paymentData = [
            'transport_id' => $transportId,
            'vehicle_id' => $vehicleId,
            'pickup_location' => $pickup_location,
            'dropoff_location' => $dropoff_location,
            'pickup_datetime' => $pickupDatetime,
            'dropoff_datetime' => $dropoffDatetime,
            'passengers' => $passengers,
            'total_price' => $total_price,
            'driver_id' => $request->driver_id,
        ];

        // تخزين مؤقت بالـ session
        session(['transport_reservation_data' => $paymentData]);

        // إنشاء كائن وهمي مؤقت ليوافق sendPayment
        $tempReservation = new class($total_price) {
            public $id = 0;
            public $total_price;
            public function __construct($total_price) { $this->total_price = $total_price; }
        };

        $paypal = new PaypalPaymentService();
        $response = $paypal->sendPayment($tempReservation, 'transport');

        if ($response['success']) {
            return redirect()->away($response['url']); // توجيه مباشرة لصفحة الدفع
        }

        return back()->withErrors('Payment initiation failed.');
    }


}
