<!DOCTYPE html>
<html lang="en">
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-LNPGQ0GN69"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-LNPGQ0GN69');
</script>
<meta charset="UTF-8">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Client Onboarding — SEOAIco</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #080808;
  --deep: #0a0906;
  --ivory: #ede8de;
  --muted: #a8a8a0;
  --gold: #c8a84b;
  --gold-lt: #e2c97d;
  --gold-dim: #9a7a30;
  --border: rgba(200,168,75,.10);
  --input-bg: #111008;
  --input-border: rgba(200,168,75,.18);
  --error: #e05555;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { font-size: 16px; }
body {
  background: var(--bg);
  color: var(--ivory);
  font-family: 'DM Sans', sans-serif;
  font-weight: 300;
  min-height: 100vh;
  padding: 60px 24px 80px;
}

/* ── Layout ── */
.ob-wrap { max-width: 620px; margin: 0 auto; }

/* ── Header ── */
.ob-eye {
  font-size: .64rem;
  letter-spacing: .24em;
  text-transform: uppercase;
  color: var(--gold);
  display: block;
  margin-bottom: 20px;
}
.ob-hed {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 4vw, 2.8rem);
  font-weight: 300;
  line-height: 1.1;
  color: var(--ivory);
  margin-bottom: 12px;
}
.ob-hed em { font-style: italic; color: var(--gold-lt); }
.ob-sub {
  font-size: .92rem;
  color: var(--muted);
  line-height: 1.7;
  margin-bottom: 40px;
  max-width: 480px;
}

/* ── Booking badge ── */
.ob-booking-badge {
  display: inline-flex;
  flex-direction: column;
  gap: 3px;
  padding: 14px 20px;
  border: 1px solid var(--border);
  border-radius: 6px;
  margin-bottom: 40px;
  font-size: .8rem;
  color: var(--muted);
}
.ob-booking-badge strong { color: var(--ivory); font-weight: 400; }

/* ── Section titles ── */
.ob-section {
  font-size: .64rem;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: var(--gold-dim);
  margin: 36px 0 16px;
  padding-bottom: 10px;
  border-bottom: 1px solid var(--border);
}

/* ── Form fields ── */
.ob-field { margin-bottom: 20px; }
.ob-label {
  display: block;
  font-size: .72rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--gold-dim);
  margin-bottom: 8px;
}
.ob-label .req { color: var(--gold); margin-left: 2px; }
.ob-input,
.ob-textarea,
.ob-select {
  display: block;
  width: 100%;
  background: var(--input-bg);
  border: 1px solid var(--input-border);
  border-radius: 4px;
  color: var(--ivory);
  font-family: 'DM Sans', sans-serif;
  font-size: .9rem;
  font-weight: 300;
  padding: 12px 16px;
  outline: none;
  transition: border-color .25s;
  appearance: none;
}
.ob-input:focus,
.ob-textarea:focus,
.ob-select:focus { border-color: rgba(200,168,75,.45); }
.ob-textarea { min-height: 90px; resize: vertical; }
.ob-select option { background: #111; }

/* ── File upload ── */
.ob-file-label {
  display: block;
  border: 1px dashed rgba(200,168,75,.22);
  border-radius: 4px;
  padding: 20px 20px;
  text-align: center;
  cursor: pointer;
  transition: border-color .25s, background .25s;
  font-size: .84rem;
  color: var(--muted);
}
.ob-file-label:hover { border-color: rgba(200,168,75,.45); background: rgba(200,168,75,.03); }
.ob-file-input { display: none; }
.ob-file-chosen { font-size: .78rem; color: var(--gold); margin-top: 8px; display: block; }
.ob-file-hint { font-size: .74rem; color: #666; margin-top: 6px; }

/* ── Radio / toggle ── */
.ob-radio-group { display: flex; gap: 12px; }
.ob-radio-opt { display: none; }
.ob-radio-btn {
  flex: 1;
  text-align: center;
  padding: 11px 14px;
  border: 1px solid var(--input-border);
  border-radius: 4px;
  font-size: .82rem;
  cursor: pointer;
  color: var(--muted);
  transition: border-color .25s, color .25s, background .25s;
  user-select: none;
}
.ob-radio-opt:checked + .ob-radio-btn {
  border-color: var(--gold);
  color: var(--gold);
  background: rgba(200,168,75,.06);
}

/* ── Error messages ── */
.ob-error { color: var(--error); font-size: .8rem; margin-top: 6px; display: block; }

/* ── Submit button ── */
.ob-submit-wrap { margin-top: 40px; }
.ob-submit {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--gold);
  color: #080808;
  font-family: 'DM Sans', sans-serif;
  font-size: .78rem;
  font-weight: 500;
  letter-spacing: .14em;
  text-transform: uppercase;
  padding: 15px 32px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background .25s, transform .2s;
}
.ob-submit:hover { background: var(--gold-lt); transform: translateY(-1px); }
.ob-submit:disabled { opacity: .55; cursor: not-allowed; transform: none; }
.ob-fine {
  font-size: .74rem;
  color: rgba(168,168,160,.45);
  margin-top: 14px;
  line-height: 1.6;
}

/* ── Alert banner ── */
.ob-alert-error {
  background: rgba(224,85,85,.08);
  border: 1px solid rgba(224,85,85,.2);
  border-radius: 6px;
  padding: 14px 18px;
  font-size: .88rem;
  color: #e88;
  margin-bottom: 28px;
}

/* ── Platform instruction box ── */
.ob-instruction {
  display: none;
  background: rgba(200,168,75,.04);
  border: 1px solid rgba(200,168,75,.14);
  border-radius: 6px;
  padding: 16px 18px;
  margin-top: 12px;
  font-size: .84rem;
  color: var(--muted);
  line-height: 1.8;
}
.ob-instruction strong { color: var(--ivory); font-weight: 400; }
.ob-instruction.ob-visible { display: block; }
.ob-instruction ol { margin: 8px 0 0 20px; }
.ob-instruction .ob-invite-email {
  font-size: .82rem;
  color: var(--gold);
  font-style: italic;
  margin-top: 8px;
  display: block;
}

/* ── Access method radio (3-col) ── */
.ob-radio-group-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
@media (max-width: 520px) { .ob-radio-group-3 { grid-template-columns: 1fr; } }
.ob-radio-btn-3 {
  display: block;
  text-align: center;
  padding: 14px 10px;
  border: 1px solid var(--input-border);
  border-radius: 6px;
  font-size: .8rem;
  line-height: 1.4;
  cursor: pointer;
  color: var(--muted);
  transition: border-color .25s, color .25s, background .25s;
  user-select: none;
}
.ob-radio-btn-3 span { display: block; font-size: .72rem; letter-spacing: .04em; margin-top: 3px; color: #555; }
.ob-radio-opt:checked + .ob-radio-btn-3 {
  border-color: var(--gold);
  color: var(--gold);
  background: rgba(200,168,75,.06);
}
.ob-radio-opt:checked + .ob-radio-btn-3 span { color: rgba(200,168,75,.5); }

/* ── Add-on cards ── */
.ob-addons-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 520px) { .ob-addons-grid { grid-template-columns: 1fr; } }
.ob-addon-opt { display: none; }
.ob-addon-card {
  display: flex;
  flex-direction: column;
  gap: 6px;
  padding: 16px 18px;
  border: 1px solid var(--input-border);
  border-radius: 6px;
  cursor: pointer;
  transition: border-color .25s, background .25s;
}
.ob-addon-card:hover { border-color: rgba(200,168,75,.3); }
.ob-addon-opt:checked + .ob-addon-card {
  border-color: var(--gold);
  background: rgba(200,168,75,.05);
}
.ob-addon-name { font-size: .84rem; color: var(--ivory); font-weight: 400; }
.ob-addon-price { font-size: .78rem; color: var(--gold); }
.ob-addon-desc { font-size: .74rem; color: #666; line-height: 1.5; }
.ob-addon-check {
  width: 16px; height: 16px;
  border: 1px solid rgba(200,168,75,.25);
  border-radius: 3px;
  margin-left: auto;
  flex-shrink: 0;
  position: relative;
}
.ob-addon-opt:checked + .ob-addon-card .ob-addon-check {
  background: var(--gold);
  border-color: var(--gold);
}
.ob-addon-opt:checked + .ob-addon-card .ob-addon-check::after {
  content: '✓';
  position: absolute;
  top: -1px; left: 2px;
  font-size: .72rem;
  color: #080808;
}
.ob-addon-header { display: flex; align-items: flex-start; justify-content: space-between; }

/* ── Trust bar ── */
.ob-trust {
  display: flex;
  gap: 20px;
  margin: 32px 0 0;
  padding: 16px 20px;
  border: 1px solid var(--border);
  border-radius: 6px;
  font-size: .74rem;
  color: #666;
  line-height: 1.6;
}
.ob-trust-icon { font-size: 1.1rem; flex-shrink: 0; }

@media (max-width: 520px) {
  body { padding: 40px 20px 60px; }
  .ob-hed { font-size: 1.8rem; }
  .ob-submit { width: 100%; justify-content: center; }
}
</style>
</head>
<body>
<div class="ob-wrap">

  <a href="/" style="display:inline-flex;align-items:baseline;text-decoration:none;line-height:1;margin-bottom:36px">
    <span style="font-family:'DM Sans',sans-serif;font-weight:300;font-size:1.1rem;letter-spacing:.06em;color:var(--ivory)">SEO</span><span style="font-family:'Cormorant Garamond',serif;font-weight:600;font-size:1.3rem;color:var(--gold)">AI</span><span style="font-family:'DM Sans',sans-serif;font-weight:300;font-size:.9rem;color:rgba(150,150,150,.5);letter-spacing:.04em">co</span>
  </a>

  <span class="ob-eye">{{ ($isPreview ?? false) ? 'SEO Opportunity Preview' : 'Client Onboarding' }}</span>
  <h1 class="ob-hed">
    @if($isPreview ?? false)
      Let's map your<br><em>opportunity.</em>
    @else
      Let's get to<br><em>know your business.</em>
    @endif
  </h1>
  <p class="ob-sub">{{ ($isPreview ?? false) ? 'Share a few details and we\'ll analyse what\'s possible for your market.' : 'Complete your intake form to activate your account. This takes about 3 minutes.' }}</p>

  {{-- ── Booking badge (only when a booking exists) ── --}}
  @if($booking)
  <div class="ob-booking-badge">
    <span>Session: <strong>{{ $booking->consultType->name }}</strong></span>
    <span>{{ $booking->preferred_date->format('F j, Y') }} at {{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }} PT</span>
  </div>
  @endif

  {{-- ── Validation errors ── --}}
  @if($errors->any())
  <div class="ob-alert-error">
    <strong>Please correct the following:</strong>
    <ul style="margin-top:8px;padding-left:18px;line-height:1.8">
      @foreach($errors->all() as $e)
      <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <form method="POST" action="{{ route('onboarding.submit') }}" enctype="multipart/form-data" novalidate>
    @csrf
    <input type="hidden" name="booking_id" value="{{ $booking->id ?? '' }}">

    {{-- ── Business Info ── --}}
    <div class="ob-section">Business</div>

    <div class="ob-field">
      <label class="ob-label" for="business_name">Business Name <span class="req">*</span></label>
      <input class="ob-input" type="text" id="business_name" name="business_name"
             value="{{ old('business_name') }}" maxlength="255" required autocomplete="organization">
      @error('business_name')<span class="ob-error">{{ $message }}</span>@enderror
    </div>

    <div class="ob-field">
      <label class="ob-label" for="website">Website</label>
      <input class="ob-input" type="url" id="website" name="website"
             value="{{ old('website') }}" maxlength="500" placeholder="https://" autocomplete="url">
      @error('website')<span class="ob-error">{{ $message }}</span>@enderror
    </div>

    <div class="ob-field">
      <label class="ob-label" for="service_area">Service Area</label>
      <textarea class="ob-textarea" id="service_area" name="service_area"
                maxlength="1000" placeholder="Cities, counties, or states you serve…">{{ old('service_area') }}</textarea>
      @error('service_area')<span class="ob-error">{{ $message }}</span>@enderror
    </div>

    {{-- ── Business License ── --}}
    <div class="ob-section">Business License</div>

    <div class="ob-field">
      <label class="ob-label">License Document <span class="req">*</span></label>
      <label class="ob-file-label" for="license">
        &#128196;&nbsp; Click to upload your business license
        <input class="ob-file-input" type="file" id="license" name="license"
               accept=".pdf,.jpg,.jpeg,.png" required>
        <span class="ob-file-chosen" id="file-chosen-label">
          {{ old('license') ? 'File selected' : 'No file chosen' }}
        </span>
      </label>
      <span class="ob-file-hint">PDF, JPG, or PNG — max 5 MB. Stored securely, never shared publicly.</span>
      @error('license')<span class="ob-error">{{ $message }}</span>@enderror
    </div>

    {{-- ── Contact ── --}}
    <div class="ob-section">Primary Contact</div>

    <div class="ob-field">
      <label class="ob-label" for="primary_contact">Full Name <span class="req">*</span></label>
      <input class="ob-input" type="text" id="primary_contact" name="primary_contact"
             value="{{ old('primary_contact', $booking?->name) }}" maxlength="255" required autocomplete="name">
      @error('primary_contact')<span class="ob-error">{{ $message }}</span>@enderror
    </div>

    @if($booking === null)
    <div class="ob-field">
      <label class="ob-label" for="email">Email <span class="req">*</span></label>
      <input class="ob-input" type="email" id="email" name="email"
             value="{{ old('email') }}" maxlength="255" required autocomplete="email">
      @error('email')<span class="ob-error">{{ $message }}</span>@enderror
    </div>
    @endif

    <div class="ob-field">
      <label class="ob-label" for="phone">Phone <span class="req">*</span></label>
      <input class="ob-input" type="tel" id="phone" name="phone"
             value="{{ old('phone', $booking?->phone) }}" maxlength="50" required autocomplete="tel">
      @error('phone')<span class="ob-error">{{ $message }}</span>@enderror
    </div>

    {{-- ── Ad Readiness ── --}}
    <div class="ob-section">Ad Budget Readiness</div>

    <div class="ob-field">
      <label class="ob-label">Are you ready to run paid ads? <span class="req">*</span></label>
      <div class="ob-radio-group">
        <input type="radio" class="ob-radio-opt" id="ad_yes" name="ad_budget_ready" value="1"
               {{ old('ad_budget_ready') === '1' ? 'checked' : '' }}>
        <label class="ob-radio-btn" for="ad_yes">Yes — I'm ready</label>

        <input type="radio" class="ob-radio-opt" id="ad_no" name="ad_budget_ready" value="0"
               {{ old('ad_budget_ready') === '0' ? 'checked' : '' }}>
        <label class="ob-radio-btn" for="ad_no">Not yet</label>
      </div>
      @error('ad_budget_ready')<span class="ob-error">{{ $message }}</span>@enderror
    </div>

    <div class="ob-field" id="payment-method-wrap" style="{{ old('ad_budget_ready') === '1' ? '' : 'display:none' }}">
      <label class="ob-label" for="payment_method_for_ads">
        Preferred Payment Method for Ads <span style="color:#555">(optional)</span>
      </label>
      <input class="ob-input" type="text" id="payment_method_for_ads" name="payment_method_for_ads"
             value="{{ old('payment_method_for_ads') }}" maxlength="255"
             placeholder="e.g. Credit card, ACH, invoiced…">
      @error('payment_method_for_ads')<span class="ob-error">{{ $message }}</span>@enderror
    </div>

    {{-- ── Access Setup ── --}}
    <div class="ob-section">Access Setup</div>

    <p style="font-size:.84rem;color:var(--muted);line-height:1.7;margin-bottom:20px">
      To manage your SEO and ads we'll need access to a few tools. Let us know what you have set up — we'll send invites via email so you never need to share a password.
    </p>

    <div class="ob-field">
      <label class="ob-label">Google Analytics 4 <span style="color:#555">(do you have access?)</span></label>
      <div class="ob-radio-group">
        <input type="radio" class="ob-radio-opt" id="ga_yes" name="analytics_access" value="1"
               {{ old('analytics_access') === '1' ? 'checked' : '' }}>
        <label class="ob-radio-btn" for="ga_yes">Yes — I have it</label>

        <input type="radio" class="ob-radio-opt" id="ga_no" name="analytics_access" value="0"
               {{ old('analytics_access') === '0' ? 'checked' : '' }}>
        <label class="ob-radio-btn" for="ga_no">No / Not sure</label>
      </div>
    </div>

    <div class="ob-field">
      <label class="ob-label">Google Search Console <span style="color:#555">(do you have access?)</span></label>
      <div class="ob-radio-group">
        <input type="radio" class="ob-radio-opt" id="sc_yes" name="search_console_access" value="1"
               {{ old('search_console_access') === '1' ? 'checked' : '' }}>
        <label class="ob-radio-btn" for="sc_yes">Yes — I have it</label>

        <input type="radio" class="ob-radio-opt" id="sc_no" name="search_console_access" value="0"
               {{ old('search_console_access') === '0' ? 'checked' : '' }}>
        <label class="ob-radio-btn" for="sc_no">No / Not sure</label>
      </div>
    </div>

    <div class="ob-field">
      <label class="ob-label" for="platform_type">Website Platform</label>
      <select class="ob-select" id="platform_type" name="platform_type">
        <option value="" {{ old('platform_type') ? '' : 'selected' }}>— Select your platform —</option>
        <option value="wordpress" {{ old('platform_type') === 'wordpress' ? 'selected' : '' }}>WordPress</option>
        <option value="shopify" {{ old('platform_type') === 'shopify' ? 'selected' : '' }}>Shopify</option>
        <option value="other" {{ old('platform_type') === 'other' ? 'selected' : '' }}>Other / Custom</option>
      </select>
      @error('platform_type')<span class="ob-error">{{ $message }}</span>@enderror
    </div>

    {{-- ── Access Method ── --}}
    <div class="ob-section">How Would You Like to Set Up Access?</div>

    <div class="ob-field">
      <div class="ob-radio-group-3">
        <input type="radio" class="ob-radio-opt" id="access_invite" name="access_method" value="invite_email"
               {{ old('access_method', 'invite_email') === 'invite_email' ? 'checked' : '' }}>
        <label class="ob-radio-btn-3" for="access_invite">
          Invite us via email
          <span>Recommended — no passwords shared</span>
        </label>

        <input type="radio" class="ob-radio-opt" id="access_later" name="access_method" value="provide_later"
               {{ old('access_method') === 'provide_later' ? 'checked' : '' }}>
        <label class="ob-radio-btn-3" for="access_later">
          I'll provide access later
          <span>We'll follow up within 1 business day</span>
        </label>

        <input type="radio" class="ob-radio-opt" id="access_help" name="access_method" value="need_help"
               {{ old('access_method') === 'need_help' ? 'checked' : '' }}>
        <label class="ob-radio-btn-3" for="access_help">
          I need help with this
          <span>We'll walk you through it on your call</span>
        </label>
      </div>
      @error('access_method')<span class="ob-error" style="margin-top:8px;display:block">{{ $message }}</span>@enderror
    </div>

    {{-- Dynamic platform instructions (shown when invite_email selected) --}}
    <div id="instr-wordpress" class="ob-instruction {{ old('access_method', 'invite_email') === 'invite_email' && old('platform_type') === 'wordpress' ? 'ob-visible' : '' }}">
      <strong>WordPress Access Instructions</strong>
      <ol>
        <li>Log in to your WordPress admin panel</li>
        <li>Go to <strong>Users → Add New User</strong></li>
        <li>Enter <strong>invites@seoaico.com</strong> and set role to <strong>Administrator</strong></li>
        <li>Click <strong>Add New User</strong> — we'll receive an email notification</li>
      </ol>
      <span class="ob-invite-email">Our invite email: invites@seoaico.com</span>
    </div>

    <div id="instr-shopify" class="ob-instruction {{ old('access_method', 'invite_email') === 'invite_email' && old('platform_type') === 'shopify' ? 'ob-visible' : '' }}">
      <strong>Shopify Access Instructions</strong>
      <ol>
        <li>From your Shopify admin go to <strong>Settings → Users and permissions</strong></li>
        <li>Click <strong>Add staff</strong></li>
        <li>Enter <strong>invites@seoaico.com</strong> and grant full permissions</li>
        <li>Click <strong>Send invite</strong></li>
      </ol>
      <span class="ob-invite-email">Our invite email: invites@seoaico.com</span>
    </div>

    <div id="instr-other" class="ob-instruction {{ old('access_method', 'invite_email') === 'invite_email' && old('platform_type') === 'other' ? 'ob-visible' : '' }}">
      <strong>Custom Platform Access</strong>
      <p>Please prepare collaborator or admin access for <strong>invites@seoaico.com</strong>. Our team will follow up after your call to confirm the best method for your platform.</p>
      <span class="ob-invite-email">Our invite email: invites@seoaico.com</span>
    </div>

    {{-- ── Optional Add-ons ── --}}
    <div class="ob-section">Optional Add-Ons</div>

    <p style="font-size:.84rem;color:var(--muted);line-height:1.7;margin-bottom:20px">
      Accelerate your results with these optional services. Select any that interest you — our team will follow up with details and a quote before any charges are applied.
    </p>

    <div class="ob-addons-grid">

      <div>
        <input type="checkbox" class="ob-addon-opt" id="addon_local_seo" name="add_ons[]" value="local_seo_setup"
               {{ in_array('local_seo_setup', old('add_ons', [])) ? 'checked' : '' }}>
        <label class="ob-addon-card" for="addon_local_seo">
          <div class="ob-addon-header">
            <span class="ob-addon-name">Local SEO Setup</span>
            <span class="ob-addon-check"></span>
          </div>
          <span class="ob-addon-price">From $199 one-time</span>
          <span class="ob-addon-desc">GMB optimisation, citation cleanup, local schema markup</span>
        </label>
      </div>

      <div>
        <input type="checkbox" class="ob-addon-opt" id="addon_ads_setup" name="add_ons[]" value="google_ads_setup"
               {{ in_array('google_ads_setup', old('add_ons', [])) ? 'checked' : '' }}>
        <label class="ob-addon-card" for="addon_ads_setup">
          <div class="ob-addon-header">
            <span class="ob-addon-name">Google Ads Setup</span>
            <span class="ob-addon-check"></span>
          </div>
          <span class="ob-addon-price">From $299 one-time</span>
          <span class="ob-addon-desc">Campaign build, keyword research, conversion tracking</span>
        </label>
      </div>

      <div>
        <input type="checkbox" class="ob-addon-opt" id="addon_reporting" name="add_ons[]" value="monthly_reporting"
               {{ in_array('monthly_reporting', old('add_ons', [])) ? 'checked' : '' }}>
        <label class="ob-addon-card" for="addon_reporting">
          <div class="ob-addon-header">
            <span class="ob-addon-name">Monthly Reporting</span>
            <span class="ob-addon-check"></span>
          </div>
          <span class="ob-addon-price">$99/month</span>
          <span class="ob-addon-desc">Branded dashboard with rankings, traffic, and ROI summary</span>
        </label>
      </div>

      <div>
        <input type="checkbox" class="ob-addon-opt" id="addon_competitor" name="add_ons[]" value="competitor_analysis"
               {{ in_array('competitor_analysis', old('add_ons', [])) ? 'checked' : '' }}>
        <label class="ob-addon-card" for="addon_competitor">
          <div class="ob-addon-header">
            <span class="ob-addon-name">Competitor Analysis</span>
            <span class="ob-addon-check"></span>
          </div>
          <span class="ob-addon-price">$149 one-time</span>
          <span class="ob-addon-desc">Deep-dive on top 3 local competitors: gaps, backlinks, strategy</span>
        </label>
      </div>

    </div>

    {{-- ── Submit ── --}}
    <div class="ob-submit-wrap">
      <button type="submit" class="ob-submit" id="submit-btn">
        Submit Onboarding &rarr;
      </button>
      <p class="ob-fine">
        Your information is stored securely and used solely to set up your account.<br>
        License files are never publicly accessible. We never share your data.
      </p>
    </div>

    <div class="ob-trust">
      <span class="ob-trust-icon">🔒</span>
      <span>All files are stored on private, encrypted servers — never accessible via a public URL. Access is only granted to authorised SEOAIco team members.</span>
    </div>
  </form>

</div>

<script>
// Show selected filename
document.getElementById('license').addEventListener('change', function() {
  const label = document.getElementById('file-chosen-label');
  label.textContent = this.files.length ? this.files[0].name : 'No file chosen';
});

// Show/hide payment method field
document.querySelectorAll('[name="ad_budget_ready"]').forEach(function(el) {
  el.addEventListener('change', function() {
    const wrap = document.getElementById('payment-method-wrap');
    wrap.style.display = this.value === '1' ? '' : 'none';
  });
});

// Dynamic platform instructions
function updateInstructions() {
  const method = document.querySelector('[name="access_method"]:checked')?.value;
  const platform = document.getElementById('platform_type')?.value;
  const show = method === 'invite_email' && !!platform;

  ['wordpress', 'shopify', 'other'].forEach(function(p) {
    const el = document.getElementById('instr-' + p);
    if (el) el.classList.toggle('ob-visible', show && platform === p);
  });
}
document.querySelectorAll('[name="access_method"]').forEach(el => el.addEventListener('change', updateInstructions));
document.getElementById('platform_type').addEventListener('change', updateInstructions);

// Disable submit on submit to prevent double-post
document.querySelector('form').addEventListener('submit', function() {
  const btn = document.getElementById('submit-btn');
  btn.disabled = true;
  btn.textContent = 'Submitting…';
});
</script>
<script>
  if(typeof gtag==='function'){gtag('event','onboarding_start',{page_location:window.location.href});}
</script>
</body>
</html>
