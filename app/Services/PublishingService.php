<?php

namespace App\Services;

use App\Contracts\PublishingAdapterInterface;
use App\DTOs\PublishResult;
use App\Models\PagePayload;
use App\Models\PublishingLog;
use App\Models\Site;
use App\Services\Publishing\ExportPublishingAdapter;
use App\Services\Publishing\WordPressPublishingAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PublishingService
{
    protected array $adapters = [];

    public function __construct()
    {
        $this->registerAdapter('wordpress', WordPressPublishingAdapter::class);
        $this->registerAdapter('export', ExportPublishingAdapter::class);
    }

    public function registerAdapter(string $type, string $adapterClass): void
    {
        $this->adapters[$type] = $adapterClass;
    }

    /**
     * Get the appropriate adapter for a site
     */
    public function getAdapter(Site $site): PublishingAdapterInterface
    {
        $adapterClass = match ($site->publishing_mode) {
            'native' => $this->getAdapterForCms($site->cms_type),
            'export_only', 'manual' => ExportPublishingAdapter::class,
            default => ExportPublishingAdapter::class,
        };

        return app($adapterClass);
    }

    protected function getAdapterForCms(string $cmsType): string
    {
        return match ($cmsType) {
            'wordpress' => WordPressPublishingAdapter::class,
            default => ExportPublishingAdapter::class,
        };
    }

    /**
     * Publish a page payload using the appropriate adapter
     */
    public function publish(PagePayload $payload): PublishResult
    {
        $adapter = $this->getAdapter($payload->site);
        $adapterType = class_basename($adapter);

        try {
            $result = $adapter->publish($payload);

            // Log the publishing attempt
            $this->logPublishing($payload, $adapterType, 'publish', $result);

            if ($result->success) {
                $payload->markAsPublished(
                    $result->remoteId,
                    $result->remoteUrl,
                    $result->remoteEditUrl
                );

                Log::channel('page-generation')->info('Page published', [
                    'payload_id' => $payload->id,
                    'remote_id' => $result->remoteId,
                    'adapter' => $adapterType,
                ]);
            } else {
                Log::channel('page-generation')->error('Publishing failed', [
                    'payload_id' => $payload->id,
                    'error' => $result->error,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            $result = PublishResult::failure($e->getMessage());
            $this->logPublishing($payload, $adapterType, 'publish', $result);

            Log::channel('page-generation')->error('Publishing exception', [
                'payload_id' => $payload->id,
                'error' => $e->getMessage(),
            ]);

            return $result;
        }
    }

    /**
     * Validate connection for a site
     */
    public function validateConnection(Site $site): bool
    {
        $adapter = $this->getAdapter($site);
        return $adapter->validateConnection($site);
    }

    /**
     * Export a batch of payloads as ZIP
     */
    public function exportBatch(Collection $payloads, string $format = 'json'): string
    {
        if ($payloads->isEmpty()) {
            throw new \InvalidArgumentException('Cannot export empty payload collection');
        }

        $site = $payloads->first()->site;
        $adapter = $this->getAdapter($site);
        $batchId = $payloads->first()->batch_id;

        // Export each payload
        $exports = $payloads->map(function($payload) use ($adapter, $format) {
            return [
                'filename' => $payload->slug . '.' . $format,
                'content' => $adapter->export($payload, $format),
            ];
        });

        // Create ZIP file
        $zipPath = storage_path("app/exports/batch-{$batchId}-{$format}.zip");
        $zip = new \ZipArchive();
        
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($exports as $export) {
                $zip->addFromString($export['filename'], $export['content']);
            }
            
            // Add manifest
            $manifest = [
                'batch_id' => $batchId,
                'site' => $site->domain,
                'exported_at' => now()->toIso8601String(),
                'format' => $format,
                'count' => $payloads->count(),
                'files' => $exports->pluck('filename')->toArray(),
            ];
            $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
            
            $zip->close();
        }

        return $zipPath;
    }

    /**
     * Log publishing attempt
     */
    protected function logPublishing(
        PagePayload $payload,
        string $adapterType,
        string $action,
        PublishResult $result
    ): void {
        PublishingLog::create([
            'payload_id' => $payload->id,
            'site_id' => $payload->site_id,
            'adapter_type' => $adapterType,
            'action' => $action,
            'result' => $result->success ? 'success' : 'failure',
            'error_message' => $result->error,
            'remote_response' => $result->metadata,
            'remote_id' => $result->remoteId,
            'remote_url' => $result->remoteUrl,
        ]);
    }
}
