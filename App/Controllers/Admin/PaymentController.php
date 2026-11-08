<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class PaymentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
    }

    public function transactions(): void
    {
        $this->authorize('REPORT_VIEW');
        $pdo = Database::getInstance()->getConnection();

        $status = trim((string) $this->input('status', ''));
        $gateway = trim((string) $this->input('gateway', ''));
        $keyword = trim((string) $this->input('keyword', ''));
        $where = ['1=1'];
        $params = [];

        if ($status !== '') {
            $where[] = 'pt.status = :status';
            $params[':status'] = $status;
        }
        if ($gateway !== '') {
            $where[] = 'pt.gateway = :gateway';
            $params[':gateway'] = $gateway;
        }
        if ($keyword !== '') {
            $where[] = '(pt.external_txn_id LIKE :keyword OR CAST(pt.invoice_id AS CHAR) LIKE :keyword)';
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $whereSql = implode(' AND ', $where);
        $sql = "
            SELECT pt.*,
                   i.booking_id,
                   i.final_total,
                   i.balance_due
            FROM payment_transactions pt
            LEFT JOIN invoices i ON i.id = pt.invoice_id
            WHERE {$whereSql}
            ORDER BY pt.id DESC
            LIMIT 300
        ";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $transactions = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $summary = [
            'total' => count($transactions),
            'success' => count(array_filter($transactions, static fn($t) => strtolower((string) ($t['status'] ?? '')) === 'success')),
            'failed' => count(array_filter($transactions, static fn($t) => strtolower((string) ($t['status'] ?? '')) === 'failed')),
        ];

        $export = $this->input('export');
        if ($export === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=payment_transactions_' . date('Y-m-d') . '.csv');
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'Invoice ID', 'Gateway', 'External Txn ID', 'Amount', 'Status', 'Created At']);
            foreach ($transactions as $t) {
                fputcsv($output, [
                    $t['id'],
                    $t['invoice_id'],
                    $t['gateway'],
                    $t['external_txn_id'],
                    $t['amount'],
                    $t['status'],
                    $t['created_at']
                ]);
            }
            fclose($output);
            exit;
        }

        $this->useLayout = true;
        $this->render('Admin/payments/transactions', [
            'title' => 'Payment transactions - Admin',
            'pageTitle' => 'Payment Transactions',
            'currentPage' => 'transactions',
            'transactions' => $transactions,
            'summary' => $summary,
            'filters' => ['status' => $status, 'gateway' => $gateway, 'keyword' => $keyword],
        ]);
    }
}

