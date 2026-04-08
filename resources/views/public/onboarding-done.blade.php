<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Onboarding Complete — You’re All Set | SEO AI Co™</title>
<meta name="robots" content="noindex,nofollow">
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
/* ── Expectation note ── */
.done-expect {
  margin-top: 28px;
  padding: 18px 20px;
  border: 1px solid rgba(200,168,75,.07);
  border-radius: 3px;
  background: rgba(200,168,75,.025);
}
.done-expect-label {
  font-size: .62rem;
  letter-spacing: .22em;
  text-transform: uppercase;
  color: var(--gold-dim);
  display: block;
  margin-bottom: 8px;
}
.done-expect-body {
  font-size: .82rem;
  color: rgba(168,168,160,.55);
  line-height: 1.7;
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

/* ── Share section ── */
.done-share {
  margin-top: 44px;
  padding-top: 28px;
  border-top: 1px solid rgba(200,168,75,.08);
  position: relative;
}
.done-share-eye {
  font-size: .64rem;
  letter-spacing: .24em;
  text-transform: uppercase;
  color: rgba(200,168,75,.45);
  display: block;
  margin-bottom: 14px;
}
.done-share-hed {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.2rem, 3vw, 1.55rem);
  font-weight: 300;
  color: var(--ivory);
  line-height: 1.25;
  margin-bottom: 8px;
}
.done-share-sub {
  font-size: .84rem;
  color: rgba(168,168,160,.62);
  line-height: 1.7;
  margin-bottom: 22px;
}
.done-share-btns {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.done-share-primary {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: transparent;
  border: 1px solid rgba(200,168,75,.32);
  color: var(--gold);
  font-family: 'DM Sans', sans-serif;
  font-size: .76rem;
  font-weight: 500;
  letter-spacing: .14em;
  text-transform: uppercase;
  padding: 14px 28px;
  border-radius: 3px;
  cursor: pointer;
  transition: border-color .3s, background .3s, box-shadow .3s;
  overflow: hidden;
  align-self: flex-start;
}
.done-share-primary:hover {
  border-color: rgba(200,168,75,.70);
  background: rgba(200,168,75,.06);
  box-shadow: 0 0 18px rgba(200,168,75,.10);
}
.done-share-primary:active { transform: scale(.98); }
.done-share-pulse {
  position: absolute;
  inset: 0;
  border-radius: 3px;
  pointer-events: none;
  animation: sharePulse 2.8s ease-out infinite;
  border: 1px solid rgba(200,168,75,.18);
}
@keyframes sharePulse {
  0% { transform: scale(1); opacity: .7; }
  70% { transform: scale(1.06); opacity: 0; }
  100% { transform: scale(1.06); opacity: 0; }
}
.done-share-copy {
  background: none;
  border: none;
  padding: 0;
  font-family: 'DM Sans', sans-serif;
  font-size: .72rem;
  letter-spacing: .08em;
  color: rgba(168,168,160,.44);
  cursor: pointer;
  text-transform: uppercase;
  transition: color .2s;
  align-self: flex-start;
}
.done-share-copy:hover { color: rgba(168,168,160,.78); }
.done-share-copy.copied { color: var(--gold); }
.done-share-micro {
  margin-top: 14px;
  font-size: .68rem;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: rgba(168,168,160,.28);
  line-height: 1.6;
}
/* ── Share node canvas ── */
.done-share-canvas {
  position: absolute;
  top: 0; right: 0;
  width: 120px; height: 80px;
  pointer-events: none;
  opacity: .35;
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
@media (max-width: 520px) {
  body { padding: 36px 20px; }
  .done-share-primary { align-self: stretch; justify-content: center; }
  .done-share-copy { align-self: center; }
}
</style>
@include('partials.clarity')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="canonical" href="{{ url('/onboarding/done') }}">
<meta name="robots" content="noindex, nofollow">
</head>
<body>
<div x-data="{ howOpen: false }">
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
  </p>  <p class="done-sub" style="font-size:.82rem;color:rgba(168,168,160,.80);margin-top:0">The SEO AI Co™ system combines structured content, local relevance, internal link architecture, search signals, and ongoing optimization &mdash; designed to strengthen every signal that drives local visibility and market dominance.</p>  <p style="font-size:.70rem;color:rgba(168,168,160,.68);margin-top:0;text-align:center;max-width:460px;margin-left:auto;margin-right:auto;line-height:1.65">SEO AI Co™ is a programmatic SEO and market intelligence system for operators competing in active markets. This platform maps local search visibility and identifies expansion opportunities.</p>  <p class="done-email-note">Questions? Reach us at <a href="mailto:hello@seoaico.com" style="color:rgba(168,168,160,.7);text-decoration:none">hello@seoaico.com</a></p>
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

  <!-- EXPECTATION PROTECTION -->
  <div class="done-expect">
    <span class="done-expect-label">How this works</span>
    <p class="done-expect-body">Market positioning and indexing require sustained signal development. Results compound over time — early interruption resets the compounding cycle rather than pausing it. The system operates in structured 4-month cycles for this reason: build, stabilization, expansion, and growth. Early interruption prevents full system performance. This is built for operators who understand that market position is built and held, not switched on.</p>
  </div>

  <!-- OPTIONAL ACCELERATION -->
  <div class="done-actions">
    <span class="done-actions-eye">Optional: accelerate your review</span>
    <a href="/book" class="done-cta-primary">Secure a Priority Review Session &rarr;</a>
    <p style="font-size:.82rem;color:rgba(168,168,160,.85);line-height:1.65;margin-top:2px">Priority sessions provide faster activation and guided setup.</p>
    <button type="button" class="done-cta-tertiary" style="margin-top:6px;background:none;border:none;cursor:pointer;font-family:'DM Sans',sans-serif" @click="howOpen = true">Review how the system works &rarr;</button>
  </div>

  <!-- HOW IT WORKS OVERLAY -->
  <div x-show="howOpen" @keydown.escape.window="howOpen = false" x-cloak
       style="position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;padding:24px">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,.86)" @click="howOpen = false"></div>
    <div style="position:relative;z-index:1;background:#0e0e0e;border:1px solid rgba(200,168,75,.14);border-radius:8px;padding:36px 32px;max-width:500px;width:100%;max-height:80vh;overflow-y:auto">
      <button type="button" @click="howOpen = false"
              style="position:absolute;top:16px;right:16px;background:none;border:none;color:rgba(168,168,160,.6);font-size:1.1rem;cursor:pointer;line-height:1" aria-label="Close">&times;</button>
      <p style="font-size:.64rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold-dim);margin-bottom:14px">How the system works</p>
      <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:400;color:var(--ivory);margin-bottom:18px;line-height:1.2">Structured deployment.<br><em style="color:var(--gold-lt)">Compounding results.</em></h2>
      <p style="font-size:.88rem;color:rgba(168,168,160,.88);line-height:1.8;margin-bottom:18px">The system operates in structured 4-month deployment cycles. Each phase builds on the last — content, signals, architecture, and authority compound progressively.</p>
      <ul style="list-style:none;display:flex;flex-direction:column;gap:10px;margin-bottom:20px">
        <li style="font-size:.86rem;color:var(--ivory);line-height:1.6;padding-left:18px;position:relative"><span style="position:absolute;left:0;color:var(--gold-dim)">&ndash;</span>Build &mdash; structural content and signal foundation</li>
        <li style="font-size:.86rem;color:var(--ivory);line-height:1.6;padding-left:18px;position:relative"><span style="position:absolute;left:0;color:var(--gold-dim)">&ndash;</span>Stabilization &mdash; indexing, coverage, and compounding</li>
        <li style="font-size:.86rem;color:var(--ivory);line-height:1.6;padding-left:18px;position:relative"><span style="position:absolute;left:0;color:var(--gold-dim)">&ndash;</span>Expansion &mdash; territory depth and competitive positioning</li>
        <li style="font-size:.86rem;color:var(--ivory);line-height:1.6;padding-left:18px;position:relative"><span style="position:absolute;left:0;color:var(--gold-dim)">&ndash;</span>Growth &mdash; sustained market authority</li>
      </ul>
      <p style="font-size:.80rem;color:rgba(168,168,160,.72);font-style:italic;line-height:1.7">Early interruption resets the compounding cycle. This is built for operators who understand that market position is built and held, not switched on.</p>
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

  <!-- SHARE EARLY ACCESS -->
  <div class="done-share" id="doneShare">
    <canvas class="done-share-canvas" id="shareNodeCanvas" aria-hidden="true"></canvas>
    <span class="done-share-eye">Pass it on</span>
    <h2 class="done-share-hed">Know someone who should<br>see this early?</h2>
    <p class="done-share-sub">Share access with your circle — before this expands further.</p>
    <div class="done-share-btns">
      <button class="done-share-primary" id="shareBtn" type="button" aria-label="Share early access">
        <span class="done-share-pulse" aria-hidden="true"></span>
        Share Early Access &nbsp;&rarr;
      </button>
      <button class="done-share-copy" id="copyLinkBtn" type="button">Copy Invite Link</button>
    </div>
    <p class="done-share-micro">Access expands through qualified operators — not mass release.</p>
  </div>

</div>{{-- /x-data --}}

<script>
(function(){
  var SHARE_URL = 'https://seoaico.com';
  var SHARE_TEXT = "I just found a new AI-driven system that expands your visibility across every city you serve.\n\nIt\u2019s not typical SEO \u2014 it\u2019s structured expansion.\n\nThought you might want early access:\n" + SHARE_URL;

  /* ── Primary: native share with clipboard fallback ── */
  var shareBtn = document.getElementById('shareBtn');
  if (shareBtn) {
    shareBtn.addEventListener('click', function() {
      if (navigator.share) {
        navigator.share({
          title: 'SEO AI Co\u2122 \u2014 Early Market Expansion Access',
          text: SHARE_TEXT,
          url: SHARE_URL
        }).catch(function(){});
      } else {
        copyToClipboard(SHARE_TEXT, shareBtn, 'Copied!');
      }
    });
  }

  /* ── Secondary: copy invite link ── */
  var copyBtn = document.getElementById('copyLinkBtn');
  if (copyBtn) {
    copyBtn.addEventListener('click', function() {
      copyToClipboard(SHARE_URL, copyBtn, 'Copied \u2013');
    });
  }

  function copyToClipboard(text, el, label) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(function() {
        flashLabel(el, label);
      }).catch(function() {
        fallbackCopy(text, el, label);
      });
    } else {
      fallbackCopy(text, el, label);
    }
  }

  function fallbackCopy(text, el, label) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.cssText = 'position:fixed;left:-9999px;top:-9999px;opacity:0';
    document.body.appendChild(ta);
    ta.focus(); ta.select();
    try { document.execCommand('copy'); flashLabel(el, label); } catch(e) {}
    document.body.removeChild(ta);
  }

  function flashLabel(el, label) {
    var orig = el.textContent;
    el.textContent = label;
    el.classList.add('copied');
    setTimeout(function() {
      el.textContent = orig;
      el.classList.remove('copied');
    }, 2200);
  }

  /* ── Mini node canvas ── */
  var canvas = document.getElementById('shareNodeCanvas');
  if (canvas) {
    var ctx = canvas.getContext('2d');
    var W = canvas.width  = canvas.offsetWidth  || 120;
    var H = canvas.height = canvas.offsetHeight || 80;
    var nodes = Array.from({length: 7}, function() {
      return {
        x: Math.random() * W,
        y: Math.random() * H,
        vx: (Math.random() - .5) * .28,
        vy: (Math.random() - .5) * .28,
        r: 1.2 + Math.random() * 1.2
      };
    });
    function drawNodes() {
      ctx.clearRect(0, 0, W, H);
      for (var i = 0; i < nodes.length; i++) {
        var n = nodes[i];
        n.x += n.vx; n.y += n.vy;
        if (n.x < 0 || n.x > W) n.vx *= -1;
        if (n.y < 0 || n.y > H) n.vy *= -1;
        for (var j = i + 1; j < nodes.length; j++) {
          var m = nodes[j];
          var dx = n.x - m.x, dy = n.y - m.y;
          var dist = Math.sqrt(dx*dx + dy*dy);
          if (dist < 55) {
            ctx.beginPath();
            ctx.moveTo(n.x, n.y);
            ctx.lineTo(m.x, m.y);
            ctx.strokeStyle = 'rgba(200,168,75,' + (.22 * (1 - dist/55)) + ')';
            ctx.lineWidth = .6;
            ctx.stroke();
          }
        }
        ctx.beginPath();
        ctx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(200,168,75,.55)';
        ctx.fill();
      }
      requestAnimationFrame(drawNodes);
    }
    drawNodes();
  }
})();
</script>
</body>
</html>
