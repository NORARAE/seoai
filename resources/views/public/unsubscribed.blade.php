<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex,nofollow">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<title>Unsubscribed — SEO AI Co™</title>
<style>
  body { background: #f5f3ef; font-family: 'Georgia', serif; color: #1a1a1a; margin: 0; padding: 0; }
  .wrap { max-width: 480px; margin: 80px auto; background: #fff; border-top: 3px solid #c8a84b; padding: 48px 40px; text-align: center; }
  .logo { font-size: .8rem; letter-spacing: .2em; text-transform: uppercase; color: #c8a84b; margin-bottom: 28px; }
  h1 { font-size: 1.4rem; font-weight: 400; color: #111; margin: 0 0 16px; }
  p { font-size: .9rem; color: #555; line-height: 1.75; margin: 0 0 12px; }
  .note { font-size: .8rem; color: #aaa; margin-top: 32px; }
  a { color: #c8a84b; text-decoration: none; }
</style>
</head>
<body>
<div class="wrap">
  <div class="logo">SEO AI Co™</div>
  <h1>You've been unsubscribed.</h1>
  <p>{{ $lead->email }} has been removed from follow-up emails.</p>
  <p>You will still receive transactional messages related to confirmed bookings and account activity.</p>
  <p class="note">
    Changed your mind? <a href="mailto:hello@seoaico.com">hello@seoaico.com</a>
  </p>
</div>
@include('components.tm-style')
</body>
</html>
