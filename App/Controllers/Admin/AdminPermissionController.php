<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Domain\Services\RBAC\RBACPermissionService;

class AdminPermissionController extends Controller
{
    private RBACPermissionService $permissionService;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->useLayout = true;

        $this->permissionService = new RBACPermissionService();
    }

    public function index(): void
    {
        $permissions = $this->permissionService->listPermissions();
        $this->render('Admin/permissions/index_v2', [
            'title' => 'Quản lý Permissions - Admin',
            'pageTitle' => 'Permissions',
            'currentPage' => 'permissions',
            'permissions' => $permissions,
        ]);
    }

    public function create(): void
    {
        $this->render('Admin/permissions/form', [
            'title' => 'Tạo Permission - Admin',
            'pageTitle' => 'Tạo Permission',
            'currentPage' => 'permissions',
            'permission' => null,
        ]);
    }

    public function store(): void
    {
        $data = [
            'name' => (string) $this->input('name'),
            'code' => (string) $this->input('code', ''),
            'module' => (string) $this->input('module', ''),
            'description' => (string) $this->input('description', ''),
        ];

        $id = $this->permissionService->createPermission($data);
        if (!$id) {
            $_SESSION['error'] = 'Tạo permission thất bại.';
            $this->redirect(\App\Core\url('/admin/permissions/create'));
            return;
        }

        $_SESSION['success'] = 'Tạo permission thành công.';
        $this->redirect(\App\Core\url('/admin/permissions/' . $id . '/edit'));
    }

    public function edit(int $id): void
    {
        $permission = $this->permissionService->findPermission($id);
        if (!$permission) {
            $_SESSION['error'] = 'Không tìm thấy permission.';
            $this->redirect(\App\Core\url('/admin/permissions'));
            return;
        }

        $this->render('Admin/permissions/form', [
            'title' => 'Chỉnh sửa Permission - Admin',
            'pageTitle' => 'Chỉnh sửa',
            'currentPage' => 'permissions',
            'permission' => $permission,
        ]);
    }

    public function update(int $id): void
    {
        $data = [
            'name' => (string) $this->input('name'),
            'code' => (string) $this->input('code', ''),
            'module' => (string) $this->input('module', ''),
            'description' => (string) $this->input('description', ''),
            'is_active' => (string) $this->input('is_active', '1') === '1' ? 1 : 0,
        ];

        $ok = $this->permissionService->updatePermission($id, $data);
        if (!$ok) {
            $_SESSION['error'] = 'Cập nhật permission thất bại.';
            $this->redirect(\App\Core\url('/admin/permissions/' . $id . '/edit'));
            return;
        }

        $_SESSION['success'] = 'Cập nhật permission thành công.';
        $this->redirect(\App\Core\url('/admin/permissions/' . $id . '/edit'));
    }

    public function disable(int $id): void
    {
        $ok = $this->permissionService->disablePermission($id);
        $_SESSION[$ok ? 'success' : 'error'] = $ok ? 'Đã vô hiệu hóa permission.' : 'Vô hiệu hóa thất bại.';
        $this->redirect(\App\Core\url('/admin/permissions'));
    }

    public function destroy(int $id): void
    {
        $ok = $this->permissionService->softDeletePermission($id);
        $_SESSION[$ok ? 'success' : 'error'] = $ok ? 'Đã xóa mềm permission.' : 'Xóa permission thất bại.';
        $this->redirect(\App\Core\url('/admin/permissions'));
    }
}

