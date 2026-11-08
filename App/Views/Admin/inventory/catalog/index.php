<div class="admin-card">
    <div class="admin-card-header" style="display: flex; justify-content: flex-end; align-items: center;">
        <a href="<?= \App\Core\url('/admin/inventory/catalog/create') ?>" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm vật tư mới
        </a>
    </div>
    <div class="admin-card-body">
        <?php if (isset($_GET['success'])): ?>
            <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;">
                <i class="fa-solid fa-circle-check"></i> Cập nhật danh mục thành công!
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                <i class="fa-solid fa-trash-can"></i> Đã xóa vật tư khỏi danh mục.
            </div>
        <?php endif; ?>

        <div class="admin-table-wrap">
            <table class="admin-table admin-table-min">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Vật tư / Phân loại</th>
                        <th style="width: 100px;">Đơn vị</th>
                        <th style="width: 120px;">Giá đền bù</th>
                        <th style="width: 100px; text-align: center;">Tồn kho</th>
                        <th style="width: 150px; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" style="text-align: center; padding: 60px; color: #94a3b8;">
                            <i class="fa-solid fa-box-open" style="font-size: 3rem; display: block; margin-bottom: 15px; opacity: 0.3;"></i>
                            Chưa có vật tư nào trong danh mục.
                        </td></tr>
                    <?php else: foreach ($items as $item): ?>
                        <tr>
                            <td><span style="color: #94a3b8; font-family: monospace;">#<?= $item['id'] ?></span></td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($item['name']) ?></div>
                                <div style="font-size: 0.75rem; color: #6366f1; background: #eef2ff; display: inline-block; padding: 1px 6px; border-radius: 4px; margin-top: 4px;">
                                    <?= htmlspecialchars($item['category'] ?? 'Chưa phân loại') ?>
                                </div>
                            </td>
                            <td><span style="font-size: 0.9rem; color: #475569;"><?= htmlspecialchars($item['unit'] ?? '-') ?></span></td>
                            <td>
                                <div style="color: #059669; font-weight: 700; font-size: 0.95rem;">
                                    <?= number_format($item['default_price'], 0, ',', '.') ?>₫
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span style="font-weight: 700; color: <?= (int)$item['stock_quantity'] < 10 ? '#ef4444' : '#0f172a' ?>;">
                                    <?= (int)$item['stock_quantity'] ?>
                                </span>
                            </td>
                            <td class="action-btns" style="text-align: center; justify-content: center;">
                                <a href="<?= \App\Core\url('/admin/inventory/catalog/edit/'.$item['id']) ?>" class="admin-btn-icon" style="background: #f1f5f9; color: #475569;" title="Chỉnh sửa">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="javascript:void(0)" onclick="confirmDelete(<?= $item['id'] ?>)" class="admin-btn-icon" style="background: #fff1f2; color: #e11d48;" title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 25px;">
            <?php if ($pageNumber > 1): ?>
                <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/inventory/catalog?page=' . ($pageNumber - 1)) ?>" style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 6px; color: #475569; text-decoration: none;">&laquo; Trước</a>
            <?php endif; ?>
            
            <span style="font-weight: 600; color: #475569;">Trang <?= $pageNumber ?></span>
            
            <?php if ($hasMore): ?>
                <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/inventory/catalog?page=' . ($pageNumber + 1) . '&cursor=' . $nextCursor) ?>" style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 6px; color: #475569; text-decoration: none;">Sau &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Bạn có chắc chắn muốn xóa vật tư này khỏi danh mục?')) {
        window.location.href = '<?= \App\Core\url('/admin/inventory/catalog/delete/') ?>' + id;
    }
}
</script>

