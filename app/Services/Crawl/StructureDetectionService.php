<?php

namespace App\Services\Crawl;

use App\Models\Site;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Detects structural patterns in a site's URL inventory.
 *
 * Identifies service pages, location pages, and service+location combination
 * pages using a two-pass approach:
 *
 *   Pass 1 — Typed extraction:  URLs already classified as service/location by
 *             the crawler's page_type column provide the seed sets.
 *
 *   Pass 2 — Combination detection: every URL path is searched for co-occurrence
 *             of a known service slug AND a known city slug.
 *
 * Falls back gracefully when the crawler's page_type assignments are sparse:
 * the segment-frequency analysis provides supplementary insight even when most
 * pages are classified as "other".
 */
class StructureDetectionService
{
    /** Path segments that are structural prefixes, not meaningful identifiers. */
    private const SKIP_SEGMENTS = [
        'service',
        'services',
        'location',
        'locations',
        'in',
        'near',
        'area',
        'areas',
        'page',
        'pages',
        'category',
        'categories',
        'blog',
        'news',
        'about',
        'contact',
        'www',
        'http',
        'https',
    ];

    public function detect(Site $site): ?array
    {
        $rows = DB::table('url_inventory')
            ->where('site_id', $site->id)
            ->where('status', 'completed')
            ->whereNotNull('path')
            ->where('path', '!=', '/')
            ->select(['id', 'path', 'page_type', 'incoming_link_count', 'depth'])
            ->get();

        if ($rows->isEmpty()) {
            return null;
        }

        // ── Pass 1: extract from typed pages ──────────────────────────────────
        $serviceRows = $rows->filter(fn($r) => $r->page_type === 'service');
        $locationRows = $rows->filter(fn($r) => $r->page_type === 'location');

        $serviceSlugCounts = $this->slugFrequency($serviceRows);
        $citySlugCounts = $this->slugFrequency($locationRows);

        // Build ordered arrays with labels
        $services = $this->buildEntries($serviceSlugCounts);
        $cities = $this->buildEntries($citySlugCounts);

        // ── Pass 2: combination detection ─────────────────────────────────────
        // A page is a "combination" when its path contains both a service slug
        // and a city slug. We search in URL-decoded path to handle %-encoding.
        $serviceSlugs = array_column($services, 'slug');
        $citySlugs = array_column($cities, 'slug');

        $combinations = [];
        if ($serviceSlugs && $citySlugs) {
            foreach ($rows as $row) {
                $path = strtolower(rawurldecode($row->path));

                $matchedService = null;
                foreach ($serviceSlugs as $slug) {
                    if (str_contains($path, $slug)) {
                        $matchedService = $slug;
                        break;
                    }
                }

                $matchedCity = null;
                foreach ($citySlugs as $slug) {
                    if (str_contains($path, $slug)) {
                        $matchedCity = $slug;
                        break;
                    }
                }

                if ($matchedService && $matchedCity && $matchedService !== $matchedCity) {
                    $key = "{$matchedService}|{$matchedCity}";
                    if (!isset($combinations[$key])) {
                        $combinations[$key] = [
                            'service' => $matchedService,
                            'service_label' => $this->slugToLabel($matchedService),
                            'city' => $matchedCity,
                            'city_label' => $this->slugToLabel($matchedCity),
                            'url_count' => 0,
                            'example_path' => $row->path,
                        ];
                    }
                    $combinations[$key]['url_count']++;
                }
            }
            $combinations = array_values($combinations);
            usort($combinations, fn($a, $b) => $b['url_count'] <=> $a['url_count']);
        }

        // ── Segment frequency analysis (supplementary) ────────────────────────
        // Finds top depth-1 slugs that may be service/location hubs — useful when
        // the crawler's page_type is "other" for many pages.
        $segmentFrequency = $this->topDepth1Segments($rows, $serviceSlugs, $citySlugs);

        return [
            'services' => $services,
            'cities' => $cities,
            'combinations' => $combinations,
            'service_page_count' => $serviceRows->count(),
            'location_page_count' => $locationRows->count(),
            'combination_page_count' => count($combinations),
            'segment_frequency' => $segmentFrequency,
            'is_partial' => $rows->count() < DB::table('url_inventory')
                ->where('site_id', $site->id)->count(),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Count how many rows share each "last meaningful segment" in their path.
     *
     * @param  Collection<object>  $rows
     * @return array<string, int>  slug → count, sorted desc
     */
    private function slugFrequency(Collection $rows): array
    {
        $counts = [];
        foreach ($rows as $row) {
            $slug = $this->lastMeaningfulSegment($row->path);
            if ($slug !== '') {
                $counts[$slug] = ($counts[$slug] ?? 0) + 1;
            }
        }
        arsort($counts);
        return $counts;
    }

    /**
     * Convert a slug → count map into the structured entry array.
     *
     * @param  array<string, int>  $slugCounts
     * @return list<array{slug: string, label: string, page_count: int}>
     */
    private function buildEntries(array $slugCounts): array
    {
        $entries = [];
        foreach ($slugCounts as $slug => $count) {
            $entries[] = [
                'slug' => $slug,
                'label' => $this->slugToLabel($slug),
                'page_count' => $count,
            ];
        }
        return $entries;
    }

    /**
     * Return the last path segment that is not a structural skip word.
     */
    private function lastMeaningfulSegment(string $path): string
    {
        $path = strtolower(rawurldecode(trim($path, '/')));
        $segments = array_values(array_filter(explode('/', $path)));

        // Walk from the end to find the first non-skip segment
        foreach (array_reverse($segments) as $seg) {
            // Collapse hyphens to find the first atom of a compound slug
            $atoms = explode('-', $seg);
            $meaningful = array_filter($atoms, fn($a) => !in_array($a, self::SKIP_SEGMENTS, true));
            if ($meaningful) {
                return $seg; // return the full compound slug, not just the atom
            }
        }

        return '';
    }

    private function slugToLabel(string $slug): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $slug));
    }

    /**
     * Find the most frequent depth-1 URL segments across the whole site —
     * these are the "hubs" that form the top-level structure.
     *
     * Excludes slugs already known to be services or cities.
     *
     * @param  Collection<object>  $rows
     * @param  list<string>        $knownServiceSlugs
     * @param  list<string>        $knownCitySlugs
     * @return list<array{slug: string, label: string, count: int, role: string}>
     */
    private function topDepth1Segments(
        Collection $rows,
        array $knownServiceSlugs,
        array $knownCitySlugs,
        int $top = 20
    ): array {
        $counts = [];
        foreach ($rows as $row) {
            $path = strtolower(rawurldecode(trim($row->path ?? '/', '/')));
            $segments = array_values(array_filter(explode('/', $path)));

            // Only look at the first (depth-1) segment of the path
            $first = $segments[0] ?? '';
            if ($first !== '' && !in_array($first, self::SKIP_SEGMENTS, true)) {
                $counts[$first] = ($counts[$first] ?? 0) + 1;
            }
        }

        arsort($counts);
        $top20 = array_slice($counts, 0, $top, true);

        $entries = [];
        foreach ($top20 as $slug => $count) {
            $role = 'unknown';
            if (in_array($slug, $knownServiceSlugs, true)) {
                $role = 'service';
            } elseif (in_array($slug, $knownCitySlugs, true)) {
                $role = 'location';
            }

            $entries[] = [
                'slug' => $slug,
                'label' => $this->slugToLabel($slug),
                'count' => $count,
                'role' => $role,
            ];
        }

        return $entries;
    }
}
