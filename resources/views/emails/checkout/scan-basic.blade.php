<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;overflow:hidden;border:1px solid #e8e8e8">

  <div style="background:#080808;padding:28px 24px;text-align:center">
    <span style="font-size:17px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:19px;font-style:italic">AI</span> Co&#8482;</span>
    <p style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.5);margin:6px 0 0">AI Citation Scan — Purchase Confirmed</p>
  </div>

  <div style="padding:28px 24px">
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 14px;line-height:1.3">Your scan is processing</h1>
    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 16px">Your <strong style="color:#333">$2 AI Citation Scan</strong> has been confirmed. Results are typically ready within seconds.</p>

    <div style="background:#fafaf8;border:1px solid rgba(200,168,75,.25);padding:16px 18px;margin-bottom:24px">
      <p style="font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:#c8a84b;margin:0 0 6px">What you unlocked</p>
      <p style="font-size:14px;color:#333;margin:0 0 6px;font-weight:500">AI Citation Quick Scan &mdash; Layer 1</p>
      <ul style="margin:6px 0 0;padding-left:18px">
        <li style="font-size:13px;color:#555;line-height:1.65;margin-bottom:3px">AI visibility score (0&ndash;100)</li>
        <li style="font-size:13px;color:#555;line-height:1.65;margin-bottom:3px">Extraction signal breakdown</li>
        <li style="font-size:13px;color:#555;line-height:1.65;margin-bottom:3px">Top constraint + fastest fix</li>
        <li style="font-size:13px;color:#555;line-height:1.65">Saved to your account dashboard</li>
      </ul>
    </div>

    @if(isset($reportUrl) && $reportUrl)
    <div style="text-align:center;margin:24px 0">
      <a href="{{ $reportUrl }}" style="display:inline-block;padding:14px 32px;background:#080808;color:#ede8de;font-size:13px;letter-spacing:.1em;text-decoration:none;text-transform:uppercase">View My Report</a>
    </div>
    @endif

    <div style="text-align:center;margin:16px 0">
      <a href="{{ url('/dashboard') }}" style="display:inline-block;padding:12px 28px;background:transparent;color:#c8a84b;font-size:13px;letter-spacing:.08em;text-decoration:none;text-transform:uppercase;border:1px solid rgba(200,168,75,.5)">View My Dashboard</a>
    </div>

    <p style="font-size:12px;color:#888;line-height:1.7;margin:20px 0 0">If you don&rsquo;t see your report yet, it&rsquo;s still processing. Log in to your dashboard and it will appear automatically once complete.</p>

    <p style="font-size:12px;color:#888;line-height:1.7;margin:16px 0 0">Questions? Reply to this email or contact us at <a href="mailto:hello@seoaico.com" style="color:#c8a84b;text-decoration:none">hello@seoaico.com</a></p>
  </div>

  <div style="padding:20px 24px;border-top:1px solid #e8e8e8;text-align:center">
    <p style="font-size:11px;color:#bbb;margin:0 0 4px">&copy; {{ date('Y') }} SEO AI Co&trade; &middot; <a href="{{ url('/unsubscribe/' . urlencode($email)) }}" style="color:#bbb">Unsubscribe</a></p>
    <p style="font-size:10px;color:#ccc;margin:0;line-height:1.5">This is a digital analysis product. Results are not guaranteed. <a href="{{ url('/terms') }}" style="color:#ccc">Terms</a> &middot; <a href="{{ url('/refund-policy') }}" style="color:#ccc">Refund Policy</a></p>
  </div>

</div>
</body>
</html>
