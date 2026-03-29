<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Filament\Pages\Auth\ResetPassword;
use App\Filament\Pages\SeoGrowthCommandCenter;
use App\Http\Middleware\FrontendDevAccessMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->registration(Register::class)
            ->passwordReset(RequestPasswordReset::class, ResetPassword::class)
            ->brandName(new HtmlString(
                '<a href="/" style="text-decoration:none;color:inherit;display:inline-flex;align-items:baseline;gap:0;line-height:1;font-family:inherit">'
                . '<span style="font-family:\'DM Sans\',sans-serif;font-weight:300;font-size:1.15rem;letter-spacing:.06em;color:inherit">SEO</span>'
                . '<span style="font-family:\'Cormorant Garamond\',serif;font-weight:600;font-size:1.38rem;color:#c8a84b;letter-spacing:.02em;display:inline-block;transform:skewX(-11deg) translateY(-1px)">AI</span>'
                . '<span style="font-family:\'DM Sans\',sans-serif;font-weight:300;font-size:1rem;color:rgba(150,150,150,.7);letter-spacing:.04em">co</span>'
                . '</a>'
            ))
            ->colors([
                'primary' => Color::hex('#c8a84b'),
                'success' => Color::Green,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): HtmlString => auth()->check() && auth()->user()->isFrontendDev()
                    ? new HtmlString(
                        '<div style="background:#1a1200;border-bottom:1px solid rgba(200,168,75,.2);'
                        . 'padding:10px 24px;font-size:.78rem;color:#c8a84b;letter-spacing:.06em;'
                        . 'text-align:center;font-family:\'DM Sans\',sans-serif;">'
                        . '&#128274;&nbsp; Your account has <strong>limited access</strong>. '
                        . 'Only the homepage editor and dashboard are available to you.'
                        . '</div>'
                    )
                    : new HtmlString('')
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(
                    '<link rel="preconnect" href="https://fonts.googleapis.com">'
                    . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
                    . '<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=DM+Sans:wght@300;400&display=swap" rel="stylesheet">'
                )
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                function (): HtmlString {
                    if (! config('services.google_login.enabled', false)) {
                        return new HtmlString('');
                    }
                    $url  = url('/auth/google/redirect');
                    $svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">'
                          . '<path fill="#FFC107" d="M43.6 20H24v8h11.3c-1.6 4.7-6.1 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.8 1.2 8 3l5.7-5.7C34 6.1 29.3 4 24 4 13 4 4 13 4 24s9 20 20 20 20-9 20-20c0-1.3-.1-2.7-.4-4z"/>'
                          . '<path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 15.1 19 12 24 12c3 0 5.8 1.2 8 3l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>'
                          . '<path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.5 35 26.9 36 24 36c-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z"/>'
                          . '<path fill="#1976D2" d="M43.6 20H24v8h11.3c-.7 2-2 3.8-3.6 5.2l6.2 5.2C41 35.2 44 30 44 24c0-1.3-.1-2.7-.4-4z"/>'
                          . '</svg>';
                    $html  = '<div style="margin-top:1.25rem;display:flex;flex-direction:column;align-items:stretch;gap:.75rem;">';
                    $html .= '<div style="display:flex;align-items:center;gap:.75rem;">';
                    $html .= '<div style="flex:1;height:1px;background:rgba(128,128,128,.2);"></div>';
                    $html .= '<span style="font-size:.78rem;color:rgba(128,128,128,.6);text-transform:uppercase;letter-spacing:.08em;">or</span>';
                    $html .= '<div style="flex:1;height:1px;background:rgba(128,128,128,.2);"></div>';
                    $html .= '</div>';
                    $html .= '<a href="' . e($url) . '" style="display:flex;align-items:center;justify-content:center;gap:.625rem;padding:.625rem 1rem;background:#fff;border:1px solid #dadce0;border-radius:6px;box-shadow:0 1px 2px rgba(0,0,0,.05);text-decoration:none;font-family:Roboto,Arial,sans-serif;font-size:.875rem;font-weight:500;color:#3c4043;">';
                    $html .= $svg . '<span>Continue with Google</span></a>';
                    $html .= '</div>';
                    return new HtmlString($html);
                }
            );

        if (File::exists(public_path('build/manifest.json'))) {
            $panel->viteTheme('resources/css/filament/admin/theme.css');
        }

        return $panel
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                SeoGrowthCommandCenter::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                FrontendDevAccessMiddleware::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
