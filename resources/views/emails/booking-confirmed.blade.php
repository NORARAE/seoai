<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;border-radius:8px;overflow:hidden">
  <div style="background:#080808;padding:32px 28px;text-align:center">
    <span style="font-size:18px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:20px">AI</span> Co™</span>
  </div>
  <div style="padding:32px 28px">
    <h1 style="font-size:22px;font-weight:400;color:#111;margin:0 0 8px">You're booked. Your spot is locked in.</h1>
    <p style="font-size:14px;color:#666;margin:0 0 24px;line-height:1.6">Most operators in your market haven't made this call yet. That gap is exactly the point.</p>

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

    <p style="font-size:13px;color:#888;line-height:1.6;margin-top:24px">
      <strong style="color:#555">Walk in ready:</strong><br>
      Know your primary geography, top 2–3 service categories, and the single biggest growth obstacle you're facing right now. That context makes the session sharp.
    </p>

    <hr style="border:none;border-top:1px solid #eee;margin:24px 0">
    @if($manageUrl)
    <p style="font-size:13px;color:#666;margin:0 0 12px;line-height:1.6">
      Need to reschedule or cancel? Use your self-service booking link:
    </p>
    <div style="text-align:center;margin:0 0 20px">
      <a href="{{ $manageUrl }}" style="display:inline-block;background:#f5f5f4;color:#333;font-size:13px;font-weight:500;text-decoration:none;padding:10px 24px;border-radius:6px;border:1px solid #e0e0e0">Manage my booking</a>
    </div>
    @else
    <p style="font-size:12px;color:#999;margin:0">
      Need to reschedule or cancel? Reply to this email or visit <a href="{{ url('/') }}" style="color:#c8a84b">seoaico.com</a>.
    </p>
    @endif
  </div>
</div>
</body></html>
