/* ══ Shared Public Nav CSS ══ */
/* Logo — hard-locked branding */
.logo{display:inline-flex;align-items:baseline;text-decoration:none;line-height:1;color:inherit}
.logo,.logo:visited,.logo:hover,.logo:active,.logo:focus{text-decoration:none;padding:8px 4px;margin:-8px -4px;position:relative;z-index:1}
.logo-seo{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.2rem;letter-spacing:.06em;color:#f5f0e8;color:rgba(245,240,232,1)}
.logo-seo:visited,.logo-seo:link{color:#f5f0e8}
.logo-ai{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.42rem;color:#c8a84b;letter-spacing:.02em;display:inline-block;transform:skewX(-11deg) translateY(-1px)}
.logo-ai:visited,.logo-ai:link{color:#c8a84b}
.logo-co{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.05rem;color:rgba(255,255,255,.45);letter-spacing:.04em}
.logo-co:visited,.logo-co:link{color:rgba(255,255,255,.45)}

/* Nav container */
#nav{position:fixed;top:0;left:0;right:0;z-index:200;display:flex;align-items:center;justify-content:space-between;padding:28px 64px;border-bottom:1px solid transparent;transition:all .4s}
#nav::after{content:'';position:absolute;bottom:-1px;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.06),rgba(200,168,75,.10),rgba(200,168,75,.06),transparent);opacity:0;transition:opacity .4s}
#nav.stuck{background:rgba(8,8,8,.95);backdrop-filter:blur(16px);border-color:var(--border);padding:16px 64px}
#nav.stuck::after{opacity:1}

/* Nav links */
.nav-right{display:flex;align-items:center;gap:26px}
.nav-link{font-size:.76rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(168,168,160,.72);text-decoration:none;transition:color .15s ease,text-shadow .15s ease;position:relative;padding-bottom:2px;font-weight:400}
.nav-link::after{content:'';position:absolute;bottom:0;left:0;right:100%;height:1px;background:var(--gold);transition:right .3s cubic-bezier(.23,1,.32,1)}
.nav-link:hover{color:rgba(200,168,75,.85);text-shadow:0 0 8px rgba(200,168,75,.1)}
.nav-link:hover::after{right:0}
.nav-link.active{color:rgba(200,168,75,.7)}

/* Nav CTA */
.nav-btn{font-size:.74rem;letter-spacing:.14em;text-transform:uppercase;color:var(--bg);background:var(--gold);padding:11px 28px;text-decoration:none;transition:background .15s ease,box-shadow .15s ease,transform .15s ease;display:inline-flex;align-items:center;white-space:nowrap;font-weight:500;margin-left:6px;box-shadow:0 0 16px rgba(200,168,75,.08)}
.nav-btn:hover{background:var(--gold-lt);box-shadow:0 4px 20px rgba(200,168,75,.22),0 0 24px rgba(200,168,75,.10);transform:translateY(-1px)}

/* 1200px breakpoint */
@media(max-width:1200px){
  #nav{padding:20px 36px}
  #nav.stuck{padding:14px 36px}
  .nav-right{gap:20px}
  .nav-link{font-size:.7rem;letter-spacing:.12em}
  .nav-btn{font-size:.68rem;letter-spacing:.12em;padding:10px 22px;min-height:38px}
}
