{{--
  AI Assistant — Floating launcher + slide-up panel
  Brand: dark / gold / premium minimal
  Backend: POST /ai/chat (AiAssistantController, web middleware, CSRF)
  Usage:
    @include('partials.ai-assistant', [
        'context'  => 'pricing',          // optional — informs assistant tone
        'starters' => ['...', '...'],     // optional starter prompts
    ])
--}}

@php
    $aiContext  = $context  ?? 'general';
    $aiStarters = $starters ?? [];
@endphp

{{-- ══ Launcher button ══ --}}
<button id="ai-launcher" aria-label="Ask AI assistant" aria-expanded="false" aria-controls="ai-panel">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
    <path d="M8 10h.01M12 10h.01M16 10h.01M8 10c0 2.21 1.79 4 4 4s4-1.79 4-4"/>
  </svg>
  <span class="ai-launcher-label">Ask AI</span>
</button>

{{-- ══ Panel ══ --}}
<div id="ai-panel" role="dialog" aria-label="AI Assistant" aria-hidden="true">
  <div class="ai-panel-inner">

    <header class="ai-header">
      <div class="ai-header-left">
        <span class="ai-header-mark">&#x25C6;</span>
        <div>
          <p class="ai-header-name">SEO AI Co™ Assistant</p>
          <p class="ai-header-sub">AI visibility guide</p>
        </div>
      </div>
      <button class="ai-close" id="ai-close" aria-label="Close assistant">&times;</button>
    </header>

    <div class="ai-messages" id="ai-messages" role="log" aria-live="polite" aria-atomic="false">
      <div class="ai-msg ai-msg--assistant">
        <p>System active. I can explain how each level works, help you identify your entry point, or answer any questions about AI visibility.</p>
      </div>
    </div>

    {{-- Starter prompts --}}
    @if(count($aiStarters))
    <div class="ai-starters" id="ai-starters">
      @foreach($aiStarters as $starter)
      <button class="ai-starter" type="button" data-starter="{{ $starter }}">{{ $starter }}</button>
      @endforeach
    </div>
    @endif

    <form class="ai-input-row" id="ai-form" autocomplete="off" novalidate>
      @csrf
      <input
        type="text"
        id="ai-input"
        name="message"
        placeholder="Ask anything…"
        maxlength="500"
        autocomplete="off"
        spellcheck="false"
        aria-label="Your message"
      >
      <button type="submit" class="ai-send" aria-label="Send">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <line x1="22" y1="2" x2="11" y2="13"/>
          <polygon points="22 2 15 22 11 13 2 9 22 2"/>
        </svg>
      </button>
    </form>

  </div>
</div>

<style>
/* ── BTT stacking — Ask AI is primary (corner), BTT floats above ── */
:root{--btt-bottom:80px;--btt-bottom-mob:72px}
/* ── Launcher ── */
#ai-launcher{
  position:fixed;bottom:20px;right:24px;z-index:9000;
  display:flex;align-items:center;gap:8px;
  padding:11px 18px 11px 14px;
  background:rgba(12,11,8,.96);
  border:1px solid rgba(200,168,75,.28);
  color:rgba(200,168,75,.88);
  font-family:'DM Sans',sans-serif;font-size:.62rem;letter-spacing:.14em;text-transform:uppercase;font-weight:500;
  cursor:pointer;
  transition:border-color .26s,background .26s,box-shadow .26s;
  box-shadow:0 4px 24px rgba(0,0,0,.45);
}
#ai-launcher:hover{
  border-color:rgba(200,168,75,.52);
  background:rgba(16,15,10,.98);
  box-shadow:0 6px 32px rgba(0,0,0,.55),0 0 20px rgba(200,168,75,.06);
}
#ai-launcher svg{color:rgba(200,168,75,.72);flex-shrink:0}
.ai-launcher-label{white-space:nowrap}
/* ── Panel ── */
#ai-panel{
  position:fixed;bottom:74px;right:24px;z-index:8999;
  width:min(400px, calc(100vw - 48px));
  background:rgba(11,10,8,.98);
  border:1px solid rgba(200,168,75,.2);
  box-shadow:0 16px 64px rgba(0,0,0,.65),0 0 0 1px rgba(200,168,75,.06) inset;
  display:flex;flex-direction:column;
  max-height:min(540px, calc(100vh - 120px));
  opacity:0;transform:translateY(12px) scale(.97);
  pointer-events:none;
  transition:opacity .26s ease,transform .26s ease;
  font-family:'DM Sans',sans-serif;
}
#ai-panel.is-open{
  opacity:1;transform:translateY(0) scale(1);
  pointer-events:all;
}
.ai-panel-inner{display:flex;flex-direction:column;height:100%;min-height:0}
/* ── Header ── */
.ai-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:14px 18px 12px;
  border-bottom:1px solid rgba(200,168,75,.1);
  flex-shrink:0;
}
.ai-header-left{display:flex;align-items:center;gap:10px}
.ai-header-mark{font-size:.8rem;color:rgba(200,168,75,.72);line-height:1}
.ai-header-name{
  font-size:.68rem;letter-spacing:.1em;text-transform:uppercase;
  color:rgba(220,216,206,.88);font-weight:500;margin-bottom:1px;
}
.ai-header-sub{
  font-size:.6rem;color:rgba(155,155,148,.56);letter-spacing:.06em;
}
.ai-close{
  background:none;border:none;cursor:pointer;padding:4px;
  color:rgba(155,155,148,.5);font-size:1.2rem;line-height:1;
  transition:color .2s;
}
.ai-close:hover{color:rgba(200,168,75,.72)}
/* ── Messages ── */
.ai-messages{
  flex:1;overflow-y:auto;padding:16px 18px;display:flex;flex-direction:column;gap:10px;
  scrollbar-width:thin;scrollbar-color:rgba(200,168,75,.12) transparent;
  min-height:0;
}
.ai-messages::-webkit-scrollbar{width:3px}
.ai-messages::-webkit-scrollbar-track{background:transparent}
.ai-messages::-webkit-scrollbar-thumb{background:rgba(200,168,75,.12);border-radius:2px}
.ai-msg{
  max-width:92%;font-size:.76rem;line-height:1.68;
}
.ai-msg p{margin:0}
.ai-msg--assistant{
  align-self:flex-start;
  color:rgba(192,190,182,.88);
}
.ai-msg--user{
  align-self:flex-end;
  background:rgba(200,168,75,.08);
  border:1px solid rgba(200,168,75,.16);
  padding:8px 12px;
  color:rgba(210,207,198,.9);
}
.ai-msg--thinking{
  align-self:flex-start;
  color:rgba(155,155,148,.5);
  font-style:italic;
  font-size:.72rem;
}
/* ── Starters ── */
.ai-starters{
  display:flex;flex-direction:column;gap:4px;
  padding:10px 18px 6px;
  border-top:1px solid rgba(200,168,75,.07);
  flex-shrink:0;
}
.ai-starter{
  background:rgba(200,168,75,.04);
  border:1px solid rgba(200,168,75,.14);
  padding:7px 12px;text-align:left;cursor:pointer;
  font-family:'DM Sans',sans-serif;font-size:.64rem;
  color:rgba(200,168,75,.78);letter-spacing:.04em;
  transition:border-color .2s,background .2s,color .2s;
}
.ai-starter:hover{
  border-color:rgba(200,168,75,.3);
  background:rgba(200,168,75,.07);
  color:rgba(200,168,75,.96);
}
/* ── Input ── */
.ai-input-row{
  display:flex;gap:8px;padding:12px 18px;
  border-top:1px solid rgba(200,168,75,.1);
  flex-shrink:0;
  background:rgba(8,8,6,.6);
}
#ai-input{
  flex:1;background:rgba(200,168,75,.04);
  border:1px solid rgba(200,168,75,.14);
  padding:9px 12px;
  font-family:'DM Sans',sans-serif;font-size:.74rem;
  color:rgba(210,207,198,.9);outline:none;
  transition:border-color .2s;
}
#ai-input::placeholder{color:rgba(155,155,148,.44);font-size:.7rem}
#ai-input:focus{border-color:rgba(200,168,75,.32)}
.ai-send{
  background:rgba(200,168,75,.1);
  border:1px solid rgba(200,168,75,.22);
  padding:9px 12px;cursor:pointer;
  color:rgba(200,168,75,.78);
  transition:all .2s;flex-shrink:0;
}
.ai-send:hover{
  background:rgba(200,168,75,.16);
  border-color:rgba(200,168,75,.4);
  color:var(--gold,#c8a84b);
}
@media(max-width:480px){
  /* --mob-bar-h: 0px default; override on pages with fixed bottom bar */
  #ai-launcher{bottom:calc(16px + var(--mob-bar-h,0px) + env(safe-area-inset-bottom,0px));right:16px;padding:10px 14px 10px 12px;font-size:.6rem}
  #ai-panel{bottom:calc(60px + var(--mob-bar-h,0px) + env(safe-area-inset-bottom,0px));right:16px;width:calc(100vw - 32px);max-height:calc(100vh - 100px)}
}
</style>

<script>
(function(){
  var launcher = document.getElementById('ai-launcher');
  var panel    = document.getElementById('ai-panel');
  var closeBtn = document.getElementById('ai-close');
  var form     = document.getElementById('ai-form');
  var input    = document.getElementById('ai-input');
  var msgs     = document.getElementById('ai-messages');
  var starters = document.getElementById('ai-starters');
  if(!launcher || !panel){ return; }

  var history  = [];
  var context  = '{{ $aiContext }}';
  var csrfToken = document.querySelector('meta[name="csrf-token"]') ?
    document.querySelector('meta[name="csrf-token"]').content : '';
  // Also check for the hidden CSRF input from the @csrf Blade directive
  var csrfInput = form ? form.querySelector('input[name="_token"]') : null;
  if(csrfInput && !csrfToken){ csrfToken = csrfInput.value; }

  function open(){
    panel.classList.add('is-open');
    launcher.setAttribute('aria-expanded','true');
    panel.setAttribute('aria-hidden','false');
    setTimeout(function(){ input && input.focus(); }, 280);
  }
  function close(){
    panel.classList.remove('is-open');
    launcher.setAttribute('aria-expanded','false');
    panel.setAttribute('aria-hidden','true');
  }

  launcher.addEventListener('click', function(){
    panel.classList.contains('is-open') ? close() : open();
  });
  closeBtn && closeBtn.addEventListener('click', close);

  // Close on outside click
  document.addEventListener('click', function(e){
    if(panel.classList.contains('is-open') &&
       !panel.contains(e.target) &&
       e.target !== launcher &&
       !launcher.contains(e.target)){
      close();
    }
  });

  // Escape key
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape' && panel.classList.contains('is-open')){ close(); }
  });

  // Starter prompts
  if(starters){
    starters.querySelectorAll('.ai-starter').forEach(function(btn){
      btn.addEventListener('click', function(){
        var text = btn.getAttribute('data-starter') || btn.textContent.trim();
        input.value = text;
        starters.style.display = 'none';
        form.dispatchEvent(new Event('submit'));
      });
    });
  }

  function appendMsg(role, text){
    var div = document.createElement('div');
    div.className = 'ai-msg ai-msg--' + role;
    var p = document.createElement('p');
    p.textContent = text;
    div.appendChild(p);
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
  }

  form && form.addEventListener('submit', function(e){
    e.preventDefault();
    var message = input.value.trim();
    if(!message){ return; }

    // Hide starters once first message sent
    if(starters){ starters.style.display = 'none'; }

    appendMsg('user', message);
    history.push({role:'user', content:message});
    input.value = '';

    var thinking = appendMsg('thinking', 'Working…');

    fetch('/ai/chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken || '',
      },
      body: JSON.stringify({
        message: message,
        history: history.slice(-10).filter(function(m){ return m.role !== 'user' || m.content !== message; }),
        context: context,
      }),
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
      msgs.removeChild(thinking);
      var reply = (data.ok && data.reply) ? data.reply : 'Something went wrong. Please try again.';
      appendMsg('assistant', reply);
      history.push({role:'assistant', content:reply});
    })
    .catch(function(){
      msgs.removeChild(thinking);
      appendMsg('assistant', 'Connection error. Please try again.');
    });
  });
})();
</script>
