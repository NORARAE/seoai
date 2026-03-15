<?php

namespace App\Jobs;

use App\Models\AutomationLog;
use App\Models\Site;
use App\Services\OpportunityDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WeeklyOpportunityScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ?int $siteId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OpportunityDetectionService $opportunityService): void
    {
        $startTime = now();

        // Determine which sites to scan
        $sites = $this->siteId 
            ? Site::where('id', $this->siteId)->get()
            : Site::where('is_active', true)->get();

        $overallResults = [
            'sites_processed' => 0,
            'sites_succeeded' => 0,
            'sites_failed' => 0,
            'total_opportunities_detected' => 0,
            'opportunities_by_type' => [],
            'errors' => [],
        ];

        foreach ($sites as $site) {
            $log = AutomationLog::create([
                'site_id' => $site->id,
                'client_id' => $site->client_id,
                'job_name' => 'weekly_opportunity_scan',
                'job_class' => self::class,
                'status' => 'started',
                'started_at' => now(),
            ]);

            try {
                $results = $opportunityService->scanSite($site);

                $log->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'duration_seconds' => now()->diffInSeconds($log->started_at),
                    'items_processed' => $results['total'],
                    'items_succeeded' => $results['total'],
                    'summary' => $results,
                ]);

                $overallResults['sites_succeeded']++;
                $overallResults['total_opportunities_detected'] += $results['total'];

                // Aggregate by type
                foreach ($results['by_type'] as $type => $count) {
                    $overallResults['opportunities_by_type'][$type] = 
                        ($overallResults['opportunities_by_type'][$type] ?? 0) + $count;
                }

            } catch (\Exception $e) {
                $log->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                    'duration_seconds' => now()->diffInSeconds($log->started_at),
                    'error_message' => $e->getMessage(),
                    'error_context' => [
                        'site_id' => $site->id,
                        'trace' => $e->getTraceAsString(),
                    ],
                ]);

                $overallResults['sites_failed']++;
                $overallResults['errors'][] = [
                    'site_id' => $site->id,
                    'error' => $e->getMessage(),
                ];

                Log::error('Opportunity scan failed for site', [
                    'site_id' => $site->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $overallResults['sites_processed']++;
        }

        // Create summary log
        AutomationLog::create([
            'job_name' => 'weekly_opportunity_scan',
            'job_class' => self::class,
            'status' => 'completed',
            'started_at' => $startTime,
            'completed_at' => now(),
            'duration_seconds' => now()->diffInSeconds($startTime),
            'items_processed' => $overallResults['sites_processed'],
            'items_succeeded' => $overallResults['sites_succeeded'],
            'items_failed' => $overallResults['sites_failed'],
            'summary' => $overallResults,
        ]);

        Log::info('Weekly opportunity scan completed', $overallResults);
    }
}
