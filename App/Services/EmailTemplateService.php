<?php

namespace App\Services;

/**
 * EmailTemplateService
 * Generates HTML email templates for booking status changes and notifications
 */
class EmailTemplateService
{
    private $statusConfig;

    public function __construct()
    {
        $this->statusConfig = require BASE_PATH . '/Config/booking_status.php';
    }

    /**
     * Get HTML email template for booking status change
     * 
     * @param array $booking Booking details
     * @param string $oldStatus Previous status
     * @param string $newStatus New status
     * @return array ['subject' => string, 'body' => html]
     */
    public function getStatusChangeEmailTemplate($booking, $oldStatus, $newStatus)
    {
        $guestName = htmlspecialchars($booking['guest_name'] ?? 'Khách hàng');
        $bookingId = $booking['id'] ?? 'N/A';
        $bookingCode = $booking['booking_code'] ?? 'N/A';
        $oldStatusLabel = $this->statusConfig[strtolower($oldStatus)]['label'] ?? ucfirst($oldStatus);
        $newStatusLabel = $this->statusConfig[strtolower($newStatus)]['label'] ?? ucfirst($newStatus);

        $checkInDate = isset($booking['check_in'])
            ? date('d/m/Y', strtotime($booking['check_in']))
            : 'N/A';
        $checkOutDate = isset($booking['check_out'])
            ? date('d/m/Y', strtotime($booking['check_out']))
            : 'N/A';
        $totalPrice = isset($booking['total_price'])
            ? number_format($booking['total_price'], 0, ',', '.') . 'đ'
            : 'N/A';

        $subject = "Thông báo: Trạng thái đặt phòng #$bookingCode đã thay đổi";

        $body = $this->getEmailTemplate(
            title: "Cập nhật trạng thái đặt phòng",
            greeting: "Xin chào {$guestName},",
            body: $this->getStatusChangeBody(
                $guestName,
                $bookingCode,
                $bookingId,
                $oldStatusLabel,
                $newStatusLabel,
                $checkInDate,
                $checkOutDate,
                $totalPrice,
                $newStatus
            ),
            ctaButton: $this->getCtaForStatus($newStatus),
            footer: "Nếu có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi."
        );

        return [
            'subject' => $subject,
            'body' => $body
        ];
    }

    /**
     * Generate the status change body content
     */
    private function getStatusChangeBody(
        $guestName,
        $bookingCode,
        $bookingId,
        $oldStatusLabel,
        $newStatusLabel,
        $checkInDate,
        $checkOutDate,
        $totalPrice,
        $newStatus
    ) {
        $colorMap = [
            'pending' => '#FFC107',
            'processing' => '#17A2B8',
            'paid' => '#28A745',
            'confirmed' => '#28A745',
            'cancelled' => '#DC3545',
            'completed' => '#6C757D',
        ];
        $statusColor = $colorMap[strtolower($newStatus)] ?? '#007BFF';
        $statusMessage = $this->getStatusMessage($newStatus);

        return <<<HTML
<p style="font-size: 16px; color: #333; line-height: 1.6;">
    Trạng thái đặt phòng của bạn vừa được cập nhật.
</p>

<div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid {$statusColor}; margin: 20px 0;">
    <p style="margin: 0; font-size: 14px; color: #666;">
        <strong>Mã đặt phòng:</strong> <span style="font-family: monospace; color: #333;">{$bookingCode}</span>
    </p>
    <p style="margin: 5px 0; font-size: 14px; color: #666;">
        <strong>Trạng thái cũ:</strong> <span style="color: #666;">{$oldStatusLabel}</span>
    </p>
    <p style="margin: 5px 0; font-size: 14px; color: #666;">
        <strong>Trạng thái mới:</strong> <span style="color: {$statusColor}; font-weight: bold;">{$newStatusLabel}</span>
    </p>
</div>

<p style="font-size: 14px; color: #666; margin: 15px 0;">
    <strong>Chi tiết đặt phòng:</strong>
</p>
<ul style="font-size: 14px; color: #666; line-height: 1.8;">
    <li><strong>Check-in:</strong> {$checkInDate}</li>
    <li><strong>Check-out:</strong> {$checkOutDate}</li>
    <li><strong>Tổng giá:</strong> {$totalPrice}</li>
</ul>

<p style="font-size: 15px; color: #333; margin: 20px 0; line-height: 1.6;">
    {$statusMessage}
</p>
HTML;
    }

    /**
     * Get status-specific message
     */
    private function getStatusMessage($status)
    {
        return match (strtolower($status)) {
            'pending' => 'Đặt phòng của bạn đang chờ xử lý. Vui lòng hoàn tất thanh toán để xác nhận.',
            'processing' => 'Giao dịch thanh toán của bạn đang được xử lý. Chúng tôi sẽ cập nhật cho bạn sớm nhất.',
            'paid' => 'Thanh toán thành công! Đặt phòng của bạn sắp được xác nhận.',
            'confirmed' => 'Đặt phòng của bạn đã được xác nhận. Chúng tôi rất mong được chào đón bạn!',
            'cancelled' => 'Đặt phòng của bạn đã bị hủy. Nếu có câu hỏi, vui lòng liên hệ với chúng tôi.',
            'completed' => 'Cảm ơn bạn đã lưu trú tại chúng tôi. Chúng tôi mong được phục vụ bạn lần tới!',
            default => 'Trạng thái đặt phòng của bạn đã được cập nhật. Vui lòng kiểm tra chi tiết.'
        };
    }

    /**
     * Get CTA button for specific status
     */
    private function getCtaForStatus($status)
    {
        $status = strtolower($status);

        return match ($status) {
            'pending', 'processing' => [
                'text' => 'Hoàn tất thanh toán',
                'url' => '{booking_payment_url}',
                'color' => '#007BFF'
            ],
            'confirmed', 'paid' => [
                'text' => 'Xem chi tiết đặt phòng',
                'url' => '{booking_detail_url}',
                'color' => '#28A745'
            ],
            'completed' => [
                'text' => 'Viết đánh giá',
                'url' => '{booking_review_url}',
                'color' => '#17A2B8'
            ],
            default => null
        };
    }

    /**
     * Base email template wrapper
     */
    private function getEmailTemplate($title, $greeting, $body, $ctaButton = null, $footer = '')
    {
        $ctaHtml = '';
        if ($ctaButton) {
            $ctaHtml = <<<HTML
<div style="text-align: center; margin: 30px 0;">
    <a href="{$ctaButton['url']}" style="
        display: inline-block;
        padding: 12px 30px;
        background-color: {$ctaButton['color']};
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        font-size: 16px;
    ">
        {$ctaButton['text']}
    </a>
</div>
HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007BFF;
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border-left: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-left: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
            border-radius: 0 0 5px 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{$title}</h1>
        </div>
        <div class="content">
            <p style="font-size: 16px; margin-top: 0;">{$greeting}</p>
            {$body}
            {$ctaHtml}
        </div>
        <div class="footer">
            <p style="margin: 0 0 5px 0;">{$footer}</p>
            <p style="margin: 5px 0 0 0;">© 2026 Hotel Management System. Bảo lưu mọi quyền.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Create a simple text email (plain text fallback for emails)
     */
    public function getStatusChangeTextEmail($booking, $oldStatus, $newStatus)
    {
        $guestName = $booking['guest_name'] ?? 'Khách hàng';
        $bookingCode = $booking['booking_code'] ?? 'N/A';
        $oldStatusLabel = $this->statusConfig[strtolower($oldStatus)]['label'] ?? ucfirst($oldStatus);
        $newStatusLabel = $this->statusConfig[strtolower($newStatus)]['label'] ?? ucfirst($newStatus);

        return <<<TEXT
Xin chào $guestName,

Trạng thái đặt phòng của bạn vừa được cập nhật:

Mã đặt phòng: $bookingCode
Trạng thái cũ: $oldStatusLabel
Trạng thái mới: $newStatusLabel

Chi tiết đặt phòng:
- Check-in: {$booking['check_in']}
- Check-out: {$booking['check_out']}
- Tổng giá: {$booking['total_price']}

Nếu có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.

Cảm ơn bạn!
Hotel Management System
TEXT;
    }
}

