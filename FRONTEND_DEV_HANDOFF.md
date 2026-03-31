# Frontend Developer Handoff — SEOAIco

**Branch:** `frontend/plug007-ui-pass`  
**Repo:** `NORARAE/seoai`  
**Role:** Front-end UI only  
**Date:** March 30, 2026  
**Updated:** March 31, 2026

---

## Your branch

```
git clone git@github.com:NORARAE/seoai.git
cd seoai
git checkout frontend/plug007-ui-pass
```

All work goes in this branch. **Never commit to `main`.**

---

## Local setup

```bash
composer install
cp .env.example .env        # ask for the real .env values separately
php artisan key:generate
npm install
npm run dev                 # asset compilation (watch mode)
php artisan serve           # http://127.0.0.1:8000
```

---

## TASK LIST — Current sprint

These are the specific pages and improvements needed. Work through them in order.

---

### TASK 1 — `/book` page — visual alignment with brand

**File:** `resources/views/public/book.blade.php`  
**URL:** `http://127.0.0.1:8000/book`

The booking page currently has minimal styling. It needs to match the dark bronze-gold design language of the landing page.

Needed:
- Dark background `#0c0c0c`, same as landing page
- Bronze-gold color palette vars (`--gold: #c8a84b`, `--ivory: #ede8de`, `--muted: #a8a8a0`)
- DM Sans + Cormorant Garamond fonts (already loaded via Google Fonts)
- Heading hierarchy, spacing, and card treatment matching `landing.blade.php`
- Mobile responsive — test at 375px, 768px, 1200px

**DO NOT change:** any form field names, JS fetch logic, or booking step flow.

---

### TASK 2 — `/book` page — booking modal visual pass

**File:** `resources/views/components/booking-modal.blade.php`  
**URL:** loads as modal on `/book`

Visual styling only on the booking modal:
- Step indicator dots — match gold palette
- Button styling — match `.btn-primary` style from landing page
- Form field dark styling — dark input backgrounds, light text, gold focus ring
- Typography consistency

**Strictly no logic changes.** The booking flow, payment redirect, and step conditionals are live in production.

---

### TASK 3 — `/book/confirmed` — confirmation page polish

**File:** `resources/views/public/booking-confirmed.blade.php`  
**URL:** `http://127.0.0.1:8000/book/confirmed`

This page shows after a booking is confirmed. It needs the same dark premium treatment.

Needed:
- Match dark background + bronze gold palette
- Confirmation checkmark/icon styled on-brand
- Typography and spacing polish
- Clear next-steps messaging, visually scannable

---

### TASK 4 — `/privacy` and `/terms` — brand consistency

**Files:**
- `resources/views/public/privacy.blade.php`
- `resources/views/public/terms.blade.php`

Both pages currently have basic styling. Update to match the brand:
- Dark background, brand fonts
- Comfortable reading width (max ~680px centered)
- Gold accent on headings
- Muted body text (`--muted` or `--ivory`)
- Sticky back-to-top or back link

---

### TASK 5 — Landing page — mobile responsiveness audit

**File:** `resources/views/public/landing.blade.php`  
**URL:** `http://127.0.0.1:8000/`

Do a full responsive pass at 375px, 768px, 1024px, 1440px:
- Hero headline sizing
- Nav collapse/hamburger at mobile (if not already present)
- Pricing tier cards — stacking on mobile
- Contact form — full width on mobile
- CTA buttons — full width on mobile at small breakpoints
- Any text that overflows or wraps awkwardly

Visual/CSS changes only. Do not touch Blade logic or Alpine.js conditionals.

---

### TASK 6 — `pending-approval` page — match auth theme

**File:** `resources/views/pending-approval.blade.php`  
**URL:** `http://127.0.0.1:8000/pending-approval`

This page shows after someone registers but hasn't been approved yet.
Make sure it uses the exact same bronze-gold design system as the auth pages:
- Same card gradient and border
- Same font stack
- Same ambient feel
- Clear, reassuring copy (copy already in place — just style it)

---

## Design system reference

All pages should follow this system (already in use on landing + auth pages):

```css
--bg: #080808 / #0c0c0c
--gold: #c8a84b
--gold-lt: #e2c97d
--gold-dim: #9a7a30
--ivory: #ede8de
--muted: #a8a8a0
--card: linear-gradient(160deg, #1f1f1f 0%, #161616 100%)
--border: rgba(200,168,75,.18)

Font stack:
  Headings: 'Cormorant Garamond', serif (weight 300/400/600)
  Body/UI:  'DM Sans', sans-serif (weight 300/400/500)
```

Reference files to match visually:
- `resources/views/public/landing.blade.php` — master design reference
- `resources/views/user-onboarding.blade.php` — card + form reference

---

## Allowed work areas — UI only

| Path | What you can do |
|---|---|
| `resources/views/public/**` | Blade templates — layout, structure, HTML, CSS |
| `resources/views/components/booking-modal.blade.php` | Visual styling only |
| `resources/views/pending-approval.blade.php` | Full visual pass |
| `resources/css/app.css` | Global CSS additions |
| `resources/js/app.js` | Alpine.js UI interactions only |
| `public/css/`, `public/fonts/` | Compiled/static assets only |

---

## STRICTLY FORBIDDEN — Do not touch any of these

```
app/Http/Controllers/**
app/Models/**
app/Services/**
app/Jobs/**
app/Mail/**
app/Notifications/**
database/migrations/**
database/seeders/**
config/**
routes/**
deploy/**
.env / .env.example
resources/views/filament/**
resources/css/filament/**
app/Filament/**
app/Providers/**
```

### Specific logic that is off-limits — no exceptions
| Area | Why |
|---|---|
| Stripe Checkout session creation | Live payment processing |
| Stripe webhook handler | Payment confirmation in production |
| Booking status values | Recently fixed DB constraint |
| `initiateCheckout()` in `BookingController` | Wires booking → Stripe |
| `handlePaymentReturn()` in `BookingController` | Confirms payment |
| Any booking modal JS conditionals/fetch calls | Controls live payment flow |
| Any `database/migrations/` file | Schema is stable |

---

## Commit and PR rules

- Work only on `frontend/plug007-ui-pass`
- Small, focused commits — one page/task at a time
- Prefix: `style:`, `ui:`, or `ux:`
- Do **not** mix frontend and backend changes in one commit
- When a task is complete, open a PR from `frontend/plug007-ui-pass` → `main`
- Do **not** merge your own PR

### Example good commits
```
style: book page — dark bg + gold palette alignment
ui: booking modal — step indicator and button polish
style: booking-confirmed — premium dark confirmation layout
ux: landing — mobile responsive pass at 375px and 768px
style: privacy + terms — brand font and dark background
```

---

## Questions?

Before touching anything you're unsure about — ask. The backend is sensitive.


---

## Your branch

```
git clone git@github.com:NORARAE/seoai.git
cd seoai
git checkout frontend/plug007-ui-pass
```

All work goes in this branch. **Never commit to `main`.**

---

## Local setup

```bash
composer install
cp .env.example .env        # ask for the real .env values separately
php artisan key:generate
npm install
npm run dev                 # asset compilation (watch mode)
php artisan serve           # http://127.0.0.1:8000
```

---

## Allowed work areas — UI only

| Path | What you can do |
|---|---|
| `resources/views/**` | Blade templates — layout, structure, HTML |
| `resources/css/**` | Stylesheets, Tailwind, custom CSS |
| `resources/js/**` | Alpine.js components, UI interactions, animations |
| `public/css/`, `public/js/`, `public/fonts/` | Compiled/static assets only |

### Also allowed (visual polish only)
- Typography, color, spacing, responsiveness
- Hover states, transitions, loading states
- Layout of existing pages (landing, `/book`, pricing, marketing pages)
- Dark mode consistency
- Mobile breakpoints

### Booking modal — LIMITED
You may adjust **visual styling only** on the booking modal:
- layout, spacing, color, typography
- step indicator dot styling
- button appearance

**You must NOT change any logic inside the booking modal** — no JS conditionals, no fetch calls, no form field changes, no step-flow changes. The booking + payment flow is live in production.

---

## STRICTLY FORBIDDEN — Do not touch any of these

These areas directly control a live booking and payment system. Any change risks breaking production revenue.

```
app/Http/Controllers/**       ← backend logic, DO NOT TOUCH
app/Models/**                 ← database models, DO NOT TOUCH
app/Services/**               ← business logic, DO NOT TOUCH
app/Jobs/**                   ← queue jobs, DO NOT TOUCH
app/Mail/**                   ← email, DO NOT TOUCH
app/Notifications/**          ← notifications, DO NOT TOUCH
database/migrations/**        ← RECENTLY FIXED — DO NOT TOUCH
database/seeders/**           ← DO NOT TOUCH
config/**                     ← environment config, DO NOT TOUCH
routes/**                     ← URL routing, DO NOT TOUCH
deploy/**                     ← server deploy scripts, DO NOT TOUCH
.env / .env.example           ← environment secrets, DO NOT TOUCH
```

### Specific logic that is off-limits — no exceptions
| Area | Why |
|---|---|
| Stripe Checkout session creation | Live payment processing |
| Stripe webhook handler | Payment confirmation in production |
| Booking status values (`pending`, `awaiting_payment`, `confirmed`) | Recently fixed DB constraint — must not change |
| `initiateCheckout()` in `BookingController` | Wires booking → Stripe |
| `handlePaymentReturn()` in `BookingController` | Confirms payment |
| `BookingWebhookController` | Processes Stripe webhook events |
| Any `database/migrations/` file | Database schema is stable, do not alter |

---

## Critical warning

> **The booking and payment system is live and actively processing real transactions.**
> Broken logic here means lost revenue and broken customer data.
> If you are unsure whether a change touches logic — **don't make it. Ask first.**

---

## Commit and PR rules

- Work only on `frontend/plug007-ui-pass`
- Small, focused commits — UI changes only, one area at a time
- Commit messages: `style: ...`, `ui: ...`, or `ux: ...` prefix
- Do **not** mix frontend and backend changes in one commit
- When ready for review, open a PR from `frontend/plug007-ui-pass` → `main`
- Do **not** merge your own PR — the repo owner will review and merge

### Example good commits
```
style: improve book page typography and spacing
ui: add transition to booking modal step indicator
ux: fix mobile padding on landing hero section
style: darken muted text for WCAG AA contrast compliance
```

### Example bad commits (do not do these)
```
fix: update BookingController to handle new flow     ← backend
chore: update .env with new key                      ← env file
refactor: clean up booking status logic              ← payment logic
```

---

## Pages you can work on

| URL | View file |
|---|---|
| `/` | `resources/views/public/landing.blade.php` (or similar) |
| `/book` | `resources/views/public/book.blade.php` — **visual only** |
| `/book/confirmed` | `resources/views/public/booking-confirmed.blade.php` |
| Any marketing/static pages | `resources/views/public/**` |

---

## Questions?

Before touching anything you're unsure about — ask. The backend is sensitive.
