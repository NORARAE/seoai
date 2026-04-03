<!DOCTYPE html>
<html lang="en">
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-LNPGQ0GN69"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-LNPGQ0GN69');
</script>
<meta charset="UTF-8">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Market Opportunity Session — SEO AI Co™</title>
<meta name="description" content="A focused market opportunity session — not a sales call. We identify where your business can expand and how to take it.">
<link rel="canonical" href="{{ url('/book') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root{--bg:#080808;--deep:#0b0b0b;--gold:#c8a84b;--gold-lt:#e2c97d;--ivory:#ede8de;--muted:#a8a8a0;--border:#1a1a1a;--section-gap:96px}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:18px;scroll-behavior:smooth}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.85}

/* ── Nav ── */
.bk-nav{display:flex;align-items:center;justify-content:space-between;padding:24px 40px;border-bottom:1px solid var(--border)}
.bk-logo{text-decoration:none;display:inline-flex;align-items:baseline;gap:0}
.bk-logo .l-seo{font-weight:300;font-size:1.1rem;letter-spacing:.06em;color:#fff}
.bk-logo .l-ai{color:var(--gold);font-weight:500;font-size:1.25rem;letter-spacing:.02em}
.bk-logo .l-co{font-weight:300;font-size:.95rem;letter-spacing:.04em;color:rgba(150,150,150,.5)}
.bk-nav-back{font-size:.8rem;color:var(--muted);text-decoration:none;letter-spacing:.04em;transition:color .2s}
.bk-nav-back:hover{color:var(--gold)}

/* ── Layout ── */
.bk-wrap{max-width:760px;margin:0 auto;padding:0 32px}

/* ── Hero ── */
.bk-hero{padding:var(--section-gap) 0 80px;text-align:center;position:relative}
.bk-hero h1{font-family:'Cormorant Garamond',serif;font-size:clamp(2rem,5vw,3.2rem);font-weight:400;line-height:1.2;letter-spacing:-.01em;margin-bottom:16px}
.bk-hero h1 em{font-style:italic;color:var(--gold)}
.bk-hero .hero-sub{font-size:1rem;color:var(--muted);max-width:520px;margin:0 auto 40px;line-height:1.9}
/* ── Signal map background ── */
.bk-signal-bg{position:absolute;inset:0;pointer-events:none;z-index:-1;width:100%;height:100%;overflow:visible}
/* ── Signal copy + supporting line ── */
.bk-signal-copy{font-family:'Cormorant Garamond',serif;font-size:clamp(.95rem,2vw,1.15rem);font-style:italic;color:var(--gold);opacity:.6;margin:0 0 28px;letter-spacing:.01em;font-weight:300}
.bk-supporting-line{font-size:.83rem;color:rgba(168,168,160,.55);font-style:italic;margin:-12px auto 32px;max-width:480px;line-height:1.7}
/* ── Platform trust row ── */
.bk-trust-row{display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:10px 22px;margin-top:0;opacity:.42}
.bk-trust-item{display:inline-flex;align-items:center;gap:5px;font-size:.57rem;letter-spacing:.11em;text-transform:uppercase;color:var(--gold);font-weight:400;line-height:1}
.bk-trust-item svg{flex-shrink:0;color:inherit}
.bk-trust-dot{color:var(--gold);font-size:.5rem;opacity:.4;line-height:1;align-self:center}
/* ── Internal how-link ── */
.bk-how-link{display:inline-block;margin-top:18px;font-size:.72rem;letter-spacing:.1em;color:rgba(168,168,160,.45);text-decoration:none;transition:color .2s;text-transform:uppercase}
.bk-how-link:hover{color:var(--gold)}
.bk-notice{background:#1a0a0a;border:1px solid rgba(200,80,80,.35);border-radius:8px;padding:14px 20px;margin-bottom:40px;font-size:.85rem;color:#f0a0a0;text-align:center}
.bk-cta-btn{display:inline-flex;align-items:center;gap:10px;background:var(--gold);color:#080808;font-family:'DM Sans',sans-serif;font-size:.88rem;font-weight:500;letter-spacing:.08em;text-transform:uppercase;text-decoration:none;padding:17px 44px;border-radius:4px;border:none;cursor:pointer;transition:background .2s,transform .15s}
.bk-cta-btn:hover{background:var(--gold-lt);transform:translateY(-1px)}
.bk-cta-note{font-size:.75rem;color:var(--muted);margin-top:14px;letter-spacing:.02em}
.bk-cta-commitment{font-size:.72rem;color:rgba(168,168,160,.38);margin-top:8px;letter-spacing:.02em;font-style:italic}
.bk-trust-source{font-size:.60rem;letter-spacing:.11em;text-transform:uppercase;color:rgba(168,168,160,.32);margin-top:22px;margin-bottom:6px;display:block}

/* ── Divider ── */
.bk-divider{border:none;border-top:1px solid var(--border);margin:0}

/* ── What Happens ── */
.bk-happens{padding:64px 0 40px;text-align:left}
.bk-happens ul{list-style:none;display:flex;flex-direction:column;gap:14px;margin-top:20px}
.bk-happens li{display:flex;align-items:flex-start;gap:14px;font-size:.93rem;color:var(--ivory);line-height:1.7}
.bk-happens li::before{content:'·';color:var(--gold);flex-shrink:0;font-size:1.6rem;line-height:1.1}

/* ── Authority (signature mark) ── */
.bk-authority{padding:18px 0 30px;border-top:1px solid rgba(26,26,26,.7);text-align:center}
.bk-authority .auth-brand{font-family:'Cormorant Garamond',serif;font-size:.8rem;font-weight:300;color:rgba(237,232,222,.18);line-height:1.4;letter-spacing:.04em}
.bk-authority .auth-brand em{font-style:italic;color:rgba(200,168,75,.18)}
.bk-authority .auth-sub{font-size:.52rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.12);margin-bottom:4px;font-weight:400;display:block}
.bk-authority .auth-body{font-size:.9rem;color:var(--muted);max-width:460px;margin:0 auto;line-height:1.85}

/* ── Footer ── */
.bk-footer{padding:32px 40px;border-top:1px solid var(--border);text-align:center;font-size:.75rem;color:var(--muted);letter-spacing:.02em}
.bk-footer a{color:var(--muted);text-decoration:none}
.bk-footer a:hover{color:var(--gold)}

/* ── Booking entry section ── */
#book-now{background:var(--deep);border-top:1px solid var(--border);scroll-margin-top:80px}
.bk-entry-intro{max-width:640px;margin:0 auto;padding:80px 32px 48px;text-align:center}
.bk-entry-intro .bk-section-label{margin-bottom:14px}
.bk-entry-intro h2{font-family:'Cormorant Garamond',serif;font-size:clamp(1.5rem,3.5vw,2rem);font-weight:400;line-height:1.3;margin-bottom:18px}
.bk-entry-intro p{font-size:.9rem;color:var(--muted);line-height:1.9;max-width:500px;margin:0 auto}
.bk-entry-intro .bk-entry-system-note{font-size:.72rem;color:#4a4a42;margin-top:10px;letter-spacing:.02em;line-height:1.6}

@media(max-width:600px){
  .bk-nav{padding:20px 24px}
  .bk-wrap{padding:0 24px}
  :root{--section-gap:64px}
  .bk-entry-intro{padding:56px 24px 36px}
}
</style>
@include('partials.clarity')
</head>
<body>

  {{-- Nav --}}
  <nav class="bk-nav">
    <a href="/" class="bk-logo">
      <span class="l-seo">SEO</span><span class="l-ai">AI</span><span class="l-co">co</span>
    </a>
    <a href="/" class="bk-nav-back">← Back</a>
  </nav>

  <main>
    <div class="bk-wrap">

      {{-- Hero --}}
      <section class="bk-hero">
        {{-- Signal-map constellation — expanded, SMIL animated --}}
        <svg class="bk-signal-bg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 760 360" width="100%" height="100%" fill="none" aria-hidden="true" focusable="false" preserveAspectRatio="xMidYMid slice">
          <defs>
            <radialGradient id="sg" cx="50%" cy="42%" r="58%">
              <stop offset="0%" stop-color="#c8a84b" stop-opacity=".08"/>
              <stop offset="100%" stop-color="#c8a84b" stop-opacity="0"/>
            </radialGradient>
          </defs>
          <ellipse cx="380" cy="148" rx="360" ry="200" fill="url(#sg)"/>
          <!-- orbit rings -->
          <circle cx="380" cy="148" r="80" stroke="#c8a84b" stroke-width=".5" stroke-opacity=".07" fill="none"/>
          <circle cx="380" cy="148" r="148" stroke="#c8a84b" stroke-width=".4" stroke-opacity=".05" fill="none"/>
          <circle cx="380" cy="148" r="225" stroke="#c8a84b" stroke-width=".35" stroke-opacity=".035" fill="none"/>
          <circle cx="380" cy="148" r="320" stroke="#c8a84b" stroke-width=".25" stroke-opacity=".02" fill="none"/>
          <!-- primary lines: center to satellites -->
          <line x1="380" y1="148" x2="308" y2="84" stroke="#c8a84b" stroke-width=".9" stroke-opacity=".10"><animate attributeName="stroke-opacity" values=".10;.17;.10" dur="6s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="456" y2="90" stroke="#c8a84b" stroke-width=".9" stroke-opacity=".10"><animate attributeName="stroke-opacity" values=".10;.15;.10" dur="7.7s" begin="1s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="286" y2="172" stroke="#c8a84b" stroke-width=".8" stroke-opacity=".09"><animate attributeName="stroke-opacity" values=".09;.14;.09" dur="9.4s" begin="2s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="480" y2="175" stroke="#c8a84b" stroke-width=".8" stroke-opacity=".09"><animate attributeName="stroke-opacity" values=".09;.14;.09" dur="6.8s" begin="3.5s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="340" y2="228" stroke="#c8a84b" stroke-width=".7" stroke-opacity=".08"><animate attributeName="stroke-opacity" values=".08;.13;.08" dur="8.5s" begin="1.5s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="432" y2="52" stroke="#c8a84b" stroke-width=".7" stroke-opacity=".08"><animate attributeName="stroke-opacity" values=".08;.12;.08" dur="11.1s" begin="0.5s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="248" y2="110" stroke="#c8a84b" stroke-width=".65" stroke-opacity=".07"><animate attributeName="stroke-opacity" values=".07;.11;.07" dur="10.2s" begin="4s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="528" y2="136" stroke="#c8a84b" stroke-width=".65" stroke-opacity=".07"><animate attributeName="stroke-opacity" values=".07;.10;.07" dur="8.1s" begin="2.5s" repeatCount="indefinite"/></line>
          <!-- outer reach lines -->
          <line x1="380" y1="148" x2="152" y2="104" stroke="#c8a84b" stroke-width=".5" stroke-opacity=".05"><animate attributeName="stroke-opacity" values=".05;.08;.05" dur="11.9s" begin="3s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="614" y2="108" stroke="#c8a84b" stroke-width=".5" stroke-opacity=".05"><animate attributeName="stroke-opacity" values=".05;.08;.05" dur="13.6s" begin="5s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="562" y2="278" stroke="#c8a84b" stroke-width=".4" stroke-opacity=".04"><animate attributeName="stroke-opacity" values=".04;.07;.04" dur="12.8s" begin="6s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="196" y2="262" stroke="#c8a84b" stroke-width=".4" stroke-opacity=".04"><animate attributeName="stroke-opacity" values=".04;.07;.04" dur="15.3s" begin="7s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="68" y2="195" stroke="#c8a84b" stroke-width=".3" stroke-opacity=".03"/>
          <line x1="380" y1="148" x2="694" y2="200" stroke="#c8a84b" stroke-width=".3" stroke-opacity=".03"/>
          <!-- web connections -->
          <line x1="308" y1="84" x2="456" y2="90" stroke="#c8a84b" stroke-width=".5" stroke-opacity=".06"/>
          <line x1="286" y1="172" x2="340" y2="228" stroke="#c8a84b" stroke-width=".5" stroke-opacity=".05"/>
          <line x1="456" y1="90" x2="480" y2="175" stroke="#c8a84b" stroke-width=".45" stroke-opacity=".05"/>
          <line x1="248" y1="110" x2="286" y2="172" stroke="#c8a84b" stroke-width=".45" stroke-opacity=".045"/>
          <line x1="528" y1="136" x2="480" y2="175" stroke="#c8a84b" stroke-width=".45" stroke-opacity=".045"/>
          <line x1="432" y1="52" x2="528" y2="136" stroke="#c8a84b" stroke-width=".35" stroke-opacity=".04"/>
          <line x1="152" y1="104" x2="248" y2="110" stroke="#c8a84b" stroke-width=".35" stroke-opacity=".035"/>
          <line x1="614" y1="108" x2="528" y2="136" stroke="#c8a84b" stroke-width=".35" stroke-opacity=".035"/>
          <line x1="562" y1="278" x2="480" y2="175" stroke="#c8a84b" stroke-width=".3" stroke-opacity=".03"/>
          <line x1="196" y1="262" x2="286" y2="172" stroke="#c8a84b" stroke-width=".3" stroke-opacity=".03"/>
          <!-- center node (primary pulse + halo) -->
          <circle cx="380" cy="148" r="12" fill="none" stroke="#c8a84b" stroke-width=".5" stroke-opacity="0"><animate attributeName="r" values="5;20;5" dur="3.4s" repeatCount="indefinite"/><animate attributeName="stroke-opacity" values=".30;0;.30" dur="3.4s" repeatCount="indefinite"/></circle>
          <circle cx="380" cy="148" r="3.5" fill="#c8a84b" fill-opacity=".42"><animate attributeName="fill-opacity" values=".42;.72;.42" dur="3.4s" repeatCount="indefinite"/><animate attributeName="r" values="3.5;5.2;3.5" dur="3.4s" repeatCount="indefinite"/></circle>
          <!-- primary satellites -->
          <circle cx="308" cy="84" r="2.5" fill="#c8a84b" fill-opacity=".20"><animate attributeName="fill-opacity" values=".20;.38;.20" dur="5.1s" begin="0.8s" repeatCount="indefinite"/></circle>
          <circle cx="456" cy="90" r="2.2" fill="#c8a84b" fill-opacity=".18"><animate attributeName="fill-opacity" values=".18;.35;.18" dur="6s" begin="1.6s" repeatCount="indefinite"/></circle>
          <circle cx="286" cy="172" r="2" fill="#c8a84b" fill-opacity=".16"><animate attributeName="fill-opacity" values=".16;.30;.16" dur="6.8s" begin="2.4s" repeatCount="indefinite"/></circle>
          <circle cx="480" cy="175" r="2" fill="#c8a84b" fill-opacity=".16"><animate attributeName="fill-opacity" values=".16;.28;.16" dur="7.7s" begin="3.2s" repeatCount="indefinite"/></circle>
          <circle cx="340" cy="228" r="1.8" fill="#c8a84b" fill-opacity=".14"><animate attributeName="fill-opacity" values=".14;.26;.14" dur="8.5s" begin="1s" repeatCount="indefinite"/></circle>
          <circle cx="432" cy="52" r="1.8" fill="#c8a84b" fill-opacity=".14"><animate attributeName="fill-opacity" values=".14;.25;.14" dur="9.4s" begin="4s" repeatCount="indefinite"/></circle>
          <circle cx="248" cy="110" r="1.6" fill="#c8a84b" fill-opacity=".12"><animate attributeName="fill-opacity" values=".12;.22;.12" dur="7.7s" begin="5s" repeatCount="indefinite"/></circle>
          <circle cx="528" cy="136" r="1.6" fill="#c8a84b" fill-opacity=".12"><animate attributeName="fill-opacity" values=".12;.20;.12" dur="10.2s" begin="2s" repeatCount="indefinite"/></circle>
          <!-- outer / edge nodes -->
          <circle cx="556" cy="165" r="1.2" fill="#c8a84b" fill-opacity=".08"><animate attributeName="fill-opacity" values=".08;.16;.08" dur="11.1s" begin="6s" repeatCount="indefinite"/></circle>
          <circle cx="384" cy="36" r="1.2" fill="#c8a84b" fill-opacity=".07"><animate attributeName="fill-opacity" values=".07;.14;.07" dur="11.9s" begin="3s" repeatCount="indefinite"/></circle>
          <circle cx="212" cy="130" r="1.1" fill="#c8a84b" fill-opacity=".07"><animate attributeName="fill-opacity" values=".07;.13;.07" dur="9.4s" begin="7s" repeatCount="indefinite"/></circle>
          <circle cx="152" cy="104" r="1.3" fill="#c8a84b" fill-opacity=".07"><animate attributeName="fill-opacity" values=".07;.14;.07" dur="12.8s" begin="4s" repeatCount="indefinite"/></circle>
          <circle cx="614" cy="108" r="1.3" fill="#c8a84b" fill-opacity=".07"><animate attributeName="fill-opacity" values=".07;.13;.07" dur="13.6s" begin="8s" repeatCount="indefinite"/></circle>
          <circle cx="562" cy="278" r="1.1" fill="#c8a84b" fill-opacity=".05"><animate attributeName="fill-opacity" values=".05;.10;.05" dur="14.5s" begin="2s" repeatCount="indefinite"/></circle>
          <circle cx="196" cy="262" r="1.1" fill="#c8a84b" fill-opacity=".05"><animate attributeName="fill-opacity" values=".05;.09;.05" dur="15.3s" begin="9s" repeatCount="indefinite"/></circle>
          <circle cx="68" cy="195" r="1" fill="#c8a84b" fill-opacity=".04"/>
          <circle cx="694" cy="200" r="1" fill="#c8a84b" fill-opacity=".04"/>
          <circle cx="318" cy="316" r="1" fill="#c8a84b" fill-opacity=".03"/>
          <circle cx="462" cy="310" r="1" fill="#c8a84b" fill-opacity=".03"/>
        </svg>
        <h1>See exactly where your market stands &mdash; and what to do next.</h1>
        <p class="hero-sub">A focused session that reveals your position, your gaps, and your next move.</p>
        <p class="bk-supporting-line" style="margin:0 auto 10px">No guesswork. No recycled strategy. Only real signal.</p>
        <p class="bk-signal-copy" style="font-size:.95rem;opacity:.68;letter-spacing:.03em;margin:28px auto 34px;color:var(--ivory,#ede8de)">Position is not held by default.</p>

        @if(request('payment') === 'cancelled')
        <div class="bk-notice">
          Your payment was not completed &mdash; your spot was not reserved. Select a session below to try again.
        </div>
        @endif

        <button class="bk-cta-btn" onclick="openSession()">Reserve Your Market Opportunity Session</button>
        <p class="bk-cta-commitment">You&rsquo;ll know exactly where you stand.</p>
        <p class="bk-cta-note">Reserved for operators in active markets.</p>
        <span class="bk-trust-source">Validated through live search and platform signals</span>
        <div class="bk-trust-row" aria-label="Platform integrations">
          <span class="bk-trust-item">
            <svg width="12" height="12" viewBox="0 0 14 14" fill="currentColor" aria-hidden="true"><rect x="1" y="8" width="3" height="5"/><rect x="5.5" y="5" width="3" height="8"/><rect x="10" y="2" width="3" height="11"/></svg>
            Google Analytics
          </span>
          <span class="bk-trust-dot">&middot;</span>
          <span class="bk-trust-item">
            <svg width="12" height="12" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" aria-hidden="true"><circle cx="6.5" cy="6.5" r="4.5"/><line x1="9.7" y1="9.7" x2="13" y2="13"/></svg>
            Search Console
          </span>
          <span class="bk-trust-dot">&middot;</span>
          <span class="bk-trust-item">
            <svg width="12" height="12" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 1a4.5 4.5 0 0 0-4.5 4.5C2.5 8.8 7 13 7 13s4.5-4.2 4.5-7.5A4.5 4.5 0 0 0 7 1z"/><circle cx="7" cy="5.5" r="1.5"/></svg>
            Google Business
          </span>
          <span class="bk-trust-dot">&middot;</span>
          <span class="bk-trust-item">
            <svg width="12" height="12" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" aria-hidden="true"><circle cx="7" cy="7" r="1.2" fill="currentColor" stroke="none"/><path d="M4.5 9.5a3.5 3.5 0 0 1 0-5"/><path d="M9.5 9.5a3.5 3.5 0 0 0 0-5"/></svg>
            Bing
          </span>
          <span class="bk-trust-dot">&middot;</span>
          <span class="bk-trust-item">
            <svg width="12" height="12" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" aria-hidden="true"><circle cx="7" cy="2.5" r="1.2" fill="currentColor" stroke="none"/><circle cx="2.5" cy="11" r="1.2" fill="currentColor" stroke="none"/><circle cx="11.5" cy="11" r="1.2" fill="currentColor" stroke="none"/><line x1="7" y1="2.5" x2="2.5" y2="11"/><line x1="7" y1="2.5" x2="11.5" y2="11"/><line x1="2.5" y1="11" x2="11.5" y2="11"/></svg>
            AI Visibility
          </span>
          <span class="bk-trust-dot">&middot;</span>
          <span class="bk-trust-item">
            <svg width="12" height="12" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" aria-hidden="true"><line x1="2" y1="4" x2="12" y2="4"/><line x1="2" y1="7" x2="9" y2="7"/><line x1="2" y1="10" x2="11" y2="10"/><circle cx="12" cy="10" r="1.2" fill="currentColor" stroke="none"/></svg>
            LLM Discovery
          </span>
        </div>
      </section>


      {{-- Authority --}}
      <div class="bk-authority">
        <p class="auth-sub">System by</p>
        <p class="auth-brand"><em>SEO AI Co™</em></p>
        <p class="auth-sub" style="margin-bottom:0">Programmatic AI SEO Systems</p>
      </div>

    </div>

    {{-- Booking entry section --}}
    <section id="book-now">
      <div class="bk-entry-intro">
        <p class="bk-section-label">Reserve Your Session</p>
        <h2>Choose your starting point.</h2>
        <p class="bk-entry-system-note">Start with clarity, then move with confidence.</p>
      </div>
      @include('components.booking-modal', ['disableOverlayDismiss' => true, 'panelMode' => true])
    </section>

  </main>

  <footer class="bk-footer">
    <div style="margin-bottom:8px;font-size:.82rem"><strong style="color:var(--ivory)">SEO AI Co™</strong> &nbsp;&middot;&nbsp; Programmatic AI SEO Systems</div>
    <div style="margin-bottom:8px"><a href="mailto:hello@seoaico.com">hello@seoaico.com</a></div>
    <p style="font-size:.63rem;color:rgba(168,168,160,.38);max-width:440px;margin:0 auto 12px;line-height:1.5">SEO AI Co™ and associated systems, processes, and methodologies are proprietary and may not be reproduced without permission.</p>
    <div><a href="/">seoaico.com</a> &nbsp;&mdash;&nbsp; <a href="{{ route('privacy') }}">Privacy</a> &nbsp;&mdash;&nbsp; <a href="{{ route('terms') }}">Terms</a></div>
  </footer>

  <script>
  function openSession() {
    window.dispatchEvent(new CustomEvent('open-booking', {detail: null}));
    document.getElementById('book-now').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  </script>
  <script>
  if(typeof gtag==='function'){gtag('event','view_book',{page_location:window.location.href});}
  </script>
</body>
</html>
