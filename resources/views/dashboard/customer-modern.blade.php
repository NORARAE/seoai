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
    : 'No active signals. Baseline detection required.';
  $latestEvaluatedAt = $latestScan?->scanned_at ?? $latestScan?->created_at;
  $latestEvaluatedLabel = $latestEvaluatedAt ? $latestEvaluatedAt->diffForHumans() : 'Awaiting first evaluation';
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
  $objectiveTitle = $hasSystem ? ($nextStep ?? 'System active') : 'No system deployed';
  $objectiveCta = $hasSystem ? ($nextRoute ? route($nextRoute) : route('quick-scan.show')) : route('quick-scan.show');
  $objectiveCtaLabel = $hasSystem ? ($nextRoute ? 'Unlock Next Layer' : 'Run New Scan') : 'Run First Scan';
  $secondaryCtaLabel = $hasSystem ? 'Book Consultation' : 'Start System Build';
  $reportReadyScans = $scanHistory->where('is_renderable_report', true)->values();
  $leadScan = $reportReadyScans->first() ?? $scanHistory->first();
  $leadDomain = $leadScan['scan_name'] ?? $leadScan['domain'] ?? 'No domain scanned yet';
  $leadScore = (int) ($leadScan['score'] ?? 0);
  $leadState = $leadScore >= 85 ? 'Stable' : ($leadScore >= 60 ? 'Under-optimized' : ($leadScore > 0 ? 'At Risk' : 'Awaiting baseline'));
  $leadBottleneck = trim((string) ($leadScan['fastest_fix'] ?? '')) !== ''
    ? $leadScan['fastest_fix']
    : 'No bottleneck detected yet. Run a scan to establish baseline constraints.';
  $leadRouteKey = $leadScan['scan_route_key'] ?? $leadScan['public_scan_id'] ?? $leadScan['system_scan_id'] ?? null;
  $leadReportHref = ($leadRouteKey && (bool) ($leadScan['is_renderable_report'] ?? false))
    ? route('dashboard.scans.show', ['scan' => $leadRouteKey])
    : route('quick-scan.show');
  $nextUnlockLabel = $nextStep ?? 'Continue improvement loop';
  $nextUnlockHref = $nextRoute ? route($nextRoute) : $leadReportHref;
  $scanFocusList = $reportReadyScans->take(6);
  $isScansView = request()->is('dashboard/scans');
  $isReportsView = request()->is('dashboard/reports');
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
  .scan-history-shell{border:1px solid rgba(200,168,75,.2);border-radius:16px;background:linear-gradient(155deg,#141108,#0c0a06 72%);padding:16px}
  .scan-history-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;margin-top:12px}
  .scan-history-card{border:1px solid rgba(200,168,75,.2);border-radius:12px;background:linear-gradient(152deg,#1a140c,#110d08 68%);padding:12px;display:flex;flex-direction:column;gap:8px}
  .scan-history-card .meta{font-size:10px;letter-spacing:.13em;text-transform:uppercase;color:#ab9f84}
  .scan-history-card .domain{font-size:15px;font-weight:600;color:#efe6d1;line-height:1.3}
  .scan-history-card .bottleneck{font-size:12px;color:#d7ccb4;line-height:1.45}
  .scan-history-card .state-row{display:flex;align-items:center;justify-content:space-between;gap:8px}
  .scan-history-card .pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:4px 8px;font-size:10px;letter-spacing:.12em;text-transform:uppercase;border:1px solid rgba(200,168,75,.28);color:#e6d4a5;background:rgba(200,168,75,.12)}
  .scan-history-card .score{font-size:12px;font-weight:700;color:#f1e7cd}
  .scan-history-card .actions{display:flex;gap:8px;flex-wrap:wrap;padding-top:2px}
  .scan-history-card .actions a{display:inline-flex;align-items:center;justify-content:center;min-height:32px;padding:7px 10px;border-radius:8px;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-decoration:none}
  .scan-history-card .open{border:1px solid rgba(200,168,75,.4);background:rgba(200,168,75,.16);color:#f3e8cb}
  .scan-history-card .deploy{border:1px solid rgba(106,175,144,.44);background:rgba(106,175,144,.16);color:#bfe3d2}
  .scan-history-card .inspect{border:1px solid rgba(200,168,75,.24);background:rgba(200,168,75,.08);color:#ddd3bc}
  .operations-quiet{opacity:.78}
  .surface-focus-kicker{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;border:1px solid rgba(200,168,75,.32);background:rgba(200,168,75,.12);font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#e3d0a0}
  .surface-focus-kicker::before{content:'';width:7px;height:7px;border-radius:999px;background:#c8a84b}
  .system-grid-toolbar{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:14px}
  .system-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px}
  .system-grid.grid-compact{grid-template-columns:repeat(auto-fill,minmax(220px,1fr))}
  .system-grid.grid-wide{grid-template-columns:repeat(auto-fill,minmax(280px,1fr))}
  .system-grid-card{position:relative;display:flex;flex-direction:column;border:1px solid rgba(200,168,75,.2);background:linear-gradient(152deg,#1a150d,#100c07 68%);border-radius:14px;padding:11px;min-height:188px;text-decoration:none;color:inherit;overflow:hidden;transition:transform .24s ease,box-shadow .26s ease,border-color .24s ease,background .24s ease,opacity .2s ease}
  .system-grid-card.clickable{cursor:pointer}
  .system-grid-card::before{content:'';position:absolute;inset:0;opacity:.45;pointer-events:none;transition:opacity .25s ease;background:radial-gradient(circle at 80% 0,rgba(255,255,255,.06),transparent 45%)}
  .system-grid-card::after{content:'';position:absolute;inset:-1px;background:linear-gradient(110deg,transparent 30%,rgba(200,168,75,.12) 50%,transparent 70%);transform:translateX(-130%);transition:transform .7s ease;pointer-events:none}
  .system-grid-card:hover{transform:translateY(-4px);border-color:rgba(200,168,75,.48);background:linear-gradient(152deg,#1f1910,#120d08 68%);box-shadow:0 0 0 1px rgba(200,168,75,.22) inset,0 14px 26px rgba(0,0,0,.42),0 0 16px rgba(200,168,75,.12)}
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
  .stack-card.dormant{opacity:.86}

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

  @media(max-width:900px){
    .system-grid-toolbar{flex-direction:column;align-items:stretch}
    .state-metric-grid{grid-template-columns:1fr 1fr}
  }
  @media(max-width:768px){
    .system-grid{grid-template-columns:1fr}
    .system-grid-card{min-height:unset}
    .next-move-grid{grid-template-columns:1fr}
    .state-metric-grid{grid-template-columns:1fr}
    .hub-priority-grid{grid-template-columns:1fr}
    .scan-history-grid{grid-template-columns:1fr}
  }
  @media(min-width:1100px){
    .system-grid-card.featured{grid-column:span 2}
  }
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
    <p class="mb-5 text-xs uppercase tracking-[0.14em] text-[#c8a84b]/72">Your previous scans are ready.</p>
    @endif

    <section class="system-section system-section-primary mb-8 dash-section-anchor" id="system-state">
      <div class="system-unified-module">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div>
            <p class="text-xs uppercase tracking-[0.22em] text-[#c8a84b]/80">{{ $isScansView ? 'System Readout Active · Scan History Focus' : ($isReportsView ? 'System Readout Active · Reports Focus' : 'System Readout Active') }}</p>
            <h1 class="mt-1 text-2xl font-semibold leading-tight lg:text-3xl">{{ $leadDomain }}</h1>
            <p class="state-summary">Saved to your dashboard. Previous scans ready for deployment.</p>
            <p class="mt-1 text-xs text-[#a9a08c]">{{ $summaryLine }}</p>
          </div>
          <span class="state-pulse {{ in_array($systemState, ['At Risk']) ? 'risk' : (in_array($systemState, ['Expanding']) ? 'expand' : (in_array($systemState, ['Under-optimized']) ? 'optimize' : '')) }}">{{ $leadState }}</span>
        </div>
        <div class="hub-priority-grid">
          <article class="hub-priority-card">
            <p>Latest Score / State</p>
            <p>{{ $leadScore > 0 ? $leadScore : 'No score yet' }} · {{ $leadState }}</p>
          </article>
          <article class="hub-priority-card">
            <p>Primary Bottleneck</p>
            <p>{{ $leadBottleneck }}</p>
          </article>
          <article class="hub-priority-card">
            <p>Next Move / Next Unlock</p>
            <p><a href="{{ $nextUnlockHref }}" class="hub-link">{{ $nextUnlockLabel }}</a></p>
          </article>
          <article class="hub-priority-card">
            <p>Recent Scan / Report</p>
            <p><a href="{{ $leadReportHref }}" class="hub-link">Open latest readout</a></p>
          </article>
        </div>
        <p class="mt-2 text-[11px] uppercase tracking-[0.14em] text-[#988f7c]">Last evaluated: {{ $latestEvaluatedLabel }} · Deploy Fix or Inspect Signal to move this system forward.</p>
      </div>
    </section>

    <section class="system-section mb-10 dash-section-anchor" id="scan-history">
      <div class="scan-history-shell">
        <div class="section-head">
          <div>
            <h2>{{ $isScansView ? 'Scan History Control Surface' : 'Scans' }}</h2>
            <p>Your scan history is the center of this system. Open report, deploy fix, or inspect signal immediately.</p>
          </div>
          <div class="flex items-center gap-2">
            @if($isScansView)
              <span class="surface-focus-kicker">Scans Destination</span>
            @endif
            <span class="text-xs text-[#9f9b8d]">{{ $scanFocusList->count() }} report{{ $scanFocusList->count() === 1 ? '' : 's' }} ready</span>
          </div>
        </div>

        <div class="scan-history-grid">
          @forelse($scanFocusList as $scan)
            @php
              $scanRouteKey = $scan['scan_route_key'] ?? $scan['public_scan_id'] ?? $scan['system_scan_id'] ?? null;
              $reportHref = $scanRouteKey ? route('dashboard.scans.show', ['scan' => $scanRouteKey]) : route('quick-scan.show');
              $scanScore = (int) ($scan['score'] ?? 0);
              $scanState = $scanScore >= 85 ? 'Stable' : ($scanScore >= 60 ? 'Under-optimized' : 'At Risk');
              $scanFix = trim((string) ($scan['fastest_fix'] ?? '')) !== '' ? $scan['fastest_fix'] : 'Inspect signal layer for next best correction.';
            @endphp
            <article class="scan-history-card">
              <p class="meta">System Readout Active</p>
              <p class="domain">{{ $scan['scan_name'] ?? $scan['domain'] }}</p>
              <div class="state-row">
                <span class="pill">{{ $scanState }}</span>
                <span class="score">Score {{ $scanScore }}</span>
              </div>
              <p class="bottleneck"><span class="text-[#d9c78f]">Primary Bottleneck:</span> {{ $scanFix }}</p>
              <p class="text-[11px] uppercase tracking-[0.12em] text-[#aaa08b]">Updated {{ $scan['scanned_at']?->diffForHumans() ?? $scan['created_at']?->diffForHumans() }}</p>
              <div class="actions">
                <a href="{{ $reportHref }}" class="open">Open Report</a>
                <a href="{{ $reportHref }}#layer-signal" class="deploy">Deploy Fix</a>
                <a href="{{ $reportHref }}#detailed-layer-view" class="inspect">Inspect Signal</a>
              </div>
            </article>
          @empty
            <article class="scan-history-card">
              <p class="meta">System Readout Active</p>
              <p class="domain">No previous scans ready yet.</p>
              <p class="bottleneck">Run your first scan to unlock report history, bottlenecks, and next moves.</p>
              <div class="actions">
                <a href="{{ route('quick-scan.show') }}" class="open">Run First Scan</a>
              </div>
            </article>
          @endforelse
        </div>
      </div>
    </section>

    @if(!$agencyModeActive)
    <section class="system-section mb-10 rounded-2xl border border-[#c8a84b]/16 bg-linear-to-br from-[#19160f] via-[#121008] to-[#0c0a06] p-5 shadow-xl operations-quiet dash-section-anchor" id="systems">
      <p class="mb-2 text-xs uppercase tracking-[0.22em] text-[#c8a84b]/80">System Readout</p>
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <h1 class="text-xl font-semibold leading-tight lg:text-2xl">{{ $objectiveTitle }}</h1>
          <p class="mt-2 max-w-2xl text-sm text-[#b5b0a3]">{{ $hasSystem ? 'Control surface active. Coverage expanding; gaps persisting.' : 'No baseline detected. Coverage undeployed. Initialization required.' }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
          <a href="{{ $objectiveCta }}" class="inline-flex items-center justify-center rounded-xl bg-[#c8a84b] px-5 py-3 text-sm font-semibold text-[#0b0905] transition hover:bg-[#dfc477]">{{ $objectiveCtaLabel }}</a>
          <a href="{{ url('/book?entry=consultation') }}" class="inline-flex items-center justify-center rounded-xl border border-[#c8a84b]/40 px-5 py-3 text-sm font-semibold text-[#e7dfc9] transition hover:border-[#c8a84b] hover:bg-[#c8a84b]/10">{{ $secondaryCtaLabel }}</a>
        </div>
      </div>
    </section>
    @endif

    @if($agencyModeActive)
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
      <section class="system-section system-section-primary mb-10 dash-section-anchor" id="systems">
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
                                  <a href="{{ $inspectHref }}" class="system-grid-cta cta-view js-readout-link">{{ $scan['is_renderable_report'] ? 'Open Report' : 'Inspect Readout' }}</a>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    @if(!$agencyModeActive)
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

    @if(!$agencyModeActive)
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

    <section class="system-section system-section-secondary mb-10 dash-section-anchor operations-quiet" id="coverage">
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
  })();
</script>
@endsection
