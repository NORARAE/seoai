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
<title>Solutions — SEOAIco</title>
<meta name="description" content="SEOAIco is built for agencies and service businesses ready to own their market. Explore who we work with.">
<link rel="canonical" href="{{ url('/solutions') }}">
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
.page{max-width:900px;margin:0 auto;padding:72px 40px 100px}
.page-eye{font-size:.62rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:18px}
.page-title{font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem,5vw,4rem);font-weight:300;line-height:1.05;margin-bottom:20px}
.page-title em{font-style:italic;color:var(--gold-lt)}
.page-intro{font-size:.94rem;color:var(--muted);max-width:580px;line-height:1.8;margin-bottom:64px}

/* ── Cards ── */
.sol-grid{display:grid;grid-template-columns:1fr 1fr;gap:2px}
.sol-card{
  background:var(--card);border:1px solid var(--border);
  padding:40px 36px;display:flex;flex-direction:column;gap:16px;
  text-decoration:none;transition:border-color .25s,background .25s;
}
.sol-card:hover{border-color:rgba(200,168,75,.28);background:#111008}
.sol-eye{font-size:.55rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.4)}
.sol-title{font-family:'Cormorant Garamond',serif;font-size:1.9rem;font-weight:400;color:var(--ivory);line-height:1.1}
.sol-title em{font-style:italic;color:var(--gold-lt)}
.sol-desc{font-size:.84rem;color:var(--muted);line-height:1.7;flex:1}
.sol-arrow{font-size:.76rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(200,168,75,.5);transition:color .25s}
.sol-card:hover .sol-arrow{color:var(--gold)}

/* ── Bottom CTA ── */
.page-cta{margin-top:72px;padding:40px;border:1px solid rgba(200,168,75,.12);background:var(--card);display:flex;flex-direction:column;align-items:center;gap:16px;text-align:center}
.page-cta p{font-size:.88rem;color:var(--muted);max-width:480px;line-height:1.7}
.cta-btn{background:var(--gold);color:var(--deep);font-size:.78rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;text-decoration:none;padding:15px 36px;transition:background .25s;display:inline-block}
.cta-btn:hover{background:var(--gold-lt)}

footer{border-top:1px solid var(--border);padding:32px 64px;display:flex;align-items:center;justify-content:space-between}
.footer-copy{font-size:.68rem;color:rgba(168,168,160,.28)}
.footer-links{display:flex;gap:24px}
.footer-links a{font-size:.66rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(168,168,160,.3);text-decoration:none;transition:color .25s}
.footer-links a:hover{color:var(--gold)}
@media(max-width:700px){
  .top-bar{padding:20px 24px}
  .page{padding:48px 24px 72px}
  .sol-grid{grid-template-columns:1fr}
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
  <a href="/" class="back">← Home</a>
</div>

<main class="page">
  <span class="page-eye">Solutions</span>
  <h1 class="page-title">Built for agencies and<br><em>service businesses</em><br>ready to scale.</h1>
  <p class="page-intro">SEOAIco is a territory-locked SEO infrastructure system. The system is built for two distinct operators — agencies and local service businesses. Choose your path.</p>

  <div class="sol-grid">
    <a href="/solutions/agencies" class="sol-card">
      <span class="sol-eye">Agencies</span>
      <h2 class="sol-title">For <em>Agencies</em></h2>
      <p class="sol-desc">White-label SEO infrastructure you can deploy under your brand across all your clients. Own the system, hold the territory, retain the margin.</p>
      <span class="sol-arrow">Explore Agencies →</span>
    </a>
    <a href="/solutions/business-owners" class="sol-card">
      <span class="sol-eye">Business Owners</span>
      <h2 class="sol-title">For <em>Business Owners</em></h2>
      <p class="sol-desc">Own your city. Lock out local competitors. One territory per operator — yours to hold as long as you're active.</p>
      <span class="sol-arrow">Explore Business Owners →</span>
    </a>
  </div>

  <div class="page-cta">
    <p>Not sure which fits? A strategy session will clarify your best path in under 30 minutes.</p>
    <a href="/book" class="cta-btn">Book a Strategy Session</a>
  </div>
</main>

<footer>
  <a href="/" class="logo" style="opacity:.5">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; {{ date('Y') }} SEOAIco</span>
  <nav class="footer-links">
    <a href="/how-it-works">How It Works</a>
    <a href="/book">Book</a>
    <a href="/privacy">Privacy</a>
  </nav>
</footer>
</body>
</html>
