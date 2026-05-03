<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TripReservationConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(public $reservation)
    {}

    public function via($notifiable)
    {
        return ['database']; 
    }




    public function toDatabase($notifiable)
{
    return [
        'type' => 'trip_reservation',
        'message' => 'Your trip has been successfully booked 🎉',

        'reservation_id' => $this->reservation->id,

        // Trip info
        'trip_name' => $this->reservation->trip->name ?? null,
        'trip_id' => $this->reservation->trip_id,

        // Package info
        'package_name' => $this->reservation->tripPackage->name ?? null,
        'people_count' => $this->reservation->people_count,

        // Schedule info
        'start_date' => $this->reservation->tripSchedule->start_date ?? null,
        'end_date' => $this->reservation->tripSchedule->end_date ?? null,

        'total_price' => $this->reservation->total_price,
    ];
}
}