<?php
$booking = $booking ?? null;

if (!$booking) {
    die('KhÃ´ng tÃ¬m tháº¥y thÃ´ng tin Ä‘áº·t phÃ²ng');
}

$baseUrl = rtrim(APP_URL ?? '', '/');
$bookingId = (int) ($booking['id'] ?? 0);
$statusValue = strtolower((string) ($booking['status'] ?? 'processing'));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công - Booking Hotel</title>
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
                radial-gradient(circle at top left, rgba(16, 185, 129, 0.24), transparent 32%),
                radial-gradient(circle at bottom right, rgba(59, 130, 246, 0.22), transparent 30%),
                linear-gradient(135deg, #f0fdf4 0%, #ecfeff 50%, #eff6ff 100%);
        }

        .payment-success-card {
            width: 100%;
            max-width: 620px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 28px;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.14);
            overflow: hidden;
        }

        .hero {
            padding: 36px 32px 24px;
            text-align: center;
            background: linear-gradient(135deg, #10b981, #0f766e);
            color: #fff;
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
            border: 1px solid rgba(255, 255, 255, 0.28);
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
            color: rgba(255, 255, 255, 0.92);
        }

        .content {
            padding: 28px 32px 32px;
        }

        .booking-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-bottom: 26px;
        }

        .meta-item {
            padding: 16px 18px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .meta-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 8px;
        }

        .meta-value {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .meta-value.status {
            color: #0f766e;
        }

        .helper-text {
            margin-bottom: 26px;
            padding: 18px 20px;
            border-radius: 18px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            line-height: 1.6;
            font-size: 15px;
        }

        .action-list {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            flex: 1;
            min-width: 170px;
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
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 14px 24px rgba(16, 185, 129, 0.22);
        }

        .btn-secondary {
            color: #0f172a;
            background: #e2e8f0;
        }

        .btn-outline {
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

            .action-list {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="payment-success-card">
        <div class="hero">
            <div class="hero-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1>Bạn đã thanh toán thành công</h1>
            <p>Hệ thống đã ghi nhận giao dịch VNPay của bạn. Đơn đặt phòng đang được xử lý để hoàn tất xác nhận.</p>
        </div>

        <div class="content">
            <div class="booking-meta">
                <div class="meta-item">
                    <span class="meta-label">Mã đơn</span>
                    <div class="meta-value">#<?= $bookingId ?></div>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Trạng thái hiện tại</span>
                    <div class="meta-value status"><?= $statusValue === 'processing' ? 'Đang xử lý' : 'Đã ghi nhận thanh toán' ?></div>
                </div>
            </div>

            <div class="helper-text">
                Bạn có thể xem lại thông tin đơn đặt phòng ngay bây giờ hoặc quay về trang chủ để tiếp tục sử dụng hệ thống.
            </div>

            <div class="action-list">
                <a href="<?= $baseUrl ?>/bill?id=<?= $bookingId ?>" class="btn btn-primary">
                    <i class="fa-solid fa-receipt"></i>
                    Xem đơn
                </a>
                <a href="<?= $baseUrl ?>/bookings" class="btn btn-secondary">
                    <i class="fa-solid fa-calendar-check"></i>
                    Danh sách đơn
                </a>
                <a href="<?= $baseUrl ?>/" class="btn btn-outline">
                    <i class="fa-solid fa-house"></i>
                    Quay về trang chủ
                </a>
            </div>
        </div>
    </div>
</body>
</html>

