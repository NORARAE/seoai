<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;overflow:hidden;border:1px solid #e8e8e8">

  <!-- Header -->
  <div style="background:#080808;padding:28px 24px;text-align:center">
    <span style="font-size:17px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:19px;font-style:italic">AI</span> Co&#8482;</span>
    <p style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.5);margin:6px 0 0">Secure Account Access</p>
  </div>

  <!-- Body -->
  <div style="padding:32px 24px">
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 10px;line-height:1.3">Reset your password</h1>
    <p style="font-size:13px;color:#666;margin:0 0 24px;line-height:1.7">We received a request to reset the password for your SEOAIco account. Click below to choose a new password.</p>

    <!-- CTA Button -->
    <div style="text-align:center;margin:28px 0">
      <a href="{{ $url }}" style="display:inline-block;background:#c8a84b;color:#080808;font-size:13px;font-weight:600;text-decoration:none;padding:14px 40px;letter-spacing:.06em">Reset Password</a>
    </div>

    <!-- Expiry notice -->
    <div style="background:#fafaf8;border-left:3px solid #c8a84b;padding:12px 16px;margin-bottom:24px">
      <p style="font-size:12px;color:#888;margin:0;line-height:1.6">This link expires in <strong style="color:#555">{{ $expireMinutes }} minutes</strong>. After that, you'll need to request a new one.</p>
    </div>

    <p style="font-size:12px;color:#999;margin:0 0 8px;line-height:1.6">If you didn't request this reset, you can safely ignore this email — your password won't change.</p>

    <!-- Fallback URL -->
    <div style="margin-top:24px;padding-top:20px;border-top:1px solid #eee">
      <p style="font-size:11px;color:#bbb;margin:0 0 6px">If the button doesn't work, copy and paste this link into your browser:</p>
      <p style="font-size:11px;color:#c8a84b;margin:0;word-break:break-all;line-height:1.5">{{ $url }}</p>
    </div>
  </div>

  <!-- Footer -->
  <div style="background:#f5f5f4;padding:16px 24px;text-align:center;border-top:1px solid #e8e8e8">
    <p style="font-size:11px;color:#bbb;margin:0">SEO AI Co&#8482; &middot; <a href="{{ url('/') }}" style="color:#bbb">seoaico.com</a></p>
  </div>

</div>
</body>
</html>
