<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Domain\Services\AuditLogService;
use App\Models\User;

class AuditLogController extends Controller
{
    private AuditLogService $auditLogService;
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->useLayout = true;
        
        $this->auditLogService = new AuditLogService();
        $this->userModel = new User();
    }

    public function index(): void
    {
        $this->authorize('USER_MANAGE');

        $filters = [
            'user_id' => $this->input('user_id'),
            'action' => $this->input('action'),
            'table_name' => $this->input('table_name'),
            'start_date' => $this->input('start_date'),
            'end_date' => $this->input('end_date'),
        ];
        
        $cursorId = $this->input('cursor_id') !== null && $this->input('cursor_id') !== ''
            ? (int) $this->input('cursor_id')
            : null;
        $limit = 50;

        $page = $this->auditLogService->getCursorLogs($filters, $cursorId, $limit);

        // Fetch users for dropdown filter
        $users = $this->userModel->getAll();

        $this->render('Admin/audit_logs/index', [
            'title' => 'Audit Logs - Admin',
            'pageTitle' => 'Lịch Sử Hoạt Động',
            'currentPage' => 'audit_logs',
            'logs' => $page['items'],
            'filters' => $filters,
            'nextCursorId' => $page['next_cursor_id'],
            'users' => $users
        ]);
    }
}

