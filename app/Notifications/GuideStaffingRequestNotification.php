<?php

namespace App\Notifications;

use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GuideStaffingRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Trip $trip)
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
            'message' => 'You have a new guide staffing request.',
        ];
    }
}
