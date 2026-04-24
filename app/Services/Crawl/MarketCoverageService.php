<?php

namespace App\Services\Crawl;

use App\Models\Site;

/**
 * Computes market coverage for a site based on its detected URL structure.
 *
 * "Coverage" measures how many (service × city) combinations the site has
 * actually built pages for, versus the theoretical total. Missing combinations
 * are the product's core growth insight: each gap = a page that should exist.
 *
 * High-value gaps are prioritised by how often the service AND the city appear
 * elsewhere on the site (frequency-weighted importance).
 */
class MarketCoverageService
{
    public function __construct(
        private readonly StructureDetectionService $structureDetection
    ) {
    }

    public function compute(Site $site): ?array
    {
        $structure = $this->structureDetection->detect($site);

        if ($structure === null) {
            return null;
        }

        $services = $structure['services'];   // [{slug, label, page_count}, ...]
        $cities = $structure['cities'];     // [{slug, label, page_count}, ...]
        $existing = $structure['combinations']; // [{service, city, url_count, ...}]

        $serviceCount = count($services);
        $cityCount = count($cities);

        if ($serviceCount === 0 || $cityCount === 0) {
            // Can still return partial data — useful when only one axis exists
            return [
                'services_count' => $serviceCount,
                'cities_count' => $cityCount,
                'combinations_found' => count($existing),
                'combinations_possible' => 0,
                'coverage_pct' => 0.0,
                'missing_combinations' => [],
                'high_value_gaps' => [],
                'structure' => $structure,
                'is_partial' => $structure['is_partial'],
            ];
        }

        // Build a set of existing (service, city) pair keys for O(1) lookup
        $existingKeys = [];
        foreach ($existing as $combo) {
            $existingKeys["{$combo['service']}|{$combo['city']}"] = true;
        }

        $possible = $serviceCount * $cityCount;

        // --- Missing combinations -------------------------------------------
        // Every (service, city) pair that has no detected page yet.
        $missing = [];
        foreach ($services as $svc) {
            foreach ($cities as $cty) {
                $key = "{$svc['slug']}|{$cty['slug']}";
                if (!isset($existingKeys[$key])) {
                    // Priority score: sum of page_count from each axis.
                    // Higher = more established service/city on the site → higher value gap.
                    $priority = ($svc['page_count'] ?? 0) + ($cty['page_count'] ?? 0);

                    $missing[] = [
                        'service' => $svc['slug'],
                        'service_label' => $svc['label'],
                        'city' => $cty['slug'],
                        'city_label' => $cty['label'],
                        'priority' => $priority,
                        'suggested_url' => $this->suggestUrl($svc['slug'], $cty['slug']),
                        'suggested_title' => $this->suggestTitle($svc['label'], $cty['label']),
                    ];
                }
            }
        }

        // Sort missing: highest priority first
        usort($missing, fn($a, $b) => $b['priority'] <=> $a['priority']);

        // --- High-value gaps -------------------------------------------------
        // Top 10 missing combinations with the strongest business case.
        $highValueGaps = array_slice($missing, 0, 10);

        $found = count($existing);
        $coveragePct = $possible > 0 ? round($found / $possible * 100, 1) : 0.0;

        return [
            'services_count' => $serviceCount,
            'cities_count' => $cityCount,
            'combinations_found' => $found,
            'combinations_possible' => $possible,
            'coverage_pct' => $coveragePct,
            'missing_combinations' => $missing,
            'high_value_gaps' => $highValueGaps,
            'structure' => $structure,
            'is_partial' => $structure['is_partial'],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Suggestion helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Produce a canonical URL slug for a service+city combination.
     * Format: /{service}-in-{city}
     */
    private function suggestUrl(string $serviceSlag, string $citySlug): string
    {
        return '/' . $serviceSlag . '-in-' . $citySlug;
    }

    /**
     * Produce a human-readable page title for a service+city combination.
     */
    private function suggestTitle(string $serviceLabel, string $cityLabel): string
    {
        return "{$serviceLabel} in {$cityLabel}";
    }
}
