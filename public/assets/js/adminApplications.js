/* Compatibility loader for legacy path. */
(function () {
    var current = document.currentScript;
    var src = current && current.src ? current.src : '';
    var base = '';

    if (src) {
        var marker = '/assets/js/';
        var idx = src.indexOf(marker);
        base = idx >= 0 ? src.substring(0, idx) : '';
    }

    var nextSrc = (base ? base : '') + '/assets/js/admin/applications/index.js';
    if (document.querySelector('script[data-compat-loader="adminApplications"]')) {
        return;
    }

    var script = document.createElement('script');
    script.src = nextSrc;
    script.defer = true;
    script.setAttribute('data-compat-loader', 'adminApplications');
    document.head.appendChild(script);
})();
