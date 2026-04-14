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
<title>How Programmatic Local SEO Works | SEO AI Co™</title>
<meta name="description" content="SEO AI Co™ builds structured, location-specific pages on your domain — one page per service and city — so your business ranks across its entire local market.">
<link rel="canonical" href="{{ url('/how-it-works') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="SEO AI Co™">
<meta property="og:title" content="How Programmatic Local SEO Works | SEO AI Co™">
<meta property="og:description" content="SEO AI Co™ builds structured, location-specific pages on your domain — one page per service and city — so your business ranks across its entire local market.">
<meta property="og:url" content="{{ url('/how-it-works') }}">
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type'       => 'WebPage',
            '@id'         => url('/how-it-works') . '#webpage',
            'url'         => url('/how-it-works'),
            'name'        => 'How Programmatic Local SEO Works | SEO AI Co™',
            'description' => 'SEO AI Co™ builds structured, location-specific pages on your domain — one page per service and city — so your business ranks across its entire local market.',
            'isPartOf'    => ['@id' => url('/') . '#website'],
            'about' => [
                '@type'    => 'Service',
                'name'     => 'Programmatic Local SEO',
                'provider' => ['@type' => 'Organization', 'name' => 'SEO AI Co™', 'url' => url('/')],
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'How It Works', 'item' => url('/how-it-works')],
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
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

/* ── Reveal animation ── */
@keyframes fadeUp{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}
[data-stagger-child]{opacity:0;transform:translateY(18px);transition:opacity .45s ease,transform .45s ease}
[data-stagger-child].vis{opacity:1;transform:translateY(0)}

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
.page{max-width:920px;margin:0 auto;padding:80px 40px 120px}

/* ── Hero ── */
.page-eye{font-size:.62rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:20px}
.page-title{font-family:'Cormorant Garamond',serif;font-size:clamp(3.2rem,5.5vw,5rem);font-weight:300;line-height:1.04;margin-bottom:28px;color:var(--ivory)}
.page-title em{font-style:italic;color:var(--gold-lt)}
.page-intro{font-size:1.12rem;color:var(--muted);max-width:640px;line-height:1.85;margin-bottom:12px}
.page-intro-note{font-size:.88rem;color:rgba(200,168,75,.60);letter-spacing:.02em;margin-bottom:52px;font-weight:400}

/* ── Positioning line ── */
.page-position{font-size:1.18rem;font-weight:400;color:var(--ivory);max-width:600px;line-height:1.55;margin-bottom:52px;border-left:2px solid rgba(200,168,75,.35);padding-left:18px}

/* ── Top trust strip ── */
.trust-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:rgba(200,168,75,.08);border:1px solid rgba(200,168,75,.14);margin-bottom:72px}
.ts-item{background:rgba(14,13,9,.95);padding:26px 20px 24px;display:flex;flex-direction:column;gap:10px;transition:background .22s}
.ts-item:hover{background:rgba(22,19,11,1)}
.ts-icon-wrap{width:36px;height:36px;border-radius:50%;background:rgba(200,168,75,.07);border:1px solid rgba(200,168,75,.16);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.ts-icon{width:18px;height:18px;color:var(--gold);stroke:currentColor;fill:none}
.ts-label{font-size:.84rem;color:var(--ivory);font-weight:400;line-height:1.35}
.ts-sub{font-size:.74rem;color:var(--muted-lt);line-height:1.55}

/* ── How-strip (4-pillar summary) ── */
.how-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:rgba(200,168,75,.08);border:1px solid rgba(200,168,75,.14);margin-bottom:80px}
.how-pill{background:rgba(14,13,9,.90);padding:36px 22px 32px;display:flex;flex-direction:column;gap:12px;transition:background .22s,transform .22s;cursor:default;position:relative}
.how-pill.how-pill--lead{background:rgba(20,17,10,.98);border-right:1px solid rgba(200,168,75,.18)}
.how-pill::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:rgba(200,168,75,0);transition:background .28s}
.how-pill:hover{background:rgba(22,19,11,1);transform:translateY(-2px)}
.how-pill:hover::after{background:rgba(200,168,75,.40)}
.how-pill-num{font-family:'Cormorant Garamond',serif;font-size:1.9rem;color:rgba(200,168,75,.32);font-weight:300;line-height:1}
.how-pill-label{font-size:.92rem;letter-spacing:.05em;text-transform:uppercase;color:var(--ivory);line-height:1.3;font-weight:400}
.how-pill-sub{font-size:.86rem;color:var(--muted);line-height:1.62}

/* ── Steps ── */
.steps{display:grid;gap:0;margin-bottom:88px}
.step{display:grid;grid-template-columns:64px 1fr;gap:0 36px;padding:52px 0;border-top:1px solid var(--border);position:relative}
.step:last-child{border-bottom:1px solid var(--border)}
.step-num{font-family:'Cormorant Garamond',serif;font-size:3rem;font-weight:300;color:rgba(200,168,75,.15);line-height:1;padding-top:2px;user-select:none;transition:color .3s}
.step:hover .step-num{color:rgba(200,168,75,.30)}
.step-label{font-size:.60rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:12px}
.step-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.9rem,2.8vw,2.4rem);font-weight:400;color:var(--ivory);margin-bottom:14px;line-height:1.16}
.step-copy{font-size:1.00rem;color:var(--muted);line-height:1.82;max-width:600px}
.step-copy strong{color:rgba(237,232,222,.88);font-weight:400}
.step-copy p+p{margin-top:14px}
.step-note{display:inline-block;margin-top:16px;font-size:.76rem;color:rgba(200,168,75,.50);letter-spacing:.04em;font-style:italic}
.step-lead{color:rgba(237,232,222,.88);font-weight:400}
.step-output{color:rgba(200,168,75,.55);font-size:.88rem;letter-spacing:.02em}
.step-link{color:inherit;text-decoration:none;border-bottom:1px solid rgba(200,168,75,.28);transition:border-color .22s,color .22s}
.step-link:hover{color:rgba(200,168,75,.85);border-color:rgba(200,168,75,.60)}

/* ── Search shift block ── */
.shift-block{padding:40px 0 52px;border-bottom:1px solid var(--border);margin-bottom:64px}
.shift-old{font-size:.92rem;color:rgba(168,168,160,.62);line-height:1.75;margin-bottom:20px}
.shift-now{font-size:1.08rem;color:var(--ivory);font-weight:400;line-height:1.65;margin-bottom:12px}
.shift-pain{font-size:.96rem;color:rgba(168,168,160,.75);line-height:1.72;margin-bottom:20px}
.shift-bridge{font-size:.96rem;color:rgba(237,232,222,.82);line-height:1.72;border-left:2px solid rgba(200,168,75,.30);padding-left:16px}
.trust-block{background:var(--card2);border:1px solid rgba(200,168,75,.10);padding:56px 52px;margin-bottom:88px}
.trust-block-eye{font-size:.60rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:14px;text-align:center}
.trust-block-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.5rem,2.2vw,1.9rem);font-weight:300;color:var(--ivory);text-align:center;margin-bottom:10px;line-height:1.2}
.trust-block-sub{font-size:.86rem;color:var(--muted-lt);text-align:center;margin-bottom:44px;line-height:1.75;max-width:520px;margin-left:auto;margin-right:auto}
.trust-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.trust-card{padding:26px 22px;border:1px solid rgba(200,168,75,.07);background:rgba(0,0,0,.20);display:flex;flex-direction:column;gap:12px;transition:transform .22s,border-color .22s,background .22s}
.trust-card:hover{transform:translateY(-3px);border-color:rgba(200,168,75,.20);background:rgba(0,0,0,.34)}
.trust-icon{width:28px;height:28px;color:rgba(200,168,75,.52);flex-shrink:0}
.trust-title{font-size:.88rem;color:var(--ivory);letter-spacing:.02em;font-weight:400}
.trust-desc{font-size:.80rem;color:var(--muted-lt);line-height:1.72}
.trust-list{list-style:none;display:flex;flex-direction:column;gap:6px;margin-top:10px}
.trust-list li{font-size:.78rem;color:var(--muted-lt);line-height:1.60;display:flex;align-items:flex-start;gap:8px}
.trust-list li::before{content:'\2014';color:rgba(200,168,75,.28);flex-shrink:0;font-size:.74rem;margin-top:.1em}
.trust-caveat-wrap{margin-top:40px;padding-top:36px;border-top:1px solid rgba(200,168,75,.06);text-align:center}
.trust-caveat{font-size:.76rem;color:rgba(168,168,160,.36);line-height:1.74;max-width:580px;margin:0 auto;font-style:italic}

/* ── Platform compat block ── */
.compat-block{background:var(--card2);border:1px solid rgba(200,168,75,.10);padding:56px 52px;margin-bottom:88px}
.compat-block-eye{font-size:.60rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:14px;text-align:center}
.compat-block-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.5rem,2.2vw,1.9rem);font-weight:300;color:var(--ivory);text-align:center;margin-bottom:10px;line-height:1.2}
.compat-block-sub{font-size:.86rem;color:var(--muted-lt);text-align:center;margin-bottom:44px;line-height:1.75;max-width:520px;margin-left:auto;margin-right:auto}
.compat-platform-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:36px}
.compat-platform-card{padding:28px 24px;border:1px solid rgba(200,168,75,.07);background:rgba(0,0,0,.20);display:flex;flex-direction:column;gap:14px;transition:border-color .22s,transform .22s,background .22s}
.compat-platform-card:hover{border-color:rgba(200,168,75,.22);transform:translateY(-2px);background:rgba(0,0,0,.32)}
.compat-platform-card--lead{border-color:rgba(200,168,75,.16);background:rgba(12,10,6,.55)}
.compat-platform-card--lead:hover{border-color:rgba(200,168,75,.30)}
.compat-platform-icon{width:36px;height:36px;color:rgba(200,168,75,.55);flex-shrink:0}
.compat-platform-meta{display:flex;flex-direction:column;gap:6px}
.compat-platform-name{font-size:.92rem;color:var(--ivory);font-weight:400;letter-spacing:.01em;line-height:1.3}
.compat-platform-tag{display:inline-block;font-size:.58rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(200,168,75,.80);background:rgba(200,168,75,.07);border:1px solid rgba(200,168,75,.18);padding:3px 9px;align-self:flex-start}
.compat-platform-desc{font-size:.80rem;color:var(--muted-lt);line-height:1.68}
.compat-note-wrap{margin-top:36px;padding-top:32px;border-top:1px solid rgba(200,168,75,.06);text-align:center;display:flex;flex-direction:column;gap:12px}
.compat-support-line{font-size:.82rem;color:var(--muted);line-height:1.76;max-width:520px;margin:0 auto}
.compat-position-line{font-size:.78rem;color:rgba(200,168,75,.46);line-height:1.74;max-width:500px;margin:0 auto;font-style:italic}

/* ── FAQ section ── */
.faq-section{max-width:640px;margin:0 auto;padding:0 24px 72px}
.faq-section-heading{font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;font-size:clamp(1.3rem,2.2vw,1.8rem);color:var(--ivory);letter-spacing:-.01em;margin-bottom:40px;text-align:center}
.faq-list{display:flex;flex-direction:column}
.faq-item{border-top:1px solid rgba(200,168,75,.09);padding:22px 0}
.faq-item:last-child{border-bottom:1px solid rgba(200,168,75,.09)}
.faq-q{font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:400;color:var(--ivory);margin-bottom:9px;line-height:1.5}
.faq-a{font-size:.86rem;line-height:1.78;color:rgba(168,168,160,.68)}

/* ── CTA block ── */
.page-cta{border:1px solid rgba(200,168,75,.14);background:var(--card);padding:60px 52px;text-align:center}
.page-cta-eye{font-size:.60rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:14px}
.page-cta-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.7rem,2.6vw,2.2rem);font-weight:300;color:var(--ivory);margin-bottom:16px;line-height:1.18}
.page-cta-body{font-size:.92rem;color:var(--muted);margin-bottom:34px;line-height:1.78;max-width:440px;margin-left:auto;margin-right:auto}
.cta-btn{
  display:inline-flex;align-items:center;gap:10px;
  background:var(--gold);color:var(--deep);
  font-size:.78rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;
  text-decoration:none;padding:17px 44px;
  transition:background .25s,transform .2s;
}
.cta-btn:hover{background:var(--gold-lt);transform:translateY(-1px)}
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
  .trust-strip{grid-template-columns:repeat(2,1fr)}
  .how-strip{grid-template-columns:repeat(2,1fr)}
  .trust-grid{grid-template-columns:1fr}
  .page-cta{padding:40px 24px}
  .trust-block{padding:40px 24px}
  .compat-block{padding:40px 24px}
  .compat-platform-grid{grid-template-columns:1fr}
  footer{padding:24px;flex-direction:column;text-align:center}
}
@media(max-width:480px){
  .trust-strip{grid-template-columns:1fr}
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

  {{-- ── HERO ── --}}
  <span class="page-eye">The System</span>
  <h1 class="page-title">How <em>SEO AI Co<sup>&trade;</sup></em><br>works.</h1>
  <p class="page-intro">We map how your business actually operates &mdash; then build the structure that reflects how your customers search. Location-specific pages on your domain, across every service and city you serve.</p>
  <p class="page-position">Your website, mapped to your market &mdash; every page placed with purpose.</p>
  <p class="page-intro-note">No site replacement. No separate platform. A system built on your URL &mdash; one you own.</p>
  {{-- ── SEARCH SHIFT ── --}}
  <div class="shift-block">
    <p class="shift-old">SEO used to be about getting one page into Google.</p>
    <p class="shift-now">Now your business has to show up everywhere your customers are searching &mdash; or you get missed.</p>
    <p class="shift-pain">Most websites aren&rsquo;t built for how search works today.</p>
    <p class="shift-bridge">We turn your website into a growth foundation &mdash; built so search engines, AI, and your marketing channels all work from the same source.</p>
  </div>
  {{-- ── TOP TRUST STRIP ── --}}
  <div class="trust-strip" data-stagger>
    <div class="ts-item" data-stagger-child>
      <div class="ts-icon-wrap">
        <svg class="ts-icon" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
          <circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/>
        </svg>
      </div>
      <span class="ts-label">Built on your domain</span>
      <span class="ts-sub">Your site &mdash; not a replacement.</span>
    </div>
    <div class="ts-item" data-stagger-child>
      <div class="ts-icon-wrap">
        <svg class="ts-icon" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
          <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h6M9 12h6M9 15h4"/>
        </svg>
      </div>
      <span class="ts-label">Built into WordPress</span>
      <span class="ts-sub">No migration. No disruption.</span>
    </div>
    <div class="ts-item" data-stagger-child>
      <div class="ts-icon-wrap">
        <svg class="ts-icon" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/>
        </svg>
      </div>
      <span class="ts-label">Custom builds supported</span>
      <span class="ts-sub">Flexible implementation.</span>
    </div>
    <div class="ts-item" data-stagger-child>
      <div class="ts-icon-wrap">
        <svg class="ts-icon" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
          <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/>
        </svg>
      </div>
      <span class="ts-label">Structured rollout</span>
      <span class="ts-sub">Phased, controlled expansion.</span>
    </div>
  </div>

  {{-- ── HOW-STRIP: 4-pillar summary ── --}}
  <div class="how-strip" data-stagger>
    <div class="how-pill how-pill--lead" data-stagger-child>
      <span class="how-pill-num">01</span>
      <span class="how-pill-label">Map</span>
      <span class="how-pill-sub">Every service, city, and gap &mdash; mapped first.</span>
    </div>
    <div class="how-pill" data-stagger-child>
      <span class="how-pill-num">02</span>
      <span class="how-pill-label">Build</span>
      <span class="how-pill-sub">One page per service, per city.</span>
    </div>
    <div class="how-pill" data-stagger-child>
      <span class="how-pill-num">03</span>
      <span class="how-pill-label">Connect</span>
      <span class="how-pill-sub">Links, schema, and signals unified.</span>
    </div>
    <div class="how-pill" data-stagger-child>
      <span class="how-pill-num">04</span>
      <span class="how-pill-label">Expand</span>
      <span class="how-pill-sub">Authority compounds. Position holds.</span>
    </div>
  </div>

  {{-- ── STEPS ── --}}
  <div class="steps" data-stagger>

    <div class="step" data-stagger-child>
      <span class="step-num">01</span>
      <div class="step-body">
        <span class="step-label">Map</span>
        <h2 class="step-hed">Map your market before anything is built.</h2>
        <div class="step-copy">
          <p class="step-lead">Every service. Every city. Every gap.</p>
          <p>We define the full structure first &mdash; so every page has a role, and nothing is wasted.</p>
          <p class="step-output">Output: service coverage, location depth, URL structure, <a href="{{ route('ai-seo-geo-aeo') }}" class="step-link">internal linking</a>.</p>
        </div>
        <span class="step-note">We map how your business operates &mdash; then build the structure that reflects how your market actually searches.</span>
      </div>
    </div>

    <div class="step" data-stagger-child>
      <span class="step-num">02</span>
      <div class="step-body">
        <span class="step-label">Build</span>
        <h2 class="step-hed"><a href="{{ route('growth-services') }}" class="step-link">Structured pages</a> &mdash; built directly on your domain.</h2>
        <div class="step-copy">
          <p class="step-lead">One page. One service. One city.</p>
          <p>Each page is created inside your existing site &mdash; aligned to your brand, your URL, and your authority.</p>
          <p>Content, schema, and search signals are built in from the start.</p>
          <p class="step-lead">Your site expands. Your domain gains authority.</p>
          <p>Your domain. Your structure. Your long-term position.</p>
        </div>
        <span class="step-note">No migration required. Built directly into WordPress &mdash; with the expertise to structure, expand, and scale it correctly.</span>
      </div>
    </div>

    <div class="step" data-stagger-child>
      <span class="step-num">03</span>
      <div class="step-body">
        <span class="step-label">Connect</span>
        <h2 class="step-hed">Every page strengthens the whole.</h2>
        <div class="step-copy">
          <p>Internal links, schema, and search signals work together &mdash; so your site isn&rsquo;t just indexed, it&rsquo;s understood.</p>
          <p class="step-lead">Each page strengthens the next.</p>
          <p>This is where most sites break.</p>
        </div>
        <span class="step-note"><a href="{{ route('ai-seo-geo-aeo') }}" class="step-link">Structured data</a>, canonical signals, and local schema included.</span>
      </div>
    </div>

    <div class="step" data-stagger-child>
      <span class="step-num">04</span>
      <div class="step-body">
        <span class="step-label">Expand</span>
        <h2 class="step-hed">Coverage compounds over time.</h2>
        <div class="step-copy">
          <p>New pages are deployed in a phased rollout &mdash; building authority as coverage grows.</p>
          <p class="step-lead">Earlier pages strengthen later ones.<br>Momentum builds. Position holds.</p>
          <p class="step-output">4-month structured build. Continuous expansion under active management.</p>
        </div>
        <span class="step-note">Phased rollout. Active management. Long-term position.</span>
      </div>
    </div>

  </div>

  {{-- ── TRUST BLOCK ── --}}
  <div class="trust-block">
    <span class="trust-block-eye">How We Work</span>
    <h2 class="trust-block-hed">Built correctly. Built to compound.</h2>
    <p class="trust-block-sub">Built with the signals, structure, and consistency that modern search systems reward.</p>

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
          <circle cx="12" cy="12" r="3"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.343 6.343a8 8 0 000 11.314M17.657 6.343a8 8 0 010 11.314M3.515 3.515a13 13 0 000 16.97M20.485 3.515a13 13 0 010 16.97"/>
        </svg>
        <span class="trust-title">Built for modern search systems</span>
        <p class="trust-desc">We create structured, location-specific pages designed to align with how search engines evaluate and surface content today.</p>
        <ul class="trust-list">
          <li>Clean structure and internal linking</li>
          <li>Consistent signals across your domain</li>
          <li>Ongoing expansion that strengthens coverage over time</li>
        </ul>
      </div>

      <div class="trust-card">
        <svg class="trust-icon" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24" aria-hidden="true">
          <rect x="3" y="16" width="18" height="3" rx="1"/>
          <rect x="3" y="10.5" width="18" height="3" rx="1" opacity=".7"/>
          <rect x="3" y="5" width="18" height="3" rx="1" opacity=".4"/>
        </svg>
        <span class="trust-title">Structured. Consistent. Continuously expanding.</span>
        <p class="trust-desc">This is not a one-time deployment. Your site evolves through phased expansion &mdash; adding new pages, reinforcing signals, and strengthening overall coverage as your market grows.</p>
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
      <p class="trust-caveat">As your footprint grows, your site becomes more complete, more connected, and more visible across your market. Visibility develops over time based on market conditions, competition, and search system behavior.</p>
    </div>
  </div>

  {{-- ── WORDPRESS EXPERTISE ── --}}
  <div class="compat-block">
    <span class="compat-block-eye">Platform</span>
    <h2 class="compat-block-hed">WordPress is our environment &mdash; not our limitation.</h2>
    <p class="compat-block-sub">We integrate directly into your existing WordPress site. No migration. No disruption. Your domain, your structure, your authority &mdash; expanded correctly.</p>

    <div class="compat-platform-grid" style="grid-template-columns:repeat(2,1fr)">

      <div class="compat-platform-card compat-platform-card--lead">
        <svg class="compat-platform-icon" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24" aria-hidden="true">
          <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/>
        </svg>
        <div class="compat-platform-meta">
          <span class="compat-platform-name">Your existing site</span>
          <span class="compat-platform-tag">No migration</span>
        </div>
        <p class="compat-platform-desc">We build directly inside your WordPress installation &mdash; under your URL, within your structure. Your existing pages, design, and content stay exactly as they are.</p>
      </div>

      <div class="compat-platform-card">
        <svg class="compat-platform-icon" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24" aria-hidden="true">
          <rect x="3" y="3" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M3 9h18M9 21V9"/>
        </svg>
        <div class="compat-platform-meta">
          <span class="compat-platform-name">Starting fresh</span>
        </div>
        <p class="compat-platform-desc">For clients without a current site, we build the right WordPress foundation &mdash; architected for structured expansion, performance, and search from day one.</p>
      </div>

    </div>

    <div class="compat-note-wrap">
      <p class="compat-support-line">WordPress expertise applied at the architecture, performance, and search level &mdash; not just installation.</p>
    </div>
  </div>

  @php
  $howFaqs = [
    [
      'question' => 'What is programmatic SEO?',
      'answer'   => 'Programmatic SEO is the systematic creation of location- and service-specific pages built from structured data. Instead of one generic page, your business gets a dedicated page for every city and service combination — scaling search presence across your entire market without manual effort.',
    ],
    [
      'question' => 'Do I need to rebuild my website to use this?',
      'answer'   => 'No. We work directly within your existing website — typically WordPress — deploying structured pages at the URL level of your current domain. No migration, no platform change, no disruption to what\'s already working.',
    ],
    [
      'question' => 'What is the difference between traditional SEO and programmatic SEO?',
      'answer'   => 'Traditional SEO optimizes a handful of existing pages. Programmatic SEO builds new, structured pages at scale — one per service, per city — so your business is visible everywhere your customers search, not just in one or two locations.',
    ],
    [
      'question' => 'How long until I see results from programmatic SEO?',
      'answer'   => 'Most clients begin seeing indexed pages within 4–6 weeks. Meaningful ranking movement typically starts at 60–90 days as pages accumulate authority. Coverage compounds over time — the more markets you are live in, the stronger your overall position becomes.',
    ],
    [
      'question' => 'Does this work for AI search and ChatGPT, not just Google?',
      'answer'   => 'Yes. Structured, location-specific pages are exactly what AI search systems extract when answering local and service queries. By building clear entity relationships — your business, your services, your locations — the content surfaces across Google, AI Overviews, ChatGPT, and Perplexity.',
    ],
  ];
  @endphp
  <x-faq-section heading="Questions about how this works" :faqs="$howFaqs" />

  {{-- ── CTA ── --}}
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

<script>
(function(){
  var containers = document.querySelectorAll('[data-stagger]');
  if (!containers.length) return;

  function revealGroup(container) {
    var children = container.querySelectorAll('[data-stagger-child]');
    children.forEach(function(el, i){
      setTimeout(function(){ el.classList.add('vis'); }, i * 85);
    });
  }

  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(e){
        if (e.isIntersecting) {
          revealGroup(e.target);
          io.unobserve(e.target);
        }
      });
    }, {threshold: 0.07, rootMargin: '0px 0px -40px 0px'});
    containers.forEach(function(c){ io.observe(c); });
  } else {
    containers.forEach(function(c){ revealGroup(c); });
  }
})();
</script>

</body>
</html>
