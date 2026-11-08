<?php
$booking = $booking ?? null;
$baseUrl = rtrim(APP_URL ?? '', '/');

if (!$booking) {
    die('Không tìm thấy thông tin đặt phòng');
}

// Lấy thông tin booking details
$bookingDetails = [];
try {
    $sql = "SELECT bd.*, r.room_number, r.floor, rt.name as room_type_name, 
                   GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') as amenities
            FROM booking_details bd
            LEFT JOIN rooms r ON bd.room_id = r.id
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            LEFT JOIN room_type_amenities rta ON rta.room_type_id = rt.id
            LEFT JOIN amenities a ON rta.amenity_id = a.id
            WHERE bd.booking_id = ?
            GROUP BY bd.id";
            
    $stmt = $bookingModel->getPdo()->prepare($sql);
    $stmt->execute([$booking['id']]);
    $bookingDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Lỗi truy vấn: " . $e->getMessage();
    $bookingDetails = [];
}

// Tính số đêm
$checkIn = strtotime($booking['check_in']);
$checkOut = strtotime($booking['check_out']);
$nights = ($checkOut > $checkIn) ? ceil(($checkOut - $checkIn) / (60 * 60 * 24)) : 1;
$totalPrice = $booking['total_price'] ?? 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - Booking Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .payment-container { max-width: 1200px; margin: 0 auto; }
        .payment-header { text-align: center; margin-bottom: 30px; color: white; }
        .payment-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 30px; }
        .booking-info-panel, .payment-summary { background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .panel-header { background: linear-gradient(135deg, #1e293b, #0f172a); padding: 20px 24px; color: white; }
        .panel-content { padding: 24px; }
        .room-item { border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px; margin-bottom: 16px; }
        .room-name { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
        .room-detail { font-size: 13px; color: #64748b; margin-bottom: 5px; }
        .room-detail i { color: #3b82f6; width: 18px; }
        .amenity-tag { background: #f1f5f9; padding: 4px 10px; border-radius: 20px; font-size: 11px; margin-right: 5px; }
        .customer-info { background: #f8fafc; border-radius: 16px; padding: 20px; margin-bottom: 24px; }
        .info-row { display: flex; margin-bottom: 10px; font-size: 14px; }
        .info-label { width: 120px; color: #64748b; }
        .payment-methods { margin-top: 25px; }
        .method-item { border: 2px solid #e2e8f0; border-radius: 16px; padding: 15px; margin-bottom: 10px; cursor: pointer; display: flex; align-items: center; gap: 15px; transition: 0.3s; }
        .method-item.selected { border-color: #3b82f6; background: #eff6ff; }
        .summary-row { display: flex; justify-content: space-between; padding: 10px 0; }
        .summary-row.total { border-top: 2px solid #e2e8f0; margin-top: 10px; padding-top: 15px; font-weight: 800; font-size: 20px; color: #059669; }
        .btn-payment { width: 100%; background: #10b981; color: white; border: none; padding: 16px; border-radius: 40px; font-weight: 700; cursor: pointer; }
        .btn-back { display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h1><i class="fa-solid fa-credit-card"></i> Thông tin thanh toán</h1>
            <p>Mã đơn hàng: #BK-<?= $booking['id'] ?></p>
        </div>

        <div class="payment-grid">
            <div class="booking-info-panel">
                <div class="panel-header">
                    <h2><i class="fa-solid fa-bed"></i> Danh sách phòng</h2>
                </div>
                <div class="panel-content">
                    <?php if (empty($bookingDetails)): ?>
                        <p style="color: #94a3b8; text-align: center;">Không có chi tiết phòng.</p>
                    <?php else: ?>
                        <?php foreach ($bookingDetails as $detail): ?>
                        <div class="room-item">
                            <div class="room-name"><?= htmlspecialchars($detail['room_type_name'] ?? 'Phòng tiêu chuẩn') ?></div>
                            <div class="room-detail"><i class="fa-solid fa-door-open"></i> Số phòng: <?= $detail['room_number'] ?></div>
                            <div class="room-detail"><i class="fa-solid fa-calendar-days"></i> <?= date('d/m/Y', $checkIn) ?> - <?= date('d/m/Y', $checkOut) ?></div>
                            
                            <?php if (!empty($detail['amenities'])): ?>
                            <div style="margin-top: 10px;">
                                <?php foreach (explode(', ', $detail['amenities']) as $am): ?>
                                    <span class="amenity-tag"><?= htmlspecialchars($am) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="customer-info">
                        <h3>Thông tin khách hàng</h3>
                        <div class="info-row"><div class="info-label">Người đặt:</div><div class="info-value"><?= htmlspecialchars($booking['guest_name']) ?></div></div>
                        <div class="info-row"><div class="info-label">Số điện thoại:</div><div class="info-value"><?= htmlspecialchars($booking['guest_phone']) ?></div></div>
                    </div>
                    
                    <div class="payment-methods">
                        <h3>Phương thức thanh toán</h3>
                        <div class="method-item selected" onclick="selectMethod('cod')">
                            <input type="radio" name="pay_m" value="cod" checked>
                            <div class="method-info"><strong>Tại quầy</strong><br><small>Thanh toán khi nhận phòng</small></div>
                        </div>
                        <div class="method-item" onclick="selectMethod('vnpay')">
                            <input type="radio" name="pay_m" value="vnpay">
                            <div class="method-info"><strong>VNPay</strong><br><small>Cổng thanh toán ngân hàng</small></div>
                        </div>
                        <div class="method-item" onclick="selectMethod('momo')">
                            <input type="radio" name="pay_m" value="momo">
                            <div class="method-info"><strong>Ví điện tử MoMo</strong><br><small>Thanh toán nhanh qua QR Code</small></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="payment-summary">
                <div class="panel-header"><h2><i class="fa-solid fa-receipt"></i> Tạm tính</h2></div>
                <div class="panel-content">
                    <div class="summary-row"><span>Số đêm:</span><span><?= $nights ?> đêm</span></div>
                    <div class="summary-row"><span>Giá phòng:</span><span><?= number_format($totalPrice) ?> ₫</span></div>
                    <div class="summary-row total"><span>Tổng thanh toán:</span><span><?= number_format($totalPrice) ?> ₫</span></div>
                    
                    <button class="btn-payment" onclick="processPayment()">XÁC NHẬN THANH TOÁN</button>
                    <a href="<?= \App\Core\url('bookings') ?>" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentMethod = 'cod';
        function selectMethod(m) {
            currentMethod = m;
            document.querySelectorAll('.method-item').forEach(el => el.classList.remove('selected'));
            event.currentTarget.classList.add('selected');
            event.currentTarget.querySelector('input').checked = true;
        }

        function processPayment() {
            if (currentMethod === 'cod') {
                if(confirm('Xác nhận đặt phòng và thanh toán tại khách sạn?')) {
                    window.location.href = '<?= \App\Core\url('booking/success') ?>?id=<?= $booking['id'] ?>';
                }
            } else {
                window.location.href = '<?= \App\Core\url('payment') ?>/' + currentMethod + '?booking_id=<?= $booking['id'] ?>';
            }
        }
    </script>
</body>
</html>

