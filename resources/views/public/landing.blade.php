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
<title>SEOAIco — Licensed Ranking Infrastructure for Agencies & Growing Businesses</title>
<meta name="description" content="SEOAIco licenses structured ranking infrastructure — not content. Agencies and growing businesses gain a controlled, expandable search footprint that compounds over time.">
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
.nav-link{font-size:.82rem;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.nav-link:hover{color:var(--gold)}
.nav-btn{
  font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;
  color:var(--bg);background:var(--gold);padding:12px 28px;text-decoration:none;transition:background .3s;
  display:inline-flex;align-items:center;white-space:nowrap;
}
.nav-btn:hover{background:var(--gold-lt)}
.nav-account-short{display:none}

/* ── Hero ── */
#hero{
  min-height:100vh;display:flex;flex-direction:column;
  justify-content:center;align-items:flex-start;
  padding:140px 64px 100px;position:relative;overflow:hidden;
  max-width:1200px;margin:0 auto;
}
.hero-grid{
  position:fixed;inset:0;pointer-events:none;z-index:0;
  background-image:
    linear-gradient(rgba(200,168,75,.03) 1px,transparent 1px),
    linear-gradient(90deg,rgba(200,168,75,.03) 1px,transparent 1px);
  background-size:88px 88px;
}
.hero-orb{
  position:absolute;top:30%;right:-10%;width:600px;height:600px;border-radius:50%;
  background:radial-gradient(ellipse,rgba(200,168,75,.07) 0%,transparent 65%);pointer-events:none;
}
/* ── Hero pre-headline (kicker + question) ── */
.hero-pre{
  margin-bottom:24px;
  display:flex;flex-direction:column;gap:6px;
  opacity:0;animation:up .7s .15s forwards;
}
.hp-kicker{
  font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;
  font-size:clamp(1.6rem,3vw,2.4rem);color:var(--gold);
  letter-spacing:.04em;line-height:1.4;
  display:flex;align-items:center;gap:14px;
}
.hp-kicker::before{content:'';display:inline-block;width:28px;height:1px;background:var(--gold);flex-shrink:0}
.hp-question{
  font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;
  font-size:clamp(1.3rem,2.2vw,1.8rem);color:var(--ivory);
  letter-spacing:.04em;line-height:1.45;padding-left:42px;
}
.hero-h1{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(3rem,7vw,6.8rem);font-weight:300;line-height:1.06;
  margin-bottom:28px;max-width:780px;opacity:0;animation:up .9s .3s forwards;
}
.hero-h1 em{font-style:italic;color:var(--gold)}
.hero-p{
  max-width:580px;margin-bottom:48px;
  opacity:0;animation:up .85s .5s forwards;
  display:flex;flex-direction:column;gap:0;
}
.hero-p-line{
  font-size:1.08rem;line-height:1.9;color:var(--muted);
  padding:6px 0;
}
.hero-p-line + .hero-p-line{border-top:none}
.hp-emphasis{
  font-family:'Cormorant Garamond',serif;font-weight:400;font-style:italic;
  font-size:clamp(1.55rem,2.8vw,2.2rem);color:var(--ivory);
  padding-top:16px;padding-bottom:12px;
  margin-top:8px;
  border-top:1px solid rgba(200,168,75,.18);
}
.hp-strong{
  color:var(--ivory);font-size:1.15rem;font-weight:500;
  letter-spacing:.03em;padding-top:12px;margin-top:6px;
}
.hero-p strong{color:var(--ivory);font-weight:400}
.hero-actions{display:flex;gap:20px;align-items:center;opacity:0;animation:up .85s .65s forwards}
.btn-primary{
  background:var(--gold);color:var(--bg);font-size:.82rem;font-weight:500;letter-spacing:.14em;
  text-transform:uppercase;padding:18px 48px;text-decoration:none;transition:background .3s,transform .2s;
}
.btn-primary:hover{background:var(--gold-lt);transform:translateY(-2px)}
.btn-ghost{
  font-size:.82rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);
  text-decoration:none;border-bottom:1px solid var(--border);padding-bottom:3px;transition:color .3s,border-color .3s;
}
.btn-ghost:hover{color:var(--ivory);border-color:var(--muted)}
.hero-scroll{
  position:absolute;bottom:48px;left:64px;display:flex;align-items:center;gap:16px;
  opacity:0;animation:up .8s 1.1s forwards;
}
.hero-scroll span{font-size:.58rem;letter-spacing:.28em;text-transform:uppercase;color:var(--muted)}
.scroll-line{width:48px;height:1px;background:linear-gradient(to right,var(--gold),transparent)}

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
  position:relative;padding:48px 56px;border:1px solid var(--border);
  background:linear-gradient(135deg,rgba(200,168,75,.03) 0%,transparent 60%);
}
.stmt-quote::before{content:'';position:absolute;top:0;left:48px;right:48px;height:1px;background:linear-gradient(90deg,transparent,var(--gold-dim),transparent)}
.stmt-quote::after{content:'';position:absolute;bottom:0;left:48px;right:48px;height:1px;background:linear-gradient(90deg,transparent,var(--gold-dim),transparent)}
.stmt-quote .sq-mark{display:block;font-family:'Cormorant Garamond',serif;font-size:3.2rem;line-height:1;color:var(--gold-dim);margin-bottom:12px;user-select:none}
.stmt-quote .sq-text{
  font-family:'Cormorant Garamond',serif;font-size:clamp(1.5rem,2.6vw,2.2rem);
  font-weight:300;font-style:italic;line-height:1.5;color:var(--ivory);letter-spacing:.01em;
}
.stmt-quote .sq-text strong{font-style:normal;color:var(--gold);font-weight:400}
.stmt-quote .sq-rule{display:block;width:48px;height:1px;background:var(--gold-dim);margin:20px auto 0}
.stmt-body p{font-size:1.05rem;line-height:1.95;color:var(--muted);margin-bottom:18px}
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
.aud-card{background:var(--deep);padding:56px 44px;position:relative;overflow:hidden;transition:background .4s}
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
.aud-body{font-size:1.05rem;line-height:1.95;color:var(--muted);margin-bottom:28px}
.aud-body strong{color:var(--ivory);font-weight:400}
.aud-list{list-style:none;display:flex;flex-direction:column;gap:14px}
.aud-list li{font-size:1rem;color:var(--muted);padding-left:22px;position:relative;line-height:1.8}
.aud-list li::before{content:'';position:absolute;left:0;top:12px;width:10px;height:1px;background:var(--gold)}
.aud-list li strong{color:var(--ivory);font-weight:400}
.aud-cta{
  display:inline-block;margin-top:32px;font-size:.82rem;font-weight:500;letter-spacing:.14em;
  text-transform:uppercase;padding:16px 40px;text-decoration:none;transition:background .3s,transform .2s,border-color .3s;
  background:var(--gold);color:var(--bg);border:1px solid var(--gold);
}
.aud-cta:hover{background:var(--gold-lt);border-color:var(--gold-lt);transform:translateY(-2px)}

/* ── WYL (What You're Licensing) ── */
.wyl-section{border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:88px 64px;background:var(--deep)}
.wyl-inner{max-width:1200px;margin:0 auto}
.wyl-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-top:52px}
.wyl-card{
  background:linear-gradient(160deg,rgba(18,18,16,.98) 0%,rgba(12,12,10,1) 100%);
  border:1px solid rgba(200,168,75,.1);
  padding:44px 36px;position:relative;overflow:hidden;
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
  font-size:1.8rem;color:var(--gold);opacity:.55;margin-bottom:22px;
  display:block;transition:opacity .35s,transform .45s cubic-bezier(.23,1,.32,1);line-height:1;
}
.wyl-card:hover .wyl-icon{opacity:1;transform:translateY(-3px)}
.wyl-title{font-family:'Cormorant Garamond',serif;font-size:1.35rem;font-weight:400;letter-spacing:.02em;line-height:1.25;margin-bottom:14px;color:var(--ivory)}
.wyl-desc{font-size:.88rem;line-height:2;color:var(--muted);opacity:.82}

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
.url-more{font-size:.72rem;letter-spacing:.1em;color:var(--muted);text-align:center;margin-top:12px;opacity:.45}

/* ── Steps ── */
.steps-section{background:var(--deep);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.steps-wrap{max-width:1200px;margin:0 auto;padding:72px 64px}
.steps-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--border);margin-top:40px}
.step{background:var(--deep);padding:48px 32px;position:relative;overflow:hidden;transition:background .4s}
.step::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent);transform:scaleX(0);transition:transform .5s cubic-bezier(.23,1,.32,1)}
.step:hover{background:var(--card)}
.step:hover::after{transform:scaleX(1)}
.step-n{font-family:'Cormorant Garamond',serif;font-size:3.6rem;font-weight:300;color:rgba(200,168,75,.25);line-height:1;margin-bottom:16px;transition:color .3s}
.step:hover .step-n{color:rgba(200,168,75,.45)}
.step-title{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:400;line-height:1.15;margin-bottom:10px;color:var(--ivory)}
.step-desc{font-size:.92rem;line-height:1.85;color:var(--muted)}

/* ── URL Lock ── */
.url-lock{
  background:var(--deep);border-top:1px solid var(--border);border-bottom:1px solid var(--border);
  padding:72px 64px;
}
.url-lock-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 1.4fr;gap:64px;align-items:center}
.ul-title{font-family:'Cormorant Garamond',serif;font-size:clamp(1.8rem,3vw,2.6rem);font-weight:300;line-height:1.3;margin-top:14px}
.ul-title em{font-style:italic;color:var(--gold)}
.ul-body{font-size:1rem;line-height:1.9;color:var(--muted)}
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

/* ── Integrity ── */
.integrity-section{border-top:1px solid var(--border);padding:72px 64px;max-width:1200px;margin:0 auto}
.integrity-grid{display:grid;grid-template-columns:1fr 1fr;gap:44px;align-items:start;margin-top:40px}
.integrity-block{padding:36px 32px;border:1px solid var(--border);position:relative;overflow:hidden}
.integrity-block::before{content:'';position:absolute;top:0;left:0;bottom:0;width:2px;background:var(--gold-dim)}
.ib-label{font-size:.7rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);margin-bottom:10px;display:block}
.ib-title{font-family:'Cormorant Garamond',serif;font-size:1.35rem;font-weight:400;margin-bottom:10px;color:var(--ivory)}
.ib-body{font-size:.94rem;line-height:1.9;color:var(--muted)}
.ib-body strong{color:var(--ivory);font-weight:400}

/* ── Pricing / Offer ── */
#offer{padding:72px 64px;max-width:1200px;margin:0 auto}
.offer-intro{display:grid;grid-template-columns:1fr 1fr;gap:56px;margin-bottom:40px;align-items:end}
.offer-note{font-size:1rem;line-height:1.9;color:var(--muted)}
.offer-note strong{color:var(--ivory);font-weight:400}
.tier-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1px;background:var(--border)}
.tier{background:var(--deep);padding:52px 44px;position:relative;overflow:hidden;transition:background .4s}
.tier:hover{background:var(--card)}
.tier.prime{background:var(--card)}
.tier.prime::before{
  content:'';position:absolute;top:0;left:0;right:0;height:2px;
  background:linear-gradient(90deg,transparent,var(--gold),transparent);
}
.tier.starter{background:var(--deep);opacity:.9}
.tier.starter .tier-flag{color:var(--muted)}
.tier.starter .tier-price{font-size:2.8rem;color:var(--muted)}
.tier.starter .tier-price sup{color:var(--muted)}
.tier-flag{font-size:.7rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);margin-bottom:16px;display:block}
.tier-name{font-family:'Cormorant Garamond',serif;font-size:1.9rem;font-weight:300;margin-bottom:6px}
.tier-urls{font-size:.82rem;color:var(--muted);letter-spacing:.04em;margin-bottom:32px;padding-bottom:32px;border-bottom:1px solid var(--border)}
.tier-price{font-family:'Cormorant Garamond',serif;font-size:4.2rem;font-weight:300;color:var(--gold);line-height:1;margin-bottom:6px}
.tier-price sup{font-size:1.8rem;vertical-align:top;margin-top:14px;color:var(--gold-dim)}
.tier-price sub{font-size:1.1rem;color:var(--muted)}
.tier-commitment{font-size:.82rem;color:var(--muted);margin-bottom:36px;line-height:1.7}
.tier-features{list-style:none;display:flex;flex-direction:column;gap:10px;margin-bottom:40px}
.tier-features li{font-size:.92rem;color:var(--muted);padding-left:20px;position:relative;line-height:1.75}
.tier-features li::before{content:'';position:absolute;left:0;top:10px;width:9px;height:1px;background:var(--gold)}
.tier-features li strong{color:var(--ivory);font-weight:400}
.tier-features .soon{color:var(--gold);font-style:normal}
.tier-cta{
  display:block;text-align:center;font-size:.78rem;letter-spacing:.16em;text-transform:uppercase;
  padding:16px;text-decoration:none;transition:all .3s;
}
.tier .tier-cta{color:var(--gold);border:1px solid var(--gold-dim)}
.tier .tier-cta:hover,.tier.prime .tier-cta:hover{background:var(--gold-lt);color:var(--bg);border-color:var(--gold-lt)}
.tier.prime .tier-cta{background:var(--gold);color:var(--bg);border:1px solid var(--gold)}
.tier-book{
  display:block;width:100%;margin-top:10px;padding:12px 16px;background:transparent;
  border:1px solid var(--border);color:var(--muted);font-size:.78rem;font-weight:400;
  letter-spacing:.12em;text-transform:uppercase;cursor:pointer;transition:all .3s;
  font-family:'DM Sans',sans-serif;
}
.tier-book:hover{border-color:var(--gold-dim);color:var(--gold)}
.tier-gated{
  margin-top:12px;padding:14px 16px;border:1px solid var(--border);
  font-size:.82rem;line-height:1.7;color:var(--muted);
  display:flex;align-items:flex-start;gap:10px;
}
.tier-gated-icon{color:var(--gold);flex-shrink:0;margin-top:1px;font-size:.8rem}
.tier-gated strong{color:var(--ivory);font-weight:400}

/* ── Proof strip ── */
.proof-strip{border-top:1px solid var(--border);border-bottom:1px solid var(--border);display:grid;grid-template-columns:repeat(4,1fr)}
.proof-item{padding:36px 28px;text-align:center;border-right:1px solid var(--border);transition:background .3s}
.proof-item:last-child{border-right:none}
.proof-item:hover{background:rgba(200,168,75,.03)}
.proof-icon{font-size:1.5rem;color:var(--gold);margin-bottom:12px;opacity:.75}
.proof-label{font-size:.78rem;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);line-height:1.6}
.proof-label strong{display:block;font-size:.92rem;color:var(--ivory);font-weight:400;letter-spacing:.06em;text-transform:none;margin-bottom:3px}

/* ── Roadmap ── */
.roadmap{border-top:1px solid var(--border);padding:72px 64px;max-width:1200px;margin:0 auto}
.rm-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:28px;margin-top:36px}
.rm-item{padding:28px 24px;border:1px solid var(--border);position:relative;overflow:hidden;transition:background .3s}
.rm-item:hover{background:var(--deep)}
.rm-item::after{
  content:'Coming Soon';position:absolute;top:14px;right:12px;
  font-size:.54rem;letter-spacing:.18em;text-transform:uppercase;
  color:var(--gold-dim);border:1px solid var(--gold-dim);padding:3px 8px;
}
.rm-title{font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:400;margin-bottom:8px}
.rm-desc{font-size:.9rem;line-height:1.85;color:var(--muted)}

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
.fg input::placeholder,.fg textarea::placeholder{color:#222}
.fg input:focus,.fg textarea:focus,.fg select:focus{border-color:var(--gold-dim)}
.fg textarea{resize:vertical;min-height:100px}
.fg select option{background:var(--bg)}
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

/* ── Mobile ── */
@media(max-width:900px){
  html{font-size:17px;-webkit-text-size-adjust:100%}
  body{-webkit-overflow-scrolling:touch}
  nav{padding:14px 20px}nav.stuck{padding:10px 20px}.nav-link{display:none}
  .nav-btn{display:none}
  .nav-account{display:inline-flex;padding:12px 24px;font-size:.78rem;letter-spacing:.14em;border-radius:3px;min-height:44px;align-items:center}
  .nav-account-full{display:none}.nav-account-short{display:inline}
  #hero{padding:110px 24px 60px;min-height:auto}
  .hero-h1{font-size:clamp(2.6rem,9vw,3.8rem);max-width:100%;margin-bottom:20px;line-height:1.05}
  .hero-pre{margin-bottom:16px;gap:4px}
  .hp-kicker{font-size:clamp(1.45rem,5.5vw,2rem)}
  .hp-question{font-size:clamp(1.15rem,4vw,1.55rem);padding-left:42px}
  .hero-p{margin-bottom:32px}
  .hero-p-line{font-size:1.15rem;padding:5px 0}
  .hp-emphasis{font-size:clamp(1.5rem,6vw,2rem);padding-top:14px;padding-bottom:10px}
  .hp-strong{font-size:1.15rem}
  .hero-actions{flex-direction:column;gap:16px;width:100%}
  .btn-primary{width:100%;text-align:center;padding:16px 24px}
  .btn-ghost{text-align:center}
  .hero-orb{display:none}
  .hero-scroll{left:20px;bottom:32px}
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
  .integrity-grid{grid-template-columns:1fr}
  .integrity-block{padding:28px 24px}
  .rm-grid{grid-template-columns:1fr}
  .rm-item{padding:22px 20px}
  .url-lock-inner{grid-template-columns:1fr;gap:28px}
  .ul-title{font-size:clamp(1.5rem,5vw,2rem)}
  .ul-lead{font-size:.95rem}
  .ul-states{grid-template-columns:1fr}
  .ul-state{padding:18px 18px}
  .ul-note{padding:14px 16px}
  .proof-strip{grid-template-columns:1fr 1fr}
  .proof-item{padding:24px 16px}
  .tier{padding:40px 28px}
  .tier-name{font-size:1.6rem}
  .tier-price{font-size:3.2rem}
  .tier-price sup{font-size:1.4rem}
  .frow{grid-template-columns:1fr}
  .fg input,.fg textarea,.fg select{font-size:16px;padding:14px 16px}
  .fsub{width:100%;text-align:center;padding:16px 24px}
  .s-h{font-size:clamp(1.7rem,6vw,2.4rem)}
  .s-eye{font-size:.7rem;letter-spacing:.2em}
  .s-p{font-size:.96rem}
  .c-meta{gap:16px;margin-top:24px}
  .audience-section,.url-lock,.wyl-section,.url-section{padding:48px 24px}
  .steps-wrap,.integrity-section{padding:48px 24px}
  #offer,.roadmap,#contact,footer{padding:48px 24px}
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
  #hero{padding:100px 20px 48px}
  .hero-h1{font-size:clamp(2.8rem,11vw,3.8rem);line-height:1.04}
  .hero-pre{gap:3px;margin-bottom:14px}
  .hp-kicker{font-size:clamp(1.3rem,6vw,1.75rem)}
  .hp-question{font-size:clamp(1.05rem,4.5vw,1.35rem);padding-left:42px}
  .hero-p{margin-bottom:28px}
  .hero-p-line{font-size:1.05rem;padding:5px 0}
  .hp-emphasis{font-size:clamp(1.3rem,6vw,1.7rem);padding-top:12px;padding-bottom:8px}
  .hp-strong{font-size:1.05rem}
  .hero-scroll{display:none}
  .wyl-icon{font-size:2.2rem;margin-bottom:20px}
  .wyl-card{padding:36px 28px}
  .wyl-title{font-size:1.3rem}
  .proof-icon{font-size:1.8rem;margin-bottom:10px}
  .wyl-grid,.steps-grid{grid-template-columns:1fr}
  .proof-strip{grid-template-columns:1fr}
  .stmt-quote{padding:24px 18px}
  .stmt-quote .sq-text{font-size:clamp(1.1rem,4vw,1.4rem)}
  .stmt-quote::before,.stmt-quote::after{left:18px;right:18px}
  .audience-section,.url-lock,.wyl-section,.url-section{padding:36px 20px}
  .steps-wrap,.integrity-section{padding:36px 20px}
  #offer,.roadmap,#contact,footer{padding:36px 20px}
  .aud-card{padding:32px 18px}
  .tier{padding:32px 20px}
  .tier-price{font-size:2.6rem}
  .offer-note{font-size:.88rem}
  .contact-inner{gap:28px}
  .gate-box{padding:28px 18px}
  .logo-seo{font-size:1.28rem}.logo-ai{font-size:1.5rem}.logo-co{font-size:1.1rem}
}
</style>
</head>
<body>

<!-- ════════════ NAV ════════════ -->
<nav id="nav">
  <a href="#" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <div class="nav-right">
    <a href="#wyl" class="nav-link">The System</a>
    <a href="#offer" class="nav-link">Licensing</a>
    <a href="/dashboard" class="nav-btn nav-account" style="background:linear-gradient(90deg,var(--gold),var(--gold-lt));color:var(--deep);box-shadow:0 2px 12px 0 rgba(200,168,75,.13);font-weight:500;letter-spacing:.18em;"><span class="nav-account-full">Sign Up / Account</span><span class="nav-account-short">Account</span></a>
    <a href="#contact" class="nav-btn">Request Licensing Access</a>
  </div>
</nav>

<!-- ════════════ HERO ════════════ -->
<section id="hero">
  <div class="hero-grid"></div>
  <div class="hero-orb"></div>

  <div class="hero-pre">
    <p class="hp-kicker">Someone is searching for what you do.</p>
    <p class="hp-question">Are you showing up?</p>
  </div>

  <h1 class="hero-h1">
    Own the rankings.<br>
    <em>In every city.</em>
  </h1>

  <div class="hero-p">
    <p class="hero-p-line">Your competitor does — because they have a page built for that exact search.</p>
    <p class="hero-p-line hp-emphasis">You don't.</p>
    <p class="hero-p-line">SEOAIco builds and manages every service and location page your business needs.</p>
    <p class="hero-p-line">So you show up in every city, for every service, every time someone searches.</p>
    <p class="hero-p-line hp-strong">You're on top.</p>
  </div>

  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Request Licensing Access</a>
    <a href="#wyl" class="btn-ghost">See the System</a>
  </div>
</section>

<!-- ════════════ PROOF STRIP ════════════ -->
<div class="proof-strip r">
  <div class="proof-item">
    <div class="proof-icon">◈</div>
    <div class="proof-label"><strong>White-Label Licensed</strong>Your brand. Your clients. Your margin.</div>
  </div>
  <div class="proof-item">
    <div class="proof-icon">⬡</div>
    <div class="proof-label"><strong>Built for Search Engines</strong>Structure-first. Not just content.</div>
  </div>
  <div class="proof-item">
    <div class="proof-icon">◻</div>
    <div class="proof-label"><strong>Controlled Page Inventory</strong>Licensed capacity. Protected growth.</div>
  </div>
  <div class="proof-item">
    <div class="proof-icon">◈</div>
    <div class="proof-label"><strong>Agencies &amp; Businesses</strong>Agencies. Operators. Businesses ready to grow.</div>
  </div>
</div>

<div class="gold-rule"></div>

<!-- ════════════ STATEMENT ════════════ -->
<div class="statement r">
  <div class="stmt-quote">
    <span class="sq-mark">&ldquo;</span>
    <p class="sq-text">It's not because your competitor is better at what they do. It's because their website has a page built for that search — <strong>and yours doesn't.</strong></p>
    <span class="sq-rule"></span>
  </div>
  <div class="stmt-body">
    <p>Google and AI search engines all work the same way: they look for a page that matches what someone typed. If that page doesn't exist on your site, you don't show up — no matter how good your business is.</p>
    <p>SEOAIco builds that page. Then builds one for every service you offer, in every city you serve. Each page is written around the exact words your customers search — with FAQs, local business details, and every page linked together so the whole system gets stronger over time.</p>
    <p><strong>You don't touch any of it.</strong> We build it, manage it, and keep it current. You lease the system. You own the rankings it earns.</p>
  </div>
</div>

<div class="gold-rule"></div>

<!-- ════════════ URL DEMO — LICENSED SEARCH FOOTPRINT ════════════ -->
<div class="url-section">
  <div class="url-inner">
    <div>
      <p class="s-eye r">Licensed Search Footprint</p>
      <h2 class="s-h r">Thousands of search<br>targets. <em>One structured</em><br>system.</h2>
      <p class="s-p r">Each page is built around a specific search — combining the right service, city, and keywords so Google knows exactly what your page is about. <strong>Not pages thrown together in bulk. A complete system built for search coverage.</strong></p>
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
    <div class="stmt-quote r" style="text-align:center;max-width:960px;margin:0 auto 48px">
      <span class="sq-mark">&ldquo;</span>
      <p class="sq-text">Imagine 500 pages — each targeting a different service in a different city — all <strong>live, ranking, and bringing in leads… while you sleep.</strong> That's what your competitors just turned on.</p>
      <span class="sq-rule"></span>
    </div>
    <p class="s-eye r">Who This Is For</p>
    <h2 class="s-h r">Two different starting points.<br><em>One infrastructure.</em></h2>
    <div class="audience-grid">

      <div class="aud-card r">
        <span class="aud-tag">For Agencies</span>
        <h3 class="aud-title">Give every client a <em>full local SEO engine</em> — overnight.</h3>
        <p class="aud-body">Your clients need to rank in dozens of cities — not just where their office is. SEOAIco gives you a ready-to-go system: hundreds of AI-written service + location pages, deployed <strong>under your brand, at your markup</strong>, with search data, linking, and local targeting handled automatically.</p>
        <ul class="aud-list">
          <li><strong>New revenue stream</strong> — offer local SEO at scale as a premium service without hiring more writers</li>
          <li><strong>White-label, your brand</strong> — zero SEOAIco branding, clients see only your work</li>
          <li><strong>One licence, all your clients</strong> — deploy across your entire book of business from one subscription</li>
          <li><strong>AI content unique to each city &amp; service</strong> — not templates with a city name swapped in</li>
          <li><strong>Reduce churn</strong> — clients stay because their rankings depend on your licence being active</li>
          <li><strong>Upsell built in</strong> — add more pages as clients grow into new markets</li>
          <li><strong>Faster delivery</strong> — deploy 500+ pages in days, not months of writing</li>
        </ul>
        <a href="#offer" class="aud-cta">See Licensing Plans &rarr;</a>
      </div>

      <div class="aud-card r">
        <span class="aud-tag">For CEOs &amp; Business Owners</span>
        <h3 class="aud-title">Finally rank in <em>every city</em><br>you actually serve.</h3>
        <p class="aud-body">You've spent years paying for SEO that covers a handful of pages. Meanwhile, your competitors show up in 100+ cities — because they have the pages. <strong>SEOAIco builds those pages for you:</strong> every service, every location, every search your customers are typing. AI-written, tagged for Google, and ranking.</p>
        <ul class="aud-list">
          <li><strong>Leads from cities you've never ranked in</strong> — cover every service and location combination automatically</li>
          <li><strong>Stop overpaying for SEO</strong> — get 500 pages for the cost of your agency writing 10</li>
          <li><strong>AI content + FAQ data + local business details</strong> on every single page</li>
          <li><strong>Unique content for every city</strong> — Google sees each page as genuinely local, not a copy</li>
          <li><strong>Show up in AI search results</strong> — structured data feeds ChatGPT, Gemini, and AI Overviews</li>
          <li><strong>You own the URLs</strong> — pages live on your domain, your site, your brand</li>
          <li><strong>Gets stronger over time</strong> — rankings improve the longer the system runs, not weaker</li>
        </ul>
        <a href="#offer" class="aud-cta">See Licensing Plans &rarr;</a>
      </div>

    </div>
  </div>
</section>

<!-- ════════════ WYL — FEATURES / WOW ════════════ -->
<section id="wyl">
  <div class="wyl-section">
    <div class="wyl-inner">
      <p class="s-eye r">What You're Actually Licensing</p>
      <h2 class="s-h r">The AI engine behind<br><em>every page on your site.</em></h2>
      <p class="s-p r" style="max-width:640px">SEOAIco is the software behind every service and location page — handling headlines, FAQs, local business data for Google, page descriptions, internal links, and AI-written content across 100+ cities. <strong>You license the engine. It keeps your pages ranking.</strong></p>
      <div class="wyl-grid">
        <div class="wyl-card r">
          <span class="wyl-icon">⬡</span>
          <h3 class="wyl-title">Precision Page Assembly</h3>
          <p class="wyl-desc">Every page is constructed through an automated pipeline — pairing a targeted headline, service-specific body, FAQ signal, and conversion elements to the exact search intent it targets. Zero manual composition.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◈</span>
          <h3 class="wyl-title">Topical Authority Architecture</h3>
          <p class="wyl-desc">Related services, adjacent cities, category hubs, and breadcrumb trails are woven into every page at build time — forming a structured link graph that signals topical authority and domain depth to search engines.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">⊹</span>
          <h3 class="wyl-title">Structured Data Injection</h3>
          <p class="wyl-desc">Rich business markup, FAQ schema, and breadcrumb signals are generated and embedded on every page automatically — no manual entry, no missed opportunities for AI-powered search visibility.</p>
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
          <p class="wyl-desc">Every page is delivered with clean URL structure, stable server-side output, and proper fallback logic — ensuring consistent, indexable rendering whether Googlebot requests JavaScript or not.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◼</span>
          <h3 class="wyl-title">Zero-Attribution Deployment</h3>
          <p class="wyl-desc">The entire system deploys under your brand identity. No SEOAIco attribution in code, content, or metadata. Your clients see your agency's work. The infrastructure behind it remains invisible.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◈</span>
          <h3 class="wyl-title">Capacity-Controlled Growth</h3>
          <p class="wyl-desc">Your licence defines a precise page inventory. Expansion is planned and incremental — protecting per-page quality and ensuring the system scales without diluting the search authority already earned.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ════════════ STEPS — PROCESS (TRANSITION) ════════════ -->
<section id="how">
  <div class="steps-section">
    <div class="steps-wrap">
      <p class="s-eye r">The Process</p>
      <h2 class="s-h r">Simple setup.<br><em>Controlled growth.</em></h2>
      <div class="steps-grid">
        <div class="step r">
          <div class="step-n">01</div>
          <h3 class="step-title">Map Your Search Coverage</h3>
          <p class="step-desc">We map out your services, target cities, and related topics — laying out the full plan for your pages before anything gets built.</p>
        </div>
        <div class="step r">
          <div class="step-n">02</div>
          <h3 class="step-title">Set Up Your System</h3>
          <p class="step-desc">The system is configured for your brand, linking structure, local content details, and search data — all within your licensed page count.</p>
        </div>
        <div class="step r">
          <div class="step-n">03</div>
          <h3 class="step-title">Deploy Under Your Brand</h3>
          <p class="step-desc">Pages are delivered for your site via API or file export. White-label from top to bottom — no SEOAIco branding anywhere.</p>
        </div>
        <div class="step r">
          <div class="step-n">04</div>
          <h3 class="step-title">Grow Your Footprint</h3>
          <p class="step-desc">As rankings build and you reach more markets, your licence lets you add new services, cities, and topics — all within a protected, controlled system.</p>
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
          <div class="ul-state-desc">AI content, search data, FAQ sections, local business details, internal links, and page descriptions all active. Google and AI search engines reading and ranking your pages across every city you serve. <strong>Your SEO gets stronger month over month.</strong></div>
        </div>
        <div class="ul-state inactive">
          <span class="ul-state-label">Licence Lapsed</span>
          <div class="ul-state-title">Pages revert — company name &amp; phone number only</div>
          <div class="ul-state-desc">All AI content, search data, structured details, and internal links removed. Pages stay on your site, but <strong>rankings drop, leads stop, and the SEO advantage disappears.</strong> Reactivate anytime to bring everything back.</div>
        </div>
      </div>
      <div class="ul-note">
        <p><strong>Need more pages?</strong> Upgrade to the next tier — your existing pages carry over.</p>
        <p><strong>Already have pages built outside SEOAIco?</strong> You can bring them in on the 10,000 page tier. Full search data, linking, and AI content across your entire site.</p>
      </div>
    </div>
  </div>
</div>
</section>

<!-- ════════════ INTEGRITY ════════════ -->
<section>
  <div class="integrity-section">
    <p class="s-eye r">Licensing &amp; Page Protection</p>
    <h2 class="s-h r">Protected pages need<br><em>an active licence.</em></h2>
    <div class="integrity-grid r">
      <div class="integrity-block">
        <span class="ib-label">Active Licence Holders</span>
        <h3 class="ib-title">Your pages are protected within your licence</h3>
        <p class="ib-body">Pages built through an active SEOAIco licence are tied to that licence. <strong>Growth, updates, and system upkeep are all maintained within your licensed page count.</strong> More coverage requires a tier upgrade — simple, step-by-step, and controlled.</p>
      </div>
      <div class="integrity-block">
        <span class="ib-label">Legacy &amp; Pre-Licence Builds</span>
        <h3 class="ib-title">Unlicensed builds need a fresh start</h3>
        <p class="ib-body">Pages built outside SEOAIco aren't eligible for protected growth within the licensed system. <strong>To bring an existing site into the licensed system — with full growth rights, search data, and protected pages — you'll need to start at the 10,000 page licence tier.</strong></p>
      </div>
    </div>
  </div>
</section>

<!-- ════════════ PRICING ════════════ -->
<section id="offer">
  <div class="offer-intro r">
    <div>
      <p class="s-eye">The Licence</p>
      <h2 class="s-h">Three tiers.<br><em>One system.</em><br>Pick your level.</h2>
    </div>
    <div class="offer-note">
      <p>We review every application individually — this isn't a self-serve checkout. Once you're approved, the licence controls everything: the AI content, search data, and page system your site runs on. <strong>Built for agencies and businesses serious about owning their search results.</strong></p>
      <p style="margin-top:12px;font-size:.84rem">Need more pages than your current tier allows? Upgrade to the next level — your existing pages carry over.</p>
    </div>
  </div>

  <div class="tier-grid-3 r">

    <div class="tier starter">
      <span class="tier-flag">Starter — By Application</span>
      <h3 class="tier-name">Entry Access</h3>
      <div class="tier-urls">Limited page capacity · Reviewed &amp; approved individually</div>
      <div class="tier-price">Apply</div>
      <div class="tier-commitment">Entry-level access for qualifying businesses or agencies. Capacity and pricing confirmed on application.</div>
      <ul class="tier-features">
        <li>Page building within your approved capacity</li>
        <li>Search data added automatically</li>
        <li>Smart internal linking</li>
        <li>Upgrades to 5K or 10K tier as you grow</li>
      </ul>
      <div class="tier-gated">
        <span class="tier-gated-icon">◈</span>
        <span><strong>Not publicly priced.</strong> Starter access is granted case-by-case. Apply below — we'll assess fit and confirm capacity.</span>
      </div>
      <a href="#contact" class="tier-cta" style="margin-top:20px">Apply for Starter Access</a>
      <button class="tier-book" onclick="window.dispatchEvent(new CustomEvent('open-booking', {detail: {id: 1, duration: 15, name: 'Free Discovery Call'}}))">Book a Free Discovery Call</button>
    </div>

    <div class="tier">
      <span class="tier-flag">Agency / Business Licence — Foundation</span>
      <h3 class="tier-name">5,000 Page Licence</h3>
      <div class="tier-urls">Up to 5,000 licensed pages — agencies or single-business deployment</div>
      <div class="tier-price"><sup>$</sup>2,995<sub>/mo</sub></div>
      <div class="tier-commitment">3-month minimum engagement. Month-to-month thereafter.</div>
      <ul class="tier-features">
        <li><strong>Licensed page inventory</strong> — 5,000 active pages</li>
        <li><strong>White-label — your brand only</strong></li>
        <li>Automatic page building for every service + city</li>
        <li>Search data &amp; local business details on every page</li>
        <li>Smart internal linking</li>
        <li>Grow into new topics &amp; cities within your licence</li>
        <li>API or file export delivery</li>
        <li>Dashboard onboarding <em class="soon">(coming soon)</em></li>
      </ul>
      <a href="#contact" class="tier-cta">Request 5K Licensing Details</a>
      <button class="tier-book" onclick="window.dispatchEvent(new CustomEvent('open-booking', {detail: {id: 1, duration: 15, name: 'Free Discovery Call'}}))">Book a Free Discovery Call</button>
    </div>

    <div class="tier prime">
      <span class="tier-flag">Agency / Business Licence — Preferred</span>
      <h3 class="tier-name">10,000 Page Licence</h3>
      <div class="tier-urls">Up to 10,000 licensed pages — full portfolio or large-scale deployment</div>
      <div class="tier-price"><sup>$</sup>4,799<sub>/mo</sub></div>
      <div class="tier-commitment">Priority processing. Dedicated account contact. 3-month minimum, then month-to-month.</div>
      <ul class="tier-features">
        <li>Everything in the 5,000 page licence</li>
        <li><strong>Extended page inventory</strong> — 10,000 pages</li>
        <li><strong>Required for bringing in existing sites</strong> &amp; unlicensed builds</li>
        <li>Priority deployment</li>
        <li>Dedicated account contact</li>
        <li>Early access to dashboard &amp; reporting <em class="soon">(coming soon)</em></li>
        <li>First access to new industries &amp; features</li>
      </ul>
      <a href="#contact" class="tier-cta">Request 10K Licensing Details</a>
      <button class="tier-book" onclick="window.dispatchEvent(new CustomEvent('open-booking', {detail: {id: 3, duration: 60, name: 'Agency License Review'}}))">Review My Agency License</button>
    </div>

  </div>
</section>

<!-- ════════════ ROADMAP — PROOF / FUTURE ════════════ -->
<div class="roadmap">
  <p class="s-eye r">On the Roadmap</p>
  <h2 class="s-h r">The platform grows<br><em>with the licence base.</em></h2>
  <div class="rm-grid">
    <div class="rm-item r">
      <h3 class="rm-title">Agency Dashboard</h3>
      <p class="rm-desc">A self-serve dashboard for managing your licensed pages, checking deployment status, and adding new client sites — no developer needed.</p>
    </div>
    <div class="rm-item r">
      <h3 class="rm-title">Per-URL Search Tracking</h3>
      <p class="rm-desc">Search ranking tracking at the individual page level — giving agencies the reports they need to show clients the value of their pages.</p>
    </div>
    <div class="rm-item r">
      <h3 class="rm-title">Reseller Sub-Licensing</h3>
      <p class="rm-desc">Bring client accounts into the system under your brand. You control access, pricing, and capacity allocation — the infrastructure remains SEOAIco's.</p>
    </div>
  </div>
</div>

<!-- ════════════ CONTACT ════════════ -->
<section id="contact">
  <div class="contact-inner">
    <div>
      <p class="s-eye r">Request Licensing Details</p>
      <h2 class="s-h r">Tell us where you are — and where you need <em>search to take you.</em></h2>
      <p class="s-p r">Whether you're an agency looking for a scalable SEO system or a business that's hit the ceiling with traditional SEO — licensing access is reviewed individually. We'll look at your market, your goals, and the right licence level for your situation.</p>
      <div class="c-meta r">
        <div class="cm"><label>Licensing Model</label><span>Reviewed individually — not automated</span></div>
        <div class="cm"><label>Commitment</label><span>3-month minimum, then month-to-month</span></div>
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
            <option value="starter" @selected(old('tier') === 'starter')>Starter — apply for access &amp; pricing</option>
            <option value="5k" @selected(old('tier') === '5k')>5,000 URLs — $2,995/mo</option>
            <option value="10k" @selected(old('tier') === '10k')>10,000 URLs — $4,799/mo</option>
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
        <label for="message">Tell us about your current SEO situation and expansion goals</label>
        <textarea id="message" name="message" placeholder="We're an agency managing SEO for local service businesses / We're a business that's hit a growth ceiling and need structured market coverage across…" required>{{ old('message') }}</textarea>
        @error('message') <span class="field-error">{{ $message }}</span> @enderror
      </div>

      <input type="text" name="website_url" style="display:none" tabindex="-1" autocomplete="off" aria-hidden="true">

      <button type="submit" class="fsub" id="submitBtn">Submit Licensing Enquiry</button>
    </form>
  </div>
</section>

<!-- ════════════ BACK TO TOP ════════════ -->
<button class="btt" id="btt" aria-label="Back to top">
  <svg viewBox="0 0 24 24"><path d="M12 4l-8 8h5v8h6v-8h5z"/></svg>
</button>

<!-- ════════════ PAYWALL GATE (placeholder — Stripe checkout ready) ════════════ -->
<div class="gate-overlay" id="gateOverlay">
  <div class="gate-box">
    <div class="gate-icon">◈</div>
    <span class="gate-badge">Licensed Access Required</span>
    <h2 class="gate-title">Activate your<br><em>ranking infrastructure.</em></h2>
    <p class="gate-desc">Select a licence tier to unlock full access to the SEOAIco platform — <strong>dashboard, deployment tools, and URL inventory management.</strong></p>
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
    <a href="#contact" class="gate-cta" id="gateCta">Request Licensing Access</a>
    <button class="gate-skip" id="gateSkip">Continue browsing</button>
  </div>
</div>

<!-- ════════════ FOOTER — privacy/terms at very bottom ════════════ -->
<footer>
  <div class="footer-main">
    <a href="#" class="logo">
      <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
    </a>
    <a href="/dashboard" class="footer-account-btn" style="background:linear-gradient(90deg,var(--gold),var(--gold-lt));color:var(--deep);padding:10px 28px;border-radius:24px;font-weight:500;letter-spacing:.18em;text-decoration:none;box-shadow:0 2px 12px 0 rgba(200,168,75,.13);margin:0 18px;">Sign Up / Account</a>
    <span class="footer-copy">&copy; 2026 SEOAIco. Licensed Ranking Infrastructure.</span>
  </div>
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

  document.getElementById('inquiryForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = 'Submitting…';
  });

  // ── Back to Top ──
  const btt = document.getElementById('btt');
  window.addEventListener('scroll', () => btt.classList.toggle('show', scrollY > 600), {passive:true});
  btt.addEventListener('click', () => window.scrollTo({top:0, behavior:'smooth'}));

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
</script>

@include('components.booking-modal')

</body>
</html>
