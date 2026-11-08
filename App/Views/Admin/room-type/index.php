<div class="admin-card">
    <div class="admin-card-header" style="display: flex; justify-content: flex-end; align-items: center;">
        <a href="<?= \App\Core\url('/admin/room-type/create') ?>" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm mới
        </a>
    </div>
    <div class="admin-card-body" style="padding: 0;">
        <?php if (!empty($_SESSION['success'])): ?>
            <div style="margin: 15px; padding: 12px 16px; border-radius: 8px; background: #d4edda; color: #155724;">
                <?= htmlspecialchars((string) $_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div style="margin: 15px; padding: 12px 16px; border-radius: 8px; background: #f8d7da; color: #842029;">
                <?= htmlspecialchars((string) $_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <div class="admin-table-wrap">
            <table class="admin-table admin-table-min">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên loại phòng</th>
                        <th>Sức chứa</th>
                        <th>Diện tích</th>
                        <th>Giá cơ bản</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($roomTypes)): ?>
                        <?php foreach ($roomTypes as $rt): ?>
                            <tr>
                                <td>#<?= (int) $rt['id'] ?></td>
                                <td>
                                    <?php if (!empty($rt['image_url'])): ?>
                                        <img src="<?= htmlspecialchars(\App\Core\url($rt['image_url'])) ?>" alt="<?= htmlspecialchars($rt['name']) ?>" style="width:72px; height:52px; object-fit:cover; border-radius:8px;">
                                    <?php else: ?>
                                        <span style="color:#999;">Không có</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($rt['name']) ?></strong></td>
                                <td><?= (int) ($rt['capacity_adults'] ?? 0) ?> NL, <?= (int) ($rt['capacity_children'] ?? 0) ?> TE</td>
                                <td><?= htmlspecialchars((string) ($rt['area_sqm'] ?? '-')) ?> m²</td>
                                <td style="font-weight: 600; color: var(--admin-primary);"><?= number_format((float) ($rt['base_price'] ?? 0), 0, ',', '.') ?>đ</td>
                                <td>
                                    <div class="action-btns">
                                        <a href="<?= \App\Core\url('/admin/room-type/' . $rt['id'] . '/edit') ?>" class="action-btn action-btn-edit" title="Sửa"><i class="fa-solid fa-pen"></i></a>
                                        <form action="<?= \App\Core\url('/admin/room-type/' . $rt['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                            <button type="submit" class="action-btn action-btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: #666;">Chưa có loại phòng nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
    <div class="admin-pagination">
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/room-type?page=1') ?>">&laquo;</a>
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/room-type?page=' . max(1, ($pageNumber ?? 1) - 1)) ?>">&lsaquo;</a>
        <?php foreach (\App\Core\pagination_range((int) ($pageNumber ?? 1), (int) ($totalPages ?? 1)) as $pageItem): ?>
            <?php if ($pageItem === '...'): ?>
                <span class="admin-pagination-ellipsis">...</span>
            <?php else: ?>
                <a class="admin-pagination-link <?= (int) $pageItem === (int) ($pageNumber ?? 1) ? 'is-active' : '' ?>" href="<?= \App\Core\url('/admin/room-type?page=' . (int) $pageItem) ?>"><?= (int) $pageItem ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/room-type?page=' . min((int) ($totalPages ?? 1), (int) ($pageNumber ?? 1) + 1)) ?>">&rsaquo;</a>
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/room-type?page=' . (int) ($totalPages ?? 1)) ?>">&raquo;</a>
    </div>
<?php endif; ?>


