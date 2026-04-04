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
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SEO AI Co™ — Programmatic Search Infrastructure. Licensed. Controlled. Exclusive.</title>
<meta name="description" content="SEO AI Co™ is programmatic search infrastructure—deploying structured content, internal link architecture, and structured data across 1,000+ U.S. cities. Access is licensed. One operator per market.">
<link rel="canonical" href="{{ url('/') }}">
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
  --space-xs:10px;--space-sm:14px;--space-md:18px;--space-lg:28px;--space-xl:72px;
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
.logo-ai{
  font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.42rem;
  color:var(--gold);letter-spacing:.02em;
  display:inline-block;transform:skewX(-11deg) translateY(-1px);
}
.logo-co{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.05rem;color:rgba(255,255,255,.45);letter-spacing:.04em}

/* ── Nav ── */
nav{
  position:fixed;top:0;left:0;right:0;z-index:200;
  display:flex;align-items:center;justify-content:space-between;
  padding:28px 64px;border-bottom:1px solid transparent;transition:all .4s;
}
nav.stuck{background:rgba(8,8,8,.95);backdrop-filter:blur(16px);border-color:var(--border);padding:16px 64px}
.nav-right{display:flex;align-items:center;gap:32px}
.nav-link{font-size:.82rem;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s;position:relative;padding-bottom:2px}
.nav-link::after{content:'';position:absolute;bottom:0;left:0;right:100%;height:1px;background:var(--gold);transition:right .3s cubic-bezier(.23,1,.32,1)}
.nav-link:hover{color:var(--gold)}
.nav-link:hover::after{right:0}
.nav-btn{
  font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;
  color:var(--bg);background:var(--gold);padding:12px 28px;text-decoration:none;transition:background .3s;
  display:inline-flex;align-items:center;white-space:nowrap;
}
.nav-btn:hover{background:var(--gold-lt)}
.nav-account-short{display:none}

/* ── Hero ── */
#hero{
  display:flex;flex-direction:column;
  justify-content:flex-start;align-items:flex-start;
  padding:clamp(100px,13vh,148px) 64px 44px;position:relative;
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
  background:radial-gradient(ellipse at center,rgba(200,168,75,.07) 0%,transparent 62%);
  animation:ambDriftA 28s ease-in-out infinite alternate;
  will-change:transform;
}
.amb-orb-b{
  width:min(55vw,700px);height:min(55vw,700px);
  border-radius:50%;
  background:radial-gradient(ellipse at center,rgba(200,168,75,.04) 0%,transparent 60%);
  animation:ambDriftB 38s ease-in-out infinite alternate;
  will-change:transform;
}

/* Focal bloom — centred, reinforces hero headline composition */
.amb-bloom{
  position:fixed;top:8%;left:50%;
  width:min(110vw,1100px);height:60vh;
  transform:translateX(-50%);
  border-radius:50%;
  background:radial-gradient(ellipse at 42% 46%,rgba(200,168,75,.038) 0%,transparent 65%);
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
  .amb-orb-b{opacity:.55}
  .amb-bloom{opacity:.65}
  .amb-shimmer{display:none}
}

/* Reduced-motion: kill all CSS animations */
@media(prefers-reduced-motion:reduce){
  .amb-orb-a,.amb-orb-b{animation:none}
  .amb-shimmer{display:none!important}
}
/* ══ END AMBIENT SYSTEM ══ */

/* ── Rotating headline ── */
.hero-rotate{
  position:relative;
  width:100%;max-width:820px;
  margin-bottom:16px;
  opacity:0;animation:up .7s .1s forwards;
}
/*
  Sizer: in-flow, invisible — its text matches the longest headline phrase.
  This forces .hero-rotate to adopt the correct minimum height naturally,
  so absolutely-positioned lines never overflow into content below.
*/
.hero-rotate-sizer{
  display:block;visibility:hidden;pointer-events:none;user-select:none;
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(3rem,7vw,6.8rem);font-weight:300;line-height:1.06;
}
.hero-rotate-line{
  position:absolute;top:0;left:0;width:100%;
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(3rem,7vw,6.8rem);font-weight:300;line-height:1.06;
  color:var(--ivory);
  opacity:0;transition:opacity 1.4s ease;
  pointer-events:none;
}
.hero-rotate-line.active{opacity:1;pointer-events:auto}

/* ── Static anchor ── */
.hero-anchor{
  font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;
  font-size:clamp(1.05rem,1.7vw,1.4rem);letter-spacing:.04em;
  color:rgba(200,168,75,.5);line-height:1.5;
  margin-bottom:28px;
  opacity:0;animation:up .75s .32s forwards;
}

/* ── Body copy ── */
.hero-body{
  max-width:540px;margin-bottom:28px;
  display:flex;flex-direction:column;gap:14px;
  opacity:0;animation:up .85s .48s forwards;
}
.hb-line{font-size:.98rem;line-height:1.65;color:var(--muted)}
.hb-rule{
  padding-top:18px;margin-top:4px;
  border-top:1px solid rgba(200,168,75,.14);
  color:rgba(237,232,222,.5);
}

/* ── Conversion block ── */
.hero-convert{opacity:0;animation:up .85s .62s forwards}
.hc-alloc{
  font-family:'Cormorant Garamond',serif;font-style:italic;font-weight:300;
  font-size:clamp(1.2rem,1.9vw,1.65rem);color:var(--ivory);
  letter-spacing:.02em;margin-bottom:28px;line-height:1.4;
}

/* ── CTAs ── */
.hero-actions{display:flex;gap:20px;align-items:center}
.btn-primary{
  background:var(--gold);color:var(--bg);font-size:.82rem;font-weight:500;letter-spacing:.14em;
  text-transform:uppercase;padding:18px 48px;text-decoration:none;
  transition:background .3s,transform .2s,box-shadow .3s;
}
.btn-primary:hover{background:var(--gold-lt);transform:translateY(-2px);box-shadow:0 10px 30px rgba(200,168,75,.18)}
.btn-ghost{
  font-size:.82rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);
  text-decoration:none;padding-bottom:4px;position:relative;transition:color .3s;
}
.btn-ghost::after{
  content:'';position:absolute;bottom:0;left:0;right:100%;height:1px;
  background:var(--gold-dim);transition:right .38s cubic-bezier(.23,1,.32,1);
}
.btn-ghost:hover{color:var(--ivory)}
.btn-ghost:hover::after{right:0}

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

@keyframes up{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}

/* ── Shared section helpers ── */
.gold-rule{height:1px;background:linear-gradient(to right,transparent,var(--gold-dim),transparent)}
.s-eye{font-size:.76rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold);margin-bottom:14px;display:flex;align-items:center;gap:14px}
.s-eye::before{content:'';width:28px;height:1px;background:var(--gold)}
.s-h{font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem,4vw,3.6rem);font-weight:400;line-height:1.12;margin-bottom:20px}
.s-h em{font-style:italic;color:var(--gold)}
.s-p{font-size:1.05rem;line-height:1.9;color:var(--muted);max-width:680px}
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
.aud-card{background:var(--deep);padding:40px 36px;position:relative;overflow:hidden;transition:background .4s}
.aud-card:hover{background:var(--card)}
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
.steps-wrap{max-width:1200px;margin:0 auto;padding:72px 64px}
.steps-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--border);margin-top:40px}
.step{background:var(--deep);padding:36px 28px;position:relative;overflow:hidden;transition:background .4s}
.step::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent);transform:scaleX(0);transition:transform .5s cubic-bezier(.23,1,.32,1)}
.step:hover{background:var(--card)}
.step:hover::after{transform:scaleX(1)}
.step-n{font-family:'Cormorant Garamond',serif;font-size:3.6rem;font-weight:300;color:rgba(200,168,75,.25);line-height:1;margin-bottom:16px;transition:color .3s}
.step:hover .step-n{color:rgba(200,168,75,.45)}
.step-title{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:400;line-height:1.15;margin-bottom:10px;color:var(--ivory)}
.step-desc{font-size:.92rem;line-height:1.70;color:var(--muted)}

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
/* ── Pricing Buyer Guide ── */
.offer-guide{padding:0 0 40px;text-align:center;position:relative;z-index:2}
.offer-guide-line{font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;color:var(--gold-dim)}

/* ── Pricing / Offer ── */
#offer{padding:48px 64px;max-width:1200px;margin:0 auto;position:relative;overflow:hidden}
.offer-intro{display:grid;grid-template-columns:1fr 1fr;gap:56px;margin-bottom:40px;align-items:start;position:relative;z-index:2}
.offer-note{font-size:1rem;line-height:1.9;color:var(--muted)}
.offer-note strong{color:var(--ivory);font-weight:400}
/* ── Tier grid ─────────────────────────────────────────────── */
.tier-grid-3{
  display:grid;grid-template-columns:1fr 1fr 1fr;
  gap:0;background:transparent;
  align-items:start;
  position:relative;z-index:2;
}
/* ambient radial glow behind focal card */
.tier-grid-3::before{
  content:'';
  position:absolute;top:-80px;left:33.33%;width:33.34%;height:calc(100% + 160px);
  background:radial-gradient(ellipse at 50% 38%,rgba(200,168,75,.06) 0%,transparent 68%);
  pointer-events:none;z-index:0;
}

/* ── Base card ── */
.tier{
  background:var(--deep);padding:44px 40px;
  position:relative;overflow:hidden;z-index:1;
  border:1px solid rgba(200,168,75,.08);
  opacity:0;transform:translateY(18px);
  transition:
    opacity .65s cubic-bezier(.23,1,.32,1),
    transform .65s cubic-bezier(.23,1,.32,1),
    border-color .3s,box-shadow .35s,background .3s;
}
.tier.vis{opacity:1;transform:none}
.tier-grid-3 .tier:nth-child(1){transition-delay:.07s}
.tier-grid-3 .tier:nth-child(2){transition-delay:.17s}
.tier-grid-3 .tier:nth-child(3){transition-delay:.27s}
.tier:hover{
  background:rgba(13,12,10,1);
  transform:translateY(-4px);
  border-color:rgba(200,168,75,.18);
  box-shadow:0 20px 60px rgba(0,0,0,.6);
}

/* ── Entry Access — reduced visual weight ── */
.tier.starter{
  background:var(--deep);
  padding:36px 32px;
}
.tier.starter .tier-flag{color:rgba(200,168,75,.4);font-size:.65rem}
.tier.starter .tier-name{font-size:1.5rem;color:rgba(168,168,160,.82)}
.tier.starter .tier-price{font-size:2.4rem;color:rgba(200,168,75,.35)}
.tier.starter .tier-price sup{color:rgba(200,168,75,.35)}
.tier.starter .tier-commitment{color:rgba(168,168,160,.62)}
.tier.starter .tier-cta{
  color:rgba(200,168,75,.45);
  border:1px solid rgba(200,168,75,.18);
  font-size:.72rem;
}
.tier.starter .tier-cta:hover{
  background:rgba(200,168,75,.06);
  border-color:rgba(200,168,75,.3);
  color:var(--gold-dim);
}
.tier.starter .tier-book{
  border-color:rgba(255,255,255,.07);
  color:rgba(168,168,160,.45);
}
.tier.starter .tier-book:hover{
  border-color:rgba(200,168,75,.18);
  color:rgba(200,168,75,.45);
}

/* ── 5K card — primary focal point ── */
.tier.focal{
  background:rgba(14,13,11,1);
  padding:52px 44px;
  transform:scale(1.03) translateY(12px);
  z-index:2;
  border-color:rgba(200,168,75,.16);
  box-shadow:
    0 0 64px rgba(200,168,75,.04),
    0 28px 72px rgba(0,0,0,.55);
}
.tier.focal.vis{opacity:1;transform:scale(1.03) translateY(-6px)}
.tier.focal:hover{
  transform:scale(1.03) translateY(-10px);
  border-color:rgba(200,168,75,.26);
  box-shadow:
    0 0 80px rgba(200,168,75,.07),
    0 32px 80px rgba(0,0,0,.65);
  background:rgba(16,15,12,1);
}
.tier.focal::before{
  content:'';
  position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.5),transparent);
}
.tier.focal .tier-flag{color:var(--gold)}
.tier.focal .tier-name{font-weight:400}
.tier.focal .tier-cta{
  background:var(--gold);
  color:var(--bg);
  border:1px solid var(--gold);
}
.tier.focal .tier-cta:hover{
  background:var(--gold-lt);
  border-color:var(--gold-lt);
  box-shadow:0 8px 24px rgba(200,168,75,.18);
  transform:translateY(-2px);
}

/* ── 10K card ── */
.tier.prime{background:rgba(12,11,9,1)}
.tier.prime::before{
  content:'';
  position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.25),transparent);
}
.tier.prime .tier-cta{background:var(--gold);color:var(--bg);border:1px solid var(--gold)}
.tier.prime .tier-cta:hover{
  background:var(--gold-lt);border-color:var(--gold-lt);
  box-shadow:0 8px 24px rgba(200,168,75,.18);
  transform:translateY(-2px);
}
.tier.prime .tier-name{font-weight:400}

/* ── Shared type ── */
.tier-flag{font-size:.65rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);margin-bottom:20px;display:block}
.tier-name{font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:300;line-height:1.0;margin-bottom:8px;letter-spacing:-.02em;color:var(--ivory)}
.tier-urls{font-size:.8rem;color:var(--muted);letter-spacing:.03em;margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid rgba(200,168,75,.08)}
.tier-price{font-family:'Cormorant Garamond',serif;font-size:4.6rem;font-weight:300;color:var(--gold);line-height:1;margin-bottom:10px}
.tier-price sup{font-size:1.6rem;vertical-align:top;margin-top:16px;color:var(--gold-dim);opacity:.7}
.tier-price sub{font-size:.82rem;color:rgba(168,168,160,.58);letter-spacing:.01em}
.tier-commitment{font-size:.8rem;color:var(--muted);margin-bottom:28px;line-height:1.75}

/* ── Feature rows with SVG icons ── */
.tier-features{list-style:none;display:flex;flex-direction:column;gap:14px;margin-bottom:32px}
.tier-features li{
  display:flex;align-items:flex-start;gap:11px;
  font-size:.88rem;color:var(--muted);line-height:1.65;
}
.tier-features li svg{
  flex-shrink:0;margin-top:1px;
  width:16px;height:16px;
  color:var(--gold-dim);
  opacity:.75;
}
.tier.focal .tier-features li svg{opacity:1;color:var(--gold)}
.tier-features li strong{color:var(--ivory);font-weight:400}
.tier-features .soon{color:var(--gold-dim);font-style:normal;font-size:.8rem}

/* ── CTAs ── */
.tier-cta{
  display:block;text-align:center;font-size:.76rem;letter-spacing:.16em;text-transform:uppercase;
  padding:16px;text-decoration:none;transition:all .3s;
}
.tier .tier-cta{color:var(--gold);border:1px solid var(--gold-dim)}
.tier .tier-cta:hover{background:var(--gold-lt);color:var(--bg);border-color:var(--gold-lt)}
.tier-book{
  display:block;width:100%;margin-top:10px;padding:12px 16px;background:transparent;
  border:1px solid rgba(200,168,75,.1);color:var(--muted);font-size:.72rem;font-weight:400;
  letter-spacing:.12em;text-transform:uppercase;cursor:pointer;transition:all .3s;
  font-family:'DM Sans',sans-serif;
}
.tier-book:hover{border-color:var(--gold-dim);color:var(--gold)}

/* ── Gated note (Entry) ── */
.tier-gated{
  margin-top:16px;padding:14px 16px;border:1px solid rgba(200,168,75,.12);
  font-size:.8rem;line-height:1.7;color:rgba(168,168,160,.72);
  display:flex;align-items:flex-start;gap:10px;
}
.tier-gated-icon{color:rgba(200,168,75,.4);flex-shrink:0;margin-top:1px;font-size:.8rem}
.tier-gated strong{color:rgba(237,232,222,.7);font-weight:400}

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
@media(max-width:768px){.alloc-cell-tooltip{display:none}}
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
.alloc-status{display:flex;align-items:center;gap:9px}

/* ── Status dot animations ── */
@keyframes dotPulseActive{
  0%,100%{opacity:.70}
  50%{opacity:1}
}
@keyframes dotGlowLimited{
  0%,100%{box-shadow:0 0 4px rgba(245,158,11,.45)}
  50%{box-shadow:0 0 9px rgba(245,158,11,.65)}
}
.alloc-dot{
  width:8px;height:8px;border-radius:50%;flex-shrink:0;
  opacity:0;transition:opacity .4s;
}
.alloc-dot.alloc-dot-visible{opacity:1}
.alloc-dot.allocated{
  background:#22c55e;
  box-shadow:0 0 5px rgba(34,197,94,.35);
  animation:dotPulseActive 3.5s ease-in-out infinite;
}
.alloc-dot.limited{
  background:#f59e0b;
  box-shadow:0 0 4px rgba(245,158,11,.40);
  animation:dotGlowLimited 3.8s ease-in-out infinite;
  opacity:.95;
}
.alloc-dot.open{
  background:#84cc16;
  box-shadow:0 0 5px rgba(132,204,22,.28);
}

.alloc-status-label{font-size:.74rem;letter-spacing:.15em;text-transform:uppercase;font-weight:400}
.alloc-status-label.allocated{color:#22c55e}
.alloc-status-label.limited{color:#f59e0b}
.alloc-status-label.open{color:#84cc16;letter-spacing:.18em}
.alloc-legend{
  margin-top:16px;padding:16px 22px;border:1px solid rgba(200,168,75,.18);
  display:flex;gap:24px;align-items:center;flex-wrap:wrap;
  background:rgba(8,8,8,.6);
}
.alloc-legend-item{display:flex;align-items:center;gap:9px}
.alloc-legend-label{font-size:.75rem;letter-spacing:.13em;text-transform:uppercase;color:rgba(168,168,160,.75)}
.alloc-avail-note{
  margin-top:14px;padding:20px 24px;
  border:1px solid rgba(200,168,75,.18);
  background:rgba(12,11,8,.7);
  font-size:clamp(.97rem,1.2vw,1rem);line-height:1.85;color:rgba(168,168,160,.82);
}
.alloc-avail-note strong{color:rgba(237,232,222,.82);font-weight:400}

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
.gate-skip{display:block;margin-top:16px;font-size:.76rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);cursor:pointer;border:none;background:none;transition:color .3s}
.gate-skip:hover{color:var(--ivory)}
.gate-badge{display:inline-block;font-size:.62rem;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);border:1px solid var(--gold-dim);padding:4px 10px;margin-bottom:20px}

/* ── Reveal animation ── */
.r{opacity:0;transform:translateY(32px);transition:opacity .85s cubic-bezier(.23,1,.32,1),transform .85s cubic-bezier(.23,1,.32,1)}
.r.on{opacity:1;transform:none}

/* ── Large tablet / small desktop nav compact ── */
@media(max-width:1200px){
  nav{padding:20px 36px}
  nav.stuck{padding:14px 36px}
  .nav-link{font-size:.72rem;letter-spacing:.13em}
  .nav-btn{font-size:.72rem;letter-spacing:.13em;padding:12px 22px;min-height:40px}
  .nav-account-full{display:none}
  .nav-account-short{display:inline}
}

/* ── Mobile hamburger ── */
.nav-hamburger{
  display:none;
  flex-direction:column;justify-content:center;align-items:center;gap:5px;
  width:44px;height:44px;background:none;border:none;cursor:pointer;
  padding:8px;z-index:9300;position:relative;flex-shrink:0;
}
.nav-hamburger span{
  display:block;width:22px;height:1.5px;background:var(--ivory);
  transition:transform .28s ease,opacity .22s,width .22s;
  transform-origin:center;
}
.nav-hamburger.is-open span:nth-child(1){transform:translateY(6.5px) rotate(45deg)}
.nav-hamburger.is-open span:nth-child(2){opacity:0;width:0}
.nav-hamburger.is-open span:nth-child(3){transform:translateY(-6.5px) rotate(-45deg)}

/* ── Panel backdrop ── */
.nav-backdrop{
  position:fixed;inset:0;
  z-index:9100;
  background:rgba(0,0,0,.62);
  opacity:0;visibility:hidden;
  transition:opacity .3s ease,visibility 0s .3s;
}
.nav-backdrop.is-open{
  opacity:1;visibility:visible;
  transition:opacity .3s ease,visibility 0s 0s;
}

/* ── Slide-in panel ── */
.nav-menu{
  position:fixed;
  top:0;right:0;bottom:0;
  width:300px;max-width:85vw;
  z-index:9200;
  background:#0d0c08;
  border-left:1px solid rgba(200,168,75,.12);
  box-shadow:-12px 0 48px rgba(0,0,0,.6);
  overflow-y:auto;
  display:flex;flex-direction:column;
  transform:translateX(100%);
  visibility:hidden;
  transition:transform .3s cubic-bezier(.23,1,.32,1),visibility 0s .3s;
}
.nav-menu.is-open{
  transform:translateX(0);
  visibility:visible;
  transition:transform .3s cubic-bezier(.23,1,.32,1),visibility 0s 0s;
}
.nav-menu-inner{
  padding:88px 0 52px;
  flex:1;display:flex;flex-direction:column;
}

/* Panel menu links */
.nm-link{
  display:flex;align-items:center;justify-content:space-between;
  padding:20px 32px;
  font-family:'DM Sans',sans-serif;
  font-size:.82rem;letter-spacing:.08em;
  color:rgba(168,168,160,.85);text-decoration:none;
  transition:color .18s,background .18s;
  min-height:52px;
}
.nm-link::after{
  content:'›';
  color:rgba(200,168,75,.3);
  font-size:1rem;transition:color .18s,transform .18s;
}
.nm-link:hover{color:var(--ivory);background:rgba(200,168,75,.04)}
.nm-link:hover::after{color:var(--gold);transform:translateX(3px)}
.nm-link.nm-featured{
  color:var(--gold);font-weight:500;letter-spacing:.1em;
}
.nm-link.nm-featured::after{color:var(--gold)}

/* Divider */
.nm-divider{
  height:1px;
  background:rgba(200,168,75,.08);
  margin:8px 0;
}

/* Portal row */
.nm-portal{
  display:flex;align-items:center;justify-content:space-between;
  padding:20px 32px;
  color:var(--gold);font-family:'DM Sans',sans-serif;
  font-size:.82rem;font-weight:500;letter-spacing:.1em;
  text-decoration:none;
  transition:color .18s,background .18s;
  min-height:52px;
}
.nm-portal::after{
  content:'›';
  color:var(--gold);
  font-size:1rem;transition:color .18s,transform .18s;
}
.nm-portal:hover{color:var(--ivory);background:rgba(200,168,75,.04)}
.nm-portal:hover::after{color:var(--ivory);transform:translateX(3px)}

/* Sign In secondary row */
.nm-signin{
  display:flex;align-items:center;justify-content:space-between;
  padding:16px 32px;
  font-family:'DM Sans',sans-serif;
  font-size:.74rem;letter-spacing:.1em;
  color:rgba(168,168,160,.45);text-decoration:none;
  transition:color .18s;
  min-height:44px;
}
.nm-signin::after{content:'›';color:rgba(200,168,75,.2);font-size:.95rem;transition:color .18s}
.nm-signin:hover{color:rgba(168,168,160,.8)}
.nm-signin:hover::after{color:rgba(200,168,75,.5)}

/* ── Mobile ── */
@media(max-width:900px){
  html{font-size:17px;-webkit-text-size-adjust:100%}
  body{-webkit-overflow-scrolling:touch}
  nav{padding:14px 20px}nav.stuck{padding:10px 20px}.nav-link{display:none}
  .nav-btn{display:none}
  .nav-account{display:none}
  .nav-hamburger{display:flex}
  #hero{padding:88px 24px 60px}
  .hero-actions{flex-direction:column;gap:16px;width:100%}
  .btn-primary{width:100%;text-align:center;padding:16px 24px}
  .btn-ghost{text-align:center}
  /* Mobile: stronger ambient atmosphere for cinematic first-screen feel */
  .amb-orb-a{background:radial-gradient(ellipse at center,rgba(200,168,75,.11) 0%,transparent 62%)}
  .amb-orb-b{background:radial-gradient(ellipse at center,rgba(200,168,75,.07) 0%,transparent 60%);opacity:1}
  .amb-bloom{background:radial-gradient(ellipse at 42% 46%,rgba(200,168,75,.07) 0%,transparent 65%);opacity:1}
  .amb-shimmer{display:block}
  .hero-scroll{left:20px;bottom:32px}
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
  .audience-grid,.tier-grid-3{grid-template-columns:1fr}
  .aud-card{padding:40px 24px}
  .aud-title{font-size:1.6rem}
  .wyl-grid,.steps-grid{grid-template-columns:1fr 1fr}
  .wyl-grid{gap:14px}
  .wyl-card{padding:32px 26px}
  .wyl-icon{font-size:1.9rem;margin-bottom:18px}
  .step{padding:32px 20px}
  .step-n{font-size:2.8rem;margin-bottom:14px}
  .step-title{font-size:1.2rem}
  /* exp-grid mobile handled by expansion @media block */
  .url-lock-inner{grid-template-columns:1fr;gap:28px}
  .ul-title{font-size:clamp(1.5rem,5vw,2rem)}
  .ul-lead{font-size:.95rem}
  .ul-states{grid-template-columns:1fr}
  .ul-state{padding:18px 18px}
  .ul-note{padding:14px 16px}
  .access-grid{grid-template-columns:1fr}
  .ac-card{padding:40px 32px}
  .tier{padding:40px 28px}
  .tier-name{font-size:1.6rem}
  .tier-price{font-size:3.2rem}
  .tier-price sup{font-size:1.4rem}
  .tier.focal{transform:none;border:1px solid rgba(200,168,75,.12)}
  .tier.focal.vis{transform:none}
  .tier.focal:hover{transform:translateY(-4px)}
  .tier-grid-3::before{display:none}
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
  .r{transform:translateY(20px)}
}
@media(max-width:520px){
  html{font-size:16px}
  #hero{padding:80px 20px 48px}
  .hero-scroll{display:none}
  .alloc-section{padding:48px 20px}
  .alloc-sub{max-width:100%}
  .wyl-icon{font-size:2.2rem;margin-bottom:20px}
  .wyl-card{padding:30px 24px}
  .wyl-title{font-size:1.3rem}
  .proof-icon{font-size:1.8rem;margin-bottom:10px}
  .wyl-grid,.steps-grid{grid-template-columns:1fr}
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
  height:calc(2em * 1.03);
  position:relative;      /* positioning context for h1 */
  width:100%;             /* fill flex parent so absolute h1 isn't clipped */
  margin-bottom:40px;     /* gap to gold accent */
}
#heroSeq{
  font-family:'Cormorant Garamond',serif;
  font-size:inherit;font-weight:300;line-height:1.03;
  color:var(--ivory);letter-spacing:-.02em;margin:0;
  /* position:absolute removes from flow — animation is visual only */
  position:absolute;top:0;left:0;width:100%;
  opacity:0;transform:translateY(24px);
  transition:opacity 560ms cubic-bezier(.16,1,.3,1),
             transform 560ms cubic-bezier(.16,1,.3,1);
}
#heroSeq.hs-visible{opacity:1;transform:translateY(0)}
#heroSeq.hs-out{opacity:0;transform:translateY(-8px)}
.hero-gold-accent{
  font-family:'Cormorant Garamond',serif;font-style:italic;font-weight:400;
  font-size:clamp(1.45rem,2.75vw,2.15rem);
  color:var(--gold);letter-spacing:.025em;line-height:1.32;
  opacity:0;animation:up .75s .2s forwards;
  margin-bottom:22px;
}
.hero-sub{
  font-size:clamp(.98rem,1.4vw,1.1rem);line-height:1.75;
  color:rgba(168,168,160,.75);max-width:560px;
  opacity:0;animation:up .8s .35s forwards;
  margin-bottom:12px;
}
.hero-note{
  font-size:.74rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.5);
  opacity:0;animation:up .8s .46s forwards;
  margin-bottom:28px;
}

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
}
.infra-canvas{
  position:absolute;inset:0;width:100%;height:100%;
  pointer-events:none;z-index:0;
}
/* breathing radial glow */
.infra-principle::before{
  content:'';
  position:absolute;inset:0;
  background:radial-gradient(ellipse 72% 68% at 50% 50%,rgba(200,168,75,.07) 0%,transparent 68%);
  pointer-events:none;
  animation:infraGlow 7s ease-in-out infinite;
}
/* faint grid texture */
.infra-principle::after{
  content:'';
  position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(200,168,75,.02) 1px,transparent 1px),
    linear-gradient(90deg,rgba(200,168,75,.02) 1px,transparent 1px);
  background-size:72px 72px;
  pointer-events:none;
}
@keyframes infraGlow{
  0%,100%{opacity:.65}
  50%{opacity:1}
}
.infra-inner{
  position:relative;z-index:1;
  max-width:900px;margin:0 auto;
}
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
  line-height:1.1;margin-bottom:48px;
  display:flex;flex-direction:column;gap:0;
}
.infra-hed-1{
  font-size:clamp(3rem,5.8vw,5.6rem);
  color:var(--ivory);letter-spacing:-.018em;
}
.infra-hed-2{
  font-size:clamp(3rem,5.8vw,5.6rem);
  color:rgba(237,232,222,.68);letter-spacing:-.018em;font-style:italic;
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

/* ── Settlement section ── */
.settlement{
  padding:44px 64px;
  border-top:1px solid rgba(154,122,48,.1);
}
.settlement-inner{max-width:960px;margin:0 auto}
.settle-hed{
  font-family:'Cormorant Garamond',serif;font-weight:300;font-style:normal;
  font-size:clamp(1.4rem,2.2vw,2rem);line-height:1.1;
  color:var(--ivory);margin-bottom:4px;
}
.settle-hed em{font-style:italic;color:var(--gold-lt)}
.settle-body{font-size:.88rem;line-height:1.8;color:rgba(168,168,160,.78);margin-bottom:8px;max-width:560px}
.settle-wallet-note{
  font-size:.76rem;letter-spacing:.06em;color:rgba(168,168,160,.44);
  margin-top:12px;font-style:italic;
}
.settle-icons{
  display:flex;gap:32px;align-items:center;flex-wrap:wrap;
  margin:24px 0 20px;
}
.settle-icon-item{
  display:flex;flex-direction:column;align-items:center;gap:8px;
  opacity:.46;transition:opacity .3s,filter .3s;cursor:default;
  position:relative;
}
.settle-icon-item:hover{
  opacity:.86;filter:drop-shadow(0 0 10px rgba(200,168,75,.22));
}
.settle-icon-item:hover .settle-icon-label{color:var(--ivory)}
.settle-icon-item[data-tip]:hover::after{
  content:attr(data-tip);
  position:absolute;bottom:calc(100% + 10px);left:50%;transform:translateX(-50%);
  background:rgba(10,9,7,.94);border:1px solid rgba(200,168,75,.14);
  color:rgba(168,168,160,.82);font-size:.6rem;letter-spacing:.08em;
  white-space:nowrap;padding:5px 10px;pointer-events:none;z-index:10;
}
.settle-icon-logo{
  width:44px;height:32px;display:flex;align-items:center;justify-content:center;
  color:var(--ivory);
}
.settle-icon-label{
  font-size:.56rem;letter-spacing:.22em;text-transform:uppercase;color:var(--muted);
  transition:color .3s;
}
.settle-trust{
  display:grid;grid-template-columns:1fr 1fr;gap:0;
  background:rgba(200,168,75,.05);
  margin-top:24px;
}
.settle-trust-item{
  background:var(--deep);padding:20px 24px;position:relative;
}
.settle-trust-item::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.09),transparent);
}
.settle-trust-strong{
  font-size:.62rem;letter-spacing:.18em;text-transform:uppercase;
  color:var(--gold-dim);display:block;margin-bottom:6px;
}
.settle-trust-text{font-size:.84rem;line-height:1.7;color:rgba(168,168,160,.7)}

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
  .settlement{padding:36px 24px}
  .settle-trust{grid-template-columns:1fr}
  .settle-icons{gap:20px}
  .alloc-decision{padding:28px 24px;max-width:100%}
}
@media(max-width:520px){
  .infra-principle{padding:72px 20px}
  .infra-hed-1,.infra-hed-2{font-size:clamp(2.4rem,9.5vw,3.4rem)}
  .infra-gold{font-size:clamp(1.6rem,5.8vw,2.2rem)}
  .settlement{padding:28px 20px}
  .hero-stage{font-size:clamp(3.2rem,11vw,5rem)}
  .hero-gold-accent{font-size:clamp(1.1rem,4.5vw,1.4rem)}
  .exp-momentum-main{font-size:clamp(1.2rem,4.5vw,1.6rem)}
}

/* ═══════════════════════════════════════════════════════════
   MOBILE REFINEMENT PASS
   Scope: max-width:768px (phone-first, 375–430px primary)
   Rule: zero desktop impact — all selectors inside media queries
═══════════════════════════════════════════════════════════ */

/* ── 1. Typography ── */
@media(max-width:768px){
  /* Body copy — lift to ≥16px at 17px root */
  .wyl-desc{font-size:.95rem;line-height:1.76;color:rgba(168,168,160,.92)}
  .step-desc{font-size:.95rem;line-height:1.74;color:rgba(168,168,160,.92)}
  .tier-features li{font-size:.93rem;line-height:1.72}
  .tier-commitment{font-size:.86rem;line-height:1.82}
  .tier-urls{font-size:.86rem;line-height:1.7}
  .ul-body{font-size:.97rem;line-height:1.76}
  .ul-state-desc{font-size:.9rem;line-height:1.76}
  .pos-support{font-size:.97rem;line-height:1.74}
  .pos-proof li{font-size:.97rem;line-height:1.72}
  .alloc-reinforce span{font-size:.97rem;line-height:1.76}
  .alloc-sub p{font-size:1rem;line-height:1.82}
  .offer-note{font-size:.95rem;line-height:1.84}
  .settle-body{font-size:.93rem;line-height:1.84}
  .settle-trust-text{font-size:.9rem;line-height:1.76}
  .alloc-avail-note{font-size:.91rem;line-height:1.86}
  .exp-body{font-size:.93rem;line-height:1.76}
  .ac-body{font-size:.93rem;line-height:1.76}
  .stmt-body p{font-size:.97rem;line-height:1.8}
  .hb-line{font-size:1rem;line-height:1.74}
  .hb-rule{font-size:.97rem;line-height:1.74}

  /* Small uppercase labels — reduce letter-spacing */
  .s-eye{letter-spacing:.18em;font-size:.72rem}
  .tier-flag{letter-spacing:.16em}
  .alloc-eyebrow{letter-spacing:.2em;font-size:.66rem}
  .access-eyebrow{letter-spacing:.2em;font-size:.68rem}
  .infra-eyebrow{letter-spacing:.24em;font-size:.6rem}
  .alloc-convert-label{letter-spacing:.16em;font-size:.75rem}
  .alloc-panel-label{letter-spacing:.18em;font-size:.75rem}

  /* Display headings — tighter so wrapping is intentional, not accidental */
  .pos-h2{line-height:1.07}
  .alloc-hed{line-height:1.11}
  .access-headline{line-height:1.13}
  .wyl-title{font-size:1.28rem}
  .step-title{font-size:1.18rem}

  /* Hero supporting copy */
  .hero-gold-accent{margin-bottom:14px;line-height:1.3}
  .hero-sub{margin-bottom:10px}
  .hero-note{margin-bottom:22px}
}

/* ── 2. Section spacing ── */
@media(max-width:768px){
  .wyl-card{padding:32px 22px}
  .step{padding:32px 22px}
  .ac-card{padding:36px 26px}
  .settle-trust-item{padding:18px 20px}
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
  /* Primary — dominant, thumb-friendly */
  .btn-primary{
    min-height:56px;padding:18px 28px;font-size:.84rem;
    display:flex;align-items:center;justify-content:center;
  }
  /* Secondary — quieter, still accessible */
  .btn-ghost{opacity:.68;font-size:.78rem;letter-spacing:.13em;padding-bottom:3px}
  .hero-actions{gap:20px}
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

/* ── 11. Hero headline — very small phones (≤390px) ── */
@media(max-width:390px){
  /* Reduce font-size floor so 5-word headlines wrap to 2 lines max, not 3 */
  .hero-stage{font-size:clamp(2.7rem,10.2vw,3.5rem)}
  /* Compensate: stage height is 2em × line-height — keep reserve at 2 lines */
  /* (height is set via JS-matchable clamp; CSS height:2.12em still correct) */

  /* Gold accent — slightly tighter */
  .hero-gold-accent{font-size:clamp(1rem,4.2vw,1.3rem);margin-bottom:12px}

  /* Hero section padding — recover vertical space gained */
  #hero{padding:76px 20px 44px}
}
</style>
@if(config('services.recaptcha.site_key'))
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>
@endif
@include('partials.clarity')
</head>
<body>

<!-- ════════════ AMBIENT ATMOSPHERIC LAYERS ════════════ -->
<div class="amb-wrap amb-wrap-a" aria-hidden="true"><div class="amb-orb-a"></div></div>
<div class="amb-wrap amb-wrap-b" aria-hidden="true"><div class="amb-orb-b"></div></div>
<div class="amb-bloom" aria-hidden="true"></div>
<div class="amb-shimmer" aria-hidden="true"></div>

<!-- ════════════ NAV ════════════ -->
<nav id="nav">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <div class="nav-right">
    <a href="/how-it-works" class="nav-link">How It Works</a>
    @auth
      <a href="/dashboard" class="nav-btn nav-account" style="background:linear-gradient(90deg,var(--gold),var(--gold-lt));color:var(--deep);box-shadow:0 2px 12px 0 rgba(200,168,75,.13);font-weight:500;letter-spacing:.18em;"><span class="nav-account-full">My Dashboard</span><span class="nav-account-short">Dashboard</span></a>
    @else
      <a href="/admin/login" class="nav-btn nav-account" style="background:linear-gradient(90deg,var(--gold),var(--gold-lt));color:var(--deep);box-shadow:0 2px 12px 0 rgba(200,168,75,.13);font-weight:500;letter-spacing:.18em;"><span class="nav-account-full">Portal</span><span class="nav-account-short">Portal</span></a>
    @endauth
  </div>
  <button class="nav-hamburger" id="navHamburger" aria-label="Open menu" aria-expanded="false" aria-controls="navMenu">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- ════════════ MOBILE SLIDE-IN PANEL ════════════ -->
<div id="navBackdrop" class="nav-backdrop" aria-hidden="true"></div>
<div id="navMenu" class="nav-menu" aria-hidden="true" role="dialog" aria-label="Site navigation">
  <div class="nav-menu-inner">
    <a href="#contact" class="nm-link nm-featured" data-menu-close>Check Availability</a>
    <a href="/book" class="nm-link" data-menu-close>Book a Session</a>
    <a href="/how-it-works" class="nm-link" data-menu-close>How It Works</a>
    <div class="nm-divider"></div>
    @auth
      <a href="/dashboard" class="nm-portal" data-menu-close>My Dashboard</a>
    @else
      <a href="/admin/login" class="nm-portal" data-menu-close>Portal</a>
      <a href="/admin/login" class="nm-signin" data-menu-close>Sign In</a>
    @endauth
  </div>
</div>

<!-- ════════════ HERO ════════════ -->
<section id="hero">
  <div class="hero-grid"></div>

  <div class="hero-stage">
    <h1 id="heroSeq" aria-label="Own your market. Capture your territory. Lock out competitors. One operator, one territory. Claim it before they do.">Own your<br>market.</h1>
  </div>
  <p class="hero-gold-accent">Your market. Your territory. One owner.</p>
  <p class="hero-sub">Full search coverage across every city you target — one business per territory, yours.</p>
  <p class="hero-note">One operator per market &mdash; select territories only.</p>
  <div class="hero-actions" style="opacity:0;animation:up .85s .52s forwards">
    <a href="#contact" class="btn-primary">Claim Your Territory</a>
    <a href="#offer" class="btn-ghost">Review the Structure</a>
  </div>

  <a href="#alloc" class="hero-scroll" aria-label="Scroll to next section">
    <div class="scroll-line"></div>
    <div class="scroll-caret"></div>
  </a>

</section>

<div class="gold-rule"></div>

<!-- ════════════ MARKET ALLOCATION ════════════ -->
<section id="alloc" class="alloc-section r">
  <div class="alloc-layout">

    <!-- Left: editorial copy -->
    <div class="alloc-copy">
      <p class="alloc-eyebrow">Market Allocation</p>
      <h2 class="alloc-hed">
        Market allocation<br>is active.<br>
        <em>Not every territory<br>is available.</em>
      </h2>
      <div class="alloc-sub">
        <p>Each market is assigned to a single operator.</p>
        <p>Once active, <em>access is exclusive.</em></p>
      </div>
      <div class="alloc-reinforce">
        <span>High-demand markets are secured first.</span>
        <span>No overlap. No secondary access.</span>
        <span>Coverage is held under agreement — reinforced, where appropriate, through paid media.</span>
      </div>
      <p class="alloc-urgency">If your market is still available,<br><em>secure it before the window closes.</em></p>
      <span class="alloc-convert-label">Access is reserved. Not open.</span>
      <div class="alloc-actions">
        <a href="/onboarding/start" class="btn-primary">Claim Your Territory</a>
        <a href="#how" class="btn-ghost">See How It Works</a>
      </div>
    </div>

    <!-- Right: regional allocation grid (data rendered from JS array below) -->
    <div class="alloc-panel">
      <p style="font-size:.68rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.82);margin-bottom:6px;font-weight:500">Live Territory Status</p>
      <p class="alloc-panel-label">U.S. Territory Status</p>
      <div class="alloc-grid" id="allocGrid"></div>
      <div class="alloc-legend">
        <div class="alloc-legend-item">
          <span class="alloc-dot allocated"></span>
          <span class="alloc-legend-label">Active</span>
        </div>
        <div class="alloc-legend-item">
          <span class="alloc-dot limited"></span>
          <span class="alloc-legend-label">Selective Access</span>
        </div>
        <div class="alloc-legend-item">
          <span class="alloc-dot open"></span>
          <span class="alloc-legend-label">Expansion Available</span>
        </div>
      </div>
      <p class="alloc-trust-line">Territory status is continuously evaluated across live search, competitive signals, and platform data.</p>
      <p class="alloc-avail-note"><strong>Territory access is structured by industry and market.</strong> Availability reflects current operator coverage — not total capacity.<br><br>Access is reviewed individually based on market availability, operator readiness, and business fit. Approval is reserved for legitimate operators with the capacity to support deployment and, where appropriate, media amplification. Search visibility is secured through structured systems and reinforced over time.</p>
    </div>

  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ ACCESS SECTION ════════════ -->
<section class="access-section r">

  <div class="access-eyebrow">The System</div>
  <h2 class="access-headline">Not a tool.<br><em>A territory.</em></h2>
  <p class="access-subline">One licensee per category, per market. Position held under agreement — unavailable to competitors while active.</p>

  <div class="access-grid">

    <div class="ac-card">
      <span class="ac-label">Controlled Access</span>
      <h3 class="ac-head">One Market.<br><em>One Agreement.</em></h3>
      <p class="ac-impact">Once held, competitors are excluded.</p>
      <p class="ac-body">No two businesses in the same vertical share the same licensed territory. This is not a subscription — it is a position held under contract.</p>
    </div>

    <div class="ac-card">
      <span class="ac-label">Full-Stack Visibility</span>
      <h3 class="ac-head">Every surface.<br><em>Every signal.</em></h3>
      <p class="ac-impact">Organic search, AI, emerging discovery — all layers.</p>
      <p class="ac-body">Every page is structured to surface across the full discovery stack — not just crawled, but understood, cited, and returned.</p>
    </div>

    <div class="ac-card">
      <span class="ac-label">Market Coverage</span>
      <h3 class="ac-head">A market claimed,<br><em>not a keyword.</em></h3>
      <p class="ac-impact">Complete coverage of your entire search surface.</p>
      <p class="ac-body">Every service. Every city. Every variation. Structured to compound as the system builds authority — not a campaign, a held position.</p>
    </div>

    <div class="ac-card">
      <span class="ac-label">Active Coverage</span>
      <h3 class="ac-head">Built to last.<br><em>Maintained to win.</em></h3>
      <p class="ac-impact">The system runs. You don’t manage it.</p>
      <p class="ac-body">Pages are maintained and adapted under your active agreement. Coverage is not static — it is actively defended.</p>
    </div>

  </div><!-- /tier-grid-3 -->

  <div class="offer-fomo r">
    <p class="offer-fomo-line">Availability contracts with every agreement signed.</p>
  </div>

</section>

<div class="gold-rule"></div>

<!-- ════════════ INFRASTRUCTURE PRINCIPLE ════════════ -->
<section class="infra-principle r">
  <canvas class="infra-canvas" id="infraCanvas" aria-hidden="true"></canvas>
  <div class="infra-inner">
    <p class="infra-eyebrow">Market Position</p>
    <h2 class="infra-hed">
      <span class="infra-hed-1">The territory will be owned.</span>
      <span class="infra-hed-2">The only question is by whom.</span>
    </h2>
    <p class="infra-gold">First to claim. First to rank.</p>
    <p class="infra-sub">Available ground closes without warning.</p>
    <span class="infra-rule"></span>
  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ URL DEMO — LICENSED SEARCH FOOTPRINT ════════════ -->
<div class="url-section">
  <div class="url-inner">
    <div>
      <p class="s-eye r">Licensed Search Footprint</p>
      <h2 class="s-h r">Every service.<br>Every city.<br><em>One structured system.</em></h2>
      <p class="s-p r">Each page targets a precise search intent — service, city, search variation — structured for organic discovery, AI citation, and every emerging layer. <strong>Structured coverage that compounds.</strong></p>
    </div>
    <div class="url-box r">
      <div class="url-box-label">Example Agency Deployment — Home Services</div>
      <div class="url-list">
        <div class="url-item"><div class="url-dot"></div>client.com/<span class="hl">hvac-repair-seattle-wa</span></div>
        <div class="url-item"><div class="url-dot"></div>client.com/<span class="hl">furnace-installation-bellevue-wa</span></div>
        <div class="url-item"><div class="url-dot"></div>client.com/<span class="hl">ac-service-tacoma-wa</span></div>
        <div class="url-item"><div class="url-dot"></div>client.com/<span class="hl">heat-pump-repair-renton-wa</span></div>
        <div class="url-item"><div class="url-dot"></div>client.com/<span class="hl">emergency-hvac-kent-wa</span></div>
        <div class="url-item"><div class="url-dot"></div>client.com/<span class="hl">air-duct-cleaning-redmond-wa</span></div>
        <div class="url-item"><div class="url-dot"></div>client.com/<span class="hl">boiler-service-kirkland-wa</span></div>
      </div>
      <div class="url-more">+ coverage across every service and location combination</div>
    </div>
  </div>
</div>

<!-- ════════════ AUDIENCE — SELL ════════════ -->
<section id="who">
  <div class="audience-section">
    <div class="stmt-quote r" style="text-align:center;max-width:820px;margin:0 auto 48px">
      <p class="sq-text">The first to claim the market, owns it.<br><strong>Premium territories are still available.</strong></p>
      <span class="sq-rule"></span>
    </div>
    <p class="s-eye r">Who This Is For</p>
    <h2 class="s-h r">Two paths in.<br><em>One outcome. Total market control.</em></h2>
    <div class="audience-grid">

      <div class="aud-card r">
        <span class="aud-tag">For Agencies</span>
        <h3 class="aud-title">Own search<br><em>for every client.</em></h3>
        <p class="aud-body">White-label deployment, entirely under your brand — complete search coverage across your client portfolio, exclusive by territory, with no third-party attribution anywhere.</p>
        <ul class="aud-list">
          <li><strong>Zero attribution</strong> — invisible to clients and competitors alike</li>
          <li><strong>One licence covers your full portfolio</strong></li>
          <li><strong>Exclusive territory per client</strong> — no overlap, no competition</li>
          <li><strong>Search presence is tied to your agreement</strong> — retention by design</li>
        </ul>
        <p style="font-size:.82rem;color:var(--muted);letter-spacing:.04em;margin-bottom:12px">Not ads. Not campaigns. Ownership.</p>
        <a href="#offer" class="aud-cta">Review Agency Licensing &rarr;</a>
      </div>

      <div class="aud-card r">
        <span class="aud-tag">For Operators &amp; Business Owners</span>
        <h3 class="aud-title">Take your market.<br><em>Before someone else does.</em></h3>
        <p class="aud-body">Full search coverage across every service, every city, every variation — structured, maintained, and held under your active agreement. <strong>A position you hold.</strong></p>
        <ul class="aud-list">
          <li><strong>Every service. Every city. Total coverage.</strong></li>
          <li><strong>Visible across organic, AI, and emerging search</strong></li>
          <li><strong>One operator per market</strong> — your category, exclusively yours</li>
          <li><strong>Locked in from day one</strong> — competitors cannot enter while you're active</li>
        </ul>
        <p style="font-size:.82rem;color:var(--muted);letter-spacing:.04em;margin-bottom:12px">Once claimed, your territory cannot be reallocated while active.</p>
        <a href="#offer" class="aud-cta">Assess Market Availability &rarr;</a>
      </div>

    </div>
  </div>
</section>

<!-- ════════════ WYL — FEATURES / WOW ════════════ -->
<section id="wyl">
  <div class="wyl-section">
    <div class="wyl-inner">
      <p class="s-eye r">What the Licence Includes</p>
      <h2 class="s-h r">The system behind<br><em>every page in your territory.</em></h2>
      <p class="s-p r" style="max-width:640px">Every service page, every location page — structured headlines, intelligent FAQs, localized data, internal link architecture. <strong>This is not a tool. This is what holds your position.</strong></p>
      <div class="wyl-grid">
        <div class="wyl-card r">
          <span class="wyl-icon">⬡</span>
          <h3 class="wyl-title">Precision Page Assembly</h3>
          <p class="wyl-desc">Every page is constructed through an automated pipeline — pairing a targeted headline, service-specific body, FAQ signal, and conversion elements to the exact search intent it targets. Zero manual composition.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◈</span>
          <h3 class="wyl-title">Topical Authority Architecture</h3>
          <p class="wyl-desc">Related services, adjacent cities, category hubs, and breadcrumb trails are woven into every page at build time — forming a structured link graph that signals topical authority and domain depth across search engines, AI systems, and language models.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">⊹</span>
          <h3 class="wyl-title">Structured Data Injection</h3>
          <p class="wyl-desc">Rich business markup, FAQ schema, and breadcrumb signals are generated and embedded on every page automatically — no manual entry, no missed signals for organic search, AI-generated answers, or language model citations.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◎</span>
          <h3 class="wyl-title">Location-Aware Content Generation</h3>
          <p class="wyl-desc">Each page is independently generated around its specific city and service combination — producing content that reads and ranks as locally distinct. Not a city-name swap. Genuinely differentiated output at scale.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">⬧</span>
          <h3 class="wyl-title">Semantic Cluster Architecture</h3>
          <p class="wyl-desc">Services, sub-services, and locations are mapped into topical clusters — building a coherent, authoritative site structure that positions your domain as the definitive resource in your niche.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◻</span>
          <h3 class="wyl-title">Crawl-Safe Rendering</h3>
          <p class="wyl-desc">Every page is delivered with clean URL structure, stable server-side output, and proper fallback logic — ensuring consistent, indexable delivery to every crawler and discovery system that reads the web.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◼</span>
          <h3 class="wyl-title">Zero-Attribution Deployment</h3>
          <p class="wyl-desc">Everything deploys under your brand identity. No third-party attribution in code, content, or metadata. Your clients see your agency's work. The platform behind it remains invisible.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◈</span>
          <h3 class="wyl-title">Capacity-Controlled Growth</h3>
          <p class="wyl-desc">Your licence defines a precise page inventory. Expansion is planned and incremental — protecting per-page quality and ensuring coverage scales without diluting the authority already earned.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ════════════ POSITIONING BLOCK ════════════ -->
<section class="pos-block">
  <div class="pos-block-inner">

    <!-- LEFT: text hierarchy -->
    <div class="pos-left">
      <h2 class="pos-h2 r">Claimed.<br><em>Not competed for daily.</em></h2>
      <div class="pos-rule r"></div>
      <p class="pos-support r">Structure does what effort cannot sustain.</p>
      <span class="pos-gold r">Every page. Every market. One operator.</span>
      <ul class="pos-proof r">
        <li>One operator per market.</li>
        <li>No overlap. No duplication.</li>
        <li>Once secured, it is held.</li>
      </ul>
      <span class="pos-close r">We don't compete. We own the ground.</span>
    </div>

    <!-- RIGHT: territory lock canvas -->
    <div class="pos-right r">
      <canvas class="pos-canvas" id="territoryCanvas" aria-hidden="true"></canvas>
    </div>

  </div>
</section>
<script>
(function(){
  var C=document.getElementById('territoryCanvas');
  if(!C)return;
  var ctx=C.getContext('2d');
  var DPR=window.devicePixelRatio||1;
  var COLS=10,ROWS=7;
  var W,H,cW,cH,nodes=[],lastClaim=0;
  var INTERVAL=1600;

  function resize(){
    var r=C.getBoundingClientRect();
    W=r.width;H=r.height;
    if(!W||!H)return;
    C.width=Math.round(W*DPR);C.height=Math.round(H*DPR);
    ctx.setTransform(DPR,0,0,DPR,0,0);
    cW=W/COLS;cH=H/ROWS;
    build();
  }

  function shuffle(a){return a.slice().sort(function(){return Math.random()-.5});}

  function build(){
    nodes=[];
    for(var ro=0;ro<ROWS;ro++){
      for(var co=0;co<COLS;co++){
        nodes.push({x:cW*(co+.5),y:cH*(ro+.5),ri:ro,ci:co,state:'n',prog:0});
      }
    }
    shuffle(nodes).slice(0,Math.floor(nodes.length*.32)).forEach(function(n){n.state='l';n.prog=1;});
  }

  function frame(ts){
    ctx.clearRect(0,0,W,H);

    // faint grid
    ctx.strokeStyle='rgba(200,168,75,.05)';ctx.lineWidth=.5;
    for(var c=0;c<=COLS;c++){ctx.beginPath();ctx.moveTo(cW*c,0);ctx.lineTo(cW*c,H);ctx.stroke();}
    for(var ro=0;ro<=ROWS;ro++){ctx.beginPath();ctx.moveTo(0,cH*ro);ctx.lineTo(W,cH*ro);ctx.stroke();}

    // connection lines between adjacent locked nodes
    for(var i=0;i<nodes.length;i++){
      var n=nodes[i];
      if(n.state!=='l')continue;
      var dirs=[[0,1],[1,0]];
      for(var d=0;d<dirs.length;d++){
        var dr=dirs[d][0],dc=dirs[d][1];
        for(var j=0;j<nodes.length;j++){
          var nb=nodes[j];
          if(nb.ri===n.ri+dr&&nb.ci===n.ci+dc&&nb.state==='l'){
            ctx.beginPath();ctx.moveTo(n.x,n.y);ctx.lineTo(nb.x,nb.y);
            ctx.strokeStyle='rgba(200,168,75,.07)';ctx.lineWidth=.5;ctx.stroke();
            break;
          }
        }
      }
    }

    // claim new node
    if(!lastClaim||ts-lastClaim>INTERVAL){
      var avail=nodes.filter(function(n){return n.state==='n';});
      if(avail.length){
        shuffle(avail)[0].state='a';
      } else {
        shuffle(nodes.filter(function(n){return n.state==='l';})).slice(0,Math.floor(nodes.length*.22)).forEach(function(n){n.state='n';n.prog=0;});
      }
      lastClaim=ts;
    }

    // draw nodes
    for(var k=0;k<nodes.length;k++){
      var n=nodes[k];
      if(n.state==='a'){
        n.prog=Math.min(1,n.prog+.009);
        var ring=cW*.38*(0.4+0.6*n.prog);
        var g=ctx.createRadialGradient(n.x,n.y,0,n.x,n.y,ring);
        g.addColorStop(0,'rgba(200,168,75,'+(0.18+0.18*n.prog)+')');
        g.addColorStop(1,'rgba(200,168,75,0)');
        ctx.fillStyle=g;ctx.beginPath();ctx.arc(n.x,n.y,ring,0,Math.PI*2);ctx.fill();
        ctx.fillStyle='rgba(200,168,75,'+(0.55+0.35*n.prog)+')';
        ctx.beginPath();ctx.arc(n.x,n.y,3,0,Math.PI*2);ctx.fill();
        if(n.prog>=1)n.state='l';
      } else if(n.state==='l'){
        var s=5;
        ctx.strokeStyle='rgba(200,168,75,.2)';ctx.lineWidth=.7;
        ctx.strokeRect(n.x-s,n.y-s,s*2,s*2);
        ctx.fillStyle='rgba(200,168,75,.48)';
        ctx.beginPath();ctx.arc(n.x,n.y,2.5,0,Math.PI*2);ctx.fill();
      } else {
        ctx.fillStyle='rgba(200,168,75,.07)';
        ctx.beginPath();ctx.arc(n.x,n.y,1.8,0,Math.PI*2);ctx.fill();
      }
    }

    requestAnimationFrame(frame);
  }

  function init(){
    resize();
    if(W&&H)requestAnimationFrame(frame);
  }

  // Defer until element is visible
  if('IntersectionObserver' in window){
    var obs=new IntersectionObserver(function(entries){
      if(entries[0].isIntersecting){init();obs.disconnect();}
    },{threshold:.1});
    obs.observe(C);
  } else {
    init();
  }

  window.addEventListener('resize',function(){
    resize();
  });
})();
</script>

<!-- ════════════ STEPS — PROCESS (TRANSITION) ════════════ -->
<section id="how">
  <div class="steps-section">
    <div class="steps-wrap">
      <p class="s-eye r">The Process</p>
      <h2 class="s-h r">Controlled onboarding.<br><em>Disciplined expansion.</em></h2>
      <div class="steps-grid">
        <div class="step r">
          <div class="step-n">01</div>
          <h3 class="step-title">Map Your Territory</h3>
          <p class="step-desc">Your full search territory is mapped — every service, every city, every topic variation — establishing the complete coverage architecture before a single page is deployed.</p>
        </div>
        <div class="step r">
          <div class="step-n">02</div>
          <h3 class="step-title">Set Up Your System</h3>
          <p class="step-desc">The system is configured to your brand: link architecture, localized content signals, and structured data — calibrated to your licensed territory before deployment begins.</p>
        </div>
        <div class="step r">
          <div class="step-n">03</div>
          <h3 class="step-title">Deploy Under Your Brand</h3>
          <p class="step-desc">Pages deploy across your licensed territory — white-label from top to bottom, no third-party branding on any client-facing page.</p>
        </div>
        <div class="step r">
          <div class="step-n">04</div>
          <h3 class="step-title">Expand Your Position</h3>
          <p class="step-desc">As your licensed position compounds, your agreement accommodates controlled expansion — new services, additional cities, deeper coverage — all within the same licensed agreement.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ════════════ LICENCE LOCK ════════════ -->
<section id="integrity">
<div class="url-lock">
  <div class="url-lock-inner">
    <div class="ul-head r">
      <p class="s-eye">The Licence Is the Product</p>
      <h2 class="ul-title">Your URLs are assets.<br><em>The licence is the key.</em></h2>
      <p class="ul-lead">Every page, every ranking, every lead — tied to one thing: your active licence.</p>
    </div>
    <div class="r">
      <div class="ul-states">
        <div class="ul-state active">
          <span class="ul-state-label">Active Licence</span>
          <div class="ul-state-title">Fully live — ranking, indexing, generating leads</div>
          <div class="ul-state-desc">Structured content, search signals, FAQ architecture, localized data, internal link structure, and page descriptions all active. Organic search, AI systems, and emerging discovery layers indexing your pages across every city under your agreement. <strong>Position compounds under active maintenance.</strong></div>
        </div>
        <div class="ul-state inactive">
          <span class="ul-state-label">Licence Lapsed</span>
          <div class="ul-state-title">Pages revert — company name &amp; phone number only</div>
          <div class="ul-state-desc">All structured content, search signals, and internal link architecture removed. Pages remain on your site, but <strong>search coverage is inactive. Position is no longer held.</strong> Reactivate the agreement to restore full coverage.</div>
        </div>
      </div>
      <div class="ul-note">
        <p><strong>Need more pages?</strong> Upgrade to the next tier — your existing pages carry over.</p>
        <p><strong>Already have pages built outside this platform?</strong> You can bring them under the licensed platform at the 10,000 page tier — full structured coverage, link architecture, and search signals across your entire footprint.</p>
      </div>
    </div>
  </div>
</div>
</section>

<!-- ════════════ LICENCE STATEMENT ════════════ -->
<section>
  <div class="licence-stmt-section">
    <p class="licence-stmt-principle r">Position is held under licence &mdash; not by default.</p>
    <div class="licence-stmt-body r">
      <p>Active licences protect your position.</p>
      <p>Unlicensed builds do not carry forward.</p>
    </div>
  </div>
  <p style="text-align:center;font-size:.7rem;color:rgba(168,168,160,.35);max-width:560px;margin:0 auto;padding:0 24px 32px;line-height:1.8">The SEO AI Co™ system combines structured content, local relevance, internal link architecture, search signals, and ongoing optimization &mdash; designed to strengthen every signal that drives local visibility and market dominance.</p>
</section>

<!-- ════════════ PRICING ════════════ -->
<section id="offer">
  <div class="ambient-network" aria-hidden="true">
    <canvas class="ambient-canvas" id="offerCanvas"></canvas>
    <div class="ambient-overlay"></div>
  </div>
  <div class="offer-intro r">
    <div>
      <p class="s-eye">Licensing Positions</p>
      <h2 class="s-h offer-hed-split">
        <span>Your market will be owned.</span>
        <span class="offer-hed-mid">The only question is:</span>
        <em>by you — or someone else.</em>
      </h2>
    </div>
    <div class="offer-panel">

      <!-- A. SCARCITY -->
      <div class="offer-scarcity">
        <p class="offer-scarcity-main">Territories are not open by default.</p>
        <p class="offer-scarcity-sub">Access is selective. Capacity is limited.</p>
      </div>

      <!-- B. VALUE -->
      <div class="offer-value">
        <span class="offer-value-price">Annual engagements range from $36K&ndash;$60K+.</span>
        <span class="offer-value-inline">Position.&ensp;Coverage.&ensp;Performance.</span>
        <span class="offer-value-media">Paid media and ad management are separate.</span>
      </div>

      <!-- C. FINAL POSITIONING -->
      <div class="offer-positioning">
        <p class="offer-positioning-bottom">Not a deliverable.<br>A position that is actively held.</p>
      </div>

    </div>
  </div>

  <div class="offer-guide r">
    <p class="offer-guide-line">Start with the market you need to own. Expand from there.</p>
  </div>

  <div class="tier-grid-3" id="tierGrid">

    <div class="tier starter">
      <span class="tier-flag">Entry — By Application</span>
      <h3 class="tier-name">Market Entry</h3>
      <div class="tier-urls">For approved operators entering a market selectively.</div>
      <ul class="tier-features">
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
          Search coverage across your assigned market
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          Single-operator exclusivity
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          Ongoing visibility management included
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          Upgradeable to Market Position or Expansion
        </li>
      </ul>
      <div class="tier-gated">
        <span class="tier-gated-icon">◈</span>
        <span><strong>Not publicly priced.</strong> Entry access is reviewed individually. Apply below.</span>
      </div>
      <a href="#contact" class="tier-cta">Apply for Entry Access</a>
      <button class="tier-book" onclick="window._bkPending={id:{{ $consultTypes->get('discovery')?->id ?? 1 }},duration:{{ $consultTypes->get('discovery')?->duration_minutes ?? 15 }},name:{{ json_encode($consultTypes->get('discovery')?->name ?? 'Free Discovery Call') }},isFree:{{ ($consultTypes->get('discovery')?->is_free ?? true) ? 'true' : 'false' }}};window.dispatchEvent(new CustomEvent('open-booking',{detail:window._bkPending}))">Book a Discovery Call</button>
    </div>

    <div class="tier focal">
      <span class="tier-flag">Most Selected</span>
      <h3 class="tier-name">Market Position</h3>
      <div class="tier-urls">For businesses securing full search coverage across a target market.</div>
      <ul class="tier-features">
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          <strong>Exclusive market</strong> — one operator, protected coverage
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
          Full search coverage across services and locations
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          <strong>Your brand only</strong> — zero attribution
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          Ongoing optimization and reporting included
        </li>
      </ul>
      <div class="tier-price"><sup>$</sup>2,995<sub>/mo</sub></div>
      <div class="tier-commitment">Structured 4-month deployment cycle.</div>
      <a href="#contact" class="tier-cta">Apply for Market Position</a>
      <button class="tier-book" onclick="window._bkPending={id:{{ $consultTypes->get('strategy')?->id ?? 2 }},duration:{{ $consultTypes->get('strategy')?->duration_minutes ?? 30 }},name:{{ json_encode($consultTypes->get('strategy')?->name ?? 'Strategy Call') }},isFree:{{ ($consultTypes->get('strategy')?->is_free ?? false) ? 'true' : 'false' }}};window.dispatchEvent(new CustomEvent('open-booking',{detail:window._bkPending}))">Book a Strategy Call</button>
    </div>

    <div class="tier prime">
      <span class="tier-flag">Full Scale</span>
      <h3 class="tier-name">Market Expansion</h3>
      <div class="tier-urls">For operators expanding coverage, scale, and protected reach.</div>
      <ul class="tier-features">
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          Everything in Market Position
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
          <strong>Expanded coverage</strong> — broader market footprint
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          Priority processing and dedicated support
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          For full-scale market ownership and expansion
        </li>
      </ul>
      <div class="tier-price"><sup>$</sup>4,799<sub>/mo</sub></div>
      <div class="tier-commitment">Priority processing. Structured 4-month deployment cycle.</div>
      <a href="#contact" class="tier-cta">Apply for Expansion Access</a>
      <button class="tier-book" onclick="window._bkPending={id:{{ $consultTypes->get('agency-review')?->id ?? $consultTypes->get('agency')?->id ?? 3 }},duration:{{ $consultTypes->get('agency-review')?->duration_minutes ?? $consultTypes->get('agency')?->duration_minutes ?? 60 }},name:{{ json_encode($consultTypes->get('agency-review')?->name ?? $consultTypes->get('agency')?->name ?? 'Agency Licence Review') }},isFree:{{ ($consultTypes->get('agency-review')?->is_free ?? $consultTypes->get('agency')?->is_free ?? false) ? 'true' : 'false' }}};window.dispatchEvent(new CustomEvent('open-booking',{detail:window._bkPending}))">Review My Agency Licence</button>
    </div>

  </div>
</section>
<script>
(function(){
  var grid=document.getElementById('tierGrid');
  if(!grid)return;
  var cards=grid.querySelectorAll('.tier');
  if('IntersectionObserver' in window){
    var io=new IntersectionObserver(function(entries){
      if(entries[0].isIntersecting){
        cards.forEach(function(c){c.classList.add('vis');});
        io.disconnect();
      }
    },{threshold:.12});
    io.observe(grid);
  } else {
    cards.forEach(function(c){c.classList.add('vis');});
  }
})();
</script>

<!-- ════════════ SETTLEMENT ════════════ -->
<div class="settlement">
  <div class="settlement-inner">
    <p class="s-eye r">Settlement</p>
    <h2 class="settle-hed r">Secure. Direct.<br><em>Flexible.</em></h2>

    <p class="settle-body r">Transactions can be completed via traditional payment methods or digital assets. USDC is supported for direct settlement. Stripe is available for standard invoicing and card payments.</p>
    <p class="settle-wallet-note r">Wallet-based settlement will be available within your dashboard upon onboarding.</p>

    <div class="settle-icons r">
      <div class="settle-icon-item" data-tip="Standard billing and invoicing">
        <div class="settle-icon-logo">
          <svg width="44" height="30" viewBox="0 0 44 30" fill="none">
            <rect x="9" y="5" width="26" height="20" rx="4" stroke="currentColor" stroke-width="1.2"/>
            <text x="22" y="20" font-size="11" text-anchor="middle" fill="currentColor" font-family="DM Sans,sans-serif" font-weight="300" letter-spacing="1">S</text>
          </svg>
        </div>
        <span class="settle-icon-label">Stripe</span>
      </div>
      <div class="settle-icon-item" data-tip="Preferred for direct settlement">
        <div class="settle-icon-logo">
          <svg width="44" height="30" viewBox="0 0 44 30" fill="none">
            <circle cx="22" cy="15" r="13" stroke="currentColor" stroke-width="1.2"/>
            <text x="22" y="20" font-size="9" text-anchor="middle" fill="currentColor" font-family="DM Sans,sans-serif" font-weight="400" letter-spacing="0">USDC</text>
          </svg>
        </div>
        <span class="settle-icon-label">USD Coin</span>
      </div>
      <div class="settle-icon-item" data-tip="Eligible for premium access tiers">
        <div class="settle-icon-logo">
          <svg width="44" height="30" viewBox="0 0 44 30" fill="none">
            <polygon points="22,3 36,15 22,20 8,15" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/>
            <polygon points="22,20 36,15 22,28 8,15" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" opacity=".6"/>
          </svg>
        </div>
        <span class="settle-icon-label">Ethereum</span>
      </div>
      <div class="settle-icon-item" data-tip="Accepted for settlement">
        <div class="settle-icon-logo">
          <svg width="44" height="30" viewBox="0 0 44 30" fill="none">
            <text x="22" y="22" font-size="18" text-anchor="middle" fill="currentColor" font-family="serif" font-weight="400">&#x20BF;</text>
          </svg>
        </div>
        <span class="settle-icon-label">Bitcoin</span>
      </div>
    </div>

    <div class="settle-trust r">
      <div class="settle-trust-item">
        <strong class="settle-trust-strong">Verified per agreement</strong>
        <p class="settle-trust-text">No automated billing. Structured engagement only.</p>
      </div>
      <div class="settle-trust-item">
        <strong class="settle-trust-strong">Access approved before payment</strong>
        <p class="settle-trust-text">Position is confirmed first. Not self-service.</p>
      </div>
    </div>
  </div>
</div>

<!-- ════════════ PLATFORM EXPANSION ════════════ -->
<div class="expansion">
  <div class="expansion-inner">
    <div class="exp-hed-block">
      <p class="s-eye r">Platform Expansion</p>
      <h2 class="s-h r">The system expands<br><em>with you.</em></h2>
      <span class="exp-gold-line r">Your position compounds.</span>
    </div>
    <div class="exp-grid" id="expGrid">
      <div class="exp-card">
        <span class="exp-dev-tag">In Development</span>
        <h3 class="exp-title">Agency Dashboard</h3>
        <p class="exp-punch">Full visibility. Real ownership.</p>
        <p class="exp-body">Manage deployment, track coverage, and oversee expansion across your licensed territory — every page, every market, one place.</p>
      </div>
      <div class="exp-card">
        <span class="exp-dev-tag">In Development</span>
        <h3 class="exp-title">Per-URL Search Tracking</h3>
        <p class="exp-punch">Know exactly where you stand.</p>
        <p class="exp-body">Track performance at the page level — visibility, indexing, and the compounding advantage of your position as it builds.</p>
      </div>
      <div class="exp-card">
        <span class="exp-dev-tag">In Development</span>
        <h3 class="exp-title">Reseller Sub-Licensing</h3>
        <p class="exp-punch">Expand under your brand.</p>
        <p class="exp-body">Bring clients into your system with structured access and scalable territory allocation. The advantage is yours to extend.</p>
      </div>
    </div>
    <div class="exp-footer r">
      <span class="exp-footer-line">New capabilities are deployed as the network expands.</span>
      <span class="exp-footer-accent">The advantage compounds over time.</span>
    </div>
    <div class="exp-momentum r">
      <p class="exp-momentum-main">Your position expands over time.<br><em>Without restarting. Without renegotiating.</em></p>
      <p class="exp-momentum-sub">New capabilities deploy continuously</p>
    </div>
  </div>
</div>
<script>
(function(){
  var grid=document.getElementById('expGrid');
  if(!grid)return;
  var cards=grid.querySelectorAll('.exp-card');
  if('IntersectionObserver' in window){
    var io=new IntersectionObserver(function(entries){
      if(entries[0].isIntersecting){
        cards.forEach(function(c){c.classList.add('vis');});
        io.disconnect();
      }
    },{threshold:.15});
    io.observe(grid);
  } else {
    cards.forEach(function(c){c.classList.add('vis');});
  }
})();
</script>

<!-- ════════════ TERRITORY PREVIEW ════════════ -->
<section id="preview" style="padding:56px 40px;text-align:center;border-top:1px solid rgba(200,168,75,.06)">
  <p style="font-family:'Cormorant Garamond',serif;font-size:clamp(1.7rem,4vw,2.5rem);font-weight:400;color:rgba(237,232,222,.95);margin-bottom:12px;line-height:1.2">Explore your territory first.</p>
  <p style="font-size:.84rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(200,168,75,.88);margin-bottom:20px;font-weight:400">See what's available in your territory.</p>
  <p style="font-size:.92rem;color:rgba(168,168,160,.88);max-width:480px;margin:0 auto 28px;line-height:1.8">Share your site and we'll map what's available in your territory — including gaps, missed coverage, and positions competitors may take if left open.</p>
  <a href="{{ route('onboarding.start') }}" style="display:inline-block;background:#c8a84b;color:#080808;font-family:'DM Sans',sans-serif;font-size:.82rem;font-weight:500;letter-spacing:.08em;text-transform:uppercase;padding:14px 36px;border-radius:4px;text-decoration:none;transition:background .2s,transform .15s" onmouseover="this.style.background='#e2c97d';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#c8a84b';this.style.transform='none'">Check Your Market Position &rarr;</a>
  <p style="font-size:.72rem;letter-spacing:.10em;text-transform:uppercase;color:rgba(200,168,75,.88);margin-top:14px;font-weight:400">Territories close as agreements are secured.</p>
</section>

<!-- ════════════ CONTACT ════════════ -->
<section id="contact">
  <div class="contact-inner">
    <div>
      <p class="s-eye r">Submit Market for Review</p>
      <h2 class="s-h r">Submit your market<br>for review.<br><em>Before it's claimed.</em></h2>
      <p class="s-p r">Access is not guaranteed. Availability is limited per territory. We assess your category, your market, and your operating context — then confirm whether access is available. Not automated. Not self-serve.</p>
      <p class="s-p r" style="font-family:'Cormorant Garamond',serif;font-style:italic;font-size:1.05rem;margin-top:8px;color:rgba(168,168,160,.68)">This is not a purchase decision. It is a position decision.</p>
      <div class="c-meta r">
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

      <p style="font-size:.82rem;color:var(--muted);text-align:center;letter-spacing:.04em;margin-bottom:8px">Markets are reviewed individually. Not all territories are available at time of application.</p>
      <button type="submit" class="fsub" id="submitBtn">Submit Market for Review</button>
    </form>
  </div>
</section>

<!-- ════════════ STICKY MOBILE CTA (mobile only — hidden on desktop via CSS) ════════════ -->
<div id="mobStickyCta" class="mob-sticky-cta" role="complementary" aria-label="Quick access — assess market availability">
  <div class="msc-inner">
    <a href="#contact" class="msc-primary">Check My Market</a>
    <a href="/book" class="msc-secondary">Book a Call</a>
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
    <span class="gate-badge">Licensed Access Required</span>
    <h2 class="gate-title">Secure your<br><em>position.</em></h2>
    <p class="gate-desc">Select a licence tier to activate your territory — <strong>structured pages, link architecture, and search signals deployed across your markets under a single agreement.</strong></p>
    <div class="gate-tiers">
      <div class="gate-tier" data-tier="5k">
        <div class="gate-tier-name">5,000 URLs</div>
        <div class="gate-tier-price">$2,995/mo</div>
        <div class="gate-tier-urls">Foundation licence</div>
      </div>
      <div class="gate-tier selected" data-tier="10k">
        <div class="gate-tier-name">10,000 URLs</div>
        <div class="gate-tier-price">$4,799/mo</div>
        <div class="gate-tier-urls">Preferred · Priority access</div>
      </div>
    </div>
    <a href="#contact" class="gate-cta" id="gateCta">Claim Your Territory</a>
    <button class="gate-skip" id="gateSkip">Continue browsing</button>
  </div>
</div>

<!-- ════════════ FOOTER — privacy/terms at very bottom ════════════ -->
<footer>
  <div class="footer-main">
    <a href="{{ url('/') }}" class="logo">
      <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
    </a>
    <span class="footer-copy">&copy; 2026 SEO AI Co™ &middot; Programmatic AI SEO Systems</span>
  </div>
  <p style="text-align:center;font-size:.72rem;color:var(--muted);margin:6px 0 4px">
    <a href="mailto:hello@seoaico.com" style="color:var(--muted);text-decoration:none">hello@seoaico.com</a>
  </p>
  <p style="text-align:center;font-size:.6rem;color:rgba(168,168,160,.28);max-width:540px;margin:0 auto 8px;line-height:1.65">SEO AI Co™ is a programmatic SEO and market intelligence system for operators competing in active markets. This platform maps local search visibility and identifies expansion opportunities.</p>
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
  </nav>
</footer>

<script>
  const nav = document.getElementById('nav');
  window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60));
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
  window.addEventListener('scroll', () => btt.classList.toggle('show', scrollY > 600), {passive:true});
  btt.addEventListener('click', () => window.scrollTo({top:0, behavior:'smooth'}));

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

  // ── Paywall Gate (placeholder) ──
  const gateOverlay = document.getElementById('gateOverlay');
  const gateSkip = document.getElementById('gateSkip');
  const gateCta = document.getElementById('gateCta');
  const gateTiers = document.querySelectorAll('.gate-tier');
  let gateShown = sessionStorage.getItem('seoai_gate_shown');

  // Show gate when user scrolls past pricing (soft gate — dismissible)
  if (!gateShown) {
    const offerSection = document.getElementById('offer');
    const gateIO = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting && !e.target.dataset.gateTriggered) {
          e.target.dataset.gateTriggered = '1';
          setTimeout(() => {
            gateOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            sessionStorage.setItem('seoai_gate_shown', '1');
          }, 1200);
          gateIO.unobserve(e.target);
        }
      });
    }, {threshold: 0.4});
    gateIO.observe(offerSection);
  }

  // Tier selection
  gateTiers.forEach(t => t.addEventListener('click', () => {
    gateTiers.forEach(x => x.classList.remove('selected'));
    t.classList.add('selected');
  }));

  // Close gate
  function closeGate() {
    gateOverlay.classList.remove('active');
    document.body.style.overflow = '';
  }
  gateSkip.addEventListener('click', closeGate);
  gateCta.addEventListener('click', closeGate);

  @if (session('inquiry_success'))
    document.getElementById('contact').scrollIntoView({behavior:'smooth'});
  @endif

  /* ── Hero sequence ── */
  (function(){
    var el = document.getElementById('heroSeq');
    if(!el) return;
    var headlines = [
      'Own your<br>market.',
      'Capture your<br>territory.',
      'Lock out<br>competitors.',
      'One operator.<br>One territory.',
      'Claim it<br>before they do.'
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
    var tooltips = {
      allocated:'This territory is currently secured by an operator.',
      limited:'Access is limited — subject to review and availability.',
      open:'New operators may be considered in this region.'
    };
    var grid = document.getElementById('allocGrid');
    if(!grid) return;
    grid.innerHTML = markets.map(function(m, i){
      return '<div class="alloc-cell">'
        + '<span class="alloc-cell-tooltip">' + tooltips[m.status] + '</span>'
        + '<p class="alloc-region">' + m.region + '</p>'
        + '<p class="alloc-states">' + m.states + '</p>'
        + '<div class="alloc-status">'
        +   '<span class="alloc-dot ' + m.status + '" data-delay="' + i + '"></span>'
        +   '<span class="alloc-status-label ' + m.status + '">' + labels[m.status] + '</span>'
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
      tick += 0.013;  /* slow, elegant sine cycle */
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
          n.x += n.vx * .58;  /* slow, smooth */
          n.y += n.vy * .58;
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

  /* ── Infrastructure Principle canvas ── */
  (function(){
    var canvas = document.getElementById('infraCanvas');
    if(!canvas) return;
    var ctx = canvas.getContext('2d');
    var nodes=[], raf, W, H;
    var COUNT=32, LINK=215, G='200,168,75';
    var reduced = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    var tick=0;

    function resize(){
      W = canvas.width  = canvas.offsetWidth;
      H = canvas.height = canvas.offsetHeight;
    }
    function init(){
      resize(); nodes=[];
      for(var i=0;i<COUNT;i++){
        nodes.push({
          x:Math.random()*W, y:Math.random()*H,
          vx:(Math.random()-.5)*.26, vy:(Math.random()-.5)*.26,
          r:Math.random()*1.6+.8,
          phase:Math.random()*Math.PI*2,      // per-node sine offset
          glowMult:Math.random()*.9+.55       // depth variation: 0.55–1.45
        });
      }
    }
    function frame(){
      ctx.clearRect(0,0,W,H);
      tick += 0.016;  // slightly faster sine cycle

      /* connection lines */
      for(var i=0;i<nodes.length;i++){
        for(var j=i+1;j<nodes.length;j++){
          var dx=nodes[j].x-nodes[i].x, dy=nodes[j].y-nodes[i].y;
          var d=Math.sqrt(dx*dx+dy*dy);
          if(d<LINK){
            ctx.beginPath();
            ctx.moveTo(nodes[i].x,nodes[i].y);
            ctx.lineTo(nodes[j].x,nodes[j].y);
            ctx.strokeStyle='rgba('+G+','+(1-d/LINK)*.26+')';
            ctx.lineWidth=.55;
            ctx.stroke();
          }
        }
      }

      /* nodes */
      for(var i=0;i<nodes.length;i++){
        var n = nodes[i];
        // breathing range 0.23–0.41, per-node phase so no two nodes sync
        var pulse = .32 + Math.sin(tick + n.phase) * .09;
        var glow  = n.glowMult;

        ctx.shadowBlur  = glow * 8;
        ctx.shadowColor = 'rgba('+G+','+(glow*.32).toFixed(2)+')';
        ctx.beginPath();
        ctx.arc(n.x, n.y, n.r, 0, Math.PI*2);
        ctx.fillStyle   = 'rgba('+G+','+pulse.toFixed(3)+')';
        ctx.fill();
        ctx.shadowBlur  = 0;

        if(!reduced){
          n.x += n.vx * .75;  // effective velocity ∼0.195
          n.y += n.vy * .75;
          if(n.x<0)n.x=W; if(n.x>W)n.x=0;
          if(n.y<0)n.y=H; if(n.y>H)n.y=0;
        }
      }

      raf = requestAnimationFrame(frame);
    }
    init();
    // use rAF unconditionally; reduced-motion nodes just don't move
    raf = requestAnimationFrame(frame);
    window.addEventListener('resize',function(){
      cancelAnimationFrame(raf); init();
      raf = requestAnimationFrame(frame);
    });
  })();

  /* ── Mobile slide-in panel ── */
  (function(){
    var btn      = document.getElementById('navHamburger');
    var menu     = document.getElementById('navMenu');
    var backdrop = document.getElementById('navBackdrop');
    if(!btn || !menu) return;

    function openMenu(){
      backdrop.classList.add('is-open');
      menu.classList.add('is-open');
      menu.removeAttribute('aria-hidden');
      backdrop.removeAttribute('aria-hidden');
      btn.classList.add('is-open');
      btn.setAttribute('aria-expanded','true');
      document.body.style.overflow = 'hidden';
    }
    function closeMenu(){
      menu.classList.remove('is-open');
      backdrop.classList.remove('is-open');
      btn.classList.remove('is-open');
      btn.setAttribute('aria-expanded','false');
      menu.setAttribute('aria-hidden','true');
      backdrop.setAttribute('aria-hidden','true');
      document.body.style.overflow = '';
    }
    function toggleMenu(){
      menu.classList.contains('is-open') ? closeMenu() : openMenu();
    }

    btn.addEventListener('click', function(e){
      e.stopPropagation();
      toggleMenu();
    });

    // Close on backdrop tap
    backdrop.addEventListener('click', closeMenu);

    // Close on Escape
    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape') closeMenu();
    });

    // Close on panel link tap
    menu.querySelectorAll('[data-menu-close]').forEach(function(el){
      el.addEventListener('click', closeMenu);
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

@include('components.booking-modal')

<script>
  if(typeof gtag==='function'){gtag('event','view_landing',{page_location:window.location.href});}
</script>
</body>
</html>
