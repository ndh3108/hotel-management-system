<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Domain\Services\LoggerService;

class SystemLogController extends Controller
{
    private LoggerService $loggerService;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->loggerService = new LoggerService();
    }

    public function index(): void
    {
        $this->authorize('REPORT_VIEW');

        $filters = [
            'resource_type' => $this->input('resource_type', ''),
            'severity' => $this->input('severity', '')
        ];
        
        $logs = $this->loggerService->getLogs($filters, 500);
        $export = $this->input('export');

        if ($export === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=system_logs_' . date('Y-m-d') . '.csv');
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'Trace ID', 'Actor ID', 'Actor Name', 'Role', 'Action', 'Resource Type', 'Resource ID', 'Status', 'Severity', 'IP', 'Created At']);
            foreach ($logs as $log) {
                fputcsv($output, [
                    $log['id'],
                    $log['trace_id'],
                    $log['actor_id'],
                    $log['actor_name'],
                    $log['role'],
                    $log['action'],
                    $log['resource_type'],
                    $log['resource_id'],
                    $log['status'],
                    $log['severity'],
                    $log['ip'],
                    $log['created_at']
                ]);
            }
            fclose($output);
            exit;
        }

        $this->useLayout = true;
        $this->render('Admin/system_logs/index', [
            'title' => 'System Logs - Admin',
            'pageTitle' => 'System Logs',
            'currentPage' => 'system_logs',
            'logs' => $logs,
            'filters' => $filters
        ]);
    }

    public function detail(): void
    {
        $this->authorize('REPORT_VIEW');
        $id = (int)$this->input('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM system_logs WHERE id = ?");
        $stmt->execute([$id]);
        $log = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$log) {
            echo "Not found";
            return;
        }

        header('Content-Type: application/json');
        echo json_encode($log);
    }
}

