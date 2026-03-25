<?php

namespace App\Services\Discovery;

use App\Models\CompetitorDomain;
use App\Models\CompetitorGap;
use App\Models\CompetitorScanUrl;
use App\Models\InternalLink;
use App\Models\SeoOpportunity;
use App\Models\Site;
use App\Models\UrlInventory;
use App\Services\OpportunityScoreService;
use App\Support\CurrentScanResolver;

class CompetitorPageGapDiscovery
{
    public function __construct(
        protected OpportunityScoreService $opportunityScoreService,
    ) {}

    public function run(Site $site): array
    {
        $siteScanRun = CurrentScanResolver::resolveForSite($site);

        if (! $siteScanRun) {
            return ['discovered' => 0, 'created' => 0, 'compared' => 0, 'current_gaps' => 0];
        }

        $ownPaths = UrlInventory::query()
            ->where('site_id', $site->id)
            ->where('last_seen_scan_run_id', $siteScanRun->id)
            ->whereNotNull('path')
            ->pluck('path')
            ->map(fn (string $path) => trim(strtolower($path), '/'))
            ->filter()
            ->values()
            ->all();

        $domains = CompetitorDomain::query()
            ->where('site_id', $site->id)
            ->with(['scanRuns' => fn ($query) => $query->where('status', 'completed')->orderByDesc('completed_at')->orderByDesc('id')])
            ->get();

        if ($domains->isEmpty()) {
            return ['discovered' => 0, 'created' => 0, 'compared' => count($ownPaths), 'current_gaps' => 0];
        }

        CompetitorGap::query()
            ->where('site_id', $site->id)
            ->update(['is_current' => false]);

        $created = 0;
        $discovered = 0;

        foreach ($domains as $domain) {
            $latestCompetitorScan = $domain->scanRuns->first();

            if (! $latestCompetitorScan) {
                continue;
            }

            $urls = CompetitorScanUrl::query()
                ->where('competitor_scan_run_id', $latestCompetitorScan->id)
                ->whereNotNull('path')
                ->get(['url', 'path']);

            foreach ($urls as $competitorUrl) {
                $path = trim(strtolower((string) $competitorUrl->path), '/');

                if ($path === '' || in_array($path, $ownPaths, true)) {
                    continue;
                }

                $discovered++;

                $searchVolume = $this->estimateSearchVolume($path);
                $topic = $this->inferKeywordTopic($path);

                $internalLinkCoverage = $this->estimateInternalLinkCoverage($site);
                $openOpps = SeoOpportunity::query()->where('site_id', $site->id)->whereIn('status', ['pending', 'approved'])->count();

                $scoreData = $this->opportunityScoreService->scoreCompetitorGap(
                    searchVolume: $searchVolume,
                    competitorPresence: 1,
                    internalLinkSupport: max(1, min(10, $internalLinkCoverage)),
                    marketGap: $openOpps > 0 ? 9 : 6,
                    servicePriority: 70,
                );

                $gap = CompetitorGap::firstOrNew([
                    'site_id' => $site->id,
                    'keyword_topic' => $topic,
                    'competitor_domain' => $domain->domain,
                ]);

                $wasRecentlyCreated = ! $gap->exists;

                $gap->fill([
                    'competitor_domain_id' => $domain->id,
                    'site_scan_run_id' => $siteScanRun->id,
                    'competitor_scan_run_id' => $latestCompetitorScan->id,
                    'search_volume' => $searchVolume,
                    'competitor_url' => $competitorUrl->url,
                    'page_missing' => true,
                    'opportunity_score' => $scoreData['score'],
                    'score_label' => $scoreData['label'],
                    'is_current' => true,
                    'evidence' => [
                        'path' => $path,
                        'source' => 'competitor_scan',
                    ],
                ]);

                if (! $gap->exists || ! in_array($gap->status, ['queued', 'generated'], true)) {
                    $gap->status = 'open';
                }

                $gap->save();

                if ($wasRecentlyCreated) {
                    $created++;
                }
            }
        }

        return [
            'discovered' => $discovered,
            'created' => $created,
            'compared' => count($ownPaths),
            'current_gaps' => CompetitorGap::query()
                ->where('site_id', $site->id)
                ->where('site_scan_run_id', $siteScanRun->id)
                ->where('is_current', true)
                ->count(),
        ];
    }

    protected function inferKeywordTopic(string $path): string
    {
        $topic = str_replace(['/', '-'], ' ', $path);
        $topic = preg_replace('/\s+/', ' ', trim($topic)) ?: $path;

        return $topic;
    }

    protected function estimateSearchVolume(string $path): int
    {
        return 100 + (abs(crc32($path)) % 1900);
    }

    protected function estimateInternalLinkCoverage(Site $site): int
    {
        $pageCount = UrlInventory::query()->where('site_id', $site->id)->count();

        if ($pageCount === 0) {
            return 0;
        }

        $linkCount = InternalLink::query()->where('site_id', $site->id)->count();

        return (int) round(min(10, ($linkCount / max(1, $pageCount))));
    }
}
