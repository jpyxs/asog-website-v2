<link rel="stylesheet" href="<?= base_url('assets/css/adminApplications.css') ?>">

<!-- Stats -->
<div class="grid-stats">
    <div class="stat">
        <div class="n"><?= $counts['total'] ?></div>
        <div class="t">Total</div>
    </div>
    <div class="stat">
        <div class="n"><?= $counts['pending'] ?></div>
        <div class="t">Under Review</div>
    </div>
    <div class="stat">
        <div class="n"><?= $counts['accepted'] ?></div>
        <div class="t">Accepted</div>
    </div>
    <div class="stat">
        <div class="n"><?= $counts['rejected'] ?></div>
        <div class="t">Rejected</div>
    </div>
</div>

<!-- Table -->
<?php if (empty($applications)): ?>
<div class="tbl-wrap">
    <div class="empty">No applications yet.</div>
</div>
<?php else: ?>
<div class="tbl-wrap">
    <table class="tbl">
        <thead>
            <tr>
                <th>Applicant</th>
                <th>Startup</th>
                <th>Email</th>
                <th>Date</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $app): ?>
            <tr data-id="<?= esc($app['id']) ?>">
                <td><?= esc($app['applicantName']) ?></td>
                <td><?= esc($app['startupName']) ?></td>
                <td class="mono"><?= esc($app['applicantEmail']) ?></td>
                <td class="mono"><?= date('M j, Y', strtotime($app['createdAt'])) ?></td>
                <td>
                    <?php
                                $statusLabels = ['pending' => 'Under Review', 'accepted' => 'Accepted', 'rejected' => 'Rejected', 'reviewed' => 'Reviewed'];
                            ?>
                    <span class="tag tag-<?= esc($app['applicationStatus']) ?>">
                        <?= esc($statusLabels[$app['applicationStatus']] ?? ucfirst($app['applicationStatus'])) ?>
                    </span>
                </td>
                <td>
                    <button class="btn-review" data-id="<?= esc($app['id']) ?>">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Review
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Review Modal -->
<div class="modal-bg" id="reviewModal">
    <div class="modal">
        <div class="modal-head">
            <h2 id="modalTitle">Application Review</h2>
            <button class="modal-close" id="modalClose">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="modal-scroll">
            <div class="modal-body" id="modalBody">
                <!-- Populated by JS -->
            </div>
        </div>
        <div class="modal-foot" id="modalFoot">
            <!-- Shown for under review -->
            <button class="btn-reject" id="btnReject" style="display:none">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reject
            </button>
            <button class="btn-accept" id="btnAccept" style="display:none">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Accept
            </button>
            <!-- Shown for accepted/rejected -->
            <button class="btn-change" id="btnChange" style="display:none">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Change Status
            </button>
            <select class="status-select" id="statusSelect" style="display:none">
                <option value="pending">Under Review</option>
                <option value="accepted">Accepted</option>
                <option value="rejected">Rejected</option>
            </select>
            <button class="btn-accept" id="btnSaveStatus" style="display:none">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Save
            </button>
        </div>
    </div>
</div>

<!-- Universal Confirm Dialog -->
<div class="confirm-bg" id="confirmDialog">
    <div class="confirm-box">
        <div class="confirm-body">
            <div class="confirm-icon" id="confirmIcon">
                <svg id="confirmSvg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"></svg>
            </div>
            <h3 id="confirmTitle"></h3>
            <p id="confirmMsg"></p>
        </div>
        <div class="confirm-actions">
            <button class="c-cancel" id="confirmCancel">Cancel</button>
            <button class="c-ok" id="confirmOk">Confirm</button>
        </div>
    </div>
</div>

<?= jsBaseUrl() ?>
<script src="<?= site_url('assets/js/admin/applications/index.js') ?>"></script>