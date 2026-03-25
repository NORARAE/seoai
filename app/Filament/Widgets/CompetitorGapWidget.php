<?php

namespace App\Filament\Widgets;

use App\Models\CompetitorDomain;
use App\Models\CompetitorGap;
use App\Services\Discovery\CompetitorPageGapDiscovery;
use App\Services\Discovery\CompetitorScanService;
use App\Services\MarketCaptureEngine;
use App\Support\ActiveSiteContext;
use App\Support\CurrentScanResolver;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CompetitorGapWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        $site = ActiveSiteContext::resolveForUser(Auth::user());
        $state = app(CompetitorScanService::class)->widgetState($site, Auth::user());

        return 'Competitor Opportunities' . ($site ? " for {$site->domain}" : '') . " ({$state['label']})";
    }

    public function table(Table $table): Table
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());
        $state = app(CompetitorScanService::class)->widgetState(ActiveSiteContext::resolveForUser(Auth::user()), Auth::user());

        return $table
            ->query($this->getTableQuery())
            ->description($this->comparisonDescription($context, $state['description']))
            ->headerActions([
                Action::make('add_competitor_domain')
                    ->label('Add Competitor')
                    ->icon('heroicon-o-plus')
                    ->form([
                        TextInput::make('domain')
                            ->required()
                            ->placeholder('competitor.com'),
                    ])
                    ->action(function (array $data, CompetitorScanService $competitorScanService): void {
                        $site = ActiveSiteContext::resolveForUser(Auth::user());

                        if (! $site) {
                            Notification::make()->title('Select an active site first')->warning()->send();
                            return;
                        }

                        $result = $competitorScanService->registerDomain($site, (string) $data['domain'], Auth::user());

                        if ($result['status'] === 'blocked') {
                            Notification::make()
                                ->title('Competitor saved, rescan requires payment')
                                ->body('The domain is saved, but additional competitor scans need purchased scan credits.')
                                ->warning()
                                ->send();
                            return;
                        }

                        Notification::make()
                            ->title('Competitor domain saved')
                            ->body($result['status'] === 'started'
                                ? 'Competitor scan queued automatically.'
                                : 'A competitor scan is already in progress for this domain.')
                            ->success()
                            ->send();
                    }),
                Action::make('rescan_saved_domains')
                    ->label('Rescan Competitors')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->visible(fn (): bool => CompetitorDomain::query()->where('site_id', ActiveSiteContext::resolveForUser(Auth::user())?->id)->exists())
                    ->action(function (CompetitorScanService $competitorScanService): void {
                        $site = ActiveSiteContext::resolveForUser(Auth::user());

                        if (! $site) {
                            Notification::make()->title('Select an active site first')->warning()->send();
                            return;
                        }

                        $result = $competitorScanService->startSavedDomainScans($site, Auth::user());

                        Notification::make()
                            ->title('Competitor rescans processed')
                            ->body("Started {$result['started']} scans, {$result['active']} already running, {$result['blocked']} blocked by payment limits.")
                            ->success()
                            ->send();
                    }),
                Action::make('run_competitor_discovery')
                    ->label('Refresh Comparison')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('primary')
                    ->action(function (CompetitorPageGapDiscovery $discovery): void {
                        $site = ActiveSiteContext::resolveForUser(Auth::user());

                        if (! $site) {
                            Notification::make()->title('Select an active site first')->warning()->send();
                            return;
                        }

                        $result = $discovery->run($site);

                        Notification::make()
                            ->title('Competitor gap discovery complete')
                            ->body("Compared {$result['compared']} scanned site paths and found {$result['current_gaps']} current competitor gaps.")
                            ->success()
                            ->send();
                    }),
            ])
            ->columns([
                TextColumn::make('keyword_topic')
                    ->label('Keyword / Topic')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('search_volume')
                    ->label('Search Volume')
                    ->numeric(),
                TextColumn::make('competitor_domain')
                    ->label('Competitor')
                    ->badge(),
                TextColumn::make('comparison_context')
                    ->label('Comparison')
                    ->state(fn (CompetitorGap $record): string => "Site #{$record->site_scan_run_id} vs competitor #{$record->competitor_scan_run_id}")
                    ->badge()
                    ->color('gray'),
                IconColumn::make('page_missing')
                    ->label('You Need A Page')
                    ->boolean(),
                BadgeColumn::make('score_label')
                    ->label('Opportunity Score')
                    ->colors([
                        'danger' => 'high',
                        'warning' => 'medium',
                        'gray' => 'low',
                    ])
                    ->formatStateUsing(fn (string $state): string => str($state)->title()),
            ])
            ->actions([
                Action::make('generate_page')
                    ->label('Generate Page')
                    ->label('Create Draft')
                    ->icon('heroicon-o-bolt')
                    ->color('primary')
                    ->visible(fn (CompetitorGap $record): bool => $record->status !== 'ignored')
                    ->action(function (CompetitorGap $record, MarketCaptureEngine $engine): void {
                        $opportunity = $engine->generateDraftFromCompetitorGap($record);

                        if ($opportunity) {
                            Notification::make()->title('Draft generation queued')->success()->send();
                            return;
                        }

                        Notification::make()->title('Could not map this gap to service and location')->warning()->send();
                    }),
                Action::make('add_to_queue')
                    ->label('Add To Review Queue')
                    ->icon('heroicon-o-queue-list')
                    ->color('success')
                    ->visible(fn (CompetitorGap $record): bool => $record->status !== 'ignored')
                    ->action(function (CompetitorGap $record, MarketCaptureEngine $engine): void {
                        $opportunity = $engine->queueCompetitorGap($record);

                        if ($opportunity) {
                            Notification::make()->title('Added to opportunity queue')->success()->send();
                            return;
                        }

                        Notification::make()->title('Could not map this gap to service and location')->warning()->send();
                    }),
                Action::make('ignore')
                    ->label('Hide')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->action(function (CompetitorGap $record): void {
                        $record->update(['status' => 'ignored']);
                        Notification::make()->title('Gap ignored')->success()->send();
                    }),
            ])
            ->defaultSort('opportunity_score', 'desc')
            ->emptyStateHeading($this->emptyStateHeading())
            ->emptyStateDescription($this->emptyStateDescription())
            ->poll('45s');
    }

    protected function getTableQuery(): Builder
    {
        $site = ActiveSiteContext::resolveForUser(Auth::user());
        $siteScanRun = $site ? \App\Support\CurrentScanResolver::resolveForSite($site) : null;

        return CompetitorGap::query()
            ->when($site?->id, fn (Builder $query) => $query->where('site_id', $site->id))
            ->when($siteScanRun?->id, fn (Builder $query, int $scanRunId) => $query->where('site_scan_run_id', $scanRunId))
            ->when(! $siteScanRun && $site?->id, fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->where('is_current', true)
            ->whereIn('status', ['open', 'queued', 'generated']);
    }

    protected function emptyStateHeading(): string
    {
        return match ($this->widgetState()['state']) {
            'no_competitor' => 'No competitors added yet',
            'competitor_not_scanned' => 'Competitor scans are not ready yet',
            'scanning' => 'Competitor scans are still running',
            default => 'No competitor opportunities found yet',
        };
    }

    protected function emptyStateDescription(): string
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());

        return $this->comparisonDescription($context, $this->widgetState()['description']);
    }

    /** @return array{state:string,label:string,description:string,tone:string} */
    protected function widgetState(): array
    {
        return app(CompetitorScanService::class)->widgetState(ActiveSiteContext::resolveForUser(Auth::user()), Auth::user());
    }

    protected function comparisonDescription(\App\Support\ScanContext $context, string $baseDescription): string
    {
        if (! $context->siteId()) {
            return 'Choose a site first, then add competitors to compare what they rank for against your site.';
        }

        if (! $context->scanRunId()) {
            return "{$baseDescription} Finish a site scan first. Competitor suggestions only appear after your site has been scanned.";
        }

        return "Comparing your selected site scan with the latest completed competitor scans for each saved domain. {$baseDescription}";
    }
}
