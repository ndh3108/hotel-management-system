<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

<style>
:root {
    --primary: 37, 99, 235; /* rgb value for primary blue */
    --primary-color: #2563eb;
    --primary-dark: #1d4ed8;
    --luxury-gold: #d4af37;
    --luxury-navy: #0f172a;
    --luxury-slate: #f8fafc;
    --font-heading: 'Outfit', sans-serif;
    --font-body: 'Inter', sans-serif;
    --radius-premium: 20px;
    --shadow-premium: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}

body {
    font-family: var(--font-body);
    color: #334155;
    background-color: #fff;
}

h1, h2, h3, h4, .section-title {
    font-family: var(--font-heading);
}

/* --- Hero Slider Redesign --- */
.hero {
    position: relative;
    height: 100vh;
    min-height: 800px;
    display: flex;
    align-items: center;
    background: #000;
}

.swiper-container-hero {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.swiper-slide-bg {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: scale(1.05); /* initial scale for kenburns */
    transition: transform 10s linear;
}

.swiper-slide-active .swiper-slide-bg {
    transform: scale(1);
}

.video-bg {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.4) 50%, rgba(0, 0, 0, 0.1) 100%);
    z-index: 2;
    pointer-events: none;
}

.hero-content {
    position: relative;
    z-index: 10;
    max-width: 900px;
    padding-left: 8%;
    margin-top: -80px; 
}

.hero-subtitle {
    display: inline-block;
    color: var(--luxury-gold);
    text-transform: uppercase;
    letter-spacing: 5px;
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 24px;
    border-left: 3px solid var(--luxury-gold);
    padding-left: 12px;
}

.hero-title {
    font-size: clamp(3.5rem, 8vw, 5.5rem);
    color: white;
    line-height: 1.05;
    margin-bottom: 28px;
    font-weight: 800;
}

.hero-desc {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    max-width: 600px;
    line-height: 1.7;
    margin-bottom: 48px;
    font-weight: 300;
}

.hero-buttons {
    display: flex;
    gap: 20px;
}

.btn-luxury {
    padding: 16px 36px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.05rem;
    text-transform: none;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    font-family: var(--font-heading);
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-luxury-primary {
    background: var(--primary-color);
    color: white;
    box-shadow: 0 10px 20px -3px rgba(37, 99, 235, 0.4);
    border: 1px solid var(--primary-color);
}

.btn-luxury-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-3px);
    box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.5);
    border-color: var(--primary-dark);
}

.btn-luxury-outline {
    background: transparent;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.4);
    backdrop-filter: blur(5px);
}

.btn-luxury-outline:hover {
    background: white;
    color: var(--luxury-navy);
    transform: translateY(-3px);
}

/* --- Search Box Redesign --- */
.search-box {
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translate(-50%, 50%);
    width: min(1200px, 92%);
    background: white;
    padding: 35px 40px;
    border-radius: var(--radius-premium);
    box-shadow: var(--shadow-premium);
    z-index: 120;
}

.search-form {
    display: grid;
    grid-template-columns: repeat(4, 1fr) auto;
    gap: 20px;
    align-items: end;
}

.form-group label {
    display: block;
    font-weight: 700;
    font-size: 0.8rem;
    text-transform: uppercase;
    color: #475569;
    margin-bottom: 10px;
    letter-spacing: 1px;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 14px 18px;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 500;
    color: #1e293b;
    transition: all 0.2s ease;
    background-color: #fff;
}

.form-group input:focus, .form-group select:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
}

.search-btn {
    height: 54px;
    padding: 0 35px;
    border-radius: 12px;
    background: var(--luxury-navy);
    color: white;
    border: none;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.search-btn:hover {
    background: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
}

/* --- Section Styling --- */
.section {
    padding: 110px 0;
}

.section-alt {
    background-color: var(--luxury-slate);
}

.section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto 60px;
}

.section-subtitle {
    display: inline-block;
    color: var(--primary-color);
    font-weight: 800;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 3px;
    margin-bottom: 16px;
}

.section-title {
    font-size: clamp(2.2rem, 5vw, 3rem);
    color: var(--luxury-navy);
    margin-bottom: 20px;
    font-weight: 800;
    line-height: 1.2;
}

.section-desc {
    color: #64748b;
    font-size: 1.1rem;
    line-height: 1.7;
}

/* --- Premium Cards Redesign (Rooms & Services) --- */
.item-card {
    border-radius: var(--radius-premium);
    overflow: hidden;
    background: white;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.item-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 40px -10px rgba(0, 0, 0, 0.1);
}

.item-image {
    height: 280px;
    position: relative;
    overflow: hidden;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
}

.item-card:hover .item-image img {
    transform: scale(1.08);
}

.item-badge {
    position: absolute;
    top: 20px;
    left: 20px;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    color: var(--luxury-navy);
    font-weight: 800;
    font-size: 0.75rem;
    text-transform: uppercase;
    border-radius: 50px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}

.item-content {
    padding: 30px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.item-title {
    font-size: 1.5rem;
    margin-bottom: 16px;
    font-weight: 700;
    color: var(--luxury-navy);
}

.item-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    color: #475569;
    font-size: 0.9rem;
    margin-bottom: 24px;
    font-weight: 500;
}

.item-meta i {
    color: var(--primary-color);
    margin-right: 6px;
}

.item-footer {
    margin-top: auto;
    padding-top: 24px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.item-price {
    display: flex;
    flex-direction: column;
}

.item-price .amount {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--primary-color);
    line-height: 1;
}

.item-price .period {
    color: #94a3b8;
    font-size: 0.85rem;
    margin-top: 4px;
}

.btn-card-luxury {
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 700;
    background: var(--luxury-navy);
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.btn-card-luxury:hover {
    background: var(--primary-color);
    box-shadow: 0 4px 12px rgba(37,99,235,0.3);
}

/* --- Amenities Redesign --- */
.amenities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
}

.amenity-card {
    background: white;
    padding: 40px 30px;
    border-radius: var(--radius-premium);
    text-align: center;
    border: 1px solid #f1f5f9;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.amenity-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: linear-gradient(135deg, rgba(37,99,235,0.03) 0%, rgba(37,99,235,0) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.amenity-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-premium);
    border-color: #e2e8f0;
}

.amenity-card:hover::before {
    opacity: 1;
}

.amenity-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: rgba(37, 99, 235, 0.08);
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 24px;
    transition: all 0.3s ease;
}

.amenity-card:hover .amenity-icon {
    background: var(--primary-color);
    color: white;
    transform: scale(1.1);
}

.amenity-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--luxury-navy);
    margin-bottom: 12px;
}

.amenity-desc {
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.6;
}

.amenity-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 20px;
    color: var(--primary-color);
    font-weight: 600;
    font-size: 0.9rem;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
}

.amenity-card:hover .amenity-action {
    opacity: 1;
    transform: translateY(0);
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.8);
    backdrop-filter: blur(8px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.modal-content {
    background: white;
    width: 90%;
    max-width: 600px;
    border-radius: 24px;
    position: relative;
    transform: scale(0.95) translateY(20px);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
}

.modal-overlay.active .modal-content {
    transform: scale(1) translateY(0);
    opacity: 1;
}

.modal-close {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 40px;
    height: 40px;
    background: rgba(0,0,0,0.05);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--luxury-navy);
    cursor: pointer;
    font-size: 1.2rem;
    z-index: 10;
    transition: background 0.2s, color 0.2s;
    border: none;
}

.modal-close:hover {
    background: var(--luxury-navy);
    color: white;
}

.modal-image {
    width: 100%;
    height: 250px;
    background: #f1f5f9;
}

.modal-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.modal-body {
    padding: 40px;
}

.modal-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--luxury-navy);
    margin-bottom: 16px;
    font-family: var(--font-heading);
}

.modal-text {
    color: #475569;
    font-size: 1.05rem;
    line-height: 1.7;
    margin-bottom: 24px;
}

/* Call to action */
.cta-section {
    background: linear-gradient(135deg, var(--luxury-navy) 0%, #1e293b 100%);
    color: white;
    padding: 120px 0;
    text-align: center;
}

@media (max-width: 1024px) {
    .search-form {
        grid-template-columns: repeat(2, 1fr);
    }
    .search-btn {
        grid-column: span 2;
    }
}

@media (max-width: 768px) {
    .section { padding: 80px 0; }
    .hero {
        height: auto;
        min-height: 700px;
    }
    .hero-content {
        padding-left: 20px;
        padding-right: 20px;
        text-align: center;
        margin-top: -120px; /* Make space for search box */
    }
    .hero-subtitle { border-left: none; padding-left: 0; }
    .hero-desc { margin-inline: auto; font-size: 1.05rem; }
    .hero-buttons { justify-content: center; flex-direction: column; align-items: stretch;}
    .search-box {
        position: relative;
        transform: none;
        left: 0;
        bottom: 0;
        width: 100%;
        margin-top: -60px;
        border-radius: 24px 24px 0 0;
        padding: 30px 20px;
    }
    .search-form {
        grid-template-columns: 1fr;
    }
    .search-btn {
        grid-column: span 1;
    }
}
</style>

<!-- Hero Slider Section -->
<section class="hero">
    <!-- Swiper Container -->
    <div class="swiper swiper-container-hero">
        <div class="swiper-wrapper">
            <!-- Slide 1: Video -->
            <div class="swiper-slide">
                <video class="video-bg" autoplay loop muted playsinline>
                    <!-- Using a generic luxury sample video -->
                    <source src="https://player.vimeo.com/external/498457220.hd.mp4?s=d7607a97fd02ca74df44a778e5abdfceb7f16a04&profile_id=174" type="video/mp4">
                    <!-- Fallback image -->
                    <img src="<?= \App\Core\url('assets/image/banner/banner-1.png') ?>" class="swiper-slide-bg" alt="Luxury Hotel">
                </video>
            </div>
            <!-- Slide 2: Image -->
            <div class="swiper-slide">
                <img src="<?= \App\Core\url('assets/image/banner/banner-1.png') ?>" class="swiper-slide-bg" alt="Grand Entrance">
            </div>
            <!-- Slide 3: Image -->
            <div class="swiper-slide">
                <img src="<?= \App\Core\url('assets/image/banner/banner-2.png') ?>" class="swiper-slide-bg" alt="Infinity Pool">
            </div>
            <!-- Slide 4: Image -->
            <div class="swiper-slide">
                <img src="<?= \App\Core\url('assets/image/banner/banner-3.png') ?>" class="swiper-slide-bg" alt="Luxury Suites">
            </div>
            <!-- Slide 5: Image -->
            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1542314831-c6a4d14d8373?auto=format&fit=crop&q=80&w=2000" class="swiper-slide-bg" alt="Dining">
            </div>
            <!-- Slide 6: Image -->
            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&q=80&w=2000" class="swiper-slide-bg" alt="Spa & Wellness">
            </div>
        </div>
    </div>
    
    <div class="hero-overlay"></div>

    <div class="hero-content">
        <span class="hero-subtitle">BEYOND THE ORDINARY</span>
        <h1 class="hero-title">Kiến Tạo Kỳ Nghỉ<br>Đẳng Cấp Tối Thượng</h1>
        <p class="hero-desc">
            Đắm mình trong không gian nghỉ dưỡng tinh tế, nơi nghệ thuật kiến trúc hòa quyện cùng hệ thống tiện nghi 5 sao, mang đến trải nghiệm lưu trú vượt xa mọi sự mong đợi.
        </p>
        <div class="hero-buttons">
            <a href="#rooms-section" class="btn-luxury btn-luxury-primary">Khám Phá Phòng</a>
            <a href="<?= \App\Core\url('rooms/search') ?>" class="btn-luxury btn-luxury-outline">Đặt Chỗ Ngay</a>
        </div>
    </div>
</section>

<!-- Floating Search Box (Moved immediately after hero to flow correctly) -->
<div class="container relative z-20">
    <div class="search-box">
        <form class="search-form" action="<?= \App\Core\url('rooms/search') ?>" method="GET">
            <div class="form-group">
                <label>Ngày nhận phòng</label>
                <input type="date" name="check_in" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Ngày trả phòng</label>
                <input type="date" name="check_out" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
            </div>
            <div class="form-group">
                <label>Số lượng khách</label>
                <select name="guests">
                    <option value="1">1 Khách</option>
                    <option value="2" selected>2 Khách</option>
                    <option value="3">3 Khách</option>
                    <option value="4">4 Khách+</option>
                </select>
            </div>
            <div class="form-group">
                <label>Hạng phòng</label>
                <select name="room_type_id">
                    <option value="">Tất cả hạng phòng</option>
                    <?php if (!empty($roomTypes)): ?>
                        <?php foreach ($roomTypes as $rt): ?>
                            <option value="<?= $rt['id'] ?>"><?= htmlspecialchars($rt['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm Kiếm
            </button>
        </form>
    </div>
</div>

<!-- ================= 1. Rooms Section (Moved to first position after search) ================= -->
<section id="rooms-section" class="section section-alt" style="padding-top: 180px;">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Luxury Stays</span>
            <h2 class="section-title">Danh Sách Phòng Dành Cho Bạn</h2>
            <p class="section-desc">Hệ thống phòng nghỉ cao cấp được thiết kế tinh xảo, giao thoa giữa nét đẹp đương đại và nội thất sang trọng mang lại giấc ngủ hoàn hảo.</p>
        </div>

        <div class="rooms-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 35px;">
            <?php if (!empty($roomTypes)): ?>
                <?php foreach (array_slice($roomTypes, 0, 6) as $rt): ?>
                    <div class="item-card">
                        <div class="item-image">
                            <span class="item-badge"><?= $rt['base_price'] > 1500000 ? 'Premium' : 'Standard' ?></span>
                            <img src="<?= \App\Core\url($rt['image_url'] ?? 'assets/image/image.png') ?>" alt="<?= htmlspecialchars($rt['name']) ?>">
                        </div>
                        <div class="item-content">
                            <h3 class="item-title"><?= htmlspecialchars($rt['name']) ?></h3>
                            <div class="item-meta">
                                <span><i class="fa-solid fa-expand"></i> Diện tích: <?= $rt['area_sqm'] ?? '35' ?>m²</span>
                                <span><i class="fa-solid fa-bed"></i> Giường: <?= $rt['capacity_adults'] > 1 ? 'King Size' : 'Single' ?></span>
                                <span><i class="fa-solid fa-user-group"></i> Max: <?= $rt['capacity_adults'] ?? 2 ?> Người lớn</span>
                            </div>
                            <div class="item-footer">
                                <div class="item-price">
                                    <span class="amount"><?= number_format($rt['base_price'] ?? 0, 0, ',', '.') ?> <span style="font-size: 1rem; font-weight:600;">VNĐ</span></span>
                                    <span class="period">mỗi đêm</span>
                                </div>
                                <a href="<?= \App\Core\url('room-types?id=' . $rt['id']) ?>" class="btn-card-luxury">Chi Tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div style="text-align: center; margin-top: 60px;">
            <a href="<?= \App\Core\url('rooms') ?>" class="btn-luxury btn-luxury-primary" style="background: var(--luxury-navy);">Xem Tất Cả Hạng Phòng <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i></a>
        </div>
    </div>
</section>

<!-- ================= 2. Amenities Section (Expanded with Modal functionality) ================= -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">World-class Facilities</span>
            <h2 class="section-title">Tiện Nghi Đẳng Cấp</h2>
            <p class="section-desc">Từ hồ bơi vô cực ngắm trọn đại dương đến ẩm thực Michelin tinh tế, mọi trải nghiệm tại đây đều hướng đến sự hoàn mỹ.</p>
        </div>

        <!-- 8 Amenities Layout -->
        <div class="amenities-grid">
            <!-- Amenity 1 -->
            <div class="amenity-card" onclick="openAmenityModal('pool')">
                <div class="amenity-icon"><i class="fa-solid fa-water"></i></div>
                <h3 class="amenity-title">Hồ Bơi Vô Cực</h3>
                <p class="amenity-desc">Đắm mình vào làn nước mát lạnh ranh giới đất trời hòa làm một.</p>
                <div class="amenity-action"><span>Khám phá thêm</span> <i class="fa-solid fa-chevron-right"></i></div>
            </div>
            <!-- Amenity 2 -->
            <div class="amenity-card" onclick="openAmenityModal('dining')">
                <div class="amenity-icon"><i class="fa-solid fa-utensils"></i></div>
                <h3 class="amenity-title">Nhà Hàng Michelin</h3>
                <p class="amenity-desc">Ẩm thực thượng hạng chế tác bởi các đầu bếp danh tiếng quốc tế.</p>
                <div class="amenity-action"><span>Khám phá thêm</span> <i class="fa-solid fa-chevron-right"></i></div>
            </div>
            <!-- Amenity 3 -->
            <div class="amenity-card" onclick="openAmenityModal('spa')">
                <div class="amenity-icon"><i class="fa-solid fa-spa"></i></div>
                <h3 class="amenity-title">Royal Oasis Spa</h3>
                <p class="amenity-desc">Phục hồi năng lượng tinh thần qua các liệu trình trị liệu độc quyền.</p>
                <div class="amenity-action"><span>Khám phá thêm</span> <i class="fa-solid fa-chevron-right"></i></div>
            </div>
            <!-- Amenity 4 -->
            <div class="amenity-card" onclick="openAmenityModal('gym')">
                <div class="amenity-icon"><i class="fa-solid fa-dumbbell"></i></div>
                <h3 class="amenity-title">Fitness Center 24/7</h3>
                <p class="amenity-desc">Hệ thống phòng tập trang bị máy móc tối tân từ Technogym.</p>
                <div class="amenity-action"><span>Khám phá thêm</span> <i class="fa-solid fa-chevron-right"></i></div>
            </div>
            <!-- Amenity 5 -->
            <div class="amenity-card" onclick="openAmenityModal('kids')">
                <div class="amenity-icon"><i class="fa-solid fa-child-reaching"></i></div>
                <h3 class="amenity-title">Kid's Wonderland</h3>
                <p class="amenity-desc">Thiên đường vui chơi an toàn và phát triển sáng tạo cho trẻ nhỏ.</p>
                <div class="amenity-action"><span>Khám phá thêm</span> <i class="fa-solid fa-chevron-right"></i></div>
            </div>
            <!-- Amenity 6 -->
            <div class="amenity-card" onclick="openAmenityModal('concierge')">
                <div class="amenity-icon"><i class="fa-solid fa-bell-concierge"></i></div>
                <h3 class="amenity-title">Quản Gia Riêng biệt</h3>
                <p class="amenity-desc">Dịch vụ chăm sóc cá nhân hóa 24/7 đáp ứng mọi yêu cầu khắt khe.</p>
                <div class="amenity-action"><span>Khám phá thêm</span> <i class="fa-solid fa-chevron-right"></i></div>
            </div>
            <!-- Amenity 7 -->
            <div class="amenity-card" onclick="openAmenityModal('chauffeur')">
                <div class="amenity-icon"><i class="fa-solid fa-car-side"></i></div>
                <h3 class="amenity-title">Đưa Đón Hạng Sang</h3>
                <p class="amenity-desc">Trải nghiệm dịch vụ chuyên chở cao cấp với dòng xe Maybach và đội ngũ tài xế riêng.</p>
                <div class="amenity-action"><span>Khám phá thêm</span> <i class="fa-solid fa-chevron-right"></i></div>
            </div>
            <!-- Amenity 8 -->
            <div class="amenity-card" onclick="openAmenityModal('beach')">
                <div class="amenity-icon"><i class="fa-solid fa-umbrella-beach"></i></div>
                <h3 class="amenity-title">Bãi Biển Tư Nhân</h3>
                <p class="amenity-desc">Tận hưởng không gian riêng tư tuyệt đối trên dải cát trắng mịn màng.</p>
                <div class="amenity-action"><span>Khám phá thêm</span> <i class="fa-solid fa-chevron-right"></i></div>
            </div>
        </div>
    </div>
</section>

<!-- ================= 3. Services Section ================= -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Exclusive Services</span>
            <h2 class="section-title">Dịch Vụ Khác Theo Khung Giờ</h2>
            <p class="section-desc">Tối ưu thời gian lưu trú với hệ thống phòng họp hạng sang, không gian làm việc số và hội trường yến tiệc chuyên nghiệp lộng lẫy.</p>
        </div>

        <div class="rooms-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 35px;">
            <?php if (!empty($services)): ?>
                <?php foreach (array_slice($services, 0, 6) as $srv): ?>
                    <div class="item-card">
                        <div class="item-image">
                            <span class="item-badge"><?= htmlspecialchars($srv['type_name'] ?? 'Service') ?></span>
                            <img src="<?= \App\Core\url($srv['image_url'] ?? 'assets/image/image.png') ?>" alt="<?= htmlspecialchars($srv['name']) ?>">
                        </div>
                        <div class="item-content">
                            <h3 class="item-title"><?= htmlspecialchars($srv['name']) ?></h3>
                            <div class="item-meta">
                                <span><i class="fa-solid fa-users"></i> Sức chứa tới: <?= $srv['capacity'] ?? 1 ?> người</span>
                                <span style="color: #059669; font-weight: 700;"><i class="fa-solid fa-bolt"></i> Có thể đặt ngay</span>
                            </div>
                            <div class="item-footer">
                                <div class="item-price">
                                    <span class="amount"><?= number_format($srv['price_per_hour'] ?? 0, 0, ',', '.') ?> <span style="font-size: 1rem; font-weight:600;">VNĐ</span></span>
                                    <span class="period">tính theo 1 giờ</span>
                                </div>
                                <a href="<?= \App\Core\url('services/' . $srv['id']) ?>" class="btn-card-luxury">Đăng Ký Đặt</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div style="text-align: center; margin-top: 60px;">
            <a href="<?= \App\Core\url('services') ?>" class="btn-luxury btn-luxury-outline" style="color: var(--luxury-navy); border-color: var(--luxury-navy);">Danh Sách Tất Cả Dịch Vụ</a>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section">
    <div class="container">
        <h2 style="font-size: clamp(2rem, 5vw, 3.5rem); font-family: var(--font-heading); font-weight: 800; margin-bottom: 24px;">Trải Nghiệm Mọi Giác Quan Thức Tỉnh</h2>
        <p style="font-size: 1.25rem; color: #94a3b8; max-width: 700px; margin: 0 auto 40px; line-height: 1.6;">Khám phá bộ sưu tập ưu đãi mùa vàng. Giảm ngay lên tới 25% cho các hóa đơn đặt phòng sớm trải nghiệm dịch vụ hoàng gia.</p>
        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
            <a href="<?= \App\Core\url('rooms/search') ?>" class="btn-luxury btn-luxury-primary" style="font-size: 1.1rem; padding: 18px 45px;">Tiến Hành Đặt Phòng</a>
            <a href="tel:02812345678" class="btn-luxury btn-luxury-outline" style="font-size: 1.1rem; padding: 18px 45px;"><i class="fa-solid fa-phone mr-2"></i> 028.1234.5678</a>
        </div>
    </div>
</section>


<!-- ================= Modal System for Amenities ================= -->
<div id="amenityModal" class="modal-overlay">
    <div class="modal-content">
        <button class="modal-close" onclick="closeAmenityModal()"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-image">
            <img id="modalImg" src="" alt="Amenity Image">
        </div>
        <div class="modal-body">
            <h3 id="modalTitle" class="modal-title">Tiêu Đề Tiện Nghi</h3>
            <p id="modalDesc" class="modal-text">Nội dung chi tiết về tiện nghi sẽ hiển thị tại đây.</p>
            <a href="<?= \App\Core\url('rooms') ?>" class="btn-luxury btn-luxury-primary" style="width: 100%;">Trải Nghiệm Ngay</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    // Initialize Swiper Hero
    document.addEventListener('DOMContentLoaded', function () {
        if(typeof Swiper !== 'undefined') {
            const swiper = new Swiper('.swiper-container-hero', {
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                speed: 1500,
                autoplay: {
                    delay: 6000,
                    disableOnInteraction: false,
                },
                loop: true,
                allowTouchMove: false
            });
        }
    });

    // Amenities Modal Data
    const amenitiesData = {
        'pool': {
            title: 'Hồ Bơi Vô Cực Trên Cao',
            desc: 'Thừa hưởng thiết kế không gian mở với tầm nhìn panorama ôm trọn cảnh sắc đại dương tuyệt mỹ. Nhiệt độ nước điều chỉnh thông minh theo mùa, đi kèm cabana cá nhân và phục vụ cocktail tại ghế.',
            image: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&q=80&w=800' // Placeholder fallback
        },
        'dining': {
            title: 'Nhà Hàng Chuẩn Michelin',
            desc: 'Bản giao hưởng ẩm thực phương Đông hòa quyện phương Tây từ đôi tay tì phép của các Siêu Đầu Bếp. Không gian lãng mạn dưới ánh nến và hầm rượu vang lên đến hàng vạn chai.',
            image: 'https://images.unsplash.com/photo-1542314831-c6a4d14d8373?auto=format&fit=crop&q=80&w=800'
        },
        'spa': {
            title: 'Royal Oasis Spa',
            desc: 'Liệu pháp chăm sóc sức khỏe hoàng gia áp dụng nguyên liệu thuần thiên nhiên. Chúng tôi mang đến phút giây thư giãn tuyệt đối, loại bỏ căng thẳng triệt để và kích thích tái tạo năng lượng.',
            image: 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&q=80&w=800'
        },
        'gym': {
            title: 'Trung Tâm Thể Hình 24/7',
            desc: 'Cung cấp không gian lập luyện ngập ánh sáng tự nhiên, trang bị máy tập đỉnh cao từ Technogym và đội ngũ HLV cá nhân chuyên nghiệp sẵn sàng thiết kế lộ trình riêng cho bạn.',
            image: 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&q=80&w=800'
        },
        'kids': {
            title: 'Kid\'s Wonderland',
            desc: 'Thiên đường giải trí dành riêng cho thượng khách nhí. Đa dạng hoạt động giáo dục, nghệ thuật tạo hình, trò chơi vận động an toàn với người bảo mẫu trực 24/24.',
            image: 'https://images.unsplash.com/photo-1566453664790-281ce13f3cb1?auto=format&fit=crop&q=80&w=800'
        },
        'concierge': {
            title: 'Dịch Vụ Quản Gia Cá Nhân',
            desc: 'Sự chuyên nghiệp đỉnh cao khi mỗi một khách lưu trú đều có quản gia riêng biệt. Từ sắp xếp bữa ăn riêng tư, đặt vé sự kiện đến việc cá nhân nhỏ nhất, chúng tôi luôn tận tâm.',
            image: 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&q=80&w=800'
        },
        'chauffeur': {
            title: 'Đưa Đón Siêu Sang',
            desc: 'Dịch vụ đưa đón tận nơi đẳng cấp với đội xe Mercedes Maybach, Rolls-Royce. Không gian nội thất tĩnh lặng biệt lập cùng với tài xế có tay lái êm ái cùng phong thái chuyên nghiệp.',
            image: 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&q=80&w=800'
        },
        'beach': {
            title: 'Bãi Biển Tư Nhân Tách Biệt',
            desc: 'Dải cát trắng mịn màng như pha lê chỉ dành riêng cho khách ưu tiên. Các tiện ích lều bạt mang phong cách nhiệt đới cùng dịch vụ bữa tối dưới sao sẽ làm bạn say đắm.',
            image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&q=80&w=800'
        }
    };

    const modal = document.getElementById('amenityModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalDesc = document.getElementById('modalDesc');
    const modalImg = document.getElementById('modalImg');

    function openAmenityModal(id) {
        const data = amenitiesData[id];
        if(data) {
            modalTitle.textContent = data.title;
            modalDesc.textContent = data.desc;
            modalImg.src = data.image; // Apply image dynamically
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }
    }

    function closeAmenityModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close on click outside
    modal.addEventListener('click', function(e) {
        if(e.target === modal) {
            closeAmenityModal();
        }
    });

    // Close on escape
    document.addEventListener('keydown', function(e) {
        if(e.key === 'Escape' && modal.classList.contains('active')) {
            closeAmenityModal();
        }
    });
</script>

