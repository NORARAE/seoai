<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent 24-48 hours after the session to follow up.
 * Scaffold — content and dispatch hook to be completed.
 * Scheduled via Job dispatch with delay.
 */
class BookingFollowUp extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Booking $booking,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Following up on your strategy session — seoaico.com',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-follow-up',
        );
    }
}
