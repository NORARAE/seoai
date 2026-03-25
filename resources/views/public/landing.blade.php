<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SEOAIco — Licensed Ranking Infrastructure for Agencies & Growing Businesses</title>
<meta name="description" content="SEOAIco licenses structured ranking infrastructure — not content. Agencies and growing businesses gain a controlled, expandable search footprint that compounds over time.">
<link rel="canonical" href="{{ url('/') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;--deep:#0b0b0b;--card:#101010;--border:#1a1a1a;
  --gold:#c8a84b;--gold-lt:#e2c97d;--gold-dim:#9a7a30;
  --white:#ffffff;--ivory:#ede8de;--muted:#8a8a8a;--warn:#b84040;
}
html{scroll-behavior:smooth;font-size:19px}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;overflow-x:hidden;line-height:1.75}
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
}
.nav-btn:hover{background:var(--gold-lt)}

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
.hero-kicker{
  font-size:.78rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold);
  margin-bottom:28px;display:flex;align-items:center;gap:14px;
  opacity:0;animation:up .7s .2s forwards;
}
.hero-kicker::before{content:'';width:28px;height:1px;background:var(--gold)}
.hero-h1{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(3rem,7vw,6.8rem);font-weight:300;line-height:1.06;
  margin-bottom:32px;max-width:780px;opacity:0;animation:up .9s .35s forwards;
}
.hero-h1 em{font-style:italic;color:var(--gold)}
.hero-p{
  font-size:1.1rem;line-height:1.85;color:var(--muted);max-width:560px;margin-bottom:44px;
  opacity:0;animation:up .85s .5s forwards;
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

@keyframes up{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}

/* ── Shared section helpers ── */
.gold-rule{height:1px;background:linear-gradient(to right,transparent,var(--gold-dim),transparent)}
.s-eye{font-size:.76rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold);margin-bottom:12px;display:flex;align-items:center;gap:14px}
.s-eye::before{content:'';width:28px;height:1px;background:var(--gold)}
.s-h{font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem,4vw,3.6rem);font-weight:400;line-height:1.08;margin-bottom:16px}
.s-h em{font-style:italic;color:var(--gold)}
.s-p{font-size:1rem;line-height:1.85;color:var(--muted)}
.s-p strong{color:var(--ivory);font-weight:400}

/* ── Statement ── */
.statement{
  padding:56px 64px;display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:center;
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
.stmt-body p{font-size:1rem;line-height:1.85;color:var(--muted);margin-bottom:14px}
.stmt-body p:last-child{margin-bottom:0}
.stmt-body strong{color:var(--ivory);font-weight:400}
.stmt-split{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px}
.stmt-split-card{padding:22px 24px;border-left:2px solid var(--gold-dim);background:rgba(200,168,75,.02)}
.stmt-split-card .split-tag{font-size:.68rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);margin-bottom:8px;display:block}
.stmt-split-card .split-body{font-size:.94rem;line-height:1.8;color:var(--muted)}
.stmt-split-card .split-body strong{color:var(--ivory);font-weight:400}

/* ── Audience ── */
.audience-section{border-top:1px solid var(--border);padding:56px 64px;max-width:1200px;margin:0 auto}
.audience-grid{display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--border);margin-top:36px}
.aud-card{background:var(--deep);padding:48px 40px;position:relative;overflow:hidden;transition:background .4s}
.aud-card:hover{background:var(--card)}
.aud-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:2px;
  background:linear-gradient(90deg,transparent,var(--gold-dim),transparent);
  transition:background .4s;
}
.aud-card:hover::before{background:linear-gradient(90deg,transparent,var(--gold),transparent)}
.aud-tag{font-size:.78rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);margin-bottom:16px;display:block}
.aud-title{font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:300;line-height:1.2;margin-bottom:16px}
.aud-title em{font-style:italic;color:var(--gold)}
.aud-body{font-size:1.05rem;line-height:1.85;color:var(--muted);margin-bottom:24px}
.aud-body strong{color:var(--ivory);font-weight:400}
.aud-list{list-style:none;display:flex;flex-direction:column;gap:11px}
.aud-list li{font-size:1rem;color:var(--muted);padding-left:20px;position:relative;line-height:1.7}
.aud-list li::before{content:'';position:absolute;left:0;top:12px;width:10px;height:1px;background:var(--gold)}
.aud-list li strong{color:var(--ivory);font-weight:400}

/* ── WYL (What You're Licensing) ── */
.wyl-section{border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:56px 64px;background:var(--deep)}
.wyl-inner{max-width:1200px;margin:0 auto}
.wyl-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--border);margin-top:36px}
.wyl-card{background:var(--deep);padding:32px 28px;position:relative;overflow:hidden;transition:background .4s}
.wyl-card:hover{background:var(--card)}
.wyl-card::after{
  content:'';position:absolute;bottom:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,var(--gold-dim),transparent);
  transform:scaleX(0);transition:transform .5s cubic-bezier(.23,1,.32,1);
}
.wyl-card:hover::after{transform:scaleX(1)}
.wyl-icon{font-size:.9rem;color:var(--gold);opacity:.6;margin-bottom:16px;display:block;transition:opacity .3s}
.wyl-card:hover .wyl-icon{opacity:1}
.wyl-title{font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:400;margin-bottom:8px;color:var(--ivory)}
.wyl-desc{font-size:.88rem;line-height:1.8;color:var(--muted)}

/* ── URL demo section ── */
.url-section{background:var(--deep);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:56px 64px}
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
.steps-wrap{max-width:1200px;margin:0 auto;padding:56px 64px}
.steps-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--border);margin-top:36px}
.step{background:var(--deep);padding:44px 32px;position:relative;overflow:hidden;transition:background .4s}
.step::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent);transform:scaleX(0);transition:transform .5s cubic-bezier(.23,1,.32,1)}
.step:hover{background:var(--card)}
.step:hover::after{transform:scaleX(1)}
.step-n{font-family:'Cormorant Garamond',serif;font-size:3.6rem;font-weight:300;color:rgba(200,168,75,.25);line-height:1;margin-bottom:16px;transition:color .3s}
.step:hover .step-n{color:rgba(200,168,75,.45)}
.step-title{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:400;line-height:1.15;margin-bottom:10px;color:var(--ivory)}
.step-desc{font-size:.9rem;line-height:1.8;color:var(--muted)}

/* ── URL Lock ── */
.url-lock{
  background:var(--deep);border-top:1px solid var(--border);border-bottom:1px solid var(--border);
  padding:56px 64px;
}
.url-lock-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 1.4fr;gap:64px;align-items:center}
.ul-title{font-family:'Cormorant Garamond',serif;font-size:clamp(1.8rem,3vw,2.6rem);font-weight:300;line-height:1.3;margin-top:14px}
.ul-title em{font-style:italic;color:var(--gold)}
.ul-body{font-size:.96rem;line-height:1.85;color:var(--muted)}
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
.integrity-section{border-top:1px solid var(--border);padding:56px 64px;max-width:1200px;margin:0 auto}
.integrity-grid{display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:start;margin-top:36px}
.integrity-block{padding:36px 32px;border:1px solid var(--border);position:relative;overflow:hidden}
.integrity-block::before{content:'';position:absolute;top:0;left:0;bottom:0;width:2px;background:var(--gold-dim)}
.ib-label{font-size:.7rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);margin-bottom:10px;display:block}
.ib-title{font-family:'Cormorant Garamond',serif;font-size:1.35rem;font-weight:400;margin-bottom:10px;color:var(--ivory)}
.ib-body{font-size:.92rem;line-height:1.85;color:var(--muted)}
.ib-body strong{color:var(--ivory);font-weight:400}

/* ── Pricing / Offer ── */
#offer{padding:64px 64px;max-width:1200px;margin:0 auto}
.offer-intro{display:grid;grid-template-columns:1fr 1fr;gap:56px;margin-bottom:40px;align-items:end}
.offer-note{font-size:.96rem;line-height:1.85;color:var(--muted)}
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
.tier-features li{font-size:.9rem;color:var(--muted);padding-left:20px;position:relative;line-height:1.6}
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
.proof-icon{font-size:1.1rem;color:var(--gold);margin-bottom:10px;opacity:.7}
.proof-label{font-size:.78rem;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);line-height:1.6}
.proof-label strong{display:block;font-size:.92rem;color:var(--ivory);font-weight:400;letter-spacing:.06em;text-transform:none;margin-bottom:3px}

/* ── Roadmap ── */
.roadmap{border-top:1px solid var(--border);padding:56px 64px;max-width:1200px;margin:0 auto}
.rm-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:28px;margin-top:36px}
.rm-item{padding:28px 24px;border:1px solid var(--border);position:relative;overflow:hidden;transition:background .3s}
.rm-item:hover{background:var(--deep)}
.rm-item::after{
  content:'Coming Soon';position:absolute;top:14px;right:12px;
  font-size:.54rem;letter-spacing:.18em;text-transform:uppercase;
  color:var(--gold-dim);border:1px solid var(--gold-dim);padding:3px 8px;
}
.rm-title{font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:400;margin-bottom:8px}
.rm-desc{font-size:.88rem;line-height:1.8;color:var(--muted)}

/* ── Contact ── */
#contact{background:var(--deep);border-top:1px solid var(--border);padding:64px 64px}
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

/* ── Mobile ── */
@media(max-width:900px){
  html{font-size:17px;-webkit-text-size-adjust:100%}
  body{-webkit-overflow-scrolling:touch}
  nav{padding:16px 20px}nav.stuck{padding:12px 20px}.nav-link{display:none}
  .nav-btn{padding:10px 20px;font-size:.72rem}
  #hero{padding:110px 20px 60px;min-height:auto}
  .hero-h1{font-size:clamp(2.4rem,8vw,3.6rem);max-width:100%;margin-bottom:24px}
  .hero-p{font-size:1rem;margin-bottom:32px}
  .hero-actions{flex-direction:column;gap:16px;width:100%}
  .btn-primary{width:100%;text-align:center;padding:16px 24px}
  .btn-ghost{text-align:center}
  .hero-orb{display:none}
  .statement{grid-template-columns:1fr;gap:28px;padding:36px 20px}
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
  .aud-card{padding:36px 24px}
  .aud-title{font-size:1.6rem}
  .wyl-grid,.steps-grid{grid-template-columns:1fr 1fr}
  .wyl-card{padding:24px 20px}
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
  .s-p{font-size:.95rem}
  .c-meta{gap:16px;margin-top:24px}
  .audience-section,.url-lock,.wyl-section,.url-section{padding:40px 20px}
  .steps-wrap,.integrity-section{padding:40px 20px}
  #offer,.roadmap,#contact,footer{padding:40px 20px}
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
  #hero{padding:100px 16px 48px}
  .hero-h1{font-size:clamp(2rem,7.5vw,2.8rem)}
  .hero-kicker{font-size:.68rem;letter-spacing:.18em;margin-bottom:20px}
  .wyl-grid,.steps-grid{grid-template-columns:1fr}
  .proof-strip{grid-template-columns:1fr}
  .stmt-quote{padding:24px 18px}
  .stmt-quote .sq-text{font-size:clamp(1.1rem,4vw,1.4rem)}
  .stmt-quote::before,.stmt-quote::after{left:18px;right:18px}
  .audience-section,.url-lock,.wyl-section,.url-section{padding:32px 16px}
  .steps-wrap,.integrity-section{padding:32px 16px}
  #offer,.roadmap,#contact,footer{padding:32px 16px}
  .aud-card{padding:28px 18px}
  .tier{padding:32px 20px}
  .tier-price{font-size:2.6rem}
  .offer-note{font-size:.88rem}
  .contact-inner{gap:28px}
  .gate-box{padding:28px 18px}
  .logo-seo{font-size:1.05rem}.logo-ai{font-size:1.2rem}.logo-co{font-size:.9rem}
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
    <a href="#contact" class="nav-btn">Request Licensing Access</a>
  </div>
</nav>

<!-- ════════════ HERO — SELL ════════════ -->
<section id="hero">
  <div class="hero-grid"></div>
  <div class="hero-orb"></div>

  <p class="hero-kicker">AI-Powered SEO Infrastructure · Licensed to Agencies &amp; Businesses</p>

  <h1 class="hero-h1">
    Lease the pages.<br>
    <em>Own the rankings.</em><br>
    In every city.
  </h1>

  <p class="hero-p">
    SEOAIco builds and manages <strong>every service and location page on your website</strong> — AI-written content, hero sections, FAQ schema, local business data, and internal linking across 100+ cities. <strong>You license the system. We keep it ranking.</strong>
  </p>

  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Request Licensing Access</a>
    <a href="#wyl" class="btn-ghost">See the System</a>
  </div>

</section>

<!-- ════════════ URL DEMO — LICENSED SEARCH FOOTPRINT ════════════ -->
<div class="url-section">
  <div class="url-inner">
    <div>
      <p class="s-eye r">Licensed Search Footprint</p>
      <h2 class="s-h r">Thousands of search<br>targets. <em>One structured</em><br>system.</h2>
      <p class="s-p r">Each licensed URL is a deliberately composed search asset — built around topic clusters, service-location intent, and structured internal linking logic. <strong>Not pages generated in bulk. A search footprint engineered for coverage.</strong></p>
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
      <div class="url-more">+ structured coverage across all service × location permutations</div>
    </div>
  </div>
</div>

<!-- ════════════ AUDIENCE — SELL ════════════ -->
<section id="who">
  <div class="audience-section">
    <div class="stmt-quote r" style="text-align:center;max-width:960px;margin:0 auto 48px">
      <span class="sq-mark">&ldquo;</span>
      <p class="sq-text">Imagine 500 pages — each targeting a different service in a different city — all <strong>live, ranking, and generating leads… while you sleep.</strong> That's what your competitors just turned on.</p>
      <span class="sq-rule"></span>
    </div>
    <p class="s-eye r">Who This Is For</p>
    <h2 class="s-h r">Two different starting points.<br><em>One infrastructure.</em></h2>
    <div class="audience-grid">

      <div class="aud-card r">
        <span class="aud-tag">For Agencies</span>
        <h3 class="aud-title">Give every client a <em>full local SEO engine</em> — overnight.</h3>
        <p class="aud-body">Your clients need to rank in dozens of cities — not just their headquarters. SEOAIco gives you a turn-key system: hundreds of AI-optimised service + location pages, deployed <strong>under your brand, at your margin</strong>, with schema, linking, and geo-targeting handled automatically.</p>
        <ul class="aud-list">
          <li><strong>New revenue stream</strong> — offer local SEO at scale as a premium retainer without hiring more writers</li>
          <li><strong>White-label, your brand</strong> — zero SEOAIco attribution, clients see only your work</li>
          <li><strong>One licence, entire client book</strong> — deploy across all your clients from a single subscription</li>
          <li><strong>AI content unique to each city &amp; service</strong> — not templates with a city name swapped in</li>
          <li><strong>Reduce churn</strong> — clients stay because their rankings depend on your licence being active</li>
          <li><strong>Upsell built in</strong> — expand URL inventory as clients grow into new markets</li>
          <li><strong>Faster delivery</strong> — deploy 500+ pages in days, not months of content production</li>
        </ul>
      </div>

      <div class="aud-card r">
        <span class="aud-tag">For CEOs &amp; Business Owners</span>
        <h3 class="aud-title">Finally rank in <em>every city</em><br>you actually serve.</h3>
        <p class="aud-body">You've spent years paying for SEO that covers a handful of pages. Meanwhile, your competitors are showing up in 100+ cities — because they have the infrastructure. <strong>SEOAIco builds that infrastructure for you:</strong> every service, every location, every query your customers are typing. AI-written, schema-marked, and ranking.</p>
        <ul class="aud-list">
          <li><strong>Leads from cities you've never ranked in</strong> — cover every service × location combination automatically</li>
          <li><strong>Stop overpaying for SEO</strong> — get 500 pages for the cost of your agency writing 10</li>
          <li><strong>AI content + FAQ schema + LocalBusiness JSON-LD</strong> on every single page</li>
          <li><strong>Geo-targeted, unique content</strong> — Google sees each page as genuinely local, not a duplicate</li>
          <li><strong>Show up in AI search results</strong> — structured data feeds ChatGPT, Gemini, and AI Overviews</li>
          <li><strong>You own the URLs</strong> — pages live on your domain, your site, your brand</li>
          <li><strong>Compounds over time</strong> — rankings get stronger the longer the system runs, not weaker</li>
        </ul>
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
      <p class="s-p r" style="max-width:640px">SEOAIco is the software that powers every service and location page — managing hero sections, FAQ schema, LocalBusiness JSON-LD, meta descriptions, internal linking, and AI-optimised content across 100+ cities. <strong>You license the engine. It keeps your pages ranking.</strong></p>
      <div class="wyl-grid">
        <div class="wyl-card r">
          <span class="wyl-icon">⬡</span>
          <h3 class="wyl-title">Programmatic Page Composition</h3>
          <p class="wyl-desc">Each URL is assembled from structured SEO components — hero targeting, service content, FAQ schema, and CTAs — composed specifically for that search target.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◈</span>
          <h3 class="wyl-title">Internal Linking Architecture</h3>
          <p class="wyl-desc">Related services, nearby cities, topic cluster navigation, and breadcrumb logic are built into every page — creating crawlable, reinforcing link structures across the entire footprint.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◻</span>
          <h3 class="wyl-title">Automated Schema Deployment</h3>
          <p class="wyl-desc">JSON-LD schema graphs, service FAQ schema, and breadcrumb schema are deployed at the system level — not added manually per page.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">⬡</span>
          <h3 class="wyl-title">Hyper-Local Content Variation</h3>
          <p class="wyl-desc">Location-specific copy variations ensure each URL reads and ranks as a genuinely distinct page — not a templated clone with a city name swapped in.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◈</span>
          <h3 class="wyl-title">Topic Clustering</h3>
          <p class="wyl-desc">Service categories, sub-services, and geographic markets are structured as topic clusters — giving each client a coherent, authority-signalling search presence.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◻</span>
          <h3 class="wyl-title">Crawl-Aware SEO Architecture</h3>
          <p class="wyl-desc">Safe rendering logic, canonical structure, and fallback handling are baked in — so the system is stable under Google's crawl and rendering pipelines.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">⬡</span>
          <h3 class="wyl-title">White-Label Licensing Framework</h3>
          <p class="wyl-desc">Every deliverable is stripped of SEOAIco attribution. The infrastructure deploys under your agency brand — your clients see your work, not ours.</p>
        </div>
        <div class="wyl-card r">
          <span class="wyl-icon">◈</span>
          <h3 class="wyl-title">Licensed URL Inventory Control</h3>
          <p class="wyl-desc">Your licence governs a defined, protected URL capacity. Expansion is controlled and sequential — protecting the integrity of every search asset in the system.</p>
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
      <h2 class="s-h r">Structured deployment.<br><em>Controlled expansion.</em></h2>
      <div class="steps-grid">
        <div class="step r">
          <div class="step-n">01</div>
          <h3 class="step-title">Define Search Coverage</h3>
          <p class="step-desc">We map your client's service categories, target locations, and topic clusters — establishing the full architecture of the licensed search footprint before a single page is composed.</p>
        </div>
        <div class="step r">
          <div class="step-n">02</div>
          <h3 class="step-title">Configure Licensed Architecture</h3>
          <p class="step-desc">The system is configured for your client's brand, internal linking structure, schema requirements, and local content parameters — within your licensed URL capacity.</p>
        </div>
        <div class="step r">
          <div class="step-n">03</div>
          <h3 class="step-title">Deploy Under Your Brand</h3>
          <p class="step-desc">Structured pages are delivered for integration into your client's site via API or flat-file export. White-label throughout — no SEOAIco attribution anywhere in the output.</p>
        </div>
        <div class="step r">
          <div class="step-n">04</div>
          <h3 class="step-title">Expand and Protect Your Footprint</h3>
          <p class="step-desc">As rankings develop and market coverage grows, your licence provides the capacity to expand into new services, locations, and topic clusters — within a protected, controlled system.</p>
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
      <p class="ul-lead">Every page, every ranking, every lead — controlled by one thing: your active licence.</p>
    </div>
    <div class="r">
      <div class="ul-states">
        <div class="ul-state active">
          <span class="ul-state-label">Active Licence</span>
          <div class="ul-state-title">Fully live — ranking, indexing, generating leads</div>
          <div class="ul-state-desc">AI content, schema markup, FAQ sections, LocalBusiness JSON-LD, internal links, and meta descriptions all active. Google and AI search engines reading and ranking your pages across every city you serve. <strong>Your SEO compounds month over month.</strong></div>
        </div>
        <div class="ul-state inactive">
          <span class="ul-state-label">Licence Lapsed</span>
          <div class="ul-state-title">Pages revert — company name &amp; phone number only</div>
          <div class="ul-state-desc">All AI content, schema, structured data, and internal links removed. URLs stay on your site, but <strong>rankings drop, leads stop, and the SEO signal disappears.</strong> Reactivate anytime to restore everything.</div>
        </div>
      </div>
      <div class="ul-note">
        <p><strong>Need more pages?</strong> Upgrade to the next tier — your existing pages carry over.</p>
        <p><strong>Already have pages built outside SEOAIco?</strong> You can bring them in on the 10,000 URL tier. Full schema, linking, and AI content across your entire footprint.</p>
      </div>
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
      <p>We review every application individually — this isn't a self-serve checkout. Once you're in, the licence controls everything: the AI content, schema, structured data, and page architecture your site runs on. <strong>Built for agencies and businesses serious about owning their search presence.</strong></p>
      <p style="margin-top:12px;font-size:.84rem">Need more URLs than your current tier allows? Upgrade to the next level — your existing pages carry over.</p>
    </div>
  </div>

  <div class="tier-grid-3 r">

    <div class="tier starter">
      <span class="tier-flag">Starter — By Application</span>
      <h3 class="tier-name">Entry Access</h3>
      <div class="tier-urls">Limited URL capacity · Reviewed &amp; approved individually</div>
      <div class="tier-price">Apply</div>
      <div class="tier-commitment">Entry-level access for qualifying businesses or agencies. Capacity and pricing confirmed on application.</div>
      <ul class="tier-features">
        <li>Structured page composition within approved capacity</li>
        <li>Schema framework deployment</li>
        <li>Internal linking architecture</li>
        <li>Upgrades to 5K or 10K tier as footprint grows</li>
      </ul>
      <div class="tier-gated">
        <span class="tier-gated-icon">◈</span>
        <span><strong>Not publicly priced.</strong> Starter access is granted case-by-case. Apply below — we'll assess fit and confirm capacity.</span>
      </div>
      <a href="#contact" class="tier-cta" style="margin-top:20px">Apply for Starter Access</a>
    </div>

    <div class="tier">
      <span class="tier-flag">Agency / Business Licence — Foundation</span>
      <h3 class="tier-name">5,000 URL Licence</h3>
      <div class="tier-urls">Up to 5,000 licensed URLs — agencies or single-business deployment</div>
      <div class="tier-price"><sup>$</sup>2,995<sub>/mo</sub></div>
      <div class="tier-commitment">3-month minimum engagement. Month-to-month thereafter.</div>
      <ul class="tier-features">
        <li><strong>Licensed URL inventory</strong> — 5,000 active search assets</li>
        <li><strong>White-label deployment rights</strong></li>
        <li>Structured programmatic page composition</li>
        <li>Schema framework &amp; JSON-LD deployment</li>
        <li>Internal linking architecture</li>
        <li>Topic &amp; location scaling within capacity</li>
        <li>API or flat-file export delivery</li>
        <li>Dashboard onboarding <em class="soon">(coming soon)</em></li>
      </ul>
      <a href="#contact" class="tier-cta">Request 5K Licensing Details</a>
    </div>

    <div class="tier prime">
      <span class="tier-flag">Agency / Business Licence — Preferred</span>
      <h3 class="tier-name">10,000 URL Licence</h3>
      <div class="tier-urls">Up to 10,000 licensed URLs — full portfolio or enterprise-scale deployment</div>
      <div class="tier-price"><sup>$</sup>5,995<sub>/mo</sub></div>
      <div class="tier-commitment">Priority processing. Dedicated account contact. 3-month minimum, then month-to-month.</div>
      <ul class="tier-features">
        <li>Everything in the 5,000 URL licence</li>
        <li><strong>Extended licensed inventory</strong> — 10,000 search assets</li>
        <li><strong>Required for all legacy re-entry</strong> &amp; unlicensed build continuity</li>
        <li>Priority deployment processing</li>
        <li>Dedicated account contact</li>
        <li>Early access to dashboard &amp; reporting <em class="soon">(coming soon)</em></li>
        <li>First access to new verticals &amp; system features</li>
      </ul>
      <a href="#contact" class="tier-cta">Request 10K Licensing Details</a>
    </div>

  </div>
</section>

<!-- ════════════ PROOF STRIP — PROOF (moved to bottom) ════════════ -->
<div class="proof-strip r">
  <div class="proof-item">
    <div class="proof-icon">◈</div>
    <div class="proof-label"><strong>White-Label Licensed</strong>Your brand. Your clients. Your margin.</div>
  </div>
  <div class="proof-item">
    <div class="proof-icon">⬡</div>
    <div class="proof-label"><strong>Structured SEO System</strong>Architecture-first. Not content-first.</div>
  </div>
  <div class="proof-item">
    <div class="proof-icon">◻</div>
    <div class="proof-label"><strong>URL Inventory Control</strong>Licensed capacity. Protected expansion.</div>
  </div>
  <div class="proof-item">
    <div class="proof-icon">◈</div>
    <div class="proof-label"><strong>Agencies &amp; Businesses</strong>Agencies. Operators. Businesses at the wall.</div>
  </div>
</div>

<!-- ════════════ ROADMAP — PROOF / FUTURE ════════════ -->
<div class="roadmap">
  <p class="s-eye r">On the Roadmap</p>
  <h2 class="s-h r">The platform expands<br><em>with the licence base.</em></h2>
  <div class="rm-grid">
    <div class="rm-item r">
      <h3 class="rm-title">Agency Dashboard</h3>
      <p class="rm-desc">A self-serve portal for managing licensed URL sets, reviewing deployment status, and onboarding new client campaigns — without developer involvement.</p>
    </div>
    <div class="rm-item r">
      <h3 class="rm-title">Per-URL Search Tracking</h3>
      <p class="rm-desc">SERP visibility monitoring at the individual URL level — giving agencies the reporting layer to demonstrate the value of the licensed footprint to clients.</p>
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
      <p class="s-p r">Whether you're an agency building a scalable infrastructure layer or a business that has hit the ceiling of conventional SEO — licensing access is reviewed individually. We'll assess your market, your coverage goals, and the right licence structure for your situation.</p>
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
            <option value="10k" @selected(old('tier') === '10k')>10,000 URLs — $5,995/mo</option>
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
        <div class="gate-tier-price">$5,995/mo</div>
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
</body>
</html>
