<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Onboarding Complete — SEOAIco</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #080808;
  --ivory: #ede8de;
  --muted: #a8a8a0;
  --gold: #c8a84b;
  --gold-lt: #e2c97d;
  --gold-dim: #9a7a30;
  --border: rgba(200,168,75,.10);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
  background: var(--bg);
  color: var(--ivory);
  font-family: 'DM Sans', sans-serif;
  font-weight: 300;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 24px;
}
.done-wrap { max-width: 520px; width: 100%; }
.done-eye {
  font-size: .64rem;
  letter-spacing: .24em;
  text-transform: uppercase;
  color: var(--gold);
  display: block;
  margin-bottom: 20px;
}
.done-mark {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 1px solid rgba(200,168,75,.22);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 28px;
  color: var(--gold);
  font-size: 1.2rem;
}
.done-hed {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 4vw, 2.8rem);
  font-weight: 300;
  line-height: 1.1;
  color: var(--ivory);
  margin-bottom: 14px;
}
.done-hed em { font-style: italic; color: var(--gold-lt); }
.done-sub {
  font-size: .92rem;
  color: var(--muted);
  line-height: 1.75;
  margin-bottom: 36px;
  max-width: 420px;
}
.done-email-note {
  font-size: .78rem;
  color: rgba(168,168,160,.52);
  font-style: italic;
  margin-bottom: 28px;
}
.done-home {
  display: block;
  font-size: .74rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--gold-dim);
  text-decoration: none;
  transition: color .25s;
}
.done-home:hover { color: var(--gold); }
@media (max-width: 520px) {
  body { padding: 40px 20px; }
}
</style>
</head>
<body>
<div class="done-wrap">
  <span class="done-eye">Onboarding</span>
  <div class="done-mark">&#10003;</div>
  <h1 class="done-hed">
    @if($alreadySubmitted)
      Already<br><em>received.</em>
    @else
      Intake<br><em>submitted.</em>
    @endif
  </h1>
  <p class="done-sub">
    @if($alreadySubmitted)
      We already have your onboarding on file. Our team will reach out within 1–2 business days.
    @else
      We've received your intake form and business license. Our team will review everything and be in touch within 1–2 business days.
    @endif
  </p>
  <p class="done-email-note">A confirmation email has been sent to your inbox.</p>
  <a href="{{ url('/') }}" class="done-home">&larr; seoaico.com</a>
</div>
</body>
</html>
