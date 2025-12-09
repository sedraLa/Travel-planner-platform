<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\TransportReservation;

class TransportPaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $vehicleName;
    protected $reservation;

    /**
     * Create a new message instance.
     */
    public function __construct(TransportReservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function build()
    {
        return $this->subject('Transport Payment Confirmation')
                    ->view('emails.transport_payment_confirmation')
                    ->with([
                        'reservation' => $this->reservation,
                    ]);
    }
}
