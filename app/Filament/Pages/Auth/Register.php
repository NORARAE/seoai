<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class Register extends BaseRegister
{
    // ─── Heading ──────────────────────────────────────────────────────────────

    public function getHeading(): string|Htmlable|null
    {
        return 'Apply for early access';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Operator positions are limited and reviewed individually. Early applicants receive priority.';
    }

    // ─── Form components (autocomplete + conversational copy) ─────────────────

    protected function getNameFormComponent(): Component
    {
        return parent::getNameFormComponent()
            ->label('Full name')
            ->placeholder('Jane Smith')
            ->autocomplete('name');
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->label('Work email')
            ->placeholder('you@yourcompany.com')
            ->autocomplete('email');
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->label('Create a password')
            ->autocomplete('new-password');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return parent::getPasswordConfirmationFormComponent()
            ->label('Confirm password')
            ->autocomplete('new-password');
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
                ->label("I'm joining as a...")
                ->options([
                    'Agency'         => 'Agency owner or team',
                    'Local Business' => 'Local business owner',
                    'Enterprise'     => 'Enterprise / in-house team',
                    'Other'          => 'Something else',
                ])
                ->native(false)
                ->placeholder('Choose your path')
                ->required(),

            TextInput::make('access_code')
                ->label('Invite code')
                ->placeholder('Have one? Paste it here to skip the queue.')
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

        $data['approved']          = $approved;
        $data['role']              = 'buyer';
        $data['signup_ip']         = request()->ip();
        $data['signup_user_agent'] = substr((string) request()->userAgent(), 0, 512);
        $data['signup_referrer']   = substr((string) request()->headers->get('referer', ''), 0, 512) ?: null;
        $data['signup_source']     = 'web-register';

        // Timezone — set by JS cookie on first page load
        $rawTz = request()->cookie('tz', '');
        $data['signup_timezone'] = preg_match('/^[A-Za-z0-9_\/\-+]{1,64}$/', (string) $rawTz)
            ? $rawTz
            : null;

        // UTM parameters — stored in session by CaptureUtmParameters middleware
        $utms = session('utm', []);
        $data['signup_utm'] = ! empty($utms) ? json_encode($utms) : null;

        // Strip — never persisted to the database
        unset($data['access_code']);

        return $data;
    }
}
