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
.proc-status a{color:var(--gold);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.3)}
.proc-status a:hover{color:var(--gold-lt)}

/* ── Footer ── */
footer{border-top:1px solid var(--border);padding:28px 48px;display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center}
.footer-copy{font-size:.66rem;letter-spacing:.08em;color:var(--muted)}
.footer-legal{display:flex;gap:20px;padding-top:8px;border-top:1px solid var(--border);width:100%;justify-content:center}
.footer-legal a{font-size:.64rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.footer-legal a:hover{color:var(--gold)}

@media(max-width:768px){
  nav{padding:14px 20px}
  .nav-link{display:none}
  .nav-btn{padding:10px 20px;font-size:.72rem}
  .proc-hero{padding:100px 20px 60px}
  footer{padding:24px 20px}
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
  <div class="proc-spinner" id="spinner"></div>
  <h1 class="proc-h1">Analyzing <em>your site…</em></h1>
  <p class="proc-sub">This usually takes just a few seconds. Your results will appear automatically.</p>
  <p class="proc-url">{{ $scan->url ?? '' }}</p>
  <p class="proc-status" id="statusText">Running checks…</p>
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
  const resultUrl = @json(url('/quick-scan/result'));
  const sessionId = @json($sessionId ?? '');
  const statusEl = document.getElementById('statusText');
  let attempts = 0;
  const maxAttempts = 40; // ~2 minutes

  function pollStatus() {
    if (!scanId) return;
    attempts++;

    fetch('/quick-scan/status?scan_id=' + scanId + '&session_id=' + encodeURIComponent(sessionId))
      .then(r => r.json())
      .then(data => {
        if (data.ready) {
          statusEl.textContent = 'Score ready — loading results…';
          window.location.href = resultUrl + '?session_id=' + encodeURIComponent(sessionId) + '&scan_id=' + scanId;
          return;
        }
        if (data.status === 'error') {
          statusEl.innerHTML = 'Something went wrong with your scan. <a href="' + resultUrl + '?session_id=' + encodeURIComponent(sessionId) + '&scan_id=' + scanId + '">Try loading results</a> or contact <a href="mailto:hello@seoaico.com">hello@seoaico.com</a>.';
          statusEl.classList.add('error');
          document.getElementById('spinner').style.display = 'none';
          return;
        }
        if (attempts >= maxAttempts) {
          statusEl.innerHTML = 'Taking longer than expected. <a href="' + resultUrl + '?session_id=' + encodeURIComponent(sessionId) + '&scan_id=' + scanId + '">Refresh manually</a> or contact <a href="mailto:hello@seoaico.com">hello@seoaico.com</a>.';
          statusEl.classList.add('error');
          document.getElementById('spinner').style.display = 'none';
          return;
        }
        setTimeout(pollStatus, 3000);
      })
      .catch(() => {
        setTimeout(pollStatus, 5000);
      });
  }

  setTimeout(pollStatus, 3000);
</script>
@include('components.tm-style')
</body>
</html>
