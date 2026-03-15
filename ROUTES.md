# SEOAI Route Architecture

## Three-Layer Architecture

### 1. Public Layer (/)
**Purpose**: Marketing, product information, and public-facing content  
**Authentication**: None required  
**Layout**: Custom public layouts  

| Route | Controller | Purpose |
|-------|-----------|---------|
| `/` | PublicController@landing | Main landing page |

**Future Routes**:
- `/pricing` - Pricing information
- `/features` - Feature showcase
- `/docs` - Public documentation
- `/blog` - Marketing blog

---

### 2. Customer App Layer (/dashboard/*)
**Purpose**: Main SaaS application for customers  
**Authentication**: Required (future: via middleware)  
**Layout**: `layouts.app` (app navigation, user menu)  
**Prefix**: `/dashboard`  
**Route Name Prefix**: `app.`

#### Current Routes

| Route | Controller | Purpose | Status |
|-------|-----------|---------|--------|
| `/dashboard` | DashboardController@index | Main command center | ✅ Live |

#### Future Routes (Planned)

**Sites Management**
```php
/dashboard/sites              → SiteController@index       Sites list
/dashboard/sites/create       → SiteController@create      Create new site
/dashboard/sites/{site}       → SiteController@show        Site details
/dashboard/sites/{site}/edit  → SiteController@edit        Edit site
```

**Pages Management**
```php
/dashboard/pages              → PageController@index       Customer-facing pages list
/dashboard/pages/{page}       → PageController@show        Page details & preview
/dashboard/pages/{page}/edit  → PageController@edit        Quick edit page content
```

**Internal Links**
```php
/dashboard/internal-links           → InternalLinkController@index     Link overview
/dashboard/internal-links/suggest   → InternalLinkController@suggest   Generate suggestions
/dashboard/internal-links/validate  → InternalLinkController@validate  Validate existing links
```

**Reports**
```php
/dashboard/reports              → ReportController@index    Report list
/dashboard/reports/performance  → ReportController@perf     Performance metrics
/dashboard/reports/coverage     → ReportController@coverage Location coverage
/dashboard/reports/health       → ReportController@health   System health over time
/dashboard/reports/{report}     → ReportController@show     View saved report
```

**Automations**
```php
/dashboard/automations        → AutomationController@index   Automation list
/dashboard/automations/create → AutomationController@create  Setup automation
/dashboard/automations/{id}   → AutomationController@show    View automation status
```

**Sitemaps**
```php
/dashboard/sitemaps           → SitemapController@index     Sitemap management
/dashboard/sitemaps/generate  → SitemapController@generate  Generate new sitemap
/dashboard/sitemaps/download  → SitemapController@download  Download sitemap
```

**Schema Management**
```php
/dashboard/schema           → SchemaController@index    Schema overview
/dashboard/schema/validate  → SchemaController@validate Validate all schemas
/dashboard/schema/test      → SchemaController@test     Test schema markup
```

**Settings**
```php
/dashboard/settings         → SettingController@index   User settings
/dashboard/settings/profile → SettingController@profile Edit profile
/dashboard/settings/billing → SettingController@billing Billing & subscription
/dashboard/settings/api     → SettingController@api     API keys & webhooks
```

---

### 3. Internal Admin Layer (/admin/*)
**Purpose**: Super-admin controls, advanced operations, internal management  
**Authentication**: Filament authentication (superadmin role)  
**Framework**: Filament Admin Panel  
**Managed By**: `App\Providers\Filament\AdminPanelProvider`

| Route | Resource | Purpose |
|-------|----------|---------|
| `/admin` | Filament Dashboard | Admin home |
| `/admin/login` | Auth | Admin login |
| `/admin/location-pages` | LocationPageResource | Full page CRUD |
| `/admin/sites` | SiteResource | Site management |
| `/admin/clients` | ClientResource | Client management |
| `/admin/users` | UserResource | User management |

**Future Admin Resources**:
- `/admin/opportunities` - Business opportunities
- `/admin/analytics` - System analytics
- `/admin/logs` - System logs
- `/admin/jobs` - Queue management

---

### 4. Preview Layer (/preview/*)
**Purpose**: Public-facing rendered location pages  
**Authentication**: Draft pages require Filament auth; published pages are public  

| Route | Controller | Purpose |
|-------|-----------|---------|
| `/preview/{slug}` | LocationPagePreviewController@show | View rendered page |

---

## Middleware Strategy

### Current
- No middleware currently applied (development phase)

### Planned Implementation

```php
// Customer App Layer - Require authentication
Route::prefix('dashboard')
    ->middleware(['auth', 'verified'])
    ->name('app.')
    ->group(function () {
        // All customer app routes
    });

// Role-Based Access (future)
Route::prefix('dashboard')
    ->middleware(['auth', 'role:customer'])
    ->group(function () {
        // Standard customer routes
    });

Route::prefix('dashboard/admin-tools')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        // Advanced customer admin routes
    });

// API Routes (future)
Route::prefix('api/v1')
    ->middleware(['auth:sanctum', 'throttle:60,1'])
    ->group(function () {
        // API endpoints for programmatic access
    });
```

---

## License & Role Planning

### User Roles

1. **Guest** (unauthenticated)
   - Access: Public layer only
   - Can view landing page, pricing, docs

2. **Customer** (basic license)
   - Access: Customer app layer
   - Can view dashboard, sites, pages, reports
   - Cannot access: Advanced automations, bulk operations

3. **Premium Customer** (premium license)
   - Access: Full customer app layer
   - Can access: Advanced reports, automations, API access
   - Cannot access: Internal admin tools

4. **Admin** (internal staff)
   - Access: Customer app + Internal admin layer
   - Can manage: All customer data, system settings
   - Cannot access: Superadmin features

5. **Superadmin** (system administrator)
   - Access: All layers
   - Can manage: Everything including system config, users, roles

### License-Based Feature Gates

```php
// Example feature gating by license tier
if (auth()->user()->hasFeature('advanced_automations')) {
    // Show automations section
}

if (auth()->user()->canAccessApi()) {
    // Show API credentials
}

if (auth()->user()->hasRole('admin')) {
    // Show admin tools link
}
```

---

## Navigation Structure

### Customer App Navigation (layouts.app)

**Main Nav**:
- Dashboard (/)
- Sites
- Pages
- Links
- Reports

**Dropdown / Secondary**:
- Automations
- Sitemaps
- Schema
- Settings

**Quick Actions**:
- Generate Pages
- Run Validation
- Export Data
- View Stats

**User Menu**:
- Profile
- Billing
- API Keys
- Admin Panel (if role permits)
- Logout

---

## URL Best Practices

1. **Use Resource-Based Routes**: `/dashboard/sites/{site}` not `/dashboard/site-details?id=123`
2. **Use Plural Nouns**: `/pages` not `/page`
3. **Use Verbs for Actions**: `/generate`, `/validate`, `/export`
4. **Keep URLs Short**: Max 3 segments after `/dashboard`
5. **Use Kebab-Case**: `/internal-links` not `/internalLinks`
6. **Consistent Naming**: Use same terms across routes (e.g., `site` not `website`)

---

## Migration Path

### Phase 1 (Current) ✅
- ✅ Public landing page at `/`
- ✅ Dashboard moved to `/dashboard`
- ✅ Three-layer route structure established
- ✅ App layout created

### Phase 2 (Next)
- ⏳ Add `auth` middleware to `/dashboard` routes
- ⏳ Implement Sites list view
- ⏳ Implement Pages list view
- ⏳ Create user profile/settings page

### Phase 3 (Future)
- ⏳ Build Reports module
- ⏳ Build Automations module
- ⏳ Add role-based access control
- ⏳ Implement API endpoints
- ⏳ Add license/subscription checks

### Phase 4 (Advanced)
- ⏳ Multi-tenant support
- ⏳ White-label options
- ⏳ Advanced analytics
- ⏳ Webhook integrations

---

## Developer Notes

### Adding a New Customer App Route

1. Create controller: `php artisan make:controller App/SiteController`
2. Add route to `routes/web.php` under the `dashboard` prefix group
3. Use route name: `app.sites.index`
4. Create view in `resources/views/app/sites/`
5. Extend `layouts.app` layout
6. Add navigation link to `layouts.app` navbar

### Adding a New Admin Resource

1. Create Filament resource: `php artisan make:filament-resource Site`
2. Configure in `app/Filament/Resources/SiteResource.php`
3. No route changes needed (Filament handles routing)

---

**Last Updated**: March 11, 2026  
**Version**: 1.0
