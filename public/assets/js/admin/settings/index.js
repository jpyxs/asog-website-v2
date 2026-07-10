(function () {
  'use strict';

  function bindToggleLabels(root) {
    var scope = root && root.querySelectorAll ? root : document;
    scope.querySelectorAll('[data-toggle-form]').forEach(function (form) {
      form.querySelectorAll('.settings-switch').forEach(function (switchEl) {
        var checkbox = switchEl.querySelector('input[type="checkbox"]');
        var stateLabel = switchEl.querySelector('.settings-switch-label');
        if (!checkbox || !stateLabel || checkbox.dataset.settingsToggleBound === '1') return;

        function updateLabel() {
          stateLabel.textContent = checkbox.checked ? 'ON' : 'OFF';
        }

        checkbox.dataset.settingsToggleBound = '1';
        checkbox.addEventListener('change', updateLabel);
        updateLabel();
      });
    });
  }

  function bindPasswordToggles(root) {
    var scope = root && root.querySelectorAll ? root : document;
    scope.querySelectorAll('[data-settings-password-toggle]').forEach(function (button) {
      var input = document.getElementById(button.getAttribute('aria-controls') || '');
      if (!input || button.dataset.settingsPasswordBound === '1') return;

      button.dataset.settingsPasswordBound = '1';
      button.addEventListener('click', function () {
        var willShow = input.type === 'password';
        input.type = willShow ? 'text' : 'password';
        button.classList.toggle('is-visible', willShow);
        button.setAttribute('aria-pressed', willShow ? 'true' : 'false');
        button.setAttribute('aria-label', willShow ? 'Hide password' : 'Show password');
      });
    });
  }

  function padDatePart(value) {
    return String(value).padStart(2, '0');
  }

  function parseLocalDate(value) {
    var match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(value || ''));
    if (!match) return null;

    var year = Number(match[1]);
    var month = Number(match[2]);
    var day = Number(match[3]);
    var date = new Date(year, month - 1, day);

    if (
      date.getFullYear() !== year ||
      date.getMonth() !== month - 1 ||
      date.getDate() !== day
    ) {
      return null;
    }

    return date;
  }

  function formatDateInputValue(date) {
    return [
      date.getFullYear(),
      padDatePart(date.getMonth() + 1),
      padDatePart(date.getDate())
    ].join('-');
  }

  function formatDateLabel(date) {
    return date.toLocaleDateString('en-US', {
      month: 'long',
      day: 'numeric',
      year: 'numeric'
    });
  }

  function bindDeadlineExtension(root) {
    var scope = root && root.querySelectorAll ? root : document;
    scope.querySelectorAll('[data-deadline-extension]').forEach(function (wrap) {
      if (wrap.dataset.deadlineExtensionBound === '1') return;

      var endDateInput = wrap.querySelector('[data-deadline-end-date]');
      var daysInput = wrap.querySelector('[data-deadline-days]');
      var feedback = wrap.querySelector('[data-deadline-feedback]');
      var presetButtons = wrap.querySelectorAll('[data-deadline-preset]');

      if (!endDateInput || !daysInput || !presetButtons.length) return;

      function setFeedback(message, type) {
        if (!feedback) return;
        feedback.textContent = message || '';
        feedback.classList.toggle('is-error', type === 'error');
        feedback.classList.toggle('is-success', type === 'success');
      }

      function applyExtension(days) {
        var baseDate = parseLocalDate(endDateInput.value);

        if (!endDateInput.value) {
          setFeedback('Set an end date first.', 'error');
          return;
        }

        if (!baseDate) {
          setFeedback('Enter a valid end date.', 'error');
          return;
        }

        if (!Number.isInteger(days) || days < 1 || days > 365) {
          setFeedback('Use 1 to 365 days.', 'error');
          return;
        }

        var nextDate = new Date(baseDate.getFullYear(), baseDate.getMonth(), baseDate.getDate() + days);
        endDateInput.value = formatDateInputValue(nextDate);
        endDateInput.dispatchEvent(new Event('input', { bubbles: true }));
        endDateInput.dispatchEvent(new Event('change', { bubbles: true }));
        setFeedback('Extended to ' + formatDateLabel(nextDate) + '. Save to publish this change.', 'success');
      }

      function applyInputExtension() {
        applyExtension(Number(daysInput.value));
      }

      wrap.dataset.deadlineExtensionBound = '1';

      presetButtons.forEach(function (button) {
        button.addEventListener('click', function () {
          daysInput.value = button.getAttribute('data-deadline-preset') || '7';
          applyInputExtension();
        });
      });

      daysInput.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        applyInputExtension();
      });

      daysInput.addEventListener('input', function () {
        setFeedback('', '');
      });

      endDateInput.addEventListener('input', function () {
        setFeedback('', '');
      });
    });
  }

  function init(root) {
    bindToggleLabels(root);
    bindPasswordToggles(root);
    bindDeadlineExtension(root);

    if (window.AdminLeanCanvas && typeof window.AdminLeanCanvas.init === 'function') {
      return window.AdminLeanCanvas.init(root);
    }

    return function () {};
  }

  if (window.AdminShell && typeof window.AdminShell.register === 'function') {
    window.AdminShell.register('settings', { init: init });
  } else if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { init(document); });
  } else {
    init(document);
  }
})();
