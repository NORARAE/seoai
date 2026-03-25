<?php

namespace App\Services\Sitemaps;

use App\Models\PagePayload;
use App\Models\Site;
use App\Models\UrlInventory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SitemapPublisherService
{
    public function buildIndexXml(Site $site): string
    {
        $chunks = $this->entryChunks($site);

        $xml = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($chunks as $index => $chunk) {
            $xml[] = '  <sitemap>';
            $xml[] = '    <loc>' . e($this->sitemapPageUrl($site, $index + 1)) . '</loc>';
            $xml[] = '    <lastmod>' . $this->formatDate($chunk->max('lastmod')) . '</lastmod>';
            $xml[] = '  </sitemap>';
        }

        $xml[] = '</sitemapindex>';

        return implode("\n", $xml);
    }

    public function buildPageXml(Site $site, int $page): string
    {
        $chunk = $this->entryChunks($site)->get($page - 1);

        abort_unless($chunk, 404);

        $xml = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($chunk as $entry) {
            $xml[] = '  <url>';
            $xml[] = '    <loc>' . e($entry['loc']) . '</loc>';

            if (! empty($entry['lastmod'])) {
                $xml[] = '    <lastmod>' . $this->formatDate($entry['lastmod']) . '</lastmod>';
            }

            if (! empty($entry['changefreq'])) {
                $xml[] = '    <changefreq>' . e($entry['changefreq']) . '</changefreq>';
            }

            if (! empty($entry['priority'])) {
                $xml[] = '    <priority>' . number_format((float) $entry['priority'], 1, '.', '') . '</priority>';
            }

            $xml[] = '  </url>';
        }

        $xml[] = '</urlset>';

        return implode("\n", $xml);
    }

    public function sitemapPageUrl(Site $site, int $page): string
    {
        return route('public.sitemaps.page', ['site' => $site, 'page' => $page]);
    }

    public function publicIndexUrl(Site $site): string
    {
        return route('public.sitemaps.index', ['site' => $site]);
    }

    /**
     * @return Collection<int, Collection<int, array{loc: string, lastmod: mixed, priority: float|null, changefreq: string|null}>>
     */
    public function entryChunks(Site $site): Collection
    {
        abort_unless($site->sitemap_enabled, 404);

        $entries = collect();

        if ($site->sitemap_include_payload_pages) {
            $entries = $entries->merge($this->payloadEntries($site));
        }

        if ($site->sitemap_include_discovered_pages) {
            $entries = $entries->merge($this->discoveredEntries($site));
        }

        $entries = $entries->merge($this->manualEntries($site));

        $excluded = collect($site->sitemapManualExcludeUrlList())
            ->map(fn (string $url) => $this->normalizePublicUrl($site, $url))
            ->filter();

        $entries = $entries
            ->filter(fn (array $entry) => ! $excluded->contains($entry['loc']))
            ->unique('loc')
            ->sortBy('loc')
            ->values();

        $chunkSize = max(1, (int) ($site->sitemap_max_urls_per_file ?: 500));

        return $entries->chunk($chunkSize)->values();
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: mixed, priority: float|null, changefreq: string|null}>
     */
    protected function payloadEntries(Site $site): Collection
    {
        return PagePayload::query()
            ->where('site_id', $site->id)
            ->whereIn('status', ['ready', 'published'])
            ->whereIn('publish_status', ['pending', 'published', 'exported'])
            ->whereNotNull('slug')
            ->get()
            ->map(function (PagePayload $payload) use ($site): ?array {
                $loc = $this->resolvePayloadUrl($site, $payload);

                if (! $loc) {
                    return null;
                }

                return [
                    'loc' => $loc,
                    'lastmod' => $payload->sitemap_lastmod ?? $payload->published_at ?? $payload->updated_at,
                    'priority' => $payload->sitemap_priority !== null ? (float) $payload->sitemap_priority : null,
                    'changefreq' => $payload->sitemap_changefreq,
                ];
            })
            ->filter();
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: mixed, priority: float|null, changefreq: string|null}>
     */
    protected function discoveredEntries(Site $site): Collection
    {
        return UrlInventory::query()
            ->where('site_id', $site->id)
            ->where('status', 'completed')
            ->where('indexability_status', 'indexable')
            ->get()
            ->map(function (UrlInventory $urlInventory) use ($site): ?array {
                $loc = $this->normalizePublicUrl($site, $urlInventory->normalized_url ?: $urlInventory->url);

                if (! $loc) {
                    return null;
                }

                return [
                    'loc' => $loc,
                    'lastmod' => $urlInventory->last_crawled_at ?? $urlInventory->updated_at,
                    'priority' => null,
                    'changefreq' => null,
                ];
            })
            ->filter();
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: mixed, priority: float|null, changefreq: string|null}>
     */
    protected function manualEntries(Site $site): Collection
    {
        return collect($site->sitemapManualIncludeUrlList())
            ->map(function (string $url) use ($site): ?array {
                $loc = $this->normalizePublicUrl($site, $url);

                if (! $loc) {
                    return null;
                }

                return [
                    'loc' => $loc,
                    'lastmod' => $site->updated_at,
                    'priority' => null,
                    'changefreq' => null,
                ];
            })
            ->filter();
    }

    protected function resolvePayloadUrl(Site $site, PagePayload $payload): ?string
    {
        $candidates = [
            $payload->remote_url,
            $payload->canonical_url_suggestion,
            filled($payload->slug) ? 'https://' . $site->domain . '/' . ltrim($payload->slug, '/') : null,
        ];

        foreach ($candidates as $candidate) {
            $normalized = $this->normalizePublicUrl($site, (string) $candidate);

            if ($normalized) {
                return $normalized;
            }
        }

        return null;
    }

    protected function normalizePublicUrl(Site $site, ?string $url): ?string
    {
        $value = trim((string) $url);

        if ($value === '') {
            return null;
        }

        if (Str::startsWith($value, ['/'])) {
            $value = 'https://' . $site->domain . $value;
        }

        if (! Str::startsWith($value, ['http://', 'https://'])) {
            $value = 'https://' . $site->domain . '/' . ltrim($value, '/');
        }

        $parts = parse_url($value);

        if (! is_array($parts) || empty($parts['host'])) {
            return null;
        }

        if (mb_strtolower($parts['host']) !== mb_strtolower($site->domain)) {
            return null;
        }

        return rtrim($value, '/');
    }

    protected function formatDate(mixed $date): string
    {
        return optional($date)->toAtomString() ?? now()->toAtomString();
    }
}