{{-- Shared mobile hamburger + slide-in menu CSS --}}
{{-- @include('partials.public-nav-mobile-css') inside <style> blocks --}}

/* ── Mobile hamburger ── */
.nav-hamburger{
  display:none;
  flex-direction:column;justify-content:center;align-items:center;gap:6px;
  width:48px;height:48px;
  background:rgba(255,255,255,.04);
  border:1px solid rgba(255,255,255,.07);
  border-radius:8px;
  cursor:pointer;
  padding:0;z-index:9300;position:relative;flex-shrink:0;
  touch-action:manipulation;
  transition:background .2s,border-color .2s,transform .12s;
  -webkit-tap-highlight-color:transparent;
}
.nav-hamburger:hover{background:rgba(255,255,255,.07);border-color:rgba(200,168,75,.18)}
.nav-hamburger:active{transform:scale(.93)}
.nav-hamburger:focus-visible{outline:2px solid rgba(200,168,75,.55);outline-offset:3px}
.nav-hamburger span{
  display:block;width:24px;height:2px;background:var(--ivory);
  border-radius:2px;
  transition:transform .28s ease,opacity .22s,width .22s,background .2s;
  transform-origin:center;
}
.nav-hamburger.is-open{background:rgba(200,168,75,.08);border-color:rgba(200,168,75,.22)}
.nav-hamburger.is-open span{background:var(--gold,#c8a84b)}
.nav-hamburger.is-open span:nth-child(1){transform:translateY(8px) rotate(45deg)}
.nav-hamburger.is-open span:nth-child(2){opacity:0;width:0}
.nav-hamburger.is-open span:nth-child(3){transform:translateY(-8px) rotate(-45deg)}

/* ── Panel backdrop ── */
.nav-backdrop{
  position:fixed;inset:0;
  z-index:9100;
  background:rgba(0,0,0,.48);
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
  width:320px;max-width:88vw;
  z-index:9200;
  background:linear-gradient(160deg,#1a1810 0%,#141210 60%,#171510 100%);
  border-left:1px solid rgba(200,168,75,.22);
  box-shadow:-16px 0 64px rgba(0,0,0,.72);
  overflow-y:auto;
  display:flex;flex-direction:column;
  transform:translateX(100%);
  visibility:hidden;
  transition:transform .32s cubic-bezier(.23,1,.32,1),visibility 0s .32s;
}
.nav-menu::before{
  content:'';
  position:absolute;top:0;left:0;right:0;
  height:320px;
  background:radial-gradient(ellipse 260px 200px at 80% -10%,rgba(200,168,75,.055) 0%,transparent 70%);
  pointer-events:none;
}
.nav-menu.is-open{
  transform:translateX(0);
  visibility:visible;
  transition:transform .32s cubic-bezier(.23,1,.32,1),visibility 0s 0s;
}
.nav-menu-inner{
  padding:72px 0 48px;
  flex:1;display:flex;flex-direction:column;
  position:relative;
}

/* Identity block */
.nm-identity{
  padding:0 28px 28px;
  border-bottom:1px solid rgba(200,168,75,.09);
  margin-bottom:8px;
}
.nm-identity-brand{
  font-family:'Cormorant Garamond',serif;
  font-size:1.45rem;font-weight:400;letter-spacing:.04em;
  color:var(--ivory);
  line-height:1.1;
  margin-bottom:5px;
}
.nm-identity-brand em{
  color:var(--gold);
  font-style:normal;
}
.nm-identity-sub{
  font-size:.68rem;letter-spacing:.2em;text-transform:uppercase;
  color:rgba(200,168,75,.58);
  font-family:'DM Sans',sans-serif;
}

/* Section group labels */
.nm-group{
  padding:20px 28px 6px;
}
.nm-group-label{
  font-size:.6rem;letter-spacing:.26em;text-transform:uppercase;
  color:rgba(200,168,75,.58);
  font-family:'DM Sans',sans-serif;font-weight:500;
}

/* Panel menu links */
.nm-link{
  display:flex;align-items:center;justify-content:space-between;
  padding:13px 28px;
  font-family:'DM Sans',sans-serif;
  font-size:.84rem;letter-spacing:.07em;
  color:rgba(220,220,214,.88);text-decoration:none;
  transition:color .18s ease,background .18s ease,transform .15s ease;
  min-height:48px;
  position:relative;
}
.nm-link::after{
  content:'›';
  color:rgba(200,168,75,.28);
  font-size:1.05rem;
  transition:color .18s ease,transform .18s ease;
  flex-shrink:0;
}
.nm-link:hover,.nm-link:focus-visible{
  color:var(--ivory);
  background:rgba(200,168,75,.05);
  transform:scale(1.018);
}
.nm-link:hover::after{color:var(--gold);transform:translateX(3px)}
.nm-link:active{transform:scale(.97)}

/* Primary featured link */
.nm-link.nm-featured{
  font-size:.9rem;
  color:var(--gold);
  font-weight:500;
  letter-spacing:.1em;
  padding-top:14px;padding-bottom:14px;
}
.nm-link.nm-featured::after{color:var(--gold);opacity:.7}
.nm-link.nm-featured:hover{
  color:#e2c96e;
  background:rgba(200,168,75,.07);
  box-shadow:inset 3px 0 0 rgba(200,168,75,.4);
}
.nm-link.nm-featured:hover::after{transform:translateX(4px)}

/* Tertiary muted link */
.nm-link.nm-muted{
  font-size:.76rem;
  color:rgba(168,168,160,.68);
  letter-spacing:.09em;
  padding-top:10px;padding-bottom:10px;
  min-height:44px;
}
.nm-link.nm-muted::after{color:rgba(200,168,75,.32);font-size:.9rem}
.nm-link.nm-muted:hover{color:rgba(210,210,202,.88);background:transparent}
.nm-link.nm-muted:hover::after{color:rgba(200,168,75,.55)}

/* Divider */
.nm-divider{
  height:1px;
  background:rgba(200,168,75,.07);
  margin:6px 0;
}

/* Portal row */
.nm-portal{
  display:flex;align-items:center;justify-content:space-between;
  padding:13px 28px;
  color:var(--gold);font-family:'DM Sans',sans-serif;
  font-size:.84rem;font-weight:500;letter-spacing:.1em;
  text-decoration:none;
  transition:color .18s ease,background .18s ease,transform .15s ease;
  min-height:48px;
}
.nm-portal::after{
  content:'›';
  color:var(--gold);opacity:.7;
  font-size:1.05rem;transition:color .18s ease,transform .18s ease;
}
.nm-portal:hover{color:#e2c96e;background:rgba(200,168,75,.06);transform:scale(1.018)}
.nm-portal:hover::after{transform:translateX(3px)}
.nm-portal:active{transform:scale(.97)}

/* Sign In secondary row */
.nm-signin{
  display:flex;align-items:center;justify-content:space-between;
  padding:10px 28px;
  font-family:'DM Sans',sans-serif;
  font-size:.74rem;letter-spacing:.1em;
  color:rgba(168,168,160,.72);text-decoration:none;
  transition:color .18s ease;
  min-height:44px;
}
.nm-signin::after{content:'›';color:rgba(200,168,75,.32);font-size:.9rem;transition:color .18s ease,transform .18s ease}
.nm-signin:hover{color:rgba(210,210,202,.9)}
.nm-signin:hover::after{color:rgba(200,168,75,.62);transform:translateX(2px)}