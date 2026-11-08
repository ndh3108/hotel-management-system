<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceBooking;
use App\Services\PdfInvoiceService;

class ServiceBookingController extends Controller
{
    private Service $serviceModel;
    private ServiceType $serviceTypeModel;
    private ServiceBooking $serviceBookingModel;
    private PdfInvoiceService $pdfInvoiceService;

    public function __construct()
    {
        parent::__construct();
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->useLayout = true;
        $this->serviceModel = new Service();
        $this->serviceTypeModel = new ServiceType();
        $this->serviceBookingModel = new ServiceBooking();
        $this->pdfInvoiceService = new PdfInvoiceService();
    }

    public function index(): void
    {
        $this->authorize('MANAGE_SERVICE_BOOKING');
        $cursor = (int) $this->input('cursor', 0);
        $pageNumber = max(1, (int) $this->input('page', 1));
        $page = $this->serviceBookingModel->getPaginated($cursor, 10);
        $this->render('Admin/service_bookings/index', [
            'title' => 'Quản lý đặt dịch vụ',
            'bookings' => $page['items'],
            'currentPage' => 'service_bookings',
            'hasMore' => $page['has_more'],
            'nextCursor' => $page['next_last_id'],
            'pageNumber' => $pageNumber,
        ]);
    }

    public function services(): void
    {
        $this->authorize('MANAGE_SERVICE_BOOKING');
        $cursor = (int) $this->input('cursor', 0);
        $pageNumber = max(1, (int) $this->input('page', 1));
        $page = $this->serviceModel->getPaginatedWithTypeName($cursor, 10);
        $this->render('Admin/service_bookings/services', [
            'title' => 'Danh sách dịch vụ',
            'services' => $page['items'],
            'currentPage' => 'services_list',
            'hasMore' => $page['has_more'],
            'nextCursor' => $page['next_last_id'],
            'pageNumber' => $pageNumber,
        ]);
    }

    public function show(int $id): void
    {
        $this->authorize('MANAGE_SERVICE_BOOKING');

        $booking = $this->serviceBookingModel->getBookingWithDetails($id);
        if (!$booking) {
            $this->redirect(\App\Core\url('/admin/service-bookings'));
            return;
        }

        $this->render('Admin/service_bookings/show', [
            'title' => 'Chi tiết Đặt Dịch Vụ #' . $id,
            'pageTitle' => 'Chi tiết Đặt Dịch Vụ #' . $id,
            'currentPage' => 'service_bookings',
            'booking' => $booking
        ]);
    }

    public function updateStatus(int $id): void
    {
        $this->authorize('MANAGE_SERVICE_BOOKING');

        $status = $this->input('status', '');
        
        if ($status && $this->serviceBookingModel->updateStatus($id, $status)) {
            $_SESSION['success'] = 'Cập nhật trạng thái thành công.';
        } else {
            $_SESSION['error'] = 'Cập nhật thất bại.';
        }
        
        $this->redirect(\App\Core\url('/admin/service-bookings/' . $id));
    }

    public function invoicePdf(int $id): void
    {
        $this->authorize('MANAGE_SERVICE_BOOKING');
        try {
            $this->pdfInvoiceService->streamByServiceBookingId($id);
        } catch (\Throwable $e) {
            $_SESSION['error'] = 'Lỗi xuất PDF: ' . $e->getMessage();
            $this->redirect(\App\Core\url('/admin/service-bookings/' . $id));
        }
    }

    public function bill(int $id): void
    {
        $this->authorize('MANAGE_SERVICE_BOOKING');
        try {
            echo $this->pdfInvoiceService->renderByServiceBookingId($id);
        } catch (\Throwable $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            $this->redirect(\App\Core\url('/admin/service-bookings/' . $id));
        }
    }
}

