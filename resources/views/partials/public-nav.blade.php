{{-- Shared public navigation — single source of truth --}}
<nav id="nav" aria-label="Site navigation">
  <a href="/" class="logo" aria-label="SEO AI Co home">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <div class="nav-right">
    <a href="/how-it-works" class="nav-link">How It Works</a>
    <a href="/pricing" class="nav-link">Pricing</a>
    <a href="/book" class="nav-link">Book</a>
    <a href="/dashboard" class="nav-link">Portal</a>
    <a href="/quick-scan" class="nav-btn">Get Started</a>
  </div>
  @if($showHamburger ?? false)
  <button class="nav-hamburger" id="navHamburger" aria-label="Open menu" aria-expanded="false" aria-controls="navMenu">
    <span></span><span></span><span></span>
  </button>
  @endif
</nav>

@if($showHamburger ?? false)
{{-- Mobile slide-in panel --}}
<div id="navBackdrop" class="nav-backdrop" aria-hidden="true"></div>
<div id="navMenu" class="nav-menu" aria-hidden="true" role="dialog" aria-label="Site navigation">
  <div class="nav-menu-inner">

    <div class="nm-identity">
      <div class="nm-identity-brand">SEO AI <em>Co&trade;</em></div>
      <div class="nm-identity-sub">Control your search presence.</div>
    </div>

    <div class="nm-group"><span class="nm-group-label">Get Started</span></div>
    <a href="/quick-scan" class="nm-link nm-featured" data-menu-close>Run a $2 Scan &nbsp;&rarr;</a>

    <div class="nm-divider"></div>

    <div class="nm-group"><span class="nm-group-label">Explore</span></div>
    <a href="/how-it-works" class="nm-link" data-menu-close>How It Works</a>
    <a href="/pricing" class="nm-link" data-menu-close>Pricing</a>
    <a href="/book" class="nm-link" data-menu-close>Book</a>

    <div class="nm-divider"></div>

    <div class="nm-group"><span class="nm-group-label">Account</span></div>
    @auth
      <a href="/dashboard" class="nm-portal" data-menu-close>My Dashboard</a>
    @else
      <a href="/login" class="nm-link" data-menu-close>Sign In</a>
    @endauth

  </div>
</div>
@endif
