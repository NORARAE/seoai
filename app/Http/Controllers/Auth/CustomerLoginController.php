<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\QuickScan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomerLoginController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->authenticatedRedirect(Auth::user());
        }

        $redirectTarget = $this->sanitizeRedirectTarget($request->query('redirect'));
        if ($redirectTarget) {
            $request->session()->put('url.intended', $redirectTarget);
        }

        $notice = $request->query('notice');
        $checkoutNotice = $notice === 'scan-results' ? 'Sign in to view your results.' : null;

        return view('auth.login', [
            'googleEnabled' => (bool) config('services.google_login.enabled', false),
            'error' => session('google_error'),
            'errorType' => session('google_error_type'),
            'checkoutNotice' => $checkoutNotice,
        ]);
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $email = Str::lower($request->input('email'));
        $password = (string) $request->input('password');
        $remember = $request->boolean('remember');
        $key = 'login:' . $email . ':' . $request->ip();

        // Rate limit: 5 attempts per minute per email+IP
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Too many attempts. Please wait {$seconds} seconds."]);
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (!$user) {
            RateLimiter::hit($key, 60);

            return back()
                ->withInput($request->only('email'))
                ->with('login_error_type', 'no_account')
                ->with('login_error_message', 'No account found for this email.');
        }

        if ($user->isGoogleOnlyAccount()) {
            RateLimiter::hit($key, 60);

            return back()
                ->withInput($request->only('email'))
                ->with('login_error_type', 'google_account')
                ->with('login_error_message', 'This account uses Google sign-in. Continue with Google to access your dashboard.');
        }

        if (!Hash::check($password, (string) $user->password)) {
            RateLimiter::hit($key, 60);

            return back()
                ->withInput($request->only('email'))
                ->with('login_error_type', 'wrong_password')
                ->with('login_error_message', 'Incorrect password. Try again or reset your password.');
        }

        Auth::login($user, $remember);

        RateLimiter::clear($key);
        $request->session()->regenerate();

        $user = Auth::user();

        // Link any orphan scans (paid but no user_id) matching this user's email
        $linked = QuickScan::where('email', $user->email)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        // Auto-approve if user has paid scans but isn't approved yet
        if (!$user->isApproved() && $user->quickScans()->where('paid', true)->exists()) {
            $user->update(['approved' => true]);
        }

        $redirect = $this->authenticatedRedirect($user);

        if ($linked > 0) {
            $redirect = $redirect->with('scan_saved', 'We found a previous purchase and added it to your dashboard.');
        } elseif ($user->quickScans()->where('paid', true)->exists()) {
            $redirect = $redirect->with('scan_saved', 'Your previous scans are ready — view them in your dashboard.');
        }

        return $redirect;
    }

    private function authenticatedRedirect($user): RedirectResponse
    {
        // Unapproved non-staff → pending
        if (!$user->isPrivilegedStaff() && !$user->isApproved()) {
            return redirect()->route('pending-approval');
        }

        // Approved but onboarding not complete
        if (!$user->isPrivilegedStaff() && $user->isApproved() && is_null($user->onboarding_completed_at)) {
            return redirect()->route('user.onboarding');
        }

        // Staff → admin panel
        if ($user->isPrivilegedStaff() || $user->isFrontendDev()) {
            return redirect()->intended('/admin');
        }

        // Customer → dashboard
        return redirect()->intended('/dashboard');
    }

    private function sanitizeRedirectTarget(mixed $target): ?string
    {
        if (!is_string($target)) {
            return null;
        }

        $target = trim($target);
        if ($target === '' || !str_starts_with($target, '/')) {
            return null;
        }

        return str_starts_with($target, '//') ? null : $target;
    }
}
