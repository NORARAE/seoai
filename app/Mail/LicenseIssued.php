<?php

namespace App\Mail;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LicenseIssued extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public License $license)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your SEOAico Core Content Engine license',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.licenses.issued',
        );
    }
}