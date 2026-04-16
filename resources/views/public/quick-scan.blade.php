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
<title>AI Citation Quick Scan — $2 Instant Score | SEO AI Co™</title>
<meta name="description" content="Find out in 60 seconds whether AI systems would cite your website. Enter your URL and get an instant AI citation readiness score for $2.">
<link rel="canonical" href="{{ url('/quick-scan') }}">
<meta property="og:title" content="AI Citation Quick Scan — $2 Instant Score">
<meta property="og:description" content="Find out in 60 seconds if AI would cite your site. Instant AI citation readiness score — $2.">
<meta property="og:url" content="{{ url('/quick-scan') }}">
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'WebPage',
    '@id'      => url('/quick-scan') . '#webpage',
    'url'      => url('/quick-scan'),
    'name'     => 'AI Citation Quick Scan — $2 Instant Score',
    'description' => 'Instant AI citation readiness score for your website. Find out in 60 seconds if AI systems would cite you.',
], JSON_UNESCAPED_SLASHES) !!}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;--deep:#0b0b0b;--card:#0e0d09;--border:rgba(200,168,75,.09);
  --gold:#c8a84b;--gold-lt:#d9bc6e;--gold-dim:rgba(200,168,75,.32);
  --ivory:#ede8de;--muted:rgba(168,168,160,.78);--warn:#c84b4b;
}
html{font-size:18px;scroll-behavior:smooth}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.6;-webkit-font-smoothing:antialiased;min-height:100vh;overflow-x:hidden}

/* ── Nav ── */
nav{position:fixed;top:0;left:0;right:0;z-index:200;display:flex;align-items:center;justify-content:space-between;padding:28px 64px;border-bottom:1px solid transparent;transition:all .4s}
nav.stuck{background:rgba(8,8,8,.95);backdrop-filter:blur(16px);border-color:var(--border);padding:16px 64px}
.logo,.logo:visited,.logo:hover,.logo:active,.logo:focus{display:inline-flex;align-items:baseline;text-decoration:none;line-height:1;color:inherit}
.logo-seo,.logo-seo:visited,.logo-seo:link{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.2rem;letter-spacing:.06em;color:#f5f0e8}
.logo-ai,.logo-ai:visited,.logo-ai:link{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.42rem;color:#c8a84b;letter-spacing:.02em;display:inline-block;transform:skewX(-11deg) translateY(-1px)}
.logo-co,.logo-co:visited,.logo-co:link{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.05rem;color:rgba(255,255,255,.45);letter-spacing:.04em}
.nav-right{display:flex;align-items:center;gap:26px}
.nav-link{font-size:.76rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(168,168,160,.72);text-decoration:none;transition:color .3s;position:relative;padding-bottom:2px;font-weight:400}
.nav-link::after{content:'';position:absolute;bottom:0;left:0;right:100%;height:1px;background:var(--gold);transition:right .3s cubic-bezier(.23,1,.32,1)}
.nav-link:hover{color:var(--gold)}
.nav-link:hover::after{right:0}
.nav-btn{font-size:.74rem;letter-spacing:.14em;text-transform:uppercase;color:var(--bg);background:var(--gold);padding:11px 28px;text-decoration:none;transition:background .3s cubic-bezier(.23,1,.32,1),box-shadow .3s cubic-bezier(.23,1,.32,1);display:inline-flex;align-items:center;white-space:nowrap;font-weight:500;margin-left:6px}
.nav-btn:hover{background:var(--gold-lt);box-shadow:0 4px 16px rgba(200,168,75,.22)}

/* ── Hero ── */
.qs-hero{
  min-height:100vh;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:120px 24px 80px;
  text-align:center;
  position:relative;
  overflow:hidden;
}
.qs-hero::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse 80% 60% at 50% 40%,rgba(200,168,75,.07) 0%,transparent 65%);
  pointer-events:none;
}
.qs-eyebrow{
  font-size:.68rem;letter-spacing:.32em;text-transform:uppercase;
  color:rgba(200,168,75,.7);margin-bottom:20px;
  display:flex;align-items:center;gap:16px;
}
.qs-eyebrow::before,.qs-eyebrow::after{content:'';width:32px;height:1px;background:rgba(200,168,75,.3)}
.qs-h1{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(2.8rem,6vw,5rem);
  font-weight:300;line-height:1.08;
  color:var(--ivory);letter-spacing:-.02em;
  margin-bottom:16px;
}
.qs-h1 em{font-style:italic;color:var(--gold)}
.qs-sub{
  font-size:clamp(1rem,2vw,1.2rem);
  color:rgba(168,168,160,.8);
  max-width:520px;margin:0 auto 12px;
  line-height:1.7;
}
.qs-price-badge{
  display:inline-flex;align-items:center;gap:8px;
  font-size:.78rem;letter-spacing:.18em;text-transform:uppercase;
  color:var(--gold);
  border:1px solid rgba(200,168,75,.22);
  padding:8px 20px;margin-bottom:40px;
}
.qs-price-badge strong{color:var(--ivory);font-weight:400}

/* ── Form card ── */
.qs-card{
  background:rgba(14,13,9,.92);
  border:1px solid rgba(200,168,75,.12);
  max-width:520px;width:100%;
  margin:0 auto;
  padding:48px 40px;
  position:relative;
}
.qs-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.4),transparent);
}

.qs-form-group{display:flex;flex-direction:column;gap:8px;margin-bottom:22px}
.qs-form-group label{
  font-size:.72rem;letter-spacing:.18em;text-transform:uppercase;
  color:rgba(168,168,160,.72);
}
.qs-form-group input{
  background:rgba(8,8,8,.8);
  border:1px solid rgba(200,168,75,.12);
  color:var(--ivory);
  font-family:'DM Sans',sans-serif;font-size:1rem;font-weight:300;
  padding:16px 20px;outline:none;
  transition:border-color .25s,box-shadow .25s;
  width:100%;
  -webkit-appearance:none;border-radius:0;
}
.qs-form-group input::placeholder{color:rgba(168,168,160,.3)}
.qs-form-group input:focus{
  border-color:rgba(200,168,75,.5);
  box-shadow:0 0 0 3px rgba(200,168,75,.07),0 0 20px rgba(200,168,75,.08);
}
.qs-form-group .field-error{
  font-size:.74rem;color:#cf8f8f;margin-top:2px;
}
.qs-form-group .field-hint{
  display:block;font-size:.72rem;color:rgba(168,168,160,.5);margin-top:3px;
}

.qs-submit{
  display:flex;align-items:center;justify-content:center;gap:10px;
  width:100%;
  background:var(--gold);color:#080808;
  font-family:'DM Sans',sans-serif;
  font-size:.84rem;font-weight:500;letter-spacing:.16em;text-transform:uppercase;
  padding:20px 24px;border:none;cursor:pointer;
  transition:background .3s,transform .2s,box-shadow .2s;
  min-height:60px;
  margin-top:8px;
  position:relative;overflow:hidden;
}
.qs-submit:hover{background:var(--gold-lt);box-shadow:0 6px 24px rgba(200,168,75,.22);transform:translateY(-1px)}
.qs-submit:active{transform:scale(.98)}
.qs-submit:disabled{opacity:.55;cursor:not-allowed;transform:none;box-shadow:none}
@keyframes scanShimmer{0%{transform:translateX(-100%)}100%{transform:translateX(100%)}}
.qs-submit::after{content:'';position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,.13) 50%,transparent 60%);animation:scanShimmer 3.5s ease-in-out infinite;pointer-events:none}

.qs-trust{
  margin-top:18px;
  font-size:.72rem;letter-spacing:.06em;
  color:rgba(168,168,160,.4);
  text-align:center;
  line-height:1.6;
}

/* Global error banner */
.qs-error-banner{
  background:rgba(200,68,68,.1);
  border:1px solid rgba(200,68,68,.3);
  color:#e8a0a0;
  padding:14px 20px;
  font-size:.88rem;
  line-height:1.6;
  margin-bottom:20px;
}

/* ── What you get ── */
.qs-what{
  max-width:900px;margin:80px auto 0;
  padding:0 24px 80px;
}
.qs-what-hed{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.5rem,3vw,2.2rem);
  font-weight:300;text-align:center;
  color:var(--ivory);margin-bottom:36px;
}
.qs-what-hed em{font-style:italic;color:var(--gold)}
.qs-checks{
  display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
  gap:20px;
}
.qs-check{
  border:1px solid rgba(200,168,75,.08);
  background:rgba(14,13,9,.6);
  padding:26px 24px;
  position:relative;
  transition:border-color .25s,transform .25s cubic-bezier(.23,1,.32,1),box-shadow .25s;
}
.qs-check:hover{border-color:rgba(200,168,75,.16);transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.2)}
.qs-check::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.14),transparent);
}
.qs-check-icon{
  font-size:1.3rem;margin-bottom:10px;
  color:rgba(200,168,75,.7);
}
.qs-check-title{
  font-size:.88rem;font-weight:400;
  color:var(--ivory);margin-bottom:6px;
}
.qs-check-desc{
  font-size:.82rem;color:var(--muted);line-height:1.65;
}

/* ── Footer ── */
footer{border-top:1px solid var(--border);padding:28px 48px;display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center}
.footer-copy{font-size:.66rem;letter-spacing:.08em;color:var(--muted)}
.footer-legal{display:flex;gap:20px;padding-top:8px;border-top:1px solid var(--border);width:100%;justify-content:center}
.footer-legal a{font-size:.64rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.footer-legal a:hover{color:var(--gold)}

/* ── Mobile ── */
@media(max-width:768px){
  nav{padding:14px 20px}
  nav.stuck{padding:10px 20px}
  .nav-link{display:none}
  .nav-btn{padding:9px 20px;font-size:.66rem}
  .qs-hero{padding:100px 20px 60px}
  .qs-card{padding:32px 24px}
  footer{padding:24px 20px}
}
@media(max-width:480px){
  .qs-checks{grid-template-columns:1fr}
}
</style>
@include('partials.clarity')
</head>
<body>

<!-- Nav -->
@include('partials.public-nav')

<!-- Hero -->
<section class="qs-hero">
  <p class="qs-eyebrow">AI Citation Quick Scan</p>
  <h1 class="qs-h1">Will AI Cite<br><em>Your Website?</em></h1>
  <p class="qs-sub">Find out in 60 seconds. Instant AI citation readiness score — see exactly why AI won't cite you and what to fix first.</p>
  <p class="qs-price-badge">Instant score &nbsp;·&nbsp; <strong>$2</strong> &nbsp;·&nbsp; No subscription</p>

  <div class="qs-card">

    @if(session('flow_message'))
      <div class="qs-error-banner" style="background:rgba(200,168,75,.12);border-color:rgba(200,168,75,.3);color:#c8a84b;">
        {{ session('flow_message') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="qs-error-banner">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('quick-scan.checkout') }}" id="scanForm">
      @csrf

      {{-- Honeypot — hidden from real users, bots fill it --}}
      <div style="position:absolute;left:-9999px;top:-9999px" aria-hidden="true">
        <label for="company_website">Company Website</label>
        <input type="text" id="company_website" name="company_website" tabindex="-1" autocomplete="off" value="">
      </div>
      {{-- Timing check — records when form was rendered --}}
      <input type="hidden" name="_loaded_at" id="_loadedAt" value="">

      <div class="qs-form-group">
        <label for="url">Your Website URL</label>
        <input
          type="text"
          id="url"
          name="url"
          placeholder="yoursite.com"
          value="{{ old('url', request('url')) }}"
          autocomplete="url"
          autocorrect="off"
          autocapitalize="off"
          spellcheck="false"
          required
        >
        <span class="field-hint">Enter any domain or URL — we'll handle the formatting.</span>
        <span style="display:block;font-size:.72rem;color:rgba(200,168,75,.45);margin-top:6px;font-style:italic;letter-spacing:.02em">This becomes your system baseline.</span>
        @error('url')
          <span class="field-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="qs-form-group">
        <label for="email">Your Email Address</label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder="you@yoursite.com"
          value="{{ old('email', request('email')) }}"
          autocomplete="email"
          required
        >
        @error('email')
          <span class="field-error">{{ $message }}</span>
        @enderror
      </div>

      <button type="submit" class="qs-submit" id="submitBtn">
        <span id="btnText">See Where You Stand — $2</span>
        <span id="btnSpinner" style="display:none">Redirecting to payment…</span>
      </button>

      <p class="qs-trust">
        Real websites only. Scan begins instantly after payment.<br>
        Secure checkout via Stripe &nbsp;·&nbsp; Score delivered instantly &nbsp;·&nbsp; Non-refundable once processing begins
      </p>
    </form>
  </div>

  <!-- What you get -->
  <div class="qs-what">
    <h2 class="qs-what-hed">What your <em>$2 scan</em> checks</h2>
    <div class="qs-checks">
      <div class="qs-check">
        <div class="qs-check-icon">⬡</div>
        <div class="qs-check-title">Machine-Readable Context</div>
        <div class="qs-check-desc">Does your site have the data layers AI systems use to identify your business type, services, and location?</div>
      </div>
      <div class="qs-check">
        <div class="qs-check-icon">?</div>
        <div class="qs-check-title">Direct Answer Signals</div>
        <div class="qs-check-desc">Does your site have direct answers AI can extract and cite in response to customer questions?</div>
      </div>
      <div class="qs-check">
        <div class="qs-check-icon">∷</div>
        <div class="qs-check-title">Definitions &amp; Explanations</div>
        <div class="qs-check-desc">Does your content clearly define what you do in the terms AI knowledge graphs look for?</div>
      </div>
      <div class="qs-check">
        <div class="qs-check-icon">↗</div>
        <div class="qs-check-title">Content Connectivity</div>
        <div class="qs-check-desc">Can AI systems navigate your site to build a complete picture of your authority and coverage?</div>
      </div>
      <div class="qs-check">
        <div class="qs-check-icon">≡</div>
        <div class="qs-check-title">Content Depth</div>
        <div class="qs-check-desc">Do your pages have enough substance for AI to justify citing you as an authoritative source?</div>
      </div>
    </div>
  </div>
</section>

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

  // Set timing check value on page load
  document.getElementById('_loadedAt').value = (Date.now() / 1000).toFixed(3);

  document.getElementById('scanForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const txt = document.getElementById('btnText');
    const spin = document.getElementById('btnSpinner');
    btn.disabled = true;
    txt.style.display = 'none';
    spin.style.display = 'inline';
  });
</script>
@include('components.tm-style')
</body>
</html>
