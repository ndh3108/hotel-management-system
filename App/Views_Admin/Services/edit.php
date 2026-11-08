<?php $item = $item ?? null;
if (!$item) {
    echo 'Không tìm thấy.';
    return;
} ?>
<h1>Sửa dịch vụ</h1>
<form method="post" action="admin.php?page=service&action=update&id=<?= (int)($item['id'] ?? 0) ?>">
    <p><label>Tên</label> <input type="text" name="name" value="<?= htmlspecialchars($item['name'] ?? '') ?>" required></p>
    <p><label>Giá</label> <input type="number" name="price" step="0.01" min="0" value="<?= (float)($item['price'] ?? 0) ?>"></p>
    <p><label>Mô tả</label> <textarea name="description" rows="3"><?= htmlspecialchars($item['description'] ?? '') ?></textarea></p>
    <p><label>Loại</label>
        <select name="type">
            <option value="addon" <?= ($item['type'] ?? '') === 'addon' ? 'selected' : '' ?>>Addon</option>
            <option value="standalone" <?= ($item['type'] ?? '') === 'standalone' ? 'selected' : '' ?>>Standalone</option>
        </select>
    </p>
    <p><label>Kích hoạt</label> <select name="is_active">
            <option value="1" <?= (int)($item['is_active'] ?? 1) === 1 ? 'selected' : '' ?>>Có</option>
            <option value="0" <?= (int)($item['is_active'] ?? 1) === 0 ? 'selected' : '' ?>>Không</option>
        </select></p>
    <p><button type="submit">Cập nhật</button> <a href="admin.php?page=service" class="btn">Hủy</a></p>
</form>
