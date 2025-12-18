<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $driverName;
    public $status;
    public $messageText;

    public function __construct($driverName, $status, $messageText)
    {
        $this->driverName = $driverName;
        $this->status = $status;
        $this->messageText = $messageText;
    }

    public function build()
    {
        return $this->subject('update you status account')
                    ->view('emails.driver_status')
                    ->with([
                        'driverName' => $this->driverName,
                        'status' => $this->status,
                        'messageText' => $this->messageText,
                    ]);
    }
}
