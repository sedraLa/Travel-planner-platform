<?php

namespace App\Notifications;

use App\Models\TransportReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DriverBookingRequestNotification extends Notification
{
    use Queueable;

    public function __construct(private TransportReservation $reservation)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'pickup' => $this->reservation->pickup_location,
            'dropoff' => $this->reservation->dropoff_location,
            'pickup_datetime' => optional($this->reservation->pickup_datetime)->format('d-m-Y H:i'),
            'receiver_role' => 'driver',
            'message' => 'You have a new booking request waiting for acceptance.',
        ];
    }
}