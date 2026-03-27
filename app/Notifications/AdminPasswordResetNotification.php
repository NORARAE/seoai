<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

/**
 * Custom password reset notification that points to the Filament admin
 * reset route — not the standard Laravel `password.reset` route which
 * does not exist in this Filament-only application.
 */
class AdminPasswordResetNotification extends Notification
{
    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        $expireMinutes = Config::get('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject(Lang::get('Reset Your Password'))
            ->line(Lang::get('You are receiving this email because a password reset request was made for your account.'))
            ->action(Lang::get('Reset Password'), $url)
            ->line(Lang::get('This link will expire in :count minutes.', ['count' => $expireMinutes]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }

    protected function resetUrl(object $notifiable): string
    {
        return url(route(
            'filament.admin.auth.password-reset.reset',
            [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ],
            false
        ));
    }
}
