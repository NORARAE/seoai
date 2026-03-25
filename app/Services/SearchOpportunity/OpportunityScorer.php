<?php

namespace App\Services\SearchOpportunity;

class OpportunityScorer
{
    public function score(array $fact, string $category): array
    {
        $signals = $fact['signals'] ?? [];

        $demandScore = $this->demandScore($signals, $category);
        $readinessScore = $this->readinessScore($signals, $category, (bool) ($fact['page_exists'] ?? false));
        $businessValueScore = $this->businessValueScore($signals);
        $riskScore = $this->riskScore($signals, $category, (bool) ($fact['page_exists'] ?? false));

        $totalScore = round(
            ($demandScore * 0.35)
            + ($readinessScore * 0.25)
            + ($businessValueScore * 0.25)
            + ((100 - $riskScore) * 0.15),
            2,
        );

        return [
            'demand_score' => $demandScore,
            'readiness_score' => $readinessScore,
            'business_value_score' => $businessValueScore,
            'risk_score' => $riskScore,
            'total_score' => $totalScore,
            'priority_score' => $totalScore,
            'score_components' => [
                'weights' => [
                    'demand' => 0.35,
                    'readiness' => 0.25,
                    'business_value' => 0.25,
                    'risk_inverse' => 0.15,
                ],
                'category' => $category,
                'phase' => 'phase_1_rule_based',
                'phase_2_note' => 'Demand score currently proxies off impressions and local market size. Phase 2 should enrich with external query demand.',
            ],
        ];
    }

    protected function demandScore(array $signals, string $category): float
    {
        $impressions = (int) ($signals['impressions_30d'] ?? 0);
        $population = (int) ($signals['city_population'] ?? 0);
        $priorityBoost = (bool) ($signals['city_is_priority'] ?? false) ? 10 : 0;

        $impressionComponent = min(60, round($impressions / 20, 2));
        $populationComponent = min(30, round($population / 5000, 2));
        $categoryBoost = match ($category) {
            'missing_page' => 10,
            'coverage_gap' => 8,
            'optimization_candidate' => 5,
            default => 0,
        };

        return min(100, round($impressionComponent + $populationComponent + $priorityBoost + $categoryBoost, 2));
    }

    protected function readinessScore(array $signals, string $category, bool $pageExists): float
    {
        if (! $pageExists && $category === 'missing_page') {
            return 65.0;
        }

        $score = 0;
        $score += ($signals['title_present'] ?? false) ? 20 : 0;
        $score += ($signals['meta_description_present'] ?? false) ? 15 : 0;
        $score += ($signals['h1_present'] ?? false) ? 15 : 0;
        $score += ($signals['schema_present'] ?? false) ? 20 : 0;
        $score += min(20, round(((int) ($signals['word_count'] ?? 0)) / 25, 2));
        $score += min(10, round(((float) ($signals['payload_seo_score'] ?? 0)) / 10, 2));

        return min(100, round($score, 2));
    }

    protected function businessValueScore(array $signals): float
    {
        $population = (int) ($signals['city_population'] ?? 0);
        $priorityBoost = (bool) ($signals['city_is_priority'] ?? false) ? 25 : 0;

        return min(100, round(min(75, $population / 2500) + $priorityBoost, 2));
    }

    protected function riskScore(array $signals, string $category, bool $pageExists): float
    {
        if (! $pageExists && $category === 'missing_page') {
            return 18.0;
        }

        $score = 15.0;

        if (($signals['impressions_30d'] ?? 0) > 1000) {
            $score += 20;
        }

        if (($signals['average_position_30d'] ?? 100) <= 5) {
            $score += 20;
        }

        if (($signals['schema_present'] ?? false) && ($signals['title_present'] ?? false)) {
            $score += 10;
        }

        if (($signals['incoming_link_count'] ?? 0) <= 1) {
            $score -= 5;
        }

        if ($category === 'structural_weakness') {
            $score -= 10;
        }

        return max(5, min(100, round($score, 2)));
    }
}
