<?php $item = $item ?? null;
if (!$item) {
    echo 'Không tìm thấy.';
    return;
} ?>
<h1>Sửa loại phòng</h1>
<form method="post" action="admin.php?page=room-type&action=update&id=<?= (int)($item['id'] ?? 0) ?>">
    <p><label>Tên</label> <input type="text" name="name" value="<?= htmlspecialchars($item['name'] ?? '') ?>" required></p>
    <p><label>Sức chứa</label> <input type="number" name="capacity" min="1" value="<?= (int)($item['capacity'] ?? 1) ?>"></p>
    <p><label>Giá gốc</label> <input type="number" name="base_price" step="0.01" min="0" value="<?= (float)($item['base_price'] ?? 0) ?>"></p>
    <p><button type="submit">Cập nhật</button> <a href="admin.php?page=room-type" class="btn">Hủy</a></p>
</form>
