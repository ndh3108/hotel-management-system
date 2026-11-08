<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class LoyaltyService
{
    private PDO $pdo;

    // Có thể chuyển sang config sau nếu muốn.
    private const EARN_RATE = 1 / 100000; // 1 point / 100k
    private const REDEEM_RATE = 1000;     // 1 point = 1,000đ
    private const MAX_REDEEM_PERCENT = 0.5; // Tối đa 50% giá trị đơn

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function calculateRedeemDiscount(int $userId, int $pointsWant, float $bookingTotal): array
    {
        if ($userId <= 0 || $pointsWant <= 0 || $bookingTotal <= 0) {
            return ['discount' => 0.0, 'points_used' => 0];
        }
        $stmt = $this->pdo->prepare("SELECT current_points FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $currentPoints = (int)$stmt->fetchColumn();
        if ($currentPoints <= 0) {
            return ['discount' => 0.0, 'points_used' => 0];
        }

        $maxByBalance = min($pointsWant, $currentPoints);
        $maxByPercent = (int)floor(($bookingTotal * self::MAX_REDEEM_PERCENT) / self::REDEEM_RATE);
        $pointsUsed = max(0, min($maxByBalance, $maxByPercent));

        $discount = $pointsUsed * self::REDEEM_RATE;
        return ['discount' => $discount, 'points_used' => $pointsUsed];
    }

    public function earnOnInvoice(int $userId, int $invoiceId, float $finalTotal): void
    {
        if ($userId <= 0 || $finalTotal <= 0) {
            return;
        }
        $earned = (int)floor($finalTotal * self::EARN_RATE);
        if ($earned <= 0) {
            return;
        }

        $stmtUser = $this->pdo->prepare("SELECT current_points, total_points_earned FROM users WHERE id = ? FOR UPDATE");
        $stmtUser->execute([$userId]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return;
        }

        $newCurrent = (int)($user['current_points'] ?? 0) + $earned;
        $newTotalEarned = (int)($user['total_points_earned'] ?? 0) + $earned;
        $membershipId = $this->resolveMembershipId($newTotalEarned);

        $stmtUpdate = $this->pdo->prepare("UPDATE users SET current_points = ?, total_points_earned = ?, membership_id = ? WHERE id = ?");
        $stmtUpdate->execute([$newCurrent, $newTotalEarned, $membershipId, $userId]);

        $stmtPointHistory = $this->pdo->prepare("INSERT INTO point_history (user_id, invoice_id, points, type, note) VALUES (?, ?, ?, 'earn', ?)");
        $stmtPointHistory->execute([$userId, $invoiceId, $earned, 'Earned from paid invoice']);
    }

    private function resolveMembershipId(int $totalPointsEarned): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM memberships WHERE min_points <= ? ORDER BY min_points DESC LIMIT 1");
        $stmt->execute([$totalPointsEarned]);
        $id = $stmt->fetchColumn();
        return $id ? (int)$id : 1;
    }
}


