<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Domain\Services\RBAC\RBACRoleService;
use App\Domain\Services\RBAC\RBACPermissionService;

class RBACController extends Controller
{
    private RBACRoleService $roleService;
    private RBACPermissionService $permissionService;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->roleService = new RBACRoleService();
        $this->permissionService = new RBACPermissionService();
    }

    public function index()
    {
        $this->useLayout = true;
        $roles = $this->roleService->listRoles();
        $permissions = $this->permissionService->listPermissions();
        $activeRoleCount = count(array_filter($roles, fn (array $role): bool => (int) ($role['is_active'] ?? 1) === 1));
        $systemRoleCount = count(array_filter($roles, fn (array $role): bool => (int) ($role['is_system'] ?? 0) === 1));
        $activePermissionCount = count(array_filter($permissions, fn (array $permission): bool => (int) ($permission['is_active'] ?? 1) === 1));
        $this->render('Admin/rbac/matrix_v2', [
            'title' => 'Phân quyền Matrix - Admin',
            'pageTitle' => 'Ma trận Phân Quyền Vai Trò',
            'currentPage' => 'rbac',
            'roleCount' => count($roles),
            'permissionCount' => count($permissions),
            'activeRoleCount' => $activeRoleCount,
            'systemRoleCount' => $systemRoleCount,
            'activePermissionCount' => $activePermissionCount,
        ]);
    }
}

