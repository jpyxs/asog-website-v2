<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    private const HIERARCHY = [
        'editor'     => 1,
        'admin'      => 2,
        'superadmin' => 3,
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $required = $arguments[0] ?? 'superadmin';
        $role     = (string) session()->get('admin_role');

        $requiredLevel = self::HIERARCHY[$required] ?? 99;
        $actualLevel   = self::HIERARCHY[$role] ?? 0;

        if ($actualLevel < $requiredLevel) {
            setToast('error', 'Access denied. Your role (' . ucfirst($role) . ') does not have permission to access that page.');
            return redirect()->to(site_url('admin'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
