<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\ReviewableItemCompleted;
use App\Notifications\ReviewRequestNotification;

class SendReviewRequestNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReviewableItemCompleted $event)
    {
        $user = \App\Models\User::find($event->userId);

        $user->notify(
            new ReviewRequestNotification(
                type: $event->type,
                itemId: $event->id
            )
        );
    }
}
