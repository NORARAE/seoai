<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your AI Citation Score: {{ $scan->score ?? 0 }}/100 — SEO AI Co™</title>
<meta name="description" content="Your AI citation readiness score is {{ $scan->score ?? 0 }}/100. See your issues, strengths, and fastest fix.">
<meta name="robots" content="noindex">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;--deep:#0b0b0b;--card:#0e0d09;--border:rgba(200,168,75,.09);
  --gold:#c8a84b;--gold-lt:#d9bc6e;--gold-dim:rgba(200,168,75,.32);
  --ivory:#ede8de;--muted:rgba(168,168,160,.78);
  --score: {{ $scan->score ?? 0 }};
}
html{font-size:18px;scroll-behavior:smooth}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.55;-webkit-font-smoothing:antialiased;overflow-x:hidden}

/* ── Nav ── */
nav{position:fixed;top:0;left:0;right:0;z-index:200;display:flex;align-items:center;justify-content:space-between;padding:28px 64px;border-bottom:1px solid transparent;transition:all .4s}
nav.stuck{background:rgba(8,8,8,.95);backdrop-filter:blur(16px);border-color:var(--border);padding:16px 64px}
.logo{text-decoration:none;display:flex;align-items:baseline;gap:1px;flex-shrink:0}
.logo-seo{font-family:'DM Sans',sans-serif;font-size:1.38rem;font-weight:300;letter-spacing:-.02em;color:var(--ivory)}
.logo-ai{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:400;letter-spacing:.02em;color:var(--gold);font-style:italic;margin:0 1px}
.logo-co{font-family:'DM Sans',sans-serif;font-size:1.18rem;font-weight:300;color:rgba(168,168,160,.65)}
.nav-right{display:flex;align-items:center;gap:28px}
.nav-link{font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.nav-link:hover{color:var(--gold)}
.nav-btn{font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;color:var(--bg);background:var(--gold);padding:12px 28px;text-decoration:none;transition:background .3s;display:inline-flex;align-items:center}
.nav-btn:hover{background:var(--gold-lt)}

/* ── Hero band ── */
.result-hero{
  padding:120px 64px 80px;
  text-align:center;
  position:relative;overflow:hidden;
}
.result-hero::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse 70% 55% at 50% 50%,rgba(200,168,75,.06) 0%,transparent 68%);
  pointer-events:none;
}
.result-eyebrow{
  font-size:.66rem;letter-spacing:.28em;text-transform:uppercase;
  color:rgba(200,168,75,.6);margin-bottom:20px;
}
.result-url{
  font-size:.82rem;color:rgba(168,168,160,.48);
  font-family:'DM Sans',sans-serif;font-weight:300;
  letter-spacing:.04em;margin-bottom:24px;
  max-width:560px;margin-left:auto;margin-right:auto;
  overflow-wrap:break-word;white-space:normal;word-break:normal;
}

/* ── Score display ── */
.score-ring-wrap{
  display:inline-flex;flex-direction:column;align-items:center;gap:14px;
  margin-bottom:28px;
}
.score-ring-svg{width:160px;height:160px}
.score-ring-bg{fill:none;stroke:rgba(200,168,75,.08);stroke-width:8}
.score-ring-fill{
  fill:none;stroke:var(--gold);stroke-width:8;
  stroke-linecap:round;
  stroke-dasharray:440;
  stroke-dashoffset:440;
  transform:rotate(-90deg);
  transform-origin:50% 50%;
  transition:stroke-dashoffset 1.4s cubic-bezier(.23,1,.32,1);
}
.score-ring-fill.animate{
  stroke-dashoffset: calc(440 - (440 * {{ (int) ($scan->score ?? 0) }} / 100));
}
.score-ring-text{
  position:absolute;
  display:flex;flex-direction:column;align-items:center;
}
.score-number{
  font-family:'Cormorant Garamond',serif;
  font-size:3.8rem;font-weight:300;line-height:1;
  color:var(--ivory);
  @php
    $score = (int) ($scan->score ?? 0);
  @endphp
  @if($score >= 70)
  color:#6aaf90;
  @elseif($score >= 40)
  color:var(--gold);
  @else
  color:#c47878;
  @endif
}
.score-label{
  font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;
  color:rgba(200,168,75,.5);margin-top:2px;
}
.score-verdict{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.4rem,3vw,2rem);
  font-weight:300;line-height:1.2;
  @if($score >= 70)
  color:#6aaf90;
  @elseif($score >= 40)
  color:var(--gold);
  @else
  color:#c47878;
  @endif
}

/* ── Report layout ── */
.result-body{max-width:1000px;margin:0 auto;padding:0 24px 80px}

/* ── Section blocks ── */
.r-section{margin-bottom:40px}
.r-section-label{
  font-size:.66rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.55);margin-bottom:16px;
  display:flex;align-items:center;gap:12px;
}
.r-section-label::before{content:'';width:20px;height:1px;background:rgba(200,168,75,.35)}

.r-list{list-style:none;display:flex;flex-direction:column;gap:12px}
.r-list-item{
  display:flex;align-items:flex-start;gap:14px;
  padding:12px 14px;
  border:1px solid rgba(200,168,75,.07);
  font-size:.92rem;line-height:1.45;word-break:normal;white-space:normal;
}
.r-list-item.issue{border-color:rgba(200,68,68,.18);background:rgba(200,68,68,.04)}
.r-list-item.strength{border-color:rgba(74,140,110,.18);background:rgba(74,140,110,.04)}
.r-list-icon{flex-shrink:0;margin-top:2px;font-size:.9rem}
.r-list-item.issue .r-list-icon{color:#c47878}
.r-list-item.strength .r-list-icon{color:#6aaf90}
.r-list-text{color:var(--muted)}

/* ── Fastest fix callout ── */
.fastest-fix{
  background:rgba(14,13,9,.9);
  border:1px solid rgba(200,168,75,.18);
  padding:28px 28px;
  margin-bottom:40px;
  position:relative;
}
.fastest-fix::before{
  content:'';position:absolute;top:0;left:0;right:0;height:2px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.5),transparent);
}
.fix-label{
  font-size:.64rem;letter-spacing:.24em;text-transform:uppercase;
  color:var(--gold);margin-bottom:10px;display:block;
}
.fix-text{font-size:.96rem;line-height:1.55;color:rgba(237,232,222,.88);white-space:normal;word-break:normal}

/* ── Upsell section ── */
.upsell-section{
  border-top:1px solid rgba(200,168,75,.1);
  padding:64px 0 0;
  text-align:center;
}
.upsell-eyebrow{
  font-size:.66rem;letter-spacing:.28em;text-transform:uppercase;
  color:rgba(200,168,75,.6);margin-bottom:16px;
}
.upsell-hed{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.8rem,4vw,2.8rem);
  font-weight:300;line-height:1.1;
  color:var(--ivory);margin-bottom:12px;
}
.upsell-hed em{font-style:italic;color:var(--gold)}
.upsell-sub{
  font-size:.92rem;color:rgba(168,168,160,.7);
  max-width:540px;margin:0 auto 36px;
  line-height:1.5;white-space:normal;word-break:normal;
}

.upsell-grid{
  display:grid;grid-template-columns:1fr 1fr;
  gap:20px;max-width:720px;margin:0 auto 32px;
}
.upsell-card{
  background:rgba(18,16,14,.92);
  border:1px solid rgba(200,168,75,.08);
  padding:20px 18px;
  text-align:left;
  position:relative;
  transition:all .2s ease;
}
@media(min-width:769px){
  .upsell-card:hover{border-color:rgba(200,168,75,.22);box-shadow:0 8px 24px rgba(0,0,0,.45);transform:translateY(-4px)}
}
.upsell-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.2),transparent);
}
.upsell-card.featured{border-color:rgba(200,168,75,.22)}
.upsell-card.featured::before{background:linear-gradient(90deg,transparent,rgba(200,168,75,.45),transparent)}
.upsell-tier{
  font-size:.62rem;letter-spacing:.2em;text-transform:uppercase;
  color:rgba(200,168,75,.55);margin-bottom:10px;display:block;
}
.upsell-name{
  font-family:'Cormorant Garamond',serif;
  font-size:1.5rem;font-weight:300;
  color:var(--ivory);margin-bottom:6px;
}
.upsell-price{
  font-family:'Cormorant Garamond',serif;
  font-size:2.2rem;font-weight:300;
  color:var(--gold);margin-bottom:12px;line-height:1;
}
.upsell-price sup{font-size:.9rem;vertical-align:top;margin-top:4px;color:rgba(200,168,75,.6)}
.upsell-desc{
  font-size:.84rem;color:var(--muted);line-height:1.5;white-space:normal;word-break:normal;
  margin-bottom:14px;
}
.upsell-cta{
  display:block;text-align:center;
  font-size:.82rem;letter-spacing:.06em;text-transform:none;
  padding:12px 18px;text-decoration:none;transition:all .2s ease;border-radius:6px
}
.upsell-card .upsell-cta{border:1px solid rgba(200,168,75,.22);color:var(--gold)}
.upsell-card .upsell-cta:hover{background:rgba(200,168,75,.08);border-color:rgba(200,168,75,.4)}
.upsell-card.featured .upsell-cta{background:var(--gold);color:#080808;border-color:var(--gold)}
.upsell-card.featured .upsell-cta:hover{background:var(--gold-lt)}

.upsell-book{
  font-size:.8rem;color:rgba(168,168,160,.45);
  line-height:1.7;margin-top:8px;
}
.upsell-book a{color:rgba(200,168,75,.6);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.2)}
.upsell-book a:hover{color:var(--gold)}

/* ── Share / scan again ── */
.result-actions{
  text-align:center;
  padding:56px 0 80px;
  border-top:1px solid rgba(200,168,75,.06);
  margin-top:64px;
}
.scan-again{
  font-size:.76rem;letter-spacing:.14em;text-transform:uppercase;
  color:rgba(168,168,160,.45);text-decoration:none;
  border-bottom:1px solid rgba(168,168,160,.14);
  transition:color .2s,border-color .2s;
}
.scan-again:hover{color:var(--muted);border-color:rgba(168,168,160,.3)}

/* ── Footer ── */
footer{border-top:1px solid var(--border);padding:28px 48px;display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center}
.footer-copy{font-size:.66rem;letter-spacing:.08em;color:var(--muted)}
.footer-legal{display:flex;gap:20px;padding-top:8px;border-top:1px solid var(--border);width:100%;justify-content:center}
.footer-legal a{font-size:.64rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.footer-legal a:hover{color:var(--gold)}

/* ── Mobile ── */
@media(max-width:768px){
  nav{padding:12px 16px}
  .nav-link{display:none}
  .nav-btn{padding:8px 16px;font-size:.72rem}
  .result-hero{padding:48px 20px 40px}
  .result-body{padding:0 16px 48px}
  .upsell-grid{grid-template-columns:1fr}
  .upsell-section{padding:40px 0 0}
  .upsell-hed{font-size:clamp(1.6rem,4vw,2.4rem)}
  footer{padding:20px 16px}
}
</style>
@include('partials.clarity')
</head>
<body>

<!-- Nav -->
<nav id="nav">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <div class="nav-right">
    <a href="/pricing" class="nav-link">Pricing</a>
    <a href="/book" class="nav-btn">Get Started</a>
  </div>
</nav>

<!-- Hero: score -->
<section class="result-hero">
  <p class="result-eyebrow">AI Citation Readiness Score</p>
  <p class="result-url">{{ $scan->url ?? '' }}</p>

  <div class="score-ring-wrap">
    <div style="position:relative;display:inline-flex;align-items:center;justify-content:center">
      <svg class="score-ring-svg" viewBox="0 0 160 160" aria-hidden="true">
        <circle class="score-ring-bg" cx="80" cy="80" r="70"/>
        <circle class="score-ring-fill" id="scoreRing" cx="80" cy="80" r="70"/>
      </svg>
      <div style="position:absolute;text-align:center">
        <div class="score-number" aria-label="Score: {{ $scan->score ?? 0 }} out of 100">{{ $scan->score ?? 0 }}</div>
        <div class="score-label">/ 100</div>
      </div>
    </div>

    @php $score = (int) ($scan->score ?? 0); @endphp
    @if($score >= 70)
      <p class="score-verdict">Strong AI citation foundation</p>
    @elseif($score >= 40)
      <p class="score-verdict">Partial — AI may cite you inconsistently</p>
    @else
      <p class="score-verdict">AI systems are unlikely to cite your site</p>
    @endif
  </div>
</section>

<!-- Report body -->
<div class="result-body">

  @if(!empty($scan->fastest_fix))
  <div class="fastest-fix">
    <span class="fix-label">Your Fastest Fix</span>
    <p class="fix-text">{{ $scan->fastest_fix }}</p>
  </div>
  @endif

  @if(!empty($scan->issues) && is_array($scan->issues))
  <div class="r-section">
    <p class="r-section-label">Issues Found ({{ count($scan->issues) }})</p>
    <ul class="r-list">
      @foreach($scan->issues as $issue)
        <li class="r-list-item issue">
          <span class="r-list-icon">✕</span>
          <span class="r-list-text">{{ $issue }}</span>
        </li>
      @endforeach
    </ul>
  </div>
  @endif

  @if(!empty($scan->strengths) && is_array($scan->strengths))
  <div class="r-section">
    <p class="r-section-label">What's Working ({{ count($scan->strengths) }})</p>
    <ul class="r-list">
      @foreach($scan->strengths as $strength)
        <li class="r-list-item strength">
          <span class="r-list-icon">✓</span>
          <span class="r-list-text">{{ $strength }}</span>
        </li>
      @endforeach
    </ul>
  </div>
  @endif

  <!-- Upsell -->
  <div class="upsell-section">
    <p class="upsell-eyebrow">Want to Fix This Automatically?</p>
    <h2 class="upsell-hed">Turn your score into<br><em>AI citations that win customers.</em></h2>
    <p class="upsell-sub">We do the work — schema, FAQ structure, entity optimization, internal linking, and content expansion — so AI systems cite you as the answer.</p>

    <div class="upsell-grid">

      <div class="upsell-card">
        <span class="upsell-tier">Citation Builder</span>
        <div class="upsell-name">Citation Builder</div>
        <div class="upsell-price"><sup>$</sup>249</div>
        <p class="upsell-desc">Full opportunity mapping, FAQ optimization, entity structure, internal linking plan, and actionable fixes delivered within two weeks.</p>
        <a href="{{ route('onboarding.start') }}?plan=citation-builder&scan_id={{ $scan->id }}" class="upsell-cta">See how to improve this site</a>
      </div>

      <div class="upsell-card featured">
        <span class="upsell-tier">Most Comprehensive</span>
        <div class="upsell-name">Authority Engine</div>
        <div class="upsell-price"><sup>$</sup>499</div>
        <p class="upsell-desc">Everything in Citation Builder plus AI-generated content structures, schema deployment, citation scoring system, and 4-month roadmap.</p>
        <a href="{{ route('onboarding.start') }}?plan=authority-engine&scan_id={{ $scan->id }}" class="upsell-cta">Fix this for me</a>
      </div>

    </div>

    <p class="upsell-book">
      Not sure which plan fits? &nbsp;
      <a href="{{ route('book.index') }}">Book a free 20-minute strategy call</a> — we'll map your market first.
    </p>
  </div>

  <!-- Save to Dashboard CTA -->
  @auth
  <div style="text-align:center;padding:40px 0 0;border-top:1px solid rgba(200,168,75,.1);margin-top:48px">
    <a href="/dashboard#ai-scans" style="display:inline-flex;align-items:center;gap:10px;padding:14px 32px;background:var(--gold);color:#080808;font-size:.78rem;letter-spacing:.12em;text-transform:uppercase;text-decoration:none;transition:background .3s">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
      View in Dashboard
    </a>
  </div>
  @else
  <div style="text-align:center;padding:32px 0 0;border-top:1px solid rgba(200,168,75,.1);margin-top:36px">  
    <p style="font-size:.76rem;color:rgba(200,168,75,.55);margin-bottom:10px">Save your results</p>
    <a href="{{ route('auth.google.redirect') }}?scan_id={{ $scan->id }}" style="display:inline-flex;align-items:center;gap:10px;padding:12px 28px;background:var(--gold);color:#080808;font-size:.86rem;letter-spacing:.02em;text-transform:none;text-decoration:none;transition:background .2s;border-radius:6px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18A10.96 10.96 0 001 12c0 1.77.42 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
      Save to Dashboard with Google
    </a>
    <p style="font-size:.78rem;color:rgba(168,168,160,.6);margin-top:12px">Track your score, scan history, and access upgrade recommendations instantly.</p>
  </div>
  @endauth

  <!-- Actions -->
  <div class="result-actions">
    <a href="{{ route('quick-scan.show') }}" class="scan-again">Scan a different URL</a>
  </div>

</div>

<!-- Footer -->
<footer>
  <a href="{{ url('/') }}" class="logo" style="opacity:.5">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; 2026 SEO AI Co™</span>
  <nav class="footer-legal">
    <a href="{{ route('privacy') }}">Privacy</a>
    <a href="{{ route('terms') }}">Terms</a>
    <a href="/pricing">Pricing</a>
  </nav>
</footer>

<script>
  const nav = document.getElementById('nav');
  window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60));

  // Animate score ring on load
  window.addEventListener('load', function() {
    setTimeout(function() {
      const ring = document.getElementById('scoreRing');
      if (ring) { ring.classList.add('animate'); }
    }, 300);
  });
</script>
@include('components.tm-style')
</body>
</html>
