(function () {
    'use strict';

    var filterForm = document.getElementById('filterForm');
    var configNode = document.getElementById('adminAdminsConfig');
    var baseUrl    = configNode ? configNode.getAttribute('data-base-url') : '';

    function bindTableEvents() {
        // Intercept sorting links
        document.querySelectorAll('.tbl th.sortable a').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                loadPage(this.href);
            });
        });

        // Intercept pagination buttons
        document.querySelectorAll('.tbl-pagination .pag-btn:not(.pag-disabled)').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                var href = this.getAttribute('href');
                if (!href || href === '#') return;
                e.preventDefault();
                loadPage(href);
            });
        });

        // Intercept clear button
        var clearBtn = document.querySelector('.app-btn-clear');
        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                e.preventDefault();
                var searchInput = document.getElementById('accountSearchInput');
                if (searchInput) searchInput.value = '';
                var statusSelect = document.getElementById('statusFilterSelect');
                if (statusSelect) statusSelect.value = 'all';
                var roleSelect = document.getElementById('roleFilterSelect');
                if (roleSelect) roleSelect.value = 'all';
                loadPage(this.href);
            });
        }
    }

    var isFetchingPage = false;
    function loadPage(url) {
        if (isFetchingPage) return;
        isFetchingPage = true;

        var tblWrap = document.querySelector('.tbl-wrap');
        if (tblWrap) {
            tblWrap.style.transition = 'opacity 0.15s ease';
            tblWrap.style.opacity = '0.5';
            tblWrap.style.pointerEvents = 'none';
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.text(); })
            .then(function (htmlText) {
                isFetchingPage = false;
                var parser = new DOMParser();
                var doc = parser.parseFromString(htmlText, 'text/html');

                // Swap Toolbar (to update count)
                var newToolbar = doc.querySelector('.toolbar');
                var currentToolbar = document.querySelector('.toolbar');
                if (newToolbar && currentToolbar) {
                    currentToolbar.parentNode.replaceChild(newToolbar, currentToolbar);
                }

                // Swap Stats
                var newStats = doc.querySelector('.grid-stats');
                var currentStats = document.querySelector('.grid-stats');
                if (newStats && currentStats) {
                    currentStats.parentNode.replaceChild(newStats, currentStats);
                }

                // Swap Table Wrap
                var newTblWrap = doc.querySelector('.tbl-wrap');
                var currentTblWrap = document.querySelector('.tbl-wrap');
                if (newTblWrap && currentTblWrap) {
                    currentTblWrap.parentNode.replaceChild(newTblWrap, currentTblWrap);
                }

                // Swap Clear Button in Form
                var newClear = doc.querySelector('.app-btn-clear');
                var currentClear = document.querySelector('.app-btn-clear');
                if (currentClear) {
                    if (newClear) {
                        currentClear.parentNode.replaceChild(newClear, currentClear);
                    } else {
                        currentClear.parentNode.removeChild(currentClear);
                    }
                } else if (newClear) {
                    var formNode = document.getElementById('filterForm');
                    if (formNode) {
                        formNode.appendChild(newClear);
                    }
                }

                // Push new URL state to browser
                history.pushState(null, '', url);

                // Re-bind events on new elements
                bindTableEvents();
            })
            .catch(function () {
                isFetchingPage = false;
                if (tblWrap) {
                    tblWrap.style.opacity = '1';
                    tblWrap.style.pointerEvents = 'auto';
                }
            });
    }

    // Intercept form submit
    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var params = new URLSearchParams(new FormData(this));
            var url = baseUrl + '?' + params.toString();
            loadPage(url);
        });
    }

    // Intercept dropdown status selection changes
    var statusSelect = document.getElementById('statusFilterSelect');
    if (statusSelect) {
        statusSelect.addEventListener('change', function () {
            if (filterForm) {
                filterForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        });
    }

    var roleSelect = document.getElementById('roleFilterSelect');
    if (roleSelect) {
        roleSelect.addEventListener('change', function () {
            if (filterForm) {
                filterForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        });
    }

    // Handle browser back/forward history navigation
    window.addEventListener('popstate', function () {
        loadPage(location.href);
    });

    // Initialize initial load bindings
    bindTableEvents();
})();
