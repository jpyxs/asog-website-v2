<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Organization extends BaseController
{
    public function index(): string
    {
        $data = [
            'title'        => 'Organization - ASOG TBI',
            'heroSubtitle' => 'Our People',
            'heroTitle'    => 'Organization',
            'heroDesc'     => 'The team behind ASOG TBI — leadership, staff, and mentors driving innovation forward.',
        ];

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('organization/list', $data)
            . view('templates/footer');
    }
}