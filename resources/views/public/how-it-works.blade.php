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
.hiw-hero::before{
  content:'';position:absolute;inset:8% 12% auto 12%;height:220px;pointer-events:none;
  background:
    radial-gradient(circle at 50% 50%,rgba(200,168,75,.08),transparent 62%),
    linear-gradient(to right,rgba(200,168,75,.08) 1px,transparent 1px),
    linear-gradient(to bottom,rgba(200,168,75,.07) 1px,transparent 1px);
  background-size:auto,28px 28px,28px 28px;
  opacity:.5;
  mask-image:linear-gradient(to bottom,rgba(0,0,0,.85),transparent 90%);
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
.hiw-hero-promise{
  font-size:.72rem;letter-spacing:.11em;text-transform:uppercase;
  color:rgba(200,168,75,.58);margin:14px auto 0;max-width:640px;
}
.hiw-hero-cta{display:inline-flex;gap:16px;flex-wrap:wrap;justify-content:center}
.hiw-system-meta{
  margin:0 auto 26px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;
}
.hiw-system-meta span{
  font-size:.56rem;letter-spacing:.15em;text-transform:uppercase;
  border:1px solid rgba(200,168,75,.22);color:rgba(200,168,75,.72);
  padding:6px 10px;border-radius:999px;background:rgba(200,168,75,.03);
}

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

/* ── CTA actions ── */
.hiw-cta-actions{display:flex;align-items:center;justify-content:center;gap:16px;flex-wrap:wrap}

/* ── Transition block ── */
.hiw-transition{
  text-align:center;padding:clamp(56px,8vw,96px) 0 clamp(52px,7vw,84px);
  max-width:520px;margin:0 auto;position:relative;
}
.hiw-transition::before{
  content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
  width:320px;height:180px;border-radius:50%;
  background:radial-gradient(ellipse,rgba(200,168,75,.06) 0%,transparent 70%);
  pointer-events:none;
}
.hiw-transition-calm{
  font-size:.78rem;color:var(--muted);letter-spacing:.03em;
  line-height:1.7;margin-bottom:26px;transition-delay:.05s;
}
.hiw-transition-action{
  font-family:'Cormorant Garamond',serif;font-weight:300;
  font-size:clamp(1.6rem,3.4vw,2.4rem);color:#f0ece3;
  line-height:1.2;margin-bottom:30px;transition-delay:.2s;
}
.hiw-transition-signal{
  font-size:.7rem;letter-spacing:.12em;text-transform:uppercase;
  color:rgba(200,168,75,.48);margin-bottom:36px;transition-delay:.35s;
}
.hiw-transition-lead{
  font-size:.72rem;color:var(--muted);letter-spacing:.04em;
  opacity:.55;padding-bottom:8px;transition-delay:.48s;
}

/* ── System visual (pipeline) ── */
.hiw-pipeline{
  display:flex;align-items:stretch;gap:0;max-width:960px;margin:0 auto 40px;
  position:relative;
}
.hiw-pipeline::before{
  content:'';position:absolute;inset:-12px;pointer-events:none;border-radius:14px;
  background:
    linear-gradient(to right,rgba(200,168,75,.06) 1px,transparent 1px),
    linear-gradient(to bottom,rgba(200,168,75,.05) 1px,transparent 1px);
  background-size:24px 24px;
  opacity:.28;
}
.hiw-pipe-step{
  flex:1;text-align:center;padding:24px 12px 20px;position:relative;
  border:1px solid var(--card-border);background:var(--card-bg);
  transition:border-color .3s,background .3s;
}
.hiw-pipe-step::after{
  content:'';position:absolute;top:14px;right:12px;width:8px;height:8px;border-radius:50%;
  background:rgba(200,168,75,.36);box-shadow:0 0 0 0 rgba(200,168,75,.35);
  animation:nodePulse 2.8s ease-out infinite;
}
.hiw-pipe-step:first-child{border-radius:10px 0 0 10px}
.hiw-pipe-step:last-child{border-radius:0 10px 10px 0}
.hiw-pipe-step:last-child::after{display:none}
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
.hiw-pipe-desc{font-size:.72rem;color:var(--muted);line-height:1.6}
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
  display:grid;grid-template-columns:repeat(2,1fr);
  grid-auto-rows:1fr;align-items:stretch;
  gap:1px;max-width:960px;margin:0 auto;
  background:rgba(200,168,75,.12); /* fills 1px gap = connector seam */
  border:1px solid rgba(200,168,75,.14);
  border-radius:12px;overflow:hidden;
  position:relative;
}
.hiw-tiers::before{
  content:'';
  position:absolute;inset:-1px;
  background:linear-gradient(120deg,transparent 18%,rgba(200,168,75,.1) 50%,transparent 82%);
  opacity:0;
  transform:translateX(-24%);
  transition:opacity .32s ease;
  pointer-events:none;
  z-index:1;
}
/* center hub node — pinned at grid crosshair; grid-auto-rows:1fr ensures equal rows so top:50% = seam */
.hiw-tiers::after{
  content:'';position:absolute;top:50%;left:50%;
  transform:translate(-50%,-50%);
  width:22px;height:22px;border-radius:50%;
  background:var(--bg,#080806);
  border:1px solid rgba(200,168,75,.46);
  box-shadow:0 0 0 4px rgba(200,168,75,.05),0 0 20px rgba(200,168,75,.2);
  z-index:4;pointer-events:none;
}
.hiw-tier{
  background:var(--card-bg);border:none;
  border-radius:0;padding:30px 24px 26px;text-align:center;
  transition:background .28s,transform .24s,box-shadow .24s,filter .24s;
  position:relative;z-index:1;
  display:flex;flex-direction:column;justify-content:center;align-items:center;
  min-height:254px;
}
.hiw-tier:hover{background:rgba(200,168,75,.04);transform:none}
/* Ladder animation — alive, progressive, directional */
@keyframes hiwNodePulse{
  0%,100%{box-shadow:0 0 0 4px rgba(200,168,75,.05),0 0 20px rgba(200,168,75,.2)}
  50%     {box-shadow:0 0 0 6px rgba(200,168,75,.1), 0 0 32px rgba(200,168,75,.38)}
}
.hiw-tiers::after{animation:hiwNodePulse 3.6s ease-in-out infinite}
@keyframes hiwTierLift{
  to{transform:translateY(-3px)}
}
.hiw-tier:hover{
  background:rgba(200,168,75,.06);
  transform:translateY(-4px);
  box-shadow:0 12px 38px rgba(200,168,75,.16);
  z-index:2;
}
.hiw-tier:hover .hiw-tier-price{color:rgba(228,198,112,1)}
.hiw-tiers:hover .hiw-tier{filter:brightness(.94)}
.hiw-tiers:hover .hiw-tier:hover{filter:brightness(1.05)}
/* Connector seam brightens when any tile is hovered — CSS-only trick via :has() */
@supports selector(:has(*)){
  .hiw-tiers:has(.hiw-tier:hover){
    background:rgba(200,168,75,.28);
    transition:background .25s;
  }
  .hiw-tiers:has(.hiw-tier:hover)::before{
    opacity:.62;
    animation:hiwEnergyFlow 2.4s ease-in-out infinite;
  }
}
@keyframes hiwEnergyFlow{
  0%{transform:translateX(-24%)}
  50%{transform:translateX(0%)}
  100%{transform:translateX(24%)}
}
.hiw-tier-price{
  font-family:'Cormorant Garamond',serif;font-size:1.52rem;
  color:var(--gold-lt);font-weight:400;margin-bottom:8px;
  line-height:1;
}
.hiw-tier:not(.hiw-tier-entry) .hiw-tier-price{font-size:2.08rem}
.hiw-tier-name{
  font-size:.78rem;letter-spacing:.145em;text-transform:uppercase;
  color:var(--ivory);margin-bottom:12px;
}
.hiw-tier-desc{font-size:.84rem;color:var(--muted);line-height:1.72;max-width:31ch;margin:0 auto}
.hiw-tier-arrow{
  color:rgba(200,168,75,.25);font-size:.7rem;margin:0 auto;
  display:flex;align-items:center;justify-content:center;padding:8px 0;
}
@media(max-width:700px){
  .hiw-tier-arrow{transform:rotate(90deg)}
}

/* ── Flow sequence indicator strip ── */
.hiw-flow-seq{
  display:flex;align-items:center;justify-content:center;
  gap:8px;flex-wrap:wrap;margin-bottom:20px;
  overflow:hidden; /* prevent bleed */
}
.hiw-flow-seq-step{
  font-size:.52rem;letter-spacing:.16em;text-transform:uppercase;
  color:rgba(200,168,75,.46);font-family:'DM Sans',sans-serif;
  padding:4px 10px;border:1px solid rgba(200,168,75,.18);border-radius:99px;
  transition:color .2s,border-color .2s;
}
.hiw-flow-seq-step.active{color:rgba(200,168,75,.7);border-color:rgba(200,168,75,.26)}
.hiw-flow-seq-arrow{
  color:rgba(200,168,75,.2);font-size:.58rem;display:inline-block;
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

.hiw-process-meta{
  display:flex;justify-content:center;gap:10px;flex-wrap:wrap;
  margin:-8px auto 26px;
}
.hiw-process-meta span{
  font-size:.52rem;letter-spacing:.16em;text-transform:uppercase;
  border:1px solid rgba(200,168,75,.2);border-radius:999px;
  color:rgba(200,168,75,.68);padding:6px 10px;background:rgba(200,168,75,.03);
}

/* ── Level highlight (entry layer) ── */
.hiw-tier.hiw-tier-entry{
  background:linear-gradient(148deg,rgba(200,168,75,.045) 0%,var(--card-bg) 60%);
  position:relative;
}
.hiw-tier.hiw-tier-entry::before{
  content:'';position:absolute;top:0;left:0;right:0;height:2px;
  background:linear-gradient(90deg,rgba(200,168,75,.46),rgba(200,168,75,.1));
  z-index:2;
}
.hiw-tier.hiw-tier-entry:hover{
  background:linear-gradient(148deg,rgba(200,168,75,.07) 0%,var(--card-bg) 60%);
}
.hiw-tier-badge{
  display:inline-block;
  font-size:.5rem;letter-spacing:.18em;text-transform:uppercase;
  color:var(--gold);background:var(--bg);
  padding:2px 10px;border:1px solid rgba(200,168,75,.22);border-radius:20px;
  margin-bottom:10px;
}
.hiw-tier-level{
  display:inline-flex;align-items:center;justify-content:center;
  min-height:24px;padding:0 10px;
  font-size:.56rem;letter-spacing:.19em;text-transform:uppercase;
  color:rgba(224,198,114,.92);margin-bottom:8px;
  border:1px solid rgba(200,168,75,.38);
  border-radius:999px;
  background:linear-gradient(180deg,rgba(200,168,75,.16),rgba(200,168,75,.06));
  box-shadow:inset 0 0 0 1px rgba(200,168,75,.08),0 0 18px rgba(200,168,75,.08);
}
.hiw-progression-note{
  text-align:center;font-size:.86rem;color:var(--muted);
  max-width:480px;margin:28px auto 0;line-height:1.78;
}
.hiw-progression-note strong{color:var(--ivory);font-weight:400}

/* ── Tier inline CTA link (replaces btn-ghost buttons) ── */
.hiw-tier-link{
  display:inline-flex;align-items:center;gap:5px;
  position:relative;
  font-family:'DM Sans',sans-serif;font-size:.78rem;font-weight:600;
  letter-spacing:.11em;text-transform:uppercase;text-decoration:none;
  color:rgba(232,205,122,.92);
  margin-top:16px;
  padding-bottom:3px;
  cursor:pointer;
  transition:color .2s,filter .2s;
}
.hiw-tier-link::after{
  content:'';
  position:absolute;
  left:0;right:0;bottom:0;
  height:1px;
  background:linear-gradient(90deg,rgba(200,168,75,.52),rgba(228,198,112,.82));
  transform:scaleX(.24);
  transform-origin:left center;
  transition:transform .24s ease,opacity .24s ease;
  opacity:.72;
}
.hiw-tier-link .hiw-tier-link-arrow{
  display:inline-block;transition:transform .22s;
}
.hiw-tier-link:hover{color:rgba(242,217,134,.98);filter:drop-shadow(0 0 10px rgba(200,168,75,.3))}
.hiw-tier-link:hover::after{transform:scaleX(1);opacity:.98}
.hiw-tier-link:hover .hiw-tier-link-arrow{transform:translateX(3px)}

/* ── Tier stagger animation delays ── */
.hiw-tiers .hiw-tier:nth-child(1){transition-delay:0s}
.hiw-tiers .hiw-tier:nth-child(2){transition-delay:.12s}
.hiw-tiers .hiw-tier:nth-child(3){transition-delay:.22s}
.hiw-tiers .hiw-tier:nth-child(4){transition-delay:.32s}

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

@keyframes nodePulse{
  0%{box-shadow:0 0 0 0 rgba(200,168,75,.28)}
  70%{box-shadow:0 0 0 8px rgba(200,168,75,0)}
  100%{box-shadow:0 0 0 0 rgba(200,168,75,0)}
}

@media (prefers-reduced-motion:reduce){
  .hiw-pipe-step::after{animation:none}
  .hiw-tiers::after,.hiw-tiers::before{animation:none !important}
}

/* ── Icon system ── */
.hiw-icon{
  display:flex;align-items:center;justify-content:center;
  width:44px;height:44px;margin-bottom:16px;
  border-radius:10px;
  background:rgba(200,168,75,.06);
  border:1px solid rgba(200,168,75,.16);
  color:rgba(212,178,82,.9);
  flex-shrink:0;
}
.hiw-icon svg{
  width:22px;height:22px;
  stroke:currentColor;stroke-width:1.5;fill:none;
  stroke-linecap:round;stroke-linejoin:round;
}

@media(max-width:768px){
  .hiw-happens{grid-template-columns:1fr}
  .wrap{padding:0 24px}
  .hiw-grid{grid-template-columns:1fr}
  .hiw-tiers{grid-template-columns:1fr 1fr} /* stays 2×2 on tablet */
  .hiw-tier{padding:28px 20px 24px;min-height:236px}
  .hiw-icon{width:48px;height:48px;margin-bottom:18px}
  .hiw-icon svg{width:24px;height:24px;stroke-width:1.6}
}
@media(max-width:480px){
  .hiw-tiers{grid-template-columns:1fr} /* single column on small phone */
  .hiw-tier-price{font-size:1.64rem}
  .hiw-tier:not(.hiw-tier-entry) .hiw-tier-price{font-size:1.86rem}
  .hiw-flow-seq{gap:5px}
  .hiw-flow-seq-step{font-size:.46rem;padding:3px 8px}
  .hiw-flow-seq-arrow{font-size:.5rem}
}

/* ═══════════════════════════════════════════
   MOBILE UX REFINEMENT PASS
   ═══════════════════════════════════════════ */

/* ── Accent brightness boost — mobile ── */
@media(max-width:768px){
  .hiw-hero-eye,.hiw-section-eye,.hiw-pipe-num,
  .hiw-happens-num,.hiw-card-num,.hiw-tier-level{
    color:rgba(200,168,75,.82);
  }
  .hiw-transition-signal{color:rgba(200,168,75,.78)}
  .hiw-final-reassure{color:rgba(200,168,75,.7);font-size:.8rem;margin-top:20px}
}

/* ── Typography + spacing — mobile ── */
@media(max-width:768px){
  /* Hero */
  .hiw-hero{padding:clamp(88px,12vh,128px) 0 48px}
  .hiw-hero-hed{font-size:clamp(1.9rem,5.5vw,2.8rem);margin-bottom:18px}
  .hiw-hero-sub{font-size:1rem;color:#d1d1cf;line-height:1.84;margin-bottom:32px}
  .hiw-hero-eye{font-size:.68rem;letter-spacing:.22em;margin-bottom:16px}

  /* Section headings */
  .hiw-section-hed{font-size:clamp(1.5rem,4.5vw,2rem);margin-bottom:14px}
  .hiw-section-sub{font-size:.96rem;color:#c8c8c0;line-height:1.82;margin-bottom:32px}
  .hiw-section-eye{font-size:.66rem;letter-spacing:.2em;margin-bottom:12px}
  .hiw-section{padding:clamp(52px,6.5vw,80px) 0}

  /* Pipeline steps */
  .hiw-pipe-label{font-size:1rem;margin-bottom:6px;color:#f0f0ea}
  .hiw-pipe-desc{font-size:.84rem;color:#c8c8c0;line-height:1.72}
  .hiw-pipe-num{font-size:.6rem;margin-bottom:8px}
  .hiw-pipe-step{padding:22px 16px 18px}

  /* What-happens cards */
  .hiw-happens-block{padding:32px 26px}
  .hiw-happens-title{font-size:1.2rem;margin-bottom:10px;line-height:1.2;color:#f5f5f3}
  .hiw-happens-text{font-size:.96rem;color:#c8c8c0;line-height:1.82}

  /* General grid cards */
  .hiw-card{padding:32px 26px}
  .hiw-card-title{font-size:1.2rem;margin-bottom:10px;color:#f5f5f3}
  .hiw-card-text{font-size:.96rem;color:#c8c8c0;line-height:1.82}
  .hiw-card-num{font-size:.62rem;margin-bottom:10px}

  /* Tier progression cards */
  .hiw-tier{padding:30px 24px;min-height:236px}
  .hiw-tier-price{font-size:1.7rem;margin-bottom:8px;color:rgba(215,182,88,1)}
  .hiw-tier:not(.hiw-tier-entry) .hiw-tier-price{font-size:1.95rem}
  .hiw-tier-name{font-size:.78rem;letter-spacing:.14em;margin-bottom:10px;color:#f0f0ea}
  .hiw-tier-desc{font-size:.9rem;color:#c8c8c0;line-height:1.78}
  .hiw-tier-level{font-size:.62rem;margin-bottom:6px}
  .hiw-tier-badge{font-size:.56rem;margin-bottom:10px}
  .hiw-tier.hiw-tier-entry{border-color:rgba(200,168,75,.32);box-shadow:0 0 32px rgba(200,168,75,.1)}
  .hiw-progression-note{font-size:.94rem;color:#c8c8c0;line-height:1.8;margin-top:28px}

  /* CTA — glow + bigger tap target */
  .btn-primary{
    min-height:56px;padding:18px 28px;font-size:.82rem;
    display:flex;align-items:center;justify-content:center;
    box-shadow:0 4px 24px rgba(200,168,75,.24);
  }
  .btn-primary:hover,.btn-primary:active{
    box-shadow:0 6px 32px rgba(200,168,75,.36);
  }
  .btn-ghost{font-size:.8rem;padding-bottom:4px;color:rgba(200,168,75,.78)}
  .hiw-hero-cta{flex-direction:column;gap:14px;width:100%}
  .hiw-hero-cta .btn-primary{width:100%;text-align:center;justify-content:center}
  .hiw-hero-cta .btn-ghost{width:100%;text-align:center}

  /* Transition block */
  .hiw-transition{padding:52px 0 48px}
  .hiw-transition::before{width:260px;height:140px}
  .hiw-transition-calm{font-size:.88rem;color:#c8c8c0;line-height:1.82;margin-bottom:20px}
  .hiw-transition-action{font-size:clamp(1.45rem,5vw,1.95rem);color:#f5f5f3;line-height:1.28;margin-bottom:24px}
  .hiw-transition-signal{font-size:.76rem;letter-spacing:.1em;margin-bottom:28px}
  .hiw-transition-lead{font-size:.78rem;color:#c8c8c0;padding-bottom:4px}

  /* Momentum + final */
  .hiw-momentum-hed{font-size:clamp(1.4rem,4.5vw,1.9rem);color:#f5f5f3;margin-bottom:16px}
  .hiw-momentum-sub{font-size:.96rem;color:#c8c8c0;line-height:1.82;margin-bottom:30px}
  .hiw-final-hed{font-size:clamp(1.5rem,5vw,2.2rem);margin-bottom:16px}
  .hiw-final-sub{font-size:.96rem;color:#c8c8c0;line-height:1.82;margin-bottom:30px}

  .gold-rule{margin:8px 0}
}

/* ── Small phones ── */
@media(max-width:430px){
  .hiw-hero{padding:clamp(80px,10vh,100px) 0 40px}
  .hiw-hero-hed{font-size:clamp(1.7rem,7vw,2.2rem)}
  .hiw-hero-sub{font-size:.94rem}
  .hiw-section-hed{font-size:clamp(1.35rem,5.5vw,1.8rem)}
  .hiw-happens-title{font-size:1.14rem}
  .hiw-card-title{font-size:1.14rem}
  .hiw-tier-price{font-size:1.45rem}
  .hiw-tier-desc{font-size:.88rem}
  .wrap{padding:0 20px}
}

@include('partials.public-nav-mobile-css')

/* ── Mobile nav ── */
@media(max-width:900px){
  #nav{padding:14px 20px}#nav.stuck{padding:10px 20px}
  .nav-link{display:none}
  .nav-btn{display:none}
  .nav-hamburger{display:flex}
}

/* Final pre-live readability refinements */
.hiw-hero-eye,
.hiw-pipe-desc,
.hiw-tier-note,
.hiw-final-reassure,
.hiw-transition-signal,
.hiw-transition-lead,
.hiw-card-kicker,
.hiw-happens-kicker{
  font-size:max(.78rem, 12px);
  line-height:1.58;
}
.hiw-pipe-desc,
.hiw-transition-lead{color:rgba(190,190,182,.84)}

</style>
</head>
<body>

@include('partials.public-nav', ['showHamburger' => true])

<!-- ════════════ SECTION 1 — HERO ════════════ -->
<section class="hiw-hero">
  <div class="wrap">
    <p class="hiw-hero-eye r">The Evolution Layer</p>
    <h1 class="hiw-hero-hed r">
      Search has evolved.<br><em>Most websites haven't.</em>
    </h1>
    <p class="hiw-hero-sub r">
      AI systems now decide what gets seen, cited, and surfaced.
      SEOAIco scans how your site is interpreted, then maps what those systems actually need.
    </p>
    <div class="hiw-system-meta r" aria-label="System status labels">
      <span>SYSTEM STATUS: READY</span>
      <span>INPUT: DOMAIN</span>
      <span>OUTPUT: VISIBILITY INTELLIGENCE</span>
    </div>
    <div class="hiw-hero-cta r">
      <a href="{{ route('scan.start') }}" class="btn-primary" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'hero',cta_label:'start_scan'});">Run AI Visibility Scan &mdash; $2</a>
      <a href="#system-flow" class="btn-ghost">See system activation flow &darr;</a>
    </div>
    <p class="hiw-hero-promise r">Test your site for AI visibility in seconds, and see whether AI systems are likely to surface you or skip you.</p>
  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ SECTION 2 — SYSTEM FLOW STRIP ════════════ -->
<section class="hiw-section" id="system-flow">
  <div class="wrap">
    <p class="hiw-section-eye r">AI Visibility System</p>
    <h2 class="hiw-section-hed r">Built for the AI discovery system,<br><em style="font-style:italic;color:var(--gold-lt)">not legacy SEO checklists.</em></h2>
    <p class="hiw-section-sub r">
      This is not a rank tracker. It is an AI visibility scan and build path.
      The system reveals how your domain is interpreted, where citation signals break, and what to deploy next.
    </p>

    <!-- System Visual: Pipeline -->
    <div class="hiw-pipeline r">
      <div class="hiw-pipe-step active">
        <span class="hiw-pipe-you">Input: domain</span>
        <div class="hiw-pipe-num">Phase 1</div>
        <div class="hiw-pipe-label">Signal Scan Initiated</div>
        <p class="hiw-pipe-desc">Your domain enters the engine for AI visibility extraction.</p>
        <svg class="hiw-pipe-arrow" viewBox="0 0 14 14" fill="none" aria-hidden="true">
          <path d="M5 2l5 5-5 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="hiw-pipe-step">
        <div class="hiw-pipe-num">Phase 2</div>
        <div class="hiw-pipe-label">Visibility Score Generated</div>
        <p class="hiw-pipe-desc">You get a live 0&ndash;100 readout of AI citation strength.</p>
        <svg class="hiw-pipe-arrow" viewBox="0 0 14 14" fill="none" aria-hidden="true">
          <path d="M5 2l5 5-5 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="hiw-pipe-step">
        <div class="hiw-pipe-num">Phase 3</div>
        <div class="hiw-pipe-label">Gaps + Opportunities Mapped</div>
        <p class="hiw-pipe-desc">Critical misses, ignored signals, and leverage points are surfaced.</p>
        <svg class="hiw-pipe-arrow" viewBox="0 0 14 14" fill="none" aria-hidden="true">
          <path d="M5 2l5 5-5 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="hiw-pipe-step">
        <div class="hiw-pipe-num">Phase 4</div>
        <div class="hiw-pipe-label">System Build Path Created</div>
        <p class="hiw-pipe-desc">A prioritized rollout is generated for the next revenue move.</p>
        <svg class="hiw-pipe-arrow" viewBox="0 0 14 14" fill="none" aria-hidden="true">
          <path d="M5 2l5 5-5 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="hiw-pipe-step">
        <div class="hiw-pipe-num">Phase 5</div>
        <div class="hiw-pipe-label">Compounding Visibility</div>
        <p class="hiw-pipe-desc">As structure deploys, AI recommendation coverage expands.</p>
      </div>
    </div>
  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ SECTION 3 — WHAT ACTUALLY HAPPENS ════════════ -->
<section class="hiw-section">
  <div class="wrap">
    <p class="hiw-section-eye r">Guided Execution</p>
    <h2 class="hiw-section-hed r">When your site enters the system</h2>
    <p class="hiw-section-sub r">
      You submit one domain. The system reveals how AI search interprets your business, where visibility breaks, and what to build first.
    </p>
    <div class="hiw-process-meta r" aria-label="Process labels">
      <span>SCAN TYPE: AI VISIBILITY</span>
      <span>OUTPUT: ACTIONABLE INTELLIGENCE</span>
      <span>TIME TO RESULT: SECONDS</span>
    </div>

    <div class="hiw-happens">
      <div class="hiw-happens-block r">
        <div class="hiw-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7.5"/><path d="M16.5 16.5l4 4"/></svg>
        </div>
        <div class="hiw-happens-title">Signal Scan Initiated</div>
        <p class="hiw-happens-text">
          Enter your domain. The system tests structured data, topical clarity,
          and citation readiness against live AI discovery patterns.
        </p>
      </div>
      <div class="hiw-happens-block r">
        <div class="hiw-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><rect x="3" y="13" width="4" height="8" rx="1"/><rect x="10" y="8" width="4" height="13" rx="1"/><rect x="17" y="4" width="4" height="17" rx="1"/></svg>
        </div>
        <div class="hiw-happens-title">Visibility Score Generated</div>
        <p class="hiw-happens-text">
          You get a 0&ndash;100 AI visibility score with the exact gaps that cause
          systems to surface competitors instead of your site.
        </p>
      </div>
      <div class="hiw-happens-block r">
        <div class="hiw-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
        </div>
        <div class="hiw-happens-title">System Build Path Created</div>
        <p class="hiw-happens-text">
          A prioritized fix list is mapped by impact &mdash; each step raises the
          probability your business gets cited and recommended.
        </p>
      </div>
    </div>
  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ TRANSITION — SYSTEM PROGRESSION ════════════ -->
<div class="hiw-transition">
  <p class="hiw-transition-calm r">Search evolved. The operating model did too.</p>
  <p class="hiw-transition-action r">Start with one scan.<br>Scale only what the system proves.</p>
  <p class="hiw-transition-signal r">One intelligence layer&ensp;&middot;&ensp;compounding deployment path</p>
  <p class="hiw-transition-lead r">System layers below&ensp;<span style="display:inline-block;opacity:.55;font-size:.58rem;vertical-align:middle;transform:translateY(1px);transition:opacity .3s">&#9662;</span></p>
</div>

<div class="gold-rule"></div>

<!-- ════════════ SECTION 4 — PROGRESSION (LEVELS) ════════════ -->
<section class="hiw-section">
  <div class="wrap">
    <p class="hiw-section-eye r">Deployment Progression</p>
    <h2 class="hiw-section-hed r">Four layers. One AI visibility system.</h2>
    <p class="hiw-section-sub r">
      Your $2 scan is the entry layer. Every expansion reuses the same intelligence model,
      so nothing is rebuilt and every step compounds visibility.
    </p>

    {{-- Sequence indicator strip --}}
    <div class="hiw-flow-seq r" aria-hidden="true">
      <span class="hiw-flow-seq-step active">01 &middot; Scan</span>
      <span class="hiw-flow-seq-arrow">&rarr;</span>
      <span class="hiw-flow-seq-step">02 &middot; Signal</span>
      <span class="hiw-flow-seq-arrow">&rarr;</span>
      <span class="hiw-flow-seq-step">03 &middot; Leverage</span>
      <span class="hiw-flow-seq-arrow">&rarr;</span>
      <span class="hiw-flow-seq-step">04 &middot; Activate</span>
    </div>

    <div class="hiw-tiers">

      {{-- Level 1 — entry layer --}}
      <div class="hiw-tier hiw-tier-entry r">
        <span class="hiw-tier-badge">Most common entry point</span>
        <div class="hiw-tier-level">Level 1</div>
        <div class="hiw-tier-price">$2</div>
        <div class="hiw-tier-name">AI Visibility Scan</div>
        <p class="hiw-tier-desc">Test whether AI systems are likely to surface or ignore your site, then see the highest-impact fixes.</p>
        <a href="{{ route('scan.start') }}" class="btn-primary" style="margin-top:16px;font-size:.7rem;padding:12px 28px" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'tier_1',cta_label:'start_scan'});">Start Scan &mdash; $2</a>
      </div>

      {{-- Level 2 --}}
      <div class="hiw-tier r">
        <div class="hiw-tier-level">Level 2</div>
        <div class="hiw-tier-price">$99</div>
        <div class="hiw-tier-name">Signal Analysis</div>
        <p class="hiw-tier-desc">Expand analysis depth across structure, schema, and competitive citation signals in active markets.</p>
        <a href="{{ route('checkout.signal-expansion') }}" class="hiw-tier-link" data-layer="level-2" role="button" aria-haspopup="dialog">See this layer <span class="hiw-tier-link-arrow">&rarr;</span></a>
      </div>

      {{-- Level 3 --}}
      <div class="hiw-tier r">
        <div class="hiw-tier-level">Level 3</div>
        <div class="hiw-tier-price">$249</div>
        <div class="hiw-tier-name">Action Plan</div>
        <p class="hiw-tier-desc">Map full service-by-location coverage and generate the structural system AI platforms can reliably interpret.</p>
        <a href="{{ route('checkout.structural-leverage') }}" class="hiw-tier-link" data-layer="level-3" role="button" aria-haspopup="dialog">See this layer <span class="hiw-tier-link-arrow">&rarr;</span></a>
      </div>

      {{-- Level 4 --}}
      <div class="hiw-tier r">
        <div class="hiw-tier-level">Level 4</div>
        <div class="hiw-tier-price">$489</div>
        <div class="hiw-tier-name">Guided Execution</div>
        <p class="hiw-tier-desc">Deploy the full visibility layer: pages, schema, and architecture tuned for AI citation and recommendation scale.</p>
        <a href="{{ route('checkout.system-activation') }}" class="hiw-tier-link" data-layer="level-4" role="button" aria-haspopup="dialog">See this layer <span class="hiw-tier-link-arrow">&rarr;</span></a>
      </div>

    </div>

    <p class="hiw-progression-note r">
      Start here. Move up as the system unlocks what&rsquo;s next.
    </p>
  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ FINAL — SINGLE DECISION POINT ════════════ -->
<section class="hiw-final">
  <div class="wrap">
    <p class="hiw-momentum-hed r">
      You don't need a full rebuild to start.
    </p>
    <p class="hiw-momentum-sub r">
      One $2 scan reveals how AI search systems read your site and where visibility is being lost right now.
    </p>

    <h2 class="hiw-final-hed r">
      AI systems are already deciding<br><em>who gets surfaced first.</em>
    </h2>
    <p class="hiw-final-sub r">
      Run the visibility scan and see whether your business is being interpreted for citation &mdash; or filtered out.
    </p>

    <div class="hiw-cta-actions r">
      <a href="{{ route('scan.start') }}" class="btn-primary" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'final_cta',cta_label:'start_scan'});">Run AI Visibility Scan &mdash; $2</a>
    </div>
    <p class="hiw-final-reassure r">Premium guided entry.&ensp;System-mapped rollout.&ensp;No wasted build.</p>
  </div>
</section>

@include('partials.info-cta-modal')

@include('partials.back-to-top')

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

  /* ── GA page view ── */
  if(typeof gtag==='function'){gtag('event','view_how_it_works',{page_location:window.location.href});}
</script>

@include('partials.public-nav-js')

@include('components.layer-modal')
@include('components.booking-modal')
@include('components.ai-assistant', [
    'aiMicroLabel'  => 'Ask how the system works',
    'aiTeaserTitle' => 'Ask about the system',
    'aiTeaserText'  => 'I can explain how each level works, what AI visibility means, or what to do first.',
    'aiSuggestedPrompts' => [
        'How does the $2 scan work?',
        'What does Signal Analysis unlock?',
        'What is AI Visibility Score?',
        'Which level should I start with?',
    ],
])
@include('components.tm-style')
</body>
</html>
