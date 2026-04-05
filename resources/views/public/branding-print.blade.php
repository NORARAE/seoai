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
<title>Branding &amp; Print — SEO AI Co™</title>
<meta name="description" content="Brand identity systems, business cards, marketing collateral, and print production from SEO AI Co™ — aligned with your market expansion strategy.">
<link rel="canonical" href="{{ url('/branding-print') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

<!-- Schema: Service + WebPage + BreadcrumbList -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "name": "SEO AI Co",
      "url": "{{ url('/') }}"
    },
    {
      "@type": "WebPage",
      "name": "Branding & Print — SEO AI Co™",
      "url": "{{ url('/branding-print') }}",
      "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
          {"@type":"ListItem","position":1,"name":"Home","item":"{{ url('/') }}"},
          {"@type":"ListItem","position":2,"name":"Growth Services","item":"{{ url('/growth-services') }}"},
          {"@type":"ListItem","position":3,"name":"Branding & Print","item":"{{ url('/branding-print') }}"}
        ]
      }
    },
    {
      "@type": "Service",
      "name": "Brand Identity and Print Production",
      "provider": {"@type": "Organization", "name": "SEO AI Co"},
      "serviceType": "Brand Identity and Print Design",
      "description": "Brand identity systems, logo design, business cards, marketing collateral, and print production for local service businesses expanding through programmatic SEO.",
      "url": "{{ url('/branding-print') }}",
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

  <span class="page-eye">Brand &amp; Print</span>
  <h1 class="page-title">Identity built<br><em>for your market.</em></h1>
  <p class="page-intro">A brand system should be consistent across everything your business produces — online and offline. We build and maintain brand identity systems and print collateral that align with your market positioning and expansion strategy — not generic templates.</p>

  <div class="features">

    <div class="feature">
      <span class="feat-label">Brand Identity</span>
      <h2 class="feat-hed">Brand systems and visual identity design.</h2>
      <p class="feat-copy">Logo design, color systems, typography, and brand guidelines. <strong>Built for consistency across every touchpoint</strong> — digital, print, signage, and delivered materials. Not a kit. A system.</p>
    </div>

    <div class="feature">
      <span class="feat-label">Business Cards &amp; Collateral</span>
      <h2 class="feat-hed">Professional print materials that represent your brand.</h2>
      <p class="feat-copy">Business cards, brochures, flyers, rack cards, and presentation folders. <strong>Designed for your brand standards</strong> — press-ready files handled from concept through print production.</p>
    </div>

    <div class="feature">
      <span class="feat-label">Signage &amp; Vehicle</span>
      <h2 class="feat-hed">Physical presence across your territory.</h2>
      <p class="feat-copy">Signage design, vehicle wrap layouts, and exterior brand applications. <strong>Your physical market presence should match your digital expansion</strong> — consistent identity across every channel reinforces recognition.</p>
    </div>

    <div class="feature">
      <span class="feat-label">Brand Refresh</span>
      <h2 class="feat-hed">Existing brand refinement and modernization.</h2>
      <p class="feat-copy">Logo refinement, color and typography updates, and brand standard documentation. <strong>Not a rebrand — a strategic refinement</strong> that aligns your existing identity with current standards and your growth direction.</p>
    </div>

  </div>

  <div class="page-cta">
    <p>Brand systems are most effective when aligned with your market strategy. Check your market availability to see how the full system is structured.</p>
    <a href="{{ route('onboarding.start') }}" class="cta-btn">Check Market Availability</a><br>
    <a href="/#contact" class="cta-ghost">Discuss Brand &amp; Print</a>
  </div>

  <div class="related">
    <span class="related-label">Related Services</span>
    <div class="related-links">
      <a href="{{ route('web-design-development') }}">Website Design &amp; Development</a>
      <a href="{{ route('ads-management') }}">Ads Management</a>
      <a href="{{ route('growth-services') }}">All Services</a>
    </div>
  </div>

  <div class="ai-note">
    <strong>About this service:</strong> SEO AI Co™ provides brand identity and print production services for local service businesses. Services include logo design, brand system development, business card and collateral design, signage design, vehicle wrap layouts, and brand refresh work. Brand and print services are offered as execution services alongside the core SEO AI Co™ AI-powered programmatic SEO expansion platform.
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
