<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public int $itemId,
        public string $itemName,
        public string $message = 'Please rate your experience'
    ) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'review_request',
            'review_type' => $this->type,
            'review_id' => $this->itemId,
            'review_name' => $this->itemName,
            'message' => $this->message,
        ];
    }
}