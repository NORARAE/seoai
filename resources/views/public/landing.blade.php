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
<title>The AI Citation Engine™ for Local Service Businesses | SEO AI Co™</title>
<meta name="description" content="SEO AI Co™ operates the AI Citation Engine™ — structuring web content for extraction and citation by AI systems. Get cited by Google AI Overviews, ChatGPT, and Perplexity across every city you serve.">
<link rel="canonical" href="{{ url('/') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="SEO AI Co™">
<meta property="og:title" content="The AI Citation Engine™ for Local Service Businesses | SEO AI Co™">
<meta property="og:description" content="SEO AI Co™ operates the AI Citation Engine™ — structuring web content for extraction and citation by AI systems. Get cited by Google AI Overviews, ChatGPT, and Perplexity.">
<meta property="og:url" content="{{ url('/') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
@include('partials.design-system')
@include('partials.public-nav-css')

/* ── Landing page token overrides ── */
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
.nav-account-short{display:none}

/* ── Hero ── */
#hero{
  display:flex;flex-direction:column;
  justify-content:flex-start;align-items:flex-start;
  padding:clamp(100px,13vh,148px) 64px 44px;position:relative;
  min-height:68vh;
  max-width:1200px;margin:0 auto;
}
.hero-grid{
  position:fixed;inset:0;pointer-events:none;z-index:0;
  background-image:
    linear-gradient(rgba(200,168,75,.03) 1px,transparent 1px),
    linear-gradient(90deg,rgba(200,168,75,.03) 1px,transparent 1px);
  background-size:88px 88px;
}

/* ══════════════════════════════════════════════════════
   AMBIENT SYSTEM — DO NOT MODIFY WITHOUT REVIEW
   ──────────────────────────────────────────────────────
   Architecture:
     .amb-wrap-* — position:fixed wrappers, receive JS
                   parallax translateY only.
     .amb-orb-*  — inner divs, carry CSS drift animation
                   only. Transforms do not conflict.
     .amb-bloom  — centred focal glow, static transform,
                   no JS parallax applied.
     .amb-shimmer— mobile-only tonal breathing overlay.
   Rendering:
     All layers use transform + opacity — compositor
     thread only, zero layout/paint cost.
     -webkit-backface-visibility:hidden locks iOS Safari
     GPU layer to prevent address-bar-resize flicker.
   Layering:
     z-index:0 — ambient layers in DOM before content,
     so all content sections naturally stack above.
   Reduced-motion:
     CSS: animations set to none.
     JS:  matchMedia check exits handler before bind.
══════════════════════════════════════════════════════ */

.amb-wrap{
  position:fixed;pointer-events:none;z-index:0;
  will-change:transform;
  -webkit-backface-visibility:hidden;
  backface-visibility:hidden;
}
.amb-wrap-a{top:-8%;right:-6%}
.amb-wrap-b{bottom:6%;left:-10%}

.amb-orb-a{
  width:min(72vw,900px);height:min(72vw,900px);
  border-radius:50%;
  background:radial-gradient(ellipse at center,rgba(200,168,75,.09) 0%,transparent 62%);
  animation:ambDriftA 18s ease-in-out infinite alternate;
  will-change:transform;
}
.amb-orb-b{
  width:min(55vw,700px);height:min(55vw,700px);
  border-radius:50%;
  background:radial-gradient(ellipse at center,rgba(200,168,75,.055) 0%,transparent 60%);
  animation:ambDriftB 24s ease-in-out infinite alternate;
  will-change:transform;
}

/* Focal bloom — centred, reinforces hero headline composition */
.amb-bloom{
  position:fixed;top:8%;left:50%;
  width:min(110vw,1100px);height:60vh;
  transform:translateX(-50%);
  border-radius:50%;
  background:radial-gradient(ellipse at 42% 46%,rgba(200,168,75,.054) 0%,transparent 65%);
  pointer-events:none;z-index:0;
  will-change:opacity;
  -webkit-backface-visibility:hidden;
  backface-visibility:hidden;
}

/* Shimmer — mobile-only barely-visible tonal breathing */
.amb-shimmer{
  position:fixed;top:0;left:0;right:0;height:72vh;
  background:linear-gradient(158deg,transparent 28%,rgba(200,168,75,.022) 50%,transparent 72%);
  pointer-events:none;z-index:0;
  opacity:0;
  animation:shimmerBreath 16s ease-in-out infinite;
  display:none; /* enabled per media query below */
}

@keyframes ambDriftA{
  from{transform:translate(0,0) scale(1)}
  to{transform:translate(-5%,4%) scale(1.09)}
}
@keyframes ambDriftB{
  from{transform:translate(0,0) scale(1)}
  to{transform:translate(5%,-4%) scale(1.07)}
}
@keyframes shimmerBreath{
  0%,100%{opacity:0}
  50%{opacity:1}
}

/* Desktop ≥901px: restrained opacity, shimmer off */
@media(min-width:901px){
  .amb-orb-b{opacity:.60}
  .amb-bloom{opacity:.78}
  .amb-shimmer{display:none}
}

/* Reduced-motion: kill all CSS animations */
@media(prefers-reduced-motion:reduce){
  .amb-orb-a,.amb-orb-b{animation:none}
  .amb-shimmer{display:none!important}
}
/* ══ END AMBIENT SYSTEM ══ */

/* ── CTAs ── */
.hero-actions{display:flex;gap:20px;align-items:center}

/* ── Scroll cue ── */
.hero-scroll{
  position:absolute;bottom:48px;left:64px;
  display:flex;flex-direction:column;align-items:center;gap:8px;
  opacity:0;animation:up .8s 1.1s forwards;
  text-decoration:none;
}
.scroll-line{width:1px;height:28px;background:linear-gradient(to bottom,transparent,rgba(200,168,75,.35))}
.scroll-caret{
  width:9px;height:9px;
  border-right:1px solid rgba(200,168,75,.45);
  border-bottom:1px solid rgba(200,168,75,.45);
  transform:rotate(45deg);
  animation:pullDown 2.6s ease-in-out infinite;
}
@keyframes pullDown{
  0%,100%{opacity:.4;transform:rotate(45deg) translateY(0)}
  55%{opacity:.7;transform:rotate(45deg) translateY(4px)}
}

@keyframes up{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}

/* ── Shared section helpers ── */
.gold-rule{height:1px;background:linear-gradient(to right,transparent,rgba(154,122,48,.38),transparent)}
.s-eye{font-size:.76rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold);margin-bottom:14px;display:flex;align-items:center;gap:14px}
.s-eye::before{content:'';width:28px;height:1px;background:var(--gold)}
.s-h{font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem,4vw,3.6rem);font-weight:400;line-height:1.12;margin-bottom:20px}
.s-h em{font-style:italic;color:var(--gold)}
.s-p{font-size:1.05rem;line-height:1.9;color:rgba(168,168,160,.82);max-width:680px}
.s-p strong{color:var(--ivory);font-weight:400}

/* ── Statement ── */
.statement{
  padding:72px 64px;display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;
  max-width:1200px;margin:0 auto;
}
.stmt-quote{
  position:relative;padding:28px 32px;border:1px solid var(--border);
  background:linear-gradient(135deg,rgba(200,168,75,.03) 0%,transparent 60%);
}
.stmt-quote::before{content:'';position:absolute;top:0;left:28px;right:28px;height:1px;background:linear-gradient(90deg,transparent,var(--gold-dim),transparent)}
.stmt-quote::after{content:'';position:absolute;bottom:0;left:28px;right:28px;height:1px;background:linear-gradient(90deg,transparent,var(--gold-dim),transparent)}
.stmt-quote .sq-mark{display:block;font-family:'Cormorant Garamond',serif;font-size:3.2rem;line-height:1;color:var(--gold-dim);margin-bottom:12px;user-select:none}
.stmt-quote .sq-text{
  font-family:'Cormorant Garamond',serif;font-size:clamp(1.6rem,2.8vw,2.4rem);
  font-weight:300;font-style:italic;line-height:1.45;color:var(--ivory);letter-spacing:.01em;
  display:flex;flex-direction:column;gap:.35em;
}
.stmt-quote .sq-text strong{font-style:normal;color:var(--gold);font-weight:400}
.stmt-quote .sq-rule{display:block;width:48px;height:1px;background:var(--gold-dim);margin:20px auto 0}
.stmt-body p{font-size:1.05rem;line-height:1.72;color:var(--muted);margin-bottom:18px}
.stmt-body p:last-child{margin-bottom:0}
.stmt-body strong{color:var(--ivory);font-weight:400}
.stmt-split{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px}
.stmt-split-card{padding:22px 24px;border-left:2px solid var(--gold-dim);background:rgba(200,168,75,.02)}
.stmt-split-card .split-tag{font-size:.68rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);margin-bottom:8px;display:block}
.stmt-split-card .split-body{font-size:.94rem;line-height:1.8;color:var(--muted)}
.stmt-split-card .split-body strong{color:var(--ivory);font-weight:400}

/* ── Audience ── */
.audience-section{border-top:1px solid var(--border);padding:72px 64px;max-width:1200px;margin:0 auto}
.audience-grid{display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--border);margin-top:40px}
.aud-card{background:var(--deep);padding:40px 36px;position:relative;overflow:hidden;transition:background .32s,transform .32s cubic-bezier(.23,1,.32,1),box-shadow .32s}
.aud-card:hover{background:var(--card);transform:translateY(-1px);box-shadow:0 16px 48px rgba(0,0,0,.5)}
.aud-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:2px;
  background:linear-gradient(90deg,transparent,var(--gold-dim),transparent);
  transition:background .4s;
}
.aud-card:hover::before{background:linear-gradient(90deg,transparent,var(--gold),transparent)}
.aud-tag{font-size:.78rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);margin-bottom:18px;display:block}
.aud-title{font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:300;line-height:1.25;margin-bottom:20px}
.aud-title em{font-style:italic;color:var(--gold)}
.aud-body{font-size:1.05rem;line-height:1.72;color:var(--muted);margin-bottom:28px}
.aud-body strong{color:var(--ivory);font-weight:400}
.aud-list{list-style:none;display:flex;flex-direction:column;gap:14px}
.aud-list li{font-size:1rem;color:var(--muted);padding-left:22px;position:relative;line-height:1.8}
.aud-list li::before{content:'';position:absolute;left:0;top:12px;width:10px;height:1px;background:var(--gold)}
.aud-list li strong{color:var(--ivory);font-weight:400}
.aud-cta{
  display:inline-block;margin-top:22px;font-size:.82rem;font-weight:500;letter-spacing:.14em;
  text-transform:uppercase;padding:16px 40px;text-decoration:none;transition:background .3s,transform .2s,border-color .3s;
  background:var(--gold);color:var(--bg);border:1px solid var(--gold);
}
.aud-cta:hover{background:var(--gold-lt);border-color:var(--gold-lt);transform:translateY(-2px)}

/* ── WYL (What You're Licensing) ── */
.wyl-section{border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:72px 64px;background:var(--deep)}
.wyl-inner{max-width:1200px;margin:0 auto}
.wyl-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-top:36px}
.wyl-card{
  background:linear-gradient(160deg,rgba(18,18,16,.98) 0%,rgba(12,12,10,1) 100%);
  border:1px solid rgba(200,168,75,.1);
  padding:32px 28px;position:relative;overflow:hidden;
  transition:transform .45s cubic-bezier(.23,1,.32,1),box-shadow .45s cubic-bezier(.23,1,.32,1),border-color .4s;
}
.wyl-card:hover{
  transform:translateY(-5px);
  box-shadow:0 12px 48px rgba(0,0,0,.55),0 0 0 1px rgba(200,168,75,.2);
  border-color:rgba(200,168,75,.24);
}
.wyl-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.18),transparent);
}
.wyl-card::after{
  content:'';position:absolute;bottom:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,var(--gold-dim),transparent);
  transform:scaleX(0);transition:transform .55s cubic-bezier(.23,1,.32,1);
}
.wyl-card:hover::after{transform:scaleX(1)}
.wyl-icon{
  font-size:1.8rem;color:var(--gold);opacity:.55;margin-bottom:16px;
  display:block;transition:opacity .35s,transform .45s cubic-bezier(.23,1,.32,1);line-height:1;
}
.wyl-card:hover .wyl-icon{opacity:1;transform:translateY(-3px)}
.wyl-title{font-family:'Cormorant Garamond',serif;font-size:1.35rem;font-weight:400;letter-spacing:.02em;line-height:1.25;margin-bottom:14px;color:var(--ivory)}
.wyl-desc{font-size:.88rem;line-height:1.7;color:var(--muted);opacity:.82}

/* ── Position block (split composition) ── */
.pos-block{
  border-top:1px solid rgba(154,122,48,.18);
  padding:72px 64px;
  background:var(--bg);
  position:relative;overflow:hidden;
}
.pos-block-inner{
  max-width:1200px;margin:0 auto;
  display:grid;grid-template-columns:1.1fr 1fr;gap:72px;align-items:center;
}
.pos-left{position:relative;z-index:2}
.pos-h2{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(3.2rem,5vw,4.5rem);font-weight:300;line-height:1.06;
  color:var(--ivory);margin-bottom:16px;letter-spacing:-.01em;
}
.pos-rule{
  width:48px;height:1px;
  background:linear-gradient(to right,var(--gold),transparent);
  margin-bottom:20px;
}
.pos-support{
  font-size:clamp(.94rem,1.3vw,1.08rem);font-weight:300;
  color:rgba(168,168,160,.72);line-height:1.6;margin-bottom:20px;
  letter-spacing:.01em;
}
.pos-gold{
  display:block;
  font-family:'Cormorant Garamond',serif;font-style:italic;font-weight:300;
  font-size:clamp(1.05rem,1.6vw,1.25rem);
  color:var(--gold);letter-spacing:.02em;line-height:1.5;
  margin-bottom:20px;
  text-shadow:0 0 28px rgba(200,168,75,.22);
}
.pos-proof{
  list-style:none;display:flex;flex-direction:column;gap:10px;
  margin-bottom:24px;max-width:520px;
}
.pos-proof li{
  font-size:1.08rem;font-weight:300;
  color:var(--muted);line-height:1.65;
  padding-left:18px;position:relative;
}
.pos-proof li::before{
  content:'';position:absolute;left:0;top:.65em;
  width:8px;height:1px;background:var(--gold-dim);
}
.pos-close{
  display:block;
  font-size:1.08rem;font-weight:400;color:var(--ivory);
  letter-spacing:.02em;line-height:1.5;
  padding-top:20px;border-top:1px solid rgba(200,168,75,.12);
}
.pos-right{
  position:relative;
  display:flex;align-items:center;justify-content:center;
  min-height:280px;
}
.pos-canvas{
  display:block;width:100%;height:320px;
  max-width:460px;opacity:.9;
}
@media(max-width:900px){
  .pos-block{padding:52px 24px}
  .pos-block-inner{grid-template-columns:1fr;gap:36px}
  .pos-canvas{height:200px;max-width:100%}
}
@media(max-width:520px){
  .pos-block{padding:40px 20px}
  .pos-right{display:none}
}

/* ── Crypto acceptance block ── */
.crypto-accept{
  text-align:center;
  padding:56px 64px;
  border-top:1px solid rgba(154,122,48,.12);
  background:var(--bg);
  max-width:680px;
  margin:0 auto;
}
.crypto-lines{
  display:block;
  margin:0 0 10px;
  font-size:.92rem;
  color:var(--muted);
  line-height:1.7;
  letter-spacing:.02em;
}
.crypto-emphasis{
  font-size:1.06rem;
  font-weight:500;
  color:var(--gold);
  letter-spacing:.12em;
  text-transform:uppercase;
  margin-bottom:16px;
}
.crypto-sub{
  font-size:.82rem;
  color:rgba(168,168,160,.6);
  letter-spacing:.06em;
}
@media(max-width:900px){.crypto-accept{padding:44px 24px}}
@media(max-width:520px){.crypto-accept{padding:36px 18px}.crypto-lines{font-size:.88rem}}

/* ── URL demo section ── */
.url-section{background:var(--deep);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:72px 64px}
.url-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 1.1fr;gap:64px;align-items:start}
.url-box{background:var(--bg);border:1px solid var(--border);padding:36px 32px}
.url-box-label{font-size:.7rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold-dim);margin-bottom:18px}
.url-list{display:flex;flex-direction:column;gap:6px}
.url-item{
  background:#070707;border:1px solid var(--border);padding:10px 14px;
  font-size:.86rem;font-family:monospace;color:var(--muted);
  display:flex;align-items:center;gap:10px;transition:border-color .3s,color .3s;
}
.url-item:hover{border-color:var(--gold-dim);color:var(--ivory)}
.url-dot{width:5px;height:5px;border-radius:50%;background:var(--gold);flex-shrink:0;animation:blink 2.8s ease-in-out infinite}
.url-item .hl{color:var(--gold)}
@keyframes blink{0%,100%{opacity:.2}50%{opacity:1}}
.url-more{font-size:.72rem;letter-spacing:.1em;color:var(--muted);text-align:center;margin-top:12px;opacity:.6}

/* ── Steps ── */
.steps-section{background:var(--deep);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.steps-wrap{max-width:1200px;margin:0 auto;padding:52px 64px}
.steps-panel{margin-top:18px;border:1px solid rgba(200,168,75,.08);position:relative}
.steps-panel::before{content:'';position:absolute;top:-1px;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent 5%,var(--gold-dim) 28%,var(--gold) 50%,var(--gold-dim) 72%,transparent 95%);opacity:.65}
.steps-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0;align-items:start}
.step{padding:56px 48px;position:relative;transition:background .32s,transform .32s cubic-bezier(.23,1,.32,1),box-shadow .32s}
.step:not(:last-child)::after{content:'';position:absolute;top:10%;right:0;width:1px;height:80%;background:linear-gradient(to bottom,transparent,rgba(200,168,75,.12) 28%,rgba(200,168,75,.12) 72%,transparent)}
.step:hover{background:rgba(200,168,75,.02)}
.step-n{font-family:'Cormorant Garamond',serif;font-size:5rem;font-weight:300;color:rgba(200,168,75,.10);line-height:1;letter-spacing:-.02em;display:block;margin-bottom:20px;transition:color .4s}
.step:hover .step-n{color:rgba(200,168,75,.20)}
.step-rule{width:24px;height:1px;background:linear-gradient(90deg,var(--gold-dim),transparent);margin-bottom:24px;opacity:.65}
.step-title{font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:400;line-height:1.22;margin-bottom:14px;color:var(--ivory)}
.step-desc{font-size:.88rem;line-height:1.82;color:var(--muted)}
/* ── Steps Trust Row ── */
.steps-trust{margin-top:10px;padding-top:10px;text-align:center}
.steps-trust-label{font-size:.64rem;letter-spacing:.26em;text-transform:uppercase;color:rgba(200,168,75,.28);margin-bottom:12px}
.steps-surface-row{display:flex;justify-content:center;align-items:flex-start;flex-wrap:wrap;column-gap:36px;row-gap:14px}
.steps-surface{display:flex;flex-direction:column;align-items:center;gap:7px;font-size:.65rem;letter-spacing:.16em;text-transform:uppercase;color:rgba(232,220,190,.44);font-family:'DM Sans',sans-serif;font-weight:300;cursor:default;transition:color .25s,transform .25s}
.steps-surface svg{width:18px;height:18px;color:rgba(200,168,75,.46);flex-shrink:0;transition:color .25s,filter .25s,transform .25s}
.steps-surface:hover{color:rgba(232,220,190,.72);transform:translateY(-2px)}
.steps-surface:hover svg{color:rgba(200,168,75,.78);filter:drop-shadow(0 0 6px rgba(200,168,75,.28))}
.steps-surface:active{transform:translateY(-1px) scale(.96)}
.steps-surface-sep{display:none}

/* ── URL Lock ── */
.url-lock{
  background:var(--deep);border-top:1px solid var(--border);border-bottom:1px solid var(--border);
  padding:72px 64px;
}
.url-lock-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 1.4fr;gap:64px;align-items:center}
.ul-title{font-family:'Cormorant Garamond',serif;font-size:clamp(1.8rem,3vw,2.6rem);font-weight:300;line-height:1.3;margin-top:14px}
.ul-title em{font-style:italic;color:var(--gold)}
.ul-body{font-size:1rem;line-height:1.75;color:var(--muted)}
.ul-body strong{color:var(--ivory);font-weight:400}
.ul-lead{font-size:1.05rem;color:var(--muted);margin-top:18px;line-height:1.7}
.ul-note{margin-top:20px;padding:16px 20px;border:1px solid var(--border);border-radius:2px;background:var(--card)}
.ul-note p{font-size:.88rem;line-height:1.7;color:var(--muted)}
.ul-note p+p{margin-top:8px}
.ul-note strong{color:var(--ivory);font-weight:400}
.ul-states{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:24px}
.ul-state{padding:20px 22px;border:1px solid var(--border);position:relative}
.ul-state-label{font-size:.68rem;letter-spacing:.2em;text-transform:uppercase;margin-bottom:6px;display:block}
.ul-state.active .ul-state-label{color:var(--gold)}
.ul-state.inactive .ul-state-label{color:var(--warn)}
.ul-state-title{font-size:.92rem;color:var(--ivory);margin-bottom:5px;font-weight:400}
.ul-state-desc{font-size:.84rem;line-height:1.7;color:var(--muted)}
.ul-state.active{border-color:#2a2a18}
.ul-state.inactive{border-color:#2a1414}

/* ── Licence Statement ── */
.licence-stmt-section{border-top:1px solid var(--border);padding:64px 64px;max-width:1200px;margin:0 auto}
.licence-stmt-principle{font-family:'Cormorant Garamond',serif;font-size:clamp(1.2rem,1.8vw,1.55rem);font-weight:400;font-style:italic;color:var(--ivory);letter-spacing:.01em;margin-bottom:16px}
.licence-stmt-body{display:flex;flex-direction:column;gap:6px}
.licence-stmt-body p{font-size:.9rem;color:var(--muted);letter-spacing:.04em;line-height:1.9}
/* ══════════════════════════════════════════════════════════
   ASCENSION SYSTEM — Pricing Architecture  v2
   ══════════════════════════════════════════════════════════ */

/* ── Section container ── */
#offer{
  padding:44px 64px 64px;max-width:1280px;margin:0 auto;
  position:relative;overflow:hidden;
}
/* Signal-field atmosphere behind entire section */
#offer::before{
  content:'';position:absolute;inset:0;
  background:
    radial-gradient(ellipse at 50% 18%,rgba(200,168,75,.035) 0%,transparent 55%),
    radial-gradient(ellipse at 25% 70%,rgba(200,168,75,.015) 0%,transparent 45%),
    radial-gradient(ellipse at 75% 65%,rgba(200,168,75,.015) 0%,transparent 45%);
  pointer-events:none;z-index:0;
}
/* Faint grid texture overlay */
#offer::after{
  content:'';position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(200,168,75,.018) 1px,transparent 1px),
    linear-gradient(90deg,rgba(200,168,75,.018) 1px,transparent 1px);
  background-size:64px 64px;
  mask-image:radial-gradient(ellipse at 50% 40%,black 20%,transparent 70%);
  -webkit-mask-image:radial-gradient(ellipse at 50% 40%,black 20%,transparent 70%);
  pointer-events:none;z-index:0;opacity:.6;
}

/* ── Section intro ── */
.offer-intro{display:grid;grid-template-columns:1fr 1fr;gap:56px;margin-bottom:44px;align-items:start;position:relative;z-index:2}
.offer-note{font-size:1rem;line-height:1.9;color:var(--muted)}
.offer-note strong{color:var(--ivory);font-weight:400}
.offer-hed-split{display:flex;flex-direction:column;gap:.06em}
.offer-hed-mid{font-size:clamp(1.5rem,2.5vw,2.4rem);color:rgba(237,232,222,.64);font-style:normal;font-weight:300;line-height:1.1}
.offer-panel{display:flex;flex-direction:column;gap:0}
.offer-positioning{padding-top:24px}
.offer-positioning-bottom{font-size:clamp(1rem,1.6vw,1.2rem);font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;color:rgba(237,232,222,.72);line-height:1.5}

/* ── Buyer guide ── */
.offer-guide{padding:0 0 24px;text-align:center;position:relative;z-index:2}
.offer-guide-line{font-size:.72rem;letter-spacing:.18em;text-transform:uppercase;color:var(--gold-dim)}
.offer-guide-sub{font-size:.82rem;color:rgba(168,168,160,.44);text-align:center;margin-top:10px;letter-spacing:.02em}

/* ════════════════════════════════════════════════════
   ASCENSION RAIL — Stage progression indicator
   ════════════════════════════════════════════════════ */
.ascent-rail{
  display:flex;align-items:center;justify-content:center;gap:0;
  margin:0 auto 32px;padding:14px 28px;
  position:relative;z-index:2;
  border:1px solid rgba(200,168,75,.06);
  border-radius:40px;
  background:rgba(10,9,7,.55);
  backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);
  max-width:720px;
}
/* Subtle inner glow */
.ascent-rail::before{
  content:'';position:absolute;inset:0;border-radius:40px;
  background:radial-gradient(ellipse at 50% 50%,rgba(200,168,75,.025) 0%,transparent 60%);
  pointer-events:none;
}
.ascent-node{
  display:flex;flex-direction:column;align-items:center;gap:3px;
  padding:4px 16px;position:relative;
  transition:all .3s ease;
}
.ascent-num{
  font-size:.5rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.28);font-weight:600;
  font-family:'DM Sans',sans-serif;
  transition:color .3s ease;
}
.ascent-label{
  font-size:.58rem;letter-spacing:.12em;text-transform:uppercase;
  color:rgba(168,168,160,.32);
  transition:color .3s ease;
}
.ascent-node.--active .ascent-num{color:rgba(200,168,75,.75)}
.ascent-node.--active .ascent-label{color:rgba(200,168,75,.55)}
.ascent-node.--active::after{
  content:'';position:absolute;bottom:-2px;left:50%;width:16px;height:2px;
  transform:translateX(-50%);border-radius:1px;
  background:rgba(200,168,75,.4);
}
.ascent-line{
  width:32px;height:1px;flex-shrink:0;position:relative;
  background:linear-gradient(90deg,rgba(200,168,75,.06),rgba(200,168,75,.16),rgba(200,168,75,.06));
}
/* Subtle directional pulse on lines */
.ascent-line::after{
  content:'';position:absolute;top:0;left:0;width:8px;height:1px;
  background:rgba(200,168,75,.3);border-radius:1px;
  animation:ascent-pulse 3.5s ease-in-out infinite;
}
@keyframes ascent-pulse{
  0%,100%{left:0;opacity:0}
  15%{opacity:1}
  85%{opacity:1}
  100%{left:calc(100% - 8px);opacity:0}
}

/* ════════════════════════════════════════════════════
   TIER GRID — 5-column ascension row
   ════════════════════════════════════════════════════ */
.tier-grid-5{
  display:grid;grid-template-columns:repeat(5,1fr);
  gap:1px;
  background:rgba(200,168,75,.035);
  position:relative;z-index:2;
  border-radius:3px;
  overflow:hidden;
}
/* System connector trace behind cards */
.tier-grid-5::before{
  content:'';position:absolute;top:50%;left:0;right:0;height:1px;
  background:linear-gradient(90deg,
    transparent 2%,
    rgba(200,168,75,.04) 10%,
    rgba(200,168,75,.08) 30%,
    rgba(200,168,75,.12) 50%,
    rgba(200,168,75,.08) 70%,
    rgba(200,168,75,.04) 90%,
    transparent 98%
  );
  pointer-events:none;z-index:0;
}
/* Ambient glow behind center focal card */
.tier-grid-5::after{
  content:'';position:absolute;top:-80px;left:25%;width:50%;height:calc(100% + 160px);
  background:radial-gradient(ellipse at 50% 38%,rgba(200,168,75,.045) 0%,transparent 60%);
  pointer-events:none;z-index:0;
}

/* ── Node connector dots aligned with each card ── */
.tier-grid-5 .tier::after{
  content:'';position:absolute;bottom:-1px;left:50%;
  width:3px;height:3px;border-radius:50%;
  transform:translateX(-50%);
  background:rgba(200,168,75,.15);
  z-index:3;
  transition:background .35s ease,box-shadow .35s ease;
}
.tier-grid-5 .tier:hover::after{
  background:rgba(200,168,75,.4);
  box-shadow:0 0 6px rgba(200,168,75,.2);
}

/* ── Base tier panel ── */
.tier{
  background:linear-gradient(180deg,rgba(14,13,10,.98) 0%,rgba(11,10,8,.98) 100%);
  padding:32px 26px 26px;
  position:relative;overflow:hidden;z-index:1;
  border:none;
  display:flex;flex-direction:column;
  min-height:100%;
  transition:
    background .4s ease,
    box-shadow .4s ease,
    opacity .35s ease,
    filter .35s ease,
    transform .4s cubic-bezier(.23,1,.32,1);
}
/* Glass edge highlight */
.tier::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.1),transparent);
  transition:background .35s ease;
}
.tier:hover::before{
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.38),transparent);
}

/* ── Hover psychology: sibling dimming ── */
.tier-grid-5:hover .tier{
  opacity:.72;filter:brightness(.92);
}
.tier-grid-5:hover .tier:hover{
  opacity:1;filter:brightness(1);
  background:linear-gradient(180deg,rgba(18,16,12,.99) 0%,rgba(14,13,10,.99) 100%);
  box-shadow:
    0 0 52px rgba(200,168,75,.05),
    0 16px 48px rgba(0,0,0,.35);
  transform:translateY(-3px);
}
/* Keep focal visible even when siblings are hovered */
.tier-grid-5:hover .tier.focal{opacity:.82;filter:brightness(.96)}
.tier-grid-5:hover .tier.focal:hover{opacity:1;filter:brightness(1)}

/* ── Stagger reveal delays ── */
.tier-grid-5 .tier:nth-child(1){transition-delay:.03s}
.tier-grid-5 .tier:nth-child(2){transition-delay:.07s}
.tier-grid-5 .tier:nth-child(3){transition-delay:.11s}
.tier-grid-5 .tier:nth-child(4){transition-delay:.15s}
.tier-grid-5 .tier:nth-child(5){transition-delay:.19s}

/* ── Ordinal step indicator ── */
.tier-step{
  font-size:.5rem;letter-spacing:.3em;text-transform:uppercase;
  color:rgba(200,168,75,.18);margin-bottom:12px;font-weight:600;
  display:flex;align-items:center;gap:8px;
  font-family:'DM Sans',sans-serif;
}
.tier-step::after{
  content:'';flex:1;height:1px;
  background:linear-gradient(90deg,rgba(200,168,75,.08),transparent 80%);
}

/* ── Stage flag ── */
.tier-flag{
  display:block;font-size:.6rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.45);margin-bottom:6px;font-weight:400;
}

/* ── Title ── */
.tier-name{
  font-family:'Cormorant Garamond',serif;
  font-size:1.28rem;font-weight:300;color:var(--ivory);
  margin-bottom:14px;line-height:1.2;letter-spacing:.01em;
}

/* ── Price ── */
.tier-price{
  font-family:'Cormorant Garamond',serif;
  font-size:2.6rem;font-weight:300;color:var(--gold);
  line-height:1;margin-bottom:6px;
}
.tier-price sup{font-size:.92rem;vertical-align:top;margin-top:6px;color:var(--gold-dim);opacity:.6}
.tier-price sub{font-size:.68rem;color:rgba(168,168,160,.42);letter-spacing:.01em;font-family:'DM Sans',sans-serif}

/* ── Position line ── */
.tier-position{
  font-size:.72rem;letter-spacing:.015em;color:rgba(168,168,160,.4);
  line-height:1.62;font-style:italic;margin-bottom:16px;
  border-left:2px solid rgba(200,168,75,.12);padding-left:11px;
  max-width:240px;
  min-height:3.8em;
}

/* ── Divider ── */
.tier-divider{
  width:100%;height:1px;margin:2px 0 16px;
  background:linear-gradient(90deg,rgba(200,168,75,.1),transparent 75%);
}

/* ── Feature list ── */
.tier-features{list-style:none;display:flex;flex-direction:column;gap:10px;margin-bottom:0}
.tier-features li{
  display:flex;align-items:flex-start;gap:9px;
  font-size:.74rem;color:rgba(168,168,160,.6);line-height:1.58;
}
.tier-features li svg{
  flex-shrink:0;margin-top:2px;width:13px;height:13px;
  color:var(--gold);opacity:.5;
  transition:opacity .25s ease,transform .25s ease;
}
.tier:hover .tier-features li svg{opacity:.82;transform:translateX(1px)}
.tier-features li strong{color:rgba(237,232,222,.82);font-weight:400}

/* ── Tier stack (flex-grow body) ── */
.tier-stack{display:flex;flex-direction:column;flex:1}

/* ── Actions footer ── */
.tier-actions{
  margin-top:auto;
  display:flex;flex-direction:column;gap:8px;
  padding-top:18px;
  border-top:1px solid rgba(200,168,75,.05);
  min-height:68px;
  justify-content:flex-end;
}

/* ── CTA buttons ── */
.tier-cta{
  display:flex;align-items:center;justify-content:center;
  width:100%;min-height:44px;padding:12px 14px;
  text-align:center;font-size:.64rem;letter-spacing:.16em;text-transform:uppercase;
  text-decoration:none;
  background:transparent;color:var(--gold);
  border:1px solid rgba(200,168,75,.16);
  transition:all .32s cubic-bezier(.23,1,.32,1);
  font-weight:500;font-family:'DM Sans',sans-serif;
  position:relative;overflow:hidden;
}
/* CTA hover shimmer */
.tier-cta::before{
  content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.06),transparent);
  transition:left .5s ease;pointer-events:none;
}
.tier-cta:hover::before{left:100%}
.tier-cta:hover{
  background:rgba(200,168,75,.05);
  border-color:rgba(200,168,75,.32);
  color:var(--gold-lt);
  transform:translateY(-1px);
  box-shadow:0 4px 18px rgba(200,168,75,.06);
}
.tier-book{
  display:block;width:100%;margin-top:6px;padding:10px 14px;background:transparent;
  border:1px solid rgba(200,168,75,.07);color:var(--muted);font-size:.6rem;font-weight:400;
  letter-spacing:.12em;text-transform:uppercase;cursor:pointer;transition:all .3s;
  font-family:'DM Sans',sans-serif;
}
.tier-book:hover{border-color:var(--gold-dim);color:var(--gold)}
.tier-commitment{font-size:.68rem;color:rgba(168,168,160,.38);margin-bottom:8px;line-height:1.6;letter-spacing:.02em}

/* ── Gated tooltip ── */
.tier-gated{
  margin-top:12px;padding:11px 13px;border:1px solid rgba(200,168,75,.07);
  font-size:.7rem;line-height:1.62;color:rgba(168,168,160,.5);
  display:flex;align-items:flex-start;gap:9px;
}
.tier-gated-icon{color:rgba(200,168,75,.28);flex-shrink:0;margin-top:1px;font-size:.7rem}
.tier-gated strong{color:rgba(237,232,222,.55);font-weight:400}

/* ═══════════════════════════════════════════
   TIER VARIANTS — Progressive visual weight
   ═══════════════════════════════════════════ */

/* ── Scan tier (lightest, entry) ── */
.tier.scan-tier{background:linear-gradient(180deg,rgba(12,11,9,.96) 0%,rgba(10,9,7,.96) 100%)}
.tier.scan-tier .tier-flag{color:rgba(200,168,75,.28)}
.tier.scan-tier .tier-name{font-size:1.18rem;color:rgba(168,168,160,.68)}
.tier.scan-tier .tier-price{font-size:2.2rem;color:rgba(200,168,75,.32)}
.tier.scan-tier .tier-price sup{color:rgba(200,168,75,.22)}
.tier.scan-tier .tier-step{color:rgba(200,168,75,.14)}

/* ── Report tier ── */
.tier.report-tier{background:linear-gradient(180deg,rgba(13,12,10,.97) 0%,rgba(11,10,8,.97) 100%)}
.tier.report-tier .tier-step{color:rgba(200,168,75,.16)}

/* ── Focal tier (primary recommendation) ── */
.tier.focal{
  background:linear-gradient(180deg,rgba(16,14,11,.99) 0%,rgba(13,12,9,.99) 100%);
  z-index:2;
  box-shadow:
    inset 0 1px 0 rgba(200,168,75,.18),
    0 0 64px rgba(200,168,75,.04),
    0 20px 56px rgba(0,0,0,.4);
}
.tier.focal::before{
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.45),transparent);
}
.tier-grid-5:hover .tier.focal:hover{
  box-shadow:
    inset 0 1px 0 rgba(200,168,75,.25),
    0 0 80px rgba(200,168,75,.07),
    0 24px 64px rgba(0,0,0,.5);
  background:linear-gradient(180deg,rgba(19,17,13,1) 0%,rgba(15,14,11,1) 100%);
  transform:translateY(-4px);
}
.tier.focal .tier-flag{color:var(--gold)}
.tier.focal .tier-name{font-weight:400;font-size:1.34rem}
.tier.focal .tier-step{color:rgba(200,168,75,.38)}
.tier.focal .tier-cta{
  background:var(--gold);color:var(--bg);border:1px solid var(--gold);
  font-weight:600;
}
.tier.focal .tier-cta:hover{
  background:var(--gold-lt);border-color:var(--gold-lt);
  box-shadow:0 6px 22px rgba(200,168,75,.2);transform:translateY(-2px);
}
.tier.focal .tier-cta::before{
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.12),transparent);
}
/* Focal node dot emphasized */
.tier-grid-5 .tier.focal::after{
  width:4px;height:4px;
  background:rgba(200,168,75,.35);
  box-shadow:0 0 8px rgba(200,168,75,.15);
}

/* ═══════════════════════════════════════════
   ANCHOR ROW — Market Control (apex)
   ═══════════════════════════════════════════ */
.tier-anchor-row{
  margin-top:56px;padding-top:28px;
  display:grid;grid-template-columns:1fr minmax(380px,700px) 1fr;
  align-items:start;position:relative;z-index:2;
}
/* Vertical connector bridge from grid to apex */
.tier-anchor-row::before{
  content:'';position:absolute;top:-28px;left:50%;width:1px;height:56px;
  transform:translateX(-50%);
  background:linear-gradient(180deg,rgba(200,168,75,.08),rgba(200,168,75,.2) 50%,rgba(200,168,75,.08));
}
/* Ambient halo behind apex card */
.tier-anchor-row::after{
  content:'';position:absolute;top:0;left:50%;
  width:min(720px,96%);height:calc(100% + 28px);
  transform:translateX(-50%);
  border:1px solid rgba(200,168,75,.05);
  border-radius:6px;
  background:
    radial-gradient(ellipse at 50% 0%,rgba(200,168,75,.03) 0%,transparent 45%),
    radial-gradient(ellipse at 50% 100%,rgba(200,168,75,.015) 0%,transparent 40%);
  pointer-events:none;z-index:0;
}
.tier-anchor-row .tier.prime{grid-column:2}

/* ── Prime / Apex card ── */
.tier.prime{
  background:linear-gradient(180deg,rgba(17,15,12,.99) 0%,rgba(12,11,8,.99) 100%);
  border:1px solid rgba(200,168,75,.16);
  border-radius:6px;
  padding:48px 52px;
}
.tier.prime::before{
  content:'';position:absolute;top:0;left:10%;width:80%;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.4),transparent);
  border-radius:0;
}
/* Bottom edge glow for apex */
.tier.prime .tier-stack::after{
  content:'';position:absolute;bottom:0;left:10%;width:80%;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.08),transparent);
}
.tier-anchor-row .tier.prime{
  box-shadow:
    inset 0 1px 0 rgba(200,168,75,.12),
    0 0 90px rgba(200,168,75,.05),
    0 28px 72px rgba(0,0,0,.42);
}
.tier.prime:hover{
  border-color:rgba(200,168,75,.28);
  background:linear-gradient(180deg,rgba(19,17,13,1) 0%,rgba(14,12,9,1) 100%);
  box-shadow:
    inset 0 1px 0 rgba(200,168,75,.2),
    0 0 110px rgba(200,168,75,.07),
    0 32px 80px rgba(0,0,0,.5);
  transform:translateY(-2px);
}
.tier.prime .tier-step{color:rgba(200,168,75,.3);font-size:.52rem;margin-bottom:14px}
.tier.prime .tier-flag{color:rgba(200,168,75,.65);font-size:.62rem;margin-bottom:8px}
.tier.prime .tier-name{font-size:1.6rem;font-weight:400;margin-bottom:20px;letter-spacing:.02em}
.tier.prime .tier-price{font-size:3.4rem;margin-bottom:10px}
.tier.prime .tier-position{max-width:440px;margin-bottom:24px;line-height:1.72;min-height:auto}
.tier.prime .tier-divider{margin:4px 0 20px;background:linear-gradient(90deg,rgba(200,168,75,.15),transparent 70%)}
.tier.prime .tier-features{gap:13px}
.tier.prime .tier-features li{font-size:.8rem}
.tier.prime .tier-features li svg{width:15px;height:15px;opacity:.65}
.tier.prime .tier-actions{padding-top:24px;min-height:auto;border-top-color:rgba(200,168,75,.08)}
.tier.prime .tier-cta{
  min-height:58px;padding:18px 20px;
  font-size:.72rem;letter-spacing:.2em;font-weight:600;
  background:linear-gradient(180deg,#d8be72 0%,#c8a84b 100%);
  color:var(--bg);border:1px solid rgba(226,201,125,.5);
  box-shadow:0 4px 20px rgba(200,168,75,.1);
}
.tier.prime .tier-cta::before{
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.15),transparent);
}
.tier.prime .tier-cta:hover{
  background:linear-gradient(180deg,#e0c97e 0%,#d4b45a 100%);
  border-color:rgba(226,201,125,.7);
  box-shadow:0 8px 32px rgba(200,168,75,.22);
  transform:translateY(-2px);
}
.tier.prime .tier-book{font-size:.62rem;padding:11px 16px;border-color:rgba(200,168,75,.1)}
.tier.prime .tier-commitment{font-size:.7rem;color:rgba(168,168,160,.42);letter-spacing:.03em}

/* ── Bottom progression text ── */
.offer-bottom-line{
  text-align:center;font-size:.76rem;color:rgba(168,168,160,.38);
  letter-spacing:.03em;padding:18px 0 3px;
  position:relative;z-index:2;font-style:italic;
}
.offer-bottom-sub{
  text-align:center;font-size:.64rem;color:rgba(168,168,160,.22);
  letter-spacing:.05em;padding:4px 0 18px;
  position:relative;z-index:2;
}

/* ═══════════════════════════════════════════
   OFFER TRUST / SCARCITY / VALUE BLOCKS
   ═══════════════════════════════════════════ */
.offer-trust-line{margin-top:28px;padding:18px 22px;border:1px solid rgba(200,168,75,.08);background:rgba(10,9,7,.6)}
.offer-trust-main{font-size:.86rem;color:var(--ivory);line-height:1.75;opacity:.8}
.offer-trust-sub{font-family:'Cormorant Garamond',serif;font-style:italic;font-size:.94rem;color:rgba(168,168,160,.58);margin-top:6px;display:block}
.offer-scarcity{padding-bottom:28px;border-bottom:1px solid rgba(200,168,75,.12)}
.offer-scarcity-main{font-family:'Cormorant Garamond',serif;font-size:clamp(1.25rem,2.2vw,1.65rem);font-weight:300;line-height:1.25;color:rgba(237,232,222,.86);letter-spacing:-.01em;margin-bottom:8px}
.offer-scarcity-sub{font-size:.85rem;color:rgba(168,168,160,.68);line-height:1.6}
.offer-value{padding:20px 22px;margin:24px 0;background:rgba(9,8,6,.65);border:1px solid rgba(200,168,75,.15)}
.offer-value-price{font-size:1.04rem;font-weight:400;color:rgba(237,232,222,.9);line-height:1.55;display:block;margin-bottom:10px}
.offer-value-inline{font-size:.82rem;color:rgba(168,168,160,.72);line-height:1.6;display:block;margin-bottom:6px}
.offer-value-media{font-size:.74rem;color:rgba(168,168,160,.55);line-height:1.5;letter-spacing:.02em}

/* ═══════════════════════════════════════════
   RESPONSIVE — Pricing
   ═══════════════════════════════════════════ */
@media(max-width:1100px){
  .tier-grid-5{grid-template-columns:repeat(3,1fr)}
  .ascent-rail{flex-wrap:wrap;gap:4px;max-width:480px;padding:12px 20px}
  .ascent-line{width:16px}
  .tier-position{min-height:auto}
}
@media(max-width:900px){
  #offer{padding:40px 24px 48px}
  .offer-intro{grid-template-columns:1fr;gap:28px}
  .tier-grid-5{grid-template-columns:1fr 1fr}
  .tier-anchor-row{grid-template-columns:1fr;margin-top:32px;padding-top:22px}
  .tier-anchor-row .tier.prime{grid-column:auto}
  .tier-anchor-row::before{height:32px;top:-16px}
  .ascent-rail{display:none}
  .tier-position{min-height:auto}
  /* Disable sibling dimming on touch */
  .tier-grid-5:hover .tier{opacity:1;filter:none}
}
@media(max-width:520px){
  .tier-grid-5{grid-template-columns:1fr}
  .tier-position{max-width:100%}
  .tier.prime{padding:36px 28px}
  .tier.prime .tier-position{max-width:100%}
  .tier-name{font-size:1.3rem}
  .tier-price{font-size:2.4rem}
  .tier-price sup{font-size:.95rem}
  .tier.prime .tier-name{font-size:1.5rem}
  .tier.prime .tier-price{font-size:3rem}
}

/* ── Value anchor ── */
.value-anchor{
  padding:56px 64px;max-width:1200px;margin:0 auto;
  border-top:1px solid rgba(200,168,75,.08);
}
.value-anchor-inner{
  max-width:760px;margin:0 auto;text-align:center;
}
.va-main{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.5rem,2.6vw,2rem);font-weight:300;
  line-height:1.4;color:var(--ivory);margin-bottom:16px;letter-spacing:-.01em;
}
.va-main em{font-style:italic;color:var(--gold)}
.va-sub{
  font-size:.96rem;color:rgba(168,168,160,.68);line-height:1.85;
  max-width:600px;margin:0 auto;
}

/* ── Execution services ── */
.exec-services{
  padding:56px 64px;max-width:1200px;margin:0 auto;
  border-top:1px solid var(--border);
}
.exec-eyebrow{
  font-size:.68rem;letter-spacing:.28em;text-transform:uppercase;
  color:var(--gold);display:flex;align-items:center;gap:14px;margin-bottom:22px;
}
.exec-eyebrow::before{content:'';width:28px;height:1px;background:var(--gold)}
.exec-hed{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.8rem,2.8vw,2.5rem);font-weight:300;
  line-height:1.1;color:var(--ivory);margin-bottom:12px;
}
.exec-hed em{font-style:italic;color:var(--gold)}
.exec-intro{font-size:.92rem;color:rgba(168,168,160,.60);margin-bottom:36px;max-width:560px;line-height:1.76}
.exec-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1px;background:rgba(200,168,75,.08)}
.exec-card{
  background:var(--bg);padding:36px 32px;position:relative;overflow:hidden;
  transition:background .28s,transform .28s cubic-bezier(.23,1,.32,1),box-shadow .28s;
}
.exec-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.10),transparent);
}
.exec-card:hover{background:rgba(14,13,10,1);transform:translateY(-1px);box-shadow:0 12px 36px rgba(0,0,0,.45)}
.exec-label{
  font-size:.65rem;letter-spacing:.24em;text-transform:uppercase;
  color:var(--gold-dim);display:block;margin-bottom:14px;
}
.exec-title{
  font-family:'Cormorant Garamond',serif;
  font-size:1.5rem;font-weight:400;color:var(--ivory);margin-bottom:10px;line-height:1.2;
}
.exec-body{font-size:.88rem;color:var(--muted);line-height:1.72}
.exec-learn{
  display:inline-block;margin-top:14px;
  font-size:.66rem;letter-spacing:.16em;text-transform:uppercase;
  color:rgba(200,168,75,.42);text-decoration:none;transition:color .25s;
}
.exec-learn:hover{color:var(--gold)}
.exec-all{
  display:block;text-align:center;margin-top:28px;
  font-size:.72rem;letter-spacing:.16em;text-transform:uppercase;
  color:rgba(168,168,160,.38);text-decoration:none;transition:color .25s;
}
.exec-all:hover{color:var(--gold)}
.exec-positioning{
  margin-top:32px;padding:22px 28px;
  border:1px solid rgba(200,168,75,.12);
  font-size:.88rem;font-style:italic;
  color:rgba(168,168,160,.62);line-height:1.76;
  text-align:center;
}

/* ── Positioning block ── */
.access-position{
  padding:52px 64px;max-width:1200px;margin:0 auto;
  border-top:1px solid var(--border);
  display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:center;
}
.ap-main{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.6rem,2.5vw,2.1rem);font-weight:300;
  line-height:1.35;color:var(--ivory);margin-bottom:18px;
}
.ap-main em{font-style:italic;color:var(--gold)}
.ap-qualifier{
  font-size:.86rem;line-height:1.82;
  color:rgba(168,168,160,.58);
  border-left:2px solid rgba(200,168,75,.18);padding-left:16px;
}

/* ── Access model ── */
.access-model{
  padding:52px 64px;max-width:1200px;margin:0 auto;
  border-top:1px solid var(--border);
}
.am-eyebrow{
  font-size:.68rem;letter-spacing:.28em;text-transform:uppercase;
  color:rgba(200,168,75,.55);margin-bottom:14px;
}
.am-hed{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.4rem,2.2vw,1.9rem);font-weight:300;
  color:var(--ivory);margin-bottom:20px;line-height:1.2;
}
.am-body{display:flex;flex-direction:column;gap:12px;max-width:640px}
.am-line{
  font-size:.94rem;color:rgba(168,168,160,.74);line-height:1.78;
  display:flex;align-items:flex-start;gap:12px;
}
.am-line::before{
  content:'—';color:rgba(200,168,75,.35);flex-shrink:0;
  font-family:'Cormorant Garamond',serif;margin-top:1px;
}

/* ── Decision guide ── */
.decision-guide{
  padding:44px 64px;max-width:1200px;margin:0 auto;
  border-top:1px solid var(--border);
}
.dg-hed{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.3rem,2vw,1.7rem);font-weight:300;
  color:rgba(237,232,222,.75);margin-bottom:22px;letter-spacing:.01em;
}
.dg-rows{display:flex;flex-direction:column;gap:13px}
.dg-row{
  display:flex;align-items:baseline;gap:16px;
  font-size:.88rem;line-height:1.6;color:rgba(168,168,160,.78);
}
.dg-if{color:rgba(168,168,160,.44);font-size:.72rem;letter-spacing:.12em;text-transform:uppercase;flex-shrink:0;width:62px}
.dg-goal{color:rgba(168,168,160,.62);flex:1}
.dg-arrow{color:rgba(200,168,75,.4);flex-shrink:0;align-self:center}
.dg-tier{color:var(--gold);font-weight:400;min-width:80px;text-align:right}

/* ── Pricing CTA ── */
.pricing-cta{
  padding:32px 64px 28px;max-width:1200px;margin:0 auto;text-align:center;
  border-top:1px solid rgba(200,168,75,.08);
}
.pricing-cta-actions{display:flex;align-items:center;justify-content:center;gap:24px;margin-bottom:16px}
.pricing-cta-meta{font-size:.68rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(168,168,160,.42)}

/* ── Commitment note (after #how steps) ── */
.commitment-note{
  max-width:720px;margin:0 auto;padding:52px 64px 44px;
  text-align:center;
  border-top:1px solid rgba(200,168,75,.09);
  position:relative;
}
.commitment-note::before{
  content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);
  width:100px;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.30),transparent);
}
.cn-hed{
  font-family:'Cormorant Garamond',serif;font-weight:300;
  font-size:clamp(1.4rem,2.3vw,1.82rem);color:var(--ivory);
  letter-spacing:-.01em;line-height:1.28;margin-bottom:16px;
  max-width:560px;margin-left:auto;margin-right:auto;
}
.commitment-note{
  max-width:660px;margin:0 auto;padding:72px 64px 60px;
  text-align:center;
  border-top:1px solid rgba(200,168,75,.09);
  position:relative;
}
.commitment-note::before{
  content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);
  width:100px;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.30),transparent);
}
.cn-hed{
  font-family:'Cormorant Garamond',serif;font-weight:300;
  font-size:clamp(1.6rem,2.6vw,2.1rem);color:var(--ivory);
  letter-spacing:-.01em;line-height:1.32;margin-bottom:20px;
  max-width:580px;margin-left:auto;margin-right:auto;
}
.cn-aha{
  font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;
  font-size:clamp(1.1rem,1.8vw,1.32rem);
  color:rgba(200,168,75,.62);
  line-height:1.5;letter-spacing:-.005em;
  margin-bottom:28px;
}
.cn-body{
  font-size:.94rem;color:rgba(168,168,160,.64);line-height:1.9;
  max-width:520px;margin:0 auto;
  margin-bottom:20px;
}
.cn-surface{
  font-size:.7rem;letter-spacing:.18em;text-transform:uppercase;
  color:rgba(200,168,75,.38);
}
.cn-secondary{
  display:none;
}
.cn-link{
  color:rgba(200,168,75,.58);text-decoration:none;
  border-bottom:1px solid rgba(200,168,75,.18);
  transition:color .2s,border-color .2s;
}
.cn-link:hover{color:rgba(200,168,75,.88);border-color:rgba(200,168,75,.46)}

/* ── Fit screening block ── */
.fit-screen{
  max-width:1200px;margin:0 auto;padding:52px 64px;
  text-align:center;border-top:1px solid rgba(200,168,75,.06);
}
.fs-hed{
  font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;
  font-size:clamp(1.5rem,2.6vw,2.1rem);color:rgba(237,232,222,.80);
  letter-spacing:-.01em;margin-bottom:20px;
}
.fs-body{
  max-width:640px;margin:0 auto 20px;
}
.fs-body p{
  font-size:.875rem;color:rgba(168,168,160,.60);line-height:1.85;margin-bottom:10px;
}
.fs-note{
  font-size:.76rem;letter-spacing:.08em;text-transform:uppercase;
  color:rgba(200,168,75,.38);max-width:580px;margin:0 auto;
  line-height:1.7;
}

/* ── Final close ── */
.final-close{
  padding:60px 64px 72px;max-width:1200px;margin:0 auto;text-align:center;
  border-top:1px solid rgba(200,168,75,.10);
}
.fc-main{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.9rem,3.4vw,2.8rem);font-weight:300;line-height:1.25;
  color:var(--ivory);margin-bottom:10px;
}
.fc-question{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.9rem,3.4vw,2.8rem);font-weight:300;font-style:italic;
  color:rgba(237,232,222,.48);margin-bottom:32px;
}
.fc-tagline{
  font-size:.72rem;letter-spacing:.24em;text-transform:uppercase;
  color:rgba(200,168,75,.45);
}

/* ── Final Closing CTA section ── */
.fcc{
  position:relative;overflow:hidden;
  padding:110px 64px;text-align:center;
  border-top:1px solid rgba(200,168,75,.10);
}
.fcc::before{
  content:'';
  position:absolute;inset:0;
  background:radial-gradient(ellipse 70% 60% at 50% 48%,rgba(200,168,75,.08) 0%,rgba(200,168,75,.02) 55%,transparent 72%);
  pointer-events:none;
  animation:fccGlow 12s ease-in-out infinite;
  z-index:0;
}
.fcc::after{
  content:'';
  position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(200,168,75,.01) 1px,transparent 1px),
    linear-gradient(90deg,rgba(200,168,75,.01) 1px,transparent 1px);
  background-size:72px 72px;
  pointer-events:none;
  z-index:0;
}
@keyframes fccGlow{
  0%,100%{opacity:.55}
  50%{opacity:.82}
}
.fcc-canvas{
  position:absolute;inset:0;width:100%;height:100%;
  pointer-events:none;z-index:0;opacity:.55;
}
.fcc-inner{
  position:relative;z-index:1;
  max-width:1040px;margin:0 auto;
}
.fcc-eye{
  font-size:.63rem;letter-spacing:.38em;text-transform:uppercase;
  color:rgba(200,168,75,.50);margin-bottom:48px;
  display:flex;align-items:center;justify-content:center;gap:20px;
}
.fcc-eye::before,.fcc-eye::after{
  content:'';width:44px;height:1px;background:rgba(200,168,75,.16);
}
.fcc-hed{
  font-family:'Cormorant Garamond',serif;font-weight:200;
  line-height:1.08;margin-bottom:36px;
  display:flex;flex-direction:column;gap:.30em;
}
.fcc-hed-1{
  font-size:clamp(3rem,4.8vw,4.4rem);
  color:var(--ivory);letter-spacing:-.020em;font-weight:300;
  white-space:nowrap;
}
.fcc-hed-2{
  font-size:clamp(2.6rem,4.2vw,3.8rem);
  color:rgba(237,232,222,.44);letter-spacing:-.018em;font-style:italic;
  white-space:nowrap;
}
.fcc-sub{
  font-size:.96rem;color:rgba(168,168,160,.70);
  line-height:1.85;margin-bottom:28px;
  max-width:560px;margin-left:auto;margin-right:auto;
}
.fcc-gold{
  font-family:'Cormorant Garamond',serif;font-weight:400;font-style:italic;
  font-size:clamp(1.4rem,2.4vw,2.0rem);
  letter-spacing:.01em;
  background:linear-gradient(90deg,var(--gold) 0%,rgba(245,228,152,.92) 44%,var(--gold) 62%,var(--gold) 100%);
  background-size:260% 100%;
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  background-clip:text;
  animation:fccGoldShimmer 14s ease-in-out infinite;
  margin-bottom:20px;
  display:block;
}
@keyframes fccGoldShimmer{
  0%,100%{background-position:120% 0}
  35%,65%{background-position:0% 0}
}
.fcc-micro{
  font-size:.70rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(168,168,160,.42);margin-bottom:56px;
}
.fcc-rule{
  display:block;width:80px;height:1px;margin:0 auto 52px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.24),transparent);
}
.fcc-actions{
  display:flex;align-items:stretch;justify-content:center;gap:24px;
  flex-wrap:wrap;margin-bottom:28px;
}
/* ── FCC path cards ── */
.fcc-card{
  flex:1;min-width:240px;max-width:340px;
  border-radius:10px;padding:34px 28px 30px;
  text-align:center;position:relative;
  backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);
}
.fcc-card--scan{
  background:rgba(200,168,75,.05);
  border:1px solid rgba(200,168,75,.10);
}
.fcc-card--system{
  background:rgba(106,175,144,.03);
  border:1px solid rgba(106,175,144,.09);
}
.fcc-card-label{
  font-size:.62rem;letter-spacing:.16em;text-transform:uppercase;
  color:rgba(168,168,160,.45);margin-bottom:10px;
}
.fcc-card-title{
  font-size:1.08rem;color:var(--ivory);font-family:'Cormorant Garamond',serif;
  font-weight:300;margin-bottom:20px;line-height:1.3;
}
.fcc-card-note{
  font-size:.64rem;letter-spacing:.06em;color:rgba(168,168,160,.28);
  margin-top:14px;line-height:1.5;
}
.fcc-primary{
  display:inline-flex;align-items:center;justify-content:center;
  background:var(--gold);color:#080808;
  font-family:'DM Sans',sans-serif;
  font-size:.82rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;
  padding:18px 44px;border-radius:6px;border:none;cursor:pointer;
  transition:background .3s,transform .2s,box-shadow .2s;
  min-height:56px;text-decoration:none;
}
.fcc-primary:hover{
  background:var(--gold-lt);transform:translateY(-2px);
  box-shadow:0 8px 28px rgba(200,168,75,.22);
}
.fcc-secondary{
  display:inline-flex;align-items:center;justify-content:center;
  font-size:.80rem;letter-spacing:.14em;text-transform:uppercase;
  color:var(--green);text-decoration:none;
  background:rgba(106,175,144,.10);
  border:1px solid rgba(106,175,144,.18);
  padding:16px 36px;border-radius:6px;
  transition:background .3s,transform .2s,border-color .2s;
  min-height:54px;
}
.fcc-secondary:hover{
  background:rgba(106,175,144,.16);border-color:rgba(106,175,144,.32);
  transform:translateY(-2px);
}
.fcc-reassure{
  font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;
  color:rgba(168,168,160,.32);margin-top:6px;
}

/* ── How-this-works trust strip ── */
.how-strip{padding:32px 64px 28px;max-width:1200px;margin:0 auto;border-top:1px solid rgba(200,168,75,.10);text-align:center}
.how-strip-hed{font-size:.68rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(200,168,75,.55);margin-bottom:20px}
.how-strip-bullets{display:flex;justify-content:center;align-items:center;gap:20px 32px;flex-wrap:wrap;margin-bottom:14px}
.how-strip-item{display:flex;align-items:center;gap:8px}
.how-strip-dot{width:5px;height:5px;border-radius:50%;background:var(--gold);flex-shrink:0;box-shadow:0 0 6px rgba(200,168,75,.45);animation:pulseDot 2.4s ease-in-out infinite}
.how-strip-item:nth-child(2) .how-strip-dot{animation-delay:.4s}
.how-strip-item:nth-child(3) .how-strip-dot{animation-delay:.8s}
.how-strip-item:nth-child(4) .how-strip-dot{animation-delay:1.2s}
.how-strip-item:nth-child(5) .how-strip-dot{animation-delay:1.6s}
@keyframes pulseDot{0%,100%{opacity:.5;transform:scale(1)}50%{opacity:1;transform:scale(1.5)}}
.how-strip-label{font-size:.82rem;color:rgba(168,168,160,.72);letter-spacing:.01em}
.how-strip-sub{font-size:.72rem;color:rgba(168,168,160,.48);letter-spacing:.08em;text-transform:uppercase}

/* ── Value anchor additions ── */
.va-eye{font-size:.66rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(200,168,75,.45);margin-bottom:16px}
.va-contrast{display:grid;grid-template-columns:1fr 1fr;gap:12px 40px;margin:28px auto 28px;max-width:620px;text-align:left}
.va-col-hed{font-size:.70rem;letter-spacing:.16em;text-transform:uppercase;margin-bottom:10px}
.va-col--left .va-col-hed{color:rgba(168,168,160,.38)}
.va-col--right .va-col-hed{color:rgba(200,168,75,.5)}
.va-col-item{font-size:.88rem;line-height:1.65;padding-left:14px;position:relative;margin-bottom:4px}
.va-col-item::before{content:'—';position:absolute;left:0;color:rgba(168,168,160,.28)}
.va-col--left .va-col-item{color:rgba(168,168,160,.58)}
.va-col--right .va-col-item{color:rgba(237,232,222,.70)}
.va-col--right .va-col-item::before{color:rgba(200,168,75,.45)}
.va-close{font-family:'Cormorant Garamond',serif;font-size:clamp(1.1rem,1.8vw,1.4rem);font-weight:300;color:rgba(237,232,222,.75);line-height:1.5;border-top:1px solid rgba(200,168,75,.08);padding-top:22px;margin-top:8px}
.va-act{display:flex;align-items:center;justify-content:center;gap:28px;flex-wrap:wrap;}
.va-act .btn-primary{opacity:.86;}
.va-act .btn-primary:hover{opacity:1;}
.va-act .btn-ghost{opacity:.64;}
.va-act .btn-ghost:hover{opacity:1;}
.va-close em{color:var(--gold);font-style:italic}

/* ── Competitive positioning ── */
.comp-pos{padding:28px 64px;max-width:1200px;margin:0 auto;text-align:center;border-top:1px solid rgba(200,168,75,.05)}
.cp-line-1{font-size:.88rem;color:rgba(168,168,160,.50);letter-spacing:.02em;margin-bottom:6px}
.cp-line-2{font-family:'Cormorant Garamond',serif;font-size:clamp(1.1rem,1.9vw,1.45rem);font-weight:300;color:var(--ivory);letter-spacing:-.01em}

/* ── Service support block ── */
.svc-support{padding:56px 64px;max-width:1200px;margin:0 auto;border-top:1px solid var(--border)}
.ss-eye{font-size:.68rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(200,168,75,.72);margin-bottom:14px;display:flex;align-items:center;gap:14px}
.ss-eye::before{content:'';width:22px;height:1px;background:rgba(200,168,75,.52)}
.ss-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.6rem,2.6vw,2.2rem);font-weight:300;color:var(--ivory);margin-bottom:16px;line-height:1.18;letter-spacing:-.01em}
.ss-intro{font-size:.92rem;color:rgba(168,168,160,.80);line-height:1.80;max-width:580px;margin-bottom:32px}
.ss-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:12px 20px;margin-bottom:28px}
.ss-item{display:flex;align-items:flex-start;gap:12px;padding:13px 16px;background:rgba(200,168,75,.015);border:1px solid rgba(200,168,75,.07);border-radius:2px;transition:background .22s ease,border-color .30s cubic-bezier(.23,1,.32,1),transform .34s cubic-bezier(.22,.84,.36,1)}
.ss-item:hover{background:rgba(200,168,75,.04);border-color:rgba(200,168,75,.2);transform:translateY(-1px)}
.ss-item:hover .ss-icon{color:rgba(200,168,75,.9)}
.ss-item:hover .ss-label{color:rgba(237,232,222,.90)}
.ss-icon{width:14px;height:14px;flex-shrink:0;margin-top:2px;color:rgba(200,168,75,.58);transition:color .28s}
.ss-label{font-size:.84rem;color:rgba(168,168,160,.82);line-height:1.52;transition:color .28s}
.ss-note{font-size:.80rem;color:rgba(168,168,160,.62);font-style:italic;line-height:1.7;max-width:600px;border-top:1px solid rgba(200,168,75,.12);padding-top:20px;margin-top:6px;letter-spacing:.01em}

/* ── fcc wait line ── */
.fcc-wait{font-size:.84rem;color:rgba(168,168,160,.36);line-height:1.9;margin-top:14px;letter-spacing:.01em}
.fcc-wait em{color:rgba(200,168,75,.52);font-style:italic}

/* ── Responsive pricing/services ── */
@media(max-width:900px){
  .value-anchor,.exec-services,.access-position,.access-model,.decision-guide,.pricing-cta,.final-close{padding:48px 24px}
  .fcc{padding:80px 32px}
  .fcc-hed-1,.fcc-hed-2{font-size:clamp(1.9rem,5.2vw,2.8rem)}
  .fcc-canvas{opacity:.4}
  .fcc::before{background:radial-gradient(ellipse 70% 60% at 50% 48%,rgba(200,168,75,.06) 0%,transparent 65%)}
  .fcc-card{max-width:300px;padding:30px 24px 26px}
  .commitment-note{padding:40px 28px 36px}
  .fit-screen{padding:40px 24px}
  .exec-grid{grid-template-columns:1fr}
  .access-position{grid-template-columns:1fr;gap:28px}
}
@media(max-width:520px){
  .value-anchor,.exec-services,.access-position,.access-model,.decision-guide,.pricing-cta,.final-close{padding:36px 20px}
  .fcc{padding:64px 20px}
  .fcc-hed-1,.fcc-hed-2{white-space:normal}
  .fcc-hed-1{font-size:clamp(1.65rem,7.8vw,2.4rem)}
  .fcc-hed-2{font-size:clamp(1.45rem,6.8vw,2.1rem)}
  .fcc-canvas{opacity:.3}
  .fcc::before{animation:none;opacity:.6}
  .fcc::after{opacity:.4}
  .fcc-eye{margin-bottom:36px}
  .fcc-hed{margin-bottom:28px}
  .fcc-sub{font-size:.92rem;margin-bottom:22px}
  .fcc-gold{font-size:clamp(1.2rem,4.5vw,1.6rem);margin-bottom:16px}
  .fcc-rule{width:60px;margin:0 auto 36px}
  .fcc-actions{flex-direction:column;align-items:stretch;gap:14px}
  .fcc-card{max-width:100%;min-width:auto;padding:28px 22px 24px}
  .fcc-card-title{font-size:1.02rem;margin-bottom:18px}
  .fcc-primary{justify-content:center;width:100%;padding:18px 32px;min-height:54px}
  .fcc-secondary{width:100%;padding:16px 28px;min-height:52px}
  .fcc-reassure{font-size:.68rem}
  .commitment-note{padding:32px 20px 28px}
  .fit-screen{padding:32px 20px}
  .dg-row{flex-wrap:wrap;gap:8px}
  .dg-tier{text-align:left;min-width:auto}
  .dg-if{width:auto}
  .pricing-cta-actions{flex-direction:column;gap:16px}
  .pricing-cta-actions .btn-primary{width:100%;text-align:center;justify-content:center}
}
@media(max-width:430px){
  .fcc{padding:56px 18px}
  .fcc-hed-1{font-size:clamp(1.5rem,8vw,2.1rem)}
  .fcc-hed-2{font-size:clamp(1.3rem,7vw,1.9rem)}
  .fcc-sub{font-size:.88rem}
  .fcc-gold{font-size:clamp(1.1rem,5vw,1.4rem)}
  .fcc-card{padding:26px 20px 22px}
  .fcc-card-title{font-size:.98rem}
  .fcc-primary{font-size:.78rem;padding:16px 28px;min-height:52px}
  .fcc-secondary{font-size:.76rem;padding:14px 24px;min-height:50px}
  .fcc-card-note{font-size:.62rem}
}
@media(max-width:390px){
  .fcc{padding:48px 16px}
  .fcc-eye{font-size:.58rem;margin-bottom:28px}
  .fcc-hed{gap:.22em;margin-bottom:24px}
  .fcc-hed-1{font-size:clamp(1.4rem,8.5vw,1.9rem)}
  .fcc-hed-2{font-size:clamp(1.2rem,7.5vw,1.7rem)}
  .fcc-sub{font-size:.86rem;line-height:1.75}
  .fcc-gold{font-size:clamp(1rem,5.5vw,1.3rem)}
  .fcc-rule{margin:0 auto 28px}
  .fcc-card{padding:24px 18px 20px}
  .fcc-primary{font-size:.76rem;min-height:50px}
  .fcc-secondary{font-size:.74rem;min-height:48px}
}

/* ── Market Allocation ── */
.alloc-section{padding:68px 64px;max-width:1200px;margin:0 auto;position:relative}
.alloc-layout{display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:start}

/* left copy */
.alloc-eyebrow{
  font-size:.68rem;letter-spacing:.28em;text-transform:uppercase;
  color:var(--gold);display:flex;align-items:center;gap:14px;margin-bottom:16px;
}
.alloc-eyebrow::before{content:'';width:28px;height:1px;background:var(--gold)}
.alloc-hed{
  font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem,3.8vw,3.4rem);
  font-weight:300;line-height:1.1;color:var(--ivory);margin-bottom:14px;
}
.alloc-hed em{font-style:italic;color:rgba(200,168,75,.75)}
.alloc-sub{
  font-size:clamp(1rem,1.35vw,1.12rem);color:rgba(168,168,160,.88);line-height:1.92;margin-bottom:18px;
  display:flex;flex-direction:column;gap:10px;
}
.alloc-sub em{
  font-family:'Cormorant Garamond',serif;font-style:italic;
  color:var(--ivory);font-size:clamp(1.06rem,1.45vw,1.18rem);
}
.alloc-reinforce{
  font-size:clamp(.98rem,1.2vw,1rem);color:rgba(237,232,222,.68);line-height:1.78;
  display:flex;flex-direction:column;gap:7px;margin-bottom:18px;
  padding-left:20px;border-left:1px solid rgba(200,168,75,.28);
}
.alloc-urgency{
  font-family:'Cormorant Garamond',serif;font-style:italic;font-weight:300;
  font-size:clamp(1.25rem,1.9vw,1.55rem);color:rgba(200,168,75,.82);
  line-height:1.65;margin-bottom:18px;
}
.alloc-convert-label{
  display:block;font-size:clamp(.80rem,.9vw,.85rem);letter-spacing:.26em;text-transform:uppercase;
  color:rgba(168,168,160,.88);margin-bottom:18px;
}
.alloc-actions{display:flex;gap:16px;align-items:center;flex-wrap:wrap}

/* right panel */
.alloc-panel{position:relative}
.alloc-panel-label{
  font-size:.78rem;letter-spacing:.20em;text-transform:uppercase;
  color:rgba(200,168,75,.85);margin-bottom:18px;
  display:flex;align-items:center;gap:10px;
}
.alloc-panel-label::after{content:'';flex:1;height:1px;background:rgba(200,168,75,.18)}
/* hover tooltip on territory cells */
.alloc-cell-tooltip{
  position:absolute;bottom:calc(100% + 8px);left:50%;transform:translateX(-50%);
  background:#131210;border:1px solid rgba(200,168,75,.28);border-radius:6px;
  padding:8px 13px;font-size:.76rem;color:rgba(237,232,222,.88);line-height:1.4;
  white-space:nowrap;pointer-events:none;z-index:20;
  opacity:0;transition:opacity .18s;letter-spacing:.02em;
}
.alloc-cell:hover .alloc-cell-tooltip{opacity:1}
@media(max-width:768px){.alloc-cell-tooltip{display:none}.alloc-insight-cols{grid-template-columns:1fr}}
/* trust line below grid */
.alloc-trust-line{
  font-size:.80rem;color:rgba(168,168,160,.82);margin-top:14px;line-height:1.65;
  letter-spacing:.01em;
}
.alloc-grid{
  display:grid;grid-template-columns:1fr 1fr;
  gap:1px;background:rgba(200,168,75,.11);
}
.alloc-cell{
  background:var(--bg);padding:30px 26px;position:relative;overflow:hidden;
  transition:background .3s;
}
.alloc-cell::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.14),transparent);
}
.alloc-cell:hover{background:rgba(14,13,10,1)}
.alloc-region{
  font-family:'Cormorant Garamond',serif;font-weight:300;
  font-size:1.22rem;color:rgba(237,232,222,.95);margin-bottom:5px;line-height:1.25;
}
.alloc-states{
  font-size:.82rem;color:rgba(168,168,160,.72);letter-spacing:.05em;margin-bottom:13px;
}
.alloc-status{display:flex;flex-direction:column;gap:5px}
.alloc-status-row{display:flex;align-items:center;gap:9px}
.alloc-status-sub{font-size:.67rem;letter-spacing:.06em;color:rgba(168,168,160,.40);margin-left:17px;line-height:1.3}

/* ── Status dot animations ── */
@keyframes dotPulseActive{
  0%,100%{opacity:.65;box-shadow:0 0 5px rgba(74,140,110,.40)}
  50%{opacity:1;box-shadow:0 0 12px rgba(74,140,110,.75),0 0 24px rgba(74,140,110,.22)}
}
@keyframes dotGlowLimited{
  0%,100%{opacity:.72;box-shadow:0 0 5px rgba(200,168,75,.45)}
  50%{opacity:1;box-shadow:0 0 12px rgba(200,168,75,.82),0 0 22px rgba(200,168,75,.24)}
}
@keyframes dotPulseOpen{
  0%,100%{opacity:.55;box-shadow:0 0 5px rgba(112,152,184,.36)}
  50%{opacity:.90;box-shadow:0 0 12px rgba(112,152,184,.68),0 0 22px rgba(112,152,184,.22)}
}
.alloc-dot{
  width:8px;height:8px;border-radius:50%;flex-shrink:0;
  opacity:0;transition:opacity .4s;
}
.alloc-dot.alloc-dot-visible{opacity:1}
.alloc-dot.allocated{
  background:#4a8c6e;
  box-shadow:0 0 5px rgba(74,140,110,.35);
  animation:dotPulseActive 3.5s ease-in-out infinite;
}
.alloc-dot.limited{
  background:#c8993a;
  box-shadow:0 0 4px rgba(200,155,58,.40);
  animation:dotGlowLimited 3.8s ease-in-out infinite;
}
.alloc-dot.open{
  background:#7098b8;
  box-shadow:0 0 5px rgba(112,152,184,.32);
  animation:dotPulseOpen 4s ease-in-out infinite;
}

.alloc-status-label{font-size:.74rem;letter-spacing:.15em;text-transform:uppercase;font-weight:400}
.alloc-status-label.allocated{color:#6aaf90}
.alloc-status-label.limited{color:#c8993a}
.alloc-status-label.open{color:#88b0cc;letter-spacing:.18em}
/* ── Card micro-icon ── */
.alloc-cell-icon{
  position:absolute;top:18px;right:18px;
  color:var(--gold);opacity:.18;
  transition:opacity .3s;pointer-events:none;
}
.alloc-cell:hover .alloc-cell-icon{opacity:.36}
/* ── Legend layout ── */
.alloc-legend{
  margin-top:16px;padding:18px 22px;border:1px solid rgba(200,168,75,.18);
  display:flex;gap:22px;align-items:flex-start;flex-wrap:wrap;
  background:rgba(8,8,8,.6);
}
.alloc-legend-item{display:flex;align-items:flex-start;gap:10px;padding-top:2px}
.alloc-legend-item .alloc-dot{margin-top:3px;flex-shrink:0}
.alloc-legend-text{display:flex;flex-direction:column;gap:3px}
.alloc-legend-label{font-size:.72rem;letter-spacing:.15em;text-transform:uppercase;color:rgba(168,168,160,.85)}
.alloc-legend-desc{font-size:.67rem;color:rgba(168,168,160,.46);letter-spacing:.03em;line-height:1.5}
.alloc-avail-note{
  margin-top:14px;padding:20px 24px;
  border:1px solid rgba(200,168,75,.18);
  background:rgba(12,11,8,.7);
  font-size:clamp(.97rem,1.2vw,1rem);line-height:1.85;color:rgba(168,168,160,.82);
}
.alloc-avail-note strong{color:rgba(237,232,222,.82);font-weight:400}
.alloc-access-note{font-size:.74rem;color:rgba(168,168,160,.40);letter-spacing:.03em;line-height:1.65;margin-top:14px}

/* Body intro + insight columns */
.alloc-body-intro{
  font-size:.94rem;color:rgba(168,168,160,.82);line-height:1.75;
  margin-bottom:20px;
}
.alloc-insight-cols{
  display:grid;grid-template-columns:1fr 1fr 1fr;gap:1px;
  background:rgba(200,168,75,.10);
  margin-bottom:28px;
  border:1px solid rgba(200,168,75,.10);
}
.alloc-insight-col{
  background:var(--bg);padding:18px 16px 16px;
  display:flex;flex-direction:column;gap:7px;
  position:relative;
}
.alloc-insight-col::before{
  content:'';
  position:absolute;top:0;left:0;right:0;height:2px;
}
.alloc-col-covered::before{background:linear-gradient(90deg,rgba(74,140,110,.6),rgba(74,140,110,.1));}
.alloc-col-missing::before{background:linear-gradient(90deg,rgba(180,80,80,.55),rgba(180,80,80,.1));}
.alloc-col-building::before{background:linear-gradient(90deg,rgba(200,168,75,.6),rgba(200,168,75,.1));}
.alloc-insight-dot{
  width:7px;height:7px;border-radius:50%;flex-shrink:0;
}
.alloc-col-covered .alloc-insight-dot{background:#4a8c6e;box-shadow:0 0 8px rgba(74,140,110,.5)}
.alloc-col-missing .alloc-insight-dot{background:#b45050;box-shadow:0 0 8px rgba(180,80,80,.45)}
.alloc-col-building .alloc-insight-dot{background:#c8a84b;box-shadow:0 0 8px rgba(200,168,75,.45)}
.alloc-insight-label{
  font-size:.65rem;letter-spacing:.22em;text-transform:uppercase;
  font-weight:500;
}
.alloc-col-covered .alloc-insight-label{color:#6aaf90}
.alloc-col-missing .alloc-insight-label{color:#c47878}
.alloc-col-building .alloc-insight-label{color:var(--gold)}
.alloc-insight-desc{
  font-size:.78rem;color:rgba(168,168,160,.68);line-height:1.55;
  margin:0;
}
.alloc-differentiator{
  font-family:'Cormorant Garamond',serif;font-style:italic;
  font-size:clamp(1.1rem,1.6vw,1.3rem);
  color:rgba(200,168,75,.72);
  line-height:1.55;margin-bottom:28px;
  padding-left:14px;
  border-left:2px solid rgba(200,168,75,.25);
}

/* ── Access section (replaces proof strip) ── */
.access-section{padding:72px 64px;max-width:1200px;margin:0 auto}
.access-eyebrow{font-size:.72rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold);display:flex;align-items:center;gap:16px;margin-bottom:20px}
.access-eyebrow::before{content:'';width:28px;height:1px;background:var(--gold)}
.access-headline{font-family:'Cormorant Garamond',serif;font-size:clamp(2.2rem,3.5vw,3.2rem);font-weight:300;line-height:1.12;max-width:640px;margin-bottom:14px}
.access-headline em{font-style:italic;color:var(--gold)}
.access-subline{font-size:1rem;color:var(--muted);max-width:520px;line-height:1.8;margin-bottom:40px}
.access-grid{display:grid;grid-template-columns:1fr 1fr;gap:1px;background:rgba(200,168,75,.08)}
.ac-card{background:var(--bg);padding:40px 40px;position:relative;overflow:hidden;transition:transform .28s cubic-bezier(.23,1,.32,1),box-shadow .28s cubic-bezier(.23,1,.32,1),background .28s;cursor:default}
.ac-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.12),transparent)}
.ac-card::after{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 20% 50%,rgba(200,168,75,.04) 0%,transparent 65%);opacity:0;transition:opacity .35s;pointer-events:none}
.ac-card:hover{transform:translateY(-4px);background:rgba(14,13,10,1);box-shadow:0 24px 64px rgba(0,0,0,.65),0 0 0 1px rgba(200,168,75,.14)}
.ac-card:hover::after{opacity:1}
.ac-label{font-size:.65rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold-dim);margin-bottom:18px;display:block;transition:color .25s}
.ac-card:hover .ac-label{color:var(--gold)}
.ac-head{font-family:'Cormorant Garamond',serif;font-size:clamp(1.55rem,2.2vw,2rem);font-weight:400;line-height:1.15;color:var(--ivory);margin-bottom:14px;letter-spacing:-.01em}
.ac-head em{font-style:italic;color:var(--gold)}
.ac-impact{font-size:.96rem;font-weight:400;color:var(--ivory);opacity:.85;margin-bottom:18px;letter-spacing:.01em;line-height:1.5}
.ac-body{font-size:.88rem;line-height:1.75;color:var(--muted);max-width:420px}

/* ── Platform Expansion section ── */
.expansion{
  border-top:1px solid var(--border);padding:72px 64px;
  position:relative;overflow:hidden;
}
.expansion::before{
  content:'';
  position:absolute;top:0;left:50%;transform:translateX(-50%);
  width:600px;height:400px;pointer-events:none;z-index:0;
  background:radial-gradient(ellipse at 50% 0%,rgba(200,168,75,.04) 0%,transparent 70%);
}
.expansion-inner{max-width:1200px;margin:0 auto;position:relative;z-index:1}
.exp-hed-block{margin-bottom:48px}
.exp-gold-line{
  display:block;
  font-family:'Cormorant Garamond',serif;font-style:italic;font-weight:300;
  font-size:clamp(1rem,1.5vw,1.2rem);color:var(--gold);
  letter-spacing:.02em;margin-top:12px;
}
.exp-grid{
  display:grid;grid-template-columns:repeat(3,1fr);gap:24px;
  margin-bottom:48px;
}
.exp-card{
  padding:40px 36px 44px;
  border:1px solid var(--border);
  background:var(--deep);
  position:relative;overflow:hidden;
  opacity:0;transform:translateY(20px);
  transition:
    opacity .7s cubic-bezier(.23,1,.32,1),
    transform .7s cubic-bezier(.23,1,.32,1),
    border-color .35s,
    box-shadow .35s,
    background .35s;
}
.exp-card.vis{
  opacity:1;transform:none;
}
.exp-card:nth-child(1){transition-delay:.08s}
.exp-card:nth-child(2){transition-delay:.18s}
.exp-card:nth-child(3){transition-delay:.28s}
.exp-card::before{
  content:'';
  position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.15),transparent);
  transition:background .35s;
}
.exp-card::after{
  content:'';
  position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
  width:200px;height:200px;border-radius:50%;pointer-events:none;
  background:radial-gradient(ellipse,rgba(200,168,75,.04) 0%,transparent 70%);
  opacity:0;transition:opacity .5s;
}
.exp-card:hover{
  transform:translateY(-6px);
  border-color:rgba(200,168,75,.28);
  box-shadow:0 12px 48px rgba(0,0,0,.5),0 0 0 1px rgba(200,168,75,.1);
  background:var(--card);
}
.exp-card:hover::before{background:linear-gradient(90deg,transparent,rgba(200,168,75,.3),transparent)}
.exp-card:hover::after{opacity:1}
.exp-dev-tag{
  display:inline-block;
  font-size:.56rem;letter-spacing:.2em;text-transform:uppercase;
  color:rgba(200,168,75,.4);border:1px solid rgba(200,168,75,.18);
  padding:3px 8px;margin-bottom:20px;
}
.exp-title{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.4rem,1.9vw,1.75rem);font-weight:400;
  line-height:1.15;margin-bottom:10px;color:var(--ivory);
  transition:color .3s;
}
.exp-card:hover .exp-title{color:var(--ivory)}
.exp-punch{
  font-size:.98rem;font-weight:400;
  color:rgba(237,232,222,.72);
  line-height:1.5;margin-bottom:12px;
  letter-spacing:.01em;
  transition:color .3s;
}
.exp-card:hover .exp-punch{color:rgba(237,232,222,.9)}
.exp-body{
  font-size:.88rem;line-height:1.7;
  color:var(--muted);opacity:.85;
}
.exp-footer{
  text-align:center;
  padding-top:36px;
  border-top:1px solid rgba(200,168,75,.08);
}
.exp-footer-line{
  display:block;
  font-size:.94rem;color:var(--muted);line-height:1.6;margin-bottom:8px;
}
.exp-footer-accent{
  display:block;
  font-family:'Cormorant Garamond',serif;font-style:italic;
  font-size:clamp(1.05rem,1.6vw,1.2rem);color:var(--gold);
  letter-spacing:.02em;
}
@media(max-width:900px){
  .expansion{padding:48px 24px}
  .exp-hed-block{margin-bottom:36px}
  .exp-grid{grid-template-columns:1fr;gap:16px}
  .exp-card{padding:32px 28px 36px}
}
@media(max-width:520px){
  .expansion{padding:36px 20px}
  .exp-card{padding:28px 22px 32px}
}

/* ── Contact ── */
#contact{background:var(--deep);border-top:1px solid var(--border);padding:72px 64px}
.contact-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 1.2fr;gap:64px;align-items:start}
.c-meta{display:flex;flex-direction:column;gap:20px;margin-top:36px}
.cm label{font-size:.7rem;letter-spacing:.18em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:3px}
.cm span{font-size:.94rem;color:var(--ivory)}
.cform{display:flex;flex-direction:column;gap:12px}
.fg{display:flex;flex-direction:column;gap:6px}
.fg label{font-size:.74rem;letter-spacing:.16em;text-transform:uppercase;color:var(--muted)}
.fg input,.fg textarea,.fg select{
  background:var(--bg);border:1px solid var(--border);color:var(--ivory);
  font-family:'DM Sans',sans-serif;font-size:.94rem;font-weight:300;
  padding:14px 18px;outline:none;transition:border-color .3s;appearance:none;
}
.fg input::placeholder,.fg textarea::placeholder{color:rgba(168,168,160,.28)}
.fg input:focus,.fg textarea:focus,.fg select:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(200,168,75,.08)}
.fg input:focus-visible,.fg textarea:focus-visible,.fg select:focus-visible{outline:none}
.btn-primary:focus-visible,.btn-ghost:focus-visible,.aud-cta:focus-visible,.fsub:focus-visible,.tier-cta:focus-visible,.tier-book:focus-visible,.nav-btn:focus-visible,.gate-cta:focus-visible,.gate-skip:focus-visible,.btt:focus-visible{outline:2px solid var(--gold);outline-offset:3px}
.fg textarea{resize:vertical;min-height:100px}
.fg select option{background:var(--bg);color:var(--ivory)}
.frow{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.fsub{
  background:var(--gold);color:var(--bg);border:none;
  font-family:'DM Sans',sans-serif;font-size:.82rem;font-weight:500;
  letter-spacing:.16em;text-transform:uppercase;
  padding:17px 40px;cursor:pointer;transition:background .3s,transform .2s;
  align-self:flex-start;margin-top:6px;
}
.fsub:hover{background:var(--gold-lt);transform:translateY(-2px)}
.fsub:disabled{opacity:.5;cursor:not-allowed;transform:none}

/* ── Footer ── */
footer{border-top:1px solid var(--border);padding:36px 64px;display:flex;flex-direction:column;align-items:center;gap:16px}
.footer-main{display:flex;align-items:center;justify-content:space-between;width:100%}
.footer-copy{font-size:.7rem;letter-spacing:.1em;color:var(--muted)}
.footer-legal{position:static;display:flex;gap:24px;padding:12px 0 0;border-top:1px solid var(--border);border-bottom:none;width:100%;justify-content:center;z-index:auto}
.footer-legal a{font-size:.66rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.footer-legal a:hover{color:var(--gold)}

/* ── Form feedback ── */
.form-success{background:#142a14;border:1px solid #1e3a1e;color:#8fcf8f;padding:18px 24px;font-size:.9rem;line-height:1.7;margin-bottom:12px}
.form-error{background:#2a1414;border:1px solid #3a1e1e;color:#cf8f8f;padding:14px 18px;font-size:.86rem;line-height:1.6;margin-bottom:10px}
.field-error{font-size:.74rem;color:#cf8f8f;margin-top:4px}

/* ── Back to Top ── */
.btt{position:fixed;bottom:36px;right:36px;z-index:300;width:48px;height:48px;background:var(--gold);color:var(--bg);border:none;cursor:pointer;font-size:1.1rem;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .35s,transform .35s,background .3s;transform:translateY(12px)}
.btt.show{opacity:1;pointer-events:auto;transform:none}
.btt:hover{background:var(--gold-lt);transform:translateY(-2px)}
.btt svg{width:18px;height:18px;fill:currentColor}

/* ── Paywall Gate Overlay ── */
.gate-overlay{display:none;position:fixed;inset:0;z-index:500;background:rgba(8,8,8,.92);backdrop-filter:blur(18px);align-items:center;justify-content:center}
.gate-overlay.active{display:flex}
.gate-box{max-width:540px;width:90%;padding:56px 48px;border:1px solid var(--border);background:var(--card);position:relative;text-align:center}
.gate-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent)}
.gate-icon{font-size:1.6rem;color:var(--gold);margin-bottom:18px}
.gate-title{font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:300;line-height:1.25;margin-bottom:14px;color:var(--ivory)}
.gate-title em{font-style:italic;color:var(--gold)}
.gate-desc{font-size:.94rem;line-height:1.8;color:var(--muted);margin-bottom:28px}
.gate-desc strong{color:var(--ivory);font-weight:400}
.gate-tiers{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:28px}
.gate-tier{padding:20px 16px;border:1px solid var(--border);cursor:pointer;transition:border-color .3s,background .3s}
.gate-tier:hover,.gate-tier.selected{border-color:var(--gold-dim);background:rgba(200,168,75,.04)}
.gate-tier-name{font-family:'Cormorant Garamond',serif;font-size:1.15rem;font-weight:400;color:var(--ivory);margin-bottom:4px}
.gate-tier-price{font-size:.88rem;color:var(--gold)}
.gate-tier-urls{font-size:.76rem;color:var(--muted);margin-top:4px}
.gate-cta{display:inline-block;background:var(--gold);color:var(--bg);font-size:.8rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;padding:16px 42px;border:none;cursor:pointer;transition:background .3s,transform .2s;text-decoration:none}
.gate-cta:hover{background:var(--gold-lt);transform:translateY(-2px)}
.gate-guidance{font-size:.78rem;color:rgba(168,168,160,.45);font-style:italic;margin:-10px 0 22px;line-height:1.6}
.gate-skip{display:block;margin-top:16px;font-size:.76rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);cursor:pointer;border:none;background:none;transition:color .3s}
.gate-skip:hover{color:var(--ivory)}
.gate-badge{display:inline-block;font-size:.62rem;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);border:1px solid var(--gold-dim);padding:4px 10px;margin-bottom:20px}

/* ── Reveal animation (progressive enhancement: visible by default) ── */
.r{opacity:1;transform:none;transition:opacity .72s cubic-bezier(.22,.84,.36,1),transform .72s cubic-bezier(.22,.84,.36,1)}
.js-enabled .r{opacity:0;transform:translateY(22px)}
.js-enabled .r.on{opacity:1;transform:none}

/* ── 1200px: nav-account toggle ── */
@media(max-width:1200px){
  .nav-account-full{display:none}
  .nav-account-short{display:inline}
}

@include('partials.public-nav-mobile-css')

/* ── Mobile ── */
@media(max-width:900px){
  html{font-size:17px;-webkit-text-size-adjust:100%}
  body{-webkit-overflow-scrolling:touch}
  #nav{padding:14px 20px}#nav.stuck{padding:10px 20px}.nav-link{display:none}
  .nav-btn{display:none}
  .nav-btn.nav-book{display:inline-flex;font-size:.68rem;padding:9px 18px;letter-spacing:.1em}
  .nav-account{display:none}
  .nav-hamburger{display:flex}
  #hero{padding:88px 24px 24px;min-height:0}
  .hero-actions{flex-direction:column;gap:12px;width:100%;align-items:center}
  .btn-primary{width:100%;text-align:center;padding:16px 24px}
  .btn-ghost{text-align:center}
  /* Mobile: stronger ambient atmosphere for cinematic first-screen feel */
  .amb-orb-a{background:radial-gradient(ellipse at center,rgba(200,168,75,.11) 0%,transparent 62%)}
  .amb-orb-b{background:radial-gradient(ellipse at center,rgba(200,168,75,.07) 0%,transparent 60%);opacity:1}
  .amb-bloom{background:radial-gradient(ellipse at 42% 46%,rgba(200,168,75,.07) 0%,transparent 65%);opacity:1}
  .amb-shimmer{display:block}
  .hero-scroll{left:20px;bottom:32px}
  .hero-transition{padding:6px 0 0}
  .hero-scroll-arrow{margin:8px auto 0}
  .alloc-section{padding:64px 24px}
  .alloc-layout{grid-template-columns:1fr;gap:48px}
  .alloc-actions{flex-direction:column;gap:16px;width:100%;}
  .alloc-actions .btn-primary{width:100%;text-align:center}
  .statement{grid-template-columns:1fr;gap:32px;padding:48px 24px}
  .stmt-quote{padding:32px 24px}
  .stmt-quote .sq-mark{font-size:2.4rem;margin-bottom:8px}
  .stmt-quote .sq-text{font-size:clamp(1.2rem,4.5vw,1.6rem)}
  .stmt-quote::before,.stmt-quote::after{left:24px;right:24px}
  .stmt-split{grid-template-columns:1fr}
  .url-inner{grid-template-columns:1fr;gap:28px}
  .url-item{font-size:.78rem;padding:10px 12px}
  .offer-intro{grid-template-columns:1fr;gap:28px}
  .contact-inner{grid-template-columns:1fr;gap:36px}
  .audience-grid{grid-template-columns:1fr}
  .aud-card{padding:40px 24px}
  .aud-title{font-size:1.6rem}
  .wyl-grid{grid-template-columns:1fr 1fr;gap:14px}
  .steps-grid{grid-template-columns:1fr}
  .step:not(:last-child)::after{top:auto;bottom:0;left:8%;right:8%;width:84%;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.12) 30%,rgba(200,168,75,.12) 70%,transparent)}
  .wyl-card{padding:32px 26px}
  .wyl-icon{font-size:1.9rem;margin-bottom:18px}
  .step{padding:44px 36px}
  .step-n{font-size:3.4rem;margin-bottom:16px}
  .step-rule{margin-bottom:18px}
  .step-title{font-size:1.3rem}
  .steps-trust{margin-top:18px;padding-top:16px}
  .steps-surface{font-size:.63rem;gap:6px}
  .steps-surface svg{width:17px;height:17px}
  /* exp-grid mobile handled by expansion @media block */
  .url-lock-inner{grid-template-columns:1fr;gap:28px}
  .ul-title{font-size:clamp(1.5rem,5vw,2rem)}
  .ul-lead{font-size:.95rem}
  .ul-states{grid-template-columns:1fr}
  .ul-state{padding:18px 18px}
  .ul-note{padding:14px 16px}
  .access-grid{grid-template-columns:1fr}
  .ac-card{padding:40px 32px}
  .frow{grid-template-columns:1fr}
  .fg input,.fg textarea,.fg select{font-size:16px;padding:14px 16px}
  .fsub{width:100%;text-align:center;padding:16px 24px}
  .s-h{font-size:clamp(1.7rem,6vw,2.4rem)}
  .s-eye{font-size:.7rem;letter-spacing:.2em}
  .s-p{font-size:.96rem}
  .c-meta{gap:16px;margin-top:24px}
  .audience-section,.url-lock,.wyl-section,.url-section{padding:48px 24px}
  .steps-wrap,.licence-stmt-section{padding:40px 24px}
  #offer,#contact,footer{padding:48px 24px}
  .footer-main{flex-direction:column;gap:12px;text-align:center}
  .btt{bottom:20px;right:20px;width:42px;height:42px}
  .gate-box{padding:36px 24px}
  .gate-tiers{grid-template-columns:1fr}
  .gate-title{font-size:1.5rem}
  .gate-desc{font-size:.88rem}
  /* Tighter reveal offset on mobile */
  .js-enabled .r{transform:translateY(20px)}
}
@media(max-width:520px){
  html{font-size:16px}
  #hero{padding:76px 20px 18px;min-height:0}
  .hero-scroll{display:none}
  .alloc-section{padding:48px 20px}
  .alloc-sub{max-width:100%}
  .wyl-icon{font-size:2.2rem;margin-bottom:20px}
  .wyl-card{padding:30px 24px}
  .wyl-title{font-size:1.3rem}
  .proof-icon{font-size:1.8rem;margin-bottom:10px}
  .wyl-grid{grid-template-columns:1fr}
  .step{padding:32px 28px}
  .step-n{font-size:2.8rem}
  .steps-trust{margin-top:14px;padding-top:12px}
  .steps-surface{font-size:.62rem;gap:7px;letter-spacing:.12em}
  .steps-surface svg{width:18px;height:18px}
  .steps-surface-row{column-gap:24px;row-gap:16px}
  .ac-card{padding:32px 24px}
  .stmt-quote{padding:24px 18px}
  .stmt-quote .sq-text{font-size:clamp(1.1rem,4vw,1.4rem)}
  .stmt-quote::before,.stmt-quote::after{left:18px;right:18px}
  .audience-section,.url-lock,.wyl-section,.url-section{padding:36px 20px}
  .steps-wrap,.licence-stmt-section{padding:32px 20px}
  #offer,#contact,footer{padding:36px 20px}
  .aud-card{padding:32px 18px}
  .tier{padding:32px 20px}
  .tier-price{font-size:2.6rem}
  .offer-note{font-size:.88rem}
  .contact-inner{gap:28px}
  .gate-box{padding:28px 18px}
  .logo-seo{font-size:1.28rem}.logo-ai{font-size:1.5rem}.logo-co{font-size:1.1rem}
}

/* ═══════════════════════════════════════════════════════════
   LUXURY REFACTOR — new components
═══════════════════════════════════════════════════════════ */

/* ── Ambient body glow ── */
body::before{
  content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background:radial-gradient(ellipse at 50% 12%,rgba(200,168,75,.025) 0%,transparent 55%);
  animation:ambientShift 20s ease-in-out infinite alternate;
}
@keyframes ambientShift{
  from{transform:translate(0,0) scale(1)}
  to{transform:translate(3%,2%) scale(1.12)}
}

/* ── Hero stage — fixed-height headline container ── */
.hero-stage{
  /* font-size mirrors the h1 so em units here = h1 font size */
  font-size:clamp(4.4rem,9.5vw,8.5rem);
  /* 2-line reserve: handles any headline that wraps at this font size */
  height:calc(2em * 1.18);
  overflow:hidden;            /* hard guard — never allows a 3rd line to bleed */
  position:relative;      /* positioning context for h1 */
  width:100%;             /* fill flex parent so absolute h1 isn't clipped */
  margin-bottom:40px;     /* gap to gold accent */
}
#heroSeq{
  font-family:'Cormorant Garamond',serif;
  font-size:inherit;font-weight:300;line-height:1.18;
  color:var(--ivory);letter-spacing:-.02em;margin:0;
  /* position:absolute removes from flow — animation is visual only */
  position:absolute;top:0;left:0;width:100%;
  opacity:1;transform:none;
  transition:opacity 560ms cubic-bezier(.16,1,.3,1),
             transform 560ms cubic-bezier(.16,1,.3,1);
}
#heroSeq.hs-visible{opacity:1;transform:translateY(0)}
#heroSeq.hs-out{opacity:0;transform:translateY(-8px)}
.hero-gold-accent{
  font-family:'Cormorant Garamond',serif;font-style:italic;font-weight:400;
  font-size:clamp(1.45rem,2.75vw,2.15rem);
  color:var(--gold);letter-spacing:.025em;line-height:1.35;
  opacity:1;
  margin-bottom:36px;
}
.hero-note{
  font-family:'Cormorant Garamond',serif;font-style:italic;font-weight:400;
  font-size:clamp(1.05rem,2vw,1.3rem);
  color:rgba(200,168,75,.72);
  letter-spacing:.015em;line-height:1.5;
  padding-left:14px;
  border-left:2px solid rgba(200,168,75,.32);
  opacity:0;animation:up .8s .54s forwards;
  margin-top:0;margin-bottom:32px;
  max-width:480px;
}

/* ── Hero differentiation line ── */
.hero-diff{
  font-size:.88rem;letter-spacing:.04em;
  color:rgba(200,168,75,.64);line-height:1.74;
  opacity:0;animation:up .8s .44s forwards;
  margin-bottom:28px;max-width:580px;
}
/* Micro-type — elegant 3-line stacked serif moment */
.hero-cities{
  font-family:'Cormorant Garamond',serif;font-weight:300;
  font-size:clamp(1rem,1.55vw,1.28rem);
  color:rgba(237,232,222,.62);letter-spacing:.12em;line-height:1.72;
  opacity:0;animation:up .8s .32s forwards;
  margin-bottom:32px;
  border-left:1px solid rgba(200,168,75,.18);padding-left:18px;
}
.hero-cities span{display:block}
.hero-cta-note{
  font-size:.64rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.30);
  opacity:0;animation:up .8s .68s forwards;
  margin-top:14px;
}

/* ── Hero constellation overlay ── */
.hero-net{
  position:absolute;inset:0;width:100%;height:100%;
  pointer-events:none;z-index:0;opacity:.22;
}
.hero-net-g{
  animation:netDrift 42s ease-in-out infinite alternate;
  transform-origin:600px 280px;
}
@keyframes netDrift{
  from{transform:translate(0,0) scale(1)}
  to{transform:translate(-2.4%,1.6%) scale(1.05)}
}
@media(prefers-reduced-motion:reduce){.hero-net-g{animation:none}}
/* ── Node intelligence pulse — hub intersections ── */
.hero-net-pulse circle{fill:#c8a84b;opacity:0}
.np-1{animation:nodePulse 4.8s ease-in-out 0.4s infinite}
.np-2{animation:nodePulse 4.8s ease-in-out 1.6s infinite}
.np-3{animation:nodePulse 4.8s ease-in-out 2.8s infinite}
.np-4{animation:nodePulse 4.8s ease-in-out 4.0s infinite}
.np-5{animation:nodePulse 4.8s ease-in-out 0.9s infinite}
.np-6{animation:nodePulse 4.8s ease-in-out 3.4s infinite}
@keyframes nodePulse{
  0%,100%{opacity:0}
  38%{opacity:0}
  50%{opacity:.88}
  62%{opacity:0}
}
@media(prefers-reduced-motion:reduce){.hero-net-pulse circle{animation:none}}
/* ── Hero live network canvas ── */
.hero-anim-canvas{
  position:absolute;inset:0;width:100%;height:100%;
  pointer-events:none;z-index:0;display:block;
}

/* ── Hero → Section transition: animated shimmer line ── */
.hero-transition{text-align:center;padding:28px 0 0}
.hero-scroll-label{
  display:block;font-size:.64rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.44);margin-bottom:18px;
  opacity:0;animation:up .9s 1.4s forwards;
}
.hero-rule-shimmer{
  position:relative;overflow:hidden;height:1px;
  background:linear-gradient(to right,transparent,rgba(154,122,48,.28),rgba(200,168,75,.42),rgba(154,122,48,.28),transparent);
}
.hero-rule-shimmer::after{
  content:'';position:absolute;top:0;left:-120%;width:55%;height:100%;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.82),rgba(237,210,130,.6),rgba(200,168,75,.82),transparent);
  animation:shimmerSweep 6.2s ease-in-out 2s infinite;
}
@keyframes shimmerSweep{
  0%{left:-120%;opacity:0}
  8%{opacity:1}
  42%{left:150%;opacity:0}
  100%{left:150%;opacity:0}
}
/* refined descent indicator — double V-chevron */
.hero-scroll-arrow{
  display:flex;flex-direction:column;align-items:center;gap:4px;
  margin:16px auto 0;width:20px;
  animation:chevronFade 3.8s ease-in-out infinite;
}
.hero-scroll-arrow::before,
.hero-scroll-arrow::after{
  content:'';display:block;
  width:9px;height:9px;
  border-right:1px solid rgba(200,168,75,.45);
  border-bottom:1px solid rgba(200,168,75,.45);
  transform:rotate(45deg);
}
.hero-scroll-arrow::after{
  opacity:.40;margin-top:-2px;
}
@keyframes chevronFade{
  0%,100%{opacity:.20;transform:translateY(0)}
  50%{opacity:.54;transform:translateY(5px)}
}

/* ══════════════════════════════════════════════════════════
   DIAGNOSTIC GATEWAY — AI Readiness Intelligence  v2
   ══════════════════════════════════════════════════════════ */

/* ── Section container ── */
#proof{
  padding:72px 64px 0;max-width:1280px;margin:0 auto;
  position:relative;overflow:hidden;text-align:center;
  border-top:none;border-bottom:none;background:transparent;
  scroll-margin-top:64px;
}
/* Signal-field atmosphere */
#proof::before{
  content:'';position:absolute;inset:0;
  background:
    radial-gradient(ellipse 60% 50% at 50% 25%,rgba(200,168,75,.04) 0%,transparent 70%),
    radial-gradient(ellipse 40% 60% at 18% 70%,rgba(200,168,75,.02) 0%,transparent 60%),
    radial-gradient(ellipse 35% 45% at 82% 55%,rgba(200,168,75,.025) 0%,transparent 55%);
  pointer-events:none;z-index:0;
}
/* Faint diagnostic grid texture */
#proof::after{
  content:'';position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(200,168,75,.025) 1px,transparent 1px),
    linear-gradient(90deg,rgba(200,168,75,.025) 1px,transparent 1px);
  background-size:52px 52px;
  mask-image:radial-gradient(ellipse 65% 55% at 50% 35%,black 15%,transparent 75%);
  -webkit-mask-image:radial-gradient(ellipse 65% 55% at 50% 35%,black 15%,transparent 75%);
  pointer-events:none;z-index:0;
}

/* ── Typography ── */
.diag-eyebrow{
  font-size:.58rem;letter-spacing:.32em;text-transform:uppercase;
  color:rgba(200,168,75,.48);margin-bottom:20px;
  position:relative;z-index:1;
}
.diag-hed{
  font-family:'Cormorant Garamond',Georgia,serif;
  font-size:clamp(1.6rem,3.5vw,2.6rem);font-weight:300;
  color:var(--ivory);line-height:1.18;
  margin:0 auto 18px;max-width:640px;
  position:relative;z-index:1;
}
.diag-hed em{color:var(--gold);font-style:italic}
.diag-sub{
  font-size:.88rem;color:rgba(178,176,168,.72);
  margin:0 auto 48px;max-width:500px;line-height:1.76;
  position:relative;z-index:1;
}

/* ── Score Panel — luxury AI diagnostic dashboard ── */
.diag-panel{
  max-width:620px;margin:0 auto 0;
  background:rgba(14,13,9,.88);
  border:1px solid rgba(200,168,75,.1);border-radius:4px;
  overflow:hidden;text-align:left;
  position:relative;z-index:1;
  backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);
  box-shadow:
    inset 0 1px 0 rgba(200,168,75,.06),
    0 20px 72px rgba(0,0,0,.45),
    0 2px 14px rgba(0,0,0,.3);
}
/* Soft ambient glow behind panel */
.diag-panel::before{
  content:'';position:absolute;inset:-48px;
  background:radial-gradient(ellipse 55% 50% at 50% 50%,rgba(200,168,75,.04) 0%,transparent 70%);
  pointer-events:none;z-index:-1;
}
.diag-panel-bar{
  background:rgba(17,16,9,.92);
  border-bottom:1px solid rgba(200,168,75,.07);
  padding:12px 24px;display:flex;align-items:center;gap:12px;
}
.diag-panel-dots{display:flex;gap:6px}
.diag-panel-dot{width:7px;height:7px;border-radius:50%}
.diag-panel-label{
  font-size:.58rem;letter-spacing:.14em;text-transform:uppercase;
  color:rgba(168,168,160,.42);
}
.diag-panel-body{
  padding:30px 30px 26px;display:flex;gap:28px;align-items:flex-start;
}
.diag-score-block{
  flex-shrink:0;text-align:center;
  padding:6px 26px 6px 6px;
  border-right:1px solid rgba(200,168,75,.07);
  min-width:115px;
}
.diag-score-num{
  font-family:'Cormorant Garamond',serif;
  font-size:3.8rem;font-weight:300;color:#6aaf90;
  line-height:1;margin-bottom:4px;letter-spacing:-.02em;
}
.diag-score-denom{
  font-size:.56rem;letter-spacing:.15em;text-transform:uppercase;
  color:rgba(178,176,168,.48);margin-bottom:14px;
}
.diag-score-badge{
  display:inline-block;font-size:.52rem;letter-spacing:.18em;text-transform:uppercase;
  color:rgba(106,175,144,.72);border:1px solid rgba(106,175,144,.15);
  padding:3px 10px;border-radius:2px;
}
.diag-results{flex:1;display:flex;flex-direction:column;gap:11px}
.diag-result-row{
  display:flex;align-items:center;gap:10px;
  font-size:.78rem;color:rgba(195,193,186,.74);line-height:1.4;
}
.diag-result-indicator{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.diag-result-indicator.--pass{background:rgba(106,175,144,.68)}
.diag-result-indicator.--fail{background:rgba(196,120,120,.62)}
.diag-result-indicator.--warn{background:rgba(200,168,75,.58)}
.diag-panel-footer{
  border-top:1px solid rgba(200,168,75,.05);
  padding:13px 30px;display:flex;justify-content:space-between;align-items:center;
}
.diag-panel-footer-left{font-size:.68rem;color:rgba(178,176,168,.48);letter-spacing:.02em}
.diag-panel-footer-right{font-size:.58rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(200,168,75,.44)}

/* ── Connector trace — panel to modules ── */
.diag-connector{
  width:1px;height:48px;
  background:linear-gradient(180deg,rgba(200,168,75,.22),rgba(200,168,75,.06));
  margin:0 auto;position:relative;z-index:1;
}
.diag-connector::after{
  content:'';position:absolute;bottom:-3px;left:-3px;
  width:7px;height:7px;border-radius:50%;
  background:rgba(200,168,75,.2);box-shadow:0 0 10px rgba(200,168,75,.06);
}

/* ── Modules label ── */
.diag-modules-label{
  font-size:.58rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.42);margin:14px auto 18px;
  position:relative;z-index:1;
}

/* ── 5 Signal Modules ── */
.diag-modules{
  display:grid;grid-template-columns:repeat(5,1fr);gap:1px;
  max-width:980px;margin:0 auto 40px;
  background:rgba(200,168,75,.04);
  position:relative;z-index:1;
}
.diag-module{
  background:rgba(8,8,8,.96);padding:26px 22px;text-align:left;
  transition:background .3s,transform .3s;position:relative;
}
.diag-module:hover{background:rgba(14,13,9,.96);transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.3)}
.diag-module:hover .diag-module-num{color:rgba(200,168,75,.5)}
.diag-module-num{
  font-size:.52rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.48);margin-bottom:12px;
}
.diag-module-title{
  font-family:'Cormorant Garamond',serif;
  font-size:1.04rem;font-weight:600;
  color:rgba(237,232,222,.94);margin-bottom:10px;
  line-height:1.28;letter-spacing:.01em;
}
.diag-module-body{
  font-size:.9rem;color:rgba(208,206,198,.76);
  line-height:1.82;font-weight:300;
}

/* ── CTA wrap ── */
.diag-cta-wrap{position:relative;z-index:1;padding:0}
.diag-cta{
  display:inline-block;
  background:linear-gradient(180deg,#d8be72 0%,#c8a84b 100%);
  color:var(--bg);font-size:.68rem;font-weight:600;
  letter-spacing:.16em;text-transform:uppercase;text-decoration:none;
  padding:17px 48px;border:1px solid rgba(226,201,125,.4);border-radius:2px;
  position:relative;overflow:hidden;transition:all .3s;
  box-shadow:0 4px 22px rgba(200,168,75,.08);
}
.diag-cta::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.12),transparent);
  transform:translateX(-100%);transition:transform .6s;
}
.diag-cta:hover{
  background:linear-gradient(180deg,#e0c97e 0%,#d4b45a 100%);
  border-color:rgba(226,201,125,.65);
  box-shadow:0 8px 32px rgba(200,168,75,.18);transform:translateY(-1px);
}
.diag-cta:hover::before{transform:translateX(100%)}
.diag-cta-meta{
  font-size:.62rem;letter-spacing:.14em;text-transform:uppercase;
  color:rgba(195,192,184,.52);margin-top:16px;
  position:relative;z-index:1;
}

/* ── Section bridge — visual handoff into system below ── */
.diag-bridge{
  width:1px;height:56px;margin:32px auto 0;
  background:linear-gradient(180deg,rgba(200,168,75,.18),rgba(200,168,75,.04),rgba(200,168,75,.02));
  position:relative;z-index:1;
}
.diag-bridge::before{
  content:'';position:absolute;top:-4px;left:-4px;
  width:9px;height:9px;border-radius:50%;
  background:rgba(200,168,75,.14);box-shadow:0 0 12px rgba(200,168,75,.06);
}
.diag-bridge::after{
  content:'';position:absolute;bottom:-4px;left:-4px;
  width:9px;height:9px;border-radius:50%;
  background:rgba(200,168,75,.2);
  box-shadow:0 0 18px rgba(200,168,75,.1);
}

/* ── Diagnostic responsive ── */
@media(max-width:900px){
  #proof{padding:40px 40px 0;scroll-margin-top:56px}
  .diag-modules{grid-template-columns:repeat(2,1fr);gap:1px;margin-bottom:40px}
  .diag-module:last-child{grid-column:1 / -1}
  .diag-module{padding:24px 22px}
  .diag-module-title{font-size:1.08rem}
  .diag-module-body{font-size:.92rem;line-height:1.84}
  .diag-cta-meta{font-size:.64rem;letter-spacing:.13em}
  .diag-panel-body{gap:20px;padding:24px 24px 20px}
  .diag-bridge{height:40px;margin-top:24px}
}
@media(max-width:600px){
  #proof{padding:32px 24px 0;scroll-margin-top:48px}
  .diag-modules{grid-template-columns:1fr 1fr;margin-bottom:36px}
  .diag-module{padding:22px 20px}
  .diag-module-title{font-size:1.1rem}
  .diag-module-body{font-size:.94rem;line-height:1.86}
  .diag-modules-label{font-size:.58rem;color:rgba(200,168,75,.42)}
  .diag-panel{max-width:100%}
  .diag-panel-body{flex-direction:column;text-align:center;gap:16px;padding:20px 20px 16px}
  .diag-score-block{border-right:none;border-bottom:1px solid rgba(200,168,75,.07);padding:0 0 16px;min-width:auto}
  .diag-results{align-items:center}
  .diag-hed{max-width:100%}
  .diag-panel-footer{flex-direction:column;gap:6px;text-align:center}
  .diag-bridge{height:32px;margin-top:20px}
}
@media(max-width:400px){
  .diag-modules{grid-template-columns:1fr}
  .diag-module:last-child{grid-column:auto}
  .diag-module-body{font-size:.96rem;line-height:1.88}
}
/* product feature grid */
.feat-grid{padding:40px 24px;text-align:center;max-width:1100px;margin:0 auto}
.feat-grid-eye{font-size:.64rem;letter-spacing:.26em;text-transform:uppercase;color:rgba(200,168,75,.62);margin-bottom:10px}
.feat-grid-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.4rem,2.8vw,2rem);font-weight:300;color:var(--ivory);line-height:1.25;margin:0 auto 24px;max-width:560px}
.feat-grid-hed em{font-style:italic;color:var(--gold)}
.feat-cards{display:grid;grid-template-columns:repeat(2,1fr);gap:1px;background:rgba(200,168,75,.07);max-width:720px;margin:0 auto}
.feat-card{background:#080808;padding:26px 24px;text-align:left;transition:background .25s,transform .25s cubic-bezier(.23,1,.32,1),box-shadow .25s}
.feat-card:hover{background:#0e0d09;transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.35)}
.feat-card:hover .feat-card-icon{opacity:1}
.feat-card-icon{font-size:1.1rem;margin-bottom:12px;opacity:.7;transition:opacity .25s}
.feat-card-title{font-size:.82rem;color:var(--ivory);font-weight:400;margin-bottom:8px;letter-spacing:.02em}
.feat-card-body{font-size:.78rem;color:rgba(178,176,168,.72);line-height:1.68}
.feat-card-score{display:inline-block;font-size:.6rem;letter-spacing:.1em;text-transform:uppercase;color:rgba(200,168,75,.5);border:1px solid rgba(200,168,75,.15);padding:2px 8px;margin-top:10px}
.sys-struct{padding:72px 64px;max-width:1200px;margin:0 auto;position:relative}
.sys-struct-inner{display:grid;grid-template-columns:1fr 1.2fr;gap:68px;align-items:start}
.sys-eyebrow{
  font-size:.68rem;letter-spacing:.28em;text-transform:uppercase;
  color:var(--gold);display:flex;align-items:center;gap:14px;margin-bottom:18px;
}
.sys-eyebrow::before{content:'';width:28px;height:1px;background:var(--gold)}
.sys-hed{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(2rem,3.2vw,3rem);font-weight:300;line-height:1.08;
  color:var(--ivory);margin-bottom:16px;
}
.sys-hed em{font-style:italic;color:var(--gold)}
.sys-sub{
  font-size:clamp(.95rem,1.25vw,1.04rem);line-height:1.78;
  color:rgba(168,168,160,.78);margin-bottom:18px;max-width:480px;
}
.sys-position-note{
  font-size:.88rem;line-height:1.72;
  color:rgba(237,232,222,.52);letter-spacing:.01em;margin-bottom:28px;max-width:460px;
}
/* City coverage cards */
.sys-city-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.sys-city-card{
  background:#040404;border:1px solid rgba(200,168,75,.10);padding:18px 16px;
  position:relative;transition:border-color .22s,box-shadow .22s;
}
.sys-city-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.18),transparent);
  opacity:0;transition:opacity .22s;
}
.sys-city-card:hover{border-color:rgba(200,168,75,.22);box-shadow:0 0 24px rgba(200,168,75,.04)}
.sys-city-card:hover::before{opacity:1}
.sys-city-name{
  font-size:.66rem;letter-spacing:.14em;text-transform:uppercase;
  color:rgba(237,232,222,.38);font-weight:400;margin-bottom:10px;
}
/* URL-tree display */
.sys-domain{
  font-size:.76rem;font-family:'DM Mono','Courier New',monospace;
  color:rgba(237,232,222,.93);letter-spacing:.02em;margin-bottom:9px;
  padding-bottom:7px;border-bottom:1px solid rgba(200,168,75,.14);
  font-weight:500;
}
.sys-url-list{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:5px}
.sys-url-item{
  font-size:.72rem;font-family:'DM Mono','Courier New',monospace;
  color:rgba(168,168,160,.48);line-height:1.5;padding-left:16px;position:relative;
}
.sys-url-item::before{content:'→';position:absolute;left:0;color:rgba(200,168,75,.35);font-family:inherit;font-size:.68rem}
.sys-url-loc{color:rgba(200,168,75,.72);font-style:normal}
.sys-city-foot{font-size:.62rem;letter-spacing:.07em;color:rgba(168,168,160,.22);margin-top:14px;border-top:1px solid rgba(200,168,75,.05);padding-top:10px;text-align:center}
.sys-city-clarity{font-size:.78rem;color:rgba(168,168,160,.46);letter-spacing:.03em;text-align:center;margin-top:18px;line-height:1.65;border-top:1px solid rgba(200,168,75,.07);padding-top:14px;font-style:italic}
/* Left-copy extra elements */
.sys-gold-line{
  display:flex;flex-direction:column;gap:2px;
  margin-bottom:24px;
}
.sys-gold-phrase{
  font-size:.72rem;letter-spacing:.18em;text-transform:uppercase;
  font-family:'DM Sans',sans-serif;font-weight:300;
  color:rgba(200,168,75,.55);
  line-height:1.5;
  opacity:0;
  animation:sysPhraseFade .7s cubic-bezier(.23,1,.32,1) forwards;
}
.sys-gold-phrase:nth-child(1){animation-delay:.1s}
.sys-gold-phrase:nth-child(2){animation-delay:.28s}
.sys-gold-phrase:nth-child(3){
  animation-delay:.48s;
  font-family:'Cormorant Garamond',serif;
  font-size:1.08rem;letter-spacing:.04em;text-transform:none;
  font-style:italic;font-weight:300;
  color:rgba(200,168,75,.82);
  margin-top:4px;
}
@keyframes sysPhraseFade{
  from{opacity:0;transform:translateY(6px)}
  to{opacity:1;transform:none}
}
/* sys-clarity editorial units */
.sys-clarity-block{max-width:460px;}
.sys-cl-unit{
  font-size:.92rem;line-height:1.52;
  color:rgba(237,232,222,.68);
  margin-bottom:20px;
}
.sys-cl-dim{
  font-size:.88rem;line-height:1.52;
  color:rgba(168,168,160,.42);
  margin-bottom:20px;
}
.sys-cl-sub{
  font-size:.62rem;letter-spacing:.22em;text-transform:uppercase;
  font-family:'DM Sans',sans-serif;font-weight:400;
  color:rgba(200,168,75,.52);
  margin-top:8px;
}
.sys-hed-sub{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.1rem,1.8vw,1.38rem);
  font-weight:300;font-style:italic;
  color:rgba(200,168,75,.68);
  line-height:1.5;letter-spacing:-.005em;
  margin-bottom:32px;
}
.sys-aha{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1rem,1.55vw,1.16rem);
  font-weight:300;font-style:italic;
  color:rgba(237,232,222,.6);
  line-height:1.66;
  margin-bottom:32px;
  border-left:2px solid rgba(200,168,75,.22);
  padding-left:14px;
}
.sys-stmt{
  font-size:.92rem;line-height:1.7;
  color:rgba(237,232,222,.72);
  margin-bottom:8px;
}
.sys-stmt-sub{
  font-size:.86rem;line-height:1.78;
  color:rgba(168,168,160,.44);
  margin-bottom:32px;
}
.sys-platform{
  font-size:.6rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.36);
}
.sys-trust{
  font-size:.76rem;letter-spacing:.04em;color:rgba(168,168,160,.38);
  line-height:1.6;display:flex;align-items:flex-start;gap:8px;
}
.sys-trust::before{content:'✓';color:rgba(200,168,75,.40);flex-shrink:0;margin-top:1px}

/* ── Market decision trigger ── */
.alloc-decision{
  margin-top:56px;padding:40px 44px;
  background:rgba(10,9,7,1);
  border:1px solid rgba(200,168,75,.1);
  position:relative;overflow:hidden;max-width:600px;
}
.alloc-decision::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.2),transparent);
}
.alloc-d-main{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.55rem,2.8vw,2.2rem);font-weight:300;
  line-height:1.22;color:var(--ivory);margin-bottom:14px;
}
.alloc-d-main em{color:var(--gold);font-style:italic}
.alloc-d-sub{
  font-size:.82rem;color:var(--muted);margin-bottom:24px;
  letter-spacing:.01em;line-height:1.7;
}
.scarcity-strip{
  margin-top:40px;display:flex;flex-direction:column;gap:9px;
}
.scarcity-line{
  font-size:.7rem;letter-spacing:.18em;text-transform:uppercase;
  color:rgba(200,168,75,.24);padding-left:16px;position:relative;
}
.scarcity-line::before{
  content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);
  width:6px;height:1px;background:rgba(200,168,75,.24);
}

/* ── Infrastructure Principle ── */
.infra-principle{
  position:relative;overflow:hidden;
  padding:140px 64px;text-align:center;
  border-top:1px solid rgba(200,168,75,.12);
  border-bottom:1px solid rgba(200,168,75,.12);
  background:#070706;
}
.infra-canvas{
  position:absolute;inset:0;width:100%;height:100%;
  pointer-events:none;z-index:0;
}
/* dark readability overlay — sits above canvas, below content */
.infra-principle .infra-overlay{
  position:absolute;inset:0;z-index:1;pointer-events:none;
  background:
    radial-gradient(ellipse 100% 100% at 50% 50%,rgba(7,7,6,.82) 0%,rgba(7,7,6,.96) 100%),
    linear-gradient(to bottom,rgba(7,7,6,.92) 0%,rgba(7,7,6,.78) 40%,rgba(7,7,6,.78) 60%,rgba(7,7,6,.92) 100%);
}
/* breathing radial glow */
.infra-principle::before{
  content:'';
  position:absolute;inset:0;
  background:radial-gradient(ellipse 72% 68% at 50% 50%,rgba(200,168,75,.09) 0%,transparent 68%);
  pointer-events:none;
  animation:infraGlow 7s ease-in-out infinite;z-index:1;
}
/* faint grid texture */
.infra-principle::after{
  content:'';
  position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(200,168,75,.015) 1px,transparent 1px),
    linear-gradient(90deg,rgba(200,168,75,.015) 1px,transparent 1px);
  background-size:72px 72px;
  pointer-events:none;z-index:1;
}
@keyframes infraGlow{
  0%,100%{opacity:.74}
  50%{opacity:1}
}
.infra-inner{
  position:relative;z-index:2;
  max-width:1080px;margin:0 auto;
}
@media(min-width:1001px){.infra-hed-1,.infra-hed-2{white-space:nowrap}}
.infra-eyebrow{
  font-size:.64rem;letter-spacing:.38em;text-transform:uppercase;
  color:rgba(200,168,75,.6);margin-bottom:44px;
  display:flex;align-items:center;justify-content:center;gap:20px;
}
.infra-eyebrow::before,.infra-eyebrow::after{
  content:'';width:44px;height:1px;background:rgba(200,168,75,.22);
}
.infra-hed{
  font-family:'Cormorant Garamond',serif;font-weight:200;
  line-height:1.1;margin-bottom:28px;
  display:flex;flex-direction:column;gap:.18em;
}
.infra-hed-1{
  font-size:clamp(3rem,5.8vw,5.6rem);
  color:var(--ivory);letter-spacing:-.018em;font-weight:300;
}
.infra-hed-2{
  font-size:clamp(2.8rem,5.2vw,5.0rem);
  color:rgba(237,232,222,.52);letter-spacing:-.018em;font-style:italic;
}
.infra-sub-copy{
  font-family:'DM Sans',sans-serif;font-weight:300;
  font-size:clamp(.9rem,1.6vw,1.08rem);
  color:rgba(168,168,160,.72);line-height:1.76;letter-spacing:.01em;
  max-width:640px;margin:0 auto 48px;
}
.infra-stmt{
  font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;
  font-size:clamp(1.3rem,2.2vw,1.72rem);
  color:rgba(200,168,75,.72);letter-spacing:.01em;line-height:1.4;
  margin-bottom:28px;
}
.infra-tagline{
  font-size:.66rem;letter-spacing:.28em;text-transform:uppercase;
  color:rgba(237,232,222,.34);margin-bottom:32px;
}
.infra-tenets{
  list-style:none;padding:0;margin:0 auto 36px;
  max-width:440px;text-align:left;
  display:flex;flex-direction:column;gap:10px;
}
.infra-tenet{
  font-family:'DM Sans',sans-serif;font-size:.84rem;font-weight:300;
  line-height:1.6;color:rgba(168,168,160,.56);
  padding-left:20px;position:relative;
}
.infra-tenet::before{content:'\2013';position:absolute;left:0;color:rgba(200,168,75,.38);}
.infra-close{
  font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;
  font-size:clamp(1.1rem,1.8vw,1.36rem);
  color:rgba(237,232,222,.5);margin-bottom:72px;letter-spacing:-.005em;
}
/* ── Infra bridge ── */
.infra-bridge{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:64px;
  text-align:left;
  max-width:760px;
  margin:0 auto 72px;
  padding-top:8px;
  border-top:1px solid rgba(200,168,75,.08);
}
.infra-bridge-left{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.05rem,1.7vw,1.26rem);
  font-weight:300;font-style:italic;
  line-height:1.58;
  color:rgba(237,232,222,.44);
}
.infra-bridge-left strong{
  font-style:normal;font-weight:300;
  color:rgba(237,232,222,.72);
}
.infra-bridge-right{
  display:flex;flex-direction:column;gap:13px;
  justify-content:center;
}
.infra-bridge-item{
  font-family:'DM Sans',sans-serif;
  font-size:.78rem;font-weight:300;
  letter-spacing:.06em;
  color:rgba(168,168,160,.46);
  line-height:1.5;
  padding-left:14px;
  position:relative;
}
.infra-bridge-item::before{
  content:'→';
  position:absolute;left:0;
  color:rgba(200,168,75,.32);
  font-size:.7rem;
}
/* ── Conversion Bridge ── */
.cvb{
  padding:80px 64px;max-width:760px;margin:0 auto;text-align:center;
}
.cvb-lead{
  font-size:.68rem;letter-spacing:.32em;text-transform:uppercase;
  color:rgba(200,168,75,.52);margin-bottom:20px;
}
.cvb-hed{
  font-family:'Cormorant Garamond',serif;font-weight:300;
  font-size:clamp(2rem,3.4vw,3.1rem);line-height:1.12;
  color:var(--ivory);letter-spacing:-.015em;margin-bottom:28px;
}
.cvb-hed em{font-style:italic;color:rgba(200,168,75,.82);}
.cvb-body{
  font-size:.96rem;line-height:1.82;color:rgba(168,168,160,.62);
  max-width:580px;margin:0 auto 44px;
}
.cvb-points{
  list-style:none;padding:0;margin:0 auto 52px;
  max-width:480px;text-align:left;
  display:flex;flex-direction:column;gap:14px;
  border-left:1px solid rgba(200,168,75,.14);
  padding-left:24px;
}
.cvb-point{
  font-family:'DM Sans',sans-serif;font-size:.88rem;font-weight:300;
  line-height:1.6;color:rgba(237,232,222,.58);
}
.cvb-close{
  font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;
  font-size:clamp(1rem,1.7vw,1.28rem);
  color:rgba(237,232,222,.46);margin-bottom:44px;letter-spacing:-.005em;
}
.cvb-actions{
  display:flex;align-items:center;justify-content:center;gap:28px;flex-wrap:wrap;
}
/* ── Trust differentiation block ── */
.trust-diff{
  max-width:680px;margin:0 auto;
  padding:80px 64px;
  text-align:center;
  border-top:1px solid rgba(200,168,75,.07);
  position:relative;
}
.trust-diff::before{
  content:'';
  position:absolute;top:0;left:50%;transform:translateX(-50%);
  width:60px;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.28),transparent);
}
.trust-diff-hed{
  font-family:'Cormorant Garamond',serif;font-weight:300;
  font-size:clamp(1.5rem,2.4vw,2rem);
  color:var(--ivory);letter-spacing:-.01em;line-height:1.28;
  margin-bottom:28px;
}
.trust-diff-body{
  font-size:.9rem;line-height:1.84;
  color:rgba(168,168,160,.52);
  max-width:560px;margin:0 auto 10px;
}
.trust-diff-body strong{
  color:rgba(237,232,222,.72);font-weight:400;
}
.trust-diff-close{
  font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;
  font-size:clamp(1rem,1.6vw,1.22rem);
  color:rgba(200,168,75,.58);
  line-height:1.5;margin-top:24px;
}
.infra-pillars{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:1px;
  background:rgba(200,168,75,.09);
  border:1px solid rgba(200,168,75,.09);
  margin:0 auto 44px;
}
.infra-pillar{
  display:flex;
  flex-direction:column;
  align-items:flex-start;
  justify-content:flex-start;
  gap:12px;
  padding:32px 28px;
  background:rgba(8,8,8,.55);
  transition:background .3s;
}
.infra-pillar:hover{background:rgba(200,168,75,.055);transform:translateY(-1px)}
.infra-pillar svg{
  width:18px;height:18px;
  color:rgba(200,168,75,.62);flex-shrink:0;
  margin-bottom:4px;
  transition:color .28s;
}
.infra-pillar:hover svg{color:rgba(200,168,75,.9)}
.infra-pillar-title{
  font-family:'DM Sans',sans-serif;
  font-size:.72rem;letter-spacing:.13em;text-transform:uppercase;
  font-weight:500;color:rgba(237,232,222,.84);
  line-height:1.3;
}
.infra-pillar-desc{
  font-family:'DM Sans',sans-serif;
  font-size:.8rem;font-weight:300;
  line-height:1.68;
  color:rgba(168,168,160,.6);
}
.infra-gold{
  font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;
  font-size:clamp(2.1rem,3.6vw,3.4rem);
  letter-spacing:.01em;line-height:1.2;margin-bottom:28px;
  /* shimmer pass */
  background:linear-gradient(90deg,var(--gold) 0%,rgba(245,228,152,.98) 45%,var(--gold) 62%,var(--gold) 100%);
  background-size:260% 100%;
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  background-clip:text;
  animation:infraGoldShimmer 9s ease-in-out infinite;
}
@keyframes infraGoldShimmer{
  0%,100%{background-position:120% 0}
  40%,60%{background-position:0% 0}
}
.infra-sub{
  font-size:.72rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(168,168,160,.55);
}
.infra-rule{
  display:block;width:72px;height:1px;margin:44px auto 0;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.3),transparent);
}
/* Ticker */
.infra-ticker{
  overflow:hidden;padding:12px 0;margin-top:40px;position:relative;
  border-top:1px solid rgba(200,168,75,.1);
  border-bottom:1px solid rgba(200,168,75,.1);
}
.infra-ticker::before,.infra-ticker::after{
  content:'';position:absolute;top:0;width:100px;height:100%;z-index:2;pointer-events:none;
}
.infra-ticker::before{left:0;background:linear-gradient(90deg,#080808 20%,transparent);}
.infra-ticker::after{right:0;background:linear-gradient(270deg,#080808 20%,transparent);}
.itk-track{display:flex;width:max-content;animation:infraTicker 28s linear infinite;}
.itk-set{
  white-space:nowrap;flex-shrink:0;
  font-family:'DM Sans',sans-serif;font-size:.68rem;font-weight:300;
  letter-spacing:.3em;text-transform:uppercase;
  color:rgba(200,168,75,.44);padding:0 52px;
}
.itk-dot{color:rgba(200,168,75,.2);margin:0 6px;}
@keyframes infraTicker{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}

/* ── Settlement section ── */
.settlement{
  padding:32px 64px;
  border-top:1px solid rgba(154,122,48,.08);
}
.settlement-inner{max-width:960px;margin:0 auto}
.settle-cards{
  display:grid;grid-template-columns:repeat(3,1fr);gap:12px;
}
.settle-card{
  padding:22px 20px;
  border:1px solid rgba(200,168,75,.08);
  background:rgba(255,255,255,.012);
  position:relative;
  transition:border-color .3s,background .3s;
}
.settle-card:hover{border-color:rgba(200,168,75,.18);background:rgba(200,168,75,.025)}
.settle-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.14),transparent);
}
.settle-card-hed{
  display:block;font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;
  color:rgba(200,168,75,.78);margin-bottom:10px;
}
.settle-card-body{
  font-size:.83rem;line-height:1.75;color:rgba(168,168,160,.78);
}
.pricing-cta-trust{
  font-size:.68rem;letter-spacing:.06em;color:rgba(168,168,160,.46);
  text-align:center;margin-top:6px;
}

/* ── Offer trust block ── */
.offer-trust-line{
  margin-top:28px;padding:18px 22px;
  border:1px solid rgba(200,168,75,.08);
  background:rgba(10,9,7,.6);
}
.offer-trust-main{
  font-size:.86rem;color:var(--ivory);line-height:1.75;opacity:.8;
}
.offer-trust-sub{
  font-family:'Cormorant Garamond',serif;font-style:italic;
  font-size:.94rem;color:rgba(168,168,160,.58);margin-top:6px;display:block;
}

/* ── Offer headline split ── */
.offer-hed-split{display:flex;flex-direction:column;gap:.06em}
.offer-hed-mid{font-size:clamp(1.5rem,2.5vw,2.4rem);color:rgba(237,232,222,.64);font-style:normal;font-weight:300;line-height:1.1}

/* ── Offer panel (right side — 3-chunk) ── */
.offer-panel{display:flex;flex-direction:column;gap:0}

/* A. Scarcity */
.offer-scarcity{padding-bottom:28px;border-bottom:1px solid rgba(200,168,75,.12)}
.offer-scarcity-main{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.25rem,2.2vw,1.65rem);font-weight:300;line-height:1.25;
  color:rgba(237,232,222,.86);letter-spacing:-.01em;margin-bottom:8px;
}
.offer-scarcity-sub{
  font-size:.85rem;color:rgba(168,168,160,.68);line-height:1.6;
}

/* B. Value */
.offer-value{padding:20px 22px;margin:24px 0;background:rgba(9,8,6,.65);border:1px solid rgba(200,168,75,.15)}
.offer-value-price{
  font-size:1.04rem;font-weight:400;
  color:rgba(237,232,222,.9);line-height:1.55;
  display:block;margin-bottom:10px;
}
.offer-value-inline{
  font-size:.82rem;color:rgba(168,168,160,.72);line-height:1.6;display:block;margin-bottom:6px;
}
.offer-value-media{
  font-size:.74rem;color:rgba(168,168,160,.55);line-height:1.5;letter-spacing:.02em;
}

/* C. Final positioning */
.offer-positioning{padding-top:24px}
.offer-positioning-bottom{
  font-size:clamp(1rem,1.6vw,1.2rem);
  font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;
  color:rgba(237,232,222,.72);line-height:1.5;
}

/* ── Ambient network canvas (Offer/Licensing section) ── */
.ambient-network{position:absolute;inset:0;width:100%;height:100%;pointer-events:none}
.ambient-canvas{position:absolute;inset:0;width:100%;height:100%;display:block;pointer-events:none;z-index:0}
.ambient-overlay{
  position:absolute;inset:0;z-index:1;
  background:linear-gradient(to bottom,
    rgba(6,6,6,.80),
    rgba(6,6,6,.50) 28%,
    rgba(6,6,6,.46) 50%,
    rgba(6,6,6,.50) 72%,
    rgba(6,6,6,.80)
  );
}

/* ── FOMO close ── */
.offer-fomo{padding:52px 0 8px;text-align:center;position:relative;z-index:2}
.offer-fomo-line{font-family:'Cormorant Garamond',serif;font-size:clamp(1.3rem,2.4vw,2rem);font-weight:300;font-style:italic;color:rgba(237,232,222,.68);letter-spacing:.02em}

/* ── Expansion momentum ── */
.exp-momentum{
  margin-top:48px;padding-top:40px;
  border-top:1px solid rgba(200,168,75,.07);
  text-align:center;
}
.exp-momentum-main{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.4rem,2.6vw,2.1rem);font-weight:300;
  color:var(--ivory);line-height:1.35;margin-bottom:8px;
}
.exp-momentum-main em{font-style:italic;color:var(--gold)}
.exp-momentum-sub{
  font-size:.7rem;letter-spacing:.2em;text-transform:uppercase;
  color:rgba(168,168,160,.55);
}

/* ── Button press micro-UX ── */
.btn-primary:active{transform:scale(.98)!important;box-shadow:none!important}
.tier-cta:active,.aud-cta:active,.nav-btn:active,.fsub:active,.gate-cta:active{
  transform:scale(.98)!important;
}

@media(max-width:900px){
  .infra-principle{padding:96px 24px}
  .infra-hed-1,.infra-hed-2{font-size:clamp(1.8rem,4.5vw,2.6rem)}
  .infra-pillars{grid-template-columns:1fr 1fr}
  .infra-bridge{grid-template-columns:1fr;gap:28px;max-width:100%}
  .settlement{padding:36px 24px}
  .settle-cards{grid-template-columns:1fr 1fr}
  .alloc-decision{padding:28px 24px;max-width:100%}
  .sys-struct{padding:52px 24px}
  .sys-struct-inner{grid-template-columns:1fr;gap:32px}
  .sys-city-grid{grid-template-columns:1fr 1fr}
  .cvb{padding:80px 32px}
  .trust-diff{padding:60px 32px}
}
@media(max-width:520px){
  .infra-principle{padding:72px 20px}
  .infra-hed-1,.infra-hed-2{font-size:clamp(1.45rem,6.8vw,2.1rem)}
  .infra-gold{font-size:clamp(1.6rem,5.8vw,2.2rem)}
  .infra-sub-copy{font-size:.9rem}
  .infra-pillars{grid-template-columns:1fr}
  .infra-pillar{padding:24px 20px}
  .infra-pillar-desc{font-size:.76rem}
  .settlement{padding:28px 20px}
  .settle-cards{grid-template-columns:1fr}
  .hero-stage{font-size:clamp(2.8rem,9vw,3.8rem)}
  .cvb{padding:64px 24px}
  .trust-diff{padding:48px 24px}
  .cvb-actions{flex-direction:column;gap:20px;align-items:stretch}
  .cvb-actions .btn-primary{text-align:center}
  .cvb-points{padding-left:18px}
  .hero-gold-accent{font-size:clamp(1.1rem,4.5vw,1.4rem)}
  .exp-momentum-main{font-size:clamp(1.2rem,4.5vw,1.6rem)}
  .sys-struct{padding:36px 20px}
}

/* ═══════════════════════════════════════════════════════════
   MOBILE REFINEMENT PASS
   Scope: max-width:768px (phone-first, 375–430px primary)
   Rule: zero desktop impact — all selectors inside media queries
═══════════════════════════════════════════════════════════ */

/* ── 1. Typography ── */
@media(max-width:768px){
  /* Body copy — lift to ≥16px at 17px root */
  .wyl-desc{font-size:.98rem;line-height:1.78;color:rgba(168,168,160,.92)}
  .step-desc{font-size:.98rem;line-height:1.76;color:rgba(168,168,160,.92)}
  .tier-features li{font-size:.95rem;line-height:1.74}
  .tier-commitment{font-size:.88rem;line-height:1.84}
  .tier-urls{font-size:.88rem;line-height:1.72}
  .ul-body{font-size:1rem;line-height:1.78}
  .ul-state-desc{font-size:.92rem;line-height:1.78}
  .pos-support{font-size:1rem;line-height:1.76}
  .pos-proof li{font-size:1rem;line-height:1.74}
  .alloc-reinforce span{font-size:1rem;line-height:1.78}
  .alloc-sub p{font-size:1.02rem;line-height:1.84}
  .offer-note{font-size:.98rem;line-height:1.86}
  .alloc-avail-note{font-size:.93rem;line-height:1.88}
  .exp-body{font-size:.96rem;line-height:1.78}
  .ac-body{font-size:.96rem;line-height:1.78}
  .stmt-body p{font-size:1rem;line-height:1.82}

  /* Small uppercase labels — reduce letter-spacing */
  .s-eye{letter-spacing:.18em;font-size:.72rem}
  .tier-flag{letter-spacing:.16em}
  .alloc-eyebrow{letter-spacing:.2em;font-size:.66rem}
  .access-eyebrow{letter-spacing:.2em;font-size:.68rem}
  .infra-eyebrow{letter-spacing:.24em;font-size:.6rem}
  .alloc-convert-label{letter-spacing:.16em;font-size:.75rem}
  .alloc-panel-label{letter-spacing:.18em;font-size:.75rem}

  /* Display headings — tighter so wrapping is intentional, not accidental */
  .pos-h2{line-height:1.07;font-size:clamp(1.8rem,5vw,2.4rem)}
  .alloc-hed{line-height:1.11;font-size:clamp(1.8rem,5vw,2.4rem)}
  .access-headline{line-height:1.13;font-size:clamp(1.7rem,5vw,2.2rem)}
  .wyl-title{font-size:1.32rem}
  .step-title{font-size:1.22rem}

  /* Hero zone structure: Z1=headline+accent, Z2=actions, Z3=transition */
  .hero-stage{font-size:clamp(2.8rem,7.5vw,4rem);margin-bottom:12px;height:calc(2em * 1.18)}
  .hero-gold-accent{margin-bottom:30px;line-height:1.3;font-size:clamp(1.15rem,3.8vw,1.6rem)}
  .hero-transition{padding:6px 0 0}
  .hero-scroll-arrow{margin:8px auto 0}
  .hero-note{font-size:clamp(1rem,4vw,1.2rem);margin-bottom:22px;padding-left:12px;max-width:100%}
  .hero-diff{font-size:.82rem;margin-bottom:20px}
  .hero-cities{font-size:.96rem;margin-bottom:24px;letter-spacing:.09em;padding-left:14px}
  .hero-cta-note{font-size:.64rem;letter-spacing:.12em}
  .hero-net{opacity:.11}
  .hero-scroll-label{font-size:.60rem;letter-spacing:.16em;margin-bottom:8px}
  #proof{padding:48px 24px 0}
}

/* ── 2. Section spacing ── */
@media(max-width:768px){
  .wyl-card{padding:32px 22px}
  .step{padding:32px 22px}
  .ac-card{padding:36px 26px}
  .stmt-split-card{padding:18px 20px}
  .stmt-split-card .split-body{font-size:.91rem;line-height:1.76}
  .aud-list{gap:18px}
  .aud-list li{line-height:1.76;padding-left:20px}
  .alloc-cell{padding:22px 18px}
  .licence-stmt-principle{font-size:clamp(1.08rem,3.8vw,1.28rem)}
  .licence-stmt-body p{font-size:.9rem;letter-spacing:.03em;line-height:1.88}
  #preview{padding:56px 22px}
  #preview p{font-size:.93rem}
}

/* ── 3. CTA hierarchy ── */
@media(max-width:768px){
  /* Primary — dominant, inevitable center of gravity */
  .btn-primary{
    min-height:54px;padding:16px 28px;font-size:.82rem;
    display:flex;align-items:center;justify-content:center;
    box-shadow:0 4px 28px rgba(200,168,75,.18);
  }
  /* Secondary — quieter, a clear secondary path downward */
  .btn-ghost{opacity:.68;font-size:.76rem;letter-spacing:.12em;padding-bottom:5px;border-bottom-color:rgba(200,168,75,.28)}
  .hero-actions{gap:12px}
  .alloc-actions{gap:22px}
  /* Audience CTAs — full-width on card */
  .aud-cta{
    display:flex;align-items:center;justify-content:center;
    width:100%;text-align:center;padding:17px 24px;min-height:54px;
  }
  /* Tier CTAs */
  .tier-cta{
    padding:17px 16px;min-height:52px;font-size:.78rem;
    display:flex;align-items:center;justify-content:center;
  }
  .tier-book{padding:15px 16px;min-height:48px;font-size:.74rem}
}

/* ── 4. Sticky mobile CTA bar ── */
.mob-sticky-cta{display:none} /* hidden everywhere by default */
@media(max-width:768px){
  .mob-sticky-cta{
    display:block;
    position:fixed;bottom:0;left:0;right:0;
    z-index:250;
    background:rgba(8,8,8,.97);
    backdrop-filter:blur(18px);-webkit-backdrop-filter:blur(18px);
    border-top:1px solid rgba(200,168,75,.18);
    padding:12px 20px calc(12px + env(safe-area-inset-bottom,0px));
    transform:translateY(100%);
    transition:transform .34s cubic-bezier(.23,1,.32,1);
    will-change:transform;
  }
  .mob-sticky-cta.msc-visible{transform:translateY(0)}
  .mob-sticky-cta.msc-hidden{transform:translateY(100%)}
  .msc-inner{
    display:flex;align-items:center;gap:14px;
    max-width:480px;margin:0 auto;
  }
  .msc-primary{
    flex:1;display:flex;align-items:center;justify-content:center;
    background:var(--gold);color:var(--bg);
    font-family:'DM Sans',sans-serif;
    font-size:.79rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;
    text-decoration:none;padding:16px 18px;min-height:52px;
    transition:background .25s;white-space:nowrap;
  }
  .msc-primary:hover,.msc-primary:active{background:var(--gold-lt)}
  .msc-primary:focus-visible{outline:2px solid var(--gold);outline-offset:3px}
  .msc-secondary{
    font-family:'DM Sans',sans-serif;
    font-size:.68rem;letter-spacing:.12em;text-transform:uppercase;
    color:rgba(200,168,75,.6);text-decoration:none;
    padding:8px 2px;white-space:nowrap;flex-shrink:0;
    transition:color .25s;
  }
  .msc-secondary:hover{color:var(--gold)}
  .msc-secondary:focus-visible{outline:2px solid var(--gold);outline-offset:3px}
  /* Nudge BTT above bar */
  body.msc-active .btt{bottom:88px}
}

/* ── 5. Pricing card improvements ── */
@media(max-width:768px){
  .tier-price{margin-bottom:6px;line-height:1}
  .tier-commitment{margin-bottom:22px}
  .tier-features{gap:17px;margin-bottom:26px}
  .tier-features li svg{width:15px;height:15px;margin-top:4px;flex-shrink:0}
  .tier-gated{padding:14px 14px;font-size:.82rem;line-height:1.72}
  .offer-scarcity-main{font-size:clamp(1.12rem,4vw,1.48rem)}
  .offer-value{padding:18px 20px;margin:18px 0}
  .offer-value-price{font-size:.98rem}
  .offer-value-inline{font-size:.8rem}
  .offer-positioning-bottom{font-size:clamp(.95rem,3.5vw,1.08rem)}
}

/* ── 6. Feature/bullet row cleanup ── */
@media(max-width:768px){
  .tier-features li{gap:13px;padding-bottom:1px}
  .pos-proof{gap:13px}
  .pos-proof li{padding-left:16px;font-size:.96rem;line-height:1.7}
}

/* ── 7. Territory status grid ── */
@media(max-width:768px){
  .alloc-grid{grid-template-columns:1fr}
  .alloc-region{font-size:1.08rem}
  .alloc-states{font-size:.8rem;margin-bottom:10px}
  .alloc-status-label{font-size:.72rem;letter-spacing:.12em}
  .alloc-legend{gap:16px;padding:14px 18px;flex-wrap:wrap}
  .alloc-legend-label{font-size:.72rem;letter-spacing:.12em}
  .alloc-dot{width:7px;height:7px}
}

/* ── 8. Back-to-top safe placement ── */
@media(max-width:768px){
  /* Always above sticky CTA bar */
  .btt{bottom:84px;right:18px;width:42px;height:42px}
}

/* ── 9. Mobile nav refinements ── */
/* Book button hidden on mobile — panel is the sole navigation path */

/* ── 10. Contact form — mobile refinements ── */
@media(max-width:768px){
  /* Field group: tighter inner gap (label→field) */
  .fg{gap:5px}

  /* Form vertical rhythm: slightly tighter between fields */
  .cform{gap:10px}

  /* Fields: ~52px touch height, readable font, consistent padding */
  .fg input,.fg textarea,.fg select{
    font-size:16px;            /* prevent iOS zoom */
    padding:17px 16px;         /* 17+17 + ~18px line-height ≈ 52px */
    min-height:52px;
    line-height:1.2;
  }

  /* Textarea keeps its own min-height but obeys field rhythm */
  .fg textarea{min-height:110px;padding:15px 16px}

  /* Labels — lift brightness slightly, reduce letter-spacing for readability */
  .fg label{
    font-size:.76rem;
    letter-spacing:.12em;
    color:rgba(168,168,160,.78);  /* brighter than default var(--muted) .62 visual weight */
  }

  /* Placeholders — lift from .28 opacity to .42 for legibility on dark bg */
  .fg input::placeholder,.fg textarea::placeholder{
    color:rgba(168,168,160,.42);
  }

  /* Select — ensure arrow area is not clipped */
  .fg select{padding-right:36px}

  /* Helper / error text */
  .field-error{font-size:.76rem;letter-spacing:.01em;margin-top:5px}

  /* Form note above submit */
  .cform > p[style]{
    font-size:.84rem !important;
    color:rgba(168,168,160,.68) !important;
    line-height:1.7;
    text-align:left;
  }

  /* Submit — full-width, clearly dominant, 56px min */
  .fsub{
    width:100%;
    text-align:center;
    padding:19px 24px;
    min-height:56px;
    font-size:.84rem;
    letter-spacing:.16em;
    margin-top:10px;
    align-self:stretch;
  }

  /* 2-col frow already collapses to 1-col at ≤900px; tighten gap */
  .frow{gap:10px}

  /* Meta list in contact left col */
  .c-meta{gap:14px;margin-top:20px}
  .cm label{font-size:.68rem;letter-spacing:.14em;margin-bottom:2px}
  .cm span{font-size:.92rem;line-height:1.6}
}

/* ── 11. Section spacing + breathing room ── */
@media(max-width:768px){
  /* More space between major sections */
  .gold-rule{margin:12px 0}
  .audience-section,.url-lock,.wyl-section,.url-section{padding:56px 24px}
  .steps-wrap,.licence-stmt-section{padding:48px 24px}
  #offer,#contact{padding:56px 24px}
  /* Section headings — bigger + more breathing room */
  .s-h{margin-bottom:24px}
  .s-p{font-size:1rem;line-height:1.82}
  .s-eye{margin-bottom:18px}
  /* Diagnostic section — bigger text */
  .diag-hed{font-size:clamp(1.6rem,5vw,2.2rem)}
  .diag-sub{font-size:.95rem;line-height:1.78}
  .diag-eyebrow{font-size:.68rem;letter-spacing:.2em;margin-bottom:16px}
  .diag-score-num{font-size:2.8rem}
  .diag-score-denom{font-size:.82rem}
  .diag-result-row{font-size:.9rem;line-height:1.72;padding:6px 0}
  .diag-panel-footer{font-size:.82rem}
  .diag-cta-meta{font-size:.68rem}
  /* Feature cards — bigger text */
  .feat-card-title{font-size:.9rem}
  .feat-card-body{font-size:.84rem;line-height:1.74}
  /* ACE section — bigger text */
  .ace-block-title{font-size:.96rem;margin-bottom:10px}
  .ace-block-desc{font-size:.86rem;line-height:1.74}
  .ace-sub{font-size:1rem}
  /* System structure section */
  .sys-hed{font-size:clamp(1.8rem,5vw,2.4rem)}
  .sys-sub{font-size:1rem;line-height:1.8}
  .sys-position-note{font-size:.92rem;line-height:1.76}
  .sys-domain{font-size:.82rem}
  .sys-url-item{font-size:.78rem}
  .sys-city-name{font-size:.72rem}
  /* Infrastructure section */
  .infra-sub-copy{font-size:.95rem;line-height:1.8}
  .infra-pillar-title{font-size:1.06rem}
  .infra-pillar-desc{font-size:.82rem;line-height:1.76}
  /* Settlement section */
  .settle-hed{font-size:clamp(1.4rem,4.5vw,1.9rem)}
  .settle-card-title{font-size:1rem}
  .settle-card-body{font-size:.88rem;line-height:1.76}
  /* Expansion section */
  .exp-hed{font-size:clamp(1.6rem,5vw,2.2rem)}
  .exp-body{font-size:.96rem;line-height:1.78}
  /* Contact form section — bigger labels + copy */
  .contact-hed{font-size:clamp(1.6rem,5vw,2rem)}
  /* CVB / trust-diff — bigger copy */
  .cvb-hed{font-size:clamp(1.6rem,5vw,2.2rem)}
  .cvb-body{font-size:.95rem;line-height:1.78}
  .cvb-point{font-size:.95rem;line-height:1.72}
  .trust-hed{font-size:clamp(1.5rem,5vw,2rem)}
  .trust-body{font-size:.94rem;line-height:1.78}
  /* Crypto section */
  .crypto-lines{font-size:.95rem;line-height:1.76}
  .crypto-emphasis{font-size:1.1rem}
  .crypto-sub{font-size:.86rem}
}

/* ── 12. Hero — very small phones (≤390px): zone model preserved ── */
@media(max-width:390px){
  /* Z1: headline — tighter internal bond */
  .hero-stage{font-size:clamp(2.4rem,8.5vw,3rem);margin-bottom:8px}
  /* Z1→Z2: zone break maintained at small scale */
  .hero-gold-accent{font-size:clamp(1rem,4.2vw,1.3rem);margin-bottom:24px}
  /* Container — minimal base padding */
  #hero{padding:72px 18px 16px;min-height:0}
  /* Z3: transition — compact descent */
  .hero-transition{padding:4px 0 0}
  .hero-scroll-arrow{margin:4px auto 0}
  /* CTA — compact but dominant */
  .btn-primary{min-height:50px;padding:14px 24px;font-size:.8rem}
  .btn-ghost{margin-top:2px}
}
/* ── AI Citation Engine section ── */
.ace-section{padding:96px 64px;max-width:1200px;margin:0 auto}
.ace-inner{max-width:960px;margin:0 auto}
.ace-eyebrow{font-size:.68rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.7);margin-bottom:14px}
.ace-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(2.2rem,4vw,3.2rem);font-weight:300;color:rgba(237,232,222,.97);margin-bottom:18px;line-height:1.12}
.ace-hed em{font-style:italic;color:#e2c97d}
.ace-sub{font-size:.97rem;color:rgba(168,168,160,.75);max-width:600px;line-height:1.8;margin-bottom:52px}
.ace-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:2px;margin-bottom:40px}
.ace-block{background:#0e0d09;border:1px solid rgba(200,168,75,.08);padding:26px 24px;transition:border-color .2s,background .2s}
.ace-block:hover{border-color:rgba(200,168,75,.18);background:rgba(200,168,75,.03)}
.ace-block-num{font-size:.6rem;letter-spacing:.18em;color:rgba(200,168,75,.55);margin-bottom:10px}
.ace-block-title{font-size:.9rem;color:rgba(237,232,222,.92);font-weight:400;margin-bottom:8px;line-height:1.3}
.ace-block-desc{font-size:.8rem;color:rgba(168,168,160,.65);line-height:1.68}
.ace-cta{text-align:center;margin-top:8px}
.ace-cta a{font-size:.82rem;color:rgba(200,168,75,.8);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.25);padding-bottom:2px;letter-spacing:.04em;transition:color .2s,border-color .2s}
.ace-cta a:hover{color:#e2c97d;border-color:rgba(226,201,125,.6)}
@media(max-width:900px){.ace-section{padding:72px 40px}.ace-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:600px){.ace-section{padding:56px 24px}.ace-grid{grid-template-columns:1fr 1fr}.feat-cards{grid-template-columns:1fr}}
@media(max-width:400px){.ace-grid{grid-template-columns:1fr}}
</style>
@if(config('services.recaptcha.site_key'))
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>
@endif
@include('partials.clarity')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "https://seoaico.com/#org",
      "name": "SEO AI Co",
      "legalName": "SEO AI Co™",
      "url": "https://seoaico.com",
      "logo": "https://seoaico.com/favicon.svg",
      "description": "SEO AI Co™ operates the AI Citation Engine™ — structuring web content for extraction and citation by AI systems including Google AI Overviews, ChatGPT, and Perplexity. Built for local service businesses.",
      "sameAs": [],
      "contactPoint": {
        "@type": "ContactPoint",
        "email": "hello@seoaico.com",
        "contactType": "customer support"
      }
    },
    {
      "@type": "WebSite",
      "@id": "https://seoaico.com/#website",
      "url": "https://seoaico.com",
      "name": "SEO AI Co™",
      "publisher": { "@id": "https://seoaico.com/#org" }
    },
    {
      "@type": "Service",
      "@id": "https://seoaico.com/#service",
      "name": "AI Citation Engine™",
      "serviceType": "AI Citation & Search Visibility",
      "provider": { "@id": "https://seoaico.com/#org" },
      "description": "The AI Citation Engine™ deploys structured pages, schema, and entity systems that make local service businesses the source AI systems cite across Google AI Overviews, ChatGPT, and Perplexity.",
      "areaServed": { "@type": "Country", "name": "United States" },
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "SEO AI Co™ Access Tiers",
        "itemListElement": [
          {
            "@type": "Offer",
            "name": "Market Opportunity Snapshot",
            "description": "Free market opportunity session — see where you are missing visibility and where competitors are winning.",
            "price": "0",
            "priceCurrency": "USD",
            "url": "https://seoaico.com/book"
          },
          {
            "@type": "Offer",
            "name": "Market Expansion Blueprint",
            "description": "75-minute session mapping full-market visibility expansion across every service and city.",
            "price": "500",
            "priceCurrency": "USD",
            "url": "https://seoaico.com/book"
          },
          {
            "@type": "Offer",
            "name": "Market Growth Plan",
            "description": "60-minute session to identify clear, actionable next steps for your market growth.",
            "price": "250",
            "priceCurrency": "USD",
            "url": "https://seoaico.com/book"
          }
        ]
      }
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "What is programmatic SEO?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Programmatic SEO is the practice of building and deploying structured, hyper-local web pages at scale — one page per service-city combination — so your business appears in searches across your entire market."
          }
        },
        {
          "@type": "Question",
          "name": "What is hyper-local SEO?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Hyper-local SEO targets searches for specific services in specific cities or neighborhoods. SEO AI Co™ deploys one optimized page per service-location pair, covering every area you serve."
          }
        },
        {
          "@type": "Question",
          "name": "How does AI SEO work?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "AI SEO uses machine learning and structured data to generate, deploy, and continuously optimize content pages for local search — faster and at greater scale than manual methods."
          }
        }
      ]
    }
  ]
}
</script>
@endverbatim
</head>
<body>

<!-- ════════════ AMBIENT ATMOSPHERIC LAYERS ════════════ -->
<div class="amb-wrap amb-wrap-a" aria-hidden="true"><div class="amb-orb-a"></div></div>
<div class="amb-wrap amb-wrap-b" aria-hidden="true"><div class="amb-orb-b"></div></div>
<div class="amb-bloom" aria-hidden="true"></div>
<div class="amb-shimmer" aria-hidden="true"></div>

<!-- ════════════ NAV ════════════ -->
@include('partials.public-nav', ['showHamburger' => true])

<!-- ════════════ HERO ════════════ -->
<section id="hero">
  <div class="hero-grid"></div>

  {{-- Constellation network overlay --}}
  <svg class="hero-net" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 560" aria-hidden="true" focusable="false" fill="none" preserveAspectRatio="xMidYMid slice">
    <g class="hero-net-g" stroke="#c8a84b" fill="#c8a84b">
      <line x1="140" y1="90" x2="340" y2="210" stroke-opacity=".55" stroke-width=".8"/>
      <line x1="340" y1="210" x2="540" y2="150" stroke-opacity=".50" stroke-width=".7"/>
      <line x1="540" y1="150" x2="700" y2="280" stroke-opacity=".46" stroke-width=".7"/>
      <line x1="700" y1="280" x2="920" y2="195" stroke-opacity=".44" stroke-width=".65"/>
      <line x1="920" y1="195" x2="1090" y2="340" stroke-opacity=".38" stroke-width=".6"/>
      <line x1="340" y1="210" x2="460" y2="400" stroke-opacity=".36" stroke-width=".55"/>
      <line x1="460" y1="400" x2="700" y2="280" stroke-opacity=".36" stroke-width=".55"/>
      <line x1="700" y1="280" x2="800" y2="460" stroke-opacity=".30" stroke-width=".45"/>
      <line x1="140" y1="90" x2="260" y2="340" stroke-opacity=".30" stroke-width=".45"/>
      <line x1="260" y1="340" x2="460" y2="400" stroke-opacity=".30" stroke-width=".45"/>
      <line x1="920" y1="195" x2="1000" y2="90" stroke-opacity=".34" stroke-width=".5"/>
      <line x1="800" y1="460" x2="1090" y2="340" stroke-opacity=".26" stroke-width=".38"/>
      <line x1="540" y1="150" x2="580" y2="45" stroke-opacity=".34" stroke-width=".5"/>
      <line x1="1090" y1="340" x2="1160" y2="200" stroke-opacity=".22" stroke-width=".35"/>
      <line x1="580" y1="45" x2="1000" y2="90" stroke-opacity=".28" stroke-width=".4"/>
      <line x1="460" y1="400" x2="1090" y2="340" stroke-opacity=".24" stroke-width=".36"/>
      <line x1="260" y1="340" x2="700" y2="280" stroke-opacity=".26" stroke-width=".38"/>
      <circle cx="140" cy="90" r="2" fill-opacity=".55"/>
      <circle cx="340" cy="210" r="2.5" fill-opacity=".70"/>
      <circle cx="540" cy="150" r="2" fill-opacity=".55"/>
      <circle cx="700" cy="280" r="3" fill-opacity=".80"/>
      <circle cx="920" cy="195" r="2" fill-opacity=".55"/>
      <circle cx="1090" cy="340" r="1.8" fill-opacity=".48"/>
      <circle cx="460" cy="400" r="1.8" fill-opacity=".48"/>
      <circle cx="260" cy="340" r="1.5" fill-opacity=".40"/>
      <circle cx="800" cy="460" r="1.5" fill-opacity=".40"/>
      <circle cx="1000" cy="90" r="1.8" fill-opacity=".48"/>
      <circle cx="580" cy="45" r="1.5" fill-opacity=".42"/>
      <circle cx="1160" cy="200" r="1.2" fill-opacity=".34"/>
    </g>
    {{-- Intelligence pulse — hub intersection nodes --}}
    <g class="hero-net-pulse" fill="#c8a84b">
      <circle class="np-1" cx="340" cy="210" r="5.5"/>
      <circle class="np-2" cx="700" cy="280" r="7"/>
      <circle class="np-3" cx="460" cy="400" r="5"/>
      <circle class="np-4" cx="920" cy="195" r="4.5"/>
      <circle class="np-5" cx="540" cy="150" r="4.8"/>
      <circle class="np-6" cx="1000" cy="90" r="4"/>
    </g>
  </svg>
  <canvas class="hero-anim-canvas" id="heroAnimCanvas" aria-hidden="true"></canvas>

  <div class="hero-stage">
    <h1 id="heroSeq" aria-label="Will AI Cite Your Website? Instant AI citation readiness score in 60 seconds.">Will AI Cite<br>Your Website?</h1>
  </div>
  <p class="hero-gold-accent">Instant AI citation readiness score in 60 seconds.</p>
  <div class="hero-actions">
    <a href="{{ route('scan.start') }}" class="btn-primary">Start Your Scan — $2</a>
    <a href="#proof" class="btn-ghost">See an Example</a>
  </div>

</section>

<div class="hero-transition">
  <div class="hero-rule-shimmer"></div>
  <div class="hero-scroll-arrow"></div>
</div>

<!-- ════════════ PHASE 2 — DIAGNOSTIC GATEWAY ════════════ -->
<section id="proof" aria-label="AI Citation Diagnostic">
  <p class="diag-eyebrow">Readiness Intelligence</p>
  <h2 class="diag-hed">See exactly where you stand&nbsp;&mdash; <em>in seconds</em></h2>
  <p class="diag-sub">Enter your URL. Receive a 0&ndash;100 AI citation readiness score, your top structural gaps, and the fastest correction path&nbsp;&mdash; for $2.</p>

  <!-- Diagnostic score panel -->
  <div class="diag-panel">
    <div class="diag-panel-bar">
      <div class="diag-panel-dots">
        <div class="diag-panel-dot" style="background:#c47878;opacity:.45"></div>
        <div class="diag-panel-dot" style="background:#c8a84b;opacity:.3"></div>
        <div class="diag-panel-dot" style="background:#6aaf90;opacity:.38"></div>
      </div>
      <span class="diag-panel-label">yourbusiness.com &middot; AI Citation Analysis</span>
    </div>
    <div class="diag-panel-body">
      <div class="diag-score-block">
        <div class="diag-score-num">72</div>
        <div class="diag-score-denom">/ 100 Citation Score</div>
        <div class="diag-score-badge">Above Baseline</div>
      </div>
      <div class="diag-results">
        <div class="diag-result-row"><span class="diag-result-indicator --pass"></span> Structured data signals detected</div>
        <div class="diag-result-row"><span class="diag-result-indicator --fail"></span> Answerable content gaps found</div>
        <div class="diag-result-row"><span class="diag-result-indicator --pass"></span> Entity authority present</div>
        <div class="diag-result-row"><span class="diag-result-indicator --warn"></span> Content connectivity below threshold</div>
        <div class="diag-result-row"><span class="diag-result-indicator --pass"></span> Authority depth sufficient</div>
      </div>
    </div>
    <div class="diag-panel-footer">
      <span class="diag-panel-footer-left">Fastest correction identified&nbsp;&mdash; estimated <strong style="color:rgba(106,175,144,.7)">+20 pts</strong></span>
      <span class="diag-panel-footer-right">Full report &rarr;</span>
    </div>
  </div>

  <div class="diag-connector" aria-hidden="true"></div>
  <p class="diag-modules-label">Evaluation Dimensions</p>

  <!-- 5 Signal Modules -->
  <div class="diag-modules">
    <div class="diag-module">
      <p class="diag-module-num">01</p>
      <p class="diag-module-title">Machine-Readable Context</p>
      <p class="diag-module-body">Structured data markers and schema signals that let AI systems parse your business identity.</p>
    </div>
    <div class="diag-module">
      <p class="diag-module-num">02</p>
      <p class="diag-module-title">Direct Answer Signals</p>
      <p class="diag-module-body">Citable, extractable answers positioned where AI retrieval systems look first.</p>
    </div>
    <div class="diag-module">
      <p class="diag-module-num">03</p>
      <p class="diag-module-title">Definitions &amp; Explanations</p>
      <p class="diag-module-body">Clear entity identity and contextual definitions AI systems can trust and reference.</p>
    </div>
    <div class="diag-module">
      <p class="diag-module-num">04</p>
      <p class="diag-module-title">Content Connectivity</p>
      <p class="diag-module-body">Internal linking architecture that reinforces topical authority across your domain.</p>
    </div>
    <div class="diag-module">
      <p class="diag-module-num">05</p>
      <p class="diag-module-title">Authority Depth</p>
      <p class="diag-module-body">Structural depth sufficient for AI systems to confidently cite you as the definitive answer.</p>
    </div>
  </div>

  <div class="diag-cta-wrap">
    <a href="{{ route('scan.start') }}" class="diag-cta">Run Your Diagnostic&nbsp;&mdash; $2</a>
    <p class="diag-cta-meta">Results in seconds &middot; Sent to your inbox &middot; No account required</p>
  </div>

  <div class="diag-bridge" aria-hidden="true"></div>
</section>

<!-- ════════════ PHASE 3 — DOCTRINE ════════════ -->
<section class="how-strip" aria-label="The shift">
  <p class="how-strip-hed">The Rules Have Changed</p>
  <div class="how-strip-bullets">
    <div class="how-strip-item">
      <span class="how-strip-dot" aria-hidden="true"></span>
      <span class="how-strip-label"><strong style="color:var(--ivory)">Search engines rank pages.</strong> AI systems choose answers. The selection criteria are different.</span>
    </div>
    <div class="how-strip-item">
      <span class="how-strip-dot" aria-hidden="true"></span>
      <span class="how-strip-label"><strong style="color:var(--ivory)">This is not SEO.</strong> It&rsquo;s retrieval infrastructure &mdash; the structural layer that determines whether AI can find, trust, and cite your business.</span>
    </div>
    <div class="how-strip-item">
      <span class="how-strip-dot" aria-hidden="true"></span>
      <span class="how-strip-label"><strong style="color:var(--ivory)">Most sites aren&rsquo;t built for this.</strong> The businesses AI cites are the ones who structured their content for it. Everyone else is invisible.</span>
    </div>
  </div>
  <p class="how-strip-sub">Your scan measures exactly how ready your site is for this new landscape.</p>
</section>

<!-- ════════════ PHASE 4 — SYSTEM ABSTRACTION ════════════ -->
<section class="feat-grid" aria-label="Four layers of AI visibility">
  <p class="feat-grid-eye">How the System Works</p>
  <h2 class="feat-grid-hed">Four layers. One compounding <em>advantage.</em></h2>
  <div class="feat-cards">
    <div class="feat-card">
      <div class="feat-card-icon">&#x25CE;</div>
      <p class="feat-card-title">Signals</p>
      <p class="feat-card-body">Structured data, entity markers, and machine-readable context that tell AI systems your business exists and what it does.</p>
    </div>
    <div class="feat-card">
      <div class="feat-card-icon">&#x25A4;</div>
      <p class="feat-card-title">Structure</p>
      <p class="feat-card-body">The content architecture that makes your answers retrievable &mdash; organized so AI can find, parse, and cite specific claims.</p>
    </div>
    <div class="feat-card">
      <div class="feat-card-icon">&#x2197;</div>
      <p class="feat-card-title">Coverage</p>
      <p class="feat-card-body">Expanding your citation footprint across every service, location, and query type where your market searches for answers.</p>
    </div>
    <div class="feat-card">
      <div class="feat-card-icon">&#x7B;&#x7D;</div>
      <p class="feat-card-title">Market</p>
      <p class="feat-card-body">Full infrastructure deployment &mdash; your citation system built, maintained, and reinforced across every surface that matters.</p>
    </div>
  </div>
</section>

<!-- ════════════ PHASE 5 — MID-PAGE CTA ════════════ -->
<div class="pricing-cta">
  <div class="pricing-cta-actions">
    <a href="{{ route('scan.start') }}" class="btn-primary">Start Your Scan — $2</a>
  </div>
  <p class="pricing-cta-meta">Instant score &nbsp;&middot;&nbsp; No account needed &nbsp;&middot;&nbsp; One URL, 60 seconds</p>
</div>

<!-- ════════════ PHASE 6 — TIER FRAMING ════════════ -->
<section class="how-strip" aria-label="Upgrade progression">
  <p class="how-strip-hed">One Scan. Multiple Levels of Control.</p>
  <div class="how-strip-bullets">
    <div class="how-strip-item">
      <span class="how-strip-dot" aria-hidden="true"></span>
      <span class="how-strip-label"><strong style="color:var(--ivory)">Some stop at insight.</strong> The $2 scan shows where you stand. That alone is worth knowing.</span>
    </div>
    <div class="how-strip-item">
      <span class="how-strip-dot" aria-hidden="true"></span>
      <span class="how-strip-label"><strong style="color:var(--ivory)">Some build leverage.</strong> Expand your scan into a full signal map, structural plan, and action sequence.</span>
    </div>
    <div class="how-strip-item">
      <span class="how-strip-dot" aria-hidden="true"></span>
      <span class="how-strip-label"><strong style="color:var(--ivory)">Some take the market.</strong> Deploy the full system &mdash; infrastructure built, maintained, and scaled for dominance.</span>
    </div>
  </div>
  <p class="how-strip-sub">Every level builds on the last. Your data carries forward. No wasted steps.</p>
</section>

<!-- ════════════ PRICING ════════════ -->
<section id="offer">
  <div class="ambient-network" aria-hidden="true">
    <canvas class="ambient-canvas" id="offerCanvas"></canvas>
    <div class="ambient-overlay"></div>
  </div>
  <div class="offer-intro">
    <div>
      <p class="s-eye">Pricing</p>
      <h2 class="s-h offer-hed-split">
        <span>One system.</span>
        <em>Six levels of control.</em>
      </h2>
    </div>
    <div class="offer-panel">
      <div class="offer-positioning">
        <p class="offer-positioning-bottom">Every level reveals more &mdash; from a quick readiness check to full market infrastructure.</p>
      </div>
    </div>
  </div>

  <div class="offer-guide">
    <p class="offer-guide-line">Choose where to start.</p>
    <p class="offer-guide-sub">Choose your entry point. The system builds forward from there.</p>
  </div>

  <!-- Ascension Rail -->
  <div class="ascent-rail">
    <div class="ascent-node"><span class="ascent-num">01</span><span class="ascent-label">Scan</span></div>
    <div class="ascent-line"></div>
    <div class="ascent-node"><span class="ascent-num">02</span><span class="ascent-label">Signal</span></div>
    <div class="ascent-line"></div>
    <div class="ascent-node --active"><span class="ascent-num">03</span><span class="ascent-label">Leverage</span></div>
    <div class="ascent-line"></div>
    <div class="ascent-node"><span class="ascent-num">04</span><span class="ascent-label">Activate</span></div>
    <div class="ascent-line"></div>
    <div class="ascent-node"><span class="ascent-num">05</span><span class="ascent-label">Expand</span></div>
    <div class="ascent-line"></div>
    <div class="ascent-node"><span class="ascent-num">06</span><span class="ascent-label">Control</span></div>
  </div>

  <div class="tier-grid-5" id="tierGrid">

    {{-- TIER 1 — Start: Citation Scan --}}
    <div class="tier scan-tier">
      <span class="tier-step">Step 01</span>
      <span class="tier-flag">Start</span>
      <h3 class="tier-name">Citation Scan</h3>
      <div class="tier-stack">
        <div class="tier-price"><sup>$</sup>2</div>
        <p class="tier-position">Reveals your citation readiness and AI visibility in seconds.</p>
        <div class="tier-divider"></div>
        <ul class="tier-features">
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4zM4 10h16M10 4v16"/></svg>
            0&ndash;100 citation readiness score
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 17l6-6 4 4 6-8M16 7h4v4"/></svg>
            Limited signal detection
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 6h4l3 3 3-3h4M5 18h4l3-3 3 3h4M12 9v6"/></svg>
            Sent to your inbox instantly
          </li>
        </ul>
      </div>
      <div class="tier-actions">
        <a href="{{ route('scan.start') }}" class="tier-cta">Start Scan&nbsp;&mdash; $2</a>
      </div>
    </div>

    {{-- TIER 2 — Grow: Signal Expansion --}}
    <div class="tier report-tier">
      <span class="tier-step">Step 02</span>
      <span class="tier-flag">Grow</span>
      <h3 class="tier-name">Signal Expansion</h3>
      <div class="tier-stack">
        <div class="tier-price"><sup>$</sup>99</div>
        <p class="tier-position">Full signal mapping — every gap, every opportunity, ranked by impact.</p>
        <div class="tier-divider"></div>
        <ul class="tier-features">
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4zM4 10h16M10 4v16"/></svg>
            <strong>Full visibility mapping</strong>
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 17l6-6 4 4 6-8M16 7h4v4"/></svg>
            <strong>Every gap ranked by impact</strong>
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10"/></svg>
            Exportable intelligence + dashboard
          </li>
        </ul>
      </div>
      <div class="tier-actions">
        <a href="{{ route('checkout.signal-expansion') }}" class="tier-cta">Map Your Signals&nbsp;&mdash; $99</a>
      </div>
    </div>

    {{-- TIER 3 — Scale: Structural Leverage (FOCAL / CORE) --}}
    <div class="tier focal">
      <span class="tier-step">Step 03</span>
      <span class="tier-flag">Scale &mdash; Core</span>
      <h3 class="tier-name">Structural Leverage</h3>
      <div class="tier-stack">
        <div class="tier-price"><sup>$</sup>249</div>
        <p class="tier-position">Every correction prioritized, every opportunity sized, every gap closed.</p>
        <div class="tier-divider"></div>
        <ul class="tier-features">
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4zM4 10h16M10 4v16"/></svg>
            <strong>Everything in Signal Expansion</strong>
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 17l6-6 4 4 6-8M16 7h4v4"/></svg>
            <strong>Priority correction sequence</strong>
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l4-4M8.5 9.5 6.7 11.3a3 3 0 1 0 4.2 4.2l1.8-1.8M15.5 14.5l1.8-1.8a3 3 0 1 0-4.2-4.2l-1.8 1.8"/></svg>
            Structural guidance + opportunity sizing
          </li>
        </ul>
      </div>
      <div class="tier-actions">
        <a href="{{ route('checkout.structural-leverage') }}" class="tier-cta">Build Your Leverage&nbsp;&mdash; $249</a>
      </div>
    </div>

    {{-- TIER 4 — Implement: System Activation --}}
    <div class="tier">
      <span class="tier-step">Step 04</span>
      <span class="tier-flag">Implement</span>
      <h3 class="tier-name">System Activation</h3>
      <div class="tier-stack">
        <div class="tier-price"><sup>$</sup>489</div>
        <p class="tier-position">Competitive positioning, market mapping, and full coverage architecture.</p>
        <div class="tier-divider"></div>
        <ul class="tier-features">
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <strong>Everything in Structural Leverage</strong>
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
            <strong>Competitive benchmarks</strong>
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            Coverage expansion map
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            50+ page structural architecture
          </li>
        </ul>
      </div>
      <div class="tier-actions">
        <a href="{{ route('checkout.system-activation') }}" class="tier-cta">Activate Your System&nbsp;&mdash; $489</a>
      </div>
    </div>

    {{-- TIER 5 — Expand: Market Expansion --}}
    <div class="tier">
      <span class="tier-step">Step 05</span>
      <span class="tier-flag">Expand</span>
      <h3 class="tier-name">Market Expansion</h3>
      <div class="tier-stack">
        <div class="tier-price"><sup>$</sup>1,500<sub>&ndash;2,500</sub></div>
        <p class="tier-position">Extend coverage, reinforce signals, and scale across new surfaces.</p>
        <div class="tier-divider"></div>
        <ul class="tier-features">
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
            <strong>Service + location expansion</strong>
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l4-4M8.5 9.5 6.7 11.3a3 3 0 1 0 4.2 4.2l1.8-1.8M15.5 14.5l1.8-1.8a3 3 0 1 0-4.2-4.2l-1.8 1.8"/></svg>
            Entity + linking reinforcement
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            Citation signal layering
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Priority growth roadmap
          </li>
        </ul>
      </div>
      <div class="tier-actions">
        <a href="{{ route('onboarding.start', ['tier' => 'expansion']) }}" class="tier-cta">Expand My Coverage</a>
      </div>
    </div>

  </div>

  <div class="tier-anchor-row">

    {{-- TIER 6 — Dominate: Market Control --}}
    <div class="tier prime">
      <span class="tier-step">Step 06</span>
      <span class="tier-flag">Dominate</span>
      <h3 class="tier-name">Market Control</h3>
      <div class="tier-stack">
        <div class="tier-price"><sup>$</sup>4,799<sub>+</sub></div>
        <p class="tier-position">Your entire citation infrastructure — built, deployed, and actively maintained across every surface.</p>
        <div class="tier-divider"></div>
        <ul class="tier-features">
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4a8 8 0 1 1 0 16 8 8 0 0 1 0-16Zm0 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/></svg>
            <strong>Full market infrastructure</strong>
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10"/></svg>
            <strong>Complete system activation</strong> — built for you
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l4-4M8.5 9.5 6.7 11.3a3 3 0 1 0 4.2 4.2l1.8-1.8M15.5 14.5l1.8-1.8a3 3 0 1 0-4.2-4.2l-1.8 1.8"/></svg>
            Ongoing maintenance + reinforcement
          </li>
          <li>
            <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4a8 8 0 1 1 0 16 8 8 0 0 1 0-16Zm0 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/></svg>
            Strategic oversight across full coverage
          </li>
        </ul>
      </div>
      <div class="tier-actions">
        <div class="tier-commitment">Structured 4-month build. Active coverage maintained thereafter.</div>
        <a href="{{ route('onboarding.start', ['tier' => 'dominance']) }}" class="tier-cta">Dominate Your Market</a>
        <button class="tier-book" onclick="window._bkPending={id:{{ $consultTypes->get('strategy')?->id ?? 2 }},duration:{{ $consultTypes->get('strategy')?->duration_minutes ?? 30 }},name:{{ json_encode($consultTypes->get('strategy')?->name ?? 'Strategy Call') }},isFree:{{ ($consultTypes->get('strategy')?->is_free ?? false) ? 'true' : 'false' }}};window.dispatchEvent(new CustomEvent('open-booking',{detail:window._bkPending}));if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'pricing_section',cta_label:'book_strategy_call'});">Book a Strategy Call</button>
      </div>
    </div>

  </div>

  <p class="offer-bottom-line">Most businesses begin with the $2 scan and expand as signal gaps are revealed.</p>
  <p class="offer-bottom-sub">Your scan data carries forward through every level. No wasted steps. &mdash; $2 &rarr; $99 &rarr; $249 &rarr; $489 &rarr; $1,500+ &rarr; $4,799+</p>

</section>

<!-- ════════════ EXECUTION SERVICES ════════════ -->
<section class="exec-services" aria-label="Build, Launch, and Scale">
  <p class="exec-eyebrow">Build, Launch, and Scale</p>
  <h2 class="exec-hed">Everything your expansion <em>requires.</em></h2>
  <p class="exec-intro">All services align with your market expansion — not disconnected marketing efforts.</p>
  <div class="exec-grid">
    <div class="exec-card r">
      <span class="exec-label">Website Strategy &amp; Development</span>
      <h3 class="exec-title">Conversion Architecture</h3>
      <p class="exec-body">Strategy-led builds engineered for conversion — designed to hold position and perform across every market you enter.</p>
      <a href="{{ route('web-design-development') }}" class="exec-learn">Learn more &rarr;</a>
    </div>
    <div class="exec-card r">
      <span class="exec-label">Paid Media Strategy</span>
      <h3 class="exec-title">Campaign Launch &amp; Growth</h3>
      <p class="exec-body">Paid media structured to reinforce your organic position — not replace it. Campaigns built for market velocity, not just clicks.</p>
      <a href="{{ route('ads-management') }}" class="exec-learn">Learn more &rarr;</a>
    </div>
    <div class="exec-card r">
      <span class="exec-label">Brand Management &amp; Creative Direction</span>
      <h3 class="exec-title">Market Delivery</h3>
      <p class="exec-body">Brand systems, creative direction, and market-ready collateral — built to communicate authority at every client touchpoint.</p>
      <a href="{{ route('branding-print') }}" class="exec-learn">Learn more &rarr;</a>
    </div>
  </div>
  <a href="{{ route('growth-services') }}" class="exec-all">See all services &rarr;</a>
</section>

<div style="margin:0 auto;width:60%;max-width:540px;height:1px;background:rgba(200,168,75,.06)"></div>

<!-- ════════════ CONTACT ════════════ -->
<section id="contact">
  <div class="contact-inner">
    <div>
      <p class="s-eye">Questions Before You Begin</p>
      <h2 class="s-h">Not ready yet?<br><em>Ask a question.</em></h2>
      <p class="s-p">If you have questions before starting onboarding, submit them here. We review every inquiry personally and respond directly.</p>
      <div class="c-meta">
        <div class="cm"><label>Licensing Model</label><span>Reviewed individually — not automated</span></div>
        <div class="cm"><label>Commitment</label><span>Structured 4-month deployment cycle</span></div>
        <div class="cm"><label>Legacy Builds</label><span>Re-entry at 10K tier required</span></div>
      </div>
    </div>

    <form class="cform r" method="POST" action="{{ route('licensing-inquiry.store') }}" id="inquiryForm">
      @csrf

      @if (session('inquiry_success'))
        <div class="form-success">{{ session('inquiry_success') }}</div>
      @endif

      @if ($errors->any())
        <div class="form-error">Please correct the highlighted fields and resubmit.</div>
      @endif

      <div class="frow">
        <div class="fg">
          <label for="name">Your Name</label>
          <input type="text" id="name" name="name" placeholder="Alex Chen" value="{{ old('name') }}" required>
          @error('name') <span class="field-error">{{ $message }}</span> @enderror
        </div>
        <div class="fg">
          <label for="company">Agency or Business Name</label>
          <input type="text" id="company" name="company" placeholder="Apex Digital" value="{{ old('company') }}" required>
          @error('company') <span class="field-error">{{ $message }}</span> @enderror
        </div>
      </div>
      <div class="fg">
        <label for="email">Work Email</label>
        <input type="email" id="email" name="email" placeholder="alex@apexdigital.com" value="{{ old('email') }}" required>
        @error('email') <span class="field-error">{{ $message }}</span> @enderror
      </div>
      <div class="fg">
        <label for="website">Website</label>
        <input type="url" id="website" name="website" placeholder="https://apexdigital.com" value="{{ old('website') }}">
        @error('website') <span class="field-error">{{ $message }}</span> @enderror
      </div>
      <div class="frow">
        <div class="fg">
          <label for="type">You are a…</label>
          <select id="type" name="type" required>
            <option value="">Select…</option>
            <option value="agency" @selected(old('type') === 'agency')>Agency — deploying across client portfolio</option>
            <option value="business" @selected(old('type') === 'business')>Business — seeking organic growth at scale</option>
            <option value="both" @selected(old('type') === 'both')>Both — agency with own business operations</option>
          </select>
          @error('type') <span class="field-error">{{ $message }}</span> @enderror
        </div>
        <div class="fg">
          <label for="tier">Licence Level of Interest</label>
          <select id="tier" name="tier" required>
            <option value="">Select…</option>
            <option value="starter" @selected(old('tier') === 'starter')>Entry Allocation — apply for access</option>
            <option value="5k" @selected(old('tier') === '5k')>Strategic Territory — $2,995/mo</option>
            <option value="10k" @selected(old('tier') === '10k')>Dominant Territory — $4,799/mo</option>
            <option value="legacy" @selected(old('tier') === 'legacy')>Legacy build — re-licence required</option>
          </select>
          @error('tier') <span class="field-error">{{ $message }}</span> @enderror
        </div>
      </div>
      <div class="fg">
        <label for="niche">Primary Niche or Market</label>
        <input type="text" id="niche" name="niche" placeholder="e.g. Law firms, Home services, Med-spa, HVAC…" value="{{ old('niche') }}">
        @error('niche') <span class="field-error">{{ $message }}</span> @enderror
      </div>
      <div class="fg">
        <label for="message">Tell us about your market and what you're looking to own</label>
        <textarea id="message" name="message" placeholder="We're an agency managing SEO for local service businesses / We're a business that's hit a growth ceiling and need structured market coverage across…" required>{{ old('message') }}</textarea>
        @error('message') <span class="field-error">{{ $message }}</span> @enderror
      </div>

      <input type="text" name="website_confirm" id="website_confirm" autocomplete="off" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;opacity:0">
      <input type="hidden" name="form_loaded_at" id="form_loaded_at" value="">
      @if(config('services.recaptcha.site_key'))
      <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response" value="">
      @endif

      <p style="font-size:.82rem;color:var(--muted);text-align:center;letter-spacing:.04em;margin-bottom:8px">We review every message personally. Not all markets are available.</p>
      <button type="submit" class="fsub" id="submitBtn">Send My Question</button>
    </form>
  </div>
</section>

<!-- ════════════ FINAL CLOSING CTA — DUAL SPLIT ════════════ -->
<section class="fcc" aria-label="Start your market expansion">
  <canvas class="fcc-canvas" id="fccCanvas" aria-hidden="true"></canvas>
  <div class="fcc-inner">
    <p class="fcc-eye">Market Position</p>
    <h2 class="fcc-hed">
      <span class="fcc-hed-1">The territory will be owned.</span>
      <span class="fcc-hed-2">The only question is by whom.</span>
    </h2>
    <p class="fcc-sub">Start with a scan. Or skip ahead and deploy the full system.</p>
    <span class="fcc-gold">First to structure. First to scale. First to be cited.</span>
    <span class="fcc-rule" aria-hidden="true"></span>
    <div class="fcc-actions">
      <div class="fcc-card fcc-card--scan">
        <p class="fcc-card-label">Start here</p>
        <p class="fcc-card-title">See where you stand</p>
        <a href="{{ route('scan.start') }}" class="fcc-primary" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'final_close',cta_label:'run_scan'});">Start Your Scan — $2</a>
        <p class="fcc-card-note">Results in seconds &middot; No account needed</p>
      </div>
      <div class="fcc-card fcc-card--system">
        <p class="fcc-card-label">Skip ahead</p>
        <p class="fcc-card-title">Deploy the full system</p>
        <a href="{{ route('onboarding.start', ['tier' => 'dominance']) }}" class="fcc-secondary" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'final_close',cta_label:'deploy_system'});">Start Market Control</a>
        <p class="fcc-card-note">Guided onboarding &middot; Strategy call included</p>
      </div>
    </div>
    <p class="fcc-reassure">Guided entry.&ensp;Structured rollout.&ensp;Full support.</p>
  </div>
</section>

<!-- ════════════ STICKY MOBILE CTA (mobile only — hidden on desktop via CSS) ════════════ -->
<div id="mobStickyCta" class="mob-sticky-cta" role="complementary" aria-label="Quick access — assess market availability">
  <div class="msc-inner">
    <a href="{{ route('scan.start') }}" class="msc-primary">Start Your Scan — $2</a>
    <a href="#offer" class="msc-secondary">Pricing</a>
  </div>
</div>

<!-- ════════════ BACK TO TOP ════════════ -->
<button class="btt" id="btt" aria-label="Back to top">
  <svg viewBox="0 0 24 24"><path d="M12 4l-8 8h5v8h6v-8h5z"/></svg>
</button>

<!-- ════════════ PAYWALL GATE (placeholder — Stripe checkout ready) ════════════ -->
<div class="gate-overlay" id="gateOverlay">
  <div class="gate-box">
    <div class="gate-icon">◈</div>
    <span class="gate-badge">Activation Required</span>
    <h2 class="gate-title">Make your site<br><em>the answer.</em></h2>
    <p class="gate-desc">Deploy the AI Citation Engine™ into your existing site.<br><br>This includes:<br><strong>&#8226; Structured service + location pages<br>&#8226; Schema, internal linking, and AI guidance signals<br>&#8226; Citation infrastructure across your full service area</strong><br><br>Deployed and managed under a single agreement.</p>
    <div class="gate-tiers">
      <div class="gate-tier" data-tier="expansion">
        <div class="gate-tier-name">System Activation</div>
        <div class="gate-tier-price">$489+</div>
        <div class="gate-tier-urls">Foundation level</div>
      </div>
      <div class="gate-tier" data-tier="market-expansion">
        <div class="gate-tier-name">Market Expansion</div>
        <div class="gate-tier-price">$1,500&ndash;$2,500</div>
        <div class="gate-tier-urls">Extended coverage</div>
      </div>
      <div class="gate-tier selected" data-tier="dominance">
        <div class="gate-tier-name">Market Control</div>
        <div class="gate-tier-price">$4,799/mo</div>
        <div class="gate-tier-urls">Preferred &middot; Priority access</div>
      </div>
    </div>
    <p class="gate-guidance">Most businesses start with System Activation, then expand into Market Control.</p>
    <a href="/onboarding/start?tier=dominance" class="gate-cta" id="gateCta">Activate My Market</a>
    <button class="gate-skip" id="gateSkip">Continue browsing</button>
  </div>
</div>

<!-- ════════════ GLOBAL CTA — funnel capture ════════════ -->
@include('partials.global-cta')

<!-- ════════════ FOOTER — privacy/terms at very bottom ════════════ -->
<footer>
  @include('components.payment-trust-footer')
  <div class="footer-main">
    <a href="{{ url('/') }}" class="logo">
      <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
    </a>
    <span class="footer-copy">&copy; 2026 SEO AI Co™ &middot; Programmatic AI SEO Systems</span>
  </div>
  <p style="text-align:center;font-size:.72rem;color:var(--muted);margin:6px 0 4px">
    <a href="mailto:hello@seoaico.com" style="color:var(--muted);text-decoration:none">hello@seoaico.com</a>
  </p>
  <p style="text-align:center;font-size:.6rem;color:rgba(168,168,160,.28);max-width:540px;margin:0 auto 8px;line-height:1.65">SEO AI Co™ operates the AI Citation Engine™ — structuring content for extraction and citation by AI systems across Google AI Overviews, ChatGPT, and Perplexity. Built for local service businesses competing in active markets.</p>
  <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:6px 14px;opacity:.22;margin-bottom:10px" aria-label="Platform integrations">
    <span style="font-size:.52rem;letter-spacing:.12em;text-transform:uppercase;color:#c8a84b">Google Analytics</span>
    <span style="color:#c8a84b;font-size:.5rem">&middot;</span>
    <span style="font-size:.52rem;letter-spacing:.12em;text-transform:uppercase;color:#c8a84b">Search Console</span>
    <span style="color:#c8a84b;font-size:.5rem">&middot;</span>
    <span style="font-size:.52rem;letter-spacing:.12em;text-transform:uppercase;color:#c8a84b">Google Business Profile</span>
    <span style="color:#c8a84b;font-size:.5rem">&middot;</span>
    <span style="font-size:.52rem;letter-spacing:.12em;text-transform:uppercase;color:#c8a84b">Microsoft Bing Ads</span>
  </div>
  <p style="text-align:center;font-size:.62rem;color:rgba(168,168,160,.35);max-width:520px;margin:0 auto 12px;line-height:1.5">
    SEO AI Co™ and associated systems, processes, and methodologies are proprietary and may not be reproduced without permission.
  </p>
  <nav class="footer-legal">
    <a href="{{ route('privacy') }}">Privacy</a>
    <a href="{{ route('terms') }}">Terms</a>
    <a href="{{ route('scan.start') }}">AI Citation Scan</a>
  </nav>
</footer>

<script>
  const nav = document.getElementById('nav');
  if(nav) window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60));
  const items = document.querySelectorAll('.r');
  const io = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
      if(e.isIntersecting){ setTimeout(() => e.target.classList.add('on'), i * 55); io.unobserve(e.target); }
    });
  }, {threshold:.1});
  items.forEach(el => io.observe(el));

  document.getElementById('form_loaded_at').value = Math.floor(Date.now() / 1000);

  document.getElementById('inquiryForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = 'Submitting…';
    @if(config('services.recaptcha.site_key'))
    // reCAPTCHA v3 — execute before submit
    e.preventDefault();
    grecaptcha.ready(function() {
      grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'inquiry_submit'}).then(function(token) {
        document.getElementById('g-recaptcha-response').value = token;
        document.getElementById('inquiryForm').submit();
      }).catch(function() {
        // reCAPTCHA failure should not block submission
        document.getElementById('inquiryForm').submit();
      });
    });
    @endif
  });

  // ── Back to Top ──
  const btt = document.getElementById('btt');
  if(btt){
    window.addEventListener('scroll', () => btt.classList.toggle('show', scrollY > 600), {passive:true});
    btt.addEventListener('click', () => window.scrollTo({top:0, behavior:'smooth'}));
  }

  // ── Sticky mobile CTA ──
  (function(){
    var bar = document.getElementById('mobStickyCta');
    if (!bar) return;
    var footer = document.querySelector('footer');
    var visible = false;

    function updateBar() {
      // Only active on phones
      if (window.innerWidth > 768) {
        if (visible) { bar.classList.remove('msc-visible'); document.body.classList.remove('msc-active'); visible = false; }
        return;
      }
      var sy = window.scrollY || window.pageYOffset;
      var winH = window.innerHeight;
      // Show after scrolling ~80% of first viewport
      var shouldShow = sy > winH * 0.8;
      // Hide when footer enters view (300px buffer)
      if (footer) {
        var ft = footer.getBoundingClientRect().top;
        if (ft < winH + 150) shouldShow = false;
      }
      // Hide when gate overlay is active (avoids layering)
      var gate = document.getElementById('gateOverlay');
      if (gate && gate.classList.contains('active')) shouldShow = false;

      if (shouldShow !== visible) {
        visible = shouldShow;
        bar.classList.toggle('msc-visible', visible);
        bar.classList.toggle('msc-hidden', !visible);
        document.body.classList.toggle('msc-active', visible);
      }
    }

    window.addEventListener('scroll', updateBar, {passive:true});
    window.addEventListener('resize', updateBar, {passive:true});
    updateBar();
  })();

  // ── Activation Gate ──
  const gateOverlay = document.getElementById('gateOverlay');
  const gateSkip = document.getElementById('gateSkip');
  const gateCta = document.getElementById('gateCta');
  const gateTiers = document.querySelectorAll('.gate-tier');

  function openGate() {
    gateOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeGate() {
    gateOverlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  // Clean up body lock on back-navigation
  window.addEventListener('popstate', closeGate);
  document.addEventListener('visibilitychange', function(){ if(document.hidden) closeGate(); });

  // Intent-based trigger — only open on explicit CTA click
  document.querySelectorAll('.js-open-gate').forEach(function(el) {
    el.addEventListener('click', function(e) {
      e.preventDefault();
      openGate();
    });
  });

  // Tier selection
  gateTiers.forEach(t => t.addEventListener('click', () => {
    gateTiers.forEach(x => x.classList.remove('selected'));
    t.classList.add('selected');
    var selectedTier = t.getAttribute('data-tier');
    gateCta.href = '/onboarding/start?tier=' + encodeURIComponent(selectedTier);
  }));

  // Skip closes gate; CTA navigates (href handles it after gate closes)
  gateSkip.addEventListener('click', closeGate);
  gateOverlay.addEventListener('click', function(e) {
    if (e.target === gateOverlay) closeGate();
  });

  @if (session('inquiry_success'))
    document.getElementById('contact').scrollIntoView({behavior:'smooth'});
  @endif

  /* ── Hero sequence ── */
  (function(){
    var el = document.getElementById('heroSeq');
    if(!el) return;
    var headlines = [
      'Will AI Cite<br>Your Website?',
      'Is Your Site<br>AI-Ready?',
      'Would AI<br>Recommend You?',
      'Are You Visible<br>to AI Search?',
      'Check Your<br>AI Readiness'
    ];
    var current = 0;
    var FADE  = 560;  // ms — must match CSS transition duration
    var PAUSE = 100;  // ms invisible gap; text swaps here
    var HOLD  = 3200; // ms fully visible per headline

    // Initial reveal — staggered to match rest of hero
    setTimeout(function(){ el.classList.add('hs-visible'); }, 80);

    // Reduced-motion: first headline only, no cycling
    if(window.matchMedia('(prefers-reduced-motion:reduce)').matches) return;

    // Start cycling after initial reveal + first hold period
    setTimeout(function(){
      (function cycle(){
        // Fade out
        el.classList.remove('hs-visible');
        el.classList.add('hs-out');
        setTimeout(function(){
          // Swap HTML while invisible, then fade in
          current = (current + 1) % headlines.length;
          el.innerHTML = headlines[current];
          el.classList.remove('hs-out');
          el.classList.add('hs-visible');
          // Schedule next cycle
          setTimeout(cycle, HOLD);
        }, FADE + PAUSE);
      })();
    }, 80 + HOLD);
  })();

  /* ── Market Allocation grid ── */
  (function(){
    var markets = [
      {region:'Pacific Northwest', states:'WA · OR',            status:'allocated'},
      {region:'California',        states:'CA',                 status:'allocated'},
      {region:'Southwest',         states:'AZ · NV · NM',       status:'limited'},
      {region:'Mountain West',     states:'CO · UT · ID · MT',  status:'open'},
      {region:'Midwest',           states:'IL · OH · MI · MN',  status:'allocated'},
      {region:'South Central',     states:'TX · OK · AR · LA',  status:'limited'},
      {region:'Southeast',         states:'FL · GA · NC · SC',  status:'limited'},
      {region:'Northeast',         states:'NY · NJ · CT · MA',  status:'allocated'},
    ];
    var labels = {allocated:'Active',limited:'Selective Access',open:'Expansion Available'};
    var subLabels = {allocated:'Live and expanding',limited:'Limited strategic entry',open:'Launch-ready market'};
    var tooltips = {
      allocated:'Search position claimed. Branded rollout active.',
      limited:'Limited entry window. Strategic review required.',
      open:'Expansion available. Search market open for rollout.'
    };
    var icons = {
      allocated:'<svg class="alloc-cell-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><rect x="1" y="10" width="3" height="5" rx=".6" fill="currentColor"/><rect x="6" y="6" width="3" height="9" rx=".6" fill="currentColor"/><rect x="11" y="2" width="3" height="13" rx=".6" fill="currentColor"/></svg>',
      limited:'<svg class="alloc-cell-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1"/><path d="M8 4.5V8.5L10.5 10" stroke="currentColor" stroke-width="1" stroke-linecap="round"/></svg>',
      open:'<svg class="alloc-cell-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M2.5 8h11M9 4l4.5 4L9 12" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/></svg>'
    };
    var grid = document.getElementById('allocGrid');
    if(!grid) return;
    grid.innerHTML = markets.map(function(m, i){
      return '<div class="alloc-cell">'
        + (icons[m.status] || '')
        + '<span class="alloc-cell-tooltip">' + tooltips[m.status] + '</span>'
        + '<p class="alloc-region">' + m.region + '</p>'
        + '<p class="alloc-states">' + m.states + '</p>'
        + '<div class="alloc-status">'
        +   '<div class="alloc-status-row">'
        +     '<span class="alloc-dot ' + m.status + '" data-delay="' + i + '"></span>'
        +     '<span class="alloc-status-label ' + m.status + '">' + labels[m.status] + '</span>'
        +   '</div>'
        +   '<span class="alloc-status-sub">' + subLabels[m.status] + '</span>'
        + '</div>'
        + '</div>';
    }).join('');
    /* staggered fade-in of dots */
    var dots = grid.querySelectorAll('.alloc-dot');
    dots.forEach(function(dot){
      var i = parseInt(dot.getAttribute('data-delay'), 10) || 0;
      setTimeout(function(){ dot.classList.add('alloc-dot-visible'); }, 120 + i * 90);
    });
  })();

  /* ── Offer / Licensing section ambient canvas ── */
  (function(){
    var canvas = document.getElementById('offerCanvas');
    if(!canvas) return;
    var ctx = canvas.getContext('2d');
    var DPR = window.devicePixelRatio || 1;
    var G = '200,168,75';
    var reduced = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    var nodes=[], raf, W, H;
    var COUNT, LINK, mobile;
    var tick = 0;
    /* Mouse proximity tracking (CSS-pixel coordinates) */
    var mouseX = -9999, mouseY = -9999;
    var HOVER_R = 180;
    var section = canvas.closest ? canvas.closest('section') : canvas.parentElement.parentElement.parentElement;
    if(section){
      section.addEventListener('mousemove', function(e){
        var r = canvas.getBoundingClientRect();
        mouseX = e.clientX - r.left;
        mouseY = e.clientY - r.top;
      },{passive:true});
      section.addEventListener('mouseleave', function(){
        mouseX = -9999; mouseY = -9999;
      },{passive:true});
    }
    function resize(){
      mobile = window.innerWidth < 700;
      COUNT  = mobile ? 27 : 42;
      LINK   = mobile ? 195 : 250;
      var r = canvas.getBoundingClientRect();
      W = r.width; H = r.height;
      if(!W || !H) return;
      canvas.width  = Math.round(W * DPR);
      canvas.height = Math.round(H * DPR);
      ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
    }
    function init(){
      resize(); nodes = [];
      for(var i = 0; i < COUNT; i++){
        nodes.push({
          x:  Math.random() * W,
          y:  Math.random() * H,
          vx: (Math.random() - .5) * .26,
          vy: (Math.random() - .5) * .26,
          r:  Math.random() * 2.1 + 1.05,  /* larger: range 1.05–3.15 */
          phase:    Math.random() * Math.PI * 2,
          glowMult: Math.random() * .9 + .55  /* depth: 0.55–1.45 */
        });
      }
    }
    function frame(){
      if(!W || !H){ raf = requestAnimationFrame(frame); return; }
      ctx.clearRect(0, 0, W, H);
      tick += 0.020;  /* slightly faster sine cycle */
      /* ── Connection lines ── */
      for(var i = 0; i < nodes.length; i++){
        for(var j = i+1; j < nodes.length; j++){
          var dx = nodes[j].x - nodes[i].x;
          var dy = nodes[j].y - nodes[i].y;
          var d  = Math.sqrt(dx*dx + dy*dy);
          if(d < LINK){
            ctx.beginPath();
            ctx.moveTo(nodes[i].x, nodes[i].y);
            ctx.lineTo(nodes[j].x, nodes[j].y);
            ctx.strokeStyle = 'rgba('+G+','+(1 - d/LINK)*.42+')';
            ctx.lineWidth = .65;
            ctx.stroke();
          }
        }
      }
      /* ── Nodes ── */
      for(var i = 0; i < nodes.length; i++){
        var n = nodes[i];
        var pulse = .44 + Math.sin(tick + n.phase) * .12;  /* range 0.32–0.56 */
        var glow  = n.glowMult;
        /* proximity boost — subtle card hover effect */
        var mdx = n.x - mouseX;
        var mdy = n.y - mouseY;
        var md  = Math.sqrt(mdx*mdx + mdy*mdy);
        var boost = md < HOVER_R ? (1 - md / HOVER_R) * .55 : 0;
        ctx.shadowBlur  = (glow + boost) * 12;
        ctx.shadowColor = 'rgba('+G+','+((glow + boost) * .52).toFixed(2)+')';
        ctx.beginPath();
        ctx.arc(n.x, n.y, n.r * (1 + boost * .3), 0, Math.PI*2);
        ctx.fillStyle = 'rgba('+G+','+(pulse + boost * .24).toFixed(3)+')';
        ctx.fill();
        ctx.shadowBlur = 0;
        if(!reduced){
          n.x += n.vx * .80;  /* moderately paced */
          n.y += n.vy * .80;
          if(n.x < 0) n.x = W;  if(n.x > W) n.x = 0;
          if(n.y < 0) n.y = H;  if(n.y > H) n.y = 0;
        }
      }
      raf = requestAnimationFrame(frame);
    }
    /* IntersectionObserver: only run when section is visible */
    if('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(entries){
        if(entries[0].isIntersecting){ init(); raf = requestAnimationFrame(frame); io.disconnect(); }
      },{threshold:.05});
      io.observe(canvas);
    } else {
      init(); raf = requestAnimationFrame(frame);
    }
    window.addEventListener('resize', function(){
      cancelAnimationFrame(raf); init(); raf = requestAnimationFrame(frame);
    });
  })();

  /* ── Infrastructure Principle canvas — sparse constellation ── */
  (function(){
    var canvas = document.getElementById('infraCanvas');
    if(!canvas) return;
    var ctx = canvas.getContext('2d');
    var nodes=[], raf, W, H;
    var COUNT=22, LINK=160, G='200,168,75';
    var reduced = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    var tick=0;

    function resize(){
      var DPR = window.devicePixelRatio || 1;
      W = canvas.offsetWidth;
      H = canvas.offsetHeight;
      canvas.width  = Math.round(W * DPR);
      canvas.height = Math.round(H * DPR);
      ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
    }
    function init(){
      resize(); nodes=[];
      for(var i=0;i<COUNT;i++){
        nodes.push({
          x:Math.random()*W, y:Math.random()*H,
          vx:(Math.random()-.5)*.52, vy:(Math.random()-.5)*.52,
          r:Math.random()*1.2+.7,
          phase:Math.random()*Math.PI*2
        });
      }
    }
    function frame(){
      ctx.clearRect(0,0,W,H);
      tick += 0.018;

      /* connection lines — sparse, fine */
      for(var i=0;i<nodes.length;i++){
        for(var j=i+1;j<nodes.length;j++){
          var dx=nodes[j].x-nodes[i].x, dy=nodes[j].y-nodes[i].y;
          var d=Math.sqrt(dx*dx+dy*dy);
          if(d<LINK){
            ctx.beginPath();
            ctx.moveTo(nodes[i].x,nodes[i].y);
            ctx.lineTo(nodes[j].x,nodes[j].y);
            ctx.strokeStyle='rgba('+G+','+(1-d/LINK)*.18+')';
            ctx.lineWidth=.4;
            ctx.stroke();
          }
        }
      }

      /* nodes — small, gentle pulse, no heavy glow */
      for(var i=0;i<nodes.length;i++){
        var n = nodes[i];
        var pulse = .30 + Math.sin(tick + n.phase) * .10;

        ctx.shadowBlur  = 6;
        ctx.shadowColor = 'rgba('+G+',.18)';
        ctx.beginPath();
        ctx.arc(n.x, n.y, n.r, 0, Math.PI*2);
        ctx.fillStyle   = 'rgba('+G+','+pulse.toFixed(3)+')';
        ctx.fill();
        ctx.shadowBlur  = 0;

        if(!reduced){
          n.x += n.vx;
          n.y += n.vy;
          if(n.x<0)n.x=W; if(n.x>W)n.x=0;
          if(n.y<0)n.y=H; if(n.y>H)n.y=0;
        }
      }

      raf = requestAnimationFrame(frame);
    }
    init();
    raf = requestAnimationFrame(frame);
    window.addEventListener('resize',function(){
      cancelAnimationFrame(raf); init();
      raf = requestAnimationFrame(frame);
    });
  })();

  /* ── Hero live network canvas (restrained — lighter than FCC) ── */
  (function(){
    var canvas = document.getElementById('heroAnimCanvas');
    if(!canvas) return;
    var ctx = canvas.getContext('2d');
    var nodes=[], raf, W, H;
    var BASE_COUNT=28, LINK=200, G='200,168,75';
    var reduced = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    var tick=0;

    function resize(){
      var DPR = Math.min(window.devicePixelRatio||1, 2);
      W = canvas.offsetWidth;
      H = canvas.offsetHeight;
      canvas.width  = Math.round(W * DPR);
      canvas.height = Math.round(H * DPR);
      ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
    }
    function init(){
      resize(); nodes=[];
      var COUNT = W < 640 ? 16 : BASE_COUNT;
      for(var i=0;i<COUNT;i++){
        nodes.push({
          x:Math.random()*W, y:Math.random()*H,
          vx:(Math.random()-.5)*.14, vy:(Math.random()-.5)*.14,
          r:Math.random()*1.4+.6,
          phase:Math.random()*Math.PI*2
        });
      }
    }
    function frame(){
      ctx.clearRect(0,0,W,H);
      tick += 0.016;

      for(var i=0;i<nodes.length;i++){
        for(var j=i+1;j<nodes.length;j++){
          var dx=nodes[j].x-nodes[i].x, dy=nodes[j].y-nodes[i].y;
          var d=Math.sqrt(dx*dx+dy*dy);
          if(d<LINK){
            ctx.beginPath();
            ctx.moveTo(nodes[i].x,nodes[i].y);
            ctx.lineTo(nodes[j].x,nodes[j].y);
            ctx.strokeStyle='rgba('+G+','+(1-d/LINK)*.22+')';
            ctx.lineWidth=.45;
            ctx.stroke();
          }
        }
      }

      for(var i=0;i<nodes.length;i++){
        var n = nodes[i];
        var pulse = .24 + Math.sin(tick + n.phase) * .09;
        ctx.beginPath();
        ctx.arc(n.x, n.y, n.r, 0, Math.PI*2);
        ctx.fillStyle = 'rgba('+G+','+pulse.toFixed(3)+')';
        ctx.fill();

        if(!reduced){
          n.x += n.vx * .72;
          n.y += n.vy * .72;
          if(n.x<0)n.x=W; if(n.x>W)n.x=0;
          if(n.y<0)n.y=H; if(n.y>H)n.y=0;
        }
      }
      raf = requestAnimationFrame(frame);
    }

    if('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(entries){
        if(entries[0].isIntersecting){init();raf=requestAnimationFrame(frame);io.disconnect();}
      },{threshold:.05});
      io.observe(canvas);
    } else {
      init(); raf = requestAnimationFrame(frame);
    }
    window.addEventListener('resize',function(){
      cancelAnimationFrame(raf); init();
      raf = requestAnimationFrame(frame);
    });
  })();

  /* ── Final Closing CTA canvas (brighter than infra-principle) ── */
  (function(){
    var canvas = document.getElementById('fccCanvas');
    if(!canvas) return;
    var ctx = canvas.getContext('2d');
    var nodes=[], raf, W, H;
    var COUNT=30, LINK=200, G='200,168,75';
    var reduced = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    var tick=0;

    function resize(){
      var DPR = window.devicePixelRatio || 1;
      W = canvas.offsetWidth;
      H = canvas.offsetHeight;
      canvas.width  = Math.round(W * DPR);
      canvas.height = Math.round(H * DPR);
      ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
    }
    function init(){
      resize(); nodes=[];
      for(var i=0;i<COUNT;i++){
        nodes.push({
          x:Math.random()*W, y:Math.random()*H,
          vx:(Math.random()-.5)*.22, vy:(Math.random()-.5)*.22,
          r:Math.random()*1.8+.9,
          phase:Math.random()*Math.PI*2,
          glowMult:Math.random()*.9+.6
        });
      }
    }
    function frame(){
      ctx.clearRect(0,0,W,H);
      tick += 0.022;

      for(var i=0;i<nodes.length;i++){
        for(var j=i+1;j<nodes.length;j++){
          var dx=nodes[j].x-nodes[i].x, dy=nodes[j].y-nodes[i].y;
          var d=Math.sqrt(dx*dx+dy*dy);
          if(d<LINK){
            ctx.beginPath();
            ctx.moveTo(nodes[i].x,nodes[i].y);
            ctx.lineTo(nodes[j].x,nodes[j].y);
            ctx.strokeStyle='rgba('+G+','+(1-d/LINK)*.22+')';
            ctx.lineWidth=.45;
            ctx.stroke();
          }
        }
      }

      for(var i=0;i<nodes.length;i++){
        var n = nodes[i];
        var pulse = .28 + Math.sin(tick + n.phase) * .10;
        var glow  = n.glowMult;
        ctx.shadowBlur  = glow * 8;
        ctx.shadowColor = 'rgba('+G+','+(glow*.28).toFixed(2)+')';
        ctx.beginPath();
        ctx.arc(n.x, n.y, n.r, 0, Math.PI*2);
        ctx.fillStyle   = 'rgba('+G+','+pulse.toFixed(3)+')';
        ctx.fill();
        ctx.shadowBlur  = 0;

        if(!reduced){
          n.x += n.vx * .65;
          n.y += n.vy * .65;
          if(n.x<0)n.x=W; if(n.x>W)n.x=0;
          if(n.y<0)n.y=H; if(n.y>H)n.y=0;
        }
      }
      raf = requestAnimationFrame(frame);
    }

    // Defer start until section enters viewport
    if('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(entries){
        if(entries[0].isIntersecting){init();raf=requestAnimationFrame(frame);io.disconnect();}
      },{threshold:.05});
      io.observe(canvas);
    } else {
      init(); raf = requestAnimationFrame(frame);
    }
    window.addEventListener('resize',function(){
      cancelAnimationFrame(raf); init();
      raf = requestAnimationFrame(frame);
    });
  })();

  /* ══════════════════════════════════════════════════════
     AMBIENT PARALLAX
     ──────────────────────────────────────────────────────
     GPU-only: transform + backgroundPosition only.
     Throttled: single rAF per scroll event, ticking flag.
     Passive listener: cannot block scroll thread.
     Reduced-motion: exits before any binding.
     Mobile (≤900px): rates halved to prevent iOS jitter.
     DO NOT add layout-triggering reads inside update().
  ══════════════════════════════════════════════════════ */
  (function(){
    if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    var wA   = document.querySelector('.amb-wrap-a');
    var wB   = document.querySelector('.amb-wrap-b');
    var grid = document.querySelector('.hero-grid');
    /* Halved rates on mobile prevent iOS Safari touch-scroll jitter */
    var mob  = window.innerWidth <= 900;
    var rA   = mob ? 0.04  : 0.08;
    var rB   = mob ? 0.025 : 0.05;
    var rG   = mob ? 0.015 : 0.03;
    var ticking = false;
    function update(){
      var y = window.scrollY;
      if(wA)   wA.style.transform   = 'translateY('+(-y*rA).toFixed(1)+'px)';
      if(wB)   wB.style.transform   = 'translateY('+(y*rB).toFixed(1)+'px)';
      if(grid) grid.style.backgroundPosition = '0 '+(-y*rG).toFixed(1)+'px';
      ticking = false;
    }
    window.addEventListener('scroll',function(){
      if(!ticking){requestAnimationFrame(update);ticking=true;}
    },{passive:true});
  })();
</script>

@include('partials.public-nav-js')

@include('components.booking-modal')

<script>
  if(typeof gtag==='function'){gtag('event','view_landing',{page_location:window.location.href});}
</script>
<script>
(function(){
  document.querySelectorAll('a[href*="/checkout/"]').forEach(function(el){
    el.addEventListener('click',function(){
      fetch('/api/v1/track',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({event:'homepage_cta_click',metadata:{label:el.textContent.trim().substring(0,60),href:el.getAttribute('href')}})}).catch(function(){});
    });
  });
})();
</script>
@include('components.tm-style')
</body>
</html>
