<div class="container" style="padding-top: 120px; padding-bottom: 40px;">
    <div class="section-header">
        <span class="section-subtitle">Service Booking</span>
        <h2 class="section-title">Đặt Dịch Vụ Theo Khung Giờ</h2>
        <p class="section-desc">Tổ chức sự kiện của bạn với những dịch vụ tốt nhất.</p>
    </div>

    <?php if (empty($services)): ?>
        <div style="text-align:center; background:#fff; border:1px solid #eee; border-radius:12px; padding:40px;">
            Hiện chưa có dịch vụ khả dụng.
        </div>
    <?php else: ?>
        <div class="rooms-grid">
            <?php foreach ($services as $service): ?>
                <div class="room-card">
                    <?php if (!empty($service['image_url'])): ?>
                        <div class="room-image">
                            <img src="<?= htmlspecialchars(\App\Core\url($service['image_url'])) ?>" alt="<?= htmlspecialchars($service['name']) ?>">
                        </div>
                    <?php endif; ?>
                    <div class="room-content">
                        <h3 class="room-title"><?= htmlspecialchars($service['name']) ?></h3>
                        <p style="color:#666; margin:8px 0 12px;">
                            Loại: <?= htmlspecialchars($service['type_name'] ?? 'Khác') ?>
                        </p>
                        <div class="room-amenities">
                            <span><i class="fa-solid fa-users"></i> Sức chứa: <?= (int) ($service['capacity'] ?? 1) ?></span>
                            <span><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($service['status'] ?? 'UNKNOWN') ?></span>
                        </div>
                        <div class="room-price">
                            <span class="amount"><?= number_format((float) ($service['price_per_hour'] ?? 0), 0, ',', '.') ?>đ</span>
                            <span class="period">/ giờ</span>
                        </div>
                        <div style="margin-top:15px;">
                            <?php if (($service['status'] ?? '') === 'AVAILABLE'): ?>
                                <a href="<?= \App\Core\url('/services/' . (int) $service['id']) ?>" class="btn btn-primary" style="width:100%;">Đặt ngay</a>
                            <?php else: ?>
                                <button type="button" class="btn btn-outline" style="width:100%; cursor:not-allowed;" disabled>Tạm bảo trì</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($hasMore): ?>
            <div style="text-align: center; margin-top: 50px; margin-bottom: 50px;">
                <a href="<?= \App\Core\url('services?cursor=' . $nextCursor . '&page=' . ($pageNumber + 1)) ?>" class="btn btn-outline" style="padding: 12px 40px; border-radius: 50px; font-weight: 700; transition: all 0.3s ease;">
                    Đường truyền dịch vụ <i class="fa-solid fa-chevron-down" style="margin-left: 8px; font-size: 0.8rem;"></i>
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

