<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Next Steps for Activation</title>
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
  .steps-block { margin: 24px 0; }
  .step { display: flex; gap: 16px; margin-bottom: 20px; align-items: flex-start; }
  .step-num { flex-shrink: 0; width: 28px; height: 28px; background: #c8a84b; color: #fff; font-size: .8rem; letter-spacing: .05em; display: flex; align-items: center; justify-content: center; }
  .step-content h4 { font-size: .9rem; font-weight: 600; color: #111; margin: 0 0 4px; }
  .step-content p { font-size: .88rem; color: #555; line-height: 1.6; margin: 0; }
  .cta-block { background: #faf8f4; border: 1px solid #e8e3d8; padding: 24px 28px; margin: 28px 0; text-align: center; }
  .cta-block p { font-size: .92rem; color: #444; line-height: 1.6; margin: 0 0 16px; }
  .cta-block a { display: inline-block; background: #c8a84b; color: #fff; text-decoration: none; padding: 12px 36px; font-size: .88rem; letter-spacing: .08em; }
  .footer { padding: 20px 40px 36px; border-top: 1px solid #e8e3d8; }
  .footer p { font-size: .75rem; color: #999; line-height: 1.6; }
  .footer a { color: #c8a84b; text-decoration: none; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="logo">SEO AI Co™</div>
    <h1>Next steps for<br>{{ $submission->business_name }}.</h1>
    <p>Your review is underway. Here is exactly what happens from this point — no ambiguity.</p>
  </div>

  <div class="divider"></div>

  <div class="body">
    <p>Reviews complete within 2–3 business days. When yours is finalized, you will receive a direct message with your activation status and a clear outline of how we move forward together.</p>

    <div class="steps-block">
      <div class="step">
        <div class="step-num">1</div>
        <div class="step-content">
          <h4>Review Completion</h4>
          <p>Our team finalizes the territory, competitive, and site assessment for your market.</p>
        </div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div class="step-content">
          <h4>Position Confirmation</h4>
          <p>You receive a direct notification with your activation status and an outline of the proposed approach.</p>
        </div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div class="step-content">
          <h4>Onboarding Kickoff</h4>
          <p>Once confirmed, your account is scheduled for kickoff and access is provisioned.</p>
        </div>
      </div>
    </div>

    @if($submission->rd_referral_interest)
    <div style="background:#f0f7f0; border-left:3px solid #4a7c59; padding:18px 20px; margin:24px 0;">
      <p style="font-size:.82rem; color:#2d5a3d; font-weight:600; margin:0 0 8px; letter-spacing:.05em; text-transform:uppercase;">Research &amp; Development Reference</p>
      <p style="font-size:.85rem; color:#444; line-height:1.7; margin:0 0 10px;">This is informational only. We are not tax advisors and this is not tax advice. Official IRS documentation on the R&amp;D tax credit (Form 6765):</p>
      <p style="font-size:.85rem; color:#444; line-height:1.7; margin:0;">
        &bull; <a href="https://www.irs.gov/instructions/i6765" style="color:#4a7c59;">Form 6765 Instructions — irs.gov</a><br>
        &bull; <a href="https://www.irs.gov/pub/irs-pdf/f6765.pdf" style="color:#4a7c59;">Form 6765 PDF — irs.gov</a>
      </p>
      <p style="font-size:.78rem; color:#777; margin:10px 0 0;">Consult a qualified CPA or tax attorney for guidance specific to your situation.</p>
    </div>
    @endif

    <div class="cta-block">
      <p>Haven't scheduled your market opportunity session yet? Do it now — priority onboarding is available for confirmed operators.</p>
      <a href="{{ url('/book') }}">Book Your Market Opportunity Session</a>
    </div>

    <p style="font-size:.85rem; color:#888; line-height:1.7;">Have a question in the meantime? Reply directly to this email or contact us at <a href="mailto:{{ config('services.booking.owner_email', 'hello@seoaico.com') }}" style="color:#c8a84b;">{{ config('services.booking.owner_email', 'hello@seoaico.com') }}</a>.</p>
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
