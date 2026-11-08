<?php
$booking = $booking ?? null;
$paymentStatus = $paymentStatus ?? '';
$paymentMessage = $paymentMessage ?? '';

if (!$booking) {
    die('Không tìm thấy thông tin đặt phòng');
}

$baseUrl = rtrim(APP_URL ?? '', '/');
$bookingId = $booking['id'] ?? 0;
$statusValue = strtolower($booking['status'] ?? 'pending');
$totalAmount = $booking['total_price'] ?? 0;

// Determine status labels and classes
$statusLabel = 'Chờ thanh toán';
$badgeClass = 'status-pending';

if ($statusValue === 'processing') {
    $statusLabel = 'Đang xử lý';
    $badgeClass = 'status-processing';
} elseif ($statusValue === 'paid' || $statusValue === 'confirmed') {
    $statusLabel = 'Đã xác nhận';
    $badgeClass = 'status-paid';
} elseif ($statusValue === 'cancelled') {
    $statusLabel = 'Đã hủy';
    $badgeClass = 'status-cancelled';
} elseif ($statusValue === 'completed') {
    $statusLabel = 'Đã hoàn thành';
    $badgeClass = 'status-completed';
}

$canPay = \App\Core\BookingStatus::isAwaitingPayment($statusValue);
$showPaymentBtn = ($paymentStatus !== 'success' && $canPay);

// Calculate nights
$nights = 1;
if (!empty($booking['check_in']) && !empty($booking['check_out'])) {
    $nights = max(1, ceil((strtotime($booking['check_out']) - strtotime($booking['check_in'])) / (60 * 60 * 24)));
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Đặt Phòng - Antigravity Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
            --momo-color: #A50064;
            --vnpay-color: #005baa;
            --pay-later: #f59e0b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            background: #f8fafc;
            background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                              radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                              radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .success-card {
            background: white;
            width: 100%;
            max-width: 600px;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 40px 32px;
            text-align: center;
            color: white;
        }

        .success-icon {
            width: 70px;
            height: 70px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 0 20px rgba(16,185,129,0.4);
        }

        .success-icon i { font-size: 35px; color: white; }

        .header-title { font-family: var(--font-heading); font-size: 28px; font-weight: 800; margin-bottom: 8px; }
        .header-subtitle { color: #94a3b8; font-size: 14px; }

        .card-body { padding: 32px; }

        .status-banner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 12px;
            border-radius: 16px;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .status-pending { background: #fef3c7; color: #d97706; }
        .status-processing { background: #e0f2fe; color: #0369a1; }
        .status-paid { background: #d1fae5; color: #059669; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
        .status-completed { background: #f1f5f9; color: #475569; }

        .booking-summary {
            background: #f8fafc;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary-row:last-child { border-bottom: none; }

        .summary-label { color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-value { color: #1e293b; font-weight: 700; font-size: 15px; text-align: right; }

        .total-price { color: #0f172a; font-size: 20px !important; }

        .payment-methods {
            display: grid;
            gap: 12px;
            margin-top: 30px;
        }

        .btn-payment {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px;
            border-radius: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            width: 100%;
            font-family: var(--font-body);
        }

        .btn-momo { background: var(--momo-color); color: white; }
        .btn-momo:hover { background: #bd0073; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(165,0,100,0.3); }

        .btn-vnpay { background: var(--vnpay-color); color: white; }
        .btn-vnpay:hover { background: #004a80; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,91,170,0.3); }

        .btn-later { background: #f8fafc; border: 2px solid #e2e8f0; color: #475569; }
        .btn-later:hover { background: #f1f5f9; border-color: var(--pay-later); color: var(--pay-later); }

        .secondary-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 20px;
        }

        .btn-secondary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            color: #64748b;
            font-weight: 600;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-secondary:hover { background: #f8fafc; border-color: #cbd5e1; color: #1e293b; }

        @media (max-width: 480px) {
            .secondary-actions { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="card-header">
            <div class="success-icon">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1 class="header-title">Xác Nhận Đặt Phòng</h1>
            <p class="header-subtitle">Mã đơn hàng: #<?= (int)$bookingId ?> • Đã ghi nhận lúc <?= date('H:i, d/m/Y', strtotime($booking['created_at'])) ?></p>
        </div>

        <div class="card-body">
            <div class="status-banner <?= $badgeClass ?>">
                <i class="fa-solid <?= (in_array($statusLabel, ['Đã xác nhận', 'Đã hoàn thành'])) ? 'fa-circle-check' : 'fa-clock-rotate-left' ?>"></i>
                <?= htmlspecialchars($statusLabel) ?>
            </div>

            <?php if ($paymentStatus === 'error' || $paymentStatus === 'failed'): ?>
                <div style="background: #fef2f2; border: 1px solid #fecaca; padding: 15px; border-radius: 16px; color: #dc2626; margin-bottom: 25px; font-size: 14px; text-align: center;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($paymentMessage ?: 'Thanh toán không thành công. Vui lòng thử lại.') ?>
                </div>
            <?php endif; ?>

            <div class="booking-summary">
                <div class="summary-row">
                    <span class="summary-label">Loại phòng</span>
                    <span class="summary-value"><?= htmlspecialchars($booking['room_type'] ?? $booking['room_type_name'] ?? 'Phòng Hotel') ?></span>
                </div>
                <?php if (!empty($booking['room_number'])): ?>
                <div class="summary-row">
                    <span class="summary-label">Số phòng</span>
                    <span class="summary-value"><?= htmlspecialchars($booking['room_number']) ?></span>
                </div>
                <?php endif; ?>
                <div class="summary-row">
                    <span class="summary-label">Nhận phòng</span>
                    <span class="summary-value"><?= !empty($booking['check_in']) ? date('d/m/Y', strtotime($booking['check_in'])) : '--/--/----' ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Trả phòng</span>
                    <span class="summary-value"><?= !empty($booking['check_out']) ? date('d/m/Y', strtotime($booking['check_out'])) : '--/--/----' ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Thời gian</span>
                    <?php 
                        $nightsCount = 0;
                        if (!empty($booking['check_in']) && !empty($booking['check_out'])) {
                            $checkIn = new DateTime($booking['check_in']);
                            $checkOut = new DateTime($booking['check_out']);
                            $nightsCount = $checkIn->diff($checkOut)->days;
                        }
                    ?>
                    <span class="summary-value"><?= (int)$nightsCount ?> Đêm</span>
                </div>
                <div class="summary-row" style="border-top: 2px dashed #e2e8f0; margin-top: 10px; padding-top: 15px;">
                    <span class="summary-label">Tổng cộng</span>
                    <span class="summary-value total-price"><?= number_format($totalAmount) ?> VND</span>
                </div>
            </div>

            <?php if ($showPaymentBtn): ?>
                <div style="text-align: center; margin-bottom: 15px;">
                    <p style="font-size: 14px; color: #64748b; font-weight: 500;">Vui lòng chọn phương thức thanh toán:</p>
                </div>
                <div class="payment-methods">
                    <form action="<?= $baseUrl ?>/payment/momo" method="POST">
                        <input type="hidden" name="booking_id" value="<?= (int)$bookingId ?>">
                        <button type="submit" class="btn-payment btn-momo">
                            <i class="fa-solid fa-wallet"></i>
                            Thanh toán qua MoMo
                        </button>
                    </form>
                    
                    <form action="<?= $baseUrl ?>/payment/vnpay" method="POST">
                        <input type="hidden" name="booking_id" value="<?= (int)$bookingId ?>">
                        <button type="submit" class="btn-payment btn-vnpay">
                            <i class="fa-solid fa-credit-card"></i>
                            Thanh toán qua VNPay
                        </button>
                    </form>

                    <form action="<?= $baseUrl ?>/payment/pay-later" method="POST">
                        <input type="hidden" name="booking_id" value="<?= (int)$bookingId ?>">
                        <button type="submit" class="btn-payment btn-later">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                            Thanh toán tại quầy
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 20px; background: #f0fdf4; border-radius: 16px; border: 1px solid #dcfce7; color: #166534; font-weight: 600;">
                    <i class="fa-solid fa-heart" style="color: #ed419d;"></i> Cảm ơn quý khách đã tin tưởng và lựa chọn Antigravity!
                </div>
            <?php endif; ?>

            <div class="secondary-actions">
                <a href="<?= $baseUrl ?>/bill?id=<?= $bookingId ?>" class="btn-secondary">
                    <i class="fa-solid fa-file-invoice"></i>
                    Xem hóa đơn
                </a>
                <a href="<?= $baseUrl ?>/" class="btn-secondary">
                    <i class="fa-solid fa-house"></i>
                    Về trang chủ
                </a>
            </div>
        </div>
    </div>
</body>
</html>
