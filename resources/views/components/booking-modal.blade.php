{{-- Booking Modal — Alpine.js multi-step wizard --}}
{{-- Include Alpine CDN + Flatpickr CDN once on the page --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
[x-cloak]{display:none!important}
.bk-overlay{position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.72);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;pointer-events:none;transition:opacity .35s}
.bk-overlay.open{opacity:1;pointer-events:auto}
.bk-box{background:#111;border:1px solid #222;border-radius:12px;width:100%;max-width:640px;max-height:90vh;overflow-y:auto;padding:40px 36px;position:relative;transform:translateY(20px);transition:transform .35s}
.bk-overlay.open .bk-box{transform:none}
.bk-close{position:absolute;top:16px;right:16px;background:none;border:none;color:#8a8a8a;font-size:1.4rem;cursor:pointer;line-height:1;padding:4px 8px;transition:color .2s}
.bk-close:hover{color:#ede8de}
.bk-progress{display:flex;gap:8px;margin-bottom:28px}
.bk-dot{width:100%;height:3px;border-radius:2px;background:#222;transition:background .3s}
.bk-dot.active{background:var(--gold,#c8a84b)}
.bk-dot.done{background:var(--gold-dim,#9a7a30)}
.bk-title{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:400;color:#ede8de;margin-bottom:6px}
.bk-sub{font-size:.88rem;color:#a8a8a0;margin-bottom:24px;line-height:1.7}
.bk-back{background:none;border:none;color:#a8a8a0;font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;cursor:pointer;margin-bottom:16px;transition:color .2s;padding:0}
.bk-back:hover{color:#ede8de}

/* Step 1 — Consult type cards */
.bk-types{display:grid;gap:10px}
.bk-type{background:#0b0b0b;border:1px solid #1a1a1a;border-radius:8px;padding:18px 20px;cursor:pointer;transition:border-color .25s,background .25s;display:flex;justify-content:space-between;align-items:center}
.bk-type:hover{border-color:#333;background:#0f0f0f}
.bk-type.selected{border-color:var(--gold,#c8a84b);background:rgba(200,168,75,.04)}
.bk-type-name{font-size:.95rem;color:#ede8de;font-weight:400}
.bk-type-meta{display:flex;gap:14px;align-items:center}
.bk-type-dur{font-size:.76rem;color:#a8a8a0}
.bk-type-price{font-size:.82rem;color:var(--gold,#c8a84b);font-weight:500}
.bk-type-desc{font-size:.78rem;color:#777;margin-top:4px}

/* Step 2 — Date & Time */
.bk-datepicker{margin-bottom:20px}
.bk-datepicker input{width:100%;background:#0b0b0b;border:1px solid #1a1a1a;border-radius:6px;color:#ede8de;font-size:.92rem;padding:14px 16px;font-family:'DM Sans',sans-serif}
.bk-tz{font-size:.72rem;color:#666;margin-top:4px;margin-bottom:16px}
.bk-slots{display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px}
.bk-slot{background:#0b0b0b;border:1px solid #1a1a1a;border-radius:6px;padding:10px 4px;text-align:center;font-size:.84rem;color:#a8a8a0;cursor:pointer;transition:all .2s}
.bk-slot:hover{border-color:#333;color:#ede8de}
.bk-slot.picked{border-color:var(--gold,#c8a84b);color:var(--gold,#c8a84b);background:rgba(200,168,75,.04)}
.bk-no-slots{font-size:.88rem;color:#666;text-align:center;padding:24px 0}

/* Step 3 — Details form */
.bk-summary{background:#0b0b0b;border:1px solid #1a1a1a;border-radius:8px;padding:14px 18px;margin-bottom:20px;display:flex;gap:20px;flex-wrap:wrap}
.bk-sum-item{font-size:.82rem;color:#a8a8a0}
.bk-sum-item strong{color:#ede8de;font-weight:400}
.bk-field{margin-bottom:14px}
.bk-field label{display:block;font-size:.76rem;letter-spacing:.12em;text-transform:uppercase;color:#a8a8a0;margin-bottom:6px}
.bk-field input,.bk-field textarea{width:100%;background:#0b0b0b;border:1px solid #1a1a1a;border-radius:6px;color:#ede8de;font-size:.92rem;padding:12px 14px;font-family:'DM Sans',sans-serif;transition:border-color .2s}
.bk-field input:focus,.bk-field textarea:focus{outline:none;border-color:var(--gold,#c8a84b)}
.bk-field textarea{min-height:80px;resize:vertical}
.bk-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.bk-submit{width:100%;background:var(--gold,#c8a84b);color:#080808;font-size:.82rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;padding:16px;border:none;border-radius:6px;cursor:pointer;transition:background .3s,transform .2s;margin-top:8px}
.bk-submit:hover{background:var(--gold-lt,#e2c97d);transform:translateY(-1px)}
.bk-submit:disabled{opacity:.5;cursor:not-allowed;transform:none}

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

/* Error */
.bk-error{background:rgba(184,64,64,.1);border:1px solid rgba(184,64,64,.25);border-radius:6px;padding:12px 16px;color:#d46060;font-size:.84rem;margin-bottom:16px}

/* Mobile */
@media(max-width:600px){
  .bk-box{padding:28px 20px}
  .bk-row{grid-template-columns:1fr}
  .bk-slots{grid-template-columns:repeat(3,1fr)}
  .bk-summary{flex-direction:column;gap:8px}
}
</style>

<div x-data="bookingModal()" x-cloak
     @open-booking.window="open($event.detail); window._bkPending = undefined;"
     @keydown.escape.window="if (isOpen) close()">
  {{-- Overlay --}}
  <div class="bk-overlay" :class="{ open: isOpen }" @click.self="close()">
    <div class="bk-box">
      <button class="bk-close" @click="close()">&times;</button>

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
        <h3 class="bk-title">Choose Your Consult</h3>
        <p class="bk-sub">Select the type of session that fits your needs.</p>
        <div class="bk-types">
          @foreach(($types ?? collect()) as $ct)
          <div class="bk-type"
               :class="{ selected: selectedType === {{ $ct->id }} }"
               @click="selectType({{ $ct->id }}, {{ $ct->duration_minutes }}, {{ json_encode($ct->name) }}, {{ $ct->is_free ? 'true' : 'false' }})">
            <div>
              <div class="bk-type-name">{{ $ct->name }}</div>
              <div class="bk-type-desc">{{ $ct->description }}</div>
            </div>
            <div class="bk-type-meta">
              <span class="bk-type-dur">{{ $ct->formattedDuration() }}</span>
              <span class="bk-type-price">{{ $ct->formattedPrice() }}</span>
            </div>
          </div>
          @endforeach
        </div>
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
                @click="step = 3">Continue &rarr;</button>
      </div>

      {{-- ═══ STEP 3: Your details ═══ --}}
      <div x-show="step === 3">
        <button class="bk-back" @click="step = 2">&larr; Back</button>
        <h3 class="bk-title">Your Details</h3>
        <div class="bk-summary">
          <div class="bk-sum-item"><strong x-text="selectedTypeName"></strong></div>
          <div class="bk-sum-item" x-text="formatDateDisplay()"></div>
          <div class="bk-sum-item" x-text="formatTime(selectedTime)"></div>
        </div>
        <div class="bk-row">
          <div class="bk-field">
            <label for="bk-name">Full Name *</label>
            <input type="text" id="bk-name" x-model="form.name" required>
          </div>
          <div class="bk-field">
            <label for="bk-email">Email *</label>
            <input type="email" id="bk-email" x-model="form.email" required>
          </div>
        </div>
        <div class="bk-row">
          <div class="bk-field">
            <label for="bk-phone">Phone</label>
            <input type="tel" id="bk-phone" x-model="form.phone">
          </div>
          <div class="bk-field">
            <label for="bk-company">Company</label>
            <input type="text" id="bk-company" x-model="form.company">
          </div>
        </div>
        <div class="bk-field">
          <label for="bk-website">Website</label>
          <input type="url" id="bk-website" x-model="form.website" placeholder="https://…">
        </div>
        <div class="bk-field">
          <label for="bk-message">Message / Goals</label>
          <textarea id="bk-message" x-model="form.message" placeholder="Tell us what you'd like to discuss…"></textarea>
        </div>
        {{-- Add-ons (paid bookings only) --}}
        <div x-show="!selectedTypeIsFree">
          <p class="bk-enhance-title">Enhance Your Session <span style="color:#555;font-size:.72rem;letter-spacing:0;text-transform:none">— optional</span></p>
          <div class="bk-addon-grid">
            <div class="bk-addon-card" :class="{selected: addOns.includes('seo_audit')}" @click="toggleAddOn('seo_audit')">
              <div class="bk-addon-check" x-text="addOns.includes('seo_audit') ? '✓' : ''"></div>
              <div>
                <span class="bk-addon-name">SEO Audit <span class="bk-addon-price">+$150</span></span>
                <div class="bk-addon-desc">Full site audit delivered post-session</div>
              </div>
            </div>
            <div class="bk-addon-card" :class="{selected: addOns.includes('competitor_analysis')}" @click="toggleAddOn('competitor_analysis')">
              <div class="bk-addon-check" x-text="addOns.includes('competitor_analysis') ? '✓' : ''"></div>
              <div>
                <span class="bk-addon-name">Competitor Analysis <span class="bk-addon-price">+$100</span></span>
                <div class="bk-addon-desc">Top 5 competitor gap breakdown</div>
              </div>
            </div>
            <div class="bk-addon-card" :class="{selected: addOns.includes('thirty_day_plan')}" @click="toggleAddOn('thirty_day_plan')">
              <div class="bk-addon-check" x-text="addOns.includes('thirty_day_plan') ? '✓' : ''"></div>
              <div>
                <span class="bk-addon-name">30-Day SEO Plan <span class="bk-addon-price">+$250</span></span>
                <div class="bk-addon-desc">Custom roadmap with prioritised actions</div>
              </div>
            </div>
            <div class="bk-addon-card" :class="{selected: addOns.includes('strategy_followup')}" @click="toggleAddOn('strategy_followup')">
              <div class="bk-addon-check" x-text="addOns.includes('strategy_followup') ? '✓' : ''"></div>
              <div>
                <span class="bk-addon-name">Strategy Follow-up <span class="bk-addon-price">+$75</span></span>
                <div class="bk-addon-desc">30-min check-in call 2 weeks after session</div>
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

    form: { name: '', email: '', phone: '', company: '', website: '', message: '' },
    addOns: [],
    confirmation: { consult_type: '', date: '', time: '', duration: 0, meet_link: '' },

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
      document.body.style.overflow = 'hidden';
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
      this.form = { name: '', email: '', phone: '', company: '', website: '', message: '' };
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
        minDate: 'today',
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
        this.slots = data.slots || [];
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
