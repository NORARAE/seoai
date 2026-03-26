@extends('admin.bookings.layout')
@section('title', 'Booking #' . $booking->id)
@section('subtitle', $booking->consultType->name . ' — ' . $booking->name)

@section('content')
<div class="card" style="padding:24px">
  <div class="detail-grid">
    <div class="detail-label">Status</div>
    <div class="detail-value"><span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span></div>

    <div class="detail-label">Consult Type</div>
    <div class="detail-value">{{ $booking->consultType->name }} ({{ $booking->consultType->duration_minutes }} min)</div>

    <div class="detail-label">Date</div>
    <div class="detail-value">{{ $booking->preferred_date->format('l, F j, Y') }}</div>

    <div class="detail-label">Time</div>
    <div class="detail-value">{{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }} PT</div>

    <div class="detail-label">Client Name</div>
    <div class="detail-value">{{ $booking->name }}</div>

    <div class="detail-label">Email</div>
    <div class="detail-value"><a href="mailto:{{ $booking->email }}" style="color:#c8a84b">{{ $booking->email }}</a></div>

    @if($booking->phone)
    <div class="detail-label">Phone</div>
    <div class="detail-value">{{ $booking->phone }}</div>
    @endif

    @if($booking->company)
    <div class="detail-label">Company</div>
    <div class="detail-value">{{ $booking->company }}</div>
    @endif

    @if($booking->website)
    <div class="detail-label">Website</div>
    <div class="detail-value"><a href="{{ $booking->website }}" target="_blank" style="color:#c8a84b">{{ $booking->website }}</a></div>
    @endif

    @if($booking->message)
    <div class="detail-label">Message</div>
    <div class="detail-value" style="white-space:pre-line">{{ $booking->message }}</div>
    @endif

    @if($booking->google_meet_link)
    <div class="detail-label">Google Meet</div>
    <div class="detail-value"><a href="{{ $booking->google_meet_link }}" target="_blank" class="btn btn-primary btn-sm">Join Meeting</a></div>
    @endif

    <div class="detail-label">Booked At</div>
    <div class="detail-value">{{ $booking->created_at->format('M j, Y g:i A') }}</div>

    @if($booking->confirmed_at)
    <div class="detail-label">Confirmed At</div>
    <div class="detail-value">{{ $booking->confirmed_at->format('M j, Y g:i A') }}</div>
    @endif

    @if($booking->cancelled_at)
    <div class="detail-label">Cancelled At</div>
    <div class="detail-value">{{ $booking->cancelled_at->format('M j, Y g:i A') }}</div>
    @endif
  </div>

  <div style="display:flex;gap:8px;margin-top:20px">
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-ghost">&larr; Back</a>
    @if(!$booking->isCancelled())
    <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" onsubmit="return confirm('Cancel this booking?')">
      @csrf
      <button type="submit" class="btn btn-danger">Cancel Booking</button>
    </form>
    @endif
  </div>
</div>
@endsection
