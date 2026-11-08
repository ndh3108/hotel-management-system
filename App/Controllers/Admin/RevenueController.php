<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Payment;
use App\Models\Booking;

/**
 * Admin Revenue - Báo cáo doanh thu (chỉ đọc).
 */
class RevenueController extends Controller
{
    private Payment $paymentModel;
    private Booking $bookingModel;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->paymentModel = new Payment();
        $this->bookingModel = new Booking();
    }

    public function index(): void
    {
        // Align with seeded permission code trong DB.
        $this->authorize('REPORT_VIEW');
        $paymentPageNumber = max(1, (int) $this->input('payment_page', 1));
        $bookingPageNumber = max(1, (int) $this->input('booking_page', 1));
        // Payment model hiện tại không thống nhất phương thức phân trang.
        // Dùng `all()` rồi tự cắt theo offset để tránh lỗi runtime/linter.
        $allPayments = $this->paymentModel->all();
        $perPage = 12;
        $offset = ($paymentPageNumber - 1) * $perPage;
        $totalItems = count($allPayments);
        $paymentsItems = array_slice($allPayments, $offset, $perPage);
        $paymentsPage = [
            'items' => $paymentsItems,
            'current_page' => $paymentPageNumber,
            'per_page' => $perPage,
            'total_items' => $totalItems,
            'total_pages' => max(1, (int) ceil($totalItems / $perPage)),
        ];
        $allBookings = $this->bookingModel->getAllWithDetails();
        $bookingOffset = ($bookingPageNumber - 1) * 12;
        $totalBookings = count($allBookings);
        $bookingsPage = [
            'items' => array_slice($allBookings, $bookingOffset, 12),
            'current_page' => $bookingPageNumber,
            'total_pages' => max(1, (int) ceil($totalBookings / 12)),
        ];
        
        $occupancyRate = $this->getOccupancyRate();
        $revenueBySource = $this->getRevenueBySource();
        $revenueHeatmap = $this->getRevenueHeatmap();
        
        $totalRevenue = 0.0;
        foreach ($revenueBySource as $source) {
            $totalRevenue += (float) ($source['total'] ?? 0);
        }

        $this->useLayout = true;
        $this->render('Admin/revenue/index', [
            'title' => 'Doanh thu - Admin',
            'pageTitle' => 'Báo cáo Doanh thu',
            'currentPage' => 'revenue',
            'payments' => $paymentsPage['items'],
            'bookings' => $bookingsPage['items'],
            'paymentPageNumber' => $paymentsPage['current_page'],
            'paymentTotalPages' => $paymentsPage['total_pages'],
            'bookingPageNumber' => $bookingsPage['current_page'],
            'bookingTotalPages' => $bookingsPage['total_pages'],
            'occupancyRate' => $occupancyRate,
            'revenueBySource' => $revenueBySource,
            'revenueHeatmap' => $revenueHeatmap,
            'totalRevenue' => $totalRevenue,
        ]);
    }

    public function getRevenue()
    {

        $pdo = \App\Core\Database::getInstance()->getConnection();
        $sql="SELECT 
        DATE(payment_date) as date,
        SUM(amount_paid) as revenue
        FROM payments
        WHERE status='Success'
        GROUP BY DATE(payment_date)
        ORDER BY date DESC";

        $stmt=$pdo->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    private function getOccupancyRate(): float
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $totalRooms = (int)$pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
        if ($totalRooms <= 0) {
            return 0.0;
        }
        $occupied = (int)$pdo->query("SELECT COUNT(*) FROM rooms WHERE status IN ('Occupied', 'Cleaning', 'Hold')")->fetchColumn();
        return round(($occupied / $totalRooms) * 100, 2);
    }

    private function getRevenueBySource(): array
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT COALESCE(method, 'unknown') as source, SUM(amount_paid) as total FROM payments WHERE status IN ('success', 'Success') GROUP BY method");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    private function getRevenueHeatmap(): array
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->query("
            SELECT DATE(COALESCE(paid_at, payment_date)) as d, COALESCE(method, 'unknown') as source, SUM(amount_paid) as total
            FROM payments
            WHERE status IN ('success', 'Success')
            GROUP BY DATE(COALESCE(paid_at, payment_date)), method
            ORDER BY d DESC
            LIMIT 90
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}

