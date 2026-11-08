<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;
use App\Models\Role;
use App\Services\UserService;
use App\Models\UserProfile;
use App\Models\UserRole;

class UserController extends Controller
{
    private User $userModel;
    private Role $roleModel;
    private UserService $userService;
    private UserProfile $userProfileModel;
    private UserRole $userRoleModel;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->userModel = new User();
        $this->roleModel = new Role();
        $this->userService = new UserService();
        $this->userProfileModel = new UserProfile();
        $this->userRoleModel = new UserRole();
    }

    public function index(): void
    {
        $roleId = $this->input('role_id');
        $cursor = (int) $this->input('cursor', 0);
        $pageNumber = max(1, (int) $this->input('page', 1));
        
        $selectedRoleId = $roleId !== null && $roleId !== '' ? (int) $roleId : null;
        $page = $this->userModel->getPaginatedWithRole($cursor, 10, $selectedRoleId);
        $roleAssignments = $this->userRoleModel->getAssignmentsForUsers(array_map(
            fn (array $user): int => (int) ($user['id'] ?? 0),
            $page['items']
        ));

        $users = array_map(function (array $user) use ($roleAssignments): array {
            $assignments = $roleAssignments[(int) ($user['id'] ?? 0)] ?? [];
            $activeRoles = array_values(array_filter($assignments, fn (array $assignment): bool => $assignment['is_assignment_active']));
            $expiredRoles = array_values(array_filter($assignments, fn (array $assignment): bool => $assignment['is_expired']));

            $user['role_assignments'] = $assignments;
            $user['active_role_count'] = count($activeRoles);
            $user['expired_role_count'] = count($expiredRoles);
            $user['primary_role_name'] = $activeRoles[0]['name'] ?? ($user['role_name'] ?? null);
            $user['profile_completion_percent'] = (int) ($user['profile_completion_percent'] ?? 0);
            return $user;
        }, $page['items']);

        $roles = $this->roleModel->getActiveRoles();

        $this->useLayout = true;
        $this->render('Admin/users/index_v2', [
            'title' => 'Quản lý người dùng - Admin',
            'pageTitle' => 'Quản Lý Người Dùng',
            'currentPage' => 'users',
            'users' => $users,
            'roles' => $roles,
            'selectedRole' => $selectedRoleId,
            'hasMore' => $page['has_more'],
            'nextCursor' => $page['next_last_id'],
            'pageNumber' => $pageNumber,
        ]);
    }

    public function create(): void
    {
        $roles = $this->roleModel->getActiveRoles();
        $this->useLayout = true;
        $this->render('Admin/users/form_v2', [
            'title' => 'Thêm người dùng - Admin',
            'pageTitle' => 'Thêm Người Dùng Mới',
            'currentPage' => 'users',
            'roles' => $roles,
            'user' => null,
            'userProfile' => null,
        ]);
    }

    public function store(): void
    {
        $roleIds = $this->normalizeRoleIds($this->input('roles') ?? []);
        $status = $this->normalizeStatus($this->input('status'));
        $password = (string) $this->input('password', '');

        if ($password === '') {
            $_SESSION['error'] = 'Mật khẩu là bắt buộc khi tạo người dùng.';
            $this->redirect(\App\Core\url('/admin/users/create'));
            return;
        }

        $data = [
            'role_id' => !empty($roleIds) ? $roleIds[0] : null,
            'full_name' => trim((string) $this->input('full_name')),
            'email' => trim((string) $this->input('email')),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => $this->normalizePhone($this->input('phone')),
            'status' => $status,
        ];

        if ($this->hasDuplicateIdentity($data['email'], $data['phone'])) {
            $_SESSION['error'] = 'Email hoặc số điện thoại đã tồn tại.';
            $this->redirect(\App\Core\url('/admin/users/create'));
            return;
        }

        $id = $this->userModel->create($data);
        if ($id) {
            $this->syncUserRoles($id);
            $this->userService->ensureProfile($id);
            $this->userService->updateAdminNote($id, (string) $this->input('admin_note', ''));
        }
        $this->redirect(\App\Core\url('/admin/users'));
    }

    public function edit(int $id): void
    {
        $user = $this->userModel->findById($id);
        $roles = $this->roleModel->getActiveRoles();
        $userProfile = $this->userProfileModel->findByUserId($id);
        $activities = $this->userService->getRecentActivities($id, 6);
        $sessions = $this->userService->getRecentSessions($id, 6);
        
        $userRoles = [];
        $userRolesData = (new \App\Models\UserRole())->getUserRoles($id);
        foreach ($userRolesData as $ur) {
            $userRoles[$ur['id']] = $ur['expires_at'] ? substr($ur['expires_at'], 0, 10) : '';
        }

        $this->useLayout = true;
        $this->render('Admin/users/form_v2', [
            'title' => 'Sửa người dùng - Admin',
            'pageTitle' => 'Chỉnh Sửa Người Dùng',
            'currentPage' => 'users',
            'roles' => $roles,
            'user' => $user,
            'userRoles' => $userRoles,
            'userProfile' => $userProfile,
            'activities' => $activities,
            'sessions' => $sessions,
        ]);
    }

    public function update(int $id): void
    {
        $roleIds = $this->normalizeRoleIds($this->input('roles') ?? []);
        $status = $this->normalizeStatus($this->input('status'));
        $email = trim((string) $this->input('email'));
        $phone = $this->normalizePhone($this->input('phone'));

        $data = [
            'role_id' => !empty($roleIds) ? $roleIds[0] : null,
            'full_name' => trim((string) $this->input('full_name')),
            'email' => $email,
            'phone' => $phone,
            'status' => $status,
        ];

        if ($this->hasDuplicateIdentity($email, $phone, $id)) {
            $_SESSION['error'] = 'Email hoặc số điện thoại đã tồn tại.';
            $this->redirect(\App\Core\url('/admin/users/' . $id . '/edit'));
            return;
        }

        if ($this->input('password')) {
            $data['password_hash'] = password_hash((string) $this->input('password'), PASSWORD_DEFAULT);
        }
        $this->userModel->update($id, $data);
        $this->syncUserRoles($id);
        $this->userService->ensureProfile($id);
        $this->userService->updateAdminNote($id, (string) $this->input('admin_note', ''));
        $this->redirect(\App\Core\url('/admin/users'));
    }

    private function syncUserRoles(int $userId): void
    {
        $roleIds = $this->normalizeRoleIds($_POST['roles'] ?? []);
        $roleExpiries = $_POST['role_expiries'] ?? [];

        $rbac = new \App\Domain\Services\RBACService();
        $userRoleModel = new \App\Models\UserRole();

        $newRoleIds = array_values(array_unique(array_map('intval', (array) $roleIds)));

        $existingRoles = $userRoleModel->getUserRoles($userId);
        $existingRoleIds = array_map(fn($ur) => (int) ($ur['id'] ?? 0), $existingRoles);

        $rolesToRevoke = array_values(array_diff($existingRoleIds, $newRoleIds));

        // Revoke roles not present anymore.
        foreach ($rolesToRevoke as $rId) {
            $rbac->revokeRoleFromUser($userId, $rId);
        }

        // Assign/update roles.
        foreach ($newRoleIds as $rId) {
            $expiresAt = !empty($roleExpiries[$rId]) ? $roleExpiries[$rId] . ' 23:59:59' : null;
            $rbac->assignRoleToUser($userId, $rId, $expiresAt);
        }
    }

    private function normalizeRoleIds(array $roleIds): array
    {
        return array_values(array_unique(array_filter(array_map('intval', $roleIds), fn ($id) => $id > 0)));
    }

    private function normalizeStatus(mixed $status): int
    {
        return in_array((string) $status, ['0', 'inactive'], true) ? 0 : 1;
    }

    private function normalizePhone(mixed $phone): ?string
    {
        $normalized = trim((string) $phone);
        return $normalized !== '' ? $normalized : null;
    }

    private function hasDuplicateIdentity(string $email, ?string $phone, ?int $ignoreUserId = null): bool
    {
        $existingByEmail = $this->userModel->findByEmail($email);
        if ($existingByEmail && (int) ($existingByEmail['id'] ?? 0) !== (int) ($ignoreUserId ?? 0)) {
            return true;
        }

        if ($phone !== null) {
            $existingByPhone = $this->userModel->findByPhone($phone);
            if ($existingByPhone && (int) ($existingByPhone['id'] ?? 0) !== (int) ($ignoreUserId ?? 0)) {
                return true;
            }
        }

        return false;
    }

    public function destroy(int $id): void
    {
        $this->userModel->delete($id);
        $this->redirect(\App\Core\url('/admin/users'));
    }
}

