<?php

namespace App\Services;

use App\Models\BlockedIp;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * InquiryAntiSpamService
 *
 * Hard-rule pre-flight layer that runs BEFORE enrichment and database persistence.
 * Evaluates deterministic signals (IP blocklist, company names, rate limits,
 * honeypot, submission speed, VPN/proxy) and returns a structured decision.
 *
 * IMPORTANT: This service makes no outbound HTTP calls. It is fast, offline, and
 * must never throw — all errors are caught and default to allowing the submission
 * through (fail-open), which is safer than silently dropping legitimate users.
 *
 * INTEGRATION PATTERN:
 *
 *   $result = $antiSpam->evaluate($request, [
 *       'company'          => $validated['company'],
 *       'email'            => $validated['email'],
 *       'message'          => $validated['message'],
 *       'ip_is_proxy'      => null,   // known after enrichment — pass null here
 *       'recaptcha_score'  => null,   // known after enrichment — pass null here
 *       'form_loaded_at'   => $validated['form_loaded_at'] ?? null,
 *       'honeypot_value'   => $validated['website_confirm'] ?? null,
 *   ]);
 *
 *   if ($result['action'] === 'block') {
 *       // silently reject — do not reveal to submitter
 *   }
 *
 * The second evaluate() call (post-enrichment) passes ip_is_proxy + recaptcha_score.
 */
class InquiryAntiSpamService
{
    // Score thresholds
    private const SCORE_FLAG = 3;
    private const SCORE_BLOCK = 6;

    // Number of spam hits within the window before auto-blocking to DB
    private const AUTO_BLOCK_THRESHOLD = 3;
    private const AUTO_BLOCK_WINDOW_MINUTES = 10;

    /**
     * Evaluate a submission against all configured anti-spam rules.
     *
     * @param  string  $ip       The submitter's IP address.
     * @param  array   $context  {
     *     company:         ?string   — company name field value
     *     email:           ?string   — email address
     *     message:         ?string   — message body
     *     ip_is_proxy:     ?bool     — VPN/proxy flag from enrichment (null if not yet known)
     *     recaptcha_score: ?float    — reCAPTCHA v3 score (null if not yet known / not used)
     *     form_loaded_at:  ?int      — Unix timestamp when the page was loaded
     *     honeypot_value:  ?string   — value of the honeypot field (should be empty/null)
     * }
     * @return array {allowed: bool, action: 'allow'|'flag'|'block', risk_score: int, reasons: string[]}
     */
    public function evaluate(string $ip, array $context = []): array
    {
        if (!config('antispam.enabled', true)) {
            return $this->decision(0, []);
        }

        try {
            return $this->runChecks($ip, $context);
        } catch (\Throwable $e) {
            // Fail-open: log and allow if the service itself errors
            Log::error('InquiryAntiSpamService: unexpected error (fail-open)', [
                'error' => $e->getMessage(),
                'ip' => $ip,
            ]);
            return $this->decision(0, []);
        }
    }

    /**
     * Record a spam/high-risk event for an IP and auto-block if threshold is reached.
     * Call this from the controller after persisting a blocked or high-risk inquiry.
     */
    public function recordSpamHit(string $ip, string $reason = 'auto_block_repeat_spam'): void
    {
        try {
            $key = 'antispam_hits:' . md5($ip);
            $current = (int) Cache::get($key, 0);
            Cache::put($key, $current + 1, now()->addMinutes(self::AUTO_BLOCK_WINDOW_MINUTES));

            if (($current + 1) >= self::AUTO_BLOCK_THRESHOLD) {
                BlockedIp::block($ip, $reason, 'antispam_service');
                Log::info('InquiryAntiSpamService: auto-blocked IP after repeated hits', [
                    'ip' => $ip,
                    'hits' => $current + 1,
                    'reason' => $reason,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('InquiryAntiSpamService: recordSpamHit failed (non-fatal)', [
                'error' => $e->getMessage(),
                'ip' => $ip,
            ]);
        }
    }

    /**
     * Record a successful legitimate submission to reset/update rate limit tracking.
     * Call this only when a submission passes all checks and is accepted.
     */
    public function recordAccepted(string $ip, string $email): void
    {
        // Nothing to do here — rate-limit counters increment on every evaluate() call.
        // This method exists as an extension point for future trusted-submitter caching.
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PRIVATE — CHECK PIPELINE
    // ──────────────────────────────────────────────────────────────────────────

    private function runChecks(string $ip, array $ctx): array
    {
        $score = 0;
        $reasons = [];

        $add = function (int $pts, string $reason) use (&$score, &$reasons): void {
            $score += $pts;
            $reasons[] = $reason;
        };

        // ── 1. Honeypot (highest confidence — immediate block) ──────────────────
        $honeypot = $ctx['honeypot_value'] ?? null;
        if ($honeypot !== null && $honeypot !== '') {
            $add(10, 'honeypot_filled');
        }

        // ── 2a. Config IP blocklist ─────────────────────────────────────────────
        if ($this->isConfigBlockedIp($ip)) {
            $add(10, 'ip_blocklisted');
        }

        // ── 2b. Database persistent blocklist ──────────────────────────────────
        if ($this->isDbBlockedIp($ip)) {
            $add(10, 'ip_db_blocklisted');
        }

        // ── 3. Blocked company names ────────────────────────────────────────────
        $company = trim((string) ($ctx['company'] ?? ''));
        if ($company !== '' && $this->isBlockedCompany($company)) {
            $add(8, 'blocked_company_name');
        }

        // ── 4. reCAPTCHA score below threshold ──────────────────────────────────
        $recaptchaScore = $ctx['recaptcha_score'] ?? null;
        if ($recaptchaScore !== null) {
            $minScore = (float) config('antispam.recaptcha_min_score', 0.5);
            if ($recaptchaScore < $minScore) {
                $add(6, 'low_recaptcha_score');
            }
        }

        // ── 5. VPN / Proxy ──────────────────────────────────────────────────────
        $isProxy = $ctx['ip_is_proxy'] ?? null;
        if ($isProxy === true && config('antispam.block_vpn_proxy', true)) {
            // Check for high-trust signals that override VPN block
            $hasBusinessEmail = $this->hasBusinessEmail($ctx['email'] ?? null);
            if (!$hasBusinessEmail) {
                $add(6, 'vpn_proxy_no_trust');
            } else {
                $add(2, 'vpn_proxy_partial');
            }
        }

        // ── 6. Submission too fast ───────────────────────────────────────────────
        $minSeconds = (int) config('antispam.min_submit_seconds', 3);
        if ($minSeconds > 0) {
            $formLoadedAt = $ctx['form_loaded_at'] ?? null;
            if ($formLoadedAt !== null) {
                $elapsed = time() - (int) $formLoadedAt;
                if ($elapsed < $minSeconds) {
                    $add(6, 'submit_too_fast');
                }
            }
        }

        // ── 7. IP rate limiting ──────────────────────────────────────────────────
        if ($this->isIpRateLimited($ip)) {
            $add(6, 'ip_rate_limited');
        }

        // ── 8. Duplicate message fingerprint ────────────────────────────────────
        $message = trim((string) ($ctx['message'] ?? ''));
        if ($message !== '' && $this->isDuplicateMessage($ip, $message)) {
            $add(5, 'duplicate_message');
        }

        // ── 9. Cloudflare Turnstile ─────────────────────────────────────────────
        $turnstileValid = $ctx['turnstile_valid'] ?? null;   // null = not checked
        $turnstileMissing = (bool) ($ctx['turnstile_missing'] ?? false);

        if ($turnstileValid === false) {
            // Invalid token — strong bot signal
            $add(8, 'turnstile_invalid');
        } elseif ($turnstileMissing && $turnstileValid === null) {
            // No token submitted — mild signal; escalates when combined with others
            $add(2, 'turnstile_missing');
        }

        $result = $this->decision($score, $reasons);

        // Auto-block on hard signals (honeypot/blocklist always qualify)
        if ($result['action'] === 'block') {
            $this->maybeAutoBlock($ip);
        }

        return $result;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CHECK IMPLEMENTATIONS
    // ──────────────────────────────────────────────────────────────────────────

    private function isConfigBlockedIp(string $ip): bool
    {
        $blocklist = config('antispam.block_ips', []);
        return in_array($ip, (array) $blocklist, true);
    }

    private function isDbBlockedIp(string $ip): bool
    {
        try {
            return BlockedIp::isBlocked($ip);
        } catch (\Throwable) {
            // If DB is unavailable, fail-open (do not block legitimate users)
            return false;
        }
    }

    /**
     * Increment spam-hit counter for this IP; auto-insert into DB blocklist
     * if the threshold is reached within the rolling window.
     */
    private function maybeAutoBlock(string $ip): void
    {
        try {
            $key = 'antispam_hits:' . md5($ip);
            $current = (int) Cache::get($key, 0);
            Cache::put($key, $current + 1, now()->addMinutes(self::AUTO_BLOCK_WINDOW_MINUTES));

            if (($current + 1) >= self::AUTO_BLOCK_THRESHOLD) {
                BlockedIp::block($ip, 'auto_block_repeat_spam', 'antispam_service');
                Log::info('InquiryAntiSpamService: IP auto-blocked after repeated hits', [
                    'ip' => $ip,
                    'hits' => $current + 1,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('InquiryAntiSpamService: maybeAutoBlock failed (non-fatal)', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function isBlockedCompany(string $company): bool
    {
        $blocked = config('antispam.blocked_companies', []);
        $normalised = strtolower(trim($company));
        foreach ((array) $blocked as $entry) {
            if (strtolower(trim($entry)) === $normalised) {
                return true;
            }
        }
        return false;
    }

    /**
     * Increment and check rate limit counter for this IP.
     * Returns true if the IP has exceeded the allowed attempts within the window.
     */
    private function isIpRateLimited(string $ip): bool
    {
        $maxAttempts = (int) config('antispam.rate_limit_attempts', 5);
        $windowMins = (int) config('antispam.rate_limit_minutes', 10);

        if ($maxAttempts <= 0 || $windowMins <= 0) {
            return false;
        }

        $key = 'antispam_ip:' . md5($ip);
        $current = (int) Cache::get($key, 0);

        // Increment the counter — always, so every attempt counts against the window
        Cache::put($key, $current + 1, now()->addMinutes($windowMins));

        return $current >= $maxAttempts;
    }

    /**
     * Detect if the same message body has been submitted from this IP recently.
     * Uses a short hash of IP + normalised message as the cache key.
     */
    private function isDuplicateMessage(string $ip, string $message): bool
    {
        $windowMins = (int) config('antispam.duplicate_window_minutes', 10);
        if ($windowMins <= 0) {
            return false;
        }

        // Normalise: collapse whitespace, lowercase
        $normalised = preg_replace('/\s+/', ' ', strtolower(trim($message))) ?? '';
        $fingerprint = md5($ip . '|' . $normalised);
        $key = 'antispam_msg:' . $fingerprint;

        if (Cache::has($key)) {
            return true;
        }

        Cache::put($key, 1, now()->addMinutes($windowMins));
        return false;
    }

    /**
     * A business email is a rough high-trust signal to partially offset VPN detection.
     * Returns true if the email domain is not a known free/disposable provider.
     */
    private function hasBusinessEmail(?string $email): bool
    {
        if (!$email || !str_contains($email, '@')) {
            return false;
        }

        $domain = strtolower(substr(strrchr($email, '@'), 1));

        $freeOrDisposable = [
            'gmail.com',
            'googlemail.com',
            'yahoo.com',
            'hotmail.com',
            'hotmail.co.uk',
            'live.com',
            'outlook.com',
            'msn.com',
            'aol.com',
            'icloud.com',
            'me.com',
            'mac.com',
            'protonmail.com',
            'pm.me',
            'yopmail.com',
            'mailinator.com',
            'guerrillamail.com',
            'tempmail.com',
            'throwaway.email',
            'trashmail.com',
            'maildrop.cc',
            '10minutemail.com',
            'discard.email',
            'fakeinbox.com',
        ];

        return !in_array($domain, $freeOrDisposable, true);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DECISION BUILDER
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Build the final decision array from accumulated score and reasons.
     */
    private function decision(int $score, array $reasons): array
    {
        if ($score >= self::SCORE_BLOCK) {
            $action = 'block';
            $allowed = false;
        } elseif ($score >= self::SCORE_FLAG) {
            $action = 'flag';
            $allowed = true; // flag but still allow through — controller decides
        } else {
            $action = 'allow';
            $allowed = true;
        }

        return [
            'allowed' => $allowed,
            'action' => $action,
            'risk_score' => $score,
            'reasons' => $reasons,
        ];
    }
}
