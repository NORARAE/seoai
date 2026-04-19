<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $ogTitle }} - SEOAIco</title>
<meta name="description" content="{{ $ogDescription }}">
<meta name="robots" content="index,follow">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

<meta property="og:title" content="AI Visibility Score: {{ $scan->score }}" />
<meta property="og:description" content="AI systems are answering in your market — some aren't citing you. Check your score." />
<meta property="og:image" content="{{ url('/images/share-preview.png') }}" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta name="twitter:card" content="summary_large_image">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #080808;
  --panel: #11110f;
  --text: #ede8de;
  --muted: #b4aca0;
  --gold: #c8a84b;
  --gold-lt: #e2c97d;
  --line: rgba(200, 168, 75, 0.22);
  --good: #6aaf90;
  --sys-space-section: 32px;
  --sys-space-panel: 24px;
  --sys-panel-radius: 12px;
  --sys-cta-size: .72rem;
  --sys-cta-track: .12em;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'DM Sans', sans-serif;
  background: radial-gradient(circle at 20% 15%, rgba(200, 168, 75, 0.1), transparent 40%),
              radial-gradient(circle at 80% 80%, rgba(106, 175, 144, 0.08), transparent 42%),
              var(--bg);
  color: var(--text);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
}
.share-shell {
  width: 100%;
  max-width: 760px;
  background: rgba(10, 10, 9, 0.94);
  border: 1px solid var(--line);
  border-radius: var(--sys-panel-radius);
  padding: 56px 40px;
  text-align: center;
}
.score {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2.2rem, 5vw, 3.2rem);
  font-weight: 300;
  line-height: 1.1;
  color: var(--good);
  margin-bottom: var(--sys-space-section);
}
.primary-line {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.5rem, 3vw, 1.8rem);
  font-weight: 300;
  line-height: 1.3;
  color: var(--text);
  margin-bottom: 16px;
}
.secondary-line {
  font-size: 0.88rem;
  line-height: 1.6;
  color: rgba(237, 232, 222, 0.85);
  margin-bottom: var(--sys-space-section);
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}
.tease-box {
  background: rgba(200, 168, 75, 0.04);
  border: 1px solid rgba(200, 168, 75, 0.12);
  padding: 22px 24px;
  margin-bottom: var(--sys-space-section);
  border-radius: var(--sys-panel-radius);
}
.tease-box p {
  font-size: 0.88rem;
  line-height: 1.55;
  color: rgba(237, 232, 222, 0.9);
  margin: 0;
}
.tease-box p.muted {
  color: rgba(168, 168, 160, 0.7);
  margin-top: 10px;
  font-size: 0.82rem;
}
.cta-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  background: linear-gradient(135deg, var(--gold), var(--gold-lt));
  color: #080808;
  padding: 14px 28px;
  min-height: 44px;
  font-size: var(--sys-cta-size);
  text-transform: uppercase;
  letter-spacing: var(--sys-cta-track);
  font-weight: 700;
  border-radius: 10px;
  border: 1px solid rgba(200,168,75,.55);
  transition: all 0.3s;
  box-shadow: 0 4px 16px rgba(200, 168, 75, 0.4);
}
.cta-primary:hover {
  box-shadow: 0 8px 28px rgba(200, 168, 75, 0.6);
  transform: translateY(-2px);
}

@media (max-width: 640px) {
  .share-shell { 
    padding: 40px 24px;
  }
  .score {
    margin-bottom: 24px;
  }
  .primary-line {
    margin-bottom: 14px;
  }
}
</style>
</head>
<body>
  <main class="share-shell">
    <h1 class="score">
      AI Visibility Score: {{ $scan->score }}
    </h1>

    <p class="primary-line">
      You're being seen — but not consistently cited.
    </p>

    <p class="secondary-line">
      AI systems are already answering in your market.
      Some are not citing you.
    </p>

    <div class="tease-box">
      <p>This is what's visible from the outside.</p>
      <p class="muted">The real gaps — and how to fix them — are hidden.</p>
    </div>

    <a href="{{ route('quick-scan.show') }}" class="cta-primary">
      See What AI Sees → $2
    </a>
  </main>
</body>
</html>
