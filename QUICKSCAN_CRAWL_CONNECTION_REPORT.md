# QuickScan → Full Crawl Connection Report

**Status: COMPLETE — All 8 phases implemented and verified**

---

## CONNECTION STATUS

QuickScan and the Site/ScanRun multi-page crawl system are now connected. Every successful QuickScan run will:

1. Find or create a `Site` record for the scanned domain
2. Link the `QuickScan` record to that `Site` via `quick_scans.site_id`
3. Seed conservative `SiteCrawlSetting` (max 250 pages, depth 3) if none exists
4. Associate the scanning user to the site via the `site_user` pivot table
5. Dispatch `StartSiteDiscoveryJob` → which creates a `ScanRun` linked back to the `QuickScan` and runs the full crawl pipeline

The QuickScan product is **unchanged** — it still scans, scores, saves to CRM, and sends emails. The crawl is a non-fatal follow-on step. If crawl dispatch fails for any reason, the paid QuickScan result is preserved.

---

## NEW MIGRATIONS

| Migration                                                | Table         | Change                                                      |
| -------------------------------------------------------- | ------------- | ----------------------------------------------------------- |
| `2026_04_23_000001_add_site_id_to_quick_scans_table`     | `quick_scans` | Nullable FK `site_id → sites.id` (nullOnDelete)             |
| `2026_04_23_000002_add_quick_scan_id_to_scan_runs_table` | `scan_runs`   | Nullable FK `quick_scan_id → quick_scans.id` (nullOnDelete) |

Both migrations have been run (`DONE`).

---

## FILES MODIFIED

### `app/Models/QuickScan.php`

- Added `'site_id'` to `$fillable`
- Added `site(): BelongsTo` relationship → `Site::class`
- Added `scanRun(): HasOne` relationship via `latestOfMany('started_at')`

### `app/Models/ScanRun.php`

- Added `'quick_scan_id'` to `$fillable`
- Added `quickScan(): BelongsTo` relationship → `QuickScan::class`

### `app/Models/Site.php`

- Added `scanRuns(): HasMany` relationship
- Added `latestScanRun(): HasOne` via `latestOfMany()`
- Added `quickScans(): HasMany` relationship (via new `site_id` FK)
- Added `crawlStats(): ?array` method (Phase 7 — see below)

### `app/Jobs/StartSiteDiscoveryJob.php`

- Added `public ?int $quickScanId = null` to constructor signature
- Passes `'quick_scan_id' => $this->quickScanId` into `ScanRun::create()`
- No other behaviour changed

### `app/Jobs/RunQuickScanJob.php`

- Added imports: `Site`, `SiteCrawlSetting`, `DB`
- After scan succeeds: calls `$this->triggerSiteCrawl($scan)` (non-fatal, wrapped in try/catch)
- Added private method `triggerSiteCrawl(QuickScan $scan): void`:
    - `Site::firstOrCreate(['domain' => $domain], [name, status='active', crawl_status='idle', sitemap_enabled=false])`
    - `$scan->update(['site_id' => $site->id])` (idempotent)
    - `DB::table('site_user')->insertOrIgnore([...])` if user_id present
    - `SiteCrawlSetting::firstOrCreate(['site_id' => $site->id], [max_pages=250, crawl_delay=1, max_depth=3, obey_robots=true, follow_nofollow=false])`
    - `StartSiteDiscoveryJob::dispatch(..., quickScanId: $scan->id)->onQueue('crawl')`

---

## JOB FLOW CONFIRMED

```
RunQuickScanJob::handle()
  └─ QuickScanService::scan()          # homepage-only scan (unchanged)
  └─ $scan->update([status=scanned])  # QuickScan record saved (unchanged)
  └─ CRM + email dispatch              # (unchanged)
  └─ triggerSiteCrawl($scan)          # NEW — non-fatal
       └─ Site::firstOrCreate()
       └─ SiteCrawlSetting::firstOrCreate()
       └─ StartSiteDiscoveryJob::dispatch() → queue: crawl
            └─ ScanRun::create([quick_scan_id => $scan->id])
            └─ RobotsPolicyService::refreshPolicy()
            └─ SitemapIngestionService::ingest()   # skipped: sitemap_enabled=false on new sites
            └─ CrawlQueueService::enqueueUrl(homepage)
            └─ DispatchCrawlQueueJob::dispatch()   → queue: crawl
                 └─ ProcessCrawlQueueItemJob per URL → queue: crawl
                      └─ CrawlQueueService::processQueueItem()
                           └─ PageExtractionService::extract()
                           └─ PageMetadata::updateOrCreate()
                           └─ PageContent::updateOrCreate()
                           └─ page_snapshots insert
                           └─ storeDiscoveredLinks() → enqueueUrl() (recursive, up to max_pages)
```

All jobs in the pipeline were confirmed real and functional in the prior audit. No stub jobs exist in this path.

---

## DATA VALIDATION

After a crawl completes, real data lands in:

| Table            | What's stored                                                           |
| ---------------- | ----------------------------------------------------------------------- |
| `sites`          | One record per domain, `crawl_status` updated by StartSiteDiscoveryJob  |
| `scan_runs`      | One record per crawl run linked to the triggering `quick_scan_id`       |
| `url_inventory`  | One row per discovered URL with `status`, `indexability_status`         |
| `page_metadata`  | `title`, `meta_description`, `h1`, `schema` (JSON), `canonical` per URL |
| `page_content`   | Full extracted text content per URL                                     |
| `page_snapshots` | Raw HTML snapshots per URL                                              |
| `internal_links` | All `<a href>` internal links discovered during crawl                   |

---

## PHASE 7 — BACKEND STATS METHOD

`Site::crawlStats(): ?array` queries live data (not denormalized counts). Returns `null` if no crawl has run for the site.

Fields returned:

- `pages_discovered` — all `url_inventory` rows for the site
- `pages_crawled` — inventory rows with `status = 'completed'`
- `indexable_pages` — completed rows not in `blocked|non_200|noindex`
- `pages_with_schema` — joined to `page_metadata`, schema not null/empty
- `pages_missing_title` — joined to `page_metadata`, title null or empty
- `pages_missing_meta_description` — joined to `page_metadata`, meta_description null or empty
- `total_internal_links` — count of `InternalLink` rows for site
- `last_crawled_at` — max `last_crawled_at` from `url_inventory`
- `crawl_status` — from `sites.crawl_status`

Usage:

```php
$site = $quickScan->site;
$stats = $site?->crawlStats(); // null until crawl runs
```

---

## RISKS & OPERATIONAL NOTES

### 1. The `crawl` queue worker must be running

The entire pipeline dispatches to `->onQueue('crawl')`. If no worker processes this queue, jobs silently back up. Ensure your supervisor config has:

```ini
[program:laravel-crawl-worker]
command=php artisan queue:work --queue=crawl --tries=3 --timeout=180
```

### 2. Conservative defaults apply only to NEW sites

`SiteCrawlSetting::firstOrCreate` only sets `max_pages=250, max_depth=3` for sites created for the first time via QuickScan. Existing sites operated by admins keep their current (potentially higher) settings.

### 3. Duplicate crawl guard is in StartSiteDiscoveryJob

If a user QuickScans the same domain twice within a short window, the second `StartSiteDiscoveryJob` will detect a `running` or `pending` `ScanRun` for that site and abort gracefully (it logs a warning and returns). No duplicate crawls are created.

### 4. Sitemap discovery is disabled for QuickScan-created sites

New `Site` records created by this flow have `sitemap_enabled = false`. Crawl seeds from the homepage only. An admin can enable sitemap ingestion in the Filament admin panel once the site is confirmed active.

### 5. Crawl runtime scales with site size

With `max_pages=250` and `crawl_delay=1s`, a full crawl takes up to ~4 minutes. The `ScanRun` will be in `running` status for this duration. A 250-page site at 1s delay = ~4 min minimum crawl time. `ProcessCrawlQueueItemJob` has a 30s per-URL timeout.

### 6. QuickScan failure does not trigger crawl

`triggerSiteCrawl()` is only called when `$result['score'] !== null && empty($result['error'])`. Failed or errored QuickScans do not create Site records or dispatch crawl jobs.

### 7. No UI changes made

All changes are backend only. Data is now accumulating in the crawl tables. UI wiring to display `crawlStats()` results is the natural next step when ready.

---

## WHAT'S NOT CHANGED

- `QuickScanService` — homepage scan logic untouched
- QuickScan pricing, checkout, stripe webhook flow — untouched
- Filament admin panels for Sites, ScanRuns — untouched
- The crawl pipeline itself — no changes to `StartSiteDiscoveryJob`, `DispatchCrawlQueueJob`, `ProcessCrawlQueueItemJob`, or `CrawlQueueService` logic (only the constructor signature of `StartSiteDiscoveryJob` was extended with an optional param)
- All existing routes, controllers, views — untouched
