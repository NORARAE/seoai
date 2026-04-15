<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\QuickScan;
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
        if (!$this->isEnabled()) {
            abort(404);
        }

        // Preserve optional scan_id so we can return the user to their scan
        $scanId = (int) request()->query('scan_id', 0);
        if ($scanId) {
            request()->session()->put('oauth_scan_id', $scanId);
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
        if (!$this->isEnabled()) {
            abort(404);
        }

        // Handle OAuth denial / errors from Google
        if (request()->has('error')) {
            Log::info('Google OAuth: user denied or error returned', [
                'error' => request('error'),
                'ip' => request()->ip(),
            ]);

            return redirect()->route('filament.admin.auth.login')
                ->with('google_error', 'Google sign-in was cancelled or denied.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google OAuth: callback exception', [
                'message' => $e->getMessage(),
                'ip' => request()->ip(),
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
        if (!empty($allowedDomains)) {
            $emailDomain = Str::after($email, '@');
            if (!in_array($emailDomain, $allowedDomains, true)) {
                Log::info('Google OAuth: domain not allowed', [
                    'domain' => $emailDomain,
                    'ip' => request()->ip(),
                ]);

                return redirect()->route('filament.admin.auth.login')
                    ->with('google_error', 'Your email domain is not authorised to sign in. Contact your administrator.');
            }
        }

        // Locate existing user: first by google_id, then by email.
        // This prevents account takeover — Google is only linked to a matching email.
        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $email)->first();

        if (!$user) {
            if (!config('services.google_login.auto_provision', false)) {
                Log::info('Google OAuth: no account, auto-provision disabled', [
                    'email' => $email,
                    'ip' => request()->ip(),
                ]);

                return redirect()->route('filament.admin.auth.login')
                    ->with('google_error', 'No account found for this email address. Please contact your administrator.');
            }

            // Auto-provision: create account, unapproved by default.
            $user = User::create([
                'name' => $googleUser->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'google_id' => $googleUser->getId(),
                'google_avatar' => $googleUser->getAvatar(),
                'auth_provider' => 'google',
                'email_verified_at' => now(),
                'role' => config('services.google_login.default_role', 'viewer'),
                'approved' => false,
                'last_login_at' => now(),
                'signup_ip' => request()->ip(),
                'signup_user_agent' => substr((string) request()->userAgent(), 0, 512),
                'signup_referrer' => substr((string) request()->headers->get('referer', ''), 0, 512) ?: null,
                'signup_source' => 'google-oauth',
            ]);

            Log::info('Google OAuth: auto-provisioned new user', ['email' => $email]);
        } else {
            // Attach google_id if this is the first Google sign-in for this account.
            if (blank($user->google_id)) {
                $user->google_id = $googleUser->getId();
            }
            $user->google_avatar = $googleUser->getAvatar();
            // Don't overwrite auth_provider — it was set at account creation and
            // tracks the original signup method (null=email, 'google'=Google signup).
            // This lets Login.php correctly identify Google-only users.
            $user->last_login_at = now();
            if (blank($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
            $user->save();
        }

        // Sign in and regenerate session to prevent fixation.
        Auth::login($user, remember: true);
        request()->session()->regenerate();

        // Link any Quick Scans purchased with this email to the user account.
        $linked = QuickScan::where('email', $user->email)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        // Also link the specific scan from the OAuth flow (handles email mismatch:
        // user may have purchased as work@co.com but signed in with personal@gmail.com).
        $oauthScanId = (int) request()->session()->get('oauth_scan_id', 0);
        if ($oauthScanId) {
            $oauthScan = QuickScan::find($oauthScanId);
            if ($oauthScan && is_null($oauthScan->user_id)) {
                $oauthScan->update(['user_id' => $user->id]);
                $linked++;
            }
        }

        // Auto-approve users who have at least one paid scan.
        if (!$user->isApproved() && $user->quickScans()->where('paid', true)->exists()) {
            $user->update(['approved' => true]);
        }

        Log::info('Google OAuth: login successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'scans_linked' => $linked,
            'oauth_scan_id' => $oauthScanId ?: null,
        ]);

        // Approval check: unapproved non-staff users go to pending page.
        if (!$user->isPrivilegedStaff() && !$user->isApproved()) {
            // Keep oauth_scan_id in session — it will be available when the user
            // is eventually approved and completes onboarding.
            return redirect()->route('pending-approval');
        }

        // Approved but onboarding not yet complete — scan_id stays in session
        // so UserOnboardingController can redirect to dashboard#ai-scans after setup.
        if (!$user->isPrivilegedStaff() && $user->isApproved() && is_null($user->onboarding_completed_at)) {
            return redirect()->route('user.onboarding');
        }

        // Privileged staff (super_admin, admin, owner, account_manager) → Filament panel
        if ($user->isPrivilegedStaff()) {
            return redirect()->intended('/admin');
        }

        // Regular approved + onboarded user → SaaS dashboard
        // If a scan ID was saved before OAuth, take user back to the dashboard
        $oauthScanId = (int) request()->session()->pull('oauth_scan_id', 0);
        if ($oauthScanId) {
            // Confirm the scan exists and is associated (by email or now by user_id)
            $scan = QuickScan::find($oauthScanId);
            if ($scan && ($scan->user_id === $user->id || $scan->email === $user->email)) {
                return redirect()->to(url('/dashboard') . '#ai-scans')
                    ->with('scan_saved', 'Your scan has been saved');
            }
        }

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
