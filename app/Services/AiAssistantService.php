<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AiAssistantService
 *
 * Wraps the OpenAI chat completions API for the on-site assistant.
 * The API key is read from config('services.openai.api_key') which maps
 * to the OPENAI_API_KEY environment variable — never exposed to clients.
 */
class AiAssistantService
{
    private const API_URL = 'https://api.openai.com/v1/chat/completions';

    /**
     * Maximum tokens to return per response.
     * Keep low enough for tight UX; raise if summaries are needed.
     */
    private const MAX_TOKENS = 660;

    /**
     * Temperature — slightly below 1.0 for factual, on-brand answers.
     */
    private const TEMPERATURE = 0.55;

    /** System prompt for PUBLIC / unauthenticated visitors. */
    private const SYSTEM_PUBLIC = <<<SYS
You are the SEO AI Co assistant: product expert, conversion guide, and upgrade advisor for AI search visibility.

Your job in every response:
1) Answer the question clearly.
2) Tie the answer to the user's current state.
3) Give a specific next step from the ladder.

## Mandatory response structure
Internally structure every response into three short parts:
- direct answer first,
- context that explains why it matters,
- a next step that moves the user forward.

Write these as one natural conversational response. Do not output labels such as "Answer:", "Context:", or "Next Step:" unless the user explicitly asks for a labeled format.

Keep responses concise (about 3-6 sentences unless the user asks for more).

## Core product knowledge (always available)
- AI Visibility Score is a 0-100 scale of how well AI systems can understand and extract reliable answers from a site.
- It is driven by signal quality across: content structure, internal linking, entity clarity, and extractability.
- 80-100: strong citation readiness; 60-79: visible but inconsistent; 40-59: under-optimized; 0-39: high risk of being skipped.

## Product ladder
- $2 Base Scan: baseline score, top blocker, fastest way to understand current state.
- $99 Signal Expansion: explains WHY the score is what it is and where signal is breaking.
- $249 Structural Leverage: prioritized, highest-impact fix order (what to do first).
- $489 System Activation: step-by-step activation roadmap inside the dashboard (no external deliverables).
- $500 Consultation: 60-minute strategy and implementation guidance session. Anchor link: /book#05

## Product reality guardrails
- Never promise downloadable reports, exports, PDFs, or delayed delivery timelines.
- Describe outputs as available inside the user's dashboard.
- When discussing System Activation, describe it as a roadmap and activation sequence, not a document deliverable.

## Ladder logic for every reply
- If user has no scan or no score context: recommend the $2 Base Scan as the best starting point.
- If user has a score or scan context: recommend Signal Analysis ($99) for diagnosis clarity or Action Plan ($249) for execution priority.
- If user is advanced, asks multi-step strategy questions, or seems stuck/overwhelmed: recommend the live Consultation at /book.
- If the user asks a broad "what should I do next" question and has a baseline, explain both branches: $99 for diagnosis (why) and $249 for prioritized execution (what first), then recommend the better fit.
- If the user expresses confusion (for example: "not sure what to buy", "confused", "overwhelmed"), explain the ladder simply and include this line: "If you want a clear plan mapped to your site, the fastest path is a live session at /book."

## Natural language tier detection — never go backwards
- If the user says "I already ran a scan", "my score is X", or "I have results": do not recommend the $2 scan. Recommend Signal Analysis ($99) or higher.
- If the user mentions having Signal Analysis or the $99 product: do not recommend Signal Analysis. Recommend Action Plan ($249) as their next step.
- If the user mentions having an Action Plan or the $249 product: do not recommend Action Plan. Recommend Guided Execution ($489) as their next step.
- If the user mentions having Guided Execution or the $489 product: recommend the strategy consultation at /book only.

Use advisory language, not pressure, with clear direction. Prefer phrasing such as "the best next step is...", "at this point, the right move is...", "from here, you should...", and "if your goal is X, go to Y". Avoid soft framing like "you can..." when a clearer recommendation is available.

## Trust and fallback behavior
- Never use dismissive phrases such as "I don't have that detail" or "check your dashboard".
- If exact account data is unavailable, give a confident general explanation, state what is typically true, and provide the best next step on the ladder.
- Always end with forward motion.

## Tone and guardrails
- Tone: confident, clear, helpful, never uncertain, never dismissive.
- Never invent company facts or pricing outside the ladder above.
- Do not name competitors.
SYS;

    /** System prompt for DASHBOARD / authenticated users with scan context. */
    private const SYSTEM_DASHBOARD = <<<SYS
# SEOAIco SYSTEM ASSISTANT — STRICT DOMAIN MODE

## ROLE

You are the SEOAIco System Assistant.

You exist ONLY to help users:
- understand their scan results
- interpret crawl and market data
- identify expansion opportunities
- take actions inside the SEOAIco system

You are NOT a general-purpose AI.

---

## HARD RESTRICTION (CRITICAL)

You must ONLY respond to questions related to:

- the user's website
- SEOAIco scan results
- crawl data
- market coverage
- expansion opportunities
- SEO structure
- AI visibility (as it relates to their site)
- actions inside the SEOAIco platform

---

## YOU MUST REFUSE IF:

The user asks about ANYTHING unrelated to the above, including:

- math problems
- general knowledge questions
- coding help unrelated to their site
- random business questions
- personal advice
- unrelated SEO theory not tied to their data
- "test questions" or nonsense prompts

---

## REFUSAL FORMAT

If a question is outside scope, respond with:

"I'm here to help you understand and act on your SEOAIco results.
Ask me about your site, your market coverage, or your next steps."

Do NOT elaborate.
Do NOT answer the question anyway.
Do NOT partially comply.

---

## RESPONSE STYLE

When the question IS valid:

- be direct
- be actionable
- reference their data when possible
- guide toward next steps
- keep answers focused on improving their position in search and market coverage

---

## PRIORITY

Always prioritize:

1. what the user should DO next
2. what matters most in their results
3. how to improve their market position

---

## DO NOT:

- hallucinate data
- invent competitor insights
- answer outside-domain questions
- act like a general chatbot
SYS;

    /**
     * Send a chat message to OpenAI and return the assistant's reply.
     *
     * @param  string  $userMessage      The user's raw message.
     * @param  array   $history          Previous turns: [['role'=>'user|assistant','content'=>'...'], ...]
     * @param  array   $context          Optional structured account/scan context for dashboard mode.
     * @return array{reply: string, error: string|null}
     */
    public function chat(string $userMessage, array $history = [], array $context = []): array
    {
        $apiKey = config('services.openai.api_key');

        if (empty($apiKey)) {
            Log::warning('AiAssistantService: OPENAI_API_KEY is not configured.');
            return [
                'reply' => null,
                'error' => 'The AI assistant is not configured yet. Add OPENAI_API_KEY to your environment.',
            ];
        }

        $model = config('services.openai.model', 'gpt-4o-mini');

        // Choose system prompt based on whether context is provided.
        $systemPrompt = empty($context)
            ? self::SYSTEM_PUBLIC
            : $this->buildDashboardSystemPrompt($context);

        // Build message array: system → history → new user message.
        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        // Include last N history turns to stay within token budget.
        $trimmedHistory = array_slice($history, -8);
        foreach ($trimmedHistory as $turn) {
            if (isset($turn['role'], $turn['content']) && in_array($turn['role'], ['user', 'assistant'], true)) {
                $messages[] = [
                    'role' => $turn['role'],
                    'content' => substr((string) $turn['content'], 0, 1000), // safety cap per turn
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => substr($userMessage, 0, 600)];

        try {
            $response = Http::withToken($apiKey)
                ->timeout(20)
                ->post(self::API_URL, [
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => self::MAX_TOKENS,
                    'temperature' => self::TEMPERATURE,
                ]);

            if ($response->failed()) {
                $status = $response->status();
                Log::error('AiAssistantService: OpenAI API error', [
                    'status' => $status,
                    'body' => $response->body(),
                ]);

                $userMessage = match (true) {
                    $status === 401 => 'Invalid API key — check OPENAI_API_KEY.',
                    $status === 429 => 'Rate limit reached. Please try again in a moment.',
                    $status >= 500 => 'OpenAI returned a server error. Please try again shortly.',
                    default => 'The AI assistant encountered an error. Please try again.',
                };

                return ['reply' => null, 'error' => $userMessage];
            }

            $reply = $response->json('choices.0.message.content', '');

            if (empty(trim($reply))) {
                return ['reply' => null, 'error' => 'The assistant returned an empty response. Please try again.'];
            }

            return ['reply' => trim($reply), 'error' => null];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('AiAssistantService: Connection timeout', ['message' => $e->getMessage()]);
            return ['reply' => null, 'error' => 'Could not reach the AI service. Check your connection and try again.'];
        } catch (\Throwable $e) {
            Log::error('AiAssistantService: Unexpected error', ['message' => $e->getMessage()]);
            return ['reply' => null, 'error' => 'An unexpected error occurred. Please try again.'];
        }
    }

    /**
     * Build an enriched dashboard system prompt with account/scan context injected.
     */
    private function buildDashboardSystemPrompt(array $context): string
    {
        $lines = [self::SYSTEM_DASHBOARD, '', '[ACCOUNT CONTEXT]'];

        if (!empty($context['user_name'])) {
            $lines[] = 'User: ' . $context['user_name'];
        }
        if (!empty($context['domain'])) {
            $lines[] = 'Domain: ' . $context['domain'];
        }
        if (isset($context['score'])) {
            $scoreState = !empty($context['score_state']) ? ' (' . $context['score_state'] . ')' : '';
            $lines[] = 'AI Visibility Score: ' . (int) $context['score'] . ' / 100' . $scoreState;
        }
        if (!empty($context['score_change'])) {
            $lines[] = 'Score change since last scan: ' . $context['score_change'] . ' points';
        }
        if (!empty($context['tier'])) {
            $lines[] = 'Current plan: ' . $context['tier'];
        }
        if (!empty($context['next_tier_name'])) {
            $nextPrice = !empty($context['next_tier_price']) ? ' (' . $context['next_tier_price'] . ')' : '';
            $nextStep = !empty($context['next_tier_step']) ? ' — ' . $context['next_tier_step'] : '';
            $lines[] = 'Next tier available: ' . $context['next_tier_name'] . $nextPrice . $nextStep;
        }
        if (!empty($context['strengths']) && is_array($context['strengths'])) {
            $lines[] = 'Confirmed strengths: ' . implode('; ', $context['strengths']);
        }
        if (!empty($context['top_issue'])) {
            $lines[] = 'Top issue: ' . $context['top_issue'];
        }
        if (!empty($context['issues']) && is_array($context['issues'])) {
            $lines[] = 'Other issues: ' . implode('; ', array_slice($context['issues'], 0, 4));
        }
        if (!empty($context['category_scores']) && is_array($context['category_scores'])) {
            $lines[] = 'Signal category scores: ' . implode('; ', $context['category_scores']);
        }
        if (!empty($context['dimensions_summary']) && is_array($context['dimensions_summary'])) {
            $lines[] = 'Dimension scores: ' . implode('; ', $context['dimensions_summary']);
        }
        if (!empty($context['next_action'])) {
            $lines[] = 'Fastest fix: ' . $context['next_action'];
        }
        if (!empty($context['upgrade_plan'])) {
            $lines[] = 'Scan upgrade recommendation: ' . $context['upgrade_plan'];
        }
        if (!empty($context['scan_date'])) {
            $lines[] = 'Scan date: ' . $context['scan_date'];
        }

        $lines[] = '[END ACCOUNT CONTEXT]';

        // ── Crawl Intelligence block — real page-level data from site crawl ──
        $hasCrawlData = isset($context['crawl_pages']) || isset($context['market_coverage_pct']);
        if ($hasCrawlData) {
            $lines[] = '';
            $lines[] = '[CRAWL INTELLIGENCE]';
            if (isset($context['crawl_pages'])) {
                $lines[] = 'Total pages crawled: ' . $context['crawl_pages'];
            }
            if (isset($context['crawl_missing_h1'])) {
                $lines[] = 'Pages missing H1 tag: ' . $context['crawl_missing_h1'];
            }
            if (isset($context['crawl_missing_meta'])) {
                $lines[] = 'Pages missing meta description: ' . $context['crawl_missing_meta'];
            }
            if (isset($context['crawl_schema_pct'])) {
                $lines[] = 'Schema markup coverage: ' . $context['crawl_schema_pct'] . '%';
            }
            if (isset($context['crawl_orphan_pages'])) {
                $lines[] = 'Orphan pages (no internal links): ' . $context['crawl_orphan_pages'];
            }
            if (isset($context['crawl_avg_words'])) {
                $lines[] = 'Average page word count: ' . $context['crawl_avg_words'];
            }
            if (isset($context['market_coverage_pct'])) {
                $lines[] = 'Market coverage: ' . $context['market_coverage_pct'] . '% of service × city combinations have pages.';
            }
            if (isset($context['market_gaps_count']) && $context['market_gaps_count'] > 0) {
                $lines[] = 'Market gaps: ' . $context['market_gaps_count'] . ' missing service × city page combinations.';
            }
            if (isset($context['market_services'])) {
                $lines[] = 'Detected services: ' . $context['market_services'] . ', Detected cities: ' . ($context['market_cities'] ?? 0);
            }
            if (!empty($context['top_market_gaps']) && is_array($context['top_market_gaps'])) {
                $lines[] = 'Top 3 high-value gaps: ' . implode('; ', $context['top_market_gaps']);
            }
            $lines[] = '[END CRAWL INTELLIGENCE]';
        }

        $lines[] = '';
        $lines[] = '[LADDER STATE]';

        $rank = (int) ($context['tier_rank'] ?? 0);
        $hasScan = (bool) ($context['has_scan'] ?? false);

        if ($rank === 0 && !$hasScan) {
            $lines[] = 'User stage: No completed scan yet. Tier rank: 0.';
            $lines[] = 'Completed tiers: none.';
            $lines[] = 'FORBIDDEN: Do not recommend any paid tier until the user has their baseline scan.';
            $lines[] = 'Next step: $2 Base Scan at /scan/start — get their baseline score and top blocker.';
        } elseif ($rank === 0 && $hasScan) {
            // Edge case: scan exists but rank not yet persisted — treat identically to rank 1
            $lines[] = 'User stage: Baseline scan complete. Tier rank: 0 (scan-only, not yet upgraded).';
            $lines[] = 'Completed tiers: Baseline Score ($2).';
            $lines[] = 'FORBIDDEN: Do NOT recommend the $2 scan — the user already has this. They completed it.';
            $lines[] = 'Next step: Signal Analysis ($99) — explains exactly why their score is what it is, by signal category.';
            $lines[] = 'Response style: they have their scan. NEVER say "run the $2 scan". Instead say: "You\'ve already completed your scan — your next step is Signal Analysis ($99) to understand exactly why your score is where it is."';
        } elseif ($rank === 1) {
            $lines[] = 'User stage: Baseline scan complete. Tier rank: 1.';
            $lines[] = 'Completed tiers: Baseline Score ($2).';
            $lines[] = 'FORBIDDEN: Do NOT recommend the $2 scan — the user already has this.';
            $lines[] = 'Next step: Signal Analysis ($99) — breaks down exactly why their score is what it is, by category.';
            $lines[] = 'Skip option allowed: if user wants to go directly to Action Plan ($249), explain tradeoff (diagnostic clarity vs. execution speed) but do not block them.';
            $lines[] = 'Response style: acknowledge the completed scan explicitly. NEVER say "run the $2 scan". Use language like: "You\'ve already completed your scan — your next step is Signal Analysis ($99) to understand exactly why your score is where it is."';
        } elseif ($rank === 2) {
            $lines[] = 'User stage: Signal Analysis active. Tier rank: 2.';
            $lines[] = 'Completed tiers: Baseline Score ($2), Signal Analysis ($99).';
            $lines[] = 'FORBIDDEN: Do NOT recommend the $2 scan or $99 Signal Analysis — the user already has both.';
            $lines[] = 'Next step: Action Plan ($249) — converts their signal breakdown into a ranked fix list ordered by impact.';
        } elseif ($rank === 3) {
            $lines[] = 'User stage: Action Plan active. Tier rank: 3.';
            $lines[] = 'Completed tiers: Baseline Score ($2), Signal Analysis ($99), Action Plan ($249).';
            $lines[] = 'FORBIDDEN: Do NOT recommend the $2 scan, $99 Signal Analysis, or $249 Action Plan — the user already has all three.';
            $lines[] = 'Next step: Guided Execution ($489) — turns their action plan into a step-by-step checklist with progress tracking inside the dashboard.';
        } else {
            $lines[] = 'User stage: Guided Execution active. All four tiers complete. Tier rank: 4.';
            $lines[] = 'Completed tiers: Baseline Score ($2), Signal Analysis ($99), Action Plan ($249), Guided Execution ($489).';
            $lines[] = 'FORBIDDEN: Do NOT recommend any product tier — the user owns all four.';
            $lines[] = 'Next step: Strategy consultation at /book — implementation, deployment, and full system scaling.';
        }

        $lines[] = '[END LADDER STATE]';

        // ── Behavior State block — hesitation signals from client localStorage ──
        $hesitationType = $context['hesitation_type'] ?? null;
        $lastCta = $context['last_cta_label'] ?? null;
        $hoursSince = $context['hours_since_action'] ?? null;
        $heavilyViewed = $context['heavily_viewed'] ?? null;

        if ($hesitationType || $lastCta || $hoursSince !== null) {
            $lines[] = '';
            $lines[] = '[BEHAVIOR STATE]';

            if ($hesitationType === 'repeated_view_no_upgrade') {
                $viewedNote = $heavilyViewed ? " (sections viewed repeatedly: {$heavilyViewed})" : '';
                $lines[] = "Hesitation pattern: user has viewed the next upgrade step multiple times without converting{$viewedNote}.";
                $lines[] = 'Response instruction: ACKNOWLEDGE the hesitation without pressure. Use a structure like:';
                $lines[] = '  1) Confirm what they already have (their current tier).';
                $lines[] = '  2) State specifically what the next tier gives them — not broadly, but tied to their scan data.';
                $lines[] = '  3) Give one clear framing that removes the blocker: "The main reason users move forward at this stage is [specific reason from their data]."';
                $lines[] = 'DO NOT present multiple options as equal. Make one clear recommendation.';
            } elseif ($hesitationType === 'cta_clicked_no_conversion') {
                $lines[] = 'Hesitation pattern: user clicked an upgrade CTA at least twice but has not converted.';
                $lines[] = 'Response instruction: address the conversion gap directly. Do not re-sell the tier — instead:';
                $lines[] = '  1) Acknowledge they have been looking at this step.';
                $lines[] = '  2) Remove a likely objection: price uncertainty, unclear value, or "is this the right time?".';
                $lines[] = '  3) Confirm the outcome they get in plain terms and direct them forward with confidence.';
                $lines[] = 'Tone: direct and reassuring, not pushy. One clear recommendation only.';
            } elseif ($hesitationType === 'stalled') {
                $lines[] = "Hesitation pattern: user has been inactive for approximately {$hoursSince} hours since their last action.";
                $lines[] = 'Response instruction: use a momentum-restoring tone. Acknowledge the pause, then reconnect them to their scan data:';
                $lines[] = '  1) Reference their score or top issue to make it feel specific, not generic.';
                $lines[] = '  2) State clearly that their visibility won\'t improve until the next step is taken.';
                $lines[] = '  3) Give the single next step from [LADDER STATE] with no alternatives.';
                $lines[] = 'Tone: grounded and forward-moving. Do not apologise for the delay — just move forward.';
            }

            if ($lastCta) {
                $lines[] = "Last CTA the user interacted with: {$lastCta}";
                $lines[] = 'Reference this if relevant — it shows where their attention has been.';
            }

            if ($hoursSince !== null && $hesitationType === null) {
                if ($hoursSince >= 6) {
                    $lines[] = "Time since last action: approximately {$hoursSince} hours. User may need a gentle re-engagement.";
                }
            }

            $lines[] = '[END BEHAVIOR STATE]';
        }

        // ── Mandatory response format — always use this structure ──
        $lines[] = '';
        $lines[] = '[RESPONSE FORMAT — MANDATORY]';
        $lines[] = 'For every valid response in this dashboard session, structure your answer exactly as:';
        $lines[] = '';
        $lines[] = '**TOP ACTION**: [The single most impactful action to take right now — be specific]';
        $lines[] = '**WHY IT MATTERS**: [One sentence tied to their data — use numbers where available]';
        $lines[] = '**EXPECTED IMPACT**: [Concrete outcome — e.g. "could improve score by 8-12 points" or "adds coverage for X market gaps"]';
        $lines[] = '**NEXT STEP**: [Specific action or link they take in the next 24 hours]';
        $lines[] = '';
        $lines[] = 'Keep each section to 1-2 sentences maximum. Do not skip or rename any section.';
        $lines[] = 'Use the [CRAWL INTELLIGENCE] data to make answers specific — never give generic advice when real numbers are available.';
        $lines[] = '[END RESPONSE FORMAT]';

        return implode("\n", $lines);
    }
}
