<?php

namespace App\Services;

use App\Models\Service;

/**
 * AdminServiceService
 * - Gom logic CRUD dịch vụ (services) cho khu vực admin.
 */
class AdminServiceService
{
    private Service $serviceModel;

    public function __construct()
    {
        $this->serviceModel = new Service();
    }

    public function listServices(): array
    {
        return $this->serviceModel->getAll();
    }

    public function findService(int $id): ?array
    {
        $id = (int) $id;
        if ($id <= 0) {
            return null;
        }
        /** @var array|null $item */
        $item = $this->serviceModel->findById($id);
        return $item ?: null;
    }

    /**
     * Tạo dịch vụ từ input thô.
     * @return array{success: bool, id: int|null, error: ?string}
     */
    public function createFromRequest(array $input): array
    {
        $name        = trim((string) ($input['name'] ?? ''));
        $priceInput  = $input['price'] ?? null;
        $price       = $priceInput !== '' ? (float) $priceInput : null;
        $description = trim((string) ($input['description'] ?? ''));
        $type        = trim((string) ($input['type'] ?? ''));
        $isActive    = (int) ($input['is_active'] ?? 1);

        if ($name === '') {
            return ['success' => false, 'id' => null, 'error' => 'invalid_input'];
        }

        $data = [
            'name'        => $name,
            'price'       => $price,
            'description' => $description,
            'type'        => $type,
            'is_active'   => $isActive,
        ];

        $id = $this->serviceModel->create($data);

        return [
            'success' => (bool) $id,
            'id'      => $id ?: null,
            'error'   => $id ? null : 'create_failed',
        ];
    }

    /**
     * Cập nhật dịch vụ từ input thô.
     * @return array{success: bool, error: ?string}
     */
    public function updateFromRequest(int $id, array $input): array
    {
        $id = (int) $id;
        if ($id <= 0) {
            return ['success' => false, 'error' => 'invalid_id'];
        }

        $existing = $this->serviceModel->findById($id);
        if (!$existing) {
            return ['success' => false, 'error' => 'not_found'];
        }

        $name        = trim((string) ($input['name'] ?? $existing['name'] ?? ''));
        $priceInput  = $input['price'] ?? ($existing['price'] ?? null);
        $price       = $priceInput !== '' ? (float) $priceInput : null;
        $description = trim((string) ($input['description'] ?? $existing['description'] ?? ''));
        $type        = trim((string) ($input['type'] ?? $existing['type'] ?? ''));
        $isActive    = (int) ($input['is_active'] ?? $existing['is_active'] ?? 1);

        if ($name === '') {
            return ['success' => false, 'error' => 'invalid_input'];
        }

        $data = [
            'name'        => $name,
            'price'       => $price,
            'description' => $description,
            'type'        => $type,
            'is_active'   => $isActive,
        ];

        $ok = $this->serviceModel->update($id, $data);

        return [
            'success' => (bool) $ok,
            'error'   => $ok ? null : 'update_failed',
        ];
    }

    public function deleteService(int $id): bool
    {
        $id = (int) $id;
        if ($id <= 0) {
            return false;
        }
        return (bool) $this->serviceModel->delete($id);
    }
}

