<?php

namespace App\Services;

use App\Models\BaselineSnapshot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * BaselineSnapshotService
 * 
 * Creates baseline snapshots before optimizations
 */
class BaselineSnapshotService
{
    protected PerformanceAggregationService $performanceService;

    public function __construct(PerformanceAggregationService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * Create a snapshot for a page
     */
    public function createSnapshot(Model $page): BaselineSnapshot
    {
        $performanceData = $this->performanceService->get30DaySummary($page);

        return BaselineSnapshot::createFromModel($page, $performanceData);
    }

    /**
     * Create snapshots for multiple pages
     */
    public function createBatchSnapshots(iterable $pages): array
    {
        $snapshots = [];

        foreach ($pages as $page) {
            try {
                $snapshots[] = $this->createSnapshot($page);
            } catch (\Exception $e) {
                Log::error('Failed to create snapshot', [
                    'page_type' => get_class($page),
                    'page_id' => $page->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $snapshots;
    }
}
