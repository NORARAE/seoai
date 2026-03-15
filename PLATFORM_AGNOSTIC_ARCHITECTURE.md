# Platform-Agnostic SEO Intelligence Architecture

## 🎯 Core Philosophy

**SEOAIco is an SEO intelligence and expansion engine, not a WordPress plugin.**

The platform analyzes any website, identifies opportunities, generates optimized content assets, and adapts publishing to the target CMS—or exports for manual implementation.

---

## 🏗️ Five-Layer Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    1. INGESTION LAYER                        │
│  Sitemap parsing • URL discovery • Structure inference       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                   2. INTELLIGENCE LAYER                      │
│  Coverage gaps • Revenue opportunities • Optimization recs   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                 3. ASSET GENERATION LAYER                    │
│  PagePayload creation • Content • Schema • Link suggestions  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                   4. PUBLISHING LAYER                        │
│  CMS adapters • Native publish • Export packages             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                   5. MEASUREMENT LAYER                       │
│  GSC tracking • Performance metrics • ROI attribution        │
└─────────────────────────────────────────────────────────────┘
```

---

## 1️⃣ INGESTION LAYER

### Purpose
Analyze any website without requiring CMS authentication.

### Services

#### `SitemapScannerService`
**Responsibilities:**
- Fetch and parse sitemap.xml (and sitemap indexes)
- Extract all URLs with metadata (lastmod, priority, changefreq)
- Store discovered URLs in `discovered_pages` table
- Identify URL patterns

**Methods:**
```php
public function scanSitemap(Site $site): array
public function detectSitemapUrl(string $domain): ?string
public function parseSitemapIndex(string $url): Collection
public function extractUrlPatterns(Collection $urls): array
```

#### `SiteStructureAnalyzerService`
**Responsibilities:**
- Analyze URL patterns (e.g., `/services/{service}/{city}`)
- Infer taxonomy from URL structure
- Detect parent-child relationships
- Identify hub pages vs leaf pages
- Map navigation hierarchy

**Methods:**
```php
public function analyzeUrlStructure(Site $site): array
public function inferServiceTaxonomy(Collection $urls): Collection
public function inferLocationTaxonomy(Collection $urls): Collection
public function detectHubPages(Collection $urls): Collection
public function buildHierarchyMap(Collection $urls): array
```

#### `CoverageInferenceService`
**Responsibilities:**
- Compare discovered pages vs expected coverage
- Identify missing service-location combinations
- Suggest gaps in coverage
- Detect incomplete hierarchies

**Methods:**
```php
public function detectCoverageGaps(Site $site): Collection
public function compareExpectedVsActual(Site $site): array
public function suggestMissingPages(Site $site): Collection
```

### Database Tables

#### `discovered_pages`
```php
id
site_id
url
discovered_via (sitemap|crawl|inference)
url_pattern
inferred_service
inferred_location
page_type (hub|leaf|other)
parent_url
last_seen_at
http_status
meta_title
meta_description
indexed_in_gsc
timestamps
```

#### `url_patterns`
```php
id
site_id
pattern
example_urls (json)
service_segment
location_segment
page_type
confidence_score
timestamps
```

---

## 2️⃣ INTELLIGENCE LAYER

### Expanded Capabilities

The intelligence layer now produces **strategic recommendations**, not just opportunities:

#### Existing Engines (Keep & Enhance)
- ✅ Coverage Intelligence Map
- ✅ Revenue Opportunity Engine
- ✅ Title Optimization Engine

#### New Intelligence Outputs

##### `SitemapRecommendationService`
**Produces:**
- Recommended sitemap hierarchy
- Missing sitemap nodes
- Priority scores for URLs
- Update frequency recommendations

##### `NavigationRecommendationService`
**Produces:**
- Submenu structure suggestions
- Parent-child page relationships
- Hub page recommendations
- Breadcrumb path suggestions

##### `InternalLinkingStrategyService`
**Produces:**
- Link graph recommendations
- Anchor text suggestions
- Priority pages to link from/to
- Link velocity recommendations

##### `ContentExpansionPlannerService`
**Produces:**
- Service-location matrix with gaps
- Priority ranking for missing pages
- Content cluster recommendations
- Topical authority maps

### Intelligence Output Format

All intelligence services return structured recommendations:

```php
[
    'type' => 'missing_page|link_suggestion|sitemap_node|navigation_item',
    'priority_score' => 85,
    'confidence_score' => 92,
    'entity_type' => 'service_location_page',
    'entity_data' => [...],
    'recommendation' => [...],
    'reasoning' => 'High search volume, weak competition, nearby pages exist',
    'estimated_impact' => [
        'monthly_impressions' => 500,
        'monthly_clicks' => 50,
        'monthly_revenue' => 500,
    ],
]
```

---

## 3️⃣ ASSET GENERATION LAYER

### Core Concept: PagePayload

**A PagePayload is the normalized output of the generation engine.**

It contains everything needed to create a page, regardless of where it gets published.

### PagePayload Schema

#### Database: `page_payloads` table

```php
id
batch_id (FK to page_generation_batches)
site_id
client_id
service_id (nullable)
location_id (nullable - could be City, County, State)
location_type (city|county|state)

// Core content
title
meta_description
slug
canonical_url_suggestion
body_content (HTML)
excerpt (optional)

// SEO assets
schema_json_ld (JSON)
structured_data_type (LocalBusiness|Service|etc)
og_image_url
og_tags (JSON)

// Linking strategy
internal_link_suggestions (JSON array)
anchor_text_suggestions (JSON array)
outbound_links (JSON array)

// Hierarchy
parent_page_slug
hub_page_slug
related_pages (JSON array)
submenu_suggestions (JSON array)

// Sitemap metadata
sitemap_priority
sitemap_changefreq
sitemap_lastmod

// Publishing metadata
publish_notes (text)
publish_status (pending|published|exported|failed)
published_at
remote_id (WordPress post ID, Wix page ID, etc)
remote_url
remote_edit_url

// Quality scores
content_quality_score
seo_score
readability_score

// Generation metadata
generated_by (service|job class)
generation_params (JSON)
template_used
ai_model_used

status (draft|ready|published|archived)
timestamps
```

### PagePayload Model

**File:** `app/Models/PagePayload.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagePayload extends Model
{
    protected $fillable = [
        'batch_id', 'site_id', 'client_id', 'service_id', 'location_id',
        'location_type', 'title', 'meta_description', 'slug',
        'canonical_url_suggestion', 'body_content', 'excerpt',
        'schema_json_ld', 'structured_data_type', 'og_image_url', 'og_tags',
        'internal_link_suggestions', 'anchor_text_suggestions', 'outbound_links',
        'parent_page_slug', 'hub_page_slug', 'related_pages', 'submenu_suggestions',
        'sitemap_priority', 'sitemap_changefreq', 'sitemap_lastmod',
        'publish_notes', 'publish_status', 'published_at', 'remote_id',
        'remote_url', 'remote_edit_url', 'content_quality_score',
        'seo_score', 'readability_score', 'generated_by', 'generation_params',
        'template_used', 'ai_model_used', 'status',
    ];

    protected $casts = [
        'schema_json_ld' => 'array',
        'og_tags' => 'array',
        'internal_link_suggestions' => 'array',
        'anchor_text_suggestions' => 'array',
        'outbound_links' => 'array',
        'related_pages' => 'array',
        'submenu_suggestions' => 'array',
        'generation_params' => 'array',
        'published_at' => 'datetime',
        'content_quality_score' => 'decimal:2',
        'seo_score' => 'decimal:2',
        'readability_score' => 'decimal:2',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PageGenerationBatch::class, 'batch_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(City::class, 'location_id');
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(SeoOpportunity::class, 'opportunity_id');
    }

    /**
     * Export as structured array for external publishing
     */
    public function toExportFormat(): array
    {
        return [
            'meta' => [
                'generated_at' => $this->created_at->toIso8601String(),
                'payload_id' => $this->id,
            ],
            'content' => [
                'title' => $this->title,
                'meta_description' => $this->meta_description,
                'slug' => $this->slug,
                'body_html' => $this->body_content,
                'excerpt' => $this->excerpt,
            ],
            'seo' => [
                'schema_json_ld' => $this->schema_json_ld,
                'canonical_url' => $this->canonical_url_suggestion,
                'og_tags' => $this->og_tags,
            ],
            'hierarchy' => [
                'parent_page' => $this->parent_page_slug,
                'hub_page' => $this->hub_page_slug,
                'related_pages' => $this->related_pages,
            ],
            'internal_linking' => [
                'outgoing_links' => $this->internal_link_suggestions,
                'suggested_anchors' => $this->anchor_text_suggestions,
            ],
            'navigation' => [
                'submenu_suggestions' => $this->submenu_suggestions,
            ],
            'sitemap' => [
                'priority' => $this->sitemap_priority,
                'changefreq' => $this->sitemap_changefreq,
            ],
            'notes' => $this->publish_notes,
        ];
    }

    /**
     * Mark as published with remote details
     */
    public function markAsPublished(string $remoteId, string $remoteUrl, ?string $editUrl = null): void
    {
        $this->update([
            'publish_status' => 'published',
            'published_at' => now(),
            'remote_id' => $remoteId,
            'remote_url' => $remoteUrl,
            'remote_edit_url' => $editUrl,
            'status' => 'published',
        ]);
    }

    /**
     * Mark as exported (for non-native CMS)
     */
    public function markAsExported(): void
    {
        $this->update([
            'publish_status' => 'exported',
            'status' => 'published', // Exported counts as "done"
        ]);
    }
}
```

### Generation Services

#### `PagePayloadGeneratorService`
Refactored from `LocationPageGeneratorService`

**Responsibilities:**
- Generate complete PagePayload records
- No direct CMS interaction
- Output normalized, CMS-agnostic content
- Score content quality
- Suggest internal links
- Generate schema markup

**Methods:**
```php
public function generatePayload(
    Site $site,
    Service $service,
    City $city,
    array $options = []
): PagePayload

public function generateBulk(
    Site $site,
    Collection $opportunities,
    array $options = []
): Collection

public function regeneratePayload(PagePayload $payload): PagePayload
public function scorePayload(PagePayload $payload): void
```

---

## 4️⃣ PUBLISHING LAYER

### Site Model Updates

Add to `sites` table:

```php
cms_type (enum: wordpress|wix|squarespace|webflow|shopify|custom)
publishing_mode (enum: native|export_only|api|manual)
publishing_status (enum: connected|partial|manual|error)
wordpress_url (if WordPress)
wordpress_username (encrypted)
wordpress_app_password (encrypted)
api_endpoint (if custom API)
api_credentials (encrypted JSON)
```

### Publishing Adapter Interface

**File:** `app/Contracts/PublishingAdapterInterface.php`

```php
<?php

namespace App\Contracts;

use App\Models\PagePayload;
use App\Models\Site;

interface PublishingAdapterInterface
{
    /**
     * Validate connection to publishing destination
     */
    public function validateConnection(Site $site): bool;

    /**
     * Publish a page payload to the destination
     */
    public function publish(PagePayload $payload): PublishResult;

    /**
     * Update an already-published page
     */
    public function update(PagePayload $payload): PublishResult;

    /**
     * Delete a published page
     */
    public function delete(PagePayload $payload): bool;

    /**
     * Get the status of a published page
     */
    public function getStatus(PagePayload $payload): PublishStatus;

    /**
     * Export payload as structured file/format
     */
    public function export(PagePayload $payload, string $format = 'json'): string;

    /**
     * Check if this adapter supports batch publishing
     */
    public function supportsBatch(): bool;

    /**
     * Get adapter capabilities
     */
    public function getCapabilities(): array;
}
```

### PublishResult DTO

**File:** `app/DTOs/PublishResult.php`

```php
<?php

namespace App\DTOs;

class PublishResult
{
    public function __construct(
        public bool $success,
        public ?string $remoteId = null,
        public ?string $remoteUrl = null,
        public ?string $remoteEditUrl = null,
        public ?string $error = null,
        public ?array $metadata = null,
    ) {}

    public static function success(
        string $remoteId,
        string $remoteUrl,
        ?string $editUrl = null,
        ?array $metadata = null
    ): self {
        return new self(
            success: true,
            remoteId: $remoteId,
            remoteUrl: $remoteUrl,
            remoteEditUrl: $editUrl,
            metadata: $metadata
        );
    }

    public static function failure(string $error): self
    {
        return new self(success: false, error: $error);
    }
}
```

### WordPress Publishing Adapter

**File:** `app/Services/Publishing/WordPressPublishingAdapter.php`

```php
<?php

namespace App\Services\Publishing;

use App\Contracts\PublishingAdapterInterface;
use App\DTOs\PublishResult;
use App\Models\PagePayload;
use App\Models\Site;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WordPressPublishingAdapter implements PublishingAdapterInterface
{
    public function validateConnection(Site $site): bool
    {
        if (!$site->wordpress_url || !$site->wordpress_app_password) {
            return false;
        }

        try {
            $response = Http::withBasicAuth(
                $site->wordpress_username,
                decrypt($site->wordpress_app_password)
            )->get($site->wordpress_url . '/wp-json/wp/v2/users/me');

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WordPress connection validation failed', [
                'site_id' => $site->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function publish(PagePayload $payload): PublishResult
    {
        $site = $payload->site;

        if (!$this->validateConnection($site)) {
            return PublishResult::failure('WordPress connection not configured');
        }

        try {
            $response = Http::withBasicAuth(
                $site->wordpress_username,
                decrypt($site->wordpress_app_password)
            )->post($site->wordpress_url . '/wp-json/wp/v2/pages', [
                'title' => $payload->title,
                'content' => $payload->body_content,
                'slug' => $payload->slug,
                'status' => 'draft', // Start as draft
                'meta' => [
                    'seoaico_payload_id' => $payload->id,
                ],
                'meta_description' => $payload->meta_description,
                // Add Yoast/RankMath schema if plugins detected
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $remoteId = (string) $data['id'];
                $remoteUrl = $data['link'];
                $editUrl = $site->wordpress_url . '/wp-admin/post.php?post=' . $remoteId . '&action=edit';

                return PublishResult::success($remoteId, $remoteUrl, $editUrl, $data);
            }

            return PublishResult::failure('WordPress API error: ' . $response->body());
        } catch (\Exception $e) {
            return PublishResult::failure($e->getMessage());
        }
    }

    public function update(PagePayload $payload): PublishResult
    {
        if (!$payload->remote_id) {
            return $this->publish($payload);
        }

        $site = $payload->site;

        try {
            $response = Http::withBasicAuth(
                $site->wordpress_username,
                decrypt($site->wordpress_app_password)
            )->post($site->wordpress_url . '/wp-json/wp/v2/pages/' . $payload->remote_id, [
                'title' => $payload->title,
                'content' => $payload->body_content,
                'slug' => $payload->slug,
                'meta_description' => $payload->meta_description,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return PublishResult::success(
                    $payload->remote_id,
                    $data['link'],
                    $payload->remote_edit_url,
                    $data
                );
            }

            return PublishResult::failure('WordPress update failed: ' . $response->body());
        } catch (\Exception $e) {
            return PublishResult::failure($e->getMessage());
        }
    }

    public function delete(PagePayload $payload): bool
    {
        if (!$payload->remote_id) {
            return false;
        }

        $site = $payload->site;

        try {
            $response = Http::withBasicAuth(
                $site->wordpress_username,
                decrypt($site->wordpress_app_password)
            )->delete($site->wordpress_url . '/wp-json/wp/v2/pages/' . $payload->remote_id);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WordPress page deletion failed', [
                'payload_id' => $payload->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function getStatus(PagePayload $payload): string
    {
        // Query WordPress API for page status
        return 'unknown';
    }

    public function export(PagePayload $payload, string $format = 'json'): string
    {
        // Export as WordPress-compatible format
        return json_encode($payload->toExportFormat());
    }

    public function supportsBatch(): bool
    {
        return true; // WordPress REST API supports batch requests
    }

    public function getCapabilities(): array
    {
        return [
            'native_publish' => true,
            'draft_mode' => true,
            'schema_injection' => true,
            'featured_images' => true,
            'custom_fields' => true,
            'categories' => true,
            'tags' => true,
        ];
    }
}
```

### Export Publishing Adapter

**File:** `app/Services/Publishing/ExportPublishingAdapter.php`

```php
<?php

namespace App\Services\Publishing;

use App\Contracts\PublishingAdapterInterface;
use App\DTOs\PublishResult;
use App\Models\PagePayload;
use App\Models\Site;
use Illuminate\Support\Facades\Storage;

class ExportPublishingAdapter implements PublishingAdapterInterface
{
    public function validateConnection(Site $site): bool
    {
        // Export mode doesn't require connection
        return true;
    }

    public function publish(PagePayload $payload): PublishResult
    {
        // "Publishing" means marking as ready for export
        $exportPath = $this->generateExportFile($payload);

        return PublishResult::success(
            remoteId: 'export-' . $payload->id,
            remoteUrl: Storage::url($exportPath),
            metadata: ['export_path' => $exportPath]
        );
    }

    public function update(PagePayload $payload): PublishResult
    {
        return $this->publish($payload);
    }

    public function delete(PagePayload $payload): bool
    {
        // Remove export file if exists
        return true;
    }

    public function getStatus(PagePayload $payload): string
    {
        return 'exported';
    }

    public function export(PagePayload $payload, string $format = 'json'): string
    {
        return match($format) {
            'json' => json_encode($payload->toExportFormat(), JSON_PRETTY_PRINT),
            'markdown' => $this->exportAsMarkdown($payload),
            'html' => $this->exportAsHtml($payload),
            default => json_encode($payload->toExportFormat()),
        };
    }

    public function supportsBatch(): bool
    {
        return true;
    }

    public function getCapabilities(): array
    {
        return [
            'native_publish' => false,
            'export_json' => true,
            'export_markdown' => true,
            'export_html' => true,
            'export_csv' => true,
        ];
    }

    protected function generateExportFile(PagePayload $payload): string
    {
        $exportData = $payload->toExportFormat();
        $filename = "exports/site-{$payload->site_id}/batch-{$payload->batch_id}/payload-{$payload->id}.json";

        Storage::put($filename, json_encode($exportData, JSON_PRETTY_PRINT));

        return $filename;
    }

    protected function exportAsMarkdown(PagePayload $payload): string
    {
        $content = "# {$payload->title}\n\n";
        $content .= "**Slug:** `{$payload->slug}`\n\n";
        $content .= "**Meta Description:** {$payload->meta_description}\n\n";
        $content .= "---\n\n";
        $content .= $payload->body_content;
        
        if ($payload->internal_link_suggestions) {
            $content .= "\n\n## Suggested Internal Links\n\n";
            foreach ($payload->internal_link_suggestions as $link) {
                $content .= "- [{$link['anchor']}]({$link['url']})\n";
            }
        }

        return $content;
    }

    protected function exportAsHtml(PagePayload $payload): string
    {
        return view('exports.page-payload-html', ['payload' => $payload])->render();
    }
}
```

### Publishing Service

**File:** `app/Services/PublishingService.php`

```php
<?php

namespace App\Services;

use App\Contracts\PublishingAdapterInterface;
use App\DTOs\PublishResult;
use App\Models\PagePayload;
use App\Models\Site;
use App\Services\Publishing\ExportPublishingAdapter;
use App\Services\Publishing\WordPressPublishingAdapter;
use Illuminate\Support\Facades\Log;

class PublishingService
{
    protected array $adapters = [];

    public function __construct()
    {
        $this->registerAdapter('wordpress', WordPressPublishingAdapter::class);
        $this->registerAdapter('export', ExportPublishingAdapter::class);
    }

    public function registerAdapter(string $type, string $adapterClass): void
    {
        $this->adapters[$type] = $adapterClass;
    }

    /**
     * Get the appropriate adapter for a site
     */
    public function getAdapter(Site $site): PublishingAdapterInterface
    {
        $adapterClass = match ($site->publishing_mode) {
            'native' => $this->getAdapterForCms($site->cms_type),
            'export_only', 'manual' => ExportPublishingAdapter::class,
            default => ExportPublishingAdapter::class,
        };

        return app($adapterClass);
    }

    protected function getAdapterForCms(string $cmsType): string
    {
        return match ($cmsType) {
            'wordpress' => WordPressPublishingAdapter::class,
            default => ExportPublishingAdapter::class,
        };
    }

    /**
     * Publish a page payload using the appropriate adapter
     */
    public function publish(PagePayload $payload): PublishResult
    {
        $adapter = $this->getAdapter($payload->site);

        try {
            $result = $adapter->publish($payload);

            if ($result->success) {
                $payload->markAsPublished(
                    $result->remoteId,
                    $result->remoteUrl,
                    $result->remoteEditUrl
                );

                Log::channel('page-generation')->info('Page published', [
                    'payload_id' => $payload->id,
                    'remote_id' => $result->remoteId,
                    'adapter' => get_class($adapter),
                ]);
            } else {
                Log::channel('page-generation')->error('Publishing failed', [
                    'payload_id' => $payload->id,
                    'error' => $result->error,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::channel('page-generation')->error('Publishing exception', [
                'payload_id' => $payload->id,
                'error' => $e->getMessage(),
            ]);

            return PublishResult::failure($e->getMessage());
        }
    }

    /**
     * Validate connection for a site
     */
    public function validateConnection(Site $site): bool
    {
        $adapter = $this->getAdapter($site);
        return $adapter->validateConnection($site);
    }

    /**
     * Export a batch of payloads
     */
    public function exportBatch(Collection $payloads, string $format = 'json'): string
    {
        $site = $payloads->first()->site;
        $adapter = $this->getAdapter($site);

        $exports = $payloads->map(fn($p) => json_decode($adapter->export($p, $format), true));

        $filename = "exports/batch-{$payloads->first()->batch_id}-{$format}.zip";
        
        // Create ZIP archive
        // (Implementation details omitted for brevity)

        return $filename;
    }
}
```

---

## 5️⃣ MEASUREMENT LAYER

### Already Built (Keep & Enhance)
- ✅ Google Search Console integration
- ✅ PerformanceMetrics tracking
- ✅ BaselineSnapshots
- ✅ OptimizationRuns

### Enhancements Needed

#### Link PagePayloads to Metrics
Add to `performance_metrics` table:
```php
page_payload_id (nullable FK)
```

This allows tracking generated page performance even if published externally.

#### Track Export Performance
For export-only sites:
- Manual CSV upload of GSC data
- URL matching via canonical_url_suggestion
- Attribution to page_payload_id

---

## 📊 Refactored Step 3: Bulk Page Expansion Engine

### Updated Flow

```
User selects opportunities
         ↓
BulkPageExpansionService::generateBatch()
         ↓
Creates PageGenerationBatch record
         ↓
Dispatches GeneratePagePayloadJob for each opportunity
         ↓
Each job:
  - Calls PagePayloadGeneratorService
  - Creates PagePayload record
  - No publishing yet
         ↓
All payloads generated
         ↓
Optional: Auto-publish if site.publishing_mode = 'native'
  - Dispatches PublishPagePayloadJob for each
  - Uses appropriate adapter
         ↓
Otherwise: Mark batch as ready for export
         ↓
Update batch status
Dispatch sitemap/baseline jobs
```

### Refactored BulkPageExpansionService

**Key Changes:**
1. Generate PayloadS, not LocationPages
2. Publishing is optional second step
3. Support export-only mode
4. Track generation separate from publishing

```php
public function generateBatch(
    Site $site,
    Collection $opportunities,
    array $options = []
): PageGenerationBatch

public function publishBatch(
    PageGenerationBatch $batch,
    ?bool $autoPublish = null
): void
```

### Queue Jobs

#### `GeneratePagePayloadJob`
- Generates PagePayload
- No publishing
- Updates batch progress

#### `PublishPagePayloadJob` (New)
- Takes existing PagePayload
- Uses PublishingService
- Retries on failure
- Updates payload status

---

## 🗄️ Complete Database Schema

### New Tables

1. **`page_payloads`** (detailed above)
2. **`discovered_pages`** (detailed above)
3. **`url_patterns`** (detailed above)
4. **`publishing_logs`**
   ```php
   id, payload_id, adapter_type, action (publish|update|delete),
   result, error_message, remote_response, timestamps
   ```

### Updated Tables

1. **`sites`** - add CMS and publishing fields
2. **`page_generation_batches`** - add `payload_count`, `published_count`
3. **`performance_metrics`** - add `page_payload_id`
4. **`seo_opportunities`** - add `payload_id` FK

---

## 🎨 Filament UI Changes

### 1. Site Resource
Add fields:
- CMS Type dropdown
- Publishing Mode dropdown
- Connection validation button
- WordPress credentials (if applicable)

### 2. PagePayload Resource (New)
**List view:**
- Filter by: site, batch, status, publish_status
- Columns: title, service, location, status, publish_status
- Actions: view, edit, publish, export, regenerate

**Detail view:**
- Content preview
- SEO score breakdown
- Publishing status
- Export button
- Publish button (if native mode)

### 3. PageGenerationBatch Resource (Enhanced)
- Show payload generation progress
- Show publishing progress (separate)
- Batch export button
- Batch publish button

### 4. Dashboard Widget: Unpublished Payloads
- Count of ready-to-publish payloads
- Quick publish action
- Export all button

---

## 📦 Implementation Rollout Order

### Phase 1: Foundation (Week 1)
1. ✅ Create `page_payloads` table migration
2. ✅ Create PagePayload model
3. ✅ Create PublishingAdapterInterface
4. ✅ Create ExportPublishingAdapter (simple)
5. ✅ Add CMS fields to Site model
6. ✅ Update PageGenerationBatch to track payloads

### Phase 2: Generation Refactor (Week 1-2)
1. ✅ Refactor PagePayloadGeneratorService (from LocationPageGeneratorService)
2. ✅ Update BulkPageExpansionService to generate payloads
3. ✅ Create GeneratePagePayloadJob
4. ✅ Test payload generation end-to-end

### Phase 3: Publishing Layer (Week 2)
1. ✅ Create PublishingService
2. ✅ Create WordPressPublishingAdapter
3. ✅ Create PublishPagePayloadJob
4. ✅ Add publishing commands
5. ✅ Test WordPress publishing

### Phase 4: Export Features (Week 2-3)
1. ✅ Enhanced export formats (JSON, Markdown, HTML, CSV)
2. ✅ Batch export ZIP generation
3. ✅ Export UI in Filament

### Phase 5: Ingestion Layer (Week 3-4)
1. ✅ Create SitemapScannerService
2. ✅ Create discovered_pages table
3. ✅ Create SiteStructureAnalyzerService
4. ✅ Integration with opportunity detection

### Phase 6: Intelligence Enhancements (Week 4+)
1. ✅ SitemapRecommendationService
2. ✅ NavigationRecommendationService
3. ✅ expanded internal linking strategy

---

## ⚠️ Architectural Risks & Mitigations

### Risk 1: WordPress-Specific Code Scattered
**Mitigation:** Audit codebase for WordPress assumptions, centralize in adapter

### Risk 2: LocationPage Table Conflicts
**Status:** Keep for now, but new code uses PagePayload
**Migration Path:** Eventually migrate LocationPage → PagePayload

### Risk 3: Performance at Scale
**Mitigation:** Payload generation is lightweight, publishing is queued and rate-limited

### Risk 4: Export Format Compatibility
**Mitigation:** Support multiple formats, document import processes per CMS

### Risk 5: Credential Security
**Mitigation:** Encrypt all API credentials, use Laravel's encryption

---

## 🔮 Future Adapter Roadmap

- **Wix Adapter** (API-based)
- **Shopify Adapter** (Storefront API)
- **Webflow Adapter** (CMS API)
- **Squarespace Adapter** (limited API, mostly export)
- **Custom API Adapter** (configurable endpoints)
- **FTP Adapter** (upload static HTML files)

---

**Status:** 🟢 Architecture designed, ready for phased implementation

**Next:** Begin Phase 1 implementation (migrations + core models)
