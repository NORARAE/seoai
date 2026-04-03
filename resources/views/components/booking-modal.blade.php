{{-- Booking Modal — Alpine.js multi-step wizard --}}
{{-- Include Alpine CDN + Flatpickr CDN once on the page --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
[x-cloak]{display:none!important}
.bk-overlay{position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.75);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;padding:16px;opacity:0;pointer-events:none;transition:opacity .35s}
.bk-overlay.open{opacity:1;pointer-events:auto}
.bk-box{background:#111;border:1px solid #222;border-radius:16px;width:100%;max-width:640px;max-height:92vh;overflow-y:auto;padding:40px 36px;position:relative;transform:translateY(20px);transition:transform .35s}
.bk-overlay.open .bk-box{transform:none}
.bk-close{position:absolute;top:14px;right:14px;background:rgba(255,255,255,.04);border:1px solid #222;border-radius:50%;width:36px;height:36px;color:#8a8a8a;font-size:1.2rem;cursor:pointer;line-height:1;padding:0;display:flex;align-items:center;justify-content:center;transition:color .2s,background .2s}
.bk-close:hover{color:#ede8de;background:rgba(255,255,255,.08)}
.bk-progress{display:flex;gap:8px;margin-bottom:28px;opacity:.5}
.bk-dot{width:100%;height:3px;border-radius:2px;background:#222;transition:background .3s}
.bk-dot.active{background:var(--gold,#c8a84b)}
.bk-dot.done{background:var(--gold-dim,#9a7a30)}
.bk-title{font-family:'Cormorant Garamond',serif;font-size:1.7rem;font-weight:400;color:#ede8de;margin-bottom:6px;line-height:1.15}
.bk-sub{font-size:.9rem;color:#a8a8a0;margin-bottom:24px;line-height:1.75}
.bk-back{background:none;border:none;color:#a8a8a0;font-size:.76rem;letter-spacing:.14em;text-transform:uppercase;cursor:pointer;margin-bottom:18px;transition:color .2s;padding:6px 0;min-height:36px}
.bk-back:hover{color:#ede8de}

/* Step 1 — Consult type cards */
.bk-types{display:grid;gap:12px}
.bk-type{background:#0b0b0b;border:1px solid #1a1a1a;border-radius:8px;padding:18px 20px;cursor:pointer;transition:border-color .25s,background .25s,transform .2s,box-shadow .2s;display:flex;justify-content:space-between;align-items:flex-start;position:relative}
.bk-type:hover{border-color:#2a2a2a;background:#0e0e0e;transform:translateY(-1px);box-shadow:0 4px 14px rgba(0,0,0,.2)}
.bk-type.selected{border-color:var(--gold,#c8a84b);background:rgba(200,168,75,.04)}
/* Secondary (free) — first-step card, readable but de-prioritised vs paid */
.bk-type.secondary{opacity:.88}
.bk-type.secondary:hover{opacity:1;border-color:#2a2a2a}
.bk-type.secondary.selected{opacity:1;border-color:var(--gold,#c8a84b);background:rgba(200,168,75,.04)}
/* Featured (audit) — gold accent */
.bk-type.featured{border-color:rgba(200,168,75,.30);background:rgba(200,168,75,.03);box-shadow:inset 3px 0 0 rgba(200,168,75,.45)}
.bk-type.featured:hover{border-color:rgba(200,168,75,.55);background:rgba(200,168,75,.06);box-shadow:inset 3px 0 0 rgba(200,168,75,.65),0 4px 18px rgba(0,0,0,.25);transform:translateY(-1px)}
.bk-type.featured.selected{border-color:var(--gold,#c8a84b);background:rgba(200,168,75,.08);box-shadow:inset 3px 0 0 var(--gold,#c8a84b)}
/* Reserved (partner/agency) — authoritative, restrained */
.bk-type.reserved{border-color:#1e1e1e;background:#090909}
.bk-type.reserved:hover{border-color:#2e2e2a;background:#0b0b09;transform:translateY(-1px);box-shadow:0 4px 16px rgba(0,0,0,.3)}
.bk-type.reserved.selected{border-color:var(--gold,#c8a84b);background:rgba(200,168,75,.04)}
.bk-type-badge{display:inline-flex;align-items:center;gap:5px;background:var(--gold,#c8a84b);color:#080808;font-size:.62rem;font-weight:600;letter-spacing:.10em;text-transform:uppercase;padding:3px 8px;border-radius:20px;margin-bottom:6px}
.bk-type-name{font-size:1rem;color:#ede8de;font-weight:400}
.bk-type-meta{display:flex;gap:14px;align-items:center;flex-shrink:0;margin-left:12px}
.bk-type-dur{font-size:.76rem;color:#a8a8a0}
.bk-type-price{font-size:.92rem;color:var(--gold,#c8a84b);font-weight:500}
.bk-type-desc{font-size:.78rem;color:#9a9a92;margin-top:4px;line-height:1.55}
.bk-type-microcopy{font-size:.72rem;color:#a8a8a0;margin-top:5px;font-style:italic}
.bk-type-qualify{font-size:.70rem;color:#7a7a72;margin-top:5px;letter-spacing:.01em;line-height:1.45}
.bk-avail-note{font-size:.66rem;color:#3a3a35;text-align:center;margin:18px 0 0;letter-spacing:.08em;text-transform:uppercase}
/* R&D tax microcopy */
.bk-rd-note{font-size:.70rem;color:#4a4a42;margin-top:20px;line-height:1.6;text-align:center}
.bk-rd-note a{color:#7a6a3a;text-decoration:none;border-bottom:1px solid #3a3020}
.bk-rd-note a:hover{color:#c8a84b;border-color:#c8a84b}

/* Anchor pricing tiers (non-bookable — visual / price anchoring only) */
.bk-anchor-label{font-size:.68rem;letter-spacing:.14em;text-transform:uppercase;color:#444;margin:20px 0 8px}
.bk-anchor-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.bk-anchor-card{background:#080808;border:1px solid #161616;border-radius:8px;padding:15px 16px;position:relative;overflow:hidden;cursor:default}
.bk-anchor-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,rgba(200,168,75,.2),rgba(200,168,75,.06))}
.bk-anchor-name{font-size:.80rem;color:#555;display:block;margin-bottom:3px}
.bk-anchor-price{font-size:.92rem;color:#4a4032;font-weight:500}
.bk-anchor-note{font-size:.68rem;color:#333;margin-top:4px;display:block;line-height:1.5}
.bk-anchor-label-tag{font-size:.60rem;letter-spacing:.08em;text-transform:uppercase;color:#3a3028;display:inline-block;margin-top:6px}
@media(max-width:480px){.bk-anchor-grid{grid-template-columns:1fr}}

/* Step 2 — Date & Time */
.bk-datepicker{margin-bottom:20px}
.bk-datepicker input{width:100%;background:#0b0b0b;border:1px solid #1a1a1a;border-radius:6px;color:#ede8de;font-size:.92rem;padding:14px 16px;font-family:'DM Sans',sans-serif}
.bk-tz{font-size:.72rem;color:#666;margin-top:4px;margin-bottom:16px}
.bk-slots{display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px}
.bk-slot{background:#0b0b0b;border:1px solid #1a1a1a;border-radius:8px;padding:13px 6px;min-height:44px;text-align:center;font-size:.84rem;color:#a8a8a0;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center}
.bk-slot:hover{border-color:#333;color:#ede8de}
.bk-slot.picked{border-color:var(--gold,#c8a84b);color:var(--gold,#c8a84b);background:rgba(200,168,75,.04)}
.bk-no-slots{font-size:.88rem;color:#666;text-align:center;padding:24px 0}

/* Step 3 — Details form */
.bk-summary{background:#0b0b0b;border:1px solid #1a1a1a;border-radius:8px;padding:14px 18px;margin-bottom:20px;display:flex;gap:20px;flex-wrap:wrap}
.bk-sum-item{font-size:.82rem;color:#a8a8a0}
.bk-sum-item strong{color:#ede8de;font-weight:400}
.bk-field{margin-bottom:18px}
.bk-field label{display:block;font-size:.76rem;letter-spacing:.12em;text-transform:uppercase;color:#a8a8a0;margin-bottom:7px}
.bk-field input,.bk-field textarea{width:100%;background:#0b0b0b;border:1px solid #1a1a1a;border-radius:8px;color:#ede8de;font-size:.95rem;padding:14px 16px;min-height:50px;font-family:'DM Sans',sans-serif;transition:border-color .2s,box-shadow .2s}
.bk-field input:focus,.bk-field textarea:focus{outline:none;border-color:var(--gold,#c8a84b);box-shadow:0 0 0 3px rgba(200,168,75,.08)}
.bk-field textarea{min-height:88px;resize:vertical}
.bk-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.bk-submit{width:100%;background:var(--gold,#c8a84b);color:#080808;font-size:.82rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;padding:16px;border:none;border-radius:12px;min-height:52px;cursor:pointer;transition:background .3s,transform .2s,box-shadow .2s;margin-top:8px}
.bk-submit:hover{background:var(--gold-lt,#e2c97d);transform:translateY(-1px);box-shadow:0 4px 20px rgba(200,168,75,.22)}
.bk-submit:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}

/* Step 4 — Confirmation */
.bk-check{font-size:2.4rem;margin-bottom:12px}
.bk-conf-title{font-family:'Cormorant Garamond',serif;font-size:1.8rem;color:#ede8de;margin-bottom:8px}
.bk-conf-details{display:flex;flex-direction:column;gap:8px;margin:20px 0}
.bk-conf-row{font-size:.88rem;color:#a8a8a0}
.bk-conf-row strong{color:#ede8de;font-weight:400}
.bk-meet-btn{display:inline-block;background:var(--gold,#c8a84b);color:#080808;font-size:.82rem;font-weight:500;letter-spacing:.12em;text-transform:uppercase;padding:14px 32px;border-radius:6px;text-decoration:none;margin:16px 0;transition:background .3s}
.bk-meet-btn:hover{background:var(--gold-lt,#e2c97d)}
.bk-gcal-link{font-size:.78rem;color:var(--gold,#c8a84b);text-decoration:none;display:inline-block;margin-top:4px}
.bk-gcal-link:hover{text-decoration:underline}
.bk-conf-note{font-size:.82rem;color:#666;margin-top:16px;line-height:1.6}

/* Add-on cards */
.bk-enhance-title{font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;color:#666;margin:20px 0 10px}
.bk-addon-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:18px}
.bk-addon-card{background:#0b0b0b;border:1px solid #1a1a1a;border-radius:8px;padding:12px 14px;cursor:pointer;transition:border-color .25s,background .25s;display:flex;align-items:flex-start;gap:10px}
.bk-addon-card:hover{border-color:#333;background:#0f0f0f}
.bk-addon-card.selected{border-color:var(--gold,#c8a84b);background:rgba(200,168,75,.05)}
.bk-addon-check{width:16px;height:16px;border:1px solid #333;border-radius:3px;flex-shrink:0;margin-top:2px;display:flex;align-items:center;justify-content:center;font-size:.7rem;color:var(--gold,#c8a84b);transition:background .2s,border-color .2s}
.bk-addon-card.selected .bk-addon-check{background:rgba(200,168,75,.15);border-color:var(--gold,#c8a84b)}
.bk-addon-name{font-size:.84rem;color:#ede8de;display:block}
.bk-addon-price{font-size:.76rem;color:var(--gold,#c8a84b)}
.bk-addon-desc{font-size:.72rem;color:#666;margin-top:2px}
@media(max-width:600px){.bk-addon-grid{grid-template-columns:1fr}}

/* Loading spinner */
.bk-spinner{display:inline-block;width:18px;height:18px;border:2px solid #333;border-top-color:var(--gold,#c8a84b);border-radius:50%;animation:bkspin .6s linear infinite;vertical-align:middle;margin-right:8px}
@keyframes bkspin{to{transform:rotate(360deg)}}

/* AI polish button */
.bk-ai-btn{background:none;border:1px solid #2a2a2a;border-radius:4px;color:#666;font-size:.64rem;letter-spacing:.08em;text-transform:uppercase;padding:3px 8px;cursor:pointer;transition:color .2s,border-color .2s;white-space:nowrap}
.bk-ai-btn:hover{color:var(--gold,#c8a84b);border-color:rgba(200,168,75,.3)}

/* Error */
.bk-error{background:rgba(184,64,64,.1);border:1px solid rgba(184,64,64,.25);border-radius:6px;padding:12px 16px;color:#d46060;font-size:.84rem;margin-bottom:16px}

/* Mobile */
@media(max-width:600px){
  .bk-box{padding:24px 18px;border-radius:12px}
  .bk-row{grid-template-columns:1fr}
  .bk-slots{grid-template-columns:repeat(3,1fr)}
  .bk-summary{flex-direction:column;gap:8px}
  .bk-submit{border-radius:12px}
  .bk-datepicker input,.bk-field input[type="date"]{font-size:1rem}
  .bk-type{padding:16px 16px;border-color:#242424}
  .bk-type.featured{border-color:rgba(200,168,75,.40)}
}

/* ── Panel / inline mode (used on /book) ───────────────────── */
.bk-overlay.bk-panel{position:static;background:transparent;backdrop-filter:none;z-index:auto;display:block;opacity:1;pointer-events:auto;padding:0 32px 80px;transition:none}
.bk-overlay.bk-panel .bk-box{max-width:640px;max-height:none;overflow-y:visible;overflow-x:visible;transform:none;transition:none;margin:0 auto;padding:48px 44px;background:#0d0c09;border:1px solid rgba(200,168,75,.12);border-top:2px solid rgba(200,168,75,.30);border-radius:12px;box-shadow:0 0 80px rgba(200,168,75,.03),0 24px 80px rgba(0,0,0,.4)}
@media(max-width:600px){.bk-overlay.bk-panel{padding:0 16px 60px}.bk-overlay.bk-panel .bk-box{border-radius:8px;padding:32px 22px}}
</style>

<div x-data="bookingModal()" x-cloak
     @open-booking.window="open($event.detail); window._bkPending = undefined;"
     @keydown.escape.window="if (isOpen && !disableOverlayDismiss) close()">
  {{-- Overlay --}}
  <div class="bk-overlay" :class="{ open: isOpen, 'bk-panel': disableOverlayDismiss }" @click.self="if (!disableOverlayDismiss) close()">
    <div class="bk-box">
      <button class="bk-close" x-show="!disableOverlayDismiss" @click="close()">&times;</button>

      {{-- Progress dots --}}
      <div class="bk-progress">
        <template x-for="i in totalSteps" :key="i">
          <div class="bk-dot" :class="{ active: step === i, done: step > i }"></div>
        </template>
      </div>

      {{-- Error display --}}
      <div class="bk-error" x-show="errorMsg" x-text="errorMsg" x-cloak></div>

      {{-- ═══ STEP 1: Choose consult type ═══ --}}
      <div x-show="step === 1">
        @if(!($panelMode ?? false))
        <h3 class="bk-title">Choose Your Session Type</h3>
        <p class="bk-sub">Reserve your spot &mdash; takes under 2 minutes.</p>
        @endif
        <div class="bk-types">
          <p style="font-size:.62rem;letter-spacing:.10em;text-transform:uppercase;color:rgba(168,168,160,.32);margin:0 0 4px">Each path represents a different level of system access.</p>
          <p style="font-size:.60rem;letter-spacing:.06em;text-transform:uppercase;color:rgba(168,168,160,.20);margin:0 0 14px">Access is structured &mdash; not open.</p>
          @foreach(($types ?? collect()) as $ct)
          <div class="bk-type {{ $ct->slug === 'audit' ? 'featured' : ($ct->slug === 'agency-review' ? 'reserved' : ($ct->is_free ? 'secondary' : '')) }}"
               :class="{ selected: selectedType === {{ $ct->id }} }"
               @click="selectType({{ $ct->id }}, {{ $ct->duration_minutes }}, {{ json_encode($ct->name) }}, {{ $ct->is_free ? 'true' : 'false' }})">
            <div style="flex:1;min-width:0">
              <div class="bk-type-name">{{ $ct->name }}</div>
              <div class="bk-type-desc">{{ $ct->description }}</div>
              @if($ct->slug === 'discovery')
              <div class="bk-type-qualify">For businesses evaluating their position before committing.</div>
              @elseif($ct->slug === 'audit')
              <div class="bk-type-qualify">For operators ready to move with structure and direction.</div>
              @elseif($ct->slug === 'agency-review')
              <div class="bk-type-qualify">For operators prepared for system-level control.</div>
              @endif
            </div>
            <div class="bk-type-meta">
              <span class="bk-type-dur">{{ $ct->formattedDuration() }}</span>
              <span class="bk-type-price">{{ $ct->formattedPrice() }}</span>
            </div>
          </div>
          @endforeach
        </div>

        {{-- ── Price anchors (visual only — price anchoring) ── --}}
        <p class="bk-anchor-label">Full-service engagements</p>
        <div class="bk-anchor-grid">
          <div class="bk-anchor-card">
            <span class="bk-anchor-name">Strategy Session</span>
            <span class="bk-anchor-price">$1,500–$2,500</span>
            <span class="bk-anchor-note">Deep analysis + prioritised growth roadmap</span>
            <span class="bk-anchor-label-tag">Contact to discuss</span>
          </div>
          <div class="bk-anchor-card">
            <span class="bk-anchor-name">Full Market Expansion System</span>
            <span class="bk-anchor-price">$5,000–$15,000+</span>
            <span class="bk-anchor-note">Complete build — done-for-you</span>
            <span class="bk-anchor-label-tag">Contact to discuss</span>
          </div>
        </div>

        <p class="bk-avail-note">Limited availability based on active markets</p>
        <p class="bk-avail-note" style="margin-top:4px">Access is limited per territory.</p>

        {{-- R&D tax microcopy --}}
        <p class="bk-rd-note">Development-focused SEO systems may qualify for R&amp;D tax credits.&nbsp; <a href="/rd-tax-credit">Learn more &rarr;</a></p>
      </div>

      {{-- ═══ STEP 2: Pick date & time ═══ --}}
      <div x-show="step === 2">
        <button class="bk-back" @click="step = 1">&larr; Back</button>
        <h3 class="bk-title">Pick a Date &amp; Time</h3>
        <p class="bk-sub" x-text="selectedTypeName + ' · ' + selectedDuration + ' min'"></p>
        <div class="bk-datepicker">
          <input type="text" x-ref="datepicker" placeholder="Choose a date…" readonly>
          <div class="bk-tz">Times shown in Pacific Time (PT)</div>
        </div>
        <div x-show="slotsLoading" style="text-align:center;padding:24px 0">
          <span class="bk-spinner"></span> Loading available times…
        </div>
        <div x-show="!slotsLoading && slots.length > 0" class="bk-slots">
          <template x-for="slot in slots" :key="slot">
            <button class="bk-slot"
                    :class="{ picked: selectedTime === slot }"
                    @click="selectedTime = slot; errorMsg = ''"
                    x-text="formatTime(slot)"></button>
          </template>
        </div>
        <div x-show="!slotsLoading && slots.length === 0 && selectedDate" class="bk-no-slots">
          No available times on this date. Try another day.
        </div>
        <button class="bk-submit" style="margin-top:20px"
                :disabled="!selectedDate || !selectedTime"
                @click="step = 3">Continue</button>
      </div>

      {{-- ═══ STEP 3: Your details ═══ --}}
      <div x-show="step === 3">
        <button class="bk-back" @click="step = 2">&larr; Back</button>
        <h3 class="bk-title">Your Details</h3>
        <p class="bk-sub" style="margin-bottom:14px">Last step — fill in your details below.</p>
        <div class="bk-summary">
          <div class="bk-sum-item"><strong x-text="selectedTypeName"></strong></div>
          <div class="bk-sum-item" x-text="formatDateDisplay()"></div>
          <div class="bk-sum-item" x-text="formatTime(selectedTime)"></div>
        </div>
        <div class="bk-row">
          <div class="bk-field">
            <label for="bk-name">Full Name *</label>
            <input type="text" id="bk-name" x-model="form.name" required
                   autocomplete="name" maxlength="255">
          </div>
          <div class="bk-field">
            <label for="bk-email">Email *</label>
            <input type="email" id="bk-email" x-model="form.email" required
                   autocomplete="email" maxlength="255">
          </div>
        </div>
        <div class="bk-row">
          <div class="bk-field">
            <label for="bk-phone">Phone</label>
            <input type="tel" id="bk-phone" x-model="form.phone"
                   autocomplete="tel" maxlength="50">
          </div>
          <div class="bk-field">
            <label for="bk-company">Company</label>
            <input type="text" id="bk-company" x-model="form.company"
                   autocomplete="organization" maxlength="255">
          </div>
        </div>
        <div class="bk-field">
          <label for="bk-website">Website</label>
          <input type="text" id="bk-website" x-model="form.website" placeholder="yoursite.com"
                 autocomplete="url" maxlength="500" @blur="prefixWebsite()">
        </div>
        <div class="bk-field" style="position:relative">
          <label for="bk-message" style="display:block">
            Message / Goals
          </label>
          <textarea id="bk-message" x-model="form.message" maxlength="1000" spellcheck="true"
                    placeholder="Tell us what you want to improve, where you feel stuck, and what growth would look like for your business."></textarea>
          <div class="bk-char-count" x-text="(form.message || '').length + ' / 1000'" style="font-size:.68rem;color:#555;text-align:right;margin-top:2px"></div>
        </div>
        {{-- Honeypot — invisible to real users, filled by bots --}}
        <div style="position:absolute;left:-9999px;top:-9999px;height:0;overflow:hidden" aria-hidden="true">
          <label for="bk-hp">Website</label>
          <input type="text" id="bk-hp" x-model="form.website_confirm" name="website_confirm" tabindex="-1" autocomplete="off">
        </div>
        {{-- Add-ons (paid bookings only) --}}
        <div x-show="!selectedTypeIsFree">
          <p class="bk-enhance-title">Session Enhancements</p>
          <div class="bk-addon-grid">
            <div class="bk-addon-card" :class="{selected: addOns.includes('seo_audit')}" @click="toggleAddOn('seo_audit')">
              <div class="bk-addon-check" x-text="addOns.includes('seo_audit') ? '✓' : ''"></div>
              <div>
                <span class="bk-addon-name">Technical Visibility Snapshot <span class="bk-addon-price">+$175</span></span>
                <div class="bk-addon-desc">Full-site crawl and signal analysis delivered within 48 hours of your session</div>
              </div>
            </div>
            <div class="bk-addon-card" :class="{selected: addOns.includes('competitor_analysis')}" @click="toggleAddOn('competitor_analysis')">
              <div class="bk-addon-check" x-text="addOns.includes('competitor_analysis') ? '✓' : ''"></div>
              <div>
                <span class="bk-addon-name">Competitive Gap Snapshot <span class="bk-addon-price">+$200</span></span>
                <div class="bk-addon-desc">Your top five market competitors mapped against your current position</div>
              </div>
            </div>
            <div class="bk-addon-card" :class="{selected: addOns.includes('thirty_day_plan')}" @click="toggleAddOn('thirty_day_plan')">
              <div class="bk-addon-check" x-text="addOns.includes('thirty_day_plan') ? '✓' : ''"></div>
              <div>
                <span class="bk-addon-name">Priority Action Brief <span class="bk-addon-price">+$250</span></span>
                <div class="bk-addon-desc">A structured document of your highest-impact actions, ready to deploy</div>
              </div>
            </div>
            <div class="bk-addon-card" :class="{selected: addOns.includes('strategy_followup')}" @click="toggleAddOn('strategy_followup')">
              <div class="bk-addon-check" x-text="addOns.includes('strategy_followup') ? '✓' : ''"></div>
              <div>
                <span class="bk-addon-name">Executive Follow-Up Session <span class="bk-addon-price">+$150</span></span>
                <div class="bk-addon-desc">A 30-minute review session two weeks after activation</div>
              </div>
            </div>
          </div>
        </div>

        <button class="bk-submit" :disabled="submitting || !form.name || !form.email" @click="submit()">
          <span x-show="submitting"><span class="bk-spinner"></span> Booking…</span>
          <span x-show="!submitting">Confirm Booking</span>
        </button>
      </div>

      {{-- ═══ STEP 4: Redirecting ═══ --}}
      <div x-show="step === 4" style="text-align:center;padding:32px 0">
        <div class="bk-check">&#10003;</div>
        <h3 class="bk-conf-title">Confirmed &mdash; redirecting&hellip;</h3>
        <p class="bk-conf-note" style="margin-top:12px">Taking you to your confirmation page.</p>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('bookingModal', () => ({
    isOpen: false,
    step: 1,
    totalSteps: 4,
    errorMsg: '',
    slotsLoading: false,
    submitting: false,

    selectedType: null,
    selectedDuration: 0,
    selectedTypeName: '',
    selectedTypeIsFree: false,
    selectedDate: '',
    selectedTime: '',
    slots: [],
    flatpickrInstance: null,

    form: { name: '', email: '', phone: '', company: '', website: '', website_confirm: '', message: '' },
    addOns: [],
    confirmation: { consult_type: '', date: '', time: '', duration: 0, meet_link: '' },

    // When true, overlay click and Escape will not close the modal (used on /book).
    disableOverlayDismiss: {{ json_encode($disableOverlayDismiss ?? false) }},

    // Available days (0=Sun..6=Sat) — supplied by the controller, never re-queried in the view.
    availableDays: @json($availableDays ?? []),


    init() {
      if (window._bkPending !== undefined) {
        this.$nextTick(() => {
          this.open(window._bkPending);
          window._bkPending = undefined;
        });
      }
    },

    open(preselect) {
      this.isOpen = true;
      if (!this.disableOverlayDismiss) {
        document.body.style.overflow = 'hidden';
      }
      if (typeof gtag === 'function') {
        gtag('event', 'open_booking_modal', { page_location: window.location.href });
      }
      // Fire-and-forget — record intent flag server-side
      fetch('/track/modal-open', {
        method: 'POST',
        credentials: 'include',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      }).catch(() => {});
      if (preselect) {
        this.selectType(preselect.id, preselect.duration, preselect.name, preselect.isFree ?? false);
      }
    },

    close() {
      this.isOpen = false;
      document.body.style.overflow = '';
      this.resetForm();
    },

    resetForm() {
      this.step = 1;
      this.selectedType = null;
      this.selectedDuration = 0;
      this.selectedTypeName = '';
      this.selectedDate = '';
      this.selectedTime = '';
      this.slots = [];
        this.form = { name: '', email: '', phone: '', company: '', website: '', website_confirm: '', message: '' };
      this.addOns = [];
      this.confirmation = { consult_type: '', date: '', time: '', duration: 0, meet_link: '' };
      this.errorMsg = '';
      if (this.flatpickrInstance) {
        this.flatpickrInstance.clear();
      }
    },

    selectType(id, duration, name, isFree = false) {
      this.selectedType = id;
      this.selectedDuration = duration;
      this.selectedTypeName = name;
      this.selectedTypeIsFree = isFree;
      this.errorMsg = '';
      if (typeof gtag === 'function') {
        gtag('event', 'select_booking_type', { booking_type: name, is_free: isFree });
      }
      this.step = 2;

      this.$nextTick(() => this.initDatepicker());
    },

    initDatepicker() {
      if (this.flatpickrInstance) {
        this.flatpickrInstance.destroy();
        this.flatpickrInstance = null;
      }
      const avail = this.availableDays;
      this.flatpickrInstance = flatpickr(this.$refs.datepicker, {
        minDate: new Date(Date.now() + 86400000),
        maxDate: new Date().fp_incr(60),
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'l, F j, Y',
        disableMobile: true,
        onReady: (_dates, _str, instance) => {
          // Keep Flatpickr calendar above the modal overlay (z-index 9000)
          instance.calendarContainer.style.zIndex = '99999';
          // Prevent altInput from stealing focus and silently opening the calendar
          if (instance.altInput) instance.altInput.blur();
        },
        disable: [
          function(date) {
            return !avail.includes(date.getDay());
          }
        ],
        onChange: (selectedDates, dateStr) => {
          this.selectedDate = dateStr;
          this.selectedTime = '';
          this.fetchSlots();
        }
      });
    },

    async fetchSlots() {
      this.slotsLoading = true;
      this.slots = [];
      this.errorMsg = '';
      try {
        const params = new URLSearchParams({
          date: this.selectedDate,
          consult_type_id: this.selectedType
        });
        const resp = await fetch(`/book/slots?${params}`);
        const data = await resp.json();
        // Filter client-side: strip any slot within 24 h of now (belt-and-suspenders)
        const cutoff = Date.now() + 24 * 60 * 60 * 1000;
        const [yr, mo, dy] = this.selectedDate.split('-').map(Number);
        this.slots = (data.slots || []).filter(slot => {
          const [h, m] = slot.split(':').map(Number);
          return new Date(yr, mo - 1, dy, h, m).getTime() > cutoff;
        });
      } catch (e) {
        this.errorMsg = 'Could not load available times. Please try again.';
      }
      this.slotsLoading = false;
    },

    async submit() {
      if (!this.form.name || !this.form.email) {
        this.errorMsg = 'Name and email are required.';
        return;
      }
      // Honeypot check
      if (this.form.website_confirm) {
        return;
      }
      this.submitting = true;
      this.errorMsg = '';
      try {
        const endpoint = this.selectedTypeIsFree ? '/book' : '/book/checkout';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
              || '{{ csrf_token() }}';
        const resp = await fetch(endpoint, {
          method: 'POST',
          credentials: 'include',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            consult_type_id: this.selectedType,
            preferred_date: this.selectedDate,
            preferred_time: this.selectedTime,
            ...this.form,
            add_ons: this.addOns,
          })
        });
        const data = await resp.json();
        if (resp.status === 419) {
          // Session expired — CSRF token is stale. Reload the page to get a fresh token.
          this.errorMsg = 'Your session has expired. The page will reload — please try again.';
          this.submitting = false;
          setTimeout(() => window.location.reload(), 2500);
          return;
        }
        if (!resp.ok) {
          this.errorMsg = data.message || 'Something went wrong. Please try again.';
          this.submitting = false;
          return;
        }
        // Paid booking — redirect to Stripe checkout
        if (data.checkout_url) {
          window.location.href = data.checkout_url;
          return;
        }
        // Free booking — flash step 4 then redirect to confirmed page
        this.confirmation = data.booking;
        this.step = 4;
        if (typeof gtag === 'function') {
          gtag('event', 'booking_completed', {
            booking_type: this.selectedTypeName,
            booking_id: data.booking.id,
            is_free: this.selectedTypeIsFree,
          });
        }
        setTimeout(() => {
          window.location.href = '/book/confirmed?booking=' + data.booking.id;
        }, 900);
      } catch (e) {
        this.errorMsg = 'Network error — please try again.';
      }
      this.submitting = false;
    },

    formatTime(t) {
      if (!t) return '';
      const [h, m] = t.split(':').map(Number);
      const ampm = h >= 12 ? 'PM' : 'AM';
      const h12 = h % 12 || 12;
      return h12 + ':' + String(m).padStart(2, '0') + ' ' + ampm;
    },

    formatDateDisplay() {
      if (!this.selectedDate) return '';
      const d = new Date(this.selectedDate + 'T12:00:00');
      return d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
    },

    prefixWebsite() {
      if (this.form.website && !/^https?:\/\//i.test(this.form.website)) {
        this.form.website = 'https://' + this.form.website;
      }
    },

    // ── AI assist stub ────────────────────────────────────────────────────────
    // Full AI rewrite requires a backend endpoint (e.g. POST /api/ai/polish)
    // connected to OpenAI or similar. This stub does basic text cleanup only.
    polishMessage() {
      if (!this.form.message || !this.form.message.trim()) return;
      this.form.message = this.form.message
        .trim()
        .replace(/\s{2,}/g, ' ')
        .replace(/([.!?])\s{0,}([A-Za-z])/g, '$1 $2');
    },

    toggleAddOn(slug) {
      const idx = this.addOns.indexOf(slug);
      if (idx === -1) {
        this.addOns.push(slug);
      } else {
        this.addOns.splice(idx, 1);
      }
    },

    googleCalendarLink() {
      if (!this.confirmation.date) return '#';
      const start = this.selectedDate.replace(/-/g, '') + 'T' + this.selectedTime.replace(':', '') + '00';
      const title = encodeURIComponent(this.confirmation.consult_type + ' — seoaico.com');
      return `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&dates=${start}/${start}&details=Booked+via+seoaico.com`;
    }
  }));
});
</script>
