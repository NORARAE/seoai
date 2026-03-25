<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\PagePayload;
use App\Models\Site;
use App\Models\UrlInventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_index_and_child_xml_include_expected_urls(): void
    {
        $client = Client::create([
            'name' => 'BioNW Client',
            'email' => 'client@example.com',
            'status' => 'active',
        ]);

        $site = Site::create([
            'client_id' => $client->id,
            'name' => 'BioNW',
            'domain' => 'bionw.com',
            'status' => 'active',
            'sitemap_enabled' => true,
            'sitemap_include_payload_pages' => true,
            'sitemap_include_discovered_pages' => true,
            'sitemap_manual_include_urls' => "/manual-page\n",
            'sitemap_manual_exclude_urls' => "/ignore-me\n",
            'sitemap_max_urls_per_file' => 500,
        ]);

        PagePayload::create([
            'site_id' => $site->id,
            'client_id' => $client->id,
            'title' => 'Emergency Restoration',
            'slug' => 'services/emergency-restoration',
            'canonical_url_suggestion' => 'https://bionw.com/services/emergency-restoration',
            'publish_status' => 'published',
            'status' => 'published',
        ]);

        PagePayload::create([
            'site_id' => $site->id,
            'client_id' => $client->id,
            'title' => 'Rejected Draft',
            'slug' => 'rejected-draft',
            'canonical_url_suggestion' => 'https://bionw.com/rejected-draft',
            'publish_status' => 'failed',
            'status' => 'draft',
        ]);

        UrlInventory::create([
            'site_id' => $site->id,
            'url' => 'https://bionw.com/about-us',
            'normalized_url' => 'https://bionw.com/about-us',
            'path' => '/about-us',
            'status' => 'completed',
            'indexability_status' => 'indexable',
        ]);

        UrlInventory::create([
            'site_id' => $site->id,
            'url' => 'https://bionw.com/noindex-page',
            'normalized_url' => 'https://bionw.com/noindex-page',
            'path' => '/noindex-page',
            'status' => 'completed',
            'indexability_status' => 'noindex',
        ]);

        UrlInventory::create([
            'site_id' => $site->id,
            'url' => 'https://bionw.com/canonicalized-page',
            'normalized_url' => 'https://bionw.com/canonicalized-page',
            'path' => '/canonicalized-page',
            'status' => 'completed',
            'indexability_status' => 'canonicalized',
        ]);

        UrlInventory::create([
            'site_id' => $site->id,
            'url' => 'https://bionw.com/ignore-me',
            'normalized_url' => 'https://bionw.com/ignore-me',
            'path' => '/ignore-me',
            'status' => 'completed',
            'indexability_status' => 'indexable',
        ]);

        $indexResponse = $this->get(route('public.sitemaps.index', ['site' => $site]));

        $indexResponse->assertOk();
        $indexXml = simplexml_load_string($indexResponse->getContent());

        $this->assertNotFalse($indexXml);
        $this->assertSame('sitemapindex', $indexXml->getName());
        $this->assertCount(1, $indexXml->sitemap);

        $pageResponse = $this->get(route('public.sitemaps.page', ['site' => $site, 'page' => 1]));

        $pageResponse->assertOk();
        $pageXml = simplexml_load_string($pageResponse->getContent());
        $this->assertNotFalse($pageXml);
        $this->assertSame('urlset', $pageXml->getName());

        $pageContent = $pageResponse->getContent();

        $this->assertStringContainsString('https://bionw.com/services/emergency-restoration', $pageContent);
        $this->assertStringContainsString('https://bionw.com/about-us', $pageContent);
        $this->assertStringContainsString('https://bionw.com/manual-page', $pageContent);
        $this->assertStringNotContainsString('https://bionw.com/rejected-draft', $pageContent);
        $this->assertStringNotContainsString('https://bionw.com/noindex-page', $pageContent);
        $this->assertStringNotContainsString('https://bionw.com/canonicalized-page', $pageContent);
        $this->assertStringNotContainsString('https://bionw.com/ignore-me', $pageContent);
    }

    public function test_sitemap_index_splits_large_sets_into_multiple_child_files(): void
    {
        $client = Client::create([
            'name' => 'Split Client',
            'email' => 'split@example.com',
            'status' => 'active',
        ]);

        $site = Site::create([
            'client_id' => $client->id,
            'name' => 'Split Site',
            'domain' => 'split-site.com',
            'status' => 'active',
            'sitemap_enabled' => true,
            'sitemap_include_payload_pages' => false,
            'sitemap_include_discovered_pages' => true,
            'sitemap_max_urls_per_file' => 2,
        ]);

        foreach (range(1, 5) as $index) {
            UrlInventory::create([
                'site_id' => $site->id,
                'url' => "https://split-site.com/page-{$index}",
                'normalized_url' => "https://split-site.com/page-{$index}",
                'path' => "/page-{$index}",
                'status' => 'completed',
                'indexability_status' => 'indexable',
            ]);
        }

        $indexResponse = $this->get(route('public.sitemaps.index', ['site' => $site]));

        $indexResponse->assertOk();
        $indexXml = simplexml_load_string($indexResponse->getContent());

        $this->assertNotFalse($indexXml);
        $this->assertCount(3, $indexXml->sitemap);

        $firstPageXml = simplexml_load_string(
            $this->get(route('public.sitemaps.page', ['site' => $site, 'page' => 1]))->getContent()
        );
        $thirdPageXml = simplexml_load_string(
            $this->get(route('public.sitemaps.page', ['site' => $site, 'page' => 3]))->getContent()
        );

        $this->assertNotFalse($firstPageXml);
        $this->assertNotFalse($thirdPageXml);
        $this->assertCount(2, $firstPageXml->url);
        $this->assertCount(1, $thirdPageXml->url);
    }
}