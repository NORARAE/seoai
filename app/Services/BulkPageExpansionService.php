<?php

namespace App\Services;

use App\Jobs\GeneratePagePayloadJob;
use App\Jobs\PublishPagePayloadJob;
use App\Models\PageGenerationBatch;
use App\Models\PagePayload;
use App\Models\SeoOpportunity;
use App\Models\Site;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * BulkPageExpansionService
 * 
 * Orchestrates bulk page generation and publishing workflows.
 * This is the main entry point for creating batches of pages from SEO opportunities.
 * 
 * Responsibilities:
 * - Create batch records
 * - Select and validate opportunities
 * - Dispatch generation jobs
 * - Optionally dispatch publishing jobs
 * - Enforce batch size limits
 * - Prevent duplicates
 */
class BulkPageExpansionService
{
    public const MAX_BATCH_SIZE = 50;

    /**
     * Generate a batch of pages from top SEO opportunities
     * 
     * @param Site $site
     * @param array $options
     * @return PageGenerationBatch
     */
    public function generateBatch(Site $site, array $options = []): PageGenerationBatch
    {
        $options = array_merge([
            'count' => 20,
            'auto_publish' => false,
            'opportunity_source' => 'manual',
            'skip_existing' => true,
            'order_by' => 'priority_score',
            'order_direction' => 'desc',
            'filters' => [],
        ], $options);

        // Enforce max batch size
        $count = min($options['count'], self::MAX_BATCH_SIZE);

        // Start transaction
        return DB::transaction(function () use ($site, $options, $count) {
            // Create batch record
            $batch = PageGenerationBatch::create([
                'site_id' => $site->id,
                'client_id' => $site->client_id,
                'initiated_by_user_id' => Auth::id(),
                'name' => $options['name'] ?? "Batch " . now()->format('Y-m-d H:i'),
                'description' => $options['description'] ?? null,
                'status' => 'pending',
                'opportunity_source' => $options['opportunity_source'],
                'requested_count' => $count,
                'auto_publish' => $options['auto_publish'],
                'started_at' => now(),
            ]);

            // Select opportunities
            $opportunities = $this->selectOpportunities($site, $count, $options);

            $batch->update(['requested_count' => $opportunities->count()]);

            if ($opportunities->isEmpty()) {
                $batch->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'duration_seconds' => 0,
                    'error_summary' => 'No eligible opportunities found for this batch.',
                ]);

                return $batch->fresh();
            }

            // Dispatch generation jobs
            $this->dispatchGenerationJobs($opportunities, $batch, $options);

            // Update batch status
            $batch->update(['status' => 'processing']);

            // Log batch creation
            Log::channel('page-generation')->info('Batch created', [
                'batch_id' => $batch->id,
                'site_id' => $site->id,
                'requested_count' => $count,
                'opportunities_selected' => $opportunities->count(),
                'auto_publish' => $options['auto_publish'],
            ]);

            return $batch;
        });
    }

    /**
     * Select opportunities for batch generation
     * 
     * @param Site $site
     * @param int $count
     * @param array $options
     * @return Collection
     */
    protected function selectOpportunities(Site $site, int $count, array $options): Collection
    {
        $query = SeoOpportunity::where('site_id', $site->id)
            ->whereIn('status', ['identified', 'pending']); // Include both identified and pending

        // Apply filters
        if (!empty($options['filters']['opportunity_type'])) {
            $query->where('opportunity_type', $options['filters']['opportunity_type']);
        }

        if (!empty($options['filters']['service_id'])) {
            $query->where('service_id', $options['filters']['service_id']);
        }

        if (!empty($options['filters']['min_priority'])) {
            $query->where('priority_score', '>=', $options['filters']['min_priority']);
        }

        // Skip opportunities that already have payloads
        if ($options['skip_existing']) {
            $query->whereNull('payload_id');
        }

        // Order by priority
        $query->orderBy($options['order_by'], $options['order_direction']);

        // Limit to requested count
        $opportunities = $query->limit($count)->get();

        // Validate selected opportunities
        $opportunities = $this->validateOpportunities($opportunities, $site);

        return $opportunities;
    }

    /**
     * Validate opportunities before generation
     * 
     * @param Collection $opportunities
     * @param Site $site
     * @return Collection
     */
    protected function validateOpportunities(Collection $opportunities, Site $site): Collection
    {
        return $opportunities->filter(function ($opportunity) use ($site) {
            // Ensure service is active
            if (!$opportunity->service || !$opportunity->service->is_active) {
                Log::warning('Skipping opportunity with inactive service', [
                    'opportunity_id' => $opportunity->id,
                    'service_id' => $opportunity->service_id,
                ]);
                return false;
            }

            // Ensure location exists
            if (!$opportunity->location_id) {
                Log::warning('Skipping opportunity without location', [
                    'opportunity_id' => $opportunity->id,
                ]);
                return false;
            }

            // Check for existing payload with same slug
            $slug = $this->generateSlug($opportunity);
            $existing = PagePayload::where('site_id', $site->id)
                ->where('slug', $slug)
                ->exists();

            if ($existing) {
                Log::info('Skipping opportunity with existing payload', [
                    'opportunity_id' => $opportunity->id,
                    'slug' => $slug,
                ]);
                return false;
            }

            return true;
        });
    }

    /**
     * Generate slug for opportunity (for duplicate checking)
     */
    protected function generateSlug(SeoOpportunity $opportunity): string
    {
        $serviceName = Str::slug($opportunity->service->name);
        $locationName = Str::slug($opportunity->city->name ?? 'location');
        $stateCode = strtolower($opportunity->city->state->code ?? 'us');
        
        return "{$serviceName}-{$locationName}-{$stateCode}";
    }

    /**
     * Dispatch payload generation jobs
     * 
     * @param Collection $opportunities
     * @param PageGenerationBatch $batch
     * @param array $options
     * @return void
     */
    protected function dispatchGenerationJobs(Collection $opportunities, PageGenerationBatch $batch, array $options): void
    {
        foreach ($opportunities as $opportunity) {
            // Dispatch generation job
            $job = new GeneratePagePayloadJob($opportunity->id, $batch->id);
            
            // Chain publishing job if auto_publish is enabled
            if ($options['auto_publish'] && $batch->site->publishing_mode === 'native') {
                $job->chain([
                    function () use ($opportunity) {
                        // Get the created payload and dispatch publish job
                        $payload = PagePayload::where('site_id', $opportunity->site_id)
                            ->where('slug', $this->generateSlug($opportunity))
                            ->first();
                        
                        if ($payload && $payload->isReadyToPublish()) {
                            dispatch(new PublishPagePayloadJob($payload->id))
                                ->onQueue('publishing')
                                ->delay(now()->addSeconds(5)); // Small delay to avoid rate limits
                        }
                    },
                ]);
            }
            
            dispatch($job)->onQueue('generation');
        }

        Log::channel('page-generation')->info('Generation jobs dispatched', [
            'batch_id' => $batch->id,
            'jobs_count' => $opportunities->count(),
            'auto_publish' => $options['auto_publish'],
        ]);
    }

    /**
     * Publish all unpublished payloads in a batch
     * 
     * @param PageGenerationBatch $batch
     * @param bool $force Force republish already published payloads
     * @return int Number of jobs dispatched
     */
    public function publishBatch(PageGenerationBatch $batch, bool $force = false): int
    {
        $query = $batch->payloads()->where('status', 'approved');
        
        if (!$force) {
            $query->where('publish_status', 'pending');
        }
        
        $payloads = $query->get();

        $dispatched = 0;

        foreach ($payloads as $payload) {
            if ($force || $payload->isReadyToPublish()) {
                dispatch(new PublishPagePayloadJob($payload->id))
                    ->onQueue('publishing')
                    ->delay(now()->addSeconds($dispatched * 10)); // Stagger by 10s to avoid rate limits
                
                $dispatched++;
            }
        }

        Log::channel('publishing')->info('Batch publishing initiated', [
            'batch_id' => $batch->id,
            'payloads_queued' => $dispatched,
            'force' => $force,
        ]);

        return $dispatched;
    }

    /**
     * Export all payloads in a batch
     * 
     * @param PageGenerationBatch $batch
     * @param string $format
     * @return string Path to export file
     */
    public function exportBatch(PageGenerationBatch $batch, string $format = 'json'): string
    {
        $payloads = $batch->payloads;

        // Use PublishingService for export
        $publishingService = app(PublishingService::class);
        $exportPath = $publishingService->exportBatch($payloads, $format);

        // Update batch with export info
        $batch->update([
            'export_path' => $exportPath,
            'export_format' => $format,
            'exported_count' => $payloads->count(),
        ]);

        Log::channel('page-generation')->info('Batch exported', [
            'batch_id' => $batch->id,
            'format' => $format,
            'payload_count' => $payloads->count(),
            'export_path' => $exportPath,
        ]);

        return $exportPath;
    }

    /**
     * Get batch progress summary
     * 
     * @param PageGenerationBatch $batch
     * @return array
     */
    public function getBatchProgress(PageGenerationBatch $batch): array
    {
        return [
            'batch_id' => $batch->id,
            'status' => $batch->status,
            'requested_count' => $batch->requested_count,
            'payload_count' => $batch->payload_count,
            'published_count' => $batch->published_count,
            'exported_count' => $batch->exported_count,
            'failed_count' => $batch->failed_count,
            'payload_progress' => $batch->getPayloadProgressPercentage(),
            'publishing_progress' => $batch->getPublishingProgressPercentage(),
            'duration_seconds' => $batch->duration_seconds,
            'is_complete' => $batch->status === 'completed',
        ];
    }

    /**
     * Mark batch as completed
     * 
     * @param PageGenerationBatch $batch
     * @return void
     */
    public function completeBatch(PageGenerationBatch $batch): void
    {
        $batch->update([
            'status' => 'completed',
            'completed_at' => now(),
            'duration_seconds' => now()->diffInSeconds($batch->started_at),
        ]);

        Log::channel('page-generation')->info('Batch completed', [
            'batch_id' => $batch->id,
            'duration_seconds' => $batch->duration_seconds,
            'stats' => $this->getBatchProgress($batch),
        ]);
    }

    /**
     * Cancel a batch (stop dispatching new jobs)
     * 
     * @param PageGenerationBatch $batch
     * @return void
     */
    public function cancelBatch(PageGenerationBatch $batch): void
    {
        $batch->update([
            'status' => 'failed',
            'error_summary' => 'Batch cancelled by user',
            'completed_at' => now(),
            'duration_seconds' => now()->diffInSeconds($batch->started_at),
        ]);

        Log::channel('page-generation')->warning('Batch cancelled', [
            'batch_id' => $batch->id,
            'payload_count' => $batch->payload_count,
            'published_count' => $batch->published_count,
        ]);
    }
}
