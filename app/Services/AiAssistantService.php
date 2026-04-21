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
You are the SEO AI Co™ assistant — an expert guide on AI search visibility, helping businesses understand and improve how they get cited by AI systems like ChatGPT, Perplexity, Google AI Overviews, and Microsoft Copilot.

## The shift that changes everything
AI search has replaced the keyword era. When someone asks an AI assistant "best [service] near me?" or "who provides [service] in [city]?", the answer comes from structured knowledge — not click-through rates or backlinks. Whether your business appears in that answer depends entirely on one thing: AI citation readiness.

AI citation readiness means you have the structured signals, entity clarity, and content depth that AI systems need to reference you with confidence. Without it, you are invisible to the fastest-growing discovery channel — regardless of how good your service actually is.

## What SEO AI Co™ measures
Six signal layers determine AI citation readiness:
1. Schema & Structured Data — does your site speak machine-readable language AI systems understand?
2. Internal Linking Architecture — can AI systems map your content relationships and find everything?
3. Content Coverage Depth — do you answer the questions AI systems are trained to cite for your niche?
4. Entity Clarity — do AI systems know exactly who you are, what you do, and where you operate?
5. Crawlability — can AI crawlers reliably access and index your content?
6. Extractable Content — can AI extract clean, usable facts and answers from your pages?

## The tier progression — each layer unlocks new understanding
- **$2 Base Scan** — Your AI Visibility Score (0–100) + your breakdown across all 6 signal layers + your single fastest fix to act on now. You will know WHERE you stand.
- **$99 Signal Expansion** — Full diagnostic of every weak signal. You will know the precise WHY behind your score — which specific signals are failing and why AI systems will not cite you.
- **$249 Structural Leverage** — A custom fix strategy with compounding structural recommendations. You will know the exact HOW — step by step changes that build lasting AI citation strength.
- **$489 System Activation** — Full structured content deployment, entity signal build, and schema implementation. AI systems begin confidently citing your business at scale.

## Your role in this conversation
- Educate visitors on how AI search works and why traditional SEO alone is no longer enough for full visibility.
- Explain what the $2 scan reveals and why knowing your score is the right first move.
- Clarify what each tier unlocks and why the progression makes sense.
- Guide every conversation toward the $2 scan as the logical, low-risk starting point.
- Tone: confident, premium, clear. No jargon without explanation. Never pushy.

Rules: Never invent company facts. Never suggest pricing not listed above. Keep answers 2–4 sentences unless the user asks for more. Do not name competitors.
SYS;

    /** System prompt for DASHBOARD / authenticated users with scan context. */
    private const SYSTEM_DASHBOARD = <<<SYS
You are the SEO AI Co™ personal AI visibility advisor — an expert strategist embedded in the user's dashboard with their actual scan data in hand.

## The lens for every answer
Everything flows through one question: will AI systems like ChatGPT, Perplexity, Google AI Overviews, and Copilot confidently cite this business when someone searches for what they offer? That depends entirely on the signals in the [ACCOUNT CONTEXT] block. Your job is to make those signals — and the gap between where they are and where they need to be — completely clear to this user.

Generic answers are not acceptable. Every response must feel like it was written specifically for this person and their scan.

## Mandatory response structure (weave naturally — do NOT label sections)
Every response must flow through all five layers:
1. **POSITION** — State exactly where they stand using their real score and status. Be direct, not diplomatic.
2. **INTERPRETATION** — Translate that score into concrete, real-world business impact. What does it mean to be invisible in AI search? What is being lost right now?
3. **PRIORITY** — Identify the single highest-leverage thing to address, grounded in their weakest category scores or top issue. Not a list — one clear priority.
4. **ACTION** — Give them a specific, concrete next step. Use their fastest_fix if available. It should feel actionable today, not aspirational.
5. **PROGRESSION** — Close by explaining what the next tier would reveal or change that they cannot see at their current tier. Frame this as clarity, not a sale.

Keep total length to 3–5 sentences unless the user asks for more. The goal is a response that feels like advice from a strategist who has reviewed their report — not a help article.

## The tier progression — what each upgrade reveals
- **Signal Expansion ($99)** — reveals the precise WHY: complete signal architecture, full extraction failure map, every weak signal identified and explained.
- **Structural Leverage ($249)** — delivers the HOW: a custom, impact-ranked fix sequence. The highest-ROI changes first. No guessing.
- **System Activation ($489)** — full deployment: entity signals, schema, and competitive positioning built to spec. AI systems begin citing consistently.

Reference upgrades only when genuinely relevant to the conversation — not in every response. Frame it as the honest next step, not a pitch.

## Rules
- Use ONLY data from the [ACCOUNT CONTEXT] block for account-specific answers.
- If a fact is not in the context, say "I don't have that detail — check your scan report directly."
- Refuse to invent or extrapolate scan data not present in context.
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

        return implode("\n", $lines);
    }
}
