<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>404 — Page Not Found · SEO AI Co</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
  --bg:#080808;
  --gold:#c8a84b;
  --gold-lt:#e2c97d;
  --gold-dim:rgba(200,168,75,.4);
  --ivory:#ede8de;
  --muted:rgba(168,168,160,.72);
  --card-bg:rgba(10,9,7,.92);
  --border:rgba(200,168,75,.09);
}

html{font-size:18px}
body{
  background:var(--bg);color:var(--ivory);
  font-family:'DM Sans',sans-serif;font-weight:300;
  line-height:1.75;min-height:100vh;display:flex;flex-direction:column;
}

/* ── Ambient background ── */
.err-page{position:relative;flex:1;display:flex;flex-direction:column}
.err-page::before{
  content:'';position:fixed;inset:0;
  background-image:
    linear-gradient(rgba(200,168,75,.03) 1px,transparent 1px),
    linear-gradient(90deg,rgba(200,168,75,.03) 1px,transparent 1px);
  background-size:48px 48px;pointer-events:none;z-index:0;
}
.err-page::after{
  content:'';position:fixed;top:8%;left:50%;transform:translateX(-50%);
  width:800px;height:500px;
  background:radial-gradient(ellipse,rgba(200,168,75,.05) 0%,rgba(200,168,75,.015) 40%,transparent 70%);
  pointer-events:none;z-index:0;
}

/* ── Nav ── */
.err-nav{
  position:relative;z-index:2;
  border-bottom:1px solid rgba(200,168,75,.08);
  background:rgba(8,8,8,.85);
  backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
}
.err-nav-inner{
  max-width:1100px;margin:0 auto;padding:0 28px;height:56px;
  display:flex;align-items:center;justify-content:space-between;
}
.err-nav a.logo{text-decoration:none;display:inline-flex;align-items:baseline;gap:0}
.logo-seo{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.2rem;letter-spacing:.06em;color:#f5f0e8}
.logo-ai{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.42rem;color:#c8a84b;letter-spacing:.02em;display:inline-block;transform:skewX(-11deg) translateY(-1px)}
.logo-co{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.05rem;color:rgba(255,255,255,.45);letter-spacing:.04em}

.err-nav-link{
  font-size:.76rem;color:var(--muted);text-decoration:none;
  letter-spacing:.04em;transition:color .25s;display:flex;align-items:center;gap:6px;
}
.err-nav-link:hover{color:var(--ivory)}
.err-nav-link svg{width:15px;height:15px;opacity:.6}

/* ── Main ── */
.err-main{
  flex:1;display:flex;align-items:center;justify-content:center;
  padding:48px 24px;position:relative;z-index:1;
}
.err-center{max-width:640px;width:100%;text-align:center}

/* ── 404 display ── */
.err-code{
  font-family:'Cormorant Garamond',serif;font-size:clamp(4rem,12vw,7rem);
  font-weight:300;letter-spacing:.08em;
  background:linear-gradient(180deg,rgba(200,168,75,.18),rgba(200,168,75,.06));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-clip:text;
  margin-bottom:8px;line-height:1;user-select:none;
}
.err-hed{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.5rem,3.5vw,2.2rem);font-weight:300;
  color:var(--ivory);line-height:1.15;margin-bottom:12px;
}
.err-sub{
  font-size:.84rem;color:var(--muted);line-height:1.78;
  max-width:440px;margin:0 auto 8px;
}
.err-path{
  display:inline-block;font-size:.72rem;font-family:'DM Sans',monospace;
  color:rgba(200,168,75,.5);background:rgba(200,168,75,.06);
  border:1px solid rgba(200,168,75,.08);border-radius:3px;
  padding:3px 10px;letter-spacing:.02em;margin-bottom:36px;
  word-break:break-all;
}

/* ── CTAs ── */
.err-ctas{
  display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin-bottom:48px;
}
.err-btn-primary{
  display:inline-flex;align-items:center;gap:8px;
  padding:14px 32px;
  background:linear-gradient(180deg,#d8be72,#c8a84b);color:#080808;
  font-size:.74rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;
  text-decoration:none;border:1px solid rgba(226,201,125,.4);border-radius:3px;
  transition:all .3s;position:relative;overflow:hidden;
}
.err-btn-primary::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,.12),transparent);transform:translateX(-100%);transition:transform .6s}
.err-btn-primary:hover{background:linear-gradient(180deg,#e0c97e,#d4b45a);border-color:rgba(226,201,125,.65);box-shadow:0 8px 32px rgba(200,168,75,.18);transform:translateY(-1px)}
.err-btn-primary:hover::before{transform:translateX(100%)}
.err-btn-primary svg{width:14px;height:14px}

.err-btn-ghost{
  display:inline-flex;align-items:center;gap:8px;
  padding:14px 28px;
  background:rgba(200,168,75,.04);color:var(--ivory);
  font-size:.74rem;font-weight:500;letter-spacing:.08em;text-transform:uppercase;
  text-decoration:none;border:1px solid rgba(200,168,75,.1);border-radius:3px;
  transition:all .3s;
}
.err-btn-ghost:hover{background:rgba(200,168,75,.08);border-color:rgba(200,168,75,.2);transform:translateY(-1px)}
.err-btn-ghost svg{width:14px;height:14px;opacity:.6}

/* ── Popular pages ── */
.err-popular-label{
  font-size:.58rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.4);margin-bottom:16px;
}
.err-grid{
  display:grid;grid-template-columns:1fr 1fr;gap:8px;
  max-width:480px;margin:0 auto;text-align:left;
}
.err-link{
  display:flex;align-items:flex-start;gap:12px;padding:14px 16px;
  background:rgba(10,9,7,.88);border:1px solid rgba(200,168,75,.06);
  border-radius:4px;text-decoration:none;
  transition:all .3s;
}
.err-link:hover{background:rgba(14,13,9,.95);border-color:rgba(200,168,75,.14);transform:translateY(-1px)}
.err-link-icon{
  flex-shrink:0;width:28px;height:28px;border-radius:4px;
  background:rgba(200,168,75,.06);display:flex;align-items:center;justify-content:center;
  margin-top:1px;
}
.err-link-icon svg{width:14px;height:14px;color:var(--gold);opacity:.6}
.err-link:hover .err-link-icon{background:rgba(200,168,75,.1)}
.err-link:hover .err-link-icon svg{opacity:.85}
.err-link-title{font-size:.78rem;font-weight:500;color:var(--ivory);line-height:1.4}
.err-link-desc{font-size:.66rem;color:rgba(168,168,160,.45);line-height:1.5;margin-top:2px}

/* ── Responsive ── */
@media(max-width:640px){
  .err-grid{grid-template-columns:1fr}
  .err-ctas{flex-direction:column;align-items:center}
  .err-btn-primary,.err-btn-ghost{width:100%;max-width:300px;justify-content:center}
  .err-nav-inner{padding:0 20px;height:50px}
}
@media(max-width:420px){
  .err-main{padding:36px 16px}
  .err-link{padding:12px 14px}
}

/* ── Fade-in ── */
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.fade-1{animation:fadeUp .5s ease-out forwards}
.fade-2{opacity:0;animation:fadeUp .5s ease-out .12s forwards}
.fade-3{opacity:0;animation:fadeUp .5s ease-out .24s forwards}
.fade-4{opacity:0;animation:fadeUp .5s ease-out .36s forwards}
</style>
</head>
<body>

@php
    $popularPages = [
        ['label' => 'AI Visibility Scan', 'url' => route('quick-scan.show'), 'desc' => 'Get your $2 citation and visibility report', 'icon' => 'trending'],
        ['label' => 'Pricing & System Tiers', 'url' => route('pricing'), 'desc' => 'See what each level unlocks', 'icon' => 'chart'],
        ['label' => 'How It Works', 'url' => route('how-it-works'), 'desc' => 'Understand the system flow', 'icon' => 'document'],
        ['label' => 'Book a Consultation', 'url' => route('book.index'), 'desc' => 'Schedule a strategy call', 'icon' => 'calendar'],
    ];
    $icons = [
        'chart'    => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'trending' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
        'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'shield'   => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    ];
@endphp

<div class="err-page">

  {{-- Nav --}}
  <nav class="err-nav">
    <div class="err-nav-inner">
      <a href="{{ route('home') }}" class="logo" aria-label="SEO AI Co — home">
        <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
      </a>
      @auth
      <a href="{{ route('app.dashboard') }}" class="err-nav-link">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
      </a>
      @endauth
    </div>
  </nav>

  {{-- Main --}}
  <main class="err-main">
    <div class="err-center">

      <div class="fade-1">
        <div class="err-code">404</div>
      </div>

      <h1 class="err-hed fade-2">Not everything is visible yet.</h1>
      <p class="err-sub fade-2">The page you&rsquo;re looking for isn&rsquo;t available &mdash; but your next step is.</p>
      <div class="fade-2">
        <span class="err-path">{{ e('/'.request()->path()) }}</span>
      </div>

      {{-- Primary CTAs --}}
      <div class="err-ctas fade-3">
        @auth
        <a href="{{ route('app.dashboard') }}" class="err-btn-primary">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          Return to your dashboard
        </a>
        <a href="{{ route('quick-scan.show') }}" class="err-btn-ghost">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          Start a scan
        </a>
        @else
        <a href="{{ route('quick-scan.show') }}" class="err-btn-primary">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          Start a scan
        </a>
        <a href="{{ route('home') }}" class="err-btn-ghost">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          Return home
        </a>
        @endauth
      </div>
      <p class="err-sub fade-3" style="margin-bottom:32px;font-size:.72rem;color:rgba(168,168,160,.42)">Most users start with a visibility scan.</p>

      {{-- Popular Pages --}}
      <div class="fade-4">
        <p class="err-popular-label">Popular Pages</p>
        <div class="err-grid">
          @foreach($popularPages as $link)
          <a href="{{ $link['url'] }}" class="err-link">
            <div class="err-link-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$link['icon']] ?? $icons['chart'] }}"/>
              </svg>
            </div>
            <div>
              <div class="err-link-title">{{ $link['label'] }}</div>
              <div class="err-link-desc">{{ $link['desc'] }}</div>
            </div>
          </a>
          @endforeach
        </div>
      </div>

    </div>
  </main>

</div>

{{-- Analytics --}}
<script>
(function(){
    var payload={event:'404_page_view',requested_url:window.location.href,referrer:document.referrer||'(direct)',user_status:{{ auth()->check() ? "'authenticated'" : "'guest'" }}};
    if(typeof gtag==='function')gtag('event','404_page_view',payload);
    if(typeof dataLayer!=='undefined'&&Array.isArray(dataLayer))dataLayer.push(payload);
})();
</script>

</body>
</html>
