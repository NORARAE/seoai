<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

        return view('auth.login', [
            'googleEnabled' => (bool) config('services.google_login.enabled', false),
            'error' => session('google_error'),
            'errorType' => session('google_error_type'),
        ]);
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $email = Str::lower($request->input('email'));
        $key = 'login:' . $email . ':' . $request->ip();

        // Rate limit: 5 attempts per minute per email+IP
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Too many attempts. Please wait {$seconds} seconds."]);
        }

        // Check for Google-only user before attempting auth
        $user = User::where('email', $email)->first();
        if ($user && $user->auth_provider === 'google' && !Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($key, 60);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'This account uses Google sign-in. Use "Continue with Google" above, or click "Forgot password?" to set an email password.']);
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return $this->authenticatedRedirect(Auth::user());
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
}
