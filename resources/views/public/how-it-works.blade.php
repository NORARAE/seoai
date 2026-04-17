<!DOCTYPE html>
<html lang="en">
<head>
<script>document.documentElement.classList.add('js-enabled')</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-LNPGQ0GN69"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-LNPGQ0GN69');
</script>
<meta charset="UTF-8">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>How the AI Citation Engine™ Works — From Scan to Market Dominance | SEO AI Co™</title>
<meta name="description" content="See how SEO AI Co™ structures your domain for AI citation — from your first $2 scan to full market deployment. Five steps to getting cited everywhere AI searches.">
<link rel="canonical" href="{{ url('/how-it-works') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="SEO AI Co™">
<meta property="og:title" content="How the AI Citation Engine™ Works | SEO AI Co™">
<meta property="og:description" content="See how SEO AI Co™ structures your domain for AI citation — five steps to getting cited across Google, ChatGPT, and AI search.">
<meta property="og:url" content="{{ url('/how-it-works') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
@include('partials.design-system')
@include('partials.public-nav-css')

/* ── Page token overrides ── */
:root{
  --card:#101010;--border:#1a1a1a;--gold-dim:#9a7a30;
  --white:#ffffff;--muted:#a8a8a0;--warn:#b84040;
  --space-xs:10px;--space-sm:14px;--space-md:18px;--space-lg:28px;--space-xl:72px;
}
body{overflow-x:hidden;line-height:1.85}
body::after{
  content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  opacity:.022;
}

/* ── Shared ── */
.wrap{max-width:var(--wrap-max,1280px);margin:0 auto;padding:0 var(--wrap-pad,64px)}
.r{opacity:0;transform:translateY(18px);transition:opacity .65s var(--ease-out,.23),transform .65s var(--ease-out,.23)}
.r.on{opacity:1;transform:none}
html:not(.js-enabled) .r{opacity:1;transform:none}
.gold-rule{height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.18) 30%,rgba(200,168,75,.18) 70%,transparent);margin:0 auto;max-width:960px}

/* ── Hero ── */
.hiw-hero{
  position:relative;padding:clamp(100px,13vh,148px) 0 56px;text-align:center;
}
.hiw-hero-eye{
  font-size:.6rem;letter-spacing:.26em;text-transform:uppercase;
  color:rgba(200,168,75,.52);margin-bottom:18px;
}
.hiw-hero-hed{
  font-family:'Cormorant Garamond',serif;font-weight:300;
  font-size:clamp(2rem,4.5vw,3.4rem);color:var(--ivory);
  line-height:1.1;margin-bottom:20px;
}
.hiw-hero-hed em{font-style:italic;color:var(--gold-lt)}
.hiw-hero-sub{
  font-size:clamp(.88rem,1.2vw,1.02rem);color:var(--muted);
  max-width:560px;margin:0 auto 36px;line-height:1.8;
}
.hiw-hero-cta{display:inline-flex;gap:16px;flex-wrap:wrap;justify-content:center}

/* ── Section pattern ── */
.hiw-section{padding:clamp(48px,6vw,80px) 0}
.hiw-section-eye{
  font-size:.58rem;letter-spacing:.24em;text-transform:uppercase;
  color:rgba(200,168,75,.5);margin-bottom:14px;text-align:center;
}
.hiw-section-hed{
  font-family:'Cormorant Garamond',serif;font-weight:300;
  font-size:clamp(1.5rem,3vw,2.2rem);color:var(--ivory);
  line-height:1.15;text-align:center;margin-bottom:16px;
}
.hiw-section-sub{
  font-size:.88rem;color:var(--muted);line-height:1.8;
  max-width:600px;margin:0 auto 40px;text-align:center;
}

/* ── Grid cards ── */
.hiw-grid{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:20px;max-width:960px;margin:0 auto;
}
.hiw-card{
  background:var(--card-bg);border:1px solid var(--card-border);
  border-radius:10px;padding:28px 24px;
  transition:border-color .3s,transform .3s;
}
.hiw-card:hover{border-color:var(--card-border-hover);transform:translateY(-2px)}
.hiw-card-num{
  font-size:.56rem;letter-spacing:.2em;text-transform:uppercase;
  color:var(--gold);margin-bottom:10px;
}
.hiw-card-title{
  font-family:'Cormorant Garamond',serif;font-weight:400;
  font-size:1.15rem;color:var(--ivory);margin-bottom:8px;
}
.hiw-card-text{font-size:.82rem;color:var(--muted);line-height:1.75}

/* ── CTA blocks ── */
.hiw-cta-block{
  text-align:center;padding:48px 0 56px;
}
.hiw-cta-block p{
  font-size:.84rem;color:var(--muted);line-height:1.8;
  max-width:480px;margin:0 auto 28px;
}
.hiw-cta-actions{display:flex;align-items:center;justify-content:center;gap:16px;flex-wrap:wrap}

/* ── System visual (pipeline) ── */
.hiw-pipeline{
  display:flex;align-items:stretch;gap:0;max-width:960px;margin:0 auto 40px;
  position:relative;
}
.hiw-pipe-step{
  flex:1;text-align:center;padding:24px 12px 20px;position:relative;
  border:1px solid var(--card-border);background:var(--card-bg);
  transition:border-color .3s,background .3s;
}
.hiw-pipe-step:first-child{border-radius:10px 0 0 10px}
.hiw-pipe-step:last-child{border-radius:0 10px 10px 0}
.hiw-pipe-step.active{
  border-color:rgba(200,168,75,.28);
  background:rgba(200,168,75,.04);
}
.hiw-pipe-step.active .hiw-pipe-label{color:var(--gold-lt)}
.hiw-pipe-num{
  font-size:.52rem;letter-spacing:.2em;text-transform:uppercase;
  color:rgba(200,168,75,.4);margin-bottom:6px;
}
.hiw-pipe-label{
  font-family:'Cormorant Garamond',serif;font-size:.92rem;
  color:var(--ivory);font-weight:400;margin-bottom:4px;
}
.hiw-pipe-desc{font-size:.68rem;color:var(--muted);line-height:1.6}
.hiw-pipe-arrow{
  position:absolute;right:-8px;top:50%;transform:translateY(-50%);z-index:2;
  width:14px;height:14px;color:rgba(200,168,75,.3);
}
.hiw-pipe-you{
  position:absolute;top:-22px;left:50%;transform:translateX(-50%);
  font-size:.5rem;letter-spacing:.18em;text-transform:uppercase;
  color:var(--gold);background:var(--bg);padding:2px 10px;
  border:1px solid rgba(200,168,75,.2);border-radius:20px;
  white-space:nowrap;
}
@media(max-width:700px){
  .hiw-pipeline{flex-direction:column;gap:8px}
  .hiw-pipe-step{border-radius:8px !important}
  .hiw-pipe-arrow{display:none}
  .hiw-pipe-you{position:static;transform:none;display:inline-block;margin:0 auto 6px}
}

/* ── Tier progression ── */
.hiw-tiers{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
  gap:16px;max-width:960px;margin:0 auto;
}
.hiw-tier{
  background:var(--card-bg);border:1px solid var(--card-border);
  border-radius:10px;padding:24px 20px;text-align:center;
  transition:border-color .3s,transform .3s;
}
.hiw-tier:hover{border-color:var(--card-border-hover);transform:translateY(-2px)}
.hiw-tier-price{
  font-family:'Cormorant Garamond',serif;font-size:1.4rem;
  color:var(--gold-lt);font-weight:400;margin-bottom:4px;
}
.hiw-tier-name{
  font-size:.72rem;letter-spacing:.16em;text-transform:uppercase;
  color:var(--ivory);margin-bottom:10px;
}
.hiw-tier-desc{font-size:.78rem;color:var(--muted);line-height:1.7}
.hiw-tier-arrow{
  color:rgba(200,168,75,.25);font-size:.7rem;margin:0 auto;
  display:flex;align-items:center;justify-content:center;padding:8px 0;
}
@media(max-width:700px){
  .hiw-tier-arrow{transform:rotate(90deg)}
}

/* ── Final CTA section ── */
.hiw-final{
  text-align:center;padding:clamp(56px,8vw,96px) 0 clamp(48px,6vw,72px);
}
.hiw-final-hed{
  font-family:'Cormorant Garamond',serif;font-weight:300;
  font-size:clamp(1.6rem,3.5vw,2.6rem);color:var(--ivory);
  line-height:1.15;margin-bottom:14px;
}
.hiw-final-hed em{font-style:italic;color:var(--gold-lt)}
.hiw-final-sub{
  font-size:.88rem;color:var(--muted);line-height:1.8;
  max-width:500px;margin:0 auto 32px;
}
.hiw-final-reassure{
  font-size:.72rem;color:rgba(168,168,160,.45);margin-top:20px;
  letter-spacing:.06em;
}

/* ── "What happens" blocks ── */
.hiw-happens{
  display:grid;grid-template-columns:repeat(3,1fr);
  gap:20px;max-width:960px;margin:0 auto;
}
.hiw-happens-block{
  background:var(--card-bg);border:1px solid var(--card-border);
  border-radius:10px;padding:28px 24px;
  transition:border-color .3s,transform .3s;
}
.hiw-happens-block:hover{border-color:var(--card-border-hover);transform:translateY(-2px)}
.hiw-happens-num{
  font-size:.52rem;letter-spacing:.2em;text-transform:uppercase;
  color:rgba(200,168,75,.4);margin-bottom:10px;
}
.hiw-happens-title{
  font-family:'Cormorant Garamond',serif;font-weight:400;
  font-size:1.1rem;color:var(--ivory);margin-bottom:8px;line-height:1.25;
}
.hiw-happens-text{font-size:.82rem;color:var(--muted);line-height:1.75}

/* ── Level highlight ── */
.hiw-tier.hiw-tier-entry{
  border-color:rgba(200,168,75,.22);
  box-shadow:0 0 24px rgba(200,168,75,.06);
  position:relative;
}
.hiw-tier-badge{
  display:inline-block;
  font-size:.5rem;letter-spacing:.18em;text-transform:uppercase;
  color:var(--gold);background:var(--bg);
  padding:2px 10px;border:1px solid rgba(200,168,75,.2);border-radius:20px;
  margin-bottom:10px;
}
.hiw-tier-level{
  font-size:.56rem;letter-spacing:.16em;text-transform:uppercase;
  color:rgba(200,168,75,.45);margin-bottom:4px;
}
.hiw-progression-note{
  text-align:center;font-size:.86rem;color:var(--muted);
  max-width:480px;margin:28px auto 0;line-height:1.78;
}
.hiw-progression-note strong{color:var(--ivory);font-weight:400}

/* ── Momentum block ── */
.hiw-momentum{
  text-align:center;padding:clamp(56px,8vw,88px) 0 clamp(48px,6vw,72px);
}
.hiw-momentum-hed{
  font-family:'Cormorant Garamond',serif;font-weight:300;
  font-size:clamp(1.5rem,3vw,2.2rem);color:var(--ivory);
  line-height:1.15;margin-bottom:14px;
}
.hiw-momentum-sub{
  font-size:.88rem;color:var(--muted);line-height:1.8;
  max-width:440px;margin:0 auto 32px;
}

@media(max-width:768px){
  .hiw-happens{grid-template-columns:1fr}
  .wrap{padding:0 24px}
  .hiw-grid{grid-template-columns:1fr}
  .hiw-tiers{grid-template-columns:1fr 1fr}
}
@media(max-width:480px){
  .hiw-tiers{grid-template-columns:1fr}
}

@include('partials.public-nav-mobile-css')

/* ── Mobile nav ── */
@media(max-width:900px){
  #nav{padding:14px 20px}#nav.stuck{padding:10px 20px}
  .nav-link{display:none}
  .nav-btn{display:none}
  .nav-hamburger{display:flex}
}
</style>
</head>
<body>

@include('partials.public-nav', ['showHamburger' => true])

<!-- ════════════ SECTION 1 — HERO ════════════ -->
<section class="hiw-hero">
  <div class="wrap">
    <p class="hiw-hero-eye r">How It Works</p>
    <h1 class="hiw-hero-hed r">
      Search has changed.<br><em>Your visibility hasn't.</em>
    </h1>
    <p class="hiw-hero-sub r">
      AI systems now choose who gets seen.
      Your site either feeds them &mdash; or gets ignored.
    </p>
    <div class="hiw-hero-cta r">
      <a href="{{ route('scan.start') }}" class="btn-primary" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'hero',cta_label:'start_scan'});">Start AI Citation Scan &mdash; $2</a>
      <a href="#system-flow" class="btn-ghost">Or see how the system works &darr;</a>
    </div>
  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ SECTION 2 — SYSTEM FLOW STRIP ════════════ -->
<section class="hiw-section" id="system-flow">
  <div class="wrap">
    <p class="hiw-section-eye r">The System</p>
    <h2 class="hiw-section-hed r">We structure your domain so AI<br><em style="font-style:italic;color:var(--gold-lt)">has to cite you.</em></h2>
    <p class="hiw-section-sub r">
      The AI Citation Engine&trade; builds machine-readable structure across your entire service area &mdash;
      giving AI systems the signals they need to reference, recommend, and cite your business.
    </p>

    <!-- System Visual: Pipeline -->
    <div class="hiw-pipeline r">
      <div class="hiw-pipe-step active">
        <span class="hiw-pipe-you">You enter here</span>
        <div class="hiw-pipe-num">Layer 1</div>
        <div class="hiw-pipe-label">Scan</div>
        <p class="hiw-pipe-desc">See how AI sees your business right now.</p>
        <svg class="hiw-pipe-arrow" viewBox="0 0 14 14" fill="none" aria-hidden="true">
          <path d="M5 2l5 5-5 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="hiw-pipe-step">
        <div class="hiw-pipe-num">Layer 2</div>
        <div class="hiw-pipe-label">Signals</div>
        <p class="hiw-pipe-desc">Map every gap ranked by revenue impact.</p>
        <svg class="hiw-pipe-arrow" viewBox="0 0 14 14" fill="none" aria-hidden="true">
          <path d="M5 2l5 5-5 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="hiw-pipe-step">
        <div class="hiw-pipe-num">Layer 3</div>
        <div class="hiw-pipe-label">Structure</div>
        <p class="hiw-pipe-desc">Build the correction sequence in priority order.</p>
        <svg class="hiw-pipe-arrow" viewBox="0 0 14 14" fill="none" aria-hidden="true">
          <path d="M5 2l5 5-5 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="hiw-pipe-step">
        <div class="hiw-pipe-num">Layer 4</div>
        <div class="hiw-pipe-label">Activation</div>
        <p class="hiw-pipe-desc">Full deployment &mdash; pages, schema, architecture.</p>
        <svg class="hiw-pipe-arrow" viewBox="0 0 14 14" fill="none" aria-hidden="true">
          <path d="M5 2l5 5-5 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="hiw-pipe-step">
        <div class="hiw-pipe-num">Growth</div>
        <div class="hiw-pipe-label">Expansion</div>
        <p class="hiw-pipe-desc">System grows as AI citations compound.</p>
      </div>
    </div>
  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ SECTION 3 — WHAT ACTUALLY HAPPENS ════════════ -->
<section class="hiw-section">
  <div class="wrap">
    <p class="hiw-section-eye r">What Actually Happens</p>
    <h2 class="hiw-section-hed r">What happens when you start</h2>
    <p class="hiw-section-sub r">
      Three moves. That's all it takes to enter the system.
    </p>

    <div class="hiw-happens">
      <div class="hiw-happens-block r">
        <div class="hiw-happens-num">01</div>
        <div class="hiw-happens-title">You enter your website</div>
        <p class="hiw-happens-text">
          We immediately analyze structure, signals, and AI visibility gaps.
          You see results in seconds &mdash; not days.
        </p>
      </div>
      <div class="hiw-happens-block r">
        <div class="hiw-happens-num">02</div>
        <div class="hiw-happens-title">Your scored report arrives</div>
        <p class="hiw-happens-text">
          A 0&ndash;100 citation score with your top gaps and the fastest correction path &mdash; delivered in seconds.
        </p>
      </div>
      <div class="hiw-happens-block r">
        <div class="hiw-happens-num">03</div>
        <div class="hiw-happens-title">You decide what to build</div>
        <p class="hiw-happens-text">
          Each level reveals what competitors already have &mdash; and builds the structure to close the gap.
        </p>
      </div>
    </div>
  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ CTA BLOCK 1 ════════════ -->
<div class="hiw-cta-block">
  <p class="r">You've seen the system. Now see where your site stands.</p>
  <div class="hiw-cta-actions r">
    <a href="{{ route('scan.start') }}" class="btn-primary" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'cta_block_1',cta_label:'start_scan'});">See Where You Stand &mdash; $2</a>
    <a href="/book" class="btn-ghost">Book a Strategy Call &rarr;</a>
  </div>
</div>

<div class="gold-rule"></div>

<!-- ════════════ SECTION 4 — PROGRESSION (LEVELS) ════════════ -->
<section class="hiw-section">
  <div class="wrap">
    <p class="hiw-section-eye r">The Progression</p>
    <h2 class="hiw-section-hed r">Four levels. Each one builds on the last.</h2>
    <p class="hiw-section-sub r">
      These aren't options. They're layers.
      Your $2 scan feeds into every tier. Data compounds. Nothing is repeated.
    </p>

    <div class="hiw-tiers">
      <div class="hiw-tier hiw-tier-entry r">
        <span class="hiw-tier-badge">Most common entry point</span>
        <div class="hiw-tier-level">Level 1</div>
        <div class="hiw-tier-price">$2</div>
        <div class="hiw-tier-name">Citation Scan</div>
        <p class="hiw-tier-desc">See if AI is citing your site. Get a scored report with your biggest visibility gaps.</p>
        <a href="{{ route('scan.start') }}" class="btn-primary" style="margin-top:14px;font-size:.7rem;padding:12px 28px" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'tier_1',cta_label:'start_scan'});">Start Scan &mdash; $2</a>
      </div>

      <div class="hiw-tier r">
        <div class="hiw-tier-level">Level 2</div>
        <div class="hiw-tier-price">$99</div>
        <div class="hiw-tier-name">Signal Expansion</div>
        <p class="hiw-tier-desc">Full analysis of structure, schema, and competitive positioning across AI platforms.</p>
        <a href="{{ route('checkout.signal-expansion') }}" class="btn-ghost" style="margin-top:10px;font-size:.7rem">Learn More &rarr;</a>
      </div>

      <div class="hiw-tier r">
        <div class="hiw-tier-level">Level 3</div>
        <div class="hiw-tier-price">$249</div>
        <div class="hiw-tier-name">Structural Leverage</div>
        <p class="hiw-tier-desc">Deep market mapping with service &times; city coverage strategy and implementation blueprint.</p>
        <a href="{{ route('checkout.structural-leverage') }}" class="btn-ghost" style="margin-top:10px;font-size:.7rem">Learn More &rarr;</a>
      </div>

      <div class="hiw-tier r">
        <div class="hiw-tier-level">Level 4</div>
        <div class="hiw-tier-price">$489</div>
        <div class="hiw-tier-name">System Activation</div>
        <p class="hiw-tier-desc">Full deployment. Pages built, schema installed, domain structured for AI citation at scale.</p>
        <a href="{{ route('checkout.system-activation') }}" class="btn-ghost" style="margin-top:10px;font-size:.7rem">Learn More &rarr;</a>
      </div>
    </div>

    <p class="hiw-progression-note r">
      Start at $2. Expand when the next move is clear.
    </p>
  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ SECTION 5 — MOMENTUM BLOCK ════════════ -->
<section class="hiw-momentum">
  <div class="wrap">
    <h2 class="hiw-momentum-hed r">
      You don't need to decide everything now.
    </h2>
    <p class="hiw-momentum-sub r">
      The first scan shows you exactly what to do next &mdash; and every step after.
    </p>
    <div class="hiw-cta-actions r">
      <a href="{{ route('scan.start') }}" class="btn-primary" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'momentum',cta_label:'start_scan'});">Enter the System &mdash; $2</a>
    </div>
  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ SECTION 6 — FINAL CTA ════════════ -->
<section class="hiw-final">
  <div class="wrap">
    <p class="hiw-section-eye r">Your Move</p>
    <h2 class="hiw-final-hed r">
      AI is already deciding<br><em>who to recommend.</em>
    </h2>
    <p class="hiw-final-sub r">
      The only question is whether your business is included.
    </p>
    <div class="hiw-cta-actions r">
      <a href="{{ route('scan.start') }}" class="btn-primary" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'final_cta',cta_label:'start_scan'});">Start Your Scan &mdash; $2</a>
      <a href="/book" class="btn-ghost">Book a Strategy Call &rarr;</a>
    </div>
    <p class="hiw-final-reassure r">Guided entry.&ensp;Structured rollout.&ensp;Full support.</p>
  </div>
</section>

<!-- ════════════ GLOBAL CTA — funnel capture ════════════ -->
@include('partials.global-cta')

<!-- ════════════ BACK TO TOP ════════════ -->
<button class="btt" id="btt" aria-label="Back to top" style="position:fixed;bottom:24px;right:24px;width:40px;height:40px;border-radius:50%;background:var(--card-bg);border:1px solid var(--card-border);color:var(--gold);cursor:pointer;opacity:0;transform:translateY(10px);transition:opacity .3s,transform .3s;z-index:90;display:flex;align-items:center;justify-content:center">
  <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 4l-8 8h5v8h6v-8h5z"/></svg>
</button>
<style>.btt.show{opacity:1;transform:none}</style>

<!-- ════════════ FOOTER ════════════ -->
<footer>
  @include('components.payment-trust-footer')
  <div class="footer-main" style="text-align:center;padding:20px 0 12px">
    <a href="{{ url('/') }}" class="logo" style="display:inline-flex;text-decoration:none;margin-bottom:8px">
      <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
    </a>
    <span style="display:block;font-size:.62rem;color:var(--muted);letter-spacing:.06em">&copy; 2026 SEO AI Co&trade; &middot; Programmatic AI SEO Systems</span>
  </div>
  <p style="text-align:center;font-size:.72rem;color:var(--muted);margin:6px 0 4px">
    <a href="mailto:hello@seoaico.com" style="color:var(--muted);text-decoration:none">hello@seoaico.com</a>
  </p>
  <p style="text-align:center;font-size:.6rem;color:rgba(168,168,160,.28);max-width:540px;margin:0 auto 8px;line-height:1.65">SEO AI Co&trade; operates the AI Citation Engine&trade; &mdash; structuring content for extraction and citation by AI systems across Google AI Overviews, ChatGPT, and Perplexity. Built for local service businesses competing in active markets.</p>
  <nav style="text-align:center;padding:8px 0 16px;display:flex;justify-content:center;gap:20px">
    <a href="{{ route('privacy') }}" style="font-size:.64rem;color:var(--muted);text-decoration:none;letter-spacing:.06em">Privacy</a>
    <a href="{{ route('terms') }}" style="font-size:.64rem;color:var(--muted);text-decoration:none;letter-spacing:.06em">Terms</a>
    <a href="{{ route('scan.start') }}" style="font-size:.64rem;color:var(--muted);text-decoration:none;letter-spacing:.06em">AI Citation Scan</a>
  </nav>
</footer>

<script>
  /* ── Nav sticky ── */
  const nav = document.getElementById('nav');
  if(nav) window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60), {passive:true});

  /* ── Reveal on scroll ── */
  const items = document.querySelectorAll('.r');
  const io = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
      if(e.isIntersecting){ setTimeout(() => e.target.classList.add('on'), i * 55); io.unobserve(e.target); }
    });
  }, {threshold:.1});
  items.forEach(el => io.observe(el));

  /* ── Back to top ── */
  const btt = document.getElementById('btt');
  if(btt){
    window.addEventListener('scroll', () => btt.classList.toggle('show', scrollY > 600), {passive:true});
    btt.addEventListener('click', () => window.scrollTo({top:0, behavior:'smooth'}));
  }

  /* ── GA page view ── */
  if(typeof gtag==='function'){gtag('event','view_how_it_works',{page_location:window.location.href});}
</script>

@include('partials.public-nav-js')

@include('components.booking-modal')
@include('components.tm-style')
</body>
</html>
