<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\PagePayloadResource\Pages;
use App\Models\PagePayload;
use App\Models\User;
use App\Services\PublishingService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PagePayloadResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = PagePayload::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return Auth::user() instanceof User;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['site', 'service', 'batch', 'reviewedBy', 'city']);

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Editable Metadata')
                    ->description('Only title and meta description are editable. Generated body content and structured data stay read-only.')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn (?PagePayload $record) => $record !== null && ! $record->isEditable())
                            ->columnSpanFull(),
                        Textarea::make('meta_description')
                            ->rows(4)
                            ->maxLength(500)
                            ->disabled(fn (?PagePayload $record) => $record !== null && ! $record->isEditable())
                            ->columnSpanFull(),
                    ]),
                Section::make('Read-only Context')
                    ->schema([
                        TextInput::make('status')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('publish_status')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('canonical_url_suggestion')
                            ->label('Canonical URL')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Workflow')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'secondary',
                                'needs_review' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'published' => 'info',
                                'failed' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()),
                        TextEntry::make('publish_status')
                            ->label('Publish')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'published' => 'success',
                                'exported' => 'info',
                                'failed' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()),
                        TextEntry::make('reviewedBy.name')
                            ->label('Reviewed By')
                            ->placeholder('—'),
                        TextEntry::make('reviewed_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('review_notes')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Search Metadata')
                    ->schema([
                        TextEntry::make('title')
                            ->columnSpanFull(),
                        TextEntry::make('meta_description')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('canonical_url_suggestion')
                            ->label('Canonical URL')
                            ->copyable()
                            ->url(fn (?string $state) => $state, shouldOpenInNewTab: true)
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('slug')
                            ->copyable(),
                        TextEntry::make('location_name')
                            ->label('Location'),
                        TextEntry::make('site.domain')
                            ->label('Site Domain'),
                        TextEntry::make('formatted_body_length')
                            ->label('Body Size'),
                        TextEntry::make('section_count')
                            ->label('Sections'),
                        TextEntry::make('seo_score')
                            ->badge()
                            ->color(fn ($state) => match (true) {
                                $state === null => 'gray',
                                $state >= 85 => 'success',
                                $state >= 70 => 'warning',
                                default => 'danger',
                            }),
                        TextEntry::make('internal_links_count')
                            ->label('Internal Links'),
                    ])
                    ->columns(2),
                Section::make('Section Outline')
                    ->schema([
                        TextEntry::make('preview_sections')
                            ->hiddenLabel()
                            ->state(fn (PagePayload $record): array => collect($record->preview_sections)->pluck('heading')->all())
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->placeholder('No H2 sections detected.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Rendered HTML Preview')
                    ->schema([
                        TextEntry::make('preview_body_html')
                            ->hiddenLabel()
                            ->html()
                            ->prose()
                            ->placeholder('No body content generated.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Structured Data & Assets')
                    ->schema([
                        CodeEntry::make('schema_json_ld')
                            ->label('Schema JSON-LD')
                            ->columnSpanFull()
                            ->placeholder('No schema generated.'),
                        CodeEntry::make('og_tags')
                            ->label('OG Tags')
                            ->columnSpanFull()
                            ->placeholder('No OG tags generated.'),
                        CodeEntry::make('internal_link_suggestions')
                            ->label('Internal Link Suggestions')
                            ->columnSpanFull()
                            ->placeholder('No internal links generated.'),
                        CodeEntry::make('generation_params')
                            ->label('Generation Parameters')
                            ->columnSpanFull()
                            ->placeholder('No generation metadata available.'),
                    ]),
            ]);
    }

    public static function makeQuickPreviewAction(): Action
    {
        return Action::make('preview')
            ->label('Preview')
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->modalHeading(fn (PagePayload $record) => $record->title)
            ->modalWidth('7xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->modalContent(fn (PagePayload $record): ViewContract => view('filament.page-payloads.quick-preview-modal', [
                'payload' => $record,
            ]));
    }

    public static function makeApproveAction(): Action
    {
        return Action::make('approve')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (PagePayload $record) => in_array($record->status, ['needs_review', 'rejected'], true))
            ->form([
                Textarea::make('review_note')
                    ->label('Approval Note')
                    ->rows(3)
                    ->maxLength(1000),
            ])
            ->action(function (PagePayload $record, array $data): void {
                $record->approve(Auth::id(), $data['review_note'] ?? null);

                Notification::make()
                    ->title('Payload approved')
                    ->success()
                    ->send();
            });
    }

    public static function makeRejectAction(): Action
    {
        return Action::make('requestChanges')
            ->label('Request Changes')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(fn (PagePayload $record) => in_array($record->status, ['needs_review', 'approved'], true))
            ->form([
                Textarea::make('review_note')
                    ->label('Change Request')
                    ->rows(4)
                    ->required()
                    ->maxLength(2000),
            ])
            ->action(function (PagePayload $record, array $data): void {
                $record->reject(Auth::id(), $data['review_note']);

                Notification::make()
                    ->title('Payload marked as rejected')
                    ->body('The rejection reason has been stored in review notes.')
                    ->warning()
                    ->send();
            });
    }

    public static function makePublishAction(): Action
    {
        return Action::make('publish')
            ->icon('heroicon-o-cloud-arrow-up')
            ->color('success')
            ->visible(fn (PagePayload $record) => $record->isReadyToPublish())
            ->requiresConfirmation()
            ->action(function (PagePayload $record): void {
                \App\Jobs\PublishPagePayloadJob::dispatch($record->id);

                Notification::make()
                    ->title('Publishing queued')
                    ->success()
                    ->send();
            });
    }

    public static function makeExportAction(): Action
    {
        return Action::make('export')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('info')
            ->form([
                Forms\Components\Select::make('format')
                    ->options([
                        'json' => 'JSON',
                        'markdown' => 'Markdown',
                        'html' => 'HTML',
                        'csv' => 'CSV',
                    ])
                    ->default('json')
                    ->required(),
            ])
            ->action(function (PagePayload $record, array $data) {
                $exportContent = $record->toExportFormat($data['format']);
                $filename = "payload-{$record->id}.{$data['format']}";
                $path = "exports/single/{$filename}";
                Storage::put($path, $exportContent);

                Notification::make()
                    ->title('Export ready')
                    ->body("Download: {$filename}")
                    ->success()
                    ->send();

                return response()->download(Storage::path($path));
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn (PagePayload $record) => $record->title),
                Tables\Columns\TextColumn::make('location_name')
                    ->label('Location')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('site.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('batch.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'secondary',
                        'needs_review' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'published' => 'info',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\BadgeColumn::make('publish_status')
                    ->label('Publish')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'published',
                        'info' => 'exported',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('formatted_body_length')
                    ->label('Body Size')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('section_count')
                    ->label('Sections')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('seo_score')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'needs_review' => 'Needs Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'published' => 'Published',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('publish_status')
                    ->options([
                        'pending' => 'Pending',
                        'published' => 'Published',
                        'exported' => 'Exported',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('site_id')
                    ->relationship('site', 'name')
                    ->label('Site'),
                Tables\Filters\Filter::make('review_queue')
                    ->label('Review Queue')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'needs_review')),
            ])
            ->actions([
                ViewAction::make(),
                static::makeQuickPreviewAction(),
                EditAction::make()
                    ->visible(fn (PagePayload $record) => $record->isEditable()),
                static::makeApproveAction(),
                static::makeRejectAction(),
                static::makePublishAction(),
                static::makeExportAction(),
            ])
            ->bulkActions([
                BulkAction::make('approveSelected')
                    ->label('Approve Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Textarea::make('review_note')
                            ->label('Approval Note')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $approvedCount = 0;

                        foreach ($records as $record) {
                            if (in_array($record->status, ['needs_review', 'rejected'], true)) {
                                $record->approve(Auth::id(), $data['review_note'] ?? null);
                                $approvedCount++;
                            }
                        }

                        Notification::make()
                            ->title("{$approvedCount} payloads approved")
                            ->success()
                            ->send();
                    }),
                BulkAction::make('export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('format')
                            ->options([
                                'json' => 'JSON',
                                'markdown' => 'Markdown',
                                'html' => 'HTML',
                                'csv' => 'CSV',
                            ])
                            ->default('json')
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $publishingService = app(PublishingService::class);
                        $zipPath = $publishingService->exportBatch($records, $data['format']);

                        Notification::make()
                            ->title('Bulk export ready')
                            ->body("Download ZIP: {$zipPath}")
                            ->success()
                            ->send();
                    }),
                BulkAction::make('publish')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $queued = 0;

                        foreach ($records as $record) {
                            if ($record->isReadyToPublish()) {
                                \App\Jobs\PublishPagePayloadJob::dispatch($record->id);
                                $queued++;
                            }
                        }

                        Notification::make()
                            ->title("{$queued} payloads queued for publishing")
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPagePayloads::route('/'),
            'view' => Pages\ViewPagePayload::route('/{record}'),
            'edit' => Pages\EditPagePayload::route('/{record}/edit'),
        ];
    }
}
