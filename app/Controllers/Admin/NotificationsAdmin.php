<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminNotificationModel;

class NotificationsAdmin extends BaseController
{
    private const PAGE_SIZE = 8;

    private AdminNotificationModel $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new AdminNotificationModel();
    }

    public function index()
    {
        $adminId = (int) session()->get('admin_id');
        $role = (string) session()->get('admin_role');

        if ($adminId <= 0) {
            return $this->response->setStatusCode(401)->setJSON([
                'error' => 'Not signed in.',
            ]);
        }

        $offset = max(0, (int) $this->request->getGet('offset'));
        $limit = self::PAGE_SIZE;
        $rows = $this->notificationModel->getLatestForAdmin($adminId, $role, $limit + 1, $offset);
        $hasMore = count($rows) > $limit;
        $rows = array_slice($rows, 0, $limit);
        $unreadCount = $this->notificationModel->countUnreadForAdmin($adminId, $role);

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setJSON([
                'success' => true,
                'items' => self::formatNotifications($rows),
                'unreadCount' => $unreadCount,
                'hasMore' => $hasMore,
                'nextOffset' => $offset + count($rows),
            ]);
    }

    public function markRead(int $id)
    {
        $adminId = (int) session()->get('admin_id');
        $role = (string) session()->get('admin_role');
        $notification = $this->notificationModel->find($id);

        if (! $notification || $adminId <= 0 || ! $this->notificationModel->canAdminSee($id, $adminId, $role)) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Notification not found.',
            ]);
        }

        $this->notificationModel->markReadForAdmin($id, $adminId);

        return $this->response->setJSON([
            'success' => true,
            'unreadCount' => $this->notificationModel->countUnreadForAdmin($adminId, $role),
        ]);
    }

    public function markAllRead()
    {
        $adminId = (int) session()->get('admin_id');
        $role = (string) session()->get('admin_role');
        if ($adminId <= 0) {
            return $this->response->setStatusCode(401)->setJSON([
                'error' => 'Not signed in.',
            ]);
        }

        $this->notificationModel->markAllReadForAdmin($adminId, $role);

        return $this->response->setJSON([
            'success' => true,
            'unreadCount' => $this->notificationModel->countUnreadForAdmin($adminId, $role),
        ]);
    }

    public static function formatNotifications(array $notifications): array
    {
        return array_map(static function (array $notification): array {
            $notificationId = (int) ($notification['id'] ?? 0);
            $created = (string) ($notification['createdAt'] ?? '');

            return [
                'id'        => $notificationId,
                'type'      => (string) ($notification['type'] ?? 'system_update'),
                'title'     => (string) ($notification['title'] ?? 'Notification'),
                'body'      => (string) ($notification['body'] ?? ''),
                'link'      => (string) ($notification['link'] ?? site_url('admin')),
                'isRead'    => ! empty($notification['userReadAt']) || ! empty($notification['isRead']),
                'timeLabel' => $created !== '' && strtotime($created) !== false
                    ? date('M j, g:i A', strtotime($created))
                    : '',
                'readUrl'   => site_url('admin/notifications/' . $notificationId . '/read'),
            ];
        }, $notifications);
    }
}
