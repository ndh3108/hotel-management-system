<div class="admin-card" style="max-width: 600px; margin: 0 auto;">
    <div class="admin-card-header">
        <h2 class="admin-card-title"><?= htmlspecialchars($pageTitle) ?></h2>
        <a href="<?= \App\Core\url('/admin/inventory/catalog') ?>" class="admin-btn admin-btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>
    <div class="admin-card-body">
        <form action="<?= $item ? \App\Core\url('/admin/inventory/catalog/update/'.$item['id']) : \App\Core\url('/admin/inventory/catalog/store') ?>" method="POST">
            <div class="admin-form-group">
                <label class="admin-label">Tên vật tư <span style="color:red">*</span></label>
                <input type="text" name="name" class="admin-form-control" value="<?= htmlspecialchars($item['name'] ?? '') ?>" placeholder="VD: Khăn tắm trắng, Ly thủy tinh..." required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="admin-form-group">
                    <label class="admin-label">Phân loại</label>
                    <input type="text" name="category" class="admin-form-control" value="<?= htmlspecialchars($item['category'] ?? '') ?>" placeholder="VD: Bedding, Toiletries...">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Đơn vị tính</label>
                    <input type="text" name="unit" class="admin-form-control" value="<?= htmlspecialchars($item['unit'] ?? '') ?>" placeholder="VD: Cái, Bộ, Chai...">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px;">
                <div class="admin-form-group">
                    <label class="admin-label">Giá đền bù mặc định <span style="color:red">*</span></label>
                    <div style="position: relative;">
                        <input type="number" name="default_price" class="admin-form-control" value="<?= (int)($item['default_price'] ?? 0) ?>" min="0" required>
                        <span style="position: absolute; right: 15px; top: 12px; color: #64748b;">VNĐ</span>
                    </div>
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Tồn kho hiện tại</label>
                    <input type="number" name="stock_quantity" class="admin-form-control" value="<?= (int)($item['stock_quantity'] ?? 0) ?>" min="0">
                </div>
            </div>
            <small style="color: #94a3b8; display: block; margin-top: -5px; margin-bottom: 15px;">Giá này sẽ được tự động gợi ý khi báo cáo hư hỏng hoặc mất mát.</small>

            <div style="margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">
                    <i class="fa-solid fa-save"></i> <?= $item ? 'Lưu thay đổi' : 'Thêm vào danh mục' ?>
                </button>
            </div>
        </form>
    </div>
</div>

