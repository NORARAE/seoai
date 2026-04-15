<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\ScanRunResource\Pages;
use App\Filament\Resources\ScanRunResource\RelationManagers\CrawlQueueItemsRelationManager;
use App\Filament\Resources\ScanRunResource\RelationManagers\FirstSeenUrlsRelationManager;
use App\Filament\Resources\ScanRunResource\RelationManagers\SeoOpportunitiesRelationManager;
use App\Filament\Resources\Sites\SiteResource;
use App\Models\ScanRun;
use App\Models\User;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ScanRunResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = ScanRun::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Scan History';

    protected static string|\UnitEnum|null $navigationGroup = 'Scans & Discovery';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'id';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user instanceof User && ($user->isAdmin() || $user->isOperator());
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('site');
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
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('site.domain')
                    ->label('Site')
                    ->url(fn (ScanRun $record): string => SiteResource::getUrl('view', ['record' => $record->site_id]))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'running' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        'pending' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('duration')
                    ->state(fn (ScanRun $record): string => static::formatDuration($record))
                    ->label('Duration'),
                Tables\Columns\TextColumn::make('pages_crawled')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pages_failed')
                    ->sortable(),
                Tables\Columns\TextColumn::make('opportunities_found')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('active_only')
                    ->label('Active only')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'running')),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'running' => 'Running',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('site_id')
                    ->relationship('site', 'domain')
                    ->label('Site'),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->emptyStateHeading('No scan history yet')
            ->emptyStateDescription('Start the first site scan from Command Center to create a snapshot for this site.')
            ->recordClasses(fn (ScanRun $record): ?string => $record->status === 'running'
                ? 'bg-warning-50/60 ring-1 ring-inset ring-warning-300 dark:bg-warning-950/20 dark:ring-warning-800'
                : null)
            ->defaultSort('id', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Run Overview')
                    ->schema([
                        TextEntry::make('site.domain')
                            ->label('Site')
                            ->url(fn (ScanRun $record): string => SiteResource::getUrl('view', ['record' => $record->site_id])),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'running' => 'warning',
                                'completed' => 'success',
                                'failed' => 'danger',
                                'pending' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('started_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('completed_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('duration')
                            ->state(fn (ScanRun $record): string => static::formatDuration($record))
                            ->label('Duration'),
                    ])
                    ->columns(5),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CrawlQueueItemsRelationManager::class,
            FirstSeenUrlsRelationManager::class,
            SeoOpportunitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScanRuns::route('/'),
            'view' => Pages\ViewScanRun::route('/{record}'),
        ];
    }

    protected static function formatDuration(ScanRun $record): string
    {
        if (! $record->started_at) {
            return '—';
        }

        $end = $record->completed_at ?? now();
        $seconds = max(0, $record->started_at->diffInSeconds($end));

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
        }

        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }
}
