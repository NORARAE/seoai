{{--
  Component: ai-score-orbit
  Props:
    $score  — int 0–100
    $label  — string displayed below the score
    $size   — 'sm' | 'md' | 'lg'  (default 'md')
--}}
@props(['score', 'label', 'size' => 'md'])

@php
  $cfg = match ($size) {
    'sm' => ['vb' => 120, 'outerR' => 50, 'innerR' => 36, 'sw' => 2.5, 'iw' => 1.5, 'glowR' => 56, 'scoreSize' => '1.8rem',  'labelSize' => '.52rem'],
    'lg' => ['vb' => 220, 'outerR' => 94, 'innerR' => 68, 'sw' => 2.5, 'iw' => 1.5, 'glowR' => 101,'scoreSize' => '3.9rem',    'labelSize' => '1rem'],
    default => ['vb' => 160, 'outerR' => 68, 'innerR' => 50, 'sw' => 2.5, 'iw' => 1.5, 'glowR' => 74, 'scoreSize' => '2.6rem', 'labelSize' => '.68rem'],
  };
  $vb          = $cfg['vb'];
  $cx          = $vb / 2;
  $cy          = $vb / 2;
  $outerR      = $cfg['outerR'];
  $innerR      = $cfg['innerR'];
  $glowR       = $cfg['glowR'];
  $safeScore   = max(0, min(100, (int) $score));
  $scoreBadge      = match(true){
    $safeScore >= 71 => 'Strong AI visibility',
    $safeScore >= 41 => 'Visibility gaps detected',
    default          => 'At risk — low AI visibility',
  };
  $scoreBadgeClass = match(true){
    $safeScore >= 71 => 'above',
    $safeScore >= 41 => 'emerging',
    default          => 'risk',
  };
  $outerCirc   = round(2 * M_PI * $outerR, 2);
  $innerCirc   = round(2 * M_PI * $innerR, 2);
  $glowCirc    = round(2 * M_PI * $glowR,  2);
  $outerOffset = round($outerCirc * (1 - $safeScore / 100), 2);
  $innerOffset = round($innerCirc * (1 - ($safeScore / 100) * 0.72), 2);
  // Rotating glow arc: short arc (18% of circumference), always visible
  $glowDash    = round($glowCirc * 0.22, 2);
  $glowGap     = round($glowCirc - $glowDash, 2);
  $uid         = 'orbit-' . substr(md5($label . $score . $size . microtime()), 0, 8);
@endphp

<div class="ai-orbit ai-orbit--{{ $size }}" id="{{ $uid }}"
     data-score="{{ $safeScore }}"
     role="img"
     aria-label="{{ $label }}: {{ $safeScore }} out of 100">

  <svg viewBox="0 0 {{ $vb }} {{ $vb }}" fill="none"
       xmlns="http://www.w3.org/2000/svg"
       aria-hidden="true" focusable="false">

    {{-- Rotating glow ring (outermost, decorative) --}}
    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $glowR }}"
            stroke="url(#{{ $uid }}-glow)"
            stroke-width="1.5"
            fill="none"
            stroke-linecap="round"
            stroke-dasharray="{{ $glowDash }} {{ $glowGap }}"
            transform="rotate(-90 {{ $cx }} {{ $cy }})"
            class="orbit-ring-glow"
            opacity="0"/>

    {{-- Outer track --}}
    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $outerR }}"
            stroke="rgba(200,168,75,0.14)"
            stroke-width="{{ $cfg['sw'] }}"
            fill="none"/>

    {{-- Outer score arc —  starts at full circumference (=0 filled) --}}
    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $outerR }}"
            stroke="url(#{{ $uid }}-grd)"
            stroke-width="{{ $cfg['sw'] }}"
            fill="none"
            stroke-linecap="round"
            stroke-dasharray="{{ $outerCirc }}"
            stroke-dashoffset="{{ $outerCirc }}"
            transform="rotate(-90 {{ $cx }} {{ $cy }})"
            class="orbit-arc orbit-arc--outer"
            data-final="{{ $outerOffset }}"/>

    {{-- Inner track --}}
    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $innerR }}"
            stroke="rgba(200,168,75,0.06)"
            stroke-width="{{ $cfg['iw'] }}"
            fill="none"/>

    {{-- Inner accent arc --}}
    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $innerR }}"
            stroke="rgba(200,168,75,0.34)"
            stroke-width="{{ $cfg['iw'] }}"
            fill="none"
            stroke-linecap="round"
            stroke-dasharray="{{ $innerCirc }}"
            stroke-dashoffset="{{ $innerCirc }}"
            transform="rotate(-90 {{ $cx }} {{ $cy }})"
            class="orbit-arc orbit-arc--inner"
            data-final="{{ $innerOffset }}"/>

    <defs>
      <linearGradient id="{{ $uid }}-grd" x1="0" y1="0" x2="{{ $vb }}" y2="0"
                      gradientUnits="userSpaceOnUse">
        <stop offset="0%"   stop-color="#c8a84b" stop-opacity=".88"/>
        <stop offset="100%" stop-color="#e7c873" stop-opacity="1"/>
      </linearGradient>
      <linearGradient id="{{ $uid }}-glow" x1="0" y1="0" x2="{{ $vb }}" y2="0"
                      gradientUnits="userSpaceOnUse">
        <stop offset="0%"   stop-color="#e7c873" stop-opacity="0"/>
        <stop offset="50%"  stop-color="#e7c873" stop-opacity=".60"/>
        <stop offset="100%" stop-color="#e7c873" stop-opacity="0"/>
      </linearGradient>
    </defs>
  </svg>

  <div class="orbit-center" aria-hidden="true">
    <span class="orbit-score" style="font-size:{{ $cfg['scoreSize'] }}">{{ $safeScore }}</span>
    <span class="orbit-label" style="font-size:{{ $cfg['labelSize'] }}">{{ $label }}</span>
    <span class="orbit-badge orbit-badge--{{ $scoreBadgeClass }}">{{ $scoreBadge }}</span>
  </div>
</div>

@once
<style>
.ai-orbit{position:relative;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0}
.ai-orbit--sm{width:120px;height:120px}
.ai-orbit--md{width:160px;height:160px}
.ai-orbit--lg{width:300px;height:300px}
.ai-orbit svg{position:absolute;inset:0;width:100%;height:100%}
.orbit-center{position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;gap:0;text-align:center;padding:8px}
.ai-orbit--lg .orbit-center{max-width:168px}
.ai-orbit--lg .orbit-score{margin-bottom:10px}
.ai-orbit--lg .orbit-label{margin-bottom:14px}
.orbit-score{font-family:'Cormorant Garamond',serif;font-weight:300;color:#f5f0e8;line-height:1;letter-spacing:.01em}
.orbit-label{font-family:'DM Sans',sans-serif;text-transform:uppercase;letter-spacing:.14em;color:rgba(200,168,75,.9);line-height:1.25;text-align:center}
.orbit-badge{display:inline-block;font-family:'DM Sans',sans-serif;font-size:.46rem;letter-spacing:.06em;text-transform:uppercase;padding:3px 12px;border-radius:20px;line-height:1.5;border:1px solid rgba(200,168,75,.22);opacity:0;transition:opacity .5s ease .3s}
.orbit-badge--risk{color:rgba(220,210,180,.9);background:rgba(200,168,75,.1)}
.orbit-badge--emerging{color:rgba(220,210,180,.9);background:rgba(200,168,75,.1)}
.orbit-badge--above{color:rgba(220,210,180,.9);background:rgba(200,168,75,.1)}
.ai-orbit.is-filled .orbit-badge{opacity:1}
.orbit-arc{transition:stroke-dashoffset 1.5s cubic-bezier(.22,.68,0,1.15)}
.orbit-arc--inner{transition-duration:1.8s;transition-delay:.2s}
.orbit-ring-glow{transform-origin:50% 50%;transform-box:fill-box;transition:opacity .3s ease}
.ai-orbit.is-scanning .orbit-ring-glow{opacity:.65;animation:orbitSweep .8s ease-out forwards}
@keyframes orbitSweep{from{transform:rotate(-90deg)}to{transform:rotate(270deg)}}
.ai-orbit.is-filled .orbit-ring-glow{opacity:1;animation:orbitSpin 16s linear infinite}
@keyframes orbitSpin{from{transform:rotate(-90deg)}to{transform:rotate(270deg)}}
@keyframes orbitPulse{
  0%,55%,100%{filter:drop-shadow(0 0 1px rgba(200,168,75,0))}
  65%{filter:drop-shadow(0 0 9px rgba(200,168,75,.2))}
  78%{filter:drop-shadow(0 0 3px rgba(200,168,75,.07))}
}
.ai-orbit.is-filled svg{animation:orbitPulse 7s ease-in-out infinite;animation-delay:.3s}
@media(max-width:600px){.ai-orbit--lg{width:240px;height:240px}}
@media(max-width:400px){.ai-orbit--lg{width:200px;height:200px}}
@media(prefers-reduced-motion:reduce){
  .orbit-ring-glow,.ai-orbit.is-scanning .orbit-ring-glow,.ai-orbit.is-filled .orbit-ring-glow{animation:none!important}
  .ai-orbit.is-filled svg{animation:none!important}
  .orbit-arc,.orbit-arc--inner{transition:none!important}
}
</style>

<script>
window._aiOrbitActivate = function(id){
  var el = document.getElementById(id);
  if (!el) return;
  var finalScore = parseInt(el.getAttribute('data-score') || '0', 10);
  if (!finalScore) { var s = el.querySelector('.orbit-score'); if (s) finalScore = parseInt(s.textContent||'0',10); }
  var scoreEl = el.querySelector('.orbit-score');
  function countUp(to, duration) {
    if (!scoreEl) return;
    var start = performance.now();
    scoreEl.textContent = '0';
    function tick(now) {
      var p = Math.min((now - start) / duration, 1);
      var e = p < 0.5 ? 2*p*p : -1+(4-2*p)*p;
      scoreEl.textContent = Math.round(to * e);
      if (p < 1) requestAnimationFrame(tick);
      else scoreEl.textContent = to;
    }
    requestAnimationFrame(tick);
  }
  function activate() {
    el.classList.add('is-scanning');
    setTimeout(function() {
      el.classList.remove('is-scanning');
      el.querySelectorAll('.orbit-arc').forEach(function(arc){
        arc.style.strokeDashoffset = arc.dataset.final;
      });
      countUp(finalScore, 1500);
      setTimeout(function(){ el.classList.add('is-filled'); }, 800);
    }, 200);
  }
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function(entries){
      if (entries[0].isIntersecting) { activate(); io.disconnect(); }
    }, { threshold: 0.2 });
    io.observe(el);
  } else {
    activate();
  }
};
</script>
@endonce

<script>
(function(){
  var id = '{{ $uid }}';
  function run(){ if (window._aiOrbitActivate) window._aiOrbitActivate(id); }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run);
  } else {
    run();
  }
})();
</script>
