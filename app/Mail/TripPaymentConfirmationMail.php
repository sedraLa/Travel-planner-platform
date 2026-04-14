<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\TripReservation;

class TripPaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    public function __construct(TripReservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function build()
    {
        return $this->subject('✈️ Trip Booking Confirmed - Your Adventure is Ready')
            ->view('emails.trip_booking_confirmation')
            ->with([
                'reservation' => $this->reservation,
                'trip' => $this->reservation->trip,
                'package' => $this->reservation->tripPackage,
                'schedule' => $this->reservation->tripSchedule,
                'user' => $this->reservation->user,
            ]);
    }
}