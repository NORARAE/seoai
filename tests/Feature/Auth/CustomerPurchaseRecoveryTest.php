<?php

namespace Tests\Feature\Auth;

use App\Models\QuickScan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class CustomerPurchaseRecoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_register_auto_attaches_paid_scan_and_redirects_to_dashboard(): void
    {
        $testIp = '10.0.0.55';
        RateLimiter::clear('register-ip:' . sha1($testIp));

        $scan = QuickScan::create([
            'email' => 'buyer@example.com',
            'url' => 'https://example.com',
            'stripe_session_id' => 'cs_register_recovery',
            'paid' => true,
            'status' => QuickScan::STATUS_PAID,
        ]);

        $this->get(route('register'));

        $response = $this->withServerVariables(['REMOTE_ADDR' => $testIp])->post(route('register.store'), [
            '_token' => csrf_token(),
            'name' => 'Buyer',
            'email' => 'buyer@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
            'use_case' => 'recover-report',
        ]);

        $user = User::where('email', 'buyer@example.com')->firstOrFail();
        $scan->refresh();

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('scan_saved', 'Your paid report is now linked to your account.');
        $this->assertAuthenticatedAs($user);
        $this->assertSame($user->id, $scan->user_id);
        $this->assertTrue((bool) $user->approved);
        $this->assertNotNull($user->onboarding_completed_at);
    }

    public function test_login_redirect_query_returns_user_to_dashboard_report(): void
    {
        $user = User::factory()->create([
            'email' => 'returning@example.com',
            'password' => Hash::make('CorrectPassword123!'),
            'approved' => true,
            'onboarding_completed_at' => now(),
        ]);

        $scan = QuickScan::create([
            'email' => 'returning@example.com',
            'url' => 'https://example.com',
            'user_id' => $user->id,
            'stripe_session_id' => 'cs_login_return',
            'paid' => true,
            'status' => QuickScan::STATUS_SCANNED,
            'score' => 72,
            'issues' => ['Missing answer block'],
            'strengths' => ['Schema detected'],
        ]);

        $redirectTarget = route('dashboard.scans.show', ['scan' => $scan->publicScanId()], false);

        $this->get(route('login', ['redirect' => $redirectTarget]));

        $response = $this->post(route('login'), [
            '_token' => csrf_token(),
            'email' => 'returning@example.com',
            'password' => 'CorrectPassword123!',
        ]);

        $response->assertRedirect(route('dashboard.scans.show', ['scan' => $scan->publicScanId()]));
        $response->assertSessionHas('scan_saved', 'Your previous scans are ready — view them in your dashboard.');
        $this->assertAuthenticatedAs($user);
    }

    public function test_logged_out_dashboard_report_access_returns_to_report_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'middleware@example.com',
            'password' => Hash::make('CorrectPassword123!'),
            'approved' => true,
            'onboarding_completed_at' => now(),
        ]);

        $scan = QuickScan::create([
            'email' => 'middleware@example.com',
            'url' => 'https://example.com',
            'user_id' => $user->id,
            'stripe_session_id' => 'cs_middleware_return',
            'paid' => true,
            'status' => QuickScan::STATUS_SCANNED,
            'score' => 81,
            'issues' => ['Connectivity gap'],
            'strengths' => ['Entity authority present'],
        ]);

        $reportUrl = route('dashboard.scans.show', ['scan' => $scan->publicScanId()]);

        $this->get($reportUrl)
            ->assertRedirect(route('login'));

        $response = $this->post(route('login'), [
            '_token' => csrf_token(),
            'email' => 'middleware@example.com',
            'password' => 'CorrectPassword123!',
        ]);

        $response->assertRedirect($reportUrl);
        $this->assertAuthenticatedAs($user);
    }
}