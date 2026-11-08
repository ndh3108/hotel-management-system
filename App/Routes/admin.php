<?php

use App\Core\Router;
use App\Core\AdminMiddleware;
use App\Core\AuthMiddleware;
use App\Core\GuestMiddleware;
use App\Core\RequireViewDashboardPermissionMiddleware;
use App\Core\RequireUserManagePermissionMiddleware;
use App\Core\RequireManageRoomsPermissionMiddleware;
use App\Core\RequireBookingViewAllPermissionMiddleware;
use App\Core\RequirePaymentCreatePermissionMiddleware;
use App\Core\ManageServiceBookingMiddleware;
use App\Core\RequireReportViewPermissionMiddleware;
use App\Core\RequireContentManagePermissionMiddleware;
use App\Core\RequireManageInventoryPermissionMiddleware;
use App\Core\RequireReviewApprovePermissionMiddleware;

use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\RoomTypeController;
use App\Controllers\Admin\RoomController as AdminRoomController;
use App\Controllers\Admin\BookingController as AdminBookingController;
use App\Controllers\Admin\AmenityController;
use App\Controllers\Admin\AddonController;
use App\Controllers\Admin\ServiceController as AdminServiceController;
use App\Controllers\Admin\ServiceBookingController as AdminServiceBookingController;
use App\Controllers\Admin\InventoryController;
use App\Controllers\Admin\InventoryCatalogController;
use App\Controllers\Admin\DamageManagementController;
use App\Controllers\Admin\HousekeepingController;
use App\Controllers\Admin\RevenueController;
use App\Controllers\Admin\ReviewController as AdminReviewController;
use App\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Controllers\Admin\LoyaltyController as AdminLoyaltyController;
use App\Controllers\Admin\SystemLogController;
use App\Controllers\Admin\AdminRoleController;
use App\Controllers\Admin\AdminPermissionController;
use App\Controllers\Admin\RBACController;
use App\Controllers\Admin\AuditLogController;
use App\Controllers\Admin\ArticleController;
use App\Controllers\AuthController;

// Admin Login
Router::group(['prefix' => '/admin', 'middlewares' => [GuestMiddleware::class]], function() {
    Router::get('/login', \App\Controllers\AuthController::class, 'loginForm');
    Router::post('/login', \App\Controllers\AuthController::class, 'login');
});

// Admin Dashboard & Core
Router::group(['prefix' => '/admin', 'namespace' => 'App\Controllers\Admin', 'middlewares' => [AdminMiddleware::class]], function() {
    
    // Dashboard
    Router::get('/', AdminController::class, 'index');
    Router::get('/dashboard', AdminController::class, 'dashboard', [RequireViewDashboardPermissionMiddleware::class]);
    
    // RBAC & Logs
    Router::group(['middlewares' => [RequireUserManagePermissionMiddleware::class]], function() {
        Router::get('/rbac', RBACController::class, 'index');
        Router::get('/audit-logs', AuditLogController::class, 'index');
        
        // Roles
        Router::group(['prefix' => '/roles'], function() {
            Router::get('/', AdminRoleController::class, 'index');
            Router::get('/create', AdminRoleController::class, 'create');
            Router::post('/store', AdminRoleController::class, 'store');
            Router::get('/{id}/edit', AdminRoleController::class, 'edit');
            Router::post('/{id}/update', AdminRoleController::class, 'update');
            Router::post('/{id}/delete', AdminRoleController::class, 'destroy');
        });

        // Permissions
        Router::group(['prefix' => '/permissions'], function() {
            Router::get('/', AdminPermissionController::class, 'index');
            Router::get('/create', AdminPermissionController::class, 'create');
            Router::post('/store', AdminPermissionController::class, 'store');
            Router::get('/{id}/edit', AdminPermissionController::class, 'edit');
            Router::post('/{id}/update', AdminPermissionController::class, 'update');
            Router::post('/{id}/disable', AdminPermissionController::class, 'disable');
            Router::post('/{id}/delete', AdminPermissionController::class, 'destroy');
        });

        // Users
        Router::group(['prefix' => '/users'], function() {
            Router::get('/', UserController::class, 'index');
            Router::get('/create', UserController::class, 'create');
            Router::post('/store', UserController::class, 'store');
            Router::get('/{id}/edit', UserController::class, 'edit');
            Router::post('/{id}/update', UserController::class, 'update');
            Router::post('/{id}/delete', UserController::class, 'destroy');
        });
    });

    // Room Management
    Router::group(['middlewares' => [RequireManageRoomsPermissionMiddleware::class]], function() {
        // Room Types
        Router::group(['prefix' => '/room-type'], function() {
            Router::get('/', RoomTypeController::class, 'index');
            Router::get('/create', RoomTypeController::class, 'create');
            Router::post('/store', RoomTypeController::class, 'store');
            Router::get('/{id}/edit', RoomTypeController::class, 'edit');
            Router::post('/{id}/update', RoomTypeController::class, 'update');
            Router::post('/{id}/images/{imageId}/delete', RoomTypeController::class, 'deleteImage');
            Router::post('/{id}/images/{imageId}/set-primary', RoomTypeController::class, 'setPrimaryImage');
            Router::post('/{id}/delete', RoomTypeController::class, 'destroy');
        });

        // Rooms
        Router::group(['prefix' => '/room'], function() {
            Router::get('/', AdminRoomController::class, 'index');
            Router::get('/grid', AdminRoomController::class, 'grid');
            Router::get('/create', AdminRoomController::class, 'create');
            Router::post('/store', AdminRoomController::class, 'store');
            Router::get('/{id}/edit', AdminRoomController::class, 'edit');
            Router::post('/{id}/update', AdminRoomController::class, 'update');
            Router::post('/{id}/images/{imageId}/delete', AdminRoomController::class, 'deleteImage');
            Router::post('/{id}/delete', AdminRoomController::class, 'destroy');
        });

        // Amenities
        Router::group(['prefix' => '/amenities'], function() {
            Router::get('/', AmenityController::class, 'index');
            Router::get('/create', AmenityController::class, 'create');
            Router::post('/store', AmenityController::class, 'store');
            Router::get('/{id}/edit', AmenityController::class, 'edit');
            Router::post('/{id}/update', AmenityController::class, 'update');
            Router::post('/{id}/delete', AmenityController::class, 'destroy');
        });
    });

    // Bookings
    Router::group(['prefix' => '/bookings', 'middlewares' => [RequireBookingViewAllPermissionMiddleware::class]], function() {
        Router::get('/', AdminBookingController::class, 'index');
        Router::get('/{id}', AdminBookingController::class, 'show');
        Router::post('/{id}/status', AdminBookingController::class, 'updateStatus');
        Router::post('/{id}/check-in', AdminBookingController::class, 'checkIn');
        Router::post('/{id}/check-out', AdminBookingController::class, 'markCheckedOut');
        Router::post('/{id}/complete', AdminBookingController::class, 'markCompleted');
        Router::post('/{id}/damage', AdminBookingController::class, 'storeDamage');
        Router::post('/{id}/checkout', AdminBookingController::class, 'checkout', [RequirePaymentCreatePermissionMiddleware::class]);
        Router::get('/{id}/invoice-pdf', AdminBookingController::class, 'invoicePdf');
        Router::get('/{id}/bill', AdminBookingController::class, 'bill');
    });

    // Inventory & Housekeeping
    Router::group(['prefix' => '/inventory', 'middlewares' => [RequireManageInventoryPermissionMiddleware::class]], function() {
        Router::get('/', InventoryController::class, 'index');
        Router::get('/create', InventoryController::class, 'create');
        Router::post('/store', InventoryController::class, 'store');
        Router::get('/{id}/edit', InventoryController::class, 'edit');
        Router::post('/{id}/update', InventoryController::class, 'update');
        Router::post('/{id}/delete', InventoryController::class, 'destroy');

        Router::group(['prefix' => '/catalog'], function() {
            Router::get('/', InventoryCatalogController::class, 'index');
            Router::get('/create', InventoryCatalogController::class, 'create');
            Router::post('/store', InventoryCatalogController::class, 'store');
            Router::get('/edit/{id}', InventoryCatalogController::class, 'edit');
            Router::post('/update/{id}', InventoryCatalogController::class, 'update');
            Router::get('/delete/{id}', InventoryCatalogController::class, 'delete');
        });

        Router::group(['prefix' => '/damage'], function() {
            Router::get('/', DamageManagementController::class, 'index');
            Router::post('/approve/{id}', DamageManagementController::class, 'approve');
            Router::post('/reject/{id}', DamageManagementController::class, 'reject');
        });
    });

    // Housekeeping
    Router::group(['prefix' => '/housekeeping'], function() {
        Router::get('/', HousekeepingController::class, 'index');
        Router::get('/manage/{id}', HousekeepingController::class, 'manageRoom');
        Router::post('/report-damage', HousekeepingController::class, 'reportDamage');
        Router::post('/quick-replace', HousekeepingController::class, 'quickReplace');
        Router::post('/update-status/{id}', HousekeepingController::class, 'updateRoomStatus');
        Router::post('/update-item-status', HousekeepingController::class, 'updateItemStatus');
    });

    // Addons & Services
    Router::group(['middlewares' => [RequireManageRoomsPermissionMiddleware::class]], function() {
        Router::group(['prefix' => '/addons'], function() {
            Router::get('/', AddonController::class, 'index');
            Router::get('/create', AddonController::class, 'create');
            Router::post('/store', AddonController::class, 'store');
            Router::get('/{id}/edit', AddonController::class, 'edit');
            Router::post('/{id}/update', AddonController::class, 'update');
            Router::post('/{id}/delete', AddonController::class, 'destroy');
        });
    });

    Router::group(['middlewares' => [ManageServiceBookingMiddleware::class]], function() {
        Router::group(['prefix' => '/service-bookings'], function() {
            Router::get('/', AdminServiceBookingController::class, 'index');
            Router::get('/{id}', AdminServiceBookingController::class, 'show');
            Router::post('/{id}/status', AdminServiceBookingController::class, 'updateStatus');
            Router::get('/{id}/invoice-pdf', AdminServiceBookingController::class, 'invoicePdf');
            Router::get('/{id}/bill', AdminServiceBookingController::class, 'bill');
        });

        Router::group(['prefix' => '/services'], function() {
            Router::get('/', AdminServiceController::class, 'index');
            Router::get('/create', AdminServiceController::class, 'create');
            Router::post('/store', AdminServiceController::class, 'store');
            Router::get('/{id}/edit', AdminServiceController::class, 'edit');
            Router::post('/{id}/update', AdminServiceController::class, 'update');
            Router::post('/{id}/images/{imageId}/delete', AdminServiceController::class, 'deleteImage');
            Router::post('/{id}/delete', AdminServiceController::class, 'destroy');
        });
    });

    // Articles & Content
    Router::group(['prefix' => '/articles', 'middlewares' => [RequireContentManagePermissionMiddleware::class]], function() {
        Router::get('/', ArticleController::class, 'index');
        Router::get('/create', ArticleController::class, 'create');
        Router::post('/store', ArticleController::class, 'store');
        Router::get('/{id}/edit', ArticleController::class, 'edit');
        Router::post('/{id}/update', ArticleController::class, 'update');
        Router::post('/{id}/delete', ArticleController::class, 'destroy');
    });

    // Revenue & Reports
    Router::group(['middlewares' => [RequireReportViewPermissionMiddleware::class]], function() {
        Router::get('/revenue', RevenueController::class, 'index');
        Router::get('/payments/transactions', AdminPaymentController::class, 'transactions');
        Router::get('/loyalty', AdminLoyaltyController::class, 'index');
        Router::get('/system-logs', SystemLogController::class, 'index');
        Router::get('/system-logs/detail', SystemLogController::class, 'detail');
    });

    // Reviews
    Router::group(['prefix' => '/reviews', 'middlewares' => [RequireReviewApprovePermissionMiddleware::class]], function() {
        Router::get('/', AdminReviewController::class, 'index');
        Router::post('/{id}/moderate', AdminReviewController::class, 'moderate');
    });
});
