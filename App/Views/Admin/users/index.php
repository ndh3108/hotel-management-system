<div class="admin-card">
    <div class="admin-card-header" style="display: flex; justify-content: flex-end; align-items: center; flex-wrap: wrap; gap: 15px;">

        <form method="GET" action="<?= \App\Core\url('/admin/users') ?>" style="display: flex; gap: 10px; align-items: center;">
            <select name="role_id" class="form-control" style="width: 200px; padding: 8px; border-radius: 4px; border: 1px solid #ddd;" onchange="this.form.submit()">
                <option value="">-- Tất cả vai trò --</option>
                <?php foreach ($roles ?? [] as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= (isset($selectedRole) && $selectedRole == $role['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <a href="<?= \App\Core\url('/admin/users/create') ?>" class="admin-btn admin-btn-primary" data-permission="USER_MANAGE">
            <i class="fa-solid fa-plus"></i> Thêm mới
        </a>
    </div>
    <div class="admin-card-body" style="padding: 0;">
        <div class="admin-table-wrap">
            <table class="admin-table admin-table-min">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>#<?= (int) $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                                <td><span class="badge badge-primary"><?= htmlspecialchars($user['role_name'] ?? 'Chưa phân quyền') ?></span></td>
                                <td>
                                    <?php $status = $user['status'] ?? 'active'; ?>
                                    <span class="badge <?= $status == 1 || $status === 'active' ? 'badge-success' : 'badge-danger' ?>">
                                        <?= ($status == 1 || $status === 'active') ? 'Hoạt động' : 'Bị khóa' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="<?= \App\Core\url('/admin/users/' . $user['id'] . '/edit') ?>" class="action-btn action-btn-edit" title="Sửa" data-permission="USER_MANAGE"><i class="fa-solid fa-pen"></i></a>
                                        <form action="<?= \App\Core\url('/admin/users/' . $user['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                            <button type="submit" class="action-btn action-btn-delete" title="Xóa" data-permission="USER_MANAGE"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: #666;">Chưa có người dùng nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
    <?php $baseQuery = []; if (!empty($selectedRole)) { $baseQuery['role_id'] = (int) $selectedRole; } ?>
    <div class="admin-pagination">
        <?php $firstQuery = $baseQuery; $firstQuery['page'] = 1; ?>
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/users?' . http_build_query($firstQuery)) ?>">&laquo;</a>
        <?php $prevQuery = $baseQuery; $prevQuery['page'] = max(1, ($pageNumber ?? 1) - 1); ?>
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/users?' . http_build_query($prevQuery)) ?>">&lsaquo;</a>
        <?php foreach (\App\Core\pagination_range((int) ($pageNumber ?? 1), (int) ($totalPages ?? 1)) as $pageItem): ?>
            <?php if ($pageItem === '...'): ?>
                <span class="admin-pagination-ellipsis">...</span>
            <?php else: ?>
                <?php $itemQuery = $baseQuery; $itemQuery['page'] = (int) $pageItem; ?>
                <a class="admin-pagination-link <?= (int) $pageItem === (int) ($pageNumber ?? 1) ? 'is-active' : '' ?>" href="<?= \App\Core\url('/admin/users?' . http_build_query($itemQuery)) ?>"><?= (int) $pageItem ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php $nextQuery = $baseQuery; $nextQuery['page'] = min((int) ($totalPages ?? 1), (int) ($pageNumber ?? 1) + 1); ?>
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/users?' . http_build_query($nextQuery)) ?>">&rsaquo;</a>
        <?php $lastQuery = $baseQuery; $lastQuery['page'] = (int) ($totalPages ?? 1); ?>
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/users?' . http_build_query($lastQuery)) ?>">&raquo;</a>
    </div>
<?php endif; ?>

