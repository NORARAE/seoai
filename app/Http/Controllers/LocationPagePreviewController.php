<?php

namespace App\Http\Controllers;

use App\Models\LocationPage;
use App\Services\LocationPageRenderService;
use App\Services\LocationSchemaBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Check if user should see this page
        $isAdmin = Auth::check();
        
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
            ['label' => $page->state->name, 'url' => null],
            ['label' => $page->county->name, 'url' => null],
        ];

        if ($page->type === 'service_city' && $page->city) {
            $breadcrumbs[] = ['label' => $page->city->name, 'url' => null];
        }

        if ($page->type === 'service_city' && $page->service) {
            $breadcrumbs[] = ['label' => $page->service->name, 'url' => null];
        }

        return $breadcrumbs;
    }
}
