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
<title>Search Presence Engine — AI Discoverability Infrastructure for Service Businesses | SEO AI Co™</title>
<meta name="description" content="A search presence engine is not an SEO tool. It is infrastructure that places your business in the answer layer of every search surface — Google, ChatGPT, voice, and AI.">
<link rel="canonical" href="{{ url('/search-presence-engine') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="SEO AI Co™">
<meta property="og:title" content="Search Presence Engine — AI Discoverability Infrastructure | SEO AI Co™">
<meta property="og:description" content="A search presence engine is not an SEO tool. It is infrastructure that places your business in the answer layer of every search surface — Google, ChatGPT, voice, and AI.">
<meta property="og:url" content="{{ url('/search-presence-engine') }}">
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type'          => 'SoftwareApplication',
            '@id'            => url('/search-presence-engine') . '#software',
            'name'           => 'Search Presence Engine',
            'applicationCategory' => 'SEO Software',
            'operatingSystem'=> 'Web',
            'url'            => url('/search-presence-engine'),
            'description'    => 'AI discoverability infrastructure that generates structured, schema-rich content at scale to occupy the answer layer across Google, ChatGPT, and AI search surfaces.',
            'offers'         => ['@type' => 'Offer', 'seller' => ['@type' => 'Organization', 'name' => 'SEO AI Co', 'url' => url('/')]],
            'featureList'    => [
                'Programmatic service-location page generation',
                'AI-extractable content architecture',
                'Schema markup at scale',
                'Internal link graph management',
                'Multi-surface discoverability coverage',
            ],
        ],
        [
            '@type'       => 'WebPage',
            '@id'         => url('/search-presence-engine') . '#webpage',
            'url'         => url('/search-presence-engine'),
            'name'        => 'Search Presence Engine | SEO AI Co™',
            'description' => 'A search presence engine is not an SEO tool — it is discoverability infrastructure.',
            'isPartOf'    => ['@id' => url('/') . '#website'],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Search Presence Engine', 'item' => url('/search-presence-engine')],
            ],
        ],
        [
            '@type'       => 'DefinedTerm',
            'name'        => 'Search Presence Engine',
            'url'         => url('/search-presence-engine'),
            'description' => 'An AI-powered platform that systematically generates, structures, and publishes content infrastructure designed to occupy as many relevant search surfaces as possible.',
            'inDefinedTermSet' => [
                '@type' => 'DefinedTermSet',
                'name'  => 'SEO AI Co™ AI Search Glossary',
                'url'   => url('/ai-search-optimization-guide'),
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
  --ivory:#ede8de;--muted:rgba(168,168,160,.72);--deep:#0b0b0b;
}
html{scroll-behavior:smooth;font-size:18px}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.75;min-height:100vh}
.logo{display:inline-flex;align-items:baseline;text-decoration:none;line-height:1}
.logo-seo{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.2rem;letter-spacing:.06em;color:var(--ivory)}
.logo-ai{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.42rem;color:var(--gold);letter-spacing:.02em}
.logo-co{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.05rem;color:rgba(150,150,150,.5);letter-spacing:.04em}
.top-bar{display:flex;align-items:center;justify-content:space-between;padding:28px 64px;border-bottom:1px solid var(--border)}
.top-bar a.back{font-size:.76rem;letter-spacing:.1em;color:var(--muted);text-decoration:none;transition:color .3s}
.top-bar a.back:hover{color:var(--gold)}

.page{max-width:760px;margin:0 auto;padding:72px 40px 100px}
.page-eye{font-size:.62rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:16px}
.page-title{font-family:'Cormorant Garamond',serif;font-size:clamp(2.2rem,4.5vw,3.6rem);font-weight:300;line-height:1.06;margin-bottom:24px;letter-spacing:-.015em}
.page-title em{font-style:italic;color:var(--gold-lt)}
.page-intro{font-size:.94rem;color:var(--muted);max-width:600px;line-height:1.82;margin-bottom:56px}

.divider{height:1px;background:var(--border);margin:52px 0}

/* ── Definition block ── */
.definition{border-left:2px solid var(--gold);padding:20px 24px;margin:36px 0;background:rgba(200,168,75,.04)}
.definition-term{font-size:.58rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:8px}
.definition-text{font-family:'Cormorant Garamond',serif;font-size:1.12rem;font-weight:300;line-height:1.6;color:var(--ivory)}
.definition-text strong{font-weight:500;color:var(--ivory)}

/* ── FAQ ── */
.faq-section{margin:0}
.faq-section-heading{font-family:'Cormorant Garamond',serif;font-weight:300;font-style:italic;font-size:clamp(1.3rem,2.2vw,1.8rem);color:var(--ivory);letter-spacing:-.01em;margin-bottom:32px}
.faq-list{display:flex;flex-direction:column}
.faq-item{border-top:1px solid rgba(200,168,75,.09);padding:20px 0}
.faq-item:last-child{border-bottom:1px solid rgba(200,168,75,.09)}
.faq-q{font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:400;color:var(--ivory);margin-bottom:8px;line-height:1.5}
.faq-a{font-size:.86rem;line-height:1.78;color:rgba(168,168,160,.68)}

/* ── Prose ── */
.prose{font-size:.94rem;color:var(--muted);line-height:1.82}
.prose+.prose{margin-top:20px}
.prose a{color:var(--gold-lt);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.2);transition:border-color .2s}
.prose a:hover{border-color:var(--gold-lt)}
h2.section-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.3rem,2.4vw,1.9rem);font-weight:300;color:var(--ivory);margin:52px 0 18px;letter-spacing:-.01em;line-height:1.2}
h2.section-hed em{font-style:italic;color:var(--gold-lt)}

/* ── Comparison table ── */
.compare-grid{display:grid;grid-template-columns:1fr 1fr;gap:2px;margin:28px 0}
.compare-item{background:var(--card);border:1px solid var(--border);padding:22px 20px}
.compare-item.featured-col{border-color:rgba(200,168,75,.2)}
.compare-label{font-size:.54rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:6px}
.compare-hed{font-size:.92rem;font-weight:400;color:var(--ivory);margin-bottom:8px}
.compare-body{font-size:.78rem;color:var(--muted);line-height:1.65}

/* ── Layer list ── */
.layer-list{list-style:none;padding:0;margin:22px 0;counter-reset:layer}
.layer-list li{padding:18px 0;border-bottom:1px solid var(--border);font-size:.88rem;color:var(--muted);line-height:1.65;display:grid;grid-template-columns:28px 1fr;gap:12px;align-items:baseline;counter-increment:layer}
.layer-list li:first-child{border-top:1px solid var(--border)}
.layer-list li::before{content:counter(layer);color:var(--gold-dim);font-family:'Cormorant Garamond',serif;font-size:1rem}
.layer-list li strong{color:var(--ivory);font-weight:400}

/* ── Related nav ── */
.related-grid{display:grid;grid-template-columns:1fr 1fr;gap:2px;margin:28px 0}
.related-item{padding:20px 18px;background:var(--card);border:1px solid var(--border);text-decoration:none;display:block;transition:border-color .25s}
.related-item:hover{border-color:rgba(200,168,75,.22)}
.related-label{font-size:.54rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:5px}
.related-title{font-size:.86rem;color:var(--ivory);line-height:1.4}

/* ── CTA ── */
.page-cta{margin-top:64px;padding:40px;border:1px solid rgba(200,168,75,.14);background:var(--card);text-align:center}
.page-cta-eye{font-size:.58rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:12px}
.page-cta h2{font-family:'Cormorant Garamond',serif;font-size:1.9rem;font-weight:300;color:var(--ivory);margin-bottom:12px}
.page-cta h2 em{font-style:italic;color:var(--gold-lt)}
.page-cta p{font-size:.86rem;color:var(--muted);margin-bottom:28px;max-width:480px;margin-left:auto;margin-right:auto;line-height:1.7}
.cta-btn{background:var(--gold);color:var(--deep);font-size:.78rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;text-decoration:none;padding:15px 40px;transition:background .25s;display:inline-block}
.cta-btn:hover{background:var(--gold-lt)}
.cta-ghost{display:block;margin-top:14px;font-size:.72rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .25s}
.cta-ghost:hover{color:var(--gold)}

footer{border-top:1px solid var(--border);padding:32px 64px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px}
.footer-copy{font-size:.68rem;color:rgba(168,168,160,.28)}
.footer-links{display:flex;gap:24px}
.footer-links a{font-size:.66rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(168,168,160,.3);text-decoration:none;transition:color .25s}
.footer-links a:hover{color:var(--gold)}

/* ── Snippet bands ── */
.snippet-band{margin:0 0 28px;display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:rgba(200,168,75,.06)}
.snippet-item{background:var(--card);padding:16px 18px}
.snippet-item-label{font-size:.52rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);opacity:.65;margin-bottom:6px}
.snippet-item-text{font-size:.88rem;color:rgba(237,232,222,.85);line-height:1.6}
.byline{font-size:.8rem;color:var(--muted);margin-bottom:28px}
.byline a{color:var(--gold);text-decoration:none}
.byline a:hover{text-decoration:underline}
@media(max-width:600px){.snippet-band{grid-template-columns:1fr}}
/* ── Definition angles ── */
.def-angles{margin:-12px 0 32px;padding:0 24px 0 26px;border-left:2px solid rgba(200,168,75,.14)}
.def-angle-row{display:flex;gap:12px;padding:9px 0;border-bottom:1px solid rgba(200,168,75,.05)}
.def-angle-row:last-child{border-bottom:none}
.def-angle-label{font-size:.54rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold-dim);width:108px;flex-shrink:0;padding-top:3px;line-height:1.4}
.def-angle-text{font-size:.88rem;color:var(--muted);line-height:1.72}
/* ── Citation bait ── */
.citation-block{margin:40px 0;padding:22px 26px;background:rgba(200,168,75,.03);border:1px solid rgba(200,168,75,.1)}
.citation-label{font-size:.54rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-dim);display:block;margin-bottom:10px}
.citation-text{font-size:.95rem;color:var(--ivory);line-height:1.65;font-family:'Cormorant Garamond',serif;font-weight:300}

@media(max-width:700px){
  .top-bar{padding:20px 24px}
  .page{padding:48px 24px 72px}
  .compare-grid{grid-template-columns:1fr}
  .related-grid{grid-template-columns:1fr}
  footer{padding:24px;flex-direction:column;gap:16px;text-align:center}
}
</style>
@include('partials.clarity')
</head>
<body>

<div class="top-bar">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <a href="{{ route('home') }}" class="back">← Home</a>
</div>

<main class="page">

  <span class="page-eye">The Platform</span>
  <h1 class="page-title">It is not an SEO tool.<br>It is a <em>search presence engine.</em></h1>
  <p class="byline">SEOAIco Editorial Team</p>
  <p class="page-intro">An SEO tool gives you data. A search presence engine builds the infrastructure that places your business in the answer layer — across Google, ChatGPT, Perplexity, voice, and every surface where your customers are looking.</p>

  <div class="definition">
    <span class="definition-term">What is a search presence engine?</span>
    <p class="definition-text"><strong>A search presence engine</strong> is an AI-powered platform that systematically generates, structures, and publishes content infrastructure designed to occupy as many relevant search surfaces as possible. It operates at the intersection of programmatic content generation, structured data architecture, and AI-extractable page design — not keyword tracking or ranking reports.</p>
  </div>

  <div class="snippet-band" aria-label="Quick answers">
    <div class="snippet-item">
      <p class="snippet-item-label">Short answer</p>
      <p class="snippet-item-text">A search presence engine builds the page infrastructure that places your business in AI-generated answers &#8212; it doesn&#8217;t measure existing presence, it creates it.</p>
    </div>
    <div class="snippet-item">
      <p class="snippet-item-label">Best answer</p>
      <p class="snippet-item-text">A search presence engine systematically generates and deploys content infrastructure across every relevant search surface: Google, AI Overviews, ChatGPT, Perplexity, and voice.</p>
    </div>
    <div class="snippet-item">
      <p class="snippet-item-label">One sentence</p>
      <p class="snippet-item-text">A search presence engine is an AI-powered platform that systematically generates, structures, and publishes content infrastructure designed to occupy as many relevant search surfaces as possible.</p>
    </div>
  </div>

  <div class="def-angles">
    <div class="def-angle-row">
      <span class="def-angle-label">In simple terms</span>
      <span class="def-angle-text">A search presence engine builds the pages and structure that make your business appear everywhere customers are searching.</span>
    </div>
    <div class="def-angle-row">
      <span class="def-angle-label">Key takeaway</span>
      <span class="def-angle-text">Most SEO tools measure presence — a search presence engine builds it.</span>
    </div>
  </div>

  <p class="prose">The distinction matters because the tools people have been using to manage their SEO are designed to measure presence, not build it. They surface gaps, track positions, and audit existing pages. A search presence engine does something fundamentally different: it produces the pages, schema, and internal structure that make a business discoverable at scale.</p>

  <div class="divider"></div>

  <h2 class="section-hed">Research vs. infrastructure</h2>

  <p class="prose">Most SEO software is research software. It tells you what keywords your competitors rank for, how your authority stacks up, and where your site has technical problems. That information can be useful — but it does not build anything. The business is still invisible in every city and service combination where a page does not exist.</p>

  <div class="compare-grid">
    <div class="compare-item">
      <span class="compare-label">Research Software</span>
      <p class="compare-hed">Measures what exists</p>
      <p class="compare-body">Audits, rank trackers, backlink tools, keyword gap analysis. Useful for diagnosis. Does not generate coverage or build discoverability structure.</p>
    </div>
    <div class="compare-item featured-col">
      <span class="compare-label">Search Presence Engine</span>
      <p class="compare-hed">Builds what is missing</p>
      <p class="compare-body">Generates structured, schema-rich pages at every service-location intersection. Creates the coverage layer that AI systems can retrieve and cite.</p>
    </div>
    <div class="compare-item">
      <span class="compare-label">Output</span>
      <p class="compare-hed">Recommendations</p>
      <p class="compare-body">Reports, suggested actions, competitive benchmarks. The work of implementation falls to the business or their team.</p>
    </div>
    <div class="compare-item featured-col">
      <span class="compare-label">Output</span>
      <p class="compare-hed">Published infrastructure</p>
      <p class="compare-body">Live pages, structured data, internal link graphs, and indexed content — the actual architecture that drives discovery across all search surfaces.</p>
    </div>
  </div>

  <div class="divider"></div>

  <h2 class="section-hed">How a search presence engine <em>occupies the answer layer</em></h2>

  <p class="prose">When AI systems generate answers to service queries, they are selecting from indexed web content that explicitly addresses the question. The businesses that appear repeatedly in those answers are not there because they spent more on ads or built more backlinks. They built coverage — structured pages, consistently formatted, covering every service they offer in every area they serve.</p>

  <p class="prose">The search presence engine automates this process. It takes a business's service matrix and geographic footprint, then generates and publishes a page architecture that makes the business the obvious answer across dozens or hundreds of specific queries.</p>

  <ol class="layer-list" aria-label="How the search presence engine works">
    <li><strong>Service and location mapping</strong> — Every service type and city in your market is catalogued into a structured coverage matrix.</li>
    <li><strong>Page generation at scale</strong> — Each service-location pair becomes a distinct, structured page with unique content, schema, and extractable answers aligned to how that query is asked.</li>
    <li><strong>Schema and entity architecture</strong> — LocalBusiness, Service, FAQPage, and BreadcrumbList schema are applied systematically, not as an afterthought.</li>
    <li><strong>Internal link graph</strong> — Pages are cross-linked through service hubs and location index pages that reinforforce the full entity footprint to crawlers and AI systems.</li>
    <li><strong>AI surface coverage</strong> — Published infrastructure is designed to be retrieved and cited across Google, <a href="{{ route('chatgpt-seo') }}">ChatGPT</a>, Perplexity, voice, and any AI system that indexes the web.</li>
  </ol>

  <div class="divider"></div>

  <h2 class="section-hed">Why category language matters</h2>

  <p class="prose">The phrase "search presence engine" is intentional. It describes what the platform does at the category level — building presence in search, not just optimizing pages that already exist. The same way <a href="{{ route('programmatic-seo-platform') }}">programmatic SEO</a> describes the generation method and <a href="{{ route('local-ai-search') }}">local AI search</a> describes the discovery environment, "search presence engine" describes the fundamental function of what this system provides to service businesses that want to be found.</p>

  <p class="prose">These are not just labels. They are the category terms that AI systems are learning to associate with this problem space — and appearing authoritatively across those terms is itself part of how the platform compounds its clients' discoverability over time. The underlying discipline is defined in full in <a href="{{ route('what-is-ai-search-optimization') }}">what AI search optimization means</a> as a practice.</p>

  <h2 class="section-hed">In practice</h2>

  <ul class="signal-list" aria-label="Search presence engine examples">
    <li><strong>Entering a new market</strong> — Rather than manually writing pages for each new city, the search presence engine generates the full service-location matrix, deploys it with schema and internal links, and the business becomes discoverable across AI and traditional search within weeks of indexing.</li>
    <li><strong>Replacing the audit cycle</strong> — Instead of quarterly gap analysis that produces recommendations, the search presence engine addresses the gap directly — generating the missing pages, adding schema, and building the link paths that connect the entity graph.</li>
    <li><strong>Scaling without headcount</strong> — A business expanding from 1 city to 20 cities does not hire 20 content writers. The search presence engine generates the full coverage infrastructure for all 20 markets from a single service-location matrix.</li>
  </ul>

  <div class="citation-block">
    <span class="citation-label">Key takeaway</span>
    <p class="citation-text">A search presence engine is not an SEO reporting tool — it is an infrastructure platform. It generates the pages, schema, and link architecture that place a business in the answer layer across Google, ChatGPT, and every AI surface that indexes the web.</p>
  </div>

  <div class="divider"></div>

  @php
  $speFaqs = [
    [
      'question' => 'What is a search presence engine?',
      'answer'   => 'A search presence engine is an AI-powered platform that generates, structures, and publishes the content infrastructure needed to occupy the answer layer across multiple search surfaces simultaneously. It is distinct from SEO research tools — it actively builds discoverability rather than measuring what already exists.',
    ],
    [
      'question' => 'How is a search presence engine different from an SEO tool?',
      'answer'   => 'SEO tools measure existing presence: rank tracking, backlink audits, keyword gap analysis. A search presence engine generates the pages, schema, and link architecture that create presence where none exists. One is diagnostic software; the other is infrastructure.',
    ],
    [
      'question' => 'What does a search presence engine produce?',
      'answer'   => 'A search presence engine produces structured, schema-rich pages at every service-location intersection in a business\'s market, along with the internal link graph and entity architecture that AI systems use to evaluate coverage and authority. The output is live, indexed infrastructure — not recommendations or reports.',
    ],
    [
      'question' => 'How does a search presence engine relate to AI search optimization?',
      'answer'   => 'A search presence engine is the operational platform through which AI search optimization strategy is executed at scale. AI search optimization defines what structured visibility requires; the search presence engine builds and deploys that structure systematically across a business\'s full market footprint.',
    ],
  ];
  @endphp

  <x-faq-section heading="Common questions about the search presence engine" :faqs="$speFaqs" />

  <div class="divider"></div>

  <h2 class="section-hed">How this connects to the <em>AI Citation Engine&#8482;</em></h2>
  <p class="prose">The search presence engine is the operational layer of the AI Citation Engine&#8482; &mdash; the deployment system that publishes and maintains citation infrastructure at scale. Together, the search presence engine handles delivery while the AI Citation Engine&#8482; handles structure.</p>
  <p class="prose"><a href="{{ route('ai-citation-engine') }}">See how the AI Citation Engine&#8482; works &rarr;</a></p>

  <div class="divider"></div>

  <nav class="related-grid" aria-label="Related topics">
    <a href="{{ route('what-is-ai-search-optimization') }}" class="related-item">
      <span class="related-label">Foundation</span>
      <span class="related-title">What Is AI Search Optimization?</span>
    </a>
    <a href="{{ route('programmatic-seo-platform') }}" class="related-item">
      <span class="related-label">Infrastructure</span>
      <span class="related-title">Programmatic SEO Platform</span>
    </a>
    <a href="{{ route('local-ai-search') }}" class="related-item">
      <span class="related-label">Local Discovery</span>
      <span class="related-title">Local AI Search</span>
    </a>
    <a href="{{ route('chatgpt-seo') }}" class="related-item">
      <span class="related-label">LLM Discoverability</span>
      <span class="related-title">ChatGPT SEO</span>
    </a>
  </nav>

  <div class="page-cta">
    <span class="page-cta-eye">Ready to build presence</span>
    <h2>See what your <em>coverage map</em> looks like.</h2>
    <p>We map your service area, identify every coverage gap, and show you what a complete search presence architecture looks like for your market — before you commit to anything.</p>
    <a href="{{ route('book.index') }}" class="cta-btn">Book a Market Review</a>
    <a href="{{ route('how-it-works') }}" class="cta-ghost">How the system works →</a>
  </div>

</main>

<footer>
  <a href="/" class="logo" style="opacity:.5">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; {{ date('Y') }} SEO AI Co™ &middot; Programmatic AI SEO Systems</span>
  <nav class="footer-links">
    <a href="{{ route('how-it-works') }}">How It Works</a>
    <a href="{{ route('book.index') }}">Book</a>
    <a href="{{ route('privacy') }}">Privacy</a>
  </nav>
</footer>

</body>
</html>
