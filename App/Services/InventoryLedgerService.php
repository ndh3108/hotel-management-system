<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class InventoryLedgerService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function record(
        int $roomId,
        string $action,
        ?int $bookingId = null,
        ?int $issueId = null,
        ?int $userId = null,
        ?string $reason = null
    ): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO room_inventory_ledger
            (room_id, action, booking_id, issue_id, user_id, reason)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$roomId, $action, $bookingId, $issueId, $userId, $reason]);
    }
}

