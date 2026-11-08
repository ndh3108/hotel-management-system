<?php

use App\Core\Router;
use App\Core\AuthMiddleware;
use App\Core\GuestMiddleware;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\RoomController;
use App\Controllers\BookingController;
use App\Controllers\ReviewController;
use App\Controllers\CartController;
use App\Controllers\ServiceBookingController;

// Routes công khai
Router::get('/', HomeController::class, 'index');
Router::get('/home', HomeController::class, 'index');
Router::get('/attractions', \App\Controllers\AttractionController::class, 'index');

// Room routes
Router::get('/rooms', RoomController::class, 'index');
Router::get('/room-types', RoomController::class, 'showType');
Router::get('/room-types/{id}', RoomController::class, 'showType');
Router::get('/rooms/search', RoomController::class, 'search');
Router::get('/rooms/about', RoomController::class, 'about');
Router::get('/rooms/{id}', RoomController::class, 'detail');

// Auth Guest routes
Router::group(['middlewares' => [GuestMiddleware::class]], function() {
    Router::get('/login', AuthController::class, 'loginForm');
    Router::post('/login', AuthController::class, 'login');
    Router::get('/register', AuthController::class, 'registerForm');
    Router::post('/register', AuthController::class, 'register');
    Router::get('/forgot-password', AuthController::class, 'forgotPasswordForm');
    Router::post('/forgot-password', AuthController::class, 'forgotPassword');
    Router::get('/reset-password', AuthController::class, 'resetPasswordForm');
    Router::post('/reset-password', AuthController::class, 'resetPassword');
});

Router::get('/logout', AuthController::class, 'logout');
Router::post('/logout', AuthController::class, 'logout');

// Public Services
Router::get('/services', ServiceBookingController::class, 'index');
Router::get('/services/{id}', ServiceBookingController::class, 'show');
Router::post('/services/check-availability', ServiceBookingController::class, 'checkAvailability');

// Authenticated Routes
Router::group(['middlewares' => [AuthMiddleware::class]], function() {
    // Profile
    Router::group(['prefix' => '/profile'], function() {
        Router::get('/', ProfileController::class, 'index');
        Router::post('/update', ProfileController::class, 'update');
        Router::post('/avatar', ProfileController::class, 'uploadAvatar');
        Router::post('/password', ProfileController::class, 'changePassword');
        Router::post('/verify-email', ProfileController::class, 'verifyEmail');
        Router::post('/verify-phone', ProfileController::class, 'verifyPhone');
        Router::post('/logout-other-sessions', ProfileController::class, 'logoutOtherSessions');
    });

    // Booking & Payment
    Router::get('/booking/form', BookingController::class, 'form');
    Router::get('/booking/success', BookingController::class, 'success');
    Router::get('/booking/payment-success', BookingController::class, 'paymentSuccess');
    Router::get('/booking/payment-result', BookingController::class, 'paymentResult');
    Router::post('/booking/store', BookingController::class, 'store');
    
    Router::get('/bookings', BookingController::class, 'index');
    Router::get('/bookings/{id}/view', BookingController::class, 'viewDetails');
    Router::post('/booking/cancel', BookingController::class, 'cancel');
    Router::get('/booking/edit', BookingController::class, 'edit');
    Router::post('/booking/update', BookingController::class, 'update');
    Router::get('/bill', BookingController::class, 'bill');
    Router::get('/service-bookings', ServiceBookingController::class, 'myBookings');

    Router::post('/payment/vnpay', BookingController::class, 'vnpay');
    Router::get('/payment/vnpay', BookingController::class, 'vnpay');
    Router::get('/payment/vnpay-return', BookingController::class, 'vnpayReturn');
    Router::post('/payment/pay-later', BookingController::class, 'payLater');
    Router::post('/payment/momo', BookingController::class, 'momo');
    Router::get('/payment/momo', BookingController::class, 'momo');
    Router::get('/payment/momo-return', BookingController::class, 'momoReturn');
    Router::post('/payment/momo-notify', BookingController::class, 'momoNotify');
    Router::get('/payment', BookingController::class, 'payment');

    // Service Bookings
    Router::post('/services/book', ServiceBookingController::class, 'store', [\App\Core\CreateServiceBookingMiddleware::class]);
    Router::get('/services/booking/success', ServiceBookingController::class, 'success');
    Router::get('/services/booking/bill', ServiceBookingController::class, 'bill');
    Router::post('/services/payment/vnpay', ServiceBookingController::class, 'vnpay');
    Router::get('/services/payment/vnpay', ServiceBookingController::class, 'vnpay');
    Router::get('/services/payment/vnpay-return', ServiceBookingController::class, 'vnpayReturn');
    Router::post('/services/payment/momo', ServiceBookingController::class, 'momo');
    Router::get('/services/payment/momo', ServiceBookingController::class, 'momo');
    Router::get('/services/payment/momo-return', ServiceBookingController::class, 'momoReturn');
    Router::post('/services/payment/momo-notify', ServiceBookingController::class, 'momoNotify');
    Router::post('/services/payment/pay-later', ServiceBookingController::class, 'payLater');

    // Reviews
    Router::post('/reviews', ReviewController::class, 'store');

    // Cart
    Router::group(['prefix' => '/cart'], function() {
        Router::get('/', CartController::class, 'index');
        Router::post('/add', CartController::class, 'add');
        Router::post('/update', CartController::class, 'update');
        Router::post('/remove', CartController::class, 'remove');
        Router::post('/checkout', CartController::class, 'checkout');
    });
});
