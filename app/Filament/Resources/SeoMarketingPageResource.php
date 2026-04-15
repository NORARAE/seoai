<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\SeoMarketingPageResource\Pages\ListSeoMarketingPages;
use App\Filament\Resources\SeoMarketingPageResource\Pages\ViewSeoMarketingPage;
use App\Models\SeoMarketingPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SeoMarketingPageResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = SeoMarketingPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $navigationLabel = 'SEO Pages';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'SEO Page';

    protected static ?string $pluralModelLabel = 'SEO Pages';

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
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('money_page_rank', 'asc')
            ->columns([
                // ── Rank / money badge ──────────────────────────────────────
                TextColumn::make('money_page_rank')
                    ->label('#')
                    ->sortable()
                    ->placeholder('—')
                    ->badge()
                    ->color(fn($state) => $state !== null ? 'warning' : 'gray')
                    ->width('56px'),

                // ── Health indicators ────────────────────────────────────────
                IconColumn::make('health_meta_title')
                    ->label('Title')
                    ->tooltip('Meta Title')
                    ->getStateUsing(fn(SeoMarketingPage $r): bool => filled($r->meta_title))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedExclamationTriangle)
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->width('52px'),

                IconColumn::make('health_meta_desc')
                    ->label('Desc')
                    ->tooltip('Meta Description')
                    ->getStateUsing(fn(SeoMarketingPage $r): bool => filled($r->meta_description))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedExclamationTriangle)
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->width('52px'),

                IconColumn::make('health_schema')
                    ->label('Schema')
                    ->tooltip('Stored JSON-LD Schema')
                    ->getStateUsing(fn(SeoMarketingPage $r): bool => !empty($r->schema_json))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedMinusCircle)
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->width('60px'),

                IconColumn::make('health_links')
                    ->label('Links')
                    ->tooltip('Internal Links present')
                    ->getStateUsing(fn(SeoMarketingPage $r): bool => !empty($r->internal_links))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedExclamationTriangle)
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->width('52px'),

                // ── Core content ──────────────────────────────────────────────
                TextColumn::make('h1')
                    ->label('Page Title (H1)')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn(SeoMarketingPage $r): string => $r->h1 ?? '')
                    ->weight('semibold'),

                TextColumn::make('url_slug')
                    ->label('Slug')
                    ->searchable()
                    ->fontFamily('mono')
                    ->color('gray')
                    ->prefix('/')
                    ->copyable()
                    ->copyMessage('Slug copied')
                    ->copyMessageDuration(1500),

                TextColumn::make('cluster')
                    ->label('Cluster')
                    ->badge()
                    ->sortable()
                    ->color(fn(string $state): string => match ($state) {
                        'core' => 'primary',
                        'agency' => 'info',
                        'local' => 'success',
                        'strategy' => 'warning',
                        'industry' => 'danger',
                        default => 'gray',
                    }),

                // ── Counts ────────────────────────────────────────────────────
                TextColumn::make('h2_count')
                    ->label('H2s')
                    ->getStateUsing(fn(SeoMarketingPage $r): int => count($r->h2_structure ?? []))
                    ->badge()
                    ->color('gray')
                    ->sortable(
                        query: fn(Builder $q, string $dir) =>
                        $q->orderByRaw("JSON_ARRAY_LENGTH(h2_structure) {$dir}")
                    )
                    ->width('56px'),

                TextColumn::make('link_count')
                    ->label('Int. Links')
                    ->getStateUsing(function (SeoMarketingPage $r): int {
                        $links = $r->internal_links ?? [];
                        return count($r->lateral_links) + count($r->homepage_ctas);
                    })
                    ->badge()
                    ->color(fn($state): string => $state > 0 ? 'success' : 'warning')
                    ->width('80px'),

                // ── Priority ──────────────────────────────────────────────────
                TextColumn::make('sitemap_priority')
                    ->label('Priority')
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2)),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->timezone('America/Los_Angeles'),
            ])
            ->searchable()
            ->filters([
                SelectFilter::make('cluster')
                    ->options([
                        'core' => 'Core',
                        'agency' => 'Agency',
                        'local' => 'Local',
                        'strategy' => 'Strategy',
                        'industry' => 'Industry',
                    ]),

                Filter::make('money_pages_only')
                    ->label('Money Pages Only')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereNotNull('money_page_rank')
                    ),

                SelectFilter::make('sitemap_priority')
                    ->label('Priority Tier')
                    ->options([
                        '1.00' => 'Tier 1 (1.00)',
                        '0.95' => 'Tier 1 (0.95)',
                        '0.90' => 'Tier 1 (0.90)',
                        '0.80' => 'Tier 2 (0.80)',
                        '0.70' => 'Tier 2 (0.70)',
                        '0.60' => 'Tier 3 (0.60)',
                        '0.50' => 'Tier 3 (0.50)',
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder =>
                        isset($data['value']) && $data['value'] !== ''
                        ? $query->where('sitemap_priority', (float) $data['value'])
                        : $query
                    ),

                Filter::make('missing_meta_title')
                    ->label('Missing Meta Title')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereNull('meta_title')->orWhere('meta_title', '')
                    ),

                Filter::make('missing_meta_description')
                    ->label('Missing Meta Description')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereNull('meta_description')->orWhere('meta_description', '')
                    ),

                Filter::make('missing_schema')
                    ->label('Missing Stored Schema')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereNull('schema_json')
                            ->orWhere('schema_json', '[]')
                            ->orWhere('schema_json', 'null')
                    ),

                Filter::make('missing_internal_links')
                    ->label('Missing Internal Links')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereNull('internal_links')
                            ->orWhere('internal_links', '[]')
                            ->orWhere('internal_links', 'null')
                    ),
            ])
            ->actions([
                ViewAction::make(),

                ActionGroup::make([
                    Action::make('open_public')
                        ->label('View Live Page')
                        ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                        ->color('gray')
                        ->url(fn(SeoMarketingPage $record): string => url('/' . $record->url_slug))
                        ->openUrlInNewTab(),

                    Action::make('copy_slug')
                        ->label('Copy Slug')
                        ->icon(Heroicon::OutlinedClipboard)
                        ->color('gray')
                        ->action(fn() => null)  // client-side only via extraAttributes
                        ->extraAttributes(fn(SeoMarketingPage $record): array => [
                            'x-data' => '',
                            'x-on:click.prevent' => "navigator.clipboard.writeText('/{$record->url_slug}').then(() => \$dispatch('notify', {message: 'Slug copied!'}))",
                        ]),

                    Action::make('copy_url')
                        ->label('Copy Live URL')
                        ->icon(Heroicon::OutlinedLink)
                        ->color('gray')
                        ->action(fn() => null)
                        ->extraAttributes(fn(SeoMarketingPage $record): array => [
                            'x-data' => '',
                            'x-on:click.prevent' => "navigator.clipboard.writeText('" . url('/') . "/{$record->url_slug}').then(() => \$dispatch('notify', {message: 'URL copied!'}))",
                        ]),

                    Action::make('open_sitemap')
                        ->label('View Cluster Sitemap')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->color('gray')
                        ->visible(fn(SeoMarketingPage $record): bool => filled($record->cluster))
                        ->url(
                            fn(SeoMarketingPage $record): string =>
                            url("/sitemaps/marketing-{$record->cluster}.xml")
                        )
                        ->openUrlInNewTab(),
                ])->icon(Heroicon::OutlinedEllipsisVertical),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoMarketingPages::route('/'),
            'view' => ViewSeoMarketingPage::route('/{record}'),
        ];
    }
}
