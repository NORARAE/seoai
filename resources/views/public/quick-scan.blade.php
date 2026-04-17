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
#nav{position:fixed;top:0;left:0;right:0;z-index:200;display:flex;align-items:center;justify-content:space-between;padding:28px 64px;border-bottom:1px solid transparent;transition:all .4s}
#nav.stuck{background:rgba(8,8,8,.95);backdrop-filter:blur(16px);border-color:var(--border);padding:16px 64px}
.logo,.logo:visited,.logo:hover,.logo:active,.logo:focus{display:inline-flex;align-items:baseline;text-decoration:none;line-height:1;color:inherit;padding:8px 4px;margin:-8px -4px;position:relative;z-index:1}
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

/* ── System Progression Rail ── */
.sys-rail{
  max-width:780px;margin:0 auto 0;padding:0 24px;
  text-align:center;
}
.sys-rail-lead{
  font-size:.78rem;color:var(--muted);letter-spacing:.04em;line-height:1.7;
  margin-bottom:24px;max-width:500px;margin-left:auto;margin-right:auto;
}
.sys-rail-track{
  display:flex;align-items:center;justify-content:center;gap:0;
  background:linear-gradient(145deg,rgba(255,255,255,.025),rgba(255,255,255,.008));
  border:1px solid rgba(200,168,75,.12);
  backdrop-filter:blur(8px);
  border-radius:60px;
  padding:14px 10px;
  position:relative;
  overflow:hidden;
}
.sys-rail-track::before{
  content:'';position:absolute;top:0;left:15%;width:70%;height:1px;
  background:linear-gradient(90deg,transparent,rgba(212,175,55,.35),transparent);
}
.sys-rail-step{
  display:flex;flex-direction:column;align-items:center;gap:3px;
  padding:8px 18px;
  position:relative;
  transition:all .3s ease;
  border-radius:40px;
}
.sys-rail-step:hover{
  background:rgba(200,168,75,.06);
}
.sys-rail-step.--active{
  background:rgba(200,168,75,.1);
  box-shadow:0 0 20px rgba(200,168,75,.08);
}
.sys-rail-price{
  font-family:'Cormorant Garamond',serif;
  font-size:1.05rem;font-weight:400;color:var(--gold);letter-spacing:.01em;
}
.sys-rail-step.--active .sys-rail-price{
  color:var(--gold-lt);
  text-shadow:0 0 12px rgba(200,168,75,.3);
}
.sys-rail-label{
  font-size:.56rem;letter-spacing:.12em;text-transform:uppercase;
  color:rgba(168,168,160,.45);
}
.sys-rail-arrow{
  color:rgba(200,168,75,.2);font-size:.7rem;padding:0 2px;
  display:flex;align-items:center;
}
@media(max-width:600px){
  .sys-rail-track{flex-wrap:wrap;border-radius:20px;gap:4px;padding:12px 8px}
  .sys-rail-step{padding:6px 12px}
  .sys-rail-arrow{display:none}
  .sys-rail-price{font-size:.9rem}
}

/* ── What you get ── */
.qs-what{
  max-width:960px;margin:80px auto 0;
  padding:0 24px 80px;
}
.qs-what-hed{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.6rem,3.2vw,2.4rem);
  font-weight:300;text-align:center;
  color:var(--ivory);margin-bottom:12px;
  letter-spacing:-.01em;
}
.qs-what-hed em{font-style:italic;color:var(--gold)}
.qs-what-sub{
  text-align:center;
  font-size:.82rem;color:var(--muted);line-height:1.7;
  max-width:540px;margin:0 auto 20px;
}
.qs-what-divider{
  width:64px;height:1px;margin:0 auto 44px;
  background:linear-gradient(90deg,transparent,var(--gold),transparent);
}

/* ── Scan Cards Grid ── */
.scan-cards-row{
  display:flex;justify-content:center;gap:22px;
  margin-bottom:22px;
}
.scan-cards-row:last-child{margin-bottom:0}

/* ── Scan Card ── */
.scan-card{
  background:linear-gradient(145deg,rgba(255,255,255,.025),rgba(255,255,255,.007));
  border:1px solid rgba(212,175,55,.13);
  backdrop-filter:blur(6px);
  border-radius:14px;
  padding:32px 28px;
  position:relative;
  transition:all .35s ease;
  flex:1 1 0;
  max-width:290px;
  text-align:center;
  overflow:hidden;
}
.scan-card:hover{
  border-color:rgba(212,175,55,.38);
  box-shadow:0 0 40px rgba(212,175,55,.08);
  transform:translateY(-3px);
}
.scan-card::before{
  content:'';position:absolute;top:0;left:20%;width:60%;height:1px;
  background:linear-gradient(90deg,transparent,rgba(212,175,55,.45),transparent);
}
.scan-card:hover::after{
  content:'';position:absolute;inset:0;border-radius:14px;
  background:radial-gradient(circle at 50% 0%,rgba(212,175,55,.08),transparent 70%);
  pointer-events:none;
}
.scan-card svg{
  opacity:.82;transition:all .3s ease;margin-bottom:16px;
}
.scan-card:hover svg{
  transform:scale(1.08);opacity:1;
}
.scan-card-title{
  font-family:'Cormorant Garamond',serif;
  font-size:1rem;font-weight:400;color:var(--ivory);
  margin-bottom:10px;letter-spacing:.01em;
}
.scan-card-desc{
  font-size:.78rem;color:rgba(255,255,255,.6);line-height:1.7;
  max-width:260px;margin:0 auto;
}

@media(max-width:768px){
  .scan-cards-row{flex-direction:column;align-items:center;gap:16px}
  .scan-card{max-width:380px;width:100%}
}


/* ── Footer ── */
footer{border-top:1px solid var(--border);padding:28px 48px;display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center}
.footer-copy{font-size:.66rem;letter-spacing:.08em;color:var(--muted)}
.footer-legal{display:flex;gap:20px;padding-top:8px;border-top:1px solid var(--border);width:100%;justify-content:center}
.footer-legal a{font-size:.64rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.footer-legal a:hover{color:var(--gold)}

/* ── Mobile ── */
@media(max-width:768px){
  #nav{padding:14px 20px}
  #nav.stuck{padding:10px 20px}
  .nav-link{display:none}
  .nav-btn{padding:9px 20px;font-size:.66rem}
  .qs-hero{padding:100px 20px 60px}
  .qs-card{padding:32px 24px}
  footer{padding:24px 20px}
}
@media(max-width:480px){
  .scan-cards-row{gap:14px}
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
        <span style="display:block;font-size:.72rem;color:rgba(200,168,75,.78);margin-top:6px;font-style:italic;letter-spacing:.04em;background:linear-gradient(90deg,rgba(200,168,75,.75),rgba(217,188,110,.95));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">This becomes your system baseline.</span>
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

  <!-- System Progression Rail -->
  <div class="sys-rail">
    <p class="sys-rail-lead">Every layer builds on the last. Your data moves forward — never restarted.</p>
    <div class="sys-rail-track">
      <div class="sys-rail-step --active">
        <span class="sys-rail-price">$2</span>
        <span class="sys-rail-label">Quick Scan</span>
      </div>
      <span class="sys-rail-arrow">→</span>
      <div class="sys-rail-step">
        <span class="sys-rail-price">$99</span>
        <span class="sys-rail-label">Deep Scan</span>
      </div>
      <span class="sys-rail-arrow">→</span>
      <div class="sys-rail-step">
        <span class="sys-rail-price">$249</span>
        <span class="sys-rail-label">Fix Plan</span>
      </div>
      <span class="sys-rail-arrow">→</span>
      <div class="sys-rail-step">
        <span class="sys-rail-price">$489</span>
        <span class="sys-rail-label">Build</span>
      </div>
      <span class="sys-rail-arrow">→</span>
      <div class="sys-rail-step">
        <span class="sys-rail-price">$1,500+</span>
        <span class="sys-rail-label">Expand</span>
      </div>
      <span class="sys-rail-arrow">→</span>
      <div class="sys-rail-step">
        <span class="sys-rail-price">$4,799+</span>
        <span class="sys-rail-label">Managed</span>
      </div>
    </div>
  </div>

  <!-- What the scan analyzes -->
  <div class="qs-what">
    <h2 class="qs-what-hed">What the <em>$2 Scan</em> Analyzes</h2>
    <p class="qs-what-sub">Five structural layers that determine whether AI systems can find, interpret, and cite your business.</p>
    <div class="qs-what-divider"></div>

    <div class="scan-cards-row">
      <div class="scan-card">
        <svg width="38" height="38" viewBox="0 0 38 38" fill="none" stroke="rgba(212,175,55,.85)" stroke-width="1.35">
          <rect x="4" y="4" width="12" height="12" rx="2"/>
          <rect x="22" y="4" width="12" height="12" rx="2"/>
          <rect x="4" y="22" width="12" height="12" rx="2"/>
          <rect x="22" y="22" width="12" height="12" rx="2"/>
          <line x1="16" y1="10" x2="22" y2="10"/>
          <line x1="10" y1="16" x2="10" y2="22"/>
          <line x1="28" y1="16" x2="28" y2="22"/>
        </svg>
        <div class="scan-card-title">Machine-Readable Context</div>
        <div class="scan-card-desc">Structured data layers that define your business, services, and geography in a format AI systems can interpret and trust.</div>
      </div>

      <div class="scan-card">
        <svg width="38" height="38" viewBox="0 0 38 38" fill="none" stroke="rgba(212,175,55,.85)" stroke-width="1.35">
          <circle cx="19" cy="19" r="14"/>
          <circle cx="19" cy="19" r="8"/>
          <circle cx="19" cy="19" r="2.5" fill="rgba(212,175,55,.85)"/>
          <line x1="19" y1="1" x2="19" y2="5"/>
          <line x1="19" y1="33" x2="19" y2="37"/>
          <line x1="1" y1="19" x2="5" y2="19"/>
          <line x1="33" y1="19" x2="37" y2="19"/>
        </svg>
        <div class="scan-card-title">Direct Answer Signals</div>
        <div class="scan-card-desc">Extractable answers formatted for AI retrieval and citation in real-time search responses.</div>
      </div>

      <div class="scan-card">
        <svg width="38" height="38" viewBox="0 0 38 38" fill="none" stroke="rgba(212,175,55,.85)" stroke-width="1.35">
          <path d="M8 6 L4 10 L8 14"/>
          <path d="M30 6 L34 10 L30 14"/>
          <line x1="14" y1="10" x2="24" y2="10"/>
          <line x1="10" y1="21" x2="28" y2="21"/>
          <line x1="10" y1="26" x2="24" y2="26"/>
          <line x1="10" y1="31" x2="20" y2="31"/>
        </svg>
        <div class="scan-card-title">Definitions &amp; Explanations</div>
        <div class="scan-card-desc">Clear, structured definitions that align with how AI systems build knowledge graphs.</div>
      </div>
    </div>

    <div class="scan-cards-row">
      <div class="scan-card">
        <svg width="38" height="38" viewBox="0 0 38 38" fill="none" stroke="rgba(212,175,55,.85)" stroke-width="1.35">
          <circle cx="10" cy="10" r="3"/>
          <circle cx="28" cy="10" r="3"/>
          <circle cx="19" cy="28" r="3"/>
          <circle cx="10" cy="28" r="2" opacity=".5"/>
          <circle cx="28" cy="28" r="2" opacity=".5"/>
          <line x1="13" y1="10" x2="25" y2="10"/>
          <line x1="11.5" y1="12.5" x2="17.5" y2="25.5"/>
          <line x1="26.5" y1="12.5" x2="20.5" y2="25.5"/>
        </svg>
        <div class="scan-card-title">Content Connectivity</div>
        <div class="scan-card-desc">Internal linking and entity relationships that allow AI systems to map your authority.</div>
      </div>

      <div class="scan-card">
        <svg width="38" height="38" viewBox="0 0 38 38" fill="none" stroke="rgba(212,175,55,.85)" stroke-width="1.35">
          <rect x="6" y="22" width="5" height="12" rx="1"/>
          <rect x="13" y="17" width="5" height="17" rx="1"/>
          <rect x="20" y="12" width="5" height="22" rx="1"/>
          <rect x="27" y="6" width="5" height="28" rx="1"/>
          <line x1="4" y1="4" x2="4" y2="36" opacity=".3"/>
          <line x1="4" y1="36" x2="36" y2="36" opacity=".3"/>
        </svg>
        <div class="scan-card-title">Content Depth</div>
        <div class="scan-card-desc">Content substance and coverage required for AI systems to treat your site as a credible source.</div>
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
  if(nav) window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60));

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
