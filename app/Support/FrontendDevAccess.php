<?php

namespace App\Support;

/**
 * Central configuration for the `frontend_dev` role access restrictions.
 *
 * To grant a frontend_dev user access to a new Filament resource or page:
 *   1. Add the fully-qualified class name to ALLOWED_CLASSES.
 *   2. Add the matching URL slug to ALLOWED_URL_PREFIXES.
 * No other files need to be edited.
 */
class FrontendDevAccess
{
    /**
     * Filament resource and page classes the frontend_dev role may access.
     *
     * @var class-string[]
     */
    public const ALLOWED_CLASSES = [
        \App\Filament\Pages\SeoGrowthCommandCenter::class,       // Main dashboard
        \App\Filament\Resources\SeoMarketingPageResource::class, // SEO marketing pages editor

        // ── Optional: uncomment to expand access ──────────────────────────
        // \App\Filament\Pages\HelpGuides::class,
        // \App\Filament\Resources\Sites\SiteResource::class,
    ];

    /**
     * URL path segments (relative to /admin/) that are permitted.
     *
     * The empty string '' represents /admin itself (the dashboard root).
     * Each entry must correspond to a class in ALLOWED_CLASSES.
     *
     * @var string[]
     */
    public const ALLOWED_URL_PREFIXES = [
        '',                     // /admin  (dashboard root)
        'seo-marketing-pages',  // SeoMarketingPageResource

        // ── Add matching slugs when expanding ALLOWED_CLASSES ──────────────
        // 'help-guides',
        // 'sites',
    ];

    /**
     * Whether the currently authenticated user is subject to frontend_dev restrictions.
     */
    public static function isRestricted(): bool
    {
        return auth()->check() && auth()->user()->isFrontendDev();
    }

    /**
     * Whether the given Filament class is accessible to a frontend_dev user.
     *
     * @param  class-string  $class
     */
    public static function allows(string $class): bool
    {
        return in_array($class, static::ALLOWED_CLASSES, true);
    }

    /**
     * Whether the given /admin/* path segment is accessible to a frontend_dev user.
     * Pass the first path segment after /admin/, e.g. 'seo-marketing-pages'.
     */
    public static function allowsPath(string $segment): bool
    {
        return in_array($segment, static::ALLOWED_URL_PREFIXES, true);
    }
}
