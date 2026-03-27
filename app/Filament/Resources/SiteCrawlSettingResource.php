<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\SiteCrawlSettingResource\Pages;
use App\Models\SiteCrawlSetting;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SiteCrawlSettingResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = SiteCrawlSetting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Scan Settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 4;

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
                Tables\Columns\TextColumn::make('max_pages')->sortable(),
                Tables\Columns\TextColumn::make('max_depth')->sortable(),
                Tables\Columns\TextColumn::make('crawl_delay')->label('Delay (s)')->sortable(),
                Tables\Columns\IconColumn::make('obey_robots')->boolean(),
                Tables\Columns\IconColumn::make('follow_nofollow')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('obey_robots'),
                Tables\Filters\TernaryFilter::make('follow_nofollow'),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->fillForm(fn (SiteCrawlSetting $record): array => [
                        'max_pages' => $record->max_pages,
                        'max_depth' => $record->max_depth,
                        'crawl_delay' => $record->crawl_delay,
                        'obey_robots' => $record->obey_robots,
                        'follow_nofollow' => $record->follow_nofollow,
                    ])
                    ->form([
                        \Filament\Forms\Components\TextInput::make('max_pages')->numeric()->required()->minValue(1),
                        \Filament\Forms\Components\TextInput::make('max_depth')->numeric()->required()->minValue(0),
                        \Filament\Forms\Components\TextInput::make('crawl_delay')->numeric()->required()->minValue(0),
                        \Filament\Forms\Components\Toggle::make('obey_robots')->required(),
                        \Filament\Forms\Components\Toggle::make('follow_nofollow')->required(),
                    ])
                    ->action(function (SiteCrawlSetting $record, array $data): void {
                        $record->update($data);
                    }),
            ])
            ->defaultSort('id')
            ->poll('45s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteCrawlSettings::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
