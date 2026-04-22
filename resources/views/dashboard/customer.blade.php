@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>
<style>
/* ── System visual tokens (aligned with public report/share surfaces) ── */
:root {
    --sys-font-body: 'DM Sans', sans-serif;
    --sys-font-display: 'Cormorant Garamond', serif;
    --sys-space-section: 32px;
    --sys-space-panel: 24px;
    --sys-panel-bg: #0e0d09;
    --sys-panel-border: rgba(200,168,75,.14);
    --sys-panel-radius: 12px;
    --sys-text-soft: rgba(168,168,160,.72);
    --sys-cta-gold-start: #c8a84b;
    --sys-cta-gold-end: #e2c97d;
}

/* ══════════════════════════════════════════════════════
   SEOAIco Dark Theme — Tailwind Color Overrides
   Maps light-mode Tailwind classes → dark luxe palette
   ══════════════════════════════════════════════════════ */

/* ── Neutral scale (light surfaces → dark) ────────── */
.bg-white { background-color: #0e0d09 !important; }
.bg-gray-50 { background-color: #13120e !important; }
.bg-gray-100 { background-color: rgba(200,168,75,.04) !important; }
.bg-gray-200 { background-color: rgba(200,168,75,.06) !important; }
.hover\:bg-gray-50:hover { background-color: rgba(200,168,75,.03) !important; }
.hover\:bg-gray-200:hover { background-color: rgba(200,168,75,.08) !important; }
.text-gray-900,.text-gray-800 { color: #ede8de !important; }
.text-gray-700 { color: rgba(237,232,222,.85) !important; }
.text-gray-600 { color: rgba(168,168,160,.72) !important; }
.text-gray-500 { color: rgba(168,168,160,.6) !important; }
.text-gray-400 { color: rgba(168,168,160,.45) !important; }
.text-gray-300 { color: rgba(168,168,160,.35) !important; }
.hover\:text-gray-900:hover { color: #ede8de !important; }
.border-gray-100,.border-gray-200 { border-color: rgba(200,168,75,.09) !important; }
.border-gray-300 { border-color: rgba(200,168,75,.12) !important; }
.border-gray-700 { border-color: rgba(200,168,75,.15) !important; }
.hover\:border-gray-500:hover { border-color: rgba(200,168,75,.2) !important; }
.divide-gray-200 > :not([hidden]) ~ :not([hidden]) { border-color: rgba(200,168,75,.09) !important; }
.bg-white\/10 { background-color: rgba(237,232,222,.1) !important; }

/* ── Blue → Gold ───────────────────────────────────── */
.bg-blue-50 { background-color: rgba(200,168,75,.04) !important; }
.bg-blue-100 { background-color: rgba(200,168,75,.08) !important; }
.bg-blue-500 { background-color: #c8a84b !important; }
.bg-blue-600 { background-color: #c8a84b !important; }
.hover\:bg-blue-700:hover { background-color: #a88a3a !important; }
.text-blue-300 { color: rgba(200,168,75,.5) !important; }
.text-blue-600,.text-blue-700,.text-blue-800 { color: #c8a84b !important; }
.hover\:text-blue-600:hover,.hover\:text-blue-900:hover { color: #c8a84b !important; }
.border-blue-100 { border-color: rgba(200,168,75,.08) !important; }
.border-blue-200 { border-color: rgba(200,168,75,.12) !important; }
.hover\:border-blue-200:hover { border-color: rgba(200,168,75,.12) !important; }
.hover\:border-blue-300:hover { border-color: rgba(200,168,75,.18) !important; }
.hover\:border-blue-400:hover { border-color: rgba(200,168,75,.22) !important; }

/* ── Indigo → Gold ─────────────────────────────────── */
.bg-indigo-50 { background-color: rgba(200,168,75,.04) !important; }
.bg-indigo-100 { background-color: rgba(200,168,75,.08) !important; }
.bg-indigo-600 { background-color: #c8a84b !important; }
.hover\:bg-indigo-700:hover { background-color: #a88a3a !important; }
.text-indigo-300 { color: rgba(200,168,75,.5) !important; }
.text-indigo-400 { color: rgba(200,168,75,.55) !important; }
.text-indigo-600,.text-indigo-700 { color: #c8a84b !important; }
.border-indigo-100 { border-color: rgba(200,168,75,.08) !important; }
.border-indigo-200 { border-color: rgba(200,168,75,.12) !important; }
.hover\:border-indigo-300:hover { border-color: rgba(200,168,75,.18) !important; }

/* ── Amber / Yellow → Gold ─────────────────────────── */
.bg-amber-50 { background-color: rgba(200,168,75,.06) !important; }
.bg-amber-100 { background-color: rgba(200,168,75,.1) !important; }
.border-amber-100 { border-color: rgba(200,168,75,.1) !important; }
.border-amber-200 { border-color: rgba(200,168,75,.18) !important; }
.text-amber-400\/80 { color: rgba(200,168,75,.8) !important; }
.bg-yellow-50 { background-color: rgba(200,168,75,.06) !important; }
.bg-yellow-100 { background-color: rgba(200,168,75,.1) !important; }
.border-yellow-200 { border-color: rgba(200,168,75,.18) !important; }
.bg-orange-50 { background-color: rgba(200,168,75,.06) !important; }
.bg-orange-100 { background-color: rgba(200,168,75,.08) !important; }
.text-orange-800 { color: #c8a84b !important; }
.border-orange-200 { border-color: rgba(200,168,75,.12) !important; }

/* ── Green (keep hue, adapt for dark) ──────────────── */
.bg-green-50 { background-color: rgba(106,175,144,.06) !important; }
.bg-green-100 { background-color: rgba(106,175,144,.12) !important; }
.bg-green-500 { background-color: #6aaf90 !important; }
.bg-green-600 { background-color: #5a9a7d !important; }
.hover\:bg-green-700:hover { background-color: #4d8a6f !important; }
.text-green-300 { color: rgba(106,175,144,.7) !important; }
.text-green-400,.text-green-500,.text-green-600,.text-green-700,.text-green-800 { color: #6aaf90 !important; }
.hover\:text-green-700:hover { color: #5a9a7d !important; }
.border-green-100 { border-color: rgba(106,175,144,.12) !important; }
.border-green-200 { border-color: rgba(106,175,144,.18) !important; }
.hover\:border-green-300:hover { border-color: rgba(106,175,144,.25) !important; }

/* ── Red (keep hue, adapt for dark) ────────────────── */
.bg-red-50 { background-color: rgba(196,120,120,.06) !important; }
.bg-red-100 { background-color: rgba(196,120,120,.12) !important; }
.bg-red-400,.bg-red-500 { background-color: #c47878 !important; }
.bg-red-600 { background-color: #b56868 !important; }
.hover\:bg-red-700:hover { background-color: #a55858 !important; }
.text-red-400,.text-red-500,.text-red-600,.text-red-700,.text-red-800 { color: #c47878 !important; }
.border-red-100 { border-color: rgba(196,120,120,.12) !important; }
.border-red-200 { border-color: rgba(196,120,120,.18) !important; }
.border-red-500 { border-color: #c47878 !important; }
.border-l-red-500 { border-left-color: #c47878 !important; }
.border-l-amber-400 { border-left-color: #c8a84b !important; }
.border-l-gray-300 { border-left-color: rgba(168,168,160,.35) !important; }

/* ── Emerald (green family) ────────────────────────── */
.bg-emerald-50 { background-color: rgba(106,175,144,.06) !important; }
.bg-emerald-500\/20 { background-color: rgba(106,175,144,.2) !important; }
.text-emerald-300 { color: rgba(106,175,144,.7) !important; }

/* ── Opacity variants ──────────────────────────────── */
.bg-blue-500\/20 { background-color: rgba(200,168,75,.2) !important; }
.bg-indigo-500\/20 { background-color: rgba(200,168,75,.2) !important; }
.text-white\/80 { color: rgba(237,232,222,.8) !important; }

/* ── White text (stays light on dark/colored bgs) ──── */
.text-white { color: #f5f0e8 !important; }

/* ── Gold-bg buttons: force dark text for contrast ─── */
.bg-blue-600,.bg-blue-600 *,
.bg-indigo-600,.bg-indigo-600 *,
.bg-amber-500,.bg-amber-500 *,
.hover\:bg-blue-700:hover,.hover\:bg-blue-700:hover *,
.hover\:bg-indigo-700:hover,.hover\:bg-indigo-700:hover *,
.hover\:bg-amber-600:hover,.hover\:bg-amber-600:hover * { color: #080808 !important; }

/* ── Gradient from/to overrides ────────────────────── */
.from-indigo-50 { --tw-gradient-from: rgba(200,168,75,.04) !important; }
.to-blue-50 { --tw-gradient-to: rgba(200,168,75,.02) !important; }
.from-blue-50 { --tw-gradient-from: rgba(200,168,75,.04) !important; }
.from-green-50,.from-emerald-50 { --tw-gradient-from: rgba(106,175,144,.04) !important; }
.to-green-50,.to-emerald-50 { --tw-gradient-to: rgba(106,175,144,.04) !important; }
.from-amber-50 { --tw-gradient-from: rgba(200,168,75,.04) !important; }
.to-yellow-50 { --tw-gradient-to: rgba(200,168,75,.02) !important; }
.to-cyan-50 { --tw-gradient-to: rgba(200,168,75,.04) !important; }
.from-blue-400 { --tw-gradient-from: #c8a84b !important; }
.to-blue-600 { --tw-gradient-to: #a88a3a !important; }
.from-white\/50 { --tw-gradient-from: rgba(14,13,9,.5) !important; }
.to-white\/90 { --tw-gradient-to: rgba(14,13,9,.9) !important; }

/* ── Rings ─────────────────────────────────────────── */
.ring-green-100 { --tw-ring-color: rgba(106,175,144,.1) !important; }
.ring-green-400\/30 { --tw-ring-color: rgba(106,175,144,.3) !important; }
.ring-amber-100 { --tw-ring-color: rgba(200,168,75,.1) !important; }
.ring-blue-100 { --tw-ring-color: rgba(200,168,75,.08) !important; }
.ring-blue-400\/30 { --tw-ring-color: rgba(200,168,75,.3) !important; }
.ring-indigo-400\/30 { --tw-ring-color: rgba(200,168,75,.3) !important; }
.ring-emerald-400\/30 { --tw-ring-color: rgba(106,175,144,.3) !important; }
.ring-red-50 { --tw-ring-color: rgba(196,120,120,.06) !important; }
.ring-white\/20 { --tw-ring-color: rgba(237,232,222,.2) !important; }

/* ── Shadows (deeper for dark theme) ───────────────── */
.shadow-sm { box-shadow: 0 1px 3px rgba(0,0,0,.3) !important; }
.shadow-md { box-shadow: 0 4px 10px rgba(0,0,0,.35) !important; }
.shadow-lg { box-shadow: 0 10px 24px rgba(0,0,0,.4) !important; }
.shadow-xl { box-shadow: 0 16px 40px rgba(0,0,0,.45) !important; }
.shadow-2xl { box-shadow: 0 24px 60px rgba(0,0,0,.5) !important; }
.hover\:shadow-md:hover { box-shadow: 0 4px 10px rgba(0,0,0,.35) !important; }
.hover\:shadow-lg:hover { box-shadow: 0 10px 24px rgba(0,0,0,.4) !important; }
.hover\:shadow-xl:hover { box-shadow: 0 16px 40px rgba(0,0,0,.45) !important; }

/* ── Group hover ───────────────────────────────────── */
.group:hover .group-hover\:text-blue-600 { color: #c8a84b !important; }
.group:hover .group-hover\:text-amber-600 { color: #c8a84b !important; }
</style>
@endpush

@section('content')
<style>
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(14px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes pulseGlow {
    0%, 100% { box-shadow: 0 0 0 0 rgba(200, 168, 75, 0.4); }
    50% { box-shadow: 0 0 24px 4px rgba(200, 168, 75, 0.15); }
}
.finding-card { will-change: transform, box-shadow; }
.cta-glow { transition: all 0.2s ease; }
.cta-glow:hover { box-shadow: 0 0 20px rgba(200, 168, 75, 0.35); transform: translateY(-1px); }
.cta-glow-red:hover { box-shadow: 0 0 20px rgba(196, 120, 120, 0.3); transform: translateY(-1px); }
.cta-glow-dark:hover { box-shadow: 0 0 24px rgba(0, 0, 0, 0.4); transform: translateY(-1px); }
.locked-teaser { backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); }
.pipeline-step { transition: all 0.3s ease; }
.pipeline-step:hover { transform: translateY(-2px); }
.layer-active { border-color: rgba(106,175,144,.2) !important; box-shadow: 0 8px 20px rgba(106,175,144,.08); }
.layer-locked { border-color: rgba(200,168,75,.12) !important; opacity: .92; }
.layer-dimmed { opacity: .72; }
.layer-highlight { border-color: rgba(200,168,75,.18) !important; box-shadow: 0 8px 20px rgba(200,168,75,.08); }

/* ── Shared system primitives ── */
.sys-section { margin-bottom: var(--sys-space-section); }
.sys-panel {
    background: var(--sys-panel-bg);
    border: 1px solid var(--sys-panel-border);
    border-radius: var(--sys-panel-radius);
    padding: var(--sys-space-panel);
}
.sys-panel-soft {
    border-radius: var(--sys-panel-radius);
    border: 1px solid rgba(200,168,75,.1);
}
.sys-eyebrow {
    font-size: .62rem;
    letter-spacing: .22em;
    text-transform: uppercase;
    color: rgba(200,168,75,.66);
}
.sys-copy-muted { color: var(--sys-text-soft); }
.sys-cta-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    min-height: 44px;
    border-radius: 10px;
    font-size: .72rem;
    letter-spacing: .12em;
    text-transform: uppercase;
    font-weight: 700;
    text-decoration: none;
    color: #080808 !important;
    background: linear-gradient(135deg, var(--sys-cta-gold-start), var(--sys-cta-gold-end));
    border: 1px solid rgba(200,168,75,.55);
    transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
}
.sys-cta-primary:hover {
    background: linear-gradient(135deg, var(--sys-cta-gold-end), var(--sys-cta-gold-start));
    transform: translateY(-1px);
    box-shadow: 0 8px 24px rgba(200,168,75,.2);
}
.sys-cta-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 18px;
    min-height: 44px;
    border-radius: 10px;
    font-size: .72rem;
    letter-spacing: .08em;
    text-transform: uppercase;
    text-decoration: none;
    border: 1px solid rgba(200,168,75,.2);
    color: rgba(237,232,222,.84) !important;
    background: rgba(200,168,75,.03);
    transition: border-color .2s ease, background .2s ease;
}
.sys-cta-secondary:hover {
    border-color: rgba(200,168,75,.35);
    background: rgba(200,168,75,.06);
}

/* ── Grid board conversion ── */
.board-shell {
    background: #11100c;
    border: 1px solid rgba(200,168,75,.14);
    border-radius: 14px;
    padding: 12px;
    margin-bottom: 18px;
}
.board-zone-label {
    font-size: .56rem;
    letter-spacing: .2em;
    text-transform: uppercase;
    color: rgba(200,168,75,.62);
    margin: 2px 2px 8px;
}
.command-focus {
    border: 1px solid rgba(214,181,84,.42);
    background: linear-gradient(145deg, rgba(214,181,84,.2), #1c1912 66%);
    border-radius: 14px;
    padding: 14px 14px 12px;
    box-shadow: 0 16px 34px rgba(0,0,0,.34), 0 0 0 1px rgba(214,181,84,.12) inset;
    margin-bottom: 10px;
}
.command-focus-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(1.1rem,2.1vw,1.55rem);
    line-height: 1.16;
    color: #ede8de;
    margin-bottom: 4px;
}
.command-focus-copy {
    font-size: .75rem;
    line-height: 1.46;
    color: rgba(201,201,193,.9);
    max-width: 760px;
    margin-bottom: 10px;
}
.command-focus .board-primary-cta {
    min-height: 42px;
    padding: 10px 16px;
    font-size: .68rem;
}
.board-row-top {
    display: grid;
    grid-template-columns: 1.1fr .8fr 1.1fr;
    gap: 10px;
    margin-bottom: 10px;
}
.board-row-modules {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 10px;
}
.board-row-actions {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}
.board-tile {
    border: 1px solid rgba(200,168,75,.18);
    background: #181611;
    border-radius: 11px;
    padding: 11px;
    min-height: 112px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}
.board-tile h4 {
    font-size: .8rem;
    font-weight: 700;
    color: #ede8de;
    line-height: 1.3;
    margin: 0 0 5px;
}
.board-tile p {
    font-size: .7rem;
    color: rgba(176,176,168,.86);
    line-height: 1.4;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.board-kicker {
    font-size: .54rem;
    letter-spacing: .2em;
    text-transform: uppercase;
    color: rgba(200,168,75,.68);
    margin-bottom: 5px;
}
.board-state-active {
    border-color: rgba(106,175,144,.32);
    background: linear-gradient(160deg, rgba(106,175,144,.09), #17150f 62%);
}
.board-state-available {
    border-color: rgba(200,168,75,.34);
    background: linear-gradient(160deg, rgba(200,168,75,.12), #17150f 62%);
}
.board-state-locked {
    border-color: rgba(200,168,75,.09);
    opacity: .78;
}
.board-state-active:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0,0,0,.26), 0 0 0 1px rgba(106,175,144,.2) inset;
}
.board-state-available:hover {
    transform: translateX(4px) translateY(-1px);
    box-shadow: 0 12px 24px rgba(0,0,0,.26);
}
.board-state-available:hover .board-primary-cta {
    background: linear-gradient(135deg, #e2c97d, #f1db9e);
}
.board-state-available::after {
    content: '';
    position: absolute;
    inset: -1px;
    background: linear-gradient(100deg, transparent 30%, rgba(214,181,84,.14) 50%, transparent 70%);
    transform: translateX(-130%);
    animation: availableSweep 4.8s ease-in-out infinite;
    pointer-events: none;
}
.board-state-locked {
    filter: blur(.45px) saturate(.86) contrast(.86);
}
.board-state-locked:hover {
    transform: none;
    box-shadow: none;
}

.board-tile.status-tile {
    opacity: .86;
    transform: scale(.985);
}

.board-tile.control-tile {
    border-color: rgba(214,181,84,.46);
    background: linear-gradient(145deg, rgba(214,181,84,.2), #1c1912 62%);
    box-shadow: 0 14px 30px rgba(0,0,0,.3), 0 0 0 1px rgba(214,181,84,.16) inset;
}

.board-tile.control-tile .board-primary-cta {
    min-height: 42px;
    padding: 10px 16px;
    font-size: .68rem;
}

.board-score-live {
    animation: scorePulse 4.8s ease-in-out infinite;
}

@keyframes scorePulse {
    0%,100% { text-shadow: 0 0 12px rgba(200,168,75,.18); }
    50% { text-shadow: 0 0 22px rgba(200,168,75,.34); }
}

@keyframes availableSweep {
    0% { transform: translateX(-130%); }
    55% { transform: translateX(130%); }
    100% { transform: translateX(130%); }
}
.board-primary-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    border-radius: 10px;
    padding: 9px 12px;
    font-size: .64rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    text-decoration: none;
    color: #080808 !important;
    background: linear-gradient(135deg, #c8a84b, #e2c97d);
    border: 1px solid rgba(200,168,75,.54);
}
.board-secondary-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 36px;
    border-radius: 10px;
    padding: 8px 10px;
    font-size: .62rem;
    letter-spacing: .08em;
    text-transform: uppercase;
    text-decoration: none;
    color: rgba(237,232,222,.86) !important;
    border: 1px solid rgba(200,168,75,.2);
    background: rgba(200,168,75,.04);
}
.dash-deep-dive {
    margin-top: 6px;
    border: 1px solid rgba(200,168,75,.12);
    border-radius: 12px;
    background: #100f0c;
    padding: 0;
}
.dash-deep-dive > summary {
    list-style: none;
    cursor: pointer;
    padding: 12px 14px;
    font-size: .7rem;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: rgba(200,168,75,.72);
}
.dash-deep-dive > summary::-webkit-details-marker { display: none; }
.dash-deep-dive-content { padding: 6px 12px 12px; }

@media(max-width: 980px) {
    .command-focus { padding: 12px; }
    .board-row-top { grid-template-columns: 1fr; }
    .board-row-modules { grid-template-columns: 1fr; }
    .board-row-actions { grid-template-columns: 1fr; }
}

/* ── System architecture primitives ── */
.system-shell {
    position: relative;
}
.system-shell::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(200,168,75,.02) 1px, transparent 1px), linear-gradient(90deg, rgba(200,168,75,.02) 1px, transparent 1px);
    background-size: 28px 28px;
    pointer-events: none;
    opacity: .1;
}
.system-shell > * {
    position: relative;
    z-index: 1;
}
.system-card {
    background: #0e0d09;
    border: 1px solid rgba(200,168,75,.14);
    border-radius: 12px;
    padding: 18px;
    transition: border-color .2s ease, transform .2s ease, box-shadow .2s ease;
}
.system-card:hover {
    border-color: rgba(200,168,75,.18);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(0,0,0,.22);
}
.card-eyebrow {
    font-size: .6rem;
    letter-spacing: .18em;
    text-transform: uppercase;
    color: rgba(200,168,75,.68);
    margin-bottom: 8px;
}
.card-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(1.2rem, 2vw, 1.55rem);
    line-height: 1.2;
    color: #ede8de;
    margin-bottom: 6px;
}
.card-meta {
    font-size: .64rem;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: rgba(106,175,144,.82);
    margin-bottom: 8px;
}
.card-body {
    font-size: .8rem;
    color: rgba(168,168,160,.74);
    line-height: 1.55;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 12px;
}
.card-action {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}
.card-action > a { min-height: 42px; }

.progression-rail {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 24px;
    align-items: stretch;
}
.rail-level {
    border-radius: 12px;
    border: 1px solid rgba(200,168,75,.14);
    background: #0e0d09;
    padding: 12px;
    opacity: .68;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.rail-level.locked {
    opacity: .44;
    border-color: rgba(200,168,75,.08);
}
.rail-level.active {
    opacity: 1;
    border-color: rgba(200,168,75,.28);
    box-shadow: 0 0 0 1px rgba(200,168,75,.12) inset;
}
.rail-level.completed {
    opacity: 1;
    border-color: rgba(106,175,144,.24);
    background: linear-gradient(180deg, rgba(106,175,144,.05), rgba(14,13,9,.94));
}
.rail-kicker {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.rail-name {
    font-size: .68rem;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: rgba(200,168,75,.72);
}
.rail-state {
    font-size: .54rem;
    letter-spacing: .14em;
    text-transform: uppercase;
    padding: 2px 6px;
    border-radius: 999px;
    border: 1px solid transparent;
    min-width: 68px;
    text-align: center;
}
.rail-level.completed .rail-state { color: rgba(106,175,144,.9); border-color: rgba(106,175,144,.26); background: rgba(106,175,144,.08); }
.rail-level.active .rail-state { color: rgba(200,168,75,.9); border-color: rgba(200,168,75,.24); background: rgba(200,168,75,.08); }
.rail-level.locked .rail-state { color: rgba(168,168,160,.55); border-color: rgba(168,168,160,.16); background: rgba(168,168,160,.06); }
.rail-desc {
    font-size: .74rem;
    line-height: 1.4;
    color: rgba(168,168,160,.72);
    min-height: 2.1em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.newly-unlocked-card {
    border-color: rgba(106,175,144,.34);
    background: linear-gradient(180deg, rgba(106,175,144,.08), rgba(14,13,9,.96));
    box-shadow: 0 0 0 1px rgba(106,175,144,.14) inset;
    position: relative;
}
.newly-unlocked-card::before {
    content: 'NEWLY UNLOCKED';
    position: absolute;
    top: -11px;
    right: 14px;
    font-size: .5rem;
    letter-spacing: .18em;
    text-transform: uppercase;
    color: rgba(106,175,144,.95);
    background: #0e0d09;
    border: 1px solid rgba(106,175,144,.3);
    border-radius: 999px;
    padding: 3px 8px;
}
.newly-unlocked-card .card-title {
    font-size: clamp(1.3rem, 2.1vw, 1.7rem);
}
.newly-unlocked-card .card-meta {
    color: rgba(106,175,144,.9);
}

@media(max-width: 900px) {
    .progression-rail { grid-template-columns: 1fr; }
}
</style>
<!-- Page Header — state-aware -->
<div class="mb-6 system-shell">
@if($scanProjects->count() === 0)
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome to your command center.</h2>
    <p class="text-gray-600">Start with a scan to establish your AI citation baseline.</p>
@elseif($tierRank >= 4)
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Your system is fully active.</h2>
    <p class="text-gray-600">All analysis layers complete — implementation is your next move.</p>
@else
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Your market position.</h2>
    <p class="text-gray-600">Your next move defines your position. Expansion is the current priority.</p>
@endif
</div>

{{-- ═══════════════════════════════════════════════════ --}}
{{-- FIRST-USE: No scan yet — elevated above fold       --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if($scanProjects->count() === 0)
<div class="mb-8" style="animation: fadeSlideUp 0.4s ease-out both">
    <div class="relative bg-linear-to-br from-gray-900 via-gray-900 to-gray-800 rounded-2xl overflow-hidden shadow-2xl">
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22><rect width=%2240%22 height=%2240%22 fill=%22none%22 stroke=%22white%22 stroke-width=%220.5%22/></svg>');"></div>
        <div class="relative p-8 sm:p-10">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-amber-400 mb-4">Step 1 — Establish Your Baseline</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-white mb-3 leading-tight">
                How visible is your business<br class="hidden sm:block"> to AI systems right now?
            </h3>
            <p class="text-sm text-gray-400 leading-relaxed mb-8 max-w-xl">Run your first scan to see your AI citation readiness score, identify your highest-impact gaps, and understand exactly where your market position stands today.</p>
            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('scan.start') }}" class="cta-glow inline-flex items-center justify-center gap-2 px-8 py-4 bg-amber-500 hover:bg-amber-400 text-gray-900 rounded-xl text-base font-extrabold transition-all shadow-lg shadow-amber-500/20 w-full sm:w-auto">
                    Start Your Scan — $2
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="{{ url('/pricing') }}" class="inline-flex items-center gap-2 px-5 py-3 text-gray-400 hover:text-white border border-gray-700 hover:border-gray-500 rounded-xl text-sm font-medium transition-all">
                    See the full system →
                </a>
            </div>
            <div class="mt-8 pt-6 border-t border-gray-800 grid grid-cols-3 gap-3 sm:gap-6 text-center">
                <div>
                    <p class="text-lg sm:text-xl font-bold text-white mb-0.5">60s</p>
                    <p class="text-[10px] sm:text-xs text-gray-500">to your first score</p>
                </div>
                <div>
                    <p class="text-lg sm:text-xl font-bold text-white mb-0.5">0–100</p>
                    <p class="text-[10px] sm:text-xs text-gray-500">citation readiness score</p>
                </div>
                <div>
                    <p class="text-lg sm:text-xl font-bold text-white mb-0.5">$2</p>
                    <p class="text-[10px] sm:text-xs text-gray-500">data carries forward</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════ --}}
{{-- SYSTEM ENTRY CONFIRMATION (flash from checkout)    --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if(session('system_entry'))
    <div class="mb-6">
        <div class="rounded-xl bg-linear-to-r from-indigo-50 to-blue-50 border border-indigo-200 p-6">
            <div class="flex items-start gap-4">
                <div class="shrink-0">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">You've entered the system at {{ $systemTier?->label() ?? 'Base' }}.</h3>
                    <p class="text-sm text-gray-600 mt-1">Everything builds forward from here.</p>
                    @if($nextStep)
                    <p class="text-sm text-indigo-600 font-medium mt-2">Next: {{ $nextStep }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

@php
    $tierRankInt = (int) ($tierRank ?? 0);
    $layerVisualClassMap = [
        'active' => 'layer-active',
        'locked' => 'layer-locked',
        'dimmed' => 'layer-dimmed',
        'highlight' => 'layer-highlight',
    ];
    $layerStates = [
        1 => [
            'isActive' => $tierRankInt === 1,
            'isUnlocked' => $tierRankInt >= 1,
            'isLocked' => $tierRankInt < 1,
        ],
        2 => [
            'isActive' => $tierRankInt === 2,
            'isUnlocked' => $tierRankInt >= 2,
            'isLocked' => $tierRankInt < 2,
        ],
        3 => [
            'isActive' => $tierRankInt === 3,
            'isUnlocked' => $tierRankInt >= 3,
            'isLocked' => $tierRankInt < 3,
        ],
        4 => [
            'isActive' => $tierRankInt === 4,
            'isUnlocked' => $tierRankInt >= 4,
            'isLocked' => $tierRankInt < 4,
        ],
    ];
    $tierDescriptions = [
        2 => [
            'name' => 'Signal Analysis',
            'adds' => [
                'Full signal breakdown by category',
                'Priority gap visibility from scan data',
                'Deeper diagnostic context',
            ],
        ],
        3 => [
            'name' => 'Action Plan',
            'adds' => [
                'Ranked fix list from your scan',
                'Structural fixes ordered by business impact',
                'Execution sequence by effort and impact',
            ],
        ],
        4 => [
            'name' => 'Guided Execution',
            'adds' => [
                'Step-by-step execution checklist in dashboard',
                'Guided steps tied to your scan issues',
                'Progress tracking as you complete items',
            ],
        ],
    ];
    $currentTierName = $tierRankInt >= 4 ? 'Guided Execution' : ($tierRankInt >= 3 ? 'Action Plan' : ($tierRankInt >= 2 ? 'Signal Analysis' : 'Baseline Score'));
    $nextTierRank = $tierRankInt < 4 ? max(2, $tierRankInt + 1) : null;
    $nextTierDetails = $nextTierRank ? ($tierDescriptions[$nextTierRank] ?? null) : null;
    $currentTierDetails = $tierDescriptions[$tierRankInt] ?? null;
    $tierBadgeClass = $layerStates[4]['isUnlocked']
        ? 'bg-green-50 text-green-700 border border-green-200 '
        : 'bg-amber-50 text-amber-700 border border-amber-200';
    $currentLayerStateClass = $tierRankInt > 0 ? $layerVisualClassMap['active'] : $layerVisualClassMap['dimmed'];
    $nextLayerStateClass = $nextTierDetails ? $layerVisualClassMap['highlight'] : $layerVisualClassMap['active'];
    $nextAddsStateClass = $nextTierDetails ? $layerVisualClassMap['highlight'] : $layerVisualClassMap['dimmed'];
    $progressionRail = [
        [
            'name' => 'Signal Analysis',
            'desc' => 'Signal breakdown + gap visibility from scan.',
            'state' => $tierRankInt > 2 ? 'completed' : ($tierRankInt === 2 ? 'active' : 'locked'),
        ],
        [
            'name' => 'Action Plan',
            'desc' => 'Prioritized fix list ordered by impact.',
            'state' => $tierRankInt > 3 ? 'completed' : ($tierRankInt === 3 ? 'active' : 'locked'),
        ],
        [
            'name' => 'Guided Execution',
            'desc' => 'In-dashboard execution checklist with progress tracking.',
            'state' => $tierRankInt >= 4 ? 'completed' : ($tierRankInt === 4 ? 'active' : 'locked'),
        ],
    ];
    $justUnlockedTier = session('system_entry') ? ($currentTierName ?? null) : null;
@endphp

@php
    $boardLatestScan = $scanProjects->first();
    $boardScore = (int) ($boardLatestScan->score ?? 0);
    $boardScoreMessage = $boardScore >= 80
        ? 'Visible now. Secure position before expansion.'
        : 'Position unstable. Advance the next layer.';
    $boardStateText = $tierRankInt >= 4
        ? 'System Active'
        : ($tierRankInt >= 2 ? 'Module Progressing' : 'Base Layer');
    $boardPrimaryHref = $nextRoute ? route($nextRoute) : route('quick-scan.show');
    $boardPrimaryLabel = $nextRoute ? 'Advance Layer' : 'Open Scan';
    $boardModuleDefs = [
        2 => ['title' => 'Signal Analysis', 'line' => 'Full signal breakdown from your scan data.', 'route' => 'checkout.signal-expansion'],
        3 => ['title' => 'Action Plan', 'line' => 'Prioritized fix list ordered by scan impact.', 'route' => 'checkout.structural-leverage'],
        4 => ['title' => 'Guided Execution', 'line' => 'In-dashboard execution checklist with progress tracking.', 'route' => 'checkout.system-activation'],
    ];
@endphp

<section class="board-shell sys-section" aria-label="System board">
    <p class="board-zone-label">Command</p>
    <article class="command-focus">
        <p class="board-kicker">Current Objective</p>
        <h3 class="command-focus-title">{{ $nextTierDetails['name'] ?? 'Maintain Mission Control' }}</h3>
        <p class="command-focus-copy">{{ $nextTierDetails ? ($nextTierDetails['adds'][0] ?? 'Activate the next layer.') : 'All layers are active. Keep control, expand territory, and defend reference position.' }}</p>
        <a href="{{ $boardPrimaryHref }}" class="board-primary-cta">Advance Layer →</a>
    </article>

    <p class="board-zone-label">System Status</p>
    <div class="board-row-top">
        <article class="board-tile board-state-active">
            <div>
                <p class="board-kicker">Active Mission</p>
                <h4>{{ $currentTierName }}</h4>
                <p>{{ $boardStateText }} across {{ $tierRankInt }}/4 intelligence layers.</p>
            </div>
            <div>
                <a href="{{ route('quick-scan.show') }}" class="board-secondary-cta">View Layer</a>
            </div>
        </article>

        <article class="board-tile status-tile">
            <div>
                <p class="board-kicker">Status</p>
                <h4 class="board-score-live">{{ $boardScore }}/100</h4>
                <p>{{ $boardScoreMessage }}</p>
            </div>
            <div>
                <a href="{{ route('quick-scan.show') }}" class="board-secondary-cta">Continue Tracking</a>
            </div>
        </article>

        <article class="board-tile board-state-available control-tile">
            <div>
                <p class="board-kicker">Priority Move</p>
                <h4>{{ $nextTierDetails['name'] ?? 'System Review' }}</h4>
                <p>{{ $nextTierDetails ? ($nextTierDetails['adds'][0] ?? 'Unlock next capability.') : 'All layers unlocked. Move to deployment.' }}</p>
            </div>
            <div>
                <a href="{{ $boardPrimaryHref }}" class="board-primary-cta">Advance Layer →</a>
            </div>
        </article>
    </div>

    <p class="board-zone-label">Available Modules</p>
    <div class="board-row-modules" aria-label="Module row">
        @foreach([2,3,4] as $moduleRank)
            @php
                $module = $boardModuleDefs[$moduleRank];
                $moduleState = $tierRankInt >= $moduleRank ? 'board-state-active' : ($nextTierRank === $moduleRank ? 'board-state-available' : 'board-state-locked');
                $moduleAction = $tierRankInt >= $moduleRank ? 'Active' : ($nextTierRank === $moduleRank ? 'Activate' : 'Locked');
                $moduleHref = route($module['route']);
            @endphp
            <article class="board-tile {{ $moduleState }}">
                <div>
                    <p class="board-kicker">Module</p>
                    <h4>{{ $module['title'] }}</h4>
                    <p>{{ $module['line'] }}</p>
                </div>
                <div>
                    @if($moduleAction === 'Activate')
                        <a href="{{ $moduleHref }}" class="board-primary-cta">Activate Module →</a>
                    @else
                        <a href="{{ route('quick-scan.show') }}" class="board-secondary-cta">{{ $moduleAction }}</a>
                    @endif
                </div>
            </article>
        @endforeach
    </div>

    <p class="board-zone-label">Deep Analysis</p>
    <div class="board-row-actions" aria-label="Action row">
        <article class="board-tile">
            <div>
                <p class="board-kicker">Fix Card</p>
                <h4>Top Correction</h4>
                <p>{{ $topFindings[0]['what_missing'] ?? 'No immediate corrective item.' }}</p>
            </div>
            <div><a href="{{ route('quick-scan.show') }}" class="board-secondary-cta">Open Signal</a></div>
        </article>
        <article class="board-tile">
            <div>
                <p class="board-kicker">Action</p>
                <h4>Scan Timeline</h4>
                <p>Track score movement and reopen saved scans.</p>
            </div>
            <div><a href="#ai-scans" class="board-secondary-cta">Track Timeline</a></div>
        </article>
        <article class="board-tile">
            <div>
                <p class="board-kicker">Action</p>
                <h4>Deploy Support</h4>
                <p>Get implementation support when deployment begins.</p>
            </div>
            <div><a href="{{ url('/book') }}" class="board-secondary-cta">Secure Support</a></div>
        </article>
    </div>
</section>

<details class="dash-deep-dive">
    <summary>Open Detailed Analysis</summary>
    <div class="dash-deep-dive-content">

@if(session('scan_saved'))
    <div class="mb-6">
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.172l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('scan_saved') }}</p>
                    <p class="mt-1 text-sm text-green-700">Your scan baseline has been saved. View it below under Your Visibility Baselines.</p>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- ═══════════════════════════════════════════════════ --}}
{{-- CUSTOMER: Your Current Position hero               --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if($scanProjects->count() > 0)
    @php
        $latestScan = $scanProjects->first();
        $latestScoreValue = (int) ($latestScan->score ?? 0);
        $latestScoreCircleClass = 'bg-gradient-to-br from-red-400 to-red-600';
        $latestPositionMessage = 'Your visibility is low. Priority signal correction is required to establish position.';

        if ($latestScoreValue >= 90) {
            $latestScoreCircleClass = 'bg-gradient-to-br from-green-400 to-green-600';
            $latestPositionMessage = 'You are visible. Your position is not yet secured. Expansion opportunity exists.';
        } elseif ($latestScoreValue >= 80) {
            $latestScoreCircleClass = 'bg-gradient-to-br from-blue-400 to-blue-600';
            $latestPositionMessage = 'You are visible. Your position is not yet secured. Expansion opportunity exists.';
        } elseif ($latestScoreValue >= 70) {
            $latestScoreCircleClass = 'bg-gradient-to-br from-blue-400 to-blue-600';
            $latestPositionMessage = 'Visibility is improving. Structural gaps still limit market control.';
        } elseif ($latestScoreValue >= 40) {
            $latestScoreCircleClass = 'bg-gradient-to-br from-yellow-400 to-yellow-600';
            $latestPositionMessage = 'Your coverage is partial. Significant gaps leave market share on the table.';
        }

        $latestScoreChangeClass = 'text-gray-500';
        $latestScoreChangeText = '— No change since last scan';
        if ($latestScan->score_change > 0) {
            $latestScoreChangeClass = 'text-green-600';
            $latestScoreChangeText = '↑ +' . $latestScan->score_change . ' points since last scan';
        } elseif ($latestScan->score_change < 0) {
            $latestScoreChangeClass = 'text-red-600';
            $latestScoreChangeText = '↓ ' . $latestScan->score_change . ' points since last scan';
        }
    @endphp
    <div class="mb-8 bg-white rounded-xl border-2 border-gray-100 shadow-sm overflow-hidden sys-panel-soft sys-section system-card">
        <div class="px-6 py-4 border-b border-gray-100">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Your Current Position</p>
        </div>
        <div class="p-6">
            <div class="flex items-start gap-6">
                <div class="shrink-0 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full {{ $latestScoreCircleClass }} text-white">
                        <span class="text-3xl font-bold">{{ $latestScan->score ?? '—' }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Latest Score</p>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $latestScan->domain() }}</h3>
                    <p class="text-sm text-gray-600 mt-1 card-body" style="margin-bottom:0">{{ $latestPositionMessage }}</p>
                    @if($latestScan->score_change !== null)
                    <p class="text-sm font-medium mt-2 {{ $latestScoreChangeClass }}">{{ $latestScoreChangeText }}</p>
                    @endif
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('dashboard.scans.show', ['scan' => $latestScan->publicScanId()]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                            View Full Report
                        </a>
                        <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-blue-300 text-gray-700 hover:text-blue-600 rounded-lg text-sm font-medium transition-colors">
                            Re-check Your Position
                        </a>
                    </div>
                </div>
            </div>
            @if($totalScans > 1)
            <div class="mt-6 pt-5 border-t border-gray-100 grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalScans }}</p>
                    <p class="text-xs text-gray-500">Total Scans</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ round($scanProjects->avg('score'), 0) }}</p>
                    <p class="text-xs text-gray-500">Average Score</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $scanProjects->pluck('url')->map(fn($u) => parse_url($u, PHP_URL_HOST) ?? $u)->unique()->count() }}</p>
                    <p class="text-xs text-gray-500">Domains Tracked</p>
                </div>
            </div>
            @endif
        </div>
    </div>
@endif

{{-- ═══════════════════════════════════════════════════ --}}
{{-- TOP FINDINGS: High-impact severity cards           --}}
{{-- ═══════════════════════════════════════════════════ --}}
@php
    $dashLatestScore = (int) ($scanProjects->first()?->score ?? 0);
    $dashIsHighScore = $dashLatestScore >= 88;
    $dashIsMaxScore  = $dashLatestScore >= 95;
    // Deterministic territory simulation for dashboard (stable per scan id)
    $dashScanSeed       = max(1, (int)($scanProjects->first()?->id ?? 1));
    $dashLocalCities    = ($dashScanSeed % 3) + 1;
    $dashRegionalCities = ($dashScanSeed % 8) + 12;
    $dashUncovered      = $dashRegionalCities - $dashLocalCities;
    $dashFootprintPct   = round(($dashLocalCities / $dashRegionalCities) * 100);
    $dashMissedAreas    = (int) round($dashUncovered * 2.4);
@endphp
@if(!empty($topFindings))
    <div class="mb-8 sys-section">
        {{-- Header with pulsing alert --}}
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                @if($dashIsHighScore)
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-50"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                </span>
                @else
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
                @endif
                <div>
                    @if($dashIsHighScore)
                        <h3 class="text-lg font-bold text-gray-900">Your expansion opportunities</h3>
                        <p class="text-sm text-gray-500">Where your market position can grow</p>
                    @else
                        <h3 class="text-lg font-bold text-gray-900">What needs your attention</h3>
                        <p class="text-sm text-gray-500">Ranked by impact on your AI visibility</p>
                    @endif
                </div>
            </div>
            @if($dashIsHighScore)
            <span class="px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-bold uppercase tracking-wider">{{ min(count($topFindings), 4) }} {{ Str::plural('Opportunity', min(count($topFindings), 4)) }}</span>
            @else
            <span class="px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded-full text-xs font-bold uppercase tracking-wider">{{ min(count($topFindings), 4) }} {{ Str::plural('Issue', min(count($topFindings), 4)) }}</span>
            @endif
        </div>

        {{-- ═══════════════────────────────────────────────── --}}
        {{-- YOUR NEXT MOVE: Highest-impact action -- }}
        {{--═══════════════────────────────────────────────── --}}
        @if($nextBestAction && $tierRank > 0)
        <div class="mb-12" style="animation: fadeSlideUp 0.5s ease-out 0s both">
            <div class="bg-linear-to-br from-amber-50 to-yellow-50 rounded-xl border-2 border-amber-200 overflow-hidden shadow-md">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-pulse absolute inline-flex h-full w-full rounded-full bg-amber-500 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                            </span>
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-amber-600">Your Next Move</p>
                        </div>
                    </div>

                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">{{ $nextBestAction['what_missing'] }}</h3>
                    <p class="text-sm text-gray-700 mb-5 max-w-2xl">{{ $nextBestAction['why_it_matters'] }}</p>

                    <a href="{{ route($nextBestAction['fix_route'], isset($latestScan) ? ['scan' => $latestScan->systemScanId()] : []) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-600 text-gray-900 rounded-lg text-sm font-bold transition-all shadow-md hover:shadow-lg sys-cta-primary">
                        Start Fix → 
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- ═══════════════────────────────────────────────── --}}
        {{-- WHAT NEEDS YOUR ATTENTION → Action Cards --}}
        {{-- ═══════════════────────────────────────────────── --}}
        <div class="mb-8">
            {{-- Action cards --}}
            <div class="space-y-4">
                @foreach(array_slice($topFindings, 0, 4) as $finding)
                @php
                    $severity = $loop->index <= 1 ? 'critical' : ($loop->index === 2 ? 'important' : 'minor');
                    $statusColor = $finding['status'] === 'Needs Action' ? 'bg-red-50 text-red-700 border-red-200' : 
                        ($finding['status'] === 'In Progress' ? 'bg-amber-50 text-amber-700 border-amber-200' : 
                        'bg-green-50 text-green-700 border-green-200');
                    $findingCtaClass = 'bg-gray-700 hover:bg-gray-800 text-white';
                    $findingCtaLabel = 'View Details →';

                    if ($finding['is_unlocked']) {
                        $findingCtaClass = $severity === 'minor'
                            ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            : 'bg-blue-100 text-blue-700 hover:bg-blue-200';
                        $findingCtaLabel = $severity === 'critical'
                            ? 'Open Fix Plan →'
                            : ($severity === 'important' ? 'View Recommendation →' : 'View Details →');
                    } elseif ($severity === 'critical') {
                        $findingCtaClass = 'bg-red-600 hover:bg-red-700 text-white';
                    } elseif ($severity === 'important') {
                        $findingCtaClass = 'bg-amber-500 hover:bg-amber-600 text-white';
                    }
                @endphp
                <div class="action-card bg-white rounded-lg border border-gray-200 p-6 transition-all duration-200 hover:shadow-md hover:border-gray-300 system-card" style="animation: fadeSlideUp 0.4s ease-out {{ $loop->index * 100 }}ms both">
                    <div class="flex items-start gap-4">
                        {{-- LEFT: Priority indicator --}}
                        <div class="shrink-0 mt-0.5">
                            @if($severity === 'critical')
                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                            @elseif($severity === 'important')
                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                            @else
                            <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                            @endif
                        </div>

                        {{-- CENTER: Title, explanation, actions --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="card-eyebrow" style="margin-bottom:6px">Fix Card</p>
                                    <h4 class="text-base font-bold text-gray-900">{{ $finding['what_missing'] }}</h4>
                                    <p class="text-sm text-gray-600 mt-1 leading-relaxed card-body" style="margin-bottom:0">{{ $finding['why_it_matters'] }}</p>
                                </div>
                                <span class="shrink-0 ml-4 px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusColor }}">
                                    {{ $finding['status'] }}
                                </span>
                            </div>

                            {{-- Action items --}}
                            @if(!empty($finding['action_items']))
                            <ul class="mt-3 space-y-2">
                                @foreach($finding['action_items'] as $item)
                                <li class="flex items-start gap-2 text-sm text-gray-600">
                                    <span class="text-gray-400 font-bold mt-0.5">•</span>
                                    <span>{{ $item }}</span>
                                </li>
                                @endforeach
                            </ul>
                            @endif

                            {{-- CTA --}}
                            <div class="mt-4 card-action">
                                @if($finding['fix_route'])
                                <a href="{{ route($finding['fix_route'], isset($latestScan) ? ['scan' => $latestScan->systemScanId()] : []) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 whitespace-nowrap {{ $findingCtaClass }}">
                                    @if($finding['is_unlocked'])
                                        {{ $findingCtaLabel }}
                                    @else
                                        Unlock {{ $finding['fix_tier'] }} — {{ $finding['fix_price'] }} →
                                    @endif
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        </div>

        {{-- LOCKED VALUE TEASER --}}
        @if($tierRank < 4)
        <div class="mt-6 relative rounded-xl border border-gray-200 overflow-hidden" style="animation: fadeSlideUp 0.5s ease-out 0.7s both">
            <div class="absolute inset-0 bg-linear-to-b from-white/50 to-white/90 locked-teaser z-10 flex flex-col items-center justify-center">
                <div class="w-12 h-12 rounded-full bg-gray-900 flex items-center justify-center mb-3 shadow-lg">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="2" stroke-width="1.5"/><path d="M8 11V7a4 4 0 118 0v4" stroke-width="1.5"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-900 mb-1">Full Intelligence Locked</p>
                <p class="text-xs text-gray-500 mb-3">Upgrade to unlock your complete analysis</p>
                @if($nextUpgrade)
                <a href="{{ route($nextUpgrade['route']) }}" class="cta-glow inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-lg text-sm font-bold transition-all shadow-lg hover:shadow-xl">
                    Unlock Full Report →
                </a>
                @endif
            </div>
            <div class="p-6 grid grid-cols-3 gap-4 select-none" aria-hidden="true">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="h-3 w-20 bg-gray-200 rounded mb-3"></div>
                    <div class="h-10 w-16 bg-gray-200 rounded-lg mb-2"></div>
                    <div class="h-2 w-24 bg-gray-100 rounded"></div>
                    <p class="text-[10px] text-gray-300 mt-2 font-medium">AI Visibility Score</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="h-3 w-16 bg-gray-200 rounded mb-3"></div>
                    <div class="space-y-2">
                        <div class="h-2 w-full bg-gray-200 rounded"></div>
                        <div class="h-2 w-3/4 bg-gray-200 rounded"></div>
                        <div class="h-2 w-1/2 bg-gray-100 rounded"></div>
                    </div>
                    <p class="text-[10px] text-gray-300 mt-2 font-medium">Complete Signal Map</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="h-3 w-24 bg-gray-200 rounded mb-3"></div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2"><div class="h-2 w-2 bg-gray-200 rounded-full"></div><div class="h-2 w-20 bg-gray-200 rounded"></div></div>
                        <div class="flex items-center gap-2"><div class="h-2 w-2 bg-gray-200 rounded-full"></div><div class="h-2 w-16 bg-gray-200 rounded"></div></div>
                        <div class="flex items-center gap-2"><div class="h-2 w-2 bg-gray-100 rounded-full"></div><div class="h-2 w-12 bg-gray-100 rounded"></div></div>
                    </div>
                    <p class="text-[10px] text-gray-300 mt-2 font-medium">Implementation Plan</p>
                </div>
            </div>
        </div>
        @endif
    </div>
@endif

{{-- ═══════════════════════════════════════════════════ --}}
{{-- NEXT BEST ACTION: Dominant conversion trigger      --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if($tierRank > 0 && $tierRank < 4 && $nextUpgrade)
    <div class="mb-8" style="animation: fadeSlideUp 0.5s ease-out 0.3s both">
        <div class="relative bg-linear-to-br from-gray-900 via-gray-900 to-gray-800 rounded-2xl overflow-hidden shadow-2xl" style="animation: pulseGlow 3s ease-in-out infinite">
            {{-- Decorative grid overlay --}}
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22><rect width=%2240%22 height=%2240%22 fill=%22none%22 stroke=%22white%22 stroke-width=%220.5%22/></svg>');"></div>
            <div class="relative p-8 sm:p-10">
                <div class="flex items-center gap-2 mb-4">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-400"></span>
                    </span>
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-amber-400">Recommended Next Move</p>
                </div>

                <h3 class="text-2xl sm:text-3xl font-extrabold text-white mb-3 leading-tight">
                    @if($nextUpgrade['label'] === 'Signal Analysis')
                        {{ $dashIsHighScore ? 'Map where your coverage stops — and which cities to enter next' : 'Fix your core signals to unlock full visibility' }}
                    @elseif($nextUpgrade['label'] === 'Action Plan')
                        {{ $dashIsHighScore ? 'Structure your territory before competitors claim adjacent areas' : 'Take structural control of your market position' }}
                    @elseif($nextUpgrade['label'] === 'Guided Execution')
                        Activate the full system — own your market
                    @else
                        {{ $nextUpgrade['description'] }}
                    @endif
                </h3>

                <p class="text-sm text-gray-400 leading-relaxed mb-2 max-w-xl">{{ $nextUpgrade['description'] }}</p>
                <p class="text-[11px] text-gray-500 leading-relaxed mb-2 max-w-xl">Recommended from your latest observed signal set and current uncovered categories.</p>
                @if($dashIsHighScore)
                <p class="text-sm text-gray-500 leading-relaxed mb-2 max-w-xl">Uncovered areas remain open. Search systems reward coverage over time, and early expansion compounds advantage.</p>
                @endif
                <p class="text-sm text-gray-500 mb-8">
                    <span class="text-amber-400 font-bold">{{ $nextUpgrade['issue_count'] }} {{ Str::plural('issue', $nextUpgrade['issue_count']) }}</span>
                    detected — this upgrade resolves them.
                </p>

                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route($nextUpgrade['route']) }}" class="cta-glow inline-flex items-center gap-2 px-8 py-4 bg-amber-500 hover:bg-amber-400 text-gray-900 rounded-xl text-base font-extrabold transition-all shadow-lg shadow-amber-500/20 hover:shadow-amber-400/30">
                        @if($nextUpgrade['label'] === 'Signal Analysis')
                            {{ $dashIsHighScore ? 'Expand into New Cities' : 'Unlock Signal Analysis' }} — {{ $nextUpgrade['price'] }}
                        @elseif($nextUpgrade['label'] === 'Action Plan')
                            {{ $dashIsHighScore ? 'Activate Territory Coverage' : 'Resolve Structural Gaps' }} — {{ $nextUpgrade['price'] }}
                        @elseif($nextUpgrade['label'] === 'Guided Execution')
                            Activate Full System — {{ $nextUpgrade['price'] }}
                        @else
                            Unlock {{ $nextUpgrade['label'] }} — {{ $nextUpgrade['price'] }}
                        @endif
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="{{ url('/book?entry=consultation') }}" class="inline-flex items-center gap-2 px-5 py-3 text-gray-400 hover:text-white border border-gray-700 hover:border-gray-500 rounded-xl text-sm font-medium transition-all">
                        Optional: Book Consultation
                    </a>
                </div>

                <p class="text-xs text-gray-600 mt-6">This resolves the highest-impact gaps detected in your scan and advances your market position.</p>
            </div>
        </div>
@elseif($tierRank >= 4)
    {{-- Fully activated — celebratory card --}}
    <div class="mb-8" style="animation: fadeSlideUp 0.5s ease-out 0.3s both">
        <div class="bg-linear-to-br from-emerald-50 to-green-50 rounded-2xl border-2 border-green-200 overflow-hidden shadow-sm">
            <div class="p-8 sm:p-10">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-green-600 mb-0.5">System Fully Activated</p>
                        <h3 class="text-xl font-extrabold text-gray-900">All layers unlocked. Your system is live.</h3>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-6 max-w-lg">Your analysis layers are complete. The next step is implementation — we build the structure that makes AI systems cite your business as the answer.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ url('/book?entry=consultation') }}" class="cta-glow inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition-all shadow-md">
                        Book Consultation →
                    </a>
                    <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-5 py-3 text-gray-700 bg-white border border-gray-200 hover:border-green-300 rounded-xl text-sm font-medium transition-all">
                        Re-check Your Position
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- ═══════════════════════════════════════════════════ --}}

{{-- HIGH-SCORE EXPANSION HOOK: territory / geographic positioning --}}
@if($dashIsHighScore && $scanProjects->count() > 0)
<div class="mb-8" style="animation: fadeSlideUp 0.5s ease-out 0.2s both">
    <div class="relative bg-linear-to-br from-gray-900 via-gray-900 to-gray-800 rounded-2xl overflow-hidden shadow-xl border border-gray-700">
        <div class="absolute inset-0 opacity-[0.025]" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22><rect width=%2240%22 height=%2240%22 fill=%22none%22 stroke=%22white%22 stroke-width=%220.5%22/></svg>');"></div>
        <div class="relative p-7 sm:p-9">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-amber-400">Territory Intelligence</p>
                <p class="text-[10px] text-gray-600 uppercase tracking-wider">Level 1 Complete — Expansion at Level 2</p>
            </div>
            @if($dashIsMaxScore)
            <h3 class="text-xl sm:text-2xl font-extrabold text-white mb-2 leading-tight">You are strong — but not protected.<br class="hidden sm:block"><span class="text-amber-400">Others can still enter your market.</span></h3>
            @else
            <h3 class="text-xl sm:text-2xl font-extrabold text-white mb-2 leading-tight">Visible in {{ $dashLocalCities }} {{ $dashLocalCities === 1 ? 'city' : 'cities' }}.<br class="hidden sm:block"><span class="text-amber-400">Your market spans {{ $dashRegionalCities }}.</span></h3>
            @endif
            <p class="text-sm text-gray-400 leading-relaxed mb-5 max-w-lg">Your footprint covers {{ $dashFootprintPct }}% of reachable territory. {{ $dashMissedAreas }} service areas answer AI queries without citing you. Visibility without coverage is unstable. Control ends where coverage stops.</p>
            <p class="text-[11px] text-gray-500 mb-4">Observed: score and scan signals. Modeled: territory range estimates to size opportunity before deeper expansion layers.</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-5">
                <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700 text-center">
                    <p class="text-xl font-extrabold text-amber-400 mb-0.5">{{ $dashUncovered }}</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">Nearby Areas Uncovered</p>
                </div>
                <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700 text-center">
                    <p class="text-xl font-extrabold text-amber-400 mb-0.5">{{ $dashMissedAreas }}</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">Unserved Service Areas</p>
                </div>
                <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700 text-center col-span-2 sm:col-span-1">
                    <p class="text-xl font-extrabold text-amber-400 mb-0.5">{{ $dashFootprintPct }}%</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">Current Footprint</p>
                </div>
            </div>
            <p class="text-xs text-gray-600 mb-5">Signal Analysis ($99) identifies where your signals are failing. Action Plan ($249) gives you a ranked fix list from your scan. Guided Execution ($489) turns that plan into a step-by-step checklist you track in your dashboard.</p>
            <p class="text-[11px] text-gray-500 mb-5">Use the next layer to validate these modeled ranges with deeper signal evidence before deployment.</p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('onboarding.start', ['tier' => 'expansion', 'plan' => 'authority-engine']) }}" class="cta-glow inline-flex items-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-400 text-gray-900 rounded-xl text-sm font-extrabold transition-all shadow-lg shadow-amber-500/20">
                    Expand into New Cities →
                </a>
                <a href="{{ url('/pricing') }}" class="inline-flex items-center gap-2 px-4 py-3 text-gray-400 hover:text-white border border-gray-700 hover:border-gray-500 rounded-xl text-xs font-medium transition-all">
                    Secondary: See territory coverage tiers →
                </a>
            </div>
        </div>
    </div>
@endif

        <!-- AI Scan Projects Section -->
        <div id="ai-scans" class="mb-8 sys-section">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Scan History & Tracking</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $totalScans }} {{ $totalScans === 1 ? 'scan' : 'scans' }} on record. This dashboard is your scan timeline; open any item to view its full report.</p>
                </div>
                <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Scan New Domain
                </a>
            </div>

            @if($scanProjects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($scanProjects as $project)
                @php
                    $projectScore = (int) ($project->score ?? 0);
                    $projectCardHeaderClass = 'bg-red-50 border-b border-red-100';
                    $projectScoreBadgeClass = 'bg-red-100 text-red-800';
                    $projectBandTextClass = 'text-red-700';
                    $projectBandLabel = 'Needs Work';

                    if ($projectScore >= 90) {
                        $projectCardHeaderClass = 'bg-green-50 border-b border-green-100';
                        $projectScoreBadgeClass = 'bg-green-100 text-green-800';
                        $projectBandTextClass = 'text-green-700';
                        $projectBandLabel = 'Visible';
                    } elseif ($projectScore >= 80) {
                        $projectCardHeaderClass = 'bg-blue-50 border-b border-blue-100';
                        $projectScoreBadgeClass = 'bg-blue-100 text-blue-800';
                        $projectBandTextClass = 'text-blue-700';
                        $projectBandLabel = 'Visible';
                    } elseif ($projectScore >= 70) {
                        $projectCardHeaderClass = 'bg-blue-50 border-b border-blue-100';
                        $projectScoreBadgeClass = 'bg-blue-100 text-blue-800';
                        $projectBandTextClass = 'text-blue-700';
                        $projectBandLabel = 'Positioning';
                    } elseif ($projectScore >= 40) {
                        $projectCardHeaderClass = 'bg-yellow-50 border-b border-yellow-100';
                        $projectScoreBadgeClass = 'bg-yellow-100 text-yellow-800';
                        $projectBandTextClass = 'text-yellow-700';
                        $projectBandLabel = 'Partial';
                    }

                    $projectScoreChangeClass = 'text-gray-500';
                    if ($project->score_change > 0) {
                        $projectScoreChangeClass = 'text-green-600';
                    } elseif ($project->score_change < 0) {
                        $projectScoreChangeClass = 'text-red-600';
                    }
                @endphp
                <div class="bg-white rounded-xl border-2 border-gray-100 shadow-sm hover:border-blue-200 hover:shadow-lg hover:-translate-y-0.5 transition-all overflow-hidden">
                    <!-- Score header band -->
                    <div class="px-5 py-3 flex items-center justify-between {{ $projectCardHeaderClass }}">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold {{ $projectScoreBadgeClass }}">{{ $project->score ?? '—' }}</span>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider {{ $projectBandTextClass }}">{{ $projectBandLabel }}</p>
                                @if($project->score_change !== null)
                                <p class="text-xs font-medium mt-0.5 {{ $projectScoreChangeClass }}">
                                    @if($project->score_change > 0) ↑ +{{ $project->score_change }} @elseif($project->score_change < 0) ↓ {{ $project->score_change }} @else — No change @endif
                                    since last scan
                                </p>
                                @endif
                            </div>
                        </div>
                        @if($project->upgrade_plan)
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">
                            {{ $project->upgrade_plan === 'authority-engine' ? 'Authority' : 'Citation' }}
                        </span>
                        @endif
                    </div>

                    <!-- Project body -->
                    <div class="p-5">
                        <h4 class="text-sm font-semibold text-gray-900 truncate" title="{{ $project->url }}">{{ $project->domain() }}</h4>
                        <p class="text-xs text-gray-500 mt-1">Scanned {{ $project->scanned_at?->diffForHumans() ?? $project->created_at->diffForHumans() }}</p>
                        <p class="text-xs mt-1 font-medium {{ $project->paid ? 'text-green-600' : 'text-amber-600' }}">
                            {{ $project->paid ? 'Unlocked' : 'Pending Unlock' }}
                        </p>

                        {{-- Scan Progression / Stagnation Messaging --}}
                        @if($project->is_repeat_scan)
                          @if($project->score_change !== null && $project->score_change > 0)
                          <div class="mt-3 p-2.5 bg-green-50 border border-green-100 rounded-lg">
                            <p class="text-xs font-medium text-green-700">↑ Your position improved — but {{ max(0, 100 - ($project->score ?? 0)) }}% of your market remains uncovered.</p>
                          </div>
                          @elseif($project->score_change !== null && $project->score_change === 0)
                          <div class="mt-3 p-2.5 bg-amber-50 border border-amber-200 rounded-lg">
                            <p class="text-xs font-medium text-amber-800">⚡ Your position hasn't changed — without action, competitors continue pulling ahead.</p>
                          </div>
                          @elseif($project->score_change !== null && $project->score_change < 0)
                          <div class="mt-3 p-2.5 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-xs font-medium text-red-700">⚠ Your position weakened — dropped {{ abs($project->score_change) }} points. A coverage system stops this decline.</p>
                          </div>
                          @else
                          <div class="mt-3 p-2.5 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-xs font-medium text-gray-600">Repeat scan — tracking changes reveals whether your market position is improving or eroding.</p>
                          </div>
                          @endif
                        @endif

                        @if($project->fastest_fix && $project->status === 'scanned')
                        <div class="mt-3 p-2.5 bg-blue-50 border border-blue-100 rounded-lg">
                            <p class="text-xs font-medium text-blue-600 uppercase tracking-wider mb-0.5">Your Fastest Fix</p>
                            <p class="text-xs text-blue-800 line-clamp-2">{{ $project->fastest_fix }}</p>
                        </div>
                        @endif

                        @if(!empty($project->issues) && is_array($project->issues))
                        <p class="text-xs text-gray-500 mt-3">{{ count($project->issues) }} visibility gap{{ count($project->issues) !== 1 ? 's' : '' }} in your coverage</p>
                        @endif

                        {{-- Score-tiered action buttons --}}
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('dashboard.scans.show', ['scan' => $project->publicScanId()]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Your Report
                            </a>
                            @if(!$project->upgrade_plan)
                            <a href="{{ route('dashboard.scans.show', ['scan' => $project->publicScanId()]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                @if(($project->score ?? 0) < 40) Resolve Your Gaps @elseif(($project->score ?? 0) < 70) Fix Your Visibility @elseif(($project->score ?? 0) < 90) Unlock Full Coverage @else Activate System @endif
                            </a>
                            @elseif($project->upgrade_plan === 'optimization' && !$project->onboarding_submission_id)
                            <a href="{{ route('onboarding.start') }}?scan_id={{ $project->id }}&plan=authority-engine" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Start Deployment
                            </a>
                            @elseif($project->onboarding_submission_id)
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-lg">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Deploying
                            </span>
                            @else
                            {{-- Mid-tier upgrade (diagnostic/fix-strategy) — link back to expanded report --}}
                            <a href="{{ route('dashboard.scans.show', ['scan' => $project->publicScanId()]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                Unlock Next Level
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- System-tier aware upgrade path -->
            @if($tierRank >= 4)
            {{-- Fully activated — point to strategy / deployment --}}
            <div class="mt-6 bg-linear-to-br from-emerald-50 to-green-50 rounded-xl border border-green-200 p-6">
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <h4 class="font-semibold text-gray-900">Your System Is Fully Activated</h4>
                </div>
                <p class="text-sm text-gray-600 mb-4">All analysis layers are complete. The next step is implementation — we build the coverage infrastructure that makes AI systems return your business as the answer.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ url('/book?entry=consultation') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                        Book Consultation
                    </a>
                    <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-green-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        Re-check Your Position
                    </a>
                </div>
            </div>

            @if($tierRank >= 3)
            {{-- Has Action Plan ($249) — push to Guided Execution ($489) --}}
            <div class="mt-6 bg-linear-to-br from-indigo-50 to-blue-50 rounded-xl border border-indigo-200 p-6 hover:shadow-lg transition-all">
                <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-1">Your Next Level</p>
                <h4 class="font-semibold text-gray-900 mb-1">You've built your action plan — now execute it with guidance</h4>
                <p class="text-sm text-gray-600 mb-4">Guided Execution turns your action plan into a step-by-step checklist inside your dashboard with progress tracking.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('checkout.system-activation') }}" class="cta-glow inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold transition-all shadow-sm">
                        Start Guided Execution — $489 →
                    </a>
                    <a href="{{ url('/book?entry=consultation') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-indigo-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        Secondary: Book Consultation
                    </a>
                </div>
            </div>

            @elseif($tierRank >= 2)
            {{-- Has Signal Analysis ($99) — push to Action Plan ($249) --}}
            <div class="mt-6 bg-linear-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-200 p-6 relative overflow-hidden hover:shadow-lg transition-all">
                <div class="absolute top-3 right-3 px-2 py-0.5 bg-blue-600 text-white text-[10px] font-bold uppercase rounded-full">Your Next Level</div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-1">Level 3</p>
                <h4 class="font-semibold text-gray-900 mb-1">You've got your signal analysis — now get your action plan</h4>
                <p class="text-sm text-gray-600 mb-4">Action Plan gives you a ranked fix list from your scan data, ordered by impact so you execute the highest-value changes first.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('checkout.structural-leverage') }}" class="cta-glow inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition-all shadow-sm">
                        Get Your Action Plan — $249 →
                    </a>
                    <a href="{{ route('checkout.system-activation') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-blue-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        Secondary: Start Guided Execution — $489
                    </a>
                </div>
            </div>

            @elseif($tierRank >= 1)
            {{-- Has base scan ($2) — show both $99 and $249 paths --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                @if($dashIsHighScore)
                <div class="bg-linear-to-br from-indigo-50 to-blue-50 rounded-xl border border-indigo-100 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all md:col-span-2">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-1">Recommended Next Move</p>
                    @if($dashIsMaxScore)
                    <h4 class="font-semibold text-gray-900 mb-1">Structure Territory to Prevent Entry</h4>
                    <p class="text-sm text-gray-600 mb-4">For high-score positions, this is the fastest path to reduce displacement risk and lock adjacent coverage.</p>
                    <a href="{{ route('checkout.structural-leverage') }}" class="cta-glow inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition-all">
                        Recommended: Resolve Structural Gaps — $249 →
                    </a>
                    @else
                    <h4 class="font-semibold text-gray-900 mb-1">Identify Open Entry Zones First</h4>
                    <p class="text-sm text-gray-600 mb-4">For high-score positions, map where competitors can still enter before moving into structural deployment.</p>
                    <a href="{{ route('checkout.signal-expansion') }}" class="cta-glow inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition-all">
                        Recommended: Unlock Signal Analysis — $99 →
                    </a>
                    @endif
                </div>
                @else
                <div class="bg-linear-to-br from-indigo-50 to-blue-50 rounded-xl border border-indigo-100 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-1">Level 2</p>
                    <h4 class="font-semibold text-gray-900 mb-1">Signal Analysis</h4>
                    <p class="text-sm text-gray-600 mb-4">Full signal breakdown by category, priority gap visibility, and deeper diagnostic context from your scan.</p>
                    <a href="{{ route('checkout.signal-expansion') }}" class="cta-glow inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold transition-all">
                        Unlock Signal Analysis — $99 →
                    </a>
                </div>
                <div class="bg-linear-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-100 p-6 relative overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <div class="absolute top-3 right-3 px-2 py-0.5 bg-blue-600 text-white text-[10px] font-bold uppercase rounded-full">Most Popular</div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-1">Level 3</p>
                    <h4 class="font-semibold text-gray-900 mb-1">Action Plan</h4>
                    <p class="text-sm text-gray-600 mb-4">Ranked fix list from your scan, ordered by impact — the fastest path from diagnosis to execution.</p>
                    <a href="{{ route('checkout.structural-leverage') }}" class="cta-glow inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition-all">
                        Get Your Action Plan — $249 →
                    </a>
                </div>
                @endif
            </div>
            @endif
            @endif
            @else
            <!-- Empty state -->
            <div class="bg-white rounded-xl border border-gray-100 p-8 text-center mb-6">
                <p class="font-medium text-gray-900 mb-1">Your scan results will appear here.</p>
                <p class="text-sm text-gray-500 mb-5">Run a scan above to establish your baseline — results save automatically to your account.</p>
                <a href="{{ route('scan.start') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg text-sm transition-all">
                    Run your first AI Visibility Scan
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
            @endif
        </div>

        @if(!$dashIsHighScore)
        <!-- Suggested Actions Panel -->
        <div class="bg-linear-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-100 p-6 shadow-sm">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Your Next Move</h3>
                    <p class="text-sm text-gray-600">Actions that strengthen your market position</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Customer actions — tier-aware, no staff links --}}
                <a href="{{ route('quick-scan.show') }}" class="block p-4 bg-white rounded-lg border border-blue-200 hover:border-blue-400 hover:shadow-lg hover:-translate-y-0.5 transition-all group ring-1 ring-blue-100">
                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600 text-sm">Re-check Your Position</h4>
                    <p class="text-xs text-gray-500">See if your visibility has changed</p>
                </a>

                @if($tierRank === 0)
                <a href="{{ route('scan.start') }}" class="block p-4 bg-white rounded-lg border border-amber-200 hover:border-amber-400 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                    <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-amber-600 text-sm">Start Your First Scan</h4>
                    <p class="text-xs text-gray-500">Establish your AI citation baseline</p>
                </a>
                @else
                <a href="{{ url('/pricing') }}" class="block p-4 bg-white rounded-lg border border-amber-200 hover:border-amber-400 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                    <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-amber-600 text-sm">Expand Your Coverage</h4>
                    <p class="text-xs text-gray-500">Unlock the next level of intelligence</p>
                </a>
                @endif

                @if(isset($tierRank) && $tierRank >= 2)
                <a href="{{ route('onboarding.start', ['tier' => 'expansion', 'plan' => 'authority-engine']) }}" class="block p-4 bg-white rounded-lg border border-amber-200 hover:border-amber-400 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                    <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-amber-600 text-sm">Activate Deployment</h4>
                    <p class="text-xs text-gray-500">We build your coverage infrastructure</p>
                </a>
                @else
                <a href="{{ url('/book?entry=consultation') }}" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                    <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4 text-gray-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600 text-sm">Book Consultation</h4>
                    <p class="text-xs text-gray-500">Interpret your visibility and map the right expansion path.</p>
                </a>
                @endif
            </div>
        </div>
        @endif

    </div>
</details>

</div>

@push('scripts')
<script>
(function(){
    var scoreBand = {{ ((int) ($scanProjects->first()?->score ?? 0)) >= 88 ? "'high'" : (((int) ($scanProjects->first()?->score ?? 0)) >= 60 ? "'mid'" : "'low'") }};

    // Track dashboard CTA clicks
    document.querySelectorAll('a[href*="onboarding/start"], a[href*="checkout/"], a[href*="scan/start"], a[href="/book"], a[href="/pricing"]').forEach(function(el){
    el.addEventListener('click',function(){
            fetch('/api/v1/track',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({event:'dashboard_cta_click',metadata:{label:(el.textContent||'').trim().substring(0,60),href:el.getAttribute('href')||'',source_page:'dashboard',user_state:'logged_in',role:'customer',score_band:scoreBand}})}).catch(function(){});
    });
  });

  // Staggered reveal for scan history cards
  var cards = document.querySelectorAll('#ai-scans .grid > div');
  cards.forEach(function(card, i){
    card.style.opacity = '0';
    card.style.transform = 'translateY(12px)';
    card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    setTimeout(function(){
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, 100 + (i * 100));
  });
})();
</script>
@endpush

@endsection
