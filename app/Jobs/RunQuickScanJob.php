<?php

namespace App\Jobs;

use App\Mail\QuickScanDay1;
use App\Mail\QuickScanDay2;
use App\Mail\QuickScanDay3;
use App\Mail\QuickScanResult;
use App\Models\Lead;
use App\Models\QuickScan;
use App\Models\Site;
use App\Models\SiteCrawlSetting;
use App\Services\QuickScanService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

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
            try {
                $result = $scanner->scan($scan->url);

                // Scan memory: capture previous score for same domain
                $previousScan = QuickScan::where('domain', $scan->domain)
                    ->where('status', QuickScan::STATUS_SCANNED)
                    ->where('id', '!=', $scan->id)
                    ->latest('scanned_at')
                    ->first();
                $lastScore = $previousScan?->score;
                $scoreChange = $lastScore !== null ? ($result['score'] - $lastScore) : null;

                $scan->update([
                    'score' => $result['score'],
                    'last_score' => $lastScore,
                    'score_change' => $scoreChange,
                    'categories' => $result['categories'],
                    'issues' => $result['issues'],
                    'strengths' => $result['strengths'],
                    'fastest_fix' => $result['fastest_fix'],
                    'raw_checks' => $result['raw_checks'],
                    'broken_links' => $result['broken_links'],
                    'page_count' => $result['page_count'],
                    'dimensions' => $result['dimensions'] ?? null,
                    'intelligence' => $result['intelligence'] ?? null,
                    'status' => QuickScan::STATUS_SCANNED,
                    'scanned_at' => now(),
                    // Persist the derived domain so crawl trigger + future look-ups
                    // can use the column directly without reparsing the URL.
                    'domain' => parse_url($scan->url, PHP_URL_HOST) ?? $scan->url,
                ]);
                $scan->refresh();

                // ── Phase 1+2+3: Trigger full site crawl after successful scan ──
                // Only when the scan produced a meaningful result (score is set).
                // Wrapped in its own try/catch — crawl failures must NEVER fail the
                // QuickScan itself; the paid result has already been stored above.
                if ($result['score'] !== null && empty($result['error'])) {
                    $this->triggerSiteCrawl($scan);
                }
            } catch (Throwable $e) {
                $scan->update(['status' => QuickScan::STATUS_ERROR]);

                Log::error('RunQuickScanJob: scan execution failed', [
                    'scan_id' => $scan->id,
                    'url' => $scan->url,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                throw $e;
            }
        }

        // Upsert CRM lead (idempotent — updateOrCreate)
        try {
            $lead = Lead::updateOrCreate(
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

            // User classification: detect multi-domain / agency behavior
            $domainCount = QuickScan::where('email', $scan->email)
                ->whereNotNull('domain')
                ->distinct('domain')
                ->count('domain');
            $scanCount = QuickScan::where('email', $scan->email)->count();
            $userType = $domainCount >= 5 ? 'agency_suspect' : ($domainCount >= 3 ? 'multi_domain' : 'individual');

            $lead->update([
                'domain_count' => $domainCount,
                'scan_count' => $scanCount,
                'user_type' => $userType,
            ]);
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

        // Email 1: Immediate result (transactional — always send)
        try {
            if (app()->environment('local')) {
                Mail::to($scan->email)->send(new QuickScanResult($scan));
            } else {
                Mail::to($scan->email)->queue(new QuickScanResult($scan));
            }
        } catch (\Throwable $e) {
            Log::warning('RunQuickScanJob: Email 1 failed', ['scan_id' => $scan->id, 'error' => $e->getMessage()]);
        }

        // Marketing follow-up sequence (Day 1 / 3 / 5).
        // Skip if the lead has already unsubscribed or opted out of marketing emails.
        $lead = Lead::where('email', $scan->email)->first();
        $userOptedOut = \App\Models\User::where('email', $scan->email)
            ->where('email_marketing_opt_in', false)
            ->exists();

        if ($lead?->email_unsubscribed_at || $userOptedOut) {
            Log::info('RunQuickScanJob: marketing follow-ups skipped (unsubscribed)', ['scan_id' => $scan->id]);
            $scan->update(['emails_sent' => true]);
            Log::info('RunQuickScanJob: completed', ['scan_id' => $scan->id, 'score' => $scan->score]);
            return;
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

    public function failed(Throwable $e): void
    {
        $scan = QuickScan::find($this->scanId);
        if ($scan && $scan->status !== QuickScan::STATUS_SCANNED) {
            $scan->update(['status' => QuickScan::STATUS_ERROR]);
        }

        Log::error('RunQuickScanJob: failed callback invoked', [
            'scan_id' => $this->scanId,
            'error' => $e->getMessage(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Phase 1+2+3+6: Site discovery pipeline trigger
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Find or create a Site for the scanned domain, seed conservative crawl
     * settings (Phase 6 safety limits), and dispatch StartSiteDiscoveryJob.
     *
     * This method must NEVER throw — it is wrapped in a try/catch at the call
     * site so that crawl failures cannot affect the paid QuickScan result.
     */
    private function triggerSiteCrawl(QuickScan $scan): void
    {
        try {
            $domain = $scan->domain;

            if (blank($domain)) {
                Log::warning('RunQuickScanJob: crawl trigger skipped — blank domain', [
                    'scan_id' => $scan->id,
                ]);
                return;
            }

            // ── Phase 1: Find or create the Site record ──────────────────────
            $site = Site::firstOrCreate(
                ['domain' => $domain],
                [
                    'name' => $domain,
                    'status' => 'active',
                    'crawl_status' => 'idle',
                    // sitemap_enabled defaults to false; leave crawl to homepage
                    // seeding until explicitly enabled by an admin.
                    'sitemap_enabled' => false,
                ]
            );

            // Store site_id on the QuickScan so dashboard can later join crawl data.
            if ($scan->site_id !== $site->id) {
                $scan->update(['site_id' => $site->id]);
            }

            // Associate the authenticated user to the site via the pivot table
            // so they can access it from the dashboard later.
            if ($scan->user_id) {
                DB::table('site_user')->insertOrIgnore([
                    'site_id' => $site->id,
                    'user_id' => $scan->user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // ── Phase 6: Seed conservative crawl settings ────────────────────
            // SiteCrawlSetting::firstOrCreate means existing manual overrides
            // from the admin are preserved. Only new Sites get these defaults.
            SiteCrawlSetting::firstOrCreate(
                ['site_id' => $site->id],
                [
                    // QuickScan-triggered crawls: 250 pages max, depth 3.
                    // This prevents runaway crawls on large sites. An admin can
                    // raise these limits in the admin panel after first crawl.
                    'max_pages' => 250,
                    'crawl_delay' => 1,    // seconds between requests
                    'max_depth' => 3,
                    'obey_robots' => true,
                    'follow_nofollow' => false,
                ]
            );

            Log::info('RunQuickScanJob: site record resolved for crawl', [
                'scan_id' => $scan->id,
                'site_id' => $site->id,
                'domain' => $domain,
                'site_was_new' => $site->wasRecentlyCreated,
            ]);

            // ── Phase 3: Dispatch the discovery pipeline ─────────────────────
            // StartSiteDiscoveryJob guards against duplicate runs internally:
            // if a crawl is already running for this site it will log + return.
            StartSiteDiscoveryJob::dispatch(
                siteId: $site->id,
                triggeredByType: 'quick_scan',
                initiatedBy: $scan->user_id,
                quickScanId: $scan->id,
            )->onQueue('crawl');

            Log::info('RunQuickScanJob: StartSiteDiscoveryJob dispatched', [
                'scan_id' => $scan->id,
                'site_id' => $site->id,
                'domain' => $domain,
                'queue' => 'crawl',
            ]);
        } catch (Throwable $e) {
            // Crawl trigger failure is non-fatal — log and continue.
            Log::error('RunQuickScanJob: site crawl trigger failed (non-fatal)', [
                'scan_id' => $scan->id,
                'domain' => $scan->domain,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

