<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomType;

/**
 * AdminRoomService
 * - Gom logic CRUD phòng cho khu vực admin.
 * - Controller chỉ điều phối, không thao tác Model trực tiếp.
 */
class AdminRoomService
{
    private Room $roomModel;
    private RoomType $roomTypeModel;

    public function __construct()
    {
        $this->roomModel = new Room();
        $this->roomTypeModel = new RoomType();
    }

    public function listRooms(): array
    {
        return $this->roomModel->getAll();
    }

    public function listRoomTypes(): array
    {
        return $this->roomTypeModel->getAll();
    }

    public function findRoom(int $id): ?array
    {
        $id = (int) $id;
        if ($id <= 0) {
            return null;
        }
        /** @var array|null $item */
        $item = $this->roomModel->findById($id);
        return $item ?: null;
    }

    /**
     * Tạo phòng từ input thô.
     * @return array{success: bool, id: int|null, error: ?string}
     */
    public function createFromRequest(array $input): array
    {
        $roomTypeId = (int) ($input['room_type_id'] ?? 0);
        $roomNumber = trim((string) ($input['room_number'] ?? ''));
        $status     = (string) ($input['status'] ?? 'available');

        if ($roomTypeId <= 0 || $roomNumber === '') {
            return ['success' => false, 'id' => null, 'error' => 'invalid_input'];
        }

        $data = [
            'room_type_id' => $roomTypeId,
            'room_number'  => $roomNumber,
            'status'       => $status,
        ];

        $id = $this->roomModel->create($data);

        return [
            'success' => (bool) $id,
            'id'      => $id ?: null,
            'error'   => $id ? null : 'create_failed',
        ];
    }

    /**
     * Cập nhật phòng từ input thô.
     * @return array{success: bool, error: ?string}
     */
    public function updateFromRequest(int $id, array $input): array
    {
        $id = (int) $id;
        if ($id <= 0) {
            return ['success' => false, 'error' => 'invalid_id'];
        }

        $existing = $this->roomModel->findById($id);
        if (!$existing) {
            return ['success' => false, 'error' => 'not_found'];
        }

        $roomTypeId = (int) ($input['room_type_id'] ?? $existing['room_type_id'] ?? 0);
        $roomNumber = trim((string) ($input['room_number'] ?? $existing['room_number'] ?? ''));
        $status     = (string) ($input['status'] ?? $existing['status'] ?? 'available');

        if ($roomTypeId <= 0 || $roomNumber === '') {
            return ['success' => false, 'error' => 'invalid_input'];
        }

        $data = [
            'room_type_id' => $roomTypeId,
            'room_number'  => $roomNumber,
            'status'       => $status,
        ];

        $ok = $this->roomModel->update($id, $data);

        return [
            'success' => (bool) $ok,
            'error'   => $ok ? null : 'update_failed',
        ];
    }

    public function deleteRoom(int $id): bool
    {
        $id = (int) $id;
        if ($id <= 0) {
            return false;
        }
        return (bool) $this->roomModel->delete($id);
    }
}

