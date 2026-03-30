<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth consent screen.
     * Requests only the minimal scopes required for identity sign-in.
     */
    public function redirect(): RedirectResponse
    {
        if (! $this->isEnabled()) {
            abort(404);
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->redirect();
    }

    /**
     * Handle the Google OAuth callback.
     */
    public function callback(): RedirectResponse
    {
        if (! $this->isEnabled()) {
            abort(404);
        }

        // Handle OAuth denial / errors from Google
        if (request()->has('error')) {
            Log::info('Google OAuth: user denied or error returned', [
                'error' => request('error'),
                'ip'    => request()->ip(),
            ]);

            return redirect()->route('filament.admin.auth.login')
                ->with('google_error', 'Google sign-in was cancelled or denied.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google OAuth: callback exception', [
                'message' => $e->getMessage(),
                'ip'      => request()->ip(),
            ]);

            return redirect()->route('filament.admin.auth.login')
                ->with('google_error', 'Google sign-in failed. Please try again.');
        }

        // Email is required — Google always provides one, but be defensive.
        $email = $googleUser->getEmail();
        if (blank($email)) {
            Log::warning('Google OAuth: no email returned', ['ip' => request()->ip()]);

            return redirect()->route('filament.admin.auth.login')
                ->with('google_error', 'No email address was returned by Google.');
        }

        // Domain restriction
        $allowedDomains = $this->getAllowedDomains();
        if (! empty($allowedDomains)) {
            $emailDomain = Str::after($email, '@');
            if (! in_array($emailDomain, $allowedDomains, true)) {
                Log::info('Google OAuth: domain not allowed', [
                    'domain' => $emailDomain,
                    'ip'     => request()->ip(),
                ]);

                return redirect()->route('filament.admin.auth.login')
                    ->with('google_error', 'Your email domain is not authorised to sign in. Contact your administrator.');
            }
        }

        // Locate existing user: first by google_id, then by email.
        // This prevents account takeover — Google is only linked to a matching email.
        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $email)->first();

        if (! $user) {
            if (! config('services.google_login.auto_provision', false)) {
                Log::info('Google OAuth: no account, auto-provision disabled', [
                    'email' => $email,
                    'ip'    => request()->ip(),
                ]);

                return redirect()->route('filament.admin.auth.login')
                    ->with('google_error', 'No account found for this email address. Please contact your administrator.');
            }

            // Auto-provision: create account, unapproved by default.
            $user = User::create([
                'name'              => $googleUser->getName() ?: Str::before($email, '@'),
                'email'             => $email,
                'password'          => Hash::make(Str::random(40)),
                'google_id'         => $googleUser->getId(),
                'google_avatar'     => $googleUser->getAvatar(),
                'auth_provider'     => 'google',
                'email_verified_at' => now(),
                'role'              => config('services.google_login.default_role', 'viewer'),
                'approved'          => false,
                'last_login_at'     => now(),
            ]);

            Log::info('Google OAuth: auto-provisioned new user', ['email' => $email]);
        } else {
            // Attach google_id if this is the first Google sign-in for this account.
            if (blank($user->google_id)) {
                $user->google_id = $googleUser->getId();
            }
            $user->google_avatar = $googleUser->getAvatar();
            $user->auth_provider = 'google';
            $user->last_login_at = now();
            if (blank($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
            $user->save();
        }

        // Sign in and regenerate session to prevent fixation.
        Auth::login($user, remember: true);
        request()->session()->regenerate();

        Log::info('Google OAuth: login successful', [
            'user_id' => $user->id,
            'email'   => $user->email,
        ]);

        // Approval check: unapproved non-staff users go to pending page.
        if (! $user->isPrivilegedStaff() && ! $user->isApproved()) {
            return redirect()->route('pending-approval');
        }

        // Approved but onboarding not yet complete
        if (! $user->isPrivilegedStaff() && $user->isApproved() && is_null($user->onboarding_completed_at)) {
            return redirect()->route('user.onboarding');
        }

        // Privileged staff (super_admin, admin, owner, account_manager) → Filament panel
        if ($user->isPrivilegedStaff()) {
            return redirect()->intended('/admin');
        }

        // Regular approved + onboarded user → SaaS dashboard
        return redirect()->intended('/dashboard');
    }

    // ─── Private Helpers ────────────────────────────────────────────────────

    private function isEnabled(): bool
    {
        return (bool) config('services.google_login.enabled', true);
    }

    /** @return string[] */
    private function getAllowedDomains(): array
    {
        $domains = (string) config('services.google_login.allowed_domains', '');
        if (blank($domains)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $domains))));
    }
}
