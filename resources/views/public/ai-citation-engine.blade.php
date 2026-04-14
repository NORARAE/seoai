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
<title>The AI Citation Engine™ — How SEOAIco Structures Content for AI Citation | SEO AI Co™</title>
<meta name="description" content="The AI Citation Engine™ is the infrastructure layer that structures web content for extraction and citation by AI systems — Google AI Overviews, ChatGPT, Perplexity, and Gemini. Built for local service businesses.">
<link rel="canonical" href="{{ url('/ai-citation-engine') }}">
<meta property="og:type" content="article">
<meta property="og:site_name" content="SEO AI Co™">
<meta property="og:title" content="The AI Citation Engine™ — How SEOAIco Structures Content for AI Citation">
<meta property="og:description" content="The AI Citation Engine™ is the infrastructure layer that structures web content for extraction and citation by AI systems — Google AI Overviews, ChatGPT, Perplexity, and Gemini.">
<meta property="og:url" content="{{ url('/ai-citation-engine') }}">
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type'       => 'Article',
            '@id'         => url('/ai-citation-engine') . '#article',
            'url'         => url('/ai-citation-engine'),
            'headline'    => 'The AI Citation Engine™',
            'description' => 'The AI Citation Engine™ is the infrastructure layer that structures web content for extraction and citation by AI systems — Google AI Overviews, ChatGPT, Perplexity, and Gemini.',
            'author'      => ['@type' => 'Organization', 'name' => 'SEOAIco', 'url' => 'https://seoaico.com'],
            'publisher'   => ['@type' => 'Organization', 'name' => 'SEO AI Co™', 'url' => url('/')],
            'isPartOf'    => ['@id' => url('/') . '#website'],
            'about'       => ['@type' => 'Thing', 'name' => 'AI Citation Engine', 'description' => 'Infrastructure that structures web content for extraction and citation by AI-powered search systems.'],
            'mentions'    => [
                ['@type' => 'WebPage', 'name' => 'What Is AI Search Optimization?', 'url' => url('/what-is-ai-search-optimization')],
                ['@type' => 'WebPage', 'name' => 'AI Search Optimization', 'url' => url('/ai-search-optimization')],
                ['@type' => 'WebPage', 'name' => 'Programmatic SEO Platform', 'url' => url('/programmatic-seo-platform')],
                ['@type' => 'WebPage', 'name' => 'ChatGPT SEO', 'url' => url('/chatgpt-seo')],
                ['@type' => 'WebPage', 'name' => 'Local AI Search', 'url' => url('/local-ai-search')],
                ['@type' => 'WebPage', 'name' => 'Search Presence Engine', 'url' => url('/search-presence-engine')],
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'AI Search Optimization', 'item' => url('/ai-search-optimization')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => 'AI Citation Engine™', 'item' => url('/ai-citation-engine')],
            ],
        ],
        [
            '@type'       => 'DefinedTerm',
            'name'        => 'AI Citation Engine',
            'url'         => url('/ai-citation-engine'),
            'description' => 'The infrastructure layer that generates structured pages, applies schema, establishes entity relationships, and optimizes content at the sentence level for retrieval in AI-generated answers — operating across Google AI Overviews, ChatGPT, Perplexity, and Gemini.',
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
.eyebrow{font-size:.7rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);opacity:.8;margin-bottom:22px}
h1{font-family:'Cormorant Garamond',serif;font-size:clamp(2.6rem,5vw,3.8rem);font-weight:300;line-height:1.1;color:var(--ivory);margin-bottom:28px}
h1 em{font-style:italic;color:var(--gold-lt)}
.lead{font-size:1.08rem;color:rgba(237,232,222,.85);line-height:1.82;margin-bottom:36px;border-left:2px solid var(--gold);padding-left:20px}
.section-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.7rem,3vw,2.4rem);font-weight:300;color:var(--ivory);margin:52px 0 18px;line-height:1.2}
.section-hed em{font-style:italic;color:var(--gold-lt)}
.prose{font-size:.97rem;color:var(--muted);line-height:1.88;margin-bottom:22px}
.prose strong{color:var(--ivory);font-weight:400}
.prose a{color:var(--gold);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.3);transition:border-color .2s}
.prose a:hover{border-color:var(--gold)}
.divider{height:1px;background:linear-gradient(to right,transparent,rgba(200,168,75,.12),transparent);margin:52px 0}
/* Definition blocks */
.def-card{background:var(--card);border:1px solid var(--border);border-radius:6px;padding:28px 32px;margin:32px 0}
.def-card-label{font-size:.65rem;letter-spacing:.18em;text-transform:uppercase;color:var(--gold);opacity:.7;margin-bottom:10px}
.def-card-text{font-size:1.02rem;color:rgba(237,232,222,.92);line-height:1.78}
/* Snippet bands */
.snippet-band{margin:0 0 28px;display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:rgba(200,168,75,.06)}
.snippet-item{background:var(--card);padding:16px 18px}
.snippet-item-label{font-size:.52rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);opacity:.65;margin-bottom:6px}
.snippet-item-text{font-size:.88rem;color:rgba(237,232,222,.85);line-height:1.6}
.byline{font-size:.8rem;color:var(--muted);margin-bottom:28px}
.byline a{color:var(--gold);text-decoration:none}
.byline a:hover{text-decoration:underline}
@media(max-width:600px){.snippet-band{grid-template-columns:1fr}}
/* Angle blocks */
.def-angles{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:28px 0}
.def-angle{background:rgba(200,168,75,.04);border:1px solid rgba(200,168,75,.08);border-radius:4px;padding:20px 22px}
.def-angle-label{font-size:.6rem;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);opacity:.65;margin-bottom:8px}
.def-angle-text{font-size:.9rem;color:rgba(237,232,222,.78);line-height:1.72}
@media(max-width:600px){.def-angles{grid-template-columns:1fr}}
/* Citation block */
.citation-block{background:rgba(200,168,75,.035);border-left:3px solid rgba(200,168,75,.35);border-radius:0 4px 4px 0;padding:22px 26px;margin:32px 0}
.citation-block-label{font-size:.6rem;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);opacity:.65;margin-bottom:10px}
.citation-block-text{font-size:.93rem;color:rgba(237,232,222,.80);line-height:1.78;font-style:italic}
/* Comparison table */
.compare-table{width:100%;border-collapse:collapse;margin:32px 0;font-size:.92rem}
.compare-table thead th{padding:14px 18px;text-align:left;border-bottom:1px solid rgba(200,168,75,.15);color:var(--gold);font-weight:400;letter-spacing:.04em}
.compare-table thead th:first-child{color:var(--muted)}
.compare-table tbody td{padding:13px 18px;border-bottom:1px solid rgba(200,168,75,.06);color:var(--muted);vertical-align:top;line-height:1.65}
.compare-table tbody td:nth-child(2){color:rgba(237,232,222,.88)}
.compare-table tbody tr:last-child td{border-bottom:none}
/* Components grid */
.components-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:28px 0}
.component-card{background:var(--card);border:1px solid var(--border);border-radius:5px;padding:22px 24px;transition:border-color .25s,transform .25s cubic-bezier(.23,1,.32,1),box-shadow .25s}
.component-card:hover{border-color:rgba(200,168,75,.18);transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.3)}
.component-card-num{font-size:.7rem;letter-spacing:.14em;color:var(--gold);opacity:.6;margin-bottom:6px}
.component-card-title{font-size:.97rem;color:var(--ivory);font-weight:400;margin-bottom:8px}
.component-card-text{font-size:.85rem;color:var(--muted);line-height:1.7}
@media(max-width:600px){.components-grid{grid-template-columns:1fr}}
/* In practice */
.in-practice{background:rgba(200,168,75,.03);border:1px solid rgba(200,168,75,.09);border-radius:5px;padding:26px 30px;margin:32px 0}
.in-practice-label{font-size:.62rem;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);opacity:.65;margin-bottom:12px}
.in-practice-text{font-size:.93rem;color:rgba(237,232,222,.82);line-height:1.8}
/* Related grid */
.related-grid{display:grid;grid-template-columns:1fr 1fr;gap:2px;margin:28px 0}
.related-item{background:var(--card);border:1px solid var(--border);padding:18px 22px;text-decoration:none;transition:border-color .2s,background .2s}
.related-item:hover{border-color:rgba(200,168,75,.22);background:rgba(200,168,75,.04)}
.related-label{display:block;font-size:.6rem;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);opacity:.65;margin-bottom:5px}
.related-title{display:block;font-size:.95rem;color:var(--ivory);line-height:1.35}
@media(max-width:600px){.related-grid{grid-template-columns:1fr}}
/* CTA */
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
@media(max-width:640px){article{padding:48px 24px 72px}nav.site-nav{padding:18px 24px}.nav-links{gap:16px}}
</style>
</head>
<body>

<nav class="site-nav" aria-label="Site navigation">
  <a href="{{ url('/') }}" class="logo" aria-label="SEO AI Co home">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <ul class="nav-links">
    <li><a href="{{ route('ai-search-optimization') }}">AI Search</a></li>
    <li><a href="{{ route('how-it-works') }}">How It Works</a></li>
    <li><a href="{{ route('book.index') }}">Book</a></li>
  </ul>
</nav>

<article itemscope itemtype="https://schema.org/Article">

  <p class="eyebrow">Feature</p>
  <h1>The <em>AI Citation Engine™</em></h1>
  <p class="byline">SEOAIco Editorial Team</p>

  <p class="lead">The AI Citation Engine™ is the infrastructure layer that makes web content the source AI systems cite — not just a page they rank. It generates structured pages, applies schema, establishes entity relationships, and optimizes content at the sentence level for retrieval in AI-generated answers.</p>

  <div class="def-card">
    <p class="def-card-label">Definition</p>
    <p class="def-card-text">The <strong>AI Citation Engine™</strong> is a proprietary content and technical infrastructure system that structures web pages for extraction and citation by AI-powered search systems — including Google AI Overviews, ChatGPT, Perplexity, and Gemini. It operates across page architecture, entity definition, structured data, AI guidance, and internal link topology simultaneously.</p>
  </div>

  <div class="snippet-band" aria-label="Quick answers">
    <div class="snippet-item">
      <p class="snippet-item-label">Short answer</p>
      <p class="snippet-item-text">The AI Citation Engine™ makes web content the source AI systems cite &#8212; deploying structured pages, schema, entity relationships, and sentence-level content optimized for retrieval.</p>
    </div>
    <div class="snippet-item">
      <p class="snippet-item-label">Best answer</p>
      <p class="snippet-item-text">The AI Citation Engine™ operates across six layers &#8212; page architecture, entity definition, structured data, AI guidance, internal link topology, and programmatic content &#8212; to earn citations in AI-generated answers.</p>
    </div>
    <div class="snippet-item">
      <p class="snippet-item-label">One sentence</p>
      <p class="snippet-item-text">The AI Citation Engine™ is a proprietary infrastructure system that structures web pages for extraction and citation by AI-powered search systems, including Google AI Overviews, ChatGPT, Perplexity, and Gemini.</p>
    </div>
  </div>

  <div class="def-angles">
    <div class="def-angle">
      <p class="def-angle-label">In simple terms</p>
      <p class="def-angle-text">It's the system that makes AI search engines quote your site instead of a competitor's.</p>
    </div>
    <div class="def-angle">
      <p class="def-angle-label">Key takeaway</p>
      <p class="def-angle-text">Traditional SEO earns rankings. The AI Citation Engine™ earns citations — the new unit of AI search visibility.</p>
    </div>
  </div>

  <div class="divider"></div>

  <h2 class="section-hed">How it <em>works.</em></h2>

  <p class="prose">AI systems — including ChatGPT, Google AI Overviews, and Perplexity — do not rank results the way standard search engines do. They retrieve candidate pages, evaluate extractability, and select specific passages to include in generated answers. The AI Citation Engine™ is designed around all five layers of that retrieval process.</p>

  <div class="components-grid">
    <div class="component-card">
      <p class="component-card-num">Layer 01</p>
      <p class="component-card-title">Structured Page Architecture</p>
      <p class="component-card-text">Pages are built with heading hierarchies, self-contained paragraphs, and entity-first sentence construction — matching the extraction patterns AI systems use when pulling answers.</p>
    </div>
    <div class="component-card">
      <p class="component-card-num">Layer 02</p>
      <p class="component-card-title">Entity Definition</p>
      <p class="component-card-text">Every page defines who the business is, what it does, and where it operates — with the precision AI knowledge graphs require to connect businesses to service-location queries.</p>
    </div>
    <div class="component-card">
      <p class="component-card-num">Layer 03</p>
      <p class="component-card-title">Schema Layer</p>
      <p class="component-card-text">JSON-LD structured data — LocalBusiness, Service, FAQPage, DefinedTerm, BreadcrumbList — signals entity type, service scope, and geographic authority to every AI and search system that processes the page.</p>
    </div>
    <div class="component-card">
      <p class="component-card-num">Layer 04</p>
      <p class="component-card-title">AI Guidance Layer</p>
      <p class="component-card-text">An llms.txt file at the domain root instructs AI crawlers on the site's structure, authority topics, and preferred citation surfaces — a direct signal layer that standard SEO tools don't address.</p>
    </div>
    <div class="component-card">
      <p class="component-card-num">Layer 05</p>
      <p class="component-card-title">Internal Link Graph</p>
      <p class="component-card-text">Every page connects into a structured internal graph — creating topical clusters and entity relationships that AI systems use to establish authority and cross-reference coverage.</p>
    </div>
    <div class="component-card">
      <p class="component-card-num">Layer 06</p>
      <p class="component-card-title">Coverage at Scale</p>
      <p class="component-card-text">The engine generates one structured, schema-rich page per service-location combination — deploying citation infrastructure across an entire service area, not just a homepage.</p>
    </div>
  </div>

  <div class="in-practice">
    <p class="in-practice-label">In practice</p>
    <p class="in-practice-text">A plumbing business serving 12 cities with 8 services has 96 potential service-location intersections. The AI Citation Engine™ deploys a structured, citation-ready page for each one — so when someone asks ChatGPT "who's the best plumber in [city]?", the business has a page that AI systems have already indexed, parsed, and can cite.</p>
  </div>

  <div class="divider"></div>

  <h2 class="section-hed">Why it <em>matters.</em></h2>

  <p class="prose">AI search is not replacing traditional search — it's being layered on top of it. Google AI Overviews appear above organic results. ChatGPT answers questions directly. Perplexity synthesizes sources in real time. The businesses that get cited in those answers earn visibility that standard rankings don't deliver.</p>

  <p class="prose"><strong>Citation is the new rank.</strong> A business cited in an AI-generated answer gets the click, the credibility signal, and the user's attention — without appearing in the traditional first-position result. The AI Citation Engine™ is built specifically to earn those citations at scale.</p>

  <div class="citation-block">
    <p class="citation-block-label">Citation advantage</p>
    <p class="citation-block-text">"The highest-ranked page is not always the page AI systems cite. They select the passage that is clearest, most self-contained, and most directly relevant to the query — regardless of domain authority or traditional ranking position."</p>
  </div>

  <div class="divider"></div>

  <h2 class="section-hed">AI Citation Engine™ vs <em>traditional SEO tools.</em></h2>

  <table class="compare-table" aria-label="AI Citation Engine vs Traditional SEO comparison">
    <thead>
      <tr>
        <th>Dimension</th>
        <th>Traditional SEO Tool</th>
        <th style="color:var(--gold-lt)">AI Citation Engine™</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Primary output</td>
        <td>Keyword rankings in Google</td>
        <td>Citations in AI-generated answers</td>
      </tr>
      <tr>
        <td>Content unit</td>
        <td>Blog posts and landing pages</td>
        <td>Structured service-location pages</td>
      </tr>
      <tr>
        <td>Structured data</td>
        <td>Optional add-on</td>
        <td>Core infrastructure requirement</td>
      </tr>
      <tr>
        <td>AI guidance</td>
        <td>Not addressed</td>
        <td>llms.txt layer + crawler directives</td>
      </tr>
      <tr>
        <td>Geographic coverage</td>
        <td>Manual, city by city</td>
        <td>Programmatic, full service area</td>
      </tr>
      <tr>
        <td>Entity clarity</td>
        <td>Implied through context</td>
        <td>Explicit entity definition on every page</td>
      </tr>
      <tr>
        <td>Compound effect</td>
        <td>Rankings decay without maintenance</td>
        <td>Citation authority builds over deployment lifetime</td>
      </tr>
    </tbody>
  </table>

  <div class="divider"></div>

  <h2 class="section-hed">The five components <em>in depth.</em></h2>

  <p class="prose"><strong>Structured page architecture</strong> is the foundation. AI systems extract information at the paragraph level — not the page level. Every page produced by the AI Citation Engine™ is built with self-contained paragraphs, leading with the most specific and relevant information first, so extraction can happen from any entry point in the document.</p>

  <p class="prose"><strong>Entity definition</strong> is what connects the business to AI knowledge graphs. When an AI system receives a query about a specific service in a specific city, it looks for entities it can associate with that query. The AI Citation Engine™ establishes entity identity — business name, service category, geographic scope — at every layer of the page.</p>

  <p class="prose"><strong>The schema layer</strong> is the machine-readable confirmation of everything the page content establishes. JSON-LD markup tells AI and search systems what type of entity is on the page, what services it provides, what area it covers, and how it relates to other entities in the graph.</p>

  <p class="prose"><strong>The AI guidance layer (llms.txt)</strong> is a domain-level signal that tells AI crawlers — including those used by ChatGPT and Perplexity — how the site is organized, which pages represent the site's core authority topics, and what the site is the authoritative source on. Standard SEO tools don't address this layer at all.</p>

  <p class="prose"><strong>The internal link graph</strong> connects every page into a topical cluster — reinforcing entity relationships and geographic coverage patterns in a structure that mirrors how AI systems model relationships between pages, topics, and locations.</p>

  <div class="divider"></div>

  @php
  $aceFaqs = [
    [
      'question' => 'What is the AI Citation Engine™?',
      'answer'   => 'The AI Citation Engine™ is a proprietary infrastructure system developed by SEO AI Co™ that structures web content for extraction and citation by AI-powered search systems — including Google AI Overviews, ChatGPT, Perplexity, and Gemini. It operates across six layers: structured page architecture, entity definition, schema markup, AI guidance (llms.txt), internal link graph, and programmatic coverage at scale.',
    ],
    [
      'question' => 'How does the AI Citation Engine™ differ from standard SEO?',
      'answer'   => 'Standard SEO is optimized for ranking — getting a page to position 1 in Google\'s blue-link results. The AI Citation Engine™ is optimized for citation — getting a page\'s content extracted and referenced in AI-generated answers, which appear above or instead of traditional rankings. The two share some foundation (technical structure, authority signals) but the AI Citation Engine™ addresses layers — entity definition, llms.txt, extraction-optimized sentence construction — that standard SEO tools do not.',
    ],
    [
      'question' => 'Which AI systems does the AI Citation Engine™ target?',
      'answer'   => 'The AI Citation Engine™ is designed to earn citations across Google AI Overviews, ChatGPT (web search mode), Perplexity, Gemini, and any AI-powered search interface that retrieves and synthesizes web content. The structural requirements for citation overlap significantly across all of these systems — entity clarity, extractable content, and machine-readable structured data are universal requirements.',
    ],
    [
      'question' => 'Does a business need a new website to use the AI Citation Engine™?',
      'answer'   => 'No. The AI Citation Engine™ is deployed into an existing website — adding structured service-location pages to the current domain. This means the authority built by the existing site carries forward, and the new pages benefit from the domain\'s history immediately. For sites that need structural work first, SEO AI Co™ also offers website development as part of an integrated engagement.',
    ],
  ];
  @endphp

  <x-faq-section heading="Common questions about the AI Citation Engine™" :faqs="$aceFaqs" />

  <div class="divider"></div>

  <h2 class="section-hed">Explore the <em>connected infrastructure.</em></h2>

  <nav class="related-grid" aria-label="Related topics">
    <a href="{{ route('what-is-ai-search-optimization') }}" class="related-item">
      <span class="related-label">Definition</span>
      <span class="related-title">What Is AI Search Optimization?</span>
    </a>
    <a href="{{ route('ai-search-optimization') }}" class="related-item">
      <span class="related-label">Category Overview</span>
      <span class="related-title">AI Search Optimization</span>
    </a>
    <a href="{{ route('programmatic-seo-platform') }}" class="related-item">
      <span class="related-label">Infrastructure</span>
      <span class="related-title">Programmatic SEO Platform</span>
    </a>
    <a href="{{ route('chatgpt-seo') }}" class="related-item">
      <span class="related-label">LLM Discoverability</span>
      <span class="related-title">ChatGPT SEO</span>
    </a>
    <a href="{{ route('local-ai-search') }}" class="related-item">
      <span class="related-label">Local Discovery</span>
      <span class="related-title">Local AI Search</span>
    </a>
    <a href="{{ route('search-presence-engine') }}" class="related-item">
      <span class="related-label">Platform</span>
      <span class="related-title">The Search Presence Engine</span>
    </a>
  </nav>

  <div class="page-cta">
    <span class="page-cta-eye">Make your site the answer</span>
    <h2>Get cited by AI — <em>not just ranked.</em></h2>
    <p>We deploy the AI Citation Engine™ into your existing site — structuring your service pages for extraction and citation across every AI search platform that matters.</p>
    <a href="{{ route('book.index') }}" class="cta-btn">Get cited by AI</a>
    <a href="{{ route('how-it-works') }}" class="cta-ghost">See how it works →</a>
  </div>

</article>

<footer>
  <a href="/" class="logo" style="opacity:.5">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; {{ date('Y') }} SEO AI Co™ &middot; Programmatic AI SEO Systems</span>
  <nav class="footer-links">
    <a href="{{ route('ai-search-optimization') }}">AI Search</a>
    <a href="{{ route('how-it-works') }}">How It Works</a>
    <a href="{{ route('book.index') }}">Book</a>
    <a href="{{ route('privacy') }}">Privacy</a>
  </nav>
</footer>

@include('components.tm-style')
</body>
</html>
