<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\RevenueOpportunityService;
use Illuminate\Console\Command;

class ScanRevenueOpportunities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'opportunities:scan
                            {--site= : Site ID to scan}
                            {--min-priority=60 : Minimum priority score}
                            {--min-volume=20 : Minimum search volume}
                            {--service-value=500 : Service value per conversion}
                            {--conversion-rate=0.02 : Conversion rate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan for SEO revenue opportunities';

    /**
     * Execute the console command.
     */
    public function handle(RevenueOpportunityService $revenueService): int
    {
        $this->info('🔍 Scanning for SEO Revenue Opportunities...');
        $this->newLine();

        // Get site
        $siteId = $this->option('site');
        $site = $siteId 
            ? Site::findOrFail($siteId)
            : Site::where('status', 'active')->first();

        if (!$site) {
            $this->error('❌ No active site found');
            $this->info('💡 Tip: Check that a site exists with status="active"');
            
            // Show diagnostic info
            $totalSites = Site::count();
            $activeSites = Site::where('status', 'active')->count();
            
            $this->newLine();
            $this->warn("Sites in database: {$totalSites}");
            $this->warn("Active sites: {$activeSites}");
            
            if ($totalSites > 0) {
                $this->newLine();
                $this->info('Available sites:');
                Site::all()->each(function ($s) {
                    $this->line("  - ID: {$s->id}, Name: {$s->name}, Domain: {$s->domain}, Status: {$s->status}");
                });
            }
            
            return self::FAILURE;
        }

        // Diagnostic output
        $this->info("✅ Site Found: {$site->name} ({$site->domain})");
        $this->info("   Status: {$site->status}");
        $this->info("   Client: " . ($site->client ? $site->client->name : 'N/A'));
        
        if ($site->state_id && $site->state) {
            $this->info("   State: {$site->state->name} ({$site->state->code})");
        } else {
            $this->warn("   ⚠️  State: Not set (required for location-based opportunities)");
        }
        
        $this->newLine();

        // Check for required data
        $services = \App\Models\Service::where('is_active', true)->get();
        $this->info("📋 Active Services: {$services->count()}");
        if ($services->count() > 0) {
            $services->each(fn($s) => $this->line("   - {$s->name}"));
        } else {
            $this->warn('   ⚠️  No active services found');
        }
        $this->newLine();

        if ($site->state_id) {
            $cities = \App\Models\City::where('state_id', $site->state_id)->get();
            $this->info("🏙️  Cities in {$site->state->name}: {$cities->count()}");
            if ($cities->count() > 0) {
                $cities->take(10)->each(fn($c) => $this->line("   - {$c->name}"));
                if ($cities->count() > 10) {
                    $this->line("   ... and " . ($cities->count() - 10) . " more");
                }
            } else {
                $this->warn("   ⚠️  No cities found for this state");
            }
        } else {
            $this->warn("⚠️  Cannot check cities: site.state_id not set");
        }
        
        $this->newLine();

        // Validate minimum requirements
        if ($services->count() === 0) {
            $this->error('❌ Cannot scan: No active services found');
            $this->info('💡 Create services first or activate existing ones');
            return self::FAILURE;
        }

        if (!$site->state_id) {
            $this->error('❌ Cannot scan: Site does not have a state_id assigned');
            $this->info('💡 Assign a state to this site first');
            return self::FAILURE;
        }

        if ($site->state_id) {
            $cityCount = \App\Models\City::where('state_id', $site->state_id)->count();
            if ($cityCount === 0) {
                $this->error('❌ Cannot scan: No cities found for this state');
                $this->info('💡 Seed cities for ' . $site->state->name . ' first');
                return self::FAILURE;
            }
        }

        // Scan options
        $options = [
            'min_priority_score' => (int) $this->option('min-priority'),
            'min_search_volume' => (int) $this->option('min-volume'),
            'service_value' => (float) $this->option('service-value'),
            'conversion_rate' => (float) $this->option('conversion-rate'),
        ];

        $this->info("⚙️  Scanning Parameters:");
        $this->table([], [
            ['Min Priority Score', $options['min_priority_score']],
            ['Min Search Volume', $options['min_search_volume']],
            ['Service Value', '$' . number_format($options['service_value'], 2)],
            ['Conversion Rate', ($options['conversion_rate'] * 100) . '%'],
        ]);
        $this->newLine();

        // Generate opportunities
        $bar = $this->output->createProgressBar();
        $bar->setFormat('Processing: %current% combinations... [%bar%] %percent:3s%%');
        $bar->start();

        $result = $revenueService->generateOpportunities($site, $options);

        $bar->finish();
        $this->newLine(2);

        // Display results
        $this->info('✅ Scan Complete!');
        $this->table([], [
            ['Created', $result['created']],
            ['Updated', $result['updated']],
            ['Skipped', $result['skipped']],
            ['Total Processed', $result['total_processed']],
        ]);
        $this->newLine();

        // Show top opportunities
        $topOpportunities = $revenueService->getTopOpportunities($site, 10);

        if ($topOpportunities->isEmpty()) {
            $this->warn('No opportunities found matching criteria.');
            return self::SUCCESS;
        }

        $this->info('🎯 Top 10 Revenue Opportunities:');
        $this->newLine();

        $tableData = $topOpportunities->map(function ($opp) {
            return [
                'Priority' => number_format($opp->priority_score, 0),
                'Type' => str_replace('_', ' ', ucwords($opp->opportunity_type, '_')),
                'Service' => $opp->service->name,
                'Location' => $opp->location_name,
                'Volume' => number_format($opp->search_volume),
                'Revenue' => '$' . number_format($opp->estimated_monthly_revenue, 0),
                'Potential' => number_format($opp->rank_potential, 0) . '%',
                'Competition' => number_format($opp->competition_score, 0),
                'Status' => $opp->status,
            ];
        })->toArray();

        $this->table(
            ['Priority', 'Type', 'Service', 'Location', 'Volume', 'Revenue', 'Potential', 'Comp.', 'Status'],
            $tableData
        );

        // Summary stats
        $totalRevenue = $topOpportunities->sum('estimated_monthly_revenue');
        $this->newLine();
        $this->info("💰 Total Revenue Potential (Top 10): $" . number_format($totalRevenue, 0) . "/month");

        // Quick wins
        $quickWins = $revenueService->getQuickWins($site, 5);
        if ($quickWins->isNotEmpty()) {
            $this->newLine();
            $this->info('⚡ Quick Wins (Top 5):');
            $quickWinData = $quickWins->map(function ($opp) {
                return [
                    $opp->service->name,
                    $opp->location_name,
                    '$' . number_format($opp->estimated_monthly_revenue, 0),
                ];
            })->toArray();
            $this->table(['Service', 'Location', 'Est. Revenue'], $quickWinData);
        }

        $this->newLine();
        $this->comment('💡 Tip: View opportunities in the admin dashboard at /admin');
        $this->comment('💡 Generate pages directly from the TopRevenueOpportunitiesWidget');

        return self::SUCCESS;
    }
}
