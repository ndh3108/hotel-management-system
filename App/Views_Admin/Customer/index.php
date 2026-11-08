<?php
$items = $items ?? [];
$message = $_GET['message'] ?? '';
?>
<h1>Khách hàng</h1>
<?php if ($message === 'created'): ?> <p style="color:green">Đã thêm mới.</p> <?php endif; ?>
<?php if ($message === 'updated'): ?> <p style="color:green">Đã cập nhật.</p> <?php endif; ?>
<?php if ($message === 'deleted'): ?> <p style="color:green">Đã xóa.</p> <?php endif; ?>
<p><a href="admin.php?page=customer&action=create" class="btn">Thêm khách hàng</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Điện thoại</th>
            <th>Trạng thái</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $row): ?>
            <tr>
                <td><?= (int)($row['id'] ?? 0) ?></td>
                <td><?= htmlspecialchars($row['full_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                <td><?= (int)($row['status'] ?? 0) === 1 ? 'Hoạt động' : 'Ẩn' ?></td>
                <td>
                    <a href="admin.php?page=customer&action=edit&id=<?= (int)($row['id'] ?? 0) ?>" class="btn btn-small">Sửa</a>
                    <a href="admin.php?page=customer&action=delete&id=<?= (int)($row['id'] ?? 0) ?>" class="btn btn-small" onclick="return confirm('Xóa?');">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?> <tr>
                <td colspan="6">Chưa có dữ liệu.</td>
            </tr> <?php endif; ?>
    </tbody>
</table>
