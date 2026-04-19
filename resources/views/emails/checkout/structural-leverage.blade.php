<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;overflow:hidden;border:1px solid #e8e8e8">

  <div style="background:#080808;padding:28px 24px;text-align:center">
    <span style="font-size:17px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:19px;font-style:italic">AI</span> Co&#8482;</span>
    <p style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.5);margin:6px 0 0">Structural Leverage</p>
  </div>

  <div style="padding:28px 24px">
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 14px;line-height:1.3">Your correction sequence is being built</h1>
    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 20px">We&rsquo;re building your complete priority correction sequence &mdash; every fix prioritized by ROI, every opportunity sized, every gap closed systematically. Your report will be delivered within 5 business days.</p>

    <div style="background:#fafaf8;border-left:3px solid #c8a84b;padding:14px 16px;margin-bottom:20px">
      <div style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:#c8a84b;margin-bottom:6px">What&rsquo;s Included</div>
      <ul style="font-size:13px;color:#444;margin:0;padding:0 0 0 16px;line-height:1.7">
        <li>Priority correction sequence ordered by ROI</li>
        <li>Structural guidance + opportunity sizing</li>
        <li>Full action sequence with implementation notes</li>
      </ul>
    </div>

    <p style="font-size:12px;color:#999;line-height:1.6;margin:20px 0 0">We&rsquo;ll email you when your report is ready. In the meantime, no action is needed on your part.</p>
  </div>

  <div style="background:#fafaf8;padding:20px 24px;border-top:1px solid #e8e8e8;text-align:center">
    <p style="font-size:12px;color:#888;margin:0 0 10px">Want us to implement the corrections for you?</p>
    <a href="{{ \App\Support\EmailUrl::tracked('/checkout/system-activation', 'checkout-structural-leverage') }}" style="display:inline-block;padding:12px 28px;background:#080808;color:#ede8de;font-size:12px;letter-spacing:.1em;text-decoration:none;text-transform:uppercase">Upgrade to System Activation &mdash; $489</a>
  </div>

  <div style="padding:20px 24px;border-top:1px solid #e8e8e8;text-align:center">
    <p style="font-size:11px;color:#bbb;margin:0">&copy; {{ date('Y') }} SEO AI Co&trade; &middot; <a href="{{ url('/unsubscribe/' . urlencode($email)) }}" style="color:#bbb">Unsubscribe</a></p>
  </div>

</div>
</body>
</html>
