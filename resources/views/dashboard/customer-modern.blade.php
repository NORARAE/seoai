@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
  $latestScan = $scanProjects->first();
  $systemCount = $scanHistory->count();
  $hasSystem = $totalScans > 0;
  $scoreValues = $scanHistory
    ->map(fn($scan) => (int) ($scan['score'] ?? 0))
    ->filter(fn($score) => $score > 0)
    ->values();
  $avgScore = $scoreValues->count() > 0 ? (int) round($scoreValues->avg()) : 0;
  $attentionCount = $scanHistory
    ->filter(fn($scan) => (int) ($scan['score'] ?? 0) > 0 && (int) ($scan['score'] ?? 0) < 70)
    ->count();
  $systemState = match (true) {
    !$hasSystem => 'Under-optimized',
    $attentionCount > 0 && $avgScore < 55 => 'At Risk',
    $attentionCount > 0 => 'Under-optimized',
    $systemCount >= 4 && $avgScore >= 80 => 'Expanding',
    default => 'Stable',
  };
  $summaryLine = $hasSystem
    ? ($systemCount . ' signals tracked. ' . $attentionCount . ' under active pressure watch.')
    : 'Scan complete — we need site access to score visibility.';
  $latestEvaluatedAt = $latestScan?->scanned_at ?? $latestScan?->created_at;
  $latestEvaluatedLabel = $latestEvaluatedAt ? $latestEvaluatedAt->diffForHumans() : 'Run your first scan to begin';
  $priorIssueHint = $attentionCount > 0
    ? 'Prior pressure persisting.'
    : 'Stable - no active pressure (temporary).';
  $progressionLine = match (true) {
    !$hasSystem => 'Progression inactive.',
    $attentionCount > 1 => 'Multi-point pressure detected.',
    $attentionCount === 1 => 'Single pressure channel detected.',
    $avgScore >= 85 => 'Selection stable - expansion required.',
    default => 'Selection stabilizing.',
  };
  $directiveLine = match (true) {
    !$hasSystem => 'Initialize baseline now.',
    $attentionCount > 0 => 'Resolve pressure next.',
    $avgScore >= 85 => 'Expand coverage next.',
    default => 'Continue stabilization.',
  };
  $objectiveTitle = $hasSystem ? ($nextStep ?? 'System active') : 'We couldn\'t fully read your site yet';
  $objectiveCta = $hasSystem ? ($nextRoute ? route($nextRoute) : route('quick-scan.show')) : route('quick-scan.show');
  $objectiveCtaLabel = $hasSystem ? ($nextRoute ? 'Unlock Next Layer' : 'Run New Scan') : 'Run First Scan';
  $secondaryCtaLabel = $hasSystem ? 'Book Consultation' : 'Start System Build';
  $reportReadyScans = $scanHistory->where('is_renderable_report', true)->values();
  $leadScan = $reportReadyScans->first() ?? $scanHistory->first() ?? [];
  if (!is_array($leadScan)) {
    $leadScan = [];
  }
  $leadDomain = $leadScan['scan_name'] ?? $leadScan['domain'] ?? 'No domain scanned yet';
  $leadScore = (int) ($leadScan['score'] ?? 0);
  $leadState = $leadScore >= 85 ? 'Stable' : ($leadScore >= 60 ? 'Under-optimized' : ($leadScore > 0 ? 'At Risk' : 'We couldn\'t fully read your site yet'));
  $leadBottleneck = trim((string) ($leadScan['fastest_fix'] ?? '')) !== ''
    ? $leadScan['fastest_fix']
    : 'No bottleneck detected yet. Run a scan to establish baseline constraints.';
  $leadRouteKey = $leadScan['scan_route_key'] ?? $leadScan['public_scan_id'] ?? $leadScan['system_scan_id'] ?? null;
  $leadRenderable = (bool) ($leadScan['is_renderable_report'] ?? false);
  $leadReportHref = ($leadRouteKey && $leadRenderable)
    ? route('dashboard.scans.show', ['scan' => $leadRouteKey])
    : route('quick-scan.show');
  $leadReadoutStatus = $leadRenderable
    ? 'Renderable'
    : match ((string) ($leadScan['status'] ?? '')) {
        'paid'    => 'Analyzing your site',
        'error'   => 'Scan could not be completed',
        'pending' => 'Scan in progress',
        default   => 'Scan in progress',
      };
  $leadLastEvaluation = $leadScannedAt?->diffForHumans() ?? $leadCreatedAt?->diffForHumans() ?? 'Scan in progress';
  $nextUnlockLabel = $nextStep ?? 'Continue improvement loop';
  $nextUnlockHref = $nextRoute ? route($nextRoute) : $leadReportHref;
  $nextMovePrimaryIssue = trim((string) ($leadScan['primary_issue'] ?? '')) !== ''
    ? $leadScan['primary_issue']
    : $leadBottleneck;
  $nextMoveFastestFix = trim((string) ($leadScan['fastest_fix'] ?? '')) !== ''
    ? $leadScan['fastest_fix']
    : 'Strengthen your primary service signal and rerun the scan.';
  $nextMoveStep = $nextStep ?? 'Unlock Signal Analysis';
  $nextMoveActionHref = $nextUnlockHref;
  $scanFocusList = $scanHistory->take(6);
  $isScansView = request()->is('dashboard/scans');
  $isReportsView = request()->is('dashboard/reports');
  $isSystemView = ! $isScansView && ! $isReportsView;
  $leadInsight = $leadScan['quick_insight'] ?? 'Your latest system readout is active and ready for action.';
  $leadScannedAt = $leadScan['scanned_at'] ?? null;
  $leadCreatedAt = $leadScan['created_at'] ?? null;
  $leadLastEvaluation = $leadScannedAt?->diffForHumans() ?? $leadCreatedAt?->diffForHumans() ?? 'Scan in progress';
  $leadTelemetryTone = $leadScore >= 85 ? 'is-strong' : ($leadScore >= 60 ? 'is-watching' : 'is-critical');
  $trendSource = $reportReadyScans->take(6)->reverse()->values();
  $trendScores = $trendSource->map(fn($scan) => max(0, min(100, (int) ($scan['score'] ?? 0))))->values();
  if ($trendScores->isEmpty()) {
    $trendScores = collect([max(0, min(100, $leadScore))]);
  }
  $sparklineScores = $trendScores->count() === 1 ? collect([$trendScores->first(), $trendScores->first()]) : $trendScores;
  $sparklineWidth = 240;
  $sparklineHeight = 72;
  $sparklinePadding = 8;
  $sparklineRangeX = $sparklineWidth - ($sparklinePadding * 2);
  $sparklineRangeY = $sparklineHeight - ($sparklinePadding * 2);
  $sparklinePoints = $sparklineScores->values()->map(function ($score, $index) use ($sparklineScores, $sparklinePadding, $sparklineRangeX, $sparklineRangeY) {
    $denominator = max($sparklineScores->count() - 1, 1);
    $x = $sparklinePadding + ($sparklineRangeX * ($index / $denominator));
    $y = $sparklinePadding + ($sparklineRangeY * (1 - ($score / 100)));

    return round($x, 1) . ',' . round($y, 1);
  })->implode(' ');
  $sparklineAreaPoints = '0,' . $sparklineHeight . ' ' . $sparklinePoints . ' ' . $sparklineWidth . ',' . $sparklineHeight;
  $scoreDelta = !is_null($leadScan['score_change'] ?? null)
    ? (int) $leadScan['score_change']
    : ($trendScores->count() > 1 ? (int) ($trendScores->last() - $trendScores->first()) : 0);
  $scoreDeltaLabel = ($scoreDelta > 0 ? '+' : '') . $scoreDelta;
  $scoreDeltaTone = $scoreDelta > 0 ? 'up' : ($scoreDelta < 0 ? 'down' : 'flat');
  $trendLabel = $scoreDelta > 0 ? 'Readiness climbing' : ($scoreDelta < 0 ? 'Readiness under pressure' : 'Readiness holding');
  $latestIssues = (int) ($leadScan['issues_count'] ?? 0);
  $oldestIssues = (int) (($trendSource->first()['issues_count'] ?? $latestIssues));
  $blockerDelta = $oldestIssues - $latestIssues;
  $blockerDeltaLabel = $blockerDelta > 0 ? ('-' . $blockerDelta . ' blockers') : ($blockerDelta < 0 ? ('+' . abs($blockerDelta) . ' blockers') : 'No blocker change');
  $latestImprovement = $scoreDelta > 0
    ? 'Latest improvement is holding above prior baseline.'
    : ($scoreDelta < 0 ? 'Latest improvement stalled. Bottleneck pressure increased.' : 'Latest improvement is neutral. Signal needs another push.');
  $readinessPercent = max(4, min(100, $leadScore > 0 ? $leadScore : 8));
  $dominantActionHref = ($leadRouteKey && (bool) ($leadScan['is_renderable_report'] ?? false))
    ? ($leadReportHref . '#layer-signal')
    : $nextUnlockHref;
  $dominantActionLabel = ($leadRouteKey && (bool) ($leadScan['is_renderable_report'] ?? false))
    ? 'Deploy Priority Fix'
    : $nextUnlockLabel;
  $featuredRecentScans = $scanFocusList->take($isScansView ? 3 : 2)->values();
  $archivedScans = $scanFocusList->slice($featuredRecentScans->count())->values();
  $reportDeskScans = $scanFocusList->take(4)->values();
  $layers = collect($analysisLayers ?? []);
  $currentLayer = $layers->filter(fn($layer) => (bool) ($layer['complete'] ?? false))->last();
  $nextLayer = $layers->first(fn($layer) => ! (bool) ($layer['complete'] ?? false));
  $currentLevelLabel = $currentLayer['label'] ?? 'No level unlocked yet';
  $nextLevelLabel = $nextLayer['label'] ?? 'Guided Execution Complete';
  $nextLevelPrice = $nextLayer['price'] ?? null;
  $nextLevelHref = $nextRoute ? route($nextRoute) : route('quick-scan.show');
  $levelUnlockMap = [
    'scan-basic'          => ['Your baseline score and top issue.', 'What to fix first to start improving.'],
    'signal-expansion'    => ['See where competitors are stronger.', 'Find your biggest visibility gaps.'],
    'structural-leverage' => ['Your impact-ranked fix list from scan data.', 'Execute what moves your score the most.'],
    'system-activation'   => ['Execution checklist inside your dashboard.', 'Track progress as you complete guided steps.'],
  ];
  $nextLevelUnlocks = $nextLayer
    ? ($levelUnlockMap[$nextLayer['key']] ?? ['Deeper visibility controls.', 'More actionable correction guidance.'])
    : ['All core levels are unlocked.', 'Use Scans to review history and compare outcomes.'];
  $showMarketCoverage = ((string) ($currentLayer['key'] ?? 'scan-basic')) !== 'scan-basic';
  // Project identity vars
  $projectDomain = ($leadDomain && $leadDomain !== 'No domain scanned yet')
    ? preg_replace('#^https?://(www\.)?#i', '', rtrim($leadDomain, '/'))
    : null;
  // State chip color
  $stateChipClass = match(true) {
    $leadState === 'At Risk'           => 'state-chip state-chip-red',
    $leadState === 'Under-optimized'   => 'state-chip state-chip-amber',
    $leadState === 'Stable'            => 'state-chip state-chip-green',
    default                            => 'state-chip state-chip-gold',
  };
  // Live feedback insight — plain-English for business users
  $noScore = ($leadScore === 0);
  $liveFeedbackInsight = match(true) {
    $noScore                         => '<strong>Site not fully readable yet</strong> &rarr; fix access to get your score.',
    $leadState === 'At Risk'         => '<strong>Competitive risk detected</strong> &rarr; stronger sites are outranking yours right now.',
    $leadState === 'Under-optimized' => '<strong>Visibility gaps found</strong> &rarr; your site has gaps that limit how often AI tools recommend it.',
    $scoreDelta < 0                  => '<strong>Score dropped</strong> &rarr; your top issue needs attention.',
    $scoreDelta > 0                  => '<strong>Score improving</strong> &rarr; keep working on your top issues.',
    default                          => '<strong>Baseline scan complete</strong> &rarr; your visibility reading is ready below.',
  };
  $primaryConstraintChip = $attentionCount > 0
    ? ($attentionCount . ' area' . ($attentionCount > 1 ? 's' : '') . ' need attention')
    : 'No issues found';
  $pressureChipClass = $attentionCount > 0 ? 'state-chip state-chip-amber' : 'state-chip state-chip-green';
  // Score confidence label for baseline readout
  $scoreConfidence = match(true) {
    $leadScore >= 70 => 'High',
    $leadScore >= 45 => 'Medium',
    $leadScore > 0   => 'Low',
    default          => null,
  };
  $pagesAnalyzed = (int) ($leadScan['pages_scanned'] ?? 0);
  $profileData = auth()->user()->profile_data ?? [];
  $profileBrand = $profileData['business_name'] ?? $profileData['public_brand_name'] ?? null;
  $heroHeadline = $projectDomain ? 'AI Visibility Baseline for ' . $projectDomain : 'Your AI Visibility Baseline';
  $scanCompletedLabel = null;
  if (!empty($leadScan['scanned_at'])) {
    try { $scanCompletedLabel = \Carbon\Carbon::parse($leadScan['scanned_at'])->format('M j, Y'); } catch (\Throwable $e) {}
  } elseif (!empty($leadScan['created_at'])) {
    try { $scanCompletedLabel = \Carbon\Carbon::parse($leadScan['created_at'])->format('M j, Y'); } catch (\Throwable $e) {}
  }
  $profileCompletionScore = count(array_filter([
    !empty($profileData['business_name'] ?? ''),
    !empty($profileData['primary_service'] ?? ''),
    !empty($profileData['primary_location'] ?? ''),
    !empty($profileData['cms_platform'] ?? ''),
    !empty($profileData['primary_goal'] ?? ''),
  ]));
@endphp

@push('styles')
<style>
  .system-grid-shell{border:1px solid rgba(200,168,75,.2);border-radius:16px;background:linear-gradient(155deg,#141108,#0b0906 70%);padding:18px}
  .system-section{position:relative}
  .system-section-primary{margin-bottom:14px}
  .system-section-secondary{margin-bottom:14px}
  .system-section-tertiary{margin-top:2px;opacity:.92}
  .system-subshell{border:1px solid rgba(200,168,75,.18);border-radius:14px;background:linear-gradient(155deg,#151109,#0d0a06 70%);padding:16px;box-shadow:0 8px 24px rgba(0,0,0,.3),inset 0 1px 0 rgba(255,255,255,.02)}
  .system-subshell.dim{opacity:.88;border-color:rgba(200,168,75,.14)}
  .section-head{display:flex;align-items:end;justify-content:space-between;gap:12px;margin-bottom:12px}
  .section-head h2{font-size:.82rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.78)}
  .section-head p{font-size:.72rem;color:#a79f8d;line-height:1.4}
  .system-unified-module{border:1px solid rgba(200,168,75,.24);border-radius:16px;background:linear-gradient(155deg,#1d190f,#100d08 68%);padding:18px;box-shadow:0 10px 34px rgba(0,0,0,.38),inset 0 1px 0 rgba(255,255,255,.03);position:relative;overflow:hidden}
  .system-unified-module::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.42),transparent)}
  .system-unified-module::after{content:'';position:absolute;inset:-10% -20% auto -20%;height:130%;background:radial-gradient(ellipse at 50% 0,rgba(200,168,75,.08),transparent 60%);pointer-events:none;animation:presenceDrift 12s ease-in-out infinite}
  .control-hero{padding:24px;background:
    linear-gradient(140deg,rgba(35,28,15,.96),rgba(13,10,7,.98) 62%),
    radial-gradient(circle at 12% 20%,rgba(200,168,75,.16),transparent 28%),
    radial-gradient(circle at 82% 18%,rgba(106,175,144,.12),transparent 26%)}
  .control-hero::before{height:2px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.72),transparent)}
  .control-hero::after{inset:0;background:
    radial-gradient(circle at 18% 22%,rgba(200,168,75,.12),transparent 22%),
    linear-gradient(transparent 0,rgba(200,168,75,.05) 48%,transparent 100%);
    opacity:.75;mix-blend-mode:screen;animation:gridDrift 16s linear infinite}
  .dashboard-primary-flow{display:flex;flex-direction:column;gap:0}
  .dashboard-primary-flow.is-scans-view #scan-history{order:1}
  .dashboard-primary-flow.is-scans-view #system-state{order:2}
  .dashboard-primary-flow.is-reports-view #report-readouts{order:1}
  .dashboard-primary-flow.is-reports-view #system-state{order:2}
  .dashboard-primary-flow.is-reports-view #scan-history{order:3}
  .view-clarity-note{margin-top:2px;font-size:.76rem;color:#bdb39c;line-height:1.55}
  .report-readout-shell{border:1px solid rgba(200,168,75,.2);border-radius:18px;background:linear-gradient(160deg,#15110a,#0c0a06 72%);padding:16px;box-shadow:0 14px 30px rgba(0,0,0,.3),inset 0 1px 0 rgba(255,255,255,.03)}
  .report-readout-head{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:12px}
  .report-readout-head p:first-child{font-size:.62rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.74)}
  .report-readout-head h2{margin-top:5px;font-size:1.45rem;line-height:1.1;font-weight:650;color:#f3ebd6}
  .report-readout-head p:last-child{margin-top:6px;max-width:42rem;font-size:.84rem;line-height:1.6;color:#c5bba3}
  .report-readout-grid{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(0,.85fr);gap:12px}
  .report-lead-card,.report-list-card{border:1px solid rgba(200,168,75,.16);border-radius:14px;background:rgba(0,0,0,.2);padding:12px}
  .report-lead-meta{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px}
  .report-lead-meta p:first-child{font-size:.58rem;letter-spacing:.16em;text-transform:uppercase;color:#b5a886}
  .report-lead-meta p:last-child{font-size:.68rem;letter-spacing:.12em;text-transform:uppercase;color:#a9a08b}
  .report-lead-title{margin-top:7px;font-size:1.1rem;font-weight:650;line-height:1.2;color:#eee4cd}
  .report-lead-summary{margin-top:6px;font-size:.84rem;line-height:1.55;color:#d8ceb7}
  .report-lead-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
  .report-lead-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:8px 11px;border-radius:10px;font-size:10px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;text-decoration:none}
  .report-lead-actions a.primary{border:1px solid rgba(200,168,75,.42);background:rgba(200,168,75,.16);color:#f3e8cb}
  .report-lead-actions a.secondary{border:1px solid rgba(200,168,75,.24);background:rgba(200,168,75,.08);color:#ddd3bc}
  .report-list-card h3{font-size:.72rem;letter-spacing:.18em;text-transform:uppercase;color:#cbb98b}
  .report-list{display:grid;gap:8px;margin-top:10px}
  .report-list-item{display:flex;align-items:center;justify-content:space-between;gap:10px;border:1px solid rgba(200,168,75,.14);border-radius:10px;background:rgba(0,0,0,.18);padding:9px 10px}
  .report-list-item p{font-size:.78rem;color:#e9dfc8;line-height:1.35}
  .report-list-item span{font-size:.62rem;letter-spacing:.12em;text-transform:uppercase;color:#a8a08b}
  .report-list-item a{font-size:.62rem;letter-spacing:.14em;text-transform:uppercase;color:#ecd8a8;text-decoration:none;border-bottom:1px solid rgba(200,168,75,.3)}
  .report-list-item a:hover{border-color:rgba(200,168,75,.62)}
  .onboarding-command-shell{border:1px solid rgba(200,168,75,.26);border-radius:22px;background:linear-gradient(160deg,#17120a,#0b0906 72%);padding:24px;box-shadow:0 18px 40px rgba(0,0,0,.34),inset 0 1px 0 rgba(255,255,255,.03);position:relative;overflow:hidden}
  .onboarding-command-shell::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 18% 12%,rgba(200,168,75,.12),transparent 28%);pointer-events:none}
  .onboarding-command-kicker{font-size:.64rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.72)}
  .onboarding-command-title{margin-top:10px;font-size:clamp(2rem,4vw,3rem);line-height:.95;font-weight:700;letter-spacing:-.04em;color:#f4ecd7;max-width:12ch}
  .onboarding-command-copy{margin-top:12px;max-width:38rem;font-size:1rem;line-height:1.65;color:#d8cdb6}
  .onboarding-command-cta{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:0 20px;border-radius:14px;margin-top:16px;background:#c6a85a;color:#1a1a1a;text-decoration:none;font-size:.76rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;box-shadow:0 18px 28px rgba(198,168,90,.22),0 0 0 1px rgba(255,255,255,.18) inset;transition:all .2s ease}
  .onboarding-command-cta:hover{transform:translateY(-2px);box-shadow:0 24px 36px rgba(198,168,90,.38),0 0 0 1px rgba(255,255,255,.22) inset}
  .onboarding-command-reassure{margin-top:10px;font-size:.86rem;line-height:1.5;color:#d9cfba}
  .onboarding-command-footnote{margin-top:12px;font-size:.72rem;letter-spacing:.08em;text-transform:uppercase;color:#b5a988}
  .onboarding-explainer{margin-top:8px}
  .onboarding-explainer summary{display:inline-flex;align-items:center;gap:6px;cursor:pointer;font-size:.74rem;letter-spacing:.08em;text-transform:uppercase;color:#dcc996;list-style:none}
  .onboarding-explainer summary::-webkit-details-marker{display:none}
  .onboarding-explainer-panel{margin-top:8px;max-width:34rem;border:1px solid rgba(200,168,75,.14);border-radius:12px;background:rgba(0,0,0,.2);padding:10px 11px}
  .onboarding-explainer-panel ul{display:grid;gap:5px;padding-left:16px;color:#d5cab2;font-size:.82rem;line-height:1.45}
  .onboarding-proof-strip{margin-top:14px;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;max-width:42rem}
  .onboarding-proof-item{border:1px solid rgba(200,168,75,.14);border-radius:11px;background:rgba(255,255,255,.03);padding:9px 10px}
  .onboarding-proof-item p:first-child{font-size:.56rem;letter-spacing:.14em;text-transform:uppercase;color:#b9ab89}
  .onboarding-proof-item p:last-child{margin-top:4px;font-size:.8rem;line-height:1.4;color:#ece1c8}
  .ia-progress-shell{border:1px solid rgba(200,168,75,.2);border-radius:18px;background:linear-gradient(158deg,#151109,#0b0906 72%);padding:18px;box-shadow:0 14px 30px rgba(0,0,0,.3),inset 0 1px 0 rgba(255,255,255,.03)}
  .ia-progress-head{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:10px;margin-bottom:12px}
  .ia-progress-head h2{font-size:.88rem;letter-spacing:.22em;text-transform:uppercase;color:#d2c08d}
  .ia-progress-head p{font-size:.78rem;line-height:1.5;color:#b8ad95;max-width:42rem}
  .ia-level-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
  .ia-level-card{border:1px solid rgba(200,168,75,.16);border-radius:12px;background:rgba(0,0,0,.2);padding:11px}
  .ia-level-card p:first-child{font-size:.58rem;letter-spacing:.16em;text-transform:uppercase;color:#b9ac8a}
  .ia-level-card p:last-child{margin-top:5px;font-size:.9rem;line-height:1.45;color:#ece1c8}
  .ia-level-unlocks{margin-top:10px;border:1px solid rgba(200,168,75,.14);border-radius:12px;background:rgba(0,0,0,.18);padding:10px 11px}
  .ia-level-unlocks p{font-size:.58rem;letter-spacing:.16em;text-transform:uppercase;color:#cbb98b}
  .ia-level-unlocks ul{margin-top:7px;display:grid;gap:6px;padding-left:16px;color:#ddd1b8;font-size:.84rem;line-height:1.45}
  .ia-progress-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
  .ia-progress-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:36px;padding:8px 12px;border-radius:10px;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;text-decoration:none}
  .ia-progress-actions a.primary{border:1px solid rgba(200,168,75,.42);background:rgba(200,168,75,.16);color:#f3e8cb}
  .ia-progress-actions a.secondary,.ia-progress-actions .secondary{display:inline-flex;align-items:center;justify-content:center;min-height:36px;padding:8px 12px;border-radius:10px;border:1px solid rgba(200,168,75,.24);background:rgba(200,168,75,.08);color:#ddd3bc}
  .hero-grid{position:relative;display:grid;grid-template-columns:minmax(0,1.5fr) minmax(280px,.92fr);gap:18px;z-index:1}
  .hero-main{display:flex;flex-direction:column;gap:14px}
  .hero-status-strip{display:flex;flex-wrap:wrap;gap:8px}
  .hero-status-item{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;border:1px solid rgba(200,168,75,.18);background:rgba(255,255,255,.03);font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#c4bca7}
  .hero-status-item::before{content:'';width:6px;height:6px;border-radius:999px;background:rgba(200,168,75,.72)}
  .hero-overline{font-size:.68rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(200,168,75,.68)}
  .hero-domain{font-size:clamp(2.2rem,4.8vw,4.35rem);line-height:.96;font-weight:650;letter-spacing:-.04em;color:#f3ecd8;text-wrap:balance;max-width:10ch;text-shadow:0 8px 30px rgba(0,0,0,.35)}
  .hero-intro{max-width:48rem;font-size:1rem;line-height:1.6;color:#eae4d8}
  .hero-priority-grid{margin-top:2px;gap:12px}
  .hub-priority-card{position:relative;border-color:rgba(200,168,75,.2);border-radius:14px;padding:12px 13px;background:linear-gradient(165deg,rgba(255,255,255,.05),rgba(0,0,0,.18));backdrop-filter:blur(10px);overflow:hidden;box-shadow:inset 0 1px 0 rgba(255,255,255,.03)}
  .hub-priority-card::after{content:'';position:absolute;inset:0;background:linear-gradient(115deg,transparent 20%,rgba(200,168,75,.08) 48%,transparent 70%);transform:translateX(-135%);animation:heroSweep 10s ease-in-out infinite}
  .hero-side{display:grid;gap:12px;align-self:stretch}
  .hero-score-panel,.hero-side-card{position:relative;border:1px solid rgba(200,168,75,.18);border-radius:16px;background:linear-gradient(160deg,rgba(255,255,255,.05),rgba(0,0,0,.18));padding:16px;overflow:hidden;box-shadow:0 12px 28px rgba(0,0,0,.24),inset 0 1px 0 rgba(255,255,255,.03)}
  .hero-score-panel::before,.hero-side-card::before{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,255,255,.02),transparent 36%);pointer-events:none}
  .hero-panel-label{font-size:.62rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.7)}
  .hero-score-wrap{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:12px}
  .hero-score-orb{position:relative;display:flex;flex-direction:column;align-items:center;justify-content:center;width:126px;height:126px;border-radius:999px;border:1px solid rgba(200,168,75,.3);background:radial-gradient(circle at 50% 35%,rgba(200,168,75,.18),rgba(17,13,8,.95) 72%);box-shadow:0 18px 40px rgba(0,0,0,.42),inset 0 1px 0 rgba(255,255,255,.05)}
  .hero-score-orb::before{content:'';position:absolute;inset:10px;border-radius:999px;border:1px solid rgba(200,168,75,.15)}
  .hero-score-orb::after{content:'';position:absolute;inset:-12px;border-radius:999px;background:radial-gradient(circle,rgba(200,168,75,.18),transparent 62%);z-index:-1;animation:scorePulse 3.2s ease-in-out infinite}
  .hero-score-orb.is-critical{border-color:rgba(196,120,120,.38);background:radial-gradient(circle at 50% 35%,rgba(196,120,120,.16),rgba(17,13,8,.95) 72%)}
  .hero-score-orb.is-critical::after{background:radial-gradient(circle,rgba(196,120,120,.18),transparent 62%)}
  .hero-score-orb.is-watching{border-color:rgba(214,177,95,.38)}
  .hero-score-orb.is-strong{border-color:rgba(106,175,144,.34);background:radial-gradient(circle at 50% 35%,rgba(106,175,144,.16),rgba(17,13,8,.95) 72%)}
  .hero-score-orb.is-strong::after{background:radial-gradient(circle,rgba(106,175,144,.18),transparent 62%)}
  .hero-score-value{font-size:2.6rem;line-height:1;font-weight:700;color:#f6eed8;letter-spacing:-.04em}
  .hero-score-caption{margin-top:4px;font-size:.56rem;letter-spacing:.18em;text-transform:uppercase;color:#b8ad92}
  .hero-score-meta{display:grid;gap:8px;flex:1}
  .hero-telemetry{border:1px solid rgba(200,168,75,.14);border-radius:12px;background:rgba(0,0,0,.2);padding:10px 11px}
  .hero-telemetry p:first-child{font-size:.58rem;letter-spacing:.18em;text-transform:uppercase;color:#ae9f81}
  .hero-telemetry p:last-child{margin-top:4px;font-size:.88rem;color:#ede4cf;line-height:1.4}
  .hero-telemetry .telemetry-emphasis{color:#f1dfae}
  .hero-side-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px;margin-top:12px}
  .hero-side-metric{border:1px solid rgba(200,168,75,.14);border-radius:11px;background:rgba(0,0,0,.18);padding:10px}
  .hero-side-metric p:first-child{font-size:.56rem;letter-spacing:.16em;text-transform:uppercase;color:#aa9e85}
  .hero-side-metric p:last-child{margin-top:4px;font-size:.84rem;color:#ebe0c7;line-height:1.4}
  .hero-telemetry-deck{display:grid;grid-template-columns:minmax(0,1.18fr) minmax(210px,.82fr);gap:12px;margin-top:14px}
  .telemetry-trend-card,.telemetry-mini-grid{border:1px solid rgba(200,168,75,.18);border-radius:16px;background:linear-gradient(160deg,rgba(255,255,255,.04),rgba(0,0,0,.18));padding:14px;box-shadow:inset 0 1px 0 rgba(255,255,255,.03)}
  .telemetry-card-label{font-size:.6rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.68)}
  .telemetry-trend-row{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-top:10px}
  .telemetry-delta{display:grid;gap:5px;min-width:88px}
  .telemetry-delta .value{font-size:1.7rem;font-weight:700;line-height:1;color:#f3ead2;letter-spacing:-.04em}
  .telemetry-delta .value.up{color:#b9e0ce}
  .telemetry-delta .value.down{color:#e2b0b0}
  .telemetry-delta .value.flat{color:#e2d4a8}
  .telemetry-delta .label{font-size:.58rem;letter-spacing:.16em;text-transform:uppercase;color:#ab9e84}
  .telemetry-delta .sub{font-size:.75rem;line-height:1.45;color:#d8ceba}
  .telemetry-chart{flex:1;min-height:90px;border:1px solid rgba(200,168,75,.14);border-radius:14px;background:linear-gradient(180deg,rgba(0,0,0,.18),rgba(0,0,0,.08));padding:10px;position:relative;overflow:hidden}
  .telemetry-chart::before{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,255,255,.02),transparent 30%),repeating-linear-gradient(180deg,transparent,transparent 18px,rgba(200,168,75,.05) 18px,rgba(200,168,75,.05) 19px);pointer-events:none}
  .telemetry-chart svg{position:relative;z-index:1;width:100%;height:74px;overflow:visible}
  .telemetry-chart .grid-labels{position:relative;z-index:1;display:flex;justify-content:space-between;margin-top:4px;font-size:.52rem;letter-spacing:.16em;text-transform:uppercase;color:#988d74}
  .telemetry-mini-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}
  .telemetry-mini-grid article{border:1px solid rgba(200,168,75,.14);border-radius:12px;background:rgba(0,0,0,.16);padding:10px}
  .telemetry-mini-grid article p:first-child{font-size:.56rem;letter-spacing:.16em;text-transform:uppercase;color:#ac9f82}
  .telemetry-mini-grid article p:last-child{margin-top:4px;font-size:.84rem;line-height:1.4;color:#e7ddc4}
  .surface-reveal{opacity:0;transform:translateY(18px) scale(.985);transition:opacity .7s ease,transform .7s ease}
  .surface-reveal.is-visible{opacity:1;transform:translateY(0) scale(1)}
  .state-pulse{display:inline-flex;align-items:center;gap:7px;padding:5px 10px;border-radius:999px;border:1px solid rgba(106,175,144,.34);font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#9fd2ba;background:rgba(106,175,144,.12)}
  .state-pulse::before{content:'';width:7px;height:7px;border-radius:999px;background:#6aaf90;box-shadow:0 0 0 0 rgba(106,175,144,.45);animation:statePulse 2.6s ease-in-out infinite}
  .state-pulse.risk{border-color:rgba(196,120,120,.34);background:rgba(196,120,120,.12);color:#db9c9c}
  .state-pulse.risk::before{background:#c47878;box-shadow:0 0 0 0 rgba(196,120,120,.45)}
  .state-pulse.expand{border-color:rgba(200,168,75,.4);background:rgba(200,168,75,.14);color:#ead5a0}
  .state-pulse.expand::before{background:#c8a84b;box-shadow:0 0 0 0 rgba(200,168,75,.45)}
  .state-pulse.optimize{border-color:rgba(214,177,95,.35);background:rgba(214,177,95,.11);color:#dfc788}
  .state-pulse.optimize::before{background:#d6b15f;box-shadow:0 0 0 0 rgba(214,177,95,.45)}
  .state-metric-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:12px}
  .state-metric{border:1px solid rgba(200,168,75,.16);background:rgba(200,168,75,.03);padding:10px;border-radius:10px}
  .state-metric .label{font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#a69f8c}
  .state-metric .value{margin-top:2px;font-size:20px;font-weight:700;color:#eee6d2;line-height:1}
  .state-summary{margin-top:8px;font-size:13px;color:#d3ccb9;line-height:1.45}
  .hub-priority-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:14px}
  .hub-priority-card{border:1px solid rgba(200,168,75,.18);border-radius:10px;padding:10px;background:rgba(0,0,0,.22)}
  .hub-priority-card p:first-child{font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#b6ab90}
  .hub-priority-card p:last-child{margin-top:4px;font-size:13px;color:#ece2cb;line-height:1.35}
  .hub-priority-card .hub-link{color:#f0ddb0;text-decoration:none;border-bottom:1px solid rgba(200,168,75,.32)}
  .hub-priority-card .hub-link:hover{border-color:rgba(200,168,75,.62)}
  .scan-history-shell{position:relative;border:1px solid rgba(200,168,75,.2);border-radius:18px;background:linear-gradient(155deg,#151109,#0b0906 72%);padding:18px;overflow:hidden;box-shadow:0 18px 36px rgba(0,0,0,.26),inset 0 1px 0 rgba(255,255,255,.03)}
  .scan-history-shell::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 85% 0,rgba(200,168,75,.1),transparent 30%);pointer-events:none}
  .scan-library-toolbar{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
  .scan-library-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;border:1px solid rgba(200,168,75,.18);background:rgba(255,255,255,.03);font-size:.58rem;letter-spacing:.16em;text-transform:uppercase;color:#c9bea3}
  .scan-library-pill strong{font-size:.72rem;letter-spacing:normal;color:#f0e4c7}
  .scan-history-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;margin-top:12px}
  .scan-history-card{position:relative;border:1px solid rgba(200,168,75,.18);border-radius:16px;background:linear-gradient(160deg,#19130c,#0f0b08 72%);padding:14px;display:flex;flex-direction:column;gap:10px;overflow:hidden;box-shadow:0 12px 28px rgba(0,0,0,.28),inset 0 1px 0 rgba(255,255,255,.03);transition:transform .26s ease,box-shadow .28s ease,border-color .25s ease}
  .scan-history-card::before{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,255,255,.03),transparent 34%);pointer-events:none}
  .scan-history-card::after{content:'';position:absolute;left:-30%;right:-30%;top:-55%;height:120%;background:linear-gradient(120deg,transparent 32%,rgba(200,168,75,.1) 50%,transparent 68%);transform:translateX(-120%);transition:transform 1s ease;pointer-events:none}
  .scan-history-card:hover{transform:translateY(-4px);border-color:rgba(200,168,75,.34);box-shadow:0 18px 34px rgba(0,0,0,.38),0 0 0 1px rgba(200,168,75,.12) inset}
  .scan-history-card:hover::after{transform:translateX(120%)}
  .scan-history-card .meta{font-size:10px;letter-spacing:.13em;text-transform:uppercase;color:#ab9f84}
  .scan-history-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px}
  .scan-history-card .domain{font-size:1.05rem;font-weight:650;color:#efe6d1;line-height:1.18;max-width:14ch}
  .scan-history-subline{margin-top:5px;font-size:.75rem;color:#bfb39a;line-height:1.45}
  .scan-history-card .bottleneck{font-size:12px;color:#d7ccb4;line-height:1.45}
  .scan-history-card .state-row{display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap}
  .scan-history-card .pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:4px 8px;font-size:10px;letter-spacing:.12em;text-transform:uppercase;border:1px solid rgba(200,168,75,.28);color:#e6d4a5;background:rgba(200,168,75,.12)}
  .scan-history-score{display:flex;flex-direction:column;align-items:flex-end;gap:3px;padding:7px 9px;border-radius:12px;border:1px solid rgba(200,168,75,.22);background:rgba(0,0,0,.22)}
  .scan-history-score .score{font-size:1.22rem;font-weight:700;color:#f6ecd0;line-height:1;letter-spacing:-.03em}
  .scan-history-score .label{font-size:.52rem;letter-spacing:.16em;text-transform:uppercase;color:#b5a788}
  .scan-history-context{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
  .scan-history-context article{border:1px solid rgba(200,168,75,.14);border-radius:11px;background:rgba(0,0,0,.16);padding:9px 10px}
  .scan-history-context article p:first-child{font-size:.56rem;letter-spacing:.16em;text-transform:uppercase;color:#ac9f82}
  .scan-history-context article p:last-child{margin-top:4px;font-size:.78rem;line-height:1.45;color:#e7dcc3}
  .scan-history-fastfix{border:1px solid rgba(200,168,75,.16);border-radius:12px;background:rgba(0,0,0,.2);padding:10px 11px}
  .scan-history-fastfix p:first-child{font-size:.58rem;letter-spacing:.16em;text-transform:uppercase;color:#d8c58f}
  .scan-history-fastfix p:last-child{margin-top:5px;font-size:.83rem;line-height:1.5;color:#ece2ca}
  .scan-history-card .score{font-size:12px;font-weight:700;color:#f1e7cd}
  .scan-history-card .actions{display:flex;gap:8px;flex-wrap:wrap;padding-top:2px}
  .scan-history-card .actions a{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:8px 11px;border-radius:10px;font-size:10px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;text-decoration:none;transition:transform .18s ease,border-color .2s ease,background .2s ease,box-shadow .2s ease}
  .scan-history-card .actions a:hover{transform:translateY(-1px)}
  .scan-history-card .actions .disabled{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:8px 11px;border-radius:10px;font-size:10px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;border:1px solid rgba(160,153,139,.24);background:rgba(130,124,110,.14);color:#bcb4a1;cursor:not-allowed}
  /* In-progress scan card accent */
  @keyframes scan-pulse-ring{0%{opacity:.55;transform:scale(1)}70%{opacity:0;transform:scale(1.7)}100%{opacity:0;transform:scale(1.7)}}
  @keyframes scan-dot-beat{0%,80%,100%{opacity:.4;transform:scale(.9)}40%{opacity:1;transform:scale(1.1)}}
  @keyframes scan-shimmer{0%{background-position:-200px 0}100%{background-position:200px 0}}
  .status-dot{display:inline-block;width:8px;height:8px;border-radius:50%;vertical-align:middle;flex-shrink:0;position:relative}
  .status-dot.is-active{background:#c8a84b;animation:scan-dot-beat 1.4s ease-in-out infinite}
  .status-dot.is-active::after{content:'';position:absolute;inset:-3px;border-radius:50%;border:1px solid rgba(200,168,75,.55);animation:scan-pulse-ring 1.6s ease-out infinite}
  .status-dot.is-error{background:#d64c4c}
  .status-dot.is-done{background:#5a9e6f}
  .scan-history-card.is-inprogress{border-color:rgba(200,168,75,.32);background:linear-gradient(160deg,#1a1409,#0f0b08 72%)}
  .scan-history-card.is-inprogress .scan-history-subline{color:#d0c29a}
  .scan-history-card.is-error{border-color:rgba(199,72,72,.22);background:linear-gradient(160deg,#180d0d,#0f0b08 72%)}
  .inprogress-link{display:inline-flex;align-items:center;gap:7px;min-height:34px;padding:8px 11px;border-radius:10px;font-size:10px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;border:1px solid rgba(200,168,75,.32);background:rgba(200,168,75,.1);color:#d9c579;text-decoration:none;transition:background .2s,border-color .2s}
  .inprogress-link:hover{background:rgba(200,168,75,.18);border-color:rgba(200,168,75,.5);color:#ffe28a}
  .scan-card-shimmer{height:3px;border-radius:2px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.45) 50%,transparent);background-size:200px 100%;animation:scan-shimmer 1.8s linear infinite;margin-top:4px}
  .scan-history-card .open{border:1px solid rgba(200,168,75,.42);background:rgba(200,168,75,.16);color:#f3e8cb;box-shadow:0 0 0 1px rgba(200,168,75,.1) inset}
  .scan-history-card .deploy{border:1px solid rgba(106,175,144,.44);background:rgba(106,175,144,.16);color:#bfe3d2}
  .scan-history-card .inspect{border:1px solid rgba(200,168,75,.24);background:rgba(200,168,75,.08);color:#ddd3bc}
  .operations-quiet{opacity:.78}
  .surface-focus-kicker{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;border:1px solid rgba(200,168,75,.32);background:rgba(200,168,75,.12);font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#e3d0a0}
  .surface-focus-kicker::before{content:'';width:7px;height:7px;border-radius:999px;background:#c8a84b}
  .system-grid-toolbar{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:14px}
  .system-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px}
  .system-grid.grid-compact{grid-template-columns:repeat(auto-fill,minmax(220px,1fr))}
  .system-grid.grid-wide{grid-template-columns:repeat(auto-fill,minmax(280px,1fr))}
  .system-grid-card{position:relative;display:flex;flex-direction:column;border:1px solid rgba(200,168,75,.18);background:linear-gradient(160deg,#1a140d,#0f0b08 72%);border-radius:16px;padding:13px;min-height:202px;text-decoration:none;color:inherit;overflow:hidden;transition:transform .24s ease,box-shadow .26s ease,border-color .24s ease,background .24s ease,opacity .2s ease}
  .system-grid-card.clickable{cursor:pointer}
  .system-grid-card::before{content:'';position:absolute;inset:0;opacity:.45;pointer-events:none;transition:opacity .25s ease;background:radial-gradient(circle at 80% 0,rgba(255,255,255,.06),transparent 45%),linear-gradient(180deg,rgba(255,255,255,.03),transparent 32%)}
  .system-grid-card::after{content:'';position:absolute;inset:-1px;background:linear-gradient(110deg,transparent 30%,rgba(200,168,75,.12) 50%,transparent 70%);transform:translateX(-130%);transition:transform .7s ease;pointer-events:none}
  .system-grid-card:hover{transform:translateY(-5px);border-color:rgba(200,168,75,.48);background:linear-gradient(160deg,#1f1910,#120d08 72%);box-shadow:0 0 0 1px rgba(200,168,75,.22) inset,0 18px 30px rgba(0,0,0,.46),0 0 18px rgba(200,168,75,.12)}
  .system-grid-card:focus-visible{outline:2px solid rgba(200,168,75,.58);outline-offset:2px}
  .system-grid-card.is-executing{border-color:rgba(214,181,84,.62);box-shadow:0 0 0 1px rgba(214,181,84,.28) inset,0 0 24px rgba(214,181,84,.2)}
  .system-grid-card.is-engaged{border-color:rgba(106,175,144,.46);box-shadow:0 0 0 1px rgba(106,175,144,.22) inset,0 0 20px rgba(106,175,144,.14)}
  .system-grid-card:hover::before{opacity:.75}
  .system-grid-card:hover::after{transform:translateX(130%)}
  .system-grid-card.supporting{opacity:.86;border-color:rgba(200,168,75,.16)}
  .system-grid-card.featured{border-color:rgba(200,168,75,.62);padding:13px;box-shadow:0 0 0 1px rgba(200,168,75,.3) inset,0 18px 36px rgba(0,0,0,.5),0 0 24px rgba(200,168,75,.2)}
  .system-grid-card.featured .system-grid-score::before{animation:scorePulse 2.8s ease-in-out infinite}
  .system-grid-card.featured .featured-tag{display:inline-flex}
  .system-grid-card.featured .priority-tag{display:inline-flex}
  .featured-tag{display:none;align-items:center;gap:6px;padding:4px 8px;border-radius:999px;border:1px solid rgba(200,168,75,.34);background:rgba(200,168,75,.14);font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#ecd8a2}
  .priority-tag{display:none;align-items:center;gap:6px;padding:4px 8px;border-radius:999px;border:1px solid rgba(196,120,120,.34);background:rgba(196,120,120,.12);font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#e2afaf}
  .status-strong{border-color:rgba(106,175,144,.38);background:linear-gradient(152deg,#172017,#0e140e 68%)}
  .status-partial{border-color:rgba(200,168,75,.45);background:linear-gradient(152deg,#211b10,#120d08 68%)}
  .status-critical{border-color:rgba(196,120,120,.42);background:linear-gradient(152deg,#241412,#120a08 68%);box-shadow:0 0 0 1px rgba(196,120,120,.12) inset,0 0 18px rgba(196,120,120,.08)}
  .system-grid-score{position:relative;display:inline-flex;align-items:center;justify-content:center;min-width:44px;height:30px;padding:0 10px;border-radius:999px;border:1px solid rgba(200,168,75,.32);font-size:12px;font-weight:700;color:#f2e8ce;background:rgba(200,168,75,.14);z-index:1}
  .system-grid-score::before{content:'';position:absolute;inset:-5px;border-radius:999px;background:radial-gradient(circle,rgba(200,168,75,.24),transparent 66%);z-index:-1;animation:scorePulse 3.8s ease-in-out infinite}
  .system-grid-meta{display:flex;align-items:center;gap:7px;font-size:11px;text-transform:uppercase;letter-spacing:.12em;color:#a79f8d}
  .selection-row{margin-top:7px;display:flex;align-items:center;justify-content:space-between;gap:8px;padding:6px 8px;border-radius:10px;border:1px solid rgba(200,168,75,.2);background:rgba(0,0,0,.22)}
  .selection-row p{font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:#bfb6a0}
  .selection-pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:4px 9px;font-size:10px;letter-spacing:.11em;text-transform:uppercase;font-weight:700}
  .selection-pill::before{content:'';width:7px;height:7px;border-radius:999px;background:currentColor;opacity:.95}
  .selection-green{border:1px solid rgba(106,175,144,.34);background:rgba(106,175,144,.12);color:#97d0b5}
  .selection-amber{border:1px solid rgba(200,168,75,.34);background:rgba(200,168,75,.12);color:#e6cf91}
  .selection-red{border:1px solid rgba(196,120,120,.34);background:rgba(196,120,120,.12);color:#e4aaaa}
  .system-grid-card.is-engaged .selection-pill{border-color:rgba(106,175,144,.52);background:rgba(106,175,144,.16);color:#a6dabf}
  .selection-subline{margin-top:5px;font-size:11px;color:#bfb6a0;line-height:1.3}
  .featured-insight{margin-top:6px;padding:7px 8px;border-radius:9px;border:1px solid rgba(200,168,75,.2);background:rgba(200,168,75,.07);font-size:11px;color:#decfa8;line-height:1.35}
  .memory-line{margin-top:5px;font-size:10px;letter-spacing:.09em;color:#a99f89;text-transform:uppercase}
  .action-memory-line{margin-top:4px;font-size:10px;letter-spacing:.11em;text-transform:uppercase;color:#b8ab87}
  .action-memory-line.is-fresh{color:#9fd2ba}
  .card-open-hint{margin-top:4px;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#9b927e}
  .card-title-link{color:inherit;text-decoration:none;border-bottom:1px solid transparent;transition:border-color .2s ease,color .2s ease}
  .card-title-link:hover{border-color:rgba(200,168,75,.48);color:#f2ead8}
  .pressure-line{margin-top:5px;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#d9c897}
  .pressure-line.risk{color:#d7a0a0}
  .pressure-line.stable{color:#99cdb2}
  .system-fast-fix{margin-top:8px;border:1px solid rgba(200,168,75,.16);background:#16120c;border-radius:10px;padding:7px 8px}
  .system-fast-fix p:first-child{font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#d7c28b}
  .system-fast-fix p:last-child{margin-top:4px;font-size:12px;color:#ede3ca;line-height:1.4;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;overflow:hidden}
  .expansion-potential{margin-top:7px;padding:7px 8px;border-radius:9px;border:1px solid rgba(200,168,75,.22);background:rgba(200,168,75,.06)}
  .expansion-potential p:first-child{font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#d7c28b}
  .expansion-potential p:last-child{margin-top:4px;font-size:11px;color:#e6d8b3;line-height:1.35}
  .expansion-preview{margin-top:7px;padding:7px 8px;border-radius:9px;border:1px dashed rgba(200,168,75,.18);background:rgba(0,0,0,.24);opacity:.72;position:relative;overflow:hidden}
  .expansion-preview::after{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(14,12,8,0) 0%,rgba(14,12,8,.28) 100%);pointer-events:none}
  .expansion-preview p:first-child{font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#cab579}
  .expansion-preview p:nth-child(2){margin-top:4px;font-size:11px;color:#c8bfaa;line-height:1.35}
  .expansion-preview .preview-directive{margin-top:5px;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#a69770}
  .next-path-line{margin-top:7px;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#c9bb97}
  .card-response-line{margin-top:6px;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#9fd2ba;opacity:0;transform:translateY(2px);transition:opacity .26s ease,transform .26s ease}
  .card-response-line.live{opacity:1;transform:translateY(0)}
  .system-grid-cta{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:8px 10px;border-radius:8px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;transition:all .2s ease}
  .cta-fix{border:1px solid rgba(200,168,75,.4);background:rgba(200,168,75,.12);color:#f2e8ce}
  .cta-fix:hover{border-color:rgba(200,168,75,.6);background:rgba(200,168,75,.2)}
  .cta-unlock{border:1px solid rgba(214,181,84,.52);background:rgba(214,181,84,.18);color:#f4e8c9}
  .cta-unlock:hover{border-color:rgba(214,181,84,.72);background:rgba(214,181,84,.28)}
  .cta-modal{border:1px solid rgba(159,153,136,.42);background:rgba(159,153,136,.14);color:#e0d8c5}
  .cta-modal:hover{border-color:rgba(200,168,75,.4);background:rgba(200,168,75,.12)}
  .cta-progress{border-color:rgba(214,181,84,.58)!important;background:rgba(214,181,84,.2)!important;color:#f4e9cb!important;position:relative;overflow:hidden}
  .cta-progress::after{content:'';position:absolute;inset:-1px;background:linear-gradient(105deg,transparent 35%,rgba(255,240,199,.2) 50%,transparent 65%);transform:translateX(-130%);animation:ctaSweep .95s ease-in-out infinite}
  .cta-view{border:1px solid rgba(200,168,75,.42);background:rgba(200,168,75,.06);color:#dfd6c1}
  .cta-view:hover{border-color:rgba(200,168,75,.45);background:rgba(200,168,75,.14)}
  .system-card-actions{margin-top:auto;display:flex;gap:7px;padding-top:8px}

  .execution-state{position:absolute;inset:0;display:none;align-items:center;justify-content:center;background:linear-gradient(160deg,rgba(15,12,8,.82),rgba(8,7,5,.9));backdrop-filter:blur(2px);z-index:5}
  .execution-state.active{display:flex}
  .execution-state .chip{display:inline-flex;align-items:center;gap:8px;padding:7px 11px;border-radius:999px;border:1px solid rgba(214,181,84,.44);background:rgba(214,181,84,.15);font-size:10px;letter-spacing:.13em;text-transform:uppercase;color:#ecd9a8}
  .execution-state .chip::before{content:'';width:8px;height:8px;border-radius:999px;background:#d6b15f;box-shadow:0 0 0 0 rgba(214,177,95,.48);animation:execPulse 1.05s ease-in-out infinite}

  /* ── Phase 15: Apply Fix progress system ──────────────────────────── */
  @keyframes nextCardPulse{0%,100%{box-shadow:0 0 0 1px rgba(106,175,144,.22) inset,0 0 20px rgba(106,175,144,.14)}50%{box-shadow:0 0 0 2px rgba(106,175,144,.55) inset,0 0 32px rgba(106,175,144,.36),0 0 0 4px rgba(106,175,144,.12)}}
  .system-grid-card.next-fix-pulse{animation:nextCardPulse 1.6s ease-in-out 3}
  .fix-progress-counter{margin-top:10px;font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#9fd2ba;display:none}
  .fix-progress-counter.has-progress{display:block}
  .cta-applied{border-color:rgba(106,175,144,.44)!important;background:rgba(106,175,144,.12)!important;color:#a6dabf!important;cursor:default}
  .modal-apply-note{margin-top:8px;font-size:11px;color:#a99f89;line-height:1.4}
  /* ────────────────────────────────────────────────────────────────── */
  .correction-modal-mask{position:fixed;inset:0;background:rgba(6,5,3,.74);backdrop-filter:blur(3px);z-index:140;display:none}
  .correction-modal-mask[data-open='true']{display:block}
  .correction-modal{max-width:640px;width:calc(100% - 36px);margin:9vh auto 0;border:1px solid rgba(200,168,75,.24);border-radius:14px;background:linear-gradient(155deg,#17130c,#0f0c08 72%);box-shadow:0 18px 46px rgba(0,0,0,.46);padding:18px}
  .correction-modal h3{font-size:1.15rem;font-weight:600;color:#f0e8d6}
  .correction-modal .meta{margin-top:8px;font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#a99f89}
  .correction-modal .panel{margin-top:12px;border:1px solid rgba(200,168,75,.16);border-radius:10px;background:rgba(200,168,75,.04);padding:10px}
  .correction-modal .panel p:first-child{font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#d7c28b}
  .correction-modal .panel p:last-child{margin-top:4px;font-size:12px;color:#ddd2bc;line-height:1.45}
  .correction-modal .actions{margin-top:14px;display:flex;flex-wrap:wrap;gap:8px;justify-content:space-between;align-items:center}
  .correction-modal .close-btn{display:inline-flex;align-items:center;justify-content:center;padding:9px 12px;border-radius:8px;border:1px solid rgba(200,168,75,.22);background:rgba(200,168,75,.08);color:#ded2b8;font-size:11px;letter-spacing:.08em;text-transform:uppercase}
  .correction-modal .assist-link{font-size:11px;color:#a99f89;text-decoration:none;border-bottom:1px solid rgba(169,159,137,.35)}
  .correction-modal .assist-link:hover{color:#c8b684;border-color:rgba(200,182,132,.58)}

  .readout-flyout-mask{position:fixed;inset:0;z-index:145;background:linear-gradient(120deg,rgba(7,6,4,.44),rgba(7,6,4,.74));backdrop-filter:blur(4px);display:none}
  .readout-flyout-mask[data-open='true']{display:block}
  .readout-flyout{position:absolute;top:0;right:0;height:100%;width:min(560px,100%);border-left:1px solid rgba(200,168,75,.26);background:linear-gradient(160deg,#15120b,#0d0b07 72%);box-shadow:-20px 0 46px rgba(0,0,0,.46),0 0 0 1px rgba(200,168,75,.08) inset;transform:translateX(100%);transition:transform .34s ease}
  .readout-flyout-mask[data-open='true'] .readout-flyout{transform:translateX(0)}
  .readout-flyout::before{content:'';position:absolute;inset:0;pointer-events:none;background:radial-gradient(480px 280px at 0 var(--origin-y,22%),rgba(200,168,75,.16),transparent 72%)}
  .readout-flyout-inner{position:relative;height:100%;overflow:auto;padding:20px 18px 22px}
  .readout-flyout-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px}
  .readout-flyout-kicker{font-size:.58rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.82)}
  .readout-flyout-close{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:7px 10px;border-radius:8px;border:1px solid rgba(200,168,75,.36);background:rgba(200,168,75,.08);font-size:.62rem;letter-spacing:.12em;text-transform:uppercase;color:#ddd2b8}
  .readout-flyout-close:hover{border-color:rgba(200,168,75,.42);background:rgba(200,168,75,.15)}
  .readout-identity{border:1px solid rgba(200,168,75,.2);background:rgba(200,168,75,.05);border-radius:12px;padding:12px 12px 10px;margin-bottom:10px}
  .readout-identity-domain{font-size:.9rem;font-weight:700;color:#efe5cf;line-height:1.35;margin-bottom:8px}
  .readout-identity-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
  .readout-metric{border:1px solid rgba(200,168,75,.16);background:rgba(0,0,0,.2);border-radius:9px;padding:8px}
  .readout-metric-label{font-size:.56rem;letter-spacing:.16em;text-transform:uppercase;color:#ab9f85}
  .readout-metric-value{margin-top:2px;font-size:.78rem;color:#e7dcc1;line-height:1.35}
  .readout-section{border:1px solid rgba(200,168,75,.16);background:rgba(0,0,0,.22);border-radius:10px;padding:10px 11px;margin-bottom:10px}
  .readout-section > p:first-child{font-size:.56rem;letter-spacing:.16em;text-transform:uppercase;color:#cbb67a}
  .readout-section > p:last-child{margin-top:5px;font-size:.78rem;color:#ddd2bb;line-height:1.5}
  .readout-actions{display:grid;grid-template-columns:1fr;gap:8px;margin-top:12px}
  .readout-action-btn{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:9px 12px;border-radius:8px;font-size:.64rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;transition:all .2s ease}
  .readout-action-full{border:1px solid rgba(200,168,75,.46);background:rgba(200,168,75,.16);color:#f3e9cf}
  .readout-action-full:hover{border-color:rgba(200,168,75,.72);background:rgba(200,168,75,.24)}
  .readout-action-full.is-executing{pointer-events:none;opacity:.78;border-color:rgba(200,168,75,.6);box-shadow:0 0 14px rgba(200,168,75,.26)}
  .readout-action-correction{border:1px solid rgba(106,175,144,.4);background:rgba(106,175,144,.14);color:#b9e2cf}
  .readout-action-correction:hover{border-color:rgba(106,175,144,.62);background:rgba(106,175,144,.22)}
  .readout-action-close{border:1px solid rgba(159,153,136,.34);background:rgba(159,153,136,.12);color:#d8cfbb}
  .readout-action-close:hover{border-color:rgba(200,168,75,.38);background:rgba(200,168,75,.12)}
  .readout-correction-inline{display:none;margin-top:10px;border:1px dashed rgba(200,168,75,.24);border-radius:10px;padding:10px;background:rgba(200,168,75,.04)}
  .readout-correction-inline.active{display:block}
  .readout-correction-inline p{font-size:.74rem;color:#d9ccb0;line-height:1.5}
  .next-move-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px}
  .next-move-card{border:1px solid rgba(200,168,75,.16);background:linear-gradient(150deg,#15120b,#0f0c07 70%);border-radius:12px;padding:12px;min-height:132px;transition:transform .22s ease,box-shadow .22s ease,border-color .22s ease,opacity .2s ease}
  .next-move-card:hover{transform:translateY(-2px);border-color:rgba(200,168,75,.36);box-shadow:0 10px 20px rgba(0,0,0,.35)}
  .next-move-card.primary{border-color:rgba(200,168,75,.58);background:linear-gradient(150deg,#21170a,#120c07 70%);box-shadow:0 0 0 1px rgba(200,168,75,.28) inset,0 20px 38px rgba(0,0,0,.48),0 0 20px rgba(200,168,75,.16);transform:translateY(-1px)}
  .next-move-card.primary .action-label{color:#f0ddab}
  .next-move-card.secondary{opacity:.82}
  .next-move-card.secondary:hover{opacity:.94}
  .next-move-command-shell{border:1px solid rgba(200,168,75,.22);border-radius:18px;background:linear-gradient(160deg,#16120b,#0d0a06 72%);padding:18px;box-shadow:0 16px 30px rgba(0,0,0,.3),inset 0 1px 0 rgba(255,255,255,.03)}
  .next-move-command-label{font-size:.68rem;letter-spacing:.24em;text-transform:uppercase;color:#d1bf8d}
  .next-move-command-grid{margin-top:12px;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
  .next-move-command-item{border:1px solid rgba(200,168,75,.15);border-radius:12px;background:rgba(0,0,0,.2);padding:11px}
  .next-move-command-item p:first-child{font-size:.56rem;letter-spacing:.16em;text-transform:uppercase;color:#b6aa8a}
  .next-move-command-item p:last-child{margin-top:5px;font-size:.88rem;line-height:1.45;color:#eee3ca}
  .next-move-command-actions{display:flex;flex-direction:column;align-items:flex-start;gap:8px;margin-top:14px}
  .next-move-command-primary{display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:0 18px;border-radius:12px;background:#c6a85a;color:#1a1a1a;text-decoration:none;font-size:.72rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;box-shadow:0 14px 24px rgba(198,168,90,.2),0 0 0 1px rgba(255,255,255,.16) inset;transition:all .2s ease}
  .next-move-command-primary:hover{transform:translateY(-2px);box-shadow:0 20px 32px rgba(198,168,90,.36),0 0 0 1px rgba(255,255,255,.22) inset}
  .next-move-command-secondary{font-size:.7rem;letter-spacing:.14em;text-transform:uppercase;color:#d7ccb4;text-decoration:none;border-bottom:1px solid rgba(200,168,75,.28)}
  .next-move-command-secondary:hover{border-color:rgba(200,168,75,.54);color:#efe2c5}
  .impact-hint{margin-top:5px;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#b6ad98}
  .impact-hint.high{color:#e4cf97}
  .impact-hint.medium{color:#c9b98a}
  .impact-hint.low{color:#a29677}

  .activation-log{opacity:.78}
  .activation-log article{border-color:rgba(200,168,75,.14)!important;background:#0e0c07!important;box-shadow:none!important;transition:border-color .2s ease,opacity .2s ease,transform .2s ease}
  .activation-log article:hover{opacity:.9;transform:translateY(-2px);border-color:rgba(200,168,75,.24)!important}
  .stack-card{border:1px solid rgba(200,168,75,.16);background:linear-gradient(152deg,#141008,#0f0c07 72%);border-radius:12px;padding:13px;transition:border-color .2s ease,transform .2s ease,box-shadow .2s ease}
  .stack-card:hover{transform:translateY(-2px);border-color:rgba(200,168,75,.3);box-shadow:0 10px 20px rgba(0,0,0,.28)}
  .stack-card.active{border-color:rgba(106,175,144,.32);background:linear-gradient(152deg,#10160f,#0d110d 72%)}
  .stack-card.dormant{opacity:.74}

  .control-hero{border-radius:26px;border-color:rgba(200,168,75,.3);box-shadow:0 28px 56px rgba(0,0,0,.46),0 0 0 1px rgba(200,168,75,.12) inset;padding:28px 28px 24px;background:linear-gradient(145deg,rgba(33,25,15,.98),rgba(11,9,7,.98) 64%),radial-gradient(circle at 8% 18%,rgba(200,168,75,.12),transparent 24%),radial-gradient(circle at 85% 12%,rgba(98,142,135,.1),transparent 26%)}
  .hero-command-deck{position:relative;z-index:1;display:grid;gap:16px}
  .hero-grid{grid-template-columns:minmax(0,1.2fr) minmax(320px,1fr);gap:20px}
  .hero-domain{font-size:clamp(2.8rem,5.5vw,5.2rem);line-height:.9;font-weight:700;letter-spacing:-.05em;color:#f6efdc;max-width:9ch;text-shadow:0 10px 34px rgba(0,0,0,.42)}
  .hero-bottleneck-panel{position:relative;border:1px solid rgba(200,168,75,.22);border-radius:18px;background:linear-gradient(160deg,rgba(0,0,0,.24),rgba(255,255,255,.03));padding:16px 18px;box-shadow:0 14px 26px rgba(0,0,0,.26),inset 0 1px 0 rgba(255,255,255,.04);overflow:hidden}
  .hero-bottleneck-panel::after{content:'';position:absolute;inset:0;background:linear-gradient(120deg,transparent 28%,rgba(200,168,75,.1) 50%,transparent 72%);transform:translateX(-140%);animation:heroSweep 12s ease-in-out infinite}
  .hero-bottleneck-label{font-size:.62rem;letter-spacing:.22em;text-transform:uppercase;color:#d8c58f}
  .hero-bottleneck-copy{position:relative;z-index:1;margin-top:8px;font-size:1rem;line-height:1.65;color:#eae4d8;max-width:36rem}
  .hero-action-band{display:flex;flex-wrap:wrap;gap:10px;align-items:center}
  .hero-primary-cta,.hero-secondary-cta{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:0 18px;border-radius:14px;font-size:.72rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;text-decoration:none;transition:transform .18s ease,box-shadow .22s ease,border-color .22s ease,background .22s ease}
  .hero-primary-cta{background:#c6a85a;color:#1a1a1a;box-shadow:0 18px 28px rgba(198,168,90,.22),0 0 0 1px rgba(255,255,255,.18) inset}
  .hero-primary-cta:hover{transform:translateY(-2px);box-shadow:0 24px 36px rgba(198,168,90,.38),0 0 0 1px rgba(255,255,255,.24) inset}
  .hero-secondary-cta{border:1px solid rgba(200,168,75,.28);background:rgba(255,255,255,.03);color:#ece1c8}
  .hero-secondary-cta:hover{transform:translateY(-2px);border-color:rgba(200,168,75,.52);background:rgba(200,168,75,.1)}
  .hero-micro-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
  .hero-micro-card{border:1px solid rgba(200,168,75,.16);border-radius:14px;background:rgba(255,255,255,.03);padding:12px 13px;box-shadow:inset 0 1px 0 rgba(255,255,255,.03)}
  .hero-micro-card p:first-child{font-size:.58rem;letter-spacing:.16em;text-transform:uppercase;color:#ad9f80}
  .hero-micro-card p:last-child{margin-top:4px;font-size:.88rem;line-height:1.45;color:#eee3ca}
  .hero-score-panel,.hero-side-card{border-radius:20px;padding:18px;box-shadow:0 16px 32px rgba(0,0,0,.3),inset 0 1px 0 rgba(255,255,255,.03)}
  .hero-score-orb{width:168px;height:168px;background:radial-gradient(circle at 50% 35%,rgba(200,168,75,.22),rgba(17,13,8,.96) 72%);box-shadow:0 22px 48px rgba(0,0,0,.46),inset 0 1px 0 rgba(255,255,255,.05)}
  .hero-score-value{font-size:3.7rem;letter-spacing:-.05em}
  .readiness-meter-card{border:1px solid rgba(200,168,75,.18);border-radius:18px;background:linear-gradient(160deg,rgba(255,255,255,.04),rgba(0,0,0,.18));padding:14px;box-shadow:inset 0 1px 0 rgba(255,255,255,.03)}
  .readiness-meter-track{position:relative;height:14px;border-radius:999px;background:rgba(255,255,255,.05);overflow:hidden;margin-top:12px;border:1px solid rgba(200,168,75,.12)}
  .readiness-meter-fill{position:absolute;inset:0 auto 0 0;border-radius:999px;background:linear-gradient(90deg,#7cb89f 0%,#d6b15f 58%,#e6c76b 100%);box-shadow:0 0 18px rgba(200,168,75,.3)}
  .readiness-meter-meta{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:10px;font-size:.68rem;letter-spacing:.16em;text-transform:uppercase;color:#b4a78b}
  .readiness-meter-meta strong{font-size:1.15rem;letter-spacing:-.03em;text-transform:none;color:#f5ead2}
  .scan-history-shell{border:1px solid rgba(200,168,75,.24);border-radius:24px;padding:22px;box-shadow:0 26px 44px rgba(0,0,0,.34),inset 0 1px 0 rgba(255,255,255,.03)}
  .scan-library-header{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(260px,.9fr);gap:18px;align-items:end;margin-bottom:18px}
  .scan-library-kicker{font-size:.68rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(200,168,75,.76)}
  .scan-library-title{margin-top:6px;font-size:clamp(2.1rem,4vw,3.2rem);line-height:.96;font-weight:700;letter-spacing:-.04em;color:#f4ecd7}
  .scan-library-description{margin-top:10px;max-width:42rem;font-size:.95rem;line-height:1.65;color:#d8cdb7}
  .scan-library-summary-wall{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
  .scan-library-summary-wall article{border:1px solid rgba(200,168,75,.16);border-radius:16px;background:rgba(255,255,255,.03);padding:12px 13px;box-shadow:inset 0 1px 0 rgba(255,255,255,.03)}
  .scan-library-summary-wall article p:first-child{font-size:.56rem;letter-spacing:.16em;text-transform:uppercase;color:#aa9e83}
  .scan-library-summary-wall article p:last-child{margin-top:5px;font-size:.92rem;line-height:1.45;color:#efe2c8}
  .scan-shelf{margin-top:18px}
  .scan-shelf-head{display:flex;align-items:end;justify-content:space-between;gap:14px;margin-bottom:12px}
  .scan-shelf-head h3{font-size:.82rem;letter-spacing:.24em;text-transform:uppercase;color:#d9c58f}
  .scan-shelf-head p{font-size:.76rem;line-height:1.5;color:#aba08a;max-width:32rem}
  .scan-featured-grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:12px}
  .scan-featured-grid .scan-history-card{grid-column:span 6;min-height:100%}
  .scan-featured-grid .scan-history-card:first-child{grid-column:span 12}
  .scan-archive-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:12px}
  .scan-card-badges{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
  .scan-card-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 8px;border-radius:999px;border:1px solid rgba(200,168,75,.16);background:rgba(255,255,255,.03);font-size:.52rem;letter-spacing:.16em;text-transform:uppercase;color:#c7baa0}

  @keyframes gridDrift {
    0%{transform:translate3d(0,0,0)}
    50%{transform:translate3d(-2%,2%,0)}
    100%{transform:translate3d(0,0,0)}
  }
  @keyframes heroSweep {
    0%,18%{transform:translateX(-135%)}
    38%,100%{transform:translateX(135%)}
  }

  @keyframes scorePulse {
    0%,100%{opacity:.45;transform:scale(1)}
    50%{opacity:.8;transform:scale(1.06)}
  }
  @keyframes statePulse {
    0%,100%{box-shadow:0 0 0 0 rgba(106,175,144,.45)}
    50%{box-shadow:0 0 0 9px rgba(106,175,144,0)}
  }
  @keyframes presenceDrift {
    0%,100%{transform:translateY(0);opacity:.55}
    50%{transform:translateY(-8px);opacity:.78}
  }
  @keyframes execPulse {
    0%,100%{box-shadow:0 0 0 0 rgba(214,177,95,.48)}
    50%{box-shadow:0 0 0 9px rgba(214,177,95,0)}
  }
  @keyframes ctaSweep {
    0%{transform:translateX(-130%)}
    100%{transform:translateX(130%)}
  }

  .next-action-block{border:1px solid rgba(200,168,75,.26);border-radius:14px;background:linear-gradient(155deg,rgba(30,22,10,.96),rgba(12,9,6,.98) 68%);padding:14px;box-shadow:0 10px 24px rgba(0,0,0,.28),inset 0 1px 0 rgba(255,255,255,.03)}
  .next-action-label{font-size:.6rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.82)}
  .next-action-copy{margin-top:6px;font-size:.86rem;line-height:1.55;color:#eae4d8}
  .next-action-cta{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 18px;border-radius:10px;margin-top:12px;background:#c6a85a;color:#1a1a1a;text-decoration:none;font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;transition:all .2s ease;box-shadow:0 8px 18px rgba(198,168,90,.22),0 0 0 1px rgba(255,255,255,.12) inset}
  .next-action-cta:hover{transform:translateY(-2px);box-shadow:0 14px 28px rgba(198,168,90,.38),0 0 0 1px rgba(255,255,255,.18) inset}

  @media(max-width:900px){
    .system-grid-toolbar{flex-direction:column;align-items:stretch}
    .state-metric-grid{grid-template-columns:1fr 1fr}
    .hero-grid{grid-template-columns:1fr}
    .hero-score-wrap{align-items:flex-start}
    .hero-telemetry-deck{grid-template-columns:1fr}
    .report-readout-grid{grid-template-columns:1fr}
    .onboarding-proof-strip{grid-template-columns:1fr}
    .ia-level-grid{grid-template-columns:1fr}
    .scan-library-header{grid-template-columns:1fr;gap:10px}
    .scan-library-summary-wall{grid-template-columns:1fr 1fr}
  }
  @media(max-width:768px){
    /* ── Grid layouts ── */
    .system-grid{grid-template-columns:1fr;gap:12px}
    .system-grid-card{min-height:unset;padding:16px}
    .next-move-grid{grid-template-columns:1fr}
    .state-metric-grid{grid-template-columns:1fr}
    .hub-priority-grid{grid-template-columns:1fr}
    .scan-history-grid{grid-template-columns:1fr}
    .hero-domain{max-width:none}
    .hero-score-wrap{flex-direction:column;align-items:flex-start}
    .hero-side-grid,.scan-history-context{grid-template-columns:1fr}
    .telemetry-mini-grid{grid-template-columns:1fr}
    .next-move-command-grid{grid-template-columns:1fr}
    .scan-featured-grid .scan-history-card{grid-column:span 12}
    .hero-micro-grid{grid-template-columns:1fr 1fr}

    /* ── Section shells — comfortable padding on mobile ── */
    .exec-hero-shell{padding:20px 18px 18px}
    .proj-identity-bar{padding:14px 16px;margin-bottom:22px}
    .score-drivers-shell{padding:18px 16px}
    .ai-advisor-shell{padding:18px 16px}
    .scan-history-shell{padding:18px 16px}
    .your-plan-shell{padding:20px 16px}
    .next-move-shell{padding:20px 16px}
    .next-action-block{padding:16px}
    .system-grid-card.featured{padding:16px}
    .score-driver-card{padding:16px 14px;gap:12px}
    .nm-card{padding:16px 18px}
    .level-card{padding:18px 16px}
    .level-card.state-active{padding:20px 18px}
    .plan-current-card,.plan-next-card,.plan-all-unlocked-card{padding:16px}
    .next-move-row{gap:12px}
    .level-system-shell{gap:20px}
    .score-drivers-grid{gap:12px}
    .exec-interp-col{gap:12px}

    /* ── Section header typography ── */
    .dash-section-label{font-size:.62rem;letter-spacing:.2em}
    .dash-section-heading{font-size:1.05rem;line-height:1.35;margin-bottom:6px}
    .dash-section-subhead{font-size:.84rem;line-height:1.62;color:#b0a898}
    .section-head h2{font-size:.78rem}
    .section-head p{font-size:.74rem;line-height:1.52}

    /* ── Kicker / label text — floor at .62rem ── */
    .scan-library-kicker{font-size:.64rem;letter-spacing:.2em}
    .score-drivers-kicker{font-size:.64rem;letter-spacing:.2em}
    .ai-advisor-kicker{font-size:.63rem;letter-spacing:.2em}
    .hero-bottleneck-label{font-size:.64rem}
    .hero-panel-label{font-size:.64rem}
    .hero-overline{font-size:.65rem;color:rgba(200,168,75,.78)}
    .hero-score-caption{font-size:.6rem}
    .next-action-label{font-size:.62rem;letter-spacing:.18em}
    .exec-interp-question{font-size:.63rem;letter-spacing:.18em}
    .exec-bottleneck-label{font-size:.6rem}
    .readout-flyout-kicker{font-size:.62rem}
    .plan-current-kicker,.plan-next-kicker,.plan-all-unlocked-kicker{font-size:.6rem}
    .nm-card-kicker{font-size:.59rem;letter-spacing:.16em}
    .level-card-kicker{font-size:.6rem;letter-spacing:.16em}
    .sdc-fix-label{font-size:.6rem}
    .level-state-badge{font-size:.56rem}
    .exec-ctx-pill{font-size:.6rem}

    /* ── Body / content text ── */
    .exec-interp-answer{font-size:1rem;line-height:1.64}
    .exec-bottleneck-copy{font-size:.88rem;line-height:1.62}
    .hero-bottleneck-copy{font-size:.95rem;line-height:1.68}
    .sdc-issue{font-size:.9rem;line-height:1.46}
    .sdc-why{font-size:.81rem;line-height:1.62}
    .sdc-fix-copy{font-size:.82rem;line-height:1.58}
    .nm-card-title{font-size:.92rem;line-height:1.48}
    .nm-card-rationale{font-size:.81rem;line-height:1.6}
    .next-action-copy{font-size:.88rem;line-height:1.64}
    .level-card-desc{font-size:.8rem;line-height:1.58}
    .level-card-name{font-size:.97rem;line-height:1.35}
    .level-card-step{font-size:.79rem;line-height:1.52}
    .level-card-why{font-size:.76rem;line-height:1.54}
    .plan-current-desc,.plan-next-desc{font-size:.82rem;line-height:1.58}
    .plan-current-name,.plan-next-name,.plan-all-unlocked-title{font-size:.96rem}
    .plan-included-item,.plan-next-unlock-item{font-size:.8rem;line-height:1.52}
    .scan-library-description{font-size:.9rem;line-height:1.66}
    .scan-library-title{font-size:clamp(1.9rem,5vw,3rem)}
    .ai-advisor-desc{font-size:.81rem;line-height:1.6}
    .ai-advisor-title{font-size:.95rem}
    .score-drivers-title{font-size:1.01rem;line-height:1.38}
    .score-drivers-desc{font-size:.82rem;line-height:1.62}
    .consult-cta-copy p:last-child{font-size:.87rem;line-height:1.52}
    .plan-consult-copy{font-size:.8rem;line-height:1.5}

    /* ── Small meta text — readable on phone screens ── */
    .system-grid-meta{font-size:.64rem}
    .selection-row p{font-size:.64rem}
    .selection-pill{font-size:.6rem}
    .memory-line,.action-memory-line{font-size:.63rem}
    .proj-identity-pill{font-size:.62rem}
    .featured-insight{font-size:.79rem;line-height:1.48}
    .scan-card-badge{font-size:.58rem}
    .scan-shelf-head p{font-size:.79rem}
    .level-rail-step-label{font-size:.58rem;max-width:64px}
    .card-open-hint{font-size:.62rem}
    .readout-metric-label{font-size:.62rem}
    .pressure-line,.next-path-line{font-size:.62rem}
    .impact-hint{font-size:.62rem}
    .exec-cta-stat{font-size:.58rem}
    .exec-trust-line{font-size:.62rem}
    .proj-identity-brand{font-size:.72rem}
    .proj-identity-url{font-size:.74rem}

    /* ── Tap targets — all key buttons ≥44px ── */
    .exec-ctx-btn,.exec-ctx-btn-outline{min-height:44px;padding:0 18px;font-size:.64rem}
    .sdc-ask-btn{min-height:44px;padding:8px 16px}
    .sdc-unlock-btn{min-height:44px;padding:0 16px}
    .nm-card-action,.nm-card-action-btn{min-height:44px;padding:8px 16px}
    .plan-consult-btn,.plan-view-report-btn{min-height:44px;padding:0 18px}
    .next-action-cta{min-height:48px;width:100%;font-size:.74rem;border-radius:12px}
    .exec-cta-primary{min-height:48px;font-size:.72rem}
    .exec-cta-secondary{min-height:44px;font-size:.7rem}
    .plan-unlock-cta{min-height:48px;font-size:.72rem}
    .premium-gate-cta{min-height:48px;font-size:.74rem;padding:0 26px}
    .ai-advisor-open-btn{min-height:44px;padding:0 20px}
    .pib-report-btn{min-height:44px;padding:0 20px}
    .pib-report-btn-outline{min-height:44px;padding:0 18px}
    .system-grid-cta{min-height:44px;font-size:.64rem;padding:9px 12px}
    .readout-action-btn{min-height:44px;width:100%}
    .readout-action-full,.readout-action-correction,.readout-action-close{min-height:44px}
    .level-card-cta-primary,.level-card-cta-secondary,.level-card-cta-disabled{min-height:44px}
    .hero-primary-cta{min-height:50px;width:100%;font-size:.74rem}
    .hero-secondary-cta{min-height:44px;width:100%}
    .hero-action-band{flex-direction:column;gap:8px;align-items:stretch}
    .consult-cta-btn{min-height:44px;width:100%;justify-content:center}
    .dcm-btn-primary,.dcm-btn-skip{min-height:46px}

    /* ── Layout fixes ── */
    .consult-cta-banner{flex-direction:column;gap:14px;align-items:stretch}
    .plan-consult-row{flex-direction:column;align-items:flex-start;gap:10px}
    .pib-right{align-items:stretch;gap:8px}
    .exec-score-ring{width:90px;height:90px}
    .exec-score-num{font-size:2.2rem}
    .score-drivers-head{flex-direction:column;gap:10px}
    .score-drivers-meta{align-items:flex-start}
    .ai-advisor-chip{width:100%}
    .readout-actions{gap:10px}
    .readout-identity-grid{grid-template-columns:1fr}
    .exec-action-col{min-width:unset;gap:8px}
  }
  @media(max-width:480px){
    /* Very small phones — tighten shells, scale down a few last elements */
    .exec-hero-shell{padding:16px 14px 14px}
    .proj-identity-bar{padding:12px 14px;margin-bottom:18px}
    .pib-domain-name{font-size:1.5rem;letter-spacing:-.02em}
    .exec-score-ring{width:80px;height:80px}
    .exec-score-num{font-size:2rem}
    .exec-score-lbl{font-size:.48rem}
    .scan-library-title{font-size:clamp(1.7rem,7vw,2.4rem)}
    .hero-primary-cta,.hero-secondary-cta{font-size:.72rem}
    .your-plan-shell{padding:14px}
    .next-move-shell{padding:14px}
    .score-drivers-shell{padding:14px}
    .scan-history-shell{padding:14px}
    .ai-advisor-shell{padding:14px}
    .ai-advisor-chip{font-size:.7rem;padding:8px 13px}
    .level-rail-step-dot{width:24px;height:24px;font-size:.54rem}
    .level-rail-step-label{font-size:.54rem;max-width:52px}
    .plan-next-price{font-size:.96rem}
    .exec-interp-answer{font-size:.94rem}
    .hero-micro-grid{grid-template-columns:1fr}
    .next-action-cta,.plan-unlock-cta,.exec-cta-primary{font-size:.72rem}
  }
  @media(min-width:1100px){
    .system-grid-card.featured{grid-column:span 2}
  }
  @media(prefers-reduced-motion:reduce){
    .control-hero::after,.hub-priority-card::after,.hero-score-orb::after,.system-unified-module::after,.state-pulse::before,.system-grid-score::before,.execution-state .chip::before{animation:none!important}
    .surface-reveal,.system-grid-card,.scan-history-card,.next-move-card,.stack-card{transition:none!important}
  }

  /* ── Level System ─────────────────────────────────── */
  .level-system-shell{display:flex;flex-direction:column;gap:24px}

  /* Rail */
  .level-rail{display:flex;align-items:center;gap:0;margin-bottom:4px}
  .level-rail-step{display:flex;flex-direction:column;align-items:center;gap:6px;position:relative;flex:1}
  .level-rail-step-dot{width:28px;height:28px;border-radius:50%;border:2px solid rgba(200,168,75,.3);background:#1a1610;display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;letter-spacing:.06em;color:rgba(200,168,75,.4);transition:all .25s ease;position:relative;z-index:2}
  .level-rail-step.is-complete .level-rail-step-dot{background:rgba(200,168,75,.22);border-color:rgba(200,168,75,.75);color:#c6a85a}
  .level-rail-step.is-active .level-rail-step-dot{background:rgba(200,168,75,.18);border-color:#c6a85a;color:#c6a85a;box-shadow:0 0 0 4px rgba(200,168,75,.12)}
  .level-rail-step.is-locked .level-rail-step-dot{opacity:.42}
  .level-rail-step-label{font-size:.55rem;letter-spacing:.1em;text-transform:uppercase;color:rgba(200,168,75,.45);text-align:center;max-width:76px;line-height:1.35;word-break:break-word}
  .level-rail-step.is-complete .level-rail-step-label,.level-rail-step.is-active .level-rail-step-label{color:rgba(200,168,75,.7)}
  .level-rail-connector{flex:1;height:1px;background:rgba(200,168,75,.18);margin-top:-14px;position:relative;z-index:1}
  .level-rail-connector.is-complete{background:rgba(200,168,75,.62);box-shadow:0 0 8px rgba(200,168,75,.28)}

  /* Level card grid */
  .level-card-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
  @media(max-width:780px){.level-card-grid{grid-template-columns:1fr}}

  /* Level cards */
  .level-card{border:1px solid rgba(200,168,75,.18);border-radius:14px;background:linear-gradient(155deg,#181410,#0e0c09 70%);padding:18px;position:relative;overflow:hidden;transition:transform .2s ease,box-shadow .2s ease}
  .level-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.28),transparent)}
  .level-card.state-active{border-color:rgba(200,168,75,.58);background:linear-gradient(155deg,#1e1b10,#110f08 68%);box-shadow:0 14px 38px rgba(0,0,0,.38),0 0 30px rgba(200,168,75,.18),0 0 0 1px rgba(200,168,75,.14) inset;padding:22px}
  .level-card.state-active::before{background:linear-gradient(90deg,transparent,rgba(200,168,75,.6),transparent)}
  .level-card.state-complete{border-color:rgba(106,175,144,.3);background:linear-gradient(155deg,#101a14,#090d0b 70%)}
  .level-card.state-complete::before{background:linear-gradient(90deg,transparent,rgba(106,175,144,.4),transparent)}
  .level-card.state-locked{border-color:rgba(200,168,75,.1);background:linear-gradient(155deg,#101009,#0a0908 70%)}
  .level-card:not(.state-locked):hover{transform:translateY(-3px);box-shadow:0 16px 40px rgba(0,0,0,.4)}

  /* Accent bar */
  .level-card-accent{position:absolute;top:16px;bottom:16px;left:0;width:3px;border-radius:0 2px 2px 0}
  .state-active .level-card-accent{background:linear-gradient(180deg,#c6a85a,rgba(200,168,75,.45))}
  .state-complete .level-card-accent{background:linear-gradient(180deg,#6aaf90,rgba(106,175,144,.45))}
  .state-locked .level-card-accent{background:rgba(200,168,75,.18)}

  /* Card header */
  .level-card-kicker{font-size:.55rem;letter-spacing:.22em;text-transform:uppercase;margin-bottom:6px}
  .state-active .level-card-kicker{color:rgba(200,168,75,.8)}
  .state-complete .level-card-kicker{color:rgba(106,175,144,.8)}
  .state-locked .level-card-kicker{color:rgba(200,168,75,.38)}
  .level-card-name{font-size:.95rem;font-weight:600;color:#ede8de;letter-spacing:.01em;margin-bottom:4px}
  .level-card-desc{font-size:.78rem;line-height:1.52;color:#b5ae9e;margin-bottom:12px}

  /* Step list */
  .level-card-steps{display:flex;flex-direction:column;gap:7px;margin-bottom:14px}
  .level-card-step{display:flex;align-items:flex-start;gap:8px;font-size:.76rem;line-height:1.45;color:#b8b0a0}
  .level-card-step-icon{flex-shrink:0;width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-top:1px}
  .state-active .level-card-step-icon{background:rgba(200,168,75,.2);color:#c6a85a}
  .state-complete .level-card-step-icon{background:rgba(106,175,144,.2);color:#6aaf90}
  .state-locked .level-card-step-icon{background:rgba(200,168,75,.1);color:rgba(200,168,75,.3)}
  .level-card-step-icon svg{width:8px;height:8px}

  /* Lift badge */
  .level-card-lift{display:inline-flex;align-items:center;gap:5px;font-size:.6rem;letter-spacing:.1em;text-transform:uppercase;padding:3px 9px;border-radius:20px;margin-bottom:14px}
  .state-active .level-card-lift{background:rgba(200,168,75,.14);color:#c6a85a;border:1px solid rgba(200,168,75,.3)}
  .state-complete .level-card-lift{background:rgba(106,175,144,.12);color:#6aaf90;border:1px solid rgba(106,175,144,.3)}
  .state-locked .level-card-lift{background:rgba(200,168,75,.06);color:rgba(200,168,75,.3);border:1px solid rgba(200,168,75,.12)}

  /* State badge */
  .level-state-badge{display:inline-flex;align-items:center;gap:5px;font-size:.52rem;letter-spacing:.18em;text-transform:uppercase;padding:3px 8px;border-radius:20px;position:absolute;top:14px;right:14px}
  .level-state-badge.badge-ready{background:rgba(200,168,75,.16);color:#c6a85a;border:1px solid rgba(200,168,75,.35)}
  .level-state-badge.badge-completed{background:rgba(106,175,144,.14);color:#6aaf90;border:1px solid rgba(106,175,144,.3)}
  .level-state-badge.badge-locked{background:rgba(80,70,55,.3);color:rgba(200,168,75,.4);border:1px solid rgba(200,168,75,.12)}

  /* Level CTAs */
  .level-card-cta-primary{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 18px;border-radius:10px;background:#c6a85a;color:#1a1a1a;text-decoration:none;font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;transition:all .2s ease;box-shadow:0 6px 16px rgba(198,168,90,.2),inset 0 1px 0 rgba(255,255,255,.12);border:none;cursor:pointer;width:100%}
  .level-card-cta-primary:hover{transform:translateY(-2px);box-shadow:0 12px 26px rgba(198,168,90,.36),inset 0 1px 0 rgba(255,255,255,.18)}
  .level-card-cta-secondary{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 16px;border-radius:10px;background:transparent;border:1px solid rgba(106,175,144,.4);color:#6aaf90;text-decoration:none;font-size:.68rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;transition:all .2s ease;width:100%}
  .level-card-cta-secondary:hover{background:rgba(106,175,144,.08);border-color:rgba(106,175,144,.6)}
  .level-card-cta-disabled{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 16px;border-radius:10px;background:rgba(200,168,75,.06);border:1px solid rgba(200,168,75,.14);color:rgba(200,168,75,.35);font-size:.68rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;width:100%;cursor:not-allowed;gap:6px}

  /* "Start Here" label for active step 1 */
  .lc-start-here{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:999px;background:rgba(200,168,75,.2);border:1px solid rgba(200,168,75,.48);color:#f0e2b0;font-size:.52rem;letter-spacing:.18em;text-transform:uppercase;margin-bottom:8px}
  .lc-start-here::before{content:'';width:5px;height:5px;border-radius:50%;background:#c6a85a;box-shadow:0 0 6px rgba(200,168,75,.7);flex-shrink:0}

  /* Impact badges (replacing bullet step list) */
  .lc-impact-badges{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px}
  .lc-impact-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:999px;font-size:.6rem;letter-spacing:.08em;line-height:1.3;}
  .state-active .lc-impact-badge{background:rgba(200,168,75,.1);border:1px solid rgba(200,168,75,.22);color:#d8c98a}
  .state-complete .lc-impact-badge{background:rgba(106,175,144,.1);border:1px solid rgba(106,175,144,.22);color:#9acab8}
  .state-locked .lc-impact-badge{background:rgba(200,168,75,.05);border:1px solid rgba(200,168,75,.1);color:rgba(200,168,75,.32)}
  .lc-impact-badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}

  /* Locked card overlay */
  .lc-locked-overlay{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(8,6,4,.62);backdrop-filter:blur(2px);border-radius:inherit;z-index:10}
  .lc-locked-overlay-chip{display:inline-flex;align-items:center;gap:7px;padding:8px 13px;border-radius:999px;background:rgba(18,14,8,.92);border:1px solid rgba(200,168,75,.24);font-size:.58rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(200,168,75,.65)}

  /* Premium gate */
  .premium-gate-card{border:1px solid rgba(200,168,75,.35);border-radius:14px;background:linear-gradient(145deg,#1f1a0e,#120f09 70%);padding:22px;text-align:center;position:relative;overflow:hidden}
  .premium-gate-card::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 50% 0,rgba(200,168,75,.1),transparent 60%);pointer-events:none}
  .premium-gate-kicker{font-size:.58rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.65);margin-bottom:8px}
  .premium-gate-title{font-size:1.1rem;font-weight:600;color:#ede8de;margin-bottom:8px}
  .premium-gate-desc{font-size:.82rem;line-height:1.6;color:#9a9082;margin-bottom:18px;max-width:480px;margin-inline:auto}
  .premium-gate-cta{display:inline-flex;align-items:center;gap:8px;min-height:44px;padding:0 24px;border-radius:11px;background:#c6a85a;color:#1a1a1a;text-decoration:none;font-size:.72rem;font-weight:700;letter-spacing:.13em;text-transform:uppercase;transition:all .2s ease;box-shadow:0 8px 20px rgba(198,168,90,.25)}
  .premium-gate-cta:hover{transform:translateY(-2px);box-shadow:0 14px 32px rgba(198,168,90,.4)}

  /* Consultation CTA banner */
  .consult-cta-banner{border:1px solid rgba(200,168,75,.22);border-radius:13px;background:linear-gradient(155deg,#161309,#0d0b07 70%);padding:18px 22px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
  .consult-cta-copy p:first-child{font-size:.62rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.65);margin-bottom:4px}
  .consult-cta-copy p:last-child{font-size:.88rem;color:#d4cebc;line-height:1.45}
  .consult-cta-btn{flex-shrink:0;display:inline-flex;align-items:center;gap:8px;min-height:40px;padding:0 18px;border-radius:10px;background:transparent;border:1px solid rgba(200,168,75,.5);color:#d4b972;text-decoration:none;font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;transition:all .2s ease;white-space:nowrap}
  .consult-cta-btn:hover{background:rgba(200,168,75,.1);border-color:rgba(200,168,75,.75)}

  /* Data capture modal */
  .dcm-mask{position:fixed;inset:0;background:rgba(0,0,0,.82);z-index:9990;display:flex;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(6px);opacity:0;pointer-events:none;transition:opacity .22s ease}
  .dcm-mask[data-open="true"]{opacity:1;pointer-events:all}
  .dcm-shell{background:linear-gradient(145deg,#1d1912,#0f0d09);border:1px solid rgba(200,168,75,.4);border-radius:16px;padding:28px;width:100%;max-width:480px;position:relative;transform:translateY(12px);transition:transform .22s ease;box-shadow:0 24px 60px rgba(0,0,0,.6),inset 0 1px 0 rgba(255,255,255,.04)}
  .dcm-mask[data-open="true"] .dcm-shell{transform:translateY(0)}
  .dcm-kicker{font-size:.58rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.7);margin-bottom:8px}
  .dcm-title{font-size:1rem;font-weight:600;color:#ede8de;margin-bottom:4px}
  .dcm-subtitle{font-size:.8rem;color:#948c7c;line-height:1.5;margin-bottom:20px}
  .dcm-close{position:absolute;top:16px;right:16px;width:28px;height:28px;border-radius:6px;background:rgba(200,168,75,.1);border:1px solid rgba(200,168,75,.2);color:rgba(200,168,75,.6);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.9rem;transition:all .15s ease}
  .dcm-close:hover{background:rgba(200,168,75,.2);color:#c6a85a}
  .dcm-form-fields{display:flex;flex-direction:column;gap:12px;margin-bottom:20px}
  .dcm-field label{display:block;font-size:.62rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(200,168,75,.65);margin-bottom:5px}
  .dcm-field input{width:100%;background:#0f0d09;border:1px solid rgba(200,168,75,.28);border-radius:8px;padding:9px 12px;color:#ede8de;font-size:.84rem;outline:none;transition:border-color .15s ease}
  .dcm-field input:focus{border-color:rgba(200,168,75,.65)}
  .dcm-field input::placeholder{color:#5a5245}
  .dcm-actions{display:flex;gap:10px}
  .dcm-btn-primary{flex:1;min-height:42px;border-radius:10px;background:#c6a85a;color:#1a1a1a;border:none;font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;cursor:pointer;transition:all .2s ease}
  .dcm-btn-primary:hover{background:#d4b865;transform:translateY(-1px)}
  .dcm-btn-primary:disabled{opacity:.55;cursor:not-allowed;transform:none}
  .dcm-btn-skip{min-height:42px;padding:0 14px;border-radius:10px;background:transparent;border:1px solid rgba(200,168,75,.38);color:rgba(200,168,75,.82);font-size:.68rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;transition:all .15s ease}
  .dcm-btn-skip:hover{border-color:rgba(200,168,75,.4);color:rgba(200,168,75,.75)}

  /* ── Project Identity Bar ─────────────────────────────── */
  .proj-identity-bar{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding:12px 16px;border:1px solid rgba(200,168,75,.2);border-radius:12px;background:linear-gradient(135deg,rgba(26,20,10,.9),rgba(10,8,5,.95));margin-bottom:20px}
  .proj-identity-left{display:flex;align-items:center;gap:12px}
  .proj-identity-icon{width:34px;height:34px;border-radius:8px;background:rgba(200,168,75,.12);border:1px solid rgba(200,168,75,.28);display:flex;align-items:center;justify-content:center;flex-shrink:0}
  .proj-identity-brand{font-size:.7rem;font-weight:600;color:rgba(200,168,75,.9);letter-spacing:.04em;margin-bottom:1px}
  .proj-identity-url{font-size:.72rem;color:#b8b0a0;letter-spacing:.02em}
  .proj-identity-meta{display:flex;align-items:center;gap:14px;flex-wrap:wrap}
  .proj-identity-pill{display:flex;align-items:center;gap:5px;font-size:.58rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(200,168,75,.6);padding:3px 8px;border-radius:6px;background:rgba(200,168,75,.08);border:1px solid rgba(200,168,75,.14)}
  .proj-identity-pill.is-live{color:rgba(106,175,144,.8);background:rgba(106,175,144,.1);border-color:rgba(106,175,144,.25)}
  .proj-identity-pill svg{flex-shrink:0}
  /* Scan provenance line */
  .scan-provenance{font-size:.58rem;letter-spacing:.1em;text-transform:uppercase;color:rgba(200,168,75,.42);margin-top:6px}
  /* Level step descriptions */
  .level-card-why{font-size:.72rem;line-height:1.5;color:rgba(200,168,75,.65);border-left:2px solid rgba(200,168,75,.25);padding-left:10px;margin-bottom:12px;font-style:italic}
  /* DCM level fields show/hide */
  .dcm-level-fields{display:none}
  .dcm-level-fields.is-active{display:block}
  .dcm-section-label{font-size:.58rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.55);margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid rgba(200,168,75,.15)}
  .dcm-field-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
  @media(max-width:500px){.dcm-field-row{grid-template-columns:1fr}}

  /* ── Enhanced Project Identity Bar ─────────────────── */
  .proj-identity-bar{padding:22px 24px;background:linear-gradient(140deg,rgba(34,26,10,.98),rgba(10,8,5,.99));border-color:rgba(200,168,75,.42);border-radius:14px;box-shadow:0 16px 48px rgba(0,0,0,.5),0 0 0 1px rgba(200,168,75,.1),inset 0 1px 0 rgba(255,255,255,.05);margin-bottom:28px}
  .pib-live-row{display:flex;align-items:center;gap:8px;margin-bottom:8px}
  .pib-live-dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:#c8a84b;flex-shrink:0;animation:pibPulse 2s ease-in-out infinite;box-shadow:0 0 10px rgba(198,168,90,.9)}
  .pib-live-label{font-size:.6rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.8)}
  .pib-domain-name{font-size:clamp(1.5rem,3.5vw,2.2rem);font-weight:800;color:#fff;letter-spacing:-.03em;line-height:1.1;text-shadow:0 0 40px rgba(200,168,75,.28),0 2px 10px rgba(0,0,0,.5)}
  .pib-right{display:flex;flex-direction:column;align-items:flex-end;gap:10px}
  .pib-report-btn{display:inline-flex;align-items:center;gap:7px;min-height:40px;padding:0 18px;border-radius:10px;background:#c6a85a;color:#1a1a1a;text-decoration:none;font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;transition:all .2s ease;box-shadow:0 6px 16px rgba(198,168,90,.24),inset 0 1px 0 rgba(255,255,255,.14);white-space:nowrap;flex-shrink:0}
  .pib-report-btn:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(198,168,90,.38),inset 0 1px 0 rgba(255,255,255,.18)}
  .pib-report-btn-outline{display:inline-flex;align-items:center;gap:6px;min-height:38px;padding:0 16px;border-radius:10px;border:1px solid rgba(200,168,75,.48);background:rgba(200,168,75,.06);color:rgba(200,168,75,.9);text-decoration:none;font-size:.66rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;transition:all .2s ease;white-space:nowrap;flex-shrink:0}
  .pib-report-btn-outline:hover{background:rgba(200,168,75,.1);border-color:rgba(200,168,75,.55)}
  @keyframes pibPulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.65);opacity:0.45}}
  /* Domain glow on scan cards */
  .scan-history-card .domain{text-shadow:0 0 22px rgba(200,168,75,.15)}
  .scan-history-card:hover .domain{text-shadow:0 0 30px rgba(200,168,75,.32);color:#fff8ec}
  /* DCM select style */
  .dcm-field select{width:100%;background:#0f0d09;border:1px solid rgba(200,168,75,.28);border-radius:8px;padding:9px 12px;color:#ede8de;font-size:.84rem;outline:none;transition:border-color .15s ease;-webkit-appearance:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='rgba(200,168,75,0.5)' stroke-width='1.4' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center}
  .dcm-field select:focus{border-color:rgba(200,168,75,.65)}

  /* ── Domain-first hero upgrades ───────────────────────────── */
  /* Bigger, gold-gradient domain name */
  .hero-domain-live{
    font-size:clamp(2.6rem,5.8vw,5.6rem);line-height:.9;font-weight:700;
    letter-spacing:-.05em;
    background:linear-gradient(125deg,#f5e9c8 8%,#c8a84b 44%,#ede0ba 78%);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
    text-shadow:none;
    filter:drop-shadow(0 0 28px rgba(200,168,75,.35));
    max-width:none;text-wrap:balance
  }

  /* ── Live feedback strip ───────────────────────────────────── */
  .live-feedback-strip{
    display:flex;align-items:center;gap:10px;
    padding:9px 14px;
    border:1px solid rgba(200,168,75,.18);
    border-radius:10px;
    background:linear-gradient(135deg,rgba(22,17,8,.96),rgba(10,8,5,.98));
    margin-bottom:16px;
    box-shadow:0 0 18px rgba(200,168,75,.07);
    overflow:hidden;position:relative
  }
  .live-feedback-strip::before{
    content:'';position:absolute;left:0;top:0;bottom:0;width:3px;
    background:linear-gradient(180deg,rgba(200,168,75,.9),rgba(200,168,75,.32));
    border-radius:2px
  }
  .lfs-dot{
    width:7px;height:7px;border-radius:50%;background:#c8a84b;flex-shrink:0;
    box-shadow:0 0 0 0 rgba(200,168,75,.55);
    animation:lfsPulse 2.2s ease-in-out infinite
  }
  @keyframes lfsPulse{
    0%,100%{box-shadow:0 0 0 0 rgba(200,168,75,.55)}
    50%{box-shadow:0 0 0 8px rgba(200,168,75,0)}
  }
  .lfs-label{font-size:.52rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.62);flex-shrink:0}
  .lfs-divider{width:1px;height:14px;background:rgba(200,168,75,.2);flex-shrink:0}
  .lfs-insight{font-size:.76rem;color:#e4dcc8;line-height:1.4}
  .lfs-insight strong{color:#f0dfaa}

  /* ── State color chips ─────────────────────────────────────── */
  .state-chip{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:999px;font-size:.56rem;letter-spacing:.16em;text-transform:uppercase;font-weight:600}
  .state-chip::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0}
  .state-chip-red{border:1px solid rgba(196,80,80,.45);background:rgba(196,80,80,.14);color:#e8a8a8}
  .state-chip-amber{border:1px solid rgba(214,163,55,.45);background:rgba(214,163,55,.14);color:#e8cfa0}
  .state-chip-green{border:1px solid rgba(80,175,120,.42);background:rgba(80,175,120,.13);color:#9fd4b8}
  .state-chip-gold{border:1px solid rgba(200,168,75,.42);background:rgba(200,168,75,.13);color:#dfc98a}

  /* ── YOU ARE HERE marker ───────────────────────────────────── */
  .you-are-here{
    display:inline-flex;align-items:center;gap:7px;
    padding:5px 12px;border-radius:8px;
    background:rgba(200,168,75,.12);
    border:1px solid rgba(200,168,75,.35);
    border-left:3px solid #c8a84b;
    font-size:.58rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.9)
  }
  .you-are-here-dot{width:8px;height:8px;border-radius:50%;background:#c8a84b;box-shadow:0 0 10px rgba(200,168,75,.8);flex-shrink:0}

  /* ── System status strip ───────────────────────────────────── */
  .sys-status-strip{
    display:flex;flex-wrap:wrap;align-items:center;gap:8px;
    padding:10px 14px;
    border:1px solid rgba(200,168,75,.16);
    border-radius:10px;
    background:rgba(0,0,0,.24);
    margin-bottom:16px
  }
  .sys-status-label{font-size:.52rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.48);margin-right:4px}

  /* ── Level rail "YOU ARE HERE" callout ─────────────────────── */
  .rail-you-are-here{
    margin-bottom:10px;display:flex;align-items:center;gap:8px
  }

  /* ── Domain highlight in section headings ─────────────────── */
  .domain-accent{color:#e8d090;font-style:normal;font-weight:inherit}

  /* ── Score Drivers ─────────────────────────── */
  .score-drivers-shell{border:1px solid rgba(200,168,75,.22);border-radius:20px;background:linear-gradient(155deg,#16120a,#0d0a06 72%);padding:22px;box-shadow:0 18px 38px rgba(0,0,0,.32),inset 0 1px 0 rgba(255,255,255,.03)}
  .score-drivers-head{display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:18px}
  .score-drivers-kicker{font-size:.62rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(200,168,75,.76);margin-bottom:6px}
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
  .sdc-why{font-size:.78rem;line-height:1.55;color:#c8c0ae}
  .sdc-fix{border:1px solid rgba(200,168,75,.16);border-radius:10px;background:rgba(200,168,75,.05);padding:10px 12px}
  .sdc-fix-label{font-size:.52rem;letter-spacing:.18em;text-transform:uppercase;color:#d8c58f;margin-bottom:5px}
  .sdc-fix-copy{font-size:.8rem;line-height:1.52;color:#e4dbca}
  .sdc-ask-btn{display:inline-flex;align-items:center;gap:7px;padding:6px 13px;border-radius:999px;border:1px solid rgba(200,168,75,.44);background:rgba(200,168,75,.08);color:#d9c988;font-size:.62rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;cursor:pointer;transition:all .18s ease;margin-top:auto;align-self:flex-start}
  .sdc-ask-btn:hover{border-color:rgba(200,168,75,.52);background:rgba(200,168,75,.16);color:#f0dda6}
  .sdc-locked-hint{font-size:.75rem;line-height:1.5;color:#9a9082;font-style:italic}
  .sdc-unlock-btn{display:inline-flex;align-items:center;justify-content:center;min-height:32px;padding:6px 14px;border-radius:8px;border:1px solid rgba(200,168,75,.48);background:rgba(200,168,75,.1);color:#d9c988;font-size:.62rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;transition:all .18s ease;align-self:flex-start}
  .sdc-unlock-btn:hover{border-color:rgba(200,168,75,.52);background:rgba(200,168,75,.18)}
  @media(max-width:640px){.score-drivers-grid{grid-template-columns:1fr}}

  /* ── AI Advisor / Confidence ─────────────────────────────────── */
  .ai-advisor-shell{border:1px solid rgba(200,168,75,.24);border-radius:18px;background:linear-gradient(155deg,#1a1610,#0e0c08 72%);padding:20px 22px;box-shadow:0 12px 30px rgba(0,0,0,.28),inset 0 1px 0 rgba(255,255,255,.03);position:relative;overflow:hidden}
  .ai-advisor-shell::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.54),transparent)}
  .ai-advisor-shell::after{content:'';position:absolute;inset:-10% -20% auto -20%;height:140%;background:radial-gradient(ellipse at 50% 0,rgba(200,168,75,.07),transparent 56%);pointer-events:none}
  .ai-advisor-head{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px;margin-bottom:16px}
  .ai-advisor-kicker{font-size:.58rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.78);margin-bottom:5px}
  .ai-advisor-title{font-size:.96rem;font-weight:600;color:#ede8de;line-height:1.25}
  .ai-advisor-desc{margin-top:4px;font-size:.78rem;line-height:1.55;color:#a8a193;max-width:36rem}
  .ai-advisor-open-btn{flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 18px;border-radius:10px;background:#c6a85a;color:#1a1a1a;font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;border:none;cursor:pointer;transition:all .2s ease;box-shadow:0 6px 16px rgba(198,168,90,.2),inset 0 1px 0 rgba(255,255,255,.14)}
  .ai-advisor-open-btn:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(198,168,90,.34),inset 0 1px 0 rgba(255,255,255,.18)}
  .ai-advisor-chips{display:flex;flex-wrap:wrap;gap:8px;position:relative;z-index:1}
  .ai-advisor-chip{display:inline-flex;align-items:center;gap:8px;padding:9px 15px;border-radius:999px;border:1px solid rgba(200,168,75,.22);background:rgba(200,168,75,.07);color:#d9c98a;font-size:.72rem;font-weight:500;letter-spacing:.04em;cursor:pointer;transition:all .18s ease;text-align:left}
  .ai-advisor-chip:hover{border-color:rgba(200,168,75,.5);background:rgba(200,168,75,.16);color:#f0e0a6;transform:translateY(-1px)}
  .ai-chip-icon{width:18px;height:18px;border-radius:50%;background:rgba(200,168,75,.14);border:1px solid rgba(200,168,75,.22);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0}
  @media(max-width:640px){.ai-advisor-chips{flex-direction:column}.ai-advisor-chip{width:100%}.ai-advisor-head{flex-direction:column;align-items:flex-start}}

  /* ── Executive Context Bar ───────────────────────────────────────── */
  .exec-ctx-bar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;padding:10px 16px;border:1px solid rgba(200,168,75,.16);border-radius:12px;background:rgba(0,0,0,.28);margin-bottom:22px}
  .exec-ctx-domain{font-size:.95rem;font-weight:700;color:#f2edd8;letter-spacing:-.01em;display:flex;align-items:center;gap:8px}
  .exec-ctx-live{display:inline-block;width:7px;height:7px;border-radius:50%;background:#c8a84b;animation:pibPulse 2s ease-in-out infinite;flex-shrink:0}
  .exec-ctx-brand{color:#948c7c;font-size:.84rem;font-weight:400;margin-right:2px}
  .exec-ctx-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
  .exec-ctx-pill{font-size:.56rem;letter-spacing:.14em;text-transform:uppercase;color:#9a9082;padding:3px 8px;border-radius:6px;background:rgba(200,168,75,.06);border:1px solid rgba(200,168,75,.12)}
  .exec-ctx-btn{display:inline-flex;align-items:center;gap:6px;min-height:32px;padding:0 14px;border-radius:8px;background:#c6a85a;color:#1a1a1a;text-decoration:none;font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;transition:all .18s ease;box-shadow:0 4px 12px rgba(198,168,90,.18)}
  .exec-ctx-btn:hover{transform:translateY(-1px);filter:brightness(1.08)}
  .exec-ctx-btn-outline{display:inline-flex;align-items:center;gap:6px;min-height:32px;padding:0 14px;border-radius:8px;border:1px solid rgba(200,168,75,.42);background:transparent;color:rgba(200,168,75,.9);text-decoration:none;font-size:.62rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;transition:all .18s ease}
  .exec-ctx-btn-outline:hover{border-color:rgba(200,168,75,.5);background:rgba(200,168,75,.08)}

  /* ── Executive Hero ──────────────────────────────────────────────── */
  .exec-hero-shell{border:1px solid rgba(200,168,75,.26);border-radius:22px;background:linear-gradient(145deg,rgba(28,22,12,.98),rgba(10,9,7,.99) 66%),radial-gradient(circle at 8% 22%,rgba(200,168,75,.12),transparent 28%);padding:28px 28px 24px;box-shadow:0 24px 52px rgba(0,0,0,.44),0 0 0 1px rgba(200,168,75,.1) inset;position:relative;overflow:hidden}
  .exec-hero-shell::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.72),transparent)}
  .exec-hero-shell::after{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 50% -10%,rgba(200,168,75,.08),transparent 58%);pointer-events:none}
  .exec-hero-grid{display:grid;grid-template-columns:auto 1fr auto;gap:28px;align-items:start;position:relative;z-index:1}
  @media(max-width:840px){.exec-hero-grid{grid-template-columns:auto 1fr;gap:20px}.exec-action-col{grid-column:1/-1}.exec-bottleneck-block{margin-top:4px}}
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
  .nm-card.nm-secondary{opacity:.72}
  .nm-card-kicker{font-size:.54rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.62)}
  .nm-card-title{font-size:.9rem;font-weight:600;color:#ece3cc;line-height:1.45}
  .nm-card-rationale{font-size:.78rem;line-height:1.55;color:#9a9080}
  .nm-card-action{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;border:1px solid rgba(200,168,75,.46);background:rgba(200,168,75,.09);color:#d9c988;font-size:.62rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;align-self:flex-start;transition:all .18s ease;margin-top:auto}
  .nm-card-action:hover{border-color:rgba(200,168,75,.54);background:rgba(200,168,75,.17)}
  .nm-card-action-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;border:1px solid rgba(200,168,75,.46);background:rgba(200,168,75,.09);color:#d9c988;font-size:.62rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;align-self:flex-start;transition:all .18s ease;margin-top:auto}
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
  .plan-view-report-btn{display:inline-flex;align-items:center;gap:6px;min-height:34px;padding:0 14px;border-radius:8px;border:1px solid rgba(106,175,144,.48);background:rgba(106,175,144,.1);color:#9fd4b8;font-size:.62rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;transition:all .18s ease;margin-top:14px}
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
  .plan-consult-btn{display:inline-flex;align-items:center;gap:6px;min-height:34px;padding:0 14px;border-radius:8px;border:1px solid rgba(200,168,75,.38);background:transparent;color:rgba(200,168,75,.88);text-decoration:none;font-size:.62rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;transition:all .18s ease}
  .plan-consult-btn:hover{border-color:rgba(200,168,75,.42);background:rgba(200,168,75,.06)}

  /* Final pre-live readability refinements + chart-ready style tokens */
  .section-head p,
  .view-clarity-note,
  .report-readout-head p:first-child,
  .report-lead-meta p:first-child,
  .report-lead-meta p:last-child,
  .report-list-card h3,
  .report-list-item p,
  .report-list-item span,
  .report-list-item a,
  .onboarding-command-kicker,
  .onboarding-command-footnote,
  .onboarding-explainer summary,
  .onboarding-proof-item p:first-child,
  .ia-progress-head p,
  .ia-level-card p:first-child,
  .ia-level-unlocks p,
  .hero-overline,
  .hero-panel-label,
  .hero-score-caption,
  .dash-section-label,
  .dash-section-subhead,
  .nm-card-kicker,
  .nm-card-rationale,
  .plan-current-kicker,
  .plan-current-desc,
  .plan-next-kicker,
  .plan-next-desc,
  .plan-all-unlocked-kicker,
  .plan-all-unlocked-desc,
  .plan-consult-copy,
  .plan-consult-btn,
  .nm-card-action,
  .nm-card-action-btn{
    font-size:max(.8rem, 12px);
    line-height:1.55;
  }

  .chart-surface{
    border:1px solid rgba(200,168,75,.18);
    border-radius:16px;
    background:linear-gradient(155deg,#151109,#0d0b07 72%);
    box-shadow:0 14px 30px rgba(0,0,0,.28), inset 0 1px 0 rgba(255,255,255,.03);
    padding:16px;
  }
  .chart-surface-title{
    font-size:.84rem;
    letter-spacing:.14em;
    text-transform:uppercase;
    color:rgba(214,181,95,.86);
    margin-bottom:10px;
  }
  .chart-surface-caption{
    font-size:.8rem;
    line-height:1.5;
    color:#c8bca3;
    margin-top:10px;
  }

  /* ── L2: Signal Analysis ─────────────────────────────────────────── */
  .signal-analysis-shell{border:1px solid rgba(100,140,220,.28);border-radius:20px;background:linear-gradient(155deg,#0e1120,#090b14 72%);padding:22px;box-shadow:0 18px 38px rgba(0,0,0,.34),inset 0 1px 0 rgba(255,255,255,.03);position:relative;overflow:hidden}
  .signal-analysis-shell::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(100,140,220,.52),transparent)}
  .signal-analysis-shell::after{content:'';position:absolute;inset:-10% -20% auto -20%;height:140%;background:radial-gradient(ellipse at 50% 0,rgba(100,140,220,.07),transparent 56%);pointer-events:none}
  .sa-kicker{font-size:.58rem;letter-spacing:.26em;text-transform:uppercase;color:rgba(100,160,240,.72);margin-bottom:5px}
  .sa-heading{font-size:1rem;font-weight:600;color:#d8e8ff;line-height:1.3;margin-bottom:4px}
  .sa-subhead{font-size:.82rem;line-height:1.58;color:#8898b0;max-width:44rem;margin-bottom:18px}
  .sa-category-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;margin-bottom:18px}
  .sa-cat-card{border:1px solid rgba(100,140,220,.18);border-radius:14px;background:linear-gradient(155deg,#0f1428,#0a0d1a 70%);padding:14px;display:flex;flex-direction:column;gap:8px;transition:transform .2s ease,border-color .2s ease}
  .sa-cat-card:hover{transform:translateY(-2px);border-color:rgba(100,140,220,.3)}
  .sa-cat-head{display:flex;align-items:center;justify-content:space-between;gap:8px}
  .sa-cat-name{font-size:.78rem;font-weight:600;color:#c8d8f0;letter-spacing:.02em}
  .sa-cat-score{font-size:.68rem;font-weight:700;padding:2px 8px;border-radius:999px}
  .sa-cat-score.good{background:rgba(106,175,144,.14);border:1px solid rgba(106,175,144,.28);color:#8ec8ac}
  .sa-cat-score.warn{background:rgba(214,163,55,.12);border:1px solid rgba(214,163,55,.28);color:#d9c47a}
  .sa-cat-score.bad{background:rgba(196,80,80,.12);border:1px solid rgba(196,80,80,.26);color:#d47878}
  .sa-cat-bar-track{height:4px;border-radius:3px;background:rgba(255,255,255,.06);overflow:hidden}
  .sa-cat-bar-fill{height:100%;border-radius:3px;transition:width .6s ease}
  .sa-cat-bar-fill.good{background:linear-gradient(90deg,#3a9a78,#6aaf90)}
  .sa-cat-bar-fill.warn{background:linear-gradient(90deg,#c8970a,#e8c458)}
  .sa-cat-bar-fill.bad{background:linear-gradient(90deg,#b43030,#e04848)}
  .sa-cat-issues{display:flex;flex-direction:column;gap:5px;margin-top:2px}
  .sa-cat-issue{font-size:.72rem;line-height:1.45;color:#9ab0cc;display:flex;align-items:flex-start;gap:6px}
  .sa-cat-issue-dot{flex-shrink:0;margin-top:5px;width:5px;height:5px;border-radius:50%;background:rgba(196,90,90,.6)}
  .sa-cat-pass{font-size:.68rem;color:#5a8868;display:flex;align-items:center;gap:5px}
  .sa-summary-row{display:flex;flex-wrap:wrap;gap:10px;padding:12px 14px;border:1px solid rgba(100,140,220,.14);border-radius:12px;background:rgba(100,140,220,.04)}
  .sa-summary-stat{display:flex;flex-direction:column;align-items:center;gap:2px}
  .sa-summary-stat-num{font-size:1.4rem;font-weight:700;color:#a0c0f0}
  .sa-summary-stat-label{font-size:.54rem;letter-spacing:.16em;text-transform:uppercase;color:#6888aa}
  .sa-upsell-banner{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;margin-top:16px;padding:14px 16px;border:1px solid rgba(200,168,75,.38);border-radius:12px;background:rgba(200,168,75,.08);box-shadow:0 2px 12px rgba(200,168,75,.06)}
  .sa-upsell-text{font-size:.8rem;color:#c8b880;line-height:1.45}
  .sa-upsell-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:9px;border:1px solid rgba(200,168,75,.44);background:rgba(200,168,75,.1);color:#d9c988;font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;white-space:nowrap;transition:all .18s ease}
  .sa-upsell-btn:hover{border-color:rgba(200,168,75,.52);background:rgba(200,168,75,.18)}
  @media(max-width:640px){.sa-category-grid{grid-template-columns:1fr}}

  /* ── L3: Action Plan ─────────────────────────────────────────────── */
  .action-plan-shell{border:1px solid rgba(90,160,110,.28);border-radius:20px;background:linear-gradient(155deg,#0c1a10,#080f0a 72%);padding:22px;box-shadow:0 18px 38px rgba(0,0,0,.34),inset 0 1px 0 rgba(255,255,255,.03);position:relative;overflow:hidden}
  .action-plan-shell::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(90,160,110,.44),transparent)}
  .action-plan-shell::after{content:'';position:absolute;inset:-10% -20% auto -20%;height:140%;background:radial-gradient(ellipse at 50% 0,rgba(90,160,110,.06),transparent 56%);pointer-events:none}
  .ap-kicker{font-size:.58rem;letter-spacing:.26em;text-transform:uppercase;color:rgba(100,190,130,.72);margin-bottom:5px}
  .ap-heading{font-size:1rem;font-weight:600;color:#cce8d4;line-height:1.3;margin-bottom:4px}
  .ap-subhead{font-size:.82rem;line-height:1.58;color:#7a9a84;max-width:44rem;margin-bottom:18px}
  .ap-list{display:flex;flex-direction:column;gap:10px}
  .ap-item{border:1px solid rgba(90,160,110,.16);border-radius:14px;background:linear-gradient(155deg,#101c14,#090e0b 70%);padding:14px 16px;display:flex;gap:14px;transition:border-color .2s ease}
  .ap-item:hover{border-color:rgba(90,160,110,.28)}
  .ap-item-rank{flex-shrink:0;display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;background:rgba(90,160,110,.12);border:1px solid rgba(90,160,110,.26);font-size:.65rem;font-weight:700;color:rgba(130,200,150,.8);margin-top:2px}
  .ap-item-body{flex:1;min-width:0;display:flex;flex-direction:column;gap:7px}
  .ap-item-head{display:flex;align-items:center;flex-wrap:wrap;gap:8px}
  .ap-item-title{font-size:.86rem;font-weight:600;color:#c8e0cc;line-height:1.35}
  .ap-item-tier{font-size:.5rem;letter-spacing:.12em;text-transform:uppercase;padding:2px 8px;border-radius:999px;background:rgba(90,160,110,.1);border:1px solid rgba(90,160,110,.22);color:rgba(120,190,140,.7)}
  .ap-item-why{font-size:.76rem;line-height:1.52;color:#7a9884}
  .ap-item-fix{border-left:2px solid rgba(90,160,110,.24);padding:8px 12px;background:rgba(90,160,110,.05);border-radius:0 8px 8px 0}
  .ap-item-fix-label{font-size:.5rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(100,190,130,.6);margin-bottom:4px}
  .ap-item-fix-copy{font-size:.78rem;line-height:1.5;color:#b0c8b8}
  .ap-upsell-banner{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;margin-top:16px;padding:14px 16px;border:1px solid rgba(200,168,75,.22);border-radius:12px;background:rgba(200,168,75,.05)}
  .ap-upsell-text{font-size:.8rem;color:#c8b880;line-height:1.45}
  .ap-upsell-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:9px;border:1px solid rgba(200,168,75,.44);background:rgba(200,168,75,.1);color:#d9c988;font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;white-space:nowrap;transition:all .18s ease}
  .ap-upsell-btn:hover{border-color:rgba(200,168,75,.52);background:rgba(200,168,75,.18)}

  /* ── L4: Guided Execution ────────────────────────────────────────── */
  .guided-exec-shell{border:1px solid rgba(160,120,220,.28);border-radius:20px;background:linear-gradient(155deg,#130e1e,#0b0910 72%);padding:22px;box-shadow:0 18px 38px rgba(0,0,0,.34),inset 0 1px 0 rgba(255,255,255,.03);position:relative;overflow:hidden}
  .guided-exec-shell::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(160,120,220,.44),transparent)}
  .guided-exec-shell::after{content:'';position:absolute;inset:-10% -20% auto -20%;height:140%;background:radial-gradient(ellipse at 50% 0,rgba(160,120,220,.06),transparent 56%);pointer-events:none}
  .ge-kicker{font-size:.58rem;letter-spacing:.26em;text-transform:uppercase;color:rgba(180,140,240,.72);margin-bottom:5px}
  .ge-heading{font-size:1rem;font-weight:600;color:#e0d0f8;line-height:1.3;margin-bottom:4px}
  .ge-subhead{font-size:.82rem;line-height:1.58;color:#8878a8;max-width:44rem;margin-bottom:16px}
  .ge-progress-bar-track{height:6px;border-radius:4px;background:rgba(255,255,255,.06);overflow:hidden;margin-bottom:6px}
  .ge-progress-bar-fill{height:100%;border-radius:4px;background:linear-gradient(90deg,#7a44c8,#a870f0);transition:width .6s cubic-bezier(.4,0,.2,1)}
  .ge-progress-label{font-size:.62rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(180,140,240,.58);margin-bottom:16px}
  .ge-list{display:flex;flex-direction:column;gap:8px}
  .ge-item{border:1px solid rgba(160,120,220,.14);border-radius:12px;background:linear-gradient(155deg,#15102a,#0e0b18 70%);padding:12px 14px;display:flex;gap:12px;align-items:flex-start;transition:border-color .2s ease,background .2s ease;cursor:pointer}
  .ge-item.is-done{opacity:.6;border-color:rgba(160,120,220,.08);background:linear-gradient(155deg,#0f0e1a,#0a0912 70%)}
  .ge-item:hover:not(.is-done){border-color:rgba(160,120,220,.26)}
  .ge-item-check{flex-shrink:0;width:20px;height:20px;border-radius:5px;border:1.5px solid rgba(160,120,220,.4);background:rgba(160,120,220,.06);display:flex;align-items:center;justify-content:center;margin-top:2px;transition:all .15s ease}
  .ge-item.is-done .ge-item-check{background:rgba(160,120,220,.24);border-color:rgba(160,120,220,.5)}
  .ge-item-body{flex:1;min-width:0;display:flex;flex-direction:column;gap:4px}
  .ge-item-title{font-size:.84rem;font-weight:600;color:#d8c8f0;line-height:1.35;transition:color .15s ease}
  .ge-item.is-done .ge-item-title{color:#7a6898;text-decoration:line-through}
  .ge-item-tier{font-size:.5rem;letter-spacing:.12em;text-transform:uppercase;padding:1px 7px;border-radius:999px;background:rgba(160,120,220,.1);border:1px solid rgba(160,120,220,.2);color:rgba(180,140,240,.6)}
  .ge-item-why{font-size:.73rem;line-height:1.48;color:#7868a0;margin-top:2px}
  .ge-item-fix{font-size:.74rem;line-height:1.48;color:#c0b0e0;margin-top:4px}
  .ge-reset-btn{display:inline-flex;align-items:center;gap:6px;margin-top:16px;padding:6px 14px;border-radius:8px;border:1px solid rgba(160,120,220,.22);background:transparent;color:rgba(180,140,240,.5);font-size:.58rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;transition:all .18s ease}
  .ge-reset-btn:hover{border-color:rgba(160,120,220,.36);color:rgba(180,140,240,.8)}
  /* ── Phase 9: Completion + Momentum ─────────────────────────────── */
  @keyframes ge-check-pop{0%{transform:scale(0)}60%{transform:scale(1.3)}100%{transform:scale(1)}}
  @keyframes ge-item-glow{0%{box-shadow:none}35%{box-shadow:0 0 0 3px rgba(168,112,240,.28),inset 0 0 8px rgba(168,112,240,.06)}100%{box-shadow:none}}
  @keyframes ge-msg-out{to{opacity:0}}
  .ge-item.just-done .ge-check-icon{animation:ge-check-pop .25s ease forwards}
  .ge-item.just-done{animation:ge-item-glow .7s ease forwards}
  .ge-milestone-msg{font-size:.72rem;color:rgba(180,140,240,.7);font-style:italic;letter-spacing:.02em;min-height:1.1em;margin:0 0 12px;line-height:1.5;opacity:0;transition:opacity .3s ease}
  .ge-milestone-msg.is-visible{opacity:1}
  .ge-milestone-msg.is-fading{animation:ge-msg-out .42s ease forwards}
  .ge-completion-nudge{display:none;margin-top:20px;padding:18px 20px;border:1px solid rgba(200,168,75,.28);border-radius:14px;background:linear-gradient(140deg,rgba(25,19,8,.98),rgba(10,8,4,.99));text-align:center}
  .ge-completion-nudge.is-visible{display:block}
  .ge-cn-hed{font-size:.92rem;font-weight:600;color:rgba(200,168,75,.9);margin:0 0 6px}
  .ge-cn-body{font-size:.74rem;color:rgba(200,168,75,.55);margin:0 0 14px;line-height:1.5}
  .ge-cn-cta{display:inline-block;padding:9px 22px;border-radius:10px;border:1px solid rgba(200,168,75,.44);background:rgba(200,168,75,.1);color:#d9c988;font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;transition:all .18s ease}
  .ge-cn-cta:hover{background:rgba(200,168,75,.2);border-color:rgba(200,168,75,.6)}
  /* ── L4: Consultation nudge ─────────────────────────────────────── */
  .ge-upsell-banner{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;margin-top:16px;padding:14px 16px;border:1px solid rgba(200,168,75,.22);border-radius:12px;background:rgba(200,168,75,.05)}
  .ge-upsell-text{font-size:.8rem;color:#c8b880;line-height:1.45}
  .ge-upsell-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:9px;border:1px solid rgba(200,168,75,.44);background:rgba(200,168,75,.1);color:#d9c988;font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;white-space:nowrap;transition:all .18s ease}
  .ge-upsell-btn:hover{border-color:rgba(200,168,75,.52);background:rgba(200,168,75,.18)}
  /* ── Progress strip ─────────────────────────────────────────────── */
  .dcm-progress-strip{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px;margin-bottom:10px;padding:8px 14px;border:1px solid rgba(200,168,75,.1);border-radius:10px;background:rgba(200,168,75,.03)}
  .dcm-progress-steps{display:flex;align-items:center;gap:0}
  .dcm-progress-step{display:flex;align-items:center;gap:5px}
  .dcm-ps-dot{width:18px;height:18px;border-radius:50%;border:1.5px solid rgba(200,168,75,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;background:transparent}
  .dcm-progress-step.is-done .dcm-ps-dot{background:rgba(200,168,75,.18);border-color:rgba(200,168,75,.5);color:rgba(200,168,75,.9)}
  .dcm-progress-step.is-current .dcm-ps-dot{background:rgba(200,168,75,.1);border-color:rgba(200,168,75,.6);box-shadow:0 0 0 3px rgba(200,168,75,.1)}
  .dcm-progress-step.is-ahead .dcm-ps-dot{opacity:.35}
  .dcm-ps-label{font-size:.54rem;letter-spacing:.1em;text-transform:uppercase;color:rgba(200,168,75,.55);white-space:nowrap}
  .dcm-progress-step.is-done .dcm-ps-label{color:rgba(200,168,75,.72)}
  .dcm-progress-step.is-current .dcm-ps-label{color:rgba(200,168,75,.92);font-weight:600}
  .dcm-progress-step.is-ahead .dcm-ps-label{opacity:.4}
  .dcm-ps-line{width:20px;height:1px;background:rgba(200,168,75,.15);margin:0 4px;flex-shrink:0}
  .dcm-ps-line.is-done{background:rgba(200,168,75,.4)}
  .dcm-progress-pct{font-size:.6rem;letter-spacing:.1em;color:rgba(200,168,75,.48);white-space:nowrap}
  @media(max-width:520px){.dcm-ps-label{display:none}.dcm-ps-line{width:12px}.dcm-progress-strip{padding:7px 10px}}
  /* ── Next-move urgency line ─────────────────────────────────────── */
  .nm-urgency{font-size:.7rem;color:rgba(200,168,75,.5);letter-spacing:.04em;margin:2px 0 0;font-style:italic}
  /* ── Return Banner ──────────────────────────────────────────── */
  .dcm-return-banner{position:relative;margin-bottom:16px;padding:16px 40px 16px 16px;border:1px solid rgba(200,168,75,.25);border-radius:14px;background:linear-gradient(140deg,rgba(22,17,9,.98),rgba(10,8,5,.99))}
  .dcm-rb-dismiss{position:absolute;top:10px;right:12px;background:none;border:none;cursor:pointer;color:rgba(200,168,75,.4);font-size:1.1rem;padding:0;line-height:1;transition:color .15s}
  .dcm-rb-dismiss:hover{color:rgba(200,168,75,.75)}
  .dcm-rb-sub{font-size:.62rem;letter-spacing:.1em;text-transform:uppercase;color:rgba(200,168,75,.52);margin:0 0 4px}
  .dcm-rb-hed{font-size:.88rem;font-weight:600;color:rgba(200,168,75,.88);margin:0 0 4px}
  .dcm-rb-body{font-size:.72rem;color:rgba(200,168,75,.54);margin:0 0 12px;line-height:1.5}
  .dcm-rb-cta{display:inline-block;font-size:.72rem;font-weight:600;color:rgba(200,168,75,.85);border:1px solid rgba(200,168,75,.28);border-radius:8px;padding:5px 12px;text-decoration:none;transition:border-color .15s,color .15s}
  .dcm-rb-cta:hover{color:#c8a84b;border-color:rgba(200,168,75,.5)}
  /* ── Layer nudges (JS-revealed after 12h away) ───────────────── */
  .dcm-layer-nudge{font-size:.72rem;color:rgba(200,168,75,.42);font-style:italic;margin:6px 0 0;line-height:1.5;display:none}
</style>
@endpush

<div class="min-h-screen bg-[#090805] text-[#ede8de]">
  <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">

    @if(session('status'))
    <div class="mb-4 rounded-xl border border-[rgba(200,168,75,0.25)] bg-[rgba(200,168,75,0.08)] px-4 py-3" role="status">
      <p class="text-sm text-[#e9debe]">{{ session('status') }}</p>
    </div>
    @endif

    @if(session('system_entry'))
      @php
        $entryLabel = ucwords(str_replace('-', ' ', (string) session('system_entry')));
      @endphp
    <div class="mb-4 rounded-xl border border-[rgba(106,175,144,0.30)] bg-[rgba(106,175,144,0.09)] px-4 py-3" role="status">
      <p class="text-sm text-[#d9eee5]">Unlock confirmed: {{ $entryLabel }} is now active on your account.</p>
    </div>
    @endif

    @if(session('scan_saved'))
    <div class="mb-6 flex items-center gap-3 rounded-xl border border-[rgba(106,175,144,0.32)] bg-[rgba(106,175,144,0.08)] px-4 py-3" role="alert">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm-1.5 11.5-3-3L4.9 7l1.6 1.6 3.6-3.6 1.4 1.4-5 5z" fill="#6aaf90"/></svg>
      <p class="text-sm text-[#d9eee5]">{{ session('scan_saved') }}</p>
    </div>
    @endif

    {{-- Checkout resume banner (user backed out of Stripe from dashboard) --}}
    @if(request()->query('checkout_resumed'))
    <div id="checkout-resume-banner" role="status" style="display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border:1px solid rgba(200,168,75,.28);border-radius:12px;background:linear-gradient(140deg,rgba(28,22,12,.97),rgba(10,8,5,.98));margin-bottom:16px">
      <span style="flex-shrink:0;width:20px;height:20px;border-radius:50%;background:rgba(200,168,75,.12);border:1px solid rgba(200,168,75,.3);display:flex;align-items:center;justify-content:center;margin-top:1px">
        <svg width="9" height="9" viewBox="0 0 9 9" fill="none" aria-hidden="true"><circle cx="4.5" cy="4.5" r="3.5" stroke="rgba(200,168,75,0.8)" stroke-width="1.2"/><path d="M4.5 2.5v2.2l1.3 1.3" stroke="rgba(200,168,75,0.8)" stroke-width="1.1" stroke-linecap="round"/></svg>
      </span>
      <div style="flex:1;min-width:0">
        <p style="font-size:.72rem;font-weight:600;color:rgba(200,168,75,.92);margin-bottom:3px">You paused checkout — your system is still waiting.</p>
        <p style="font-size:.65rem;color:rgba(200,168,75,.58);line-height:1.5">Resume your next unlock when ready. Your progress and domain context are preserved.</p>
      </div>
      <button type="button" aria-label="Dismiss" onclick="this.closest('#checkout-resume-banner').remove()" style="flex-shrink:0;background:none;border:none;cursor:pointer;color:rgba(200,168,75,.4);font-size:1rem;padding:0;line-height:1;margin-top:1px">&times;</button>
    </div>
    @endif

    @if($hasSystem)

    {{-- Return Banner (JS-controlled — shown on return visits with incomplete tier) --}}
    @if($tierRank < 4)
    <script>
// ── Signal Tracker ────────────────────────────────────────────────────
(function(){
  'use strict';
  var LOG_KEY   = 'seo_event_log';
  var SES_KEY   = 'seo_session_start';
  var LAST_KEY  = 'seo_last_cta';
  var INT_KEY   = 'seo_last_interaction';
  var MAX       = 200;

  if (!sessionStorage.getItem(SES_KEY)) {
    try { sessionStorage.setItem(SES_KEY, String(Date.now())); } catch(e) {}
  }

  window.track = function(event, meta) {
    var payload = Object.assign({ event: event, ts: Date.now(), page: 'dashboard', tier_rank: {{ $tierRank }} }, meta || {});
    console.log('[TRACK]', event, payload);
    try {
      var log = JSON.parse(localStorage.getItem(LOG_KEY) || '[]');
      log.push(payload);
      if (log.length > MAX) log = log.slice(-MAX);
      localStorage.setItem(LOG_KEY, JSON.stringify(log));
      localStorage.setItem(INT_KEY, String(payload.ts));
      if (event === 'cta_click') localStorage.setItem(LAST_KEY, JSON.stringify(payload));
    } catch(e) {}
  };

  // ── Section view tracking (L2 / L3 / L4) ──────────────────────────
  if (typeof IntersectionObserver !== 'undefined') {
    var sectionTrackMap = {
      'signal-analysis':  { tier: 99,  label: 'signal_analysis' },
      'action-plan':      { tier: 249, label: 'action_plan' },
      'guided-execution': { tier: 489, label: 'guided_execution' },
    };
    var seen = {};
    var io = new IntersectionObserver(function(entries) {
      entries.forEach(function(e) {
        if (!e.isIntersecting) return;
        var id = e.target.id;
        if (id && sectionTrackMap[id] && !seen[id]) {
          seen[id] = true;
          window.track('section_view', sectionTrackMap[id]);
        }
      });
    }, { threshold: 0.25 });
    ['signal-analysis', 'action-plan', 'guided-execution'].forEach(function(id) {
      var el = document.getElementById(id);
      if (el) io.observe(el);
    });
  }
})();
</script>

<div id="dcm-return-banner" class="dcm-return-banner" role="alert" aria-live="polite" style="display:none">
      <button type="button" id="dcm-rb-dismiss-btn" class="dcm-rb-dismiss" aria-label="Dismiss">&times;</button>
      <p id="dcm-rb-sub" class="dcm-rb-sub"></p>
      <p class="dcm-rb-hed">Pick up where you left off</p>
      <p id="dcm-rb-body" class="dcm-rb-body"></p>
      <a id="dcm-rb-cta" href="{{ $nextUnlockHref }}" class="dcm-rb-cta">Continue &rarr;</a>
    </div>
    @endif

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

    <div class="dashboard-primary-flow {{ $isScansView ? 'is-scans-view' : '' }} {{ $isReportsView ? 'is-reports-view' : '' }}">

    {{-- ── System Progress Strip ──────────────────────────────────── --}}
    @if($isSystemView)
    @php
      $progressLabels = ['Scan', 'Signal Analysis', 'Action Plan', 'Guided Execution'];
      $progressPct    = min(100, $tierRank * 25);
    @endphp
    <div class="dcm-progress-strip" aria-label="System progress: step {{ $tierRank }} of 4">
      <div class="dcm-progress-steps">
        @foreach($progressLabels as $pidx => $plbl)
        <div class="dcm-progress-step {{ $pidx < $tierRank ? 'is-done' : ($pidx === $tierRank - 1 ? 'is-current' : 'is-ahead') }}">
          <div class="dcm-ps-dot">
            @if($pidx < $tierRank)
            <svg width="8" height="8" viewBox="0 0 8 8" fill="none" aria-hidden="true"><path d="M1 4l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @endif
          </div>
          <span class="dcm-ps-label">{{ $plbl }}</span>
        </div>
        @if(!$loop->last)
        <div class="dcm-ps-line {{ $pidx < $tierRank - 1 ? 'is-done' : '' }}" aria-hidden="true"></div>
        @endif
        @endforeach
      </div>
      <p class="dcm-progress-pct">{{ $progressPct }}% through your visibility system</p>
    </div>
    @endif
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
    </section>

        @if($isSystemView)

    @php
      $planLevels = [
        ['key' => 'scan-basic',          'num' => 1, 'kicker' => 'Step 1', 'name' => 'Baseline Score',
          'desc'     => 'Your starting visibility score, your top issue, and your first clear fix.',
          'included' => ['Visibility score (0–100)', 'Site standing: Strong / Average / At Risk', 'Top issue identified', 'Fastest fix recommendation'],
          'why'      => 'Know where you stand and exactly what to fix first.',
          'lift' => '+12 pts', 'price' => '$2'],
        ['key' => 'signal-expansion',    'num' => 2, 'kicker' => 'Step 2', 'name' => 'Signal Analysis',
          'desc'     => 'Your score broken into the signals that actually matter — where structure is weak and what\'s holding you back most.',
          'included' => ['Visibility score by category', 'Where your structure is weak', 'Where competitors are outranking you', 'What\'s holding you back most'],
          'why'      => 'Understand exactly why your visibility is limited — and where things are breaking down.',
          'lift' => '+18 pts', 'price' => '$99'],
        ['key' => 'structural-leverage', 'num' => 3, 'kicker' => 'Step 3', 'name' => 'Action Plan',
          'desc'     => 'A prioritized fix list built from your scan — highest-impact issues ranked first, with clear instructions for each.',
          'included' => ['Step-by-step fix roadmap from your scan', 'Highest-impact issues ranked first', 'Clear fix instructions per issue', 'Structured path to improve visibility fast'],
          'why'      => 'Know exactly what to fix — and in what order. This is not generic advice.',
          'lift' => '+22 pts', 'price' => '$249'],
        ['key' => 'system-activation',   'num' => 4, 'kicker' => 'Step 4', 'name' => 'Guided Execution',
          'desc'     => 'An interactive checklist inside your dashboard — follow guided steps and track progress as you complete each fix.',
          'included' => ['Interactive checklist tied to your action plan', 'Progress tracking as you complete each step', 'Structured execution flow (no guesswork)', 'Clear path from fixes → real visibility improvements'],
          'why'      => 'Turn your plan into a working system. You don\'t just know what to do — you actually get it done.',
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
      $nextLevelCheckoutHref = $nextLevelMeta && isset($checkoutRoutes[$nextLevelMeta['key']]) && \Route::has($checkoutRoutes[$nextLevelMeta['key']])
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
            // When unlocked, the route is 'dashboard.scans.show' which requires a {scan} param.
            // Use $leadReportHref (already built with the correct scan key) instead of calling route() bare.
            $findingUnlockHref = $findingIsUnlocked
              ? $leadReportHref
              : (($findingRouteKey && $findingRouteKey !== 'dashboard.scans.show' && \Route::has($findingRouteKey)) ? route($findingRouteKey) : $nextUnlockHref);
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
        @php
          $nmSubtext = match(true) {
            $tierRank >= 4 => 'You\'ve got the execution checklist. Want this implemented for you?',
            $tierRank === 3 => 'You\'ve got the plan. Now execute it \u2014 step by step inside your dashboard.',
            $tierRank === 2 => 'You\'ve identified the gaps. Now fix them in order.',
            $tierRank === 1 && $latestIssues > 0 => 'Your scan found '.$latestIssues.' gap'.($latestIssues !== 1 ? 's' : '').'. This is the next step to fix them.',
            default => 'Your baseline is set. Signal Analysis breaks down exactly where you\'re losing visibility.',
          };
          $nmCtaLabel = match(true) {
            $tierRank >= 4 => 'Book Strategy Session',
            $tierRank === 3 => 'Start Guided Execution',
            $tierRank === 2 => 'Get My Action Plan',
            default        => 'Unlock Signal Analysis',
          };
        @endphp
        <p class="dash-section-label">Your next move</p>
        <h2 id="next-move-heading" class="dash-section-heading">{{ $nmSubtext }}</h2>
        <p class="nm-urgency">You\'ve already done the hard part &mdash; most users at Level {{ $tierRank }} move here next.</p>
        <div class="next-move-row">

          {{-- Fastest win: primary when no report yet; secondary when user already has report and an upgrade is the real next step --}}
          <div class="nm-card {{ $leadRenderable ? 'nm-secondary' : 'nm-primary' }}">
            <p class="nm-card-kicker">Fastest win</p>
            <h3 class="nm-card-title">{{ $nextMoveFastestFix }}</h3>
            <p class="nm-card-rationale">The quickest change likely to raise your score before your next scan.</p>
            @if($leadRenderable)
            <a href="{{ $leadReportHref }}" class="nm-card-action" onclick="track('cta_click',{tier:'report',label:'view_report_fastest_win',location:'next_move'})">View fix in your report &rarr;</a>
            @else
            <a href="{{ $nextMoveActionHref }}" class="nm-card-action" onclick="track('cta_click',{tier:'next',label:'fastest_win_unlock',location:'next_move'})">{{ $nextUnlockLabel }} &rarr;</a>
            @endif
          </div>

          {{-- Highest leverage --}}
          @if(isset($nextBestAction) && !empty($nextBestAction))
          <div class="nm-card">
            <p class="nm-card-kicker">Highest leverage</p>
            <h3 class="nm-card-title">{{ $nextBestAction['what_missing'] ?? 'Primary visibility gap' }}</h3>
            <p class="nm-card-rationale">{{ !empty($nextBestAction['why_it_matters']) ? $nextBestAction['why_it_matters'] : 'Fixing this has the highest projected impact on your visibility score.' }}</p>
            @if($leadRenderable)
            <a href="{{ $leadReportHref }}" class="nm-card-action" onclick="track('cta_click',{tier:'report',label:'view_report_highest_leverage',location:'next_move'})">See full fix details &rarr;</a>
            @else
            <a href="{{ $nextMoveActionHref }}" class="nm-card-action" onclick="track('cta_click',{tier:'next',label:'highest_leverage_unlock',location:'next_move'})">{{ $nextUnlockLabel }} &rarr;</a>
            @endif
          </div>
          @elseif(isset($nextUpgrade) && !empty($nextUpgrade))
          <div class="nm-card {{ $leadRenderable ? 'nm-primary' : '' }}">
            <p class="nm-card-kicker">Recommended for your level</p>
            <h3 class="nm-card-title">{{ $nextUpgrade['label'] ?? $nextStep }}</h3>
            <p class="nm-card-rationale">{{ $nextUpgrade['description'] ?? 'Unlock the next level to see your highest-impact fixes, ranked by score potential.' }}</p>
            <a href="{{ $nextUnlockHref }}" class="nm-card-action" onclick="track('cta_click',{tier:'next',label:'upgrade_recommended',location:'next_move_upgrade'})">
              {{ $nmCtaLabel }}{{ isset($nextUpgrade['price']) ? ' &mdash; '.$nextUpgrade['price'] : ($nextLevelPrice ? ' &mdash; '.$nextLevelPrice : '') }} &rarr;
            </a>
          </div>
          @elseif($nextStep)
          <div class="nm-card {{ $leadRenderable ? 'nm-primary' : '' }}">
            <p class="nm-card-kicker">Recommended for your level</p>
            <h3 class="nm-card-title">{{ $nextStep }}</h3>
            <p class="nm-card-rationale">The next level reveals more precise fixes and shows exactly which changes will move your score the most.</p>
            <a href="{{ $nextUnlockHref }}" class="nm-card-action" onclick="track('cta_click',{tier:'next',label:'upgrade_next_step',location:'next_move_upgrade'})">
              {{ $nmCtaLabel }}{{ $nextLevelPrice ? ' &mdash; '.$nextLevelPrice : '' }} &rarr;
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
              data-price="{{ $nextLevelMeta['price'] }}"
              onclick="track('cta_click',{tier:{{ $nextLevelMeta['price'] }},label:'{{ $nextLevelMeta['name'] }}',location:'your_plan'})">
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
          $nextCheckHref = (isset($checkoutRoutes[$nextLevelMeta['key']]) && \Route::has($checkoutRoutes[$nextLevelMeta['key']]))
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
          <p class="plan-consult-copy">Your system is built from your scan data. We can deploy, scale, and accelerate what it&rsquo;s already surfaced &mdash; done for you.</p>
          <a href="{{ route('book.index') }}?entry=dashboard-plan" class="plan-consult-btn" onclick="track('cta_click',{tier:'consult',label:'consultation',location:'your_plan'})">Book a Strategy Session &rarr;</a>
        </div>

      </div>
    </section>

    {{-- ── L2: SIGNAL ANALYSIS (unlocked for tierRank >= 2) ─────────── --}}
    @if($tierRank >= 2 && !empty($scanCategories))
    @php
      $saCategories = [];
      $saTotalChecks = 0;
      $saPassCount = 0;
      $saFailCount = 0;
      $categoryLabels = [
        'extractable_content' => 'Extractable Content',
        'schema'              => 'Schema & Entities',
        'coverage'            => 'Topic Coverage',
        'crawlability'        => 'Crawlability',
        'internal_linking'    => 'Internal Linking',
        'authority'           => 'Authority Signals',
      ];
      foreach ($scanCategories as $catKey => $cat) {
        $score   = (int) ($cat['score'] ?? 0);
        $maxScore = (int) ($cat['max_score'] ?? 20);
        $pct     = $maxScore > 0 ? min(100, round($score / $maxScore * 100)) : 0;
        $label   = $categoryLabels[$catKey] ?? ucwords(str_replace('_', ' ', $catKey));
        $failing = [];
        $passing = 0;
        foreach ($cat['checks'] ?? [] as $chk) {
          $saTotalChecks++;
          if ($chk['passed'] ?? false) { $saPassCount++; $passing++; }
          else { $saFailCount++; $failing[] = $chk['label'] ?? $chk['key']; }
        }
        $saCategories[] = compact('label','score','maxScore','pct','failing','passing');
      }
      usort($saCategories, fn($a,$b) => $a['pct'] <=> $b['pct']); // worst first
    @endphp
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="signal-analysis" aria-labelledby="sa-heading">
      <div class="signal-analysis-shell">
        <p class="sa-kicker">Signal Analysis</p>
        <h2 id="sa-heading" class="sa-heading">Where your visibility signals are breaking down</h2>
        <p class="sa-subhead">Your site scored across {{ count($saCategories) }} signal categories. These are the areas where AI systems encounter friction — ranked weakest first.</p>
        <p class="dcm-layer-nudge">Your weakest category is still unresolved</p>

        {{-- Summary stats --}}
        <div class="sa-summary-row mb-4">
          <div class="sa-summary-stat">
            <span class="sa-summary-stat-num">{{ $saPassCount }}</span>
            <span class="sa-summary-stat-label">Signals Passing</span>
          </div>
          <div class="sa-summary-stat">
            <span class="sa-summary-stat-num" style="color:#d47878">{{ $saFailCount }}</span>
            <span class="sa-summary-stat-label">Signals Failing</span>
          </div>
          <div class="sa-summary-stat">
            <span class="sa-summary-stat-num">{{ $saTotalChecks > 0 ? round($saPassCount/$saTotalChecks*100) : 0 }}%</span>
            <span class="sa-summary-stat-label">Pass Rate</span>
          </div>
        </div>

        {{-- Category grid --}}
        <div class="sa-category-grid">
          @foreach($saCategories as $cat)
          @php
            $scoreClass = $cat['pct'] >= 75 ? 'good' : ($cat['pct'] >= 40 ? 'warn' : 'bad');
          @endphp
          <div class="sa-cat-card">
            <div class="sa-cat-head">
              <span class="sa-cat-name">{{ $cat['label'] }}</span>
              <span class="sa-cat-score {{ $scoreClass }}">{{ $cat['pct'] }}%</span>
            </div>
            <div class="sa-cat-bar-track">
              <div class="sa-cat-bar-fill {{ $scoreClass }}" style="width:{{ $cat['pct'] }}%"></div>
            </div>
            @if(count($cat['failing']) > 0)
            <div class="sa-cat-issues">
              @foreach(array_slice($cat['failing'], 0, 3) as $issue)
              <div class="sa-cat-issue">
                <span class="sa-cat-issue-dot"></span>
                {{ $issue }}
              </div>
              @endforeach
              @if(count($cat['failing']) > 3)
              <div class="sa-cat-issue" style="color:#6888a8">+ {{ count($cat['failing']) - 3 }} more</div>
              @endif
            </div>
            @else
            <div class="sa-cat-pass">
              <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1.5 6l3 3 6-6" stroke="#5a9870" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              All checks passing
            </div>
            @endif
          </div>
          @endforeach
        </div>

        {{-- Upsell to L3 if applicable --}}
        @if($tierRank === 2)
        <div class="sa-upsell-banner">
          <span class="sa-upsell-text">You&rsquo;ve seen the gaps. <strong>Action Plan</strong> turns this into a numbered fix list &mdash; what to fix first, in order of impact.</span>
          <a href="{{ route('checkout.structural-leverage') }}" class="sa-upsell-btn" onclick="track('cta_click',{tier:249,label:'action_plan',location:'signal_analysis_upsell'})">Get Your Action Plan &mdash; $249 &rarr;</a>
        </div>
        @endif
      </div>
    </section>
    @endif

    {{-- ── L3: ACTION PLAN (unlocked for tierRank >= 3) ─────────────── --}}
    @if($tierRank >= 3 && !empty($scanIntelligence))
    @php
      $apItems = [];
      foreach ($scanIntelligence as $tierBlock) {
        $blockTier = $tierBlock['tier'] ?? null;
        $blockRank = match ($blockTier) {
          'signal-expansion'   => 2,
          'structural-leverage' => 3,
          'system-activation'  => 4,
          default              => 0,
        };
        if ($blockRank === 0 || $blockRank > $tierRank) continue;
        $tierLabel = $tierBlock['label'] ?? '';
        foreach ($tierBlock['issues'] ?? [] as $issue) {
          $apItems[] = [
            'title'      => $issue['what_missing'] ?? $issue['key'] ?? 'Issue',
            'why'        => $issue['why_it_matters'] ?? '',
            'fix'        => $issue['fix'] ?? '',
            'tier_label' => $tierLabel,
            'rank'       => $blockRank,
          ];
        }
      }
      // Sort: lower tier first (foundational first), deterministic order
      usort($apItems, fn($a,$b) => $a['rank'] <=> $b['rank']);
    @endphp
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="action-plan" aria-labelledby="ap-heading">
      <div class="action-plan-shell">
        <p class="ap-kicker">Action Plan</p>
        <h2 id="ap-heading" class="ap-heading">Your ranked fix list</h2>
        <p class="ap-subhead">{{ count($apItems) }} issue{{ count($apItems) !== 1 ? 's' : '' }} from your scan, ordered by what to fix first. Start at the top — highest-leverage changes for {{ $projectDomain ?? 'your site' }}.</p>
        <p class="dcm-layer-nudge">You haven&rsquo;t completed your highest-priority fix</p>

        <div class="ap-list">
          @foreach($apItems as $apIdx => $apItem)
          <div class="ap-item">
            <div class="ap-item-rank">{{ $apIdx + 1 }}</div>
            <div class="ap-item-body">
              <div class="ap-item-head">
                <span class="ap-item-title">{{ $apItem['title'] }}</span>
                @if($apItem['tier_label'])
                <span class="ap-item-tier">{{ $apItem['tier_label'] }}</span>
                @endif
              </div>
              @if($apItem['why'])
              <p class="ap-item-why">{{ $apItem['why'] }}</p>
              @endif
              @if($apItem['fix'])
              <div class="ap-item-fix">
                <p class="ap-item-fix-label">Fix</p>
                <p class="ap-item-fix-copy">{{ $apItem['fix'] }}</p>
              </div>
              @endif
            </div>
          </div>
          @endforeach
        </div>

        {{-- Upsell to L4 if applicable --}}
        @if($tierRank === 3)
        <div class="ap-upsell-banner">
          <span class="ap-upsell-text">You have the plan — now execute it with structure. <strong>Guided Execution</strong> turns this into a step-by-step checklist with progress tracking inside your dashboard.</span>
          <a href="{{ route('checkout.system-activation') }}" class="ap-upsell-btn" onclick="track('cta_click',{tier:489,label:'guided_execution',location:'action_plan_upsell'})">Start Guided Execution &mdash; $489 &rarr;</a>
        </div>
        @endif
      </div>
    </section>
    @endif

    {{-- ── L4: GUIDED EXECUTION (unlocked for tierRank >= 4) ──────────── --}}
    @if($tierRank >= 4 && !empty($scanIntelligence))
    @php
      $geItems = [];
      foreach ($scanIntelligence as $tierBlock) {
        $blockTier = $tierBlock['tier'] ?? null;
        $blockRank = match ($blockTier) {
          'signal-expansion'   => 2,
          'structural-leverage' => 3,
          'system-activation'  => 4,
          default              => 0,
        };
        if ($blockRank === 0) continue;
        $tierLabel = $tierBlock['label'] ?? '';
        foreach ($tierBlock['issues'] ?? [] as $issue) {
          $geItems[] = [
            'id'         => 'ge-' . md5(($issue['key'] ?? '') . ($latestScanned?->id ?? '')),
            'title'      => $issue['what_missing'] ?? $issue['key'] ?? 'Issue',
            'why'        => $issue['why_it_matters'] ?? '',
            'fix'        => $issue['fix'] ?? '',
            'tier_label' => $tierLabel,
          ];
        }
      }
    @endphp
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="guided-execution" aria-labelledby="ge-heading">
      <div class="guided-exec-shell">
        <p class="ge-kicker">Guided Execution</p>
        <h2 id="ge-heading" class="ge-heading">Your execution checklist</h2>
        <p class="ge-subhead">Work through each fix in order. Check items off as you complete them — your progress is saved in this browser.</p>
        <p class="dcm-layer-nudge">You&rsquo;ve started execution &mdash; keep going</p>

        <div class="ge-progress-bar-track">
          <div class="ge-progress-bar-fill" id="ge-progress-fill" style="width:0%"></div>
        </div>
        <p class="ge-progress-label" id="ge-progress-label">0 of {{ count($geItems) }} complete</p>
        <p id="ge-milestone-msg" class="ge-milestone-msg" aria-live="polite" aria-atomic="true"></p>

        <div class="ge-list" id="ge-checklist">
          @foreach($geItems as $geIdx => $geItem)
          <div class="ge-item" id="ge-item-{{ $geIdx }}" data-ge-id="{{ $geItem['id'] }}" role="button" tabindex="0" aria-pressed="false" onclick="geToggle(this)" onkeydown="if(event.key==='Enter'||event.key===' ')geToggle(this)">
            <div class="ge-item-check" aria-hidden="true">
              <svg width="11" height="11" viewBox="0 0 11 11" fill="none" class="ge-check-icon" style="opacity:0"><path d="M1.5 5.5l3 3 5-5" stroke="#a870f0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="ge-item-body">
              <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <span class="ge-item-title">{{ $geItem['title'] }}</span>
                @if($geItem['tier_label'])
                <span class="ge-item-tier">{{ $geItem['tier_label'] }}</span>
                @endif
              </div>
              @if($geItem['why'])
              <p class="ge-item-why">{{ $geItem['why'] }}</p>
              @endif
              @if($geItem['fix'])
              <p class="ge-item-fix">{{ $geItem['fix'] }}</p>
              @endif
            </div>
          </div>
          @endforeach
        </div>

        <button type="button" class="ge-reset-btn" onclick="geReset()" aria-label="Reset checklist progress">
          <svg width="11" height="11" viewBox="0 0 11 11" fill="none"><path d="M1 5.5A4.5 4.5 0 0 1 9.5 3M9.5 1v2H7.5M10 5.5A4.5 4.5 0 0 1 1.5 8M1.5 10V8h2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Reset progress
        </button>

        {{-- Consultation nudge --}}
        <div class="ge-upsell-banner">
          <span class="ge-upsell-text">Your checklist is built from your scan. We can now deploy, scale, and accelerate the full system &mdash; implemented for you.</span>
          <a href="{{ route('book.index', ['entry' => 'dashboard-upgrade']) }}" class="ge-upsell-btn" onclick="track('cta_click',{tier:'consult',label:'consultation',location:'ge_upsell'})">Book Strategy Session &rarr;</a>
        </div>

        {{-- Phase 9: 100% completion nudge --}}
        <div id="ge-completion-nudge" class="ge-completion-nudge" role="status" aria-live="polite">
          <p class="ge-cn-hed">Ready to take this further?</p>
          <p class="ge-cn-body">Your checklist reveals exactly what your system needs. We can build, deploy, and scale everything it&rsquo;s surfaced &mdash; so you don&rsquo;t have to do it alone.</p>
          <a href="{{ route('book.index', ['entry' => 'dashboard-completion']) }}" class="ge-cn-cta" onclick="track('cta_click',{tier:'consult',label:'consultation_post_completion',location:'ge_completion'})">Book Strategy Session &rarr;</a>
        </div>
      </div>
    </section>
    @endif

    {{-- ── CONFIDENCE: Your AI Advisor ─────────────────────────────── --}}
    @if($leadScore > 0 || $leadRenderable)
    @php
      $userFocusArea = auth()->user()->profile_data['focus_area'] ?? null;
      $userFocusLabel = match ($userFocusArea) {
        'improve_visibility' => 'Improve visibility',
        'expand_markets'     => 'Expand into new markets',
        'generate_leads'     => 'Generate more leads',
        'not_sure'           => 'Balanced overview',
        default              => null,
      };
    @endphp
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="ai-advisor-section" aria-labelledby="ai-advisor-heading">
      <div class="ai-advisor-shell">
        <div class="ai-advisor-head">
          <div>
            <p class="ai-advisor-kicker">Your AI Visibility Advisor</p>
            @if($userFocusLabel)
            <p style="font-size:.7rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(200,168,75,.6);margin:0 0 8px">Your focus: <span style="color:var(--gold,#c8a84b);font-weight:500">{{ $userFocusLabel }}</span></p>
            @endif
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
    @if($isReportsView)
    <section class="system-section mb-8 dash-section-anchor surface-reveal" id="report-readouts">
      <div class="ia-progress-shell">
        <div class="ia-progress-head">
          <div>
            <h2>Your progress path</h2>
            <p>Where you are now, what you unlock next, and how to get there.</p>
          </div>
          <span class="scan-library-pill">Current Level <strong>{{ $currentLevelLabel }}</strong></span>
        </div>
        <div class="ia-level-grid">
          <article class="ia-level-card">
            <p>Where you are</p>
            <p>{{ $currentLevelLabel }}</p>
          </article>
          <article class="ia-level-card">
            <p>What&rsquo;s next</p>
            <p>{{ $nextLevelLabel }}{{ $nextLevelPrice ? (' &middot; ' . $nextLevelPrice) : '' }}</p>
          </article>
        </div>
        <div class="ia-level-unlocks">
          <p>What you unlock next</p>
          <ul>
            @foreach($nextLevelUnlocks as $unlock)
              <li>{{ $unlock }}</li>
            @endforeach
          </ul>
        </div>
        <div class="ia-progress-actions">
          @if($leadRenderable)
            <a href="{{ $leadReportHref }}" class="secondary">View Full Report</a>
          @else
            <span class="secondary" aria-disabled="true">{{ $leadReadoutStatus }}</span>
          @endif
          @if($nextLayer)
            <a href="{{ $nextLevelHref }}" class="primary">Unlock {{ $nextLevelLabel }}{{ $nextLevelPrice ? (' — ' . $nextLevelPrice) : '' }} →</a>
          @endif
        </div>

        @if($showMarketCoverage)
        <div class="ia-level-unlocks" style="margin-top:12px">
          <p>Expand Your Market Coverage</p>
          <ul>
            <li>Programmatic SEO expansion across service and location permutations.</li>
            <li>AI authority system deployment to strengthen retrieval trust.</li>
            <li>Advanced deployment workflows for multi-market growth.</li>
          </ul>
        </div>
        @endif
      </div>
    </section>
    @endif

    @if($isScansView)
    <section class="system-section mb-10 dash-section-anchor surface-reveal" id="scan-history">
      <div class="scan-history-shell">
        <div class="scan-library-header">
          <div>
            <p class="scan-library-kicker">Reports Active</p>
            <h2 class="scan-library-title">Your Reports &amp; Visibility History</h2>
            <p class="scan-library-description">Every scan is stored here with its score, status, and full report. Review your visibility history and open any full report below.</p>
          </div>
          <div class="scan-library-summary-wall">
            <article>
              <p>Recent Scans</p>
              <p>{{ $scanFocusList->count() }} archived</p>
            </article>
            <article>
              <p>Last Scan</p>
              <p>{{ $latestEvaluatedLabel }}</p>
            </article>
            <article>
              <p>Current Status</p>
              <p>{{ $leadState }}</p>
            </article>
            <article>
              <p>Total Scans</p>
              <p>{{ $totalScans }}</p>
            </article>
          </div>
        </div>

        <div class="scan-library-toolbar">
          <span class="scan-library-pill">Recent Scans <strong>{{ $scanFocusList->count() }}</strong></span>
          <span class="scan-library-pill">Total Scans <strong>{{ $totalScans }}</strong></span>
          <span class="scan-library-pill">Status <strong>{{ $leadState }}</strong></span>
          <span class="scan-library-pill">Last Scan <strong>{{ $latestEvaluatedLabel }}</strong></span>
        </div>

        @if($featuredRecentScans->isNotEmpty())
        <div class="scan-shelf">
          <div class="scan-shelf-head">
            <div>
              <h3>Latest Scans</h3>
              <p>Newest scans first. Open any report to inspect details.</p>
            </div>
          </div>
          <div class="scan-featured-grid">
          @foreach($featuredRecentScans as $scan)
            @php
              $scanRouteKey = $scan['scan_route_key'] ?? $scan['public_scan_id'] ?? $scan['system_scan_id'] ?? null;
              $isRenderable = (bool) ($scan['is_renderable_report'] ?? false);
              $reportHref = ($scanRouteKey && $isRenderable) ? route('dashboard.scans.show', ['scan' => $scanRouteKey]) : null;
              $scanScore = (int) ($scan['score'] ?? 0);
              $scanStatusStr = (string) ($scan['status'] ?? '');
              // Compute processing link for paid/pending scans that have a session
              $scanSessionId = (string) ($scan['stripe_session_id'] ?? '');
              $isInProgress = !$isRenderable && in_array($scanStatusStr, ['pending', 'paid']);
              $processingHref = ($isInProgress && $scanRouteKey && $scanSessionId !== '')
                ? (route('dashboard.scans.show', ['scan' => $scanRouteKey]) . '?session_id=' . urlencode($scanSessionId))
                : null;
              $isError = ($scanStatusStr === 'error');
              $scanState = $isRenderable
                ? ($scanScore >= 85 ? 'Stable' : ($scanScore >= 60 ? 'Under-optimized' : 'At Risk'))
                : match ($scanStatusStr) {
                    'paid'    => 'Analyzing your site',
                    'error'   => 'Scan could not be completed',
                    default   => 'Scan in progress',
                  };
              $scanSubline = $isRenderable
                ? 'Report ready — view your full results.'
                : match ($scanStatusStr) {
                    'paid'    => 'AI analysis running now. Usually completes in 10–30 seconds.',
                    'error'   => 'Something went wrong. You can retry below.',
                    'pending' => 'Queued — starts automatically once payment confirms.',
                    default   => 'Scan in progress — usually under a minute.',
                  };
              $scanNextStep = $isRenderable
                ? 'View report to see findings and fixes.'
                : match ($scanStatusStr) {
                    'paid'    => 'You don\'t need to do anything — we\'ll guide you when it\'s ready.',
                    'error'   => 'Contact support or run a new scan.',
                    default   => 'You don\'t need to do anything — results appear automatically.',
                  };
              $cardClass = 'scan-history-card surface-reveal'
                . ($isInProgress ? ' is-inprogress' : '')
                . ($isError ? ' is-error' : '');
              $dotClass = $isRenderable ? 'is-done' : ($isError ? 'is-error' : 'is-active');
            @endphp
            <article class="{{ $cardClass }}">
              <div class="scan-history-head">
                <div>
                  <p class="meta">Scan Archive</p>
                  <p class="domain">{{ $scan['scan_name'] ?? $scan['domain'] }}</p>
                  <p class="scan-history-subline">{{ $scanSubline }}</p>
                  <div class="scan-card-badges">
                    <span class="scan-card-badge">Latest</span>
                    <span class="scan-card-badge" style="display:inline-flex;align-items:center;gap:5px">
                      <span class="status-dot {{ $dotClass }}"></span>{{ $scanState }}
                    </span>
                  </div>
                </div>
                <div class="scan-history-score">
                  <span class="score">{{ $isRenderable ? $scanScore : '—' }}</span>
                  <span class="label">{{ $isRenderable ? 'Readout Score' : 'Pending' }}</span>
                </div>
              </div>
              @if($isInProgress)
                <div class="scan-card-shimmer"></div>
              @endif
              <div class="state-row">
                <span class="pill" style="{{ $isInProgress ? 'border-color:rgba(200,168,75,.4);color:#d9c579' : ($isError ? 'border-color:rgba(199,72,72,.3);color:#d68888' : '') }}">{{ $scanState }}</span>
                <span class="text-[11px] uppercase tracking-[0.12em] text-[#aaa08b]">
                  {{ $scan['scanned_at']?->diffForHumans() ?? $scan['created_at']?->diffForHumans() }}
                </span>
              </div>
              <p class="scan-history-subline" style="font-size:.72rem;color:#a09585;margin-top:-4px">{{ $scanNextStep }}</p>
              <div class="actions">
                @if($reportHref)
                  <a href="{{ $reportHref }}" class="open">View Full Report</a>
                @elseif($processingHref)
                  <a href="{{ $processingHref }}" class="inprogress-link">
                    <span class="status-dot is-active" style="width:6px;height:6px"></span>
                    Watch Progress
                  </a>
                @else
                  <span class="disabled" aria-disabled="true">{{ $scanState }}</span>
                @endif
              </div>
            </article>
          @endforeach
          </div>
        </div>
        @endif

        @if($archivedScans->isNotEmpty())
        <div class="scan-shelf">
          <div class="scan-shelf-head">
            <div>
              <h3>Scan Archive</h3>
              <p>Older scans remain available for history and comparison.</p>
            </div>
          </div>
          <div class="scan-archive-grid">
          @foreach($archivedScans as $scan)
            @php
              $scanRouteKey = $scan['scan_route_key'] ?? $scan['public_scan_id'] ?? $scan['system_scan_id'] ?? null;
              $isRenderable = (bool) ($scan['is_renderable_report'] ?? false);
              $reportHref = ($scanRouteKey && $isRenderable) ? route('dashboard.scans.show', ['scan' => $scanRouteKey]) : null;
              $scanScore = (int) ($scan['score'] ?? 0);
              $archivedStatusStr = (string) ($scan['status'] ?? '');
              $archivedSessionId = (string) ($scan['stripe_session_id'] ?? '');
              $archIsInProgress = !$isRenderable && in_array($archivedStatusStr, ['pending', 'paid']);
              $archProcessingHref = ($archIsInProgress && $scanRouteKey && $archivedSessionId !== '')
                ? (route('dashboard.scans.show', ['scan' => $scanRouteKey]) . '?session_id=' . urlencode($archivedSessionId))
                : null;
              $archIsError = ($archivedStatusStr === 'error');
              $scanState = $isRenderable
                ? ($scanScore >= 85 ? 'Stable' : ($scanScore >= 60 ? 'Under-optimized' : 'At Risk'))
                : match ($archivedStatusStr) {
                    'paid'    => 'Analyzing your site',
                    'error'   => 'Scan could not be completed',
                    default   => 'Scan in progress',
                  };
              $archCardClass = 'scan-history-card surface-reveal'
                . ($archIsInProgress ? ' is-inprogress' : '')
                . ($archIsError ? ' is-error' : '');
              $archDotClass = $isRenderable ? 'is-done' : ($archIsError ? 'is-error' : 'is-active');
            @endphp
            <article class="{{ $archCardClass }}">
              <div class="scan-history-head">
                <div>
                  <p class="meta">Archived Scan</p>
                  <p class="domain">{{ $scan['scan_name'] ?? $scan['domain'] }}</p>
                  <div class="scan-card-badges">
                    <span class="scan-card-badge">Archive</span>
                    <span class="scan-card-badge" style="display:inline-flex;align-items:center;gap:5px">
                      <span class="status-dot {{ $archDotClass }}"></span>{{ $scanState }}
                    </span>
                  </div>
                </div>
                <div class="scan-history-score">
                  <span class="score">{{ $isRenderable ? $scanScore : '—' }}</span>
                  <span class="label">{{ $isRenderable ? 'Readout Score' : 'Pending' }}</span>
                </div>
              </div>
              @if($archIsInProgress)
                <div class="scan-card-shimmer"></div>
              @endif
              <div class="state-row">
                <span class="pill" style="{{ $archIsInProgress ? 'border-color:rgba(200,168,75,.4);color:#d9c579' : ($archIsError ? 'border-color:rgba(199,72,72,.3);color:#d68888' : '') }}">{{ $scanState }}</span>
                <span class="text-[11px] uppercase tracking-[0.12em] text-[#aaa08b]">
                  {{ $scan['scanned_at']?->diffForHumans() ?? $scan['created_at']?->diffForHumans() }}
                </span>
              </div>
              <div class="actions">
                @if($reportHref)
                  <a href="{{ $reportHref }}" class="open">View Full Report</a>
                @elseif($archProcessingHref)
                  <a href="{{ $archProcessingHref }}" class="inprogress-link">
                    <span class="status-dot is-active" style="width:6px;height:6px"></span>
                    Watch Progress
                  </a>
                @else
                  <span class="disabled" aria-disabled="true">{{ $scanState }}</span>
                @endif
              </div>
            </article>
          @endforeach
          </div>
        </div>
        @elseif($featuredRecentScans->isEmpty())
          <article class="scan-history-card surface-reveal">
            <p class="meta">System Readout Active</p>
            <p class="domain">No previous scans ready yet.</p>
            <p class="bottleneck">Run your first scan to unlock report history, bottlenecks, and next moves.</p>
            <div class="actions">
              <a href="{{ route('quick-scan.show') }}" class="open">Run First Scan</a>
            </div>
          </article>
        @endif
      </div>
    </section>
    @endif
    </div>
    @else
    <section class="system-section system-section-primary mb-10 dash-section-anchor surface-reveal is-visible" id="onboarding-command">
      <div class="onboarding-command-shell">
        <p class="onboarding-command-kicker">System Initialization Required</p>
        <h1 class="onboarding-command-title">AI can’t see your business yet</h1>
        <p class="onboarding-command-copy">Run your first scan to establish your visibility baseline and unlock your system readout.</p>
        <a href="{{ route('scan.start') }}" class="onboarding-command-cta">Run My First Scan →</a>
        <p class="onboarding-command-reassure">See your score, your biggest gap, and your first fix.</p>
        <p class="onboarding-command-footnote">Takes 10 seconds &bull; $2</p>
        <details class="onboarding-explainer">
          <summary>What will the scan show?</summary>
          <div class="onboarding-explainer-panel">
            <ul>
              <li>Your baseline visibility score (0&ndash;100).</li>
              <li>Your biggest issue holding you back.</li>
              <li>The first fix most likely to improve your score.</li>
            </ul>
          </div>
        </details>
        <div class="onboarding-proof-strip" aria-hidden="true">
          <article class="onboarding-proof-item">
            <p>You get</p>
            <p>Baseline visibility score</p>
          </article>
          <article class="onboarding-proof-item">
            <p>You get</p>
            <p>Your biggest issue</p>
          </article>
          <article class="onboarding-proof-item">
            <p>You get</p>
            <p>Your first fix</p>
          </article>
        </div>
      </div>
    </section>
    @endif

    @if($hasSystem && !$agencyModeActive && $isSystemView && false)
    <section class="system-section mb-10 rounded-2xl border border-[#c8a84b]/16 bg-linear-to-br from-[#19160f] via-[#121008] to-[#0c0a06] p-5 shadow-xl operations-quiet dash-section-anchor surface-reveal" id="systems">
      <p class="mb-2 text-xs uppercase tracking-[0.22em] text-[#c8a84b]/80">System Readout</p>
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <h1 class="text-xl font-semibold leading-tight lg:text-2xl">Current Level: {{ $currentLevelLabel }}</h1>
          <p class="mt-2 max-w-2xl text-sm text-[#b5b0a3]">Next level: {{ $nextLevelLabel }}{{ $nextLevelPrice ? (' · ' . $nextLevelPrice) : '' }}. This unlock adds deeper guidance for your next optimization step.</p>
        </div>
        <div class="flex flex-wrap gap-3">
          <a href="{{ $leadReportHref }}" class="inline-flex items-center justify-center rounded-xl border border-[#c8a84b]/40 px-5 py-3 text-sm font-semibold text-[#e7dfc9] transition hover:border-[#c8a84b] hover:bg-[#c8a84b]/10">Open Latest Report</a>
          @if($nextLayer)
            <a href="{{ $nextLevelHref }}" class="inline-flex items-center justify-center rounded-xl bg-[#c8a84b] px-5 py-3 text-sm font-semibold text-[#0b0905] transition hover:bg-[#dfc477]">Unlock {{ $nextLevelLabel }}</a>
          @endif
        </div>
      </div>
      <div class="ia-level-unlocks mt-4">
        <p>What This Level Unlocks</p>
        <ul>
          @foreach($nextLevelUnlocks as $unlock)
            <li>{{ $unlock }}</li>
          @endforeach
        </ul>
      </div>
    </section>
    @endif

    @if($hasSystem && $agencyModeActive && $isSystemView && false)
      @php
        $gridClass = $systemCount >= 10 ? 'grid-compact' : ($systemCount <= 4 ? 'grid-wide' : '');
        $gridDensity = $systemCount >= 10 ? 'compact' : ($systemCount <= 4 ? 'wide' : 'standard');
        $orderedScans = $scanHistory
          ->filter(fn ($scan) => (bool) ($scan['is_renderable_report'] ?? false))
          ->sortByDesc(function ($scan) {
              $score = (int) ($scan['score'] ?? 0);
              $issues = (int) ($scan['issues_count'] ?? 0);
              $urgency = (100 - $score) + ($issues * 2);
              return $urgency;
          })
          ->values();
      @endphp
      <section class="system-section system-section-primary mb-10 dash-section-anchor surface-reveal" id="systems">
        <div class="system-grid-shell">
          <div class="system-grid-toolbar">
            <div>
              <h2 class="text-sm uppercase tracking-[0.2em] text-[#c8a84b]/78">System Grid</h2>
              <p class="mt-1 text-sm text-[#bcb6a8]">Continuous readout across active systems.</p>
              <p class="mt-1 text-xs text-[#9f9785]">Priority auto-ranked by active blockers.</p>
            </div>
            <div class="flex flex-wrap gap-2">
              <span class="inline-flex items-center rounded-lg border border-[#c8a84b]/22 px-3 py-2 text-xs font-semibold tracking-[0.08em] text-[#dfd6c1]">{{ $systemCount }} systems</span>
              <a href="{{ route('for-agencies') }}" class="inline-flex items-center rounded-lg border border-[#c8a84b]/30 px-3 py-2 text-xs font-semibold tracking-[0.08em] text-[#dfd6c1] transition hover:border-[#c8a84b] hover:bg-[#c8a84b]/10">Client load detected? Deploy additional systems.</a>
            </div>
          </div>

          <p id="fixProgressCounter" class="fix-progress-counter" aria-live="polite"></p>

          <div class="system-grid {{ $gridClass }}" data-grid-density="{{ $gridDensity }}">
            @foreach($orderedScans as $scan)
              @php
                $score = (int) ($scan['score'] ?? 0);
                $statusClass = $score >= 85 ? 'status-strong' : ($score >= 60 ? 'status-partial' : 'status-critical');
                $fastFix = trim((string) ($scan['fastest_fix'] ?? '')) !== ''
                  ? $scan['fastest_fix']
                  : 'Strengthen primary service signal to improve selection probability';
                $selectionLabel = $score >= 85 ? 'Stable' : ($score >= 70 ? 'Expanding' : ($score >= 50 ? 'Under-optimized' : 'At Risk'));
                $selectionClass = $score >= 85 ? 'selection-green' : ($score >= 60 ? 'selection-amber' : 'selection-red');
                $isFeatured = $loop->first;
                $pressureLabel = $score >= 85 ? 'Stable - expansion required' : ($score >= 70 ? 'Coverage partial' : ($score >= 50 ? 'Constraint detected - stabilizing' : 'Signal weak - selection risk'));
                $featuredInsight = $score >= 85
                  ? 'Momentum holding. Expansion window active.'
                  : ($score >= 60
                    ? 'Partial coverage detected. Core signal reinforcement required.'
                    : 'Weak coverage persisting. Primary signal restoration required.');
                $scanEvaluatedAt = $scan['scanned_at'] ?? $scan['created_at'] ?? null;
                $scanEvaluatedLabel = $scanEvaluatedAt?->diffForHumans() ?? 'awaiting evaluation';
                $scanMemoryLine = $score < 70
                  ? ($score < 50 ? 'Pressure persisting from prior evaluation.' : 'Prior instability detected. Stabilization in progress.')
                  : 'No pressure detected (temporary).';
                $actionMemoryLine = $scanEvaluatedAt
                  ? ('Last action: ' . $scanEvaluatedLabel)
                  : 'Correction initiated recently';
                $expansionPotentialLabel = $score >= 85
                  ? '+12-18 expansion potential detected'
                  : ($score >= 70
                    ? 'Expansion capacity available'
                    : ($score >= 50 ? 'Limited expansion surface detected' : 'High expansion opportunity'));
                $expansionPreviewLine = $score >= 85
                  ? 'Service-area signals not detected.'
                  : ($score >= 70
                    ? 'Entities missing from selection surface.'
                    : ($score >= 50 ? 'FAQ / semantic expansion incomplete.' : 'Structured definition layers absent.'));
              @endphp
              @php
                $scanRouteKey = $scan['scan_route_key'] ?? $scan['public_scan_id'] ?? $scan['system_scan_id'] ?? null;
                $inspectHref = $scanRouteKey
                  ? route('dashboard.scans.show', ['scan' => $scanRouteKey])
                  : route('app.dashboard');
                $rankToLayerAnchor = [2 => 'layer-signal', 3 => 'layer-structure', 4 => 'layer-system'];
                $rankToLayerName = [2 => 'Signal Analysis', 3 => 'Action Plan', 4 => 'Guided Execution'];
                $rankToPlan = [2 => 'diagnostic', 3 => 'fix-strategy', 4 => 'optimization'];
                $rankToPrice = [2 => '$99', 3 => '$249', 4 => '$489'];
                $rankToCheckoutRoute = [2 => 'checkout.signal-expansion', 3 => 'checkout.structural-leverage', 4 => 'checkout.system-activation'];
                $scanTierRank = (int) ($scan['tier_rank'] ?? $tierRank);
                $includedLayerSummary = match (true) {
                  $scanTierRank >= 4 => 'All system layers active for this readout.',
                  $scanTierRank === 3 => 'Action Plan and Signal Analysis active.',
                  $scanTierRank === 2 => 'Signal Analysis active with baseline layer.',
                  default => 'Baseline readout active only.',
                };
                $lockedLayerSummary = match (true) {
                  $scanTierRank >= 4 => 'No locked layers. Guided Execution is active.',
                  $scanTierRank === 3 => 'Guided Execution remains as next unlock.',
                  $scanTierRank === 2 => 'Action Plan remains locked.',
                  default => 'Signal Analysis remains locked.',
                };
                $nextProgressionRank = match (true) {
                  $scanTierRank <= 1 => 2,
                  $scanTierRank === 2 => 3,
                  $scanTierRank === 3 => 4,
                  default => null,
                };
                $noDeeperPath = $nextProgressionRank === null;
                $suggestedCorrectionRank = $nextProgressionRank ?? 4;

                $modalTitle = 'Constraint Resolution';
                $modalUnlockEffect = 'Next corrective layer unavailable.';
                $modalPrice = 'N/A';
                $modalPrimaryHref = $inspectHref;
                $modalPrimaryLabel = 'Inspect Readout';

                if ($noDeeperPath) {
                  $correctionActionType = 'modal';
                  $correctionLabel = 'Open Fix Details';
                  $postCorrectionLabel = '✓ Fix Applied — Verifying impact';
                  $nextPathLine = 'Current path: See fix options';
                  $correctionHref = null;
                  $modalTitle = 'Expansion Opportunity';
                  $modalUnlockEffect = 'System fully active. Correction now means expanding execution pathways.';
                  $modalPrice = 'No additional unlock required';
                  $modalPrimaryHref = $inspectHref . '#sys-actions';
                  $modalPrimaryLabel = 'Open Expansion Opportunities';
                } elseif ($scanTierRank >= $suggestedCorrectionRank) {
                  $correctionActionType = 'unlocked';
                  $correctionLabel = 'Apply Fix';
                  $postCorrectionLabel = '✓ Fix Applied';
                  $nextPathLine = 'Next path: ' . ($rankToLayerName[$suggestedCorrectionRank] ?? 'Signal Analysis');
                  $correctionHref = $inspectHref . '#' . ($rankToLayerAnchor[$suggestedCorrectionRank] ?? 'detailed-layer-view');
                } else {
                  $correctionActionType = 'locked';
                  $correctionLabel = 'Unlock to Apply Fix';
                  $postCorrectionLabel = '✓ Fix Applied';
                  $nextPathLine = 'Next unlock: ' . ($rankToLayerName[$suggestedCorrectionRank] ?? 'Signal Analysis');
                  $targetPlan = $rankToPlan[$suggestedCorrectionRank] ?? 'diagnostic';
                  $modalTitle = 'Unlock ' . ($rankToLayerName[$suggestedCorrectionRank] ?? 'Signal Analysis');
                  $modalUnlockEffect = 'Unlock adds deeper correction controls and progression context.';
                  $modalPrice = $rankToPrice[$suggestedCorrectionRank] ?? 'N/A';
                  $correctionHref = !empty($scan['scan_id']) && !empty($scan['stripe_session_id'])
                    ? route('quick-scan.upgrade', [
                      'plan' => $targetPlan,
                      'scan_id' => $scan['scan_id'],
                      'sid' => $scan['stripe_session_id'],
                    ])
                    : route($rankToCheckoutRoute[$suggestedCorrectionRank] ?? 'checkout.signal-expansion');
                  $modalPrimaryHref = $correctionHref;
                  $modalPrimaryLabel = 'Unlock ' . ($rankToLayerName[$suggestedCorrectionRank] ?? 'Signal Analysis') . ' - ' . ($rankToPrice[$suggestedCorrectionRank] ?? '');
                }
              @endphp
              <article
                class="system-grid-card clickable {{ $statusClass }} {{ $isFeatured ? 'featured' : 'supporting' }}"
                aria-label="Open {{ $scan['scan_name'] }} system view"
                role="link"
                tabindex="0"
                data-open-href="{{ $inspectHref }}"
                data-scan-key="{{ $scanRouteKey ?? $scan['scan_id'] }}"
                data-readout-domain="{{ $scan['scan_name'] ?? $scan['domain'] }}"
                data-readout-score="{{ $score }}"
                data-readout-state="{{ $selectionLabel }}"
                data-readout-evaluated="{{ $scanEvaluatedLabel }}"
                data-readout-constraint="{{ $fastFix }}"
                data-readout-included="{{ $includedLayerSummary }}"
                data-readout-locked="{{ $lockedLayerSummary }}"
                data-correction-type="{{ $correctionActionType }}"
                data-correction-href="{{ $correctionHref ?? '' }}"
                data-correction-label="{{ $correctionLabel }}"
                data-correction-next="{{ $nextPathLine }}"
              >
                <div class="execution-state" aria-hidden="true">
                  <span class="chip">Applying fix...</span>
                </div>
                <div class="mb-2 flex items-center justify-between gap-3">
                  <p class="truncate text-sm font-semibold text-[#ece5d4]">
                    <a href="{{ $inspectHref }}" class="card-title-link js-readout-link">{{ $scan['scan_name'] ?? $scan['domain'] }}</a>
                  </p>
                  <div class="flex items-center gap-2">
                    <span class="priority-tag">Priority Target</span>
                    <span class="featured-tag">Primary Focus</span>
                    <span class="system-grid-score">{{ $score }}</span>
                  </div>
                </div>

                <div class="selection-row">
                  <p>Readout</p>
                  <span class="selection-pill {{ $selectionClass }}">{{ $selectionLabel }}</span>
                </div>
                <p class="selection-subline">This is limiting your visibility.</p>
                <p class="pressure-line {{ $score < 60 ? 'risk' : ($score >= 85 ? 'stable' : '') }}">{{ $pressureLabel }}</p>

                @if($isFeatured)
                <p class="featured-insight">{{ $featuredInsight }}</p>
                @endif

                <p class="memory-line">Last evaluated: {{ $scanEvaluatedLabel }} · {{ $scanMemoryLine }}</p>
                <p class="action-memory-line" data-default-action-memory="{{ $actionMemoryLine }}">{{ $actionMemoryLine }}</p>
                <p class="card-open-hint">Open system: current state</p>

                <div class="system-fast-fix">
                  <p>Constraint</p>
                  <p>{{ $fastFix }}</p>
                </div>

                <div class="expansion-potential">
                  <p>Expansion Potential</p>
                  <p>{{ $expansionPotentialLabel }}</p>
                </div>

                <div class="expansion-preview">
                  <p>AI Expansion Preview</p>
                  <p>{{ $expansionPreviewLine }}</p>
                  <p class="preview-directive">Increase signal resolution. Activation required to deploy expansion.</p>
                </div>

                <p class="next-path-line">{{ $nextPathLine }}</p>
                <p class="card-response-line">System responding to adjustment</p>

                <div class="system-card-actions">
                  @if($correctionActionType === 'modal')
                    <button
                      type="button"
                      class="system-grid-cta cta-modal js-open-correction-modal"
                      data-scan-name="{{ $scan['scan_name'] ?? $scan['domain'] }}"
                      data-constraint="{{ $fastFix }}"
                      data-pressure="{{ $pressureLabel }}"
                      data-inspect-href="{{ $inspectHref }}"
                      data-modal-title="{{ $modalTitle }}"
                      data-unlock-effect="{{ $modalUnlockEffect }}"
                      data-unlock-price="{{ $modalPrice }}"
                      data-primary-href="{{ $modalPrimaryHref }}"
                      data-primary-label="{{ $modalPrimaryLabel }}"
                      data-resolve-type="modal"
                      data-resolve-href=""
                      data-post-label="{{ $postCorrectionLabel }}"
                      data-feedback-line="Fix details opened &#x2014; reviewing options"
                    >{{ $correctionLabel }}</button>
                  @elseif($correctionActionType === 'locked')
                    <button
                      type="button"
                      class="system-grid-cta cta-unlock js-correction-action"
                      data-resolve-type="redirect"
                      data-resolve-href="{{ $correctionHref }}"
                      data-post-label="{{ $postCorrectionLabel }}"
                      data-feedback-line="Unlocking this removes a visibility constraint"
                    >{{ $correctionLabel }}</button>
                  @else
                    <button
                      type="button"
                      class="system-grid-cta cta-fix js-correction-action"
                      data-resolve-type="redirect"
                      data-resolve-href="{{ $correctionHref }}"
                      data-post-label="{{ $postCorrectionLabel }}"
                      data-feedback-line="This removes a constraint limiting your visibility"
                    >{{ $correctionLabel }}</button>
                  @endif
                                  <a href="{{ $inspectHref }}" class="system-grid-cta cta-view js-readout-link">{{ $scan['is_renderable_report'] ? 'View Full Report' : 'Inspect Readout' }}</a>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    @if($hasSystem && !$agencyModeActive && $isReportsView)
    <section class="system-section system-section-secondary mb-10 dash-section-anchor" id="intelligence-stack">
      <div class="system-subshell">
        <div class="section-head">
          <div>
            <h2>Active Intelligence Stack</h2>
            <p>Access-state readout across deployed layers.</p>
          </div>
          <span class="text-xs text-[#9f9b8d]">{{ $hasSystem ? $totalScans . ' baselines persisting' : 'No active baseline' }}</span>
        </div>
        <div class="grid gap-4 md:grid-cols-4">
        @foreach($analysisLayers as $layer)
          <article class="stack-card {{ $layer['complete'] ? 'active' : 'dormant' }}">
            <p class="text-[11px] uppercase tracking-[0.16em] {{ $layer['complete'] ? 'text-[#6aaf90]' : 'text-[#c8a84b]/70' }}">{{ $layer['complete'] ? 'Tracking live' : 'Dormant layer' }}</p>
            <h3 class="mt-1 text-base font-semibold">
              {{ $layer['key'] === 'scan-basic' ? 'Baseline Engine' : ($layer['key'] === 'signal-expansion' ? 'Signal Engine' : ($layer['key'] === 'structural-leverage' ? 'Leverage Engine' : 'Activation Engine')) }}
            </h3>
            <p class="mt-1 text-xs text-[#a7a18f]">{{ $layer['price'] }}</p>
            @if(!$layer['complete'])
              <p class="mt-2 text-[11px] uppercase tracking-[0.12em] text-[#c8a84b]/70">Signal not extended into this layer</p>
            @else
              <p class="mt-2 text-[11px] uppercase tracking-[0.12em] text-[#86c4a6]">Continuous detection persisting</p>
            @endif
          </article>
        @endforeach
        </div>
      </div>
    </section>
    @endif

    @if($hasSystem && !$agencyModeActive && $isReportsView)
    <section class="system-section system-section-tertiary mb-10 dash-section-anchor" id="extensions">
      <div class="system-subshell dim">
        <div class="section-head">
          <div>
            <h2>Dormant Extensions</h2>
            <p>Additional paths unlock as pressure expands.</p>
          </div>
        </div>
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        @foreach($additionalCapabilities as $capability)
          <article class="stack-card dormant">
            <p class="text-[11px] uppercase tracking-[0.14em] text-[#c8a84b]/65">Awaiting activation</p>
            <h3 class="mt-2 text-sm font-semibold text-[#ece5d4]">{{ $capability }}</h3>
            <p class="mt-2 text-xs text-[#a9a392]">Dormant under current readout.</p>
          </article>
        @endforeach
        </div>
      </div>
    </section>
    @endif

    @if($hasSystem && $isSystemView && false)
    <section class="system-section system-section-secondary mb-10 dash-section-anchor operations-quiet surface-reveal" id="coverage">
      <h2 class="mb-3 text-sm uppercase tracking-[0.2em] text-[#c8a84b]/68">Next Move Queue</h2>
      <div class="next-move-grid">
        <a href="{{ route('reports.index') }}" class="next-move-card primary">
          <p class="text-[11px] uppercase tracking-[0.14em] action-label text-[#c8a84b]/70">Next Unlock</p>
          <h3 class="mt-1 text-lg font-semibold">Apply Fix on At-Risk Readouts</h3>
          <p class="mt-2 text-sm text-[#b2ac9a]">Primary bottlenecks are still active. Push the highest-impact correction first.</p>
          <p class="impact-hint high">Impact: High</p>
        </a>
        <a href="{{ route('pages.index') }}" class="next-move-card secondary">
          <p class="text-[11px] uppercase tracking-[0.14em] text-[#c8a84b]/70">Inspect Signal</p>
          <h3 class="mt-1 text-lg font-semibold">Tighten Page Signal Clarity</h3>
          <p class="mt-2 text-sm text-[#b2ac9a]">Signal extraction remains soft on key pages. Refine structure and definitions.</p>
          <p class="impact-hint medium">Impact: Medium</p>
        </a>
        <a href="{{ route('quick-scan.show') }}" class="next-move-card secondary">
          <p class="text-[11px] uppercase tracking-[0.14em] text-[#c8a84b]/70">Next Move</p>
          <h3 class="mt-1 text-lg font-semibold">Run Fresh System Readout</h3>
          <p class="mt-2 text-sm text-[#b2ac9a]">Refresh your latest score/state and confirm whether pressure is clearing.</p>
          <p class="impact-hint low">Impact: Low</p>
        </a>
        <a href="{{ ($latestScan && $latestScan->status === \App\Models\QuickScan::STATUS_SCANNED && $latestScan->score !== null) ? route('dashboard.scans.show', ['scan' => $latestScan->publicScanId()]) : route('quick-scan.show') }}" class="next-move-card secondary">
          <p class="text-[11px] uppercase tracking-[0.14em] text-[#c8a84b]/70">Reports</p>
          <h3 class="mt-1 text-lg font-semibold">Open Latest Detailed Readout</h3>
          <p class="mt-2 text-sm text-[#b2ac9a]">Inspect score trajectory, bottleneck context, and fastest fix details.</p>
          <p class="impact-hint low">Impact: Low</p>
        </a>
      </div>
    </section>
    @endif

  </div>
</div>

<div class="correction-modal-mask" id="correctionPathModal" data-open="false" role="dialog" aria-modal="true" aria-labelledby="correctionPathTitle">
  <div class="correction-modal">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="meta">Correction Path</p>
        <h3 id="correctionPathTitle">Constraint Resolution</h3>
      </div>
      <button type="button" id="correctionPathClose" class="close-btn">Close</button>
    </div>

    <div class="panel">
      <p>What correction means here</p>
      <p id="correctionPathWhat">Correction path is being prepared from current system state.</p>
    </div>

    <div class="panel">
      <p>Current constraint</p>
      <p id="correctionPathConstraint">-</p>
    </div>

    <div class="panel">
      <p>Next system action available</p>
      <p id="correctionPathNext">Open execution pathway or inspect this system readout for highest-priority correction.</p>
    </div>

    <div class="panel">
      <p>Unlock</p>
      <p id="correctionPathPrice">N/A</p>
    </div>

    <div class="actions">
      <a id="correctionPathInspect" href="{{ route('quick-scan.show') }}" class="system-grid-cta cta-view">Inspect Readout</a>
      <div>
        <p class="modal-apply-note">This marks the fix as applied and advances your system progress.</p>
        <a id="correctionPathPrimary" href="{{ route('quick-scan.show') }}" class="system-grid-cta cta-fix">Apply This Fix</a>
      </div>
      <a href="{{ url('/book?entry=consultation') }}" class="assist-link">Prefer guided activation? Book consultation</a>
    </div>
  </div>
</div>

<div class="readout-flyout-mask" id="readoutFlyout" data-open="false" role="dialog" aria-modal="true" aria-labelledby="readoutFlyoutTitle">
  <aside class="readout-flyout">
    <div class="readout-flyout-inner">
      <div class="readout-flyout-head">
        <p class="readout-flyout-kicker">System Panel Expanded</p>
        <button type="button" class="readout-flyout-close" id="readoutFlyoutCloseTop">Close</button>
      </div>

      <div class="readout-identity">
        <h2 id="readoutFlyoutTitle" class="readout-identity-domain">Readout</h2>
        <div class="readout-identity-grid">
          <div class="readout-metric">
            <p class="readout-metric-label">Score</p>
            <p class="readout-metric-value" id="readoutFlyoutScore">-</p>
          </div>
          <div class="readout-metric">
            <p class="readout-metric-label">State</p>
            <p class="readout-metric-value" id="readoutFlyoutState">-</p>
          </div>
          <div class="readout-metric" style="grid-column:1/-1">
            <p class="readout-metric-label">Last Evaluated</p>
            <p class="readout-metric-value" id="readoutFlyoutEvaluated">-</p>
          </div>
        </div>
      </div>

      <div class="readout-section">
        <p>Primary Constraint</p>
        <p id="readoutFlyoutConstraint">-</p>
      </div>

      <div class="readout-section">
        <p>Current Layer Summary</p>
        <p id="readoutFlyoutLayerIncluded">-</p>
        <p id="readoutFlyoutLayerLocked" style="margin-top:6px;color:#bcae8b">-</p>
      </div>

      <div class="readout-correction-inline" id="readoutCorrectionInline">
        <p id="readoutCorrectionInlineText">Correction path status unavailable.</p>
      </div>

      <div class="readout-actions">
        <a href="{{ route('quick-scan.show') }}" class="readout-action-btn readout-action-full" id="readoutFlyoutFull">Expand to Full Analysis</a>
        <button type="button" class="readout-action-btn readout-action-correction" id="readoutFlyoutCorrection">Open Fix Details</button>
        <button type="button" class="readout-action-btn readout-action-close" id="readoutFlyoutClose">Close Panel</button>
      </div>
    </div>
  </aside>
</div>

<script>
  (function () {
    const revealables = document.querySelectorAll('.surface-reveal');
    if ('IntersectionObserver' in window) {
      const revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-visible');
          revealObserver.unobserve(entry.target);
        });
      }, { threshold: 0.16, rootMargin: '0px 0px -6% 0px' });

      revealables.forEach(function (node) {
        if (node.classList.contains('is-visible')) return;
        revealObserver.observe(node);
      });
    } else {
      revealables.forEach(function (node) {
        node.classList.add('is-visible');
      });
    }

    const cards = document.querySelectorAll('.system-grid-card.clickable[data-open-href]');
    const flyout = document.getElementById('readoutFlyout');
    const flyoutDomain = document.getElementById('readoutFlyoutTitle');
    const flyoutScore = document.getElementById('readoutFlyoutScore');
    const flyoutState = document.getElementById('readoutFlyoutState');
    const flyoutEvaluated = document.getElementById('readoutFlyoutEvaluated');
    const flyoutConstraint = document.getElementById('readoutFlyoutConstraint');
    const flyoutLayerIncluded = document.getElementById('readoutFlyoutLayerIncluded');
    const flyoutLayerLocked = document.getElementById('readoutFlyoutLayerLocked');
    const flyoutFull = document.getElementById('readoutFlyoutFull');
    const flyoutCorrection = document.getElementById('readoutFlyoutCorrection');
    const flyoutClose = document.getElementById('readoutFlyoutClose');
    const flyoutCloseTop = document.getElementById('readoutFlyoutCloseTop');
    const correctionInline = document.getElementById('readoutCorrectionInline');
    const correctionInlineText = document.getElementById('readoutCorrectionInlineText');

    let flyoutContext = null;
    let flyoutScrollY = 0;

    function buildDashboardReturnAwareHref(rawHref) {
      if (!rawHref) return '{{ route('quick-scan.show') }}';

      try {
        const target = new URL(rawHref, window.location.origin);
        target.searchParams.set('from', 'dashboard');
        target.searchParams.set('return_to', 'systems');
        return target.toString();
      } catch (err) {
        return rawHref;
      }
    }

    function openReadoutFlyout(card) {
      if (!flyout || !card) return;

      flyoutContext = {
        fullHref: card.dataset.openHref || '{{ route('quick-scan.show') }}',
        correctionType: card.dataset.correctionType || 'modal',
        correctionHref: card.dataset.correctionHref || '',
        correctionLabel: card.dataset.correctionLabel || 'Open Fix Details',
        correctionNext: card.dataset.correctionNext || 'Correction path available.',
      };

      flyoutDomain.textContent = card.dataset.readoutDomain || 'System Readout';
      flyoutScore.textContent = card.dataset.readoutScore || '-';
      flyoutState.textContent = card.dataset.readoutState || '-';
      flyoutEvaluated.textContent = card.dataset.readoutEvaluated || '-';
      flyoutConstraint.textContent = card.dataset.readoutConstraint || 'No constraint detected.';
      flyoutLayerIncluded.textContent = card.dataset.readoutIncluded || 'Layer status unavailable.';
      flyoutLayerLocked.textContent = card.dataset.readoutLocked || 'Next layer status unavailable.';

      flyoutFull.href = buildDashboardReturnAwareHref(flyoutContext.fullHref);
      flyoutCorrection.textContent = flyoutContext.correctionLabel;
      correctionInline.classList.remove('active');

      const rect = card.getBoundingClientRect();
      const originPct = Math.max(8, Math.min(92, Math.round((rect.top + rect.height / 2) / window.innerHeight * 100)));
      flyout.querySelector('.readout-flyout').style.setProperty('--origin-y', originPct + '%');

      flyoutScrollY = window.scrollY;
      flyout.dataset.open = 'true';
      document.body.style.overflow = 'hidden';
    }

    function closeReadoutFlyout(options) {
      if (!flyout) return;
      const restoreScroll = !options || options.restoreScroll !== false;
      flyout.dataset.open = 'false';
      correctionInline.classList.remove('active');
      document.body.style.overflow = '';
      if (restoreScroll) {
        window.scrollTo(0, flyoutScrollY);
      }
    }

    function runReadoutTransition(card, href) {
      if (!card || !href || card.dataset.readoutOpening === 'true') return;

      card.dataset.readoutOpening = 'true';
      card.classList.add('is-engaged');

      const executionState = card.querySelector('.execution-state');
      const stateChip = executionState ? executionState.querySelector('.chip') : null;
      if (stateChip) {
        stateChip.textContent = 'Opening system readout...';
      }
      if (executionState) {
        executionState.classList.add('active');
      }

      const responseLine = card.querySelector('.card-response-line');
      if (responseLine) {
        responseLine.textContent = 'Opening system readout...';
        responseLine.classList.add('live');
      }

      const memoryLine = card.querySelector('.action-memory-line');
      if (memoryLine) {
        memoryLine.textContent = 'Readout handoff initiated';
        memoryLine.classList.add('is-fresh');
      }

      const delayMs = 400 + Math.floor(Math.random() * 301);
      window.setTimeout(function () {
        const executionState = card.querySelector('.execution-state');
        if (executionState) executionState.classList.remove('active');
        card.dataset.readoutOpening = 'false';
        openReadoutFlyout(card);
      }, delayMs);
    }

    cards.forEach(function (card) {
      const href = card.dataset.openHref;
      if (!href) return;

      card.addEventListener('click', function (evt) {
        if (evt.target.closest('a,button')) return;
        runReadoutTransition(card, href);
      });

      card.addEventListener('keydown', function (evt) {
        if (evt.key !== 'Enter' && evt.key !== ' ') return;
        if (evt.target.closest('a,button')) return;
        evt.preventDefault();
        runReadoutTransition(card, href);
      });

      card.querySelectorAll('.js-readout-link').forEach(function (link) {
        link.addEventListener('click', function (evt) {
          evt.preventDefault();
          evt.stopPropagation();
          runReadoutTransition(card, link.getAttribute('href'));
        });
      });
    });

    if (flyoutCorrection) {
      flyoutCorrection.addEventListener('click', function () {
        if (!flyoutContext) return;

        if (flyoutContext.correctionType === 'locked' && flyoutContext.correctionHref) {
          window.location.href = flyoutContext.correctionHref;
          return;
        }

        correctionInlineText.textContent = flyoutContext.correctionNext || 'Correction path available.';
        correctionInline.classList.add('active');
      });
    }

    if (flyoutClose) {
      flyoutClose.addEventListener('click', closeReadoutFlyout);
    }
    if (flyoutCloseTop) {
      flyoutCloseTop.addEventListener('click', closeReadoutFlyout);
    }
    if (flyout) {
      flyout.addEventListener('click', function (evt) {
        if (evt.target === flyout) closeReadoutFlyout();
      });
    }

    if (flyoutFull) {
      flyoutFull.addEventListener('click', function (evt) {
        if (!flyoutContext || !flyoutContext.fullHref) return;

        evt.preventDefault();
        if (flyoutFull.dataset.executing === 'true') return;

        flyoutFull.dataset.executing = 'true';
        const originalLabel = flyoutFull.textContent;
        flyoutFull.classList.add('is-executing');
        flyoutFull.textContent = 'Loading system layer...';

        const returnScrollY = Number.isFinite(flyoutScrollY) ? flyoutScrollY : window.scrollY;
        sessionStorage.setItem('seoai.dashboard.return.scroll', String(returnScrollY));

        const delayMs = 400 + Math.floor(Math.random() * 501);
        window.setTimeout(function () {
          closeReadoutFlyout({ restoreScroll: false });
          window.location.assign(buildDashboardReturnAwareHref(flyoutContext.fullHref));

          // Fallback reset if navigation is blocked.
          flyoutFull.dataset.executing = 'false';
          flyoutFull.classList.remove('is-executing');
          flyoutFull.textContent = originalLabel;
        }, delayMs);
      });
    }

    window.addEventListener('pageshow', function () {
      if (flyout) {
        flyout.dataset.open = 'false';
      }
      correctionInline.classList.remove('active');
      document.body.style.overflow = '';

      const pendingScroll = sessionStorage.getItem('seoai.dashboard.return.scroll');
      if (pendingScroll !== null) {
        const parsed = Number(pendingScroll);
        if (Number.isFinite(parsed)) {
          window.requestAnimationFrame(function () {
            window.scrollTo(0, parsed);
          });
        }
        sessionStorage.removeItem('seoai.dashboard.return.scroll');
      }
    });

    window.addEventListener('pagehide', function () {
      document.body.style.overflow = '';
    });

    const modal = document.getElementById('correctionPathModal');
    const closeBtn = document.getElementById('correctionPathClose');
    const constraintNode = document.getElementById('correctionPathConstraint');
    const whatNode = document.getElementById('correctionPathWhat');
    const nextNode = document.getElementById('correctionPathNext');
    const priceNode = document.getElementById('correctionPathPrice');
    const inspectLink = document.getElementById('correctionPathInspect');
    const primaryLink = document.getElementById('correctionPathPrimary');

    function openCorrectionModal(btn) {
      const scanName = btn.dataset.scanName || 'System';
      const constraint = btn.dataset.constraint || 'Primary service signal remains constrained.';
      const pressure = btn.dataset.pressure || 'Constraint detected';
      const inspectHref = btn.dataset.inspectHref || '{{ route('quick-scan.show') }}';
      const modalTitle = btn.dataset.modalTitle || 'Constraint Resolution';
      const unlockEffect = btn.dataset.unlockEffect || 'Unlock adds deeper correction controls and progression context.';
      const unlockPrice = btn.dataset.unlockPrice || 'N/A';
      const primaryHref = btn.dataset.primaryHref || inspectHref;
      const primaryLabel = btn.dataset.primaryLabel || 'Apply This Fix';

      constraintNode.textContent = constraint;
      nextNode.textContent = pressure + '. Execution pathway available.';
      whatNode.textContent = unlockEffect;
      priceNode.textContent = unlockPrice;
      inspectLink.href = inspectHref;
      primaryLink.href = primaryHref;
      primaryLink.textContent = primaryLabel;

      const titleNode = document.getElementById('correctionPathTitle');
      if (titleNode) {
        titleNode.textContent = scanName + ' - ' + modalTitle;
      }

      modal.dataset.open = 'true';
    }

    function closeCorrectionModal() {
      modal.dataset.open = 'false';
    }

    function applyEngagedState(card, button, feedbackLine, postLabel, atMillis) {
      card.classList.remove('is-executing');
      card.classList.add('is-engaged');

      if (button) {
        button.classList.remove('cta-progress');
        button.classList.add('cta-applied');
        button.textContent = postLabel || '\u2713 Fix Applied';
        button.disabled = true;
      }

      const responseLine = card.querySelector('.card-response-line');
      if (responseLine) {
        responseLine.textContent = feedbackLine || 'This removes a constraint limiting your visibility';
        responseLine.classList.add('live');
      }

      const memoryLine = card.querySelector('.action-memory-line');
      if (memoryLine) {
        memoryLine.textContent = 'Fix applied just now';
        memoryLine.classList.add('is-fresh');
      }

      const scanKey = card.dataset.scanKey || '';
      if (scanKey !== '') {
        const payload = {
          at: atMillis,
          feedback: feedbackLine || 'This removes a constraint limiting your visibility',
          label: postLabel || '\u2713 Fix Applied',
        };
        try { localStorage.setItem('fix-state:' + scanKey, JSON.stringify(payload)); } catch (e) {}
      }

      updateFixProgress();
      pulseNextCard(card);
    }

    function pulseNextCard(currentCard) {
      var allCards = Array.prototype.slice.call(document.querySelectorAll('.system-grid-card'));
      var idx = allCards.indexOf(currentCard);
      if (idx < 0 || idx >= allCards.length - 1) return;
      var next = allCards[idx + 1];
      if (next.classList.contains('is-engaged')) return;
      next.classList.add('next-fix-pulse');
      next.addEventListener('animationend', function () {
        next.classList.remove('next-fix-pulse');
      }, { once: true });
    }

    function updateFixProgress() {
      var allCards = document.querySelectorAll('.system-grid-card[data-scan-key]');
      var total = allCards.length;
      var applied = 0;
      allCards.forEach(function (card) {
        var key = card.dataset.scanKey || '';
        if (!key) return;
        var raw; try { raw = localStorage.getItem('fix-state:' + key); } catch (e) { raw = null; }
        if (raw) applied++;
      });
      var counter = document.getElementById('fixProgressCounter');
      if (!counter) return;
      if (applied > 0) {
        counter.textContent = "You've improved " + applied + ' of ' + total + ' visibility blocker' + (total !== 1 ? 's' : '');
        counter.classList.add('has-progress');
      } else {
        counter.classList.remove('has-progress');
        counter.textContent = '';
      }
    }

    function runCorrectionCommit(button) {
      const card = button.closest('.system-grid-card');
      if (!card) return;

      const resolveType = button.dataset.resolveType || 'redirect';
      const resolveHref = button.dataset.resolveHref || '';
      const postLabel = button.dataset.postLabel || '✓ Fix Applied';
      const feedbackLine = button.dataset.feedbackLine || 'This removes a constraint limiting your visibility';

      const executionState = card.querySelector('.execution-state');
      if (executionState) executionState.classList.add('active');
      card.classList.add('is-executing');

      button.classList.add('cta-progress');
      button.disabled = true;

      const delayMs = 420 + Math.floor(Math.random() * 460);
      const actionAt = Date.now();

      window.setTimeout(function () {
        if (executionState) executionState.classList.remove('active');

        applyEngagedState(card, button, feedbackLine, postLabel, actionAt);

        if (resolveType === 'modal') {
          openCorrectionModal(button);
          return;
        }

        if (resolveHref) {
          window.location.href = resolveHref;
        }
      }, delayMs);
    }

    document.querySelectorAll('.js-correction-action, .js-open-correction-modal').forEach(function (btn) {
      btn.addEventListener('click', function () {
        runCorrectionCommit(btn);
      });
    });

    document.querySelectorAll('.system-grid-card[data-scan-key]').forEach(function (card) {
      const scanKey = card.dataset.scanKey || '';
      if (scanKey === '') return;

      var raw; try { raw = localStorage.getItem('fix-state:' + scanKey); } catch (e) { raw = null; }
      if (!raw) return;

      try {
        const parsed = JSON.parse(raw);
        const elapsedMs = Date.now() - Number(parsed.at || 0);

        if (!Number.isFinite(elapsedMs) || elapsedMs < 0 || elapsedMs > 48 * 60 * 60 * 1000) {
          try { localStorage.removeItem('fix-state:' + scanKey); } catch (e) {}
          return;
        }

        card.classList.add('is-engaged');

        const responseLine = card.querySelector('.card-response-line');
        if (responseLine) {
          responseLine.textContent = parsed.feedback || 'This removes a constraint limiting your visibility';
          responseLine.classList.add('live');
        }

        const actionBtn = card.querySelector('.js-correction-action, .js-open-correction-modal');
        if (actionBtn && parsed.label) {
          actionBtn.classList.add('cta-applied');
          actionBtn.textContent = parsed.label;
          actionBtn.disabled = true;
        }

        const elapsedHrs = Math.floor(elapsedMs / 3600000);
        const elapsedMins = Math.max(1, Math.floor((elapsedMs % 3600000) / 60000));
        const timeStr = elapsedHrs >= 1
          ? (elapsedHrs + ' hour' + (elapsedHrs === 1 ? '' : 's') + ' ago')
          : (elapsedMins + ' minute' + (elapsedMins === 1 ? '' : 's') + ' ago');
        const memoryLine = card.querySelector('.action-memory-line');
        if (memoryLine) {
          memoryLine.textContent = 'Fix applied: ' + timeStr;
          memoryLine.classList.add('is-fresh');
        }
      } catch (err) {
        try { localStorage.removeItem('fix-state:' + scanKey); } catch (e) {}
      }
    });

    // Initialize progress counter on load
    updateFixProgress();

    closeBtn.addEventListener('click', closeCorrectionModal);
    modal.addEventListener('click', function (evt) {
      if (evt.target === modal) closeCorrectionModal();
    });
    document.addEventListener('keydown', function (evt) {
      if (evt.key === 'Escape' && flyout && flyout.dataset.open === 'true') {
        closeReadoutFlyout();
        return;
      }
      if (evt.key === 'Escape' && modal.dataset.open === 'true') closeCorrectionModal();
    });

    // ── Data Capture Modal ────────────────────────────────────────────
    (function () {
      var dcmMask = document.getElementById('dcmMask');
      if (!dcmMask) return;

      var dcmForm     = document.getElementById('dcmForm');
      var dcmClose    = document.getElementById('dcmClose');
      var dcmSkip     = document.getElementById('dcmSkip');
      var dcmSubmit   = document.getElementById('dcmSubmit');
      var dcmTitle    = document.getElementById('dcmTitle');
      var dcmSubtitle = document.getElementById('dcmSubtitle');
      var currentCheckoutHref = '';

      function openDcm(btn) {
        var level        = btn.dataset.level        || '1';
        var levelName    = btn.dataset.levelName   || 'This Level';
        var checkoutHref = btn.dataset.checkoutHref || '';
        var price        = btn.dataset.price        || '';

        // Always tag dashboard-originating checkouts so Stripe cancel_url returns here
        var sep = checkoutHref.indexOf('?') >= 0 ? '&' : '?';
        currentCheckoutHref = checkoutHref + sep + 'source=dashboard&dash_level=' + level;

        if (dcmTitle)    dcmTitle.textContent    = 'Unlock ' + levelName;
        if (dcmSubtitle) dcmSubtitle.textContent = 'A few details so we can calibrate your' + (price ? ' ' + price : '') + ' signal analysis.';

        // Show the correct level field group
        if (dcmForm) {
          dcmForm.querySelectorAll('.dcm-level-fields').forEach(function (g) {
            g.classList.remove('is-active');
          });
          var group = dcmForm.querySelector('.dcm-level-fields[data-level="' + level + '"]');
          if (group) group.classList.add('is-active');
        }

        dcmMask.dataset.open = 'true';
        document.body.style.overflow = 'hidden';
        var firstInput = dcmForm && dcmForm.querySelector('.dcm-level-fields.is-active input, .dcm-level-fields.is-active select');
        if (firstInput) { window.setTimeout(function () { firstInput.focus(); }, 80); }
      }

      function closeDcm() {
        dcmMask.dataset.open = 'false';
        document.body.style.overflow = '';
      }

      function skipAndRedirect() {
        closeDcm();
        if (currentCheckoutHref) window.location.href = currentCheckoutHref;
      }

      document.querySelectorAll('.js-dcm-open').forEach(function (btn) {
        btn.addEventListener('click', function () { openDcm(btn); });
      });

      if (dcmClose) dcmClose.addEventListener('click', closeDcm);
      if (dcmSkip)  dcmSkip.addEventListener('click', skipAndRedirect);

      dcmMask.addEventListener('click', function (evt) {
        if (evt.target === dcmMask) closeDcm();
      });

      document.addEventListener('keydown', function (evt) {
        if (evt.key === 'Escape' && dcmMask.dataset.open === 'true') closeDcm();
      });

      if (dcmForm) {
        dcmForm.addEventListener('submit', function (evt) {
          evt.preventDefault();
          if (dcmSubmit) { dcmSubmit.disabled = true; dcmSubmit.textContent = 'Saving…'; }

          var data = new FormData(dcmForm);
          var payload = {};
          data.forEach(function (v, k) { if (k !== '_token') payload[k] = v; });

          fetch('{{ route('app.dashboard.profile-data') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || '',
              'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
          })
          .then(function (res) { return res.json(); })
          .then(function () {
            closeDcm();
            if (currentCheckoutHref) window.location.href = currentCheckoutHref;
          })
          .catch(function () {
            // On error still proceed to checkout
            closeDcm();
            if (currentCheckoutHref) window.location.href = currentCheckoutHref;
          });
        });
      }
    })();

  })();

  // ── Dashboard checkout return: scroll + highlight ──────────────
  (function () {
    var params = new URLSearchParams(window.location.search);
    var isResumed = params.get('checkout_resumed') === '1';
    var isSuccess = params.get('checkout_success') === '1';
    var resumeLevel = parseInt(params.get('resume_level') || '0', 10);

    if (!isResumed && !isSuccess) return;

    // Clean the query params from the URL bar without triggering a reload
    try {
      history.replaceState(null, '', window.location.pathname);
    } catch (e) {}

    if (!resumeLevel) return;

    // Give the page a moment to render, then scroll and highlight
    setTimeout(function () {
      var section = document.getElementById('level-cards');
      if (section) {
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }

      // Find and highlight the target level card (1-based index)
      var cards = document.querySelectorAll('#level-cards .level-card');
      var card = cards[resumeLevel - 1];
      if (!card) return;

      // Gold ring highlight that fades after 3 seconds
      card.style.transition = 'box-shadow 0.4s ease, outline 0.4s ease';
      card.style.outline = '2px solid rgba(200,168,75,0.65)';
      card.style.outlineOffset = '3px';
      setTimeout(function () {
        card.style.outline = '2px solid rgba(200,168,75,0)';
        setTimeout(function () {
          card.style.outline = '';
          card.style.outlineOffset = '';
        }, 600);
      }, 2800);
    }, 650);
  })();
</script>

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

{{-- Data Capture Modal --}}
<div id="dcmMask" class="dcm-mask" role="dialog" aria-modal="true" aria-labelledby="dcmTitle" data-open="false">
  <div class="dcm-shell">
    <button type="button" id="dcmClose" class="dcm-close" aria-label="Close">&times;</button>
    <p class="dcm-kicker">Quick Profile</p>
    <h2 id="dcmTitle" class="dcm-title">Unlock This Layer</h2>
    <p id="dcmSubtitle" class="dcm-subtitle">A few details so we can tailor your signal analysis.</p>
    <form id="dcmForm" novalidate>
      @csrf

      {{-- ── Level 1: Foundation (business identity + location) ── --}}
      <div class="dcm-level-fields" data-level="1">
        <p class="dcm-section-label">Business Identity</p>
        <div class="dcm-field-row">
          <div class="dcm-field">
            <label for="dcm_business_name">Business Name</label>
            <input type="text" id="dcm_business_name" name="business_name" placeholder="e.g. Apex Plumbing Co." autocomplete="organization" maxlength="120"
              value="{{ auth()->user()->profile_data['business_name'] ?? '' }}">
          </div>
          <div class="dcm-field">
            <label for="dcm_business_type">Business Type</label>
            <input type="text" id="dcm_business_type" name="business_type" placeholder="e.g. Plumber, HVAC, Law Firm" maxlength="120"
              value="{{ auth()->user()->profile_data['business_type'] ?? '' }}">
          </div>
        </div>
        <p class="dcm-section-label">Location</p>
        <div class="dcm-field-row">
          <div class="dcm-field">
            <label for="dcm_primary_location">Primary City &amp; State</label>
            <input type="text" id="dcm_primary_location" name="primary_location" placeholder="e.g. Austin, TX" autocomplete="address-level2" maxlength="120"
              value="{{ auth()->user()->profile_data['primary_location'] ?? '' }}">
          </div>
          <div class="dcm-field">
            <label for="dcm_website_url">Website URL</label>
            <input type="url" id="dcm_website_url" name="website_url" placeholder="https://yoursite.com" autocomplete="url" maxlength="250"
              value="{{ auth()->user()->profile_data['website_url'] ?? ($leadDomain !== 'No domain scanned yet' ? 'https://'.$projectDomain : '') }}">
          </div>
        </div>
      </div>

      {{-- ── Level 2: Authority (services + market reach) ── --}}
      <div class="dcm-level-fields" data-level="2">
        <p class="dcm-section-label">Services</p>
        <div class="dcm-field-row">
          <div class="dcm-field">
            <label for="dcm_primary_service">Primary Service</label>
            <input type="text" id="dcm_primary_service" name="primary_service" placeholder="e.g. Emergency plumbing" maxlength="200"
              value="{{ auth()->user()->profile_data['primary_service'] ?? '' }}">
          </div>
          <div class="dcm-field">
            <label for="dcm_core_services">Other Core Services</label>
            <input type="text" id="dcm_core_services" name="core_services" placeholder="e.g. Drain cleaning, water heater repair" maxlength="500"
              value="{{ auth()->user()->profile_data['core_services'] ?? '' }}">
          </div>
        </div>
        <p class="dcm-section-label">Market Coverage</p>
        <div class="dcm-field-row">
          <div class="dcm-field">
            <label for="dcm_service_areas">Service Areas</label>
            <input type="text" id="dcm_service_areas" name="service_areas" placeholder="e.g. Austin, Round Rock, Cedar Park" maxlength="300"
              value="{{ auth()->user()->profile_data['service_areas'] ?? '' }}">
          </div>
          <div class="dcm-field">
            <label for="dcm_target_cities">Target Cities to Grow Into</label>
            <input type="text" id="dcm_target_cities" name="target_cities" placeholder="e.g. Pflugerville, Buda, Kyle" maxlength="300"
              value="{{ auth()->user()->profile_data['target_cities'] ?? '' }}">
          </div>
        </div>
      </div>

      {{-- ── Level 3: Structure (platform + trust signals) ── --}}
      <div class="dcm-level-fields" data-level="3">
        <p class="dcm-section-label">Website Platform</p>
        <div class="dcm-field-row">
          <div class="dcm-field">
            <label for="dcm_cms_platform">CMS / Website Builder</label>
            <select id="dcm_cms_platform" name="cms_platform">
              <option value="">Select platform&hellip;</option>
              @foreach(['WordPress','Squarespace','Wix','Webflow','Shopify','Custom/HTML','Other'] as $p)
              <option value="{{ $p }}" {{ (auth()->user()->profile_data['cms_platform'] ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
              @endforeach
            </select>
          </div>
          <div class="dcm-field">
            <label for="dcm_has_service_pages">Dedicated Service Pages?</label>
            <select id="dcm_has_service_pages" name="has_service_pages">
              <option value="">Select&hellip;</option>
              <option value="yes"     {{ (auth()->user()->profile_data['has_service_pages'] ?? '') === 'yes'     ? 'selected' : '' }}>Yes — one per service</option>
              <option value="partial" {{ (auth()->user()->profile_data['has_service_pages'] ?? '') === 'partial' ? 'selected' : '' }}>Partial — some grouped</option>
              <option value="no"      {{ (auth()->user()->profile_data['has_service_pages'] ?? '') === 'no'      ? 'selected' : '' }}>No — all on one page</option>
            </select>
          </div>
        </div>
        <p class="dcm-section-label">Trust &amp; Presence</p>
        <div class="dcm-field-row">
          <div class="dcm-field">
            <label for="dcm_gbp_active">Google Business Profile?</label>
            <select id="dcm_gbp_active" name="gbp_active">
              <option value="">Select&hellip;</option>
              <option value="yes"   {{ (auth()->user()->profile_data['gbp_active'] ?? '') === 'yes'   ? 'selected' : '' }}>Yes — claimed and active</option>
              <option value="no"    {{ (auth()->user()->profile_data['gbp_active'] ?? '') === 'no'    ? 'selected' : '' }}>No — not set up</option>
              <option value="unsure"{{ (auth()->user()->profile_data['gbp_active'] ?? '') === 'unsure' ? 'selected' : '' }}>Not sure</option>
            </select>
          </div>
          <div class="dcm-field">
            <label for="dcm_has_reviews">Online Reviews?</label>
            <select id="dcm_has_reviews" name="has_reviews">
              <option value="">Select&hellip;</option>
              <option value="yes_google"   {{ (auth()->user()->profile_data['has_reviews'] ?? '') === 'yes_google'   ? 'selected' : '' }}>Yes — Google reviews</option>
              <option value="yes_multiple" {{ (auth()->user()->profile_data['has_reviews'] ?? '') === 'yes_multiple' ? 'selected' : '' }}>Yes — Google + others</option>
              <option value="no"           {{ (auth()->user()->profile_data['has_reviews'] ?? '') === 'no'           ? 'selected' : '' }}>No reviews yet</option>
            </select>
          </div>
        </div>
      </div>

      {{-- ── Level 4: Dominance (goals + intent + consultation readiness) ── --}}
      <div class="dcm-level-fields" data-level="4">
        <p class="dcm-section-label">Goals &amp; Timeline</p>
        <div class="dcm-field-row">
          <div class="dcm-field">
            <label for="dcm_primary_goal">Primary Growth Goal</label>
            <input type="text" id="dcm_primary_goal" name="primary_goal" placeholder="e.g. More local leads, rank for HVAC Austin" maxlength="300"
              value="{{ auth()->user()->profile_data['primary_goal'] ?? '' }}">
          </div>
          <div class="dcm-field">
            <label for="dcm_urgency">Timeline</label>
            <select id="dcm_urgency" name="urgency">
              <option value="">Select&hellip;</option>
              <option value="immediate" {{ (auth()->user()->profile_data['urgency'] ?? '') === 'immediate' ? 'selected' : '' }}>Immediate — need results now</option>
              <option value="soon"      {{ (auth()->user()->profile_data['urgency'] ?? '') === 'soon'      ? 'selected' : '' }}>Soon — next 1&ndash;3 months</option>
              <option value="planning"  {{ (auth()->user()->profile_data['urgency'] ?? '') === 'planning'  ? 'selected' : '' }}>Planning — 3&ndash;6 months out</option>
              <option value="exploring" {{ (auth()->user()->profile_data['urgency'] ?? '') === 'exploring' ? 'selected' : '' }}>Exploring options</option>
            </select>
          </div>
        </div>
        <div class="dcm-field-row">
          <div class="dcm-field">
            <label for="dcm_interest_in_help">Interested in Done-For-You Help?</label>
            <select id="dcm_interest_in_help" name="interest_in_help">
              <option value="">Select&hellip;</option>
              <option value="yes"   {{ (auth()->user()->profile_data['interest_in_help'] ?? '') === 'yes'   ? 'selected' : '' }}>Yes — tell me more</option>
              <option value="maybe" {{ (auth()->user()->profile_data['interest_in_help'] ?? '') === 'maybe' ? 'selected' : '' }}>Maybe — need more info</option>
              <option value="no"    {{ (auth()->user()->profile_data['interest_in_help'] ?? '') === 'no'    ? 'selected' : '' }}>No — doing it myself</option>
            </select>
          </div>
          <div class="dcm-field">
            <label for="dcm_notes">Anything Else?</label>
            <input type="text" id="dcm_notes" name="notes" placeholder="Open questions, specific challenges&hellip;" maxlength="500"
              value="{{ auth()->user()->profile_data['notes'] ?? '' }}">
          </div>
        </div>
      </div>

      <div class="dcm-actions">
        <button type="submit" id="dcmSubmit" class="dcm-btn-primary">Save &amp; Continue</button>
        <button type="button" id="dcmSkip" class="dcm-btn-skip">Skip for Now</button>
      </div>
    </form>
  </div>
</div>

<script>
// ── Return + Reminder System ───────────────────────────────────────────
(function () {
  'use strict';
  var VISIT_KEY   = 'dcm_last_visit';
  var TIER_KEY    = 'dcm_tier_rank';
  var SESSION_KEY = 'dcm_banner_seen';

  var tierRank  = {{ $tierRank }};
  var issues    = {{ $latestIssues }};
  var nextHref  = '{{ $nextUnlockHref }}';
  var nextLabel = tierRank >= 4 ? 'Book Strategy Session'
                : tierRank === 3 ? 'Start Guided Execution'
                : tierRank === 2 ? 'Get My Action Plan'
                : 'Unlock Signal Analysis';

  var lastVisitMs = parseInt(localStorage.getItem(VISIT_KEY) || '0', 10);
  var now         = Date.now();
  var hoursAway   = lastVisitMs ? (now - lastVisitMs) / 3600000 : 0;

  // Record this visit
  try {
    localStorage.setItem(VISIT_KEY, String(now));
    localStorage.setItem(TIER_KEY, String(tierRank));
  } catch (e) {}

  var hasReturned  = lastVisitMs > 0;
  var tierComplete = tierRank >= 4;
  var seenSession  = sessionStorage.getItem(SESSION_KEY);

  // ── Return banner ──────────────────────────────────────────────
  if (hasReturned && !tierComplete && !seenSession) {
    var sub = hoursAway >= 72 ? 'Your visibility hasn\u2019t improved yet \u2014 continue your next step'
            : hoursAway >= 48 ? 'Most users fix this within a day \u2014 your system is waiting'
            : hoursAway >= 12 ? 'You still have unresolved gaps'
            :                   'Pick up where you left off';

    var banner = document.getElementById('dcm-return-banner');
    if (banner) {
      var subEl  = document.getElementById('dcm-rb-sub');
      var bodyEl = document.getElementById('dcm-rb-body');
      var ctaEl  = document.getElementById('dcm-rb-cta');
      if (subEl)  subEl.textContent  = sub;
      if (bodyEl) bodyEl.textContent = 'Your scan found ' + issues + ' gap' + (issues !== 1 ? 's' : '') + '. You\u2019re at step ' + tierRank + ' of 4.';
      if (ctaEl)  { ctaEl.href = nextHref; ctaEl.textContent = nextLabel + ' \u2192'; }
      banner.style.display = 'block';

      var dismissBtn = document.getElementById('dcm-rb-dismiss-btn');
      if (dismissBtn) {
        dismissBtn.addEventListener('click', function () {
          banner.remove();
          try { sessionStorage.setItem(SESSION_KEY, '1'); } catch (e) {}
        });
      }
    }
  }

  // ── Layer nudges (reveal after ≥ 12 h away) ────────────────────
  if (hasReturned && hoursAway >= 12) {
    document.querySelectorAll('.dcm-layer-nudge').forEach(function (el) {
      el.style.display = 'block';
    });
  }
})();
</script>

@if($tierRank >= 4)
<script>
// ── Guided Execution Checklist + Momentum System ───────────────────────
(function () {
  'use strict';
  var STORAGE_KEY    = 'ge_progress_{{ $latestScanned?->id ?? "0" }}';
  var total          = document.querySelectorAll('.ge-item').length;
  var isInteractive  = false;
  var lastMilestonePct = 0;

  function getCompleted() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); } catch(e) { return []; }
  }
  function saveCompleted(ids) {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(ids)); } catch(e) {}
  }

  // ── Milestone message ──────────────────────────────────────────────
  function showMilestoneMsg(text) {
    var el = document.getElementById('ge-milestone-msg');
    if (!el) return;
    el.classList.remove('is-fading');
    el.textContent = text;
    el.classList.add('is-visible');
    clearTimeout(el._fadeTimer);
    el._fadeTimer = setTimeout(function () {
      el.classList.add('is-fading');
      setTimeout(function () { el.classList.remove('is-visible', 'is-fading'); }, 440);
    }, 3600);
  }

  function checkMilestone(count, pct) {
    // Always reveal completion nudge at 100% (page load + live toggle)
    if (pct >= 100) {
      var nudge = document.getElementById('ge-completion-nudge');
      if (nudge) nudge.classList.add('is-visible');
    }
    if (!isInteractive) return;
    var msg = '';
    if      (pct >= 100 && lastMilestonePct < 100) msg = 'Your system is fully executed';
    else if (pct >= 75  && lastMilestonePct < 75)  msg = 'Almost complete';
    else if (pct >= 50  && lastMilestonePct < 50)  msg = 'You\u2019re halfway to a stronger visibility system';
    else if (pct >= 25  && lastMilestonePct < 25)  msg = 'You\u2019ve started fixing real gaps';
    else if (count > 0 && count % 3 === 0 && pct < 100) msg = 'You\u2019re making real progress';
    // Advance watermark so fixed milestones only fire once per bracket
    if      (pct >= 100) lastMilestonePct = 100;
    else if (pct >= 75)  lastMilestonePct = 75;
    else if (pct >= 50)  lastMilestonePct = 50;
    else if (pct >= 25)  lastMilestonePct = 25;
    if (msg) showMilestoneMsg(msg);
  }

  function updateProgress() {
    var done = getCompleted();
    var pct  = total > 0 ? Math.round(done.length / total * 100) : 0;
    var fill  = document.getElementById('ge-progress-fill');
    var label = document.getElementById('ge-progress-label');
    if (fill)  fill.style.width  = pct + '%';
    if (label) label.textContent = done.length + ' of ' + total + ' complete';
    checkMilestone(done.length, pct);
  }

  function applyState() {
    var done = getCompleted();
    document.querySelectorAll('.ge-item').forEach(function(el) {
      var id    = el.getAttribute('data-ge-id');
      var isDone = done.indexOf(id) !== -1;
      el.classList.toggle('is-done', isDone);
      el.setAttribute('aria-pressed', isDone ? 'true' : 'false');
      var icon = el.querySelector('.ge-check-icon');
      if (icon) icon.style.opacity = isDone ? '1' : '0';
    });
    updateProgress();
  }

  window.geToggle = function(el) {
    var id = el.getAttribute('data-ge-id');
    if (!id) return;
    var done   = getCompleted();
    var idx    = done.indexOf(id);
    var adding = idx === -1;
    if (adding) done.push(id); else done.splice(idx, 1);
    saveCompleted(done);
    applyState();
    if (adding) {
      el.classList.add('just-done');
      setTimeout(function () { el.classList.remove('just-done'); }, 750);
    }
  };

  window.geReset = function() {
    saveCompleted([]);
    lastMilestonePct = 0;
    var nudge = document.getElementById('ge-completion-nudge');
    if (nudge) nudge.classList.remove('is-visible');
    applyState();
  };

  // Initial load — seed milestone watermark so already-passed brackets don't re-fire
  applyState();
  var _done = getCompleted();
  var _pct  = total > 0 ? Math.round(_done.length / total * 100) : 0;
  lastMilestonePct = _pct >= 100 ? 100 : _pct >= 75 ? 75 : _pct >= 50 ? 50 : _pct >= 25 ? 25 : 0;
  isInteractive = true;
})();
</script>
@endif

@endsection
