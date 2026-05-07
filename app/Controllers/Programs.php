<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Programs extends BaseController
{
    public function index(): string
    {
        $data = [
            'title'        => 'Programs & Services - ASOG TBI',
            'heroSubtitle' => 'What We Offer',
            'heroTitle'    => 'Programs & Services',
            'heroDesc'     => 'Explore the programs, services, and facilities that power innovation at ASOG TBI.',
        ];

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('programs/list', $data)
            . view('templates/footer');
    }

    public function services(): string
    {
        $data = [
            'title'        => 'Services Offered - ASOGTBI',
            'heroSubtitle' => 'Comprehensive Support',
            'heroTitle'    => 'Services Offered',
            'heroDesc'     => 'A comprehensive range of capacity-building services designed to support startups and MSMEs at every stage.',
        ];

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('services/list', $data)
            . view('templates/footer');
    }
}