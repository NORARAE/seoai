<?php

namespace App\Services\Discovery;

use App\Jobs\GeneratePagePayloadJob;
use App\Models\PageGenerationBatch;
use App\Models\SeoOpportunity;
use App\Models\Site;
use App\Models\UrlInventory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DiscoveryOpportunityBridgeService
{
    public function __construct(
        protected OpportunityDetectionService $detectionService,
    ) {}

    /**
     * Full bridge pipeline:
     *  1. Sync coverage flags from url_inventory.
     *  2. Run opportunity detection.
     *  3. Dispatch payload generation for newly approved opportunities.
     *
     * @return array{updated: int, covered: int, missing: int, detected: array, dispatched: int}
     */
    public function run(Site $site): array
    {
        $coverage = $this->syncCoverage($site);
        $detected = $this->detectionService->detect($site);
        $dispatched = $this->dispatchApprovedOpportunities($site);

        return array_merge($coverage, ['detected' => $detected, 'dispatched' => $dispatched]);
    }

    /**
     * Update opportunity coverage flags from discovered URL inventory.
     *
     * @return array{updated: int, covered: int, missing: int}
     */
    public function syncCoverage(Site $site): array
    {
        $paths = UrlInventory::where('site_id', $site->id)
            ->pluck('path')
            ->filter()
            ->map(fn (string $path) => trim(strtolower($path), '/'))
            ->values();

        $updated = 0;
        $covered = 0;
        $missing = 0;

        SeoOpportunity::with(['service', 'location.state'])
            ->where('site_id', $site->id)
            ->chunkById(200, function ($opportunities) use ($paths, &$updated, &$covered, &$missing): void {
                foreach ($opportunities as $opportunity) {
                    /** @var SeoOpportunity $opportunity */
                    if (! $opportunity->service || ! $opportunity->location) {
                        continue;
                    }

                    $slug = Str::slug($opportunity->service->name)
                        . '-'
                        . Str::slug($opportunity->location->name)
                        . '-'
                        . strtolower($opportunity->location->state->code ?? 'us');

                    $exists = $paths->contains(fn (string $path) => str_contains($path, $slug));

                    $opportunity->update(['page_exists' => $exists]);
                    $updated++;

                    if ($exists) {
                        $covered++;
                    } else {
                        $missing++;
                    }
                }
            });

        return [
            'updated' => $updated,
            'covered' => $covered,
            'missing' => $missing,
        ];
    }

    /**
     * Find approved opportunities that don't yet have a payload and dispatch
     * GeneratePagePayloadJob for each one.  A shared batch is created so
     * progress can be tracked in the editorial workflow.
     */
    public function dispatchApprovedOpportunities(Site $site): int
    {
        $opportunities = SeoOpportunity::where('site_id', $site->id)
            ->where('status', 'approved')
            ->whereNull('payload_id')
            ->get();

        if ($opportunities->isEmpty()) {
            return 0;
        }

        // Create a generation batch to group all dispatched payloads.
        $batch = PageGenerationBatch::create([
            'site_id'          => $site->id,
            'client_id'        => $site->client_id,
            'name'             => 'Discovery – ' . now()->toDateString(),
            'status'           => 'processing',
            'requested_count'  => $opportunities->count(),
        ]);

        foreach ($opportunities as $opportunity) {
            /** @var SeoOpportunity $opportunity */
            // Mark in_progress so we don't re-dispatch on next run.
            $opportunity->update(['status' => 'in_progress']);

            GeneratePagePayloadJob::dispatch($opportunity->id, $batch->id)
                ->onQueue('generation');
        }

        Log::info('DiscoveryOpportunityBridgeService: dispatched payload jobs', [
            'site_id'    => $site->id,
            'batch_id'   => $batch->id,
            'dispatched' => $opportunities->count(),
        ]);

        return $opportunities->count();
    }
}

