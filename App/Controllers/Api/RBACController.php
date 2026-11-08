<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Models\User;
use App\Models\Permission;
use App\Models\UserRole;
use App\Domain\Services\RBAC\RBACRoleService;
use App\Domain\Services\RBAC\RBACPermissionService;

class RBACController extends Controller
{
    // GET /api/users
    public function users()
    {
        AuthMiddleware::requirePermission('USER_MANAGE');

        $userModel = new User();
        $userRoleModel = new UserRole();
        $sql = "SELECT u.id, u.full_name, u.email, u.phone, u.status, u.role_id, u.last_login_at, u.profile_completion_percent, r.name as role_name
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.deleted_at IS NULL
                ORDER BY u.id ASC";
        $stmt = $userModel->getConnection()->query($sql);
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $assignments = $userRoleModel->getAssignmentsForUsers(array_map(
            fn (array $user): int => (int) ($user['id'] ?? 0),
            $users
        ));

        $users = array_map(function (array $user) use ($assignments): array {
            $user['role_assignments'] = $assignments[(int) ($user['id'] ?? 0)] ?? [];
            return $user;
        }, $users);

        $this->json(['success' => true, 'data' => $users]);
    }

    // POST /api/roles
    public function storeRole()
    {
        AuthMiddleware::requirePermission('USER_MANAGE');

        $content = trim(file_get_contents("php://input"));
        $decoded = current(json_decode($content, true) ?: [$_POST]);
        if(!$decoded && $_POST) $decoded = $_POST;

        $name = trim($decoded['name'] ?? '');
        $description = trim($decoded['description'] ?? '');

        if (!$name) {
            return $this->json(['success' => false, 'message' => 'Name is required'], 400);
        }

        $roleService = new RBACRoleService();
        $id = $roleService->createRole([
            'name' => $name,
            'code' => (string) ($decoded['code'] ?? ''),
            'description' => $description,
            'sort_order' => (int) ($decoded['sort_order'] ?? 0),
        ]);

        if (!$id) {
            return $this->json(['success' => false, 'message' => 'Unable to create role'], 422);
        }

        $this->json([
            'success' => true,
            'data' => ['id' => $id, 'name' => $name, 'description' => $description]
        ]);
    }

    // GET /api/permissions
    public function permissions()
    {
        AuthMiddleware::requirePermission('USER_MANAGE');
        
        $permModel = new Permission();
        $roleService = new RBACRoleService();
        $permissionService = new RBACPermissionService();

        $roles = $roleService->listRoles();
        $allPerms = $permissionService->listPermissions();
        
        $matrix = [];
        foreach ($roles as $role) {
            $roleId = (int) ($role['id'] ?? 0);
            $rolePerms = $permModel->getRolePermissions($roleId);
            $matrix[$roleId] = array_map('intval', $rolePerms);
        }

        $this->json([
            'success' => true,
            'roles' => $roles,
            'permissions' => $allPerms,
            'matrix' => $matrix
        ]);
    }

    // PUT /api/permissions-to-role
    public function updateRolePermissions()
    {
        AuthMiddleware::requirePermission('USER_MANAGE');
        
        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content, true);
        
        $roleId = (int) ($decoded['role_id'] ?? 0);
        $permissionIds = array_values(array_unique(array_map('intval', $decoded['permission_ids'] ?? [])));

        if (!$roleId) {
            return $this->json(['success' => false, 'message' => 'Role ID is required'], 400);
        }

        $rbacService = new \App\Domain\Services\RBACService();
        $ok = $rbacService->updateRolePermissions($roleId, $permissionIds);

        if (!$ok) {
            return $this->json(['success' => false, 'message' => 'Unable to update permissions'], 422);
        }
        
        $this->json([
            'success' => true,
            'message' => 'Permissions updated successfully'
        ]);
    }

    // POST /api/users/{id}/roles
    public function assignRoleToUser($userId)
    {
        AuthMiddleware::requirePermission('USER_MANAGE');
        
        $content = trim(file_get_contents("php://input"));
        $decoded = current(json_decode($content, true) ?: [$_POST]);
        if(!$decoded && $_POST) $decoded = $_POST;

        $roleId = (int) ($decoded['role_id'] ?? 0);
        $expiresAt = $decoded['expires_at'] ?? null;

        if (!$userId || !$roleId) {
            return $this->json(['success' => false, 'message' => 'User ID and Role ID are required'], 400);
        }

        $rbacService = new \App\Domain\Services\RBACService();
        $success = $rbacService->assignRoleToUser((int)$userId, $roleId, $expiresAt);

        $this->json([
            'success' => $success,
            'message' => $success ? 'Role assigned' : 'Failed to assign role'
        ]);
    }

    // DELETE /api/users/{id}/roles/{role_id}
    public function revokeRoleFromUser($userId, $roleId)
    {
        AuthMiddleware::requirePermission('USER_MANAGE');
        
        if (!$userId || !$roleId) {
            return $this->json(['success' => false, 'message' => 'User ID and Role ID are required'], 400);
        }

        $rbacService = new \App\Domain\Services\RBACService();
        $success = $rbacService->revokeRoleFromUser((int)$userId, (int)$roleId);

        $this->json([
            'success' => $success, 
            'message' => $success ? 'Role revoked' : 'Failed to revoke role'
        ]);
    }
}

