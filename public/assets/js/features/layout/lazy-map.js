(function () {
  'use strict';

  var maps = Array.prototype.slice.call(document.querySelectorAll('[data-lazy-map]'));
  if (!maps.length) return;

  function loadMap(container) {
    if (!container || container.dataset.mapLoaded === '1') return;

    var src = container.dataset.mapSrc || '';
    if (!src) return;

    container.dataset.mapLoaded = '1';

    var iframe = document.createElement('iframe');
    iframe.title = container.dataset.mapTitle || 'Google Maps location';
    iframe.setAttribute('aria-label', container.dataset.mapLabel || iframe.title);
    iframe.src = src;
    iframe.className = 'absolute inset-0 w-full h-full';
    iframe.style.border = '0';
    iframe.allowFullscreen = true;
    iframe.loading = 'lazy';
    iframe.referrerPolicy = 'no-referrer-when-downgrade';

    container.innerHTML = '';
    container.appendChild(iframe);
  }

  if (!('IntersectionObserver' in window)) {
    maps.forEach(loadMap);
    return;
  }

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;

      loadMap(entry.target);
      observer.unobserve(entry.target);
    });
  }, {
    rootMargin: '900px 0px',
    threshold: 0.01
  });

  maps.forEach(function (map) {
    map.addEventListener('pointerenter', function () { loadMap(map); }, { once: true });
    map.addEventListener('touchstart', function () { loadMap(map); }, { once: true, passive: true });
    map.addEventListener('focusin', function () { loadMap(map); }, { once: true });
    observer.observe(map);
  });
})();
