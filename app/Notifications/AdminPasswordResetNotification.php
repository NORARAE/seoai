<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

/**
 * Custom password reset notification that points to the Filament admin
 * reset route — not the standard Laravel `password.reset` route which
 * does not exist in this Filament-only application.
 *
 * Uses a branded HTML view matching the SEOAIco black/gold design system.
 */
class AdminPasswordResetNotification extends Notification
{
    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        $expireMinutes = Config::get('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('Reset Your SEOAIco Password')
            ->view('emails.password-reset', [
                'url' => $url,
                'expireMinutes' => $expireMinutes,
            ]);
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
