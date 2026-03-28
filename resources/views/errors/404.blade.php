<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found · SEOAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-10px); }
        }
        .float-anim { animation: float 4s ease-in-out infinite; }

        @keyframes fade-in {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in          { animation: fade-in 0.5s ease-out forwards; }
        .fade-in-delay-1  { opacity: 0; animation: fade-in 0.5s ease-out 0.15s forwards; }
        .fade-in-delay-2  { opacity: 0; animation: fade-in 0.5s ease-out 0.3s forwards;  }
        .fade-in-delay-3  { opacity: 0; animation: fade-in 0.5s ease-out 0.45s forwards; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 min-h-screen flex flex-col">

    {{-- ────────────────────────────────────────────────────────────────
         Define the page's link structure in one place.
         All URLs are confirmed routes from routes/web.php.
         Filament admin URLs are direct paths (no named route needed).
    ──────────────────────────────────────────────────────────────────── --}}
    @php
        $popularPages = [
            [
                'label' => 'SEO Growth Command Center',
                'url'   => '/admin',
                'desc'  => 'Main SEO dashboard & command center',
                'icon'  => 'chart',
            ],
            [
                'label' => 'SEO Marketing Pages',
                'url'   => '/admin/seo-marketing-pages',
                'desc'  => 'Browse all indexed SEO content pages',
                'icon'  => 'document',
            ],
            [
                'label' => 'SEO Opportunities',
                'url'   => '/admin/seo-opportunities',
                'desc'  => 'Revenue gaps and ranking opportunities',
                'icon'  => 'trending',
            ],
            [
                'label' => 'Book a Consultation',
                'url'   => route('book.index'),
                'desc'  => 'Schedule a strategy call',
                'icon'  => 'calendar',
            ],
            [
                'label' => 'Privacy Policy',
                'url'   => route('privacy'),
                'desc'  => 'Legal & data information',
                'icon'  => 'shield',
            ],
        ];

        // SVG path fragments keyed by icon name (keeps the loop tidy)
        $icons = [
            'chart'    => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'trending' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
            'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'shield'   => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        ];
    @endphp

    {{-- ── TOP NAV ──────────────────────────────────────────────── --}}
    <nav class="border-b border-white/10 bg-white/5 backdrop-blur-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg p-1.5 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="text-white font-bold text-lg">SEOAI</span>
            </a>
            @auth
            <a href="{{ route('app.dashboard') }}"
               class="text-sm text-slate-300 hover:text-white transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            @endauth
        </div>
    </nav>

    {{-- ── MAIN CONTENT ──────────────────────────────────────────── --}}
    <main class="flex-1 flex items-center justify-center px-4 py-16">
        <div class="max-w-2xl w-full text-center">

            {{-- Floating illustration --}}
            <div class="float-anim mb-8 fade-in">
                <div class="inline-flex items-center justify-center w-28 h-28 rounded-full bg-white/5 border border-white/10 shadow-2xl">
                    <svg class="w-14 h-14 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <div class="mt-3 text-7xl font-black text-white/8 tracking-tighter select-none">404</div>
            </div>

            {{-- Heading --}}
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3 fade-in-delay-1">
                Oops — This Page Doesn't Exist
            </h1>
            <p class="text-slate-400 text-lg mb-2 fade-in-delay-1">
                Looks like this URL wandered off the map.
            </p>
            <p class="text-slate-500 text-sm mb-10 fade-in-delay-1">
                The page you're looking for may have been moved, deleted, or never existed.<br class="hidden sm:block">
                {{-- e() escapes output; request()->path() is always a plain string --}}
                Requested: <code class="text-indigo-300/80 bg-white/5 px-2 py-0.5 rounded text-xs font-mono">{{ e('/'.request()->path()) }}</code>
            </p>

            {{-- ── PRIMARY CTAs ────────────────────────────────────────── --}}
            <div class="flex flex-wrap gap-3 justify-center mb-14 fade-in-delay-2">

                {{-- Always present — confirmed route: 'home' → GET / --}}
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold text-sm transition-all shadow-lg hover:shadow-indigo-500/25 hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Go to Homepage
                </a>

                {{-- Auth-gated — confirmed route: 'app.dashboard' → GET /dashboard --}}
                @auth
                <a href="{{ route('app.dashboard') }}"
                    class="inline-flex items-center gap-2 px-5 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl font-semibold text-sm border border-white/20 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    View Dashboard
                </a>
                @endauth

                {{-- Filament admin area — direct URL, no named route --}}
                <a href="/admin/seo-marketing-pages"
                    class="inline-flex items-center gap-2 px-5 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl font-semibold text-sm border border-white/20 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    SEO Opportunities
                </a>

                {{-- Confirmed route: 'book.index' → GET /book --}}
                <a href="{{ route('book.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-3 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 rounded-xl font-semibold text-sm border border-amber-500/30 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Book a Call
                </a>

            </div>

            {{-- ── POPULAR PAGES ────────────────────────────────────────── --}}
            <div class="mb-4 fade-in-delay-3">
                <p class="text-xs uppercase tracking-widest text-slate-500 font-semibold mb-4">Popular Pages</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-w-lg mx-auto text-left">
                    @foreach($popularPages as $link)
                    <a href="{{ $link['url'] }}"
                        class="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-indigo-500/40 transition-all group">
                        <div class="mt-0.5 w-7 h-7 rounded-lg bg-indigo-500/20 flex-shrink-0 flex items-center justify-center group-hover:bg-indigo-500/30 transition-colors">
                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $icons[$link['icon']] ?? $icons['chart'] }}"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-white">{{ $link['label'] }}</div>
                            <div class="text-xs text-slate-500">{{ $link['desc'] }}</div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

        </div>
    </main>

    {{-- ── ANALYTICS (defensive — fires only if gtag / dataLayer exists) ── --}}
    <script>
    (function () {
        var payload = {
            event:       '404_page_view',
            requested_url: window.location.href,
            referrer:    document.referrer || '(direct)',
            user_status: {{ auth()->check() ? "'authenticated'" : "'guest'" }},
        };
        if (typeof gtag === 'function') {
            gtag('event', '404_page_view', payload);
        }
        if (typeof dataLayer !== 'undefined' && Array.isArray(dataLayer)) {
            dataLayer.push(payload);
        }
    })();
    </script>

</body>
</html>
