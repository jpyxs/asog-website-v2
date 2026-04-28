(function () {
  var bars = document.querySelectorAll('.eval-bar');
  if (!bars.length) return;

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      entry.target.style.transition = 'width .8s cubic-bezier(.16,1,.3,1)';
      entry.target.style.width = entry.target.dataset.w;
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.3 });

  bars.forEach(function (bar) {
    observer.observe(bar);
  });
})();
