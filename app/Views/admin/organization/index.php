<link rel="stylesheet" href="<?= base_url('assets/css/adminOrganization.css') ?>">



<div class="org-admin-toolbar">

    <div>

        <span class="org-admin-count"><?= array_sum(array_map('count', $membersBySection ?? [])) ?> members</span>

        <p>Manage team members shown on the public Organization page.</p>

    </div>

    <div class="org-admin-toolbar-actions">

        <a href="<?= site_url('organization') ?>" target="_blank" rel="noopener" class="btn btn-o">View page</a>

        <?php if (($activeSection ?? '') !== 'mentor'): ?>
            <a href="<?= site_url('admin/organization?' . http_build_query(['section' => $activeSection ?? 'core_team', 'modal' => 'add'])) ?>" class="btn btn-p">Add member</a>
        <?php endif; ?>

    </div>

</div>



<div class="org-admin-tabs">

    <?php foreach (($sectionLabels ?? []) as $sectionKey => $sectionLabel): ?>

        <a href="<?= site_url('admin/organization?section=' . $sectionKey) ?>"

           class="org-admin-tab <?= ($activeSection ?? '') === $sectionKey ? 'on' : '' ?>">

            <?= esc($sectionLabel) ?>

            <span><?= count($membersBySection[$sectionKey] ?? []) ?></span>

        </a>

    <?php endforeach; ?>

</div>



<?php if (($activeSection ?? '') === 'mentor'): ?>
    <div class="org-admin-mentor-groups">

        <?php foreach (($mentorGroups ?? []) as $group): ?>

            <?php

                $category = $group['category'];

                $members = $group['members'];

                $lastIndex = count($members) - 1;

                $categoryId = 'mentor-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($category));

            ?>

            <section id="<?= esc($categoryId) ?>" class="org-admin-mentor-group<?= ($activeMentorCategory ?? '') === $category ? ' is-focused' : '' ?>">

                <div class="org-admin-mentor-head">

                    <div>

                        <span class="org-admin-mentor-kicker">Mentor area</span>

                        <h3><?= esc($category) ?></h3>

                    </div>

                    <a href="<?= site_url('admin/organization?' . http_build_query(['section' => 'mentor', 'category' => $category, 'modal' => 'add'])) ?>" class="btn btn-o btn-s">Add mentor</a>

                </div>



                <?php if (empty($members)): ?>

                    <div class="org-admin-empty org-admin-empty-compact">

                        <span>No mentors in this area yet.</span>

                    </div>

                <?php else: ?>

                    <div class="org-admin-list">

                        <?php foreach ($members as $index => $member): ?>

                            <?= view('admin/organization/_member_row', [

                                'member' => $member,

                                'activeSection' => 'mentor',

                                'isFirst' => $index === 0,

                                'isLast' => $index === $lastIndex,

                            ]) ?>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </section>

        <?php endforeach; ?>

    </div>

<?php else: ?>

    <?php

        $members = $membersBySection[$activeSection] ?? [];

        $lastIndex = count($members) - 1;

    ?>



    <?php if (empty($members)): ?>

        <div class="org-admin-empty">

            <strong>No members in this section</strong>

            <span>Add the first member using the button above.</span>

        </div>

    <?php else: ?>

        <div class="org-admin-list">

            <?php foreach ($members as $index => $member): ?>

                <?= view('admin/organization/_member_row', [

                    'member' => $member,

                    'activeSection' => $activeSection,

                    'isFirst' => $index === 0,

                    'isLast' => $index === $lastIndex,

                ]) ?>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

<?php endif; ?>

<?php if (! empty($isModalOpen)): ?>
    <?= view('admin/organization/_member_modal', [
        'modalMember' => $modalMember ?? null,
        'defaultSection' => $defaultSection ?? ($activeSection ?? 'core_team'),
        'defaultCategory' => $defaultCategory ?? ($activeMentorCategory ?? ''),
        'mentorCategories' => $mentorCategories ?? [],
    ]) ?>
<?php endif; ?>
