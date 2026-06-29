<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class MessagesAdmin extends BaseController
{
    public function index()
    {
        $view   = $this->request->getGet('view') ?? 'inbox';
        $view   = in_array($view, ['inbox', 'archived'], true) ? $view : 'inbox';
        $search = trim($this->request->getGet('search') ?? '');
        $date   = $this->request->getGet('date') ?? 'all';
        $date   = in_array($date, ['all', 'today', 'week', 'month'], true) ? $date : 'all';
        $page   = max(1, (int) ($this->request->getGet('page') ?? 1));

        $result = $this->contactModel->getFiltered(
            $view === 'archived' ? 1 : 0,
            $search,
            $date,
            $page,
            10
        );

        $data = [
            'pageTitle'     => 'Messages',
            'activePage'    => 'messages',
            'messages'      => $result['messages'],
            'total'         => $result['total'],
            'currentPage'   => $result['page'],
            'totalPages'    => $result['totalPages'],
            'perPage'       => $result['perPage'],
            'counts'        => $this->contactModel->getCounts(),
            'activeView'    => $view,
            'currentSearch' => $search,
            'currentDate'   => $date,
        ];

        return view('admin/layout/header', $data)
             . view('admin/messages/index', $data)
             . view('admin/layout/footer');
    }

    public function show(int $id)
    {
        $msg = $this->contactModel->find($id);

        if (! $msg) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Message not found.']);
        }

        if (! $msg['isRead']) {
            $this->contactModel->markRead($id);
            $msg['isRead'] = 1;
        }

        return $this->response->setJSON($msg);
    }

    public function toggleRead(int $id)
    {
        $msg = $this->contactModel->find($id);

        if (! $msg) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Message not found.']);
        }

        $newState = $msg['isRead'] ? 0 : 1;
        $this->contactModel->update($id, ['isRead' => $newState]);

        return $this->response->setJSON([
            'success' => true,
            'isRead'  => $newState,
            'message' => $newState ? 'Marked as read.' : 'Marked as unread.',
        ]);
    }

    public function delete(int $id)
    {
        $msg = $this->contactModel->find($id);

        if (! $msg) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Message not found.']);
        }

        $this->contactModel->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Message deleted.',
        ]);
    }

    public function bulkAction()
    {
        $body   = $this->request->getJSON(true) ?? [];
        $action = trim((string) ($body['action'] ?? ''));
        $ids    = array_values(array_filter(array_map('intval', (array) ($body['ids'] ?? []))));

        $validActions = ['mark_read', 'mark_unread', 'delete', 'archive', 'unarchive'];
        if (empty($ids) || ! in_array($action, $validActions, true)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid request.']);
        }

        $count = count($ids);

        match ($action) {
            'mark_read'   => $this->contactModel->bulkMarkRead($ids),
            'mark_unread' => $this->contactModel->bulkMarkUnread($ids),
            'delete'      => $this->contactModel->bulkDelete($ids),
            'archive'     => $this->contactModel->bulkArchive($ids),
            'unarchive'   => $this->contactModel->bulkUnarchive($ids),
        };

        $labels = [
            'mark_read'   => $count . ' message' . ($count !== 1 ? 's' : '') . ' marked as read.',
            'mark_unread' => $count . ' message' . ($count !== 1 ? 's' : '') . ' marked as unread.',
            'delete'      => $count . ' message' . ($count !== 1 ? 's' : '') . ' deleted.',
            'archive'     => $count . ' message' . ($count !== 1 ? 's' : '') . ' archived.',
            'unarchive'   => $count . ' message' . ($count !== 1 ? 's' : '') . ' moved to inbox.',
        ];

        return $this->response->setJSON(['success' => true, 'message' => $labels[$action]]);
    }
}