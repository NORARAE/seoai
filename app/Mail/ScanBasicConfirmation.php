<?php

namespace App\Mail;

use App\Models\QuickScan;
use App\Support\QuickScanReportToken;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScanBasicConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public readonly string $email;
    public readonly string $sessionId;
    public readonly ?string $reportUrl;

    public function __construct(
        public readonly QuickScan $scan,
    ) {
        $this->email = $scan->email;
        $this->sessionId = (string) $scan->stripe_session_id;
        $this->reportUrl = route('report.show', [
            'scan' => $scan->id,
            'token' => QuickScanReportToken::generate($scan),
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your AI Citation Scan is processing',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.checkout.scan-basic',
        );
    }
}
