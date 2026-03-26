@extends('admin.bookings.layout')
@section('title', 'Consult Types')
@section('subtitle', 'All bookable session types')

@section('content')
<div class="card">
  <table>
    <thead>
      <tr>
        <th>Order</th>
        <th>Name</th>
        <th>Duration</th>
        <th>Price</th>
        <th>Active</th>
        <th>Bookings</th>
      </tr>
    </thead>
    <tbody>
      @foreach($types as $t)
      <tr>
        <td>{{ $t->sort_order }}</td>
        <td><strong>{{ $t->name }}</strong><br><span style="color:#888;font-size:.8rem">{{ $t->slug }}</span></td>
        <td>{{ $t->duration_minutes }} min</td>
        <td>{{ $t->formattedPrice() }}</td>
        <td>{!! $t->is_active ? '<span style="color:#166534">&#10003;</span>' : '<span style="color:#999">—</span>' !!}</td>
        <td>{{ $t->bookings()->count() }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<p style="font-size:.82rem;color:#999;margin-top:12px">To edit consult types, use the database seeder or update records directly.</p>
@endsection
