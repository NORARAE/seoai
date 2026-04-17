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
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 14px;line-height:1.3">You&rsquo;re leaving visibility on the table</h1>

    <p style="font-size:14px;color:#555;line-height:1.7;margin:0 0 16px">Your scan of <strong style="color:#111">{{ parse_url($scan->url, PHP_URL_HOST) }}</strong> revealed missing signals that directly impact how AI systems evaluate your site.</p>

    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 6px">These gaps affect:</p>
    <ul style="font-size:13px;color:#555;line-height:1.8;padding-left:20px;margin:0 0 16px">
      <li>AI retrieval &mdash; whether your content gets surfaced</li>
      <li>Search positioning &mdash; where you rank vs. competitors</li>
      <li>Citation probability &mdash; how often AI systems reference you</li>
    </ul>

    @if(!empty($scan->issues) && is_array($scan->issues))
    <div style="background:#fafaf9;border:1px solid #e8e8e8;padding:14px 16px;margin:0 0 16px">
      <p style="font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:#999;margin:0 0 8px">From your scan:</p>
      @foreach(array_slice($scan->issues, 0, 2) as $issue)
      <p style="font-size:13px;color:#555;line-height:1.5;margin:0 0 4px;padding-left:12px;border-left:2px solid #c87878">{{ $issue }}</p>
      @endforeach
    </div>
    @endif

    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 24px">Fixing these unlocks your next level of visibility.</p>

    <div style="text-align:center;margin:24px 0">
      <a href="{{ route('quick-scan.upgrade') }}?plan=diagnostic&scan_id={{ $scan->id }}" style="display:inline-block;padding:14px 36px;background:#080808;color:#ede8de;font-size:13px;letter-spacing:.1em;text-decoration:none;text-transform:uppercase">Run Full Signal Analysis &mdash; $99</a>
    </div>

    <p style="font-size:12px;color:#999;line-height:1.6;margin:20px 0 0;text-align:center">Every day without corrections, competitors lock in positions.</p>
  </div>

  <div style="padding:20px 24px;border-top:1px solid #e8e8e8;text-align:center">
    <p style="font-size:11px;color:#bbb;margin:0">&copy; {{ date('Y') }} SEO AI Co&trade; &middot; <a href="{{ url('/unsubscribe/' . urlencode($scan->email)) }}" style="color:#bbb">Unsubscribe</a></p>
  </div>

</div>
</body>
</html>
