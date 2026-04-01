<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent 24-48 hours before the session to prime the client.
 * Scheduled via Job dispatch with delay.
 */
class BookingPreCall extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Booking $booking,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Preparing for your session — ' . $this->booking->consultType->name . ' | seoaico.com',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-pre-call',
        );
    }
}
