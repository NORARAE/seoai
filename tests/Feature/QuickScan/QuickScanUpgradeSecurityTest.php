<?php

namespace Tests\Feature\QuickScan;

use App\Models\QuickScan;
use App\Support\QuickScanReportToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuickScanUpgradeSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_mint_report_token_via_upgrade_route_without_ownership_proof(): void
    {
        $scan = QuickScan::create([
            'email' => 'owner@example.com',
            'url' => 'https://example.com',
            'domain' => 'example.com',
            'paid' => true,
            'status' => QuickScan::STATUS_SCANNED,
            'stripe_session_id' => 'cs_owner_123',
            'upgrade_plan' => 'diagnostic',
            'upgrade_status' => 'paid',
        ]);

        $response = $this->get(route('quick-scan.upgrade', [
            'plan' => 'diagnostic',
            'scan_id' => $scan->id,
        ]));

        $response
            ->assertRedirect(route('quick-scan.show'))
            ->assertSessionHasErrors('error');
    }

    public function test_guest_with_valid_session_can_access_already_unlocked_upgrade_redirect(): void
    {
        $scan = QuickScan::create([
            'email' => 'owner@example.com',
            'url' => 'https://example.com',
            'domain' => 'example.com',
            'paid' => true,
            'status' => QuickScan::STATUS_SCANNED,
            'stripe_session_id' => 'cs_owner_456',
            'upgrade_plan' => 'diagnostic',
            'upgrade_status' => 'paid',
        ]);

        $response = $this->get(route('quick-scan.upgrade', [
            'plan' => 'diagnostic',
            'scan_id' => $scan->id,
            'sid' => 'cs_owner_456',
        ]));

        $location = $response->headers->get('Location', '');

        $this->assertNotSame('', $location);
        $this->assertStringContainsString('/report/' . $scan->id, $location);

        parse_str(parse_url($location, PHP_URL_QUERY) ?? '', $query);

        $this->assertArrayHasKey('token', $query);
        $this->assertTrue(QuickScanReportToken::isValid((string) $query['token'], $scan));
    }

    public function test_guest_with_expired_session_cannot_use_upgrade_route(): void
    {
        $scan = QuickScan::create([
            'email' => 'owner@example.com',
            'url' => 'https://example.com',
            'domain' => 'example.com',
            'paid' => true,
            'status' => QuickScan::STATUS_SCANNED,
            'stripe_session_id' => 'cs_owner_old',
            'upgrade_plan' => 'diagnostic',
            'upgrade_status' => 'paid',
        ]);

        $scan->forceFill([
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ])->saveQuietly();

        $response = $this->get(route('quick-scan.upgrade', [
            'plan' => 'diagnostic',
            'scan_id' => $scan->id,
            'sid' => 'cs_owner_old',
        ]));

        $response
            ->assertRedirect(route('quick-scan.show'))
            ->assertSessionHasErrors('error');
    }
}
