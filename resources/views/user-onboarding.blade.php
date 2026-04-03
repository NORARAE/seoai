<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup Your Workspace — SEO AI Co™</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
<style>
*,*::before,*::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --bg: #0c0c0c;
  --gold: #6f541d;
  --gold-mid: #7a5e22;
  --gold-lt: #a87828;
  --gold-bright: #c49235;
  --ivory: #f1f5f9;
  --muted: #94a3b8;
  --dim: #64748b;
  --card: linear-gradient(160deg, #1f1f1f 0%, #161616 100%);
  --border: rgba(111,84,29,.3);
  --input-bg: #232323;
}

html { font-size: 16px; }

body {
  background: var(--bg);
  background-image: radial-gradient(ellipse 70% 50% at 50% -5%, rgba(111,84,29,.07), transparent);
  color: var(--ivory);
  font-family: 'DM Sans', ui-sans-serif, sans-serif;
  font-weight: 400;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  padding: 48px 20px 80px;
}

/* ── Logo ── */
.logo {
  display: inline-flex;
  align-items: baseline;
  text-decoration: none;
  line-height: 1;
  margin-bottom: 40px;
}
.logo-seo { font-family: 'DM Sans', sans-serif; font-weight: 300; font-size: 1.15rem; letter-spacing: .06em; color: #fff; }
.logo-ai  {
  font-family: 'Cormorant Garamond', serif; font-weight: 600; font-size: 1.35rem;
  color: var(--gold-lt); letter-spacing: .02em;
  display: inline-block; transform: skewX(-11deg) translateY(-1px);
}
.logo-co { font-family: 'DM Sans', sans-serif; font-weight: 300; font-size: 1rem; color: rgba(255,255,255,.4); letter-spacing: .04em; }

/* ── Step progress ── */
.step-track {
  display: flex;
  align-items: center;
  width: 100%;
  max-width: 560px;
  margin-bottom: 32px;
  gap: 0;
}
.step-node {
  width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: .7rem; font-weight: 600; letter-spacing: .04em;
  border: 1px solid rgba(111,84,29,.25);
  color: rgba(111,84,29,.4);
  background: transparent;
  transition: all .3s ease;
}
.step-node.active { border-color: var(--gold-mid); background: var(--gold); color: #fff; }
.step-node.done   { border-color: rgba(111,84,29,.5); background: rgba(111,84,29,.15); color: var(--gold-lt); }
.step-line {
  flex: 1; height: 1px; background: rgba(111,84,29,.15);
  transition: background .4s ease;
}
.step-line.done { background: rgba(111,84,29,.5); }

/* ── Card ── */
.card {
  background: var(--card);
  border: 1px solid var(--border);
  box-shadow: 0 24px 64px rgba(0,0,0,.65), 0 0 0 1px rgba(111,84,29,.1), inset 0 1px 0 rgba(255,255,255,.04);
  border-radius: 14px;
  padding: 48px 44px;
  width: 100%;
  max-width: 560px;
}

/* ── Step panel ── */
.step-panel { min-height: 340px; display: flex; flex-direction: column; }

.step-eyebrow {
  font-size: .7rem; letter-spacing: .14em; text-transform: uppercase;
  color: var(--gold-lt); margin-bottom: 10px; font-weight: 500;
}
.step-heading { margin-bottom: 32px; }
.step-heading h2 {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.85rem; font-weight: 400; line-height: 1.2;
  color: var(--ivory); margin-bottom: 6px;
}
.step-heading p { font-size: .88rem; color: var(--muted); line-height: 1.55; }

/* ── Fields ── */
.field { margin-bottom: 20px; }
.field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }

.field-label {
  display: block;
  font-size: .72rem; letter-spacing: .1em; text-transform: uppercase;
  color: var(--muted); margin-bottom: 7px; font-weight: 500;
}

input[type="text"],
input[type="url"],
input[type="email"],
.select-wrap select,
textarea {
  width: 100%;
  background: var(--input-bg);
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 7px;
  color: var(--ivory);
  font-family: 'DM Sans', sans-serif;
  font-size: .9rem;
  font-weight: 400;
  padding: .68rem .9rem;
  outline: none;
  transition: border-color .2s ease, box-shadow .2s ease;
  -webkit-appearance: none;
  color-scheme: dark;
}
input[type="text"]:focus,
input[type="url"]:focus,
input[type="email"]:focus,
.select-wrap select:focus,
textarea:focus {
  border-color: rgba(111,84,29,.7);
  box-shadow: 0 0 0 3px rgba(111,84,29,.13), 0 1px 3px rgba(0,0,0,.3);
}
input::placeholder,
textarea::placeholder { color: var(--dim); }

.select-wrap { position: relative; }
.select-wrap::after {
  content: '▾';
  position: absolute; right: .9rem; top: 50%; transform: translateY(-50%);
  color: rgba(111,84,29,.7); pointer-events: none; font-size: .8rem;
}
.select-wrap select option { background: #1f1f1f; color: var(--ivory); }

textarea { resize: vertical; min-height: 110px; line-height: 1.6; }

/* ── Service tags (pill checkboxes) ── */
.tags-label {
  font-size: .72rem; letter-spacing: .1em; text-transform: uppercase;
  color: var(--muted); margin-bottom: 11px; font-weight: 500; display: block;
}
.tags-grid {
  display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px;
}
.tag input[type="checkbox"] { display: none; }
.tag span {
  display: inline-block;
  padding: .32rem .78rem;
  border: 1px solid rgba(111,84,29,.3);
  border-radius: 20px;
  font-size: .8rem; color: var(--muted);
  cursor: pointer;
  transition: border-color .2s, background .2s, color .2s;
  user-select: none;
}
.tag input[type="checkbox"]:checked + span {
  border-color: var(--gold-mid);
  background: rgba(111,84,29,.16);
  color: var(--gold-bright);
}
.tag:hover span { border-color: rgba(111,84,29,.5); color: var(--ivory); }

/* ── Navigation buttons ── */
.btn-row {
  display: flex; align-items: center; justify-content: space-between;
  margin-top: auto; padding-top: 28px; gap: 12px;
}
.btn-back {
  background: none; border: none; padding: 0;
  font-size: .82rem; letter-spacing: .06em; color: var(--dim);
  cursor: pointer; font-family: 'DM Sans', sans-serif;
  transition: color .2s ease;
}
.btn-back:hover { color: var(--muted); }
.btn-primary {
  flex: 1;
  background: linear-gradient(180deg, #7a5e22 0%, #5b4416 100%);
  border: 1px solid #4a3610;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.09);
  color: #ffffff;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  font-size: .9rem; font-weight: 600;
  letter-spacing: .04em;
  padding: .78rem 1.5rem;
  text-shadow: 0 1px 2px rgba(0,0,0,.35);
  transition: background .2s ease, box-shadow .2s ease;
  -webkit-appearance: none;
}
.btn-primary:hover {
  background: linear-gradient(180deg, #896827 0%, #6a501c 100%);
  box-shadow: 0 4px 14px rgba(0,0,0,.55), inset 0 1px 0 rgba(255,255,255,.12);
}
.btn-primary:active { transform: translateY(1px); }

/* ── Error summary ── */
.error-box {
  background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3);
  border-radius: 8px; padding: 14px 16px; margin-bottom: 24px;
  font-size: .83rem; color: #f87171; line-height: 1.6;
}
.error-box ul { padding-left: 1.25rem; }

/* ── Completion note ── */
.completion-note {
  text-align: center; margin-top: 24px;
  font-size: .75rem; color: var(--dim); line-height: 1.6;
}

@media (max-width: 580px) {
  .card { padding: 36px 24px; }
  .field-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<a href="/" class="logo">
  <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
</a>

<div
  class="step-track"
  x-data="{ step: {{ $errors->any() ? 1 : 1 }} }"
  id="tracker"
>
  <div class="step-node" :class="{ active: step >= 1, done: step > 1 }">1</div>
  <div class="step-line" :class="{ done: step > 1 }"></div>
  <div class="step-node" :class="{ active: step >= 2, done: step > 2 }">2</div>
  <div class="step-line" :class="{ done: step > 2 }"></div>
  <div class="step-node" :class="{ active: step >= 3 }">3</div>
</div>

<div class="card">

  @if($errors->any())
    <div class="error-box">
      <ul>
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form
    method="POST"
    action="{{ route('user.onboarding.store') }}"
    x-data="{ step: {{ $errors->any() ? 1 : 1 }} }"
  >
    @csrf

    {{-- ━━━━━━━━━━━━━━━━━━━━ STEP 1 — YOUR BUSINESS ━━━━━━━━━━━━━━━━━━━━ --}}
    <div class="step-panel" x-show="step === 1" x-transition.duration.250ms>
      <div class="step-heading">
        <div class="step-eyebrow">Step 1 of 3</div>
        <h2>Your Business</h2>
        <p>Tell us about the company you're building with SEO AI Co™.</p>
      </div>

      <div class="field">
        <label class="field-label" for="business_name">Business Name <span style="color:#f87171">*</span></label>
        <input
          type="text" id="business_name" name="business_name"
          value="{{ old('business_name') }}"
          placeholder="e.g. Acme Digital Agency"
          required maxlength="255"
        >
      </div>

      <div class="field">
        <label class="field-label" for="website_url">Website URL</label>
        <input
          type="url" id="website_url" name="website_url"
          value="{{ old('website_url') }}"
          placeholder="https://yourdomain.com"
          maxlength="500"
        >
      </div>

      <div class="field">
        <label class="field-label" for="industry">Industry <span style="color:#f87171">*</span></label>
        <div class="select-wrap">
          <select id="industry" name="industry" required>
            <option value="" disabled {{ old('industry') ? '' : 'selected' }}>Select your industry</option>
            @foreach([
              'SEO & Digital Marketing',
              'Legal Services',
              'Healthcare & Medical',
              'Real Estate',
              'Home Services',
              'E-commerce & Retail',
              'SaaS & Technology',
              'Finance & Accounting',
              'Hospitality & Travel',
              'Education',
              'Other'
            ] as $opt)
              <option value="{{ $opt }}" {{ old('industry') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="btn-row">
        <button type="button" class="btn-primary" @click="step = 2; $el.closest('.step-track, form') && document.getElementById('tracker') && (document.getElementById('tracker').__x.$data.step = 2)">
          Continue →
        </button>
      </div>
    </div>

    {{-- ━━━━━━━━━━━━━━━━━━━━ STEP 2 — YOUR ROLE & SERVICES ━━━━━━━━━━━━━━━━━━━━ --}}
    <div class="step-panel" x-show="step === 2" x-transition.duration.250ms>
      <div class="step-heading">
        <div class="step-eyebrow">Step 2 of 3</div>
        <h2>Your Role & Services</h2>
        <p>Help us understand how you operate and who you serve.</p>
      </div>

      <div class="field-grid">
        <div class="field" style="margin-bottom:0">
          <label class="field-label" for="role_at_company">Your Role <span style="color:#f87171">*</span></label>
          <div class="select-wrap">
            <select id="role_at_company" name="role_at_company" required>
              <option value="" disabled {{ old('role_at_company') ? '' : 'selected' }}>Select your role</option>
              @foreach([
                'CEO / Founder',
                'CMO / Marketing Director',
                'SEO Manager / Specialist',
                'Agency Owner / Director',
                'Marketing Manager',
                'Consultant / Freelancer',
                'Other'
              ] as $opt)
                <option value="{{ $opt }}" {{ old('role_at_company') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="field" style="margin-bottom:0">
          <label class="field-label" for="primary_market">Primary Market <span style="color:#f87171">*</span></label>
          <div class="select-wrap">
            <select id="primary_market" name="primary_market" required>
              <option value="" disabled {{ old('primary_market') ? '' : 'selected' }}>Select market</option>
              @foreach([
                'Local (city / region)',
                'State / Province',
                'National',
                'International / Multi-location'
              ] as $opt)
                <option value="{{ $opt }}" {{ old('primary_market') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div style="margin-top:4px;">
        <span class="tags-label">Services You Offer <span style="color:var(--dim);text-transform:none;letter-spacing:0;font-size:.72rem">(select all that apply)</span></span>
        <div class="tags-grid">
          @foreach([
            'On-Page SEO',
            'Technical SEO',
            'Local SEO',
            'Link Building',
            'Content Strategy',
            'Programmatic SEO',
            'Enterprise SEO',
            'Reporting & Analytics'
          ] as $service)
            <label class="tag">
              <input
                type="checkbox" name="services[]" value="{{ $service }}"
                {{ in_array($service, old('services', [])) ? 'checked' : '' }}
              >
              <span>{{ $service }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <div class="btn-row">
        <button type="button" class="btn-back" @click="step = 1; document.getElementById('tracker').__x.$data.step = 1">
          ← Back
        </button>
        <button type="button" class="btn-primary" @click="step = 3; document.getElementById('tracker').__x.$data.step = 3">
          Continue →
        </button>
      </div>
    </div>

    {{-- ━━━━━━━━━━━━━━━━━━━━ STEP 3 — YOUR GOALS ━━━━━━━━━━━━━━━━━━━━ --}}
    <div class="step-panel" x-show="step === 3" x-transition.duration.250ms>
      <div class="step-heading">
        <div class="step-eyebrow">Step 3 of 3</div>
        <h2>Your Goals</h2>
        <p>What are you most focused on achieving right now?</p>
      </div>

      <div class="field">
        <label class="field-label" for="top_goal">Primary Goal <span style="color:#f87171">*</span></label>
        <div class="select-wrap">
          <select id="top_goal" name="top_goal" required>
            <option value="" disabled {{ old('top_goal') ? '' : 'selected' }}>Select your top goal</option>
            @foreach([
              'Rank higher in search results',
              'Drive more organic traffic',
              'Automate content at scale',
              'Improve local SEO presence',
              'Manage enterprise SEO operations',
              'Generate more qualified leads',
              'Other'
            ] as $opt)
              <option value="{{ $opt }}" {{ old('top_goal') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="field">
        <label class="field-label" for="biggest_challenge">Biggest Challenge <span style="color:#f87171">*</span></label>
        <textarea
          id="biggest_challenge" name="biggest_challenge"
          placeholder="Describe what's holding you back or what you're trying to solve..."
          required maxlength="2000"
        >{{ old('biggest_challenge') }}</textarea>
      </div>

      <div class="btn-row">
        <button type="button" class="btn-back" @click="step = 2; document.getElementById('tracker').__x.$data.step = 2">
          ← Back
        </button>
        <button type="submit" class="btn-primary">
          Complete Setup
        </button>
      </div>
    </div>

  </form>

  <p class="completion-note">
    Your information is kept private and used only to personalise your SEO AI Co™ experience.
  </p>
</div>

<script>
// Sync the external step tracker with the form's Alpine state
document.addEventListener('alpine:init', () => {
  // Re-sync tracker when form step changes by watching the form's Alpine data
  document.addEventListener('click', () => {
    setTimeout(() => {
      const form = document.querySelector('form');
      const tracker = document.getElementById('tracker');
      if (form && tracker && form._x_dataStack && tracker._x_dataStack) {
        const formStep = form._x_dataStack[0]?.step;
        if (formStep !== undefined) {
          tracker._x_dataStack[0].step = formStep;
        }
      }
    }, 10);
  });
});
</script>

</body>
</html>
