<?php

namespace App\Jobs;

use App\Models\AutomationLog;
use App\Models\Site;
use App\Services\GscSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DailyGscSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ?int $siteId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(GscSyncService $gscService): void
    {
        $startTime = now();

        // Determine which sites to sync
        $sites = $this->siteId 
            ? Site::where('id', $this->siteId)->get()
            : Site::where('is_active', true)
                  ->whereNotNull('gsc_property_url')
                  ->get();

        $results = [
            'sites_processed' => 0,
            'sites_succeeded' => 0,
            'sites_failed' => 0,
            'total_metrics_synced' => 0,
            'errors' => [],
        ];

        foreach ($sites as $site) {
            $log = AutomationLog::create([
                'site_id' => $site->id,
                'client_id' => $site->client_id,
                'job_name' => 'daily_gsc_sync',
                'job_class' => self::class,
                'status' => 'started',
                'started_at' => now(),
            ]);

            try {
                $syncResult = $gscService->syncSite($site);

                if ($syncResult['success'] ?? false) {
                    $log->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'duration_seconds' => now()->diffInSeconds($log->started_at),
                        'items_processed' => $syncResult['metrics_count'] ?? 0,
                        'items_succeeded' => $syncResult['metrics_count'] ?? 0,
                        'summary' => $syncResult,
                    ]);

                    $results['sites_succeeded']++;
                    $results['total_metrics_synced'] += $syncResult['metrics_count'] ?? 0;
                } else {
                    throw new \Exception($syncResult['error'] ?? 'Unknown error');
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

                $results['sites_failed']++;
                $results['errors'][] = [
                    'site_id' => $site->id,
                    'error' => $e->getMessage(),
                ];

                Log::error('GSC sync failed for site', [
                    'site_id' => $site->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $results['sites_processed']++;
        }

        // Create summary log
        AutomationLog::create([
            'job_name' => 'daily_gsc_sync',
            'job_class' => self::class,
            'status' => 'completed',
            'started_at' => $startTime,
            'completed_at' => now(),
            'duration_seconds' => now()->diffInSeconds($startTime),
            'items_processed' => $results['sites_processed'],
            'items_succeeded' => $results['sites_succeeded'],
            'items_failed' => $results['sites_failed'],
            'summary' => $results,
        ]);

        Log::info('Daily GSC sync completed', $results);
    }
}
