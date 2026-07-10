(function () {
  'use strict';

  function initEvaluationBars() {
    var bars = document.querySelectorAll('.eval-bar');
    if (!bars.length || !('IntersectionObserver' in window)) {
      bars.forEach(function (bar) {
        bar.style.width = bar.dataset.w || bar.style.width;
      });
      return;
    }

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
  }

  function safeSessionGet(key) {
    try {
      return window.sessionStorage ? window.sessionStorage.getItem(key) : null;
    } catch (error) {
      return null;
    }
  }

  function safeSessionSet(key, value) {
    try {
      if (window.sessionStorage) window.sessionStorage.setItem(key, value);
    } catch (error) {}
  }

  function initStatusModal() {
    var modal = document.querySelector('[data-apply-status-modal]');
    if (!modal) return;

    var dialog = modal.querySelector('.apply-status-modal__dialog');
    var closeButtons = modal.querySelectorAll('[data-apply-status-close]');
    var overviewButton = modal.querySelector('[data-apply-status-overview]');
    var dismissKey = modal.getAttribute('data-dismiss-key') || 'asog_apply_status_modal';
    var targetSelector = modal.getAttribute('data-overview-target') || '#application-overview';
    var previousFocus = null;
    var closeTimer = null;

    function getFocusable() {
      return Array.prototype.slice.call(modal.querySelectorAll([
        'a[href]',
        'button:not([disabled])',
        'textarea:not([disabled])',
        'input:not([disabled])',
        'select:not([disabled])',
        '[tabindex]:not([tabindex="-1"])'
      ].join(','))).filter(function (node) {
        return !!(node.offsetWidth || node.offsetHeight || node.getClientRects().length);
      });
    }

    function openModal() {
      if (closeTimer) {
        window.clearTimeout(closeTimer);
        closeTimer = null;
      }
      previousFocus = document.activeElement;
      modal.classList.remove('is-closing');
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      document.documentElement.classList.add('apply-status-modal-lock');

      window.setTimeout(function () {
        var focusable = getFocusable();
        (dialog || focusable[0] || modal).focus({ preventScroll: true });
      }, 30);
    }

    function closeModal(markDismissed) {
      if (!modal.classList.contains('is-open') || modal.classList.contains('is-closing')) return;
      var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      modal.classList.add('is-closing');
      modal.setAttribute('aria-hidden', 'true');
      if (markDismissed !== false) safeSessionSet(dismissKey, '1');

      closeTimer = window.setTimeout(function () {
        modal.classList.remove('is-open', 'is-closing');
        document.documentElement.classList.remove('apply-status-modal-lock');

        if (previousFocus && typeof previousFocus.focus === 'function' && document.contains(previousFocus)) {
          previousFocus.focus({ preventScroll: true });
        }

        closeTimer = null;
      }, reduceMotion ? 0 : 190);
    }

    function scrollToOverview() {
      var target = document.querySelector(targetSelector);
      closeModal(true);
      if (!target) return;
      var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      window.setTimeout(function () {
        target.scrollIntoView({
          behavior: reduceMotion ? 'auto' : 'smooth',
          block: 'start'
        });
      }, reduceMotion ? 0 : 210);
    }

    closeButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        closeModal(true);
      });
    });

    if (overviewButton) {
      overviewButton.addEventListener('click', scrollToOverview);
    }

    document.addEventListener('keydown', function (event) {
      if (!modal.classList.contains('is-open')) return;

      if (event.key === 'Escape') {
        event.preventDefault();
        closeModal(true);
        return;
      }

      if (event.key !== 'Tab') return;
      var focusable = getFocusable();
      if (!focusable.length) {
        event.preventDefault();
        if (dialog) dialog.focus({ preventScroll: true });
        return;
      }

      var first = focusable[0];
      var last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    });

    if (safeSessionGet(dismissKey) !== '1') {
      openModal();
    }
  }

  initEvaluationBars();
  initStatusModal();
})();
