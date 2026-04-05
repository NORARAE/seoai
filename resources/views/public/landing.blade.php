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
<meta name="description" content="SEO AI Co™ is programmatic search infrastructure—deploying structured content, internal link architecture, and structured data across 1,000+ U.S. cities. Position built through execution, not assigned. Authority that compounds.">
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
  background:radial-gradient(ellipse at center,rgba(200,168,75,.09) 0%,transparent 62%);
  animation:ambDriftA 28s ease-in-out infinite alternate;
  will-change:transform;
}
.amb-orb-b{
  width:min(55vw,700px);height:min(55vw,700px);
  border-radius:50%;
  background:radial-gradient(ellipse at center,rgba(200,168,75,.055) 0%,transparent 60%);
  animation:ambDriftB 38s ease-in-out infinite alternate;
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

/* ── Tier position line ── */
.tier-position{
  font-size:.78rem;letter-spacing:.04em;color:rgba(168,168,160,.48);
  line-height:1.6;font-style:italic;margin-bottom:20px;
  border-left:2px solid rgba(200,168,75,.18);padding-left:12px;
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
  transition:background .28s;
}
.exec-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.10),transparent);
}
.exec-card:hover{background:rgba(14,13,10,1)}
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
  padding:52px 64px;max-width:1200px;margin:0 auto;text-align:center;
  border-top:1px solid rgba(200,168,75,.08);
}
.pricing-cta-actions{display:flex;align-items:center;justify-content:center;gap:24px;margin-bottom:16px}
.pricing-cta-meta{font-size:.68rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(168,168,160,.30)}

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
.cn-body{
  font-size:.9rem;color:rgba(168,168,160,.78);line-height:1.88;
  max-width:560px;margin:0 auto;
}

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
  padding:160px 64px;text-align:center;
  border-top:1px solid rgba(200,168,75,.14);
}
.fcc::before{
  content:'';
  position:absolute;inset:0;
  background:radial-gradient(ellipse 82% 74% at 50% 46%,rgba(200,168,75,.17) 0%,rgba(200,168,75,.05) 52%,transparent 70%);
  pointer-events:none;
  animation:fccGlow 8s ease-in-out infinite;
  z-index:0;
}
.fcc::after{
  content:'';
  position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(200,168,75,.018) 1px,transparent 1px),
    linear-gradient(90deg,rgba(200,168,75,.018) 1px,transparent 1px);
  background-size:72px 72px;
  pointer-events:none;
  z-index:0;
}
@keyframes fccGlow{
  0%,100%{opacity:.72}
  50%{opacity:1}
}
.fcc-canvas{
  position:absolute;inset:0;width:100%;height:100%;
  pointer-events:none;z-index:0;
}
.fcc-inner{
  position:relative;z-index:1;
  max-width:1040px;margin:0 auto;
}
.fcc-eye{
  font-size:.63rem;letter-spacing:.38em;text-transform:uppercase;
  color:rgba(200,168,75,.58);margin-bottom:52px;
  display:flex;align-items:center;justify-content:center;gap:20px;
}
.fcc-eye::before,.fcc-eye::after{
  content:'';width:44px;height:1px;background:rgba(200,168,75,.2);
}
.fcc-hed{
  font-family:'Cormorant Garamond',serif;font-weight:200;
  line-height:1.08;margin-bottom:44px;
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
  line-height:1.85;margin-bottom:32px;
  max-width:560px;margin-left:auto;margin-right:auto;
}
.fcc-gold{
  font-family:'Cormorant Garamond',serif;font-weight:400;font-style:italic;
  font-size:clamp(1.4rem,2.4vw,2.0rem);
  letter-spacing:.01em;
  background:linear-gradient(90deg,var(--gold) 0%,rgba(245,228,152,.98) 44%,var(--gold) 62%,var(--gold) 100%);
  background-size:260% 100%;
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  background-clip:text;
  animation:fccGoldShimmer 10s ease-in-out infinite;
  margin-bottom:18px;
  display:block;
}
@keyframes fccGoldShimmer{
  0%,100%{background-position:120% 0}
  40%,60%{background-position:0% 0}
}
.fcc-micro{
  font-size:.70rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(168,168,160,.42);margin-bottom:56px;
}
.fcc-rule{
  display:block;width:64px;height:1px;margin:0 auto 48px;
  background:linear-gradient(90deg,transparent,rgba(200,168,75,.32),transparent);
}
.fcc-actions{
  display:flex;align-items:center;justify-content:center;gap:22px;
  flex-wrap:wrap;margin-bottom:22px;
}
.fcc-primary{
  display:inline-flex;align-items:center;
  background:var(--gold);color:#080808;
  font-family:'DM Sans',sans-serif;
  font-size:.82rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;
  padding:18px 44px;border-radius:6px;border:none;cursor:pointer;
  transition:background .3s,transform .2s,box-shadow .2s;
  min-height:56px;
}
.fcc-primary:hover{
  background:var(--gold-lt);transform:translateY(-2px);
  box-shadow:0 10px 32px rgba(200,168,75,.26);
}
.fcc-secondary{
  font-size:.80rem;letter-spacing:.14em;text-transform:uppercase;
  color:rgba(168,168,160,.65);text-decoration:none;
  border-bottom:1px solid rgba(168,168,160,.18);padding-bottom:2px;
  transition:color .2s,border-color .2s;
}
.fcc-secondary:hover{color:var(--gold);border-color:rgba(200,168,75,.42)}
.fcc-reassure{
  font-size:.70rem;letter-spacing:.14em;text-transform:uppercase;
  color:rgba(168,168,160,.35);
}

/* ── How-this-works trust strip ── */
.how-strip{padding:36px 64px;max-width:1200px;margin:0 auto;border-top:1px solid rgba(200,168,75,.06);text-align:center}
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
.how-strip-sub{font-size:.72rem;color:rgba(168,168,160,.36);letter-spacing:.08em;text-transform:uppercase}

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
.va-close em{color:var(--gold);font-style:italic}

/* ── Competitive positioning ── */
.comp-pos{padding:28px 64px;max-width:1200px;margin:0 auto;text-align:center;border-top:1px solid rgba(200,168,75,.05)}
.cp-line-1{font-size:.88rem;color:rgba(168,168,160,.50);letter-spacing:.02em;margin-bottom:6px}
.cp-line-2{font-family:'Cormorant Garamond',serif;font-size:clamp(1.1rem,1.9vw,1.45rem);font-weight:300;color:var(--ivory);letter-spacing:-.01em}

/* ── Service support block ── */
.svc-support{padding:56px 64px;max-width:1200px;margin:0 auto;border-top:1px solid var(--border)}
.ss-eye{font-size:.68rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(200,168,75,.55);margin-bottom:12px}
.ss-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.4rem,2.2vw,1.9rem);font-weight:300;color:var(--ivory);margin-bottom:14px;line-height:1.2}
.ss-intro{font-size:.92rem;color:rgba(168,168,160,.68);line-height:1.78;max-width:580px;margin-bottom:28px}
.ss-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:10px 24px;margin-bottom:24px}
.ss-item{display:flex;align-items:flex-start;gap:10px;padding:10px 14px;background:rgba(200,168,75,.015);border:1px solid rgba(200,168,75,.07);border-radius:4px}
.ss-dot{width:4px;height:4px;border-radius:50%;background:rgba(200,168,75,.5);flex-shrink:0;margin-top:6px}
.ss-label{font-size:.84rem;color:rgba(168,168,160,.78);line-height:1.5}
.ss-note{font-size:.80rem;color:rgba(168,168,160,.45);font-style:italic;line-height:1.7;max-width:600px;border-top:1px solid rgba(200,168,75,.06);padding-top:16px}

/* ── fcc wait line ── */
.fcc-wait{font-size:.84rem;color:rgba(168,168,160,.36);line-height:1.9;margin-top:14px;letter-spacing:.01em}
.fcc-wait em{color:rgba(200,168,75,.52);font-style:italic}

/* ── Responsive pricing/services ── */
@media(max-width:900px){
  .value-anchor,.exec-services,.access-position,.access-model,.decision-guide,.pricing-cta,.final-close{padding:48px 24px}
  .fcc{padding:100px 32px}
  .fcc-hed-1,.fcc-hed-2{font-size:clamp(1.9rem,5.2vw,2.8rem)}
  .commitment-note{padding:40px 28px 36px}
  .fit-screen{padding:40px 24px}
  .exec-grid{grid-template-columns:1fr}
  .access-position{grid-template-columns:1fr;gap:28px}
}
@media(max-width:520px){
  .value-anchor,.exec-services,.access-position,.access-model,.decision-guide,.pricing-cta,.final-close{padding:36px 20px}
  .fcc{padding:72px 20px}
  .fcc-hed-1,.fcc-hed-2{white-space:normal}
  .fcc-hed-1{font-size:clamp(1.65rem,7.8vw,2.4rem)}
  .fcc-hed-2{font-size:clamp(1.45rem,6.8vw,2.1rem)}
  .commitment-note{padding:32px 20px 28px}
  .fit-screen{padding:32px 20px}
  .fcc-actions{flex-direction:column;align-items:stretch}
  .fcc-primary{justify-content:center;width:100%}
  .fcc-secondary{text-align:center}
  .dg-row{flex-wrap:wrap;gap:8px}
  .dg-tier{text-align:left;min-width:auto}
  .dg-if{width:auto}
  .pricing-cta-actions{flex-direction:column;gap:16px}
  .pricing-cta-actions .btn-primary{width:100%;text-align:center;justify-content:center}
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
  margin-bottom:32px;
}
.hero-note{
  font-size:.72rem;letter-spacing:.24em;text-transform:uppercase;
  color:rgba(200,168,75,.44);
  opacity:0;animation:up .8s .54s forwards;
  margin-top:4px;margin-bottom:32px;
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

/* ── System Structure section ── */
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
  font-size:.78rem;letter-spacing:.12em;text-transform:uppercase;
  color:rgba(200,168,75,.72);margin-bottom:16px;line-height:1.6;
}
.sys-clarity{
  font-size:.88rem;line-height:1.72;color:rgba(237,232,222,.42);
  margin-bottom:14px;max-width:460px;
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
}
.infra-canvas{
  position:absolute;inset:0;width:100%;height:100%;
  pointer-events:none;z-index:0;
}
/* breathing radial glow */
.infra-principle::before{
  content:'';
  position:absolute;inset:0;
  background:radial-gradient(ellipse 72% 68% at 50% 50%,rgba(200,168,75,.13) 0%,transparent 68%);
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
  0%,100%{opacity:.74}
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
  display:flex;flex-direction:column;gap:.22em;
}
.infra-hed-1{
  font-size:clamp(3rem,5.8vw,5.6rem);
  color:var(--ivory);letter-spacing:-.018em;font-weight:300;
}
.infra-hed-2{
  font-size:clamp(2.8rem,5.2vw,5.0rem);
  color:rgba(237,232,222,.52);letter-spacing:-.018em;font-style:italic;
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
  .infra-hed-1,.infra-hed-2{font-size:clamp(1.8rem,4.5vw,2.6rem)}
  .settlement{padding:36px 24px}
  .settle-trust{grid-template-columns:1fr}
  .settle-icons{gap:20px}
  .alloc-decision{padding:28px 24px;max-width:100%}
  .sys-struct{padding:52px 24px}
  .sys-struct-inner{grid-template-columns:1fr;gap:32px}
  .sys-city-grid{grid-template-columns:1fr 1fr}
}
@media(max-width:520px){
  .infra-principle{padding:72px 20px}
  .infra-hed-1,.infra-hed-2{font-size:clamp(1.45rem,6.8vw,2.1rem)}
  .infra-gold{font-size:clamp(1.6rem,5.8vw,2.2rem)}
  .settlement{padding:28px 20px}
  .hero-stage{font-size:clamp(3.2rem,11vw,5rem)}
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
  .hero-gold-accent{margin-bottom:22px;line-height:1.3}
  .hero-note{margin-bottom:22px}
  .hero-diff{font-size:.82rem;margin-bottom:20px}
  .hero-cities{font-size:.96rem;margin-bottom:24px;letter-spacing:.09em;padding-left:14px}
  .hero-cta-note{font-size:.64rem;letter-spacing:.12em}
  .hero-net{opacity:.11}
  .hero-scroll-label{font-size:.60rem;letter-spacing:.16em}
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
      "description": "AI-powered programmatic SEO infrastructure for local service businesses. Hyper-local, structured, and built for AI and organic search.",
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
      "name": "Programmatic SEO Infrastructure",
      "serviceType": "SEO",
      "provider": { "@id": "https://seoaico.com/#org" },
      "description": "AI-powered programmatic SEO for local service businesses. Hyper-local URL architecture, structured data, and internal link systems deployed at scale.",
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
<nav id="nav">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <div class="nav-right">
    <a href="/how-it-works" class="nav-link">How It Works</a>
    <a href="/book" class="nav-btn nav-book">Book</a>
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
    <a href="{{ route('onboarding.start') }}" class="nm-link nm-featured" data-menu-close>Check Availability</a>
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
    <h1 id="heroSeq" aria-label="Your market. Your territory. Every city, every service. Programmatic expansion. Maximum coverage. Built for search and AI discovery.">Your market.<br>Your territory.</h1>
  </div>
  <p class="hero-gold-accent">AI-powered hyper-local market expansion across every city in your market.</p>
  <p class="hero-cities"><span>Every Service.</span><span>Every City.</span><span>Every Neighborhood.</span></p>
  <p class="hero-diff">Programmatic URL expansion — expanding your visibility across every service and every city.</p>
  <p class="hero-note">Position early. Expand everywhere.</p>
  <div class="hero-actions" style="opacity:0;animation:up .85s .52s forwards">
    <a href="#" class="btn-primary js-open-gate">Assess Your Market</a>
  </div>
  <p class="hero-cta-note">Signal-aware. Continuously adapting.</p>

</section>

<div class="hero-transition" aria-hidden="true">
  <span class="hero-scroll-label">See how your market expands</span>
  <div class="hero-rule-shimmer"></div>
  <div class="hero-scroll-arrow"></div>
</div>

<!-- ════════════ HOW THIS WORKS ════════════ -->
<section class="how-strip r" aria-label="How the system works">
  <p class="how-strip-hed">How your market expansion is built</p>
  <div class="how-strip-bullets">
    <div class="how-strip-item">
      <span class="how-strip-dot" aria-hidden="true"></span>
      <span class="how-strip-label">Market mapping with live search data</span>
    </div>
    <div class="how-strip-item">
      <span class="how-strip-dot" aria-hidden="true"></span>
      <span class="how-strip-label">Programmatic pages across every service &amp; city</span>
    </div>
    <div class="how-strip-item">
      <span class="how-strip-dot" aria-hidden="true"></span>
      <span class="how-strip-label">AI signals, schema, and search infrastructure built in</span>
    </div>
    <div class="how-strip-item">
      <span class="how-strip-dot" aria-hidden="true"></span>
      <span class="how-strip-label">Continuous optimization as algorithms evolve</span>
    </div>
    <div class="how-strip-item">
      <span class="how-strip-dot" aria-hidden="true"></span>
      <span class="how-strip-label">First-mover position — authority built through structure</span>
    </div>
  </div>
  <p class="how-strip-sub">A full-system expansion — not a tool, not a shortcut.</p>
</section>

<!-- ════════════ SYSTEM STRUCTURE ════════════ -->
<section class="sys-struct r">
  <div class="sys-struct-inner">

    {{-- Left: domain expansion copy --}}
    <div>
      <p class="sys-eyebrow">Your Domain. Expanded.</p>
      <h2 class="sys-hed">This is how your<br>domain takes position.<br><em>Built on your URL. Expanded across your market.</em></h2>
      <p class="sys-gold-line">Every service. Every city. Every search that matters.</p>
      <p class="sys-sub">We build structured, hyper-local pages on your domain — so you show up where your competitors don’t.</p>
      <p class="sys-clarity">We do not replace your existing website. We expand it — increasing visibility, reach, and authority.</p>
      <p class="sys-trust">Works with your existing site. WordPress (including Divi) fully supported.</p>
    </div>

    {{-- Right: URL expansion card grid --}}
    <div>
      <div class="sys-city-grid r">

        <div class="sys-city-card">
          <div class="sys-city-name">Personal Injury Law</div>
          <div class="sys-domain">client.com</div>
          <ul class="sys-url-list">
            <li class="sys-url-item">/personal-injury-lawyer-<span class="sys-url-loc">seattle</span></li>
            <li class="sys-url-item">/car-accident-lawyer-<span class="sys-url-loc">seattle</span></li>
            <li class="sys-url-item">/personal-injury-lawyer-<span class="sys-url-loc">alki</span></li>
          </ul>
        </div>

        <div class="sys-city-card">
          <div class="sys-city-name">Biohazard Cleanup</div>
          <div class="sys-domain">client.com</div>
          <ul class="sys-url-list">
            <li class="sys-url-item">/biohazard-cleanup-<span class="sys-url-loc">olympia</span></li>
            <li class="sys-url-item">/crime-scene-cleanup-<span class="sys-url-loc">lacey</span></li>
            <li class="sys-url-item">/unattended-death-cleanup-<span class="sys-url-loc">tumwater</span></li>
          </ul>
        </div>

        <div class="sys-city-card">
          <div class="sys-city-name">HVAC Repair</div>
          <div class="sys-domain">client.com</div>
          <ul class="sys-url-list">
            <li class="sys-url-item">/hvac-repair-<span class="sys-url-loc">bellevue</span></li>
            <li class="sys-url-item">/ac-installation-<span class="sys-url-loc">redmond</span></li>
            <li class="sys-url-item">/furnace-repair-<span class="sys-url-loc">kirkland</span></li>
          </ul>
        </div>

        <div class="sys-city-card">
          <div class="sys-city-name">Emergency Plumbing</div>
          <div class="sys-domain">client.com</div>
          <ul class="sys-url-list">
            <li class="sys-url-item">/emergency-plumber-<span class="sys-url-loc">tacoma</span></li>
            <li class="sys-url-item">/water-heater-repair-<span class="sys-url-loc">lakewood</span></li>
            <li class="sys-url-item">/burst-pipe-repair-<span class="sys-url-loc">puyallup</span></li>
          </ul>
        </div>

        <div class="sys-city-card">
          <div class="sys-city-name">Cosmetic Dentistry</div>
          <div class="sys-domain">client.com</div>
          <ul class="sys-url-list">
            <li class="sys-url-item">/cosmetic-dentist-<span class="sys-url-loc">portland</span></li>
            <li class="sys-url-item">/teeth-whitening-<span class="sys-url-loc">beaverton</span></li>
            <li class="sys-url-item">/dental-implants-<span class="sys-url-loc">hillsboro</span></li>
          </ul>
        </div>

        <div class="sys-city-card">
          <div class="sys-city-name">Roofing</div>
          <div class="sys-domain">client.com</div>
          <ul class="sys-url-list">
            <li class="sys-url-item">/roof-replacement-<span class="sys-url-loc">denver</span></li>
            <li class="sys-url-item">/roof-repair-<span class="sys-url-loc">aurora</span></li>
            <li class="sys-url-item">/emergency-roofing-<span class="sys-url-loc">lakewood</span></li>
          </ul>
        </div>

      </div>
      <p class="sys-city-foot r">+ every service, every city, every neighborhood — built on your domain</p>
      <p class="sys-city-clarity r">All pages are built on your domain — expanding your visibility across your entire market.</p>
    </div>

  </div>
</section>

<div class="gold-rule"></div>

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
          <p class="step-desc">Every service, every city, every topic — your complete coverage architecture is mapped before a single page deploys.</p>
        </div>
        <div class="step r">
          <div class="step-n">02</div>
          <h3 class="step-title">Set Up Your System</h3>
          <p class="step-desc">Link architecture, localized signals, and structured data — configured to your brand before deployment begins.</p>
        </div>
        <div class="step r">
          <div class="step-n">03</div>
          <h3 class="step-title">Deploy Under Your Brand</h3>
          <p class="step-desc">Pages deploy under your brand, across every service and city in your market — no third-party branding on any client-facing page.</p>
        </div>
        <div class="step r">
          <div class="step-n">04</div>
          <h3 class="step-title">Expand Your Position</h3>
          <p class="step-desc">As your position compounds, your agreement accommodates expansion — new services, cities, and deeper coverage without renegotiation.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ════════════ COMMITMENT NOTE ════════════ -->
<div class="commitment-note r">
  <p class="cn-hed">This is a 4-month structured expansion process —<br>not a one-week experiment.</p>
  <p class="cn-body">Most market builds require a focused 4-month rollout window to map, configure, deploy, and compound correctly. That structure is what makes results durable — and why we structure access carefully.</p>
</div>

<!-- ════════════ MARKET ALLOCATION ════════════ -->
<section id="alloc" class="alloc-section r">
  <div class="alloc-layout">

    <!-- Left: editorial copy -->
    <div class="alloc-copy">
      <p class="alloc-eyebrow">Market Status Overview</p>
      <h2 class="alloc-hed">
        Where expansion<br>is active, limited,<br>
        <em>and opening.</em>
      </h2>
      <div class="alloc-sub">
        <p>Each region reflects real-time rollout status — driven by demand, competition, and phased expansion sequencing.</p>
        <p><em>Structured market positioning at scale. Strategic expansion phases.</em></p>
      </div>
      <p class="alloc-urgency">Position established early compounds. <em>Structure your market as phases open.</em></p>
      <span class="alloc-convert-label">Selective access. Launch-ready markets only.</span>
      <div class="alloc-actions">
        <a href="#" class="btn-primary js-open-gate">Assess Your Market</a>
      </div>
    </div>

    <!-- Right: regional allocation grid (data rendered from JS array below) -->
    <div class="alloc-panel">
      <p style="font-size:.68rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.82);margin-bottom:6px;font-weight:500">Live Market Status</p>
      <p class="alloc-panel-label">U.S. Market Coverage</p>
      <div class="alloc-grid" id="allocGrid"></div>
      <div class="alloc-legend">
        <div class="alloc-legend-item">
          <span class="alloc-dot allocated"></span>
          <div class="alloc-legend-text">
            <span class="alloc-legend-label">Active</span>
            <span class="alloc-legend-desc">Established and expanding</span>
          </div>
        </div>
        <div class="alloc-legend-item">
          <span class="alloc-dot limited"></span>
          <div class="alloc-legend-text">
            <span class="alloc-legend-label">Selective Access</span>
            <span class="alloc-legend-desc">Limited strategic entry</span>
          </div>
        </div>
        <div class="alloc-legend-item">
          <span class="alloc-dot open"></span>
          <div class="alloc-legend-text">
            <span class="alloc-legend-label">Expansion Available</span>
            <span class="alloc-legend-desc">Open for structured rollout</span>
          </div>
        </div>
      </div>
      <p class="alloc-trust-line">These regions show where access is active, limited, or currently available for expansion. Status reflects live search coverage and market positioning.</p>
      <p class="alloc-avail-note"><strong>AI-guided deployment. Position built through structured coverage.</strong> Each position covers structured service and location pages — built for organic search, AI-assisted visibility, and LLM-aware discovery across your full territory.<br><br>Access is reviewed individually based on market availability and strategic fit. Search presence is established through structured systems and expanded over time.</p>
    </div>

  </div>
</section>

<div class="gold-rule"></div>

<!-- ════════════ ACCESS SECTION ════════════ -->
<section class="access-section r">

  <div class="access-eyebrow">The System</div>
  <h2 class="access-headline">Not a tool.<br><em>A system.</em></h2>
  <p class="access-subline">Your position is built — not assigned.</p>

  <div class="access-grid">

    <div class="ac-card">
      <span class="ac-label">Controlled Access</span>
      <h3 class="ac-head">Built on structure.<br><em>Sustained by execution.</em></h3>
      <p class="ac-impact">The advantage compounds with every deployment.</p>
      <p class="ac-body">The advantage comes from structured expansion, domain authority, and continuous coverage. Businesses that move first build stronger signals, expand faster, and become harder to displace over time.</p>
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
    <p class="offer-fomo-line">Position is not reserved. It is built — and held — through execution.</p>
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

<!-- ════════════ AUDIENCE — SELL ════════════ -->
<section id="who">
  <div class="audience-section">
    <p class="s-eye r">Who This Is For</p>
    <h2 class="s-h r">Two paths in.<br><em>One outcome. Total market control.</em></h2>
    <div class="audience-grid">

      <div class="aud-card r">
        <span class="aud-tag">For Agencies</span>
        <h3 class="aud-title">Own search<br><em>for every client.</em></h3>
        <p class="aud-body">White-label deployment, entirely under your brand — complete market coverage across your client portfolio, with no third-party attribution anywhere.</p>
        <ul class="aud-list">
          <li><strong>Zero attribution</strong> — invisible to clients and competitors alike</li>
          <li><strong>Full portfolio coverage under one agreement</strong></li>
          <li><strong>Structured coverage per client</strong> — depth that builds retention</li>
          <li><strong>Search presence is tied to your agreement</strong> — retention by design</li>
        </ul>
        <p style="font-size:.82rem;color:var(--muted);letter-spacing:.04em;margin-bottom:12px">Not ads. Not campaigns. Ownership.</p>
        <a href="#offer" class="aud-cta">Review Agency Deployment &rarr;</a>
      </div>

      <div class="aud-card r">
        <span class="aud-tag">For Operators &amp; Business Owners</span>
        <h3 class="aud-title">Take your market.<br><em>Before someone else does.</em></h3>
        <p class="aud-body">Full search coverage across every service, every city, every variation — structured, maintained, and held under your active agreement. <strong>A position you hold.</strong></p>
        <ul class="aud-list">
          <li><strong>Every service. Every city. Total coverage.</strong></li>
          <li><strong>Visible across organic, AI, and emerging search</strong></li>
          <li><strong>Move first, compound faster</strong> — early coverage builds stronger authority</li>
          <li><strong>Harder to displace over time</strong> — position that strengthens with every deployment</li>
        </ul>
        <p style="font-size:.82rem;color:var(--muted);letter-spacing:.04em;margin-bottom:12px">Position built first holds longest. The system compounds in your favor.</p>
        <a href="#offer" class="aud-cta">Assess Market Availability &rarr;</a>
      </div>

    </div>
  </div>
</section>

<!-- ════════════ WYL — FEATURES / WOW ════════════ -->
<section id="wyl">
  <div class="wyl-section">
    <div class="wyl-inner">
      <p class="s-eye r">What Your Deployment Delivers</p>
      <h2 class="s-h r">The system behind<br><em>every page, every market.</em></h2>
      <p class="s-p r" style="max-width:640px">Every result this system produces comes from structure — not effort. <strong>This is what takes and keeps your market position.</strong></p>
      <div class="wyl-grid">
        <div class="wyl-card r">
          <span class="wyl-icon">⬡</span>
          <h3 class="wyl-title">You show up everywhere.</h3>
          <p class="wyl-desc">Every service, every city — structured pages built to show up before your competitors do.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◈</span>
          <h3 class="wyl-title">Built on your domain.</h3>
          <p class="wyl-desc">Expands your own URL footprint — domain authority you own, built from your existing site outward.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">⊹</span>
          <h3 class="wyl-title">Visible to AI systems.</h3>
          <p class="wyl-desc">Structured to surface in AI, search engines, and every discovery layer that indexes the web.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◎</span>
          <h3 class="wyl-title">Position compounds over time.</h3>
          <p class="wyl-desc">Every month you're live, your position deepens — harder for competitors to displace, compounding automatically.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">⬧</span>
          <h3 class="wyl-title">Coverage they can't replicate.</h3>
          <p class="wyl-desc">A hyper-local coverage structure competitors cannot quickly build, match, or replicate.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◼</span>
          <h3 class="wyl-title">The system runs itself.</h3>
          <p class="wyl-desc">Deployed, held, and expanded for you — no dashboards, no management overhead required.</p>
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
      <h2 class="pos-h2 r">Built first.<br><em>Held through execution.</em></h2>
      <div class="pos-rule r"></div>
      <p class="pos-support r">Structure does what effort cannot sustain.</p>
      <span class="pos-gold r">Every page. Every market. Every signal.</span>
      <ul class="pos-proof r">
        <li>Position built through structured coverage.</li>
        <li>Authority that compounds with every deployment.</li>
        <li>Harder to displace with every passing month.</li>
      </ul>
      <span class="pos-close r">Position is not reserved. It is built — and held — through execution.</span>
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

<!-- ════════════ PRICING ════════════ -->
<section id="offer">
  <div class="ambient-network" aria-hidden="true">
    <canvas class="ambient-canvas" id="offerCanvas"></canvas>
    <div class="ambient-overlay"></div>
  </div>
  <div class="offer-intro r">
    <div>
      <p class="s-eye">Market Access Plans</p>
      <h2 class="s-h offer-hed-split">
        <span>Access is structured by level</span>
        <span class="offer-hed-mid">of expansion —</span>
        <em>not generic packages.</em>
      </h2>
    </div>
    <div class="offer-panel">

      <!-- A. SCARCITY -->
      <div class="offer-scarcity">
        <p class="offer-scarcity-main">Capacity is selective. Access is guided.</p>
        <p class="offer-scarcity-sub">Market position is confirmed before activation.</p>
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
        <p style="font-size:.78rem;color:rgba(168,168,160,.48);letter-spacing:.04em;margin-top:14px;line-height:1.68">Anyone can create pages.<br>Very few can make them rank &mdash; and fewer still can hold that position over time.</p>
      </div>

    </div>
  </div>

  <div class="offer-guide r">
    <p class="offer-guide-line">Select the level that matches your goal.</p>
    <p style="font-size:.86rem;color:rgba(168,168,160,.65);text-align:center;margin-top:12px;letter-spacing:.02em">Access begins with onboarding.<br>We review your market, confirm availability, and activate your system with you.</p>
    <p style="font-size:.76rem;color:rgba(168,168,160,.38);text-align:center;margin-top:10px;letter-spacing:.03em;font-style:italic">Your expansion system is maintained under an active licensing agreement.</p>
  </div>

  <div class="tier-grid-3" id="tierGrid">

    <div class="tier starter">
      <span class="tier-flag">Market Launch Access</span>
      <h3 class="tier-name">Launch</h3>
      <p class="tier-position">The structured entry path — establish your first market position.</p>
      <ul class="tier-features">
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
          Initial system deployment
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          Structured service + location rollout
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          AI-driven setup and foundation
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          Upgradeable as your market grows
        </li>
      </ul>
      <div class="tier-gated">
        <span class="tier-gated-icon">◈</span>
        <span><strong>Access reviewed individually.</strong> Apply below to confirm availability.</span>
      </div>
      <a href="{{ route('onboarding.start', ['tier' => 'launch']) }}" class="tier-cta">Start Launch Setup</a>
      <button class="tier-book" onclick="window._bkPending={id:{{ $consultTypes->get('discovery')?->id ?? 1 }},duration:{{ $consultTypes->get('discovery')?->duration_minutes ?? 15 }},name:{{ json_encode($consultTypes->get('discovery')?->name ?? 'Free Discovery Call') }},isFree:{{ ($consultTypes->get('discovery')?->is_free ?? true) ? 'true' : 'false' }}};window.dispatchEvent(new CustomEvent('open-booking',{detail:window._bkPending}));if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'pricing_section',cta_label:'book_discovery_call'});">Book a Discovery Call</button>
    </div>

    <div class="tier focal">
      <span class="tier-flag">Market Expansion Access</span>
      <h3 class="tier-name">Expansion</h3>
      <p class="tier-position">The standard growth path — real coverage, real expansion.</p>
      <ul class="tier-features">
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          <strong>Expanded service and city coverage</strong>
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
          Continuous rollout and optimization
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          <strong>Competitive reinforcement</strong> — active position held
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          Ongoing reporting and performance tracking
        </li>
      </ul>
      <div class="tier-price"><sup>$</sup>2,995<sub>/mo</sub></div>
      <div class="tier-commitment">Structured 4-month deployment cycle.</div>
      <a href="{{ route('onboarding.start', ['tier' => 'expansion']) }}" class="tier-cta">Start Expansion Planning</a>
      <button class="tier-book" onclick="window._bkPending={id:{{ $consultTypes->get('strategy')?->id ?? 2 }},duration:{{ $consultTypes->get('strategy')?->duration_minutes ?? 30 }},name:{{ json_encode($consultTypes->get('strategy')?->name ?? 'Strategy Call') }},isFree:{{ ($consultTypes->get('strategy')?->is_free ?? false) ? 'true' : 'false' }}};window.dispatchEvent(new CustomEvent('open-booking',{detail:window._bkPending}));if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'pricing_section',cta_label:'book_strategy_call'});">Book a Strategy Call</button>
    </div>

    <div class="tier prime">
      <span class="tier-flag">Market Dominance Access</span>
      <h3 class="tier-name">Dominance</h3>
      <p class="tier-position">Full market control — maximum speed and coverage.</p>
      <ul class="tier-features">
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          Full market expansion across services and locations
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
          <strong>Priority deployment and optimization</strong>
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          Advanced signal reinforcement
        </li>
        <li>
          <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          Strategic oversight and market lock
        </li>
      </ul>
      <div class="tier-price"><sup>$</sup>4,799<sub>/mo</sub></div>
      <div class="tier-commitment">Priority processing. Structured 4-month deployment cycle.</div>
      <a href="{{ route('onboarding.start', ['tier' => 'dominance']) }}" class="tier-cta">Review Dominance Setup</a>
      <button class="tier-book" onclick="window._bkPending={id:{{ $consultTypes->get('agency-review')?->id ?? $consultTypes->get('agency')?->id ?? 3 }},duration:{{ $consultTypes->get('agency-review')?->duration_minutes ?? $consultTypes->get('agency')?->duration_minutes ?? 60 }},name:{{ json_encode($consultTypes->get('agency-review')?->name ?? $consultTypes->get('agency')?->name ?? 'Agency Licence Review') }},isFree:{{ ($consultTypes->get('agency-review')?->is_free ?? $consultTypes->get('agency')?->is_free ?? false) ? 'true' : 'false' }}};window.dispatchEvent(new CustomEvent('open-booking',{detail:window._bkPending}));if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'pricing_section',cta_label:'book_agency_review'});">Book a Market Review</button>
    </div>

  </div>

  <p style="text-align:center;font-size:.82rem;color:rgba(168,168,160,.48);letter-spacing:.03em;padding:12px 0 2px;position:relative;z-index:2;font-style:italic">Most businesses begin with Expansion. Dominance is used when speed and coverage matter most.</p>
  <p style="text-align:center;font-size:.74rem;color:rgba(168,168,160,.32);letter-spacing:.04em;padding:6px 0 4px;position:relative;z-index:2">Applying selects your market level — not a payment. We review your inquiry, confirm availability, and guide activation personally.</p>
  <p style="text-align:center;font-size:.72rem;color:rgba(168,168,160,.26);letter-spacing:.04em;padding:4px 0 12px;position:relative;z-index:2;line-height:1.72">Initial deployment is structured over a 4-month build phase.<br>Ongoing expansion, optimization, and position strength are maintained through continued licensing.<br><em>Pages remain live, but ranking strength depends on active system maintenance.</em></p>

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

<!-- ════════════ VALUE ANCHOR ════════════ -->
<div class="value-anchor r">
  <div class="value-anchor-inner">
    <p class="va-eye">What this is</p>
    <p class="va-main">This is not a tool.<br><em>This is a system designed to expand your visibility across your entire market.</em></p>
    <div class="va-contrast">
      <div class="va-col va-col--left">
        <p class="va-col-hed">What most businesses do</p>
        <p class="va-col-item">Buy SEO tools. Publish content.</p>
        <p class="va-col-item">Target keywords one at a time.</p>
        <p class="va-col-item">Wait months for unclear results.</p>
        <p class="va-col-item">Repeat the same tactics.</p>
      </div>
      <div class="va-col va-col--right">
        <p class="va-col-hed">What this system does</p>
        <p class="va-col-item">Builds your entire online structure.</p>
        <p class="va-col-item">Deploys across every service and city.</p>
        <p class="va-col-item">Establishes position while others wait.</p>
        <p class="va-col-item">Compounds continuously over time.</p>
      </div>
    </div>
    <p class="va-close">This is not marketing spend.<br><em>This is structured market expansion — built and compounded over time.</em></p>
  </div>
</div>

<div class="gold-rule"></div>

<!-- ════════════ EXECUTION SERVICES ════════════ -->
<section class="exec-services r">
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
  <p class="exec-positioning">All services align with your market expansion strategy — not disconnected marketing efforts.</p>
</section>

<div class="gold-rule"></div>

<!-- ════════════ SERVICE SUPPORT ════════════ -->
<div class="svc-support r">
  <p class="ss-eye">Full-Spectrum Capability</p>
  <h2 class="ss-hed">We guide and support every layer of your growth.</h2>
  <p class="ss-intro">Your engagement spans every capability required to build, position, and hold your market — all coordinated under one agreement, one team, one system.</p>
  <div class="ss-grid">
    <div class="ss-item"><span class="ss-dot"></span><span class="ss-label">Programmatic SEO &amp; territorial market expansion</span></div>
    <div class="ss-item"><span class="ss-dot"></span><span class="ss-label">Website Strategy, Development &amp; Conversion Architecture</span></div>
    <div class="ss-item"><span class="ss-dot"></span><span class="ss-label">Paid Media Strategy, Campaign Launch &amp; Growth Management</span></div>
    <div class="ss-item"><span class="ss-dot"></span><span class="ss-label">Brand Management, Creative Direction &amp; Market Delivery</span></div>
    <div class="ss-item"><span class="ss-dot"></span><span class="ss-label">WordPress Management, Enhancements &amp; Technical Support</span></div>
    <div class="ss-item"><span class="ss-dot"></span><span class="ss-label">Content Strategy, Authority Copy &amp; Search Signal Architecture</span></div>
    <div class="ss-item"><span class="ss-dot"></span><span class="ss-label">Structured Data, Schema Markup &amp; Technical SEO</span></div>
    <div class="ss-item"><span class="ss-dot"></span><span class="ss-label">Performance Intelligence, Reporting &amp; Market Tracking</span></div>
  </div>
  <p class="ss-note">One agreement. One team. Every capability required — nothing outsourced, nothing disconnected.</p>
</div>

<div class="gold-rule"></div>

<!-- ════════════ POSITIONING ════════════ -->
<div class="access-position r">
  <div>
    <p class="ap-main">This system is not limited to the largest companies.<br><em>It rewards those who move first.</em></p>
    <p style="font-size:.96rem;color:rgba(168,168,160,.70);line-height:1.82;margin-top:14px">A smaller, faster business can establish market presence before larger competitors — and hold it.</p>
  </div>
  <div>
    <p class="ap-qualifier">Built for growth-focused businesses — from emerging operators to established teams scaling beyond their current reach.</p>
  </div>
</div>

<!-- ════════════ FIT SCREENING ════════════ -->
<div class="fit-screen r">
  <p class="fs-hed">We work selectively.</p>
  <div class="fs-body">
    <p>Before expansion begins, we review whether a business is legitimate, growth-ready, and aligned with platform and search-quality standards.</p>
    <p>This protects the system, the market, and the businesses we choose to support.</p>
  </div>
  <p class="fs-note">We may decline businesses that are not a fit, not aligned with our standards, or not positioned for the kind of growth this system is built to deliver.</p>
</div>

<div class="gold-rule"></div>

<!-- ════════════ PRICING CTA ════════════ -->
<div class="pricing-cta r">
  <div class="pricing-cta-actions">
    <a href="{{ route('onboarding.start') }}" class="btn-primary">Start My Market Setup</a>
  </div>
  <p class="pricing-cta-meta">No commitment &nbsp;&middot;&nbsp; Access reviewed individually &nbsp;&middot;&nbsp; Takes ~2 minutes</p>
</div>

<!-- ════════════ FINAL CLOSE ════════════ -->
<div class="final-close r">
  <p class="fc-main">Your market will be claimed.</p>
  <p class="fc-question">The only question is — by whom.</p>
  <p class="fc-tagline">First to structure. First to deploy. First to rank.</p>
</div>

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
        <p class="exp-body">Manage deployment, track coverage, and oversee expansion across your full market — every page, every city, one place.</p>
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
  <a href="{{ route('onboarding.start') }}" class="btn-primary">Check Your Market Position &rarr;</a>
  <p style="font-size:.72rem;letter-spacing:.10em;text-transform:uppercase;color:rgba(200,168,75,.88);margin-top:14px;font-weight:400">Territories close as agreements are secured.</p>
</section>

<!-- ════════════ CONTACT ════════════ -->
<section id="contact">
  <div class="contact-inner">
    <div>
      <p class="s-eye r">Questions Before You Begin</p>
      <h2 class="s-h r">Not ready yet?<br><em>Ask a question.</em></h2>
      <p class="s-p r">If you have questions before starting onboarding, submit them here. We review every inquiry personally and respond directly.</p>
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

      <p style="font-size:.82rem;color:var(--muted);text-align:center;letter-spacing:.04em;margin-bottom:8px">We review every message personally. Not all markets are available.</p>
      <button type="submit" class="fsub" id="submitBtn">Send My Question</button>
    </form>
  </div>
</section>

<!-- ════════════ FINAL CLOSING CTA ════════════ -->
<section class="fcc r" aria-label="Start your market expansion">
  <canvas class="fcc-canvas" id="fccCanvas" aria-hidden="true"></canvas>
  <div class="fcc-inner">
    <p class="fcc-eye">Market Position</p>
    <h2 class="fcc-hed">
      <span class="fcc-hed-1">The territory will be owned.</span>
      <span class="fcc-hed-2">The only question is by whom.</span>
    </h2>
    <p class="fcc-sub">Markets are structured, expanded, and secured over time.<br>Early positioning establishes long-term visibility.</p>
    <span class="fcc-gold">First to structure. First to scale.</span>
    <p class="fcc-micro">Expansion moves continuously. Entry is guided.</p>
    <span class="fcc-rule" aria-hidden="true"></span>
    <div class="fcc-actions">
      <a href="{{ route('onboarding.start') }}" class="fcc-primary" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'final_close',cta_label:'start_market_setup'});">Start My Market Setup</a>
      <a href="{{ route('how-it-works') }}" class="fcc-secondary">See how it works</a>
    </div>
    <p class="fcc-reassure">Guided entry.&ensp;Structured rollout.&ensp;Full support.</p>
    <p class="fcc-wait">Most businesses wait.<br>Some position early.<br><em>A few own their market.</em></p>
  </div>
</section>

<!-- ════════════ STICKY MOBILE CTA (mobile only — hidden on desktop via CSS) ════════════ -->
<div id="mobStickyCta" class="mob-sticky-cta" role="complementary" aria-label="Quick access — assess market availability">
  <div class="msc-inner">
    <a href="{{ route('onboarding.start') }}" class="msc-primary">Check My Market</a>
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
    <span class="gate-badge">Activation Required</span>
    <h2 class="gate-title">Secure your<br><em>position.</em></h2>
    <p class="gate-desc">Activate your market expansion system.<br><br>This includes:<br><strong>• Structured service + location pages<br>• Internal linking and authority signals<br>• AI-driven expansion across your territory</strong><br><br>Deployed and managed under a single system.</p>
    <div class="gate-tiers">
      <div class="gate-tier" data-tier="expansion">
        <div class="gate-tier-name">Expansion</div>
        <div class="gate-tier-price">$2,995/mo</div>
        <div class="gate-tier-urls">Foundation tier</div>
      </div>
      <div class="gate-tier selected" data-tier="dominance">
        <div class="gate-tier-name">Dominance</div>
        <div class="gate-tier-price">$4,799/mo</div>
        <div class="gate-tier-urls">Preferred · Priority access</div>
      </div>
    </div>
    <p class="gate-guidance">Most businesses start with Expansion, then scale into Dominance.</p>
    <a href="/onboarding/start" class="gate-cta" id="gateCta">Activate My Market</a>
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
      'Your market.<br>Your territory.',
      'Every city.<br>Every service.',
      'Programmatic<br>expansion.',
      'One brand.<br>One territory.',
      'Built for<br>AI search.'
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
    var COUNT=40, LINK=240, G='200,168,75';
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
            ctx.strokeStyle='rgba('+G+','+(1-d/LINK)*.36+')';
            ctx.lineWidth=.55;
            ctx.stroke();
          }
        }
      }

      /* nodes */
      for(var i=0;i<nodes.length;i++){
        var n = nodes[i];
        // breathing range 0.26–0.50, per-node phase so no two nodes sync
        var pulse = .38 + Math.sin(tick + n.phase) * .12;
        var glow  = n.glowMult;

        ctx.shadowBlur  = glow * 12;
        ctx.shadowColor = 'rgba('+G+','+(glow*.42).toFixed(2)+')';
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
      tick += 0.010;

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
          n.x += n.vx * .55;
          n.y += n.vy * .55;
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
    var COUNT=46, LINK=260, G='200,168,75';
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
      tick += 0.014;

      for(var i=0;i<nodes.length;i++){
        for(var j=i+1;j<nodes.length;j++){
          var dx=nodes[j].x-nodes[i].x, dy=nodes[j].y-nodes[i].y;
          var d=Math.sqrt(dx*dx+dy*dy);
          if(d<LINK){
            ctx.beginPath();
            ctx.moveTo(nodes[i].x,nodes[i].y);
            ctx.lineTo(nodes[j].x,nodes[j].y);
            ctx.strokeStyle='rgba('+G+','+(1-d/LINK)*.44+')';
            ctx.lineWidth=.60;
            ctx.stroke();
          }
        }
      }

      for(var i=0;i<nodes.length;i++){
        var n = nodes[i];
        var pulse = .42 + Math.sin(tick + n.phase) * .14;
        var glow  = n.glowMult;
        ctx.shadowBlur  = glow * 14;
        ctx.shadowColor = 'rgba('+G+','+(glow*.46).toFixed(2)+')';
        ctx.beginPath();
        ctx.arc(n.x, n.y, n.r, 0, Math.PI*2);
        ctx.fillStyle   = 'rgba('+G+','+pulse.toFixed(3)+')';
        ctx.fill();
        ctx.shadowBlur  = 0;

        if(!reduced){
          n.x += n.vx * .70;
          n.y += n.vy * .70;
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
