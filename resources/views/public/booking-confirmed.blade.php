<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Consult Confirmed — SEOAIco</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #080808;
  --deep: #0a0906;
  --ivory: #ede8de;
  --muted: #a8a8a0;
  --gold: #c8a84b;
  --gold-lt: #e2c97d;
  --gold-dim: #9a7a30;
  --border: rgba(200,168,75,.10);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { font-size: 17px; }
body {
  background: var(--bg);
  color: var(--ivory);
  font-family: 'DM Sans', sans-serif;
  font-weight: 300;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 24px;
}

/* ── Layout ── */
.conf-wrap {
  max-width: 560px;
  width: 100%;
}

/* ── Eyebrow ── */
.conf-eye {
  font-size: .68rem;
  letter-spacing: .24em;
  text-transform: uppercase;
  color: var(--gold);
  display: block;
  margin-bottom: 20px;
}

/* ── Check mark ── */
.conf-mark {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 1px solid rgba(200,168,75,.22);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 28px;
  color: var(--gold);
  font-size: 1.2rem;
}

/* ── Headline ── */
.conf-hed {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 4.5vw, 3rem);
  font-weight: 300;
  line-height: 1.08;
  color: var(--ivory);
  margin-bottom: 12px;
  letter-spacing: -.01em;
}
.conf-hed em {
  font-style: italic;
  color: var(--gold-lt);
}

/* ── Subline ── */
.conf-sub {
  font-size: .92rem;
  color: var(--muted);
  line-height: 1.75;
  margin-bottom: 40px;
  max-width: 420px;
}

/* ── Detail block ── */
.conf-details {
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  padding: 28px 0;
  margin-bottom: 36px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.conf-detail-row {
  display: flex;
  align-items: baseline;
  gap: 12px;
  font-size: .88rem;
}
.conf-detail-label {
  font-size: .64rem;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: var(--gold-dim);
  min-width: 72px;
  flex-shrink: 0;
}
.conf-detail-value {
  color: var(--ivory);
}

/* ── CTA row ── */
.conf-ctas {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 40px;
}
.conf-cta-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--gold);
  color: #080808;
  font-size: .78rem;
  font-weight: 500;
  letter-spacing: .14em;
  text-transform: uppercase;
  padding: 14px 28px;
  text-decoration: none;
  border-radius: 4px;
  transition: background .3s, transform .2s;
  align-self: flex-start;
}
.conf-cta-primary:hover {
  background: var(--gold-lt);
  transform: translateY(-1px);
}
.conf-cta-secondary {
  font-size: .82rem;
  color: var(--muted);
  text-decoration: none;
  letter-spacing: .04em;
  transition: color .25s;
  align-self: flex-start;
}
.conf-cta-secondary:hover { color: var(--ivory); }
.conf-cta-secondary span { color: var(--gold-dim); margin-right: 4px; }

/* ── Email note ── */
.conf-email-note {
  font-size: .78rem;
  color: rgba(168,168,160,.52);
  font-style: italic;
  border-top: 1px solid rgba(200,168,75,.05);
  padding-top: 24px;
}

/* ── Home link ── */
.conf-home {
  display: block;
  margin-top: 28px;
  font-size: .74rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--gold-dim);
  text-decoration: none;
  transition: color .25s;
}
.conf-home:hover { color: var(--gold); }

@media (max-width: 520px) {
  body { padding: 40px 20px; }
  .conf-hed { font-size: 1.9rem; }
  .conf-cta-primary { width: 100%; justify-content: center; }
}
</style>
</head>
<body>
<div class="conf-wrap">

  <span class="conf-eye">Booking Confirmed</span>

  <div class="conf-mark">&#10003;</div>

  <h1 class="conf-hed">Your consult<br><em>is confirmed.</em></h1>
  <p class="conf-sub">We've reserved your time. Confirmation details are on the way.</p>

  <div class="conf-details">
    <div class="conf-detail-row">
      <span class="conf-detail-label">Session</span>
      <span class="conf-detail-value">{{ $booking->consultType->name }}</span>
    </div>
    <div class="conf-detail-row">
      <span class="conf-detail-label">Date</span>
      <span class="conf-detail-value">{{ $booking->preferred_date->format('l, F j, Y') }}</span>
    </div>
    <div class="conf-detail-row">
      <span class="conf-detail-label">Time</span>
      <span class="conf-detail-value">{{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }} PT</span>
    </div>
    <div class="conf-detail-row">
      <span class="conf-detail-label">Duration</span>
      <span class="conf-detail-value">{{ $booking->consultType->duration_minutes }} minutes</span>
    </div>
    @if($booking->google_meet_link)
    <div class="conf-detail-row">
      <span class="conf-detail-label">Location</span>
      <span class="conf-detail-value">
        <a href="{{ $booking->google_meet_link }}" target="_blank" rel="noopener"
           style="color:var(--gold);text-decoration:none">Google Meet &rarr;</a>
      </span>
    </div>
    @endif
  </div>

  <div class="conf-ctas">
    @php
      $gcalStart = $booking->preferred_date->format('Ymd')
        . 'T' . str_replace(':', '', \Carbon\Carbon::parse($booking->preferred_time)->format('Hi')) . '00';
      $gcalEnd   = $booking->preferred_date->format('Ymd')
        . 'T' . str_replace(':', '', \Carbon\Carbon::parse($booking->preferred_time)->addMinutes($booking->consultType->duration_minutes)->format('Hi')) . '00';
      $gcalTitle = urlencode($booking->consultType->name . ' — seoaico.com');
      $gcalUrl   = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
        . '&text=' . $gcalTitle
        . '&dates=' . $gcalStart . '/' . $gcalEnd
        . ($booking->google_meet_link ? '&location=' . urlencode($booking->google_meet_link) : '')
        . '&details=' . urlencode('Booked via seoaico.com');
    @endphp

    {{-- Primary CTA: onboarding --}}
    <a href="{{ route('onboarding.start', ['booking' => $booking->id]) }}" class="conf-cta-primary">
      Complete Your Onboarding &rarr;
    </a>

    <a href="{{ $gcalUrl }}" target="_blank" rel="noopener" class="conf-cta-secondary">
      <span>+</span> Add to Google Calendar
    </a>
    <a href="{{ url('/') }}#contact" class="conf-cta-secondary">
      <span>&#8594;</span> Questions? Contact us
    </a>
  </div>

  <p class="conf-email-note">
    A confirmation email has been sent to {{ $booking->email }}.
  </p>

  <a href="{{ url('/') }}" class="conf-home">&larr; seoaico.com</a>

</div>
</body>
</html>
