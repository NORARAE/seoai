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
    <h1 style="font-size:22px;font-weight:400;color:#111;margin:0 0 8px">Thanks for the session</h1>
    <p style="font-size:14px;color:#666;margin:0 0 24px;line-height:1.6">
      We hope your strategy session was valuable. Here's a quick recap of where things stand and what to expect next.
    </p>

    {{-- Session recap --}}
    <table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:14px;color:#333;margin-bottom:24px">
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Session</td>
        <td>{{ $booking->consultType->name }}</td>
      </tr>
      <tr>
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Date</td>
        <td>{{ $booking->preferred_date->format('l, F j, Y') }}</td>
      </tr>
    </table>

    {{-- Next steps placeholder --}}
    <div style="background:#f9f9f7;border-radius:6px;padding:20px 24px;margin:0 0 24px">
      <p style="font-size:13px;font-weight:600;color:#333;margin:0 0 12px;text-transform:uppercase;letter-spacing:.06em">What comes next</p>
      <table cellpadding="0" cellspacing="0" style="font-size:13px;color:#555;line-height:1.7">
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px">→</td>
          <td>We'll prepare your territory map and coverage plan</td>
        </tr>
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px">→</td>
          <td>You'll receive a summary of our recommendations within 2 business days</td>
        </tr>
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px">→</td>
          <td>Once approved, infrastructure build begins</td>
        </tr>
      </table>
    </div>

    <p style="font-size:13px;color:#888;line-height:1.6;margin:0 0 24px">
      Questions or want to move faster? Reply to this email — we're here.
    </p>

    <div style="text-align:center;margin:0 0 8px">
      <a href="{{ url('/') }}" style="display:inline-block;background:#c8a84b;color:#080808;font-size:14px;font-weight:600;text-decoration:none;padding:14px 36px;border-radius:6px;letter-spacing:.04em">Visit seoaico.com</a>
    </div>

    <hr style="border:none;border-top:1px solid #eee;margin:24px 0">
    <p style="font-size:12px;color:#aaa;margin:0;line-height:1.6">
      You're receiving this because you completed a strategy session at <a href="{{ url('/') }}" style="color:#c8a84b;text-decoration:none">seoaico.com</a>.
    </p>
  </div>

</div>
</body></html>
