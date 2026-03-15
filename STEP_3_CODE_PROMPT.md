# Step 3: Bulk Page Expansion Engine - Complete Code Prompt

## Context

You are building Step 3 of the SEOAIco platform - a Laravel 12 + Filament 3 SaaS for programmatic SEO.

**What's already built:**
- ✅ Step 1: GSC integration, performance tracking
- ✅ Step 2: Revenue Opportunity Engine (scans service × location combinations, scores by priority)
- ✅ LocationPageGeneratorService (generates individual pages)
- ✅ Site, Service, City, County, State, SeoOpportunity models
- ✅ Multi-tenant architecture with client_id on all records

**What Step 3 needs:**
Build a queue-based bulk page generation system that takes identified `SeoOpportunity` records and generates location pages at scale (up to 50 per batch).

---

## Implementation Requirements

### 1. Create PageGenerationBatch Model + Migration

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_page_generation_batches_table.php`

```php
Schema::create('page_generation_batches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('site_id')->constrained()->onDelete('cascade');
    $table->foreignId('client_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    
    $table->string('batch_type'); // 'opportunity_list', 'service_wide', 'county_wide', 'state_wide'
    $table->string('initiator_type')->nullable(); // 'command', 'api', 'dashboard'
    $table->string('initiator_id')->nullable();
    
    $table->unsignedInteger('requested_count')->default(0);
    $table->unsignedInteger('completed_count')->default(0);
    $table->unsignedInteger('failed_count')->default(0);
    $table->unsignedInteger('skipped_count')->default(0);
    
    $table->json('parameters')->nullable();
    $table->json('filters')->nullable();
    $table->json('error_summary')->nullable();
    
    $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
    
    $table->timestamp('started_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
    
    $table->index(['site_id', 'status']);
    $table->index(['client_id', 'created_at']);
});
```

**File:** `app/Models/PageGenerationBatch.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageGenerationBatch extends Model
{
    protected $fillable = [
        'site_id',
        'client_id',
        'user_id',
        'batch_type',
        'initiator_type',
        'initiator_id',
        'requested_count',
        'completed_count',
        'failed_count',
        'skipped_count',
        'parameters',
        'filters',
        'error_summary',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'filters' => 'array',
        'error_summary' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function locationPages(): HasMany
    {
        return $this->hasMany(LocationPage::class, 'batch_id');
    }

    public function getProgressPercentage(): float
    {
        if ($this->requested_count === 0) {
            return 0;
        }
        return ($this->completed_count / $this->requested_count) * 100;
    }

    public function isComplete(): bool
    {
        return in_array($this->status, ['completed', 'failed', 'cancelled']);
    }

    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_summary' => ['reason' => $reason],
        ]);
    }
}
```

---

### 2. Update LocationPage and SeoOpportunity Models

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_add_batch_id_to_location_pages_table.php`

```php
Schema::table('location_pages', function (Blueprint $table) {
    $table->foreignId('batch_id')->nullable()->after('site_id')
        ->constrained('page_generation_batches')->nullOnDelete();
    $table->index('batch_id');
});
```

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_add_generation_tracking_to_seo_opportunities_table.php`

```php
Schema::table('seo_opportunities', function (Blueprint $table) {
    $table->timestamp('generated_at')->nullable()->after('last_analyzed_at');
    $table->foreignId('generated_page_id')->nullable()->after('location_page_id')
        ->constrained('location_pages')->nullOnDelete();
});
```

**Update:** `app/Models/LocationPage.php` - add to fillable:
```php
protected $fillable = [
    // ... existing fields
    'batch_id',
];
```

**Update:** `app/Models/SeoOpportunity.php` - add to fillable:
```php
protected $fillable = [
    // ... existing fields
    'generated_at',
    'generated_page_id',
];

protected $casts = [
    // ... existing casts
    'generated_at' => 'datetime',
];
```

---

### 3. Create BulkPageExpansionService

**File:** `app/Services/BulkPageExpansionService.php`

```php
<?php

namespace App\Services;

use App\Jobs\CreateBaselineSnapshotJob;
use App\Jobs\GenerateLocationPageJob;
use App\Jobs\UpdateSitemapAfterBatchJob;
use App\Models\City;
use App\Models\County;
use App\Models\PageGenerationBatch;
use App\Models\SeoOpportunity;
use App\Models\Service;
use App\Models\Site;
use App\Models\State;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class BulkPageExpansionService
{
    const MAX_BATCH_SIZE = 50;

    /**
     * Generate pages from a collection of opportunities
     */
    public function generateFromOpportunities(
        Site $site,
        Collection $opportunities,
        array $options = []
    ): PageGenerationBatch {
        $limit = min($opportunities->count(), self::MAX_BATCH_SIZE);
        $opportunities = $opportunities->take($limit);

        $batch = PageGenerationBatch::create([
            'site_id' => $site->id,
            'client_id' => $site->client_id,
            'user_id' => auth()->id(),
            'batch_type' => 'opportunity_list',
            'initiator_type' => $options['initiator_type'] ?? 'manual',
            'initiator_id' => $options['initiator_id'] ?? null,
            'requested_count' => $opportunities->count(),
            'parameters' => $options,
            'status' => 'pending',
        ]);

        $batch->markAsProcessing();

        $jobs = [];
        foreach ($opportunities as $opportunity) {
            $jobs[] = new GenerateLocationPageJob(
                $batch->id,
                $site->id,
                $opportunity->service_id,
                $opportunity->location_id,
                $options
            );
        }

        // Dispatch jobs as a batch with chained cleanup jobs
        Bus::batch($jobs)
            ->then(function () use ($batch) {
                $batch->markAsCompleted();
                UpdateSitemapAfterBatchJob::dispatch($batch->site);
                CreateBaselineSnapshotJob::dispatch($batch->site, 'post_batch_generation', $batch->id);
            })
            ->catch(function () use ($batch) {
                $batch->markAsFailed('One or more jobs failed');
            })
            ->onQueue('high')
            ->dispatch();

        Log::channel('page-generation')->info('Batch started', [
            'batch_id' => $batch->id,
            'site_id' => $site->id,
            'opportunity_count' => $opportunities->count(),
        ]);

        return $batch;
    }

    /**
     * Generate pages for all cities in a service (up to limit)
     */
    public function generateForService(
        Site $site,
        Service $service,
        array $options = []
    ): PageGenerationBatch {
        $limit = $options['limit'] ?? self::MAX_BATCH_SIZE;
        $minPriority = $options['min_priority'] ?? 50;

        $opportunities = SeoOpportunity::where('site_id', $site->id)
            ->where('service_id', $service->id)
            ->where('status', 'pending')
            ->where('priority_score', '>=', $minPriority)
            ->whereNull('generated_at')
            ->orderByDesc('priority_score')
            ->limit($limit)
            ->get();

        return $this->generateFromOpportunities($site, $opportunities, array_merge($options, [
            'batch_type' => 'service_wide',
        ]));
    }

    /**
     * Generate pages for a specific county (all services × cities in that county)
     */
    public function generateForCounty(
        Site $site,
        County $county,
        array $options = []
    ): PageGenerationBatch {
        $limit = $options['limit'] ?? self::MAX_BATCH_SIZE;
        $minPriority = $options['min_priority'] ?? 50;

        $cityIds = City::where('county_id', $county->id)->pluck('id');

        $opportunities = SeoOpportunity::where('site_id', $site->id)
            ->whereIn('location_id', $cityIds)
            ->where('status', 'pending')
            ->where('priority_score', '>=', $minPriority)
            ->whereNull('generated_at')
            ->orderByDesc('priority_score')
            ->limit($limit)
            ->get();

        return $this->generateFromOpportunities($site, $opportunities, array_merge($options, [
            'batch_type' => 'county_wide',
        ]));
    }

    /**
     * Generate pages for a state (up to limit)
     */
    public function generateForState(
        Site $site,
        State $state,
        array $options = []
    ): PageGenerationBatch {
        $limit = $options['limit'] ?? self::MAX_BATCH_SIZE;
        $minPriority = $options['min_priority'] ?? 50;

        $cityIds = City::where('state_id', $state->id)->pluck('id');

        $opportunities = SeoOpportunity::where('site_id', $site->id)
            ->whereIn('location_id', $cityIds)
            ->where('status', 'pending')
            ->where('priority_score', '>=', $minPriority)
            ->whereNull('generated_at')
            ->orderByDesc('priority_score')
            ->limit($limit)
            ->get();

        return $this->generateFromOpportunities($site, $opportunities, array_merge($options, [
            'batch_type' => 'state_wide',
        ]));
    }

    /**
     * Get status of a batch
     */
    public function getBatchStatus(int $batchId): array
    {
        $batch = PageGenerationBatch::with(['site', 'locationPages'])->findOrFail($batchId);

        return [
            'id' => $batch->id,
            'status' => $batch->status,
            'progress_percentage' => $batch->getProgressPercentage(),
            'requested' => $batch->requested_count,
            'completed' => $batch->completed_count,
            'failed' => $batch->failed_count,
            'skipped' => $batch->skipped_count,
            'started_at' => $batch->started_at?->toIso8601String(),
            'completed_at' => $batch->completed_at?->toIso8601String(),
            'pages' => $batch->locationPages->map(fn($p) => [
                'id' => $p->id,
                'url' => $p->url_path,
                'title' => $p->title,
            ]),
        ];
    }
}
```

---

### 4. Create GenerateLocationPageJob

**File:** `app/Jobs/GenerateLocationPageJob.php`

```php
<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\LocationPage;
use App\Models\PageGenerationBatch;
use App\Models\SeoOpportunity;
use App\Models\Service;
use App\Models\Site;
use App\Services\LocationPageGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateLocationPageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;
    public int $backoff = 10;

    public function __construct(
        public int $batchId,
        public int $siteId,
        public int $serviceId,
        public int $cityId,
        public array $options = []
    ) {
        $this->onQueue('high');
    }

    public function handle(LocationPageGeneratorService $generator): void
    {
        $site = Site::findOrFail($this->siteId);
        $service = Service::findOrFail($this->serviceId);
        $city = City::findOrFail($this->cityId);
        $batch = PageGenerationBatch::findOrFail($this->batchId);

        // Check if page already exists
        $existing = LocationPage::where([
            'site_id' => $this->siteId,
            'service_id' => $this->serviceId,
            'city_id' => $this->cityId,
        ])->first();

        if ($existing) {
            Log::channel('page-generation')->info('Page already exists, skipping', [
                'batch_id' => $this->batchId,
                'page_id' => $existing->id,
            ]);
            $batch->increment('skipped_count');
            return;
        }

        try {
            // Generate the page using existing service
            $page = $generator->generateForServiceAndCity($site, $service, $city, [
                'batch_id' => $this->batchId,
            ]);

            // Update opportunity status
            SeoOpportunity::where([
                'site_id' => $this->siteId,
                'service_id' => $this->serviceId,
                'location_id' => $this->cityId,
            ])->update([
                'status' => 'completed',
                'generated_at' => now(),
                'generated_page_id' => $page->id,
                'location_page_id' => $page->id,
            ]);

            $batch->increment('completed_count');

            Log::channel('page-generation')->info('Page generated successfully', [
                'batch_id' => $this->batchId,
                'page_id' => $page->id,
                'service' => $service->name,
                'city' => $city->name,
            ]);
        } catch (Throwable $e) {
            $batch->increment('failed_count');
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::channel('page-generation')->error('Failed to generate page', [
            'batch_id' => $this->batchId,
            'site_id' => $this->siteId,
            'service_id' => $this->serviceId,
            'city_id' => $this->cityId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Update opportunity status
        SeoOpportunity::where([
            'site_id' => $this->siteId,
            'service_id' => $this->serviceId,
            'location_id' => $this->cityId,
        ])->update(['status' => 'generation_failed']);
    }
}
```

---

### 5. Create Support Jobs

**File:** `app/Jobs/UpdateSitemapAfterBatchJob.php`

```php
<?php

namespace App\Jobs;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateSitemapAfterBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Site $site)
    {
        $this->onQueue('low');
    }

    public function handle(): void
    {
        // TODO: Implement sitemap generation
        // This would generate XML sitemap with all location pages
        
        Log::info('Sitemap update queued for site', ['site_id' => $this->site->id]);
        
        // Placeholder for now
        // Future: Call SitemapGeneratorService
    }
}
```

**File:** `app/Jobs/CreateBaselineSnapshotJob.php`

```php
<?php

namespace App\Jobs;

use App\Models\BaselineSnapshot;
use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateBaselineSnapshotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Site $site,
        public string $snapshotType,
        public ?int $batchId = null
    ) {
        $this->onQueue('low');
    }

    public function handle(): void
    {
        BaselineSnapshot::create([
            'site_id' => $this->site->id,
            'snapshot_type' => $this->snapshotType,
            'reference_id' => $this->batchId,
            'total_pages' => $this->site->pages()->count(),
            'total_indexed_pages' => $this->site->pages()->where('is_indexed', true)->count(),
            'avg_position' => $this->site->performanceMetrics()->avg('avg_position') ?? 0,
            'total_impressions' => $this->site->performanceMetrics()->sum('impressions'),
            'total_clicks' => $this->site->performanceMetrics()->sum('clicks'),
            'avg_ctr' => $this->site->performanceMetrics()->avg('ctr') ?? 0,
            'captured_at' => now(),
        ]);
    }
}
```

---

### 6. Create Artisan Commands

**File:** `app/Console/Commands/GeneratePagesFromOpportunities.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\County;
use App\Models\Service;
use App\Models\SeoOpportunity;
use App\Models\Site;
use App\Models\State;
use App\Services\BulkPageExpansionService;
use Illuminate\Console\Command;

class GeneratePagesFromOpportunities extends Command
{
    protected $signature = 'pages:generate-batch
                            {--site= : Site ID}
                            {--service= : Service ID}
                            {--county= : County ID}
                            {--state= : State ID}
                            {--limit=50 : Maximum pages to generate}
                            {--min-priority=60 : Minimum priority score}
                            {--min-revenue=0 : Minimum estimated monthly revenue}
                            {--dry-run : Show what would be generated without doing it}';

    protected $description = 'Generate location pages from SEO opportunities';

    public function handle(BulkPageExpansionService $service): int
    {
        $this->info('📄 Bulk Page Generation');
        $this->newLine();

        $siteId = $this->option('site');
        $site = $siteId ? Site::findOrFail($siteId) : Site::where('status', 'active')->first();

        if (!$site) {
            $this->error('❌ No site found');
            return self::FAILURE;
        }

        $this->info("Site: {$site->name} ({$site->domain})");

        // Determine generation scope
        if ($serviceId = $this->option('service')) {
            $serviceModel = Service::findOrFail($serviceId);
            $this->info("Scope: Service-wide ({$serviceModel->name})");
            $batch = $service->generateForService($site, $serviceModel, [
                'limit' => (int) $this->option('limit'),
                'min_priority' => (int) $this->option('min-priority'),
            ]);
        } elseif ($countyId = $this->option('county')) {
            $county = County::findOrFail($countyId);
            $this->info("Scope: County-wide ({$county->name})");
            $batch = $service->generateForCounty($site, $county, [
                'limit' => (int) $this->option('limit'),
                'min_priority' => (int) $this->option('min-priority'),
            ]);
        } elseif ($stateId = $this->option('state')) {
            $state = State::findOrFail($stateId);
            $this->info("Scope: State-wide ({$state->name})");
            $batch = $service->generateForState($site, $state, [
                'limit' => (int) $this->option('limit'),
                'min_priority' => (int) $this->option('min-priority'),
            ]);
        } else {
            // Top opportunities
            $opportunities = SeoOpportunity::where('site_id', $site->id)
                ->where('status', 'pending')
                ->where('priority_score', '>=', (int) $this->option('min-priority'))
                ->whereNull('generated_at')
                ->orderByDesc('priority_score')
                ->limit((int) $this->option('limit'))
                ->get();

            $this->info("Scope: Top {$opportunities->count()} opportunities");
            
            if ($this->option('dry-run')) {
                $this->table(
                    ['Priority', 'Service', 'Location', 'Revenue'],
                    $opportunities->map(fn($o) => [
                        $o->priority_score,
                        $o->service->name,
                        $o->location_name,
                        '$' . number_format($o->estimated_monthly_revenue),
                    ])
                );
                return self::SUCCESS;
            }

            $batch = $service->generateFromOpportunities($site, $opportunities, [
                'initiator_type' => 'command',
                'initiator_id' => $this->signature,
            ]);
        }

        $this->newLine();
        $this->info("✅ Batch created: #{$batch->id}");
        $this->info("   Status: {$batch->status}");
        $this->info("   Pages queued: {$batch->requested_count}");
        $this->newLine();
        $this->info("💡 Monitor progress: php artisan queue:work");
        $this->info("💡 Check status: php artisan batch:status {$batch->id}");

        return self::SUCCESS;
    }
}
```

---

### 7. Add Logging Channel

**File:** `config/logging.php` - add this to the 'channels' array:

```php
'page-generation' => [
    'driver' => 'daily',
    'path' => storage_path('logs/page-generation.log'),
    'level' => env('LOG_LEVEL', 'info'),
    'days' => 14,
],
```

---

### 8. Update Queue Configuration (Optional but Recommended)

**File:** `config/queue.php` - ensure you have appropriate queue connections configured.

If using database queue (default), make sure queue tables exist:
```bash
php artisan queue:table
php artisan migrate
```

---

## Testing the Implementation

### Step 1: Run Migrations
```bash
php artisan migrate
```

### Step 2: Start Queue Worker
```bash
php artisan queue:work --queue=high,default,low
```

### Step 3: Generate a Test Batch
```bash
# Generate top 10 opportunities
php artisan pages:generate-batch --site=1 --limit=10

# Or service-specific
php artisan pages:generate-batch --site=1 --service=1 --limit=20

# Dry run first
php artisan pages:generate-batch --site=1 --limit=10 --dry-run
```

### Step 4: Monitor Progress
```bash
# Watch queue worker output
# Check logs
tail -f storage/logs/page-generation.log

# Check database
php artisan tinker
>>> App\Models\PageGenerationBatch::latest()->first()->toArray()
```

---

## Integration with Dashboard Widget

To add bulk generation to your TopRevenueOpportunitiesWidget, add an action:

```php
// In app/Filament/Widgets/TopRevenueOpportunitiesWidget.php

protected function getTableActions(): array
{
    return [
        Tables\Actions\Action::make('generate_batch')
            ->label('Generate Top 10')
            ->icon('heroicon-o-lightning-bolt')
            ->requiresConfirmation()
            ->action(function () {
                $opportunities = $this->getTableQuery()
                    ->where('status', 'pending')
                    ->whereNull('generated_at')
                    ->limit(10)
                    ->get();
                
                $site = Site::first(); // Or get from context
                $service = app(\App\Services\BulkPageExpansionService::class);
                
                $batch = $service->generateFromOpportunities($site, $opportunities, [
                    'initiator_type' => 'dashboard',
                ]);
                
                Notification::make()
                    ->title("Batch #{$batch->id} created")
                    ->body("{$batch->requested_count} pages queued for generation")
                    ->success()
                    ->send();
            }),
    ];
}
```

---

## Success Criteria

✅ Batch record created in `page_generation_batches` table  
✅ Jobs dispatched to queue  
✅ Pages generated without duplicates  
✅ SeoOpportunities updated with `generated_at` and `generated_page_id`  
✅ LocationPages linked to batch via `batch_id`  
✅ Sitemap update job dispatched after completion  
✅ Baseline snapshot created  
✅ Logs captured in `page-generation.log`  
✅ Failed jobs retry 3 times before giving up  
✅ Batch size limited to 50 pages  

---

## Next Steps After Implementation

1. Add Filament resource for PageGenerationBatch (view/manage batches)
2. Build sitemap generator service
3. Add batch status polling to dashboard
4. Configure supervisor for production queue workers
5. Set up monitoring/alerts for failed batches

---

**Ready to implement!** Copy this prompt and start building Step 3.
