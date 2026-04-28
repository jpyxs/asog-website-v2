(function () {
  var modal = document.getElementById('guidelinesModal');
  var body = document.getElementById('guidelinesBody');
  if (!modal || !body) return;

  function onEscape(event) {
    if (event.key === 'Escape') {
      closeGuidelines();
    }
  }

  function openGuidelines() {
    modal.classList.remove('opacity-0', 'pointer-events-none');
    body.classList.remove('scale-95');
    body.classList.add('scale-100');
    document.body.style.overflow = 'hidden';
    document.addEventListener('keydown', onEscape);
  }

  function closeGuidelines() {
    modal.classList.add('opacity-0', 'pointer-events-none');
    body.classList.remove('scale-100');
    body.classList.add('scale-95');
    document.body.style.overflow = '';
    document.removeEventListener('keydown', onEscape);
  }

  document.querySelectorAll('[data-open-guidelines]').forEach(function (btn) {
    btn.addEventListener('click', function (event) {
      event.preventDefault();
      openGuidelines();
    });
  });

  var closeBtn = document.getElementById('btnCloseGuidelines');
  if (closeBtn) {
    closeBtn.addEventListener('click', closeGuidelines);
  }

  var backdrop = document.getElementById('guidelinesBackdrop');
  if (backdrop) {
    backdrop.addEventListener('click', closeGuidelines);
  }

  window.openGuidelines = openGuidelines;
})();
