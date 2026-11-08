<?php
use App\Core\StatusLabel;
$baseUrl = rtrim(APP_URL ?? '', '/');
?>
<div class="booking-container">
    <div class="booking-header">
        <h2>
            <i class="fa-solid fa-concierge-bell"></i>
            Đơn dịch vụ của tôi
        </h2>
        <p class="subtitle">Quản lý và theo dõi các dịch vụ bạn đã đặt</p>
    </div>

    <?php if(empty($serviceBookings)): ?>
        <div class="empty-state">
            <i class="fa-solid fa-concierge-bell" style="font-size: 80px; color: #cbd5e1; margin-bottom: 20px;"></i>
            <h3>Chưa có đơn dịch vụ nào</h3>
            <p>Khám phá các dịch vụ làm đẹp, nhà hàng và tiện ích ngay hôm nay!</p>
            <a href="<?= \App\Core\url('services') ?>" class="btn-explore">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm dịch vụ ngay
            </a>
        </div>
    <?php else: ?>
        <div class="booking-stats">
            <div class="stat-card">
                <i class="fa-solid fa-bell-concierge"></i>
                <div class="stat-info">
                    <span class="stat-number"><?= count($serviceBookings) ?></span>
                    <span class="stat-label">Tổng đặt dịch vụ</span>
                </div>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-clock"></i>
                <div class="stat-info">
                    <span class="stat-number">
                        <?= count(array_filter($serviceBookings, function($b) { 
                            $st = strtolower($b['status'] ?? 'requested');
                            return $st !== 'reserved' && $st !== 'paid' && $st !== 'completed' && $st !== 'cancelled';
                        })) ?>
                    </span>
                    <span class="stat-label">Chờ thanh toán</span>
                </div>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-check-circle"></i>
                <div class="stat-info">
                    <span class="stat-number">
                        <?= count(array_filter($serviceBookings, function($b) { 
                            $st = strtolower($b['status'] ?? 'requested');
                            return in_array($st, ['reserved', 'paid', 'completed'], true);
                        })) ?>
                    </span>
                    <span class="stat-label">Đã thanh toán</span>
                </div>
            </div>
        </div>

        <div class="bookings-list">
            <?php foreach($serviceBookings as $sb): 
                $status = strtolower($sb['status'] ?? 'requested');
                $isPaid = ($status === 'reserved' || $status === 'paid' || $status === 'completed');
                $isCancelled = ($status === 'cancelled');
                
                $statusClass = 'status-pending';
                $statusIcon = 'fa-clock';
                $statusText = 'Chờ thanh toán';
                
                if ($isPaid) {
                    $statusClass = 'status-paid';
                    $statusIcon = 'fa-check-circle';
                    $statusText = 'Đã thanh toán';
                } elseif ($isCancelled) {
                    $statusClass = 'status-cancelled';
                    $statusIcon = 'fa-times-circle';
                    $statusText = 'Đã hủy';
                } elseif ($status === 'requested') {
                    $statusText = 'Chờ tại quầy';
                }
            ?>
                <div class="booking-card">
                    <div class="booking-card-header">
                        <div class="room-type">
                            <i class="fa-solid fa-concierge-bell"></i>
                            <h3><?= htmlspecialchars($sb['service_name'] ?? 'Dịch vụ') ?></h3>
                        </div>
                        <div class="room-number">
                            <i class="fa-solid fa-hashtag"></i>
                            <span>Mã SB: <?= $sb['id'] ?></span>
                        </div>
                    </div>

                    <div class="booking-details">
                        <div class="detail-item">
                            <i class="fa-regular fa-calendar-check"></i>
                            <div class="detail-info">
                                <span class="detail-label">Bắt đầu</span>
                                <span class="detail-value"><?= date('d/m/Y H:i', strtotime($sb['start_time'] ?? 'now')) ?></span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fa-regular fa-calendar-xmark"></i>
                            <div class="detail-info">
                                <span class="detail-label">Kết thúc</span>
                                <span class="detail-value"><?= date('d/m/Y H:i', strtotime($sb['end_time'] ?? 'now')) ?></span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fa-solid fa-box"></i>
                            <div class="detail-info">
                                <span class="detail-label">Số lượng</span>
                                <span class="detail-value"><?= (int)($sb['quantity'] ?? 1) ?></span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <div class="detail-info">
                                <span class="detail-label">Tổng tiền</span>
                                <span class="detail-value price"><?= number_format((int)($sb['total_price'] ?? 0)) ?> VND</span>
                            </div>
                        </div>
                    </div>

                    <div class="booking-footer">
                        <div class="status-badge <?= $statusClass ?>">
                            <i class="fa-solid <?= $statusIcon ?>"></i>
                            <span><?= $statusText ?></span>
                        </div>

                        <div class="action-buttons">
                            <a href="<?= $baseUrl ?>/services/booking/bill?id=<?= $sb['id'] ?>" class="btn btn-detail">
                                <i class="fa-solid fa-receipt"></i>
                                <span>Chi tiết</span>
                            </a>
                            
                            <?php if (!$isPaid && !$isCancelled): ?>
                                <a href="<?= $baseUrl ?>/services/payment/momo?id=<?= $sb['id'] ?>" class="btn btn-pay" style="background: linear-gradient(135deg, #ae2070, #80004d);">
                                    <i class="fa-solid fa-mobile-screen"></i> MoMo
                                </a>
                                <a href="<?= $baseUrl ?>/services/payment/vnpay?id=<?= $sb['id'] ?>" class="btn btn-pay" style="background: linear-gradient(135deg, #005baa, #003b70);">
                                    <i class="fa-solid fa-credit-card"></i> VNPay
                                </a>
                                <form action="<?= $baseUrl ?>/services/payment/pay-later" method="POST" style="margin: 0;">
                                    <input type="hidden" name="id" value="<?= $sb['id'] ?>">
                                    <button type="submit" class="btn btn-pay" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                        <i class="fa-solid fa-cash-register"></i> Tại quầy
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .booking-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 40px 20px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* Header */
    .booking-header {
        margin-bottom: 40px;
        text-align: center;
    }

    .booking-header h2 {
        font-size: 32px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
        display: inline-flex;
        align-items: center;
        gap: 12px;
    }

    .booking-header h2 i {
        color: #3b82f6;
    }

    .subtitle {
        color: #64748b;
        font-size: 16px;
    }

    /* Stats Cards */
    .booking-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s;
        border: 1px solid #e2e8f0;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .stat-card i {
        font-size: 40px;
        color: #3b82f6;
    }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 800;
        color: #1e293b;
    }

    .stat-label {
        font-size: 14px;
        color: #64748b;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .empty-state i {
        font-size: 80px;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        font-size: 24px;
        color: #1e293b;
        margin-bottom: 12px;
    }

    .empty-state p {
        color: #64748b;
        margin-bottom: 24px;
    }

    .btn-explore {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        padding: 12px 28px;
        border-radius: 40px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-explore:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59,130,246,0.3);
    }

    /* Bookings List */
    .bookings-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Booking Card */
    .booking-card {
        background: white;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: all 0.3s;
        border: 1px solid #e2e8f0;
    }

    .booking-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        border-color: #3b82f6;
    }

    /* Card Header */
    .booking-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f1f5f9;
    }

    .room-type {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .room-type i {
        font-size: 24px;
        color: #3b82f6;
    }

    .room-type h3 {
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .room-number {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        color: #475569;
    }

    .room-number i {
        font-size: 12px;
    }

    /* Booking Details */
    .booking-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .detail-item i {
        font-size: 22px;
        color: #94a3b8;
        width: 32px;
    }

    .detail-info {
        display: flex;
        flex-direction: column;
    }

    .detail-label {
        font-size: 11px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
    }

    .detail-value.price {
        color: #f59e0b;
        font-size: 16px;
    }

    /* Footer */
    .booking-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #e2e8f0;
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 40px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-pending {
        background: #fef3c7;
        color: #d97706;
    }

    .status-processing {
        background: #e0e7ff;
        color: #4f46e5;
        animation: pulse 1.5s infinite;
    }

    .status-paid {
        background: #d1fae5;
        color: #059669;
    }

    .status-confirmed {
        background: #dbeafe;
        color: #2563eb;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #dc2626;
    }

    .status-default {
        background: #f1f5f9;
        color: #475569;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 20px;
        border-radius: 40px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }

    .btn-detail {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-detail:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    .btn-pay {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-pay:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16,185,129,0.4);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .booking-container {
            padding: 20px;
        }

        .booking-card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .booking-details {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .booking-footer {
            flex-direction: column;
            gap: 16px;
            align-items: stretch;
        }

        .action-buttons {
             flex-direction: column; 
        }

        .action-buttons .btn {
            justify-content: center;
            width: 100%;
        }

        .status-badge {
            justify-content: center;
        }
    }
</style>
