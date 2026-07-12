<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $resetUrl;

    public function __construct(
        public User $user,
        public string $token
    ) {
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        $this->resetUrl = $frontendUrl . '/reset-password?token=' . $this->token
            . '&email=' . urlencode($this->user->email);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password - CoFund'
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'email.password-reset',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}