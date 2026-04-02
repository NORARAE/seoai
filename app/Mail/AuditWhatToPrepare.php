<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent 48 hours before a Market Opportunity Audit session.
 * Primes the client with what to prepare so the session delivers maximum value.
 */
class AuditWhatToPrepare extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Booking $booking,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Preparing for your Market Opportunity Analysis — seoaico.com',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.audit-what-to-prepare',
        );
    }
}
