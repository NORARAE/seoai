# Funnel QA & Conversion Review — Static Analysis Report

**Date:** 2025-07  
**Method:** Static analysis of Blade templates (no browser automation)  
**Scope:** `/` (landing) → `/onboarding/start` → `/onboarding/done` → `/book` + 3 email sequences  
**Commit reviewed:** `3f2fff1`

---

## JSON Report

```json
{
    "meta": {
        "method": "static_analysis",
        "commit": "3f2fff1",
        "pages_analyzed": [
            "/",
            "/onboarding/start",
            "/onboarding/done",
            "/book"
        ],
        "emails_analyzed": [
            "onboarding-received (+0h)",
            "onboarding-step2 (+24h)",
            "onboarding-step3 (+48h)"
        ],
        "screenshots": "not_available — requires Playwright/Puppeteer browser automation"
    },
    "scores": {
        "messaging_clarity": 7,
        "funnel_consistency": 6,
        "cta_strength": 7,
        "conversion_friction": 6,
        "email_sequence_alignment": 8,
        "overall": 6.8
    },
    "friction_points": [
        {
            "id": "FP-01",
            "severity": "high",
            "page": "/",
            "issue": "Landing page has 4+ distinct CTAs for the same action",
            "evidence": [
                "'Claim Your Territory'",
                "'Apply for Market Access'",
                "'Review the Structure'",
                "'Review Licensing Structure'",
                "'Request Licensing Access' (modal)"
            ],
            "impact": "Decision paralysis — user doesn't know which CTA is the real entry point"
        },
        {
            "id": "FP-02",
            "severity": "high",
            "page": "/ (section #how)",
            "issue": "Brand name leak: 'no SEOAIco branding anywhere' in step 03 description",
            "evidence": "Step 03 Deploy text: '...white-label from top to bottom, no SEOAIco branding anywhere.'",
            "impact": "Exposes the white-label infrastructure to end clients; inconsistent with SEO AI Co™ rebrand"
        },
        {
            "id": "FP-03",
            "severity": "high",
            "page": "cross-funnel",
            "issue": "The call has three different names across the funnel",
            "evidence": {
                "/onboarding/start H1": "Let's prepare your strategy session.",
                "/book H1": "This is a market opportunity session.",
                "/book CTA": "Reserve Your Market Opportunity Session",
                "email-step3 CTA": "Book a Strategy Session",
                "/onboarding/done CTA": "Prepare for your onboarding call"
            },
            "impact": "Users can't form a consistent mental model of what they're booking. Erodes trust between touchpoints."
        },
        {
            "id": "FP-04",
            "severity": "medium",
            "page": "/onboarding/start",
            "issue": "'All optional — skip anything you're unsure about.' in Step 3",
            "evidence": "done-trust module: 'All optional — skip anything you're unsure about.'",
            "impact": "Reduces perceived commitment signal at the final onboarding step. If access is selective and exclusive, asking users to 'skip' cheapens the qualification signal."
        },
        {
            "id": "FP-05",
            "severity": "medium",
            "page": "/onboarding/done",
            "issue": "Three competing next-step actions on confirmation page",
            "evidence": [
                "Primary CTA: 'Prepare for your onboarding call →' (/book)",
                "Secondary CTA: 'Review how the system works →' (/#how)",
                "Tertiary CTA: 'Secure a priority onboarding session →' (/book)"
            ],
            "impact": "Two CTAs pointing to /book with different framing ('onboarding call' vs 'priority session') creates confusion about whether these are the same or different offers"
        },
        {
            "id": "FP-06",
            "severity": "medium",
            "page": "/book",
            "issue": "No scarcity signal or qualification echo on /book page",
            "evidence": "Landing emphasizes 'one operator per market' / 'Before it's claimed.' Book page has no territory reference.",
            "impact": "Prospect who lands on /book directly loses the urgency context established on landing. Cold entry point is flat."
        },
        {
            "id": "FP-07",
            "severity": "low",
            "page": "/",
            "issue": "Audience split (agency vs. business owner) not resolved in hero or onboarding path",
            "evidence": "Contact form has 'You are a… Agency / Business / Both' but the hero speaks only to a single owner ('Your market. Your territory. One owner.')",
            "impact": "Agency operators will recognize themselves in the contact form but may feel the hero copy doesn't include them. Minor but relevant for conversion at scale."
        }
    ],
    "strengths": [
        {
            "id": "S-01",
            "page": "/onboarding/done ↔ email-step2",
            "observation": "Assessment criteria on done page exactly match email-step2 subject list",
            "evidence": {
                "done_page": [
                    "Territory availability",
                    "Existing competition",
                    "Site readiness",
                    "Expansion potential"
                ],
                "email_step2": [
                    "Territory Availability",
                    "Competitive Landscape",
                    "Site Readiness",
                    "Expansion Potential"
                ]
            },
            "impact": "Creates strong anticipatory coherence — user knows exactly what the email will say before it arrives"
        },
        {
            "id": "S-02",
            "page": "all emails",
            "observation": "Email footer is perfectly consistent across all 3 sequences",
            "evidence": "SEO AI Co™ · Programmatic AI SEO Systems / hello@seoaico.com on all 3",
            "impact": "Brand recall and deliverability trust"
        },
        {
            "id": "S-03",
            "page": "/",
            "observation": "Hero H1 animated sequence is high-authority and territorial",
            "evidence": "aria-label: 'Own your market. Capture your territory. Lock out competitors. One operator, one territory. Claim it before they do.'",
            "impact": "Best-in-funnel clarity and positioning. Sets strong frame for all downstream pages."
        },
        {
            "id": "S-04",
            "page": "/onboarding/start",
            "observation": "Step 2 qualifier copy is disarming and trust-positive",
            "evidence": "'Be as honest as you like. We use this to prepare your strategy — not to judge.'",
            "impact": "Reduces anxiety at the most vulnerable point of the form. Increases completion rate."
        },
        {
            "id": "S-05",
            "page": "/book",
            "observation": "Hero messaging shift from generic consulting to specific diagnostic is strong",
            "evidence": "'We help you see what's actually happening in your market — where you're losing visibility, where competitors are winning, and where real opportunity exists.'",
            "impact": "Specific outcome language outperforms 'strategy call' framing"
        }
    ],
    "recommendations": [
        {
            "id": "R-01",
            "priority": "P1",
            "addresses": "FP-02",
            "action": "Fix 'SEOAIco' brand leak in step 03 of How It Works section",
            "current": "white-label from top to bottom, no SEOAIco branding anywhere.",
            "suggested": "white-label from top to bottom, no SEO AI Co™ attribution on client-facing pages.",
            "effort": "1 line edit"
        },
        {
            "id": "R-02",
            "priority": "P1",
            "addresses": "FP-03",
            "action": "Standardize call name across all touchpoints",
            "options": [
                "Option A: 'Market Opportunity Session' everywhere (matches /book CTA — highest specificity)",
                "Option B: 'Strategy Session' everywhere (matches email-step3 and onboarding-start — highest existing coverage)",
                "Recommend Option A — it differentiates from generic 'strategy calls'"
            ],
            "touchpoints_to_update": [
                "onboarding-start H1: 'Let's prepare your market opportunity session.'",
                "onboarding-done primary CTA: 'Book your market opportunity session →'",
                "onboarding-done tertiary CTA: 'Secure a priority slot →'",
                "email-step3 CTA: 'Book Your Market Opportunity Session'"
            ],
            "effort": "4 targeted string replacements across 3 files"
        },
        {
            "id": "R-03",
            "priority": "P1",
            "addresses": "FP-01",
            "action": "Consolidate landing page CTAs to two: primary (Claim/Apply) + secondary (Review Structure)",
            "current_problem": "5 CTA variations pointing to the same or similar destinations",
            "suggested_hierarchy": {
                "primary": "'Claim Your Territory' → /onboarding/start (dominant action)",
                "secondary": "'Review the System' → /#how (for not-ready visitors)"
            },
            "remove": [
                "'Apply for Market Access' (redundant with Claim)",
                "'Review Licensing Structure' (redundant with Review the Structure)"
            ],
            "effort": "Medium — requires reviewing all CTA instances in landing.blade.php (~2900 lines)"
        },
        {
            "id": "R-04",
            "priority": "P2",
            "addresses": "FP-04",
            "action": "Replace 'All optional — skip anything' with trust-positive alternative in Step 3",
            "current": "All optional — skip anything you're unsure about.",
            "suggested": "All optional — only share what's ready. We'll cover the rest on the call.",
            "rationale": "Removes the word 'skip' (implies carelessness) while keeping opt-out permission intact",
            "effort": "1 line edit"
        },
        {
            "id": "R-05",
            "priority": "P2",
            "addresses": "FP-05",
            "action": "Consolidate /onboarding/done next actions to single primary path",
            "current_problem": "Primary and tertiary both link /book but with different names, implying different offers",
            "suggested": {
                "primary": "Single CTA: 'Book your market opportunity session →' (/book)",
                "remove": "Tertiary 'Secure a priority onboarding session' link (or make it a real upsell with distinct page/pricing)"
            },
            "effort": "Small edit to onboarding-done.blade.php"
        },
        {
            "id": "R-06",
            "priority": "P2",
            "addresses": "FP-06",
            "action": "Add brief scarcity echo to /book page above the booking form",
            "suggested_copy": "Markets are reviewed individually. One position per territory — if yours is still available, this session confirms it.",
            "placement": "Above the booking embed, after the authority block",
            "effort": "1 paragraph addition to book.blade.php"
        },
        {
            "id": "R-07",
            "priority": "P3",
            "addresses": "FP-07",
            "action": "Add a second hero accent line for agency operators on landing",
            "suggested": "Add a 3rd rotate variant to hero aria-label: 'Your entire client portfolio. One licensed infrastructure.'",
            "effort": "Minor — 1 line addition to hero JS rotation array"
        }
    ]
}
```

---

## Narrative Summary

### What's Working Well

The email-to-page criteria mirror (done page ↔ email step 2) is the single best consistency signal in the funnel. Users who submit the form arrive at a page that lists exactly what gets evaluated, and 24 hours later receive an email with the same four items named in a slightly more formal register. This is deliberate and trust-building.

The landing hero is the strongest page in the funnel. The rotating animated copy ("Own your market. Capture your territory. Lock out competitors.") establishes an unambiguous territorial frame that nothing after it quite matches in energy. The onboarding form and /book page are both quieter — intentionally — but they don't fully leverage the positioning equity built on the homepage.

### Critical Path Issues

**Call naming inconsistency** (FP-03 / R-02) is the most conversion-damaging problem. A user can move through: "strategy session" (onboarding form) → "onboarding call" (done page) → "market opportunity session" (/book) → "strategy session" (email 3). These all mean the same call. To the user, they appear to be different products at different stages of commitment. Standardizing to "Market Opportunity Session" solves this in four targeted edits.

**"SEOAIco" in step 03** (FP-02 / R-01) is a one-line fix that is currently leaking the white-label infrastructure brand to any prospect who reads How It Works. This is the easiest edit with the highest reputational impact.

**CTA proliferation on landing** (FP-01 / R-03) is the most effort-intensive fix. The landing page is a 2,900-line file with CTAs embedded in multiple scroll sections. A full audit and consolidation to two CTA types (primary: Claim, secondary: Review) would reduce visual noise and analysis paralysis.

### Email Sequence Assessment

| Email                 | Timing | Subject Focus                             | Alignment with Funnel                 | Issue                                             |
| --------------------- | ------ | ----------------------------------------- | ------------------------------------- | ------------------------------------------------- |
| `onboarding-received` | +0h    | Confirmation + territory under evaluation | ✅ Matches done page                  | None                                              |
| `onboarding-step2`    | +24h   | 4-criteria evaluation detail              | ✅ Exact mirror of done page list     | None                                              |
| `onboarding-step3`    | +48h   | Next steps + book call                    | ⚠️ CTA says "Book a Strategy Session" | Should say "Book Your Market Opportunity Session" |

### Scoring Rationale

| Dimension           | Score      | Reason                                                                                                                 |
| ------------------- | ---------- | ---------------------------------------------------------------------------------------------------------------------- |
| Messaging Clarity   | 7/10       | Landing and onboarding strong; /book solid; brand leak in How It Works                                                 |
| Funnel Consistency  | 6/10       | Call name drifts 3 ways; CTAs proliferate on landing; email 3 mismatches                                               |
| CTA Strength        | 7/10       | "Claim Your Territory" is best-in-class; "Reserve Your Market Opportunity Session" is verbose; done page CTAs are flat |
| Conversion Friction | 6/10       | "Skip" language; competing done-page CTAs; no qualification gate before /book                                          |
| Email Alignment     | 8/10       | Excellent done page ↔ email 2 mirror; consistent brand footer; email 3 CTA drift only                                  |
| **Overall**         | **6.8/10** |                                                                                                                        |

---

## Implementation Notes

### What's Automatable (Future)

Browser-automation testing (screenshots, form simulation, email capture) requires Playwright or Puppeteer pointed at the live server or a local `php artisan serve` instance. Dependencies:

```json
{
    "devDependencies": {
        "@playwright/test": "^1.44",
        "nodemailer": "^6.9" // for email capture from Resend test mode
    }
}
```

A Playwright script could:

1. Visit `http://127.0.0.1:8000/`
2. Screenshot hero + scroll to contact form
3. Fill onboarding form with test data
4. Capture done page
5. Visit `/book` and screenshot
6. Query Resend API for delivered emails in test mode

This wiring is ~100 lines of Playwright and does not require modifying production code.

### Prioritized Fix Order

```
P1 — 30 minutes total:
  ✱ R-01: Fix "SEOAIco" leak in How It Works (1 line)
  ✱ R-02: Standardize call name to "Market Opportunity Session" (4 files)

P2 — 1–2 hours:
  ✱ R-04: Replace "skip" language in onboarding step 3
  ✱ R-05: Consolidate done page CTAs to single /book link
  ✱ R-06: Add scarcity echo paragraph to /book

P3 — 2–4 hours:
  ✱ R-03: Full CTA audit and consolidation on landing page
  ✱ R-07: Add agency operator hero variant
```
