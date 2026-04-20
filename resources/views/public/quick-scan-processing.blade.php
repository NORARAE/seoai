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
<title>Analyzing Your Site… — SEO AI Co™</title>
<meta name="robots" content="noindex">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;--deep:#0b0b0b;--card:#0e0d09;--border:rgba(200,168,75,.09);
  --gold:#c8a84b;--gold-lt:#d9bc6e;--gold-dim:rgba(200,168,75,.32);
  --ivory:#ede8de;--muted:rgba(168,168,160,.78);
}
html{font-size:18px;scroll-behavior:smooth}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden}

/* ── Nav ── */
nav{position:fixed;top:0;left:0;right:0;z-index:200;display:flex;align-items:center;justify-content:space-between;padding:28px 64px;border-bottom:1px solid transparent;transition:all .4s}
nav.stuck{background:rgba(8,8,8,.95);backdrop-filter:blur(16px);border-color:var(--border);padding:16px 64px}
.logo{text-decoration:none;display:flex;align-items:baseline;gap:1px;flex-shrink:0}
.logo-seo{font-family:'DM Sans',sans-serif;font-size:1.38rem;font-weight:300;letter-spacing:-.02em;color:var(--ivory)}
.logo-ai{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:400;letter-spacing:.02em;color:var(--gold);font-style:italic;margin:0 1px}
.logo-co{font-family:'DM Sans',sans-serif;font-size:1.18rem;font-weight:300;color:rgba(168,168,160,.65)}
.nav-right{display:flex;align-items:center;gap:28px}
.nav-link{font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.nav-link:hover{color:var(--gold)}
.nav-btn{font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;color:var(--bg);background:var(--gold);padding:12px 28px;text-decoration:none;transition:background .3s;display:inline-flex;align-items:center}
.nav-btn:hover{background:var(--gold-lt)}

/* ── Processing hero ── */
.proc-hero{
  min-height:100vh;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:120px 24px 80px;
  text-align:center;
  position:relative;overflow:hidden;
}
.proc-hero::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse 80% 60% at 50% 40%,rgba(200,168,75,.06) 0%,transparent 65%);
  pointer-events:none;
}
.proc-eyebrow{
  font-size:.66rem;letter-spacing:.28em;text-transform:uppercase;
  color:rgba(200,168,75,.6);margin-bottom:20px;
}
.proc-h1{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.8rem,4vw,2.8rem);
  font-weight:300;line-height:1.15;
  color:var(--ivory);margin-bottom:16px;
}
.proc-h1 em{font-style:italic;color:var(--gold)}
.proc-sub{
  font-size:clamp(.92rem,1.5vw,1.05rem);
  color:rgba(168,168,160,.7);
  max-width:460px;margin:0 auto 40px;
  line-height:1.7;
}
.proc-url{
  font-size:.82rem;color:rgba(168,168,160,.4);
  font-family:'DM Sans',sans-serif;font-weight:300;
  letter-spacing:.04em;margin-bottom:40px;
  max-width:460px;overflow-wrap:break-word;
}

/* ── Spinner ── */
.proc-spinner{
  width:56px;height:56px;
  border:2px solid rgba(200,168,75,.12);
  border-top-color:var(--gold);
  border-radius:50%;
  animation:procSpin 1.1s linear infinite;
  margin-bottom:32px;
}
@keyframes procSpin{to{transform:rotate(360deg)}}

/* ── Status text ── */
.proc-status{
  font-size:.76rem;letter-spacing:.12em;text-transform:uppercase;
  color:rgba(168,168,160,.4);
  transition:opacity .3s;
}
.proc-status.error{
  color:#c47878;
  letter-spacing:.06em;
  text-transform:none;
  font-size:.88rem;
  margin-top:12px;
}
.proc-reassurance{
  font-size:.72rem;
  letter-spacing:.04em;
  color:rgba(168,168,160,.38);
  margin-top:14px;
  max-width:380px;
}
.proc-payment{
  font-size:.62rem;
  letter-spacing:.16em;
  text-transform:uppercase;
  color:rgba(100,185,130,.88);
  margin:0 0 20px;
}
.proc-activity{
  font-size:.6rem;
  letter-spacing:.14em;
  text-transform:uppercase;
  color:rgba(200,168,75,.44);
  margin:8px 0 0;
  min-height:1.1em;
  transition:opacity .5s;
}
.proc-status a{color:var(--gold);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.3)}
.proc-status a:hover{color:var(--gold-lt)}

.proc-fallback{
  display:none;
  max-width:620px;
  margin:26px auto 0;
  padding:18px 18px 16px;
  border:1px solid rgba(200,168,75,.2);
  background:rgba(200,168,75,.03);
  border-radius:10px;
}
.proc-fallback.on{display:block}
.proc-fallback-title{
  font-family:'Cormorant Garamond',serif;
  font-size:1.1rem;
  color:var(--ivory);
  margin-bottom:8px;
}
.proc-fallback-text{
  font-size:.86rem;
  color:rgba(168,168,160,.9);
  line-height:1.7;
  margin-bottom:14px;
}
.proc-fallback-actions{
  display:flex;
  justify-content:center;
  align-items:center;
  flex-wrap:wrap;
  gap:10px;
}
.proc-fallback-btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  font-size:.72rem;
  letter-spacing:.11em;
  text-transform:uppercase;
  text-decoration:none;
  padding:10px 16px;
  border:1px solid rgba(200,168,75,.26);
  color:var(--gold-lt);
  background:rgba(200,168,75,.04);
  transition:background .2s, color .2s, border-color .2s;
}
.proc-fallback-btn:hover{
  background:rgba(200,168,75,.1);
  border-color:rgba(200,168,75,.45);
  color:#f2e8cd;
}

/* ── Footer ── */
footer{border-top:1px solid var(--border);padding:28px 48px;display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center}
.footer-copy{font-size:.66rem;letter-spacing:.08em;color:var(--muted)}
.footer-legal{display:flex;gap:20px;padding-top:8px;border-top:1px solid var(--border);width:100%;justify-content:center}
.footer-legal a{font-size:.64rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.footer-legal a:hover{color:var(--gold)}

@media(max-width:900px){
  nav{padding:14px 16px;max-width:100vw}
  nav.stuck{padding:10px 16px}
  .nav-right{gap:10px;flex-shrink:0}
  .nav-link{display:none}
  .nav-btn{display:none}
  .logo-seo{font-size:1.2rem}
  .logo-ai{font-size:1.36rem}
  .logo-co{font-size:1.02rem}
}

@media(max-width:768px){
  nav{padding:12px 14px}
  nav.stuck{padding:9px 14px}
  .proc-hero{padding:88px 18px 56px}
  .proc-sub{margin:0 auto 28px;line-height:1.55}
  .proc-url{margin-bottom:30px}
  footer{padding:22px 18px}
}

@media(max-width:412px){
  nav{padding:10px 12px}
  nav.stuck{padding:8px 12px}
  .logo-seo{font-size:1.08rem}
  .logo-ai{font-size:1.24rem}
  .logo-co{font-size:.94rem}
}
</style>
@include('partials.clarity')
</head>
<body>

<!-- Nav -->
<nav id="nav">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <div class="nav-right">
    <a href="/pricing" class="nav-link">Pricing</a>
    <a href="/book" class="nav-btn">Get Started</a>
  </div>
</nav>

<!-- Processing state -->
<section class="proc-hero">
  <p class="proc-eyebrow">AI Citation Quick Scan</p>
  <p class="proc-payment">✓ Payment received — your scan is running now</p>
  <div class="proc-spinner" id="spinner"></div>
  <h1 class="proc-h1" id="procHeading">Starting <em>your scan…</em></h1>
  <p class="proc-sub">Usually completes in 10–30 seconds. Your results will appear automatically.</p>
  <p class="proc-url">{{ $scan->url ?? '' }}</p>
  <p class="proc-status" id="statusText">Starting scan…</p>
  <p class="proc-activity" id="procActivity"></p>
  <p class="proc-reassurance">You don't need to do anything — we'll take you straight to your report when it's ready.</p>
  <div class="proc-fallback" id="processingFallback" role="status" aria-live="polite">
    <p class="proc-fallback-title">This is taking a bit longer than expected.</p>
    <p class="proc-fallback-text">Hold tight — complex sites can take up to a minute. You can also continue in your dashboard and results will appear automatically.</p>
    <div class="proc-fallback-actions">
      <a class="proc-fallback-btn" href="{{ url('/dashboard#ai-scans') }}">Go to Dashboard →</a>
    </div>
  </div>
</section>

<!-- Footer -->
<footer>
  <a href="{{ url('/') }}" class="logo" style="opacity:.5">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; 2026 SEO AI Co™</span>
  <nav class="footer-legal">
    <a href="{{ route('privacy') }}">Privacy</a>
    <a href="{{ route('terms') }}">Terms</a>
    <a href="/pricing">Pricing</a>
  </nav>
</footer>

<script>
  const nav = document.getElementById('nav');
  if(nav) window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60));

  // Poll for scan completion every 3 seconds
  const scanId = @json($scan->id ?? null);
  const resultUrl = @json($resultUrl ?? url('/quick-scan/result'));
  const sessionId = @json($sessionId ?? '');
  const token = @json($token ?? '');
  const statusEl = document.getElementById('statusText');
  const headingEl = document.getElementById('procHeading');
  const fallbackEl = document.getElementById('processingFallback');
  const spinnerEl = document.getElementById('spinner');
  let attempts = 0;
  const maxAttempts = 40; // ~2 minutes at 3s intervals

  // Stage headings and status text to convey progression
  const stages = [
    { minAttempt: 0,  heading: 'Starting <em>your scan…</em>',    status: 'Starting scan…' },
    { minAttempt: 2,  heading: 'Analyzing <em>your site…</em>',    status: 'Analyzing your site…' },
    { minAttempt: 6,  heading: 'Building <em>your report…</em>',   status: 'Building your report…' },
    { minAttempt: 11, heading: 'Almost <em>there…</em>',           status: 'Finalizing results…' },
  ];
  function applyStage() {
    for (let i = stages.length - 1; i >= 0; i--) {
      if (attempts >= stages[i].minAttempt) {
        if (headingEl) headingEl.innerHTML = stages[i].heading;
        if (statusEl && !statusEl.classList.contains('error')) statusEl.textContent = stages[i].status;
        break;
      }
    }
  }

  // Live-feel activity text cycle
  const scanUrl = @json($scan->url ?? '');
  const scanDomain = (function(){
    try{return new URL(scanUrl).hostname;}catch(e){return scanUrl;}
  }());
  const activityItems = [
    'Reviewing: ' + scanDomain,
    'Structure signals detected',
    'Entity signals identified',
    'Citation pattern analysis active',
    'Location signals mapping\u2026',
    'Service keywords evaluated',
    'AI visibility score calculating\u2026',
  ];
  let activityIdx = 0;
  const activityEl = document.getElementById('procActivity');
  function cycleActivity() {
    if (!activityEl || activityEl.classList.contains('error')) return;
    activityEl.style.opacity = '0';
    setTimeout(function(){
      activityEl.textContent = activityItems[activityIdx % activityItems.length];
      activityIdx++;
      activityEl.style.opacity = '1';
    }, 320);
  }
  if (activityEl) {
    activityEl.textContent = activityItems[0];
    activityIdx = 1;
    setInterval(cycleActivity, 4500);
  }

  function buildFallbackUrl() {
    if (resultUrl.indexOf('/quick-scan/result') !== -1) {
      let url = resultUrl + '?scan_id=' + scanId;
      if (token) {
        url += '&token=' + encodeURIComponent(token);
      }
      if (sessionId) {
        url += '&session_id=' + encodeURIComponent(sessionId);
      }
      return url;
    }

    if (resultUrl.indexOf('?') === -1) {
      if (token) {
        return resultUrl + '?token=' + encodeURIComponent(token);
      }
      return resultUrl + '?session_id=' + encodeURIComponent(sessionId);
    }

    if (token) {
      return resultUrl + '&token=' + encodeURIComponent(token);
    }

    return resultUrl + '&session_id=' + encodeURIComponent(sessionId);
  }

  function pollStatus() {
    if (!scanId) return;
    attempts++;
    applyStage();

    let statusUrl = '/quick-scan/status?scan_id=' + scanId;
    if (sessionId) {
      statusUrl += '&session_id=' + encodeURIComponent(sessionId);
    }
    if (token) {
      statusUrl += '&token=' + encodeURIComponent(token);
    }

    fetch(statusUrl)
      .then(r => r.json())
      .then(data => {
        if (data.ready) {
          statusEl.textContent = 'Score ready — loading results…';
          if (data.report_url) {
            window.location.href = data.report_url;
          } else {
            window.location.href = buildFallbackUrl();
          }
          return;
        }
        if (data.status === 'error') {
          statusEl.innerHTML = 'Something went wrong with your scan. <a href="' + buildFallbackUrl() + '">Try loading results</a> or contact <a href="mailto:hello@seoaico.com">hello@seoaico.com</a>.';
          statusEl.classList.add('error');
          if (spinnerEl) spinnerEl.style.display = 'none';
          if (fallbackEl) fallbackEl.classList.add('on');
          return;
        }
        if (attempts >= maxAttempts) {
          statusEl.innerHTML = 'Taking longer than expected. <a href="' + buildFallbackUrl() + '">Check this report link</a> or contact <a href="mailto:hello@seoaico.com">hello@seoaico.com</a>.';
          statusEl.classList.add('error');
          if (spinnerEl) spinnerEl.style.display = 'none';
          if (fallbackEl) fallbackEl.classList.add('on');
          return;
        }
        setTimeout(pollStatus, 3000);
      })
      .catch(() => {
        if (attempts >= maxAttempts) {
          statusEl.innerHTML = 'We are still processing your scan. <a href="' + buildFallbackUrl() + '">Open report link</a> or contact <a href="mailto:hello@seoaico.com">hello@seoaico.com</a>.';
          statusEl.classList.add('error');
          if (spinnerEl) spinnerEl.style.display = 'none';
          if (fallbackEl) fallbackEl.classList.add('on');
          return;
        }
        setTimeout(pollStatus, 5000);
      });
  }

  setTimeout(pollStatus, 3000);
</script>
@include('components.tm-style')
</body>
</html>
