<?php
/**
 * Header Layout - Modern Design
 */
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$user = \App\Core\Auth::user();
$canAccessAdmin = $user ? \App\Core\Auth::canAccessAdmin() : false;
?>
<style>
    :root {
        --header-height: 80px;
        --nav-glass: rgba(255, 255, 255, 0.85);
    }

    .header {
        position: sticky;
        top: 0;
        z-index: 1000;
        height: var(--header-height);
        background: var(--nav-glass);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        transition: all 0.3s ease;
    }

    .header.scrolled {
        height: 70px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .header-inner {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--primary);
        font-weight: 800;
        font-size: 1.4rem;
        letter-spacing: -0.5px;
    }

    .logo-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }

    .user-nav {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .user-profile {
        position: relative;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 16px;
        border-radius: 14px;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid var(--gray-200);
    }

    .user-profile:hover {
        border-color: var(--primary);
        background: #f8fafc;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        background: var(--primary);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .user-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--dark);
    }

    .dropdown-container {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: 240px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        padding: 8px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 1000;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .dropdown-container:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        color: var(--text);
        font-size: 0.95rem;
        font-weight: 500;
        border-radius: 10px;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .dropdown-item:hover {
        background: #f1f5f9;
        color: var(--primary);
    }

    .dropdown-item i {
        font-size: 1.1rem;
        color: #64748b;
    }

    .dropdown-item.logout {
        color: #ef4444;
    }

    .dropdown-item.logout:hover {
        background: #fef2f2;
    }

    .dropdown-divider {
        height: 1px;
        background: #f1f5f9;
        margin: 6px 8px;
    }

    .nav-links {
        display: flex;
        gap: 8px;
    }

    .nav-link {
        padding: 8px 16px;
        color: #475569;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        color: var(--primary);
        background: rgba(37, 99, 235, 0.05);
    }

    .nav-toggle {
        display: none;
        width: 40px;
        height: 40px;
        border: none;
        background: #f1f5f9;
        border-radius: 10px;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .nav-links {
            display: none;
        }
        .nav-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }
</style>

<script>
    window.addEventListener('scroll', () => {
        const header = document.getElementById('header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
</script>

<header class="header" id="header">
    <div class="container" style="height: 100%;">
        <div class="header-inner">
            <a href="<?= \App\Core\url('/') ?>" class="logo" style="display: flex; align-items: center; height: 100%;">
                <img src="<?= \App\Core\url('assets/image/logo.png') ?>" alt="Booking Hotel Logo" style="height: 42px; max-width: 160px; object-fit: contain;">
            </a>

            <nav class="nav" data-main-nav style="display: flex; align-items: center; gap: 20px; height: 100%;">
                <button type="button" class="nav-toggle" data-nav-toggle aria-label="Mở menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="nav-links">
                    <a href="<?= \App\Core\url('') ?>" class="nav-link">Trang chủ</a>
                    <a href="<?= \App\Core\url('rooms') ?>" class="nav-link">Phòng nghỉ</a>
                    <a href="<?= \App\Core\url('services') ?>" class="nav-link">Dịch vụ</a>
                    <a href="<?= \App\Core\url('rooms/about') ?>" class="nav-link">Giới thiệu</a>
                </div>

                <div class="user-nav">
                    <?php if ($user): ?>
                        <div class="dropdown-container">
                            <div class="user-profile" style="padding: 4px 12px; height: 44px;">
                                <div class="user-avatar" title="<?= htmlspecialchars($user['full_name'] ?? 'Người dùng') ?>" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <div class="user-info">
                                    <span class="user-name" style="font-size: 0.85rem;"><?= htmlspecialchars($user['full_name'] ?? 'Tài khoản') ?></span>
                                </div>
                                <i class="fa-solid fa-chevron-down" style="font-size: 0.6rem; color: var(--text-light);"></i>
                            </div>
                            <div class="dropdown-menu">
                                <a href="<?= \App\Core\url('profile') ?>" class="dropdown-item">
                                    <i class="fa-solid fa-user-circle"></i> Thông tin cá nhân
                                </a>
                                <a href="<?= \App\Core\url('bookings') ?>" class="dropdown-item">
                                    <i class="fa-solid fa-calendar-check"></i> Đơn đặt phòng
                                </a>
                                <a href="<?= \App\Core\url('service-bookings') ?>" class="dropdown-item">
                                    <i class="fa-solid fa-bell-concierge"></i> Đơn dịch vụ
                                </a>
                                <?php if ($canAccessAdmin): ?>
                                    <div class="dropdown-divider"></div>
                                    <a href="<?= \App\Core\url('admin') ?>" class="dropdown-item">
                                        <i class="fa-solid fa-shield-halved"></i> Trang quản trị
                                    </a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a href="<?= \App\Core\url('logout') ?>" class="dropdown-item logout">
                                    <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= \App\Core\url('login') ?>" class="btn btn-outline" style="padding: 8px 18px; font-size: 0.9rem;">
                            <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>
</header>

