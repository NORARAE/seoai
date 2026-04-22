<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Action Plan — Results | SEO AI Co™</title>
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
@include('partials.design-system')
@include('partials.public-nav-css')

.result-hero{padding:80px 0 56px;text-align:center}
.result-hero-eye{font-size:.62rem;letter-spacing:.3em;text-transform:uppercase;color:rgba(200,168,75,.58);margin-bottom:20px}
.result-hero-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(2rem,4vw,3rem);font-weight:300;color:var(--ivory);line-height:1.12;margin-bottom:18px}
.result-hero-hed em{font-style:italic;color:var(--gold-lt)}
.result-hero-sub{font-size:.88rem;color:var(--muted);line-height:1.8;max-width:540px;margin:0 auto 40px}
.result-status{
  max-width:680px;margin:0 auto;padding:48px 40px;
  background:var(--card-bg);border:1px solid var(--card-border);
  text-align:center;
}
.result-status-hed{font-family:'Cormorant Garamond',serif;font-size:1.4rem;font-weight:400;color:var(--ivory);margin-bottom:14px}
.result-status-body{font-size:.84rem;color:var(--muted);line-height:1.8;margin-bottom:28px}
.result-status-body strong{color:rgba(237,232,222,.78);font-weight:400}

.upsell-section{padding:56px 0;text-align:center}
.upsell-hed{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:300;color:var(--ivory);margin-bottom:14px}
.upsell-sub{font-size:.84rem;color:var(--muted);line-height:1.8;max-width:480px;margin:0 auto 32px}
.upsell-actions{display:flex;align-items:center;justify-content:center;gap:18px;flex-wrap:wrap}
</style>
@include('partials.clarity')
</head>
<body>

@include('partials.public-nav')

<section class="result-hero">
  <div class="wrap">
    <p class="result-hero-eye">Action Plan — $249</p>
    <h1 class="result-hero-hed">Your <em>action sequence</em> is being built.</h1>
    <p class="result-hero-sub">We're building your complete priority correction sequence — every fix prioritized, every opportunity sized, every gap closed systematically.</p>
  </div>
</section>

<section class="wrap">
  <div class="result-status">
    <h2 class="result-status-hed">What happens next</h2>
    <p class="result-status-body">Your Action Plan is being built. When processing completes, your dashboard will show your prioritized fix list and highest-impact structural actions.</p>
    <a href="/" class="btn-ghost">Return to home &rarr;</a>
  </div>
</section>

<section class="upsell-section">
  <div class="wrap">
    <h2 class="upsell-hed">Ready for full implementation?</h2>
    <p class="upsell-sub">Move from correction plan to full system deployment. Your structural data carries forward — nothing is lost.</p>
    <div class="upsell-actions">
      <a href="{{ route('checkout.system-activation') }}" class="btn-primary">Start Guided Execution — $489</a>
      <a href="{{ route('book.index', ['entry' => 'consultation']) }}" class="btn-ghost">Book Consultation &rarr;</a>
    </div>
  </div>
</section>

@include('partials.public-footer')

</body>
</html>
