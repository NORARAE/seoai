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
<title>Confirm Your Entry Session | SEO AI Co™</title>
<meta name="robots" content="noindex,nofollow">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#080808;color:#ede8de;font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 20px}
.box{text-align:center;max-width:480px}
.check{font-size:3rem;margin-bottom:16px}
h1{font-size:1.6rem;font-weight:400;margin-bottom:8px}
p{color:#a8a8a0;font-size:.92rem;line-height:1.7;margin-bottom:16px}
.detail{font-size:.88rem;color:#a8a8a0;margin-bottom:8px}
.detail strong{color:#ede8de;font-weight:400}
.meet{display:inline-block;background:#c8a84b;color:#080808;padding:14px 32px;font-size:.84rem;font-weight:500;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;border-radius:6px;margin:20px 0;transition:background .3s}
.meet:hover{background:#e2c97d}
.home{color:#c8a84b;font-size:.82rem;text-decoration:none;display:inline-block;margin-top:16px}
</style>
@include('partials.clarity')
</head>
<body>
<div class="box">
  <div class="check">&#10003;</div>
  <h1>Entry Reserved</h1>
  <p>System Intake Scheduled for your {{ $booking->consultType->name }} session.</p>
  <div class="detail"><strong>Date:</strong> {{ $booking->preferred_date->format('l, F j, Y') }}</div>
  <div class="detail"><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }} PT</div>
  <div class="detail"><strong>Duration:</strong> {{ $booking->consultType->duration_minutes }} minutes</div>
  @if($booking->google_meet_link)
    <a href="{{ $booking->google_meet_link }}" target="_blank" class="meet">Join Google Meet</a>
  @endif
  <br>
  <a href="/" class="home">&larr; Back to seoaico.com</a>
</div>
@include('components.tm-style')
</body>
</html>
