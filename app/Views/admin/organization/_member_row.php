<?php
/**
 * @var array $member
 * @var string $activeSection
 */
$id = (int) $member['id'];
$isMentor = ($activeSection ?? '') === 'mentor';
?>
<article class="org-admin-item">
    <div class="org-admin-item-head">
        <div class="org-admin-item-meta">
            <?php if (! $isMentor && ! empty($member['photoPath'])): ?>
                <img src="<?= esc(org_photo_url($member['photoPath'])) ?>" alt="" class="org-admin-thumb">
            <?php elseif (! $isMentor): ?>
                <span class="org-admin-thumb org-admin-thumb-empty">—</span>
            <?php endif; ?>
            <div>
                <strong><?= esc($member['fullName']) ?></strong>
                <span class="org-admin-status <?= ! empty($member['isPublished']) ? 'is-live' : 'is-hidden' ?>">
                    <?= ! empty($member['isPublished']) ? 'Published' : 'Hidden' ?>
                </span>
                <?php if (! $isMentor && ! empty($member['isFeatured'])): ?>
                    <span class="org-admin-badge">Featured</span>
                <?php endif; ?>
                <?php if (! $isMentor): ?>
                    <span class="org-admin-meta"><?= esc(trim(($member['rolePrimary'] ?? '') . ($member['roleSecondary'] ? ' · ' . $member['roleSecondary'] : ''))) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="org-admin-item-actions">
            <form method="POST" action="<?= site_url('admin/organization/members/' . $id . '/move/up') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="org-admin-icon-btn" title="Move up" aria-label="Move up" <?= ! empty($isFirst) ? 'disabled' : '' ?>>↑</button>
            </form>
            <form method="POST" action="<?= site_url('admin/organization/members/' . $id . '/move/down') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="org-admin-icon-btn" title="Move down" aria-label="Move down" <?= ! empty($isLast) ? 'disabled' : '' ?>>↓</button>
            </form>
            <div class="acts">
                <a href="<?= site_url('admin/organization?' . http_build_query(array_filter([
                    'section' => $activeSection,
                    'category' => $activeSection === 'mentor' ? ($member['mentorCategory'] ?? '') : '',
                    'modal' => 'edit',
                    'memberId' => $id,
                ], static fn ($value) => $value !== null && $value !== ''))) ?>" class="act-btn edit" title="Edit" aria-label="Edit member">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/>
                    </svg>
                </a>
                <form method="POST" action="<?= site_url('admin/organization/members/' . $id . '/delete') ?>" onsubmit="return confirm('Delete this member?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="act-btn delete" title="Delete" aria-label="Delete member">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4h8v2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14H6L5 6"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6M14 11v6"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</article>
