# Phase 2 & 3 Quick Start Guide

## 🚀 Getting Started

### Step 1: Run Migrations
```bash
cd "/Users/noragenetti/Library/CloudStorage/Dropbox/! SEO AI CO/seoai"
php artisan migrate
```

This will create all new tables:
- ✅ Enhanced clients (multi-tenant)
- ✅ Enhanced users (with client relationships)
- ✅ roles & role_user
- ✅ title_recommendations
- ✅ automation_logs
- ✅ plans, subscriptions, usage_records, invoices
- ✅ Enhanced opportunities (polymorphic)

---

## 📊 Seed Initial Data

### Create Default Plans
```bash
php artisan tinker
```

```php
use App\Models\Plan;

Plan::create([
    'name' => 'Starter',
    'slug' => 'starter',
    'description' => 'Perfect for small businesses',
    'monthly_price' => 49.00,
    'yearly_price' => 490.00,
    'currency' => 'USD',
    'max_sites' => 1,
    'max_pages' => 100,
    'max_ai_operations_per_month' => 50,
    'max_users' => 2,
    'has_api_access' => false,
    'has_white_label' => false,
    'has_priority_support' => false,
    'is_active' => true,
    'is_public' => true,
    'sort_order' => 1,
]);

Plan::create([
    'name' => 'Professional',
    'slug' => 'professional',
    'description' => 'For growing agencies',
    'monthly_price' => 149.00,
    'yearly_price' => 1490.00,
    'currency' => 'USD',
    'max_sites' => 5,
    'max_pages' => 1000,
    'max_ai_operations_per_month' => 500,
    'max_users' => 10,
    'has_api_access' => true,
    'has_white_label' => false,
    'has_priority_support' => true,
    'is_active' => true,
    'is_public' => true,
    'sort_order' => 2,
]);

Plan::create([
    'name' => 'Enterprise',
    'slug' => 'enterprise',
    'description' => 'Unlimited scale',
    'monthly_price' => 499.00,
    'yearly_price' => 4990.00,
    'currency' => 'USD',
    'max_sites' => 999,
    'max_pages' => 999999,
    'max_ai_operations_per_month' => 999999,
    'max_users' => 999,
    'has_api_access' => true,
    'has_white_label' => true,
    'has_priority_support' => true,
    'is_active' => true,
    'is_public' => true,
    'sort_order' => 3,
]);
```

---

## 🧪 Test the Services

### Test Opportunity Detection
```php
use App\Services\OpportunityDetectionService;
use App\Models\Site;

$site = Site::first();
$service = app(OpportunityDetectionService::class);
$results = $service->scanSite($site);

// Results show:
// - Low CTR opportunities
// - High impression pages
// - Thin content pages
```

### Test Title Optimization
```php
use App\Services\TitleOptimizationService;
use App\Models\Opportunity;

$opportunity = Opportunity::where('type', 'low_ctr')->first();
$service = app(TitleOptimizationService::class);
$recommendations = $service->generateFromOpportunity($opportunity);

// Generates 3 title variants with confidence scores
```

### Test Usage Tracking
```php
use App\Services\UsageTrackingService;
use App\Models\Client;

$client = Client::first();
$service = app(UsageTrackingService::class);

// Check limits
$summary = $service->getUsageSummary($client);
// Returns: ['sites' => [...], 'pages' => [...], 'ai_operations' => [...]]

// Track usage
$service->track($client, 'ai_operation', 1);
```

### Create a Tenant
```php
use App\Services\TenantService;

$service = app(TenantService::class);

$client = $service->createTenant([
    'name' => 'Acme Corp',
    'company_name' => 'Acme Corporation',
    'email' => 'admin@acme.com',
    'owner_name' => 'John Doe',
    'password' => 'secure-password',
    'subdomain' => 'acme',
    'max_sites' => 5,
    'max_pages' => 500,
]);

// Creates client + owner user with 14-day trial
```

---

## 🤖 Run Automation Jobs Manually

### Daily GSC Sync
```bash
php artisan tinker
```

```php
use App\Jobs\DailyGscSyncJob;

// All sites
dispatch(new DailyGscSyncJob());

// Specific site
dispatch(new DailyGscSyncJob(siteId: 1));
```

### Weekly Opportunity Scan
```php
use App\Jobs\WeeklyOpportunityScanJob;

dispatch(new WeeklyOpportunityScanJob());
```

### Monthly Content Refresh
```php
use App\Jobs\MonthlyContentRefreshJob;

dispatch(new MonthlyContentRefreshJob());
```

---

## ⏰ Enable Scheduled Jobs

### 1. Configure Queue Worker (Production)

**Supervisor Config** (`/etc/supervisor/conf.d/seoai-worker.conf`):
```ini
[program:seoai-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/seoai/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/seoai/storage/logs/worker.log
stopwaitsecs=3600
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start seoai-worker:*
```

### 2. Enable Laravel Scheduler

Add to crontab (`crontab -e`):
```cron
* * * * * cd /path/to/seoai && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Verify Schedule
```bash
php artisan schedule:list
```

Should show:
- `daily_gsc_sync` - Daily at 2:00 AM
- `weekly_opportunity_scan` - Sundays at 3:00 AM
- `monthly_content_refresh` - 1st of month at 4:00 AM

---

## 📱 Access Filament Admin

### View New Resources

1. **Title Recommendations**
   - URL: `/admin/title-recommendations`
   - Features: Approve/reject, view predictions, apply changes
   - Badge shows pending count

2. **Dashboard Widgets**
   - Navigate to: `/admin`
   - See: Stats overview, top opportunities, performance trend, usage limits

---

## 🔍 Query Examples

### Get All Open Opportunities for a Client
```php
use App\Models\Opportunity;
use App\Models\Site;

$clientId = 1;

$opportunities = Opportunity::whereIn('site_id', 
    Site::where('client_id', $clientId)->pluck('id')
)
->where('status', 'open')
->orderByDesc('score')
->get();
```

### Get Pending Title Recommendations
```php
use App\Models\TitleRecommendation;

$pending = TitleRecommendation::pending()
    ->highConfidence(70)
    ->with('site', 'recommendable')
    ->get();
```

### Check Client Usage
```php
use App\Models\Client;
use App\Services\UsageTrackingService;

$client = Client::find(1);
$service = app(UsageTrackingService::class);

$sites = $service->checkLimit($client, 'site');
$pages = $service->checkLimit($client, 'page');
$aiOps = $service->checkLimit($client, 'ai_operation');

if ($sites['exceeded']) {
    echo "Site limit reached!";
}
```

### View Automation Log Summary
```php
use App\Models\AutomationLog;

$logs = AutomationLog::forJob('daily_gsc_sync')
    ->recent(7)
    ->completed()
    ->get();

foreach ($logs as $log) {
    echo "{$log->started_at}: {$log->items_succeeded} succeeded, {$log->items_failed} failed\n";
}
```

---

## 🎯 Common Workflows

### Workflow 1: Generate and Apply Title Recommendations

```php
// 1. Detect opportunities
$service = app(OpportunityDetectionService::class);
$service->scanSite($site);

// 2. Generate recommendations
$titleService = app(TitleOptimizationService::class);
$titleService->generateBatchFromOpportunities($site, 10);

// 3. Review in Filament Admin
// Navigate to /admin/title-recommendations

// 4. Approve recommendation
$rec = TitleRecommendation::find(1);
$rec->update(['status' => 'approved', 'reviewed_by' => auth()->id()]);

// 5. Apply recommendation
$titleService->applyRecommendation($rec);
```

### Workflow 2: Onboard New Client

```php
$tenantService = app(TenantService::class);

// 1. Create tenant
$client = $tenantService->createTenant([
    'name' => 'New Agency',
    'email' => 'contact@newagency.com',
    'owner_name' => 'Agency Owner',
    'password' => bcrypt('secure-pass'),
]);

// 2. Create subscription
$plan = Plan::where('slug', 'professional')->first();
$subscription = Subscription::create([
    'client_id' => $client->id,
    'plan_id' => $plan->id,
    'status' => 'trial',
    'billing_cycle' => 'monthly',
    'amount' => $plan->monthly_price,
    'starts_at' => now(),
    'trial_ends_at' => now()->addDays(14),
]);

// 3. Client can now create sites
```

### Workflow 3: Monitor System Health

```php
// Recent automation failures
$failures = AutomationLog::failed()
    ->recent(7)
    ->with('site', 'client')
    ->get();

// Usage across all clients
$clients = Client::with('activeSubscription.plan')->get();
foreach ($clients as $client) {
    $stats = app(TenantService::class)->getTenantStats($client);
    echo "{$client->name}: {$stats['sites_count']} sites, {$stats['opportunities_count']} opportunities\n";
}
```

---

## 📊 Monitoring & Debugging

### Check Queue Status
```bash
php artisan queue:failed
php artisan queue:work --once  # Test single job
```

### View Automation Logs
```bash
php artisan tinker
```

```php
use App\Models\AutomationLog;

// Recent failures
AutomationLog::failed()->recent(1)->get()->each(function($log) {
    echo "Job: {$log->job_name}\n";
    echo "Error: {$log->error_message}\n";
    echo "---\n";
});
```

### Test Performance Queries
```php
use App\Models\PerformanceMetric;
use Illuminate\Support\Facades\DB;

// Low CTR pages
$lowCtr = PerformanceMetric::select([
        'page_type',
        'page_id',
        DB::raw('SUM(impressions) as total_impressions'),
        DB::raw('SUM(clicks) / SUM(impressions) as ctr'),
    ])
    ->where('site_id', 1)
    ->where('date', '>=', now()->subDays(30))
    ->groupBy('page_type', 'page_id')
    ->having('total_impressions', '>', 1000)
    ->having('ctr', '<', 0.02)
    ->get();
```

---

## 🛠️ Troubleshooting

### Issue: Jobs not running
**Solution:**
```bash
# Check queue worker
ps aux | grep "queue:work"

# Restart supervisor
sudo supervisorctl restart seoai-worker:*

# Check for failed jobs
php artisan queue:failed
```

### Issue: Migrations fail
**Solution:**
```bash
# Check current migrations
php artisan migrate:status

# Rollback specific batch
php artisan migrate:rollback --step=1

# Re-run
php artisan migrate
```

### Issue: Filament resource not showing
**Solution:**
```bash
# Clear cache
php artisan filament:cache-components
php artisan optimize:clear

# Check resource is registered
php artisan route:list | grep filament
```

---

## 📚 File Reference

### New Files Created (40+ files)

**Migrations:**
- `2026_03_15_000001_enhance_multi_tenant_structure.php`
- `2026_03_15_000002_create_title_recommendations_table.php`
- `2026_03_15_000003_create_automation_logs_table.php`
- `2026_03_15_000004_create_plans_and_subscriptions_tables.php`
- `2026_03_15_000005_enhance_opportunities_for_phase_2.php`

**Models:**
- `TitleRecommendation.php`, `AutomationLog.php`, `Plan.php`
- `Subscription.php`, `UsageRecord.php`, `Invoice.php`, `Role.php`
- Enhanced: `Client.php`, `User.php`, `Opportunity.php`

**Services:**
- `OpportunityDetectionService.php`
- `TitleOptimizationService.php`
- `UsageTrackingService.php`
- `TenantService.php`
- `BaselineSnapshotService.php`

**Jobs:**
- `DailyGscSyncJob.php`
- `WeeklyOpportunityScanJob.php`
- `MonthlyContentRefreshJob.php`

**Filament:**
- Resources: `TitleRecommendationResource.php` + 3 pages
- Widgets: `SeoDashboardStats.php`, `TopOpportunitiesWidget.php`, `PerformanceTrendChart.php`, `UsageLimitsWidget.php`
- View: `usage-limits-widget.blade.php`

**Config:**
- Enhanced: `routes/console.php` (scheduler)

**Documentation:**
- `PHASE_2_3_ARCHITECTURE.md` (comprehensive guide)
- `PHASE_2_3_QUICKSTART.md` (this file)

---

## ✅ Implementation Checklist

Before going live:

- [ ] Run all migrations
- [ ] Seed plans
- [ ] Test each service in tinker
- [ ] Configure queue worker
- [ ] Enable cron scheduler
- [ ] Test Filament widgets load
- [ ] Verify tenant isolation
- [ ] Test opportunity detection with real data
- [ ] Configure Stripe keys (for billing)
- [ ] Set up monitoring/logging
- [ ] Review error handling
- [ ] Performance test with 1000+ pages

---

## 🎉 You're Ready!

The Phase 2 & 3 architecture is now complete. You have:

✅ Full multi-tenant SaaS infrastructure
✅ Automated SEO opportunity detection
✅ AI title optimization engine
✅ Usage tracking & billing foundation
✅ Comprehensive automation jobs
✅ Professional admin interface

**Next:** Run migrations, seed data, and test the workflows above.

For detailed architecture information, see: `PHASE_2_3_ARCHITECTURE.md`
