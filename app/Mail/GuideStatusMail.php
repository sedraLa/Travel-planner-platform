<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuideStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $guideName,
        public string $status,
        public string $messageText
    ) {
    }

    public function build(): self
    {
        return $this->subject('Guide application status update')
            ->view('emails.guide_status')
            ->with([
                'guideName' => $this->guideName,
                'status' => $this->status,
                'messageText' => $this->messageText,
            ]);
    }
}
