<?php
use App\Core\BookingStatus;
use App\Models\Booking;

// Tạo instance Booking
$bookingModel = new Booking();

// Lấy dữ liệu VNPay
$vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
$amount = ($_GET['vnp_Amount'] ?? 0) / 100;
$transaction = $_GET['vnp_TransactionNo'] ?? '';
$orderId = $_GET['vnp_TxnRef'] ?? '';

// Kiểm tra giao dịch thành công
$success = ($vnp_ResponseCode === "00");

$booking = null;

if ($orderId) {
    $booking = $bookingModel->findById($orderId);

    if ($booking) {
        if ($success && ($booking['status'] ?? '') === BookingStatus::PENDING_PAYMENT) {
            // Nếu thanh toán thành công và trước đó PENDING_PAYMENT → chuyển sang CONFIRMED
            $bookingModel->update($orderId, [
                'status' => BookingStatus::CONFIRMED,
                'transaction_no' => $transaction,
                'paid_amount' => $amount
            ]);
        }

        // Lấy lại dữ liệu mới nhất
        $booking = $bookingModel->findById($orderId);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Kết quả thanh toán & Hóa đơn</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { font-family:sans-serif; background:#f5f5f5; margin:0; padding:20px; }
.bill-container{ max-width:900px; margin:40px auto; }
.bill-card{ background:white; padding:30px; border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,0.1); }
.bill-info p{ margin-bottom:10px; font-size:16px; }
.bill-table{ width:100%; margin-top:20px; border-collapse:collapse; }
.bill-table th, .bill-table td{ padding:12px; border-bottom:1px solid #ddd; text-align:left; }
.bill-table th{ background:#f5f5f5; }
.bill-total{ margin-top:20px; text-align:right; font-size:18px; font-weight:bold; }
.status{ padding:6px 12px; border-radius:50px; font-weight:bold; display:inline-block; }
.status-pending_payment { background:#fff3cd; color:#856404; }
.status-confirmed { background:#d4edda; color:#155724; }
.status-cancelled { background:#f8d7da; color:#721c24; }
@keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.5;} }
.bill-action{ margin-top:25px; text-align:right; }
.btn{ padding:12px 22px; border-radius:8px; font-weight:600; text-decoration:none; display:inline-block; transition:all 0.3s; margin-left:5px; }
.btn-pay { background:#28a745; color:#fff; }
.btn-pay:hover { background:#218838; }
.btn-home { background:#007bff; color:#fff; }
.btn-home:hover { background:#0056b3; }
.processing{ color:orange; font-weight:bold; }
.paid{ color:green; font-weight:bold; }
.cancelled{ color:red; font-weight:bold; }
.payment-icon { font-size:60px; margin-bottom:10px; display:block; text-align:center; }
.payment-icon.success-icon { color:#28a745; }
.payment-icon.fail-icon { color:red; }
.payment-title { font-size:24px; text-align:center; font-weight:bold; margin-bottom:10px; }
.payment-desc { text-align:center; margin-bottom:20px; color:#555; }
.payment-box { background:#f8f9fa; padding:20px; border-radius:8px; margin-bottom:20px; }
.payment-row { display:flex; justify-content:space-between; margin-bottom:8px; }
.payment-money { font-weight:bold; }
</style>
</head>
<body>

<div class="bill-container">
<div class="bill-card">

<!-- Icon kết quả thanh toán -->
<div class="payment-icon <?= $success ? 'success-icon':'fail-icon' ?>">
<?= $success ? '✔':'✕' ?>
</div>
<div class="payment-title"><?= $success ? 'Thanh toán thành công' : 'Thanh toán thất bại' ?></div>
<div class="payment-desc">
<?php if($success): ?>
Giao dịch của bạn đang được xử lý. Vui lòng chờ xác nhận từ admin.
<?php else: ?>
Giao dịch không thành công. Vui lòng thử lại hoặc liên hệ hỗ trợ.
<?php endif; ?>
</div>

<!-- Thông tin hóa đơn -->
<div class="bill-info">
<p><strong>Mã đơn:</strong> <?= $booking['id'] ?? $orderId ?></p>
<p><strong>Phòng:</strong> <?= $booking['room_type'] ?? '-' ?> (<?= $booking['room_number'] ?? '-' ?>)</p>
<p><strong>Check in:</strong> <?= $booking['check_in'] ?? '-' ?></p>
<p><strong>Check out:</strong> <?= $booking['check_out'] ?? '-' ?></p>
<p><strong>Trạng thái:</strong>
<span class="status status-<?= strtolower($booking['status'] ?? 'pending') ?>">
<?php
$status = $booking['status'] ?? BookingStatus::PENDING_PAYMENT;
if($status === BookingStatus::PENDING_PAYMENT) echo '<i class="fa-solid fa-clock"></i> Chờ thanh toán';
elseif($status === BookingStatus::CONFIRMED) echo '<i class="fa-solid fa-circle-check"></i> Đã xác nhận / Thanh toán thành công';
elseif($status === BookingStatus::CANCELLED) echo '<i class="fa-solid fa-xmark"></i> Hủy';
?>
</span>
</p>
</div>

<!-- Bảng chi tiết -->
<table class="bill-table">
<thead>
<tr>
<th>Dịch vụ</th>
<th>Số lượng</th>
<th>Giá</th>
<th>Tổng</th>
</tr>
</thead>
<tbody>
<tr>
<td>Phòng <?= $booking['room_type'] ?? '-' ?></td>
<td>1</td>
<td><?= number_format($booking['total_price'] ?? $amount) ?> VND</td>
<td><?= number_format($booking['total_price'] ?? $amount) ?> VND</td>
</tr>
</tbody>
</table>

<div class="bill-total">
<h3>Tổng tiền: <?= number_format($booking['total_price'] ?? $amount) ?> VND</h3>
</div>

<!-- Nút hành động -->
<div class="bill-action">
<?php if($status === BookingStatus::PENDING_PAYMENT): ?>
<a href="<?= \App\Core\url('/payment?booking_id=' . $booking['id']) ?>" class="btn btn-pay">
<i class="fa-solid fa-credit-card me-1"></i> Thanh toán VNPay
</a>
<?php elseif($status === BookingStatus::CONFIRMED): ?>
<span class="paid"><i class="fa-solid fa-circle-check"></i> Đã xác nhận (Thanh toán thành công)</span>
<?php elseif($status === BookingStatus::COMPLETED): ?>
<span class="paid"><i class="fa-solid fa-check-double"></i> Đã trả phòng</span>
<?php else: ?>
<span class="cancelled"><i class="fa-solid fa-xmark"></i> Giao dịch hủy</span>
<?php endif; ?>
<a href="<?= \App\Core\url('/') ?>" class="btn btn-home">Trang chủ</a>
</div>

</div>
</div>

</body>
</html>
