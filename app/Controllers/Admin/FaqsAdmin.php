<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FaqModel;
use App\Models\LandingSettingModel;

class FaqsAdmin extends BaseController
{
    public function index()
    {
        $faqModel = new FaqModel();
        $settings = new LandingSettingModel();

        $data = [
            'pageTitle' => 'FAQs',
            'activePage' => 'faqs',
            'faqs' => $faqModel->getAllOrdered(),
            'faqTitle' => $settings->getValue(
                LandingSettingModel::KEY_APPLY_FAQ_TITLE,
                'A few things you might be wondering.'
            ),
            'faqIntro' => $settings->getValue(
                LandingSettingModel::KEY_APPLY_FAQ_INTRO,
                'Find quick answers about eligibility, requirements, and what happens after you submit your application.'
            ),
            'allowDuplicateEmails' => trim((string) $settings->getValue(
                LandingSettingModel::KEY_APPLY_ALLOW_DUPLICATE_EMAILS,
                '0'
            )) === '1',
        ];

        return view('admin/layout/header', $data)
            . view('admin/faqs/index', $data)
            . view('admin/layout/footer');
    }

    public function updateSection()
    {
        $title = trim((string) $this->request->getPost('faqTitle'));
        $intro = trim((string) $this->request->getPost('faqIntro'));
        $allowDuplicateEmails = $this->request->getPost('allowDuplicateEmails') === '1';

        if ($title === '' || mb_strlen($title) > 120) {
            setToast('error', 'FAQ heading is required and must be 120 characters or fewer.');
            return redirect()->back()->withInput();
        }

        if ($intro === '' || mb_strlen($intro) > 500) {
            setToast('error', 'FAQ introduction is required and must be 500 characters or fewer.');
            return redirect()->back()->withInput();
        }

        $settings = new LandingSettingModel();
        $this->db->transStart();
        $savedTitle = $settings->setValue(LandingSettingModel::KEY_APPLY_FAQ_TITLE, $title);
        $savedIntro = $settings->setValue(LandingSettingModel::KEY_APPLY_FAQ_INTRO, $intro);
        $savedDuplicateSetting = $settings->setValue(
            LandingSettingModel::KEY_APPLY_ALLOW_DUPLICATE_EMAILS,
            $allowDuplicateEmails ? '1' : '0'
        );
        $this->db->transComplete();

        if (! $savedTitle || ! $savedIntro || ! $savedDuplicateSetting || ! $this->db->transStatus()) {
            setToast('error', 'Unable to save the apply page settings.');
            return redirect()->back()->withInput();
        }

        setToast('success', 'Apply page settings updated.');

        return redirect()->to(site_url('admin/faqs'));
    }

    public function store()
    {
        $faqModel = new FaqModel();
        $data = $this->faqPayload();
        $data['sortOrder'] = $faqModel->getNextSortOrder();

        if (! $faqModel->insert($data)) {
            setToast('error', $this->validationMessage($faqModel));
            return redirect()->back()->withInput();
        }

        setToast('success', 'FAQ added.');

        return redirect()->to(site_url('admin/faqs'));
    }

    public function update(int $id)
    {
        $faqModel = new FaqModel();
        if (! $faqModel->find($id)) {
            setToast('error', 'FAQ not found.');
            return redirect()->to(site_url('admin/faqs'));
        }

        if (! $faqModel->update($id, $this->faqPayload())) {
            setToast('error', $this->validationMessage($faqModel));
            return redirect()->back()->withInput();
        }

        setToast('success', 'FAQ updated.');

        return redirect()->to(site_url('admin/faqs'));
    }

    public function move(int $id, string $direction)
    {
        if (! in_array($direction, ['up', 'down'], true)) {
            setToast('error', 'Invalid FAQ position.');
            return redirect()->to(site_url('admin/faqs'));
        }

        $faqModel = new FaqModel();
        $faqModel->normalizeOrder();
        $faqs = $faqModel->getAllOrdered();
        $currentIndex = null;

        foreach ($faqs as $index => $faq) {
            if ((int) $faq['id'] === $id) {
                $currentIndex = $index;
                break;
            }
        }

        if ($currentIndex === null) {
            setToast('error', 'FAQ not found.');
            return redirect()->to(site_url('admin/faqs'));
        }

        $targetIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;
        if (! isset($faqs[$targetIndex])) {
            return redirect()->to(site_url('admin/faqs'));
        }

        $current = $faqs[$currentIndex];
        $target = $faqs[$targetIndex];

        $this->db->transStart();
        $faqModel->update((int) $current['id'], ['sortOrder' => (int) $target['sortOrder']]);
        $faqModel->update((int) $target['id'], ['sortOrder' => (int) $current['sortOrder']]);
        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            setToast('error', 'Unable to move the FAQ.');
        } else {
            setToast('success', 'FAQ order updated.');
        }

        return redirect()->to(site_url('admin/faqs'));
    }

    public function delete(int $id)
    {
        $faqModel = new FaqModel();
        if (! $faqModel->find($id)) {
            setToast('error', 'FAQ not found.');
            return redirect()->to(site_url('admin/faqs'));
        }

        if (! $faqModel->delete($id)) {
            setToast('error', 'Unable to delete the FAQ.');
            return redirect()->to(site_url('admin/faqs'));
        }

        $faqModel->normalizeOrder();
        setToast('success', 'FAQ deleted.');

        return redirect()->to(site_url('admin/faqs'));
    }

    private function faqPayload(): array
    {
        return [
            'question' => trim((string) $this->request->getPost('question')),
            'answer' => trim((string) $this->request->getPost('answer')),
            'isPublished' => $this->request->getPost('isPublished') === '1' ? 1 : 0,
        ];
    }

    private function validationMessage(FaqModel $faqModel): string
    {
        $errors = $faqModel->errors();

        return $errors === []
            ? 'Unable to save the FAQ.'
            : implode(' ', array_values($errors));
    }
}
