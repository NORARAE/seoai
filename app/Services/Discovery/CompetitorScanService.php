<?php

namespace App\Services\Discovery;

use App\Jobs\RunCompetitorScanJob;
use App\Models\CompetitorDomain;
use App\Models\CompetitorScanRun;
use App\Models\Site;
use App\Models\UsageRecord;
use App\Models\User;
use App\Services\UsageTrackingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CompetitorScanService
{
    public function __construct(
        protected UsageTrackingService $usageTrackingService,
    ) {}

    /**
     * @return array{status:string,domain:CompetitorDomain,created:bool,scan_run:?CompetitorScanRun,reason:?string,credit_consumed:bool}
     */
    public function registerDomain(Site $site, string $domain, ?User $user = null): array
    {
        $normalizedDomain = $this->normalizeDomain($domain);

        $competitorDomain = CompetitorDomain::query()->firstOrCreate(
            [
                'site_id' => $site->id,
                'domain' => $normalizedDomain,
            ],
            [
                'scan_count' => 0,
                'paid_scan_credits' => 0,
            ],
        );

        $result = $this->startScan($competitorDomain, $user, 'auto');

        return [
            'status' => $result['status'],
            'domain' => $competitorDomain,
            'created' => $competitorDomain->wasRecentlyCreated,
            'scan_run' => $result['scan_run'],
            'reason' => $result['reason'],
            'credit_consumed' => $result['credit_consumed'],
        ];
    }

    /**
     * @return array{status:string,scan_run:?CompetitorScanRun,reason:?string,credit_consumed:bool}
     */
    public function startScan(CompetitorDomain $competitorDomain, ?User $user = null, string $triggeredByType = 'manual'): array
    {
        $activeRun = $competitorDomain->scanRuns()
            ->whereIn('status', ['pending', 'running'])
            ->latest('id')
            ->first();

        if ($activeRun) {
            return [
                'status' => 'active',
                'scan_run' => $activeRun,
                'reason' => 'already_running',
                'credit_consumed' => false,
            ];
        }

        $creditConsumed = false;
        $usageRecordId = null;

        if (! $this->canStartScan($competitorDomain, $user)) {
            return [
                'status' => 'blocked',
                'scan_run' => null,
                'reason' => 'payment_required',
                'credit_consumed' => false,
            ];
        }

        if ($competitorDomain->scan_count >= 1 && ! $this->bypassesLimits($user)) {
            $usageRecord = DB::transaction(function () use ($competitorDomain): ?UsageRecord {
                $lockedDomain = CompetitorDomain::query()->lockForUpdate()->find($competitorDomain->id);

                if (! $lockedDomain || $lockedDomain->paid_scan_credits < 1) {
                    return null;
                }

                $lockedDomain->decrement('paid_scan_credits');

                $client = $lockedDomain->site?->client;

                if (! $client) {
                    return null;
                }

                return $this->usageTrackingService->track($client, 'competitor_rescan', 1, [
                    'site_id' => $lockedDomain->site_id,
                    'competitor_domain_id' => $lockedDomain->id,
                    'domain' => $lockedDomain->domain,
                ]);
            });

            if (! $usageRecord && $competitorDomain->site?->client) {
                return [
                    'status' => 'blocked',
                    'scan_run' => null,
                    'reason' => 'payment_required',
                    'credit_consumed' => false,
                ];
            }

            $creditConsumed = true;
            $usageRecordId = $usageRecord?->id;
            $competitorDomain->refresh();
        }

        $scanRun = CompetitorScanRun::create([
            'site_id' => $competitorDomain->site_id,
            'competitor_domain_id' => $competitorDomain->id,
            'triggered_by_type' => $triggeredByType,
            'initiated_by' => $user?->id,
            'status' => 'pending',
            'credit_consumed' => $creditConsumed,
            'usage_record_id' => $usageRecordId,
        ]);

        RunCompetitorScanJob::dispatch($scanRun->id)->onQueue('crawl');

        return [
            'status' => 'started',
            'scan_run' => $scanRun,
            'reason' => null,
            'credit_consumed' => $creditConsumed,
        ];
    }

    /**
     * @return array{started:int,blocked:int,active:int}
     */
    public function startSavedDomainScans(Site $site, ?User $user = null): array
    {
        $result = [
            'started' => 0,
            'blocked' => 0,
            'active' => 0,
        ];

        foreach ($site->competitorDomains()->get() as $competitorDomain) {
            $scan = $this->startScan($competitorDomain, $user, 'manual');

            if ($scan['status'] === 'started') {
                $result['started']++;
            } elseif ($scan['status'] === 'blocked') {
                $result['blocked']++;
            } elseif ($scan['status'] === 'active') {
                $result['active']++;
            }
        }

        return $result;
    }

    /**
     * @return array{state:string,label:string,description:string,tone:string}
     */
    public function widgetState(?Site $site, ?User $user = null): array
    {
        if (! $site) {
            return [
                'state' => 'no_competitor',
                'label' => 'No competitor',
                'description' => 'Select an active site before adding competitor domains.',
                'tone' => 'gray',
            ];
        }

        $domainCount = CompetitorDomain::query()->where('site_id', $site->id)->count();

        if ($domainCount === 0) {
            return [
                'state' => 'no_competitor',
                'label' => 'No competitor',
                'description' => 'Add a competitor domain to start a scan-backed gap comparison.',
                'tone' => 'gray',
            ];
        }

        $activeRunExists = CompetitorScanRun::query()
            ->where('site_id', $site->id)
            ->whereIn('status', ['pending', 'running'])
            ->exists();

        if ($activeRunExists) {
            return [
                'state' => 'scanning',
                'label' => 'Scanning',
                'description' => 'Competitor discovery is scanning saved domains and will refresh gap comparisons when complete.',
                'tone' => 'info',
            ];
        }

        $siteScan = \App\Support\CurrentScanResolver::resolveForSite($site);
        $hasCompletedCompetitorScan = CompetitorScanRun::query()
            ->where('site_id', $site->id)
            ->where('status', 'completed')
            ->exists();

        if (! $siteScan || ! $hasCompletedCompetitorScan) {
            return [
                'state' => 'competitor_not_scanned',
                'label' => 'Competitor not scanned',
                'description' => ! $siteScan
                    ? 'Complete a site scan first, then competitor scans can be compared against that current scan.'
                    : 'Saved competitor domains exist, but none have a completed competitor scan yet.',
                'tone' => 'warning',
            ];
        }

        $blockedRescans = CompetitorDomain::query()
            ->where('site_id', $site->id)
            ->where('scan_count', '>=', 1)
            ->where('paid_scan_credits', '<', 1)
            ->count();

        return [
            'state' => 'comparison_ready',
            'label' => 'Comparison ready',
            'description' => $blockedRescans > 0 && ! $this->bypassesLimits($user)
                ? 'Current gap comparison is ready. Additional rescans require purchased competitor scan credits.'
                : 'Current gap comparison is ready from persisted site and competitor scans.',
            'tone' => 'success',
        ];
    }

    public function collectUrlsForDomain(string $domain): array
    {
        $seed = 'https://' . trim(strtolower($domain), '/');
        $sitemapUrl = $seed . '/sitemap.xml';

        $response = Http::timeout(15)->withUserAgent('SEOAIco/1.0 Competitor Discovery')->get($sitemapUrl);

        if (! $response->successful()) {
            return [];
        }

        $xml = @simplexml_load_string($response->body());

        if (! $xml) {
            return [];
        }

        $urls = [];

        if ($xml->getName() === 'urlset') {
            foreach ($xml->url as $url) {
                $loc = trim((string) $url->loc);

                if ($loc !== '') {
                    $urls[] = $loc;
                }
            }

            return array_values(array_unique($urls));
        }

        if ($xml->getName() === 'sitemapindex') {
            foreach ($xml->sitemap as $sitemap) {
                $nested = trim((string) $sitemap->loc);

                if ($nested === '') {
                    continue;
                }

                $nestedResponse = Http::timeout(15)->withUserAgent('SEOAIco/1.0 Competitor Discovery')->get($nested);

                if (! $nestedResponse->successful()) {
                    continue;
                }

                $nestedXml = @simplexml_load_string($nestedResponse->body());

                if (! $nestedXml || $nestedXml->getName() !== 'urlset') {
                    continue;
                }

                foreach ($nestedXml->url as $url) {
                    $loc = trim((string) $url->loc);

                    if ($loc !== '') {
                        $urls[] = $loc;
                    }
                }
            }
        }

        return array_values(array_unique($urls));
    }

    public function normalizeDomain(string $domain): string
    {
        $normalized = trim(mb_strtolower($domain));
        $normalized = preg_replace('#^https?://#', '', $normalized) ?? $normalized;

        return preg_replace('#/.*$#', '', $normalized) ?? $normalized;
    }

    public function normalizeUrl(string $url): string
    {
        return rtrim(trim(mb_strtolower($url)), '/');
    }

    public function normalizePath(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        $normalized = trim(strtolower($path), '/');

        return $normalized === '' ? null : $normalized;
    }

    protected function canStartScan(CompetitorDomain $competitorDomain, ?User $user = null): bool
    {
        if ($this->bypassesLimits($user)) {
            return true;
        }

        if ($competitorDomain->scan_count < 1) {
            return true;
        }

        return $competitorDomain->paid_scan_credits > 0;
    }

    protected function bypassesLimits(?User $user = null): bool
    {
        return $user?->isSuperAdmin() === true;
    }
}