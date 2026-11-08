<?php $roomTypes = $roomTypes ?? []; ?>
<h1>Thêm phòng</h1>
<form method="post" action="admin.php?page=room&action=store">
    <p><label>Loại phòng</label>
        <select name="room_type_id" required>
            <?php foreach ($roomTypes as $rt): ?>
                <option value="<?= (int)($rt['id'] ?? 0) ?>"><?= htmlspecialchars($rt['name'] ?? '') ?> (ID: <?= (int)($rt['id'] ?? 0) ?>)</option>
            <?php endforeach; ?>
        </select>
    </p>
    <p><label>Số phòng</label> <input type="text" name="room_number" required></p>
    <p><label>Trạng thái</label> <select name="status">
            <option value="available">Trống</option>
            <option value="booked">Đã đặt</option>
            <option value="maintenance">Bảo trì</option>
        </select></p>
    <p><button type="submit">Lưu</button> <a href="admin.php?page=room" class="btn">Hủy</a></p>
</form>
