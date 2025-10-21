<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $email;

    public function __construct($email, $code)
    {
        $this->email = $email;
        $this->code = $code;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Verify Your Email - FlippDeal',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.verification',
            with: [
                'code' => $this->code,
                'email' => $this->email
            ]
        );
    }

    public function attachments()
    {
        return [];
    }
}