<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * TenantService
 * 
 * Manages tenant (client) creation, user provisioning, and isolation
 */
class TenantService
{
    /**
     * Create a new tenant with owner user
     */
    public function createTenant(array $data): Client
    {
        $client = Client::create([
            'name' => $data['name'],
            'company_name' => $data['company_name'] ?? $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => 'active',
            'subdomain' => $data['subdomain'] ?? Str::slug($data['name']),
            'domain' => $data['domain'] ?? null,
            'timezone' => $data['timezone'] ?? 'UTC',
            'trial_ends_at' => now()->addDays(14), // 14-day trial
            'max_sites' => $data['max_sites'] ?? 1,
            'max_pages' => $data['max_pages'] ?? 100,
            'max_ai_operations_per_month' => $data['max_ai_operations_per_month'] ?? 50,
        ]);

        // Create owner user
        $owner = $this->createUser($client, [
            'name' => $data['owner_name'] ?? $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ?? Str::random(16),
            'role' => 'owner',
        ]);

        return $client;
    }

    /**
     * Create a user within a tenant
     */
    public function createUser(Client $client, array $data): User
    {
        $user = User::create([
            'client_id' => $client->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'member',
            'is_active' => true,
        ]);

        // Assign default role if exists
        $this->assignDefaultRole($user);

        return $user;
    }

    /**
     * Assign default role to user
     */
    protected function assignDefaultRole(User $user): void
    {
        $defaultRole = Role::where('client_id', $user->client_id)
            ->where('is_default', true)
            ->first();

        if ($defaultRole) {
            $user->roles()->attach($defaultRole->id);
        }
    }

    /**
     * Check if subdomain is available
     */
    public function isSubdomainAvailable(string $subdomain): bool
    {
        return !Client::where('subdomain', $subdomain)->exists();
    }

    /**
     * Suspend a tenant
     */
    public function suspendTenant(Client $client, string $reason = null): bool
    {
        return $client->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'notes' => $reason ? "Suspended: $reason" : $client->notes,
        ]);
    }

    /**
     * Reactivate a tenant
     */
    public function reactivateTenant(Client $client): bool
    {
        return $client->update([
            'status' => 'active',
            'suspended_at' => null,
        ]);
    }

    /**
     * Check if tenant is within limits
     */
    public function checkTenantLimits(Client $client): array
    {
        $usageService = app(UsageTrackingService::class);

        return [
            'within_limits' => !$this->hasExceededLimits($client),
            'limits' => $usageService->getUsageSummary($client),
        ];
    }

    /**
     * Check if tenant has exceeded any limits
     */
    public function hasExceededLimits(Client $client): bool
    {
        $usageService = app(UsageTrackingService::class);
        $summary = $usageService->getUsageSummary($client);

        foreach ($summary as $resource) {
            if ($resource['exceeded']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get tenant statistics
     */
    public function getTenantStats(Client $client): array
    {
        return [
            'sites_count' => $client->sites()->count(),
            'users_count' => $client->users()->count(),
            'location_pages_count' => \DB::table('location_pages')
                ->whereIn('site_id', $client->sites()->pluck('id'))
                ->count(),
            'opportunities_count' => \DB::table('opportunities')
                ->whereIn('site_id', $client->sites()->pluck('id'))
                ->where('status', 'open')
                ->count(),
            'trial_days_remaining' => $client->trial_ends_at 
                ? max(0, now()->diffInDays($client->trial_ends_at, false))
                : null,
        ];
    }
}
