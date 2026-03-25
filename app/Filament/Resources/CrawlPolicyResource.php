<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrawlPolicyResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CrawlPolicyResource extends Resource
{
    protected static ?string $model = 'App\\Models\\CrawlPolicy';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Advanced Scan Rules';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 20;

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user instanceof User && ($user->isSuperAdmin() || $user->isOperator());
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
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('site.name')->label('Site')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('crawl_delay')->label('Delay (s)')->sortable(),
                Tables\Columns\TextColumn::make('allow_rules_count')
                    ->label('Allow Rules')
                    ->state(fn ($record): int => count($record->allow_rules ?? [])),
                Tables\Columns\TextColumn::make('disallow_rules_count')
                    ->label('Disallow Rules')
                    ->state(fn ($record): int => count($record->disallow_rules ?? [])),
                Tables\Columns\TextColumn::make('sitemap_urls_count')
                    ->label('Sitemaps')
                    ->state(fn ($record): int => count($record->sitemap_urls ?? [])),
                Tables\Columns\TextColumn::make('last_fetched_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('last_request_at')->dateTime()->sortable(),
            ])
            ->defaultSort('last_fetched_at', 'desc')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCrawlPolicies::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
