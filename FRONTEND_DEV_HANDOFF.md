# Frontend Developer Handoff — SEOAIco

**Branch:** `frontend/plug007-ui-pass`  
**Repo:** `NORARAE/seoai`  
**Role:** Front-end UI only  
**Date:** March 30, 2026

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
