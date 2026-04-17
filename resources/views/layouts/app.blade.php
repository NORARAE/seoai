<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title', 'Dashboard') — SEO AI Co</title>
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
  --bg:#080808;
  --deep:#0b0b0b;
  --card:#0e0d09;
  --gold:#c8a84b;
  --gold-lt:#e2c97d;
  --gold-dim:rgba(200,168,75,.4);
  --gold-secondary:rgba(200,168,75,.55);
  --ivory:#ede8de;
  --muted:rgba(168,168,160,.72);
  --border:rgba(200,168,75,.09);
  --ease-out:cubic-bezier(.23,1,.32,1);
}

html{font-size:16px}
body{
  background:var(--bg);color:var(--ivory);
  font-family:'DM Sans',sans-serif;font-weight:400;
  line-height:1.65;min-height:100vh;
  position:relative;
}
/* Subtle grid background */
body::before{
  content:'';position:fixed;inset:0;
  background-image:
    linear-gradient(rgba(200,168,75,.025) 1px,transparent 1px),
    linear-gradient(90deg,rgba(200,168,75,.025) 1px,transparent 1px);
  background-size:48px 48px;pointer-events:none;z-index:0;
}

/* ── Logo ── */
.logo-seo{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.15rem;letter-spacing:.06em;color:#f5f0e8}
.logo-ai{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.35rem;color:#c8a84b;letter-spacing:.02em;display:inline-block;transform:skewX(-11deg) translateY(-1px)}
.logo-co{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1rem;color:rgba(255,255,255,.45);letter-spacing:.04em}

/* ── Top Nav ── */
.dash-nav{
  position:sticky;top:0;z-index:50;
  background:rgba(8,8,8,.88);
  backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);
  border-bottom:1px solid rgba(200,168,75,.08);
}
.dash-nav-inner{
  max-width:1280px;margin:0 auto;padding:0 28px;height:60px;
  display:flex;align-items:center;justify-content:space-between;
}
.dash-nav-left{display:flex;align-items:center;gap:32px}
.dash-nav-brand{text-decoration:none;display:inline-flex;align-items:baseline;gap:0}
.dash-nav-brand .dash-tagline{
  font-size:.6rem;color:rgba(200,168,75,.35);letter-spacing:.08em;text-transform:uppercase;
  margin-left:12px;
}

/* Nav links */
.dash-nav-links{display:flex;align-items:center;gap:2px}
.dash-nav-links a{
  padding:8px 14px;font-size:.78rem;font-weight:500;letter-spacing:.02em;
  color:rgba(168,168,160,.6);text-decoration:none;border-radius:4px;
  transition:color .25s,background .25s;
}
.dash-nav-links a:hover{color:var(--ivory);background:rgba(200,168,75,.04)}
.dash-nav-links a.active{color:var(--gold);background:rgba(200,168,75,.06)}

/* Right actions */
.dash-nav-right{display:flex;align-items:center;gap:12px}
.dash-admin-btn{
  display:inline-flex;align-items:center;gap:6px;
  padding:7px 16px;font-size:.72rem;font-weight:500;letter-spacing:.06em;
  color:rgba(200,168,75,.6);text-decoration:none;text-transform:uppercase;
  border:1px solid rgba(200,168,75,.1);border-radius:3px;
  transition:all .25s;
}
.dash-admin-btn:hover{color:var(--gold);border-color:rgba(200,168,75,.22);background:rgba(200,168,75,.04)}
.dash-admin-btn svg{width:14px;height:14px;opacity:.5}

.dash-avatar{
  width:34px;height:34px;border-radius:50%;
  background:linear-gradient(135deg,rgba(200,168,75,.18),rgba(200,168,75,.08));
  border:1px solid rgba(200,168,75,.12);
  display:flex;align-items:center;justify-content:center;
  font-size:.72rem;font-weight:600;color:var(--gold);letter-spacing:.02em;
}

/* ── Page Content ── */
.dash-content{
  position:relative;z-index:1;
  max-width:1280px;margin:0 auto;padding:32px 28px;
}

/* ── Responsive ── */
@media(max-width:900px){
  .dash-nav-inner{padding:0 20px}
  .dash-content{padding:24px 20px}
  .dash-nav-brand .dash-tagline{display:none}
}
@media(max-width:768px){
  .dash-nav-links{display:none}
  .dash-nav-inner{height:54px}
}
@media(max-width:640px){
  .dash-content{padding:20px 16px}
  .dash-nav-inner{padding:0 16px}
}
</style>
@stack('styles')
</head>
<body>
    
    {{-- Top Navigation --}}
    <nav class="dash-nav">
        <div class="dash-nav-inner">
            {{-- Logo & Brand --}}
            <div class="dash-nav-left">
                <a href="/dashboard" class="dash-nav-brand">
                    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
                    <span class="dash-tagline">AI Visibility System</span>
                </a>
                
                {{-- Main Navigation --}}
                <div class="dash-nav-links">
                    <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="/sites" class="{{ request()->is('sites*') ? 'active' : '' }}">Sites</a>
                    <a href="/pages" class="{{ request()->is('pages*') ? 'active' : '' }}">Pages</a>
                    <a href="/internal-links" class="{{ request()->is('internal-links*') ? 'active' : '' }}">Links</a>
                    <a href="/reports" class="{{ request()->is('reports*') ? 'active' : '' }}">Reports</a>
                    <a href="/dashboard#ai-scans" class="{{ request()->is('dashboard') && request()->fragment === 'ai-scans' ? 'active' : '' }}">AI Scans</a>
                </div>
            </div>
            
            {{-- Right Actions --}}
            <div class="dash-nav-right">
                @if(auth()->user()?->isPrivilegedStaff() || auth()->user()?->isFrontendDev())
                <a href="/admin" target="_blank" class="dash-admin-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Admin</span>
                </a>
                @endif
                
                {{-- User avatar --}}
                <div class="dash-avatar">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(strrchr(auth()->user()?->name ?? '', ' ') ?: '', 1, 1)) }}
                </div>
            </div>
        </div>
    </nav>

    {{-- Page Content --}}
    <main class="dash-content">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
