<?php

namespace App\Jobs;

use App\Mail\ScanStartedReminder;
use App\Mail\ScanStartedUrgent;
use App\Models\Lead;
use App\Models\QuickScan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendScanStartedEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 30;

    public function __construct(
        public readonly string $email,
        public readonly string $url,
    ) {
    }

    public function handle(): void
    {
        // Guard: if user already paid (QuickScan exists with paid=true), skip
        $hasPaid = QuickScan::where('email', $this->email)
            ->where('paid', true)
            ->exists();

        if ($hasPaid) {
            Log::info('SendScanStartedEmailsJob: user already paid, skipping', [
                'email' => $this->email,
            ]);
            return;
        }

        $host = parse_url($this->url, PHP_URL_HOST) ?? $this->url;
        $pagesDetected = rand(12, 50);

        // Email 1: Immediate reminder
        try {
            Mail::to($this->email)->queue(
                new ScanStartedReminder($this->email, $this->url, $pagesDetected)
            );
        } catch (\Throwable $e) {
            Log::warning('SendScanStartedEmailsJob: Email 1 failed', [
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
        }

        // Email 2: 3 hours later — urgency follow-up
        try {
            Mail::to($this->email)->later(
                now()->addHours(3),
                new ScanStartedUrgent($this->email, $this->url)
            );
        } catch (\Throwable $e) {
            Log::warning('SendScanStartedEmailsJob: Email 2 failed', [
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
        }

        // Upsert CRM lead
        try {
            Lead::updateOrCreate(
                ['email' => $this->email],
                [
                    'website' => $this->url,
                    'source' => 'scan-started',
                    'lifecycle_stage' => Lead::STAGE_NEW,
                    'tags' => array_merge(
                        Lead::where('email', $this->email)->value('tags') ?? [],
                        ['scan-started:abandoned']
                    ),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('SendScanStartedEmailsJob: Lead upsert failed', [
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
        }

        Log::info('SendScanStartedEmailsJob: dispatched', [
            'email' => $this->email,
            'url' => $this->url,
        ]);
    }
}
