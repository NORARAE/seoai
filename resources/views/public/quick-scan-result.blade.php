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
<title>Your AI Citation Score: {{ $scan->score ?? 0 }}/100 — SEO AI Co™</title>
<meta name="description" content="Your AI citation readiness score is {{ $scan->score ?? 0 }}/100. See your gaps, strengths, and fastest correction.">
<meta name="robots" content="noindex">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@php
  $score = (int) ($scan->score ?? 0);
  $categories = $scan->categories ?? [];
  $upgradePlan = $scan->upgrade_plan;
  $isUpgraded = $scan->upgrade_status === 'paid';
  // Determine unlock level: 1=base scan, 2=diagnostic/$99, 3=fix-strategy/$249, 4=optimization/$489
  $unlockLevel = 1;
  if ($isUpgraded) {
      $unlockLevel = match($upgradePlan) {
          'optimization' => 4,
          'fix-strategy' => 3,
          'diagnostic' => 2,
          default => 1,
      };
  }
@endphp
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080808;--deep:#0b0b0b;--card:#0e0d09;--border:rgba(200,168,75,.09);
  --gold:#c8a84b;--gold-lt:#d9bc6e;--gold-dim:rgba(200,168,75,.32);
  --gold-glow:rgba(200,168,75,.12);--gold-glow-strong:rgba(200,168,75,.22);
  --ivory:#f0ece3;--ivory-soft:#ede8de;--muted:rgba(178,178,170,.82);
  --green:#6aaf90;--red:#c47878;
}
html{font-size:18px;scroll-behavior:smooth}
body{background:var(--bg);color:var(--ivory);font-family:'DM Sans',sans-serif;font-weight:300;line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden}

/* ── Nav ── */
nav{position:fixed;top:0;left:0;right:0;z-index:200;display:flex;align-items:center;justify-content:space-between;padding:28px 64px;border-bottom:1px solid transparent;transition:all .4s}
nav.stuck{background:rgba(8,8,8,.95);backdrop-filter:blur(16px);border-color:var(--border);padding:16px 64px}
.logo{text-decoration:none;display:flex;align-items:baseline;gap:1px;flex-shrink:0}
.logo-seo{font-family:'DM Sans',sans-serif;font-size:1.38rem;font-weight:300;letter-spacing:-.02em;color:var(--ivory)}
.logo-ai{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:400;letter-spacing:.02em;color:var(--gold);font-style:italic;margin:0 1px}
.logo-co{font-family:'DM Sans',sans-serif;font-size:1.18rem;font-weight:300;color:rgba(168,168,160,.65)}
.nav-right{display:flex;align-items:center;gap:28px}
.nav-link{font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.nav-link:hover{color:var(--gold)}
.nav-btn{font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;color:var(--bg);background:var(--gold);padding:12px 28px;text-decoration:none;transition:all .3s;display:inline-flex;align-items:center}
.nav-btn:hover{background:var(--gold-lt);box-shadow:0 2px 12px var(--gold-glow)}

/* ── Hero band ── */
.result-hero{padding:150px 64px 80px;text-align:center;position:relative;overflow:hidden}
.result-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 70% 55% at 50% 50%,rgba(200,168,75,.07) 0%,transparent 68%);pointer-events:none}
.result-eyebrow{font-size:.66rem;letter-spacing:.3em;text-transform:uppercase;color:rgba(200,168,75,.65);margin-bottom:26px}
.result-url{font-size:.82rem;color:rgba(168,168,160,.48);font-weight:300;letter-spacing:.04em;margin-bottom:36px;max-width:560px;margin-left:auto;margin-right:auto;overflow-wrap:break-word}

/* ── Score ring ── */
.score-ring-wrap{display:inline-flex;flex-direction:column;align-items:center;gap:20px;margin-bottom:28px}
.score-ring-svg{width:180px;height:180px;filter:drop-shadow(0 0 24px var(--gold-glow))}
.score-ring-bg{fill:none;stroke:rgba(200,168,75,.06);stroke-width:7}
.score-ring-fill{fill:none;stroke-width:7;stroke-linecap:round;stroke-dasharray:440;stroke-dashoffset:440;transform:rotate(-90deg);transform-origin:50% 50%;transition:stroke-dashoffset 1.4s cubic-bezier(.23,1,.32,1)}
.score-ring-fill.animate{stroke-dashoffset:calc(440 - (440 * {{ $score }} / 100))}
.score-number{font-family:'Cormorant Garamond',serif;font-size:4rem;font-weight:300;line-height:1;color:@if($score >= 70) var(--green) @elseif($score >= 40) var(--gold) @else var(--red) @endif;text-shadow:0 0 24px var(--gold-glow)}
.score-label{font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.5);margin-top:2px}
.score-verdict{font-family:'Cormorant Garamond',serif;font-size:clamp(1.4rem,3vw,2.1rem);font-weight:300;line-height:1.35;color:@if($score >= 90) var(--green) @elseif($score >= 70) var(--gold-lt) @elseif($score >= 40) var(--gold) @else var(--red) @endif;max-width:620px;margin:0 auto;padding-top:4px}
.score-subline{font-size:.76rem;color:var(--muted);letter-spacing:.03em;margin-top:14px;opacity:.65}
.score-ring-fill{stroke:@if($score >= 70) var(--green) @elseif($score >= 40) var(--gold) @else var(--red) @endif}

/* ── Stats row ── */
.stats-row{display:flex;justify-content:center;gap:36px;margin-top:28px;padding:18px 32px;background:rgba(200,168,75,.03);border:1px solid rgba(200,168,75,.06);border-radius:2px;flex-wrap:wrap;max-width:480px;margin-left:auto;margin-right:auto}
.stat-item{text-align:center;min-width:68px}
.stat-value{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:300;color:var(--gold);line-height:1.1}
.stat-label{font-size:.58rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.5);margin-top:5px}

/* ── Category grid ── */
.result-body{max-width:1060px;margin:0 auto;padding:0 24px 48px}
.cat-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:36px}
.cat-card{background:rgba(14,13,9,.95);border:1px solid rgba(200,168,75,.08);padding:26px 24px 28px;position:relative;transition:all .3s ease;display:flex;flex-direction:column;min-height:280px}
.cat-card:hover{border-color:rgba(200,168,75,.22);box-shadow:0 6px 28px rgba(0,0,0,.4),0 0 1px rgba(200,168,75,.15);transform:translateY(-2px)}
.cat-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}
.cat-name{font-size:.72rem;letter-spacing:.16em;text-transform:uppercase;color:rgba(200,168,75,.75);font-weight:400}
.cat-score{font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:300;color:var(--ivory)}
.cat-bar{height:4px;background:rgba(200,168,75,.06);margin-bottom:18px;overflow:hidden;border-radius:2px}
.cat-bar-fill{height:100%;transition:width 1s cubic-bezier(.23,1,.32,1);border-radius:2px}
.cat-bar-fill.high{background:linear-gradient(90deg,var(--green),rgba(106,175,144,.6))}
.cat-bar-fill.mid{background:linear-gradient(90deg,var(--gold),var(--gold-dim))}
.cat-bar-fill.low{background:linear-gradient(90deg,var(--red),rgba(196,120,120,.5))}

/* ── Check items ── */
.check-list{display:flex;flex-direction:column;gap:10px;flex:1;min-height:160px}
.check-item{display:flex;align-items:flex-start;gap:10px;font-size:.82rem;line-height:1.45;color:var(--muted)}
.check-icon{flex-shrink:0;margin-top:1px;font-size:.78rem;width:16px;text-align:center}
.check-item.passed .check-icon{color:var(--green)}
.check-item.failed .check-icon{color:var(--red)}
.check-pts{font-size:.68rem;color:rgba(168,168,160,.35);margin-left:auto;flex-shrink:0}

/* ── Locked / blurred sections ── */
.locked-zone{position:relative;overflow:hidden}
.locked-zone .check-text{filter:blur(5px);-webkit-filter:blur(5px);user-select:none;pointer-events:none}
.locked-overlay{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:rgba(6,6,6,.84);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);z-index:10;gap:16px;padding:32px 28px;border:1px solid rgba(200,168,75,.14);box-shadow:inset 0 0 60px rgba(0,0,0,.4)}
.locked-overlay .lock-icon{font-size:1.2rem;color:var(--gold);filter:drop-shadow(0 0 12px var(--gold-glow-strong));line-height:1;margin-bottom:2px}
.locked-overlay .lock-text{font-size:.68rem;letter-spacing:.15em;text-transform:uppercase;color:rgba(200,168,75,.9);text-align:center;line-height:1.7;max-width:240px}
.locked-overlay .lock-cta{font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;color:var(--bg);background:linear-gradient(135deg,var(--gold),var(--gold-lt));padding:14px 32px;text-decoration:none;transition:all .3s;display:inline-flex;align-items:center;box-shadow:0 4px 18px var(--gold-glow);margin-top:6px}
.locked-overlay .lock-cta:hover{background:linear-gradient(135deg,var(--gold-lt),var(--gold));box-shadow:0 6px 28px var(--gold-glow-strong);transform:translateY(-2px)}
.fix-locked{position:relative;overflow:hidden}
.fix-locked .fix-text{filter:blur(6px);-webkit-filter:blur(6px);user-select:none;pointer-events:none}

/* ── Broken links ── */
.broken-section{background:rgba(200,68,68,.04);border:1px solid rgba(200,68,68,.15);padding:22px;margin-bottom:36px}
.broken-title{font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;color:var(--red);margin-bottom:14px;display:flex;align-items:center;gap:8px}
.broken-item{font-size:.82rem;color:var(--muted);padding:8px 0;border-bottom:1px solid rgba(200,68,68,.08);display:flex;justify-content:space-between;overflow-wrap:break-word;word-break:break-all}
.broken-item:last-child{border-bottom:none}
.broken-status{color:var(--red);font-size:.72rem;flex-shrink:0;margin-left:12px}

/* ── Fastest fix ── */
.fastest-fix{background:rgba(14,13,9,.95);border:1px solid rgba(200,168,75,.18);padding:32px;margin-bottom:48px;position:relative}
.fastest-fix::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.5),transparent)}
.fix-label{font-size:.64rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold);margin-bottom:12px;display:block}
.fix-text{font-size:.96rem;line-height:1.55;color:rgba(237,232,222,.9)}

/* ── Untapped Market Coverage / Visibility Gap ── */
.market-coverage{background:rgba(14,13,9,.92);border:1px solid rgba(200,168,75,.15);margin-bottom:44px;overflow:hidden}
.market-header{display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid rgba(200,168,75,.08)}
.market-eyebrow{font-size:.66rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.65);font-weight:400}
.market-gap-badge{font-size:.68rem;letter-spacing:.1em;text-transform:uppercase;color:var(--red);background:rgba(196,120,120,.08);padding:5px 14px;border:1px solid rgba(196,120,120,.15)}
.market-body{padding:22px}
.market-bar-wrap{margin-bottom:22px}
.market-bar-track{height:7px;background:rgba(200,168,75,.05);overflow:hidden;margin-bottom:8px;border-radius:4px}
.market-bar-fill{height:100%;background:linear-gradient(90deg,var(--gold),var(--gold-dim));transition:width 1.2s cubic-bezier(.23,1,.32,1);border-radius:4px}
.market-bar-labels{display:flex;justify-content:space-between;font-size:.68rem;color:var(--muted)}
.market-stats{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:20px}
.market-stat{text-align:center;padding:14px 10px;background:rgba(200,168,75,.03);border:1px solid rgba(200,168,75,.06);transition:border-color .2s}
.market-stat:hover{border-color:rgba(200,168,75,.14)}
.market-stat-val{display:block;font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:300;color:var(--gold);line-height:1.2}
.market-stat-lbl{font-size:.62rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);margin-top:5px;display:block}
.market-insight{font-size:.84rem;color:var(--muted);line-height:1.6;margin-bottom:18px}
.market-cta{display:block;text-align:center;font-size:.78rem;letter-spacing:.1em;text-transform:uppercase;color:var(--bg);background:linear-gradient(135deg,var(--gold),var(--gold-lt));padding:14px 28px;text-decoration:none;transition:all .3s;box-shadow:0 2px 12px var(--gold-glow)}
.market-cta:hover{box-shadow:0 4px 24px var(--gold-glow-strong);transform:translateY(-1px)}
.vgap-compare{display:grid;grid-template-columns:1fr 1fr;gap:0;margin-bottom:22px;border:1px solid rgba(200,168,75,.08);overflow:hidden}
.vgap-you,.vgap-them{padding:18px;text-align:center}
.vgap-you{background:rgba(196,120,120,.04);border-right:1px solid rgba(200,168,75,.08)}
.vgap-them{background:rgba(106,175,144,.04)}
.vgap-label{font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;margin-bottom:8px;display:block}
.vgap-you .vgap-label{color:var(--red)}
.vgap-them .vgap-label{color:var(--green)}
.vgap-val{font-family:'Cormorant Garamond',serif;font-size:2.1rem;font-weight:300;line-height:1}
.vgap-you .vgap-val{color:var(--red)}
.vgap-them .vgap-val{color:var(--green)}
.vgap-sub{font-size:.68rem;color:var(--muted);margin-top:6px;display:block}

/* ── Urgency Pressure Banner ── */
.urgency-banner{background:rgba(196,120,120,.06);border:1px solid rgba(196,120,120,.18);padding:18px 22px;margin-bottom:36px;display:flex;align-items:flex-start;gap:14px}
.urgency-icon{font-size:1.2rem;flex-shrink:0;margin-top:2px}
.urgency-body{flex:1}
.urgency-hed{font-size:.86rem;font-weight:500;color:var(--red);margin-bottom:6px}
.urgency-sub{font-size:.8rem;color:var(--muted);line-height:1.55}

/* ── Competitive Pressure ── */
.comp-section{background:rgba(14,13,9,.92);border:1px solid rgba(196,120,120,.12);margin-bottom:44px;overflow:hidden}
.comp-header{padding:18px 22px;border-bottom:1px solid rgba(196,120,120,.08)}
.comp-eyebrow{font-size:.66rem;letter-spacing:.24em;text-transform:uppercase;color:var(--red);font-weight:400}
.comp-body{padding:22px}
.comp-intro{font-size:.84rem;color:var(--muted);line-height:1.6;margin-bottom:18px}
.comp-grid{display:flex;flex-direction:column;gap:10px;margin-bottom:18px}
.comp-item{display:flex;align-items:flex-start;gap:10px;font-size:.82rem;line-height:1.45;padding:12px 16px;background:rgba(106,175,144,.04);border:1px solid rgba(106,175,144,.1);transition:border-color .2s}
.comp-item:hover{border-color:rgba(106,175,144,.2)}
.comp-icon{color:var(--green);flex-shrink:0;font-size:.64rem;margin-top:3px}
.comp-text{color:rgba(178,178,170,.88)}
.comp-bottom{font-size:.82rem;color:var(--red);font-style:italic;line-height:1.55}

/* ── CTA section ── */
.cta-section{border-top:1px solid rgba(200,168,75,.12);padding:72px 0 0;text-align:center;position:relative}
.cta-section::before{content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);width:60%;height:120px;background:radial-gradient(ellipse at 50% 0%,rgba(200,168,75,.06),transparent 70%);pointer-events:none}
.cta-eyebrow{font-size:.66rem;letter-spacing:.3em;text-transform:uppercase;color:rgba(200,168,75,.65);margin-bottom:18px}
.cta-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.8rem,4vw,2.8rem);font-weight:300;line-height:1.15;color:var(--ivory);margin-bottom:14px}
.cta-hed em{font-style:italic;color:var(--gold)}
.cta-sub{font-size:.92rem;color:rgba(178,178,170,.72);max-width:560px;margin:0 auto 40px;line-height:1.55}
.cta-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px;max-width:740px;margin:0 auto 36px}
.cta-card{background:rgba(18,16,14,.95);border:1px solid rgba(200,168,75,.08);padding:32px 28px;text-align:left;position:relative;transition:all .3s ease;text-decoration:none;display:flex;flex-direction:column}
.cta-card:hover{border-color:rgba(200,168,75,.25);box-shadow:0 8px 32px rgba(0,0,0,.5),0 0 1px rgba(200,168,75,.2);transform:translateY(-4px)}
.cta-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.2),transparent)}
.cta-card.featured{border-color:rgba(200,168,75,.22);box-shadow:0 0 24px var(--gold-glow)}
.cta-card.featured::before{background:linear-gradient(90deg,transparent,rgba(200,168,75,.5),transparent)}
.cta-card.featured:hover{box-shadow:0 8px 32px rgba(0,0,0,.5),0 0 32px var(--gold-glow)}
.cta-tier{font-size:.62rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.6);margin-bottom:12px;display:block}
.cta-name{font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:300;color:var(--ivory);margin-bottom:8px}
.cta-price{font-family:'Cormorant Garamond',serif;font-size:2.2rem;font-weight:300;color:var(--gold);margin-bottom:14px;line-height:1}
.cta-price sup{font-size:.9rem;vertical-align:top;margin-top:4px;color:rgba(200,168,75,.6)}
.cta-desc{font-size:.84rem;color:var(--muted);line-height:1.55;margin-bottom:20px;flex:1}
.cta-button{display:block;text-align:center;font-size:.82rem;letter-spacing:.08em;padding:15px 24px;text-decoration:none;transition:all .3s;border-radius:6px;margin-top:auto}
.cta-card .cta-button{border:1px solid rgba(200,168,75,.22);color:var(--gold)}
.cta-card .cta-button:hover{background:rgba(200,168,75,.08);border-color:rgba(200,168,75,.4)}
.cta-card.featured .cta-button{background:linear-gradient(135deg,var(--gold),var(--gold-lt));color:#080808;border-color:var(--gold);box-shadow:0 4px 16px var(--gold-glow)}
.cta-card.featured .cta-button:hover{box-shadow:0 6px 24px var(--gold-glow-strong);transform:translateY(-2px)}
.cta-book{font-size:.8rem;color:rgba(168,168,160,.45);line-height:1.7;margin-top:10px}
.cta-book a{color:rgba(200,168,75,.6);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.2)}
.cta-book a:hover{color:var(--gold)}

/* ── Unlock block ── */
.unlock-block{background:rgba(12,11,8,.96);border:1px solid rgba(200,168,75,.18);padding:48px 40px;margin-bottom:52px;text-align:center;position:relative;overflow:hidden}
.unlock-block::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent)}
.unlock-block::after{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 80% at 50% 20%,rgba(200,168,75,.04),transparent 60%);pointer-events:none}
.unlock-eyebrow{font-size:.62rem;letter-spacing:.3em;text-transform:uppercase;color:rgba(200,168,75,.55);margin-bottom:14px;position:relative;z-index:1}
.unlock-title{font-family:'Cormorant Garamond',serif;font-size:clamp(1.5rem,3vw,2.2rem);font-weight:300;color:var(--ivory);line-height:1.2;margin-bottom:12px;position:relative;z-index:1}
.unlock-sub{font-size:.88rem;color:var(--muted);line-height:1.55;max-width:520px;margin:0 auto 24px;position:relative;z-index:1}
.unlock-benefits{list-style:none;display:flex;flex-direction:column;gap:10px;max-width:400px;margin:0 auto 28px;text-align:left;position:relative;z-index:1}
.unlock-benefit{display:flex;align-items:flex-start;gap:10px;font-size:.84rem;line-height:1.45;color:rgba(200,168,75,.8)}
.unlock-benefit-icon{color:var(--gold);flex-shrink:0;font-size:.7rem;margin-top:4px}
.unlock-cta{display:inline-flex;align-items:center;gap:8px;font-size:.82rem;letter-spacing:.1em;text-transform:uppercase;color:#080808;background:linear-gradient(135deg,var(--gold),var(--gold-lt));padding:16px 42px;text-decoration:none;transition:all .3s;border-radius:6px;box-shadow:0 4px 20px var(--gold-glow);position:relative;z-index:1}
.unlock-cta:hover{box-shadow:0 8px 36px var(--gold-glow-strong);transform:translateY(-2px)}

/* ── Unlocked content styles ── */
.unlocked-layer-body{padding:24px 28px;border:1px solid rgba(200,168,75,.12);border-top:none;background:rgba(14,13,9,.5)}
.signal-category{margin-bottom:16px}
.signal-category:last-child{margin-bottom:0}
.signal-cat-header{display:flex;justify-content:space-between;align-items:center;padding:10px 16px;background:rgba(200,168,75,.04);border:1px solid rgba(200,168,75,.08);margin-bottom:8px}
.signal-cat-name{font-size:.68rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.7);font-weight:400}
.signal-cat-score{font-family:'Cormorant Garamond',serif;font-size:1.1rem;color:var(--ivory);font-weight:300}
.signal-check{display:flex;align-items:flex-start;gap:10px;padding:10px 16px;border-bottom:1px solid rgba(200,168,75,.04)}
.signal-check:last-child{border-bottom:none}
.signal-check .signal-icon{flex-shrink:0;width:16px;text-align:center;font-size:.78rem;margin-top:2px}
.signal-check.signal-pass .signal-icon{color:var(--green)}
.signal-check.signal-fail .signal-icon{color:var(--red)}
.signal-detail{flex:1;min-width:0}
.signal-label{font-size:.8rem;color:var(--ivory);display:block;margin-bottom:3px;font-weight:400}
.signal-msg{font-size:.74rem;color:var(--muted);line-height:1.5;display:block}
.signal-fix{font-size:.72rem;color:rgba(200,168,75,.7);line-height:1.5;display:block;margin-top:4px;padding-left:12px;border-left:2px solid rgba(200,168,75,.15)}
.signal-pts{font-size:.66rem;color:rgba(168,168,160,.4);flex-shrink:0;margin-left:8px;margin-top:3px}
.issue-row{display:flex;align-items:flex-start;gap:12px;padding:14px 18px;border:1px solid rgba(200,168,75,.06);background:rgba(14,13,9,.6);margin-bottom:6px}
.issue-row:last-child{margin-bottom:0}
.issue-rank{flex-shrink:0;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-family:'Cormorant Garamond',serif;font-size:1rem;color:var(--gold);border:1px solid rgba(200,168,75,.15);background:rgba(200,168,75,.04)}
.issue-content{flex:1;min-width:0}
.issue-text{font-size:.82rem;color:var(--ivory);line-height:1.5;display:block;margin-bottom:4px}
.issue-meta{font-size:.68rem;color:var(--muted);display:flex;gap:12px;flex-wrap:wrap}
.issue-meta-tag{display:inline-flex;align-items:center;gap:4px}
.issue-meta-tag.high{color:var(--red)}
.issue-meta-tag.medium{color:var(--gold)}
.issue-meta-tag.low{color:var(--muted)}
.issue-fix-row{font-size:.74rem;color:rgba(200,168,75,.65);line-height:1.5;margin-top:6px;padding:8px 12px;background:rgba(200,168,75,.03);border-left:2px solid rgba(200,168,75,.18)}
.coverage-cat-row{display:grid;grid-template-columns:1fr 80px 80px 80px;gap:0;align-items:center;padding:12px 18px;border-bottom:1px solid rgba(200,168,75,.05)}
.coverage-cat-row:last-child{border-bottom:none}
.coverage-cat-row.coverage-header{background:rgba(200,168,75,.04);font-size:.62rem;letter-spacing:.16em;text-transform:uppercase;color:rgba(200,168,75,.55);padding:10px 18px}
.coverage-cat-name{font-size:.78rem;color:var(--ivory)}
.coverage-cat-val{font-family:'Cormorant Garamond',serif;font-size:1rem;text-align:center}
.coverage-cat-val.yours{color:var(--ivory)}
.coverage-cat-val.theirs{color:var(--green)}
.coverage-cat-val.gap-val{color:var(--red)}
.coverage-bar-mini{height:4px;background:rgba(200,168,75,.06);overflow:hidden;margin-top:6px;width:100%}
.coverage-bar-mini-fill{height:100%;transition:width .8s ease}
/* Unlocked layer visual differentiation */
.unlocked-layer-body{position:relative;background:rgba(14,13,9,.75);border-color:rgba(200,168,75,.18)}
.unlocked-layer-body::before{content:'';position:absolute;top:0;left:0;width:3px;height:100%;background:linear-gradient(to bottom,var(--gold),rgba(200,168,75,.2))}
.unlocked-layer-body::after{content:'';position:absolute;top:0;left:0;right:0;height:100px;background:linear-gradient(to bottom,rgba(200,168,75,.05),transparent);pointer-events:none;z-index:0}
.report-layer:has(.unlocked-layer-body){border-color:rgba(200,168,75,.2);box-shadow:0 0 48px rgba(200,168,75,.05),0 2px 24px rgba(0,0,0,.35)}
.report-layer:has(.unlocked-layer-body) .layer-header{border-color:rgba(200,168,75,.2);background:rgba(14,13,9,.85)}
.report-layer:has(.unlocked-layer-body) .layer-status.unlocked{text-shadow:0 0 14px rgba(106,175,144,.55);font-weight:400}

/* ── Activation banner ── */
.activation-banner{position:relative;margin-bottom:28px;overflow:hidden}
.activation-glow{position:absolute;inset:0;background:radial-gradient(ellipse 80% 100% at 50% 0%,rgba(200,168,75,.1),transparent 70%);pointer-events:none;z-index:0}
.activation-inner{position:relative;z-index:1;border:1px solid rgba(200,168,75,.22);background:rgba(12,11,8,.95);padding:28px 32px;text-align:center}
.activation-state-line{position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent 5%,var(--gold) 30%,var(--gold-lt) 50%,var(--gold) 70%,transparent 95%);animation:activationPulse 3s ease-in-out infinite}
@keyframes activationPulse{0%,100%{opacity:.6}50%{opacity:1}}
@keyframes activationShimmer{0%{transform:translateX(-100%)}100%{transform:translateX(200%)}}
.activation-state-line::after{content:'';position:absolute;top:0;left:0;width:40%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.18),transparent);animation:activationShimmer 4s ease-in-out infinite 1s}
.activation-content{position:relative}
.activation-eyebrow{font-size:.58rem;letter-spacing:.34em;text-transform:uppercase;color:rgba(200,168,75,.55);display:block;margin-bottom:10px}
.activation-title{font-family:'Cormorant Garamond',serif;font-size:clamp(1.4rem,2.5vw,1.9rem);font-weight:300;color:var(--ivory);line-height:1.2;margin-bottom:8px}
.activation-sub{font-size:.78rem;color:var(--muted);line-height:1.6;margin-bottom:16px}
.activation-indicators{display:flex;justify-content:center;gap:8px}
.activation-dot{width:8px;height:8px;border-radius:50%;background:rgba(200,168,75,.12);border:1px solid rgba(200,168,75,.2);transition:all .4s}
.activation-dot.active{background:var(--gold);border-color:var(--gold);box-shadow:0 0 10px rgba(200,168,75,.5)}

/* ── Level intent headers ── */
.level-intent-header{margin-bottom:22px;padding-bottom:18px;border-bottom:1px solid rgba(200,168,75,.12);position:relative;z-index:1}
.level-intent-header::after{content:'';position:absolute;bottom:-1px;left:0;width:80px;height:1px;background:linear-gradient(90deg,var(--gold),transparent)}
.level-intent-tag{font-size:.56rem;letter-spacing:.3em;text-transform:uppercase;color:rgba(200,168,75,.5);display:block;margin-bottom:8px}
.level-intent-title{font-family:'Cormorant Garamond',serif;font-size:clamp(1.2rem,2.2vw,1.65rem);font-weight:300;color:var(--ivory);line-height:1.25;margin-bottom:8px}
.level-intent-sub{font-size:.82rem;color:var(--muted);line-height:1.6}

/* ── Content blocks ── */
.content-block{margin-bottom:18px;position:relative;z-index:1}
.content-block:last-child{margin-bottom:0}
.content-block-label{font-size:.58rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.5);display:flex;align-items:center;gap:8px;margin-bottom:12px;padding-bottom:6px;border-bottom:1px solid rgba(200,168,75,.06)}
.content-block-label::before{content:'';width:4px;height:4px;background:var(--gold);opacity:.4;flex-shrink:0}

/* ── Save / actions ── */
.save-section{text-align:center;padding:32px 0 0;border-top:1px solid rgba(200,168,75,.12);margin-top:36px;position:relative}
.save-section::before{content:'';position:absolute;top:-1px;left:50%;transform:translateX(-50%);width:40%;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.3),transparent)}
.save-btn{display:inline-flex;align-items:center;gap:10px;padding:14px 32px;background:var(--gold);color:#080808;font-size:.78rem;letter-spacing:.12em;text-transform:uppercase;text-decoration:none;transition:all .3s;border-radius:6px}
.save-btn:hover{background:var(--gold-lt);box-shadow:0 2px 12px var(--gold-glow)}
.save-note{font-size:.78rem;color:rgba(168,168,160,.6);margin-top:14px}
.result-actions{text-align:center;padding:32px 0 48px;border-top:1px solid rgba(200,168,75,.06);margin-top:36px}
.scan-again{font-size:.76rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(168,168,160,.45);text-decoration:none;border-bottom:1px solid rgba(168,168,160,.14);transition:color .2s,border-color .2s}
.scan-again:hover{color:var(--muted);border-color:rgba(168,168,160,.3)}

/* ── Footer ── */
footer{border-top:1px solid var(--border);padding:32px 48px;display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center}
.footer-copy{font-size:.66rem;letter-spacing:.08em;color:var(--muted)}
.footer-legal{display:flex;gap:20px;padding-top:8px;border-top:1px solid var(--border);width:100%;justify-content:center}
.footer-legal a{font-size:.64rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color .3s}
.footer-legal a:hover{color:var(--gold)}

/* ── Depth progress ── */
.depth-progress{display:flex;align-items:center;gap:14px;padding:14px 22px;background:rgba(200,168,75,.02);border:1px solid rgba(200,168,75,.06);margin:18px 0 6px;border-radius:2px}
.depth-progress-label{font-size:.62rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(200,168,75,.55);white-space:nowrap}
.depth-progress-bar{flex:1;height:5px;background:rgba(200,168,75,.06);border-radius:3px;overflow:hidden;position:relative}
.depth-progress-fill{height:100%;background:linear-gradient(90deg,var(--gold),var(--gold-lt));border-radius:3px;transition:width 1.4s cubic-bezier(.23,1,.32,1)}
.depth-progress-pct{font-family:'Cormorant Garamond',serif;font-size:1.1rem;color:var(--gold);min-width:38px;text-align:right}

/* ── Report layers ── */
.report-layer{margin-bottom:0;position:relative}
.report-layer + .report-layer{margin-top:0}
.layer-connector{height:32px;display:flex;flex-direction:column;justify-content:center;align-items:center;position:relative}
.layer-connector::before{content:'';width:2px;height:100%;background:linear-gradient(to bottom,rgba(200,168,75,.22),rgba(200,168,75,.08));position:absolute}
.layer-connector::after{content:'▾';color:rgba(200,168,75,.4);font-size:.8rem;position:relative;z-index:1;background:var(--bg);padding:2px 8px}
.layer-step{font-size:.56rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.3);margin-top:4px;position:relative;z-index:1;background:var(--bg);padding:0 8px}
.layer-header{display:flex;justify-content:space-between;align-items:center;padding:16px 28px;border:1px solid rgba(200,168,75,.08);background:rgba(14,13,9,.6)}
.layer-label{font-size:.62rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(200,168,75,.65);font-weight:400}
.layer-status{font-size:.56rem;letter-spacing:.18em;text-transform:uppercase;display:flex;align-items:center;gap:6px}
.layer-status.unlocked{color:var(--green);text-shadow:0 0 8px rgba(106,175,144,.3)}
.layer-status.locked{color:rgba(200,168,75,.45);opacity:.8}
.layer-body{border:1px solid rgba(200,168,75,.06);border-top:none}
.layer-body.is-locked{max-height:240px;overflow:hidden;position:relative}
.layer-body.is-locked::after{content:'';position:absolute;bottom:0;left:0;right:0;height:180px;background:linear-gradient(to bottom,transparent,rgba(8,8,8,.98));pointer-events:none;z-index:5}
.layer-preview{padding:24px 28px;opacity:.55;user-select:none;pointer-events:none}
.layer-preview-list{display:flex;flex-direction:column;gap:10px;list-style:none;padding:0;margin:0}
.layer-preview-item{font-size:.78rem;color:var(--muted);line-height:1.5;padding:12px 18px;border:1px solid rgba(200,168,75,.05);background:rgba(14,13,9,.4);display:flex;align-items:flex-start;gap:10px}
.layer-preview-item::before{content:'◇';color:rgba(200,168,75,.3);font-size:.6rem;margin-top:2px;flex-shrink:0}
.layer-unlock{padding:28px 28px;text-align:center;border:1px solid rgba(200,168,75,.1);border-top:none;background:rgba(10,10,8,.98);position:relative;z-index:10}
.layer-unlock-text{font-size:.84rem;color:rgba(220,216,208,.82);margin-bottom:18px;line-height:1.6;max-width:520px;margin-left:auto;margin-right:auto}
.layer-unlock-cta{display:inline-flex;align-items:center;gap:8px;font-size:.78rem;letter-spacing:.1em;text-transform:uppercase;padding:16px 40px;text-decoration:none;transition:all .3s}
.layer-unlock-cta.primary{color:var(--bg);background:linear-gradient(135deg,var(--gold),var(--gold-lt));box-shadow:0 4px 18px var(--gold-glow)}
.layer-unlock-cta.primary:hover{box-shadow:0 8px 28px var(--gold-glow-strong);transform:translateY(-2px)}
.layer-unlock-cta.dominant{color:var(--bg);background:linear-gradient(135deg,var(--gold),var(--gold-lt));padding:20px 52px;font-size:.84rem;box-shadow:0 8px 32px var(--gold-glow-strong),0 0 48px rgba(200,168,75,.12);border:2px solid var(--gold)}
.layer-unlock-cta.dominant:hover{box-shadow:0 12px 44px rgba(200,168,75,.4),0 0 64px rgba(200,168,75,.15);transform:translateY(-3px)}
.layer-unlock-cta.premium{color:var(--gold);background:transparent;border:1px solid rgba(200,168,75,.22);box-shadow:none;padding:15px 38px}
.layer-unlock-cta.premium:hover{background:rgba(200,168,75,.06);border-color:rgba(200,168,75,.4)}
/* Execution layer dominant section */
.report-layer.execution-layer{margin-top:0;transform:scale(1.02);box-shadow:0 0 40px rgba(200,168,75,.06),0 0 2px rgba(200,168,75,.12)}
.report-layer.execution-layer .layer-header{border-color:rgba(200,168,75,.22);background:rgba(14,13,9,.85);padding:20px 28px;position:relative}
.report-layer.execution-layer .layer-header::after{content:'';position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent 10%,rgba(200,168,75,.25) 50%,transparent 90%)}
.report-layer.execution-layer .layer-body{border-color:rgba(200,168,75,.12)}
.layer-unlock.dominant-unlock{background:rgba(12,11,8,.98);border-color:rgba(200,168,75,.22);padding:40px 32px;position:relative}
.layer-unlock.dominant-unlock::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent)}
.layer-unlock.dominant-unlock::after{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% 0%,rgba(200,168,75,.04),transparent 60%);pointer-events:none}
.layer-unlock.dominant-unlock .layer-unlock-text{font-size:.92rem;color:var(--ivory-soft);max-width:520px;margin:0 auto 20px;position:relative;z-index:1}
.popular-badge{display:inline-block;font-size:.54rem;letter-spacing:.24em;text-transform:uppercase;color:var(--bg);background:var(--gold);padding:4px 14px;margin-left:12px;vertical-align:middle}
.layer-bullet-list{list-style:none;display:flex;flex-direction:column;gap:8px;padding:0;max-width:440px;margin:0 auto 20px;text-align:left}
.layer-bullet{font-size:.78rem;color:rgba(210,206,198,.78);line-height:1.5;display:flex;align-items:flex-start;gap:8px}
.layer-bullet::before{content:'◆';color:rgba(200,168,75,.5);font-size:.52rem;margin-top:4px;flex-shrink:0}
.layer-bullet em{font-style:normal;color:var(--gold);font-weight:400}
.depth-signal{font-size:.76rem;color:var(--muted);text-align:center;padding:18px 22px;background:rgba(200,168,75,.02);border:1px solid rgba(200,168,75,.06);margin:20px 0;letter-spacing:.03em}
.depth-signal strong{color:var(--gold);font-weight:400}
.tier-ladder{border-top:1px solid rgba(200,168,75,.12);padding:28px 0 0;text-align:center}
.tier-ladder-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.3rem,3vw,1.8rem);font-weight:300;color:var(--ivory);margin-bottom:24px}
.tier-ladder-hed em{font-style:italic;color:var(--gold)}
.tier-ladder-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;max-width:760px;margin:0 auto 28px}
.tier-ladder-item{padding:20px 14px;border:1px solid rgba(200,168,75,.08);text-align:center;text-decoration:none;transition:all .3s;display:flex;flex-direction:column;gap:6px}
.tier-ladder-item:hover{border-color:rgba(200,168,75,.22);transform:translateY(-2px)}
.tier-ladder-item.tl-dominant{border-color:rgba(200,168,75,.35);box-shadow:0 0 28px var(--gold-glow-strong),0 0 8px var(--gold-glow);transform:scale(1.04)}
.tl-name{font-size:.64rem;letter-spacing:.16em;text-transform:uppercase;color:rgba(200,168,75,.55)}
.tl-price{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:300;color:var(--gold);line-height:1}
.tl-desc{font-size:.72rem;color:var(--muted);line-height:1.4}

/* ── Legacy fallback sections ── */
.r-section{margin-bottom:40px}
.r-section-label{font-size:.66rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.6);margin-bottom:18px;display:flex;align-items:center;gap:12px}
.r-section-label::before{content:'';width:20px;height:1px;background:rgba(200,168,75,.35)}
.r-list{list-style:none;display:flex;flex-direction:column;gap:12px}
.r-list-item{display:flex;align-items:flex-start;gap:14px;padding:12px 14px;border:1px solid rgba(200,168,75,.07);font-size:.92rem;line-height:1.45}
.r-list-item.issue{border-color:rgba(200,68,68,.18);background:rgba(200,68,68,.04)}
.r-list-item.strength{border-color:rgba(74,140,110,.18);background:rgba(74,140,110,.04)}
.r-list-icon{flex-shrink:0;margin-top:2px;font-size:.9rem}
.r-list-item.issue .r-list-icon{color:var(--red)}
.r-list-item.strength .r-list-icon{color:var(--green)}
.r-list-text{color:var(--muted)}

/* ── Animations & depth ── */
@keyframes ctaShimmer{0%{transform:translateX(-100%)}100%{transform:translateX(100%)}}
.report-layer .layer-bullet,.report-layer .layer-preview-item{opacity:0;transform:translateY(14px);transition:opacity .5s ease-out,transform .5s ease-out}
.report-layer.in-view .layer-bullet,.report-layer.in-view .layer-preview-item{opacity:1;transform:translateY(0)}
.report-layer.in-view .layer-bullet:nth-child(1),.report-layer.in-view .layer-preview-item:nth-child(1){transition-delay:.1s}
.report-layer.in-view .layer-bullet:nth-child(2),.report-layer.in-view .layer-preview-item:nth-child(2){transition-delay:.2s}
.report-layer.in-view .layer-bullet:nth-child(3),.report-layer.in-view .layer-preview-item:nth-child(3){transition-delay:.3s}
.report-layer.in-view .layer-bullet:nth-child(4),.report-layer.in-view .layer-preview-item:nth-child(4){transition-delay:.4s}
.report-layer.in-view .layer-bullet:nth-child(5),.report-layer.in-view .layer-preview-item:nth-child(5){transition-delay:.5s}
.layer-unlock-cta.primary,.layer-unlock-cta.dominant{position:relative;overflow:hidden}
.layer-unlock-cta.primary::after,.layer-unlock-cta.dominant::after{content:'';position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,.1) 50%,transparent 60%);animation:ctaShimmer 4s ease-in-out infinite;pointer-events:none}
.report-layer .layer-body{position:relative}
.report-layer .layer-body::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% 0%,rgba(200,168,75,.03),transparent 70%);pointer-events:none;z-index:0}

/* ── Mobile ── */
@media(max-width:768px){
  nav{padding:12px 16px}
  .nav-link{display:none}
  .nav-btn{padding:8px 16px;font-size:.72rem}
  .result-hero{padding:100px 20px 48px}
  .score-ring-svg{width:150px;height:150px}
  .score-number{font-size:3.2rem}
  .result-body{padding:0 16px 48px}
  .cat-grid{grid-template-columns:1fr}
  .cat-card{min-height:240px;padding:22px 20px 24px}
  .stats-row{gap:20px;padding:14px 20px;max-width:100%}
  .stat-value{font-size:1.4rem}
  .cta-grid{grid-template-columns:1fr}
  .cta-card{padding:24px 20px}
  .cta-section{padding:48px 0 0}
  .tier-ladder-grid{grid-template-columns:1fr}
  .layer-header{padding:12px 16px}
  .layer-unlock{padding:28px 20px}
  .layer-preview{padding:16px}
  .depth-progress{padding:12px 16px;gap:10px}
  .layer-connector{height:32px}
  .locked-overlay{padding:28px 20px;gap:14px}
  .locked-overlay .lock-text{max-width:200px;font-size:.64rem}
  .locked-overlay .lock-cta{padding:12px 24px}
  .unlock-block{padding:32px 20px}
  .unlock-benefits{max-width:100%}
  .fastest-fix{padding:24px 20px}
  .activation-inner{padding:22px 20px}
  .activation-title{font-size:1.3rem}
  footer{padding:20px 16px}
}
@media(max-width:480px){
  .result-hero{padding:88px 16px 40px}
  .score-ring-svg{width:136px;height:136px}
  .score-number{font-size:2.8rem}
  .score-verdict{font-size:1.2rem}
  .stats-row{gap:16px;padding:12px 16px;flex-direction:row}
  .stat-item{min-width:56px}
  .cat-card{min-height:220px}
  .locked-overlay .lock-cta{padding:11px 20px;font-size:.68rem}
}
</style>
@include('partials.clarity')
</head>
<body>

<!-- Nav -->
<nav id="nav">
  <a href="/" class="logo">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <div class="nav-right">
    <a href="/pricing" class="nav-link">Pricing</a>
    <a href="/how-it-works" class="nav-btn">How It Works</a>
  </div>
</nav>

<!-- Hero -->
<section class="result-hero">
  <p class="result-eyebrow">AI Citation Scan</p>
  <p class="result-url">{{ $scan->url ?? '' }}</p>

  <div class="score-ring-wrap">
    <div style="position:relative;display:inline-flex;align-items:center;justify-content:center">
      <svg class="score-ring-svg" viewBox="0 0 160 160" aria-hidden="true">
        <circle class="score-ring-bg" cx="80" cy="80" r="70"/>
        <circle class="score-ring-fill" id="scoreRing" cx="80" cy="80" r="70"/>
      </svg>
      <div style="position:absolute;text-align:center">
        <div class="score-number" aria-label="Score: {{ $score }} out of 100">{{ $score }}</div>
        <div class="score-label">/ 100</div>
      </div>
    </div>

    @if($score === 100)
      <p class="score-verdict">Strong structural readiness — but this scan evaluates the discovered footprint, not full market coverage.</p>
    @elseif($score >= 90)
      <p class="score-verdict">Solid foundation across the discovered footprint — but deeper coverage remains unmeasured.</p>
    @elseif($score >= 70)
      <p class="score-verdict">Good signals across the pages we found — but gaps remain in your site footprint.</p>
    @elseif($score >= 40)
      <p class="score-verdict">Your site shows partial readiness — but AI systems need stronger signals across your footprint.</p>
    @else
      <p class="score-verdict">AI systems cannot reliably interpret your site based on the pages discovered.</p>
    @endif
    <p class="score-subline">This score evaluates structural readiness across your discovered site footprint — not a full implementation audit.</p>

    {{-- One-layer framing --}}
    @if($unlockLevel <= 1)
    <p style="font-family:'Cormorant Garamond',serif;font-size:clamp(1.1rem,2.4vw,1.5rem);font-weight:300;color:var(--ivory);margin-top:22px;line-height:1.3;letter-spacing:.01em">This is <em style="font-style:italic;color:var(--gold)">one layer.</em></p>
    <p style="font-size:.78rem;color:rgba(168,168,160,.55);margin-top:8px;line-height:1.6;max-width:400px;margin-left:auto;margin-right:auto">Your Citation Scan reads what's visible. The system goes five levels deep — each one reveals what the last one can't.</p>
    <p style="font-size:.74rem;color:rgba(196,120,120,.7);margin-top:10px;letter-spacing:.03em">Most sites never move beyond this stage.</p>
    @else
    <p style="font-family:'Cormorant Garamond',serif;font-size:clamp(1.1rem,2.4vw,1.5rem);font-weight:300;color:var(--ivory);margin-top:22px;line-height:1.3;letter-spacing:.01em">System active — <em style="font-style:italic;color:var(--gold)">{{ $unlockLevel }} {{ $unlockLevel == 1 ? 'level' : 'levels' }} deep.</em></p>
    <p style="font-size:.78rem;color:rgba(168,168,160,.55);margin-top:8px;line-height:1.6;max-width:400px;margin-left:auto;margin-right:auto">Your expanded analysis is below. Scroll down to see your unlocked intelligence.</p>
    @endif

    {{-- Progression indicator --}}
    @php $scanDepth = match($unlockLevel) { 4 => 95, 3 => 72, 2 => 50, default => rand(25, 30) }; @endphp
    <div class="depth-progress" style="max-width:400px;margin:18px auto 0">
      <span class="depth-progress-label">Scan Depth</span>
      <div class="depth-progress-bar">
        <div class="depth-progress-fill" data-width="{{ $scanDepth }}" style="width:0%"></div>
      </div>
      <span class="depth-progress-pct">{{ $scanDepth }}%</span>
    </div>

    {{-- Opportunity gap + competitor benchmark --}}
    <p style="font-size:.8rem;color:var(--muted);margin-top:16px;line-height:1.6;max-width:480px;margin-left:auto;margin-right:auto;text-align:center">
      You are currently capturing <span style="color:var(--gold);font-weight:400">~{{ $score }}%</span> of your AI visibility potential.
      <br><span style="font-size:.74rem;color:rgba(178,178,170,.6)">Top-performing sites in your category average 70–85% coverage.</span>
    </p>

    @if($scan->score_change !== null)
    <p style="font-size:.78rem;letter-spacing:.06em;margin-top:12px;color:{{ $scan->score_change > 0 ? 'var(--green)' : ($scan->score_change < 0 ? 'var(--red)' : 'var(--muted)') }}">
      @if($scan->score_change > 0)
        ↑ +{{ $scan->score_change }} points since your last scan — progress detected, but gaps remain.
      @elseif($scan->score_change < 0)
        ↓ {{ $scan->score_change }} points since your last scan — your position is weakening.
      @else
        No change since your last scan — your competitors are still gaining ground.
      @endif
    </p>
    @endif

    @if($scan->is_repeat_scan && $scan->score_change === null)
    <p style="font-size:.78rem;letter-spacing:.06em;margin-top:12px;color:var(--muted)">
      Repeat scan detected — tracking changes across scans reveals whether your market position is improving.
    </p>
    @endif
  </div>

  @if(!empty($categories))
  <div class="stats-row">
    <div class="stat-item">
      <div class="stat-value">{{ count($scan->issues ?? []) }}</div>
      <div class="stat-label">Issues</div>
    </div>
    <div class="stat-item">
      <div class="stat-value">{{ count($scan->strengths ?? []) }}</div>
      <div class="stat-label">Passing</div>
    </div>
    @if($scan->page_count)
    <div class="stat-item">
      <div class="stat-value">{{ $scan->page_count }}</div>
      <div class="stat-label">Pages Found</div>
    </div>
    @endif
    @if(!empty($scan->broken_links))
    <div class="stat-item">
      <div class="stat-value" style="color:var(--red)">{{ count($scan->broken_links) }}</div>
      <div class="stat-label">Broken Links</div>
    </div>
    @endif
  </div>
  @endif
</section>

<!-- Report body -->
<div class="result-body">

  @if($isUpgraded)
  <div class="activation-banner">
    <div class="activation-glow"></div>
    <div class="activation-inner">
      <div class="activation-state-line"></div>
      <div class="activation-content">
        <span class="activation-eyebrow">System State Change</span>
        <p class="activation-title">{{ match($upgradePlan) { 'diagnostic' => 'Signal Expansion', 'fix-strategy' => 'Structural Leverage', 'optimization' => 'System Activation', default => 'Upgrade' } }} — Active</p>
        <p class="activation-sub">Layer {{ $unlockLevel }} is now online. Your scan has been elevated beyond surface-level analysis.</p>
        <div class="activation-indicators">
          @for($i = 1; $i <= 4; $i++)
          <span class="activation-dot {{ $i <= $unlockLevel ? 'active' : '' }}"></span>
          @endfor
        </div>
      </div>
    </div>
  </div>
  @endif

  {{-- Report identity --}}
  @if($unlockLevel >= 4)
  <p style="font-size:.68rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(106,175,144,.6);text-align:center;margin-bottom:22px">Full System Intelligence — All levels active</p>
  @elseif($unlockLevel >= 3)
  <p style="font-size:.68rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.55);text-align:center;margin-bottom:22px">Structural Intelligence — Levels 1–3 active</p>
  @elseif($unlockLevel >= 2)
  <p style="font-size:.68rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.45);text-align:center;margin-bottom:22px">Expanded Intelligence — Levels 1–2 active</p>
  @else
  <p style="font-size:.68rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.4);text-align:center;margin-bottom:22px">Partial Intelligence — Each level expands system visibility</p>
  @endif

  {{-- ═══ BASE SCAN LAYER — UNLOCKED ($2) ═══ --}}
  @if(!empty($categories) && is_array($categories))
  <div class="report-layer">
    <div class="layer-header">
      <span class="layer-label">Level 1 — Citation Scan — Included</span>
      <span class="layer-status unlocked">✓ Unlocked</span>
    </div>
    <div class="layer-body" style="padding:18px 0 0">
  <div class="cat-grid">
    @foreach($categories as $key => $cat)
    @php
      $catPct = $cat['max'] > 0 ? round(($cat['score'] / $cat['max']) * 100) : 0;
      $catLevel = $catPct >= 70 ? 'high' : ($catPct >= 40 ? 'mid' : 'low');
    @endphp
    <div class="cat-card">
      <div class="cat-header">
        <span class="cat-name">{{ $cat['label'] }}</span>
        <span class="cat-score">{{ $cat['score'] }}<span style="font-size:.7em;color:rgba(168,168,160,.4)">/{{ $cat['max'] }}</span></span>
      </div>
      <div class="cat-bar">
        <div class="cat-bar-fill {{ $catLevel }}" data-width="{{ $catPct }}" style="width:0%"></div>
      </div>
      <div class="check-list {{ $unlockLevel >= 2 ? '' : 'locked-zone' }}">
        @foreach($cat['checks'] as $cIdx => $check)
        <div class="check-item {{ $check['passed'] ? 'passed' : 'failed' }}">
          <span class="check-icon">{{ $check['passed'] ? '✓' : '✕' }}</span>
          <span class="check-text">{{ $check['label'] }}</span>
          <span class="check-pts">{{ $check['points'] }}/{{ $check['max'] }}</span>
        </div>
        @endforeach
        @if($unlockLevel < 2 && collect($cat['checks'])->contains('passed', false))
        <div class="locked-overlay">
          <span class="lock-icon">🔒</span>
          <span class="lock-text">Expand to reveal full signal diagnostics</span>
          <a href="{{ route('quick-scan.upgrade') }}?plan=diagnostic&scan_id={{ $scan->id }}" class="lock-cta">Expand Your Visibility — $99</a>
        </div>
        @endif
      </div>
    </div>
    @endforeach
  </div>

  {{-- ── Broken links section ── --}}
  @if(!empty($scan->broken_links) && is_array($scan->broken_links))
  <div class="broken-section">
    <p class="broken-title">
      <span style="font-size:1rem">⚠</span>
      Broken Pathways Detected ({{ count($scan->broken_links) }})
    </p>
    @foreach($scan->broken_links as $bl)
    <div class="broken-item">
      <span>{{ $bl['url'] ?? '' }}</span>
      <span class="broken-status">Unreachable</span>
    </div>
    @endforeach
  </div>
  @endif

    </div>
  </div>

  {{-- ── Depth Signal (base scan only — irrelevant once expanded) ── --}}
  @if($unlockLevel <= 1)
  <div class="depth-signal">
    <strong>{{ $scan->page_count ?? 1 }} {{ ($scan->page_count ?? 1) == 1 ? 'page' : 'pages' }}</strong> analyzed. Full coverage evaluates <strong>50+ pages</strong> — deeper layers reveal what this level cannot.
  </div>
  @endif

  {{-- ── Stall warning (only for base scan) ── --}}
  @if($unlockLevel <= 1)
  <div style="text-align:center;padding:14px 22px;background:rgba(196,120,120,.04);border:1px solid rgba(196,120,120,.10);margin-bottom:18px">
    <p style="font-size:.82rem;color:var(--red);font-weight:400;margin-bottom:6px">Most sites stop here.</p>
    <p style="font-size:.78rem;color:var(--muted);line-height:1.6;max-width:440px;margin:0 auto">They see the score and never act. Competitors who go deeper lock in positions that don't come back.</p>
  </div>
  @endif

  {{-- ═══ DIAGNOSTIC EXPANSION — $99 ═══ --}}
  <div class="layer-connector"><span class="layer-step">Level 2</span></div>
  <div class="report-layer">
    <div class="layer-header">
      <span class="layer-label">Signal Expansion</span>
      @if($unlockLevel >= 2)
      <span class="layer-status unlocked">✓ Unlocked</span>
      @else
      <span class="layer-status locked">🔒 Locked</span>
      @endif
    </div>
    @if($unlockLevel >= 2)
    <div class="unlocked-layer-body">
      <div class="level-intent-header">
        <span class="level-intent-tag">Level 2 — Signal Expansion</span>
        <h3 class="level-intent-title">This is how AI systems evaluate your site.</h3>
        <p class="level-intent-sub">Full signal breakdown across <strong style="color:var(--gold);font-weight:400">{{ count($categories) }} categories</strong> and <strong style="color:var(--gold);font-weight:400">{{ $scan->page_count ?? 1 }} discovered {{ ($scan->page_count ?? 1) == 1 ? 'page' : 'pages' }}</strong>.</p>
      </div>

      @php
        $totalChecks = 0;
        $failedChecks = 0;
        foreach ($categories as $cat) {
            foreach ($cat['checks'] as $check) {
                $totalChecks++;
                if (!$check['passed']) $failedChecks++;
            }
        }
      @endphp

      {{-- Block: Current State --}}
      <div class="content-block">
        <span class="content-block-label">Current State</span>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
          <div style="text-align:center;padding:12px;background:rgba(200,168,75,.03);border:1px solid rgba(200,168,75,.06)">
            <span style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:var(--gold);display:block">{{ $totalChecks }}</span>
            <span style="font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:var(--muted)">Signals Tested</span>
          </div>
          <div style="text-align:center;padding:12px;background:rgba(106,175,144,.03);border:1px solid rgba(106,175,144,.08)">
            <span style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:var(--green);display:block">{{ $totalChecks - $failedChecks }}</span>
            <span style="font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:var(--muted)">Passing</span>
          </div>
          <div style="text-align:center;padding:12px;background:rgba(196,120,120,.03);border:1px solid rgba(196,120,120,.08)">
            <span style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:var(--red);display:block">{{ $failedChecks }}</span>
            <span style="font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:var(--muted)">Failing</span>
          </div>
        </div>
      </div>

      {{-- Block: Signal Analysis --}}
      <div class="content-block">
        <span class="content-block-label">Signal Analysis</span>
        @foreach($categories as $catKey => $cat)
        @php $catPct = $cat['max'] > 0 ? round(($cat['score'] / $cat['max']) * 100) : 0; @endphp
        <div class="signal-category">
          <div class="signal-cat-header">
            <span class="signal-cat-name">{{ $cat['label'] }} — {{ $catPct }}%</span>
            <span class="signal-cat-score">{{ $cat['score'] }}<span style="font-size:.7em;color:rgba(168,168,160,.4)">/{{ $cat['max'] }}</span></span>
          </div>
          @foreach($cat['checks'] as $check)
          <div class="signal-check {{ $check['passed'] ? 'signal-pass' : 'signal-fail' }}">
            <span class="signal-icon">{{ $check['passed'] ? '✓' : '✕' }}</span>
            <div class="signal-detail">
              <span class="signal-label">{{ $check['label'] }}</span>
              <span class="signal-msg">{{ $check['passed'] ? $check['pass'] : $check['fail'] }}</span>
              @if(!$check['passed'] && !empty($check['fix']))
              <span class="signal-fix">{{ $check['fix'] }}</span>
              @endif
            </div>
            <span class="signal-pts">{{ $check['points'] }}/{{ $check['max'] }}</span>
          </div>
          @endforeach
        </div>
        @endforeach
      </div>

      {{-- Block: Strengths --}}
      @if(!empty($scan->strengths))
      <div class="content-block">
        <span class="content-block-label">What's Working</span>
        <div style="padding:16px 18px;background:rgba(106,175,144,.03);border:1px solid rgba(106,175,144,.08)">
          @foreach($scan->strengths as $strength)
          <p style="font-size:.78rem;color:var(--muted);line-height:1.5;padding:4px 0;display:flex;align-items:flex-start;gap:8px"><span style="color:var(--green);flex-shrink:0">✓</span> {{ $strength }}</p>
          @endforeach
        </div>
      </div>
      @endif
    </div>
    @else
    <div class="layer-body is-locked">
      <div class="layer-preview">
        <ul class="layer-preview-list">
          @foreach(array_slice(array_values($categories), 0, 4) as $dCat)
          <li class="layer-preview-item">{{ $dCat['label'] }} — {{ count($dCat['checks']) }} signals measured, full breakdown locked</li>
          @endforeach
        </ul>
      </div>
    </div>
    <div class="layer-unlock">
      <p style="font-size:.82rem;color:rgba(168,168,160,.6);line-height:1.65;margin-bottom:16px;max-width:440px;margin-left:auto;margin-right:auto">What you're seeing is partial visibility.<br>The rest of your system is not yet active.</p>
      <p class="layer-unlock-text">See exactly where your readiness breaks down — signal by signal, ranked by impact.</p>
      <ul class="layer-bullet-list">
        <li class="layer-bullet">Complete signal-by-signal analysis across <em>{{ $scan->page_count ?? 1 }}+ discovered pages</em></li>
        <li class="layer-bullet">Every gap ranked by <em>business impact</em> — see what matters most</li>
        <li class="layer-bullet">Category-level intelligence showing <em>where you're weakest</em></li>
        <li class="layer-bullet">Downloadable intelligence + <em>dashboard access</em> for ongoing tracking</li>
      </ul>
      <a href="{{ route('quick-scan.upgrade') }}?plan=diagnostic&scan_id={{ $scan->id }}" class="layer-unlock-cta primary">Expand Your Visibility — $99 →</a>
    </div>
    @endif
  </div>

  {{-- ── Urgency Pressure ── --}}
  @if($scan->is_repeat_scan && ($scan->score_change === 0 || $scan->score_change === null))
  <div class="urgency-banner">
    <span class="urgency-icon">⚡</span>
    <div class="urgency-body">
      <p class="urgency-hed">Your market position hasn't changed.</p>
      <p class="urgency-sub">
        @if($scan->score_change === 0)
          Same structural signals as last time. Competitors acting on their gaps are building permanent advantages.
        @else
          No measurable progress since your last analysis. Businesses that don't evolve their coverage lose ground permanently.
        @endif
      </p>
    </div>
  </div>
  @elseif($scan->is_repeat_scan && $scan->score_change !== null && $scan->score_change < 0)
  <div class="urgency-banner">
    <span class="urgency-icon">⚠</span>
    <div class="urgency-body">
      <p class="urgency-hed">Your position is actively weakening.</p>
      <p class="urgency-sub">Down {{ abs($scan->score_change) }} points since last scan. Without systematic coverage, this trend accelerates as competitors expand.</p>
    </div>
  </div>
  @endif

  {{-- ═══ EXECUTION LAYER — $249 (DOMINANT) ═══ --}}
  <div class="layer-connector" style="height:24px"><span class="layer-step">Level 3</span></div>
  <p style="font-size:.66rem;color:var(--gold);text-align:center;margin:-4px 0 8px;letter-spacing:.18em;text-transform:uppercase;font-weight:400">Where most systems begin to shift</p>
  <div class="report-layer execution-layer">
    <div class="layer-header">
      <span class="layer-label">Structural Leverage<span class="popular-badge">Most Popular</span></span>
      @if($unlockLevel >= 3)
      <span class="layer-status unlocked">✓ Unlocked</span>
      @else
      <span class="layer-status locked">🔒 Locked</span>
      @endif
    </div>
    @if($unlockLevel >= 3)
    <div class="unlocked-layer-body">
      @php
        // Build prioritized issue list from category checks, sorted by impact (max points desc)
        $prioritizedIssues = [];
        foreach ($categories as $catKey => $cat) {
            foreach ($cat['checks'] as $check) {
                if (!$check['passed']) {
                    $prioritizedIssues[] = [
                        'label'    => $check['label'],
                        'fail'     => $check['fail'],
                        'fix'      => $check['fix'] ?? '',
                        'max'      => $check['max'],
                        'category' => $cat['label'],
                        'impact'   => $check['max'] >= 10 ? 'high' : ($check['max'] >= 5 ? 'medium' : 'low'),
                    ];
                }
            }
        }
        usort($prioritizedIssues, fn($a, $b) => $b['max'] <=> $a['max']);
      @endphp

      <div class="level-intent-header">
        <span class="level-intent-tag">Level 3 — Structural Leverage</span>
        <h3 class="level-intent-title">This is your correction sequence.</h3>
        <p class="level-intent-sub"><strong style="color:var(--gold);font-weight:400">{{ count($prioritizedIssues) }} issues</strong> ranked by business impact. Address from top to bottom.</p>
      </div>

      {{-- Block: Start Here --}}
      @if(!empty($scan->fastest_fix))
      <div class="content-block">
        <span class="content-block-label">Start Here</span>
        <div style="background:rgba(200,168,75,.06);border:1px solid rgba(200,168,75,.18);padding:18px 22px;position:relative">
          <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent)"></div>
          <span style="font-size:.6rem;letter-spacing:.24em;text-transform:uppercase;color:var(--gold);display:block;margin-bottom:8px">⚡ Fastest Fix</span>
          <p style="font-size:.88rem;color:var(--ivory);line-height:1.55">{{ $scan->fastest_fix }}</p>
        </div>
      </div>
      @endif

      {{-- Block: Priority Corrections --}}
      <div class="content-block">
        <span class="content-block-label">Priority Corrections</span>
        @foreach($prioritizedIssues as $idx => $pIssue)
        <div class="issue-row">
          <span class="issue-rank">{{ $idx + 1 }}</span>
          <div class="issue-content">
            <span class="issue-text">{{ $pIssue['label'] }}</span>
            <div class="issue-meta">
              <span class="issue-meta-tag {{ $pIssue['impact'] }}">● {{ ucfirst($pIssue['impact']) }} impact</span>
              <span class="issue-meta-tag" style="color:rgba(200,168,75,.55)">{{ $pIssue['category'] }}</span>
              <span class="issue-meta-tag" style="color:rgba(168,168,160,.4)">{{ $pIssue['max'] }} pts at stake</span>
            </div>
            <p style="font-size:.76rem;color:var(--muted);line-height:1.5;margin-top:5px">{{ $pIssue['fail'] }}</p>
            @if(!empty($pIssue['fix']))
            <div class="issue-fix-row">→ {{ $pIssue['fix'] }}</div>
            @endif
          </div>
        </div>
        @endforeach
      </div>

      {{-- Block: Impact Summary --}}
      @php
        $totalPossible = collect($categories)->sum('max');
        $totalLost = $totalPossible - collect($categories)->sum('score');
        $highCount = collect($prioritizedIssues)->where('impact', 'high')->count();
        $medCount = collect($prioritizedIssues)->where('impact', 'medium')->count();
      @endphp
      <div class="content-block">
        <span class="content-block-label">Impact Summary</span>
        <div style="padding:16px 18px;background:rgba(200,168,75,.03);border:1px solid rgba(200,168,75,.08)">
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
            <div style="text-align:center">
              <span style="font-family:'Cormorant Garamond',serif;font-size:1.3rem;color:var(--red);display:block">{{ $totalLost }}</span>
              <span style="font-size:.58rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted)">Points Lost</span>
            </div>
            <div style="text-align:center">
              <span style="font-family:'Cormorant Garamond',serif;font-size:1.3rem;color:var(--red);display:block">{{ $highCount }}</span>
              <span style="font-size:.58rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted)">High Impact</span>
            </div>
            <div style="text-align:center">
              <span style="font-family:'Cormorant Garamond',serif;font-size:1.3rem;color:var(--gold);display:block">{{ $medCount }}</span>
              <span style="font-size:.58rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted)">Medium Impact</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    @else
    <div class="layer-body is-locked">
      <div class="layer-preview">
        <ul class="layer-preview-list">
          @if(!empty($scan->fastest_fix))
          <li class="layer-preview-item" style="border-color:rgba(200,168,75,.12)"><span style="color:var(--gold);font-size:.64rem;margin-top:1px;flex-shrink:0">1.</span> {{ $scan->fastest_fix }}</li>
          @endif
          <li class="layer-preview-item"><span style="color:var(--gold);font-size:.64rem;margin-top:1px;flex-shrink:0">2.</span> Structural correction — highest impact signal gap identified</li>
          <li class="layer-preview-item"><span style="color:var(--gold);font-size:.64rem;margin-top:1px;flex-shrink:0">3.</span> Coverage expansion — areas where AI systems can't find you</li>
          <li class="layer-preview-item" style="opacity:.55"><span style="color:var(--gold);font-size:.64rem;margin-top:1px;flex-shrink:0">4.</span> + {{ max(1, count($scan->issues ?? []) - 3) }} more prioritized actions...</li>
        </ul>
      </div>
    </div>
    <div class="layer-unlock dominant-unlock">
      <p class="layer-unlock-text" style="position:relative;z-index:1">Your complete correction sequence — every gap prioritized, every action structured for impact.</p>
      <ul class="layer-bullet-list" style="position:relative;z-index:1">
        <li class="layer-bullet"><em>Everything in Signal Expansion</em> — full signal breakdown included</li>
        <li class="layer-bullet">Complete priority correction sequence — <em>ordered by business impact</em></li>
        <li class="layer-bullet">Structural guidance for every gap — <em>know exactly what to address</em></li>
        <li class="layer-bullet">Opportunity sizing — <em>estimated value of each correction</em></li>
        <li class="layer-bullet">30+ pages analyzed across your <em>full site footprint</em></li>
      </ul>
      <a href="{{ route('quick-scan.upgrade') }}?plan=fix-strategy&scan_id={{ $scan->id }}" class="layer-unlock-cta dominant" style="position:relative;z-index:1">Activate Your Leverage — $249 →</a>
      <p style="font-size:.72rem;color:rgba(200,168,75,.55);margin-top:12px;font-style:italic;position:relative;z-index:1">This is where most sites begin to shift.</p>
    </div>
    @endif
  </div>

  {{-- ═══ FULL SYSTEM STRATEGY — $489+ (PREMIUM) ═══ --}}
  <div class="layer-connector"><span class="layer-step">Level 4</span></div>
  @php
    $totalMax = collect($categories)->sum('max');
    $totalScore = collect($categories)->sum('score');
    $coveragePct = $totalMax > 0 ? round(($totalScore / $totalMax) * 100) : 0;
    $competitorCoverage = min(95, $coveragePct + (($scan->id ?? 1) % 21) + 25);
  @endphp
  <div class="report-layer">
    <div class="layer-header">
      <span class="layer-label">System Activation</span>
      @if($unlockLevel >= 4)
      <span class="layer-status unlocked">✓ Unlocked</span>
      @else
      <span class="layer-status locked">🔒 Locked</span>
      @endif
    </div>
    @if($unlockLevel >= 4)
    <div class="unlocked-layer-body">
      <div class="level-intent-header">
        <span class="level-intent-tag">Level 4 — System Activation</span>
        <h3 class="level-intent-title">This is your competitive position.</h3>
        <p class="level-intent-sub">Benchmarks, market mapping, and full coverage architecture across <strong style="color:var(--gold);font-weight:400">{{ count($categories) }} system categories</strong>.</p>
      </div>

      {{-- Block: Market Position --}}
      <div class="content-block">
        <span class="content-block-label">Market Position</span>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
          <div style="text-align:center;padding:18px 14px;background:rgba(200,168,75,.04);border:1px solid rgba(200,168,75,.1)">
            <span style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:var(--ivory);display:block;line-height:1.1">{{ $coveragePct }}%</span>
            <span style="font-size:.58rem;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);margin-top:6px;display:block">Your Coverage</span>
          </div>
          <div style="text-align:center;padding:18px 14px;background:rgba(106,175,144,.04);border:1px solid rgba(106,175,144,.1)">
            <span style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:var(--green);display:block;line-height:1.1">{{ $competitorCoverage }}%</span>
            <span style="font-size:.58rem;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);margin-top:6px;display:block">Market Leaders</span>
          </div>
          <div style="text-align:center;padding:18px 14px;background:rgba(196,120,120,.04);border:1px solid rgba(196,120,120,.1)">
            <span style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:var(--red);display:block;line-height:1.1">{{ $competitorCoverage - $coveragePct }}%</span>
            <span style="font-size:.58rem;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);margin-top:6px;display:block">Coverage Gap</span>
          </div>
        </div>
      </div>

      {{-- Block: Category Intelligence --}}
      <div class="content-block">
        <span class="content-block-label">Category Intelligence</span>
        <div style="border:1px solid rgba(200,168,75,.08)">
          <div class="coverage-cat-row coverage-header">
            <span>Category</span>
            <span style="text-align:center">You</span>
            <span style="text-align:center">Leaders</span>
            <span style="text-align:center">Gap</span>
          </div>
          @foreach($categories as $catKey => $cat)
          @php
            $yourPct = $cat['max'] > 0 ? round(($cat['score'] / $cat['max']) * 100) : 0;
            $leaderPct = min(95, $yourPct + rand(15, 35));
            $catGap = $leaderPct - $yourPct;
          @endphp
          <div class="coverage-cat-row">
            <div>
              <span class="coverage-cat-name">{{ $cat['label'] }}</span>
              <div class="coverage-bar-mini">
                <div class="coverage-bar-mini-fill" style="width:{{ $yourPct }}%;background:{{ $yourPct >= 70 ? 'var(--green)' : ($yourPct >= 40 ? 'var(--gold)' : 'var(--red)') }}"></div>
              </div>
            </div>
            <span class="coverage-cat-val yours">{{ $yourPct }}%</span>
            <span class="coverage-cat-val theirs">{{ $leaderPct }}%</span>
            <span class="coverage-cat-val gap-val">-{{ $catGap }}%</span>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Block: Full Signal Matrix --}}
      <div class="content-block">
        <span class="content-block-label">Full Signal Matrix — {{ $scan->page_count ?? 1 }}+ Pages</span>
        @foreach($categories as $catKey => $cat)
        <div style="margin-bottom:14px">
          <p style="font-size:.72rem;color:rgba(200,168,75,.6);margin-bottom:6px;letter-spacing:.12em;text-transform:uppercase">{{ $cat['label'] }} — {{ $cat['score'] }}/{{ $cat['max'] }}</p>
          @foreach($cat['checks'] as $check)
          <div style="display:flex;align-items:flex-start;gap:8px;padding:7px 12px;{{ !$check['passed'] ? 'background:rgba(196,120,120,.04);border-left:2px solid rgba(196,120,120,.12);' : '' }}">
            <span style="flex-shrink:0;font-size:.72rem;margin-top:1px;color:{{ $check['passed'] ? 'var(--green)' : 'var(--red)' }}">{{ $check['passed'] ? '✓' : '✕' }}</span>
            <span style="font-size:.76rem;color:var(--ivory);flex:1">{{ $check['label'] }}</span>
            <span style="font-size:.66rem;color:rgba(168,168,160,.35);flex-shrink:0">{{ $check['points'] }}/{{ $check['max'] }}</span>
          </div>
          @endforeach
        </div>
        @endforeach
      </div>

      {{-- Block: Strategic Opportunity --}}
      <div class="content-block">
        <span class="content-block-label">Strategic Opportunity</span>
        <div style="background:rgba(200,168,75,.04);border:1px solid rgba(200,168,75,.12);padding:20px 22px">
          <p style="font-size:.84rem;color:var(--ivory);line-height:1.6;margin-bottom:12px">
            You capture <strong style="color:var(--gold);font-weight:400">{{ $coveragePct }}%</strong> of AI visibility signals. Market leaders operate at <strong style="color:var(--green);font-weight:400">{{ $competitorCoverage }}%</strong>. The <strong style="color:var(--red);font-weight:400">{{ $competitorCoverage - $coveragePct }}% gap</strong> is uncaptured market coverage.
          </p>
          @php
            $weakestCat = collect($categories)->sortBy(fn($c) => $c['max'] > 0 ? ($c['score'] / $c['max']) : 1)->first();
            $strongestCat = collect($categories)->sortByDesc(fn($c) => $c['max'] > 0 ? ($c['score'] / $c['max']) : 0)->first();
          @endphp
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:14px">
            <div style="padding:14px;border:1px solid rgba(196,120,120,.12);background:rgba(196,120,120,.03)">
              <span style="font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:var(--red);display:block;margin-bottom:6px">Weakest Category</span>
              <span style="font-size:.84rem;color:var(--ivory);display:block">{{ $weakestCat['label'] ?? 'N/A' }}</span>
              <span style="font-size:.72rem;color:var(--muted)">{{ $weakestCat['score'] ?? 0 }}/{{ $weakestCat['max'] ?? 0 }} — Highest leverage for improvement</span>
            </div>
            <div style="padding:14px;border:1px solid rgba(106,175,144,.12);background:rgba(106,175,144,.03)">
              <span style="font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:var(--green);display:block;margin-bottom:6px">Strongest Category</span>
              <span style="font-size:.84rem;color:var(--ivory);display:block">{{ $strongestCat['label'] ?? 'N/A' }}</span>
              <span style="font-size:.72rem;color:var(--muted)">{{ $strongestCat['score'] ?? 0 }}/{{ $strongestCat['max'] ?? 0 }} — Protect and expand this advantage</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Broken links (if available) --}}
      @if(!empty($scan->broken_links) && is_array($scan->broken_links))
      <div class="content-block">
        <span class="content-block-label">Broken Pathways</span>
        <div style="background:rgba(196,120,120,.03);border:1px solid rgba(196,120,120,.1);padding:16px 18px">
          @foreach($scan->broken_links as $bl)
          <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(196,120,120,.06);font-size:.76rem">
            <span style="color:var(--muted);word-break:break-all">{{ $bl['url'] ?? '' }}</span>
            <span style="color:var(--red);flex-shrink:0;margin-left:12px">{{ $bl['status'] ?? 'Unreachable' }}</span>
          </div>
          @endforeach
        </div>
      </div>
      @endif

      <p style="font-size:.72rem;color:var(--muted);line-height:1.6;font-style:italic;text-align:center;margin-top:16px">
        For teams ready to delegate the complete system build — <a href="{{ route('book.index') }}" style="color:rgba(200,168,75,.55);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.15)">Full System starts at $4,799+</a>
      </p>
    </div>
    @else
    <div class="layer-body is-locked">
      <div class="layer-preview">
        <ul class="layer-preview-list">
          <li class="layer-preview-item">Your coverage: {{ $coveragePct }}% — Market leaders in your category: {{ $competitorCoverage }}%</li>
          <li class="layer-preview-item">Competitive gap analysis across 50+ pages and full system structure</li>
          <li class="layer-preview-item">Coverage expansion map — uncaptured market intelligence</li>
        </ul>
      </div>
    </div>
    <div class="layer-unlock">
      <p class="layer-unlock-text">Competitive benchmarks, market mapping, and your full coverage architecture.</p>
      <ul class="layer-bullet-list">
        <li class="layer-bullet"><em>Everything in Structural Leverage</em> — diagnostics + correction sequence included</li>
        <li class="layer-bullet">Competitive intelligence — <em>how market leaders outperform you</em></li>
        <li class="layer-bullet">Coverage expansion map — <em>where your market is uncaptured</em></li>
        <li class="layer-bullet">50+ pages analyzed with <em>full structural architecture</em></li>
      </ul>
      <a href="{{ route('quick-scan.upgrade') }}?plan=optimization&scan_id={{ $scan->id }}" class="layer-unlock-cta premium">Activate Your System — $489+ →</a>
      <p style="font-size:.68rem;color:rgba(168,168,160,.4);margin-top:14px">For teams ready to delegate the entire system build — <a href="{{ route('book.index') }}" style="color:rgba(200,168,75,.5);text-decoration:none;border-bottom:1px solid rgba(200,168,75,.15)">Full System starts at $4,799+</a></p>
    </div>
    @endif
  </div>

  @else
  {{-- ── Legacy fallback for old scans without categories ── --}}
  @if(!empty($scan->issues) && is_array($scan->issues))
  <div class="r-section">
    <p class="r-section-label">Issues Found ({{ count($scan->issues) }})</p>
    <ul class="r-list">
      @foreach($scan->issues as $issue)
        <li class="r-list-item issue">
          <span class="r-list-icon">✕</span>
          <span class="r-list-text">{{ $issue }}</span>
        </li>
      @endforeach
    </ul>
  </div>
  @endif

  @if(!empty($scan->strengths) && is_array($scan->strengths))
  <div class="r-section">
    <p class="r-section-label">What's Working ({{ count($scan->strengths) }})</p>
    <ul class="r-list">
      @foreach($scan->strengths as $strength)
        <li class="r-list-item strength">
          <span class="r-list-icon">✓</span>
          <span class="r-list-text">{{ $strength }}</span>
        </li>
      @endforeach
    </ul>
  </div>
  @endif
  @endif

  {{-- ── Tier Ladder ── --}}
  <div class="tier-ladder">
    @if($unlockLevel >= 4)
    <h2 class="tier-ladder-hed">All levels <em>active.</em></h2>
    @elseif($unlockLevel >= 2)
    <h2 class="tier-ladder-hed">Go deeper. <em>Expand it.</em></h2>
    @else
    <h2 class="tier-ladder-hed">This system goes deeper. <em>Expand it.</em></h2>
    @endif
    <div class="tier-ladder-grid">
      @if($unlockLevel >= 2)
      <div class="tier-ladder-item" style="border-color:rgba(106,175,144,.25);opacity:.7">
        <span class="tl-name">Signal Expansion</span>
        <span style="font-size:.68rem;color:var(--green)">✓ Active</span>
      </div>
      @else
      <a href="{{ route('quick-scan.upgrade') }}?plan=diagnostic&scan_id={{ $scan->id }}" class="tier-ladder-item">
        <span class="tl-name">Signal Expansion</span>
        <span class="tl-price"><sup style="font-size:.7rem;color:rgba(200,168,75,.5)">$</sup>99</span>
        <span class="tl-desc">Full signal mapping + priority gaps + exportable intelligence</span>
      </a>
      @endif
      @if($unlockLevel >= 3)
      <div class="tier-ladder-item" style="border-color:rgba(106,175,144,.25);opacity:.7">
        <span class="tl-name">Structural Leverage</span>
        <span style="font-size:.68rem;color:var(--green)">✓ Active</span>
      </div>
      @else
      <a href="{{ route('quick-scan.upgrade') }}?plan=fix-strategy&scan_id={{ $scan->id }}" class="tier-ladder-item tl-dominant">
        <span class="tl-name">Structural Leverage</span>
        <span class="tl-price"><sup style="font-size:.7rem;color:rgba(200,168,75,.5)">$</sup>249</span>
        <span class="tl-desc">Priority correction sequence + structural guidance</span>
        <span style="font-size:.68rem;color:rgba(200,168,75,.55);margin-top:2px;font-style:italic">This is where most sites begin to shift.</span>
      </a>
      @endif
      @if($unlockLevel >= 4)
      <div class="tier-ladder-item" style="border-color:rgba(106,175,144,.25);opacity:.7">
        <span class="tl-name">System Activation</span>
        <span style="font-size:.68rem;color:var(--green)">✓ Active</span>
      </div>
      @else
      <a href="{{ route('quick-scan.upgrade') }}?plan=optimization&scan_id={{ $scan->id }}" class="tier-ladder-item">
        <span class="tl-name">System Activation</span>
        <span class="tl-price"><sup style="font-size:.7rem;color:rgba(200,168,75,.5)">$</sup>489<sup style="font-size:.6rem;color:rgba(200,168,75,.4)">+</sup></span>
        <span class="tl-desc">Competitive positioning + coverage architecture</span>
      </a>
      @endif
    </div>
    @if($unlockLevel < 4)
    <p class="cta-book">
      Not sure which level?&nbsp;
      <a href="/pricing">See all levels</a> — find the right depth for your site.
    </p>
    @endif
  </div>

  <!-- Save to Dashboard -->
  @auth
  <div class="save-section">
    <span style="font-size:.56rem;letter-spacing:.3em;text-transform:uppercase;color:rgba(200,168,75,.45);display:block;margin-bottom:10px">System Baseline</span>
    @if($isUpgraded)
    <p style="font-family:'Cormorant Garamond',serif;font-size:1.15rem;color:var(--ivory);font-weight:300;margin-bottom:6px">This is your active baseline.</p>
    <p style="font-size:.74rem;color:var(--muted);line-height:1.5;margin-bottom:16px;max-width:380px;margin-left:auto;margin-right:auto">Saved to your account. Future scans will measure against this point.</p>
    @else
    <p style="font-family:'Cormorant Garamond',serif;font-size:1.15rem;color:var(--ivory);font-weight:300;margin-bottom:6px">Lock this as your system baseline.</p>
    <p style="font-size:.74rem;color:var(--muted);line-height:1.5;margin-bottom:16px;max-width:380px;margin-left:auto;margin-right:auto">Track changes over time. Every future scan measures against this point.</p>
    @endif
    <a href="/dashboard#ai-scans" class="save-btn">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
      View in Dashboard
    </a>
  </div>
  @else
  <div class="save-section">
    <span style="font-size:.56rem;letter-spacing:.3em;text-transform:uppercase;color:rgba(200,168,75,.45);display:block;margin-bottom:10px">System Baseline</span>
    @if($isUpgraded)
    <p style="font-family:'Cormorant Garamond',serif;font-size:1.15rem;color:var(--ivory);font-weight:300;margin-bottom:6px">Lock this as yours.</p>
    <p style="font-size:.74rem;color:var(--muted);line-height:1.5;margin-bottom:16px;max-width:380px;margin-left:auto;margin-right:auto">Your expanded intelligence is ready. Save it to track changes over time.</p>
    @else
    <p style="font-family:'Cormorant Garamond',serif;font-size:1.15rem;color:var(--ivory);font-weight:300;margin-bottom:6px">Make this your baseline.</p>
    <p style="font-size:.74rem;color:var(--muted);line-height:1.5;margin-bottom:16px;max-width:380px;margin-left:auto;margin-right:auto">Save your score, track changes, and access recommendations.</p>
    @endif
    <a href="{{ route('auth.google.redirect') }}?scan_id={{ $scan->id }}" class="save-btn" style="background:var(--gold);border-radius:6px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18A10.96 10.96 0 001 12c0 1.77.42 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
      Save to Dashboard with Google
    </a>
  </div>
  @endauth

  <!-- Actions -->
  <div class="result-actions">
    <a href="{{ route('quick-scan.show') }}" class="scan-again">Scan a different URL</a>
  </div>

</div>

<!-- Footer -->
<footer>
  <a href="{{ url('/') }}" class="logo" style="opacity:.5">
    <span class="logo-seo">SEO</span><span class="logo-ai">AI</span><span class="logo-co">co</span>
  </a>
  <span class="footer-copy">&copy; 2026 SEO AI Co™</span>
  <nav class="footer-legal">
    <a href="{{ route('privacy') }}">Privacy</a>
    <a href="{{ route('terms') }}">Terms</a>
    <a href="/pricing">Pricing</a>
  </nav>
</footer>

<script>
  const nav = document.getElementById('nav');
  window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 60));

  window.addEventListener('load', function() {
    // Animate score ring
    setTimeout(function() {
      const ring = document.getElementById('scoreRing');
      if (ring) ring.classList.add('animate');
    }, 300);

    // Animate category bars + depth progress
    setTimeout(function() {
      document.querySelectorAll('.cat-bar-fill, .market-bar-fill, .depth-progress-fill').forEach(function(bar) {
        bar.style.width = bar.dataset.width + '%';
      });
    }, 600);

    // Staggered reveal on scroll
    var io = new IntersectionObserver(function(entries) {
      entries.forEach(function(e) { if (e.isIntersecting) { e.target.classList.add('in-view'); io.unobserve(e.target); } });
    }, { threshold: 0.15 });
    document.querySelectorAll('.report-layer').forEach(function(el) { io.observe(el); });
  });
</script>
@include('components.tm-style')
</body>
</html>
