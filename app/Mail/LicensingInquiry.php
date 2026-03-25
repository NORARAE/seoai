<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LicensingInquiry extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly array $inquiry,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Licensing Enquiry — ' . ($this->inquiry['company'] ?? 'Unknown'),
            replyTo: [$this->inquiry['email']],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.licensing-inquiry',
        );
    }
}
