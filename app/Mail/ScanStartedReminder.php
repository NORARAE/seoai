<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScanStartedReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $email,
        public readonly string $url,
        public readonly int $pagesDetected,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your scan is already in progress…',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.funnel.scan-started-reminder',
        );
    }
}
