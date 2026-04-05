<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Onboarding Received</title>
<style>
  body { background: #f5f3ef; font-family: 'Georgia', serif; color: #1a1a1a; margin: 0; padding: 0; }
  .wrap { max-width: 560px; margin: 40px auto; background: #fff; border-top: 3px solid #c8a84b; }
  .header { padding: 36px 40px 20px; }
  .header .logo { font-size: .8rem; letter-spacing: .2em; text-transform: uppercase; color: #c8a84b; }
  .header h1 { font-size: 1.55rem; font-weight: 400; color: #111; margin: 12px 0 6px; line-height: 1.25; }
  .header p  { font-size: .92rem; color: #555; line-height: 1.7; margin: 0; }
  .divider { height: 1px; background: #e8e3d8; margin: 0 40px; }
  .body { padding: 28px 40px; }
  .body p { font-size: .92rem; color: #444; line-height: 1.75; margin-bottom: 16px; }
  .detail-block { background: #faf8f4; border-left: 3px solid #c8a84b; padding: 18px 20px; margin: 24px 0; }
  .detail-block p { margin: 4px 0; font-size: .88rem; color: #333; }
  .detail-block strong { color: #111; }
  .footer { padding: 20px 40px 36px; border-top: 1px solid #e8e3d8; }
  .footer p { font-size: .75rem; color: #999; line-height: 1.6; }
  .footer a { color: #c8a84b; text-decoration: none; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="logo">SEO AI Co™</div>
    <h1>Your intake is in.<br>{{ $lead->name }}, your position is now under review.</h1>
    <p>We review every submission individually. Your market is being evaluated now.</p>
  </div>

  <div class="divider"></div>

  <div class="body">
    <p>We accept one primary operator per market. Your submission is under review — territory availability, competitive position, and site readiness are all evaluated before activation.</p>

    <div class="detail-block">
      <p><strong>Business:</strong> {{ $submission->business_name }}</p>
      @if($submission->website)
      <p><strong>Website:</strong> {{ $submission->website }}</p>
      @endif
      @if($submission->service_area)
      <p><strong>Service Area:</strong> {{ $submission->service_area }}</p>
      @endif
      <p><strong>Primary Contact:</strong> {{ $submission->primary_contact }}</p>
      <p><strong>Phone:</strong> {{ $submission->phone }}</p>
      <p><strong>Ad Budget Ready:</strong> {{ $submission->ad_budget_ready ? 'Yes' : 'No' }}</p>
      @if($submission->license_original_name)
      <p><strong>License Uploaded:</strong> {{ $submission->license_original_name }}</p>
      @endif
    </div>

    <p>Our team will be in touch within 1–2 business days. In the meantime:</p>

    <p style="margin: 20px 0;">
      &bull; <a href="{{ url('/book') }}" style="color:#c8a84b;">Schedule a strategy session &rarr;</a><br>
      &bull; <a href="{{ url('/#how') }}" style="color:#c8a84b;">How it works &rarr;</a>
    </p>

    @if($submission->rd_referral_interest)
    <div style="background:#f0f7f0; border-left:3px solid #4a7c59; padding:18px 20px; margin:24px 0;">
      <p style="font-size:.82rem; color:#2d5a3d; font-weight:600; margin:0 0 8px; letter-spacing:.05em; text-transform:uppercase;">Research &amp; Development Reference</p>
      <p style="font-size:.85rem; color:#444; line-height:1.7; margin:0 0 10px;">You indicated interest in R&amp;D documentation practices. This is informational only — we are not tax advisors and this is not tax advice. Official IRS resources on Form 6765:</p>
      <p style="font-size:.85rem; color:#444; line-height:1.7; margin:0;">
        &bull; <a href="https://www.irs.gov/instructions/i6765" style="color:#4a7c59;">Form 6765 Instructions — irs.gov</a><br>
        &bull; <a href="https://www.irs.gov/pub/irs-pdf/f6765.pdf" style="color:#4a7c59;">Form 6765 PDF — irs.gov</a>
      </p>
      <p style="font-size:.78rem; color:#777; margin:10px 0 0;">Consult a qualified CPA or tax attorney for guidance specific to your situation.</p>
    </div>
    @endif

    <p>Questions? Reply to this email or reach us at <a href="mailto:{{ config('services.booking.owner_email', 'hello@seoaico.com') }}" style="color:#c8a84b;">{{ config('services.booking.owner_email', 'hello@seoaico.com') }}</a>.</p>
  </div>

  <div class="footer">
    <p>
      <strong style="color:#1a1a1a">SEO AI Co™</strong> &nbsp;&middot;&nbsp; Programmatic AI SEO Systems<br>
      <a href="mailto:hello@seoaico.com">hello@seoaico.com</a>
    </p>
    <p style="margin-top:10px">
      This email was sent to {{ $lead->email }} because you submitted an onboarding form on seoaico.com.<br>
      <a href="{{ url('/') }}">seoaico.com</a>
    </p>
  </div>
</div>
</body>
</html>
