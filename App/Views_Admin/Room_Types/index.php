<?php $items = $items ?? [];
$message = $_GET['message'] ?? ''; ?>
<h1>Loại phòng</h1>
<?php if ($message): ?> <p style="color:green"><?= $message === 'created' ? 'Đã thêm.' : ($message === 'updated' ? 'Đã cập nhật.' : 'Đã xóa.') ?></p> <?php endif; ?>
<p><a href="<?= \App\Core\url('/admin/room-types/create') ?>" class="btn">Thêm loại phòng</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Sức chứa</th>
            <th>Giá gốc</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $row): ?>
            <tr>
                <td><?= (int)($row['id'] ?? 0) ?></td>
                <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
                <td><?= (int)($row['capacity'] ?? 0) ?></td>
                <td><?= number_format((float)($row['base_price'] ?? 0)) ?></td>
                <td>
                    <a href="<?= \App\Core\url('/admin/room-types/' . (int)$row['id'] . '/edit') ?>" class="btn btn-small">Sửa</a>
                    <a href="<?= \App\Core\url('/admin/room-types/' . (int)$row['id'] . '/delete') ?>" 
                        class="btn btn-small" 
                        onclick="return confirm('Xóa?');">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?> <tr>
                <td colspan="5">Chưa có dữ liệu.</td>
            </tr> <?php endif; ?>
    </tbody>
</table>
