<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TripPackage;
use App\Models\TripSchedule;
use App\Models\TripReservation;

class TripBookingController extends Controller
{

    //show booking form
    public function showBookingForm($packageId)
{
    $package = TripPackage::with('trip.schedules')->findOrFail($packageId);

    return view('trips.user.booking-form', compact('package'));
}


public function storeBooking(Request $request)
{
    $package = TripPackage::findOrFail($request->package_id);
    $schedule = TripSchedule::findOrFail($request->schedule_id);

    $total = $package->price * $request->people_count;

    $reservation = TripReservation::create([
        'user_id' => auth()->id(),
        'trip_id' => $package->trip_id,
        'trip_package_id' => $package->id,
        'trip_schedule_id' => $schedule->id,
        'people_count' => $request->people_count,
        'total_price' => $total,
        'status' => 'pending',
    ]);

    session(['trip_reservation_id' => $reservation->id]);

    return redirect()->route('trip.paypal');
}
}
