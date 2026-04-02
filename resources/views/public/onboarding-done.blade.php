<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Onboarding Complete — SEO AI Co™</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #080808;
  --ivory: #ede8de;
  --muted: #a8a8a0;
  --gold: #c8a84b;
  --gold-lt: #e2c97d;
  --gold-dim: #9a7a30;
  --border: rgba(200,168,75,.10);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
  background: var(--bg);
  color: var(--ivory);
  font-family: 'DM Sans', sans-serif;
  font-weight: 300;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  padding: 56px 24px 72px;
}
.done-wrap { max-width: 520px; width: 100%; position: relative; z-index: 1; }
.done-eye {
  font-size: .64rem;
  letter-spacing: .24em;
  text-transform: uppercase;
  color: var(--gold);
  display: block;
  margin-bottom: 14px;
}
.done-mark {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 1px solid rgba(200,168,75,.22);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  color: var(--gold);
  font-size: 1.2rem;
}
.done-hed {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 4vw, 2.8rem);
  font-weight: 300;
  line-height: 1.1;
  color: var(--ivory);
  margin-bottom: 10px;
}
.done-hed em { font-style: italic; color: var(--gold-lt); }
.done-sub {
  font-size: .92rem;
  color: var(--muted);
  line-height: 1.75;
  margin-bottom: 22px;
  max-width: 440px;
}
.done-email-note {
  font-size: .78rem;
  color: rgba(168,168,160,.52);
  font-style: italic;
  margin-bottom: 16px;
}
.done-home {
  display: block;
  font-size: .74rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--gold-dim);
  text-decoration: none;
  transition: color .25s;
}
.done-home:hover { color: var(--gold); }

/* ── Dot-grid background ── */
.done-bg {
  position: fixed;
  inset: 0;
  pointer-events: none;
  z-index: 0;
  background-image: radial-gradient(circle, rgba(200,168,75,.055) 1px, transparent 1px);
  background-size: 36px 36px;
  animation: bgDrift 60s linear infinite;
}
@keyframes bgDrift {
  0% { background-position: 0 0; }
  100% { background-position: 72px 72px; }
}
/* ── What happens next ── */
.done-next {
  margin-top: 36px;
  padding-top: 28px;
  border-top: 1px solid rgba(200,168,75,.08);
}
.done-next-eye {
  font-size: .64rem;
  letter-spacing: .24em;
  text-transform: uppercase;
  color: var(--gold);
  display: block;
  margin-bottom: 10px;
}
.done-next-body {
  font-size: .96rem;
  color: var(--ivory);
  line-height: 1.6;
  margin-bottom: 12px;
}
.done-assess {
  list-style: none;
  margin-bottom: 14px;
}
.done-assess li {
  font-size: .88rem;
  color: var(--muted);
  line-height: 1.65;
  padding-left: 18px;
  position: relative;
  margin-bottom: 6px;
}
.done-assess li::before {
  content: '\2013';
  position: absolute;
  left: 0;
  color: var(--gold-dim);
}
.done-next-close {
  font-size: .88rem;
  color: rgba(168,168,160,.72);
  font-style: italic;
  font-family: 'Cormorant Garamond', serif;
  line-height: 1.6;
}
/* ── Prepare next step ── */
.done-actions {
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid rgba(200,168,75,.08);
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.done-actions-eye {
  font-size: .64rem;
  letter-spacing: .24em;
  text-transform: uppercase;
  color: var(--gold);
  display: block;
  margin-bottom: 6px;
}
.done-cta-primary {
  display: inline-block;
  background: var(--gold);
  color: #080808;
  font-size: .76rem;
  font-weight: 500;
  letter-spacing: .14em;
  text-transform: uppercase;
  padding: 14px 28px;
  border-radius: 3px;
  text-decoration: none;
  transition: background .25s;
}
.done-cta-primary:hover { background: var(--gold-lt); }
.done-cta-secondary {
  display: inline-block;
  background: transparent;
  border: 1px solid rgba(200,168,75,.28);
  color: var(--gold);
  font-size: .74rem;
  font-weight: 400;
  letter-spacing: .14em;
  text-transform: uppercase;
  padding: 12px 28px;
  border-radius: 3px;
  text-decoration: none;
  transition: border-color .25s, background .25s;
}
.done-cta-secondary:hover { border-color: rgba(200,168,75,.6); background: rgba(200,168,75,.06); }
.done-priority-wrap { margin-top: 8px; }
.done-cta-tertiary {
  display: inline-block;
  font-size: .72rem;
  color: var(--muted);
  letter-spacing: .1em;
  text-transform: uppercase;
  text-decoration: none;
  transition: color .25s;
  margin-bottom: 6px;
}
.done-cta-tertiary:hover { color: var(--gold); }
.done-priority-note {
  font-size: .74rem;
  color: rgba(168,168,160,.45);
  font-style: italic;
  margin-top: 4px;
}
/* ── Future state ── */
.done-future {
  margin-top: 36px;
  padding-top: 22px;
  border-top: 1px solid rgba(200,168,75,.06);
  text-align: center;
}
.done-future-text {
  font-size: .80rem;
  color: rgba(168,168,160,.4);
  letter-spacing: .06em;
  margin-bottom: 8px;
}
.done-future-sub {
  font-size: .66rem;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: rgba(200,168,75,.22);
}

/* ── Additional opportunities link ── */
.done-learn-link {
  display: inline-block;
  margin-top: 12px;
  font-size: .74rem;
  letter-spacing: .1em;
  color: rgba(168,168,160,.42);
  text-decoration: none;
  text-transform: uppercase;
  transition: color .2s;
}
.done-learn-link:hover { color: var(--gold); }

@media (max-width: 520px) {
  body { padding: 36px 20px; }
}
@media (min-width: 768px) {
  .done-hed { font-size: clamp(2.4rem, 4vw, 3.2rem); }
  .done-sub { font-size: 1.02rem; }
  .done-next-body { font-size: 1.04rem; }
  .done-assess li { font-size: .96rem; }
  .done-next-close { font-size: .96rem; }
  .done-email-note { font-size: .86rem; }
  .done-priority-note { font-size: .82rem; }
  .done-future-text { font-size: .88rem; }
  .done-next-eye { font-size: .68rem; }
  .done-actions-eye { font-size: .68rem; }
  .done-cta-primary { font-size: .80rem; padding: 16px 32px; }
  .done-cta-secondary { font-size: .78rem; padding: 14px 28px; }
  .done-cta-tertiary { font-size: .76rem; }
}
</style>
@include('partials.clarity')
</head>
<body>
<div class="done-bg" aria-hidden="true"></div>
<div class="done-wrap">
  <span class="done-eye">Onboarding</span>
  <div class="done-mark">&#10003;</div>
  <h1 class="done-hed">
    @if($alreadySubmitted)
      Already<br><em>received.</em>
    @else
      Intake<br><em>submitted.</em>
    @endif
  </h1>
  <p class="done-sub">
    @if($alreadySubmitted)
      We already have your onboarding on file. Our team will reach out within 1–2 business days.
    @else
      We've received your intake form and business license. Our team will review everything and be in touch within 1–2 business days.
    @endif
  </p>  <p class="done-sub" style="font-size:.82rem;opacity:.7;margin-top:0">The SEO AI Co™ system combines structured content, local relevance, internal link architecture, search signals, and ongoing optimization &mdash; designed to strengthen every signal that drives local visibility.</p>  <p class="done-email-note">Questions? Reach us at <a href="mailto:hello@seoaico.com" style="color:rgba(168,168,160,.7);text-decoration:none">hello@seoaico.com</a></p>
  <a href="{{ url('/') }}" class="done-home">&larr; seoaico.com</a>

  <!-- WHAT HAPPENS NEXT -->
  <div class="done-next">
    <span class="done-next-eye">What happens next</span>
    <p class="done-next-body">Your position is now under review.</p>
    <ul class="done-assess">
      <li>Territory availability</li>
      <li>Existing competition</li>
      <li>Site readiness</li>
      <li>Expansion potential</li>
    </ul>
    <p class="done-next-close">If approved, your market is reserved before activation.</p>
    <p class="done-next-close" style="margin-top:10px;opacity:.75">Review is typically completed within 1–2 business days.</p>
  </div>

  <!-- PREPARE NEXT STEP -->
  <div class="done-actions">
    <span class="done-actions-eye">Prepare your next step</span>
    <a href="/book" class="done-cta-primary">Prepare for your onboarding call &rarr;</a>
    <a href="/#how" class="done-cta-secondary">Review how the system works &rarr;</a>
    <div class="done-priority-wrap">
      <a href="/book" class="done-cta-tertiary">Secure a priority onboarding session &rarr;</a>
      <p class="done-priority-note">Priority sessions ensure faster activation and guided setup.</p>
    </div>
  </div>

  <!-- FUTURE STATE -->
  <div class="done-future">
    <p class="done-future-text">Dashboard access becomes available upon approval and activation.</p>
    <span class="done-future-sub">Preview coming soon</span>
  </div>

  <!-- ADDITIONAL OPPORTUNITIES -->
  <div class="done-next" style="margin-top:44px">
    <span class="done-next-eye">Additional Opportunities</span>
    <p class="done-next-body" style="font-size:.94rem">Some operators may qualify for R&amp;D tax credits related to technical and digital development. See official IRS resources: <a href="https://www.irs.gov/instructions/i6765" target="_blank" rel="noopener noreferrer" style="color:var(--gold);text-decoration:none">Form 6765 Instructions</a> or <a href="https://www.irs.gov/pub/irs-pdf/f6765.pdf" target="_blank" rel="noopener noreferrer" style="color:var(--gold);text-decoration:none">Form 6765 PDF</a>. This is not tax advice — consult a qualified CPA.</p>
  </div>

</div>
</body>
</html>
