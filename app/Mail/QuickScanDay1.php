<?php

namespace App\Mail;

use App\Models\QuickScan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuickScanDay1 extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly QuickScan $scan)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Why AI won\'t cite your site (yet)',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quick-scan.day1',
        );
    }
}
