<?php

namespace App\Services\Discovery;

use App\Models\CrawlPolicy;
use App\Models\Site;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SitemapIngestionService
{
    public function __construct(
        protected CrawlQueueService $crawlQueueService,
        protected RobotsPolicyService $robotsPolicyService,
    ) {}

    /**
     * Ingest sitemap URLs and queue discovered pages.
     *
     * @return array{discovered: int, queued: int, sitemap_count: int}
     */
    public function ingest(Site $site, ?int $scanRunId = null, int $maxUrls = 5000): array
    {
        $policy = $this->robotsPolicyService->getOrRefreshPolicy($site);
        $sitemapUrls = $policy->sitemap_urls ?? [];

        if (empty($sitemapUrls)) {
            $sitemapUrls = ['https://' . ltrim($site->domain, '/') . '/sitemap.xml'];
        }

        $seenSitemaps = [];
        $discoveredUrls = [];

        foreach ($sitemapUrls as $sitemapUrl) {
            $this->collectUrlsFromSitemap($sitemapUrl, $seenSitemaps, $discoveredUrls, $maxUrls);

            if (count($discoveredUrls) >= $maxUrls) {
                break;
            }
        }

        $queued = 0;

        foreach (array_keys($discoveredUrls) as $url) {
            if ($this->crawlQueueService->enqueueUrl($site, $url, 'sitemap', 1, null, 90, $scanRunId)) {
                $queued++;
            }
        }

        CrawlPolicy::where('site_id', $site->id)->update([
            'sitemap_urls' => array_values(array_unique($sitemapUrls)),
        ]);

        return [
            'discovered' => count($discoveredUrls),
            'queued' => $queued,
            'sitemap_count' => count($seenSitemaps),
        ];
    }

    /**
     * Parse sitemap content into URL list (used by tests).
     *
     * @return array{type: 'urlset'|'sitemapindex'|'unknown', urls: array<int, string>, sitemaps: array<int, string>}
     */
    public function parseSitemapXml(string $xml): array
    {
        libxml_use_internal_errors(true);
        $parsed = simplexml_load_string($xml);

        if (! $parsed) {
            return [
                'type' => 'unknown',
                'urls' => [],
                'sitemaps' => [],
            ];
        }

        $root = $parsed->getName();
        $urls = [];
        $sitemaps = [];

        if ($root === 'urlset') {
            foreach ($parsed->url as $url) {
                $loc = trim((string) $url->loc);

                if ($loc !== '') {
                    $urls[] = $loc;
                }
            }

            return [
                'type' => 'urlset',
                'urls' => $urls,
                'sitemaps' => [],
            ];
        }

        if ($root === 'sitemapindex') {
            foreach ($parsed->sitemap as $sitemap) {
                $loc = trim((string) $sitemap->loc);

                if ($loc !== '') {
                    $sitemaps[] = $loc;
                }
            }

            return [
                'type' => 'sitemapindex',
                'urls' => [],
                'sitemaps' => $sitemaps,
            ];
        }

        return [
            'type' => 'unknown',
            'urls' => [],
            'sitemaps' => [],
        ];
    }

    protected function collectUrlsFromSitemap(string $sitemapUrl, array &$seenSitemaps, array &$discoveredUrls, int $maxUrls): void
    {
        if (isset($seenSitemaps[$sitemapUrl])) {
            return;
        }

        $seenSitemaps[$sitemapUrl] = true;

        try {
            $response = Http::timeout(25)
                ->withUserAgent('SEOAIco/1.0 (SEO Crawler)')
                ->get($sitemapUrl);

            if (! $response->successful()) {
                return;
            }

            $parsed = $this->parseSitemapXml($response->body());

            if ($parsed['type'] === 'urlset') {
                foreach ($parsed['urls'] as $url) {
                    $discoveredUrls[$url] = true;

                    if (count($discoveredUrls) >= $maxUrls) {
                        return;
                    }
                }

                return;
            }

            if ($parsed['type'] === 'sitemapindex') {
                foreach ($parsed['sitemaps'] as $nested) {
                    $this->collectUrlsFromSitemap($nested, $seenSitemaps, $discoveredUrls, $maxUrls);

                    if (count($discoveredUrls) >= $maxUrls) {
                        return;
                    }
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('Sitemap ingestion error', [
                'sitemap_url' => $sitemapUrl,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
