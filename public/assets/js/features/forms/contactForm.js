(function () {
  var form = document.getElementById('contactForm');
  if (!form) return;

  var messageField = form.querySelector('[data-contact-message]');
  var counterEl = form.querySelector('[data-contact-counter]');
  var fieldMap = {
    name: form.querySelector('input[name="name"]'),
    email: form.querySelector('input[name="email"]'),
    message: messageField,
  };
  var minWords = 10;
  var maxWords = 5000;
  var allowNativeSubmit = false;
  var recaptchaEnabled = form.dataset.recaptchaEnabled === '1';
  var recaptchaSiteKey = form.dataset.recaptchaSiteKey || '';
  var recaptchaScriptUrl = form.dataset.recaptchaScriptUrl || '';
  var recaptchaAction = form.dataset.recaptchaAction || 'contact_send';
  var recaptchaTokenField = form.querySelector('[data-recaptcha-token]');
  var submitButton = form.querySelector('button[type="submit"]');

  function normalizeScriptUrl(src) {
    try {
      return new URL(src, window.location.href).href;
    } catch (error) {
      return src;
    }
  }

  function findScriptBySrc(src) {
    var normalized = normalizeScriptUrl(src);
    var scripts = document.getElementsByTagName('script');
    for (var i = 0; i < scripts.length; i++) {
      if (scripts[i].src === normalized) {
        return scripts[i];
      }
    }
    return null;
  }

  function loadRecaptchaScript() {
    if (!recaptchaEnabled) {
      return Promise.resolve(null);
    }

    if (window.grecaptcha && window.grecaptcha.enterprise) {
      return Promise.resolve(window.grecaptcha);
    }

    if (!recaptchaScriptUrl) {
      return Promise.reject(new Error('recaptcha_unavailable'));
    }

    var normalizedSrc = normalizeScriptUrl(recaptchaScriptUrl);
    if (window.ASOGRecaptchaLoader && window.ASOGRecaptchaLoader.src === normalizedSrc) {
      return window.ASOGRecaptchaLoader.promise;
    }

    var promise = new Promise(function (resolve, reject) {
      var existing = findScriptBySrc(recaptchaScriptUrl);
      if (existing) {
        existing.addEventListener('load', function () { resolve(window.grecaptcha || null); }, { once: true });
        existing.addEventListener('error', function () {
          existing.remove();
          reject(new Error('recaptcha_unavailable'));
        }, { once: true });
        return;
      }

      var script = document.createElement('script');
      script.src = recaptchaScriptUrl;
      script.async = true;
      script.onload = function () { resolve(window.grecaptcha || null); };
      script.onerror = function () {
        script.remove();
        reject(new Error('recaptcha_unavailable'));
      };
      document.head.appendChild(script);
    });

    window.ASOGRecaptchaLoader = {
      src: normalizedSrc,
      promise: promise.catch(function (error) {
        if (window.ASOGRecaptchaLoader && window.ASOGRecaptchaLoader.src === normalizedSrc) {
          window.ASOGRecaptchaLoader = null;
        }
        throw error;
      }),
    };

    return window.ASOGRecaptchaLoader.promise;
  }

  function warmRecaptcha() {
    if (!recaptchaEnabled) {
      return;
    }
    loadRecaptchaScript().catch(function () {});
  }

  function escHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str || '')));
    return div.innerHTML;
  }

  function getErrorEl(field) {
    if (!field) return null;
    var wrapper = field.closest('div');
    if (field.name === 'message' && wrapper) {
      var messageError = wrapper.parentElement ? wrapper.parentElement.querySelector('[data-contact-message-error]') : null;
      if (messageError) {
        return messageError;
      }
    }
    return wrapper ? wrapper.querySelector('.contact-field-error') : null;
  }

  function getErrorTarget(field) {
    if (!field) return null;
    if (field.name === 'message') {
      return field.closest('[data-contact-message-frame]') || field;
    }
    return field;
  }

  function setError(field, message) {
    var errorEl = getErrorEl(field);
    var target = getErrorTarget(field);
    if (!errorEl) return;
    if (!message) {
      errorEl.textContent = '';
      errorEl.classList.add('hidden');
      if (target) {
        target.classList.remove('border-red-500', 'ring-1', 'ring-red-200');
      }
      return;
    }
    errorEl.textContent = message;
    errorEl.classList.remove('hidden');
    if (target) {
      target.classList.add('border-red-500', 'ring-1', 'ring-red-200');
    }
  }

  function updateCounter() {
    if (!messageField || !counterEl) return;
    var words = countWords(messageField.value);
    counterEl.textContent = words + '/' + maxWords;
    counterEl.style.display = 'inline-block';
    counterEl.style.padding = '0';
    counterEl.style.borderRadius = '2px';
    counterEl.style.background = 'transparent';
    counterEl.style.color = 'rgba(2,13,24,.6)';
    counterEl.style.fontSize = '10px';
    counterEl.style.lineHeight = '1';
    counterEl.style.fontWeight = '500';
    counterEl.style.letterSpacing = '.08em';
    counterEl.style.boxShadow = 'none';
  }

  function countWords(value) {
    var text = String(value || '').trim();
    if (!text) return 0;
    return text.split(/\s+/).filter(Boolean).length;
  }

  function validateField(field) {
    if (!field) return true;
    var value = String(field.value || '').trim();

    if (field.hasAttribute('required') && value === '') {
      setError(field, 'This field is required.');
      return false;
    }

    if (field.name === 'email' && value !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
      setError(field, 'Please enter a valid email address.');
      return false;
    }

    if (field.name === 'message') {
      if (value === '') {
        setError(field, 'Message is required.');
        updateCounter();
        return false;
      }

      var words = countWords(value);
      if (words < minWords) {
        setError(field, 'Message must be at least ' + minWords + ' words.');
        updateCounter();
        return false;
      }

      if (words > maxWords) {
        setError(field, 'Message cannot exceed ' + maxWords + ' words.');
        updateCounter();
        return false;
      }
    }

    setError(field, '');
    return true;
  }

  function validateForm() {
    var ok = true;

    Object.keys(fieldMap).forEach(function (key) {
      if (!validateField(fieldMap[key])) {
        ok = false;
      }
    });

    Object.keys(fieldMap).forEach(function (key) {
      var field = fieldMap[key];
      if (!field) return;
      if (field.name !== 'message' && field.value.trim() === '') {
        setError(field, 'This field is required.');
      }
    });

    if (!ok) {
      var firstError = form.querySelector('.contact-field-error:not(.hidden)');
      if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }

    return ok;
  }

  function submitNative() {
    allowNativeSubmit = true;
    if (window.HTMLFormElement && HTMLFormElement.prototype.submit) {
      HTMLFormElement.prototype.submit.call(form);
      return;
    }
    form.submit();
  }

  function setSubmitting(isSubmitting) {
    if (!submitButton) return;
    submitButton.disabled = isSubmitting;
    submitButton.style.opacity = isSubmitting ? '0.72' : '';
    submitButton.style.cursor = isSubmitting ? 'wait' : '';
  }

  function getRecaptchaToken() {
    if (!recaptchaEnabled) {
      return Promise.resolve('');
    }

    if (!recaptchaSiteKey) {
      return Promise.reject(new Error('recaptcha_unavailable'));
    }

    return loadRecaptchaScript().then(function () {
      if (!window.grecaptcha || !window.grecaptcha.enterprise) {
        return Promise.reject(new Error('recaptcha_unavailable'));
      }

      return new Promise(function (resolve, reject) {
        window.grecaptcha.enterprise.ready(function () {
          window.grecaptcha.enterprise.execute(recaptchaSiteKey, { action: recaptchaAction })
            .then(resolve)
            .catch(reject);
        });
      });
    });
  }

  if (messageField) {
    updateCounter();
    messageField.addEventListener('input', function () {
      updateCounter();
      validateField(messageField);
    });
    messageField.addEventListener('blur', function () {
      updateCounter();
      validateField(messageField);
    });
  }

  Object.keys(fieldMap).forEach(function (key) {
    var field = fieldMap[key];
    if (!field || field === messageField) return;
    field.addEventListener('input', function () {
      validateField(field);
    });
    field.addEventListener('blur', function () {
      validateField(field);
    });
  });

  form.addEventListener('focusin', warmRecaptcha, { once: true });
  form.addEventListener('pointerdown', warmRecaptcha, { once: true, passive: true });
  form.addEventListener('keydown', warmRecaptcha, { once: true });
  form.addEventListener('input', warmRecaptcha, { once: true });

  form.addEventListener('submit', function (event) {
    if (allowNativeSubmit) {
      allowNativeSubmit = false;
      return;
    }

    event.preventDefault();
    updateCounter();
    if (!validateForm()) {
      return;
    }

    setSubmitting(true);
    getRecaptchaToken()
      .then(function (token) {
        if (recaptchaTokenField) {
          recaptchaTokenField.value = token;
        }
        submitNative();
      })
      .catch(function () {
        setError(messageField, 'We could not verify your submission. Please refresh the page and try again.');
        if (messageField) {
          messageField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        setSubmitting(false);
      });
  });
})();
