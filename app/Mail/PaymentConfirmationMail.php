<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class PaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $hotelName;
    protected $reservation;

    /**
     * Create a new message instance.
     */
    public function __construct($hotelName, Reservation $reservation)
    {
        $this->hotelName = $hotelName;
        $this->reservation = $reservation;
    }


   // buuild the message 

   public function build() {
    return $this->subject('Payment Confirmation')
    ->view('emails.payment_confirmation')->with([
                        'reservation' => $this->reservation,
                        'hotelName' => $this->hotelName,
                    ]);;
   }
}
