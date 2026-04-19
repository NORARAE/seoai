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
<title>System Readout Unavailable | SEO AI Co</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#080808;--card:#0f0d08;--border:rgba(200,168,75,.2);--gold:#c8a84b;--ivory:#ede8de;--muted:#a8a8a0}
body{min-height:100vh;background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;display:flex;align-items:center;justify-content:center;padding:28px}
.wrap{width:100%;max-width:760px;border:1px solid var(--border);border-radius:14px;padding:30px;background:linear-gradient(160deg,#15120b,#0d0b07 70%);box-shadow:0 18px 44px rgba(0,0,0,.45)}
.kicker{font-size:.62rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.74);margin-bottom:10px}
h1{font-family:'Cormorant Garamond',serif;font-size:clamp(1.7rem,3vw,2.3rem);line-height:1.14;margin-bottom:10px}
.body{font-size:.92rem;color:#c2bcab;line-height:1.75;max-width:640px}
.actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:22px}
.btn{display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:10px 16px;border-radius:9px;text-decoration:none;text-transform:uppercase;letter-spacing:.11em;font-size:.7rem;font-weight:600;transition:all .2s ease}
.btn-primary{background:var(--gold);color:#080808;border:1px solid rgba(200,168,75,.5)}
.btn-primary:hover{filter:brightness(1.08)}
.btn-secondary{background:rgba(200,168,75,.08);color:#e8ddc3;border:1px solid rgba(200,168,75,.28)}
.btn-secondary:hover{background:rgba(200,168,75,.15)}
.btn-muted{background:transparent;color:var(--muted);border:1px solid rgba(168,168,160,.26)}
.btn-muted:hover{color:#d8d2bf;border-color:rgba(200,168,75,.3)}
.meta{margin-top:14px;font-size:.72rem;color:#9f9887;letter-spacing:.06em}
</style>
@include('partials.clarity')
</head>
<body>
  <main class="wrap" role="main">
    <p class="kicker">Readout Routing</p>
    <h1>System Readout Unavailable</h1>
    <p class="body">This scan could not be fully loaded from the current path. Reopen from dashboard memory or refresh system state.</p>
    <div class="actions">
      <a class="btn btn-primary" href="{{ route('app.dashboard') }}">Return to Dashboard</a>
      <a class="btn btn-secondary" href="{{ $latestScanUrl ?? route('quick-scan.show') }}">Reopen Latest Scan</a>
      <a class="btn btn-muted" href="{{ $refreshUrl ?? url()->current() }}">Refresh Readout</a>
    </div>
    @if(isset($scan) && $scan)
      <p class="meta">Reference: {{ $scan->publicScanId() }} · {{ $scan->domain() }}</p>
    @endif
  </main>
@include('components.tm-style')
</body>
</html>
