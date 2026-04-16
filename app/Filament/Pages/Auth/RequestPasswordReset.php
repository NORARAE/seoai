<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\RateLimiter;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'Reset your access';
    }

    public function getSubheading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return "We'll send a secure reset link to your email address.";
    }

    /**
     * Extra per-IP bucket (3 per 15 min) on top of Filament's built-in
     * per-email throttle, preventing mass enumeration from a single IP.
     * After the parent sends the reset link, dispatch a calm 3-second
     * redirect back to the admin login page.
     */
    public function request(): void
    {
        $key = 'pw-reset-ip:' . sha1(request()->ip());

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            Notification::make()
                ->title(__('filament-panels::auth/pages/password-reset/request-password-reset.notifications.throttled.title', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]))
                ->danger()
                ->send();

            return;
        }

        RateLimiter::hit($key, 900);

        parent::request();

        // After the generic success notification has been sent, show a secondary
        // message and redirect to the login page after 3 seconds.
        // The message deliberately does not reveal whether an account was found.
        Notification::make()
            ->title('You\'ll be redirected shortly.')
            ->info()
            ->send();

        $this->js("setTimeout(() => window.location.href = '/login', 3000)");
    }
}
