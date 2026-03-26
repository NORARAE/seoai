<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>New Licensing Inquiry</title>
<style>
  body{margin:0;padding:0;background:#f2f2f2;font-family:Arial,sans-serif;color:#1a1a1a}
  .wrap{max-width:640px;margin:40px auto;background:#fff;border:1px solid #e0e0e0;border-radius:4px;overflow:hidden}
  .header{background:#0a0a0a;padding:24px 32px;display:flex;align-items:center;gap:12px;flex-wrap:wrap}
  .header-title{color:#fff;font-size:1rem;font-weight:600;letter-spacing:.04em}
  .badge{background:#c8a84b;color:#000;font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;padding:3px 10px;border-radius:2px}
  .risk-high{background:#e83c3c;color:#fff}
  .risk-medium{background:#e8941a;color:#fff}
  .risk-low{background:#2d9e5f;color:#fff}
  .section-title{margin:0 0 8px;padding:12px 0 8px;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:#888;border-bottom:2px solid #f0f0f0}
  .body{padding:28px 32px}
  table{width:100%;border-collapse:collapse;margin-bottom:20px}
  td{padding:9px 10px;font-size:.87rem;border-bottom:1px solid #f4f4f4;vertical-align:top}
  td:first-child{width:38%;color:#999;font-weight:600;text-transform:uppercase;letter-spacing:.08em;font-size:.74rem;white-space:nowrap}
  td:last-child{color:#222}
  .message-block{background:#fafafa;border:1px solid #ebebeb;border-radius:3px;padding:14px;font-size:.87rem;line-height:1.65;color:#333;white-space:pre-wrap;margin-bottom:24px}
  .flag-yes{color:#e83c3c;font-weight:700}
  .flag-no{color:#2d9e5f}
  .meta{margin-top:16px;font-size:.76rem;color:#bbb;border-top:1px solid #f0f0f0;padding-top:14px}
  .footer{padding:14px 32px;border-top:1px solid #f0f0f0;font-size:.76rem;color:#bbb;text-align:center}
  .logo-cell{max-width:48px;max-height:48px}
  .logo-cell img{max-width:48px;max-height:48px;object-fit:contain;border-radius:4px}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <span class="header-title">SEOAIco — New Inquiry</span>
    <span class="badge">{{ strtoupper($inquiry->tier) }}</span>
    @php
      $riskClass = match($inquiry->spam_risk ?? 'low') {
          'high'   => 'badge risk-high',
          'medium' => 'badge risk-medium',
          default  => 'badge risk-low',
      };
    @endphp
    <span class="{{ $riskClass }}">Risk: {{ strtoupper($inquiry->spam_risk ?? 'LOW') }}</span>
  </div>

  <div class="body">

    {{-- ── CONTACT INFO ─────────────────────────────────────── --}}
    <p class="section-title">Contact Info</p>
    <table>
      <tr><td>Name</td><td>{{ $inquiry->name }}</td></tr>
      <tr>
        <td>Email</td>
        <td>
          <a href="mailto:{{ $inquiry->email }}" style="color:#c8a84b">{{ $inquiry->email }}</a>
          @if($inquiry->email_type)
            <span style="margin-left:8px;font-size:.76rem;color:#888;text-transform:uppercase">({{ $inquiry->email_type }})</span>
          @endif
        </td>
      </tr>
      <tr><td>Company</td><td>{{ $inquiry->company }}</td></tr>
      @if($inquiry->website)
      <tr><td>Website</td><td><a href="{{ $inquiry->website }}" style="color:#c8a84b" target="_blank">{{ $inquiry->website }}</a></td></tr>
      @endif
      <tr><td>Tier</td><td>{{ $inquiry->tierLabel() }}</td></tr>
      <tr><td>Type</td><td>{{ $inquiry->typeLabel() }}</td></tr>
      @if($inquiry->niche)
      <tr><td>Niche</td><td>{{ $inquiry->niche }}</td></tr>
      @endif
    </table>

    {{-- ── LOCATION & IP ────────────────────────────────────── --}}
    <p class="section-title">Location &amp; IP</p>
    <table>
      <tr>
        <td>Location</td>
        <td>
          @php
            $location = array_filter([$inquiry->ip_city, $inquiry->ip_region, $inquiry->ip_country]);
          @endphp
          {{ count($location) ? implode(', ', $location) : '—' }}
        </td>
      </tr>
      <tr><td>IP Address</td><td>{{ $inquiry->ip_address ?? '—' }}</td></tr>
      <tr><td>ISP / Org</td><td>{{ $inquiry->ip_isp ?? '—' }}</td></tr>
      <tr>
        <td>VPN / Proxy</td>
        <td>
          @if($inquiry->ip_is_proxy)
            <span class="flag-yes">&#9888; YES</span>
          @else
            <span class="flag-no">Clean</span>
          @endif
        </td>
      </tr>
      <tr>
        <td>Hosting / DC</td>
        <td>
          @if($inquiry->ip_is_hosting)
            <span class="flag-yes">&#9888; YES</span>
          @else
            <span class="flag-no">Clean</span>
          @endif
        </td>
      </tr>
    </table>

    {{-- ── WEBSITE CHECK ────────────────────────────────────── --}}
    @if($inquiry->website)
    <p class="section-title">Website Check</p>
    <table>
      <tr><td>URL</td><td><a href="{{ $inquiry->website }}" style="color:#c8a84b" target="_blank">{{ $inquiry->website }}</a></td></tr>
      <tr>
        <td>Status</td>
        <td>
          @php
            $statusColor = match($inquiry->url_status ?? '') {
                'valid'        => '#2d9e5f',
                'redirect'     => '#e8941a',
                'parked', 'suspicious', 'unresolvable' => '#e83c3c',
                default        => '#888',
            };
          @endphp
          <span style="color:{{ $statusColor }};font-weight:600;text-transform:uppercase">
            {{ $inquiry->url_status ?? '—' }}
          </span>
        </td>
      </tr>
      <tr><td>HTTPS</td><td>{{ $inquiry->url_is_https ? '✓ Yes' : '✗ No' }}</td></tr>
      <tr>
        <td>Domain Age</td>
        <td>
          @if($inquiry->domain_age_days !== null)
            {{ number_format($inquiry->domain_age_days / 365.25, 1) }} yrs ({{ $inquiry->domain_age_days }} days)
          @else
            —
          @endif
        </td>
      </tr>
    </table>
    @endif

    {{-- ── COMPANY INFO ─────────────────────────────────────── --}}
    @if($inquiry->company_enrichment)
    @php $co = $inquiry->company_enrichment; @endphp
    <p class="section-title">Company Info</p>
    <table>
      @if(!empty($co['logo']))
      <tr><td>Logo</td><td class="logo-cell"><img src="{{ $co['logo'] }}" alt="Logo"></td></tr>
      @endif
      @if(!empty($co['name']))
      <tr><td>Legal Name</td><td>{{ $co['name'] }}</td></tr>
      @endif
      @if(!empty($co['industry']))
      <tr><td>Industry</td><td>{{ $co['industry'] }}{{ !empty($co['sub_industry']) ? ' / ' . $co['sub_industry'] : '' }}</td></tr>
      @endif
      @if(!empty($co['employees']))
      <tr><td>Employees</td><td>{{ number_format($co['employees']) }}</td></tr>
      @endif
      @if(!empty($co['founded']))
      <tr><td>Founded</td><td>{{ $co['founded'] }}</td></tr>
      @endif
      @if(!empty($co['location']))
      <tr><td>HQ</td><td>{{ $co['location'] }}</td></tr>
      @endif
      @if(!empty($co['linkedin']))
      <tr><td>LinkedIn</td><td><a href="https://linkedin.com/company/{{ $co['linkedin'] }}" style="color:#c8a84b" target="_blank">{{ $co['linkedin'] }}</a></td></tr>
      @endif
    </table>
    @endif

    {{-- ── SECURITY FLAGS ───────────────────────────────────── --}}
    <p class="section-title">Security Flags</p>
    <table>
      <tr>
        <td>Spam Risk</td>
        <td>
          @php
            $riskStyle = match($inquiry->spam_risk ?? 'low') {
                'high'   => 'color:#e83c3c;font-weight:700',
                'medium' => 'color:#e8941a;font-weight:700',
                default  => 'color:#2d9e5f;font-weight:700',
            };
          @endphp
          <span style="{{ $riskStyle }}">{{ strtoupper($inquiry->spam_risk ?? 'LOW') }}</span>
        </td>
      </tr>
      <tr>
        <td>reCAPTCHA Score</td>
        <td>
          @if($inquiry->recaptcha_score !== null)
            @php
              $scoreColor = $inquiry->recaptcha_score >= 0.7 ? '#2d9e5f' :
                           ($inquiry->recaptcha_score >= 0.5 ? '#e8941a' : '#e83c3c');
            @endphp
            <span style="color:{{ $scoreColor }};font-weight:600">{{ number_format($inquiry->recaptcha_score, 2) }}</span>
            <span style="color:#aaa;font-size:.8rem"> / 1.0</span>
          @else
            <span style="color:#bbb">Not checked</span>
          @endif
        </td>
      </tr>
      <tr>
        <td>Time to Submit</td>
        <td>
          @if($inquiry->time_to_submit_seconds !== null)
            @php $ttsColor = $inquiry->time_to_submit_seconds < 4 ? '#e83c3c' : '#2d9e5f'; @endphp
            <span style="color:{{ $ttsColor }}">{{ $inquiry->time_to_submit_seconds }}s</span>
            @if($inquiry->time_to_submit_seconds < 4)
              <span style="color:#e83c3c;font-weight:600"> &#9873; Too fast</span>
            @endif
          @else
            —
          @endif
        </td>
      </tr>
      <tr>
        <td>Honeypot</td>
        <td>
          @if($inquiry->honeypot_triggered)
            <span class="flag-yes">&#9888; TRIGGERED</span>
          @else
            <span class="flag-no">Clean</span>
          @endif
        </td>
      </tr>
    </table>

    {{-- ── MESSAGE ─────────────────────────────────────────── --}}
    <strong style="font-size:.78rem;text-transform:uppercase;letter-spacing:.1em;color:#888">Message</strong>
    <div class="message-block" style="margin-top:8px">{{ $inquiry->message }}</div>

    <div class="meta">
      Inquiry #{{ $inquiry->id }}
      &nbsp;·&nbsp; {{ $inquiry->created_at->setTimezone('America/Los_Angeles')->format('M j, Y g:i A T') }}
      @if($inquiry->status === 'rejected')
        &nbsp;·&nbsp; <span style="color:#e83c3c;font-weight:700">REJECTED (silent)</span>
      @endif
    </div>
  </div>
  <div class="footer">SEOAIco internal notification &nbsp;·&nbsp; Do not reply to this address</div>
</div>
</body>
</html>
