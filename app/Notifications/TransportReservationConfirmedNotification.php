<?php

namespace App\Notifications;

use App\Models\TransportReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransportReservationConfirmedNotification extends Notification
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
        $isDriver = $notifiable->role === 'driver';

        return [
            'reservation_id' => $this->reservation->id,
            'pickup' => $this->reservation->pickup_location,
            'dropoff' => $this->reservation->dropoff_location,
            'pickup_datetime' => optional($this->reservation->pickup_datetime)->format('d-m-Y H:i'),
            'receiver_role' => $isDriver ? 'driver' : 'user',
            'message' => $isDriver
                ? 'Transport reservation confirmed and paid by the user.'
                : 'Your transport reservation has been confirmed successfully.',
        ];
    }
}