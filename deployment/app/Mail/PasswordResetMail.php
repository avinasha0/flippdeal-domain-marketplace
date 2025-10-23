<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($token, $email, $user = null)
    {
        $this->token = $token;
        $this->email = $email;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your FlippDeal Password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'token' => $this->token,
                'email' => $this->email,
                'user' => $this->user,
                'resetUrl' => $this->getResetUrl(),
            ]
        );
    }

    /**
     * Get the reset URL for the password reset.
     */
    private function getResetUrl(): string
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->email,
        ], false));
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
