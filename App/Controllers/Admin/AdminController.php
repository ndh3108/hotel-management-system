<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Booking;
use App\Services\DashboardService;

class AdminController extends Controller
{
    private User $userModel;
    private RoomType $roomTypeModel;
    private Room $roomModel;
    private Booking $bookingModel;
    private DashboardService $dashboardService;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->userModel = new User();
        $this->roomTypeModel = new RoomType();
        $this->roomModel = new Room();
        $this->bookingModel = new Booking();
        $this->dashboardService = new DashboardService();
    }

    public function index(): void
    {
        $target = $this->resolveAdminLandingPath();
        if ($target === null) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $this->redirect(\App\Core\url($target));
    }

    public function dashboard(): void
    {
        $this->authorize('VIEW_DASHBOARD');
        $dateFrom = $this->input('date_from') ?: null;
        $dateTo = $this->input('date_to') ?: null;

        $overview = $this->dashboardService->getOverview($dateFrom, $dateTo);
        $revenueChart = $this->dashboardService->revenueByDate($dateFrom, $dateTo);
        $bookingChart = $this->dashboardService->bookingsByDate($dateFrom, $dateTo);
        $recentBookings = $this->dashboardService->latestBookings(8);
        $recentUsers = $this->dashboardService->latestUsers(5);

        $this->useLayout = true;
        $this->render('Admin/dashboard', [
            'title' => 'Bảng điều khiển - Admin Panel',
            'pageTitle' => 'Bảng điều khiển',
            'currentPage' => 'dashboard',
            'overview' => $overview,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'revenueChart' => $revenueChart,
            'bookingChart' => $bookingChart,
            'recentBookings' => $recentBookings,
            'recentUsers' => $recentUsers,
        ]);
    }

    private function resolveAdminLandingPath(): ?string
    {
        $candidates = [
            'VIEW_DASHBOARD' => '/admin/dashboard',
            'USER_MANAGE' => '/admin/users',
            'BOOKING_VIEW_ALL' => '/admin/bookings',
            'MANAGE_ROOMS' => '/admin/rooms',
            'MANAGE_SERVICE_BOOKING' => '/admin/services',
            'MANAGE_INVENTORY' => '/admin/inventory',
            'REPORT_VIEW' => '/admin/revenue',
            'REVIEW_APPROVE' => '/admin/reviews',
        ];

        foreach ($candidates as $permission => $path) {
            if (\App\Core\Auth::hasPermission($permission)) {
                return $path;
            }
        }

        if (\App\Core\Auth::canAccessAdmin()) {
            return '/';
        }

        return null;
    }
}

