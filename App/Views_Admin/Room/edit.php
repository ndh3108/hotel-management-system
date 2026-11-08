<?php 
$room = $room ?? null;
$roomTypes = $roomTypes ?? [];

if (!$room) {
    echo 'Không tìm thấy.';
    return;
}
?>
<h1>Sửa phòng</h1>
<form method="post" action="<?= \App\Core\url('/admin/rooms/' . (int)$room['id']) ?>">
    <p><label>Loại phòng</label>
        <select name="room_type_id" required>
            <?php foreach ($roomTypes as $rt): ?>
                <option value="<?= (int)($rt['id'] ?? 0) ?>" <?= (int)($item['room_type_id'] ?? 0) === (int)($rt['id'] ?? 0) ? 'selected' : '' ?>><?= htmlspecialchars($rt['name'] ?? '') ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p><label>Số phòng</label> <input type="text" name="room_number" value="<?= htmlspecialchars($item['room_number'] ?? '') ?>" required></p>
    <p><label>Trạng thái</label>
        <select name="status">
            <option value="available" <?= ($item['status'] ?? '') === 'available' ? 'selected' : '' ?>>Trống</option>
            <option value="booked" <?= ($item['status'] ?? '') === 'booked' ? 'selected' : '' ?>>Đã đặt</option>
            <option value="maintenance" <?= ($item['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>Bảo trì</option>
        </select>
    </p>
    <p><button type="submit">Cập nhật</button> <a href="admin.php?page=room" class="btn">Hủy</a></p>
</form>
