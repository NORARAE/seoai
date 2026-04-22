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
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Begin Your Analysis | SEOAIco</title>
<meta name="robots" content="noindex,nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
:root {
  --bg: #080808;
  --bg-deep: #050504;
  --ivory: #ede8de;
  --muted: #a8a8a0;
  --muted-dim: rgba(168,168,160,.55);
  --gold: #c8a84b;
  --gold-lt: #e2c97d;
  --gold-dim: #9a7a30;
  --border: rgba(200,168,75,.10);
  --border-strong: rgba(200,168,75,.22);
  --input-bg: #111008;
  --input-border: rgba(200,168,75,.18);
  --error: #e05555;
  --shadow-glow: 0 0 32px rgba(200,168,75,.08);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
[x-cloak] { display: none !important; }
html { font-size: 16px; }
body {
  background: var(--bg);
  background-image:
    radial-gradient(ellipse 80% 50% at 50% -10%, rgba(200,168,75,.06), transparent 60%),
    radial-gradient(ellipse 60% 40% at 50% 110%, rgba(200,168,75,.04), transparent 60%);
  color: var(--ivory);
  font-family: 'DM Sans', sans-serif;
  font-weight: 300;
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
  text-rendering: optimizeLegibility;
}

.ob-app { min-height: 100vh; display: flex; flex-direction: column; }

/* Topbar */
.ob-topbar {
  position: sticky; top: 0; z-index: 50;
  background: rgba(8,8,8,.82);
  backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
  border-bottom: 1px solid var(--border);
}
.ob-topbar-inner { max-width: 980px; margin: 0 auto; padding: 18px 24px 14px; display: flex; flex-direction: column; gap: 12px; }
.ob-topbar-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.ob-brand { display: inline-flex; align-items: baseline; text-decoration: none; line-height: 1; }
.ob-brand-seo { font-family: 'DM Sans', sans-serif; font-weight: 300; font-size: 1rem; letter-spacing: .06em; color: var(--ivory); }
.ob-brand-ai  { font-family: 'Cormorant Garamond', serif; font-weight: 600; font-size: 1.2rem; color: var(--gold); }
.ob-brand-co  { font-family: 'DM Sans', sans-serif; font-weight: 300; font-size: .85rem; color: rgba(150,150,150,.5); letter-spacing: .04em; }
.ob-stepcount { font-size: .68rem; letter-spacing: .22em; text-transform: uppercase; color: var(--gold-dim); font-variant-numeric: tabular-nums; }
.ob-stepcount strong { color: var(--gold); font-weight: 500; }

.ob-progress-track { position: relative; height: 2px; background: rgba(200,168,75,.10); border-radius: 2px; overflow: hidden; }
.ob-progress-fill { position: absolute; inset: 0 auto 0 0; background: linear-gradient(90deg, var(--gold-dim), var(--gold), var(--gold-lt)); border-radius: 2px; transition: width .55s cubic-bezier(.22,.61,.36,1); box-shadow: 0 0 12px rgba(200,168,75,.5); }
.ob-step-markers { display: flex; align-items: center; gap: 6px; justify-content: center; }
.ob-step-marker { flex: 1; height: 2px; background: rgba(200,168,75,.10); border-radius: 2px; transition: background .35s ease; }
.ob-step-marker.is-done   { background: rgba(200,168,75,.55); }
.ob-step-marker.is-active { background: var(--gold); box-shadow: 0 0 8px rgba(200,168,75,.45); }

/* Stage */
.ob-stage { flex: 1; display: flex; align-items: flex-start; justify-content: center; padding: 56px 24px 40px; }
.ob-stage-inner { width: 100%; max-width: 720px; }
.ob-step { animation: obStepIn .55s cubic-bezier(.22,.61,.36,1) both; }
@keyframes obStepIn { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
.ob-step[hidden] { display: none !important; }

.ob-step-eye { font-size: .66rem; letter-spacing: .26em; text-transform: uppercase; color: var(--gold); display: inline-flex; align-items: center; gap: 10px; margin-bottom: 18px; }
.ob-step-eye::before { content: ''; display: inline-block; width: 22px; height: 1px; background: var(--gold); }
.ob-step-title { font-family: 'Cormorant Garamond', serif; font-size: clamp(2.2rem, 4.6vw, 3.05rem); font-weight: 300; line-height: 1.06; letter-spacing: -0.005em; color: var(--ivory); margin-bottom: 16px; }
.ob-step-title em { font-style: italic; color: var(--gold-lt); }
.ob-step-lede { font-size: 1.04rem; color: var(--muted); line-height: 1.7; max-width: 560px; margin-bottom: 36px; }

.ob-booking-badge { display: inline-flex; flex-direction: column; gap: 4px; margin-bottom: 28px; padding: 14px 20px; border: 1px solid var(--border-strong); border-radius: 10px; font-size: .82rem; color: var(--muted); background: rgba(200,168,75,.025); }
.ob-booking-badge strong { color: var(--ivory); font-weight: 400; }

/* Step 1 — preview cards */
.ob-preview-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-bottom: 28px; }
@media (max-width: 580px) { .ob-preview-grid { grid-template-columns: 1fr; } }
.ob-preview-card {
  position: relative; padding: 22px 22px 20px;
  border: 1px solid var(--border-strong); border-radius: 12px;
  background: linear-gradient(180deg, rgba(200,168,75,.045), rgba(200,168,75,.012));
  overflow: hidden;
  transition: transform .35s cubic-bezier(.22,.61,.36,1), border-color .35s, box-shadow .35s;
  opacity: 0; transform: translateY(10px);
  animation: obCardIn .6s cubic-bezier(.22,.61,.36,1) both;
}
@keyframes obCardIn { to { opacity: 1; transform: translateY(0); } }
.ob-preview-card:nth-child(1) { animation-delay: .05s; }
.ob-preview-card:nth-child(2) { animation-delay: .12s; }
.ob-preview-card:nth-child(3) { animation-delay: .19s; }
.ob-preview-card:nth-child(4) { animation-delay: .26s; }
.ob-preview-card:nth-child(5) { animation-delay: .33s; }
.ob-preview-card:nth-child(6) { animation-delay: .40s; }
.ob-preview-card:hover { border-color: rgba(200,168,75,.4); box-shadow: var(--shadow-glow); transform: translateY(-2px); }
.ob-preview-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(200,168,75,.5), transparent); opacity: .6; }
.ob-preview-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.ob-preview-tag { font-size: .58rem; letter-spacing: .22em; text-transform: uppercase; color: var(--gold-dim); font-variant-numeric: tabular-nums; }
.ob-preview-pulse { width: 7px; height: 7px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 0 0 rgba(200,168,75,.6); animation: obPulse 2.4s ease-in-out infinite; }
@keyframes obPulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(200,168,75,.6); opacity: 1; } 50% { box-shadow: 0 0 0 6px rgba(200,168,75,0); opacity: .55; } }
.ob-preview-name { font-family: 'Cormorant Garamond', serif; font-size: 1.18rem; color: var(--ivory); font-weight: 400; line-height: 1.25; margin-bottom: 6px; }
.ob-preview-desc { font-size: .82rem; color: var(--muted-dim); line-height: 1.55; }
.ob-preview-foot { font-size: .82rem; color: var(--muted-dim); font-style: italic; line-height: 1.65; margin-bottom: 36px; text-align: center; }

/* Inputs (Step 2) */
.ob-input-card { padding: 28px 28px 24px; border: 1px solid var(--border-strong); border-radius: 14px; background: linear-gradient(180deg, rgba(200,168,75,.018), rgba(200,168,75,.005)); margin-bottom: 16px; }
.ob-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 540px) { .ob-row { grid-template-columns: 1fr; } }
.ob-field { margin-bottom: 18px; }
.ob-field:last-child { margin-bottom: 0; }
.ob-label { display: block; font-size: .68rem; letter-spacing: .16em; text-transform: uppercase; color: var(--gold-dim); margin-bottom: 10px; }
.ob-label .req { color: var(--gold); margin-left: 2px; }
.ob-input { display: block; width: 100%; background: var(--input-bg); border: 1px solid var(--input-border); border-radius: 10px; color: var(--ivory); font-family: 'DM Sans', sans-serif; font-size: 1rem; font-weight: 300; padding: 16px 20px; min-height: 56px; outline: none; transition: border-color .25s, box-shadow .25s, background .25s; }
.ob-input::placeholder { color: rgba(168,168,160,.4); }
.ob-input:hover { border-color: rgba(200,168,75,.32); }
.ob-input:focus { border-color: rgba(200,168,75,.55); background: #131009; box-shadow: 0 0 0 3px rgba(200,168,75,.10); }
.ob-help { font-size: .76rem; color: var(--muted-dim); margin-top: 8px; line-height: 1.55; }
.ob-error { color: var(--error); font-size: .8rem; margin-top: 6px; display: block; }

/* Decision cards (Steps 3,4) */
.ob-cards { display: flex; flex-direction: column; gap: 12px; }
.ob-cards-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 600px) { .ob-cards-2 { grid-template-columns: 1fr; } }
.ob-decision-input { position: absolute; opacity: 0; pointer-events: none; }
.ob-decision-card { position: relative; display: flex; flex-direction: column; gap: 8px; padding: 22px 24px; border: 1px solid var(--input-border); border-radius: 14px; cursor: pointer; transition: transform .25s cubic-bezier(.22,.61,.36,1), border-color .25s, background .25s, box-shadow .25s; background: linear-gradient(180deg, rgba(200,168,75,.012), rgba(200,168,75,.004)); user-select: none; min-height: 88px; }
.ob-decision-card:hover { border-color: rgba(200,168,75,.42); background: rgba(200,168,75,.04); transform: translateY(-1px); }
.ob-decision-input:checked + .ob-decision-card { border-color: var(--gold); background: linear-gradient(180deg, rgba(200,168,75,.10), rgba(200,168,75,.03)); box-shadow: var(--shadow-glow), 0 0 0 1px rgba(200,168,75,.35) inset; transform: translateY(-1px); }
.ob-decision-input:focus-visible + .ob-decision-card { outline: 2px solid var(--gold); outline-offset: 3px; }
.ob-decision-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.ob-decision-name { font-family: 'Cormorant Garamond', serif; font-size: 1.32rem; color: var(--ivory); font-weight: 400; line-height: 1.2; }
.ob-decision-mark { width: 22px; height: 22px; border-radius: 50%; border: 1px solid rgba(200,168,75,.32); position: relative; flex-shrink: 0; transition: all .25s; }
.ob-decision-input:checked + .ob-decision-card .ob-decision-mark { background: var(--gold); border-color: var(--gold); box-shadow: 0 0 12px rgba(200,168,75,.5); }
.ob-decision-input:checked + .ob-decision-card .ob-decision-mark::after { content: '\2713'; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -52%); color: #080808; font-size: .82rem; font-weight: 700; }
.ob-decision-desc { font-size: .88rem; color: var(--muted); line-height: 1.55; }

/* Add-on (Step 5) */
.ob-addon-input { position: absolute; opacity: 0; pointer-events: none; }
.ob-addon-card { display: flex; flex-direction: column; gap: 12px; padding: 28px 28px 26px; border: 1px solid var(--input-border); border-radius: 14px; cursor: pointer; transition: transform .25s, border-color .25s, background .25s, box-shadow .25s; background: linear-gradient(180deg, rgba(200,168,75,.012), rgba(200,168,75,.004)); }
.ob-addon-card:hover { border-color: rgba(200,168,75,.42); background: rgba(200,168,75,.04); transform: translateY(-1px); }
.ob-addon-input:checked + .ob-addon-card { border-color: var(--gold); background: linear-gradient(180deg, rgba(200,168,75,.10), rgba(200,168,75,.03)); box-shadow: var(--shadow-glow); }
.ob-addon-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; }
.ob-addon-name { font-family: 'Cormorant Garamond', serif; font-size: 1.45rem; color: var(--ivory); font-weight: 400; line-height: 1.2; }
.ob-addon-price { font-size: .8rem; letter-spacing: .14em; text-transform: uppercase; color: var(--gold); font-weight: 500; white-space: nowrap; margin-top: 4px; }
.ob-addon-desc { font-size: .92rem; color: var(--muted); line-height: 1.6; }
.ob-addon-check { width: 22px; height: 22px; border: 1px solid rgba(200,168,75,.32); border-radius: 5px; position: relative; flex-shrink: 0; margin-top: 4px; transition: all .25s; }
.ob-addon-input:checked + .ob-addon-card .ob-addon-check { background: var(--gold); border-color: var(--gold); box-shadow: 0 0 12px rgba(200,168,75,.5); }
.ob-addon-input:checked + .ob-addon-card .ob-addon-check::after { content: '\2713'; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -52%); color: #080808; font-size: .92rem; font-weight: 700; }
.ob-addon-skip { display: block; margin-top: 14px; text-align: center; font-size: .82rem; color: var(--muted-dim); font-style: italic; }

/* Step nav */
.ob-stepnav { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--border); }
.ob-stepnav-right { margin-left: auto; }
.ob-btn { display: inline-flex; align-items: center; justify-content: center; gap: 10px; font-family: 'DM Sans', sans-serif; font-weight: 500; letter-spacing: .14em; text-transform: uppercase; cursor: pointer; border: none; border-radius: 12px; transition: background .25s, color .25s, transform .2s, box-shadow .25s, border-color .25s; }
.ob-btn-ghost { background: transparent; color: var(--muted); font-size: .76rem; padding: 14px 22px; border: 1px solid transparent; }
.ob-btn-ghost:hover { color: var(--ivory); border-color: var(--border-strong); }
.ob-btn-primary { background: var(--gold); color: #080808; font-size: .82rem; padding: 17px 34px; min-height: 54px; box-shadow: 0 0 0 0 rgba(200,168,75,0); }
.ob-btn-primary:hover { background: var(--gold-lt); transform: translateY(-1px); box-shadow: 0 6px 24px rgba(200,168,75,.32); }
.ob-btn-primary:disabled { opacity: .4; cursor: not-allowed; transform: none; box-shadow: none; background: var(--gold-dim); }
.ob-btn-primary .ob-arrow { display: inline-block; transition: transform .25s; }
.ob-btn-primary:hover:not(:disabled) .ob-arrow { transform: translateX(4px); }

/* Final CTA */
.ob-cta-reinforce { text-align: center; font-size: .82rem; color: var(--gold-dim); letter-spacing: .04em; margin-bottom: 18px; font-style: italic; }
.ob-cta-block { display: flex; flex-direction: column; align-items: center; text-align: center; padding: 32px 24px 28px; border: 1px solid var(--border-strong); border-radius: 14px; background: linear-gradient(180deg, rgba(200,168,75,.045), rgba(200,168,75,.012)); }
.ob-cta-block .ob-btn-primary { width: 100%; max-width: 380px; margin-top: 4px; }
.ob-fine { font-size: .76rem; color: var(--muted-dim); margin-top: 18px; line-height: 1.65; max-width: 440px; text-align: center; }

/* Recap */
.ob-recap { width: 100%; margin-bottom: 24px; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
.ob-recap-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 20px; font-size: .88rem; border-bottom: 1px solid var(--border); text-align: left; }
.ob-recap-row:last-child { border-bottom: none; }
.ob-recap-key { color: var(--muted-dim); font-size: .7rem; letter-spacing: .14em; text-transform: uppercase; }
.ob-recap-val { color: var(--ivory); font-weight: 400; text-align: right; }
.ob-recap-val.is-empty { color: var(--muted-dim); font-style: italic; }

/* Errors */
.ob-alert-error { background: rgba(224,85,85,.08); border: 1px solid rgba(224,85,85,.22); border-radius: 10px; padding: 16px 20px; font-size: .88rem; color: #e88; margin-bottom: 28px; }
.ob-alert-error ul { margin-top: 8px; padding-left: 18px; line-height: 1.8; }

/* Transition overlay */
.ob-transition { position: fixed; inset: 0; z-index: 200; background: rgba(8,8,8,.95); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 40px 24px; opacity: 0; pointer-events: none; transition: opacity .4s ease; }
.ob-transition.is-on { opacity: 1; pointer-events: auto; }
.ob-transition-mark { width: 76px; height: 76px; border-radius: 50%; border: 1px solid rgba(200,168,75,.3); position: relative; margin-bottom: 28px; }
.ob-transition-mark::before, .ob-transition-mark::after { content: ''; position: absolute; inset: 0; border-radius: 50%; border: 1px solid var(--gold); border-right-color: transparent; border-bottom-color: transparent; animation: obSpin 1.4s linear infinite; }
.ob-transition-mark::after { inset: 8px; border-color: var(--gold-lt); border-right-color: transparent; border-top-color: transparent; animation-duration: 1.9s; animation-direction: reverse; }
@keyframes obSpin { to { transform: rotate(360deg); } }
.ob-transition-eye { font-size: .68rem; letter-spacing: .26em; text-transform: uppercase; color: var(--gold); margin-bottom: 14px; }
.ob-transition-msg { font-family: 'Cormorant Garamond', serif; font-size: clamp(1.6rem, 3.4vw, 2.1rem); font-weight: 300; color: var(--ivory); line-height: 1.25; max-width: 520px; min-height: 1.25em; }
.ob-transition-msg em { font-style: italic; color: var(--gold-lt); }

@media (prefers-reduced-motion: reduce) {
  .ob-step, .ob-preview-card, .ob-transition-mark::before, .ob-transition-mark::after { animation: none !important; transition: none !important; }
}
</style>
</head>
<body>

<div
  class="ob-app"
  x-data="obWizard({
    initial: {{ $errors->any() ? '2' : '1' }},
    hasBooking: {{ !empty($booking) ? 'true' : 'false' }},
    needsEmail: {{ empty($booking) ? 'true' : 'false' }},
    oldFocus: @js(old('focus_area')),
    oldPace:  @js(old('growth_intent')),
    oldAddons: @js(old('add_ons', [])),
  })"
  x-cloak
>

  <header class="ob-topbar">
    <div class="ob-topbar-inner">
      <div class="ob-topbar-row">
        <a href="/" class="ob-brand" aria-label="SEOAIco home">
          <span class="ob-brand-seo">SEO</span><span class="ob-brand-ai">AI</span><span class="ob-brand-co">co</span>
        </a>
        <div class="ob-stepcount" aria-live="polite">
          Step <strong x-text="step"></strong> of <strong x-text="totalSteps"></strong>
        </div>
      </div>
      <div class="ob-progress-track" role="progressbar" :aria-valuenow="step" aria-valuemin="1" :aria-valuemax="totalSteps">
        <div class="ob-progress-fill" :style="{ width: ((step / totalSteps) * 100) + '%' }"></div>
      </div>
      <div class="ob-step-markers" aria-hidden="true">
        <template x-for="i in totalSteps" :key="i">
          <span class="ob-step-marker" :class="{ 'is-done': i < step, 'is-active': i === step }"></span>
        </template>
      </div>
    </div>
  </header>

  <main class="ob-stage">
    <div class="ob-stage-inner">

      @if($errors->any())
      <div class="ob-alert-error">
        <strong>Please correct the following:</strong>
        <ul>
          @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <form method="POST" action="{{ route('onboarding.submit') }}" novalidate id="ob-form" @submit="onSubmit($event)">
        @csrf
        <input type="text" name="website_confirm" value="" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;height:0;width:0;opacity:0" aria-hidden="true">
        <input type="hidden" name="booking_id" value="{{ $booking->id ?? '' }}">
        <input type="hidden" name="scan_id" value="{{ $scanId ?? '' }}">
        <input type="hidden" name="plan" value="{{ $plan ?? '' }}">

        {{-- STEP 1 — SYSTEM PREVIEW --}}
        <section class="ob-step" :hidden="step !== 1" x-show="step === 1" x-transition.opacity.duration.500ms>
          <span class="ob-step-eye">{{ ($isPreview ?? false) ? 'Begin Analysis' : 'Session Confirmed' }}</span>
          <h1 class="ob-step-title">You've secured<br><em>your session.</em></h1>
          <p class="ob-step-lede">We'll use real signals from your market — not assumptions. Here is what your system is about to generate.</p>

          @if(!empty($booking))
          <div class="ob-booking-badge">
            <span>Session: <strong>{{ $booking->consultType->name }}</strong></span>
            <span>{{ $booking->preferred_date->format('F j, Y') }} at {{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }} PT</span>
          </div>
          @endif

          @php
            $previewItems = [
              ['tag' => '01 / Score',     'name' => 'AI Visibility Score',     'desc' => 'Your 0–100 readiness for AI-driven discovery.'],
              ['tag' => '02 / Coverage',  'name' => 'Market Coverage',         'desc' => 'How fully your services and territory are represented.'],
              ['tag' => '03 / Gaps',      'name' => 'Service + City Gaps',     'desc' => 'Where coverage breaks across the markets you serve.'],
              ['tag' => '04 / Structure', 'name' => 'Structural SEO Signals',  'desc' => 'Foundation issues that limit how your site is parsed.'],
              ['tag' => '05 / Citations', 'name' => 'AI Citation Readiness',   'desc' => 'How clearly AI engines can extract and cite your business.'],
              ['tag' => '06 / Direction', 'name' => 'Recommended Next Step',   'desc' => 'A single best action, prioritized by impact.'],
            ];
          @endphp
          <div class="ob-preview-grid">
            @foreach($previewItems as $item)
            <article class="ob-preview-card">
              <div class="ob-preview-head">
                <span class="ob-preview-tag">{{ $item['tag'] }}</span>
                <span class="ob-preview-pulse" aria-hidden="true"></span>
              </div>
              <h3 class="ob-preview-name">{{ $item['name'] }}</h3>
              <p class="ob-preview-desc">{{ $item['desc'] }}</p>
            </article>
            @endforeach
          </div>
          <p class="ob-preview-foot">Every item above is generated from real signals — not generic templates.</p>

          <div class="ob-stepnav">
            <span></span>
            <button type="button" class="ob-btn ob-btn-primary ob-stepnav-right" @click="next()">
              Begin Setup <span class="ob-arrow">→</span>
            </button>
          </div>
        </section>

        {{-- STEP 2 — BUSINESS BASICS --}}
        <section class="ob-step" :hidden="step !== 2" x-show="step === 2" x-transition.opacity.duration.500ms>
          <span class="ob-step-eye">Business Basics</span>
          <h2 class="ob-step-title">A few details to <em>scope your analysis.</em></h2>
          <p class="ob-step-lede">Used to bind your account and frame the right markets, services, and signals.</p>

          <div class="ob-input-card">
            <div class="ob-row">
              <div class="ob-field">
                <label class="ob-label" for="business_name">Business Name <span class="req">*</span></label>
                <input class="ob-input" type="text" id="business_name" name="business_name"
                       value="{{ old('business_name', $booking?->company) }}" maxlength="255" autocomplete="organization" required
                       x-model="form.business_name">
                <p class="ob-help">As it should appear in your dashboard and report.</p>
                @error('business_name')<span class="ob-error">{{ $message }}</span>@enderror
              </div>
              <div class="ob-field">
                <label class="ob-label" for="primary_contact">Your Name <span class="req">*</span></label>
                <input class="ob-input" type="text" id="primary_contact" name="primary_contact"
                       value="{{ old('primary_contact', $booking?->name) }}" maxlength="255" autocomplete="name" required
                       x-model="form.primary_contact">
                <p class="ob-help">Who we should address in your dashboard.</p>
                @error('primary_contact')<span class="ob-error">{{ $message }}</span>@enderror
              </div>
            </div>

            @if($booking === null)
            <div class="ob-field">
              <label class="ob-label" for="email">Email <span class="req">*</span></label>
              <input class="ob-input" type="email" id="email" name="email"
                     value="{{ old('email') }}" maxlength="255" autocomplete="email" required
                     x-model="form.email">
              <p class="ob-help">Where your access link and report will be sent.</p>
              @error('email')<span class="ob-error">{{ $message }}</span>@enderror
            </div>
            @endif
          </div>

          <div class="ob-input-card">
            <div class="ob-field">
              <label class="ob-label" for="website">Website</label>
              <input class="ob-input" type="text" id="website" name="website"
                     value="{{ old('website', $booking?->website) }}" maxlength="500" placeholder="yoursite.com" autocomplete="url"
                     x-model="form.website">
              <p class="ob-help">We'll parse it as the primary surface to analyze.</p>
              @error('website')<span class="ob-error">{{ $message }}</span>@enderror
            </div>

            <div class="ob-field">
              <label class="ob-label" for="service_area">Service Area</label>
              <input class="ob-input" type="text" id="service_area" name="service_area"
                     value="{{ old('service_area') }}" maxlength="1000" placeholder="Cities, counties, or states you serve"
                     x-model="form.service_area">
              <p class="ob-help">Defines the markets we measure your coverage against.</p>
              @error('service_area')<span class="ob-error">{{ $message }}</span>@enderror
            </div>
          </div>

          <div class="ob-stepnav">
            <button type="button" class="ob-btn ob-btn-ghost" @click="prev()">← Back</button>
            <button type="button" class="ob-btn ob-btn-primary ob-stepnav-right" @click="next()" :disabled="!step2Ready">
              Continue <span class="ob-arrow">→</span>
            </button>
          </div>
        </section>

        {{-- STEP 3 — PRIORITY (focus_area) --}}
        <section class="ob-step" :hidden="step !== 3" x-show="step === 3" x-transition.opacity.duration.500ms>
          <span class="ob-step-eye">Priority Selection</span>
          <h2 class="ob-step-title">What should your system <em>focus on first?</em></h2>
          <p class="ob-step-lede">This shapes how your dashboard is ordered and how your report frames the next move.</p>

          @php
            $focusOptions = [
              ['value' => 'improve_visibility', 'name' => 'Improve visibility',      'desc' => 'Strengthen your AI Visibility Score and structural foundation.'],
              ['value' => 'expand_markets',     'name' => 'Expand into new markets', 'desc' => 'Map service and city gaps before scaling coverage.'],
              ['value' => 'generate_leads',     'name' => 'Generate more leads',     'desc' => 'Tighten the path from discoverability to lead capture.'],
              ['value' => 'not_sure',           'name' => 'Help me decide',          'desc' => 'See a balanced overview — we’ll recommend the next step.'],
            ];
          @endphp
          <div class="ob-cards-2" role="radiogroup" aria-label="Improvement priority">
            @foreach($focusOptions as $opt)
            <input type="radio" class="ob-decision-input" id="fa_{{ $opt['value'] }}" name="focus_area" value="{{ $opt['value'] }}"
                   x-model="form.focus_area">
            <label class="ob-decision-card" for="fa_{{ $opt['value'] }}">
              <div class="ob-decision-head">
                <span class="ob-decision-name">{{ $opt['name'] }}</span>
                <span class="ob-decision-mark" aria-hidden="true"></span>
              </div>
              <p class="ob-decision-desc">{{ $opt['desc'] }}</p>
            </label>
            @endforeach
          </div>
          @error('focus_area')<span class="ob-error" style="margin-top:14px">{{ $message }}</span>@enderror

          <div class="ob-stepnav">
            <button type="button" class="ob-btn ob-btn-ghost" @click="prev()">← Back</button>
            <button type="button" class="ob-btn ob-btn-primary ob-stepnav-right" @click="next()" :disabled="!form.focus_area">
              Continue <span class="ob-arrow">→</span>
            </button>
          </div>
        </section>

        {{-- STEP 4 — SPEED (growth_intent) --}}
        <section class="ob-step" :hidden="step !== 4" x-show="step === 4" x-transition.opacity.duration.500ms>
          <span class="ob-step-eye">Pace</span>
          <h2 class="ob-step-title">How fast do you want to <em>move?</em></h2>
          <p class="ob-step-lede">Sets expectations for rollout sequencing and how aggressively we surface actions.</p>

          @php
            $paceOptions = [
              ['value' => 'steady',     'name' => 'Standard',       'desc' => 'A steady, structured rollout.'],
              ['value' => 'aggressive', 'name' => 'Accelerated',    'desc' => 'Faster progress, prioritized actions.'],
              ['value' => 'unsure',     'name' => 'Full expansion', 'desc' => 'Maximum velocity across visibility and markets.'],
            ];
          @endphp
          <div class="ob-cards" role="radiogroup" aria-label="Pace">
            @foreach($paceOptions as $opt)
            <input type="radio" class="ob-decision-input" id="gi_{{ $opt['value'] }}" name="growth_intent" value="{{ $opt['value'] }}"
                   x-model="form.growth_intent">
            <label class="ob-decision-card" for="gi_{{ $opt['value'] }}">
              <div class="ob-decision-head">
                <span class="ob-decision-name">{{ $opt['name'] }}</span>
                <span class="ob-decision-mark" aria-hidden="true"></span>
              </div>
              <p class="ob-decision-desc">{{ $opt['desc'] }}</p>
            </label>
            @endforeach
          </div>
          @error('growth_intent')<span class="ob-error" style="margin-top:14px">{{ $message }}</span>@enderror

          <div class="ob-stepnav">
            <button type="button" class="ob-btn ob-btn-ghost" @click="prev()">← Back</button>
            <button type="button" class="ob-btn ob-btn-primary ob-stepnav-right" @click="next()" :disabled="!form.growth_intent">
              Continue <span class="ob-arrow">→</span>
            </button>
          </div>
        </section>

        {{-- STEP 5 — OPTIONAL ADD-ON --}}
        <section class="ob-step" :hidden="step !== 5" x-show="step === 5" x-transition.opacity.duration.500ms>
          <span class="ob-step-eye">Optional</span>
          <h2 class="ob-step-title">One add-on, <em>if it helps.</em></h2>
          <p class="ob-step-lede">Skip if you'd rather start with the standard analysis.</p>

          <input type="checkbox" class="ob-addon-input" id="addon_growth_review" name="add_ons[]" value="website_growth_review"
                 x-model="form.addon_growth_review">
          <label class="ob-addon-card" for="addon_growth_review">
            <div class="ob-addon-head">
              <div>
                <h3 class="ob-addon-name">Website + Growth System Review</h3>
                <p class="ob-addon-price">$150 one-time</p>
              </div>
              <span class="ob-addon-check" aria-hidden="true"></span>
            </div>
            <p class="ob-addon-desc">We review your site before your session so we can move faster with real insights — and arrive with concrete recommendations instead of generic ones.</p>
          </label>
          <p class="ob-addon-skip">Optional — feel free to skip and continue.</p>

          <div class="ob-stepnav">
            <button type="button" class="ob-btn ob-btn-ghost" @click="prev()">← Back</button>
            <button type="button" class="ob-btn ob-btn-primary ob-stepnav-right" @click="next()">
              Review &amp; Complete <span class="ob-arrow">→</span>
            </button>
          </div>
        </section>

        {{-- STEP 6 — REVIEW + SUBMIT --}}
        <section class="ob-step" :hidden="step !== 6" x-show="step === 6" x-transition.opacity.duration.500ms>
          <span class="ob-step-eye">Final Step</span>
          <h2 class="ob-step-title">Ready to <em>initialize.</em></h2>
          <p class="ob-step-lede">Confirm your inputs below and we'll begin scoping your analysis.</p>

          <div class="ob-recap" aria-label="Summary of your inputs">
            <div class="ob-recap-row">
              <span class="ob-recap-key">Business</span>
              <span class="ob-recap-val" :class="{ 'is-empty': !form.business_name }" x-text="form.business_name || '—'"></span>
            </div>
            <div class="ob-recap-row">
              <span class="ob-recap-key">Contact</span>
              <span class="ob-recap-val" :class="{ 'is-empty': !form.primary_contact }" x-text="form.primary_contact || '—'"></span>
            </div>
            <template x-if="needsEmail">
              <div class="ob-recap-row">
                <span class="ob-recap-key">Email</span>
                <span class="ob-recap-val" :class="{ 'is-empty': !form.email }" x-text="form.email || '—'"></span>
              </div>
            </template>
            <div class="ob-recap-row">
              <span class="ob-recap-key">Website</span>
              <span class="ob-recap-val" :class="{ 'is-empty': !form.website }" x-text="form.website || '—'"></span>
            </div>
            <div class="ob-recap-row">
              <span class="ob-recap-key">Service Area</span>
              <span class="ob-recap-val" :class="{ 'is-empty': !form.service_area }" x-text="form.service_area || '—'"></span>
            </div>
            <div class="ob-recap-row">
              <span class="ob-recap-key">Focus</span>
              <span class="ob-recap-val" :class="{ 'is-empty': !form.focus_area }" x-text="focusLabel || '—'"></span>
            </div>
            <div class="ob-recap-row">
              <span class="ob-recap-key">Pace</span>
              <span class="ob-recap-val" :class="{ 'is-empty': !form.growth_intent }" x-text="paceLabel || '—'"></span>
            </div>
            <div class="ob-recap-row">
              <span class="ob-recap-key">Add-on</span>
              <span class="ob-recap-val" :class="{ 'is-empty': !form.addon_growth_review }"
                    x-text="form.addon_growth_review ? 'Website + Growth System Review ($150)' : 'None'"></span>
            </div>
          </div>

          <p class="ob-cta-reinforce">This sets up your analysis and dashboard.</p>
          <div class="ob-cta-block">
            <button type="submit" class="ob-btn ob-btn-primary" id="submit-btn">
              Initialize My Analysis <span class="ob-arrow">→</span>
            </button>
            <p class="ob-fine">We respond within 1&ndash;2 business days. Your information is used only to set up your analysis.</p>
          </div>

          <div class="ob-stepnav">
            <button type="button" class="ob-btn ob-btn-ghost" @click="prev()">← Back</button>
            <span></span>
          </div>
        </section>

      </form>
    </div>
  </main>

  <div class="ob-transition" :class="{ 'is-on': transitioning }" aria-hidden="true">
    <div class="ob-transition-mark" aria-hidden="true"></div>
    <p class="ob-transition-eye">Activating</p>
    <p class="ob-transition-msg" x-text="transitionMsg">Initializing your market analysis…</p>
  </div>

</div>

<script>
function obWizard(opts) {
  return {
    step: parseInt(opts.initial || 1, 10),
    totalSteps: 6,
    hasBooking: !!opts.hasBooking,
    needsEmail: !!opts.needsEmail,
    transitioning: false,
    transitionMsg: 'Initializing your market analysis…',
    _transitionMsgs: [
      'Initializing your market analysis…',
      'Mapping your service area…',
      'Calibrating visibility signals…',
      'Preparing your system…',
    ],
    _transitionTimer: null,
    form: {
      business_name: '',
      primary_contact: '',
      email: '',
      website: '',
      service_area: '',
      focus_area: opts.oldFocus || '',
      growth_intent: opts.oldPace || '',
      addon_growth_review: Array.isArray(opts.oldAddons) && opts.oldAddons.indexOf('website_growth_review') !== -1,
    },
    init() {
      this.$nextTick(() => {
        ['business_name','primary_contact','email','website','service_area'].forEach(id => {
          const el = document.getElementById(id);
          if (el && typeof this.form[id] !== 'undefined') this.form[id] = el.value || '';
        });
      });
    },
    get step2Ready() {
      const ok = (this.form.business_name || '').trim().length > 0
              && (this.form.primary_contact || '').trim().length > 0;
      if (this.needsEmail) {
        return ok && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test((this.form.email || '').trim());
      }
      return ok;
    },
    get focusLabel() {
      return ({
        improve_visibility: 'Improve visibility',
        expand_markets:     'Expand into new markets',
        generate_leads:     'Generate more leads',
        not_sure:           'Help me decide',
      })[this.form.focus_area] || '';
    },
    get paceLabel() {
      return ({
        steady:     'Standard',
        aggressive: 'Accelerated',
        unsure:     'Full expansion',
      })[this.form.growth_intent] || '';
    },
    next() {
      if (this.step < this.totalSteps) {
        this.step += 1;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    },
    prev() {
      if (this.step > 1) {
        this.step -= 1;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    },
    onSubmit(e) {
      const websiteEl = document.getElementById('website');
      if (websiteEl) {
        const v = (websiteEl.value || '').trim();
        if (v && !/^https?:\/\//i.test(v)) websiteEl.value = 'https://' + v;
      }
      const btn = document.getElementById('submit-btn');
      if (btn) { btn.disabled = true; btn.textContent = 'Submitting…'; }
      if (typeof gtag === 'function') {
        gtag('event', 'onboarding_submitted', {
          page_location: window.location.href,
          booking_id: (new URLSearchParams(window.location.search)).get('booking') || null,
        });
      }
      this.transitioning = true;
      let i = 0;
      this._transitionTimer = setInterval(() => {
        i = (i + 1) % this._transitionMsgs.length;
        this.transitionMsg = this._transitionMsgs[i];
      }, 1400);
    },
  };
}

document.addEventListener('DOMContentLoaded', function() {
  if (typeof gtag === 'function') {
    gtag('event', 'onboarding_start', { page_location: window.location.href });
    @if(!empty($booking) && !$booking->consultType?->is_free)
    gtag('event', 'purchase', {
      transaction_id: '{{ $booking->id }}',
      value: {{ (float) ($booking->consultType?->price ?? 0) }},
      currency: 'USD',
      items: [{ item_name: '{{ addslashes($booking->consultType?->name ?? '') }}', price: {{ (float) ($booking->consultType?->price ?? 0) }}, quantity: 1 }]
    });
    @endif
  }
});
</script>
@include('components.tm-style')
</body>
</html>
