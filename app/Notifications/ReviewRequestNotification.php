<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public int $id,
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
            'review_id' => $this->id,
            'message' => $this->message,
        ];
    }
}