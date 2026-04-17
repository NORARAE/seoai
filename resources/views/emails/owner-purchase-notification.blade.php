<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;overflow:hidden;border:1px solid #e8e8e8">

  <div style="background:#080808;padding:28px 24px;text-align:center">
    <span style="font-size:17px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:19px;font-style:italic">AI</span> Co&#8482;</span>
    <p style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.5);margin:6px 0 0">Purchase Alert</p>
  </div>

  <div style="padding:28px 24px">
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 14px;line-height:1.3">New Purchase: {{ $amount }}</h1>

    <table style="width:100%;font-size:13px;color:#333;line-height:1.8;border-collapse:collapse">
      <tr><td style="padding:4px 8px 4px 0;color:#999;white-space:nowrap;vertical-align:top">Product</td><td style="padding:4px 0">{{ $tierName }}</td></tr>
      <tr><td style="padding:4px 8px 4px 0;color:#999;white-space:nowrap;vertical-align:top">Amount</td><td style="padding:4px 0">{{ $amount }}</td></tr>
      <tr><td style="padding:4px 8px 4px 0;color:#999;white-space:nowrap;vertical-align:top">Buyer Email</td><td style="padding:4px 0">{{ $scan->email }}</td></tr>
      <tr><td style="padding:4px 8px 4px 0;color:#999;white-space:nowrap;vertical-align:top">Domain</td><td style="padding:4px 0">{{ $scan->domain ?? $scan->url ?? '—' }}</td></tr>
      <tr><td style="padding:4px 8px 4px 0;color:#999;white-space:nowrap;vertical-align:top">Scan ID</td><td style="padding:4px 0">#{{ $scan->id }}</td></tr>
      @if($scan->user_id)<tr><td style="padding:4px 8px 4px 0;color:#999;white-space:nowrap;vertical-align:top">User ID</td><td style="padding:4px 0">#{{ $scan->user_id }}</td></tr>@endif
      <tr><td style="padding:4px 8px 4px 0;color:#999;white-space:nowrap;vertical-align:top">Stripe Session</td><td style="padding:4px 0;word-break:break-all;font-size:11px">{{ $scan->stripe_session_id ?? $scan->upgrade_stripe_session_id ?? '—' }}</td></tr>
      <tr><td style="padding:4px 8px 4px 0;color:#999;white-space:nowrap;vertical-align:top">Time</td><td style="padding:4px 0">{{ now()->format('M j, Y g:i A T') }}</td></tr>
    </table>
  </div>

  <div style="padding:20px 24px;border-top:1px solid #e8e8e8;text-align:center">
    <p style="font-size:11px;color:#bbb;margin:0">&copy; {{ date('Y') }} SEO AI Co&trade; &middot; Internal Alert</p>
  </div>

</div>
</body>
</html>
