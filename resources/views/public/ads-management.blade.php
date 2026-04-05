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
<title>Ads Management — Search &amp; Local Campaigns — SEO AI Co™</title>
<meta name="description" content="Google Ads, local search campaigns, and paid media management from SEO AI Co™ — designed to reinforce your organic market position while your programmatic SEO compounds.">
<link rel="canonical" href="{{ url('/ads-management') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

<!-- Schema: Service + WebPage + BreadcrumbList -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "name": "SEO AI Co",
      "url": "{{ url('/') }}"
    },
    {
      "@type": "WebPage",
      "name": "Ads Management — SEO AI Co™",
      "url": "{{ url('/ads-management') }}",
      "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
          {"@type":"ListItem","position":1,"name":"Home","item":"{{ url('/') }}"},
          {"@type":"ListItem","position":2,"name":"Growth Services","item":"{{ url('/growth-services') }}"},
          {"@type":"ListItem","position":3,"name":"Ads Management","item":"{{ url('/ads-management') }}"}
        ]
      }
    },
    {
      "@type": "Service",
      "name": "Paid Advertising and Campaign Management",
      "provider": {"@type": "Organization", "name": "SEO AI Co"},
      "serviceType": "Digital Advertising Management",
      "description": "Google Ads setup and management, local search campaigns, and paid media strategy for local service businesses expanding through programmatic SEO.",
      "url": "{{ url('/ads-management') }}",
      "areaServed": {"@type": "Country", "name": "United States"}
    }
  ]
}
</script>

<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;--deep:#0b0b0b;--card:#0e0d09;--border:rgba(200,168,75,.09);
  --gold:#c8a84b;--gold-lt:#e2c97d;--gold-dim:rgba(200,168,75,.4);
  --ivory:#ede8de;--muted:rgba(168,168,160,.72);
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
.page{max-width:860px;margin:0 auto;padding:72px 40px 100px}
.page-eye{font-size:.62rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:18px}
.page-title{font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem,5vw,4rem);font-weight:300;line-height:1.05;margin-bottom:20px;color:var(--ivory)}
.page-title em{font-style:italic;color:var(--gold-lt)}
.page-intro{font-size:.94rem;color:var(--muted);max-width:600px;line-height:1.8;margin-bottom:56px}
.features{display:grid;gap:0;margin-bottom:48px}
.feature{padding:32px 0;border-top:1px solid var(--border)}
.feature:last-child{border-bottom:1px solid var(--border)}
.feat-label{font-size:.58rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:8px}
.feat-hed{font-family:'Cormorant Garamond',serif;font-size:1.42rem;font-weight:400;color:var(--ivory);margin-bottom:10px}
.feat-copy{font-size:.88rem;color:var(--muted);line-height:1.8}
.feat-copy strong{color:var(--ivory);font-weight:400}
.page-cta{margin-top:64px;padding:40px;border:1px solid rgba(200,168,75,.14);background:var(--card);text-align:center}
.page-cta p{font-size:.88rem;color:var(--muted);margin-bottom:24px;line-height:1.7}
.cta-btn{display:inline-flex;align-items:center;background:var(--gold);color:var(--deep);font-size:.78rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;text-decoration:none;padding:15px 36px;transition:background .25s}
.cta-btn:hover{background:var(--gold-lt)}
.cta-ghost{display:inline-block;margin-top:14px;font-size:.74rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .25s}
.cta-ghost:hover{color:var(--gold)}
.related{margin-top:56px;padding-top:32px;border-top:1px solid var(--border)}
.related-label{font-size:.62rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);margin-bottom:20px;display:block}
.related-links{display:flex;flex-wrap:wrap;gap:12px}
.related-links a{font-size:.8rem;letter-spacing:.06em;color:var(--muted);text-decoration:none;border:1px solid var(--border);padding:8px 18px;transition:color .25s,border-color .25s}
.related-links a:hover{color:var(--gold);border-color:rgba(200,168,75,.32)}
.ai-note{margin-top:48px;padding:24px 28px;border-left:2px solid rgba(200,168,75,.18);font-size:.82rem;color:rgba(168,168,160,.52);line-height:1.82}
.ai-note strong{color:rgba(237,232,222,.52);font-weight:400}
footer{border-top:1px solid var(--border);padding:32px 64px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px}
.footer-copy{font-size:.68rem;color:rgba(168,168,160,.28);letter-spacing:.06em}
.footer-links{display:flex;gap:24px}
.footer-links a{font-size:.7rem;letter-spacing:.08em;color:rgba(168,168,160,.38);text-decoration:none;transition:color .25s}
.footer-links a:hover{color:var(--gold)}
@media(max-width:700px){
  .top-bar{padding:22px 24px}
  .page{padding:48px 24px 72px}
  footer{padding:28px 24px;flex-direction:column;align-items:flex-start}
}
</style>
@include('partials.clarity')
</head>
<body>

<div class="top-bar">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <a href="/growth-services" class="back">← Growth Services</a>
</div>

<main class="page">

  <span class="page-eye">Paid Advertising</span>
  <h1 class="page-title">Campaigns that<br><em>reinforce your position.</em></h1>
  <p class="page-intro">Paid advertising is most effective when your organic foundation is already growing. We manage search and local campaigns designed to capture demand now — while your programmatic SEO infrastructure compounds visibility over time. Both working toward the same market.</p>

  <div class="features">

    <div class="feature">
      <span class="feat-label">Google Ads</span>
      <h2 class="feat-hed">Search campaign setup and management.</h2>
      <p class="feat-copy">Google Ads account setup, keyword strategy, ad copy, bid management, and ongoing optimization. <strong>Campaigns structured around your service areas and target markets</strong> — not generic intent targeting.</p>
    </div>

    <div class="feature">
      <span class="feat-label">Local Search Ads</span>
      <h2 class="feat-hed">Local Services Ads and Google Business placement.</h2>
      <p class="feat-copy">Local Services Ads setup for qualifying industries, Google Business optimization, and local pack visibility strategy. <strong>Designed for high-intent local queries</strong> where conversion rates are highest.</p>
    </div>

    <div class="feature">
      <span class="feat-label">Campaign Strategy</span>
      <h2 class="feat-hed">Market-aligned paid media planning.</h2>
      <p class="feat-copy">Campaign strategy built around your specific market expansion goals — not a generic playbook. <strong>Paid and organic work are coordinated</strong> to avoid cannibalization and maximize overall visibility in each territory.</p>
    </div>

    <div class="feature">
      <span class="feat-label">Reporting</span>
      <h2 class="feat-hed">Clear performance tracking and transparent reporting.</h2>
      <p class="feat-copy">Regular reporting on spend, clicks, conversions, and cost-per-lead. <strong>No ambiguous metrics</strong> — results measured against what matters to your market expansion strategy.</p>
    </div>

  </div>

  <div class="page-cta">
    <p>Ads management works best when your organic infrastructure is established. Explore your market availability and see how the full system comes together.</p>
    <a href="{{ route('onboarding.start') }}" class="cta-btn">Check Market Availability</a><br>
    <a href="/#contact" class="cta-ghost">Discuss Ads Management</a>
  </div>

  <div class="related">
    <span class="related-label">Related Services</span>
    <div class="related-links">
      <a href="{{ route('web-design-development') }}">Website Design &amp; Development</a>
      <a href="{{ route('wordpress-support') }}">WordPress Support</a>
      <a href="{{ route('branding-print') }}">Brand &amp; Print</a>
      <a href="{{ route('growth-services') }}">All Services</a>
    </div>
  </div>

  <div class="ai-note">
    <strong>About this service:</strong> SEO AI Co™ provides paid advertising management services for local service businesses. Services include Google Ads setup and management, Local Services Ads, local search campaign strategy, and performance reporting. Advertising management is offered as an execution service alongside the core SEO AI Co™ AI-powered programmatic SEO expansion platform.
  </div>

</main>

<footer>
  <a href="/" class="logo" style="opacity:.5">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; {{ date('Y') }} SEO AI Co™ &middot; Programmatic AI SEO Systems</span>
  <nav class="footer-links">
    <a href="/growth-services">Services</a>
    <a href="/access-plans">Access Plans</a>
    <a href="/how-it-works">How It Works</a>
    <a href="/privacy">Privacy</a>
  </nav>
</footer>

</body>
</html>
