<?php

namespace App\Filament\Concerns;

use App\Support\FrontendDevAccess;

/**
 * Apply this trait to any Filament Resource or Page class to enable
 * deny-by-default frontend_dev role restriction.
 *
 * Behaviour:
 *  - `shouldRegisterNavigation()` — hides item from sidebar for restricted users.
 *  - `canViewAny()`               — blocks resource list/index for restricted users
 *                                   (only used when the class has no own override).
 *  - `canAccess()`                — blocks page rendering for restricted users
 *                                   (used by Filament Pages).
 *
 * Admins and all other roles are unaffected — the parent method is called
 * and their existing access logic is preserved.
 */
trait FrontendDevRestricted
{
    /**
     * Hide from sidebar navigation for restricted users who are not allowed here.
     */
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        if (FrontendDevAccess::isRestricted() && !FrontendDevAccess::allows(static::class)) {
            return false;
        }

        return parent::shouldRegisterNavigation($parameters);
    }

    /**
     * Block resource list/index access for restricted users.
     * Only effective when the resource class does not define its own canViewAny().
     */
    public static function canViewAny(): bool
    {
        if (FrontendDevAccess::isRestricted()) {
            return FrontendDevAccess::allows(static::class);
        }

        return parent::canViewAny();
    }

    /**
     * Block page rendering for restricted users.
     * Used by Filament Page classes (Dashboard, custom pages, etc.).
     */
    public static function canAccess(): bool
    {
        if (FrontendDevAccess::isRestricted()) {
            return FrontendDevAccess::allows(static::class);
        }

        return parent::canAccess();
    }
}
