<?php

namespace App\Providers;

use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Livewire\Features\SupportRedirects\Redirector;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // After registering via Filament, send new users to the pending-approval
        // page rather than the /admin panel home.
        $this->app->bind(RegistrationResponse::class, function (): object {
            return new class implements RegistrationResponse {
                public function toResponse($request): RedirectResponse
                {
                    return redirect()->route('pending-approval');
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cashier::ignoreRoutes();
    }
}
