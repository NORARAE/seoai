<!DOCTYPE html>
<html lang="en">
<head>
<script>document.documentElement.classList.add('js-enabled')</script>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Start Your AI Search Scan — SEO AI Co</title>
<meta name="description" content="Enter your website URL and email. We'll analyze your structure, signals, and AI search coverage in seconds.">
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
         tier: new URLSearchParams(window.location.search).get('tier') || 'basic',
         tierLabels: {
           basic:      'Running initial AI citation scan',
           pro:        'Running full signal analysis',
           structure:  'Mapping structural gaps and site architecture',
           activation: 'Preparing full system activation plan'
         },
         init() {
           if (this.urlError || this.emailError) {
             this.step = this.emailError ? 2 : 0;
           }
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
         submitForm() {
           this.$refs.form.submit();
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
      <p class="se-eye">Analysis Active</p>
      <h2 class="se-hed">Where should we send <em>your results?</em></h2>
      <p class="se-sub">Your scan is in progress. Enter your email to receive your full AI visibility report.</p>

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
      <p class="se-eye">System Ready</p>
      <span class="se-tier-badge" x-text="tierLabels[tier] || tierLabels.basic"></span>
      <p class="se-tier-desc">Your analysis is ready to run.</p>
      <p class="se-ready">Unlock your results to continue.</p>

      <form method="POST" action="{{ route('scan.submit') }}" x-ref="form">
        @csrf
        <input type="hidden" name="url" :value="url">
        <input type="hidden" name="email" :value="email">
        <button type="submit" class="se-btn" style="max-width:480px;margin:0 auto;display:block">Continue to Secure Checkout &rarr;</button>
      </form>
      <p class="se-note">Your <strong style="color:var(--gold-secondary);font-weight:500">$2 diagnostic</strong> runs in seconds. Results carry forward through every level.</p>
    </div>

  </div>
</main>

@include('partials.public-footer')

<script>
const nav = document.getElementById('nav');
if(nav) window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60), {passive:true});
</script>
@include('partials.public-nav-js')
@include('components.booking-modal')
@include('components.tm-style')
</body>
</html>
