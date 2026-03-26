@extends('admin.bookings.layout')
@section('title', 'Availability')
@section('subtitle', 'Set which days and hours are bookable')

@section('content')
<form method="POST" action="{{ route('admin.bookings.availability.save') }}">
  @csrf
  <div class="card">
    <table>
      <thead>
        <tr>
          <th>Day</th>
          <th>Start</th>
          <th>End</th>
          <th>Active</th>
        </tr>
      </thead>
      <tbody>
        @php $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; @endphp
        @for($d = 0; $d < 7; $d++)
          @php $slot = $slots->firstWhere('day_of_week', $d); @endphp
          <tr>
            <td>
              <strong>{{ $days[$d] }}</strong>
              <input type="hidden" name="slots[{{ $d }}][day_of_week]" value="{{ $d }}">
            </td>
            <td><input type="time" name="slots[{{ $d }}][start_time]" value="{{ $slot?->start_time ? \Carbon\Carbon::parse($slot->start_time)->format('H:i') : '09:00' }}" style="padding:6px 10px;border:1px solid #ddd;border-radius:4px"></td>
            <td><input type="time" name="slots[{{ $d }}][end_time]" value="{{ $slot?->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('H:i') : '17:00' }}" style="padding:6px 10px;border:1px solid #ddd;border-radius:4px"></td>
            <td><input type="checkbox" name="slots[{{ $d }}][is_active]" value="1" {{ $slot?->is_active ? 'checked' : '' }}></td>
          </tr>
        @endfor
      </tbody>
    </table>
  </div>
  <button type="submit" class="btn btn-primary" style="margin-top:12px">Save Availability</button>
</form>
@endsection
