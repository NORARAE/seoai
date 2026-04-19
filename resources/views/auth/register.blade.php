<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Access — SEOAIco</title>
    <link rel="canonical" href="{{ url('/register') }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #080808;
            --surface: #111111;
            --border: rgba(111,84,29,.18);
            --gold: #c8a84b;
            --gold-dim: rgba(200,168,75,.5);
            --ivory: #ede8de;
            --muted: rgba(168,168,156,.65);
            --red: #c47878;
        }

        body {
            font-family: 'DM Sans', -apple-system, sans-serif;
            background: var(--bg);
            color: var(--ivory);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .register-container {
            width: 100%;
            max-width: 460px;
        }

        .logo {
            text-align: center;
            margin-bottom: 2.2rem;
        }
        .logo a {
            text-decoration: none;
            display: inline-flex;
            align-items: baseline;
        }
        .logo-seo {
            font-family: 'DM Sans', sans-serif;
            font-weight: 300;
            font-size: 1.5rem;
            letter-spacing: .06em;
            color: var(--ivory);
        }
        .logo-ai {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
            font-size: 1.8rem;
            color: var(--gold);
            letter-spacing: .02em;
            display: inline-block;
            transform: skewX(-11deg) translateY(-1px);
        }
        .logo-co {
            font-family: 'DM Sans', sans-serif;
            font-weight: 300;
            font-size: 1.2rem;
            color: rgba(255,255,255,.45);
            letter-spacing: .04em;
        }

        .heading {
            text-align: center;
            margin-bottom: 1.6rem;
        }
        .heading h1 {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 300;
            font-size: 1.6rem;
            color: var(--ivory);
            margin-bottom: .35rem;
        }
        .heading p {
            font-size: .78rem;
            color: var(--muted);
            letter-spacing: .01em;
            line-height: 1.55;
        }

        .form-group { margin-bottom: .95rem; }
        .form-group label {
            display: block;
            font-size: .75rem;
            font-weight: 500;
            color: rgba(200,168,75,.7);
            letter-spacing: .04em;
            margin-bottom: .4rem;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: .75rem 1rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--ivory);
            font-family: 'DM Sans', sans-serif;
            font-size: .88rem;
            outline: none;
            transition: border-color .2s;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--gold);
        }
        .form-group input::placeholder { color: rgba(168,168,156,.35); }

        .submit-btn {
            width: 100%;
            padding: .85rem;
            background: var(--gold);
            color: var(--bg);
            border: none;
            border-radius: 6px;
            font-family: 'DM Sans', sans-serif;
            font-size: .88rem;
            font-weight: 600;
            letter-spacing: .04em;
            cursor: pointer;
            transition: opacity .2s, transform .15s;
            margin-top: .35rem;
        }
        .submit-btn:hover {
            opacity: .92;
            transform: translateY(-1px);
        }

        .error-box {
            background: rgba(196,120,120,.12);
            border: 1px solid rgba(196,120,120,.25);
            border-radius: 8px;
            padding: .85rem 1rem;
            margin-bottom: 1rem;
        }
        .error-box p {
            font-size: .8rem;
            color: var(--red);
            line-height: 1.5;
        }

        .help-text {
            font-size: .72rem;
            color: var(--muted);
            margin-top: .32rem;
            line-height: 1.45;
        }

        .footer-links {
            text-align: center;
            margin-top: 1.25rem;
            font-size: .78rem;
            color: var(--muted);
        }
        .footer-links a {
            color: var(--gold);
            text-decoration: none;
            opacity: .8;
        }
        .footer-links a:hover { opacity: 1; }

        .site-footer {
            margin-top: 2.2rem;
            text-align: center;
            font-size: .68rem;
            color: rgba(168,168,156,.35);
        }
        .site-footer a { color: rgba(168,168,156,.35); text-decoration: none; }
    </style>
</head>
<body>

<div class="register-container">
    <div class="logo">
        <a href="/">
            <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
        </a>
    </div>

    <div class="heading">
        <h1>Request access to the system</h1>
        <p>Access is reviewed and granted based on fit, market, and intent.</p>
    </div>

    @if($errors->any())
    <div class="error-box">
        <p>{{ $errors->first() }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="form-group">
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Jane Smith" required autocomplete="name" autofocus>
        </div>

        <div class="form-group">
            <label for="email">Work email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@yourcompany.com" required autocomplete="email">
        </div>

        <div class="form-group">
            <label for="password">Create a password</label>
            <input type="password" id="password" name="password" placeholder="••••••••••••" required autocomplete="new-password">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••••••" required autocomplete="new-password">
        </div>

        <div class="form-group">
            <label for="use_case">Primary objective</label>
            <select id="use_case" name="use_case" required>
                <option value="">Select your objective</option>
                <option value="Build visibility" {{ old('use_case') === 'Build visibility' ? 'selected' : '' }}>Build my own market visibility</option>
                <option value="Agency" {{ old('use_case') === 'Agency' ? 'selected' : '' }}>Manage multiple clients / agency</option>
                <option value="Evaluate" {{ old('use_case') === 'Evaluate' ? 'selected' : '' }}>Evaluate system capabilities</option>
                <option value="Other" {{ old('use_case') === 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="access_code">Priority access code (optional)</label>
            <input type="password" id="access_code" name="access_code" placeholder="Optional" autocomplete="off">
            <p class="help-text">If provided and valid, your account may be approved immediately.</p>
        </div>

        <button type="submit" class="submit-btn">Submit for Review</button>
    </form>

    <div class="footer-links">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
        @if($googleEnabled)
        &nbsp;&middot;&nbsp;<a href="{{ route('auth.google.redirect') }}">Continue with Google</a>
        @endif
    </div>
</div>

<div class="site-footer">
    <p>&copy; {{ date('Y') }} SEOAIco &middot; <a href="{{ url('/') }}">seoaico.com</a></p>
</div>

</body>
</html>
