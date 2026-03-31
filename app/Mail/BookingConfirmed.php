<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public ?string $manageUrl;

    public function __construct(
        public readonly Booking $booking,
        public readonly bool $rescheduled = false,
    ) {
        $this->manageUrl = $booking->public_booking_token
            ? route('booking.manage', ['token' => $booking->public_booking_token])
            : null;
    }

    public function envelope(): Envelope
    {
        $prefix = $this->rescheduled ? 'Booking Rescheduled — ' : 'Booking Confirmed — ';

        return new Envelope(
            subject: $prefix . $this->booking->consultType->name . ' | seoaico.com',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmed',
        );
    }
}
