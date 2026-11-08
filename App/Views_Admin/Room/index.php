<?php $items = $items ?? [];
$message = $_GET['message'] ?? ''; ?>
<h1>Phòng</h1>
<?php if ($message): ?> <p style="color:green"><?= $message === 'created' ? 'Đã thêm.' : ($message === 'updated' ? 'Đã cập nhật.' : 'Đã xóa.') ?></p> <?php endif; ?>
<p><a href="admin.php?page=room&action=create" class="btn">Thêm phòng</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Loại phòng ID</th>
            <th>Số phòng</th>
            <th>Trạng thái</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $row): ?>
            <tr>
                <td><?= (int)($row['id'] ?? 0) ?></td>
                <td><?= (int)($row['room_type_id'] ?? 0) ?></td>
                <td><?= htmlspecialchars($row['room_number'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                <td>
                    <a href="admin.php?page=room&action=edit&id=<?= (int)($row['id'] ?? 0) ?>" class="btn btn-small">Sửa</a>
                    <a href="admin.php?page=room&action=delete&id=<?= (int)($row['id'] ?? 0) ?>" class="btn btn-small" onclick="return confirm('Xóa?');">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?> <tr>
                <td colspan="5">Chưa có dữ liệu.</td>
            </tr> <?php endif; ?>
    </tbody>
</table>
