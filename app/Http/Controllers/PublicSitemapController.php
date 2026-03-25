<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Services\Sitemaps\SitemapPublisherService;
use Illuminate\Http\Response;

class PublicSitemapController extends Controller
{
    public function __construct(protected SitemapPublisherService $sitemapPublisherService) {}

    public function index(Site $site): Response
    {
        abort_unless($site->sitemap_enabled, 404);

        return response(
            $this->sitemapPublisherService->buildIndexXml($site),
            200,
            ['Content-Type' => 'application/xml; charset=UTF-8'],
        );
    }

    public function page(Site $site, int $page): Response
    {
        abort_unless($site->sitemap_enabled, 404);

        return response(
            $this->sitemapPublisherService->buildPageXml($site, $page),
            200,
            ['Content-Type' => 'application/xml; charset=UTF-8'],
        );
    }
}