<?php

namespace App\Http\Controllers;

use App\Models\LocationPage;
use App\Services\LocationPageRenderService;
use App\Services\LocationSchemaBuilder;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class LocationPagePreviewController extends Controller
{
    protected LocationPageRenderService $renderService;
    protected LocationSchemaBuilder $schemaBuilder;

    public function __construct(
        LocationPageRenderService $renderService,
        LocationSchemaBuilder $schemaBuilder
    ) {
        $this->renderService = $renderService;
        $this->schemaBuilder = $schemaBuilder;
    }

    /**
     * Display a location page by slug
     */
    public function show(string $slug)
    {
        // Find page by slug
        $page = LocationPage::with([
            'state',
            'county',
            'city',
            'service',
            'parent',
        ])->where('slug', $slug)->firstOrFail();

        // Check if user is logged into Filament admin
        $isAdmin = Filament::auth()->check();
        
        // Non-admins can only see published pages
        if (!$isAdmin && $page->status !== 'published') {
            abort(404);
        }

        // Build breadcrumbs
        $breadcrumbs = $this->buildBreadcrumbs($page);

        // Render HTML using the service
        $renderedHtml = $this->renderService->render($page);
        
        // Get schemas
        $schemas = $this->schemaBuilder->generateAllSchemas($page);

        return view('location-pages.show', compact(
            'page',
            'isAdmin',
            'breadcrumbs',
            'renderedHtml',
            'schemas'
        ));
    }

    /**
     * Build breadcrumbs for the page
     */
    protected function buildBreadcrumbs(LocationPage $page): array
    {
        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/'],
        ];

        // Add state (no link for now as we don't have state hub pages)
        $breadcrumbs[] = ['label' => $page->state->name, 'url' => null];

        // Add county with link to parent county hub
        if ($page->parent && $page->parent->type === 'county_hub') {
            $breadcrumbs[] = [
                'label' => $page->county->name,
                'url' => '/preview/' . $page->parent->slug
            ];
        } else {
            $breadcrumbs[] = ['label' => $page->county->name, 'url' => null];
        }

        // Add city if it's a service_city page (no link, just label)
        if ($page->type === 'service_city' && $page->city) {
            $breadcrumbs[] = ['label' => $page->city->name, 'url' => null];
        }

        return $breadcrumbs;
    }
}
