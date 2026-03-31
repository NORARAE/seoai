# Frontend Developer Handoff — SEOAIco

**Branch:** `frontend/plug007-ui-pass`  
**Repo:** `NORARAE/seoai`  
**Role:** Front-end UI / visual only  
**Date:** March 30, 2026  
**Updated:** March 31, 2026

---

## 1. Clone and branch setup

```bash
git clone https://github.com/NORARAE/seoai.git
cd seoai
git checkout frontend/plug007-ui-pass
```

All work goes in this branch only. **Never commit to `main`.**

---

## 2. Local dev setup

```bash
composer install
cp .env.example .env        # ask the repo owner for the real .env values
php artisan key:generate
npm install
npm run dev                 # Vite watch mode (compiles CSS + JS)
php artisan serve           # http://127.0.0.1:8000
```

> The database is SQLite by default. Run `php artisan migrate --seed` if asked to seed local test data.

---

## 3. Daily workflow

```bash
# Every morning before starting work:
git pull origin frontend/plug007-ui-pass

# After each completed task:
git add resources/  public/
git commit -m "style: describe what changed"
git push origin frontend/plug007-ui-pass
```

When the sprint is complete, open a Pull Request:  
**`frontend/plug007-ui-pass` → `main`**  
Do **not** merge your own PR. The repo owner will review and merge.

---

## 4. Task list — current sprint

Work through these in order. One commit per task.

---

### TASK 1 — `/book` page — visual brand alignment

**File:** `resources/views/public/book.blade.php`  
**URL:** `http://127.0.0.1:8000/book`

The booking page needs to match the dark bronze-gold design of the landing page.

- Dark background `#0c0c0c`
- Bronze-gold palette vars (`--gold: #c8a84b`, `--ivory: #ede8de`, `--muted: #a8a8a0`)
- DM Sans + Cormorant Garamond fonts (already loaded via Google Fonts)
- Heading hierarchy, spacing, and card treatment matching `landing.blade.php`
- Mobile responsive — test at 375px, 768px, 1200px

**Do not change:** any form field names, `fetch()`/AJAX logic, booking step flow, or JS conditionals.

---

### TASK 2 — `/book` booking modal — visual pass only

**File:** `resources/views/components/booking-modal.blade.php`  
**URL:** loads as a modal overlay on `/book`

Visual styling only:

- Step indicator dots — gold palette
- Buttons — match `.btn-primary` style from landing page
- Form field dark styling — dark inputs, light text, gold focus ring
- Typography consistency

**Strictly no logic changes.** The booking flow, payment redirect, and step conditionals are live and processing real payments.

---

### TASK 3 — `/book/confirmed` — confirmation page polish

**File:** `resources/views/public/booking-confirmed.blade.php`  
**URL:** `http://127.0.0.1:8000/book/confirmed`

- Dark background + bronze-gold palette
- Confirmation icon styled on-brand
- Typography and spacing polish
- Clear next-steps messaging, visually scannable

---

### TASK 4 — `/privacy` and `/terms` — brand consistency

**Files:**

- `resources/views/public/privacy.blade.php`
- `resources/views/public/terms.blade.php`

- Dark background, brand fonts
- Comfortable reading width (max ~680px centered)
- Gold accent on headings
- Muted body text (`--muted` or `--ivory`)

---

### TASK 5 — Landing page — mobile responsiveness audit

**File:** `resources/views/public/landing.blade.php`  
**URL:** `http://127.0.0.1:8000/`

Full responsive pass at 375px, 768px, 1024px, 1440px:

- Hero headline sizing
- Nav collapse/hamburger at mobile
- Pricing tier cards — stacking correctly on mobile
- CTA buttons — full width at small breakpoints

CSS/visual changes only. Do not touch Blade logic or Alpine.js conditionals.

---

### TASK 6 — `pending-approval` page — match auth theme

**File:** `resources/views/pending-approval.blade.php`  
**URL:** `http://127.0.0.1:8000/pending-approval`

- Same card gradient and border as auth pages
- Same font stack
- Copy is already in place — style it only

---

## 5. Design system reference

All pages must follow this system (already live on landing + auth pages):

```css
--bg:
    #080808 / #0c0c0c --gold: #c8a84b --gold-lt: #e2c97d --gold-dim: #9a7a30
        --ivory: #ede8de --muted: #a8a8a0
        --card: linear-gradient(160deg, #1f1f1f 0%, #161616 100%)
        --border: rgba(200, 168, 75, 0.18) Font
        stack: Headings: "Cormorant Garamond",
    serif (weight 300/400/600) Body/UI: "DM Sans",
    sans-serif (weight 300/400/500);
```

Reference files to match visually:

- `resources/views/public/landing.blade.php` — master design reference
- `resources/views/user-onboarding.blade.php` — card + form reference

---

## 6. Allowed work areas — UI only

These are the **only** paths you may edit:

| Path | What you may do |
|---|---|
| `resources/views/public/**` | Blade layout, HTML structure, CSS classes |
| `resources/views/components/booking-modal.blade.php` | Visual / CSS only — no JS logic |
| `resources/views/pending-approval.blade.php` | Full visual pass |
| `resources/css/app.css` | Global CSS additions |
| `resources/js/app.js` | Alpine.js UI interactions only — no fetch/API changes |
| `public/css/`, `public/fonts/`, `public/images/` | Static/compiled assets only |

### Booking pages — visual polish rules

The booking system (`/book`, `/book/confirmed`, the booking modal) recently received significant backend additions including a Stripe payment flow, Twilio SMS reminders, a reschedule token system, and lead scoring. You may adjust **visual styling only**. You must **not** change:

- Any `fetch()` call, API endpoint, or URL in booking JS
- Any form field `name` attributes or hidden inputs
- Any JS step-flow conditionals (`step === 2`, `isPaid`, etc.)
- Any Alpine.js `x-data` properties or event handlers related to booking
- Any validation rules or error message logic
- Any payment redirect or Stripe-related code paths
- Any pricing display values or consult type data
- Any route, controller method, or PHP logic

If you are unsure whether a change crosses this line — **stop and ask first.**

---

## 7. Off-limits — do not touch any of these

The following directories and files control live booking, payment, authentication, and CRM systems. **No exceptions.**

```
app/                              ← ALL backend PHP — no exceptions
  app/Http/Controllers/**         ← BookingController, BookingManageController, etc.
  app/Models/**                   ← Booking, Lead, User, ConsultType models
  app/Services/**                 ← TwilioSmsService, LeadScoringService, BookingRescheduleService
  app/Jobs/**                     ← queue jobs (SMS reminders, etc.)
  app/Mail/**                     ← transactional email
  app/Filament/**                 ← admin panel — do not touch
  app/Providers/**                ← service providers
  app/Http/Middleware/**          ← auth + UTM middleware

routes/**                         ← URL routing — recently extended, do not touch
config/**                         ← app config (includes config/booking.php, config/sms.php)
bootstrap/**                      ← app bootstrapping
database/migrations/**            ← schema recently extended — do not touch
database/seeders/**
deploy/**                         ← server provisioning scripts
storage/framework/**              ← cached framework files
.env                              ← secrets — never edit, never commit
.env.example                      ← do not change keys
```

### Specific systems that are off-limits — no exceptions

| System | Why it's off-limits |
|---|---|
| Booking logic (`BookingController`, `BookingManageController`) | Live booking creation and reschedule flow |
| All booking JS step flow and fetch calls | Controls real-money payment redirect |
| Payment / Stripe (`initiateCheckout`, `handlePaymentReturn`, `BookingWebhookController`) | Real money — broken = lost revenue |
| Booking pricing and consult type display | Any visual change may alter payment amounts |
| SMS / Twilio (`TwilioSmsService`, `SendBookingReminderJob`, `DispatchBookingReminders`) | Automated reminder system — live |
| Calendar sync (`GoogleCalendarService`) | Google Calendar event creation |
| Lead scoring (`LeadScoringService`, score/grade on Lead model) | CRM scoring — recently built |
| Booking token / reschedule (`BookingRescheduleService`, `/booking/manage/{token}`) | Self-service reschedule system — recently built |
| Auth / registration / approval (`Register.php`, `CaptureUtmParameters`, `AdminPanelProvider`) | User access control — broken = locked-out users |
| Booking status values (`pending`, `awaiting_payment`, `confirmed`, `cancelled`) | DB constraint recently fixed — must not change |
| Any `database/migrations/` file | Schema is stable and deployed to production |

---

## 8. Commit and PR rules

**Branch:** Always `frontend/plug007-ui-pass`
**Prefix:** `style:`, `ui:`, or `ux:` only
**Scope:** One page or task per commit
**Never mix** frontend visual changes with any backend file in the same commit

### Good commit examples

```
style: book page — dark bg + gold palette alignment
ui: booking modal — step indicator and button polish
style: booking-confirmed — premium dark confirmation layout
ux: landing — mobile responsive pass at 375px and 768px
style: privacy + terms — brand font and dark background
style: pending-approval — match auth card theme
```

### Bad commit examples (do not do these)

```
fix: update BookingController to handle new flow     ← backend — forbidden
chore: update .env with new key                      ← secrets — forbidden
refactor: clean up booking JS fetch calls            ← payment logic — forbidden
feat: add reschedule button to booking modal         ← routing/controller — forbidden
```

---

## 9. Questions?

Before touching anything you're unsure about — **ask the repo owner first.**

The backend is actively processing real bookings and payments. A broken deployment means lost revenue.

> **You are working on the UI layer only.**
> `resources/views/`, `resources/css/`, `resources/js/`, and `public/` assets.
> Everything else is off-limits.
