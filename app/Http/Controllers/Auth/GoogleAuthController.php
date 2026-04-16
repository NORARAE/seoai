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

        // Determine if this is a public scan-originated flow
        $oauthScanId = (int) request()->session()->get('oauth_scan_id', 0);
        $isPublicScanFlow = $oauthScanId > 0;

        // Handle OAuth denial / errors from Google
        if (request()->has('error')) {
            Log::info('Google OAuth: user denied or error returned', [
                'error' => request('error'),
                'ip' => request()->ip(),
            ]);

            return $this->errorRedirect('Google sign-in was cancelled or denied.', $oauthScanId);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google OAuth: callback exception', [
                'message' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);

            return $this->errorRedirect('Google sign-in failed. Please try again.', $oauthScanId);
        }

        // Email is required — Google always provides one, but be defensive.
        $email = $googleUser->getEmail();
        if (blank($email)) {
            Log::warning('Google OAuth: no email returned', ['ip' => request()->ip()]);

            return $this->errorRedirect('No email address was returned by Google.', $oauthScanId);
        }

        // Domain restriction (not applied to public scan users — they've already paid)
        $allowedDomains = $this->getAllowedDomains();
        if (!empty($allowedDomains) && !$isPublicScanFlow) {
            $emailDomain = Str::after($email, '@');
            if (!in_array($emailDomain, $allowedDomains, true)) {
                Log::info('Google OAuth: domain not allowed', [
                    'domain' => $emailDomain,
                    'ip' => request()->ip(),
                ]);

                return $this->errorRedirect('Your email domain is not authorised to sign in.', $oauthScanId);
            }
        }

        // Locate existing user: first by google_id, then by email.
        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $email)->first();

        if (!$user) {
            // Auto-provision: always for paid scan users, otherwise respect config.
            $shouldProvision = $isPublicScanFlow || config('services.google_login.auto_provision', false);

            if (!$shouldProvision) {
                Log::info('Google OAuth: no account, auto-provision disabled', [
                    'email' => $email,
                    'ip' => request()->ip(),
                ]);

                return $this->errorRedirect('No account found for this email address.', $oauthScanId);
            }

            // Auto-provision: create account. Approved=false initially —
            // auto-approval happens below if user has a paid scan.
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
                'signup_source' => $isPublicScanFlow ? 'quick-scan' : 'google-oauth',
            ]);

            Log::info('Google OAuth: auto-provisioned new user', [
                'email' => $email,
                'source' => $isPublicScanFlow ? 'quick-scan' : 'google-oauth',
            ]);
        } else {
            // Attach google_id if this is the first Google sign-in for this account.
            if (blank($user->google_id)) {
                $user->google_id = $googleUser->getId();
            }
            $user->google_avatar = $googleUser->getAvatar();
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

        // ── Public scan flow: always return to the scan result page ──────
        if ($oauthScanId) {
            $scan = QuickScan::find($oauthScanId);
            if ($scan && ($scan->user_id === $user->id || $scan->email === $user->email)) {
                // Skip approval/onboarding gates — the user is saving their scan.
                // They'll hit those gates when they navigate to /dashboard later.
                request()->session()->pull('oauth_scan_id');

                $resultUrl = url('/quick-scan/result')
                    . '?session_id=' . ($scan->stripe_session_id ?? 'none')
                    . '&scan_id=' . $scan->id;

                return redirect()->to($resultUrl)
                    ->with('scan_saved', 'Your scan has been saved to your account.');
            }
        }

        // ── Non-scan flows below ────────────────────────────────────────

        // Approval check: unapproved non-staff users go to pending page.
        if (!$user->isPrivilegedStaff() && !$user->isApproved()) {
            return redirect()->route('pending-approval');
        }

        // Approved but onboarding not yet complete
        if (!$user->isPrivilegedStaff() && $user->isApproved() && is_null($user->onboarding_completed_at)) {
            return redirect()->route('user.onboarding');
        }

        // Privileged staff → Filament panel
        if ($user->isPrivilegedStaff()) {
            return redirect()->intended('/admin');
        }

        // Regular approved + onboarded user → SaaS dashboard
        return redirect()->intended('/dashboard');
    }

    // ─── Private Helpers ────────────────────────────────────────────────────

    /**
     * Redirect to the appropriate error page depending on context.
     * Public scan users go back to their scan result; admin users go to Filament login.
     */
    private function errorRedirect(string $message, int $oauthScanId = 0): RedirectResponse
    {
        if ($oauthScanId) {
            $scan = QuickScan::find($oauthScanId);
            if ($scan) {
                $resultUrl = url('/quick-scan/result')
                    . '?session_id=' . ($scan->stripe_session_id ?? 'none')
                    . '&scan_id=' . $scan->id;

                return redirect()->to($resultUrl)
                    ->with('google_error', $message);
            }
        }

        return redirect()->to('/login')
            ->with('google_error', $message)
            ->with('google_error_type', $this->classifyError($message));
    }

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

    private function classifyError(string $message): string
    {
        if (str_contains($message, 'No account found')) {
            return 'no-account';
        }
        if (str_contains($message, 'domain is not authorised')) {
            return 'domain-blocked';
        }
        if (str_contains($message, 'No email')) {
            return 'no-email';
        }
        return 'failed';
    }
}
