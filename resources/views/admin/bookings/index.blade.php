@extends('admin.bookings.layout')
@section('title', 'All Bookings')
@section('subtitle', $bookings->total() . ' total bookings')

@section('content')
<div class="filters">
  <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap">
    <select name="status" onchange="this.form.submit()">
      <option value="">All Statuses</option>
      @foreach(['pending','confirmed','cancelled','completed'] as $s)
        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <select name="type" onchange="this.form.submit()">
      <option value="">All Types</option>
      @foreach($types as $t)
        <option value="{{ $t->id }}" @selected(request('type') == $t->id)>{{ $t->name }}</option>
      @endforeach
    </select>
    @if(request('status') || request('type'))
      <a href="{{ route('admin.bookings.index') }}" class="btn btn-ghost btn-sm">Clear</a>
    @endif
  </form>
</div>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Client</th>
        <th>Type</th>
        <th>Date / Time</th>
        <th>Status</th>
        <th>Meet</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($bookings as $b)
      <tr>
        <td>{{ $b->id }}</td>
        <td>
          <strong>{{ $b->name }}</strong><br>
          <span style="color:#888;font-size:.8rem">{{ $b->email }}</span>
        </td>
        <td>{{ $b->consultType->name }}</td>
        <td>{{ $b->preferred_date->format('M j, Y') }}<br><span style="color:#888;font-size:.8rem">{{ \Carbon\Carbon::parse($b->preferred_time)->format('g:i A') }}</span></td>
        <td><span class="badge badge-{{ $b->status }}">{{ $b->status }}</span></td>
        <td>
          @if($b->google_meet_link)
            <a href="{{ $b->google_meet_link }}" target="_blank" style="color:#c8a84b;font-size:.82rem">Join</a>
          @else
            <span style="color:#ccc">—</span>
          @endif
        </td>
        <td><a href="{{ route('admin.bookings.show', $b) }}" class="btn btn-ghost btn-sm">View</a></td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:#999;padding:32px">No bookings found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($bookings->hasPages())
<div class="pagination">
  {{ $bookings->appends(request()->query())->links('pagination::simple-bootstrap-4') }}
</div>
@endif
@endsection
