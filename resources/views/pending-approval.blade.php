<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Account Under Review — SEOAIco</title>
<link rel="canonical" href="{{ url('/pending-approval') }}">
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;
  --card-bg:linear-gradient(160deg,rgba(14,13,9,.92) 0%,rgba(10,9,7,.95) 100%);
  --border:rgba(200,168,75,.12);
  --gold:#c8a84b;--gold-lt:#e2c97d;--gold-dim:rgba(200,168,75,.4);
  --ivory:#ede8de;--muted:rgba(168,168,160,.72);
}
html{font-size:18px}
body{
  background:var(--bg);
  background-image:radial-gradient(ellipse 70% 50% at 50% -5%,rgba(200,168,75,.06),transparent);
  color:var(--ivory);
  font-family:'DM Sans',sans-serif;font-weight:300;
  min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:40px 20px;
}
.wrap{
  max-width:520px;width:100%;text-align:center;
}
.logo{
  display:inline-flex;align-items:baseline;text-decoration:none;line-height:1;margin-bottom:48px;
}
.logo-seo{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.2rem;letter-spacing:.06em;color:#f5f0e8}
.logo-ai{
  font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.42rem;
  color:var(--gold);letter-spacing:.02em;
  display:inline-block;transform:skewX(-11deg) translateY(-1px);
}
.logo-co{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.05rem;color:rgba(255,255,255,.45);letter-spacing:.04em}
.logo-ai{color:var(--gold) !important}

.card{
  background:var(--card-bg);border:1px solid var(--border);border-radius:14px;
  box-shadow:0 24px 64px rgba(0,0,0,.65),0 0 0 1px rgba(200,168,75,.06),inset 0 1px 0 rgba(255,255,255,.04);
  padding:48px 40px;
}
.status-icon{
  font-size:2.2rem;margin-bottom:24px;
  display:block;
  color:var(--gold);
  font-family:'Cormorant Garamond',serif;
  font-style:italic;
  letter-spacing:.04em;
}
.card-title{
  font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:300;
  color:var(--ivory);margin-bottom:8px;line-height:1.2;
}
.card-sub{
  font-size:.82rem;letter-spacing:.12em;text-transform:uppercase;
  color:var(--gold-dim);margin-bottom:32px;
}
.card-body{
  font-size:1rem;color:var(--muted);line-height:1.8;margin-bottom:28px;
}
.card-body strong{color:var(--ivory);font-weight:400}
.divider{width:40px;height:1px;background:rgba(200,168,75,.15);margin:28px auto}
.support-line{
  font-size:.78rem;color:var(--muted);line-height:1.7;
}
.support-line a{color:var(--gold);text-decoration:none}
.support-line a:hover{color:var(--gold-lt)}
.logout-link{
  display:inline-block;margin-top:32px;
  font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;
  color:var(--muted);text-decoration:none;
  transition:color .2s;
}
.logout-link:hover{color:var(--ivory)}
</style>
</head>
<body>

<div class="wrap">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>

  <div class="card">
    <span class="status-icon">◈</span>
    <h1 class="card-title">Account Under Review</h1>
    <p class="card-sub">Pending Admin Approval</p>

    <p class="card-body">
      Your account has been <strong>created successfully</strong>, but dashboard access is not yet active.<br><br>
      An administrator must approve your account before you can continue. This typically happens after your <strong>Discovery Call</strong> or initial onboarding review.
    </p>

    <div class="divider"></div>

    <p class="support-line" style="margin-bottom:20px">
      <strong style="color:var(--ivory);font-weight:400">Already ran a $2 scan?</strong><br>
      <a href="/login" style="letter-spacing:.06em">Sign in again</a> to auto-verify your account.
    </p>

    <p class="support-line" style="margin-bottom:0">
      Haven't scanned yet?&nbsp;
      <a href="/scan/start" style="letter-spacing:.06em">Complete a scan</a> to activate your account.
    </p>

    <div class="divider"></div>

    <p class="support-line">
      If you believe this is an error or need immediate access,<br>
      please contact us at <a href="mailto:hello@seoaico.com">hello@seoaico.com</a>.
    </p>
  </div>

  <div style="display:flex;gap:32px;align-items:center;justify-content:center;margin-top:32px">
    <a href="/" class="logout-link">← Back to Homepage</a>
    <form method="POST" action="{{ route('filament.admin.auth.logout') }}" style="display:inline">
      @csrf
      <button type="submit" class="logout-link">Sign Out</button>
    </form>
  </div>
</div>

</body>
</html>
