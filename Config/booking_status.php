<?php

/**
 * Booking Status Flow - Trạng thái đặt phòng (bảng bookings.status)
 * 6-state model aligned with BookingStatus.php constants
 * Flow: pending → processing/paid → confirmed → completed (or cancelled at any stage)
 */

return [
    'draft' => [
        'label' => 'Nháp',
        'label_en' => 'Draft',
        'description' => 'Booking được tạo tạm thời.',
        'color' => 'secondary',
    ],
    'pending_payment' => [
        'label' => 'Chờ thanh toán',
        'label_en' => 'Pending Payment',
        'description' => 'Booking đang chờ thanh toán xác nhận.',
        'color' => 'warning',
    ],
    'confirmed' => [
        'label' => 'Đã xác nhận',
        'label_en' => 'Confirmed',
        'description' => 'Booking đã được xác nhận và giữ phòng.',
        'color' => 'info',
    ],
    'checked_in' => [
        'label' => 'Đã check-in',
        'label_en' => 'Checked In',
        'description' => 'Khách đã nhận phòng.',
        'color' => 'primary',
    ],
    'checked_out' => [
        'label' => 'Đã check-out',
        'label_en' => 'Checked Out',
        'description' => 'Khách đã trả phòng chờ hoàn tất.',
        'color' => 'info',
    ],
    'completed' => [
        'label' => 'Hoàn thành',
        'label_en' => 'Completed',
        'description' => 'Booking đã hoàn tất nghiệp vụ.',
        'color' => 'success',
    ],
    'cancelled' => [
        'label' => 'Đã hủy',
        'label_en' => 'Cancelled',
        'description' => 'Booking đã bị hủy.',
        'color' => 'danger',
    ],
    'expired' => [
        'label' => 'Hết hạn',
        'label_en' => 'Expired',
        'description' => 'Booking quá hạn thanh toán.',
        'color' => 'dark',
    ],
    'no_show' => [
        'label' => 'Không đến',
        'label_en' => 'No Show',
        'description' => 'Khách không đến nhận phòng.',
        'color' => 'secondary',
    ],

    // Luồng chuyển trạng thái hợp lệ
    'transitions' => [
        'draft' => ['pending_payment', 'confirmed', 'cancelled', 'expired'],
        'pending_payment' => ['confirmed', 'cancelled', 'expired'],
        'confirmed' => ['checked_in', 'cancelled', 'no_show'],
        'checked_in' => ['checked_out', 'cancelled'],
        'checked_out' => ['completed'],
        'completed' => [],
        'cancelled' => [],
        'expired' => [],
        'no_show' => [],
    ],

    // Trạng thái được coi là "giữ phòng" (tính overlap, không cho đặt trùng)
    'blocks_room' => ['pending_payment', 'confirmed', 'checked_in', 'checked_out'],

    // Trạng thái có thể hủy được
    'cancellable' => ['draft', 'pending_payment', 'confirmed', 'checked_in'],

    // Trạng thái mặc định khi tạo booking mới
    'default' => 'pending_payment',
];

