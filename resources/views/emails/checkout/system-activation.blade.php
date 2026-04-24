<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;overflow:hidden;border:1px solid #e8e8e8">

  <div style="background:#080808;padding:28px 24px;text-align:center">
    <span style="font-size:17px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:19px;font-style:italic">AI</span> Co&#8482;</span>
    <p style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.5);margin:6px 0 0">Guided Execution</p>
  </div>

  <div style="padding:28px 24px">
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 14px;line-height:1.3">Your Guided Execution plan is underway</h1>
    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 20px">We&rsquo;re building your complete system &mdash; competitive positioning, market mapping, and 50+ page structural architecture. You&rsquo;ll receive onboarding instructions within 24 hours.</p>

    <div style="background:#fafaf8;border-left:3px solid #c8a84b;padding:14px 16px;margin-bottom:20px">
      <div style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:#c8a84b;margin-bottom:6px">What&rsquo;s Included</div>
      <ul style="font-size:13px;color:#444;margin:0;padding:0 0 0 16px;line-height:1.7">
        <li>Competitive positioning intelligence</li>
        <li>Market expansion mapping</li>
        <li>50+ page structural architecture</li>
        <li>Full system coverage plan</li>
      </ul>
    </div>

    <div style="background:#f0ede4;padding:16px;text-align:center;margin-bottom:20px">
      <p style="font-size:13px;color:#555;margin:0 0 10px;line-height:1.6"><strong style="color:#333">Next step:</strong> Complete your onboarding so we can begin implementation.</p>
      <a href="{{ \App\Support\EmailUrl::tracked('/onboarding/start?tier=activation', 'checkout-system-activation') }}" style="display:inline-block;padding:12px 28px;background:#080808;color:#ede8de;font-size:12px;letter-spacing:.1em;text-decoration:none;text-transform:uppercase">Start Onboarding</a>
    </div>

    <p style="font-size:12px;color:#999;line-height:1.6;margin:20px 0 0">A strategy advisor will reach out within 24 hours to begin your deployment timeline.</p>
  </div>

  <div style="padding:20px 24px;border-top:1px solid #e8e8e8;text-align:center">
    <p style="font-size:11px;color:#bbb;margin:0 0 4px">&copy; {{ date('Y') }} SEO AI Co&trade; &middot; <a href="{{ url('/unsubscribe/' . urlencode($email)) }}" style="color:#bbb">Unsubscribe</a></p>
    <p style="font-size:10px;color:#ccc;margin:0;line-height:1.5">This is a digital analysis product. Results are not guaranteed. <a href="{{ url('/terms') }}" style="color:#ccc">Terms</a> &middot; <a href="{{ url('/refund-policy') }}" style="color:#ccc">Refund Policy</a></p>
  </div>

</div>
</body>
</html>
