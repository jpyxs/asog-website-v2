<link rel="stylesheet" href="<?= base_url('assets/css/adminAdmins.css') ?>">

<div class="toolbar">
    <span class="count"><?= count($admins ?? []) ?> accounts</span>
    <div class="toolbar-actions">
        <a href="<?= site_url('admin/accounts/create') ?>" class="btn btn-p">New Account</a>
    </div>
</div>

<?php if (empty($admins)): ?>
    <div class="empty-row">No accounts yet. <a href="<?= site_url('admin/accounts/create') ?>">Create one.</a></div>
<?php else: ?>
    <table class="admins-tbl">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Google Account</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last Login</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($admins as $admin): ?>
            <tr>
                <td><strong><?= esc($admin['fullName']) ?></strong></td>
                <td><?= esc($admin['email']) ?></td>
                <td>
                    <?php if (!empty($admin['googleEmail'])): ?>
                        <span class="tag tag-linked">
                            <?= esc($admin['googleEmail']) ?>
                        </span>
                    <?php else: ?>
                        <span class="tag tag-unlinked">Not linked</span>
                    <?php endif; ?>
                </td>
                <td><span class="tag tag-role"><?= ucfirst(esc($admin['role'])) ?></span></td>
                <td>
                    <?php if ($admin['isActive']): ?>
                        <span class="tag tag-active">Active</span>
                    <?php else: ?>
                        <span class="tag tag-inactive">Inactive</span>
                    <?php endif; ?>
                </td>
                <td style="font-size:.72rem;color:#94a3b8;white-space:nowrap">
                    <?php if ($admin['lastLoginAt']): ?>
                        <?= date('M d, Y h:i A', strtotime($admin['lastLoginAt'])) ?>
                    <?php else: ?>
                        <em>Never</em>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="acts">
                        <a href="<?= site_url('admin/accounts/' . $admin['id'] . '/edit') ?>" title="Edit">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 7.125L16.862 4.487"/><path stroke-linecap="round" stroke-linejoin="round" d="M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                        </a>
                        <form action="<?= site_url('admin/accounts/' . $admin['id']) ?>" method="POST" onsubmit="return confirm('Delete this account?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="del" title="Delete">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
