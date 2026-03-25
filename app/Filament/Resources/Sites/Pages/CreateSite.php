<?php

namespace App\Filament\Resources\Sites\Pages;

use App\Filament\Resources\SiteCrawlSettingResource;
use App\Filament\Resources\Sites\SiteResource;
use App\Jobs\StartSiteDiscoveryJob;
use App\Models\Site;
use App\Models\SiteCrawlSetting;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSite extends CreateRecord
{
    protected static string $resource = SiteResource::class;

    /** @var array{start_initial_scan: bool, initial_scan_depth_limit: int} */
    protected array $discoveryOptions = [
        'start_initial_scan' => true,
        'initial_scan_depth_limit' => 4,
    ];

    public function getSubheading(): ?string
    {
        return 'Add the site, then run discovery. Advanced crawl and sitemap controls can be refined later.';
    }

    /** @param array<string, mixed> $data */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->discoveryOptions = [
            'start_initial_scan' => (bool) ($data['start_initial_scan'] ?? true),
            'initial_scan_depth_limit' => max(0, (int) ($data['initial_scan_depth_limit'] ?? 4)),
        ];

        unset($data['start_initial_scan'], $data['initial_scan_depth_limit']);

        return $data;
    }

    protected function afterCreate(): void
    {
        SiteCrawlSetting::query()->updateOrCreate(
            ['site_id' => $this->record->id],
            [
                'max_pages' => 2000,
                'crawl_delay' => 1,
                'max_depth' => $this->discoveryOptions['initial_scan_depth_limit'],
                'obey_robots' => true,
                'follow_nofollow' => false,
            ],
        );

        if ($this->discoveryOptions['start_initial_scan']) {
            StartSiteDiscoveryJob::dispatch(
                $this->record->id,
                triggeredByType: 'manual',
                initiatedBy: Auth::id(),
            )->onQueue('crawl');

            Notification::make()
                ->title('Site connected and initial scan queued')
                ->body('Discovery will start with the saved sitemap preference and depth limit. You can refine advanced settings later from Crawl Settings or Site edit.')
                ->success()
                ->send();

            return;
        }

        Notification::make()
            ->title('Site connected')
            ->body('You can start discovery when ready. Advanced crawl settings remain available later in Crawl Settings and Site edit.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return SiteResource::getUrl('view', ['record' => $this->record]);
    }
}
