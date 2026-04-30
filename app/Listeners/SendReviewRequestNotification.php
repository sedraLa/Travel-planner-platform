<?php

namespace App\Listeners;

use App\Events\ReviewableItemCompleted;
use App\Notifications\ReviewRequestNotification;

class SendReviewRequestNotification
{
    public function handle(ReviewableItemCompleted $event)
    {
        $user = \App\Models\User::find($event->userId);

        if (! $user) {
            return;
        }

        $user->notify(
            new ReviewRequestNotification(
                type: $event->type,
                itemId: $event->id,
                reservationId: $event->reservationId,
                itemName: $this->getItemName($event->type, $event->id)
            )
        );
    }


    private function getItemName(string $type, int $id): string
    {
        return match ($type) {

            //get hotel name
            'hotel' => \App\Models\Hotel::find($id)?->name ?? 'Hotel',

            'trip' => \App\Models\Trip::find($id)?->name ?? 'Trip',

            'driver' => \App\Models\Driver::find($id)?->name ?? 'Driver',

            'guide' => \App\Models\Guide::find($id)?->name ?? 'Guide',

            'activity' => \App\Models\Activity::find($id)?->name ?? 'Activity',

            default => 'Item',
        };
    }
}
