<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ImageUpload;
use App\Models\OrganizationMemberModel;

class OrganizationAdmin extends BaseController
{
    private const PHOTO_MAX_BYTES = 2097152; // 2 MB

    private function memberModel(): OrganizationMemberModel
    {
        return new OrganizationMemberModel();
    }

    public function index()
    {
        $memberModel = $this->memberModel();
        $activeSection = trim((string) ($this->request->getGet('section') ?? OrganizationMemberModel::SECTION_CORE_TEAM));

        if (! in_array($activeSection, OrganizationMemberModel::SECTIONS, true)) {
            $activeSection = OrganizationMemberModel::SECTION_CORE_TEAM;
        }

        $activeMentorCategory = '';
        if ($activeSection === OrganizationMemberModel::SECTION_MENTOR) {
            $activeMentorCategory = trim((string) ($this->request->getGet('category') ?? ''));
            if ($activeMentorCategory !== '' && ! in_array($activeMentorCategory, OrganizationMemberModel::MENTOR_CATEGORIES, true)) {
                $activeMentorCategory = '';
            }
        }

        $modalMode = trim((string) ($this->request->getGet('modal') ?? ''));
        if (! in_array($modalMode, ['add', 'edit'], true)) {
            $modalMode = '';
        }

        $modalMemberId = (int) ($this->request->getGet('memberId') ?? 0);
        $modalMember = null;
        if ($modalMode === 'edit' && $modalMemberId > 0) {
            $modalMember = $memberModel->find($modalMemberId);
            if (! $modalMember) {
                $modalMode = '';
            }
        }

        $isModalOpen = $modalMode !== '';

        $membersBySection = [];
        foreach (OrganizationMemberModel::SECTIONS as $section) {
            $membersBySection[$section] = $memberModel->getBySection($section);
        }

        $data = [
            'pageTitle'           => 'Organization',
            'activePage'          => 'organization',
            'activeSection'       => $activeSection,
            'activeMentorCategory'=> $activeMentorCategory,
            'modalMode'           => $modalMode,
            'modalMember'         => $modalMember,
            'isModalOpen'         => $isModalOpen,
            'membersBySection'    => $membersBySection,
            'mentorGroups'        => $memberModel->getMentorsGroupedForAdmin(),
            'sectionLabels'       => array_combine(
                OrganizationMemberModel::SECTIONS,
                array_map([OrganizationMemberModel::class, 'sectionLabel'], OrganizationMemberModel::SECTIONS)
            ),
            'defaultSection'      => $modalMode === 'edit' && is_array($modalMember)
                ? (string) ($modalMember['section'] ?? $activeSection)
                : $activeSection,
            'defaultCategory'     => $modalMode === 'edit' && is_array($modalMember)
                ? (string) ($modalMember['mentorCategory'] ?? '')
                : $activeMentorCategory,
            'mentorCategories'    => OrganizationMemberModel::MENTOR_CATEGORIES,
        ];

        return view('admin/layout/header', $data)
            . view('admin/organization/index', $data)
            . view('admin/layout/footer');
    }

    public function create()
    {
        $section = trim((string) ($this->request->getGet('section') ?? OrganizationMemberModel::SECTION_CORE_TEAM));
        if (! in_array($section, OrganizationMemberModel::SECTIONS, true)) {
            $section = OrganizationMemberModel::SECTION_CORE_TEAM;
        }

        $defaultCategory = trim((string) ($this->request->getGet('category') ?? ''));
        if ($section !== OrganizationMemberModel::SECTION_MENTOR) {
            $defaultCategory = '';
        } elseif ($defaultCategory !== '' && ! in_array($defaultCategory, OrganizationMemberModel::MENTOR_CATEGORIES, true)) {
            $defaultCategory = '';
        }

        return redirect()->to($this->organizationListUrl($section, $defaultCategory, [
            'modal' => 'add',
        ]));
    }

    public function store()
    {
        $memberModel = $this->memberModel();
        $payload = $this->memberPayload();

        try {
            $photoPath = $this->handlePhotoUpload();
            if ($photoPath !== null) {
                $payload['photoPath'] = $photoPath;
            }
        } catch (\RuntimeException $e) {
            setToast('error', $e->getMessage());
            return redirect()->to($this->organizationListUrl($payload['section'], $payload['mentorCategory'] ?? null, [
                'modal' => 'add',
            ]))->withInput();
        }

        $payload['sortOrder'] = $memberModel->getNextSortOrder(
            $payload['section'],
            $payload['section'] === OrganizationMemberModel::SECTION_MENTOR ? ($payload['mentorCategory'] ?? null) : null
        );

        if (! $memberModel->insert($payload)) {
            setToast('error', $this->validationMessage($memberModel));
            return redirect()->to($this->organizationListUrl($payload['section'], $payload['mentorCategory'] ?? null, [
                'modal' => 'add',
            ]))->withInput();
        }

        setToast('success', 'Member added.');

        return redirect()->to($this->organizationListUrl($payload['section'], $payload['mentorCategory'] ?? null));
    }

    public function edit(int $id)
    {
        $memberModel = $this->memberModel();
        $member = $memberModel->find($id);
        if (! $member) {
            setToast('error', 'Member not found.');
            return redirect()->to(site_url('admin/organization'));
        }

        return redirect()->to($this->organizationListUrl(
            (string) $member['section'],
            (string) ($member['mentorCategory'] ?? ''),
            ['modal' => 'edit', 'memberId' => $id]
        ));
    }

    public function update(int $id)
    {
        $memberModel = $this->memberModel();
        $member = $memberModel->find($id);
        if (! $member) {
            setToast('error', 'Member not found.');
            return redirect()->to(site_url('admin/organization'));
        }

        $payload = $this->memberPayload();

        try {
            $photoPath = $this->handlePhotoUpload($member['photoPath'] ?? null);
            if ($photoPath !== null) {
                $payload['photoPath'] = $photoPath;
            }
        } catch (\RuntimeException $e) {
            setToast('error', $e->getMessage());
            return redirect()->to($this->organizationListUrl($payload['section'], $payload['mentorCategory'] ?? null, [
                'modal' => 'edit',
                'memberId' => $id,
            ]))->withInput();
        }

        if (! $memberModel->update($id, $payload)) {
            setToast('error', $this->validationMessage($memberModel));
            return redirect()->to($this->organizationListUrl($payload['section'], $payload['mentorCategory'] ?? null, [
                'modal' => 'edit',
                'memberId' => $id,
            ]))->withInput();
        }

        setToast('success', 'Member updated.');

        return redirect()->to($this->organizationListUrl($payload['section'], $payload['mentorCategory'] ?? null));
    }

    public function delete(int $id)
    {
        $memberModel = $this->memberModel();
        $member = $memberModel->find($id);
        if (! $member) {
            setToast('error', 'Member not found.');
            return redirect()->to(site_url('admin/organization'));
        }

        $section = (string) $member['section'];
        $mentorCategory = $section === OrganizationMemberModel::SECTION_MENTOR
            ? ($member['mentorCategory'] ?? null)
            : null;

        if (! $memberModel->delete($id)) {
            setToast('error', 'Unable to delete member.');
            return redirect()->to($this->organizationListUrl($section, $mentorCategory));
        }

        $this->deleteUploadedPhoto($member['photoPath'] ?? null);
        $memberModel->normalizeOrder($section, $mentorCategory);

        setToast('success', 'Member deleted.');

        return redirect()->to($this->organizationListUrl($section, $mentorCategory));
    }

    public function move(int $id, string $direction)
    {
        if (! in_array($direction, ['up', 'down'], true)) {
            setToast('error', 'Invalid position.');
            return redirect()->to(site_url('admin/organization'));
        }

        $memberModel = $this->memberModel();
        $member = $memberModel->find($id);
        if (! $member) {
            setToast('error', 'Member not found.');
            return redirect()->to(site_url('admin/organization'));
        }

        $section = (string) $member['section'];
        $mentorCategory = $section === OrganizationMemberModel::SECTION_MENTOR
            ? ($member['mentorCategory'] ?? null)
            : null;

        $memberModel->normalizeOrder($section, $mentorCategory);
        $members = $memberModel->getBySection($section, $mentorCategory);
        $currentIndex = null;

        foreach ($members as $index => $row) {
            if ((int) $row['id'] === $id) {
                $currentIndex = $index;
                break;
            }
        }

        if ($currentIndex === null) {
            setToast('error', 'Member not found.');
            return redirect()->to($this->organizationListUrl($section, $mentorCategory));
        }

        $targetIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;
        if (! isset($members[$targetIndex])) {
            return redirect()->to($this->organizationListUrl($section, $mentorCategory));
        }

        $current = $members[$currentIndex];
        $target = $members[$targetIndex];

        $this->db->transStart();
        $memberModel->update((int) $current['id'], ['sortOrder' => (int) $target['sortOrder']]);
        $memberModel->update((int) $target['id'], ['sortOrder' => (int) $current['sortOrder']]);
        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            setToast('error', 'Unable to move member.');
        } else {
            setToast('success', 'Order updated.');
        }

        return redirect()->to($this->organizationListUrl($section, $mentorCategory));
    }

    private function organizationListUrl(string $section, ?string $mentorCategory = null, array $query = []): string
    {
        $params = ['section' => $section];

        if ($section === OrganizationMemberModel::SECTION_MENTOR && $mentorCategory !== null && $mentorCategory !== '') {
            $params['category'] = $mentorCategory;
        }

        foreach ($query as $key => $value) {
            if ($value !== null && $value !== '') {
                $params[$key] = (string) $value;
            }
        }

        return site_url('admin/organization?' . http_build_query($params));
    }

    private function memberPayload(): array
    {
        $section = trim((string) $this->request->getPost('section'));
        $mentorCategory = trim((string) $this->request->getPost('mentorCategory'));

        return [
            'section'        => $section,
            'fullName'       => trim((string) $this->request->getPost('fullName')),
            'rolePrimary'    => trim((string) $this->request->getPost('rolePrimary')) ?: null,
            'roleSecondary'  => trim((string) $this->request->getPost('roleSecondary')) ?: null,
            'mentorCategory' => $section === OrganizationMemberModel::SECTION_MENTOR && $mentorCategory !== ''
                ? $mentorCategory
                : null,
            'isFeatured'     => $this->request->getPost('isFeatured') === '1' ? 1 : 0,
            'isPublished'    => $this->request->getPost('isPublished') === '1' ? 1 : 0,
        ];
    }

    private function handlePhotoUpload(?string $existingPath = null): ?string
    {
        $file = $this->request->getFile('photo');
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (! $file->isValid() || $file->hasMoved()) {
            throw new \RuntimeException('Invalid photo upload.');
        }

        $this->assertSquareImage($file, 'Photo');

        $uploader = new ImageUpload();
        $path = $uploader->upload($file, 'team', self::PHOTO_MAX_BYTES);

        if ($existingPath !== null && str_starts_with($existingPath, 'uploads/')) {
            $uploader->delete($existingPath);
        }

        return $path;
    }

    private function deleteUploadedPhoto(?string $path): void
    {
        if ($path !== null && str_starts_with($path, 'uploads/')) {
            (new ImageUpload())->delete($path);
        }
    }

    private function assertSquareImage(\CodeIgniter\HTTP\Files\UploadedFile $file, string $label): void
    {
        $dimensions = @getimagesize($file->getTempName());
        if ($dimensions === false || ! isset($dimensions[0], $dimensions[1])) {
            throw new \RuntimeException($label . ' must be a valid image.');
        }

        if ((int) $dimensions[0] !== (int) $dimensions[1]) {
            throw new \RuntimeException($label . ' must be square (1:1).');
        }
    }

    private function validationMessage(OrganizationMemberModel $memberModel): string
    {
        $errors = $memberModel->errors();

        return $errors === []
            ? 'Unable to save member.'
            : implode(' ', array_values($errors));
    }
}
