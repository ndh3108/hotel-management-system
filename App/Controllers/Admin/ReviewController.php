<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

class ReviewController extends Controller
{
    private \App\Models\Review $reviewModel;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->reviewModel = new \App\Models\Review();
    }

    public function index(): void
    {
        // Align with seeded permission code trong DB.
        $this->authorize('REVIEW_APPROVE');
        $statusFilter = $this->input('status');
        $export = $this->input('export');

        $sql = "
            SELECT
                r.*,
                r.content AS comment,
                u.full_name AS user_name,
                (
                    SELECT rm.room_number
                    FROM booking_details bd
                    INNER JOIN rooms rm ON rm.id = bd.room_id
                    WHERE bd.booking_id = r.booking_id
                    ORDER BY bd.id ASC
                    LIMIT 1
                ) AS room_number
            FROM reviews r
            LEFT JOIN users u ON u.id = r.user_id
            WHERE 1=1
        ";
        $params = [];
        if ($statusFilter) {
            $sql .= " AND r.status = :status";
            $params[':status'] = $statusFilter;
        }
        $sql .= " ORDER BY r.created_at DESC LIMIT 500";
        
        $pdo = $this->reviewModel->getConnection();
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        if ($export === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=reviews_' . date('Y-m-d') . '.csv');
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'Customer', 'Room', 'Rating', 'Content', 'Status', 'Date']);
            foreach ($rows as $r) {
                fputcsv($output, [
                    $r['id'],
                    $r['user_name'],
                    $r['room_number'],
                    $r['rating'],
                    $r['comment'],
                    $r['status'],
                    $r['created_at']
                ]);
            }
            fclose($output);
            exit;
        }

        $this->useLayout = true;
        $this->render('Admin/reviews/index', [
            'title' => 'Duyệt đánh giá - Admin',
            'pageTitle' => 'Duyệt đánh giá',
            'currentPage' => 'reviews',
            'reviews' => $rows,
            'currentStatus' => $statusFilter,
        ]);
    }

    public function moderate(int $id): void
    {
        $this->authorize('REVIEW_APPROVE');
        $status = (string)$this->input('status', 'Pending');
        $this->reviewModel->moderate($id, $status);
        $this->redirect(\App\Core\url('/admin/reviews'));
    }
}

