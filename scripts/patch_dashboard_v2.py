#!/usr/bin/env python3
"""
Dashboard full design + flow overhaul (v2).

Changes:
  1. Add new CSS: exec-ctx-bar, exec-hero, next-move, your-plan sections
  2. Replace Project Identity Bar + Live Feedback Strip + System Status Strip
     → compact exec-ctx-bar (single line domain header)
  3. Replace #system-state hero → cleaner executive summary card
  4. Replace entire @if($isSystemView) block:
       - Fix @php/@endphp scope (INTERPRETATION outside @php)
       - Preserve INTERPRETATION / score-drivers section (verbatim)
       - Add WHAT TO DO NEXT section
       - Replace Level Rail + Level Cards + Premium Gate + broken Consult/AI nesting
         → unified YOUR PLAN section
       - Fix AI ADVISOR position (properly placed, not inside consult div)

Keeps untouched:
  - All existing CSS + JS
  - Scan history (@if($isScansView))
  - Reports section (@if($isReportsView))
  - DCM modal, flyout modals, @else onboarding
"""
import sys, os

BLADE = os.path.join(os.path.dirname(__file__),
                     '../resources/views/dashboard/customer-modern.blade.php')

def load(p):
    with open(p, 'r', encoding='utf-8') as f:
        return f.read()

def save(p, c):
    with open(p, 'w', encoding='utf-8') as f:
        f.write(c)

def cut(content, start_anchor, end_anchor, label):
    """Split content at start_anchor and end_anchor.
    Returns (before_start, between, after_end) or exits on failure."""
    if start_anchor not in content:
        print(f'  FAIL [{label}] start anchor not found')
        sys.exit(1)
    if end_anchor not in content:
        print(f'  FAIL [{label}] end anchor not found')
        sys.exit(1)
    pre, _, rest = content.partition(start_anchor)
    mid, _, post = rest.partition(end_anchor)
    print(f'  OK   [{label}]')
    return pre, mid, post

content = load(BLADE)

# ══════════════════════════════════════════════════════════════════════
# PATCH 1 — Fix orphaned CSS (injected after </style> last session)
#           + add new CSS for exec-ctx-bar, exec-hero, next-move, your-plan
#
# The previous patch placed Score Drivers + AI Advisor CSS *after* </style>
# (outside the <style> block). Fix: remove the premature </style>, keep all
# existing CSS inside the block, append new CSS, then close </style>.
# ══════════════════════════════════════════════════════════════════════

# The premature </style> is followed immediately by the orphaned CSS.
CSS_FIX_START_ANCHOR = '</style>\n  /* ── Score Drivers / Interpretation'
CSS_FIX_END_ANCHOR   = '  @media(max-width:640px){.ai-advisor-chips{flex-direction:column}.ai-advisor-chip{width:100%}.ai-advisor-head{flex-direction:column;align-items:flex-start}}\n@endpush'

if CSS_FIX_START_ANCHOR not in content:
    print('  FAIL [CSS-fix] start anchor not found')
    sys.exit(1)
if CSS_FIX_END_ANCHOR not in content:
    print('  FAIL [CSS-fix] end anchor not found')
    sys.exit(1)

pre_css, _, after_premature_close = content.partition(CSS_FIX_START_ANCHOR)
orphaned_and_tail, _, after_endpush = after_premature_close.partition('\n@endpush\n')

NEW_CSS_ONLY = """\

  /* ── Executive Context Bar ───────────────────────────────────────── */
  .exec-ctx-bar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;padding:10px 16px;border:1px solid rgba(200,168,75,.16);border-radius:12px;background:rgba(0,0,0,.28);margin-bottom:22px}
  .exec-ctx-domain{font-size:.95rem;font-weight:700;color:#f2edd8;letter-spacing:-.01em;display:flex;align-items:center;gap:8px}
  .exec-ctx-live{display:inline-block;width:7px;height:7px;border-radius:50%;background:#c8a84b;animation:pibPulse 2s ease-in-out infinite;flex-shrink:0}
  .exec-ctx-brand{color:#948c7c;font-size:.84rem;font-weight:400;margin-right:2px}
  .exec-ctx-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
  .exec-ctx-pill{font-size:.56rem;letter-spacing:.14em;text-transform:uppercase;color:#9a9082;padding:3px 8px;border-radius:6px;background:rgba(200,168,75,.06);border:1px solid rgba(200,168,75,.12)}
  .exec-ctx-btn{display:inline-flex;align-items:center;gap:6px;min-height:32px;padding:0 14px;border-radius:8px;background:#c6a85a;color:#1a1a1a;text-decoration:none;font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;transition:all .18s ease;box-shadow:0 4px 12px rgba(198,168,90,.18)}
  .exec-ctx-btn:hover{transform:translateY(-1px);filter:brightness(1.08)}
  .exec-ctx-btn-outline{display:inline-flex;align-items:center;gap:6px;min-height:32px;padding:0 14px;border-radius:8px;border:1px solid rgba(200,168,75,.26);background:transparent;color:rgba(200,168,75,.78);text-decoration:none;font-size:.62rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;transition:all .18s ease}
  .exec-ctx-btn-outline:hover{border-color:rgba(200,168,75,.5);background:rgba(200,168,75,.08)}

  /* ── Executive Hero ──────────────────────────────────────────────── */
  .exec-hero-shell{border:1px solid rgba(200,168,75,.26);border-radius:22px;background:linear-gradient(145deg,rgba(28,22,12,.98),rgba(10,9,7,.99) 66%),radial-gradient(circle at 8% 22%,rgba(200,168,75,.12),transparent 28%);padding:28px 28px 24px;box-shadow:0 24px 52px rgba(0,0,0,.44),0 0 0 1px rgba(200,168,75,.1) inset;position:relative;overflow:hidden}
  .exec-hero-shell::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.72),transparent)}
  .exec-hero-shell::after{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 50% -10%,rgba(200,168,75,.08),transparent 58%);pointer-events:none}
  .exec-hero-grid{display:grid;grid-template-columns:auto 1fr auto;gap:28px;align-items:start;position:relative;z-index:1}
  @media(max-width:840px){.exec-hero-grid{grid-template-columns:auto 1fr;gap:20px}.exec-action-col{grid-column:1/-1}}
  @media(max-width:520px){.exec-hero-grid{grid-template-columns:1fr}}
  .exec-score-col{display:flex;flex-direction:column;align-items:center;gap:10px;padding-top:4px}
  .exec-score-ring{position:relative;width:108px;height:108px;border-radius:50%;border:1.5px solid rgba(200,168,75,.35);background:radial-gradient(circle at 50% 38%,rgba(200,168,75,.2),rgba(14,11,8,.96) 70%);display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 0 36px rgba(200,168,75,.14),inset 0 1px 0 rgba(255,255,255,.04);flex-shrink:0}
  .exec-score-ring::before{content:'';position:absolute;inset:8px;border-radius:50%;border:1px solid rgba(200,168,75,.12)}
  .exec-score-ring::after{content:'';position:absolute;inset:-10px;border-radius:50%;background:radial-gradient(circle,rgba(200,168,75,.14),transparent 60%);z-index:-1;animation:scorePulse 3.2s ease-in-out infinite}
  .exec-score-ring.is-critical{border-color:rgba(196,80,80,.38);background:radial-gradient(circle at 50% 38%,rgba(196,80,80,.16),rgba(14,11,8,.96) 70%)}
  .exec-score-ring.is-critical::after{background:radial-gradient(circle,rgba(196,80,80,.14),transparent 60%)}
  .exec-score-ring.is-watching{border-color:rgba(214,177,95,.36)}
  .exec-score-ring.is-strong{border-color:rgba(106,175,144,.34);background:radial-gradient(circle at 50% 38%,rgba(106,175,144,.14),rgba(14,11,8,.96) 70%)}
  .exec-score-ring.is-strong::after{background:radial-gradient(circle,rgba(106,175,144,.14),transparent 60%)}
  .exec-score-num{font-size:2.6rem;font-weight:700;line-height:1;color:#f5eed8;letter-spacing:-.04em}
  .exec-score-lbl{font-size:.5rem;letter-spacing:.18em;text-transform:uppercase;color:#9a9080;margin-top:2px}
  .exec-score-no{font-size:.8rem;color:#7a7266;text-align:center;padding:0 8px;line-height:1.3}

  /* Interpretation column */
  .exec-interp-col{display:flex;flex-direction:column;gap:14px;min-width:0}
  .exec-interp-question{font-size:.58rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.58);margin-bottom:4px}
  .exec-interp-answer{font-size:1.05rem;line-height:1.58;color:#e8dfcc;font-weight:500;max-width:38rem}
  .exec-interp-answer strong{color:#f5e6b8;font-weight:600}
  .exec-bottleneck-block{border:1px solid rgba(200,168,75,.18);border-radius:13px;background:rgba(0,0,0,.24);padding:13px 15px}
  .exec-bottleneck-label{font-size:.54rem;letter-spacing:.2em;text-transform:uppercase;color:#d8c58f;margin-bottom:5px}
  .exec-bottleneck-copy{font-size:.86rem;line-height:1.58;color:#d8d0bc}
  .exec-trust-line{font-size:.6rem;letter-spacing:.1em;color:#6a6254;margin-top:2px}

  /* Action column */
  .exec-action-col{display:flex;flex-direction:column;gap:10px;min-width:172px}
  .exec-cta-primary{display:flex;align-items:center;justify-content:center;min-height:44px;padding:0 20px;border-radius:12px;background:#c6a85a;color:#1a1a1a;text-decoration:none;font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;box-shadow:0 12px 24px rgba(198,168,90,.22),0 0 0 1px rgba(255,255,255,.14) inset;transition:all .2s ease;text-align:center;border:none;cursor:pointer}
  .exec-cta-primary:hover{transform:translateY(-2px);box-shadow:0 18px 32px rgba(198,168,90,.38)}
  .exec-cta-secondary{display:flex;align-items:center;justify-content:center;border:1px solid rgba(200,168,75,.28);background:transparent;color:#d9cfa9;text-decoration:none;font-size:.68rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;min-height:40px;padding:0 18px;border-radius:10px;transition:all .2s ease;text-align:center}
  .exec-cta-secondary:hover{border-color:rgba(200,168,75,.52);background:rgba(200,168,75,.08)}
  .exec-cta-stat{font-size:.56rem;letter-spacing:.12em;text-transform:uppercase;color:#5e5648;text-align:center;line-height:1.4}
  .exec-cta-stat.positive{color:#6aaf90}
  .exec-cta-stat.negative{color:#d47878}

  /* ── Section common headers ──────────────────────────────────────── */
  .dash-section-label{font-size:.58rem;letter-spacing:.26em;text-transform:uppercase;color:rgba(200,168,75,.58);margin-bottom:5px}
  .dash-section-heading{font-size:1rem;font-weight:600;color:#ede8de;line-height:1.3;margin-bottom:4px}
  .dash-section-subhead{font-size:.82rem;line-height:1.58;color:#9a8f80;max-width:44rem}

  /* ── What to Do Next ─────────────────────────────────────────────── */
  .next-move-shell{border:1px solid rgba(200,168,75,.2);border-radius:20px;background:linear-gradient(155deg,#161209,#0e0b07 72%);padding:22px;box-shadow:0 16px 36px rgba(0,0,0,.3),inset 0 1px 0 rgba(255,255,255,.03)}
  .next-move-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:18px}
  @media(max-width:640px){.next-move-row{grid-template-columns:1fr}}
  .nm-card{border:1px solid rgba(200,168,75,.15);border-radius:14px;background:linear-gradient(155deg,#17130c,#0e0b08 70%);padding:16px 18px;display:flex;flex-direction:column;gap:10px;transition:transform .2s ease,box-shadow .2s ease,border-color .22s ease}
  .nm-card:hover{transform:translateY(-2px);box-shadow:0 10px 22px rgba(0,0,0,.3)}
  .nm-card.nm-primary{border-color:rgba(200,168,75,.3);background:linear-gradient(155deg,#1e1a0e,#120f08 68%);box-shadow:0 0 0 1px rgba(200,168,75,.12) inset}
  .nm-card-kicker{font-size:.54rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.62)}
  .nm-card-title{font-size:.9rem;font-weight:600;color:#ece3cc;line-height:1.45}
  .nm-card-rationale{font-size:.78rem;line-height:1.55;color:#9a9080}
  .nm-card-action{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;border:1px solid rgba(200,168,75,.3);background:rgba(200,168,75,.09);color:#d9c988;font-size:.62rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;align-self:flex-start;transition:all .18s ease;margin-top:auto}
  .nm-card-action:hover{border-color:rgba(200,168,75,.54);background:rgba(200,168,75,.17)}
  .nm-card-action-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;border:1px solid rgba(200,168,75,.3);background:rgba(200,168,75,.09);color:#d9c988;font-size:.62rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;align-self:flex-start;transition:all .18s ease;margin-top:auto}
  .nm-card-action-btn:hover{border-color:rgba(200,168,75,.54);background:rgba(200,168,75,.17)}

  /* ── Your Plan ───────────────────────────────────────────────────── */
  .your-plan-shell{border:1px solid rgba(200,168,75,.22);border-radius:22px;background:linear-gradient(155deg,#17130a,#0e0b07 72%);padding:24px;box-shadow:0 20px 42px rgba(0,0,0,.34),inset 0 1px 0 rgba(255,255,255,.03)}
  .plan-rail-row{display:flex;align-items:center;gap:0;margin:16px 0 20px;padding:0 2px}
  .plan-level-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
  @media(max-width:680px){.plan-level-row{grid-template-columns:1fr}}
  .plan-current-card{border:1px solid rgba(106,175,144,.24);border-radius:16px;background:linear-gradient(155deg,#0f1a12,#090d0b 70%);padding:18px;position:relative;overflow:hidden}
  .plan-current-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(106,175,144,.38),transparent)}
  .plan-current-kicker{font-size:.55rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(106,175,144,.72);margin-bottom:7px}
  .plan-current-name{font-size:.94rem;font-weight:600;color:#d6edd8;margin-bottom:5px}
  .plan-current-desc{font-size:.78rem;line-height:1.52;color:#8ab090;margin-bottom:14px}
  .plan-included-list{display:flex;flex-direction:column;gap:7px}
  .plan-included-item{display:flex;align-items:flex-start;gap:8px;font-size:.78rem;line-height:1.45;color:#b8d4bc}
  .plan-included-icon{flex-shrink:0;color:#6aaf90;margin-top:1px}
  .plan-view-report-btn{display:inline-flex;align-items:center;gap:6px;min-height:34px;padding:0 14px;border-radius:8px;border:1px solid rgba(106,175,144,.32);background:rgba(106,175,144,.1);color:#9fd4b8;font-size:.62rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;transition:all .18s ease;margin-top:14px}
  .plan-view-report-btn:hover{border-color:rgba(106,175,144,.56);background:rgba(106,175,144,.17)}
  .plan-next-card{border:1px solid rgba(200,168,75,.26);border-radius:16px;background:linear-gradient(155deg,#1d1910,#120f08 70%);padding:18px;position:relative;overflow:hidden}
  .plan-next-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.44),transparent)}
  .plan-next-kicker{font-size:.55rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.68);margin-bottom:7px}
  .plan-next-name{font-size:.94rem;font-weight:600;color:#ede8de;margin-bottom:3px}
  .plan-next-price{font-size:1.05rem;font-weight:700;color:#c6a85a;letter-spacing:-.02em;margin-bottom:8px}
  .plan-next-desc{font-size:.78rem;line-height:1.52;color:#b0a48c;margin-bottom:14px}
  .plan-next-unlocks{display:flex;flex-direction:column;gap:7px;margin-bottom:16px}
  .plan-next-unlock-item{display:flex;align-items:flex-start;gap:8px;font-size:.76rem;line-height:1.45;color:#d6c887}
  .plan-next-unlock-icon{flex-shrink:0;color:rgba(200,168,75,.62);margin-top:1px}
  .plan-unlock-cta{display:flex;align-items:center;justify-content:center;min-height:40px;padding:0 18px;border-radius:10px;background:#c6a85a;color:#1a1a1a;text-decoration:none;font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;transition:all .2s ease;box-shadow:0 8px 18px rgba(198,168,90,.2);border:none;cursor:pointer;width:100%}
  .plan-unlock-cta:hover{transform:translateY(-2px);box-shadow:0 14px 28px rgba(198,168,90,.36)}
  .plan-all-unlocked-card{border:1px solid rgba(200,168,75,.2);border-radius:16px;background:linear-gradient(155deg,#1a180e,#110f08 70%);padding:18px}
  .plan-all-unlocked-kicker{font-size:.55rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.65);margin-bottom:7px}
  .plan-all-unlocked-title{font-size:.94rem;font-weight:600;color:#e8d88a;margin-bottom:8px}
  .plan-all-unlocked-desc{font-size:.78rem;line-height:1.55;color:#6aaf90}
  .plan-consult-row{border-top:1px solid rgba(200,168,75,.1);margin-top:18px;padding-top:16px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
  .plan-consult-copy{font-size:.78rem;color:#9a9080;line-height:1.45}
  .plan-consult-btn{display:inline-flex;align-items:center;gap:6px;min-height:34px;padding:0 14px;border-radius:8px;border:1px solid rgba(200,168,75,.22);background:transparent;color:rgba(200,168,75,.7);text-decoration:none;font-size:.62rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;transition:all .18s ease}
  .plan-consult-btn:hover{border-color:rgba(200,168,75,.42);background:rgba(200,168,75,.06)}"""

# Reconstruct: move premature </style> to AFTER all CSS, then close the @push stack
# pre_css   = everything before the premature </style>
# orphaned_and_tail = Score Drivers CSS + AI Advisor CSS (was outside <style>)
# after_endpush = everything after @endpush (the real HTML content)
content = (
    pre_css
    + '\n  /* ── Score Drivers' + orphaned_and_tail
    + '\n' + NEW_CSS_ONLY
    + '\n</style>\n@endpush\n'
    + after_endpush
)
print('  OK   [CSS fix: moved </style>, added new sections]')


# ══════════════════════════════════════════════════════════════════════
# PATCH 2 — Replace Project Identity Bar + Live Feedback Strip +
#           System Status Strip  →  compact exec-ctx-bar
# ══════════════════════════════════════════════════════════════════════
STRIPS_START = '    {{-- Project Identity Bar --}}\n    @if($projectDomain)\n    <div class="proj-identity-bar"'
STRIPS_END   = '    <div class="dashboard-primary-flow {{ $isScansView ? \'is-scans-view\' : \'\' }} {{ $isReportsView ? \'is-reports-view\' : \'\' }}">'

NEW_EXEC_CTX_BAR = """\
    {{-- Domain Context Bar --}}
    @if($projectDomain)
    <div class="exec-ctx-bar" role="banner" aria-label="Project context: {{ $projectDomain }}">
      <div class="exec-ctx-domain">
        <span class="exec-ctx-live" aria-hidden="true"></span>
        @if($profileBrand)<span class="exec-ctx-brand">{{ $profileBrand }} &mdash;</span>@endif
        {{ $projectDomain }}
      </div>
      <div class="exec-ctx-meta">
        @if($scanCompletedLabel)
        <span class="exec-ctx-pill">Scanned {{ $scanCompletedLabel }}</span>
        @endif
        @if($pagesAnalyzed > 0)
        <span class="exec-ctx-pill">{{ $pagesAnalyzed }} pages</span>
        @endif
        <span class="exec-ctx-pill">Level {{ $tierRank }}</span>
        @if($leadRenderable)
        <a href="{{ $leadReportHref }}" class="exec-ctx-btn">
          Open Report
          <svg width="9" height="9" viewBox="0 0 9 9" fill="none" aria-hidden="true"><path d="M2 4.5h5M5 2.5l2 2-2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
        @else
        <a href="{{ route('quick-scan.show') }}" class="exec-ctx-btn-outline">Run Scan</a>
        @endif
      </div>
    </div>
    @endif

    <div class="dashboard-primary-flow {{ $isScansView ? 'is-scans-view' : '' }} {{ $isReportsView ? 'is-reports-view' : '' }}">"""

pre, mid, post = cut(content, STRIPS_START, STRIPS_END, 'strips → exec-ctx-bar')
content = pre + NEW_EXEC_CTX_BAR + post


# ══════════════════════════════════════════════════════════════════════
# PATCH 3 — Replace #system-state hero  →  executive summary card
# ══════════════════════════════════════════════════════════════════════
HERO_START = '    <section class="system-section system-section-primary mb-8 dash-section-anchor" id="system-state">'
HERO_END   = '        <p class="mt-4 text-[11px] uppercase tracking-[0.16em] text-[#988f7c]">Last evaluated: {{ $latestEvaluatedLabel }}</p>\n      </div>\n    </section>'

NEW_EXEC_HERO = """\
    <section class="system-section system-section-primary mb-6 dash-section-anchor" id="system-state" aria-labelledby="exec-score-label">
      <div class="exec-hero-shell surface-reveal is-visible">
        <div class="exec-hero-grid">

          {{-- Score ring --}}
          <div class="exec-score-col">
            <div class="exec-score-ring {{ $noScore ? '' : $leadTelemetryTone }}"
              @if($noScore) style="border-color:rgba(150,140,120,.28);background:radial-gradient(circle at 50% 38%,rgba(80,72,55,.1),rgba(14,11,8,.96) 70%)" @endif>
              @if($noScore)
                <span class="exec-score-no" id="exec-score-label">No<br>score<br>yet</span>
              @else
                <span class="exec-score-num" id="exec-score-label" aria-label="Score: {{ $leadScore }} out of 100">{{ $leadScore }}</span>
                <span class="exec-score-lbl" aria-hidden="true">/ 100</span>
              @endif
            </div>
            <span class="{{ $noScore ? 'state-chip state-chip-gold' : $stateChipClass }}">
              {{ $noScore ? 'No data yet' : $leadState }}
            </span>
          </div>

          {{-- Interpretation --}}
          <div class="exec-interp-col">
            <div>
              <p class="exec-interp-question">What this means for your business</p>
              <p class="exec-interp-answer">{!! $liveFeedbackInsight !!}</p>
            </div>
            @if(!$noScore)
            <div class="exec-bottleneck-block">
              <p class="exec-bottleneck-label">Biggest issue right now</p>
              <p class="exec-bottleneck-copy">{{ $leadBottleneck }}</p>
            </div>
            @else
            <div class="exec-bottleneck-block">
              <p class="exec-bottleneck-label">To get your score</p>
              <p class="exec-bottleneck-copy">We need to be able to read your site content. Open your site in a private browser — if it doesn&rsquo;t load publicly, we can&rsquo;t measure it yet.</p>
            </div>
            @endif
            <p class="exec-trust-line">
              Based on publicly readable signals
              @if($scanCompletedLabel) &nbsp;&middot;&nbsp; {{ $scanCompletedLabel }} @endif
              @if($pagesAnalyzed > 0) &nbsp;&middot;&nbsp; {{ $pagesAnalyzed }} pages analyzed @endif
              @if($scoreConfidence && !$noScore) &nbsp;&middot;&nbsp; Confidence: {{ $scoreConfidence }} @endif
            </p>
          </div>

          {{-- Primary actions --}}
          <div class="exec-action-col">
            @if($noScore)
              <a href="{{ route('quick-scan.show') }}" class="exec-cta-primary">Re-check my site &rarr;</a>
            @elseif($leadRenderable)
              <a href="{{ $leadReportHref }}" class="exec-cta-primary">Open Full Report &rarr;</a>
              @if($nextRoute)
              <a href="{{ $nextUnlockHref }}" class="exec-cta-secondary">{{ $nextStep }}</a>
              @endif
            @else
              <a href="{{ $nextMoveActionHref }}" class="exec-cta-primary">{{ $nextUnlockLabel }} &rarr;</a>
            @endif
            @if($scoreDelta > 0 && !$noScore)
              <p class="exec-cta-stat positive">{{ $scoreDeltaLabel }} pts since last scan</p>
            @elseif($scoreDelta < 0 && !$noScore)
              <p class="exec-cta-stat negative">{{ $scoreDeltaLabel }} pts since last scan</p>
            @elseif($leadLastEvaluation)
              <p class="exec-cta-stat">Last scanned {{ $leadLastEvaluation }}</p>
            @endif
          </div>

        </div>
      </div>
    </section>"""

pre, mid, post = cut(content, HERO_START, HERO_END, 'hero → exec-summary')
content = pre + NEW_EXEC_HERO + post


# ══════════════════════════════════════════════════════════════════════
# PATCH 4 — Replace entire @if($isSystemView) block
#           New structure: INTERPRETATION + NEXT MOVE + YOUR PLAN + AI ADVISOR
# ══════════════════════════════════════════════════════════════════════
SYSVIEW_START = "        @if($isSystemView)\n    @php\n      $levelMeta = ["
# End fence: start of @if($isReportsView) (everything between the two is discarded —
# includes the old consult-cta closing tags, stray </div>/</section>, and the
# @endif that closes isSystemView in the old code)
SYSVIEW_END   = "    @if($isReportsView)\n    <section class=\"system-section mb-8 dash-section-anchor surface-reveal\" id=\"report-readouts\">"

NEW_SYSTEM_VIEW = """\
        @if($isSystemView)

    @php
      $planLevels = [
        ['key' => 'scan-basic',          'num' => 1, 'kicker' => 'Step 1', 'name' => 'Baseline Score',
          'desc'     => 'Your starting visibility score, your top issue, and your first clear fix.',
          'included' => ['Visibility score (0–100)', 'Site standing: Strong / Average / At Risk', 'Top issue identified', 'Fastest fix recommendation'],
          'why'      => 'Know where you stand and exactly what to fix first.',
          'lift' => '+12 pts', 'price' => '$2'],
        ['key' => 'signal-expansion',    'num' => 2, 'kicker' => 'Step 2', 'name' => 'Signal Analysis',
          'desc'     => 'See precisely where stronger competitors are outranking or out-citing your site.',
          'included' => ['Competitor visibility comparison', 'Gap vs. competitors, signal by signal', 'Clearer path to improvement', 'Why you lose to specific competitors'],
          'why'      => 'Understand exactly why competitors beat you in AI search results.',
          'lift' => '+18 pts', 'price' => '$99'],
        ['key' => 'structural-leverage', 'num' => 3, 'kicker' => 'Step 3', 'name' => 'Priority Fixes',
          'desc'     => 'A ranked list of fixes sorted by impact — stop guessing, start with what matters most.',
          'included' => ['Impact-ranked fix list', 'Before/after score estimates per fix', 'Specific implementation steps', 'Time vs. impact analysis'],
          'why'      => 'Know exactly which changes move your score the most.',
          'lift' => '+22 pts', 'price' => '$249'],
        ['key' => 'system-activation',   'num' => 4, 'kicker' => 'Step 4', 'name' => 'Expansion Strategy',
          'desc'     => 'A full roadmap for consistently outranking competitors across AI, voice, and local search.',
          'included' => ['Full competitor roadmap', 'Multi-channel visibility strategy', 'Long-term growth plan', 'Ongoing improvement framework'],
          'why'      => 'Go from found sometimes to consistently recommended.',
          'lift' => '+28 pts', 'price' => '$489'],
      ];
      $layersByKey   = collect($analysisLayers ?? [])->keyBy('key');
      $firstIncompleteIdx = null;
      foreach ($planLevels as $idx => $lm) {
        if (! (bool) ($layersByKey->get($lm['key'])['complete'] ?? false)) {
          $firstIncompleteIdx = $idx; break;
        }
      }
      $checkoutRoutes = [
        'scan-basic'          => 'checkout.scan-basic',
        'signal-expansion'    => 'checkout.signal-expansion',
        'structural-leverage' => 'checkout.structural-leverage',
        'system-activation'   => 'checkout.system-activation',
      ];
      $completedLevelMeta = collect($planLevels)->filter(function($lm) use ($layersByKey) {
        return (bool) ($layersByKey->get($lm['key'])['complete'] ?? false);
      });
      $nextLevelMeta = isset($firstIncompleteIdx) ? $planLevels[$firstIncompleteIdx] : null;
      $nextLevelCheckoutHref = $nextLevelMeta && isset($checkoutRoutes[$nextLevelMeta['key']]) && \\Route::has($checkoutRoutes[$nextLevelMeta['key']])
        ? route($checkoutRoutes[$nextLevelMeta['key']])
        : $nextUnlockHref;
    @endphp

    {{-- ── INTERPRETATION: What's Driving Your Score ───────────────── --}}
    @if(!$noScore && isset($topFindings) && count($topFindings) > 0)
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="score-drivers" aria-labelledby="score-drivers-heading">
      <div class="score-drivers-shell">
        <div class="score-drivers-head">
          <div>
            <p class="score-drivers-kicker">What&rsquo;s Driving Your Score</p>
            <h2 id="score-drivers-heading" class="score-drivers-title">
              @if($leadScore >= 70)
                Solid base &mdash; these gaps are limiting your visibility.
              @elseif($leadScore >= 45)
                Visibility gaps found &mdash; here&rsquo;s what to prioritize.
              @else
                Critical gaps are blocking you from AI citation.
              @endif
            </h2>
            <p class="score-drivers-desc">These are the issues most likely affecting how often AI tools find and recommend {{ $projectDomain ?? 'your business' }}.</p>
          </div>
          <div class="score-drivers-meta">
            <span class="{{ $stateChipClass }}">{{ $leadState }}</span>
            <span class="score-drivers-count">{{ count($topFindings) }} issue{{ count($topFindings) !== 1 ? 's' : '' }} identified</span>
          </div>
        </div>
        <div class="score-drivers-grid">
          @foreach(array_slice($topFindings ?? [], 0, 3) as $findingIdx => $finding)
          @php
            $findingImpact    = (int) ($finding['impact_score'] ?? 0);
            $impactLabel      = $findingImpact >= 80 ? 'Critical' : ($findingImpact >= 60 ? 'High' : ($findingImpact >= 40 ? 'Medium' : 'Low'));
            $impactClass      = $findingImpact >= 80 ? 'impact-critical' : ($findingImpact >= 60 ? 'impact-high' : ($findingImpact >= 40 ? 'impact-medium' : 'impact-low'));
            $findingIsUnlocked = (bool) ($finding['is_unlocked'] ?? false);
            $findingTierName  = $finding['fix_tier'] ?? '';
            $findingTierPrice = $finding['fix_price'] ?? '';
            $findingRouteKey  = $finding['fix_route'] ?? null;
            $findingUnlockHref = ($findingRouteKey && \\Route::has($findingRouteKey)) ? route($findingRouteKey) : $nextUnlockHref;
            $findingPrompt    = 'My top issue is: ' . ($finding['what_missing'] ?? 'unknown') . '. Why does this matter for my AI visibility score, and what should I do about it?';
          @endphp
          <article class="score-driver-card {{ $findingIdx === 0 ? 'is-primary' : '' }} {{ !$findingIsUnlocked ? 'is-locked' : '' }}"
            aria-label="Issue {{ $findingIdx + 1 }}: {{ Str::limit($finding['what_missing'] ?? 'Issue detected', 60) }}">
            <div class="sdc-header">
              <span class="sdc-rank" aria-label="Priority {{ $findingIdx + 1 }}">{{ $findingIdx + 1 }}</span>
              <span class="sdc-impact-badge {{ $impactClass }}">{{ $impactLabel }}</span>
              @if(!$findingIsUnlocked && $findingTierName)
              <span class="sdc-lock-badge">Unlock {{ $findingTierName }}</span>
              @endif
            </div>
            <h3 class="sdc-issue">{{ $finding['what_missing'] ?? 'Issue detected' }}</h3>
            @if($findingIsUnlocked)
              @if(!empty($finding['why_it_matters']))
              <p class="sdc-why">{{ $finding['why_it_matters'] }}</p>
              @endif
              @if(!empty($finding['fix']))
              <div class="sdc-fix">
                <p class="sdc-fix-label">Fix</p>
                <p class="sdc-fix-copy">{{ $finding['fix'] }}</p>
              </div>
              @endif
              <button type="button"
                class="sdc-ask-btn js-ask-scan-chip"
                data-prompt="{{ $findingPrompt }}"
                aria-label="Ask AI about this issue">
                <svg width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true"><circle cx="5.5" cy="5.5" r="4.5" stroke="currentColor" stroke-width="1.1"/><path d="M5.5 7V5.5M5.5 4h.01" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                Ask AI about this
              </button>
            @else
              <p class="sdc-locked-hint">Unlock {{ $findingTierName }}{{ $findingTierPrice ? ' ('.$findingTierPrice.')' : '' }} to see the full explanation and fix for this issue.</p>
              <a href="{{ $findingUnlockHref }}" class="sdc-unlock-btn">Unlock to See Fix &rarr;</a>
            @endif
          </article>
          @endforeach
        </div>
      </div>
    </section>
    @endif

    {{-- ── PRIORITY: What to Do Next ───────────────────────────────── --}}
    @if(!$noScore)
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="next-move" aria-labelledby="next-move-heading">
      <div class="next-move-shell">
        <p class="dash-section-label">What to do next</p>
        <h2 id="next-move-heading" class="dash-section-heading">Your clearest path to a higher score</h2>
        <p class="dash-section-subhead">Two actions, ranked by speed and impact for {{ $projectDomain ?? 'your site' }}.</p>
        <div class="next-move-row">

          {{-- Fastest win --}}
          <div class="nm-card nm-primary">
            <p class="nm-card-kicker">Fastest win</p>
            <h3 class="nm-card-title">{{ $nextMoveFastestFix }}</h3>
            <p class="nm-card-rationale">The quickest change likely to raise your score before your next scan.</p>
            @if($leadRenderable)
            <a href="{{ $leadReportHref }}" class="nm-card-action">View fix in your report &rarr;</a>
            @else
            <a href="{{ $nextMoveActionHref }}" class="nm-card-action">{{ $nextUnlockLabel }} &rarr;</a>
            @endif
          </div>

          {{-- Highest leverage --}}
          @if(isset($nextBestAction) && !empty($nextBestAction))
          <div class="nm-card">
            <p class="nm-card-kicker">Highest leverage</p>
            <h3 class="nm-card-title">{{ $nextBestAction['what_missing'] ?? 'Primary visibility gap' }}</h3>
            <p class="nm-card-rationale">{{ !empty($nextBestAction['why_it_matters']) ? $nextBestAction['why_it_matters'] : 'Fixing this has the highest projected impact on your visibility score.' }}</p>
            @if($leadRenderable)
            <a href="{{ $leadReportHref }}" class="nm-card-action">See full fix details &rarr;</a>
            @else
            <a href="{{ $nextMoveActionHref }}" class="nm-card-action">{{ $nextUnlockLabel }} &rarr;</a>
            @endif
          </div>
          @elseif(isset($nextUpgrade) && !empty($nextUpgrade))
          <div class="nm-card">
            <p class="nm-card-kicker">Deeper insight available</p>
            <h3 class="nm-card-title">{{ $nextUpgrade['label'] ?? $nextStep }}</h3>
            <p class="nm-card-rationale">{{ $nextUpgrade['description'] ?? 'Unlock the next level to see your highest-impact fixes, ranked by score potential.' }}</p>
            <a href="{{ $nextUnlockHref }}" class="nm-card-action">
              Unlock{{ isset($nextUpgrade['price']) ? ' — '.$nextUpgrade['price'] : ($nextLevelPrice ? ' — '.$nextLevelPrice : '') }} &rarr;
            </a>
          </div>
          @elseif($nextStep)
          <div class="nm-card">
            <p class="nm-card-kicker">Deeper analysis available</p>
            <h3 class="nm-card-title">{{ $nextStep }}</h3>
            <p class="nm-card-rationale">The next level reveals more precise fixes and shows exactly which changes will move your score the most.</p>
            <a href="{{ $nextUnlockHref }}" class="nm-card-action">
              Unlock{{ $nextLevelPrice ? ' — '.$nextLevelPrice : '' }} &rarr;
            </a>
          </div>
          @endif

        </div>
      </div>
    </section>
    @endif

    {{-- ── PROGRESSION: Your Plan ───────────────────────────────────── --}}
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="your-plan" aria-labelledby="your-plan-heading">
      <div class="your-plan-shell">

        <p class="dash-section-label">Your Plan</p>
        <h2 id="your-plan-heading" class="dash-section-heading">
          @if($completedLevelMeta->isEmpty())
            Start with Step 1 — your baseline score
          @elseif($nextLevelMeta)
            Level {{ $tierRank }} active &mdash; {{ $nextLevelMeta['name'] }} is your next step
          @else
            All levels complete &mdash; expansion mode active
          @endif
        </h2>
        <p class="dash-section-subhead">What your current plan includes, and what deeper access unlocks.</p>

        {{-- Level progress rail --}}
        <div class="plan-rail-row" aria-label="Analysis level progress">
          @foreach($planLevels as $idx => $lm)
            @php
              $plIsComplete = (bool) ($layersByKey->get($lm['key'])['complete'] ?? false);
              $plIsActive   = $idx === $firstIncompleteIdx;
              $plRailState  = $plIsComplete ? 'is-complete' : ($plIsActive ? 'is-active' : 'is-locked');
              $plConnector  = $plIsComplete ? 'level-rail-connector is-complete' : 'level-rail-connector';
            @endphp
            <div class="level-rail-step {{ $plRailState }}" aria-label="{{ $lm['name'] }}{{ $plIsComplete ? ' (complete)' : ($plIsActive ? ' (next)' : ' (locked)') }}">
              <div class="level-rail-step-dot">
                @if($plIsComplete)
                  <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M1.5 5l2.5 2.5 4.5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                @elseif($plIsActive)
                  <svg width="8" height="8" viewBox="0 0 8 8" fill="none"><circle cx="4" cy="4" r="3" fill="currentColor"/></svg>
                @else
                  <svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M4.5 1a1.75 1.75 0 0 1 1.75 1.75V4h-3.5V2.75A1.75 1.75 0 0 1 4.5 1z" stroke="currentColor" stroke-width="1.2"/><rect x="2" y="4" width="5" height="4" rx="1" stroke="currentColor" stroke-width="1.2"/></svg>
                @endif
              </div>
              <span class="level-rail-step-label">{{ $lm['kicker'] }}</span>
            </div>
            @if(!$loop->last)
              <div class="{{ $plConnector }}"></div>
            @endif
          @endforeach
        </div>

        {{-- Current plan + next upgrade --}}
        @if($completedLevelMeta->isNotEmpty() || $nextLevelMeta)
        <div class="plan-level-row">

          {{-- What's included now --}}
          @if($completedLevelMeta->isNotEmpty())
          @php $currentPlanLevel = $completedLevelMeta->last(); @endphp
          <div class="plan-current-card">
            <p class="plan-current-kicker">What&rsquo;s included in your plan now</p>
            <h3 class="plan-current-name">{{ $currentPlanLevel['kicker'] }} &mdash; {{ $currentPlanLevel['name'] }}</h3>
            <p class="plan-current-desc">{{ $currentPlanLevel['why'] }}</p>
            <div class="plan-included-list">
              @foreach($currentPlanLevel['included'] as $item)
              <div class="plan-included-item">
                <span class="plan-included-icon" aria-hidden="true">
                  <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1.5 6l3 3 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                {{ $item }}
              </div>
              @endforeach
            </div>
            @if($leadRenderable)
            <a href="{{ $leadReportHref }}" class="plan-view-report-btn">View your report &rarr;</a>
            @endif
          </div>
          @endif

          {{-- Next level --}}
          @if($nextLevelMeta)
          @php
            $nextCheckHref = (isset($checkoutRoutes[$nextLevelMeta['key']]) && \Route::has($checkoutRoutes[$nextLevelMeta['key']]))
              ? route($checkoutRoutes[$nextLevelMeta['key']]) : $nextUnlockHref;
          @endphp
          <div class="plan-next-card">
            <p class="plan-next-kicker">{{ $nextLevelMeta['kicker'] }} &mdash; next level to unlock</p>
            <h3 class="plan-next-name">{{ $nextLevelMeta['name'] }}</h3>
            <p class="plan-next-price">{{ $nextLevelMeta['price'] }}</p>
            <p class="plan-next-desc">{{ $nextLevelMeta['why'] }}</p>
            <div class="plan-next-unlocks">
              @foreach($nextLevelMeta['included'] as $item)
              <div class="plan-next-unlock-item">
                <span class="plan-next-unlock-icon" aria-hidden="true">
                  <svg width="11" height="11" viewBox="0 0 11 11" fill="none"><path d="M2 5.5l2.5 2.5 4.5-4.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                {{ $item }}
              </div>
              @endforeach
            </div>
            <button type="button" class="plan-unlock-cta js-dcm-open"
              data-level="{{ $nextLevelMeta['num'] }}"
              data-level-name="{{ $nextLevelMeta['name'] }}"
              data-checkout-href="{{ $nextCheckHref }}"
              data-price="{{ $nextLevelMeta['price'] }}">
              Unlock {{ $nextLevelMeta['name'] }} &mdash; {{ $nextLevelMeta['price'] }}
            </button>
          </div>
          @else
          {{-- All levels complete --}}
          <div class="plan-all-unlocked-card">
            <p class="plan-all-unlocked-kicker">All levels complete</p>
            <h3 class="plan-all-unlocked-title">System fully activated</h3>
            <p class="plan-all-unlocked-desc">You have access to all four analysis levels. Your complete visibility framework is active.</p>
          </div>
          @endif

        </div>
        @else
        {{-- No completed layers, one next step --}}
        @if($nextLevelMeta)
        @php
          $nextCheckHref = (isset($checkoutRoutes[$nextLevelMeta['key']]) && \\Route::has($checkoutRoutes[$nextLevelMeta['key']]))
            ? route($checkoutRoutes[$nextLevelMeta['key']]) : $nextUnlockHref;
        @endphp
        <div class="plan-level-row">
          <div class="plan-next-card" style="grid-column:1/-1">
            <p class="plan-next-kicker">Start here — Step 1</p>
            <h3 class="plan-next-name">Baseline Score</h3>
            <p class="plan-next-price">$2</p>
            <p class="plan-next-desc">Get your visibility score, your top issue, and your first clear fix.</p>
            <button type="button" class="plan-unlock-cta js-dcm-open"
              data-level="1" data-level-name="Baseline Score"
              data-checkout-href="{{ $nextCheckHref }}" data-price="$2">
              Get Your Baseline Score &mdash; $2
            </button>
          </div>
        </div>
        @endif
        @endif

        {{-- Consultation row --}}
        <div class="plan-consult-row">
          <p class="plan-consult-copy">Want us to implement this for you? We map, build, and deploy your full visibility system.</p>
          <a href="{{ route('book.index') }}?entry=dashboard-plan" class="plan-consult-btn">Book a Strategy Session &rarr;</a>
        </div>

      </div>
    </section>

    {{-- ── CONFIDENCE: Your AI Advisor ─────────────────────────────── --}}
    @if($leadScore > 0 || $leadRenderable)
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="ai-advisor-section" aria-labelledby="ai-advisor-heading">
      <div class="ai-advisor-shell">
        <div class="ai-advisor-head">
          <div>
            <p class="ai-advisor-kicker">Your AI Visibility Advisor</p>
            <h2 id="ai-advisor-heading" class="ai-advisor-title">Ask anything about{{ $projectDomain ? ' '.$projectDomain : ' your scan' }}</h2>
            <p class="ai-advisor-desc">Get plain-English explanations of your score, what to fix, and what to do next.</p>
          </div>
          <button type="button" class="ai-advisor-open-btn js-open-ai-no-prompt" aria-label="Open AI advisor">
            Open Advisor
            <svg width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true" style="margin-left:6px"><path d="M2.5 5.5h6M6.5 3l2.5 2.5L6.5 8" stroke="currentColor" stroke-width="1.45" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
        <div class="ai-advisor-chips" role="list">
          <button type="button" role="listitem" class="ai-advisor-chip js-ask-scan-chip"
            data-prompt="My AI visibility score is {{ $leadScore }}/100 and my status is {{ $leadState }}. In plain English, what does this mean for my business, and what's the single most important thing I can do to improve it?"
            aria-label="Ask: Why is my score {{ $leadScore }}?">
            <span class="ai-chip-icon" aria-hidden="true"><svg width="9" height="9" viewBox="0 0 9 9" fill="none"><circle cx="4.5" cy="4.5" r="3.5" stroke="rgba(200,168,75,0.7)" stroke-width="1"/><path d="M4.5 6V4.5M4.5 3.5h.01" stroke="rgba(200,168,75,0.7)" stroke-width="1.1" stroke-linecap="round"/></svg></span>
            Why is my score {{ $leadScore }}?
          </button>
          <button type="button" role="listitem" class="ai-advisor-chip js-ask-scan-chip"
            data-prompt="What's the single fastest fix I can make right now to improve my AI visibility score for {{ $projectDomain ?? 'my website' }}? Be specific and practical."
            aria-label="Ask about fastest fix">
            <span class="ai-chip-icon" aria-hidden="true"><svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M4.5 1v3.5l2 1.5" stroke="rgba(200,168,75,0.7)" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/><circle cx="4.5" cy="4.5" r="3.5" stroke="rgba(200,168,75,0.7)" stroke-width="1"/></svg></span>
            What&rsquo;s the fastest fix?
          </button>
          <button type="button" role="listitem" class="ai-advisor-chip js-ask-scan-chip"
            data-prompt="What does being '{{ $leadState }}' mean in practical terms for {{ $projectDomain ?? 'my business' }}? How urgently should I act and what's at risk if I don't?"
            aria-label="Ask what {{ $leadState }} means">
            <span class="ai-chip-icon" aria-hidden="true"><svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M1.5 4.5h6M4.5 1.5v6" stroke="rgba(200,168,75,0.7)" stroke-width="1.1" stroke-linecap="round"/></svg></span>
            What does &lsquo;{{ $leadState }}&rsquo; mean?
          </button>
          @if($nextStep)
          <button type="button" role="listitem" class="ai-advisor-chip js-ask-scan-chip"
            data-prompt="Is it worth unlocking {{ $nextStep }} for {{ $projectDomain ?? 'my website' }}? What specific improvements will I see, and how does it help my visibility in AI search results?"
            aria-label="Ask about {{ $nextStep }}">
            <span class="ai-chip-icon" aria-hidden="true"><svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M4.5 1.5a1.5 1.5 0 0 1 1.5 1.5V4H3V3a1.5 1.5 0 0 1 1.5-1.5z" stroke="rgba(200,168,75,0.7)" stroke-width="1"/><rect x="2" y="4" width="5" height="3.5" rx="0.75" stroke="rgba(200,168,75,0.7)" stroke-width="1"/></svg></span>
            Is &ldquo;{{ $nextStep }}&rdquo; worth it?
          </button>
          @else
          <button type="button" role="listitem" class="ai-advisor-chip js-ask-scan-chip"
            data-prompt="I've completed all scan analysis levels for {{ $projectDomain ?? 'my website' }}. What should I focus on next to keep growing my AI visibility score over time?"
            aria-label="Ask what to focus on next">
            <span class="ai-chip-icon" aria-hidden="true"><svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M4.5 1v7M2 3.5l2.5-2.5 2.5 2.5" stroke="rgba(200,168,75,0.7)" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            What should I focus on next?
          </button>
          @endif
        </div>
      </div>
    </section>
    @endif

    @endif
"""

pre, mid, post = cut(content, SYSVIEW_START, SYSVIEW_END, '@if($isSystemView) block')
content = pre + NEW_SYSTEM_VIEW + SYSVIEW_END + post


# ══════════════════════════════════════════════════════════════════════
# Save
# ══════════════════════════════════════════════════════════════════════
save(BLADE, content)
print('\nAll patches applied successfully. File saved.')
