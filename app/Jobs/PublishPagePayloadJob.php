<?php

namespace App\Jobs;

use App\Models\PageGenerationBatch;
use App\Models\PagePayload;
use App\Models\PublishingLog;
use App\Services\PublishingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishPagePayloadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 120, 300]; // Retry after 30s, 2m, 5m
    public $timeout = 300; // 5 minutes (WordPress can be slow)

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $payloadId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PublishingService $publishingService): void
    {
        // Load payload with relationships
        $payload = PagePayload::with(['site', 'batch'])->findOrFail($this->payloadId);
        $site = $payload->site;
        $batch = $payload->batch;

        try {
            // Publish via adapter
            $result = $publishingService->publish($payload);

            if ($result->success) {
                // Update payload status
                if ($result->remoteId) {
                    $payload->markAsPublished($result->remoteId, $result->remoteUrl, $result->remoteEditUrl);
                } else {
                    $payload->markAsExported();
                }

                // Update batch counts
                if ($batch) {
                    if ($result->remoteId) {
                        $batch->incrementPublished();
                    } else {
                        $batch->increment('exported_count');
                    }
                }

                // Log publishing
                PublishingLog::create([
                    'payload_id' => $payload->id,
                    'site_id' => $site->id,
                    'client_id' => $site->client_id,
                    'adapter_type' => $site->publishing_mode,
                    'action' => 'publish',
                    'result' => 'success',
                    'remote_id' => $result->remoteId,
                    'remote_url' => $result->remoteUrl,
                    'remote_response' => $result->rawResponse,
                ]);

                Log::channel('publishing')->info('Payload published successfully', [
                    'payload_id' => $payload->id,
                    'site_id' => $site->id,
                    'adapter' => $site->publishing_mode,
                    'remote_id' => $result->remoteId,
                    'remote_url' => $result->remoteUrl,
                ]);
            } else {
                $this->handlePublishingFailure($payload, $result->errorMessage);
            }

        } catch (\Exception $e) {
            $this->handlePublishingFailure($payload, $e->getMessage());
            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle publishing failure
     */
    protected function handlePublishingFailure(PagePayload $payload, string $errorMessage): void
    {
        // Update payload
        $payload->update([
            'publish_status' => 'failed',
        ]);

        // Log failure
        PublishingLog::create([
            'payload_id' => $payload->id,
            'site_id' => $payload->site_id,
            'client_id' => $payload->client_id,
            'adapter_type' => $payload->site->publishing_mode,
            'action' => 'publish',
            'result' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $this->attempts(),
        ]);

        Log::channel('publishing')->error('Payload publishing failed', [
            'payload_id' => $payload->id,
            'site_id' => $payload->site_id,
            'error' => $errorMessage,
            'attempt' => $this->attempts(),
        ]);
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        $payload = PagePayload::with(['site', 'batch'])->find($this->payloadId);
        
        if ($payload) {
            $payload->update([
                'publish_status' => 'failed',
            ]);

            PublishingLog::create([
                'payload_id' => $payload->id,
                'site_id' => $payload->site_id,
                'client_id' => $payload->client_id,
                'adapter_type' => $payload->site->publishing_mode,
                'action' => 'publish',
                'result' => 'failed',
                'error_message' => 'Job permanently failed: ' . $exception->getMessage(),
                'retry_count' => $this->tries,
            ]);
        }

        Log::channel('publishing')->error('Payload publishing permanently failed', [
            'payload_id' => $this->payloadId,
            'error' => $exception->getMessage(),
        ]);
    }
}
