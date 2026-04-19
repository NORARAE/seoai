<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Checkout Cancelled — Start Over Anytime | SEOAIco</title>
    <meta name="robots" content="noindex,nofollow">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#080808;color:#ede8de;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:2rem}
        .card{max-width:540px;text-align:center}
        h1{font-size:2rem;font-weight:600;margin-bottom:.75rem;color:#ede8de}
        p{font-size:1.05rem;line-height:1.6;color:#a0a0a0;margin-bottom:1.25rem}
        a{color:#c8a84b;text-decoration:none;font-weight:600}
        a:hover{text-decoration:underline}
    </style>
@include('partials.clarity')
</head>
<body>
    <div class="card">
        <h1>Checkout Cancelled</h1>
        <p>No payment was processed. You can close this tab and try again from your WordPress admin panel.</p>
        <p>
            Questions? <a href="mailto:hello@seoaico.com">hello@seoaico.com</a>
        </p>
    </div>
<script>
(function(){
    fetch('/api/v1/track',{
        method:'POST',
        headers:{'Content-Type':'application/json','Accept':'application/json'},
        body:JSON.stringify({event:'checkout_cancelled',metadata:{flow:'direct_checkout',source_page:'checkout_cancelled'}})
    }).catch(function(){});
})();
</script>
@include('components.tm-style')
</body>
</html>
