<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    /**
     * Render the admin dashboard overview.
     *
     * Aggregates post, application, and message metrics for display.
     */
     
    public function index()
    {
        $role       = (string) session()->get('admin_role');
        $postCounts = $this->postModel->getCounts();

        $data = [
            'pageTitle'      => 'Dashboard',
            'activePage'     => 'dashboard',
            'role'           => $role,
            'totalPosts'     => $postCounts['total'],
            'publishedPosts' => $postCounts['published'],
            'draftPosts'     => $postCounts['drafts'],
            'featuredPosts'  => $postCounts['featured'],
            'recentPosts'    => $this->postModel->orderBy('createdAt', 'DESC')->findAll(5),
        ];

        if (in_array($role, ['admin', 'superadmin'], true)) {
            $appCounts                = $this->applicationModel->getCounts();
            $data['totalApps']        = $appCounts['total'];
            $data['pendingApps']      = $appCounts['pending'];
            $data['acceptedApps']     = $appCounts['accepted'];
            $data['rejectedApps']     = $appCounts['rejected'];
            $data['recentApps']       = $this->applicationModel->getAll(5);
            $data['unreadMessages']   = $this->contactModel->countUnread();
            $data['recentIncubatees'] = $this->incubateeModel->orderBy('createdAt', 'DESC')->findAll(5);
        }

        if ($role === 'superadmin') {
            $data['recentAdmins'] = $this->adminModel->orderBy('createdAt', 'DESC')->findAll(5);
        }

        return view('admin/layout/header', $data)
             . view('admin/dashboard', $data)
             . view('admin/layout/footer');
    }
}