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
    $query = Reservation::with(['hotel','user','hotel.destination']);

    // غير الأدمن يشوف حجوزاته فقط
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        $query->where('user_id', Auth::id());
    }

    // فلترة/بحث
    if ($request->filled('search')) {
        $term  = trim($request->search);
        $month = null;
        $year  = null;

        // 1) سنة فقط: 4 أرقام
        if (ctype_digit($term) && strlen($term) === 4) {
            $year = (int) $term;
        }
        // 2) شهر بس: رقم 1–2
        elseif (ctype_digit($term) && strlen($term) <= 2) {
            $month = (int) $term; // 1..12
        }
        // 3) شهر + سنة: فيه فاصل - أو / (مثل 2025-08 أو 2025/08) أو اسم شهر مع رقم
        elseif (strpos($term, '-') !== false || strpos($term, '/') !== false) {
            try {
                $dt    = Carbon::parse("1 $term");
                $month = $dt->month;
                $year  = $dt->year;
            } catch (\Throwable $e) {}
        }
        // 4) اسم شهر فقط (August / Sep ...)
        else {
            try {
                $dt    = Carbon::parse("1 $term");
                $month = $dt->month;
                // ما مناخد السنة هون
            } catch (\Throwable $e) {}
        }

        $query->where(function ($q) use ($term, $month, $year) {
            // بحث نصّي
            $q->whereHas('hotel', fn($h) =>
                $h->where('name','like',"%{$term}%")
            )
            ->orWhereHas('hotel.destination', fn($d) =>
                $d->where('name','like',"%{$term}%")

            )
            ->orWhereHas('user', fn($u) =>
                $u->where('name','like',"%{$term}%")
                  ->orWhere('last_name','like',"%{$term}%")
                  ->orWhere('email','like',"%{$term}%")
            );

            // فلترة التاريخ
            if ($month !== null && $year !== null) {
                $q->orWhere(function($qq) use ($month, $year) {
                    $qq->whereMonth('check_in_date', $month)
                       ->whereYear('check_in_date',  $year);
                });
            } elseif ($month !== null) {
                $q->orWhereMonth('check_in_date', $month); // بأي سنة
            } elseif ($year !== null) {
                $q->orWhereYear('check_in_date',  $year);  // سنة كاملة
            }
        });
    }

    $reservations = $query->get();
    return view('reservation.index', compact('reservations'));
}


}
