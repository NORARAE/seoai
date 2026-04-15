<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\CrawlQueueResource\Pages;
use App\Jobs\ProcessCrawlQueueItemJob;
use App\Models\CrawlQueue;
use App\Models\User;
use App\Support\CurrentScanResolver;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CrawlQueueResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = CrawlQueue::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationLabel = 'Scan Progress';

    protected static string|\UnitEnum|null $navigationGroup = 'Scans & Discovery';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user instanceof User && ($user->isAdmin() || $user->isOperator());
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isSuperAdmin()) {
            return $query;
        }

        $siteIds = $user->accessibleSites()->pluck('sites.id');

        if ($siteIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('site_id', $siteIds);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $siteId = static::resolveTableSiteId();
                $scanRunId = static::resolveTableScanRunId();

                if (static::currentScanFilterRequested() && (! $siteId || ! $scanRunId)) {
                    return $query->whereRaw('1 = 0');
                }

                return $query
                    ->when($siteId, fn (Builder $query, int $resolvedSiteId): Builder => $query->where('site_id', $resolvedSiteId))
                    ->when($scanRunId, fn (Builder $query, int $resolvedScanRunId): Builder => $query->where('scan_run_id', $resolvedScanRunId));
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('site.name')->label('Site')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('scan_run_id')->label('Scan')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('url')->searchable()->limit(80),
                Tables\Columns\TextColumn::make('priority')->sortable(),
                Tables\Columns\TextColumn::make('depth')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'queued',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('attempts')->sortable(),
                Tables\Columns\TextColumn::make('available_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('last_attempted_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('error_message')->limit(60)->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('active_only')
                    ->label('Active only (hide completed)')
                    ->query(fn (Builder $query) => $query->whereIn('status', ['queued', 'processing', 'failed'])),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'queued' => 'Queued',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\Filter::make('current_scan')
                    ->label('From selected scan')
                    ->form([
                        Forms\Components\Hidden::make('scan_run_id'),
                        Forms\Components\Hidden::make('site_id'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $siteId = static::resolveFilterSiteId($data);
                        $scanRunId = static::resolveFilterScanRunId($data);

                        if (static::currentScanFilterRequested($data) && (! $siteId || ! $scanRunId)) {
                            return $query->whereRaw('1 = 0');
                        }

                        if (! $siteId || ! $scanRunId) {
                            return $query;
                        }

                        return $query
                            ->where('site_id', $siteId)
                            ->where('scan_run_id', $scanRunId);
                    })
                    ->indicateUsing(fn (): ?string => CurrentScanResolver::indicatorForUser(Auth::user(), static::requestedCurrentScanSiteId(), static::requestedCurrentScanRunId())),
                Tables\Filters\Filter::make('ready_now')
                    ->label('Ready now')
                    ->query(fn (Builder $query) => $query->where('status', 'queued')->where(function ($q) {
                        $q->whereNull('available_at')->orWhere('available_at', '<=', now());
                    })),
            ])
            ->actions([
                Action::make('retry')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (CrawlQueue $record) => in_array($record->status, ['failed', 'queued'], true))
                    ->action(function (CrawlQueue $record): void {
                        $record->update([
                            'status' => 'queued',
                            'error_message' => null,
                            'available_at' => now(),
                        ]);

                        ProcessCrawlQueueItemJob::dispatch($record->id)->onQueue('crawl');

                        Notification::make()->title('Retry queued')->success()->send();
                    }),
            ])
            ->emptyStateHeading(static::currentScanFilterRequested()
                ? 'No scan-progress items were recorded for this snapshot'
                : 'No scan-progress items found')
            ->emptyStateDescription(static::currentScanFilterRequested()
                ? 'If a scan should be running, return to Command Center and start or resume it.'
                : 'No scan-progress items match the current filters.')
            ->defaultSort('id', 'desc')
            ->poll('10s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCrawlQueues::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    protected static function requestedCurrentScanRunId(): ?int
    {
        $scanRunId = (int) data_get(request()->input('tableFilters', []), 'current_scan.scan_run_id');

        return $scanRunId > 0 ? $scanRunId : null;
    }

    protected static function requestedCurrentScanSiteId(): ?int
    {
        $siteId = (int) data_get(request()->input('tableFilters', []), 'current_scan.site_id');

        return $siteId > 0 ? $siteId : null;
    }

    protected static function resolveTableSiteId(): ?int
    {
        return CurrentScanResolver::resolveSiteForUser(Auth::user(), static::requestedCurrentScanSiteId())?->id;
    }

    protected static function resolveTableScanRunId(): ?int
    {
        return CurrentScanResolver::resolveIdForUser(
            Auth::user(),
            static::requestedCurrentScanSiteId(),
            static::requestedCurrentScanRunId(),
        );
    }

    /** @param array<string, mixed> $data */
    protected static function resolveFilterSiteId(array $data): ?int
    {
        $explicitSiteId = (int) ($data['site_id'] ?? 0);

        return CurrentScanResolver::resolveSiteForUser(Auth::user(), $explicitSiteId > 0 ? $explicitSiteId : null)?->id;
    }

    /** @param array<string, mixed> $data */
    protected static function resolveFilterScanRunId(array $data): ?int
    {
        $explicitSiteId = (int) ($data['site_id'] ?? 0);
        $explicitScanRunId = (int) ($data['scan_run_id'] ?? 0);

        return CurrentScanResolver::resolveIdForUser(
            Auth::user(),
            $explicitSiteId > 0 ? $explicitSiteId : null,
            $explicitScanRunId > 0 ? $explicitScanRunId : null,
        );
    }

    /** @param array<string, mixed>|null $data */
    protected static function currentScanFilterRequested(?array $data = null): bool
    {
        $siteId = (int) ($data['site_id'] ?? static::requestedCurrentScanSiteId() ?? 0);
        $scanRunId = (int) ($data['scan_run_id'] ?? static::requestedCurrentScanRunId() ?? 0);

        return $siteId > 0 || $scanRunId > 0;
    }
}
