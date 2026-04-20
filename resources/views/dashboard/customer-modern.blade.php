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
    : 'Baseline not established yet. Run your first scan to begin.';
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
  $objectiveTitle = $hasSystem ? ($nextStep ?? 'System active') : 'Baseline not established yet';
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
  $leadState = $leadScore >= 85 ? 'Stable' : ($leadScore >= 60 ? 'Under-optimized' : ($leadScore > 0 ? 'At Risk' : 'Baseline not established yet'));
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
  $nextMoveStep = $nextStep ?? 'Deploy Signal Expansion';
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
  $nextLevelLabel = $nextLayer['label'] ?? 'System Activation Complete';
  $nextLevelPrice = $nextLayer['price'] ?? null;
  $nextLevelHref = $nextRoute ? route($nextRoute) : route('quick-scan.show');
  $levelUnlockMap = [
    'scan-basic' => ['Baseline score and initial visibility status.', 'First set of blockers and quick context.'],
    'signal-expansion' => ['Deeper signal diagnostics.', 'Sharper bottleneck detection and priority fix path.'],
    'structural-leverage' => ['Structural leverage opportunities.', 'Higher-impact correction sequencing.'],
    'system-activation' => ['Full system activation controls.', 'Continuous optimization workflow visibility.'],
  ];
  $nextLevelUnlocks = $nextLayer
    ? ($levelUnlockMap[$nextLayer['key']] ?? ['Deeper visibility controls.', 'More actionable correction guidance.'])
    : ['All core levels are unlocked.', 'Use Scans to review history and compare outcomes.'];
  $showMarketCoverage = ((string) ($currentLayer['key'] ?? 'scan-basic')) !== 'scan-basic';
  // Project identity vars
  $projectDomain = ($leadDomain && $leadDomain !== 'No domain scanned yet')
    ? preg_replace('#^https?://(www\.)?#i', '', rtrim($leadDomain, '/'))
    : null;
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
  .hero-status-item{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;border:1px solid rgba(200,168,75,.18);background:rgba(255,255,255,.03);font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#c4bca7;backdrop-filter:blur(6px)}
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
  .cta-view{border:1px solid rgba(200,168,75,.24);background:rgba(200,168,75,.06);color:#dfd6c1}
  .cta-view:hover{border-color:rgba(200,168,75,.45);background:rgba(200,168,75,.14)}
  .system-card-actions{margin-top:auto;display:flex;gap:7px;padding-top:8px}

  .execution-state{position:absolute;inset:0;display:none;align-items:center;justify-content:center;background:linear-gradient(160deg,rgba(15,12,8,.82),rgba(8,7,5,.9));backdrop-filter:blur(2px);z-index:5}
  .execution-state.active{display:flex}
  .execution-state .chip{display:inline-flex;align-items:center;gap:8px;padding:7px 11px;border-radius:999px;border:1px solid rgba(214,181,84,.44);background:rgba(214,181,84,.15);font-size:10px;letter-spacing:.13em;text-transform:uppercase;color:#ecd9a8}
  .execution-state .chip::before{content:'';width:8px;height:8px;border-radius:999px;background:#d6b15f;box-shadow:0 0 0 0 rgba(214,177,95,.48);animation:execPulse 1.05s ease-in-out infinite}

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
  .readout-flyout-kicker{font-size:.58rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.72)}
  .readout-flyout-close{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:7px 10px;border-radius:8px;border:1px solid rgba(200,168,75,.2);background:rgba(200,168,75,.08);font-size:.62rem;letter-spacing:.12em;text-transform:uppercase;color:#ddd2b8}
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
  .scan-library-kicker{font-size:.68rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(200,168,75,.68)}
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
  .next-action-label{font-size:.6rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.72)}
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
  }
  @media(max-width:768px){
    .system-grid{grid-template-columns:1fr}
    .system-grid-card{min-height:unset}
    .next-move-grid{grid-template-columns:1fr}
    .state-metric-grid{grid-template-columns:1fr}
    .hub-priority-grid{grid-template-columns:1fr}
    .scan-history-grid{grid-template-columns:1fr}
    .hero-domain{max-width:none}
    .hero-score-wrap{flex-direction:column;align-items:flex-start}
    .hero-side-grid,.scan-history-context{grid-template-columns:1fr}
    .telemetry-mini-grid{grid-template-columns:1fr}
    .next-move-command-grid{grid-template-columns:1fr}
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
  .level-rail-step-label{font-size:.55rem;letter-spacing:.13em;text-transform:uppercase;color:rgba(200,168,75,.45);text-align:center}
  .level-rail-step.is-complete .level-rail-step-label,.level-rail-step.is-active .level-rail-step-label{color:rgba(200,168,75,.7)}
  .level-rail-connector{flex:1;height:1px;background:rgba(200,168,75,.18);margin-top:-14px;position:relative;z-index:1}
  .level-rail-connector.is-complete{background:rgba(200,168,75,.5)}

  /* Level card grid */
  .level-card-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
  @media(max-width:780px){.level-card-grid{grid-template-columns:1fr}}

  /* Level cards */
  .level-card{border:1px solid rgba(200,168,75,.18);border-radius:14px;background:linear-gradient(155deg,#181410,#0e0c09 70%);padding:18px;position:relative;overflow:hidden;transition:transform .2s ease,box-shadow .2s ease}
  .level-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.28),transparent)}
  .level-card.state-active{border-color:rgba(200,168,75,.5);background:linear-gradient(155deg,#1d1a0f,#100e08 68%);box-shadow:0 12px 32px rgba(0,0,0,.32),0 0 0 1px rgba(200,168,75,.08) inset}
  .level-card.state-active::before{background:linear-gradient(90deg,transparent,rgba(200,168,75,.6),transparent)}
  .level-card.state-complete{border-color:rgba(106,175,144,.3);background:linear-gradient(155deg,#101a14,#090d0b 70%)}
  .level-card.state-complete::before{background:linear-gradient(90deg,transparent,rgba(106,175,144,.4),transparent)}
  .level-card.state-locked{opacity:.58}
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
  .level-card-desc{font-size:.78rem;line-height:1.55;color:#9a9082;margin-bottom:14px}

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
  .consult-cta-btn{flex-shrink:0;display:inline-flex;align-items:center;gap:8px;min-height:40px;padding:0 18px;border-radius:10px;background:transparent;border:1px solid rgba(200,168,75,.5);color:#c6a85a;text-decoration:none;font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;transition:all .2s ease;white-space:nowrap}
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
  .dcm-btn-skip{min-height:42px;padding:0 14px;border-radius:10px;background:transparent;border:1px solid rgba(200,168,75,.22);color:rgba(200,168,75,.55);font-size:.68rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;transition:all .15s ease}
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
  .proj-identity-bar{padding:16px 18px;background:linear-gradient(140deg,rgba(28,22,12,.96),rgba(10,8,5,.98));border-color:rgba(200,168,75,.28);box-shadow:0 8px 28px rgba(0,0,0,.32),inset 0 1px 0 rgba(255,255,255,.03)}
  .pib-live-row{display:flex;align-items:center;gap:8px;margin-bottom:8px}
  .pib-live-dot{width:8px;height:8px;border-radius:50%;background:#c8a84b;flex-shrink:0;animation:pibPulse 2.4s ease-in-out infinite;box-shadow:0 0 0 0 rgba(200,168,75,.5)}
  .pib-live-label{font-size:.58rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.72)}
  .pib-domain-name{font-size:1.35rem;font-weight:700;color:#f3ecd8;letter-spacing:-.025em;line-height:1.1;text-shadow:0 0 28px rgba(200,168,75,.18)}
  .pib-right{display:flex;flex-direction:column;align-items:flex-end;gap:10px}
  .pib-report-btn{display:inline-flex;align-items:center;gap:7px;min-height:40px;padding:0 18px;border-radius:10px;background:#c6a85a;color:#1a1a1a;text-decoration:none;font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;transition:all .2s ease;box-shadow:0 6px 16px rgba(198,168,90,.24),inset 0 1px 0 rgba(255,255,255,.14);white-space:nowrap;flex-shrink:0}
  .pib-report-btn:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(198,168,90,.38),inset 0 1px 0 rgba(255,255,255,.18)}
  .pib-report-btn-outline{display:inline-flex;align-items:center;gap:6px;min-height:38px;padding:0 16px;border-radius:10px;border:1px solid rgba(200,168,75,.32);background:transparent;color:rgba(200,168,75,.8);text-decoration:none;font-size:.66rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;transition:all .2s ease;white-space:nowrap;flex-shrink:0}
  .pib-report-btn-outline:hover{background:rgba(200,168,75,.1);border-color:rgba(200,168,75,.55)}
  @keyframes pibPulse{0%,100%{box-shadow:0 0 0 0 rgba(200,168,75,.55)}50%{box-shadow:0 0 0 9px rgba(200,168,75,0)}}
  /* Domain glow on scan cards */
  .scan-history-card .domain{text-shadow:0 0 22px rgba(200,168,75,.15)}
  .scan-history-card:hover .domain{text-shadow:0 0 30px rgba(200,168,75,.32);color:#fff8ec}
  /* DCM select style */
  .dcm-field select{width:100%;background:#0f0d09;border:1px solid rgba(200,168,75,.28);border-radius:8px;padding:9px 12px;color:#ede8de;font-size:.84rem;outline:none;transition:border-color .15s ease;-webkit-appearance:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='rgba(200,168,75,0.5)' stroke-width='1.4' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center}
  .dcm-field select:focus{border-color:rgba(200,168,75,.65)}
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

    @if($hasSystem)

    {{-- Project Identity Bar --}}
    @if($projectDomain)
    <div class="proj-identity-bar" role="region" aria-label="Project: {{ $projectDomain }}">
      <div class="proj-identity-left" style="flex:1;min-width:0">
        <div class="pib-live-row">
          <span class="pib-live-dot" aria-hidden="true"></span>
          <span class="pib-live-label">AI Visibility System Active</span>
        </div>
        @if($profileBrand)<p class="proj-identity-brand" style="margin-bottom:3px">{{ $profileBrand }}</p>@endif
        <p class="pib-domain-name">{{ $projectDomain }}</p>
      </div>
      <div class="pib-right">
        <div class="proj-identity-meta">
          @if($scanCompletedLabel)
          <span class="proj-identity-pill">Scanned {{ $scanCompletedLabel }}</span>
          @endif
          @if($pagesAnalyzed > 0)
          <span class="proj-identity-pill">{{ $pagesAnalyzed }} pages</span>
          @endif
          <span class="proj-identity-pill">Level {{ $tierRank }}</span>
        </div>
        @if($leadRenderable)
        <a href="{{ $leadReportHref }}" class="pib-report-btn">
          Open Latest Report
          <svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true"><path d="M2 5h6M5.5 2.5 8 5 5.5 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
        @else
        <a href="{{ route('quick-scan.show') }}" class="pib-report-btn-outline">
          Run Scan to Activate Report
        </a>
        @endif
      </div>
    </div>
    @else
    <p class="mb-5 text-xs uppercase tracking-[0.14em] text-[#c8a84b]/72">Your previous scans are ready.</p>
    @endif

    <div class="dashboard-primary-flow {{ $isScansView ? 'is-scans-view' : '' }} {{ $isReportsView ? 'is-reports-view' : '' }}">
    <section class="system-section system-section-primary mb-8 dash-section-anchor" id="system-state">
      <div class="system-unified-module control-hero surface-reveal is-visible">
        <div class="hero-command-deck">
        <div class="hero-grid">
          <div class="hero-main">
            <div class="hero-status-strip">
              <span class="surface-focus-kicker">{{ $projectDomain ?? 'AI Visibility Dashboard' }}</span>
              <span class="hero-status-item">Scan completed {{ $leadLastEvaluation }}</span>
            </div>

            <div>
              <p class="hero-overline">Latest Completed Scan</p>
              <h1 class="hero-domain">{{ $heroHeadline }}</h1>
              <p class="hero-intro">{{ $projectDomain ? 'Showing your current AI visibility state for '.$projectDomain.'. Your top blocker and fastest improvement path are below.' : 'Your current AI visibility state is ready. Top blockers and your fastest improvement path are below.' }}</p>
            </div>

            <div class="hero-bottleneck-panel">
              <p class="hero-bottleneck-label">Primary Bottleneck</p>
              <p class="hero-bottleneck-copy">{{ $leadBottleneck }}</p>
            </div>
          </div>

          <aside class="hero-side surface-reveal is-visible">
            <div class="hero-score-panel">
              <p class="hero-panel-label">AI Visibility Score</p>
              <div class="hero-score-wrap">
                <div class="hero-score-orb {{ $leadTelemetryTone }}">
                  <span class="hero-score-value">{{ $leadScore > 0 ? $leadScore : '--' }}</span>
                  <span class="hero-score-caption">Score</span>
                </div>
                <div class="hero-score-meta">
                  <div class="hero-telemetry">
                    <p>Status</p>
                    <p class="telemetry-emphasis">{{ $leadState }}</p>
                  </div>
                  <div class="hero-telemetry">
                    <p>Completed</p>
                    <p>{{ $leadLastEvaluation }}</p>
                  </div>
                </div>
              </div>
              @if($scanCompletedLabel)
              <p class="scan-provenance">Based on scan completed {{ $scanCompletedLabel }}{{ $pagesAnalyzed > 0 ? ' &nbsp;&middot;&nbsp; '.$pagesAnalyzed.' pages' : '' }}</p>
              @endif
              <div class="hero-side-grid">
                <article class="hero-side-metric">
                  <p>Visibility</p>
                  <p>{{ $leadState }}</p>
                </article>
                <article class="hero-side-metric">
                  @if($pagesAnalyzed > 0)
                  <p>Pages Scanned</p>
                  <p>{{ $pagesAnalyzed }}</p>
                  @else
                  <p>Last Scan</p>
                  <p>{{ $latestEvaluatedLabel }}</p>
                  @endif
                </article>
              </div>
            </div>

            <div class="next-action-block">
              <p class="next-action-label">Next Action</p>
              <p class="next-action-copy">{{ $projectDomain ? 'Your top blocker for '.$projectDomain.' is limiting AI visibility.' : 'Fix your primary content signal to improve your AI visibility score.' }}</p>
              <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px">
                @if($leadRenderable)
                <a href="{{ $leadReportHref }}" class="next-action-cta" style="margin-top:0">Open Latest Report &rarr;</a>
                <a href="{{ $nextMoveActionHref }}" class="hero-secondary-cta" style="min-height:42px;font-size:.7rem">See Top Fix</a>
                @else
                <a href="{{ $nextMoveActionHref }}" class="next-action-cta" style="margin-top:0">Fix This Now</a>
                @endif
              </div>
            </div>
          </aside>
        </div>
        </div>
        <p class="mt-4 text-[11px] uppercase tracking-[0.16em] text-[#988f7c]">Last evaluated: {{ $latestEvaluatedLabel }}</p>
      </div>
    </section>

        @if($isSystemView)
    @php
      $levelMeta = [
        ['key' => 'scan-basic',         'num' => 1, 'kicker' => 'Level 1',  'name' => 'Foundation Signals', 'desc' => 'Establishes your core content signal — business identity, services, and location data for AI discovery.',  'why' => 'AI systems look for clear business identity, service clarity, and a location signal. Without this, your site is invisible to AI-powered search.', 'steps' => ['Baseline visibility score established', 'Primary service signal validated', 'Location targeting activated'], 'lift' => '+12 visibility pts', 'price' => '$2'],
        ['key' => 'signal-expansion',   'num' => 2, 'kicker' => 'Level 2',  'name' => 'Authority Signals',  'desc' => 'Expands authority coverage across citation layers and validates your market position signals.',            'why' => 'Authority signals tell AI systems you are a real, trusted business in your market. Citation gaps are the #1 reason local businesses are skipped.', 'steps' => ['Citation authority layer active', 'Competitor gap analysis unlocked', 'Secondary market signals validated'], 'lift' => '+18 visibility pts', 'price' => '$99'],
        ['key' => 'structural-leverage','num' => 3, 'kicker' => 'Level 3',  'name' => 'Expansion Signals',  'desc' => 'Builds structural web presence with schema alignment, topical depth, and conversion architecture.',        'why' => 'Structured data and topical coverage are what move you from "found sometimes" to "consistently cited" in AI and voice search results.', 'steps' => ['Schema and structured data mapped', 'Topical authority stack initiated', 'Conversion pathway architecture active'], 'lift' => '+22 visibility pts', 'price' => '$249'],
        ['key' => 'system-activation',  'num' => 4, 'kicker' => 'Level 4',  'name' => 'Dominance Layer',    'desc' => 'Activates competitive suppression, AI citation eligibility, and full market coverage controls.',           'why' => 'This is where you go from visible to dominant — AI citation eligibility, competitive suppression, and full market coverage running as a system.', 'steps' => ['AI citation eligibility unlocked', 'Competitive suppression active', 'Full market coverage operational'], 'lift' => '+28 visibility pts', 'price' => '$489'],
      ];
      $layersByKey = collect($analysisLayers ?? [])->keyBy('key');
      $firstIncompleteIdx = null;
      foreach ($levelMeta as $idx => $lm) {
        $layerData = $layersByKey->get($lm['key']);
        if (! (bool) ($layerData['complete'] ?? false)) {
          $firstIncompleteIdx = $idx;
          break;
        }
      }
      $checkoutRoutes = [
        'scan-basic'          => 'checkout.scan-basic',
        'signal-expansion'    => 'checkout.signal-expansion',
        'structural-leverage' => 'checkout.structural-leverage',
        'system-activation'   => 'checkout.system-activation',
      ];
    @endphp

    {{-- Level Progression Rail --}}
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="level-rail-section">
      <div class="system-subshell" style="padding:20px 24px">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px">
          <div>
            <p style="font-size:.58rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.65);margin-bottom:4px">Your Progression Path</p>
            <p style="font-size:.9rem;font-weight:600;color:#ede8de">Start Here: Fix Your Top Blocker</p>
          </div>
          <span style="font-size:.62rem;letter-spacing:.14em;text-transform:uppercase;padding:4px 10px;border-radius:20px;background:rgba(200,168,75,.12);border:1px solid rgba(200,168,75,.28);color:rgba(200,168,75,.8);white-space:nowrap">Level {{ $tierRank }} Active</span>
        </div>
        <div class="level-rail">
          @foreach($levelMeta as $idx => $lm)
            @php
              $layerData = $layersByKey->get($lm['key']);
              $isComplete = (bool) ($layerData['complete'] ?? false);
              $isActive   = $idx === $firstIncompleteIdx;
              $railState  = $isComplete ? 'is-complete' : ($isActive ? 'is-active' : 'is-locked');
              $connectorClass = $isComplete ? 'level-rail-connector is-complete' : 'level-rail-connector';
            @endphp
            <div class="level-rail-step {{ $railState }}">
              <div class="level-rail-step-dot">
                @if($isComplete)
                  <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M1.5 5l2.5 2.5 4.5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                @elseif($isActive)
                  <svg width="8" height="8" viewBox="0 0 8 8" fill="none"><circle cx="4" cy="4" r="3" fill="currentColor"/></svg>
                @else
                  <svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M4.5 1a1.75 1.75 0 0 1 1.75 1.75V4h-3.5V2.75A1.75 1.75 0 0 1 4.5 1z" stroke="currentColor" stroke-width="1.2"/><rect x="2" y="4" width="5" height="4" rx="1" stroke="currentColor" stroke-width="1.2"/></svg>
                @endif
              </div>
              <span class="level-rail-step-label">L{{ $lm['num'] }}</span>
            </div>
            @if(! $loop->last)
              <div class="{{ $connectorClass }}"></div>
            @endif
          @endforeach
        </div>
      </div>
    </section>

    {{-- Level Cards Grid --}}
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="level-cards">
      <div class="level-card-grid">
        @foreach($levelMeta as $idx => $lm)
          @php
            $layerData = $layersByKey->get($lm['key']);
            $isComplete = (bool) ($layerData['complete'] ?? false);
            $isActive   = $idx === $firstIncompleteIdx;
            $isLocked   = ! $isComplete && ! $isActive;
            $cardState  = $isComplete ? 'state-complete' : ($isActive ? 'state-active' : 'state-locked');
            $badgeClass = $isComplete ? 'badge-completed' : ($isActive ? 'badge-ready' : 'badge-locked');
            $badgeLabel = $isComplete ? 'Completed' : ($isActive ? 'Ready' : 'Locked');
            $checkoutHref = (isset($checkoutRoutes[$lm['key']]) && \Route::has($checkoutRoutes[$lm['key']])) ? route($checkoutRoutes[$lm['key']]) : $nextUnlockHref;
            $reportHref = ($leadRenderable && $leadRouteKey) ? route('dashboard.scans.show', ['scan' => $leadRouteKey]) : $leadReportHref;
          @endphp
          <article class="level-card {{ $cardState }}" aria-label="{{ $lm['name'] }} level card">
            <div class="level-card-accent" aria-hidden="true"></div>
            <span class="level-state-badge {{ $badgeClass }}">
              @if($isComplete)
                <svg width="8" height="8" viewBox="0 0 8 8" fill="none"><path d="M1 4l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              @elseif($isActive)
                <svg width="6" height="6" viewBox="0 0 6 6" fill="none"><circle cx="3" cy="3" r="2.5" fill="currentColor"/></svg>
              @else
                <svg width="8" height="8" viewBox="0 0 8 8" fill="none"><path d="M4 0.75a1.5 1.5 0 0 1 1.5 1.5V3.5h-3V2.25A1.5 1.5 0 0 1 4 .75z" stroke="currentColor" stroke-width="1.1"/><rect x="1.5" y="3.5" width="5" height="3.75" rx=".75" stroke="currentColor" stroke-width="1.1"/></svg>
              @endif
              {{ $badgeLabel }}
            </span>

            <p class="level-card-kicker">{{ $lm['kicker'] }}</p>
            <h3 class="level-card-name">{{ $lm['name'] }}</h3>
            <p class="level-card-desc">{{ $lm['desc'] }}</p>

            @if($isActive && !empty($lm['why']))
            <p class="level-card-why">{{ $lm['why'] }}</p>
            @endif

            <ul class="level-card-steps" aria-label="Level steps">
              @foreach($lm['steps'] as $step)
              <li class="level-card-step">
                <span class="level-card-step-icon" aria-hidden="true">
                  @if($isComplete)
                    <svg width="8" height="8" viewBox="0 0 8 8" fill="none"><path d="M1 4l2 2 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  @elseif($isActive)
                    <svg width="6" height="6" viewBox="0 0 6 6" fill="none"><circle cx="3" cy="3" r="2" fill="currentColor"/></svg>
                  @else
                    <svg width="6" height="6" viewBox="0 0 6 6" fill="none"><rect x=".75" y=".75" width="4.5" height="4.5" rx="1" stroke="currentColor" stroke-width="1.1"/></svg>
                  @endif
                </span>
                <span>{{ $step }}</span>
              </li>
              @endforeach
            </ul>

            <div class="level-card-lift">
              <svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M4.5 1v7M1.5 3.5l3-3 3 3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
              {{ $lm['lift'] }}
            </div>

            @if($isComplete)
              <a href="{{ $reportHref }}" class="level-card-cta-secondary">View Report &rarr;</a>
            @elseif($isActive)
              <button type="button"
                class="level-card-cta-primary js-dcm-open"
                data-level="{{ $lm['num'] }}"
                data-level-name="{{ $lm['name'] }}"
                data-checkout-href="{{ $checkoutHref }}"
                data-price="{{ $lm['price'] }}">
                Unlock This Layer
              </button>
            @else
              <div class="level-card-cta-disabled" aria-disabled="true">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true"><path d="M5 0.875a2 2 0 0 1 2 2V4.5H3V2.875A2 2 0 0 1 5 .875z" stroke="currentColor" stroke-width="1.2"/><rect x="1.5" y="4.5" width="7" height="5" rx="1" stroke="currentColor" stroke-width="1.2"/></svg>
                Unlock Previous Level First
              </div>
            @endif
          </article>
        @endforeach
      </div>
    </section>

    {{-- Premium Gate (shown until Level 3 is achieved) --}}
    @if($tierRank < 3)
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="premium-gate">
      <div class="premium-gate-card">
        <p class="premium-gate-kicker">Premium Intelligence</p>
        <h3 class="premium-gate-title">Unlock Full Competitive Suppression</h3>
        <p class="premium-gate-desc">Levels 3 and 4 unlock structural leverage, AI citation eligibility, and competitive takedown sequencing &mdash; the tools that move you from visible to dominant.</p>
        <a href="{{ $nextUnlockHref }}" class="premium-gate-cta">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M7 1a3 3 0 0 1 3 3v2H4V4A3 3 0 0 1 7 1z" stroke="currentColor" stroke-width="1.4"/><rect x="2" y="6" width="10" height="7" rx="1.5" stroke="currentColor" stroke-width="1.4"/><circle cx="7" cy="9.5" r="1" fill="currentColor"/></svg>
          Unlock Levels 3 &amp; 4
        </a>
      </div>
    </section>
    @endif

    {{-- Consultation CTA Banner --}}
    <section class="system-section mb-6 dash-section-anchor surface-reveal" id="consult-cta">
      <div class="consult-cta-banner">
        <div class="consult-cta-copy">
          <p>Need expert guidance?</p>
          <p>Apply your system fixes with us &mdash; book a strategy session and we&rsquo;ll execute the corrections for you.</p>
        </div>
        <a href="{{ route('book.index') }}?entry=dashboard-level-system" class="consult-cta-btn">
          Book a Strategy Session &rarr;
        </a>
      </div>
    </section>

    @endif

    @if($isReportsView)
    <section class="system-section mb-8 dash-section-anchor surface-reveal" id="report-readouts">
      <div class="ia-progress-shell">
        <div class="ia-progress-head">
          <div>
            <h2>Progression and Unlock Path</h2>
            <p>Reports is repurposed for level progression only: current level, next level, what unlocks next, and the direct upgrade path.</p>
          </div>
          <span class="scan-library-pill">Current Level <strong>{{ $currentLevelLabel }}</strong></span>
        </div>
        <div class="ia-level-grid">
          <article class="ia-level-card">
            <p>Current Level</p>
            <p>{{ $currentLevelLabel }}</p>
          </article>
          <article class="ia-level-card">
            <p>Next Level</p>
            <p>{{ $nextLevelLabel }}{{ $nextLevelPrice ? (' · ' . $nextLevelPrice) : '' }}</p>
          </article>
        </div>
        <div class="ia-level-unlocks">
          <p>Next Level Unlocks</p>
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
        <p class="onboarding-command-reassure">See your score, biggest gaps, and first fix in seconds.</p>
        <p class="onboarding-command-footnote">Takes 10 seconds • $2 scan</p>
        <details class="onboarding-explainer">
          <summary>What will the scan show?</summary>
          <div class="onboarding-explainer-panel">
            <ul>
              <li>Your current visibility score baseline.</li>
              <li>The biggest gaps limiting AI retrieval.</li>
              <li>The first highest-impact fix to apply.</li>
            </ul>
          </div>
        </details>
        <div class="onboarding-proof-strip" aria-hidden="true">
          <article class="onboarding-proof-item">
            <p>AI Check</p>
            <p>Visibility score baseline</p>
          </article>
          <article class="onboarding-proof-item">
            <p>AI Check</p>
            <p>Primary gap identification</p>
          </article>
          <article class="onboarding-proof-item">
            <p>AI Check</p>
            <p>Fastest first correction</p>
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
              <p class="mt-1 text-xs text-[#9f9785]">Priority auto-ranked by selection pressure.</p>
            </div>
            <div class="flex flex-wrap gap-2">
              <span class="inline-flex items-center rounded-lg border border-[#c8a84b]/22 px-3 py-2 text-xs font-semibold tracking-[0.08em] text-[#dfd6c1]">{{ $systemCount }} systems</span>
              <a href="{{ route('for-agencies') }}" class="inline-flex items-center rounded-lg border border-[#c8a84b]/30 px-3 py-2 text-xs font-semibold tracking-[0.08em] text-[#dfd6c1] transition hover:border-[#c8a84b] hover:bg-[#c8a84b]/10">Client load detected? Deploy additional systems.</a>
            </div>
          </div>

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
                $rankToLayerName = [2 => 'Signal Expansion', 3 => 'Structural Leverage', 4 => 'System Activation'];
                $rankToPlan = [2 => 'diagnostic', 3 => 'fix-strategy', 4 => 'optimization'];
                $rankToPrice = [2 => '$99', 3 => '$249', 4 => '$489'];
                $rankToCheckoutRoute = [2 => 'checkout.signal-expansion', 3 => 'checkout.structural-leverage', 4 => 'checkout.system-activation'];
                $scanTierRank = (int) ($scan['tier_rank'] ?? $tierRank);
                $includedLayerSummary = match (true) {
                  $scanTierRank >= 4 => 'All system layers active for this readout.',
                  $scanTierRank === 3 => 'Structural leverage and signal diagnostics active.',
                  $scanTierRank === 2 => 'Signal diagnostics active with baseline layer.',
                  default => 'Baseline readout active only.',
                };
                $lockedLayerSummary = match (true) {
                  $scanTierRank >= 4 => 'No locked layers. Expansion path is active.',
                  $scanTierRank === 3 => 'System Activation remains as next unlock.',
                  $scanTierRank === 2 => 'Structural Leverage remains locked.',
                  default => 'Signal Expansion remains locked.',
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
                  $correctionLabel = 'View Correction Path';
                  $postCorrectionLabel = 'Advance System Layer';
                  $nextPathLine = 'Current path: Constraint Resolution';
                  $correctionHref = null;
                  $modalTitle = 'Expansion Opportunity';
                  $modalUnlockEffect = 'System fully active. Correction now means expanding execution pathways.';
                  $modalPrice = 'No additional unlock required';
                  $modalPrimaryHref = $inspectHref . '#sys-actions';
                  $modalPrimaryLabel = 'Open Expansion Opportunities';
                } elseif ($scanTierRank >= $suggestedCorrectionRank) {
                  $correctionActionType = 'unlocked';
                  $correctionLabel = 'Open Correction Path';
                  $postCorrectionLabel = 'Continue Optimization';
                  $nextPathLine = 'Next path: ' . ($rankToLayerName[$suggestedCorrectionRank] ?? 'Signal Expansion');
                  $correctionHref = $inspectHref . '#' . ($rankToLayerAnchor[$suggestedCorrectionRank] ?? 'detailed-layer-view');
                } else {
                  $correctionActionType = 'locked';
                  $correctionLabel = 'Unlock Correction Path';
                  $postCorrectionLabel = 'Correction Engaged';
                  $nextPathLine = 'Next unlock: ' . ($rankToLayerName[$suggestedCorrectionRank] ?? 'Signal Expansion');
                  $targetPlan = $rankToPlan[$suggestedCorrectionRank] ?? 'diagnostic';
                  $modalTitle = 'Unlock ' . ($rankToLayerName[$suggestedCorrectionRank] ?? 'Signal Expansion');
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
                  $modalPrimaryLabel = 'Unlock ' . ($rankToLayerName[$suggestedCorrectionRank] ?? 'Signal Expansion') . ' - ' . ($rankToPrice[$suggestedCorrectionRank] ?? '');
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
                  <span class="chip">Processing correction path</span>
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
                <p class="selection-subline">Selection active. Retrieval pressure ongoing.</p>
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
                      data-feedback-line="Advance system layer"
                    >{{ $correctionLabel }}</button>
                  @elseif($correctionActionType === 'locked')
                    <button
                      type="button"
                      class="system-grid-cta cta-unlock js-correction-action"
                      data-resolve-type="redirect"
                      data-resolve-href="{{ $correctionHref }}"
                      data-post-label="{{ $postCorrectionLabel }}"
                      data-feedback-line="Correction path engaged"
                    >{{ $correctionLabel }}</button>
                  @else
                    <button
                      type="button"
                      class="system-grid-cta cta-fix js-correction-action"
                      data-resolve-type="redirect"
                      data-resolve-href="{{ $correctionHref }}"
                      data-post-label="{{ $postCorrectionLabel }}"
                      data-feedback-line="Signal reinforcement initiated"
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
          <h3 class="mt-1 text-lg font-semibold">Deploy Fix on At-Risk Readouts</h3>
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
      <a id="correctionPathPrimary" href="{{ route('quick-scan.show') }}" class="system-grid-cta cta-fix">Continue Correction</a>
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
        <button type="button" class="readout-action-btn readout-action-correction" id="readoutFlyoutCorrection">View Correction Path</button>
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
        correctionLabel: card.dataset.correctionLabel || 'View Correction Path',
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
      const primaryLabel = btn.dataset.primaryLabel || 'Continue Correction';

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
        button.textContent = postLabel || 'Correction Engaged';
        button.disabled = false;
      }

      const responseLine = card.querySelector('.card-response-line');
      if (responseLine) {
        responseLine.textContent = feedbackLine || 'System responding to adjustment';
        responseLine.classList.add('live');
      }

      const memoryLine = card.querySelector('.action-memory-line');
      if (memoryLine) {
        memoryLine.textContent = 'Correction initiated recently';
        memoryLine.classList.add('is-fresh');
      }

      const scanKey = card.dataset.scanKey || '';
      if (scanKey !== '') {
        const payload = {
          at: atMillis,
          feedback: feedbackLine || 'System responding to adjustment',
          label: postLabel || 'Correction Engaged',
        };
        sessionStorage.setItem('correction-state:' + scanKey, JSON.stringify(payload));
      }
    }

    function runCorrectionCommit(button) {
      const card = button.closest('.system-grid-card');
      if (!card) return;

      const resolveType = button.dataset.resolveType || 'redirect';
      const resolveHref = button.dataset.resolveHref || '';
      const postLabel = button.dataset.postLabel || 'Correction Engaged';
      const feedbackLine = button.dataset.feedbackLine || 'System responding to adjustment';

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

      const raw = sessionStorage.getItem('correction-state:' + scanKey);
      if (!raw) return;

      try {
        const parsed = JSON.parse(raw);
        const elapsedMs = Date.now() - Number(parsed.at || 0);

        if (!Number.isFinite(elapsedMs) || elapsedMs < 0 || elapsedMs > 30 * 60 * 1000) {
          sessionStorage.removeItem('correction-state:' + scanKey);
          return;
        }

        card.classList.add('is-engaged');

        const responseLine = card.querySelector('.card-response-line');
        if (responseLine) {
          responseLine.textContent = parsed.feedback || 'System responding to adjustment';
          responseLine.classList.add('live');
        }

        const actionBtn = card.querySelector('.js-correction-action, .js-open-correction-modal');
        if (actionBtn && parsed.label) {
          actionBtn.textContent = parsed.label;
        }

        const mins = Math.max(1, Math.floor(elapsedMs / 60000));
        const memoryLine = card.querySelector('.action-memory-line');
        if (memoryLine) {
          memoryLine.textContent = 'Last action: ' + mins + ' minute' + (mins === 1 ? '' : 's') + ' ago';
          memoryLine.classList.add('is-fresh');
        }
      } catch (err) {
        sessionStorage.removeItem('correction-state:' + scanKey);
      }
    });

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
        currentCheckoutHref = checkoutHref;

        if (dcmTitle)    dcmTitle.textContent    = 'Unlock ' + levelName;
        if (dcmSubtitle) dcmSubtitle.textContent = 'A few details so we can calibrate your' + (price ? ' ' + price : '') + ' signal expansion.';

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
</script>

{{-- Data Capture Modal --}}
<div id="dcmMask" class="dcm-mask" role="dialog" aria-modal="true" aria-labelledby="dcmTitle" data-open="false">
  <div class="dcm-shell">
    <button type="button" id="dcmClose" class="dcm-close" aria-label="Close">&times;</button>
    <p class="dcm-kicker">Quick Profile</p>
    <h2 id="dcmTitle" class="dcm-title">Unlock This Layer</h2>
    <p id="dcmSubtitle" class="dcm-subtitle">A few details so we can tailor your signal expansion.</p>
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

@endsection
