<?php
// Lấy dữ liệu từ controller
$user = $user ?? [];
$bookingCount = $bookingCount ?? 0;
$reviewCount = $reviewCount ?? 0;
$points = $points ?? 0;
$baseUrl = rtrim(APP_URL ?? '', '/');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang cá nhân - Booking Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1566665797739-1674de7a421a?ixlib=rb-4.0.3') center/cover no-repeat;
            opacity: 0.05;
            pointer-events: none;
            z-index: 0;
        }

        /* Main Container */
        .main {
            flex: 1;
            max-width: 1100px;
            margin: 90px auto;
            position: relative;
            z-index: 1;
            padding: 0 40px;
        }

        /* Profile Container */
        .profile-container {
            display: flex;
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left Panel */
        .profile-left {
            width: 35%;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .profile-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Avatar */
        .avatar-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            margin: 0 auto;
            background-size: cover;
            background-position: center;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }

        .avatar-wrapper:hover .avatar {
            transform: scale(1.05);
            border-color: #f59e0b;
        }

        .avatar-upload {
            margin-top: 16px;
            position: relative;
        }

        .avatar-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .avatar-upload .upload-btn {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 40px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .avatar-upload .upload-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .avatar-upload .upload-btn i {
            font-size: 14px;
        }

        .profile-left h2 {
            font-size: 24px;
            font-weight: 700;
            margin: 16px 0 8px;
        }

        .profile-left .email {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-bottom: 24px;
        }

        /* Stats */
        .profile-stats {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            padding: 20px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            display: block;
            font-size: 28px;
            font-weight: 800;
            color: #f59e0b;
        }

        .stat-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 4px;
        }

        /* Navigation Buttons */
        .profile-nav {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 30px;
        }

        .nav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }

        .nav-btn i {
            font-size: 16px;
        }

        .nav-btn.logout {
            background: rgba(239, 68, 68, 0.2);
            color: #fecaca;
        }

        .nav-btn.logout:hover {
            background: rgba(239, 68, 68, 0.3);
            color: white;
        }

        /* Right Panel */
        .profile-right {
            width: 65%;
            padding: 40px;
            background: white;
        }

        .profile-right h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-right h2 i {
            color: #3b82f6;
        }

        .profile-right .subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }

        /* Alert */
        .success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            padding: 16px 20px;
            border-radius: 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid #10b981;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .success i {
            font-size: 20px;
        }

        /* Form */
        .profile-form {
            margin-top: 24px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            font-size: 14px;
        }

        label i {
            color: #3b82f6;
            width: 20px;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        input:hover {
            border-color: #cbd5e1;
        }

        input:disabled {
            background: #f8fafc;
            color: #94a3b8;
            cursor: not-allowed;
        }

        /* Save Button */
        .save-btn {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 16px;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }

        .save-btn:active {
            transform: translateY(0);
        }

        .tab-nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .tab-link {
            padding: 10px 16px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            background: #f1f5f9;
            transition: all 0.2s ease;
        }

        .tab-link:hover {
            background: #e2e8f0;
        }

        .tab-link.is-active {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #fff;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.is-active {
            display: block;
        }

        .info-card {
            padding: 14px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            margin-bottom: 12px;
            background: #fff;
        }

        .info-card-title {
            font-weight: 600;
            color: #1e293b;
        }

        .info-card-meta {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .chip.success {
            background: #dcfce7;
            color: #166534;
        }

        .chip.muted {
            background: #e2e8f0;
            color: #475569;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
            }

            .profile-left,
            .profile-right {
                width: 100%;
            }

            .profile-left {
                padding: 30px 20px;
            }

            .profile-right {
                padding: 30px 20px;
            }

            .profile-stats {
                margin: 20px 0;
            }

            .stat-number {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>

    <div class="main">
        <div class="profile-container">

            <!-- LEFT PANEL -->
            <div class="profile-left">
                <div class="avatar-wrapper">
                    <div class="avatar" style="background-image: url('<?= $user['avatar_url'] ?? ($user['avatar'] ?? 'https://ui-avatars.com/api/?background=3b82f6&color=fff&size=140&name=' . urlencode($user['full_name'] ?? 'User')) ?>')"></div>
                </div>

                <h2><?= htmlspecialchars($user['full_name'] ?? 'Chưa cập nhật') ?></h2>
                <div class="email"><?= htmlspecialchars($user['email'] ?? '') ?></div>

                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?= $bookingCount ?? 0 ?></span>
                        <span class="stat-label">Đặt phòng</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?= $reviewCount ?? 0 ?></span>
                        <span class="stat-label">Đánh giá</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?= $points ?? 0 ?></span>
                        <span class="stat-label">Điểm thưởng</span>
                    </div>
                </div>

                <div class="avatar-upload">
                    <form method="POST" action="<?= $baseUrl ?>/profile/avatar" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <label class="upload-btn">
                            <i class="fa-solid fa-camera"></i>
                            <span>Đổi ảnh đại diện</span>
                            <input type="file" name="avatar" accept="image/*" onchange="this.form.submit()">
                        </label>
                    </form>
                </div>

                <div class="profile-nav">
                    <a href="<?= $baseUrl ?>/bookings" class="nav-btn">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Quản lý đặt phòng</span>
                    </a>
                    <a href="<?= $baseUrl ?>/" class="nav-btn">
                        <i class="fa-solid fa-home"></i>
                        <span>Trang chủ</span>
                    </a>
                    <a href="<?= $baseUrl ?>/logout" class="nav-btn logout">
                        <i class="fa-solid fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                </div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="profile-right">
                <?php $currentTab = in_array(($activeTab ?? 'profile'), ['profile', 'security', 'sessions', 'activity'], true) ? $activeTab : 'profile'; ?>
                <h2>
                    <i class="fa-solid fa-user-edit"></i>
                    Trung tâm tài khoản
                </h2>
                <div class="subtitle">Quản lý hồ sơ, bảo mật, phiên đăng nhập và lịch sử hoạt động của bạn</div>

                <?php if (!empty($flashMessage)): ?>
                    <div class="success">
                        <i class="fa-solid fa-circle-check"></i>
                        <span><?= htmlspecialchars((string) $flashMessage) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($flashError)): ?>
                    <div class="success" style="background: #fee2e2; color: #991b1b; border-left-color: #dc2626;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span><?= htmlspecialchars((string) $flashError) ?></span>
                    </div>
                <?php endif; ?>

                <div class="tab-nav">
                    <a href="<?= $baseUrl ?>/profile?tab=profile" class="tab-link <?= $currentTab === 'profile' ? 'is-active' : '' ?>">Hồ sơ</a>
                    <a href="<?= $baseUrl ?>/profile?tab=security" class="tab-link <?= $currentTab === 'security' ? 'is-active' : '' ?>">Bảo mật</a>
                    <a href="<?= $baseUrl ?>/profile?tab=sessions" class="tab-link <?= $currentTab === 'sessions' ? 'is-active' : '' ?>">Phiên</a>
                    <a href="<?= $baseUrl ?>/profile?tab=activity" class="tab-link <?= $currentTab === 'activity' ? 'is-active' : '' ?>">Hoạt động</a>
                </div>

                <section class="tab-panel <?= $currentTab === 'profile' ? 'is-active' : '' ?>">
                    <form method="POST" action="<?= $baseUrl ?>/profile/update" class="profile-form">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label><i class="fa-solid fa-user"></i> Họ và tên</label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="Nhập họ tên đầy đủ" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fa-solid fa-envelope"></i> Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="example@email.com" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fa-solid fa-phone"></i> Số điện thoại</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="0xx xxx xxxx">
                        </div>
                        <div class="form-group">
                            <label><i class="fa-solid fa-location-dot"></i> Địa chỉ mặc định</label>
                            <input type="text" name="address_line" value="<?= htmlspecialchars($user['address_line'] ?? '') ?>" placeholder="Số nhà, tên đường">
                        </div>
                        <div class="form-group">
                            <label><i class="fa-solid fa-building"></i> Quận / Huyện</label>
                            <input type="text" name="district" value="<?= htmlspecialchars($user['district'] ?? '') ?>" placeholder="Quận / Huyện">
                        </div>
                        <div class="form-group">
                            <label><i class="fa-solid fa-city"></i> Thành phố</label>
                            <input type="text" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>" placeholder="Thành phố">
                        </div>
                        <div class="form-group">
                            <label><i class="fa-solid fa-signs-post"></i> Mã bưu chính</label>
                            <input type="text" name="postal_code" value="<?= htmlspecialchars($user['postal_code'] ?? '') ?>" placeholder="Postal code">
                        </div>
                        <div class="form-group">
                            <label><i class="fa-solid fa-calendar"></i> Ngày tham gia</label>
                            <input type="text" value="<?= date('d/m/Y', strtotime($user['created_at'] ?? 'now')) ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label><i class="fa-solid fa-chart-simple"></i> Hoàn thiện hồ sơ</label>
                            <input type="text" value="<?= (int) ($user['profile_completion_percent'] ?? 0) ?>%" disabled>
                        </div>
                        <button type="submit" class="save-btn">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Lưu thay đổi</span>
                        </button>
                    </form>
                </section>


                <section class="tab-panel <?= $currentTab === 'security' ? 'is-active' : '' ?>">
                    <div class="profile-form">
                        <div class="form-group">
                            <label><i class="fa-solid fa-envelope-circle-check"></i> Trạng thái email</label>
                            <input type="text" value="<?= !empty($user['email_verified_at']) ? 'Đã xác minh lúc ' . date('d/m/Y H:i', strtotime($user['email_verified_at'])) : 'Chưa xác minh' ?>" disabled>
                        </div>
                        <form method="POST" action="<?= $baseUrl ?>/profile/verify-email" style="margin-bottom:16px;">
                            <?= csrf_field() ?>
                            <button type="submit" class="save-btn" style="margin-top:0;">
                                <i class="fa-solid fa-badge-check"></i>
                                <span>Xác minh email</span>
                            </button>
                        </form>

                        <div class="form-group">
                            <label><i class="fa-solid fa-phone-volume"></i> Trạng thái số điện thoại</label>
                            <input type="text" value="<?= !empty($user['phone_verified_at']) ? 'Đã xác minh lúc ' . date('d/m/Y H:i', strtotime($user['phone_verified_at'])) : 'Chưa xác minh' ?>" disabled>
                        </div>
                        <form method="POST" action="<?= $baseUrl ?>/profile/verify-phone" style="margin-bottom:16px;">
                            <?= csrf_field() ?>
                            <button type="submit" class="save-btn" style="margin-top:0;">
                                <i class="fa-solid fa-shield-phone"></i>
                                <span>Xác minh số điện thoại</span>
                            </button>
                        </form>

                        <form method="POST" action="<?= $baseUrl ?>/profile/password" class="profile-form" style="margin-top: 20px;">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <label><i class="fa-solid fa-lock"></i> Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fa-solid fa-key"></i> Mật khẩu mới</label>
                                <input type="password" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fa-solid fa-key"></i> Xác nhận mật khẩu mới</label>
                                <input type="password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="save-btn">
                                <i class="fa-solid fa-rotate"></i>
                                <span>Đổi mật khẩu</span>
                            </button>
                        </form>
                    </div>
                </section>

                <section class="tab-panel <?= $currentTab === 'sessions' ? 'is-active' : '' ?>">
                    <div class="profile-form">
                        <form method="POST" action="<?= $baseUrl ?>/profile/logout-other-sessions" style="margin-bottom:20px;">
                            <?= csrf_field() ?>
                            <button type="submit" class="save-btn" style="margin-top:0;">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>Đăng xuất các phiên khác</span>
                            </button>
                        </form>

                        <?php if (!empty($sessions)): ?>
                            <?php foreach ($sessions as $session): ?>
                                <?php
                                    $isCurrentSession = !empty($session['is_current']) && empty($session['logged_out_at']);
                                    $isLoggedOut = !empty($session['logged_out_at']);
                                ?>
                                <div class="info-card">
                                    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                                        <div>
                                            <div class="info-card-title"><?= htmlspecialchars((string) ($session['ip_address'] ?? 'Unknown IP')) ?></div>
                                            <div class="info-card-meta">Hoạt động cuối: <?= !empty($session['last_activity_at']) ? date('d/m/Y H:i', strtotime($session['last_activity_at'])) : '-' ?></div>
                                            <div class="info-card-meta"><?= htmlspecialchars((string) ($session['user_agent'] ?? '')) ?></div>
                                        </div>
                                        <div>
                                            <?php if ($isCurrentSession): ?>
                                                <span class="chip success">Phiên hiện tại</span>
                                            <?php elseif ($isLoggedOut): ?>
                                                <span class="chip muted">Đã đăng xuất</span>
                                            <?php else: ?>
                                                <span class="chip muted">Đang hoạt động</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="color:#64748b;">Chưa có dữ liệu phiên đăng nhập.</div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="tab-panel <?= $currentTab === 'activity' ? 'is-active' : '' ?>">
                    <div class="profile-form">
                        <?php if (!empty($activities)): ?>
                            <?php foreach ($activities as $activity): ?>
                                <div class="info-card">
                                    <div class="info-card-title"><?= htmlspecialchars((string) ($activity['description'] ?? $activity['action'] ?? 'Activity')) ?></div>
                                    <div class="info-card-meta"><?= !empty($activity['created_at']) ? date('d/m/Y H:i', strtotime($activity['created_at'])) : '-' ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="color:#64748b;">Chưa có lịch sử hoạt động.</div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

        </div>
    </div>

    <script>
        // Auto hide success message after 3 seconds
        setTimeout(function() {
            const successMsg = document.querySelector('.success');
            if (successMsg) {
                successMsg.style.opacity = '0';
                setTimeout(() => {
                    successMsg.style.display = 'none';
                }, 300);
            }
        }, 3000);

        // Preview avatar before upload
        document.querySelector('input[name="avatar"]')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatar = document.querySelector('.avatar');
                    if (avatar) {
                        avatar.style.backgroundImage = `url('${e.target.result}')`;
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    </script>

</body>

</html>
