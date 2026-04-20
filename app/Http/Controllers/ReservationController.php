<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Requests\ReservationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
   public function showReservationForm(Request $request, $id)
{
    $hotel = Hotel::with('roomTypes')->findOrFail($id);

    $selectedRoomType = null;
    $roomTypeId = $request->integer('room_type_id');

    if ($roomTypeId) {
        $selectedRoomType = $hotel->roomTypes->firstWhere('id', $roomTypeId);
    }

    $prefill = [
        'check_in_date' => $request->input('check_in_date', $request->input('check_in')),
        'check_out_date' => $request->input('check_out_date', $request->input('check_out')),
        'guest_count' => $request->input('guest_count', $request->input('guests')),
    ];

    return view('reservation.create', compact('hotel', 'selectedRoomType', 'prefill'));
}
public function store(ReservationRequest $request)
{
    $hotel = Hotel::findOrFail($request->hotel_id);

    $checkInInput = $request->input('check_in') ?? $request->input('check_in_date');
    $checkOutInput = $request->input('check_out') ?? $request->input('check_out_date');
    $guests = (int) ($request->input('guests') ?? $request->input('guest_count', 0));
    $roomsCount = (int) ($request->input('rooms_count', 1));
    $roomTypeId = $request->input('room_type_id');

    if (!$checkInInput || !$checkOutInput || $guests < 1) {
        return redirect()->back()->withInput()->withErrors(['Please provide valid booking details.']);
    }

    $checkIn = Carbon::parse($checkInInput);
    $checkOut = Carbon::parse($checkOutInput);
    $days = max(1, $checkIn->diffInDays($checkOut));

    $roomPrice = $hotel->price_per_night;
    $reservationPayload = [
        'user_id' => Auth::check() ? Auth::id() : null,
        'hotel_id' => $hotel->id,
        'check_in_date' => $checkIn->toDateString(),
        'check_out_date' => $checkOut->toDateString(),
        'check_in' => $checkIn->toDateString(),
        'check_out' => $checkOut->toDateString(),
        'rooms_count' => $roomsCount,
        'guest_count' => $guests,
        'guests' => $guests,
        'reservation_status' => 'pending',
    ];

    if ($roomTypeId) {
        $roomType = RoomType::where('hotel_id', $hotel->id)->findOrFail($roomTypeId);

        if ($guests > $roomType->capacity) {
            return redirect()->back()->withInput()->withErrors(['guests' => 'Guests exceed this room type capacity.']);
        }

        $overlappingReservations = Reservation::where('hotel_id', $hotel->id)
            ->where('room_type_id', $roomType->id)
            ->where('reservation_status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where('check_in_date', '<', $checkOut)
                    ->where('check_out_date', '>', $checkIn);
            })
            ->count();

        if ($overlappingReservations >= $roomType->quantity) {
            return redirect()->back()->withInput()->withErrors(['room_type_id' => 'This room type is not available for the selected dates.']);
        }

        $roomPrice = $roomType->price_per_night;
        $reservationPayload['room_type_id'] = $roomType->id;
        $reservationPayload['rooms_count'] = 1;

        $reservation = DB::transaction(function () use ($reservationPayload, $roomType) {
            $created = Reservation::create($reservationPayload + [
                'total_price' => 0,
            ]);

            if ($roomType->quantity > 0) {
                $roomType->decrement('quantity');
            }

            return $created;
        });
    } else {
        // check reserved rooms in required period
        $overlappingReservations = Reservation::where('hotel_id', $hotel->id)
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where('check_in_date', '<', $checkOut)
                    ->where('check_out_date', '>', $checkIn);
            })
            ->sum('rooms_count');

        // available rooms in this period
        $availableRooms = $hotel->total_rooms - $overlappingReservations;

        // check if there is enough rooms
        if ($availableRooms < $roomsCount) {
            return redirect()->back()->withInput()->withErrors(['Not enough rooms available for the selected dates!']);
        }

        $reservation = Reservation::create($reservationPayload + [
            'total_price' => 0,
        ]);
    }

    $totalPrice = $days * $reservation->rooms_count * $roomPrice;
    $reservation->update(['total_price' => $totalPrice]);

    return redirect()->route('reservations.pay', $reservation->id)->with('success', 'Thanks, Your reservation now is pending please click on pay. Total cost: $' . $totalPrice);
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

    $reviewedReservationIds = Review::where('user_id', Auth::id())
        ->whereIn('reservation_id', $reservations->pluck('id'))
        ->pluck('reservation_id')
        ->map(fn($id) => (int) $id)
        ->all();

    return view('reservation.index', compact('reservations', 'reviewedReservationIds'));
}
}
