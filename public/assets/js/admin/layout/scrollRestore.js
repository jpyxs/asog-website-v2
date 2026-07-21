(function () {
    'use strict';

    var STORAGE_KEY = 'adminScrollY';
    var savedY = null;

    try {
        var stored = sessionStorage.getItem(STORAGE_KEY);
        if (stored !== null) {
            savedY = parseInt(stored, 10) || 0;
        }
    } catch (e) { }

    function applyScroll(behavior) {
        if (savedY === null) return;
        window.scrollTo({ top: savedY, left: 0, behavior: behavior || 'auto' });
    }

    applyScroll();

    window.addEventListener('load', function () {
        applyScroll('smooth');
        try {
            sessionStorage.removeItem(STORAGE_KEY);
        } catch (e) { }
    });

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form || !form.matches || !form.matches('[data-preserve-scroll]')) return;
        try {
            sessionStorage.setItem(STORAGE_KEY, String(window.scrollY));
        } catch (e) { }
    }, true);
})();