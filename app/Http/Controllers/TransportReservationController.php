<?php

namespace App\Http\Controllers;
use App\Http\Requests\VehicleOrderRequest;
use Illuminate\Http\Request;
use App\Models\TransportReservation;
use Illuminate\Support\Facades\Auth;
use App\Services\GeocodingService;
use Carbon\Carbon;


class TransportReservationController extends Controller
{
    /**
     * Show the reservation page for a vehicle. (complete reservation)
     */
    public function create(Request $request, TransportReservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        if ($reservation->status !== 'driver_assigned' || !$reservation->vehicle) {
            return redirect()->route('vehicle.searching', $reservation)->withErrors('No assigned vehicle yet.');
        }

        $distance = (float) $request->query('distance', 0);
        $duration = (int) $request->query('duration', 0);

        if ($distance > 0) {
            $totalPrice = ($distance * $reservation->vehicle->price_per_km) + $reservation->vehicle->base_price;
            $dropoffDateTime = Carbon::parse($reservation->pickup_datetime)->addMinutes($duration > 0 ? $duration : 0);


        $reservation->update([
                'total_price' => $totalPrice,
                'dropoff_datetime' => $dropoffDateTime,
            ]);
        }

        $reservation->load('vehicle');

        return view('vehicles.reservation', [
            'reservation' => $reservation,
            'vehicle' => $reservation->vehicle,
        ]);

    }

    /**
     * Store a new reservation.
     */
    public function store(VehicleOrderRequest $request, TransportReservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        if ($reservation->status !== 'driver_assigned') {
            return back()->withErrors('Reservation is not ready for payment.');
        }

        $reservation->update([
            'pickup_location' => $request->pickup_location,
            'dropoff_location' => $request->dropoff_location,
            'pickup_datetime' => $request->pickup_datetime,
            'passengers' => $request->passengers,
            'total_price' => $request->total_price,
        ]);

        session(['transport_reservation_id' => $reservation->id]);

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
