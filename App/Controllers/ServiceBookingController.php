<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Services\VnpayService;
use App\Services\ServiceBookingService;
use App\Services\EntityImageService;
use App\Services\UserService;
use App\Models\Service;

class ServiceBookingController extends Controller
{
    private Service $serviceModel;
    private ServiceBookingService $serviceBookingService;
    private UserService $userService;
    private \App\Services\MomoService $momoService;

    public function __construct()
    {
        parent::__construct();
        $this->serviceModel = new Service();
        $this->serviceBookingService = new ServiceBookingService();
        $this->userService = new UserService();
        $this->momoService = new \App\Services\MomoService();
    }

    public function index(): void
    {
        $cursor = (int) $this->input('cursor', 0);
        $pageNumber = max(1, (int) $this->input('page', 1));
        
        $pageData = $this->serviceModel->getPaginatedWithTypeName($cursor, 9);

        $this->render('Services/index', [
            'title' => 'Dịch vụ cao cấp - Booking Hotel',
            'services' => $pageData['items'],
            'hasMore' => $pageData['has_more'],
            'nextCursor' => $pageData['next_last_id'] ?? null,
            'pageNumber' => $pageNumber,
        ]);
    }

    public function show(int $id): void
    {
        $service = $this->serviceModel->findWithTypeName($id);
        if (!$service) {
            $this->redirect('/services');
            return;
        }

        $imageService = new EntityImageService();
        $service['images'] = $imageService->getImages('service', $id);

        $this->render('Services/show', [
            'title' => $service['name'] . ' - Dịch vụ Antigravity',
            'service' => $service,
        ]);
    }

    public function checkAvailability(): void
    {
        $serviceId = (int) $this->input('service_id');
        $startTime = $this->input('start_time');
        $endTime = $this->input('end_time');
        $quantity = (int) $this->input('quantity', 1);

        if (!$serviceId || !$startTime || !$endTime || $quantity <= 0) {
            echo json_encode(['available' => false, 'error' => 'Thông tin không hợp lệ']);
            return;
        }

        $result = $this->serviceBookingService->checkAvailability($serviceId, $startTime, $endTime, null, $quantity);
        echo json_encode($result);
    }

    public function store(): void
    {
        AuthMiddleware::requireAuth();
        if (!Auth::hasPermission('CREATE_SERVICE_BOOKING')) {
            http_response_code(403);
            echo '403 Forbidden - Missing permission CREATE_SERVICE_BOOKING';
            return;
        }

        $data = [
            'user_id' => Auth::user()['id'],
            'service_id' => (int) $this->input('service_id'),
            'start_time' => $this->input('start_time'),
            'end_time' => $this->input('end_time'),
            'quantity' => (int) $this->input('quantity', 1),
            'booking_id' => $this->input('booking_id') ? (int) $this->input('booking_id') : null,
        ];

        $result = $this->serviceBookingService->createBooking($data);

        if ($result['success']) {
            $this->userService->logCustomActivity((int) Auth::id(), 'SERVICE_BOOKING_CREATED', 'Tạo booking dịch vụ #' . (int) ($result['booking_id'] ?? 0));
            if (!empty($data['booking_id'])) {
                $this->redirect('/profile?tab=services&success=1');
            } else {
                $this->redirect('/services/booking/success?id=' . $result['booking_id']);
            }
        } else {
            $this->redirect('/services/' . $data['service_id'] . '?error=' . urlencode($result['error']));
        }
    }

    public function success(): void
    {
        AuthMiddleware::requireAuth();
        $id = (int)$this->input('id');
        $booking = (new \App\Models\ServiceBooking())->getBookingWithDetails($id);
        if (!$booking) {
            $this->redirect('/services');
            return;
        }

        if ((int) ($booking['user_id'] ?? 0) !== (int) Auth::id()) {
            $this->redirect('/profile?tab=services');
            return;
        }

        $paymentStatus = $this->input('payment_status');
        $paymentMessage = $this->input('message');

        $this->useLayout = false;
        $this->render('Services/success', [
            'booking' => $booking,
            'paymentStatus' => $paymentStatus,
            'paymentMessage' => $paymentMessage
        ]);
    }

    public function payLater(): void
    {
        AuthMiddleware::requireAuth();
        $id = (int)($this->input('id') ?: $this->input('booking_id'));
        $model = new \App\Models\ServiceBooking();
        $booking = $model->findById($id);
        if ($booking && (int) ($booking['user_id'] ?? 0) === (int) Auth::id()) {
            $model->updateStatus($id, 'requested');
            $this->userService->logCustomActivity((int) Auth::id(), 'SERVICE_BOOKING_PAY_LATER', 'Chọn thanh toán tại quầy cho booking dịch vụ #' . $id);
            $_SESSION['success'] = 'Xác nhận thanh toán tại quầy thành công. Vui lòng thanh toán khi sử dụng dịch vụ!';
        }
        $this->redirect(\App\Core\url('/service-bookings'));
    }

    public function vnpay(): void
    {
        AuthMiddleware::requireAuth();
        $id = (int)($this->input('id') ?: $this->input('booking_id'));
        $model = new \App\Models\ServiceBooking();
        $booking = $model->findById($id);
        
        if (
            !$booking
            || (int) ($booking['user_id'] ?? 0) !== (int) Auth::id()
            || !in_array(strtolower((string) ($booking['status'] ?? '')), ['requested', 'reserved'], true)
        ) {
            $this->redirect('/services');
            return;
        }

        $amount = $booking['total_price'];
        // Use prefix SB_ to identify Service Booking in VNPay
        $txnRef = 'SB_' . $id . '_' . time();
        $returnUrl = APP_URL . '/services/payment/vnpay-return';
        
        $url = \App\Services\VnpayService::createPayment($amount, $txnRef, $returnUrl);
        $this->userService->logCustomActivity((int) Auth::id(), 'SERVICE_BOOKING_VNPAY_INIT', 'Khởi tạo thanh toán VNPay cho booking dịch vụ #' . $id);
        header("Location: $url");
        exit;
    }

    public function momo(): void
    {
        AuthMiddleware::requireAuth();
        $id = (int)($this->input('id') ?: $this->input('booking_id'));
        $model = new \App\Models\ServiceBooking();
        $booking = $model->findById($id);

        if (
            !$booking
            || (int) ($booking['user_id'] ?? 0) !== (int) Auth::id()
            || !in_array(strtolower((string) ($booking['status'] ?? '')), ['requested', 'reserved'], true)
        ) {
            $this->redirect('/services');
            return;
        }

        $amount = (float) $booking['total_price'];
        $orderInfo = "Thanh toán dịch vụ #" . $id . " tại Antigravity Hotel";

        $returnUrl = (defined('APP_URL') ? APP_URL : 'http://localhost/doan/public') . '/services/payment/momo-return';
        $notifyUrl = (defined('APP_URL') ? APP_URL : 'http://localhost/doan/public') . '/services/payment/momo-notify';

        $result = $this->momoService->createPaymentUrl($id, $amount, $orderInfo, 'SB_', $returnUrl, $notifyUrl);

        if ($result['success'] && !empty($result['payUrl'])) {
            $this->userService->logCustomActivity((int) Auth::id(), 'SERVICE_BOOKING_MOMO_INIT', 'Khởi tạo thanh toán MoMo cho booking dịch vụ #' . $id);
            header('Location: ' . $result['payUrl']);
            exit;
        } else {
            $error = $result['error'] ?? 'Lỗi khởi tạo MoMo';
            $this->redirect('/services/booking/success?id=' . $id . '&payment_status=error&message=' . urlencode($error));
        }
    }



    public function vnpayReturn(): void
    {
        $vnp_TxnRef = $this->input('vnp_TxnRef', '');
        $vnp_ResponseCode = $this->input('vnp_ResponseCode', '');
        
        $validation = \App\Services\VnpayService::validateReturnData($_GET);
        
        $paymentStatus = 'error';
        $message = 'Chữ ký không hợp lệ';
        $bookingId = 0;

        if ($validation['valid']) {
            $parts = explode('_', $vnp_TxnRef);
            if (count($parts) >= 2 && $parts[0] === 'SB') {
                $bookingId = (int)$parts[1];
                $model = new \App\Models\ServiceBooking();
                
                if ($vnp_ResponseCode == '00') {
                    $model->updateStatus($bookingId, 'reserved');
                    $model->saveTransactionLog($bookingId, $vnp_TxnRef, (int)$this->input('vnp_Amount')/100, 'VNPay', 'SUCCESS', $_GET);
                    $paymentStatus = 'success';
                    $message = 'Thanh toán thành công';
                } else {
                    $model->updateStatus($bookingId, 'requested');
                    $paymentStatus = 'failed';
                    $message = 'Giao dịch thất bại hoặc bị huỷ';
                }
            }
        }

        $this->redirect('/services/booking/success?id=' . $bookingId . '&payment_status=' . $paymentStatus . '&message=' . urlencode($message));
    }

    public function momoReturn(): void
    {
        AuthMiddleware::requireAuth();
        $payload = $_GET;
        $isValid = $this->momoService->verifySignature($payload);
        
        $resultCode = (int)($payload['resultCode'] ?? -1);
        $orderId = $payload['orderId'] ?? '';
        
        $paymentStatus = 'error';
        $message = $payload['message'] ?? 'Thanh toán thất bại';
        $bookingId = 0;

        $parts = explode('_', $orderId);
        if (count($parts) >= 2 && $parts[0] === 'SB') {
            $bookingId = (int)$parts[1];
            $model = new \App\Models\ServiceBooking();
            
            if ($isValid) {
                if ($resultCode === 0) {
                    $model->updateStatus($bookingId, 'reserved'); 
                    $model->saveTransactionLog($bookingId, $orderId, (float)($payload['amount'] ?? 0), 'MoMo', 'SUCCESS', $payload);
                    $paymentStatus = 'success';
                    $message = 'Thanh toán MoMo thành công';
                } else {
                    $paymentStatus = 'failed';
                    $message = 'Thanh toán bị từ chối (Mã: ' . $resultCode . ')';
                }
            } else {
                $message = 'Chữ ký không hợp lệ';
            }
        }

        $this->redirect('/services/booking/success?id=' . $bookingId . '&payment_status=' . $paymentStatus . '&message=' . urlencode($message));
    }

    public function momoNotify(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            return;
        }

        if ($this->momoService->verifySignature($data)) {
            $resultCode = (int)($data['resultCode'] ?? -1);
            $orderId = $data['orderId'] ?? '';
            $parts = explode('_', $orderId);

            if (count($parts) >= 2 && $parts[0] === 'SB' && $resultCode === 0) {
                $bookingId = (int)$parts[1];
                $model = new \App\Models\ServiceBooking();
                $booking = $model->findById($bookingId);
                
                if ($booking && strtolower((string)($booking['status'] ?? '')) === 'requested') {
                    $model->updateStatus($bookingId, 'reserved');
                    $model->saveTransactionLog($bookingId, $orderId, (float)($data['amount'] ?? 0), 'MoMo', 'IPN_SUCCESS', $data);
                }
            }
            http_response_code(204);
        } else {
            http_response_code(401);
        }
    }

    // MoMo execPostRequest removed in favor of MomoService

    public function bill(): void
    {
        AuthMiddleware::requireAuth();
        $id = (int)$this->input('id');
        $model = new \App\Models\ServiceBooking();
        $booking = $model->getBookingWithDetails($id);
        
        if (!$booking || (int) ($booking['user_id'] ?? 0) !== (int) Auth::id()) {
            $this->redirect('/service-bookings');
            return;
        }

        $this->useLayout = true;
        $this->render('ServiceBooking/bill', [
            'booking' => $booking
        ]);
    }
    public function myBookings(): void
    {
        AuthMiddleware::requireAuth();
        $userId = Auth::id();
        
        $model = new \App\Models\ServiceBooking();
        $serviceBookings = $model->getByUserId((int)$userId);
        
        $this->useLayout = true;
        $this->render('ServiceBooking/my_bookings', [
            'title' => 'Đơn dịch vụ của tôi',
            'serviceBookings' => $serviceBookings
        ]);
    }
}

