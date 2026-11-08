<style>
@media (max-width: 768px) {
    .search-box {
        width: 100% !important;
        padding: 16px !important;
    }

    .search-form {
        grid-template-columns: 1fr !important;
    }

    .search-form .btn,
    .search-form .search-btn {
        width: 100%;
    }

    .room-card .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
<div class="container" style="padding-top: 100px;">
    <div class="section-header">
        <span class="section-subtitle">Đặt phòng</span>
        <h2 class="section-title">Tìm Phòng Trống</h2>
        <p class="section-desc">Chọn ngày và loại phòng để tìm không gian nghỉ dưỡng phù hợp nhất</p>
    </div>

    <!-- Search Form -->
    <div class="search-box"
        style="position: static; margin: 0 auto 50px; transform: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #eee;">
        <form class="search-form" action="<?= \App\Core\url('rooms/search') ?>" method="GET">
            <div class="form-group">
                <label>Ngày nhận phòng</label>
                <input type="date" name="check_in" value="<?= htmlspecialchars($checkIn) ?>" required>
            </div>
            <div class="form-group">
                <label>Ngày trả phòng</label>
                <input type="date" name="check_out" value="<?= htmlspecialchars($checkOut) ?>" required>
            </div>
            <div class="form-group">
                <label>Loại phòng</label>
                <select name="room_type_id">
                    <option value="">Tất cả loại phòng</option>
                    <?php foreach ($roomTypes as $rt): ?>
                        <option value="<?= $rt['id'] ?>" <?= $roomTypeId == $rt['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rt['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary search-btn">Cập Nhật</button>
        </form>
    </div>

    <?php if ($checkIn && $checkOut): ?>
        <h3 style="margin-bottom: 30px; color: var(--secondary); font-size: 1.5rem;">
            Kết quả: <?= count($rooms) ?> phòng trống từ <?= date('d/m/Y', strtotime($checkIn)) ?> đến
            <?= date('d/m/Y', strtotime($checkOut)) ?>
        </h3>

        <?php if (empty($rooms)): ?>
            <div style="text-align: center; padding: 50px; background: #fff; border-radius: 15px; border: 1px solid #eee;">
                <p style="font-size: 1.2rem; color: #666;">Rất tiếc, không có phòng nào trống trong khoảng thời gian này.</p>
                <a href="<?= \App\Core\url('rooms') ?>" class="btn btn-primary" style="margin-top: 20px;">Xem Các Loại Phòng</a>
            </div>
        <?php else: ?>
            <div class="rooms-grid">
                <?php foreach ($rooms as $r): ?>
                    <?php
                    $adultCapacity = (int) ($r['capacity_adults'] ?? 0);
                    $childrenCapacity = (int) ($r['capacity_children'] ?? 0);
                    $totalCapacity = $adultCapacity + $childrenCapacity;
                    $displayCapacity = $totalCapacity > 0 ? $totalCapacity : 1;
                    $r['capacity'] = $displayCapacity;
                    ?>
                    <div class="room-card">
                        <div class="room-image">
                            <?php $imageUrl = !empty($r['image_url']) ? \App\Core\url($r['image_url']) : \App\Core\url('assets/image/image.png'); ?>
                            <img loading="lazy" src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($r['room_type_name']) ?>">
                            <span class="room-badge">Số phòng: <?= htmlspecialchars($r['room_number']) ?></span>
                        </div>
                        <div class="room-content">
                            <h3 class="room-title"><?= htmlspecialchars($r['room_type_name']) ?></h3>
                            <div class="room-amenities">
                                <span><i class="fa-solid fa-users"></i> <?= $r['capacity'] ?> Khách</span>
                                <span><i class="fa-solid fa-bed"></i> <?= $r['capacity'] > 1 ? 'King Bed' : 'Single Bed' ?></span>
                                <span><i class="fa-solid fa-circle-check"></i> Có sẵn</span>
                            </div>
                            <div class="room-price">
                                <span class="amount"><?= number_format($r['base_price'], 0, ',', '.') ?>đ</span>
                                <span class="period">/ đêm</span>
                            </div>
                                <a href="<?= \App\Core\url('rooms/' . $r['id']) ?>?check_in=<?= $checkIn ?>&check_out=<?= $checkOut ?>"
                                    class="btn btn-primary">
                                    Chọn Phòng Này
                                </a>
                            </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 50px; background: #fff; border-radius: 15px; border: 1px solid #eee;">
            <p style="font-size: 1.2rem; color: #666;">Vui lòng nhập ngày để tìm phòng trống.</p>
        </div>
    <?php endif; ?>
</div>

