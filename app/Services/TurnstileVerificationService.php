<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * TurnstileVerificationService
 *
 * Verifies Cloudflare Turnstile tokens server-side against the Siteverify API.
 *
 * Fail-open contract: any exception or network failure returns {valid: true} to
 * prevent legitimate users from being blocked by a transient Cloudflare outage.
 * The token's absence or invalidity is handled via risk score escalation in
 * InquiryAntiSpamService, not as a hard gate.
 *
 * Test keys (use in testing/staging, always pass verification):
 *   Site key:   1x00000000000000000000AA
 *   Secret key: 1x0000000000000000000000000000000AA
 *
 * Test keys that always fail:
 *   Site key:   2x00000000000000000000AB
 *   Secret key: 2x0000000000000000000000000000000AB
 */
class TurnstileVerificationService
{
    private const SITEVERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * Verify a Turnstile token.
     *
     * @param  string  $token   The cf-turnstile-response token from the form.
     * @param  string  $ip      The submitter's IP (passed as remoteip to Cloudflare).
     * @return array {valid: bool, reason: ?string}
     *   reason values: null | 'turnstile_disabled' | 'turnstile_missing' | 'turnstile_invalid' | 'turnstile_error'
     */
    public function verify(string $token, string $ip): array
    {
        // Bypass Turnstile entirely in the test environment
        if (app()->environment('testing')) {
            return ['valid' => true, 'reason' => 'turnstile_disabled'];
        }

        // If Turnstile is disabled globally, treat as valid (pass-through)
        if (!config('services.turnstile.enabled', true)) {
            return ['valid' => true, 'reason' => 'turnstile_disabled'];
        }

        $secret = (string) config('services.turnstile.secret_key', '');

        // If no secret key is configured, skip silently (local dev with no keys)
        if ($secret === '') {
            return ['valid' => true, 'reason' => 'turnstile_disabled'];
        }

        // Token is missing from the form submission
        if (trim($token) === '') {
            return ['valid' => false, 'reason' => 'turnstile_missing'];
        }

        try {
            $response = Http::asForm()->timeout(5)->post(self::SITEVERIFY_URL, [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            if (!$response->successful()) {
                Log::warning('TurnstileVerification: HTTP error from Cloudflare (fail-open)', [
                    'status' => $response->status(),
                    'ip' => $ip,
                ]);
                return ['valid' => true, 'reason' => 'turnstile_error'];
            }

            $data = $response->json();

            if ($data['success'] ?? false) {
                return ['valid' => true, 'reason' => null];
            }

            $errorCodes = $data['error-codes'] ?? [];
            Log::info('TurnstileVerification: token failed', [
                'errors' => $errorCodes,
                'ip' => $ip,
            ]);

            return ['valid' => false, 'reason' => 'turnstile_invalid'];
        } catch (\Throwable $e) {
            // Network failure — fail-open to avoid blocking legitimate users
            Log::warning('TurnstileVerification: request exception (fail-open)', [
                'error' => $e->getMessage(),
                'ip' => $ip,
            ]);
            return ['valid' => true, 'reason' => 'turnstile_error'];
        }
    }
}
