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
    2 => ['name' => 'Signal Expansion', 'plan' => 'diagnostic', 'price' => '$99', 'value' => 'Reveal the full signal architecture behind your score.'],
    3 => ['name' => 'Structural Leverage', 'plan' => 'fix-strategy', 'price' => '$249', 'value' => 'This unlocks smarter prioritization so you fix what matters first.'],
    4 => ['name' => 'System Activation', 'plan' => 'optimization', 'price' => '$489', 'value' => 'Expose competitive position and expansion intelligence.'],
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
      default => 'Layer 4 active - full system activation available',
    };
    $nextUnlockName = $nextTier['name'] ?? 'System Activation';
    $nextUnlockBullets = match ($nextUnlockName) {
        'Signal Expansion' => ['full signal architecture', 'deeper failure mapping', 'ranked correction visibility'],
        'Structural Leverage' => ['ranked correction sequencing', 'constraint-to-impact mapping', 'execution priority visibility'],
        'System Activation' => ['competitive intelligence layer', 'expansion opportunity map', 'deployment-grade direction'],
        default => ['signal architecture expansion', 'failure visibility increase', 'selection readiness growth'],
    };

    $nextUnlockWhyMatters = match ($nextUnlockName) {
      'Signal Expansion' => 'This reveals exactly where AI cannot extract or trust your site signals.',
      'Structural Leverage' => 'This exposes highest-impact fixes first so lower-value work does not delay outcomes.',
      'System Activation' => 'This exposes competitive displacement risk and strategic control opportunities.',
      default => 'This unlock clarifies where trust and extraction are currently limited.',
    };

    $nextUnlockWhyShort = match ($nextUnlockName) {
      'Signal Expansion' => 'Reveals where extraction and trust fail.',
      'Structural Leverage' => 'Reorders fixes by impact for faster gains.',
      'System Activation' => 'Reveals where market control is won or lost.',
      default => 'Clarifies where trust and extraction remain limited.',
    };

    $nextUnlockImproves = match ($nextUnlockName) {
      'Signal Expansion' => ['clearer extraction insight', 'higher signal trust', 'better fix focus'],
      'Structural Leverage' => ['faster high-impact execution', 'cleaner fix order', 'stronger readiness lift'],
      'System Activation' => ['market control visibility', 'expansion precision', 'stronger synthesis readiness'],
      default => ['clearer prioritization', 'higher insight quality', 'stronger readiness confidence'],
    };

    $layerProgressModel = [
      ['rank' => 1, 'label' => 'Current visibility confirmed'],
      ['rank' => 2, 'label' => 'Signal map opened'],
      ['rank' => 3, 'label' => 'Correction sequence unlocked'],
      ['rank' => 4, 'label' => 'System activation / market control'],
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
          ['rank' => 2, 'name' => 'Signal Expansion', 'status' => $unlockLevel >= 2 ? 'Included' : 'Locked', 'enabled' => $unlockLevel >= 2, 'value' => 'This layer opens full signal mapping where extraction currently stops.', 'cta' => $unlockLevel >= 2 ? '#priority-actions' : ($singleNextStep['href'] ?? route('checkout.signal-expansion')), 'cta_label' => $unlockLevel >= 2 ? 'View Fix Sequence' : 'Unlock Signal Expansion', 'cta_note' => $unlockLevel >= 2 ? 'Opens signal-map-guided fixes.' : 'Reveals full signal architecture.'],
          ['rank' => 3, 'name' => 'Structural Leverage', 'status' => $unlockLevel >= 3 ? 'Included' : 'Locked', 'enabled' => $unlockLevel >= 3, 'value' => 'This layer ranks corrections by impact so execution follows leverage.', 'cta' => $unlockLevel >= 3 ? '#priority-actions' : ($singleNextStep['href'] ?? route('quick-scan.upgrade')), 'cta_label' => $unlockLevel >= 3 ? 'View Fix Sequence' : 'Unlock Structural Leverage', 'cta_note' => $unlockLevel >= 3 ? 'Opens impact-ranked correction order.' : 'Unlocks ranked fixes by impact.'],
          ['rank' => 4, 'name' => 'System Activation', 'status' => $unlockLevel >= 4 ? 'Included' : 'Locked', 'enabled' => $unlockLevel >= 4, 'value' => 'This layer unlocks competitive control insights and market-level direction.', 'cta' => $unlockLevel >= 4 ? '#priority-actions' : ($singleNextStep['href'] ?? route('quick-scan.upgrade')), 'cta_label' => $unlockLevel >= 4 ? 'View Fix Sequence' : 'Unlock System Activation', 'cta_note' => $unlockLevel >= 4 ? 'Opens competitive signal controls.' : 'Reveals competitive gaps and expansion map.'],
    ];

  $findings = [
      ['title' => 'Coverage', 'state' => $coveragePct >= 70 ? 'Selection Lifted' : ($coveragePct >= 45 ? 'Selection Diluted' : 'Selection Suppressed'), 'copy' => 'Low coverage → AI excludes your domain from final answers.', 'pct' => $coveragePct],
      ['title' => 'Authority', 'state' => $authorityPct >= 70 ? 'Trust Weighting Active' : ($authorityPct >= 45 ? 'Trust Weighting Reduced' : 'Trust Weighting Lost'), 'copy' => 'Weak authority → AI routes to stronger competitor signals instead.', 'pct' => $authorityPct],
      ['title' => 'Structure', 'state' => $structurePct >= 70 ? 'Extraction Reliable' : ($structurePct >= 45 ? 'Extraction Unstable' : 'Extraction Failing'), 'copy' => 'Unstable structure → AI skips your pages when extracting answers.', 'pct' => $structurePct],
      ['title' => 'Selection Readiness', 'state' => $selectionReadiness >= 70 ? 'Selection Probability Rising' : ($selectionReadiness >= 45 ? 'Selection Probability Contested' : 'Selection Probability Collapsing'), 'copy' => 'Readiness score directly controls AI domain selection vs competitors.', 'pct' => $selectionReadiness],
  ];

  $lockedLayerModules = [
      ['rank' => 2, 'title' => 'Signal Expansion', 'statement' => 'Full signal architecture, extraction failure tree, and suppressed answer opportunities.', 'reveals' => 'While suppressed, AI cannot map complete service depth. Selection bias toward competitors with fuller signal graphs persists.', 'improvement' => ['Selection pressure reduced', 'Signal coverage depth increased', 'Extraction failure visibility restored'], 'cta' => 'Unlock Signal Expansion', 'href' => $singleNextStep['href'] ?? route('checkout.signal-expansion')],
      ['rank' => 3, 'title' => 'Structural Leverage', 'statement' => 'Ranked remediation path, constraint-to-impact map, and fix priority sequence.', 'reveals' => 'Without ranked correction order, fixes compound inefficiency. Highest-ROI constraints remain buried below lower-value work.', 'improvement' => ['Correction sequencing clarified', 'Fix efficiency increased', 'Constraint compounding halted'], 'cta' => 'Unlock Structural Leverage', 'href' => $singleNextStep['href'] ?? route('quick-scan.upgrade')],
      ['rank' => 4, 'title' => 'System Activation', 'statement' => 'Competitive intelligence layer, market displacement signals, and activation-grade expansion map.', 'reveals' => 'Without activation, competitive entities dominate synthesis-level recommendation slots. AI preference shifts toward fuller-signal domains.', 'improvement' => ['Competitive position exposed', 'Expansion opportunity map unlocked', 'Displacement risk quantified'], 'cta' => 'Unlock System Activation', 'href' => $singleNextStep['href'] ?? route('quick-scan.upgrade')],
  ];

  $humanTranslation = match (true) {
      $score >= 85 => 'AI can see you clearly, but stronger competitors still provide more complete extraction signals.',
      $score >= 60 => 'Your site is visible, but not yet structurally strong enough to control answer selection.',
      default => 'You are detectable, but AI still lacks enough trusted structure to confidently select your site.',
  };
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
.hero-domain{font-size:.58rem;letter-spacing:.2em;text-transform:uppercase;color:#d6bf88;margin:0 0 6px}
.hero-title{font-family:'Cormorant Garamond',serif;font-size:2rem;line-height:1.04;margin:0 0 8px;color:var(--text)}
.hero-state{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px}
.score-meaning{margin:0;font-size:.62rem;letter-spacing:.12em;text-transform:uppercase;color:#dfd2ae}
.pill{display:inline-flex;align-items:center;justify-content:center;min-height:25px;padding:4px 10px;border-radius:999px;font-size:.5rem;letter-spacing:.14em;text-transform:uppercase;border:1px solid var(--line-soft);background:rgba(214,181,95,.08);color:#ebddb4}
.pill-score{border-color:rgba(214,181,95,.35);background:rgba(214,181,95,.14);color:#f0e1bc}
.pill-state-stable{border-color:rgba(106,175,144,.34);background:rgba(106,175,144,.1);color:#d9eee5}
.pill-state-expanding{border-color:rgba(214,181,95,.4);background:rgba(214,181,95,.14);color:#f0e2be}
.pill-state-risk{border-color:rgba(196,120,120,.38);background:rgba(196,120,120,.1);color:#f1d7cf}
.pill-layer{border-color:rgba(179,153,83,.32);background:rgba(179,153,83,.12);color:#e8d9b0}
.hero-bottleneck{padding:12px 13px;border:1px solid rgba(214,181,95,.28);border-radius:11px;background:linear-gradient(150deg,rgba(214,181,95,.08),rgba(12,10,8,.3));margin-bottom:11px;box-shadow:0 0 0 1px rgba(214,181,95,.05) inset}
.hero-bottleneck strong{display:block;font-size:.54rem;letter-spacing:.2em;text-transform:uppercase;color:#dcc995;margin-bottom:6px}
.hero-bottleneck p{margin:0;font-size:.82rem;color:#efe4cc;line-height:1.45}
.hero-copy{margin:0 0 12px;font-size:.74rem;color:#c8bea7;line-height:1.45}
.hero-actions{display:flex;gap:8px;flex-wrap:nowrap;align-items:center}
.hero-actions .btn{min-width:164px}
.hero-translation{margin:8px 0 0;font-size:.74rem;line-height:1.45;color:#d6cbaa;border-top:1px solid rgba(214,181,95,.12);padding-top:8px}
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
.layer{padding:11px;border-radius:12px;border:1px solid var(--line-soft);background:rgba(214,181,95,.03);transition:all .18s ease;border-left:2px solid transparent}
.layer:hover{transform:translateY(-2px);box-shadow:0 2px 12px rgba(214,181,95,.08)}
.layer.is-locked-card{border-left-color:rgba(214,181,95,.4);box-shadow:0 0 16px rgba(214,181,95,.08)}
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

.section-head{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 12px;border-bottom:1px solid var(--line-soft)}
.section-head h2{margin:0;font-family:'Cormorant Garamond',serif;font-size:1.25rem;font-weight:400}
.state-rail{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}
.state-chip{padding:8px;border:1px solid var(--line-soft);border-radius:10px;background:rgba(214,181,95,.04)}
.state-chip strong{display:block;font-size:.48rem;letter-spacing:.16em;text-transform:uppercase;color:#d4bf89;margin-bottom:3px}
.state-chip span{display:block;font-size:.66rem;letter-spacing:.08em;text-transform:uppercase;color:#eee3c8}

.actions{padding:12px 12px 13px}
.action-stack{display:grid;grid-template-columns:1.18fr .82fr .82fr;gap:12px;position:relative}
.action-stack::before{content:'';position:absolute;top:24px;left:23%;right:12%;height:1px;background:linear-gradient(90deg,rgba(214,181,95,.28),rgba(214,181,95,.06));pointer-events:none}
.action{padding:14px 13px;border-radius:12px;border:1px solid var(--line-soft);background:rgba(214,181,95,.03);display:flex;flex-direction:column;gap:11px;min-height:210px;position:relative;transition:all .18s ease;border-left:2px solid transparent}
.action:hover{transform:translateY(-2px);box-shadow:0 2px 10px rgba(214,181,95,.07)}
.action.is-locked-card{border-left-color:rgba(214,181,95,.4);box-shadow:0 0 16px rgba(214,181,95,.08)}
.cta-consequence{margin:5px 0 0;font-size:.52rem;letter-spacing:.08em;color:#c8bb94;opacity:.82;line-height:1.35}

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

.btn.is-disabled{opacity:.62;pointer-events:none}
.btn.is-executing{box-shadow:0 0 16px rgba(214,181,95,.26)}
.btn.is-resolved{box-shadow:0 0 16px rgba(214,181,95,.22)}

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


@media (max-width:1080px){
  .layout{grid-template-columns:1fr}
  .sticky{position:static}
}
@media (max-width:900px){
  .grid,.layer-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
  .action-stack{grid-template-columns:1fr}
  .action-stack::before{display:none}
  .state-rail{grid-template-columns:1fr}
  .global-state-strip{grid-template-columns:1fr}
  .progression-strip{grid-template-columns:1fr}
}
@media (max-width:640px){
  .mode-bar{grid-template-columns:1fr}
  .mode-return{width:100%}
  .grid,.layer-grid{grid-template-columns:1fr}
  .hero-actions{flex-wrap:wrap}
  .hero-actions .btn{min-width:unset;width:100%}
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
.save-report-text{font-size:.72rem;color:#e0d5b8;line-height:1.35}
.save-report-text strong{color:#f0e2be}
.save-report-actions{display:flex;align-items:center;gap:8px;flex-shrink:0}
.save-report-google{display:inline-flex;align-items:center;gap:7px;min-height:34px;padding:6px 12px;border-radius:8px;border:1px solid rgba(214,181,95,.4);background:rgba(214,181,95,.1);font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;color:#f0e5c6;text-decoration:none;transition:all .16s ease}
.save-report-google:hover{border-color:rgba(214,181,95,.6);background:rgba(214,181,95,.18)}
.save-report-login{display:inline-flex;align-items:center;min-height:34px;padding:6px 10px;font-size:.6rem;letter-spacing:.1em;text-transform:uppercase;color:#c8b98a;text-decoration:none;border-bottom:1px solid rgba(214,181,95,.3);transition:color .16s}
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
            <h1 class="hero-title">System Readout Active</h1>
            <div class="hero-state">
              <span class="pill pill-score">Score {{ $score }}</span>
              <span class="pill pill-state-{{ $readoutStateKey }}">{{ $readoutState }}</span>
              <span class="pill pill-layer">Layer {{ $unlockLevel }}</span>
            </div>
            <p class="score-meaning">{{ $scoreSelectionInterpretation }}</p>
          </div>
        </div>
        <div class="hero-bottleneck">
          <strong>Primary Bottleneck</strong>
          <p>{{ $topBottleneck }}</p>
        </div>
        <p class="hero-copy">{{ $interpretation }}</p>
        <div class="hero-actions">
          @if($singleNextStep)
          <a href="{{ $singleNextStep['href'] }}" class="btn btn-primary">Progress System</a>
          @else
          <a href="#priority-actions" class="btn btn-primary">Deploy Fix</a>
          @endif
          <a href="#priority-actions" class="btn btn-secondary">Inspect Signal</a>
        </div>
        <p class="cta-consequence">{{ $singleNextStep ? 'Advances system state → unlocks next signal layer' : 'Removes constraint → improves AI selection likelihood' }}</p>
        <p class="hero-translation">{{ $humanTranslation }}</p>
      </section>

      <section class="card" id="findings">
        <div class="section-head">
          <h2>Key Findings</h2>
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
          <h2>Layer Control Grid</h2>
          <p style="margin:0;font-size:.55rem;letter-spacing:.12em;text-transform:uppercase;color:#c0b38c">Active layer state</p>
        </div>
        <div class="layer-grid" style="padding:12px">
          @foreach($layerCards as $layer)
          <article class="layer {{ $layer['status'] === 'Locked' ? 'is-locked-card' : 'is-included' }}">
            <div class="layer-head">
              <span class="layer-name">Layer {{ $layer['rank'] }} · {{ $layer['name'] }}</span>
              <span class="layer-status">@if($layer['status'] === 'Locked')<span class="lock-glyph" aria-hidden="true"></span>@endif {{ $layer['status'] }}</span>
            </div>
            <div class="layer-strip">
              <div class="layer-sig">Coverage<div class="meter"><span style="width:{{ $coveragePct }}%"></span></div></div>
              <div class="layer-sig">Authority<div class="meter"><span style="width:{{ $authorityPct }}%"></span></div></div>
              <div class="layer-sig">Structure<div class="meter"><span style="width:{{ $structurePct }}%"></span></div></div>
            </div>
            <div class="layer-main">{{ $layer['value'] }}</div>
            <div class="layer-micro">
              <span>+{{ $totalPassed }} signals <em>{{ $signalsMeaningLabel }}</em></span>
              <span>{{ $totalFailed }} blockers <em>{{ $blockersMeaningLabel }}</em></span>
              <span>{{ max(1, $sysActionsLimit) }} constraints <em>{{ $constraintMeaningLabel }}</em></span>
            </div>
            <a href="{{ $layer['cta'] }}" class="btn btn-secondary">{{ $layer['cta_label'] }}</a>
            <p class="layer-cta-note">{{ $layer['cta_note'] }}</p>
          </article>
          @endforeach
        </div>
      </section>

      <section class="card" id="priority-actions">
        <div class="section-head">
          <h2>Fix Sequence</h2>
          <div class="state-rail">
            <div class="state-chip"><strong>System State</strong><span>{{ $readoutState }}</span></div>
            <div class="state-chip"><strong>Pressure</strong><span>{{ $totalFailed > 0 ? 'Active' : 'Contained' }}</span></div>
            <div class="state-chip"><strong>Primary Constraint</strong><span>{{ count($sysActions) > 0 ? 'Detected' : 'Clear' }}</span></div>
          </div>
        </div>
        <p style="margin:8px 12px 0;font-size:.72rem;color:#d9ceb0">You&rsquo;re close. Fix these to increase AI selection likelihood.</p>
        <div class="actions">
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
                <span class="muted">AI cannot fully use this yet</span>
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
                <p style="margin:6px 0 0;font-size:.62rem;color:#e7dcc0">This improves your probability of appearing in high-intent answer results.</p>
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
                  data-exec-resolved="Fix deployed"
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
                >Deploy Fix</button>
                <p class="cta-consequence">Removes constraint → improves AI selection likelihood</p>
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
                >Inspect Signal</button>
              </div>
              <p class="action-memory">No action taken yet</p>
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
                <a href="{{ route('checkout.signal-expansion') }}" class="btn btn-primary js-unlock-signal-expansion" data-exec-init="Opening layer access..." data-exec-process="Preparing layer access..." data-exec-resolved="Layer access initiated">Unlock Signal Expansion</a>
                @endif
                <button type="button" class="btn btn-secondary" disabled><span class="lock-glyph" aria-hidden="true"></span> Preview Restricted Layer</button>
              </div>
              <p class="action-memory">No action taken yet</p>
            </article>
            @endif
          </div>
        </div>
      </section>

      <section class="card modules" id="deeper-layers">
        <div class="section-head">
          <h2>Restricted Intelligence Layers</h2>
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
                <a href="{{ $module['href'] }}" class="btn btn-primary">{{ $module['cta'] }}</a>
                <p class="cta-consequence">
                  @if($module['rank'] === 2)Reveals extraction failure tree → removes signal suppression
                  @elseif($module['rank'] === 3)Unlocks impact-ranked fix order → highest-leverage first
                  @else Exposes competitive gaps → activates expansion intelligence
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
      <p class="sticky-kicker">Next Step</p>
      <h2 class="sticky-title">{{ $singleNextStep['title'] ?? 'Deploy your lead fix' }}</h2>
      <p class="sticky-copy">{{ $singleNextStep['copy'] ?? 'Resolve the lead constraint to reduce selection pressure.' }}</p>
      <p class="sticky-unlock">Unlocks next: full signal clarity, ranked correction order, and stronger readiness progression.</p>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:4px">
        @if($singleNextStep)
        <a href="{{ $singleNextStep['href'] }}" class="btn btn-primary">Progress System</a>
        @else
        <a href="#priority-actions" class="btn btn-primary">Deploy Fix</a>
        @endif
      </div>
      <p class="cta-consequence" style="margin:0 0 8px">{{ $singleNextStep ? 'Advances to ' . $nextUnlockName . ' → unlocks ranked priorities and signal depth' : 'Removes constraint → improves AI selection likelihood' }}</p>
      <a href="#deeper-layers" class="sticky-link">Preview Restricted Layers</a>
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
        <p class="fix-detail-kicker">Constraint Intelligence Panel</p>
        <button type="button" class="fix-detail-close" id="fixDetailCloseTop">Close Panel</button>
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
        <div class="fix-detail-block"><p>State</p><p id="fixDetailFailure">Unavailable</p></div>
        <div class="fix-detail-block"><p>Effect</p><p id="fixDetailWhy">Unavailable</p></div>
        <div class="fix-detail-block"><p>Fix Path</p><p id="fixDetailCorrection">Unavailable</p></div>
        <div class="fix-detail-block"><p>Outcome If Resolved</p><p id="fixDetailUnlocks">Unavailable</p></div>
        <div class="fix-detail-block"><p>Active Consequence</p><p id="fixDetailConsequence">Unavailable</p></div>
        <div class="fix-detail-block"><p>Signal Domain</p><p id="fixDetailCategory">Unavailable</p></div>
      </div>

      <div class="fix-detail-actions">
        <a href="#priority-actions" class="btn btn-primary" id="fixDetailNext">Deploy Fix</a>
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

  function restoreActionMemory() {
    document.querySelectorAll('.action[data-action-key]').forEach(function (card) {
      var key = card.dataset.actionKey || '';
      if (!key) return;
      var raw = sessionStorage.getItem(ACTION_MEMORY_PREFIX + key);
      if (!raw) return;
      try {
        var parsed = JSON.parse(raw);
        var at = Number(parsed.at || 0);
        if (!Number.isFinite(at) || at <= 0) return;
        var elapsedMs = Date.now() - at;
        var outcome = typeof parsed.outcome === 'string' && parsed.outcome !== ''
          ? parsed.outcome
          : 'Constraint resolved -> selection pressure reduced -> signal clarity increased';
        var label = elapsedMs < 90000
          ? 'Fix deployed. Outcome pending verification.'
          : 'Fix deployed ' + Math.max(1, Math.floor(elapsedMs / 60000)) + ' min ago. Re-evaluation pending.';
        setActionMemory(card, label);
      } catch (err) {
        sessionStorage.removeItem(ACTION_MEMORY_PREFIX + key);
      }
    });
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
  }

  function closeFixDetail() {
    if (!fixDetailMask) return;
    fixDetailMask.dataset.open = 'false';
    document.body.style.overflow = '';
  }

  function runActionExecution(trigger, onComplete) {
    var card = trigger ? trigger.closest('.action') : null;
    if (!card || card.dataset.execBusy === 'true') return;

    var nodes = card.querySelectorAll('.btn');
    var initLabel = trigger.dataset.execInit || 'Deploying fix...';
    var processLabel = trigger.dataset.execProcess || 'Processing selection model...';
    var resolvedLabel = trigger.dataset.execResolved || 'Fix deployed';

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
        sessionStorage.setItem(ACTION_MEMORY_PREFIX + actionKey, JSON.stringify({ at: Date.now(), outcome: outcomeLine }));
      }
      setActionMemory(card, 'Fix deployed. Outcome pending verification.');
      trigger.textContent = resolvedLabel;
      card.classList.remove('is-executing');
      card.classList.add('is-resolved');
      trigger.classList.remove('is-executing');
      trigger.classList.add('is-resolved');

      nodes.forEach(function (node) {
        node.classList.remove('is-disabled');
        node.removeAttribute('aria-disabled');
        if (node.tagName === 'BUTTON') node.disabled = false;
      });

      window.setTimeout(function () {
        nodes.forEach(function (node) {
          if (node.dataset.originalLabel) node.textContent = node.dataset.originalLabel;
          node.classList.remove('is-resolved');
        });
        card.classList.remove('is-resolved');
        trigger.classList.remove('is-resolved');
      }, 520);

      card.dataset.execBusy = 'false';
      if (typeof onComplete === 'function') onComplete();
    }, delayMs);
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
})();
</script>
@include('partials.back-to-top')
@include('components.tm-style')
@include('partials.public-nav-js')
</body>
</html>
