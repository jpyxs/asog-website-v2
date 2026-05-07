<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class About extends BaseController
{
    public function index(): string
    {
        $data = [
            'title'        => 'About ASOG TBI',
            'heroSubtitle' => 'Get to Know Us',
            'heroTitle'    => 'About ASOG TBI',
            'heroDesc'     => 'Bicol Region\'s Premier AI & Engineering Technology Business Incubator at Camarines Sur Polytechnic Colleges.',
        ];

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('about/index', $data)
            . view('templates/footer');
    }

    public function logo(): RedirectResponse
    {
        return redirect()->to(site_url('about') . '#about-panel-4');
    }
}