<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class PdfInvoiceService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function streamByBookingId(int $bookingId): void
    {
        $data = $this->getBookingData($bookingId);
        $html = $this->renderHtml($data['invoice'], $data['details']);
        $this->generatePdf($html, 'invoice-room-' . $bookingId . '.pdf');
    }

    public function renderByBookingId(int $bookingId): string
    {
        $data = $this->getBookingData($bookingId);
        return $this->renderHtml($data['invoice'], $data['details']);
    }

    public function streamByServiceBookingId(int $serviceBookingId): void
    {
        $booking = $this->getServiceBookingData($serviceBookingId);
        $html = $this->renderServiceHtml($booking);
        $this->generatePdf($html, 'invoice-service-' . $serviceBookingId . '.pdf');
    }

    public function renderByServiceBookingId(int $serviceBookingId): string
    {
        $booking = $this->getServiceBookingData($serviceBookingId);
        return $this->renderServiceHtml($booking);
    }

    private function getBookingData(int $bookingId): array
    {
        // Try to get formal invoice first
        $stmt = $this->pdo->prepare("
            SELECT i.*, b.booking_code, b.user_id, u.full_name, u.email
            FROM invoices i
            JOIN bookings b ON b.id = i.booking_id
            LEFT JOIN users u ON u.id = b.user_id
            WHERE i.booking_id = ?
            ORDER BY i.id DESC
            LIMIT 1
        ");
        $stmt->execute([$bookingId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        $details = $this->loadDetails($bookingId);

        if (!$invoice) {
            // Fallback: Generate Draft Invoice (Bill) from booking data
            $stmtBooking = $this->pdo->prepare("
                SELECT b.id as booking_id, b.booking_code, b.total_initial_price as final_total, 
                       u.full_name, u.email, b.status as booking_status
                FROM bookings b
                LEFT JOIN users u ON u.id = b.user_id
                WHERE b.id = ?
            ");
            $stmtBooking->execute([$bookingId]);
            $booking = $stmtBooking->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                throw new \RuntimeException('Booking not found');
            }

            $invoice = [
                'id' => 0, // 0 indicates draft/not saved
                'booking_code' => $booking['booking_code'],
                'full_name' => $booking['full_name'],
                'email' => $booking['email'],
                'status' => 'DRAFT/UNPAID',
                'final_total' => $booking['final_total'],
                'total_room_amount' => array_sum(array_column($details['rooms'], 'price_per_night')), 
                'total_service_amount' => array_sum(array_column($details['services'], 'total_amount')),
                'discount_amount' => 0
            ];
        }

        return compact('invoice', 'details');
    }

    private function getServiceBookingData(int $serviceBookingId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT sb.*, u.full_name, u.email, s.name as service_name, s.price_per_hour
            FROM service_bookings sb
            LEFT JOIN users u ON u.id = sb.user_id
            LEFT JOIN services s ON s.id = sb.service_id
            WHERE sb.id = ?
        ");
        $stmt->execute([$serviceBookingId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            throw new \RuntimeException('Service booking not found');
        }

        return $booking;
    }

    private function generatePdf(string $html, string $filename): void
    {
        if (class_exists('\Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream($filename, ['Attachment' => false]);
            return;
        }

        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }

    private function loadDetails(int $bookingId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT bd.*, r.room_number, rt.name AS room_type_name,
                   bd.check_in_date, bd.check_out_date
            FROM booking_details bd
            JOIN rooms r ON r.id = bd.room_id
            JOIN room_types rt ON rt.id = r.room_type_id
            WHERE bd.booking_id = ?
        ");
        $stmt->execute([$bookingId]);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $stmtSrv = $this->pdo->prepare("
            SELECT os.*, GROUP_CONCAT(CONCAT(s.name, ' x', d.quantity) SEPARATOR ', ') AS service_summary
            FROM order_services os
            JOIN booking_details bd ON bd.id = os.booking_detail_id
            LEFT JOIN order_service_details d ON d.order_service_id = os.id
            LEFT JOIN services s ON s.id = d.service_id
            WHERE bd.booking_id = ?
            GROUP BY os.id
        ");
        $stmtSrv->execute([$bookingId]);
        $services = $stmtSrv->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $stmtDam = $this->pdo->prepare("SELECT * FROM inventory_damage WHERE booking_id = ? AND status = 'Approved'");
        $stmtDam->execute([$bookingId]);
        $damages = $stmtDam->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return compact('rooms', 'services', 'damages');
    }

    private function renderHtml(array $invoice, array $details): string
    {
        $fmt = static fn($v) => number_format((float)$v, 0, ',', '.') . '₫';
        $rooms = $details['rooms'] ?? [];
        $services = $details['services'] ?? [];
        $damages = $details['damages'] ?? [];

        ob_start();
        ?>
        <html>
        <head>
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
                .header { border-bottom: 2px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
                .logo { font-size: 24px; font-weight: bold; color: #4f46e5; }
                .invoice-info { text-align: right; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; text-align: left; }
                td { border: 1px solid #e2e8f0; padding: 10px; }
                .total-box { width: 300px; margin-left: auto; }
                .total-row { display: flex; justify-content: space-between; padding: 5px 0; }
                .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #64748b; }
                .signature-space { margin-top: 40px; }
            </style>
        </head>
        <body>
            <div class="header">
                <table width="100%" style="border:none;">
                    <tr>
                        <td style="border:none;width:50%;">
                            <div class="logo">BOOKING HOTEL</div>
                            <div style="font-size:10px;color:#64748b;">Luxury Experience & Professional Service</div>
                            <div style="margin-top:10px;">Địa chỉ: 123 Luxury St, Hà Nội, Việt Nam</div>
                            <div>SĐT: +84 123 456 789 | Email: contact@bookinghotel.com</div>
                        </td>
                        <td style="border:none;width:50%;text-align:right;">
                            <h2 style="margin:0;color:#1e293b;">HÓA ĐƠN DỊCH VỤ</h2>
                            <div>Mã đơn: <strong>#<?= htmlspecialchars((string)($invoice['booking_code'] ?? 'N/A')) ?></strong></div>
                            <div>Ngày xuất: <?= date('d/m/Y') ?></div>
                            <div>Trạng thái: <?= strtoupper(htmlspecialchars((string)($invoice['status'] ?? 'UNPAID'))) ?></div>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 30px;">
                <h4 style="border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px;">THÔNG TIN KHÁCH HÀNG</h4>
                <div>Họ và tên: <strong><?= htmlspecialchars((string)($invoice['full_name'] ?? 'Guest')) ?></strong></div>
                <div>Email: <?= htmlspecialchars((string)($invoice['email'] ?? 'N/A')) ?></div>
            </div>

            <h3>1. Chi tiết lưu trú</h3>
            <table>
                <thead>
                    <tr>
                        <th>Phòng</th>
                        <th>Hạng phòng</th>
                        <th>Nhận phòng</th>
                        <th>Trả phòng</th>
                        <th style="text-align:right;">Đơn giá</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $r): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars((string)($r['room_number'] ?? '')) ?></strong></td>
                        <td><?= htmlspecialchars((string)($r['room_type_name'] ?? '')) ?></td>
                        <td><?= date('d/m/Y', strtotime((string)$r['check_in_date'])) ?></td>
                        <td><?= date('d/m/Y', strtotime((string)$r['check_out_date'])) ?></td>
                        <td style="text-align:right;"><?= $fmt($r['price_per_night'] ?? 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (!empty($services)): ?>
            <h3>2. Dịch vụ & Phụ thu</h3>
            <table>
                <thead>
                    <tr>
                        <th>Mô tả dịch vụ</th>
                        <th style="text-align:right;">Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)($s['service_summary'] ?? 'Dịch vụ lẻ')) ?></td>
                        <td style="text-align:right;"><?= $fmt($s['total_amount'] ?? 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <?php if (!empty($damages)): ?>
            <h3>3. Bồi thường hư hỏng</h3>
            <table>
                <thead>
                    <tr>
                        <th>Hạng mục</th>
                        <th>Số lượng</th>
                        <th style="text-align:right;">Chi phí</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($damages as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)($d['item_name'] ?? 'Hư hại vật tư')) ?></td>
                        <td style="text-align:center;"><?= (int)$d['quantity'] ?></td>
                        <td style="text-align:right;"><?= $fmt($d['penalty_amount'] ?? 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <div class="total-box">
                <table style="border:none;">
                    <tr>
                        <td style="border:none;">Tiền phòng:</td>
                        <td style="border:none;text-align:right;"><?= $fmt($invoice['total_room_amount'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td style="border:none;">Dịch vụ/Phụ thu:</td>
                        <td style="border:none;text-align:right;"><?= $fmt($invoice['total_service_amount'] ?? 0) ?></td>
                    </tr>
                    <?php if (!empty($invoice['damage_fee'])): ?>
                    <tr>
                        <td style="border:none;">Phí bồi thường:</td>
                        <td style="border:none;text-align:right;"><?= $fmt($invoice['damage_fee']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="border:none; border-top: 1px solid #e2e8f0; font-weight:bold; font-size:16px;">TỔNG CỘNG:</td>
                        <td style="border:none; border-top: 1px solid #e2e8f0; font-weight:bold; font-size:16px; text-align:right; color:#4f46e5;">
                            <?= $fmt($invoice['final_total'] ?? 0) ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="signature-space">
                <table width="100%" style="border:none;">
                    <tr>
                        <td style="border:none;text-align:center;width:50%;">
                            <strong>KHÁCH HÀNG</strong><br>
                            <span style="font-size:9px;">(Ký và ghi rõ họ tên)</span>
                        </td>
                        <td style="border:none;text-align:center;width:50%;">
                            <strong>NHÂN VIÊN XÁC NHẬN</strong><br>
                            <span style="font-size:9px;">(Ký và đóng dấu)</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                Cảm ơn Quý khách đã tin tưởng và sử dụng dịch vụ tại Booking Hotel!<br>
                Hẹn gặp lại Quý khách trong những chuyến hành trình tiếp theo.
            </div>
        </body>
        </html>
        <?php
        return (string)ob_get_clean();
    }

    private function renderServiceHtml(array $booking): string
    {
        $fmt = static fn($v) => number_format((float)$v, 0, ',', '.') . '₫';
        ob_start();
        ?>
        <html>
        <head>
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
                .header { border-bottom: 2px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
                .logo { font-size: 24px; font-weight: bold; color: #4f46e5; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; text-align: left; }
                td { border: 1px solid #e2e8f0; padding: 10px; }
                .total-row { float: right; width: 300px; margin-top: 20px; }
                .footer { margin-top: 100px; text-align: center; font-size: 10px; color: #64748b; clear: both; }
            </style>
        </head>
        <body>
            <div class="header">
                <table width="100%" style="border:none;">
                    <tr>
                        <td style="border:none;width:50%;">
                            <div class="logo">BOOKING HOTEL</div>
                            <div style="font-size:10px;color:#64748b;">Services & Events Dispatch</div>
                        </td>
                        <td style="border:none;width:50%;text-align:right;">
                            <h2 style="margin:0;color:#1e293b;">HÓA ĐƠN DỊCH VỤ LẺ</h2>
                            <div>Mã SVC: <strong>#SVC-<?= (int)$booking['id'] ?></strong></div>
                            <div>Ngày: <?= date('d/m/Y') ?></div>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 30px;">
                <h4 style="border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px;">THÔNG TIN KHÁCH HÀNG</h4>
                <div>Họ và tên: <strong><?= htmlspecialchars((string)($booking['full_name'] ?? 'Guest')) ?></strong></div>
                <div>Email: <?= htmlspecialchars((string)($booking['email'] ?? 'N/A')) ?></div>
            </div>

            <h3>Chi tiết dịch vụ</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tên dịch vụ</th>
                        <th>Thời gian</th>
                        <th style="text-align:center;">Số lượng</th>
                        <th style="text-align:right;">Đơn giá</th>
                        <th style="text-align:right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong><?= htmlspecialchars((string)($booking['service_name'] ?? '')) ?></strong></td>
                        <td>
                            Từ: <?= date('d/m/Y H:i', strtotime((string)$booking['start_time'])) ?><br>
                            Đến: <?= date('d/m/Y H:i', strtotime((string)$booking['end_time'])) ?>
                        </td>
                        <td style="text-align:center;"><?= (int)$booking['quantity'] ?></td>
                        <td style="text-align:right;"><?= $fmt($booking['price_per_hour'] ?? 0) ?></td>
                        <td style="text-align:right;"><?= $fmt($booking['total_price'] ?? 0) ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="total-row">
                <table style="border:none;">
                    <tr>
                        <td style="border:none; font-weight:bold; font-size:16px;">TỔNG THANH TOÁN:</td>
                        <td style="border:none; font-weight:bold; font-size:16px; text-align:right; color:#4f46e5;">
                            <?= $fmt($booking['total_price'] ?? 0) ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                Mọi thắc mắc về hóa đơn xin vui lòng liên hệ bộ phận chăm sóc khách hàng.<br>
                Trân trọng!
            </div>
        </body>
        </html>
        <?php
        return (string)ob_get_clean();
    }
}

