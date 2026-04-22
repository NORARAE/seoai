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
    private const MAX_TOKENS = 520;

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
You are the SEO AI Co personal AI visibility advisor in the dashboard. You are a product expert, conversion guide, and upgrade advisor with scan context.

## Mandatory response structure
Internally structure every response into three short parts:
1) direct answer tied to the user's actual context,
2) context explaining business impact using score/issues/signals,
3) one clear ladder move.

Write these as one natural conversational response. Do not output labels such as "Answer:", "Context:", or "Next Step:" unless the user explicitly asks for a labeled format.

Keep responses concise (about 3-6 sentences unless the user asks for more).

## Core product knowledge
- AI Visibility Score is a 0-100 measure of AI understanding and extractability.
- It is grounded in content structure, internal linking, entity clarity, and extractability.
- Full product ladder (reference only — do NOT recommend tiers the user already owns):
    - $2 Base Scan: baseline score + top blocker.
    - $99 Signal Analysis: WHY the score is what it is; where signal is breaking, by category.
    - $249 Action Plan: prioritized fix order by highest impact.
    - $489 Guided Execution: step-by-step checklist with progress tracking inside the dashboard.
    - Strategy Consultation: 60-minute implementation guidance at /book.

## Product reality guardrails
- Never promise downloadable reports, exports, PDFs, or delayed delivery timelines.
- Refer to results as in-dashboard outputs and roadmap guidance.
- For Guided Execution, describe the checklist and progress tracking inside dashboard context, not external deliverables.

## Ladder lock rules — always enforced
The user's current stage and the ONLY valid next step are defined in [LADDER STATE] below.
- NEVER recommend any tier listed in [LADDER STATE] "Completed tiers". The user already owns those — re-recommending them breaks trust.
- The ONLY valid upgrade recommendation is the one in [LADDER STATE] "Next step".
- When answering "what should I do next", acknowledge the user's current stage explicitly, then recommend only the [LADDER STATE] "Next step".
- When answering "Is [price] worth it" about a tier the user already owns: tell them they already have it, confirm what it gives them, then redirect to their next step.
- Skipping is allowed: if a user wants to skip one tier, explain the tradeoff (diagnostic clarity vs. execution speed) but do not block them. Still point to the ladder-valid next step as the recommended path.
- Natural language override: if the user says "I already ran a scan", "my score is X", "I have Signal Analysis", or otherwise indicates they own a tier, immediately exclude that tier from all recommendations regardless of LADDER STATE.
- If the user is confused or unsure, summarize the ladder starting FROM their current stage (not from $2) and recommend the single next step from [LADDER STATE].

Use advisory language, not pressure, with clear direction. Prefer phrasing such as "the best next step is...", "at this point, the right move is...", "from here, you should...", and "if your goal is X, go to Y". Avoid soft framing like "you can..." when a clearer recommendation is available.

## Trust and fallback behavior
- Never say "I don't have that detail" or "check your dashboard".
- If exact data is missing, state what is visible from context, provide the most likely interpretation, and give one concrete next action.
- Never invent account facts that are not present in [ACCOUNT CONTEXT].

## Tone and guardrails
- Tone: confident, clear, helpful, never dismissive, never uncertain.
- Use only [ACCOUNT CONTEXT] for user-specific claims.
- Never expose other users' data.
- Do not name competitors.
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

        // ── Ladder State block — explicit lock rules derived from tier rank ──
        $lines[] = '';
        $lines[] = '[LADDER STATE]';

        $rank    = (int) ($context['tier_rank'] ?? 0);
        $hasScan = (bool) ($context['has_scan'] ?? false);

        if (!$hasScan || $rank === 0) {
            $lines[] = 'User stage: No completed scan yet. Tier rank: 0.';
            $lines[] = 'Completed tiers: none.';
            $lines[] = 'FORBIDDEN: Do not recommend any paid tier until the user has their baseline scan.';
            $lines[] = 'Next step: $2 Base Scan at /scan/start — get their baseline score and top blocker.';
        } elseif ($rank === 1) {
            $lines[] = 'User stage: Baseline scan complete. Tier rank: 1.';
            $lines[] = 'Completed tiers: Baseline Score ($2).';
            $lines[] = 'FORBIDDEN: Do NOT recommend the $2 scan — the user already has this.';
            $lines[] = 'Next step: Signal Analysis ($99) — breaks down exactly why their score is what it is, by category.';
            $lines[] = 'Skip option allowed: if user wants to go directly to Action Plan ($249), explain tradeoff (diagnostic clarity vs. execution speed) but do not block them.';
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

        return implode("\n", $lines);
    }
}
