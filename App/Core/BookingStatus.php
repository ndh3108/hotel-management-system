<?php
// App/Core/BookingStatus.php

namespace App\Core;

class BookingStatus
{
    // Booking lifecycle
    public const DRAFT = 'draft';
    public const PENDING_PAYMENT = 'pending_payment';
    public const CONFIRMED = 'confirmed';
    public const CHECKED_IN = 'checked_in';
    public const CHECKED_OUT = 'checked_out';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';
    public const EXPIRED = 'expired';
    public const NO_SHOW = 'no_show';

    /**
     * Lấy trạng thái mặc định khi tạo booking mới.
     */
    public static function defaultStatus()
    {
        return self::CONFIRMED;
    }

    /**
     * Lấy danh sách trạng thái chặn phòng (không cho phép đặt lại).
     */
    public static function statusesThatBlockRoom()
    {
        return [
            self::PENDING_PAYMENT,
            self::CONFIRMED,
            self::CHECKED_IN,
            self::CHECKED_OUT,
        ];
    }
    
    /**
     * Lấy danh sách trạng thái có thể hủy
     */
    public static function cancellableStatuses()
    {
        return [
            self::DRAFT,
            self::PENDING_PAYMENT,
            self::CONFIRMED,
            self::CHECKED_IN,
        ];
    }
    
    /**
     * Lấy danh sách trạng thái đã hoàn thành (không còn hiệu lực)
     */
    public static function completedStatuses()
    {
        return [
            self::CANCELLED,
            self::EXPIRED,
            self::NO_SHOW,
            self::COMPLETED,
        ];
    }
    
    /**
     * Lấy danh sách trạng thái đã xác nhận thành công
     */
    public static function confirmedStatuses()
    {
        return [
            self::CONFIRMED,
            self::CHECKED_IN,
            self::CHECKED_OUT,
            self::COMPLETED,
        ];
    }
    
    /**
     * Lấy tên hiển thị của trạng thái
     */
    public static function label($status)
    {
        $labels = [
            self::DRAFT => 'Nháp',
            self::PENDING_PAYMENT => 'Chờ thanh toán',
            self::CONFIRMED => 'Đã xác nhận',
            self::CHECKED_IN => 'Đã check-in',
            self::CHECKED_OUT => 'Đã check-out',
            self::CANCELLED => 'Đã hủy',
            self::EXPIRED => 'Hết hạn',
            self::NO_SHOW => 'Không đến',
            self::COMPLETED => 'Hoàn thành',
        ];
        
        return $labels[$status] ?? 'Không xác định';
    }
    
    /**
     * Lấy màu sắc cho trạng thái (dùng cho CSS)
     */
    public static function color($status)
    {
        $colors = [
            self::DRAFT => 'secondary',
            self::PENDING_PAYMENT => 'warning',
            self::CONFIRMED => 'info',
            self::CHECKED_IN => 'primary',
            self::CHECKED_OUT => 'info',
            self::CANCELLED => 'danger',
            self::EXPIRED => 'dark',
            self::NO_SHOW => 'secondary',
            self::COMPLETED => 'success',
        ];
        
        return $colors[$status] ?? 'secondary';
    }
    
    /**
     * Kiểm tra trạng thái có hợp lệ không
     */
    public static function isValid($status)
    {
        return in_array($status, [
            self::DRAFT,
            self::PENDING_PAYMENT,
            self::CONFIRMED,
            self::CHECKED_IN,
            self::CHECKED_OUT,
            self::CANCELLED,
            self::EXPIRED,
            self::NO_SHOW,
            self::COMPLETED,
        ]);
    }
    
    /**
     * Lấy danh sách tất cả trạng thái
     */
    public static function all()
    {
        return [
            self::DRAFT => self::label(self::DRAFT),
            self::PENDING_PAYMENT => self::label(self::PENDING_PAYMENT),
            self::CONFIRMED => self::label(self::CONFIRMED),
            self::CHECKED_IN => self::label(self::CHECKED_IN),
            self::CHECKED_OUT => self::label(self::CHECKED_OUT),
            self::CANCELLED => self::label(self::CANCELLED),
            self::EXPIRED => self::label(self::EXPIRED),
            self::NO_SHOW => self::label(self::NO_SHOW),
            self::COMPLETED => self::label(self::COMPLETED)
        ];
    }
    
    /**
     * Lấy các trạng thái có thể thanh toán
     */
    public static function payableStatuses()
    {
        return [
            self::DRAFT,
            self::PENDING_PAYMENT,
            self::CONFIRMED,
        ];
    }
    
    /**
     * Kiểm tra trạng thái có thể thanh toán không
     */
    public static function isPayable($status)
    {
        return \in_array(strtolower((string) $status), self::payableStatuses(), true);
    }

    /**
     * Kiểm tra trạng thái đang chờ người dùng thanh toán.
     */
    public static function isAwaitingPayment($status)
    {
        return self::isPayable($status);
    }
    
    /**
     * Kiểm tra trạng thái có đang chiếm phòng không
     */
    public static function isOccupyingRoom($status)
    {
        return \in_array($status, self::statusesThatBlockRoom());
    }
    
    /**
     * Kiểm tra trạng thái có thể chỉnh sửa không
     */
    public static function isEditable($status)
    {
        return \in_array($status, [
            self::DRAFT,
            self::PENDING_PAYMENT,
            self::CONFIRMED
        ]);
    }
    
    /**
     * Kiểm tra trạng thái có thể hủy không
     */
    public static function isCancellable($status)
    {
        return \in_array($status, self::cancellableStatuses());
    }

    public static function canTransitionTo($currentStatus, $newStatus)
    {
        $current = strtolower($currentStatus);
        $new = strtolower($newStatus);
        
        // Định nghĩa các chuyển trạng thái hợp lệ
        $allowedTransitions = [
            self::DRAFT => [self::PENDING_PAYMENT, self::CONFIRMED, self::CANCELLED, self::EXPIRED],
            self::PENDING_PAYMENT => [self::CONFIRMED, self::CANCELLED, self::EXPIRED],
            self::CONFIRMED => [self::CHECKED_IN, self::CANCELLED, self::NO_SHOW],
            self::CHECKED_IN => [self::CHECKED_OUT, self::CANCELLED],
            self::CHECKED_OUT => [self::COMPLETED],
            self::CANCELLED => [],
            self::EXPIRED => [],
            self::NO_SHOW => [],
            self::COMPLETED => [],
        ];
        
        // Nếu trạng thái hiện tại không tồn tại trong mảng
        if (!isset($allowedTransitions[$current])) {
            return false;
        }
        
        // Kiểm tra xem trạng thái mới có trong danh sách cho phép không
        return \in_array($new, $allowedTransitions[$current]);
    }
}

