(function () {
    'use strict';

    /* ── DOM refs ──────────────────────────────────────────── */
    var modalBg         = document.getElementById('reviewModal');
    var modalBody       = document.getElementById('modalBody');
    var modalTitle      = document.getElementById('modalTitle');
    var btnClose        = document.getElementById('modalClose');
    var btnAccept       = document.getElementById('btnAccept');
    var btnReject       = document.getElementById('btnReject');
    var btnChange       = document.getElementById('btnChange');
    var statusSelect    = document.getElementById('statusSelect');
    var btnSaveStatus   = document.getElementById('btnSaveStatus');
    var btnArchModal    = document.getElementById('btnArchModal');
    var btnRestoreModal = document.getElementById('btnRestoreModal');

    var confirmBg     = document.getElementById('confirmDialog');
    var confirmIcon   = document.getElementById('confirmIcon');
    var confirmSvg    = document.getElementById('confirmSvg');
    var confirmTitle  = document.getElementById('confirmTitle');
    var confirmMsg    = document.getElementById('confirmMsg');
    var confirmOk     = document.getElementById('confirmOk');
    var confirmCancel = document.getElementById('confirmCancel');

    var selectAll       = document.getElementById('selectAll');
    var smartCaretBtn   = document.getElementById('smartCaretBtn');
    var smartChkMenu    = document.getElementById('smartChkMenu');
    var filterForm      = document.getElementById('filterForm');
    var bulkActionsBar  = document.getElementById('bulkActionsBar');
    var bulkCount       = document.getElementById('bulkCount');

    var currentId      = null;
    var confirmCb      = null;
    var currentAppData = null;

    /* ══════════════════════════════════════════════════
       SECTION 1 — Universal Confirm Dialog
       ══════════════════════════════════════════════════ */

    function showConfirm(opts) {
        confirmTitle.textContent = opts.title   || 'Are you sure?';
        confirmMsg.textContent   = opts.message || '';
        confirmIcon.className    = 'confirm-icon ' + (opts.color || 'green');

        confirmSvg.innerHTML = opts.icon === 'x'
            ? '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>';

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

    /* ══════════════════════════════════════════════════
       SECTION 2 — Review Modal
       ══════════════════════════════════════════════════ */

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

        fetch(siteUrl('admin/applications/' + id), { headers: { 'Accept': 'application/json' } })
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

        if (d.teamCvPath) {
            var cards = d.teamCvPath.split(',').map(function (p, i) {
                var trimmed = p.trim();
                if (!trimmed) return '';
                var filename = trimmed.split('/').pop();
                var url = siteUrl('uploads/applications/' + filename);
                return '<a class="file-card" href="' + escHtml(url) + '" target="_blank" rel="noopener" title="Open ' + escHtml(filename) + '">'
                     + '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>'
                     + '<span>CV ' + (i + 1) + '</span></a>';
            }).join('');
            html += '<div class="field"><div class="field-label">Team CVs</div><div class="file-cards">' + cards + '</div></div>';
        }

        if (d.leanCanvasPath) {
            var lcFilename = d.leanCanvasPath.split('/').pop();
            var lcUrl = siteUrl('uploads/applications/' + lcFilename);
            var lcCard = '<a class="file-card" href="' + escHtml(lcUrl) + '" target="_blank" rel="noopener" title="Open ' + escHtml(lcFilename) + '">'
                       + '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                       + '<span>Lean Canvas</span></a>';
            html += '<div class="field"><div class="field-label">Lean Canvas</div><div class="file-cards">' + lcCard + '</div></div>';
        }

        html += '<div class="modal-divider"></div>';
        html += '<div class="field-grid">';
        html += field('Status', '<span class="tag tag-' + escHtml(d.applicationStatus) + '">' + statusLabel(d.applicationStatus) + '</span>');
        html += field('Submitted', formatDate(d.createdAt));
        html += '</div>';

        modalBody.innerHTML = html;
        updateFooterButtons(d.applicationStatus, d.isArchived);
    }

    /* ── Footer button state ────────────────────────────── */
    function updateFooterButtons(status, isArchived) {
        hideChangeMode();

        var archived = (Number(isArchived) === 1);

        if (archived) {
            btnAccept.style.display  = 'none';
            btnReject.style.display  = 'none';
            btnChange.style.display  = 'none';
            if (btnArchModal)    btnArchModal.style.display    = 'none';
            if (btnRestoreModal) btnRestoreModal.style.display = 'inline-flex';
        } else if (status === 'pending' || status === 'reviewed') {
            btnAccept.style.display  = 'inline-flex';
            btnReject.style.display  = 'inline-flex';
            btnChange.style.display  = 'none';
            if (btnArchModal)    btnArchModal.style.display    = 'inline-flex';
            if (btnRestoreModal) btnRestoreModal.style.display = 'none';
        } else {
            btnAccept.style.display  = 'none';
            btnReject.style.display  = 'none';
            btnChange.style.display  = 'inline-flex';
            if (btnArchModal)    btnArchModal.style.display    = 'inline-flex';
            if (btnRestoreModal) btnRestoreModal.style.display = 'none';
        }
    }

    function hideAllFooterButtons() {
        btnAccept.style.display  = 'none';
        btnReject.style.display  = 'none';
        btnChange.style.display  = 'none';
        if (btnArchModal)    btnArchModal.style.display    = 'none';
        if (btnRestoreModal) btnRestoreModal.style.display = 'none';
        hideChangeMode();
    }

    function hideChangeMode() {
        statusSelect.style.display  = 'none';
        btnSaveStatus.style.display = 'none';
    }

    /* ── Close modal ──────────────────────────────────── */
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
        currentId      = null;
        currentAppData = null;
    }

    /* ══════════════════════════════════════════════════
       SECTION 3 — Accept / Reject / Change Status
       ══════════════════════════════════════════════════ */

    btnAccept.addEventListener('click', function () {
        showConfirm({
            title:     'Accept Application',
            message:   'This will mark the application as accepted. Continue?',
            color:     'green',
            icon:      'check',
            onConfirm: function () { sendStatus('accepted'); }
        });
    });

    btnReject.addEventListener('click', function () {
        showConfirm({
            title:     'Reject Application',
            message:   'This will mark the application as rejected. Continue?',
            color:     'red',
            icon:      'x',
            onConfirm: function () { sendStatus('rejected'); }
        });
    });

    btnChange.addEventListener('click', function () {
        btnChange.style.display     = 'none';
        statusSelect.style.display  = 'inline-block';
        btnSaveStatus.style.display = 'inline-flex';
        if (currentAppData) statusSelect.value = currentAppData.applicationStatus;
    });

    btnSaveStatus.addEventListener('click', function () {
        var newStatus = statusSelect.value;
        if (!newStatus || !currentId) return;

        if (newStatus === (currentAppData && currentAppData.applicationStatus)) {
            hideChangeMode();
            btnChange.style.display = 'inline-flex';
            return;
        }

        showConfirm({
            title:     'Change Status',
            message:   'Change this application to "' + statusLabel(newStatus) + '"?',
            color:     newStatus === 'rejected' ? 'red' : 'green',
            icon:      newStatus === 'rejected' ? 'x'   : 'check',
            onConfirm: function () { sendStatus(newStatus); }
        });
    });

    function sendStatus(status) {
        if (!currentId) return;

        fetch(siteUrl('admin/applications/' + currentId + '/status'), {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ status: status })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                var row = document.querySelector('tr[data-id="' + currentId + '"]');
                if (row) {
                    var tag = row.querySelector('.tag:not(.tag-archived)');
                    if (tag) { tag.className = 'tag tag-' + status; tag.textContent = statusLabel(status); }
                    var chk = row.querySelector('.row-select');
                    if (chk) chk.dataset.status = status;
                }
                if (currentAppData) {
                    currentAppData.applicationStatus = status;
                    renderModal(currentAppData);
                }
            } else {
                showConfirm({ title: 'Error', message: data.error || 'Something went wrong.', color: 'red', icon: 'x', onConfirm: function () {} });
            }
        })
        .catch(function () {
            showConfirm({ title: 'Network Error', message: 'Could not reach the server.', color: 'red', icon: 'x', onConfirm: function () {} });
        });
    }

    /* ══════════════════════════════════════════════════
       SECTION 4 — Archive / Restore
       ══════════════════════════════════════════════════ */

    if (btnArchModal) {
        btnArchModal.addEventListener('click', function () {
            if (!currentId) return;
            showConfirm({
                title:     'Archive Application',
                message:   'This application will be moved to the archive. Continue?',
                color:     'red',
                icon:      'x',
                onConfirm: function () { sendToggleArchive(currentId); }
            });
        });
    }

    if (btnRestoreModal) {
        btnRestoreModal.addEventListener('click', function () {
            if (!currentId) return;
            showConfirm({
                title:     'Restore Application',
                message:   'This will restore the application to active status. Continue?',
                color:     'green',
                icon:      'check',
                onConfirm: function () { sendToggleArchive(currentId); }
            });
        });
    }

    document.querySelectorAll('.btn-arch-row').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var id         = this.dataset.id;
            var isArchived = this.closest('tr').dataset.archived === '1';
            showConfirm({
                title:     isArchived ? 'Restore Application' : 'Archive Application',
                message:   isArchived ? 'Restore this application to active status?' : 'Move this application to the archive?',
                color:     isArchived ? 'green' : 'red',
                icon:      isArchived ? 'check'  : 'x',
                onConfirm: function () { sendToggleArchive(id); }
            });
        });
    });

    function sendToggleArchive(id) {
        fetch(siteUrl('admin/applications/' + id + '/toggle-archive'), {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                var row = document.querySelector('tr[data-id="' + id + '"]');
                if (row) {
                    row.dataset.archived = data.isArchived;

                    var tagCell = row.querySelector('td:nth-child(6)');
                    if (tagCell) {
                        var archTag = tagCell.querySelector('.tag-archived');
                        if (data.isArchived && !archTag) {
                            var span = document.createElement('span');
                            span.className   = 'tag tag-archived';
                            span.textContent = 'Archived';
                            tagCell.appendChild(span);
                        } else if (!data.isArchived && archTag) {
                            archTag.remove();
                        }
                    }

                    var archBtn = row.querySelector('.btn-arch-row');
                    if (archBtn) {
                        archBtn.title = data.isArchived ? 'Restore' : 'Archive';
                        archBtn.innerHTML = data.isArchived
                            ? '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>'
                            : '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>';
                    }

                    var chk = row.querySelector('.row-select');
                    if (chk) chk.dataset.isArchived = data.isArchived;

                    var filterSel = document.querySelector('.app-select-filter');
                    var curFilter = filterSel ? filterSel.value : 'active';
                    if ((curFilter === 'active' && data.isArchived) || (curFilter === 'archived' && !data.isArchived)) {
                        row.style.transition = 'opacity .3s';
                        row.style.opacity    = '0';
                        setTimeout(function () {
                            row.remove();
                            if (!document.querySelector('.tbl tbody tr')) window.location.reload();
                        }, 300);
                    }
                }

                if (currentAppData && currentId === id) {
                    currentAppData.isArchived = data.isArchived;
                    renderModal(currentAppData);
                }
            } else {
                showConfirm({ title: 'Error', message: data.error || 'Something went wrong.', color: 'red', icon: 'x', onConfirm: function () {} });
            }
        })
        .catch(function () {
            showConfirm({ title: 'Network Error', message: 'Could not reach the server.', color: 'red', icon: 'x', onConfirm: function () {} });
        });
    }

    /* ══════════════════════════════════════════════════
       SECTION 5 — Bulk Selection & Direct Action Buttons
       ══════════════════════════════════════════════════ */

    function refreshBulkBar() {
        var checked      = document.querySelectorAll('.row-select:checked');
        var total        = document.querySelectorAll('.row-select').length;
        var hasSelection = checked.length > 0;

        if (bulkCount) bulkCount.textContent = checked.length;

        if (filterForm)     filterForm.classList.toggle('search-hidden', hasSelection);
        if (bulkActionsBar) bulkActionsBar.classList.toggle('bulk-visible', hasSelection);

        if (selectAll) {
            selectAll.indeterminate = hasSelection && checked.length < total;
            selectAll.checked = total > 0 && checked.length === total;
        }

        var hasNonArchivedNonPending  = false;
        var hasNonArchivedNonAccepted = false;
        var hasNonArchivedNonRejected = false;
        var hasNonArchived            = false;
        var hasArchived               = false;

        checked.forEach(function (chk) {
            var status     = chk.dataset.status;
            var isArchived = chk.dataset.isArchived === '1';
            if (isArchived) {
                hasArchived = true;
            } else {
                hasNonArchived = true;
                if (status !== 'pending')  hasNonArchivedNonPending  = true;
                if (status !== 'accepted') hasNonArchivedNonAccepted = true;
                if (status !== 'rejected') hasNonArchivedNonRejected = true;
            }
        });

        document.querySelectorAll('.bulk-act-btn').forEach(function (btn) {
            var action = btn.dataset.action;
            var shouldDisable = false;
            if (action === 'pending')   shouldDisable = !hasNonArchivedNonPending;
            if (action === 'accepted')  shouldDisable = !hasNonArchivedNonAccepted;
            if (action === 'rejected')  shouldDisable = !hasNonArchivedNonRejected;
            if (action === 'archive')   shouldDisable = !hasNonArchived;
            if (action === 'unarchive') shouldDisable = !hasArchived;
            btn.disabled = shouldDisable;
        });
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            var isChecked = selectAll.checked;
            document.querySelectorAll('.row-select').forEach(function (chk) {
                chk.checked = isChecked;
                var row = chk.closest('tr');
                if (row) row.classList.toggle('row-selected', isChecked);
            });
            refreshBulkBar();
        });
    }

    document.querySelectorAll('.row-select').forEach(function (chk) {
        chk.addEventListener('change', function () {
            var row = this.closest('tr');
            if (row) row.classList.toggle('row-selected', this.checked);
            refreshBulkBar();
        });
    });

    if (smartCaretBtn && smartChkMenu) {
        smartCaretBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            smartChkMenu.classList.toggle('open');
        });

        document.addEventListener('click', function () {
            if (smartChkMenu) smartChkMenu.classList.remove('open');
        });

        smartChkMenu.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        document.querySelectorAll('.scm-item').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var type = this.dataset.smart;
                document.querySelectorAll('.row-select').forEach(function (chk) {
                    var shouldCheck;
                    if (type === 'all') {
                        shouldCheck = true;
                    } else if (type === 'none') {
                        shouldCheck = false;
                    } else if (type === 'archived') {
                        shouldCheck = chk.dataset.isArchived === '1';
                    } else {
                        shouldCheck = (chk.dataset.status === type && chk.dataset.isArchived !== '1');
                    }
                    chk.checked = shouldCheck;
                    var row = chk.closest('tr');
                    if (row) row.classList.toggle('row-selected', shouldCheck);
                });
                refreshBulkBar();
                smartChkMenu.classList.remove('open');
            });
        });
    }

    document.querySelectorAll('.bulk-act-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var action = this.dataset.action;
            if (!action) return;

            var checked = document.querySelectorAll('.row-select:checked');
            var ids = Array.prototype.map.call(checked, function (chk) { return chk.dataset.id; });
            if (!ids.length) return;

            var label = this.textContent.trim();
            var isDestructive = action === 'rejected' || action === 'archive';
            showConfirm({
                title:     'Bulk Action',
                message:   'Apply \u201c' + label + '\u201d to ' + ids.length + ' application(s)?',
                color:     isDestructive ? 'red' : 'green',
                icon:      isDestructive ? 'x'   : 'check',
                onConfirm: function () { sendBulkAction(ids, action); }
            });
        });
    });

    function sendBulkAction(ids, action) {
        fetch(siteUrl('admin/applications/bulk'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ ids: ids, action: action })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                window.location.reload();
            } else {
                showConfirm({ title: 'Error', message: data.error || 'Something went wrong.', color: 'red', icon: 'x', onConfirm: function () {} });
            }
        })
        .catch(function () {
            showConfirm({ title: 'Network Error', message: 'Could not reach the server.', color: 'red', icon: 'x', onConfirm: function () {} });
        });
    }

    /* ══════════════════════════════════════════════════
       SECTION 6 — Helpers
       ══════════════════════════════════════════════════ */

    function field(label, value) {
        return '<div class="field">'
             + '<div class="field-label">' + label + '</div>'
             + '<div class="field-value">' + (value || '\u2014') + '</div>'
             + '</div>';
    }

    function escHtml(str) {
        if (!str) return '';
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    function capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

    function statusLabel(s) {
        var map = { pending: 'Under Review', reviewed: 'Under Review', accepted: 'Accepted', rejected: 'Rejected' };
        return map[s] || capitalize(s);
    }

    function formatDate(iso) {
        if (!iso) return '\u2014';
        var d = new Date(iso);
        var m = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return m[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
    }

    function truncateUrl(url) {
        return url && url.length > 50 ? url.substring(0, 47) + '\u2026' : url;
    }

    function siteUrl(path) {
        var base = window.APP_BASE_URL || window.location.origin;
        return base.replace(/\/$/, '') + '/' + path;
    }
})();
