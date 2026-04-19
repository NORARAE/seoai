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

class CustomerRegisterController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->authenticatedRedirect(Auth::user());
        }

        return view('auth.register', [
            'googleEnabled' => (bool) config('services.google_login.enabled', false),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $ipKey = 'register-ip:' . sha1($request->ip());

        if (RateLimiter::tooManyAttempts($ipKey, 3)) {
            $seconds = RateLimiter::availableIn($ipKey);

            return back()
                ->withInput($request->except(['password', 'password_confirmation', 'access_code']))
                ->withErrors(['email' => "Too many registration attempts. Please wait {$seconds} seconds."]);
        }

        RateLimiter::hit($ipKey, 600);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'use_case' => ['required', 'string', 'max:255'],
            'access_code' => ['nullable', 'string', 'max:128'],
        ]);

        $accessCode = trim((string) ($validated['access_code'] ?? ''));
        $validCode = (string) config('services.registration.access_code', '');

        $approved = $validCode !== '' && $accessCode !== '' && hash_equals($validCode, $accessCode);

        $user = User::create([
            'name' => $validated['name'],
            'email' => Str::lower($validated['email']),
            'password' => Hash::make($validated['password']),
            'use_case' => $validated['use_case'],
            'approved' => $approved,
            'role' => 'buyer',
            'signup_ip' => $request->ip(),
            'signup_user_agent' => substr((string) $request->userAgent(), 0, 512),
            'signup_referrer' => substr((string) $request->headers->get('referer', ''), 0, 512) ?: null,
            'signup_source' => 'web-register-public',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if (!$user->isApproved()) {
            return redirect()->route('pending-approval');
        }

        return redirect()->route('user.onboarding');
    }

    private function authenticatedRedirect($user): RedirectResponse
    {
        if (!$user->isPrivilegedStaff() && !$user->isApproved()) {
            return redirect()->route('pending-approval');
        }

        if (!$user->isPrivilegedStaff() && $user->isApproved() && is_null($user->onboarding_completed_at)) {
            return redirect()->route('user.onboarding');
        }

        if ($user->isPrivilegedStaff() || $user->isFrontendDev()) {
            return redirect()->intended('/admin');
        }

        return redirect()->intended('/dashboard');
    }
}
