<!DOCTYPE html>
<html lang="en">
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-LNPGQ0GN69"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-LNPGQ0GN69');
</script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $page->meta_title }}</title>
<meta name="description" content="{{ $page->meta_description }}">
<link rel="canonical" href="{{ $page->canonical_url }}">
@if(!$page->is_indexed)
<meta name="robots" content="noindex,nofollow">
@endif

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $page->canonical_url }}">
<meta property="og:title" content="{{ $page->og_title ?? $page->meta_title }}">
<meta property="og:description" content="{{ $page->og_description ?? $page->meta_description }}">
<meta property="og:site_name" content="SEOAIco">

{{-- JSON-LD Schema --}}
<script type="application/ld+json">{!! json_encode($page->resolved_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;--deep:#0b0b0b;--card:#101010;--border:#1a1a1a;
  --gold:#c8a84b;--gold-lt:#e2c97d;--gold-dim:#9a7a30;
  --white:#ffffff;--ivory:#ede8de;--muted:#a8a8a0;--warn:#b84040;
}
html{scroll-behavior:smooth;font-size:18px}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;overflow-x:hidden;line-height:1.85}
body::after{
  content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  opacity:.022;
}

/* ── Logo ── */
.logo{display:inline-flex;align-items:baseline;text-decoration:none;line-height:1}
.logo-seo{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.2rem;letter-spacing:.06em;color:var(--white)}
.logo-ai{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.42rem;color:var(--gold);letter-spacing:.02em;display:inline-block;transform:skewX(-11deg) translateY(-1px)}
.logo-co{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.05rem;color:rgba(255,255,255,.45);letter-spacing:.04em}

/* ── Nav ── */
nav{position:fixed;top:0;left:0;right:0;z-index:200;display:flex;align-items:center;justify-content:space-between;padding:28px 64px;border-bottom:1px solid transparent;transition:all .4s}
nav.stuck{background:rgba(8,8,8,.95);backdrop-filter:blur(16px);border-color:var(--border);padding:16px 64px}
.nav-right{display:flex;align-items:center;gap:32px}
.nav-link{font-size:.82rem;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.nav-link:hover{color:var(--gold)}
.nav-btn{font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;color:var(--bg);background:var(--gold);padding:12px 28px;text-decoration:none;transition:background .3s;display:inline-flex;align-items:center;white-space:nowrap}
.nav-btn:hover{background:var(--gold-lt)}
.nav-account-short{display:none}

/* ── Shared section helpers ── */
.gold-rule{height:1px;background:linear-gradient(to right,transparent,var(--gold-dim),transparent)}
.s-eye{font-size:.76rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold);margin-bottom:14px;display:flex;align-items:center;gap:14px}
.s-eye::before{content:'';width:28px;height:1px;background:var(--gold)}
.s-h{font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem,4vw,3.6rem);font-weight:400;line-height:1.12;margin-bottom:20px}
.s-h em{font-style:italic;color:var(--gold)}
.s-p{font-size:1.05rem;line-height:1.9;color:var(--muted);max-width:680px}
.s-p strong{color:var(--ivory);font-weight:400}

/* ── Page Hero ── */
#hero{min-height:90vh;display:flex;flex-direction:column;justify-content:center;align-items:flex-start;padding:140px 64px 100px;position:relative;overflow:hidden;max-width:1200px;margin:0 auto}
.hero-grid{position:fixed;inset:0;pointer-events:none;z-index:0;background-image:linear-gradient(rgba(200,168,75,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(200,168,75,.03) 1px,transparent 1px);background-size:88px 88px}
.hero-orb{position:absolute;top:30%;right:-10%;width:600px;height:600px;border-radius:50%;background:radial-gradient(ellipse,rgba(200,168,75,.07) 0%,transparent 65%);pointer-events:none}
.hero-cluster{font-size:.72rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold);margin-bottom:18px;display:flex;align-items:center;gap:14px;opacity:0;animation:up .7s .1s forwards}
.hero-cluster::before{content:'';width:28px;height:1px;background:var(--gold)}
.hero-h1{font-family:'Cormorant Garamond',serif;font-size:clamp(2.8rem,6vw,5.8rem);font-weight:300;line-height:1.08;margin-bottom:28px;max-width:820px;opacity:0;animation:up .9s .3s forwards}
.hero-h1 em{font-style:italic;color:var(--gold)}
.hero-hook{max-width:620px;font-size:1.08rem;line-height:1.9;color:var(--muted);margin-bottom:48px;opacity:0;animation:up .85s .5s forwards}
.hero-actions{display:flex;gap:20px;align-items:center;opacity:0;animation:up .85s .65s forwards}
.btn-primary{background:var(--gold);color:var(--bg);font-size:.82rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;padding:18px 48px;text-decoration:none;transition:background .3s,transform .2s}
.btn-primary:hover{background:var(--gold-lt);transform:translateY(-2px)}
.btn-primary:focus-visible,.btn-ghost:focus-visible{outline:2px solid var(--gold);outline-offset:3px}
.btn-ghost{font-size:.82rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);text-decoration:none;border-bottom:1px solid var(--border);padding-bottom:3px;transition:color .3s,border-color .3s}
.btn-ghost:hover{color:var(--ivory);border-color:var(--muted)}

@keyframes up{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}

/* ── Statement (System Explanation) ── */
.statement{padding:72px 64px;display:grid;grid-template-columns:1fr 1.1fr;gap:64px;align-items:center;max-width:1200px;margin:0 auto}
.stmt-quote{position:relative;padding:48px 56px;border:1px solid var(--border);background:linear-gradient(135deg,rgba(200,168,75,.03) 0%,transparent 60%)}
.stmt-quote::before{content:'';position:absolute;top:0;left:48px;right:48px;height:1px;background:linear-gradient(90deg,transparent,var(--gold-dim),transparent)}
.stmt-quote::after{content:'';position:absolute;bottom:0;left:48px;right:48px;height:1px;background:linear-gradient(90deg,transparent,var(--gold-dim),transparent)}
.sq-mark{display:block;font-family:'Cormorant Garamond',serif;font-size:3.2rem;line-height:1;color:var(--gold-dim);margin-bottom:12px;user-select:none}
.sq-text{font-family:'Cormorant Garamond',serif;font-size:clamp(1.5rem,2.6vw,2.2rem);font-weight:300;font-style:italic;line-height:1.5;color:var(--ivory);letter-spacing:.01em}
.stmt-body p{font-size:1.05rem;line-height:1.95;color:var(--muted);margin-bottom:18px}
.stmt-body p:last-child{margin-bottom:0}
.stmt-body strong{color:var(--ivory);font-weight:400}

/* ── Benefits (WYL-style cards) ── */
.wyl-section{border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:88px 64px;background:var(--deep)}
.wyl-inner{max-width:1200px;margin:0 auto}
.wyl-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:52px}
.wyl-card{background:linear-gradient(160deg,rgba(18,18,16,.98) 0%,rgba(12,12,10,1) 100%);border:1px solid rgba(200,168,75,.1);padding:44px 36px;position:relative;overflow:hidden;transition:transform .45s cubic-bezier(.23,1,.32,1),box-shadow .45s cubic-bezier(.23,1,.32,1),border-color .4s}
.wyl-card:hover{transform:translateY(-5px);box-shadow:0 12px 48px rgba(0,0,0,.55),0 0 0 1px rgba(200,168,75,.2);border-color:rgba(200,168,75,.24)}
.wyl-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.18),transparent)}
.wyl-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--gold-dim),transparent);transform:scaleX(0);transition:transform .55s cubic-bezier(.23,1,.32,1)}
.wyl-card:hover::after{transform:scaleX(1)}
.wyl-num{font-family:'Cormorant Garamond',serif;font-size:2.2rem;font-weight:300;color:rgba(200,168,75,.25);line-height:1;margin-bottom:16px;display:block;transition:color .35s}
.wyl-card:hover .wyl-num{color:rgba(200,168,75,.5)}
.wyl-desc{font-size:.94rem;line-height:2;color:var(--muted)}

/* ── Exclusivity (Positioning Block) ── */
.positioning-block{border-top:1px solid rgba(154,122,48,.18);padding:96px 64px;background:var(--bg)}
.positioning-inner{max-width:760px;margin:0 auto}
.pos-line{display:block;font-size:1rem;font-weight:300;line-height:1.75;color:var(--muted);opacity:.8;margin-bottom:14px}
.pos-line:last-child{margin-bottom:0}
.pos-line.lead{font-family:'Cormorant Garamond',serif;font-size:clamp(1.35rem,2.2vw,1.75rem);font-weight:300;letter-spacing:.02em;color:var(--ivory);opacity:1;margin-bottom:28px}
.pos-line.emphasis{color:var(--gold);opacity:1;font-size:1.02rem;letter-spacing:.01em;margin-top:10px}
.pos-line.strong{color:var(--ivory);opacity:.95;font-size:1.02rem;font-weight:400;margin-top:10px;letter-spacing:.01em}

/* ── Use Cases (Integrity grid) ── */
.integrity-section{border-top:1px solid var(--border);padding:72px 64px;max-width:1200px;margin:0 auto}
.integrity-grid{display:grid;grid-template-columns:1fr 1fr;gap:44px;align-items:start;margin-top:40px}
.integrity-block{padding:36px 32px;border:1px solid var(--border);position:relative;overflow:hidden}
.integrity-block::before{content:'';position:absolute;top:0;left:0;bottom:0;width:2px;background:var(--gold-dim)}
.ib-label{font-size:.7rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);margin-bottom:10px;display:block}
.ib-title{font-family:'Cormorant Garamond',serif;font-size:1.35rem;font-weight:400;margin-bottom:10px;color:var(--ivory)}
.ib-body{font-size:.94rem;line-height:1.9;color:var(--muted)}

/* ── Internal Links (Steps-style grid) ── */
.links-section{background:var(--deep);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.links-wrap{max-width:1200px;margin:0 auto;padding:72px 64px}
.links-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--border);margin-top:40px}
.lk-card{background:var(--deep);padding:36px 28px;position:relative;overflow:hidden;transition:background .4s;text-decoration:none}
.lk-card:hover{background:var(--card)}
.lk-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent);transform:scaleX(0);transition:transform .5s cubic-bezier(.23,1,.32,1)}
.lk-card:hover::after{transform:scaleX(1)}
.lk-arrow{font-size:.82rem;color:var(--gold-dim);margin-bottom:12px;display:block;transition:color .3s}
.lk-card:hover .lk-arrow{color:var(--gold)}
.lk-label{font-size:.7rem;letter-spacing:.18em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;display:block}
.lk-title{font-family:'Cormorant Garamond',serif;font-size:1.18rem;font-weight:400;color:var(--ivory);line-height:1.25}

/* ── CTA Section ── */
.cta-section{border-top:1px solid var(--border);padding:72px 64px;text-align:center;max-width:1200px;margin:0 auto}
.cta-eyebrow{font-size:.76rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold);margin-bottom:24px;display:flex;align-items:center;justify-content:center;gap:14px}
.cta-eyebrow::before,.cta-eyebrow::after{content:'';width:28px;height:1px;background:var(--gold)}
.cta-h{font-family:'Cormorant Garamond',serif;font-size:clamp(2rem,4vw,3.2rem);font-weight:300;line-height:1.15;margin-bottom:20px}
.cta-h em{font-style:italic;color:var(--gold)}
.cta-p{font-size:1.05rem;line-height:1.9;color:var(--muted);max-width:560px;margin:0 auto 40px}
.cta-strip{border-top:1px solid var(--border);border-bottom:1px solid var(--border);background:var(--deep);padding:64px;display:flex;flex-direction:column;align-items:center;gap:20px}

/* ── Footer ── */
footer{border-top:1px solid var(--border);padding:36px 64px;display:flex;flex-direction:column;align-items:center;gap:16px}
.footer-main{display:flex;align-items:center;justify-content:space-between;width:100%}
.footer-copy{font-size:.7rem;letter-spacing:.1em;color:var(--muted)}
.footer-legal{position:static;display:flex;gap:24px;padding:12px 0 0;border-top:1px solid var(--border);width:100%;justify-content:center}
.footer-legal a{font-size:.66rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.footer-legal a:hover{color:var(--gold)}

/* ── Back to Top ── */
.btt{position:fixed;bottom:36px;right:36px;z-index:300;width:48px;height:48px;background:var(--gold);color:var(--bg);border:none;cursor:pointer;font-size:1.1rem;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .35s,transform .35s,background .3s;transform:translateY(12px)}
.btt.show{opacity:1;pointer-events:auto;transform:none}
.btt:hover{background:var(--gold-lt);transform:translateY(-2px)}

/* ── Reveal animation ── */
.r{opacity:0;transform:translateY(32px);transition:opacity .85s cubic-bezier(.23,1,.32,1),transform .85s cubic-bezier(.23,1,.32,1)}
.r.on{opacity:1;transform:none}

/* ── Discovery strip ── */
.discovery-strip{display:grid;grid-template-columns:repeat(5,1fr);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.ds-item{padding:30px 20px;text-align:center;border-right:1px solid var(--border);transition:background .3s}
.ds-item:last-child{border-right:none}
.ds-item:hover{background:rgba(200,168,75,.03)}
.ds-icon{font-size:1.2rem;color:var(--gold);opacity:.65;margin-bottom:10px;transition:opacity .3s}
.ds-item:hover .ds-icon{opacity:1}
.ds-label{font-size:.72rem;letter-spacing:.15em;text-transform:uppercase;color:var(--muted);line-height:1.65}
.ds-label strong{display:block;font-size:.82rem;color:var(--ivory);font-weight:400;letter-spacing:.04em;text-transform:none;margin-bottom:3px}

/* ── AI Discovery block ── */
.ai-discovery{background:var(--deep);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:88px 64px}
.ai-discovery-inner{max-width:1200px;margin:0 auto}
.ai-claim{font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;line-height:1.35;color:var(--ivory);margin-bottom:16px}
.ai-claim.loud{font-size:clamp(2rem,3.8vw,3.2rem);letter-spacing:-.01em;margin-bottom:10px}
.ai-claim.mid{font-size:clamp(1.45rem,2.6vw,2.1rem)}
.ai-claim em{color:var(--gold);font-style:italic}
.ai-channels{display:grid;grid-template-columns:repeat(5,1fr);gap:1px;background:rgba(200,168,75,.08);margin-top:52px}
.ai-channel{background:var(--deep);padding:36px 24px;text-align:center;transition:background .4s}
.ai-channel:hover{background:rgba(200,168,75,.04)}
.ac-icon{font-size:1.3rem;color:var(--gold);opacity:.55;display:block;margin-bottom:14px;transition:opacity .3s,transform .4s cubic-bezier(.23,1,.32,1)}
.ai-channel:hover .ac-icon{opacity:1;transform:translateY(-3px)}
.ac-name{font-family:'Cormorant Garamond',serif;font-size:1.08rem;font-weight:400;color:var(--ivory);margin-bottom:8px;line-height:1.2}
.ac-desc{font-size:.82rem;line-height:1.75;color:var(--muted)}

/* ── Responsive ── */
@media(max-width:1200px){
  nav{padding:20px 36px}nav.stuck{padding:14px 36px}
  .nav-link{font-size:.72rem;letter-spacing:.13em}
  .nav-btn{font-size:.72rem;padding:12px 22px}
  .nav-account-full{display:none}.nav-account-short{display:inline}
}
@media(max-width:900px){
  html{font-size:17px}
  nav{padding:14px 20px}nav.stuck{padding:10px 20px}.nav-link{display:none}.nav-btn{display:none}
  #hero{padding:110px 24px 60px;min-height:auto}
  .hero-h1{font-size:clamp(2.4rem,8vw,3.4rem);max-width:100%}
  .hero-actions{flex-direction:column;gap:16px;width:100%}
  .btn-primary{width:100%;text-align:center;padding:16px 24px}
  .hero-orb{display:none}
  .statement{grid-template-columns:1fr;gap:32px;padding:48px 24px}
  .stmt-quote{padding:32px 24px}
  .stmt-quote::before,.stmt-quote::after{left:24px;right:24px}
  .wyl-section,.integrity-section,.links-wrap{padding:48px 24px}
  .wyl-grid{grid-template-columns:1fr 1fr}
  .links-grid{grid-template-columns:1fr 1fr}
  .integrity-grid{grid-template-columns:1fr}
  .cta-section,.cta-strip{padding:48px 24px}
  .positioning-block{padding:64px 24px}
  .discovery-strip{grid-template-columns:1fr 1fr 1fr}
  .ai-channels{grid-template-columns:1fr 1fr}
  .ai-discovery{padding:56px 24px}
  footer{padding:36px 24px}
  .footer-main{flex-direction:column;gap:12px;text-align:center}
  .s-h{font-size:clamp(1.7rem,6vw,2.4rem)}
  .r{transform:translateY(20px)}
}
@media(max-width:520px){
  html{font-size:16px}
  #hero{padding:100px 20px 48px}
  .hero-h1{font-size:clamp(2.4rem,10vw,3.4rem)}
  .wyl-grid,.links-grid{grid-template-columns:1fr}
  .cta-section,.cta-strip{padding:36px 20px}
  .integrity-block{padding:28px 24px}
  .discovery-strip{grid-template-columns:1fr 1fr}
  .ai-channels{grid-template-columns:1fr}
  .ai-discovery{padding:44px 20px}
}
</style>
</head>
<body>

<!-- ════════════ NAV ════════════ -->
<nav id="nav">
  <a href="{{ url('/') }}" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <div class="nav-right">
    <a href="{{ url('/') }}" class="nav-link">Home</a>
    <a href="{{ url('/#contact') }}" class="nav-btn">Request Access</a>
  </div>
</nav>

<!-- ════════════ HERO ════════════ -->
<section id="hero">
  <div class="hero-grid"></div>
  <div class="hero-orb"></div>

  <div class="hero-cluster">{{ ucfirst($page->cluster) }} · Exclusive Territory</div>

  <h1 class="hero-h1">{{ $page->h1 }}</h1>

  @if($page->hook)
  <p class="hero-hook">{{ $page->hook }}</p>
  @endif

  @if(!empty($page->cta_top['text']))
  <div class="hero-actions">
    <a href="{{ url('/#contact') }}" class="btn-primary">{{ $page->cta_top['text'] }}</a>
    <a href="#explore" class="btn-ghost">See the Architecture</a>
  </div>
  @endif
</section>

<!-- ════════════ DISCOVERY ECOSYSTEM STRIP ════════════ -->
<div class="discovery-strip r">
  <div class="ds-item">
    <div class="ds-icon">◉</div>
    <div class="ds-label"><strong>Organic Search</strong>Rankings &amp; indexing</div>
  </div>
  <div class="ds-item">
    <div class="ds-icon">◈</div>
    <div class="ds-label"><strong>AI Search</strong>Synthesized answers &amp; overviews</div>
  </div>
  <div class="ds-item">
    <div class="ds-icon">◻</div>
    <div class="ds-label"><strong>Language Models</strong>Citations &amp; training signals</div>
  </div>
  <div class="ds-item">
    <div class="ds-icon">⬡</div>
    <div class="ds-label"><strong>Assistants &amp; Agents</strong>Voice, agents &amp; interfaces</div>
  </div>
  <div class="ds-item">
    <div class="ds-icon">◇</div>
    <div class="ds-label"><strong>Emerging Signals</strong>Next-generation layers</div>
  </div>
</div>

<div class="gold-rule"></div>

<!-- ════════════ SYSTEM EXPLANATION ════════════ -->
@if($page->system_explanation)
<div class="statement r" id="explore">
  <div class="stmt-quote">
    <span class="sq-mark">&ldquo;</span>
    <p class="sq-text">{{ $page->system_explanation }}</p>
  </div>
  <div class="stmt-body">
    @foreach(array_filter(explode('. ', $page->system_explanation)) as $sentence)
      @if(strlen(trim($sentence)) > 20)
      <p>{{ trim($sentence) }}{{ str_ends_with(trim($sentence), '.') ? '' : '.' }}</p>
      @endif
    @endforeach
    @if(!empty($page->cta_mid['text']))
    <p style="margin-top:28px">
      <a href="{{ url('/#contact') }}" class="btn-primary" style="display:inline-block;padding:14px 36px">{{ $page->cta_mid['text'] }}</a>
    </p>
    @endif
  </div>
</div>
@endif

<div class="gold-rule"></div>

<!-- ════════════ BENEFITS ════════════ -->
@if(!empty($page->benefits))
<section class="wyl-section">
  <div class="wyl-inner">
    <div class="s-eye r">What This System Delivers</div>
    <h2 class="s-h r">{{ $page->primary_keyword ? 'Built for ' . ucwords($page->primary_keyword) : 'Platform Capabilities' }}</h2>
    <p class="s-p r">{{ $page->internal_linking_section }}</p>
    <div class="wyl-grid r">
      @foreach($page->benefits as $i => $benefit)
      <div class="wyl-card">
        <span class="wyl-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
        <p class="wyl-desc">{{ $benefit }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<div class="gold-rule"></div>

<!-- ════════════ EXCLUSIVITY BLOCK ════════════ -->
@if($page->exclusivity)
<section class="positioning-block">
  <div class="positioning-inner r">
    <div class="s-eye">Territory Control</div>
    <h2 class="s-h" style="margin-bottom:36px">The Exclusivity Agreement</h2>
    @php
      $lines = array_filter(array_map('trim', preg_split('/(?<=[.!?])\s+/', $page->exclusivity)));
      $sentences = array_values($lines);
    @endphp
    @foreach($sentences as $i => $line)
      @if($i === 0)
        <span class="pos-line lead">{{ $line }}</span>
      @elseif($i === count($sentences) - 1)
        <span class="pos-line emphasis">{{ $line }}</span>
      @else
        <span class="pos-line">{{ $line }}</span>
      @endif
    @endforeach
  </div>
</section>
@endif

<div class="gold-rule"></div>

<!-- ════════════ AI DISCOVERY POSITIONING ════════════ -->
<section class="ai-discovery">
  <div class="ai-discovery-inner">
    <div class="s-eye r">Structural Advantage</div>
    <p class="ai-claim loud r">Built to be read by every machine that matters.</p>
    <p class="ai-claim mid r">One page. Every system that <em>determines what gets found.</em></p>

    <p class="s-p r" style="margin-top:20px;max-width:780px">This isn't just about Google. This is positioning across every system that determines visibility — search engines, AI platforms, language models, and interfaces not yet built. Each licensed page is structured to surface wherever intent becomes action.</p>

    <p class="s-p r" style="margin-top:16px;max-width:780px">Exclusivity compounds the advantage. One licensee per category, per territory — meaning the authority signals and discovery presence you establish cannot be replicated by a direct competitor in the same market.</p>

    <p class="s-p r" style="margin-top:20px;max-width:780px"><strong style="color:var(--ivory);font-weight:400">This is built for how information is discovered now — and next.</strong></p>

    <div class="ai-channels r">
      <div class="ai-channel">
        <span class="ac-icon">◉</span>
        <div class="ac-name">Traditional Search</div>
        <div class="ac-desc">Authoritative organic rankings that compound over time.</div>
      </div>
      <div class="ai-channel">
        <span class="ac-icon">◈</span>
        <div class="ac-name">AI Search</div>
        <div class="ac-desc">Cited in synthesized answers and AI overviews.</div>
      </div>
      <div class="ai-channel">
        <span class="ac-icon">◻</span>
        <div class="ac-name">Language Models</div>
        <div class="ac-desc">Referenced in LLM responses and training corpora.</div>
      </div>
      <div class="ai-channel">
        <span class="ac-icon">⬡</span>
        <div class="ac-name">Assistants &amp; Agents</div>
        <div class="ac-desc">Retrieved by agents, voice interfaces, and conversational AI.</div>
      </div>
      <div class="ai-channel">
        <span class="ac-icon">◇</span>
        <div class="ac-name">Emerging Signals</div>
        <div class="ac-desc">Positioned for architectures still being built.</div>
      </div>
    </div>
  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ USE CASES ════════════ -->
@if(!empty($page->use_cases))
<section class="integrity-section">
  <div class="s-eye r">Who This Is For</div>
  <h2 class="s-h r">Use Cases &amp; Applications</h2>
  <div class="integrity-grid r">
    @foreach($page->use_cases as $useCase)
    <div class="integrity-block">
      <span class="ib-label">{{ $useCase['type'] ?? 'Client Type' }}</span>
      <h3 class="ib-title">{{ $useCase['type'] ?? 'Use Case' }}</h3>
      <p class="ib-body">{{ $useCase['description'] ?? '' }}</p>
    </div>
    @endforeach
  </div>
</section>
@endif

<div class="gold-rule"></div>

<!-- ════════════ H2 TOPIC SECTIONS ════════════ -->
@if(!empty($page->h2_structure) && count($page->h2_structure) > 0)
<section style="border-top:1px solid var(--border);padding:72px 64px;max-width:1200px;margin:0 auto">
  <div class="s-eye r">Deep Dive</div>
  <h2 class="s-h r">Inside the System</h2>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:40px">
    @foreach($page->h2_structure as $h2)
    <div class="r" style="padding:28px 32px;border:1px solid var(--border);position:relative;overflow:hidden">
      <span style="position:absolute;top:0;left:0;bottom:0;width:2px;background:var(--gold-dim)"></span>
      <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:400;color:var(--ivory);line-height:1.3">{{ $h2 }}</h3>
    </div>
    @endforeach
  </div>
</section>
@endif

<div class="gold-rule"></div>

<!-- ════════════ INTERNAL LINKS ════════════ -->
@if($related->isNotEmpty() || $moneyPages->isNotEmpty())
<section class="links-section">
  <div class="links-wrap">
    <div class="s-eye r">Explore the Platform</div>
    <h2 class="s-h r">Related <em>Systems</em></h2>
    <div class="links-grid r">
      @foreach($related->take(4) as $rel)
      <a href="{{ url('/'.$rel->url_slug) }}" class="lk-card">
        <span class="lk-arrow">→</span>
        <span class="lk-label">{{ ucfirst($rel->cluster) }}</span>
        <span class="lk-title">{{ $rel->nav_label ?? $rel->primary_keyword }}</span>
      </a>
      @endforeach
      @foreach($moneyPages->take(max(0, 4 - $related->count())) as $mp)
      <a href="{{ url('/'.$mp->url_slug) }}" class="lk-card">
        <span class="lk-arrow">◈</span>
        <span class="lk-label">Core System</span>
        <span class="lk-title">{{ $mp->nav_label ?? $mp->primary_keyword }}</span>
      </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<div class="gold-rule"></div>

<!-- ════════════ CTA BOTTOM ════════════ -->
<div class="cta-strip r">
  <div class="cta-eyebrow">License Access</div>
  <h2 class="s-h" style="text-align:center">
    @if(!empty($page->cta_bottom['text']))
      {{ $page->cta_bottom['text'] }}
    @else
      Lock Your Territory Before a Competitor Does
    @endif
  </h2>
  <p style="font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;font-size:clamp(1.05rem,1.8vw,1.35rem);color:var(--gold);text-align:center;letter-spacing:.01em;margin-bottom:8px">The system is engineered to secure position—not chase it. We don't compete. We own the position.</p>
  <p style="font-size:1.05rem;color:var(--muted);max-width:560px;text-align:center;line-height:1.9">
    One licensee per category, per territory. Once a market is secured under agreement, it is no longer available to competing operators. Position is held while the licence is active.
  </p>
  <p style="font-size:.82rem;color:var(--muted);text-align:center;letter-spacing:.07em;text-transform:uppercase;margin-top:4px">Availability confirmed individually — not all territories remain open.</p>
  <a href="{{ url('/#contact') }}" class="btn-primary">Check Market Availability</a>
</div>

<!-- ════════════ FOOTER ════════════ -->
<footer>
  <div class="footer-main">
    <a href="{{ url('/') }}" class="logo">
      <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
    </a>
    <span class="footer-copy">&copy; {{ date('Y') }} SEOAIco. Licensed Ranking Infrastructure.</span>
  </div>
  <nav class="footer-legal">
    <a href="{{ route('privacy') }}">Privacy</a>
    <a href="{{ route('terms') }}">Terms</a>
  </nav>
</footer>

<button class="btt" id="btt" aria-label="Back to top">
  <svg viewBox="0 0 24 24"><path d="M12 4l-8 8h5v8h6v-8h5z"/></svg>
</button>

<script>
  // Nav scroll
  const nav = document.getElementById('nav');
  window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60), {passive:true});

  // Reveal on scroll
  const items = document.querySelectorAll('.r');
  const io = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
      if (e.isIntersecting) {
        setTimeout(() => e.target.classList.add('on'), i * 55);
        io.unobserve(e.target);
      }
    });
  }, {threshold: .1});
  items.forEach(el => io.observe(el));

  // Back to top
  const btt = document.getElementById('btt');
  window.addEventListener('scroll', () => btt.classList.toggle('show', scrollY > 600), {passive:true});
  btt.addEventListener('click', () => window.scrollTo({top: 0, behavior: 'smooth'}));
</script>

</body>
</html>
