<?php

namespace App\Console\Commands;

use App\Services\PageImportService;
use Illuminate\Console\Command;

class ImportSeoPages extends Command
{
    protected $signature   = 'seo:import-pages';
    protected $description = 'Import all SEO marketing pages from docs/seo-architecture JSON files';

    public function handle(PageImportService $service): int
    {
        $this->info('Importing SEO marketing pages…');

        $stats = $service->import();

        $this->table(
            ['Imported', 'Updated', 'Skipped'],
            [[$stats['imported'], $stats['updated'], $stats['skipped']]]
        );

        if (! empty($stats['errors'])) {
            $this->warn('Errors encountered:');
            foreach ($stats['errors'] as $err) {
                $this->line("  <fg=red>✗</> {$err}");
            }
        }

        $total = $stats['imported'] + $stats['updated'];
        $this->info("Done. {$total} pages available.");

        return self::SUCCESS;
    }
}
