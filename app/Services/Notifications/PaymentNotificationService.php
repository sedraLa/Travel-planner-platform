<?php
namespace App\Services\Notifications;

use App\Models\Reservation;
use App\Models\TransportReservation;
use App\Models\TripReservation;
use App\Mail\PaymentConfirmationMail;
use App\Mail\TransportPaymentConfirmationMail;
use App\Mail\TripPaymentConfirmationMail;
use App\Notifications\NewTransportBookingNotification;
use Illuminate\Support\Facades\Mail;
use App\Notifications\TransportReservationConfirmedNotification;
use App\Notifications\TripReservationConfirmedNotification;

class PaymentNotificationService
{
    public function sendHotelPaymentConfirmation(Reservation $reservation)
    {
        Mail::to($reservation->user->email)
            ->send(
                new PaymentConfirmationMail(
                    $reservation->hotel->name,
                    $reservation
                )
            );
    }

    public function sendTransportPaymentConfirmation(TransportReservation $reservation)
    {
        // Email for traveler
        Mail::to($reservation->user->email)
            ->send(new TransportPaymentConfirmationMail($reservation));

        // Notifications for traveler and driver
        if ($reservation->driver && $reservation->driver->user) {
            $reservation->driver->user->notify(
                new TransportReservationConfirmedNotification($reservation)
            );
        }

        $reservation->user->notify(
            new TransportReservationConfirmedNotification($reservation)
        );
    }

    public function sendTripPaymentConfirmation(TripReservation $reservation)
{
    Mail::to($reservation->user->email)
        ->send(new TripPaymentConfirmationMail($reservation));

    $reservation->user->notify(
        new TripReservationConfirmedNotification($reservation)
    );
}
}
