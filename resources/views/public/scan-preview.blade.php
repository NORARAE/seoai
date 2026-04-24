<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Your AI Visibility Results — SEO AI Co™</title>
<link rel="canonical" href="{{ url('/scan/preview') }}">
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
@include('partials.design-system')
@include('partials.public-nav-css')

/* ── Page ── */
.prev-page{
  min-height:100vh;display:flex;flex-direction:column;
  align-items:center;justify-content:center;
  padding:120px 20px 88px;position:relative;overflow-x:hidden;
}
.prev-page::before{
  content:'';position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(200,168,75,.04) 1px,transparent 1px),
    linear-gradient(90deg,rgba(200,168,75,.04) 1px,transparent 1px);
  background-size:48px 48px;
  pointer-events:none;z-index:0;
  animation:gridBreath 12s ease-in-out infinite;
}
@keyframes gridBreath{
  0%,100%{opacity:1}
  50%{opacity:.65}
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
  animation:gapBadgePulse 3s ease-in-out infinite;
}
@keyframes gapBadgePulse{
  0%,100%{border-color:rgba(196,120,120,.12);box-shadow:none}
  50%{border-color:rgba(196,120,120,.18);box-shadow:0 0 12px rgba(196,120,120,.06)}
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

/* ── Visibility Score ── */
.prev-vis-score{
  text-align:center;margin:0 auto 6px;
  animation:visScorePulse 3.5s ease-in-out infinite;
}
@keyframes visScorePulse{
  0%,100%{opacity:1}
  50%{opacity:.82}
}
.prev-vis-num{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(2.6rem,6vw,3.4rem);font-weight:300;
  color:rgba(196,120,120,.8);line-height:1;
}
.prev-vis-of{
  font-size:.82rem;color:rgba(168,168,160,.4);
  letter-spacing:.04em;font-weight:300;
}
.prev-vis-label{
  display:block;font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;
  color:rgba(196,120,120,.45);margin-top:8px;font-weight:500;
}
.prev-vis-readiness{
  text-align:center;font-size:.74rem;color:rgba(168,168,160,.5);
  margin-bottom:20px;letter-spacing:.01em;
}
.prev-gap-subtext{
  text-align:center;font-size:.72rem;color:rgba(196,120,120,.5);
  margin-bottom:4px;letter-spacing:.01em;
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

/* Signal row hover */
.prev-signal{
  transition:opacity .5s var(--ease-out),transform .5s var(--ease-out),background .15s ease;
  border-radius:3px;padding-left:6px;padding-right:6px;margin-left:-6px;margin-right:-6px;
}
.prev-signal:hover{
  background:rgba(200,168,75,.03);
}
.prev-signal:hover .prev-signal-text{color:rgba(237,232,222,1)}
.prev-signal:hover .prev-icon{box-shadow:0 0 14px rgba(200,168,75,.18)}

/* ── AI Proof Section ── */
.prev-ai-proof{
  margin:10px 0 30px;
  padding:24px 22px;
  border:1px solid rgba(200,168,75,.09);
  border-radius:8px;
  background:linear-gradient(180deg,rgba(14,13,10,.92),rgba(10,10,8,.92));
}
.prev-ai-proof-title{
  text-align:center;
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.35rem,2.7vw,2rem);
  font-weight:300;
  color:var(--ivory);
  margin:0 0 8px;
}
.prev-ai-proof-sub{
  text-align:center;
  font-size:.8rem;
  line-height:1.7;
  color:var(--muted);
  margin:0 auto 20px;
  max-width:500px;
}
.prev-ai-proof-grid{
  display:grid;
  grid-template-columns:1.15fr .95fr;
  gap:14px;
}
.prev-ai-panel{
  border:1px solid rgba(200,168,75,.08);
  border-radius:6px;
  background:rgba(8,8,8,.45);
  padding:14px;
}
.prev-ai-panel-head{
  font-size:.58rem;
  letter-spacing:.14em;
  text-transform:uppercase;
  color:rgba(200,168,75,.58);
  margin-bottom:10px;
}
.prev-ai-chat-window{
  border:1px solid rgba(168,168,160,.16);
  border-radius:6px;
  padding:12px;
  background:rgba(18,18,16,.86);
}
.prev-ai-chat-line{
  opacity:0;
  transform:translateY(5px);
  transition:opacity .35s ease,transform .35s ease;
  color:rgba(235,230,220,.92);
  font-size:.76rem;
  line-height:1.65;
  margin:0 0 7px;
}
.prev-ai-chat-line.is-visible{opacity:1;transform:translateY(0)}
.prev-ai-chat-line:last-child{margin-bottom:0}
.prev-ai-chat-cursor{
  display:inline-block;
  width:7px;
  margin-left:2px;
  color:rgba(200,168,75,.72);
  animation:prevProofCursor 1s steps(2, start) infinite;
}
@keyframes prevProofCursor{to{visibility:hidden}}

.prev-ai-keyline{
  margin:12px 0 0;
  font-size:.78rem;
  line-height:1.6;
  color:rgba(226,210,165,.9);
  font-weight:600;
}

.prev-ai-signals{list-style:none;margin:0;padding:0}
.prev-ai-signal{
  display:flex;
  align-items:flex-start;
  gap:10px;
  padding:8px 0;
  border-bottom:1px solid rgba(200,168,75,.05);
  opacity:0;
  transform:translateY(5px);
  transition:opacity .35s ease,transform .35s ease;
}
.prev-ai-signal:last-child{border-bottom:none}
.prev-ai-signal.is-visible{opacity:1;transform:translateY(0)}
.prev-ai-signal-mark{
  width:18px;
  flex-shrink:0;
  font-size:.76rem;
  line-height:1.45;
  font-weight:700;
}
.prev-ai-signal-mark.is-ok{color:#6aaf90}
.prev-ai-signal-mark.is-bad{color:#c47878}
.prev-ai-signal-text{
  font-size:.76rem;
  line-height:1.55;
  color:rgba(232,226,214,.88);
}

.prev-ai-proof-cta{
  margin-top:16px;
  text-align:center;
}
.prev-ai-proof-btn{
  display:inline-block;
  padding:14px 28px;
  border-radius:4px;
  border:1px solid rgba(226,201,125,.4);
  background:linear-gradient(180deg,#d8be72,#c8a84b);
  color:#080808;
  text-decoration:none;
  font-size:.73rem;
  letter-spacing:.1em;
  text-transform:uppercase;
  font-weight:700;
  transition:transform .22s ease,box-shadow .22s ease;
}
.prev-ai-proof-btn:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(200,168,75,.22)}
.prev-ai-proof-meta{
  margin-top:9px;
  font-size:.69rem;
  color:rgba(168,168,160,.63);
  letter-spacing:.02em;
}

/* ── Locked Signal Intelligence ── */
.prev-locked-depth{
  background:rgba(10,9,7,.60);
  border:1px solid rgba(200,168,75,.06);
  border-radius:6px;padding:24px 28px;
  margin-bottom:10px;position:relative;overflow:hidden;
}
.prev-locked-depth::after{
  content:'';position:absolute;bottom:0;left:0;right:0;height:48px;
  background:linear-gradient(to top,rgba(8,8,8,.92),transparent);
  pointer-events:none;
}
.prev-locked-depth-head{
  font-size:.58rem;letter-spacing:.2em;text-transform:uppercase;
  color:rgba(200,168,75,.35);margin-bottom:16px;padding-bottom:10px;
  border-bottom:1px solid rgba(200,168,75,.06);
}
.prev-locked-depth-row{
  display:flex;align-items:center;gap:12px;
  padding:9px 0;opacity:.45;
}
.prev-locked-depth-row .depth-icon{
  width:20px;height:20px;border-radius:50%;
  background:rgba(200,168,75,.06);border:1px solid rgba(200,168,75,.10);
  display:flex;align-items:center;justify-content:center;
  font-size:.6rem;color:rgba(200,168,75,.4);flex-shrink:0;
}
.prev-locked-depth-row .depth-label{
  font-size:.78rem;color:rgba(237,232,222,.5);letter-spacing:.01em;
}
/* Locked depth row hover */
.prev-locked-depth-row{
  transition:background .15s ease;
  border-radius:3px;padding-left:6px;padding-right:6px;margin-left:-6px;margin-right:-6px;
}
.prev-locked-depth-row:hover{
  background:rgba(200,168,75,.03);
}
.prev-locked-depth-row:hover .depth-label{color:rgba(237,232,222,.65)}
.prev-locked-depth-row:hover .depth-icon{box-shadow:0 0 10px rgba(200,168,75,.12)}
.prev-strategic{
  text-align:center;font-size:.78rem;color:var(--muted);
  line-height:1.7;margin-bottom:32px;max-width:440px;margin-left:auto;margin-right:auto;
}
.prev-strategic em{
  color:rgba(200,168,75,.55);font-style:normal;
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
  box-shadow:0 4px 24px rgba(200,168,75,.15),0 0 40px rgba(200,168,75,.08);
}
.prev-cta::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.14),transparent);
  transform:translateX(-100%);transition:transform .6s;
}
.prev-cta:hover{
  background:linear-gradient(180deg,#e0c97e,#d4b45a);
  border-color:rgba(226,201,125,.65);
  box-shadow:0 8px 40px rgba(200,168,75,.28),0 0 60px rgba(200,168,75,.12);
  transform:translateY(-2px);
}
.prev-cta:hover::before{transform:translateX(100%)}

@keyframes ctaPulse{
  0%,100%{box-shadow:0 4px 24px rgba(200,168,75,.15),0 0 40px rgba(200,168,75,.08)}
  50%{box-shadow:0 6px 40px rgba(200,168,75,.22),0 0 56px rgba(200,168,75,.12)}
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
.prev-reassure .chk{color:rgba(106,175,144,.5);font-weight:600}

.prev-secondary{
  text-align:center;margin-top:20px;
}
.prev-secondary a{
  font-size:.72rem;color:rgba(200,168,75,.4);text-decoration:none;
  letter-spacing:.04em;transition:color .3s,opacity .3s;
  opacity:.6;
}
.prev-secondary a:hover{color:var(--gold);opacity:1}

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

@include('partials.public-nav-mobile-css')

/* ── Nav mobile breakpoint ── */
@media(max-width:900px){
  #nav{padding:14px 20px}#nav.stuck{padding:10px 20px}
  .nav-link{display:none}
  .nav-btn{display:none}
  .nav-hamburger{display:flex}
}

/* ── Responsive ── */

/* ── Mobile UX refinement ── */
@media(max-width:768px){
  /* Page container — breathing room */
  .prev-page{padding:88px 20px 88px}

  /* Header — bigger, more assertive */
  .prev-hed{font-size:clamp(1.7rem,5vw,2.4rem);margin-bottom:14px}
  .prev-sub{font-size:.92rem;line-height:1.8;margin-bottom:8px}
  .prev-eye{font-size:.66rem;letter-spacing:.24em;margin-bottom:14px}
  .prev-urgency{font-size:.82rem;margin-bottom:10px}
  .prev-url{font-size:.78rem;margin-bottom:32px}

  /* Issue counter — dominant score feel */
  .prev-issue-badge .count{font-size:clamp(2.4rem,8vw,3rem);font-weight:800;line-height:1}
  .prev-issue-badge .label{font-size:.9rem;opacity:.85}
  .prev-issue-badge{padding:16px 28px;gap:12px;border-radius:6px}
  .prev-hidden-count{font-size:.78rem;margin-bottom:24px}

  /* Visibility score — mobile */
  .prev-vis-num{font-size:clamp(2.8rem,7vw,3.4rem)}
  .prev-vis-readiness{font-size:.8rem;margin-bottom:22px}
  .prev-gap-subtext{font-size:.78rem}

  /* Signal rows — readable bullets */
  .prev-signal{padding:11px 0;gap:14px}
  .prev-signal-text{font-size:.95rem;line-height:1.65}
  .prev-signal-text .impact{font-size:.76rem;margin-top:3px;line-height:1.5}
  .prev-icon{width:26px;height:26px;font-size:.8rem}

  /* Cards — centered floating panels */
  .prev-card,.prev-locked,.prev-locked-depth{
    padding:34px 26px 30px;margin-bottom:28px;
    border-radius:8px;
  }
  .prev-card-head{font-size:.62rem;margin-bottom:16px;padding-bottom:12px;
    border-bottom-color:rgba(200,168,75,.1)}

  /* Locked section — hierarchy */
  .prev-locked{padding:38px 26px 32px}
  .prev-locked-badge{font-size:.66rem;padding:11px 22px}
  .prev-locked-unlock-text{font-size:.82rem;line-height:1.6;max-width:280px}
  .prev-locked-score{font-size:2.6rem;opacity:.4}
  .prev-locked-row{padding:12px 0}
  .prev-locked-label{font-size:.88rem}

  /* CTA — dominant, clear next action */
  .prev-cta{padding:20px 48px;font-size:.88rem;letter-spacing:.12em;min-height:54px}
  .prev-cta-sub{font-size:.86rem;line-height:1.7;margin-top:18px}
  .prev-reassure{font-size:.74rem;margin-top:16px}
  .prev-secondary a{font-size:.78rem}

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

  /* Sticky bar */
  .prev-sticky{padding:14px 20px;gap:14px}
  .prev-sticky-text{font-size:.74rem}
  .prev-sticky-cta{padding:14px 28px;font-size:.76rem;min-height:48px}
}

@media(max-width:520px){
  /* ── True centered floating cards ── */
  .prev-card,.prev-locked,.prev-locked-depth,.prev-value-card{
    width:94%;max-width:420px;margin-left:auto;margin-right:auto;
    padding:36px 28px 32px;
  }

  /* Issue badge — centered score module */
  .prev-issue-badge{
    padding:18px 30px;gap:14px;
    border-radius:8px;
  }
  .prev-issue-badge .count{font-size:clamp(2.4rem,8vw,3rem)}
  .prev-issue-badge .label{font-size:.9rem;letter-spacing:.05em}

  /* Background isolation — card foreground dominance */
  .prev-page::after{opacity:.5}
  .prev-scanline{opacity:.5}

  /* Header tighten */
  .prev-page{padding:80px 16px 80px}
  .prev-inner{max-width:100%}
  .prev-hed{font-size:clamp(1.5rem,6vw,1.9rem)}

  /* Signal rows — clean bullet alignment */
  .prev-signal{padding:10px 0;gap:12px}
  .prev-signal-text{font-size:.95rem;line-height:1.62}
  .prev-signal-text .impact{font-size:.76rem;margin-top:2px}
  .prev-icon{width:24px;height:24px;font-size:.76rem;margin-top:2px}

  /* Card section spacing — tighter flow */
  .prev-card-head{margin-bottom:14px;padding-bottom:10px;
    border-bottom:1px solid rgba(200,168,75,.12)}

  /* Locked panel — hierarchy: score → status → findings */
  .prev-locked{padding:36px 28px 30px}
  .prev-locked-score{font-size:2.8rem;padding-top:16px;opacity:.35}
  .prev-locked-row{padding:10px 0}
  .prev-locked-label{font-size:.86rem}
  .prev-locked-bar{height:4px}

  /* CTA — strong next-action */
  .prev-cta{padding:18px 36px;font-size:.88rem;min-height:52px}
  .prev-cta-sub{font-size:.86rem}
  .prev-secondary a{font-size:.8rem;opacity:.65}
  .prev-secondary a:hover{opacity:1}

  /* Sticky bar */
  .prev-sticky{gap:12px;padding:14px 18px}
  .prev-sticky-text{font-size:.76rem}
  .prev-sticky-cta{padding:14px 28px;font-size:.76rem;min-height:46px}

  .prev-ai-proof{padding:20px 16px}
  .prev-ai-proof-grid{grid-template-columns:1fr;gap:12px}
}

/* ── Small phones ── */
@media(max-width:430px){
  .prev-page{padding:76px 14px 76px}
  .prev-card,.prev-locked,.prev-locked-depth,.prev-value-card{
    width:94%;max-width:400px;padding:32px 24px 28px;
  }
  .prev-hed{font-size:clamp(1.4rem,6.5vw,1.8rem)}
  .prev-sub{font-size:.90rem}
  .prev-signal{padding:9px 0}
  .prev-signal-text{font-size:.92rem;line-height:1.6}
  .prev-signal-text .impact{font-size:.74rem}
  .prev-issue-badge .count{font-size:clamp(2.2rem,7.5vw,2.8rem)}
  .prev-issue-badge .label{font-size:.86rem}
  .prev-cta{padding:16px 32px;font-size:.84rem;min-height:48px}
  .prev-sticky-cta{padding:12px 24px;font-size:.74rem;min-height:44px}
  .prev-locked-score{font-size:2.4rem}
  .prev-locked-label{font-size:.84rem}
  .prev-locked-row{padding:9px 0}

  .prev-ai-proof-title{font-size:1.25rem}
  .prev-ai-proof-sub{font-size:.78rem}
  .prev-ai-chat-line,.prev-ai-signal-text{font-size:.74rem}
}

/* ── Very small phones ── */
@media(max-width:390px){
  .prev-card,.prev-locked,.prev-locked-depth,.prev-value-card{width:96%;max-width:380px;padding:28px 20px 24px}
  .prev-hed{font-size:clamp(1.3rem,7vw,1.7rem)}
  .prev-sub{font-size:.88rem}
  .prev-cta{padding:16px 28px;font-size:.82rem}
  .prev-signal{padding:8px 0}
  .prev-signal-text{font-size:.88rem}
  .prev-issue-badge .count{font-size:clamp(2rem,7vw,2.6rem)}
  .prev-issue-badge .label{font-size:.84rem}
  .prev-locked-score{font-size:2.2rem}
}
</style>
</head>
<body class="prev-page">

@include('partials.public-nav', ['isReportPage' => true, 'showHamburger' => true])

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
      <span class="prev-prog-label next">Results</span>
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
  <h1 class="prev-hed">Visibility Intelligence Preview</h1>
  <p class="prev-sub">Initial scan complete. This view is intentionally partial before unlock.</p>
  @if($issueCount > 0)
  <p class="prev-urgency">Critical signal gaps detected in your citation structure.</p>
  @endif
  <p class="prev-url">{{ $host }}</p>

  <!-- Visibility score -->
  @php
    $passCount = ($preview['has_sitemap'] ? 1 : 0) + ($preview['has_schema'] ? 1 : 0) + ($preview['has_locations'] ? 1 : 0) + ($preview['has_authority'] ? 1 : 0);
    $visibilityScore = max(32, min(68, 32 + ($passCount * 9) + (intval($preview['pages_detected'] ?? 0) > 5 ? 2 : 0)));
  @endphp
  <div class="prev-vis-score">
    <span class="prev-vis-num">{{ $visibilityScore }} <span class="prev-vis-of">/ 100</span></span>
    <span class="prev-vis-label">Preview Score</span>
  </div>
  <p class="prev-vis-readiness">AI citation readiness is currently limited at the surface layer.</p>

  <!-- Issue counter badge -->
  @if($issueCount > 0)
  <div class="prev-issue-badge">
    <span class="count">{{ $issueCount }}</span>
    <span class="label">{{ $issueCount === 1 ? 'critical gap' : 'critical gaps' }} detected</span>
  </div>
  <p class="prev-gap-subtext">These gaps reduce source-selection confidence across AI answers.</p>
  <p class="prev-hidden-count">{{ $issueCount }} identified &mdash; deeper structural findings remain locked</p>
  @endif

  <!-- Primary above-fold CTA ($2) -->
  <div class="prev-cta-wrap" style="margin-bottom:14px">
    <a href="{{ route('checkout.scan-basic') }}" class="prev-cta prev-cta-pulse" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'above_fold',cta_label:'scan_basic'});">Reveal Full Signal Map&nbsp;&mdash;&nbsp;$2</a>
  </div>
  <p class="prev-cta-sub" style="margin-top:0;margin-bottom:18px">Secure unlock. Full signal map, deeper layers, and correction sequence.</p>

  <!-- Detected signals card -->
  <div class="prev-card">
    <p class="prev-card-head">Detected Signals</p>
    <ul class="prev-signals" id="prevSignals">
      @foreach(($surfaceSignals ?? []) as $idx => $signal)
      <li class="prev-signal" data-delay="{{ $idx }}">
        <span class="prev-icon {{ !empty($signal['ok']) ? 'prev-icon--ok' : 'prev-icon--warn' }}">{{ !empty($signal['ok']) ? '✓' : '⚠' }}</span>
        <span class="prev-signal-text">{{ $signal['label'] }}</span>
      </li>
      @endforeach

      <li class="prev-signal" data-delay="2">
        <span class="prev-icon prev-icon--warn">⚠</span>
        <span class="prev-signal-text">
          {{ $primaryGap ?? 'Critical structural gap detected' }}
          <span class="impact">Deeper structural context remains locked</span>
        </span>
      </li>

    </ul>
  </div>

  <!-- AI proof section -->
  <section class="prev-ai-proof" id="prevAiProof">
    <h2 class="prev-ai-proof-title">How AI Sees Your Site</h2>
    <p class="prev-ai-proof-sub">This is what AI systems actually extract &mdash; and why most sites get ignored.</p>

    <div class="prev-ai-proof-grid">
      <article class="prev-ai-panel">
        <p class="prev-ai-panel-head">AI View</p>
        <div class="prev-ai-chat-window" aria-live="polite">
          <p class="prev-ai-chat-line">Best local biohazard cleanup companies in Seattle include:</p>
          <p class="prev-ai-chat-line">&bull; American Bio Management</p>
          <p class="prev-ai-chat-line">&bull; Bio-One Seattle</p>
          <p class="prev-ai-chat-line">&bull; Aftermath Services<span class="prev-ai-chat-cursor">|</span></p>
        </div>

        <p class="prev-ai-keyline">
          @if($issueCount > 0)
            AI didn't choose your site &mdash; here's why.
          @else
            AI would choose your site &mdash; if these were fixed.
          @endif
        </p>
      </article>

      <article class="prev-ai-panel">
        <p class="prev-ai-panel-head">Extraction Signals</p>
        <ul class="prev-ai-signals" id="prevAiSignals">
          <li class="prev-ai-signal"><span class="prev-ai-signal-mark is-ok">&#10003;</span><span class="prev-ai-signal-text">Business name detected</span></li>
          <li class="prev-ai-signal"><span class="prev-ai-signal-mark is-ok">&#10003;</span><span class="prev-ai-signal-text">Service category detected</span></li>
          <li class="prev-ai-signal"><span class="prev-ai-signal-mark is-bad">&#10005;</span><span class="prev-ai-signal-text">No clear answer block</span></li>
          <li class="prev-ai-signal"><span class="prev-ai-signal-mark is-bad">&#10005;</span><span class="prev-ai-signal-text">Weak internal linking</span></li>
          <li class="prev-ai-signal"><span class="prev-ai-signal-mark is-ok">&#10003;</span><span class="prev-ai-signal-text">Schema partially detected</span></li>
        </ul>
      </article>
    </div>

    <div class="prev-ai-proof-cta">
      <a href="{{ route('checkout.scan-basic') }}" class="prev-ai-proof-btn" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'ai_proof_section',cta_label:'scan_basic'});">See My AI Result &rarr;</a>
      <p class="prev-ai-proof-meta">Takes 10 seconds &bull; $2 scan</p>
    </div>
  </section>

  <!-- Locked Signal Intelligence -->
  <div class="prev-locked-depth">
    <p class="prev-locked-depth-head">Locked Layers</p>
    <div class="prev-locked-depth-row">
      <span class="depth-icon">🔒</span>
      <span class="depth-label">+{{ max(3, $issueCount + rand(2,4)) }} structural gaps hidden</span>
    </div>
    <div class="prev-locked-depth-row">
      <span class="depth-icon">🔒</span>
      <span class="depth-label">+{{ rand(2,5) }} authority gaps hidden</span>
    </div>
    <div class="prev-locked-depth-row">
      <span class="depth-icon">🔒</span>
      <span class="depth-label">+{{ rand(3,6) }} competitive weaknesses hidden</span>
    </div>
  </div>

  <!-- Consequence layer -->
  <p class="prev-strategic" style="margin-bottom:12px">AI systems are already selecting which sources to cite.<br>Gaps in your signal layer reduce the likelihood your business is chosen.</p>

  <!-- Strategic line -->
  <p class="prev-strategic">This is the first layer only.<br><em>Deeper structural findings remain locked until unlock.</em></p>

  <!-- Locked intelligence section -->
  <div class="prev-locked">
    <div class="prev-locked-overlay">
      <span class="prev-locked-badge">
        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="10" height="8" rx="1.5"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg>
        Deeper Layers Locked
      </span>
      <p class="prev-locked-unlock-text">Your priority path has been calculated.<br>Unlock to access full signal depth and next actions.</p>
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
  <p class="prev-strategic" style="margin-bottom:24px">Your full signal map is ready for unlock.</p>
  <div class="prev-cta-wrap">
    <a href="{{ route('checkout.scan-basic') }}" class="prev-cta prev-cta-pulse" id="mainCta" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'primary_cta',cta_label:'scan_basic'});">Reveal Full Signal Map&nbsp;&mdash;&nbsp;$2</a>
  </div>
  <p class="prev-cta-sub">Secure access. No repeated work. Data carries forward.</p>

  <!-- Secondary $99 path (non-distracting) -->
  <div class="prev-secondary">
    <a href="{{ route('checkout.signal-expansion') }}" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'secondary',cta_label:'signal_expansion'});">Or go deeper: Signal Analysis &mdash; $99</a>
  </div>

  <!-- Layer progression block -->
  <div class="prev-layer">
    <p class="prev-layer-label">You are at Layer 1</p>
    <p class="prev-layer-sub"><span>Unlock Results ($2)</span> &rarr; Signal Analysis &rarr; Action Plan &rarr; Guided Execution</p>
  </div>

</div>

<!-- Sticky bottom bar ($2) -->
<div class="prev-sticky" id="prevSticky">
  <span class="prev-sticky-text">Your results are ready</span>
  <a href="{{ route('checkout.scan-basic') }}" class="prev-sticky-cta" onclick="if(typeof gtag==='function')gtag('event','cta_click',{cta_location:'sticky_bar',cta_label:'scan_basic'});">Reveal Full Signal Map&nbsp;&mdash;&nbsp;$2</a>
</div>

<script>
(function(){
  // Nav sticky
  var nav = document.getElementById('nav');
  if(nav) window.addEventListener('scroll', function(){ nav.classList.toggle('stuck', scrollY > 60); }, {passive:true});

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

  // AI proof reveal animation
  var aiProof = document.getElementById('prevAiProof');
  if(aiProof){
    var aiLines = aiProof.querySelectorAll('.prev-ai-chat-line');
    var aiSignals = aiProof.querySelectorAll('.prev-ai-signal');
    var aiStarted = false;

    function runAiProof(){
      if(aiStarted) return;
      aiStarted = true;

      aiLines.forEach(function(line, idx){
        setTimeout(function(){ line.classList.add('is-visible'); }, 180 + (idx * 260));
      });

      aiSignals.forEach(function(row, idx){
        setTimeout(function(){ row.classList.add('is-visible'); }, 650 + (idx * 180));
      });
    }

    if('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(entries){
        if(entries[0] && entries[0].isIntersecting){
          runAiProof();
          io.disconnect();
        }
      }, { threshold: 0.24 });
      io.observe(aiProof);
    } else {
      runAiProof();
    }
  }
})();
</script>
@include('partials.public-footer')
@include('partials.public-nav-js')
</body>
</html>
