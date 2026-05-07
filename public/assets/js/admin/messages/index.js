var currentMsg = null;
var allRows    = document.querySelectorAll('.msg-row');
var configNode = document.getElementById('adminMessagesConfig');
var msgBaseUrl = configNode ? configNode.getAttribute('data-base-url') : '';

/* ── Helpers ─────────────────────────────── */
function showToast(text) {
    var t = document.getElementById('toast');
    t.textContent = text;
    t.classList.add('show');
    setTimeout(function(){ t.classList.remove('show'); }, 2400);
}

function formatDate(str) {
    var d = new Date(str);
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var h = d.getHours(), m = d.getMinutes();
    var ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear() + ', ' + h + ':' + (m < 10 ? '0' : '') + m + ' ' + ampm;
}

/* ── Open message (Gmail-style) ──────────── */
function openMsg(id) {
    if (!msgBaseUrl) return;
    allRows.forEach(function(r){ r.classList.remove('active'); });
    var row = document.querySelector('.msg-row[data-id="' + id + '"]');
    if (row) row.classList.add('active');

    fetch(msgBaseUrl + '/' + id)
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (d.error) { showToast(d.error); return; }
            currentMsg = d;

            document.getElementById('rSubject').textContent = 'Message from ' + d.name;
            document.getElementById('rAvatar').textContent  = d.name.charAt(0);
            document.getElementById('rName').textContent    = d.name;
            document.getElementById('rEmail').innerHTML     = '<a href="mailto:' + d.email + '">' + d.email + '</a>';
            document.getElementById('rDate').textContent    = formatDate(d.createdAt);
            document.getElementById('rBody').textContent    = d.message;
            document.getElementById('rReply').href          = 'mailto:' + encodeURIComponent(d.email) + '?subject=' + encodeURIComponent('Re: Your message to ASOG TBI');
            document.getElementById('toggleLabel').textContent = d.isRead == 1 ? 'Mark unread' : 'Mark read';

            if (row) {
                row.classList.remove('unread');
                var dot = row.querySelector('.dot');
                if (dot) { dot.classList.remove('dot-unread'); dot.classList.add('dot-read'); }
            }

            document.getElementById('inbox').style.display  = 'none';
            document.getElementById('reader').classList.add('open');
            window.scrollTo({top: 0, behavior: 'smooth'});
        });
}

/* ── Back to inbox ───────────────────────── */
function backToInbox() {
    document.getElementById('reader').classList.remove('open');
    document.getElementById('inbox').style.display  = '';
    currentMsg = null;
    allRows.forEach(function(r){ r.classList.remove('active'); });
}

/* ── Toggle read / unread ────────────────── */
function toggleRead() {
    if (!currentMsg) return;
    if (!msgBaseUrl) return;
    fetch(msgBaseUrl + '/' + currentMsg.id + '/read', {
        method: 'PUT',
        headers: { 'Content-Type':'application/json', 'X-Requested-With':'XMLHttpRequest' }
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (!d.success) return;
        currentMsg.isRead = d.isRead;
        document.getElementById('toggleLabel').textContent = d.isRead ? 'Mark unread' : 'Mark read';

        var row = document.querySelector('.msg-row[data-id="' + currentMsg.id + '"]');
        if (row) {
            var dot = row.querySelector('.dot');
            if (d.isRead) {
                row.classList.remove('unread');
                dot.classList.remove('dot-unread'); dot.classList.add('dot-read');
            } else {
                row.classList.add('unread');
                dot.classList.add('dot-unread'); dot.classList.remove('dot-read');
            }
        }
        showToast(d.message);
    });
}

/* ── Delete ───────────────────────────────── */
function confirmDelete() {
    if (!currentMsg) return;
    document.getElementById('confirmText').textContent = 'Delete message from "' + currentMsg.name + '"? This cannot be undone.';
    document.getElementById('confirmDel').classList.add('open');
}
function closeConfirm() {
    document.getElementById('confirmDel').classList.remove('open');
}
function doDelete() {
    if (!currentMsg) return;
    if (!msgBaseUrl) return;
    var id = currentMsg.id;
    fetch(msgBaseUrl + '/' + id, {
        method: 'DELETE',
        headers: { 'X-Requested-With':'XMLHttpRequest' }
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (!d.success) return;
        var row = document.querySelector('.msg-row[data-id="' + id + '"]');
        if (row) row.remove();
        backToInbox();
        showToast('Message deleted');
    });
    closeConfirm();
}

/* ── Keyboard shortcuts ──────────────────── */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('confirmDel').classList.contains('open')) {
            closeConfirm();
        } else if (document.getElementById('reader').classList.contains('open')) {
            backToInbox();
        }
    }
    if (e.key === 'u' && currentMsg && !e.ctrlKey && !e.metaKey && document.activeElement.tagName !== 'INPUT') {
        toggleRead();
    }
});

var confirmDialog = document.getElementById('confirmDel');
if (confirmDialog) {
    confirmDialog.addEventListener('click', function(e) { if (e.target === this) closeConfirm(); });
}
