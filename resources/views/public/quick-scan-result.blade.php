<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#080808">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>System Readout Layer: {{ $scan->score ?? 0 }}/100 — SEOAIco™</title>
<meta name="description" content="System readout score {{ $scan->score ?? 0 }}/100 with active signals, constraints, and state.">
<meta name="robots" content="noindex">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@php
  $score = (int) ($scan->score ?? 0);
  $categories = is_array($scan->categories ?? null) ? $scan->categories : [];
  $unlockLevel = max(1, (int) $scan->upgradeTierRank());
  $nextPurchasableRank = $unlockLevel < 4 ? $unlockLevel + 1 : null;
  $isUpgraded = $scan->upgrade_status === 'paid' && $scan->normalizedUpgradePlan() !== null;

  $tierDefs = [
    2 => ['name' => 'Signal Analysis', 'plan' => 'diagnostic', 'price' => '$99', 'value' => 'Full signal-by-signal breakdown — see exactly what is suppressing your score.'],
    3 => ['name' => 'Action Plan', 'plan' => 'fix-strategy', 'price' => '$249', 'value' => 'Prioritized fix list ordered by impact — know what to execute first.'],
    4 => ['name' => 'Guided Execution', 'plan' => 'optimization', 'price' => '$489', 'value' => 'Step-by-step execution checklist with progress tracking inside your dashboard.'],
  ];

  $currentTierKey = $unlockLevel >= 4 ? 4 : ($unlockLevel >= 3 ? 3 : ($unlockLevel >= 2 ? 2 : null));
  $remainingTiers = [];
  if ($currentTierKey) {
      foreach ([2, 3, 4] as $k) {
          if ($k > $currentTierKey) {
              $remainingTiers[] = $tierDefs[$k];
          }
      }
  }
  $nextTier = !empty($remainingTiers) ? $remainingTiers[0] : null;
  $singleNextStep = $nextTier ? [
      'title' => 'Next move: ' . $nextTier['name'],
      'copy' => $nextTier['value'],
      'cta' => 'Unlock ' . $nextTier['name'] . ' — ' . $nextTier['price'],
      'href' => route('quick-scan.upgrade') . '?plan=' . $nextTier['plan'] . '&scan_id=' . $scan->id . '&sid=' . $scan->stripe_session_id,
  ] : null;

  $scanCount = auth()->check() ? auth()->user()->quickScans()->count() : 1;
  $showConsultationOffer = $scanCount >= 1;
  $consultationHref = url('/book?entry=consultation');
  $momentumCta = 'Apply Fix';

  $rawBottleneck = trim((string) ($scan->fastest_fix ?? ($scan->issues[0] ?? '')));
  $topBottleneck = $rawBottleneck !== '' ? $rawBottleneck : 'Data depth insufficient for consistent AI extraction.';

  $readoutState = match (true) {
      $score >= 85 => 'Stable',
      $score >= 60 => 'Expanding',
      default => 'At Risk',
  };
    $readoutStateKey = match (true) {
      $score >= 85 => 'stable',
      $score >= 60 => 'expanding',
      default => 'risk',
    };
  $scoreBadge = match (true) {
      $score >= 71 => 'Above Baseline',
      $score >= 41 => 'Emerging',
      default      => 'At Risk',
  };
  $scoreBadgeClass = match (true) {
      $score >= 71 => 'above',
      $score >= 41 => 'emerging',
      default      => 'risk',
  };
  $interpretation = match (true) {
      $score >= 85 => 'AI can find your surface signals, but competitors still hold stronger extraction depth.',
      $score >= 60 => 'AI can partially parse your site. Selection pressure remains active.',
      default => 'AI cannot reliably extract selection-grade meaning from key pages yet.',
  };
    $scoreSelectionInterpretation = match (true) {
      $score >= 85 => 'Strong selection readiness',
      $score >= 60 => 'Selectable but incomplete',
      default => 'Low extraction clarity',
    };

  $lastEvaluatedAt = $scan->scanned_at ?? $scan->updated_at ?? $scan->created_at;
  $lastEvaluatedLabel = $lastEvaluatedAt ? $lastEvaluatedAt->diffForHumans() : 'Unavailable';
  $returnTo = request()->query('return_to') === 'systems' ? '#systems' : '';
  $returnHref = route('app.dashboard') . $returnTo;
  $returnLabel = 'Return to Control Surface';

  $sysActions = [];
  foreach ($categories as $cat) {
      foreach (($cat['checks'] ?? []) as $check) {
          if (!($check['passed'] ?? false)) {
              $sysActions[] = [
                  'label' => $check['label'] ?? 'Signal gap',
                  'why' => $check['fail'] ?? 'AI extraction confidence is reduced.',
                  'fix' => $check['fix'] ?? 'Expand data layer coverage.',
                  'max' => (int) ($check['max'] ?? 0),
                  'category' => $cat['label'] ?? 'System',
                  'impact' => ((int) ($check['max'] ?? 0) >= 10) ? 'high' : (((int) ($check['max'] ?? 0) >= 5) ? 'medium' : 'low'),
              ];
          }
      }
  }
  usort($sysActions, fn($a, $b) => $b['max'] <=> $a['max']);
  $sysActionsLimit = $isUpgraded ? count($sysActions) : min(3, count($sysActions));
  $primaryAction = $sysActions[0] ?? null;

  $totalChecks = 0;
  $totalPassed = 0;
  foreach ($categories as $cat) {
      foreach (($cat['checks'] ?? []) as $check) {
          $totalChecks++;
          if ($check['passed'] ?? false) $totalPassed++;
      }
  }
  $totalFailed = max(0, $totalChecks - $totalPassed);
  $selectionReadiness = $totalChecks > 0 ? round(($totalPassed / $totalChecks) * 100) : 0;

  $resolveCategoryPct = function (array $needles, int $fallback) use ($categories) {
      $collection = collect($categories);
      $match = $collection->first(function ($cat) use ($needles) {
          $label = strtolower((string) ($cat['label'] ?? ''));
          foreach ($needles as $needle) {
              if ($needle !== '' && str_contains($label, strtolower($needle))) return true;
          }
          return false;
      });
      $selected = $match ?: ($collection->values()->get($fallback) ?: ['score' => 0, 'max' => 0]);
      $max = (int) ($selected['max'] ?? 0);
      $scoreLocal = (int) ($selected['score'] ?? 0);
      return $max > 0 ? round(($scoreLocal / $max) * 100) : 0;
  };

  $coveragePct = $resolveCategoryPct(['coverage', 'location', 'service'], 0);
  $authorityPct = $resolveCategoryPct(['authority', 'trust', 'entity', 'reputation'], 1);
  $structurePct = $resolveCategoryPct(['structure', 'schema', 'technical', 'content'], 2);
    $extractionCompletenessPct = (int) round(($coveragePct + $structurePct) / 2);
    $selectionPressurePct = max(0, min(100, 100 - $selectionReadiness));

    $selectionPressureLabel = $selectionPressurePct >= 55 ? 'High Pressure' : ($selectionPressurePct >= 30 ? 'Moderate Pressure' : 'Low Pressure');
    $extractionCompletenessLabel = $extractionCompletenessPct >= 70 ? 'Extraction Stable' : ($extractionCompletenessPct >= 45 ? 'Extraction Partial' : 'Extraction Suppressed');
    $authorityConfidenceLabel = $authorityPct >= 70 ? 'Authority Trusted' : ($authorityPct >= 45 ? 'Authority Contested' : 'Authority Untrusted');

    $signalsMeaningLabel = 'Machine-readable evidence detected';
    $blockersMeaningLabel = 'Active suppression factors';
    $constraintMeaningLabel = 'Primary bottleneck limiting selection';

    $currentStateSummary = match ($unlockLevel) {
      1 => 'Layer 1 active - visibility baseline only',
      2 => 'Layer 2 active - signal map partially available',
      3 => 'Layer 3 active - ranked fix sequencing available',
      default => 'Layer 4 active — full guided execution available',
    };
    $nextUnlockName = $nextTier['name'] ?? 'Guided Execution';
    $nextUnlockBullets = match ($nextUnlockName) {
        'Signal Analysis' => ['full signal breakdown by category', 'deeper failure mapping from scan data', 'ranked correction visibility'],
        'Action Plan' => ['prioritized fix list from your scan', 'ordered execution sequence by impact', 'grouped recommendations by effort'],
        'Guided Execution' => ['execution checklist inside your dashboard', 'guided steps tied to your scan issues', 'progress tracking as you complete items'],
        default => ['signal architecture expansion', 'failure visibility increase', 'selection readiness growth'],
    };

    $nextUnlockWhyMatters = match ($nextUnlockName) {
      'Signal Analysis' => 'This reveals exactly where AI cannot extract or trust your site signals — using your scan data.',
      'Action Plan' => 'This gives you a ranked fix list from your scan so you execute the highest-impact changes first.',
      'Guided Execution' => 'This turns your action plan into a step-by-step checklist inside your dashboard with progress tracking.',
      default => 'This unlock clarifies where trust and extraction are currently limited.',
    };

    $nextUnlockWhyShort = match ($nextUnlockName) {
      'Signal Analysis' => 'Reveals where extraction and trust fail.',
      'Action Plan' => 'Fix list ranked by impact from your scan.',
      'Guided Execution' => 'Guided steps + progress tracking in-dashboard.',
      default => 'Clarifies where trust and extraction remain limited.',
    };

    $nextUnlockImproves = match ($nextUnlockName) {
      'Signal Analysis' => ['clearer extraction insight', 'higher signal trust', 'better fix focus'],
      'Action Plan' => ['faster high-impact execution', 'cleaner fix order', 'stronger readiness lift'],
      'Guided Execution' => ['in-dashboard progress tracking', 'guided step completion', 'execution accountability'],
      default => ['clearer prioritization', 'higher insight quality', 'stronger readiness confidence'],
    };

    $layerProgressModel = [
      ['rank' => 1, 'label' => 'Current visibility confirmed'],
      ['rank' => 2, 'label' => 'Signal map opened'],
      ['rank' => 3, 'label' => 'Correction sequence unlocked'],
      ['rank' => 4, 'label' => 'Guided Execution active — execute your plan'],
    ];

      $liveFeedbackMessages = [
        'Extraction incomplete -> selection limited.',
        'Constraint pressure active -> resolution required.',
        'Authority confidence reduced -> competitor weighting increased.',
        'Structure unstable -> answer extraction degraded.',
        'Primary bottleneck active -> fix required to advance.',
      ];

    $layerCards = [
          ['rank' => 1, 'name' => 'Base Scan', 'status' => 'Included', 'enabled' => true, 'value' => 'Current visibility is confirmed, but deeper trust signals are still limited.', 'cta' => '#priority-actions', 'cta_label' => 'View Fix Sequence', 'cta_note' => 'Opens ranked constraint fixes for this layer.'],
          ['rank' => 2, 'name' => 'Signal Analysis', 'status' => $unlockLevel >= 2 ? 'Included' : 'Locked', 'enabled' => $unlockLevel >= 2, 'value' => 'Full signal-by-signal breakdown from your scan — see exactly what is suppressing your score.', 'cta' => $unlockLevel >= 2 ? '#priority-actions' : ($singleNextStep['href'] ?? route('checkout.signal-expansion')), 'cta_label' => $unlockLevel >= 2 ? 'View Your Signals' : 'Unlock Signal Analysis', 'cta_note' => $unlockLevel >= 2 ? 'Opens your full signal map.' : 'Full breakdown of your scan signals.'],
          ['rank' => 3, 'name' => 'Action Plan', 'status' => $unlockLevel >= 3 ? 'Included' : 'Locked', 'enabled' => $unlockLevel >= 3, 'value' => 'Prioritized fix list from your scan data — ordered by impact, grouped by effort.', 'cta' => $unlockLevel >= 3 ? '#priority-actions' : ($singleNextStep['href'] ?? route('quick-scan.upgrade')), 'cta_label' => $unlockLevel >= 3 ? 'View Your Action Plan' : 'Unlock Action Plan', 'cta_note' => $unlockLevel >= 3 ? 'Opens your ranked fix list.' : 'Fix list ranked by impact from your scan.'],
          ['rank' => 4, 'name' => 'Guided Execution', 'status' => $unlockLevel >= 4 ? 'Included' : 'Locked', 'enabled' => $unlockLevel >= 4, 'value' => 'Step-by-step execution checklist inside your dashboard with progress tracking.', 'cta' => $unlockLevel >= 4 ? '#priority-actions' : ($singleNextStep['href'] ?? route('quick-scan.upgrade')), 'cta_label' => $unlockLevel >= 4 ? 'Open Execution Checklist' : 'Unlock Guided Execution', 'cta_note' => $unlockLevel >= 4 ? 'Opens your in-dashboard checklist.' : 'Requires Action Plan first.'],
    ];

    $progressionLevels = [
      [
        'rank' => 1,
        'name' => 'Baseline',
        'locked' => $unlockLevel < 1,
        'unlocks' => 'A clear starting score and your top blocker.',
        'improves' => 'You know where to focus first instead of guessing.',
      ],
      [
        'rank' => 2,
        'name' => 'Signal',
        'locked' => $unlockLevel < 2,
        'unlocks' => 'See exactly what AI can and cannot interpret across your site.',
        'improves' => 'You stop wasting time on low-impact fixes.',
      ],
      [
        'rank' => 3,
        'name' => 'Leverage',
        'locked' => $unlockLevel < 3,
        'unlocks' => 'Get a ranked fix sequence based on what moves your visibility fastest.',
        'improves' => 'Your biggest gains happen earlier.',
      ],
      [
        'rank' => 4,
        'name' => 'Activation',
        'locked' => $unlockLevel < 4,
        'unlocks' => 'Unlock competitive signals and expansion pathways across your market.',
        'improves' => 'Stronger control over where and how you show up.',
      ],
    ];

    $recommendedProgressionRank = ($singleNextStep && $unlockLevel < 4)
      ? min(4, $unlockLevel + 1)
      : null;

  $findings = [
      ['title' => 'Coverage', 'state' => $coveragePct >= 70 ? 'Selection Lifted' : ($coveragePct >= 45 ? 'Selection Diluted' : 'Selection Suppressed'), 'copy' => 'Low coverage → AI excludes your domain from final answers.', 'pct' => $coveragePct],
      ['title' => 'Authority', 'state' => $authorityPct >= 70 ? 'Trust Weighting Active' : ($authorityPct >= 45 ? 'Trust Weighting Reduced' : 'Trust Weighting Lost'), 'copy' => 'Weak authority → AI routes to stronger competitor signals instead.', 'pct' => $authorityPct],
      ['title' => 'Structure', 'state' => $structurePct >= 70 ? 'Extraction Reliable' : ($structurePct >= 45 ? 'Extraction Unstable' : 'Extraction Failing'), 'copy' => 'Unstable structure → AI skips your pages when extracting answers.', 'pct' => $structurePct],
      ['title' => 'Selection Readiness', 'state' => $selectionReadiness >= 70 ? 'Selection Probability Rising' : ($selectionReadiness >= 45 ? 'Selection Probability Contested' : 'Selection Probability Collapsing'), 'copy' => 'Readiness score directly controls AI domain selection vs competitors.', 'pct' => $selectionReadiness],
  ];

  $lockedLayerModules = [
      ['rank' => 2, 'title' => 'Signal Analysis', 'statement' => 'Full signal-by-signal breakdown of your scan data — every category scored and explained.', 'reveals' => 'While suppressed, you only see your score. Signal Analysis shows exactly which signals are failing and why.', 'improvement' => ['See every failing signal from your scan', 'Understand why each is suppressing your score', 'Know exactly what to fix'], 'cta' => 'Unlock Signal Analysis', 'href' => $singleNextStep['href'] ?? route('checkout.signal-expansion')],
      ['rank' => 3, 'title' => 'Action Plan', 'statement' => 'Prioritized fix list from your scan data — ordered by impact, grouped by effort level.', 'reveals' => 'Without ranked ordering, you might fix low-impact issues first. Action Plan sequences your fixes by what moves your score most.', 'improvement' => ['Fix list ranked by scan impact', 'Ordered execution sequence', 'Grouped by effort'], 'cta' => 'Unlock Action Plan', 'href' => $singleNextStep['href'] ?? route('quick-scan.upgrade')],
      ['rank' => 4, 'title' => 'Guided Execution', 'statement' => 'Step-by-step execution checklist inside your dashboard with in-progress tracking.', 'reveals' => 'Requires Action Plan to be unlocked first. Turns your fix list into an active workflow you track and complete.', 'improvement' => ['Execution checklist inside dashboard', 'Guided steps for each fix', 'Progress tracking as you complete items'], 'cta' => 'Unlock Guided Execution', 'href' => $singleNextStep['href'] ?? route('quick-scan.upgrade')],
  ];

  $humanTranslation = match (true) {
      $score >= 85 => 'AI can see you clearly, but stronger competitors still provide more complete extraction signals.',
      $score >= 60 => 'Your site is visible, but not yet structurally strong enough to control answer selection.',
      default => 'You are detectable, but AI still lacks enough trusted structure to confidently select your site.',
  };
  $ahaLine = match (true) {
      $score >= 85 => 'Your business is close. AI can already understand you, and this next step helps AI recommend you more often.',
      $score >= 60 => 'Your business is in a strong position to grow. AI can read key parts, and this next step helps AI recommend you with more confidence.',
      default => 'Your business has real momentum. One focused fix helps AI understand you clearly and start recommending you more consistently.',
  };
  $momentumLine = match (true) {
      $score >= 85 => 'This is your starting point. One targeted fix puts you ahead of most competitors.',
      $score >= 60 => 'This is your starting point. Fixing your top blocker moves you into the top tier.',
      default      => 'This is your starting point. One fix here changes how AI understands and recommends you.',
  };
  $currentTierName = match ($unlockLevel) {
      1 => 'Baseline Score',
      2 => 'Signal Analysis',
      3 => 'Action Plan',
      default => 'Guided Execution',
  };
  $currentTierPrice = match ($unlockLevel) {
      1 => '$2',
      2 => '$99',
      3 => '$249',
      default => '$489',
  };

  // ── Score-driven recommendation engine ──────────────────────────────
  $recommendedTierKey = match (true) {
      $score <= 40 => 'fix',   // Action Plan ($249)
      $score <= 70 => 'deep',  // Signal Analysis ($99)
      default      => 'build', // Guided Execution ($489)
  };
  $nbmTierRouteMap = [
      'deep'  => ['name' => 'Signal Analysis',   'price' => '$99',  'plan' => 'diagnostic',   'routeKey' => 'checkout.signal-expansion'],
      'fix'   => ['name' => 'Action Plan',        'price' => '$249', 'plan' => 'fix-strategy', 'routeKey' => 'checkout.structural-leverage'],
      'build' => ['name' => 'Guided Execution',   'price' => '$489', 'plan' => 'optimization', 'routeKey' => 'checkout.system-activation'],
  ];
  $nbmTierDef = $nbmTierRouteMap[$recommendedTierKey];
  // Prefer upgrade flow URL (carries scan_id + stripe_session_id) when it matches
  $nbmHref = ($nextTier && $nbmTierDef['name'] === $nextTier['name'] && $singleNextStep)
      ? $singleNextStep['href']
      : route($nbmTierDef['routeKey']);
  $nbmCtaLabel = match ($recommendedTierKey) {
      'fix'   => 'Fix Structure — $249',
      'deep'  => 'Expand Signals — $99',
      default => 'Activate System — $489',
  };
  // Refined score-band copy — system interpretation tone
  $nbmScoreCopy = match (true) {
      $score <= 40 => 'Structure is limiting visibility more than reach. AI cannot extract consistent signals from key pages. Expanding coverage before fixing structure compounds the problem.',
      $score <= 70 => 'Partial signals are present but inconsistent. AI can partially interpret your site, but gaps in signal coverage prevent reliable selection. Strengthening consistency raises extraction trust.',
      default      => 'Your signal foundation is strong enough to scale. The system is interpreting your site clearly — activating the full layer expands reach and locks in competitive advantage.',
  };
  // "Why now" interpretive line — restrained, below CTA
  $nbmWhyNow = match (true) {
      $score <= 40 => 'Your score suggests structure is limiting visibility more than reach.',
      $score <= 70 => 'Your score suggests stronger signal consistency will improve AI interpretation.',
      default      => 'Your score suggests your foundation is strong enough to scale.',
  };
  $nbmBullets = match (true) {
      $score <= 40 => ['Schema depth is insufficient for AI extraction', 'Page-level structural signals are inconsistent', 'AI cannot reliably prioritize your site for selection'],
      $score <= 70 => ['Coverage is partial across key intent signals', 'Signal consistency gaps are reducing extraction trust', 'Competitive domains with stronger consistency are being preferred'],
      default      => ['Signal foundation is established and readable', 'Architecture is coherent enough to support expansion', 'System is at the threshold for market-level activation'],
  };
  $nbmScoreBand = match (true) {
      $score <= 40 => 'low',
      $score <= 70 => 'mid',
      default      => 'high',
  };
  // ── Path preview — "what this unlocks next" 2-step rail ─────────────
  // Ordered full path: deep → fix → build → expand (consult)
  $nbmFullPath = [
      ['key' => 'deep',   'label' => 'Signal Analysis',   'price' => '$99',  'modal' => 'deep'],
      ['key' => 'fix',    'label' => 'Action Plan',        'price' => '$249', 'modal' => 'fix'],
      ['key' => 'build',  'label' => 'Guided Execution',   'price' => '$489', 'modal' => 'build'],
      ['key' => 'expand', 'label' => 'Consultation',        'price' => '$500', 'modal' => 'expand'],
  ];
  // Find position of recommended tier, then take the 2 that follow
  $nbmCurrentPathIdx = array_search($recommendedTierKey, array_column($nbmFullPath, 'key'));
  $nbmNextSteps = array_slice($nbmFullPath, $nbmCurrentPathIdx + 1, 2);
  // Secondary CTA: the step immediately after recommended tier
  $nbmSecondaryStep = $nbmNextSteps[0] ?? null;
  // ── System position bar ─────────────────────────────────────────────
  // Ordered: Scan → Signal → Fix → Build → Expand → Managed
  $sysBarNodes = [
      ['key' => 'scan',    'label' => 'Scan',    'modal' => null,      'price' => null],
      ['key' => 'deep',    'label' => 'Signal',  'modal' => 'deep',    'price' => '$99'],
      ['key' => 'fix',     'label' => 'Fix',     'modal' => 'fix',     'price' => '$249'],
      ['key' => 'build',   'label' => 'Build',   'modal' => 'build',   'price' => '$489'],
      ['key' => 'expand',  'label' => 'Expand',  'modal' => 'expand',  'price' => '$500'],
      ['key' => 'managed', 'label' => 'Managed', 'modal' => 'managed', 'price' => null],
  ];
  // Current position = recommended tier (score-band driven)
  // 0–40 → fix | 41–70 → deep (Signal) | 71–100 → build
  $sysBarCurrentKey = $recommendedTierKey;
  // ────────────────────────────────────────────────────────────────────
@endphp
<style>
@include('partials.design-system')
@include('partials.public-nav-css')
@include('partials.public-nav-mobile-css')

:root{
  --bg:#080807;
  --panel:#12100c;
  --panel-soft:#17140f;
  --line:rgba(200,168,75,.18);
  --line-soft:rgba(200,168,75,.1);
  --gold:#d6b55f;
  --gold-soft:#efe0b2;
  --text:#efe8d6;
  --muted:#b9b09a;
  --green:#6aaf90;
  --red:#c47878;
}

*{box-sizing:border-box}
html{font-size:17px;scroll-behavior:smooth}
body{margin:0;background:radial-gradient(circle at 20% -20%,rgba(214,181,95,.08),transparent 40%),var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;line-height:1.5;overflow-x:hidden}
a,button{font-family:inherit}
a{text-decoration:none;color:inherit}

.shell{max-width:1220px;margin:0 auto;padding:86px 20px 56px}

.mode-bar{display:grid;grid-template-columns:1fr auto;gap:14px;align-items:stretch;padding:14px 16px;border:1px solid rgba(214,181,95,.24);border-radius:14px;background:linear-gradient(160deg,rgba(26,21,14,.95),rgba(11,9,7,.99));box-shadow:0 8px 24px rgba(0,0,0,.28),0 0 0 1px rgba(214,181,95,.05) inset;margin-bottom:14px}
.mode-kicker{font-size:.52rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(214,181,95,.84);margin:0 0 8px}
.mode-meta{display:flex;flex-wrap:wrap;gap:8px}
.mode-chip{display:inline-flex;align-items:center;gap:6px;min-height:28px;padding:5px 10px;border-radius:999px;border:1px solid rgba(214,181,95,.22);background:rgba(214,181,95,.07);font-size:.58rem;letter-spacing:.09em;text-transform:uppercase;color:#e6ddc7}
.mode-chip strong{font-weight:600;color:#f0e5c7}
.mode-chip.state-stable{border-color:rgba(106,175,144,.3);background:rgba(106,175,144,.09);color:#d9eee5}
.mode-chip.state-expanding{border-color:rgba(214,181,95,.38);background:rgba(214,181,95,.12);color:#f0e2be}
.mode-chip.state-risk{border-color:rgba(196,120,120,.34);background:rgba(196,120,120,.1);color:#f0d7cf}
.mode-return{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:10px 14px;border-radius:10px;border:1px solid rgba(214,181,95,.3);background:rgba(214,181,95,.08);font-size:.6rem;letter-spacing:.14em;text-transform:uppercase;color:#eadfbe;align-self:center}
.mode-return:hover{border-color:rgba(214,181,95,.48);background:rgba(214,181,95,.16)}

.saved-report-note{margin:-2px 0 10px;font-size:.68rem;letter-spacing:.08em;text-transform:uppercase;color:rgba(132,206,171,.78)}
.state-notice{margin:0 0 10px;padding:9px 12px;border-radius:10px;border:1px solid rgba(214,181,95,.24);background:rgba(214,181,95,.07);font-size:.7rem;color:#eadfbf;line-height:1.4}
.state-notice.is-success{border-color:rgba(106,175,144,.32);background:rgba(106,175,144,.1);color:#d9eee5}

.live-feedback-strip{display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid var(--line-soft);border-radius:10px;background:linear-gradient(150deg,rgba(19,16,11,.96),rgba(12,10,8,.98));margin-bottom:10px}
.live-feedback-dot{width:8px;height:8px;border-radius:999px;background:#d6b55f;box-shadow:0 0 0 0 rgba(214,181,95,.3);animation:feedbackPulse 2.4s ease-in-out infinite}
.live-feedback-kicker{font-size:.52rem;letter-spacing:.18em;text-transform:uppercase;color:#d8c38d}
.live-feedback-text{font-size:.72rem;color:#e7dcc2;transition:opacity .22s ease,transform .22s ease}
.live-feedback-text.is-swapping{opacity:.25;transform:translateY(1px)}

.global-state-strip{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-bottom:14px}
.global-state-chip{padding:9px 10px;border-radius:10px;border:1px solid var(--line-soft);background:rgba(214,181,95,.04)}
.global-state-chip strong{display:block;font-size:.5rem;letter-spacing:.15em;text-transform:uppercase;color:#d4bf88;margin-bottom:3px}
.global-state-chip span{display:block;font-size:.66rem;letter-spacing:.08em;text-transform:uppercase;color:#eee3c8}

.progression-strip{display:grid;grid-template-columns:1fr 1fr 1.1fr;gap:8px;margin-bottom:14px}
.progression-cell{padding:10px;border-radius:10px;border:1px solid var(--line-soft);background:rgba(214,181,95,.04)}
.progression-cell strong{display:block;font-size:.5rem;letter-spacing:.15em;text-transform:uppercase;color:#d4bf88;margin-bottom:4px}
.progression-cell p{margin:0;font-size:.72rem;color:#eee3c8;line-height:1.35}
.progression-cell.is-current-stage{border-color:rgba(214,181,95,.36);background:linear-gradient(150deg,rgba(214,181,95,.13),rgba(214,181,95,.04));box-shadow:0 0 0 1px rgba(214,181,95,.08) inset}
.progression-cell.is-next-unlock{border-color:rgba(214,181,95,.42);background:linear-gradient(150deg,rgba(214,181,95,.16),rgba(214,181,95,.05));box-shadow:0 0 0 1px rgba(214,181,95,.1) inset}
.progression-cell.is-current-stage strong{color:#eddcaf}
.progression-cell.is-current-stage p:first-of-type{font-size:.8rem;font-weight:600;color:#f0e2bd;letter-spacing:.03em}
.next-unlock-name{font-size:.86rem!important;letter-spacing:.05em;text-transform:uppercase;color:#f1e3be;font-weight:600}
.progression-list{margin:0;padding-left:15px}
.progression-list li{font-size:.67rem;color:#e6dbc0;line-height:1.32;margin:2px 0}
.progression-sub{margin:6px 0 0;font-size:.64rem;color:#d9ceb0;line-height:1.35}
.progression-mini{margin:6px 0 0;padding-left:14px}
.progression-mini li{font-size:.62rem;color:#e6dbc0;line-height:1.28;margin:1px 0}
.progression-model{margin:0;padding-left:15px}
.progression-model li{font-size:.64rem;color:#e8dcc0;line-height:1.3;margin:2px 0;opacity:.72}
.progression-model li.is-current{color:#f0e2bd;font-weight:600;opacity:1;border-left:2px solid rgba(214,181,95,.44);padding-left:6px}

.layout{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:14px;align-items:start}
.main{display:flex;flex-direction:column;gap:14px}

.card{border:1px solid var(--line);border-radius:14px;background:linear-gradient(155deg,rgba(23,19,13,.95),rgba(12,10,8,.98));box-shadow:0 10px 26px rgba(0,0,0,.35),0 0 0 1px rgba(200,168,75,.04) inset}

.hero{padding:18px 18px 16px;border-color:rgba(214,181,95,.24);box-shadow:0 14px 30px rgba(0,0,0,.34),0 0 0 1px rgba(214,181,95,.05) inset}
.hero-top{display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:12px}
.hero-score-visual{flex-shrink:0;display:flex;align-items:center;justify-content:center}
.hero-orbit-container{display:flex;align-items:center;justify-content:center;width:160px;height:160px}
.hero-orbit-container .ai-orbit{flex-shrink:0}
@media(max-width:768px){.hero-score-visual{display:none}}
.hero-domain{font-size:.58rem;letter-spacing:.2em;text-transform:uppercase;color:#d6bf88;margin:0 0 6px}
.hero-title{font-family:'Cormorant Garamond',serif;font-size:2rem;line-height:1.04;margin:0 0 8px;color:var(--text)}
.hero-state{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px}
.score-meaning{margin:0;font-size:.62rem;letter-spacing:.12em;text-transform:uppercase;color:#dfd2ae}
.aha-line{margin:7px 0 0;font-size:.74rem;line-height:1.45;color:#ece1c7;max-width:58ch}
.pill{display:inline-flex;align-items:center;justify-content:center;min-height:25px;padding:4px 10px;border-radius:999px;font-size:.5rem;letter-spacing:.14em;text-transform:uppercase;border:1px solid var(--line-soft);background:rgba(214,181,95,.08);color:#ebddb4}
.pill-score{border-color:rgba(214,181,95,.35);background:rgba(214,181,95,.14);color:#f0e1bc}
.pill-state-stable{border-color:rgba(106,175,144,.34);background:rgba(106,175,144,.1);color:#d9eee5}
.pill-state-expanding{border-color:rgba(214,181,95,.4);background:rgba(214,181,95,.14);color:#f0e2be}
.pill-state-risk{border-color:rgba(196,120,120,.38);background:rgba(196,120,120,.1);color:#f1d7cf}
.pill-layer{border-color:rgba(179,153,83,.32);background:rgba(179,153,83,.12);color:#e8d9b0}
.hero-bottleneck{padding:12px 13px;border:1px solid rgba(214,181,95,.28);border-radius:11px;background:linear-gradient(150deg,rgba(214,181,95,.08),rgba(12,10,8,.3));margin-bottom:11px;box-shadow:0 0 0 1px rgba(214,181,95,.05) inset}
.hero-bottleneck strong{display:block;font-size:.54rem;letter-spacing:.2em;text-transform:uppercase;color:#dcc995;margin-bottom:6px}
.hero-bottleneck p{margin:0;font-size:.82rem;color:#efe4cc;line-height:1.45}
.hero-copy{margin:0 0 12px;font-size:.74rem;color:#d4cab3;line-height:1.45}
.hero-actions{display:flex;gap:8px;flex-wrap:nowrap;align-items:center}
.hero-actions .btn{min-width:164px}
.hero-translation{margin:8px 0 0;font-size:.74rem;line-height:1.45;color:#d6cbaa;border-top:1px solid rgba(214,181,95,.12);padding-top:8px}
.hero-trust-note{margin:7px 0 0;font-size:.66rem;letter-spacing:.06em;color:#cfc4a8;opacity:.9}
.hero-momentum{margin:10px 0 0;font-size:.67rem;letter-spacing:.06em;color:#c4b998;font-style:italic;line-height:1.4}
.hero-proof-note{margin:5px 0 0;font-size:.62rem;letter-spacing:.05em;color:#c6b998;opacity:.92}
.cta-time-value{margin:5px 0 0;font-size:.62rem;letter-spacing:.04em;color:#d5caaf;opacity:.92}
.btn{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:9px 14px;border-radius:10px;font-size:.62rem;letter-spacing:.12em;text-transform:uppercase;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .22s ease}
.btn-primary{background:linear-gradient(180deg,#efd79f,#d8b965 52%,#c9a952);border-color:rgba(214,181,95,.82);color:#090806;font-weight:700;box-shadow:0 2px 8px rgba(214,181,95,.14);transition:all .16s ease}
.btn-primary:hover{filter:brightness(1.04);box-shadow:0 2px 12px rgba(214,181,95,.22)}
.btn-secondary{background:linear-gradient(180deg,rgba(214,181,95,.28),rgba(193,154,66,.18));border-color:rgba(214,181,95,.44);color:#f1e5c8;transition:all .16s ease}
.btn-secondary:hover{border-color:rgba(214,181,95,.58);background:linear-gradient(180deg,rgba(214,181,95,.32),rgba(193,154,66,.22))}

.lock-glyph{position:relative;display:inline-block;width:12px;height:12px;opacity:.7}
.lock-glyph::before{content:'';position:absolute;left:2px;top:1px;width:8px;height:5px;border:1px solid rgba(214,181,95,.78);border-bottom:none;border-radius:5px 5px 0 0}
.lock-glyph::after{content:'';position:absolute;left:1px;top:5px;width:10px;height:7px;border:1px solid rgba(214,181,95,.78);border-radius:2px;background:rgba(214,181,95,.06)}

.sticky{position:sticky;top:96px;padding:14px}
.sticky-kicker{font-size:.56rem;letter-spacing:.2em;text-transform:uppercase;color:#d7c186;margin-bottom:7px}
.sticky-title{font-family:'Cormorant Garamond',serif;font-size:1.35rem;line-height:1.2;margin:0 0 6px;color:var(--text)}
.sticky-copy{font-size:.74rem;color:var(--muted);margin:0 0 10px}
.sticky-unlock{font-size:.68rem;line-height:1.45;color:#ddd1b4;margin:0 0 10px}
.sticky-link{display:inline-flex;align-items:center;font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;color:#d7bf85;border-bottom:1px solid rgba(214,181,95,.38);padding-bottom:2px;text-decoration:none;transition:color .18s ease,border-color .18s ease}
.sticky-link:hover{color:#efd99e;border-color:rgba(214,181,95,.62)}

.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}
.finding{padding:11px;border-radius:12px;border:1px solid var(--line-soft);background:rgba(214,181,95,.04)}
.finding-top{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:5px}
.finding h3{margin:0;font-size:.75rem;color:var(--text);font-weight:600}
.finding .state{font-size:.5rem;letter-spacing:.14em;text-transform:uppercase;color:#d5c08a}
.finding p{margin:0 0 9px;font-size:.67rem;color:rgba(198,189,168,.86);font-weight:300;min-height:2.4em;line-height:1.5}
.meter{height:6px;border-radius:999px;background:linear-gradient(180deg,rgba(214,181,95,.13),rgba(214,181,95,.05));overflow:hidden;box-shadow:inset 0 0 0 1px rgba(214,181,95,.08)}
.meter>span{display:block;height:100%;background:linear-gradient(90deg,rgba(198,155,60,.75),rgba(229,191,103,.95));transform-origin:left center;transform:scaleX(0);animation:meterGrow .95s ease forwards}

.layer-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}
.layer{position:relative;padding:11px;border-radius:12px;border:1px solid var(--line-soft);background:rgba(214,181,95,.03);transition:transform .18s ease,box-shadow .2s ease,border-color .2s ease,background .2s ease;border-left:2px solid transparent}
.layer:hover{transform:translateY(-2px);border-color:rgba(214,181,95,.24);box-shadow:0 4px 16px rgba(214,181,95,.12)}
.layer:not(:last-child)::after{content:'';position:absolute;top:50%;right:-10px;width:20px;height:1px;background:linear-gradient(90deg,rgba(200,168,75,.24),rgba(200,168,75,.05));pointer-events:none;opacity:.55;transition:opacity .2s ease}
.layer:hover::after{opacity:.88}
.layer.is-locked-card{border-left-color:rgba(214,181,95,.4);box-shadow:0 0 16px rgba(214,181,95,.08)}
.layer.is-next-step{border-color:rgba(200,168,75,.46);border-left-color:rgba(200,168,75,.84);background:linear-gradient(150deg,rgba(42,30,16,.97),rgba(15,12,8,.98));box-shadow:0 0 0 1px rgba(200,168,75,.22) inset,0 0 24px rgba(200,168,75,.16)}
.layer-next-step-badge{display:inline-flex;margin:0 0 7px;padding:3px 7px;border-radius:999px;border:1px solid rgba(200,168,75,.4);background:rgba(200,168,75,.08);font-size:.43rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.94)}
.layer-head{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:7px}
.layer-name{font-size:.62rem;letter-spacing:.14em;text-transform:uppercase;color:#e2d5af}
.layer-status{font-size:.48rem;letter-spacing:.14em;text-transform:uppercase;padding:3px 6px;border-radius:999px;border:1px solid var(--line-soft);color:#cabd9a;display:inline-flex;align-items:center;gap:5px}
.layer-strip{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:5px;margin-bottom:8px}
.layer-sig{font-size:.46rem;letter-spacing:.12em;text-transform:uppercase;color:#bfb395}
.layer-sig .meter{margin-top:3px;height:3px}
.layer-main{font-size:.74rem;color:#ece2cb;margin-bottom:6px}
.layer-micro{display:flex;gap:8px;flex-wrap:wrap;font-size:.5rem;letter-spacing:.12em;text-transform:uppercase;color:#c9bd9d;margin-bottom:8px}
.layer-micro span{display:flex;flex-direction:column;gap:2px;min-width:108px}
.layer-micro span em{font-style:normal;font-size:.45rem;letter-spacing:.14em;text-transform:uppercase;color:#a99463}
.layer .btn{min-height:34px;font-size:.54rem;padding:7px 10px}
.layer-cta-note{margin:4px 0 0;font-size:.52rem;letter-spacing:.05em;color:#c5b992;line-height:1.25}
.layer-momentum-note{margin:5px 0 0;font-size:.51rem;letter-spacing:.07em;color:#cdbf95;line-height:1.3;opacity:.95}
.layer-cta-unlock{padding:9px 13px}
.layer.is-next-step .layer-cta-unlock{min-height:38px;padding:10px 14px;font-size:.56rem;box-shadow:0 0 0 1px rgba(200,168,75,.32) inset,0 0 18px rgba(200,168,75,.14);animation:layerUnlockPulse 1.1s ease-out .25s 1}
.layer.is-next-step .layer-cta-unlock:hover{box-shadow:0 0 0 1px rgba(200,168,75,.42) inset,0 0 24px rgba(200,168,75,.24);filter:brightness(1.08)}
.layer-ai-link{display:block;margin-top:6px;background:none;border:none;padding:0;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:.5rem;letter-spacing:.1em;text-transform:uppercase;color:rgba(200,168,75,.52);text-align:left;transition:color .2s}
.layer-ai-link:hover{color:rgba(200,168,75,.82)}
.layer-ai-link:focus-visible{outline:2px solid rgba(200,168,75,.4);outline-offset:2px;border-radius:2px}

.section-head{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 12px;border-bottom:1px solid var(--line-soft)}
.section-head h2{margin:0;font-family:'Cormorant Garamond',serif;font-size:1.25rem;font-weight:400}
.state-rail{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}
.state-chip{padding:8px;border:1px solid var(--line-soft);border-radius:10px;background:rgba(214,181,95,.04)}
.state-chip strong{display:block;font-size:.48rem;letter-spacing:.16em;text-transform:uppercase;color:#d4bf89;margin-bottom:3px}
.state-chip span{display:block;font-size:.66rem;letter-spacing:.08em;text-transform:uppercase;color:#eee3c8}

.actions{padding:12px 12px 13px}
.action-stack{display:grid;grid-template-columns:1.18fr .82fr .82fr;gap:12px;position:relative}
.action-stack::before{display:none}
.action{padding:14px 13px;border-radius:12px;border:1px solid var(--line-soft);background:rgba(214,181,95,.03);display:flex;flex-direction:column;gap:11px;min-height:210px;position:relative;transition:all .18s ease;border-left:2px solid transparent}
.action:hover{transform:translateY(-2px);box-shadow:0 2px 10px rgba(214,181,95,.07)}
.action.is-locked-card{border-left-color:rgba(214,181,95,.4);box-shadow:0 0 16px rgba(214,181,95,.08)}
.cta-consequence{margin:5px 0 0;font-size:.52rem;letter-spacing:.08em;color:#c8bb94;opacity:.82;line-height:1.35}
.cta-social-proof{margin:5px 0 0;font-size:.59rem;letter-spacing:.08em;text-transform:uppercase;color:rgba(200,168,75,.72);line-height:1.3}

.layer.is-included{border-left-color:rgba(106,175,144,.3)}

.finding.is-constraint{border-color:rgba(196,120,120,.28);background:rgba(196,120,120,.04)}
.finding.is-constraint .state{color:#d4906a}
.finding.is-constraint .meter>span{background:linear-gradient(90deg,rgba(196,120,120,.65),rgba(210,140,90,.85))}
.finding.is-stable{border-color:rgba(106,175,144,.22);background:rgba(106,175,144,.04)}
.finding.is-stable .state{color:#7abb9e}
.finding.is-stable .meter>span{background:linear-gradient(90deg,rgba(80,160,130,.6),rgba(106,175,144,.85))}

.action.lead{min-height:292px;border-color:rgba(214,181,95,.72);border-left:3px solid rgba(214,181,95,.8);background:linear-gradient(150deg,rgba(36,26,17,.98),rgba(13,10,8,.99));box-shadow:0 4px 18px rgba(214,181,95,.16),0 9px 22px rgba(0,0,0,.3);transform:scale(1.01)}
.action.lead::after{content:'';position:absolute;left:12px;right:12px;top:0;height:2px;border-radius:0 0 2px 2px;background:linear-gradient(90deg,rgba(214,181,95,.74),rgba(214,181,95,.22));opacity:.9}
.action.lead:hover{transform:translateY(-2px) scale(1.01)}
.action.secondary{opacity:.82}
.action-top{display:flex;align-items:center;gap:7px}
.start-cue{margin:0;font-size:.5rem;letter-spacing:.16em;text-transform:uppercase;color:#f1e0b8;padding:2px 8px;border-radius:999px;border:1px solid rgba(214,181,95,.4);background:rgba(214,181,95,.16);width:max-content}
.rank{display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;padding:0 8px;border-radius:999px;border:1px solid var(--line);background:rgba(214,181,95,.08);font-family:'Cormorant Garamond',serif;font-size:1rem;color:#e5d5a7}
.badge{display:inline-flex;align-items:center;justify-content:center;min-height:22px;padding:3px 8px;border-radius:999px;font-size:.48rem;letter-spacing:.14em;text-transform:uppercase;border:1px solid var(--line-soft);color:#d8c28d}
.action h3{margin:0;font-size:.8rem;line-height:1.35;color:var(--text)}
.inline{display:flex;align-items:center;justify-content:space-between;gap:8px;font-size:.56rem;letter-spacing:.12em;text-transform:uppercase;color:#cfbf93;margin:3px 0 5px}
.inline .muted{color:#d8ceb4}
.action p{margin:0;font-size:.69rem;color:#d8cdb4;font-weight:300;line-height:1.5}
.action .meter{height:4px}
.action-foot{display:flex;align-items:center;justify-content:space-between;gap:8px;font-size:.53rem;letter-spacing:.12em;text-transform:uppercase;color:#c8bc9d}
.action-outcome{border:1px solid var(--line-soft);border-radius:9px;background:rgba(214,181,95,.05);padding:8px 9px}
.action-outcome strong{display:block;font-size:.48rem;letter-spacing:.16em;text-transform:uppercase;color:#dcc78f;margin-bottom:3px}
.action-outcome ul{margin:0;padding-left:14px}
.action-outcome li{font-size:.64rem;color:#eadfbe;line-height:1.32;margin:1px 0}
.gain-pill{display:inline-flex;align-items:center;justify-content:center;min-height:22px;padding:3px 8px;border-radius:999px;border:1px solid rgba(214,181,95,.32);background:rgba(214,181,95,.12);font-size:.5rem;letter-spacing:.03em;text-transform:none;color:#f0e1bc;line-height:1.2}
.action-actions{display:grid;grid-template-columns:1fr 1fr;gap:7px;margin-top:auto}
.action-actions .cta-consequence{grid-column:1/-1;margin-top:2px}
.action .btn-primary{min-height:38px;font-size:.57rem;padding:8px 12px;font-weight:700;box-shadow:0 2px 12px rgba(214,181,95,.2)}
.action .btn-primary:hover{box-shadow:0 3px 14px rgba(214,181,95,.24)}
.action .btn-secondary{min-height:34px;font-size:.52rem;padding:7px 10px;border-color:rgba(214,181,95,.26);background:transparent;color:#cfbf9e;box-shadow:none;opacity:.9}
.action .btn-secondary:hover{border-color:rgba(214,181,95,.52);background:rgba(214,181,95,.07);box-shadow:none}
.action.secondary .action-actions{grid-template-columns:1fr}
.action.secondary .btn-primary{min-height:34px;font-size:.54rem;padding:7px 10px;opacity:.94}
.action.secondary .btn-secondary{min-height:32px;padding:6px 9px;font-size:.5rem;justify-content:center}
.action.is-locked-card{background:linear-gradient(150deg,rgba(25,20,14,.96),rgba(13,10,8,.99));border-color:rgba(214,181,95,.44)}
.action.is-locked-card .btn-primary{background:linear-gradient(180deg,rgba(214,181,95,.3),rgba(193,154,66,.22));border-color:rgba(214,181,95,.42);color:#f3e7c6;box-shadow:none}
.action.is-locked-card .btn-primary:hover{opacity:.93;box-shadow:none;filter:none}
.action-memory{font-size:.51rem;letter-spacing:.11em;text-transform:uppercase;color:#bdb29a;margin-top:2px}
.action.is-executing{box-shadow:0 0 0 1px rgba(214,181,95,.34) inset,0 0 20px rgba(214,181,95,.16)}
.action.is-resolved{box-shadow:0 0 0 1px rgba(214,181,95,.3) inset,0 0 20px rgba(214,181,95,.12)}
.action.is-applied{box-shadow:0 0 0 1px rgba(106,175,144,.52) inset,0 0 22px rgba(106,175,144,.1),0 0 0 1px rgba(214,181,95,.18);background:rgba(106,175,144,.05);border-left-color:rgba(106,175,144,.5)!important}
.action.is-applied .action-memory{color:rgba(106,175,144,.85);font-weight:500}
.action-applied-badge{display:inline-flex;align-items:center;width:max-content;font-size:.5rem;letter-spacing:.14em;text-transform:uppercase;padding:2px 7px;border-radius:999px;border:1px solid rgba(106,175,144,.42);background:rgba(106,175,144,.12);color:#7abb9e;margin-top:-2px}
.btn.is-applied{background:rgba(80,110,90,.22)!important;border-color:rgba(106,175,144,.32)!important;color:#7abb9e!important;box-shadow:none!important;opacity:.78;cursor:default;pointer-events:none}

.next-move-guide{margin:10px 12px 0;padding:11px;border:1px solid rgba(214,181,95,.28);border-radius:11px;background:rgba(214,181,95,.05)}
.next-move-guide h3{margin:0 0 9px;font-size:.82rem;letter-spacing:.09em;text-transform:uppercase;color:#efdcae}
.next-move-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;align-items:stretch}
.next-move-card{border:1px solid var(--line-soft);border-radius:10px;background:rgba(0,0,0,.16);padding:8px 9px}
.next-move-card strong{display:block;font-size:.5rem;letter-spacing:.16em;text-transform:uppercase;color:#ddc88e;margin-bottom:4px}
.next-move-card p{margin:0;font-size:.68rem;line-height:1.4;color:#eee2c8}

.consult-offer{margin-top:10px;padding:11px;border:1px solid rgba(214,181,95,.3);border-radius:11px;background:linear-gradient(140deg,rgba(214,181,95,.12),rgba(214,181,95,.04));display:none}
.consult-offer[data-show='true']{display:block}
.consult-offer h3{margin:0 0 5px;font-size:.92rem;color:#f0e3bf}
.consult-offer p{margin:0 0 9px;font-size:.72rem;color:#e9dcc1;line-height:1.42}

.btn.is-disabled{opacity:.62;pointer-events:none}
.btn.is-executing{box-shadow:0 0 16px rgba(214,181,95,.26)}
.btn.is-resolved{box-shadow:0 0 16px rgba(214,181,95,.22)}

@keyframes applyGlowPulse{0%{box-shadow:0 0 0 1px rgba(106,175,144,.52) inset,0 0 22px rgba(106,175,144,.1)}45%{box-shadow:0 0 0 2px rgba(106,175,144,.72) inset,0 0 40px rgba(106,175,144,.3),0 -5px 28px rgba(106,175,144,.2)}100%{box-shadow:0 0 0 1px rgba(106,175,144,.52) inset,0 0 22px rgba(106,175,144,.1)}}
.action.is-applying{animation:applyGlowPulse .95s ease-out forwards;transform:translateY(-5px) scale(1.006)!important;transition:transform .34s ease!important}
.action-memory{transition:opacity .38s ease}
.action-memory.is-fading{opacity:0}

.fix-progress-bar-wrap{margin:0 12px 8px;display:none;align-items:center;gap:10px}
.fix-progress-bar{flex:1;height:3px;border-radius:999px;background:rgba(255,255,255,.07);overflow:hidden}
.fix-progress-bar-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,rgba(106,175,144,.72),rgba(80,175,130,.4));width:0%;transition:width .52s ease}
.fix-progress-label{font-size:.46rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(214,181,95,.54);white-space:nowrap}

.fix-ai-nudge{position:fixed;bottom:84px;left:50%;transform:translateX(-50%) translateY(18px);z-index:140;background:linear-gradient(140deg,#1a140c,#120f09);border:1px solid rgba(106,175,144,.38);border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:10px;max-width:420px;width:calc(100% - 32px);box-shadow:0 4px 24px rgba(0,0,0,.42);opacity:0;transition:opacity .3s ease,transform .3s ease;pointer-events:none}
.fix-ai-nudge.is-visible{opacity:1;transform:translateX(-50%) translateY(0);pointer-events:auto}
.fix-ai-nudge-label{flex-shrink:0;font-size:.46rem;letter-spacing:.18em;text-transform:uppercase;padding:2px 6px;border-radius:999px;border:1px solid rgba(106,175,144,.42);color:#7abb9e;background:rgba(106,175,144,.1)}
.fix-ai-nudge p{margin:0;font-size:.68rem;color:#e2d9c1;line-height:1.4;flex:1}
.fix-ai-nudge-cta{flex-shrink:0;font-size:.52rem;letter-spacing:.1em;text-transform:uppercase;padding:5px 10px;border-radius:7px;border:1px solid rgba(106,175,144,.42);color:#7abb9e;background:rgba(106,175,144,.12);cursor:pointer;white-space:nowrap}
.fix-ai-nudge-cta:hover{background:rgba(106,175,144,.22)}
.fix-ai-nudge-dismiss{font-size:.88rem;color:#9a9275;background:none;border:none;cursor:pointer;padding:0 2px;line-height:1;flex-shrink:0}

.modules{padding:12px}
.accordion{display:flex;flex-direction:column;gap:8px}
.module{border:1px solid var(--line-soft);border-radius:12px;background:rgba(214,181,95,.03);overflow:hidden;transition:all .18s ease;border-left:2px solid transparent}
.module:hover{transform:translateY(-2px);box-shadow:0 2px 12px rgba(214,181,95,.08)}
.module.locked{border-left-color:rgba(214,181,95,.4)}
.module summary{list-style:none;cursor:pointer;padding:11px 12px;display:flex;align-items:center;justify-content:space-between;gap:8px}
.module summary::-webkit-details-marker{display:none}
.module-title{font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;color:#e2d4ab}
.module-tag{font-size:.48rem;letter-spacing:.14em;text-transform:uppercase;padding:3px 6px;border-radius:999px;border:1px solid var(--line-soft);color:#cfbf91}
.module-body{padding:0 12px 12px;border-top:1px solid var(--line-soft)}
.module-body p{margin:10px 0 0;font-size:.72rem;color:#ddd1b4;line-height:1.45}
.module-cta{margin-top:10px}
.module.locked{position:relative;border-color:rgba(200,168,75,.2);border-left:2px solid rgba(214,181,95,.4);background:linear-gradient(150deg,rgba(214,181,95,.06),rgba(16,13,9,.97));overflow:hidden;box-shadow:0 0 16px rgba(214,181,95,.08);transition:all .18s ease}
.module.locked:hover{transform:translateY(-2px);box-shadow:0 0 20px rgba(214,181,95,.12)}
.module.locked .module-body{position:relative;background:rgba(0,0,0,.12)}
.module.locked .module-body p{filter:blur(.6px);opacity:.9}
.module.locked .module-cta{filter:drop-shadow(0 0 12px rgba(214,181,95,.2))}
.locked-logic{margin-top:9px;display:grid;gap:7px}
.locked-logic-item{border:1px solid var(--line-soft);border-radius:8px;background:rgba(214,181,95,.06);padding:7px 8px}
.locked-logic-item strong{display:block;font-size:.5rem;letter-spacing:.16em;text-transform:uppercase;color:#ddc88e;margin-bottom:3px}
.locked-logic-item p{margin:0;font-size:.67rem;line-height:1.35;color:#efe2c6}
.locked-logic-item ul{margin:0;padding-left:14px}
.locked-logic-item li{font-size:.64rem;line-height:1.3;color:#efe2c6}

.footer{margin:20px auto 0;padding:12px 20px 6px;max-width:1220px;border-top:1px solid rgba(214,181,95,.14);color:#c9be99;font-size:.64rem;letter-spacing:.09em;line-height:1.6}
.footer-brand{margin-bottom:6px}
.footer-brand strong{display:block;font-size:.70rem;letter-spacing:.12em;text-transform:uppercase;color:#e0d9b8;font-weight:600;margin-bottom:2px}
.footer-tagline{font-size:.54rem;letter-spacing:.08em;text-transform:uppercase;color:#9a9479;display:block}
.footer-meta{font-size:.62rem;letter-spacing:.08em;color:#a89d84}
.footer-copyright{margin:4px 0 2px}
.footer-links{margin:0;letter-spacing:.08em}
.footer a{color:#d4bf89;text-decoration:none;border-bottom:1px solid rgba(214,181,95,.32);padding-bottom:1px;transition:all .16s ease}
.footer a:hover{color:#e8d9a3;border-color:rgba(214,181,95,.62)}

.fix-detail-mask{position:fixed;inset:0;z-index:150;background:linear-gradient(120deg,rgba(6,5,3,.5),rgba(6,5,3,.82));backdrop-filter:blur(4px);display:none}
.fix-detail-mask[data-open='true']{display:block}
body.fix-panel-open .aia-trigger,body.fix-panel-open #aiaPanel,body.fix-panel-open #aiaTeaser,body.fix-panel-open #aiaBackdrop{z-index:80!important}
.fix-detail-panel{position:absolute;top:0;right:0;height:100%;width:min(620px,100%);border-left:1px solid var(--line);background:linear-gradient(160deg,#16120b,#0c0a07 72%);box-shadow:-24px 0 50px rgba(0,0,0,.52);transform:translateX(100%);transition:transform .34s ease}
.fix-detail-mask[data-open='true'] .fix-detail-panel{transform:translateX(0)}
.fix-detail-inner{height:100%;overflow:auto;padding:18px 16px 20px}
.fix-detail-head{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:10px}
.fix-detail-kicker{font-size:.54rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.72)}
.fix-detail-close{display:inline-flex;align-items:center;justify-content:center;min-height:32px;padding:7px 9px;border-radius:8px;border:1px solid var(--line);background:rgba(200,168,75,.09);font-size:.58rem;letter-spacing:.12em;text-transform:uppercase;color:#ddd1b8}
.fix-detail-close:hover{border-color:rgba(200,168,75,.42);background:rgba(200,168,75,.16)}
.fix-detail-module{border:1px solid var(--line);background:rgba(200,168,75,.04);border-radius:10px;padding:10px 11px;margin-bottom:10px}
.fix-detail-title{font-size:.84rem;line-height:1.35;color:var(--text);margin:0}
.fix-detail-meta{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
.fix-detail-pill{display:inline-flex;align-items:center;justify-content:center;min-height:22px;padding:3px 7px;border-radius:999px;font-size:.5rem;letter-spacing:.14em;text-transform:uppercase;border:1px solid var(--line-soft);color:#d9c692}
.fix-detail-points{margin-left:auto;font-size:.56rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(200,168,75,.72)}
.fix-detail-grid{display:grid;grid-template-columns:1fr;gap:8px}
.fix-detail-block{border:1px solid var(--line-soft);border-radius:9px;background:rgba(0,0,0,.2);padding:9px 10px}
.fix-detail-block p:first-child{font-size:.52rem;letter-spacing:.18em;text-transform:uppercase;color:#ddc88e;opacity:.9}
.fix-detail-block p:last-child{margin-top:5px;font-size:.72rem;line-height:1.5;color:#f0e5c8}
.fix-detail-actions{margin-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:8px}
.fix-detail-progression{margin-top:10px;display:grid;grid-template-columns:1fr;gap:7px}
.fix-what-happens{margin:10px 0 8px;padding:10px 11px;border:1px solid rgba(106,175,144,.28);border-radius:9px;background:rgba(106,175,144,.06)}.fix-what-happens-title{font-size:.52rem;letter-spacing:.18em;text-transform:uppercase;color:#6aaf90;margin:0 0 7px}
.fix-what-happens-list{margin:0;padding:0 0 0 1.1em;display:flex;flex-direction:column;gap:4px}
.fix-what-happens-list li{font-size:.68rem;line-height:1.45;color:#d8ccb0}

/* ── Phase 29: upgrade pressure system ── */
.fix-locked-insight{margin:10px 0 8px;padding:10px 11px;border:1px solid rgba(196,120,120,.28);border-radius:9px;background:rgba(196,120,120,.05);display:none}
.fix-locked-insight.is-visible{display:block}
.fix-locked-insight-title{font-size:.52rem;letter-spacing:.18em;text-transform:uppercase;color:#c47878;margin:0 0 6px;display:flex;align-items:center;gap:5px}
.fix-locked-insight-title::before{content:'\1F512';font-size:.7rem;filter:grayscale(.4)}
.fix-locked-insight p{margin:0 0 6px;font-size:.68rem;line-height:1.45;color:#d8ccb0}
.fix-locked-insight ul{margin:0 0 8px;padding-left:1.1em;display:flex;flex-direction:column;gap:3px}
.fix-locked-insight ul li{font-size:.67rem;line-height:1.42;color:#ddd1b4}
.fix-locked-partial{margin:8px 0 0;border:1px solid rgba(196,120,120,.2);border-radius:8px;overflow:hidden}
.fix-locked-partial-head{font-size:.5rem;letter-spacing:.14em;text-transform:uppercase;padding:5px 9px;background:rgba(196,120,120,.1);color:#c47878;opacity:.82}
.fix-locked-partial-row{padding:5px 9px;font-size:.67rem;color:#ccbf9e;border-top:1px solid rgba(255,255,255,.04)}
.fix-locked-partial-row.is-blurred{filter:blur(3px);opacity:.42;user-select:none;pointer-events:none}

.fix-friction-bar{margin:0 0 8px;padding:9px 11px;border:1px solid rgba(196,120,120,.22);border-radius:9px;background:rgba(196,120,120,.05);display:none}
.fix-friction-bar.is-visible{display:block}
.fix-friction-bar-title{font-size:.5rem;letter-spacing:.18em;text-transform:uppercase;color:#c47878;margin:0 0 5px}
.fix-friction-bar ul{margin:0;padding-left:1.2em;display:flex;flex-direction:column;gap:3px}
.fix-friction-bar li{font-size:.66rem;color:#d8ccb0;line-height:1.36}

.upgrade-pressure-bar{margin:8px 12px 0;padding:11px 13px;border:1px solid rgba(214,181,95,.32);border-radius:11px;background:linear-gradient(140deg,rgba(36,26,14,.94),rgba(16,12,8,.97));display:none;align-items:center;gap:12px}
.upgrade-pressure-bar.is-visible{display:flex}
.upgrade-pressure-bar p{margin:0;font-size:.7rem;line-height:1.42;color:#e9ddc2;flex:1}
.upgrade-pressure-bar p strong{color:#f0e3bf;display:block;font-size:.76rem;margin-bottom:2px}
.upgrade-pressure-bar-cta{flex-shrink:0;padding:8px 12px;border-radius:9px;border:1px solid rgba(214,181,95,.5);background:rgba(214,181,95,.14);font-size:.56rem;letter-spacing:.1em;text-transform:uppercase;color:#efdcae;font-weight:700;cursor:pointer;white-space:nowrap;text-decoration:none}
.upgrade-pressure-bar-cta:hover{background:rgba(214,181,95,.24)}
@keyframes nextFocus{0%,100%{box-shadow:none}40%{box-shadow:0 0 0 2px rgba(200,168,75,.42)}}
.action.next-focus{animation:nextFocus 1s ease forwards}


@media (max-width:1080px){
  .layout{grid-template-columns:1fr}
  .sticky{position:static}
}
@media (max-width:900px){
  /* Match mobile nav behavior used across other public pages */
  #nav{padding:14px 16px}
  #nav.stuck{padding:10px 16px}
  .nav-right{display:none}
  .nav-hamburger{display:flex}

  .grid,.layer-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
  .layer::after{display:none}
  .action-stack{grid-template-columns:1fr}
  .action-stack::before{display:none}
  .shell{padding:64px 14px 38px}

  /* Keep System Analysis visible but secondary and compact */
  .mode-bar{
    grid-template-columns:1fr auto;
    gap:8px;
    padding:8px 10px;
    border-radius:10px;
    margin-bottom:8px;
  }
  .mode-kicker{display:none}
  .mode-meta{gap:6px}
  .mode-chip{min-height:22px;padding:3px 8px;font-size:.5rem;letter-spacing:.07em}
  .mode-return{min-height:30px;padding:6px 9px;font-size:.48rem;letter-spacing:.09em;border-radius:8px}

  /* Reduce mobile above-the-fold noise so score -> meaning -> action is clear */
  .live-feedback-strip,
  .global-state-strip,
  .progression-strip{display:none}

  .hero{
    display:flex;
    flex-direction:column;
    padding:14px 12px 12px;
    border-radius:12px;
    gap:8px;
  }
  .hero-title{font-size:1.52rem;line-height:1.05}
  .score-meaning{font-size:.56rem;letter-spacing:.11em}
  .aha-line{font-size:.78rem;line-height:1.42;max-width:100%}

  /* Mobile hierarchy inside hero */
  .hero > .hero-top{order:1}
  .hero > .hero-actions{order:2}
  .hero > .cta-time-value{order:3}
  .hero > .hero-bottleneck{order:4}
  .hero > .hero-copy{order:5}
  .hero > .cta-consequence{order:6}
  .hero > .hero-translation{order:7}
  .hero > .hero-trust-note{order:8}
  .hero > .hero-proof-note{order:9}
  .hero > .hero-momentum{order:10}

  .hero-bottleneck{padding:9px 10px;margin:2px 0 0}
  .hero-bottleneck strong{font-size:.5rem}
  .hero-bottleneck p{font-size:.74rem;line-height:1.35}
  .hero-copy,.hero-translation{font-size:.72rem;line-height:1.42}
  .hero-trust-note,.hero-proof-note{font-size:.62rem;line-height:1.35}

  /* One dominant action near top */
  .hero-actions{display:block;margin-top:2px}
  .hero-actions .btn-primary{display:flex;width:100%;min-height:42px}
  .hero-actions .btn-secondary{
    margin-top:7px;
    min-height:auto;
    padding:0;
    border:none;
    background:transparent;
    color:#d9c79e;
    letter-spacing:.08em;
    font-size:.56rem;
    text-transform:uppercase;
    justify-content:flex-start;
  }
  .hero-actions .btn-secondary:hover{background:transparent}
  .cta-time-value{font-size:.63rem;line-height:1.35;margin-top:3px}

  /* Mobile section flow: next move before progression */
  .main > #priority-actions{order:2}
  .main > #layers{order:3}
  .main > #findings{order:4}
  .main > #deeper-layers{order:5}

  .section-head{padding:9px 10px}
  .section-head h2{font-size:1.08rem;line-height:1.2}
  .state-rail{display:none}
  .actions{padding:10px}

  .next-move-guide{margin:8px 10px 0;padding:10px}
  .next-move-guide h3{font-size:.72rem;letter-spacing:.08em;margin-bottom:7px}
  .next-move-card{padding:8px}
  .next-move-card p{font-size:.72rem;line-height:1.42}

  /* Reduce overload in fix stack on small phones */
  .action{padding:11px 10px;min-height:auto}
  .action h3{font-size:.76rem}
  .action p{font-size:.68rem}
  .action-outcome li{font-size:.66rem;line-height:1.33}
  .action-actions .btn-primary{min-height:36px;font-size:.55rem}
  .action-actions .btn-secondary{min-height:32px;font-size:.5rem}
  .action.secondary{display:none}

  /* Progression readability */
  .layer{padding:10px}
  .layer-name{font-size:.56rem;letter-spacing:.12em}
  .layer-main{font-size:.72rem;line-height:1.4}
  .layer-main strong{font-size:.48rem;letter-spacing:.13em}
  .layer-micro span{min-width:unset;font-size:.48rem}
  .layer .btn{min-height:32px;font-size:.52rem;padding:7px 9px}
  .layer-cta-note{font-size:.53rem;line-height:1.25}

  /* Sticky panel should not compete with hero CTA on mobile */
  .sticky{margin-top:16px;padding:12px;border-radius:12px}
  .sticky .js-track-sticky-cta{display:none}
  .sticky-copy{font-size:.72rem;line-height:1.4}
  .sticky-unlock{font-size:.66rem;line-height:1.35}
  .consult-offer{margin-top:12px;padding:10px}
  .consult-offer h3{font-size:.84rem;line-height:1.25}
  .consult-offer p{font-size:.69rem;line-height:1.4}

  .action-actions,.fix-detail-actions{grid-template-columns:1fr}
  .progression-strip{grid-template-columns:1fr}
  .next-move-grid{grid-template-columns:1fr}
}

/* ── Ask-Scan module (inline AI entry point) ─────────── */
.ask-scan-module{border-color:rgba(200,168,75,.28);background:linear-gradient(155deg,rgba(23,19,13,.97),rgba(11,9,7,.99))}
.ask-scan-inner{padding:16px 18px}
.ask-scan-kicker{font-size:.52rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.72);margin:0 0 4px}
.ask-scan-title{font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:400;margin:0 0 6px;color:#ede8de}
.ask-scan-desc{margin:0 0 12px;font-size:.75rem;line-height:1.55;color:#c8beaa}
.ask-scan-chips{display:flex;gap:7px;flex-wrap:wrap;margin-bottom:12px}
.ask-scan-chip{background:rgba(200,168,75,.07);border:1px solid rgba(200,168,75,.2);border-radius:18px;padding:6px 13px;font-family:'DM Sans',sans-serif;font-size:.71rem;letter-spacing:.03em;color:rgba(200,168,75,.88);cursor:pointer;white-space:nowrap;transition:background .2s,border-color .2s,color .2s;-webkit-tap-highlight-color:transparent}
.ask-scan-chip:hover{background:rgba(200,168,75,.14);border-color:rgba(200,168,75,.4);color:#d8be72}
@media(max-width:640px){.ask-scan-chips{flex-direction:column}.ask-scan-chip{white-space:normal;text-align:left}}

/* ── Hero tier callout ────────────────────────────────── */
.hero-tier-callout{margin:10px 0 0;padding:10px 13px;border:1px solid rgba(200,168,75,.18);border-radius:10px;background:rgba(200,168,75,.05)}
.hero-tier-callout strong{display:block;font-size:.52rem;letter-spacing:.18em;text-transform:uppercase;color:#ddc98e;margin-bottom:6px}
.hero-tier-list{margin:0;padding-left:16px}
.hero-tier-list li{font-size:.71rem;color:#ece2c7;line-height:1.4;margin:2px 0}
.hero-tier-upgrade{margin:8px 0 0;font-size:.68rem;line-height:1.45;color:#c8b98a}
.hero-tier-upgrade em{font-style:italic;color:#e0d0a6}

/* ── Module tier price label ─────────────────────────── */
.module-tier-price{font-size:.54rem;letter-spacing:.16em;text-transform:uppercase;color:rgba(200,168,75,.72);margin:0 0 7px;padding:4px 8px;border:1px solid rgba(200,168,75,.2);border-radius:6px;display:inline-block}

@media (max-width:640px){
  #nav{padding:12px 14px}
  #nav.stuck{padding:9px 14px}
  .logo-seo{font-size:1.08rem}
  .logo-ai{font-size:1.28rem}
  .logo-co{font-size:.96rem}

  .mode-bar{grid-template-columns:1fr}
  .mode-return{width:100%;justify-content:center;min-height:28px}
  .mode-meta{display:grid;grid-template-columns:1fr 1fr;gap:5px}
  .mode-chip{justify-content:center;min-width:0}

  .grid,.layer-grid{grid-template-columns:1fr}
  .action-actions,.fix-detail-actions{grid-template-columns:1fr}
}

@keyframes feedbackPulse {
  0%,100%{box-shadow:0 0 0 0 rgba(214,181,95,.5)}
  50%{box-shadow:0 0 0 8px rgba(214,181,95,0)}
}
@keyframes meterGrow {
  0%{transform:scaleX(0)}
  100%{transform:scaleX(1)}
}
@keyframes layerUnlockPulse {
  0%{box-shadow:0 0 0 1px rgba(200,168,75,.2) inset,0 0 0 rgba(200,168,75,0)}
  45%{box-shadow:0 0 0 1px rgba(200,168,75,.36) inset,0 0 22px rgba(200,168,75,.24)}
  100%{box-shadow:0 0 0 1px rgba(200,168,75,.32) inset,0 0 18px rgba(200,168,75,.14)}
}

/* ── Next Best Move upgrade panel ── */
.nbm-panel{padding:0;overflow:hidden;border-color:rgba(214,181,95,.4);box-shadow:0 0 32px rgba(214,181,95,.1),0 12px 28px rgba(0,0,0,.32),0 0 0 1px rgba(214,181,95,.06) inset;animation:nbmReveal .55s ease-out both}
@keyframes nbmReveal{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
/* Score-band border accent */
.nbm-panel--low .nbm-bar{background:linear-gradient(90deg,rgba(196,120,120,.9) 0%,rgba(196,120,120,.28) 55%,transparent 100%)}
.nbm-panel--low{border-color:rgba(196,120,120,.36);box-shadow:0 0 28px rgba(196,120,120,.09),0 12px 28px rgba(0,0,0,.32)}
.nbm-panel--mid .nbm-bar{background:linear-gradient(90deg,rgba(214,181,95,.88) 0%,rgba(214,181,95,.28) 55%,transparent 100%)}
.nbm-panel--mid{border-color:rgba(214,181,95,.4)}
.nbm-panel--high .nbm-bar{background:linear-gradient(90deg,rgba(106,175,144,.9) 0%,rgba(106,175,144,.28) 55%,transparent 100%)}
.nbm-panel--high{border-color:rgba(106,175,144,.36);box-shadow:0 0 28px rgba(106,175,144,.09),0 12px 28px rgba(0,0,0,.32)}
/* Layout */
.nbm-inner{display:grid;grid-template-columns:1fr 1fr;gap:0}
.nbm-left{padding:22px 18px 22px 22px;border-right:1px solid rgba(214,181,95,.11)}
.nbm-right{padding:22px 22px 22px 20px;display:flex;flex-direction:column;gap:9px}
/* Primary recommendation typography */
.nbm-kicker{margin:0 0 8px;font-size:.5rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(214,181,95,.62)}
.nbm-title{margin:0 0 3px;font-family:'Cormorant Garamond',serif;font-size:1.62rem;line-height:1.1;color:#f2e8cd}
.nbm-price{margin:0 0 12px;font-family:'Cormorant Garamond',serif;font-size:1rem;color:rgba(214,181,95,.7)}
.nbm-why{margin:0 0 10px;font-size:.73rem;line-height:1.55;color:#d0c7a4}
.nbm-panel--low .nbm-why{color:#deccbf}
.nbm-panel--high .nbm-why{color:#c5d9cf}
.nbm-bullets{margin:0 0 14px;padding-left:16px;display:flex;flex-direction:column;gap:5px}
.nbm-bullets li{font-size:.71rem;color:#e0d6be;line-height:1.38}
/* Right column */
.nbm-signal{margin:0 0 6px;font-size:.5rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(214,181,95,.52)}
.nbm-improves{margin:0 0 12px;padding-left:0;list-style:none;display:flex;flex-direction:column;gap:6px}
.nbm-improves li{font-size:.68rem;color:#ddd4b5;padding-left:14px;position:relative;line-height:1.35}
.nbm-improves li::before{content:'→';position:absolute;left:0;color:rgba(214,181,95,.5);font-size:.62rem}
/* CTA */
.nbm-cta{width:100%;justify-content:center}
.nbm-cta-pulse{animation:nbmCtaPulse 4s ease-in-out infinite;animation-delay:1.2s}
@keyframes nbmCtaPulse{
  0%,72%,100%{box-shadow:0 2px 8px rgba(214,181,95,.14)}
  80%{box-shadow:0 0 0 5px rgba(214,181,95,.16),0 2px 12px rgba(214,181,95,.28)}
  88%{box-shadow:0 0 0 2px rgba(214,181,95,.08),0 2px 8px rgba(214,181,95,.14)}
}
.nbm-why-now{margin:3px 0 3px;font-size:.6rem;letter-spacing:.05em;color:rgba(214,181,95,.44);font-style:italic;line-height:1.4}
.nbm-urgency{margin:0 0 4px;font-size:.57rem;letter-spacing:.06em;color:rgba(200,168,75,.38);line-height:1.3}
/* Secondary CTA (next stage) */
.nbm-secondary{font-size:.54rem;min-height:32px;padding:6px 10px;opacity:.82}
.nbm-secondary:hover{opacity:1}
/* ── System position bar ── */
.sys-bar{padding:14px 22px 18px;border-bottom:1px solid rgba(214,181,95,.09)}
.sys-bar-kicker{margin:0 0 10px;font-size:.48rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(214,181,95,.4)}
.sys-bar-track{display:flex;align-items:center;gap:0;overflow-x:auto;scrollbar-width:none;-ms-overflow-style:none;padding-top:20px}
.sys-bar-track::-webkit-scrollbar{display:none}
.sys-bar-node{display:flex;flex-direction:column;align-items:center;gap:0;flex-shrink:0;min-width:54px;cursor:default;background:transparent;border:none;padding:0;font-family:'DM Sans',sans-serif}
button.sys-bar-node{cursor:pointer;-webkit-tap-highlight-color:transparent}
button.sys-bar-node:hover .sys-bar-dot{border-color:rgba(214,181,95,.54);background:rgba(214,181,95,.13)}
.sys-bar-node-head{min-height:17px;display:flex;align-items:flex-end;justify-content:center}
.sys-bar-dot{width:28px;height:28px;border-radius:50%;border:1px solid rgba(214,181,95,.19);background:rgba(214,181,95,.04);transition:all .18s ease;position:relative;flex-shrink:0;pointer-events:none}
.sys-bar-node--done .sys-bar-dot{border-color:rgba(106,175,144,.48);background:rgba(106,175,144,.1)}
.sys-bar-node--done .sys-bar-dot::after{content:'✓';position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);font-size:.52rem;color:#7abb9e;line-height:1}
.sys-bar-node--active .sys-bar-dot{border-color:rgba(214,181,95,.82);background:rgba(214,181,95,.15);box-shadow:0 0 0 5px rgba(214,181,95,.09),0 0 18px rgba(214,181,95,.22);animation:sysNodePulse 3.8s ease-in-out infinite}
@keyframes sysNodePulse{
  0%,70%,100%{box-shadow:0 0 0 5px rgba(214,181,95,.09),0 0 14px rgba(214,181,95,.18)}
  78%{box-shadow:0 0 0 10px rgba(214,181,95,.12),0 0 26px rgba(214,181,95,.3)}
  86%{box-shadow:0 0 0 3px rgba(214,181,95,.05),0 0 10px rgba(214,181,95,.12)}
}
.sys-bar-node-foot{display:flex;flex-direction:column;align-items:center;gap:3px;margin-top:6px}
.sys-bar-label{font-size:.5rem;letter-spacing:.13em;text-transform:uppercase;color:rgba(214,181,95,.32);text-align:center;white-space:nowrap;line-height:1}
.sys-bar-node--done .sys-bar-label{color:rgba(106,175,144,.52)}
.sys-bar-node--active .sys-bar-label{color:#d8bc62;font-weight:600}
.sys-bar-price{font-size:.46rem;color:rgba(214,181,95,.27);text-align:center;white-space:nowrap;line-height:1}
.sys-bar-node--active .sys-bar-price{color:rgba(214,181,95,.6)}
.sys-bar-you-here{font-size:.44rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(214,181,95,.74);white-space:nowrap;display:none;line-height:1}
.sys-bar-node--active .sys-bar-you-here{display:block}
.sys-bar-next-move{font-size:.44rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(214,181,95,.56);white-space:nowrap;display:none;line-height:1}
.sys-bar-node--active .sys-bar-next-move{display:block}
.sys-bar-line{width:30px;height:1px;background:rgba(214,181,95,.12);flex-shrink:0;margin-bottom:15px;align-self:center}
.sys-bar-line--done{background:linear-gradient(90deg,rgba(106,175,144,.34),rgba(214,181,95,.14))}
@media(prefers-reduced-motion:reduce){.sys-bar-node--active .sys-bar-dot{animation:none}}
@media(max-width:480px){
  .sys-bar{padding:8px 10px 12px}
  .sys-bar-node{min-width:38px}
  .sys-bar-dot{width:20px;height:20px}
  .sys-bar-node-head{min-height:14px}
  .sys-bar-line{width:18px}
}
@media(max-width:768px){
  .nbm-inner{grid-template-columns:1fr}
  .nbm-left{border-right:none;border-bottom:1px solid rgba(214,181,95,.09);padding:18px 18px 14px}
  .nbm-right{padding:14px 18px 18px}
  .sys-bar{padding:10px 14px 14px}
  .sys-bar-track{padding-top:14px}
  .sys-bar-node{min-width:44px}
  .sys-bar-dot{width:22px;height:22px}
}

/* Final pre-live readability refinements */
.mode-kicker,
.mode-chip,
.saved-report-note,
.live-feedback-kicker,
.live-feedback-text,
.progression-cell p,
.progression-list li,
.progression-sub,
.progression-mini li,
.progression-model li,
.hero-domain,
.score-meaning,
.hero-copy,
.hero-translation,
.hero-trust-note,
.hero-momentum,
.hero-proof-note,
.sys-bar-label,
.sys-bar-price,
.save-report-text,
.save-report-google,
.save-report-login{
  font-size:max(.8rem, 12px);
  line-height:1.55;
}
</style>
@include('partials.clarity')
</head>
<body>
@include('partials.public-nav', ['showHamburger' => true])

@guest
<div class="save-report-bar" id="saveReportBar">
  <div class="save-report-inner">
    <span class="save-report-text"><strong>This report isn't saved yet.</strong> Sign in to keep it on your account.</span>
    <div class="save-report-actions">
      <a href="{{ route('auth.google.redirect', ['scan_id' => $scan->id, 'redirect' => route('dashboard.scans.show', ['scan' => $scan->publicScanId()], false)]) }}" class="save-report-google">
        <svg width="16" height="16" viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844a4.14 4.14 0 01-1.796 2.716v2.259h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/><path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z" fill="#34A853"/><path d="M3.964 10.71A5.41 5.41 0 013.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/><path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 00.957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/></svg>
        Continue with Google
      </a>
      <a href="{{ route('login', ['redirect' => route('dashboard.scans.show', ['scan' => $scan->publicScanId()], false)]) }}" class="save-report-login">Sign in</a>
    </div>
  </div>
</div>
<style>
.save-report-bar{position:sticky;top:64px;z-index:80;background:linear-gradient(90deg,rgba(20,16,10,.97),rgba(14,11,8,.98));border-bottom:1px solid rgba(214,181,95,.28);padding:9px 20px;box-shadow:0 2px 12px rgba(0,0,0,.3)}
.save-report-inner{max-width:1220px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.save-report-text{font-size:.8rem;color:#e0d5b8;line-height:1.45}
.save-report-text strong{color:#f0e2be}
.save-report-actions{display:flex;align-items:center;gap:8px;flex-shrink:0}
.save-report-google{display:inline-flex;align-items:center;gap:7px;min-height:34px;padding:6px 12px;border-radius:8px;border:1px solid rgba(214,181,95,.4);background:rgba(214,181,95,.1);font-size:.74rem;letter-spacing:.09em;text-transform:uppercase;color:#f0e5c6;text-decoration:none;transition:all .16s ease}
.save-report-google:hover{border-color:rgba(214,181,95,.6);background:rgba(214,181,95,.18)}
.save-report-login{display:inline-flex;align-items:center;min-height:34px;padding:6px 10px;font-size:.74rem;letter-spacing:.08em;text-transform:uppercase;color:#c8b98a;text-decoration:none;border-bottom:1px solid rgba(214,181,95,.3);transition:color .16s}
.save-report-login:hover{color:#e8d49a;border-color:rgba(214,181,95,.56)}
@media(max-width:540px){.save-report-bar{top:56px}.save-report-inner{flex-direction:column;align-items:flex-start;gap:8px}}
</style>
@endguest

<div class="shell">
  <div class="mode-bar">
    <div>
      <p class="mode-kicker">System Analysis Mode</p>
      <div class="mode-meta">
        <span class="mode-chip"><strong>Domain</strong> {{ $scan->domain() }}</span>
        <span class="mode-chip"><strong>Score</strong> {{ $score }}</span>
        <span class="mode-chip state-{{ $readoutStateKey }}"><strong>State</strong> {{ $readoutState }}</span>
        <span class="mode-chip"><strong>Last Eval</strong> {{ $lastEvaluatedLabel }}</span>
      </div>
    </div>
    <a href="{{ $returnHref }}" class="mode-return">&larr; {{ $returnLabel }}</a>
  </div>

  @if(session('status'))
  <p class="state-notice">{{ session('status') }}</p>
  @endif

  @if(session('system_entry'))
    @php
      $entryLabel = ucwords(str_replace('-', ' ', (string) session('system_entry')));
    @endphp
  <p class="state-notice is-success">Unlock confirmed: {{ $entryLabel }} is now active for this report.</p>
  @endif

  @auth
  <p class="saved-report-note">Saved to your account dashboard</p>
  @endauth

  <div class="live-feedback-strip" id="liveFeedbackStrip" data-feedback-messages='@json($liveFeedbackMessages)'>
    <span class="live-feedback-dot" aria-hidden="true"></span>
    <p class="live-feedback-kicker">Live System Feedback</p>
    <p class="live-feedback-text" id="liveFeedbackText">{{ $liveFeedbackMessages[0] ?? 'Interpreting current system state.' }}</p>
  </div>

  <div class="global-state-strip">
    <div class="global-state-chip"><strong>Selection Pressure</strong><span>{{ $selectionPressureLabel }}</span></div>
    <div class="global-state-chip"><strong>Extraction Completeness</strong><span>{{ $extractionCompletenessLabel }}</span></div>
    <div class="global-state-chip"><strong>Authority Confidence</strong><span>{{ $authorityConfidenceLabel }}</span></div>
  </div>

  <div class="progression-strip">
    <div class="progression-cell is-current-stage">
      <strong>Current State</strong>
      <p>You are here: {{ $currentStateSummary }}</p>
      <p class="progression-sub">{{ $scoreSelectionInterpretation }}. Next: {{ $nextUnlockName }}.</p>
    </div>
    <div class="progression-cell is-next-unlock">
      <strong>Next Unlock</strong>
      <p class="next-unlock-name">{{ $nextUnlockName }}</p>
      <p class="progression-sub">Why it matters: {{ $nextUnlockWhyShort }}</p>
      <ul class="progression-mini">
        @foreach($nextUnlockImproves as $improvement)
        <li>{{ $improvement }}</li>
        @endforeach
      </ul>
    </div>
    <div class="progression-cell">
      <strong>Layer Progression</strong>
      <ul class="progression-model">
        @foreach($layerProgressModel as $stage)
        <li class="{{ $stage['rank'] === $unlockLevel ? 'is-current' : '' }}">Layer {{ $stage['rank'] }}: {{ $stage['label'] }}</li>
        @endforeach
      </ul>
    </div>
  </div>

  <div class="layout">
    <main class="main">
      <section class="card hero" id="sys-overview">
        <div class="hero-top">
          <div>
            <p class="hero-domain">{{ $scan->domain() }}</p>
            <h1 class="hero-title">Your Scan Results</h1>
            <div class="hero-state">
              <span class="pill pill-score">Score {{ $score }}</span>
              <span class="pill pill-state-{{ $readoutStateKey }}">{{ $readoutState }}</span>
              <span class="pill pill-layer">Layer {{ $unlockLevel }}</span>
            </div>
            <p class="score-meaning">{{ $scoreSelectionInterpretation }}</p>
            <p class="aha-line">{{ $ahaLine }}</p>
          </div>
          <div class="hero-score-visual">
            <div class="hero-orbit-container">
              @include('components.ai-score-orbit', ['score' => $score, 'label' => 'AI Visibility Score', 'size' => 'md'])
            </div>
          </div>
        </div>
        <div class="hero-bottleneck">
          <strong>Here&rsquo;s what&rsquo;s holding you back</strong>
          <p>{{ $topBottleneck }}</p>
        </div>
        <p class="hero-copy">{{ $humanTranslation }}</p>
        <div class="hero-actions">
          @if($singleNextStep)
          <a href="{{ $singleNextStep['href'] }}" class="btn btn-primary js-track-primary-cta">{{ $momentumCta }}</a>
          @else
          <a href="#priority-actions" class="btn btn-primary js-track-primary-cta">{{ $momentumCta }}</a>
          @endif
          <a href="#priority-actions" class="btn btn-secondary">See My Next Move</a>
        </div>
        <p class="cta-time-value">Takes 2-5 minutes to apply. Start seeing improvements today.</p>
        <div class="hero-tier-callout">
          <strong>{{ $currentTierName }} ({{ $currentTierPrice }}) includes</strong>
          <ul class="hero-tier-list">
            <li>AI Visibility Score — {{ $score }}/100</li>
            <li>6-signal breakdown (schema, coverage, entity, links, crawlability, extractability)</li>
            <li>Top issues identified + fastest fix prioritised</li>
            @if($unlockLevel >= 2)<li>Full signal architecture map + extraction failure tree</li>@endif
            @if($unlockLevel >= 3)<li>Impact-ranked fix sequence — highest-leverage first</li>@endif
            @if($unlockLevel >= 4)<li>Competitive intelligence layer + expansion map</li>@endif
          </ul>
          @if($nextTier)
          <p class="hero-tier-upgrade"><span class="lock-glyph" aria-hidden="true"></span> {{ $nextTier['name'] }} ({{ $nextTier['price'] }}) unlocks the full diagnostic: <em>exactly where your signals fail and why.</em></p>
          @endif
        </div>
        <p class="cta-consequence">{{ $singleNextStep ? 'Take this step to unlock the next level.' : 'Fix this blocker to improve your chance of being selected.' }}</p>
        <p class="hero-translation">What this means: {{ $interpretation }}</p>
        <p class="hero-trust-note">This score is based on real AI answer system patterns, not generic SEO guesses.</p>
        <p class="hero-proof-note">Used to generate AI answers across Google, ChatGPT, and other systems.</p>
        <p class="hero-momentum">{{ $momentumLine }}</p>
      </section>

      @if(!$isUpgraded && $nextTier)
      <section class="card nbm-panel nbm-panel--{{ $nbmScoreBand }}" id="next-best-move"
               aria-label="Recommended next layer: {{ $nbmTierDef['name'] }}">
        <div class="nbm-bar" aria-hidden="true"></div>
        {{-- System position bar: Scan → Signal → Fix → Build → Expand → Managed --}}
        <div class="sys-bar" role="navigation" aria-label="System progression map">
          <p class="sys-bar-kicker">System Position</p>
          <div class="sys-bar-track">
            @foreach($sysBarNodes as $node)
            @php $nodeState = $node['key'] === 'scan' ? 'done' : ($node['key'] === $sysBarCurrentKey ? 'active' : 'future'); @endphp
            @if(!$loop->first)
            <div class="sys-bar-line{{ $loop->iteration === 2 ? ' sys-bar-line--done' : '' }}" aria-hidden="true"></div>
            @endif
            @if($node['modal'])
            <button type="button" class="sys-bar-node sys-bar-node--{{ $nodeState }}"
                    data-layer="{{ $node['modal'] }}" aria-haspopup="dialog"
                    aria-label="{{ $node['label'] }}{{ $node['price'] ? ' — ' . $node['price'] : '' }}">
            @else
            <div class="sys-bar-node sys-bar-node--{{ $nodeState }}" aria-label="{{ $node['label'] }} — complete">
            @endif
              <div class="sys-bar-node-head"><span class="sys-bar-you-here">You are here</span></div>
              <div class="sys-bar-dot"></div>
              <div class="sys-bar-node-foot">
                <span class="sys-bar-label">{{ $node['label'] }}</span>
                @if($node['price'])<span class="sys-bar-price">{{ $node['price'] }}</span>@endif
                <span class="sys-bar-next-move">Next best move</span>
              </div>
            @if($node['modal'])
            </button>
            @else
            </div>
            @endif
            @endforeach
          </div>
        </div>
        {{-- Primary recommendation: 2-col left/right --}}
        <div class="nbm-inner">
          <div class="nbm-left">
            <p class="nbm-kicker">Recommended Next Layer &mdash; Score {{ $score }}/100</p>
            <h2 class="nbm-title">{{ $nbmTierDef['name'] }}</h2>
            <p class="nbm-price">{{ $nbmTierDef['price'] }}</p>
            <p class="nbm-why">{{ $nbmScoreCopy }}</p>
            <ul class="nbm-bullets">
              @foreach($nbmBullets as $bullet)
              <li>{{ $bullet }}</li>
              @endforeach
            </ul>
          </div>
          <div class="nbm-right">
            <p class="nbm-signal">What this unlocks for your {{ $score }}/100 score</p>
            <ul class="nbm-improves">
              @foreach($nextUnlockImproves as $item)
              <li>{{ $item }}</li>
              @endforeach
            </ul>
            {{-- Primary CTA: score-driven tier --}}
            <a href="{{ $nbmHref }}" class="btn btn-primary nbm-cta nbm-cta-pulse">{{ $nbmCtaLabel }}</a>
            <p class="nbm-why-now">{{ $nbmWhyNow }}</p>
            <p class="nbm-urgency">Most users at your score move to this layer next.</p>
            {{-- Secondary CTA: next stage after recommended, opens layer modal --}}
            @if($nbmSecondaryStep)
            <button type="button"
                    class="btn btn-secondary nbm-secondary"
                    data-layer="{{ $nbmSecondaryStep['modal'] }}"
                    aria-haspopup="dialog">View {{ $nbmSecondaryStep['label'] }} &rarr;</button>
            @elseif($showConsultationOffer)
            <a href="{{ $consultationHref }}" class="btn btn-secondary nbm-secondary">Book a Consultation</a>
            @endif
          </div>
        </div>
      </section>
      @endif

      <section class="card ask-scan-module" id="ask-scan">
        <div class="ask-scan-inner">
          <p class="ask-scan-kicker">AI Scan Advisor</p>
          <h2 class="ask-scan-title">Ask about your scan</h2>
          <p class="ask-scan-desc">I have your results. Ask me what your {{ $score }}/100 score means for your business, which issue to prioritise, what your tier includes, or what the next level would reveal.</p>
          <div class="ask-scan-chips">
            <button type="button" class="ask-scan-chip js-ask-scan-chip" data-prompt="My score is {{ $score }}/100. What does that mean for my AI search visibility and what should I do first?">Why is my score {{ $score }}?</button>
            <button type="button" class="ask-scan-chip js-ask-scan-chip" data-prompt="What is the single most important thing I should fix first to improve my AI search visibility?">What should I fix first?</button>
            <button type="button" class="ask-scan-chip js-ask-scan-chip" data-prompt="What does my {{ $currentTierName }} plan include? What am I seeing vs what is locked?">What does my plan include?</button>
            <button type="button" class="ask-scan-chip js-ask-scan-chip" data-prompt="What would upgrading to the next tier reveal that I currently cannot see in my scan?">What would upgrading reveal?</button>
          </div>
          <button type="button" class="btn btn-secondary js-open-ai-panel">Open AI Advisor</button>
        </div>
      </section>

      <section class="card" id="findings">
        <div class="section-head">
          <h2>What We Found</h2>
          <p style="margin:0;font-size:.55rem;letter-spacing:.12em;text-transform:uppercase;color:#c0b38c">State per signal domain</p>
        </div>
        <div class="grid" style="padding:12px">
          @foreach($findings as $finding)
          @php
            $findingClass = str_contains($finding['state'], 'Lifted') || str_contains($finding['state'], 'Active') || str_contains($finding['state'], 'Rising') || str_contains($finding['state'], 'Reliable') ? 'is-stable' : 'is-constraint';
          @endphp
          <article class="finding {{ $findingClass }}">
            <div class="finding-top">
              <h3>{{ $finding['title'] }}</h3>
              <span class="state">{{ $finding['state'] }}</span>
            </div>
            <p>{{ $finding['copy'] }}</p>
            <div class="meter"><span style="width:{{ $finding['pct'] }}%"></span></div>
          </article>
          @endforeach
        </div>
      </section>

      <section class="card" id="layers">
        <div class="section-head">
          <h2>Your Plan</h2>
          <p style="margin:0;font-size:.55rem;letter-spacing:.12em;text-transform:uppercase;color:#c0b38c">What&rsquo;s included vs what&rsquo;s available</p>
        </div>
        <div class="layer-grid" style="padding:12px">
          @foreach($progressionLevels as $level)
          @php
            $isRecommendedNext = $recommendedProgressionRank !== null && $level['rank'] === $recommendedProgressionRank;
            $momentumCopy = match($level['rank']) {
              1 => 'You\'ve started — now build signal clarity',
              2 => 'Next: turn insight into prioritized action',
              3 => 'Next: deploy structure across your market',
              default => 'Next: scale and dominate your territory',
            };
          @endphp
          <article class="layer {{ $level['locked'] ? 'is-locked-card' : 'is-included' }} {{ $isRecommendedNext ? 'is-next-step' : '' }}">
            @if($isRecommendedNext)
            <span class="layer-next-step-badge">RECOMMENDED NEXT STEP</span>
            @endif
            <div class="layer-head">
              <span class="layer-name">{{ $level['name'] }}</span>
              <span class="layer-status">
              @if($level['locked'])
                <span class="lock-glyph" aria-hidden="true"></span>
                Locked &mdash; {{ $tierDefs[$level['rank']]['price'] ?? '' }}
              @elseif($level['rank'] === $unlockLevel)
                &#10003; Your Tier
              @else
                &#10003; Included
              @endif
            </span>
            </div>
            <div class="layer-strip">
              <div class="layer-sig">Coverage<div class="meter"><span style="width:{{ $coveragePct }}%"></span></div></div>
              <div class="layer-sig">Authority<div class="meter"><span style="width:{{ $authorityPct }}%"></span></div></div>
              <div class="layer-sig">Structure<div class="meter"><span style="width:{{ $structurePct }}%"></span></div></div>
            </div>
            <div class="layer-main"><strong style="display:block;font-size:.5rem;letter-spacing:.15em;text-transform:uppercase;color:#ddc88e;margin-bottom:4px">What It Does</strong>{{ $level['unlocks'] }}</div>
            <div class="layer-micro">
              <span>How It Feels <em>{{ $level['improves'] }}</em></span>
            </div>
            @if($level['locked'] && $singleNextStep && $isRecommendedNext)
            <a href="{{ $singleNextStep['href'] }}" class="btn btn-secondary layer-cta-unlock">Unlock Next Layer &rarr;</a>
            <button type="button" class="layer-ai-link js-ask-scan-chip"
              data-prompt="Based on my current scan, should I move to {{ $level['name'] }} or stay where I am?">&#128161; Ask AI if you should upgrade</button>
            <p class="layer-cta-note">&#128274; This layer builds on your current system &mdash; unlock to continue</p>
            @elseif($level['locked'])
            @if($level['rank'] > 1)
            <button type="button" class="btn btn-secondary layer-cta-explore" data-layer="level-{{ $level['rank'] }}">Explore this level &rarr;</button>
            @endif
            <button type="button" class="layer-ai-link js-ask-scan-chip"
              data-prompt="Based on my current scan, should I move to {{ $level['name'] }} or stay where I am?">&#128161; Get AI guidance on this level</button>
            <p class="layer-cta-note">&#128274; This layer builds on your current system &mdash; unlock to continue</p>
            @else
            @if($level['rank'] > 1)
            <button type="button" class="btn btn-secondary layer-cta-explore" data-layer="level-{{ $level['rank'] }}">Explore this level &rarr;</button>
            @else
            <a href="#priority-actions" class="btn btn-secondary">Review actions &rarr;</a>
            @endif
            <button type="button" class="layer-ai-link js-ask-scan-chip"
              data-prompt="Based on my current scan, should I move to {{ $level['name'] }} or stay where I am?">&#128161; Get AI guidance on this level</button>
            <p class="layer-cta-note">
              @if($level['rank'] === $unlockLevel)
                &#10004; Active &mdash; your current tier
              @else
                &#10004; Included in your plan
              @endif
            </p>
            @endif
            <p class="layer-momentum-note">{{ $momentumCopy }}</p>
          </article>
          @endforeach
        </div>
      </section>

      <section class="card" id="priority-actions">
        <div class="section-head">
          <h2>Your Next Move</h2>
          <div class="state-rail">
            <div class="state-chip"><strong>System State</strong><span>{{ $readoutState }}</span></div>
            <div class="state-chip"><strong>Pressure</strong><span>{{ $totalFailed > 0 ? 'Active' : 'Contained' }}</span></div>
            <div class="state-chip"><strong>Primary Constraint</strong><span>{{ count($sysActions) > 0 ? 'Detected' : 'Clear' }}</span></div>
          </div>
        </div>
        @if($primaryAction)
        <div class="next-move-guide">
          <h3>Your Next Move</h3>
          <div class="next-move-grid">
            <div class="next-move-card">
              <strong>Issue</strong>
              <p>{{ $primaryAction['label'] }}</p>
            </div>
            <div class="next-move-card">
              <strong>Fix</strong>
              <p>{{ $primaryAction['fix'] }}</p>
            </div>
            <div class="next-move-card">
              <strong>Action</strong>
              <p>Start with this fix now to unlock your next level.</p>
            </div>
          </div>
          <p style="margin:8px 0 0;font-size:.7rem;line-height:1.45;color:#e9ddc2"><strong style="font-size:.52rem;letter-spacing:.16em;text-transform:uppercase;color:#ddc88e">What happens after this fix</strong><br>After this, AI can clearly understand and recommend your service.</p>
          <div style="margin-top:8px">
            @if($singleNextStep)
            <a href="{{ $singleNextStep['href'] }}" class="btn btn-primary js-track-next-move-cta">{{ $momentumCta }}</a>
            @else
            <a href="#priority-actions" class="btn btn-primary js-track-next-move-cta">{{ $momentumCta }}</a>
            @endif
          </div>
          <p class="cta-time-value">Takes 2-5 minutes to apply. Start seeing improvements today.</p>
        </div>
        @endif
        <p style="margin:8px 12px 0;font-size:.72rem;color:#d9ceb0">Fix the top issue first. Move to the next level once it&rsquo;s resolved.</p>
        @if(!$isUpgraded)
        <div class="fix-friction-bar" id="fixFrictionBar">
          <p class="fix-friction-bar-title">At this level, without upgrading:</p>
          <ul>
            <li>Fixes are applied manually, one at a time</li>
            <li>Prioritization is limited to what you can see</li>
            <li>Deeper structural patterns remain hidden</li>
          </ul>
        </div>
        <div class="upgrade-pressure-bar" id="upgradePressureBar">
          <p><strong>You&rsquo;re making progress</strong>Your next gains come from structured prioritization — not manual guesswork.</p>
          <a href="{{ $singleNextStep['href'] ?? route('checkout.signal-expansion') }}" class="upgrade-pressure-bar-cta">Unlock Action Plan</a>
        </div>
        @endif
        <div class="actions">
          <div class="fix-progress-bar-wrap" id="fix-progress-wrap">
            <span class="fix-progress-label" id="fix-progress-label">0 of {{ $sysActionsLimit }} issue{{ $sysActionsLimit !== 1 ? 's' : '' }} addressed</span>
            <div class="fix-progress-bar"><div class="fix-progress-bar-fill" id="fix-progress-fill"></div></div>
          </div>
          <div class="action-stack">
            @for($i = 0; $i < $sysActionsLimit; $i++)
            @php
              $action = $sysActions[$i];
              $strength = $action['max'] >= 10 ? 'Weak' : ($action['max'] >= 5 ? 'Partial' : 'Stable');
              $isLead = $i === 0;
              $gainSignalsLow = max(4, (int) ceil($action['max'] * 1.6));
              $gainSignalsHigh = $gainSignalsLow + max(4, (int) ceil($action['max'] * .9));
              $reducibleBlockers = max(1, min(5, (int) ceil($action['max'] / 3)));
              $clarityLift = max(8, min(28, (int) ceil($action['max'] * 1.8)));
            @endphp
            <article class="action {{ $isLead ? 'lead' : 'secondary' }}" data-action-key="{{ md5(($action['label'] ?? '') . '|' . ($action['category'] ?? '') . '|' . ($i + 1)) }}">
              <div class="action-top">
                <span class="rank">#{{ $i + 1 }}</span>
                <span class="badge">{{ strtoupper($action['impact']) }} PRIORITY</span>
              </div>
              @if($isLead)
              <p class="start-cue">Start Here</p>
              @endif
              <h3>{{ $action['label'] }}</h3>
              <div class="inline">
                <span class="muted">AI cannot clearly understand this yet</span>
                <span>{{ $action['category'] }}</span>
              </div>
              @if($isLead)
              <div class="action-foot">
                <span>+{{ $action['max'] }} pts at stake</span>
                <span>Fix #{{ $i + 1 }}</span>
              </div>
              @endif
              <div class="action-outcome">
                <strong>Why It Matters</strong>
                <ul>
                  <li>AI confidence is reduced, so your content is less likely to surface first.</li>
                </ul>
              </div>
              <div class="action-outcome">
                <strong>What Improves</strong>
                <ul>
                  <li>+ clearer extraction confidence</li>
                  <li>+ stronger answer visibility</li>
                  <li>+ better category control potential</li>
                </ul>
                <p style="margin:6px 0 0;font-size:.62rem;color:#e7dcc0">This helps your content get selected more often.</p>
              </div>
              <div class="meter"><span style="width:{{ min(100, max(18, (int) $action['max'] * 10)) }}%" aria-label="{{ min(100, max(18, (int) $action['max'] * 10)) }} percent"></span></div>
              <div style="display:flex;flex-wrap:wrap;gap:6px">
                <span class="gain-pill">+{{ $gainSignalsLow }}-{{ $gainSignalsHigh }} trusted signals unlocked</span>
                <span class="gain-pill">up to {{ $reducibleBlockers }} blockers removed</span>
                <span class="gain-pill">+{{ $clarityLift }} confidence lift</span>
              </div>
              <div class="action-actions">
                <button
                  type="button"
                  class="btn btn-primary js-open-fix-detail"
                  data-exec-init="Deploying fix..."
                  data-exec-process="Applying constraint fix..."
                  data-exec-resolved="✓ Fix applied"
                  data-issue-name="{{ $action['label'] }}"
                  data-failure-state="{{ $action['why'] }}"
                  data-required-correction="{{ $action['fix'] }}"
                  data-impact-label="{{ ucfirst($action['impact']) }} impact"
                  data-impact-points="{{ $action['max'] }}"
                  data-state-label="{{ strtoupper($action['impact']) }} PRIORITY"
                  data-category-label="{{ $action['category'] }}"
                  data-next-path="Open Structural Layer Sequence"
                  data-why-matters="Selection pressure remains active while this constraint persists."
                  data-unlocks="Expand data layer coverage, introduce direct-answer nodes, establish authoritative definitions."
                >{{ $momentumCta }}</button>
                <p class="cta-social-proof">This is your highest-impact fix.</p>
                <p class="cta-consequence">This marks this issue as in progress inside your system.</p>
                <button
                  type="button"
                  class="btn btn-secondary js-open-fix-detail"
                  data-exec-init="Loading signal detail..."
                  data-exec-process="Pulling signal context..."
                  data-exec-resolved="Signal context loaded"
                  data-issue-name="{{ $action['label'] }}"
                  data-failure-state="{{ $action['why'] }}"
                  data-required-correction="{{ $action['fix'] }}"
                  data-impact-label="{{ ucfirst($action['impact']) }} impact"
                  data-impact-points="{{ $action['max'] }}"
                  data-state-label="{{ strtoupper($action['impact']) }} PRIORITY"
                  data-category-label="{{ $action['category'] }}"
                  data-next-path="Open Structural Layer Sequence"
                  data-why-matters="Selection pressure remains active while this constraint persists."
                  data-unlocks="Expand data layer coverage, introduce direct-answer nodes, establish authoritative definitions."
                >View Details</button>
              </div>
              <p class="action-memory">Not started</p>
            </article>
            @endfor

            @if(!$isUpgraded && count($sysActions) > 3)
            <article class="action secondary is-locked-card" data-action-key="locked-sequence-upgrade">
              <div class="action-top">
                <span class="rank"><span class="lock-glyph" aria-hidden="true"></span></span>
                <span class="badge"><span class="lock-glyph" aria-hidden="true"></span> RESTRICTED</span>
              </div>
              <h3>{{ count($sysActions) - 3 }} advanced fixes available</h3>
              <div class="inline">
                <span class="muted">State: restricted access</span>
                <span>Progress to unlock precision</span>
              </div>
              <div class="action-outcome">
                <strong>Contains</strong>
                <ul>
                  <li>Ranked constraint sequence</li>
                  <li>Full extraction failure map</li>
                  <li>Hidden answer opportunities</li>
                </ul>
              </div>
              <div class="action-outcome">
                <strong>Why It Matters</strong>
                <ul>
                  <li>This layer reveals the highest-leverage fixes before lower-impact work.</li>
                </ul>
              </div>
              <div class="action-outcome">
                <strong>What Improves</strong>
                <ul>
                  <li>+ priority clarity improves</li>
                  <li>+ faster high-impact execution</li>
                  <li>+ broader signal confidence</li>
                </ul>
              </div>
              <div class="action-actions">
                @if($singleNextStep)
                <a href="{{ $singleNextStep['href'] }}" class="btn btn-primary js-unlock-signal-expansion" data-exec-init="Opening layer access..." data-exec-process="Preparing layer access..." data-exec-resolved="Layer access initiated">Unlock {{ $nextUnlockName }}</a>
                @else
                <a href="{{ route('checkout.signal-expansion') }}" class="btn btn-primary js-unlock-signal-expansion" data-exec-init="Opening layer access..." data-exec-process="Preparing layer access..." data-exec-resolved="Layer access initiated">Unlock Signal Analysis</a>
                @endif
                <button type="button" class="btn btn-secondary" disabled><span class="lock-glyph" aria-hidden="true"></span> Preview Restricted Layer</button>
              </div>
              <p class="action-memory">Not started</p>
            </article>
            @endif
          </div>
        </div>
      </section>

      <section class="card modules" id="deeper-layers">
        <div class="section-head">
          <h2>Unlock More From Your Scan</h2>
          <p style="margin:0;font-size:.55rem;letter-spacing:.12em;text-transform:uppercase;color:#c0b38c">Each tier reveals a deeper layer</p>
        </div>
        <div class="accordion">
          @foreach($lockedLayerModules as $module)
          @if($module['rank'] > $unlockLevel)
          <details class="module locked">
            <summary>
              <span class="module-title">Layer {{ $module['rank'] }} · {{ $module['title'] }}</span>
              <span class="module-tag"><span class="lock-glyph" aria-hidden="true"></span> Restricted</span>
            </summary>
            <div class="module-body">
              <div class="locked-logic">
                <div class="locked-logic-item">
                  <strong>Contains</strong>
                  <p>{{ $module['statement'] }}</p>
                </div>
                <div class="locked-logic-item">
                  <strong>Why It Matters</strong>
                  <p>{{ $module['reveals'] }}</p>
                </div>
                <div class="locked-logic-item">
                  <strong>What Improves</strong>
                  <ul>
                    @foreach($module['improvement'] as $item)
                    <li>{{ $item }}</li>
                    @endforeach
                  </ul>
                </div>
              </div>
              <div class="module-cta" style="margin-top:10px">
                @php $modTierDef = $tierDefs[$module['rank']] ?? null; @endphp
                @if($modTierDef)
                <p class="module-tier-price">{{ $modTierDef['name'] }} &middot; {{ $modTierDef['price'] }}</p>
                @endif
                <a href="{{ $module['href'] }}" class="btn btn-primary">{{ $module['cta'] }}</a>
                <p class="cta-consequence">
                  @if($module['rank'] === 2)Reveals exact extraction failures + full signal map — removes active suppression
                  @elseif($module['rank'] === 3)Impact-ranked fix order — execute highest-leverage changes first
                  @else Exposes competitive weaknesses + full expansion map — full system activation
                  @endif
                </p>
              </div>
            </div>
          </details>
          @endif
          @endforeach
        </div>
      </section>

    </main>

    <aside class="card sticky" id="next-move">
      <p class="sticky-kicker">Your Next Move</p>
      <h2 class="sticky-title">{{ $singleNextStep['title'] ?? 'Next issue to address' }}</h2>
      <p class="sticky-copy">{{ $singleNextStep['copy'] ?? 'Fix this first to raise your score and move forward.' }}</p>
      <p class="sticky-unlock">This unlocks clearer signals, better fix order, and faster progress.</p>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:4px">
        @if($singleNextStep)
        <a href="{{ $singleNextStep['href'] }}" class="btn btn-primary js-track-sticky-cta">{{ $momentumCta }}</a>
        @else
        <a href="#priority-actions" class="btn btn-primary js-track-sticky-cta">{{ $momentumCta }}</a>
        @endif
      </div>
      <p class="cta-time-value">Takes 2-5 minutes to apply. Start seeing improvements today.</p>
      <p class="cta-consequence" style="margin:0 0 8px">{{ $singleNextStep ? 'Advances to ' . $nextUnlockName . ' → unlocks ranked priorities and signal depth' : 'Removes constraint → improves AI selection likelihood' }}</p>
      <a href="#deeper-layers" class="sticky-link">Preview Restricted Layers</a>

      <div class="consult-offer" id="consultOffer" data-show="{{ $showConsultationOffer ? 'true' : 'false' }}">
        <h3>Want help implementing this faster?</h3>
        <p>Once you have scan momentum, we can help you apply fixes in the right order.</p>
        <a href="{{ $consultationHref }}" class="btn btn-secondary js-track-consultation-cta">Request System Consultation</a>
      </div>
    </aside>
  </div>
</div>

  <footer class="footer">
    <div class="footer-brand">
      <strong>SEOAIco™</strong>
      <span class="footer-tagline">Programmatic AI SEO Infrastructure</span>
    </div>
    <div class="footer-meta">
      <p class="footer-copyright">&copy; 2026 — All systems active</p>
      <p class="footer-links">
        <a href="{{ route('privacy') }}">Privacy</a> ·
        <a href="{{ route('terms') }}">Terms</a> ·
        <a href="/pricing">AI Citation Scan</a>
      </p>
    </div>
  </footer>

<div class="fix-detail-mask" id="fixDetailMask" data-open="false" role="dialog" aria-modal="true" aria-labelledby="fixDetailTitle">
  <aside class="fix-detail-panel">
    <div class="fix-detail-inner">
      <div class="fix-detail-head">
        <p class="fix-detail-kicker">Fix Details</p>
        <button type="button" class="fix-detail-close" id="fixDetailCloseTop">Close</button>
      </div>

      <div class="fix-detail-module">
        <h3 class="fix-detail-title" id="fixDetailTitle">Issue Detail</h3>
        <div class="fix-detail-meta">
          <span class="fix-detail-pill" id="fixDetailState">Constraint Active</span>
          <span class="fix-detail-pill" id="fixDetailImpact">High impact</span>
          <span class="fix-detail-points" id="fixDetailPoints">0 pts at stake</span>
        </div>
      </div>

      <div class="fix-detail-progression">
        <div class="progression-cell">
          <strong>Current State</strong>
          <p>{{ $currentStateSummary }}</p>
        </div>
        <div class="progression-cell">
          <strong>Next Unlock</strong>
          <p>{{ $nextUnlockName }}</p>
        </div>
        <div class="progression-cell">
          <strong>Unlocks</strong>
          <ul class="progression-list">
            @foreach($nextUnlockBullets as $bullet)
            <li>{{ $bullet }}</li>
            @endforeach
          </ul>
        </div>
      </div>

        <div class="fix-detail-grid">
        <div class="fix-detail-block"><p>Current state</p><p id="fixDetailFailure">Unavailable</p></div>
        <div class="fix-detail-block"><p>Why it matters</p><p id="fixDetailWhy">Unavailable</p></div>
        <div class="fix-detail-block"><p>What to do</p><p id="fixDetailCorrection">Unavailable</p></div>
        <div class="fix-detail-block"><p>What improves</p><p id="fixDetailUnlocks">Unavailable</p></div>
        <div class="fix-detail-block"><p>Impact if ignored</p><p id="fixDetailConsequence">Unavailable</p></div>
        <div class="fix-detail-block"><p>Category</p><p id="fixDetailCategory">Unavailable</p></div>
      </div>

      <div class="fix-what-happens">
        <p class="fix-what-happens-title">What happens when you apply this fix</p>
        <ul class="fix-what-happens-list">
          <li>This marks the issue as addressed so your dashboard can track progress and guide your next steps.</li>
          <li>This does not automatically change your site — it helps you track and prioritize fixes.</li>
          <li>Your next highest-priority constraint becomes the suggested next move.</li>
        </ul>
      </div>
      @if(!$isUpgraded)
      <div class="fix-locked-insight" id="fixLockedInsight">
        <p class="fix-locked-insight-title">Deeper Insight Available</p>
        <p>This issue is part of a broader structural pattern across your site. Unlocking the next layer reveals:</p>
        <ul>
          <li>How this issue connects to other pages</li>
          <li>Which pages are most affected</li>
          <li>The optimal order of fixes across your system</li>
        </ul>
        <div class="fix-locked-partial">
          <div class="fix-locked-partial-head">Additional impacted pages</div>
          <div class="fix-locked-partial-row">Page structure layer — signal: detected</div>
          <div class="fix-locked-partial-row">Content depth layer — signal: partial</div>
          <div class="fix-locked-partial-row is-blurred">Authority layer — signal: suppressed</div>
          <div class="fix-locked-partial-row is-blurred">Extraction node map — status: restricted</div>
          <div class="fix-locked-partial-row is-blurred">Competitive gap analysis — status: locked</div>
        </div>
        <div style="margin-top:9px">
          <a href="{{ $singleNextStep['href'] ?? route('checkout.signal-expansion') }}" class="btn btn-primary" style="font-size:.56rem;min-height:34px;padding:7px 13px">Unlock Full Fix Strategy</a>
        </div>
      </div>
      @endif
      <div class="fix-detail-actions">
        <a href="#priority-actions" class="btn btn-primary" id="fixDetailNext">Apply Fix</a>
        <button type="button" class="btn btn-secondary" id="fixDetailClose">Close Panel</button>
      </div>
    </div>
  </aside>
</div>

<script>
(function () {
  var nav = document.getElementById('nav');
  if (nav) {
    window.addEventListener('scroll', function () {
      nav.classList.toggle('stuck', scrollY > 60);
    }, { passive: true });
  }

  var fixDetailMask = document.getElementById('fixDetailMask');
  var fixDetailClose = document.getElementById('fixDetailClose');
  var fixDetailCloseTop = document.getElementById('fixDetailCloseTop');
  var fixDetailTitle = document.getElementById('fixDetailTitle');
  var fixDetailState = document.getElementById('fixDetailState');
  var fixDetailImpact = document.getElementById('fixDetailImpact');
  var fixDetailPoints = document.getElementById('fixDetailPoints');
  var fixDetailFailure = document.getElementById('fixDetailFailure');
  var fixDetailWhy = document.getElementById('fixDetailWhy');
  var fixDetailCorrection = document.getElementById('fixDetailCorrection');
  var fixDetailUnlocks = document.getElementById('fixDetailUnlocks');
  var fixDetailConsequence = document.getElementById('fixDetailConsequence');
  var fixDetailCategory = document.getElementById('fixDetailCategory');
  var liveFeedbackStrip = document.getElementById('liveFeedbackStrip');
  var liveFeedbackText = document.getElementById('liveFeedbackText');
  var consultOffer = document.getElementById('consultOffer');
  var progressionLayers = Array.prototype.slice.call(document.querySelectorAll('#layers .layer'));

  function emitTracking(eventName, payload) {
    var safePayload = payload && typeof payload === 'object' ? payload : {};
    if (Array.isArray(window.dataLayer)) {
      window.dataLayer.push(Object.assign({ event: eventName }, safePayload));
      return;
    }
    if (typeof window.gtag === 'function') {
      window.gtag('event', eventName, safePayload);
    }
  }

  function bindClickTracking(selector, eventName, extra) {
    document.querySelectorAll(selector).forEach(function (el) {
      el.addEventListener('click', function () {
        var href = el.getAttribute('href') || '';
        emitTracking(eventName, Object.assign({
          scan_id: {{ (int) $scan->id }},
          href: href,
          location: extra && extra.location ? extra.location : ''
        }, extra || {}));
      });
    });
  }

  function rotateFeedback() {
    if (!liveFeedbackStrip || !liveFeedbackText) return;
    var raw = liveFeedbackStrip.dataset.feedbackMessages || '[]';
    var messages;
    try {
      messages = JSON.parse(raw);
    } catch (err) {
      messages = [];
    }
    if (!Array.isArray(messages) || messages.length < 2) return;

    var idx = 0;
    window.setInterval(function () {
      idx = (idx + 1) % messages.length;
      liveFeedbackText.classList.add('is-swapping');
      window.setTimeout(function () {
        liveFeedbackText.textContent = messages[idx];
        liveFeedbackText.classList.remove('is-swapping');
      }, 230);
    }, 3400);
  }

  var ACTION_MEMORY_PREFIX = 'seoai.fix.action.';

  function setActionMemory(card, text) {
    if (!card) return;
    var line = card.querySelector('.action-memory');
    if (!line) return;
    line.textContent = text;
  }

  function addAppliedBadge(card) {
    if (!card || card.querySelector('.action-applied-badge')) return;
    var h3 = card.querySelector('h3');
    if (!h3) return;
    var badge = document.createElement('span');
    badge.className = 'action-applied-badge';
    badge.textContent = '✓ Applied';
    h3.parentNode.insertBefore(badge, h3.nextSibling);
  }

  function triggerApplyGlow(card) {
    if (!card) return;
    card.classList.add('is-applying');
    window.setTimeout(function () { card.classList.remove('is-applying'); }, 1050);
  }

  function showResultFeedback(card) {
    if (!card) return;
    var memLine = card.querySelector('.action-memory');
    if (!memLine) return;
    memLine.textContent = 'Signal clarity improving…';
    window.setTimeout(function () {
      memLine.classList.add('is-fading');
      window.setTimeout(function () {
        memLine.textContent = '✓ Tracked — awaiting validation';
        memLine.classList.remove('is-fading');
      }, 380);
    }, 2400);
  }

  function animateScoreBump() {
    var scoreEl = document.querySelector('.orbit-score');
    if (!scoreEl) return;
    var current = parseInt(scoreEl.textContent || '0', 10);
    if (!Number.isFinite(current) || current <= 0) return;
    var bump = 1 + Math.floor(Math.random() * 3);
    var target = Math.min(100, current + bump);
    var steps = 14, step = 0;
    var up = window.setInterval(function () {
      step++;
      scoreEl.textContent = Math.round(current + (target - current) * (step / steps));
      if (step >= steps) {
        clearInterval(up);
        scoreEl.textContent = target;
        window.setTimeout(function () {
          var rs = 0;
          var down = window.setInterval(function () {
            rs++;
            scoreEl.textContent = Math.round(target - (target - current) * (rs / steps));
            if (rs >= steps) { clearInterval(down); scoreEl.textContent = current; }
          }, 42);
        }, 4200);
      }
    }, 42);
  }

  function updateProgressBar() {
    var wrap = document.getElementById('fix-progress-wrap');
    var fill = document.getElementById('fix-progress-fill');
    var label = document.getElementById('fix-progress-label');
    if (!wrap || !fill || !label) return;
    var total = document.querySelectorAll('.action[data-action-key]:not(.is-locked-card)').length;
    var done  = document.querySelectorAll('.action.is-applied').length;
    if (total === 0) return;
    wrap.style.display = 'flex';
    fill.style.width = Math.round((done / total) * 100) + '%';
    label.textContent = done + ' of ' + total + ' issue' + (total !== 1 ? 's' : '') + ' addressed';
    // Show friction bar after first fix
    var frictionBar = document.getElementById('fixFrictionBar');
    if (frictionBar && done >= 1) frictionBar.classList.add('is-visible');
    // Show upgrade pressure bar after 2+ fixes OR >20% progress
    var pressureBar = document.getElementById('upgradePressureBar');
    if (pressureBar && (done >= 2 || Math.round((done / total) * 100) > 20)) {
      pressureBar.classList.add('is-visible');
    }
  }

  function showAiNudge() {
    var done = document.querySelectorAll('.action.is-applied').length;
    var isEscalation = done >= 2;
    var old = document.getElementById('fix-ai-nudge');
    if (old) old.remove();
    var nudge = document.createElement('div');
    nudge.id = 'fix-ai-nudge';
    nudge.className = 'fix-ai-nudge';
    var aiLabel = document.createElement('span');
    aiLabel.className = 'fix-ai-nudge-label';
    aiLabel.textContent = 'AI';
    var msg = document.createElement('p');
    if (isEscalation) {
      msg.textContent = 'You\'re doing this manually right now. I can show you the exact order of fixes to maximize impact — want to unlock that?';
    } else {
      msg.textContent = 'Nice \u2014 that removes a key constraint. Want to move to the next highest-impact fix?';
    }
    var cta = document.createElement('button');
    cta.type = 'button';
    cta.className = 'fix-ai-nudge-cta';
    cta.textContent = isEscalation ? 'Unlock Strategy' : 'Ask AI';
    var dismiss = document.createElement('button');
    dismiss.type = 'button';
    dismiss.className = 'fix-ai-nudge-dismiss';
    dismiss.setAttribute('aria-label', 'Dismiss');
    dismiss.textContent = '\u00d7';
    nudge.appendChild(aiLabel);
    nudge.appendChild(msg);
    nudge.appendChild(cta);
    nudge.appendChild(dismiss);
    document.body.appendChild(nudge);
    window.setTimeout(function () { nudge.classList.add('is-visible'); }, 60);
    dismiss.addEventListener('click', function () { nudge.remove(); });
    cta.addEventListener('click', function () {
      nudge.remove();
      if (isEscalation) {
        var bar = document.getElementById('upgradePressureBar');
        if (bar) { bar.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
      } else {
        var aiTrigger = document.getElementById('aiaTrigger');
        if (aiTrigger) aiTrigger.click();
      }
    });
    window.setTimeout(function () { if (nudge.parentNode) nudge.remove(); }, 9000);
  }

  function syncNextMovePanel() {
    var leadCard = document.querySelector('.action.lead');
    if (!leadCard || !leadCard.classList.contains('is-applied')) return;
    var done = document.querySelectorAll('.action.is-applied').length;
    var guide = document.querySelector('.next-move-guide');
    if (!guide) return;
    var h3 = guide.querySelector('h3');
    var cells = guide.querySelectorAll('.next-move-card');
    var subP = guide.querySelector('p[style]');
    var cta = guide.querySelector('.btn-primary');
    if (done >= 2) {
      // Multi-fix upgrade state
      guide.style.borderColor = 'rgba(214,181,95,.48)';
      guide.style.background = 'rgba(214,181,95,.08)';
      if (h3) { h3.textContent = 'Structured execution unlocks next-level gains'; h3.style.color = '#efdcae'; }
      if (cells[0]) {
        var s0 = cells[0].querySelector('strong'); if (s0) s0.textContent = 'Status';
        var p0 = cells[0].querySelector('p'); if (p0) p0.textContent = 'Initial blockers removed';
      }
      if (cells[1]) {
        var p1 = cells[1].querySelector('p');
        if (p1) p1.innerHTML = '\u2192 Optimize fix order<br>\u2192 Identify highest-impact pages<br>\u2192 Move faster with structured execution';
      }
      if (cells[2]) {
        var p2 = cells[2].querySelector('p'); if (p2) p2.textContent = 'Upgrade to unlock the full fix sequence.';
      }
      if (subP) subP.innerHTML = "<strong style='font-size:.52rem;letter-spacing:.16em;text-transform:uppercase;color:#ddc88e'>You've removed initial blockers</strong><br>Next step: structured prioritization moves faster.";
      if (cta) { cta.textContent = 'Upgrade to Action Plan'; cta.setAttribute('href', cta.getAttribute('href') || '#'); }
    } else {
      // Single fix state
      guide.style.borderColor = 'rgba(106,175,144,.36)';
      guide.style.background = 'rgba(106,175,144,.06)';
      if (h3) { h3.textContent = 'Next priority unlocked'; h3.style.color = '#7abb9e'; }
      if (cells[0]) {
        var s0b = cells[0].querySelector('strong'); if (s0b) s0b.textContent = 'Status';
        var p0b = cells[0].querySelector('p'); if (p0b) p0b.textContent = 'Top constraint marked as handled';
      }
      if (cells[1]) {
        var p1b = cells[1].querySelector('p'); if (p1b) p1b.textContent = 'Apply the next fix to keep momentum building.';
      }
      if (cells[2]) {
        var p2b = cells[2].querySelector('p'); if (p2b) p2b.textContent = 'Move to fix #2 below to continue strengthening your system.';
      }
      if (subP) subP.innerHTML = "<strong style='font-size:.52rem;letter-spacing:.16em;text-transform:uppercase;color:#7abb9e'>You've cleared the top blocker</strong><br>Continue to strengthen your system.";
      if (cta) { cta.textContent = 'Apply next fix'; cta.setAttribute('href', '#priority-actions'); }
    }
  }

  function restoreActionMemory() {
    document.querySelectorAll('.action[data-action-key]').forEach(function (card) {
      var key = card.dataset.actionKey || '';
      if (!key) return;
      var raw; try { raw = localStorage.getItem(ACTION_MEMORY_PREFIX + key); } catch (e) { raw = null; }
      if (!raw) return;
      try {
        var parsed = JSON.parse(raw);
        var at = Number(parsed.at || 0);
        if (!Number.isFinite(at) || at <= 0) return;
        setActionMemory(card, '✓ Tracked — awaiting validation');
        card.classList.add('is-applied');
        addAppliedBadge(card);
        var applyBtn = card.querySelector('button[data-exec-init]');
        if (applyBtn) {
          applyBtn.textContent = '✓ Applied';
          applyBtn.disabled = true;
          applyBtn.setAttribute('aria-disabled', 'true');
          applyBtn.classList.add('is-applied', 'is-disabled');
        }
      } catch (err) {
        try { localStorage.removeItem(ACTION_MEMORY_PREFIX + key); } catch (e) {}
      }
    });
    syncNextMovePanel();
    updateProgressBar();
  }

  function openFixDetail(trigger) {
    if (!fixDetailMask || !trigger) return;

    var issueName = trigger.dataset.issueName || 'Issue Detail';
    var stateLabel = trigger.dataset.stateLabel || 'Constraint Active';
    var impactLabel = trigger.dataset.impactLabel || 'High impact';
    var impactPoints = trigger.dataset.impactPoints || '0';
    var failureState = trigger.dataset.failureState || 'Unavailable';
    var whyMatters = trigger.dataset.whyMatters || failureState;
    var requiredCorrection = trigger.dataset.requiredCorrection || 'Expand data layer coverage.';
    var unlocks = trigger.dataset.unlocks || 'Introduces direct-answer nodes and stronger authority definitions.';
    var category = trigger.dataset.categoryLabel || 'System';

    fixDetailTitle.textContent = issueName;
    fixDetailState.textContent = stateLabel;
    fixDetailImpact.textContent = impactLabel;
    fixDetailPoints.textContent = impactPoints + ' pts at stake';
    fixDetailFailure.textContent = failureState;
    fixDetailWhy.textContent = whyMatters;
    fixDetailCorrection.textContent = requiredCorrection;
    fixDetailUnlocks.textContent = unlocks;
    fixDetailConsequence.textContent = 'Selection pressure remains active while this constraint persists.';
    fixDetailCategory.textContent = category;

    fixDetailMask.dataset.open = 'true';
    document.body.style.overflow = 'hidden';
    document.body.classList.add('fix-panel-open');
    // Show locked insight for non-upgraded users (delay slightly for panel animation)
    var lockedInsight = document.getElementById('fixLockedInsight');
    if (lockedInsight) {
      window.setTimeout(function () { lockedInsight.classList.add('is-visible'); }, 260);
    }
  }

  function closeFixDetail() {
    if (!fixDetailMask) return;
    fixDetailMask.dataset.open = 'false';
    document.body.style.overflow = '';
    document.body.classList.remove('fix-panel-open');
  }

  function runActionExecution(trigger, onComplete) {
    var card = trigger ? trigger.closest('.action') : null;
    if (!card || card.dataset.execBusy === 'true' || card.classList.contains('is-applied')) return;

    var nodes = card.querySelectorAll('.btn');
    var initLabel = trigger.dataset.execInit || 'Deploying fix...';
    var processLabel = trigger.dataset.execProcess || 'Processing selection model...';
var resolvedLabel = trigger.dataset.execResolved || '✓ Fix applied';

    card.dataset.execBusy = 'true';
    card.classList.add('is-executing');
    trigger.classList.add('is-executing');
    nodes.forEach(function (node) {
      if (!node.dataset.originalLabel) node.dataset.originalLabel = (node.textContent || '').trim();
      node.classList.add('is-disabled');
      node.setAttribute('aria-disabled', 'true');
      if (node.tagName === 'BUTTON') node.disabled = true;
    });

    trigger.textContent = initLabel;

    var phaseDelay = 190 + Math.floor(Math.random() * 140);
    window.setTimeout(function () {
      trigger.textContent = processLabel;
    }, phaseDelay);

    var delayMs = 520 + Math.floor(Math.random() * 520);
    window.setTimeout(function () {
      var actionKey = card.dataset.actionKey || '';
      var outcomeLine = 'Constraint resolved -> selection pressure reduced -> signal clarity increased';
      if (actionKey) {
        try { localStorage.setItem(ACTION_MEMORY_PREFIX + actionKey, JSON.stringify({ at: Date.now(), outcome: outcomeLine, applied: true })); } catch (e) {}
      }
      setActionMemory(card, '✓ Fix applied — tracking signal impact');
      trigger.textContent = '✓ Applied';
      card.classList.remove('is-executing');
      card.classList.add('is-applied');
      addAppliedBadge(card);
      trigger.classList.remove('is-executing');
      trigger.classList.add('is-applied', 'is-disabled');
      trigger.setAttribute('aria-disabled', 'true');
      // briefly highlight next card as suggested next step
      (function () {
        var allCards = document.querySelectorAll('.action:not(.is-locked-card)');
        var idx = Array.from(allCards).indexOf(card);
        var nextCard = allCards[idx + 1];
        if (nextCard) {
          window.setTimeout(function () {
            nextCard.classList.add('next-focus');
            window.setTimeout(function () { nextCard.classList.remove('next-focus'); }, 1200);
          }, 420);
        }
      }());
      syncNextMovePanel();
      triggerApplyGlow(card);
      showResultFeedback(card);
      window.setTimeout(function () { animateScoreBump(); }, 340);
      window.setTimeout(function () { updateProgressBar(); showAiNudge(); }, 720);

      // Re-enable non-trigger buttons; keep trigger permanently disabled
      nodes.forEach(function (node) {
        if (node === trigger) return;
        node.classList.remove('is-disabled');
        node.removeAttribute('aria-disabled');
        if (node.tagName === 'BUTTON') node.disabled = false;
      });

      card.dataset.execBusy = 'false';
      if (typeof onComplete === 'function') onComplete();
      registerConsultEngagement();
    }, delayMs);
  }

  var CONSULT_ENGAGEMENT_KEY = 'seoai.consult.engagement.count';

  function getConsultEngagementCount() {
    var raw = sessionStorage.getItem(CONSULT_ENGAGEMENT_KEY) || '0';
    var parsed = parseInt(raw, 10);
    return Number.isFinite(parsed) ? Math.max(0, parsed) : 0;
  }

  function setConsultEngagementCount(count) {
    sessionStorage.setItem(CONSULT_ENGAGEMENT_KEY, String(Math.max(0, count)));
  }

  function maybeShowConsultOffer() {
    if (!consultOffer) return;
    if (consultOffer.dataset.show === 'true') return;

    if (getConsultEngagementCount() >= 2) {
      consultOffer.dataset.show = 'true';
      emitTracking('scan_result_consultation_shown', {
        scan_id: {{ (int) $scan->id }},
        trigger: 'engagement'
      });
    }
  }

  function registerConsultEngagement() {
    setConsultEngagementCount(getConsultEngagementCount() + 1);
    maybeShowConsultOffer();
  }

  document.querySelectorAll('.js-open-fix-detail').forEach(function (btn) {
    btn.addEventListener('click', function () {
      runActionExecution(btn, function () {
        openFixDetail(btn);
      });
    });
  });

  document.querySelectorAll('.js-unlock-signal-expansion').forEach(function (link) {
    link.addEventListener('click', function (evt) {
      evt.preventDefault();
      var href = link.getAttribute('href');
      if (!href) return;
      runActionExecution(link, function () {
        window.location.href = href;
      });
    });
  });

  bindClickTracking('.js-track-primary-cta', 'scan_result_primary_cta_click', { location: 'hero' });
  bindClickTracking('.js-track-next-move-cta', 'scan_result_next_move_click', { location: 'next_move' });
  bindClickTracking('.js-track-sticky-cta', 'scan_result_sticky_cta_click', { location: 'sticky' });
  bindClickTracking('.js-track-consultation-cta', 'scan_result_consultation_click', { location: 'consult_offer' });

  (function setupScrollDepthTracking() {
    var thresholds = [50, 75, 100];
    var sent = {};
    var ticking = false;

    function getScrollPercent() {
      var doc = document.documentElement;
      var scrollTop = window.pageYOffset || doc.scrollTop || 0;
      var maxScroll = Math.max(1, (doc.scrollHeight - window.innerHeight));
      var pct = Math.round((scrollTop / maxScroll) * 100);
      return Math.max(0, Math.min(100, pct));
    }

    function checkDepth() {
      ticking = false;
      var pct = getScrollPercent();
      thresholds.forEach(function (t) {
        if (!sent[t] && pct >= t) {
          sent[t] = true;
          emitTracking('scan_result_scroll_depth', {
            scan_id: {{ (int) $scan->id }},
            depth_percent: t
          });
        }
      });
    }

    function onScroll() {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(checkDepth);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    checkDepth();
  })();

  (function setupProgressionTracking() {
    progressionLayers.forEach(function (layer, idx) {
      var levelNameNode = layer.querySelector('.layer-name');
      var levelName = levelNameNode ? (levelNameNode.textContent || '').trim() : ('level_' + (idx + 1));
      var hovered = false;

      layer.addEventListener('mouseenter', function () {
        if (hovered) return;
        hovered = true;
        emitTracking('scan_result_progression_engaged', {
          scan_id: {{ (int) $scan->id }},
          interaction: 'hover',
          level: levelName,
          level_index: idx + 1
        });
      });

      layer.querySelectorAll('a,button').forEach(function (node) {
        node.addEventListener('click', function () {
          emitTracking('scan_result_progression_engaged', {
            scan_id: {{ (int) $scan->id }},
            interaction: 'click',
            level: levelName,
            level_index: idx + 1
          });
        });
      });
    });
  })();

  if (fixDetailClose) fixDetailClose.addEventListener('click', closeFixDetail);
  if (fixDetailCloseTop) fixDetailCloseTop.addEventListener('click', closeFixDetail);
  if (fixDetailMask) {
    fixDetailMask.addEventListener('click', function (evt) {
      if (evt.target === fixDetailMask) closeFixDetail();
    });
  }
  document.addEventListener('keydown', function (evt) {
    if (evt.key === 'Escape' && fixDetailMask && fixDetailMask.dataset.open === 'true') closeFixDetail();
  });

  rotateFeedback();
  restoreActionMemory();
  if (consultOffer && consultOffer.dataset.show === 'true') {
    emitTracking('scan_result_consultation_shown', {
      scan_id: {{ (int) $scan->id }},
      trigger: 'initial'
    });
  }
  maybeShowConsultOffer();
})();
</script>
@php
$_scanAiGreeting = auth()->check()
    ? "Your scan for {$scan->domain()} scored {$score}/100 — {$scoreSelectionInterpretation}.\n\nThe fastest way to improve: {$topBottleneck}.\n\nI can walk you through each fix, explain what it improves, or help you decide what to do next."
    : "Your scan scored {$score}/100 — {$scoreSelectionInterpretation}.\n\nI can walk you through each fix, explain what it improves, or help you decide what to do next.";
$_scanAiPrompts = [
    "Why is my score {$score} and what does it mean?",
    "What should I fix first?",
    "What does my {$currentTierName} plan include?",
    "What would upgrading reveal?",
];
@endphp
@include('components.ai-assistant', [
    'aiGreeting'         => $_scanAiGreeting,
    'aiSuggestedPrompts' => $_scanAiPrompts,
    'aiMicroLabel'       => 'Ask about your score',
    'aiTeaserTitle'      => 'Ask about your results',
    'aiTeaserText'       => 'I can explain your score, what to fix first, or what upgrading would reveal.',
])
<script>
(function () {
  'use strict';
  // Wire scan-page "Ask AI" chips and the "Open AI Advisor" button
  // into the floating assistant panel.
  function openAiPanel(prompt) {
    var trigger = document.getElementById('aiaTrigger');
    var inputEl = document.getElementById('aiaInput');
    var sendEl  = document.getElementById('aiaSend');
    if (!trigger) return;
    // Open panel
    if (trigger.getAttribute('aria-expanded') !== 'true') {
      trigger.click();
    }
    if (!prompt) return;
    // After greeting renders (~400ms), populate and send
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

  document.addEventListener('click', function (e) {
    var target = e.target;
    // Scan page chips
    if (target.classList.contains('js-ask-scan-chip') || target.classList.contains('js-open-ai-panel')) {
      var prompt = target.dataset.prompt || '';
      openAiPanel(prompt);
    }
    // Hero "Ask about this scan" button (no prompt — just open)
    if (target.classList.contains('js-open-ai-no-prompt')) {
      openAiPanel('');
    }
  });
})();
</script>
@include('partials.back-to-top')
@include('components.tm-style')
@include('components.layer-modal')
@include('partials.public-nav-js')
</body>
</html>
