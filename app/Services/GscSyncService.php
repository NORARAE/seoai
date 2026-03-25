<?php

namespace App\Services;

use App\Models\Site;
use App\Models\PerformanceMetric;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\SearchConsole;
use Google\Service\SearchConsole\SearchAnalyticsQueryRequest;
use Illuminate\Support\Facades\Log;

class GscSyncService
{
    protected GoogleClient $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setApplicationName(config('app.name'));
        $this->client->setScopes(['https://www.googleapis.com/auth/webmasters']);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    /**
     * Generate OAuth URL for user to authorize
     */
    public function getAuthorizationUrl(): string
    {
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        
        return $this->client->createAuthUrl();
    }

    /**
     * Exchange authorization code for tokens
     */
    public function exchangeAuthorizationCode(string $code): array
    {
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        
        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    /**
     * Connect a site to GSC with tokens
     */
    public function connectSite(Site $site, string $propertyUrl, array $tokens): bool
    {
        try {
            $site->update([
                'gsc_property_url' => $propertyUrl,
                'gsc_access_token' => encrypt($tokens['access_token']),
                'gsc_refresh_token' => encrypt($tokens['refresh_token'] ?? null),
                'gsc_token_expires_at' => isset($tokens['expires_in']) 
                    ? now()->addSeconds($tokens['expires_in']) 
                    : null,
                'gsc_sync_status' => 'pending',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to connect site to GSC', [
                'site_id' => $site->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Sync performance data for a site
     */
    public function syncSite(Site $site, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        if (!$site->gsc_property_url) {
            return ['success' => false, 'error' => 'Site not connected to GSC'];
        }

        try {
            $site->update(['gsc_sync_status' => 'syncing']);

            // Default to last 30 days if not specified
            $startDate = $startDate ?? now()->subDays(30);
            $endDate = $endDate ?? now()->subDay(); // GSC data has ~2 day delay

            // Authenticate client
            $this->authenticateClient($site);

            // Create Search Console service
            $service = new SearchConsole($this->client);

            // Sync page-level data (URL + date, no query breakdown)
            $pageMetrics = $this->syncPageMetrics($service, $site, $startDate, $endDate);

            // Sync query-level data (URL + query + date)
            $queryMetrics = $this->syncQueryMetrics($service, $site, $startDate, $endDate);

            $site->update([
                'gsc_sync_status' => 'completed',
                'gsc_last_sync_at' => now(),
                'gsc_sync_error' => null,
            ]);

            return [
                'success' => true,
                'page_metrics' => $pageMetrics,
                'query_metrics' => $queryMetrics,
            ];
        } catch (\Exception $e) {
            Log::error('GSC sync failed', [
                'site_id' => $site->id,
                'error' => $e->getMessage(),
            ]);

            $site->update([
                'gsc_sync_status' => 'failed',
                'gsc_sync_error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Sync page-level metrics (aggregated, no query breakdown)
     */
    protected function syncPageMetrics(SearchConsole $service, Site $site, Carbon $startDate, Carbon $endDate): int
    {
        $request = new SearchAnalyticsQueryRequest();
        $request->setStartDate($startDate->format('Y-m-d'));
        $request->setEndDate($endDate->format('Y-m-d'));
        $request->setDimensions(['page', 'date']);
        $request->setRowLimit(25000); // Max per request

        $response = $service->searchanalytics->query($site->gsc_property_url, $request);
        $rows = $response->getRows() ?? [];

        $imported = 0;
        $pageUrlResolver = app(PageUrlResolver::class);

        foreach ($rows as $row) {
            $dimensions = $row->getKeys();
            $url = $dimensions[0];
            $date = $dimensions[1];

            // Try to resolve to existing Page or LocationPage
            $resolved = $pageUrlResolver->resolve($site, $url);

            PerformanceMetric::updateOrCreate(
                [
                    'site_id' => $site->id,
                    'url' => $url,
                    'query' => null, // Page-level aggregate
                    'date' => $date,
                    'device' => null,
                    'country' => null,
                ],
                [
                    'page_id' => $resolved['page_id'] ?? null,
                    'location_page_id' => $resolved['location_page_id'] ?? null,
                    'clicks' => $row->getClicks(),
                    'impressions' => $row->getImpressions(),
                    'ctr' => $row->getCtr(),
                    'average_position' => $row->getPosition(),
                ]
            );

            $imported++;
        }

        return $imported;
    }

    /**
     * Sync query-level metrics (URL + query)
     */
    protected function syncQueryMetrics(SearchConsole $service, Site $site, Carbon $startDate, Carbon $endDate): int
    {
        $request = new SearchAnalyticsQueryRequest();
        $request->setStartDate($startDate->format('Y-m-d'));
        $request->setEndDate($endDate->format('Y-m-d'));
        $request->setDimensions(['page', 'query', 'date']);
        $request->setRowLimit(25000);

        $response = $service->searchanalytics->query($site->gsc_property_url, $request);
        $rows = $response->getRows() ?? [];

        $imported = 0;
        $pageUrlResolver = app(PageUrlResolver::class);

        foreach ($rows as $row) {
            $dimensions = $row->getKeys();
            $url = $dimensions[0];
            $query = $dimensions[1];
            $date = $dimensions[2];

            $resolved = $pageUrlResolver->resolve($site, $url);

            PerformanceMetric::updateOrCreate(
                [
                    'site_id' => $site->id,
                    'url' => $url,
                    'query' => $query,
                    'date' => $date,
                    'device' => null,
                    'country' => null,
                ],
                [
                    'page_id' => $resolved['page_id'] ?? null,
                    'location_page_id' => $resolved['location_page_id'] ?? null,
                    'clicks' => $row->getClicks(),
                    'impressions' => $row->getImpressions(),
                    'ctr' => $row->getCtr(),
                    'average_position' => $row->getPosition(),
                ]
            );

            $imported++;
        }

        return $imported;
    }

    /**
     * Authenticate the Google Client for a site
     */
    protected function authenticateClient(Site $site): void
    {
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));

        $accessToken = decrypt($site->gsc_access_token);
        
        $token = [
            'access_token' => $accessToken,
            'expires_in' => $site->gsc_token_expires_at 
                ? $site->gsc_token_expires_at->diffInSeconds(now()) 
                : 3600,
        ];

        if ($site->gsc_refresh_token) {
            $token['refresh_token'] = decrypt($site->gsc_refresh_token);
        }

        $this->client->setAccessToken($token);

        // Refresh token if expired
        if ($this->client->isAccessTokenExpired() && $site->gsc_refresh_token) {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken(
                decrypt($site->gsc_refresh_token)
            );

            $site->update([
                'gsc_access_token' => encrypt($newToken['access_token']),
                'gsc_token_expires_at' => now()->addSeconds($newToken['expires_in']),
            ]);
        }
    }

    /**
     * List available properties for authenticated client
     */
    public function listProperties(string $accessToken): array
    {
        try {
            $this->client->setAccessToken(['access_token' => $accessToken]);
            $service = new SearchConsole($this->client);
            
            $sites = $service->sites->listSites();
            
            return collect($sites->getSiteEntry())
                ->map(fn($site) => [
                    'url' => $site->getSiteUrl(),
                    'permission' => $site->getPermissionLevel(),
                ])
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to list GSC properties', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function submitSitemap(Site $site, string $sitemapUrl): array
    {
        if (! $site->gsc_property_url) {
            return ['success' => false, 'error' => 'Site not connected to GSC'];
        }

        try {
            $this->authenticateClient($site);

            $service = new SearchConsole($this->client);
            $service->sitemaps->submit($site->gsc_property_url, $sitemapUrl);

            $site->update([
                'gsc_last_sitemap_submission_at' => now(),
                'gsc_last_sitemap_submission_status' => 'submitted',
                'gsc_last_sitemap_submission_error' => null,
            ]);

            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('GSC sitemap submission failed', [
                'site_id' => $site->id,
                'sitemap_url' => $sitemapUrl,
                'error' => $e->getMessage(),
            ]);

            $site->update([
                'gsc_last_sitemap_submission_at' => now(),
                'gsc_last_sitemap_submission_status' => 'failed',
                'gsc_last_sitemap_submission_error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
