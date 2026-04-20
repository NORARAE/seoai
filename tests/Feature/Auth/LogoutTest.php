<?php

namespace Tests\Feature\Auth;

use App\Models\QuickScan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Logout and access-recovery tests.
 *
 * Covers:
 *  1. GET /logout logs user out and redirects to /login
 *  2. POST /logout logs user out and redirects to /login
 *  3. GET /admin/logout (compat) redirects to /logout which logs out
 *  4. Unauthenticated GET /logout redirects to /login (not error)
 *  5. frontend_dev bypasses pending-approval on Filament login (AppServiceProvider)
 *  6. Paid scan user via Filament login gets auto-approved (AppServiceProvider)
 */
class LogoutTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = 'member', array $extra = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'approved' => true,
            'onboarding_completed_at' => now(),
            'password' => Hash::make('Password123!'),
        ], $extra));
    }

    // ─── 1. GET /logout ──────────────────────────────────────────────────────

    public function test_get_logout_signs_out_and_redirects_to_login(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->get('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    // ─── 2. POST /logout ─────────────────────────────────────────────────────

    public function test_post_logout_signs_out_and_redirects_to_login(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->withSession(['_token' => 'test-tok'])
            ->post('/logout', ['_token' => 'test-tok']);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    // ─── 3. GET /admin/logout compat ─────────────────────────────────────────

    public function test_get_admin_logout_redirects_to_unified_logout(): void
    {
        $user = $this->makeUser('admin');

        // Should redirect to /logout (which then redirects to /login).
        // We stop at the first redirect — just verify it isn't a 403/405.
        $response = $this->actingAs($user)->get('/admin/logout');

        $response->assertRedirect(route('logout'));
    }

    public function test_get_admin_logout_fully_signs_out_when_following_redirects(): void
    {
        $user = $this->makeUser('admin');

        // followingRedirects() renders the final page (200), so assert OK + guest
        $this->actingAs($user)->followingRedirects()->get('/admin/logout');

        $this->assertGuest();
    }

    // ─── 4. Unauthenticated logout ───────────────────────────────────────────

    public function test_unauthenticated_get_logout_redirects_to_login(): void
    {
        $response = $this->get('/logout');

        // Laravel auth middleware sends unauthenticated users to /login, not a 500
        $response->assertRedirect(route('login'));
    }

    // ─── 5. Dashboard nav contains Sign Out ──────────────────────────────────

    public function test_dashboard_nav_contains_sign_out(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Sign Out', false);
        $response->assertSee(route('logout'), false);
    }

    // ─── 6. frontend_dev bypasses pending-approval (AppServiceProvider) ──────
    //
    // The Filament LoginResponse previously sent unapproved frontend_devs to
    // pending-approval because it didn't check isFrontendDev().
    // Verified via CustomerLoginController which uses authenticatedRedirect().

    public function test_frontend_dev_login_lands_on_admin_not_pending(): void
    {
        $user = $this->makeUser('frontend_dev', ['approved' => false]);

        $response = $this->withSession(['_token' => 'tok'])
            ->post(route('login'), [
                '_token' => 'tok',
                'email' => $user->email,
                'password' => 'Password123!',
            ]);

        // CustomerLoginController sends frontend_dev to /admin, not pending-approval
        $response->assertRedirect('/admin');
    }

    // ─── 7. Paid scan user gets auto-approved on login ───────────────────────

    public function test_unapproved_user_with_paid_scan_is_auto_approved_on_login(): void
    {
        $user = $this->makeUser('member', [
            'approved' => false,
            'onboarding_completed_at' => null,
        ]);

        QuickScan::create([
            'public_scan_id' => 'test-auto-approve',
            'user_id' => $user->id,
            'email' => $user->email,
            'url' => 'https://example.com',
            'domain' => 'example.com',
            'paid' => true,
            'status' => 'complete',
        ]);

        $this->withSession(['_token' => 'tok'])
            ->post(route('login'), [
                '_token' => 'tok',
                'email' => $user->email,
                'password' => 'Password123!',
            ]);

        $this->assertTrue($user->fresh()->isApproved(), 'User should be auto-approved after login with a paid scan');
    }

    public function test_unapproved_user_with_paid_scan_redirects_to_dashboard_after_login(): void
    {
        $user = $this->makeUser('member', [
            'approved' => false,
            'onboarding_completed_at' => now(),
        ]);

        QuickScan::create([
            'public_scan_id' => 'test-redir-' . rand(1000, 9999),
            'user_id' => $user->id,
            'email' => $user->email,
            'url' => 'https://example.com',
            'domain' => 'example.com',
            'paid' => true,
            'status' => 'complete',
        ]);

        $response = $this->withSession(['_token' => 'tok'])
            ->post(route('login'), [
                '_token' => 'tok',
                'email' => $user->email,
                'password' => 'Password123!',
            ]);

        $response->assertRedirect('/dashboard');
    }

    public function test_unapproved_user_without_scan_still_lands_on_pending_approval(): void
    {
        $user = $this->makeUser('member', [
            'approved' => false,
            'onboarding_completed_at' => null,
        ]);

        $response = $this->withSession(['_token' => 'tok'])
            ->post(route('login'), [
                '_token' => 'tok',
                'email' => $user->email,
                'password' => 'Password123!',
            ]);

        $response->assertRedirect(route('pending-approval'));
    }
}
