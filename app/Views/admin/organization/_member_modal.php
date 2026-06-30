<?php
$isEdit = isset($modalMember) && is_array($modalMember);
$memberId = $isEdit ? (int) ($modalMember['id'] ?? 0) : 0;
$formUrl = $isEdit
    ? site_url('admin/organization/members/' . $memberId . '/update')
    : site_url('admin/organization/members');

$selectedSection = old('section', $isEdit ? ($modalMember['section'] ?? '') : ($defaultSection ?? 'core_team'));
$selectedCategory = old('mentorCategory', $isEdit ? ($modalMember['mentorCategory'] ?? '') : ($defaultCategory ?? ''));
$isMentor = $selectedSection === 'mentor';
$modalTitle = $isEdit ? 'Edit member' : 'Add member';
$closeUrl = site_url('admin/organization?' . http_build_query(array_filter([
    'section' => $selectedSection,
    'category' => $selectedSection === 'mentor' ? $selectedCategory : '',
], static fn ($value) => $value !== null && $value !== '')));
?>
<div class="org-admin-modal" data-org-modal>
    <a href="<?= esc($closeUrl) ?>" class="org-admin-modal-backdrop" aria-label="Close modal"></a>
    <div class="org-admin-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="orgMemberModalTitle">
        <div class="org-admin-modal-head">
            <div>
                <span class="org-admin-modal-kicker">Organization member</span>
                <h2 id="orgMemberModalTitle"><?= esc($modalTitle) ?></h2>
            </div>
            <a href="<?= esc($closeUrl) ?>" class="org-admin-modal-close" aria-label="Close modal">×</a>
        </div>

        <form method="POST" action="<?= $formUrl ?>" enctype="multipart/form-data" class="org-admin-form org-admin-modal-form">
            <?= csrf_field() ?>

            <label class="org-admin-field">
                <span>Section</span>
                <select name="section" id="orgSection" required>
                    <?php foreach (App\Models\OrganizationMemberModel::SECTIONS as $sectionKey): ?>
                        <option value="<?= esc($sectionKey) ?>" <?= $selectedSection === $sectionKey ? 'selected' : '' ?>>
                            <?= esc(App\Models\OrganizationMemberModel::sectionLabel($sectionKey)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="org-admin-field">
                <span>Full name</span>
                <input type="text" name="fullName" value="<?= esc(old('fullName', $isEdit ? ($modalMember['fullName'] ?? '') : '')) ?>" maxlength="150" required>
            </label>

            <div id="orgRoleFields" class="<?= $isMentor ? 'is-hidden' : '' ?>">
                <label class="org-admin-field">
                    <span>Primary title / role</span>
                    <input type="text" name="rolePrimary" value="<?= esc(old('rolePrimary', $isEdit ? ($modalMember['rolePrimary'] ?? '') : '')) ?>" maxlength="255">
                </label>
                <label class="org-admin-field">
                    <span>Secondary title / role (optional)</span>
                    <input type="text" name="roleSecondary" value="<?= esc(old('roleSecondary', $isEdit ? ($modalMember['roleSecondary'] ?? '') : '')) ?>" maxlength="255">
                </label>
            </div>

            <label class="org-admin-field <?= $isMentor ? '' : 'is-hidden' ?>" id="orgMentorCategoryField">
                <span>Mentor category</span>
                <select name="mentorCategory" required>
                    <option value="">Select category</option>
                    <?php foreach (($mentorCategories ?? []) as $category): ?>
                        <option value="<?= esc($category) ?>" <?= $selectedCategory === $category ? 'selected' : '' ?>>
                            <?= esc($category) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div id="orgPhotoField" class="<?= $isMentor ? 'is-hidden' : '' ?>">
                <label class="org-admin-field">
                    <span>Photo (square, max 2 MB)</span>
                    <input type="file" name="photo" accept="image/*">
                </label>
                <?php if ($isEdit && ! empty($modalMember['photoPath'])): ?>
                    <div class="org-admin-photo-preview">
                        <img src="<?= esc(org_photo_url($modalMember['photoPath'])) ?>" alt="Current photo">
                        <span>Current photo</span>
                    </div>
                <?php endif; ?>
            </div>

            <label class="org-admin-publish <?= $isMentor ? 'is-hidden' : '' ?>" id="orgFeaturedField">
                <input type="hidden" name="isFeatured" value="0">
                <input type="checkbox" name="isFeatured" value="1" <?= old('isFeatured', $isEdit ? (string) ($modalMember['isFeatured'] ?? '0') : '0') === '1' ? 'checked' : '' ?>>
                <span>
                    <strong>Featured layout</strong>
                    <small>Centered leader/manager card in Core Team or TBI Staff.</small>
                </span>
            </label>

            <label class="org-admin-publish">
                <input type="hidden" name="isPublished" value="0">
                <input type="checkbox" name="isPublished" value="1" <?= old('isPublished', $isEdit ? (string) ($modalMember['isPublished'] ?? '1') : '1') !== '0' ? 'checked' : '' ?>>
                <span>
                    <strong>Published</strong>
                    <small>Show this member on the public page.</small>
                </span>
            </label>

            <div class="org-admin-form-actions">
                <a href="<?= esc($closeUrl) ?>" class="btn btn-o">Cancel</a>
                <button type="submit" class="btn btn-p"><?= $isEdit ? 'Save changes' : 'Add member' ?></button>
            </div>
        </form>
    </div>
</div>

<script>
(() => {
    const modal = document.querySelector('[data-org-modal]');
    const sectionSelect = document.getElementById('orgSection');
    const roleFields = document.getElementById('orgRoleFields');
    const mentorField = document.getElementById('orgMentorCategoryField');
    const photoField = document.getElementById('orgPhotoField');
    const featuredField = document.getElementById('orgFeaturedField');
    if (!modal || !sectionSelect) return;

    document.body.classList.add('org-modal-open');

    const syncFields = () => {
        const isMentor = sectionSelect.value === 'mentor';
        roleFields?.classList.toggle('is-hidden', isMentor);
        mentorField?.classList.toggle('is-hidden', !isMentor);
        photoField?.classList.toggle('is-hidden', isMentor);
        featuredField?.classList.toggle('is-hidden', isMentor);
    };

    sectionSelect.addEventListener('change', syncFields);
    syncFields();

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            const closeLink = modal.querySelector('.org-admin-modal-close');
            if (closeLink) {
                window.location.href = closeLink.href;
            }
        }
    });
})();
</script>
