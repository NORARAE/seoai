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
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 16px;line-height:1.3">Your site has gaps AI systems <span style="color:#c8a84b;font-style:italic">can't ignore</span></h1>

    <p style="font-size:14px;color:#555;margin:0 0 16px;line-height:1.65">
      Yesterday you ran a scan on <strong style="color:#333">{{ $scan->url }}</strong> and scored <strong style="color:#c8a84b">{{ $scan->score ?? 0 }}/100</strong>.
    </p>

    <p style="font-size:14px;color:#555;margin:0 0 16px;line-height:1.65">
      Most sites score under 40 on their first scan. Not because the content is bad — because the <em>structural signals</em> AI systems need aren't in place.
    </p>

    <div style="background:#f9f8f5;border-left:3px solid #c8a84b;padding:16px 18px;margin-bottom:20px">
      <p style="font-size:14px;font-style:italic;color:#444;margin:0;line-height:1.65">
        AI systems don't read your site the way a human does. They look for machine-readable signals — structured data layers, direct answer content, entity definitions, and content connectivity. Without these, your site gets overlooked.
      </p>
    </div>

    <p style="font-size:14px;color:#555;margin:0 0 16px;line-height:1.65">
      The three structural categories AI evaluates most heavily:
    </p>

    <ol style="padding-left:20px;margin:0 0 20px">
      <li style="font-size:13px;color:#555;margin-bottom:10px;line-height:1.55"><strong style="color:#333">Machine-Readable Context</strong> — The data layer that tells AI what type of business you are, what you offer, and where you operate. Without it, AI systems make assumptions — or skip you.</li>
      <li style="font-size:13px;color:#555;margin-bottom:10px;line-height:1.55"><strong style="color:#333">Direct Answer Content</strong> — AI systems are built to extract and cite direct responses. Pages with clear, structured answers get cited. General copy doesn't.</li>
      <li style="font-size:13px;color:#555;margin-bottom:10px;line-height:1.55"><strong style="color:#333">Entity Definition Strength</strong> — How clearly your site establishes <em>what</em> your business is, <em>what</em> it offers, and <em>where</em> it operates. Weak entity signals mean AI cannot confidently recommend you.</li>
    </ol>

    <p style="font-size:13px;color:#777;margin:0 0 24px;line-height:1.6">
      Tomorrow I'll show you exactly how far a few structural changes could take your score — and what that means for your market visibility.
    </p>

    <div style="text-align:center;margin:20px 0">
      <a href="{{ \App\Support\EmailUrl::tracked('/checkout/signal-expansion', 'quick-scan-day1', $scan->user_id, $scan->id) }}" style="display:inline-block;background:#c8a84b;color:#080808;font-size:12px;font-weight:600;text-decoration:none;padding:13px 36px;letter-spacing:.08em">Unlock Signal Expansion &mdash; $99</a>
    </div>

    <p style="font-size:12px;color:#bbb;text-align:center;margin:16px 0 0">
      <a href="{{ \App\Support\EmailUrl::tracked('/book', 'quick-scan-day1', $scan->user_id, $scan->id) }}" style="color:#c8a84b;text-decoration:none">Book a free strategy call</a>
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
