# Legacy WordPress Integration Plan

## Purpose
This document captures how SEOAIco should integrate legacy WordPress location/service URL generation logic without duplicating architecture.

## Legacy Codebases Inspected

### Primary legacy implementation (WordPress plugin runtime)
- `CLIENTS/!BioNW/AMBioMgmt/WEBCODE/seoaico-engine`
- Key files reviewed:
  - `includes/services.php`
  - `includes/locations-loader.php`
  - `includes/seeder.php`
  - `includes/audit.php`
  - `includes/shortcodes.php`
  - `includes/rest-api.php`
  - `includes/divi-apply.php`

### Legacy shortcode package variant (multi-site packaging)
- `CLIENTS/!BioNW/AMBioMgmt/WEBCODE/ABMServiceShortcodes`
- Key files reviewed:
  - `abm-service-shortcodes.php`
  - `includes/locations-king-adjacent.php`

### Additional legacy snapshots/duplicates found
- Multiple historical variants in `WEBCODE/abm-service-shortcodes*.php`
- These are considered archival references, not primary migration sources.

---

## SEOAIco vs Legacy WordPress: Architectural Comparison

## 1) System shape

### SEOAIco (Laravel)
- Data-model-first architecture with normalized entities:
  - `states`, `counties`, `cities`, `services`
  - `service_locations` (coverage matrix)
  - `location_pages` (generated location assets)
- Pipeline services for:
  - scoring and opportunity analysis
  - content composition
  - location-page generation
  - URL resolution
- Existing key services:
  - `App\Services\CoverageMatrixService`
  - `App\Services\LocationPageGeneratorService`
  - `App\Services\LocationPageComposer`
  - `App\Services\PageUrlResolver`

### Legacy WordPress
- Runtime/template-first architecture:
  - URL parsing from request path
  - taxonomy-driven location resolution (`abm_location`)
  - shortcode rendering and dynamic in-page content
  - page seeding via WordPress post APIs (`wp_insert_post`, `get_page_by_path`)
- Logic spread across helper functions in global namespace.

## 2) Data ownership

### SEOAIco
- Canonical source-of-truth intended for geo/service entities and generation decisions.

### Legacy WordPress
- Acts as rendering and publishing runtime with additional embedded logic and duplicated maps.

## 3) URL generation strategy

### SEOAIco
- Structured records (`slug`, `url_path`, `canonical_url`) and deterministic generation path.

### Legacy WordPress
- Candidate-path rules and pattern-based derivation in helpers:
  - service-city pattern
  - nested neighborhood/area variants
  - page existence checks by path and URL.

## 4) Coupling

### SEOAIco
- Service-oriented, CMS-agnostic potential.

### Legacy WordPress
- Hard-coupled to WP taxonomy, post meta, permalink APIs, and Divi template assignment.

---

## Recommended Integration Strategy

## Guiding principle
Use SEOAIco as the domain/control plane and keep WordPress as a publish/render adapter.

## Strategy summary
1. **Centralize domain logic in SEOAIco**
  - Service slug normalization
  - canonical label/suffix logic
   - candidate URL pattern strategy
   - parent/child page specification rules

2. **Retain WordPress-specific runtime logic in adapter layer**
   - taxonomy term assignment
   - `wp_insert_post`/`wp_update_post` orchestration
   - shortcode rendering and Divi-specific hooks

3. **Avoid dual source-of-truth**
   - SEOAIco geo tables become primary source for states/counties/cities/services.
   - Legacy static location maps become migration/bootstrap inputs only.

4. **Adopt contract-based publishing boundary**
   - SEOAIco emits normalized page specs and metadata payloads.
   - WordPress adapter consumes payloads and performs CMS-specific operations.

---

## Reusable Logic to Port into SEOAIco

## High-value deterministic logic
- Service slug/label/suffix normalization rules
  - from `includes/services.php`
- Candidate path ordering and location-type branching
  - from `includes/audit.php` (`abm_audit_candidate_paths`)
- Parent key derivation and location key normalization
  - from `includes/audit.php`, `includes/seeder.php`
- Idempotent ensure-page semantics (domain-level behavior, not WP API calls)
  - from `includes/seeder.php`

## Medium-value reusable patterns
- URL pattern token strategy (`%service%-%city%-%state%`)
  - from `includes/shortcodes.php`
- Controlled fallback ordering for URL lookup and page matching
  - from `includes/rest-api.php`

---

## Logic to Keep Separate (Do Not Port into Core Domain)

- WordPress shortcode rendering pipeline and presentation assembly
  - `includes/shortcodes.php`
- WordPress taxonomy assignment details (`abm_location`) and term lookups
  - `includes/seeder.php`, `includes/locations-loader.php`
- Divi template assignment / theme builder behaviors
  - `includes/divi-apply.php`
- WordPress-specific REST endpoint auth and controller implementation details
  - `includes/rest-api.php`

These belong in a WordPress adapter/integration package, not in SEOAIco core domain modules.

---

## Risks and Duplication Concerns

1. **Slug logic divergence**
   - If legacy and SEOAIco keep separate normalization maps, generated URLs and labels drift.

2. **Dual location maps**
   - Static legacy location arrays can conflict with SEOAIco city/county/state tables.

3. **Duplicate page creation paths**
   - WordPress seeder and SEOAIco generator both creating pages independently can produce near-duplicates.

4. **URL reconciliation mismatch**
   - Different candidate path precedence causes false missing-page detection.

5. **Operational coupling drift**
   - Domain rules hidden in shortcode runtime become difficult to test and maintain.

---

## Proposed Target Laravel Class / Module Structure

## Domain modules (first-class in SEOAIco)
- `App\Domain\LocationPages\ServiceSlugNormalizer`
  - canonical slug-to-label/suffix/base logic
- `App\Domain\LocationPages\UrlPatternStrategy`
  - candidate path generation by location type
- `App\Domain\LocationPages\LocationKeyResolver`
  - parent key, state abbreviation, normalized keys
- `App\Domain\LocationPages\PageSpecificationBuilder`
  - parent/child page specs and deterministic metadata

## Application services (orchestration)
- `App\Services\LocationPageGenerationOrchestrator`
  - coordinates matrix + specs + publishing dispatch
- `App\Services\Publishing\PublisherInterface`
  - abstract publishing contract
- `App\Services\Publishing\WordPressPublisher`
  - WP adapter implementation (can live in integration package)

## Infrastructure / integration
- `App\Integrations\WordPress\WordPressClient`
- `App\Integrations\WordPress\Dto\PagePublishPayload`
- `App\Integrations\WordPress\Dto\SeoUpdatePayload`

## Notes
- Existing services (`CoverageMatrixService`, `LocationPageGeneratorService`, `PageUrlResolver`) should be extended/reused, not replaced by parallel stacks.

---

## Phased Migration Plan

## Phase 0 — Stabilization and source-of-truth lock
- Declare SEOAIco as canonical source for geo/service entities.
- Freeze legacy map edits except emergency fixes.
- Document current WordPress-only behaviors that must remain adapter-side.

## Phase 1 — Pure logic extraction
- Port deterministic legacy helpers into SEOAIco domain modules:
  - service slug normalization
  - candidate path strategy
  - location key derivation
- Add unit tests against known legacy examples.

## Phase 2 — Generator alignment
- Refactor `LocationPageGeneratorService` to use extracted modules.
- Ensure generated specs remain idempotent.
- Validate against existing `location_pages` and `service_locations` records.

## Phase 3 — Publisher boundary
- Introduce `PublisherInterface` and WordPress adapter contract.
- Keep WordPress runtime concerns out of domain modules.
- Route publishing through explicit payloads/events.

## Phase 4 — Controlled rollout
- Pilot with one service set and one county.
- Compare generated URLs/specs against legacy outcomes.
- Resolve deltas before broad rollout.

## Phase 5 — Legacy runtime minimization
- Retain only shortcode rendering and theme wiring in legacy plugin.
- Decommission redundant legacy generation logic once parity is verified.

---

## Decision Summary
- **Port** deterministic URL/service/location business rules.
- **Adapt** WP publishing through an integration boundary.
- **Freeze** legacy generation logic after parity validation.
- **Avoid** duplicating architecture by extending current SEOAIco services instead of building a second generation stack.
