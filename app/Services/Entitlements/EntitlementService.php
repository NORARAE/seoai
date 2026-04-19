<?php

namespace App\Services\Entitlements;

use App\Enums\SystemTier;
use App\Models\QuickScan;
use App\Models\User;
use App\Models\UserEntitlement;

class EntitlementService
{
    /**
     * Rank map for ordered access layers.
     */
    private const KEY_RANK = [
        UserEntitlement::SCAN => 1,
        UserEntitlement::SIGNAL => 2,
        UserEntitlement::LEVERAGE => 3,
        UserEntitlement::ACTIVATION => 4,
    ];

    private const RANK_KEYS = [
        1 => UserEntitlement::SCAN,
        2 => UserEntitlement::SIGNAL,
        3 => UserEntitlement::LEVERAGE,
        4 => UserEntitlement::ACTIVATION,
    ];

    public function issueForScan(QuickScan $scan): void
    {
        if (!$scan->user_id) {
            return;
        }

        $rank = max(0, min(4, $scan->upgradeTierRank()));

        $this->issueForRank(
            userId: (int) $scan->user_id,
            rank: $rank,
            sourceType: 'quick_scan',
            sourceId: (int) $scan->id,
            sourceRef: $scan->publicScanId(),
            metadata: [
                'scan_id' => $scan->id,
                'status' => $scan->status,
                'upgrade_plan' => $scan->upgrade_plan,
                'upgrade_status' => $scan->upgrade_status,
            ],
        );
    }

    public function issueForUserTier(User $user): void
    {
        $rank = max(0, min(4, $this->rankFromSystemTier($user->system_tier)));

        $this->issueForRank(
            userId: (int) $user->id,
            rank: $rank,
            sourceType: 'user_tier',
            sourceId: (int) $user->id,
            sourceRef: $user->system_tier?->value,
            metadata: [
                'system_tier' => $user->system_tier?->value,
                'system_tier_upgraded_at' => $user->system_tier_upgraded_at?->toIso8601String(),
            ],
        );
    }

    /**
     * Build dashboard-friendly access map.
     */
    public function accessMap(User $user, ?QuickScan $scan = null): array
    {
        $entitlementRank = (int) $user->entitlements()
            ->where('status', 'active')
            ->get(['entitlement_key'])
            ->map(fn($item) => self::KEY_RANK[$item->entitlement_key] ?? 0)
            ->max();

        $scanRank = $scan?->upgradeTierRank() ?? 0;
        $tierRank = $this->rankFromSystemTier($user->system_tier);

        $rank = max($entitlementRank, $scanRank, $tierRank);

        return [
            'rank' => $rank,
            'scan' => $rank >= 1,
            'signal' => $rank >= 2,
            'leverage' => $rank >= 3,
            'activation' => $rank >= 4,
        ];
    }

    private function issueForRank(
        int $userId,
        int $rank,
        string $sourceType,
        int $sourceId,
        ?string $sourceRef,
        array $metadata
    ): void {
        if ($rank < 1) {
            return;
        }

        foreach (self::RANK_KEYS as $requiredRank => $key) {
            if ($rank < $requiredRank) {
                break;
            }

            UserEntitlement::query()->updateOrCreate(
                [
                    'user_id' => $userId,
                    'entitlement_key' => $key,
                ],
                [
                    'status' => 'active',
                    'granted_at' => now(),
                    'last_seen_at' => now(),
                    'source_type' => $sourceType,
                    'source_id' => $sourceId,
                    'source_ref' => $sourceRef,
                    'metadata' => $metadata,
                ]
            );
        }
    }

    private function rankFromSystemTier(SystemTier|string|null $tier): int
    {
        if ($tier instanceof SystemTier) {
            return $tier->rank();
        }

        if (!is_string($tier) || trim($tier) === '') {
            return 0;
        }

        return SystemTier::tryFrom(trim($tier))?->rank() ?? 0;
    }
}
