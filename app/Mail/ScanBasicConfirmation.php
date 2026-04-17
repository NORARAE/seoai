<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScanBasicConfirmation extends Mailable
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
            subject: 'Your AI Citation Scan is processing',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.checkout.scan-basic',
        );
    }
}
