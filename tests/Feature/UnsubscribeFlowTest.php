<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * QA tests for the unsubscribe + notification preferences flow.
 *
 * Verifies:
 *  - Unsubscribe only sets marketing opt-in to false
 *  - Auth/account fields are never mutated
 *  - Dashboard access remains valid after unsubscribe
 *  - Notification preference toggles persist correctly
 */
class UnsubscribeFlowTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'approved' => true,
            'onboarding_completed_at' => now(),
            'email_marketing_opt_in' => true,
            'email_product_updates' => true,
            'email_scan_notifications' => true,
        ], $attrs));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. Unsubscribe behavior: only sets email_marketing_opt_in = false
    // ─────────────────────────────────────────────────────────────────────────

    public function test_unsubscribe_via_lead_token_sets_marketing_opt_in_false(): void
    {
        $user = $this->makeUser(['email' => 'unsub@example.com']);
        $lead = Lead::create([
            'name' => 'Test User',
            'email' => 'unsub@example.com',
            'unsubscribe_token' => 'tok_abc123',
            'source' => 'quick-scan',
        ]);

        $this->get('/unsubscribe/tok_abc123')->assertOk();

        $user->refresh();
        $this->assertFalse($user->email_marketing_opt_in);
        // email_product_updates and email_scan_notifications must remain untouched
        $this->assertTrue($user->email_product_updates);
        $this->assertTrue($user->email_scan_notifications);
    }

    public function test_unsubscribe_does_not_touch_auth_or_account_fields(): void
    {
        $user = $this->makeUser([
            'email' => 'safe@example.com',
            'approved' => true,
            'is_active' => true,
        ]);
        Lead::create([
            'name' => 'Safe User',
            'email' => 'safe@example.com',
            'unsubscribe_token' => 'tok_safe',
            'source' => 'quick-scan',
        ]);

        $this->get('/unsubscribe/tok_safe')->assertOk();

        $user->refresh();
        $this->assertTrue((bool) $user->approved, 'approved must remain true');
        $this->assertTrue((bool) $user->is_active, 'is_active must remain true');
        $this->assertNotNull($user->password, 'password must remain set');
    }

    public function test_unsubscribe_via_email_token_also_sets_opt_in_false(): void
    {
        $user = $this->makeUser(['email' => 'direct@example.com']);
        // No Lead record — email-encoded path (@ must be URL-encoded in real links)
        $this->get('/unsubscribe/direct%40example.com')->assertOk();

        $user->refresh();
        $this->assertFalse($user->email_marketing_opt_in);
    }

    public function test_repeat_unsubscribe_is_idempotent(): void
    {
        $user = $this->makeUser(['email' => 'twice@example.com', 'email_marketing_opt_in' => false]);
        Lead::create([
            'name' => 'Twice User',
            'email' => 'twice@example.com',
            'unsubscribe_token' => 'tok_twice',
            'email_unsubscribed_at' => now()->subDay(),
            'source' => 'quick-scan',
        ]);

        $response = $this->get('/unsubscribe/tok_twice');
        $response->assertOk();
        $response->assertSee('Already unsubscribed');

        $user->refresh();
        $this->assertFalse($user->email_marketing_opt_in);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. Dashboard access remains valid after unsubscribe
    // ─────────────────────────────────────────────────────────────────────────

    public function test_dashboard_accessible_after_unsubscribe(): void
    {
        $user = $this->makeUser(['email' => 'dashuser@example.com', 'email_marketing_opt_in' => false]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertViewIs('dashboard.customer-modern');
    }

    public function test_unsubscribe_page_shows_dashboard_cta(): void
    {
        Lead::create([
            'name' => 'CTA User',
            'email' => 'cta@example.com',
            'unsubscribe_token' => 'tok_cta',
            'source' => 'quick-scan',
        ]);

        $response = $this->get('/unsubscribe/tok_cta');

        $response->assertOk();
        $response->assertSee('/dashboard');
        $response->assertSee('/dashboard/settings/notifications');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. Notification preference persistence
    // ─────────────────────────────────────────────────────────────────────────

    public function test_notification_settings_page_requires_auth(): void
    {
        $response = $this->get('/dashboard/settings/notifications');

        // Unauthenticated request must be redirected to login
        $response->assertRedirect();
        $this->assertStringContainsString('login', $response->headers->get('Location') ?? '');
    }

    public function test_notification_settings_page_loads_for_authed_user(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->get('/dashboard/settings/notifications');

        $response->assertOk();
        $response->assertViewIs('dashboard.settings.notifications');
    }

    public function test_notification_preferences_persist_on_save(): void
    {
        $user = $this->makeUser([
            'email_marketing_opt_in' => true,
            'email_product_updates' => true,
            'email_scan_notifications' => true,
        ]);

        // Submit with only scan_notifications checked (marketing + product updates off).
        // withSession sets a matching CSRF token so VerifyCsrfToken passes.
        $response = $this->actingAs($user)
            ->withSession(['_token' => 'test-token'])
            ->post('/dashboard/settings/notifications', [
                '_token' => 'test-token',
                'email_scan_notifications' => '1',
                // email_marketing_opt_in and email_product_updates intentionally absent (checkbox off)
            ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertFalse($user->email_marketing_opt_in);
        $this->assertFalse($user->email_product_updates);
        $this->assertTrue($user->email_scan_notifications);
    }

    public function test_notification_preferences_all_on_persists(): void
    {
        $user = $this->makeUser([
            'email_marketing_opt_in' => false,
            'email_product_updates' => false,
            'email_scan_notifications' => false,
        ]);

        $this->actingAs($user)
            ->withSession(['_token' => 'test-token'])
            ->post('/dashboard/settings/notifications', [
                '_token' => 'test-token',
                'email_marketing_opt_in' => '1',
                'email_product_updates' => '1',
                'email_scan_notifications' => '1',
            ]);

        $user->refresh();
        $this->assertTrue($user->email_marketing_opt_in);
        $this->assertTrue($user->email_product_updates);
        $this->assertTrue($user->email_scan_notifications);
    }
}
