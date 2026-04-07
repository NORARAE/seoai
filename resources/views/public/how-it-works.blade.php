<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>How It Works — SEO AI Co™</title>
<meta name="description" content="Learn how SEO AI Co™ builds structured, location-specific pages on your existing domain — expanding your site's coverage across every service and city you serve.">
<link rel="canonical" href="{{ url('/how-it-works') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;--deep:#0b0b0b;--card:#0e0d09;--card2:#111009;--border:rgba(200,168,75,.09);
  --gold:#c8a84b;--gold-lt:#e2c97d;--gold-dim:rgba(200,168,75,.40);
  --ivory:#ede8de;--muted:rgba(168,168,160,.72);--muted-lt:rgba(168,168,160,.50);
}
html{scroll-behavior:smooth;font-size:18px}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.75;min-height:100vh}

/* ── Logo ── */
.logo{display:inline-flex;align-items:baseline;text-decoration:none;line-height:1}
.logo-seo{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.2rem;letter-spacing:.06em;color:var(--ivory)}
.logo-ai{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.42rem;color:var(--gold);letter-spacing:.02em}
.logo-co{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.05rem;color:rgba(150,150,150,.5);letter-spacing:.04em}

/* ── Top bar ── */
.top-bar{display:flex;align-items:center;justify-content:space-between;padding:28px 64px;border-bottom:1px solid var(--border)}
.top-bar a.back{font-size:.76rem;letter-spacing:.1em;color:var(--muted);text-decoration:none;transition:color .3s}
.top-bar a.back:hover{color:var(--gold)}

/* ── Page wrap ── */
.page{max-width:920px;margin:0 auto;padding:88px 40px 120px}

/* ── Hero ── */
.page-eye{font-size:.62rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:20px}
.page-title{font-family:'Cormorant Garamond',serif;font-size:clamp(3rem,5.5vw,4.8rem);font-weight:300;line-height:1.04;margin-bottom:28px;color:var(--ivory)}
.page-title em{font-style:italic;color:var(--gold-lt)}
.page-intro{font-size:1.08rem;color:var(--muted);max-width:640px;line-height:1.88;margin-bottom:10px}
.page-intro-note{font-size:.80rem;color:rgba(168,168,160,.40);letter-spacing:.03em;margin-bottom:72px}

/* ── How-strip (4-pillar summary) ── */
.how-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:rgba(200,168,75,.06);border:1px solid rgba(200,168,75,.08);margin-bottom:88px}
.how-pill{background:var(--card);padding:30px 24px;display:flex;flex-direction:column;gap:10px}
.how-pill-num{font-family:'Cormorant Garamond',serif;font-size:1.15rem;color:rgba(200,168,75,.28);font-weight:300;line-height:1}
.how-pill-label{font-size:.70rem;letter-spacing:.14em;text-transform:uppercase;color:var(--gold-dim);line-height:1.4;font-weight:400}
.how-pill-sub{font-size:.80rem;color:var(--muted-lt);line-height:1.65}

/* ── Steps ── */
.steps{display:grid;gap:0;margin-bottom:88px}
.step{display:grid;grid-template-columns:64px 1fr;gap:0 36px;padding:52px 0;border-top:1px solid var(--border);position:relative}
.step:last-child{border-bottom:1px solid var(--border)}
.step-num{font-family:'Cormorant Garamond',serif;font-size:3rem;font-weight:300;color:rgba(200,168,75,.15);line-height:1;padding-top:2px;user-select:none}
.step-label{font-size:.60rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:12px}
.step-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.75rem,2.8vw,2.2rem);font-weight:400;color:var(--ivory);margin-bottom:16px;line-height:1.18}
.step-copy{font-size:.98rem;color:var(--muted);line-height:1.88;max-width:640px}
.step-copy strong{color:rgba(237,232,222,.88);font-weight:400}
.step-note{display:inline-block;margin-top:16px;font-size:.76rem;color:rgba(200,168,75,.50);letter-spacing:.04em;font-style:italic}

/* ── Trust block ── */
.trust-block{background:var(--card2);border:1px solid rgba(200,168,75,.10);padding:56px 52px;margin-bottom:88px}
.trust-block-eye{font-size:.60rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:14px;text-align:center}
.trust-block-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.5rem,2.2vw,1.9rem);font-weight:300;color:var(--ivory);text-align:center;margin-bottom:10px;line-height:1.2}
.trust-block-sub{font-size:.86rem;color:var(--muted-lt);text-align:center;margin-bottom:44px;line-height:1.75;max-width:520px;margin-left:auto;margin-right:auto}
.trust-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.trust-card{padding:26px 22px;border:1px solid rgba(200,168,75,.07);background:rgba(0,0,0,.20);display:flex;flex-direction:column;gap:12px}
.trust-icon{width:28px;height:28px;color:rgba(200,168,75,.52);flex-shrink:0}
.trust-title{font-size:.88rem;color:var(--ivory);letter-spacing:.02em;font-weight:400}
.trust-desc{font-size:.80rem;color:var(--muted-lt);line-height:1.72}
.trust-caveat-wrap{margin-top:40px;padding-top:36px;border-top:1px solid rgba(200,168,75,.06);text-align:center}
.trust-caveat{font-size:.76rem;color:rgba(168,168,160,.36);line-height:1.74;max-width:580px;margin:0 auto;font-style:italic}

/* ── CTA block ── */
.page-cta{border:1px solid rgba(200,168,75,.14);background:var(--card);padding:56px 52px;text-align:center}
.page-cta-eye{font-size:.60rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:14px}
.page-cta-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.7rem,2.6vw,2.2rem);font-weight:300;color:var(--ivory);margin-bottom:16px;line-height:1.18}
.page-cta-body{font-size:.92rem;color:var(--muted);margin-bottom:34px;line-height:1.78;max-width:440px;margin-left:auto;margin-right:auto}
.cta-btn{
  display:inline-flex;align-items:center;gap:10px;
  background:var(--gold);color:var(--deep);
  font-size:.78rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;
  text-decoration:none;padding:16px 42px;
  transition:background .25s,color .25s;
}
.cta-btn:hover{background:var(--gold-lt)}
.cta-meta{margin-top:16px;font-size:.72rem;color:rgba(168,168,160,.36);letter-spacing:.06em}
.cta-ghost{
  display:inline-block;margin-top:14px;
  font-size:.74rem;letter-spacing:.12em;text-transform:uppercase;
  color:var(--muted-lt);text-decoration:none;transition:color .25s;
}
.cta-ghost:hover{color:var(--gold)}

/* ── Footer ── */
footer{border-top:1px solid var(--border);padding:32px 64px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px}
.footer-copy{font-size:.68rem;color:rgba(168,168,160,.28);letter-spacing:.06em}
.footer-links{display:flex;gap:24px}
.footer-links a{font-size:.66rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(168,168,160,.3);text-decoration:none;transition:color .25s}
.footer-links a:hover{color:var(--gold)}

/* ── Responsive ── */
@media(max-width:760px){
  .top-bar{padding:20px 24px}
  .page{padding:52px 24px 80px}
  .how-strip{grid-template-columns:repeat(2,1fr)}
  .trust-grid{grid-template-columns:1fr}
  .page-cta{padding:40px 24px}
  .trust-block{padding:40px 24px}
  footer{padding:24px;flex-direction:column;text-align:center}
}
@media(max-width:480px){
  .how-strip{grid-template-columns:1fr}
  .step{grid-template-columns:44px 1fr;gap:0 22px}
  .step-num{font-size:2.2rem}
}

/* ── Utils ── */
sup{font-size:.55em;line-height:0;vertical-align:super}
</style>
@include('partials.clarity')
</head>
<body>

<div class="top-bar">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <a href="/" class="back">&larr; Back to home</a>
</div>

<main class="page">

  {{-- &#8212;&#8212; HERO &#8212;&#8212; --}}
  <span class="page-eye">The System</span>
  <h1 class="page-title">How <em>SEO AI Co<sup>&trade;</sup></em><br>works.</h1>
  <p class="page-intro">We build structured, location-specific pages on your existing website &mdash; expanding your domain&rsquo;s coverage across every service and city you serve. Here is how the process works, from start to finish.</p>
  <p class="page-intro-note">No site replacement. No separate platform. Built on your URL, under your brand.</p>

  {{-- &#8212;&#8212; HOW-STRIP: 4-pillar summary &#8212;&#8212; --}}
  <div class="how-strip">
    <div class="how-pill">
      <span class="how-pill-num">01</span>
      <span class="how-pill-label">Map</span>
      <span class="how-pill-sub">Market &amp; structure mapping before anything is built.</span>
    </div>
    <div class="how-pill">
      <span class="how-pill-num">02</span>
      <span class="how-pill-label">Build</span>
      <span class="how-pill-sub">Structured pages deployed to your existing domain.</span>
    </div>
    <div class="how-pill">
      <span class="how-pill-num">03</span>
      <span class="how-pill-label">Link</span>
      <span class="how-pill-sub">Internal linking, schema, and search signals configured.</span>
    </div>
    <div class="how-pill">
      <span class="how-pill-num">04</span>
      <span class="how-pill-label">Expand</span>
      <span class="how-pill-sub">Coverage grows on a controlled, structured schedule.</span>
    </div>
  </div>

  {{-- &#8212;&#8212; STEPS &#8212;&#8212; --}}
  <div class="steps">

    <div class="step">
      <span class="step-num">01</span>
      <div class="step-body">
        <span class="step-label">Market &amp; Structure Mapping</span>
        <h2 class="step-hed">We map your market before anything is built.</h2>
        <p class="step-copy">Every service you offer. Every city you serve. Every coverage gap your current site has. We document the full structure before a single page is created &mdash; so every deployment has a purpose, a place, and a clear relationship to everything else.<br><br>This phase produces the complete architecture for your expansion: service categories, location depth, URL structure, and internal link planning.</p>
        <span class="step-note">No guesswork. Mapped to your actual market.</span>
      </div>
    </div>

    <div class="step">
      <span class="step-num">02</span>
      <div class="step-body">
        <span class="step-label">Systemized Page Building</span>
        <h2 class="step-hed">Structured pages &mdash; built on your domain.</h2>
        <p class="step-copy">We generate location-specific service pages on your existing website &mdash; under your URL, inside your brand. These are not generic stubs or placeholder pages. Each one is properly structured with relevant content, schema markup, and the signals search engines and AI discovery systems use to understand what a page is about.<br><br><strong>Your site grows. Your domain earns the authority.</strong></p>
        <span class="step-note">WordPress and Divi supported natively. No migration required.</span>
      </div>
    </div>

    <div class="step">
      <span class="step-num">03</span>
      <div class="step-body">
        <span class="step-label">Internal Linking &amp; Signal Architecture</span>
        <h2 class="step-hed">Every page connects &mdash; and signals correctly.</h2>
        <p class="step-copy">We build the internal link structure, schema markup, and search signal layers that help your site get understood. Each page connects outward and inward &mdash; reinforcing your domain&rsquo;s overall coverage depth and topical authority.<br><br>This is the layer most sites are missing. It is also what separates a collection of pages from a structured visibility system.</p>
        <span class="step-note">Structured data, canonical signals, and local schema included.</span>
      </div>
    </div>

    <div class="step">
      <span class="step-num">04</span>
      <div class="step-body">
        <span class="step-label">Controlled Expansion Over Time</span>
        <h2 class="step-hed">Coverage grows. The system manages it.</h2>
        <p class="step-copy">New pages extend your reach on a structured, phased schedule. Coverage compounds &mdash; earlier pages build context and authority that later pages benefit from. Your team manages none of this directly.<br><br>Deployment is structured over a 4-month build phase. Ongoing coverage, optimisation, and signal maintenance continue under your active agreement.</p>
        <span class="step-note">Structured 4-month rollout. Continuous coverage thereafter.</span>
      </div>
    </div>

  </div>

  {{-- &#8212;&#8212; TRUST BLOCK &#8212;&#8212; --}}
  <div class="trust-block">
    <span class="trust-block-eye">How We Work</span>
    <h2 class="trust-block-hed">Built correctly. Clearly structured.</h2>
    <p class="trust-block-sub">A few things worth knowing before you engage &mdash; so you understand exactly what this system does and does not do.</p>

    <div class="trust-grid">

      <div class="trust-card">
        <svg class="trust-icon" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
        </svg>
        <span class="trust-title">Built on your domain</span>
        <p class="trust-desc">Pages are added to your existing URL structure. No separate website, no subdomain, no platform lock-in. Your domain earns the visibility.</p>
      </div>

      <div class="trust-card">
        <svg class="trust-icon" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span class="trust-title">Works with your existing site</span>
        <p class="trust-desc">We expand what you already have. Your current pages, design, and content stay exactly as they are. Nothing is removed or replaced.</p>
      </div>

      <div class="trust-card">
        <svg class="trust-icon" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <span class="trust-title">Structured, phased rollout</span>
        <p class="trust-desc">Coverage is deployed in planned phases &mdash; not all at once. Every stage is purposeful and reviewed before the next begins.</p>
      </div>

      <div class="trust-card">
        <svg class="trust-icon" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span class="trust-title">No guaranteed search outcomes</span>
        <p class="trust-desc">We build the structural foundation for visibility. Search engines decide how and when to index content. We do not control or promise specific results.</p>
      </div>

      <div class="trust-card">
        <svg class="trust-icon" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        <span class="trust-title">Platform-compliant methodology</span>
        <p class="trust-desc">All pages are built with proper markup, structured data, and platform quality standards in mind. No manipulative tactics. No shortcuts.</p>
      </div>

      <div class="trust-card">
        <svg class="trust-icon" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span class="trust-title">Reviewed before activation</span>
        <p class="trust-desc">Every engagement begins with a review. We confirm your market, your site, and your goals before any work is scoped or activated.</p>
      </div>

    </div>

    <div class="trust-caveat-wrap">
      <p class="trust-caveat">SEO AI Co&trade; builds the structural conditions for search and AI visibility &mdash; including page architecture, internal linking, schema markup, and location-specific content. We do not control search engine algorithms, guarantee indexing timelines, or promise specific traffic or revenue outcomes. Results vary based on market, competition, and factors outside our control.</p>
    </div>
  </div>

  {{-- &#8212;&#8212; CTA &#8212;&#8212; --}}
  <div class="page-cta">
    <span class="page-cta-eye">Ready to begin</span>
    <h2 class="page-cta-hed">See how this fits your market.</h2>
    <p class="page-cta-body">Start with a short market review. We look at your site, your service area, and your current coverage &mdash; and walk you through what a structured build would look like for you specifically.</p>
    <a href="{{ route('onboarding.start') }}" class="cta-btn">Start Your Market Review</a>
    <p class="cta-meta">No commitment &nbsp;&middot;&nbsp; Takes ~2 minutes &nbsp;&middot;&nbsp; Reviewed personally</p><br>
    <a href="/book" class="cta-ghost">Book a strategy call instead &rarr;</a>
  </div>

</main>

<footer>
  <a href="/" class="logo" style="opacity:.5">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; {{ date('Y') }} SEO AI Co&trade; &middot; Programmatic AI SEO Systems</span>
  <nav class="footer-links">
    <a href="{{ route('how-it-works') }}">How It Works</a>
    <a href="/book">Book</a>
    <a href="{{ route('privacy') }}">Privacy</a>
  </nav>
</footer>

</body>
</html>
