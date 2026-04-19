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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
  :root{
    --bg:#080808;--card:#0e0d09;--border:rgba(200,168,75,.09);
    --gold:#c8a84b;--gold-lt:#e2c97d;--gold-dim:rgba(200,168,75,.4);
    --ivory:#ede8de;--muted:rgba(168,168,160,.72);
  }
  body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.75;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 24px}
  .wrap{max-width:480px;width:100%;background:var(--card);border:1px solid var(--border);border-top:2px solid var(--gold);padding:48px 40px;text-align:center}
  .logo{display:inline-flex;align-items:baseline;text-decoration:none;line-height:1;margin-bottom:32px}
  .logo-seo{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.1rem;letter-spacing:.06em;color:var(--ivory)}
  .logo-ai{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.3rem;color:var(--gold);letter-spacing:.02em}
  .logo-co{font-family:'DM Sans',sans-serif;font-weight:300;font-size:.95rem;color:rgba(150,150,150,.5);letter-spacing:.04em}
  h1{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:300;color:var(--ivory);margin:0 0 16px}
  p{font-size:.88rem;color:var(--muted);line-height:1.75;margin:0 0 12px}
  .email-ref{color:var(--ivory);font-weight:400}
  .note{font-size:.78rem;color:rgba(168,168,160,.4);margin-top:28px}
  .note a{color:var(--gold);text-decoration:none;transition:color .25s}
  .note a:hover{color:var(--gold-lt)}
  @media(max-width:500px){
    .wrap{padding:36px 24px}
    h1{font-size:1.4rem}
  }
</style>
</head>
<body>
<div class="wrap">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>

  @if($alreadyUnsubscribed)
    <h1>Already unsubscribed.</h1>
    <p>
      @if($email)
        <span class="email-ref">{{ $email }}</span> was
      @else
        This address was
      @endif
      already removed from follow-up emails.
    </p>
    <p>No further action needed.</p>
  @elseif($lead)
    <h1>You've been unsubscribed.</h1>
    <p><span class="email-ref">{{ $email }}</span> has been removed from follow-up emails.</p>
    <p>You will still receive transactional messages related to confirmed bookings and account activity.</p>
  @else
    <h1>Unsubscribe request received.</h1>
    <p>
      @if($email)
        <span class="email-ref">{{ $email }}</span> has
      @else
        This address has
      @endif
      been noted. You will not receive further marketing emails.
    </p>
  @endif

  <p class="note">
    Changed your mind? <a href="mailto:hello@seoaico.com">hello@seoaico.com</a>
  </p>
</div>
@include('components.tm-style')
</body>
</html>
