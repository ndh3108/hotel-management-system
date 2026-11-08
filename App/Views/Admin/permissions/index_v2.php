<div class="admin-card">
    <div class="admin-card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;">
        <h2 class="admin-card-title">Quan ly permission</h2>
        <a href="<?= \App\Core\url('/admin/permissions/create') ?>" class="admin-btn admin-btn-primary" data-permission="USER_MANAGE">
            <i class="fa-solid fa-plus"></i> Tao permission
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
                        <th>Module</th>
                        <th>Trang thai</th>
                        <th>Version</th>
                        <th>Role dang dung</th>
                        <th>Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($permissions)): ?>
                        <?php foreach ($permissions as $perm): ?>
                            <?php $isActive = (int) ($perm['is_active'] ?? 1) === 1; ?>
                            <tr>
                                <td>#<?= (int) ($perm['id'] ?? 0) ?></td>
                                <td>
                                    <div style="display:grid;gap:4px;">
                                        <strong><?= htmlspecialchars((string) ($perm['name'] ?? '')) ?></strong>
                                        <?php if (!empty($perm['description'])): ?>
                                            <small style="color:#64748b;"><?= htmlspecialchars((string) $perm['description']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><code><?= htmlspecialchars((string) ($perm['code'] ?? $perm['name'] ?? '')) ?></code></td>
                                <td><span class="badge badge-info"><?= htmlspecialchars((string) ($perm['module'] ?? $perm['block_module'] ?? 'General')) ?></span></td>
                                <td><span class="badge <?= $isActive ? 'badge-success' : 'badge-danger' ?>"><?= $isActive ? 'Hoat dong' : 'Tam tat' ?></span></td>
                                <td><span class="badge badge-primary">v<?= (int) ($perm['permission_version'] ?? 1) ?></span></td>
                                <td><?= (int) ($perm['active_role_count'] ?? 0) ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="<?= \App\Core\url('/admin/permissions/' . (int) $perm['id'] . '/edit') ?>" class="action-btn action-btn-edit" title="Sua" data-permission="USER_MANAGE">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="<?= \App\Core\url('/admin/permissions/' . (int) $perm['id'] . '/disable') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Tam tat permission nay?');">
                                            <button type="submit" class="action-btn action-btn-delete" title="Tam tat" data-permission="USER_MANAGE">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </form>
                                        <form action="<?= \App\Core\url('/admin/permissions/' . (int) $perm['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Xoa mem permission nay?');">
                                            <button type="submit" class="action-btn action-btn-delete" title="Xoa mem" data-permission="USER_MANAGE">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align:center;padding:30px;color:#666;">Chua co permission nao</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

