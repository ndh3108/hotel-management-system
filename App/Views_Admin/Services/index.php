<?php $items = $items ?? [];
$message = $_GET['message'] ?? ''; ?>
<h1>Dịch vụ</h1>
<?php if ($message): ?> <p style="color:green"><?= $message === 'created' ? 'Đã thêm.' : ($message === 'updated' ? 'Đã cập nhật.' : 'Đã xóa.') ?></p> <?php endif; ?>
<p><a href="admin.php?page=service&action=create" class="btn">Thêm dịch vụ</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Giá</th>
            <th>Loại</th>
            <th>Kích hoạt</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $row): ?>
            <tr>
                <td><?= (int)($row['id'] ?? 0) ?></td>
                <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
                <td><?= number_format((float)($row['price'] ?? 0)) ?></td>
                <td><?= htmlspecialchars($row['type'] ?? '') ?></td>
                <td><?= (int)($row['is_active'] ?? 0) ? 'Có' : 'Không' ?></td>
                <td>
                    <a href="admin.php?page=service&action=edit&id=<?= (int)($row['id'] ?? 0) ?>" class="btn btn-small">Sửa</a>
                    <a href="admin.php?page=service&action=delete&id=<?= (int)($row['id'] ?? 0) ?>" class="btn btn-small" onclick="return confirm('Xóa?');">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?> <tr>
                <td colspan="6">Chưa có dữ liệu.</td>
            </tr> <?php endif; ?>
    </tbody>
</table>
