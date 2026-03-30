<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class Register extends BaseRegister
{
    // ─── Heading ──────────────────────────────────────────────────────────────

    public function getHeading(): string|Htmlable|null
    {
        return 'Create your account';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Access is curated. All accounts are reviewed before activation';
    }

    // ─── Form ─────────────────────────────────────────────────────────────────

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),

            Select::make('use_case')
                ->label('How will you use SEOAIco?')
                ->options([
                    'Agency'         => 'Agency',
                    'Local Business' => 'Local Business',
                    'Enterprise'     => 'Enterprise',
                    'Other'          => 'Other',
                ])
                ->native(false)
                ->placeholder('Select one')
                ->required(),

            TextInput::make('access_code')
                ->label('Access Code (optional)')
                ->password()
                ->revealable()
                ->maxLength(128)
                ->autocomplete('off'),
        ]);
    }

    // ─── CTA label ────────────────────────────────────────────────────────────

    public function getRegisterFormAction(): Action
    {
        return parent::getRegisterFormAction()->label('Request Access');
    }

    // ─── Registration handler (rate limit + pending redirect) ─────────────────

    public function register(): ?RegistrationResponse
    {
        // Per-IP rate limit (on top of Filament's per-email limit)
        $ipKey = 'register-ip:' . sha1(request()->ip());
        if (RateLimiter::tooManyAttempts($ipKey, 3)) {
            $seconds = RateLimiter::availableIn($ipKey);
            Notification::make()
                ->title('Too many registration attempts')
                ->body("Please wait {$seconds} seconds before trying again.")
                ->danger()
                ->send();
            return null;
        }
        RateLimiter::hit($ipKey, 600);

        // Run parent: validates form, creates user, logs them in
        $response = parent::register();

        // After login, check approval status
        $user = Filament::auth()->user();

        Log::info('registration', [
            'email'    => $user?->email,
            'ip'       => request()->ip(),
            'approved' => (bool) $user?->approved,
        ]);

        if (is_null($response)) {
            // Parent returned null — throttled or validation failed, handled upstream
            return null;
        }

        if ($user && ! $user->approved) {
            Filament::auth()->logout();
            session()->regenerateToken();
            $this->redirect(route('pending-approval'), navigate: false);
            return null;
        }

        // Approved (valid access code) — send directly to onboarding
        $this->redirect(route('user.onboarding'), navigate: false);
        return null;
    }

    // ─── Data mutation (access code gate, role, strip sensitive field) ─────────

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $accessCode = trim($data['access_code'] ?? '');
        $validCode  = config('services.registration.access_code');

        // Only grant approval if a non-empty valid code is supplied and matches
        $approved = $validCode !== null
            && $validCode !== ''
            && $accessCode !== ''
            && hash_equals((string) $validCode, $accessCode);

        $data['approved']        = $approved;
        $data['role']             = 'buyer';
        $data['signup_ip']        = request()->ip();
        $data['signup_user_agent'] = substr((string) request()->userAgent(), 0, 512);
        $data['signup_referrer']  = substr((string) request()->headers->get('referer', ''), 0, 512) ?: null;
        $data['signup_source']    = 'web-register';

        // Strip — never persisted to the database
        unset($data['access_code']);

        return $data;
    }
}
