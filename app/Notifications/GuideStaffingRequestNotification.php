<?php

namespace App\Notifications;

use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GuideStaffingRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Trip $trip, private int $guideRequestId)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->trip->loadMissing(['primaryDestination', 'schedules']);

        return [
            'trip_id' => $this->trip->id,
            'trip_name' => $this->trip->name,
            'destination' => $this->trip->primaryDestination?->name,
            'start_date' => $this->trip->schedules->min('start_date'),
            'end_date' => $this->trip->schedules->max('end_date'),
            'guide_request_id' => $this->guideRequestId,
            'message' => 'You have a new guide staffing request.',
        ];
    }
}
