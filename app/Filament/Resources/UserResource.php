<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Users';

    protected static string|\UnitEnum|null $navigationGroup = 'Access Control';

    protected static ?int $navigationSort = 10;

    /**
     * Only SuperAdmin / Admin / AccountManager can access this resource.
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && $user->canApproveUsers();
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->canApproveUsers();
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->canApproveUsers();
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->badge()
                    ->sortable(),

                TextColumn::make('approved')
                    ->label('Access')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Approved' : 'Pending')
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('approved')
                    ->label('Approval Status')
                    ->options([
                        '1' => 'Approved',
                        '0' => 'Pending',
                    ]),

                SelectFilter::make('role')
                    ->options([
                        'super_admin'     => 'Super Admin',
                        'admin'           => 'Admin',
                        'account_manager' => 'Account Manager',
                        'owner'           => 'Owner',
                        'client'          => 'Client',
                    ]),
            ])
            ->actions([
                // Approve action — visible only when not approved
                \Filament\Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (User $record): bool => ! $record->approved)
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update(['approved' => true]);
                    }),

                // Revoke action — visible only when approved and not a privileged user
                \Filament\Tables\Actions\Action::make('revoke')
                    ->label('Revoke')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (User $record): bool => $record->approved && ! $record->canApproveUsers())
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update(['approved' => false]);
                    }),

                \Filament\Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Bulk approve — only approves pending, non-privileged users
                    \Filament\Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (\Illuminate\Support\Collection $records) =>
                            $records->each(fn (User $r) => $r->update(['approved' => true]))
                        ),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Forms\Components\TextInput::make('name')
                ->required(),

            \Filament\Forms\Components\TextInput::make('email')
                ->email()
                ->required(),

            \Filament\Forms\Components\Select::make('role')
                ->options([
                    'super_admin'     => 'Super Admin',
                    'admin'           => 'Admin',
                    'account_manager' => 'Account Manager',
                    'owner'           => 'Owner',
                    'client'          => 'Client',
                ])
                ->required(),

            \Filament\Forms\Components\Toggle::make('approved')
                ->label('Account Approved')
                ->helperText('Only approved users can access the client dashboard.')
                // Prevent a non-privileged user from editing their own approval
                ->disabled(fn () => ! auth()->user()?->canApproveUsers()),

            \Filament\Forms\Components\Toggle::make('is_active')
                ->label('Active'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'edit'  => EditUser::route('/{record}/edit'),
        ];
    }
}
