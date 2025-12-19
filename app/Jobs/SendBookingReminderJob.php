<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBookingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public TransportReservation $reservation,
        public string $type // day | hour
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $message = $this->type === 'day'
            ? 'Reminder: your booking is tomorrow'
            : 'Reminder: your booking starts in 1 hour';

        $data = [
            'reservation_id' => $this->reservation->id,
            'message' => $message,
            'pickup_datetime' => $this->reservation->pickup_datetime,
        ];

        $this->reservation->driver->user->notify(
            new GenericReminderNotification($data)
        );

        $this->reservation->user->notify(
            new GenericReminderNotification($data)
        );
    }
}
