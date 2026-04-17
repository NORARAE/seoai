<?php

namespace App\Http\Controllers;

use App\Models\LocationPage;
use App\Models\QuickScan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary statistics
        $stats = [
            'total_pages' => LocationPage::count(),
            'draft_pages' => LocationPage::where('status', 'draft')->count(),
            'ready_for_review' => LocationPage::where('content_quality_status', 'approved')
                ->where('status', 'draft')
                ->count(),
            'published_pages' => LocationPage::where('status', 'published')->count(),
            'average_score' => round(LocationPage::whereNotNull('score')->avg('score') ?? 0, 1),
        ];

        // System health metrics
        $health = $this->calculateSystemHealth();

        // Action queue - things that need attention
        $actionQueue = [
            'missing_meta' => LocationPage::where(function ($query) {
                $query->whereNull('meta_title')
                    ->orWhereNull('meta_description')
                    ->orWhere('meta_title', '')
                    ->orWhere('meta_description', '');
            })->count(),
            'missing_internal_links' => LocationPage::whereNull('internal_links_json')->count(),
            'needs_render' => LocationPage::whereNull('rendered_html')->count(),
            'below_threshold' => LocationPage::where('score', '<', 70)->whereNotNull('score')->count(),
            'unreviewed' => LocationPage::where('content_quality_status', 'unreviewed')->count(),
        ];

        // Recent pages
        $recentPages = LocationPage::with(['state', 'county', 'city'])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($page) {
                // Format location inline instead of calling method
                $locationParts = [];
                if ($page->city)
                    $locationParts[] = $page->city->name;
                if ($page->county)
                    $locationParts[] = $page->county->name;
                if ($page->state)
                    $locationParts[] = $page->state->code;

                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'type' => str_replace('_', ' ', $page->type),
                    'location' => implode(', ', $locationParts),
                    'status' => $page->status,
                    'score' => $page->score,
                    'updated_at' => $page->updated_at,
                    'slug' => $page->slug,
                ];
            });

        // Score distribution for chart/visualization
        $scoreDistribution = [
            'excellent' => LocationPage::where('score', '>=', 90)->count(),
            'good' => LocationPage::whereBetween('score', [80, 89])->count(),
            'fair' => LocationPage::whereBetween('score', [70, 79])->count(),
            'poor' => LocationPage::where('score', '<', 70)->whereNotNull('score')->count(),
            'unscored' => LocationPage::whereNull('score')->count(),
        ];

        // Status breakdown
        $statusBreakdown = LocationPage::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Content quality breakdown
        $contentQualityBreakdown = LocationPage::select('content_quality_status', DB::raw('count(*) as count'))
            ->groupBy('content_quality_status')
            ->pluck('count', 'content_quality_status')
            ->toArray();

        // Quick Scan data for the logged-in user — treat as projects
        $user = Auth::user();
        $scanProjects = $user->quickScans()
            ->whereIn('status', [QuickScan::STATUS_SCANNED, QuickScan::STATUS_PAID])
            ->latest()
            ->get();
        $totalScans = $user->quickScans()->count();

        // System tier state
        $systemTier = $user->system_tier;
        $completedLayers = $systemTier?->completedLayers() ?? [];
        $nextStep = $systemTier?->nextStep();
        $nextRoute = $systemTier?->nextRoute();
        $tierRank = $user->tierRank();

        // Build analysis layer completion map
        $analysisLayers = [
            ['key' => 'scan-basic', 'label' => 'Base Scan', 'price' => '$2', 'complete' => $tierRank >= 1],
            ['key' => 'signal-expansion', 'label' => 'Signal Expansion', 'price' => '$99', 'complete' => $tierRank >= 2],
            ['key' => 'structural-leverage', 'label' => 'Structural Leverage', 'price' => '$249', 'complete' => $tierRank >= 3],
            ['key' => 'system-activation', 'label' => 'System Activation', 'price' => '$489', 'complete' => $tierRank >= 4],
        ];

        // Pull scan intelligence from the latest scanned record (for findings + upgrade triggers)
        $latestScanned = $scanProjects->first();
        $scanIntelligence = $latestScanned?->intelligence ?? [];
        $scanDimensions = $latestScanned?->dimensions ?? [];

        // Build top findings from intelligence tiers — up to 5 most important issues
        $topFindings = [];
        foreach ($scanIntelligence as $tierBlock) {
            foreach ($tierBlock['issues'] ?? [] as $issue) {
                $topFindings[] = [
                    'what_missing' => $issue['what_missing'] ?? $issue['key'] ?? 'Unknown',
                    'why_it_matters' => $issue['why_it_matters'] ?? '',
                    'fix' => $issue['fix'] ?? '',
                    'fix_tier' => $tierBlock['label'] ?? '',
                    'fix_price' => $tierBlock['price'] ?? '',
                    'fix_route' => $tierBlock['route'] ?? '',
                ];
                if (count($topFindings) >= 5)
                    break 2;
            }
        }

        // Determine the next best upgrade action based on intelligence
        $nextUpgrade = null;
        if ($tierRank < 4 && !empty($scanIntelligence)) {
            // Find the first tier with issues that the user hasn't unlocked yet
            foreach ($scanIntelligence as $tierBlock) {
                $blockTier = $tierBlock['tier'] ?? null;
                $blockRank = match ($blockTier) {
                    'signal-expansion' => 2,
                    'structural-leverage' => 3,
                    'system-activation' => 4,
                    default => 0,
                };
                if ($blockRank > $tierRank && !empty($tierBlock['issues'])) {
                    $nextUpgrade = [
                        'label' => $tierBlock['label'],
                        'price' => $tierBlock['price'],
                        'route' => $tierBlock['route'],
                        'description' => $tierBlock['description'] ?? '',
                        'issue_count' => count($tierBlock['issues']),
                    ];
                    break;
                }
            }
        }

        return view('dashboard.index', compact(
            'stats',
            'health',
            'actionQueue',
            'recentPages',
            'scoreDistribution',
            'statusBreakdown',
            'contentQualityBreakdown',
            'scanProjects',
            'totalScans',
            'systemTier',
            'completedLayers',
            'nextStep',
            'nextRoute',
            'tierRank',
            'analysisLayers',
            'topFindings',
            'nextUpgrade',
            'scanIntelligence'
        ));
    }

    /**
     * Calculate overall system health score (0-100)
     */
    protected function calculateSystemHealth(): array
    {
        $totalPages = LocationPage::count();

        if ($totalPages === 0) {
            return [
                'score' => 0,
                'grade' => 'No Data',
                'color' => 'gray',
                'metrics' => [
                    'render' => 0,
                    'meta' => 0,
                    'links' => 0,
                    'schema' => 0,
                    'quality' => 0,
                ],
            ];
        }

        // Calculate component scores
        $renderCompleteness = (LocationPage::whereNotNull('rendered_html')->count() / $totalPages) * 100;
        $metaCompleteness = (LocationPage::whereNotNull('meta_title')
            ->whereNotNull('meta_description')
            ->where('meta_title', '!=', '')
            ->where('meta_description', '!=', '')
            ->count() / $totalPages) * 100;
        $internalLinksCompleteness = (LocationPage::whereNotNull('internal_links_json')->count() / $totalPages) * 100;
        $schemaReadiness = (LocationPage::whereNotNull('schema_json')->count() / $totalPages) * 100;
        $avgScore = LocationPage::whereNotNull('score')->avg('score') ?? 0;

        // Weighted overall score
        $overallScore = round(
            ($renderCompleteness * 0.25) +
            ($metaCompleteness * 0.20) +
            ($internalLinksCompleteness * 0.20) +
            ($schemaReadiness * 0.15) +
            ($avgScore * 0.20)
        );

        // Determine grade and color
        [$grade, $color] = $this->getGradeAndColor($overallScore);

        return [
            'score' => $overallScore,
            'grade' => $grade,
            'color' => $color,
            'metrics' => [
                'render' => round($renderCompleteness, 1),
                'meta' => round($metaCompleteness, 1),
                'links' => round($internalLinksCompleteness, 1),
                'schema' => round($schemaReadiness, 1),
                'quality' => round($avgScore, 1),
            ],
        ];
    }

    protected function getGradeAndColor(int $score): array
    {
        if ($score >= 90)
            return ['Excellent', 'green'];
        if ($score >= 80)
            return ['Good', 'blue'];
        if ($score >= 70)
            return ['Fair', 'yellow'];
        if ($score >= 50)
            return ['Needs Work', 'orange'];
        return ['Critical', 'red'];
    }

    protected function formatLocation(LocationPage $page): string
    {
        $parts = [];

        if ($page->city) {
            $parts[] = $page->city->name;
        }

        if ($page->county) {
            $parts[] = $page->county->name;
        }

        if ($page->state) {
            $parts[] = $page->state->code;
        }

        return implode(', ', $parts);
    }
}
