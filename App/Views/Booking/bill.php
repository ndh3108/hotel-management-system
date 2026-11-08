<?php
// App/Views/Booking/bill.php

use App\Core\BookingStatus;
use App\Core\StatusLabel;

// Lấy dữ liệu từ controller truyền sang
$booking = $booking ?? null;
$invoice = $invoice ?? null;
$invoiceItems = $invoiceItems ?? [];
$paymentTransactions = $paymentTransactions ?? [];

if (!$booking) {
    die('Không tìm thấy thông tin đặt phòng');
}

$status = $booking['status'] ?? 'pending';
$statusLower = strtolower((string) $status);
$canPay = BookingStatus::isAwaitingPayment($status);
$baseUrl = rtrim(APP_URL ?? '', '/');

// Debug: In ra để kiểm tra (có thể bỏ comment khi cần)
// error_log('Bill - Booking data: ' . json_encode($booking));

// Hàm hiển thị trạng thái tiếng Việt
function getStatusLabel($status) { return (string) (StatusLabel::booking((string) $status)['label'] ?? 'Unknown'); }

// Hàm lấy class CSS cho trạng thái
function getStatusClass($status) {
    return match (strtolower((string) $status)) {
        'pending', 'processing', 'pending_payment' => 'status-pending',
        'confirmed', 'checked_in' => 'status-confirmed',
        'checked_out' => 'status-processing',
        'completed' => 'status-completed',
        'cancelled', 'expired', 'no_show' => 'status-cancelled',
        default => 'status-pending',
    };
}

// Tính số ngày lưu trú
$checkIn = $booking['check_in'] ?? null;
$checkOut = $booking['check_out'] ?? null;
$nights = 1;
if ($checkIn && $checkOut) {
    $nights = max(1, ceil((strtotime($checkOut) - strtotime($checkIn)) / (60 * 60 * 24)));
}

// Lấy giá phòng mỗi đêm
$pricePerNight = $booking['price_per_night'] ?? $booking['room_price'] ?? 0;
$totalPrice = $booking['total_price'] ?? 0;

// Nếu total_price không có hoặc bằng 0, tính lại
if ($totalPrice <= 0 && $pricePerNight > 0) {
    $totalPrice = $pricePerNight * $nights;
}

$balanceDue = (float) ($invoice['balance_due'] ?? $totalPrice);
$canPayMomo = $balanceDue > 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hóa đơn đặt phòng - #<?= htmlspecialchars($booking['id'] ?? '') ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* Giữ nguyên CSS của bạn */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 40px 20px;
}

.bill-container { 
    max-width: 1000px; 
    margin: 40px auto; 
}

.bill-card { 
    background: white; 
    padding: 40px; 
    border-radius: 20px; 
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
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

.bill-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.bill-header h2 {
    color: #333;
    font-size: 28px;
    margin-bottom: 10px;
}

.bill-header .bill-date {
    color: #666;
    font-size: 14px;
}

.bill-info { 
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.bill-info p { 
    margin-bottom: 12px; 
    font-size: 16px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}

.bill-info p strong {
    width: 130px;
    color: #555;
}

.bill-info p i {
    width: 25px;
    color: #667eea;
    margin-right: 10px;
}

.bill-table { 
    width: 100%; 
    margin-top: 20px; 
    border-collapse: collapse; 
}

.bill-table th, 
.bill-table td { 
    padding: 15px; 
    border-bottom: 1px solid #e0e0e0; 
    text-align: left; 
}

.bill-table th { 
    background: #f8f9fa; 
    font-weight: 600;
    color: #555;
}

.bill-table tr:hover {
    background: #f8f9fa;
}

.bill-total { 
    margin-top: 30px; 
    padding-top: 20px;
    text-align: right; 
    border-top: 2px solid #f0f0f0;
}

.bill-total h3 {
    font-size: 24px;
    color: #28a745;
}

.bill-total .nights-info {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.status { 
    padding: 6px 15px; 
    border-radius: 50px; 
    font-weight: 600; 
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.status-pending { 
    background: #fff3cd; 
    color: #856404; 
}

.status-processing { 
    background: #ffeeba; 
    color: #856404; 
    animation: blink 1s infinite; 
}

.status-paid { 
    background: #d4edda; 
    color: #155724; 
}

.status-confirmed { 
    background: #cfe2ff; 
    color: #004085; 
}

.status-cancelled { 
    background: #f8d7da; 
    color: #721c24; 
}

.status-completed { 
    background: #d1ecf1; 
    color: #0c5460; 
}

@keyframes blink { 
    0%,100%{opacity:1;} 
    50%{opacity:0.6;} 
}

.bill-action { 
    margin-top: 35px; 
    text-align: right; 
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
}

.btn { 
    padding: 12px 28px; 
    border-radius: 10px; 
    font-weight: 600; 
    text-decoration: none; 
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s; 
    border: none; 
    cursor: pointer; 
    font-size: 14px;
}

.btn-pay { 
    background: #28a745; 
    color: #fff; 
}

.btn-pay:hover { 
    background: #218838; 
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40,167,69,0.3);
}

.btn-home { 
    background: #6c757d; 
    color: #fff; 
}

.btn-home:hover { 
    background: #5a6268; 
    transform: translateY(-2px);
}

.btn-refresh { 
    background: #17a2b8; 
    color: #fff; 
}

.btn-refresh:hover { 
    background: #138496; 
    transform: translateY(-2px);
}

.btn-print { 
    background: #007bff; 
    color: #fff; 
}

.btn-print:hover { 
    background: #0056b3; 
    transform: translateY(-2px);
}

.processing { 
    color: #ff9800; 
    font-weight: 600; 
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.paid { 
    color: #28a745; 
    font-weight: 600; 
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.cancelled { 
    color: #dc3545; 
    font-weight: 600; 
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

@media print {
    body {
        background: white;
        padding: 0;
    }
    .bill-action {
        display: none;
    }
    .bill-card {
        box-shadow: none;
        padding: 0;
    }
}
</style>
</head>
<body>

<div class="bill-container">
<div class="bill-card">

<div class="bill-header">
    <h2>
        <i class="fa-solid fa-receipt"></i> HÓA ĐƠN ĐẶT PHÒNG
    </h2>
    <div class="bill-date">
        Ngày xuất: <?= date('d/m/Y H:i:s') ?>
    </div>
</div>

<div class="bill-info">
    <p>
        <i class="fa-solid fa-hashtag"></i>
        <strong>Mã đơn:</strong> 
        <span>#<?= htmlspecialchars($booking['id'] ?? '-') ?></span>
    </p>
    <p>
        <i class="fa-solid fa-door-open"></i>
        <strong>Phòng:</strong> 
        <span><?= htmlspecialchars($booking['room_type'] ?? $booking['room_type_name'] ?? '-') ?></span>
        <?php if (!empty($booking['room_number'])): ?>
            (Số phòng: <?= htmlspecialchars($booking['room_number']) ?>)
        <?php endif; ?>
    </p>
    <p>
        <i class="fa-solid fa-calendar-check"></i>
        <strong>Ngày nhận phòng:</strong> 
        <span><?= $checkIn ? date('d/m/Y', strtotime($checkIn)) : '-' ?></span>
    </p>
    <p>
        <i class="fa-solid fa-calendar-times"></i>
        <strong>Ngày trả phòng:</strong> 
        <span><?= $checkOut ? date('d/m/Y', strtotime($checkOut)) : '-' ?></span>
    </p>
    <p>
        <i class="fa-solid fa-moon"></i>
        <strong>Số đêm:</strong> 
        <span><?= $nights ?> đêm</span>
    </p>
    <p>
        <i class="fa-solid fa-user"></i>
        <strong>Khách hàng:</strong> 
        <span><?= htmlspecialchars($booking['guest_name'] ?? $booking['full_name'] ?? 'Khách lẻ') ?></span>
    </p>
    <p>
        <i class="fa-solid fa-phone"></i>
        <strong>Số điện thoại:</strong> 
        <span><?= htmlspecialchars($booking['guest_phone'] ?? $booking['phone'] ?? 'Chưa cập nhật') ?></span>
    </p>
    <p>
        <i class="fa-solid fa-envelope"></i>
        <strong>Email:</strong> 
        <span><?= htmlspecialchars($booking['guest_email'] ?? $booking['email'] ?? 'Chưa cập nhật') ?></span>
    </p>
    <p>
        <i class="fa-solid fa-tag"></i>
        <strong>Trạng thái:</strong>
        <span class="status <?= getStatusClass($status) ?>">
            <i class="fa-solid <?= $status == 'paid' ? 'fa-circle-check' : ($status == 'pending' ? 'fa-clock' : ($status == 'cancelled' ? 'fa-xmark' : 'fa-spinner fa-spin')) ?>"></i>
            <?= getStatusLabel($status) ?>
        </span>
    </p>
</div>

<!-- Bảng chi tiết -->
<table class="bill-table">
    <thead>
        <tr>
            <th>STT</th>
            <th>Dịch vụ</th>
            <th>Số lượng</th>
            <th>Đơn giá</th>
            <th>Thành tiền</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>
                <strong>Phòng <?= htmlspecialchars($booking['room_type'] ?? $booking['room_type_name'] ?? '-') ?></strong>
                <?php if (!empty($booking['room_description'])): ?>
                    <br><small style="color: #666;"><?= htmlspecialchars($booking['room_description']) ?></small>
                <?php endif; ?>
                <?php if ($nights > 1): ?>
                    <br><small style="color: #666;">(<?= $nights ?> đêm x <?= number_format($pricePerNight) ?> VND/đêm)</small>
                <?php endif; ?>
            </td>
            <td><?= $nights ?></td>
            <td><?= number_format($pricePerNight) ?> VND</td>
            <td><?= number_format($totalPrice) ?> VND</td>
        </tr>
    </tbody>
</table>

<div class="bill-total">
    <h3>Tổng tiền: <?= number_format($totalPrice) ?> VND</h3>
    <div class="nights-info">(Giá đã bao gồm VAT | <?= $nights ?> đêm x <?= number_format($pricePerNight) ?> VND)</div>
</div>

<?php if ($invoice): ?>
<div style="margin-top:20px; background:#f8fbff; border:1px solid #dbeafe; border-radius:12px; padding:16px;">
    <h4 style="margin-bottom:10px; color:#1d4ed8;">Chi tiết hóa đơn</h4>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px;">
        <div>Tiền phòng: <strong><?= number_format((float) ($invoice['room_amount'] ?? $invoice['total_room_amount'] ?? 0), 0, ',', '.') ?>đ</strong></div>
        <div>Tiền dịch vụ: <strong><?= number_format((float) ($invoice['service_amount'] ?? $invoice['total_service_amount'] ?? 0), 0, ',', '.') ?>đ</strong></div>
        <div>Late fee: <strong><?= number_format((float) ($invoice['late_fee'] ?? 0), 0, ',', '.') ?>đ</strong></div>
        <div>Damage fee: <strong><?= number_format((float) ($invoice['damage_fee'] ?? 0), 0, ',', '.') ?>đ</strong></div>
        <div>Giảm giá: <strong>-<?= number_format((float) ($invoice['discount_amount'] ?? 0), 0, ',', '.') ?>đ</strong></div>
        <div>Đã trả trước: <strong><?= number_format((float) ($invoice['prepaid_amount'] ?? 0), 0, ',', '.') ?>đ</strong></div>
        <div>Còn lại: <strong style="color:#dc2626;"><?= number_format($balanceDue, 0, ',', '.') ?>đ</strong></div>
    </div>
    <?php if (!empty($invoiceItems)): ?>
        <table class="bill-table" style="margin-top:12px;">
            <thead><tr><th>Loại</th><th>Ref</th><th>Số tiền</th><th>Ghi chú</th></tr></thead>
            <tbody>
                <?php foreach ($invoiceItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) ($item['type'] ?? '')) ?></td>
                        <td>#<?= (int) ($item['reference_id'] ?? 0) ?></td>
                        <td><?= number_format((float) ($item['amount'] ?? 0), 0, ',', '.') ?>đ</td>
                        <td><?= htmlspecialchars((string) ($item['notes'] ?? '')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php if (!empty($paymentTransactions)): ?>
        <h5 style="margin-top:14px;">Lịch sử thanh toán</h5>
        <table class="bill-table">
            <thead><tr><th>Gateway</th><th>Mã giao dịch</th><th>Số tiền</th><th>Trạng thái</th><th>Thời gian</th></tr></thead>
            <tbody>
                <?php foreach ($paymentTransactions as $txn): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) ($txn['gateway'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string) ($txn['external_txn_id'] ?? '')) ?></td>
                        <td><?= number_format((float) ($txn['amount'] ?? 0), 0, ',', '.') ?>đ</td>
                        <td><?= htmlspecialchars((string) ($txn['status'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string) ($txn['paid_at'] ?? $txn['created_at'] ?? '')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Nút hành động -->
<div class="bill-action">
    <?php if ($canPay): ?>
        <form method="POST" action="<?= \App\Core\url('/payment/vnpay') ?>" style="display: inline;">
            <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
            <button type="submit" name="pay" value="1" class="btn btn-pay">
                <i class="fa-solid fa-credit-card"></i> Thanh toán ngay
            </button>
        </form>
        <?php if ($canPayMomo): ?>
            <form method="POST" action="<?= \App\Core\url('/payment/momo') ?>" style="display: inline;">
                <input type="hidden" name="booking_id" value="<?= (int) ($booking['id'] ?? 0) ?>">
                <?php if (!empty($invoice['id'])): ?>
                    <input type="hidden" name="invoice_id" value="<?= (int) $invoice['id'] ?>">
                <?php endif; ?>
                <input type="hidden" name="amount" value="<?= (float) $balanceDue ?>">
                <button type="submit" class="btn" style="background:#d82d8b; color:#fff;">
                    <i class="fa-solid fa-wallet"></i> Thanh toán MoMo
                </button>
            </form>
        <?php endif; ?>
    <?php elseif($statusLower == 'processing'): ?>
        <span class="processing">
            <i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý thanh toán
        </span>
        <button onclick="checkPaymentStatus(<?= $booking['id'] ?>)" class="btn btn-refresh">
            <i class="fa-solid fa-rotate-right"></i> Kiểm tra
        </button>
    <?php elseif(in_array($statusLower, ['paid', 'confirmed'])): ?>
        <span class="paid">
            <i class="fa-solid fa-circle-check"></i> Đã thanh toán thành công
        </span>
        <button onclick="window.print()" class="btn btn-print">
            <i class="fa-solid fa-print"></i> In hóa đơn
        </button>
    <?php elseif($statusLower == 'cancelled'): ?>
        <span class="cancelled">
            <i class="fa-solid fa-xmark"></i> Đơn hàng đã hủy
        </span>
    <?php endif; ?>
    
    <a href="<?= $baseUrl ?>/" class="btn btn-home">
        <i class="fa-solid fa-home"></i> Về trang chủ
    </a>
</div>

<script>
function checkPaymentStatus(bookingId) {
    const btn = event.target.closest('.btn-refresh');
    if (!btn) return;
    
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang kiểm tra...';
    btn.disabled = true;
    
    fetch('/doan/public/api/check-payment-status?id=' + bookingId)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'paid' || data.status === 'confirmed') {
                location.reload();
            } else {
                alert('Thanh toán chưa hoàn tất. Vui lòng thử lại sau.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi kiểm tra trạng thái');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}
</script>

</body>
</html>

