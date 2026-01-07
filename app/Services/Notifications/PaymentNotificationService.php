<?php
namespace App\Services\Notifications;

use App\Models\Reservation;
use App\Models\TransportReservation;
use App\Mail\PaymentConfirmationMail;
use App\Mail\TransportPaymentConfirmationMail;
use App\Notifications\NewTransportBookingNotification;
use Illuminate\Support\Facades\Mail;

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
        // Email فقط للمسافر
        Mail::to($reservation->user->email)
            ->send(new TransportPaymentConfirmationMail($reservation));

        // Notifications للمسافر والسائق
        if ($reservation->driver && $reservation->driver->user) {
            $reservation->driver->user->notify(
                new NewTransportBookingNotification($reservation)
            );
        }

        $reservation->user->notify(
            new NewTransportBookingNotification($reservation)
        );
    }
}
