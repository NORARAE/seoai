<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\BuildsScanScopedLinks;
use App\Filament\Resources\UrlInventoryResource;
use App\Models\UrlInventory;
use App\Support\CurrentScanResolver;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class UrlInventorySummaryWidget extends BaseWidget
{
    use BuildsScanScopedLinks;

    protected int | string | array $columnSpan = 4;

    protected static ?int $sort = 3;

    protected function getHeading(): ?string
    {
        return 'Pages Found';
    }

    protected function getDescription(): ?string
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());

        if (! $context->siteId()) {
            return 'Choose a site to see what pages the platform can find.';
        }

        if (! $context->scanRunId()) {
            return 'Run a site scan to discover pages for this site.';
        }

        return 'These numbers come from the selected site scan.';
    }

    protected function getStats(): array
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());

        if (! $context->siteId() || ! $context->scanRunId()) {
            return [
                Stat::make('Pages Found', '0')
                    ->description('Run a site scan to discover pages for this site.')
                    ->color('gray')
                    ->url(UrlInventoryResource::getUrl()),
            ];
        }

        $base = UrlInventory::query()
            ->where('site_id', $context->siteId())
            ->where('last_seen_scan_run_id', $context->scanRunId());

        $total = (clone $base)->count();
        $new = UrlInventory::query()
            ->where('site_id', $context->siteId())
            ->where('first_seen_scan_run_id', $context->scanRunId())
            ->count();
        $indexable = (clone $base)->where('indexability_status', 'indexable')->count();

        return [
            Stat::make('Pages Found', (string) $total)
                ->description($total > 0 ? 'Pages found in the selected scan' : 'No pages were found in this scan yet')
                ->url($this->scanScopedUrl(UrlInventoryResource::class, $context)),
            Stat::make('New Pages', (string) $new)
                ->description($new > 0 ? 'First seen in this scan' : 'Run another scan later to spot new pages')
                ->color($new > 0 ? 'success' : 'gray')
                ->url($this->scanScopedUrl(UrlInventoryResource::class, $context)),
            Stat::make('Indexable', (string) $indexable)
                ->description($indexable > 0 ? 'Ready for search visibility review' : 'Pages need more analysis before review')
                ->color($indexable > 0 ? 'success' : 'gray')
                ->url($this->scanScopedUrl(UrlInventoryResource::class, $context)),
        ];
    }
}