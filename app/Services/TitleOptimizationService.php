<?php

namespace App\Services;

use App\Models\TitleRecommendation;
use App\Models\Site;
use App\Models\Page;
use App\Models\LocationPage;
use App\Models\Opportunity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * TitleOptimizationService
 * 
 * Generates AI-powered title recommendations to improve CTR
 * Uses rule-based heuristics (can be replaced with LLM integration)
 */
class TitleOptimizationService
{
    protected PerformanceAggregationService $performanceService;

    public function __construct(PerformanceAggregationService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * Generate title recommendations for a page
     */
    public function generateRecommendations(
        $page,
        int $variantsCount = 3
    ): Collection {
        $currentTitle = $page->title;
        $site = $page->site;

        // Get current performance data
        $performance = $this->getCurrentPerformance($page);

        $recommendations = [];

        // Generate multiple title variants
        for ($i = 0; $i < $variantsCount; $i++) {
            $variant = $this->generateTitleVariant($page, $currentTitle, $i);
            $confidence = $this->calculateConfidenceScore($variant, $currentTitle, $performance);

            $recommendation = TitleRecommendation::create([
                'site_id' => $site->id,
                'recommendable_type' => get_class($page),
                'recommendable_id' => $page->id,
                'current_title' => $currentTitle,
                'suggested_title' => $variant['title'],
                'reasoning' => $variant['reasoning'],
                'confidence_score' => $confidence,
                'status' => 'pending',
                'current_performance' => $performance,
                'predicted_impact' => $this->predictImpact($performance, $confidence),
                'generation_method' => 'rule_based', // or 'ai' if using LLM
                'generation_metadata' => [
                    'strategy' => $variant['strategy'],
                    'timestamp' => now()->toIso8601String(),
                ],
                'generated_at' => now(),
            ]);

            $recommendations[] = $recommendation;
        }

        return collect($recommendations);
    }

    /**
     * Generate recommendations from opportunities
     */
    public function generateFromOpportunity(Opportunity $opportunity): Collection
    {
        $page = $opportunity->opportunifiable;
        
        if (!$page) {
            Log::warning('Opportunity has no associated page', ['opportunity_id' => $opportunity->id]);
            return collect([]);
        }

        return $this->generateRecommendations($page);
    }

    /**
     * Generate recommendations for all low CTR opportunities
     */
    public function generateBatchFromOpportunities(Site $site, int $limit = 20): array
    {
        $opportunities = Opportunity::where('site_id', $site->id)
            ->where('type', 'low_ctr')
            ->where('status', 'open')
            ->orderByDesc('priority_score')
            ->limit($limit)
            ->get();

        $results = [
            'opportunities_processed' => 0,
            'recommendations_generated' => 0,
        ];

        foreach ($opportunities as $opportunity) {
            $recommendations = $this->generateFromOpportunity($opportunity);
            
            $results['opportunities_processed']++;
            $results['recommendations_generated'] += $recommendations->count();
        }

        return $results;
    }

    /**
     * Apply approved recommendation
     */
    public function applyRecommendation(TitleRecommendation $recommendation, $userId = null): bool
    {
        if ($recommendation->status !== 'approved') {
            Log::warning('Attempting to apply non-approved recommendation', [
                'recommendation_id' => $recommendation->id,
                'status' => $recommendation->status,
            ]);
            return false;
        }

        $page = $recommendation->recommendable;
        
        if (!$page) {
            return false;
        }

        try {
            // Create baseline snapshot if not exists
            if (!$recommendation->baseline_snapshot_id) {
                $snapshot = app(BaselineSnapshotService::class)
                    ->createSnapshot($page);
                
                $recommendation->update(['baseline_snapshot_id' => $snapshot->id]);
            }

            // Update page title
            $page->update(['title' => $recommendation->suggested_title]);

            // Update recommendation status
            $recommendation->update([
                'status' => 'applied',
                'applied_at' => now(),
            ]);

            // Log to optimization_runs
            $this->logOptimizationRun($recommendation);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to apply title recommendation', [
                'recommendation_id' => $recommendation->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Generate a title variant using rule-based optimization
     */
    protected function generateTitleVariant($page, string $currentTitle, int $variantIndex): array
    {
        $strategies = [
            'add_year' => [
                'title' => $this->addYear($currentTitle),
                'reasoning' => 'Adding current year can improve CTR for time-sensitive searches',
                'strategy' => 'add_year',
            ],
            'add_location' => [
                'title' => $this->emphasizeLocation($page, $currentTitle),
                'reasoning' => 'Emphasizing location can improve local search CTR',
                'strategy' => 'add_location',
            ],
            'add_benefit' => [
                'title' => $this->addBenefit($currentTitle),
                'reasoning' => 'Adding compelling benefit/value proposition',
                'strategy' => 'add_benefit',
            ],
            'shorten' => [
                'title' => $this->shortenTitle($currentTitle),
                'reasoning' => 'Shorter, punchier titles can improve mobile CTR',
                'strategy' => 'shorten',
            ],
            'add_power_words' => [
                'title' => $this->addPowerWords($currentTitle),
                'reasoning' => 'Power words increase emotional appeal and CTR',
                'strategy' => 'add_power_words',
            ],
        ];

        $strategyKeys = array_keys($strategies);
        $selectedStrategy = $strategies[$strategyKeys[$variantIndex % count($strategyKeys)]];

        return $selectedStrategy;
    }

    /**
     * Strategy: Add current year
     */
    protected function addYear(string $title): string
    {
        $year = now()->year;
        
        // If title already has a year, replace it
        if (preg_match('/\b(20\d{2})\b/', $title, $matches)) {
            return preg_replace('/\b20\d{2}\b/', $year, $title);
        }

        // Otherwise add at the beginning
        return "$year - $title";
    }

    /**
     * Strategy: Emphasize location
     */
    protected function emphasizeLocation($page, string $title): string
    {
        if ($page instanceof LocationPage) {
            $location = $page->city ? $page->city->name : ($page->county ? $page->county->name : null);
            
            if ($location && !str_contains($title, $location)) {
                return "$title | $location";
            }
        }

        return $title;
    }

    /**
     * Strategy: Add benefit/value proposition
     */
    protected function addBenefit(string $title): string
    {
        $benefits = [
            '24/7 Service',
            'Fast Response',
            'Licensed & Insured',
            'Free Quote',
            'Same Day Service',
        ];

        $benefit = $benefits[array_rand($benefits)];
        
        return "$title - $benefit";
    }

    /**
     * Strategy: Shorten title
     */
    protected function shortenTitle(string $title): string
    {
        if (strlen($title) < 50) {
            return $title; // Already short
        }

        // Take first clause before pipe or dash
        if (preg_match('/^([^|–-]+)/', $title, $matches)) {
            return trim($matches[1]);
        }

        return substr($title, 0, 50) . '...';
    }

    /**
     * Strategy: Add power words
     */
    protected function addPowerWords(string $title): string
    {
        $powerWords = [
            'Expert',
            'Professional',
            'Trusted',
            'Certified',
            'Top-Rated',
            'Best',
            '#1',
        ];

        $word = $powerWords[array_rand($powerWords)];
        
        return "$word $title";
    }

    /**
     * Get current performance for a page
     */
    protected function getCurrentPerformance($page): ?array
    {
        $summary = $this->performanceService->get30DaySummary($page);

        if (!$summary) {
            return null;
        }

        return [
            'impressions' => $summary['impressions'],
            'clicks' => $summary['clicks'],
            'ctr' => $summary['ctr'],
            'position' => $summary['position'],
            'period' => '30_days',
        ];
    }

    /**
     * Calculate confidence score for a title variant
     */
    protected function calculateConfidenceScore(array $variant, string $currentTitle, ?array $performance): float
    {
        $score = 50; // Base score

        // Increase for specific strategies
        $strategyBonus = [
            'add_year' => 15,
            'add_location' => 20,
            'add_benefit' => 10,
            'add_power_words' => 10,
        ];

        $score += $strategyBonus[$variant['strategy']] ?? 0;

        // Bonus if current CTR is low
        if ($performance && $performance['ctr'] < 0.02) {
            $score += 15;
        }

        // Check title length (optimal 50-60 chars)
        $length = strlen($variant['title']);
        if ($length >= 50 && $length <= 60) {
            $score += 10;
        }

        return min(100, max(0, $score));
    }

    /**
     * Predict impact of title change
     */
    protected function predictImpact(?array $performance, float $confidence): array
    {
        if (!$performance) {
            return [
                'estimated_ctr_lift' => 0.5, // 50% relative improvement
                'estimated_click_gain' => 0,
            ];
        }

        // Estimate CTR lift based on confidence
        $ctrLift = ($confidence / 100) * 0.8; // Up to 80% relative improvement
        
        $estimatedClickGain = $performance['impressions'] * $performance['ctr'] * $ctrLift;

        return [
            'estimated_ctr_lift' => round($ctrLift, 2),
            'estimated_click_gain' => round($estimatedClickGain),
            'confidence' => $confidence,
        ];
    }

    /**
     * Log optimization to optimization_runs table
     */
    protected function logOptimizationRun(TitleRecommendation $recommendation): void
    {
        // This would integrate with existing OptimizationRun model
        // Left as placeholder
    }
}
