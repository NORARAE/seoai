<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StructuralLeverageConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $email,
        public readonly string $sessionId,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Structural Leverage report is being built',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.checkout.structural-leverage',
        );
    }
}
