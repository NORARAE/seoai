<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerLoginStateTest extends TestCase
{
    use RefreshDatabase;

    private function postLogin(array $payload)
    {
        $this->get(route('login'));

        return $this->followingRedirects()->post(route('login'), array_merge($payload, [
            '_token' => csrf_token(),
        ]));
    }

    public function test_google_only_account_gets_google_account_state_and_google_emphasis(): void
    {
        config()->set('services.google_login.enabled', true);

        User::factory()->create([
            'email' => 'google-only@example.com',
            'password' => Hash::make('unused-password-123'),
            'google_id' => 'google_uid_state_test',
            'auth_provider' => 'google',
            'approved' => true,
            'onboarding_completed_at' => now(),
        ]);

        $response = $this->postLogin([
            'email' => 'google-only@example.com',
            'password' => 'not-the-right-way',
        ]);

        $response->assertOk();
        $response->assertSee('This account uses Google sign-in. Continue with Google to access your dashboard.');
        $response->assertSee('Recommended for this email');
        $response->assertSee('google-btn is-recommended', false);

        $this->assertGuest();
    }

    public function test_wrong_password_gets_wrong_password_state_and_reset_path(): void
    {
        config()->set('services.google_login.enabled', true);

        User::factory()->create([
            'email' => 'email-user@example.com',
            'password' => Hash::make('correct-password-123'),
            'google_id' => null,
            'auth_provider' => null,
            'approved' => true,
            'onboarding_completed_at' => now(),
        ]);

        $response = $this->postLogin([
            'email' => 'email-user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertOk();
        $response->assertSee('Incorrect password. Try again or reset your password.');
        $response->assertSee('Reset password');

        $this->assertGuest();
    }

    public function test_unknown_email_gets_no_account_state_and_register_path(): void
    {
        config()->set('services.google_login.enabled', true);

        $response = $this->postLogin([
            'email' => 'missing@example.com',
            'password' => 'irrelevant-password',
        ]);

        $response->assertOk();
        $response->assertSee('No account found for this email.');
        $response->assertSee('Create account');

        $this->assertGuest();
    }
}
