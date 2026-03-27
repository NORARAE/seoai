<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\PasswordReset\ResetPassword as BaseResetPassword;
use Filament\Schemas\Components\Component;
use Illuminate\Validation\Rules\Password;

class ResetPassword extends BaseResetPassword
{
    /**
     * Strengthen the password rule beyond Laravel's default.
     * We override only this component; the base class form() and
     * resetPassword() handle everything else (rate limiting, token
     * validation, hashing, notifications).
     */
    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->rule(Password::min(12)->mixedCase()->numbers()->symbols());
    }
}
