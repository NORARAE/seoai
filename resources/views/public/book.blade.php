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
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Strategy Session — SEO AI Co™</title>
<meta name="description" content="A focused market opportunity session — not a sales call. We identify where your business can expand and how to take it.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root{--bg:#080808;--deep:#0b0b0b;--gold:#c8a84b;--gold-lt:#e2c97d;--ivory:#ede8de;--muted:#a8a8a0;--border:#1a1a1a;--section-gap:96px}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:18px;scroll-behavior:smooth}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.85}

/* ── Nav ── */
.bk-nav{display:flex;align-items:center;justify-content:space-between;padding:24px 40px;border-bottom:1px solid var(--border)}
.bk-logo{text-decoration:none;display:inline-flex;align-items:baseline;gap:0}
.bk-logo .l-seo{font-weight:300;font-size:1.1rem;letter-spacing:.06em;color:#fff}
.bk-logo .l-ai{color:var(--gold);font-weight:500;font-size:1.25rem;letter-spacing:.02em}
.bk-logo .l-co{font-weight:300;font-size:.95rem;letter-spacing:.04em;color:#fff}
.bk-nav-back{font-size:.8rem;color:var(--muted);text-decoration:none;letter-spacing:.04em;transition:color .2s}
.bk-nav-back:hover{color:var(--gold)}

/* ── Layout ── */
.bk-wrap{max-width:760px;margin:0 auto;padding:0 32px}

/* ── Hero ── */
.bk-hero{padding:var(--section-gap) 0 80px;text-align:center}
.bk-hero h1{font-family:'Cormorant Garamond',serif;font-size:clamp(2rem,5vw,3.2rem);font-weight:400;line-height:1.2;letter-spacing:-.01em;margin-bottom:24px}
.bk-hero h1 em{font-style:italic;color:var(--gold)}
.bk-hero .hero-sub{font-size:1rem;color:var(--muted);max-width:520px;margin:0 auto 40px;line-height:1.9}
.bk-notice{background:#1a0a0a;border:1px solid rgba(200,80,80,.35);border-radius:8px;padding:14px 20px;margin-bottom:40px;font-size:.85rem;color:#f0a0a0;text-align:center}
.bk-cta-btn{display:inline-flex;align-items:center;gap:10px;background:var(--gold);color:#080808;font-family:'DM Sans',sans-serif;font-size:.88rem;font-weight:500;letter-spacing:.08em;text-transform:uppercase;text-decoration:none;padding:17px 44px;border-radius:4px;border:none;cursor:pointer;transition:background .2s,transform .15s}
.bk-cta-btn:hover{background:var(--gold-lt);transform:translateY(-1px)}
.bk-cta-note{font-size:.75rem;color:var(--muted);margin-top:14px;letter-spacing:.02em}

/* ── Divider ── */
.bk-divider{border:none;border-top:1px solid var(--border);margin:0}

/* ── Section ── */
.bk-section{padding:var(--section-gap) 0}
.bk-section-label{font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);margin-bottom:20px;font-weight:400}
.bk-section h2{font-family:'Cormorant Garamond',serif;font-size:clamp(1.6rem,3.5vw,2.2rem);font-weight:400;line-height:1.3;margin-bottom:24px}
.bk-section p{font-size:.97rem;color:var(--muted);line-height:1.95;max-width:620px}

/* ── What You Get ── */
.bk-gets{list-style:none;margin-top:32px;display:flex;flex-direction:column;gap:16px}
.bk-gets li{display:flex;align-items:flex-start;gap:16px;font-size:.9rem;color:var(--ivory);line-height:1.7}
.bk-gets li::before{content:'→';color:var(--gold);flex-shrink:0;font-size:1rem;margin-top:2px}

/* ── Promise ── */
.bk-promise{padding:64px 0;text-align:center}
.bk-promise blockquote{font-family:'Cormorant Garamond',serif;font-size:clamp(1.3rem,3vw,1.75rem);font-weight:400;font-style:italic;color:var(--ivory);line-height:1.5;max-width:580px;margin:0 auto;border-left:2px solid var(--gold);padding-left:28px;text-align:left}

/* ── Footer ── */
.bk-footer{padding:32px 40px;border-top:1px solid var(--border);text-align:center;font-size:.75rem;color:var(--muted);letter-spacing:.02em}
.bk-footer a{color:var(--muted);text-decoration:none}
.bk-footer a:hover{color:var(--gold)}

/* ── Booking entry section ── */
#book-now{background:var(--deep);border-top:1px solid var(--border);scroll-margin-top:80px}
.bk-entry-intro{max-width:640px;margin:0 auto;padding:80px 32px 48px;text-align:center}
.bk-entry-intro .bk-section-label{margin-bottom:14px}
.bk-entry-intro h2{font-family:'Cormorant Garamond',serif;font-size:clamp(1.5rem,3.5vw,2rem);font-weight:400;line-height:1.3;margin-bottom:18px}
.bk-entry-intro p{font-size:.9rem;color:var(--muted);line-height:1.9;max-width:500px;margin:0 auto}

@media(max-width:600px){
  .bk-nav{padding:20px 24px}
  .bk-wrap{padding:0 24px}
  :root{--section-gap:64px}
  .bk-promise blockquote{padding-left:20px}
  .bk-entry-intro{padding:56px 24px 36px}
}
</style>
@include('partials.clarity')
</head>
<body>

  {{-- Nav --}}
  <nav class="bk-nav">
    <a href="/" class="bk-logo">
      <span class="l-seo">SEO</span><span class="l-ai">AI</span><span class="l-co">co</span>
    </a>
    <a href="/" class="bk-nav-back">← Back</a>
  </nav>

  <main>
    <div class="bk-wrap">

      {{-- Hero --}}
      <section class="bk-hero">
        <h1>This is a <em>market opportunity session.</em></h1>
        <p class="hero-sub">We map your territory, identify coverage gaps, and give you a direct answer on whether there&rsquo;s a real case to expand &mdash; and how to move on it.</p>
        <p class="hero-sub" style="margin-top:-18px;font-size:.9rem">We look at territory availability, competitive position, signal architecture, and programmatic AI SEO coverage across your service area. This is not basic SEO advice &mdash; this is market-level visibility strategy.</p>

        @if(request('payment') === 'cancelled')
        <div class="bk-notice">
          Your payment was not completed &mdash; your spot was not reserved. Select a session below to try again.
        </div>
        @endif

        <button class="bk-cta-btn" onclick="openSession()">Reserve Your Strategy Session &rarr;</button>
        <p class="bk-cta-note">Available to service-based businesses in select markets.</p>
      </section>

      <hr class="bk-divider">

      {{-- What This Is --}}
      <section class="bk-section">
        <p class="bk-section-label">What This Is</p>
        <h2>A focused analysis. A straight answer.</h2>
        <p>Before anyone builds anything, we need to know what is actually there. The Strategy Session is a focused examination of your local market — where the opportunity exists, what your current footprint looks like, and whether there is a real case to expand.</p>
        <p style="margin-top:16px">We do not pitch programs on this session. We tell you the truth. If the opportunity is there, you will leave knowing exactly what it is and how to pursue it. If it is not, you will know that too.</p>        <p style="margin-top:16px;font-size:.88rem;color:var(--muted)">Most businesses don&rsquo;t need more SEO &mdash; they need the right signals working together. &rarr; <a href="/how-it-works" style="color:var(--gold);text-decoration:none">See how the system works</a></p>      </section>

      <hr class="bk-divider">

      {{-- What You Get --}}
      <section class="bk-section">
        <p class="bk-section-label">What You Leave With</p>
        <h2>Clarity, not a proposal.</h2>
        <ul class="bk-gets">
          <li>A clear picture of your target territory and its current coverage gaps</li>
          <li>An honest assessment of where your business stands in local search</li>
          <li>A direct answer — is there real, actionable opportunity here or not</li>
          <li>If yes: a recommended path forward with specifics, not generalities</li>
        </ul>
      </section>

    </div>

    {{-- Promise --}}
    <div class="bk-wrap">
      <div class="bk-promise">
        <blockquote>If there&rsquo;s no real opportunity, we&rsquo;ll tell you. If there is &mdash; we&rsquo;ll show you exactly how to move on it.</blockquote>
      </div>
    </div>

    {{-- Booking entry section --}}
    <section id="book-now">
      <div class="bk-entry-intro">
        <p class="bk-section-label">Reserve Your Session</p>
        <h2>Choose where you want to start.</h2>
        <p>All our work begins with a real conversation about your market. Pick the session that fits where you are.</p>
      </div>
      @include('components.booking-modal', ['disableOverlayDismiss' => true, 'panelMode' => true])
    </section>

  </main>

  <footer class="bk-footer">
    <div style="margin-bottom:8px;font-size:.82rem"><strong style="color:var(--ivory)">SEO AI Co™</strong> &nbsp;&middot;&nbsp; Programmatic AI SEO Systems</div>
    <div style="margin-bottom:8px"><a href="mailto:hello@seoaico.com">hello@seoaico.com</a></div>
    <p style="font-size:.63rem;color:rgba(168,168,160,.38);max-width:440px;margin:0 auto 12px;line-height:1.5">SEO AI Co™ and associated systems, processes, and methodologies are proprietary and may not be reproduced without permission.</p>
    <div><a href="/">seoaico.com</a> &nbsp;&mdash;&nbsp; <a href="{{ route('privacy') }}">Privacy</a> &nbsp;&mdash;&nbsp; <a href="{{ route('terms') }}">Terms</a></div>
  </footer>

  <script>
  function openSession() {
    window.dispatchEvent(new CustomEvent('open-booking', {detail: null}));
    document.getElementById('book-now').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  </script>
  <script>
  if(typeof gtag==='function'){gtag('event','view_book',{page_location:window.location.href});}
  </script>
</body>
</html>
