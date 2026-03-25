<?php

namespace Tests\Feature;

use App\Filament\Resources\SeoOpportunityResource;
use App\Filament\Resources\UrlInventoryResource;
use App\Filament\Widgets\CurrentScanDiscoverySummary;
use App\Jobs\RunCompetitorScanJob;
use App\Models\City;
use App\Models\Client;
use App\Models\CompetitorDomain;
use App\Models\CompetitorScanRun;
use App\Models\County;
use App\Models\ScanRun;
use App\Models\SeoOpportunity;
use App\Models\Service;
use App\Models\Site;
use App\Models\State;
use App\Models\UrlInventory;
use App\Models\User;
use App\Services\Discovery\CompetitorScanService;
use App\Support\CurrentScanResolver;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ScanScopedDiscoveryConsistencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetResolverCaches();
        $this->withoutMiddleware(FilamentAuthenticate::class);
    }

    #[Test]
    public function resolver_pins_metrics_to_latest_completed_scan_even_when_live_scan_exists(): void
    {
        [$user, $site] = $this->createUserWithSite();

        $completed = ScanRun::create([
            'site_id' => $site->id,
            'status' => 'completed',
            'started_at' => now()->subHours(4),
            'completed_at' => now()->subHours(3),
        ]);

        ScanRun::create([
            'site_id' => $site->id,
            'status' => 'pending',
            'started_at' => now()->subHours(2),
        ]);

        $running = ScanRun::create([
            'site_id' => $site->id,
            'status' => 'running',
            'started_at' => now()->subHour(),
        ]);

        Session::put('active_site_id', $site->id);

        $resolved = CurrentScanResolver::resolveForUser($user);
        $context = CurrentScanResolver::contextForUser($user);

        $this->assertNotNull($resolved);
        $this->assertSame($completed->id, $resolved->id);
        $this->assertSame('scanning', $context['state']);
        $this->assertSame($running->id, $context['active_scan']?->id);
    }

    #[Test]
    public function resolver_reports_incomplete_when_newer_attempt_failed_after_latest_completed(): void
    {
        [$user, $site] = $this->createUserWithSite();

        $completed = ScanRun::create([
            'site_id' => $site->id,
            'status' => 'completed',
            'started_at' => now()->subHours(4),
            'completed_at' => now()->subHours(1),
        ]);

        ScanRun::create([
            'site_id' => $site->id,
            'status' => 'failed',
            'started_at' => now()->subMinutes(45),
            'completed_at' => now()->subMinutes(30),
        ]);

        Session::put('active_site_id', $site->id);

        $resolved = CurrentScanResolver::resolveForUser($user);
        $context = CurrentScanResolver::contextForUser($user);

        $this->assertNotNull($resolved);
        $this->assertSame($completed->id, $resolved->id);
        $this->assertSame('incomplete', $context['state']);
    }

    #[Test]
    public function resolver_uses_latest_completed_by_completed_at_desc(): void
    {
        [$user, $site] = $this->createUserWithSite();

        ScanRun::create([
            'site_id' => $site->id,
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'completed_at' => now()->subHours(3),
        ]);

        $latestCompleted = ScanRun::create([
            'site_id' => $site->id,
            'status' => 'completed',
            'started_at' => now()->subHours(5),
            'completed_at' => now()->subMinutes(30),
        ]);

        Session::put('active_site_id', $site->id);

        $resolved = CurrentScanResolver::resolveForUser($user);

        $this->assertNotNull($resolved);
        $this->assertSame($latestCompleted->id, $resolved->id);
    }

    #[Test]
    public function url_inventory_current_scan_view_is_pinned_to_the_same_site_and_scan(): void
    {
        [$user, $siteA, $siteB] = $this->createUserWithTwoSites();

        $scanA = ScanRun::create([
            'site_id' => $siteA->id,
            'status' => 'completed',
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
        ]);

        $otherScanA = ScanRun::create([
            'site_id' => $siteA->id,
            'status' => 'completed',
            'started_at' => now()->subDays(1),
            'completed_at' => now()->subDays(1)->addMinutes(10),
        ]);

        $scanB = ScanRun::create([
            'site_id' => $siteB->id,
            'status' => 'completed',
            'started_at' => now()->subMinutes(5),
            'completed_at' => now()->subMinutes(1),
        ]);

        UrlInventory::create([
            'site_id' => $siteA->id,
            'first_seen_scan_run_id' => $scanA->id,
            'last_seen_scan_run_id' => $scanA->id,
            'url' => 'https://alpha.test/new-url',
            'normalized_url' => 'https://alpha.test/new-url',
            'path' => '/new-url',
            'status' => 'completed',
        ]);

        UrlInventory::create([
            'site_id' => $siteA->id,
            'first_seen_scan_run_id' => $otherScanA->id,
            'last_seen_scan_run_id' => $otherScanA->id,
            'url' => 'https://alpha.test/existing-url',
            'normalized_url' => 'https://alpha.test/existing-url',
            'path' => '/existing-url',
            'status' => 'completed',
        ]);

        UrlInventory::create([
            'site_id' => $siteB->id,
            'first_seen_scan_run_id' => $scanB->id,
            'last_seen_scan_run_id' => $scanB->id,
            'url' => 'https://beta.test/other-site-url',
            'normalized_url' => 'https://beta.test/other-site-url',
            'path' => '/other-site-url',
            'status' => 'completed',
        ]);

        Session::put('active_site_id', $siteA->id);

        $this->actingAs($user)
            ->get(UrlInventoryResource::getUrl('index', [
                'tableFilters' => [
                    'current_scan' => [
                        'isActive' => true,
                        'scan_run_id' => $scanA->id,
                        'site_id' => $siteA->id,
                    ],
                ],
            ]))
            ->assertOk()
            ->assertSee('https://alpha.test/new-url')
                ->assertSee('Complete: scan #' . $scanA->id . ' for ' . $siteA->domain)
            ->assertDontSee('https://alpha.test/existing-url')
            ->assertDontSee('https://beta.test/other-site-url');
    }

    #[Test]
    public function seo_opportunity_current_scan_view_is_pinned_to_the_same_site_and_scan(): void
    {
        [$user, $siteA, $siteB, $client] = $this->createUserWithTwoSitesAndClient();
        [$service, $city] = $this->createServiceAndCity();

        $scanA = ScanRun::create([
            'site_id' => $siteA->id,
            'status' => 'completed',
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
        ]);

        $otherScanA = ScanRun::create([
            'site_id' => $siteA->id,
            'status' => 'completed',
            'started_at' => now()->subDays(1),
            'completed_at' => now()->subDays(1)->addMinutes(10),
        ]);

        $scanB = ScanRun::create([
            'site_id' => $siteB->id,
            'status' => 'completed',
            'started_at' => now()->subMinutes(5),
            'completed_at' => now()->subMinutes(1),
        ]);

        $this->createSeoOpportunity($siteA, $client, $service, $city, $scanA->id, 'keyword-current-scan');
        $this->createSeoOpportunity($siteA, $client, $service, $this->createSecondCity($city), $otherScanA->id, 'keyword-existing-scan');
        $this->createSeoOpportunity($siteB, $client, $service, $this->createThirdCity($city), $scanB->id, 'keyword-other-site');

        Session::put('active_site_id', $siteA->id);

        $this->actingAs($user)
            ->get(SeoOpportunityResource::getUrl('index', [
                'tableFilters' => [
                    'current_scan' => [
                        'isActive' => true,
                        'scan_run_id' => $scanA->id,
                        'site_id' => $siteA->id,
                    ],
                ],
            ]))
            ->assertOk()
            ->assertSee('keyword-current-scan')
                ->assertSee('Complete: scan #' . $scanA->id . ' for ' . $siteA->domain)
            ->assertDontSee('keyword-existing-scan')
            ->assertDontSee('keyword-other-site');
    }

    #[Test]
    public function widget_urls_pin_destination_to_the_same_site_and_scan(): void
    {
        [$user, $site] = $this->createUserWithSite();

        $scan = ScanRun::create([
            'site_id' => $site->id,
            'status' => 'completed',
            'started_at' => now()->subMinutes(15),
            'completed_at' => now()->subMinutes(10),
        ]);

        UrlInventory::create([
            'site_id' => $site->id,
            'first_seen_scan_run_id' => $scan->id,
            'last_seen_scan_run_id' => $scan->id,
            'url' => 'https://example.test/new-url',
            'normalized_url' => 'https://example.test/new-url',
            'path' => '/new-url',
            'status' => 'completed',
        ]);

        Session::put('active_site_id', $site->id);
        $this->actingAs($user);

        $widget = new class extends CurrentScanDiscoverySummary {
            public function exposedStats(): array
            {
                return $this->getStats();
            }
        };

        $stats = $widget->exposedStats();

        $urlFilters = $this->extractTableFiltersFromUrl($stats[0]->getUrl());
        $opportunityFilters = $this->extractTableFiltersFromUrl($stats[1]->getUrl());

        $this->assertSame($site->id, (int) data_get($urlFilters, 'current_scan.site_id'));
        $this->assertSame($scan->id, (int) data_get($urlFilters, 'current_scan.scan_run_id'));
        $this->assertSame($site->id, (int) data_get($opportunityFilters, 'current_scan.site_id'));
        $this->assertSame($scan->id, (int) data_get($opportunityFilters, 'current_scan.scan_run_id'));
    }

    #[Test]
    public function resolver_caching_does_not_change_correctness_and_avoids_repeat_queries(): void
    {
        [$user, $site] = $this->createUserWithSite();

        $scan = ScanRun::create([
            'site_id' => $site->id,
            'status' => 'completed',
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(2),
        ]);

        Session::put('active_site_id', $site->id);

        $connection = DB::connection();
        $connection->flushQueryLog();
        $connection->enableQueryLog();

        $first = CurrentScanResolver::resolveForUser($user);
        $firstQueryCount = count($connection->getQueryLog());

        $second = CurrentScanResolver::resolveForUser($user);
        $secondQueryCount = count($connection->getQueryLog());

        $this->assertNotNull($first);
        $this->assertNotNull($second);
        $this->assertSame($scan->id, $first->id);
        $this->assertSame($scan->id, $second->id);
        $this->assertSame($firstQueryCount, $secondQueryCount);
    }

    #[Test]
    public function competitor_domain_gets_one_free_scan_then_blocks_rescans_without_payment(): void
    {
        Queue::fake();

        [$user, $site] = $this->createUserWithSite();
        $user->update(['role' => 'operator']);

        $service = app(CompetitorScanService::class);

        $first = $service->registerDomain($site, 'competitor.test', $user);

        $this->assertSame('started', $first['status']);
        Queue::assertPushed(RunCompetitorScanJob::class, 1);

        $domain = CompetitorDomain::query()->where('site_id', $site->id)->where('domain', 'competitor.test')->firstOrFail();
        CompetitorScanRun::query()->where('competitor_domain_id', $domain->id)->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $domain->update(['scan_count' => 1, 'paid_scan_credits' => 0]);

        $blocked = $service->startScan($domain->fresh(), $user, 'manual');

        $this->assertSame('blocked', $blocked['status']);
        Queue::assertPushed(RunCompetitorScanJob::class, 1);
    }

    #[Test]
    public function super_admin_bypasses_competitor_rescan_limits(): void
    {
        Queue::fake();

        [$user, $site] = $this->createUserWithSite();

        $domain = CompetitorDomain::create([
            'site_id' => $site->id,
            'domain' => 'bypass.test',
            'scan_count' => 2,
            'paid_scan_credits' => 0,
        ]);

        $result = app(CompetitorScanService::class)->startScan($domain, $user, 'manual');

        $this->assertSame('started', $result['status']);
        Queue::assertPushed(RunCompetitorScanJob::class, 1);
    }

    protected function createUserWithSite(): array
    {
        $client = Client::create([
            'name' => 'Test Client',
            'status' => 'active',
        ]);

        $site = Site::create([
            'client_id' => $client->id,
            'name' => 'Alpha Site',
            'domain' => 'alpha.test',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'client_id' => $client->id,
            'role' => 'super_admin',
        ]);

        $user->sites()->attach($site->id);

        return [$user, $site, $client];
    }

    protected function createUserWithTwoSites(): array
    {
        [$user, $siteA, $client] = $this->createUserWithSite();

        $siteB = Site::create([
            'client_id' => $client->id,
            'name' => 'Beta Site',
            'domain' => 'beta.test',
            'status' => 'active',
        ]);

        $user->sites()->attach($siteB->id);

        return [$user, $siteA, $siteB];
    }

    protected function createUserWithTwoSitesAndClient(): array
    {
        [$user, $siteA, $client] = $this->createUserWithSite();

        $siteB = Site::create([
            'client_id' => $client->id,
            'name' => 'Beta Site',
            'domain' => 'beta-opps.test',
            'status' => 'active',
        ]);

        $user->sites()->attach($siteB->id);

        return [$user, $siteA, $siteB, $client];
    }

    protected function createServiceAndCity(): array
    {
        $state = State::create([
            'name' => 'Washington',
            'code' => 'WA',
            'slug' => 'washington',
        ]);

        $county = County::create([
            'state_id' => $state->id,
            'name' => 'King County',
            'slug' => 'king-county',
        ]);

        $city = City::create([
            'state_id' => $state->id,
            'county_id' => $county->id,
            'name' => 'Seattle',
            'slug' => 'seattle',
        ]);

        $service = Service::create([
            'name' => 'Water Damage',
            'slug' => 'water-damage',
            'is_active' => true,
        ]);

        return [$service, $city];
    }

    protected function createSecondCity(City $city): City
    {
        return City::create([
            'state_id' => $city->state_id,
            'county_id' => $city->county_id,
            'name' => 'Tacoma',
            'slug' => 'tacoma',
        ]);
    }

    protected function createThirdCity(City $city): City
    {
        return City::create([
            'state_id' => $city->state_id,
            'county_id' => $city->county_id,
            'name' => 'Bellevue',
            'slug' => 'bellevue',
        ]);
    }

    protected function createSeoOpportunity(Site $site, Client $client, Service $service, City $city, int $scanRunId, string $keyword): SeoOpportunity
    {
        return SeoOpportunity::create([
            'site_id' => $site->id,
            'scan_run_id' => $scanRunId,
            'client_id' => $client->id,
            'service_id' => $service->id,
            'location_id' => $city->id,
            'opportunity_category' => 'coverage_gap',
            'opportunity_type' => 'new_page',
            'status' => 'pending',
            'target_keyword' => $keyword,
            'suggested_url' => '/' . $keyword,
            'detection_source' => 'crawl_discovery',
        ]);
    }

    /** @return array<string, mixed> */
    protected function extractTableFiltersFromUrl(?string $url): array
    {
        $this->assertNotNull($url);

        $query = parse_url($url, PHP_URL_QUERY);
        parse_str((string) $query, $parameters);

        return $parameters['tableFilters'] ?? [];
    }

    protected function resetResolverCaches(): void
    {
        CurrentScanResolver::flushCache();
    }
}