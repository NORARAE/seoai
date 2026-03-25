<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Client;
use App\Models\County;
use App\Models\PageMetadata;
use App\Models\PagePayload;
use App\Models\PerformanceMetric;
use App\Models\SeoOpportunity;
use App\Models\Service;
use App\Models\Site;
use App\Models\State;
use App\Models\UrlInventory;
use App\Services\SearchOpportunity\OpportunityMappingEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchOpportunityMappingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_detects_missing_service_location_opportunities(): void
    {
        [$site, $service, $city] = $this->makeCoverageContext();

        $result = app(OpportunityMappingEngine::class)->mapSite($site);

        $this->assertSame(1, $result['created']);
        $this->assertSame(0, $result['updated']);
        $this->assertSame(0, $result['skipped']);

        $opportunity = SeoOpportunity::firstOrFail();

        $this->assertSame('missing_page', $opportunity->opportunity_category);
        $this->assertSame('new_page', $opportunity->opportunity_type);
        $this->assertFalse($opportunity->page_exists);
        $this->assertStringContainsString($service->name, (string) $opportunity->reason_summary);
        $this->assertStringContainsString($city->name, (string) $opportunity->reason_summary);
        $this->assertStringContainsString('/water-damage-restoration-seattle', (string) $opportunity->recommended_action);
    }

    public function test_it_suppresses_existing_high_quality_pages_without_clear_growth_signal(): void
    {
        [$site, $service, $city, $client] = $this->makeCoverageContext();

        $payload = PagePayload::create([
            'site_id' => $site->id,
            'client_id' => $client->id,
            'service_id' => $service->id,
            'location_id' => $city->id,
            'location_type' => 'city',
            'title' => 'Water Damage Restoration Seattle',
            'meta_description' => 'Full service restoration in Seattle.',
            'slug' => 'water-damage-restoration-seattle',
            'canonical_url_suggestion' => 'https://example.com/water-damage-restoration-seattle',
            'schema_json_ld' => [['@type' => 'Service']],
            'structured_data_type' => 'Service',
            'seo_score' => 82,
            'content_quality_score' => 88,
            'publish_status' => 'published',
            'status' => 'published',
        ]);

        $url = UrlInventory::create([
            'site_id' => $site->id,
            'url' => 'https://example.com/water-damage-restoration-seattle',
            'normalized_url' => 'https://example.com/water-damage-restoration-seattle',
            'path' => '/water-damage-restoration-seattle',
            'depth' => 1,
            'status' => 'completed',
            'word_count' => 900,
            'indexability_status' => 'indexable',
            'page_type' => 'service',
            'internal_link_count' => 8,
            'incoming_link_count' => 5,
            'is_orphan_page' => false,
        ]);

        PageMetadata::create([
            'url_id' => $url->id,
            'title' => 'Water Damage Restoration Seattle',
            'meta_description' => 'Full service restoration in Seattle.',
            'h1' => 'Water Damage Restoration Seattle',
            'schema' => [['@type' => 'LocalBusiness']],
        ]);

        PerformanceMetric::create([
            'site_id' => $site->id,
            'url' => $url->url,
            'date' => now()->subDays(3),
            'clicks' => 12,
            'impressions' => 180,
            'ctr' => 0.0667,
            'average_position' => 12.5,
        ]);

        $result = app(OpportunityMappingEngine::class)->mapSite($site);

        $this->assertSame(0, $result['created']);
        $this->assertSame(0, SeoOpportunity::count());

        $this->assertNotNull($payload);
    }

    public function test_it_generates_optimization_opportunities_with_explainable_scores(): void
    {
        [$site, $service, $city, $client] = $this->makeCoverageContext();

        PagePayload::create([
            'site_id' => $site->id,
            'client_id' => $client->id,
            'service_id' => $service->id,
            'location_id' => $city->id,
            'location_type' => 'city',
            'title' => 'Water Damage Restoration Seattle',
            'slug' => 'water-damage-restoration-seattle',
            'canonical_url_suggestion' => 'https://example.com/water-damage-restoration-seattle',
            'publish_status' => 'published',
            'status' => 'published',
            'seo_score' => 48,
            'content_quality_score' => 51,
        ]);

        $url = UrlInventory::create([
            'site_id' => $site->id,
            'url' => 'https://example.com/water-damage-restoration-seattle',
            'normalized_url' => 'https://example.com/water-damage-restoration-seattle',
            'path' => '/water-damage-restoration-seattle',
            'depth' => 2,
            'status' => 'completed',
            'word_count' => 220,
            'indexability_status' => 'indexable',
            'page_type' => 'service',
            'internal_link_count' => 2,
            'incoming_link_count' => 1,
            'is_orphan_page' => false,
        ]);

        PageMetadata::create([
            'url_id' => $url->id,
            'title' => null,
            'meta_description' => null,
            'h1' => null,
            'schema' => [],
        ]);

        PerformanceMetric::create([
            'site_id' => $site->id,
            'url' => $url->url,
            'date' => now()->subDays(5),
            'clicks' => 6,
            'impressions' => 500,
            'ctr' => 0.012,
            'average_position' => 7.3,
        ]);

        app(OpportunityMappingEngine::class)->mapSite($site);

        $opportunity = SeoOpportunity::firstOrFail();

        $this->assertSame('optimization_candidate', $opportunity->opportunity_category);
        $this->assertSame('quick_win', $opportunity->opportunity_type);
        $this->assertNotNull($opportunity->demand_score);
        $this->assertNotNull($opportunity->readiness_score);
        $this->assertNotNull($opportunity->business_value_score);
        $this->assertNotNull($opportunity->risk_score);
        $this->assertNotNull($opportunity->total_score);
        $this->assertSame('phase_1_rule_based', $opportunity->score_components['phase']);
        $this->assertStringContainsString('CTR is weak', (string) $opportunity->reason_summary);
        $this->assertStringContainsString('Rewrite title and meta description', (string) $opportunity->recommended_action);
    }

    public function test_it_detects_structural_weakness_for_orphan_like_pages(): void
    {
        [$site, $service, $city, $client] = $this->makeCoverageContext();

        PagePayload::create([
            'site_id' => $site->id,
            'client_id' => $client->id,
            'service_id' => $service->id,
            'location_id' => $city->id,
            'location_type' => 'city',
            'title' => 'Water Damage Restoration Seattle',
            'meta_description' => 'Seattle restoration experts.',
            'slug' => 'water-damage-restoration-seattle',
            'canonical_url_suggestion' => 'https://example.com/water-damage-restoration-seattle',
            'schema_json_ld' => [['@type' => 'Service']],
            'structured_data_type' => 'Service',
            'seo_score' => 81,
            'content_quality_score' => 79,
            'publish_status' => 'published',
            'status' => 'published',
        ]);

        $url = UrlInventory::create([
            'site_id' => $site->id,
            'url' => 'https://example.com/water-damage-restoration-seattle',
            'normalized_url' => 'https://example.com/water-damage-restoration-seattle',
            'path' => '/water-damage-restoration-seattle',
            'depth' => 5,
            'status' => 'completed',
            'word_count' => 850,
            'indexability_status' => 'indexable',
            'page_type' => 'service',
            'internal_link_count' => 1,
            'incoming_link_count' => 0,
            'is_orphan_page' => true,
        ]);

        PageMetadata::create([
            'url_id' => $url->id,
            'title' => 'Water Damage Restoration Seattle',
            'meta_description' => 'Seattle restoration experts.',
            'h1' => 'Water Damage Restoration Seattle',
            'schema' => [['@type' => 'Service']],
        ]);

        app(OpportunityMappingEngine::class)->mapSite($site);

        $opportunity = SeoOpportunity::firstOrFail();

        $this->assertSame('structural_weakness', $opportunity->opportunity_category);
        $this->assertStringContainsString('internal discoverability', (string) $opportunity->reason_summary);
        $this->assertStringContainsString('Increase internal link support', (string) $opportunity->recommended_action);
    }

    protected function makeCoverageContext(): array
    {
        $client = Client::create([
            'name' => 'Example Client',
            'status' => 'active',
        ]);

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
            'population' => 750000,
            'is_priority' => true,
        ]);

        $service = Service::create([
            'name' => 'Water Damage Restoration',
            'slug' => 'water-damage-restoration',
            'is_active' => true,
        ]);

        $site = Site::create([
            'client_id' => $client->id,
            'state_id' => $state->id,
            'name' => 'Example Site',
            'domain' => 'example.com',
            'status' => 'active',
        ]);

        return [$site, $service, $city, $client];
    }
}
