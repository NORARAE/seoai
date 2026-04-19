<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Analyzing Your Site — SEO AI Co</title>
<link rel="canonical" href="{{ url('/scan/process') }}">
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
@include('partials.design-system')

/* ── Processing ── */
.proc-page{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 20px}
.proc-inner{max-width:480px;width:100%;text-align:center}

.proc-eye{
  font-size:.62rem;letter-spacing:.26em;text-transform:uppercase;
  color:var(--gold-secondary);margin-bottom:18px;
}
.proc-hed{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.5rem,2.8vw,2.1rem);font-weight:300;
  color:var(--ivory);line-height:1.2;margin-bottom:10px;
}
.proc-url{
  font-size:.82rem;color:var(--gold);word-break:break-all;
  margin-bottom:40px;
}

/* ── Spinner ── */
.proc-spinner{
  width:52px;height:52px;margin:0 auto 36px;position:relative;
}
.proc-spinner::before,.proc-spinner::after{
  content:'';position:absolute;inset:0;border-radius:50%;
  border:2px solid transparent;
}
.proc-spinner::before{
  border-top-color:var(--gold);
  animation:proc-spin 1s linear infinite;
}
.proc-spinner::after{
  border-bottom-color:rgba(200,168,75,.25);
  animation:proc-spin 1.6s linear infinite reverse;
}
@keyframes proc-spin{to{transform:rotate(360deg)}}

/* ── Stage messages ── */
.proc-stages{min-height:28px;margin-bottom:48px}
.proc-stage{
  font-size:.82rem;color:var(--muted);letter-spacing:.04em;
  opacity:0;transform:translateY(8px);
  transition:opacity .5s,transform .5s;
}
.proc-stage.active{opacity:1;transform:translateY(0)}
.proc-stage.done{color:rgba(200,168,75,.5)}

/* ── Progress bar ── */
.proc-bar-wrap{
  width:100%;height:2px;background:rgba(200,168,75,.08);
  border-radius:1px;margin-bottom:32px;overflow:hidden;
}
.proc-bar{
  height:100%;width:0;background:linear-gradient(90deg,var(--gold),var(--gold-lt));
  border-radius:1px;transition:width .6s var(--ease-out);
}

.proc-note{
  font-size:.74rem;color:rgba(168,168,160,.4);line-height:1.7;
}
</style>
</head>
<body class="proc-page">

<div class="proc-inner">
  <p class="proc-eye">Initializing Scan</p>
  <h1 class="proc-hed">Analyzing your site&hellip;</h1>
  <p class="proc-url">{{ $url }}</p>

  <div class="proc-spinner"></div>

  <div class="proc-bar-wrap"><div class="proc-bar" id="procBar"></div></div>

  <div class="proc-stages" id="procStages">
    <p class="proc-stage" data-step="0">Checking site structure&hellip;</p>
    <p class="proc-stage" data-step="1">Mapping authority signals&hellip;</p>
    <p class="proc-stage" data-step="2">Analyzing AI search coverage&hellip;</p>
    <p class="proc-stage" data-step="3">Preparing your diagnostic&hellip;</p>
  </div>

  <p class="proc-note">Your results will be ready in moments.</p>
  <p style="text-align:center;margin-top:24px"><a href="{{ $nextRoute }}" style="font-size:.72rem;color:var(--muted);text-decoration:underline;text-underline-offset:3px;opacity:.5;transition:opacity .3s" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.5'">Taking too long? Continue &rarr;</a></p>
</div>

<script>
(function(){
  const stages = document.querySelectorAll('.proc-stage');
  const bar = document.getElementById('procBar');
  const total = stages.length;
  let current = 0;

  function advance(){
    if(current > 0 && stages[current-1]){
      stages[current-1].classList.remove('active');
      stages[current-1].classList.add('done');
    }
    if(current < total){
      stages[current].classList.add('active');
      bar.style.width = ((current+1)/total*100)+'%';
      current++;
      setTimeout(advance, 900 + Math.random()*400);
    } else {
      // All stages done — continue to next stateful step
      bar.style.width = '100%';
      setTimeout(function(){
        window.location.href = "{{ $nextRoute }}";
      }, 600);
    }
  }

  // Start after brief initial delay
  setTimeout(advance, 400);
})();
</script>
</body>
</html>
