<?php

namespace App\Models;

use App\Core\Model;
use App\Core\BookingStatus;
use PDO;

/**
 * Model Room - Bảng room_details.
 * Dùng cho BOOKING FLOW: tìm phòng còn trống theo khoảng check-in/check-out, loại phòng.
 */
class Room extends Model
{
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_OCCUPIED = 'occupied';
    public const STATUS_CLEANING = 'cleaning';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_OUT_OF_SERVICE = 'out_of_service';
    public const STATUS_DAMAGED = 'damaged';

    protected string $table = 'rooms';

    protected array $fillable = [
        'room_type_id',
        'room_number',
        'floor',
        'location',
        'status',
        'version',
        'display_status',
        'notes',
    ];

    public static function statusOptions(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Trong',
            self::STATUS_RESERVED => 'Da giu cho',
            self::STATUS_OCCUPIED => 'Co khach',
            self::STATUS_CLEANING => 'Dang don',
            self::STATUS_MAINTENANCE => 'Bao tri',
            self::STATUS_BLOCKED => 'Blocked',
            self::STATUS_OUT_OF_SERVICE => 'Ngung phuc vu',
            self::STATUS_DAMAGED => 'Hu hong',
        ];
    }

    public static function normalizeStatus(string $status): string
    {
        $normalized = strtolower(trim($status));

        return match ($normalized) {
            'available' => self::STATUS_AVAILABLE,
            'reserved', 'hold' => self::STATUS_RESERVED,
            'occupied' => self::STATUS_OCCUPIED,
            'cleaning' => self::STATUS_CLEANING,
            'maintenance' => self::STATUS_MAINTENANCE,
            'blocked' => self::STATUS_BLOCKED,
            'out_of_service' => self::STATUS_OUT_OF_SERVICE,
            'damaged' => self::STATUS_DAMAGED,
            default => self::STATUS_AVAILABLE,
        };
    }

    public static function toLegacyStatus(string $status): string
    {
        return match (self::normalizeStatus($status)) {
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_RESERVED => 'Hold',
            self::STATUS_OCCUPIED => 'Occupied',
            self::STATUS_CLEANING => 'Cleaning',
            self::STATUS_MAINTENANCE => 'Maintenance',
            self::STATUS_BLOCKED => 'Hold',
            self::STATUS_OUT_OF_SERVICE => 'Maintenance',
            self::STATUS_DAMAGED => 'Maintenance',
            default => 'Available',
        };
    }

    /**
     * Get features assigned to this room
     */
    public function getFeatures(): array
    {
        $featureModel = new RoomFeature();
        return $featureModel->getForRoom((int)$this->id);
    }

    /**
     * Get maintenance history for this room
     */
    public function getMaintenanceHistory(): array
    {
        $maintenanceModel = new RoomMaintenance();
        return $maintenanceModel->getByRoom((int)$this->id);
    }

    /**
     * Get all rooms with room type info, filtered by soft delete if applicable
     */
    public function getAllWithType(): array
    {
        $sql = "
            SELECT r.*, rt.name AS room_type_name, rt.base_price,
                   (SELECT image_url FROM images i WHERE i.entity_type = 'room' AND i.entity_id = r.id ORDER BY i.id ASC LIMIT 1) as image_url,
                   (SELECT maintenance_date FROM room_maintenance rm WHERE rm.room_id = r.id AND rm.status != 'Completed' ORDER BY maintenance_date DESC LIMIT 1) as maintenance_since
            FROM `{$this->table}` r
            LEFT JOIN `room_types` rt ON rt.id = r.room_type_id
            ORDER BY r.room_number ASC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Override findById to include room type info
     */
    public function findById($id): ?array
    {
        $sql = "
            SELECT r.*, rt.name AS room_type_name
            FROM `{$this->table}` r
            LEFT JOIN `room_types` rt ON rt.id = r.room_type_id
            WHERE r.id = ? LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data)
    {
        $data['display_status'] = self::normalizeStatus((string) ($data['status'] ?? self::STATUS_AVAILABLE));
        $data['status'] = self::toLegacyStatus((string) ($data['status'] ?? self::STATUS_AVAILABLE));
        return parent::create($data);
    }

    public function update($id, array $data): bool
    {
        if (array_key_exists('status', $data)) {
            $data['display_status'] = self::normalizeStatus((string) $data['status']);
            $data['status'] = self::toLegacyStatus((string) $data['status']);
        }

        return parent::update($id, $data);
    }

    public function getCursorPageWithType(int $lastId = 0, int $limit = 15): array
    {
        $sql = "
            SELECT r.*, rt.name AS room_type_name, rt.base_price,
                   (SELECT image_url FROM images i WHERE i.entity_type = 'room' AND i.entity_id = r.id ORDER BY i.id ASC LIMIT 1) as image_url,
                   (SELECT created_at FROM room_status_logs rsl WHERE rsl.room_id = r.id AND rsl.new_status = 'Maintenance' ORDER BY id DESC LIMIT 1) as maintenance_since
            FROM `{$this->table}` r
            LEFT JOIN `room_types` rt ON rt.id = r.room_type_id
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

    public function getPaginatedWithType(int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;
        $totalItems = (int) $this->pdo->query("SELECT COUNT(*) FROM `{$this->table}`")->fetchColumn();

        $sql = "
            SELECT r.*, rt.name AS room_type_name, rt.base_price,
                   (SELECT image_url FROM images i WHERE i.entity_type = 'room' AND i.entity_id = r.id ORDER BY i.id ASC LIMIT 1) as image_url,
                   (SELECT created_at FROM room_status_logs rsl WHERE rsl.room_id = r.id AND rsl.new_status = 'Maintenance' ORDER BY id DESC LIMIT 1) as maintenance_since
            FROM `{$this->table}` r
            LEFT JOIN `room_types` rt ON rt.id = r.room_type_id
            ORDER BY r.id ASC
            LIMIT :limit_rows OFFSET :offset_rows
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit_rows', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset_rows', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildPageResult($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $page, $perPage, $totalItems);
    }

    public function getFilteredPaginatedWithType(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $where = ['1=1'];
        $params = [];

        if (!empty($filters['room_type_id'])) {
            $where[] = 'r.room_type_id = :room_type_id';
            $params[':room_type_id'] = (int) $filters['room_type_id'];
        }
        if (!empty($filters['status'])) {
            $where[] = '(r.display_status = :status OR (r.display_status IS NULL AND LOWER(r.status) = :legacy_status))';
            $params[':status'] = self::normalizeStatus((string) $filters['status']);
            $params[':legacy_status'] = strtolower(self::toLegacyStatus((string) $filters['status']));
        }
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $where[] = 'rt.base_price >= :min_price';
            $params[':min_price'] = (float) $filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $where[] = 'rt.base_price <= :max_price';
            $params[':max_price'] = (float) $filters['max_price'];
        }

        $dateFiltersProvided = !empty($filters['check_in']) && !empty($filters['check_out']);
        $bookingBlocksWhere = '';
        if ($dateFiltersProvided) {
            $blockStatuses = BookingStatus::statusesThatBlockRoom();
            $statusParams = [];
            foreach ($blockStatuses as $idx => $status) {
                $key = ':block_status_' . $idx;
                $statusParams[$key] = $status;
            }
            $params = array_merge($params, $statusParams);
            $params[':check_in'] = (string) $filters['check_in'];
            $params[':check_out'] = (string) $filters['check_out'];
            $bookingBlocksWhere = "
                AND r.id NOT IN (
                    SELECT bd.room_id
                    FROM bookings b
                    JOIN booking_details bd ON bd.booking_id = b.id
                    WHERE COALESCE(NULLIF(b.booking_status, ''), b.status) IN (" . implode(',', array_keys($statusParams)) . ")
                      AND bd.check_in_date < :check_out
                      AND bd.check_out_date > :check_in
                )
            ";
        }

        $whereSql = implode(' AND ', $where);
        $countSql = "
            SELECT COUNT(*)
            FROM `{$this->table}` r
            LEFT JOIN `room_types` rt ON rt.id = r.room_type_id
            WHERE {$whereSql}
            {$bookingBlocksWhere}
        ";
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $totalItems = (int) $countStmt->fetchColumn();

        $sql = "
            SELECT r.*, rt.name AS room_type_name, rt.base_price,
                   (SELECT image_url FROM images i WHERE i.entity_type = 'room' AND i.entity_id = r.id ORDER BY i.id ASC LIMIT 1) as image_url
            FROM `{$this->table}` r
            LEFT JOIN `room_types` rt ON rt.id = r.room_type_id
            WHERE {$whereSql}
            {$bookingBlocksWhere}
            ORDER BY rt.base_price ASC, r.floor ASC, r.room_number ASC
            LIMIT :limit_rows OFFSET :offset_rows
        ";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit_rows', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset_rows', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildPageResult($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $page, $perPage, $totalItems);
    }

    public function getPopularRoomTypeDistribution(): array
    {
        $sql = "
            SELECT rt.id, rt.name, COUNT(*) AS room_count
            FROM rooms r
            INNER JOIN room_types rt ON rt.id = r.room_type_id
            GROUP BY rt.id, rt.name
            ORDER BY room_count DESC, rt.name ASC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Phòng còn trống trong khoảng [check_in, check_out] (không trùng lịch).
     * Chỉ lấy booking status theo BookingStatus::statusesThatBlockRoom(); cancelled không chặn.
     */
    public function getAvailableInRange(string $check_in, string $check_out, ?int $room_type_id = null): array
    {
        $blockStatuses = BookingStatus::statusesThatBlockRoom();
        $placeholders = implode(',', array_fill(0, count($blockStatuses), '?'));
        $sql = "
            SELECT r.*, rt.name AS room_type_name, rt.capacity_adults, rt.capacity_children, rt.base_price,
            (SELECT image_url FROM images i WHERE i.entity_type = 'room_type' AND i.entity_id = rt.id ORDER BY i.id ASC LIMIT 1) as image_url
            FROM `{$this->table}` r
            INNER JOIN `room_types` rt ON rt.id = r.room_type_id
            WHERE r.id NOT IN (
                SELECT bd.room_id
                FROM `bookings` b
                JOIN `booking_details` bd ON b.id = bd.booking_id
                WHERE b.status IN ($placeholders)
                /* Overlap Logic - Áp dụng De Morgan: (StartA < EndB) AND (EndA > StartB) */
                AND (bd.check_out_date > ? AND bd.check_in_date < ?)
            )
            AND COALESCE(r.display_status, LOWER(r.status)) NOT IN ('maintenance', 'blocked', 'out_of_service', 'damaged')
        ";
        $bind = array_merge($blockStatuses, [$check_in, $check_out]);
        if ($room_type_id !== null) {
            $sql .= " AND r.room_type_id = ?";
            $bind[] = $room_type_id;
        }
        $sql .= " ORDER BY rt.base_price ASC, r.floor ASC, r.room_number ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bind);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Update room status and log the transition
     */
    public function updateStatus(int $id, string $newStatus, ?int $userId = null, ?string $notes = null): bool
    {
        $room = $this->findById($id);
        if (!$room) return false;

        $oldStatus = $room['status'];
        
        // Log transition
        if ($oldStatus !== $newStatus) {
            $sqlLog = "INSERT INTO `room_status_logs` (room_id, old_status, new_status, user_id, notes) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sqlLog);
            $stmt->execute([$id, $oldStatus, $newStatus, $userId, $notes]);
        }

        // Update room
        return $this->update($id, ['status' => $newStatus]);
    }
}

