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
}
