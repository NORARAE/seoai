<?php

namespace Tests\Unit\Services\Discovery;

use App\Models\City;
use App\Models\Client;
use App\Models\County;
use App\Models\SeoOpportunity;
use App\Models\Service;
use App\Models\Site;
use App\Models\State;
use App\Models\UrlInventory;
use App\Services\Discovery\OpportunityDetectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OpportunityDetectionServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_detects_missing_service_city_pages_as_location_gaps(): void
    {
        $client = Client::create(['name' => 'Acme Client']);
        $state = State::create(['name' => 'Washington', 'code' => 'WA', 'slug' => 'washington']);
        $county = County::create(['state_id' => $state->id, 'name' => 'King County', 'slug' => 'king-county']);

        $cityCovered = City::create([
            'state_id' => $state->id,
            'county_id' => $county->id,
            'name' => 'Seattle',
            'slug' => 'seattle',
        ]);

        $cityMissing = City::create([
            'state_id' => $state->id,
            'county_id' => $county->id,
            'name' => 'Tacoma',
            'slug' => 'tacoma',
        ]);

        $site = Site::create([
            'client_id' => $client->id,
            'state_id' => $state->id,
            'domain' => 'example.com',
            'name' => 'Example Site',
        ]);

        $service = Service::create([
            'name' => 'Water Damage Restoration',
            'slug' => 'water-damage-restoration',
            'is_active' => true,
        ]);

        UrlInventory::create([
            'site_id' => $site->id,
            'url' => 'https://example.com/water-damage-restoration-seattle-wa',
            'normalized_url' => 'https://example.com/water-damage-restoration-seattle-wa',
            'path' => '/water-damage-restoration-seattle-wa',
            'status' => 'completed',
            'indexability_status' => 'indexable',
            'page_type' => 'service',
        ]);

        $detector = app(OpportunityDetectionService::class);

        $created = $detector->detectLocationGaps($site);

        $this->assertSame(1, $created);

        $this->assertDatabaseHas('seo_opportunities', [
            'site_id' => $site->id,
            'service_id' => $service->id,
            'location_id' => $cityMissing->id,
            'opportunity_type' => 'new_page',
            'status' => 'pending',
            'suggested_url' => '/water-damage-restoration-tacoma-wa',
            'detection_source' => 'crawl_discovery',
        ]);

        $this->assertDatabaseMissing('seo_opportunities', [
            'site_id' => $site->id,
            'service_id' => $service->id,
            'location_id' => $cityCovered->id,
            'opportunity_type' => 'new_page',
        ]);
    }

    #[Test]
    public function detect_returns_summary_shape(): void
    {
        $client = Client::create(['name' => 'Acme Client']);
        $state = State::create(['name' => 'Washington', 'code' => 'WA', 'slug' => 'washington']);
        $site = Site::create([
            'client_id' => $client->id,
            'state_id' => $state->id,
            'domain' => 'example.org',
            'name' => 'Example Org',
        ]);

        $detector = app(OpportunityDetectionService::class);

        $summary = $detector->detect($site);

        $this->assertArrayHasKey('location_gaps', $summary);
        $this->assertArrayHasKey('content_gaps', $summary);
        $this->assertArrayHasKey('internal_link_opps', $summary);
    }
}
