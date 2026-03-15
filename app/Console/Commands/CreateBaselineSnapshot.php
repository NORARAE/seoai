<?php

namespace App\Console\Commands;

use App\Models\BaselineSnapshot;
use App\Models\LocationPage;
use App\Models\Page;
use App\Services\PerformanceAggregationService;
use Illuminate\Console\Command;

class CreateBaselineSnapshot extends Command
{
    protected $signature = 'baseline:snapshot 
                            {type : Model type: page or location_page}
                            {id : Model ID}';

    protected $description = 'Create a baseline snapshot of a page before optimization';

    public function handle(PerformanceAggregationService $perfService): int
    {
        $type = $this->argument('type');
        $id = $this->argument('id');

        // Get the model
        $model = match ($type) {
            'page' => Page::find($id),
            'location_page' => LocationPage::find($id),
            default => null,
        };

        if (!$model) {
            $this->error("Model not found: {$type} #{$id}");
            return self::FAILURE;
        }

        $this->info("Creating baseline snapshot for {$type} #{$id}...");

        // Get 30-day performance summary
        $performanceData = $perfService->get30DaySummary($model);

        // Create snapshot
        $snapshot = BaselineSnapshot::createFromModel($model, $performanceData);

        $this->info("✓ Snapshot created (ID: {$snapshot->id})");
        
        if ($performanceData) {
            $this->line("  Clicks: {$performanceData['clicks']}");
            $this->line("  Impressions: {$performanceData['impressions']}");
            $this->line("  CTR: " . ($performanceData['ctr'] * 100) . "%");
            $this->line("  Avg Position: {$performanceData['avg_position']}");
        } else {
            $this->warn("  No performance data available");
        }

        return self::SUCCESS;
    }
}
