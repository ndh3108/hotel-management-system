<?php

namespace App\Repositories;

use App\Core\Database;
use App\Core\BookingException;
use PDO;

class RoomRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Khóa phòng tạm thời (Hold) sử dụng Optimistic Locking dựa trên cột version.
     * Áp dụng cơ chế Retry tối đa 3 lần nếu phát hiện xung đột (StaleObjectStateException).
     */
    public function lockRoomOptimistic(int $roomId, string $sessionId, int $maxRetries = 3): bool
    {
        $attempts = 0;

        while ($attempts < $maxRetries) {
            // Lấy trạng thái và version hiện tại của phòng
            $stmt = $this->pdo->prepare("SELECT id, status, version, locked_at FROM rooms WHERE id = ?");
            $stmt->execute([$roomId]);
            $room = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$room) {
                throw new BookingException('room_not_found', 'Phòng không tồn tại.');
            }

            // Kiểm tra trạng thái: chỉ cho phép lấy phòng Trống (Available)
            // Nếu đang Hold nhưng đã quá hạn (VD: > 15 phút) thì cho phép ghi đè
            $isAvailable = $room['status'] === 'Available';
            $isHoldExpired = false;
            
            if ($room['status'] === 'Hold' && !empty($room['locked_at'])) {
                $lockedTime = strtotime($room['locked_at']);
                if ((time() - $lockedTime) > 900) { // Quá 15 phút
                    $isHoldExpired = true;
                }
            }

            if (!$isAvailable && !$isHoldExpired) {
                throw new BookingException('room_unavailable', 'Phòng đã được đặt hoặc đang bảo trì.');
            }

            $currentVersion = (int) $room['version'];
            $newVersion = $currentVersion + 1;
            $now = date('Y-m-d H:i:s');

            // Cập nhật với điều kiện version phải đúng bằng version đã lấy ra
            $updateStmt = $this->pdo->prepare("
                UPDATE rooms 
                SET status = 'Hold', locked_at = ?, locked_by_session = ?, version = ?
                WHERE id = ? AND version = ?
            ");
            
            $updateStmt->execute([$now, $sessionId, $newVersion, $roomId, $currentVersion]);

            // Nếu update thành công (rowCount > 0), nghĩa là không có ai khác sửa trước đó
            if ($updateStmt->rowCount() > 0) {
                return true; 
            }

            // Nếu rowCount == 0 -> Conflict xảy ra (Stale Object State)
            $attempts++;
            // Nghỉ 50ms - 150ms trước khi retry để tránh thư gửi dồn dập
            usleep(rand(50000, 150000));
        }

        // Vượt quá số lần thử -> Ném lỗi
        throw new BookingException('lock_timeout', 'Hệ thống bận, vui lòng thử lại sau.');
    }

    /**
     * Giải phóng phòng đã khóa (thường dùng khi timeout hoặc thanh toán thất bại)
     */
    public function releaseRoomLock(int $roomId, string $sessionId): bool
    {
        // Chỉ nhả khoá nếu đúng session đã khoá
        $stmt = $this->pdo->prepare("
            UPDATE rooms 
            SET status = 'Available', locked_at = NULL, locked_by_session = NULL, version = version + 1
            WHERE id = ? AND locked_by_session = ? AND status = 'Hold'
        ");
        $stmt->execute([$roomId, $sessionId]);
        return $stmt->rowCount() > 0;
    }
}

