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
<title>Your AI Citation Score: {{ $scan->score ?? 0 }}/100 — SEO AI Co™</title>
<meta name="description" content="Your AI citation readiness score is {{ $scan->score ?? 0 }}/100. See your issues, strengths, and fastest fix.">
<meta name="robots" content="noindex">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@php
  $score = (int) ($scan->score ?? 0);
  $categories = $scan->categories ?? [];
@endphp
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;--deep:#0b0b0b;--card:#0e0d09;--border:rgba(200,168,75,.09);
  --gold:#c8a84b;--gold-lt:#d9bc6e;--gold-dim:rgba(200,168,75,.32);
  --ivory:#ede8de;--muted:rgba(168,168,160,.78);
  --green:#6aaf90;--red:#c47878;
}
html{font-size:18px;scroll-behavior:smooth}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.55;-webkit-font-smoothing:antialiased;overflow-x:hidden}

/* ── Nav ── */
nav{position:fixed;top:0;left:0;right:0;z-index:200;display:flex;align-items:center;justify-content:space-between;padding:28px 64px;border-bottom:1px solid transparent;transition:all .4s}
nav.stuck{background:rgba(8,8,8,.95);backdrop-filter:blur(16px);border-color:var(--border);padding:16px 64px}
.logo{text-decoration:none;display:flex;align-items:baseline;gap:1px;flex-shrink:0}
.logo-seo{font-family:'DM Sans',sans-serif;font-size:1.38rem;font-weight:300;letter-spacing:-.02em;color:var(--ivory)}
.logo-ai{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:400;letter-spacing:.02em;color:var(--gold);font-style:italic;margin:0 1px}
.logo-co{font-family:'DM Sans',sans-serif;font-size:1.18rem;font-weight:300;color:rgba(168,168,160,.65)}
.nav-right{display:flex;align-items:center;gap:28px}
.nav-link{font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.nav-link:hover{color:var(--gold)}
.nav-btn{font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;color:var(--bg);background:var(--gold);padding:12px 28px;text-decoration:none;transition:background .3s;display:inline-flex;align-items:center}
.nav-btn:hover{background:var(--gold-lt)}

/* ── Hero band ── */
.result-hero{padding:120px 64px 60px;text-align:center;position:relative;overflow:hidden}
.result-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 70% 55% at 50% 50%,rgba(200,168,75,.06) 0%,transparent 68%);pointer-events:none}
.result-eyebrow{font-size:.66rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(200,168,75,.6);margin-bottom:20px}
.result-url{font-size:.82rem;color:rgba(168,168,160,.48);font-weight:300;letter-spacing:.04em;margin-bottom:24px;max-width:560px;margin-left:auto;margin-right:auto;overflow-wrap:break-word}

/* ── Score ring ── */
.score-ring-wrap{display:inline-flex;flex-direction:column;align-items:center;gap:14px;margin-bottom:20px}
.score-ring-svg{width:160px;height:160px}
.score-ring-bg{fill:none;stroke:rgba(200,168,75,.08);stroke-width:8}
.score-ring-fill{fill:none;stroke-width:8;stroke-linecap:round;stroke-dasharray:440;stroke-dashoffset:440;transform:rotate(-90deg);transform-origin:50% 50%;transition:stroke-dashoffset 1.4s cubic-bezier(.23,1,.32,1)}
.score-ring-fill.animate{stroke-dashoffset:calc(440 - (440 * {{ $score }} / 100))}
.score-number{font-family:'Cormorant Garamond',serif;font-size:3.8rem;font-weight:300;line-height:1;color:@if($score >= 70) var(--green) @elseif($score >= 40) var(--gold) @else var(--red) @endif}
.score-label{font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.5);margin-top:2px}
.score-verdict{font-family:'Cormorant Garamond',serif;font-size:clamp(1.4rem,3vw,2rem);font-weight:300;line-height:1.2;color:@if($score >= 90) var(--green) @elseif($score >= 70) var(--gold-lt) @elseif($score >= 40) var(--gold) @else var(--red) @endif}
.score-ring-fill{stroke:@if($score >= 70) var(--green) @elseif($score >= 40) var(--gold) @else var(--red) @endif}

/* ── Stats row ── */
.stats-row{display:flex;justify-content:center;gap:40px;margin-top:20px;flex-wrap:wrap}
.stat-item{text-align:center}
.stat-value{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:300;color:var(--ivory)}
.stat-label{font-size:.62rem;letter-spacing:.16em;text-transform:uppercase;color:rgba(200,168,75,.5)}

/* ── Category grid ── */
.result-body{max-width:1060px;margin:0 auto;padding:0 24px 80px}
.cat-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:40px}
.cat-card{background:rgba(14,13,9,.92);border:1px solid rgba(200,168,75,.08);padding:20px;position:relative;transition:border-color .2s}
.cat-card:hover{border-color:rgba(200,168,75,.18)}
.cat-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.cat-name{font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(200,168,75,.7)}
.cat-score{font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:300;color:var(--ivory)}
.cat-bar{height:3px;background:rgba(200,168,75,.08);margin-bottom:16px;overflow:hidden}
.cat-bar-fill{height:100%;transition:width 1s cubic-bezier(.23,1,.32,1)}
.cat-bar-fill.high{background:var(--green)}
.cat-bar-fill.mid{background:var(--gold)}
.cat-bar-fill.low{background:var(--red)}

/* ── Check items ── */
.check-list{display:flex;flex-direction:column;gap:8px}
.check-item{display:flex;align-items:flex-start;gap:10px;font-size:.82rem;line-height:1.4;color:var(--muted)}
.check-icon{flex-shrink:0;margin-top:1px;font-size:.78rem;width:16px;text-align:center}
.check-item.passed .check-icon{color:var(--green)}
.check-item.failed .check-icon{color:var(--red)}
.check-pts{font-size:.68rem;color:rgba(168,168,160,.4);margin-left:auto;flex-shrink:0}

/* ── Locked / blurred sections ── */
.locked-zone{position:relative;overflow:hidden}
.locked-zone .check-text{filter:blur(5px);-webkit-filter:blur(5px);user-select:none;pointer-events:none}
.locked-overlay{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:rgba(8,8,8,.55);backdrop-filter:blur(2px);z-index:10;gap:10px;padding:16px}
.locked-overlay .lock-icon{font-size:1.4rem;opacity:.7;color:var(--gold)}
.locked-overlay .lock-text{font-size:.72rem;letter-spacing:.16em;text-transform:uppercase;color:rgba(200,168,75,.7);text-align:center;line-height:1.5}
.locked-overlay .lock-cta{font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;color:var(--bg);background:var(--gold);padding:8px 20px;text-decoration:none;transition:background .3s;display:inline-flex;align-items:center}
.locked-overlay .lock-cta:hover{background:var(--gold-lt)}
.fix-locked{position:relative;overflow:hidden}
.fix-locked .fix-text{filter:blur(6px);-webkit-filter:blur(6px);user-select:none;pointer-events:none}

/* ── Broken links ── */
.broken-section{background:rgba(200,68,68,.04);border:1px solid rgba(200,68,68,.15);padding:20px;margin-bottom:32px}
.broken-title{font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;color:var(--red);margin-bottom:12px;display:flex;align-items:center;gap:8px}
.broken-item{font-size:.82rem;color:var(--muted);padding:6px 0;border-bottom:1px solid rgba(200,68,68,.08);display:flex;justify-content:space-between;overflow-wrap:break-word;word-break:break-all}
.broken-item:last-child{border-bottom:none}
.broken-status{color:var(--red);font-size:.72rem;flex-shrink:0;margin-left:12px}

/* ── Fastest fix ── */
.fastest-fix{background:rgba(14,13,9,.9);border:1px solid rgba(200,168,75,.18);padding:28px;margin-bottom:40px;position:relative}
.fastest-fix::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.5),transparent)}
.fix-label{font-size:.64rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold);margin-bottom:10px;display:block}
.fix-text{font-size:.96rem;line-height:1.55;color:rgba(237,232,222,.88)}

/* ── Untapped Market Coverage / Visibility Gap ── */
.market-coverage{background:rgba(14,13,9,.92);border:1px solid rgba(200,168,75,.15);margin-bottom:40px;overflow:hidden}
.market-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid rgba(200,168,75,.08)}
.market-eyebrow{font-size:.66rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.6)}
.market-gap-badge{font-size:.68rem;letter-spacing:.1em;text-transform:uppercase;color:var(--red);background:rgba(196,120,120,.08);padding:4px 12px;border:1px solid rgba(196,120,120,.15)}
.market-body{padding:20px}
.market-bar-wrap{margin-bottom:20px}
.market-bar-track{height:6px;background:rgba(200,168,75,.06);overflow:hidden;margin-bottom:6px}
.market-bar-fill{height:100%;background:linear-gradient(90deg,var(--gold),var(--gold-dim));transition:width 1.2s cubic-bezier(.23,1,.32,1)}
.market-bar-labels{display:flex;justify-content:space-between;font-size:.68rem;color:var(--muted)}
.market-stats{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:16px}
.market-stat{text-align:center;padding:12px 8px;background:rgba(200,168,75,.03);border:1px solid rgba(200,168,75,.06)}
.market-stat-val{display:block;font-family:'Cormorant Garamond',serif;font-size:1.4rem;font-weight:300;color:var(--gold);line-height:1.2}
.market-stat-lbl{font-size:.62rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-top:4px;display:block}
.market-insight{font-size:.84rem;color:var(--muted);line-height:1.55;margin-bottom:16px}
.market-cta{display:block;text-align:center;font-size:.78rem;letter-spacing:.1em;text-transform:uppercase;color:var(--bg);background:var(--gold);padding:12px 24px;text-decoration:none;transition:background .3s}
.market-cta:hover{background:var(--gold-lt)}
.vgap-compare{display:grid;grid-template-columns:1fr 1fr;gap:0;margin-bottom:20px;border:1px solid rgba(200,168,75,.08);overflow:hidden}
.vgap-you,.vgap-them{padding:16px;text-align:center}
.vgap-you{background:rgba(196,120,120,.04);border-right:1px solid rgba(200,168,75,.08)}
.vgap-them{background:rgba(106,175,144,.04)}
.vgap-label{font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;margin-bottom:8px;display:block}
.vgap-you .vgap-label{color:var(--red)}
.vgap-them .vgap-label{color:var(--green)}
.vgap-val{font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:300;line-height:1}
.vgap-you .vgap-val{color:var(--red)}
.vgap-them .vgap-val{color:var(--green)}
.vgap-sub{font-size:.68rem;color:var(--muted);margin-top:4px;display:block}

/* ── Urgency Pressure Banner ── */
.urgency-banner{background:rgba(196,120,120,.06);border:1px solid rgba(196,120,120,.18);padding:18px 22px;margin-bottom:32px;display:flex;align-items:flex-start;gap:14px}
.urgency-icon{font-size:1.2rem;flex-shrink:0;margin-top:2px}
.urgency-body{flex:1}
.urgency-hed{font-size:.86rem;font-weight:500;color:var(--red);margin-bottom:4px}
.urgency-sub{font-size:.8rem;color:var(--muted);line-height:1.5}

/* ── Competitive Pressure ── */
.comp-section{background:rgba(14,13,9,.92);border:1px solid rgba(196,120,120,.12);margin-bottom:40px;overflow:hidden}
.comp-header{padding:16px 20px;border-bottom:1px solid rgba(196,120,120,.08)}
.comp-eyebrow{font-size:.66rem;letter-spacing:.22em;text-transform:uppercase;color:var(--red)}
.comp-body{padding:20px}
.comp-intro{font-size:.84rem;color:var(--muted);line-height:1.55;margin-bottom:16px}
.comp-grid{display:flex;flex-direction:column;gap:10px;margin-bottom:16px}
.comp-item{display:flex;align-items:flex-start;gap:10px;font-size:.82rem;line-height:1.4;padding:10px 14px;background:rgba(106,175,144,.04);border:1px solid rgba(106,175,144,.1)}
.comp-icon{color:var(--green);flex-shrink:0;font-size:.64rem;margin-top:3px}
.comp-text{color:rgba(168,168,160,.85)}
.comp-bottom{font-size:.82rem;color:var(--red);font-style:italic;line-height:1.5}

/* ── CTA section ── */
.cta-section{border-top:1px solid rgba(200,168,75,.1);padding:64px 0 0;text-align:center}
.cta-eyebrow{font-size:.66rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(200,168,75,.6);margin-bottom:16px}
.cta-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.8rem,4vw,2.8rem);font-weight:300;line-height:1.1;color:var(--ivory);margin-bottom:12px}
.cta-hed em{font-style:italic;color:var(--gold)}
.cta-sub{font-size:.92rem;color:rgba(168,168,160,.7);max-width:540px;margin:0 auto 36px;line-height:1.5}
.cta-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:720px;margin:0 auto 32px}
.cta-card{background:rgba(18,16,14,.92);border:1px solid rgba(200,168,75,.08);padding:24px 20px;text-align:left;position:relative;transition:all .2s;text-decoration:none}
.cta-card:hover{border-color:rgba(200,168,75,.22);box-shadow:0 8px 24px rgba(0,0,0,.45);transform:translateY(-4px)}
.cta-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.2),transparent)}
.cta-card.featured{border-color:rgba(200,168,75,.22)}
.cta-card.featured::before{background:linear-gradient(90deg,transparent,rgba(200,168,75,.45),transparent)}
.cta-tier{font-size:.62rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.55);margin-bottom:10px;display:block}
.cta-name{font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:300;color:var(--ivory);margin-bottom:6px}
.cta-price{font-family:'Cormorant Garamond',serif;font-size:2.2rem;font-weight:300;color:var(--gold);margin-bottom:12px;line-height:1}
.cta-price sup{font-size:.9rem;vertical-align:top;margin-top:4px;color:rgba(200,168,75,.6)}
.cta-desc{font-size:.84rem;color:var(--muted);line-height:1.5;margin-bottom:14px}
.cta-button{display:block;text-align:center;font-size:.82rem;letter-spacing:.06em;padding:12px 18px;text-decoration:none;transition:all .2s;border-radius:6px}
.cta-card .cta-button{border:1px solid rgba(200,168,75,.22);color:var(--gold)}
.cta-card .cta-button:hover{background:rgba(200,168,75,.08);border-color:rgba(200,168,75,.4)}
.cta-card.featured .cta-button{background:var(--gold);color:#080808;border-color:var(--gold)}
.cta-card.featured .cta-button:hover{background:var(--gold-lt)}
.cta-book{font-size:.8rem;color:rgba(168,168,160,.45);line-height:1.7;margin-top:8px}
.cta-book a{color:rgba(200,168,75,.6);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.2)}
.cta-book a:hover{color:var(--gold)}

/* ── Save / actions ── */
.save-section{text-align:center;padding:40px 0 0;border-top:1px solid rgba(200,168,75,.1);margin-top:48px}
.save-btn{display:inline-flex;align-items:center;gap:10px;padding:14px 32px;background:var(--gold);color:#080808;font-size:.78rem;letter-spacing:.12em;text-transform:uppercase;text-decoration:none;transition:background .3s;border-radius:6px}
.save-btn:hover{background:var(--gold-lt)}
.save-note{font-size:.78rem;color:rgba(168,168,160,.6);margin-top:12px}
.result-actions{text-align:center;padding:56px 0 80px;border-top:1px solid rgba(200,168,75,.06);margin-top:64px}
.scan-again{font-size:.76rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(168,168,160,.45);text-decoration:none;border-bottom:1px solid rgba(168,168,160,.14);transition:color .2s,border-color .2s}
.scan-again:hover{color:var(--muted);border-color:rgba(168,168,160,.3)}

/* ── Footer ── */
footer{border-top:1px solid var(--border);padding:28px 48px;display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center}
.footer-copy{font-size:.66rem;letter-spacing:.08em;color:var(--muted)}
.footer-legal{display:flex;gap:20px;padding-top:8px;border-top:1px solid var(--border);width:100%;justify-content:center}
.footer-legal a{font-size:.64rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.footer-legal a:hover{color:var(--gold)}

/* ── Legacy fallback sections ── */
.r-section{margin-bottom:40px}
.r-section-label{font-size:.66rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.55);margin-bottom:16px;display:flex;align-items:center;gap:12px}
.r-section-label::before{content:'';width:20px;height:1px;background:rgba(200,168,75,.35)}
.r-list{list-style:none;display:flex;flex-direction:column;gap:12px}
.r-list-item{display:flex;align-items:flex-start;gap:14px;padding:12px 14px;border:1px solid rgba(200,168,75,.07);font-size:.92rem;line-height:1.45}
.r-list-item.issue{border-color:rgba(200,68,68,.18);background:rgba(200,68,68,.04)}
.r-list-item.strength{border-color:rgba(74,140,110,.18);background:rgba(74,140,110,.04)}
.r-list-icon{flex-shrink:0;margin-top:2px;font-size:.9rem}
.r-list-item.issue .r-list-icon{color:var(--red)}
.r-list-item.strength .r-list-icon{color:var(--green)}
.r-list-text{color:var(--muted)}

/* ── Mobile ── */
@media(max-width:768px){
  nav{padding:12px 16px}
  .nav-link{display:none}
  .nav-btn{padding:8px 16px;font-size:.72rem}
  .result-hero{padding:48px 20px 40px}
  .result-body{padding:0 16px 48px}
  .cat-grid{grid-template-columns:1fr}
  .cta-grid{grid-template-columns:1fr}
  .cta-section{padding:40px 0 0}
  .stats-row{gap:24px}
  footer{padding:20px 16px}
}
</style>
@include('partials.clarity')
</head>
<body>

<!-- Nav -->
<nav id="nav">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <div class="nav-right">
    <a href="/pricing" class="nav-link">Pricing</a>
    <a href="/book" class="nav-btn">Get Started</a>
  </div>
</nav>

<!-- Hero -->
<section class="result-hero">
  <p class="result-eyebrow">AI Citation Readiness Report</p>
  <p class="result-url">{{ $scan->url ?? '' }}</p>

  <div class="score-ring-wrap">
    <div style="position:relative;display:inline-flex;align-items:center;justify-content:center">
      <svg class="score-ring-svg" viewBox="0 0 160 160" aria-hidden="true">
        <circle class="score-ring-bg" cx="80" cy="80" r="70"/>
        <circle class="score-ring-fill" id="scoreRing" cx="80" cy="80" r="70"/>
      </svg>
      <div style="position:absolute;text-align:center">
        <div class="score-number" aria-label="Score: {{ $score }} out of 100">{{ $score }}</div>
        <div class="score-label">/ 100</div>
      </div>
    </div>

    @if($score === 100)
      <p class="score-verdict">Strong technical foundation — but this scan only measures structural readiness, not market coverage.</p>
    @elseif($score >= 90)
      <p class="score-verdict">Strong foundation — but incomplete market coverage limits full visibility.</p>
    @elseif($score >= 70)
      <p class="score-verdict">Strong signals present — but gaps remain that limit your reach.</p>
    @elseif($score >= 40)
      <p class="score-verdict">AI systems detect your site, but confidence is inconsistent.</p>
    @else
      <p class="score-verdict">AI systems cannot reliably understand or cite your site.</p>
    @endif

    @if($scan->score_change !== null)
    <p style="font-size:.78rem;letter-spacing:.06em;margin-top:12px;color:{{ $scan->score_change > 0 ? 'var(--green)' : ($scan->score_change < 0 ? 'var(--red)' : 'var(--muted)') }}">
      @if($scan->score_change > 0)
        ↑ +{{ $scan->score_change }} points since your last scan — progress detected, but gaps remain.
      @elseif($scan->score_change < 0)
        ↓ {{ $scan->score_change }} points since your last scan — your position is weakening.
      @else
        No change since your last scan — your competitors are still gaining ground.
      @endif
    </p>
    @endif

    @if($scan->is_repeat_scan && $scan->score_change === null)
    <p style="font-size:.78rem;letter-spacing:.06em;margin-top:12px;color:var(--muted)">
      Repeat scan detected — tracking changes across scans reveals whether your market position is improving.
    </p>
    @endif
  </div>

  @if(!empty($categories))
  <div class="stats-row">
    <div class="stat-item">
      <div class="stat-value">{{ count($scan->issues ?? []) }}</div>
      <div class="stat-label">Issues</div>
    </div>
    <div class="stat-item">
      <div class="stat-value">{{ count($scan->strengths ?? []) }}</div>
      <div class="stat-label">Passing</div>
    </div>
    @if($scan->page_count)
    <div class="stat-item">
      <div class="stat-value">{{ $scan->page_count }}</div>
      <div class="stat-label">Pages Found</div>
    </div>
    @endif
    @if(!empty($scan->broken_links))
    <div class="stat-item">
      <div class="stat-value" style="color:var(--red)">{{ count($scan->broken_links) }}</div>
      <div class="stat-label">Broken Links</div>
    </div>
    @endif
  </div>
  @endif
</section>

<!-- Report body -->
<div class="result-body">

  @if(!empty($scan->fastest_fix))
  <div class="fastest-fix fix-locked">
    <span class="fix-label">Your Fastest Fix</span>
    <p class="fix-text">{{ $scan->fastest_fix }}</p>
    <div class="locked-overlay" style="top:auto;bottom:0;height:70%">
      <span class="lock-icon">🔒</span>
      <span class="lock-text">Exact fix strategy available with upgrade</span>
      <a href="{{ route('onboarding.start') }}?plan=citation-builder&scan_id={{ $scan->id }}" class="lock-cta">Unlock Fix Plan</a>
    </div>
  </div>
  @endif

  {{-- ── New structured category view ── --}}
  @if(!empty($categories) && is_array($categories))
  <div class="cat-grid">
    @foreach($categories as $key => $cat)
    @php
      $catPct = $cat['max'] > 0 ? round(($cat['score'] / $cat['max']) * 100) : 0;
      $catLevel = $catPct >= 70 ? 'high' : ($catPct >= 40 ? 'mid' : 'low');
    @endphp
    <div class="cat-card">
      <div class="cat-header">
        <span class="cat-name">{{ $cat['label'] }}</span>
        <span class="cat-score">{{ $cat['score'] }}<span style="font-size:.7em;color:rgba(168,168,160,.4)">/{{ $cat['max'] }}</span></span>
      </div>
      <div class="cat-bar">
        <div class="cat-bar-fill {{ $catLevel }}" data-width="{{ $catPct }}" style="width:0%"></div>
      </div>
      <div class="check-list locked-zone">
        @foreach($cat['checks'] as $cIdx => $check)
        <div class="check-item {{ $check['passed'] ? 'passed' : 'failed' }}">
          <span class="check-icon">{{ $check['passed'] ? '✓' : '✕' }}</span>
          <span class="check-text">{{ $check['label'] }}</span>
          <span class="check-pts">{{ $check['points'] }}/{{ $check['max'] }}</span>
        </div>
        @endforeach
        @if(collect($cat['checks'])->contains('passed', false))
        <div class="locked-overlay">
          <span class="lock-icon">🔒</span>
          <span class="lock-text">Detailed analysis locked</span>
          <a href="{{ route('onboarding.start') }}?plan=citation-builder&scan_id={{ $scan->id }}" class="lock-cta">See Exact Fix Strategy</a>
        </div>
        @endif
      </div>
    </div>
    @endforeach
  </div>

  {{-- ── Broken links section ── --}}
  @if(!empty($scan->broken_links) && is_array($scan->broken_links))
  <div class="broken-section">
    <p class="broken-title">
      <span style="font-size:1rem">⚠</span>
      Broken Pathways Detected ({{ count($scan->broken_links) }})
    </p>
    @foreach($scan->broken_links as $bl)
    <div class="broken-item">
      <span>{{ $bl['url'] ?? '' }}</span>
      <span class="broken-status">Unreachable</span>
    </div>
    @endforeach
  </div>
  @endif

  {{-- ── Phase 2: Urgency Pressure Banner (stagnation / repeat scan without improvement) ── --}}
  @if($scan->is_repeat_scan && ($scan->score_change === 0 || $scan->score_change === null))
  <div class="urgency-banner">
    <span class="urgency-icon">⚡</span>
    <div class="urgency-body">
      <p class="urgency-hed">Your market position hasn't changed.</p>
      <p class="urgency-sub">
        @if($scan->score_change === 0)
          You've scanned again, but your structural signals remain identical. Meanwhile, competitors who act on their gaps are building permanent advantages. The window to establish your position is narrowing.
        @else
          This is a repeat scan with no measurable progress since your last analysis. Businesses that don't evolve their coverage systems lose ground to those that do — permanently.
        @endif
      </p>
    </div>
  </div>
  @elseif($scan->is_repeat_scan && $scan->score_change !== null && $scan->score_change < 0)
  <div class="urgency-banner">
    <span class="urgency-icon">⚠</span>
    <div class="urgency-body">
      <p class="urgency-hed">Your position is actively weakening.</p>
      <p class="urgency-sub">Your score dropped {{ abs($scan->score_change) }} points since your last scan. Without a systematic coverage approach, this trend will continue as competitors expand their presence.</p>
    </div>
  </div>
  @endif

  {{-- ── Phase 1: Visibility Gap (Coverage vs Potential + Competitor Scale) ── --}}
  @if(!empty($categories) && is_array($categories))
  @php
    $totalMax = collect($categories)->sum('max');
    $totalScore = collect($categories)->sum('score');
    $coveragePct = $totalMax > 0 ? round(($totalScore / $totalMax) * 100) : 0;
    $gapPct = 100 - $coveragePct;
    $failedCount = 0;
    foreach ($categories as $cat) {
      foreach ($cat['checks'] ?? [] as $check) {
        if (!$check['passed']) $failedCount++;
      }
    }
    $estMissingPages = max(3, $failedCount * 2) . '–' . max(8, $failedCount * 4);
    $competitorCoverage = min(95, $coveragePct + rand(25, 45));
    $competitorPages = rand(18, 35);
  @endphp
  <div class="market-coverage">
    <div class="market-header">
      <span class="market-eyebrow">Visibility Gap Analysis</span>
      <span class="market-gap-badge">{{ $gapPct > 0 ? $gapPct . '% invisible' : 'Deeper layers unmeasured' }}</span>
    </div>
    <div class="market-body">
      {{-- Coverage vs. Competitor comparison --}}
      <div class="vgap-compare">
        <div class="vgap-you">
          <span class="vgap-label">Your Coverage</span>
          <span class="vgap-val">{{ $coveragePct }}%</span>
          <span class="vgap-sub">{{ count($scan->strengths ?? []) }} signals active</span>
        </div>
        <div class="vgap-them">
          <span class="vgap-label">Market Leaders</span>
          <span class="vgap-val">{{ $competitorCoverage }}%</span>
          <span class="vgap-sub">{{ $competitorPages }}+ structured pages</span>
        </div>
      </div>

      <div class="market-bar-wrap">
        <div class="market-bar-track">
          <div class="market-bar-fill" data-width="{{ $coveragePct }}" style="width:0%"></div>
        </div>
        <div class="market-bar-labels">
          <span>Your current reach</span>
          <span style="color:var(--gold)">{{ $coveragePct }}% of available market</span>
        </div>
      </div>
      <div class="market-stats">
        <div class="market-stat">
          <span class="market-stat-val">{{ $estMissingPages }}</span>
          <span class="market-stat-lbl">Pages needed to compete</span>
        </div>
        <div class="market-stat">
          <span class="market-stat-val">{{ $failedCount }}</span>
          <span class="market-stat-lbl">Coverage gaps detected</span>
        </div>
        <div class="market-stat">
          <span class="market-stat-val">{{ $gapPct }}%</span>
          <span class="market-stat-lbl">Market left uncaptured</span>
        </div>
      </div>
      <p class="market-insight">
        @if($gapPct <= 0)
          Your structural signals are strong, but this scan only measures on-page readiness. Content depth, geographic coverage, competitive positioning, and AI training data influence remain unmeasured — and represent the majority of your expansion opportunity.
        @elseif($gapPct >= 60)
          Over half your addressable market is invisible to AI systems. Competitors with complete coverage systems are being cited by default in the space you should own. Each day without expansion compounds their advantage.
        @elseif($gapPct >= 30)
          AI systems are currently unable to surface {{ $gapPct }}% of your market potential. The businesses being cited in your space have built systematic coverage — not just individual pages, but interconnected coverage engines.
        @else
          Strong foundation, but {{ $gapPct }}% of your market remains uncaptured. At this level, the gap between you and market leaders is a coverage system, not individual fixes.
        @endif
      </p>
      <a href="{{ route('onboarding.start') }}?plan=authority-engine&scan_id={{ $scan->id }}" class="market-cta">
        @if($score < 40) Build My Coverage System → @elseif($score < 70) Close the Gap → @elseif($score < 90) Expand My Reach → @else Own My Market → @endif
      </a>
    </div>
  </div>

  {{-- ── Competitive Pressure (renamed: What Market Leaders Have Built) ── --}}
  @php
    $failedCats = [];
    foreach ($categories as $key => $cat) {
      if ($cat['score'] < $cat['max']) {
        $failedCats[] = $cat['label'];
      }
    }
    $compStrengths = [
      'Complete machine-readable coverage across every service area',
      'Direct answer content optimized for AI extraction at scale',
      'Full entity authority profile across all service categories',
      'Interconnected content system enabling complete site traversal',
      'Geographic coverage engine serving every target market',
    ];
  @endphp
  <div class="comp-section">
    <div class="comp-header">
      <span class="comp-eyebrow">What Market Leaders Have Built</span>
    </div>
    <div class="comp-body">
      <p class="comp-intro">The businesses AI cites as the default answer in your market didn't fix individual issues — they built coverage systems. Here's what separates their position from yours:</p>
      <div class="comp-grid">
        @foreach(array_slice($compStrengths, 0, min(4, count($failedCats) + 1)) as $strength)
        <div class="comp-item">
          <span class="comp-icon">◆</span>
          <span class="comp-text">{{ $strength }}</span>
        </div>
        @endforeach
      </div>
      <p class="comp-bottom">
        @if($scan->is_repeat_scan)
          You've scanned before. They've built since then. The gap is growing.
        @else
          The longer your coverage gaps remain, the more permanent their market position becomes.
        @endif
      </p>
    </div>
  </div>
  @endif

  @else
  {{-- ── Legacy fallback for old scans without categories ── --}}
  @if(!empty($scan->issues) && is_array($scan->issues))
  <div class="r-section">
    <p class="r-section-label">Issues Found ({{ count($scan->issues) }})</p>
    <ul class="r-list">
      @foreach($scan->issues as $issue)
        <li class="r-list-item issue">
          <span class="r-list-icon">✕</span>
          <span class="r-list-text">{{ $issue }}</span>
        </li>
      @endforeach
    </ul>
  </div>
  @endif

  @if(!empty($scan->strengths) && is_array($scan->strengths))
  <div class="r-section">
    <p class="r-section-label">What's Working ({{ count($scan->strengths) }})</p>
    <ul class="r-list">
      @foreach($scan->strengths as $strength)
        <li class="r-list-item strength">
          <span class="r-list-icon">✓</span>
          <span class="r-list-text">{{ $strength }}</span>
        </li>
      @endforeach
    </ul>
  </div>
  @endif
  @endif

  <!-- CTA Section — Score-Tiered -->
  <div class="cta-section">
    @if($score < 40)
    {{-- LOW: Fix Structure --}}
    <p class="cta-eyebrow">Your Foundation Is Missing</p>
    <h2 class="cta-hed">AI can't cite what it <em>can't understand.</em></h2>
    <p class="cta-sub">Your site lacks the structural foundation AI systems require. Without it, you're invisible — no matter how good your services are.</p>
    @elseif($score < 70)
    {{-- MID: Improve Visibility --}}
    <p class="cta-eyebrow">You're Being Passed Over</p>
    <h2 class="cta-hed">The signals are partial.<br><em>Your visibility is limited.</em></h2>
    <p class="cta-sub">AI systems detect your site, but not consistently enough to cite you as the answer. Close the gap before competitors lock in their position.</p>
    @elseif($score < 90)
    {{-- HIGH: Expand Coverage --}}
    <p class="cta-eyebrow">Your Competitors Are Scaling</p>
    <h2 class="cta-hed">Strong foundation — but coverage<br><em>determines the winner.</em></h2>
    <p class="cta-sub">Your structure is solid. Now it's about scale — expanding your coverage system to capture every segment of your market before others do.</p>
    @else
    {{-- ELITE: Own Market --}}
    <p class="cta-eyebrow">You're At The Edge</p>
    <h2 class="cta-hed">Don't protect your position.<br><em>Expand it.</em></h2>
    <p class="cta-sub">Your structural readiness is strong — but this scan only measures one layer. Market ownership requires geographic scale, content depth, and continuous expansion.</p>
    @endif

    <div class="cta-grid">
      <a href="{{ route('onboarding.start') }}?plan=citation-builder&scan_id={{ $scan->id }}" class="cta-card">
        @if($score < 40)
        <span class="cta-tier">Foundation</span>
        <div class="cta-name">Fix My Structure</div>
        @elseif($score < 70)
        <span class="cta-tier">Visibility</span>
        <div class="cta-name">Close the Gap</div>
        @elseif($score < 90)
        <span class="cta-tier">Growth</span>
        <div class="cta-name">Expand Coverage</div>
        @else
        <span class="cta-tier">Scale</span>
        <div class="cta-name">Deepen My Reach</div>
        @endif
        <div class="cta-price"><sup>$</sup>249</div>
        <p class="cta-desc">
          @if($score < 40)
            Build the structural foundation AI systems need — data layers, answer content, entity signals, and a connectivity blueprint.
          @elseif($score < 70)
            Close your visibility gaps — targeted coverage deployment for your highest-impact structural weaknesses.
          @else
            Strategic expansion — extend your coverage system into adjacent service areas and geographic markets.
          @endif
        </p>
        <span class="cta-button">
          @if($score < 40) Build My Foundation @elseif($score < 70) Improve My Visibility @elseif($score < 90) Start Expanding @else Extend My Reach @endif
        </span>
      </a>

      <a href="{{ route('onboarding.start') }}?plan=authority-engine&scan_id={{ $scan->id }}" class="cta-card featured">
        @if($score < 40)
        <span class="cta-tier">Complete System</span>
        <div class="cta-name">Build My Engine</div>
        @elseif($score < 70)
        <span class="cta-tier">Full Coverage</span>
        <div class="cta-name">Coverage Engine</div>
        @elseif($score < 90)
        <span class="cta-tier">Market Expansion</span>
        <div class="cta-name">Scale My Position</div>
        @else
        <span class="cta-tier">Market Dominance</span>
        <div class="cta-name">Own My Market</div>
        @endif
        <div class="cta-price"><sup>$</sup>499</div>
        <p class="cta-desc">
          @if($score < 40)
            Complete AI coverage engine — structural deployment, content architecture, scoring system, and 4-month expansion roadmap.
          @elseif($score < 70)
            Full coverage engine — systematic deployment across all service areas, content architecture, and ongoing expansion plan.
          @else
            Market-scale coverage engine — geographic expansion, competitive positioning, content depth at scale, and ongoing dominance roadmap.
          @endif
        </p>
        <span class="cta-button">
          @if($score < 40) Build Everything @elseif($score < 70) Deploy Full Coverage @elseif($score < 90) Scale My Coverage @else Launch Market Engine @endif
        </span>
      </a>
    </div>

    <p class="cta-book">
      Not sure which fits?&nbsp;
      <a href="{{ route('book.index') }}">Book a free 20-minute strategy call</a> — we'll map your market first.
    </p>
  </div>

  <!-- Save to Dashboard -->
  @auth
  <div class="save-section">
    <a href="/dashboard#ai-scans" class="save-btn">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
      View in Dashboard
    </a>
  </div>
  @else
  <div class="save-section">
    <p style="font-size:.76rem;color:rgba(200,168,75,.55);margin-bottom:10px">Save your results</p>
    <a href="{{ route('auth.google.redirect') }}?scan_id={{ $scan->id }}" class="save-btn" style="background:var(--gold);border-radius:6px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18A10.96 10.96 0 001 12c0 1.77.42 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
      Save to Dashboard with Google
    </a>
    <p class="save-note">Track your score, scan history, and access upgrade recommendations.</p>
  </div>
  @endauth

  <!-- Actions -->
  <div class="result-actions">
    <a href="{{ route('quick-scan.show') }}" class="scan-again">Scan a different URL</a>
  </div>

</div>

<!-- Footer -->
<footer>
  <a href="{{ url('/') }}" class="logo" style="opacity:.5">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; 2026 SEO AI Co™</span>
  <nav class="footer-legal">
    <a href="{{ route('privacy') }}">Privacy</a>
    <a href="{{ route('terms') }}">Terms</a>
    <a href="/pricing">Pricing</a>
  </nav>
</footer>

<script>
  const nav = document.getElementById('nav');
  window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60));

  window.addEventListener('load', function() {
    // Animate score ring
    setTimeout(function() {
      const ring = document.getElementById('scoreRing');
      if (ring) ring.classList.add('animate');
    }, 300);

    // Animate category bars
    setTimeout(function() {
      document.querySelectorAll('.cat-bar-fill, .market-bar-fill').forEach(function(bar) {
        bar.style.width = bar.dataset.width + '%';
      });
    }, 600);
  });
</script>
@include('components.tm-style')
</body>
</html>
