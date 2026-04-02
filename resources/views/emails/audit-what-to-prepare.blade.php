<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;border-radius:8px;overflow:hidden">

  {{-- Header --}}
  <div style="background:#080808;padding:32px 28px;text-align:center">
    <span style="font-size:18px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:20px">AI</span>co</span>
  </div>

  {{-- Body --}}
  <div style="padding:32px 28px">
    <h1 style="font-size:22px;font-weight:400;color:#111;margin:0 0 8px">Your Market Opportunity Analysis is coming up</h1>
    <p style="font-size:14px;color:#666;margin:0 0 24px;line-height:1.6">
      To deliver the most precise opportunity mapping in your session, here’s exactly what to have ready. The more context we have, the sharper the analysis.
    </p>

    {{-- Session summary --}}
    <table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:14px;color:#333;margin-bottom:28px">
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
    <div style="text-align:center;margin:0 0 28px">
      <a href="{{ $booking->google_meet_link }}" target="_blank" style="display:inline-block;background:#c8a84b;color:#080808;font-size:14px;font-weight:600;text-decoration:none;padding:14px 36px;border-radius:6px;letter-spacing:.04em">Join Google Meet</a>
    </div>
    @endif

    {{-- Prep checklist --}}
    <div style="background:#f9f9f7;border-radius:6px;padding:20px 24px;margin-bottom:24px">
      <p style="font-size:13px;font-weight:600;color:#333;margin:0 0 16px;text-transform:uppercase;letter-spacing:.06em">What to prepare for your analysis</p>
      <table cellpadding="0" cellspacing="0" style="font-size:13px;color:#555;line-height:1.8;width:100%">
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px;padding-bottom:8px">✓</td>
          <td style="padding-bottom:8px"><strong style="color:#333">Your website URL</strong> — we'll pull live data before the session</td>
        </tr>
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px;padding-bottom:8px">✓</td>
          <td style="padding-bottom:8px"><strong style="color:#333">Your top 2–3 competitors</strong> — who dominates search results in your space?</td>
        </tr>
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px;padding-bottom:8px">✓</td>
          <td style="padding-bottom:8px"><strong style="color:#333">Your primary market / geography</strong> — city, region, or national?</td>
        </tr>
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px;padding-bottom:8px">✓</td>
          <td style="padding-bottom:8px"><strong style="color:#333">Your #1 growth blocker</strong> — what's the single thing holding you back right now?</td>
        </tr>
        <tr>
          <td style="padding-right:10px;vertical-align:top;color:#c8a84b;font-size:16px">✓</td>
          <td><strong style="color:#333">Access to Google Search Console</strong> — if available (not required, but useful)</td>
        </tr>
      </table>
    </div>

    {{-- What you'll walk away with --}}
    <div style="border-left:3px solid #c8a84b;padding:16px 20px;margin-bottom:24px;background:#fffef9">
      <p style="font-size:13px;font-weight:600;color:#333;margin:0 0 10px">What you'll walk away with</p>
      <p style="font-size:13px;color:#555;line-height:1.7;margin:0">
        A clear picture of where you stand in your market, which revenue opportunities are currently invisible to you, and a realistic outline of where growth is achievable — delivered during the analysis, not weeks later.
      </p>
    </div>

    <p style="font-size:13px;color:#888;line-height:1.6;margin:0 0 20px">
      Questions before the analysis? Reply to this email and we’ll get back to you within one business day.
    </p>

    <hr style="border:none;border-top:1px solid #eee;margin:24px 0">
    <p style="font-size:12px;color:#aaa;margin:0;line-height:1.6">
      You're receiving this because you booked a Market Opportunity Audit at <a href="{{ url('/') }}" style="color:#c8a84b;text-decoration:none">seoaico.com</a>.<br>
      This is a transactional email related to your booking.
    </p>
  </div>

</div>
</body></html>
