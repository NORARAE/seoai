{{--
    AI Assistant Widget — floating orb trigger + slide-up chat panel
    Include once, just before </body>.
    Props (optional): $aiGreeting (string), $aiSuggestedPrompts (array),
                      $aiMicroLabel (string), $aiTeaserTitle (string), $aiTeaserText (string),
                      $aiCrawlSummary (array|null), $aiMarketData (array|null)
--}}

@php
$isAuth   = auth()->check();
$userName = $isAuth ? (auth()->user()->name ?? null) : null;
$firstName = $userName ? explode(' ', trim($userName))[0] : null;
$aiTierRank = $isAuth && (auth()->user()?->system_tier instanceof \App\Enums\SystemTier)
    ? auth()->user()->system_tier->rank()
    : ($isAuth ? 1 : 0); // has scan but no paid tier = rank 1; guest = 0

$greeting = $aiGreeting ?? (
    $isAuth && $firstName
        ? "Hi {$firstName} — choose a guided question below for a structured analysis, or type your own."
        : "Hi there — I can explain how AI visibility works, what your scan results mean, or help you figure out the right next step. What's on your mind?"
);

$suggestedPrompts = $aiSuggestedPrompts ?? ($isAuth
    ? ["Explain my market gaps", "What should I build next?", "Show highest impact opportunities", "How do I improve my score?"]
    : ["What does an AI visibility scan check?", "How does the $2 scan work?", "What's the difference between tiers?", "How do I get started?"]);

$microLabel  = $aiMicroLabel  ?? 'AI Analysis Ready';
$teaserTitle = $aiTeaserTitle ?? 'Your AI System';
$teaserText  = $aiTeaserText  ?? 'Get a structured analysis of your market gaps, next actions, or score improvements.';

// Crawl action data — passed from dashboard view (nullable on non-dashboard pages)
$_crawl  = $aiCrawlSummary ?? null;
$_market = $aiMarketData ?? null;
$_actionData = [];
if ($_crawl) {
    if (($_crawl['pages_missing_h1'] ?? 0) > 0) {
        $_actionData[] = ['label' => 'Fix ' . $_crawl['pages_missing_h1'] . ' pages missing H1 tags', 'type' => 'fix', 'key' => 'missing_h1'];
    }
    if (($_crawl['pages_missing_meta_desc'] ?? 0) > 0) {
        $_actionData[] = ['label' => 'Add meta descriptions to ' . $_crawl['pages_missing_meta_desc'] . ' pages', 'type' => 'fix', 'key' => 'missing_meta'];
    }
    if (($_crawl['schema_coverage_pct'] ?? 100) < 60) {
        $_actionData[] = ['label' => 'Improve schema markup (' . $_crawl['schema_coverage_pct'] . '% coverage)', 'type' => 'fix', 'key' => 'schema'];
    }
    if (($_crawl['orphan_pages'] ?? 0) > 0) {
        $_actionData[] = ['label' => 'Fix ' . $_crawl['orphan_pages'] . ' orphan pages with no internal links', 'type' => 'fix', 'key' => 'orphan'];
    }
}
if ($_market) {
    $topGaps = array_slice($_market['high_value_gaps'] ?? [], 0, 2);
    foreach ($topGaps as $gap) {
        $_actionData[] = ['label' => 'Create page: ' . ($gap['suggested_title'] ?? '') . ' (' . ($gap['suggested_url'] ?? '') . ')', 'type' => 'create', 'key' => 'gap_' . ($gap['service'] ?? '') . '_' . ($gap['city'] ?? '')];
    }
}
$_actionDataJson = json_encode(array_slice($_actionData, 0, 5), JSON_UNESCAPED_SLASHES);

$csrfToken = csrf_token();
@endphp

<style>
/* AI ASSISTANT WIDGET ================================================= */
/* BTT is secondary — AI advisor is primary floating action */
:root{--btt-bottom:90px;--btt-bottom-mob:72px}
/* Reduce BTT visual weight so AI advisor reads as primary */
#btt.show{opacity:.45}
#btt.show:hover{opacity:.88}

/* Floating pill trigger */
.aia-trigger {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 8200;
  height: 44px;
  padding: 0 20px 0 13px;
  border-radius: 22px;
  gap: 8px;
  background: linear-gradient(135deg, #d4b85a 0%, #c8a84b 60%, #b8962e 100%);
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  white-space: nowrap;
  overflow: hidden;
  box-shadow: 0 4px 22px rgba(200,168,75,.42), 0 1px 6px rgba(0,0,0,.55);
  transition: transform .22s cubic-bezier(.23,1,.32,1), box-shadow .22s,
              padding .22s cubic-bezier(.23,1,.32,1), border-radius .22s, width .22s;
  -webkit-tap-highlight-color: transparent;
  animation: aiaBreath 5s ease-in-out infinite;
}
.aia-trigger:hover {
  transform: translateY(-2px) scale(1.04);
  box-shadow: 0 8px 36px rgba(200,168,75,.72), 0 2px 10px rgba(0,0,0,.6);
  animation-play-state: paused;
}
.aia-trigger:active { transform: scale(.95); }
.aia-trigger-icon {
  width: 18px; height: 18px; flex-shrink: 0; color: #080808;
  transition: transform .3s;
}
.aia-trigger-label {
  font-family: 'DM Sans', sans-serif;
  font-size: .76rem; font-weight: 600; letter-spacing: .05em;
  color: #080808; line-height: 1;
  transition: opacity .18s, max-width .22s;
  max-width: 160px; opacity: 1; overflow: hidden;
}
.aia-trigger.is-open .aia-trigger-label { max-width: 0; opacity: 0; }
.aia-trigger.is-open {
  padding: 0; width: 44px; justify-content: center;
  border-radius: 50%; animation: none;
}
.aia-trigger.is-open .aia-trigger-icon { transform: rotate(180deg); }
.aia-trigger::after {
  content: ''; position: absolute; inset: -4px; border-radius: 27px;
  border: 1.5px solid rgba(200,168,75,.40);
  animation: aiaPulse 2.8s ease-out infinite; pointer-events: none;
  transition: border-radius .22s;
}
.aia-trigger.is-open::after { border-radius: 50%; }
.aia-trigger.is-wow::after {
  border-color: rgba(200,168,75,.65);
  animation: aiaPulseWow 1.4s ease-out infinite;
}
.aia-trigger.is-glow {
  animation: aiaGlowPulse 1.8s cubic-bezier(.25,.46,.45,.94) 1 both;
}
@keyframes aiaBreath {
  0%,100% { box-shadow: 0 4px 22px rgba(200,168,75,.42), 0 1px 6px rgba(0,0,0,.55); }
  50%      { box-shadow: 0 4px 30px rgba(200,168,75,.62), 0 1px 6px rgba(0,0,0,.55); }
}
@keyframes aiaGlowPulse {
  0%   { box-shadow: 0 4px 22px rgba(200,168,75,.42), 0 1px 6px rgba(0,0,0,.55); }
  45%  { box-shadow: 0 6px 44px rgba(200,168,75,.88), 0 2px 18px rgba(0,0,0,.5), 0 0 70px rgba(200,168,75,.22); }
  100% { box-shadow: 0 4px 22px rgba(200,168,75,.42), 0 1px 6px rgba(0,0,0,.55); }
}
@keyframes aiaPulse {
  0%   { transform: scale(1);    opacity: .5; }
  65%  { transform: scale(1.24); opacity: 0; }
  100% { transform: scale(1.24); opacity: 0; }
}
@keyframes aiaPulseWow {
  0%   { transform: scale(1);    opacity: .72; }
  60%  { transform: scale(1.36); opacity: 0; }
  100% { transform: scale(1.36); opacity: 0; }
}
/* Micro-label above button */
.aia-micro {
  position: fixed;
  bottom: 80px;
  right: 24px;
  z-index: 8199;
  font-family: 'DM Sans', sans-serif;
  font-size: .6rem;
  letter-spacing: .14em;
  text-transform: uppercase;
  color: rgba(200,168,75,.55);
  opacity: 0;
  animation: aiaMicroFade .5s ease 1s forwards;
  pointer-events: none;
  text-align: right;
  white-space: nowrap;
  transition: opacity .4s;
}
.aia-micro.is-hidden { opacity: 0 !important; animation: none; }
@keyframes aiaMicroFade { to { opacity: 1; } }

/* Teaser bubble */
.aia-teaser {
  position: fixed; bottom: 136px; right: 24px; z-index: 8100;
  background: linear-gradient(155deg, #191610 0%, #120f08 100%);
  border: 1px solid rgba(200,168,75,.22);
  border-radius: 12px 12px 4px 12px;
  padding: 12px 28px 12px 15px;
  max-width: 240px;
  box-shadow: 0 8px 32px rgba(0,0,0,.6), 0 0 0 1px rgba(200,168,75,.05);
  opacity: 0; transform: translateY(8px) scale(.95); visibility: hidden;
  transition: opacity .3s ease, transform .3s cubic-bezier(.23,1,.32,1), visibility 0s .3s;
  pointer-events: none; cursor: pointer;
}
.aia-teaser.is-visible {
  opacity: 1; transform: none; visibility: visible;
  transition: opacity .3s ease, transform .3s cubic-bezier(.23,1,.32,1), visibility 0s 0s;
  pointer-events: auto;
}
.aia-teaser-text {
  font-family: 'DM Sans', sans-serif;
  font-size: .75rem; line-height: 1.6; color: rgba(237,232,222,.84);
}
.aia-teaser-text strong { color: #d4b85a; font-weight: 600; }
.aia-teaser::before {
  content: ''; position: absolute; bottom: -7px; right: 20px;
  width: 12px; height: 12px; background: #120f08;
  border-right: 1px solid rgba(200,168,75,.22); border-bottom: 1px solid rgba(200,168,75,.22);
  transform: rotate(45deg);
}
.aia-teaser-x {
  position: absolute; top: 7px; right: 8px;
  background: none; border: none; cursor: pointer;
  color: rgba(168,168,160,.38); font-size: .68rem; line-height: 1;
  padding: 2px 5px; border-radius: 4px; transition: color .18s;
}
.aia-teaser-x:hover { color: rgba(200,168,75,.7); }

/* Backdrop */
.aia-backdrop {
  position: fixed; inset: 0; z-index: 8050;
  background: rgba(0,0,0,.5);
  backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px);
  opacity: 0; visibility: hidden;
  transition: opacity .32s ease, visibility 0s .32s;
}
.aia-backdrop.is-open {
  opacity: 1; visibility: visible;
  transition: opacity .32s ease, visibility 0s 0s;
}

/* Panel */
.aia-panel {
  position: fixed; bottom: 0; right: 0; z-index: 8300;
  width: 400px; max-width: 100vw; height: min(640px, 90vh);
  background: linear-gradient(170deg, #14120b 0%, #0e0c07 55%, #111008 100%);
  border: 1px solid rgba(200,168,75,.18); border-bottom: none;
  border-radius: 16px 16px 0 0;
  box-shadow: 0 -8px 60px rgba(0,0,0,.75), 0 -2px 20px rgba(200,168,75,.06);
  display: flex; flex-direction: column;
  transform: translateY(100%) scale(.97);
  transform-origin: bottom right;
  visibility: hidden;
  transition: transform .38s cubic-bezier(.19,1,.22,1), visibility 0s .38s;
  overflow: hidden;
}
.aia-panel.is-open {
  transform: translateY(0) scale(1);
  visibility: visible;
  transition: transform .38s cubic-bezier(.19,1,.22,1), visibility 0s 0s;
}
.aia-panel::before {
  content: ''; position: absolute; top: 0; left: -100%; height: 1px; width: 100%;
  background: linear-gradient(90deg, transparent 0%, rgba(200,168,75,.6) 50%, transparent 100%);
  pointer-events: none; z-index: 1; opacity: 0;
}
.aia-panel.is-open::before { animation: aiaShimmer .7s ease-out .1s both; }
@keyframes aiaShimmer {
  from { left: -100%; opacity: 1; }
  to   { left: 100%;  opacity: 0; }
}

/* Header */
.aia-header {
  display: flex; align-items: center; gap: 12px;
  padding: 14px 18px 13px; border-bottom: 1px solid rgba(200,168,75,.09);
  flex-shrink: 0; background: rgba(0,0,0,.18);
}
.aia-h-avatar {
  width: 38px; height: 38px; border-radius: 50%;
  background: linear-gradient(145deg, #d4b85a, #c8a84b);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  box-shadow: 0 2px 12px rgba(200,168,75,.3);
}
.aia-h-avatar svg { width: 17px; height: 17px; }
.aia-h-info { flex: 1; min-width: 0; }
.aia-h-name {
  font-family: 'DM Sans', sans-serif;
  font-size: .83rem; font-weight: 600; letter-spacing: .04em;
  color: #ede8de; line-height: 1.2;
}
.aia-badge {
  display: inline-block;
  background: rgba(200,168,75,.1); border: 1px solid rgba(200,168,75,.22);
  border-radius: 4px; padding: 1px 5px; font-size: .56rem; letter-spacing: .1em;
  color: rgba(200,168,75,.72); margin-left: 4px; vertical-align: middle; font-weight: 500;
}
.aia-h-sub {
  font-family: 'DM Sans', sans-serif; font-size: .63rem; letter-spacing: .08em;
  text-transform: uppercase; color: rgba(200,168,75,.52); margin-top: 3px;
  display: flex; align-items: center; gap: 5px;
}
.aia-status-dot {
  width: 5px; height: 5px; border-radius: 50%;
  background: rgba(100,195,140,.9); box-shadow: 0 0 6px rgba(100,195,140,.42);
}
.aia-close-btn {
  background: transparent; border: none; cursor: pointer;
  padding: 7px; color: rgba(168,168,160,.45); border-radius: 7px;
  display: flex; align-items: center; justify-content: center;
  transition: color .18s, background .18s; flex-shrink: 0;
}
.aia-close-btn:hover { color: rgba(200,168,75,.85); background: rgba(200,168,75,.07); }
.aia-close-btn svg { width: 15px; height: 15px; }

/* Messages */
.aia-messages {
  flex: 1; overflow-y: auto; padding: 16px 14px 8px;
  display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth;
  scrollbar-width: thin; scrollbar-color: rgba(200,168,75,.12) transparent;
}
.aia-messages::-webkit-scrollbar { width: 3px; }
.aia-messages::-webkit-scrollbar-thumb { background: rgba(200,168,75,.14); border-radius: 4px; }

.aia-msg {
  display: flex; gap: 8px; align-items: flex-end;
  animation: aiaMsgIn .26s cubic-bezier(.23,1,.32,1) both;
}
@keyframes aiaMsgIn {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.aia-msg.is-user { flex-direction: row-reverse; }
.aia-av {
  width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0; margin-bottom: 2px;
  display: flex; align-items: center; justify-content: center;
  font-family: 'DM Sans', sans-serif; font-size: .55rem; font-weight: 700;
}
.aia-msg.is-ai   .aia-av { background: linear-gradient(145deg,#d4b85a,#c8a84b); color: #080808; }
.aia-msg.is-user .aia-av { background: rgba(255,255,255,.05); color: rgba(168,168,160,.7); border: 1px solid rgba(255,255,255,.07); }
.aia-bub {
  max-width: 84%; padding: 10px 14px; border-radius: 14px;
  font-family: 'DM Sans', sans-serif; font-size: .83rem; line-height: 1.65;
}
.aia-msg.is-ai   .aia-bub { background: rgba(255,255,255,.035); border: 1px solid rgba(200,168,75,.1); color: rgba(237,232,222,.9); border-bottom-left-radius: 4px; }
.aia-msg.is-user .aia-bub { background: rgba(200,168,75,.11); border: 1px solid rgba(200,168,75,.2); color: rgba(237,232,222,.92); border-bottom-right-radius: 4px; }

/* Typing dots */
.aia-typing { display: flex; gap: 8px; align-items: flex-end; }
.aia-typing-bub {
  background: rgba(255,255,255,.035); border: 1px solid rgba(200,168,75,.1);
  border-radius: 14px 14px 14px 4px; padding: 12px 16px;
  display: flex; gap: 4px; align-items: center;
}
.aia-dot { width: 5px; height: 5px; border-radius: 50%; background: rgba(200,168,75,.55); animation: aiaDot 1.3s ease-in-out infinite; }
.aia-dot:nth-child(2) { animation-delay: .18s; }
.aia-dot:nth-child(3) { animation-delay: .36s; }
@keyframes aiaDot {
  0%,60%,100% { transform: translateY(0); opacity: .4; }
  30%          { transform: translateY(-4px); opacity: 1; }
}

/* Chips */
.aia-chips { padding: 0 14px 10px; display: flex; gap: 6px; flex-wrap: wrap; flex-shrink: 0; flex-direction: column; }
.aia-chips-label {
  font-family: 'DM Sans', sans-serif; font-size: .6rem; letter-spacing: .08em; text-transform: uppercase;
  color: rgba(200,168,75,.45); padding: 4px 2px 2px; font-weight: 500;
}
.aia-chips-row { display: flex; gap: 6px; flex-wrap: wrap; }
.aia-chip {
  background: rgba(200,168,75,.06); border: 1px solid rgba(200,168,75,.17); border-radius: 18px;
  padding: 5px 11px; font-family: 'DM Sans', sans-serif; font-size: .7rem; letter-spacing: .03em;
  color: rgba(200,168,75,.84); cursor: pointer; white-space: nowrap;
  transition: background .2s, border-color .2s, color .2s;
  -webkit-tap-highlight-color: transparent;
}
.aia-chip--guided {
  background: rgba(200,168,75,.1); border-color: rgba(200,168,75,.28);
  padding: 7px 13px; font-size: .75rem; border-radius: 8px; white-space: normal; text-align: left; width: 100%;
}
.aia-chip--guided:hover { background: rgba(200,168,75,.18); border-color: rgba(200,168,75,.5); color: #d8be72; }
.aia-chip:hover { background: rgba(200,168,75,.13); border-color: rgba(200,168,75,.36); color: #d8be72; }
.aia-chips.gone { display: none; }

/* Action panel — shown after AI response */
.aia-actions {
  margin: 6px 14px 2px; padding: 11px 13px; background: rgba(200,168,75,.05);
  border: 1px solid rgba(200,168,75,.12); border-radius: 10px;
}
.aia-actions-label {
  font-family: 'DM Sans', sans-serif; font-size: .58rem; letter-spacing: .09em; text-transform: uppercase;
  color: rgba(200,168,75,.4); font-weight: 600; margin-bottom: 8px;
}
.aia-action-item {
  display: flex; align-items: center; gap: 9px; padding: 7px 9px; margin-bottom: 4px;
  background: rgba(200,168,75,.05); border: 1px solid rgba(200,168,75,.1); border-radius: 7px;
  font-family: 'DM Sans', sans-serif; font-size: .75rem; color: rgba(237,232,222,.8);
  cursor: default;
}
.aia-action-item:last-child { margin-bottom: 0; }
.aia-action-dot {
  width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
  background: rgba(200,168,75,.7);
}
.aia-action-dot--create { background: rgba(100,200,120,.7); }

/* Error */
.aia-err {
  margin: 0 14px 8px; padding: 9px 13px;
  background: rgba(180,70,70,.1); border: 1px solid rgba(180,70,70,.2); border-radius: 8px;
  font-family: 'DM Sans', sans-serif; font-size: .77rem; line-height: 1.55; color: rgba(240,175,175,.85);
  display: none; flex-shrink: 0;
}
.aia-err.on { display: block; }

/* Input row */
.aia-input-row {
  display: flex; align-items: flex-end; gap: 10px;
  padding: 11px 13px calc(11px + env(safe-area-inset-bottom,0px));
  border-top: 1px solid rgba(200,168,75,.07); flex-shrink: 0;
  background: rgba(6,5,3,.65);
}
.aia-ta {
  flex: 1; background: rgba(255,255,255,.04); border: 1px solid rgba(200,168,75,.13);
  border-radius: 10px; padding: 9px 13px;
  font-family: 'DM Sans', sans-serif; font-size: .84rem; color: rgba(237,232,222,.92);
  resize: none; min-height: 38px; max-height: 120px; line-height: 1.5; outline: none;
  transition: border-color .2s;
}
.aia-ta::placeholder { color: rgba(168,168,160,.32); }
.aia-ta:focus { border-color: rgba(200,168,75,.34); }
.aia-send {
  width: 38px; height: 38px; border-radius: 50%;
  background: linear-gradient(145deg,#d4b85a,#c8a84b); border: none; cursor: pointer; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 2px 10px rgba(200,168,75,.22);
  transition: transform .18s, box-shadow .18s, opacity .18s;
}
.aia-send:hover { transform: scale(1.1); box-shadow: 0 4px 18px rgba(200,168,75,.38); }
.aia-send:active { transform: scale(.92); }
.aia-send:disabled { opacity: .35; cursor: not-allowed; transform: none; }
.aia-send svg { width: 14px; height: 14px; color: #080808; }

@media (max-width: 520px) {
  /* --mob-bar-h: 0px default; override on pages with fixed bottom bar.
     Stack from bottom: bar → gap 16px → AI trigger (40px) → gap 16px → BTT (40px) */
  .aia-trigger {
    bottom: calc(16px + var(--mob-bar-h,0px) + env(safe-area-inset-bottom,0px));
    right: 16px;
    padding: 0; width: 44px; height: 44px; border-radius: 50%; gap: 0;
  }
  .aia-trigger .aia-trigger-label { display: none; }
  .aia-micro { display: none; }
  .aia-teaser {
    right: 16px;
    /* sits above the trigger: trigger bottom + trigger height (40px) + gap (12px) */
    bottom: calc(68px + var(--mob-bar-h,0px) + env(safe-area-inset-bottom,0px));
  }
  .aia-panel { width: 100vw; border-radius: 20px 20px 0 0; height: min(600px, 92vh); }
}
/* Decision nudge — appended below every AI reply */
.aia-nudge{margin:6px 0 0 34px;padding:10px 14px;background:rgba(200,168,75,.06);border:1px solid rgba(200,168,75,.18);border-radius:0 10px 10px 10px;animation:aiaMsgIn .3s cubic-bezier(.23,1,.32,1) .05s both}
.aia-nudge-label{font-size:.6rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(200,168,75,.58);margin-bottom:7px;font-family:'DM Sans',sans-serif}
.aia-nudge-links{display:flex;flex-direction:column;gap:5px}
.aia-nudge-link{display:flex;align-items:center;gap:6px;font-family:'DM Sans',sans-serif;font-size:.82rem;color:rgba(200,168,75,.9);text-decoration:none;cursor:pointer;background:none;border:none;padding:3px 0;text-align:left;transition:color .18s;line-height:1.4}
.aia-nudge-link::before{content:'\2192';font-size:.75rem;opacity:.7;flex-shrink:0}
.aia-nudge-link:hover{color:rgba(228,198,112,1)}
</style>

{{-- Teaser tooltip --}}
<div class="aia-teaser" id="aiaTeaser">
  <button class="aia-teaser-x" id="aiaTeaserX" type="button" aria-label="Dismiss">&#x2715;</button>
  <p class="aia-teaser-text">
    <strong>{{ $teaserTitle }}</strong><br>
    {{ $teaserText }}
  </p>
</div>

{{-- Micro-label above orb --}}
<div class="aia-micro" id="aiaMicro" aria-hidden="true">{{ $microLabel }}</div>

{{-- Orb trigger --}}
<button class="aia-trigger" id="aiaTrigger" type="button"
  aria-label="Open AI Chat Assistant" aria-expanded="false" aria-controls="aiaPanel">
  <svg class="aia-trigger-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    <path d="M8 10h.01M12 10h.01M16 10h.01" stroke-width="2.5"/>
  </svg>
  <span class="aia-trigger-label">Ask Your AI Advisor</span>
</button>

{{-- Backdrop --}}
<div class="aia-backdrop" id="aiaBackdrop" aria-hidden="true"></div>

{{-- Panel --}}
<div class="aia-panel" id="aiaPanel" role="dialog"
  aria-label="AI Chat Assistant" aria-modal="true" aria-hidden="true">

  <div class="aia-header">
    <div class="aia-h-avatar" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="#080808"
        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        <path d="M8 10h.01M12 10h.01M16 10h.01" stroke-width="2.5"/>
      </svg>
    </div>
    <div class="aia-h-info">
      <div class="aia-h-name">
        SEO AI Co&trade; Assistant <span class="aia-badge">AI</span>
      </div>
      <div class="aia-h-sub">
        <span class="aia-status-dot" aria-hidden="true"></span>
        Online &middot; Powered by GPT-4o mini
      </div>
    </div>
    <button class="aia-close-btn" id="aiaClose" aria-label="Close assistant">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
        stroke-width="2" stroke-linecap="round" aria-hidden="true">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  </div>

  <div class="aia-messages" id="aiaMessages"
    role="log" aria-live="polite" aria-label="Conversation"></div>

  <div class="aia-err" id="aiaErr" role="alert"></div>

  <div class="aia-chips" id="aiaChips" aria-label="Guided questions">
    @if($isAuth)
    <div class="aia-chips-label">Guided questions</div>
    @endif
    @foreach($suggestedPrompts as $p)
      <button class="aia-chip{{ $isAuth ? ' aia-chip--guided' : '' }}" type="button" data-prompt="{{ $p }}">{{ $p }}</button>
    @endforeach
  </div>

  <div class="aia-input-row">
    <textarea class="aia-ta" id="aiaInput"
      placeholder="{{ $isAuth ? 'Or ask a specific question…' : 'Ask anything…' }}" rows="1"
      aria-label="Your message" autocomplete="off"
      spellcheck="true" maxlength="600"></textarea>
    <button class="aia-send" id="aiaSend" type="button" aria-label="Send" disabled>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="22" y1="2" x2="11" y2="13"/>
        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
      </svg>
    </button>
  </div>
</div>

<script>
(function () {
  'use strict';
  var CHAT = '/ai/chat';
  var CSRF = '{{ $csrfToken }}';
  var MSG     = @json($greeting);
  var IS_AUTH = {{ $isAuth ? 'true' : 'false' }};
  var TIER_RANK = {{ (int) $aiTierRank }};
  var ACTION_DATA = {!! $_actionDataJson !!};

  /* ── Behavior signal reader ────────────────────────────────────────── */
  function readBehaviorSignals() {
    var signals = { hesitation_type: null, cta_label: null, hours_since_action: null, section_views: {} };
    try {
      // Last CTA clicked
      var lastCta = localStorage.getItem('seo_last_cta');
      if (lastCta) {
        var ctaData = JSON.parse(lastCta);
        signals.cta_label = ctaData.label || null;
      }

      // Hours since last tracked interaction
      var lastInteraction = localStorage.getItem('seo_last_interaction');
      if (lastInteraction) {
        var elapsed = Date.now() - parseInt(lastInteraction, 10);
        signals.hours_since_action = Math.round(elapsed / 3600000);
      }

      // Analyse event log for hesitation patterns
      var rawLog = localStorage.getItem('seo_event_log');
      if (rawLog) {
        var log = JSON.parse(rawLog);
        var sectionCounts = {};
        var ctaLocations = [];
        log.forEach(function(e) {
          if (e.event === 'section_view' && e.section) {
            sectionCounts[e.section] = (sectionCounts[e.section] || 0) + 1;
          }
          if (e.event === 'cta_click' && e.location) {
            ctaLocations.push(e.location);
          }
        });
        signals.section_views = sectionCounts;

        // Hesitation case 1: viewed a section 3+ times but no CTA click from it
        var heavilyViewed = Object.keys(sectionCounts).filter(function(s) { return sectionCounts[s] >= 3; });
        if (heavilyViewed.length > 0) {
          var viewedSections = heavilyViewed.join(', ');
          // Check if any CTA was clicked from those sections
          var clickedFromViewed = ctaLocations.some(function(loc) {
            return heavilyViewed.some(function(s) { return loc.indexOf(s) !== -1; });
          });
          if (!clickedFromViewed) {
            signals.hesitation_type = 'repeated_view_no_upgrade';
            signals.heavily_viewed = viewedSections;
          }
        }

        // Hesitation case 2: CTA clicked but no tier advancement (same tier multiple clicks)
        var ctaClickedCurrent = ctaLocations.filter(function(loc) { return loc.indexOf('next_move') !== -1 || loc.indexOf('upsell') !== -1; });
        if (ctaClickedCurrent.length >= 2 && !signals.hesitation_type) {
          signals.hesitation_type = 'cta_clicked_no_conversion';
        }
      }

      // Hesitation case 3: stalled (24h+ with no action)
      if (signals.hours_since_action !== null && signals.hours_since_action >= 24 && !signals.hesitation_type) {
        signals.hesitation_type = 'stalled';
      }

    } catch (e) { /* localStorage unavailable or corrupt — fail silently */ }
    return signals;
  }

  var trigger = document.getElementById('aiaTrigger');
  var backdrop= document.getElementById('aiaBackdrop');
  var panel   = document.getElementById('aiaPanel');
  var closeBtn= document.getElementById('aiaClose');
  var msgs    = document.getElementById('aiaMessages');
  var ta      = document.getElementById('aiaInput');
  var send    = document.getElementById('aiaSend');
  var chips   = document.getElementById('aiaChips');
  var errEl   = document.getElementById('aiaErr');
  var teaser  = document.getElementById('aiaTeaser');
  var teaserX = document.getElementById('aiaTeaserX');
  var microEl = document.getElementById('aiaMicro');

  var open=false, loading=false, greeted=false, interacted=false;
  var history=[], tTimer=null;
  var pendingContext = null;
  var aiRespCount = 0;

  /* Teaser — auto-show after 2.5 s, auto-dismiss after 6 s */
  tTimer = setTimeout(function () {
    teaser.classList.add('is-visible');
    setTimeout(dismissTeaser, 6000);
  }, 2500);
  function dismissTeaser () {
    teaser.classList.remove('is-visible');
    clearTimeout(tTimer);
  }
  teaserX.addEventListener('click', function (e) { e.stopPropagation(); dismissTeaser(); });
  teaser.addEventListener('click', function () { dismissTeaser(); openPanel(); });

  /* Open / close */
  function openPanel () {
    open = true;
    dismissTeaser();
    if (microEl) microEl.classList.add('is-hidden');
    panel.classList.add('is-open');
    backdrop.classList.add('is-open');
    trigger.classList.add('is-open');
    trigger.setAttribute('aria-expanded', 'true');
    panel.removeAttribute('aria-hidden');
    if (!greeted) { greeted = true; addMsg('ai', MSG); }
    requestAnimationFrame(function () { ta.focus(); });
  }
  function closePanel () {
    open = false;
    panel.classList.remove('is-open');
    backdrop.classList.remove('is-open');
    trigger.classList.remove('is-open');
    trigger.setAttribute('aria-expanded', 'false');
    panel.setAttribute('aria-hidden', 'true');
    if (microEl) microEl.classList.remove('is-hidden');
    trigger.focus();
  }

  trigger.addEventListener('click', function () { open ? closePanel() : openPanel(); });
  closeBtn.addEventListener('click', closePanel);
  backdrop.addEventListener('click', closePanel);
  document.addEventListener('keydown', function (e) { if (e.key==='Escape' && open) closePanel(); });

  /* Chips */
  chips.querySelectorAll('.aia-chip').forEach(function (b) {
    b.addEventListener('click', function () { if (b.dataset.prompt) go(b.dataset.prompt); });
  });

  /* Textarea */
  ta.addEventListener('input', function () {
    ta.style.height = 'auto';
    ta.style.height = Math.min(ta.scrollHeight, 120) + 'px';
    send.disabled = ta.value.trim() === '' || loading;
    clrErr();
  });
  ta.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); if (!send.disabled) go(ta.value.trim()); }
  });
  send.addEventListener('click', function () { if (!send.disabled) go(ta.value.trim()); });

  /* Send */
  function go (text) {
    if (!text || loading) return;
    if (!interacted) { chips.classList.add('gone'); interacted = true; }
    clrErr();
    ta.value = ''; ta.style.height = 'auto'; send.disabled = true;
    addMsg('user', text);
    history.push({ role: 'user', content: text });
    showDots(); loading = true;

    fetch(CHAT, {
      method: 'POST',
      headers: {
        'Content-Type':  'application/json',
        'Accept':        'application/json',
        'X-CSRF-TOKEN':  CSRF
      },
      body: JSON.stringify({
        message: text,
        history: history.slice(0, -1),
        context_page: pendingContext && pendingContext.context_page ? pendingContext.context_page : 'ai-assistant',
        level_key: pendingContext ? pendingContext.level_key : null,
        level_title: pendingContext ? pendingContext.level_title : null,
        level_price: pendingContext ? pendingContext.level_price : null,
        user_state: pendingContext ? pendingContext.user_state : 'browsing',
        behavior_signals: IS_AUTH ? readBehaviorSignals() : null
      })
    })
    .then(function (r) {
      hideDots(); loading = false;
      if (!r.ok) { showErr('Assistant temporarily unavailable — please try again.'); return null; }
      return r.json();
    })
    .then(function (d) {
      if (!d) return;
      if (!d.ok || d.error) { showErr(d.error || 'Something went wrong.'); return; }
      var rep = d.reply || '(no reply)';
      addMsg('ai', rep);
      if (IS_AUTH && ACTION_DATA && ACTION_DATA.length) { renderActionPanel(); }
      addNextStepNudge(text);
      aiRespCount += 1;
      history.push({ role: 'assistant', content: rep });
      if (history.length > 12) history = history.slice(-12);
    })
    .catch(function () { hideDots(); loading = false; showErr('Could not reach the assistant.'); })
    .finally(function () {
      send.disabled = ta.value.trim() === '';
      pendingContext = null;
    });
  }

  window.addEventListener('seoai:open-ai-advisor', function (event) {
    var detail = event && event.detail ? event.detail : {};
    pendingContext = detail.modalContext || null;
    openPanel();
    if (detail.prompt) {
      ta.value = detail.prompt;
      ta.style.height = 'auto';
      ta.style.height = Math.min(ta.scrollHeight, 120) + 'px';
      send.disabled = false;
      requestAnimationFrame(function () { ta.focus(); });
    }
  });

  /* DOM helpers */
  function addMsg (role, text) {
    var u = role === 'user';
    var w = document.createElement('div'); w.className = 'aia-msg ' + (u ? 'is-user' : 'is-ai');
    var av = document.createElement('div'); av.className = 'aia-av'; av.setAttribute('aria-hidden', 'true'); av.textContent = u ? 'You' : 'AI';
    var b = document.createElement('div'); b.className = 'aia-bub'; b.innerHTML = fmt(text);
    w.appendChild(av); w.appendChild(b); msgs.appendChild(w);
    msgs.scrollTop = msgs.scrollHeight;
  }

  /* ── Intent classification ── */
  function classifyIntent (text) {
    var t = (text || '').toLowerCase();
    if (/what should i do|where do i start|next step|how do i fix|what.s first|fix first/.test(t))         return 'action';
    if (/is this worth it|should i (get|buy|upgrade|choose)|right for me|makes sense|which (plan|level|tier)/.test(t)) return 'qualify';
    if (/why is my score|score (is )?low|score mean|what does.{0,20}score|bad score|score stuck/.test(t))  return 'score';
    if (/confused|don.t understand|what does this mean|can you explain|not sure/.test(t))                   return 'confused';
    return IS_AUTH ? 'has-scan' : 'no-scan';
  }

  /* ── Append action panel below AI message (data-driven from crawl) ── */
  function renderActionPanel () {
    var panel = document.createElement('div');
    panel.className = 'aia-actions';
    var lbl = document.createElement('div'); lbl.className = 'aia-actions-label'; lbl.textContent = 'Suggested actions';
    panel.appendChild(lbl);
    ACTION_DATA.forEach(function (action) {
      var item = document.createElement('div'); item.className = 'aia-action-item';
      var dot = document.createElement('div');
      dot.className = 'aia-action-dot' + (action.type === 'create' ? ' aia-action-dot--create' : '');
      var txt = document.createElement('span'); txt.textContent = action.label;
      item.appendChild(dot); item.appendChild(txt);
      panel.appendChild(item);
    });
    msgs.appendChild(panel);
    msgs.scrollTop = msgs.scrollHeight;
  }

  /* ── Append next-step nudge below AI message (tier-aware) ── */
  function addNextStepNudge (userMsg) {
    var intent  = classifyIntent(userMsg);
    var links   = [];

    // Tier-aware: never suggest a tier the user already owns
    if (!IS_AUTH || TIER_RANK === 0) {
      // Guest or no scan
      links = [{ label: 'Get your baseline — run your $2 scan', href: '{{ route("scan.start") }}' }];
    } else if (TIER_RANK === 1) {
      // Has scan, no paid tier
      if (intent === 'confused') {
        links = [{ label: 'Map this together — Book Consultation', href: '{{ route("book.index", ["entry" => "consultation"]) }}' }];
      } else {
        links = [{ label: 'See why your score is what it is — Signal Analysis ($99)', href: '{{ route("checkout.signal-expansion") }}' }];
      }
    } else if (TIER_RANK === 2) {
      // Has Signal Analysis
      if (intent === 'confused') {
        links = [{ label: 'Map this together — Book Consultation', href: '{{ route("book.index", ["entry" => "consultation"]) }}' }];
      } else {
        links = [{ label: 'Turn your signals into ranked fixes — Action Plan ($249)', href: '{{ route("checkout.structural-leverage") }}' }];
      }
    } else if (TIER_RANK === 3) {
      // Has Action Plan
      links = [{ label: 'Execute fix by fix, with progress tracking — Guided Execution ($489)', href: '{{ route("checkout.system-activation") }}' }];
    } else {
      // Has all tiers — only consultation makes sense
      links = [{ label: 'Deploy this at scale — Book Strategy Session', href: '{{ route("book.index", ["entry" => "dashboard-upgrade"]) }}' }];
    }

    var nudge  = document.createElement('div');
    nudge.className = 'aia-nudge';
    var lbl = document.createElement('div'); lbl.className = 'aia-nudge-label'; lbl.textContent = 'Next step';
    var wrap = document.createElement('div'); wrap.className = 'aia-nudge-links';
    links.forEach(function (l) {
      var a = document.createElement('a'); a.className = 'aia-nudge-link';
      a.href = l.href; a.textContent = l.label;
    wrap.appendChild(a);
    });
    nudge.appendChild(lbl); nudge.appendChild(wrap);
    msgs.appendChild(nudge);
    msgs.scrollTop = msgs.scrollHeight;
  }

  /* ── Floating assistant soft gate: trigger on high-intent queries ── */
  var ASSISTANT_GATE_RE = /what should i do|is this worth it|why is my score|right for me|which level|where do i start/i;
  function maybeShowAssistantGate (userMsg) {
    if (ASSISTANT_GATE_RE.test(userMsg) && aiRespCount >= 1) {
      /* We don't have a gate UI in the floating assistant — handled by the modal.
         Future: could dispatch an event to prompt the modal to open. No-op for now. */
    }
  }

  var dotEl = null;
  function showDots () {
    if (dotEl) return;
    dotEl = document.createElement('div'); dotEl.className = 'aia-typing';
    dotEl.innerHTML = '<div class="aia-av" aria-hidden="true" style="width:26px;height:26px;border-radius:50%;background:linear-gradient(145deg,#d4b85a,#c8a84b);color:#080808;font-size:.55rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-bottom:2px">AI</div>' +
      '<div class="aia-typing-bub" aria-label="Typing"><span class="aia-dot"></span><span class="aia-dot"></span><span class="aia-dot"></span></div>';
    msgs.appendChild(dotEl); msgs.scrollTop = msgs.scrollHeight;
  }
  function hideDots () { if (dotEl) { dotEl.remove(); dotEl = null; } }
  function showErr (m) { errEl.textContent = m; errEl.classList.add('on'); msgs.scrollTop = msgs.scrollHeight; }
  function clrErr ()   { errEl.textContent = ''; errEl.classList.remove('on'); }
  function fmt (t) {
    var clean = sanitizeBookingCopy(t || '');
    return clean.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
      .replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>')
      .replace(/\*(.+?)\*/g,'<em>$1</em>')
      .replace(/`(.+?)`/g,'<code style="background:rgba(200,168,75,.08);padding:1px 5px;border-radius:3px;font-size:.82em">$1</code>')
      .replace(/\n/g,'<br>');
  }

  function sanitizeBookingCopy (t) {
    return t
      .replace(/\/book#05\b/gi, 'Book Consultation')
      .replace(/\/book\?entry=consultation\b/gi, 'Book Consultation')
      .replace(/\/book\?entry=activation\b/gi, 'Book Activation Strategy Call')
      .replace(/\bbookable sessions?\b/gi, 'scheduled consultation sessions')
      .replace(/\bbookable\b/gi, 'scheduled');
  }

  function escHtml (s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  /* Context-aware micro-label via IntersectionObserver */
  var ctxMap = {
    'hero':    'Instant AI insights',
    'proof':   'See how AI ranks your site',
    'offer':   'Get guidance instantly',
    'contact': 'We\'re here to help'
  };
  if (microEl && 'IntersectionObserver' in window) {
    Object.keys(ctxMap).forEach(function (id) {
      var el = document.getElementById(id);
      if (!el) return;
      new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting && !open) microEl.textContent = ctxMap[id];
        });
      }, { threshold: 0.25 }).observe(el);
    });
  }

  /* Intelligence moment — single glow pulse on every page load */
  setTimeout(function () {
    trigger.classList.add('is-glow');
    setTimeout(function () { trigger.classList.remove('is-glow'); }, 1800);
  }, 1500);
})();
</script>
