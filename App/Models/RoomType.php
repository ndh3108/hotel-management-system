<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model RoomType - Bảng room_types.
 * Dùng cho BOOKING FLOW: xem danh sách loại phòng (name, capacity, base_price, addon qua Service).
 */
class RoomType extends Model
{
    protected string $table = 'room_types';

    protected array $fillable = [
        'name',
        'slug',
        'description',
        'area_sqm',
        'capacity_adults',
        'capacity_children',
        'base_price',
        'max_occupancy',
        'bed_type',
        'room_size',
        'amenities_default',
        'refund_policy',
        'min_stay',
        'max_stay',
        'seasonal_pricing_rule',
        'check_in_time',
        'check_out_time',
        'policy_check_in',
        'policy_children',
        'policy_cancel',
    ];

    private ?bool $hasLegacyRoomImagesTable = null;
    private ?array $amenityPivotTables = null;

    private function hasLegacyRoomImagesTable(): bool
    {
        if ($this->hasLegacyRoomImagesTable !== null) {
            return $this->hasLegacyRoomImagesTable;
        }

        try {
            $stmt = $this->pdo->query("SHOW TABLES LIKE 'room_images'");
            $this->hasLegacyRoomImagesTable = (bool) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            $this->hasLegacyRoomImagesTable = false;
        }

        return $this->hasLegacyRoomImagesTable;
    }

    private function detectAmenityPivotTables(): array
    {
        if ($this->amenityPivotTables !== null) {
            return $this->amenityPivotTables;
        }

        $tables = [];
        foreach (['room_type_amenities', 'roomtype_amenities'] as $table) {
            try {
                $stmt = $this->pdo->query("SHOW TABLES LIKE '{$table}'");
                if ($stmt && $stmt->fetchColumn()) {
                    $tables[] = $table;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        $this->amenityPivotTables = array_values(array_unique($tables));
        return $this->amenityPivotTables;
    }

    public function getSelectedAmenityIds(int $roomTypeId): array
    {
        $ids = [];

        foreach ($this->detectAmenityPivotTables() as $table) {
            $stmt = $this->pdo->prepare("SELECT amenity_id FROM `{$table}` WHERE room_type_id = ?");
            $stmt->execute([$roomTypeId]);
            $ids = array_merge($ids, array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: []));
        }

        $ids = array_values(array_unique($ids));
        sort($ids);

        return $ids;
    }

    public function syncAmenities(int $roomTypeId, array $amenityIds): void
    {
        $amenityIds = array_values(array_unique(array_filter(array_map('intval', $amenityIds), static fn ($id) => $id > 0)));

        foreach ($this->detectAmenityPivotTables() as $table) {
            $this->pdo->prepare("DELETE FROM `{$table}` WHERE room_type_id = ?")->execute([$roomTypeId]);

            if (empty($amenityIds)) {
                continue;
            }

            $stmt = $this->pdo->prepare("INSERT INTO `{$table}` (room_type_id, amenity_id) VALUES (?, ?)");
            foreach ($amenityIds as $amenityId) {
                $stmt->execute([$roomTypeId, $amenityId]);
            }
        }
    }

    /**
     * Lấy tất cả loại phòng kèm ảnh chính
     */
    public function getAllWithImages(): array
    {
        if ($this->hasLegacyRoomImagesTable()) {
            $sql = "
                SELECT rt.*, 
                       COALESCE(
                           (SELECT image_url FROM images WHERE entity_type = 'room_type' AND entity_id = rt.id ORDER BY id ASC LIMIT 1),
                           (SELECT image_url FROM room_images WHERE room_type_id = rt.id AND is_primary = 1 LIMIT 1),
                           (SELECT image_url FROM room_images WHERE room_type_id = rt.id ORDER BY id DESC LIMIT 1)
                       ) as image_url
                FROM `{$this->table}` rt
            ";
        } else {
            $sql = "
                SELECT rt.*,
                       (SELECT image_url FROM images WHERE entity_type = 'room_type' AND entity_id = rt.id ORDER BY id ASC LIMIT 1) as image_url
                FROM `{$this->table}` rt
            ";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function getCursorPageWithImages(int $lastId = 0, int $limit = 9): array
    {
        $fetchLimit = $limit + 1;

        if ($this->hasLegacyRoomImagesTable()) {
            $sql = "
                SELECT rt.*, 
                       COALESCE(
                           (SELECT image_url FROM images WHERE entity_type = 'room_type' AND entity_id = rt.id ORDER BY id ASC LIMIT 1),
                           (SELECT image_url FROM room_images WHERE room_type_id = rt.id AND is_primary = 1 LIMIT 1),
                           (SELECT image_url FROM room_images WHERE room_type_id = rt.id ORDER BY id DESC LIMIT 1)
                       ) as image_url
                FROM `{$this->table}` rt
                WHERE rt.id > :last_id
                ORDER BY rt.id ASC
                LIMIT :limit_rows
            ";
        } else {
            $sql = "
                SELECT rt.*,
                       (SELECT image_url FROM images WHERE entity_type = 'room_type' AND entity_id = rt.id ORDER BY id ASC LIMIT 1) as image_url
                FROM `{$this->table}` rt
                WHERE rt.id > :last_id
                ORDER BY rt.id ASC
                LIMIT :limit_rows
            ";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':last_id', $lastId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit_rows', $fetchLimit, \PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [], $limit);
    }

    public function getPaginatedWithImages(int $cursor = 0, int $limit = 10): array
    {
        $limit = max(1, $limit);
        $fetchLimit = $limit + 1;

        if ($this->hasLegacyRoomImagesTable()) {
            $sql = "
                SELECT rt.*, 
                       COALESCE(
                           (SELECT image_url FROM images WHERE entity_type = 'room_type' AND entity_id = rt.id ORDER BY id ASC LIMIT 1),
                           (SELECT image_url FROM room_images WHERE room_type_id = rt.id AND is_primary = 1 LIMIT 1),
                           (SELECT image_url FROM room_images WHERE room_type_id = rt.id ORDER BY id DESC LIMIT 1)
                       ) as image_url
                FROM `{$this->table}` rt
                WHERE rt.deleted_at IS NULL";
            
            if ($cursor > 0) {
                $sql .= " AND rt.id > :cursor";
            }
            
            $sql .= " ORDER BY rt.id ASC LIMIT :limit_rows";
        } else {
            $sql = "
                SELECT rt.*,
                       (SELECT image_url FROM images WHERE entity_type = 'room_type' AND entity_id = rt.id ORDER BY id ASC LIMIT 1) as image_url
                FROM `{$this->table}` rt
                WHERE rt.deleted_at IS NULL";
            
            if ($cursor > 0) {
                $sql .= " AND rt.id > :cursor";
            }
            
            $sql .= " ORDER BY rt.id ASC LIMIT :limit_rows";
        }

        $stmt = $this->pdo->prepare($sql);
        if ($cursor > 0) {
            $stmt->bindValue(':cursor', $cursor, \PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit_rows', $fetchLimit, \PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [], $limit);
    }

    /**
     * Lấy chi tiết loại phòng kèm tất cả ảnh và tiện nghi
     */
    public function getFullDetail(int $id): ?array
    {
        $roomType = $this->findById($id);
        if (!$roomType)
            return null;

        // Lấy ảnh: Ưu tiên ảnh của Loại phòng
        $sqlImages = "
            SELECT id, image_url, public_id, 0 AS is_primary, 'images' AS source
            FROM `images`
            WHERE entity_type = 'room_type' AND entity_id = ?
            ORDER BY id ASC
        ";
        $stmtImages = $this->pdo->prepare($sqlImages);
        $stmtImages->execute([$id]);
        $roomType['images'] = $stmtImages->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        if ($this->hasLegacyRoomImagesTable()) {
            $sqlLegacyImages = "
                SELECT id, image_url, NULL AS public_id, is_primary, 'room_images' AS source
                FROM `room_images`
                WHERE room_type_id = ?
                ORDER BY is_primary DESC, id DESC
            ";
            $stmtLegacyImages = $this->pdo->prepare($sqlLegacyImages);
            $stmtLegacyImages->execute([$id]);
            $legacy = $stmtLegacyImages->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            if (!empty($legacy)) {
                $roomType['images'] = array_merge($roomType['images'], $legacy);
            }
        }

        usort($roomType['images'], static function (array $a, array $b): int {
            $aPrimary = (int) ($a['is_primary'] ?? 0);
            $bPrimary = (int) ($b['is_primary'] ?? 0);
            if ($aPrimary !== $bPrimary) {
                return $bPrimary <=> $aPrimary;
            }
            return ((int) ($a['id'] ?? 0)) <=> ((int) ($b['id'] ?? 0));
        });

        // Lấy tiện nghi
        $sqlVideo = "
            SELECT image_url
            FROM `images`
            WHERE entity_type = 'room_type_video' AND entity_id = ?
            ORDER BY id ASC
            LIMIT 1
        ";
        $stmtVideo = $this->pdo->prepare($sqlVideo);
        $stmtVideo->execute([$id]);
        $roomType['video_url'] = $stmtVideo->fetchColumn() ?: null;

        $amenityIds = $this->getSelectedAmenityIds($id);
        if (!empty($amenityIds)) {
            $placeholders = implode(',', array_fill(0, count($amenityIds), '?'));
            $sqlAmenities = "
                SELECT a.*
                FROM `amenities` a
                WHERE a.id IN ({$placeholders})
                ORDER BY a.name ASC
            ";
            $stmtAmenities = $this->pdo->prepare($sqlAmenities);
            $stmtAmenities->execute($amenityIds);
            $roomType['amenities'] = $stmtAmenities->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } else {
            $roomType['amenities'] = [];
        }

        // Fetch Supplies (Standard Equipment)
        $roomType['supplies'] = $this->getSupplies($id);

        return $roomType;
    }

    /**
     * Get standard equipment/supplies for this room type from inventory catalog
     */
    public function getSupplies(int $roomTypeId): array
    {
        $sql = "
            SELECT i.id, i.name, rti.quantity
            FROM `inventory_items` i
            JOIN `room_type_inventory` rti ON i.id = rti.inventory_item_id
            WHERE rti.room_type_id = ?
            ORDER BY i.name ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomTypeId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Sync supplies (called from Admin)
     * Propagates changes to all rooms of this type.
     */
    public function syncSupplies(int $roomTypeId, array $supplies): void
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Update Room Type Inventory (Standard)
            $this->pdo->prepare("DELETE FROM `room_type_inventory` WHERE room_type_id = ?")->execute([$roomTypeId]);
            
            if (!empty($supplies)) {
                $stmt = $this->pdo->prepare("INSERT INTO `room_type_inventory` (room_type_id, inventory_item_id, quantity) VALUES (?, ?, ?)");
                foreach ($supplies as $item) {
                    $itemId = (int)($item['inventory_item_id'] ?? 0);
                    $qty = (int)($item['quantity'] ?? 1);
                    if ($itemId > 0) {
                        $stmt->execute([$roomTypeId, $itemId, $qty]);
                    }
                }
            }

            // 2. Propagate to all rooms of this type
            // Get all rooms of this type
            $stmtRooms = $this->pdo->prepare("SELECT id FROM rooms WHERE room_type_id = ?");
            $stmtRooms->execute([$roomTypeId]);
            $roomIds = $stmtRooms->fetchAll(\PDO::FETCH_COLUMN);

            if (!empty($roomIds)) {
                $newItemIds = array_filter(array_column($supplies, 'inventory_item_id'), fn($id) => (int)$id > 0);
                
                foreach ($roomIds as $rid) {
                    // Remove items no longer in the standard list for this room
                    if (empty($newItemIds)) {
                        $this->pdo->prepare("DELETE FROM `room_inventory` WHERE room_id = ?")->execute([$rid]);
                    } else {
                        $placeholders = implode(',', array_fill(0, count($newItemIds), '?'));
                        $stmtDel = $this->pdo->prepare("DELETE FROM `room_inventory` WHERE room_id = ? AND item_id NOT IN ($placeholders)");
                        $stmtDel->execute(array_merge([$rid], $newItemIds));
                        
                        // Add or Update items in room_inventory
                        foreach ($supplies as $item) {
                            $itemId = (int)($item['inventory_item_id'] ?? 0);
                            $qty = (int)($item['quantity'] ?? 1);
                            if ($itemId <= 0) continue;

                            // Upsert: Try update, if 0 rows, then insert
                            $stmtUpd = $this->pdo->prepare("UPDATE room_inventory SET quantity = ?, is_active = 1 WHERE room_id = ? AND item_id = ?");
                            $stmtUpd->execute([$qty, $rid, $itemId]);

                            if ($stmtUpd->rowCount() === 0) {
                                $stmtIns = $this->pdo->prepare("INSERT INTO room_inventory (room_id, room_type_id, item_id, quantity, status) VALUES (?, ?, ?, ?, 'Available')");
                                $stmtIns->execute([$rid, $roomTypeId, $itemId, $qty]);
                            }
                        }
                    }
                }
            }

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}

