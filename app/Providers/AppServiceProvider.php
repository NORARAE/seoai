<?php

namespace App\Providers;

use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
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

        // After registering via Filament, send new users to the pending-approval
        // page rather than the /admin panel home. Bound in boot() so it runs
        // after FilamentServiceProvider::register() and wins the binding.
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
        // Licensing inquiry form — generous but prevents spam floods
        RateLimiter::for('inquiry', fn (Request $r): Limit =>
            Limit::perHour(10)->by($r->ip())
        );

        // Public booking form + slot-fetching AJAX
        RateLimiter::for('booking', fn (Request $r): Limit =>
            Limit::perMinute(20)->by($r->ip())
        );

        // Public API endpoints (license validate + Stripe checkout) for WP plugin
        RateLimiter::for('api-public', fn (Request $r): Limit =>
            Limit::perMinute(60)->by($r->ip())
        );
    }
}

