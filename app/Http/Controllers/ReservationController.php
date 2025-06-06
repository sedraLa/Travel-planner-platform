<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Requests\ReservationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;

class ReservationController extends Controller
{
   public function showReservationForm($id)
{
    $hotel = Hotel::findOrFail($id);
    return view('reservation.create', compact('hotel'));
}
    public function store(ReservationRequest $request)
    {
        $days = now()->parse($request->check_in_date)->diffInDays($request->check_out_date);
        $room_price = 100;
        $total_price = $days * $request->rooms_count * $room_price;

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

       return redirect()->route('hotel.show', $request->hotel_id)->with('success', 'Your reservation has been successfully completed! Total cost: $' . $total_price);
    }
}
