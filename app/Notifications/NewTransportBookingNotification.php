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
        $driverUser   = $this->reservation->driver?->user;
        $travelerUser = $this->reservation->user;
    
        $isDriver = $notifiable->role === 'driver';
    
        return [
            'reservation_id' => $this->reservation->id,
            'pickup' => $this->reservation->pickup_location,
            'dropoff' => $this->reservation->dropoff_location,
            'pickup_datetime' => $this->reservation->pickup_datetime->format('d-m-Y H:i'),
    
            'full_name' => $isDriver
                ? $travelerUser?->full_name
                : ($driverUser?->full_name ?? 'Auto-assigned'),
    
            'phone_number' => $isDriver
                ? $travelerUser?->phone_number
                : ($driverUser?->phone_number ?? null),
    
            'receiver_role' => $isDriver ? 'driver' : 'user',
            'message' => 'New transport booking created',
        ];
    }
    

}
