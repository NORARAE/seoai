<?php

namespace App\Mail;

use App\Models\QuickScan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HighTierActivation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly QuickScan $scan,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Let\'s activate your system',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.funnel.high-tier-activation',
        );
    }
}
