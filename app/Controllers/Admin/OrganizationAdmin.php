<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class OrganizationAdmin extends BaseController
{
    public function index()
    {
        $data = [
            'pageTitle'  => 'Organization',
            'activePage' => 'organization',
        ];

        return view('admin/layout/header', $data)
            . view('admin/organization/index', $data)
            . view('admin/layout/footer');
    }
}
