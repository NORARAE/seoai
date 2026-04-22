<?php

namespace App\Http\Controllers;

use App\Models\AiChatLog;
use App\Models\QuickScan;
use App\Services\AiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiAssistantController extends Controller
{
    public function __construct(private readonly AiAssistantService $assistant)
    {
    }

    /**
     * POST /ai/chat
     *
     * Accepts a user message and optional conversation history,
     * enriches the request with account/scan context when authenticated,
     * and returns the assistant's reply.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:600'],
            'history' => ['nullable', 'array', 'max:16'],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:1000'],
            'session_id' => ['nullable', 'string', 'max:120'],
            'scan_id' => ['nullable', 'integer', 'min:1'],
            'domain' => ['nullable', 'string', 'max:255'],
            'context_page' => ['nullable', 'string', 'max:60'],
            'context' => ['nullable', 'string', 'max:60'],
            'behavior_signals' => ['nullable', 'array'],
            'behavior_signals.hesitation_type' => ['nullable', 'string', 'max:60', 'in:repeated_view_no_upgrade,cta_clicked_no_conversion,stalled'],
            'behavior_signals.cta_label' => ['nullable', 'string', 'max:80'],
            'behavior_signals.hours_since_action' => ['nullable', 'integer', 'min:0', 'max:8760'],
            'behavior_signals.heavily_viewed' => ['nullable', 'string', 'max:200'],
        ]);

        $context = [];

        // When the user is authenticated, build scan-aware context.
        if (Auth::check()) {
            $user = Auth::user();
            $context = $this->buildUserContext($user);

            // Attach behavior signals from the client if present (client-side localStorage)
            $signals = $validated['behavior_signals'] ?? [];
            if (!empty($signals) && is_array($signals)) {
                $context['hesitation_type']    = $signals['hesitation_type'] ?? null;
                $context['last_cta_label']     = isset($signals['cta_label']) ? substr((string) $signals['cta_label'], 0, 80) : null;
                $context['hours_since_action'] = isset($signals['hours_since_action']) ? (int) $signals['hours_since_action'] : null;
                $context['heavily_viewed']     = isset($signals['heavily_viewed']) ? substr((string) $signals['heavily_viewed'], 0, 200) : null;
            }
        }

        $result = $this->assistant->chat(
            userMessage: $validated['message'],
            history: $validated['history'] ?? [],
            context: $context,
        );

        $this->persistChatLog(
            request: $request,
            validated: $validated,
            accountContext: $context,
            aiReply: $result['error'] === null ? $result['reply'] : null,
        );

        if ($result['error'] !== null) {
            // Return as 200 with error flag so the front-end can display gracefully.
            return response()->json([
                'ok' => false,
                'error' => $result['error'],
                'reply' => null,
            ]);
        }

        return response()->json([
            'ok' => true,
            'reply' => $result['reply'],
            'error' => null,
        ]);
    }

    /**
     * Persist chat telemetry for intent and UX analysis without blocking chat UX.
     */
    private function persistChatLog(Request $request, array $validated, array $accountContext, ?string $aiReply): void
    {
        try {
            $message = trim((string) ($validated['message'] ?? ''));

            $sessionId = $validated['session_id']
                ?? ($request->hasSession() ? $request->session()->getId() : null);

            $scanId = $validated['scan_id'] ?? null;
            $domain = $validated['domain'] ?? ($accountContext['domain'] ?? null);

            AiChatLog::create([
                'session_id' => $sessionId,
                'scan_id' => $scanId,
                'domain' => $domain,
                'user_message' => $message,
                'ai_response' => $aiReply,
                'intent' => $this->classifyIntent($message),
                'context_page' => $this->resolveContextPage($request, $validated),
            ]);
        } catch (\Throwable $e) {
            Log::warning('AiAssistantController: failed to persist ai chat log', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function classifyIntent(string $message): string
    {
        $haystack = strtolower($message);

        if (str_contains($haystack, 'what is')) {
            return 'education';
        }

        if (str_contains($haystack, 'should i')) {
            return 'evaluation';
        }

        if (str_contains($haystack, 'what do i do')) {
            return 'action';
        }

        if (str_contains($haystack, 'upgrade') || str_contains($haystack, '$')) {
            return 'upgrade';
        }

        return 'general';
    }

    private function resolveContextPage(Request $request, array $validated): string
    {
        $raw = strtolower(trim((string) (
            $validated['context_page']
            ?? $validated['context']
            ?? ''
        )));

        if ($raw === '') {
            $refererPath = parse_url((string) $request->headers->get('referer', ''), PHP_URL_PATH);
            $raw = strtolower((string) ($refererPath ?? ''));
        }

        if (str_contains($raw, 'modal')) {
            return 'modal';
        }

        if (str_contains($raw, 'dashboard')) {
            return 'dashboard';
        }

        if (str_contains($raw, 'result') || str_contains($raw, 'scan')) {
            return 'result';
        }

        return 'landing';
    }

    /**
     * Build safe, user-scoped context from the authenticated user's latest scan.
     * Only passes fields already available and safe to surface.
     */
    private function buildUserContext(\App\Models\User $user): array
    {
        $ctx = [
            'user_name' => $user->name ?? null,
            'has_scan'  => false,
            'tier_rank' => 0,
        ];

        // Fetch the most recent completed scan for this user.
        $scan = QuickScan::where('user_id', $user->id)
            ->where('status', QuickScan::STATUS_SCANNED)
            ->whereNotNull('score')
            ->latest()
            ->first();

        if (!$scan) {
            return $ctx;
        }

        $ctx['has_scan'] = true;

        $score = (int) ($scan->score ?? 0);

        $ctx['domain'] = $scan->domain();
        $ctx['score'] = $score;
        $ctx['scan_date'] = $scan->scanned_at
            ? \Carbon\Carbon::parse($scan->scanned_at)->format('M j, Y')
            : ($scan->created_at?->format('M j, Y'));

        $ctx['score_state'] = match (true) {
            $score >= 80 => 'Strong',
            $score >= 60 => 'Average',
            $score >= 40 => 'Under-optimised',
            $score > 0 => 'At Risk',
            default => 'Not scanned',
        };

        // Pull top-level issue and action from scan intelligence if present.
        $intel = $scan->intelligence ?? [];
        $issues = [];

        foreach ($intel as $block) {
            $blockIssues = $block['issues'] ?? [];
            foreach ($blockIssues as $issue) {
                if (!empty($issue['label'])) {
                    $issues[] = $issue['label'];
                }
            }
        }

        if (!empty($issues)) {
            $ctx['top_issue'] = $issues[0];
            $ctx['issues'] = array_slice($issues, 1, 4);
        }

        if (!empty($scan->fastest_fix)) {
            $ctx['next_action'] = $scan->fastest_fix;
        }

        // Strengths — what's already working (plain strings from QuickScanService)
        $strengthLabels = array_filter(
            array_slice($scan->strengths ?? [], 0, 3),
            fn($s) => is_string($s) && !empty($s),
        );
        if (!empty($strengthLabels)) {
            $ctx['strengths'] = array_values($strengthLabels);
        }

        // Per-category signal scores — categories is associative: ['schema' => ['score' => n, ...], ...]
        $cats = $scan->categories ?? [];
        if (!empty($cats)) {
            $catScores = [];
            foreach ($cats as $key => $cat) {
                $catScores[] = $key . ': ' . (int) ($cat['score'] ?? 0);
            }
            $ctx['category_scores'] = $catScores;
        }

        // Score trend
        if (isset($scan->score_change) && $scan->score_change !== null) {
            $change = (int) $scan->score_change;
            $ctx['score_change'] = $change > 0 ? "+{$change}" : (string) $change;
        }

        // Dimension scores (e.g. "Citation Readiness: 22%")
        $dimSummary = [];
        foreach ($scan->dimensions ?? [] as $dim) {
            if (isset($dim['label'], $dim['pct'])) {
                $dimSummary[] = $dim['label'] . ': ' . $dim['pct'] . '%';
            }
        }
        if (!empty($dimSummary)) {
            $ctx['dimensions_summary'] = $dimSummary;
        }

        // Upgrade plan from scan
        if (!empty($scan->upgrade_plan)) {
            $ctx['upgrade_plan'] = (string) $scan->upgrade_plan;
        }

        // Current plan tier label — system_tier is a SystemTier enum.
        if ($user->system_tier instanceof \App\Enums\SystemTier) {
            $ctx['tier_rank'] = $user->system_tier->rank();
            $ctx['tier'] = $user->system_tier->label() . ' (' . $user->system_tier->price() . ')';

            // Next tier — what the user could unlock
            $nextTier = $user->system_tier->nextTier();
            if ($nextTier) {
                $ctx['next_tier_name'] = $nextTier->label();
                $ctx['next_tier_price'] = $nextTier->price();
            }
            $nextStep = $user->system_tier->nextStep();
            if ($nextStep) {
                $ctx['next_tier_step'] = $nextStep;
            }
        } else {
            // Has a scan but no paid tier — rank 1 (baseline scan only)
            $ctx['tier_rank'] = 1;
            $ctx['tier'] = 'Baseline Scan only ($2)';
            $ctx['next_tier_name'] = 'Signal Analysis';
            $ctx['next_tier_price'] = '$99';
            $ctx['next_tier_step'] = 'Get your full signal breakdown — $99';
        }

        return $ctx;
    }
}
