<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Client Systems - SEOAIco</title>
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#080808;--panel:#11100c;--gold:#c8a84b;--gold-lt:#dfc477;--line:rgba(200,168,75,.16);--text:#ede8de;--muted:#b4ac9c}
body{font-family:'DM Sans',sans-serif;background:radial-gradient(circle at 20% 10%,rgba(200,168,75,.09),transparent 42%),radial-gradient(circle at 80% 85%,rgba(106,175,144,.07),transparent 45%),var(--bg);color:var(--text);min-height:100vh;padding:32px}
.shell{max-width:920px;margin:0 auto;border:1px solid var(--line);border-radius:18px;background:linear-gradient(140deg,#14120c,#0e0c08 70%);padding:42px 34px}
.kicker{font-size:.62rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.76);margin-bottom:12px}
h1{font-family:'Cormorant Garamond',serif;font-size:clamp(2rem,4.8vw,3rem);line-height:1.08;font-weight:600;margin-bottom:12px}
p{color:var(--muted);line-height:1.72;font-size:.96rem}
.grid{margin-top:24px;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px}
.card{border:1px solid rgba(200,168,75,.14);border-radius:14px;background:#14110b;padding:14px}
.card h3{font-size:.88rem;letter-spacing:.08em;text-transform:uppercase;color:#ebdfbf;margin-bottom:8px}
.card p{font-size:.85rem}
.actions{margin-top:28px;display:flex;flex-wrap:wrap;gap:10px}
.btn{display:inline-flex;align-items:center;justify-content:center;border-radius:10px;min-height:42px;padding:10px 16px;text-decoration:none;letter-spacing:.08em;text-transform:uppercase;font-size:.72rem;font-weight:700;transition:all .2s ease}
.btn.primary{background:var(--gold);color:#0b0905;border:1px solid rgba(200,168,75,.58)}
.btn.primary:hover{background:var(--gold-lt)}
.btn.secondary{border:1px solid rgba(200,168,75,.24);color:#ded2b7;background:rgba(200,168,75,.06)}
.btn.secondary:hover{border-color:rgba(200,168,75,.42);background:rgba(200,168,75,.12)}
@media(max-width:640px){body{padding:18px}.shell{padding:28px 18px}}
</style>
</head>
<body>
  <main class="shell">
    <p class="kicker">Multi-System Workflow</p>
    <h1>Run and manage additional systems without friction.</h1>
    <p>This workspace is optimized for teams and operators who track multiple domains. Add systems, monitor score movement, and open reports directly from your dashboard grid.</p>

    <section class="grid" aria-label="Client systems guidance">
      <article class="card">
        <h3>System Naming</h3>
        <p>Use domain-first naming so cards remain scannable as your list grows.</p>
      </article>
      <article class="card">
        <h3>Workflow</h3>
        <p>Each system stays tied to your account and opens with a single click.</p>
      </article>
      <article class="card">
        <h3>History</h3>
        <p>Billing activity and unlock history continue to reflect all tracked systems.</p>
      </article>
    </section>

    <div class="actions">
      <a class="btn primary" href="{{ route('quick-scan.show') }}">Run Additional System</a>
      <a class="btn secondary" href="{{ route('app.dashboard') }}">Back to Dashboard</a>
    </div>
  </main>
</body>
</html>
