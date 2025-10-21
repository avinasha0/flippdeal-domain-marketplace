<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $token;
    public $activationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $token)
    {
        $this->email = $email;
        $this->token = $token;
        $this->activationUrl = route('register.activate', ['token' => $token, 'email' => $email]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Activate Your Account - FlippDeal',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.activation',
            with: [
                'email' => $this->email,
                'token' => $this->token,
                'activationUrl' => $this->activationUrl
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
