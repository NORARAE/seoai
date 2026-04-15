<?php

namespace App\Jobs;

use App\Mail\QuickScanDay1;
use App\Mail\QuickScanDay2;
use App\Mail\QuickScanDay3;
use App\Mail\QuickScanResult;
use App\Models\Lead;
use App\Models\QuickScan;
use App\Services\QuickScanService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RunQuickScanJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 30;
    public int $uniqueFor = 300; // 5-minute window prevents duplicate dispatch

    public function __construct(
        public readonly int $scanId,
    ) {
    }

    public function uniqueId(): string
    {
        return (string) $this->scanId;
    }

    public function handle(QuickScanService $scanner): void
    {
        $scan = QuickScan::find($this->scanId);

        if (!$scan) {
            Log::warning('RunQuickScanJob: scan not found', ['scan_id' => $this->scanId]);
            return;
        }

        // Run scan only if not already scanned (idempotent).
        // CRM + emails always run — Mail::later() is fire-and-forget and
        // duplicate dispatch is acceptable (same content, same recipient).
        if ($scan->status !== QuickScan::STATUS_SCANNED || $scan->score === null) {
            $result = $scanner->scan($scan->url);

            $scan->update([
                'score' => $result['score'],
                'categories' => $result['categories'],
                'issues' => $result['issues'],
                'strengths' => $result['strengths'],
                'fastest_fix' => $result['fastest_fix'],
                'raw_checks' => $result['raw_checks'],
                'broken_links' => $result['broken_links'],
                'page_count' => $result['page_count'],
                'status' => QuickScan::STATUS_SCANNED,
                'scanned_at' => now(),
            ]);
            $scan->refresh();
        }

        // Upsert CRM lead (idempotent — updateOrCreate)
        try {
            Lead::updateOrCreate(
                ['email' => $scan->email],
                [
                    'website' => $scan->url,
                    'source' => 'quick-scan',
                    'lifecycle_stage' => Lead::STAGE_NEW,
                    'score' => $scan->score,
                    'tags' => array_merge(
                        Lead::where('email', $scan->email)->value('tags') ?? [],
                        ['quick-scan:purchased']
                    ),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('RunQuickScanJob: Lead upsert failed', ['scan_id' => $scan->id, 'error' => $e->getMessage()]);
        }

        // Guard: only send emails once, and skip for internal QA scans
        if ($scan->emails_sent || $scan->suppress_emails) {
            Log::info('RunQuickScanJob: emails skipped', [
                'scan_id' => $scan->id,
                'emails_sent' => $scan->emails_sent,
                'suppress_emails' => $scan->suppress_emails,
            ]);
            return;
        }

        // Email 1: Immediate result
        try {
            Mail::to($scan->email)->queue(new QuickScanResult($scan));
        } catch (\Throwable $e) {
            Log::warning('RunQuickScanJob: Email 1 failed', ['scan_id' => $scan->id, 'error' => $e->getMessage()]);
        }

        // Email 2: Day 1 follow-up
        try {
            Mail::to($scan->email)->later(now()->addDay(), new QuickScanDay1($scan));
        } catch (\Throwable $e) {
            Log::warning('RunQuickScanJob: Email 2 (Day 1) failed', ['scan_id' => $scan->id, 'error' => $e->getMessage()]);
        }

        // Email 3: Day 3 deepen
        try {
            Mail::to($scan->email)->later(now()->addDays(3), new QuickScanDay2($scan));
        } catch (\Throwable $e) {
            Log::warning('RunQuickScanJob: Email 3 (Day 3) failed', ['scan_id' => $scan->id, 'error' => $e->getMessage()]);
        }

        // Email 4: Day 5 conversion
        try {
            Mail::to($scan->email)->later(now()->addDays(5), new QuickScanDay3($scan));
        } catch (\Throwable $e) {
            Log::warning('RunQuickScanJob: Email 4 (Day 5) failed', ['scan_id' => $scan->id, 'error' => $e->getMessage()]);
        }

        $scan->update(['emails_sent' => true]);

        Log::info('RunQuickScanJob: completed', ['scan_id' => $scan->id, 'score' => $scan->score]);
    }
}
