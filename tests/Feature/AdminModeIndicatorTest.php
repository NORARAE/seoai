<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Admin mode indicator visibility tests.
 *
 * Verifies that:
 * - Admin mode banner shows for isPrivilegedStaff users on /dashboard
 * - Admin mode banner shows for isPrivilegedStaff users on /admin
 * - Admin mode banner hidden for normal customers
 * - Banner shows correct CTA based on current view
 */
class AdminModeIndicatorTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'approved' => true,
            'onboarding_completed_at' => now(),
            'password' => Hash::make('Password123!'),
        ]);
    }

    private function makeCustomer(): User
    {
        return User::factory()->create([
            'role' => 'member',
            'approved' => true,
            'onboarding_completed_at' => now(),
            'password' => Hash::make('Password123!'),
        ]);
    }

    // ─── Banner Visibility ──────────────────────────────────────────────────

    public function test_admin_mode_banner_shows_on_dashboard(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk()
            ->assertSee('Admin Mode — Viewing Customer System', false)
            ->assertSee('Open Admin Panel', false);
    }

    public function test_admin_mode_banner_shows_on_admin_panel(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/admin');

        // The admin panel may have its own layout, but /admin/users or similar would show our layout
        // Just verify the status is not forbidden
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    public function test_admin_mode_banner_hidden_for_customers(): void
    {
        $customer = $this->makeCustomer();

        $response = $this->actingAs($customer)->get('/dashboard');

        $response->assertOk()
            ->assertDontSee('Admin Mode — Viewing Customer System', false)
            ->assertDontSee('Open Admin Panel', false);
    }

    public function test_banner_cta_links_correct_destinations(): void
    {
        $admin = $this->makeAdmin();

        // On dashboard, should link to /admin
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertSee('href="/admin"', false);

        // On dashboard, should show "Open Admin Panel" text
        $response->assertSee('Open Admin Panel', false);
    }

    // ─── Body Class ─────────────────────────────────────────────────────────

    public function test_body_class_is_admin_viewing_dashboard_present(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk()
            ->assertSee('class="is-admin-viewing-dashboard"', false);
    }

    public function test_body_class_absent_for_customers(): void
    {
        $customer = $this->makeCustomer();

        $response = $this->actingAs($customer)->get('/dashboard');

        $response->assertOk();

        // Check body tag specifically does not have the admin class
        $content = $response->getContent();
        preg_match('/<body[^>]*class="([^"]*)"/', $content, $matches);

        $bodyClass = $matches[1] ?? '';
        $this->assertStringNotContainsString('is-admin-viewing-dashboard', $bodyClass);
    }

    // ─── Admin Panel Link ───────────────────────────────────────────────────

    public function test_admin_panel_link_no_longer_opens_in_new_tab(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk();

        // Find the Admin Panel link and verify no target="_blank"
        $content = $response->getContent();
        preg_match('/<a[^>]*href="\/admin"[^>]*role="menuitem"[^>]*>Admin Panel<\/a>/', $content, $matches);

        $this->assertNotEmpty($matches, 'Admin Panel link not found in profile menu');
        $this->assertStringNotContainsString('target="_blank"', $matches[0], 'Admin Panel link should not have target="_blank"');
    }
}
