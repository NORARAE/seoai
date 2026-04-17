<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Your AI Visibility Report — SEO AI Co</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
@include('partials.design-system')

/* ── Page ── */
.prev-page{
  min-height:100vh;display:flex;flex-direction:column;
  align-items:center;justify-content:center;
  padding:56px 20px 88px;position:relative;overflow-x:hidden;
}
.prev-page::before{
  content:'';position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(200,168,75,.04) 1px,transparent 1px),
    linear-gradient(90deg,rgba(200,168,75,.04) 1px,transparent 1px);
  background-size:48px 48px;
  pointer-events:none;z-index:0;
}
.prev-page::after{
  content:'';position:absolute;top:12%;left:50%;transform:translateX(-50%);
  width:900px;height:600px;
  background:radial-gradient(ellipse,rgba(200,168,75,.06) 0%,rgba(200,168,75,.02) 40%,transparent 70%);
  pointer-events:none;z-index:0;
}

/* Ambient scan line */
.prev-scanline{
  position:fixed;top:0;left:0;right:0;height:1px;z-index:0;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.12),transparent);
  animation:scanDrift 8s linear infinite;pointer-events:none;
}
@keyframes scanDrift{
  0%{top:0;opacity:0}
  5%{opacity:1}
  95%{opacity:1}
  100%{top:100vh;opacity:0}
}

.prev-inner{
  max-width:600px;width:100%;position:relative;z-index:1;
}

/* ── System Status ── */
.prev-status{
  display:flex;align-items:center;justify-content:center;gap:8px;
  margin-bottom:32px;
}
.prev-status-dot{
  width:6px;height:6px;border-radius:50%;
  background:var(--gold);
  box-shadow:0 0 8px rgba(200,168,75,.4);
  animation:statusPulse 2s ease-in-out infinite;
}
@keyframes statusPulse{
  0%,100%{opacity:.6;box-shadow:0 0 6px rgba(200,168,75,.3)}
  50%{opacity:1;box-shadow:0 0 14px rgba(200,168,75,.55)}
}
.prev-status-text{
  font-size:.58rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.5);
}

/* ── Header ── */
.prev-eye{
  text-align:center;font-size:.6rem;letter-spacing:.28em;text-transform:uppercase;
  color:var(--gold-secondary);margin-bottom:16px;
}
.prev-hed{
  text-align:center;font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.8rem,3.4vw,2.6rem);font-weight:300;
  color:var(--ivory);line-height:1.1;margin-bottom:14px;
}
.prev-sub{
  text-align:center;font-size:.84rem;color:var(--muted);
  line-height:1.78;max-width:460px;margin:0 auto 6px;
}
.prev-urgency{
  text-align:center;font-size:.76rem;color:rgba(196,120,120,.7);
  margin-bottom:8px;letter-spacing:.02em;
}
.prev-url{
  text-align:center;font-size:.72rem;color:var(--gold);
  word-break:break-all;margin-bottom:36px;opacity:.7;
  font-family:'DM Sans',monospace;letter-spacing:.02em;
}

/* ── Issue Counter ── */
.prev-issue-badge{
  display:flex;align-items:center;justify-content:center;gap:8px;
  margin:0 auto 10px;padding:10px 22px;
  background:rgba(196,120,120,.06);border:1px solid rgba(196,120,120,.12);
  width:fit-content;
}
.prev-issue-badge .count{
  font-size:1.3rem;font-weight:700;color:rgba(196,120,120,.85);
  font-family:'DM Sans',sans-serif;
}
.prev-issue-badge .label{
  font-size:.72rem;letter-spacing:.06em;color:rgba(196,120,120,.6);
  text-transform:uppercase;font-weight:500;
}
.prev-hidden-count{
  text-align:center;font-size:.72rem;color:rgba(168,168,160,.45);
  margin-bottom:28px;letter-spacing:.01em;
}

/* ── Results Card ── */
.prev-card{
  background:rgba(14,13,9,.88);
  border:1px solid rgba(200,168,75,.08);
  border-radius:6px;padding:32px 28px;
  margin-bottom:28px;
  position:relative;
}
.prev-card-head{
  font-size:.58rem;letter-spacing:.2em;text-transform:uppercase;
  color:rgba(200,168,75,.4);margin-bottom:18px;padding-bottom:12px;
  border-bottom:1px solid rgba(200,168,75,.06);
}

/* ── Signal rows ── */
.prev-signals{list-style:none;padding:0;margin:0}
.prev-signal{
  display:flex;align-items:flex-start;gap:14px;
  padding:13px 0;border-bottom:1px solid rgba(200,168,75,.04);
  opacity:0;transform:translateY(10px);
  transition:opacity .5s var(--ease-out),transform .5s var(--ease-out);
}
.prev-signal:last-child{border-bottom:none}
.prev-signal.revealed{opacity:1;transform:translateY(0)}

/* Check / warn icons */
.prev-icon{
  flex-shrink:0;width:24px;height:24px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:.76rem;font-weight:600;margin-top:1px;
  transition:box-shadow .5s ease;
}
.prev-icon--ok{
  background:rgba(106,175,144,.1);color:#6aaf90;
  box-shadow:0 0 10px rgba(106,175,144,.12);
}
.prev-icon--ok.revealed-glow{
  box-shadow:0 0 18px rgba(106,175,144,.25);
}
.prev-icon--warn{
  background:rgba(218,165,60,.08);color:#daa53c;
  box-shadow:0 0 10px rgba(218,165,60,.1);
}
.prev-icon--warn.revealed-glow{
  box-shadow:0 0 18px rgba(218,165,60,.3);
  animation:warnPulse 2.5s ease-in-out infinite;
}
@keyframes warnPulse{
  0%,100%{box-shadow:0 0 10px rgba(218,165,60,.15)}
  50%{box-shadow:0 0 22px rgba(218,165,60,.35)}
}

.prev-signal-text{font-size:.84rem;color:var(--ivory);letter-spacing:.01em;flex:1}
.prev-signal-text .muted{color:var(--muted)}
.prev-signal-text .impact{
  display:block;font-size:.68rem;color:rgba(196,120,120,.55);
  margin-top:3px;line-height:1.4;letter-spacing:.02em;
}

/* ── Locked Intelligence ── */
.prev-locked{
  position:relative;
  background:rgba(10,9,7,.92);
  border:1px solid rgba(200,168,75,.08);
  border-radius:6px;padding:40px 28px;
  margin-bottom:32px;overflow:hidden;
}
.prev-locked::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at center,rgba(200,168,75,.03) 0%,transparent 60%);
  pointer-events:none;
}
.prev-locked-overlay{
  position:absolute;inset:0;
  backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);
  background:rgba(8,8,8,.55);
  z-index:2;display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:16px;
}
.prev-locked-badge{
  display:flex;align-items:center;gap:8px;
  padding:10px 22px;border-radius:3px;
  background:rgba(200,168,75,.06);border:1px solid rgba(200,168,75,.12);
  font-size:.62rem;letter-spacing:.16em;text-transform:uppercase;
  color:var(--gold);font-weight:500;
}
.prev-locked-badge svg{width:13px;height:13px;opacity:.6}
.prev-locked-unlock-text{
  font-size:.76rem;color:rgba(168,168,160,.45);
  text-align:center;line-height:1.55;max-width:300px;
}

.prev-locked-content{
  opacity:.15;pointer-events:none;user-select:none;
}
.prev-locked-row{
  display:flex;align-items:center;gap:12px;
  padding:14px 0;border-bottom:1px solid rgba(200,168,75,.03);
}
.prev-locked-row:last-child{border-bottom:none}
.prev-locked-dot{
  width:8px;height:8px;border-radius:50%;
  background:rgba(200,168,75,.2);flex-shrink:0;
}
.prev-locked-label{font-size:.82rem;color:var(--ivory);flex:1}
.prev-locked-bar{
  height:5px;border-radius:3px;
  background:rgba(200,168,75,.08);max-width:100px;width:100%;
}
.prev-locked-bar.long{max-width:140px}
.prev-locked-score{
  text-align:center;padding-top:20px;
  font-family:'Cormorant Garamond',serif;
  font-size:2.2rem;color:var(--gold);font-weight:300;
  opacity:.5;
}

/* ── CTA ── */
.prev-cta-wrap{text-align:center;margin-bottom:12px}
.prev-cta{
  display:inline-block;padding:22px 60px;
  background:linear-gradient(180deg,#d8be72,#c8a84b);color:#080808;
  font-size:.82rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
  text-decoration:none;border:1px solid rgba(226,201,125,.4);border-radius:3px;
  transition:all .35s;position:relative;overflow:hidden;
  box-shadow:0 4px 24px rgba(200,168,75,.15);
}
.prev-cta::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.14),transparent);
  transform:translateX(-100%);transition:transform .6s;
}
.prev-cta:hover{
  background:linear-gradient(180deg,#e0c97e,#d4b45a);
  border-color:rgba(226,201,125,.65);
  box-shadow:0 8px 40px rgba(200,168,75,.28);
  transform:translateY(-2px);
}
.prev-cta:hover::before{transform:translateX(100%)}

@keyframes ctaPulse{
  0%,100%{box-shadow:0 4px 24px rgba(200,168,75,.15)}
  50%{box-shadow:0 6px 40px rgba(200,168,75,.3)}
}
.prev-cta-pulse{animation:ctaPulse 2.5s ease-in-out infinite}

.prev-cta-sub{
  text-align:center;font-size:.78rem;color:var(--muted);
  line-height:1.65;margin-top:16px;max-width:420px;margin-left:auto;margin-right:auto;
}

.prev-reassure{
  text-align:center;font-size:.68rem;color:rgba(168,168,160,.35);
  line-height:1.7;margin-top:14px;letter-spacing:.01em;
}

.prev-secondary{
  text-align:center;margin-top:20px;
}
.prev-secondary a{
  font-size:.72rem;color:rgba(200,168,75,.45);text-decoration:none;
  letter-spacing:.02em;transition:color .3s;
}
.prev-secondary a:hover{color:var(--gold)}

/* ── Progression ── */
.prev-progression{
  display:flex;align-items:center;justify-content:center;gap:0;
  margin-bottom:36px;
}
.prev-prog-step{
  display:flex;flex-direction:column;align-items:center;gap:4px;
  padding:0 14px;position:relative;
}
.prev-prog-step:not(:last-child)::after{
  content:'→';position:absolute;right:-8px;top:30%;
  font-size:.7rem;color:rgba(200,168,75,.2);
}
.prev-prog-dot{
  width:10px;height:10px;border-radius:50%;
}
.prev-prog-dot.active{background:var(--gold);box-shadow:0 0 12px rgba(200,168,75,.35)}
.prev-prog-dot.next{background:rgba(200,168,75,.18);border:1px solid rgba(200,168,75,.25)}
.prev-prog-dot.locked{background:rgba(168,168,160,.1);border:1px solid rgba(168,168,160,.12)}
.prev-prog-label{font-size:.56rem;letter-spacing:.12em;text-transform:uppercase}
.prev-prog-label.active{color:var(--gold);font-weight:600}
.prev-prog-label.next{color:rgba(200,168,75,.35)}
.prev-prog-label.locked{color:rgba(168,168,160,.25)}

/* ── Layer Block ── */
.prev-layer{
  text-align:center;padding:20px 22px;
  border:1px solid rgba(200,168,75,.06);
  background:rgba(200,168,75,.02);
  margin-top:36px;
}
.prev-layer-label{
  font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;
  color:var(--gold-secondary);margin-bottom:6px;font-weight:500;
}
.prev-layer-sub{
  font-size:.74rem;color:var(--muted);line-height:1.6;
}
.prev-layer-sub span{color:var(--gold)}

/* ── Sticky Bar ── */
.prev-sticky{
  position:fixed;bottom:0;left:0;right:0;z-index:100;
  background:rgba(8,8,8,.94);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);
  border-top:1px solid rgba(200,168,75,.08);
  padding:14px 24px;
  display:flex;align-items:center;justify-content:center;gap:18px;
  transform:translateY(100%);opacity:0;
  transition:transform .5s var(--ease-out),opacity .5s var(--ease-out);
}
.prev-sticky.visible{transform:translateY(0);opacity:1}
.prev-sticky-text{
  font-size:.72rem;color:rgba(168,168,160,.5);letter-spacing:.01em;
  white-space:nowrap;
}
.prev-sticky-cta{
  display:inline-block;padding:12px 32px;
  background:linear-gradient(180deg,#d8be72,#c8a84b);color:#080808;
  font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
  text-decoration:none;border:1px solid rgba(226,201,125,.4);border-radius:3px;
  transition:all .35s;position:relative;overflow:hidden;flex-shrink:0;
  box-shadow:0 4px 24px rgba(200,168,75,.15);
}
.prev-sticky-cta::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.14),transparent);
  transform:translateX(-100%);transition:transform .6s;
}
.prev-sticky-cta:hover{
  background:linear-gradient(180deg,#e0c97e,#d4b45a);
  box-shadow:0 8px 40px rgba(200,168,75,.28);
  transform:translateY(-1px);
}
.prev-sticky-cta:hover::before{transform:translateX(100%)}

/* ── Responsive ── */

/* ── Mobile UX refinement ── */
@media(max-width:768px){
  /* Page container — breathing room */
  .prev-page{padding:48px 20px 88px}

  /* Header — bigger, more assertive */
  .prev-hed{font-size:clamp(1.7rem,5vw,2.4rem);margin-bottom:14px}
  .prev-sub{font-size:.92rem;line-height:1.8;margin-bottom:8px}
  .prev-eye{font-size:.66rem;letter-spacing:.24em;margin-bottom:14px}
  .prev-urgency{font-size:.82rem;margin-bottom:10px}
  .prev-url{font-size:.78rem;margin-bottom:32px}

  /* Issue counter */
  .prev-issue-badge .count{font-size:1.4rem}
  .prev-issue-badge .label{font-size:.76rem}
  .prev-issue-badge{padding:12px 24px}
  .prev-hidden-count{font-size:.78rem;margin-bottom:24px}

  /* Signal rows — bigger text */
  .prev-signal{padding:15px 0;gap:14px}
  .prev-signal-text{font-size:.9rem;line-height:1.72}
  .prev-signal-text .impact{font-size:.74rem;margin-top:4px;line-height:1.5}
  .prev-icon{width:26px;height:26px;font-size:.8rem}

  /* Cards — more padding */
  .prev-card{padding:30px 26px;margin-bottom:28px}
  .prev-card-head{font-size:.62rem;margin-bottom:18px;padding-bottom:14px}

  /* Locked section */
  .prev-locked{padding:36px 26px;margin-bottom:28px}
  .prev-locked-badge{font-size:.66rem;padding:11px 22px}
  .prev-locked-unlock-text{font-size:.82rem;line-height:1.6;max-width:280px}

  /* CTA — bigger, more dominant */
  .prev-cta{padding:20px 48px;font-size:.84rem;letter-spacing:.12em}
  .prev-cta-sub{font-size:.84rem;line-height:1.7;margin-top:18px}
  .prev-reassure{font-size:.74rem;margin-top:16px}

  /* Progression bar — readable labels */
  .prev-prog-label{font-size:.6rem;letter-spacing:.1em}
  .prev-prog-dot{width:11px;height:11px}
  .prev-progression{gap:2px;margin-bottom:32px}
  .prev-prog-step{padding:0 12px}

  /* Layer block */
  .prev-layer{padding:22px 24px;margin-top:32px}
  .prev-layer-label{font-size:.64rem;margin-bottom:8px}
  .prev-layer-sub{font-size:.8rem;line-height:1.68}

  /* Status indicator */
  .prev-status{margin-bottom:28px}
  .prev-status-text{font-size:.62rem;letter-spacing:.2em}

  /* Sticky bar — bigger CTA */
  .prev-sticky{padding:14px 20px;gap:14px}
  .prev-sticky-text{font-size:.74rem}
  .prev-sticky-cta{padding:14px 28px;font-size:.72rem;min-height:48px}
}

@media(max-width:600px){
  .prev-page{padding:40px 16px 80px}
  .prev-card,.prev-locked{padding:28px 24px}
  .prev-inner{max-width:100%}
  .prev-hed{font-size:clamp(1.5rem,6vw,1.9rem)}
  .prev-cta{padding:18px 36px;font-size:.82rem;min-height:50px}
  .prev-sticky{gap:12px;padding:14px 18px}
  .prev-sticky-text{font-size:.76rem}
  .prev-sticky-cta{padding:14px 28px;font-size:.76rem;min-height:46px}
  .prev-locked{padding:32px 24px}
}

/* ── Small phones ── */
@media(max-width:430px){
  .prev-page{padding:36px 14px 76px}
  .prev-card,.prev-locked{padding:26px 22px}
  .prev-hed{font-size:clamp(1.4rem,6.5vw,1.8rem)}
  .prev-sub{font-size:.90rem}
  .prev-signal-text{font-size:.88rem}
  .prev-cta{padding:16px 32px;font-size:.80rem;min-height:48px}
  .prev-sticky-cta{padding:12px 24px;font-size:.74rem;min-height:44px}
  .prev-locked{padding:28px 20px}
}

/* ── Very small phones ── */
@media(max-width:390px){
  .prev-hed{font-size:clamp(1.4rem,7vw,1.7rem)}
  .prev-sub{font-size:.88rem}
  .prev-cta{padding:16px 28px;font-size:.78rem}
  .prev-signal-text{font-size:.86rem}
}
</style>
</head>
<body class="prev-page">

<!-- Ambient scan line -->
<div class="prev-scanline"></div>

<div class="prev-inner">

  <!-- System status indicator -->
  <div class="prev-status">
    <span class="prev-status-dot"></span>
    <span class="prev-status-text">System Active</span>
  </div>

  <!-- Progression bar -->
  <div class="prev-progression">
    <div class="prev-prog-step">
      <span class="prev-prog-dot active"></span>
      <span class="prev-prog-label active">Scan</span>
    </div>
    <div class="prev-prog-step">
      <span class="prev-prog-dot next"></span>
      <span class="prev-prog-label next">Report</span>
    </div>
    <div class="prev-prog-step">
      <span class="prev-prog-dot locked"></span>
      <span class="prev-prog-label locked">Signals</span>
    </div>
    <div class="prev-prog-step">
      <span class="prev-prog-dot locked"></span>
      <span class="prev-prog-label locked">Activation</span>
    </div>
  </div>

  <!-- Header -->
  <p class="prev-eye">Signal Detection Complete</p>
  <h1 class="prev-hed">Your AI Visibility Intelligence</h1>
  <p class="prev-sub">The system has completed its initial analysis of your domain.</p>
  @if($issueCount > 0)
  <p class="prev-urgency">Critical signal gaps detected in your citation structure.</p>
  @endif
  <p class="prev-url">{{ $host }}</p>

  <!-- Issue counter badge -->
  @if($issueCount > 0)
  <div class="prev-issue-badge">
    <span class="count">{{ $issueCount }}</span>
    <span class="label">{{ $issueCount === 1 ? 'gap' : 'gaps' }} detected</span>
  </div>
  <p class="prev-hidden-count">{{ $issueCount }} identified &mdash; deeper signals remain locked</p>

  <!-- Primary above-fold CTA ($2) -->
  <div class="prev-cta-wrap" style="margin-bottom:32px">
    <a href="{{ route('checkout.scan-basic') }}" class="prev-cta prev-cta-pulse" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'above_fold',cta_label:'scan_basic'});">Unlock Layer&nbsp;1 Report&nbsp;&mdash;&nbsp;$2</a>
  </div>
  @else
  <!-- CTA for clean scans -->
  <div class="prev-cta-wrap" style="margin-bottom:32px">
    <a href="{{ route('checkout.scan-basic') }}" class="prev-cta" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'above_fold_clean',cta_label:'scan_basic'});">Unlock Full Report&nbsp;&mdash;&nbsp;$2</a>
  </div>
  @endif

  <!-- Detected signals card -->
  <div class="prev-card">
    <p class="prev-card-head">Detected Signals</p>
    <ul class="prev-signals" id="prevSignals">

      {{-- Always detected --}}
      <li class="prev-signal" data-delay="0">
        <span class="prev-icon prev-icon--ok">✓</span>
        <span class="prev-signal-text">Site structure detected</span>
      </li>

      <li class="prev-signal" data-delay="1">
        <span class="prev-icon prev-icon--ok">✓</span>
        <span class="prev-signal-text">{{ $preview['pages_detected'] }} pages discovered</span>
      </li>

      {{-- Sitemap --}}
      @if($preview['has_sitemap'])
      <li class="prev-signal" data-delay="2">
        <span class="prev-icon prev-icon--ok">✓</span>
        <span class="prev-signal-text">XML Sitemap present</span>
      </li>
      @else
      <li class="prev-signal" data-delay="2">
        <span class="prev-icon prev-icon--warn">⚠</span>
        <span class="prev-signal-text">
          Missing XML Sitemap
          <span class="impact">Limits AI extraction</span>
        </span>
      </li>
      @endif

      {{-- Schema --}}
      @if($preview['has_schema'])
      <li class="prev-signal" data-delay="3">
        <span class="prev-icon prev-icon--ok">✓</span>
        <span class="prev-signal-text">Structured data (Schema) present</span>
      </li>
      @else
      <li class="prev-signal" data-delay="3">
        <span class="prev-icon prev-icon--warn">⚠</span>
        <span class="prev-signal-text">
          Missing structured data <span class="muted">(Schema)</span>
          <span class="impact">Reduces citation confidence</span>
        </span>
      </li>
      @endif

      {{-- Locations --}}
      @if($preview['has_locations'])
      <li class="prev-signal" data-delay="4">
        <span class="prev-icon prev-icon--ok">✓</span>
        <span class="prev-signal-text">Location signals detected</span>
      </li>
      @else
      <li class="prev-signal" data-delay="4">
        <span class="prev-icon prev-icon--warn">⚠</span>
        <span class="prev-signal-text">
          Weak location signals
          <span class="impact">Impacts discovery radius</span>
        </span>
      </li>
      @endif

      {{-- Authority --}}
      @if($preview['has_authority'])
      <li class="prev-signal" data-delay="5">
        <span class="prev-icon prev-icon--ok">✓</span>
        <span class="prev-signal-text">Authority signals present</span>
      </li>
      @else
      <li class="prev-signal" data-delay="5">
        <span class="prev-icon prev-icon--warn">⚠</span>
        <span class="prev-signal-text">
          No authority indicators found
          <span class="impact">Limits extraction priority</span>
        </span>
      </li>
      @endif

    </ul>
  </div>

  <!-- Inline value statement -->
  <div style="text-align:center;padding:32px 28px;margin-bottom:28px;background:rgba(14,13,9,.88);border:1px solid rgba(200,168,75,.08);border-radius:6px">
    <p style="font-family:'Cormorant Garamond',serif;font-size:clamp(1.2rem,2.2vw,1.55rem);font-weight:300;color:var(--ivory);line-height:1.25;margin-bottom:14px">
      Full analysis and priority fix path unlock next.
    </p>
    <p style="font-size:.78rem;color:var(--muted);line-height:1.7;max-width:380px;margin:0 auto 22px">
      Your Layer&nbsp;1 report includes visibility score, signal status, primary bottleneck, and next-step guidance.
    </p>
    <a href="{{ route('checkout.scan-basic') }}" class="prev-cta" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'inline_cta',cta_label:'scan_basic'});">Unlock Full Report&nbsp;&mdash;&nbsp;$2</a>
  </div>

  <!-- Locked intelligence section -->
  <div class="prev-locked">
    <div class="prev-locked-overlay">
      <span class="prev-locked-badge">
        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="10" height="8" rx="1.5"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg>
        Layer 1 &mdash; Locked
      </span>
      <p class="prev-locked-unlock-text">Your visibility score and signal intelligence are ready to view.</p>
    </div>
    <div class="prev-locked-content" aria-hidden="true">
      <div class="prev-locked-row">
        <span class="prev-locked-dot"></span>
        <span class="prev-locked-label">AI Visibility Score</span>
        <span class="prev-locked-bar"></span>
      </div>
      <div class="prev-locked-row">
        <span class="prev-locked-dot"></span>
        <span class="prev-locked-label">Signal Strength Map</span>
        <span class="prev-locked-bar long"></span>
      </div>
      <div class="prev-locked-row">
        <span class="prev-locked-dot"></span>
        <span class="prev-locked-label">Primary Bottleneck</span>
        <span class="prev-locked-bar"></span>
      </div>
      <div class="prev-locked-row">
        <span class="prev-locked-dot"></span>
        <span class="prev-locked-label">Citation Gap Matrix</span>
        <span class="prev-locked-bar long"></span>
      </div>
      <div class="prev-locked-row">
        <span class="prev-locked-dot"></span>
        <span class="prev-locked-label">Priority Fix Sequence</span>
        <span class="prev-locked-bar"></span>
      </div>
      <p class="prev-locked-score">-- / 100</p>
    </div>
  </div>

  <!-- Primary CTA ($2) -->
  <div class="prev-cta-wrap">
    <a href="{{ route('checkout.scan-basic') }}" class="prev-cta prev-cta-pulse" id="mainCta" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'primary_cta',cta_label:'scan_basic'});">Unlock Full Report&nbsp;&mdash;&nbsp;$2</a>
  </div>
  <p class="prev-cta-sub">Unlocks your visibility score, signal status, primary bottleneck, and next-step guidance.</p>
  <p class="prev-reassure">Your scan data carries forward&nbsp;&bull;&nbsp;Nothing repeated&nbsp;&bull;&nbsp;Secure checkout</p>

  <!-- Secondary $99 path (non-distracting) -->
  <div class="prev-secondary">
    <a href="{{ route('checkout.signal-expansion') }}" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'secondary',cta_label:'signal_expansion'});">Or go deeper: Signal Expansion &mdash; $99</a>
  </div>

  <!-- Layer progression block -->
  <div class="prev-layer">
    <p class="prev-layer-label">You are at Layer 1</p>
    <p class="prev-layer-sub"><span>Unlock Report ($2)</span> &rarr; Signal Expansion &rarr; Structure &rarr; Activation</p>
  </div>

</div>

<!-- Sticky bottom bar ($2) -->
<div class="prev-sticky" id="prevSticky">
  <span class="prev-sticky-text">Your report is ready</span>
  <a href="{{ route('checkout.scan-basic') }}" class="prev-sticky-cta" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'sticky_bar',cta_label:'scan_basic'});">Unlock Report&nbsp;&mdash;&nbsp;$2</a>
</div>

<script>
(function(){
  // Show sticky bar after scroll or after signal reveal
  var sticky = document.getElementById('prevSticky');
  if(sticky){
    var shown = false;
    function checkSticky(){
      if(window.scrollY > 300 && !shown){ sticky.classList.add('visible'); shown = true; }
      else if(window.scrollY <= 300 && shown){ sticky.classList.remove('visible'); shown = false; }
    }
    window.addEventListener('scroll', checkSticky, {passive:true});
    setTimeout(function(){ if(!shown){ sticky.classList.add('visible'); shown = true; } }, 3500);
  }

  // Staggered reveal for signals
  var signals = document.querySelectorAll('.prev-signal');
  signals.forEach(function(el){
    var delay = parseInt(el.getAttribute('data-delay'),10);
    setTimeout(function(){
      el.classList.add('revealed');
      var icon = el.querySelector('.prev-icon');
      if(icon) setTimeout(function(){ icon.classList.add('revealed-glow'); }, 300);
    }, 400 + delay * 350);
  });
})();
</script>
</body>
</html>
