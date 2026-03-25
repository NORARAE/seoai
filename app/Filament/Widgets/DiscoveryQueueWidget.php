<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CrawlQueueResource;
use App\Models\CrawlQueue;
use App\Support\ActiveSiteContext;
use App\Support\CurrentScanResolver;
use App\Filament\Concerns\BuildsScanScopedLinks;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class DiscoveryQueueWidget extends Widget
{
    use BuildsScanScopedLinks;

    protected string $view = 'filament.widgets.discovery-queue-widget';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 4;

    public function startCrawl(): void
    {
        $site = ActiveSiteContext::resolveForUser(Auth::user());

        if (! $site) {
            Notification::make()->title('No site found')->danger()->send();

            return;
        }

        Artisan::call('discovery:start', ['site' => $site->id]);

        Notification::make()
            ->title('Site scan started')
            ->body("Queued the first scan steps for {$site->domain}")
            ->success()
            ->send();
    }

    public function dispatchQueue(): void
    {
        $site = ActiveSiteContext::resolveForUser(Auth::user());

        Artisan::call('crawl:dispatch', [
            '--site_id' => $site?->id,
            '--limit' => 50,
        ]);

        Notification::make()
            ->title('Scan progress nudged')
            ->body($site ? "Queued another batch of page scans for {$site->domain}" : 'Queued another batch of page scans')
            ->success()
            ->send();
    }

    public function syncOpportunities(): void
    {
        $site = ActiveSiteContext::resolveForUser(Auth::user());

        Artisan::call('discovery:sync-opportunities', [
            'site' => $site?->id,
        ]);

        Notification::make()
            ->title('Opportunity review refreshed')
            ->body(trim(Artisan::output()) ?: 'Coverage and opportunity sync executed')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());
        $site = $context->site;

        return [
            'context' => $context,
            'queued' => $context->queued,
            'processing' => $context->processing,
            'failed' => $context->activeScanRunId()
                ? CrawlQueue::query()->where('scan_run_id', $context->activeScanRunId())->where('status', 'failed')->count()
                : 0,
            'lastActivityAt' => $context->lastActivityAt,
            'isStalled' => $context->isStalled,
            'activeSiteDomain' => $site?->domain,
            'crawlQueueUrl' => $this->explicitScanScopedUrl(CrawlQueueResource::class, $context->siteId(), $context->activeScanRunId() ?? $context->scanRunId()),
            'showStartScan' => $context->siteId() !== null && $context->activeScanRunId() === null && ! $context->isLimited,
            'showRetryProgress' => $context->siteId() !== null && ($context->activeScanRunId() !== null || $context->isStalled),
            'showRefreshOpportunities' => $context->hasMetricsScan(),
        ];
    }
}
