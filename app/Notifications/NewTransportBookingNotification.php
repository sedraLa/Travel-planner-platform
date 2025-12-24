<?php
namespace App\Notifications;

use App\Models\TransportReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewTransportBookingNotification extends Notification
{
    use Queueable;

    protected TransportReservation $reservation;

    public function __construct(TransportReservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        // الـ driver و user موجودين بسبب eager loading
        $driverUser = $this->reservation->driver?->user;
        $travelerUser = $this->reservation->user;

        return [
            'reservation_id' => $this->reservation->id,
            'pickup' => $this->reservation->pickup_location,
            'dropoff' => $this->reservation->dropoff_location,
            'driver' => $this->reservation->driver?->user?->name ?? 'Auto-assigned',
            'pickup_datetime' => $this->reservation->pickup_datetime->format('d-m-Y H:i'),
            'message' => 'New transport booking created',
        ];
    }
}
