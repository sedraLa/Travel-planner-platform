<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessNextDriverInChainJob;
use App\Models\BookingRequest;
use App\Models\TransportReservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class BookingRequestController extends Controller
{
    public function index()
    {
        $driver = auth()->user()->driver;

        $requests = BookingRequest::with('reservation.user')
            ->where('driver_id', $driver->id)
            ->latest()
            ->get();

        return view('driver.booking-requests', compact('requests'));
    }

    public function accept(BookingRequest $bookingRequest): RedirectResponse
    {
        $driver = auth()->user()->driver;

        abort_unless($bookingRequest->driver_id === $driver->id, 403);
        abort_unless($bookingRequest->status === 'pending', 422, 'Request is no longer pending.');

        $reservation = TransportReservation::findOrFail($bookingRequest->reservation_id);

        if ($reservation->status !== 'pending_driver') {
            return back()->withErrors('Reservation is no longer waiting for a driver.');
        }

        $vehicleId = optional($driver->assignment)->transport_vehicle_id;

        if (!$vehicleId) {
            return back()->withErrors('No assigned vehicle found for your account.');
        }

        DB::transaction(function () use ($bookingRequest, $reservation, $driver, $vehicleId) {
            $reservation->update([
                'driver_id' => $driver->id,
                'transport_vehicle_id' => $vehicleId,
                'status' => 'driver_assigned',
            ]);

            $bookingRequest->update(['status' => 'accepted']);

            $reservation->bookingRequests()
                ->where('id', '!=', $bookingRequest->id)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);
        });

        return redirect()->route('driver.pending-reservations')->with('success', 'Booking request accepted.');
    }

    public function reject(BookingRequest $bookingRequest): RedirectResponse
    {
        $driver = auth()->user()->driver;

        abort_unless($bookingRequest->driver_id === $driver->id, 403);
        abort_unless($bookingRequest->status === 'pending', 422, 'Request is no longer pending.');

        $bookingRequest->update(['status' => 'rejected']);

        $reservation = TransportReservation::findOrFail($bookingRequest->reservation_id);
        $rankedDriverIds = $reservation->ranked_driver_ids ?? [];
        $currentIndex = array_search($driver->id, $rankedDriverIds, true);

        ProcessNextDriverInChainJob::dispatch($reservation->id, $rankedDriverIds, $currentIndex === false ? 1 : $currentIndex + 1);

        return back()->with('success', 'Booking request rejected.');
    }

    public function pendingReservations()
    {
        $driver = auth()->user()->driver;

        $reservations = TransportReservation::with('user', 'vehicle')
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['driver_assigned', 'confirmed'])
            ->latest()
            ->get();

        return view('driver.pending-reservations', compact('reservations'));
    }
}