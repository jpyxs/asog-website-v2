<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * AdminsManagement — CRUD for admin accounts and Google OAuth authorization.
 */
class AdminsManagement extends BaseController
{
    /**
     * List all admin accounts with their Google authorization status.
     */
    public function index()
    {
        $search    = trim((string) ($this->request->getGet('search') ?? ''));
        $status    = trim((string) ($this->request->getGet('status') ?? 'all'));
        $status    = in_array($status, ['all', 'active', 'inactive'], true) ? $status : 'all';
        $role      = trim((string) ($this->request->getGet('role') ?? 'all'));
        $role      = in_array($role, ['all', 'superadmin', 'admin'], true) ? $role : 'all';
        $sort      = trim((string) ($this->request->getGet('sort') ?? 'fullName'));
        $direction = trim((string) ($this->request->getGet('direction') ?? 'ASC'));
        $page      = max(1, (int) ($this->request->getGet('page') ?? 1));

        $result = $this->adminModel->getFiltered($search, $status, $role, $sort, $direction, $page, 10);
        $counts = $this->adminModel->getCounts();

        $data = [
            'pageTitle'   => 'Accounts',
            'activePage'  => 'admins',
            'admins'      => $result['admins'],
            'total'       => $result['total'],
            'currentPage' => $result['page'],
            'totalPages'  => $result['totalPages'],
            'perPage'     => $result['perPage'],
            'search'      => $search,
            'status'      => $status,
            'role'        => $role,
            'sort'        => $sort,
            'direction'   => $direction,
            'counts'      => $counts,
        ];

        return view('admin/layout/header', $data)
             . view('admin/admins/index', $data)
             . view('admin/layout/footer');
    }

    /**
     * Show form to create a new admin account.
     */
    public function create()
    {
        $data = [
            'pageTitle'  => 'New Account',
            'activePage' => 'admins',
            'admin'      => null,
        ];

        return view('admin/layout/header', $data)
             . view('admin/admins/form', $data)
             . view('admin/layout/footer');
    }

    /**
     * Store a new admin account.
     */
    public function store()
    {
        $email = trim((string) $this->request->getPost('email'));
        $role  = trim((string) $this->request->getPost('role')) ?: 'superadmin';

        if ($this->adminModel->isEmailTaken($email)) {
            setToast('error', 'That email is already used by another admin.');
            return redirect()->back()->withInput();
        }

        // Generate temp password
        $tempPassword = bin2hex(random_bytes(8));

        $data = [
            // Full name is synced from Google profile on first successful OAuth login.
            'fullName'    => 'Pending Google Name',
            'email'       => $email,
            'password'    => $tempPassword,
            'role'        => $role,
            'isActive'    => 1,
        ];

        if (!$this->adminModel->insert($data)) {
            setToast('error', 'Error: ' . implode(', ', $this->adminModel->errors()));
            return redirect()->back()->withInput();
        }

        setToast('success', 'Account added. Email: ' . $email . ' | Role: ' . ucfirst($role));
        return redirect()->to('admin/accounts');
    }

    /**
     * Show form to edit an admin account.
     */
    public function edit($id = null)
    {
        $id = (int) $id;
        if ($id === 0) {
            return redirect()->to('admin/accounts')->with('error', 'Invalid admin ID.');
        }

        $admin = $this->adminModel->find($id);
        if ($admin === null) {
            return redirect()->to('admin/accounts')->with('error', 'Admin not found.');
        }

        $data = [
            'pageTitle'  => 'Edit Account',
            'activePage' => 'admins',
            'admin'      => $admin,
        ];

        return view('admin/layout/header', $data)
             . view('admin/admins/form', $data)
             . view('admin/layout/footer');
    }

    /**
     * Update admin account.
     */
    public function update($id = null)
    {
        $id = (int) $id;
        if ($id === 0) {
            return redirect()->to('admin/accounts')->with('error', 'Invalid admin ID.');
        }

        $admin = $this->adminModel->find($id);
        if ($admin === null) {
            return redirect()->to('admin/accounts')->with('error', 'Admin not found.');
        }

        $email       = trim((string) $this->request->getPost('email'));
        $googleEmail = trim((string) $this->request->getPost('googleEmail'));
        $googleSub   = trim((string) $this->request->getPost('googleSub'));
        $role        = trim((string) $this->request->getPost('role')) ?: 'superadmin';
        $isActive    = (bool) $this->request->getPost('isActive');

        if ($this->adminModel->isEmailTaken($email, $id)) {
            setToast('error', 'That email is already used by another admin.');
            return redirect()->back()->withInput();
        }

        $updateData = [
            'email'       => $email,
            'googleEmail' => $googleEmail === '' ? null : $googleEmail,
            'googleSub'   => $googleSub === '' ? null : $googleSub,
            'role'        => $role,
            'isActive'    => $isActive ? 1 : 0,
        ];

        if (!$this->adminModel->update($id, $updateData)) {
            setToast('error', 'Error: ' . implode(', ', $this->adminModel->errors()));
            return redirect()->back()->withInput();
        }

        setToast('success', 'Account updated.');
        return redirect()->to('admin/accounts');
    }

    /**
     * Delete an admin account.
     */
    public function delete($id = null)
    {
        $id = (int) $id;
        if ($id === 0) {
            return redirect()->to('admin/accounts')->with('error', 'Invalid admin ID.');
        }

        $admin = $this->adminModel->find($id);
        if ($admin === null) {
            return redirect()->to('admin/accounts')->with('error', 'Admin not found.');
        }

        if ($this->adminModel->delete($id)) {
            setToast('success', 'Admin account deleted.');
        } else {
            setToast('error', 'Failed to delete admin account.');
        }

        return redirect()->to('admin/accounts');
    }
}
