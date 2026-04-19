<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — SEOAIco</title>
    <link rel="canonical" href="{{ url('/login') }}">
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
            --green: #6aaf90;
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

        .login-container {
            width: 100%;
            max-width: 400px;
        }

        /* Logo */
        .logo {
            text-align: center;
            margin-bottom: 2.5rem;
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

        /* Heading */
        .heading {
            text-align: center;
            margin-bottom: 2rem;
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
        }

        /* Google button */
        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .65rem;
            width: 100%;
            padding: .9rem 1.1rem;
            background: #ffffff;
            border: 1px solid #dadce0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,.18);
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
            font-size: .92rem;
            font-weight: 600;
            color: #111;
            cursor: pointer;
            transition: box-shadow .2s ease, background .18s ease, transform .15s ease;
        }
        .google-btn:hover {
            background: #f8f9fa;
            box-shadow: 0 4px 16px rgba(0,0,0,.22);
            transform: translateY(-1px);
        }
        .google-btn.is-recommended {
            border-color: rgba(200,168,75,.8);
            box-shadow: 0 0 0 1px rgba(200,168,75,.28), 0 10px 24px rgba(0,0,0,.28);
        }
        .google-recommend {
            text-align: center;
            margin-bottom: .75rem;
        }
        .google-recommend span {
            display: inline-block;
            padding: .22rem .75rem;
            border: 1px solid var(--border);
            border-radius: 20px;
            font-size: .67rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(168,120,40,.9);
            font-weight: 500;
        }
        .google-recommend.is-active span {
            color: #cfb167;
            border-color: rgba(200,168,75,.32);
            background: rgba(200,168,75,.08);
        }
        .google-helper {
            text-align: center;
            font-size: .71rem;
            color: var(--muted);
            margin: .55rem 0 0;
        }
        .google-soft-state {
            color: rgba(200,168,75,.88);
            margin-top: .35rem;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 1.5rem 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .divider span {
            font-size: .7rem;
            color: rgba(178,138,60,.9);
            letter-spacing: .11em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Form */
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            font-size: .75rem;
            font-weight: 500;
            color: rgba(200,168,75,.7);
            letter-spacing: .04em;
            margin-bottom: .4rem;
        }
        .form-group input {
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
        .form-group input:focus {
            border-color: var(--gold);
        }
        .form-group input::placeholder {
            color: rgba(168,168,156,.35);
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .78rem;
            color: var(--muted);
            cursor: pointer;
        }
        .remember-label input[type="checkbox"] {
            accent-color: var(--gold);
        }
        .forgot-link {
            font-size: .78rem;
            color: var(--gold);
            text-decoration: none;
            opacity: .8;
            transition: opacity .2s;
        }
        .forgot-link:hover { opacity: 1; }

        /* Submit button */
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
        }
        .submit-btn:hover {
            opacity: .9;
            transform: translateY(-1px);
        }

        /* Error */
        .error-box {
            background: rgba(196,120,120,.12);
            border: 1px solid rgba(196,120,120,.25);
            border-radius: 8px;
            padding: .85rem 1rem;
            margin-bottom: 1.25rem;
        }
        .error-box p {
            font-size: .8rem;
            color: var(--red);
            line-height: 1.5;
        }
        .error-box .recovery-actions {
            margin-top: .75rem;
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }
        .error-box .recovery-actions a {
            font-size: .72rem;
            font-weight: 500;
            color: var(--gold);
            text-decoration: none;
            padding: .3rem .7rem;
            border: 1px solid var(--border);
            border-radius: 4px;
            transition: background .15s;
        }
        .error-box .recovery-actions a:hover {
            background: rgba(200,168,75,.1);
        }
        .error-box .recovery-actions a.action-pill-primary {
            color: #17130a;
            background: rgba(200,168,75,.92);
            border-color: rgba(200,168,75,.92);
        }
        .error-box .recovery-actions a.action-pill-primary:hover {
            background: rgba(200,168,75,.82);
        }

        .login-guidance {
            margin: .25rem 0 .9rem;
            text-align: center;
            font-size: .73rem;
            color: rgba(168,168,156,.72);
            letter-spacing: .01em;
        }

        .form-error {
            font-size: .75rem;
            color: var(--red);
            margin-top: .3rem;
        }

        /* Footer links */
        .footer-links {
            text-align: center;
            margin-top: 2rem;
            font-size: .78rem;
            color: var(--muted);
        }
        .footer-links a {
            color: var(--gold);
            text-decoration: none;
            opacity: .8;
        }
        .footer-links a:hover { opacity: 1; }
        .footer-sep { margin: 0 .5rem; }

        /* Subtle footer */
        .site-footer {
            margin-top: 3rem;
            text-align: center;
            font-size: .68rem;
            color: rgba(168,168,156,.35);
        }
        .site-footer a { color: rgba(168,168,156,.35); text-decoration: none; }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Logo -->
    <div class="logo">
        <a href="/">
            <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
        </a>
    </div>

    <!-- Heading -->
    <div class="heading">
        <h1>Sign in to your system</h1>
        <p>Your AI visibility intelligence platform</p>
    </div>

    @php
        $loginErrorType = session('login_error_type');
        $loginErrorMessage = session('login_error_message');
        $googleRecommendedEmail = $loginErrorType === 'google_account' ? strtolower((string) old('email')) : '';
        $googleStateActive = $googleEnabled && $loginErrorType === 'google_account';
    @endphp

    <!-- Google OAuth error with contextual recovery -->
    @if($error)
    <div class="error-box">
        <p>{{ $error }}</p>
        <div class="recovery-actions">
            @if($errorType === 'no-account')
                @if($googleEnabled)
                <a href="{{ route('auth.google.redirect') }}">Create account with Google</a>
                @endif
                <a href="{{ route('register') }}">Register with email</a>
            @elseif($errorType === 'account-exists')
                @if($googleEnabled)
                <a href="{{ route('auth.google.redirect') }}">Try Google again</a>
                @endif
                <a href="/admin/password-reset/request">Reset your password</a>
            @else
                @if($googleEnabled)
                <a href="{{ route('auth.google.redirect') }}">Try Google again</a>
                @endif
                <a href="/admin/password-reset/request">Reset password</a>
            @endif
        </div>
    </div>
    @endif

    @if(session('status'))
    <div class="error-box" style="border-color:rgba(106,175,144,.34);background:rgba(106,175,144,.09)">
        <p style="color:#d9eee5">{{ session('status') }}</p>
    </div>
    @endif

    @if(!empty($checkoutNotice))
    <div class="error-box" style="border-color:rgba(106,175,144,.34);background:rgba(106,175,144,.09)">
        <p style="color:#d9eee5">{{ $checkoutNotice }}</p>
    </div>
    @endif

    @if($loginErrorType && $loginErrorMessage)
    <div class="error-box" id="login-state-message" data-login-error-type="{{ $loginErrorType }}">
        <p>{{ $loginErrorMessage }}</p>
        <div class="recovery-actions">
            @if($loginErrorType === 'google_account')
                @if($googleEnabled)
                <a href="{{ route('auth.google.redirect') }}" class="action-pill-primary">Continue with Google</a>
                @endif
                <a href="/admin/password-reset/request">Set an email password</a>
            @elseif($loginErrorType === 'wrong_password')
                <a href="/admin/password-reset/request" class="action-pill-primary">Reset password</a>
                @if($googleEnabled)
                <a href="{{ route('auth.google.redirect') }}">Try Google instead</a>
                @endif
            @elseif($loginErrorType === 'no_account')
                <a href="{{ route('register') }}" class="action-pill-primary">Create account</a>
                @if($googleEnabled)
                <a href="{{ route('auth.google.redirect') }}">Continue with Google</a>
                @endif
            @endif
        </div>
    </div>
    @endif

    <!-- Validation errors -->
    @if($errors->any())
    <div class="error-box">
        <p>{{ $errors->first('email') }}</p>
    </div>
    @endif

    <!-- Google Sign-In -->
    @if($googleEnabled)
    <div class="google-recommend {{ $googleStateActive ? 'is-active' : '' }}" id="google-recommend">
        <span id="google-recommend-label">{{ $googleStateActive ? 'Recommended for this email' : 'Recommended' }}</span>
    </div>
    <a href="{{ route('auth.google.redirect') }}" class="google-btn {{ $googleStateActive ? 'is-recommended' : '' }}" id="google-login-button">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
            <path fill="#FFC107" d="M43.6 20H24v8h11.3c-1.6 4.7-6.1 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.8 1.2 8 3l5.7-5.7C34 6.1 29.3 4 24 4 13 4 4 13 4 24s9 20 20 20 20-9 20-20c0-1.3-.1-2.7-.4-4z"/>
            <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 15.1 19 12 24 12c3 0 5.8 1.2 8 3l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>
            <path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.5 35 26.9 36 24 36c-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z"/>
            <path fill="#1976D2" d="M43.6 20H24v8h11.3c-.7 2-2 3.8-3.6 5.2l6.2 5.2C41 35.2 44 30 44 24c0-1.3-.1-2.7-.4-4z"/>
        </svg>
        <span>Continue with Google</span>
    </a>
    <p class="google-helper google-soft-state" id="google-soft-suggestion" @if(!$googleStateActive) style="display:none;" @endif>This email is set up for Google sign-in.</p>
    <p class="google-helper">Used by most customers &mdash; fastest access</p>

    <div class="divider"><span>or continue with email</span></div>
    @endif

    <!-- Email/Password Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <p class="login-guidance">Using Google? Just click Continue with Google above.</p>

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@company.com" required autocomplete="email" autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required autocomplete="current-password">
        </div>

        <div class="form-row">
            <label class="remember-label">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>
            <a href="/admin/password-reset/request" class="forgot-link">Forgot password?</a>
        </div>

        <button type="submit" class="submit-btn">Sign In</button>
    </form>

    <div class="footer-links">
        Don't have an account?<span class="footer-sep">&middot;</span><a href="{{ route('register') }}">Request access</a>
    </div>
</div>

<div class="site-footer">
    <p>&copy; {{ date('Y') }} SEOAIco &middot; <a href="{{ url('/') }}">seoaico.com</a></p>
</div>

@if($googleEnabled)
<script>
(() => {
    const emailInput = document.getElementById('email');
    const googleButton = document.getElementById('google-login-button');
    const recommendWrap = document.getElementById('google-recommend');
    const recommendLabel = document.getElementById('google-recommend-label');
    const softSuggestion = document.getElementById('google-soft-suggestion');
    const loginStateMessage = document.getElementById('login-state-message');
    const recommendedEmail = @json($googleRecommendedEmail);

    if (!emailInput || !googleButton || !recommendWrap || !recommendLabel || !softSuggestion) {
        return;
    }

    const normalize = (value) => value.trim().toLowerCase();

    const setRecommended = (enabled) => {
        googleButton.classList.toggle('is-recommended', enabled);
        recommendWrap.classList.toggle('is-active', enabled);
        softSuggestion.style.display = enabled ? 'block' : 'none';
        recommendLabel.textContent = enabled ? 'Recommended for this email' : 'Recommended';
    };

    const syncRecommendation = () => {
        if (!recommendedEmail) {
            setRecommended(false);
            return;
        }

        setRecommended(normalize(emailInput.value) === recommendedEmail);
    };

    emailInput.addEventListener('input', syncRecommendation);
    emailInput.addEventListener('blur', syncRecommendation);
    syncRecommendation();

    if (loginStateMessage && loginStateMessage.dataset.loginErrorType === 'google_account') {
        googleButton.focus({ preventScroll: true });
        googleButton.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
})();
</script>
@endif

</body>
</html>
