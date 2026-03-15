# Revenue Opportunity Engine - Quick Start

## 1. Run Migration (Already Done ✓)

```bash
php artisan migrate --path=database/migrations/2026_03_15_200000_create_seo_opportunities_table.php
```

## 2. Scan for Opportunities

Use the artisan command to scan:

```bash
# Basic scan with defaults
php artisan opportunities:scan

# Custom parameters
php artisan opportunities:scan \
  --min-priority=70 \
  --min-volume=50 \
  --service-value=750 \
  --conversion-rate=0.025
```

**Output Example:**
```
🔍 Scanning for SEO Revenue Opportunities...

Site: Buffalo HVAC Pro
State: New York

Scanning Parameters:
┌──────────────────────┬────────┐
│ Min Priority Score   │ 60     │
│ Min Search Volume    │ 20     │
│ Service Value        │ $500   │
│ Conversion Rate      │ 2%     │
└──────────────────────┴────────┘

✅ Scan Complete!
┌─────────────────┬────────┐
│ Created         │ 87     │
│ Updated         │ 13     │
│ Skipped         │ 200    │
│ Total Processed │ 300    │
└─────────────────┴────────┘

🎯 Top 10 Revenue Opportunities:
┌──────────┬───────────┬─────────────────┬──────────────┬────────┬──────────┬──────────┬───────┬──────────┐
│ Priority │ Type      │ Service         │ Location     │ Volume │ Revenue  │ Potential│ Comp. │ Status   │
├──────────┼───────────┼─────────────────┼──────────────┼────────┼──────────┼──────────┼───────┼──────────┤
│ 92       │ Quick Win │ Emergency Plumb │ Buffalo, NY  │ 450    │ $1,275   │ 85%      │ 42    │ pending  │
│ 89       │ High Vol  │ HVAC Repair     │ Rochester,NY │ 520    │ $1,450   │ 82%      │ 55    │ pending  │
│ 87       │ Quick Win │ Water Heater    │ Syracuse, NY │ 380    │ $1,020   │ 83%      │ 38    │ pending  │
└──────────┴───────────┴─────────────────┴──────────────┴────────┴──────────┴──────────┴───────┴──────────┘

💰 Total Revenue Potential (Top 10): $11,850/month

⚡ Quick Wins (Top 5):
┌─────────────────┬──────────────┬──────────────┐
│ Service         │ Location     │ Est. Revenue │
├─────────────────┼──────────────┼──────────────┤
│ Emergency Plumb │ Buffalo, NY  │ $1,275       │
│ Water Heater    │ Syracuse, NY │ $1,020       │
│ Drain Cleaning  │ Albany, NY   │ $890         │
└─────────────────┴──────────────┴──────────────┘

💡 Tip: View opportunities in the admin dashboard at /admin
💡 Generate pages directly from the TopRevenueOpportunitiesWidget
```

## 3. View in Dashboard

1. Navigate to `/admin`
2. See **TopRevenueOpportunitiesWidget** on dashboard
3. Review top 20 opportunities ranked by priority score
4. Total potential monthly revenue displayed in heading

## 4. Generate Pages

### From Dashboard Widget:

**Single Page:**
1. Find opportunity in table
2. Click "Generate Page" button
3. Review metrics in confirmation modal
4. Click "Generate Page"
5. Page created automatically
6. Notification with link to view page

**Quick Wins First:**
1. Look for opportunities with "Quick Win" badge (green)
2. These have low competition + high rank potential
3. Generate these first for fastest results

**Approve for Later:**
1. Click "Approve" on opportunity
2. Status changes to "approved"
3. Generate when ready

**Dismiss Low-Value:**
1. Click "Dismiss" on irrelevant opportunities
2. Won't appear in future scans

### Programmatically:

```php
use App\Models\SeoOpportunity;
use App\Models\Site;
use App\Services\LocationPageGeneratorService;
use App\Services\RevenueOpportunityService;

// Get top opportunity
$site = Site::where('is_active', true)->first();
$revenueService = app(RevenueOpportunityService::class);
$topOpportunity = $revenueService->getTopOpportunities($site, 1)->first();

// Generate page
$generator = app(LocationPageGeneratorService::class);
$serviceLocation = \App\Models\ServiceLocation::firstOrCreate([
    'service_id' => $topOpportunity->service_id,
    'city_id' => $topOpportunity->location_id,
], [
    'state_id' => $topOpportunity->location->state_id,
    'county_id' => $topOpportunity->location->county_id,
]);

$result = $generator->generateFromOpportunity($serviceLocation, $site);

if ($result['success']) {
    $topOpportunity->markAsCompleted(
        \App\Models\LocationPage::find($result['location_page_id'])
    );
    echo "✅ Page generated! Revenue potential: $" . $topOpportunity->estimated_monthly_revenue . "/mo\n";
}
```

## 5. Monitor Performance

### Update Opportunity Metrics:

```php
// Update single opportunity with latest GSC data
$opportunity = SeoOpportunity::find(1);
$revenueService->updateOpportunityPerformance($opportunity);

// If performing well (≥100 impressions, ≤10 position)
// Status automatically changes to 'monitoring'
```

### Refresh All Opportunities:

```php
// Re-analyze all opportunities with latest data
$result = $revenueService->refreshOpportunities($site);

// Creates new opportunities + updates existing ones
```

## 6. Query Opportunities

```php
// Top 20 by priority
$top = $revenueService->getTopOpportunities($site, 20);

// Quick wins (low comp + high potential)
$quickWins = $revenueService->getQuickWins($site, 10);

// High revenue (≥$200/mo)
$highRevenue = $revenueService->getHighRevenueOpportunities($site, 200, 20);

// By type
$newPages = $revenueService->getOpportunitiesByType($site, 'new_page');
$underperforming = $revenueService->getOpportunitiesByType($site, 'underperforming');

// Using scopes
$pending = SeoOpportunity::where('site_id', $site->id)
    ->pending()
    ->highPriority(80)
    ->get();

$quickWins = SeoOpportunity::where('site_id', $site->id)
    ->quickWins()
    ->get();
```

## 7. Schedule Automated Scans

Add to `routes/console.php`:

```php
use App\Models\Site;
use App\Services\RevenueOpportunityService;

// Weekly opportunity scan
Schedule::call(function () {
    $sites = Site::where('is_active', true)->get();
    $revenueService = app(RevenueOpportunityService::class);
    
    foreach ($sites as $site) {
        $revenueService->refreshOpportunities($site, [
            'min_priority_score' => 60,
            'min_search_volume' => 20,
        ]);
    }
})->weekly();

// Daily performance updates
Schedule::call(function () {
    $opportunities = SeoOpportunity::whereNotNull('location_page_id')
        ->whereIn('status', ['completed', 'monitoring'])
        ->get();
    
    $revenueService = app(RevenueOpportunityService::class);
    foreach ($opportunities as $opportunity) {
        $revenueService->updateOpportunityPerformance($opportunity);
    }
})->daily();
```

## 8. Customize Settings

### Adjust Default Service Value:

In `RevenueOpportunityService.php`:
```php
protected float $defaultServiceValue = 750.00; // Was 500
```

### Adjust Default Conversion Rate:

```php
protected float $defaultConversionRate = 0.03; // Was 0.02 (now 3%)
```

### Per-Scan Customization:

```bash
php artisan opportunities:scan \
  --service-value=1000 \     # $1000 per conversion
  --conversion-rate=0.025    # 2.5% conversion rate
```

## Common Workflows

### Workflow 1: Launch New Site Coverage

```bash
# 1. Scan for all opportunities
php artisan opportunities:scan --min-priority=70

# 2. View in dashboard, identify quick wins
# 3. Generate top 10 quick wins from dashboard
# 4. Monitor performance over 30 days
# 5. Refresh opportunities with new data
php artisan opportunities:scan --min-priority=60
```

### Workflow 2: Optimize Underperforming Pages

```php
// Find underperforming pages
$underperforming = $revenueService->getOpportunitiesByType($site, 'underperforming');

// Review in dashboard
// Click "View Page" to see existing page
// Optimize content, meta tags, internal links
// Monitor improvement in 'current_position'
```

### Workflow 3: Revenue-First Strategy

```php
// Get highest revenue opportunities
$highRevenue = $revenueService->getHighRevenueOpportunities($site, 300, 10);

// Generate these first regardless of competition
// Focus on pages with $300+ monthly potential
// Monitor ROI closely
```

## Troubleshooting

**No opportunities found:**
- Lower `--min-priority` threshold
- Lower `--min-volume` threshold
- Check that services and cities exist in database

**Estimated revenue seems off:**
- Adjust `--service-value` based on actual data
- Adjust `--conversion-rate` based on industry averages
- Customize `estimateSearchVolume()` heuristics

**Widget not showing:**
- Check that widget is registered in `AdminPanelProvider`
- Ensure active site exists
- Run scan to generate opportunities

## Key Metrics to Track

1. **Conversion Rate**: Actual conversions / total clicks
2. **Service Value**: Average revenue per conversion
3. **Priority Accuracy**: % of high-priority opportunities that perform well
4. **Revenue Realization**: Actual revenue vs. projected revenue
5. **Time to Rank**: Days from generation to top 10 position

## Next Steps

- [x] Migration complete
- [x] Service layer built
- [x] Widget active
- [ ] Run initial scan
- [ ] Generate quick wins
- [ ] Monitor performance
- [ ] Schedule automated scans
- [ ] Customize parameters based on results

For detailed documentation, see `SEO_REVENUE_OPPORTUNITY_ENGINE.md`
