<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;border-radius:8px;overflow:hidden">
  <div style="background:#080808;padding:32px 28px;text-align:center">
    <span style="font-size:18px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:20px">AI</span>co</span>
  </div>
  <div style="padding:32px 28px">
    <h1 style="font-size:22px;font-weight:400;color:#111;margin:0 0 8px">New Booking</h1>
    <p style="font-size:14px;color:#666;margin:0 0 24px;line-height:1.6">A new consult has been booked on seoaico.com.</p>

    <table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:14px;color:#333;margin-bottom:24px">
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Session</td>
        <td>{{ $booking->consultType->name }}</td>
      </tr>
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Status</td>
        <td><strong style="color:{{ $booking->isConfirmed() ? '#22863a' : '#e36209' }}">{{ strtoupper($booking->status) }}</strong></td>
      </tr>
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Date</td>
        <td>{{ $booking->preferred_date->format('l, F j, Y') }}</td>
      </tr>
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Time</td>
        <td>{{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }} PT</td>
      </tr>
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Duration</td>
        <td>{{ $booking->consultType->duration_minutes }} minutes</td>
      </tr>
    </table>

    <h2 style="font-size:16px;font-weight:600;color:#111;margin:0 0 12px">Client Details</h2>
    <table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:14px;color:#333;margin-bottom:24px">
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Name</td>
        <td>{{ $booking->name }}</td>
      </tr>
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Email</td>
        <td><a href="mailto:{{ $booking->email }}" style="color:#c8a84b">{{ $booking->email }}</a></td>
      </tr>
      @if($booking->phone)
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Phone</td>
        <td>{{ $booking->phone }}</td>
      </tr>
      @endif
      @if($booking->company)
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Company</td>
        <td>{{ $booking->company }}</td>
      </tr>
      @endif
      @if($booking->website)
      <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Website</td>
        <td><a href="{{ $booking->website }}" style="color:#c8a84b">{{ $booking->website }}</a></td>
      </tr>
      @endif
      @if($booking->message)
      <tr>
        <td style="font-weight:600;white-space:nowrap;padding-right:16px;vertical-align:top">Message</td>
        <td style="white-space:pre-line">{{ $booking->message }}</td>
      </tr>
      @endif
    </table>

    @if($booking->google_meet_link)
    <p style="font-size:14px"><a href="{{ $booking->google_meet_link }}" style="color:#c8a84b;font-weight:600">Open Google Meet Link &rarr;</a></p>
    @endif

    @if($booking->isPending())
    <div style="background:#fff8e1;border:1px solid #ffd54f;border-radius:6px;padding:12px 16px;margin-top:16px">
      <p style="font-size:13px;color:#7c6a00;margin:0"><strong>Needs manual review.</strong> Google Calendar event could not be created automatically. Please follow up with the client.</p>
    </div>
    @endif

    <hr style="border:none;border-top:1px solid #eee;margin:24px 0">
    <p style="font-size:12px;color:#999;margin:0">
      Booking ID #{{ $booking->id }} &middot; {{ $booking->created_at->toDateTimeString() }} UTC
    </p>
  </div>
</div>
</body></html>
