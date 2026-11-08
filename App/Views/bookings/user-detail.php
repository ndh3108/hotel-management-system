<?php

/**
 * User Booking Detail View
 * Users can see their booking status updates
 */
?>

<style>
    .user-booking-container {
        max-width: 1000px;
        margin: 30px auto;
        padding: 20px;
    }

    .booking-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .booking-header h1 {
        margin: 0 0 10px 0;
        font-size: 28px;
    }

    .booking-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-card {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .info-card h3 {
        margin: 0 0 10px 0;
        color: #333;
        font-size: 14px;
        text-transform: uppercase;
        font-weight: 600;
        color: #666;
    }

    .info-card p {
        margin: 0;
        font-size: 18px;
        color: #333;
        font-weight: 500;
    }

    .status-section {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .status-section h2 {
        margin-top: 0;
        color: #333;
        border-bottom: 2px solid #667eea;
        padding-bottom: 15px;
    }

    .status-timeline {
        position: relative;
        padding: 20px 0;
    }

    .status-current {
        background: #f0f4ff;
        border-left: 4px solid #667eea;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }

    .badge-info {
        background: #cfe2ff;
        color: #084298;
    }

    .badge-success {
        background: #d1e7dd;
        color: #0f5132;
    }

    .badge-danger {
        background: #f8d7da;
        color: #842029;
    }

    .badge-secondary {
        background: #e2e3e5;
        color: #383d41;
    }

    .notification-item {
        border-left: 3px solid #667eea;
        padding: 15px;
        margin-bottom: 15px;
        background: #f9f9f9;
        border-radius: 5px;
    }

    .notification-item .time {
        color: #666;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .notification-item .message {
        color: #333;
        font-size: 14px;
    }

    .notification-item strong {
        color: #667eea;
    }

    .no-notifications {
        color: #999;
        text-align: center;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 5px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5568d3;
    }

    .btn-secondary {
        background: #ddd;
        color: #333;
    }

    .btn-secondary:hover {
        background: #ccc;
    }
</style>

<div class="user-booking-container">
    <!-- Booking Header -->
    <div class="booking-header">
        <h1>🏨 Chi Tiết Đặt Phòng #<?= htmlspecialchars($booking['id']) ?></h1>
        <p style="margin: 0; opacity: 0.9;">Đặt ngày: <?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></p>
    </div>

    <!-- Booking Information -->
    <div class="booking-info-grid">
        <div class="info-card">
            <h3>📅 Check-in</h3>
            <p><?= date('d/m/Y', strtotime($booking['check_in'])) ?></p>
        </div>
        <div class="info-card">
            <h3>📅 Check-out</h3>
            <p><?= date('d/m/Y', strtotime($booking['check_out'])) ?></p>
        </div>
        <div class="info-card">
            <h3>💰 Tổng Tiền</h3>
            <p><?= number_format($booking['total_price'] ?? 0, 0, ',', '.') ?>đ</p>
        </div>
        <div class="info-card">
            <h3>👤 Khách Hàng</h3>
            <p><?= htmlspecialchars($booking['user_name'] ?? $booking['full_name'] ?? 'N/A') ?></p>
        </div>
        <div class="info-card">
            <h3>📍 Phòng</h3>
            <p><?= htmlspecialchars($booking['room_number'] ?? $booking['room_id'] ?? 'N/A') ?></p>
        </div>
        <div class="info-card">
            <h3>📞 Liên Hệ</h3>
            <p><?= htmlspecialchars($booking['phone'] ?? 'N/A') ?></p>
        </div>
    </div>

    <!-- Current Status Section -->
    <div class="status-section">
        <h2>📊 Trạng Thái Đặt Phòng</h2>

        <div class="status-current">
            <p style="margin: 0 0 10px 0; color: #666; font-size: 12px; text-transform: uppercase;">Trạng thái hiện tại</p>
            <?php
            $badgeClass = 'badge-' . ($statusConfig[$currentStatus]['color'] ?? 'secondary');
            $statusLabel = htmlspecialchars($statusConfig[$currentStatus]['label'] ?? ucfirst($currentStatus));
            $description = htmlspecialchars($statusConfig[$currentStatus]['description'] ?? '');
            ?>
            <span class="status-badge <?= $badgeClass ?>">
                ✓ <?= $statusLabel ?>
            </span>
            <p style="margin: 10px 0 0 0; color: #666; font-size: 14px;">
                <?= $description ?>
            </p>
        </div>

        <!-- Status Timeline -->
        <?php if (!empty($notifications) && is_array($notifications)): ?>
            <h3 style="margin-top: 30px; margin-bottom: 15px; color: #333;">📜 Lịch Sử Cập Nhật</h3>
            <div class="status-timeline">
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-item">
                        <div class="time">
                            📅 <?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?>
                        </div>
                        <div class="message">
                            <strong>
                                <?php
                                $oldStatus = $notif['old_status'] ?? 'Bắt đầu';
                                $newStatus = $notif['new_status'] ?? 'Cập nhật';
                                $oldLabel = htmlspecialchars($statusConfig[$oldStatus]['label'] ?? ($oldStatus ?: 'Bắt đầu'));
                                $newLabel = htmlspecialchars($statusConfig[$newStatus]['label'] ?? strtoupper((string) $newStatus));
                                ?>
                                <?= $oldLabel ?> → <?= $newLabel ?>
                            </strong>
                        </div>
                        <?php if (!empty($notif['message'])): ?>
                            <p style="margin: 8px 0 0 0; color: #666; font-size: 13px;">
                                <?= htmlspecialchars($notif['message']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-notifications">
                <p>Chưa có cập nhật trạng thái nào</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <button class="btn btn-secondary" onclick="history.back()">
            ← Quay Lại
        </button>
        <button class="btn btn-primary" onclick="location.reload()">
            🔄 Làm Mới
        </button>
        <a href="<?= \App\Core\url('/') ?>" class="btn btn-secondary">
            🏠 Trang Chủ
        </a>
    </div>
</div>

<script>
    // Auto-refresh every 30 seconds to get live updates
    setTimeout(() => {
        location.reload();
    }, 30000);
</script>
