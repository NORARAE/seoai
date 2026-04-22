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
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 14px;line-height:1.3">Let&rsquo;s activate your system</h1>

    <p style="font-size:14px;color:#555;line-height:1.7;margin:0 0 16px">You&rsquo;ve invested in the full intelligence layer for <strong style="color:#111">{{ parse_url($scan->url, PHP_URL_HOST) }}</strong>. Now it&rsquo;s time to put it to work.</p>

    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 6px">Your next steps:</p>

    <div style="background:#fafaf9;border:1px solid #e8e8e8;padding:16px;margin:0 0 16px">
      <p style="font-size:13px;color:#111;line-height:1.6;margin:0 0 8px"><strong>1.</strong> Complete your onboarding &mdash; tell us about your market, services, and target locations.</p>
      <p style="font-size:13px;color:#111;line-height:1.6;margin:0 0 8px"><strong>2.</strong> Schedule your strategy call &mdash; we&rsquo;ll review your data and build your deployment plan.</p>
      <p style="font-size:13px;color:#111;line-height:1.6;margin:0"><strong>3.</strong> We build and deploy &mdash; entity architecture, content infrastructure, coverage defense.</p>
    </div>

    <div style="text-align:center;margin:24px 0">
      <a href="{{ \App\Support\EmailUrl::tracked('/dashboard', 'high-tier-activation', $user->id, $scan->id) }}" style="display:inline-block;padding:14px 36px;background:#080808;color:#ede8de;font-size:13px;letter-spacing:.1em;text-decoration:none;text-transform:uppercase;margin-bottom:10px">Complete Onboarding</a>
    </div>

    <div style="text-align:center;margin:0 0 16px">
      <a href="{{ \App\Support\EmailUrl::tracked('/booking', 'high-tier-activation', $user->id, $scan->id) }}" style="font-size:13px;color:#c8a84b;text-decoration:none;letter-spacing:.08em;text-transform:uppercase">Schedule Call &rarr;</a>
    </div>

    <p style="font-size:12px;color:#999;line-height:1.6;margin:16px 0 0;text-align:center">Limited deployment capacity each month. Early onboarding gets priority.</p>
  </div>

  <div style="padding:20px 24px;border-top:1px solid #e8e8e8;text-align:center">
    <p style="font-size:11px;color:#bbb;margin:0">&copy; {{ date('Y') }} SEO AI Co&trade; &middot; <a href="{{ url('/unsubscribe/' . urlencode($user->email)) }}" style="color:#bbb">Unsubscribe</a></p>
  </div>

</div>
</body>
</html>
