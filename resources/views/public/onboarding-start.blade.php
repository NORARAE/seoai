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
<title>Client Onboarding — SEOAIco</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
:root {
  --bg: #080808;
  --deep: #0a0906;
  --ivory: #ede8de;
  --muted: #a8a8a0;
  --gold: #c8a84b;
  --gold-lt: #e2c97d;
  --gold-dim: #9a7a30;
  --border: rgba(200,168,75,.10);
  --input-bg: #111008;
  --input-border: rgba(200,168,75,.18);
  --error: #e05555;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { font-size: 16px; }
body {
  background: var(--bg);
  color: var(--ivory);
  font-family: 'DM Sans', sans-serif;
  font-weight: 300;
  min-height: 100vh;
  padding: 60px 24px 80px;
}
[x-cloak] { display: none !important; }

/* ── Step transitions ── */
.ob-step-enter { transition: opacity .25s ease, transform .25s ease; }
.ob-step-from { opacity: 0; transform: translateX(12px); }
.ob-step-to { opacity: 1; transform: translateX(0); }

/* ── Layout ── */
.ob-wrap { max-width: 620px; margin: 0 auto; }

/* ── Header ── */
.ob-eye {
  font-size: .64rem;
  letter-spacing: .24em;
  text-transform: uppercase;
  color: var(--gold);
  display: block;
  margin-bottom: 20px;
}
.ob-hed {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 4vw, 2.8rem);
  font-weight: 300;
  line-height: 1.1;
  color: var(--ivory);
  margin-bottom: 12px;
}
.ob-hed em { font-style: italic; color: var(--gold-lt); }
.ob-sub {
  font-size: .92rem;
  color: var(--muted);
  line-height: 1.7;
  margin-bottom: 40px;
  max-width: 480px;
}

/* ── Booking badge ── */
.ob-booking-badge {
  display: inline-flex;
  flex-direction: column;
  gap: 3px;
  padding: 14px 20px;
  border: 1px solid var(--border);
  border-radius: 6px;
  margin-bottom: 40px;
  font-size: .8rem;
  color: var(--muted);
}
.ob-booking-badge strong { color: var(--ivory); font-weight: 400; }

/* ── Progress bar ── */
.ob-progress-wrap { margin-bottom: 40px; }
.ob-progress-counter {
  font-size: .68rem;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: var(--gold-dim);
  margin-bottom: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.ob-progress-counter strong { color: var(--gold); }
.ob-progress-labels {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}
.ob-progress-label {
  font-size: .68rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: #3a3a3a;
  transition: color .3s;
  font-weight: 400;
}
.ob-progress-label.active { color: var(--gold); font-weight: 500; }
.ob-progress-label.done { color: var(--gold-dim); }
.ob-progress-track {
  width: 100%;
  height: 3px;
  background: #1a1a1a;
  border-radius: 3px;
  overflow: hidden;
}
.ob-progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--gold-dim), var(--gold));
  border-radius: 3px;
  transition: width .4s ease;
}

/* ── Step headings ── */
.ob-step-eye {
  font-size: .62rem;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: var(--gold-dim);
  display: block;
  margin-bottom: 10px;
}
.ob-step-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.8rem, 5vw, 2.2rem);
  font-weight: 300;
  color: var(--ivory);
  margin-bottom: 10px;
  line-height: 1.12;
}
.ob-step-hint {
  font-size: .92rem;
  color: var(--muted);
  margin-bottom: 36px;
  line-height: 1.7;
  max-width: 480px;
}

/* ── Section titles ── */
.ob-section {
  font-size: .70rem;
  letter-spacing: .14em;
  font-weight: 500;
  text-transform: uppercase;
  color: var(--gold-dim);
  margin: 40px 0 16px;
  padding-bottom: 10px;
  border-bottom: 1px solid var(--border);
}

/* ── Form fields ── */
.ob-field { margin-bottom: 22px; }
.ob-label {
  display: block;
  font-size: .72rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--gold-dim);
  margin-bottom: 9px;
}
.ob-label .req { color: var(--gold); margin-left: 2px; }
.ob-input,
.ob-textarea,
.ob-select {
  display: block;
  width: 100%;
  background: var(--input-bg);
  border: 1px solid var(--input-border);
  border-radius: 8px;
  color: var(--ivory);
  font-family: 'DM Sans', sans-serif;
  font-size: .95rem;
  font-weight: 300;
  padding: 14px 18px;
  min-height: 50px;
  outline: none;
  transition: border-color .25s, box-shadow .25s;
  appearance: none;
}
.ob-input:focus,
.ob-textarea:focus,
.ob-select:focus {
  border-color: rgba(200,168,75,.5);
  box-shadow: 0 0 0 3px rgba(200,168,75,.08);
}
.ob-textarea { min-height: 100px; resize: vertical; }
.ob-select option { background: #111; }

/* ── Error messages ── */
.ob-error { color: var(--error); font-size: .8rem; margin-top: 6px; display: block; }

/* ── Button selector group (qualifying questions) ── */
.ob-btn-group { display: flex; flex-wrap: wrap; gap: 10px; }
.ob-btn-opt { display: none; }
.ob-btn-label {
  padding: 13px 20px;
  border: 1px solid var(--input-border);
  border-radius: 50px;
  font-size: .86rem;
  cursor: pointer;
  color: var(--muted);
  transition: border-color .2s, color .2s, background .2s, box-shadow .2s;
  user-select: none;
  white-space: nowrap;
  min-height: 46px;
  display: inline-flex;
  align-items: center;
}
.ob-btn-label:hover {
  border-color: rgba(200,168,75,.4);
  color: var(--ivory);
}
.ob-btn-opt:checked + .ob-btn-label {
  border-color: var(--gold);
  color: #080808;
  background: var(--gold);
  font-weight: 500;
  box-shadow: 0 0 12px rgba(200,168,75,.25);
}

/* ── Radio / toggle group ── */
.ob-radio-group { display: flex; gap: 10px; }
.ob-radio-opt { display: none; }
.ob-radio-btn {
  flex: 1;
  text-align: center;
  padding: 13px 14px;
  border: 1px solid var(--input-border);
  border-radius: 8px;
  font-size: .86rem;
  min-height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: var(--muted);
  transition: border-color .2s, color .2s, background .2s, box-shadow .2s;
  user-select: none;
}
.ob-radio-opt:checked + .ob-radio-btn {
  border-color: var(--gold);
  color: var(--gold);
  background: rgba(200,168,75,.07);
  box-shadow: 0 0 10px rgba(200,168,75,.12);
}

/* ── Access method radio (2-col) ── */
.ob-radio-group-3 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 480px) { .ob-radio-group-3 { grid-template-columns: 1fr; } }
.ob-radio-btn-3 {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 16px 12px;
  border: 1px solid var(--input-border);
  border-radius: 10px;
  font-size: .90rem;
  line-height: 1.4;
  min-height: 80px;
  cursor: pointer;
  color: var(--muted);
  transition: border-color .2s, color .2s, background .2s, box-shadow .2s;
  user-select: none;
}
.ob-radio-btn-3 span { display: block; font-size: .76rem; letter-spacing: .04em; margin-top: 4px; color: #777; }
.ob-radio-opt:checked + .ob-radio-btn-3 {
  border-color: var(--gold);
  color: var(--gold);
  background: rgba(200,168,75,.06);
  box-shadow: 0 0 12px rgba(200,168,75,.1);
}
.ob-radio-opt:checked + .ob-radio-btn-3 span { color: rgba(200,168,75,.5); }
.ob-radio-btn-3.ob-recommended {
  border-color: rgba(200,168,75,.22);
  background: rgba(200,168,75,.03);
}

/* ── Platform instruction box ── */
.ob-instruction {
  background: rgba(200,168,75,.04);
  border: 1px solid rgba(200,168,75,.14);
  border-radius: 6px;
  padding: 16px 18px;
  margin-top: 12px;
  font-size: .84rem;
  color: var(--muted);
  line-height: 1.8;
}
.ob-instruction strong { color: var(--ivory); font-weight: 400; }
.ob-instruction ol { margin: 8px 0 0 20px; }
.ob-instruction .ob-invite-email {
  font-size: .82rem;
  color: var(--gold);
  font-style: italic;
  margin-top: 8px;
  display: block;
}
.ob-instruction .ob-setup-link {
  display: inline-block;
  margin-top: 14px;
  font-size: .74rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--gold);
  text-decoration: none;
  border: 1px solid rgba(200,168,75,.22);
  padding: 8px 16px;
  border-radius: 3px;
  transition: border-color .25s, background .25s;
}
.ob-instruction .ob-setup-link:hover { border-color: rgba(200,168,75,.5); background: rgba(200,168,75,.05); }

/* ── Add-on cards ── */
.ob-addons-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 580px) { .ob-addons-grid { grid-template-columns: 1fr; } }
.ob-addon-opt { display: none; }
.ob-addon-card {
  display: flex;
  flex-direction: column;
  gap: 6px;
  padding: 18px 20px;
  border: 1px solid var(--input-border);
  border-radius: 10px;
  cursor: pointer;
  transition: border-color .2s, background .2s, box-shadow .2s;
  min-height: 88px;
}
.ob-addon-card:hover { border-color: rgba(200,168,75,.3); box-shadow: 0 2px 16px rgba(0,0,0,.3); }
.ob-addon-opt:checked + .ob-addon-card {
  border-color: var(--gold);
  background: rgba(200,168,75,.05);
  box-shadow: 0 0 14px rgba(200,168,75,.1);
}
.ob-addon-name { font-size: .88rem; color: var(--ivory); font-weight: 400; }
.ob-addon-price { font-size: .78rem; color: var(--gold); }
.ob-addon-desc { font-size: .78rem; color: #888; line-height: 1.55; }
.ob-addon-check {
  width: 16px; height: 16px;
  border: 1px solid rgba(200,168,75,.25);
  border-radius: 3px;
  margin-left: auto;
  flex-shrink: 0;
  position: relative;
}
.ob-addon-opt:checked + .ob-addon-card .ob-addon-check {
  background: var(--gold);
  border-color: var(--gold);
}
.ob-addon-opt:checked + .ob-addon-card .ob-addon-check::after {
  content: '✓';
  position: absolute;
  top: -1px; left: 2px;
  font-size: .72rem;
  color: #080808;
}
.ob-addon-header { display: flex; align-items: flex-start; justify-content: space-between; }

/* ── Navigation buttons ── */
.ob-nav { display: flex; gap: 14px; align-items: center; margin-top: 36px; flex-wrap: wrap; }
.ob-btn-next {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: var(--gold);
  color: #080808;
  font-family: 'DM Sans', sans-serif;
  font-size: .82rem;
  font-weight: 500;
  letter-spacing: .14em;
  text-transform: uppercase;
  padding: 16px 36px;
  min-height: 52px;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: background .25s, transform .2s, box-shadow .2s;
}
.ob-btn-next:hover {
  background: var(--gold-lt);
  transform: translateY(-1px);
  box-shadow: 0 4px 20px rgba(200,168,75,.25);
}
.ob-btn-back {
  background: none;
  border: none;
  color: var(--muted);
  font-size: .78rem;
  letter-spacing: .1em;
  text-transform: uppercase;
  cursor: pointer;
  padding: 8px 0;
  min-height: 44px;
  transition: color .2s;
}
.ob-btn-back:hover { color: var(--ivory); }
.ob-submit {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: var(--gold);
  color: #080808;
  font-family: 'DM Sans', sans-serif;
  font-size: .82rem;
  font-weight: 500;
  letter-spacing: .14em;
  text-transform: uppercase;
  padding: 16px 36px;
  min-height: 52px;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: background .25s, transform .2s, box-shadow .2s;
}
.ob-submit:hover {
  background: var(--gold-lt);
  transform: translateY(-1px);
  box-shadow: 0 4px 20px rgba(200,168,75,.25);
}
.ob-submit:disabled { opacity: .55; cursor: not-allowed; transform: none; box-shadow: none; }
.ob-fine {
  font-size: .76rem;
  color: rgba(168,168,160,.45);
  margin-top: 14px;
  line-height: 1.65;
}

/* ── Optional step label ── */
.ob-optional-note {
  font-size: .78rem;
  color: #555;
  font-style: italic;
  margin-bottom: 24px;
}

/* ── Alert banner ── */
.ob-alert-error {
  background: rgba(224,85,85,.08);
  border: 1px solid rgba(224,85,85,.2);
  border-radius: 6px;
  padding: 14px 18px;
  font-size: .88rem;
  color: #e88;
  margin-bottom: 28px;
}

/* ── Trust module (Step 3 header) ── */
.ob-trust-module {
  display: flex;
  gap: 14px;
  align-items: flex-start;
  margin-bottom: 36px;
  padding: 18px 20px;
  border: 1px solid rgba(200,168,75,.14);
  border-radius: 10px;
  background: rgba(200,168,75,.025);
}
.ob-trust-module-icon {
  flex-shrink: 0;
  color: var(--gold);
  opacity: .75;
  margin-top: 2px;
}
.ob-trust-module-main {
  font-size: .94rem;
  color: var(--ivory);
  line-height: 1.65;
  margin-bottom: 6px;
}
.ob-trust-module-sub {
  font-size: .82rem;
  color: var(--muted);
  line-height: 1.65;
}

/* ── Access hint (GA4 / GSC conditional) ── */
.ob-access-hint {
  margin-top: 10px;
  padding: 12px 16px;
  background: rgba(200,168,75,.03);
  border: 1px solid rgba(200,168,75,.12);
  border-radius: 6px;
  font-size: .82rem;
  color: var(--muted);
  line-height: 1.7;
}
.ob-access-hint strong { color: var(--ivory); font-weight: 400; }

/* ── Platform instruction role note ── */
.ob-instruction-role-note {
  font-size: .76rem;
  color: #555;
  margin-top: 10px;
  line-height: 1.6;
  font-style: italic;
}

/* ── Enhancements intro ── */
.ob-enhancements-intro {
  font-size: .84rem;
  color: var(--muted);
  line-height: 1.7;
  margin-bottom: 20px;
}

/* ── Trust bar ── */
.ob-trust {
  display: flex;
  gap: 16px;
  margin: 32px 0 0;
  padding: 16px 20px;
  border: 1px solid var(--border);
  border-radius: 10px;
  font-size: .76rem;
  color: #666;
  line-height: 1.65;
  background: rgba(200,168,75,.02);
}
.ob-trust-icon { font-size: 1.1rem; flex-shrink: 0; }

/* ── Collapsible toggle ── */
.ob-toggle-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: none;
  border: 1px solid var(--input-border);
  border-radius: 8px;
  color: var(--muted);
  font-size: .8rem;
  letter-spacing: .06em;
  cursor: pointer;
  padding: 10px 16px;
  min-height: 40px;
  transition: border-color .2s, color .2s;
  margin-bottom: 16px;
}
.ob-toggle-btn:hover { border-color: rgba(200,168,75,.35); color: var(--ivory); }
.ob-toggle-arrow { font-size: .7rem; transition: transform .25s; display: inline-block; }
.ob-toggle-arrow.open { transform: rotate(180deg); }

/* ── Label with icon ── */
.ob-label-with-icon { display: flex !important; align-items: center; gap: 8px; }
.ob-google-icon { flex-shrink: 0; display: inline-flex; align-items: center; }

/* ── Access trust note ── */
.ob-access-trust-note {
  font-size: .82rem;
  color: rgba(168,168,160,.65);
  line-height: 1.7;
  margin-bottom: 18px;
}

/* ── Trust microcopy (always visible) ── */
.ob-trust-microcopy {
  font-size: .76rem;
  color: rgba(168,168,160,.48);
  margin-top: 10px;
  line-height: 1.65;
  font-style: italic;
}
.ob-learn-link {
  display: inline-block;
  margin-top: 6px;
  font-size: .72rem;
  color: rgba(168,168,160,.36);
  text-decoration: none;
  letter-spacing: .04em;
  transition: color .2s;
}
.ob-learn-link:hover { color: var(--muted); }

/* ── Addon info tooltip (CSS-only) ── */
.ob-info-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 15px; height: 15px;
  border: 1px solid rgba(200,168,75,.2);
  border-radius: 50%;
  cursor: default;
  font-size: .60rem;
  font-style: normal;
  color: rgba(200,168,75,.4);
  flex-shrink: 0;
  line-height: 1;
  transition: border-color .2s, color .2s;
}
.ob-info-btn:hover { border-color: rgba(200,168,75,.45); color: rgba(200,168,75,.7); }
.ob-info-btn:hover::after {
  content: attr(data-tip);
  position: absolute;
  bottom: calc(100% + 7px);
  left: 50%;
  transform: translateX(-50%);
  background: #111;
  border: 1px solid rgba(200,168,75,.18);
  border-radius: 5px;
  padding: 8px 12px;
  font-size: .72rem;
  color: var(--muted);
  white-space: normal;
  width: 200px;
  line-height: 1.55;
  z-index: 20;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 300;
  pointer-events: none;
}

/* ── R&D Credit informational section ── */
.ob-rd-section {
  margin-top: 40px;
  padding: 22px 24px;
  border: 1px solid rgba(200,168,75,.10);
  border-radius: 10px;
  background: rgba(200,168,75,.015);
}
.ob-rd-eye {
  font-size: .64rem;
  letter-spacing: .22em;
  text-transform: uppercase;
  color: var(--gold-dim);
  display: block;
  margin-bottom: 12px;
}
.ob-rd-hed {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.3rem;
  font-weight: 300;
  color: var(--ivory);
  line-height: 1.2;
  margin-bottom: 14px;
}
.ob-rd-body {
  font-size: .83rem;
  color: var(--muted);
  line-height: 1.8;
  margin-bottom: 10px;
}
.ob-rd-links {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin: 18px 0 16px;
}
.ob-rd-link {
  display: inline-flex;
  align-items: center;
  font-size: .72rem;
  letter-spacing: .10em;
  text-transform: uppercase;
  color: var(--gold-dim);
  text-decoration: none;
  border: 1px solid rgba(200,168,75,.18);
  border-radius: 4px;
  padding: 9px 15px;
  transition: border-color .2s, color .2s;
}
.ob-rd-link:hover { border-color: rgba(200,168,75,.4); color: var(--gold); }
.ob-rd-referral {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-top: 16px;
  font-size: .82rem;
  color: rgba(168,168,160,.65);
  line-height: 1.65;
  cursor: pointer;
  user-select: none;
}
.ob-rd-referral input[type="checkbox"] {
  accent-color: var(--gold);
  width: 15px;
  height: 15px;
  flex-shrink: 0;
  margin-top: 2px;
  cursor: pointer;
}
.ob-rd-disclaimer {
  font-size: .74rem;
  color: rgba(168,168,160,.44);
  line-height: 1.75;
  margin-top: 16px;
  padding-top: 14px;
  border-top: 1px solid rgba(200,168,75,.06);
  font-style: italic;
}

/* ── Activation note ── */
.ob-activation-note {
  font-size: .78rem;
  color: rgba(168,168,160,.38);
  line-height: 1.75;
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid rgba(200,168,75,.06);
}
.ob-activation-note em { font-style: normal; color: rgba(168,168,160,.58); }

/* ── Privacy block ── */
.ob-privacy-block {
  margin-top: 18px;
  padding: 14px 18px;
  border: 1px solid rgba(200,168,75,.09);
  border-radius: 8px;
  font-size: .78rem;
  color: rgba(168,168,160,.55);
  line-height: 1.75;
  display: flex;
  gap: 12px;
  align-items: flex-start;
  background: rgba(200,168,75,.015);
}
.ob-privacy-icon { flex-shrink: 0; margin-top: 2px; opacity: .45; color: var(--gold); }
.ob-privacy-link {
  display: inline-block;
  margin-top: 6px;
  font-size: .70rem;
  color: rgba(168,168,160,.32);
  text-decoration: none;
  letter-spacing: .08em;
  text-transform: uppercase;
  transition: color .2s;
}
.ob-privacy-link:hover { color: var(--muted); }

@media (max-width: 600px) {
  body { padding: 36px 18px 64px; }
  .ob-hed { font-size: 1.8rem; }
  .ob-btn-next, .ob-submit { width: 100%; justify-content: center; }
  .ob-nav { flex-direction: column-reverse; align-items: stretch; gap: 10px; }
  .ob-btn-back { text-align: center; min-height: 44px; }
  .ob-btn-group { gap: 8px; }
  .ob-btn-label { flex: 1 1 calc(50% - 4px); justify-content: center; text-align: center; white-space: normal; font-size: .84rem; }
}

@media (max-width: 390px) {
  .ob-btn-label { flex: 1 1 100%; }
  .ob-radio-group { flex-direction: column; }
  .ob-radio-btn { min-height: 52px; font-size: .88rem; }
}

@media (min-width: 768px) {
  .ob-sub { font-size: 1rem; }
  .ob-step-hint { font-size: 1rem; }
  .ob-input, .ob-textarea, .ob-select { font-size: 1rem; }
  .ob-trust-module-main { font-size: .98rem; }
  .ob-trust-module-sub { font-size: .86rem; }
  .ob-addon-name { font-size: .92rem; }
  .ob-addon-desc { font-size: .86rem; color: #999; }
  .ob-enhancements-intro { font-size: .92rem; }
  .ob-access-trust-note { font-size: .92rem; }
  .ob-section { font-size: .74rem; letter-spacing: .16em; }
  .ob-radio-btn-3 { font-size: .96rem; min-height: 88px; }
  .ob-radio-btn-3 span { font-size: .80rem; }
  .ob-activation-note { font-size: .84rem; color: rgba(168,168,160,.52); }
  .ob-activation-note em { color: rgba(168,168,160,.75); }
  .ob-privacy-block { font-size: .82rem; }
  .ob-trust-microcopy { font-size: .78rem; }
}
</style>
@include('partials.clarity')
</head>
<body x-data="onboardingWizard()" x-cloak>
<div class="ob-wrap">

  <a href="/" style="display:inline-flex;align-items:baseline;text-decoration:none;line-height:1;margin-bottom:36px">
    <span style="font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.1rem;letter-spacing:.06em;color:var(--ivory)">SEO</span><span style="font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.3rem;color:var(--gold)">AI</span><span style="font-family:'DM Sans',sans-serif;font-weight:300;font-size:.9rem;color:rgba(150,150,150,.5);letter-spacing:.04em">co</span>
  </a>

  <span class="ob-eye">{{ ($isPreview ?? false) ? 'SEO Opportunity Preview' : 'Client Onboarding' }}</span>
  <h1 class="ob-hed">
    @if($isPreview ?? false)
      Let's map your<br><em>opportunity.</em>
    @else
      Let's prepare<br><em>your strategy session.</em>
    @endif
  </h1>
  <p class="ob-sub">Takes about 2 minutes. This helps us prepare everything before your call.</p>

  {{-- ── Booking badge (only when a booking exists) ── --}}
  @if($booking)
  <div class="ob-booking-badge">
    <span>Session: <strong>{{ $booking->consultType->name }}</strong></span>
    <span>{{ $booking->preferred_date->format('F j, Y') }} at {{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }} PT</span>
  </div>
  @endif

  {{-- ── Progress bar ── --}}
  <div class="ob-progress-wrap">
    <div class="ob-progress-counter">
      <span>Step <strong x-text="step"></strong> of 3</span>
      <span x-text="step === 1 ? 'Profile' : step === 2 ? 'Goals' : 'Setup'"></span>
    </div>
    <div class="ob-progress-labels">
      <span class="ob-progress-label" :class="{ active: step === 1, done: step > 1 }">Profile</span>
      <span class="ob-progress-label" :class="{ active: step === 2, done: step > 2 }">Goals</span>
      <span class="ob-progress-label" :class="{ active: step === 3, done: step > 3 }">Setup</span>
    </div>
    <div class="ob-progress-track">
      <div class="ob-progress-fill" :style="`width:${((step - 1) / 2) * 100}%`"></div>
    </div>
  </div>

  {{-- ── Validation errors ── --}}
  @if($errors->any())
  <div class="ob-alert-error">
    <strong>Please correct the following:</strong>
    <ul style="margin-top:8px;padding-left:18px;line-height:1.8">
      @foreach($errors->all() as $e)
      <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <form method="POST" action="{{ route('onboarding.submit') }}" novalidate id="ob-form">
    @csrf
    <input type="hidden" name="booking_id" value="{{ $booking->id ?? '' }}">

    {{-- ══════════════════════════════════════════════════
         STEP 1 — Basic Profile
    ══════════════════════════════════════════════════ --}}
    <div x-show="step === 1" x-transition:enter="ob-step-enter" x-transition:enter-start="ob-step-from" x-transition:enter-end="ob-step-to">
      <span class="ob-step-eye">Step 1 of 3</span>
      <h2 class="ob-step-title">Tell us about your business.</h2>
      <p class="ob-step-hint">This helps us prepare your strategy session — takes about 60 seconds.</p>

      <div class="ob-field">
        <label class="ob-label" for="business_name">Business Name <span class="req">*</span></label>
        <input class="ob-input" type="text" id="business_name" name="business_name"
               value="{{ old('business_name', $booking?->company) }}" maxlength="255" autocomplete="organization"
               x-ref="businessName">
        @error('business_name')<span class="ob-error">{{ $message }}</span>@enderror
      </div>

      <div class="ob-field">
        <label class="ob-label" for="website">Website</label>
        <input class="ob-input" type="text" id="website" name="website"
               value="{{ old('website', $booking?->website) }}" maxlength="500" placeholder="yoursite.com"
               autocomplete="url" @blur="prefixWebsite($event)">
        @error('website')<span class="ob-error">{{ $message }}</span>@enderror
      </div>

      <div class="ob-field">
        <label class="ob-label" for="service_area">Service Area</label>
        <textarea class="ob-textarea" id="service_area" name="service_area"
                  maxlength="1000" placeholder="Cities, counties, or states you serve…">{{ old('service_area') }}</textarea>
        @error('service_area')<span class="ob-error">{{ $message }}</span>@enderror
      </div>

      <div class="ob-section" style="margin-top:28px">Contact</div>

      <div class="ob-field">
        <label class="ob-label" for="primary_contact">Full Name <span class="req">*</span></label>
        <input class="ob-input" type="text" id="primary_contact" name="primary_contact"
               value="{{ old('primary_contact', $booking?->name) }}" maxlength="255" autocomplete="name">
        @error('primary_contact')<span class="ob-error">{{ $message }}</span>@enderror
      </div>

      @if($booking === null)
      <div class="ob-field">
        <label class="ob-label" for="email">Email <span class="req">*</span></label>
        <input class="ob-input" type="email" id="email" name="email"
               value="{{ old('email') }}" maxlength="255" autocomplete="email">
        @error('email')<span class="ob-error">{{ $message }}</span>@enderror
      </div>
      @endif

      <div class="ob-field">
        <label class="ob-label" for="phone">Phone</label>
        <input class="ob-input" type="tel" id="phone" name="phone"
               value="{{ old('phone', $booking?->phone) }}" maxlength="50" autocomplete="tel">
        @error('phone')<span class="ob-error">{{ $message }}</span>@enderror
      </div>

      <div class="ob-nav">
        <button type="button" class="ob-btn-next" @click="nextStep()">
          Next Step &rarr;
        </button>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STEP 2 — Qualifying
    ══════════════════════════════════════════════════ --}}
    <div x-show="step === 2" x-transition:enter="ob-step-enter" x-transition:enter-start="ob-step-from" x-transition:enter-end="ob-step-to">
      <span class="ob-step-eye">Step 2 of 3 — Almost there</span>
      <h2 class="ob-step-title">What are you working toward?</h2>
      <p class="ob-step-hint">Be as honest as you like. We use this to prepare your strategy — not to judge.</p>

      <div class="ob-field">
        <label class="ob-label" for="goals">What's your primary goal right now?</label>
        <textarea class="ob-textarea" id="goals" name="goals" maxlength="2000"
                  placeholder="e.g. Get more local leads, rank #1 for [keyword], outrank a competitor…">{{ old('goals') }}</textarea>
        @error('goals')<span class="ob-error">{{ $message }}</span>@enderror
      </div>

      <div class="ob-field">
        <label class="ob-label" for="challenges">What's your biggest obstacle right now?</label>
        <textarea class="ob-textarea" id="challenges" name="challenges" maxlength="2000"
                  placeholder="e.g. Low traffic, no conversions, no time to create content…">{{ old('challenges') }}</textarea>
        @error('challenges')<span class="ob-error">{{ $message }}</span>@enderror
      </div>

      <div class="ob-field">
        <label class="ob-label">How quickly do you want to grow?</label>
        <div class="ob-btn-group">
          <input type="radio" class="ob-btn-opt" id="gi_aggressive" name="growth_intent" value="aggressive"
                 {{ old('growth_intent') === 'aggressive' ? 'checked' : '' }}>
          <label class="ob-btn-label" for="gi_aggressive">Aggressive expansion</label>

          <input type="radio" class="ob-btn-opt" id="gi_steady" name="growth_intent" value="steady"
                 {{ old('growth_intent') === 'steady' ? 'checked' : '' }}>
          <label class="ob-btn-label" for="gi_steady">Steady, sustainable growth</label>

          <input type="radio" class="ob-btn-opt" id="gi_unsure" name="growth_intent" value="unsure"
                 {{ old('growth_intent') === 'unsure' ? 'checked' : '' }}>
          <label class="ob-btn-label" for="gi_unsure">Not sure yet</label>
        </div>
        @error('growth_intent')<span class="ob-error">{{ $message }}</span>@enderror
      </div>

      <div class="ob-field">
        <label class="ob-label">What's your current situation with paid ads?</label>
        <div class="ob-btn-group">
          <input type="radio" class="ob-btn-opt" id="ads_running" name="ads_status" value="running"
                 {{ old('ads_status') === 'running' ? 'checked' : '' }}>
          <label class="ob-btn-label" for="ads_running">Yes, currently running</label>

          <input type="radio" class="ob-btn-opt" id="ads_budget" name="ads_status" value="has_budget"
                 {{ old('ads_status') === 'has_budget' ? 'checked' : '' }}>
          <label class="ob-btn-label" for="ads_budget">Have budget, not running yet</label>

          <input type="radio" class="ob-btn-opt" id="ads_no_budget" name="ads_status" value="no_budget"
                 {{ old('ads_status') === 'no_budget' ? 'checked' : '' }}>
          <label class="ob-btn-label" for="ads_no_budget">No budget yet</label>

          <input type="radio" class="ob-btn-opt" id="ads_not_interested" name="ads_status" value="not_interested"
                 {{ old('ads_status') === 'not_interested' ? 'checked' : '' }}>
          <label class="ob-btn-label" for="ads_not_interested">Not interested in ads</label>
        </div>
        @error('ads_status')<span class="ob-error">{{ $message }}</span>@enderror
      </div>

      <div class="ob-nav">
        <button type="button" class="ob-btn-next" @click="nextStep()">
          Next Step &rarr;
        </button>
        <button type="button" class="ob-btn-back" @click="step = 1">&larr; Back</button>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STEP 3 — Advanced Setup (optional)
    ══════════════════════════════════════════════════ --}}
    <div x-show="step === 3" x-transition:enter="ob-step-enter" x-transition:enter-start="ob-step-from" x-transition:enter-end="ob-step-to">
      <span class="ob-step-eye">Step 3 of 3 — Last step</span>
      <h2 class="ob-step-title">Access &amp; setup.</h2>
      <div class="ob-trust-module">
        <span class="ob-trust-module-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </span>
        <div>
          <p class="ob-trust-module-main">All optional — skip anything you're unsure about. We'll walk through it with you on your call.</p>
          <p class="ob-trust-module-sub">We only request the access needed to install and configure your licensed system. We'll never ask for a password.</p>
        </div>
      </div>

      <div class="ob-field">
        <label class="ob-label ob-label-with-icon">
          <span class="ob-google-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24"><rect x="2" y="14" width="4" height="8" rx="1" fill="rgba(249,171,0,.75)"/><rect x="8" y="9" width="4" height="13" rx="1" fill="rgba(249,171,0,.75)"/><rect x="14" y="5" width="4" height="17" rx="1" fill="rgba(249,171,0,.75)"/><rect x="20" y="2" width="2" height="20" rx="1" fill="rgba(249,171,0,.75)"/></svg></span>
          Google Analytics 4 <span style="color:#555">(do you have it?)</span>
        </label>
        <div class="ob-radio-group">
          <input type="radio" class="ob-radio-opt" id="ga_yes" name="analytics_access" value="1"
                 {{ old('analytics_access') === '1' ? 'checked' : '' }}>
          <label class="ob-radio-btn" for="ga_yes" @click="gaHasIt = true">Yes — I have it</label>

          <input type="radio" class="ob-radio-opt" id="ga_no" name="analytics_access" value="0"
                 {{ old('analytics_access') === '0' ? 'checked' : '' }}>
          <label class="ob-radio-btn" for="ga_no" @click="gaHasIt = false">No / Not sure</label>
        </div>
        <p class="ob-trust-microcopy">Access will be required later to verify tracking, performance, and system accuracy.</p>
        <a href="https://support.google.com/analytics/answer/1009702" target="_blank" rel="noopener noreferrer" class="ob-learn-link">Learn how to grant access →</a>
        <div x-show="gaHasIt" x-transition.opacity.duration.200ms class="ob-access-hint">
          If your GA4 property is already active, we may need setup access to connect and verify your system. We'll ask you to add <strong>hello@seoaico.com</strong> — our team will confirm the appropriate access level with you before you make any changes.
        </div>
      </div>

      <div class="ob-field">
        <label class="ob-label ob-label-with-icon">
          <span class="ob-google-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="rgba(66,133,244,.75)" stroke-width="1.8"/><line x1="16.5" y1="16.5" x2="22" y2="22" stroke="rgba(66,133,244,.75)" stroke-width="1.8" stroke-linecap="round"/></svg></span>
          Google Search Console <span style="color:#555">(do you have it?)</span>
        </label>
        <div class="ob-radio-group">
          <input type="radio" class="ob-radio-opt" id="sc_yes" name="search_console_access" value="1"
                 {{ old('search_console_access') === '1' ? 'checked' : '' }}>
          <label class="ob-radio-btn" for="sc_yes" @click="gscHasIt = true">Yes — I have it</label>

          <input type="radio" class="ob-radio-opt" id="sc_no" name="search_console_access" value="0"
                 {{ old('search_console_access') === '0' ? 'checked' : '' }}>
          <label class="ob-radio-btn" for="sc_no" @click="gscHasIt = false">No / Not sure</label>
        </div>
        <p class="ob-trust-microcopy">Access will be required later to verify tracking, performance, and system accuracy.</p>
        <a href="https://support.google.com/webmasters/answer/2453966" target="_blank" rel="noopener noreferrer" class="ob-learn-link">Learn how to grant access →</a>
        <div x-show="gscHasIt" x-transition.opacity.duration.200ms class="ob-access-hint">
          If Search Console is already active on your site, we may need setup access to connect and verify coverage. We'll ask you to add <strong>hello@seoaico.com</strong> — our team confirms the appropriate role with you before anything is changed.
        </div>
      </div>

      <div class="ob-field">
        <label class="ob-label" for="platform_type">Website Platform</label>
        <select class="ob-select" id="platform_type" name="platform_type"
                @change="onPlatformChange($event)">
          <option value="" {{ old('platform_type') ? '' : 'selected' }}>— Select your platform —</option>
          <option value="wordpress" {{ old('platform_type') === 'wordpress' ? 'selected' : '' }}>WordPress</option>
          <option value="shopify" {{ old('platform_type') === 'shopify' ? 'selected' : '' }}>Shopify</option>
          <option value="other" {{ old('platform_type') === 'other' ? 'selected' : '' }}>Other / Custom</option>
        </select>
        @error('platform_type')<span class="ob-error">{{ $message }}</span>@enderror
      </div>

      <div class="ob-section">How Would You Like to Set Up Access?</div>
      <p class="ob-access-trust-note">We only request the access required to install and configure your licensed system. We will never ask for passwords or full account control.</p>

      <div class="ob-field">
        <div class="ob-radio-group-3">
          <input type="radio" class="ob-radio-opt" id="access_invite" name="access_method" value="invite_email"
                 {{ old('access_method', 'invite_email') === 'invite_email' ? 'checked' : '' }}>
          <label class="ob-radio-btn-3 ob-recommended" for="access_invite" @click="onAccessChange('invite_email')">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:5px;opacity:.65" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Invite us via email
            <span>Recommended — no passwords ever shared</span>
          </label>

          <input type="radio" class="ob-radio-opt" id="access_later" name="access_method" value="provide_later"
                 {{ old('access_method') === 'provide_later' ? 'checked' : '' }}>
          <label class="ob-radio-btn-3" for="access_later" @click="onAccessChange('provide_later')">
            Guided setup session
            <span>Step-by-step on your call — recommended for non-technical setups</span>
          </label>
        </div>
        @error('access_method')<span class="ob-error" style="margin-top:8px;display:block">{{ $message }}</span>@enderror
      </div>

      {{-- Dynamic platform instructions (shown when invite_email + platform selected) --}}
      <div x-show="showInstructions && platform === 'wordpress'" class="ob-instruction">
        <strong>WordPress Setup Guidance</strong>
        <p>You will be adding us as a collaborator so we can install and activate your system.</p>
        <p style="margin-top:8px">We may need temporary access for plugin installation and system configuration. The simplest method is inviting hello@seoaico.com as a user — we'll confirm the safest approach with you before you make any changes.</p>
        <p class="ob-instruction-role-note">We assign administrator-level access so we can install and configure your system correctly. We confirm the safest method with you first.</p>
        <span class="ob-invite-email">Setup email: hello@seoaico.com</span>
      </div>

      <div x-show="showInstructions && platform === 'shopify'" class="ob-instruction">
        <strong>Shopify Setup Guidance</strong>
        <p>For Shopify stores, we may need staff access to connect and configure your licensed system. The easiest method is adding hello@seoaico.com as a staff member — your team remains in full control at all times.</p>
        <p class="ob-instruction-role-note">We'll confirm the appropriate permissions with you before you proceed.</p>
        <span class="ob-invite-email">Setup email: hello@seoaico.com</span>
      </div>

      <div x-show="showInstructions && platform === 'other'" class="ob-instruction">
        <strong>Custom Platform Access</strong>
        <p>Custom platforms may require additional configuration and development support.</p>
        <p style="margin-top:10px">If you're unsure, we recommend a guided setup session where we handle everything with you.</p>
        <a href="/book" class="ob-setup-link">Book Setup Session &rarr;</a>
      </div>

      {{-- ── Recommended Enhancements ── --}}
      <div class="ob-section" style="margin-top:40px">Recommended Enhancements</div>
      <p class="ob-enhancements-intro">Enhancements can be added now or confirmed later based on your rollout needs. No charges are applied without your explicit approval.</p>

      <div class="ob-addons-grid">
        <div>
          <input type="checkbox" class="ob-addon-opt" id="addon_local_seo" name="add_ons[]" value="local_seo_setup"
                 {{ in_array('local_seo_setup', old('add_ons', [])) ? 'checked' : '' }}>
          <label class="ob-addon-card" for="addon_local_seo">
            <div class="ob-addon-header">
              <span style="display:flex;align-items:center;gap:4px">
                <span class="ob-addon-name">Local SEO Setup</span>
                <span class="ob-info-btn" data-tip="Establishes your local search presence and helps secure your territory early.">i</span>
              </span>
              <span class="ob-addon-check"></span>
            </div>
            <span class="ob-addon-price">From $199 one-time</span>
            <span class="ob-addon-desc">Establishes your local search foundation — Google Business, citations, and location schema — so your territory holds from day one.</span>
          </label>
        </div>

        <div>
          <input type="checkbox" class="ob-addon-opt" id="addon_ads_setup" name="add_ons[]" value="google_ads_setup"
                 {{ in_array('google_ads_setup', old('add_ons', [])) ? 'checked' : '' }}>
          <label class="ob-addon-card" for="addon_ads_setup">
            <div class="ob-addon-header">
              <span style="display:flex;align-items:center;gap:4px">
                <span class="ob-addon-name">Google Ads Setup</span>
                <span class="ob-info-btn" data-tip="Accelerates visibility while organic positioning builds.">i</span>
              </span>
              <span class="ob-addon-check"></span>
            </div>
            <span class="ob-addon-price">From $299 one-time</span>
            <span class="ob-addon-desc">Paid coverage that runs alongside your organic position — campaigns, targeting, and conversion tracking built to your market.</span>
          </label>
        </div>

        <div>
          <input type="checkbox" class="ob-addon-opt" id="addon_reporting" name="add_ons[]" value="monthly_reporting"
                 {{ in_array('monthly_reporting', old('add_ons', [])) ? 'checked' : '' }}>
          <label class="ob-addon-card" for="addon_reporting">
            <div class="ob-addon-header">
              <span style="display:flex;align-items:center;gap:4px">
                <span class="ob-addon-name">Monthly Reporting</span>
                <span class="ob-info-btn" data-tip="Tracks performance, rankings, and ROI over time.">i</span>
              </span>
              <span class="ob-addon-check"></span>
            </div>
            <span class="ob-addon-price">$99/month</span>
            <span class="ob-addon-desc">Branded performance dashboard delivered monthly — rankings, traffic, and position progress in one clear view.</span>
          </label>
        </div>

        <div>
          <input type="checkbox" class="ob-addon-opt" id="addon_competitor" name="add_ons[]" value="competitor_analysis"
                 {{ in_array('competitor_analysis', old('add_ons', [])) ? 'checked' : '' }}>
          <label class="ob-addon-card" for="addon_competitor">
            <div class="ob-addon-header">
              <span style="display:flex;align-items:center;gap:4px">
                <span class="ob-addon-name">Territory Intelligence</span>
                <span class="ob-info-btn" data-tip="Identifies competitive gaps and expansion opportunities.">i</span>
              </span>
              <span class="ob-addon-check"></span>
            </div>
            <span class="ob-addon-price">$149 one-time</span>
            <span class="ob-addon-desc">Analysis of your top competitors — coverage gaps, link structure, and positioning — so your system is calibrated from the start.</span>
          </label>
        </div>
      </div>

      {{-- ── Federal Research Credit (informational, optional) ── --}}
      <div class="ob-rd-section">
        <span class="ob-rd-eye">Additional Opportunities — Optional</span>
        <h3 class="ob-rd-hed">Federal Research Credit (Form 6765)</h3>
        <p class="ob-rd-body">Some businesses may perform activities that are evaluated under the Internal Revenue Code Section 41, commonly referred to as the &ldquo;Credit for Increasing Research Activities.&rdquo;</p>
        <p class="ob-rd-body">This credit is calculated using IRS Form 6765 and is based on Qualified Research Expenses (QREs), which may include certain wages, supplies, and contract research costs associated with developing or improving products, processes, or software.</p>
        <p class="ob-rd-body">The IRS applies a specific four-part test to determine whether activities meet the definition of qualified research.</p>
        <p class="ob-rd-body">We do not determine eligibility or provide tax advice. Qualification must be assessed by a licensed CPA or qualified tax professional based on your specific facts and documentation.</p>
        <p class="ob-rd-body">If you are interested, we can refer you to a third-party firm that specializes in evaluating and preparing research credit documentation in accordance with IRS guidance.</p>
        <div class="ob-rd-links">
          <a href="https://www.irs.gov/instructions/i6765" target="_blank" rel="noopener noreferrer" class="ob-rd-link">View IRS Instructions &rarr;</a>
          <a href="https://www.irs.gov/pub/irs-pdf/f6765.pdf" target="_blank" rel="noopener noreferrer" class="ob-rd-link">Download Form 6765 &rarr;</a>
        </div>
        <label class="ob-rd-referral">
          <input type="checkbox" name="rd_referral_interest" value="1"
                 {{ old('rd_referral_interest') ? 'checked' : '' }}>
          <span>I&rsquo;d like a referral to a research credit specialist</span>
        </label>
        <p class="ob-rd-disclaimer">This information is provided for general awareness only and does not constitute tax, legal, or financial advice. SEOAIco does not prepare or file tax credits. All determinations must be made by a qualified tax professional.</p>
      </div>

      {{-- ── Submit ── --}}
      <p class="ob-activation-note">
        <em>Access enables activation.</em> Positions are not reserved without activation.
      </p>

      <div class="ob-nav" style="margin-top:24px">
        <button type="submit" class="ob-submit" id="submit-btn">
          Complete Onboarding &rarr;
        </button>
        <button type="button" class="ob-btn-back" @click="step = 2">&larr; Back</button>
      </div>

      <div class="ob-privacy-block">
        <span class="ob-privacy-icon">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        </span>
        <div>
          <p>Your information is securely handled and used only for system setup and activation. We never share access or data with third parties.</p>
          <a href="{{ route('privacy') }}" class="ob-privacy-link">Privacy Policy →</a>
        </div>
      </div>
    </div>

  </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('onboardingWizard', () => ({
    step: {{ $errors->any() ? 1 : 1 }},
    platform: '{{ old('platform_type', '') }}',
    accessMethod: '{{ old('access_method', 'invite_email') }}',
    showAddons: {{ count(old('add_ons', [])) > 0 ? 'true' : 'false' }},
    gaHasIt: {{ old('analytics_access') === '1' ? 'true' : 'false' }},
    gscHasIt: {{ old('search_console_access') === '1' ? 'true' : 'false' }},

    get showInstructions() {
      return this.accessMethod === 'invite_email' && !!this.platform;
    },

    init() {
      // Restore step if there are validation errors
      @if($errors->has('goals') || $errors->has('challenges') || $errors->has('growth_intent') || $errors->has('ads_status'))
        this.step = 2;
      @elseif($errors->has('analytics_access') || $errors->has('search_console_access') || $errors->has('platform_type') || $errors->has('access_method'))
        this.step = 3;
      @endif
    },

    nextStep() {
      if (this.step === 1) {
        const name = document.getElementById('primary_contact')?.value?.trim();
        const biz = document.getElementById('business_name')?.value?.trim();
        @if($booking === null)
        const email = document.getElementById('email')?.value?.trim();
        @else
        const email = 'ok'; // booking has email
        @endif
        if (!biz) { document.getElementById('business_name')?.focus(); return; }
        if (!name) { document.getElementById('primary_contact')?.focus(); return; }
        if (!email) { document.getElementById('email')?.focus(); return; }
      }
      this.step++;
      window.scrollTo({ top: 0, behavior: 'smooth' });
      if (typeof gtag === 'function') {
        gtag('event', 'onboarding_step_completed', { step: this.step - 1 });
      }
    },

    prefixWebsite(event) {
      const input = event.target;
      if (input.value && !/^https?:\/\//i.test(input.value)) {
        input.value = 'https://' + input.value;
      }
    },

    onPlatformChange(event) {
      this.platform = event.target.value;
    },

    onAccessChange(value) {
      this.accessMethod = value;
    },
  }));
});

// Disable submit on submit to prevent double-post
document.getElementById('ob-form').addEventListener('submit', function() {
  const btn = document.getElementById('submit-btn');
  btn.disabled = true;
  btn.textContent = 'Submitting…';
});
</script>
<script>
  if(typeof gtag==='function'){
    gtag('event','onboarding_start',{page_location:window.location.href});
    gtag('event','start_onboarding',{page_location:window.location.href});
  }
</script>
</body>
</html>
