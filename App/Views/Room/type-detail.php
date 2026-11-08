<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --font-heading: 'Outfit', sans-serif;
        --font-body: 'Inter', sans-serif;
        --luxury-navy: #0f172a;
        --luxury-gold: #c5a059;
        --luxury-slate: #f8fafc;
        --radius-lg: 24px;
        --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }

    body { font-family: var(--font-body); color: #334155; }
    h1, h2, h3, h4 { font-family: var(--font-heading); font-weight: 700; color: var(--luxury-navy); }

    .room-hero {
        position: relative;
        height: 60vh;
        min-height: 500px;
        overflow: hidden;
    }

    .room-hero img { width: 100%; height: 100%; object-fit: cover; }
    .room-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(15, 23, 42, 0.9) 0%, transparent 70%);
    }

    .room-hero-content {
        position: absolute;
        bottom: 80px;
        left: 0;
        right: 0;
        z-index: 10;
        color: white;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 40px;
        margin-top: -80px;
        position: relative;
        z-index: 20;
    }

    .detail-main {
        background: white;
        border-radius: var(--radius-lg);
        padding: 40px;
        box-shadow: var(--shadow-lg);
    }

    .detail-sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
    }

    .booking-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 35px;
        box-shadow: var(--shadow-lg);
        border: 1px solid #f1f5f9;
    }

    .spec-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        padding-bottom: 30px;
        margin-bottom: 30px;
        border-bottom: 1px solid #f1f5f9;
    }

    .spec-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .spec-item i {
        font-size: 1.5rem;
        color: var(--primary);
    }

    .spec-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; }
    .spec-value { font-size: 1.1rem; font-weight: 700; color: var(--luxury-navy); }

    .section-title-premium {
        font-size: 1.5rem;
        margin-top: 40px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title-premium i { color: var(--primary); font-size: 1.2rem; }

    .amenities-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
    }

    .amenity-pill {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        font-size: 0.95rem;
        font-weight: 500;
        color: #475569;
        transition: all 0.2s;
    }
    .amenity-pill:hover { border-color: var(--primary); background: white; }

    .supply-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
    }

    .supply-item {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #64748b;
        font-size: 0.95rem;
        padding: 8px 12px;
        background: #fdfdfd;
        border-radius: 10px;
    }

    .policy-box {
        background: #fdfaf3;
        border: 1px solid #f9731622;
        border-radius: 20px;
        padding: 30px;
        margin-top: 50px;
    }

    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 18px;
        background: white;
        border: 1px solid #fed7aa;
        border-radius: 50px;
        font-weight: 700;
        color: #c2410c;
        margin-right: 15px;
        margin-bottom: 10px;
    }

    .gallery-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
        margin: 30px 0;
    }

    .gallery-preview img {
        width: 100%;
        height: 110px;
        object-fit: cover;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .gallery-preview img:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }

    .lightbox-modal {
        display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 10000;
        align-items: center; justify-content: center; backdrop-filter: blur(10px);
    }
    .lightbox-modal.active { display: flex; }
    .lightbox-content { max-width: 90%; max-height: 85vh; border-radius: 12px; transform: scale(0.9); transition: transform 0.3s; }
    .lightbox-modal.active .lightbox-content { transform: scale(1); }

    .review-card {
        padding: 24px;
        background: #f8fafc;
        border-radius: 18px;
        margin-bottom: 20px;
        border-left: 5px solid var(--primary);
    }

    @media (max-width: 991px) {
        .detail-grid { grid-template-columns: 1fr; margin-top: 0; }
        .detail-sidebar { position: static; }
        .room-hero { height: 50vh; }
    }
</style>

<?php
$primaryImage = '/assets/image/image.png';
if (!empty($roomType['images'])) {
    // 1. Try to find the marked primary image
    $foundPrimary = false;
    foreach ($roomType['images'] as $img) {
        if (!empty($img['is_primary'])) {
            $primaryImage = $img['image_url'] ?? $img['image_path'] ?? $primaryImage;
            $foundPrimary = true;
            break;
        }
    }
    // 2. Fallback to the first image in the collection if no primary is designated
    if (!$foundPrimary && !empty($roomType['images'][0])) {
        $primaryImage = $roomType['images'][0]['image_url'] ?? $roomType['images'][0]['image_path'] ?? $primaryImage;
    }
}
?>

<div class="room-hero">
    <img src="<?= htmlspecialchars($primaryImage) ?>" alt="<?= htmlspecialchars($roomType['name']) ?>">
    <div class="room-hero-overlay"></div>
    <div class="room-hero-content">
        <div class="container text-center">
            <h1 style="font-size: 3.5rem; color: white; margin-bottom: 12px;"><?= htmlspecialchars($roomType['name']) ?></h1>
            <div style="color: #fbbf24; font-size: 1.3rem; display: flex; justify-content: center; align-items: center; gap: 10px;">
                <div class="stars">
                    <?php for($i=1; $i<=5; $i++): ?>
                        <i class="fa-<?= $i <= round($avgRating ?? 5) ? 'solid' : 'regular' ?> fa-star"></i>
                    <?php endfor; ?>
                </div>
                <span style="font-weight: 800;"><?= number_format($avgRating ?? 5.0, 1) ?></span>
                <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem; font-weight: 400;">(<?= $reviewCount ?? 0 ?> đánh giá thực tế)</span>
            </div>
        </div>
    </div>
</div>

<div class="container" style="padding-bottom: 100px;">
    <div class="detail-grid">
        <div class="detail-main">
            <div class="spec-grid">
                <div class="spec-item">
                    <i class="fa-solid fa-users"></i>
                    <span class="spec-label">Sức chứa</span>
                    <span class="spec-value"><?= $roomType['capacity_adults'] ?> Người lớn, <?= $roomType['capacity_children'] ?> Trẻ em</span>
                </div>
                <div class="spec-item">
                    <i class="fa-solid fa-expand-arrows-alt"></i>
                    <span class="spec-label">Diện tích</span>
                    <span class="spec-value"><?= $roomType['area_sqm'] ?> m²</span>
                </div>
                <div class="spec-item">
                    <i class="fa-solid fa-bed"></i>
                    <span class="spec-label">Loại giường</span>
                    <span class="spec-value"><?= htmlspecialchars($roomType['bed_type'] ?? 'King Bed') ?></span>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <h2 class="section-title-premium" style="margin-top: 0;"><i class="fa-solid fa-leaf"></i> Trải nghiệm không gian</h2>
                <div style="line-height: 1.9; font-size: 1.1rem; color: #475569;">
                    <?= nl2br(htmlspecialchars($roomType['description'] ?? 'Chưa có mô tả chi tiết cho loại phòng này.')) ?>
                </div>
            </div>

            <div class="gallery-preview">
                <?php if (!empty($roomType['images'])): ?>
                    <?php foreach ($roomType['images'] as $img): ?>
                        <img src="<?= htmlspecialchars($img['image_url'] ?? $img['image_path'] ?? '') ?>" onclick="openLightbox(this.src)">
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <h2 class="section-title-premium"><i class="fa-solid fa-wand-magic-sparkles"></i> Tiện ích đẳng cấp</h2>
            <div class="amenities-list">
                <?php if (!empty($roomType['amenities'])): ?>
                    <?php foreach ($roomType['amenities'] as $amenity): ?>
                        <div class="amenity-pill">
                            <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                            <?= htmlspecialchars($amenity['name']) ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #94a3b8;">Thông tin tiện ích đang được cập nhật...</p>
                <?php endif; ?>
            </div>

            <h2 class="section-title-premium"><i class="fa-solid fa-concierge-bell"></i> Trang thiết bị trong phòng</h2>
            <div class="supply-grid">
                <?php if (!empty($roomType['supplies'])): ?>
                    <?php foreach ($roomType['supplies'] as $supply): ?>
                        <div class="supply-item">
                            <i class="fa-solid fa-square-check" style="color: var(--primary); font-size: 0.8rem;"></i>
                            <span style="color: #1e293b; font-weight: 500;"><?= htmlspecialchars($supply['name']) ?></span>
                            <?php if ($supply['quantity'] > 1): ?>
                                <span style="font-weight: 700; color: #64748b; font-size: 0.8rem;">x<?= $supply['quantity'] ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #94a3b8; font-style: italic;">Danh sách vật tư đang được đồng bộ trực tiếp từ hệ thống quản lý kho...</p>
                <?php endif; ?>
            </div>

            <div class="policy-box">
                <h2 class="section-title-premium" style="margin-top: 0; margin-bottom: 25px;"><i class="fa-solid fa-file-shield"></i> Chính sách lưu trú</h2>
                
                <div style="margin-bottom: 25px; display: flex; flex-wrap: wrap;">
                    <div class="time-badge"><i class="fa-solid fa-clock"></i> Nhận phòng: <strong><?= htmlspecialchars($roomType['check_in_time'] ?? '14:00 - 23:00') ?></strong></div>
                    <div class="time-badge"><i class="fa-solid fa-calendar-check"></i> Trả phòng: <strong><?= htmlspecialchars($roomType['check_out_time'] ?? '12:00') ?></strong></div>
                </div>

                <div style="display: grid; gap: 24px; font-size: 1rem;">
                    <div>
                        <strong style="color: var(--luxury-navy); display: block; margin-bottom: 6px; font-size: 1.1rem;">Nhận phòng sớm / Trả phòng muộn</strong>
                        <div style="color: #475569; line-height: 1.6;"><?= nl2br(htmlspecialchars($roomType['policy_check_in'] ?: 'Check-in sớm hoặc Check-out muộn phụ thuộc vào tình trạng phòng thực tế tại thời điểm yêu cầu. Có thể phát sinh phụ phí từ 30% - 100% giá phòng tùy vào khung giờ.')) ?></div>
                    </div>
                </div>
            </div>

            <?php if ($searchPerformed): ?>
            <div id="availability-results" style="margin-top: 60px; padding-top: 40px; border-top: 2px solid #f1f5f9;">
                <h2 class="section-title-premium" style="margin-top: 0;"><i class="fa-solid fa-door-open"></i> Phòng hiện đang trống</h2>
                <p style="color: #64748b; margin-bottom: 30px;">Kết quả tìm kiếm cho giai đoạn từ <strong><?= date('d/m/Y', strtotime($checkIn)) ?></strong> đến <strong><?= date('d/m/Y', strtotime($checkOut)) ?></strong></p>

                <?php if (!empty($availableRooms)): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
                        <?php foreach ($availableRooms as $room): ?>
                            <div style="background: white; border: 1px solid #f1f5f9; border-radius: 20px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.borderColor='var(--primary)'; this.style.transform='translateY(-5px)'" onmouseout="this.style.borderColor='#f1f5f9'; this.style.transform='translateY(0)'">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                                    <div>
                                        <div style="font-size: 0.8rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Phòng số</div>
                                        <div style="font-size: 1.8rem; font-weight: 800; color: var(--luxury-navy);"><?= htmlspecialchars($room['room_number']) ?></div>
                                    </div>
                                    <div style="background: #eff6ff; color: #2563eb; padding: 6px 14px; border-radius: 50px; font-size: 0.85rem; font-weight: 700;">
                                        Tầng <?= htmlspecialchars($room['floor']) ?>
                                    </div>
                                </div>
                                <div style="display: grid; gap: 10px; margin-bottom: 25px; font-size: 0.9rem; color: #64748b;">
                                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-location-dot" style="width: 15px;"></i> <?= htmlspecialchars($room['location'] ?: 'Khu vực A') ?></div>
                                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-wifi" style="width: 15px;"></i> Wi-Fi tốc độ cao miễn phí</div>
                                </div>
                                <a href="<?= \App\Core\url('rooms/' . $room['id'] . '?check_in=' . $checkIn . '&check_out=' . $checkOut) ?>" 
                                   class="btn btn-primary" 
                                   style="width: 100%; padding: 12px; border-radius: 12px; font-weight: 700; display: block; text-align: center;">
                                    Đặt phòng ngay
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px; background: #fff1f2; border-radius: 24px; border: 1px dashed #fda4af;">
                        <i class="fa-solid fa-calendar-times" style="font-size: 3rem; color: #f43f5e; margin-bottom: 20px;"></i>
                        <h3 style="color: #9f1239;">Rất tiếc, đã hết loại phòng này</h3>
                        <p style="color: #be123c;">Vui lòng chọn ngày khác hoặc tham khảo các hạng phòng khác của chúng tôi.</p>
                    </div>
                <?php endif; ?>
            </div>

            <script>
                // Smooth scroll to results if search was performed
                window.addEventListener('load', function() {
                    const resultsEl = document.getElementById('availability-results');
                    if (resultsEl) {
                        resultsEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            </script>
            <?php endif; ?>

            <!-- Reviews Section -->
            <div id="reviews" style="margin-top: 60px; padding-top: 40px; border-top: 2px solid #f1f5f9;">
                <h2 class="section-title-premium" style="margin-top: 0;"><i class="fa-solid fa-comments"></i> Tiếng nói khách hàng</h2>
                
                <?php if (!empty($reviews)): ?>
                    <div style="display: grid; gap: 20px;">
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-card">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                    <div>
                                        <div style="font-weight: 700; color: var(--luxury-navy); font-size: 1.1rem;"><?= htmlspecialchars($review['user_name'] ?? 'Khách hàng') ?></div>
                                        <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 2px;">
                                            <i class="fa-solid fa-calendar-day"></i> <?= date('d/m/Y', strtotime($review['created_at'] ?? 'now')) ?>
                                        </div>
                                    </div>
                                    <div style="color: #fbbf24; font-size: 0.9rem;">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <i class="fa-<?= $i <= ($review['rating'] ?? 5) ? 'solid' : 'regular' ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p style="color: #475569; line-height: 1.6; margin: 0; font-style: italic;">"<?= htmlspecialchars($review['comment'] ?? '') ?>"</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 50px; background: #f8fafc; border-radius: 20px;">
                        <i class="fa-solid fa-comment-slash" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                        <p style="color: #64748b;">Chưa có đánh giá nào cho loại phòng này. Hãy là người đầu tiên trải nghiệm!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-sidebar">
            <div class="booking-card">
                <div style="text-align: center; margin-bottom: 25px;">
                    <div style="font-size: 0.9rem; color: #64748b; margin-bottom: 8px;">Trải nghiệm tuyệt vời từ</div>
                    <div style="font-size: 2.8rem; font-weight: 800; color: var(--primary); line-height: 1;"><?= number_format($roomType['base_price'], 0, ',', '.') ?>đ</div>
                    <div style="font-size: 0.9rem; color: #64748b; margin-top: 8px;">/ đêm</div>
                </div>

                <form action="<?= \App\Core\url('room-types') ?>" method="GET">
                    <input type="hidden" name="id" value="<?= $roomType['id'] ?>">
                    <div style="display: grid; gap: 15px;">
                        <div class="form-group">
                            <label style="font-weight: 700; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Ngày nhận phòng</label>
                            <input type="date" name="check_in" value="<?= htmlspecialchars($checkIn ?? '') ?>" style="width: 100%; padding: 14px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-weight: 600;" required>
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 700; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Ngày trả phòng</label>
                            <input type="date" name="check_out" value="<?= htmlspecialchars($checkOut ?? '') ?>" style="width: 100%; padding: 14px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-weight: 600;" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 18px; border-radius: 14px; font-weight: 800; font-size: 1.1rem; margin-top: 10px; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);">
                            Kiểm tra phòng trống
                        </button>
                    </div>
                </form>

                <div style="margin-top: 30px; display: grid; gap: 12px; padding-top: 25px; border-top: 1px solid #f1f5f9; font-size: 0.85rem; color: #64748b;">
                    <div style="display: flex; align-items: center; gap: 10px;"><i class="fa-solid fa-shield-check" style="color: #22c55e;"></i> Bảo mật thông tin tuyệt đối</div>
                    <div style="display: flex; align-items: center; gap: 10px;"><i class="fa-solid fa-headset" style="color: var(--primary);"></i> Hỗ trợ 24/7 trong kỳ lưu trú</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="lightbox" class="lightbox-modal" onclick="closeLightbox()">
    <img id="lightbox-img" class="lightbox-content" src="">
</div>

<script>
    function openLightbox(src) {
        const modal = document.getElementById('lightbox');
        document.getElementById('lightbox-img').src = src;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
        document.body.style.overflow = '';
    }
</script>

