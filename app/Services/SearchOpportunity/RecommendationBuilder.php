<?php

namespace App\Services\SearchOpportunity;

class RecommendationBuilder
{
    public function build(array $fact, string $category): array
    {
        $service = $fact['service'];
        $city = $fact['city'];
        $signals = $fact['signals'] ?? [];
        $expectedUrl = $fact['expected_url'];

        return match ($category) {
            'missing_page' => [
                'reason_summary' => sprintf(
                    '%s has no durable %s page for %s even though this market is in the active coverage set.',
                    $fact['site']->name,
                    $service->name,
                    $city->name
                ),
                'recommended_action' => sprintf(
                    'Create and publish a dedicated %s page targeting %s at %s, then add supporting internal links from hub and adjacent location pages.',
                    $service->name,
                    $city->name,
                    $expectedUrl
                ),
            ],
            'optimization_candidate' => [
                'reason_summary' => $this->optimizationReason($fact),
                'recommended_action' => $this->optimizationAction($fact),
            ],
            'structural_weakness' => [
                'reason_summary' => sprintf(
                    'The existing %s page for %s shows weak internal discoverability or thin structural signals, which limits cluster performance.',
                    $service->name,
                    $city->name
                ),
                'recommended_action' => 'Increase internal link support from relevant service and location hubs, improve schema coverage, and verify the page is reachable within shallow crawl depth.',
            ],
            'coverage_gap' => [
                'reason_summary' => sprintf(
                    'This service × location combination is under-covered relative to the site\'s active market footprint and should be captured before adjacent demand leaks elsewhere.'
                ),
                'recommended_action' => sprintf(
                    'Prioritize a net-new landing page for %s in %s, then connect it into the local service cluster and monitor impressions after publishing.',
                    $service->name,
                    $city->name
                ),
            ],
            default => [
                'reason_summary' => 'Rule-based mapping detected a growth opportunity that is materially below expected coverage or performance.',
                'recommended_action' => 'Review the underlying signals and decide whether to create, optimize, or strengthen the page within its cluster.',
            ],
        };
    }

    protected function optimizationReason(array $fact): string
    {
        $service = $fact['service'];
        $city = $fact['city'];
        $signals = $fact['signals'] ?? [];

        if (($signals['impressions_30d'] ?? 0) >= 300 && ($signals['ctr_30d'] ?? 0) < 2.0) {
            return sprintf(
                'The %s page for %s is already earning impressions but CTR is weak, indicating the page is visible enough to optimize before expanding elsewhere.',
                $service->name,
                $city->name
            );
        }

        return sprintf(
            'The existing %s page for %s is missing core metadata or schema signals, so it is a better near-term optimization target than a brand new build.',
            $service->name,
            $city->name
        );
    }

    protected function optimizationAction(array $fact): string
    {
        $signals = $fact['signals'] ?? [];

        if (($signals['impressions_30d'] ?? 0) >= 300 && ($signals['ctr_30d'] ?? 0) < 2.0) {
            return 'Rewrite title and meta description for click intent, confirm H1 alignment with target query, and keep the existing URL while monitoring CTR over the next 30 days.';
        }

        return 'Fill missing title, meta description, H1, and schema fields first, then review content depth and internal links before considering new page generation.';
    }
}
