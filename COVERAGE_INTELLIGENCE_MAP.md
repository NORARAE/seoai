# Coverage Intelligence Map

## Overview

The Coverage Intelligence Map is a sophisticated feature that visualizes service × location coverage, identifies gaps, and provides data-driven page generation recommendations. It helps prioritize content expansion by scoring opportunities based on traffic potential, competition analysis, and strategic value.

## Architecture

### Database Layer

**ServiceLocation Model** (`service_locations` table)
- Tracks every service × city combination
- Stores `page_exists` flag and `location_page_id` reference
- Calculates and caches priority metrics:
  - `traffic_potential` (0-100): Predicted traffic value
  - `priority_score` (0-100): Composite expansion priority
  - `estimated_monthly_searches`: Projected search volume
- Maintains 30-day performance averages for existing pages:
  - `avg_impressions_30d`, `avg_clicks_30d`, `avg_ctr_30d`, `avg_position_30d`
- Status tracking: `pending`, `generated`, `active`, `low_traffic`, `no_demand`

### Service Layer

**CoverageMatrixService**
- `buildMatrix(State $state, ?Service $service)`: Populates/refreshes coverage matrix
- `calculateTrafficPotential(City $city)`: Scores based on population, geography, existing coverage
- `calculatePriorityScore()`: Composite scoring for missing vs. existing pages
- `getTopOpportunities()`: Returns highest-priority gaps
- `getCoverageStats()`: Aggregates coverage metrics

**LocationPageGeneratorService**
- `generateFromOpportunity(ServiceLocation $sl, Site $site)`: Creates single page
- `generateBatch(Site $site, int $count)`: Bulk generation for top opportunities
- `generateForService()`: Generate all pages for a specific service
- `generateForCounty()`: Generate all pages in a county
- Integrates with:
  - LocationPageComposer (content generation)
  - InternalLinkPlannerService (link strategy)
  - LocationPageValidationService (quality checks)
  - LocationPageRenderService (HTML rendering)

### UI Layer

**CoverageMap Filament Page** (`/admin/coverage-map`)
- Stats dashboard: Total combinations, pages created, missing pages, high-priority gaps
- Coverage percentage calculation
- State/service filters with "show all" toggle
- Color-coded indicators: 
  - 🟢 Green: Active page with good traffic
  - 🟡 Yellow: Page exists but low traffic
  - 🔴 Red: Missing page (opportunity)
  - ⚪ Gray: Pending analysis
- Interactive table with generate/view actions
- Batch generation buttons
- Auto-refresh every 60 seconds

**ExpansionOpportunitiesWidget** (Dashboard)
- Shows top 20 missing pages by priority score
- One-click page generation
- Detailed opportunity modal with:
  - Priority/traffic metrics
  - Location demographics
  - Rationale for recommendation
- Auto-refresh every 2 minutes

## Scoring Algorithm

### Traffic Potential (0-100)
Calculated for **missing pages** only:

1. **Population Score** (max 50 points):
   - ≥100k: 50 points
   - ≥50k: 40 points
   - ≥25k: 30 points
   - ≥10k: 20 points
   - <10k: 10 points

2. **County Seat/Major City Bonus** (max 20 points):
   - Placeholder: 10 points (future: check actual data)

3. **Geographic Diversity** (max 30 points):
   - First page in county: 30 points
   - <3 pages in county: 15 points
   - ≥3 pages in county: 5 points

### Priority Score (0-100)

**For Missing Pages:**
- 60% from traffic_potential
- 20% from competition analysis (evidence of nearby demand)
- 20% from strategic value (gap filling)

**For Existing Pages:**
Based on actual performance:
- 50% from impression volume (1000+ = 50pts, 500+ = 40pts, 100+ = 30pts)
- 50% from CTR (≥5% = 50pts, ≥3% = 35pts, ≥2% = 20pts)

## Usage Workflows

### Initial Setup

1. **Build Coverage Matrix**
   ```bash
   php artisan tinker
   $state = State::where('code', 'NY')->first();
   app(CoverageMatrixService::class)->buildMatrix($state);
   ```

2. **Review in Admin Panel**
   - Navigate to `/admin/coverage-map`
   - View stats and identify high-priority gaps
   - Filter by service or county

### Generating Pages

**Single Page Generation:**
1. Open Coverage Map
2. Find opportunity in table
3. Click "Generate Page" action
4. Confirm generation
5. View created page immediately

**Batch Generation:**
1. Click "Generate Top 10" button
2. System creates 10 highest-priority pages
3. Matrix auto-refreshes to show new coverage

**Programmatic Generation:**
```php
$site = Site::where('is_active', true)->first();
$generator = app(LocationPageGeneratorService::class);

// Generate top 20 opportunities (priority ≥70)
$result = $generator->generateBatch($site, 20, 70);

// Generate all pages for a service
$service = Service::find(1);
$result = $generator->generateForService($site, $service, 60);

// Generate all pages in a county
$result = $generator->generateForCounty($site, $countyId, 60);
```

### Monitoring Performance

**From Coverage Map:**
- Toggle "Show all" to see existing pages
- Check avg_impressions_30d and avg_clicks_30d columns
- Identify low-traffic pages (yellow status)

**From Opportunities Widget:**
- Dashboard widget shows top 20 gaps automatically
- Review priority scores and traffic potential
- Click "Details" to see full opportunity analysis

### Refreshing Matrix

**Manual Refresh:**
- Click "Refresh Matrix" button in Coverage Map
- Analyzes all service × location combinations
- Updates performance metrics from GSC data
- Recalculates priority scores

**Automated Refresh:**
- Add to scheduler in `app/Console/Kernel.php`:
  ```php
  $schedule->call(function () {
      $states = State::all();
      foreach ($states as $state) {
          app(CoverageMatrixService::class)->buildMatrix($state);
      }
  })->weekly();
  ```

## Status Lifecycle

1. **pending**: ServiceLocation created, analysis needed
2. **generated**: Page just created, no performance data yet
3. **active**: Page has ≥100 impressions/month (good traffic)
4. **low_traffic**: Page has <100 impressions/month
5. **no_demand**: Page has <50 impressions/month (possible removal candidate)

## Integration Points

### GSC Sync
- `GscSyncService` populates `performance_metrics` table
- `CoverageMatrixService` pulls 30-day averages
- Updates `avg_impressions_30d`, etc. on ServiceLocation records

### Internal Linking
- `InternalLinkPlannerService` suggests related pages
- Links populated in `internal_links_json` field
- Improves SEO and user navigation

### Validation
- `LocationPageValidationService` checks quality
- `validation_passed` and `content_score` tracked
- Ensures generated pages meet standards

### Baseline Tracking
- `BaselineSnapshot` created on page generation
- Tracks performance from day 0
- Enables before/after analysis

## Best Practices

1. **Start with High-Priority States**: Build matrix for states with active sites first
2. **Set Minimum Thresholds**: Use minPriorityScore ≥60 to avoid low-value pages
3. **Monitor Low-Traffic Pages**: Review yellow-status pages monthly, consider improvements or removal
4. **Batch Generation**: Generate 10-20 pages at a time to avoid overwhelming content queue
5. **Regular Refresh**: Schedule weekly matrix refresh to capture new performance data
6. **Review Details**: Use opportunity details modal to understand *why* a location is recommended

## Filament Navigation

The Coverage Intelligence Map appears in the admin panel:
- **Navigation Group**: Intelligence
- **Sort Order**: 3
- **Icon**: Map icon
- **Label**: Coverage Map

ExpansionOpportunitiesWidget appears on the dashboard automatically (sort: 4).

## Future Enhancements

- [ ] Visual grid/heatmap view (current: table view)
- [ ] County seat detection for better scoring
- [ ] Competition analysis integration
- [ ] Keyword research API for search volume
- [ ] Historical trend charts
- [ ] A/B testing for generated page variations
- [ ] Auto-generation scheduling (e.g., "generate 5 pages daily")
- [ ] Content refresh recommendations for low-traffic pages

## Files Created

### Database
- `database/migrations/2026_03_15_100000_create_service_locations_table.php`

### Models
- `app/Models/ServiceLocation.php`

### Services
- `app/Services/CoverageMatrixService.php`
- `app/Services/LocationPageGeneratorService.php`

### Filament
- `app/Filament/Pages/CoverageMap.php`
- `app/Filament/Widgets/ExpansionOpportunitiesWidget.php`

### Views
- `resources/views/filament/pages/coverage-map.blade.php`
- `resources/views/filament/widgets/opportunity-details.blade.php`

## Summary

The Coverage Intelligence Map transforms location page generation from reactive to proactive. Instead of manually deciding which pages to create, the system:

1. **Analyzes** all possible service × location combinations
2. **Scores** opportunities using data-driven metrics
3. **Visualizes** coverage gaps with intuitive color coding
4. **Recommends** highest-value expansion targets
5. **Generates** pages with one-click simplicity
6. **Tracks** performance from day 0

This strategic approach maximizes SEO impact and ensures content development resources are focused on the most valuable opportunities.
