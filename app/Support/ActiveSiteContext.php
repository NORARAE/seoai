<?php

namespace App\Support;

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class ActiveSiteContext
{
    public static function resolveClientIdForUser(?User $user): ?int
    {
        if (! $user) {
            return null;
        }

        if ($user->client_id) {
            return (int) $user->client_id;
        }

        $assignedSiteClientIds = $user->sites()
            ->whereNotNull('client_id')
            ->pluck('client_id')
            ->unique()
            ->filter()
            ->values();

        if ($assignedSiteClientIds->count() === 1) {
            return (int) $assignedSiteClientIds->first();
        }

        $activeClientIds = Site::query()
            ->whereNotNull('client_id')
            ->pluck('client_id')
            ->unique()
            ->filter()
            ->values();

        if ($activeClientIds->count() === 1) {
            return (int) $activeClientIds->first();
        }

        return null;
    }

    public static function accessibleSitesForUser(?User $user): Collection
    {
        if (! $user) {
            return collect();
        }

        if (method_exists($user, 'accessibleSites')) {
            return $user->accessibleSites();
        }

        $query = Site::query()->orderBy('domain');

        if ($user?->client_id) {
            $query->where('client_id', $user->client_id);
        } elseif ($resolvedClientId = static::resolveClientIdForUser($user)) {
            $query->where('client_id', $resolvedClientId);
        }

        return $query->get();
    }

    public static function syncUserToSite(?User $user, Site $site): void
    {
        if (! $user) {
            return;
        }

        if ($site->client_id && ! $user->client_id) {
            $user->forceFill(['client_id' => $site->client_id])->save();
        }

        if (! $user->sites()->where('site_id', $site->id)->exists()) {
            $user->sites()->attach($site->id);
        }
    }

    public static function resolveForUser(?User $user): ?Site
    {
        $sites = static::accessibleSitesForUser($user);

        if ($sites->isEmpty()) {
            return null;
        }

        $selectedSiteId = (int) Session::get('active_site_id');

        if ($selectedSiteId > 0) {
            $selected = $sites->firstWhere('id', $selectedSiteId);

            if ($selected) {
                return $selected;
            }
        }

        $fallback = $sites->first();

        if ($fallback) {
            Session::put('active_site_id', $fallback->id);
        }

        return $fallback;
    }

    public static function setActiveSiteId(int $siteId): void
    {
        Session::put('active_site_id', $siteId);
    }
}
