<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<?php
$serviceId = (int) ($service['id'] ?? 0);
$pricePerHour = (float) ($service['price_per_hour'] ?? 0);

$primaryImage = \App\Core\url('assets/image/image.png');
if (!empty($service['image_url'])) {
    $primaryImage = \App\Core\url($service['image_url']);
} elseif (!empty($service['images'][0]['image_url'])) {
    $primaryImage = \App\Core\url($service['images'][0]['image_url']);
}
?>

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

    .service-hero {
        position: relative;
        height: 50vh;
        min-height: 400px;
        overflow: hidden;
    }

    .service-hero img { width: 100%; height: 100%; object-fit: cover; }
    .service-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(15, 23, 42, 0.9) 0%, transparent 70%);
    }

    .service-hero-content {
        position: absolute;
        bottom: 60px;
        left: 0;
        right: 0;
        z-index: 10;
        color: white;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 40px;
        margin-top: -60px;
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

    .policy-box {
        background: #fdfaf3;
        border: 1px solid #f9731622;
        border-radius: 20px;
        padding: 30px;
        margin-top: 50px;
    }

    .form-group label {
        display: block;
        font-weight: 700;
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .form-control-premium {
        width: 100%;
        padding: 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .form-control-premium:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .calc-box-premium {
        background: #eff6ff;
        border-radius: 12px;
        padding: 15px;
        margin-top: 20px;
        border-left: 4px solid var(--primary);
    }

    @media (max-width: 991px) {
        .detail-grid { grid-template-columns: 1fr; margin-top: 0; }
        .detail-sidebar { position: static; }
        .service-hero { height: 40vh; }
    }
</style>

<div class="service-hero">
    <img src="<?= htmlspecialchars($primaryImage) ?>" alt="<?= htmlspecialchars($service['name']) ?>">
    <div class="service-hero-overlay"></div>
    <div class="service-hero-content">
        <div class="container text-center">
            <h1 style="font-size: 3.5rem; color: white; margin-bottom: 12px;"><?= htmlspecialchars($service['name']) ?></h1>
            <div style="color: rgba(255,255,255,0.9); font-size: 1.1rem; display: flex; justify-content: center; align-items: center; gap: 15px;">
                <span style="font-weight: 600; background: rgba(0,0,0,0.3); padding: 5px 15px; border-radius: 50px; border: 1px solid rgba(255,255,255,0.2);">
                    <?= htmlspecialchars($service['type_name'] ?? 'Dịch vụ cao cấp') ?>
                </span>
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
                    <span class="spec-value"><?= (int)($service['capacity'] ?? 1) ?> Khách</span>
                </div>
                <div class="spec-item">
                    <i class="fa-solid fa-clock"></i>
                    <span class="spec-label">Đơn vị</span>
                    <span class="spec-value">Tính theo giờ</span>
                </div>
                <div class="spec-item">
                    <i class="fa-solid fa-tags"></i>
                    <span class="spec-label">Giá cơ bản</span>
                    <span class="spec-value"><?= number_format($pricePerHour) ?>đ</span>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <h2 class="section-title-premium" style="margin-top: 0;"><i class="fa-solid fa-star"></i> Giới thiệu dịch vụ</h2>
                <div style="line-height: 1.9; font-size: 1.1rem; color: #475569;">
                    <?= nl2br(htmlspecialchars($service['description'] ?? 'Trải nghiệm dịch vụ đẳng cấp thế giới tại Antigravity Hotel, nơi mọi chi tiết đều được chăm chút tỉ mỉ để mang lại sự hài lòng tuyệt đối cho quý khách.')) ?>
                </div>
            </div>

            <?php if (!empty($service['images'])): ?>
            <div class="gallery-preview">
                <?php foreach($service['images'] as $img): ?>
                <img src="<?= htmlspecialchars(\App\Core\url($img['image_url'] ?? '')) ?>" 
                     alt="Gallery Image" 
                     onclick="window.open(this.src, '_blank')">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <h2 class="section-title-premium"><i class="fa-solid fa-wand-magic-sparkles"></i> Tiện ích đi kèm</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                <div style="display: flex; align-items: center; gap: 10px; padding: 12px; background: var(--luxury-slate); border-radius: 12px;">
                    <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                    <span style="font-weight: 500;">Phục vụ tận tâm</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; padding: 12px; background: var(--luxury-slate); border-radius: 12px;">
                    <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                    <span style="font-weight: 500;">Không gian riêng tư</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; padding: 12px; background: var(--luxury-slate); border-radius: 12px;">
                    <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                    <span style="font-weight: 500;">Trang thiết bị hiện đại</span>
                </div>
            </div>

            <div class="policy-box">
                <h2 class="section-title-premium" style="margin-top: 0; margin-bottom: 25px;"><i class="fa-solid fa-file-shield"></i> Lưu ý & Chính sách</h2>
                <div style="color: #475569; line-height: 1.6;">
                    <p style="margin-bottom: 12px;">• Quý khách vui lòng đặt trước ít nhất 1 giờ để chúng tôi chuẩn bị chu đáo nhất.</p>
                    <p style="margin-bottom: 12px;">• Việc hủy đặt chỗ cần được thực hiện trước 30 phút so với giờ bắt đầu.</p>
                    <p>• Phí dịch vụ có thể thay đổi tùy theo số lượng yêu cầu phát sinh tại chỗ.</p>
                </div>
            </div>
        </div>

        <div class="detail-sidebar">
            <div class="booking-card">
                <div style="text-align: center; margin-bottom: 25px;">
                    <div style="font-size: 0.9rem; color: #64748b; margin-bottom: 8px;">Giá chỉ từ</div>
                    <div style="font-size: 2.8rem; font-weight: 800; color: var(--primary); line-height: 1;"><?= number_format($pricePerHour, 0, ',', '.') ?>đ</div>
                    <div style="font-size: 0.9rem; color: #64748b; margin-top: 8px;">/ giờ / khách</div>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; padding:12px; border-radius:12px; margin-bottom:20px; font-size: 0.9rem;">
                        <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars((string) $_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= \App\Core\url('/services/book') ?>">
                    <input type="hidden" name="service_id" value="<?= $serviceId ?>">

                    <div style="display: grid; gap: 15px;">
                        <div class="form-group">
                            <label>Thời gian bắt đầu</label>
                            <input type="datetime-local" id="start_time" name="start_time" class="form-control-premium" required>
                        </div>
                        <div class="form-group">
                            <label>Thời gian kết thúc</label>
                            <input type="datetime-local" id="end_time" name="end_time" class="form-control-premium" required>
                        </div>
                        <div class="form-group">
                            <label>Số lượng khách</label>
                            <input type="number" id="quantity" name="quantity" min="1" value="1" class="form-control-premium" required>
                        </div>
                        <div class="form-group">
                            <label>Mã booking phòng (Khách đang ở)</label>
                            <input type="number" id="booking_id" name="booking_id" placeholder="Bỏ trống nếu là khách vãng lai" class="form-control-premium">
                        </div>

                        <div id="availabilityBox" style="font-size: 0.85rem; font-weight: 600; text-align: center;"></div>

                        <div id="calcBox" class="calc-box-premium" style="display: none;">
                            <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 4px;">Ước tính phí dịch vụ</div>
                            <div id="calcContent" style="font-weight: 700; color: #1e40af; font-size: 1.1rem;"></div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 18px; border-radius: 14px; font-weight: 800; font-size: 1.1rem; margin-top: 10px; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);">
                            Đặt dịch vụ ngay
                        </button>
                    </div>
                </form>

                <div style="margin-top: 30px; display: grid; gap: 12px; padding-top: 25px; border-top: 1px solid #f1f5f9; font-size: 0.85rem; color: #64748b;">
                    <div style="display: flex; align-items: center; gap: 10px;"><i class="fa-solid fa-shield-check" style="color: #22c55e;"></i> Đảm bảo an toàn sức khỏe</div>
                    <div style="display: flex; align-items: center; gap: 10px;"><i class="fa-solid fa-headset" style="color: var(--primary);"></i> Hỗ trợ kỹ thuật 24/7</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(() => {
    const startEl = document.getElementById('start_time');
    const endEl = document.getElementById('end_time');
    const quantityEl = document.getElementById('quantity');
    const calcBox = document.getElementById('calcBox');
    const calcContent = document.getElementById('calcContent');
    const availabilityBox = document.getElementById('availabilityBox');
    const pricePerHour = <?= json_encode($pricePerHour) ?>;
    const serviceId = <?= json_encode($serviceId) ?>;

    const renderCalc = () => {
        if (!startEl.value || !endEl.value) {
            calcBox.style.display = 'none';
            return;
        }
        const start = new Date(startEl.value);
        const end = new Date(endEl.value);
        const diffMs = end - start;
        if (diffMs <= 0) {
            calcContent.textContent = 'Thời gian không hợp lệ';
            calcBox.style.display = 'block';
            return;
        }
        const quantity = parseInt(quantityEl.value) || 1;
        const hours = Math.max(1, Math.ceil(diffMs / (1000 * 60 * 60)));
        const total = hours * pricePerHour * quantity;
        
        calcContent.textContent = total.toLocaleString('vi-VN') + ' VND';
        calcBox.style.display = 'block';
    };

    const checkAvailability = async () => {
        if (!startEl.value || !endEl.value) return;
        const quantity = parseInt(quantityEl.value) || 1;
        const body = new URLSearchParams({
            service_id: String(serviceId),
            start_time: startEl.value.replace('T', ' ') + ':00',
            end_time: endEl.value.replace('T', ' ') + ':00',
            quantity: String(quantity)
        });
        
        availabilityBox.textContent = 'Đang kiểm tra...';
        availabilityBox.style.color = '#64748b';

        try {
            const response = await fetch('<?= \App\Core\url('/services/check-availability') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: body.toString()
            });
            const data = await response.json();
            availabilityBox.style.color = data.available ? '#166534' : '#991b1b';
            availabilityBox.textContent = data.available ? '● Khung giờ hiện đang trống' : '● ' + (data.error || 'Khung giờ đã có đặt trước');
        } catch (e) {
            availabilityBox.style.color = '#991b1b';
            availabilityBox.textContent = 'Không thể kiểm tra lúc này';
        }
    };

    [startEl, endEl, quantityEl].forEach((el) => {
        el.addEventListener('change', () => {
            renderCalc();
            checkAvailability();
        });
    });
})();
</script>

