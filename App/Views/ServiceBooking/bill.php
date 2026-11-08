<?php
$booking = $booking ?? null;
if (!$booking) {
    die('Không tìm thấy thông tin đặt dịch vụ');
}

$status = $booking['status'] ?? 'requested';
$statusLower = strtolower((string) $status);
$isPaid = in_array($statusLower, ['reserved', 'paid', 'completed']);
$isCancelled = $statusLower === 'cancelled';
$canPay = !$isPaid && !$isCancelled;
$baseUrl = rtrim(APP_URL ?? '', '/');

$totalPrice = (int)($booking['total_price'] ?? 0);
$quantity = (int)($booking['quantity'] ?? 1);

function getStatusClass($status) {
    return match (strtolower((string) $status)) {
        'requested' => 'status-pending',
        'reserved', 'paid', 'completed' => 'status-paid',
        'cancelled' => 'status-cancelled',
        default => 'status-pending',
    };
}
function getStatusLabel($status) {
    return match (strtolower((string) $status)) {
        'requested' => 'Chờ thanh toán',
        'reserved', 'paid', 'completed' => 'Đã thanh toán',
        'cancelled' => 'Đã hủy',
        default => 'Chờ xử lý',
    };
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hóa đơn dịch vụ - #<?= htmlspecialchars($booking['id'] ?? '') ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
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

.status-paid { 
    background: #d4edda; 
    color: #155724; 
}

.status-cancelled { 
    background: #f8d7da; 
    color: #721c24; 
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

.btn-print { 
    background: #007bff; 
    color: #fff; 
}

.btn-print:hover { 
    background: #0056b3; 
    transform: translateY(-2px);
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
        <i class="fa-solid fa-receipt"></i> HÓA ĐƠN ĐẶT DỊCH VỤ
    </h2>
    <div class="bill-date">
        Ngày xuất: <?= date('d/m/Y H:i:s') ?>
    </div>
</div>

<div class="bill-info">
    <p>
        <i class="fa-solid fa-hashtag"></i>
        <strong>Mã đơn:</strong> 
        <span>SB_<?= htmlspecialchars($booking['id'] ?? '-') ?></span>
    </p>
    <p>
        <i class="fa-solid fa-concierge-bell"></i>
        <strong>Dịch vụ:</strong> 
        <span><?= htmlspecialchars($booking['service_name'] ?? 'Dịch vụ cao cấp') ?></span>
    </p>
    <p>
        <i class="fa-regular fa-calendar-check"></i>
        <strong>Thời gian bắt đầu:</strong> 
        <span><?= date('d/m/Y H:i', strtotime($booking['check_in'] ?? $booking['start_time'] ?? 'now')) ?></span>
    </p>
    <p>
        <i class="fa-regular fa-calendar-minus"></i>
        <strong>Thời gian kết thúc:</strong> 
        <span><?= date('d/m/Y H:i', strtotime($booking['check_out'] ?? $booking['end_time'] ?? 'now')) ?></span>
    </p>
    <p>
        <i class="fa-solid fa-layer-group"></i>
        <strong>Số lượng:</strong> 
        <span><?= (int)($booking['quantity'] ?? 1) ?></span>
    </p>
    <p>
        <i class="fa-solid fa-user"></i>
        <strong>Khách hàng:</strong> 
        <span><?= htmlspecialchars($booking['guest_name'] ?? $booking['user_name'] ?? $booking['full_name'] ?? 'Khách lẻ') ?></span>
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
            <i class="fa-solid <?= $isPaid ? 'fa-circle-check' : ($isCancelled ? 'fa-xmark' : 'fa-clock') ?>"></i>
            <?= getStatusLabel($status) ?>
        </span>
    </p>
</div>

<!-- Bảng chi tiết -->
<table class="bill-table">
    <thead>
        <tr>
            <th>STT</th>
            <th>Mô tả dịch vụ</th>
            <th>Số lượng</th>
            <th>Đơn giá</th>
            <th>Thành tiền</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>
                <strong><?= htmlspecialchars($booking['service_name'] ?? 'Dịch vụ') ?></strong>
                <br><small><?= date('d/m/Y H:i', strtotime($booking['check_in'] ?? $booking['start_time'] ?? 'now')) ?> - <?= date('d/m/Y H:i', strtotime($booking['check_out'] ?? $booking['end_time'] ?? 'now')) ?></small>
            </td>
            <td><?= $quantity ?></td>
            <td><?= number_format($totalPrice / $quantity) ?> VND</td>
            <td><?= number_format($totalPrice) ?> VND</td>
        </tr>
    </tbody>
</table>

<div class="bill-total">
    <h3>Tổng thanh toán: <?= number_format($totalPrice) ?> VND</h3>
    <div style="font-size: 13px; color: #64748b; margin-top: 5px;">(Đã bao gồm phí phục vụ và thuế)</div>
</div>

<!-- Nút hành động -->
<div class="bill-action">
    <?php if ($canPay): ?>
        <form method="POST" action="<?= $baseUrl ?>/services/payment/momo" style="display:inline;">
            <input type="hidden" name="id" value="<?= $booking['id'] ?>">
            <button type="submit" class="btn" style="background:#ae2070; color:#fff;">
                <i class="fa-solid fa-mobile-screen"></i> MoMo
            </button>
        </form>
        <form method="POST" action="<?= $baseUrl ?>/services/payment/vnpay" style="display:inline;">
            <input type="hidden" name="id" value="<?= $booking['id'] ?>">
            <button type="submit" class="btn" style="background:#005baa; color:#fff;">
                <i class="fa-solid fa-credit-card"></i> VNPay
            </button>
        </form>
    <?php elseif($isPaid): ?>
        <button onclick="window.print()" class="btn btn-print">
            <i class="fa-solid fa-print"></i> In hóa đơn
        </button>
    <?php endif; ?>
    
    <a href="<?= $baseUrl ?>/service-bookings" class="btn btn-home">
        <i class="fa-solid fa-arrow-left"></i> Quay lại Đơn hàng
    </a>
</div>

</div>
</div>

</body>
</html>
