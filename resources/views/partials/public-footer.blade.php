{{-- Shared site footer — @include('partials.public-footer') --}}
<footer class="site-footer" role="contentinfo">
  @include('components.payment-trust-footer')
  <div class="footer-main">
    <a href="/" class="logo" aria-label="SEOAIco — home">
      <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
    </a>
    <span class="footer-copy">&copy; {{ date('Y') }} SEOAIco &middot; Programmatic AI SEO Systems</span>
  </div>
  <p class="footer-email"><a href="mailto:hello@seoaico.com">hello@seoaico.com</a></p>
  <nav class="footer-links" aria-label="Footer links">
    <a href="{{ route('privacy') }}">Privacy</a>
    <a href="{{ route('terms') }}">Terms</a>
    <a href="{{ route('refund-policy') }}">Refund Policy</a>
    <a href="{{ route('scan.start') }}">AI Citation Scan</a>
  </nav>
</footer>
