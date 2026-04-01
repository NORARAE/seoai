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
<title>Book a Consult — SEOAIco</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root{--bg:#080808;--deep:#0b0b0b;--gold:#c8a84b;--gold-lt:#e2c97d;--ivory:#ede8de;--muted:#a8a8a0;--border:#1a1a1a}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:18px;scroll-behavior:smooth}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.85;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 20px}
.book-logo{margin-bottom:32px;text-decoration:none;display:inline-flex;align-items:baseline}
.book-logo .l-seo{font-weight:300;font-size:1.2rem;letter-spacing:.06em;color:#fff}
.book-logo .l-ai{color:var(--gold);font-weight:500;font-size:1.35rem;letter-spacing:.02em}
.book-logo .l-co{font-weight:300;font-size:1rem;letter-spacing:.04em;color:#fff}
h1{font-family:'Cormorant Garamond',serif;font-size:2.2rem;font-weight:400;text-align:center;margin-bottom:8px}
h1 em{font-style:italic;color:var(--gold)}
.book-sub{color:var(--muted);font-size:.92rem;text-align:center;margin-bottom:32px}
</style>
</head>
<body>
  <a href="/" class="book-logo">
    <span class="l-seo">SEO</span><span class="l-ai">AI</span><span class="l-co">co</span>
  </a>
  <h1><a href="/" style="color:inherit;text-decoration:none">Book a <em>Consult</em></a></h1>
  <p class="book-sub">Choose a session below and pick a time that works.</p>

  @if(request('payment') === 'cancelled')
  <div style="background:#1a0a0a;border:1px solid rgba(200,80,80,.35);border-radius:8px;padding:14px 20px;margin-bottom:24px;font-size:.88rem;color:#f0a0a0;text-align:center;max-width:480px;margin-left:auto;margin-right:auto">
    Your payment was not completed &mdash; your spot was not reserved. Please select a session below to try again.
  </div>
  @endif

  @include('components.booking-modal')

  <script>
  document.addEventListener('alpine:init', () => {
    // Auto-open modal on standalone book page
    setTimeout(() => {
      window.dispatchEvent(new CustomEvent('open-booking', {detail: null}));
    }, 300);
  });
  </script>
<script>
  if(typeof gtag==='function'){gtag('event','view_book',{page_location:window.location.href});}
</script>
</body>
</html>
