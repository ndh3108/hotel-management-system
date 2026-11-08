<?php

use App\Core\Router;
use App\Controllers\BookingController;
use App\Controllers\Api\RBACController;
use App\Controllers\Api\RoomManagementController;

// ============ API ROUTES ============
Router::group(['prefix' => '/api'], function() {
    
    Router::get('/check-payment-status', BookingController::class, 'checkPaymentStatus');

    // ============ API RBAC ROUTES ============
    Router::group(['namespace' => 'App\Controllers\Api'], function() {
        Router::get('/users', RBACController::class, 'users');
        Router::post('/roles', RBACController::class, 'storeRole');
        Router::get('/permissions', RBACController::class, 'permissions');
        Router::add('PUT', '/permissions-to-role', RBACController::class, 'updateRolePermissions');
        Router::post('/users/{id}/roles', RBACController::class, 'assignRoleToUser');
        Router::add('DELETE', '/users/{id}/roles/{role_id}', RBACController::class, 'revokeRoleFromUser');
    });

    // ============ API ROOM MANAGEMENT ROUTES ============
    Router::group(['namespace' => 'App\Controllers\Api', 'middlewares' => [\App\Core\AuthMiddleware::class]], function() {
        Router::get('/rooms', RoomManagementController::class, 'index');
        Router::post('/rooms/{id}/status', RoomManagementController::class, 'updateStatus');
        Router::get('/rooms/available', RoomManagementController::class, 'available');
    });
});
