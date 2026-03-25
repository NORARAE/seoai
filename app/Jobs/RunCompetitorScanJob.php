<?php

namespace App\Jobs;

use App\Models\CompetitorScanRun;
use App\Services\Discovery\CompetitorPageGapDiscovery;
use App\Services\Discovery\CompetitorScanService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunCompetitorScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    public function __construct(public int $competitorScanRunId) {}

    public function handle(
        CompetitorScanService $competitorScanService,
        CompetitorPageGapDiscovery $competitorPageGapDiscovery,
    ): void {
        $scanRun = CompetitorScanRun::query()->with(['competitorDomain.site'])->findOrFail($this->competitorScanRunId);
        $competitorDomain = $scanRun->competitorDomain;
        $site = $competitorDomain->site;

        if (! $site) {
            $scanRun->update([
                'status' => 'failed',
                'completed_at' => now(),
                'error_summary' => 'Competitor scan failed because the site relationship was missing.',
            ]);

            return;
        }

        $scanRun->update([
            'status' => 'running',
            'started_at' => $scanRun->started_at ?? now(),
        ]);

        $urls = $competitorScanService->collectUrlsForDomain($competitorDomain->domain);
        $rows = [];

        foreach ($urls as $url) {
            $normalizedUrl = $competitorScanService->normalizeUrl($url);

            $rows[$normalizedUrl] = [
                'site_id' => $site->id,
                'competitor_domain_id' => $competitorDomain->id,
                'competitor_scan_run_id' => $scanRun->id,
                'url' => $url,
                'normalized_url' => $normalizedUrl,
                'path' => $competitorScanService->normalizePath((string) parse_url($url, PHP_URL_PATH)),
                'source' => 'sitemap',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $scanRun->urls()->delete();

        if ($rows !== []) {
            $scanRun->urls()->insert(array_values($rows));
        }

        $comparison = $competitorPageGapDiscovery->run($site);

        $scanRun->update([
            'status' => 'completed',
            'completed_at' => now(),
            'urls_discovered' => count($rows),
            'urls_compared' => (int) ($comparison['compared'] ?? 0),
            'gaps_found' => (int) ($comparison['current_gaps'] ?? 0),
        ]);

        $competitorDomain->forceFill([
            'scan_count' => $competitorDomain->scan_count + 1,
            'last_scanned_at' => now(),
        ])->save();
    }

    public function failed(\Throwable $exception): void
    {
        $scanRun = CompetitorScanRun::query()->find($this->competitorScanRunId);

        $scanRun?->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_summary' => mb_substr($exception->getMessage(), 0, 65535),
        ]);
    }
}