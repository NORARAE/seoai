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
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Book a Session | SEO AI Co™</title>
<meta name="description" content="Choose your next step: start with a $2 AI visibility scan, book a paid consultation after your scan, or proceed to full system activation.">
<link rel="canonical" href="{{ url('/book') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="SEO AI Co™">
<meta property="og:title" content="Book a Session | SEO AI Co™">
<meta property="og:description" content="Start with a $2 scan, book a post-scan consultation, or proceed to full system activation.">
<meta property="og:url" content="{{ url('/book') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
@include('partials.design-system')
@include('partials.public-nav-css')

/* ── Book page tokens ── */
:root{--border:#1a1a1a;--muted:#a8a8a0;--panel:#0f0d08;--panel-soft:#12100a}
body{line-height:1.85}

/* ── Layout ── */
.bk-wrap{max-width:1040px;margin:0 auto;padding:0 32px}

/* ════════════════════════════════════════
   HERO
════════════════════════════════════════ */
.bk-hero{padding:clamp(110px,14vh,152px) 0 56px;text-align:center;position:relative}
.bk-hero-kicker{font-size:.66rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.72);display:inline-block;margin-bottom:14px}
.bk-hero h1{font-family:'Cormorant Garamond',serif;font-size:clamp(2rem,5vw,3.2rem);font-weight:400;line-height:1.2;letter-spacing:-.01em;margin-bottom:18px}
.bk-hero h1 em{font-style:italic;color:var(--gold)}
.bk-hero .hero-sub{font-size:.97rem;color:#b9b3a2;max-width:600px;margin:0 auto;line-height:1.9}
.bk-signal-bg{position:absolute;inset:0;pointer-events:none;z-index:-1;width:100%;height:100%;overflow:visible}
.bk-notice{background:#1a0a0a;border:1px solid rgba(200,80,80,.35);border-radius:8px;padding:14px 20px;margin:28px auto 0;max-width:520px;font-size:.85rem;color:#f0a0a0;text-align:center}

/* ════════════════════════════════════════
   BOOKING CARDS / LADDER
════════════════════════════════════════ */
.bk-ladder{padding:0 0 80px;display:flex;flex-direction:column;gap:16px}
.bk-card{
  border:1px solid rgba(200,168,75,.13);
  border-left:3px solid rgba(200,168,75,.18);
  border-radius:8px;
  padding:32px 36px 28px;
  background:rgba(200,168,75,.015);
  scroll-margin-top:100px;
  transition:border-left-color .22s,background .22s;
}
.bk-card:target,.bk-card.is-active{border-left-color:rgba(200,168,75,.52);background:rgba(200,168,75,.03)}
.bk-card--scan{
  border-left-color:rgba(200,168,75,.34);
  background:linear-gradient(148deg,rgba(200,168,75,.032) 0%,rgba(12,11,9,1) 100%);
}
.bk-card--scan:target,.bk-card--scan.is-active{
  border-left-color:rgba(200,168,75,.6);
  background:linear-gradient(148deg,rgba(200,168,75,.06) 0%,rgba(12,11,9,1) 100%);
}
.bk-card-top{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;margin-bottom:10px}
.bk-card-left{flex:1;min-width:0}
.bk-card-flag{display:flex;align-items:center;gap:8px;margin-bottom:8px}
.bk-card-num{
  font-size:.52rem;letter-spacing:.15em;color:rgba(200,168,75,.44);
  font-family:'DM Sans',sans-serif;font-weight:700;text-transform:uppercase;
  border:1px solid rgba(200,168,75,.2);border-radius:3px;padding:2px 7px;white-space:nowrap;
}
.bk-card-badge{font-size:.52rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(168,168,160,.50);font-family:'DM Sans',sans-serif}
.bk-card-name{font-family:'Cormorant Garamond',serif;font-size:1.52rem;font-weight:400;color:var(--ivory);margin:0;line-height:1.2}
.bk-card-price-block{text-align:right;flex-shrink:0;padding-top:2px}
.bk-card-price{font-family:'Cormorant Garamond',serif;font-size:2.1rem;font-weight:300;color:var(--gold);letter-spacing:-.02em;line-height:1;display:block}
.bk-card-price-note{font-size:.6rem;color:rgba(200,168,75,.46);letter-spacing:.08em;text-transform:uppercase;display:block;margin-top:3px}
.bk-card-divider{border:none;border-top:1px solid rgba(200,168,75,.07);margin:16px 0 18px}
.bk-card-body{font-size:.88rem;color:#bcb6a4;line-height:1.8;margin:0 0 14px}
.bk-card-for{font-size:.82rem;color:#a0a09a;line-height:1.72;padding-left:14px;border-left:2px solid rgba(200,168,75,.14);margin:0 0 14px}
.bk-card-for strong{color:#d0c8b4;font-weight:500}
.bk-card-outcome{font-size:.78rem;color:rgba(200,168,75,.66);margin:0 0 22px;display:flex;gap:8px;align-items:baseline}
.bk-card-outcome::before{content:'\2192';color:rgba(200,168,75,.34);flex-shrink:0}
.bk-card-cta{display:flex;align-items:center;gap:14px;flex-wrap:wrap}
.bk-card-cta-note{font-size:.74rem;color:rgba(168,168,160,.56);letter-spacing:.04em}
.bk-card-footnote{font-size:.7rem;color:rgba(168,168,160,.52);line-height:1.68;margin:14px 0 0;max-width:520px}

/* keep CTA, clarifier, section divider, FAQ, entry gate */
.bk-cta-primary{display:inline-flex;align-items:center;gap:8px;font-family:'DM Sans',sans-serif;font-size:.78rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;text-decoration:none;padding:13px 28px;border-radius:4px;border:none;cursor:pointer;background:var(--gold);color:#080808;transition:background .2s,transform .14s;white-space:nowrap}
.bk-cta-primary:hover{background:var(--gold-lt);transform:translateY(-1px)}
.bk-cta-ghost{display:inline-flex;align-items:center;gap:8px;font-family:'DM Sans',sans-serif;font-size:.78rem;font-weight:500;letter-spacing:.08em;text-transform:uppercase;text-decoration:none;padding:13px 24px;border-radius:4px;cursor:pointer;background:transparent;color:var(--gold);border:1px solid rgba(200,168,75,.24);transition:background .18s,border-color .18s,transform .14s;white-space:nowrap}
.bk-cta-ghost:hover{background:rgba(200,168,75,.06);border-color:rgba(200,168,75,.42);transform:translateY(-1px)}
.bk-cta-link{font-size:.74rem;color:rgba(200,168,75,.6);text-decoration:none;letter-spacing:.06em;text-transform:uppercase;transition:color .2s;cursor:pointer;border:none;background:none;font-family:'DM Sans',sans-serif}
.bk-cta-link:hover{color:var(--gold)}
.bk-qual-note{font-size:.72rem;color:rgba(168,168,160,.5);line-height:1.7;margin-top:10px;max-width:480px}

/* ════════════════════════════════════════
   DIVIDER
════════════════════════════════════════ */
.bk-section-divide{border:none;border-top:1px solid rgba(26,26,26,.6);margin:0 0 48px}

/* ════════════════════════════════════════
   FAQ / CLARITY STRIP
════════════════════════════════════════ */
.bk-clarity{max-width:680px;margin:0 auto;padding:0 0 72px;text-align:center}
.bk-clarity-hed{font-family:'Cormorant Garamond',serif;font-size:1.4rem;font-weight:300;color:var(--ivory);margin-bottom:28px;line-height:1.35}
.bk-qa-list{display:flex;flex-direction:column;gap:0;text-align:left}
.bk-qa{padding:18px 0;border-bottom:1px solid rgba(200,168,75,.07)}
.bk-qa:first-child{border-top:1px solid rgba(200,168,75,.07)}
.bk-qa-q{font-size:.84rem;font-weight:500;color:#e0d8c8;margin-bottom:6px;letter-spacing:.02em}
.bk-qa-a{font-size:.82rem;color:#a8a8a0;line-height:1.78}
.bk-qa-a a{color:rgba(200,168,75,.72);text-decoration:none;transition:color .2s}
.bk-qa-a a:hover{color:var(--gold)}

/* ════════════════════════════════════════
   SYSTEM ENTRY STRIP
════════════════════════════════════════ */
.bk-snav-wrap{padding:0 0 48px}
.bk-snav-label{font-size:.58rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.50);text-align:center;display:block;margin-bottom:12px}
.bk-snav{
  display:flex;align-items:stretch;
  border:1px solid rgba(200,168,75,.14);
  border-top:2px solid rgba(200,168,75,.3);
  border-radius:6px;
  overflow-x:auto;overflow-y:hidden;
  scrollbar-width:none;-webkit-overflow-scrolling:touch;
  scroll-snap-type:x mandatory;
  background:rgba(200,168,75,.02);
  position:relative;
}
.bk-snav::-webkit-scrollbar{display:none}
.bk-snav-item{
  flex:1;min-width:120px;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:18px 10px 16px;
  cursor:pointer;border:none;background:none;
  border-right:1px solid rgba(200,168,75,.08);
  border-top:3px solid transparent;margin-top:-2px;
  text-align:center;text-decoration:none;
  transition:background .18s,border-top-color .18s;
  scroll-snap-align:start;
  -webkit-tap-highlight-color:transparent;
}
.bk-snav-item:last-child{border-right:none}
.bk-snav-item:hover{background:rgba(200,168,75,.05)}
.bk-snav-item.is-active{background:rgba(200,168,75,.07);border-top-color:rgba(200,168,75,.64)}
.bk-snav-num{
  font-size:.52rem;letter-spacing:.14em;color:rgba(200,168,75,.50);
  font-family:'DM Sans',sans-serif;font-weight:700;
  display:block;margin-bottom:5px;text-transform:uppercase;
}
.bk-snav-item.is-active .bk-snav-num{color:rgba(200,168,75,.72)}
.bk-snav-name{
  font-family:'DM Sans',sans-serif;font-size:.78rem;font-weight:600;
  color:rgba(210,206,196,.68);letter-spacing:.03em;
  display:block;margin-bottom:6px;line-height:1.2;
}
.bk-snav-item.is-active .bk-snav-name{color:#ede8de}
.bk-snav-price{
  font-family:'DM Sans',sans-serif;font-size:.7rem;font-weight:500;
  color:rgba(200,168,75,.44);letter-spacing:.05em;display:block;
}
.bk-snav-item.is-active .bk-snav-price{color:rgba(200,168,75,.78)}
.bk-snav-type{
  font-size:.5rem;letter-spacing:.1em;text-transform:uppercase;
  color:rgba(168,168,160,.3);margin-top:4px;display:block;
  font-family:'DM Sans',sans-serif;
}
.bk-snav-item.is-active .bk-snav-type{color:rgba(168,168,160,.5)}
/* scroll-hint shadow on right edge (desktop) */
.bk-snav-outer{position:relative}
.bk-snav-outer::after{
  content:'';position:absolute;top:0;right:0;bottom:0;width:36px;
  background:linear-gradient(90deg,transparent,rgba(8,8,6,.7));
  pointer-events:none;border-radius:0 6px 6px 0;
  opacity:0;transition:opacity .3s;
  z-index:2;
}
.bk-snav-outer.has-overflow::after{opacity:1}

/* ── Snav interactive enhancement ── */
.bk-snav{position:relative}
.bk-snav-item{
  transition:background .18s,border-top-color .18s,box-shadow .22s,transform .18s;
  position:relative;
}
/* modal-trigger nodes: richer hover */
.bk-snav-item[data-layer]{cursor:pointer}
.bk-snav-item[data-layer]:hover{
  background:rgba(200,168,75,.07);
  transform:scaleY(1.025);
  border-top-color:rgba(200,168,75,.38);
}
.bk-snav-item[data-layer]:hover .bk-snav-price{
  color:rgba(200,168,75,.8);
  text-shadow:0 0 18px rgba(200,168,75,.3);
}
.bk-snav-item[data-layer]:hover .bk-snav-name{color:rgba(218,214,204,.92)}
.bk-snav-item[data-layer]:focus-visible{
  outline:none;
  box-shadow:inset 0 0 0 2px rgba(200,168,75,.42);
  background:rgba(200,168,75,.06);
}
/* connector sweep animation on hover */
@keyframes snavLineSweep{
  0%{background-position:-150% 0}
  100%{background-position:150% 0}
}
.bk-snav-item[data-layer]:hover::after{
  content:'';position:absolute;bottom:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.52),transparent);
  background-size:300% 100%;
  animation:snavLineSweep .55s ease-out both;
  pointer-events:none;
}
/* ── Microcopy hint bar ── */
.bk-snav-micro-wrap{
  height:26px;display:flex;align-items:center;justify-content:center;
  margin-bottom:16px;pointer-events:none;
}
.bk-snav-micro{
  font-family:'DM Sans',sans-serif;font-size:.65rem;
  letter-spacing:.06em;color:rgba(200,168,75,.48);
  text-align:center;margin:0;padding:0;
  opacity:0;
  transition:opacity .22s,color .22s;
}
.bk-snav-micro.visible{opacity:1}

/* ── Clarifier strip ── */
.bk-clarifier{
  text-align:center;font-size:.74rem;color:rgba(168,168,160,.58);
  line-height:1.75;padding:0 0 44px;max-width:540px;margin:0 auto;
}
.bk-clarifier strong{color:rgba(200,168,75,.6);font-weight:500}

/* ════════════════════════════════════════
   ENTRY GATE (unchanged — interstitial)
════════════════════════════════════════ */
.bk-entry-gate{position:fixed;inset:0;z-index:9100;background:rgba(0,0,0,.82);backdrop-filter:blur(7px);display:flex;align-items:center;justify-content:center;padding:16px;opacity:0;pointer-events:none;transition:opacity .28s}
.bk-entry-gate[data-open='true']{opacity:1;pointer-events:auto}
.bk-entry-gate-panel{width:100%;max-width:560px;background:linear-gradient(180deg,#11100d 0%,#0d0c09 100%);border:1px solid rgba(200,168,75,.24);border-top:2px solid rgba(200,168,75,.38);border-radius:12px;padding:34px 30px;box-shadow:0 22px 64px rgba(0,0,0,.46),0 0 52px rgba(200,168,75,.05)}
.bk-entry-gate-kicker{font-size:.64rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.74);margin-bottom:10px}
.bk-entry-gate-title{font-family:'Cormorant Garamond',serif;font-size:clamp(1.55rem,3vw,2.15rem);font-weight:400;line-height:1.2;color:var(--ivory);margin-bottom:10px}
.bk-entry-gate-body{font-size:.9rem;line-height:1.82;color:#c2bcab;margin-bottom:12px}
.bk-entry-gate-sub{font-size:.72rem;line-height:1.75;text-transform:uppercase;letter-spacing:.08em;color:rgba(200,168,75,.68);margin-bottom:22px}
.bk-entry-gate-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.bk-entry-gate-primary{display:inline-flex;align-items:center;justify-content:center;min-height:50px;padding:14px 24px;border-radius:8px;background:var(--gold);color:#080808;font-size:.74rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;text-decoration:none;border:1px solid rgba(200,168,75,.52);transition:background .2s,transform .14s}
.bk-entry-gate-primary:hover{background:var(--gold-lt);transform:translateY(-1px)}
.bk-entry-gate-secondary{font-size:.67rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(168,168,160,.55);text-decoration:none;transition:color .2s}
.bk-entry-gate-secondary:hover{color:rgba(200,168,75,.78)}

@include('partials.public-nav-mobile-css')

@media(max-width:900px){
  #nav{padding:14px 20px}#nav.stuck{padding:10px 20px}.nav-link{display:none}
  .nav-btn{display:none}
  .nav-hamburger{display:flex}
}
@media(max-width:768px){
  html,body{overflow-x:hidden}
  .bk-wrap{padding:0 20px;overflow-x:hidden}
  .bk-hero{padding-top:88px;padding-bottom:40px}
  .bk-hero .hero-sub{overflow-wrap:break-word;word-break:break-word;padding:0 4px}
  .bk-cta-primary,.bk-cta-ghost{width:100%;justify-content:center;font-size:.76rem}
  .bk-entry-gate-panel{padding:28px 22px}
  .bk-entry-gate-actions{flex-direction:column;align-items:stretch}
  .bk-entry-gate-primary{text-align:center}
  .bk-entry-gate-secondary{text-align:center}
  .bk-clarity{padding:0 0 52px}
  /* snav mobile: horizontal scroll strip, 3 tiles visible */
  .bk-snav-outer{overflow:hidden}
  .bk-snav{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;overflow-y:hidden}
  .bk-snav-item{flex:0 0 calc(33.33% - 0.5px);min-width:0;scroll-snap-align:start;border-right:1px solid rgba(200,168,75,.08);padding:14px 8px 12px}
  .bk-snav-item:last-child{border-right:none}
  .bk-snav-outer::after{opacity:0}
  .bk-snav-wrap{padding:0 0 36px;overflow:hidden}
  .bk-snav-label{white-space:normal;overflow-wrap:break-word;letter-spacing:.1em;font-size:.54rem;padding:0}
  .bk-clarifier{padding:0 0 32px}
  /* booking cards mobile */
  .bk-ladder{padding:0 0 52px;gap:12px}
  .bk-card{padding:22px 20px 24px}
  .bk-card-top{gap:12px}
  .bk-card-name{font-size:1.25rem}
  .bk-card-price{font-size:1.7rem}
  .bk-card-cta{flex-direction:column;align-items:stretch}
  .bk-card-cta .bk-cta-link{text-align:center}
  .bk-card-body,.bk-card-for,.bk-card-outcome,.bk-card-footnote{word-break:break-word;overflow-wrap:break-word}
  .bk-clarifier{word-break:break-word;overflow-wrap:break-word}
}
@media(max-width:430px){
  .bk-card{padding:18px 16px 20px}
  .bk-card-top{flex-direction:column;gap:8px}
  .bk-card-price-block{text-align:left}
}

/* Readability floor hardening */
.bk-snav-label,
.bk-snav-micro,
.bk-card-badge,
.bk-card-footnote,
.bk-card-cta-note,
.bk-card-price-note,
.bk-clarifier{
  font-size:max(.78rem, 12px);
  line-height:1.6;
}
.bk-card-body,
.bk-card-for,
.bk-card-outcome{color:rgba(237,232,222,.9)}
.bk-card-footnote,
.bk-snav-micro{color:rgba(178,178,170,.82)}

/* ── Phase 16: Booking clarity + interaction pass ───────────────────── */

/* Snav numbers — serif, larger, stronger gold */
.bk-snav-num{font-family:'Cormorant Garamond',serif!important;font-size:.78rem!important;font-weight:400!important;letter-spacing:.13em!important;color:rgba(200,168,75,.74)!important;border:none!important;padding:0!important}
.bk-snav-item.is-active .bk-snav-num{color:rgba(200,168,75,.96)!important}

/* Snav type — high contrast, readable, gold-tinted */
.bk-snav-type{font-size:.58rem!important;letter-spacing:.14em!important;color:rgba(200,168,75,.82)!important;margin-top:5px!important;font-weight:600!important}
.bk-snav-type--guided{color:rgba(140,200,175,.80)!important}
.bk-snav-item.is-active .bk-snav-type{color:rgba(200,168,75,.96)!important}
.bk-snav-item.is-active .bk-snav-type--guided{color:rgba(140,200,175,.96)!important}

/* Snav icon */
.bk-snav-icon{font-style:normal;font-size:.72rem;line-height:1;display:block;margin-bottom:2px;opacity:.80}

/* Card hover + active lift — desktop only */
@media(min-width:769px){
  .bk-card{cursor:pointer}
  .bk-card:hover{transform:translateY(-4px);border-left-color:rgba(200,168,75,.52);background:rgba(200,168,75,.04);box-shadow:0 10px 30px rgba(0,0,0,.34),0 0 20px rgba(200,168,75,.1);transition:transform .22s ease,box-shadow .24s ease,border-left-color .22s,background .22s}
  .bk-card--scan:hover{background:linear-gradient(148deg,rgba(200,168,75,.08) 0%,rgba(12,11,9,1) 100%)}
  .bk-card:active{transform:translateY(-2px) scale(.9993)}
}

/* System grouping divider between self-serve + guided */
.bk-guided-divider{display:flex;align-items:center;gap:12px;margin:6px 0 0;pointer-events:none}
.bk-guided-divider::before,.bk-guided-divider::after{content:'';flex:1;border-top:1px solid rgba(106,175,144,.2)}
.bk-guided-divider-label{font-size:.56rem;letter-spacing:.2em;text-transform:uppercase;font-family:'DM Sans',sans-serif;font-weight:600;color:rgba(140,200,175,.70);white-space:nowrap;padding:3px 10px;border:1px solid rgba(106,175,144,.18);border-radius:999px;background:rgba(106,175,144,.05)}

/* Step 06 gating modal */
.bk-s06-gate{position:fixed;inset:0;z-index:9200;background:rgba(0,0,0,.82);backdrop-filter:blur(7px);display:flex;align-items:center;justify-content:center;padding:16px;opacity:0;pointer-events:none;transition:opacity .28s}
.bk-s06-gate[data-open='true']{opacity:1;pointer-events:auto}
.bk-s06-gate-panel{width:100%;max-width:520px;background:linear-gradient(180deg,#11100d,#0d0c09);border:1px solid rgba(200,168,75,.2);border-top:2px solid rgba(106,175,144,.48);border-radius:12px;padding:34px 30px;box-shadow:0 22px 64px rgba(0,0,0,.46)}
.bk-s06-gate-kicker{font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(140,200,175,.74);margin-bottom:10px}
.bk-s06-gate-title{font-family:'Cormorant Garamond',serif;font-size:clamp(1.38rem,3vw,1.92rem);font-weight:400;line-height:1.22;color:var(--ivory);margin-bottom:12px}
.bk-s06-gate-body{font-size:.88rem;line-height:1.82;color:#c2bcab;margin-bottom:20px}
.bk-s06-gate-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
@media(max-width:600px){.bk-s06-gate-panel{padding:26px 20px}.bk-s06-gate-actions{flex-direction:column;align-items:stretch}}

/* Snav sweep — subtle glow pass (system feels alive) */
@keyframes snavGlowPass{0%{transform:translateX(-100%)}100%{transform:translateX(700%)}}
.bk-snav-sweep{position:absolute;top:0;bottom:0;left:0;width:60px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.065),transparent);animation:snavGlowPass 12s ease-in-out infinite;pointer-events:none;z-index:1}

/* Phase 17: outcome hints + strip */
.bk-snav-hint{display:block;font-size:.52rem;line-height:1.38;color:rgba(168,164,155,.50);margin-top:3px;letter-spacing:.03em}
.bk-snav-item.is-active .bk-snav-hint{color:rgba(200,168,75,.66)}
.bk-outcome-strip{text-align:center;font-size:.8rem;color:rgba(168,164,155,.60);line-height:1.72;padding:0 0 36px;max-width:520px;margin:0 auto;font-style:italic}

/* ──────────────────────────────────────────────────────────────────── */
</style>
@include('partials.clarity')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Service",
      "name": "AI Citation Engine — SEO AI Co™",
      "provider": {
        "@type": "Organization",
        "name": "SEO AI Co",
        "url": "https://seoaico.com"
      },
      "url": "https://seoaico.com/book",
      "description": "Two controlled entry pathways: strategic activation intake or full system activation.",
      "offers": [
        {
          "@type": "Offer",
          "name": "Strategic Activation Intake",
          "price": "500",
          "priceCurrency": "USD",
          "description": "Structural validation and deployment readiness sequencing before full activation."
        },
        {
          "@type": "Offer",
          "name": "Full System Activation",
          "price": "5000",
          "priceCurrency": "USD",
          "description": "Qualified systems proceed directly into full deployment and market-level infrastructure activation."
        }
      ]
    }
  ]
}
</script>
@endverbatim
</head>
<body>

  @include('partials.public-nav', ['showHamburger' => true])

  <main>
    <div class="bk-wrap">

      {{-- Hero --}}
      <section class="bk-hero">
        {{-- Signal-map constellation --}}
        <svg class="bk-signal-bg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 760 360" width="100%" height="100%" fill="none" aria-hidden="true" focusable="false" preserveAspectRatio="xMidYMid slice">
          <defs>
            <radialGradient id="sg" cx="50%" cy="42%" r="58%">
              <stop offset="0%" stop-color="#c8a84b" stop-opacity=".08"/>
              <stop offset="100%" stop-color="#c8a84b" stop-opacity="0"/>
            </radialGradient>
          </defs>
          <ellipse cx="380" cy="148" rx="360" ry="200" fill="url(#sg)"/>
          <circle cx="380" cy="148" r="80" stroke="#c8a84b" stroke-width=".5" stroke-opacity=".07" fill="none"/>
          <circle cx="380" cy="148" r="148" stroke="#c8a84b" stroke-width=".4" stroke-opacity=".05" fill="none"/>
          <circle cx="380" cy="148" r="225" stroke="#c8a84b" stroke-width=".35" stroke-opacity=".035" fill="none"/>
          <line x1="380" y1="148" x2="308" y2="84" stroke="#c8a84b" stroke-width=".9" stroke-opacity=".10"><animate attributeName="stroke-opacity" values=".10;.17;.10" dur="6s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="456" y2="90" stroke="#c8a84b" stroke-width=".9" stroke-opacity=".10"><animate attributeName="stroke-opacity" values=".10;.15;.10" dur="7.7s" begin="1s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="286" y2="172" stroke="#c8a84b" stroke-width=".8" stroke-opacity=".09"><animate attributeName="stroke-opacity" values=".09;.14;.09" dur="9.4s" begin="2s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="480" y2="175" stroke="#c8a84b" stroke-width=".8" stroke-opacity=".09"><animate attributeName="stroke-opacity" values=".09;.14;.09" dur="6.8s" begin="3.5s" repeatCount="indefinite"/></line>
          <line x1="308" y1="84" x2="456" y2="90" stroke="#c8a84b" stroke-width=".5" stroke-opacity=".06"/>
          <line x1="286" y1="172" x2="480" y2="175" stroke="#c8a84b" stroke-width=".45" stroke-opacity=".045"/>
          <circle cx="380" cy="148" r="12" fill="none" stroke="#c8a84b" stroke-width=".5" stroke-opacity="0"><animate attributeName="r" values="5;20;5" dur="3.4s" repeatCount="indefinite"/><animate attributeName="stroke-opacity" values=".30;0;.30" dur="3.4s" repeatCount="indefinite"/></circle>
          <circle cx="380" cy="148" r="3.5" fill="#c8a84b" fill-opacity=".42"><animate attributeName="fill-opacity" values=".42;.72;.42" dur="3.4s" repeatCount="indefinite"/><animate attributeName="r" values="3.5;5.2;3.5" dur="3.4s" repeatCount="indefinite"/></circle>
          <circle cx="308" cy="84" r="2.5" fill="#c8a84b" fill-opacity=".20"><animate attributeName="fill-opacity" values=".20;.38;.20" dur="5.1s" begin="0.8s" repeatCount="indefinite"/></circle>
          <circle cx="456" cy="90" r="2.2" fill="#c8a84b" fill-opacity=".18"><animate attributeName="fill-opacity" values=".18;.35;.18" dur="6s" begin="1.6s" repeatCount="indefinite"/></circle>
          <circle cx="286" cy="172" r="2" fill="#c8a84b" fill-opacity=".16"><animate attributeName="fill-opacity" values=".16;.30;.16" dur="6.8s" begin="2.4s" repeatCount="indefinite"/></circle>
          <circle cx="480" cy="175" r="2" fill="#c8a84b" fill-opacity=".16"><animate attributeName="fill-opacity" values=".16;.28;.16" dur="7.7s" begin="3.2s" repeatCount="indefinite"/></circle>
        </svg>

        <span class="bk-hero-kicker">AI Visibility System</span>
        <h1>Start where your<br><em>visibility is breaking.</em></h1>
        <p class="hero-sub">Your scan reveals where AI cannot fully use your content. Each step fixes a deeper constraint.</p>

        @if(request('payment') === 'cancelled')
        <div class="bk-notice">
          Payment not completed &mdash; your time slot was not held. Choose a step below to try again.
        </div>
        @endif
      </section>

      {{-- System Entry Nav --}}
      <section class="bk-snav-wrap" aria-label="System entry navigation">
        <span class="bk-snav-label">Select your entry point into the system</span>
        <div class="bk-snav-outer" id="bkSnavOuter">
          <nav class="bk-snav" id="bkSnav" role="navigation" aria-label="System levels">
            <span class="bk-snav-sweep" aria-hidden="true"></span>
            <a class="bk-snav-item" href="{{ route('scan.start') }}" data-target="bk-step-01">
              <span class="bk-snav-num">01</span>
              <span class="bk-snav-icon" aria-hidden="true">⚡</span>
              <span class="bk-snav-name">Scan</span>
              <span class="bk-snav-price">$2</span>
              <span class="bk-snav-type">Self-serve</span>
              <span class="bk-snav-hint">Find what&#39;s blocking you</span>
            </a>
            <a class="bk-snav-item" href="{{ route('checkout.signal-expansion') }}" data-target="bk-step-02" data-layer="deep" role="button" aria-haspopup="dialog" tabindex="0">
              <span class="bk-snav-num">02</span>
              <span class="bk-snav-icon" aria-hidden="true">⚡</span>
              <span class="bk-snav-name">Signal</span>
              <span class="bk-snav-price">$99</span>
              <span class="bk-snav-type">Self-serve</span>
              <span class="bk-snav-hint">See why your score is low</span>
            </a>
            <a class="bk-snav-item" href="{{ route('checkout.structural-leverage') }}" data-target="bk-step-03" data-layer="fix" role="button" aria-haspopup="dialog" tabindex="0">
              <span class="bk-snav-num">03</span>
              <span class="bk-snav-icon" aria-hidden="true">⚡</span>
              <span class="bk-snav-name">Leverage</span>
              <span class="bk-snav-price">$249</span>
              <span class="bk-snav-type">Self-serve</span>
              <span class="bk-snav-hint">Fix the highest-impact gaps</span>
            </a>
            <a class="bk-snav-item" href="{{ route('checkout.system-activation') }}" data-target="bk-step-04" data-layer="build" role="button" aria-haspopup="dialog" tabindex="0">
              <span class="bk-snav-num">04</span>
              <span class="bk-snav-icon" aria-hidden="true">⚡</span>
              <span class="bk-snav-name">Activate</span>
              <span class="bk-snav-price">$489</span>
              <span class="bk-snav-type">Self-serve</span>
              <span class="bk-snav-hint">Apply fixes step-by-step</span>
            </a>
            <a class="bk-snav-item" href="{{ route('book.index', ['entry' => 'consultation']) }}" data-target="bk-step-05" data-layer="expand" onclick="return openEntry('consultation');" role="button" aria-haspopup="dialog" tabindex="0">
              <span class="bk-snav-num">05</span>
              <span class="bk-snav-icon" aria-hidden="true">📅</span>
              <span class="bk-snav-name">Expand</span>
              <span class="bk-snav-price">$500</span>
              <span class="bk-snav-type bk-snav-type--guided">Guided</span>
              <span class="bk-snav-hint">Build full system coverage</span>
            </a>
            <a class="bk-snav-item" href="{{ route('book.index', ['entry' => 'activation']) }}" data-target="bk-step-06" onclick="openStep06Gate();return false;" role="button" aria-haspopup="dialog" tabindex="0">
              <span class="bk-snav-num">06</span>
              <span class="bk-snav-icon" aria-hidden="true">📅</span>
              <span class="bk-snav-name">Control</span>
              <span class="bk-snav-price">$5k+</span>
              <span class="bk-snav-type bk-snav-type--guided">Guided</span>
              <span class="bk-snav-hint">Own your entire market</span>
            </a>
          </nav>
        </div>
      </section>
      {{-- Microcopy hint bar --}}
      <div class="bk-snav-micro-wrap" aria-live="polite">
        <p class="bk-snav-micro" id="bkSnavMicro"></p>
      </div>

      {{-- Clarifier strip --}}
      <p class="bk-clarifier">Steps 01&ndash;04 are <strong>self-service</strong> &mdash; pay online, no booking. &nbsp;Steps 05&ndash;06 are <strong>scheduled consultation sessions</strong> &mdash; booked with a specialist.</p>

      <p class="bk-outcome-strip">Each level removes a visibility constraint and increases your AI selection likelihood.</p>

      {{-- Booking Ladder — all 6 offers, always visible --}}
      <section class="bk-ladder" aria-label="Booking options" id="bkLadder">

        {{-- ── Card 01: AI Visibility Scan ── --}}
        <article class="bk-card bk-card--scan" id="bk-step-01">
          <div class="bk-card-top">
            <div class="bk-card-left">
              <div class="bk-card-flag">
                <span class="bk-card-num">01</span>
                <span class="bk-card-badge">Scan &middot; Self-serve &middot; No booking</span>
              </div>
              <h3 class="bk-card-name">AI Visibility Scan</h3>
            </div>
            <div class="bk-card-price-block">
              <span class="bk-card-price">$2</span>
              <span class="bk-card-price-note">one-time</span>
            </div>
          </div>
          <hr class="bk-card-divider">
          <p class="bk-card-body">Instant baseline analysis of your AI visibility across ChatGPT, Perplexity, and Google AI. Shows exactly where you appear, where you&rsquo;re missing, and what&rsquo;s blocking your citations. Two minutes. Instant output.</p>
          <p class="bk-card-for"><strong>For:</strong> Any business that wants to understand their current AI search position before investing further. No prior knowledge needed.</p>
          <p class="bk-card-outcome">Baseline score, top blocker, and immediate next-step guidance &mdash; available instantly inside your dashboard.</p>
          <div class="bk-card-cta">
            <a href="{{ route('scan.start') }}" class="bk-cta-primary">Run Scan &mdash; $2 &rarr;</a>
            <span class="bk-card-cta-note">Instant &middot; No booking &middot; 2 min</span>
          </div>
        </article>

        {{-- ── Card 02: Signal Analysis ── --}}
        <article class="bk-card" id="bk-step-02">
          <div class="bk-card-top">
            <div class="bk-card-left">
              <div class="bk-card-flag">
                <span class="bk-card-num">02</span>
                <span class="bk-card-badge">Signal &middot; Self-serve checkout</span>
              </div>
              <h3 class="bk-card-name">Signal Analysis</h3>
            </div>
            <div class="bk-card-price-block">
              <span class="bk-card-price">$99</span>
              <span class="bk-card-price-note">one-time</span>
            </div>
          </div>
          <hr class="bk-card-divider">
          <p class="bk-card-body">Dashboard-based signal diagnosis that explains WHY your score is what it is. You get a signal-by-signal breakdown, clear gap visibility, and priority context without waiting on external delivery.</p>
          <p class="bk-card-for"><strong>For:</strong> Businesses who have scanned and want diagnostic clarity before acting. Builds directly on your baseline scan data.</p>
          <p class="bk-card-outcome">Score explanation, signal-gap identification, and immediate in-dashboard access.</p>
          <div class="bk-card-cta">
            <a href="{{ route('checkout.signal-expansion') }}" class="bk-cta-primary">Unlock Signal Analysis &mdash; $99 &rarr;</a>
          </div>
        </article>

        {{-- ── Card 03: Action Plan ── --}}
        <article class="bk-card" id="bk-step-03">
          <div class="bk-card-top">
            <div class="bk-card-left">
              <div class="bk-card-flag">
                <span class="bk-card-num">03</span>
                <span class="bk-card-badge">Leverage &middot; Self-serve checkout</span>
              </div>
              <h3 class="bk-card-name">Action Plan</h3>
            </div>
            <div class="bk-card-price-block">
              <span class="bk-card-price">$249</span>
              <span class="bk-card-price-note">one-time</span>
            </div>
          </div>
          <hr class="bk-card-divider">
          <p class="bk-card-body">A structure-first build sequence that shows WHAT to implement next. It prioritizes expansion order, fixes key architecture gaps, and maps the highest-impact path forward inside your dashboard workflow.</p>
          <p class="bk-card-for"><strong>For:</strong> Businesses ready to move from diagnosis into prioritized implementation.</p>
          <p class="bk-card-outcome">Prioritized structure roadmap and expansion sequence with immediate dashboard access.</p>
          <div class="bk-card-cta">
            <a href="{{ route('checkout.structural-leverage') }}" class="bk-cta-primary">Fix Structure &mdash; $249 &rarr;</a>
          </div>
        </article>

        {{-- ── Card 04: Guided Execution ── --}}
        <article class="bk-card" id="bk-step-04">
          <div class="bk-card-top">
            <div class="bk-card-left">
              <div class="bk-card-flag">
                <span class="bk-card-num">04</span>
                <span class="bk-card-badge">Activate &middot; Self-serve checkout</span>
              </div>
              <h3 class="bk-card-name">Guided Execution</h3>
            </div>
            <div class="bk-card-price-block">
              <span class="bk-card-price">$489</span>
              <span class="bk-card-price-note">one-time</span>
            </div>
          </div>
          <hr class="bk-card-divider">
          <p class="bk-card-body">Step-by-step activation roadmap inside your dashboard. Covers remaining structural gaps, activation order, and rollout guidance so execution stays clear and compounding.</p>
          <p class="bk-card-for"><strong>For:</strong> Businesses that want comprehensive infrastructure deployed in one pass. Combines all signal, structure, and entity work.</p>
          <p class="bk-card-outcome">Complete activation roadmap in your dashboard with clear, step-by-step implementation guidance.</p>
          <div class="bk-card-cta">
            <a href="{{ route('checkout.system-activation') }}" class="bk-cta-primary">Activate System &mdash; $489 &rarr;</a>
          </div>
        </article>

        {{-- ── Guided system entry divider ── --}}
        <div class="bk-guided-divider" aria-hidden="true"><span class="bk-guided-divider-label">Guided System Entry</span></div>

        {{-- ── Card 05: AI Visibility Consultation ── --}}
        <article class="bk-card" id="bk-step-05">
          <div class="bk-card-top">
            <div class="bk-card-left">
              <div class="bk-card-flag">
                <span class="bk-card-num">05</span>
                <span class="bk-card-badge">Expand &middot; Scheduled consultation</span>
              </div>
              <h3 class="bk-card-name">AI Visibility Consultation</h3>
            </div>
            <div class="bk-card-price-block">
              <span class="bk-card-price">$500</span>
              <span class="bk-card-price-note">60 min session</span>
            </div>
          </div>
          <hr class="bk-card-divider">
          <p class="bk-card-body">A paid 60-minute working session. We interpret your results, identify the highest-leverage gaps, and build a clear activation sequence. You leave with a specific plan and a recommendation for your exact next move. This session also determines whether full deployment (step 06) is the right fit.</p>
          <p class="bk-card-for"><strong>For:</strong> Businesses with scan results in hand who want expert interpretation and a prioritized roadmap. Also the qualification path for step 06.</p>
          <p class="bk-card-outcome">Human-led strategy session with a custom roadmap and clear next-tier recommendation.</p>
          <div class="bk-card-cta">
            <a href="{{ route('book.index', ['entry' => 'consultation']) }}" class="bk-cta-primary" onclick="return openEntry('consultation');">Book Consultation &mdash; $500 &rarr;</a>
            <a href="{{ route('scan.start') }}" class="bk-cta-link">Haven&rsquo;t scanned? Start at step 01 &rarr;</a>
          </div>
          <p class="bk-card-footnote" style="color:rgba(140,200,175,.72);margin-bottom:8px"><strong>60-minute strategy session.</strong> Required before full system activation at Step 06.</p>
          <p class="bk-card-footnote" style="margin-top:0">Most users reach this step after completing Levels 1&ndash;4. $500 charged at booking. Intake questions sent after you book. The $2 scan cost is not credited toward this session.</p>
        </article>

        {{-- ── Card 06: Full System Activation ── --}}
        <article class="bk-card" id="bk-step-06">
          <div class="bk-card-top">
            <div class="bk-card-left">
              <div class="bk-card-flag">
                <span class="bk-card-num">06</span>
                <span class="bk-card-badge">Control &middot; Done-for-you &middot; Qualified buyers</span>
              </div>
              <h3 class="bk-card-name">Full System Activation</h3>
            </div>
            <div class="bk-card-price-block">
              <span class="bk-card-price">$5k&ndash;$15k+</span>
              <span class="bk-card-price-note">50% deposit</span>
            </div>
          </div>
          <hr class="bk-card-divider">
          <p class="bk-card-body">We build and deploy your complete AI visibility infrastructure &mdash; entity architecture, content signal network, citation positioning, and market-level coverage. You own the outcome. We execute it.</p>
          <p class="bk-card-for"><strong>For:</strong> Businesses ready for full implementation. Direct access available for returning or referred qualified buyers. All others arrive through step 05.</p>
          <p class="bk-card-outcome">Full infrastructure build, deployment across target markets, and ownership of your AI visibility position at scale.</p>
          <div class="bk-card-cta">
            <a href="{{ route('book.index', ['entry' => 'activation']) }}" class="bk-cta-primary" onclick="openStep06Gate();return false;">Start Full Activation &rarr;</a>
            <a href="{{ route('book.index', ['entry' => 'consultation']) }}" class="bk-cta-link" onclick="return openEntry('consultation');">Not qualified? Book step 05 first &rarr;</a>
          </div>
          <p class="bk-card-footnote">Activation engagements require a qualification review. 50% deposit secures your start date; remainder due at kickoff. Most buyers arrive through step 05.</p>
        </article>

      </section>
      {{-- Clarity / FAQ --}}
      <hr class="bk-section-divide">
      <section class="bk-clarity">
        <h2 class="bk-clarity-hed">Common questions before booking</h2>
        <div class="bk-qa-list">
          <div class="bk-qa">
            <p class="bk-qa-q">Do I need a scan before booking a consultation?</p>
            <p class="bk-qa-a">No—but it helps. The consultation is more productive when you already have your baseline score. If you haven’t scanned yet, you can <a href="{{ route('scan.start') }}">start here for $2</a> and it takes about 2 minutes.</p>
          </div>
          <div class="bk-qa">
            <p class="bk-qa-q">Is the consultation free?</p>
            <p class="bk-qa-a">No. The AI Visibility Consultation is a paid 60-minute working session at $500. It is not a sales call&mdash;you get live expert guidance, a prioritized roadmap, and a clear activation recommendation for your business.</p>
          </div>
          <div class="bk-qa">
            <p class="bk-qa-q">What happens after the consultation?</p>
            <p class="bk-qa-a">You’ll leave with a clear recommendation. If full activation is the right fit, we’ll scope it during the session. If not, you keep the roadmap and can act on it independently.</p>
          </div>
          <div class="bk-qa">
            <p class="bk-qa-q">What is Full System Activation?</p>
            <p class="bk-qa-a">It’s a complete done-for-you engagement: we build your entity architecture, content signal network, and AI citation positioning across your market. Engagements start at $5,000; complex or multi-market builds start at $15,000+.</p>
          </div>
          <div class="bk-qa">
            <p class="bk-qa-q">Can I skip straight to Full Activation?</p>
            <p class="bk-qa-a">Yes, if you’re a qualified buyer or referred. Otherwise, we recommend the consultation first—it ensures activation is scoped correctly and compounds faster.</p>
          </div>
        </div>
      </section>

    </div>
  </main>

  @include('components.layer-modal')
  @include('components.booking-modal')
  {{-- Step 06 qualification gate --}}
  <div class="bk-s06-gate" id="bkS06Gate" data-open="false" role="dialog" aria-modal="true" aria-labelledby="bkS06GateTitle">
    <div class="bk-s06-gate-panel">
      <p class="bk-s06-gate-kicker">Qualification Required</p>
      <h2 class="bk-s06-gate-title" id="bkS06GateTitle">Full system control is only available after consultation.</h2>
      <p class="bk-s06-gate-body">Step 06 is a qualification-gated engagement. To access it, you must first complete the AI Visibility Consultation (Step 05), where we validate fit and scope the deployment. Most buyers arrive at Step 06 after a strategy session confirms readiness.</p>
      <div class="bk-s06-gate-actions">
        <a href="{{ route('book.index', ['entry' => 'consultation']) }}" class="bk-cta-primary" onclick="closeStep06Gate();return openEntry('consultation');">Book Consultation First &mdash; $500 &rarr;</a>
        <button type="button" class="bk-cta-link" onclick="closeStep06Gate()">Maybe later</button>
      </div>
    </div>
  </div>
  <div class="bk-entry-gate" id="entryGate" data-open="false" role="dialog" aria-modal="true" aria-labelledby="entryGateTitle">
    <div class="bk-entry-gate-panel">
      <p class="bk-entry-gate-kicker">Before you schedule</p>
      <h2 class="bk-entry-gate-title" id="entryGateTitle">A quick note on the consultation</h2>
      <p class="bk-entry-gate-body">The AI Visibility Consultation is a paid, working session — not a sales call. You'll leave with a prioritized roadmap and a specific recommendation for your next move.</p>
      <p class="bk-entry-gate-sub">$500 &middot; 60 minutes &middot; Full prepayment at booking</p>
      <div class="bk-entry-gate-actions">
        <button type="button" id="entryGateProceed" class="bk-entry-gate-primary">Continue to Scheduling</button>
        <a href="{{ url('/dashboard') }}" class="bk-entry-gate-secondary">Go back</a>
      </div>
    </div>
  </div>
  @include('partials.public-footer')
  @include('partials.back-to-top')

  <script>
  /* ── System Entry Strip: scroll-to card + scroll-spy ── */
  (function(){
    var navItems = document.querySelectorAll('.bk-snav-item');
    var snavOuter = document.getElementById('bkSnavOuter');
    var snavEl = document.getElementById('bkSnav');

    /* overflow shadow detection */
    function checkOverflow(){
      if(!snavEl||!snavOuter) return;
      snavOuter.classList.toggle('has-overflow', snavEl.scrollWidth > snavEl.clientWidth + 4);
    }
    checkOverflow();
    window.addEventListener('resize', checkOverflow, {passive:true});

    /* active tile highlight */
    function setActive(el){
      navItems.forEach(function(i){ i.classList.remove('is-active'); });
      if(el) el.classList.add('is-active');
    }

    /* scroll to card on tile click */
    navItems.forEach(function(item){
      item.addEventListener('click', function(e){
        // Modal-trigger nodes: layer-modal JS handles navigation; just update active state
        if(item.hasAttribute('data-layer')){ setActive(item); return; }
        // Direct external links (scan.start etc): allow browser to navigate
        var href = item.getAttribute('href') || '';
        if(href && href.charAt(0) !== '#'){ setActive(item); return; }
        // Anchor scroll behavior
        e.preventDefault();
        var targetId = item.getAttribute('data-target');
        var target = document.getElementById(targetId);
        if(!target) return;
        setActive(item);
        var offset = 100;
        var top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({top: Math.max(0, top), behavior:'smooth'});
      });
    });

    /* microcopy: update hint line on hover/focus */
    var microEl = document.getElementById('bkSnavMicro');
    var MICRO = {
      'deep':    'Baseline scan intelligence included. Expanded signal analysis across your full domain.',
      'fix':     'Move from signal diagnosis into structured fix planning.',
      'build':   'Translate findings into implementation-ready structure.',
      'expand':  'Extend into broader market coverage.',
      'managed': 'Shift into guided system oversight and growth.'
    };
    if(microEl){
      navItems.forEach(function(item){
        var layer = item.getAttribute('data-layer');
        if(!layer || !MICRO[layer]) return;
        function showMicro(){ microEl.textContent = MICRO[layer]; microEl.classList.add('visible'); }
        function hideMicro(){ microEl.classList.remove('visible'); }
        item.addEventListener('mouseenter', showMicro);
        item.addEventListener('mouseleave', hideMicro);
        item.addEventListener('focus', showMicro);
        item.addEventListener('blur', hideMicro);
      });
    }

    /* scroll-spy: update active tile as user scrolls through cards */
    if('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(entry.isIntersecting){
            var id = entry.target.id;
            navItems.forEach(function(item){
              if(item.getAttribute('data-target') === id) setActive(item);
            });
          }
        });
      },{rootMargin:'-15% 0px -65% 0px',threshold:0});
      navItems.forEach(function(item){
        var target = document.getElementById(item.getAttribute('data-target'));
        if(target) io.observe(target);
      });
    }

    /* init: activate first tile */
    if(navItems.length) setActive(navItems[0]);
  })();
  </script>
  <script>
  const nav = document.getElementById('nav');
  if(nav) window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60));
  const entryGate = document.getElementById('entryGate');
  const entryGateProceed = document.getElementById('entryGateProceed');
  const bkS06Gate = document.getElementById('bkS06Gate');
  let pendingEntryKey = null;

  function openEntryGate(entryKey) {
    if (!entryGate) return;
    pendingEntryKey = entryKey;
    entryGate.dataset.open = 'true';
  }

  function closeEntryGate() {
    if (!entryGate) return;
    entryGate.dataset.open = 'false';
  }

  function openStep06Gate() {
    if (bkS06Gate) bkS06Gate.dataset.open = 'true';
  }

  function closeStep06Gate() {
    if (bkS06Gate) bkS06Gate.dataset.open = 'false';
  }

  if (bkS06Gate) {
    bkS06Gate.addEventListener('click', function(evt) {
      if (evt.target === bkS06Gate) closeStep06Gate();
    });
  }

  function triggerEntryScheduling(entryKey) {
    const entry = bookingEntries[entryKey];
    if (!entry || !entry.id) {
      window.location.href = '{{ route('book.index', ['entry' => 'consultation']) }}';
      return;
    }
    window._bkPending = entry;
    window.dispatchEvent(new CustomEvent('open-booking', { detail: entry }));
  }

  if (entryGateProceed) {
    entryGateProceed.addEventListener('click', function () {
      const key = pendingEntryKey || 'consultation';
      const entry = bookingEntries[key];
      closeEntryGate();
      if (entry && entry.id) {
        triggerEntryScheduling(key);
      } else {
        // Fallback: navigate to same page with ?entry param which triggers server-side booking
        window.location.href = '{{ route('book.index') }}' + '?entry=' + key;
      }
    });
  }

  if (entryGate) {
    entryGate.addEventListener('click', function (evt) {
      if (evt.target === entryGate) closeEntryGate();
    });
  }

  document.addEventListener('keydown', function (evt) {
    if (evt.key === 'Escape') {
      if (bkS06Gate && bkS06Gate.dataset.open === 'true') { closeStep06Gate(); return; }
      if (entryGate && entryGate.dataset.open === 'true') closeEntryGate();
    }
  });

  const bookingEntries = {
    consultation: {
      id: {{ ($highTicketTypes ?? collect())->get('consultation')?->id ?? 0 }},
      duration: {{ ($highTicketTypes ?? collect())->get('consultation')?->duration_minutes ?? 60 }},
      name: 'Strategic Activation Entry',
      isFree: false,
      paymentStructure: 'full_prepay'
    },
    activation: {
      id: {{ ($highTicketTypes ?? collect())->get('activation')?->id ?? 0 }},
      duration: {{ ($highTicketTypes ?? collect())->get('activation')?->duration_minutes ?? 60 }},
      name: 'Full System Activation',
      isFree: false,
      paymentStructure: '50_50_split'
    }
  };

  function openEntry(key) {
    const entry = bookingEntries[key];

    // Step 06 is always gated — show qualification modal regardless
    if (key === 'activation') {
      openStep06Gate();
      return false;
    }

    // Step 05: always show entry gate first for clarity
    if (key === 'consultation') {
      openEntryGate(key);
      return false;    }

    if (!entry || !entry.id) {
      return true;
    }
    triggerEntryScheduling(key);
    return false;
  }

  // Delay open-booking dispatch until Alpine has initialized all components.
  // Dispatching synchronously during body parse misses the @open-booking.window listener.
  document.addEventListener('alpine:initialized', function() {
    var requestedEntry = new URLSearchParams(window.location.search).get('entry');
    if (requestedEntry && bookingEntries[requestedEntry] && bookingEntries[requestedEntry].id) {
      window._bkPending = bookingEntries[requestedEntry];
      window.dispatchEvent(new CustomEvent('open-booking', { detail: bookingEntries[requestedEntry] }));
    }
  });
  </script>
  @include('partials.public-nav-js')
  <script>
  if(typeof gtag==='function'){gtag('event','view_book',{page_location:window.location.href});}
  </script>
@include('components.tm-style')
</body>
</html>
