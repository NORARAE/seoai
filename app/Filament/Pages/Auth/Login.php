<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable|null
    {
        return 'Access your workspace';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Your SEOAIco intelligence platform';
    }

    /**
     * Show any Google OAuth error returned via session flash as a danger notification.
     */
    public function mount(): void
    {
        parent::mount();

        if ($error = session('google_error')) {
            Notification::make()
                ->danger()
                ->title($error)
                ->persistent()
                ->send();
        }
    }

    /**
     * Extra login throttle: 5 attempts per minute keyed by email+IP.
     * This runs *before* Filament's own rateLimit(), giving us a finer-grained
     * per-credential bucket in addition to the global Filament limiter.
     */
    public function authenticate(): ?LoginResponse
    {
        $email = Str::lower($this->data['email'] ?? '');
        $key   = 'login:' . $email . ':' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            Notification::make()
                ->title(__('filament-panels::auth/pages/login.notifications.throttled.title', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]))
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'data.email' => __('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        try {
            $response = parent::authenticate();

            // Clear our throttle bucket on success; regenerate to prevent fixation
            RateLimiter::clear($key);
            request()->session()->regenerate();

            return $response;

        } catch (ValidationException $e) {
            RateLimiter::hit($key, 60);
            throw $e;
        }
        // TooManyRequestsException from Filament's own limiter propagates naturally
    }
}

