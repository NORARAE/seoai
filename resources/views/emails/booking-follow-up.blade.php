<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;border-radius:8px;overflow:hidden">

  {{-- Header --}}
  <div style="background:#080808;padding:32px 28px;text-align:center">
    <span style="font-size:18px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:20px">AI</span> Co™</span>
  </div>

  {{-- Body --}}
  <div style="padding:32px 28px">
    <h1 style="font-size:22px;font-weight:400;color:#111;margin:0 0 8px">The work starts now.</h1>
    <p style="font-size:14px;color:#666;margin:0 0 24px;line-height:1.6">
      Your session is done. Most businesses stop here. That gap is exactly where your advantage builds.
    </p>

    {{-- What's underway --}}
    <div style="background:#f9f9f7;border-radius:6px;padding:20px 24px;margin:0 0 24px">
      <p style="font-size:13px;font-weight:600;color:#333;margin:0 0 12px;text-transform:uppercase;letter-spacing:.06em">What's underway</p>
      <table cellpadding="0" cellspacing="0" style="font-size:13px;color:#555;line-height:1.8">
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px">→</td>
          <td style="padding-bottom:8px">Territory mapping and coverage analysis for your market</td>
        </tr>
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px">→</td>
          <td style="padding-bottom:8px">A summary of the recommended approach — delivered within 2 business days</td>
        </tr>
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px">→</td>
          <td>Once you confirm direction, infrastructure deployment begins</td>
        </tr>
      </table>
    </div>

    {{-- Positioning reminder --}}
    <div style="border-left:3px solid #c8a84b;padding:14px 18px;margin:0 0 24px;background:#fffef9">
      <p style="font-size:13px;color:#555;line-height:1.7;margin:0">
        Most businesses in your market are not running a structured system. You are. That is the compounding advantage — and it starts from this point, not later.
      </p>
    </div>

    <p style="font-size:13px;color:#888;line-height:1.6;margin:0 0 24px">
      Want to move faster or have a question? Reply directly — we respond same day.
    </p>

    <div style="text-align:center;margin:0 0 8px">
      <a href="{{ url('/book') }}" style="display:inline-block;background:#c8a84b;color:#080808;font-size:14px;font-weight:600;text-decoration:none;padding:14px 36px;border-radius:6px;letter-spacing:.04em">Book Your Next Session</a>
    </div>

    <hr style="border:none;border-top:1px solid #eee;margin:24px 0">
    <p style="font-size:12px;color:#aaa;margin:0;line-height:1.6">
      You're receiving this because you completed a strategy session at <a href="{{ url('/') }}" style="color:#c8a84b;text-decoration:none">seoaico.com</a>.
    </p>
    @if(isset($lead) && $lead->unsubscribe_token)
    <p style="font-size:11px;color:#ccc;margin:8px 0 0;line-height:1.6">
      <a href="{{ url('/unsubscribe/' . $lead->unsubscribe_token) }}" style="color:#ccc;text-decoration:none">Unsubscribe</a> from follow-up emails.
    </p>
    @endif
  </div>

</div>
</body></html>
