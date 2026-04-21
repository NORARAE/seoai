#!/usr/bin/env python3
"""
Patch script for dashboard/customer-modern.blade.php
Adds:
  1. CSS for Score Drivers (INTERPRETATION) + AI Advisor (CONFIDENCE) sections
  2. INTERPRETATION section — "What's Driving Your Score" using $topFindings
  3. AI ADVISOR section — embedded chips panel wired to floating AI assistant
  4. openAiPanel() JS + chip click delegation
"""
import sys, os

BLADE_FILE = os.path.join(
    os.path.dirname(__file__),
    '../resources/views/dashboard/customer-modern.blade.php'
)

def load(path):
    with open(path, 'r', encoding='utf-8') as f:
        return f.read()

def save(path, content):
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)

def patch(content, anchor, replacement, label, position='after'):
    if anchor not in content:
        print(f'  FAIL [{label}] — anchor not found')
        return content, False
    if position == 'after':
        content = content.replace(anchor, anchor + replacement, 1)
    elif position == 'before':
        content = content.replace(anchor, replacement + anchor, 1)
    print(f'  OK   [{label}]')
    return content, True

# ── Load ──────────────────────────────────────────────────────────────
content = load(BLADE_FILE)
ok_all = True

# ══════════════════════════════════════════════════════════════════════
# PATCH 1 — Add CSS for score-drivers and ai-advisor sections
# ══════════════════════════════════════════════════════════════════════
CSS_ANCHOR = '  .domain-accent{color:#e8d090;font-style:normal;font-weight:inherit}\n</style>'

CSS_NEW = """
  /* ── Score Drivers / Interpretation ─────────────────────────── */
  .score-drivers-shell{border:1px solid rgba(200,168,75,.22);border-radius:20px;background:linear-gradient(155deg,#16120a,#0d0a06 72%);padding:22px;box-shadow:0 18px 38px rgba(0,0,0,.32),inset 0 1px 0 rgba(255,255,255,.03)}
  .score-drivers-head{display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:18px}
  .score-drivers-kicker{font-size:.62rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(200,168,75,.65);margin-bottom:6px}
  .score-drivers-title{font-size:1.05rem;font-weight:600;color:#ede8de;line-height:1.35;max-width:42rem}
  .score-drivers-desc{margin-top:5px;font-size:.82rem;line-height:1.6;color:#a8a193;max-width:40rem}
  .score-drivers-meta{display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0}
  .score-drivers-count{font-size:.56rem;letter-spacing:.16em;text-transform:uppercase;color:#9a9082}
  .score-drivers-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px}
  .score-driver-card{border:1px solid rgba(200,168,75,.16);border-radius:14px;background:linear-gradient(155deg,#181410,#100d08 70%);padding:16px;display:flex;flex-direction:column;gap:10px;position:relative;overflow:hidden;transition:transform .22s ease,box-shadow .22s ease,border-color .22s ease}
  .score-driver-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.24),transparent)}
  .score-driver-card:hover{transform:translateY(-3px);border-color:rgba(200,168,75,.3);box-shadow:0 12px 28px rgba(0,0,0,.34)}
  .score-driver-card.is-primary{border-color:rgba(200,168,75,.38);background:linear-gradient(155deg,#1e1a0e,#12100a 68%);box-shadow:0 0 0 1px rgba(200,168,75,.16) inset}
  .score-driver-card.is-primary::before{background:linear-gradient(90deg,transparent,rgba(200,168,75,.52),transparent)}
  .score-driver-card.is-locked{opacity:.72}
  .sdc-header{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
  .sdc-rank{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:rgba(200,168,75,.14);border:1px solid rgba(200,168,75,.28);font-size:.62rem;font-weight:700;color:rgba(200,168,75,.8);flex-shrink:0}
  .sdc-impact-badge{padding:2px 8px;border-radius:999px;font-size:.52rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase}
  .impact-critical{background:rgba(196,80,80,.14);border:1px solid rgba(196,80,80,.3);color:#e8a8a8}
  .impact-high{background:rgba(214,163,55,.12);border:1px solid rgba(214,163,55,.28);color:#e8cfa0}
  .impact-medium{background:rgba(200,168,75,.1);border:1px solid rgba(200,168,75,.22);color:#d9c68a}
  .impact-low{background:rgba(150,142,120,.1);border:1px solid rgba(150,142,120,.2);color:#b4ae9a}
  .sdc-lock-badge{margin-left:auto;padding:2px 8px;border-radius:999px;font-size:.5rem;letter-spacing:.1em;text-transform:uppercase;background:rgba(80,70,55,.28);border:1px solid rgba(200,168,75,.14);color:rgba(200,168,75,.42)}
  .sdc-issue{font-size:.88rem;font-weight:600;line-height:1.4;color:#ede3ca}
  .sdc-why{font-size:.78rem;line-height:1.55;color:#b8b0a0}
  .sdc-fix{border:1px solid rgba(200,168,75,.16);border-radius:10px;background:rgba(200,168,75,.05);padding:10px 12px}
  .sdc-fix-label{font-size:.52rem;letter-spacing:.18em;text-transform:uppercase;color:#d8c58f;margin-bottom:5px}
  .sdc-fix-copy{font-size:.8rem;line-height:1.52;color:#e4dbca}
  .sdc-ask-btn{display:inline-flex;align-items:center;gap:7px;padding:6px 13px;border-radius:999px;border:1px solid rgba(200,168,75,.28);background:rgba(200,168,75,.08);color:#d9c988;font-size:.62rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;cursor:pointer;transition:all .18s ease;margin-top:auto;align-self:flex-start}
  .sdc-ask-btn:hover{border-color:rgba(200,168,75,.52);background:rgba(200,168,75,.16);color:#f0dda6}
  .sdc-locked-hint{font-size:.75rem;line-height:1.5;color:#9a9082;font-style:italic}
  .sdc-unlock-btn{display:inline-flex;align-items:center;justify-content:center;min-height:32px;padding:6px 14px;border-radius:8px;border:1px solid rgba(200,168,75,.32);background:rgba(200,168,75,.1);color:#d9c988;font-size:.62rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;transition:all .18s ease;align-self:flex-start}
  .sdc-unlock-btn:hover{border-color:rgba(200,168,75,.52);background:rgba(200,168,75,.18)}
  @media(max-width:640px){.score-drivers-grid{grid-template-columns:1fr}}

  /* ── AI Advisor / Confidence ─────────────────────────────────── */
  .ai-advisor-shell{border:1px solid rgba(200,168,75,.24);border-radius:18px;background:linear-gradient(155deg,#1a1610,#0e0c08 72%);padding:20px 22px;box-shadow:0 12px 30px rgba(0,0,0,.28),inset 0 1px 0 rgba(255,255,255,.03);position:relative;overflow:hidden}
  .ai-advisor-shell::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.54),transparent)}
  .ai-advisor-shell::after{content:'';position:absolute;inset:-10% -20% auto -20%;height:140%;background:radial-gradient(ellipse at 50% 0,rgba(200,168,75,.07),transparent 56%);pointer-events:none}
  .ai-advisor-head{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px;margin-bottom:16px}
  .ai-advisor-kicker{font-size:.58rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.68);margin-bottom:5px}
  .ai-advisor-title{font-size:.96rem;font-weight:600;color:#ede8de;line-height:1.25}
  .ai-advisor-desc{margin-top:4px;font-size:.78rem;line-height:1.55;color:#a8a193;max-width:36rem}
  .ai-advisor-open-btn{flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 18px;border-radius:10px;background:#c6a85a;color:#1a1a1a;font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;border:none;cursor:pointer;transition:all .2s ease;box-shadow:0 6px 16px rgba(198,168,90,.2),inset 0 1px 0 rgba(255,255,255,.14)}
  .ai-advisor-open-btn:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(198,168,90,.34),inset 0 1px 0 rgba(255,255,255,.18)}
  .ai-advisor-chips{display:flex;flex-wrap:wrap;gap:8px;position:relative;z-index:1}
  .ai-advisor-chip{display:inline-flex;align-items:center;gap:8px;padding:9px 15px;border-radius:999px;border:1px solid rgba(200,168,75,.22);background:rgba(200,168,75,.07);color:#d9c98a;font-size:.72rem;font-weight:500;letter-spacing:.04em;cursor:pointer;transition:all .18s ease;text-align:left}
  .ai-advisor-chip:hover{border-color:rgba(200,168,75,.5);background:rgba(200,168,75,.16);color:#f0e0a6;transform:translateY(-1px)}
  .ai-chip-icon{width:18px;height:18px;border-radius:50%;background:rgba(200,168,75,.14);border:1px solid rgba(200,168,75,.22);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0}
  @media(max-width:640px){.ai-advisor-chips{flex-direction:column}.ai-advisor-chip{width:100%}.ai-advisor-head{flex-direction:column;align-items:flex-start}}"""

content, ok = patch(content, CSS_ANCHOR, CSS_NEW, 'CSS new sections')
ok_all = ok_all and ok

# ══════════════════════════════════════════════════════════════════════
# PATCH 2 — Add INTERPRETATION section after $levelMeta @endphp block
# ══════════════════════════════════════════════════════════════════════
INTERP_ANCHOR = """    @endphp

    {{-- Level Progression Rail --}}"""

INTERP_NEW = """

    {{-- ═══════════════════════════════════════════════════════════════
         INTERPRETATION — What's Driving Your Score
         Uses $topFindings (passed from DashboardController)
    ════════════════════════════════════════════════════════════════= --}}
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
            $findingImpact = (int) ($finding['impact_score'] ?? 0);
            $impactLabel = $findingImpact >= 80 ? 'Critical' : ($findingImpact >= 60 ? 'High' : ($findingImpact >= 40 ? 'Medium' : 'Low'));
            $impactClass = $findingImpact >= 80 ? 'impact-critical' : ($findingImpact >= 60 ? 'impact-high' : ($findingImpact >= 40 ? 'impact-medium' : 'impact-low'));
            $findingIsUnlocked = (bool) ($finding['is_unlocked'] ?? false);
            $findingTierName   = $finding['fix_tier'] ?? '';
            $findingTierPrice  = $finding['fix_price'] ?? '';
            $findingRouteKey   = $finding['fix_route'] ?? null;
            $findingUnlockHref = ($findingRouteKey && \\Route::has($findingRouteKey))
              ? route($findingRouteKey)
              : $nextUnlockHref;
            $findingPrompt = 'My top issue is: ' . ($finding['what_missing'] ?? 'unknown') . '. Why does this matter for my AI visibility score, and what should I do about it?';
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
              <a href="{{ $findingUnlockHref }}" class="sdc-unlock-btn">
                Unlock to See Fix &rarr;
              </a>
            @endif
          </article>
          @endforeach
        </div>
      </div>
    </section>
    @endif
"""

content, ok = patch(content, INTERP_ANCHOR, INTERP_NEW, 'INTERPRETATION section', position='before')
ok_all = ok_all and ok

# ══════════════════════════════════════════════════════════════════════
# PATCH 3 — Add AI ADVISOR section after #consult-cta, before @endif
# ══════════════════════════════════════════════════════════════════════
ADVISOR_ANCHOR = """      </div>
    </section>

    @endif

    @if($isReportsView)"""

ADVISOR_NEW = """

    {{-- ═══════════════════════════════════════════════════════════════
         CONFIDENCE — Your AI Advisor (embedded chips panel)
    ════════════════════════════════════════════════════════════════= --}}
    @if($leadScore > 0 || $leadRenderable)
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="ai-advisor-section" aria-labelledby="ai-advisor-heading">
      <div class="ai-advisor-shell">
        <div class="ai-advisor-head">
          <div>
            <p class="ai-advisor-kicker">Your AI Visibility Advisor</p>
            <h2 id="ai-advisor-heading" class="ai-advisor-title">Ask anything about{{ $projectDomain ? ' '.$projectDomain : ' your scan' }}</h2>
            <p class="ai-advisor-desc">Get plain-English explanations of your score, what to fix, and what to expect from each improvement.</p>
          </div>
          <button type="button" class="ai-advisor-open-btn js-open-ai-no-prompt" aria-label="Open AI advisor panel">
            Open Advisor
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" style="margin-left:6px"><path d="M2.5 6h7M6.5 3.5 9 6 6.5 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>

        <div class="ai-advisor-chips" role="list">
          {{-- Chip 1: Score explanation --}}
          <button type="button" role="listitem"
            class="ai-advisor-chip js-ask-scan-chip"
            data-prompt="My AI visibility score is {{ $leadScore }}/100 and my status is {{ $leadState }}. In plain English, what does this mean for my business, and what's the single most important thing I can do to improve it?"
            aria-label="Ask: Why is my score {{ $leadScore }}?">
            <span class="ai-chip-icon" aria-hidden="true">
              <svg width="9" height="9" viewBox="0 0 9 9" fill="none"><circle cx="4.5" cy="4.5" r="3.5" stroke="rgba(200,168,75,0.7)" stroke-width="1"/><path d="M4.5 6V4.5M4.5 3.5h.01" stroke="rgba(200,168,75,0.7)" stroke-width="1.1" stroke-linecap="round"/></svg>
            </span>
            Why is my score {{ $leadScore }}?
          </button>

          {{-- Chip 2: Fastest fix --}}
          <button type="button" role="listitem"
            class="ai-advisor-chip js-ask-scan-chip"
            data-prompt="What's the single fastest fix I can make right now to improve my AI visibility score for {{ $projectDomain ?? 'my website' }}? Be specific and practical."
            aria-label="Ask about fastest fix">
            <span class="ai-chip-icon" aria-hidden="true">
              <svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M4.5 1v3.5l2 1.5" stroke="rgba(200,168,75,0.7)" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/><circle cx="4.5" cy="4.5" r="3.5" stroke="rgba(200,168,75,0.7)" stroke-width="1"/></svg>
            </span>
            What&rsquo;s the fastest fix?
          </button>

          {{-- Chip 3: Score state plain English --}}
          <button type="button" role="listitem"
            class="ai-advisor-chip js-ask-scan-chip"
            data-prompt="What does being '{{ $leadState }}' mean in practical terms for {{ $projectDomain ?? 'my business' }}? How urgently should I act and what's at risk if I don't?"
            aria-label="Ask what {{ $leadState }} means">
            <span class="ai-chip-icon" aria-hidden="true">
              <svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M1.5 4.5h6M4.5 1.5v6" stroke="rgba(200,168,75,0.7)" stroke-width="1.1" stroke-linecap="round"/></svg>
            </span>
            What does &lsquo;{{ $leadState }}&rsquo; mean?
          </button>

          {{-- Chip 4: Next step / upgrade --}}
          @if($nextStep)
          <button type="button" role="listitem"
            class="ai-advisor-chip js-ask-scan-chip"
            data-prompt="Is it worth unlocking {{ $nextStep }} for {{ $projectDomain ?? 'my website' }}? What specific improvements will I see, and how does it help my visibility in AI search results?"
            aria-label="Ask about unlocking {{ $nextStep }}">
            <span class="ai-chip-icon" aria-hidden="true">
              <svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M4.5 1.5a1.5 1.5 0 0 1 1.5 1.5V4H3V3a1.5 1.5 0 0 1 1.5-1.5z" stroke="rgba(200,168,75,0.7)" stroke-width="1"/><rect x="2" y="4" width="5" height="3.5" rx="0.75" stroke="rgba(200,168,75,0.7)" stroke-width="1"/></svg>
            </span>
            Is &ldquo;{{ $nextStep }}&rdquo; worth it?
          </button>
          @else
          <button type="button" role="listitem"
            class="ai-advisor-chip js-ask-scan-chip"
            data-prompt="I've completed all scan analysis levels for {{ $projectDomain ?? 'my website' }}. What should I focus on next to keep growing my AI visibility score over time?"
            aria-label="Ask what to focus on next">
            <span class="ai-chip-icon" aria-hidden="true">
              <svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M4.5 1v7M2 3.5l2.5-2.5 2.5 2.5" stroke="rgba(200,168,75,0.7)" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            What should I focus on next?
          </button>
          @endif
        </div>
      </div>
    </section>
    @endif
"""

content, ok = patch(content, ADVISOR_ANCHOR, ADVISOR_NEW, 'AI ADVISOR section', position='before')
ok_all = ok_all and ok

# ══════════════════════════════════════════════════════════════════════
# PATCH 4 — Insert openAiPanel() JS + class-based chip delegation
#           before the Data Capture Modal HTML
# ══════════════════════════════════════════════════════════════════════
JS_ANCHOR = "\n{{-- Data Capture Modal --}}"

JS_NEW = """
<script>
// ── Dashboard AI Advisor chip wiring ─────────────────────────────────
(function () {
  'use strict';

  /**
   * Open the floating AI assistant panel and optionally send a prompt.
   * Matches the API used on quick-scan-result.blade.php.
   */
  function openAiPanel(prompt) {
    var trigger = document.getElementById('aiaTrigger');
    var inputEl = document.getElementById('aiaInput');
    var sendEl  = document.getElementById('aiaSend');
    if (!trigger) return;

    // Open the panel
    if (trigger.getAttribute('aria-expanded') !== 'true') {
      trigger.click();
    }
    if (!prompt) return;

    // After the greeting renders (~420ms), populate and auto-send
    setTimeout(function () {
      if (inputEl) {
        inputEl.value = prompt;
        inputEl.dispatchEvent(new Event('input', { bubbles: true }));
      }
      setTimeout(function () {
        if (sendEl && !sendEl.disabled) sendEl.click();
      }, 180);
    }, 420);
  }

  // Delegate clicks for all chip classes used on this page
  document.addEventListener('click', function (e) {
    var t = e.target.closest('.js-ask-scan-chip, .js-open-ai-panel, .js-open-ai-no-prompt');
    if (!t) return;
    if (t.classList.contains('js-open-ai-no-prompt')) {
      openAiPanel('');
      return;
    }
    openAiPanel(t.dataset.prompt || '');
  });
})();
</script>
"""

content, ok = patch(content, JS_ANCHOR, JS_NEW, 'openAiPanel JS', position='before')
ok_all = ok_all and ok

# ── Save ──────────────────────────────────────────────────────────────
if ok_all:
    save(BLADE_FILE, content)
    print('\nAll patches applied. File saved.')
else:
    print('\nOne or more patches failed — file NOT saved.')
    sys.exit(1)
