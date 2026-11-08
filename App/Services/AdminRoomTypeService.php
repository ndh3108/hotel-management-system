<?php

namespace App\Services;

use App\Models\RoomType;

/**
 * AdminRoomTypeService
 * - Gom logic CRUD loại phòng cho khu vực admin.
 */
class AdminRoomTypeService
{
    private RoomType $roomTypeModel;

    public function __construct()
    {
        $this->roomTypeModel = new RoomType();
    }

    public function listRoomTypes(): array
    {
        return $this->roomTypeModel->getAll();
    }

    public function findRoomType(int $id): ?array
    {
        $id = (int) $id;
        if ($id <= 0) {
            return null;
        }
        /** @var array|null $item */
        $item = $this->roomTypeModel->findById($id);
        return $item ?: null;
    }

    /**
     * Tạo loại phòng từ input thô.
     * @return array{success: bool, id: int|null, error: ?string}
     */
    public function createFromRequest(array $input): array
    {
        $name      = trim((string) ($input['name'] ?? ''));
        $capacity  = (int) ($input['capacity'] ?? 0);
        $basePrice = $input['base_price'] !== '' ? (float) $input['base_price'] : null;

        if ($name === '' || $capacity <= 0) {
            return ['success' => false, 'id' => null, 'error' => 'invalid_input'];
        }

        $data = [
            'name'       => $name,
            'capacity'   => $capacity,
            'base_price' => $basePrice,
        ];

        $id = $this->roomTypeModel->create($data);

        return [
            'success' => (bool) $id,
            'id'      => $id ?: null,
            'error'   => $id ? null : 'create_failed',
        ];
    }

    /**
     * Cập nhật loại phòng từ input thô.
     * @return array{success: bool, error: ?string}
     */
    public function updateFromRequest(int $id, array $input): array
    {
        $id = (int) $id;
        if ($id <= 0) {
            return ['success' => false, 'error' => 'invalid_id'];
        }

        $existing = $this->roomTypeModel->findById($id);
        if (!$existing) {
            return ['success' => false, 'error' => 'not_found'];
        }

        $name      = trim((string) ($input['name'] ?? $existing['name'] ?? ''));
        $capacity  = (int) ($input['capacity'] ?? $existing['capacity'] ?? 0);
        $basePriceInput = $input['base_price'] ?? null;
        $basePrice = $basePriceInput !== '' ? (float) $basePriceInput : null;

        if ($name === '' || $capacity <= 0) {
            return ['success' => false, 'error' => 'invalid_input'];
        }

        $data = [
            'name'       => $name,
            'capacity'   => $capacity,
            'base_price' => $basePrice,
        ];

        $ok = $this->roomTypeModel->update($id, $data);

        return [
            'success' => (bool) $ok,
            'error'   => $ok ? null : 'update_failed',
        ];
    }

    public function deleteRoomType(int $id): bool
    {
        $id = (int) $id;
        if ($id <= 0) {
            return false;
        }
        return (bool) $this->roomTypeModel->delete($id);
    }
}

