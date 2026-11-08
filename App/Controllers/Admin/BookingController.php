<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\BookingException;
use App\Services\BookingService;
use App\Services\CheckoutService;
use App\Services\PdfInvoiceService;
use App\Services\InventoryService;
use App\Services\DamageService;
use App\Models\Booking;
use App\Models\InventoryDamage;

class BookingController extends Controller
{
    private Booking $bookingModel;
    private BookingService $bookingService;
    private CheckoutService $checkoutService;
    private PdfInvoiceService $pdfInvoiceService;
    private InventoryService $inventoryService;
    private DamageService $damageService;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->bookingModel = new Booking();
        $this->bookingService = new BookingService();
        $this->checkoutService = new CheckoutService();
        $this->pdfInvoiceService = new PdfInvoiceService();
        $this->inventoryService = new InventoryService();
        $this->damageService = new DamageService();
    }

    public function index(): void
    {
        $this->authorize('BOOKING_VIEW_ALL');
        $cursor = (int) $this->input('cursor', 0);
        $pageNumber = max(1, (int) $this->input('page', 1));
        
        $filters = [
            'status' => $this->input('status'),
            'date' => $this->input('date'),
            'keyword' => $this->input('keyword'),
        ];

        $page = $this->bookingModel->getPaginatedWithDetails($cursor, 10, $filters);

        $this->useLayout = true;
        $this->render('admin/bookings/index_v2', [
            'title' => 'Quản lý đặt phòng - Admin',
            'pageTitle' => 'Danh sách đặt phòng',
            'currentPage' => 'bookings',
            'bookings' => $page['items'],
            'hasMore' => $page['has_more'],
            'nextCursor' => $page['next_last_id'],
            'pageNumber' => $pageNumber,
            'filters' => $filters
        ]);
    }

    public function show(int $id): void
    {
        $this->authorize('BOOKING_VIEW_ALL');
        $booking = $this->bookingModel->getDetailById($id);

        // Fetch inventory defaults for the room type and existing damages
        $inventoryList = [];
        $lossAndDamages = [];
        $invoice = null;
        $invoiceItems = [];
        if ($booking && !empty($booking['room_id'])) {
            // Get booking_detail_id
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $stmtDetail = $pdo->prepare("SELECT id FROM booking_details WHERE booking_id = ? AND room_id = ?");
            $stmtDetail->execute([$id, $booking['room_id']]);
            $detailId = $stmtDetail->fetchColumn();

            if ($detailId) {
                // Check if snapshot exists
                $snapshotModel = new \App\Models\BookingInventorySnapshot();
                $inventoryList = $snapshotModel->getByBookingDetailId((int)$detailId);
                
                // If no snapshot (maybe pre-checkin), show live merged inventory
                if (empty($inventoryList) && !empty($booking['room_type_id'])) {
                    $inventoryList = $this->inventoryService->getMergedInventory((int)$booking['room_id'], (int)$booking['room_type_id']);
                }

                // Get Ledger entries
                $damageModel = new InventoryDamage();
                $lossAndDamages = $damageModel->getByBookingDetailId((int)$detailId);
            }
        }
        if ($booking) {
            try {
                $pdo = $this->bookingModel->getPdo();
                $stmtInvoice = $pdo->prepare("SELECT * FROM invoices WHERE booking_id = ? ORDER BY id DESC LIMIT 1");
                $stmtInvoice->execute([$id]);
                $invoice = $stmtInvoice->fetch(\PDO::FETCH_ASSOC) ?: null;
                if ($invoice) {
                    $stmtItems = $pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC");
                    $stmtItems->execute([(int) $invoice['id']]);
                    $invoiceItems = $stmtItems->fetchAll(\PDO::FETCH_ASSOC) ?: [];
                }
            } catch (\Exception $e) {
                $invoice = null;
                $invoiceItems = [];
            }
        }

        $this->useLayout = true;
        $this->render('admin/bookings/detail', [
            'title' => 'Chi tiết đặt phòng - Admin',
            'pageTitle' => 'Chi Tiết Đặt Phòng #' . $id,
            'currentPage' => 'bookings',
            'booking' => $booking,
            'inventoryList' => $inventoryList,
            'lossAndDamages' => $lossAndDamages,
            'invoice' => $invoice,
            'invoiceItems' => $invoiceItems,
        ]);
    }

    public function storeDamage(int $id): void
    {
        $this->authorize('BOOKING_VIEW_ALL');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        $booking = $this->bookingModel->getDetailById($id);

        if (!$booking) {
            $this->redirect(\App\Core\url('/admin/bookings'));
            return;
        }

        $items = $_POST['items'] ?? [];
        $userId = $this->session->get('user_id') ?? 1;

        // Get booking_detail_id
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmtDetail = $pdo->prepare("SELECT id FROM booking_details WHERE booking_id = ? AND room_id = ?");
        $stmtDetail->execute([$id, $booking['room_id']]);
        $detailId = $stmtDetail->fetchColumn();

        if (!$detailId) {
            $this->redirect(\App\Core\url('/admin/bookings/' . $id . '?error=missing_booking_detail'));
            return;
        }

        foreach ($items as $itemId => $data) {
            $qtyLost = (int)($data['lost'] ?? 0);
            if ($qtyLost > 0) {
                $notes = $data['notes'] ?? ('Reported by session user ' . $userId);
                $this->damageService->reportDamage((int)$detailId, (int)$itemId, $qtyLost, $userId, $notes);
            }
        }

        $this->redirect(\App\Core\url('/admin/bookings/' . $id . '?success=1'));
    }

    public function updateStatus(int $id): void
    {
        $this->authorize('BOOKING_VIEW_ALL');

        $status = trim((string) $this->input('status'));

        if (!$status) {
            $this->redirect(\App\Core\url('/admin/bookings/' . $id . '?error=missing_status'));
            return;
        }

        $result = $this->bookingService->updateStatus($id, $status);

        if ($result) {
            $this->redirect(\App\Core\url('/admin/bookings/' . $id . '?status_updated=1'));
        } else {
            $this->redirect(\App\Core\url('/admin/bookings/' . $id . '?error=update_failed'));
        }
    }

    private function sendStatusChangeNotification($booking, $oldStatus, $newStatus): void
    {

        error_log("Booking {$booking['id']} status changed from {$oldStatus} to {$newStatus}");
    }

    public function checkout(int $id): void
    {
        // Align with seeded permission code trong DB.
        $this->authorize('PAYMENT_CREATE');
        
        $booking = $this->bookingModel->getDetailById($id);
        if (!$booking) {
            $this->redirect(\App\Core\url('/admin/bookings?error=not_found'));
            return;
        }

        $bookingStatus = Booking::normalizeBookingStatus($booking['booking_status'] ?? $booking['status'] ?? null);
        if (!in_array($bookingStatus, [Booking::STATUS_CHECKED_OUT, Booking::STATUS_COMPLETED], true)) {
            $this->redirect(\App\Core\url('/admin/bookings/' . $id . '?error=' . urlencode('Cần check-out trước khi hoàn tất checkout tài chính.')));
            return;
        }

        $result = $this->checkoutService->checkoutBooking($id);
        if (($result['success'] ?? false) === true) {
            $this->redirect(\App\Core\url('/admin/bookings/' . $id . '?checkout_success=1'));
            return;
        }
        $this->redirect(\App\Core\url('/admin/bookings/' . $id . '?error=' . urlencode((string)($result['error'] ?? 'checkout_failed'))));
    }

    public function checkIn(int $id): void
    {
        $this->authorize('BOOKING_VIEW_ALL');
        $result = $this->bookingService->checkIn($id);
        $this->redirect(\App\Core\url('/admin/bookings/' . $id . ($result ? '?status_updated=1' : '?error=check_in_failed')));
    }

    public function markCheckedOut(int $id): void
    {
        $this->authorize('BOOKING_VIEW_ALL');
        $result = $this->bookingService->checkOut($id);
        $this->redirect(\App\Core\url('/admin/bookings/' . $id . ($result ? '?status_updated=1' : '?error=check_out_failed')));
    }

    public function markCompleted(int $id): void
    {
        $this->authorize('BOOKING_VIEW_ALL');
        $result = $this->bookingService->completeBooking($id);
        $this->redirect(\App\Core\url('/admin/bookings/' . $id . ($result ? '?status_updated=1' : '?error=complete_failed')));
    }

    public function invoicePdf(int $id): void
    {
        $this->authorize('BOOKING_VIEW_ALL');
        try {
            $this->pdfInvoiceService->streamByBookingId($id);
        } catch (\Throwable $e) {
            $this->redirect(\App\Core\url('/admin/bookings/' . $id . '?error=' . urlencode($e->getMessage())));
        }
    }

    public function bill(int $id): void
    {
        $this->authorize('BOOKING_VIEW_ALL');
        try {
            echo $this->pdfInvoiceService->renderByBookingId($id);
        } catch (\Throwable $e) {
            $this->redirect(\App\Core\url('/admin/bookings/' . $id . '?error=' . urlencode($e->getMessage())));
        }
    }
}

