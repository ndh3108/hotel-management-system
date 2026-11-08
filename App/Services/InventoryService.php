<?php

namespace App\Services;

use App\Models\RoomInventory;
use App\Models\BookingInventorySnapshot;
use App\Core\Database;
use PDO;

class InventoryService
{
    private RoomInventory $roomInventory;
    private BookingInventorySnapshot $snapshotModel;
    private PDO $pdo;

    public function __construct()
    {
        $this->roomInventory = new RoomInventory();
        $this->snapshotModel = new BookingInventorySnapshot();
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Get merged inventory for a room, prioritizing room-specific items over room-type items.
     */
    public function getMergedInventory(int $roomId, int $roomTypeId): array
    {
        $typeItems = $this->roomInventory->getByRoomType($roomTypeId);
        $roomItems = $this->roomInventory->getByRoom($roomId);

        $merged = [];
        // Use item_id as key for merging
        foreach ($typeItems as $item) {
            $merged[$item['item_id']] = $item;
        }

        foreach ($roomItems as $item) {
            // Room specific items override type items (Standard logic: if exists in room, replace type)
            // But usually, room inventory might be ADDITIVE or OVERRIDE. 
            // The user said: "room always override", "KHÔNG cộng dồn".
            $merged[$item['item_id']] = $item;
        }

        return array_values($merged);
    }

    /**
     * Create a snapshot of the current inventory for a booking detail.
     */
    public function createSnapshot(int $bookingDetailId): bool
    {
        $stmt = $this->pdo->prepare("SELECT room_id, (SELECT room_type_id FROM rooms WHERE id = room_id) as room_type_id FROM booking_details WHERE id = ?");
        $stmt->execute([$bookingDetailId]);
        $detail = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$detail || !$detail['room_id']) {
            return false;
        }

        $inventory = $this->getMergedInventory((int)$detail['room_id'], (int)$detail['room_type_id']);

        $inTransaction = $this->pdo->inTransaction();
        try {
            if (!$inTransaction) {
                $this->pdo->beginTransaction();
            }
            
            // Clear existing snapshot if any
            $this->pdo->prepare("DELETE FROM booking_inventory_items WHERE booking_detail_id = ?")->execute([$bookingDetailId]);

            $stmtInsert = $this->pdo->prepare("
                INSERT INTO booking_inventory_items (booking_detail_id, item_id, item_name, quantity, price_at_booking)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($inventory as $item) {
                $price = $item['price_override'] ?? $item['default_price'];
                $stmtInsert->execute([
                    $bookingDetailId,
                    $item['item_id'],
                    $item['item_name'],
                    $item['quantity'],
                    $price
                ]);
            }

            if (!$inTransaction) {
                $this->pdo->commit();
            }
            return true;
        } catch (\Exception $e) {
            if (!$inTransaction && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Snapshot error: " . $e->getMessage());
            return false;
        }
    }
}

