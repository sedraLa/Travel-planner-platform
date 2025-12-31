<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Requests\ReservationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use Carbon\Carbon;

class ReservationController extends Controller
{
   public function showReservationForm($id)
{
    $hotel = Hotel::findOrFail($id);
    return view('reservation.create', compact('hotel'));
}
public function store(ReservationRequest $request)
{

    $hotel = Hotel::findOrFail($request->hotel_id);

    $checkIn = Carbon::parse($request->check_in_date);
    $checkOut = Carbon::parse($request->check_out_date);

    // check reserved rooms in required period
    $overlappingReservations = Reservation::where('hotel_id', $hotel->id)
        ->where(function ($query) use ($checkIn, $checkOut) {
            $query->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn);   //old reservation overlapped with new one
        })
        ->sum('rooms_count');   //reserved rooms count

    // available rooms in this period
    $availableRooms = $hotel->total_rooms - $overlappingReservations;

    // check if there is enough rooms
    if ($availableRooms < $request->rooms_count) {
        return redirect()->back()->withErrors(['Not enough rooms available for the selected dates!']);
    }

    // calculate nights between in and out dates
    $days = max(1, $checkIn->diffInDays($checkOut)); // 1 means make sure that nights number at least = one night
    //calculate total price
    $room_price = $hotel->price_per_night;
    $total_price = $days * $request->rooms_count * $room_price;


    // create the reservation
    $reservation = Reservation::create([
        'user_id' => Auth::check() ? Auth::id() : null,
        'hotel_id' => $request->hotel_id,
        'check_in_date' => $request->check_in_date,
        'check_out_date' => $request->check_out_date,
        'rooms_count' => $request->rooms_count,
        'guest_count' => $request->guest_count,
        'total_price' => $total_price,
        'reservation_status' => 'pending',
    ]);



    return redirect()->route('reservations.pay', $reservation->id)->with('success', 'Thanks, Your reservation now is pending please click on pay. Total cost: $' . $total_price);
}

//pay

public function pay($reservationId) {
    $reservation = Reservation::findOrFail($reservationId);
    if (Auth::id() !== $reservation->user_id) {
        abort(403,'Unauthorized action');
    }
    return view('reservation.pay',compact('reservationId','reservation'));
}


public function index(Request $request)
{
    $query = Reservation::with(['hotel', 'hotel.destination', 'user']);

    $isAdmin = Auth::check() && Auth::user()->role === 'admin';

    // ✅ المستخدم يشوف حجوزاته فقط
    if (!$isAdmin) {
        $query->where('user_id', Auth::id());
    }

    /* =======================
       Keyword search
    ======================= */
    if ($request->filled('keyword')) {
        $term = trim($request->keyword);

        $query->where(function ($q) use ($term, $isAdmin) {

            $q->whereHas('hotel', fn ($h) =>
                $h->where('name', 'like', "%{$term}%")
            )
            ->orWhereHas('hotel.destination', fn ($d) =>
                $d->where('name', 'like', "%{$term}%")
            );

            // ✅ بحث بالمستخدم فقط للأدمن
            if ($isAdmin) {
                $q->orWhereHas('user', function ($u) use ($term) {
                    $u->where('name', 'like', "%{$term}%")
                      ->orWhere('last_name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%");
                });
            }
        });
    }

    /* =======================
       Date filters
    ======================= */
    if ($request->filled('month')) {
        $query->whereMonth('check_in_date', $request->month);
    }

    if ($request->filled('year')) {
        $query->whereYear('check_in_date', $request->year);
    }

    /* =======================
       Status filter
    ======================= */
    if ($request->filled('reservation_status')) {
        $query->where('reservation_status', $request->reservation_status);
    }

    $reservations = $query->latest()->get();

    return view('reservation.index', compact('reservations'));
}
}
