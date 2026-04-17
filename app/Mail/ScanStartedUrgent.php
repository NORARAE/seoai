<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScanStartedUrgent extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $email,
        public readonly string $url,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'re missing critical visibility signals',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.funnel.scan-started-urgent',
        );
    }
}
