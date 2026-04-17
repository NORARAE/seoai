<?php

namespace App\Mail;

use App\Models\QuickScan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UpgradeImplementOffer extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly QuickScan $scan,
        public readonly string $tierSlug = 'signal-expansion',
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We can implement this for you',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.funnel.upgrade-implement-offer',
        );
    }
}
