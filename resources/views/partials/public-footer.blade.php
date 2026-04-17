{{-- Shared site footer — @include('partials.public-footer') --}}
<footer class="site-footer">
  <a href="/" class="logo" aria-label="SEO AI Co — home">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; {{ date('Y') }} SEO AI Co&trade; &middot; Programmatic AI SEO Systems</span>
  <nav class="footer-links" aria-label="Footer links">
    <a href="{{ route('privacy') }}">Privacy</a>
    <a href="{{ route('terms') }}">Terms</a>
    <a href="{{ route('scan.start') }}">AI Citation Scan</a>
  </nav>
</footer>
