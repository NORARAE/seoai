{{-- Reusable back-to-top button — include before </body> --}}
<button class="btt{{ ($dashMode ?? false) ? ' btt--dash' : '' }}" id="btt" aria-label="Back to top">
  <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 4l-8 8h5v8h6v-8h5z"/></svg>
</button>
<style>
.btt{
  position:fixed;bottom:32px;right:32px;z-index:300;
  width:44px;height:44px;border-radius:50%;
  background:var(--card-bg,rgba(18,18,18,.92));
  border:1px solid rgba(200,168,75,.18);
  color:var(--gold,#c8a84b);
  cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  opacity:0;pointer-events:none;
  transform:translateY(10px);
  transition:opacity .35s,transform .35s,border-color .3s,box-shadow .3s;
  box-shadow:0 2px 12px rgba(0,0,0,.25),0 0 0 0 rgba(200,168,75,0);
}
.btt.show{opacity:1;pointer-events:auto;transform:none}
.btt:hover{
  border-color:rgba(200,168,75,.4);
  box-shadow:0 2px 16px rgba(0,0,0,.3),0 0 16px rgba(200,168,75,.08);
  transform:translateY(-2px);
}
.btt:focus-visible{outline:2px solid var(--gold,#c8a84b);outline-offset:3px}
.btt svg{width:16px;height:16px}

/* Dashboard modifier — quieter */
.btt--dash{width:38px;height:38px;bottom:24px;right:24px}
.btt--dash svg{width:14px;height:14px}

@media(max-width:768px){
  .btt{bottom:24px;right:20px;width:40px;height:40px}
  .btt--dash{bottom:20px;right:16px;width:36px;height:36px}
}
</style>
<script>
(function(){
  var b=document.getElementById('btt');
  if(!b)return;
  window.addEventListener('scroll',function(){b.classList.toggle('show',scrollY>600)},{passive:true});
  b.addEventListener('click',function(){window.scrollTo({top:0,behavior:'smooth'})});
})();
</script>
