<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class VoucherService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Validate voucher code for a user and tentative booking total.
     *
     * @return array{valid:bool, discount:float, voucher_id:int|null, message:string}
     */
    public function validateCode(string $code, int $userId, float $bookingTotal, ?int $roomTypeId = null): array
    {
        $code = trim($code);
        if ($code === '' || $bookingTotal <= 0) {
            return ['valid' => false, 'discount' => 0.0, 'voucher_id' => null, 'message' => 'Mã không hợp lệ'];
        }

        $stmt = $this->pdo->prepare("
            SELECT * FROM vouchers
            WHERE code = ? LIMIT 1
        ");
        $stmt->execute([$code]);
        $voucher = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$voucher) {
            return ['valid' => false, 'discount' => 0.0, 'voucher_id' => null, 'message' => 'Mã voucher không tồn tại'];
        }

        if ((int)($voucher['is_active'] ?? 0) !== 1) {
            return ['valid' => false, 'discount' => 0.0, 'voucher_id' => (int)$voucher['id'], 'message' => 'Voucher đã hết hiệu lực'];
        }

        $now = date('Y-m-d H:i:s');
        $validTo = $voucher['valid_until'] ?? ($voucher['valid_to'] ?? null);
        if ((!empty($voucher['valid_from']) && $now < $voucher['valid_from']) ||
            (!empty($validTo) && $now > $validTo)
        ) {
            return ['valid' => false, 'discount' => 0.0, 'voucher_id' => (int)$voucher['id'], 'message' => 'Voucher không nằm trong thời gian sử dụng'];
        }

        $minOrder = isset($voucher['min_order_value']) ? (float)$voucher['min_order_value'] : (float)($voucher['min_booking_value'] ?? 0);
        if ($minOrder > 0 && $bookingTotal < $minOrder) {
            return [
                'valid' => false,
                'discount' => 0.0,
                'voucher_id' => (int)$voucher['id'],
                'message' => 'Giá trị đơn chưa đủ điều kiện tối thiểu',
            ];
        }

        if (!empty($voucher['room_type_id']) && $roomTypeId !== null && (int)$voucher['room_type_id'] !== $roomTypeId) {
            return [
                'valid' => false,
                'discount' => 0.0,
                'voucher_id' => (int)$voucher['id'],
                'message' => 'Voucher chỉ áp dụng cho loại phòng khác',
            ];
        }

        // Usage limits
        $voucherId = (int)$voucher['id'];
        $maxUses = (int)($voucher['max_uses'] ?? 0);
        $maxUsesPerUser = (int)($voucher['max_uses_per_user'] ?? 0);
        $usedCount = (int)($voucher['used_count'] ?? 0);

        if ($maxUses > 0 && $usedCount >= $maxUses) {
            return [
                'valid' => false,
                'discount' => 0.0,
                'voucher_id' => $voucherId,
                'message' => 'Voucher đã đạt giới hạn sử dụng',
            ];
        }

        if ($maxUsesPerUser > 0 && $userId > 0) {
            $stmtUsage = $this->pdo->prepare("
                SELECT COUNT(*) FROM user_vouchers
                WHERE user_id = ? AND voucher_id = ?
            ");
            $stmtUsage->execute([$userId, $voucherId]);
            $userUsed = (int)$stmtUsage->fetchColumn();
            if ($userUsed >= $maxUsesPerUser) {
                return [
                    'valid' => false,
                    'discount' => 0.0,
                    'voucher_id' => $voucherId,
                    'message' => 'Bạn đã dùng tối đa số lần cho voucher này',
                ];
            }
        }

        // Calculate discount
        $type = strtolower((string)($voucher['discount_type'] ?? $voucher['type'] ?? ''));
        $value = isset($voucher['discount_value']) ? (float)$voucher['discount_value'] : (float)($voucher['value'] ?? 0);
        $discount = 0.0;
        if (in_array($type, ['percent', 'percentage', 'percent_amount', 'percent_off'], true) || $type === 'percent' || $type === 'p') {
            $discount = $bookingTotal * ($value / 100);
        } else {
            $discount = $value;
        }
        if (!empty($voucher['max_discount'])) {
            $discount = min($discount, (float)$voucher['max_discount']);
        }
        $discount = max(0.0, min($discount, $bookingTotal));

        return [
            'valid' => true,
            'discount' => $discount,
            'voucher_id' => $voucherId,
            'message' => 'Áp dụng voucher thành công',
        ];
    }

    /**
     * Ghi lại việc sử dụng voucher sau khi checkout thành công.
     */
    public function recordUsage(int $userId, int $voucherId, int $bookingId): void
    {
        if ($userId <= 0 || $voucherId <= 0 || $bookingId <= 0) {
            return;
        }
        $stmt = $this->pdo->prepare("
            INSERT INTO user_vouchers (user_id, voucher_id, booking_id, used_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $voucherId, $bookingId]);

        $this->pdo->prepare("UPDATE vouchers SET used_count = used_count + 1 WHERE id = ?")
            ->execute([$voucherId]);
    }
}


