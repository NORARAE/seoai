<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;overflow:hidden;border:1px solid #e8e8e8">

  <div style="background:#080808;padding:28px 24px;text-align:center">
    <span style="font-size:17px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:19px;font-style:italic">AI</span> Co&#8482;</span>
    <p style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.5);margin:6px 0 0">System Implementation</p>
  </div>

  <div style="padding:28px 24px">
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 14px;line-height:1.3">We can implement this for you</h1>

    <p style="font-size:14px;color:#555;line-height:1.7;margin:0 0 16px">You now have the full map of what&rsquo;s missing from <strong style="color:#111">{{ parse_url($scan->url, PHP_URL_HOST) }}</strong>. The next step is execution.</p>

    @if($tierSlug === 'signal-expansion')
    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 6px">You&rsquo;ve mapped the gaps. The next step is structural correction:</p>
    <ul style="font-size:13px;color:#555;line-height:1.8;padding-left:20px;margin:0 0 16px">
      <li>Priority correction sequence ordered by ROI</li>
      <li>Structural guidance + opportunity sizing</li>
      <li>Full action sequence with implementation notes</li>
    </ul>

    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 24px">Without corrections, the gaps you&rsquo;ve identified stay open &mdash; and competitors who fix theirs lock in an advantage you can&rsquo;t reclaim.</p>

    <div style="text-align:center;margin:24px 0">
      <a href="{{ \App\Support\EmailUrl::tracked('/checkout/structural-leverage', 'upgrade-implement-offer', $scan->user_id, $scan->id) }}" style="display:inline-block;padding:14px 36px;background:#080808;color:#ede8de;font-size:13px;letter-spacing:.1em;text-decoration:none;text-transform:uppercase">Resolve Structural Gaps &mdash; $249</a>
    </div>
    @else
    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 6px">With System Activation, we:</p>
    <ul style="font-size:13px;color:#555;line-height:1.8;padding-left:20px;margin:0 0 16px">
      <li>Map your competitive position</li>
      <li>Build your coverage architecture</li>
      <li>Deploy corrections in priority order</li>
    </ul>

    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 24px">Most sites stop at insight. The ones that move forward lock in positions that competitors can&rsquo;t reclaim.</p>

    <div style="text-align:center;margin:24px 0">
      <a href="{{ \App\Support\EmailUrl::tracked('/checkout/system-activation', 'upgrade-implement-offer', $scan->user_id, $scan->id) }}" style="display:inline-block;padding:14px 36px;background:#080808;color:#ede8de;font-size:13px;letter-spacing:.1em;text-decoration:none;text-transform:uppercase">Activate Full System &mdash; $489</a>
    </div>
    @endif

    <p style="font-size:12px;color:#999;line-height:1.6;margin:20px 0 0;text-align:center">Or if you&rsquo;d prefer guidance first:</p>

    <div style="text-align:center;margin:12px 0 0">
      <a href="{{ \App\Support\EmailUrl::tracked('/booking', 'upgrade-implement-offer', $scan->user_id, $scan->id) }}" style="font-size:12px;color:#c8a84b;text-decoration:none;letter-spacing:.06em">Book a Strategy Session &rarr;</a>
    </div>
  </div>

  <div style="padding:20px 24px;border-top:1px solid #e8e8e8;text-align:center">
    <p style="font-size:11px;color:#bbb;margin:0">&copy; {{ date('Y') }} SEO AI Co&trade; &middot; <a href="{{ url('/unsubscribe/' . urlencode($scan->email)) }}" style="color:#bbb">Unsubscribe</a></p>
  </div>

</div>
</body>
</html>
