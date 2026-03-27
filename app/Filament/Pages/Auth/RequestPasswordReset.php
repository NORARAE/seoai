<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\RateLimiter;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    /**
     * Extra per-IP bucket (3 per 15 min) on top of Filament's built-in
     * per-email throttle, preventing mass enumeration from a single IP.
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
    }
}
