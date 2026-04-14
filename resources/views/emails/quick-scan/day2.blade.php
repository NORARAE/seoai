<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<div style="max-width:560px;margin:40px auto;background:#ffffff;overflow:hidden;border:1px solid #e8e8e8">

  <!-- Header -->
  <div style="background:#080808;padding:28px 24px;text-align:center">
    <span style="font-size:17px;color:#ede8de;font-weight:300;letter-spacing:.04em">SEO<span style="color:#c8a84b;font-weight:500;font-size:19px;font-style:italic">AI</span> Co&#8482;</span>
    <p style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.5);margin:6px 0 0">AI Citation</p>
  </div>

  <!-- Body -->
  <div style="padding:32px 28px">
    <h1 style="font-size:20px;font-weight:400;color:#111;margin:0 0 16px;line-height:1.3">You're closer than you think</h1>

    <p style="font-size:14px;color:#555;margin:0 0 16px;line-height:1.65">
      Your scan of <strong style="color:#333">{{ $scan->url }}</strong> returned a score of <strong style="color:#c8a84b">{{ $scan->score ?? 0 }}/100</strong>.
    </p>

    <p style="font-size:14px;color:#555;margin:0 0 16px;line-height:1.65">
      That might feel discouraging — but the gap between where you are and where AI systems start citing you is often smaller than most businesses expect.
    </p>

    @if ($scan->fastest_fix)
    <div style="background:#f9f8f5;border:1px solid rgba(200,168,75,.25);padding:16px 18px;margin-bottom:20px">
      <p style="font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#c8a84b;margin:0 0 8px;">Your highest-impact fix</p>
      <p style="font-size:14px;color:#333;margin:0;line-height:1.6">{{ $scan->fastest_fix }}</p>
    </div>
    @endif

    <p style="font-size:14px;color:#555;margin:0 0 12px;line-height:1.65">
      The businesses that get cited by AI aren't the ones with perfect websites. They're the ones whose sites are <em>structured</em> correctly — with schema, definitions, FAQ content, and clear entity signals.
    </p>

    <p style="font-size:14px;color:#555;margin:0 0 16px;line-height:1.65">
      Our <strong style="color:#333">Citation Builder</strong> handles schema implementation, Q&amp;A templates, definition pages, and internal linking — the exact signals your score is based on — in one structured engagement.
    </p>

    <!-- Comparison mini-table -->
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin:0 0 24px">
      <tr>
        <td style="font-size:12px;font-weight:600;color:#c8a84b;text-transform:uppercase;letter-spacing:.08em;padding:8px 12px;background:#f9f8f5;border:1px solid #eee">Without AI citation structure</td>
        <td style="font-size:12px;font-weight:600;color:#6aaf90;text-transform:uppercase;letter-spacing:.08em;padding:8px 12px;background:#f9f8f5;border:1px solid #eee">With AI citation structure</td>
      </tr>
      <tr>
        <td style="font-size:13px;color:#777;padding:10px 12px;border:1px solid #eee;line-height:1.45">Invisible in AI-generated answers</td>
        <td style="font-size:13px;color:#555;padding:10px 12px;border:1px solid #eee;line-height:1.45">Cited by ChatGPT, Gemini, Perplexity</td>
      </tr>
      <tr>
        <td style="font-size:13px;color:#777;padding:10px 12px;border:1px solid #eee;line-height:1.45;background:#fafafa">Losing traffic to AI summaries</td>
        <td style="font-size:13px;color:#555;padding:10px 12px;border:1px solid #eee;line-height:1.45;background:#fafafa">Appearing in AI overviews</td>
      </tr>
      <tr>
        <td style="font-size:13px;color:#777;padding:10px 12px;border:1px solid #eee;line-height:1.45">Competitors being recommended instead</td>
        <td style="font-size:13px;color:#555;padding:10px 12px;border:1px solid #eee;line-height:1.45">Being the go-to recommendation</td>
      </tr>
    </table>

    <div style="text-align:center;margin:20px 0">
      <a href="{{ url('/pricing') }}" style="display:inline-block;background:#c8a84b;color:#080808;font-size:12px;font-weight:600;text-decoration:none;padding:13px 36px;letter-spacing:.08em">See the fix</a>
    </div>

    <p style="font-size:12px;color:#bbb;text-align:center;margin:16px 0 0">
      Or <a href="{{ url('/book') }}" style="color:#c8a84b;text-decoration:none">book a free strategy call</a> to talk through your score.
    </p>
  </div>

  <!-- Footer -->
  <div style="background:#f5f5f4;padding:16px 24px;text-align:center;border-top:1px solid #e8e8e8">
    <p style="font-size:11px;color:#bbb;margin:0">SEO AI Co&#8482; &middot; <a href="{{ url('/') }}" style="color:#bbb">seoaico.com</a></p>
  </div>

</div>
</body>
</html>
