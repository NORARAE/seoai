<?php

namespace App\Jobs;

use App\Models\PageGenerationBatch;
use App\Models\SeoOpportunity;
use App\Services\PagePayloadGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePagePayloadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60]; // Retry after 10s, 30s, 60s
    public $timeout = 120; // 2 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $opportunityId,
        public ?int $batchId = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PagePayloadGeneratorService $generator): void
    {
        // Load opportunity and batch
        $opportunity = SeoOpportunity::findOrFail($this->opportunityId);
        $batch = $this->batchId ? PageGenerationBatch::find($this->batchId) : null;

        try {
            // Generate payload
            $payload = $generator->generateFromOpportunity($opportunity, $batch);

            // Link payload to opportunity
            $opportunity->update(['payload_id' => $payload->id]);

            // Update batch counts
            if ($batch) {
                $batch->incrementPayload();
                
                Log::channel('page-generation')->info('Payload generated', [
                    'batch_id' => $batch->id,
                    'opportunity_id' => $opportunity->id,
                    'payload_id' => $payload->id,
                    'title' => $payload->title,
                    'progress' => $batch->getPayloadProgressPercentage() . '%',
                ]);
            }

        } catch (\Exception $e) {
            // Update batch with failure
            if ($batch) {
                $batch->increment('failed_count');
            }

            Log::channel('page-generation')->error('Payload generation failed', [
                'batch_id' => $this->batchId,
                'opportunity_id' => $this->opportunityId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        $batch = $this->batchId ? PageGenerationBatch::find($this->batchId) : null;

        if ($batch) {
            $batch->increment('failed_count');
            
            // Add to failed items log
            $failedItems = $batch->failed_items ?? [];
            $failedItems[] = [
                'opportunity_id' => $this->opportunityId,
                'error' => $exception->getMessage(),
                'failed_at' => now()->toDateTimeString(),
            ];
            $batch->update(['failed_items' => $failedItems]);
        }

        Log::channel('page-generation')->error('Payload generation permanently failed', [
            'batch_id' => $this->batchId,
            'opportunity_id' => $this->opportunityId,
            'error' => $exception->getMessage(),
        ]);
    }
}
