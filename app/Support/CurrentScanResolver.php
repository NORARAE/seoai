<?php

namespace App\Support;

use App\Models\ScanRun;
use App\Models\Site;
use App\Models\User;

class CurrentScanResolver
{
    /** @var array<string, \Illuminate\Support\Collection<int, Site>> */
    protected static array $accessibleSitesCache = [];

    /** @var array<string, Site|null> */
    protected static array $siteCache = [];

    /** @var array<string, ScanRun|null> */
    protected static array $completedScanCache = [];

    /** @var array<string, ScanRun|null> */
    protected static array $activeScanCache = [];

    /** @var array<string, ScanRun|null> */
    protected static array $explicitScanCache = [];

    /**
     * Resolve the dashboard scan for a user: the latest completed run for the active site.
     */
    public static function resolveForUser(?User $user, ?int $explicitSiteId = null, ?int $explicitScanRunId = null): ?ScanRun
    {
        if ($explicitScanRunId) {
            return static::resolveExplicitForUser($user, $explicitScanRunId, $explicitSiteId);
        }

        $site = static::resolveSiteForUser($user, $explicitSiteId);

        if (! $site) {
            return null;
        }

        return static::resolveForSite($site);
    }

    public static function resolveIdForUser(?User $user, ?int $explicitSiteId = null, ?int $explicitScanRunId = null): ?int
    {
        return static::resolveForUser($user, $explicitSiteId, $explicitScanRunId)?->id;
    }

    public static function dtoForUser(?User $user, ?int $explicitSiteId = null, ?int $explicitScanRunId = null): ScanContext
    {
        return ScanContext::forUser($user, $explicitSiteId, $explicitScanRunId);
    }

    public static function resolveForSite(Site $site): ?ScanRun
    {
        $cacheKey = "site:{$site->id}:completed";

        if (array_key_exists($cacheKey, static::$completedScanCache)) {
            return static::$completedScanCache[$cacheKey];
        }

        return static::$completedScanCache[$cacheKey] = ScanRun::query()
            ->where('site_id', $site->id)
            ->where('status', 'completed')
            ->orderByDesc('completed_at')
            ->orderByDesc('id')
            ->first();
    }

    public static function resolveActiveForUser(?User $user, ?int $explicitSiteId = null): ?ScanRun
    {
        $site = static::resolveSiteForUser($user, $explicitSiteId);

        return $site ? static::resolveActiveForSite($site) : null;
    }

    public static function resolveActiveForSite(Site $site): ?ScanRun
    {
        $cacheKey = "site:{$site->id}:active";

        if (array_key_exists($cacheKey, static::$activeScanCache)) {
            return static::$activeScanCache[$cacheKey];
        }

        return static::$activeScanCache[$cacheKey] = ScanRun::query()
            ->where('site_id', $site->id)
            ->whereIn('status', ['running', 'pending'])
            ->orderByRaw("case when status = 'running' then 0 when status = 'pending' then 1 else 2 end")
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * @return array{site:?Site,scan_run:?ScanRun,active_scan:?ScanRun,state:string,label:string,tone:string,description:string}
     */
    public static function contextForUser(?User $user, ?int $explicitSiteId = null, ?int $explicitScanRunId = null): array
    {
        $site = static::resolveSiteForUser($user, $explicitSiteId);

        if (! $site) {
            return [
                'site' => null,
                'scan_run' => null,
                'active_scan' => null,
                'state' => 'no_scan',
                'label' => 'No scan',
                'tone' => 'gray',
                'description' => 'Select an active site to view scan-scoped discovery metrics.',
            ];
        }

        $scanRun = static::resolveForUser($user, $explicitSiteId, $explicitScanRunId);
        $activeScan = static::resolveActiveForSite($site);
        $latestAttempt = static::latestAttemptForSite($site);

        if ($activeScan) {
            return [
                'site' => $site,
                'scan_run' => $scanRun,
                'active_scan' => $activeScan,
                'state' => 'scanning',
                'label' => 'Scanning',
                'tone' => 'info',
                'description' => $scanRun
                    ? "Live discovery is in progress. Command Center metrics remain pinned to completed scan #{$scanRun->id}."
                    : 'The first discovery run is in progress. Metrics will populate once a scan completes.',
            ];
        }

        if (! $scanRun) {
            return [
                'site' => $site,
                'scan_run' => null,
                'active_scan' => null,
                'state' => 'no_scan',
                'label' => 'No scan',
                'tone' => 'gray',
                'description' => "No completed scan is available yet for {$site->domain}.",
            ];
        }

        if ($latestAttempt && in_array($latestAttempt->status, ['failed', 'cancelled'], true) && $latestAttempt->id !== $scanRun->id) {
            return [
                'site' => $site,
                'scan_run' => $scanRun,
                'active_scan' => null,
                'state' => 'incomplete',
                'label' => 'Incomplete',
                'tone' => 'warning',
                'description' => "The newest discovery attempt did not complete. Metrics are showing completed scan #{$scanRun->id}.",
            ];
        }

        if ($scanRun->completed_at && $scanRun->completed_at->lt(now()->subDays(7))) {
            return [
                'site' => $site,
                'scan_run' => $scanRun,
                'active_scan' => null,
                'state' => 'stale',
                'label' => 'Stale',
                'tone' => 'warning',
                'description' => "Current scan #{$scanRun->id} completed more than 7 days ago.",
            ];
        }

        return [
            'site' => $site,
            'scan_run' => $scanRun,
            'active_scan' => null,
            'state' => 'complete',
            'label' => 'Complete',
            'tone' => 'success',
            'description' => "Showing completed scan #{$scanRun->id} for {$site->domain}.",
        ];
    }

    public static function indicatorForUser(?User $user, ?int $explicitSiteId = null, ?int $explicitScanRunId = null): ?string
    {
        $context = static::contextForUser($user, $explicitSiteId, $explicitScanRunId);
        $site = $context['site'];
        $scanRun = $context['scan_run'];

        if (! $site) {
            return 'No active site selected';
        }

        if (! $scanRun) {
            return "{$context['label']}: {$site->domain}";
        }

        return "{$context['label']}: scan #{$scanRun->id} for {$site->domain}";
    }

    public static function resolveSiteForUser(?User $user, ?int $explicitSiteId = null): ?Site
    {
        $cacheKey = static::siteCacheKey($user, $explicitSiteId);

        if (array_key_exists($cacheKey, static::$siteCache)) {
            return static::$siteCache[$cacheKey];
        }

        if ($explicitSiteId) {
            return static::$siteCache[$cacheKey] = static::accessibleSitesForUser($user)->firstWhere('id', $explicitSiteId);
        }

        return static::$siteCache[$cacheKey] = ActiveSiteContext::resolveForUser($user);
    }

    public static function flushCache(): void
    {
        static::$accessibleSitesCache = [];
        static::$siteCache = [];
        static::$completedScanCache = [];
        static::$activeScanCache = [];
        static::$explicitScanCache = [];
    }

    protected static function resolveExplicitForUser(?User $user, int $scanRunId, ?int $explicitSiteId = null): ?ScanRun
    {
        $site = static::resolveSiteForUser($user, $explicitSiteId);

        if (! $site) {
            return null;
        }

        $cacheKey = "site:{$site->id}:scan:{$scanRunId}";

        if (array_key_exists($cacheKey, static::$explicitScanCache)) {
            return static::$explicitScanCache[$cacheKey];
        }

        return static::$explicitScanCache[$cacheKey] = ScanRun::query()
            ->whereKey($scanRunId)
            ->where('site_id', $site->id)
            ->first();
    }

    protected static function latestAttemptForSite(Site $site): ?ScanRun
    {
        $cacheKey = "site:{$site->id}:latest-attempt";

        if (array_key_exists($cacheKey, static::$explicitScanCache)) {
            return static::$explicitScanCache[$cacheKey];
        }

        return static::$explicitScanCache[$cacheKey] = ScanRun::query()
            ->where('site_id', $site->id)
            ->orderByRaw('coalesce(started_at, created_at) desc')
            ->orderByDesc('id')
            ->first();
    }

    /** @return \Illuminate\Support\Collection<int, Site> */
    protected static function accessibleSitesForUser(?User $user)
    {
        $cacheKey = $user?->getAuthIdentifier() ? "user:{$user->getAuthIdentifier()}" : 'guest';

        if (! array_key_exists($cacheKey, static::$accessibleSitesCache)) {
            static::$accessibleSitesCache[$cacheKey] = ActiveSiteContext::accessibleSitesForUser($user);
        }

        return static::$accessibleSitesCache[$cacheKey];
    }

    protected static function siteCacheKey(?User $user, ?int $explicitSiteId = null): string
    {
        $userKey = $user?->getAuthIdentifier() ? "user:{$user->getAuthIdentifier()}" : 'guest';

        return $explicitSiteId ? "{$userKey}:site:{$explicitSiteId}" : "{$userKey}:active";
    }
}