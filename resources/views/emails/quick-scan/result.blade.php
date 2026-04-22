<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;overflow:hidden;border:1px solid #e8e8e8">

  <!-- Header -->
  <div style="background:#080808;padding:28px 24px;text-align:center">
    <span style="font-size:17px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:19px;font-style:italic">AI</span> Co&#8482;</span>
    <p style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.5);margin:6px 0 0">AI Citation Quick Scan</p>
  </div>

  <!-- Score band -->
  @php $score = $scan->score ?? 0; @endphp
  <div style="background:{{ $score >= 70 ? '#0a1a12' : ($score >= 40 ? '#141008' : '#1a0808') }};padding:24px;text-align:center;border-bottom:1px solid #e8e8e8">
    <div style="font-size:56px;font-weight:300;line-height:1;color:{{ $score >= 70 ? '#6aaf90' : ($score >= 40 ? '#c8a84b' : '#c47878') }};font-family:Georgia,serif">{{ $score }}</div>
    <div style="font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:rgba(168,168,160,.6);margin-top:4px">out of 100</div>
    <div style="font-size:14px;color:{{ $score >= 70 ? '#6aaf90' : ($score >= 40 ? '#c8a84b' : '#c47878') }};margin-top:8px;font-style:italic">
      @if($score >= 90) Strong foundation — but incomplete coverage limits full visibility.
      @elseif($score >= 70) Strong signals present — but gaps remain.
      @elseif($score >= 40) AI systems detect your site, but confidence is inconsistent.
      @else AI systems cannot reliably understand or cite your site.
      @endif
    </div>
  </div>

  <!-- Body -->
  <div style="padding:28px 24px">
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 8px;line-height:1.3">Your AI Citation Score: {{ $score }}/100</h1>
    <p style="font-size:13px;color:#777;margin:0 0 20px;line-height:1.6">Scan completed for: <strong style="color:#333">{{ $scan->url }}</strong></p>

    @if($scan->fastest_fix)
    <div style="background:#fafaf8;border-left:3px solid #c8a84b;padding:14px 16px;margin-bottom:20px">
      <div style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:#c8a84b;margin-bottom:6px">Your Fastest Fix</div>
      <p style="font-size:13px;color:#444;margin:0;line-height:1.6">{{ $scan->fastest_fix }}</p>
    </div>
    @endif

    @if($scan->issues && count($scan->issues) > 0)
    <div style="margin-bottom:20px">
      <p style="font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:#999;margin:0 0 10px">Gaps Detected</p>
      @foreach(array_slice($scan->issues, 0, 3) as $issue)
      <div style="display:flex;gap:10px;margin-bottom:8px;padding:10px 12px;background:#fdf5f5;border:1px solid #f0e0e0">
        <span style="color:#c47878;flex-shrink:0;font-size:12px;margin-top:1px">✕</span>
        <span style="font-size:13px;color:#555;line-height:1.55">{{ $issue }}</span>
      </div>
      @endforeach
      @if(count($scan->issues) > 3)
      <p style="font-size:12px;color:#bbb;margin:8px 0 0;font-style:italic">+ {{ count($scan->issues) - 3 }} additional gaps identified — full analysis available with upgrade.</p>
      @endif
    </div>
    @endif

    @if($scan->strengths && count($scan->strengths) > 0)
    <div style="margin-bottom:24px">
      <p style="font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:#999;margin:0 0 10px">Signals Detected</p>
      @foreach(array_slice($scan->strengths, 0, 3) as $strength)
      <div style="display:flex;gap:10px;margin-bottom:8px;padding:10px 12px;background:#f5fdf8;border:1px solid #e0f0e8">
        <span style="color:#6aaf90;flex-shrink:0;font-size:12px;margin-top:1px">✓</span>
        <span style="font-size:13px;color:#555;line-height:1.55">{{ $strength }}</span>
      </div>
      @endforeach
    </div>
    @endif

    <!-- CTA -->
    <div style="text-align:center;margin:28px 0 20px;padding:20px;background:#f9f8f5;border:1px solid #ede8de">
      <p style="font-size:14px;font-weight:500;color:#222;margin:0 0 6px">You&rsquo;ve only unlocked the first layer.</p>
      <p style="font-size:12px;color:#888;margin:0 0 16px;line-height:1.5">The next layer maps every gap ranked by revenue impact &mdash; so you know exactly what to fix first.</p>
      <a href="{{ \App\Support\EmailUrl::tracked('/checkout/signal-expansion', 'quick-scan-result', $scan->user_id, $scan->id) }}" style="display:inline-block;background:#c8a84b;color:#080808;font-size:12px;font-weight:600;text-decoration:none;padding:12px 32px;letter-spacing:.08em">Unlock Signal Analysis &mdash; $99</a>
    </div>

    <p style="font-size:12px;color:#bbb;text-align:center;margin:0">
      <a href="{{ \App\Support\EmailUrl::tracked('/quick-scan', 'quick-scan-result', $scan->user_id, $scan->id) }}" style="color:#c8a84b">Scan another URL</a> &nbsp;&middot;&nbsp; <a href="{{ \App\Support\EmailUrl::tracked('/book', 'quick-scan-result', $scan->user_id, $scan->id) }}" style="color:#c8a84b">Book a free strategy call</a>
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
