<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cancel Booking — SEOAIco</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#080808;color:#ede8de;font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 20px}
.box{text-align:center;max-width:480px}
h1{font-size:1.4rem;font-weight:400;margin-bottom:12px}
p{color:#a8a8a0;font-size:.92rem;line-height:1.7;margin-bottom:16px}
.detail{font-size:.88rem;color:#a8a8a0;margin-bottom:6px}
.detail strong{color:#ede8de;font-weight:400}
.cancel-btn{display:inline-block;background:#b84040;color:#fff;padding:14px 32px;font-size:.82rem;font-weight:500;letter-spacing:.1em;text-transform:uppercase;border:none;border-radius:6px;cursor:pointer;margin-top:20px;transition:background .3s}
.cancel-btn:hover{background:#d44}
.home{color:#c8a84b;font-size:.82rem;text-decoration:none;display:inline-block;margin-top:16px}
.msg{background:rgba(200,168,75,.1);border:1px solid rgba(200,168,75,.2);border-radius:6px;padding:12px 16px;color:#c8a84b;font-size:.88rem;margin-bottom:16px;display:none}
</style>
</head>
<body>
<div class="box">
  @if($booking->isCancelled())
    <h1>Booking Already Cancelled</h1>
    <p>This booking was cancelled on {{ $booking->cancelled_at->format('F j, Y') }}.</p>
  @else
    <h1>Cancel Your Booking?</h1>
    <p>{{ $booking->consultType->name }} on {{ $booking->preferred_date->format('l, F j, Y') }} at {{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }} PT</p>
    <div class="detail"><strong>Name:</strong> {{ $booking->name }}</div>
    <div class="detail"><strong>Email:</strong> {{ $booking->email }}</div>
    <div class="msg" id="msg"></div>
    <button class="cancel-btn" id="cancelBtn" onclick="cancelBooking()">Confirm Cancellation</button>
    <script>
    async function cancelBooking() {
      const btn = document.getElementById('cancelBtn');
      btn.disabled = true; btn.textContent = 'Cancelling…';
      try {
        const resp = await fetch('/book/cancel/{{ $booking->id }}', {
          method: 'POST',
          headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','Content-Type':'application/json'}
        });
        const data = await resp.json();
        const msg = document.getElementById('msg');
        msg.style.display = 'block';
        msg.textContent = data.message || 'Booking cancelled.';
        btn.style.display = 'none';
      } catch(e) { btn.disabled = false; btn.textContent = 'Confirm Cancellation'; }
    }
    </script>
  @endif
  <br>
  <a href="/" class="home">&larr; Back to seoaico.com</a>
</div>
</body>
</html>
