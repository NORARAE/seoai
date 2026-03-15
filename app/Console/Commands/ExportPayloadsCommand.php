<?php

namespace App\Console\Commands;

use App\Models\PageGenerationBatch;
use App\Services\BulkPageExpansionService;
use Illuminate\Console\Command;

class ExportPayloadsCommand extends Command
{
    protected $signature = 'payloads:export 
                            {--batch= : The ID of the batch to export}
                            {--format=json : Export format (json, markdown, html)}
                            {--output= : Optional output path (defaults to storage/exports)}';

    protected $description = 'Export page payloads from a batch to files';

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

        // Validate format
        $format = $this->option('format');
        $validFormats = ['json', 'markdown', 'html'];
        if (!in_array($format, $validFormats)) {
            $this->error("Invalid format. Choose from: " . implode(', ', $validFormats));
            return 1;
        }

        $this->info("Exporting payloads from batch: {$batch->name}");
        $this->info("Format: {$format}");
        $this->newLine();

        // Check if batch has payloads
        $payloadCount = $batch->payloads()->count();
        if ($payloadCount === 0) {
            $this->error('Batch has no payloads to export.');
            return 1;
        }

        $this->info("Found {$payloadCount} payloads to export");

        try {
            $exportPath = $service->exportBatch($batch, $format);

            $this->newLine();
            $this->info("✓ Export completed successfully");
            $this->line("📦 ZIP file: {$exportPath}");
            
            // If custom output path specified, copy the file
            if ($outputPath = $this->option('output')) {
                $destination = $outputPath;
                if (is_dir($outputPath)) {
                    $destination = rtrim($outputPath, '/') . '/' . basename($exportPath);
                }
                
                copy(storage_path('app/' . $exportPath), $destination);
                $this->info("✓ Copied to: {$destination}");
            }

            $this->newLine();
            $this->comment('Export contains:');
            $this->line("  • {$payloadCount} {$format} files");
            $this->line("  • manifest.json with batch metadata");

            return 0;
        } catch (\Exception $e) {
            $this->error("Export failed: {$e->getMessage()}");
            return 1;
        }
    }
}
