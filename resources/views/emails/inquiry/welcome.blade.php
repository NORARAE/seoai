<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>We received your inquiry</title>
<style>
  body{margin:0;padding:0;background:#0d0d0d;font-family:'DM Sans',Arial,sans-serif;color:#d4d4d4}
  .wrap{max-width:560px;margin:48px auto;background:#111;border:1px solid #222;border-radius:4px;overflow:hidden}
  .header{background:#0a0a0a;padding:32px 40px;border-bottom:1px solid #1e1e1e;text-align:center}
  .logo-seo{font-family:Arial,sans-serif;font-weight:300;font-size:1.15rem;letter-spacing:.06em;color:#fff}
  .logo-ai{font-family:Georgia,serif;font-weight:700;font-size:1.35rem;color:#c8a84b;letter-spacing:.02em;font-style:italic}
  .logo-co{font-family:Arial,sans-serif;font-weight:300;font-size:1rem;color:rgba(255,255,255,.4);letter-spacing:.04em}
  .body{padding:40px}
  h1{margin:0 0 16px;font-size:1.2rem;font-weight:500;color:#f0f0f0;letter-spacing:.02em}
  p{margin:0 0 16px;font-size:.93rem;line-height:1.7;color:#a8a8a8}
  .detail-block{background:#0d0d0d;border:1px solid #1e1e1e;border-radius:3px;padding:20px 24px;margin:24px 0}
  .detail-row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #1a1a1a;font-size:.85rem}
  .detail-row:last-child{border-bottom:none}
  .detail-label{color:#666;text-transform:uppercase;letter-spacing:.1em;font-size:.75rem}
  .detail-value{color:#d4d4d4;text-align:right}
  .footer{padding:24px 40px;border-top:1px solid #1a1a1a;text-align:center;font-size:.78rem;color:#444}
  .footer a{color:#c8a84b;text-decoration:none}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </div>
  <div class="body">
    <h1>Thank you, {{ $inquiry->name }}.</h1>
    <p>We've received your licensing inquiry and will review it personally. We respond to every qualified application — typically within 1–2 business days.</p>
    <p>Here's a summary of what you submitted:</p>

    <div class="detail-block">
      <div class="detail-row">
        <span class="detail-label">Company</span>
        <span class="detail-value">{{ $inquiry->company }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">License Tier</span>
        <span class="detail-value">{{ $inquiry->tierLabel() }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Type</span>
        <span class="detail-value">{{ $inquiry->typeLabel() }}</span>
      </div>
      @if($inquiry->website)
      <div class="detail-row">
        <span class="detail-label">Website</span>
        <span class="detail-value">{{ $inquiry->website }}</span>
      </div>
      @endif
    </div>

    <p>In the meantime, if you have any questions you can reply to this email or reach us at <a href="mailto:hello@seoaico.com" style="color:#c8a84b;text-decoration:none">hello@seoaico.com</a>.</p>
    <p style="color:#666;font-size:.85rem">This is a confirmation that your message was received — not an approval. We will be in touch once we have reviewed your application.</p>
  </div>
  <div class="footer">
    &copy; {{ date('Y') }} SEOAIco &nbsp;·&nbsp; <a href="https://seoaico.com">seoaico.com</a>
  </div>
</div>
</body>
</html>
