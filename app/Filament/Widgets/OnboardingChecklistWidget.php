<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class OnboardingChecklistWidget extends Widget
{
    protected string $view = 'filament.widgets.onboarding-checklist-widget';

    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user !== null && $user->onboarding_completed_at === null;
    }

    public function dismissOnboarding(): void
    {
        $userId = Auth::id();

        if (! $userId) {
            return;
        }

        User::whereKey($userId)->update([
            'onboarding_completed_at' => now(),
        ]);

        Notification::make()
            ->title('Onboarding dismissed')
            ->body('You can access Help & Guides anytime from the command center.')
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }
}
