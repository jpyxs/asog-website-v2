<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LandingSettingModel;

class OrganizationAdmin extends BaseController
{
    public function index()
    {
        $settingModel = new LandingSettingModel();
        $raw = trim((string) $settingModel->getValue(LandingSettingModel::KEY_SHOW_INTERNS, '1'));
        $showInternsSection = $raw !== '0';

        $data = [
            'pageTitle'          => 'Organization',
            'activePage'         => 'organization',
            'showInternsSection' => $showInternsSection,
        ];

        return view('admin/layout/header', $data)
            . view('admin/organization/index', $data)
            . view('admin/layout/footer');
    }

    public function updateInternsVisibility()
    {
        $settingModel = new LandingSettingModel();
        $enabled = $this->request->getPost('showInternsSection') === '1';

        if (! $settingModel->setValue(LandingSettingModel::KEY_SHOW_INTERNS, $enabled ? '1' : '0')) {
            setToast('error', 'Unable to save interns section setting.');
            return redirect()->to(site_url('admin/organization'));
        }

        $status = $enabled ? 'visible' : 'hidden';
        setToast('success', 'Interns section is now ' . $status . '.');

        return redirect()->to(site_url('admin/organization'));
    }
}