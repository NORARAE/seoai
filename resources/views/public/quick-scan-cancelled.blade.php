<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Scan Cancelled — SEO AI Co™</title>
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;1,300&family=DM+Sans:wght@300;400&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#080808;--gold:#c8a84b;--gold-lt:#d9bc6e;--ivory:#ede8de;--muted:rgba(168,168,160,.78);--border:rgba(200,168,75,.09)}
html{font-size:18px}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:40px 24px;-webkit-font-smoothing:antialiased}
.logo{text-decoration:none;display:flex;align-items:baseline;gap:1px;margin-bottom:48px;opacity:.55}
.logo-seo{font-family:'DM Sans',sans-serif;font-size:1.38rem;font-weight:300;letter-spacing:-.02em;color:var(--ivory)}
.logo-ai{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:400;letter-spacing:.02em;color:var(--gold);font-style:italic;margin:0 1px}
.logo-co{font-family:'DM Sans',sans-serif;font-size:1.18rem;font-weight:300;color:rgba(168,168,160,.65)}
.eyebrow{font-size:.66rem;letter-spacing:.26em;text-transform:uppercase;color:rgba(200,168,75,.5);margin-bottom:16px}
h1{font-family:'Cormorant Garamond',serif;font-size:clamp(1.8rem,4vw,2.8rem);font-weight:300;color:var(--ivory);margin-bottom:14px;line-height:1.15}
h1 em{font-style:italic;color:var(--gold)}
p{font-size:.96rem;color:var(--muted);max-width:440px;margin:0 auto 32px;line-height:1.75}
.btn{display:inline-block;background:var(--gold);color:#080808;font-family:'DM Sans',sans-serif;font-size:.8rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;padding:16px 40px;text-decoration:none;transition:background .3s;margin-bottom:14px}
.btn:hover{background:var(--gold-lt)}
.btn-ghost{display:block;font-size:.74rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(168,168,160,.4);text-decoration:none;transition:color .2s}
.btn-ghost:hover{color:var(--muted)}
</style>
@include('partials.clarity')
</head>
<body>
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <p class="eyebrow">Payment Cancelled</p>
  <h1>Your scan wasn't<br><em>completed.</em></h1>
  <p>No charge was made. Head back and try again whenever you're ready — it only takes 60 seconds.</p>
  <a href="{{ route('quick-scan.show') }}{{ $scan ? '?url=' . urlencode($scan->url_input ?? $scan->url) . '&email=' . urlencode($scan->email) : '' }}" class="btn">Try Again</a>
  <a href="/" class="btn-ghost">Return Home</a>
<script>
(function(){
  fetch('/api/v1/track',{
    method:'POST',
    headers:{'Content-Type':'application/json','Accept':'application/json'},
    body:JSON.stringify({event:'checkout_cancelled',metadata:{flow:'quick_scan',source_page:'quick_scan_cancelled',scan_id:'{{ $scan?->id ?? '' }}'}})
  }).catch(function(){});
})();
</script>
@include('components.tm-style')
</body>
</html>
