<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>What We're Evaluating</title>
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
  .eval-block { background: #faf8f4; border-left: 3px solid #c8a84b; padding: 20px 24px; margin: 24px 0; }
  .eval-block h3 { font-size: .85rem; letter-spacing: .12em; text-transform: uppercase; color: #c8a84b; margin: 0 0 14px; }
  .eval-item { display: flex; align-items: flex-start; margin-bottom: 12px; }
  .eval-item:last-child { margin-bottom: 0; }
  .eval-label { font-size: .88rem; font-weight: 600; color: #111; min-width: 180px; }
  .eval-desc { font-size: .88rem; color: #555; line-height: 1.6; }
  .cta-block { text-align: center; margin: 28px 0; }
  .cta-block a { display: inline-block; background: #c8a84b; color: #fff; text-decoration: none; padding: 12px 32px; font-size: .88rem; letter-spacing: .08em; }
  .footer { padding: 20px 40px 36px; border-top: 1px solid #e8e3d8; }
  .footer p { font-size: .75rem; color: #999; line-height: 1.6; }
  .footer a { color: #c8a84b; text-decoration: none; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="logo">SEO AI Co™</div>
    <h1>What we're evaluating<br>for {{ $submission->business_name }}.</h1>
    <p>This is not a formality. It determines whether your market is a fit and how quickly deployment can begin.</p>
  </div>

  <div class="divider"></div>

  <div class="body">
    <p>Before a new operator is activated, we evaluate four dimensions. Each one affects timeline, strategy, and how aggressively we can build position in your market.</p>

    <div class="eval-block">
      <h3>Review Criteria</h3>

      <div class="eval-item">
        <span class="eval-label">Territory Availability</span>
        <span class="eval-desc">We limit representation in each service area to avoid competing interests. Your region is checked for existing active clients.</span>
      </div>

      <div class="eval-item">
        <span class="eval-label">Competitive Landscape</span>
        <span class="eval-desc">We assess how competitive your market is and whether there is a defensible ranking position available for your category.</span>
      </div>

      <div class="eval-item">
        <span class="eval-label">Site Readiness</span>
        <span class="eval-desc">Your website's current technical foundation — speed, indexability, and structure — affects how quickly results can be delivered.</span>
      </div>

      <div class="eval-item">
        <span class="eval-label">Expansion Potential</span>
        <span class="eval-desc">We evaluate whether your business model and service area can support the growth trajectory SEO can produce over 90–180 days.</span>
      </div>
    </div>

    <p>You don't need to do anything right now. If we need something from you to complete the review, you'll hear from us directly.</p>

    <p>Questions before then? Reply to this email or reach us at <a href="mailto:{{ config('services.booking.owner_email', 'hello@seoaico.com') }}" style="color:#c8a84b;">{{ config('services.booking.owner_email', 'hello@seoaico.com') }}</a>.</p>

    @if($submission->rd_referral_interest)
    <div style="background:#f0f7f0; border-left:3px solid #4a7c59; padding:18px 20px; margin:24px 0;">
      <p style="font-size:.82rem; color:#2d5a3d; font-weight:600; margin:0 0 8px; letter-spacing:.05em; text-transform:uppercase;">Research &amp; Development Note</p>
      <p style="font-size:.85rem; color:#444; line-height:1.7; margin:0 0 10px;">You indicated interest in R&amp;D documentation practices. This is an independent area outside of our market expansion system. We are providing informational links to official IRS resources only &mdash; this is not tax advice and we are not tax advisors.</p>
      <p style="font-size:.85rem; color:#444; line-height:1.7; margin:0;">
        Official IRS resources:<br>
        &bull; <a href="https://www.irs.gov/instructions/i6765" style="color:#4a7c59;">IRS Form 6765 Instructions (irs.gov)</a><br>
        &bull; <a href="https://www.irs.gov/pub/irs-pdf/f6765.pdf" style="color:#4a7c59;">IRS Form 6765 PDF (irs.gov)</a>
      </p>
      <p style="font-size:.78rem; color:#777; margin:10px 0 0;">Consult a qualified CPA or tax attorney for guidance specific to your situation.</p>
    </div>
    @endif
  </div>

  <div class="footer">
    <p>
      <strong style="color:#1a1a1a">SEO AI Co™</strong> &nbsp;&middot;&nbsp; Programmatic AI SEO Systems<br>
      <a href="mailto:hello@seoaico.com">hello@seoaico.com</a>
    </p>
    <p style="margin-top:10px">
      This email was sent to {{ $lead->email }} as part of your onboarding review at seoaico.com.<br>
      <a href="{{ url('/') }}">seoaico.com</a>
    </p>
    <p style="margin-top:10px;font-size:.7rem;color:#bbb">
      This is a follow-up email. <a href="{{ url('/unsubscribe/' . $lead->unsubscribe_token) }}" style="color:#bbb">Unsubscribe</a> to stop receiving these.
    </p>
  </div>
</div>
</body>
</html>
