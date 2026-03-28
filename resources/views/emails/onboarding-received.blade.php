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
    <div class="logo">SEO<strong>AI</strong>co</div>
    <h1>Onboarding received,<br>{{ $lead->name }}.</h1>
    <p>Thank you — your intake form has been submitted. Our team will review your information and be in touch within 1–2 business days.</p>
  </div>

  <div class="divider"></div>

  <div class="body">
    <p>Here's a summary of what you submitted:</p>

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
      <p><strong>License Uploaded:</strong> {{ $submission->license_original_name ?? 'Yes' }}</p>
    </div>

    <p>If you have questions before then, reply to this email or reach us at <a href="mailto:{{ config('services.booking.owner_email', 'hello@seoaico.com') }}">{{ config('services.booking.owner_email', 'hello@seoaico.com') }}</a>.</p>

    <p>We look forward to working with you.</p>
  </div>

  <div class="footer">
    <p>
      This email was sent to {{ $lead->email }} because you completed an onboarding form on seoaico.com.<br>
      <a href="{{ url('/') }}">seoaico.com</a>
    </p>
  </div>
</div>
</body>
</html>
