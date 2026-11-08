<?php

namespace App\Services;

use App\Core\BookingStatus;
use App\Core\Database;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Service;

/**
 * RoomService - gom toàn bộ logic tìm phòng/validate datetime để Controller mỏng.
 */
class RoomService
{
    private RoomType $roomTypeModel;
    private Room $roomModel;
    private Service $serviceModel;
    private \PDO $pdo;
    private static ?array $popularRoomTypeCache = null;

    public function __construct()
    {
        $this->roomTypeModel = new RoomType();
        $this->roomModel = new Room();
        $this->serviceModel = new Service();
        $this->pdo = Database::getInstance()->getConnection();
    }

    /** Xem loại phòng + addon services */
    public function getRoomTypesForDisplay(): array
    {
        return [
            'roomTypes' => $this->roomTypeModel->getAllWithImages(),
            'addons'    => method_exists($this->serviceModel, 'getWithTypeName')
                ? $this->serviceModel->getWithTypeName()
                : [],
        ];
    }

    public function getRoomTypeOptions(): array
    {
        return $this->roomTypeModel->getAll();
    }

    public function searchRooms(array $filters, int $pageNumber = 1, int $perPage = 15): array
    {
        return $this->roomModel->getFilteredPaginatedWithType($filters, $pageNumber, $perPage);
    }

    public function updateRoomStatus(int $roomId, string $newStatus, ?int $userId = null, ?string $notes = null): bool
    {
        return $this->roomModel->updateStatus($roomId, $newStatus, $userId, $notes);
    }

    public function getPopularRoomTypes(int $limit = 5): array
    {
        if (self::$popularRoomTypeCache !== null) {
            return array_slice(self::$popularRoomTypeCache, 0, $limit);
        }

        $popular = $this->roomModel->getPopularRoomTypeDistribution();
        self::$popularRoomTypeCache = $popular;

        return array_slice($popular, 0, $limit);
    }

    /**
     * Parse datetime input từ UI/URL sang DB datetime.
     * @return array{ok: bool, db: string, local: string}
     */
    public function parseDateTimeInput(string $value): array
    {
        $value = trim($value);
        if ($value === '') {
            return ['ok' => false, 'db' => '', 'local' => ''];
        }
        $formats = ['Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i'];
        foreach ($formats as $fmt) {
            $dt = \DateTimeImmutable::createFromFormat($fmt, $value);
            if ($dt instanceof \DateTimeImmutable) {
                return [
                    'ok'    => true,
                    'db'    => $dt->format('Y-m-d H:i:s'),
                    'local' => $dt->format('Y-m-d\TH:i'),
                ];
            }
        }
        $ts = strtotime($value);
        if ($ts === false) {
            return ['ok' => false, 'db' => '', 'local' => ''];
        }
        $dt = (new \DateTimeImmutable())->setTimestamp($ts);
        return [
            'ok'    => true,
            'db'    => $dt->format('Y-m-d H:i:s'),
            'local' => $dt->format('Y-m-d\TH:i'),
        ];
    }

    /**
     * Tìm phòng trống theo datetime (check-in/out), loại phòng.
     * Controller chỉ gọi method này và render kết quả.
     */
    public function searchAvailableRooms(?string $checkInRaw, ?string $checkOutRaw, ?int $roomTypeId): array
    {
        $roomTypes = $this->roomTypeModel->getAll();
        $rooms = [];

        $checkInRaw = (string) ($checkInRaw ?? '');
        $checkOutRaw = (string) ($checkOutRaw ?? '');

        $checkIn = $this->parseDateTimeInput($checkInRaw);
        $checkOut = $this->parseDateTimeInput($checkOutRaw);

        $error = null;
        if ($checkInRaw !== '' || $checkOutRaw !== '') {
            if (!$checkIn['ok'] || !$checkOut['ok']) {
                $error = 'missing';
            } elseif ($checkIn['db'] >= $checkOut['db']) {
                $error = 'dates';
            } else {
                $rooms = $this->getAvailableInRange($checkIn['db'], $checkOut['db'], $roomTypeId);
            }
        }

        return [
            'roomTypes'  => $roomTypes,
            'rooms'      => $rooms,
            'checkIn'    => $checkIn['local'] ?: $checkInRaw,
            'checkOut'   => $checkOut['local'] ?: $checkOutRaw,
            'roomTypeId' => $roomTypeId,
            'error'      => $error,
        ];
    }

    /**
     * Core tìm phòng trống theo khoảng thời gian + loại phòng.
     * Được tách ra khỏi Model để model chỉ CRUD dữ liệu.
     */
    private function getAvailableInRange(string $check_in, string $check_out, ?int $room_type_id = null): array
    {
        $blockStatuses = BookingStatus::statusesThatBlockRoom();
        $placeholders = implode(',', array_fill(0, count($blockStatuses), '?'));
        $sql = "
            SELECT r.*, rt.name AS room_type_name, rt.capacity, rt.base_price
            FROM `room_details` r
            INNER JOIN `room_types` rt ON rt.id = r.room_type_id
            WHERE r.id NOT IN (
                SELECT bd.room_id
                FROM `bookings` b
                JOIN `booking_details` bd ON b.id = bd.booking_id
                WHERE b.status IN ($placeholders)
                AND b.check_in < ?
                AND b.check_out > ?
            )
        ";
        $bind = array_merge($blockStatuses, [$check_out, $check_in]);
        if ($room_type_id !== null) {
            $sql .= " AND r.room_type_id = ?";
            $bind[] = $room_type_id;
        }
        $sql .= " ORDER BY rt.base_price ASC, r.room_number ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bind);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}

