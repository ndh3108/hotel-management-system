<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Review extends Model
{
    protected string $table = 'reviews';

    protected array $fillable = [
        'booking_id',
        'user_id',
        'rating',
        'content',
        'status',
        'created_at',
        'submit_token',
    ];

    public function getByRoomTypeId(int $roomTypeId): array
    {
        $sql = "
            SELECT
                rv.*,
                rv.content AS comment,
                u.full_name AS user_name,
                (
                    SELECT rm.room_number
                    FROM booking_details bd2
                    INNER JOIN rooms rm ON rm.id = bd2.room_id
                    WHERE bd2.booking_id = rv.booking_id
                    ORDER BY bd2.id ASC
                    LIMIT 1
                ) AS room_number
            FROM reviews rv
            LEFT JOIN users u ON u.id = rv.user_id
            WHERE EXISTS (
                SELECT 1
                FROM booking_details bd
                INNER JOIN rooms r ON r.id = bd.room_id
                WHERE bd.booking_id = rv.booking_id
                  AND r.room_type_id = ?
            )
              AND LOWER(rv.status) = 'approved'
            ORDER BY rv.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomTypeId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAverageByRoomTypeId(int $roomTypeId): float
    {
        $sql = "
            SELECT AVG(rv.rating) AS avg_rating
            FROM reviews rv
            WHERE EXISTS (
                SELECT 1
                FROM booking_details bd
                INNER JOIN rooms r ON r.id = bd.room_id
                WHERE bd.booking_id = rv.booking_id
                  AND r.room_type_id = ?
            )
              AND LOWER(rv.status) = 'approved'
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomTypeId]);

        return (float) ($stmt->fetchColumn() ?? 0);
    }

    public function countByRoomTypeId(int $roomTypeId): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM reviews rv
            WHERE EXISTS (
                SELECT 1
                FROM booking_details bd
                INNER JOIN rooms r ON r.id = bd.room_id
                WHERE bd.booking_id = rv.booking_id
                  AND r.room_type_id = ?
            )
              AND LOWER(rv.status) = 'approved'
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomTypeId]);

        return (int) $stmt->fetchColumn();
    }

    public function moderate(int $reviewId, string $status): bool
    {
        $normalized = strtolower(trim($status));
        $validStatuses = ['approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected'];
        $status = $validStatuses[$normalized] ?? 'Pending';

        return $this->update($reviewId, [
            'status' => $status,
        ]);
    }

    public function getCursorPageForAdmin(int $lastId = 0, int $limit = 20): array
    {
        $sql = "
            SELECT
                r.*,
                r.content AS comment,
                u.full_name AS user_name,
                (
                    SELECT rm.room_number
                    FROM booking_details bd
                    INNER JOIN rooms rm ON rm.id = bd.room_id
                    WHERE bd.booking_id = r.booking_id
                    ORDER BY bd.id ASC
                    LIMIT 1
                ) AS room_number
            FROM reviews r
            LEFT JOIN users u ON u.id = r.user_id
            WHERE r.id > :last_id
            ORDER BY r.id ASC
            LIMIT :limit_rows
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':last_id', $lastId, PDO::PARAM_INT);
        $stmt->bindValue(':limit_rows', $limit + 1, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }

    public function hasRecentByUser(int $userId, int $seconds = 60): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT id
            FROM reviews
            WHERE user_id = :user_id
              AND created_at >= DATE_SUB(NOW(), INTERVAL :window SECOND)
            LIMIT 1
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':window', max(1, $seconds), PDO::PARAM_INT);
        $stmt->execute();
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function hasSubmitToken(string $token): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM reviews WHERE submit_token = :token LIMIT 1");
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

