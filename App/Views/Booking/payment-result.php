<?php
$booking = $booking ?? null;
$paymentStatus = strtolower((string) ($paymentStatus ?? 'error'));
$paymentMessage = $paymentMessage ?? '';

if (!$booking) {
    die('KhÃ´ng tÃ¬m tháº¥y thÃ´ng tin Ä‘áº·t phÃ²ng');
}

$baseUrl = rtrim(APP_URL ?? '', '/');
$bookingId = (int) ($booking['id'] ?? 0);
$isSuccess = $paymentStatus === 'success';
$title = $isSuccess ? 'Thanh toán thành công' : 'Thanh toán thất bại';
$description = $isSuccess
    ? 'Giao dịch thanh toán của bạn đã được ghi nhận. Bạn có thể xem lại đơn đặt phòng hoặc quay về trang chủ.'
    : ($paymentMessage !== '' ? $paymentMessage : 'Giao dịch chưa hoàn tất. Vui lòng kiểm tra lại và thử thanh toán sau.');
$heroBackground = $isSuccess
    ? 'linear-gradient(135deg, #10b981, #0f766e)'
    : 'linear-gradient(135deg, #ef4444, #b91c1c)';
$iconClass = $isSuccess ? 'fa-circle-check' : 'fa-circle-xmark';
$panelBackground = $isSuccess ? '#f0fdf4' : '#fef2f2';
$panelBorder = $isSuccess ? '#bbf7d0' : '#fecaca';
$panelText = $isSuccess ? '#166534' : '#991b1b';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Booking Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.16), transparent 30%),
                radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.14), transparent 26%),
                linear-gradient(135deg, #eef2ff 0%, #f8fafc 50%, #eff6ff 100%);
        }

        .payment-result-card {
            width: 100%;
            max-width: 620px;
            background: rgba(255, 255, 255, 0.97);
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.14);
        }

        .hero {
            padding: 36px 32px 24px;
            text-align: center;
            color: #fff;
            background: <?= $heroBackground ?>;
        }

        .hero-icon {
            width: 88px;
            height: 88px;
            margin: 0 auto 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .hero-icon i {
            font-size: 42px;
        }

        .hero h1 {
            font-size: 30px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 15px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.94);
        }

        .content {
            padding: 28px 32px 32px;
        }

        .booking-box {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .booking-item {
            padding: 16px 18px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .booking-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 8px;
        }

        .booking-value {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .message-box {
            margin-bottom: 24px;
            padding: 18px 20px;
            border-radius: 18px;
            background: <?= $panelBackground ?>;
            border: 1px solid <?= $panelBorder ?>;
            color: <?= $panelText ?>;
            line-height: 1.6;
            font-size: 15px;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            flex: 1;
            min-width: 180px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 22px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 14px 24px rgba(37, 99, 235, 0.2);
        }

        .btn-secondary {
            color: #334155;
            background: #fff;
            border: 1px solid #cbd5e1;
        }

        @media (max-width: 640px) {
            .hero, .content {
                padding-left: 22px;
                padding-right: 22px;
            }

            .hero h1 {
                font-size: 26px;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="payment-result-card">
        <div class="hero">
            <div class="hero-icon">
                <i class="fa-solid <?= $iconClass ?>"></i>
            </div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p><?= htmlspecialchars($description) ?></p>
        </div>

        <div class="content">
            <div class="booking-box">
                <div class="booking-item">
                    <span class="booking-label">Mã đơn</span>
                    <div class="booking-value">#<?= $bookingId ?></div>
                </div>
                <div class="booking-item">
                    <span class="booking-label">Kết quả</span>
                    <div class="booking-value"><?= $isSuccess ? 'Đã ghi nhận thanh toán' : 'Chưa thanh toán' ?></div>
                </div>
            </div>

            <div class="message-box">
                <?= htmlspecialchars($paymentMessage !== '' ? $paymentMessage : $description) ?>
            </div>

            <div class="actions">
                <a href="<?= $baseUrl ?>/bill?id=<?= $bookingId ?>" class="btn btn-primary">
                    <i class="fa-solid fa-receipt"></i>
                    Xem đơn
                </a>
                <a href="<?= $baseUrl ?>/" class="btn btn-secondary">
                    <i class="fa-solid fa-house"></i>
                    Quay về trang chủ
                </a>
            </div>
        </div>
    </div>
</body>
</html>

