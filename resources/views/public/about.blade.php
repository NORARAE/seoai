<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About SEOAIco — AI Citation Infrastructure & Research | SEO AI Co™</title>
<meta name="description" content="SEOAIco builds the AI citation infrastructure layer for businesses — the technical and content architecture that makes your brand the source AI systems cite across Google AI Overviews, ChatGPT, and Perplexity.">
<link rel="canonical" href="{{ url('/about') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="SEO AI Co™">
<meta property="og:title" content="About SEOAIco — AI Citation Infrastructure & Research | SEO AI Co™">
<meta property="og:description" content="SEOAIco builds AI citation infrastructure for businesses — the system that makes brands the source AI systems cite by default.">
<meta property="og:url" content="{{ url('/about') }}">
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type'       => 'Organization',
            '@id'         => url('/') . '#organization',
            'name'        => 'SEO AI Co™',
            'alternateName' => 'SEOAIco',
            'url'         => url('/'),
            'description' => 'SEOAIco builds AI citation infrastructure for businesses — the technical and content architecture that structures web presence for citation by AI-powered search systems, including Google AI Overviews, ChatGPT, Perplexity, and Gemini.',
            'knowsAbout'  => [
                'AI Search Optimization',
                'Programmatic SEO',
                'AI Citation Infrastructure',
                'Local AI Search',
                'Structured Data & Schema Markup',
                'Content Extraction Optimization',
            ],
            'subjectOf'   => [
                ['@type' => 'Article', 'url' => url('/what-is-ai-search-optimization'), 'name' => 'What Is AI Search Optimization?'],
                ['@type' => 'Article', 'url' => url('/ai-search-optimization'), 'name' => 'AI Search Optimization'],
                ['@type' => 'Article', 'url' => url('/ai-citation-engine'), 'name' => 'The AI Citation Engine™'],
                ['@type' => 'Article', 'url' => url('/programmatic-seo-platform'), 'name' => 'Programmatic SEO Platform'],
                ['@type' => 'Article', 'url' => url('/chatgpt-seo'), 'name' => 'ChatGPT SEO'],
                ['@type' => 'Article', 'url' => url('/local-ai-search'), 'name' => 'Local AI Search'],
                ['@type' => 'Article', 'url' => url('/search-presence-engine'), 'name' => 'Search Presence Engine'],
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'About', 'item' => url('/about')],
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;--card:#0e0d09;--border:rgba(200,168,75,.09);
  --gold:#c8a84b;--gold-lt:#e2c97d;--gold-dim:rgba(200,168,75,.4);
  --ivory:#ede8de;--muted:rgba(168,168,160,.72);--muted-lt:rgba(168,168,160,.50);--deep:#0b0b0b;
}
html{scroll-behavior:smooth;font-size:18px}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.75;min-height:100vh}
.logo{display:inline-flex;align-items:baseline;text-decoration:none;line-height:1}
.logo-seo{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.2rem;letter-spacing:.06em;color:var(--ivory)}
.logo-ai{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.42rem;color:var(--gold);letter-spacing:.02em}
.logo-co{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.05rem;color:rgba(150,150,150,.5);letter-spacing:.04em}
nav.site-nav{display:flex;align-items:center;justify-content:space-between;padding:22px 48px;border-bottom:1px solid rgba(200,168,75,.07);position:sticky;top:0;z-index:100;background:rgba(8,8,8,.97);backdrop-filter:blur(8px)}
.nav-links{display:flex;gap:28px;list-style:none}
.nav-links a{font-size:.78rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .2s}
.nav-links a:hover{color:var(--gold)}
article{max-width:780px;margin:0 auto;padding:72px 40px 96px}
.eyebrow{font-size:.7rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);opacity:.8;margin-bottom:16px}
h1{font-family:'Cormorant Garamond',serif;font-size:clamp(2.6rem,5vw,3.8rem);font-weight:300;line-height:1.1;color:var(--ivory);margin-bottom:12px}
h1 em{font-style:italic;color:var(--gold-lt)}
.author-title{font-size:.9rem;color:rgba(200,168,75,.7);letter-spacing:.06em;margin-bottom:32px}
.lead{font-size:1.08rem;color:rgba(237,232,222,.85);line-height:1.82;margin-bottom:36px;border-left:2px solid var(--gold);padding-left:20px}
.section-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.7rem,3vw,2.4rem);font-weight:300;color:var(--ivory);margin:52px 0 18px;line-height:1.2}
.section-hed em{font-style:italic;color:var(--gold-lt)}
.prose{font-size:.97rem;color:var(--muted);line-height:1.88;margin-bottom:22px}
.prose strong{color:var(--ivory);font-weight:400}
.prose a{color:var(--gold);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.3);transition:border-color .2s}
.prose a:hover{border-color:var(--gold)}
.divider{height:1px;background:linear-gradient(to right,transparent,rgba(200,168,75,.12),transparent);margin:52px 0}
.expertise-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:28px 0}
.expertise-card{background:var(--card);border:1px solid var(--border);border-radius:5px;padding:22px 24px;transition:border-color .25s,transform .25s cubic-bezier(.23,1,.32,1),box-shadow .25s}
.expertise-card:hover{border-color:rgba(200,168,75,.18);transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.3)}
.expertise-card-title{font-size:.9rem;color:var(--ivory);font-weight:400;margin-bottom:8px}
.expertise-card-text{font-size:.85rem;color:var(--muted);line-height:1.7}
.concept-links{display:grid;grid-template-columns:1fr 1fr;gap:2px;margin:28px 0}
.concept-link{background:var(--card);border:1px solid var(--border);padding:18px 22px;text-decoration:none;transition:border-color .2s,background .2s}
.concept-link:hover{border-color:rgba(200,168,75,.22);background:rgba(200,168,75,.04)}
.concept-link-label{display:block;font-size:.6rem;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);opacity:.65;margin-bottom:5px}
.concept-link-title{display:block;font-size:.95rem;color:var(--ivory);line-height:1.35}
.signal-list{list-style:none;margin:20px 0 28px;display:flex;flex-direction:column;gap:10px}
.signal-list li{font-size:.9rem;color:var(--muted);padding-left:18px;position:relative;line-height:1.65}
.signal-list li::before{content:'→';position:absolute;left:0;color:var(--gold);opacity:.6;font-size:.8rem}
.signal-list li strong{color:var(--ivory);font-weight:400}
.page-cta{background:var(--card);border:1px solid var(--border);border-radius:6px;padding:40px 36px;text-align:center;margin-top:56px}
.page-cta-eye{display:block;font-size:.65rem;letter-spacing:.18em;text-transform:uppercase;color:var(--gold);opacity:.75;margin-bottom:14px}
.page-cta h2{font-family:'Cormorant Garamond',serif;font-size:clamp(1.6rem,3vw,2.2rem);font-weight:300;color:var(--ivory);margin-bottom:14px;line-height:1.2}
.page-cta h2 em{font-style:italic;color:var(--gold-lt)}
.page-cta p{font-size:.93rem;color:var(--muted);line-height:1.75;max-width:520px;margin:0 auto 28px}
.cta-btn{display:inline-block;padding:13px 30px;background:var(--gold);color:#080808;font-size:.82rem;font-weight:500;letter-spacing:.08em;text-transform:uppercase;text-decoration:none;border-radius:3px;transition:background .2s}
.cta-btn:hover{background:var(--gold-lt)}
.cta-ghost{display:inline-block;margin-left:18px;font-size:.82rem;color:var(--muted);text-decoration:none;letter-spacing:.04em;transition:color .2s}
.cta-ghost:hover{color:var(--gold)}
footer{border-top:1px solid rgba(200,168,75,.07);padding:32px 40px;display:flex;flex-direction:column;align-items:center;gap:10px}
.footer-copy{font-size:.72rem;color:rgba(168,168,160,.38);letter-spacing:.05em}
.footer-links{display:flex;gap:20px;list-style:none}
.footer-links a{font-size:.72rem;color:rgba(168,168,160,.38);text-decoration:none;transition:color .2s}
.footer-links a:hover{color:var(--gold)}
@media(max-width:640px){article{padding:48px 24px 72px}nav.site-nav{padding:18px 24px}.nav-links{gap:16px}.expertise-grid,.concept-links{grid-template-columns:1fr}}
</style>
</head>
<body>

<nav class="site-nav" aria-label="Site navigation">
  <a href="{{ url('/') }}" class="logo" aria-label="SEO AI Co home">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <ul class="nav-links">
    <li><a href="{{ route('ai-search-optimization') }}">AI Search</a></li>
    <li><a href="{{ route('ai-citation-engine') }}">AI Citation Engine™</a></li>
    <li><a href="{{ route('book.index') }}">Book</a></li>
  </ul>
</nav>

<article>

  <p class="eyebrow">About SEOAIco</p>
  <h1>The AI Citation<br><em>Infrastructure</em> Company</h1>
  <p class="author-title">Built to make businesses the source AI systems cite.</p>

  <p class="lead">SEOAIco builds the AI citation infrastructure layer for businesses &#8212; the technical and content architecture that structures web presence for extraction and citation by AI-powered search systems. We developed the AI Citation Engine™ to deploy this infrastructure at scale across every service and market a business operates in.</p>

  <p class="prose">The shift from ranked results to AI-generated answers has changed how discoverability works. Our research and methodology center on one core question: <strong>what makes AI systems choose to cite one source over another?</strong> The answer &#8212; structured content, entity definition, schema, and geographic coverage &#8212; is the foundation of every engagement at SEO AI Co™.</p>

  <p class="prose">We work with local service businesses and agencies to deploy citation infrastructure at scale: structured service-location pages, schema layers, llms.txt guidance, and the internal link architecture that AI systems use to establish topical authority.</p>

  <div class="divider"></div>

  <h2 class="section-hed">Areas of <em>expertise.</em></h2>

  <div class="expertise-grid">
    <div class="expertise-card">
      <p class="expertise-card-title">AI Search Optimization</p>
      <p class="expertise-card-text">Structuring web content for extraction and citation in AI-generated answers &#8212; across Google AI Overviews, ChatGPT, Perplexity, and Gemini.</p>
    </div>
    <div class="expertise-card">
      <p class="expertise-card-title">Programmatic SEO</p>
      <p class="expertise-card-text">Systematic generation of structured service-location pages at scale &#8212; deploying citation infrastructure across an entire service area as a single operation.</p>
    </div>
    <div class="expertise-card">
      <p class="expertise-card-title">AI Citation Infrastructure</p>
      <p class="expertise-card-text">The technical and architectural systems that position a business as the preferred citation source in AI systems: schema, entity definition, llms.txt, and extraction-optimized content.</p>
    </div>
    <div class="expertise-card">
      <p class="expertise-card-title">Entity Clarity & Schema</p>
      <p class="expertise-card-text">JSON-LD structured data implementation &#8212; LocalBusiness, Service, FAQPage, DefinedTerm, BreadcrumbList &#8212; that makes content machine-readable across all search surfaces.</p>
    </div>
    <div class="expertise-card">
      <p class="expertise-card-title">Local AI Discovery</p>
      <p class="expertise-card-text">Geographic coverage architecture for service businesses &#8212; building the page infrastructure that makes businesses visible in AI-generated local service answers.</p>
    </div>
    <div class="expertise-card">
      <p class="expertise-card-title">Content Extraction Optimization</p>
      <p class="expertise-card-text">Sentence-level content architecture designed for retrieval &#8212; self-contained paragraphs, entity-first structure, and extractable answers that AI systems prefer to cite.</p>
    </div>
  </div>

  <div class="divider"></div>

  <h2 class="section-hed">Our core <em>methodology.</em></h2>

  <p class="prose"><strong>AI citation is the new rank.</strong> Businesses that appear in AI-generated answers earn the click, the credibility, and the user&#8217;s intent &#8212; before they reach a ranked list. The infrastructure that earns citations is architectural, not content-volume based: pages need to define entities clearly, confirm geographic scope, provide schema confirmations, and structure sentences for extraction.</p>

  <p class="prose">The AI Citation Engine™ was built directly from this research: deploy the complete six-layer citation infrastructure &#8212; page architecture, entity definition, schema, AI guidance (llms.txt), internal link graph, and programmatic coverage &#8212; across an entire service area, not just a homepage.</p>

  <ul class="signal-list">
    <li><strong>AI systems prefer the clearest source</strong> &#8212; not always the highest-ranked one</li>
    <li><strong>Citation is earned at the sentence level</strong> &#8212; any passage must be extractable alone</li>
    <li><strong>Geographic coverage requires page coverage</strong> &#8212; one city, one service, one page</li>
    <li><strong>Schema is a trust signal</strong> &#8212; JSON-LD tells AI systems what type of entity is on the page</li>
    <li><strong>Repetition without contradiction compounds authority</strong> &#8212; identical definitions across pages reinforce the canonical source</li>
  </ul>

  <div class="divider"></div>

  <h2 class="section-hed">Concepts developed <em>here.</em></h2>

  <nav class="concept-links" aria-label="Concepts from SEOAIco">
    <a href="{{ route('what-is-ai-search-optimization') }}" class="concept-link">
      <span class="concept-link-label">Foundational Definition</span>
      <span class="concept-link-title">What Is AI Search Optimization?</span>
    </a>
    <a href="{{ route('ai-search-optimization') }}" class="concept-link">
      <span class="concept-link-label">Category Overview</span>
      <span class="concept-link-title">AI Search Optimization</span>
    </a>
    <a href="{{ route('ai-citation-engine') }}" class="concept-link">
      <span class="concept-link-label">Feature System</span>
      <span class="concept-link-title">The AI Citation Engine™</span>
    </a>
    <a href="{{ route('programmatic-seo-platform') }}" class="concept-link">
      <span class="concept-link-label">Infrastructure</span>
      <span class="concept-link-title">Programmatic SEO Platform</span>
    </a>
    <a href="{{ route('chatgpt-seo') }}" class="concept-link">
      <span class="concept-link-label">LLM Discoverability</span>
      <span class="concept-link-title">ChatGPT SEO</span>
    </a>
    <a href="{{ route('local-ai-search') }}" class="concept-link">
      <span class="concept-link-label">Local Discovery</span>
      <span class="concept-link-title">Local AI Search</span>
    </a>
    <a href="{{ route('search-presence-engine') }}" class="concept-link">
      <span class="concept-link-label">Platform</span>
      <span class="concept-link-title">Search Presence Engine</span>
    </a>
    <a href="{{ route('ai-search-optimization-guide') }}" class="concept-link">
      <span class="concept-link-label">Complete Glossary</span>
      <span class="concept-link-title">AI Search Optimization Guide</span>
    </a>
  </nav>

  <div class="page-cta">
    <span class="page-cta-eye">Work with SEO AI Co™</span>
    <h2>Deploy the <em>AI Citation Engine™</em></h2>
    <p>Book a market review to see where your business has citation gaps across Google AI Overviews, ChatGPT, and Perplexity &#8212; and how the AI Citation Engine™ closes them.</p>
    <a href="{{ route('book.index') }}" class="cta-btn">Book a Market Review</a>
    <a href="{{ route('ai-citation-engine') }}" class="cta-ghost">See how the AI Citation Engine™ works →</a>
  </div>

</article>

<footer>
  <a href="/" class="logo" style="opacity:.5">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; {{ date('Y') }} SEO AI Co™ &middot; Programmatic AI SEO Systems</span>
  <nav class="footer-links">
    <a href="{{ route('ai-search-optimization') }}">AI Search</a>
    <a href="{{ route('ai-citation-engine') }}">AI Citation Engine™</a>
    <a href="{{ route('book.index') }}">Book</a>
    <a href="{{ route('privacy') }}">Privacy</a>
  </nav>
</footer>

@include('components.tm-style')
</body>
</html>
