<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\UrlInventoryResource\Pages;
use App\Models\UrlInventory;
use App\Models\User;
use App\Support\CurrentScanResolver;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UrlInventoryResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = UrlInventory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Discovered Pages';

    protected static string|\UnitEnum|null $navigationGroup = 'Scans & Discovery';

    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user instanceof User && ($user->isSuperAdmin() || $user->isOperator());
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (!$user instanceof User) {
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

                if (static::currentScanFilterRequested() && (!$siteId || !$scanRunId)) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->when(
                    $siteId,
                    fn(Builder $query, int $resolvedSiteId): Builder => $query->where('site_id', $resolvedSiteId)
                );
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('site.name')->label('Site')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->limit(80)
                    ->tooltip(fn(UrlInventory $record) => $record->url),
                Tables\Columns\TextColumn::make('scan_visibility')
                    ->label('Scan Status')
                    ->badge()
                    ->state(function (UrlInventory $record): string {
                        $scanRunId = static::resolveTableScanRunId();

                        if (!$scanRunId) {
                            return 'No current scan';
                        }

                        if ($record->last_seen_scan_run_id === $scanRunId) {
                            return $record->first_seen_scan_run_id === $scanRunId ? 'New in current scan' : 'Seen in current scan';
                        }

                        return 'Older scan';
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'New in current scan' => 'success',
                        'Seen in current scan' => 'info',
                        'Older scan' => 'gray',
                        default => 'warning',
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'queued' => 'secondary',
                        'processing' => 'primary',
                        'completed' => 'success',
                        'failed' => 'danger',
                    ]),
                Tables\Columns\BadgeColumn::make('page_type')
                    ->colors([
                        'homepage' => 'success',
                        'category' => 'info',
                        'service' => 'warning',
                        'location' => 'primary',
                        'other' => 'secondary',
                    ]),
                Tables\Columns\BadgeColumn::make('indexability_status')
                    ->label('Indexability')
                    ->color(fn(string $state): string => match ($state) {
                        'indexable' => 'success',
                        'canonicalized' => 'warning',
                        'noindex', 'blocked', 'non_200' => 'danger',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('depth')->sortable(),
                Tables\Columns\TextColumn::make('word_count')->sortable(),
                Tables\Columns\TextColumn::make('crawl_priority')->label('Priority')->sortable(),
                Tables\Columns\TextColumn::make('last_crawled_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'queued' => 'Queued',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('page_type')
                    ->options([
                        'homepage' => 'Homepage',
                        'category' => 'Category',
                        'service' => 'Service',
                        'location' => 'Location',
                        'blog' => 'Blog',
                        'landing' => 'Landing',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('indexability_status')
                    ->options([
                        'indexable' => 'Indexable',
                        'noindex' => 'Noindex',
                        'canonicalized' => 'Canonicalized',
                        'blocked' => 'Blocked',
                        'non_200' => 'Non-200',
                        'unknown' => 'Unknown',
                    ]),
                Tables\Filters\Filter::make('deep_pages')
                    ->label('Depth >= 3')
                    ->query(fn($query) => $query->where('depth', '>=', 3)),
                Tables\Filters\Filter::make('thin_content')
                    ->label('Word count < 250')
                    ->query(fn($query) => $query->where('word_count', '<', 250)),
                Tables\Filters\Filter::make('current_scan')
                    ->label('Seen in current scan')
                    ->form([
                        Forms\Components\Hidden::make('scan_run_id'),
                        Forms\Components\Hidden::make('site_id'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $siteId = static::resolveFilterSiteId($data);
                        $scanRunId = static::resolveFilterScanRunId($data);

                        if (static::currentScanFilterRequested($data) && (!$siteId || !$scanRunId)) {
                            return $query->whereRaw('1 = 0');
                        }

                        if (!$siteId || !$scanRunId) {
                            return $query;
                        }

                        return $query
                            ->where('site_id', $siteId)
                            ->where('last_seen_scan_run_id', $scanRunId);
                    })
                    ->indicateUsing(fn(): ?string => CurrentScanResolver::indicatorForUser(Auth::user(), static::requestedCurrentScanSiteId(), static::requestedCurrentScanRunId())),
            ])
            ->emptyStateHeading(static::currentScanFilterRequested()
                ? 'No pages were captured in this snapshot yet'
                : 'No discovered pages found')
            ->emptyStateDescription(static::currentScanFilterRequested()
                ? 'If a scan is still running, check Scan Progress. If not, open Scan History and choose another snapshot.'
                : 'No discovered pages match the current filters.')
            ->defaultSort('crawl_priority', 'desc')
            ->poll('20s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUrlInventories::route('/'),
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
