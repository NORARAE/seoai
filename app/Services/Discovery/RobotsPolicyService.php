<?php

namespace App\Services\Discovery;

use App\Models\CrawlPolicy;
use App\Models\Site;
use App\Models\SiteCrawlSetting;
use Illuminate\Support\Facades\Http;

class RobotsPolicyService
{
    public function __construct(
        protected RobotsTxtParser $parser,
        protected UrlNormalizer $normalizer,
    ) {}

    public function refreshPolicy(Site $site): CrawlPolicy
    {
        $robotsUrl = 'https://' . ltrim($site->domain, '/') . '/robots.txt';

        $response = Http::timeout(20)
            ->withUserAgent('SEOAIco/1.0 (SEO Crawler)')
            ->get($robotsUrl);

        $content = $response->successful() ? $response->body() : '';
        $parsed = $this->parser->parse($content);

        return CrawlPolicy::updateOrCreate(
            ['site_id' => $site->id],
            [
                'robots_txt' => $content,
                'allow_rules' => $parsed['allow_rules'],
                'disallow_rules' => $parsed['disallow_rules'],
                'sitemap_urls' => $parsed['sitemap_urls'],
                'crawl_delay' => $parsed['crawl_delay'],
                'last_fetched_at' => now(),
            ],
        );
    }

    public function getOrRefreshPolicy(Site $site): CrawlPolicy
    {
        $policy = CrawlPolicy::where('site_id', $site->id)->first();

        if (! $policy || $policy->last_fetched_at?->lt(now()->subDay()) !== false) {
            return $this->refreshPolicy($site);
        }

        return $policy;
    }

    public function getEffectiveDelay(Site $site): int
    {
        $settings = SiteCrawlSetting::firstOrCreate(
            ['site_id' => $site->id],
            [
                'max_pages' => 2000,
                'crawl_delay' => 1,
                'max_depth' => 4,
                'obey_robots' => true,
                'follow_nofollow' => false,
            ],
        );

        $policy = CrawlPolicy::where('site_id', $site->id)->first();
        $policyDelay = $policy?->crawl_delay ?? 1;

        return max(1, $settings->crawl_delay, $policyDelay);
    }

    public function canRequestNow(Site $site): bool
    {
        $policy = CrawlPolicy::where('site_id', $site->id)->first();

        if (! $policy || ! $policy->last_request_at) {
            return true;
        }

        $delay = $this->getEffectiveDelay($site);

        return $policy->last_request_at->addSeconds($delay)->lte(now());
    }

    public function touchLastRequest(Site $site): void
    {
        CrawlPolicy::updateOrCreate(
            ['site_id' => $site->id],
            ['last_request_at' => now()],
        );
    }

    public function backoffDelay(Site $site): int
    {
        $policy = $this->getOrRefreshPolicy($site);
        $newDelay = min(120, max(1, (int) $policy->crawl_delay) * 2);

        $policy->update(['crawl_delay' => $newDelay]);

        return $newDelay;
    }

    public function isAllowed(Site $site, string $url, bool $override = false): bool
    {
        if ($override) {
            return true;
        }

        $settings = SiteCrawlSetting::where('site_id', $site->id)->first();

        if ($settings && ! $settings->obey_robots) {
            return true;
        }

        $policy = $this->getOrRefreshPolicy($site);
        $path = parse_url($url, PHP_URL_PATH) ?: '/';

        return $this->parser->isAllowed($path, $policy->allow_rules ?? [], $policy->disallow_rules ?? []);
    }
}
