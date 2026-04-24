<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;
use App\Filament\Resources\SpamLogResource\Pages\ListSpamLogs;
use App\Filament\Resources\SpamLogResource\Pages\ViewSpamLog;
use App\Models\BlockedIp;
use App\Models\SpamLog;
use BackedEnum;
use Filament\Actions\Action as TableAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SpamLogResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = SpamLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static ?string $navigationLabel = 'Spam Intelligence';

    protected static string|\UnitEnum|null $navigationGroup = 'Revenue';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'email';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && $user->canApproveUsers();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->poll('60s')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->timezone('America/Los_Angeles')
                    ->width('160px'),

                TextColumn::make('action')
                    ->label('Decision')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'block' => 'danger',
                        'flag'  => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('reason')
                    ->label('Reason')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'antispam_blocked', 'high_risk_score' => 'danger',
                        'honeypot_triggered'                  => 'danger',
                        'duplicate_submission'                => 'warning',
                        'antispam_flagged', 'medium_risk_allowed' => 'warning',
                        default                               => 'gray',
                    }),

                TextColumn::make('risk_score')
                    ->label('Score')
                    ->numeric(1)
                    ->sortable()
                    ->color(fn ($state): string => $state >= 8 ? 'danger' : ($state >= 4 ? 'warning' : 'gray')),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->placeholder('—'),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—'),

                TextColumn::make('company')
                    ->label('Company')
                    ->searchable()
                    ->weight('semibold')
                    ->placeholder('—'),

                TextColumn::make('turnstile_valid')
                    ->label('Turnstile')
                    ->formatStateUsing(function (?bool $state, SpamLog $record): string {
                        if ($state === true)  return 'Passed';
                        if ($state === false) return 'Failed';
                        return $record->turnstile_reason === 'turnstile_missing' ? 'Missing' : 'N/A';
                    })
                    ->badge()
                    ->color(function (?bool $state, SpamLog $record): string {
                        if ($state === true)  return 'success';
                        if ($state === false) return 'danger';
                        return 'gray';
                    }),

                TextColumn::make('inquiry.ip_is_proxy')
                    ->label('VPN/Proxy')
                    ->formatStateUsing(fn ($state): string => $state ? 'Yes' : 'No')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'warning' : 'gray'),

                IconColumn::make('is_reviewed')
                    ->label('Reviewed')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedClock)
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Decision')
                    ->options([
                        'block' => 'Blocked',
                        'flag'  => 'Flagged',
                    ]),

                SelectFilter::make('reason')
                    ->label('Reason')
                    ->options([
                        'antispam_blocked'     => 'Antispam Blocked',
                        'honeypot_triggered'   => 'Honeypot Triggered',
                        'duplicate_submission' => 'Duplicate Submission',
                        'high_risk_score'      => 'High Risk Score',
                        'antispam_flagged'     => 'Antispam Flagged',
                        'medium_risk_allowed'  => 'Medium Risk (Allowed)',
                    ]),

                Filter::make('turnstile_failed')
                    ->label('Turnstile Failed/Missing')
                    ->query(fn (Builder $q) => $q->where('turnstile_valid', false)
                        ->orWhere('turnstile_reason', 'turnstile_missing')),

                Filter::make('vpn_proxy')
                    ->label('VPN / Proxy')
                    ->query(fn (Builder $q) => $q->whereHas('inquiry', fn (Builder $iq) =>
                        $iq->where('ip_is_proxy', true))),

                Filter::make('unreviewed')
                    ->label('Unreviewed Only')
                    ->query(fn (Builder $q) => $q->where('is_reviewed', false)),

                Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $q) => $q->whereDate('created_at', today())),

                Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn (Builder $q) => $q->where('created_at', '>=', now()->subDays(7))),
            ])
            ->actions([
                TableAction::make('view')
                    ->label('Details')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn (SpamLog $record): string => static::getUrl('view', ['record' => $record])),

                TableAction::make('block_ip')
                    ->label('Block IP')
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Block this IP address?')
                    ->modalDescription(fn (SpamLog $record): string =>
                        "Add {$record->ip_address} to the persistent IP blocklist. All future submissions from this IP will be rejected.")
                    ->visible(fn (SpamLog $record): bool =>
                        filled($record->ip_address) && ! $record->isIpBlocked())
                    ->action(function (SpamLog $record): void {
                        $record->blockIp('admin');
                        $record->update(['is_reviewed' => true]);
                        Notification::make()
                            ->title('IP blocked')
                            ->body("{$record->ip_address} added to blocklist.")
                            ->success()
                            ->send();
                    }),

                TableAction::make('unblock_ip')
                    ->label('Unblock IP')
                    ->icon(Heroicon::OutlinedLockOpen)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Remove this IP from the blocklist?')
                    ->visible(fn (SpamLog $record): bool =>
                        filled($record->ip_address) && $record->isIpBlocked())
                    ->action(function (SpamLog $record): void {
                        $record->unblockIp();
                        Notification::make()
                            ->title('IP unblocked')
                            ->body("{$record->ip_address} removed from blocklist.")
                            ->success()
                            ->send();
                    }),

                TableAction::make('mark_reviewed')
                    ->label('Mark Reviewed')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->visible(fn (SpamLog $record): bool => ! $record->is_reviewed)
                    ->action(function (SpamLog $record): void {
                        $record->update(['is_reviewed' => true]);
                        Notification::make()
                            ->title('Marked as reviewed')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_mark_reviewed')
                        ->label('Mark Selected Reviewed')
                        ->icon(Heroicon::OutlinedCheck)
                        ->action(fn ($records) => $records->each->update(['is_reviewed' => true]))
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->isSuperAdmin() ?? false),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpamLogs::route('/'),
            'view'  => ViewSpamLog::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('inquiry');
    }
}
