<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * ApplicationsAdmin — Review & manage incubatee applications.
 *
 * Routes: admin/applications, admin/applications/(:num), etc.
 */
class ApplicationsAdmin extends BaseController
{

    /**
     * List all applications received by the TBI.
     *
     * Supports text search, status filter, and column sorting via GET params.
     */

    public function index()
    {
        $search    = $this->request->getGet('search') ?? '';
        $status    = $this->request->getGet('status') ?? 'active';
        $sort      = $this->request->getGet('sort') ?? 'createdAt';
        $direction = $this->request->getGet('direction') ?? 'DESC';
        $perPage   = 20;
        $page      = max(1, (int) ($this->request->getGet('page') ?? 1));
        $total     = $this->applicationModel->countFilteredApplications($search, $status);
        $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 1;
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        $data = [
            'pageTitle'    => 'Applications',
            'activePage'   => 'applications',
            'applications' => $this->applicationModel->getFilteredApplications($search, $status, $sort, $direction, $perPage, $offset),
            'counts'       => $this->applicationModel->getCounts(),
            'search'       => $search,
            'status'       => $status,
            'sort'         => $sort,
            'direction'    => $direction,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'total'        => $total,
            'perPage'      => $perPage,
        ];

        return view('admin/layout/header', $data)
             . view('admin/applications/index', $data)
             . view('admin/layout/footer');
    }

    /**
     * Show a single application as JSON.
     *
     * Used by the review modal to load application details.
     */

    public function show(int $id)
    {
        $app = $this->applicationModel->find($id);

        if (! $app) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Application not found.']);
        }

        return $this->response->setJSON($app);
    }

    /**
     * Update application status.
     *
     * Accepts the new status from the request payload, validates it,
     * and returns a JSON success response.
     */

    public function updateStatus(int $id)
    {
        $app = $this->applicationModel->find($id);

        if (! $app) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Application not found.']);
        }

        $status = $this->request->getJSON(true)['status'] ?? '';

        if (! $this->applicationModel->updateStatus($id, $status)) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Invalid status value.']);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Application marked as ' . $status . '.',
        ]);
    }

    /**
     * Toggle the archived state of a single application.
     */

    public function toggleArchive(int $id)
    {
        $app = $this->applicationModel->find($id);

        if (! $app) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Application not found.']);
        }

        $newState = $app['isArchived'] ? 0 : 1;
        $this->applicationModel->update($id, ['isArchived' => $newState]);

        return $this->response->setJSON([
            'success'    => true,
            'isArchived' => $newState,
            'message'    => 'Application ' . ($newState ? 'archived' : 'restored') . '.',
        ]);
    }

    /**
     * Perform a bulk action on selected applications.
     *
     * Accepts: ids (array), action (pending|accepted|rejected|archive|unarchive)
     */

    public function bulk()
    {
        $payload = $this->request->getJSON(true);
        $ids     = $payload['ids']    ?? [];
        $action  = $payload['action'] ?? '';

        if (empty($ids) || ! is_array($ids)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'No applications selected.']);
        }

        $validActions = ['pending', 'accepted', 'rejected', 'archive', 'unarchive'];
        if (! in_array($action, $validActions, true)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid bulk action.']);
        }

        if (in_array($action, ['archive', 'unarchive'], true)) {
            $isArchived = ($action === 'archive') ? 1 : 0;
            $this->applicationModel->whereIn('id', $ids)->set(['isArchived' => $isArchived])->update();
        } else {
            $this->applicationModel->whereIn('id', $ids)->set(['applicationStatus' => $action])->update();
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Bulk action applied.',
        ]);
    }
}