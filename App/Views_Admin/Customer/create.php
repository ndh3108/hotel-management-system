<?php $roles = $roles ?? []; ?>
<h1>Thêm khách hàng</h1>
<form method="post" action="admin.php?page=customer&action=store">
    <p><label>Loại tài khoản</label>
        <select name="role_id" required>
            <?php foreach ($roles as $r): ?>
                <option value="<?= (int)($r['id'] ?? 0) ?>"><?= htmlspecialchars($r['name'] ?? '') ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p><label>Họ tên</label> <input type="text" name="full_name" required></p>
    <p><label>Email</label> <input type="email" name="email" required></p>
    <p><label>Mật khẩu</label> <input type="password" name="password" required></p>
    <p><label>Điện thoại</label> <input type="text" name="phone"></p>
    <p><label>Trạng thái</label> <select name="status">
            <option value="1">Hoạt động</option>
            <option value="0">Ẩn</option>
        </select></p>
    <p><button type="submit">Lưu</button> <a href="admin.php?page=customer" class="btn">Hủy</a></p>
</form>
