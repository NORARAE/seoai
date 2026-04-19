<?php

namespace Tests\Feature\QuickScan;

use App\Models\QuickScan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuickScanWebhookFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_activation_webhook_marks_upgrade_paid(): void
    {
        config()->set('services.stripe_quick_scan.webhook_secret', 'whsec_qs_test');

        $scan = QuickScan::create([
            'email' => 'owner@example.com',
            'url' => 'https://example.com',
            'domain' => 'example.com',
            'paid' => true,
            'status' => QuickScan::STATUS_SCANNED,
            'upgrade_plan' => 'fix-strategy',
            'upgrade_status' => 'paid',
        ]);

        // Simulate direct Full Activation checkout webhook payload.
        $event = [
            'id' => 'evt_quickscan_upgrade',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_full_activation_123',
                    'payment_status' => 'paid',
                    'metadata' => [
                        'scan_id' => (string) $scan->id,
                        'upgrade_plan' => 'optimization',
                        'tier' => 'system-activation',
                    ],
                ],
            ],
        ];

        $payload = json_encode($event, JSON_THROW_ON_ERROR);
        $signature = $this->stripeSignature($payload, 'whsec_qs_test');

        $response = $this->call(
            'POST',
            '/api/v1/quick-scan/stripe-webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk()->assertJsonFragment(['message' => 'Upgrade confirmed.']);

        $scan->refresh();
        $this->assertSame('optimization', $scan->upgrade_plan);
        $this->assertSame('paid', $scan->upgrade_status);
        $this->assertSame('cs_full_activation_123', $scan->upgrade_stripe_session_id);
        $this->assertNotNull($scan->upgraded_at);
    }

    private function stripeSignature(string $payload, string $secret): string
    {
        $timestamp = time();
        $signedPayload = $timestamp . '.' . $payload;
        $hash = hash_hmac('sha256', $signedPayload, $secret);

        return "t={$timestamp},v1={$hash}";
    }
}
