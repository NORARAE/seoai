<?php

namespace App\Mail;

use App\Models\QuickScan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UpgradeStrategySession extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly QuickScan $scan)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Want help applying this?',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.funnel.upgrade-strategy-session',
        );
    }
}
