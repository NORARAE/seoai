@php
  $infoModalContent = [
      'scan-basic' => [
          'eyebrow' => 'Scan Details',
          'headline' => 'What your $2 scan shows',
          'translation' => 'See exactly why AI is skipping your site-and what to fix first.',
          'included' => [
              'Your AI visibility score (0-100)',
              'The top issues blocking AI selection',
              'The fastest fix with the biggest impact',
          ],
          'best_for' => [
              'Business owners who want quick clarity',
              'Sites not showing in AI answers',
              'Anyone unsure what is actually wrong',
          ],
          'outcome' => 'You will know exactly what to fix first-no guessing.',
          'cta' => 'Start $2 Scan',
          'href' => route('scan.start'),
      ],
      'layer-1-foundation' => [
          'eyebrow' => 'Layer 1',
          'headline' => 'Make your business understandable to AI',
          'translation' => 'If AI cannot clearly understand your site, nothing else matters.',
          'included' => [
              'Structured business identity signals',
              'Schema + machine-readable context',
              'Clear service + location definitions',
          ],
          'best_for' => [
              'Sites with unclear structure',
              'New or unoptimized websites',
              'Businesses not appearing in AI at all',
          ],
          'outcome' => 'AI can finally understand what you do.',
          'cta' => 'Unlock Foundation',
          'href' => route('scan.start'),
      ],
      'layer-2-answer-readiness' => [
          'eyebrow' => 'Layer 2',
          'headline' => 'Make your content usable by AI',
          'translation' => 'AI needs clear answers it can extract-not just pages.',
          'included' => [
              'Direct answer formatting',
              'Extractable content blocks',
              'Structured response signals',
          ],
          'best_for' => [
              'Sites with content but no AI visibility',
              'Blogs or service pages that do not convert',
              'Businesses not being quoted or cited',
          ],
          'outcome' => 'AI can pull answers directly from your site.',
          'cta' => 'Unlock Answer Readiness',
          'href' => route('checkout.signal-expansion'),
      ],
      'layer-3-structural-leverage' => [
          'eyebrow' => 'Layer 3',
          'headline' => 'Fix what matters first',
          'translation' => 'Not everything matters equally-this shows what moves the needle fastest.',
          'included' => [
              'Ranked fix sequence',
              'Priority-based optimization path',
              'Impact-driven execution order',
          ],
          'best_for' => [
              'Sites with multiple issues',
              'Anyone overwhelmed by SEO tasks',
              'Teams that need direction',
          ],
          'outcome' => 'You fix the highest-impact issues first.',
          'cta' => 'Unlock Priority System',
          'href' => route('checkout.structural-leverage'),
      ],
      'layer-4-system-activation' => [
          'eyebrow' => 'Layer 4',
          'headline' => 'Activate full AI visibility',
          'translation' => 'This is where your site becomes consistently selectable by AI.',
          'included' => [
              'Full signal integration',
              'Entity + authority reinforcement',
              'Complete system alignment',
          ],
          'best_for' => [
              'Businesses ready to dominate locally',
              'High-competition markets',
              'Scaling visibility across locations',
          ],
          'outcome' => 'AI selects your site more consistently.',
          'cta' => 'Activate System',
          'href' => route('checkout.system-activation'),
      ],
  ];
@endphp

<style>
.info-modal-mask{
  position:fixed;inset:0;z-index:220;
  background:rgba(5,5,4,.72);backdrop-filter:blur(6px);
  opacity:0;pointer-events:none;transition:opacity .28s ease;
}
.info-modal-mask[data-open='true']{opacity:1;pointer-events:auto}
.info-modal-dialog{
  width:min(700px,calc(100vw - 32px));
  max-height:min(88vh,880px);
  overflow:auto;
  margin:6vh auto 0;
  background:linear-gradient(180deg,rgba(18,16,12,.98),rgba(9,8,6,.99));
  border:1px solid rgba(200,168,75,.2);
  border-radius:6px;
  box-shadow:0 24px 60px rgba(0,0,0,.5),inset 0 1px 0 rgba(200,168,75,.06);
  padding:24px 24px 22px;
  transform:translateY(12px) scale(.985);
  transition:transform .3s ease;
}
.info-modal-mask[data-open='true'] .info-modal-dialog{transform:translateY(0) scale(1)}
.info-modal-top{display:flex;justify-content:space-between;gap:18px;align-items:flex-start;margin-bottom:16px}
.info-modal-eye{font-size:.56rem;letter-spacing:.24em;text-transform:uppercase;color:rgba(200,168,75,.72);margin:0 0 10px}
.info-modal-hed{font-family:'Cormorant Garamond',serif;font-size:clamp(1.5rem,2.4vw,2.1rem);font-weight:300;line-height:1.12;color:var(--ivory);margin:0}
.info-modal-translation{font-size:.86rem;line-height:1.72;color:rgba(206,202,192,.78);margin:10px 0 0}
.info-modal-close{
  background:none;border:1px solid rgba(200,168,75,.2);color:rgba(237,232,222,.76);
  min-height:38px;padding:0 12px;border-radius:4px;cursor:pointer;font-size:.64rem;letter-spacing:.14em;text-transform:uppercase;
}
.info-modal-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px}
.info-modal-block{border:1px solid rgba(200,168,75,.08);background:rgba(0,0,0,.18);border-radius:4px;padding:14px 14px 12px}
.info-modal-block strong{display:block;font-size:.58rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.66);margin-bottom:8px}
.info-modal-block p,.info-modal-block li{font-size:.84rem;line-height:1.72;color:rgba(206,202,192,.82)}
.info-modal-block ul{margin:0;padding-left:18px}
.info-modal-actions{display:flex;justify-content:flex-start;gap:12px;flex-wrap:wrap;margin-top:8px}
.info-modal-close-text{background:none;border:none;color:rgba(198,194,182,.72);cursor:pointer;letter-spacing:.1em;text-transform:uppercase;font-size:.64rem;padding:0 4px}
@media(max-width:640px){
  .info-modal-dialog{width:min(100vw - 20px,700px);padding:20px 18px 18px}
  .info-modal-grid{grid-template-columns:1fr}
}
</style>

<div class="info-modal-mask" id="infoCtaModalMask" data-open="false" aria-hidden="true">
  <div class="info-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="infoCtaModalTitle">
    <div class="info-modal-top">
      <div>
        <p class="info-modal-eye" id="infoCtaModalEyebrow">Package Details</p>
        <h3 class="info-modal-hed" id="infoCtaModalTitle">What this unlocks</h3>
        <p class="info-modal-translation" id="infoCtaModalTranslation"></p>
      </div>
      <button type="button" class="info-modal-close js-info-modal-close" id="infoCtaModalCloseTop">Close</button>
    </div>
    <div class="info-modal-grid">
      <div class="info-modal-block">
        <strong>Included now</strong>
        <ul id="infoCtaModalIncluded"></ul>
      </div>
      <div class="info-modal-block">
        <strong>What improves</strong>
        <p id="infoCtaModalImproves"></p>
      </div>
      <div class="info-modal-block">
        <strong>Best for</strong>
        <ul id="infoCtaModalBestFor"></ul>
      </div>
      <div class="info-modal-block">
        <strong>Next outcome</strong>
        <p id="infoCtaModalOutcome"></p>
      </div>
    </div>
    <div class="info-modal-actions">
      <a href="{{ route('scan.start') }}" class="btn-primary" id="infoCtaModalPrimary">Start $2 Scan</a>
      <button type="button" class="info-modal-close-text js-info-modal-close">Close</button>
    </div>
  </div>
</div>

<script>
(function(){
  var modalData = @json($infoModalContent);
  var mask = document.getElementById('infoCtaModalMask');
  if(!mask) return;

  var lastFocused = null;
  var eyebrowEl = document.getElementById('infoCtaModalEyebrow');
  var titleEl = document.getElementById('infoCtaModalTitle');
  var translationEl = document.getElementById('infoCtaModalTranslation');
  var includedEl = document.getElementById('infoCtaModalIncluded');
  var improvesEl = document.getElementById('infoCtaModalImproves');
  var bestForEl = document.getElementById('infoCtaModalBestFor');
  var outcomeEl = document.getElementById('infoCtaModalOutcome');
  var primaryEl = document.getElementById('infoCtaModalPrimary');
  var closeTop = document.getElementById('infoCtaModalCloseTop');

  function fillList(target, values){
    target.innerHTML = '';
    (values || []).forEach(function(item){
      var li = document.createElement('li');
      li.textContent = item;
      target.appendChild(li);
    });
  }

  function openModal(trigger){
    var key = trigger.getAttribute('data-info-modal');
    if(!key || !modalData[key]) return;
    var data = modalData[key];

    lastFocused = document.activeElement;

    eyebrowEl.textContent = data.eyebrow || 'Package Details';
    titleEl.textContent = data.headline || 'What this unlocks';
    translationEl.textContent = data.translation || '';
    fillList(includedEl, data.included || []);
    improvesEl.textContent = data.improves || '';
    fillList(bestForEl, data.best_for || []);
    outcomeEl.textContent = data.outcome || '';

    primaryEl.textContent = trigger.getAttribute('data-info-modal-cta') || data.cta || 'Continue';
    primaryEl.href = trigger.getAttribute('data-info-modal-href') || data.href || primaryEl.href;

    mask.dataset.open = 'true';
    mask.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    if(closeTop) closeTop.focus();
  }

  function closeModal(){
    mask.dataset.open = 'false';
    mask.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if(lastFocused && typeof lastFocused.focus === 'function') {
      lastFocused.focus();
    }
  }

  document.addEventListener('click', function(evt){
    var trigger = evt.target.closest('.js-info-modal-trigger');
    if(trigger){
      evt.preventDefault();
      openModal(trigger);
      return;
    }

    if(evt.target.classList.contains('js-info-modal-close')){
      evt.preventDefault();
      closeModal();
      return;
    }

    if(evt.target === mask){
      closeModal();
    }
  });

  document.addEventListener('keydown', function(evt){
    if(evt.key === 'Escape' && mask.dataset.open === 'true'){
      closeModal();
    }
  });
})();
</script>
