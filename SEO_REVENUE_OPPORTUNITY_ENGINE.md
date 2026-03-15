# SEO Revenue Opportunity Engine

## Overview

The SEO Revenue Opportunity Engine is a sophisticated system that identifies, scores, and prioritizes SEO opportunities based on revenue potential. It analyzes service × location combinations using GSC data, competition analysis, and projected conversion rates to recommend the highest-value pages to create.

## Architecture

### Database Layer

**SeoOpportunity Model** (`seo_opportunities` table)
- Tracks revenue-generating opportunities for service × location combinations
- Stores comprehensive SEO metrics:
  - `search_volume`: Estimated monthly searches
  - `competition_score`: Competition level (0-100)
  - `rank_potential`: Ranking potential (0-100)
  - `priority_score`: Overall opportunity priority (0-100)
- Calculates revenue projections:
  - `estimated_monthly_revenue`: Projected monthly income
  - `service_value`: Average service value per conversion
  - `conversion_rate`: Expected conversion rate (default 2%)
- Tracks current performance:
  - `current_position`, `current_impressions`, `current_clicks`, `current_ctr`
- Classifies opportunities by type:
  - `new_page`: No page exists - pure opportunity
  - `underperforming`: Page exists but ranking poorly
  - `high_volume`: High search volume opportunity
  - `quick_win`: Low competition + high potential
  - `content_gap`: Competitor coverage but we're missing
- Workflow status: `pending` → `approved` → `in_progress` → `completed` → `monitoring`

### Service Layer

**RevenueOpportunityService**

Core methods:
- `generateOpportunities(Site $site, array $options)`: Scans all service × location combinations and creates/updates opportunities
- `calculateOpportunityMetrics()`: Analyzes metrics for a specific combination
- `getTopOpportunities(Site $site, int $limit)`: Returns highest-priority opportunities
- `getQuickWins(Site $site, int $limit)`: Returns low-competition, high-potential opportunities
- `getHighRevenueOpportunities(Site $site, float $minRevenue, int $limit)`: Returns opportunities by revenue threshold
- `refreshOpportunities(Site $site)`: Re-analyzes existing opportunities with latest data

**Scoring Algorithm:**

1. **Search Volume Estimation** (population-based heuristic):
   - Cities ≥100k: 0.5% search rate
   - Cities ≥50k: 0.3% search rate
   - Cities ≥25k: 0.2% search rate
   - Cities <25k: 0.1% search rate

2. **Competition Score** (0-100):
   - Base: 50
   - County competition: +0-20 based on existing pages
   - High-performing nearby pages: +15
   - Population factor: +0-15

3. **Rank Potential** (0-100):
   - Existing pages: Based on current position (≤3 = 95, ≤5 = 85, ≤10 = 75)
   - New pages: Based on competition (low = 85, medium = 70, high = 55)
   - Geographic diversity bonus: +10 for first in county

4. **Priority Score** (0-100):
   - Search volume impact: 30%
   - Rank potential: 40%
   - Competition (inverse): 20%
   - Opportunity type bonus: 10%
   - Poor performance boost: +5 if position >20

5. **Revenue Calculation**:
   ```
   EstimatedRevenue = SearchVolume × EstimatedCTR × ConversionRate × ServiceValue
   ```
   
   CTR estimates by position:
   - Position 1: 31.8%
   - Position 2: 15.8%
   - Position 3: 11.0%
   - Position 4-10: 8.2% - 2.8%

### UI Layer

**TopRevenueOpportunitiesWidget** (Dashboard)
- Displays top 20 opportunities by priority score
- Shows:
  - Priority score with color-coded badge
  - Service and location
  - Opportunity type (quick win, high volume, etc.)
  - Search volume
  - Estimated monthly revenue
  - Rank potential
  - Competition level
  - Current position (if page exists)
- Actions:
  - **Generate Page**: One-click page generation
  - **View Page**: View existing page (if created)
  - **Approve**: Approve opportunity for action
  - **Dismiss**: Remove from recommendations
  - **Details**: View comprehensive opportunity analysis
- Empty state action: "Scan for Opportunities"
- Auto-refresh every 3 minutes

## Usage Workflows

### Initial Setup & Scanning

**Generate Opportunities:**
```php
use App\Services\RevenueOpportunityService;
use App\Models\Site;

$site = Site::where('is_active', true)->first();
$revenueService = app(RevenueOpportunityService::class);

$result = $revenueService->generateOpportunities($site, [
    'min_priority_score' => 60,      // Only opportunities ≥60
    'min_search_volume' => 20,       // Minimum 20 searches/month
    'service_value' => 500.00,       // $500 per conversion
    'conversion_rate' => 0.02,       // 2% conversion rate
    'limit' => null,                 // No limit
]);

// Result: ['created' => X, 'updated' => Y, 'skipped' => Z]
```

**From Admin Panel:**
1. Navigate to dashboard
2. See "Top Revenue Opportunities" widget
3. If empty, click "Scan for Opportunities"
4. System analyzes all service × location combinations
5. Opportunities appear ranked by priority score

### Reviewing Opportunities

**Dashboard Widget:**
- Top 20 opportunities displayed automatically
- Sort by priority score, revenue, search volume, etc.
- Filter by status, opportunity type
- View total potential monthly revenue in heading

**Details Modal:**
- Click "Details" action on any opportunity
- See comprehensive analysis:
  - Revenue potential breakdown
  - SEO metrics (volume, rank potential, competition)
  - Opportunity classification & rationale
  - Current performance (if page exists)
  - Revenue calculation formula
  - Why this opportunity is recommended

### Generating Pages

**Single Page Generation:**
1. Find opportunity in widget table
2. Click "Generate Page" action
3. Review confirmation modal with metrics
4. Confirm generation
5. Page created automatically with:
   - Optimized content from LocationPageComposer
   - Internal links from InternalLinkPlannerService
   - Quality validation
   - Baseline snapshot for tracking
6. Opportunity status → `completed`
7. Notification with link to view page

**Approve & Generate Later:**
1. Click "Approve" action
2. Opportunity marked for future generation
3. Use dashboard or bulk tools to generate approved opportunities

**Programmatic Generation:**
```php
use App\Models\SeoOpportunity;
use App\Services\LocationPageGeneratorService;

$opportunity = SeoOpportunity::find(1);
$site = Site::find($opportunity->site_id);
$generator = app(LocationPageGeneratorService::class);

// Create ServiceLocation mapping
$serviceLocation = \App\Models\ServiceLocation::firstOrCreate([
    'service_id' => $opportunity->service_id,
    'city_id' => $opportunity->location_id,
], [
    'state_id' => $opportunity->location->state_id,
    'county_id' => $opportunity->location->county_id,
]);

$result = $generator->generateFromOpportunity($serviceLocation, $site);

if ($result['success']) {
    $opportunity->markAsCompleted(
        \App\Models\LocationPage::find($result['location_page_id'])
    );
}
```

### Opportunity Management

**Dismiss Opportunity:**
- Click "Dismiss" action
- Opportunity status → `dismissed`
- Won't appear in future scans
- Use for low-value or irrelevant opportunities

**Refresh Opportunities:**
```php
// Re-analyze existing opportunities with latest GSC data
$result = $revenueService->refreshOpportunities($site, [
    'min_priority_score' => 60,
]);

// Result includes:
// - 'refreshed': Updated existing opportunities
// - 'created': New opportunities found
// - 'updated': Existing opportunities rescored
```

**Update Single Opportunity:**
```php
// Fetch latest performance metrics
$revenueService->updateOpportunityPerformance($opportunity);

// Automatically moves to 'monitoring' if performing well
// (impressions ≥100 AND position ≤10)
```

### Querying Opportunities

**Get Quick Wins:**
```php
$quickWins = $revenueService->getQuickWins($site, limit: 10);
// Returns top 10 low-competition, high-potential opportunities
```

**Get High Revenue Opportunities:**
```php
$highRevenue = $revenueService->getHighRevenueOpportunities(
    $site, 
    minRevenue: 200.00, 
    limit: 20
);
// Returns opportunities with ≥$200/mo potential
```

**Get by Type:**
```php
$newPages = $revenueService->getOpportunitiesByType($site, 'new_page');
$underperforming = $revenueService->getOpportunitiesByType($site, 'underperforming');
$contentGaps = $revenueService->getOpportunitiesByType($site, 'content_gap');
```

**Using Query Scopes:**
```php
// High priority pending opportunities
SeoOpportunity::where('site_id', $site->id)
    ->pending()
    ->highPriority(80)
    ->get();

// New page opportunities with high revenue
SeoOpportunity::where('site_id', $site->id)
    ->newPages()
    ->highRevenue(150.00)
    ->get();

// Quick wins
SeoOpportunity::where('site_id', $site->id)
    ->quickWins()
    ->get();
```

## Integration with Coverage Intelligence Map

The Revenue Opportunity Engine complements the Coverage Intelligence Map:

**Coverage Map** focuses on:
- Visual grid of service × location coverage
- Gap detection and fill-in strategies
- Geographic completeness

**Revenue Engine** focuses on:
- ROI-driven page prioritization
- Revenue projections
- Competitive analysis
- Performance optimization

**Use Together:**
1. Use Coverage Map to see overall coverage percentage
2. Use Revenue Engine to prioritize which gaps to fill first
3. Coverage Map shows "what's missing"
4. Revenue Engine shows "what's most valuable"

## Opportunity Lifecycle

```
┌──────────┐
│ Pending  │ ← Newly discovered opportunity
└────┬─────┘
     │
     ├─→ Approved ──→ In Progress ──→ Completed ──→ Monitoring
     │                                                    │
     └─→ Dismissed                                       │
                                                          │
                                            (Performance tracking continues)
```

1. **Pending**: Discovered by scan, awaiting review
2. **Approved**: Manually approved for page generation
3. **In Progress**: Page generation actively happening
4. **Completed**: Page successfully generated
5. **Monitoring**: Page performing well (≥100 impressions/mo, ≤10 position)
6. **Dismissed**: Not worth pursuing

## Customization

### Adjust Scoring Parameters

**In Service:**
```php
// Change default service value
protected float $defaultServiceValue = 750.00; // Increase to $750

// Change default conversion rate
protected float $defaultConversionRate = 0.03; // Increase to 3%

// Modify search rate heuristics in estimateSearchVolume()
$searchRate = match(true) {
    $city->population >= 100000 => 0.008, // 0.8% for large cities
    // ...
};
```

**Per Scan:**
```php
$revenueService->generateOpportunities($site, [
    'min_priority_score' => 70,      // Higher threshold
    'min_search_volume' => 50,       // Only ≥50 searches
    'service_value' => 1000.00,      // $1000 per conversion
    'conversion_rate' => 0.025,      // 2.5% conversion
]);
```

### Add Industry-Specific Logic

**Example: HVAC services**
```php
// In RevenueOpportunityService::estimateSearchVolume()
if (str_contains(strtolower($service->name), 'hvac')) {
    // HVAC services have higher search rates
    $estimatedVolume *= 1.5;
}
```

## Automation

**Scheduled Scanning:**
```php
// In routes/console.php or app/Console/Kernel.php
Schedule::call(function () {
    $sites = Site::where('is_active', true)->get();
    $revenueService = app(RevenueOpportunityService::class);
    
    foreach ($sites as $site) {
        $revenueService->refreshOpportunities($site, [
            'min_priority_score' => 60,
        ]);
    }
})->weekly(); // Every Sunday at midnight
```

**Auto-Approve Quick Wins:**
```php
Schedule::call(function () {
    SeoOpportunity::quickWins()
        ->where('priority_score', '>=', 85)
        ->where('status', 'pending')
        ->update(['status' => 'approved']);
})->daily();
```

## Best Practices

1. **Start with Quick Wins**: Generate low-competition opportunities first for fast results
2. **Set Realistic Service Values**: Use actual conversion data when available
3. **Regular Refresh**: Schedule weekly opportunity refresh to capture new GSC data
4. **Monitor Performance**: Track generated pages and update opportunity status
5. **Dismiss Low-Value**: Don't clutter dashboard with opportunities you won't pursue
6. **A/B Test Parameters**: Experiment with conversion rates and scoring thresholds
7. **Combine with Coverage Map**: Use both tools for comprehensive strategy
8. **Review Revenue Projections**: Regularly validate actual revenue vs. projections

## Performance Considerations

- Opportunities are indexed on `site_id + priority_score` and `status + priority_score`
- Unique constraint on `site_id + service_id + location_id` prevents duplicates
- Widget limits to 20 results to avoid overwhelming dashboard
- Auto-refresh at 3-minute intervals (can be adjusted)

## Files Created

### Database
- `database/migrations/2026_03_15_200000_create_seo_opportunities_table.php`

### Models
- `app/Models/SeoOpportunity.php`

### Services
- `app/Services/RevenueOpportunityService.php`

### Filament Widgets
- `app/Filament/Widgets/TopRevenueOpportunitiesWidget.php`

### Views
- `resources/views/filament/widgets/revenue-opportunity-details.blade.php`

## Example Results

After scanning a site with 5 active services and 100 cities:

```
Scan Complete
Found 87 new opportunities and updated 13 existing ones.

Top Opportunity:
- Service: Emergency Plumbing
- Location: Buffalo, NY
- Type: Quick Win
- Priority: 92/100
- Search Volume: 450/month
- Rank Potential: 85%
- Competition: 42/100 (Low)
- Est. Monthly Revenue: $1,275

Total Pipeline: $47,650/month potential revenue
```

## Summary

The SEO Revenue Opportunity Engine transforms SEO content strategy from guesswork to data-driven decision making. By analyzing search volume, competition, ranking potential, and revenue projections, it identifies and prioritizes the highest-ROI pages to create. Combined with one-click generation and comprehensive tracking, it streamlines the entire opportunity-to-page workflow.

**Key Benefits:**
- 📊 **Data-Driven**: Scores based on actual metrics, not hunches
- 💰 **Revenue-Focused**: Prioritizes highest-value opportunities
- ⚡ **Quick Wins**: Identifies low-hanging fruit for fast results
- 🎯 **Precision Targeting**: Recommends exactly what to build next
- 🔄 **Continuous**: Regular refresh keeps opportunities current
- 📈 **Performance Tracking**: Monitors actual vs. projected results
