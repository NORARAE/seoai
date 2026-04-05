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
    <h1 style="font-size:22px;font-weight:400;color:#111;margin:0 0 8px">Your session is tomorrow. Here's how to arrive ready.</h1>
    <p style="font-size:14px;color:#666;margin:0 0 24px;line-height:1.6">
      The operators who get the most from this session come in with specific context. Here is what matters.
    </p>

    {{-- Session summary --}}
    <table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:14px;color:#333;margin-bottom:24px">
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Session</td>
        <td>{{ $booking->consultType->name }}</td>
      </tr>
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Date</td>
        <td>{{ $booking->preferred_date->format('l, F j, Y') }}</td>
      </tr>
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Time</td>
        <td>{{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }} PT</td>
      </tr>
      <tr>
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Duration</td>
        <td>{{ $booking->consultType->duration_minutes }} minutes</td>
      </tr>
    </table>

    @if($booking->google_meet_link)
    <div style="text-align:center;margin:24px 0">
      <a href="{{ $booking->google_meet_link }}" target="_blank" style="display:inline-block;background:#c8a84b;color:#080808;font-size:14px;font-weight:600;text-decoration:none;padding:14px 36px;border-radius:6px;letter-spacing:.04em">Join Google Meet</a>
    </div>
    @endif

    {{-- Prep checklist --}}
    <div style="background:#f9f9f7;border-radius:6px;padding:20px 24px;margin:24px 0">
      <p style="font-size:13px;font-weight:600;color:#333;margin:0 0 12px;text-transform:uppercase;letter-spacing:.06em">Before we meet</p>
      <table cellpadding="0" cellspacing="0" style="font-size:13px;color:#555;line-height:1.7">
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px">→</td>
          <td>Your primary market — the specific geography where you want to hold position</td>
        </tr>
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px">→</td>
          <td>Your top 2–3 services, ranked by revenue margin — not just what you offer</td>
        </tr>
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px">→</td>
          <td>Your website URL and a rough sense of your current search visibility</td>
        </tr>
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px">→</td>
          <td>The one growth obstacle you would fix today if resources were not the constraint</td>
        </tr>
      </table>
    </div>

    <p style="font-size:13px;color:#888;line-height:1.6;margin:0">
      Questions before the call? Reply to this email — we'll get back to you same day.
    </p>

    <hr style="border:none;border-top:1px solid #eee;margin:24px 0">
    <p style="font-size:12px;color:#aaa;margin:0;line-height:1.6">
      You're receiving this because you booked a strategy session at <a href="{{ url('/') }}" style="color:#c8a84b;text-decoration:none">seoaico.com</a>.
    </p>
  </div>

</div>
</body></html>
