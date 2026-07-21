<link rel="stylesheet" href="<?= base_url('assets/css/adminIncubatees.css') ?>">
<?php helper('incubatees'); ?>

<div id="incubateesConfig"
    data-add-url="<?= site_url('admin/cohorts/add') ?>"
    data-delete-base-url="<?= site_url('admin/cohorts/') ?>"
    data-reorder-url="<?= site_url('admin/incubatees/reorder') ?>"
    data-csrf-token-name="<?= csrf_token() ?>"
    data-csrf-token-value="<?= csrf_hash() ?>"></div>

<!-- ─── Cohort Manager Modal ─── -->
<div class="cm-overlay" id="cmOverlay">
    <div class="cm-modal">
        <div class="cm-modal-head">
            <h3>Manage Cohorts</h3>
            <button type="button" class="cm-modal-close" id="cmCloseBtn" title="Close">×</button>
        </div>
        <div class="cm-modal-body">
            <table class="cm-tbl" id="cmTable">
                <thead>
                    <tr>
                        <th>Cohort</th>
                        <th>Startups</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cmBody">
                    <?php foreach ($cohorts ?? [] as $c): ?>
                    <?php $cnt = (int) (($cohortStartupCounts[$c['name']] ?? 0)); ?>
                    <tr data-id="<?= $c['id'] ?>">
                        <td class="cm-name"><?= esc($c['name']) ?></td>
                        <td class="cm-cnt"><?= $cnt ?> startup<?= $cnt !== 1 ? 's' : '' ?></td>
                        <td>
                            <?php if ($cnt > 0): ?>
                                <span class="cm-status cm-active">Active</span>
                            <?php else: ?>
                                <span class="cm-status cm-empty">Coming Soon</span>
                            <?php endif; ?>
                        </td>
                        <td class="ta-right">
                            <button type="button" class="cm-del-btn" title="Delete" data-cohort-id="<?= $c['id'] ?>" data-cohort-name="<?= esc($c['name']) ?>" <?= $cnt > 0 ? 'disabled title="Remove incubatees first"' : '' ?>>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (empty($cohorts)): ?>
            <div class="cm-empty-state" id="cmEmptyState">No cohorts yet. Add one below.</div>
            <?php endif; ?>
        </div>
        <div class="cm-modal-foot">
            <span class="cm-total" id="cmTotal"><?= count($cohorts ?? []) ?> cohort<?= count($cohorts ?? []) !== 1 ? 's' : '' ?></span>
            <button type="button" class="cm-add-btn" id="cmAddBtn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Add Cohort
            </button>
        </div>
    </div>
</div>

<div class="inc-admin-toolbar">
    <div>
        <span class="inc-admin-count"><?= count($incubatees ?? []) ?> incubatees</span>
        <p>Manage startups and MSMEs shown on the public Incubatees page.</p>
    </div>
    <div class="inc-admin-toolbar-actions">
        <div class="toolbar-actions">
            <a href="<?= site_url('incubatees') ?>" target="_blank" rel="noopener" class="btn btn-o">
                View Incubatees page
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h8v8"/>
                </svg>
            </a>
            <button type="button" class="cm-manage-btn" id="cmManageBtn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Add Cohort
            </button>
            <a href="<?= site_url('admin/incubatees/create') ?>" class="btn btn-p">New incubatee</a>
        </div>
    </div>
</div>

<div class="filter-bar">
    <div class="filter-btns" id="cohortFilterBtns">
        <button type="button" class="filter-btn active" data-filter="all">
            All cohorts
            <span class="filter-count"><?= count($incubatees ?? []) ?></span>
        </button>
        <?php foreach (($cohorts ?? []) as $cohort): ?>
            <?php $cohortCount = (int) (($cohortStartupCounts[$cohort['name']] ?? 0)); ?>
            <button type="button" class="filter-btn" data-filter="<?= esc($cohort['name']) ?>">
                <?= esc($cohort['name']) ?>
                <span class="filter-count"><?= $cohortCount ?></span>
            </button>
        <?php endforeach; ?>
    </div>
    <div class="reorder-control-group">
        <button type="button" class="btn btn-o reorder-mode-btn" id="incReorderBtn">Re-order</button>
    </div>
</div>

<div class="inc-table-shell">
<table class="inc-tbl" id="incubateeTable">
    <colgroup>
        <col class="inc-col-drag">
        <col class="inc-col-logo">
        <col class="inc-col-company">
        <col class="inc-col-founders">
        <col class="inc-col-cohort">
        <col class="inc-col-status">
        <col class="inc-col-actions">
    </colgroup>
    <thead>
        <tr>
            <th class="drag-col"></th>
            <th>Logo</th>
            <th>Company</th>
            <th>Founders</th>
            <th>Cohort</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($incubatees)): ?>
        <tr>
            <td colspan="7" class="empty-row" style="padding:2.5rem 1rem;text-align:center;color:#94a3b8;font-size:.82rem">
                No incubatees yet.
                <a href="<?= site_url('admin/incubatees/create') ?>">Add one.</a>
            </td>
        </tr>
    <?php else: ?>
        <?php foreach ($incubatees as $inc): ?>
            <?php
                $founders = [];
                $publicCardUrl = site_url('incubatees') . '#' . incubatee_anchor_id($inc);
                if (! empty($inc['teamMembers'])) {
                    $decodedFounders = json_decode((string) $inc['teamMembers'], true);
                    $founders = is_array($decodedFounders) ? array_values(array_filter($decodedFounders, static function ($founder): bool {
                        return is_array($founder) && trim((string) ($founder['name'] ?? '')) !== '';
                    })) : [];
                }
            ?>
            <tr class="drag-row" data-id="<?= (int) $inc['id'] ?>" data-cohort="<?= esc((string) ($inc['cohort'] ?? '')) ?>">
                <td class="drag-cell">
                    <span class="drag-handle" title="Drag to reorder" aria-label="Drag to reorder">⋮⋮</span>
                </td>
                <td>
                    <?php if (! empty($inc['logoPath'])): ?>
                        <img src="<?= site_url($inc['logoPath']) ?>" alt="" class="tbl-logo"/>
                    <?php else: ?>
                        <span class="tbl-logo-empty"><?= strtoupper(mb_substr($inc['companyName'], 0, 2)) ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="tbl-name"><?= esc($inc['companyName']) ?></span>
                </td>
                <td>
                    <?php if ($founders === []): ?>
                        <span class="founders-empty">No founders</span>
                    <?php else: ?>
                        <span class="founder-stack" aria-label="<?= count($founders) ?> founder<?= count($founders) === 1 ? '' : 's' ?>">
                            <?php foreach (array_slice($founders, 0, 7) as $founder): ?>
                                <?php
                                    $founderName = trim((string) ($founder['name'] ?? 'Founder'));
                                    $founderPhoto = trim((string) ($founder['photo'] ?? ''));
                                    $nameParts = preg_split('/\s+/', $founderName) ?: [];
                                    $initials = '';
                                    foreach (array_slice(array_filter($nameParts), 0, 2) as $part) {
                                        $initials .= mb_substr($part, 0, 1);
                                    }
                                    $initials = $initials !== '' ? mb_strtoupper($initials) : 'F';
                                ?>
                                <?php if ($founderPhoto !== ''): ?>
                                    <img class="founder-avatar" src="<?= site_url($founderPhoto) ?>" alt="<?= esc($founderName, 'attr') ?>" title="<?= esc($founderName, 'attr') ?>">
                                <?php else: ?>
                                    <span class="founder-avatar founder-avatar-empty" title="<?= esc($founderName, 'attr') ?>"><?= esc($initials) ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if (count($founders) > 7): ?>
                                <span class="founder-avatar founder-avatar-more">+<?= count($founders) - 7 ?></span>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (! empty($inc['cohort'])): ?>
                        <span class="tag tag-cohort"><?= esc($inc['cohort']) ?></span>
                    <?php else: ?>
                        <span class="cohort-empty">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="tag <?= $inc['isPublished'] ? 'tag-live' : 'tag-draft' ?>"><?= $inc['isPublished'] ? 'Published' : 'Draft' ?></span>
                </td>
                <td>
                    <div class="acts">
                        <?php if (! empty($inc['isPublished'])): ?>
                            <a href="<?= esc($publicCardUrl, 'attr') ?>" target="_blank" rel="noopener" class="act-btn view" title="View public card" aria-label="View public incubatee card">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h8v8"/>
                                </svg>
                            </a>
                        <?php else: ?>
                            <button type="button" class="act-btn view disabled" title="Publish to view on public page" aria-label="Publish to view on public page" disabled>
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h8v8"/>
                                </svg>
                            </button>
                        <?php endif; ?>
                        <a href="<?= site_url('admin/incubatees/' . $inc['id'] . '/edit') ?>" class="act-btn edit" title="Edit" aria-label="Edit incubatee">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 7.125L16.862 4.487"/><path stroke-linecap="round" stroke-linejoin="round" d="M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                        </a>
                        <form action="<?= site_url('admin/incubatees/' . $inc['id'] . '/delete') ?>" method="POST" data-admin-delete-confirm data-confirm-title="Delete incubatee?" data-confirm-message="This removes <?= esc($inc['companyName'], 'attr') ?> from the incubatee records and the public Incubatees page. This action cannot be undone.">
                            <?= csrf_field() ?>
                            <button type="submit" class="act-btn delete" title="Delete" aria-label="Delete incubatee">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4h8v2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14H6L5 6"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6M14 11v6"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr id="cohortEmptyState" style="display:none">
            <td colspan="7" class="empty-row" style="padding:2.5rem 1rem;text-align:center;color:#94a3b8;font-size:.82rem">
                No incubatees in this cohort yet.
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
