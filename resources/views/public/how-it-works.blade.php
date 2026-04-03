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
<meta name="description" content="Understand the SEO AI Co™ system — how territory-locked SEO works, what you get, and how to claim your market.">
<link rel="canonical" href="{{ url('/how-it-works') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;--deep:#0b0b0b;--card:#0e0d09;--border:rgba(200,168,75,.09);
  --gold:#c8a84b;--gold-lt:#e2c97d;--gold-dim:rgba(200,168,75,.4);
  --ivory:#ede8de;--muted:rgba(168,168,160,.72);
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
.page{max-width:860px;margin:0 auto;padding:72px 40px 100px}

/* ── Hero ── */
.page-eye{font-size:.62rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:18px}
.page-title{font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem,5vw,4rem);font-weight:300;line-height:1.05;margin-bottom:20px;color:var(--ivory)}
.page-title em{font-style:italic;color:var(--gold-lt)}
.page-intro{font-size:.94rem;color:var(--muted);max-width:600px;line-height:1.8;margin-bottom:64px}

/* ── Steps ── */
.steps{display:grid;gap:0}
.step{display:grid;grid-template-columns:48px 1fr;gap:0 28px;padding:36px 0;border-top:1px solid var(--border)}
.step:last-child{border-bottom:1px solid var(--border)}
.step-num{font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:300;color:rgba(200,168,75,.22);line-height:1;padding-top:4px}
.step-body{}
.step-label{font-size:.58rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:8px}
.step-hed{font-family:'Cormorant Garamond',serif;font-size:1.55rem;font-weight:400;color:var(--ivory);margin-bottom:10px}
.step-copy{font-size:.88rem;color:var(--muted);line-height:1.8}
.step-copy strong{color:var(--ivory);font-weight:400}

/* ── CTA block ── */
.page-cta{margin-top:72px;padding:40px 40px;border:1px solid rgba(200,168,75,.14);background:var(--card);text-align:center}
.page-cta p{font-size:.88rem;color:var(--muted);margin-bottom:24px;line-height:1.7}
.cta-btn{
  display:inline-flex;align-items:center;gap:10px;
  background:var(--gold);color:var(--deep);
  font-size:.78rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;
  text-decoration:none;padding:15px 36px;
  transition:background .25s,color .25s;
}
.cta-btn:hover{background:var(--gold-lt)}
.cta-ghost{
  display:inline-block;margin-top:16px;
  font-size:.74rem;letter-spacing:.12em;text-transform:uppercase;
  color:var(--muted);text-decoration:none;transition:color .25s;
}
.cta-ghost:hover{color:var(--gold)}

/* ── Footer ── */
footer{border-top:1px solid var(--border);padding:32px 64px;display:flex;align-items:center;justify-content:space-between}
.footer-copy{font-size:.68rem;color:rgba(168,168,160,.28);letter-spacing:.06em}
.footer-links{display:flex;gap:24px}
.footer-links a{font-size:.66rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(168,168,160,.3);text-decoration:none;transition:color .25s}
.footer-links a:hover{color:var(--gold)}

@media(max-width:700px){
  .top-bar{padding:20px 24px}
  .page{padding:48px 24px 72px}
  footer{padding:24px;flex-direction:column;gap:16px;text-align:center}
}

/* deep: needed for CTA button foreground */
.deep{color:#0b0b0b}

/* ── TM superscript ── */
sup{font-size:.55em;line-height:0;vertical-align:super}

/* ── Active status badge ── */
.step-active{display:inline-flex;align-items:center;gap:6px;font-size:.60rem;letter-spacing:.16em;text-transform:uppercase;color:#22c55e;margin-bottom:10px;font-weight:400}
.step-active::before{content:'●';font-size:.55rem;animation:active-pulse 2.4s ease-in-out infinite}
@keyframes active-pulse{0%,100%{opacity:.55}50%{opacity:1}}
</style>
@include('partials.clarity')
</head>
<body>

<div class="top-bar">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <a href="/" class="back">← Home</a>
</div>

<main class="page">
  <span class="page-eye">The System</span>
  <h1 class="page-title">How <em>SEO AI Co<sup>™</sup></em><br>works.</h1>
  <p class="page-intro">We build and hold territory-locked SEO coverage for a single operator per market. One licensee. One territory. Held under licence.</p>

  <div class="steps">

    <div class="step">
      <span class="step-num">01</span>
      <div class="step-body">
        <span class="step-label">Market Selection</span>
        <h2 class="step-hed">You choose your territory.</h2>
        <p class="step-copy">Coverage is defined by city, region, or category. <strong>One licensee per market.</strong> Once active, no competitor in your category and geography can access the same system.</p>
      </div>
    </div>

    <div class="step">
      <span class="step-num">02</span>
      <div class="step-body">
        <span class="step-label">Infrastructure Build</span>
        <h2 class="step-hed">We build your coverage foundation.</h2>
        <p class="step-copy">The system generates <strong>programmatic location pages</strong> across every city, borough, and neighbourhood in your market. Structured, interlinked, and built to rank — not just exist.</p>
      </div>
    </div>

    <div class="step">
      <span class="step-num">03</span>
      <div class="step-body">
        <span class="step-label">Search Coverage</span>
        <h2 class="step-hed">Your business appears where your customers search.</h2>
        <p class="step-copy">Coverage expands continuously across high-intent local queries. <strong>The more markets you hold, the more surface area you control.</strong> Competitors without coverage infrastructure don’t rank.</p>
      </div>
    </div>

    <div class="step">
      <span class="step-num">04</span>
      <div class="step-body">
        <span class="step-label">Territory Lock</span>
        <span class="step-active">Active</span>
        <h2 class="step-hed">Your position is held under licence.</h2>
        <p class="step-copy">Access is exclusive per category per market. Your position is held under licence — we cannot serve a competing operator in the same territory. <strong>Protected. Not shared. Not diluted.</strong></p>
      </div>
    </div>

    <div class="step">
      <span class="step-num">05</span>
      <div class="step-body">
        <span class="step-label">Ongoing Growth</span>
        <h2 class="step-hed">Coverage compounds over time.</h2>
        <p class="step-copy">Search authority builds progressively. Pages already indexed keep ranking. New pages extend reach. <strong>The longer the system runs, the stronger it holds</strong> — early operators carry a structural advantage.</p>
      </div>
    </div>

  </div>

  <div class="page-cta">
    <p>Understand your market position and what’s available in your territory.</p>
    <a href="/book" class="cta-btn">Reserve a Market Session</a><br>
    <a href="/access" class="cta-ghost">Request Access</a>
  </div>
</main>

<footer>
  <a href="/" class="logo" style="opacity:.5">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; {{ date('Y') }} SEO AI Co™ &middot; Programmatic AI SEO Systems</span>
  <a href="mailto:hello@seoaico.com" class="footer-copy" style="display:block;margin-top:4px;text-decoration:none">hello@seoaico.com</a>
  <p class="footer-copy" style="font-size:.65em;opacity:.48;max-width:420px;line-height:1.5;margin-top:4px">SEO AI Co™ and associated systems, processes, and methodologies are proprietary and may not be reproduced without permission.</p>
  <nav class="footer-links">
    <a href="/solutions">Solutions</a>
    <a href="/book">Book</a>
    <a href="/privacy">Privacy</a>
  </nav>
</footer>

</body>
</html>
