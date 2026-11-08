<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Domain\Services\RBAC\RBACRoleService;

class AdminRoleController extends Controller
{
    private RBACRoleService $roleService;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->useLayout = true;

        $this->roleService = new RBACRoleService();
    }

    public function index(): void
    {
        $roles = $this->roleService->listRoles();

        $this->render('Admin/roles/index_v2', [
            'title' => 'Quản lý Vai trò - Admin',
            'pageTitle' => 'Vai trò',
            'currentPage' => 'roles',
            'roles' => $roles,
        ]);
    }

    public function create(): void
    {
        $this->render('Admin/roles/form', [
            'title' => 'Tạo Vai trò - Admin',
            'pageTitle' => 'Tạo Vai trò',
            'currentPage' => 'roles',
            'role' => null,
            'permissionMatrix' => [],
        ]);
    }

    public function store(): void
    {
        $data = [
            'name' => (string) $this->input('name'),
            'code' => (string) $this->input('code', ''),
            'description' => (string) $this->input('description', ''),
            'sort_order' => (int) $this->input('sort_order', 0),
        ];

        $id = $this->roleService->createRole($data);
        if (!$id) {
            $_SESSION['error'] = 'Tạo vai trò thất bại.';
            $this->redirect(\App\Core\url('/admin/roles/create'));
            return;
        }

        $_SESSION['success'] = 'Tạo vai trò thành công.';
        $this->redirect(\App\Core\url('/admin/roles/' . $id . '/edit'));
    }

    public function edit(int $id): void
    {
        $role = $this->roleService->findRole($id);
        if (!$role) {
            $_SESSION['error'] = 'Không tìm thấy vai trò.';
            $this->redirect(\App\Core\url('/admin/roles'));
            return;
        }

        $permissionMatrix = $this->roleService->getPermissionsMatrixForRole($id);

        $this->render('Admin/roles/form', [
            'title' => 'Chỉnh sửa Vai trò - Admin',
            'pageTitle' => 'Chỉnh sửa',
            'currentPage' => 'roles',
            'role' => $role,
            'permissionMatrix' => $permissionMatrix,
        ]);
    }

    public function update(int $id): void
    {
        $data = [
            'name' => (string) $this->input('name'),
            'code' => (string) $this->input('code', ''),
            'description' => (string) $this->input('description', ''),
            'sort_order' => (int) $this->input('sort_order', 0),
            'is_active' => (string) $this->input('is_active', '1') === '1' ? 1 : 0,
        ];

        $ok = $this->roleService->updateRole($id, $data);
        if (!$ok) {
            $_SESSION['error'] = 'Cập nhật vai trò thất bại.';
            $this->redirect(\App\Core\url('/admin/roles/' . $id . '/edit'));
            return;
        }

        $_SESSION['success'] = 'Cập nhật vai trò thành công.';
        $this->redirect(\App\Core\url('/admin/roles/' . $id . '/edit'));
    }

    public function destroy(int $id): void
    {
        $ok = $this->roleService->softDeleteRole($id);
        if (!$ok) {
            $_SESSION['error'] = 'Không thể xóa vai trò hệ thống hoặc vai trò không tồn tại.';
            $this->redirect(\App\Core\url('/admin/roles'));
            return;
        }

        $_SESSION['success'] = 'Đã xóa mềm vai trò.';
        $this->redirect(\App\Core\url('/admin/roles'));
    }
}

