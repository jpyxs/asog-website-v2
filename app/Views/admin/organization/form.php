<?= view('admin/organization/_member_modal', [
    'modalMember' => $member ?? null,
    'defaultSection' => $defaultSection ?? 'core_team',
    'defaultCategory' => $defaultCategory ?? '',
    'mentorCategories' => $mentorCategories ?? [],
]) ?>
