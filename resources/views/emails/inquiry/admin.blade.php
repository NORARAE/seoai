<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>New Licensing Inquiry</title>
<style>
  body{margin:0;padding:0;background:#f5f5f5;font-family:Arial,sans-serif;color:#1a1a1a}
  .wrap{max-width:600px;margin:40px auto;background:#fff;border:1px solid #e0e0e0;border-radius:4px;overflow:hidden}
  .header{background:#0a0a0a;padding:24px 32px;display:flex;align-items:center;gap:12px}
  .header-title{color:#fff;font-size:1rem;font-weight:600;letter-spacing:.04em}
  .badge{background:#c8a84b;color:#000;font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;padding:3px 10px;border-radius:2px}
  .body{padding:32px}
  h2{margin:0 0 20px;font-size:1.1rem;font-weight:600;color:#111}
  table{width:100%;border-collapse:collapse;margin-bottom:24px}
  td{padding:10px 12px;font-size:.88rem;border-bottom:1px solid #f0f0f0;vertical-align:top}
  td:first-child{width:36%;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.08em;font-size:.75rem;white-space:nowrap}
  td:last-child{color:#222}
  .message-block{background:#fafafa;border:1px solid #e8e8e8;border-radius:3px;padding:16px;font-size:.88rem;line-height:1.65;color:#333;white-space:pre-wrap}
  .meta{margin-top:20px;font-size:.78rem;color:#aaa}
  .footer{padding:16px 32px;border-top:1px solid #f0f0f0;font-size:.78rem;color:#aaa;text-align:center}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <span class="header-title">SEOAIco — New Inquiry</span>
    <span class="badge">{{ strtoupper($inquiry->tier) }}</span>
  </div>
  <div class="body">
    <h2>{{ $inquiry->company }} — {{ $inquiry->typeLabel() }}</h2>

    <table>
      <tr><td>Name</td><td>{{ $inquiry->name }}</td></tr>
      <tr><td>Email</td><td><a href="mailto:{{ $inquiry->email }}" style="color:#c8a84b">{{ $inquiry->email }}</a></td></tr>
      <tr><td>Company</td><td>{{ $inquiry->company }}</td></tr>
      @if($inquiry->website)
      <tr><td>Website</td><td><a href="{{ $inquiry->website }}" style="color:#c8a84b" target="_blank">{{ $inquiry->website }}</a></td></tr>
      @endif
      <tr><td>Tier</td><td>{{ $inquiry->tierLabel() }}</td></tr>
      <tr><td>Type</td><td>{{ $inquiry->typeLabel() }}</td></tr>
      @if($inquiry->niche)
      <tr><td>Niche</td><td>{{ $inquiry->niche }}</td></tr>
      @endif
      <tr><td>IP Address</td><td>{{ $inquiry->ip_address }}</td></tr>
      <tr><td>Submitted</td><td>{{ $inquiry->created_at->setTimezone('America/Los_Angeles')->format('M j, Y g:i A T') }}</td></tr>
    </table>

    <strong style="font-size:.8rem;text-transform:uppercase;letter-spacing:.1em;color:#888">Message</strong>
    <div class="message-block" style="margin-top:8px">{{ $inquiry->message }}</div>

    <div class="meta">Inquiry ID #{{ $inquiry->id }} &nbsp;·&nbsp; {{ $inquiry->ip_address }}</div>
  </div>
  <div class="footer">SEOAIco internal notification &nbsp;·&nbsp; Do not reply to this address</div>
</div>
</body>
</html>
