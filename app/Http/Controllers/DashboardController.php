<?php

namespace App\Http\Controllers;

use App\Models\FunnelEvent;
use App\Models\LocationPage;
use App\Models\QuickScan;
use App\Models\User;
use App\Services\Entitlements\EntitlementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private readonly EntitlementService $entitlements)
    {
    }

    public function index()
    {
        $user = Auth::user();

        $scanData = $this->buildUserScanData($user);
        $latestScan = $scanData['scanProjects']->first();
        $score = (int) ($latestScan?->score ?? 0);
        $scoreBand = $score >= 88 ? 'high' : ($score >= 60 ? 'mid' : 'low');

        FunnelEvent::fire(FunnelEvent::DASHBOARD_VISITED, userId: $user->id, scanId: $latestScan?->id, metadata: [
            'source_page' => 'dashboard',
            'user_state' => 'logged_in',
            'role' => ($user->isPrivilegedStaff() || $user->isFrontendDev()) ? 'staff' : 'customer',
            'score_band' => $scoreBand,
        ]);

        return view('dashboard.customer-modern', $scanData);
    }

    public function saveProfileData(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'business_name' => ['nullable', 'string', 'max:120'],
            'core_services' => ['nullable', 'string', 'max:500'],
            'primary_location' => ['nullable', 'string', 'max:120'],
            'service_areas' => ['nullable', 'string', 'max:300'],
            'website_url' => ['nullable', 'url', 'max:250'],
            'level' => ['nullable', 'integer', 'min:1', 'max:4'],
            'checkout_href' => ['nullable', 'url', 'max:500'],
        ]);

        $user = Auth::user();
        $current = is_array($user->profile_data) ? $user->profile_data : [];
        $patch = array_filter($validated, fn($v) => $v !== null && $v !== '');
        unset($patch['checkout_href']);

        $user->update(['profile_data' => array_merge($current, $patch)]);

        return response()->json(['ok' => true]);
    }

    /**
     * Customer-safe dashboard data (user-scoped only).
     */
    protected function buildUserScanData(User $user): array
    {
        // Safety net: link any orphan scans matching this user's email.
        QuickScan::where('email', $user->email)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        $scanProjects = $user->quickScans()
            ->whereIn('status', [QuickScan::STATUS_SCANNED, QuickScan::STATUS_PAID, QuickScan::STATUS_PENDING, QuickScan::STATUS_ERROR])
            ->latest()
            ->get();

        // For lead scan in System view, use only completed scans (not pending/error).
        $latestScanned = $scanProjects->first(fn(QuickScan $s) => $s->status === QuickScan::STATUS_SCANNED && $s->score !== null);
        $highestRankedScan = $scanProjects
            ->filter(fn(QuickScan $s) => $s->status === QuickScan::STATUS_SCANNED && $s->score !== null)
            ->sortByDesc(fn(QuickScan $scan) => $scan->upgradeTierRank())
            ->first();

        $totalScans = $user->quickScans()->count();

        $systemTier = $user->system_tier;

        $this->entitlements->issueForUserTier($user);
        if ($highestRankedScan) {
            $this->entitlements->issueForScan($highestRankedScan);
        }

        $accessMap = $this->entitlements->accessMap($user, $highestRankedScan);
        $tierRank = (int) ($accessMap['rank'] ?? 0);

        $completedLayers = array_values(array_filter([
            $tierRank >= 1 ? 'scan-basic' : null,
            $tierRank >= 2 ? 'signal-expansion' : null,
            $tierRank >= 3 ? 'structural-leverage' : null,
            $tierRank >= 4 ? 'system-activation' : null,
        ]));

        $nextStep = match (true) {
            $tierRank <= 1 => 'Unlock Signal Expansion',
            $tierRank === 2 => 'Unlock Structural Leverage',
            $tierRank === 3 => 'Activate Full System',
            default => null,
        };

        $nextRoute = match (true) {
            $tierRank <= 1 => 'checkout.signal-expansion',
            $tierRank === 2 => 'checkout.structural-leverage',
            $tierRank === 3 => 'checkout.system-activation',
            default => null,
        };

        $analysisLayers = [
            ['key' => 'scan-basic', 'label' => 'Base Scan', 'price' => '$2', 'complete' => (bool) ($accessMap['scan'] ?? false)],
            ['key' => 'signal-expansion', 'label' => 'Signal Expansion', 'price' => '$99', 'complete' => (bool) ($accessMap['signal'] ?? false)],
            ['key' => 'structural-leverage', 'label' => 'Structural Leverage', 'price' => '$249', 'complete' => (bool) ($accessMap['leverage'] ?? false)],
            ['key' => 'system-activation', 'label' => 'System Activation', 'price' => '$489', 'complete' => (bool) ($accessMap['activation'] ?? false)],
        ];

        $scanHistory = $scanProjects
            ->take(12)
            ->map(function (QuickScan $scan) {
                $score = (int) ($scan->score ?? 0);
                $issuesCount = is_array($scan->issues) ? count($scan->issues) : 0;

                $quickInsight = match (true) {
                    $scan->status === QuickScan::STATUS_PENDING => 'Scan queued — starting analysis shortly.',
                    $scan->status === QuickScan::STATUS_PAID => 'Analyzing your site now — check back in a moment.',
                    $scan->status === QuickScan::STATUS_ERROR => 'Scan could not be completed. Retry available.',
                    $score >= 88 => 'Strong visibility. Maintain coverage expansion.',
                    $score >= 60 => 'Position building. Structural gaps still limit reach.',
                    $issuesCount > 0 => 'Core issues detected. Priority fixes are available.',
                    default => 'Baseline captured. Continue tracking progression.',
                };

                return [
                    'scan_id' => $scan->id,
                    'public_scan_id' => $scan->publicScanId(),
                    'ai_scan_id' => $scan->aiScanId(),
                    'system_scan_id' => $scan->systemScanId(),
                    'scan_route_key' => $scan->publicScanId(),
                    'stripe_session_id' => $scan->stripe_session_id,
                    'status' => $scan->status,
                    'tier_rank' => $scan->upgradeTierRank(),
                    'is_renderable_report' => $scan->status === QuickScan::STATUS_SCANNED && $scan->score !== null,
                    'score' => $scan->score,
                    'score_change' => $scan->score_change,
                    'scanned_at' => $scan->scanned_at,
                    'created_at' => $scan->created_at,
                    'domain' => $scan->domain(),
                    'issues_count' => $issuesCount,
                    'pages_scanned' => (int) ($scan->page_count ?? 0),
                    'quick_insight' => $quickInsight,
                    'fastest_fix' => $scan->fastest_fix,
                    // Future-ready dashboard fields for client naming/tagging/filtering.
                    'scan_name' => $scan->domain(),
                    'scan_tags' => [],
                    'scan_filter_bucket' => $score >= 88 ? 'high' : ($score >= 60 ? 'mid' : 'low'),
                ];
            })
            ->values();

        $agencyModeActive = $scanProjects->count() >= 3;

        $additionalCapabilities = [
            'AI Content Deployment',
            'Ad Amplification',
            'Market Systems',
            'Web Architecture',
        ];

        $scanIntelligence = $latestScanned?->intelligence ?? [];

        $topFindings = [];
        $nextBestAction = null;

        foreach ($scanIntelligence as $tierBlock) {
            // Determine tier rank for this block
            $blockTier = $tierBlock['tier'] ?? null;
            $blockRank = match ($blockTier) {
                'scan-basic' => 1,
                'signal-expansion' => 2,
                'structural-leverage' => 3,
                'system-activation' => 4,
                default => 0,
            };

            // Check if this tier is already unlocked by comparing user's tierRank
            $isUnlocked = $blockRank > 0 && $tierRank >= $blockRank;

            foreach ($tierBlock['issues'] ?? [] as $issue) {
                // For unlocked issues, use a view CTA; for locked, use checkout
                $ctaRoute = $isUnlocked ? 'dashboard.scans.show' : ($tierBlock['route'] ?? null);

                // Parse action items from fix description
                $actionItems = $this->parseActionItems($issue['fix'] ?? '');

                $finding = [
                    'what_missing' => $issue['what_missing'] ?? $issue['key'] ?? 'Unknown',
                    'why_it_matters' => $issue['why_it_matters'] ?? '',
                    'fix' => $issue['fix'] ?? '',
                    'action_items' => $actionItems,
                    'fix_tier' => $tierBlock['label'] ?? '',
                    'fix_tier_key' => $blockTier,
                    'fix_price' => $isUnlocked ? '' : ($tierBlock['price'] ?? ''),
                    'fix_route' => $ctaRoute,
                    'is_unlocked' => $isUnlocked,
                    'status' => 'Needs Action',
                    'impact_score' => $blockRank, // Higher = more impactful
                ];

                $topFindings[] = $finding;

                // Capture first unlocked issue for "Your Next Move" panel
                if ($isUnlocked && $nextBestAction === null) {
                    $nextBestAction = $finding;
                }

                if (count($topFindings) >= 5) {
                    break 2;
                }
            }
        }

        $nextUpgrade = null;
        if ($tierRank < 4 && !empty($scanIntelligence)) {
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

        return [
            'scanProjects' => $scanProjects,
            'totalScans' => $totalScans,
            'systemTier' => $systemTier,
            'completedLayers' => $completedLayers,
            'nextStep' => $nextStep,
            'nextRoute' => $nextRoute,
            'tierRank' => $tierRank,
            'analysisLayers' => $analysisLayers,
            'topFindings' => $topFindings,
            'nextBestAction' => $nextBestAction,
            'nextUpgrade' => $nextUpgrade,
            'scanIntelligence' => $scanIntelligence,
            'entitlements' => $accessMap,
            'scanHistory' => $scanHistory,
            'additionalCapabilities' => $additionalCapabilities,
            'agencyModeActive' => $agencyModeActive,
        ];
    }

    /**
     * Parse action items from fix description text.
     * Splits on bullets, or breaks long text into actionable items.
     */
    private function parseActionItems(string $text): array
    {
        if (empty(trim($text))) {
            return [];
        }

        $items = [];

        // Try to split on line breaks or bullet points
        $lines = preg_split('/[\n•\-\*]/', $text);

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (!empty($trimmed)) {
                $items[] = $trimmed;
            }
        }

        // If we got multiple items, return up to 3
        return array_slice($items, 0, 3);
    }

    /**
     * Staff-only global dashboard data.
     */
    protected function buildStaffDashboardData(): array
    {
        $stats = [
            'total_pages' => LocationPage::count(),
            'draft_pages' => LocationPage::where('status', 'draft')->count(),
            'ready_for_review' => LocationPage::where('content_quality_status', 'approved')
                ->where('status', 'draft')
                ->count(),
            'published_pages' => LocationPage::where('status', 'published')->count(),
            'average_score' => round(LocationPage::whereNotNull('score')->avg('score') ?? 0, 1),
        ];

        $health = $this->calculateSystemHealth();

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

        $recentPages = LocationPage::with(['state', 'county', 'city'])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($page) {
                $locationParts = [];
                if ($page->city) {
                    $locationParts[] = $page->city->name;
                }
                if ($page->county) {
                    $locationParts[] = $page->county->name;
                }
                if ($page->state) {
                    $locationParts[] = $page->state->code;
                }

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

        $scoreDistribution = [
            'excellent' => LocationPage::where('score', '>=', 90)->count(),
            'good' => LocationPage::whereBetween('score', [80, 89])->count(),
            'fair' => LocationPage::whereBetween('score', [70, 79])->count(),
            'poor' => LocationPage::where('score', '<', 70)->whereNotNull('score')->count(),
            'unscored' => LocationPage::whereNull('score')->count(),
        ];

        $statusBreakdown = LocationPage::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $contentQualityBreakdown = LocationPage::select('content_quality_status', DB::raw('count(*) as count'))
            ->groupBy('content_quality_status')
            ->pluck('count', 'content_quality_status')
            ->toArray();

        return [
            'stats' => $stats,
            'health' => $health,
            'actionQueue' => $actionQueue,
            'recentPages' => $recentPages,
            'scoreDistribution' => $scoreDistribution,
            'statusBreakdown' => $statusBreakdown,
            'contentQualityBreakdown' => $contentQualityBreakdown,
        ];
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
