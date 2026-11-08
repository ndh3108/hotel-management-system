<?php
$item = $item ?? null;
$roles = $roles ?? [];
if (!$item) {
    echo 'Không tìm thấy.';
    return;
}
?>
<h1>Sửa khách hàng</h1>
<form method="post" action="admin.php?page=customer&action=update&id=<?= (int)($item['id'] ?? 0) ?>">
    <p><label>Loại tài khoản</label>
        <select name="role_id" required>
            <?php foreach ($roles as $r): ?>
                <option value="<?= (int)($r['id'] ?? 0) ?>" <?= (int)($item['role_id'] ?? 0) === (int)($r['id'] ?? 0) ? 'selected' : '' ?>><?= htmlspecialchars($r['name'] ?? '') ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p><label>Họ tên</label> <input type="text" name="full_name" value="<?= htmlspecialchars($item['full_name'] ?? '') ?>" required></p>
    <p><label>Email</label> <input type="email" name="email" value="<?= htmlspecialchars($item['email'] ?? '') ?>" required></p>
    <p><label>Mật khẩu (để trống nếu không đổi)</label> <input type="password" name="password"></p>
    <p><label>Điện thoại</label> <input type="text" name="phone" value="<?= htmlspecialchars($item['phone'] ?? '') ?>"></p>
    <p><label>Trạng thái</label> <select name="status">
            <option value="1" <?= ((int)($item['status'] ?? 1)) === 1 ? 'selected' : '' ?>>Hoạt động</option>
            <option value="0" <?= ((int)($item['status'] ?? 1)) === 0 ? 'selected' : '' ?>>Ẩn</option>
        </select></p>
    <p><button type="submit">Cập nhật</button> <a href="admin.php?page=customer" class="btn">Hủy</a></p>
</form>
