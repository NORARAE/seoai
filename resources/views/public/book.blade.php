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
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Enter the System | SEO AI Co™</title>
<meta name="description" content="Controlled system entry for strategic activation intake and deployment readiness validation.">
<link rel="canonical" href="{{ url('/book') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="SEO AI Co™">
<meta property="og:title" content="Enter the System | SEO AI Co™">
<meta property="og:description" content="Enter through strategic activation intake or proceed to full activation when qualified.">
<meta property="og:url" content="{{ url('/book') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
@include('partials.design-system')
@include('partials.public-nav-css')

/* ── Book page tokens ── */
:root{--border:#1a1a1a;--muted:#a8a8a0;--panel:#0f0d08;--panel-soft:#12100a}
body{line-height:1.85}

/* ── Layout ── */
.bk-wrap{max-width:1040px;margin:0 auto;padding:0 32px}

/* ── Hero ── */
.bk-hero{padding:clamp(110px,14vh,152px) 0 48px;text-align:center;position:relative}
.bk-hero-kicker{font-size:.66rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.72);display:inline-block;margin-bottom:12px}
.bk-hero h1{font-family:'Cormorant Garamond',serif;font-size:clamp(2rem,5vw,3.2rem);font-weight:400;line-height:1.2;letter-spacing:-.01em;margin-bottom:16px}
.bk-hero h1 em{font-style:italic;color:var(--gold)}
.bk-hero .hero-sub{font-size:1rem;color:#b9b3a2;max-width:720px;margin:0 auto 0;line-height:1.9}
.bk-signal-bg{position:absolute;inset:0;pointer-events:none;z-index:-1;width:100%;height:100%;overflow:visible}
.bk-notice{background:#1a0a0a;border:1px solid rgba(200,80,80,.35);border-radius:8px;padding:14px 20px;margin:28px auto 0;max-width:520px;font-size:.85rem;color:#f0a0a0;text-align:center}

/* ── Decision grid ── */
.bk-decision{padding:52px 0 80px}
.bk-paths{display:grid;grid-template-columns:1fr 1fr;gap:28px;align-items:start}

/* ── Path column ── */
.bk-path{background:linear-gradient(180deg,var(--panel) 0%,var(--panel-soft) 100%);border:1px solid rgba(200,168,75,.14);border-radius:10px;padding:36px 32px;display:flex;flex-direction:column;transition:opacity .25s,filter .25s,transform .25s}
.bk-paths:hover .bk-path{opacity:.78;filter:brightness(.93)}
.bk-paths:hover .bk-path:hover{opacity:1;filter:brightness(1);transform:translateY(-2px)}
.bk-path--guided{border-top:2px solid rgba(200,168,75,.34)}
.bk-path--activation{border-color:rgba(200,168,75,.16);border-top:2px solid rgba(200,168,75,.26)}

/* ── Path header ── */
.bk-path-label{font-size:.68rem;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);opacity:.82;display:block;margin-bottom:8px}
.bk-path-sub{font-size:.84rem;color:#b2ac9a;line-height:1.8;margin:0 0 24px;text-transform:uppercase;letter-spacing:.08em}

/* ── Step cards ── */
.bk-steps{display:flex;flex-direction:column;gap:14px;flex:1}
.bk-step{padding:20px;border:1px solid rgba(200,168,75,.16);border-radius:8px;background:rgba(200,168,75,.03);transition:border-color .2s,background .2s}
.bk-step:hover{border-color:rgba(200,168,75,.32);background:rgba(200,168,75,.05)}
.bk-step-flag{font-size:.58rem;letter-spacing:.13em;text-transform:uppercase;color:rgba(200,168,75,.55);display:block;margin-bottom:5px}
.bk-step-row{display:flex;align-items:baseline;justify-content:space-between;gap:12px}
.bk-step-name{font-family:'Cormorant Garamond',serif;font-size:1.15rem;font-weight:400;color:var(--ivory);line-height:1.3}
.bk-step-price{font-family:'Cormorant Garamond',serif;font-size:1.35rem;font-weight:300;color:var(--gold);white-space:nowrap}
.bk-step-desc{font-size:.8rem;color:#bcb6a4;margin-top:7px;line-height:1.7}

/* ── Path footer / CTA ── */
.bk-path-foot{margin-top:28px;text-align:center}
.bk-cta-btn{display:inline-flex;align-items:center;gap:10px;font-family:'DM Sans',sans-serif;font-size:.84rem;font-weight:500;letter-spacing:.08em;text-transform:uppercase;text-decoration:none;padding:16px 40px;border-radius:4px;border:none;cursor:pointer;transition:background .2s,transform .15s,border-color .2s}
.bk-cta--solid{background:var(--gold);color:#080808}
.bk-cta--solid:hover{background:var(--gold-lt);transform:translateY(-1px)}
.bk-cta--ghost{background:transparent;color:var(--gold);border:1px solid rgba(200,168,75,.22)}
.bk-cta--ghost:hover{background:rgba(200,168,75,.06);border-color:rgba(200,168,75,.38);transform:translateY(-1px)}
.bk-path-note{font-size:.72rem;color:#b6af9d;margin-top:14px;letter-spacing:.06em;text-transform:uppercase}
.bk-path-nudge{font-size:.72rem;color:rgba(200,168,75,.6);margin-top:6px;font-style:italic}
.bk-qualification-note{font-size:.72rem;line-height:1.8;color:rgba(200,168,75,.72);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px}

/* ── Entry interstitial gate ── */
.bk-entry-gate{position:fixed;inset:0;z-index:9100;background:rgba(0,0,0,.82);backdrop-filter:blur(7px);display:flex;align-items:center;justify-content:center;padding:16px;opacity:0;pointer-events:none;transition:opacity .28s}
.bk-entry-gate[data-open='true']{opacity:1;pointer-events:auto}
.bk-entry-gate-panel{width:100%;max-width:560px;background:linear-gradient(180deg,#11100d 0%,#0d0c09 100%);border:1px solid rgba(200,168,75,.24);border-top:2px solid rgba(200,168,75,.38);border-radius:12px;padding:34px 30px;box-shadow:0 22px 64px rgba(0,0,0,.46),0 0 52px rgba(200,168,75,.05)}
.bk-entry-gate-kicker{font-size:.64rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.74);margin-bottom:10px}
.bk-entry-gate-title{font-family:'Cormorant Garamond',serif;font-size:clamp(1.55rem,3vw,2.15rem);font-weight:400;line-height:1.2;color:var(--ivory);margin-bottom:10px}
.bk-entry-gate-body{font-size:.9rem;line-height:1.82;color:#c2bcab;margin-bottom:12px}
.bk-entry-gate-sub{font-size:.72rem;line-height:1.75;text-transform:uppercase;letter-spacing:.08em;color:rgba(200,168,75,.68);margin-bottom:22px}
.bk-entry-gate-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.bk-entry-gate-primary{display:inline-flex;align-items:center;justify-content:center;min-height:50px;padding:14px 24px;border-radius:8px;background:var(--gold);color:#080808;font-size:.74rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;text-decoration:none;border:1px solid rgba(200,168,75,.52);transition:background .2s,transform .14s}
.bk-entry-gate-primary:hover{background:var(--gold-lt);transform:translateY(-1px)}
.bk-entry-gate-secondary{font-size:.67rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(168,168,160,.55);text-decoration:none;transition:color .2s}
.bk-entry-gate-secondary:hover{color:rgba(200,168,75,.78)}

/* ── Or divider between paths (mobile) ── */
.bk-or{display:none;text-align:center;font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(200,168,75,.4);padding:4px 0}

/* ── Guided path focal glow ── */
.bk-path--guided{box-shadow:0 0 60px rgba(200,168,75,.025),0 0 1px rgba(200,168,75,.06)}

/* ── Activation pitch (right path) ── */
.bk-activation-pitch{flex:1;display:flex;flex-direction:column;justify-content:center}
.bk-pitch-lead{font-family:'Cormorant Garamond',serif;font-size:1.12rem;font-weight:400;color:var(--ivory);line-height:1.55;margin:0 0 18px}
.bk-pitch-includes{list-style:none;display:flex;flex-direction:column;gap:8px;margin:0;padding:0}
.bk-pitch-includes li{font-size:.8rem;color:var(--muted);padding-left:16px;position:relative;line-height:1.65}
.bk-pitch-includes li::before{content:'\2192';color:rgba(200,168,75,.35);position:absolute;left:0;font-size:.72rem;top:.05em}
.bk-pitch-range{font-size:.78rem;color:rgba(200,168,75,.55);margin-top:18px;font-style:italic;letter-spacing:.02em}

/* ── Secondary advisory section ── */
.bk-secondary{max-width:560px;margin:0 auto;padding:0 0 20px;text-align:center}
.bk-secondary-rule{border:none;border-top:1px solid rgba(26,26,26,.6);margin:0 0 32px}
.bk-secondary-label{font-size:.6rem;letter-spacing:.16em;text-transform:uppercase;color:rgba(168,168,160,.38);margin:0 0 10px}
.bk-secondary-sub{font-size:.82rem;color:rgba(168,168,160,.48);margin:0 0 16px;line-height:1.75}
.bk-secondary-link{display:inline-block;background:none;border:1px solid rgba(26,26,26,.8);border-radius:4px;color:rgba(168,168,160,.5);font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;padding:10px 24px;cursor:pointer;transition:color .2s,border-color .2s;font-family:'DM Sans',sans-serif}
.bk-secondary-link:hover{color:rgba(200,168,75,.6);border-color:rgba(200,168,75,.15)}

@include('partials.public-nav-mobile-css')

@media(max-width:900px){
  #nav{padding:14px 20px}#nav.stuck{padding:10px 20px}.nav-link{display:none}
  .nav-btn{display:none}
  .nav-hamburger{display:flex}
}
@media(max-width:768px){
  .bk-wrap{padding:0 24px}
  .bk-paths{grid-template-columns:1fr;gap:0}
  .bk-paths:hover .bk-path{opacity:1;filter:none;transform:none}
  .bk-or{display:block}
  .bk-hero{padding-top:92px;padding-bottom:36px}
  .bk-decision{padding:36px 0 60px}
  .bk-cta-btn{width:100%;padding:16px 24px;font-size:.82rem;justify-content:center}
  .bk-secondary{padding:0 0 12px}
  .bk-secondary-link{width:100%;text-align:center}
  .bk-entry-gate-panel{padding:28px 22px}
  .bk-entry-gate-actions{flex-direction:column;align-items:stretch}
  .bk-entry-gate-primary{text-align:center}
  .bk-entry-gate-secondary{text-align:center}
}
@media(max-width:430px){
  .bk-path{padding:28px 22px}
  .bk-step{padding:16px}
  .bk-step-name{font-size:1.05rem}
  .bk-step-price{font-size:1.2rem}
}
</style>
@include('partials.clarity')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Service",
      "name": "AI Citation Engine — SEO AI Co™",
      "provider": {
        "@type": "Organization",
        "name": "SEO AI Co",
        "url": "https://seoaico.com"
      },
      "url": "https://seoaico.com/book",
      "description": "Two controlled entry pathways: strategic activation intake or full system activation.",
      "offers": [
        {
          "@type": "Offer",
          "name": "Strategic Activation Intake",
          "price": "500",
          "priceCurrency": "USD",
          "description": "Structural validation and deployment readiness sequencing before full activation."
        },
        {
          "@type": "Offer",
          "name": "Full System Activation",
          "price": "5000",
          "priceCurrency": "USD",
          "description": "Qualified systems proceed directly into full deployment and market-level infrastructure activation."
        }
      ]
    }
  ]
}
</script>
@endverbatim
</head>
<body>

  @include('partials.public-nav', ['showHamburger' => true])

  <main>
    <div class="bk-wrap">

      {{-- Hero --}}
      <section class="bk-hero">
        {{-- Signal-map constellation --}}
        <svg class="bk-signal-bg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 760 360" width="100%" height="100%" fill="none" aria-hidden="true" focusable="false" preserveAspectRatio="xMidYMid slice">
          <defs>
            <radialGradient id="sg" cx="50%" cy="42%" r="58%">
              <stop offset="0%" stop-color="#c8a84b" stop-opacity=".08"/>
              <stop offset="100%" stop-color="#c8a84b" stop-opacity="0"/>
            </radialGradient>
          </defs>
          <ellipse cx="380" cy="148" rx="360" ry="200" fill="url(#sg)"/>
          <circle cx="380" cy="148" r="80" stroke="#c8a84b" stroke-width=".5" stroke-opacity=".07" fill="none"/>
          <circle cx="380" cy="148" r="148" stroke="#c8a84b" stroke-width=".4" stroke-opacity=".05" fill="none"/>
          <circle cx="380" cy="148" r="225" stroke="#c8a84b" stroke-width=".35" stroke-opacity=".035" fill="none"/>
          <line x1="380" y1="148" x2="308" y2="84" stroke="#c8a84b" stroke-width=".9" stroke-opacity=".10"><animate attributeName="stroke-opacity" values=".10;.17;.10" dur="6s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="456" y2="90" stroke="#c8a84b" stroke-width=".9" stroke-opacity=".10"><animate attributeName="stroke-opacity" values=".10;.15;.10" dur="7.7s" begin="1s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="286" y2="172" stroke="#c8a84b" stroke-width=".8" stroke-opacity=".09"><animate attributeName="stroke-opacity" values=".09;.14;.09" dur="9.4s" begin="2s" repeatCount="indefinite"/></line>
          <line x1="380" y1="148" x2="480" y2="175" stroke="#c8a84b" stroke-width=".8" stroke-opacity=".09"><animate attributeName="stroke-opacity" values=".09;.14;.09" dur="6.8s" begin="3.5s" repeatCount="indefinite"/></line>
          <line x1="308" y1="84" x2="456" y2="90" stroke="#c8a84b" stroke-width=".5" stroke-opacity=".06"/>
          <line x1="286" y1="172" x2="480" y2="175" stroke="#c8a84b" stroke-width=".45" stroke-opacity=".045"/>
          <circle cx="380" cy="148" r="12" fill="none" stroke="#c8a84b" stroke-width=".5" stroke-opacity="0"><animate attributeName="r" values="5;20;5" dur="3.4s" repeatCount="indefinite"/><animate attributeName="stroke-opacity" values=".30;0;.30" dur="3.4s" repeatCount="indefinite"/></circle>
          <circle cx="380" cy="148" r="3.5" fill="#c8a84b" fill-opacity=".42"><animate attributeName="fill-opacity" values=".42;.72;.42" dur="3.4s" repeatCount="indefinite"/><animate attributeName="r" values="3.5;5.2;3.5" dur="3.4s" repeatCount="indefinite"/></circle>
          <circle cx="308" cy="84" r="2.5" fill="#c8a84b" fill-opacity=".20"><animate attributeName="fill-opacity" values=".20;.38;.20" dur="5.1s" begin="0.8s" repeatCount="indefinite"/></circle>
          <circle cx="456" cy="90" r="2.2" fill="#c8a84b" fill-opacity=".18"><animate attributeName="fill-opacity" values=".18;.35;.18" dur="6s" begin="1.6s" repeatCount="indefinite"/></circle>
          <circle cx="286" cy="172" r="2" fill="#c8a84b" fill-opacity=".16"><animate attributeName="fill-opacity" values=".16;.30;.16" dur="6.8s" begin="2.4s" repeatCount="indefinite"/></circle>
          <circle cx="480" cy="175" r="2" fill="#c8a84b" fill-opacity=".16"><animate attributeName="fill-opacity" values=".16;.28;.16" dur="7.7s" begin="3.2s" repeatCount="indefinite"/></circle>
        </svg>

        <span class="bk-hero-kicker">System Entry Layer</span>
        <h1>System Entry: <em>Strategic Activation</em></h1>
        <p class="hero-sub">You are entering the system at a strategic level. Current visibility position, structural gaps, and activation path are assessed before deployment.</p>

        @if(request('payment') === 'cancelled')
        <div class="bk-notice">
          Payment not completed. Entry authorization was not issued. Select a pathway below to continue.
        </div>
        @endif
      </section>

      {{-- Decision grid --}}
      <section class="bk-decision">
        <div class="bk-paths">

          {{-- LEFT — Strategic Activation Intake --}}
          <div class="bk-path bk-path--guided">
            <span class="bk-path-label">System Entry</span>
            <p class="bk-path-sub">Activation Intake · Qualified Entry · Structural Validation</p>

            <div class="bk-steps">
              <div class="bk-step">
                <span class="bk-step-flag">01 &middot; Activation Intake</span>
                <div class="bk-step-row">
                  <h3 class="bk-step-name">Strategic Activation Entry</h3>
                  <span class="bk-step-price">$250&ndash;$500</span>
                </div>
                <p class="bk-step-desc">Current system position, signal gaps, and deployment sequence are assessed before execution authority is issued.</p>
              </div>

              <div class="bk-step">
                <span class="bk-step-flag">Purpose</span>
                <div class="bk-step-row">
                  <h3 class="bk-step-name">Strategic Positioning Layer</h3>
                  <span class="bk-step-price">Readiness Required</span>
                </div>
                <p class="bk-step-desc">This phase defines system position, structural readiness, and activation sequence. Execution begins only after signal alignment is confirmed.</p>
              </div>

              <div class="bk-step">
                <span class="bk-step-flag">Required</span>
                <div class="bk-step-row">
                  <h3 class="bk-step-name">System Qualification Layer</h3>
                  <span class="bk-step-price">Validation Gate</span>
                </div>
                <p class="bk-step-desc">Entry validation is required before full activation. All deployments follow confirmed structure, signal alignment, and market-fit review.</p>
              </div>
            </div>

            <div class="bk-path-foot">
              <a href="{{ route('book.index', ['entry' => 'consultation']) }}" class="bk-cta-btn bk-cta--ghost" onclick="return openEntry('consultation');">Begin System Entry</a>
              <p class="bk-path-note">Strategic intake required for activation</p>
            </div>
          </div>

          {{-- OR separator (mobile only) --}}
          <div class="bk-or">or</div>

          {{-- RIGHT — Full Activation (primary) --}}
          <div class="bk-path bk-path--activation">
            <span class="bk-path-label">Full Activation</span>

            <div class="bk-activation-pitch">
              <p class="bk-pitch-lead">Qualified systems proceed directly into deployment.</p>
              <ul class="bk-pitch-includes">
                <li>Full system build and deployment</li>
                <li>Ownership of outcome</li>
                <li>Onboarding intake and kickoff</li>
                <li>Visibility infrastructure across your market</li>
              </ul>
              <p class="bk-pitch-range">Activation engagements from $5,000 &middot; scaled deployments from $15,000+</p>
            </div>

            <div class="bk-path-foot">
              <p class="bk-qualification-note">Direct activation is available for qualified systems only.</p>
              <a href="{{ route('book.index', ['entry' => 'activation']) }}" class="bk-cta-btn bk-cta--solid" onclick="return openEntry('activation');">Start Full Activation</a>
              <p class="bk-path-note">Deployment authorization issued after qualification review</p>
            </div>
          </div>

        </div>

      </section>

    </div>
  </main>

  @include('components.booking-modal')
  <div class="bk-entry-gate" id="entryGate" data-open="false" role="dialog" aria-modal="true" aria-labelledby="entryGateTitle">
    <div class="bk-entry-gate-panel">
      <p class="bk-entry-gate-kicker">Activation Layer</p>
      <h2 class="bk-entry-gate-title" id="entryGateTitle">System Entry Initiated</h2>
      <p class="bk-entry-gate-body">You are entering the activation layer. This process defines system position and deployment eligibility.</p>
      <p class="bk-entry-gate-sub">Entry proceeds through structured intake and validation.</p>
      <div class="bk-entry-gate-actions">
        <button type="button" id="entryGateProceed" class="bk-entry-gate-primary">Proceed to Entry Scheduling</button>
        <a href="{{ url('/dashboard') }}" class="bk-entry-gate-secondary">Return to dashboard</a>
      </div>
    </div>
  </div>
  @include('partials.public-footer')
  @include('partials.back-to-top')

  <script>
  const nav = document.getElementById('nav');
  if(nav) window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60));
  const entryGate = document.getElementById('entryGate');
  const entryGateProceed = document.getElementById('entryGateProceed');
  let pendingEntryKey = null;

  function openEntryGate(entryKey) {
    if (!entryGate) return;
    pendingEntryKey = entryKey;
    entryGate.dataset.open = 'true';
  }

  function closeEntryGate() {
    if (!entryGate) return;
    entryGate.dataset.open = 'false';
  }

  function triggerEntryScheduling(entryKey) {
    const entry = bookingEntries[entryKey];
    if (!entry || !entry.id) {
      window.location.href = '{{ route('book.index', ['entry' => 'consultation']) }}';
      return;
    }
    window._bkPending = entry;
    window.dispatchEvent(new CustomEvent('open-booking', { detail: entry }));
  }

  if (entryGateProceed) {
    entryGateProceed.addEventListener('click', function () {
      const key = pendingEntryKey || 'consultation';
      closeEntryGate();
      triggerEntryScheduling(key);
    });
  }

  if (entryGate) {
    entryGate.addEventListener('click', function (evt) {
      if (evt.target === entryGate) closeEntryGate();
    });
  }

  document.addEventListener('keydown', function (evt) {
    if (evt.key === 'Escape' && entryGate && entryGate.dataset.open === 'true') {
      closeEntryGate();
    }
  });

  const bookingEntries = {
    consultation: {
      id: {{ ($highTicketTypes ?? collect())->get('consultation')?->id ?? 0 }},
      duration: {{ ($highTicketTypes ?? collect())->get('consultation')?->duration_minutes ?? 60 }},
      name: 'Strategic Activation Entry',
      isFree: false,
      paymentStructure: 'full_prepay'
    },
    activation: {
      id: {{ ($highTicketTypes ?? collect())->get('activation')?->id ?? 0 }},
      duration: {{ ($highTicketTypes ?? collect())->get('activation')?->duration_minutes ?? 60 }},
      name: 'Full System Activation',
      isFree: false,
      paymentStructure: '50_50_split'
    }
  };

  function openEntry(key) {
    const entry = bookingEntries[key];
    if (!entry || !entry.id) {
      return true;
    }

    if (key === 'consultation') {
      openEntryGate(key);
      return false;
    }

    triggerEntryScheduling(key);
    return false;
  }

  const requestedEntry = new URLSearchParams(window.location.search).get('entry');
  if (requestedEntry && bookingEntries[requestedEntry]?.id) {
    window._bkPending = bookingEntries[requestedEntry];
    window.dispatchEvent(new CustomEvent('open-booking', { detail: bookingEntries[requestedEntry] }));
  }
  </script>
  @include('partials.public-nav-js')
  <script>
  if(typeof gtag==='function'){gtag('event','view_book',{page_location:window.location.href});}
  </script>
@include('components.tm-style')
</body>
</html>
