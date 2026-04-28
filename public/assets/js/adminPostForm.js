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

    var nextSrc = (base ? base : '') + '/assets/js/admin/posts/form.js';
    if (document.querySelector('script[data-compat-loader="adminPostForm"]')) {
        return;
    }

    var script = document.createElement('script');
    script.src = nextSrc;
    script.defer = true;
    script.setAttribute('data-compat-loader', 'adminPostForm');
    document.head.appendChild(script);
})();