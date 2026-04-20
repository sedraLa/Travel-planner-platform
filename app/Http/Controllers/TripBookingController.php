<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TripPackage;
use App\Models\TripSchedule;
use App\Models\TripReservation;
use App\Models\Review;

class TripBookingController extends Controller
{

    //show booking form
public function showBookingForm($packageId)
{
    $package = TripPackage::with('trip.schedules')->findOrFail($packageId);
    $today = now()->toDateString();

    $availableSchedules = $package->trip->schedules
        ->filter(fn ($schedule) => $schedule->status === 'available'
            && $schedule->available_seats > 0
            && $schedule->booking_deadline
            && $schedule->booking_deadline >= $today)
        ->values();

    return view('trips.user.booking-form', compact('package', 'availableSchedules'));
}


public function storeBooking(Request $request)
{
    $validated = $request->validate([
        'package_id' => ['required', 'exists:trip_packages,id'],
        'schedule_id' => ['required', 'exists:trip_schedules,id'],
        'people_count' => ['required', 'integer', 'min:1'],
    ]);

    $package = TripPackage::findOrFail($validated['package_id']);
    $schedule = TripSchedule::findOrFail($validated['schedule_id']);

    if ((int) $schedule->trip_id !== (int) $package->trip_id) {
        return back()->withErrors(['schedule_id' => 'Invalid schedule selected for this trip.'])->withInput();
    }

    if ($schedule->status !== 'available' || $schedule->available_seats <= 0) {
        return back()->withErrors(['schedule_id' => 'This schedule is no longer available for booking.'])->withInput();
    }

    if (!$schedule->booking_deadline || now()->gt(\Carbon\Carbon::parse($schedule->booking_deadline)->endOfDay())) {
        return back()->withErrors(['schedule_id' => 'Booking deadline has passed for this trip schedule.'])->withInput();
    }

    if ($validated['people_count'] > (int) $schedule->available_seats) {
        return back()->withErrors(['people_count' => 'People count exceeds available seats.'])->withInput();
    }

    $total = $package->price * $validated['people_count'];

    $reservation = TripReservation::create([
        'user_id' => auth()->id(),
        'trip_id' => $package->trip_id,
        'trip_package_id' => $package->id,
        'trip_schedule_id' => $schedule->id,
        'people_count' => $validated['people_count'],
        'total_price' => $total,
        'status' => 'pending',
        'guide_id' => $package->trip->assigned_guide_id,
    ]);

    session(['trip_reservation_id' => $reservation->id]);

    return redirect()->route('trip.paypal');
}

public function index(Request $request)
{
    $query = TripReservation::with(['user', 'trip', 'package', 'schedule']);

    if (auth()->user()->role !== 'admin') {
        $query->where('user_id', auth()->id());
    }

    // Keyword search
    if ($request->filled('keyword')) {
        $keyword = $request->keyword;

        $query->where(function ($q) use ($keyword) {
            $q->whereHas('trip', fn($t) =>
                $t->where('name', 'like', "%$keyword%")
            )
            ->orWhereHas('package', fn($p) =>
                $p->where('name', 'like', "%$keyword%")
            )
            ->orWhereHas('user', fn($u) =>
                $u->where('name', 'like', "%$keyword%")
                
            );
        });
    }

    // Month (based on schedule, مو created_at)
    if ($request->filled('month')) {
        $query->whereHas('schedule', function ($q) use ($request) {
            $q->whereMonth('start_date', $request->month);
        });
    }

    // Year (schedule)
    if ($request->filled('year')) {
        $query->whereHas('schedule', function ($q) use ($request) {
            $q->whereYear('start_date', $request->year);
        });
    }

    $reservations = $query->latest()->get();

    $reviewedReservationIds = Review::where('user_id', auth()->id())
        ->whereIn('reservation_id', $reservations->pluck('id'))
        ->pluck('reservation_id')
        ->map(fn($id) => (int) $id)
        ->all();

    return view('trips.reservations.index', compact('reservations', 'reviewedReservationIds'));
}

}
