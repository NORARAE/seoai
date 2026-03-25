<?php

namespace App\Services;

use App\Models\SeoOpportunity;

class OpportunityScoreService
{
    public function scoreSeoOpportunity(SeoOpportunity $opportunity, int $internalLinkSupport = 0, int $competitorPresence = 0, int $marketGap = 0, int $servicePriority = 50): array
    {
        $searchVolume = max(0, (int) ($opportunity->search_volume ?? 0));
        $searchComponent = min(35, (int) round($searchVolume / 100));

        $competition = (float) ($opportunity->competition_score ?? 0);
        $competitionComponent = max(0, min(15, 15 - (int) round($competition / 7)));

        $linkComponent = max(0, min(15, $internalLinkSupport));
        $competitorComponent = max(0, min(15, $competitorPresence));
        $gapComponent = max(0, min(10, $marketGap));
        $priorityComponent = max(0, min(10, (int) round($servicePriority / 10)));

        $score = max(0, min(100, $searchComponent + $competitionComponent + $linkComponent + $competitorComponent + $gapComponent + $priorityComponent));

        return [
            'score' => $score,
            'label' => $this->label($score),
        ];
    }

    public function scoreCompetitorGap(int $searchVolume, int $competitorPresence = 1, int $internalLinkSupport = 5, int $marketGap = 8, int $servicePriority = 60): array
    {
        $searchComponent = min(45, (int) round(max(0, $searchVolume) / 60));
        $competitorComponent = max(0, min(20, $competitorPresence * 8));
        $linkComponent = max(0, min(10, $internalLinkSupport));
        $gapComponent = max(0, min(15, $marketGap));
        $priorityComponent = max(0, min(10, (int) round($servicePriority / 10)));

        $score = max(0, min(100, $searchComponent + $competitorComponent + $linkComponent + $gapComponent + $priorityComponent));

        return [
            'score' => $score,
            'label' => $this->label($score),
        ];
    }

    public function estimateMonthlyTrafficPotential(int $searchVolume, ?int $targetPosition = null): int
    {
        $position = $targetPosition && $targetPosition > 0 ? $targetPosition : 3;

        $ctr = match (true) {
            $position <= 1 => 0.30,
            $position === 2 => 0.15,
            $position === 3 => 0.10,
            $position <= 6 => 0.05,
            default => 0.03,
        };

        return (int) round($searchVolume * $ctr);
    }

    protected function label(int $score): string
    {
        return match (true) {
            $score >= 75 => 'high',
            $score >= 45 => 'medium',
            default => 'low',
        };
    }
}
