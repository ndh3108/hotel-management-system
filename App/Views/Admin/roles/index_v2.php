<div class="admin-card">
    <div class="admin-card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;">
        <h2 class="admin-card-title">Quan ly vai tro</h2>
        <a href="<?= \App\Core\url('/admin/roles/create') ?>" class="admin-btn admin-btn-primary" data-permission="USER_MANAGE">
            <i class="fa-solid fa-plus"></i> Tao vai tro
        </a>
    </div>

    <div class="admin-card-body" style="padding:0;">
        <div class="admin-table-wrap">
            <table class="admin-table admin-table-min" style="min-width:980px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ten</th>
                        <th>Code</th>
                        <th>Trang thai</th>
                        <th>Version</th>
                        <th>Users</th>
                        <th>Permissions</th>
                        <th>Sap xep</th>
                        <th>Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <?php $isActive = (int) ($role['is_active'] ?? 1) === 1; ?>
                            <tr>
                                <td>#<?= (int) ($role['id'] ?? 0) ?></td>
                                <td>
                                    <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;">
                                        <strong><?= htmlspecialchars((string) ($role['name'] ?? '')) ?></strong>
                                        <?php if ((int) ($role['is_system'] ?? 0) === 1): ?>
                                            <span class="badge badge-info">System</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><code><?= htmlspecialchars((string) ($role['code'] ?? $role['name'] ?? '')) ?></code></td>
                                <td><span class="badge <?= $isActive ? 'badge-success' : 'badge-danger' ?>"><?= $isActive ? 'Hoat dong' : 'Tam tat' ?></span></td>
                                <td><span class="badge badge-primary">v<?= (int) ($role['permission_version'] ?? 1) ?></span></td>
                                <td><?= (int) ($role['active_user_count'] ?? 0) ?></td>
                                <td><?= (int) ($role['permission_count'] ?? 0) ?></td>
                                <td><?= (int) ($role['sort_order'] ?? 0) ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="<?= \App\Core\url('/admin/roles/' . (int) $role['id'] . '/edit') ?>" class="action-btn action-btn-edit" title="Sua" data-permission="USER_MANAGE">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <?php if ((int) ($role['is_system'] ?? 0) !== 1): ?>
                                            <form action="<?= \App\Core\url('/admin/roles/' . (int) $role['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Ban co chac muon xoa mem vai tro nay?');">
                                                <button type="submit" class="action-btn action-btn-delete" title="Xoa mem" data-permission="USER_MANAGE">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align:center;padding:30px;color:#666;">Chua co vai tro nao</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

