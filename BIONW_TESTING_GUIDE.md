# BioNW.com Testing Guide

## Pre-requisites

### 1. Google Cloud Console Setup

1. Go to https://console.cloud.google.com/
2. Create a new project (or select existing)
3. Enable **Google Search Console API**:
   - APIs & Services → Library
   - Search for "Google Search Console API"
   - Click Enable

4. Create OAuth 2.0 Credentials:
   - APIs & Services → Credentials
   - Create Credentials → OAuth client ID
   - Application type: Web application
   - Name: "SEOAI Platform"
   - Authorized redirect URIs: `https://yourdomain.com/admin/gsc/callback` (adjust for local: `http://localhost/admin/gsc/callback`)
   - Save Client ID and Client Secret

5. Add test users (for local development):
   - OAuth consent screen → Add users
   - Add your Google account that has access to BioNW.com Search Console

### 2. Environment Configuration

Add to your `.env` file:

```env
# Google Search Console Configuration
GOOGLE_CLIENT_ID=your-client-id-here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://localhost/admin/gsc/callback
```

### 3. Install Google API PHP Client

The service requires Google's PHP client library. If not already installed:

```bash
composer require google/apiclient
```

### 4. Run Migrations

```bash
php artisan migrate
```

This creates:
- `performance_metrics` table
- `baseline_snapshots` table
- `optimization_runs` table
- Adds GSC columns to `sites` table

---

## Testing Steps

### Phase 1: Connect BioNW.com to GSC

#### Option A: Manual Database Setup (Quick Start)

If you want to skip OAuth flow for now and manually configure:

```php
// In tinker: php artisan tinker

$site = Site::where('domain', 'bionw.com')->first();

// You'll need to manually go through OAuth once to get tokens
// Alternative: Use Google OAuth Playground (https://developers.google.com/oauthplayground/)
// to get tokens manually

$site->update([
    'gsc_property_url' => 'https://bionw.com/',  // or sc-domain:bionw.com
    'gsc_access_token' => encrypt('your-access-token'),
    'gsc_refresh_token' => encrypt('your-refresh-token'),
    'gsc_token_expires_at' => now()->addHour(),
    'gsc_sync_status' => 'pending',
]);
```

#### Option B: Build Simple OAuth Flow (Recommended for Production)

Create a simple controller for OAuth:

```php
// routes/web.php
Route::get('/admin/gsc/authorize/{site}', function ($siteId) {
    $site = Site::findOrFail($siteId);
    $gscService = app(\App\Services\GscSyncService::class);
    $authUrl = $gscService->getAuthorizationUrl();
    
    session(['gsc_connecting_site_id' => $site->id]);
    
    return redirect($authUrl);
})->name('gsc.authorize');

Route::get('/admin/gsc/callback', function (Request $request) {
    $code = $request->get('code');
    $siteId = session('gsc_connecting_site_id');
    
    if (!$code || !$siteId) {
        return redirect('/admin/sites')->with('error', 'GSC authorization failed');
    }
    
    $site = Site::findOrFail($siteId);
    $gscService = app(\App\Services\GscSyncService::class);
    
    // Exchange code for tokens
    $tokens = $gscService->exchangeAuthorizationCode($code);
    
    // List available properties
    $properties = $gscService->listProperties($tokens['access_token']);
    
    // For BioNW.com, select the matching property
    $propertyUrl = collect($properties)->firstWhere('url', 'like', '%bionw.com%')['url'] ?? null;
    
    if ($propertyUrl) {
        $gscService->connectSite($site, $propertyUrl, $tokens);
        return redirect('/admin/sites')->with('success', 'Connected to Google Search Console');
    }
    
    return redirect('/admin/sites')->with('error', 'Property not found');
})->name('gsc.callback');
```

Then visit: `/admin/gsc/authorize/{site-id}` to start OAuth flow.

---

### Phase 2: Sync Historical Data

#### First Sync (Last 90 Days)

```bash
# Sync BioNW.com specifically
php artisan gsc:sync --site=bionw.com --days=90

# Monitor progress
php artisan gsc:stats --site=bionw.com
```

**Expected output:**
```
Syncing 1 site(s)...
📊 Syncing bionw.com...
   ✓ bionw.com: 2,847 page metrics, 8,921 query metrics imported
✓ GSC sync completed
```

#### Verify Data Import

```bash
php artisan tinker
```

```php
$site = Site::where('domain', 'bionw.com')->first();

// Check total metrics imported
$site->performanceMetrics()->count();
// Expected: Several thousand

// Check date range
$site->performanceMetrics()->min('date');
$site->performanceMetrics()->max('date');

// Check page-level aggregates
$site->performanceMetrics()
    ->whereNull('query')
    ->orderByDesc('impressions')
    ->take(10)
    ->get(['url', 'clicks', 'impressions', 'ctr', 'average_position']);

// Find low CTR opportunities
$opportunities = PerformanceMetric::where('site_id', $site->id)
    ->lowCtrOpportunities(1000, 0.03)
    ->take(10)
    ->get(['url', 'impressions', 'clicks', 'ctr']);

dd($opportunities);
```

**What to look for:**
- ✅ URLs from BioNW.com present
- ✅ Clicks, impressions, CTR values realistic
- ✅ Date range covers last 90 days
- ✅ Some URLs have `page_id` or `location_page_id` (if they match existing pages)
- ✅ Query-level data (where `query` IS NOT NULL)

---

### Phase 3: Test URL Resolution

Check which GSC URLs mapped to existing Page or LocationPage records:

```php
use App\Services\PageUrlResolver;

$site = Site::where('domain', 'bionw.com')->first();
$resolver = app(PageUrlResolver::class);

// Check resolution stats
$totalUrls = $site->performanceMetrics()->distinct('url')->count('url');
$resolvedToPage = $site->performanceMetrics()->whereNotNull('page_id')->distinct('url')->count('url');
$resolvedToLocationPage = $site->performanceMetrics()->whereNotNull('location_page_id')->distinct('url')->count('url');

echo "Total unique URLs: {$totalUrls}\n";
echo "Resolved to Page: {$resolvedToPage}\n";
echo "Resolved to LocationPage: {$resolvedToLocationPage}\n";
echo "Unresolved: " . ($totalUrls - $resolvedToPage - $resolvedToLocationPage) . "\n";

// Get unresolved URLs with traffic
$unresolved = $resolver->getUnresolvedUrls($site, $minImpressions = 1000);
dd($unresolved);
```

**What this tells you:**
- Which URLs are not matching existing pages (might need to crawl them)
- Which URLs have traffic but aren't in your system yet
- Whether your URL path structure matches GSC URLs

**If resolution rate is low (<30%):**
- Run `php artisan crawl:site bionw.com` to discover more pages
- Or manually create Page records for high-traffic unresolved URLs

---

### Phase 4: Create Baseline Snapshots

Test snapshot creation for pages with performance data:

```php
$page = Page::where('site_id', $site->id)
    ->whereHas('performanceMetrics')
    ->first();

if (!$page) {
    echo "No pages with performance data yet. Check URL resolution.\n";
    exit;
}
```

```bash
# Via command
php artisan baseline:snapshot page {page-id}

# Or via code
php artisan tinker
```

```php
use App\Services\PerformanceAggregationService;

$page = Page::first(); // or specific page with traffic
$perfService = app(PerformanceAggregationService::class);

// Get performance summary
$summary = $perfService->get30DaySummary($page);
dd($summary);

// Create snapshot
$snapshot = BaselineSnapshot::createFromModel($page, $summary);
echo "Snapshot ID: {$snapshot->id}\n";
echo "Performance: {$summary['clicks']} clicks, {$summary['impressions']} impressions\n";
```

**Expected result:**
- Snapshot created with `id`, `snapshot_date`, `title`, `content_hash`
- `performance_snapshot_json` contains 30-day aggregates
- Visible in `/admin/baseline-snapshots`

---

### Phase 5: Test Optimization Run Logging

Simulate what Phase 2 will do (title optimization recommendation):

```php
use App\Enums\OptimizationType;
use App\Enums\OptimizationStatus;

$page = Page::whereHas('performanceMetrics')->first();
$snapshot = BaselineSnapshot::createFromModel($page);

// Create a "detected" opportunity
$run = OptimizationRun::create([
    'site_id' => $page->site_id,
    'optimizable_type' => Page::class,
    'optimizable_id' => $page->id,
    'optimization_type' => OptimizationType::TITLE,
    'status' => OptimizationStatus::DETECTED,
    'confidence_score' => null, // To be calculated in Phase 2
    'baseline_snapshot_id' => $snapshot->id,
    'before_state_json' => [
        'title' => $page->title,
    ],
]);

echo "Created optimization run ID: {$run->id}\n";

// Simulate progression to "recommended"
$run->update([
    'status' => OptimizationStatus::RECOMMENDED,
    'confidence_score' => 87,
    'proposed_state_json' => [
        'title' => 'NEW IMPROVED TITLE - Test',
    ],
    'predicted_impact_json' => [
        'ctr_lift' => 0.5,
        'click_estimate' => 120,
    ],
]);

// Check in admin
echo "View at: /admin/optimization-runs/{$run->id}\n";
```

**Verify:**
- Run visible in `/admin/optimization-runs`
- Status badge shows correct color (info for "recommended")
- Before/after state displayed
- Baseline snapshot linked

---

### Phase 6: Verify Filament Admin

#### Performance Metrics Resource

Visit: `/admin/performance-metrics`

**Test filters:**
- Filter by site (should show BioNW.com)
- Date range filter (last 30 days)
- "Low CTR Opportunities" toggle
- "Resolved to Pages" toggle

**Check badge count:**
- Navigation should show badge with count of low CTR opportunities

#### Baseline Snapshots Resource

Visit: `/admin/baseline-snapshots`

**Verify:**
- Snapshots created in Phase 4 are visible
- Performance data displays inline (e.g., "450 clicks, 12500 imp, 3.6% CTR")
- Can filter by site and type (Page vs LocationPage)

#### Optimization Runs Resource

Visit: `/admin/optimization-runs`

**Verify:**
- Test runs created in Phase 5 are visible
- Status badges show correct colors
- "Change Summary" column shows before → after
- Click into detail view shows full JSON states

#### Sites Resource

Visit: `/admin/sites`

**Check BioNW.com entry:**
- GSC connection status visible
- Last sync time displayed
- Can see related performance metrics, snapshots, runs

---

### Phase 7: Scheduler & Automation

#### Test Scheduled Sync

```bash
# Run scheduler once (as if it's 2 AM)
php artisan schedule:run

# Or run the command manually
php artisan gsc:sync
```

#### Set Up Cron (Production)

Add to crontab:

```bash
* * * * * cd /path/to/seoai && php artisan schedule:run >> /dev/null 2>&1
```

This will run `gsc:sync` daily at 2 AM per the schedule in `routes/console.php`.

#### Monitor Sync Health

```bash
# Create a monitoring script
php artisan tinker
```

```php
$site = Site::where('domain', 'bionw.com')->first();

// Check last sync
echo "Last synced: " . $site->gsc_last_sync_at->diffForHumans() . "\n";
echo "Sync status: {$site->gsc_sync_status}\n";

if ($site->gsc_sync_error) {
    echo "Error: {$site->gsc_sync_error}\n";
}

// Check data freshness
$latestMetric = $site->performanceMetrics()->latest('date')->first();
echo "Latest data: {$latestMetric->date}\n";

// Should be ~1-2 days ago (GSC has delay)
```

---

## Troubleshooting

### Issue: "Invalid credentials" error

**Cause:** OAuth tokens expired or invalid

**Fix:**
```bash
php artisan tinker
$site = Site::where('domain', 'bionw.com')->first();
$site->update(['gsc_sync_status' => 'pending']);
```

Then re-authorize via OAuth flow.

### Issue: No URLs resolved to pages

**Cause:** URL formats don't match between GSC and your Page records

**Fix:**
```php
// Check URL formats
$gscUrl = 'https://bionw.com/some-page/';
$pageUrl = Page::first()->url;

echo "GSC format: {$gscUrl}\n";
echo "Page format: {$pageUrl}\n";
```

Adjust `PageUrlResolver::normalizeUrl()` method if needed.

### Issue: Low CTR opportunities filter returns nothing

**Cause:** Not enough data yet or thresholds too strict

**Fix:**
```php
// Lower thresholds temporarily
PerformanceMetric::where('site_id', $site->id)
    ->where('impressions', '>=', 100)  // Lower from 1000
    ->where('ctr', '<=', 0.05)          // Raise from 0.03
    ->count();
```

### Issue: Duplicate key error on sync

**Cause:** Unique constraint violation on `performance_unique` index

**Fix:** This shouldn't happen (`updateOrCreate` handles it), but if it does:
```bash
# Check for duplicates
SELECT site_id, url, query, date, device, country, COUNT(*) 
FROM performance_metrics 
GROUP BY site_id, url, query, date, device, country 
HAVING COUNT(*) > 1;

# Delete duplicates (keep newest)
```

---

## Success Checklist

Before proceeding to Phase 2 (Title Optimization), verify:

- [ ] BioNW.com connected to GSC (check in `/admin/sites`)
- [ ] 90 days of performance data synced
- [ ] At least 1,000 metrics imported
- [ ] URL resolution working (>20% resolved)
- [ ] Low CTR opportunities visible (>10 pages)
- [ ] Baseline snapshots can be created
- [ ] Optimization runs logged successfully
- [ ] All Filament resources load without errors
- [ ] Scheduler configured and tested
- [ ] No errors in `storage/logs/laravel.log`

**When all checked:** Foundation is solid, green light for Phase 2! 🚀
