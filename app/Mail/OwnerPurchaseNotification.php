<?php

namespace App\Mail;

use App\Models\QuickScan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OwnerPurchaseNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly QuickScan $scan,
        public readonly string $tier,
        public readonly int $amountCents,
    ) {
    }

    public function envelope(): Envelope
    {
        $amount = '$' . number_format($this->amountCents / 100, 2);

        return new Envelope(
            subject: "New Purchase — {$this->tier} — {$amount}",
            replyTo: [$this->scan->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.owner-purchase-notification',
            with: [
                'scan' => $this->scan,
                'tierName' => $this->tier,
                'amount' => '$' . number_format($this->amountCents / 100, 2),
            ],
        );
    }
}
