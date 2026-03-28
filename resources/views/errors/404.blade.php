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
        .fade-in { animation: fade-in 0.5s ease-out forwards; }
        .fade-in-delay-1 { opacity: 0; animation: fade-in 0.5s ease-out 0.15s forwards; }
        .fade-in-delay-2 { opacity: 0; animation: fade-in 0.5s ease-out 0.3s forwards;  }
        .fade-in-delay-3 { opacity: 0; animation: fade-in 0.5s ease-out 0.45s forwards; }
        .fade-in-delay-4 { opacity: 0; animation: fade-in 0.5s ease-out 0.6s forwards;  }

        #countdown-bar { transition: width 1s linear; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 min-h-screen flex flex-col">

    {{-- ── TOP NAV ──────────────────────────────────────────────── --}}
    <nav class="border-b border-white/10 bg-white/5 backdrop-blur-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 group">
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
                Back to Dashboard
            </a>
            @endauth
        </div>
    </nav>

    {{-- ── MAIN CONTENT ──────────────────────────────────────────── --}}
    <main class="flex-1 flex items-center justify-center px-4 py-16">
        <div class="max-w-2xl w-full text-center">

            {{-- Illustration --}}
            <div class="float-anim mb-8 fade-in">
                <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-white/5 border border-white/10 shadow-2xl">
                    {{-- Compass / broken link SVG --}}
                    <svg class="w-16 h-16 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <div class="mt-3 text-6xl font-black text-white/10 tracking-tighter select-none">404</div>
            </div>

            {{-- Heading --}}
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3 fade-in-delay-1">
                Oops — This Page Doesn't Exist
            </h1>
            <p class="text-slate-400 text-lg mb-2 fade-in-delay-1">
                Looks like this URL wandered off the map.
            </p>
            <p class="text-slate-500 text-sm mb-10 fade-in-delay-1">
                The page you're looking for may have been moved, deleted, or never existed.
                <br class="hidden sm:block">Requested: <code class="text-indigo-300 bg-white/5 px-2 py-0.5 rounded text-xs">{{ request()->path() }}</code>
            </p>

            {{-- ── SEARCH ──────────────────────────────────────────────── --}}
            <div class="mb-10 fade-in-delay-2" id="search-section">
                <form action="/" method="GET" class="relative max-w-md mx-auto">
                    <input
                        type="text"
                        name="q"
                        id="search-input"
                        placeholder="Search pages, locations, or services..."
                        autocomplete="off"
                        class="w-full px-5 py-3.5 bg-white/10 text-white placeholder-slate-400 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm pr-12 transition"
                    >
                    <button type="submit"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </button>
                </form>
                {{-- Inline search suggestions (JS-driven) --}}
                <div id="search-suggestions"
                    class="hidden mt-2 max-w-md mx-auto bg-slate-800 border border-white/10 rounded-xl overflow-hidden shadow-xl text-left">
                </div>
            </div>

            {{-- ── PRIMARY CTAs ────────────────────────────────────────── --}}
            <div class="flex flex-wrap gap-3 justify-center mb-12 fade-in-delay-2">
                <a href="/"
                    class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold text-sm transition-all shadow-lg hover:shadow-indigo-500/25 hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Go to Homepage
                </a>

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

                <a href="/admin/seo-marketing-pages"
                    class="inline-flex items-center gap-2 px-5 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl font-semibold text-sm border border-white/20 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    SEO Opportunities
                </a>

                <a href="/admin"
                    class="inline-flex items-center gap-2 px-5 py-3 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 rounded-xl font-semibold text-sm border border-amber-500/30 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    View Reports
                </a>
            </div>

            {{-- ── SMART INTERNAL LINKS ────────────────────────────────── --}}
            <div class="mb-12 fade-in-delay-3">
                <p class="text-xs uppercase tracking-widest text-slate-500 font-semibold mb-4">Top Pages</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-w-lg mx-auto text-left">
                    @php
                        $topPages = [
                            ['label' => 'SEO Growth Command Center', 'url' => '/admin', 'desc' => 'Your main SEO dashboard'],
                            ['label' => 'SEO Marketing Pages',        'url' => '/admin/seo-marketing-pages', 'desc' => 'Browse all SEO content pages'],
                            ['label' => 'SEO Opportunities',          'url' => '/admin/seo-opportunities',   'desc' => 'Revenue & ranking gaps'],
                            ['label' => 'Book a Consultation',        'url' => '/book',                      'desc' => 'Schedule a strategy call'],
                            ['label' => 'Privacy Policy',             'url' => '/privacy',                   'desc' => 'Legal & data info'],
                        ];
                    @endphp

                    @foreach($topPages as $page)
                    <a href="{{ $page['url'] }}"
                        class="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-indigo-500/40 transition-all group">
                        <div class="mt-0.5 w-7 h-7 rounded-lg bg-indigo-500/20 flex-shrink-0 flex items-center justify-center group-hover:bg-indigo-500/30 transition-colors">
                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-white truncate">{{ $page['label'] }}</div>
                            <div class="text-xs text-slate-500 truncate">{{ $page['desc'] }}</div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- ── REDIRECT COUNTDOWN ──────────────────────────────────── --}}
            <div id="redirect-notice"
                class="fade-in-delay-4 bg-white/5 border border-white/10 rounded-xl px-6 py-4 max-w-sm mx-auto">
                <p class="text-slate-400 text-sm mb-3">
                    Redirecting to homepage in
                    <span id="countdown" class="font-bold text-white">5</span> seconds…
                </p>
                <div class="w-full bg-white/10 rounded-full h-1 overflow-hidden">
                    <div id="countdown-bar" class="h-1 bg-indigo-500 rounded-full" style="width:100%"></div>
                </div>
                <button onclick="cancelRedirect()"
                    class="mt-3 text-xs text-slate-500 hover:text-slate-300 transition-colors underline underline-offset-2">
                    Cancel redirect
                </button>
            </div>

        </div>
    </main>

    {{-- ── ANALYTICS + REDIRECT SCRIPT ──────────────────────────── --}}
    <script>
    (function () {
        // ── Analytics ────────────────────────────────────────────
        try {
            const event = {
                event:      '404_page_view',
                requested_url: window.location.href,
                referrer:   document.referrer || '(direct)',
                user_status: {{ auth()->check() ? 'true' : 'false' }} ? 'authenticated' : 'guest',
                timestamp:  new Date().toISOString(),
            };
            if (typeof gtag === 'function') {
                gtag('event', '404_page_view', event);
            }
            if (typeof dataLayer !== 'undefined') {
                dataLayer.push(event);
            }
            console.debug('[404]', event);
        } catch (e) { /* analytics optional */ }

        // ── Countdown redirect ────────────────────────────────────
        let seconds = 5;
        let cancelled = false;
        const display  = document.getElementById('countdown');
        const bar      = document.getElementById('countdown-bar');
        const notice   = document.getElementById('redirect-notice');

        // Cancel if user interacts with the page
        ['keydown', 'mousedown', 'touchstart', 'scroll'].forEach(function (ev) {
            document.addEventListener(ev, cancelRedirectOnce, { once: true, passive: true });
        });

        // Also cancel if they're typing in search
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('focus', cancelRedirectOnce, { once: true });
        }

        const interval = setInterval(function () {
            if (cancelled) return;
            seconds--;
            if (display) display.textContent = seconds;
            if (bar) bar.style.width = (seconds / 5 * 100) + '%';
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = '/';
            }
        }, 1000);

        function cancelRedirectOnce() { cancelRedirect(); }

        window.cancelRedirect = function () {
            if (cancelled) return;
            cancelled = true;
            clearInterval(interval);
            if (notice) {
                notice.innerHTML = '<p class="text-slate-500 text-sm">Redirect cancelled. Take your time.</p>';
            }
        };

        // ── Inline search suggestions ─────────────────────────────
        const pages = [
            { label: 'SEO Growth Command Center', url: '/admin' },
            { label: 'SEO Marketing Pages',        url: '/admin/seo-marketing-pages' },
            { label: 'SEO Opportunities',          url: '/admin/seo-opportunities' },
            { label: 'Dashboard',                  url: '/dashboard' },
            { label: 'Book a Consultation',        url: '/book' },
            { label: 'Homepage',                   url: '/' },
            { label: 'Privacy Policy',             url: '/privacy' },
            { label: 'Terms of Service',           url: '/terms' },
            { label: 'Admin Panel',                url: '/admin' },
        ];

        const suggestionsBox = document.getElementById('search-suggestions');

        if (searchInput && suggestionsBox) {
            searchInput.addEventListener('input', function () {
                const q = this.value.trim().toLowerCase();
                if (!q) { suggestionsBox.classList.add('hidden'); suggestionsBox.innerHTML = ''; return; }

                const matches = pages.filter(function (p) {
                    return p.label.toLowerCase().includes(q) || p.url.toLowerCase().includes(q);
                });

                if (!matches.length) { suggestionsBox.classList.add('hidden'); suggestionsBox.innerHTML = ''; return; }

                suggestionsBox.innerHTML = matches.slice(0, 5).map(function (p) {
                    return '<a href="' + p.url + '" class="flex items-center gap-3 px-4 py-3 hover:bg-white/10 transition-colors border-b border-white/5 last:border-0">'
                         + '<svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>'
                         + '<span class="text-sm text-white">' + escapeHtml(p.label) + '</span>'
                         + '<span class="text-xs text-slate-500 ml-auto">' + escapeHtml(p.url) + '</span>'
                         + '</a>';
                }).join('');
                suggestionsBox.classList.remove('hidden');
            });

            // Close on click outside
            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                    suggestionsBox.classList.add('hidden');
                }
            });
        }

        function escapeHtml(str) {
            return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
    })();
    </script>

</body>
</html>
