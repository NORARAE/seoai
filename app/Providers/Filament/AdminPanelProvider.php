<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Filament\Pages\Auth\ResetPassword;
use App\Filament\Pages\SeoGrowthCommandCenter;
use App\Http\Middleware\FrontendDevAccessMiddleware;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
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
                . '<span style="font-family:\'DM Sans\',sans-serif;font-weight:300;font-size:1.15rem;letter-spacing:.06em;color:#f5f0e8">SEO</span>'
                . '<span style="font-family:\'Cormorant Garamond\',serif;font-weight:600;font-size:1.38rem;color:#c8a84b;letter-spacing:.02em;display:inline-block;transform:skewX(-11deg) translateY(-1px)">AI</span>'
                . '<span style="font-family:\'DM Sans\',sans-serif;font-weight:300;font-size:1rem;color:rgba(255,255,255,.45);letter-spacing:.04em">co</span>'
                . '</a>'
            ))
            ->colors([
                'primary' => Color::hex('#c8a84b'),
                'success' => Color::Green,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn(): HtmlString => auth()->check() && auth()->user()->isFrontendDev()
                ? new HtmlString(
                    '<div style="background:#110e00;border-bottom:1px solid rgba(111,84,29,.2);'
                    . 'padding:10px 24px;font-size:.78rem;color:#7c6127;letter-spacing:.06em;'
                    . 'text-align:center;font-family:\'DM Sans\',sans-serif;">'
                    . '&#128274;&nbsp; Your account has <strong>limited access</strong>. '
                    . 'Only the homepage editor and dashboard are available to you.'
                    . '</div>'
                )
                : new HtmlString('')
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn(): HtmlString => new HtmlString(
                    '<link rel="preconnect" href="https://fonts.googleapis.com">'
                    . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
                    . '<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=DM+Sans:wght@300;400&display=swap" rel="stylesheet">'
                    . '<script>'
                    . '(function(){try{var tz=Intl.DateTimeFormat().resolvedOptions().timeZone;document.cookie="tz="+tz+";path=/;SameSite=Strict;max-age=86400";}catch(e){}})();'
                    . 'document.addEventListener("DOMContentLoaded",function(){'
                    . 'if(!document.querySelector(".fi-simple-layout"))return;'
                    . 'var reduced=window.matchMedia("(prefers-reduced-motion:reduce)").matches;'
                    . 'var c=document.createElement("canvas");'
                    . 'c.setAttribute("aria-hidden","true");'
                    . 'c.style.cssText="position:fixed;top:0;left:0;pointer-events:none;z-index:0;";'
                    . 'document.body.prepend(c);'
                    . 'var mainEl=document.querySelector(".fi-simple-main");'
                    . 'if(mainEl){mainEl.style.cssText+=";position:relative;z-index:1;isolation:isolate;"}'
                    . 'var ctnEl=document.querySelector(".fi-simple-main-ctn");'
                    . 'if(ctnEl){ctnEl.style.cssText+=";position:relative;z-index:1;"}'
                    . 'var ctx=c.getContext("2d"),DPR=window.devicePixelRatio||1,G="111,84,29",nodes=[],W,H,raf;'
                    . 'function resize(){W=window.innerWidth;H=window.innerHeight;c.style.width=W+"px";c.style.height=H+"px";c.width=Math.round(W*DPR);c.height=Math.round(H*DPR);ctx.setTransform(DPR,0,0,DPR,0,0);}'
                    . 'function init(){resize();nodes=[];for(var i=0;i<38;i++)nodes.push({x:Math.random()*W,y:Math.random()*H,vx:(Math.random()-.5)*.22,vy:(Math.random()-.5)*.22,r:Math.random()*1.8+1});}'
                    . 'function frame(){if(!W||!H){raf=requestAnimationFrame(frame);return;}ctx.clearRect(0,0,W,H);for(var i=0;i<nodes.length;i++){var p=nodes[i];p.x+=p.vx;p.y+=p.vy;if(p.x<0||p.x>W)p.vx*=-1;if(p.y<0||p.y>H)p.vy*=-1;ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);ctx.fillStyle="rgba("+G+",.16)";ctx.fill();for(var j=i+1;j<nodes.length;j++){var q=nodes[j],dx=p.x-q.x,dy=p.y-q.y,d=Math.sqrt(dx*dx+dy*dy);if(d<160){ctx.beginPath();ctx.moveTo(p.x,p.y);ctx.lineTo(q.x,q.y);ctx.strokeStyle="rgba("+G+","+((1-d/160)*.22)+")";ctx.lineWidth=.5;ctx.stroke();}}}raf=requestAnimationFrame(frame);}'
                    . 'window.addEventListener("resize",resize,{passive:true});'
                    . 'init();if(!reduced)frame();'
                    . '});'
                    . '</script>'
                )
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                function (): HtmlString {
                    if (!config('services.google_login.enabled', false)) {
                        return new HtmlString('');
                    }
                    $url = url('/auth/google/redirect');
                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">'
                        . '<path fill="#FFC107" d="M43.6 20H24v8h11.3c-1.6 4.7-6.1 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.8 1.2 8 3l5.7-5.7C34 6.1 29.3 4 24 4 13 4 4 13 4 24s9 20 20 20 20-9 20-20c0-1.3-.1-2.7-.4-4z"/>'
                        . '<path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 15.1 19 12 24 12c3 0 5.8 1.2 8 3l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>'
                        . '<path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.5 35 26.9 36 24 36c-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z"/>'
                        . '<path fill="#1976D2" d="M43.6 20H24v8h11.3c-.7 2-2 3.8-3.6 5.2l6.2 5.2C41 35.2 44 30 44 24c0-1.3-.1-2.7-.4-4z"/>'
                        . '</svg>';
                    return $this->buildGoogleBlock($url, $svg);
                }
            )
            ->renderHook(
                PanelsRenderHook::AUTH_REGISTER_FORM_BEFORE,
                function (): HtmlString {
                    if (!config('services.google_login.enabled', false)) {
                        return new HtmlString('');
                    }
                    $url = url('/auth/google/redirect');
                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">'
                        . '<path fill="#FFC107" d="M43.6 20H24v8h11.3c-1.6 4.7-6.1 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.8 1.2 8 3l5.7-5.7C34 6.1 29.3 4 24 4 13 4 4 13 4 24s9 20 20 20 20-9 20-20c0-1.3-.1-2.7-.4-4z"/>'
                        . '<path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 15.1 19 12 24 12c3 0 5.8 1.2 8 3l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>'
                        . '<path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.5 35 26.9 36 24 36c-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z"/>'
                        . '<path fill="#1976D2" d="M43.6 20H24v8h11.3c-.7 2-2 3.8-3.6 5.2l6.2 5.2C41 35.2 44 30 44 24c0-1.3-.1-2.7-.4-4z"/>'
                        . '</svg>';
                    return $this->buildGoogleBlock($url, $svg, 'Continue with Google', 'Faster setup. Still subject to review.');
                }
            );

        if (File::exists(public_path('build/manifest.json'))) {
            $panel->viteTheme('resources/css/filament/admin/theme.css');
        }

        // MFA / TOTP — required for privileged staff, optional for others
        $panel->multiFactorAuthentication(
            providers: [
                AppAuthentication::make()
                    ->recoverable()
                    ->brandName('SEOAIco'),
            ],
            isRequired: fn() => auth()->user()?->isPrivilegedStaff() ?? false,
        );

        return $panel
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                SeoGrowthCommandCenter::class,
            ])
            ->navigationItems([
                NavigationItem::make('Bookings')
                    ->icon('heroicon-o-calendar-days')
                    ->url('/admin/bookings')
                    ->isActiveWhen(fn() => request()->is('admin/bookings*'))
                    ->group('Revenue')
                    ->sort(3),
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

    /**
     * Reusable Google OAuth block rendered above auth forms.
     * Shared by both the login and register page hooks.
     */
    private function buildGoogleBlock(
        string $url,
        string $svg,
        string $buttonLabel = 'Continue with Google',
        string $helperText = 'Faster. Verified. Secure.'
    ): HtmlString {
        $html = '<div style="display:flex;flex-direction:column;align-items:stretch;gap:0;margin-bottom:1.25rem;">';
        $html .= '<div style="text-align:center;margin-bottom:.75rem;">';
        $html .= '<span style="display:inline-block;padding:.22rem .75rem;border:1px solid rgba(111,84,29,.28);border-radius:20px;font-size:.67rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(168,120,40,.9);font-family:\'DM Sans\',sans-serif;font-weight:500;">&#10003;&nbsp;Recommended</span>';
        $html .= '</div>';

        $html .= '<a href="' . e($url) . '" '
            . 'style="display:flex;align-items:center;justify-content:center;gap:.65rem;padding:.88rem 1.1rem;background:#ffffff;border:1px solid #dadce0;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.18),0 0 0 1px rgba(0,0,0,.04);text-decoration:none;font-family:\'DM Sans\',ui-sans-serif,sans-serif;font-size:.92rem;font-weight:600;color:#111111;transition:box-shadow .2s ease,background .18s ease,transform .15s ease;" '
            . 'onmouseover="this.style.background=\'#f8f9fa\';this.style.boxShadow=\'0 4px 16px rgba(0,0,0,.22),0 0 0 1px rgba(0,0,0,.06)\';this.style.transform=\'translateY(-1px)\'" '
            . 'onmouseout="this.style.background=\'#ffffff\';this.style.boxShadow=\'0 2px 8px rgba(0,0,0,.18),0 0 0 1px rgba(0,0,0,.04)\';this.style.transform=\'translateY(0)\'">';
        $html .= $svg . '<span style="color:#111111;font-weight:600;">' . e($buttonLabel) . '</span>';
        $html .= '</a>';

        $html .= '<p style="text-align:center;font-size:.71rem;color:rgba(168,168,156,.85);margin:.55rem 0 .85rem;font-family:\'DM Sans\',sans-serif;letter-spacing:.02em;">' . e($helperText) . '</p>';

        $html .= '<div style="display:flex;align-items:center;gap:.75rem;">';
        $html .= '<div style="flex:1;height:1px;background:rgba(111,84,29,.25);"></div>';
        $html .= '<span style="font-size:.7rem;color:rgba(178,138,60,.9);letter-spacing:.11em;text-transform:uppercase;white-space:nowrap;font-family:\'DM Sans\',sans-serif;">or continue with email</span>';
        $html .= '<div style="flex:1;height:1px;background:rgba(111,84,29,.25);"></div>';
        $html .= '</div>';

        $html .= '</div>';
        return new HtmlString($html);
    }
}
