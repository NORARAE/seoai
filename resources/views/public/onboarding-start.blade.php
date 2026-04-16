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
<title>Start Your System Deployment | SEO AI Co™</title>
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
  font-size: .75rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: #6a6a62;
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
.ob-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 480px) { .ob-row { grid-template-columns: 1fr; } }
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
.ob-nav { display: flex; gap: 20px; align-items: center; margin-top: 36px; flex-wrap: wrap; }
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
  color: rgba(168,168,160,.82);
  font-size: .80rem;
  letter-spacing: .10em;
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

/* ── Session transition ── */
.ob-session-secured {
  font-size: 1.05rem;
  font-weight: 400;
  color: var(--ivory);
  line-height: 1.5;
  margin-bottom: 6px;
}
.ob-session-secured-sub {
  font-size: .875rem;
  color: rgba(168,168,160,.62);
  line-height: 1.78;
  margin-bottom: 28px;
}

/* ── Enhancements intro ── */
.ob-enhancements-intro {
  font-size: .84rem;
  color: var(--muted);
  line-height: 1.7;
  margin-bottom: 20px;
}

/* ── Full-service capabilities block ── */
.ob-fullsvc-block {
  margin: 0 0 28px;
  padding: 22px 24px;
  border: 1px solid rgba(200,168,75,.10);
  border-radius: 10px;
  background: rgba(200,168,75,.016);
}
.ob-fullsvc-hed {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.24rem;
  font-weight: 300;
  color: var(--ivory);
  margin-bottom: 10px;
  line-height: 1.2;
}
.ob-fullsvc-body {
  font-size: .84rem;
  color: rgba(168,168,160,.65);
  line-height: 1.8;
  margin-bottom: 8px;
}
.ob-fullsvc-list {
  list-style: none;
  margin: 8px 0 12px;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.ob-fullsvc-list li {
  font-size: .82rem;
  color: rgba(168,168,160,.60);
  padding-left: 14px;
  position: relative;
  line-height: 1.5;
}
.ob-fullsvc-list li::before {
  content: '–';
  position: absolute;
  left: 0;
  color: rgba(200,168,75,.32);
}
.ob-fullsvc-note {
  font-size: .78rem;
  color: rgba(168,168,160,.40);
  line-height: 1.72;
  border-top: 1px solid rgba(200,168,75,.06);
  padding-top: 10px;
  margin-top: 4px;
}
.ob-fullsvc-note strong {
  color: rgba(200,168,75,.58);
  font-weight: 400;
}

/* ── Path note (conditional guidance by lead_type) ── */
.ob-path-note {
  margin: 14px 0 0;
  padding: 12px 16px;
  border-left: 2px solid rgba(200,168,75,.3);
  font-size: .8rem;
  color: rgba(226,201,125,.7);
  line-height: 1.6;
  background: rgba(200,168,75,.03);
  border-radius: 0 6px 6px 0;
}

/* ── Tier ladder (decision frame) ── */
.ob-tier-ladder {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 7px;
  margin: 14px 0 22px;
  padding: 0;
}
.ob-tier-item {
  display: flex;
  align-items: baseline;
  gap: 10px;
  font-size: .8rem;
  color: var(--muted);
  line-height: 1.55;
}
.ob-tier-item::before {
  content: '—';
  color: var(--gold);
  opacity: .5;
  flex-shrink: 0;
}
.ob-tier-item strong {
  color: rgba(237,232,222,.7);
  font-weight: 400;
}

/* ── Decision frame block ── */
.ob-decision-frame {
  margin: 32px 0 28px;
  padding: 24px 22px;
  border: 1px solid rgba(200,168,75,.12);
  border-radius: 10px;
  background: rgba(200,168,75,.015);
  position: relative;
}
.ob-decision-frame::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(200,168,75,.22), transparent);
}
.ob-decision-hed {
  font-size: .82rem;
  letter-spacing: .10em;
  text-transform: uppercase;
  color: rgba(237,232,222,.62);
  margin-bottom: 4px;
}
.ob-decision-sub {
  font-size: .73rem;
  color: rgba(168,168,160,.38);
  letter-spacing: .02em;
  margin-top: 4px;
  font-style: italic;
}

/* ── Premium path rows (Multi-location, Agency) ── */
.ob-premium-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  padding: 16px 20px;
  border: 1px solid rgba(200,168,75,.15);
  border-radius: 10px;
  margin-top: 10px;
  background: rgba(200,168,75,.015);
}
.ob-premium-row-body { display: flex; flex-direction: column; gap: 4px; }
.ob-premium-row-name { font-size: .88rem; color: var(--ivory); font-weight: 400; }
.ob-premium-row-label {
  font-size: .62rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--gold);
  opacity: .55;
  font-weight: 400;
}
.ob-premium-row-desc { font-size: .78rem; color: #888; line-height: 1.55; max-width: 86%; }
.ob-premium-row-badge {
  font-size: .62rem;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: var(--gold);
  opacity: .45;
  white-space: nowrap;
  flex-shrink: 0;
  padding-top: 2px;
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

/* ── Addon info button ── */
.ob-info-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 16px; height: 16px;
  border: 1px solid rgba(200,168,75,.25);
  border-radius: 50%;
  cursor: pointer;
  font-size: .60rem;
  font-style: normal;
  color: rgba(200,168,75,.5);
  flex-shrink: 0;
  line-height: 1;
  transition: border-color .2s, color .2s, background .2s;
  user-select: none;
}
.ob-info-btn:hover { border-color: rgba(200,168,75,.55); color: var(--gold); background: rgba(200,168,75,.05); }

/* ── Addon detail panel overlay ── */
.ob-detail-overlay {
  position: fixed;
  inset: 0;
  z-index: 9500;
  pointer-events: none;
}
.ob-detail-overlay.is-open {
  pointer-events: auto;
}
.ob-detail-backdrop {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,.52);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  opacity: 0;
  transition: opacity .3s ease;
}
.ob-detail-overlay.is-open .ob-detail-backdrop { opacity: 1; }
.ob-detail-panel {
  position: absolute;
  top: 0; right: 0; bottom: 0;
  width: min(420px, 100vw);
  background: #0d0d0d;
  border-left: 1px solid rgba(200,168,75,.13);
  padding: 48px 32px 64px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  transform: translateX(100%);
  transition: transform .32s cubic-bezier(.22,1,.36,1);
}
.ob-detail-overlay.is-open .ob-detail-panel { transform: translateX(0); }
@media (max-width: 600px) {
  .ob-detail-panel {
    top: auto; right: 0; left: 0; bottom: 0;
    width: 100%;
    max-height: 84vh;
    border-left: none;
    border-top: 1px solid rgba(200,168,75,.13);
    border-radius: 16px 16px 0 0;
    padding: 32px 24px 56px;
    transform: translateY(100%);
  }
  .ob-detail-overlay.is-open .ob-detail-panel { transform: translateY(0); }
}
.ob-detail-handle { display:none }
@media (max-width: 600px) {
  .ob-detail-handle {
    display: block;
    width: 36px; height: 4px;
    border-radius: 2px;
    background: rgba(200,168,75,.18);
    margin: 0 auto 24px;
    flex-shrink: 0;
  }
}
.ob-detail-close {
  position: absolute;
  top: 16px; right: 16px;
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 50%;
  width: 34px; height: 34px;
  color: #8a8a8a;
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color .2s, background .2s;
  line-height: 1;
  padding: 0;
}
.ob-detail-close:hover { color: #ede8de; background: rgba(255,255,255,.09); }
.ob-detail-eyebrow {
  font-size: .62rem;
  letter-spacing: .22em;
  text-transform: uppercase;
  color: var(--gold-dim);
  margin-bottom: 12px;
  display: block;
}
.ob-detail-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.5rem;
  font-weight: 300;
  color: var(--ivory);
  line-height: 1.2;
  margin-bottom: 8px;
}
.ob-detail-price-badge {
  display: inline-block;
  font-size: .72rem;
  color: var(--gold);
  background: rgba(200,168,75,.07);
  border: 1px solid rgba(200,168,75,.18);
  border-radius: 4px;
  padding: 4px 11px;
  margin-bottom: 24px;
  letter-spacing: .04em;
}
.ob-detail-divider {
  border: none;
  border-top: 1px solid rgba(200,168,75,.07);
  margin: 18px 0;
}
.ob-detail-section { margin-bottom: 20px; }
.ob-detail-section-label {
  font-size: .62rem;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: rgba(168,168,160,.4);
  margin-bottom: 7px;
  display: block;
}
.ob-detail-body {
  font-size: .84rem;
  color: var(--muted);
  line-height: 1.78;
}
.ob-detail-list {
  list-style: none;
  margin: 0; padding: 0;
  display: flex;
  flex-direction: column;
  gap: 7px;
}
.ob-detail-list li {
  font-size: .82rem;
  color: var(--muted);
  line-height: 1.55;
  padding-left: 16px;
  position: relative;
}
.ob-detail-list li::before {
  content: '—';
  position: absolute; left: 0;
  color: var(--gold-dim);
  font-size: .70rem;
}
.ob-detail-rd-links {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 4px;
}
.ob-detail-rd-link {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: .78rem;
  color: var(--gold-dim);
  text-decoration: none;
  border: 1px solid rgba(200,168,75,.15);
  border-radius: 6px;
  padding: 10px 14px;
  letter-spacing: .06em;
  transition: border-color .2s, color .2s, background .2s;
}
.ob-detail-rd-link:hover { border-color: rgba(200,168,75,.35); color: var(--gold); background: rgba(200,168,75,.04); }

/* ── R&D section expand toggle ── */
.ob-rd-toggle {
  background: none;
  border: none;
  cursor: pointer;
  font-size: .76rem;
  color: rgba(168,168,160,.72);
  letter-spacing: .08em;
  padding: 0;
  margin-top: 10px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: color .2s;
  text-decoration: none;
}
.ob-rd-toggle:hover { color: var(--muted); }
.ob-rd-toggle-arrow { transition: transform .25s ease; display: inline-block; }
.ob-rd-toggle-arrow.open { transform: rotate(90deg); }

/* ── R&D Credit informational section ── */
.ob-rd-preline {
  font-size: .87rem;
  color: rgba(168,168,160,.80);
  line-height: 1.7;
  margin-top: 40px;
  margin-bottom: 10px;
  font-style: italic;
}
.ob-rd-section {
  margin-top: 0;
  padding: 22px 24px;
  border: 1px solid rgba(200,168,75,.10);
  border-radius: 10px;
  background: rgba(200,168,75,.015);
}
.ob-rd-eye {
  font-size: .76rem;
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
  font-size: .76rem;
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
.ob-rd-microcopy {
  font-size: .76rem;
  color: rgba(168,168,160,.72);
  font-style: italic;
  line-height: 1.65;
  margin-bottom: 6px;
}
.ob-rd-bullets{list-style:none;margin:0 0 12px;padding:0;display:flex;flex-direction:column;gap:7px}
.ob-rd-bullets li{font-size:.87rem;color:rgba(168,168,160,.85);padding-left:18px;position:relative;line-height:1.5}
.ob-rd-bullets li::before{content:'·';color:rgba(200,168,75,.68);position:absolute;left:0;font-size:1.2rem;line-height:1.1}
.ob-rd-cta-link{display:inline-block;margin-top:12px;font-size:.80rem;letter-spacing:.08em;color:rgba(200,168,75,.80);text-decoration:none;border:1px solid rgba(200,168,75,.26);border-radius:4px;padding:9px 18px;transition:color .2s,border-color .2s}
.ob-rd-cta-link:hover{color:var(--gold);border-color:rgba(200,168,75,.52)}
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
  font-size: .76rem;
  color: rgba(168,168,160,.68);
  line-height: 1.75;
  margin-top: 16px;
  padding-top: 14px;
  border-top: 1px solid rgba(200,168,75,.06);
  font-style: italic;
}

/* ── Activation note ── */
.ob-activation-note {
  font-size: .87rem;
  color: rgba(168,168,160,.80);
  line-height: 1.75;
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid rgba(200,168,75,.06);
}
.ob-activation-note em { font-style: normal; color: rgba(168,168,160,.95); }

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
  .ob-activation-note { font-size: .90rem; color: rgba(168,168,160,.85); }
  .ob-activation-note em { color: rgba(168,168,160,.98); }
  .ob-privacy-block { font-size: .82rem; }
  .ob-trust-microcopy { font-size: .78rem; }
}
</style>
@include('partials.clarity')
<link rel="canonical" href="{{ url('/onboarding/start') }}">
<meta name="robots" content="noindex, nofollow">
</head>
<body x-data="onboardingWizard()" x-cloak>
<div class="ob-wrap">

  <a href="/" style="display:inline-flex;align-items:baseline;text-decoration:none;line-height:1;margin-bottom:36px">
    <span style="font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.1rem;letter-spacing:.06em;color:var(--ivory)">SEO</span><span style="font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.3rem;color:var(--gold)">AI</span><span style="font-family:'DM Sans',sans-serif;font-weight:300;font-size:.9rem;color:rgba(150,150,150,.5);letter-spacing:.04em">co</span>
  </a>

  <span class="ob-eye">{{ ($isPreview ?? false) ? 'System Deployment' : 'System Deployment' }}</span>
  <p style="font-size:.86rem;color:rgba(168,168,160,.60);margin:0 0 12px;letter-spacing:.01em">You've seen your gaps. You know what's missing. Let's build it.</p>
  <p style="font-size:.72rem;color:rgba(200,168,75,.4);margin:0 0 24px;font-style:italic">We build the structure that makes AI systems return you as the answer.</p>
  <h1 class="ob-hed">
    @if($isPreview ?? false)
      Deploy your<br><em>market system.</em>
    @else
      Let's prepare<br><em>your system deployment.</em>
    @endif
  </h1>
  <p class="ob-sub">Takes about 2 minutes. This maps what we need to build and deploy your coverage infrastructure.</p>

  @if(!empty($tier))
  <p style="font-size:.78rem;letter-spacing:.12em;text-transform:uppercase;color:var(--gold);opacity:.72;margin:0 0 28px">{{ $tier === 'launch' ? "Starting with Launch deployment." : ($tier === 'expansion' ? "Starting with Expansion deployment." : "Entering Dominance deployment.") }}</p>
  @endif

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
    <input type="hidden" name="scan_id" value="{{ $scanId ?? '' }}">
    <input type="hidden" name="plan" value="{{ $plan ?? '' }}">

    {{-- ══════════════════════════════════════════════════
         STEP 1 — Basic Profile
    ══════════════════════════════════════════════════ --}}
    <div x-show="step === 1" x-transition:enter="ob-step-enter" x-transition:enter-start="ob-step-from" x-transition:enter-end="ob-step-to">
      <span class="ob-step-eye">Step 1 of 3</span>
      <h2 class="ob-step-title">Tell us about your business.</h2>
      <p class="ob-step-hint">This helps us scope your system deployment.</p>

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

      <div class="ob-row">
        <div class="ob-field">
          <label class="ob-label" for="years_in_business">Years in Business</label>
          <select class="ob-input" id="years_in_business" name="years_in_business">
            <option value="">Select…</option>
            <option value="0_to_1" {{ old('years_in_business') === '0_to_1' ? 'selected' : '' }}>Less than 1 year</option>
            <option value="1_to_3" {{ old('years_in_business') === '1_to_3' ? 'selected' : '' }}>1–3 years</option>
            <option value="3_to_10" {{ old('years_in_business') === '3_to_10' ? 'selected' : '' }}>3–10 years</option>
            <option value="10_plus" {{ old('years_in_business') === '10_plus' ? 'selected' : '' }}>10+ years</option>
          </select>
        </div>
        <div class="ob-field">
          <label class="ob-label" for="team_size">Team Size</label>
          <select class="ob-input" id="team_size" name="team_size">
            <option value="">Select…</option>
            <option value="solo" {{ old('team_size') === 'solo' ? 'selected' : '' }}>Solo operator</option>
            <option value="2_to_5" {{ old('team_size') === '2_to_5' ? 'selected' : '' }}>2–5 people</option>
            <option value="6_to_20" {{ old('team_size') === '6_to_20' ? 'selected' : '' }}>6–20 people</option>
            <option value="20_plus" {{ old('team_size') === '20_plus' ? 'selected' : '' }}>20+ people</option>
          </select>
        </div>
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
      <p class="ob-step-hint">This helps us understand your situation and prepare the right approach for your session.</p>

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
        <label class="ob-label">Do you operate in multiple locations?</label>
        <div class="ob-btn-group">
          <input type="radio" class="ob-btn-opt" id="lt_single" name="lead_type" value="single_location"
                 {{ old('lead_type', 'single_location') === 'single_location' ? 'checked' : '' }}
                 @change="leadType = 'single_location'">
          <label class="ob-btn-label" for="lt_single">No — single location</label>

          <input type="radio" class="ob-btn-opt" id="lt_multi" name="lead_type" value="multi_location"
                 {{ old('lead_type') === 'multi_location' ? 'checked' : '' }}
                 @change="leadType = 'multi_location'">
          <label class="ob-btn-label" for="lt_multi">Yes — multiple markets</label>

          <input type="radio" class="ob-btn-opt" id="lt_agency" name="lead_type" value="agency"
                 {{ old('lead_type') === 'agency' ? 'checked' : '' }}
                 @change="leadType = 'agency'">
          <label class="ob-btn-label" for="lt_agency">Yes — I'm an agency</label>
        </div>
        @error('lead_type')<span class="ob-error">{{ $message }}</span>@enderror

        {{-- Conditional: scale question for multi-location and agency --}}
        <div x-show="leadType === 'multi_location' || leadType === 'agency'" x-cloak style="margin-top:14px">
          <label class="ob-label" style="font-size:.82rem;opacity:.8" for="number_of_locations">
            How many locations or sites are involved?
          </label>
          <select class="ob-select" id="number_of_locations" name="number_of_locations" style="margin-top:6px">
            <option value="" disabled selected>Select range</option>
            <option value="2_to_5" {{ old('number_of_locations') === '2_to_5' ? 'selected' : '' }}>2 &ndash; 5</option>
            <option value="6_to_10" {{ old('number_of_locations') === '6_to_10' ? 'selected' : '' }}>6 &ndash; 10</option>
            <option value="11_to_20" {{ old('number_of_locations') === '11_to_20' ? 'selected' : '' }}>11 &ndash; 20</option>
            <option value="20_plus" {{ old('number_of_locations') === '20_plus' ? 'selected' : '' }}>20+</option>
          </select>
        </div>

        {{-- Identity + path notes --}}
        <div class="ob-path-note" x-show="leadType === 'single_location'" x-cloak>
          Single-market activation is the standard starting point. The system is designed to scale from here.
        </div>
        <div class="ob-path-note" x-show="leadType === 'multi_location'" x-cloak>
          Your deployment spans multiple markets. We'll scope and structure this correctly for your situation.
        </div>
        <div class="ob-path-note" x-show="leadType === 'agency'" x-cloak>
          Partner-level access applies here. A structured deployment review is the appropriate path for your situation.
        </div>
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

      {{-- ── Growth & Support Options ── --}}
      <div class="ob-section" style="margin-top:40px">Strategic Acceleration</div>
      <p class="ob-session-secured">You’ve secured your session.<br><br>Now we prepare your market.</p>
      <p class="ob-session-secured-sub">These selections determine how fast your system deploys, how precise your structure is, and how strong your position becomes. No charges are added without your direct approval.</p>
      @if(!empty($tier))
      <p class="ob-session-secured-sub" style="color:rgba(200,168,75,.62);margin-top:-10px;margin-bottom:20px">{{ $tier === 'launch' ? 'Your Launch path is set. These decisions shape how quickly your market position takes hold.' : ($tier === 'expansion' ? 'Your Expansion path is set. These decisions determine how broadly and how fast your system scales.' : 'Your Dominance path is set. These decisions drive the speed and depth of full-market coverage.') }}</p>
      @endif
      <p class="ob-fine" style="color:rgba(168,168,160,.30);font-style:italic;margin:0 0 22px;letter-spacing:.02em">These options help us move faster, build smarter, and strengthen your position from the start.</p>



      {{-- Conditional path note in Step 3 --}}
      <div class="ob-path-note" x-show="leadType === 'multi_location'" x-cloak style="margin-bottom:18px">
        Your deployment spans multiple markets. We'll scope this correctly for your situation.
      </div>
      <div class="ob-path-note" x-show="leadType === 'agency'" x-cloak style="margin-bottom:18px">
        Partner-level access applies here. A structured deployment review is the appropriate path.
      </div>

      <div class="ob-fullsvc-block">
        <p class="ob-fullsvc-hed">This is more than visibility.</p>
        <p class="ob-fullsvc-body">We build, expand, and support your full growth system:</p>
        <ul class="ob-fullsvc-list">
          <li>Website structure and conversion architecture</li>
          <li>Market expansion and search visibility</li>
          <li>Paid acquisition and campaign strategy</li>
          <li>Brand positioning and creative direction</li>
        </ul>
        <p class="ob-fullsvc-body">The system drives visibility.<br>We support everything behind it.</p>
        <p class="ob-fullsvc-note">Typical investment: $3,000 – $15,000+ depending on scope and speed. <strong>Confirmed after your session.</strong></p>
      </div>

      <p class="ob-fine" style="color:rgba(168,168,160,.30);text-align:center;margin:0 0 20px;font-size:.76rem;letter-spacing:.025em">Pricing reflects the level of work, analysis, and system integration involved.</p>

      <div class="ob-addons-grid">
        <div>
          <input type="checkbox" class="ob-addon-opt" id="addon_growth_review" name="add_ons[]" value="website_growth_review"
                 {{ in_array('website_growth_review', old('add_ons', [])) ? 'checked' : '' }}>
          <label class="ob-addon-card" for="addon_growth_review">
            <div class="ob-addon-header">
              <span style="display:flex;align-items:center;gap:4px">
                <span class="ob-addon-name">Website + Growth System Review</span>
                <span class="ob-info-btn" @click.prevent.stop="openAddonDetail('website_growth_review')" aria-label="About Website + Growth System Review">i</span>
              </span>
              <span class="ob-addon-check"></span>
            </div>
            <span class="ob-addon-price">$150 one-time</span>
            <span class="ob-addon-desc">A hands-on review of your website and current marketing setup — what's working, what's missing, and what to fix first. Delivered before your session so we can act on real findings immediately.</span>
            <span style="display:inline-block;margin-top:6px;font-size:.72rem;color:var(--gold);letter-spacing:.06em;text-transform:uppercase">Most selected &middot; best first step</span>
          </label>
        </div>

        <div>
          <input type="checkbox" class="ob-addon-opt" id="addon_local_seo" name="add_ons[]" value="local_seo_setup"
                 {{ in_array('local_seo_setup', old('add_ons', [])) ? 'checked' : '' }}>
          <label class="ob-addon-card" for="addon_local_seo">
            <div class="ob-addon-header">
              <span style="display:flex;align-items:center;gap:4px">
                <span class="ob-addon-name">Local SEO Setup</span>
                <span class="ob-info-btn" @click.prevent.stop="openAddonDetail('local_seo')" aria-label="About Local SEO Setup">i</span>
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
                <span class="ob-addon-name">Market Activation Campaign</span>
                <span class="ob-info-btn" @click.prevent.stop="openAddonDetail('google_ads')" aria-label="About Market Activation Campaign">i</span>
              </span>
              <span class="ob-addon-check"></span>
            </div>
            <span class="ob-addon-price">Setup from $750&ndash;$1,500+ &middot; ad spend paid directly to platform</span>
            <span class="ob-addon-desc">We build your full paid acquisition setup — campaigns, targeting, and conversion tracking — structured around your market. Ad spend goes directly to the platform. Ongoing management scoped separately after your session.</span>
            <span style="display:inline-block;margin-top:6px;font-size:.72rem;color:var(--gold);letter-spacing:.06em;text-transform:uppercase">Used by growth-focused clients</span>
          </label>
        </div>

        <div>
          <input type="checkbox" class="ob-addon-opt" id="addon_reporting" name="add_ons[]" value="monthly_reporting"
                 {{ in_array('monthly_reporting', old('add_ons', [])) ? 'checked' : '' }}>
          <label class="ob-addon-card" for="addon_reporting">
            <div class="ob-addon-header">
              <span style="display:flex;align-items:center;gap:4px">
                <span class="ob-addon-name">SEOAIco&trade; Market Position Tracking</span>
                <span class="ob-info-btn" @click.prevent.stop="openAddonDetail('reporting')" aria-label="About Market Position Tracking">i</span>
              </span>
              <span class="ob-addon-check"></span>
            </div>
            <span class="ob-addon-price">$349&ndash;$749/month</span>
            <span class="ob-addon-desc">Strategic position tracking — not automated output. Clear, branded, and structured to show exactly how your market position is building month over month.</span>
          </label>
        </div>

        <div>
          <input type="checkbox" class="ob-addon-opt" id="addon_competitor" name="add_ons[]" value="competitor_analysis"
                 {{ in_array('competitor_analysis', old('add_ons', [])) ? 'checked' : '' }}>
          <label class="ob-addon-card" for="addon_competitor">
            <div class="ob-addon-header">
              <span style="display:flex;align-items:center;gap:4px">
                <span class="ob-addon-name">SEOAIco&trade; Market Intelligence Report</span>
                <span class="ob-info-btn" @click.prevent.stop="openAddonDetail('territory')" aria-label="About Market Intelligence Report">i</span>
              </span>
              <span class="ob-addon-check"></span>
            </div>
            <span class="ob-addon-price">$349–$749+</span>
            <span class="ob-addon-desc">A research report on your market — who ranks, where they’re weak, and exactly where your business can grow first. Delivered before deployment begins so we target real opportunities from day one.</span>
          </label>
        </div>

        <div style="grid-column:1/-1">
          <input type="checkbox" class="ob-addon-opt" id="addon_ai_report" name="add_ons[]" value="ai_market_report"
                 {{ in_array('ai_market_report', old('add_ons', [])) ? 'checked' : '' }}>
          <label class="ob-addon-card" for="addon_ai_report">
            <div class="ob-addon-header">
              <span style="display:flex;align-items:center;gap:4px">
                <span class="ob-addon-name">SEOAIco&trade; AI Market Analysis</span>
                <span class="ob-info-btn" @click.prevent.stop="openAddonDetail('ai_market_report')" aria-label="About AI Market Analysis">i</span>
              </span>
              <span class="ob-addon-check"></span>
            </div>
            <span class="ob-addon-price">$349–$999</span>
            <span class="ob-addon-desc">Full AI-driven market analysis — who ranks, where the gaps are, and exactly where to deploy first for the strongest position. Delivered before your session so every decision is backed by data.</span>
            <span style="display:inline-block;margin-top:6px;font-size:.72rem;color:var(--gold);letter-spacing:.06em;text-transform:uppercase">Recommended starting point &middot; strategic advantage</span>
          </label>
        </div>

        <div style="grid-column:1/-1">
          <input type="checkbox" class="ob-addon-opt" id="addon_full_service" name="add_ons[]" value="full_service_marketing"
                 {{ in_array('full_service_marketing', old('add_ons', [])) ? 'checked' : '' }}>
          <label class="ob-addon-card" for="addon_full_service">
            <div class="ob-addon-header">
              <span style="display:flex;align-items:center;gap:4px">
                <span class="ob-addon-name">Full-Service Marketing Support</span>
                <span class="ob-info-btn" @click.prevent.stop="openAddonDetail('full_service')" aria-label="About Full-Service Marketing Support">i</span>
              </span>
              <span class="ob-addon-check"></span>
            </div>
            <span class="ob-addon-price">Scoped and confirmed after your session</span>
            <span class="ob-addon-desc">Complete marketing support beyond SEO — website, ads, campaigns, and automation. We build and manage the full system alongside your growth.</span>
            <span style="display:inline-block;margin-top:6px;font-size:.72rem;color:var(--gold);letter-spacing:.06em;text-transform:uppercase">Most popular for growing businesses</span>
          </label>
        </div>
      </div>

      {{-- Premium path rows — Multi-Location and Agency (always visible, styled as note rows) --}}
      <div class="ob-premium-row">
        <div class="ob-premium-row-body">
          <span class="ob-premium-row-label">Scale tier</span>
          <span class="ob-premium-row-name">Multi-Location / Rollout Support</span>
          <span class="ob-premium-row-desc">Structured deployment across multiple territories and sites &mdash; scoped based on market complexity and page architecture.</span>
        </div>
        <span class="ob-premium-row-badge">Custom review</span>
      </div>

      <div class="ob-premium-row">
        <div class="ob-premium-row-body">
          <span class="ob-premium-row-label">Partner tier</span>
          <span class="ob-premium-row-name">Agency &amp; Partner Deployment</span>
          <span class="ob-premium-row-desc">Partner-level deployment across multiple brands or markets &mdash; reviewed, structured, and activated by arrangement.</span>
        </div>
        <span class="ob-premium-row-badge">By arrangement</span>
      </div>

      <p class="ob-fine" style="margin-top:14px;text-align:center;font-style:italic;color:rgba(168,168,160,.38)">We also partner with agencies and teams who want to integrate this system into their existing workflow.</p>

      {{-- ── Federal Research Credit (informational, optional) ── --}}
      <p class="ob-rd-preline">Some system builds may qualify for federal research credit review.</p>
      <div class="ob-rd-section">
        <span class="ob-rd-eye">Additional Opportunity</span>
        <h3 class="ob-rd-hed">Potential Federal R&D Credit Opportunity</h3>
        <p class="ob-rd-body" style="font-size:.82rem;color:rgba(168,168,160,.78);margin-bottom:14px">Some businesses may qualify for a CPA-led review of eligible development activity.</p>

        <div x-show="rdExpanded" x-cloak>
          <ul class="ob-rd-bullets">
            <li>Custom systems development may qualify</li>
            <li>Automation and AI infrastructure may qualify</li>
            <li>Process experimentation may qualify</li>
          </ul>
          <p class="ob-rd-body">This applies to businesses developing custom systems, automation, or AI-driven infrastructure. Formal eligibility is determined through independent review based on IRS filing criteria.</p>
          <div class="ob-rd-links">
            <a href="https://www.irs.gov/pub/irs-pdf/f6765.pdf" target="_blank" rel="noopener noreferrer" class="ob-rd-link">Form 6765 &mdash; Credit for Increasing Research Activities</a>
            <a href="https://www.irs.gov/instructions/i6765" target="_blank" rel="noopener noreferrer" class="ob-rd-link">IRS Instructions for Form 6765</a>
          </div>
        </div>

        <button type="button" class="ob-rd-toggle" @click="rdExpanded = !rdExpanded">
          <span class="ob-rd-toggle-arrow" :class="{ open: rdExpanded }">›</span>
          <span x-text="rdExpanded ? 'Collapse details' : 'Learn more →'"></span>
        </button>

        <label class="ob-rd-referral" style="margin-top:18px">
          <input type="checkbox" name="rd_referral_interest" value="1"
                 {{ old('rd_referral_interest') ? 'checked' : '' }}>
          <span>Include CPA R&amp;D review referral with my intake</span>
        </label>
        <p style="font-size:.70rem;letter-spacing:.04em;color:rgba(200,168,75,.82);margin-top:6px">Complimentary review available for qualifying businesses.</p>
        <p class="ob-rd-disclaimer">This information is provided for general awareness only and does not constitute tax, legal, or accounting advice.</p>
      </div>

      {{-- ── Decision frame ── --}}
      <div class="ob-decision-frame">
        <p class="ob-decision-hed">How aggressively do you want to move?</p>
        <ul class="ob-tier-ladder">
          <li class="ob-tier-item"><strong>Standard build</strong> — structured rollout, steady position growth over time</li>
          <li class="ob-tier-item"><strong>Accelerated build</strong> — intelligence reports + tracking activate faster, stronger deployment</li>
          <li class="ob-tier-item"><strong>Full expansion path</strong> — paid acquisition layered over organic, maximum velocity from day one</li>
        </ul>
        <p class="ob-decision-sub">Your selections above determine which path you’re on.</p>
      </div>

      {{-- ── Submit ── --}}
      <div class="ob-field" style="margin-bottom:24px">
        <label class="ob-rd-referral">
          <input type="checkbox" name="platform_alignment" value="1" required
                 {{ old('platform_alignment') ? 'checked' : '' }}>
          <span>I confirm this is an actively operating, legitimate business that complies with applicable advertising and platform standards.</span>
        </label>
      </div>

      <p class="ob-activation-note">
        <em>Access is reviewed before activation.</em> This is a position decision.
      </p>
      <p class="ob-activation-note" style="margin-top:8px">We work with legitimate, actively operating businesses ready to grow and invest in their market.</p>
      <p style="font-size:.80rem;color:rgba(168,168,160,.72);text-align:center;margin:16px 0 0;letter-spacing:.03em">If relevant, CPA review can be included with your intake.</p>

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

  {{-- ── Enhancement Detail Panel Overlay ── --}}
  <div class="ob-detail-overlay" :class="{ 'is-open': obDetailOpen }"
       @keydown.escape.window="closeAddonDetail()" aria-hidden="true">
    <div class="ob-detail-backdrop" @click="closeAddonDetail()"></div>
    <div class="ob-detail-panel" role="dialog" aria-modal="true">
      <div class="ob-detail-handle"></div>
      <button type="button" class="ob-detail-close" @click="closeAddonDetail()" aria-label="Close">&times;</button>

      {{-- Website + Growth System Review --}}
      <template x-if="obDetailId === 'website_growth_review'">
        <div>
          <span class="ob-detail-eyebrow">Enhancement — One-time</span>
          <div class="ob-detail-title">Website + Growth System Review</div>
          <div class="ob-detail-price-badge">$150 one-time</div>
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What this does</span>
            <p class="ob-detail-body">Before your strategy session, we do a hands-on review of your website and your current marketing setup — what's working, what's missing, and what to fix first. So when we talk, we can act on real findings immediately.</p>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What you get</span>
            <ul class="ob-detail-list">
              <li>Website review — structure, messaging, speed, and conversion gaps</li>
              <li>Visibility check — where you rank and where you're missing</li>
              <li>Marketing system audit — what's active and what's not working</li>
              <li>Written brief delivered before your session</li>
            </ul>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">Best if</span>
            <p class="ob-detail-body">You're earlier in your growth stage, haven't had an outside review recently, or want clear expert direction before investing in additional work.</p>
          </div>
        </div>
      </template>

      {{-- Local SEO Setup --}}
      <template x-if="obDetailId === 'local_seo'">
        <div>
          <span class="ob-detail-eyebrow">Enhancement — One-time</span>
          <div class="ob-detail-title">Local SEO Setup</div>
          <div class="ob-detail-price-badge">From $199</div>
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What this does</span>
            <p class="ob-detail-body">Gets your business set up correctly in local search — so Google knows exactly who you are, where you operate, and what you do. This is the foundation layer that makes everything else hold.</p>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What you get</span>
            <ul class="ob-detail-list">
              <li>Google Business profile set up and optimized — helps your business appear clearly in Maps and local search</li>
              <li>Business listings built across key directories — keeps your name, address, and phone consistent everywhere online</li>
              <li>Location data structured for search engines — technical setup handled for you, no experience needed</li>
              <li>Account verification and connection support — we help you through it step by step</li>
            </ul>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">Best if</span>
            <p class="ob-detail-body">You're entering a new market, your Google Business profile needs work, or your business information is inconsistent across the web.</p>
          </div>
        </div>
      </template>

      {{-- Campaign Build & Launch --}}
      <template x-if="obDetailId === 'google_ads'">
        <div>
          <span class="ob-detail-eyebrow">Enhancement — Expert Build</span>
          <div class="ob-detail-title">Market Activation Campaign</div>
          <div class="ob-detail-price-badge">Setup from $750&ndash;$1,500+ &middot; Ad spend direct to platform &middot; Management scoped separately</div>
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What this includes</span>
            <p class="ob-detail-body">A professionally built Google Ads account — campaign structure, targeting, ad copy, and conversion tracking — set up to run in your specific market and service area.</p>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What you get</span>
            <ul class="ob-detail-list">
              <li>Campaign structure built around your market and services</li>
              <li>Ad copy written for your business — tested and ready to run</li>
              <li>Location and audience targeting set for your territory</li>
              <li>Conversion tracking connected so you know what's generating leads</li>
              <li>First performance brief included after launch</li>
            </ul>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">How it works</span>
            <p class="ob-detail-body">Setup is a one-time fee. Once live, <strong style="color:var(--ivory);font-weight:400">ad spend goes directly to Google</strong> — it is not part of the setup fee. Ongoing campaign management is priced separately based on spend level.</p>
          </div>
          <div class="ob-detail-section" style="margin-top:14px">
            <span class="ob-detail-section-label">What happens next</span>
            <p class="ob-detail-body">After setup, we review performance and provide brief monthly recommendations. Ongoing management can be added at any time.</p>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">Best if</span>
            <p class="ob-detail-body">You want immediate visibility while your organic position builds — or you're in a competitive market where running both together makes sense.</p>
          </div>
        </div>
      </template>

      {{-- Market Position Tracking --}}
      <template x-if="obDetailId === 'reporting'">
        <div>
          <span class="ob-detail-eyebrow">Enhancement — Monthly</span>
          <div class="ob-detail-title">SEOAIco™ Market Position Tracking</div>
          <div class="ob-detail-price-badge">$349&ndash;$749 / month</div>
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What this does</span>
            <p class="ob-detail-body">Delivers a clear, branded report each month showing where your system is performing — so you don't have to log into dashboards or figure it out yourself.</p>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What you get</span>
            <ul class="ob-detail-list">
              <li>Branded monthly performance report — clean, readable, and shareable</li>
              <li>Keyword rankings and movement across your territory</li>
              <li>Traffic trends and visibility overview</li>
              <li>Position progress notes — what improved and what to watch</li>
            </ul>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">Good to know</span>
            <p class="ob-detail-body">This is a curated monthly report — not a raw automated export. Custom dashboard builds and deeper strategic reporting are available as a separate service.</p>
          </div>
          <div class="ob-detail-section" style="margin-top:14px">
            <span class="ob-detail-section-label">Best if</span>
            <p class="ob-detail-body">You want a reliable record of progress — or you share results with a partner, team, or stakeholders.</p>
          </div>
        </div>
      </template>

      {{-- Market Intelligence Report --}}
      <template x-if="obDetailId === 'territory'">
        <div>
          <span class="ob-detail-eyebrow">Enhancement — One-time</span>
          <div class="ob-detail-title">SEOAIco™ Market Intelligence Report</div>
          <div class="ob-detail-price-badge">$349&ndash;$749+</div>
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What this does</span>
            <p class="ob-detail-body">Before your system deploys, we research your market — who ranks, where they’re winning, and where there are clear gaps your business can take. You get a written report with specific direction, not a generic overview.</p>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What you get</span>
            <ul class="ob-detail-list">
              <li>Competitor visibility map — exactly who ranks and where</li>
              <li>Gap analysis — what your competitors are missing</li>
              <li>Revenue opportunity areas — where your business can gain ground fastest</li>
              <li>Written positioning brief delivered before deployment begins</li>
            </ul>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What happens next</span>
            <p class="ob-detail-body">The report is delivered before your session. Your system deployment is built around it — so we’re targeting real opportunities from day one, not starting blind.</p>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">Best if</span>
            <p class="ob-detail-body">You’re entering a competitive market and want a clear picture of where to focus first.</p>
          </div>
        </div>
      </template>

      {{-- Full-Service Marketing Support --}}
      <template x-if="obDetailId === 'full_service'">
        <div>
          <span class="ob-detail-eyebrow">Enhancement — Custom</span>
          <div class="ob-detail-title">Full-Service Marketing Support</div>
          <div class="ob-detail-price-badge">Custom pricing</div>
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What this does</span>
            <p class="ob-detail-body">Extends your growth system beyond organic search — covering design, ads, content, and marketing operations under a single point of accountability.</p>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What you get</span>
            <ul class="ob-detail-list">
              <li>Website updates, fixes, and ongoing maintenance</li>
              <li>Redesign direction and implementation support</li>
              <li>Ad management — Google Ads, Meta, or both</li>
              <li>Content creation and campaign planning</li>
              <li>Launch materials for new services or locations</li>
              <li>Marketing systems setup and automation</li>
              <li>Structured monthly growth reviews</li>
            </ul>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">Best if</span>
            <p class="ob-detail-body">You want to hand off the marketing side entirely — or you're growing fast and need the infrastructure to keep pace.</p>
          </div>
        </div>
      </template>

      {{-- SEOAIco AI Market Report --}}
      <template x-if="obDetailId === 'ai_market_report'">
        <div>
          <span class="ob-detail-eyebrow">Enhancement — One-time</span>
          <div class="ob-detail-title">SEOAIco™ AI Market Analysis</div>
          <div class="ob-detail-price-badge">$349–$999</div>
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What this does</span>
            <p class="ob-detail-body">A full AI-driven breakdown of your market — who’s winning, where the gaps are, and exactly where your business should focus to generate the most growth.</p>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">What you get</span>
            <ul class="ob-detail-list">
              <li>Where you should rank — priority keyword and topic areas for your market</li>
              <li>Where competitors are winning — visibility, content, and search presence breakdown</li>
              <li>What to build first — a prioritized action map for your deployment</li>
              <li>Where revenue opportunities exist — high-intent search areas with low competition</li>
              <li>Written report delivered before your strategy session</li>
            </ul>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">How it works</span>
            <p class="ob-detail-body">Our AI-driven research process analyzes search data, competitor structure, and market gaps at scale — producing a report that would take a team of analysts days to compile manually.</p>
          </div>
          <div class="ob-detail-section" style="margin-top:14px">
            <span class="ob-detail-section-label">What happens next</span>
            <p class="ob-detail-body">The report is ready before your session. We bring the findings directly into your deployment strategy — so every decision is backed by market data, not guesswork.</p>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">Best if</span>
            <p class="ob-detail-body">You want to go into your session with a full picture of the market — and leave with a clear, researched plan rather than general direction.</p>
          </div>
        </div>
      </template>

      {{-- R&D Detail Panel --}}
      <template x-if="obDetailId === 'rd'">
        <div>
          <span class="ob-detail-eyebrow">Additional Opportunity — Optional</span>
          <div class="ob-detail-title">Federal Research Credit (Form 6765)</div>
          <div class="ob-detail-section" style="margin-top:8px">
            <span class="ob-detail-section-label">Context</span>
            <p class="ob-detail-body">The federal research credit (IRC Section 41) allows qualifying businesses to offset costs related to qualified research activities. Some businesses with technical development, systems implementation, or infrastructure work review whether their activities may qualify.</p>
            <p class="ob-detail-body" style="margin-top:10px">Eligibility depends on how activities are structured, documented, and categorised. A qualified CPA or tax advisor can review your specific situation.</p>
          </div>
          <hr class="ob-detail-divider">
          <div class="ob-detail-section">
            <span class="ob-detail-section-label">IRS Resources</span>
            <div class="ob-detail-rd-links">
              <a href="https://www.irs.gov/pub/irs-pdf/f6765.pdf" target="_blank" rel="noopener noreferrer" class="ob-detail-rd-link">
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" aria-hidden="true"><path d="M2 13h10M7 2v8M4 7l3 4 3-4"/></svg>
                Form 6765 &mdash; Credit for Increasing Research Activities
              </a>
              <a href="https://www.irs.gov/instructions/i6765" target="_blank" rel="noopener noreferrer" class="ob-detail-rd-link">
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" aria-hidden="true"><rect x="2" y="1" width="10" height="12" rx="1.5"/><line x1="4.5" y1="5" x2="9.5" y2="5"/><line x1="4.5" y1="8" x2="9.5" y2="8"/><line x1="4.5" y1="11" x2="7" y2="11"/></svg>
                IRS Instructions for Form 6765
              </a>
            </div>
          </div>
          <hr class="ob-detail-divider">
          <p style="font-size:.76rem;color:rgba(168,168,160,.4);line-height:1.7;margin-bottom:8px">Formal eligibility is determined through independent review based on IRS filing criteria.</p>
          <p style="font-size:.74rem;color:rgba(168,168,160,.44);line-height:1.75;font-style:italic">SEO AI Co™ does not determine eligibility and does not provide tax, legal, or accounting advice. Review with a qualified CPA or tax advisor.</p>
        </div>
      </template>

    </div>
  </div>

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
    leadType: '{{ old('lead_type', 'single_location') }}',

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

    // Enhancement detail panel
    obDetailOpen: false,
    obDetailId: null,
    rdExpanded: false,

    openAddonDetail(id) {
      this.obDetailId = id;
      this.obDetailOpen = true;
      document.body.style.overflow = 'hidden';
    },

    closeAddonDetail() {
      this.obDetailOpen = false;
      setTimeout(() => { this.obDetailId = null; }, 350);
      document.body.style.overflow = '';
    },
  }));
});

// Disable submit on submit to prevent double-post
document.getElementById('ob-form').addEventListener('submit', function() {
  if (typeof gtag === 'function') {
    gtag('event', 'onboarding_submitted', {
      page_location: window.location.href,
      booking_id: (new URLSearchParams(window.location.search)).get('booking') || null,
    });
  }
  const btn = document.getElementById('submit-btn');
  btn.disabled = true;
  btn.textContent = 'Submitting…';
});
</script>
<script>
  if(typeof gtag==='function'){
    gtag('event','onboarding_start',{page_location:window.location.href});
    gtag('event','start_onboarding',{page_location:window.location.href});
    @if($booking && !$booking->consultType?->is_free)
    gtag('event','purchase',{
      transaction_id: '{{ $booking->id }}',
      value: {{ (float) ($booking->consultType?->price ?? 0) }},
      currency: 'USD',
      items: [{ item_name: '{{ addslashes($booking->consultType?->name ?? '') }}', price: {{ (float) ($booking->consultType?->price ?? 0) }}, quantity: 1 }]
    });
    @endif
  }
</script>
@include('components.tm-style')
</body>
</html>
