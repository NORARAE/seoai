<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\InquiryResource\Pages\ListInquiries;
use App\Filament\Resources\InquiryResource\Pages\ViewInquiry;
use App\Models\Inquiry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InquiryResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = Inquiry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?string $navigationLabel = 'Inquiries';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 5;

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

                TextColumn::make('company')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Inquiry $r): string => $r->email_type ? strtoupper($r->email_type) : ''),

                TextColumn::make('tier')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'legacy' => 'warning',
                        '10k'    => 'success',
                        '5k'     => 'info',
                        default  => 'gray',
                    }),

                TextColumn::make('ip_country')
                    ->label('Country')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('spam_risk')
                    ->label('Risk')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'high'   => 'danger',
                        'medium' => 'warning',
                        default  => 'success',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'new'       => 'info',
                        'contacted' => 'warning',
                        'converted' => 'success',
                        'rejected'  => 'danger',
                        'spam'      => 'danger',
                        default     => 'gray',
                    }),

                IconColumn::make('ip_is_proxy')
                    ->label('Proxy')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedExclamationTriangle)
                    ->falseIcon(Heroicon::OutlinedCheckCircle)
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->timezone('America/Los_Angeles'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new'       => 'New',
                        'contacted' => 'Contacted',
                        'converted' => 'Converted',
                        'rejected'  => 'Rejected',
                        'spam'      => 'Spam',
                    ]),

                SelectFilter::make('spam_risk')
                    ->label('Spam Risk')
                    ->options([
                        'high'   => 'High',
                        'medium' => 'Medium',
                        'low'    => 'Low',
                    ]),

                SelectFilter::make('tier')
                    ->options([
                        'starter' => 'Starter',
                        '5k'      => '5K',
                        '10k'     => '10K',
                        'legacy'  => 'Legacy',
                    ]),

                SelectFilter::make('email_type')
                    ->label('Email Type')
                    ->options([
                        'business'   => 'Business',
                        'free'       => 'Free',
                        'disposable' => 'Disposable',
                    ]),

                Filter::make('proxy_or_hosting')
                    ->label('VPN / Proxy / Hosting')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('ip_is_proxy', true)->orWhere('ip_is_hosting', true)
                    ),

                Filter::make('honeypot')
                    ->label('Honeypot Triggered')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('honeypot_triggered', true)
                    ),

                Filter::make('rejected')
                    ->label('Rejected Only')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('status', 'rejected')
                    ),
            ])
            ->actions([
                \Filament\Tables\Actions\ViewAction::make(),

                \Filament\Tables\Actions\Action::make('mark_contacted')
                    ->label('Mark Contacted')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('warning')
                    ->visible(fn (Inquiry $record): bool => $record->status === 'new')
                    ->requiresConfirmation()
                    ->action(fn (Inquiry $record) => $record->update(['status' => 'contacted'])),

                \Filament\Tables\Actions\Action::make('mark_converted')
                    ->label('Mark Converted')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->visible(fn (Inquiry $record): bool => in_array($record->status, ['new', 'contacted']))
                    ->requiresConfirmation()
                    ->action(fn (Inquiry $record) => $record->update(['status' => 'converted'])),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInquiries::route('/'),
            'view'  => ViewInquiry::route('/{record}'),
        ];
    }
}
