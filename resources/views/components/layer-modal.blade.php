{{--
  Layer Upgrade Modal — SEOAIco
  ──────────────────────────────
  Triggered by any element with data-layer="level-2|level-3|level-4".
  Vanilla JS only — no Alpine.js dependency.
  AI Ask hooks into existing POST /ai/chat endpoint.
--}}

<style>
/* ═══════════════════════════════════════════
   UI PRINCIPLE

   This is not a tool UI.
   This is a guided system.

   Every section must:
   - lead forward
   - reduce choice
   - increase certainty

   If something adds confusion, remove it.
   ═══════════════════════════════════════════ */

/* ═══════════════════════════════════════════
   LAYER MODAL SYSTEM
   ═══════════════════════════════════════════ */

/* ── Overlay ── */
.lm-overlay{
  position:fixed;inset:0;z-index:8800;
  background:rgba(4,4,3,.86);
  backdrop-filter:blur(9px);-webkit-backdrop-filter:blur(9px);
  display:flex;align-items:center;justify-content:center;padding:20px;
  opacity:0;pointer-events:none;
  transition:opacity .32s cubic-bezier(.22,.68,0,1.2);
}
.lm-overlay.open{opacity:1;pointer-events:auto}

/* ── Modal box ── */
.lm-box{
  background:linear-gradient(160deg,#131109 0%,#0d0c08 100%);
  border:1px solid rgba(200,168,75,.18);
  border-top:2px solid rgba(200,168,75,.38);
  border-radius:14px;
  width:100%;max-width:560px;
  max-height:90vh;overflow-y:auto;
  padding:44px 40px 36px;
  position:relative;
  transform:scale(.95) translateY(26px);
  transition:transform .38s cubic-bezier(.22,.68,0,1.2);
  box-shadow:0 28px 80px rgba(0,0,0,.56),0 0 48px rgba(200,168,75,.03);
}
.lm-overlay.open .lm-box{
  transform:scale(1) translateY(0);
}

/* ── Close button ── */
.lm-close{
  position:absolute;top:14px;right:14px;
  background:rgba(255,255,255,.04);
  border:1px solid rgba(255,255,255,.08);
  border-radius:50%;width:34px;height:34px;
  color:rgba(168,168,160,.6);font-size:1.1rem;
  cursor:pointer;display:flex;align-items:center;justify-content:center;
  transition:color .2s,background .2s;padding:0;line-height:1;
}
.lm-close:hover{color:#ede8de;background:rgba(255,255,255,.1)}
.lm-close:focus-visible{outline:2px solid rgba(200,168,75,.5);outline-offset:2px}

/* ── Stagger animations for content sections ── */
.lm-overlay.open .lm-stagger{
  animation:lmFadeUp .42s cubic-bezier(.22,.68,0,1.2) both;
}
.lm-stagger:nth-child(1){animation-delay:.05s}
.lm-stagger:nth-child(2){animation-delay:.12s}
.lm-stagger:nth-child(3){animation-delay:.19s}
.lm-stagger:nth-child(4){animation-delay:.26s}
.lm-stagger:nth-child(5){animation-delay:.33s}
.lm-stagger:nth-child(6){animation-delay:.40s}

@keyframes lmFadeUp{
  from{opacity:0;transform:translateY(10px)}
  to{opacity:1;transform:none}
}

/* ── Header ── */
.lm-header{margin-bottom:22px}

.lm-level-badge{
  display:inline-block;
  font-size:.5rem;letter-spacing:.2em;text-transform:uppercase;
  color:rgba(200,168,75,.82);
  border:1px solid rgba(200,168,75,.3);border-radius:99px;
  padding:3px 11px;margin-bottom:12px;
  background:rgba(200,168,75,.06);
  font-family:'DM Sans',sans-serif;
}

.lm-price{
  font-family:'Cormorant Garamond',serif;
  font-size:2.7rem;font-weight:300;color:rgba(215,183,78,1);
  line-height:1;margin-bottom:9px;
}

.lm-title{
  font-family:'Cormorant Garamond',serif;font-size:1.86rem;font-weight:400;
  color:#f5f0e8;line-height:1.1;margin:0 0 11px;
}

.lm-descriptor{
  font-size:.86rem;color:rgba(220,214,200,.84);line-height:1.68;margin:0;
}

/* ── Content sections ── */
.lm-section{margin-bottom:20px}

.lm-section-label{
  font-size:.5rem;letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.58);margin-bottom:10px;
  font-family:'DM Sans',sans-serif;
  border-bottom:1px solid rgba(200,168,75,.12);padding-bottom:6px;
}

/* Bullet list */
.lm-bullets{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px}
.lm-bullets li{
  font-size:.84rem;color:rgba(218,213,202,.9);line-height:1.65;
  display:flex;align-items:baseline;gap:9px;
}
.lm-bullets li::before{
  content:'\2014';color:rgba(200,168,75,.38);flex-shrink:0;font-size:.72rem;
}

/* Why upgrade paragraph */
.lm-reason{font-size:.84rem;color:rgba(210,205,190,.86);line-height:1.76;margin:0}

/* ── System position strip ── */
.lm-position{
  display:flex;align-items:stretch;gap:0;
  overflow:hidden;border-radius:6px;
  border:1px solid rgba(200,168,75,.1);
}
.lm-pos-step{
  flex:1;text-align:center;padding:8px 3px;
  border-right:1px solid rgba(200,168,75,.08);
  font-size:.4rem;letter-spacing:.09em;text-transform:uppercase;
  color:rgba(168,168,160,.32);font-family:'DM Sans',sans-serif;
  line-height:1.5;transition:background .2s,color .2s;
}
.lm-pos-step:last-child{border-right:none}
.lm-pos-step .lm-pos-num{display:block;font-size:.4rem;opacity:.5;margin-bottom:2px}
.lm-pos-step.lm-pos-active{
  background:rgba(200,168,75,.07);color:rgba(200,168,75,.86);
  font-weight:500;
}
.lm-pos-step.lm-pos-done{color:rgba(200,168,75,.35)}

/* ── CTA area ── */
.lm-cta{
  margin-top:26px;padding-top:20px;
  border-top:1px solid rgba(200,168,75,.08);
  display:flex;flex-direction:column;gap:12px;
}

.lm-cta-primary{
  display:block;text-align:center;
  background:linear-gradient(135deg,rgba(200,168,75,.99) 0%,rgba(172,140,52,.96) 100%);
  color:#080808;
  font-family:'DM Sans',sans-serif;font-size:.76rem;font-weight:700;
  letter-spacing:.14em;text-transform:uppercase;text-decoration:none;
  padding:18px 28px;border-radius:7px;
  transition:opacity .2s,box-shadow .2s,transform .15s;
  box-shadow:0 4px 26px rgba(200,168,75,.3);
}
.lm-cta-primary:hover{
  opacity:.93;
  box-shadow:0 6px 34px rgba(200,168,75,.42);
  transform:translateY(-1px);
}
.lm-cta-primary:active{transform:none;opacity:1}

.lm-cta-secondary{
  display:block;width:100%;background:none;border:none;
  text-align:center;padding:10px;cursor:pointer;
  font-family:'DM Sans',sans-serif;font-size:.66rem;
  letter-spacing:.1em;text-transform:uppercase;
  color:rgba(168,168,160,.56);
  transition:color .2s;
}
.lm-cta-secondary:hover{color:rgba(168,168,160,.86)}
.lm-cta-secondary:focus-visible{outline:2px solid rgba(200,168,75,.4);outline-offset:2px;border-radius:4px}

.lm-ask-trigger{
  display:block;width:100%;background:none;
  border:1px solid rgba(200,168,75,.18);border-radius:5px;
  text-align:center;padding:10px;cursor:pointer;
  font-family:'DM Sans',sans-serif;font-size:.63rem;
  letter-spacing:.1em;text-transform:uppercase;
  color:rgba(200,168,75,.54);
  transition:color .2s,border-color .2s;
}
.lm-ask-trigger:hover{color:rgba(200,168,75,.84);border-color:rgba(200,168,75,.36)}
.lm-ask-trigger:focus-visible{outline:2px solid rgba(200,168,75,.4);outline-offset:2px}

/* ── Inline AI ask panel ── */
.lm-ask-panel{
  margin-top:16px;padding:18px 20px;
  background:rgba(200,168,75,.022);
  border:1px solid rgba(200,168,75,.1);
  border-radius:8px;
  animation:lmFadeUp .3s cubic-bezier(.22,.68,0,1.2) both;
}

.lm-ask-label{
  font-size:.58rem;letter-spacing:.16em;text-transform:uppercase;
  color:rgba(200,168,75,.48);margin-bottom:10px;
  font-family:'DM Sans',sans-serif;
}

.lm-ask-row{display:flex;gap:8px}

.lm-ask-input{
  flex:1;
  background:rgba(0,0,0,.32);
  border:1px solid rgba(200,168,75,.15);
  border-radius:5px;padding:10px 14px;
  font-family:'DM Sans',sans-serif;font-size:.84rem;
  color:#ede8de;outline:none;
  transition:border-color .2s;
}
.lm-ask-input:focus{border-color:rgba(200,168,75,.36)}
.lm-ask-input::placeholder{color:rgba(168,168,160,.3)}

.lm-ask-submit{
  flex-shrink:0;
  background:rgba(200,168,75,.12);
  border:1px solid rgba(200,168,75,.2);
  border-radius:5px;padding:0 18px;
  font-family:'DM Sans',sans-serif;font-size:.63rem;
  letter-spacing:.1em;text-transform:uppercase;
  color:rgba(200,168,75,.76);cursor:pointer;
  transition:background .2s,color .2s;
}
.lm-ask-submit:hover{background:rgba(200,168,75,.22);color:rgba(200,168,75,.96)}
.lm-ask-submit:disabled{opacity:.38;cursor:not-allowed}

.lm-ask-thinking{
  font-size:.76rem;color:rgba(168,168,160,.42);
  margin-top:12px;font-style:italic;letter-spacing:.03em;
}

.lm-ask-response{
  margin-top:14px;padding-top:14px;
  border-top:1px solid rgba(200,168,75,.07);
  font-size:.8rem;color:rgba(200,200,192,.8);line-height:1.82;
}

/* ── Mobile: bottom sheet ── */
@media(max-width:640px){
  .lm-overlay{padding:0;align-items:flex-end}
  .lm-box{
    max-width:100%;width:100%;
    border-radius:18px 18px 0 0;
    border-top:2px solid rgba(200,168,75,.38);
    max-height:92dvh;
    padding:30px 24px 48px;
    transform:translateY(100%);
    transition:transform .38s cubic-bezier(.22,.68,0,1.2);
  }
  .lm-overlay.open .lm-box{transform:translateY(0)}
  .lm-cta{position:sticky;bottom:0;background:linear-gradient(to top,#0d0c08 70%,transparent);padding-bottom:14px}
  .lm-cta-primary{font-size:.78rem;padding:18px}
  .lm-ask-row{flex-direction:column}
  .lm-ask-submit{padding:12px;width:100%}
  .lm-pos-step{font-size:.42rem;padding:8px 2px}
}

@media(prefers-reduced-motion:reduce){
  .lm-overlay,.lm-box,.lm-overlay.open .lm-stagger{
    transition:none;animation:none;
  }
}

/* Final ladder conversion polish */
/* DESIGN SYSTEM RULE
   Serif (Cormorant Garamond) = headlines only — luxury, authority
   Sans  (DM Sans)            = body only — clarity, readability
   Do not introduce new font families */
.lm-box{transform:scale(.98) translateY(20px)}
.lm-overlay.open .lm-box{transform:scale(1) translateY(0)}
.lm-header{margin-bottom:28px}
.lm-section{margin-bottom:16px}
.lm-level-badge{font-size:.69rem;letter-spacing:.16em;color:rgba(241,214,133,.98);padding:6px 13px;margin-bottom:16px;font-weight:600;background:rgba(200,168,75,.1);border-color:rgba(200,168,75,.44);box-shadow:0 0 0 1px rgba(200,168,75,.14) inset,0 0 18px rgba(200,168,75,.12)}
.lm-price{font-family:'Cormorant Garamond',serif;font-size:4.05rem;color:rgba(248,232,188,.99);margin-bottom:12px;text-shadow:0 0 26px rgba(200,168,75,.24);animation:lmPriceShimmer 9s ease-in-out infinite}
.lm-title{font-family:'Cormorant Garamond',serif;font-size:2.12rem;font-weight:400;color:rgba(245,240,232,.98);margin:0 0 14px;line-height:1.08}
.lm-descriptor{font-size:1.14rem;color:rgba(239,233,220,.96);line-height:1.62}
@keyframes lmPriceShimmer{0%,100%{text-shadow:0 0 16px rgba(200,168,75,.14)}46%{text-shadow:0 0 26px rgba(200,168,75,.28)}54%{text-shadow:0 0 18px rgba(200,168,75,.18)}}
.lm-section-label{font-size:.74rem;letter-spacing:.15em;color:rgba(241,214,133,.99);margin:20px 0 6px;font-weight:600}
.lm-bullets{gap:6px}
.lm-bullets li{font-size:1.08rem;color:rgba(238,232,218,.97);line-height:1.45}
.lm-bullets li::before{color:rgba(228,198,112,.78);font-size:.9rem}
.lm-reason{font-size:1.08rem;color:rgba(238,232,218,.96);line-height:1.55}
.lm-position{
  display:grid;
  grid-template-columns:repeat(6,minmax(0,1fr));
  border:1px solid rgba(200,168,75,.16);
  border-radius:9px;
  background:linear-gradient(180deg,rgba(200,168,75,.05),rgba(200,168,75,.02));
  overflow:hidden;
}
.lm-pos-step{
  position:relative;
  display:flex;flex-direction:column;justify-content:center;align-items:center;
  gap:2px;
  min-height:56px;
  padding:10px 5px 9px;
  font-size:.62rem;
  letter-spacing:.115em;
  color:rgba(211,204,189,.87);
  border-right:1px solid rgba(200,168,75,.12);
  background:transparent;
  transition:background .22s,color .22s,box-shadow .22s,border-color .22s;
}
.lm-pos-step::before{
  content:'';
  position:absolute;
  top:0;left:8px;right:8px;
  height:1px;
  background:rgba(200,168,75,.58);
  opacity:0;
}
.lm-pos-step:last-child{border-right:none}
.lm-pos-step .lm-pos-num{
  font-family:'Cormorant Garamond',serif;
  font-size:.84rem;
  line-height:1;
  letter-spacing:.04em;
  opacity:.86;
  margin-bottom:1px;
}
.lm-pos-step.lm-pos-done{color:rgba(200,168,75,.64)}
.lm-pos-step.lm-pos-active{
  background:linear-gradient(180deg,rgba(200,168,75,.24),rgba(200,168,75,.1));
  color:rgba(241,216,136,.98);
  box-shadow:inset 0 0 0 1px rgba(200,168,75,.44),0 0 18px rgba(200,168,75,.2);
  animation:lmPosPulse 4s ease-in-out infinite;
}
.lm-pos-step.lm-pos-active::before{opacity:1}
.lm-pos-step.lm-pos-active::after{
  content:'';
  position:absolute;
  left:8px;
  right:8px;
  bottom:6px;
  height:2px;
  border-radius:99px;
  background:linear-gradient(90deg,rgba(241,216,136,.14),rgba(241,216,136,.8),rgba(241,216,136,.14));
}
@keyframes lmPosPulse{0%,100%{box-shadow:inset 0 0 0 1px rgba(200,168,75,.35),0 0 10px rgba(200,168,75,.1)}50%{box-shadow:inset 0 0 0 1px rgba(200,168,75,.5),0 0 24px rgba(200,168,75,.24)}}
.lm-cta-primary{font-size:.84rem;letter-spacing:.11em;padding:19px 28px;transition:opacity .2s,box-shadow .2s,transform .15s,filter .2s}
.lm-cta-primary:hover{opacity:.97;box-shadow:0 10px 44px rgba(200,168,75,.62),0 0 24px rgba(200,168,75,.24);filter:brightness(1.05);transform:translateY(-1px)}
.lm-cta-subtext{margin:-2px 0 2px;font-size:1rem;line-height:1.48;color:rgba(238,232,218,.95);text-align:center}
.lm-cta-support{margin:0 0 8px;font-size:.9rem;line-height:1.45;color:rgba(221,215,200,.87);text-align:center}
.lm-ask-trigger{border:1px solid rgba(200,168,75,.36);border-radius:7px;padding:12px;font-size:1rem;letter-spacing:.02em;text-transform:none;color:rgba(229,199,112,.9)}
.lm-ask-trigger:hover{color:rgba(238,209,120,.98);border-color:rgba(200,168,75,.58)}
.lm-cta-secondary{font-size:.9rem;letter-spacing:.02em;text-transform:none;color:rgba(238,208,126,.9);padding:8px 0 2px}
.lm-cta-secondary:hover{color:rgba(245,217,142,.99)}
.lm-cta-tertiary{display:block;width:100%;background:none;border:none;text-align:center;padding:4px;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:.74rem;letter-spacing:.06em;text-transform:uppercase;color:rgba(186,179,164,.61);transition:color .2s,opacity .2s}
.lm-cta-tertiary:hover{color:rgba(204,196,180,.82)}
.lm-ai-link{display:none}
.lm-ask-label{font-size:.92rem;letter-spacing:.03em;text-transform:none;color:rgba(230,224,210,.9)}
.lm-ask-input{font-size:1rem}
.lm-ask-submit{font-size:.9rem;letter-spacing:.03em;text-transform:none}
.lm-ask-thinking{font-size:1rem;color:rgba(190,184,170,.78)}
.lm-ask-response{font-size:1.04rem;color:rgba(224,218,206,.94)}
.lm-soft-gate{margin-top:16px;padding:14px 14px 12px;border:1px solid rgba(200,168,75,.22);border-radius:8px;background:linear-gradient(160deg,rgba(200,168,75,.08),rgba(16,13,10,.58))}
.lm-soft-gate-title{margin:0 0 10px;font-size:1rem;line-height:1.55;color:rgba(237,229,210,.96)}
.lm-soft-gate-actions{display:flex;flex-wrap:wrap;gap:8px}
.lm-soft-gate-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 12px;border-radius:6px;border:1px solid rgba(200,168,75,.3);text-decoration:none;font-size:.88rem;color:rgba(228,198,112,.94);background:rgba(200,168,75,.06)}
.lm-soft-gate-actions a:hover{border-color:rgba(200,168,75,.56);background:rgba(200,168,75,.12)}
/* Soft gate subtext */
.lm-soft-gate-sub{margin:0 0 10px;font-size:.92rem;color:rgba(200,194,180,.76);line-height:1.55}
/* Is this right for you? qualify block */
.lm-qualify-lead{font-family:'Cormorant Garamond',serif;font-size:1.12rem;color:rgba(228,198,112,.92);margin:0 0 10px;font-style:italic}
.lm-qualify-list{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:7px}
.lm-qualify-list li{font-size:1.04rem;color:rgba(230,224,210,.9);line-height:1.68;display:flex;align-items:baseline;gap:9px}
.lm-qualify-list li::before{content:'\2713';color:rgba(200,168,75,.78);font-size:.85rem;flex-shrink:0}
/* Ladder awareness banner */
.lm-ladder-banner{display:none}
.lm-ladder-banner-text{font-size:.9rem;color:rgba(237,229,210,.9);line-height:1.55}
.lm-ladder-banner-link{display:inline-block;margin-top:8px;font-size:.88rem;color:rgba(200,168,75,.95);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.3)}
.lm-ladder-banner-link:hover{color:rgba(228,198,112,1);border-bottom-color:rgba(228,198,112,.6)}

/* Subtle scroll affordance for dense modal content */
.lm-scroll-cue{
  position:absolute;
  right:14px;
  bottom:10px;
  display:flex;align-items:center;justify-content:center;
  opacity:0;
  pointer-events:none;
  transition:opacity .24s ease;
}
.lm-scroll-cue-chevron{
  width:7px;height:7px;
  border-right:1px solid rgba(200,168,75,.56);
  border-bottom:1px solid rgba(200,168,75,.56);
  transform:rotate(45deg);
}
.lm-box.lm-can-scroll:not(.lm-scroll-end) .lm-scroll-cue{
  opacity:.58;
  animation:lmScrollCueDrift 2.2s ease-in-out infinite;
}
.lm-box.lm-can-scroll:not(.lm-scroll-end)::after{
  content:'';
  position:absolute;
  left:0;right:0;bottom:0;
  height:48px;
  pointer-events:none;
  background:linear-gradient(to top,rgba(12,10,8,.96),rgba(12,10,8,0));
}
@keyframes lmScrollCueDrift{
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(3px)}
}
@media(max-width:640px){
  .lm-cta-primary{font-size:.9rem;padding:18px}
  .lm-cta-subtext{font-size:1rem}
  .lm-pos-step{font-size:.56rem;letter-spacing:.11em;min-height:48px;padding:8px 3px 7px}
  .lm-pos-step .lm-pos-num{font-size:.74rem}
  .lm-price{font-size:3.5rem}
  .lm-title{font-size:1.88rem}
  .lm-descriptor,.lm-bullets li,.lm-reason{font-size:1rem}
}
</style>

{{-- ════ MODAL: LAYER UPGRADE ════ --}}
<div id="layerModal" class="lm-overlay" role="dialog" aria-modal="true" aria-labelledby="lmTitle">
  <div class="lm-box">

    <button class="lm-close" id="lmClose" aria-label="Close">&times;</button>

    {{-- HEADER --}}
    <div class="lm-header lm-stagger">
      <span class="lm-level-badge" id="lmLevelBadge">LEVEL 2</span>
      <div class="lm-price" id="lmPrice">$99</div>
      <h2 class="lm-title" id="lmTitle">Signal Analysis</h2>
      <p class="lm-descriptor" id="lmDescriptor"></p>
    </div>

    {{-- WHAT YOU'LL SEE --}}
    <div class="lm-section lm-stagger">
      <div class="lm-section-label">What You&rsquo;ll See</div>
      <ul class="lm-bullets" id="lmBullets"></ul>
    </div>

    {{-- WHY UPGRADE --}}
    <div class="lm-section lm-stagger">
      <div class="lm-section-label">Why Upgrade</div>
      <p class="lm-reason" id="lmReason"></p>
    </div>

    {{-- IS THIS RIGHT FOR YOU? --}}
    <div class="lm-section lm-stagger" id="lmQualifySection">
      <div class="lm-section-label">Is This Right For You?</div>
      <p class="lm-qualify-lead" id="lmQualifyLead">Choose this if:</p>
      <ul class="lm-qualify-list" id="lmQualifyList"></ul>
    </div>

    {{-- SYSTEM POSITION --}}
    <div class="lm-section lm-stagger">
      <div class="lm-section-label">System Position</div>
      <div class="lm-position" id="lmPosition" aria-label="Deployment progression position">
        <div class="lm-pos-step" data-step="1">
          <span class="lm-pos-num">01</span>Scan
        </div>
        <div class="lm-pos-step" data-step="2">
          <span class="lm-pos-num">02</span>Signal
        </div>
        <div class="lm-pos-step" data-step="3">
          <span class="lm-pos-num">03</span>Leverage
        </div>
        <div class="lm-pos-step" data-step="4">
          <span class="lm-pos-num">04</span>Activate
        </div>
        <div class="lm-pos-step" data-step="5">
          <span class="lm-pos-num">05</span>Expand
        </div>
        <div class="lm-pos-step" data-step="6">
          <span class="lm-pos-num">06</span>Managed
        </div>
      </div>
    </div>

    {{-- CTA AREA --}}
    <div class="lm-cta lm-stagger">
      {{-- Ladder awareness banner (shown after 2+ levels or 2+ AI questions) --}}
      <div class="lm-ladder-banner" id="lmLadderBanner">
        <p class="lm-ladder-banner-text" id="lmLadderBannerText">Most users exploring multiple levels choose Signal Analysis first.</p>
        <a class="lm-ladder-banner-link" id="lmLadderBannerLink" href="{{ route('checkout.signal-expansion') }}">See what’s right for you &rarr;</a>
      </div>
      <a class="lm-cta-primary" id="lmCtaPrimary" href="#">Unlock Deeper Signal Intelligence &rarr;</a>
      <p class="lm-cta-subtext" id="lmCtaSubtext">Understand why your score is stuck &mdash; and what to fix first</p>
      <p class="lm-cta-support" id="lmMomentumLine">Most users at your level move here next.</p>
      <p class="lm-cta-subtext2" id="lmCtaSubtext2" style="display:none;font-size:.9rem;text-align:center;color:rgba(221,215,200,.87);margin-top:-4px"></p>
      <button class="lm-cta-secondary" id="lmAskAiAdvisor" type="button">Ask about this level</button>
      <button class="lm-cta-tertiary" id="lmCtaSecondary" type="button">Continue browsing</button>
    </div>

    {{-- INLINE AI ASK PANEL (hidden until triggered) --}}
    <div class="lm-ask-panel" id="lmAskPanel" style="display:none">
      <div class="lm-ask-label">What would you like to know?</div>
      <div class="lm-ask-row">
        <input
          class="lm-ask-input"
          id="lmAskInput"
          type="text"
          maxlength="300"
          autocomplete="off"
          placeholder="e.g. How is this different from a full SEO audit?"
          aria-label="Ask a question about this layer"
        >
        <button class="lm-ask-submit" id="lmAskSubmit" type="button">Ask</button>
      </div>
      <div class="lm-ask-thinking" id="lmAskThinking" style="display:none">Connecting to AI system&hellip;</div>
      <div class="lm-ask-response" id="lmAskResponse" style="display:none" role="region" aria-live="polite"></div>
      <div class="lm-soft-gate" id="lmSoftGate" style="display:none" role="region" aria-label="Deeper insights options">
        <p class="lm-soft-gate-title">You&rsquo;re asking the right questions.</p>
        <p class="lm-soft-gate-sub">Now see exactly what&rsquo;s happening on your site.</p>
        <div class="lm-soft-gate-actions">
          <a href="{{ route('scan.start') }}">Run $2 Scan</a>
          <a href="{{ url('/dashboard') }}">View My Results</a>
          <a href="{{ route('book.index', ['entry' => 'consultation']) }}">Book Consultation</a>
        </div>
      </div>
    </div>

    <div class="lm-scroll-cue" id="lmScrollCue" aria-hidden="true">
      <span class="lm-scroll-cue-chevron"></span>
    </div>

  </div>{{-- /.lm-box --}}
</div>{{-- /#layerModal --}}

<script>
(function () {
  'use strict';

  /* ── Layer content data ── */
  var LAYERS = {
    'level-2': {
      badge:      'LEVEL 2',
      price:      '$99',
      title:      'Signal Analysis',
      descriptor: 'Full signal-by-signal breakdown from your scan \u2014 every category scored and explained.',
      bullets: [
        'The exact signal gaps lowering your visibility score',
        'Where AI cannot confidently cite your site today',
        'The first fixes that raise citation confidence fastest',
        'A ranked action sequence inside your dashboard'
      ],
      reason: 'Once you\'ve seen your baseline, this is where clarity begins. This layer shows exactly where signal is leaking across your content architecture so you can fix the right things first.',
      qualify: {
        lead: 'Choose this if:',
        points: [
          'You have a score but don\'t know why it\'s low',
          'You want clarity before making any changes'
        ]
      },
      position:   2,
      ctaLabel:   'Continue to Next Level \u2192',
      ctaHref:    '{{ route("checkout.signal-expansion") }}'
    },
    'level-3': {
      badge:      'LEVEL 3',
      price:      '$249',
      title:      'Action Plan',
      descriptor: 'Prioritized fix list from your scan — ranked by impact, grouped by effort.',
      bullets: [
        'Your service-by-location coverage map with missing zones highlighted',
        'Which page structures need correction first',
        'The sequence that closes high-value coverage gaps',
        'How your visibility compounds after structural fixes'
      ],
      reason: 'When you know your gaps and are ready to build the actual architecture, this layer constructs the system \u2014 not individual fixes, but the full structural model AI platforms use to surface your site reliably across markets.',
      qualify: {
        lead: 'Choose this if:',
        points: [
          'You know something is wrong with your visibility',
          'You want the exact order to fix it \u2014 not guesswork'
        ]
      },
      position:   3,
      ctaLabel:   'Continue to Next Level \u2192',
      ctaHref:    '{{ route("checkout.structural-leverage") }}'
    },
    'level-4': {
      badge:      'LEVEL 4',
      price:      '$489',
      title:      'Guided Execution',
      descriptor: 'Step-by-step execution checklist inside your dashboard with progress tracking.',
      bullets: [
        'Your action plan organized into an in-dashboard checklist',
        'Guided steps for each fix from your scan',
        'Progress tracking as you work through items',
        'How completed items compound your score over time'
      ],
      reason: 'The complete operating layer \u2014 every page, schema object, and content signal working as a unified whole, tuned for AI recommendation at scale. Built to compound, not to maintain.',
      qualify: {
        lead: 'Choose this if:',
        points: [
          'You\'ve identified your gaps and are ready to deploy',
          'You want a complete system \u2014 not incremental patches'
        ]
      },
      position:   4,
      ctaLabel:   'Continue to Next Level \u2192',
      ctaHref:    '{{ route("checkout.system-activation") }}'
    },

    /* ── Book-page rail keys ── */
    'deep': {
      badge:      'SIGNAL ANALYSIS',
      price:      '$99',
      title:      'Signal Analysis',
      descriptor: 'Deeper structural visibility analysis. Baseline scan intelligence included.',
      bullets: [
        'Your baseline scan context and signal gaps in one view',
        'Where citation confidence is weak across key pages',
        'Which competitors are outranking your AI footprint',
        'A ranked next-step sequence to raise your score'
      ],
      reason: 'Once you\'ve seen your baseline, this is where clarity begins. This layer maps exactly where signal is leaking across your content architecture so you can fix the right things first.',
      qualify: {
        lead: 'Choose this if:',
        points: [
          'You have a score but don\'t know why it\'s low',
          'You want clarity before making any changes'
        ]
      },
      position:   2,
      ctaLabel:   'Continue to Next Level \u2192',
      ctaHref:    '{{ route("checkout.signal-expansion") }}'
    },
    'fix': {
      badge:      'ACTION PLAN',
      price:      '$249',
      title:      'Action Plan',
      descriptor: 'Structured fix planning that builds on scan and signal gap analysis.',
      bullets: [
        'The full service-by-location map your architecture needs',
        'Priority structural corrections with clear execution order',
        'Which coverage gaps block visibility growth right now',
        'How corrected structure improves AI retrieval consistency'
      ],
      reason: 'When you know your gaps and are ready to build the actual architecture, this layer constructs the system \u2014 not individual fixes, but the full structural model AI platforms use to surface your site reliably.',
      qualify: {
        lead: 'Choose this if:',
        points: [
          'You know something is wrong with your visibility',
          'You want the exact order to fix it \u2014 not guesswork'
        ]
      },
      position:   3,
      ctaLabel:   'Continue to Next Level \u2192',
      ctaHref:    '{{ route("checkout.structural-leverage") }}'
    },
    'build': {
      badge:      'GUIDED EXECUTION',
      price:      '$489',
      title:      'Guided Execution',
      descriptor: 'Translate findings into implementation-ready structure across your full domain.',
      bullets: [
        'What the full activation layer deploys across your site',
        'How all signals align into one citation-ready system',
        'Where market-wide coverage expands after deployment',
        'The ongoing visibility gains this unlocks'
      ],
      reason: 'The complete operating layer \u2014 every page, schema object, and content signal working as a unified whole, tuned for AI recommendation at scale. Built to compound, not to maintain.',
      qualify: {
        lead: 'Choose this if:',
        points: [
          'You\'ve identified your gaps and are ready to deploy',
          'You want a complete system \u2014 not incremental patches'
        ]
      },
      position:   4,
      ctaLabel:   'Continue to Next Level \u2192',
      ctaHref:    '{{ route("checkout.system-activation") }}'
    },
    'expand': {
      badge:      'AI VISIBILITY CONSULTATION',
      price:      '$500',
      title:      'AI Visibility Consultation',
      descriptor: 'Guided activation into broader market coverage. Expert interpretation of your results.',
      bullets: [
        'A live 60-minute strategy session focused on your score gaps',
        'Expert interpretation of your dashboard signals and blockers',
        'The highest-leverage moves for your market next',
        'A clear activation sequence tailored to your business'
      ],
      reason: 'Extends what your scan and signal work revealed into a clear, expert-validated activation plan mapped to your dashboard workflow.',
      qualify: {
        lead: 'Choose this if:',
        points: [
          'You have data but want expert eyes on what matters most',
          'You want a clear execution sequence specific to your market'
        ]
      },
      position:   5,
      ctaLabel:   'Book Your AI Visibility Consultation \u2192',
      ctaSubtext:  'Walk away with a clear, expert-built plan for your site.',
      ctaSubtext2: '60-minute strategy session. Get a clear expansion plan.',
      ctaHref:    '{{ route("book.index", ["entry" => "consultation"]) }}'
    },
    'managed': {
      badge:      'FULL SYSTEM ACTIVATION',
      price:      '$5k\u2013$15k+',
      title:      'Full System Activation',
      descriptor: 'Guided system oversight and full done-for-you deployment at market scale.',
      bullets: [
        'Complete done-for-you infrastructure build',
        'Entity architecture, content signal network, citation positioning',
        'Market-level coverage across your target service areas',
        'You own the outcome \u2014 we execute the system',
        'Most buyers arrive through the consultation first'
      ],
      reason: 'The full build path: activate your complete AI visibility system from architecture to citation positioning across your market, guided inside your dashboard and implementation flow.',
      qualify: {
        lead: 'Choose this if:',
        points: [
          'You want the system built and deployed for you',
          'You\'re ready to own the outcome with expert execution'
        ]
      },
      position:   6,
      ctaLabel:   'Continue to Next Level \u2192',
      ctaHref:    '{{ route("book.index", ["entry" => "activation"]) }}'
    }
  };

  /* ── DOM references ── */
  var overlay     = document.getElementById('layerModal');
  if (!overlay) return;
  var box         = overlay.querySelector('.lm-box');

  var posSteps    = overlay.querySelectorAll('.lm-pos-step');
  var elBadge     = overlay.querySelector('#lmLevelBadge');
  var elPrice     = overlay.querySelector('#lmPrice');
  var elTitle     = overlay.querySelector('#lmTitle');
  var elDesc      = overlay.querySelector('#lmDescriptor');
  var elBullets   = overlay.querySelector('#lmBullets');
  var elReason    = overlay.querySelector('#lmReason');
  var elCtaPrimary= overlay.querySelector('#lmCtaPrimary');
  var elCtaSubtext = overlay.querySelector('#lmCtaSubtext');
  var elCtaSubtext2= overlay.querySelector('#lmCtaSubtext2');
  var elMomentumLine = overlay.querySelector('#lmMomentumLine');
  var askPanel    = overlay.querySelector('#lmAskPanel');
  var askAiAdvisor= overlay.querySelector('#lmAskAiAdvisor');
  var askInput    = overlay.querySelector('#lmAskInput');
  var askSubmit   = overlay.querySelector('#lmAskSubmit');
  var askThinking = overlay.querySelector('#lmAskThinking');
  var askResponse = overlay.querySelector('#lmAskResponse');
  var softGate    = overlay.querySelector('#lmSoftGate');
  var elQualifyLead    = overlay.querySelector('#lmQualifyLead');
  var elQualifyList    = overlay.querySelector('#lmQualifyList');
  var ladderBanner     = overlay.querySelector('#lmLadderBanner');
  var ladderBannerText = overlay.querySelector('#lmLadderBannerText');
  var ladderBannerLink = overlay.querySelector('#lmLadderBannerLink');
  var scrollCue        = overlay.querySelector('#lmScrollCue');

  var activeLayer = null;
  var previousFocus = null;
  var aiInteractionCount = 0;
  var levelsOpened = {};
  var ladderBannerShown = false;
  var openedAt = 0;

  /* Next-level lookup by system position */
  var POSITION_TO_KEY = { 2: 'level-2', 3: 'level-3', 4: 'level-4', 5: 'expand', 6: 'managed' };
  var KEY_TO_CHECKOUT = {
    'level-2': { label: 'Signal Analysis',   href: '{{ route("checkout.signal-expansion") }}' },
    'level-3': { label: 'Action Plan',        href: '{{ route("checkout.structural-leverage") }}' },
    'level-4': { label: 'Guided Execution',   href: '{{ route("checkout.system-activation") }}' },
    'expand':  { label: 'Consultation', href: '{{ route("book.index", ["entry" => "consultation"]) }}' },
    'managed': { label: 'Full System Activation', href: '{{ route("book.index", ["entry" => "activation"]) }}' }
  };

  /* Intent-driven soft gate patterns */
  var GATE_RE = /what should i do|is this worth it|why is my score|right for me|where do i start|which level/i;

  /* ── Populate modal with layer data ── */
  function populate(key) {
    var d = LAYERS[key];
    if (!d) return;

    elBadge.textContent  = d.badge;
    elPrice.textContent  = d.price;
    elTitle.textContent  = d.title;
    elDesc.textContent   = d.descriptor;
    elReason.textContent = d.reason;

    /* Qualify section */
    if (d.qualify) {
      elQualifyLead.textContent = d.qualify.lead;
      elQualifyList.innerHTML   = d.qualify.points.map(function (p) { return '<li>' + escHtml(p) + '</li>'; }).join('');
      elQualifyList.parentElement.style.display = '';
    } else {
      elQualifyList.parentElement.style.display = 'none';
    }

    /* Build bullet list */
    elBullets.innerHTML = d.bullets
      .map(function (b) { return '<li>' + escHtml(b) + '</li>'; })
      .join('');

    /* Highlight system position */
    posSteps.forEach(function (step) {
      var n = parseInt(step.getAttribute('data-step'), 10);
      step.classList.toggle('lm-pos-active', n === d.position);
      step.classList.toggle('lm-pos-done',   n < d.position);
    });

    /* CTA */
    elCtaPrimary.href        = d.ctaHref;
    elCtaPrimary.textContent = d.ctaLabel;
    if (elCtaSubtext)  { elCtaSubtext.textContent  = d.ctaSubtext  || 'Understand why your score is stuck \u2014 and what to fix first'; }
    if (elCtaSubtext2) {
      if (d.ctaSubtext2) { elCtaSubtext2.textContent = d.ctaSubtext2; elCtaSubtext2.style.display = ''; }
      else               { elCtaSubtext2.textContent = '';             elCtaSubtext2.style.display = 'none'; }
    }
    if (elMomentumLine) {
      if (d.position <= 4) {
        elMomentumLine.textContent = 'This is the next step after your current score.';
      } else if (d.position === 5) {
        elMomentumLine.textContent = 'Most users at your level move here next.';
      } else {
        elMomentumLine.textContent = 'This unlocks what your score is still missing.';
      }
    }

    /* Reset ask panel */
    askPanel.style.display    = 'none';
    askInput.value            = '';
    askResponse.style.display = 'none';
    askResponse.textContent   = '';
    askThinking.style.display = 'none';
    askSubmit.disabled        = false;
    softGate.style.display    = 'none';
    ladderBanner.style.display = 'none';

    if (box) box.scrollTop = 0;
    updateScrollCue();

    activeLayer = key;
    aiInteractionCount = 0;
  }

  /* ── Open / close ── */
  function openModal(key) {
    if (!LAYERS[key]) return;
    previousFocus = document.activeElement;
    populate(key);
    openedAt = Date.now();

    /* Ladder tracking */
    levelsOpened[key] = true;
    var totalLevels = Object.keys(levelsOpened).length;
    if (totalLevels >= 2 && !ladderBannerShown) {
      showLadderBanner(key);
    }

    document.body.style.overflow = 'hidden';
    overlay.classList.add('open');
    overlay.removeAttribute('aria-hidden');
    setTimeout(function () { overlay.querySelector('#lmClose').focus(); }, 60);
    setTimeout(updateScrollCue, 90);
  }

  function closeModal() {
    overlay.classList.remove('open');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (previousFocus) previousFocus.focus();
    if (box) box.scrollTop = 0;
    updateScrollCue();
  }

  function updateScrollCue() {
    if (!box || !scrollCue) return;
    var canScroll = (box.scrollHeight - box.clientHeight) > 14;
    var atEnd = (box.scrollTop + box.clientHeight) >= (box.scrollHeight - 14);
    box.classList.toggle('lm-can-scroll', canScroll);
    box.classList.toggle('lm-scroll-end', !canScroll || atEnd);
    scrollCue.style.opacity = (canScroll && !atEnd) ? '1' : '0';
  }

  if (box) box.addEventListener('scroll', updateScrollCue, { passive: true });
  window.addEventListener('resize', updateScrollCue);

  /* Close button */
  overlay.querySelector('#lmClose').addEventListener('click', closeModal);
  /* Stay at current level */
  overlay.querySelector('#lmCtaSecondary').addEventListener('click', closeModal);
  /* Backdrop click */
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) closeModal();
  });
  /* Escape key */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && overlay.classList.contains('open')) closeModal();
  });

  /* ── Trap focus inside modal when open ── */
  overlay.addEventListener('keydown', function (e) {
    if (e.key !== 'Tab' || !overlay.classList.contains('open')) return;
    var focusable = Array.from(
      overlay.querySelectorAll('a[href],button:not([disabled]),input:not([disabled])')
    ).filter(function (el) { return el.offsetParent !== null; });
    if (!focusable.length) return;
    var first = focusable[0];
    var last  = focusable[focusable.length - 1];
    if (e.shiftKey) {
      if (document.activeElement === first) { e.preventDefault(); last.focus(); }
    } else {
      if (document.activeElement === last)  { e.preventDefault(); first.focus(); }
    }
  });

  /* ── Delegated trigger: any [data-layer] element ── */
  document.addEventListener('click', function (e) {
    var trigger = e.target.closest('[data-layer]');
    if (!trigger) return;
    e.preventDefault();
    openModal(trigger.getAttribute('data-layer'));
  });

  /* ── Ask About This Layer ── */
  /* Inline ask panel no longer has a CTA entry point; advisor dispatches to floating assistant. */
  askAiAdvisor.addEventListener('click', function () {
    var d = activeLayer ? LAYERS[activeLayer] : null;
    var advisorPrompt = d
      ? 'I\'m considering ' + d.title + ' (' + d.price + '). Is this the right next step for my current visibility gaps?'
      : 'Can you help me choose the right next step based on my current visibility gaps?';

    window.dispatchEvent(new CustomEvent('seoai:open-ai-advisor', {
      detail: {
        prompt: advisorPrompt,
        modalContext: {
          level_key: activeLayer,
          level_title: d ? d.title : null,
          level_price: d ? d.price : null,
          user_state: 'evaluating-upgrade',
          context_page: 'layer-modal'
        }
      }
    }));
    closeModal();
  });

  function submitAsk() {
    var question = askInput.value.trim();
    if (!question || !activeLayer) return;

    var d = LAYERS[activeLayer];
    var context  = d ? (d.title + ' (' + d.price + ')') : activeLayer;
    var message  = 'Regarding SEOAIco ' + context + ': ' + question;

    askSubmit.disabled        = true;
    askThinking.style.display = 'block';
    askResponse.style.display = 'none';
    askResponse.textContent   = '';

    var csrf = document.querySelector('meta[name="csrf-token"]');

    fetch('/ai/chat', {
      method:  'POST',
      headers: {
        'Content-Type':  'application/json',
        'Accept':        'application/json',
        'X-CSRF-TOKEN':  csrf ? csrf.getAttribute('content') : ''
      },
      body: JSON.stringify({
        message: message,
        context_page: 'layer-modal',
        level_key: activeLayer,
        level_title: d ? d.title : null,
        level_price: d ? d.price : null,
        user_state: 'evaluating-upgrade'
      })
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      askThinking.style.display = 'none';
      askSubmit.disabled        = false;
      askResponse.textContent   = (data.ok && data.reply)
        ? data.reply
        : (data.error || 'Unable to connect at this time. Reach us at hello@seoaico.com.');
      askResponse.style.display = 'block';
      aiInteractionCount += 1;
      /* Intent-driven gate: immediate on high-intent question */
      checkIntentGate(question);
      /* Count-based gate fallback: after 2 AI interactions */
      if (aiInteractionCount >= 2) {
        softGate.style.display = 'block';
      }
      /* Ladder banner: show after 2+ AI questions even if only 1 level opened */
      if (aiInteractionCount >= 2 && !ladderBannerShown) {
        showLadderBanner(activeLayer);
      }
    })
    .catch(function () {
      askThinking.style.display = 'none';
      askSubmit.disabled        = false;
      askResponse.textContent   = 'Connection unavailable. Reach us directly at hello@seoaico.com.';
      askResponse.style.display = 'block';
    });
  }

  askSubmit.addEventListener('click', submitAsk);
  askInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') submitAsk();
  });

  /* ── Intent-driven soft gate ── */
  function checkIntentGate (question) {
    if (GATE_RE.test(question)) {
      softGate.style.display = 'block';
    }
  }

  /* ── Ladder awareness banner ── */
  function showLadderBanner (currentKey) {
    if (ladderBannerShown) return;
    var d = currentKey ? LAYERS[currentKey] : null;
    var nextPos = d ? d.position + 1 : 3;
    var nextKey = POSITION_TO_KEY[nextPos];
    var nextInfo = nextKey ? KEY_TO_CHECKOUT[nextKey] : null;
    if (nextInfo) {
      ladderBannerText.textContent = 'Most users at this point move to ' + nextInfo.label + '.';
    } else {
      ladderBannerText.textContent = 'Most users exploring multiple levels choose Signal Analysis first.';
    }
    if (elMomentumLine) {
      elMomentumLine.textContent = ladderBannerText.textContent;
    }
    ladderBannerShown = true;
  }

  /* Start hidden */
  overlay.setAttribute('aria-hidden', 'true');

  /* ── Utility ── */
  function escHtml(str) {
    return str
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;');
  }

}());
</script>
