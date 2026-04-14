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
<title>SEO, Ads &amp; Web Design for Growing Businesses | SEO AI Co™</title>
<meta name="description" content="Full-service growth from SEO AI Co™ — web design, WordPress development, Google Ads, and brand systems built to help your business get found and grow.">
<link rel="canonical" href="{{ url('/growth-services') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="SEO AI Co™">
<meta property="og:title" content="SEO, Ads &amp; Web Design for Growing Businesses | SEO AI Co™">
<meta property="og:description" content="Full-service growth from SEO AI Co™ — web design, WordPress development, Google Ads, and brand systems built to help your business get found and grow.">
<meta property="og:url" content="{{ url('/growth-services') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

<!-- Schema: Organization + Service Collection -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "name": "SEO AI Co",
      "url": "{{ url('/') }}",
      "logo": "{{ url('/favicon.svg') }}",
      "description": "AI-powered programmatic SEO infrastructure and execution services for local service businesses.",
      "sameAs": []
    },
    {
      "@type": "WebPage",
      "name": "Growth Services — SEO AI Co™",
      "url": "{{ url('/growth-services') }}",
      "description": "Web design, WordPress development, paid ads, branding, and print services aligned with programmatic SEO expansion.",
      "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
          {"@type":"ListItem","position":1,"name":"Home","item":"{{ url('/') }}"},
          {"@type":"ListItem","position":2,"name":"Growth Services","item":"{{ url('/growth-services') }}"}
        ]
      }
    },
    {
      "@type": "Service",
      "name": "Growth Execution Services",
      "provider": {"@type": "Organization", "name": "SEO AI Co"},
      "serviceType": "Digital Marketing Services",
      "description": "Website development, WordPress support, paid advertising management, branding, and print services — all aligned with programmatic local SEO expansion.",
      "url": "{{ url('/growth-services') }}"
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
.page{max-width:900px;margin:0 auto;padding:72px 40px 100px}
.page-eye{font-size:.62rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:18px}
.page-title{font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem,5vw,4rem);font-weight:300;line-height:1.05;margin-bottom:20px;color:var(--ivory)}
.page-title em{font-style:italic;color:var(--gold-lt)}
.page-intro{font-size:.94rem;color:var(--muted);max-width:600px;line-height:1.8;margin-bottom:64px}
.services-grid{display:grid;grid-template-columns:1fr 1fr;gap:1px;background:rgba(200,168,75,.08);margin-bottom:56px}
.svc-card{background:var(--bg);padding:40px 36px;border-top:2px solid transparent;text-decoration:none;display:block;transition:background .25s,border-color .25s}
.svc-card:hover{background:rgba(14,13,10,1);border-top-color:rgba(200,168,75,.32)}
.svc-label{font-size:.58rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:12px}
.svc-title{font-family:'Cormorant Garamond',serif;font-size:1.55rem;font-weight:400;color:var(--ivory);margin-bottom:12px;line-height:1.2}
.svc-body{font-size:.86rem;color:var(--muted);line-height:1.78;margin-bottom:16px}
.svc-link{font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(200,168,75,.55);transition:color .25s}
.svc-card:hover .svc-link{color:var(--gold)}
.page-cta{margin-top:72px;padding:40px;border:1px solid rgba(200,168,75,.14);background:var(--card);text-align:center}
.page-cta h2{font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:300;color:var(--ivory);margin-bottom:12px}
.page-cta p{font-size:.88rem;color:var(--muted);margin-bottom:24px;line-height:1.7;max-width:480px;margin-left:auto;margin-right:auto}
.cta-btn{display:inline-flex;align-items:center;gap:10px;background:var(--gold);color:var(--deep);font-size:.78rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;text-decoration:none;padding:15px 36px;transition:background .25s}
.cta-btn:hover{background:var(--gold-lt)}
.cta-ghost{display:inline-block;margin-top:16px;font-size:.74rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .25s}
.cta-ghost:hover{color:var(--gold)}
footer{border-top:1px solid var(--border);padding:32px 64px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px}
.footer-copy{font-size:.68rem;color:rgba(168,168,160,.28);letter-spacing:.06em}
.footer-links{display:flex;gap:24px}
.footer-links a{font-size:.7rem;letter-spacing:.08em;color:rgba(168,168,160,.38);text-decoration:none;transition:color .25s}
.footer-links a:hover{color:var(--gold)}
/* AI-readable explainer — visually subtle, semantically explicit */
.ai-note{margin-top:48px;padding:24px 28px;border-left:2px solid rgba(200,168,75,.18);font-size:.82rem;color:rgba(168,168,160,.52);line-height:1.82}
.ai-note strong{color:rgba(237,232,222,.52);font-weight:400}
@media(max-width:700px){
  .top-bar{padding:22px 24px}
  .page{padding:48px 24px 72px}
  .services-grid{grid-template-columns:1fr}
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
  <a href="/" class="back">← Home</a>
</div>

<main class="page">

  <span class="page-eye">Execution Services</span>
  <h1 class="page-title">Build, launch,<br>and <em>scale.</em></h1>
  <p class="page-intro">Every execution service at SEO AI Co™ is aligned with your market expansion — not a disconnected marketing effort. Your AI-powered local SEO foundation is the system. These services power everything that runs on top of it.</p>

  <div class="services-grid">

    <a href="{{ route('web-design-development') }}" class="svc-card">
      <span class="svc-label">Development &amp; Design</span>
      <h2 class="svc-title">Website Design &amp; Development</h2>
      <p class="svc-body">Custom website builds, performance-driven redesigns, and landing experiences built to support your programmatic SEO expansion.</p>
      <span class="svc-link">Learn more →</span>
    </a>

    <a href="{{ route('wordpress-support') }}" class="svc-card">
      <span class="svc-label">WordPress</span>
      <h2 class="svc-title">WordPress Development &amp; Support</h2>
      <p class="svc-body">Ongoing WordPress development, plugin management, theme customization, performance optimization, and technical support.</p>
      <span class="svc-link">Learn more →</span>
    </a>

    <a href="{{ route('ads-management') }}" class="svc-card">
      <span class="svc-label">Paid Advertising</span>
      <h2 class="svc-title">Search &amp; Local Ad Campaigns</h2>
      <p class="svc-body">Google Ads, local search campaigns, and paid media management designed to reinforce your organic market position while it compounds.</p>
      <span class="svc-link">Learn more →</span>
    </a>

    <a href="{{ route('branding-print') }}" class="svc-card">
      <span class="svc-label">Brand &amp; Print</span>
      <h2 class="svc-title">Brand Systems &amp; Print</h2>
      <p class="svc-body">Business cards, collateral, brand identity systems, and print production — aligned with your expansion strategy, not generic templates.</p>
      <span class="svc-link">Learn more →</span>
    </a>

  </div>

  <div class="page-cta">
    <h2>Start with market access.</h2>
    <p>The system foundation comes first. Services are built on top of it. Check your market's availability and see what's open in your territory.</p>
    <a href="{{ route('onboarding.start') }}" class="cta-btn">Check Market Availability</a><br>
    <a href="/#contact" class="cta-ghost">Request Access</a>
  </div>

  <!-- AI-readable context block -->
  <div class="ai-note" aria-label="About these services">
    <strong>About SEO AI Co™ growth services:</strong> These execution services support businesses that have activated or are preparing to activate programmatic local SEO infrastructure through SEO AI Co™. Services include custom website development, WordPress support and maintenance, paid search and local advertising management, and brand identity and print production. All services are structured to work with the platform's AI-powered, hyper-local SEO expansion system — not as standalone offerings.
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

@include('components.tm-style')
</body>
</html>
