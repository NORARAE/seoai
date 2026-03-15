<?php

namespace App\Console\Commands;

use App\Models\PageGenerationBatch;
use App\Services\BulkPageExpansionService;
use Illuminate\Console\Command;

class PublishPayloadsCommand extends Command
{
    protected $signature = 'payloads:publish 
                            {--batch= : The ID of the batch to publish}
                            {--force : Republish already published payloads}';

    protected $description = 'Publish page payloads from a batch';

    public function handle(BulkPageExpansionService $service)
    {
        // Validate batch
        $batchId = $this->option('batch');
        if (!$batchId) {
            $this->error('Batch ID is required. Use --batch=ID');
            return 1;
        }

        $batch = PageGenerationBatch::find($batchId);
        if (!$batch) {
            $this->error("Batch with ID {$batchId} not found.");
            return 1;
        }

        $force = $this->option('force');

        $this->info("Publishing payloads from batch: {$batch->name}");
        $this->info("Site: {$batch->site->name}");
        $this->info("Publishing mode: {$batch->site->publishing_mode}");
        $this->newLine();

        // Check if batch has payloads
        $payloadCount = $batch->payloads()->count();
        if ($payloadCount === 0) {
            $this->error('Batch has no payloads to publish.');
            return 1;
        }

        // Count unpublished
        $unpublishedCount = $force 
            ? $payloadCount 
            : $batch->payloads()->where('publish_status', 'pending')->count();

        if ($unpublishedCount === 0) {
            $this->warn('All payloads are already published. Use --force to republish.');
            return 0;
        }

        if (!$this->confirm("Publish {$unpublishedCount} payloads?", true)) {
            $this->comment('Publishing cancelled.');
            return 0;
        }

        try {
            $dispatched = $service->publishBatch($batch, $force);

            $this->info("✓ {$dispatched} publishing jobs dispatched to queue");
            $this->newLine();
            $this->comment('Monitor progress:');
            $this->line("  php artisan queue:work");
            $this->comment('View publishing logs:');
            $this->line("  Filament Admin > Publishing Logs");

            return 0;
        } catch (\Exception $e) {
            $this->error("Publishing failed: {$e->getMessage()}");
            return 1;
        }
    }
}
