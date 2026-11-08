<?php

namespace App\Core;

/**
 * Permission route guards (server-side).
 * Dùng AuthMiddleware::requirePermission để đảm bảo:
 * - deny-by-default
 * - xử lý cả session + JWT
 * - redirect đúng luồng
 */

abstract class PermissionMiddlewareBase extends Middleware
{
    protected string $permissionCode = '';

    final public function handle(\Closure $next): void
    {
        if ($this->permissionCode === '') {
            http_response_code(500);
            echo 'PermissionMiddleware misconfigured: empty permissionCode';
            exit;
        }
        AuthMiddleware::requirePermission($this->permissionCode);
        $next();
    }
}

class RequireViewDashboardPermissionMiddleware extends PermissionMiddlewareBase
{
    protected string $permissionCode = 'VIEW_DASHBOARD';
}

class RequireUserManagePermissionMiddleware extends PermissionMiddlewareBase
{
    protected string $permissionCode = 'USER_MANAGE';
}

class RequireManageRoomsPermissionMiddleware extends PermissionMiddlewareBase
{
    protected string $permissionCode = 'MANAGE_ROOMS';
}

class RequireBookingViewAllPermissionMiddleware extends PermissionMiddlewareBase
{
    protected string $permissionCode = 'BOOKING_VIEW_ALL';
}

class RequirePaymentCreatePermissionMiddleware extends PermissionMiddlewareBase
{
    protected string $permissionCode = 'PAYMENT_CREATE';
}

class RequireManageServiceBookingPermissionMiddleware extends PermissionMiddlewareBase
{
    protected string $permissionCode = 'MANAGE_SERVICE_BOOKING';
}

class RequireReportViewPermissionMiddleware extends PermissionMiddlewareBase
{
    protected string $permissionCode = 'REPORT_VIEW';
}

class RequireContentManagePermissionMiddleware extends PermissionMiddlewareBase
{
    // Trong DB hiện tại không seed `CONTENT_MANAGE`, dùng USER_MANAGE để bảo vệ trang.
    protected string $permissionCode = 'USER_MANAGE';
}

class RequireManageInventoryPermissionMiddleware extends PermissionMiddlewareBase
{
    protected string $permissionCode = 'MANAGE_INVENTORY';
}

class RequireReviewApprovePermissionMiddleware extends PermissionMiddlewareBase
{
    protected string $permissionCode = 'REVIEW_APPROVE';
}


