<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TripNoSuitableGuidesNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private int $tripId)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'trip_id' => $this->tripId,
            'message' => 'No suitable guides were found for this trip. Please try again later.',
        ];
    }
}
