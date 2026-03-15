<?php

namespace App\Jobs;

use App\Models\AutomationLog;
use App\Models\LocationPage;
use App\Models\Site;
use App\Services\LocationPageRenderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonthlyContentRefreshJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ?int $siteId = null
    ) {}

    /**
     * Execute the job.
     * 
     * Refreshes content cache and updates timestamps for location pages
     */
    public function handle(): void
    {
        $startTime = now();

        // Determine which sites to refresh
        $sites = $this->siteId 
            ? Site::where('id', $this->siteId)->get()
            : Site::where('is_active', true)->get();

        $overallResults = [
            'sites_processed' => 0,
            'sites_succeeded' => 0,
            'sites_failed' => 0,
            'total_pages_refreshed' => 0,
            'errors' => [],
        ];

        foreach ($sites as $site) {
            $log = AutomationLog::create([
                'site_id' => $site->id,
                'client_id' => $site->client_id,
                'job_name' => 'monthly_content_refresh',
                'job_class' => self::class,
                'status' => 'started',
                'started_at' => now(),
            ]);

            try {
                $pagesRefreshed = 0;

                // Get all published location pages for this site
                $pages = LocationPage::where('site_id', $site->id)
                    ->where('status', 'published')
                    ->get();

                foreach ($pages as $page) {
                    try {
                        // Clear render cache
                        $page->update([
                            'render_cache' => null,
                            'cache_expires_at' => null,
                        ]);

                        // Optionally regenerate cache immediately
                        if (class_exists(LocationPageRenderService::class)) {
                            $renderService = app(LocationPageRenderService::class);
                            $renderService->renderPage($page);
                        }

                        $pagesRefreshed++;

                    } catch (\Exception $e) {
                        Log::warning('Failed to refresh page', [
                            'page_id' => $page->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                $log->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'duration_seconds' => now()->diffInSeconds($log->started_at),
                    'items_processed' => $pages->count(),
                    'items_succeeded' => $pagesRefreshed,
                    'items_failed' => $pages->count() - $pagesRefreshed,
                    'summary' => [
                        'pages_refreshed' => $pagesRefreshed,
                        'pages_total' => $pages->count(),
                    ],
                ]);

                $overallResults['sites_succeeded']++;
                $overallResults['total_pages_refreshed'] += $pagesRefreshed;

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

                Log::error('Content refresh failed for site', [
                    'site_id' => $site->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $overallResults['sites_processed']++;
        }

        // Create summary log
        AutomationLog::create([
            'job_name' => 'monthly_content_refresh',
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

        Log::info('Monthly content refresh completed', $overallResults);
    }
}
