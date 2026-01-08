<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransportVehicle;
use App\Models\Transport;
use App\Models\TransportReservation;
use App\Services\Payments\PaymentContext;
use App\Services\Payments\PaypalPaymentService;
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

        // Calculate total price , for view
        $total_price = ($distance * $vehicle->price_per_km) + $vehicle->base_price;


        return view('vehicles.reservation', [
            'vehicle'          => $vehicle,
            'pickup_location'  => $pickup_location,
            'dropoff_location' => $dropoff_location,
            'pickup_datetime'  => $pickup_datetime->format('Y-m-d\TH:i'), // For datetime-local input
            'passengers'       => $passengers,
            'distance'         => $distance,
            'duration'         => $duration,
            'total_price'      => $total_price,
        ]);
    }

    /**
     * Store a new reservation.
     */
    public function store(VehicleOrderRequest $request, $transportId, $vehicleId)
{
    $transport = Transport::findOrFail($transportId);
    $vehicle = TransportVehicle::findOrFail($vehicleId);

    $pickupDatetime = Carbon::parse($request->pickup_datetime);
    $dropoffDatetime = $pickupDatetime->copy()->addMinutes((int)$request->duration);

    $distance = (float) $request->distance;
    $total_price = ($distance * $vehicle->price_per_km) + $vehicle->base_price;

    $paymentData = [
        'transport_id' => $transportId,
        'vehicle_id' => $vehicleId,
        'pickup_location' => $request->pickup_location,
        'dropoff_location' => $request->dropoff_location,
        'pickup_datetime' => $pickupDatetime,
        'dropoff_datetime' => $dropoffDatetime,
        'passengers' => $request->passengers,
        'total_price' => $total_price,
        'driver_id' => $request->driver_id,
    ];

    session(['transport_reservation_data' => $paymentData]);

    
    return redirect()->route('vehicles.paypal');
}




    public function index(Request $request)
    {
        $query = TransportReservation::with('user');
    
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
    
        
        if (!$isAdmin) {
            $query->where('user_id', Auth::id());
        }
    
        /* =======================
           Keyword search
        ======================= */
        if ($request->filled('keyword')) {
            $term = trim($request->keyword);
    
            $query->where(function ($q) use ($term, $isAdmin) {
    
                $q->where('pickup_location', 'like', "%{$term}%")
                  ->orWhere('dropoff_location', 'like', "%{$term}%");
    
                if ($isAdmin) {
                    $q->orWhereHas('user', fn ($u) =>
                        $u->where('name', 'like', "%{$term}%")
                    );
                }
            });
        }
    
        /* =======================
           Date filters
        ======================= */
        if ($request->filled('month')) {
            $query->whereMonth('pickup_datetime', $request->month);
        }
    
        if ($request->filled('year')) {
            $query->whereYear('pickup_datetime', $request->year);
        }
    
        /* =======================
           Status filter
        ======================= */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        $reservations = $query
            ->orderBy('pickup_datetime', 'desc')
            ->paginate(10);
    
        return view('transportreservation.index', compact('reservations'));
    }
    
}
