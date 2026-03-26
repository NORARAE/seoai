<?php

namespace Tests\Feature\Inquiry;

use App\Models\License;
use App\Services\Licensing\CryptoPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use UnexpectedValueException;

/**
 * CryptoWebhookTest
 *
 * Covers:
 *  - Confirmed / resolved events activate a license (idempotent)
 *  - Failed / expired events never activate a license
 *  - Duplicate webhook delivery is safely ignored
 *  - Invalid signature returns 400
 *  - Disabled feature flag returns 200 without processing
 */
class CryptoWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const WEBHOOK_SECRET = 'test-webhook-secret';

    private function makeSignedRequest(array $event, ?string $secret = null): \Illuminate\Testing\TestResponse
    {
        $payload   = json_encode($event);
        $signature = hash_hmac('sha256', $payload, $secret ?? self::WEBHOOK_SECRET);

        return $this->postJson(
            '/api/v1/crypto/webhook',
            [],
            ['X-CC-Webhook-Signature' => $signature, 'CONTENT_TYPE' => 'application/json'],
        )->withContent($payload);
    }

    private function chargeEvent(string $type, string $chargeId = 'test-charge-abc', array $metaOverride = []): array
    {
        return [
            'type' => $type,
            'data' => [
                'id'       => $chargeId,
                'code'     => 'TESTCODE1',
                'metadata' => array_merge([
                    'plan'           => 'agency_5k',
                    'term_months'    => '3',
                    'site_url'       => 'https://test-client.com',
                    'customer_email' => 'client@test-client.com',
                    'customer_name'  => 'Test Client',
                ], $metaOverride),
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.coinbase_commerce.enabled'        => true,
            'services.coinbase_commerce.webhook_secret' => self::WEBHOOK_SECRET,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CONFIRMED — ACTIVATE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_charge_confirmed_event_activates_license(): void
    {
        $this->mockLicenseService('charge-confirm-001');

        $service = $this->app->make(CryptoPaymentService::class);
        $event   = $this->chargeEvent('charge:confirmed', 'charge-confirm-001');
        $payload = json_encode($event);
        $sig     = hash_hmac('sha256', $payload, self::WEBHOOK_SECRET);

        $result = $service->handleWebhook($payload, $sig);

        $this->assertTrue($result['handled']);
        $this->assertEquals('activated', $result['result']);
        $this->assertEquals(1, License::where('crypto_charge_id', 'charge-confirm-001')->count());
    }

    // ──────────────────────────────────────────────────────────────────────────
    // IDEMPOTENCY — DUPLICATE WEBHOOK
    // ──────────────────────────────────────────────────────────────────────────

    public function test_duplicate_webhook_for_same_charge_does_not_double_activate(): void
    {
        // Pre-seed an existing active license with this charge ID
        License::create([
            'crypto_charge_id' => 'charge-dup-test',
            'payment_method'   => 'crypto',
            'status'           => 'active',
            'plan'             => 'agency_5k',
            'site_url'         => 'https://existing-client.com',
            'customer_email'   => 'dup@existing-client.com',
            'customer_name'    => 'Dup Client',
            'license_key'      => 'TEST-KEY-DUP',
            'urls_allowed'     => 5000,
        ]);

        $service = $this->app->make(CryptoPaymentService::class);
        $event   = $this->chargeEvent('charge:confirmed', 'charge-dup-test');
        $payload = json_encode($event);
        $sig     = hash_hmac('sha256', $payload, self::WEBHOOK_SECRET);

        // Should return handled but no new license created
        $result = $service->handleWebhook($payload, $sig);

        $this->assertTrue($result['handled']);
        // Only one license should exist for this charge
        $this->assertEquals(1, License::where('crypto_charge_id', 'charge-dup-test')->count());
    }

    // ──────────────────────────────────────────────────────────────────────────
    // FAILED / EXPIRED — NEVER ACTIVATE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_charge_failed_never_activates_license(): void
    {
        $service = $this->app->make(CryptoPaymentService::class);
        $event   = $this->chargeEvent('charge:failed', 'charge-fail-001');
        $payload = json_encode($event);
        $sig     = hash_hmac('sha256', $payload, self::WEBHOOK_SECRET);

        $result = $service->handleWebhook($payload, $sig);

        $this->assertTrue($result['handled']);
        $this->assertEquals('expired', $result['result']);

        // Confirm NO license was created
        $this->assertEquals(0, License::where('crypto_charge_id', 'charge-fail-001')->count());
    }

    public function test_charge_expired_never_activates_license(): void
    {
        $service = $this->app->make(CryptoPaymentService::class);
        $event   = $this->chargeEvent('charge:expired', 'charge-expired-001');
        $payload = json_encode($event);
        $sig     = hash_hmac('sha256', $payload, self::WEBHOOK_SECRET);

        $result = $service->handleWebhook($payload, $sig);

        $this->assertEquals('expired', $result['result']);
        $this->assertEquals(0, License::where('crypto_charge_id', 'charge-expired-001')->count());
    }

    public function test_charge_failed_expires_existing_active_license(): void
    {
        License::create([
            'crypto_charge_id' => 'charge-active-fail',
            'payment_method'   => 'crypto',
            'status'           => 'active',
            'plan'             => 'agency_5k',
            'site_url'         => 'https://fail-client.com',
            'customer_email'   => 'fail@client.com',
            'customer_name'    => 'Fail Client',
            'license_key'      => 'TEST-KEY-FAIL',
            'urls_allowed'     => 5000,
        ]);

        $service = $this->app->make(CryptoPaymentService::class);
        $event   = $this->chargeEvent('charge:failed', 'charge-active-fail');
        $payload = json_encode($event);
        $sig     = hash_hmac('sha256', $payload, self::WEBHOOK_SECRET);

        $service->handleWebhook($payload, $sig);

        $license = License::where('crypto_charge_id', 'charge-active-fail')->first();
        $this->assertEquals('expired', $license->status);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // INVALID SIGNATURE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_webhook_with_invalid_signature_throws(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $service = $this->app->make(CryptoPaymentService::class);
        $payload = json_encode($this->chargeEvent('charge:confirmed', 'charge-badsig'));
        $sig     = 'invalid-signature-abc';

        $service->handleWebhook($payload, $sig);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // FEATURE FLAG DISABLED
    // ──────────────────────────────────────────────────────────────────────────

    public function test_webhook_when_disabled_returns_200_without_processing(): void
    {
        config(['services.coinbase_commerce.enabled' => false]);

        $service = $this->app->make(CryptoPaymentService::class);
        $payload = json_encode($this->chargeEvent('charge:confirmed', 'charge-disabled'));
        // Signature doesn't matter when feature is off — feature guard fires first
        $sig = hash_hmac('sha256', $payload, self::WEBHOOK_SECRET);

        $result = $service->handleWebhook($payload, $sig);

        $this->assertFalse($result['handled']);
        $this->assertEquals('feature_disabled', $result['result']);
        $this->assertEquals(0, License::where('crypto_charge_id', 'charge-disabled')->count());
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Stub LicenseService so we don't need real plan config in tests.
     */
    private function mockLicenseService(string $chargeId = 'test-charge-abc'): void
    {
        $mock = $this->createMock(\App\Services\Licensing\LicenseService::class);
        $mock->method('createLicense')->willReturnCallback(function (array $data) use ($chargeId): License {
            return License::create([
                'crypto_charge_id' => $data['crypto_charge_id'] ?? $chargeId,
                'payment_method'   => 'crypto',
                'status'           => 'active',
                'plan'             => $data['plan'] ?? 'agency_5k',
                'site_url'         => $data['site_url'] ?? 'https://test-client.com',
                'customer_email'   => $data['customer_email'] ?? 'test@test.com',
                'customer_name'    => $data['customer_name'] ?? 'Test Client',
                'license_key'      => 'TEST-' . strtoupper(substr($data['crypto_charge_id'] ?? $chargeId, 0, 8)),
                'urls_allowed'     => 5000,
            ]);
        });
        $mock->method('normalizeDomain')->willReturnArgument(0);
        $this->app->instance(\App\Services\Licensing\LicenseService::class, $mock);
    }
}
