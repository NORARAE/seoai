# SEO AI Co™ — Brand System

> **INTERNAL REFERENCE ONLY.** Not for public distribution.  
> All agents, developers, and contributors must follow this document when modifying UI, copy, email, or any branded surface.

---

## 0. Agent Instruction Layer

**All AI agents must read and follow this document before modifying any UI component, copy block, email template, booking flow, onboarding page, or admin view.**

If a change conflicts with a rule in this document, the rule takes precedence. Do not introduce patterns not defined here without explicit approval. When in doubt, err on the side of restraint.

---

## 1. Canonical Brand Identity

### 1.1 Brand Name

| Context                      | Correct     | Incorrect                    |
| ---------------------------- | ----------- | ---------------------------- |
| First mention per page/email | SEO AI Co™  | SEOAIco, SEO AI Co, SEOAI Co |
| Subsequent mentions          | SEO AI Co   | SEOAIco                      |
| Code (slugs, env, routes)    | `seoaico`   | —                            |
| Display URL                  | seoaico.com | —                            |

**The display URL `seoaico.com` is the only context where the compact form is acceptable.** In all human-readable copy, use `SEO AI Co™` on first mention per surface.

### 1.2 Trademark Symbol Usage

- The ™ is rendered as a **superscript**, visually small
- It must NEVER be styled at the same font size as the brand name
- It must NEVER receive visual emphasis (no bold, no gold color, no animation)
- In HTML: `SEO AI Co<sup style="font-size:.55em;vertical-align:super">™</sup>` or `SEO AI Co™` using the Unicode character directly in plain text contexts
- In Blade/email templates: `SEO AI Co™` (Unicode ™ character) is acceptable; avoid `&trade;` in contexts where it may render inconsistently

### 1.3 Prohibited Forms

- `SEOAIco` — forbidden in all UI and copy (code/slug use only)
- `Seo AI Co` — forbidden (incorrect capitalisation)
- `SEO Ai Co` — forbidden
- `SEO AI co` — forbidden
- `SEO AI Co.` — forbidden (no period after Co)
- `SEO AI Co, LLC` or similar legal-entity suffixes — not for public/UI use

---

## 2. Logo and Text Rendering

### 2.1 Wordmark Structure

The wordmark is three visual segments rendered inline:

| Segment | Color                                  | Weight | Notes                          |
| ------- | -------------------------------------- | ------ | ------------------------------ |
| `SEO`   | Ivory (`#ede8de`)                      | Normal | Full presence                  |
| `AI`    | Gold (`#c8a84b`)                       | Normal | Signal of intelligence         |
| `co`    | Subdued (lower opacity ivory or muted) | Normal | Smaller size, visually recedes |

### 2.2 CSS Reference (nav/logo)

```css
.l-seo {
    color: #ede8de;
}
.l-ai {
    color: #c8a84b;
}
.l-co {
    color: rgba(237, 232, 222, 0.45);
    font-size: 0.78em;
}
```

### 2.3 Hierarchy Intent

- `SEO` establishes the domain
- `AI` establishes the differentiator (the system, the intelligence layer)
- `co` is the company marker — present but not competing for attention

The wordmark is a signal, not a logo. It does not need to be large, centered, or heroic to function. Small and precise is preferred.

### 2.4 Rules

- Do NOT bold any segment of the wordmark
- Do NOT add letterpress, shadow, or glow effects
- Do NOT split the wordmark across lines
- Do NOT resize `AI` to be larger than `SEO`
- Do NOT animate the wordmark (the constellation is the motion element, not the name)

---

## 3. Color System

### 3.1 Primary Palette

| Name          | Hex                    | Usage                                                |
| ------------- | ---------------------- | ---------------------------------------------------- |
| Gold          | `#c8a84b`              | Primary accent — CTAs, active states, key highlights |
| Gold Light    | `#e2c97d`              | Hover state of gold elements                         |
| Gold Dim      | `#9a7a30`              | Inactive/done state (e.g. progress dots)             |
| Ivory         | `#ede8de`              | Primary text — headings, labels                      |
| Deep Black    | `#080808`              | Primary background                                   |
| Surface       | `#0b0b0b`              | Card backgrounds, form fields                        |
| Surface Mid   | `#0d0c09`              | Panel backgrounds (booking panel)                    |
| Border        | `#1a1a1a`              | Default borders                                      |
| Border Active | `rgba(200,168,75,.30)` | Gold-tinted borders on premium sections              |
| Muted         | `#a8a8a0`              | Secondary text, labels, subtitles                    |
| Faint         | `#555` / `#666`        | Tertiary text, disabled states                       |
| Invisible     | `#3a3a35`              | Micro-lines (availability notes, qualifiers)         |

### 3.2 Gold Usage Rules

- Gold is reserved for **signals of value**: active states, CTAs, price markers, key badges
- Gold must NOT be used for decorative text
- Gold backgrounds must remain at low opacity (`.03`–`.08` range) on card surfaces
- Never use full gold `#c8a84b` as a paragraph text color
- The gold brand color in the authority block uses `rgba(200,168,75,.5)` — deliberately subdued

### 3.3 Background Hierarchy

```
Page background:   #080808
Section surfaces:  #0b0b0b
Panel surfaces:    #0d0c09
Card hover:        #0e0e0e
Reserved card:     #090909
```

---

## 4. Typography

### 4.1 Typefaces

| Role               | Font                     | Usage                                              |
| ------------------ | ------------------------ | -------------------------------------------------- |
| Display / Headings | Cormorant Garamond (400) | Section titles, modal titles, hero h1              |
| Body / UI          | DM Sans                  | All body text, labels, form fields, navigation     |
| No third typeface  | —                        | Do not introduce additional fonts without approval |

### 4.2 Scale Principles

- Headings use `clamp()` to scale proportionally; never hardcode a large px value for a heading
- Body text: `.88rem`–`.95rem` is the standard range
- Micro-text (qualifiers, labels, legal): `.66rem`–`.78rem`
- Do NOT increase font sizes to add emphasis — use color or letter-spacing instead

### 4.3 Letter Spacing

- Section labels / uppercase markers: `.12em`–`.16em`
- Micro-labels: `.08em`–`.10em`
- Body text: default (no explicit tracking)
- Do NOT use letter-spacing on headings

---

## 5. Email Branding

### 5.1 Sender Identity

| Field        | Value               |
| ------------ | ------------------- |
| Display name | `SEO AI Co™`        |
| From address | `hello@seoaico.com` |
| Reply-to     | `hello@seoaico.com` |

### 5.2 Subject Line Style

- Sentence case only: `Your market analysis is ready.`
- No ALL CAPS in subject lines
- No emojis in subject lines
- Maximum 60 characters preferred

### 5.3 Email Footer Signature Format

```
SEO AI Co™
hello@seoaico.com
seoaico.com
```

- The word "Programmatic AI SEO Systems" may follow on a separate line as a descriptor
- Do NOT add social media handles, taglines, or marketing copy to the transactional footer

### 5.4 Email Tone

- Instructional, not promotional
- One action per email
- No urgency framing ("Act now", "Don't miss", "Last chance")
- Sender tone is that of a system, not a salesperson

---

## 6. Tone and Positioning

### 6.1 Brand Tone Principles

| Principle    | Description                                                           |
| ------------ | --------------------------------------------------------------------- |
| Controlled   | Every word is intentional. No filler, no padding.                     |
| Precise      | Claims are specific and defensible. Avoid vague superlatives.         |
| Directive    | Tell the user what will happen, not what they should feel.            |
| Restrained   | Trust is built through understatement, not enthusiasm.                |
| System-first | Language reflects a structured deployment, not a service transaction. |

### 6.2 Forbidden Language Patterns

| Pattern                   | Example                                                | Reason                        |
| ------------------------- | ------------------------------------------------------ | ----------------------------- |
| Hype superlatives         | "revolutionary", "game-changing", "best-in-class"      | Undercuts premium positioning |
| Urgency fabrication       | "Limited time", "Act now", "Don't miss out"            | Discount/SaaS pattern         |
| SaaS package framing      | "Starter", "Pro", "Enterprise", "plan", "subscription" | Commodity framing             |
| Outcome promises          | "Guaranteed results", "You will rank #1"               | Legally and tonally wrong     |
| Casual informality        | "Hey!", "Super excited", "Awesome"                     | Tone mismatch                 |
| Discount framing          | "Save X%", "Special offer", "Free trial"               | Undermines price anchor       |
| "Best" / "Popular" badges | "Most Popular", "Best Value"                           | SaaS UI pattern, prohibited   |
| Generic service labels    | "consultation", "package", "audit" as a primary label  | Commodity framing             |
| First-person aggressive   | "We'll get you to the top!"                            | Promotional register          |

### 6.3 Approved Positioning Keywords

These phrases and terms reflect the system correctly:

**System:**

- "programmatic AI SEO system"
- "structured deployment"
- "system-level engagement"
- "entry point"
- "operational layer"

**Market:**

- "market position"
- "market control"
- "market clarity"
- "territory"
- "visibility"
- "active markets"

**Outcome:**

- "where your visibility is being lost"
- "take control of your market position"
- "reshape how your business is found"
- "establish market position"

**Qualification:**

- "qualified operators"
- "active markets only"
- "limited availability based on active markets"
- "access is limited per territory"

---

## 7. Reusable System Phrases

These phrases are approved for use across all surfaces. Use verbatim or minimally adapted.

### 7.1 Booking

| Context         | Phrase                                                                                   |
| --------------- | ---------------------------------------------------------------------------------------- |
| Hero subline    | "A focused consultation that shows you what's happening — and what to do next."          |
| Supporting copy | "No guesswork. No recycled strategy. Just clarity based on real signals."                |
| Decision frame  | "Choose how you want to approach your market."                                           |
| System anchor   | "Each session is a structured entry point — part of a system, not a standalone service." |
| Qualification   | "Qualified operators in active markets only."                                            |
| CTA note        | "Limited availability based on active markets"                                           |
| Territory micro | "Access is limited per territory."                                                       |

### 7.2 Card Labels (bookable sessions)

| Slug            | Current Name                | Outcome Line                                                                 |
| --------------- | --------------------------- | ---------------------------------------------------------------------------- |
| `discovery`     | Market Clarity Session      | "See exactly where your visibility is being lost — and why."                 |
| `audit`         | Strategic Direction Session | "Define the path to take control of your market position."                   |
| `agency-review` | Market Control Deployment   | "Deploy the system that reshapes how your business is found across markets." |

### 7.3 Qualification Lines (per card)

| Card                        | Qualification Text                                 |
| --------------------------- | -------------------------------------------------- |
| Market Clarity Session      | "For businesses seeking clarity before committing" |
| Strategic Direction Session | "For operators ready to move with direction"       |
| Market Control Deployment   | "For teams prepared to execute at scale"           |

### 7.4 Onboarding

| Context                     | Phrase                                                                                                                                                    |
| --------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Path note (single location) | "Single-market deployments are typically a starting point. The system is designed to scale — operators ready to grow often move into structured rollout." |
| Commitment psychology       | "This is not a short-term deployment. The system compounds over time."                                                                                    |
| R&D preline                 | "R&D Tax Credit"                                                                                                                                          |
| R&D framing                 | "Development-focused SEO systems may qualify for R&D tax credits."                                                                                        |

### 7.5 Emails (transactional)

| Email                         | Subject                               | Key CTA phrase                   |
| ----------------------------- | ------------------------------------- | -------------------------------- |
| Booking confirmation          | "Your session is confirmed — {type}"  | "View your confirmation"         |
| Onboarding step 2             | "What happens next with SEO AI Co™"   | "Review your onboarding details" |
| Onboarding step 3 (follow-up) | "Your market analysis is in progress" | —                                |

### 7.6 Landing Pages

| Context              | Phrase                               |
| -------------------- | ------------------------------------ |
| Authority block      | "Powered by SEO AI Co™"              |
| Authority descriptor | "Programmatic AI SEO Systems"        |
| Trust strip label    | "Platform integrations" (aria-label) |

---

## 8. Visual System Rules

### 8.0 CTA Behavior Rules

- **Primary CTA buttons that anchor within the page** (e.g. opening a booking panel) must NOT use a right-facing arrow `→`. A right-facing arrow implies navigation to another page and creates friction at the decision point.
- Preferred: no arrow indicator, or a subtle downward indicator `↓` if continuation is implied
- The button label must communicate outcome, not navigation. It should feel like a commitment, not a link.
- A micro-commitment line below the CTA (e.g. "You'll know exactly where you stand.") is approved and should be maintained. Style: very small, muted, italic, low opacity.

### 8.1 Animation Philosophy

- Motion must be **ambient and structural** — it communicates system activity, not excitement
- All public-facing animation uses SMIL `<animate>` on SVG elements — no CSS `@keyframes` on SVG attributes (browser inconsistency risk)
- Animation is fill-opacity and radius pulsing only — no translate, no rotate, no scale on branded elements
- Timing: 4s–18s cycles; staggered begin offsets (0.5s–9s) to prevent uniform pulse
- No animation on text or typography
- No entrance animations (fade-in, slide-in) on page elements
- The constellation SVG is the sole expressive motion element on public pages

### 8.2 Constellation SVG (book.blade.php)

- viewBox: `0 0 760 360`
- preserveAspectRatio: `xMidYMid slice`
- 4 orbit rings at r: 80, 148, 225, 320
- Center node: dual animate (fill-opacity + radius) + expanding halo ring
- All satellite nodes and lines have SMIL animate blocks with staggered begin values
- Gold only: `#c8a84b`. No other fill color in the SVG
- Background gradient: radialGradient from `stop-opacity:.08` to `0`
- This SVG must not be refactored to CSS animation without explicit approval

### 8.3 Icon Rules

- Inline SVG only for UI icons — no external icon libraries in public-facing pages
- Size: 12×12px for trust strip / micro-labels; 16×16px for card/form contexts
- Stroke only for directional icons (magnifier, pin, signal arcs); fill for area icons (bars)
- Color: `currentColor` — inherits from parent text color
- No icon labels that duplicate the text beside them (icon is decorative)
- No icon animations

### 8.4 Card Visual Hierarchy (booking modal)

Three tiers, no labels:

| Class        | Intent                | Visual Treatment                                                          |
| ------------ | --------------------- | ------------------------------------------------------------------------- |
| `.secondary` | Entry / free          | Base opacity `.7`, restored on hover/select                               |
| `.featured`  | Primary / high-value  | Gold left inset border (`inset 3px 0 0`), warm background                 |
| `.reserved`  | Partner / agency tier | Darkest surface (`#090909`), restrained border (`#1e1e1e`), authoritative |

Hover on all: `translateY(-1px)` + subtle `box-shadow`. No glow.

---

## 9. Anti-Drift Rules

These are hard prohibitions. Any change that introduces the following patterns must be reverted immediately.

### 9.1 Identity Drift

- ❌ Using `SEOAIco` in any UI label, heading, button, or email copy
- ❌ Capitalising `Co` fully as `CO`
- ❌ Adding `.`, `,`, or `®` after the brand name
- ❌ Splitting the wordmark into separate visual elements (e.g. `SEO` on one line, `AI Co` on another)

### 9.2 Visual Drift

- ❌ Enlarging the ™ symbol or giving it color/weight
- ❌ Introducing SaaS-style UI patterns (cards with "Most Popular" badges, toggle pricing, free trial banners)
- ❌ Adding shadows on text or headings (only structural shadows on elevated cards)
- ❌ Introducing color beyond the defined palette without approval
- ❌ Using CSS `@keyframes` on SVG `fill-opacity` or `r` attributes
- ❌ Adding entrance/exit transitions to page content blocks

### 9.3 Copy Drift

- ❌ Discount framing of any kind ("save", "special", "%, off")
- ❌ Urgency framing ("limited time", "expires", "act now")
- ❌ SaaS terminology ("plan", "tier" in public copy, "subscribe", "cancel anytime")
- ❌ Generic service naming ("consultation", "package", "audit" as primary card label)
- ❌ Benefit-list style bullets in hero or CTA sections
- ❌ "Best", "Popular", "Recommended" badges on booking cards

### 9.4 Structural Drift

- ❌ Adding new public-facing sections without approval
- ❌ Adding pricing callouts or discount comparisons to public pages
- ❌ Exposing internal tier names (`core`, `multi`, `agency`) in public copy
- ❌ Linking from public pages to admin or internal routes
- ❌ Auto-seeding booking types on deploy (manual re-seed required after BookingSeeder changes)

---

## 10. File Reference Map

| Surface                 | File                                                                         |
| ----------------------- | ---------------------------------------------------------------------------- |
| Public booking page     | `resources/views/public/book.blade.php`                                      |
| Booking modal / card UI | `resources/views/components/booking-modal.blade.php`                         |
| Booking session types   | `database/seeders/BookingSeeder.php` (manual re-seed required after changes) |
| Onboarding start        | `resources/views/public/onboarding-start.blade.php`                          |
| Onboarding done         | `resources/views/public/onboarding-done.blade.php`                           |
| Email: step 2           | `resources/views/emails/onboarding-step2.blade.php`                          |
| Email: step 3           | `resources/views/emails/onboarding-step3.blade.php`                          |
| Stripe tier config      | `config/services.php` (canonical product names in `stripe_tiers`)            |
| Lead intelligence       | `app/Models/OnboardingSubmission.php`                                        |
| Admin lead view         | `app/Filament/Resources/Leads/Pages/ViewLead.php`                            |
| Landing page            | `resources/views/public/landing.blade.php`                                   |

---

## 11. Stripe Product Naming (Internal)

Canonical names used in Stripe and admin — never exposed publicly:

| Tier     | Product Name                             | Activation Name                    |
| -------- | ---------------------------------------- | ---------------------------------- |
| `core`   | SEO AI Co™ Core System — Monthly         | SEO AI Co™ Core Activation         |
| `multi`  | SEO AI Co™ Multi-Market System — Monthly | SEO AI Co™ Multi-Market Activation |
| `agency` | SEO AI Co™ Partner System — Monthly      | SEO AI Co™ Partner Activation      |

Public copy must NEVER reference these tier keys or names directly.

---

---

## 13. Favicon

### 13.1 Current State

Favicon files present in `public/`:

- `favicon.svg` — SVG format (primary)
- `favicon.ico` — ICO fallback
- `favicon-16x16.png`, `favicon-32x32.png` — PNG sizes
- `apple-touch-icon.png` — 180×180
- `site.webmanifest` — web app manifest (references 16, 32, 180 sizes)

The current favicon is a constellation/node network mark in gold on black — consistent with the visual system.

### 13.2 Rules

- The favicon represents the brand mark (constellation), not the wordmark
- All public-facing blade templates must include all four favicon `<link>` tags (svg, ico, 16, 32, apple-touch, manifest)
- Do NOT use a text-based favicon — the mark is the signal

---

## 14. BIMI Readiness

**BIMI (Brand Indicators for Message Identification)** controls the logo icon displayed in a recipient's inbox (Gmail, Yahoo, Apple Mail supporting BIMI). It is **entirely separate from the site favicon**.

### 14.1 Prerequisites for BIMI

All of the following must be in place before a BIMI DNS record can be published:

| Requirement                     | Description                                                                      | Status                          |
| ------------------------------- | -------------------------------------------------------------------------------- | ------------------------------- |
| SPF record                      | `TXT` record on `seoaico.com` authorising sending servers                        | ⬜ Not verified in project docs |
| DKIM signing                    | Email signed with domain key (via mail provider, e.g. Resend, SendGrid)          | ⬜ Not verified in project docs |
| DMARC policy                    | `_dmarc.seoaico.com TXT` record with `p=quarantine` or `p=reject` (not `p=none`) | ⬜ Not verified in project docs |
| Verified Mark Certificate (VMC) | Required for Google BIMI — issued by DigiCert or Entrust                         | ⬜ Not present                  |
| SVG brand logo                  | Tiny PS (Portable/Secure) SVG of the brand mark                                  | ⬜ Needs creation               |
| BIMI DNS record                 | `_bimi.seoaico.com TXT v=BIMI1; l=https://...`                                   | ⬜ Not in place                 |

### 14.2 BIMI Checklist (when ready)

1. Verify SPF pass via `dig TXT seoaico.com` or MXToolbox
2. Verify DKIM signing is active for the sending domain
3. Set DMARC to `p=quarantine` minimum — confirm via `dig TXT _dmarc.seoaico.com`
4. Create SVG brand mark in BIMI-compliant format (Tiny PS subset)
5. Host SVG at a stable HTTPS URL (e.g. `https://seoaico.com/brand/bimi.svg`)
6. Optionally obtain VMC (required for Gmail BIMI)
7. Publish BIMI record: `_bimi.seoaico.com TXT "v=BIMI1; l=https://seoaico.com/brand/bimi.svg; a=<vmc_url>"`

### 14.3 Do NOT conflate

- Favicon ≠ inbox brand logo
- Changing favicon does NOT affect inbox display
- BIMI requires DNS-level verification, not just icon files

---

## 15. Version and Ownership

- Document version: 1.1 (updated April 3, 2026 — CTA rules, favicon, BIMI sections added)
- Original version: 1.0 (April 3, 2026)
- Owner: Internal development only
- Review: Required before any new public-facing surface is launched
