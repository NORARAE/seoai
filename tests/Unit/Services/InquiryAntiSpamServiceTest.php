<?php

namespace Tests\Unit\Services;

use App\Services\InquiryAntiSpamService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * InquiryAntiSpamServiceTest
 *
 * Unit tests for all hard-rule checks in InquiryAntiSpamService.
 * No database, no HTTP — pure in-memory evaluation with Cache faked.
 */
class InquiryAntiSpamServiceTest extends TestCase
{
    private InquiryAntiSpamService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = new InquiryAntiSpamService();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    private function cleanContext(array $overrides = []): array
    {
        return array_merge([
            'company' => 'Acme Digital Agency',
            'email' => 'contact@acmedigital.com',
            'message' => 'We are looking for SEO support for our law firm clients.',
            'ip_is_proxy' => false,
            'recaptcha_score' => null,
            'form_loaded_at' => time() - 60, // 60s ago — well above threshold
            'honeypot_value' => null,
        ], $overrides);
    }

    private function assertAllowed(array $result): void
    {
        $this->assertTrue($result['allowed'], 'Expected allowed=true, got ' . json_encode($result));
    }

    private function assertBlocked(array $result): void
    {
        $this->assertFalse($result['allowed'], 'Expected allowed=false, got ' . json_encode($result));
        $this->assertSame('block', $result['action']);
    }

    private function assertFlagged(array $result): void
    {
        $this->assertTrue($result['allowed'], 'Expected allowed=true (flagged), got ' . json_encode($result));
        $this->assertSame('flag', $result['action']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CLEAN SUBMISSION
    // ──────────────────────────────────────────────────────────────────────────

    public function test_clean_submission_is_allowed(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext());

        $this->assertAllowed($result);
        $this->assertSame('allow', $result['action']);
        $this->assertSame(0, $result['risk_score']);
        $this->assertEmpty($result['reasons']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DISABLED
    // ──────────────────────────────────────────────────────────────────────────

    public function test_disabled_service_always_allows(): void
    {
        config(['antispam.enabled' => false]);

        $result = $this->service->evaluate('80.94.95.202', $this->cleanContext([
            'honeypot_value' => 'filled',
            'company' => 'google',
        ]));

        $this->assertAllowed($result);
        $this->assertSame('allow', $result['action']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HONEYPOT
    // ──────────────────────────────────────────────────────────────────────────

    public function test_filled_honeypot_is_blocked(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'honeypot_value' => 'http://spam.com',
        ]));

        $this->assertBlocked($result);
        $this->assertContains('honeypot_filled', $result['reasons']);
    }

    public function test_empty_string_honeypot_is_not_triggered(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'honeypot_value' => '',
        ]));

        $this->assertAllowed($result);
        $this->assertNotContains('honeypot_filled', $result['reasons']);
    }

    public function test_null_honeypot_is_not_triggered(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'honeypot_value' => null,
        ]));

        $this->assertAllowed($result);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // IP BLOCKLIST
    // ──────────────────────────────────────────────────────────────────────────

    public function test_blocklisted_ip_is_blocked(): void
    {
        $result = $this->service->evaluate('80.94.95.202', $this->cleanContext());

        $this->assertBlocked($result);
        $this->assertContains('ip_blocklisted', $result['reasons']);
    }

    public function test_non_blocklisted_ip_is_not_blocked_by_ip_rule(): void
    {
        $result = $this->service->evaluate('8.8.8.8', $this->cleanContext());

        $this->assertNotContains('ip_blocklisted', $result['reasons']);
    }

    public function test_blocklist_can_be_extended_via_config(): void
    {
        config(['antispam.block_ips' => ['80.94.95.202', '1.1.1.1']]);
        $this->service = new InquiryAntiSpamService(); // re-instantiate to pick up config

        $result = $this->service->evaluate('1.1.1.1', $this->cleanContext());

        $this->assertBlocked($result);
        $this->assertContains('ip_blocklisted', $result['reasons']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // BLOCKED COMPANY NAMES
    // ──────────────────────────────────────────────────────────────────────────

    public function test_google_company_is_blocked(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'company' => 'google',
        ]));

        $this->assertBlocked($result);
        $this->assertContains('blocked_company_name', $result['reasons']);
    }

    public function test_company_match_is_case_insensitive(): void
    {
        foreach (['Google', 'GOOGLE', 'gOoGlE'] as $variation) {
            Cache::flush();
            $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
                'company' => $variation,
            ]));
            $this->assertBlocked($result, "Expected block for company: {$variation}");
        }
    }

    public function test_legitimate_company_name_is_not_blocked(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'company' => 'Googlestein & Associates',
        ]));

        // Must be exact match — partial match should NOT trigger
        $this->assertNotContains('blocked_company_name', $result['reasons']);
    }

    public function test_all_default_blocked_companies_are_blocked(): void
    {
        $blocked = ['google', 'test', 'admin', 'seo', 'facebook', 'microsoft', 'amazon'];
        foreach ($blocked as $company) {
            Cache::flush();
            $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
                'company' => $company,
            ]));
            $this->assertBlocked($result, "Expected block for company: {$company}");
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // RECAPTCHA SCORE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_low_recaptcha_score_is_blocked(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'recaptcha_score' => 0.2,
        ]));

        $this->assertBlocked($result);
        $this->assertContains('low_recaptcha_score', $result['reasons']);
    }

    public function test_recaptcha_score_at_threshold_is_allowed(): void
    {
        // min is 0.5 — exactly 0.5 should pass
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'recaptcha_score' => 0.5,
        ]));

        $this->assertNotContains('low_recaptcha_score', $result['reasons']);
    }

    public function test_null_recaptcha_score_does_not_trigger_block(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'recaptcha_score' => null,
        ]));

        $this->assertNotContains('low_recaptcha_score', $result['reasons']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // VPN / PROXY
    // ──────────────────────────────────────────────────────────────────────────

    public function test_vpn_proxy_without_business_email_is_blocked(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'ip_is_proxy' => true,
            'email' => 'user@gmail.com', // free email — no high-trust signal
        ]));

        $this->assertBlocked($result);
        $this->assertContains('vpn_proxy_no_trust', $result['reasons']);
    }

    public function test_vpn_proxy_with_business_email_is_only_flagged(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'ip_is_proxy' => true,
            'email' => 'contact@acmedigital.com', // business email — partial trust
        ]));

        // Should NOT block outright — only add partial score
        $this->assertNotContains('vpn_proxy_no_trust', $result['reasons']);
        $this->assertContains('vpn_proxy_partial', $result['reasons']);
    }

    public function test_vpn_proxy_blocking_can_be_disabled_via_config(): void
    {
        config(['antispam.block_vpn_proxy' => false]);
        $this->service = new InquiryAntiSpamService();

        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'ip_is_proxy' => true,
            'email' => 'user@gmail.com',
        ]));

        $this->assertNotContains('vpn_proxy_no_trust', $result['reasons']);
        $this->assertNotContains('vpn_proxy_partial', $result['reasons']);
    }

    public function test_non_proxy_ip_does_not_trigger_vpn_rule(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'ip_is_proxy' => false,
        ]));

        $this->assertNotContains('vpn_proxy_no_trust', $result['reasons']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SUBMISSION SPEED
    // ──────────────────────────────────────────────────────────────────────────

    public function test_instant_submission_is_blocked(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'form_loaded_at' => time(), // 0 seconds ago
        ]));

        $this->assertBlocked($result);
        $this->assertContains('submit_too_fast', $result['reasons']);
    }

    public function test_submission_just_below_threshold_is_blocked(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'form_loaded_at' => time() - 2, // 2s ago — below 3s threshold
        ]));

        $this->assertContains('submit_too_fast', $result['reasons']);
    }

    public function test_submission_at_threshold_is_not_blocked_for_speed(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'form_loaded_at' => time() - 3, // exactly 3s
        ]));

        $this->assertNotContains('submit_too_fast', $result['reasons']);
    }

    public function test_null_form_loaded_at_skips_speed_check(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'form_loaded_at' => null,
        ]));

        $this->assertNotContains('submit_too_fast', $result['reasons']);
    }

    public function test_speed_check_can_be_disabled_via_config(): void
    {
        config(['antispam.min_submit_seconds' => 0]);
        $this->service = new InquiryAntiSpamService();

        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'form_loaded_at' => time(), // 0 seconds
        ]));

        $this->assertNotContains('submit_too_fast', $result['reasons']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // IP RATE LIMITING
    // ──────────────────────────────────────────────────────────────────────────

    public function test_ip_over_rate_limit_is_blocked(): void
    {
        $ip = '5.5.5.5';
        config(['antispam.rate_limit_attempts' => 3, 'antispam.rate_limit_minutes' => 10]);
        $this->service = new InquiryAntiSpamService();

        // First 3 attempts should be under the limit (counter starts at 0, blocks at >= 3)
        for ($i = 0; $i < 3; $i++) {
            Cache::flush(); // simulate fresh cache per attempt — no, we WANT to accumulate
        }

        // Re-flush and run exactly the right number of attempts
        Cache::flush();
        $this->service = new InquiryAntiSpamService();

        $ctx = $this->cleanContext();

        // Attempt 1, 2, 3: should pass (counter increments to 1, 2, 3)
        for ($i = 1; $i <= 3; $i++) {
            $result = $this->service->evaluate($ip, $ctx);
            $this->assertNotContains('ip_rate_limited', $result['reasons'], "Attempt {$i} should not be rate-limited yet");
        }

        // Attempt 4: counter is now 3, threshold is 3, so 3 >= 3 = rate limited
        $result = $this->service->evaluate($ip, $ctx);
        $this->assertContains('ip_rate_limited', $result['reasons']);
    }

    public function test_different_ips_have_independent_rate_limits(): void
    {
        config(['antispam.rate_limit_attempts' => 2, 'antispam.rate_limit_minutes' => 10]);
        $this->service = new InquiryAntiSpamService();

        $ctx = $this->cleanContext();

        // Exhaust IP A
        $this->service->evaluate('10.0.0.1', $ctx);
        $this->service->evaluate('10.0.0.1', $ctx);
        $this->service->evaluate('10.0.0.1', $ctx); // rate limited

        // IP B should still be clean
        $resultB = $this->service->evaluate('10.0.0.2', $ctx);
        $this->assertNotContains('ip_rate_limited', $resultB['reasons']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DUPLICATE MESSAGE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_duplicate_message_from_same_ip_is_flagged(): void
    {
        $ip = '9.9.9.9';
        $ctx = $this->cleanContext(['message' => 'Hello I want to know your price.']);

        // First submission — sets the fingerprint
        $first = $this->service->evaluate($ip, $ctx);
        $this->assertNotContains('duplicate_message', $first['reasons']);

        // Second submission with same message from same IP
        $second = $this->service->evaluate($ip, $ctx);
        $this->assertContains('duplicate_message', $second['reasons']);
    }

    public function test_same_message_from_different_ip_is_not_duplicate(): void
    {
        $ctx = $this->cleanContext(['message' => 'Same message content here.']);

        $this->service->evaluate('1.1.1.1', $ctx);
        $result = $this->service->evaluate('2.2.2.2', $ctx);

        $this->assertNotContains('duplicate_message', $result['reasons']);
    }

    public function test_whitespace_normalised_in_duplicate_detection(): void
    {
        $ip = '7.7.7.7';
        $msg = "Hello   I  want   to   know\nyour\tprice.";

        $this->service->evaluate($ip, $this->cleanContext(['message' => $msg]));

        // Same content, different whitespace
        $result = $this->service->evaluate($ip, $this->cleanContext([
            'message' => 'hello i want to know your price.',
        ]));

        $this->assertContains('duplicate_message', $result['reasons']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // COMBINED / REAL-WORLD SCENARIOS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * The specific spam pattern described in the issue:
     * - IP: 80.94.95.202 (blocklisted)
     * - Company: "google"
     * - VPN: true
     * - reCAPTCHA: not checked (null)
     * - Location: Budapest, Hungary
     * - Message in Greek
     */
    public function test_budapest_spam_pattern_is_blocked(): void
    {
        $result = $this->service->evaluate('80.94.95.202', [
            'company' => 'google',
            'email' => 'test@gmail.com',
            'message' => 'Γεια σου, ήθελα να μάθω την τιμή σας.',
            'ip_is_proxy' => true,
            'recaptcha_score' => null,
            'form_loaded_at' => time() - 60,
            'honeypot_value' => null,
        ]);

        $this->assertBlocked($result);
        $this->assertContains('ip_blocklisted', $result['reasons']);
        $this->assertContains('blocked_company_name', $result['reasons']);
    }

    public function test_multiple_medium_signals_accumulate_to_block(): void
    {
        // VPN + free email (6 pts) + fast submit (6 pts) = 12 pts → block
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'ip_is_proxy' => true,
            'email' => 'user@gmail.com',
            'form_loaded_at' => time() - 1,
        ]));

        $this->assertBlocked($result);
    }

    public function test_action_reflects_score_tiers(): void
    {
        // Score 0 → allow
        $r1 = $this->service->evaluate('1.2.3.4', $this->cleanContext());
        $this->assertSame('allow', $r1['action']);

        // Score 2 (vpn + business email = 2) → still allow (below flag threshold of 3)
        Cache::flush();
        $r2 = $this->service->evaluate('2.2.2.2', $this->cleanContext([
            'ip_is_proxy' => true,
            'email' => 'contact@legitimateco.com',
        ]));
        // 2 pts < 3 (flag threshold) — should be allow
        $this->assertSame('allow', $r2['action']);

        // Score 3+ → flag
        Cache::flush();
        // Rate limit just below threshold: 2 attempts, limit is default 5 — need another signal
        // Use duplicate message (5 pts) + no other block signal to get exactly flag
        // Actually easiest: configure a low flag threshold scenario — or just test flag directly
        // Flag = score >= 3 and < 6. One 'vpn_proxy_no_trust' signal = 5 pts → should block not flag
        // Use: duplicate message only = 5 pts → block (>= 6 is block, 5 is flag actually)
        // SCORE_FLAG = 3, SCORE_BLOCK = 6
        // duplicate_message = 5 pts → flag (>= 3, < 6)
        $ip3 = '3.3.3.3';
        $ctx3 = $this->cleanContext(['message' => 'Unique test message for flag scenario ' . uniqid()]);
        $this->service->evaluate($ip3, $ctx3); // first submission seeds cache
        $r3 = $this->service->evaluate($ip3, $ctx3); // second = duplicate_message (5 pts)
        $this->assertSame('flag', $r3['action']);
        $this->assertTrue($r3['allowed']); // flagged = allowed=true
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DB PERSISTENT BLOCKLIST
    // ──────────────────────────────────────────────────────────────────────────

    public function test_db_blocklisted_ip_is_blocked(): void
    {
        // Simulate a DB-blocked IP by adding to config blocklist (DB not available in unit tests)
        // Real DB integration is covered in feature tests; here we verify the scoring path
        config(['antispam.block_ips' => ['192.168.99.99']]);

        $result = $this->service->evaluate('192.168.99.99', $this->cleanContext());

        $this->assertBlocked($result);
        $this->assertContains('ip_blocklisted', $result['reasons']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CLOUDFLARE TURNSTILE SCORING
    // ──────────────────────────────────────────────────────────────────────────

    public function test_invalid_turnstile_token_adds_8_pts_and_blocks(): void
    {
        // 8 pts from turnstile_invalid alone exceeds SCORE_BLOCK (6)
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'turnstile_valid' => false,
            'turnstile_missing' => false,
        ]));

        $this->assertBlocked($result);
        $this->assertContains('turnstile_invalid', $result['reasons']);
        $this->assertSame(8, $result['risk_score']);
    }

    public function test_missing_turnstile_token_adds_2_pts_only(): void
    {
        // 2 pts alone is below SCORE_FLAG (3) — should still allow
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'turnstile_valid' => null,
            'turnstile_missing' => true,
        ]));

        $this->assertAllowed($result);
        $this->assertSame('allow', $result['action']);
        $this->assertContains('turnstile_missing', $result['reasons']);
        $this->assertSame(2, $result['risk_score']);
    }

    public function test_missing_turnstile_plus_vpn_accumulates_to_block(): void
    {
        // 2 pts (missing) + 6 pts (vpn_proxy_no_trust) = 8 pts → block
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'turnstile_valid' => null,
            'turnstile_missing' => true,
            'ip_is_proxy' => true,
            'email' => 'user@gmail.com',
        ]));

        $this->assertBlocked($result);
        $this->assertContains('turnstile_missing', $result['reasons']);
        $this->assertContains('vpn_proxy_no_trust', $result['reasons']);
    }

    public function test_valid_turnstile_does_not_add_any_risk_points(): void
    {
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'turnstile_valid' => true,
            'turnstile_missing' => false,
        ]));

        $this->assertAllowed($result);
        $this->assertSame('allow', $result['action']);
        $this->assertEmpty(array_filter($result['reasons'], fn($r) => str_starts_with($r, 'turnstile_')));
    }

    public function test_null_turnstile_valid_without_missing_flag_does_not_trigger(): void
    {
        // turnstile_valid=null and turnstile_missing=false (Turnstile not configured/run)
        $result = $this->service->evaluate('1.2.3.4', $this->cleanContext([
            'turnstile_valid' => null,
            'turnstile_missing' => false,
        ]));

        $this->assertAllowed($result);
        $this->assertNotContains('turnstile_missing', $result['reasons']);
        $this->assertNotContains('turnstile_invalid', $result['reasons']);
    }
}
