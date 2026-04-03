<p>Hello {{ $license->customer_name ?: 'there' }},</p>

<p>Your SEO AI Co™ Core Content Engine license is ready.</p>

<p><strong>License key:</strong> {{ $license->license_key }}</p>
<p><strong>Plan:</strong> {{ ucfirst($license->plan) }}</p>
<p><strong>Site:</strong> {{ $license->site_url }}</p>
@if (! is_null($license->urls_allowed))
<p><strong>URLs allowed:</strong> {{ number_format($license->urls_allowed) }}</p>
@endif
@if ($license->expires_at)
<p><strong>Renews / expires:</strong> {{ $license->expires_at->toDateString() }}</p>
@elseif ($license->trial_ends_at)
<p><strong>Trial ends:</strong> {{ $license->trial_ends_at->toDateString() }}</p>
@endif

<p>Enter this key into the Core Content Engine plugin license screen on your WordPress site to unlock the full rendering engine.</p>

<p>If you need help, reply to this email or contact hello@seoaico.com.</p>