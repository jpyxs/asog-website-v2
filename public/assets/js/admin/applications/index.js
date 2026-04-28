/**
 * adminApplications.js
 * --------------------
 * Review modal, themed confirm dialog, CV preview cards,
 * status change workflow for the admin Applications page.
 */
(function () {
    'use strict';

    /* ── DOM refs ──────────────────────────── */
    var modalBg    = document.getElementById('reviewModal');
    var modalBody  = document.getElementById('modalBody');
    var modalFoot  = document.getElementById('modalFoot');
    var modalTitle = document.getElementById('modalTitle');
    var btnClose   = document.getElementById('modalClose');
    var btnAccept  = document.getElementById('btnAccept');
    var btnReject  = document.getElementById('btnReject');
    var btnChange  = document.getElementById('btnChange');
    var statusSelect = document.getElementById('statusSelect');
    var btnSaveStatus = document.getElementById('btnSaveStatus');

    /* Confirm dialog refs */
    var confirmBg     = document.getElementById('confirmDialog');
    var confirmIcon   = document.getElementById('confirmIcon');
    var confirmSvg    = document.getElementById('confirmSvg');
    var confirmTitle  = document.getElementById('confirmTitle');
    var confirmMsg    = document.getElementById('confirmMsg');
    var confirmOk     = document.getElementById('confirmOk');
    var confirmCancel = document.getElementById('confirmCancel');

    var currentId      = null;
    var confirmCb      = null;
    var currentAppData = null;

    /* ══════════════════════════════════════════
       SECTION 1 — Universal Confirm Dialog
       ══════════════════════════════════════════ */

    function showConfirm(opts) {
        confirmTitle.textContent = opts.title || 'Are you sure?';
        confirmMsg.textContent   = opts.message || '';

        confirmIcon.className = 'confirm-icon ' + (opts.color || 'green');
        if (opts.icon === 'x') {
            confirmSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>';
        } else {
            confirmSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>';
        }

        confirmOk.className = 'c-ok ' + (opts.color || 'green');
        confirmCb = opts.onConfirm || null;
        confirmBg.classList.add('open');
    }

    function hideConfirm() {
        confirmBg.classList.remove('open');
        confirmCb = null;
    }

    confirmCancel.addEventListener('click', hideConfirm);
    confirmOk.addEventListener('click', function () {
        if (typeof confirmCb === 'function') confirmCb();
        hideConfirm();
    });
    confirmBg.addEventListener('click', function (e) {
        if (e.target === confirmBg) hideConfirm();
    });

    /* ══════════════════════════════════════════
       SECTION 2 — Review Modal
       ══════════════════════════════════════════ */

    document.querySelectorAll('.btn-review').forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentId = this.dataset.id;
            openModal(currentId);
        });
    });

    function openModal(id) {
        modalBody.innerHTML = '<p style="color:#94a3b8;font-size:.78rem;padding:2rem 0;text-align:center">Loading\u2026</p>';
        hideChangeMode();
        modalBg.classList.add('open');

        fetch(siteUrl('admin/applications/' + id), {
            headers: { 'Accept': 'application/json' }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.error) {
                modalBody.innerHTML = '<p style="color:#991b1b;font-size:.78rem;padding:1rem">' + escHtml(data.error) + '</p>';
                hideAllFooterButtons();
                return;
            }
            currentAppData = data;
            renderModal(data);
        })
        .catch(function () {
            modalBody.innerHTML = '<p style="color:#991b1b;font-size:.78rem;padding:1rem">Failed to load application.</p>';
            hideAllFooterButtons();
        });
    }

    /* ── Render fields ─────────────────────── */
    function renderModal(d) {
        modalTitle.textContent = d.startupName || 'Application Review';

        var html = '<div class="field-grid">';
        html += field('Applicant', escHtml(d.applicantName));
        html += field('Email', '<a href="mailto:' + escHtml(d.applicantEmail) + '">' + escHtml(d.applicantEmail) + '</a>');
        html += field('Contact', escHtml(d.contactNumber));
        html += field('Startup', escHtml(d.startupName));
        html += '</div>';

        html += '<div class="modal-divider"></div>';
        html += field('Description', escHtml(d.startupDescription));
        html += field('Main Risk', escHtml(d.mainRisk) || '\u2014');
        html += field('Short-term Goals', escHtml(d.shortTermGoals) || '\u2014');

        html += field('Video Presentation', d.videoPresentationLink
            ? '<a href="' + escHtml(d.videoPresentationLink) + '" target="_blank" rel="noopener">' + truncateUrl(d.videoPresentationLink) + '</a>'
            : '\u2014');

        /* CV file cards */
        if (d.teamCvPath) {
            var cards = d.teamCvPath.split(',').map(function (p, i) {
                var trimmed = p.trim();
                if (!trimmed) return '';
                var filename = trimmed.split('/').pop();
                var url = siteUrl('uploads/applications/' + filename);
                return '<a class="file-card" href="' + escHtml(url) + '" target="_blank" rel="noopener" title="Open ' + escHtml(filename) + '">'
                     + '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">'
                     + '<path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>'
                     + '</svg>'
                     + '<span>CV ' + (i + 1) + '</span>'
                     + '</a>';
            }).join('');
            html += '<div class="field"><div class="field-label">Team CVs</div><div class="file-cards">' + cards + '</div></div>';
        }

        /* Lean canvas card */
        if (d.leanCanvasPath) {
            var lcFilename = d.leanCanvasPath.split('/').pop();
            var lcUrl = siteUrl('uploads/applications/' + lcFilename);
            var lcCard = '<a class="file-card" href="' + escHtml(lcUrl) + '" target="_blank" rel="noopener" title="Open ' + escHtml(lcFilename) + '">'
                       + '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">'
                       + '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
                       + '</svg>'
                       + '<span>Lean Canvas</span>'
                       + '</a>';
            html += '<div class="field"><div class="field-label">Lean Canvas</div><div class="file-cards">' + lcCard + '</div></div>';
        }

        html += '<div class="modal-divider"></div>';

        html += '<div class="field-grid">';
        html += field('Status', '<span class="tag tag-' + escHtml(d.applicationStatus) + '">' + statusLabel(d.applicationStatus) + '</span>');
        html += field('Submitted', formatDate(d.createdAt));
        html += '</div>';

        modalBody.innerHTML = html;
        updateFooterButtons(d.applicationStatus);
    }

    /* ── Footer button state management ────── */
    function updateFooterButtons(status) {
        hideChangeMode();

        if (status === 'pending' || status === 'reviewed') {
            // Show accept + reject
            btnAccept.style.display = 'inline-flex';
            btnReject.style.display = 'inline-flex';
            btnChange.style.display = 'none';
        } else {
            // Already decided — show "Change Status" button
            btnAccept.style.display = 'none';
            btnReject.style.display = 'none';
            btnChange.style.display = 'inline-flex';
        }
    }

    function hideAllFooterButtons() {
        btnAccept.style.display = 'none';
        btnReject.style.display = 'none';
        btnChange.style.display = 'none';
        hideChangeMode();
    }

    function hideChangeMode() {
        statusSelect.style.display = 'none';
        btnSaveStatus.style.display = 'none';
    }

    /* ── Close modal ───────────────────────── */
    btnClose.addEventListener('click', closeModal);
    modalBg.addEventListener('click', function (e) {
        if (e.target === modalBg) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (confirmBg.classList.contains('open')) { hideConfirm(); return; }
            if (modalBg.classList.contains('open'))   { closeModal(); }
        }
    });

    function closeModal() {
        modalBg.classList.remove('open');
        currentId = null;
        currentAppData = null;
    }

    /* ══════════════════════════════════════════
       SECTION 3 — Accept / Reject / Change
       ══════════════════════════════════════════ */

    btnAccept.addEventListener('click', function () {
        showConfirm({
            title:   'Accept Application',
            message: 'This will mark the application as accepted. Continue?',
            color:   'green',
            icon:    'check',
            onConfirm: function () { sendStatus('accepted'); }
        });
    });

    btnReject.addEventListener('click', function () {
        showConfirm({
            title:   'Reject Application',
            message: 'This will mark the application as rejected. Continue?',
            color:   'red',
            icon:    'x',
            onConfirm: function () { sendStatus('rejected'); }
        });
    });

    /* Change Status flow — toggle select + save button */
    btnChange.addEventListener('click', function () {
        btnChange.style.display = 'none';
        statusSelect.style.display = 'inline-block';
        btnSaveStatus.style.display = 'inline-flex';
        // Pre-select current status
        if (currentAppData) {
            statusSelect.value = currentAppData.applicationStatus;
        }
    });

    btnSaveStatus.addEventListener('click', function () {
        var newStatus = statusSelect.value;
        if (!newStatus || !currentId) return;

        if (newStatus === currentAppData.applicationStatus) {
            hideChangeMode();
            btnChange.style.display = 'inline-flex';
            return;
        }

        var color = (newStatus === 'rejected') ? 'red' : 'green';
        var icon  = (newStatus === 'rejected') ? 'x' : 'check';

        showConfirm({
            title:   'Change Status',
            message: 'Change this application to "' + statusLabel(newStatus) + '"?',
            color:   color,
            icon:    icon,
            onConfirm: function () { sendStatus(newStatus); }
        });
    });

    function sendStatus(status) {
        if (!currentId) return;

        fetch(siteUrl('admin/applications/' + currentId + '/status'), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                /* Update table row */
                var row = document.querySelector('tr[data-id="' + currentId + '"]');
                if (row) {
                    var tag = row.querySelector('.tag');
                    tag.className = 'tag tag-' + status;
                    tag.textContent = statusLabel(status);
                }

                /* Update modal */
                if (currentAppData) {
                    currentAppData.applicationStatus = status;
                    renderModal(currentAppData);
                }
            } else {
                showConfirm({
                    title:   'Error',
                    message: data.error || 'Something went wrong. Please try again.',
                    color:   'red',
                    icon:    'x',
                    onConfirm: function () {}
                });
            }
        })
        .catch(function () {
            showConfirm({
                title:   'Network Error',
                message: 'Could not reach the server. Please try again.',
                color:   'red',
                icon:    'x',
                onConfirm: function () {}
            });
        });
    }

    /* ══════════════════════════════════════════
       SECTION 4 — Helpers
       ══════════════════════════════════════════ */

    function field(label, value) {
        return '<div class="field">'
             + '<div class="field-label">' + label + '</div>'
             + '<div class="field-value">' + (value || '\u2014') + '</div>'
             + '</div>';
    }

    function escHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function capitalize(s) {
        return s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
    }

    function statusLabel(s) {
        var map = { pending: 'Under Review', reviewed: 'Under Review', accepted: 'Accepted', rejected: 'Rejected' };
        return map[s] || capitalize(s);
    }

    function formatDate(iso) {
        if (!iso) return '\u2014';
        var d = new Date(iso);
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
    }

    function truncateUrl(url) {
        if (!url) return '';
        return url.length > 50 ? url.substring(0, 47) + '\u2026' : url;
    }

    function siteUrl(path) {
        var base = window.APP_BASE_URL || window.location.origin;
        return base.replace(/\/$/, '') + '/' + path;
    }
})();
