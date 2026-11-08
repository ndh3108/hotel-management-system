<div class="admin-card">
    <div class="admin-card-header" style="display: flex; justify-content: flex-end; align-items: center;">
        <a href="<?= \App\Core\url('/admin/room/create') ?>" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm mới
        </a>
    </div>
    <div class="admin-card-body" style="padding: 0;">
        <div class="admin-table-wrap">
            <table class="admin-table admin-table-min">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Số phòng</th>
                        <th>Loại phòng</th>
                        <th>Tầng</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rooms)): ?>
                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td>#<?= (int) $room['id'] ?></td>
                                <td><strong><?= htmlspecialchars($room['room_number']) ?></strong></td>
                                <td><?= htmlspecialchars($room['room_type_name'] ?? '-') ?></td>
                                <td>Tầng <?= htmlspecialchars((string) ($room['floor'] ?? '-')) ?></td>
                                <td>
                                    <?php
                                    $status = $room['status'] ?? 'Available';
                                    $badgeClass = match ($status) {
                                        'Available' => 'badge-success',
                                        'Occupied' => 'badge-warning',
                                        'Cleaning' => 'badge-info',
                                        'Maintenance' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                    $statusText = match ($status) {
                                        'Available' => 'Trống',
                                        'Occupied' => 'Có khách',
                                        'Cleaning' => 'Đang dọn',
                                        'Maintenance' => 'Bảo trì',
                                        default => ucfirst((string) $status)
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $statusText ?></span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="<?= \App\Core\url('/admin/room/' . $room['id'] . '/edit') ?>" class="action-btn action-btn-edit" title="Sửa"><i class="fa-solid fa-pen"></i></a>
                                        <form action="<?= \App\Core\url('/admin/room/' . $room['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                            <button type="submit" class="action-btn action-btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #666;">Chưa có phòng nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
    <div class="admin-pagination">
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/room?page=1') ?>">&laquo;</a>
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/room?page=' . max(1, ($pageNumber ?? 1) - 1)) ?>">&lsaquo;</a>
        <?php foreach (\App\Core\pagination_range((int) ($pageNumber ?? 1), (int) ($totalPages ?? 1)) as $pageItem): ?>
            <?php if ($pageItem === '...'): ?>
                <span class="admin-pagination-ellipsis">...</span>
            <?php else: ?>
                <a class="admin-pagination-link <?= (int) $pageItem === (int) ($pageNumber ?? 1) ? 'is-active' : '' ?>" href="<?= \App\Core\url('/admin/room?page=' . (int) $pageItem) ?>"><?= (int) $pageItem ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/room?page=' . min((int) ($totalPages ?? 1), (int) ($pageNumber ?? 1) + 1)) ?>">&rsaquo;</a>
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/room?page=' . (int) ($totalPages ?? 1)) ?>">&raquo;</a>
    </div>
<?php endif; ?>


