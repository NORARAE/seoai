<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Role-based access QA tests.
 *
 * Verifies post-login destinations, dashboard access, admin panel access,
 * and nav affordances for all user roles.
 *
 * Access model:
 *  - customer/member:  customer dashboard only
 *  - admin:            customer dashboard + admin panel
 *  - superadmin:       customer dashboard + admin panel
 *  - frontend_dev:     customer dashboard + admin panel (restricted)
 *  - customer (unapproved): pending-approval page
 */
class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function makeUser(string $role, array $extra = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'approved' => true,
            'onboarding_completed_at' => now(),
            'password' => Hash::make('Password123!'),
        ], $extra));
    }

    private function postLogin(User $user): \Illuminate\Testing\TestResponse
    {
        // Seed the CSRF token in session so the middleware accepts it
        return $this->withSession(['_token' => 'test-tok'])
            ->post(route('login'), [
                '_token' => 'test-tok',
                'email' => $user->email,
                'password' => 'Password123!',
            ]);
    }

    // ─── 1. Post-login destinations ─────────────────────────────────────────

    public function test_customer_login_lands_on_customer_dashboard(): void
    {
        $user = $this->makeUser('member');

        $response = $this->postLogin($user);

        $response->assertRedirect('/dashboard');
    }

    public function test_admin_login_lands_on_admin_panel(): void
    {
        $user = $this->makeUser('admin');

        $response = $this->postLogin($user);

        $response->assertRedirect('/admin');
    }

    public function test_superadmin_login_lands_on_admin_panel(): void
    {
        $user = $this->makeUser('super_admin');

        $response = $this->postLogin($user);

        $response->assertRedirect('/admin');
    }

    public function test_unapproved_customer_login_lands_on_pending_approval(): void
    {
        $user = $this->makeUser('member', ['approved' => false, 'onboarding_completed_at' => null]);

        $response = $this->postLogin($user);

        $response->assertRedirect(route('pending-approval'));
    }

    // ─── 2. Customer dashboard access ───────────────────────────────────────

    public function test_customer_can_access_customer_dashboard(): void
    {
        $user = $this->makeUser('member');

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_admin_can_access_customer_dashboard(): void
    {
        $user = $this->makeUser('admin');

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_superadmin_can_access_customer_dashboard(): void
    {
        $user = $this->makeUser('super_admin');

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    // ─── 3. Admin panel access ───────────────────────────────────────────────

    /**
     * Filament redirects unauthenticated/unauthorized requests to its own login.
     * We verify the admin panel's own middleware never sends customers to a 200 there.
     * A redirect (to Filament login) is the correct behavior for unauthorized access.
     */
    public function test_customer_cannot_access_admin_panel(): void
    {
        $user = $this->makeUser('member');

        // Filament rejects unauthorized users with a 403 (not a redirect)
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_access_admin_panel_redirect_resolves(): void
    {
        $user = $this->makeUser('admin');

        // canAccessPanel() returns true; Filament completes the request (200 or its own redirect)
        $statusCode = $this->actingAs($user)->get('/admin')->getStatusCode();

        // Must not be a 403 — either 200 (logged in) or an internal Filament redirect is fine
        $this->assertNotEquals(403, $statusCode, 'Admin must not be forbidden from admin panel');
    }

    public function test_superadmin_can_access_admin_panel_redirect_resolves(): void
    {
        $user = $this->makeUser('super_admin');

        $statusCode = $this->actingAs($user)->get('/admin')->getStatusCode();

        $this->assertNotEquals(403, $statusCode, 'SuperAdmin must not be forbidden from admin panel');
    }

    // ─── 4. Nav admin panel link visibility ────────────────────────────────

    public function test_admin_sees_admin_panel_link_in_customer_dashboard_nav(): void
    {
        $user = $this->makeUser('admin');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('/admin', false);
        $response->assertSee('Admin Panel', false);
    }

    public function test_superadmin_sees_admin_panel_link_in_customer_dashboard_nav(): void
    {
        $user = $this->makeUser('super_admin');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('/admin', false);
        $response->assertSee('Admin Panel', false);
    }

    public function test_customer_does_not_see_admin_panel_link(): void
    {
        $user = $this->makeUser('member');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertDontSee('Admin Panel', false);
    }

    // ─── 5. Intended redirect still works ───────────────────────────────────

    public function test_customer_intended_redirect_preserved_after_login(): void
    {
        $user = $this->makeUser('member');

        // Pre-seed the intended URL for a non-admin user
        $this->withSession([
            '_token' => 'test-tok',
            'url.intended' => '/dashboard/scans',
        ])->post(route('login'), [
                    '_token' => 'test-tok',
                    'email' => $user->email,
                    'password' => 'Password123!',
                ])->assertRedirect('/dashboard/scans');
    }

    public function test_admin_intended_redirect_preserved_after_login(): void
    {
        $user = $this->makeUser('admin');

        $this->withSession([
            '_token' => 'test-tok',
            'url.intended' => '/admin/users',
        ])->post(route('login'), [
                    '_token' => 'test-tok',
                    'email' => $user->email,
                    'password' => 'Password123!',
                ])->assertRedirect('/admin/users');
    }

    // ─── 6. Privileged staff roles — approval gate bypass ───────────────────
    //
    // owner and account_manager are isPrivilegedStaff() but not tested above.
    // They should: bypass approval gate, land on /admin on login, access /dashboard.

    public function test_owner_login_lands_on_admin_panel(): void
    {
        $user = $this->makeUser('owner');

        $this->postLogin($user)->assertRedirect('/admin');
    }

    public function test_account_manager_login_lands_on_admin_panel(): void
    {
        $user = $this->makeUser('account_manager');

        $this->postLogin($user)->assertRedirect('/admin');
    }

    public function test_owner_can_access_customer_dashboard(): void
    {
        $user = $this->makeUser('owner');

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_account_manager_can_access_customer_dashboard(): void
    {
        $user = $this->makeUser('account_manager');

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_unapproved_owner_bypasses_approval_gate(): void
    {
        $user = $this->makeUser('owner', ['approved' => false, 'onboarding_completed_at' => null]);

        // Privileged staff bypass the approval gate entirely
        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_unapproved_account_manager_bypasses_approval_gate(): void
    {
        $user = $this->makeUser('account_manager', ['approved' => false, 'onboarding_completed_at' => null]);

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    // ─── 7. frontend_dev approval-gate bypass ───────────────────────────────
    //
    // frontend_dev is not isPrivilegedStaff() but must still bypass EnsureUserIsApproved
    // and EnsureOnboardingComplete so they can reach /dashboard without being approved.

    public function test_frontend_dev_can_access_customer_dashboard_when_unapproved(): void
    {
        $user = $this->makeUser('frontend_dev', ['approved' => false, 'onboarding_completed_at' => null]);

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_frontend_dev_sees_admin_panel_link_in_dashboard(): void
    {
        $user = $this->makeUser('frontend_dev', ['approved' => false, 'onboarding_completed_at' => null]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('/admin', false);
    }

    // ─── 8. /pending-approval smart redirect ────────────────────────────────
    //
    // Already-approved or privileged users who hit /pending-approval should be
    // redirected away — they must never see the "Account Under Review" page.

    public function test_approved_customer_visiting_pending_approval_redirected_to_dashboard(): void
    {
        $user = $this->makeUser('member');

        $this->actingAs($user)->get('/pending-approval')->assertRedirect('/dashboard');
    }

    public function test_admin_visiting_pending_approval_redirected_to_admin(): void
    {
        $user = $this->makeUser('admin');

        $this->actingAs($user)->get('/pending-approval')->assertRedirect('/admin');
    }

    public function test_unapproved_customer_sees_pending_approval_page(): void
    {
        $user = $this->makeUser('member', ['approved' => false, 'onboarding_completed_at' => null]);

        $this->actingAs($user)->get('/pending-approval')->assertOk();
    }

    // ─── 9. buyer role — regular customer flow ──────────────────────────────
    //
    // buyer is isAdmin() = true but isPrivilegedStaff() = false.
    // Buyers are customers; they need approved = true and follow the normal flow.

    public function test_approved_buyer_can_access_dashboard(): void
    {
        $user = $this->makeUser('buyer');

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_unapproved_buyer_is_blocked_by_approval_gate(): void
    {
        $user = $this->makeUser('buyer', ['approved' => false, 'onboarding_completed_at' => null]);

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('pending-approval'));
    }

    public function test_buyer_login_lands_on_dashboard(): void
    {
        $user = $this->makeUser('buyer');

        $this->postLogin($user)->assertRedirect('/dashboard');
    }
}
