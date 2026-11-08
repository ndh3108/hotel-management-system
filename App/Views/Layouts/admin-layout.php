<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Panel') ?> - Booking Hotel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= \App\Core\asset('css/admin.css') ?>">
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
            --admin-primary: #4f46e5;
            --admin-primary-light: #6366f1;
            --admin-bg: #f8fafc;
            --sidebar-bg: #0f172a;
            --sidebar-hover: rgba(255, 255, 255, 0.05);
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --transition-speed: 0.3s;
        }

        /* Reset and Base */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--admin-bg); overflow-x: hidden; }

        /* Robust Grid Layout */
        .admin-wrapper {
            display: grid;
            grid-template-columns: var(--sidebar-width) 1fr;
            min-height: 100vh;
            width: 100%;
            transition: grid-template-columns var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-wrapper.sidebar-collapsed {
            grid-template-columns: var(--sidebar-collapsed-width) 1fr;
        }

        /* Sidebar Styling */
        .admin-sidebar {
            width: 100%;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            height: 100vh;
            position: sticky;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            white-space: nowrap;
        }

        .admin-sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
            font-weight: 800;
            font-size: 1.25rem;
        }

        .admin-sidebar-logo i { color: var(--admin-primary); font-size: 1.5rem; }

        /* Navigation Groups */
        .admin-nav {
            flex: 1;
            overflow-y: auto;
            padding: 20px 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
        }

        .admin-nav::-webkit-scrollbar { width: 4px; }
        .admin-nav::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }

        .menu-group { margin-bottom: 8px; padding: 0 12px; }

        .menu-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            color: var(--sidebar-text);
            cursor: pointer;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            user-select: none;
        }

        .menu-title:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-text-active);
        }

        .menu-title-content { display: flex; align-items: center; gap: 12px; }
        .menu-title i.arrow { font-size: 0.75rem; transition: transform 0.3s; color: #475569; }

        .menu-group.open .menu-title { color: var(--sidebar-text-active); }
        .menu-group.open .arrow { transform: rotate(90deg); color: var(--sidebar-text-active); }

        /* Submenu */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease-out;
            padding-left: 12px;
        }

        .menu-group.open .submenu { max-height: 500px; transition: max-height 0.5s ease-in; }

        .submenu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            margin: 4px 0;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .submenu-item:hover { color: var(--sidebar-text-active); padding-left: 20px; }
        .submenu-item.active {
            background: linear-gradient(90deg, var(--admin-primary), var(--admin-primary-light));
            color: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        /* Collapsed Sidebar Adjustments */
        .sidebar-collapsed .menu-title span,
        .sidebar-collapsed .menu-title .arrow,
        .sidebar-collapsed .submenu,
        .sidebar-collapsed .admin-sidebar-logo span {
            display: none;
        }

        .sidebar-collapsed .menu-group { padding: 0 16px; }
        .sidebar-collapsed .menu-title { justify-content: center; padding: 12px 0; }
        .sidebar-collapsed .menu-title-content { gap: 0; }
        .sidebar-collapsed .menu-title i:not(.arrow) { font-size: 1.25rem; }

        /* Main Content Area */
        .admin-main {
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: var(--admin-bg);
            margin: 0 !important;
        }

        .admin-header {
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .header-left { display: flex; align-items: center; gap: 20px; }

        .sidebar-toggle {
            width: 40px; height: 40px; background: #f1f5f9; border: none; border-radius: 10px;
            cursor: pointer; color: #64748b; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }

        .sidebar-toggle:hover { background: #e2e8f0; color: var(--admin-primary); }
        .admin-header-title { font-size: 1.25rem; font-weight: 700; color: #1e293b; }
        .admin-content { padding: 32px; flex: 1; }

        /* Mobile */
        @media (max-width: 1024px) {
            .admin-wrapper { grid-template-columns: 1fr; }
            .admin-sidebar { position: fixed; left: -280px; width: 280px; z-index: 1100; }
            .mobile-sidebar-active .admin-sidebar { left: 0; }
            .sidebar-overlay {
                display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5);
                z-index: 1050; backdrop-filter: blur(2px);
            }
            .mobile-sidebar-active .sidebar-overlay { display: block; }
        }

        .admin-card { width: 100%; margin-bottom: 24px; }
    </style>
</head>

<body>
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <div id="admin-wrapper" class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="admin-sidebar">
            <div class="admin-sidebar-header">
                <a href="<?= \App\Core\url('/admin') ?>" class="admin-sidebar-logo">
                    <img src="<?= \App\Core\url('assets/image/logo.png') ?>" alt="Logo" class="h-8 w-auto">
                    <span>Booking Hotel</span>
                </a>
            </div>

            <nav class="admin-nav">
                <!-- 1. Tổng quan -->
                <div class="menu-group" data-module="dashboard">
                    <div class="menu-title">
                        <div class="menu-title-content">
                            <i class="fa-solid fa-chart-pie"></i>
                            <span>Tổng quan</span>
                        </div>
                        <i class="fa-solid fa-chevron-right arrow"></i>
                    </div>
                    <div class="submenu">
                        <a href="<?= \App\Core\url('/admin') ?>" class="submenu-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span>Bảng điều khiển</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/audit-logs') ?>" class="submenu-item <?= ($currentPage ?? '') === 'audit_logs' ? 'active' : '' ?>">
                            <i class="fa-solid fa-clipboard-list"></i>
                            <span>Nhật ký hoạt động</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/system-logs') ?>" class="submenu-item <?= ($currentPage ?? '') === 'system_logs' ? 'active' : '' ?>">
                            <i class="fa-solid fa-server"></i>
                            <span>Nhật ký hệ thống</span>
                        </a>
                    </div>
                </div>

                <!-- 2. Vận hành -->
                <div class="menu-group" data-module="operations">
                    <div class="menu-title">
                        <div class="menu-title-content">
                            <i class="fa-solid fa-hotel"></i>
                            <span>Vận hành</span>
                        </div>
                        <i class="fa-solid fa-chevron-right arrow"></i>
                    </div>
                    <div class="submenu">
                        <a href="<?= \App\Core\url('/admin/bookings') ?>" class="submenu-item <?= ($currentPage ?? '') === 'bookings' ? 'active' : '' ?>">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Đặt phòng</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/room/grid') ?>" class="submenu-item <?= ($currentPage ?? '') === 'room_grid' ? 'active' : '' ?>">
                            <i class="fa-solid fa-th"></i>
                            <span>Sơ đồ phòng</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/room') ?>" class="submenu-item <?= ($currentPage ?? '') === 'room' ? 'active' : '' ?>">
                            <i class="fa-solid fa-door-open"></i>
                            <span>Quản lý phòng</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/housekeeping') ?>" class="submenu-item <?= ($currentPage ?? '') === 'housekeeping' ? 'active' : '' ?>">
                            <i class="fa-solid fa-broom"></i>
                            <span>Buồng phòng</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/service-bookings') ?>" class="submenu-item <?= ($currentPage ?? '') === 'service_bookings' ? 'active' : '' ?>">
                            <i class="fa-solid fa-bell-concierge"></i>
                            <span>Đặt dịch vụ</span>
                        </a>
                    </div>
                </div>

                <!-- 3. Cơ sở vật chất -->
                <div class="menu-group" data-module="facilities">
                    <div class="menu-title">
                        <div class="menu-title-content">
                            <i class="fa-solid fa-building"></i>
                            <span>Cơ sở vật chất</span>
                        </div>
                        <i class="fa-solid fa-chevron-right arrow"></i>
                    </div>
                    <div class="submenu">
                        <a href="<?= \App\Core\url('/admin/room-type') ?>" class="submenu-item <?= ($currentPage ?? '') === 'room-type' ? 'active' : '' ?>">
                            <i class="fa-solid fa-layer-group"></i>
                            <span>Loại phòng</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/amenities') ?>" class="submenu-item <?= ($currentPage ?? '') === 'amenities' ? 'active' : '' ?>">
                            <i class="fa-solid fa-couch"></i>
                            <span>Tiện nghi</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/inventory') ?>" class="submenu-item <?= ($currentPage ?? '') === 'inventory' ? 'active' : '' ?>">
                            <i class="fa-solid fa-boxes-packing"></i>
                            <span>Định mức phòng</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/services') ?>" class="submenu-item <?= ($currentPage ?? '') === 'services' ? 'active' : '' ?>">
                            <i class="fa-solid fa-bell-concierge"></i>
                            <span>Quản lý dịch vụ</span>
                        </a>
                    </div>
                </div>

                <!-- 4. Vật tư & Kho -->
                <div class="menu-group" data-module="inventory">
                    <div class="menu-title">
                        <div class="menu-title-content">
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <span>Vật tư & Kho</span>
                        </div>
                        <i class="fa-solid fa-chevron-right arrow"></i>
                    </div>
                    <div class="submenu">
                        <a href="<?= \App\Core\url('/admin/inventory/catalog') ?>" class="submenu-item <?= ($currentPage ?? '') === 'inventory_catalog' ? 'active' : '' ?>">
                            <i class="fa-solid fa-list-check"></i>
                            <span>Danh mục vật tư</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/inventory/damage') ?>" class="submenu-item <?= ($currentPage ?? '') === 'inventory_damage' ? 'active' : '' ?>">
                            <i class="fa-solid fa-dumpster-fire"></i>
                            <span>Quản lý hỏng hóc</span>
                        </a>
                    </div>
                </div>

                <!-- 5. Khách hàng -->
                <div class="menu-group" data-module="customers">
                    <div class="menu-title">
                        <div class="menu-title-content">
                            <i class="fa-solid fa-users"></i>
                            <span>Khách hàng</span>
                        </div>
                        <i class="fa-solid fa-chevron-right arrow"></i>
                    </div>
                    <div class="submenu">
                        <a href="<?= \App\Core\url('/admin/users') ?>" class="submenu-item <?= ($currentPage ?? '') === 'users' ? 'active' : '' ?>">
                            <i class="fa-solid fa-user-group"></i>
                            <span>Danh sách khách</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/reviews') ?>" class="submenu-item <?= ($currentPage ?? '') === 'reviews' ? 'active' : '' ?>">
                            <i class="fa-solid fa-star"></i>
                            <span>Đánh giá</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/loyalty') ?>" class="submenu-item <?= ($currentPage ?? '') === 'loyalty' ? 'active' : '' ?>">
                            <i class="fa-solid fa-crown"></i>
                            <span>Khách hàng thân thiết</span>
                        </a>
                    </div>
                </div>

                <!-- 6. Tài chính -->
                <div class="menu-group" data-module="finance">
                    <div class="menu-title">
                        <div class="menu-title-content">
                            <i class="fa-solid fa-wallet"></i>
                            <span>Tài chính</span>
                        </div>
                        <i class="fa-solid fa-chevron-right arrow"></i>
                    </div>
                    <div class="submenu">
                        <a href="<?= \App\Core\url('/admin/revenue') ?>" class="submenu-item <?= ($currentPage ?? '') === 'revenue' ? 'active' : '' ?>">
                            <i class="fa-solid fa-chart-line"></i>
                            <span>Doanh thu</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/payments/transactions') ?>" class="submenu-item <?= ($currentPage ?? '') === 'transactions' ? 'active' : '' ?>">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <span>Giao dịch</span>
                        </a>
                    </div>
                </div>

                <!-- 7. Hệ thống -->
                <div class="menu-group" data-module="system">
                    <div class="menu-title">
                        <div class="menu-title-content">
                            <i class="fa-solid fa-gears"></i>
                            <span>Hệ thống</span>
                        </div>
                        <i class="fa-solid fa-chevron-right arrow"></i>
                    </div>
                    <div class="submenu">
                        <a href="<?= \App\Core\url('/admin/users') ?>" class="submenu-item <?= ($currentPage ?? '') === 'users' ? 'active' : '' ?>">
                            <i class="fa-solid fa-users-cog"></i>
                            <span>Người dùng hệ thống</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/rbac') ?>" class="submenu-item <?= ($currentPage ?? '') === 'rbac' ? 'active' : '' ?>">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Phân quyền (RBAC)</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/roles') ?>" class="submenu-item <?= ($currentPage ?? '') === 'roles' ? 'active' : '' ?>">
                            <i class="fa-solid fa-user-shield"></i>
                            <span>Vai trò</span>
                        </a>
                        <a href="<?= \App\Core\url('/admin/permissions') ?>" class="submenu-item <?= ($currentPage ?? '') === 'permissions' ? 'active' : '' ?>">
                            <i class="fa-solid fa-key"></i>
                            <span>Quyền hạn</span>
                        </a>
                    </div>
                </div>

                <div class="mt-auto pt-4 border-t border-white/5 px-3">
                    <a href="<?= \App\Core\url('/admin/articles') ?>" class="submenu-item <?= ($currentPage ?? '') === 'articles' ? 'active' : '' ?> text-slate-400">
                        <i class="fa-solid fa-newspaper"></i>
                        <span>Bài viết</span>
                    </a>
                    <a href="<?= \App\Core\url('/') ?>" target="_blank" class="submenu-item text-slate-400">
                        <i class="fa-solid fa-globe"></i>
                        <span>Xem Website</span>
                    </a>
                    <a href="<?= \App\Core\url('/logout') ?>" class="submenu-item text-red-400 hover:text-red-300">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Đăng xuất</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="btn-toggle" onclick="toggleSidebar()">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </button>
                    <h1 class="admin-header-title">
                        <?= htmlspecialchars($pageTitle ?? 'Bảng điều khiển') ?>
                    </h1>
                </div>

                <div class="header-right">
                    <div class="flex items-center gap-4">
                        <div class="hidden md:flex flex-col items-end">
                            <span class="text-sm font-bold text-slate-700"><?= htmlspecialchars(\App\Core\Auth::user()['full_name'] ?? 'Admin') ?></span>
                            <span class="text-xs text-slate-400">Quản trị viên</span>
                        </div>
                        <img class="w-10 h-10 rounded-xl border-2 border-slate-100" 
                             src="https://ui-avatars.com/api/?name=<?= urlencode(\App\Core\Auth::user()['full_name'] ?? 'Admin') ?>&background=4f46e5&color=fff" 
                             alt="Avatar">
                    </div>
                </div>
            </header>

            <div class="admin-content">
                <?= $content ?>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const wrapper = document.getElementById('admin-wrapper');
            const isMobile = window.innerWidth <= 1024;
            
            if (isMobile) {
                document.body.classList.toggle('mobile-sidebar-active');
            } else {
                wrapper.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', wrapper.classList.contains('sidebar-collapsed'));
            }
        }

        // Accordion Logic
        document.querySelectorAll('.menu-title').forEach(title => {
            title.addEventListener('click', () => {
                const group = title.parentElement;
                const isOpen = group.classList.contains('open');
                
                // Close all other groups (Accordion style)
                document.querySelectorAll('.menu-group').forEach(g => {
                    if (g !== group) g.classList.remove('open');
                });
                
                // Toggle current group
                group.classList.toggle('open');
            });
        });

        // Auto-open active menu on load
        document.addEventListener('DOMContentLoaded', () => {
            const activeItem = document.querySelector('.submenu-item.active');
            if (activeItem) {
                const group = activeItem.closest('.menu-group');
                if (group) {
                    group.classList.add('open');
                }
            }

            // Restore collapsed state
            if (localStorage.getItem('sidebarCollapsed') === 'true' && window.innerWidth > 1024) {
                document.getElementById('admin-wrapper').classList.add('sidebar-collapsed');
            }
        });
    </script>
</body>
</html>

