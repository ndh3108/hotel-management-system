<style>
    .footer {
        background: #0f172a;
        color: #f8fafc;
        padding: 80px 0 30px;
        margin-top: 100px;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
        gap: 60px;
        margin-bottom: 60px;
    }

    .footer-brand .logo {
        color: white;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.4rem;
    }

    .footer-brand .logo-icon {
        background: #2563eb;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .footer-desc {
        color: #94a3b8;
        line-height: 1.7;
        margin-bottom: 24px;
        font-size: 0.95rem;
    }

    .social-links {
        display: flex;
        gap: 12px;
    }

    .social-link {
        width: 40px;
        height: 40px;
        background: #1e293b;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .social-link:hover {
        background: #2563eb;
        color: white;
        transform: translateY(-3px);
    }

    .footer-title {
        color: white;
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 24px;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: #94a3b8;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.95rem;
    }

    .footer-links a:hover {
        color: white;
        padding-left: 5px;
    }

    .footer-contact li {
        display: flex;
        gap: 12px;
        color: #94a3b8;
        margin-bottom: 16px;
        font-size: 0.95rem;
    }

    .footer-contact i {
        color: #2563eb;
        margin-top: 4px;
    }

    .footer-bottom {
        border-top: 1px solid #1e293b;
        padding-top: 30px;
        text-align: center;
        color: #64748b;
        font-size: 0.9rem;
    }

    @media (max-width: 991px) {
        .footer-grid {
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
    }

    @media (max-width: 576px) {
        .footer-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?= \App\Core\url('/') ?>" class="logo">
                    <div class="logo-icon"><i class="fa-solid fa-hotel"></i></div>
                    <span>Booking Hotel</span>
                </a>
                <p class="footer-desc">
                    Kiến tạo trải nghiệm nghỉ dưỡng khác biệt. Chúng tôi cam kết mang đến không gian sang trọng và dịch vụ tận tâm nhất cho mọi hành trình của bạn.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="social-link" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="social-link" title="Twitter"><i class="fa-brands fa-twitter-x"></i></a>
                    <a href="#" class="social-link" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Khám Phá</h4>
                <ul class="footer-links">
                    <li><a href="<?= \App\Core\url('/') ?>">Trang chủ</a></li>
                    <li><a href="<?= \App\Core\url('/rooms') ?>">Hạng phòng nghỉ</a></li>
                    <li><a href="<?= \App\Core\url('/services') ?>">Dịch vụ cao cấp</a></li>
                    <li><a href="<?= \App\Core\url('/rooms/search') ?>">Tìm phòng trống</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Hỗ Trợ</h4>
                <ul class="footer-links">
                    <li><a href="#">Trung tâm trợ giúp</a></li>
                    <li><a href="#">Chính sách đặt phòng</a></li>
                    <li><a href="#">Điều khoản sử dụng</a></li>
                    <li><a href="#">Tuyển dụng</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Thông Tin Liên Hệ</h4>
                <ul class="footer-links footer-contact">
                    <li>
                        <i class="fa-solid fa-location-dot"></i>
                        <span>123 Đường Nam Kỳ Khởi Nghĩa, Quận 1, TP. Hồ Chí Minh</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-phone"></i>
                        <span>+84 28 1234 5678</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-envelope"></i>
                        <span>contact@luxuryhotel.com</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-clock"></i>
                        <span>Phục vụ 24/7 (Cả ngày lễ)</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Booking Hotel Luxury Collection. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</footer>
