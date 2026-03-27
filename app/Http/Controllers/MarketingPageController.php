<?php

namespace App\Http\Controllers;

use App\Models\SeoMarketingPage;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MarketingPageController extends Controller
{
    public function show(string $slug): View|Response
    {
        $page = SeoMarketingPage::where('url_slug', $slug)
            ->where('is_indexed', true)
            ->firstOrFail();

        // Related pages in same cluster (exclude self, limit 4)
        $related = SeoMarketingPage::where('cluster', $page->cluster)
            ->where('url_slug', '!=', $slug)
            ->where('is_indexed', true)
            ->limit(4)
            ->get();

        // Top money pages for hub nav links
        $moneyPages = SeoMarketingPage::whereNotNull('money_page_rank')
            ->where('url_slug', '!=', $slug)
            ->where('is_indexed', true)
            ->orderBy('money_page_rank')
            ->limit(6)
            ->get();

        return view('public.seo-page', compact('page', 'related', 'moneyPages'));
    }
}
