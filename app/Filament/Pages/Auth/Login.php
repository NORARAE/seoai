<?php

namespace App\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    /**
     * Throttle key based on email + IP so credential stuffing hits
     * the same bucket regardless of different email variations.
     */
    protected function getRateLimitKey(): string
    {
        $email = Str::lower($this->data['email'] ?? '');

        return 'filament-login:' . $email . ':' . request()->ip();
    }

    public function authenticate(): ?LoginResponse
    {
        $key = $this->getRateLimitKey();

        // 5 attempts per minute per email+IP
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            Notification::make()
                ->title('Too many attempts')
                ->body("Please wait {$seconds} seconds before trying again.")
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

            // Successful login — clear throttle, regenerate session
            RateLimiter::clear($key);
            request()->session()->regenerate();

            return $response;

        } catch (ValidationException $e) {
            RateLimiter::hit($key, 60);
            throw $e;
        }
    }
}
