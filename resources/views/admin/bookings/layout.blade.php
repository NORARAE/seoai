<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Bookings') — SEOAIco Admin</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f5f5f4;color:#222;line-height:1.5}
.wrap{max-width:1100px;margin:0 auto;padding:24px 20px}
h1{font-size:1.5rem;font-weight:600;margin-bottom:4px}
.sub{color:#666;font-size:.88rem;margin-bottom:24px}
.nav-tabs{display:flex;gap:4px;margin-bottom:24px;border-bottom:1px solid #e0e0e0;padding-bottom:0}
.nav-tab{padding:10px 16px;font-size:.84rem;color:#666;text-decoration:none;border-bottom:2px solid transparent;transition:all .2s}
.nav-tab:hover{color:#111}
.nav-tab.active{color:#111;border-bottom-color:#c8a84b;font-weight:500}
.card{background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;margin-bottom:20px}
.card-head{padding:16px 20px;border-bottom:1px solid #f0f0f0;font-weight:600;font-size:.92rem}
table{width:100%;border-collapse:collapse}
th{text-align:left;font-size:.72rem;letter-spacing:.06em;text-transform:uppercase;color:#999;padding:10px 16px;border-bottom:1px solid #eee}
td{padding:12px 16px;border-bottom:1px solid #f5f5f4;font-size:.88rem;vertical-align:top}
tr:last-child td{border-bottom:none}
tr:hover{background:#fafaf9}
.badge{display:inline-block;font-size:.7rem;font-weight:600;letter-spacing:.04em;text-transform:uppercase;padding:3px 10px;border-radius:99px}
.badge-confirmed{background:#dcfce7;color:#166534}
.badge-pending{background:#fef3c7;color:#92400e}
.badge-cancelled{background:#fee2e2;color:#991b1b}
.badge-completed{background:#e0e7ff;color:#3730a3}
.btn{display:inline-block;padding:8px 16px;font-size:.82rem;border-radius:6px;text-decoration:none;font-weight:500;cursor:pointer;border:none;transition:all .2s}
.btn-sm{padding:5px 12px;font-size:.76rem}
.btn-primary{background:#c8a84b;color:#fff}
.btn-primary:hover{background:#b8963f}
.btn-danger{background:#dc2626;color:#fff}
.btn-danger:hover{background:#b91c1c}
.btn-ghost{background:transparent;border:1px solid #ddd;color:#555}
.btn-ghost:hover{border-color:#999;color:#111}
.filters{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.filters select{padding:8px 12px;border:1px solid #ddd;border-radius:6px;font-size:.84rem;background:#fff}
.success{background:#dcfce7;border:1px solid #86efac;border-radius:6px;padding:12px 16px;color:#166534;font-size:.88rem;margin-bottom:16px}
.detail-grid{display:grid;grid-template-columns:160px 1fr;gap:8px 16px;margin-bottom:20px}
.detail-label{font-size:.76rem;text-transform:uppercase;letter-spacing:.06em;color:#999;font-weight:600}
.detail-value{font-size:.92rem;color:#222}
.pagination{display:flex;gap:4px;margin-top:16px}
.pagination a,.pagination span{padding:6px 12px;border:1px solid #ddd;border-radius:4px;font-size:.82rem;text-decoration:none;color:#555}
.pagination a:hover{background:#f0f0f0}
.pagination .active span{background:#c8a84b;color:#fff;border-color:#c8a84b}
@media(max-width:640px){
  .filters{flex-direction:column}
  table{font-size:.8rem}
  th,td{padding:8px 10px}
  .detail-grid{grid-template-columns:1fr}
}
</style>
</head>
<body>
<div class="wrap">
  <h1>@yield('title', 'Bookings')</h1>
  <p class="sub">@yield('subtitle', 'Manage consult bookings')</p>
  <nav class="nav-tabs">
    <a href="{{ route('admin.bookings.index') }}" class="nav-tab {{ request()->routeIs('admin.bookings.index') ? 'active' : '' }}">All Bookings</a>
    <a href="{{ route('admin.bookings.availability') }}" class="nav-tab {{ request()->routeIs('admin.bookings.availability*') ? 'active' : '' }}">Availability</a>
    <a href="{{ route('admin.bookings.consultTypes') }}" class="nav-tab {{ request()->routeIs('admin.bookings.consultTypes') ? 'active' : '' }}">Consult Types</a>
  </nav>

  @if(session('success'))
    <div class="success">{{ session('success') }}</div>
  @endif

  @yield('content')
</div>
</body>
</html>
