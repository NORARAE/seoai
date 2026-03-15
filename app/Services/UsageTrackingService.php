<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Subscription;
use App\Models\UsageRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * UsageTrackingService
 * 
 * Tracks resource consumption for SaaS billing and limits
 */
class UsageTrackingService
{
    /**
     * Track a usage event
     */
    public function track(
        Client $client,
        string $resourceType,
        int $quantity = 1,
        ?array $metadata = null
    ): UsageRecord {
        $subscription = $client->activeSubscription;
        
        $period = $this->getCurrentBillingPeriod($subscription);

        return UsageRecord::create([
            'client_id' => $client->id,
            'subscription_id' => $subscription?->id,
            'resource_type' => $resourceType,
            'quantity' => $quantity,
            'period_start' => $period['start'],
            'period_end' => $period['end'],
            'metadata' => $metadata,
        ]);
    }

    /**
     * Check if client has exceeded usage limits
     */
    public function checkLimit(Client $client, string $resourceType): array
    {
        $limit = $this->getLimit($client, $resourceType);
        $usage = $this->getCurrentUsage($client, $resourceType);

        return [
            'limit' => $limit,
            'used' => $usage,
            'remaining' => max(0, $limit - $usage),
            'exceeded' => $usage >= $limit,
            'percentage' => $limit > 0 ? round(($usage / $limit) * 100, 1) : 0,
        ];
    }

    /**
     * Get usage limit for a resource type
     */
    public function getLimit(Client $client, string $resourceType): int
    {
        return match($resourceType) {
            'site' => $client->max_sites,
            'page' => $client->max_pages,
            'ai_operation' => $client->max_ai_operations_per_month,
            default => 0,
        };
    }

    /**
     * Get current usage for resource type in current period
     */
    public function getCurrentUsage(Client $client, string $resourceType): int
    {
        $subscription = $client->activeSubscription;
        $period = $this->getCurrentBillingPeriod($subscription);

        // For site count, use actual count
        if ($resourceType === 'site') {
            return $client->sites()->count();
        }

        // For page count, use actual count
        if ($resourceType === 'page') {
            return DB::table('location_pages')
                ->whereIn('site_id', $client->sites()->pluck('id'))
                ->count();
        }

        // For metered resources, sum usage records
        return UsageRecord::where('client_id', $client->id)
            ->where('resource_type', $resourceType)
            ->where('period_start', '>=', $period['start'])
            ->where('period_end', '<=', $period['end'])
            ->sum('quantity');
    }

    /**
     * Get usage summary for all resource types
     */
    public function getUsageSummary(Client $client): array
    {
        return [
            'sites' => $this->checkLimit($client, 'site'),
            'pages' => $this->checkLimit($client, 'page'),
            'ai_operations' => $this->checkLimit($client, 'ai_operation'),
        ];
    }

    /**
     * Get current billing period
     */
    protected function getCurrentBillingPeriod(?Subscription $subscription): array
    {
        if (!$subscription) {
            // If no subscription, use calendar month
            return [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
            ];
        }

        // Calculate period based on subscription start date
        $billingDay = $subscription->starts_at->day;
        $today = now();

        if ($today->day >= $billingDay) {
            // Current cycle
            $start = Carbon::create($today->year, $today->month, $billingDay);
            $end = $start->copy()->addMonth();
        } else {
            // Previous cycle still active
            $start = Carbon::create($today->year, $today->month, $billingDay)->subMonth();
            $end = $start->copy()->addMonth();
        }

        return [
            'start' => $start->startOfDay(),
            'end' => $end->endOfDay(),
        ];
    }

    /**
     * Get historical usage data for charts
     */
    public function getHistoricalUsage(
        Client $client,
        string $resourceType,
        int $months = 6
    ): array {
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();

            $usage = UsageRecord::where('client_id', $client->id)
                ->where('resource_type', $resourceType)
                ->where('period_start', '>=', $start)
                ->where('period_end', '<=', $end)
                ->sum('quantity');

            $data[] = [
                'month' => $start->format('M Y'),
                'usage' => $usage,
            ];
        }

        return $data;
    }
}
