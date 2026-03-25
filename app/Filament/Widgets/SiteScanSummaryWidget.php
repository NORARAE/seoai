<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\BuildsScanScopedLinks;
use App\Filament\Resources\CrawlQueueResource;
use App\Filament\Resources\Clients\ClientResource;
use App\Jobs\DispatchCrawlQueueJob;
use App\Filament\Resources\SeoOpportunityResource;
use App\Filament\Resources\UrlInventoryResource;
use App\Filament\Resources\Sites\SiteResource;
use Filament\Actions\Action;
use App\Models\CrawlQueue;
use App\Models\Site;
use App\Models\User;
use App\Support\ActiveSiteContext;
use App\Support\CurrentScanResolver;
use App\Support\ScanContext;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class SiteScanSummaryWidget extends Widget
{
    use BuildsScanScopedLinks;

    protected string $view = 'filament.widgets.site-scan-summary-widget';

    protected static ?int $sort = 0;

    protected static ?string $pollingInterval = '5s';

    protected int | string | array $columnSpan = 12;

    public ?int $selectedSiteId = null;

    public function mount(): void
    {
        $activeSite = ActiveSiteContext::resolveForUser(Auth::user());
        $this->selectedSiteId = $activeSite?->id;
    }

    public function updatedSelectedSiteId(?int $siteId): void
    {
        $this->applySelectedSite($siteId);
    }

    public function selectSite(int $siteId): void
    {
        $this->selectedSiteId = $siteId;
        $this->applySelectedSite($siteId);
    }

    public function startSiteScan(): void
    {
        $site = $this->resolveSelectedSiteForScan();

        if (! $site) {
            return;
        }

        ActiveSiteContext::setActiveSiteId($site->id);
        ActiveSiteContext::syncUserToSite(Auth::user(), $site);
        $this->selectedSiteId = $site->id;

        $requeuedFailed = CrawlQueue::query()
            ->where('site_id', $site->id)
            ->where('status', 'failed')
            ->update([
                'status' => 'queued',
                'error_message' => null,
                'attempts' => 0,
                'available_at' => now(),
                'updated_at' => now(),
            ]);

        Cache::put($this->scanRequestedCacheKey($site->id), now()->toDateTimeString(), now()->addMinutes(30));

        Artisan::call('discovery:start', ['site' => $site->id]);

        Notification::make()
            ->title('Site scan started')
            ->body($requeuedFailed > 0
                ? "Restarted discovery for {$site->domain} and re-queued {$requeuedFailed} failed URLs"
                : "Tracking live crawl progress below for {$site->domain}")
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }

    public function runAnotherScan(): void
    {
        $this->startSiteScan();
    }

    public function resumeStalledScan(): void
    {
        $site = ActiveSiteContext::resolveForUser(Auth::user());

        if (! $site) {
            Notification::make()->title('No active site')->body('Select a site first, then retry dispatch.')->warning()->send();
            return;
        }

        $site->forceFill(['crawl_status' => 'processing'])->save();

        DispatchCrawlQueueJob::dispatch($site->id, 100)->onQueue('crawl');

        Notification::make()
            ->title('Crawl dispatch retried')
            ->body("Queued additional crawl dispatch for {$site->domain}")
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }

    protected function resolveSelectedSiteForScan(): ?Site
    {
        if (! $this->selectedSiteId) {
            $activeSite = ActiveSiteContext::resolveForUser(Auth::user());

            if ($activeSite) {
                $this->selectedSiteId = $activeSite->id;
            }
        }

        if (! $this->selectedSiteId) {
            Notification::make()->title('No active site selected')->body('Select Active Site first, then start the scan.')->warning()->send();
            return null;
        }

        $selectedSite = $this->accessibleSites()->firstWhere('id', $this->selectedSiteId);

        if (! $selectedSite) {
            Notification::make()->title('Site not accessible')->body('Select a site that belongs to your client context.')->danger()->send();
            return null;
        }

        return $selectedSite;
    }

    /**
     * @return \Illuminate\Support\Collection<int, Site>
     */
    protected function accessibleSites()
    {
        /** @var User|null $user */
        $user = Auth::user();

        return ActiveSiteContext::accessibleSitesForUser($user);
    }

    protected function getViewData(): array
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());
        $site = $context->site;
        $siteId = $context->siteId();
        $sites = $this->accessibleSites();
        $resolvedClientId = ActiveSiteContext::resolveClientIdForUser(Auth::user());
        $heroActions = $this->heroActions($context, $sites->count());

        return [
            'context' => $context,
            'activeSite' => $site,
            'sites' => $sites,
            'siteCount' => $sites->count(),
            'siteSelectorHelp' => $this->siteSelectorHelp($sites->count()),
            'heroPrimaryAction' => $heroActions['primary'],
            'heroSecondaryActions' => $heroActions['secondary'],
            'nextStep' => $this->nextStep($context),
            'crawlQueueUrl' => $this->explicitScanScopedUrl(
                CrawlQueueResource::class,
                $siteId,
                $context->activeScanRunId() ?? $context->scanRunId(),
            ),
            'urlInventoryUrl' => $context->hasMetricsScan()
                ? $this->scanScopedUrl(UrlInventoryResource::class, $context)
                : UrlInventoryResource::getUrl(),
            'opportunitiesUrl' => $context->hasMetricsScan()
                ? $this->scanScopedUrl(SeoOpportunityResource::class, $context)
                : SeoOpportunityResource::getUrl(),
            'clientsUrl' => ClientResource::getUrl(),
            'sitesUrl' => SiteResource::getUrl(),
            'addSiteUrl' => SiteResource::getUrl('create', array_filter(['client_id' => $resolvedClientId])),
            'resolvedClientId' => $resolvedClientId,
            'showSidebarScanProgress' => $siteId !== null && ($context->activeScanRunId() !== null || $context->scanRunId() !== null),
        ];
    }

    /**
     * @return array{
     *     primary:?array{type:string,label:string,value:string,color:string},
     *     secondary:list<array{type:string,label:string,value:string,color:string}>
     * }
     */
    protected function heroActions(ScanContext $context, int $siteCount): array
    {
        if ($siteCount <= 0) {
            return [
                'primary' => ['type' => 'url', 'label' => 'Add Site', 'value' => SiteResource::getUrl('create', array_filter(['client_id' => ActiveSiteContext::resolveClientIdForUser(Auth::user())])), 'color' => 'primary'],
                'secondary' => [
                    ['type' => 'url', 'label' => 'Manage Sites', 'value' => SiteResource::getUrl(), 'color' => 'gray'],
                ],
            ];
        }

        if (! $context->siteId()) {
            return ['primary' => null, 'secondary' => []];
        }

        return match ($context->state) {
            'idle' => [
                'primary' => ['type' => 'method', 'label' => 'Start Scan', 'value' => 'startSiteScan', 'color' => 'primary'],
                'secondary' => [],
            ],
            'scanning' => [
                'primary' => ['type' => 'url', 'label' => 'Open Scan Progress', 'value' => $this->explicitScanScopedUrl(CrawlQueueResource::class, $context->siteId(), $context->activeScanRunId() ?? $context->scanRunId()), 'color' => 'primary'],
                'secondary' => [],
            ],
            'stalled' => [
                'primary' => ['type' => 'method', 'label' => 'Resume Scan', 'value' => 'resumeStalledScan', 'color' => 'danger'],
                'secondary' => [
                    ['type' => 'url', 'label' => 'Open Scan Progress', 'value' => $this->explicitScanScopedUrl(CrawlQueueResource::class, $context->siteId(), $context->activeScanRunId() ?? $context->scanRunId()), 'color' => 'gray'],
                ],
            ],
            'limited' => [
                'primary' => ['type' => 'anchor', 'label' => 'Review Limits', 'value' => '#plan-and-limits', 'color' => 'primary'],
                'secondary' => [],
            ],
            default => [
                'primary' => ['type' => 'url', 'label' => 'Review Discovered Pages', 'value' => $this->scanScopedUrl(UrlInventoryResource::class, $context), 'color' => 'primary'],
                'secondary' => [
                    ['type' => 'url', 'label' => 'Review Opportunities', 'value' => $this->scanScopedUrl(SeoOpportunityResource::class, $context), 'color' => 'gray'],
                ],
            ],
        };
    }

    protected function scanRequestedCacheKey(int $siteId): string
    {
        return "site-scan-requested:{$siteId}";
    }

    protected function applySelectedSite(?int $siteId): void
    {
        if (! $siteId) {
            return;
        }

        $site = $this->accessibleSites()->firstWhere('id', $siteId);

        if (! $site) {
            Notification::make()->title('Site not accessible')->danger()->send();
            return;
        }

        ActiveSiteContext::setActiveSiteId($site->id);
        ActiveSiteContext::syncUserToSite(Auth::user(), $site);

        Notification::make()
            ->title('Active site updated')
            ->body("You are now working on {$site->domain}")
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }

    protected function siteSelectorHelp(int $siteCount): string
    {
        return match (true) {
            $siteCount <= 0 => 'No sites are connected yet. Add a site to start scanning and finding opportunities.',
            $siteCount === 1 => 'You currently have one accessible site. Add another site when you are ready to scan a second website.',
            default => 'Choose the site you want this workspace to follow. Scan progress, discovered pages, and opportunities will update for the selected site.',
        };
    }

    /** @return array{title:string,body:string,actionLabel:string,actionType:string,actionValue:string} */
    protected function nextStep(ScanContext $context): array
    {
        return match ($context->state) {
            'idle' => [
                'title' => 'Next step: run the first scan',
                'body' => 'Start a scan to create the first site snapshot. Once it finishes, you can review discovered pages and recommended opportunities.',
                'actionLabel' => 'Start Scan',
                'actionType' => 'method',
                'actionValue' => 'startSiteScan',
            ],
            'scanning' => [
                'title' => 'Next step: monitor scan progress',
                'body' => 'This scan is still running. Open Scan Progress to see what is queued, processing, or stalled while this workspace stays pinned to the latest completed snapshot.',
                'actionLabel' => 'Open Scan Progress',
                'actionType' => 'url',
                'actionValue' => $this->explicitScanScopedUrl(CrawlQueueResource::class, $context->siteId(), $context->activeScanRunId() ?? $context->scanRunId()),
            ],
            'stalled' => [
                'title' => 'Next step: resume the scan',
                'body' => 'Queued work remains, but progress has stopped. Resume the scan, then return here once activity restarts.',
                'actionLabel' => 'Resume Scan',
                'actionType' => 'method',
                'actionValue' => 'resumeStalledScan',
            ],
            'limited' => [
                'title' => 'Next step: review scan limits',
                'body' => 'This scan reached the current page allowance. Review limits and competitor credits below before rerunning or extending work.',
                'actionLabel' => 'Review Limits',
                'actionType' => 'anchor',
                'actionValue' => '#plan-and-limits',
            ],
            default => [
                'title' => 'Next step: review discovered pages',
                'body' => 'Start with discovered pages to confirm what the system found, then move into SEO Opportunities to review what should be created, improved, or fixed.',
                'actionLabel' => 'Review Discovered Pages',
                'actionType' => 'url',
                'actionValue' => $context->hasMetricsScan() ? $this->scanScopedUrl(UrlInventoryResource::class, $context) : UrlInventoryResource::getUrl(),
            ],
        };
    }

}
