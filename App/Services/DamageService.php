<?php

namespace App\Services;

use App\Models\InventoryDamage;
use App\Models\BookingInventorySnapshot;
use App\Core\Database;
use PDO;

class DamageService
{
    private InventoryDamage $damageModel;
    private BookingInventorySnapshot $snapshotModel;
    private PDO $pdo;

    public function __construct()
    {
        $this->damageModel = new InventoryDamage();
        $this->snapshotModel = new BookingInventorySnapshot();
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Report damage/loss for an item.
     * Returns the damage ID on success, false on failure.
     */
    public function reportDamage(?int $bookingDetailId, int $itemId, int $quantity, int $userId, string $notes = ''): int|false
    {
        try {
            $bookingId = null;
            $roomId = null;
            $itemName = 'Unknown Item';
            $priceAtReport = 0.0;

            // 1. Get info
            if ($bookingDetailId) {
                $sqlInfo = "
                    SELECT bd.booking_id, bd.room_id, bii.item_name, bii.price_at_booking 
                    FROM booking_details bd
                    JOIN booking_inventory_items bii ON bii.booking_detail_id = bd.id
                    WHERE bd.id = ? AND bii.item_id = ?
                ";
                $stmtInfo = $this->pdo->prepare($sqlInfo);
                $stmtInfo->execute([$bookingDetailId, $itemId]);
                $info = $stmtInfo->fetch(PDO::FETCH_ASSOC);
                
                if ($info) {
                    $bookingId = $info['booking_id'];
                    $roomId = $info['room_id'];
                    $itemName = $info['item_name'];
                    $priceAtReport = (float)$info['price_at_booking'];
                }
            }
            
            // Fallback for non-booking damage (housekeeping check-up)
            if (!$roomId) {
                // Try to find the item in catalog
                $stmtCatalog = $this->pdo->prepare("SELECT name, default_price FROM inventory_items WHERE id = ?");
                $stmtCatalog->execute([$itemId]);
                $catalog = $stmtCatalog->fetch(PDO::FETCH_ASSOC);
                if ($catalog) {
                    $itemName = $catalog['name'];
                    $priceAtReport = (float)$catalog['default_price'];
                }
            }

            // 2. Insert as Reported
            $sql = "
                INSERT INTO inventory_damage (
                    booking_id, room_id, booking_detail_id, item_id, 
                    item_name_at_report, quantity, price, price_at_report, 
                    status, reported_by, notes
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Reported', ?, ?)
            ";
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([
                $bookingId,
                $roomId,
                $bookingDetailId,
                $itemId,
                $itemName,
                $quantity,
                $priceAtReport,
                $priceAtReport,
                $userId,
                $notes
            ])) {
                return (int)$this->pdo->lastInsertId();
            }
            return false;
        } catch (\Exception $e) {
            error_log("Damage report error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Approve damage for billing. Captures the price_at_approval snapshot.
     */
    public function approveDamage(int $damageId, int $adminId, ?float $overridePrice = null): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT idmg.*, ii.default_price 
                FROM inventory_damage idmg
                LEFT JOIN inventory_items ii ON ii.id = idmg.item_id
                WHERE idmg.id = ?
            ");
            $stmt->execute([$damageId]);
            $damage = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$damage) return false;

            // Use override price, current catalog price, or original report price
            $finalPrice = $overridePrice ?? $damage['default_price'] ?? $damage['price_at_report'];

            $sql = "
                UPDATE inventory_damage 
                SET status = 'Approved', 
                    approved_by = ?, 
                    price_at_approval = ?,
                    price = ?
                WHERE id = ? AND status IN ('Reported', 'Reviewed')
            ";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$adminId, $finalPrice, $finalPrice, $damageId]);
        } catch (\Exception $e) {
            error_log("Damage approval error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark damage as paid by the customer.
     */
    public function markAsPaid(int $damageId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE inventory_damage SET status = 'Paid' WHERE id = ? AND status = 'Approved'");
        return $stmt->execute([$damageId]);
    }

    /**
     * Deduct from warehouse stock and mark as replaced/fixed.
     * Also restores room status if it was in Maintenance.
     */
    public function replaceFromStock(int $damageId, int $adminId): array
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Get damage info and current stock
            $stmt = $this->pdo->prepare("
                SELECT idmg.*, ii.stock_quantity, ii.name as item_name
                FROM inventory_damage idmg
                JOIN inventory_items ii ON ii.id = idmg.item_id
                WHERE idmg.id = ? FOR UPDATE
            ");
            $stmt->execute([$damageId]);
            $damage = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$damage) {
                throw new \Exception("Damage record not found.");
            }

            if ($damage['stock_quantity'] < $damage['quantity']) {
                throw new \Exception("Insufficient stock in warehouse for {$damage['item_name']}. (Available: {$damage['stock_quantity']}, Needed: {$damage['quantity']})");
            }

            // 2. Deduct from stock
            $stmtStock = $this->pdo->prepare("UPDATE inventory_items SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $stmtStock->execute([$damage['quantity'], $damage['item_id']]);

            // 3. Update damage status
            $stmtUpdate = $this->pdo->prepare("
                UPDATE inventory_damage 
                SET status = 'Replaced', 
                    is_fixed = 1,
                    approved_by = ?
                WHERE id = ?
            ");
            $stmtUpdate->execute([$adminId, $damageId]);

            // 4. Restore room status if needed
            // If room is in 'Maintenance', change to 'Cleaning' so it can be prepared for next guest
            $stmtRoom = $this->pdo->prepare("UPDATE rooms SET status = 'Cleaning' WHERE id = ? AND status = 'Maintenance'");
            $stmtRoom->execute([$damage['room_id']]);

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Vật tư đã được thay thế và trừ kho thành công. Trạng thái phòng đã được cập nhật.'];

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

