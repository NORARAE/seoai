{{-- Shared mobile nav toggle JS — include before </body> --}}
<script>
(function(){
  var btn      = document.getElementById('navHamburger');
  var menu     = document.getElementById('navMenu');
  var backdrop = document.getElementById('navBackdrop');
  if(!btn || !menu) return;

  function openMenu(){
    backdrop.classList.add('is-open');
    menu.classList.add('is-open');
    menu.removeAttribute('aria-hidden');
    backdrop.removeAttribute('aria-hidden');
    btn.classList.add('is-open');
    btn.setAttribute('aria-expanded','true');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu(){
    menu.classList.remove('is-open');
    backdrop.classList.remove('is-open');
    btn.classList.remove('is-open');
    btn.setAttribute('aria-expanded','false');
    menu.setAttribute('aria-hidden','true');
    backdrop.setAttribute('aria-hidden','true');
    document.body.style.overflow = '';
  }
  function toggleMenu(){
    menu.classList.contains('is-open') ? closeMenu() : openMenu();
  }

  btn.addEventListener('click', function(e){
    e.stopPropagation();
    toggleMenu();
  });

  backdrop.addEventListener('click', closeMenu);

  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') closeMenu();
  });

  menu.querySelectorAll('[data-menu-close]').forEach(function(el){
    el.addEventListener('click', closeMenu);
  });
})();
</script>