<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class ServiceBooking extends Model
{
    protected string $table = 'service_bookings';

    protected array $fillable = [
        'user_id',
        'booking_id',
        'service_id',
        'start_time',
        'end_time',
        'quantity',
        'total_price',
        'status',
    ];

    public function getByUserId(int $userId): array
    {
        $sql = "SELECT sb.*, s.name AS service_name
                FROM service_bookings sb
                INNER JOIN services s ON s.id = sb.service_id
                WHERE sb.user_id = ?
                ORDER BY sb.created_at DESC, sb.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getCursorPage(int $lastId = 0, int $limit = 15): array
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE id > :last_id
                ORDER BY id ASC
                LIMIT :limit_rows";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':last_id', $lastId, PDO::PARAM_INT);
        $stmt->bindValue(':limit_rows', $limit + 1, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }

    public function getPaginated(int $cursor = 0, int $limit = 10): array
    {
        $limit = max(1, $limit);
        $fetchLimit = $limit + 1;

        $sql = "SELECT sb.*, u.full_name as guest_name, s.name as service_name
                FROM {$this->table} sb
                LEFT JOIN users u ON sb.user_id = u.id
                LEFT JOIN services s ON sb.service_id = s.id";

        if ($cursor > 0) {
            $sql .= " WHERE sb.id > :cursor";
        }

        $sql .= " ORDER BY sb.id ASC LIMIT :limit_rows";

        $stmt = $this->pdo->prepare($sql);
        if ($cursor > 0) {
            $stmt->bindValue(':cursor', $cursor, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit_rows', $fetchLimit, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }

    public function findById($id): ?array
    {
        $sql = "SELECT sb.*, sb.total_price FROM {$this->table} sb WHERE sb.id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getBookingWithDetails($id): ?array
    {
        $sql = "SELECT 
                    sb.id,
                    sb.user_id,
                    sb.status,
                    sb.total_price,
                    sb.created_at,
                    sb.start_time as check_in,
                    sb.end_time as check_out,
                    u.full_name as guest_name,
                    u.email as guest_email,
                    u.phone as guest_phone,
                    s.name as service_name,
                    s.price_per_hour,
                    sb.quantity
                FROM {$this->table} sb
                LEFT JOIN users u ON sb.user_id = u.id
                LEFT JOIN services s ON sb.service_id = s.id
                WHERE sb.id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    public function updateStatus($id, $status): bool
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id' => $id
        ]);
    }

    public function saveTransactionLog($bookingId, $txnRef, $amount, $method, $status, $rawData = null): bool
    {
        $sql = "INSERT INTO transaction_log (booking_id, transaction_reference, amount, payment_method, status, raw_data, created_at)
                VALUES (:booking_id, :txn_ref, :amount, :method, :status, :raw_data, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'booking_id' => $bookingId,
            'txn_ref' => $txnRef,
            'amount' => $amount,
            'method' => $method,
            'status' => $status,
            'raw_data' => $rawData ? json_encode($rawData) : null
        ]);
    }
}

