<?php

namespace App\Providers;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cashier::ignoreRoutes();

        // When a Google-only user resets their password, clear auth_provider
        // so Login.php no longer shows "use Google sign-in" on typos.
        Event::listen(PasswordReset::class, function (PasswordReset $event) {
            $user = $event->user;
            if ($user->auth_provider === 'google') {
                $user->auth_provider = null;
                $user->save();
            }
        });

        // After login: gate on approval + onboarding completion.
        // Staff → admin panel, customers → public dashboard.
        $this->app->bind(LoginResponse::class, function (): object {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    $user = Filament::auth()->user();

                    // Auto-approve paid scan users — mirrors CustomerLoginController
                    if ($user && !$user->isApproved() && $user->quickScans()->where('paid', true)->exists()) {
                        $user->update(['approved' => true]);
                    }

                    // Unapproved non-privileged users go to pending page
                    if ($user && !$user->isPrivilegedStaff() && !$user->isFrontendDev() && !$user->isApproved()) {
                        return redirect()->route('pending-approval');
                    }

                    // Approved but onboarding not yet done
                    if ($user && !$user->isPrivilegedStaff() && !$user->isFrontendDev() && $user->isApproved() && is_null($user->onboarding_completed_at)) {
                        return redirect()->route('user.onboarding');
                    }

                    // Staff and frontend devs → admin panel
                    if ($user && ($user->isPrivilegedStaff() || $user->isFrontendDev())) {
                        return redirect()->intended(Filament::getUrl());
                    }

                    // Customers → public dashboard
                    return redirect()->intended('/dashboard');
                }
            };
        });

        // After registering via Filament, always send to pending-approval
        // (Register.php itself handles the approved→onboarding redirect directly).
        $this->app->bind(RegistrationResponse::class, function (): object {
            return new class implements RegistrationResponse {
                public function toResponse($request)
                {
                    return redirect()->route('pending-approval');
                }
            };
        });

        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        // Licensing inquiry form — 3/min per IP; server-side spam scoring adds further friction
        RateLimiter::for(
            'inquiry',
            fn(Request $r): Limit =>
            Limit::perMinute(3)->by($r->ip())
        );

        // Public booking form + slot-fetching AJAX
        RateLimiter::for(
            'booking',
            fn(Request $r): Limit =>
            Limit::perMinute(20)->by($r->ip())
        );

        // Public API endpoints (license validate + Stripe checkout) for WP plugin
        RateLimiter::for(
            'api-public',
            fn(Request $r): Limit =>
            Limit::perMinute(60)->by($r->ip())
        );

        // Auth — login: 5 attempts per 60 s per email+IP (also enforced in Login page)
        RateLimiter::for(
            'login',
            fn(Request $r): Limit =>
            Limit::perMinute(5)->by(
                mb_strtolower((string) $r->input('email', '')) . '|' . $r->ip()
            )
        );

        // Password reset request: 3 per 15 min per IP
        RateLimiter::for(
            'password-reset',
            fn(Request $r): Limit =>
            Limit::perMinutes(15, 3)->by($r->ip())
        );
    }
}

