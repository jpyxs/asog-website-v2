<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LandingSettingModel;

class GamesAdmin extends BaseController
{
    public function index()
    {
        $settingModel = new LandingSettingModel();
        $raw = trim((string) $settingModel->getValue(LandingSettingModel::KEY_GUESS_STARTUP_ENABLED, '1'));
        $isGuessStartupEnabled = $raw !== '0';

        $data = [
            'pageTitle' => 'Games',
            'activePage' => 'games',
            'isGuessStartupEnabled' => $isGuessStartupEnabled,
        ];

        return view('admin/layout/header', $data)
            . view('admin/games/index', $data)
            . view('admin/layout/footer');
    }

    public function updateGuessStartupAvailability()
    {
        $settingModel = new LandingSettingModel();
        $enabled = $this->request->getPost('guessStartupEnabled') === '1';

        if (! $settingModel->setValue(LandingSettingModel::KEY_GUESS_STARTUP_ENABLED, $enabled ? '1' : '0')) {
            setToast('error', 'Unable to save game availability setting.');
            return redirect()->to(site_url('admin/games'));
        }

        $status = $enabled ? 'enabled' : 'disabled';
        setToast('success', 'Guess the Startup game is now ' . $status . '.');

        return redirect()->to(site_url('admin/games'));
    }
}
