<?php

namespace Tests\Unit\Services;

use App\Enums\SystemTier;
use App\Models\QuickScan;
use App\Models\User;
use App\Services\Entitlements\EntitlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntitlementServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_issues_entitlements_from_scan_rank(): void
    {
        $user = User::factory()->create();

        $scan = QuickScan::create([
            'email' => $user->email,
            'url' => 'https://example.com',
            'user_id' => $user->id,
            'paid' => true,
            'status' => QuickScan::STATUS_SCANNED,
            'upgrade_plan' => 'fix-strategy',
            'upgrade_status' => 'paid',
        ]);

        app(EntitlementService::class)->issueForScan($scan);

        $user->refresh();

        $this->assertTrue($user->hasAccessTo('scan'));
        $this->assertTrue($user->hasAccessTo('signal'));
        $this->assertTrue($user->hasAccessTo('leverage'));
        $this->assertFalse($user->hasAccessTo('activation'));
    }

    public function test_issues_entitlements_from_user_tier(): void
    {
        $user = User::factory()->create([
            'system_tier' => SystemTier::SYSTEM_ACTIVATION,
        ]);

        app(EntitlementService::class)->issueForUserTier($user);

        $user->refresh();

        $this->assertTrue($user->hasAccessTo('scan'));
        $this->assertTrue($user->hasAccessTo('signal'));
        $this->assertTrue($user->hasAccessTo('leverage'));
        $this->assertTrue($user->hasAccessTo('activation'));
    }

    /** Regression: system_tier stored as plain string must not 500. */
    public function test_issueForUserTier_does_not_throw_when_system_tier_is_string(): void
    {
        $user = User::factory()->create();
        // Force raw string — simulates DB row with no enum cast on User model
        $user->setRawAttributes(array_merge($user->getAttributes(), ['system_tier' => 'signal-expansion']));

        // Must not throw "Attempt to read property 'value' on string"
        app(EntitlementService::class)->issueForUserTier($user);

        $user->refresh();

        $this->assertTrue($user->hasAccessTo('scan'));
        $this->assertTrue($user->hasAccessTo('signal'));
        $this->assertFalse($user->hasAccessTo('leverage'));
    }

    /** Regression: null system_tier must not throw. */
    public function test_issueForUserTier_does_not_throw_when_system_tier_is_null(): void
    {
        $user = User::factory()->create(['system_tier' => null]);

        app(EntitlementService::class)->issueForUserTier($user);

        // No entitlements issued — rank is 0 — but no exception thrown
        $this->assertFalse($user->fresh()->hasAccessTo('scan'));
    }

    /** Regression: accessMap() must not throw when user has no entitlements. */
    public function test_accessMap_returns_zero_rank_for_user_with_no_entitlements(): void
    {
        $user = User::factory()->create(['system_tier' => null]);

        $map = app(EntitlementService::class)->accessMap($user);

        $this->assertSame(0, $map['rank']);
        $this->assertFalse($map['scan']);
        $this->assertFalse($map['signal']);
        $this->assertFalse($map['leverage']);
        $this->assertFalse($map['activation']);
    }

    /** Regression: accessMap() must not throw when system_tier is a plain string. */
    public function test_accessMap_handles_string_system_tier(): void
    {
        $user = User::factory()->create();
        $user->setRawAttributes(array_merge($user->getAttributes(), ['system_tier' => 'structural-leverage']));

        $map = app(EntitlementService::class)->accessMap($user);

        $this->assertSame(3, $map['rank']);
        $this->assertTrue($map['leverage']);
        $this->assertFalse($map['activation']);
    }

    /** Regression: system_tier_upgraded_at as raw string must not throw toIso8601String() fatal. */
    public function test_issueForUserTier_does_not_throw_when_upgraded_at_is_string(): void
    {
        $user = User::factory()->create();
        $user->setRawAttributes(array_merge($user->getAttributes(), [
            'system_tier' => 'signal-expansion',
            'system_tier_upgraded_at' => '2026-01-15 10:30:00',
        ]));

        // Must not throw "Call to a member function toIso8601String() on string"
        app(EntitlementService::class)->issueForUserTier($user);

        $this->assertTrue($user->fresh()->hasAccessTo('scan'));
    }

    /** Regression: system_tier_upgraded_at as null must not throw. */
    public function test_issueForUserTier_does_not_throw_when_upgraded_at_is_null(): void
    {
        $user = User::factory()->create();
        $user->setRawAttributes(array_merge($user->getAttributes(), [
            'system_tier' => 'scan-basic',
            'system_tier_upgraded_at' => null,
        ]));

        app(EntitlementService::class)->issueForUserTier($user);

        $this->assertTrue($user->fresh()->hasAccessTo('scan'));
    }
}
