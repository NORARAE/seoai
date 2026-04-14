<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;
use App\Filament\Resources\QuickScanResource\Pages\ListQuickScans;
use App\Models\QuickScan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuickScanResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = QuickScan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentMagnifyingGlass;

    protected static ?string $navigationLabel = 'Quick Scans';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 6;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && $user->canApproveUsers();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('url')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn(QuickScan $r): string => $r->url ?? ''),

                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('score')
                    ->sortable()
                    ->badge()
                    ->color(fn(?int $state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 70 => 'success',
                        $state >= 40 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn(string $state): string => match ($state) {
                        'scanned' => 'success',
                        'paid' => 'info',
                        'pending' => 'gray',
                        'error' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('upgrade_plan')
                    ->label('Plan')
                    ->badge()
                    ->placeholder('—')
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'citation-builder' => 'Citation Builder',
                        'authority-engine' => 'Authority Engine',
                        default => $state ?? '',
                    })
                    ->color(fn(?string $state): string => match ($state) {
                        'citation-builder' => 'info',
                        'authority-engine' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('upgrade_status')
                    ->label('Upgrade')
                    ->badge()
                    ->placeholder('—')
                    ->color(fn(?string $state): string => match ($state) {
                        'paid', 'active' => 'success',
                        'pending' => 'warning',
                        'completed' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('paid')
                    ->label('Paid')
                    ->badge()
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Yes' : 'No')
                    ->color(fn(bool $state): string => $state ? 'success' : 'gray'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'scanned' => 'Scanned',
                        'error' => 'Error',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuickScans::route('/'),
        ];
    }
}
