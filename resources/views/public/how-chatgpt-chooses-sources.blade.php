<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>How ChatGPT Chooses Sources — Citation Signals Explained | SEO AI Co™</title>
<meta name="description" content="ChatGPT chooses sources based on entity clarity, structured data, extractable sentences, and topical authority. Understanding these citation signals is the first step to getting your content cited.">
<link rel="canonical" href="{{ url('/how-chatgpt-chooses-sources') }}">
<meta property="og:type" content="article">
<meta property="og:site_name" content="SEO AI Co™">
<meta property="og:title" content="How ChatGPT Chooses Sources — Citation Signals Explained | SEO AI Co™">
<meta property="og:description" content="ChatGPT chooses sources based on entity clarity, structured data, extractable sentences, and topical authority. Understanding these citation signals is the first step to getting your content cited.">
<meta property="og:url" content="{{ url('/how-chatgpt-chooses-sources') }}">
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type'           => 'Article',
            '@id'             => url('/how-chatgpt-chooses-sources') . '#article',
            'headline'        => 'How ChatGPT Chooses Sources — Citation Signals Explained',
            'description'     => 'ChatGPT chooses sources based on entity clarity, structured data, extractable sentences, and topical authority. Understanding these citation signals is the first step to getting your content cited.',
            'url'             => url('/how-chatgpt-chooses-sources'),
            'datePublished'   => '2025-01-01',
            'dateModified'    => date('Y-m-d'),
            'author'          => ['@type' => 'Organization', 'name' => 'SEOAIco', 'url' => 'https://seoaico.com'],
            'publisher'       => ['@type' => 'Organization', '@id' => url('/') . '#organization', 'name' => 'SEO AI Co™'],
            'mainEntityOfPage'=> ['@type' => 'WebPage', '@id' => url('/how-chatgpt-chooses-sources')],
        ],
        [
            '@type'      => 'FAQPage',
            'mainEntity' => [
                ['@type' => 'Question', 'name' => 'How does ChatGPT choose which sources to cite?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'ChatGPT Search selects sources based on retrieval relevance (how closely a page matches the query), entity clarity (how explicitly the page defines its subject), structural signals (schema markup, FAQ blocks, topic-sentence structure), and passage extractability &#8212; whether the key answer can be taken from the page without needing surrounding context.']],
                ['@type' => 'Question', 'name' => 'Does ChatGPT cite the highest-ranked Google result?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Not necessarily. ChatGPT uses its own retrieval pipeline (Bing-powered for ChatGPT Search) and selects sources based on semantic relevance to the query, not solely on Google search rank. A page that is clearly structured and entity-defined may be cited by ChatGPT even if it ranks lower in traditional search results.']],
                ['@type' => 'Question', 'name' => 'What schema markup helps with ChatGPT citation?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'The most effective schema types for AI citation &#8212; including ChatGPT &#8212; are: FAQPage (signals direct-answer content), Article (confirms type and authorship), LocalBusiness or Service (confirms entity type and geographic scope), and DefinedTerm (signals this page defines a specific concept). All should be implemented as JSON-LD in the page head.']],
                ['@type' => 'Question', 'name' => 'How do I get ChatGPT to cite my business?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'To get ChatGPT to cite your business: (1) Create a page that clearly defines your service and location with an entity-first opening sentence. (2) Add FAQPage schema with questions your customers ask. (3) Add LocalBusiness or Service schema confirming your entity type and geographic scope. (4) Structure your content in self-contained paragraphs answerable in isolation. (5) Publish an llms.txt file at your domain root listing your key pages.']],
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'AI Search Optimization Guide', 'item' => url('/ai-search-optimization-guide')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => 'How ChatGPT Chooses Sources', 'item' => url('/how-chatgpt-chooses-sources')],
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
:root{--bg:#080808;--card:#0e0d09;--border:rgba(200,168,75,.09);--gold:#c8a84b;--gold-lt:#e2c97d;--gold-dim:rgba(200,168,75,.4);--ivory:#ede8de;--muted:rgba(168,168,160,.72);--muted-lt:rgba(168,168,160,.50);--deep:#0b0b0b}
html{scroll-behavior:smooth;font-size:18px}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.75;min-height:100vh}
.logo{display:inline-flex;align-items:baseline;text-decoration:none;line-height:1}
.logo-seo{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.2rem;letter-spacing:.06em;color:var(--ivory)}
.logo-ai{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.42rem;color:var(--gold);letter-spacing:.02em}
.logo-co{font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.05rem;color:rgba(150,150,150,.5);letter-spacing:.04em}
.top-bar{display:flex;align-items:center;justify-content:space-between;padding:22px 48px;border-bottom:1px solid rgba(200,168,75,.07);position:sticky;top:0;z-index:100;background:rgba(8,8,8,.97);backdrop-filter:blur(8px)}
.back-link{font-size:.78rem;letter-spacing:.08em;color:var(--muted);text-decoration:none;transition:color .2s}
.back-link:hover{color:var(--gold)}
article.page{max-width:780px;margin:0 auto;padding:72px 40px 96px}
.eyebrow{font-size:.7rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);opacity:.8;margin-bottom:16px}
h1{font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem,5vw,3.6rem);font-weight:300;line-height:1.1;color:var(--ivory);margin-bottom:24px}
h1 em{font-style:italic;color:var(--gold-lt)}
.byline{font-size:.8rem;color:var(--muted);margin-bottom:32px}
.byline a{color:var(--gold);text-decoration:none}
.byline a:hover{text-decoration:underline}
.lead{font-size:1.06rem;color:rgba(237,232,222,.85);line-height:1.82;margin-bottom:36px}
.snippet-band{margin:0 0 28px;display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:rgba(200,168,75,.06)}
.snippet-item{background:var(--card);padding:16px 18px}
.snippet-item-label{font-size:.52rem;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);opacity:.65;margin-bottom:6px}
.snippet-item-text{font-size:.88rem;color:rgba(237,232,222,.85);line-height:1.6}
.definition{background:var(--card);border-left:3px solid var(--gold);padding:24px 28px;margin:0 0 28px}
.definition-term{font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:var(--gold);opacity:.7;margin-bottom:8px}
.definition-text{font-size:1.02rem;color:var(--ivory);line-height:1.72}
.def-angles{margin:0 0 36px;display:flex;flex-direction:column;gap:0}
.def-angle-row{display:grid;grid-template-columns:140px 1fr;border:1px solid var(--border);background:var(--card)}
.def-angle-row + .def-angle-row{border-top:none}
.def-angle-label{padding:14px 16px;font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:var(--gold);opacity:.6;border-right:1px solid var(--border);display:flex;align-items:center}
.def-angle-text{padding:14px 18px;font-size:.88rem;color:var(--muted);line-height:1.65;display:flex;align-items:center}
.section-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.7rem,3vw,2.4rem);font-weight:300;color:var(--ivory);margin:52px 0 18px;line-height:1.2}
.section-hed em{font-style:italic;color:var(--gold-lt)}
.prose{font-size:.97rem;color:var(--muted);line-height:1.88;margin-bottom:22px}
.prose strong{color:var(--ivory);font-weight:400}
.prose a{color:var(--gold);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.3);transition:border-color .2s}
.prose a:hover{border-color:var(--gold)}
.signal-list{list-style:none;margin:20px 0 36px;display:flex;flex-direction:column;gap:0}
.signal-item{display:grid;grid-template-columns:auto 1fr;gap:0;background:var(--card);border:1px solid var(--border)}
.signal-item + .signal-item{border-top:none}
.signal-icon{display:flex;align-items:flex-start;padding:18px 14px 0;color:var(--gold);font-size:.85rem;opacity:.7;font-family:'Cormorant Garamond',serif}
.signal-body{padding:16px 20px 16px 0}
.signal-title{font-size:.9rem;color:var(--ivory);font-weight:400;margin-bottom:4px}
.signal-text{font-size:.85rem;color:var(--muted);line-height:1.65}
.divider{height:1px;background:linear-gradient(to right,transparent,rgba(200,168,75,.12),transparent);margin:48px 0}
.comparison-table{width:100%;border-collapse:collapse;margin:24px 0 36px;font-size:.88rem}
.comparison-table th{text-align:left;padding:10px 14px;font-size:.6rem;letter-spacing:.15em;text-transform:uppercase;color:var(--gold);opacity:.7;border-bottom:1px solid rgba(200,168,75,.12);font-weight:400}
.comparison-table td{padding:12px 14px;border-bottom:1px solid var(--border);color:var(--muted);line-height:1.6;vertical-align:top}
.comparison-table td:first-child{color:var(--ivory)}
.comparison-table tr:last-child td{border-bottom:none}
.faq-section{margin:52px 0}
.faq-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.5rem,2.8vw,2.1rem);font-weight:300;color:var(--ivory);margin-bottom:24px}
.faq-list{display:flex;flex-direction:column;gap:0}
.faq-item{border:1px solid var(--border);background:var(--card)}
.faq-item + .faq-item{border-top:none}
.faq-q{font-size:.95rem;color:var(--ivory);font-weight:400;padding:18px 22px 6px}
.faq-a{font-size:.88rem;color:var(--muted);line-height:1.7;padding:0 22px 18px}
.related-grid{display:grid;grid-template-columns:1fr 1fr;gap:2px;margin:52px 0}
.related-card{background:var(--card);border:1px solid var(--border);padding:22px;text-decoration:none;transition:border-color .2s}
.related-card:hover{border-color:rgba(200,168,75,.22)}
.related-card-label{display:block;font-size:.6rem;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);opacity:.65;margin-bottom:7px}
.related-card-title{display:block;font-size:.95rem;color:var(--ivory);line-height:1.4}
.related-card-text{display:block;font-size:.82rem;color:var(--muted);line-height:1.55;margin-top:6px}
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
@media(max-width:640px){article.page{padding:48px 24px 72px}.top-bar{padding:18px 24px}.snippet-band{grid-template-columns:1fr}.related-grid{grid-template-columns:1fr}.def-angle-row{grid-template-columns:1fr}.def-angle-label{border-right:none;border-bottom:1px solid var(--border);padding:10px 16px}.comparison-table{font-size:.8rem}}
</style>
</head>
<body>

<nav class="top-bar" aria-label="Site navigation">
  <a href="{{ url('/') }}" class="logo" aria-label="SEO AI Co home">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <a href="{{ route('ai-search-optimization-guide') }}" class="back-link">← AI Search Optimization Guide</a>
</nav>

<article class="page" itemscope itemtype="https://schema.org/Article">

  <p class="eyebrow">Citation Mechanics</p>
  <h1>How ChatGPT <em>Chooses Sources</em></h1>
  <p class="byline">SEOAIco Editorial Team</p>

  <p class="lead">ChatGPT selects citation sources based on retrieval relevance, entity clarity, structured data signals, and passage extractability &#8212; not solely on Google search rank. The same signals that earn citations from ChatGPT Search also improve citation probability in Google AI Overviews, Perplexity, and Gemini.</p>

  <div class="snippet-band" aria-label="Quick answers">
    <div class="snippet-item">
      <p class="snippet-item-label">Short answer</p>
      <p class="snippet-item-text">ChatGPT chooses sources based on semantic relevance, entity clarity, schema signals, and whether the key answer can be extracted from the page as a standalone sentence.</p>
    </div>
    <div class="snippet-item">
      <p class="snippet-item-label">Best answer</p>
      <p class="snippet-item-text">ChatGPT Search runs its own retrieval pipeline &#8212; selecting pages that clearly define their entity, use structured data, and contain self-contained answer passages &#8212; independent of traditional search rank.</p>
    </div>
    <div class="snippet-item">
      <p class="snippet-item-label">One sentence</p>
      <p class="snippet-item-text">ChatGPT citation selection is driven by semantic retrieval relevance, entity definition clarity, structured data, and passage extractability &#8212; not by Google PageRank.</p>
    </div>
  </div>

  <div class="definition">
    <p class="definition-term">Definition</p>
    <p class="definition-text"><strong>ChatGPT source selection</strong> is the process by which ChatGPT Search identifies, retrieves, and prioritizes web pages as citation candidates for a given query &#8212; using a Bing-powered retrieval pipeline that ranks pages by semantic relevance, entity clarity, and structural citation-readiness, independent of Google search ranking.</p>
  </div>

  <div class="def-angles">
    <div class="def-angle-row">
      <span class="def-angle-label">In simple terms</span>
      <span class="def-angle-text">ChatGPT picks the page that most clearly answers the question &#8212; not necessarily the page Google would rank #1.</span>
    </div>
    <div class="def-angle-row">
      <span class="def-angle-label">Key takeaway</span>
      <span class="def-angle-text">Structural clarity and entity definition improve citation probability in ChatGPT independently of traditional SEO rank signals.</span>
    </div>
  </div>

  <h2 class="section-hed">The primary <em>citation signals.</em></h2>

  <ul class="signal-list">
    <li class="signal-item">
      <span class="signal-icon">&#x25B8;</span>
      <div class="signal-body">
        <p class="signal-title">Semantic retrieval relevance</p>
        <p class="signal-text">ChatGPT Search (powered by Bing AI) retrieves pages by semantic similarity to the query vector &#8212; not keyword density. Pages that use the exact terminology the user is likely to employ, without synonymic drift, produce closer vector matches and higher retrieval scores.</p>
      </div>
    </li>
    <li class="signal-item">
      <span class="signal-icon">&#x25B8;</span>
      <div class="signal-body">
        <p class="signal-title">Entity clarity in the opening</p>
        <p class="signal-text">Pages that name their entity in the first sentence &#8212; &#8220;[Service] is a [definition]&#8221; &#8212; are easier to classify at retrieval time. Ambiguous openings force the model to infer context from surrounding text, reducing classification confidence and citation probability.</p>
      </div>
    </li>
    <li class="signal-item">
      <span class="signal-icon">&#x25B8;</span>
      <div class="signal-body">
        <p class="signal-title">Schema markup</p>
        <p class="signal-text">JSON-LD schema &#8212; especially FAQPage, Article, LocalBusiness, Service, and DefinedTerm &#8212; provides machine-readable entity confirmation that AI systems use during retrieval scoring. Schema does not guarantee citation but reduces disambiguation errors that prevent citation.</p>
      </div>
    </li>
    <li class="signal-item">
      <span class="signal-icon">&#x25B8;</span>
      <div class="signal-body">
        <p class="signal-title">Passage extractability</p>
        <p class="signal-text">ChatGPT cites passages, not pages. Any paragraph &#8212; or any sentence &#8212; should be understandable and answerable in isolation. Content that requires reading the surrounding page to make sense is a poor extraction candidate. Each paragraph should begin with a topic claim and end with supporting detail.</p>
      </div>
    </li>
    <li class="signal-item">
      <span class="signal-icon">&#x25B8;</span>
      <div class="signal-body">
        <p class="signal-title">FAQ and question-answer pairs</p>
        <p class="signal-text">FAQPage schema signals to ChatGPT that the page contains direct-answer pairs matched to user query patterns. Each FAQ question is a potential query match; each answer is a high-quality extraction candidate. Pages with well-structured FAQ blocks are cited disproportionately for informational queries.</p>
      </div>
    </li>
    <li class="signal-item">
      <span class="signal-icon">&#x25B8;</span>
      <div class="signal-body">
        <p class="signal-title">Source authority and freshness</p>
        <p class="signal-text">Bing&#8217;s underlying signals include domain authority, page freshness, and backlink profile &#8212; but these are less determinative for citation selection than structural clarity. A well-structured page on a newer domain can outperform a structurally ambiguous page on a high-authority domain when the query requires entity specificity.</p>
      </div>
    </li>
  </ul>

  <div class="divider"></div>

  <h2 class="section-hed">ChatGPT vs. Google AI Overviews: <em>Citation differences.</em></h2>

  <table class="comparison-table" aria-label="ChatGPT vs Google AI Overviews citation comparison">
    <thead>
      <tr><th>Signal</th><th>ChatGPT Search</th><th>Google AI Overviews</th></tr>
    </thead>
    <tbody>
      <tr><td>Retrieval engine</td><td>Bing AI (Microsoft)</td><td>Google Search index</td></tr>
      <tr><td>Index dependency</td><td>Bing-indexed pages</td><td>Google-indexed pages</td></tr>
      <tr><td>Schema sensitivity</td><td>High &#8212; JSON-LD strongly preferred</td><td>High &#8212; Google structured data guidelines</td></tr>
      <tr><td>Entity clarity</td><td>Critical &#8212; first-sentence entity naming</td><td>Critical &#8212; E-E-A-T + entity definition</td></tr>
      <tr><td>FAQ signal</td><td>FAQPage schema + question-first structure</td><td>FAQPage schema + featured snippet eligibility</td></tr>
      <tr><td>Geographic scope</td><td>LocalBusiness schema + page-level geo signals</td><td>LocalBusiness schema + Google Business Profile alignment</td></tr>
    </tbody>
  </table>

  <section class="faq-section" aria-label="Frequently asked questions">
    <h2 class="faq-hed">Frequently asked questions</h2>
    <div class="faq-list">
      <div class="faq-item">
        <p class="faq-q">How does ChatGPT choose which sources to cite?</p>
        <p class="faq-a">ChatGPT Search selects sources based on retrieval relevance (how closely a page matches the query), entity clarity (how explicitly the page defines its subject), structural signals (schema markup, FAQ blocks, topic-sentence structure), and passage extractability &#8212; whether the key answer can be taken from the page without surrounding context.</p>
      </div>
      <div class="faq-item">
        <p class="faq-q">Does ChatGPT cite the highest-ranked Google result?</p>
        <p class="faq-a">Not necessarily. ChatGPT uses its own retrieval pipeline (Bing-powered for ChatGPT Search) and selects sources based on semantic relevance to the query, not solely on Google search rank. A well-structured page can be cited by ChatGPT even if it ranks lower in traditional search results.</p>
      </div>
      <div class="faq-item">
        <p class="faq-q">What schema markup helps with ChatGPT citation?</p>
        <p class="faq-a">The most effective schema types for AI citation &#8212; including ChatGPT &#8212; are: FAQPage, Article, LocalBusiness or Service, and DefinedTerm. All should be implemented as JSON-LD in the page head. Multiple schema types on a single page compound the citation signal.</p>
      </div>
      <div class="faq-item">
        <p class="faq-q">How do I get ChatGPT to cite my business?</p>
        <p class="faq-a">To get ChatGPT to cite your business: (1) Open with an entity-first sentence naming your service and location. (2) Add FAQPage schema with questions matching real user intent. (3) Add LocalBusiness or Service schema confirming entity type and geographic scope. (4) Write in self-contained paragraphs. (5) Publish an llms.txt file at your domain root.</p>
      </div>
    </div>
  </section>

  <nav class="related-grid" aria-label="Related topics">
    <a href="{{ route('chatgpt-seo') }}" class="related-card">
      <span class="related-card-label">Deep Dive</span>
      <span class="related-card-title">ChatGPT SEO</span>
      <span class="related-card-text">The complete framework for optimizing content to appear in ChatGPT-generated answers.</span>
    </a>
    <a href="{{ route('how-ai-search-works') }}" class="related-card">
      <span class="related-card-label">Overview</span>
      <span class="related-card-title">How AI Search Works</span>
      <span class="related-card-text">The complete retrieval-synthesis-citation pipeline for all AI search systems.</span>
    </a>
    <a href="{{ route('optimize-for-ai-answers') }}" class="related-card">
      <span class="related-card-label">Optimization</span>
      <span class="related-card-title">Optimize for AI Answers</span>
      <span class="related-card-text">Step-by-step instructions to structure content for AI answer citation.</span>
    </a>
    <a href="{{ route('ai-citation-engine') }}" class="related-card">
      <span class="related-card-label">Infrastructure</span>
      <span class="related-card-title">The AI Citation Engine™</span>
      <span class="related-card-text">Systematic deployment of all citation signals across your entire service area.</span>
    </a>
  </nav>

  <div class="page-cta">
    <span class="page-cta-eye">AI Citation Infrastructure</span>
    <h2>Get <em>ChatGPT to cite your business.</em></h2>
    <p>The AI Citation Engine™ deploys the structured data, entity definition, FAQ architecture, and programmatic page coverage that makes your content the source ChatGPT &#8212; and every AI search system &#8212; cites.</p>
    <a href="{{ route('book.index') }}" class="cta-btn">Book a Market Review</a>
    <a href="{{ route('ai-citation-engine') }}" class="cta-ghost">See the AI Citation Engine™ →</a>
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
    <a href="{{ route('ai-search-optimization-guide') }}">Guide</a>
    <a href="{{ route('book.index') }}">Book</a>
  </nav>
</footer>

@include('components.tm-style')
</body>
</html>
