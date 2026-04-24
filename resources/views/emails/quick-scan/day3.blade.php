<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;overflow:hidden;border:1px solid #e8e8e8">

  <!-- Header -->
  <div style="background:#080808;padding:28px 24px;text-align:center">
    <span style="font-size:17px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:19px;font-style:italic">AI</span> Co&#8482;</span>
    <p style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.5);margin:6px 0 0">AI Citation</p>
  </div>

  <!-- Body -->
  <div style="padding:32px 28px">
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 16px;line-height:1.3">Your competitors are <span style="color:#c8a84b;font-style:italic">already doing this</span></h1>

    <p style="font-size:14px;color:#555;margin:0 0 16px;line-height:1.65">
      Your scan of <strong style="color:#333">{{ $scan->url }}</strong> scored <strong style="color:#c8a84b">{{ $scan->score ?? 0 }}/100</strong>. You've seen the problems. Here's how we fix them — fast.
    </p>

    <div style="background:#1a1a1a;border-radius:2px;overflow:hidden;margin-bottom:24px">
      <!-- Signal Analysis -->
      <div style="padding:20px 22px;border-bottom:1px solid #2a2a2a">
        <p style="font-size:9px;letter-spacing:.16em;text-transform:uppercase;color:rgba(200,168,75,.5);margin:0 0 6px">Layer 2</p>
        <p style="font-size:16px;font-weight:500;color:#ede8de;margin:0 0 4px">Signal Analysis</p>
        <p style="font-size:22px;font-weight:300;color:#c8a84b;margin:0 0 10px">$99 <span style="font-size:13px;color:#777">one-time</span></p>
        <ul style="padding-left:16px;margin:0">
          <li style="font-size:12px;color:#aaa;margin-bottom:6px;line-height:1.55">Complete visibility mapping across AI systems</li>
          <li style="font-size:12px;color:#aaa;margin-bottom:6px;line-height:1.55">Every gap ranked by revenue impact</li>
          <li style="font-size:12px;color:#aaa;margin-bottom:0;line-height:1.55">Dashboard-based signal insights</li>
        </ul>
        <div style="margin-top:14px">
          <a href="{{ \App\Support\EmailUrl::tracked('/checkout/signal-expansion', 'quick-scan-day3', $scan->user_id, $scan->id) }}" style="display:inline-block;border:1px solid #c8a84b;color:#c8a84b;font-size:11px;font-weight:600;text-decoration:none;padding:9px 24px;letter-spacing:.08em">Upgrade to Signal Analysis &rarr;</a>
        </div>
      </div>

      <!-- Action Plan - Featured -->
      <div style="padding:20px 22px;background:#1e1a10;border-bottom:1px solid #2a2a2a">
        <p style="font-size:9px;letter-spacing:.16em;text-transform:uppercase;color:rgba(200,168,75,.7);margin:0 0 4px">Layer 3 &mdash; Most Popular</p>
        <p style="font-size:16px;font-weight:500;color:#ede8de;margin:0 0 4px">Action Plan</p>
        <p style="font-size:22px;font-weight:300;color:#c8a84b;margin:0 0 10px">$249 <span style="font-size:13px;color:#777">one-time</span></p>
        <ul style="padding-left:16px;margin:0">
          <li style="font-size:12px;color:#c9c09a;margin-bottom:6px;line-height:1.55">Everything in Signal Analysis</li>
          <li style="font-size:12px;color:#c9c09a;margin-bottom:6px;line-height:1.55">Priority correction sequence ordered by ROI</li>
          <li style="font-size:12px;color:#c9c09a;margin-bottom:0;line-height:1.55">Full action sequence with implementation notes</li>
        </ul>
        <div style="margin-top:14px">
          <a href="{{ \App\Support\EmailUrl::tracked('/checkout/structural-leverage', 'quick-scan-day3', $scan->user_id, $scan->id) }}" style="display:inline-block;background:#c8a84b;color:#080808;font-size:11px;font-weight:600;text-decoration:none;padding:10px 28px;letter-spacing:.08em">Resolve Structural Gaps &rarr;</a>
        </div>
      </div>

      <!-- Guided Execution -->
      <div style="padding:20px 22px">
        <p style="font-size:9px;letter-spacing:.16em;text-transform:uppercase;color:rgba(200,168,75,.4);margin:0 0 6px">Layer 4</p>
        <p style="font-size:16px;font-weight:500;color:#ede8de;margin:0 0 4px">Guided Execution</p>
        <p style="font-size:22px;font-weight:300;color:#c8a84b;margin:0 0 10px">$489+ <span style="font-size:13px;color:#777">one-time</span></p>
        <ul style="padding-left:16px;margin:0">
          <li style="font-size:12px;color:#aaa;margin-bottom:6px;line-height:1.55">Everything in Action Plan</li>
          <li style="font-size:12px;color:#aaa;margin-bottom:6px;line-height:1.55">Competitive positioning + market mapping</li>
          <li style="font-size:12px;color:#aaa;margin-bottom:0;line-height:1.55">50+ page structural architecture deployed</li>
        </ul>
        <div style="margin-top:14px">
          <a href="{{ \App\Support\EmailUrl::tracked('/checkout/system-activation', 'quick-scan-day3', $scan->user_id, $scan->id) }}" style="display:inline-block;border:1px solid rgba(200,168,75,.4);color:rgba(200,168,75,.7);font-size:11px;font-weight:600;text-decoration:none;padding:9px 24px;letter-spacing:.08em">Activate Full System &rarr;</a>
        </div>
      </div>
    </div>

    <div style="background:#f9f8f5;border-left:3px solid #c8a84b;padding:14px 18px;margin-bottom:20px">
      <p style="font-size:13px;font-style:italic;color:#555;margin:0;line-height:1.6">
        "The businesses AI cites by default are the ones who structured their content for it. The longer you wait, the more entrenched their advantage becomes."
      </p>
    </div>

    <div style="text-align:center;margin:20px 0">
      <a href="{{ \App\Support\EmailUrl::tracked('/checkout/signal-expansion', 'quick-scan-day3', $scan->user_id, $scan->id) }}" style="display:inline-block;background:#c8a84b;color:#080808;font-size:12px;font-weight:600;text-decoration:none;padding:13px 36px;letter-spacing:.08em">Start with Signal Analysis &mdash; $99</a>
    </div>

    <p style="font-size:12px;color:#bbb;text-align:center;margin:16px 0 0">
      Questions? <a href="{{ \App\Support\EmailUrl::tracked('/book', 'quick-scan-day3', $scan->user_id, $scan->id) }}" style="color:#c8a84b;text-decoration:none">Book a free 15-min call</a>
    </p>
  </div>

  <!-- Footer -->
  <div style="background:#f5f5f4;padding:16px 24px;text-align:center;border-top:1px solid #e8e8e8">
    <p style="font-size:11px;color:#ccc;margin:0 0 6px">You&rsquo;re receiving this because you scanned {{ $scan->url }}</p>
    <p style="font-size:11px;color:#bbb;margin:0">&copy; {{ date('Y') }} SEO AI Co&trade; &middot; <a href="{{ url('/unsubscribe/' . urlencode($scan->email)) }}" style="color:#bbb">Unsubscribe</a></p>
  </div>

</div>
</body>
</html>
