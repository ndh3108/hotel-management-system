<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;

/**
 * AdminCustomerService
 * - Gom toàn bộ logic CRUD khách hàng cho khu vực admin.
 * - Controller chỉ: lấy input, gọi service, redirect/render.
 * - Validate cơ bản + hash mật khẩu đặt trong service.
 */
class AdminCustomerService
{
    private User $userModel;
    private Role $roleModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    /** Danh sách khách hàng */
    public function listCustomers(): array
    {
        return $this->userModel->getAll();
    }

    /** Danh sách role dùng cho form create/edit */
    public function listRoles(): array
    {
        return $this->roleModel->getAll();
    }

    /** Lấy chi tiết 1 khách hàng */
    public function findCustomer(int $id): ?array
    {
        $id = (int) $id;
        if ($id <= 0) {
            return null;
        }
        /** @var array|null $item */
        $item = $this->userModel->findById($id);
        return $item ?: null;
    }

    /**
     * Tạo khách hàng mới từ input thô của Controller.
     * Trả về:
     * - ['success' => bool, 'id' => int|null, 'error' => string|null]
     */
    public function createFromRequest(array $input): array
    {
        $roleId   = (int) ($input['role_id'] ?? 0);
        $fullName = trim((string) ($input['full_name'] ?? ''));
        $email    = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $phone    = trim((string) ($input['phone'] ?? ''));
        $status   = (int) ($input['status'] ?? 1);

        if ($email === '' || $password === '') {
            return [
                'success' => false,
                'id'      => null,
                'error'   => 'missing_credentials',
            ];
        }

        $data = [
            'role_id'   => $roleId,
            'full_name' => $fullName,
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'phone'     => $phone,
            'status'    => $status,
        ];

        $id = $this->userModel->create($data);

        return [
            'success' => (bool) $id,
            'id'      => $id ?: null,
            'error'   => $id ? null : 'create_failed',
        ];
    }

    /**
     * Cập nhật khách hàng từ input thô.
     * Trả về:
     * - ['success' => bool, 'error' => string|null]
     */
    public function updateFromRequest(int $id, array $input): array
    {
        $id = (int) $id;
        if ($id <= 0) {
            return ['success' => false, 'error' => 'invalid_id'];
        }

        $existing = $this->userModel->findById($id);
        if (!$existing) {
            return ['success' => false, 'error' => 'not_found'];
        }

        $roleId   = (int) ($input['role_id'] ?? $existing['role_id'] ?? 0);
        $fullName = trim((string) ($input['full_name'] ?? $existing['full_name'] ?? ''));
        $email    = trim((string) ($input['email'] ?? $existing['email'] ?? ''));
        $phone    = trim((string) ($input['phone'] ?? $existing['phone'] ?? ''));
        $status   = (int) ($input['status'] ?? $existing['status'] ?? 1);
        $passwordRaw = (string) ($input['password'] ?? '');

        if ($email === '') {
            return ['success' => false, 'error' => 'missing_email'];
        }

        $data = [
            'role_id'   => $roleId,
            'full_name' => $fullName,
            'email'     => $email,
            'phone'     => $phone,
            'status'    => $status,
        ];

        if ($passwordRaw !== '') {
            $data['password'] = password_hash($passwordRaw, PASSWORD_DEFAULT);
        }

        $ok = $this->userModel->update($id, $data);

        return [
            'success' => (bool) $ok,
            'error'   => $ok ? null : 'update_failed',
        ];
    }

    /** Xóa khách hàng */
    public function deleteCustomer(int $id): bool
    {
        $id = (int) $id;
        if ($id <= 0) {
            return false;
        }
        return (bool) $this->userModel->delete($id);
    }
}


