<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Your Booking — SEO AI Co™</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body { margin: 0; padding: 0; background: #f5f5f4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #333; }
    .wrap { max-width: 540px; margin: 0 auto; padding: 40px 20px; }
    .logo { text-align: center; margin-bottom: 32px; font-size: 18px; color: #ede8de; }
    .logo .ai { color: #c8a84b; font-weight: 600; font-size: 20px; }
    .card { background: #fff; border-radius: 10px; padding: 32px 28px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
    h2 { font-size: 20px; font-weight: 400; margin: 0 0 6px; }
    .subtitle { font-size: 13px; color: #999; margin: 0 0 24px; }
    .detail-table { width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 24px; }
    .detail-table td { padding: 8px 0; border-bottom: 1px solid #eee; vertical-align: top; }
    .detail-table td:first-child { font-weight: 600; white-space: nowrap; padding-right: 16px; width: 110px; }
    .status-badge { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: 12px; font-weight: 500; text-transform: capitalize; }
    @keyframes status-pulse { 0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,.30); } 60% { box-shadow: 0 0 0 5px rgba(34,197,94,0); } }
    .status-confirmed { background: rgba(34,197,94,.12); color: #22c55e; animation: status-pulse 2.4s ease-in-out infinite; }
    .status-pending   { background: #fef9c3; color: #854d0e; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
    .alert { padding: 12px 16px; border-radius: 6px; font-size: 14px; margin-bottom: 20px; }
    .alert-success { background: #dcfce7; color: #166534; }
    .alert-error   { background: #fee2e2; color: #991b1b; }
    .section-title { font-size: 15px; font-weight: 600; margin: 28px 0 14px; border-top: 1px solid #eee; padding-top: 20px; }
    label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #444; }
    input[type=date], input[type=time], select {
      display: block; width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;
      font-size: 14px; outline: none; background: #fff; margin-bottom: 14px;
    }
    input:focus, select:focus { border-color: #c8a84b; }
    .btn { display: inline-block; padding: 11px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; border: none; }
    .btn-gold { background: #c8a84b; color: #080808; }
    .btn-gold:hover { background: #b89440; }
    .btn-danger { background: transparent; color: #dc2626; border: 1px solid #dc2626; margin-left: 12px; }
    .btn-danger:hover { background: #fee2e2; }
    .actions { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
    .cutoff-notice { font-size: 12px; color: #888; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 14px; margin-top: 14px; }
    footer { text-align: center; font-size: 12px; color: #bbb; margin-top: 32px; }
  </style>
</head>
<body>
<div class="wrap">
  <div class="logo" style="background:#080808;padding:20px;border-radius:8px;margin-bottom:24px">
    SEO<span class="ai">AI</span>co
  </div>

  <div class="card">
    <h2>Manage your booking</h2>
    <p class="subtitle">#{{ $booking->id }} &mdash; {{ $booking->name }}</p>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <table class="detail-table">
      <tr>
        <td>Session</td>
        <td>{{ $booking->consultType->name }}</td>
      </tr>
      <tr>
        <td>Date</td>
        <td>{{ $booking->preferred_date->format('l, F j, Y') }}</td>
      </tr>
      <tr>
        <td>Time</td>
        <td>{{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }} PT</td>
      </tr>
      @if($booking->google_meet_link)
      <tr>
        <td>Meet link</td>
        <td><a href="{{ $booking->google_meet_link }}" style="color:#c8a84b">Join Google Meet</a></td>
      </tr>
      @endif
      <tr>
        <td>Status</td>
        <td>
          <span class="status-badge status-{{ $booking->status }}">{{ $booking->status }}</span>
        </td>
      </tr>
      @if($booking->reschedule_count > 0)
      <tr>
        <td>Rescheduled</td>
        <td>{{ $booking->reschedule_count }}×</td>
      </tr>
      @endif
    </table>

    @if(! $booking->isCancelled())

      @if($canReschedule)
      <div class="section-title">Reschedule</div>
      <form method="POST" action="{{ route('booking.reschedule', $booking->public_booking_token) }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div>
            <label for="preferred_date">New date</label>
            <input type="date" id="preferred_date" name="preferred_date"
              min="{{ now()->addDay()->format('Y-m-d') }}"
              value="{{ old('preferred_date') }}" required>
            @error('preferred_date')<span style="color:#dc2626;font-size:12px">{{ $message }}</span>@enderror
          </div>
          <div>
            <label for="preferred_time">New time</label>
            <input type="time" id="preferred_time" name="preferred_time"
              value="{{ old('preferred_time') }}" required>
            @error('preferred_time')<span style="color:#dc2626;font-size:12px">{{ $message }}</span>@enderror
          </div>
        </div>
        <div class="actions">
          <button type="submit" class="btn btn-gold">Confirm reschedule</button>
        </div>
      </form>
      @else
      <div class="cutoff-notice">
        Reschedules are no longer available within 6 hours of your appointment.
        Please reply to your confirmation email to request a change.
      </div>
      @endif

      <div class="section-title">Cancel</div>
      <form method="POST" action="{{ route('booking.cancel', $booking->public_booking_token) }}"
            onsubmit="return confirm('Are you sure you want to cancel this booking?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Cancel booking</button>
      </form>

    @endif
  </div>

  <footer>seoaico.com &mdash; Questions? Reply to your confirmation email.</footer>
</div>
</body>
</html>
