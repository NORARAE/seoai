<!DOCTYPE html>
<html lang="en">
<head>
<script>document.documentElement.classList.add('js-enabled')</script>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Start Your AI Search Scan — SEO AI Co</title>
<meta name="description" content="Enter your website URL and email. We'll analyze your structure, signals, and AI search coverage in seconds.">
<link rel="canonical" href="{{ url('/scan/start') }}">
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
@include('partials.design-system')
@include('partials.public-nav-css')

/* ── Scan Entry ── */
.scan-start{min-height:100vh;display:flex;flex-direction:column}
.scan-hero{
  flex:1;display:flex;align-items:center;justify-content:center;
  padding:120px var(--wrap-pad) 80px;
}
.scan-hero-inner{
  max-width:560px;width:100%;text-align:center;
  position:relative;
}

/* ── Step transitions ── */
.se-step{
  transition:opacity var(--transition-smooth) var(--ease-out),
             transform var(--transition-smooth) var(--ease-out);
}
.se-step[x-cloak]{display:none}

/* ── Typography reuse ── */
.se-eye{font-size:.62rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold-secondary);margin-bottom:16px}
.se-hed{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.7rem,3.2vw,2.5rem);font-weight:300;
  color:var(--ivory);line-height:1.15;margin-bottom:14px;
}
.se-hed em{font-style:italic;color:var(--gold-lt)}
.se-sub{
  font-size:.86rem;color:var(--muted);line-height:1.78;
  max-width:460px;margin:0 auto 36px;
}

/* ── Input ── */
.se-input-wrap{max-width:480px;margin:0 auto;text-align:left}
.se-label{
  display:block;font-size:.7rem;letter-spacing:.12em;text-transform:uppercase;
  color:var(--gold-secondary);margin-bottom:8px;font-weight:500;
}
.se-input{
  width:100%;padding:16px 20px;
  background:rgba(14,13,9,.8);border:1px solid rgba(200,168,75,.12);
  border-radius:3px;color:var(--ivory);font-size:1rem;
  font-family:'DM Sans',sans-serif;font-weight:300;
  transition:border-color .3s,box-shadow .3s;outline:none;
}
.se-input::placeholder{color:rgba(168,168,160,.35)}
.se-input:focus{
  border-color:rgba(200,168,75,.35);
  box-shadow:0 0 0 3px rgba(200,168,75,.08);
}
.se-error{font-size:.74rem;color:#e05c5c;margin-top:6px}
.se-note{
  text-align:center;font-size:.74rem;color:rgba(168,168,160,.42);
  margin-top:20px;line-height:1.7;letter-spacing:.02em;
}

/* ── Submit button (reuse sf-submit / btn-primary pattern) ── */
.se-btn{
  width:100%;padding:16px 36px;margin-top:16px;
  background:linear-gradient(180deg,#d8be72,#c8a84b);color:#080808;
  font-size:.74rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;
  border:1px solid rgba(226,201,125,.4);border-radius:2px;
  cursor:pointer;transition:all .3s;position:relative;overflow:hidden;
  font-family:'DM Sans',sans-serif;
}
.se-btn::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,.12),transparent);transform:translateX(-100%);transition:transform .6s}
.se-btn:hover{
  background:linear-gradient(180deg,#e0c97e,#d4b45a);
  border-color:rgba(226,201,125,.65);
  box-shadow:0 8px 32px rgba(200,168,75,.18);
  transform:translateY(-1px);
}
.se-btn:hover::before{transform:translateX(100%)}
.se-btn:disabled{opacity:.5;cursor:not-allowed;transform:none}
.se-btn:disabled::before{display:none}
.se-btn.is-securing{
  opacity:.9;
  box-shadow:0 8px 30px rgba(200,168,75,.16);
}
.se-btn.is-securing::before{
  display:block;
  background:linear-gradient(90deg,rgba(255,255,255,0),rgba(255,245,210,.34),rgba(255,255,255,0));
  animation:se-secure-sweep 1.05s ease-in-out infinite;
}
@keyframes se-secure-sweep{
  0%{transform:translateX(-110%)}
  100%{transform:translateX(120%)}
}

/* ── Analysis animation ── */
.se-analysis{max-width:420px;margin:0 auto}
.se-spinner{
  width:48px;height:48px;margin:0 auto 28px;
  border:2px solid rgba(200,168,75,.1);
  border-top-color:var(--gold);border-radius:50%;
  animation:se-spin 1s linear infinite;
}
@keyframes se-spin{to{transform:rotate(360deg)}}

.se-progress{
  height:2px;background:rgba(200,168,75,.08);
  border-radius:2px;overflow:hidden;margin-bottom:24px;
}
.se-progress-bar{
  height:100%;width:0;
  background:linear-gradient(90deg,var(--gold-dim),var(--gold));
  border-radius:2px;
  transition:width .4s var(--ease-out);
}

.se-status-line{
  font-size:.82rem;color:var(--muted);
  min-height:1.6em;
  transition:opacity .25s;
}

/* Gold pulse accent */
.se-pulse{
  width:6px;height:6px;border-radius:50%;
  background:var(--gold);margin:20px auto 0;
  animation:se-glow 1.5s ease-in-out infinite;
}
@keyframes se-glow{
  0%,100%{opacity:.3;box-shadow:0 0 4px rgba(200,168,75,.1)}
  50%{opacity:1;box-shadow:0 0 16px rgba(200,168,75,.3)}
}

/* ── Tier confirmation ── */
.se-tier-badge{
  display:inline-block;padding:6px 16px;
  font-size:.62rem;letter-spacing:.2em;text-transform:uppercase;
  color:var(--gold);
  border:1px solid rgba(200,168,75,.18);border-radius:20px;
  margin-bottom:20px;
}
.se-tier-desc{
  font-size:.92rem;color:var(--ivory);line-height:1.72;
  margin-bottom:8px;
}
.se-ready{
  font-size:.84rem;color:var(--muted);line-height:1.72;
  margin-bottom:32px;
}

.se-live-state{
  display:inline-flex;align-items:center;justify-content:center;gap:8px;
  font-size:.62rem;letter-spacing:.2em;text-transform:uppercase;
  color:var(--gold-secondary);margin-bottom:14px;
}
.se-live-dot{
  width:9px;height:9px;border-radius:50%;
  background:#d4af37;box-shadow:0 0 0 1px rgba(212,175,55,.25);
  animation:se-live-pulse 1.6s ease-in-out infinite;
}
@keyframes se-live-pulse{
  0%,100%{opacity:.4;box-shadow:0 0 8px rgba(212,175,55,.14)}
  50%{opacity:1;box-shadow:0 0 16px rgba(212,175,55,.34)}
}

.se-header-shimmer{
  width:100%;max-width:460px;height:2px;margin:0 auto 16px;
  background:rgba(200,168,75,.07);border-radius:2px;overflow:hidden;
}
.se-header-shimmer-bar{
  display:block;height:100%;width:38%;
  background:linear-gradient(90deg,rgba(200,168,75,0),rgba(212,175,55,.55),rgba(200,168,75,0));
  animation:se-shimmer 2.9s ease-in-out infinite;
}
@keyframes se-shimmer{
  0%{transform:translateX(-120%)}
  100%{transform:translateX(300%)}
}

.se-activity-line{
  min-height:1.6em;
  font-size:.78rem;
  color:rgba(222,208,165,.84);
  opacity:.95;
  transition:opacity .35s ease;
  margin:0 auto 14px;
}
.se-activity-line.is-fading{opacity:.35}
.se-activity-line.is-transfer{opacity:.74}

.se-transfer-wrap{min-height:2.8em;margin:0 0 14px}
.se-transfer-detail{
  display:inline-flex;align-items:center;justify-content:center;gap:8px;
  font-size:.72rem;color:rgba(211,196,156,.78);line-height:1.45;
}
.se-transfer-fallback{
  margin:6px 0 0;
  font-size:.7rem;
  color:rgba(199,187,156,.62);
  line-height:1.45;
}
.se-lock-icon{
  position:relative;
  display:inline-block;
  width:10px;
  height:8px;
  border:1px solid rgba(214,181,95,.72);
  border-radius:2px;
  box-sizing:border-box;
}
.se-lock-icon::before{
  content:'';
  position:absolute;
  width:6px;
  height:5px;
  left:1px;
  top:-6px;
  border:1px solid rgba(214,181,95,.72);
  border-bottom:none;
  border-radius:8px 8px 0 0;
  box-sizing:border-box;
}

.se-cta-reinforce{
  margin-top:10px;
  text-align:center;
  font-size:.72rem;
  color:rgba(201,188,151,.72);
  letter-spacing:.02em;
}

/* ── Mobile ── */
@media(max-width:600px){
  .scan-hero{padding:100px var(--wrap-pad) 60px}
  .scan-hero-inner{max-width:100%}
  .se-input{font-size:.92rem;padding:14px 16px}
}

@include('partials.public-nav-mobile-css')

@media(max-width:900px){
  #nav{padding:14px 20px}#nav.stuck{padding:10px 20px}
  .nav-link{display:none}
  .nav-btn{display:none}
  .nav-hamburger{display:flex}
}
</style>
</head>
<body class="scan-start">
@include('partials.public-nav', ['showHamburger' => true])

@if(session('flow_message'))
<div style="background:rgba(200,168,75,.08);border-bottom:1px solid rgba(200,168,75,.18);padding:14px 24px;text-align:center;font-size:.82rem;letter-spacing:.04em;color:rgba(200,168,75,.9)">
  {{ session('flow_message') }}
</div>
@endif

<main class="scan-hero">
  <div class="scan-hero-inner"
       x-data="{
         step: 0,
         url: '{{ old('url', '') }}',
         email: '{{ old('email', '') }}',
         urlError: '{{ $errors->first('url') }}',
         emailError: '{{ $errors->first('email') }}',
         analysisLine: '',
         analysisProgress: 0,
         liveActivityLines: [
           'Mapping domain structure\u2026',
           'Evaluating AI extraction signals\u2026',
           'Checking entity clarity\u2026',
           'Detecting ranking constraints\u2026'
         ],
         liveActivityIndex: 0,
         liveActivityFading: false,
         liveActivityTimer: null,
         autoAdvanceTimer: null,
         hasAdvanced: false,
         transferDelayTimer: null,
         showTransferFallback: false,
         autoAdvanceDelayMs: 1600,
         tier: new URLSearchParams(window.location.search).get('tier') || 'basic',
         tierLabels: {
           basic:      'Running initial AI citation scan',
           pro:        'Running full signal analysis',
           structure:  'Mapping structural gaps and site architecture',
           activation: 'Preparing your guided execution plan'
         },
         init() {
           if (this.urlError || this.emailError) {
             this.step = this.emailError ? 2 : 0;
           }
           this.startLiveActivity();
           this.emitTrackingEvent('scan_start_viewed');
           this.$watch('step', (val) => {
             if (val === 3) {
               this.scheduleAutoAdvance();
               return;
             }
             this.clearAutoAdvance();
             this.clearTransferFallback();
           });
           if (this.step === 3) this.scheduleAutoAdvance();
         },
         emitTrackingEvent(eventName, payload = {}) {
           const data = { event: eventName, ...payload };

           // Hook into whichever tracking layer already exists.
           if (Array.isArray(window.dataLayer)) {
             window.dataLayer.push(data);
           }
           if (typeof window.gtag === 'function') {
             window.gtag('event', eventName, payload);
           }
         },
         startLiveActivity() {
           this.liveActivityTimer = window.setInterval(() => {
             this.liveActivityFading = true;
             window.setTimeout(() => {
               this.liveActivityIndex = (this.liveActivityIndex + 1) % this.liveActivityLines.length;
               this.liveActivityFading = false;
             }, 180);
           }, 1700);
         },
         normalizeUrl() {
           let v = this.url.trim();
           if (v && !/^https?:\/\//i.test(v)) v = 'https://' + v;
           this.url = v;
         },
         validateUrl() {
           this.normalizeUrl();
           try {
             if (!this.url) { this.urlError = 'Please enter a website URL.'; return false; }
             new URL(this.url);
             this.urlError = '';
             return true;
           } catch(e) {
             this.urlError = 'Please enter a valid URL (e.g. https://yourdomain.com).';
             return false;
           }
         },
         validateEmail() {
           let v = this.email.trim();
           if (!v) { this.emailError = 'Please enter your email.'; return false; }
           if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) { this.emailError = 'Please enter a valid email address.'; return false; }
           this.emailError = '';
           return true;
         },
         goToAnalysis() {
           if (!this.validateUrl()) return;
           this.step = 1;
           this.runAnalysis();
         },
         async runAnalysis() {
           const lines = [
             'Mapping domain structure\u2026',
             'Detecting signal coverage\u2026',
             'Checking citation readiness\u2026',
             'Analyzing competitors\u2026'
           ];
           for (let i = 0; i < lines.length; i++) {
             this.analysisLine = lines[i];
             this.analysisProgress = ((i + 1) / lines.length) * 100;
             await new Promise(r => setTimeout(r, 500));
           }
           await new Promise(r => setTimeout(r, 300));
           this.step = 2;
         },
         goToConfirm() {
           if (!this.validateEmail()) return;
           this.step = 3;
         },
         scheduleAutoAdvance() {
           this.clearAutoAdvance();
           if (this.hasAdvanced) return;
           this.autoAdvanceTimer = window.setTimeout(() => {
             if (this.hasAdvanced) return;
             this.emitTrackingEvent('scan_start_auto_advance');
             this.startTransfer('auto');
           }, this.autoAdvanceDelayMs);
         },
         clearAutoAdvance() {
           if (this.autoAdvanceTimer) {
             window.clearTimeout(this.autoAdvanceTimer);
             this.autoAdvanceTimer = null;
           }
         },
         clearTransferFallback() {
           if (this.transferDelayTimer) {
             window.clearTimeout(this.transferDelayTimer);
             this.transferDelayTimer = null;
           }
           this.showTransferFallback = false;
         },
         startTransfer(mode) {
           if (this.hasAdvanced) return;
           this.hasAdvanced = true;
           this.clearAutoAdvance();
           this.clearTransferFallback();
           this.liveActivityFading = true;
           this.emitTrackingEvent('scan_start_transfer_started', { mode });

           this.transferDelayTimer = window.setTimeout(() => {
             this.showTransferFallback = true;
           }, 1200);

           window.setTimeout(() => {
             try {
               window._scanStartSubmit(this.$refs.form);
             } catch (e) {
               // Keep manual fallback usable if submit binding fails.
               this.hasAdvanced = false;
               this.clearTransferFallback();
             }
           }, 320);
         },
         submitForm() {
           if (this.hasAdvanced) return;
           this.emitTrackingEvent('scan_start_manual_advance');
           this.startTransfer('manual');
         }
       }"
  >

    {{-- ═══ STEP 0 — ENTRY ═══ --}}
    <div class="se-step" x-show="step === 0" x-transition:enter="se-step" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="se-step" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
      <p class="se-eye">AI Search Diagnostic</p>
      <h1 class="se-hed">Enter your website.<br>See how AI sees <em>your business.</em></h1>
      <p class="se-sub">We analyze your structure, signals, and visibility across AI-powered search systems.</p>

      <div class="se-input-wrap">
        <label class="se-label" for="se-url">Website URL</label>
        <input class="se-input" type="url" id="se-url" x-model="url"
               placeholder="yourdomain.com"
               @keydown.enter.prevent="goToAnalysis()"
               autofocus>
        <template x-if="urlError"><p class="se-error" x-text="urlError"></p></template>
        <button class="se-btn" @click="goToAnalysis()">Analyze My Site &rarr;</button>
        <p class="se-note">No spam. Just your results and next steps.</p>
      </div>
    </div>

    {{-- ═══ STEP 1 — ANALYSIS ANIMATION ═══ --}}
    <div class="se-step" x-show="step === 1" x-cloak x-transition:enter="se-step" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
      <div class="se-analysis">
        <div class="se-spinner"></div>
        <h2 class="se-hed" style="font-size:clamp(1.3rem,2.4vw,1.8rem);margin-bottom:20px">Initializing analysis&hellip;</h2>
        <div class="se-progress">
          <div class="se-progress-bar" :style="'width:' + analysisProgress + '%'"></div>
        </div>
        <p class="se-status-line" x-text="analysisLine"></p>
        <div class="se-pulse"></div>
      </div>
    </div>

    {{-- ═══ STEP 2 — EMAIL CAPTURE ═══ --}}
    <div class="se-step" x-show="step === 2" x-cloak x-transition:enter="se-step" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="se-step" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
      <p class="se-eye">Scan Setup Ready</p>
      <h2 class="se-hed">Where should we send <em>your results?</em></h2>
      <p class="se-sub">Your scan session is prepared. Enter your email to access your full AI visibility results inside your dashboard.</p>

      <div class="se-input-wrap">
        <label class="se-label" for="se-email">Email Address</label>
        <input class="se-input" type="email" id="se-email" x-model="email"
               placeholder="you@company.com"
               @keydown.enter.prevent="goToConfirm()">
        <template x-if="emailError"><p class="se-error" x-text="emailError"></p></template>
        <button class="se-btn" @click="goToConfirm()">Continue &rarr;</button>
        <p class="se-note">No spam. Just your results and next steps.</p>
      </div>
    </div>

    {{-- ═══ STEP 3 — TIER CONFIRMATION + SUBMIT ═══ --}}
    <div class="se-step" x-show="step === 3" x-cloak x-transition:enter="se-step" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
      <p class="se-live-state"><span class="se-live-dot" aria-hidden="true"></span>LIVE SCAN IN PROGRESS</p>
      <div class="se-header-shimmer" aria-hidden="true"><span class="se-header-shimmer-bar"></span></div>
      <span class="se-tier-badge" x-text="tierLabels[tier] || tierLabels.basic"></span>
      <p class="se-tier-desc">Your system analysis is initialized.</p>
      <p class="se-ready">Unlock your results to see where AI is failing to select your site.</p>
      <p class="se-activity-line" :class="{ 'is-fading': liveActivityFading && !hasAdvanced, 'is-transfer': hasAdvanced }" x-text="hasAdvanced ? 'Transferring to secure checkout\u2026' : liveActivityLines[liveActivityIndex]"></p>
      <div class="se-transfer-wrap" aria-live="polite">
        <p class="se-note" style="margin-top:0;margin-bottom:0" x-show="!hasAdvanced" x-text="'Preparing secure checkout\u2026'"></p>
        <p class="se-transfer-detail" x-show="hasAdvanced" x-cloak>
          <span class="se-lock-icon" aria-hidden="true"></span>
          <span>Your scan session is locked and moving into protected payment.</span>
        </p>
        <p class="se-transfer-fallback" x-show="hasAdvanced && showTransferFallback" x-cloak>Still connecting to secure checkout\u2026</p>
      </div>

      <form method="POST" action="{{ route('scan.submit') }}" x-ref="form" @submit.prevent="submitForm()">
        @csrf
        @if(config('services.turnstile.site_key'))
        <input type="hidden" name="cf-turnstile-response" id="ss-cf-turnstile-response" value="">
        @endif
        <input type="hidden" name="url" :value="url">
        <input type="hidden" name="email" :value="email">
        <button type="submit" class="se-btn" :class="{ 'is-securing': hasAdvanced }" :disabled="hasAdvanced" style="max-width:480px;margin:0 auto;display:block" x-text="hasAdvanced ? 'Securing Checkout…' : 'Unlock Full Scan →'"></button>
      </form>
      @if(config('services.turnstile.site_key'))
      {{-- Widget div outside the form — prevents Cloudflare's auto-injected input from duplicating ours --}}
      <div id="cf-turnstile-scanstart" aria-hidden="true" style="display:none"></div>
      @endif
      <p class="se-note" style="margin-top:10px">Secure checkout powered by Stripe &bull; Takes 10 seconds</p>
      <p class="se-cta-reinforce">Includes: signal map, ranking gaps, and prioritized fixes</p>
      <p class="se-note">Your <strong style="color:var(--gold-secondary);font-weight:500">$2 diagnostic</strong> runs in seconds. Results carry forward through every level.</p>
      <p class="se-note" style="margin-top:8px;font-size:.58rem;opacity:.55">By continuing you agree to our <a href="{{ route('terms') }}" style="color:inherit" target="_blank">Terms</a> and <a href="{{ route('refund-policy') }}" style="color:inherit" target="_blank">Refund Policy</a>. The $2 scan is non-refundable once processed.</p>
    </div>

  </div>
</main>

@include('partials.public-footer')

<script>
const nav = document.getElementById('nav');
if(nav) window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60), {passive:true});

@if(config('services.turnstile.site_key'))
// Cloudflare Turnstile — widget is outside the form to prevent duplicate hidden inputs
var _ssTsWidgetId = null;
var _ssTsVerified = false;
function _ssRenderTs() {
  if (window.turnstile && !_ssTsWidgetId) {
    _ssTsWidgetId = turnstile.render('#cf-turnstile-scanstart', {
      sitekey: @json(config('services.turnstile.site_key')),
      size: 'invisible', theme: 'dark', execution: 'execute',
      callback: function(token) {
        var inp = document.getElementById('ss-cf-turnstile-response');
        if (inp) inp.value = token;
        _ssTsVerified = true;
      },
      'error-callback': function() {
        // Fail-open
        _ssTsVerified = true;
      },
    });
  }
}
if (window.turnstile) { _ssRenderTs(); }
else {
  var _prevTsLoad2 = window.onTurnstileLoad;
  window.onTurnstileLoad = function() { if (_prevTsLoad2) _prevTsLoad2(); _ssRenderTs(); };
}
window._scanStartSubmit = function(form) {
  if (_ssTsVerified) {
    // Token already written (or fail-open) — submit immediately
    form.submit();
    return;
  }
  if (_ssTsWidgetId !== null) {
    // Execute challenge; poll until verified, then submit
    turnstile.execute(_ssTsWidgetId);
    // Poll every 50ms until callback fires (or 8s timeout for fail-open)
    var _poll = setInterval(function() {
      if (_ssTsVerified) {
        clearInterval(_poll);
        form.submit();
      }
    }, 50);
    // Safety timeout — if not verified within 8s, fail-open and submit anyway
    setTimeout(function() { clearInterval(_poll); if (!_ssTsVerified) { _ssTsVerified = true; form.submit(); } }, 8000);
    return;
  }
  // Widget not ready — fail-open
  form.submit();
};
@else
window._scanStartSubmit = function(form) { form.submit(); };
@endif
</script>
@include('partials.public-nav-js')
@include('components.booking-modal')
@include('components.tm-style')
@include('partials.turnstile-script')
</body>
</html>
