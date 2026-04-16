<?php

namespace App\Notifications;

use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GuideTripDeletedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Trip $trip)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'trip_id' => $this->trip->id,
            'trip_name' => $this->trip->name,
            'message' => 'This trip has been deleted',
        ];
    }
}
