<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\BulkPageExpansionService;
use Illuminate\Console\Command;

class GeneratePayloadsCommand extends Command
{
    protected $signature = 'payloads:generate 
                            {--site= : The ID of the site to generate payloads for}
                            {--top=20 : Number of top opportunities to process (max 50)}
                            {--auto-publish : Automatically publish after generation}
                            {--name= : Custom batch name}';

    protected $description = 'Generate page payloads from SEO opportunities';

    public function handle(BulkPageExpansionService $service)
    {
        // Validate site
        $siteId = $this->option('site');
        if (!$siteId) {
            $this->error('Site ID is required. Use --site=ID');
            return 1;
        }

        $site = Site::find($siteId);
        if (!$site) {
            $this->error("Site with ID {$siteId} not found.");
            return 1;
        }

        // Validate count
        $count = (int) $this->option('top');
        if ($count < 1 || $count > 50) {
            $this->error('Count must be between 1 and 50.');
            return 1;
        }

        $autoPublish = $this->option('auto-publish');
        $batchName = $this->option('name') ?? "CLI Batch - " . now()->format('Y-m-d H:i');

        $this->info("Generating {$count} page payloads for site: {$site->name}");
        $this->info("Auto-publish: " . ($autoPublish ? 'Yes' : 'No'));
        $this->newLine();

        try {
            // Create batch
            $batch = $service->generateBatch($site, [
                'count' => $count,
                'opportunity_source' => 'manual',
                'auto_publish' => $autoPublish,
                'name' => $batchName,
            ]);

            $this->info("✓ Batch created: {$batch->name} (ID: {$batch->id})");
            $this->info("✓ {$batch->requested_count} generation jobs dispatched to queue");
            
            if ($autoPublish && $site->publishing_mode === 'native') {
                $this->info("✓ Auto-publish enabled - publishing jobs will chain after generation");
            }

            $this->newLine();
            $this->comment('Monitor progress:');
            $this->line("  php artisan queue:work");
            $this->comment('View batch status:');
            $this->line("  Filament Admin > Generation Batches > {$batch->id}");

            return 0;
        } catch (\Exception $e) {
            $this->error("Generation failed: {$e->getMessage()}");
            return 1;
        }
    }
}
