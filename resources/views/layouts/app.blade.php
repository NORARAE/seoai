<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title', 'Dashboard') — SEOAIco</title>
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
@hasSection('meta')
    @yield('meta')
@else
<meta name="description" content="AI-powered search infrastructure for local businesses. Programmatic visibility systems built on citation, authority, and market intelligence.">
<meta property="og:type" content="website">
<meta property="og:site_name" content="SEOAIco">
<meta property="og:title" content="SEOAIco — AI Search Infrastructure">
<meta property="og:description" content="Programmatic visibility systems for local businesses. Licensed. Controlled. Exclusive.">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="SEOAIco — AI Search Infrastructure">
<meta name="twitter:description" content="Programmatic visibility systems for local businesses.">
@endif
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

html{font-size:16px;scroll-behavior:smooth}
body{
  background:var(--bg);color:var(--ivory);
  font-family:'DM Sans',sans-serif;font-weight:400;
  line-height:1.65;min-height:100vh;
  position:relative;
}
a{color:rgba(200,168,75,.82);text-decoration:none;transition:color .2s ease,opacity .2s ease}
a:hover{color:#e2c97d}
a:visited{color:rgba(200,168,75,.82)}
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
.dash-nav-links a.active{color:var(--gold);background:rgba(200,168,75,.04);position:relative}
.dash-nav-links a.active::after{content:'';position:absolute;bottom:6px;left:14px;right:14px;height:2px;background:linear-gradient(90deg,transparent,var(--gold) 20%,var(--gold) 80%,transparent);border-radius:2px}

/* Right actions */
.dash-nav-right{display:flex;align-items:center;gap:12px}
.dash-primary-cta{
  display:inline-flex;align-items:center;gap:7px;
  padding:9px 14px;font-size:.7rem;font-weight:700;letter-spacing:.08em;
  color:#080808;text-decoration:none;text-transform:uppercase;
  border:1px solid rgba(200,168,75,.55);border-radius:8px;
  background:linear-gradient(135deg,var(--gold),var(--gold-lt));
  transition:transform .2s ease,box-shadow .2s ease;
}
.dash-primary-cta:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(200,168,75,.42)}
.dash-primary-cta svg{width:14px;height:14px}

.dash-admin-btn{
  display:inline-flex;align-items:center;gap:6px;
  padding:7px 16px;font-size:.72rem;font-weight:500;letter-spacing:.06em;
  color:rgba(200,168,75,.6);text-decoration:none;text-transform:uppercase;
  border:1px solid rgba(200,168,75,.1);border-radius:3px;
  transition:all .25s;
}
.dash-admin-btn:hover{color:var(--gold);border-color:rgba(200,168,75,.22);background:rgba(200,168,75,.04)}
.dash-admin-btn svg{width:14px;height:14px;opacity:.5}

.dash-profile{
  display:inline-flex;align-items:center;gap:10px;
  text-decoration:none;color:inherit;
  border:1px solid rgba(200,168,75,.12);border-radius:999px;
  padding:3px 11px 3px 3px;background:rgba(200,168,75,.04);
  transition:border-color .2s ease,background .2s ease;
}
.dash-profile:hover{border-color:rgba(200,168,75,.28);background:rgba(200,168,75,.08)}
.dash-avatar{
  width:30px;height:30px;border-radius:50%;
  background:linear-gradient(135deg,rgba(200,168,75,.18),rgba(200,168,75,.08));
  border:1px solid rgba(200,168,75,.12);
  display:flex;align-items:center;justify-content:center;
  font-size:.72rem;font-weight:600;color:var(--gold);letter-spacing:.02em;
}
.dash-profile-name{font-size:.74rem;color:rgba(237,232,222,.82)}
.dash-profile-wrap{position:relative}
.dash-profile-menu{
  position:absolute;right:0;top:44px;min-width:180px;
  background:#100d08;border:1px solid rgba(200,168,75,.18);border-radius:10px;
  box-shadow:0 16px 28px rgba(0,0,0,.42);padding:8px;display:none;z-index:90;
}
.dash-profile-wrap:hover .dash-profile-menu{display:block}
.dash-profile-menu a{
  display:flex;align-items:center;padding:8px 9px;border-radius:8px;
  font-size:.73rem;color:rgba(225,220,205,.85);
}
.dash-profile-menu a:hover{background:rgba(200,168,75,.08);color:#f0dfb3}
.dash-profile-menu .sep{height:1px;background:rgba(200,168,75,.14);margin:6px 2px}

/* ── Page Content ── */
.dash-content{
  position:relative;z-index:1;
  max-width:1280px;margin:0 auto;padding:32px 28px;
}
.dash-section-anchor{scroll-margin-top:84px}

/* ── Responsive ── */
@media(max-width:900px){
  .dash-nav-inner{padding:0 20px}
  .dash-content{padding:24px 20px}
  .dash-nav-brand .dash-tagline{display:none}
}
@media(max-width:768px){
  .dash-nav-links{display:none}
  .dash-nav-inner{height:54px}
  .dash-profile-name{display:none}
}
@media(max-width:640px){
  .dash-content{padding:20px 16px}
  .dash-nav-inner{padding:0 16px}
}

/* ── Admin Mode Indicator ── */
.admin-mode-banner {
  background: linear-gradient(90deg, rgba(59, 130, 246, 0.08), rgba(59, 130, 246, 0.04));
  border-bottom: 1px solid rgba(59, 130, 246, 0.2);
  padding: 10px 0;
}
.admin-mode-banner-inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 28px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  font-size: 0.85rem;
  color: rgba(59, 130, 246, 0.9);
  letter-spacing: 0.02em;
}
.admin-mode-banner-label {
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 8px;
}
.admin-mode-banner-label::before {
  content: '⚙';
  font-size: 1.1em;
  opacity: 0.7;
}
.admin-mode-banner-cta {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border: 1px solid rgba(59, 130, 246, 0.3);
  border-radius: 6px;
  background: rgba(59, 130, 246, 0.06);
  color: rgba(59, 130, 246, 0.95);
  font-size: 0.78rem;
  font-weight: 500;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  text-decoration: none;
  transition: all 0.2s;
}
.admin-mode-banner-cta:hover {
  background: rgba(59, 130, 246, 0.12);
  border-color: rgba(59, 130, 246, 0.5);
  color: rgba(59, 130, 246, 1);
}
.admin-mode-banner-cta::after {
  content: ' →';
}
@media(max-width:768px) {
  .admin-mode-banner-inner {
    flex-direction: column;
    text-align: center;
    gap: 8px;
  }
}

/* ── Dashboard in Admin Mode (subtle visual distinction) ── */
body.is-admin-viewing-dashboard {
  --accent-primary: #3b82f6;
}
</style>
@stack('styles')
</head>
@php
  $isAdminUser = auth()->check() && (auth()->user()->isPrivilegedStaff() || auth()->user()->isFrontendDev());
  $isViewingDashboard = request()->is('dashboard*');
  $isViewingAdmin = request()->is('admin*');
@endphp
<body class="{{ $isAdminUser && $isViewingDashboard ? 'is-admin-viewing-dashboard' : '' }}">
    
    {{-- Admin Mode Indicator Banner --}}
    @if($isAdminUser)
      <div class="admin-mode-banner">
        <div class="admin-mode-banner-inner">
          @if($isViewingDashboard)
            <div class="admin-mode-banner-label">Admin Mode — Viewing Customer System</div>
            <a href="/admin" class="admin-mode-banner-cta">Open Admin Panel</a>
          @elseif($isViewingAdmin)
            <div class="admin-mode-banner-label">Admin Panel</div>
            <a href="/dashboard" class="admin-mode-banner-cta">View Customer System</a>
          @endif
        </div>
      </div>
    @endif
    
    {{-- Top Navigation --}}
    <nav class="dash-nav">
        <div class="dash-nav-inner">
            {{-- Logo & Brand --}}
            <div class="dash-nav-left">
                <a href="/" class="dash-nav-brand">
                    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
                    <span class="dash-tagline">AI Visibility System</span>
                </a>
                
                {{-- Main Navigation --}}
                <div class="dash-nav-links">
                  <a href="{{ route('app.dashboard') }}" class="{{ request()->is('dashboard') && !request()->is('dashboard/scans') && !request()->is('dashboard/reports') ? 'active' : '' }}" title="Your AI visibility levels and progression">Progress</a>
                  <a href="{{ route('app.dashboard.scans') }}" class="{{ request()->is('dashboard/scans') ? 'active' : '' }}" title="Your reports and scan history">Reports</a>
                  <a href="{{ route('quick-scan.show') }}" class="{{ request()->is('quick-scan*') ? 'active' : '' }}" title="Run a new visibility scan">New Scan</a>
                </div>
            </div>
            
            {{-- Right Actions --}}
            <div class="dash-nav-right">
                <a href="{{ route('quick-scan.show') }}" class="dash-primary-cta">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5"/>
                    </svg>
                    <span>Run New Scan</span>
                </a>

                  {{-- User avatar/profile --}}
                <div class="dash-profile-wrap">
                  <a href="{{ route('app.billing') }}" class="dash-profile" aria-label="Open profile and billing">
                      <div class="dash-avatar">
                          {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(strrchr(auth()->user()?->name ?? '', ' ') ?: '', 1, 1)) }}
                      </div>
                      <span class="dash-profile-name">{{ auth()->user()?->name ?? 'Profile' }}</span>
                  </a>
                  <div class="dash-profile-menu" role="menu">
                    <a href="{{ route('app.billing') }}" role="menuitem">Profile & Billing</a>
                    <a href="{{ route('app.settings.notifications') }}" role="menuitem">Notification Preferences</a>
                    @if(auth()->user()?->isPrivilegedStaff() || auth()->user()?->isFrontendDev())
                      <div class="sep"></div>
                       <a href="/admin" role="menuitem">Admin Panel</a>
                    @endif
                    <div class="sep"></div>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0">
                      @csrf
                      <button type="submit" role="menuitem" style="background:none;border:none;width:100%;text-align:left;cursor:pointer;padding:0;color:inherit;font:inherit">Sign Out</button>
                    </form>
                  </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Page Content --}}
    <main class="dash-content">
        @yield('content')
    </main>

    @stack('scripts')

    @include('partials.back-to-top', ['dashMode' => true])
</body>
</html>
