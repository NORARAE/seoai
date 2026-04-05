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
<title>For Business Owners — SEO AI Co™</title>
<meta name="description" content="Own your local market. Territory-locked SEO for service businesses — one operator per category per city.">
<link rel="canonical" href="{{ url('/solutions/business-owners') }}">
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

.proof-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:2px;margin-bottom:2px}
.stat-item{padding:28px 24px;background:var(--card);border:1px solid var(--border);text-align:center}
.stat-num{font-family:'Cormorant Garamond',serif;font-size:2.8rem;font-weight:300;color:var(--gold);display:block;line-height:1.1;margin-bottom:4px}
.stat-label{font-size:.68rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted)}

.benefit-list{list-style:none;padding:0;margin:40px 0}
.benefit-list li{padding:18px 0;border-bottom:1px solid var(--border);display:grid;grid-template-columns:20px 1fr;gap:16px;align-items:baseline}
.benefit-list li::before{content:'✦';color:var(--gold-dim);font-size:.6rem}
.benefit-title{font-size:.9rem;color:var(--ivory);font-weight:400;display:block;margin-bottom:4px}
.benefit-body{font-size:.84rem;color:var(--muted);line-height:1.7}

.page-cta{margin-top:64px;padding:40px;border:1px solid rgba(200,168,75,.14);background:var(--card);text-align:center}
.page-cta-eye{font-size:.58rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:12px}
.page-cta h2{font-family:'Cormorant Garamond',serif;font-size:1.9rem;font-weight:300;color:var(--ivory);margin-bottom:12px}
.page-cta h2 em{font-style:italic;color:var(--gold-lt)}
.page-cta p{font-size:.86rem;color:var(--muted);margin-bottom:28px;max-width:480px;margin-left:auto;margin-right:auto;line-height:1.7}
.cta-btn{background:var(--gold);color:var(--deep);font-size:.78rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;text-decoration:none;padding:15px 40px;transition:background .25s;display:inline-block}
.cta-btn:hover{background:var(--gold-lt)}
.cta-note{display:block;margin-top:12px;font-size:.72rem;color:rgba(168,168,160,.35);letter-spacing:.06em}

footer{border-top:1px solid var(--border);padding:32px 64px;display:flex;align-items:center;justify-content:space-between}
.footer-copy{font-size:.68rem;color:rgba(168,168,160,.28)}
.footer-links{display:flex;gap:24px}
.footer-links a{font-size:.66rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(168,168,160,.3);text-decoration:none;transition:color .25s}
.footer-links a:hover{color:var(--gold)}
@media(max-width:700px){
  .top-bar{padding:20px 24px}
  .page{padding:48px 24px 72px}
  .proof-stats{grid-template-columns:1fr}
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
  <span class="page-eye">For Business Owners</span>
  <h1 class="page-title">Own your city.<br><em>Lock out your competitors.</em></h1>
  <p class="page-intro">You operate in a local market and need to hold your position. Traditional SEO is slow, competitive, and shared. The SEO AI Co™ territory system changes the equation.</p>

  <div class="proof-stats">
    <div class="stat-item">
      <span class="stat-num">1</span>
      <span class="stat-label">Operator per territory</span>
    </div>
    <div class="stat-item">
      <span class="stat-num">100%</span>
      <span class="stat-label">Coverage exclusivity</span>
    </div>
    <div class="stat-item">
      <span class="stat-num">∞</span>
      <span class="stat-label">Coverage compounds over time</span>
    </div>
  </div>

  <ul class="benefit-list">
    <li>
      <span></span>
      <div>
        <span class="benefit-title">Your territory — no one else's</span>
        <span class="benefit-body">Once your market is activated, we cannot serve a competing business in the same category and geography. Your position is exclusive by contract.</span>
      </div>
    </li>
    <li>
      <span></span>
      <div>
        <span class="benefit-title">Coverage across every neighbourhood you serve</span>
        <span class="benefit-body">Programmatic pages are built for every city, suburb, and service area within your territory — so you show up where your customers search, not just where you rank today.</span>
      </div>
    </li>
    <li>
      <span></span>
      <div>
        <span class="benefit-title">Structural advantage — not a campaign</span>
        <span class="benefit-body">The system operates in structured 4-month cycles — build, stabilization, expansion, and growth. The infrastructure compounds. The longer it runs, the harder it is for anyone to displace you — even if they try.</span>
      </div>
    </li>
    <li>
      <span></span>
      <div>
        <span class="benefit-title">Built for businesses with the capacity to handle growth</span>
        <span class="benefit-body">We're selective. We want operators who can handle the volume this generates. Access is reviewed and approved individually.</span>
      </div>
    </li>
  </ul>

  <div class="divider"></div>

  <div class="page-cta">
    <span class="page-cta-eye">Is your territory still available?</span>
    <h2>Claim it before<br><em>someone else does.</em></h2>
    <p>A market session confirms availability, maps your territory, and outlines exactly what we'd build for your position.</p>
    <a href="/book" class="cta-btn">Reserve a Market Session</a>
    <span class="cta-note">Access reviewed. Market position confirmed.</span>
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
    <a href="/how-it-works">How It Works</a>
    <a href="/book">Book</a>
  </nav>
</footer>
</body>
</html>
