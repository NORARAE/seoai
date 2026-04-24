<?php

namespace App\Services\Crawl;

use App\Models\Site;

/**
 * Computes a multi-dimensional crawl intelligence score for a site.
 *
 * All five sub-scores are derived exclusively from real crawl data.
 * Returns null when insufficient crawl data exists (< 2 completed pages).
 *
 * Score breakdown (100 points total):
 *
 *  ┌─────────────────────────┬────────┬─────────────────────────────────────┐
 *  │ Dimension               │ Max pts│ Signal                              │
 *  ├─────────────────────────┼────────┼─────────────────────────────────────┤
 *  │ structure_score         │  25    │ Service / location / combo pages    │
 *  │ coverage_score          │  25    │ Market coverage % (services × cities│
 *  │ schema_score            │  20    │ % of pages with schema markup       │
 *  │ internal_linking_score  │  20    │ Avg outgoing links, orphan pages    │
 *  │ technical_score         │  10    │ Indexable %, non-200, blocks        │
 *  └─────────────────────────┴────────┴─────────────────────────────────────┘
 */
class CrawlScoreService
{
    public function __construct(
        private readonly CrawlSummaryService $summary,
        private readonly MarketCoverageService $coverage
    ) {
    }

    public function compute(Site $site): ?array
    {
        $summaryData = $this->summary->compute($site);

        if ($summaryData === null || $summaryData['total_crawled'] < 2) {
            return null;
        }

        $coverageData = $this->coverage->compute($site);

        $structure = $this->scoreStructure($coverageData);
        $coverageScore = $this->scoreCoverage($coverageData);
        $schema = $this->scoreSchema($summaryData);
        $internalLinking = $this->scoreInternalLinking($summaryData);
        $technical = $this->scoreTechnical($summaryData);

        $total = $structure['score']
            + $coverageScore['score']
            + $schema['score']
            + $internalLinking['score']
            + $technical['score'];

        return [
            'total' => min(100, $total),
            'structure_score' => $structure,
            'coverage_score' => $coverageScore,
            'schema_score' => $schema,
            'internal_linking_score' => $internalLinking,
            'technical_score' => $technical,
            'computed_from_pages' => $summaryData['total_crawled'],
            'is_partial' => $summaryData['is_partial'],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Sub-scorers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Structure score (max 25): how well the site covers service/location structure.
     *
     * Points:
     *   8  — has at least 1 service page
     *   8  — has at least 1 location page
     *   9  — has at least 1 combination page
     */
    private function scoreStructure(?array $coverageData): array
    {
        if ($coverageData === null) {
            return $this->scoreResult(0, 25, 'No crawl structure data available.');
        }

        $structure = $coverageData['structure'] ?? null;
        $serviceCount = $structure['service_page_count'] ?? 0;
        $locationCount = $structure['location_page_count'] ?? 0;
        $comboCount = $structure['combination_page_count'] ?? 0;

        $pts = 0;
        $notes = [];

        if ($serviceCount >= 1) {
            $pts += 8;
        } else {
            $notes[] = 'No service pages detected.';
        }

        if ($locationCount >= 1) {
            $pts += 8;
        } else {
            $notes[] = 'No location pages detected.';
        }

        if ($comboCount >= 1) {
            $pts += 9;
        } else {
            $notes[] = 'No service+location combination pages detected.';
        }

        return $this->scoreResult(
            $pts,
            25,
            $notes ?: ["Service pages: {$serviceCount}, Location pages: {$locationCount}, Combos: {$comboCount}."]
        );
    }

    /**
     * Coverage score (max 25): derived from market coverage percentage.
     *
     * 0–10 % coverage → 0–10 pts (linear)
     * 10–50 % coverage → 10–20 pts (linear)
     * 50–100 % coverage → 20–25 pts (linear)
     */
    private function scoreCoverage(?array $coverageData): array
    {
        if ($coverageData === null || $coverageData['combinations_possible'] === 0) {
            return $this->scoreResult(0, 25, 'No market combination data available.');
        }

        $pct = (float) $coverageData['coverage_pct'];
        $pts = match (true) {
            $pct >= 50 => 20 + round(($pct - 50) / 50 * 5),
            $pct >= 10 => 10 + round(($pct - 10) / 40 * 10),
            default => round($pct / 10 * 10),
        };

        $missing = $coverageData['combinations_possible'] - $coverageData['combinations_found'];
        $note = "{$pct}% market coverage — {$missing} missing service+location combinations.";

        return $this->scoreResult((int) $pts, 25, $note);
    }

    /**
     * Schema score (max 20): percentage of crawled pages with schema markup.
     *
     * Coverage %  → points (linear, capped at 20)
     */
    private function scoreSchema(array $summaryData): array
    {
        $crawled = $summaryData['total_crawled'];
        $withSchema = $summaryData['pages_with_schema'];
        $pct = $summaryData['schema_coverage_pct'];

        if ($crawled === 0) {
            return $this->scoreResult(0, 20, 'No crawled pages.');
        }

        $pts = (int) min(20, round($pct / 100 * 20));
        $note = "{$pct}% schema coverage ({$withSchema} of {$crawled} pages).";

        return $this->scoreResult($pts, 20, $note);
    }

    /**
     * Internal linking score (max 20).
     *
     * Points:
     *   10 — avg outgoing internal links ≥ 3 per page (scaled 0–10)
     *   10 — orphan page rate < 20% (scaled 0–10; 0 orphans = 10 pts)
     */
    private function scoreInternalLinking(array $summaryData): array
    {
        $crawled = $summaryData['total_crawled'];
        $avgOut = (float) $summaryData['avg_outgoing_links'];
        $orphans = (int) $summaryData['orphan_pages'];

        if ($crawled === 0) {
            return $this->scoreResult(0, 20, 'No crawled pages.');
        }

        // Link density: 0–10 pts (avg outgoing ≥ 3 = full 10 pts)
        $linkPts = (int) min(10, round($avgOut / 3 * 10));

        // Orphan penalty: 0 orphans = 10 pts; orphan_rate ≥ 50% = 0 pts
        $orphanRate = $crawled > 0 ? $orphans / $crawled : 0;
        $orphanPts = (int) max(0, round((1 - min(1, $orphanRate / 0.5)) * 10));

        $pts = $linkPts + $orphanPts;
        $note = "Avg outgoing links: {$avgOut}. Orphan pages: {$orphans} of {$crawled}.";

        return $this->scoreResult($pts, 20, $note);
    }

    /**
     * Technical score (max 10): indexability health.
     *
     * Points:
     *   10 — indexable_ratio (completed pages) ≥ 80% = 10 pts
     *   Scaled linearly below 80%
     */
    private function scoreTechnical(array $summaryData): array
    {
        $crawled = $summaryData['total_crawled'];
        $indexable = $summaryData['indexable_pages'];

        if ($crawled === 0) {
            return $this->scoreResult(0, 10, 'No crawled pages.');
        }

        $ratio = $indexable / $crawled;
        $pts = (int) min(10, round($ratio / 0.8 * 10));
        $pct = round($ratio * 100, 1);
        $note = "{$pct}% of crawled pages are indexable ({$indexable} of {$crawled}).";

        return $this->scoreResult($pts, 10, $note);
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * @param  int          $score
     * @param  int          $max
     * @param  string|array $notes
     */
    private function scoreResult(int $score, int $max, string|array $notes): array
    {
        return [
            'score' => $score,
            'max' => $max,
            'pct' => $max > 0 ? round($score / $max * 100) : 0,
            'notes' => is_array($notes) ? $notes : [$notes],
        ];
    }
}
