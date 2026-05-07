<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Facilities extends BaseController
{
    public function index(): string
    {
        $data = [
            'title'        => 'Facilities - ASOG TBI',
            'heroSubtitle' => 'Our Spaces',
            'heroTitle'    => 'Facilities',
            'heroDesc'     => 'State-of-the-art spaces designed to fuel research, collaboration, and innovation.',
        ];

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('facilities/list', $data)
            . view('templates/footer');
    }
}