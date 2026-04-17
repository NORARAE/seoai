<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;overflow:hidden;border:1px solid #e8e8e8">

  <div style="background:#080808;padding:28px 24px;text-align:center">
    <span style="font-size:17px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:19px;font-style:italic">AI</span> Co&#8482;</span>
    <p style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.5);margin:6px 0 0">AI Citation Scan</p>
  </div>

  <div style="padding:28px 24px">
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 14px;line-height:1.3">Your visibility gaps are still open</h1>

    <p style="font-size:14px;color:#555;line-height:1.7;margin:0 0 16px">We completed your scan of <strong style="color:#111">{{ parse_url($scan->url, PHP_URL_HOST) }}</strong> but haven&rsquo;t seen you take the next step.</p>

    @if($scan->score !== null)
    <div style="text-align:center;margin:16px 0;padding:16px;background:#fafaf9;border:1px solid #e8e8e8">
      <p style="font-size:28px;font-weight:300;color:#111;margin:0;line-height:1">{{ $scan->score }}<span style="font-size:14px;color:#999">/100</span></p>
      <p style="font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:#999;margin:4px 0 0">Your Current Score</p>
    </div>
    @endif

    @if(!empty($scan->issues) && is_array($scan->issues))
    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 8px">Issues we found:</p>
    <ul style="font-size:13px;color:#555;line-height:1.8;padding-left:20px;margin:0 0 16px">
      @foreach(array_slice($scan->issues, 0, 2) as $issue)
      <li>{{ $issue }}</li>
      @endforeach
    </ul>
    @endif

    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 24px">While these remain unaddressed, competitors who fix theirs are pulling ahead. Every week without corrections makes the gap harder to close.</p>

    <div style="text-align:center;margin:24px 0">
      <a href="{{ url('/dashboard') }}" style="display:inline-block;padding:14px 36px;background:#080808;color:#ede8de;font-size:13px;letter-spacing:.1em;text-decoration:none;text-transform:uppercase">View Your Dashboard</a>
    </div>

    <p style="font-size:12px;color:#999;line-height:1.6;margin:20px 0 0;text-align:center">Your results are still available. Take the next step when you&rsquo;re ready.</p>
  </div>

  <div style="padding:20px 24px;border-top:1px solid #e8e8e8;text-align:center">
    <p style="font-size:11px;color:#bbb;margin:0">&copy; {{ date('Y') }} SEO AI Co&trade; &middot; <a href="{{ url('/unsubscribe/' . urlencode($scan->email)) }}" style="color:#bbb">Unsubscribe</a></p>
  </div>

</div>
</body>
</html>
