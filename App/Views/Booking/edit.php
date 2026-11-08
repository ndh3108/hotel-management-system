<?php
$booking = $booking ?? null;
$room = $room ?? null;
$roomType = $roomType ?? null;
$baseUrl = rtrim(APP_URL ?? '', '/');

if (!$booking) {
    die('Không tìm thấy thông tin đặt phòng');
}

$nights = ceil((strtotime($booking['check_out']) - strtotime($booking['check_in'])) / (60 * 60 * 24));
$totalPrice = $booking['total_price'] ?? 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa đặt phòng - #<?= $booking['id'] ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding:  40px 40px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .edit-card {
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
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

        .edit-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 30px 40px;
            color: white;
        }

        .edit-header h1 {
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .edit-header h1 i {
            color: #f59e0b;
        }

        .edit-header p {
            opacity: 0.8;
            margin-top: 8px;
        }

        .edit-content {
            padding: 40px;
        }

        .room-info {
            background: #f8fafc;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }

        .room-info h3 {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .room-info .room-number {
            color: #64748b;
            margin-bottom: 16px;
        }

        .room-price {
            font-size: 24px;
            font-weight: 800;
            color: #f59e0b;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }

        .summary {
            background: #f0fdf4;
            border-radius: 20px;
            padding: 20px;
            margin: 30px 0;
            border: 1px solid #86efac;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }

        .summary-row.total {
            border-top: 2px solid #86efac;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 18px;
            font-weight: 800;
            color: #059669;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59,130,246,0.3);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            margin-top: 15px;
            width: 100%;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .error-message, .success-message {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .error-message {
            background: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .success-message {
            background: #d1fae5;
            color: #059669;
            border-left: 4px solid #059669;
        }

        @media (max-width: 768px) {
            .edit-header, .edit-content {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="edit-card">
            <div class="edit-header">
                <h1>
                    <i class="fa-solid fa-pen"></i>
                    Chỉnh sửa đặt phòng
                </h1>
                <p>Mã đơn: #<?= $booking['id'] ?></p>
            </div>

            <div class="edit-content">
                <div class="room-info">
                    <h3><?= htmlspecialchars($roomType['name'] ?? 'Phòng') ?></h3>
                    <div class="room-number">
                        <i class="fa-solid fa-door-open"></i> Phòng: <?= htmlspecialchars($room['room_number'] ?? '-') ?>
                    </div>
                    <div class="room-price">
                        <?= number_format($roomType['base_price'] ?? 0) ?> VND / đêm
                    </div>
                </div>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="error-message">
                        <i class="fa-solid fa-circle-exclamation"></i> <?= $_SESSION['error'] ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="success-message">
                        <i class="fa-solid fa-circle-check"></i> <?= $_SESSION['success'] ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <form method="POST" action="<?= $baseUrl ?>/booking/update">
                    <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                    
                    <div class="form-group">
                        <label><i class="fa-regular fa-calendar-check"></i> Ngày nhận phòng</label>
                        <input type="date" name="check_in" id="check_in" 
                               value="<?= date('Y-m-d', strtotime($booking['check_in'])) ?>" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fa-regular fa-calendar-xmark"></i> Ngày trả phòng</label>
                        <input type="date" name="check_out" id="check_out" 
                               value="<?= date('Y-m-d', strtotime($booking['check_out'])) ?>" required>
                    </div>

                    <div class="summary">
                        <div class="summary-row">
                            <span><i class="fa-solid fa-moon"></i> Số đêm</span>
                            <span id="nights"><?= $nights ?> đêm</span>
                        </div>
                        <div class="summary-row">
                            <span><i class="fa-solid fa-bed"></i> Đơn giá</span>
                            <span><?= number_format($roomType['base_price'] ?? 0) ?> VND / đêm</span>
                        </div>
                        <div class="summary-row total">
                            <span><i class="fa-solid fa-receipt"></i> Tổng tiền</span>
                            <span id="total-price"><?= number_format($totalPrice) ?> VND</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                    </button>

                    <a href="<?= $baseUrl ?>/bill?id=<?= $booking['id'] ?>" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Quay lại
                    </a>
                </form>
            </div>
        </div>
    </div>

    <script>
        const pricePerNight = <?= $roomType['base_price'] ?? 0 ?>;
        
        function updateTotal() {
            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;
            
            if (checkIn && checkOut) {
                const nights = Math.max(1, Math.ceil((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24)));
                const total = pricePerNight * nights;
                
                document.getElementById('nights').textContent = nights + ' đêm';
                document.getElementById('total-price').textContent = total.toLocaleString('vi-VN') + ' VND';
            }
        }
        
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('check_in').min = today;
        document.getElementById('check_out').min = today;
        
        document.getElementById('check_in').addEventListener('change', function() {
            document.getElementById('check_out').min = this.value;
            updateTotal();
        });
        
        document.getElementById('check_out').addEventListener('change', updateTotal);
    </script>
</body>
</html>
