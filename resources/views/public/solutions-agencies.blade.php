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
<title>For Agencies — SEOAIco</title>
<meta name="description" content="White-label SEO infrastructure for agencies. Deploy territory-locked coverage under your brand — own the territory, retain the margin.">
<link rel="canonical" href="{{ url('/solutions/agencies') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;--card:#0e0d09;--border:rgba(200,168,75,.09);
  --gold:#c8a84b;--gold-lt:#e2c97d;--gold-dim:rgba(200,168,75,.4);
  --ivory:#ede8de;--muted:rgba(168,168,160,.72);--deep:#0b0b0b;
}
html{scroll-behavior:smooth;font-size:18px}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.75;min-height:100vh}
.logo{display:inline-flex;align-items:baseline;text-decoration:none;line-height:1}
.logo-seo{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.2rem;letter-spacing:.06em;color:var(--ivory)}
.logo-ai{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.42rem;color:var(--gold);letter-spacing:.02em}
.logo-co{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.05rem;color:rgba(150,150,150,.5);letter-spacing:.04em}
.top-bar{display:flex;align-items:center;justify-content:space-between;padding:28px 64px;border-bottom:1px solid var(--border)}
.top-bar a.back{font-size:.76rem;letter-spacing:.1em;color:var(--muted);text-decoration:none;transition:color .3s}
.top-bar a.back:hover{color:var(--gold)}

.page{max-width:880px;margin:0 auto;padding:72px 40px 100px}
.page-eye{font-size:.62rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:18px}
.page-title{font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem,5vw,4rem);font-weight:300;line-height:1.05;margin-bottom:20px}
.page-title em{font-style:italic;color:var(--gold-lt)}
.page-intro{font-size:.94rem;color:var(--muted);max-width:600px;line-height:1.8;margin-bottom:64px}

.divider{height:1px;background:var(--border);margin:56px 0}

/* ── Value props ── */
.vp-grid{display:grid;grid-template-columns:1fr 1fr;gap:2px;margin-bottom:2px}
.vp-item{padding:32px 28px;background:var(--card);border:1px solid var(--border)}
.vp-label{font-size:.56rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:10px}
.vp-hed{font-family:'Cormorant Garamond',serif;font-size:1.35rem;font-weight:400;color:var(--ivory);margin-bottom:8px}
.vp-body{font-size:.84rem;color:var(--muted);line-height:1.7}

/* ── Objection busters ── */
.ob-list{list-style:none;padding:0;margin:40px 0}
.ob-list li{padding:16px 0;border-bottom:1px solid var(--border);font-size:.88rem;color:var(--muted);line-height:1.7;display:flex;gap:16px;align-items:baseline}
.ob-list li::before{content:'—';color:rgba(200,168,75,.3);flex-shrink:0}
.ob-list li strong{color:var(--ivory);font-weight:400}

/* ── CTA ── */
.page-cta{margin-top:64px;padding:40px;border:1px solid rgba(200,168,75,.14);background:var(--card);text-align:center}
.page-cta-eye{font-size:.58rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:12px}
.page-cta h2{font-family:'Cormorant Garamond',serif;font-size:1.9rem;font-weight:300;color:var(--ivory);margin-bottom:12px}
.page-cta h2 em{font-style:italic;color:var(--gold-lt)}
.page-cta p{font-size:.86rem;color:var(--muted);margin-bottom:28px;max-width:480px;margin-left:auto;margin-right:auto;line-height:1.7}
.cta-btn{background:var(--gold);color:var(--deep);font-size:.78rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;text-decoration:none;padding:15px 40px;transition:background .25s;display:inline-block}
.cta-btn:hover{background:var(--gold-lt)}
.cta-ghost{display:block;margin-top:14px;font-size:.72rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .25s}
.cta-ghost:hover{color:var(--gold)}

footer{border-top:1px solid var(--border);padding:32px 64px;display:flex;align-items:center;justify-content:space-between}
.footer-copy{font-size:.68rem;color:rgba(168,168,160,.28)}
.footer-links{display:flex;gap:24px}
.footer-links a{font-size:.66rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(168,168,160,.3);text-decoration:none;transition:color .25s}
.footer-links a:hover{color:var(--gold)}
@media(max-width:700px){
  .top-bar{padding:20px 24px}
  .page{padding:48px 24px 72px}
  .vp-grid{grid-template-columns:1fr}
  footer{padding:24px;flex-direction:column;gap:16px;text-align:center}
}
</style>
@include('partials.clarity')
</head>
<body>
<div class="top-bar">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <a href="/solutions" class="back">← Solutions</a>
</div>

<main class="page">
  <span class="page-eye">For Agencies</span>
  <h1 class="page-title">Infrastructure you deploy.<br><em>Margin you keep.</em></h1>
  <p class="page-intro">The SEOAIco system is designed to be operated by agencies at scale. White-label it, brand it as your own, and deploy across all your clients — while holding exclusive territory on their behalf.</p>

  <div class="vp-grid">
    <div class="vp-item">
      <span class="vp-label">White-Label Ready</span>
      <h2 class="vp-hed">Your brand. Our engine.</h2>
      <p class="vp-body">Deploy the system under your agency brand. Clients see your work; you own the relationship.</p>
    </div>
    <div class="vp-item">
      <span class="vp-label">Exclusive Territory</span>
      <h2 class="vp-hed">One agency per market.</h2>
      <p class="vp-body">Each territory is locked to a single operator. Competitors cannot access the same system in your client's category and geography.</p>
    </div>
    <div class="vp-item">
      <span class="vp-label">Recurring Revenue</span>
      <h2 class="vp-hed">Licensing, not one-off work.</h2>
      <p class="vp-body">Structured as a licensing agreement. Your clients are on a sustainable model — and so is your agency.</p>
    </div>
    <div class="vp-item">
      <span class="vp-label">Scalable Infrastructure</span>
      <h2 class="vp-hed">Grows with your book of business.</h2>
      <p class="vp-body">As you add clients and territories, the system scales. No manual content production, no per-page bottleneck.</p>
    </div>
  </div>

  <div class="divider"></div>

  <h2 style="font-family:'Cormorant Garamond',serif;font-weight:300;font-size:1.6rem;color:var(--ivory);margin-bottom:8px">Common questions.</h2>
  <ul class="ob-list">
    <li><strong>Do I need to re-brand anything?</strong> No. The system runs under your agency's domain and brand identity.</li>
    <li><strong>Can I hold multiple client territories?</strong> Yes. Each client gets their own territory lock under the same licensing framework.</li>
    <li><strong>What if I already run SEO services in-house?</strong> The system is additive. It handles programmatic coverage that would otherwise require significant manual effort.</li>
    <li><strong>Is there a minimum commitment?</strong> We discuss licensing terms during your strategy session — structure varies by scale.</li>
  </ul>

  <div class="page-cta">
    <span class="page-cta-eye">Ready to explore this?</span>
    <h2>Book a <em>strategy session.</em></h2>
    <p>A 30-minute session to understand your current client base, territories you serve, and how this fits into your agency model.</p>
    <a href="/book" class="cta-btn">Book a Strategy Session</a>
    <a href="/access" class="cta-ghost">Request access information</a>
  </div>
</main>

<footer>
  <a href="/" class="logo" style="opacity:.5">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; {{ date('Y') }} SEOAIco</span>
  <nav class="footer-links">
    <a href="/solutions">Solutions</a>
    <a href="/how-it-works">How It Works</a>
    <a href="/book">Book</a>
  </nav>
</footer>
</body>
</html>
