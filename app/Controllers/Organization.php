<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LandingSettingModel;

class Organization extends BaseController
{
    public function index(): string
    {
        $settingModel = new LandingSettingModel();
        $raw = trim((string) $settingModel->getValue(LandingSettingModel::KEY_SHOW_INTERNS, '1'));

        $data = [
            'title'        => 'Organization - ASOG TBI',
            'heroSubtitle' => 'Our People',
            'heroTitle'    => 'Organization',
            'heroDesc'     => 'The team behind ASOG TBI — leadership, staff, and mentors driving innovation forward.',
            'showInternsSection' => $raw !== '0',
        ];

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('organization/list', $data)
            . view('templates/footer');
    }
}