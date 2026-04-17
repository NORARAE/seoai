{{-- ══════════════════════════════════════════════════════════
     SEOAIco Design System — Shared Visual Language
     ══════════════════════════════════════════════════════════
     @include('partials.design-system') inside <style> blocks.
     Pages can override any token via subsequent :root declarations.
     ══════════════════════════════════════════════════════════ --}}

/* ── Reset ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

/* ── Design Tokens ── */
:root{
  /* Core palette */
  --bg:#080808;
  --deep:#0b0b0b;
  --card:#0e0d09;
  --border:rgba(200,168,75,.09);

  /* Gold system */
  --gold:#c8a84b;
  --gold-lt:#e2c97d;
  --gold-dim:rgba(200,168,75,.4);
  --gold-secondary:rgba(200,168,75,.55);
  --gold-tertiary:rgba(200,168,75,.36);

  /* Text system */
  --ivory:#ede8de;
  --muted:rgba(168,168,160,.72);
  --muted-lt:rgba(168,168,160,.50);
  --text-secondary:rgba(168,168,160,.68);
  --text-tertiary:rgba(168,168,160,.55);

  /* Card system */
  --card-bg:rgba(10,9,7,.92);
  --card-bg-hover:rgba(14,13,9,.96);
  --card-border:rgba(200,168,75,.06);
  --card-border-hover:rgba(200,168,75,.14);

  /* Motion */
  --ease-out:cubic-bezier(.23,1,.32,1);
  --transition-base:.35s;
  --transition-smooth:.4s;

  /* Layout */
  --grid-gap:2px;
  --wrap-max:1280px;
  --wrap-pad:64px;
  --section-pad:64px 0 72px;
}

/* ── Base ── */
html{scroll-behavior:smooth;scroll-padding-top:80px;font-size:18px}
body{
  background:var(--bg);color:var(--ivory);
  font-family:'DM Sans',sans-serif;font-weight:300;
  line-height:1.75;min-height:100vh;
}

/* ── Layout ── */
.wrap{max-width:var(--wrap-max);margin:0 auto;padding:0 var(--wrap-pad)}

/* ── Section Headings ── */
.s-eye{font-size:.62rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold-secondary);margin-bottom:16px}
.s-hed{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.8rem,3.5vw,2.8rem);font-weight:300;
  color:var(--ivory);line-height:1.12;margin-bottom:12px;
}
.s-hed em{font-style:italic;color:var(--gold-lt)}
.s-sub{font-size:.88rem;color:rgba(168,168,160,.78);max-width:520px;margin:0 auto;line-height:1.78}
.s-clarify{font-size:.78rem;color:var(--gold-secondary);max-width:520px;margin:8px auto 0;line-height:1.72;letter-spacing:.02em;font-style:italic}

/* ── Buttons ── */
.btn-primary{
  display:inline-block;padding:15px 36px;
  background:linear-gradient(180deg,#d8be72,#c8a84b);color:#080808;
  font-size:.74rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;
  text-decoration:none;border:1px solid rgba(226,201,125,.4);border-radius:2px;
  transition:all .3s;position:relative;overflow:hidden;
}
.btn-primary::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,.12),transparent);transform:translateX(-100%);transition:transform .6s}
.btn-primary:hover{background:linear-gradient(180deg,#e0c97e,#d4b45a);border-color:rgba(226,201,125,.65);box-shadow:0 8px 32px rgba(200,168,75,.18);transform:translateY(-1px)}
.btn-primary:hover::before{transform:translateX(100%)}

.btn-ghost{
  display:inline-block;font-size:.78rem;color:rgba(168,168,160,.6);
  text-decoration:none;letter-spacing:.06em;transition:color .2s;
  border-bottom:1px solid rgba(200,168,75,.18);padding-bottom:2px;
}
.btn-ghost:hover{color:var(--gold)}

/* ── Section Divider ── */
.section-divide{
  width:1px;height:64px;margin:0 auto;
  background:linear-gradient(180deg,rgba(200,168,75,.14),rgba(200,168,75,.03));
  position:relative;
}
.section-divide::before{
  content:'';position:absolute;top:-4px;left:-3px;
  width:7px;height:7px;border-radius:50%;background:rgba(200,168,75,.1);
}
.section-divide::after{
  content:'';position:absolute;bottom:-4px;left:-3px;
  width:7px;height:7px;border-radius:50%;
  background:rgba(200,168,75,.16);box-shadow:0 0 12px rgba(200,168,75,.06);
}
@keyframes divide-flow{
  0%{top:-4px;opacity:0}
  20%{opacity:.5}
  80%{opacity:.5}
  100%{top:calc(100% - 4px);opacity:0}
}
.section-divide .divide-pulse{
  position:absolute;left:-1px;
  width:3px;height:3px;border-radius:50%;
  background:rgba(200,168,75,.35);
  animation:divide-flow 3s ease-in-out infinite;
}

/* ── Footer ── */
.site-footer{
  border-top:1px solid var(--border);padding:32px var(--wrap-pad);
  display:flex;align-items:center;justify-content:space-between;
  flex-wrap:wrap;gap:16px;
}
.footer-copy{font-size:.72rem;color:rgba(168,168,160,.42);letter-spacing:.06em}
.footer-links{display:flex;gap:24px;list-style:none}
.footer-links a{font-size:.74rem;letter-spacing:.08em;color:rgba(168,168,160,.55);text-decoration:none;transition:color .2s}
.footer-links a:hover{color:var(--gold)}

/* ── Shared Responsive ── */
@media(max-width:1100px){
  :root{--wrap-pad:40px}
}
@media(max-width:900px){
  :root{--wrap-pad:28px}
}
@media(max-width:600px){
  :root{--wrap-pad:20px}
  .site-footer{padding:24px var(--wrap-pad);flex-direction:column;align-items:flex-start;gap:12px}
  .s-clarify{font-size:.72rem;margin-top:6px}
}
