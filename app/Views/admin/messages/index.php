<link rel="stylesheet" href="<?= base_url('assets/css/adminMessages.css') ?>">

<!-- Stats -->
<div class="grid-stats">
    <div class="stat">
        <div class="n"><?= $counts['total'] ?></div>
        <div class="t">Total Messages</div>
    </div>
    <div class="stat">
        <div class="n" style="color:#03558C"><?= $counts['unread'] ?></div>
        <div class="t">Unread</div>
    </div>
    <div class="stat">
        <div class="n" style="color:#94a3b8"><?= $counts['read'] ?></div>
        <div class="t">Read</div>
    </div>
</div>

<!-- ═══════════ INBOX LIST ═══════════ -->
<div class="inbox-wrap" id="inbox">
    <?php if (empty($messages)): ?>
        <div class="empty">No messages yet. When visitors submit the contact form, their messages will appear here.</div>
    <?php else: ?>
        <div class="inbox-bar">
            <span class="bar-label">Inbox</span>
            <span class="bar-count"><?= $counts['total'] ?> message<?= $counts['total'] !== 1 ? 's' : '' ?></span>
        </div>
        <?php foreach ($messages as $m): ?>
            <div class="msg-row <?= $m['isRead'] ? '' : 'unread' ?>" data-id="<?= $m['id'] ?>" onclick="openMsg(<?= $m['id'] ?>)">
                <span class="dot <?= $m['isRead'] ? 'dot-read' : 'dot-unread' ?>"></span>
                <span class="row-name"><?= esc($m['name']) ?></span>
                <span class="row-preview"><?= esc(mb_substr($m['message'], 0, 120)) ?></span>
                <span class="row-date"><?= date('M j', strtotime($m['createdAt'])) ?></span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ═══════════ READER (detail view) ═══════════ -->
<div class="reader" id="reader">
    <!-- Toolbar -->
    <div class="reader-bar">
        <button class="r-btn back" onclick="backToInbox()" title="Back to Inbox">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </button>
        <div class="bar-sep"></div>
        <button class="r-btn" id="btnToggleRead" onclick="toggleRead()" title="Mark as unread">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span id="toggleLabel">Mark unread</span>
        </button>
        <button class="r-btn danger" onclick="confirmDelete()" title="Delete">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Delete
        </button>
    </div>

    <!-- Header -->
    <div class="reader-head">
        <h1 class="rh-subject" id="rSubject">Message</h1>
    </div>

    <!-- Sender row -->
    <div class="sender-row">
        <div class="sender-avatar" id="rAvatar">?</div>
        <div class="sender-info">
            <div class="sender-name" id="rName">—</div>
            <div class="sender-email">to me &lt;<span id="rEmail">—</span>&gt;</div>
        </div>
        <div class="sender-date" id="rDate">—</div>
    </div>

    <!-- Body -->
    <div class="reader-body" id="rBody">—</div>

    <!-- Reply strip -->
    <div class="reply-strip">
        <a class="reply-btn" id="rReply" href="#">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a5 5 0 015 5v4M3 10l6 6M3 10l6-6"/></svg>
            Reply
        </a>
    </div>
</div>

<!-- ═══════════ CONFIRM DELETE DIALOG ═══════════ -->
<div class="confirm-bg" id="confirmDel">
    <div class="confirm-box">
        <div class="confirm-body">
            <div class="confirm-icon red">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3>Delete message?</h3>
            <p id="confirmText">This message will be permanently deleted.</p>
        </div>
        <div class="confirm-actions">
            <button class="c-cancel" onclick="closeConfirm()">Cancel</button>
            <button class="c-delete" id="confirmDelBtn" onclick="doDelete()">Delete</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<div id="adminMessagesConfig" data-base-url="<?= site_url('admin/messages') ?>"></div>
<script src="<?= base_url('assets/js/admin/messages/index.js') ?>" defer></script>
