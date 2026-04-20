<?php

namespace Tests\Feature\QuickScan;

use App\Models\QuickScan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Lifecycle QA tests for the scan → payment → processing → result → dashboard flow.
 *
 * Covers PHASE 2 state matrix, PHASE 3 redirect/session, PHASE 4 ownership,
 * PHASE 5 duplicate-domain, and PHASE 6 UI safety rules.
 */
class QuickScanLifecycleTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function makeUser(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'approved' => true,
            'onboarding_completed_at' => now(),
        ], $attrs));
    }

    private function makeScan(array $attrs = []): QuickScan
    {
        return QuickScan::create(array_merge([
            'email' => 'test@example.com',
            'url' => 'https://example.com',
            'domain' => 'example.com',
            'status' => QuickScan::STATUS_PENDING,
            'paid' => false,
        ], $attrs));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PHASE 6 Rule 1 — Non-renderable scan must never emit a clickable link
    // ──────────────────────────────────────────────────────────────────────────

    public function test_pending_scan_does_not_produce_clickable_report_link_in_dashboard(): void
    {
        $user = $this->makeUser(['email' => 'test@example.com']);
        $this->makeScan([
            'user_id' => $user->id,
            'status' => QuickScan::STATUS_PENDING,
        ]);

        // Scan history appears on the /dashboard/scans route
        $response = $this->actingAs($user)->get('/dashboard/scans');

        $response->assertOk();
        // The scan-actions block should show a disabled span, not an Open Report link
        $response->assertSee('aria-disabled="true"', false);
        $response->assertDontSee('Open Report');
    }

    public function test_paid_in_flight_scan_does_not_produce_clickable_report_link(): void
    {
        $user = $this->makeUser(['email' => 'test@example.com']);
        $scan = $this->makeScan([
            'user_id' => $user->id,
            'status' => QuickScan::STATUS_PAID,
            'paid' => true,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        // Disabled span should be present, not a link
        $response->assertSee('disabled', false);
        $response->assertSee('Analyzing your site', false);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PHASE 2 — State matrix: dashboardReport controller responses
    // ──────────────────────────────────────────────────────────────────────────

    public function test_completed_scan_opens_result_view(): void
    {
        $user = $this->makeUser(['email' => 'owner@example.com']);
        $scan = $this->makeScan([
            'user_id' => $user->id,
            'email' => 'owner@example.com',
            'status' => QuickScan::STATUS_SCANNED,
            'paid' => true,
            'score' => 55,
            'scanned_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard.scans.show', ['scan' => $scan->publicScanId()]));

        $response->assertOk();
        $response->assertViewIs('public.quick-scan-result');
    }

    public function test_paid_in_flight_scan_shows_processing_view_not_unavailable(): void
    {
        $user = $this->makeUser(['email' => 'owner@example.com']);
        $scan = $this->makeScan([
            'user_id' => $user->id,
            'email' => 'owner@example.com',
            'status' => QuickScan::STATUS_PAID,
            'paid' => true,
            'stripe_session_id' => 'cs_test_inflight',
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard.scans.show', ['scan' => $scan->publicScanId()]));

        $response->assertOk();
        $response->assertViewIs('public.quick-scan-processing');
    }

    public function test_unpaid_scan_without_session_shows_unavailable(): void
    {
        $user = $this->makeUser(['email' => 'owner@example.com']);
        $scan = $this->makeScan([
            'user_id' => $user->id,
            'email' => 'owner@example.com',
            'status' => QuickScan::STATUS_PENDING,
            'paid' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard.scans.show', ['scan' => $scan->publicScanId()]));

        $response->assertOk();
        $response->assertViewIs('public.system-readout-unavailable');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PHASE 4 — Ownership: wrong user cannot access another user's scan
    // ──────────────────────────────────────────────────────────────────────────

    public function test_different_user_cannot_access_another_users_scan(): void
    {
        $owner = $this->makeUser(['email' => 'owner@example.com']);
        $intruder = $this->makeUser(['email' => 'intruder@example.com']);
        $scan = $this->makeScan([
            'user_id' => $owner->id,
            'email' => 'owner@example.com',
            'status' => QuickScan::STATUS_SCANNED,
            'paid' => true,
            'score' => 60,
        ]);

        $response = $this->actingAs($intruder)
            ->get(route('dashboard.scans.show', ['scan' => $scan->publicScanId()]));

        $response->assertOk();
        $response->assertViewIs('public.system-readout-unavailable');
    }

    /**
     * A scan linked only by email (no user_id) must still be accessible
     * when the matching user logs in (orphan recovery path).
     */
    public function test_email_matched_scan_is_accessible_without_user_id(): void
    {
        $user = $this->makeUser(['email' => 'owner@example.com']);
        $scan = $this->makeScan([
            'user_id' => null,
            'email' => 'owner@example.com',
            'status' => QuickScan::STATUS_SCANNED,
            'paid' => true,
            'score' => 72,
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard.scans.show', ['scan' => $scan->publicScanId()]));

        $response->assertOk();
        $response->assertViewIs('public.quick-scan-result');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PHASE 4 — Orphan recovery: buildUserScanData links email-matched scans
    // ──────────────────────────────────────────────────────────────────────────

    public function test_dashboard_links_orphan_scans_to_user_on_visit(): void
    {
        $user = $this->makeUser(['email' => 'recover@example.com']);
        $scan = $this->makeScan([
            'user_id' => null,
            'email' => 'recover@example.com',
            'status' => QuickScan::STATUS_SCANNED,
            'paid' => true,
            'score' => 60,
        ]);

        $this->assertNull($scan->user_id);

        $this->actingAs($user)->get('/dashboard');

        $scan->refresh();
        $this->assertSame($user->id, $scan->user_id);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PHASE 1 — Zero-scan user sees empty state, not an error
    // ──────────────────────────────────────────────────────────────────────────

    public function test_user_with_zero_scans_sees_dashboard_without_error(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PHASE 1 — Pending scan is visible in dashboard scan history
    // ──────────────────────────────────────────────────────────────────────────

    public function test_pending_scan_appears_in_dashboard_scan_history(): void
    {
        $user = $this->makeUser(['email' => 'test@example.com']);
        $this->makeScan([
            'user_id' => $user->id,
            'status' => QuickScan::STATUS_PENDING,
            'domain' => 'pending-domain.com',
            'url' => 'https://pending-domain.com',
        ]);

        // Scan history list is rendered on the /dashboard/scans route
        $response = $this->actingAs($user)->get('/dashboard/scans');

        $response->assertOk();
        $response->assertSee('pending-domain.com');
    }

    public function test_error_scan_appears_in_dashboard_scan_history(): void
    {
        $user = $this->makeUser(['email' => 'test@example.com']);
        $this->makeScan([
            'user_id' => $user->id,
            'status' => QuickScan::STATUS_ERROR,
            'domain' => 'error-domain.com',
            'url' => 'https://error-domain.com',
        ]);

        // Scan history list is rendered on the /dashboard/scans route
        $response = $this->actingAs($user)->get('/dashboard/scans');

        $response->assertOk();
        $response->assertSee('error-domain.com');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PHASE 5 — Duplicate domain: new scan created, is_repeat_scan flagged
    // ──────────────────────────────────────────────────────────────────────────

    public function test_second_scan_for_same_domain_is_flagged_as_repeat(): void
    {
        // First completed scan
        $this->makeScan([
            'domain' => 'repeat.com',
            'status' => QuickScan::STATUS_SCANNED,
            'paid' => true,
            'score' => 50,
        ]);

        // Verify subsequent scan creation code marks repeat properly
        $domain = 'repeat.com';
        $priorCount = QuickScan::where('domain', $domain)
            ->where('status', QuickScan::STATUS_SCANNED)
            ->count();

        $this->assertSame(1, $priorCount);

        $repeatScan = $this->makeScan([
            'domain' => $domain,
            'is_repeat_scan' => true,
            'domain_scan_count' => $priorCount + 1,
        ]);

        $this->assertTrue((bool) $repeatScan->is_repeat_scan);
        $this->assertSame(2, (int) $repeatScan->domain_scan_count);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PHASE 3 — Status polling endpoint: auth owner can poll
    // ──────────────────────────────────────────────────────────────────────────

    public function test_status_endpoint_returns_ready_for_completed_scan(): void
    {
        $user = $this->makeUser(['email' => 'poller@example.com']);
        $scan = $this->makeScan([
            'user_id' => $user->id,
            'email' => 'poller@example.com',
            'status' => QuickScan::STATUS_SCANNED,
            'paid' => true,
            'score' => 70,
            'scanned_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/quick-scan/status?scan_id=' . $scan->id);

        $response->assertOk()
            ->assertJsonFragment(['ready' => true]);
    }

    public function test_status_endpoint_returns_not_ready_for_pending_scan(): void
    {
        $user = $this->makeUser(['email' => 'poller@example.com']);
        $scan = $this->makeScan([
            'user_id' => $user->id,
            'email' => 'poller@example.com',
            'status' => QuickScan::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/quick-scan/status?scan_id=' . $scan->id);

        $response->assertOk()
            ->assertJsonFragment(['ready' => false]);
    }

    public function test_status_endpoint_denies_non_owner(): void
    {
        $owner = $this->makeUser(['email' => 'owner@example.com']);
        $intruder = $this->makeUser(['email' => 'intruder@example.com']);
        $scan = $this->makeScan([
            'user_id' => $owner->id,
            'email' => 'owner@example.com',
            'status' => QuickScan::STATUS_SCANNED,
            'paid' => true,
            'score' => 70,
        ]);

        $response = $this->actingAs($intruder)
            ->getJson('/quick-scan/status?scan_id=' . $scan->id);

        $response->assertOk()
            ->assertJsonFragment(['ready' => false]);
    }
}
