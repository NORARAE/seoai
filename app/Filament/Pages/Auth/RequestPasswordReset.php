<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('email')
                ->label('Email address')
                ->email()
                ->required()
                ->autocomplete('email')
                ->autofocus(),
        ]);
    }

    public function request(): void
    {
        $key = 'password-reset-request:' . request()->ip();

        // 3 password reset requests per 15 minutes per IP
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            Notification::make()
                ->title('Too many requests')
                ->body("Please wait {$seconds} seconds before requesting another reset.")
                ->danger()
                ->send();

            return;
        }

        RateLimiter::hit($key, 900); // 15-minute decay

        $this->validate();

        // Always send the same response to prevent account enumeration
        Password::sendResetLink(['email' => $this->data['email']]);

        Notification::make()
            ->title('Check your inbox')
            ->body('If an account exists for that address, a reset link has been sent. It expires in 60 minutes.')
            ->success()
            ->send();

        $this->form->fill();
    }
}
