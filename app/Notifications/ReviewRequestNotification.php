<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewRequestNotification extends Notification
{
    use Queueable;

    //data inside notification
    public function __construct(
        public string $type,
        public int $itemId,
        public int $reservationId,
        public string $itemName,
        public string $message = 'Please rate your experience'
    ) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    //data saved in database
    public function toDatabase($notifiable)
    {
        return [
            'type' => 'review_request',
            'review_type' => $this->type,
            'review_id' => $this->itemId,
            'reservation_id' => $this->reservationId,
            'review_name' => $this->itemName,
            'message' => $this->message,
        ];
    }
}
