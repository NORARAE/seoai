<?php

namespace App\Filament\Widgets;

use App\Models\FunnelEvent;
use App\Models\QuickScan;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class FunnelVisibilityWidget extends Widget
{
    protected string $view = 'filament.widgets.funnel-visibility-widget';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        if (!Auth::user()?->canApproveUsers()) {
            return [
                'hasAccess' => false,
                'progressionRows' => [],
                'scoreBandRows' => [],
                'insights' => [],
            ];
        }

        $counts = FunnelEvent::query()
            ->selectRaw('event_name, count(*) as count')
            ->groupBy('event_name')
            ->pluck('count', 'event_name');

        $get = fn(string $event): int => (int) ($counts[$event] ?? 0);

        $stageCounts = [
            'homepage_cta' => $get(FunnelEvent::HOMEPAGE_CTA_CLICK),
            'scan_submit' => $get(FunnelEvent::SCAN_START_SUBMITTED),
            'scan_started' => $get(FunnelEvent::SCAN_STARTED),
            'scan_completed' => $get(FunnelEvent::SCAN_COMPLETED),
            'result_viewed' => $get(FunnelEvent::RESULT_PAGE_VIEWED),
            'upgrade_click' => $get(FunnelEvent::UPGRADE_CLICK),
            'checkout_entry' => $get(FunnelEvent::CHECKOUT_ENTRY),
            'payment_success' => $get(FunnelEvent::PAYMENT_SUCCESS),
        ];

        $progression = [
            ['label' => 'Homepage CTA', 'key' => 'homepage_cta'],
            ['label' => 'Scan Submit', 'key' => 'scan_submit'],
            ['label' => 'Scan Started', 'key' => 'scan_started'],
            ['label' => 'Scan Completed', 'key' => 'scan_completed'],
            ['label' => 'Result Viewed', 'key' => 'result_viewed'],
            ['label' => 'Upgrade Click', 'key' => 'upgrade_click'],
            ['label' => 'Checkout Entry', 'key' => 'checkout_entry'],
            ['label' => 'Payment Success', 'key' => 'payment_success'],
        ];

        $progressionRows = [];
        $largestLeak = [
            'from' => null,
            'to' => null,
            'drop' => 0,
            'rate' => 0.0,
        ];

        foreach ($progression as $index => $stage) {
            $count = $stageCounts[$stage['key']];
            $prevCount = $index > 0 ? $stageCounts[$progression[$index - 1]['key']] : 0;
            $dropCount = $index > 0 ? max($prevCount - $count, 0) : 0;
            $retention = $index > 0 && $prevCount > 0 ? round($count / $prevCount * 100, 1) : null;

            if ($index > 0) {
                $dropRate = $prevCount > 0 ? round($dropCount / $prevCount * 100, 1) : 0.0;
                if ($dropCount > $largestLeak['drop']) {
                    $largestLeak = [
                        'from' => $progression[$index - 1]['label'],
                        'to' => $stage['label'],
                        'drop' => $dropCount,
                        'rate' => $dropRate,
                    ];
                }
            }

            $progressionRows[] = [
                'stage' => $stage['label'],
                'count' => $count,
                'drop_count' => $dropCount,
                'retention' => $retention,
            ];
        }

        $scanIdBuckets = [
            'entered' => $this->scanIdsForEvent(FunnelEvent::RESULT_PAGE_VIEWED),
            'upgraded' => $this->scanIdsForEvent(FunnelEvent::UPGRADE_CLICK),
            'purchased' => array_values(array_unique(array_merge(
                $this->scanIdsForEvent(FunnelEvent::UPGRADE_PURCHASED),
                $this->scanIdsForEvent(FunnelEvent::PAYMENT_SUCCESS),
            ))),
        ];

        $allScanIds = array_values(array_unique(array_merge(
            $scanIdBuckets['entered'],
            $scanIdBuckets['upgraded'],
            $scanIdBuckets['purchased'],
        )));

        $scoresByScanId = empty($allScanIds)
            ? collect()
            : QuickScan::query()
                ->whereIn('id', $allScanIds)
                ->pluck('score', 'id');

        $scoreBands = ['low', 'mid', 'high'];
        $scoreBandRows = [];
        $worstBand = ['band' => null, 'stall_rate' => -1.0];
        $bestBand = ['band' => null, 'convert_rate' => -1.0];

        foreach ($scoreBands as $band) {
            $entered = $this->countBandScans($scanIdBuckets['entered'], $scoresByScanId, $band);
            $upgraded = $this->countBandScans($scanIdBuckets['upgraded'], $scoresByScanId, $band);
            $purchased = $this->countBandScans($scanIdBuckets['purchased'], $scoresByScanId, $band);

            $stalled = max($entered - $upgraded, 0);
            $dropped = max($upgraded - $purchased, 0);
            $stallRate = $entered > 0 ? round($stalled / $entered * 100, 1) : null;
            $convertRate = $entered > 0 ? round($purchased / $entered * 100, 1) : null;

            if ($stallRate !== null && $stallRate > $worstBand['stall_rate']) {
                $worstBand = ['band' => $band, 'stall_rate' => $stallRate];
            }

            if ($convertRate !== null && $convertRate > $bestBand['convert_rate']) {
                $bestBand = ['band' => $band, 'convert_rate' => $convertRate];
            }

            $scoreBandRows[] = [
                'band' => ucfirst($band),
                'entered' => $entered,
                'upgraded' => $upgraded,
                'stalled' => $stalled,
                'dropped' => $dropped,
                'converted' => $purchased,
                'stall_rate' => $stallRate,
                'convert_rate' => $convertRate,
            ];
        }

        $insights = [];

        if ($largestLeak['from'] !== null) {
            $insights[] = "Largest leak is {$largestLeak['from']} → {$largestLeak['to']} ({$largestLeak['drop']} users, {$largestLeak['rate']}% drop).";
        }

        if ($worstBand['band'] !== null) {
            $insights[] = ucfirst($worstBand['band']) . " score leads stall most often ({$worstBand['stall_rate']}% stall before upgrade click).";
        }

        if ($bestBand['band'] !== null) {
            $insights[] = ucfirst($bestBand['band']) . " score leads convert strongest ({$bestBand['convert_rate']}% from result view to payment).";
        }

        $insights[] = 'Checkout losses only include tracked cancellations/returns; off-site abandonments and tab closes remain partially invisible.';

        return [
            'hasAccess' => true,
            'progressionRows' => $progressionRows,
            'scoreBandRows' => $scoreBandRows,
            'insights' => $insights,
        ];
    }

    /**
     * @return array<int>
     */
    private function scanIdsForEvent(string $event): array
    {
        return FunnelEvent::query()
            ->where('event_name', $event)
            ->whereNotNull('scan_id')
            ->distinct()
            ->pluck('scan_id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();
    }

    private function countBandScans(array $scanIds, $scoresByScanId, string $band): int
    {
        if (empty($scanIds) || $scoresByScanId->isEmpty()) {
            return 0;
        }

        $count = 0;

        foreach ($scanIds as $scanId) {
            $score = (int) ($scoresByScanId[$scanId] ?? -1);

            if ($score < 0) {
                continue;
            }

            $scoreBand = $score >= 88 ? 'high' : ($score >= 60 ? 'mid' : 'low');

            if ($scoreBand === $band) {
                $count++;
            }
        }

        return $count;
    }
}