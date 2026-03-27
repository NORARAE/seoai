<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\PasswordReset\ResetPassword as BaseResetPassword;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class ResetPassword extends BaseResetPassword
{
    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('password')
                ->label('New password')
                ->password()
                ->revealable()
                ->required()
                ->rule(Password::min(12)->mixedCase()->numbers()->symbols())
                ->same('passwordConfirmation')
                ->autocomplete('new-password'),

            TextInput::make('passwordConfirmation')
                ->label('Confirm password')
                ->password()
                ->revealable()
                ->required()
                ->dehydrated(false)
                ->autocomplete('new-password'),
        ]);
    }

    public function resetPassword(): void
    {
        $key = 'password-reset-submit:' . request()->ip();

        // 5 reset submissions per 15 minutes per IP
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            Notification::make()
                ->title('Too many attempts')
                ->body("Please wait {$seconds} seconds.")
                ->danger()
                ->send();

            return;
        }

        RateLimiter::hit($key, 900);

        parent::resetPassword();

        // Clear throttle bucket on successful reset
        RateLimiter::clear($key);
    }
}
