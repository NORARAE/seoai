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
            'missing_meta' => LocationPage::where(function($query) {
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
                if ($page->city) $locationParts[] = $page->city->name;
                if ($page->county) $locationParts[] = $page->county->name;
                if ($page->state) $locationParts[] = $page->state->code;
                
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

        // Quick Scan data for the logged-in user
        $user = Auth::user();
        $latestScan = $user->quickScans()
            ->where('status', QuickScan::STATUS_SCANNED)
            ->latest()
            ->first();
        $scanHistory = $user->quickScans()
            ->where('status', QuickScan::STATUS_SCANNED)
            ->latest()
            ->take(10)
            ->get();
        $totalScans = $user->quickScans()->count();

        return view('dashboard.index', compact(
            'stats',
            'health',
            'actionQueue',
            'recentPages',
            'scoreDistribution',
            'statusBreakdown',
            'contentQualityBreakdown',
            'latestScan',
            'scanHistory',
            'totalScans'
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
                    'render'  => 0,
                    'meta'    => 0,
                    'links'   => 0,
                    'schema'  => 0,
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
        if ($score >= 90) return ['Excellent', 'green'];
        if ($score >= 80) return ['Good', 'blue'];
        if ($score >= 70) return ['Fair', 'yellow'];
        if ($score >= 50) return ['Needs Work', 'orange'];
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
