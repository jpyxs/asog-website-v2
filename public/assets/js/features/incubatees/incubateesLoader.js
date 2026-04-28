(function () {
  var script = document.currentScript;
  if (!script) return;

  var apiUrl = script.getAttribute('data-api-url');
  if (!apiUrl) return;

  var cohort = script.getAttribute('data-cohort');
  var appScript = script.getAttribute('data-app-script') || '/assets/js/incubatees.js';

  function buildApiUrl() {
    if (!cohort) return apiUrl;

    var separator = apiUrl.indexOf('?') === -1 ? '?' : '&';
    return apiUrl + separator + 'cohort=' + encodeURIComponent(cohort);
  }

  function loadAppScript() {
    var js = document.createElement('script');
    js.src = appScript;
    js.defer = true;
    document.body.appendChild(js);
  }

  fetch(buildApiUrl(), {
    headers: { Accept: 'application/json' },
    credentials: 'same-origin'
  })
    .then(function (response) {
      if (!response.ok) throw new Error('Failed to load incubatee data');
      return response.json();
    })
    .then(function (payload) {
      window.__ibData = Array.isArray(payload.data) ? payload.data : [];
    })
    .catch(function () {
      window.__ibData = [];
    })
    .finally(loadAppScript);
})();
