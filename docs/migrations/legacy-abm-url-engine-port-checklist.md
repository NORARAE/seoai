# Legacy ABM URL Engine Port Checklist

Use this checklist to track migration of legacy URL/location/service generation logic into SEOAIco.

| Legacy file | Target SEOAIco destination | Action type | Rationale | Status |
|---|---|---|---|---|
| `WEBCODE/seoaico-engine/includes/services.php` | `App\Services\Seo\Normalization\ServiceNameNormalizer` (new) | port | Canonical service slug normalization + display label/suffix rules are domain logic and should be centralized | DONE |
| `WEBCODE/seoaico-engine/includes/audit.php` (`abm_audit_candidate_paths`) | `App\Services\Seo\Urls\UrlPatternStrategy` (new) | port | Candidate path ordering is deterministic business logic currently duplicated across systems | DONE |
| `WEBCODE/seoaico-engine/includes/audit.php` (`abm_audit_parent_key_from_city`) | `App\Services\Seo\Normalization\LocationKeyNormalizer` (new) | port | Parent-key derivation is reusable and needed for consistent nested URL behavior | DONE |
| `WEBCODE/seoaico-engine/includes/seeder.php` (ensure-page semantics) | `App\Domain\LocationPages\PageSpecificationBuilder` (new) + `App\Services\LocationPageGeneratorService` (existing) | adapt | Keep idempotent parent/child generation behavior, but replace WP APIs with Laravel entity flow | TODO |
| `WEBCODE/seoaico-engine/includes/locations-loader.php` | `database/seeders` or import utility (`App\Services\Imports\LegacyLocationMapImporter`) | adapt | Legacy static maps useful as bootstrap input, not runtime source-of-truth | TODO |
| `WEBCODE/seoaico-engine/includes/rest-api.php` (lookup ordering ideas) | `App\Services\PageUrlResolver` (existing) | adapt | Reuse fallback strategy patterns while keeping Laravel-native resolver implementation | TODO |
| `WEBCODE/seoaico-engine/includes/shortcodes.php` (service URL helpers only) | `App\Services\Seo\Urls\CandidatePathBuilder` + `App\Services\Seo\Urls\UrlPatternStrategy` (new) | adapt | Extract pure helper logic only; rendering/shortcode code remains WP-specific | DONE |
| `WEBCODE/seoaico-engine/includes/shortcodes.php` (shortcode rendering) | WordPress integration package/plugin only | freeze | Presentation/runtime logic should remain outside SEOAIco core domain | TODO |
| `WEBCODE/seoaico-engine/includes/divi-apply.php` | WordPress integration package/plugin only | freeze | Theme/Divi coupling is CMS-specific and not suitable for Laravel domain layer | TODO |
| `WEBCODE/seoaico-engine/includes/seeder.php` (taxonomy term assignment) | WordPress adapter (`WordPressPublisher`) | freeze | Taxonomy operations are WP infrastructure concerns | TODO |
| `WEBCODE/ABMServiceShortcodes/abm-service-shortcodes.php` (multi-site config concepts) | `config/publishers.php` + WP adapter config | adapt | Keep environment/site mapping strategy but avoid importing shortcode runtime | TODO |
| `WEBCODE/abm-service-shortcodes*.php` historical duplicates | none | ignore | Archive snapshots; not authoritative migration sources | TODO |

## Suggested status values
- `TODO`
- `IN_PROGRESS`
- `BLOCKED`
- `DONE`
- `DEFERRED`

## Execution rule
No implementation should begin until this checklist is reviewed and each row is either accepted, edited, or removed.
