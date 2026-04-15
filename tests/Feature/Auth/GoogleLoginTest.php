<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function mockSocialiteUser(array $overrides = []): SocialiteUser
    {
        $data = array_merge([
            'id' => 'google_uid_123',
            'email' => 'alice@example.com',
            'name' => 'Alice Example',
            'avatar' => 'https://lh3.googleusercontent.com/a/avatar',
        ], $overrides);

        $gUser = Mockery::mock(SocialiteUser::class);
        $gUser->shouldReceive('getId')->andReturn($data['id']);
        $gUser->shouldReceive('getEmail')->andReturn($data['email']);
        $gUser->shouldReceive('getName')->andReturn($data['name']);
        $gUser->shouldReceive('getAvatar')->andReturn($data['avatar']);

        return $gUser;
    }

    private function mockSocialiteDriver(SocialiteUser $gUser): void
    {
        $driver = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $driver->shouldReceive('scopes')->andReturnSelf();
        $driver->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));
        $driver->shouldReceive('user')->andReturn($gUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($driver);
    }

    // ─── 1. Redirect route works when enabled ───────────────────────────────

    public function test_redirect_route_redirects_to_google_when_enabled(): void
    {
        Config::set('services.google_login.enabled', true);
        Config::set('services.google.client_id', 'fake-client-id');
        Config::set('services.google.client_secret', 'fake-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');

        $driver = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $driver->shouldReceive('scopes')->andReturnSelf();
        $driver->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth?foo=bar'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($driver);

        $response = $this->get(route('auth.google.redirect'));

        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth?foo=bar');
    }

    // ─── 2. Redirect route returns 404 when disabled ────────────────────────

    public function test_redirect_route_returns_404_when_disabled(): void
    {
        Config::set('services.google_login.enabled', false);

        $this->get(route('auth.google.redirect'))->assertNotFound();
    }

    // ─── 3. Callback route returns 404 when disabled ────────────────────────

    public function test_callback_route_returns_404_when_disabled(): void
    {
        Config::set('services.google_login.enabled', false);

        $this->get(route('auth.google.callback'))->assertNotFound();
    }

    // ─── 4. Logs in an existing user matched by google_id ───────────────────

    public function test_callback_logs_in_existing_user_by_google_id(): void
    {
        Config::set('services.google_login.enabled', true);
        Config::set('services.google_login.allowed_domains', '');

        $user = User::factory()->create([
            'email' => 'alice@example.com',
            'google_id' => 'google_uid_123',
            'approved' => true,
            'onboarding_completed_at' => now(),
        ]);

        $this->mockSocialiteDriver($this->mockSocialiteUser());

        // Regular user (no privileged role) → /dashboard
        $this->get(route('auth.google.callback'))
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    // ─── 5. Logs in an existing user matched by email (first Google link) ───

    public function test_callback_links_google_id_to_existing_email_user(): void
    {
        Config::set('services.google_login.enabled', true);
        Config::set('services.google_login.allowed_domains', '');

        $user = User::factory()->create([
            'email' => 'alice@example.com',
            'google_id' => null,
            'approved' => true,
            'onboarding_completed_at' => now(),
        ]);

        $this->mockSocialiteDriver($this->mockSocialiteUser());

        // Regular user (no privileged role) → /dashboard
        $this->get(route('auth.google.callback'))
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
        $this->assertSame('google_uid_123', $user->fresh()->google_id);
        // auth_provider should NOT be overwritten for existing email users
        $this->assertNull($user->fresh()->auth_provider);
    }

    // ─── 6. Denies unknown email when auto_provision is disabled ────────────

    public function test_callback_denies_unknown_user_when_auto_provision_off(): void
    {
        Config::set('services.google_login.enabled', true);
        Config::set('services.google_login.auto_provision', false);
        Config::set('services.google_login.allowed_domains', '');

        $this->mockSocialiteDriver($this->mockSocialiteUser(['email' => 'stranger@example.com']));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('filament.admin.auth.login'));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'stranger@example.com']);
    }

    // ─── 7. Auto-provisions a new user when enabled ─────────────────────────

    public function test_callback_creates_user_when_auto_provision_enabled(): void
    {
        Config::set('services.google_login.enabled', true);
        Config::set('services.google_login.auto_provision', true);
        Config::set('services.google_login.allowed_domains', '');
        Config::set('services.google_login.default_role', 'viewer');

        $this->mockSocialiteDriver($this->mockSocialiteUser([
            'email' => 'new@example.com',
            'id' => 'uid_new',
        ]));

        $this->get(route('auth.google.callback'));

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'google_id' => 'uid_new',
            'auth_provider' => 'google',
            'role' => 'viewer',
        ]);

        // Newly provisioned users are unapproved — redirect to pending
        $newUser = User::where('email', 'new@example.com')->first();
        $this->assertFalse((bool) $newUser->approved);
    }

    // ─── 8. Denies email from a disallowed domain ───────────────────────────

    public function test_callback_denies_disallowed_domain(): void
    {
        Config::set('services.google_login.enabled', true);
        Config::set('services.google_login.allowed_domains', 'mycompany.com');

        $this->mockSocialiteDriver($this->mockSocialiteUser(['email' => 'hacker@evil.com']));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('filament.admin.auth.login'));

        $this->assertGuest();
    }

    // ─── 9. Allows email from an allowed domain ─────────────────────────────

    public function test_callback_allows_user_from_allowed_domain(): void
    {
        Config::set('services.google_login.enabled', true);
        Config::set('services.google_login.allowed_domains', 'mycompany.com');
        Config::set('services.google_login.auto_provision', true);

        $this->mockSocialiteDriver($this->mockSocialiteUser(['email' => 'bob@mycompany.com']));

        $this->get(route('auth.google.callback'));

        $this->assertDatabaseHas('users', ['email' => 'bob@mycompany.com']);
    }

    // ─── 10. Unapproved user redirected to pending-approval page ───────────

    public function test_unapproved_user_redirected_to_pending_approval(): void
    {
        Config::set('services.google_login.enabled', true);
        Config::set('services.google_login.allowed_domains', '');

        $user = User::factory()->create([
            'email' => 'pending@example.com',
            'google_id' => 'google_uid_456',
            'approved' => false,
            'role' => 'viewer',
        ]);

        $this->mockSocialiteDriver($this->mockSocialiteUser([
            'email' => 'pending@example.com',
            'id' => 'google_uid_456',
        ]));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('pending-approval'));
    }

    // ─── 11. Existing password-based login still works ──────────────────────

    public function test_password_login_still_works(): void
    {
        $user = User::factory()->create([
            'email' => 'pass@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        $this->assertTrue(
            auth()->attempt(['email' => 'pass@example.com', 'password' => 'correct-password'])
        );
    }

    // ─── 12. OAuth error param redirects with session flash ─────────────────

    public function test_callback_with_error_param_redirects_with_flash(): void
    {
        Config::set('services.google_login.enabled', true);

        $response = $this->get(route('auth.google.callback') . '?error=access_denied');

        $response->assertRedirect(route('filament.admin.auth.login'));
        $response->assertSessionHas('google_error');

        $this->assertGuest();
    }

    // ═══════════════════════════════════════════════════════════════════════
    // Auth UX QA Scenarios
    // ═══════════════════════════════════════════════════════════════════════

    // ─── 13. Email user who links Google keeps their auth_provider ──────────

    public function test_email_user_linking_google_preserves_auth_provider(): void
    {
        Config::set('services.google_login.enabled', true);
        Config::set('services.google_login.allowed_domains', '');

        // User registered via email — auth_provider is null
        $user = User::factory()->create([
            'email' => 'alice@example.com',
            'password' => bcrypt('my-password'),
            'google_id' => null,
            'auth_provider' => null,
            'approved' => true,
            'onboarding_completed_at' => now(),
        ]);

        $this->mockSocialiteDriver($this->mockSocialiteUser());

        $this->get(route('auth.google.callback'));

        $fresh = $user->fresh();
        // Google ID should be linked
        $this->assertSame('google_uid_123', $fresh->google_id);
        // auth_provider should NOT change — user originally signed up with email
        $this->assertNull($fresh->auth_provider);
    }

    // ─── 14. Email user can still password-login after linking Google ────────

    public function test_email_user_password_login_works_after_google_link(): void
    {
        // Simulate an email user who already linked Google
        $user = User::factory()->create([
            'email' => 'dual@example.com',
            'password' => bcrypt('my-password'),
            'google_id' => 'google_uid_dual',
            'auth_provider' => null, // email signup, Google linked later
            'approved' => true,
            'onboarding_completed_at' => now(),
        ]);

        $this->assertTrue(
            auth()->attempt(['email' => 'dual@example.com', 'password' => 'my-password']),
            'Email/password login should still work after linking Google'
        );
    }

    // ─── 15. Google-only user has auth_provider='google' ────────────────────

    public function test_auto_provisioned_google_user_has_google_auth_provider(): void
    {
        Config::set('services.google_login.enabled', true);
        Config::set('services.google_login.auto_provision', true);
        Config::set('services.google_login.allowed_domains', '');
        Config::set('services.google_login.default_role', 'viewer');

        $this->mockSocialiteDriver($this->mockSocialiteUser([
            'email' => 'google-only@example.com',
            'id' => 'uid_google_only',
        ]));

        $this->get(route('auth.google.callback'));

        $user = User::where('email', 'google-only@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('google', $user->auth_provider);
    }

    // ─── 16. Google-only user can't password-login (random password) ────────

    public function test_google_only_user_cannot_password_login(): void
    {
        // Simulate a Google-provisioned user (random 40-char password)
        $user = User::factory()->create([
            'email' => 'google-user@example.com',
            'password' => bcrypt(\Illuminate\Support\Str::random(40)),
            'google_id' => 'google_uid_only',
            'auth_provider' => 'google',
            'approved' => true,
            'onboarding_completed_at' => now(),
        ]);

        $this->assertFalse(
            auth()->attempt(['email' => 'google-user@example.com', 'password' => 'any-guess']),
            'Google-only user should not be able to log in with a guessed password'
        );
    }

    // ─── 17. Password reset clears google auth_provider ─────────────────────

    public function test_password_reset_clears_google_auth_provider(): void
    {
        $user = User::factory()->create([
            'email' => 'resetter@example.com',
            'auth_provider' => 'google',
        ]);

        // Simulate the PasswordReset event (fired by Laravel after successful reset)
        event(new \Illuminate\Auth\Events\PasswordReset($user));

        $this->assertNull(
            $user->fresh()->auth_provider,
            'auth_provider should be cleared after password reset so email/password login works'
        );
    }

    // ─── 18. Password reset does NOT affect non-Google users ────────────────

    public function test_password_reset_preserves_null_auth_provider(): void
    {
        $user = User::factory()->create([
            'email' => 'email-user@example.com',
            'auth_provider' => null,
        ]);

        event(new \Illuminate\Auth\Events\PasswordReset($user));

        $this->assertNull(
            $user->fresh()->auth_provider,
            'auth_provider should stay null for email-only users after password reset'
        );
    }

    // ─── 19. Quick Scan scan_id preserved through OAuth flow ────────────────

    public function test_scan_id_preserved_through_oauth_flow(): void
    {
        Config::set('services.google_login.enabled', true);
        Config::set('services.google_login.allowed_domains', '');

        $scan = \App\Models\QuickScan::create([
            'url' => 'https://example.com',
            'email' => 'scanner@example.com',
            'status' => 'scanned',
            'paid' => true,
        ]);

        $user = User::factory()->create([
            'email' => 'scanner@example.com',
            'google_id' => 'uid_scanner',
            'approved' => true,
            'onboarding_completed_at' => now(),
            'role' => 'viewer',
        ]);

        $this->mockSocialiteDriver($this->mockSocialiteUser([
            'email' => 'scanner@example.com',
            'id' => 'uid_scanner',
        ]));

        // Start with scan_id in session (set during redirect to Google)
        $this->withSession(['oauth_scan_id' => $scan->id])
            ->get(route('auth.google.callback'))
            ->assertRedirect(url('/dashboard') . '#ai-scans');

        // Scan should be linked to the user
        $this->assertSame($user->id, $scan->fresh()->user_id);
    }

    // ─── 20. Privileged staff bypass approval and go to /admin ──────────────

    public function test_privileged_staff_bypasses_approval_gate(): void
    {
        Config::set('services.google_login.enabled', true);
        Config::set('services.google_login.allowed_domains', '');

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'google_id' => 'uid_admin',
            'approved' => false, // not approved, but staff
            'role' => 'super_admin',
            'onboarding_completed_at' => now(),
        ]);

        $this->mockSocialiteDriver($this->mockSocialiteUser([
            'email' => 'admin@example.com',
            'id' => 'uid_admin',
        ]));

        $this->get(route('auth.google.callback'))
            ->assertRedirect('/admin');
    }
}