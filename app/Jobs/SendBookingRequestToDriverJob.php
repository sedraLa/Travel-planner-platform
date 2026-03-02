<?php

namespace App\Jobs;

use App\Models\BookingRequest;
use App\Models\Driver;
use App\Models\TransportReservation;
use App\Notifications\DriverBookingRequestNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBookingRequestToDriverJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $reservationId,
        public int $driverId,
        public array $rankedDriverIds,
        public int $currentIndex,
    ) {
    }

    public function handle(): void
    {
        $reservation = TransportReservation::find($this->reservationId);
        $driver = Driver::with('user')->find($this->driverId);

        if (!$reservation || !$driver || $reservation->status !== 'pending_driver') {
            return;
        }

        $bookingRequest = BookingRequest::create([
            'reservation_id' => $reservation->id,
            'driver_id' => $driver->id,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(2),
        ]);

        if ($driver->user) {
            $driver->user->notify(new DriverBookingRequestNotification($reservation));
        }

        CheckBookingRequestTimeoutJob::dispatch($bookingRequest->id, $this->rankedDriverIds, $this->currentIndex)->delay($bookingRequest->expires_at);
    }
}