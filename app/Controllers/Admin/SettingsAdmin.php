<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LandingSettingModel;

class SettingsAdmin extends BaseController
{
    public function index()
    {
        $settingModel = new LandingSettingModel();

        $guessStartupRaw = trim((string) $settingModel->getValue(LandingSettingModel::KEY_GUESS_STARTUP_ENABLED, '1'));
        $isGuessStartupEnabled = $guessStartupRaw !== '0';

        $internsRaw = trim((string) $settingModel->getValue(LandingSettingModel::KEY_SHOW_INTERNS, '1'));
        $showInternsSection = $internsRaw !== '0';

        $activeCohortNames = $this->cohortModel->getActiveNames();
        $selectedLandingFilter = trim((string) $settingModel->getValue(
            LandingSettingModel::KEY_INCUBATEES_FILTER,
            'all'
        ));

        if ($selectedLandingFilter === '' || ($selectedLandingFilter !== 'all' && ! in_array($selectedLandingFilter, $activeCohortNames, true))) {
            $selectedLandingFilter = 'all';
        }

        $data = [
            'pageTitle'             => 'Settings',
            'activePage'            => 'settings',
            'isGuessStartupEnabled' => $isGuessStartupEnabled,
            'showInternsSection'    => $showInternsSection,
            'landingFilterOptions'  => $activeCohortNames,
            'selectedLandingFilter' => $selectedLandingFilter,
        ];

        return view('admin/layout/header', $data)
            . view('admin/settings/index', $data)
            . view('admin/layout/footer');
    }

    public function updateGuessStartupAvailability()
    {
        $settingModel = new LandingSettingModel();
        $enabled = $this->request->getPost('guessStartupEnabled') === '1';

        if (! $settingModel->setValue(LandingSettingModel::KEY_GUESS_STARTUP_ENABLED, $enabled ? '1' : '0')) {
            setToast('error', 'Unable to save game availability setting.');
            return redirect()->to(site_url('admin/settings'));
        }

        $status = $enabled ? 'enabled' : 'disabled';
        setToast('success', 'Guess the Startup game is now ' . $status . '.');

        return redirect()->to(site_url('admin/settings'));
    }

    public function updateInternsVisibility()
    {
        $settingModel = new LandingSettingModel();
        $enabled = $this->request->getPost('showInternsSection') === '1';

        if (! $settingModel->setValue(LandingSettingModel::KEY_SHOW_INTERNS, $enabled ? '1' : '0')) {
            setToast('error', 'Unable to save interns section setting.');
            return redirect()->to(site_url('admin/settings'));
        }

        $status = $enabled ? 'visible' : 'hidden';
        setToast('success', 'Interns section is now ' . $status . '.');

        return redirect()->to(site_url('admin/settings'));
    }

    public function updateLandingFilter()
    {
        $selected = trim((string) ($this->request->getPost('landingCohortFilter') ?? 'all'));

        $allowed = ['all'];
        foreach ($this->cohortModel->getActiveNames() as $cohortName) {
            $allowed[] = (string) $cohortName;
        }

        if (! in_array($selected, $allowed, true)) {
            setToast('error', 'Invalid cohort selection.');
            return redirect()->to(site_url('admin/settings'));
        }

        $settingModel = new LandingSettingModel();
        if (! $settingModel->setValue(LandingSettingModel::KEY_INCUBATEES_FILTER, $selected)) {
            setToast('error', 'Unable to save landing cohort setting.');
            return redirect()->to(site_url('admin/settings'));
        }

        $label = $selected === 'all' ? 'All Cohorts' : $selected;
        setToast('success', 'Landing incubatees set to ' . $label . '.');

        return redirect()->to(site_url('admin/settings'));
    }
}
