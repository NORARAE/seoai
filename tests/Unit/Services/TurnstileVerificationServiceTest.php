<?php

namespace Tests\Unit\Services;

use App\Services\TurnstileVerificationService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * TurnstileVerificationServiceTest
 *
 * Tests the server-side Cloudflare Turnstile token verification.
 * All HTTP calls are faked — no network required.
 */
class TurnstileVerificationServiceTest extends TestCase
{
    private TurnstileVerificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TurnstileVerificationService();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DISABLED / NO CONFIG
    // ──────────────────────────────────────────────────────────────────────────

    public function test_returns_valid_when_turnstile_disabled(): void
    {
        config(['services.turnstile.enabled' => false]);
        config(['services.turnstile.secret_key' => 'some-secret']);

        $result = $this->service->verify('any-token', '1.2.3.4');

        $this->assertTrue($result['valid']);
        $this->assertSame('turnstile_disabled', $result['reason']);
    }

    public function test_returns_valid_when_no_secret_key_configured(): void
    {
        config(['services.turnstile.enabled' => true]);
        config(['services.turnstile.secret_key' => '']);

        $result = $this->service->verify('any-token', '1.2.3.4');

        $this->assertTrue($result['valid']);
        $this->assertSame('turnstile_disabled', $result['reason']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // MISSING TOKEN
    // ──────────────────────────────────────────────────────────────────────────

    public function test_returns_invalid_when_token_is_empty(): void
    {
        config(['services.turnstile.enabled' => true]);
        config(['services.turnstile.secret_key' => 'test-secret-key']);

        $result = $this->service->verify('', '1.2.3.4');

        $this->assertFalse($result['valid']);
        $this->assertSame('turnstile_missing', $result['reason']);
    }

    public function test_returns_invalid_when_token_is_whitespace(): void
    {
        config(['services.turnstile.enabled' => true]);
        config(['services.turnstile.secret_key' => 'test-secret-key']);

        $result = $this->service->verify('   ', '1.2.3.4');

        $this->assertFalse($result['valid']);
        $this->assertSame('turnstile_missing', $result['reason']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SUCCESSFUL VERIFICATION
    // ──────────────────────────────────────────────────────────────────────────

    public function test_returns_valid_on_cloudflare_success_response(): void
    {
        config(['services.turnstile.enabled' => true]);
        config(['services.turnstile.secret_key' => 'test-secret-key']);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'challenge_ts' => '2026-04-24T12:00:00Z',
                'hostname' => 'seoaico.com',
            ], 200),
        ]);

        $result = $this->service->verify('valid-token', '1.2.3.4');

        $this->assertTrue($result['valid']);
        $this->assertNull($result['reason']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // FAILED VERIFICATION
    // ──────────────────────────────────────────────────────────────────────────

    public function test_returns_invalid_on_cloudflare_failure_response(): void
    {
        config(['services.turnstile.enabled' => true]);
        config(['services.turnstile.secret_key' => 'test-secret-key']);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        $result = $this->service->verify('bad-token', '1.2.3.4');

        $this->assertFalse($result['valid']);
        $this->assertSame('turnstile_invalid', $result['reason']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // NETWORK FAILURES — FAIL OPEN
    // ──────────────────────────────────────────────────────────────────────────

    public function test_fail_open_on_network_exception(): void
    {
        config(['services.turnstile.enabled' => true]);
        config(['services.turnstile.secret_key' => 'test-secret-key']);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => function () {
                throw new \Exception('Connection refused');
            },
        ]);

        $result = $this->service->verify('some-token', '1.2.3.4');

        // Fail-open: a network error should not block legitimate users
        $this->assertTrue($result['valid']);
        $this->assertSame('turnstile_error', $result['reason']);
    }

    public function test_fail_open_on_http_server_error(): void
    {
        config(['services.turnstile.enabled' => true]);
        config(['services.turnstile.secret_key' => 'test-secret-key']);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([], 503),
        ]);

        $result = $this->service->verify('some-token', '1.2.3.4');

        $this->assertTrue($result['valid']);
        $this->assertSame('turnstile_error', $result['reason']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CORRECT PARAMETERS ARE SENT
    // ──────────────────────────────────────────────────────────────────────────

    public function test_sends_correct_parameters_to_cloudflare(): void
    {
        config(['services.turnstile.enabled' => true]);
        config(['services.turnstile.secret_key' => 'my-secret']);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
            ], 200),
        ]);

        $this->service->verify('my-token', '5.6.7.8');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
                && $request['secret'] === 'my-secret'
                && $request['response'] === 'my-token'
                && $request['remoteip'] === '5.6.7.8';
        });
    }
}
