<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
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

@media (max-width: 520px) {
  body { padding: 40px 20px 60px; }
  .ob-hed { font-size: 1.8rem; }
  .ob-submit { width: 100%; justify-content: center; }
}
</style>
</head>
<body>
<div class="ob-wrap">

  <span class="ob-eye">Client Onboarding</span>
  <h1 class="ob-hed">Let's get to<br><em>know your business.</em></h1>
  <p class="ob-sub">Complete your intake form to activate your account. This takes about 3 minutes.</p>

  {{-- ── Booking badge ── --}}
  <div class="ob-booking-badge">
    <span>Session: <strong>{{ $booking->consultType->name }}</strong></span>
    <span>{{ $booking->preferred_date->format('F j, Y') }} at {{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }} PT</span>
  </div>

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
    <input type="hidden" name="booking_id" value="{{ $booking->id }}">

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
             value="{{ old('primary_contact', $booking->name) }}" maxlength="255" required autocomplete="name">
      @error('primary_contact')<span class="ob-error">{{ $message }}</span>@enderror
    </div>

    <div class="ob-field">
      <label class="ob-label" for="phone">Phone <span class="req">*</span></label>
      <input class="ob-input" type="tel" id="phone" name="phone"
             value="{{ old('phone', $booking->phone) }}" maxlength="50" required autocomplete="tel">
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

    {{-- ── Submit ── --}}
    <div class="ob-submit-wrap">
      <button type="submit" class="ob-submit" id="submit-btn">
        Submit Onboarding &rarr;
      </button>
      <p class="ob-fine">
        Your information is stored securely and used solely to set up your account.<br>
        License files are never publicly accessible.
      </p>
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

// Disable submit on submit to prevent double-post
document.querySelector('form').addEventListener('submit', function() {
  const btn = document.getElementById('submit-btn');
  btn.disabled = true;
  btn.textContent = 'Submitting…';
});
</script>
</body>
</html>
