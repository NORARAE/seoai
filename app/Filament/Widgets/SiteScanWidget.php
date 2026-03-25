<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Sites\SiteResource;
use App\Models\Site;
use App\Models\CrawlQueue;
use App\Models\User;
use App\Support\ActiveSiteContext;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class SiteScanWidget extends Widget
{
    protected string $view = 'filament.widgets.site-scan-widget';

    protected static ?int $sort = -1;

    protected int | string | array $columnSpan = 'full';

    public ?int $selectedSiteId = null;

    public function mount(): void
    {
        $activeSite = ActiveSiteContext::resolveForUser(Auth::user());

        $this->selectedSiteId = $activeSite?->id;
    }

    public function updatedSelectedSiteId(?int $siteId): void
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

        Notification::make()
            ->title('Active site updated')
            ->body("Dashboard context is now {$site->domain}")
            ->success()
            ->send();

        $this->dispatch('$refresh');
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

        Artisan::call('discovery:start', ['site' => $site->id]);

        Notification::make()
            ->title('Site scan started')
            ->body($requeuedFailed > 0
                ? "Restarted discovery for {$site->domain} and re-queued {$requeuedFailed} failed URLs"
                : "Discovery has started for {$site->domain}")
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
            Notification::make()
                ->title('No active site selected')
                ->body('Select Active Site first, then start the scan.')
                ->warning()
                ->send();

            return null;
        }

        $selectedSite = $this->accessibleSites()->firstWhere('id', $this->selectedSiteId);

        if (! $selectedSite) {
            Notification::make()
                ->title('Site not accessible')
                ->body('Select a site that belongs to your account context.')
                ->danger()
                ->send();

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
        $activeSite = ActiveSiteContext::resolveForUser(Auth::user());

        return [
            'activeSite' => $activeSite,
            'sites' => $this->accessibleSites(),
            'clientsUrl' => ClientResource::getUrl(),
            'sitesUrl' => SiteResource::getUrl(),
            'resolvedClientId' => ActiveSiteContext::resolveClientIdForUser(Auth::user()),
        ];
    }
}
