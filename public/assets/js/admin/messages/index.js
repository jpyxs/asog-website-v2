(function () {
    'use strict';

    function init(root) {
        var scope = root && root.querySelector ? root : document;
        var controller = new AbortController();
        var signal = controller.signal;
        var currentMsg = null;
        var selectedIds = new Set();
        var configNode = scope.querySelector('#adminMessagesConfig');
        var msgBaseUrl = configNode ? configNode.getAttribute('data-base-url') : '';
        var selectAll = null;
        var smartCaretBtn = null;
        var smartChkMenu = null;
        var actionConfirm = null;
        var actionConfirmBackdrop = null;
        var actionConfirmIcon = null;
        var actionConfirmSvg = null;
        var actionConfirmTitle = null;
        var actionConfirmMsg = null;
        var actionConfirmOk = null;
        var actionConfirmCancel = null;
        var actionConfirmResolver = null;
        var actionConfirmLastFocus = null;
        var actionConfirmBound = false;
        var pageRoot = scope && scope.nodeType === 1 ? scope : document.querySelector('[data-admin-page="messages"]');

        var archiveIconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>';
        var restoreIconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 10l6 6m-6-6l6-6"/>';
        var readIconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>';
        var unreadIconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>';

        function lockListScroll() {
            document.documentElement.classList.add('admin-messages-list-lock');
        }

        function unlockListScroll() {
            document.documentElement.classList.remove('admin-messages-list-lock');
        }

        function refreshRefs() {
            selectAll = document.getElementById('selectAll');
            smartCaretBtn = document.getElementById('smartCaretBtn');
            smartChkMenu = document.getElementById('smartChkMenu');
            actionConfirm = document.getElementById('msgActionConfirm');
            actionConfirmBackdrop = document.getElementById('msgActionConfirmBackdrop');
            actionConfirmIcon = document.getElementById('msgActionConfirmIcon');
            actionConfirmSvg = document.getElementById('msgActionConfirmSvg');
            actionConfirmTitle = document.getElementById('msgActionConfirmTitle');
            actionConfirmMsg = document.getElementById('msgActionConfirmMessage');
            actionConfirmOk = document.getElementById('msgActionConfirmOk');
            actionConfirmCancel = document.getElementById('msgActionConfirmCancel');
        }

        function setReaderMode(isOpen) {
            if (pageRoot) pageRoot.classList.toggle('messages-reader-open', isOpen);
            document.documentElement.classList.toggle('admin-messages-reader-open', isOpen);

            var filterBar = document.querySelector('.msg-filter-bar');
            var inbox = document.getElementById('inbox');
            if (filterBar) filterBar.style.display = isOpen ? 'none' : '';
            if (inbox) inbox.style.display = isOpen ? 'none' : '';
        }

        function updateHistory(url) {
            if (window.AdminShell && typeof window.AdminShell.updateHistory === 'function') {
                window.AdminShell.updateHistory(url);
            } else {
                history.pushState(null, '', url);
            }
        }

        function getRows() {
            return Array.from(document.querySelectorAll('.msg-row'));
        }

        function getVisibleRows() {
            return getRows().filter(function (r) { return r.style.display !== 'none'; });
        }

        function showToast(text) {
            var t = document.getElementById('toast');
            if (!t) return;
            t.textContent = text;
            t.classList.add('show');
            setTimeout(function () { t.classList.remove('show'); }, 2600);
        }

        function formatDateParts(str) {
            var d = new Date(str);
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            var h = d.getHours();
            var m = d.getMinutes();
            var ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return {
                date: months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear(),
                time: h + ':' + (m < 10 ? '0' : '') + m + ' ' + ampm
            };
        }

        function updateInboxCount() {
            var count = getRows().length;
            var el = document.getElementById('barCount');
            if (el) el.textContent = count + ' message' + (count !== 1 ? 's' : '');
        }

        function updateBulkState() {
            var count = selectedIds.size;
            var bar = document.getElementById('bulkActionsBar');
            var form = document.getElementById('filterForm');
            var countEl = document.getElementById('bulkCount');

            if (bar) bar.classList.toggle('bulk-visible', count > 0);
            if (form) form.classList.toggle('search-hidden', count > 0);
            if (countEl) countEl.textContent = count;

            getRows().forEach(function (row) {
                var id = row.getAttribute('data-id');
                var check = row.querySelector('.row-select');
                var selected = selectedIds.has(id);
                row.classList.toggle('row-selected', selected);
                if (check) check.checked = selected;
            });

            if (selectAll) {
                var visible = getVisibleRows();
                var selectedCount = visible.filter(function (r) { return selectedIds.has(r.getAttribute('data-id')); }).length;
                selectAll.checked = visible.length > 0 && selectedCount === visible.length;
                selectAll.indeterminate = selectedCount > 0 && selectedCount < visible.length;
            }

            var hasUnread = false;
            var hasRead = false;
            selectedIds.forEach(function (id) {
                var row = document.querySelector('.msg-row[data-id="' + id + '"]');
                if (!row) return;
                if (row.getAttribute('data-read') === '0') hasUnread = true;
                else hasRead = true;
            });

            var btnRead = document.querySelector('.bulk-act-read');
            var btnUnread = document.querySelector('.bulk-act-unread');
            var btnArchive = document.querySelector('.bulk-act-archive');
            if (btnRead) btnRead.disabled = !hasUnread;
            if (btnUnread) btnUnread.disabled = !hasRead;
            if (btnArchive) {
                var hasInboxSelected = false;
                selectedIds.forEach(function (id) {
                    var row = document.querySelector('.msg-row[data-id="' + id + '"]');
                    if (row && row.getAttribute('data-archived') !== '1') hasInboxSelected = true;
                });
                btnArchive.disabled = !hasInboxSelected;
            }
        }

        function onRowCheck(checkbox, id) {
            if (checkbox.checked) selectedIds.add(String(id));
            else selectedIds.delete(String(id));
            updateBulkState();
        }

        function smartSelect(type) {
            var visible = getVisibleRows();
            if (type === 'none') {
                selectedIds.clear();
            } else {
                visible.forEach(function (row) {
                    var shouldSelect = type === 'all'
                        || (type === 'read' && row.getAttribute('data-read') === '1')
                        || (type === 'unread' && row.getAttribute('data-read') === '0');
                    if (shouldSelect) selectedIds.add(row.getAttribute('data-id'));
                });
            }
            updateBulkState();
        }

        function bindStaticControls() {
            refreshRefs();

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    smartSelect(this.checked ? 'all' : 'none');
                }, { signal: signal });
            }

            if (smartCaretBtn) {
                smartCaretBtn.addEventListener('click', function (event) {
                    event.stopPropagation();
                    if (smartChkMenu) smartChkMenu.classList.toggle('open');
                }, { signal: signal });
            }

            if (smartChkMenu) {
                smartChkMenu.addEventListener('click', function (event) {
                    var item = event.target.closest('.scm-item');
                    if (!item) return;
                    smartSelect(item.getAttribute('data-smart'));
                    smartChkMenu.classList.remove('open');
                }, { signal: signal });
            }

            var filterSelectEl = document.getElementById('filterSelect');
            if (filterSelectEl) {
                filterSelectEl.addEventListener('change', function () {
                    var val = this.value;
                    var viewInp = document.getElementById('viewInput');
                    var dateInp = document.getElementById('dateInput');
                    if (!viewInp || !dateInp) return;

                    if (val === 'archived' || val === 'all' || val === 'inbox') {
                        viewInp.value = val;
                        dateInp.value = 'all';
                    } else {
                        var parts = val.split('-');
                        viewInp.value = 'inbox';
                        dateInp.value = parts[1] || 'all';
                    }

                    var filterForm = document.getElementById('filterForm');
                    if (filterForm) filterForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }, { signal: signal });
            }

            var filterForm = document.getElementById('filterForm');
            if (filterForm) {
                filterForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    var params = new URLSearchParams(new FormData(this));
                    loadPage(msgBaseUrl + '?' + params.toString());
                }, { signal: signal });
            }

            var clearBtn = document.querySelector('.app-btn-clear');
            if (clearBtn) {
                clearBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    loadPage(clearBtn.getAttribute('href'));
                }, { signal: signal });
            }

            if (!actionConfirmBound && actionConfirmCancel) {
                actionConfirmCancel.addEventListener('click', function () { closeActionConfirm(false); }, { signal: signal });
            }
            if (!actionConfirmBound && actionConfirmBackdrop) {
                actionConfirmBackdrop.addEventListener('click', function () { closeActionConfirm(false); }, { signal: signal });
            }
            if (!actionConfirmBound && actionConfirmOk) {
                actionConfirmOk.addEventListener('click', function () { closeActionConfirm(true); }, { signal: signal });
            }
            if (actionConfirmCancel && actionConfirmBackdrop && actionConfirmOk) actionConfirmBound = true;
        }

        function bindInboxEvents() {
            refreshRefs();
            getRows().forEach(function (row) {
                var id = row.getAttribute('data-id');
                row.onclick = function () { openMsg(id); };
                var checkbox = row.querySelector('.row-select');
                if (checkbox) {
                    checkbox.onchange = function () { onRowCheck(this, id); };
                }
            });

            document.querySelectorAll('.tbl-pagination .pag-btn:not(.pag-disabled)').forEach(function (btn) {
                btn.addEventListener('click', function (event) {
                    var href = this.getAttribute('href');
                    if (!href || href === '#') return;
                    event.preventDefault();
                    loadPage(href);
                }, { signal: signal });
            });
        }

        function showListSkeleton(container) {
            if (window.AdminRowSkeleton && typeof window.AdminRowSkeleton.showList === 'function') {
                return window.AdminRowSkeleton.showList(container);
            }
            if (!container || !container.querySelector('.msg-row')) return false;
            container.classList.add('is-admin-list-loading');
            return true;
        }

        function hideListSkeleton(container) {
            if (window.AdminRowSkeleton && typeof window.AdminRowSkeleton.hideList === 'function') {
                window.AdminRowSkeleton.hideList(container);
                return;
            }
            if (container) container.classList.remove('is-admin-list-loading');
        }

        function waitForListImages(root) {
            if (window.AdminRowSkeleton && typeof window.AdminRowSkeleton.waitForImages === 'function') {
                return window.AdminRowSkeleton.waitForImages(root);
            }
            return Promise.resolve();
        }

        function loadPage(url) {
            if (!url) return;
            var oldInbox = document.querySelector('.inbox-wrap');
            showListSkeleton(oldInbox);

            fetch(url, { cache: 'no-store', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (response) { return response.text(); })
                .then(function (html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    setReaderMode(false);

                    var newStats = doc.querySelector('.grid-stats');
                    var oldStats = document.querySelector('.grid-stats');
                    if (newStats && oldStats) oldStats.replaceWith(newStats);

                    var newFilterBar = doc.querySelector('.msg-filter-bar');
                    var oldFilterBar = document.querySelector('.msg-filter-bar');
                    if (newFilterBar && oldFilterBar) oldFilterBar.replaceWith(newFilterBar);
                    if (newFilterBar && window.AdminCustomSelect && typeof window.AdminCustomSelect.init === 'function') {
                        window.AdminCustomSelect.init(newFilterBar);
                    }

                    var newInbox = doc.querySelector('.inbox-wrap');
                    if (newInbox) showListSkeleton(newInbox);
                    oldInbox = document.querySelector('.inbox-wrap');
                    if (newInbox && oldInbox) oldInbox.replaceWith(newInbox);

                    selectedIds.clear();
                    refreshRefs();
                    bindStaticControls();
                    bindInboxEvents();
                    updateBulkState();
                    updateHistory(url);
                    return waitForListImages(newInbox || document.querySelector('.inbox-wrap')).then(function () {
                        hideListSkeleton(document.querySelector('.inbox-wrap'));
                    });
                })
                .catch(function () {
                    hideListSkeleton(document.querySelector('.inbox-wrap'));
                });
        }

        function actionIconPath(action) {
            if (action === 'mark_read') return readIconPath;
            if (action === 'mark_unread') return unreadIconPath;
            if (action === 'archive') return archiveIconPath;
            if (action === 'unarchive') return restoreIconPath;
            return readIconPath;
        }

        function actionConfirmCopy(action, count) {
            var plural = count !== 1 ? 's' : '';
            if (action === 'mark_read') {
                return {
                    title: 'Mark selected as read?',
                    message: 'This will mark ' + count + ' selected message' + plural + ' as read.',
                    confirmLabel: 'Mark Read',
                    tone: 'blue'
                };
            }
            if (action === 'mark_unread') {
                return {
                    title: 'Mark selected as unread?',
                    message: 'This will mark ' + count + ' selected message' + plural + ' as unread.',
                    confirmLabel: 'Mark Unread',
                    tone: 'gray'
                };
            }
            if (action === 'archive') {
                return {
                    title: 'Archive selected messages?',
                    message: 'This will move ' + count + ' selected message' + plural + ' out of the inbox.',
                    confirmLabel: 'Archive',
                    tone: 'gray'
                };
            }
            return {
                title: 'Restore selected messages?',
                message: 'This will move ' + count + ' selected archived message' + plural + ' back to the inbox.',
                confirmLabel: 'Restore to Inbox',
                tone: 'blue'
            };
        }

        function askActionConfirm(action, count) {
            if (!actionConfirm) {
                return Promise.resolve(confirm(actionConfirmCopy(action, count).message));
            }

            var copy = actionConfirmCopy(action, count);
            actionConfirmTitle.textContent = copy.title;
            actionConfirmMsg.textContent = copy.message;
            actionConfirmOk.textContent = copy.confirmLabel;
            actionConfirmOk.className = 'btn msg-action-confirm-ok ' + copy.tone;
            actionConfirmIcon.className = 'msg-action-confirm-icon ' + copy.tone;
            actionConfirmSvg.innerHTML = actionIconPath(action);

            actionConfirmLastFocus = document.activeElement;
            actionConfirm.classList.add('is-open');
            actionConfirm.setAttribute('aria-hidden', 'false');
            document.body.classList.add('msg-action-confirm-open');
            window.setTimeout(function () { actionConfirmOk.focus(); }, 30);

            return new Promise(function (resolve) {
                actionConfirmResolver = resolve;
            });
        }

        function closeActionConfirm(result) {
            if (!actionConfirm || !actionConfirm.classList.contains('is-open')) return;
            actionConfirm.classList.remove('is-open');
            actionConfirm.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('msg-action-confirm-open');

            var resolver = actionConfirmResolver;
            actionConfirmResolver = null;
            if (resolver) resolver(!!result);

            if (actionConfirmLastFocus && typeof actionConfirmLastFocus.focus === 'function') {
                window.setTimeout(function () { actionConfirmLastFocus.focus(); }, 30);
            }
        }

        function runBulkAction(action, ids) {
            fetch(msgBaseUrl + '/bulk', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: action, ids: ids.map(Number) })
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (!data.success) {
                        showToast(data.error || 'Something went wrong.');
                        return;
                    }

                    if (action === 'delete' || action === 'archive' || action === 'unarchive') {
                        selectedIds.clear();
                        updateBulkState();
                        showToast(data.message);
                        loadPage(window.location.href);
                        return;
                    } else {
                        var isRead = action === 'mark_read';
                        ids.forEach(function (id) {
                            var row = document.querySelector('.msg-row[data-id="' + id + '"]');
                            if (!row) return;
                            row.classList.toggle('unread', !isRead);
                            row.setAttribute('data-read', isRead ? '1' : '0');
                            var dot = row.querySelector('.dot');
                            if (dot) {
                                dot.classList.toggle('dot-unread', !isRead);
                                dot.classList.toggle('dot-read', isRead);
                            }
                        });
                    }

                    selectedIds.clear();
                    updateBulkState();
                    showToast(data.message);
                });
        }

        function bulkDo(action) {
            var ids = Array.from(selectedIds);
            if (!ids.length || !msgBaseUrl) return;

            if (action === 'delete') {
                var confirmBulkDelete = window.AdminDeleteConfirm
                    ? window.AdminDeleteConfirm.ask({
                        title: 'Delete selected messages?',
                        message: 'This removes ' + ids.length + ' selected contact message' + (ids.length !== 1 ? 's' : '') + ' from the inbox and archive. This action cannot be undone.',
                    })
                    : Promise.resolve(confirm('Permanently delete ' + ids.length + ' message' + (ids.length !== 1 ? 's' : '') + '? This cannot be undone.'));

                confirmBulkDelete.then(function (confirmed) {
                    if (confirmed) runBulkAction(action, ids);
                });
                return;
            }

            askActionConfirm(action, ids.length).then(function (confirmed) {
                if (confirmed) runBulkAction(action, ids);
            });
        }

        function archiveSingle() {
            if (!currentMsg || !msgBaseUrl) return;
            var btn = document.getElementById('btnArchive');
            var action = (btn && btn.getAttribute('data-action')) || 'archive';

            fetch(msgBaseUrl + '/bulk', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: action, ids: [currentMsg.id] })
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (!data.success) {
                        showToast(data.error || 'Something went wrong.');
                        return;
                    }
                    backToInbox();
                    showToast(data.message);
                    loadPage(window.location.href);
                });
        }

        function openMsg(id) {
            if (!msgBaseUrl) return;
            getRows().forEach(function (row) { row.classList.remove('active'); });
            var row = document.querySelector('.msg-row[data-id="' + id + '"]');
            if (row) row.classList.add('active');

            fetch(msgBaseUrl + '/' + id)
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data.error) {
                        showToast(data.error);
                        return;
                    }

                    currentMsg = data;
                    document.getElementById('rAvatar').textContent = data.name.charAt(0);
                    document.getElementById('rName').textContent = data.name;
                    var senderEmail = document.querySelector('.sender-email');
                    if (senderEmail) {
                        senderEmail.textContent = '';
                        var senderLink = document.createElement('a');
                        senderLink.href = 'mailto:' + data.email;
                        senderLink.textContent = data.email;
                        senderEmail.appendChild(senderLink);
                    }
                    var dateParts = formatDateParts(data.createdAt);
                    document.getElementById('rDate').textContent = dateParts.date;
                    document.getElementById('rTime').textContent = dateParts.time;
                    document.getElementById('rBody').textContent = data.message;
                    document.getElementById('rReply').href = 'mailto:' + encodeURIComponent(data.email) + '?subject=' + encodeURIComponent('Re: Your message to ASOG TBI');
                    document.getElementById('toggleLabel').textContent = data.isRead == 1 ? 'Mark unread' : 'Mark read';

                    var archiveBtn = document.getElementById('btnArchive');
                    var archiveLabel = document.getElementById('archiveLabel');
                    var archiveIcon = document.getElementById('archiveIcon');
                    if (data.isArchived == 1) {
                        archiveBtn.setAttribute('data-action', 'unarchive');
                        archiveLabel.textContent = 'Restore to Inbox';
                        if (archiveIcon) archiveIcon.innerHTML = restoreIconPath;
                    } else {
                        archiveBtn.setAttribute('data-action', 'archive');
                        archiveLabel.textContent = 'Archive';
                        if (archiveIcon) archiveIcon.innerHTML = archiveIconPath;
                    }

                    if (row) {
                        row.classList.remove('unread');
                        row.setAttribute('data-read', '1');
                        var dot = row.querySelector('.dot');
                        if (dot) {
                            dot.classList.remove('dot-unread');
                            dot.classList.add('dot-read');
                        }
                    }

                    document.getElementById('reader').classList.add('open');
                    setReaderMode(true);
                    lockListScroll();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
        }

        function backToInbox() {
            var reader = document.getElementById('reader');
            if (reader) reader.classList.remove('open');
            setReaderMode(false);
            lockListScroll();
            currentMsg = null;
            getRows().forEach(function (row) { row.classList.remove('active'); });
        }

        function toggleRead() {
            if (!currentMsg || !msgBaseUrl) return;
            fetch(msgBaseUrl + '/' + currentMsg.id + '/read', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (!data.success) return;
                    currentMsg.isRead = data.isRead;
                    document.getElementById('toggleLabel').textContent = data.isRead ? 'Mark unread' : 'Mark read';

                    var row = document.querySelector('.msg-row[data-id="' + currentMsg.id + '"]');
                    if (row) {
                        row.setAttribute('data-read', data.isRead ? '1' : '0');
                        var dot = row.querySelector('.dot');
                        row.classList.toggle('unread', !data.isRead);
                        if (dot) {
                            dot.classList.toggle('dot-unread', !data.isRead);
                            dot.classList.toggle('dot-read', !!data.isRead);
                        }
                    }
                    showToast(data.message);
                    backToInbox();
                });
        }

        function doDelete() {
            if (!currentMsg || !msgBaseUrl) return;
            var id = currentMsg.id;
            fetch(msgBaseUrl + '/' + id, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (!data.success) return;
                    var row = document.querySelector('.msg-row[data-id="' + id + '"]');
                    if (row) row.remove();
                    updateInboxCount();
                    backToInbox();
                    showToast('Message deleted.');
                });
        }

        function confirmDelete() {
            if (!currentMsg) return;
            var confirmSingleDelete = window.AdminDeleteConfirm
                ? window.AdminDeleteConfirm.ask({
                    title: 'Delete message?',
                    message: 'This removes the contact message from "' + currentMsg.name + '" from the inbox and archive. This action cannot be undone.',
                })
                : Promise.resolve(confirm('Delete message from "' + currentMsg.name + '"? This cannot be undone.'));

            confirmSingleDelete.then(function (confirmed) {
                if (confirmed) doDelete();
            });
        }

        document.addEventListener('click', function (event) {
            if (smartChkMenu) smartChkMenu.classList.remove('open');
            var clearBtn = event.target.closest('.app-btn-clear');
            if (clearBtn) {
                event.preventDefault();
                loadPage(clearBtn.getAttribute('href'));
            }
        }, { signal: signal });

        document.addEventListener('keydown', function (event) {
            var reader = document.getElementById('reader');
            if (event.key === 'Escape' && actionConfirm && actionConfirm.classList.contains('is-open')) {
                closeActionConfirm(false);
                return;
            }
            if (event.key === 'Escape' && reader && reader.classList.contains('open')) {
                backToInbox();
            }
            if (event.key === 'u' && currentMsg && !event.ctrlKey && !event.metaKey && document.activeElement.tagName !== 'INPUT') {
                toggleRead();
            }
        }, { signal: signal });

        bindStaticControls();
        bindInboxEvents();
        lockListScroll();

        window.bulkDo = bulkDo;
        window.openMsg = openMsg;
        window.backToInbox = backToInbox;
        window.toggleRead = toggleRead;
        window.archiveSingle = archiveSingle;
        window.confirmDelete = confirmDelete;

        return function () {
            controller.abort();
            unlockListScroll();
            setReaderMode(false);
            closeActionConfirm(false);
            delete window.bulkDo;
            delete window.openMsg;
            delete window.backToInbox;
            delete window.toggleRead;
            delete window.archiveSingle;
            delete window.confirmDelete;
        };
    }

    if (window.AdminShell && typeof window.AdminShell.register === 'function') {
        window.AdminShell.register('messages', { init: init });
    } else if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { init(document); });
    } else {
        init(document);
    }
})();
