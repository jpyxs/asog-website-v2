(function () {
  var tabs = document.querySelectorAll('.ib-tab');
  var cards = document.querySelectorAll('#ibStack .ib-card');
  var stack = document.getElementById('ibStack');
  var coming = document.getElementById('ibComingSoon');
  var csLabel = document.getElementById('ibCSLabel');
  var countLabel = document.getElementById('ibCountLabel');

  if (!tabs.length) return;

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      tabs.forEach(function (item) {
        item.classList.remove('is-active');
      });
      tab.classList.add('is-active');

      var cohort = tab.dataset.cohort;
      var visible = 0;

      cards.forEach(function (card) {
        if (card.dataset.cohort === cohort) {
          card.style.display = '';
          visible++;
        } else {
          card.style.display = 'none';
        }
      });

      if (visible === 0) {
        if (stack) stack.style.display = 'none';
        if (coming) coming.style.display = '';
        if (csLabel) csLabel.textContent = cohort;
        if (countLabel) countLabel.textContent = 'Interested in joining ASOG-TBI?';
        return;
      }

      if (stack) stack.style.display = '';
      if (coming) coming.style.display = 'none';
      if (countLabel) {
        countLabel.textContent =
          visible +
          ' incubatee' +
          (visible !== 1 ? 's' : '') +
          ' in ' +
          cohort;
      }

      if (typeof gsap !== 'undefined') {
        gsap.from('#ibStack .ib-card:not([style*="display: none"])', {
          opacity: 0,
          y: 25,
          scale: 0.94,
          duration: 0.35,
          stagger: 0.05,
          ease: 'power2.out'
        });
      }
    });
  });
})();
