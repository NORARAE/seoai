# Step 3: Bulk Page Expansion Engine - Implementation Plan

## 🎯 Objective

Build a robust, queue-based system for generating location-based SEO pages at scale from identified opportunities.

**Target Capabilities:**
- Generate up to 50 pages per batch
- Queue-based async generation
- Service-wide generation (all cities for one service)
- County-wide generation (all services in one county)
- Individual opportunity generation
- Automatic sitemap updates
- Baseline snapshot creation
- Internal linking integration
- Progress tracking and logging

---

## 📋 Components to Build

### 1. **BulkPageExpansionService**
**Location:** `app/Services/BulkPageExpansionService.php`

**Responsibilities:**
- Batch opportunity selection
- Job dispatching
- Progress tracking
- Deduplication logic
- Batch size limits (max 50)

**Key Methods:**
```php
public function generateFromOpportunities(Collection $opportunities, array $options = []): array
public function generateForService(Site $site, Service $service, array $options = []): array
public function generateForCounty(Site $site, County $county, array $options = []): array
public function generateForState(Site $site, State $state, int $limit = 50, array $options = []): array
public function getBatchStatus(string $batchId): array
```

### 2. **GenerateLocationPageJob**
**Location:** `app/Jobs/GenerateLocationPageJob.php`

**Responsibilities:**
- Single page generation
- Content creation via existing generators
- Schema.org markup injection
- Internal link suggestions
- Error handling and retry logic
- Status updates to seo_opportunities

**Queue:** `high` (user-initiated actions deserve priority)

**Key Properties:**
```php
public int $tries = 3;
public int $timeout = 120; // 2 minutes per page
public int $backoff = 10; // Wait 10s before retry
```

### 3. **UpdateSitemapAfterBatchJob**
**Location:** `app/Jobs/UpdateSitemapAfterBatchJob.php`

**Responsibilities:**
- Regenerate XML sitemap
- Submit to Google Search Console
- Update sitemap index if needed
- Cache invalidation

**Queue:** `low` (can wait until batch completes)

### 4. **CreateBaselineSnapshotJob**
**Location:** `app/Jobs/CreateBaselineSnapshotJob.php`

**Responsibilities:**
- Capture post-generation baseline metrics
- Record configuration used
- Tag with batch_id for tracking

**Queue:** `low`

### 5. **PageGenerationBatch Model**
**Location:** `app/Models/PageGenerationBatch.php`

**Responsibilities:**
- Track batch metadata
- Store generation parameters
- Link to generated pages
- Progress reporting

**Schema:**
```php
id, site_id, client_id, batch_type, initiator_type, initiator_id,
requested_count, completed_count, failed_count, skipped_count,
parameters (json), status, started_at, completed_at, error_summary,
created_at, updated_at
```

### 6. **Artisan Commands**

#### `php artisan pages:generate-batch`
**Location:** `app/Console/Commands/GeneratePagesFromOpportunities.php`

**Options:**
- `--site=ID` - Site to generate for
- `--service=ID` - Specific service filter
- `--county=ID` - Specific county filter
- `--state=ID` - Specific state filter
- `--limit=50` - Max pages (default: 50)
- `--min-priority=60` - Minimum priority score
- `--min-revenue=100` - Minimum monthly revenue potential
- `--dry-run` - Show what would be generated without doing it

#### `php artisan pages:bulk-expand`
**Location:** `app/Console/Commands/BulkExpandPages.php`

**Purpose:** Generate multiple batches in sequence

**Options:**
- `--site=ID`
- `--services=1,2,3` - Multiple service IDs
- `--counties=1,2,3` - Multiple county IDs
- `--batch-size=50`
- `--delay=30` - Seconds between batches

---

## 🗄️ Database Changes

### Migration: `create_page_generation_batches_table`

```php
Schema::create('page_generation_batches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('site_id')->constrained()->onDelete('cascade');
    $table->foreignId('client_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    
    $table->string('batch_type'); // 'opportunity_list', 'service_wide', 'county_wide', 'state_wide'
    $table->string('initiator_type')->nullable(); // 'command', 'api', 'dashboard'
    $table->string('initiator_id')->nullable(); // e.g., command name or user action
    
    $table->unsignedInteger('requested_count')->default(0);
    $table->unsignedInteger('completed_count')->default(0);
    $table->unsignedInteger('failed_count')->default(0);
    $table->unsignedInteger('skipped_count')->default(0);
    
    $table->json('parameters')->nullable(); // Store generation options
    $table->json('filters')->nullable(); // Store opportunity filters used
    $table->json('error_summary')->nullable(); // Common errors encountered
    
    $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])
        ->default('pending');
    
    $table->timestamp('started_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
    
    $table->index(['site_id', 'status']);
    $table->index(['client_id', 'created_at']);
});
```

### Migration: `add_batch_id_to_location_pages_table`

```php
Schema::table('location_pages', function (Blueprint $table) {
    $table->foreignId('batch_id')->nullable()->after('site_id')
        ->constrained('page_generation_batches')->nullOnDelete();
    $table->index('batch_id');
});
```

### Migration: `add_generated_at_to_seo_opportunities_table`

```php
Schema::table('seo_opportunities', function (Blueprint $table) {
    $table->timestamp('generated_at')->nullable()->after('last_analyzed_at');
    $table->foreignId('generated_page_id')->nullable()->after('location_page_id')
        ->constrained('location_pages')->nullOnDelete();
});
```

---

## 🔄 Generation Flow

### Scenario 1: Generate from Top Opportunities (Widget)

```
User clicks "Generate Top 10" in TopRevenueOpportunitiesWidget
    ↓
BulkPageExpansionService::generateFromOpportunities($opportunities)
    ↓
Create PageGenerationBatch record (status: 'pending')
    ↓
Dispatch GenerateLocationPageJob for each opportunity
    ↓
Each job:
    - Check if page already exists (skip if yes)
    - Call LocationPageGeneratorService
    - Save LocationPage
    - Update SeoOpportunity (generated_at, generated_page_id, status: 'completed')
    - Log success/failure
    ↓
When all jobs complete:
    - Update batch status to 'completed'
    - Dispatch UpdateSitemapAfterBatchJob
    - Dispatch CreateBaselineSnapshotJob
    - Flash success message to user
```

### Scenario 2: Generate All Pages for One Service

```
Admin runs: php artisan pages:generate-batch --site=1 --service=2 --limit=50
    ↓
BulkPageExpansionService::generateForService($site, $service)
    ↓
Fetch top 50 pending opportunities for this service × all cities
    ↓
[Same flow as Scenario 1]
```

### Scenario 3: Generate County-Wide (e.g., King County)

```
Admin runs: php artisan pages:generate-batch --site=1 --county=1 --limit=50
    ↓
BulkPageExpansionService::generateForCounty($site, $county)
    ↓
Fetch all cities in King County
Fetch all services
Create service × city opportunities (if not existing)
Select top 50 by priority
    ↓
[Same flow as Scenario 1]
```

---

## 🛡️ Safety Measures

### 1. **Duplicate Prevention**
```php
// In GenerateLocationPageJob
$existing = LocationPage::where([
    'site_id' => $this->siteId,
    'service_id' => $this->serviceId,
    'city_id' => $this->cityId,
])->first();

if ($existing) {
    Log::info("Page already exists", ['page_id' => $existing->id]);
    return; // Skip, don't fail
}
```

### 2. **Batch Size Limit**
```php
// In BulkPageExpansionService
const MAX_BATCH_SIZE = 50;

public function generateFromOpportunities(Collection $opportunities, array $options = [])
{
    $limit = min($opportunities->count(), self::MAX_BATCH_SIZE);
    $opportunities = $opportunities->take($limit);
    // ...
}
```

### 3. **Queue Rate Limiting**
```php
// In GenerateLocationPageJob
use Illuminate\Queue\Middleware\RateLimited;

public function middleware()
{
    return [new RateLimited('page-generation')];
}

// In RouteServiceProvider or queue config
RateLimiter::for('page-generation', function ($job) {
    return Limit::perMinute(10); // Max 10 pages/minute
});
```

### 4. **Error Handling**
```php
// In GenerateLocationPageJob
public function failed(Throwable $exception)
{
    Log::error("Failed to generate page", [
        'site_id' => $this->siteId,
        'service_id' => $this->serviceId,
        'city_id' => $this->cityId,
        'error' => $exception->getMessage(),
    ]);
    
    // Update batch failure count
    $batch = PageGenerationBatch::find($this->batchId);
    $batch->increment('failed_count');
    
    // Update opportunity status
    SeoOpportunity::where([
        'site_id' => $this->siteId,
        'service_id' => $this->serviceId,
        'location_id' => $this->cityId,
    ])->update(['status' => 'generation_failed']);
}
```

---

## 📡 Progress Tracking

### Real-Time Updates (Optional Enhancement)
Use Laravel Echo + Redis to broadcast batch progress:

```php
// In GenerateLocationPageJob after success
event(new PageGenerated($this->batchId, $page));

// In frontend (Livewire or JavaScript)
Echo.private('batch.' + batchId)
    .listen('PageGenerated', (e) => {
        updateProgressBar(e.completed, e.total);
    });
```

### Polling-Based (Simpler)
```php
// In Filament widget
protected function getViewData(): array
{
    $activeBatches = PageGenerationBatch::where('status', 'processing')
        ->with('site')
        ->get()
        ->map(fn($batch) => [
            'id' => $batch->id,
            'site' => $batch->site->name,
            'progress' => ($batch->completed_count / $batch->requested_count) * 100,
        ]);
    
    return ['activeBatches' => $activeBatches];
}
```

---

## 🔗 Integration Points

### With Existing Systems:

1. **LocationPageGeneratorService** (already exists)
   - Called by GenerateLocationPageJob
   - No changes needed (unless content improvements desired)

2. **InternalLinkingService** (if exists)
   - Called after page generation
   - Automatically suggests links to/from new page

3. **SchemaGeneratorService** (if exists)
   - Inject LocalBusiness schema into generated pages

4. **SitemapGeneratorService** (to be built or enhanced)
   - Called by UpdateSitemapAfterBatchJob
   - Regenerates sitemap.xml with new URLs

5. **BaselineSnapshot** (already exists)
   - Called after batch to capture "before" state for future comparison

---

## 🧪 Testing Strategy

### Unit Tests
- BulkPageExpansionService methods
- Job execution logic (mock dependencies)
- Duplicate detection
- Batch size limits

### Feature Tests
- End-to-end batch generation flow
- Command execution with various options
- Queue job processing
- Sitemap updates
- State transitions in PageGenerationBatch

### Integration Tests
- Multi-service batch generation
- Error recovery and retry
- Database constraint validation

### Manual Testing Checklist
- [ ] Generate from dashboard widget
- [ ] Generate via artisan command
- [ ] Test duplicate prevention (rerun same batch)
- [ ] Test with 0 opportunities
- [ ] Test with >50 opportunities (should limit)
- [ ] Test job failure handling
- [ ] Verify sitemap updates
- [ ] Check GSC submission (if configured)
- [ ] Validate internal links created
- [ ] Confirm baseline snapshot captured

---

## 📊 Monitoring & Logging

### Key Metrics to Track:
- Average page generation time
- Failure rate per batch
- Most common error types
- Pages generated per day/week
- Queue depth and processing rate

### Log Channels:
```php
// config/logging.php
'page-generation' => [
    'driver' => 'daily',
    'path' => storage_path('logs/page-generation.log'),
    'level' => 'info',
    'days' => 14,
],
```

### Usage:
```php
Log::channel('page-generation')->info('Batch started', [
    'batch_id' => $batch->id,
    'site_id' => $site->id,
    'opportunity_count' => $count,
]);
```

---

## 🚀 Deployment Checklist

Before going live with Step 3:

1. **Queue Workers**
   - [ ] Ensure queue workers are running (`php artisan queue:work`)
   - [ ] Configure supervisor for production reliability
   - [ ] Set appropriate `--tries` and `--timeout` values

2. **Rate Limiting**
   - [ ] Configure Redis for rate limiting (or use database)
   - [ ] Set reasonable limits (10-20 pages/minute recommended)

3. **Monitoring**
   - [ ] Set up Horizon (or Laravel Pulse) for queue monitoring
   - [ ] Configure failed job alerts (email/Slack)
   - [ ] Add batch completion notifications

4. **Database**
   - [ ] Run migrations in production
   - [ ] Verify indexes created for performance
   - [ ] Ensure foreign key constraints won't cause issues

5. **Storage**
   - [ ] Verify disk space for generated HTML/sitemaps
   - [ ] Configure S3 if serving from CDN
   - [ ] Set up sitemap.xml caching

---

## 🔮 Future Enhancements (Post-Step 3)

- **Scheduled batch generation** via Laravel scheduler
- **AI content variation** (avoid duplicate content penalties)
- **Image generation** per location page
- **Video embeds** (e.g., service explainer + location)
- **Multi-language support** for pages
- **A/B testing** different page templates
- **Automatic GSC performance tracking** post-generation
- **Smart regeneration** (update pages that dropped in rankings)

---

## 📝 Complete Step 3 Code Prompt

See [STEP_3_CODE_PROMPT.md](./STEP_3_CODE_PROMPT.md) for the full implementation prompt you can use with AI coding assistants.

---

**Status:** 🟢 **READY TO IMPLEMENT**

**Estimated Effort:** 6-8 hours for core functionality + 2-4 hours for testing/polish

**Risk Level:** LOW - Well-scoped, proven patterns, clear requirements
