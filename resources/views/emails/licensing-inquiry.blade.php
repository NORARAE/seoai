<p><strong>New licensing enquiry from {{ $inquiry['name'] }}</strong></p>

<table cellpadding="8" cellspacing="0" style="border-collapse:collapse;font-family:sans-serif;font-size:14px;color:#333">
    <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;vertical-align:top;padding-right:24px;white-space:nowrap">Name</td>
        <td>{{ $inquiry['name'] }}</td>
    </tr>
    <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;vertical-align:top;padding-right:24px;white-space:nowrap">Company</td>
        <td>{{ $inquiry['company'] }}</td>
    </tr>
    <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;vertical-align:top;padding-right:24px;white-space:nowrap">Email</td>
        <td><a href="mailto:{{ $inquiry['email'] }}">{{ $inquiry['email'] }}</a></td>
    </tr>
    @if (! empty($inquiry['website']))
    <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;vertical-align:top;padding-right:24px;white-space:nowrap">Website</td>
        <td>{{ $inquiry['website'] }}</td>
    </tr>
    @endif
    <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;vertical-align:top;padding-right:24px;white-space:nowrap">Type</td>
        <td>{{ $inquiry['type'] }}</td>
    </tr>
    <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;vertical-align:top;padding-right:24px;white-space:nowrap">Tier Interest</td>
        <td>{{ $inquiry['tier'] }}</td>
    </tr>
    @if (! empty($inquiry['niche']))
    <tr style="border-bottom:1px solid #eee">
        <td style="font-weight:600;vertical-align:top;padding-right:24px;white-space:nowrap">Niche / Market</td>
        <td>{{ $inquiry['niche'] }}</td>
    </tr>
    @endif
    <tr>
        <td style="font-weight:600;vertical-align:top;padding-right:24px;white-space:nowrap">Message</td>
        <td style="white-space:pre-line">{{ $inquiry['message'] }}</td>
    </tr>
</table>

<p style="margin-top:24px;font-size:12px;color:#999">Submitted {{ now()->toDateTimeString() }} UTC from {{ $inquiry['ip'] ?? 'unknown' }}</p>
