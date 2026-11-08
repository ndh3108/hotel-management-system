<?php

namespace App\Controllers;

use App\Services\VnpayService;
use App\Core\Controller;
use App\Core\Auth;
use App\Core\AuthMiddleware;
use App\Core\Session;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use App\Core\BookingStatus;
use App\Services\BookingService;
use App\Services\UserService;
use App\Services\MomoService;
use App\Services\PaymentLedgerService;
use App\Models\Invoice;
use App\Models\PaymentTransaction;
use function App\Core\url;

class BookingController extends Controller
{
    private Room $roomModel;
    private RoomType $roomTypeModel;
    private BookingService $bookingService;
    private $bookingModel;
    private UserService $userService;
    private MomoService $momoService;
    private PaymentLedgerService $paymentLedgerService;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->roomModel = new Room();
        $this->roomTypeModel = new RoomType();
        $this->bookingService = new BookingService();
        $this->bookingModel = new Booking();
        $this->userService = new UserService();
        $this->momoService = new MomoService();
        $this->paymentLedgerService = new PaymentLedgerService();
    }

    public function form(): void
    {
        AuthMiddleware::requireAuth();

        $roomId = (int) $this->input('room_id');
        $roomIds = $this->input('room_ids') ?? [];
        $checkIn = $this->input('check_in');
        $checkOut = $this->input('check_out');

        $selectedRooms = [];
        if (!empty($roomIds) && is_array($roomIds)) {
            foreach ($roomIds as $rid) {
                $r = $this->roomModel->findById((int)$rid);
                if ($r) {
                    $selectedRooms[] = $r;
                }
            }
        }

        if (!$roomId && empty($selectedRooms)) {
            $this->redirect(url('/rooms/search'));
            return;
        }

        if (!empty($selectedRooms)) {
            $room = $selectedRooms[0];
        } else {
            $room = $this->roomModel->findById($roomId);
            if (!$room) {
                $this->redirect(url('/rooms/search'));
                return;
            }
        }

        $roomType = $this->roomTypeModel->findById($room['room_type_id'] ?? 0);

        $serviceModel = new \App\Models\Addon();
        $addons = $serviceModel->getAddons();

        $this->useLayout = false;
        $this->render('Booking/form', [
            'room' => $room,
            'roomType' => $roomType,
            'selectedRooms' => $selectedRooms,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'addons' => $addons,
        ]);
    }

    public function store(): void
    {
        // Cho phép AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if (!$this->isPost()) {
            if ($isAjax) {
                echo json_encode(['success' => false, 'error' => 'Invalid request method']);
                exit;
            }
            $this->redirect(url('/rooms'));
            return;
        }

        $userId = Auth::id();
        if (!$userId) {
            if ($isAjax) {
                echo json_encode(['success' => false, 'error' => 'Vui lòng đăng nhập để đặt phòng']);
                exit;
            }
            Session::flash('_intended', url('/booking/form?room_id=' . (int) $this->input('room_id') . '&check_in=' . $this->input('check_in') . '&check_out=' . $this->input('check_out')));
            $this->redirect(url('/login'));
            return;
        }

        $addonsInput = $_POST['addons'] ?? [];
        $addons = [];
        foreach ($addonsInput as $serviceId => $data) {
            if (!empty($data['selected'])) {
                $addons[] = [
                    'service_id' => (int) $serviceId,
                    'quantity' => max(1, (int) ($data['qty'] ?? 1)),
                ];
            }
        }

        $result = $this->bookingService->createBookingFromRequest([
            'user_id' => $userId,
            'room_id' => $this->input('room_id'),
            'room_ids' => $_POST['room_ids'] ?? null,
            'check_in' => $this->input('check_in'),
            'check_out' => $this->input('check_out'),
            'voucher_code' => $this->input('voucher_code'),
            'points_to_redeem' => (int)$this->input('points_to_redeem', 0),
            'payment_method' => $this->input('payment_method', 'offline'),
            'addons' => $addons,
        ]);

        if ($isAjax) {
            if ($result['success']) {
                $response = ['success' => true, 'booking_id' => $result['booking_id']];
                $response['redirect'] = url('/booking/success?id=' . $result['booking_id']);
                echo json_encode($response);
            } else {
                echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Không thể tạo đặt phòng']);
            }
            exit;
        }

        if ($result['success']) {
            $this->userService->logCustomActivity((int) $userId, 'BOOKING_CREATED', 'Tạo đặt phòng #' . (int) ($result['booking_id'] ?? 0));
            $this->redirect(url('/booking/success?id=' . $result['booking_id']));
            return;
        }

        $roomId = (int) $this->input('room_id');
        $checkIn = $this->input('check_in');
        $checkOut = $this->input('check_out');
        $query = 'room_id=' . $roomId . '&error=' . $result['error'];
        if ($checkIn && $checkOut) {
            $query .= '&check_in=' . urlencode($checkIn) . '&check_out=' . urlencode($checkOut);
        }
        $this->redirect(url('/booking/form?' . $query));
    }

    public function success()
    {
        AuthMiddleware::requireAuth();
        $bookingId = $_GET['id'] ?? 0;
        $paymentStatus = $_GET['payment_status'] ?? ($_GET['payment_method'] ?? '');
        $paymentMessage = $_GET['message'] ?? '';

        $booking = $this->bookingModel->getBookingWithDetails($bookingId);

        if (!$booking) {
            $this->render('error/404', ['message' => 'Không tìm thấy đơn đặt phòng']);
            return;
        }

        if ((int) ($booking['user_id'] ?? 0) !== (int) Auth::id()) {
            $this->render('error/403', ['message' => 'Bạn không có quyền xem đơn đặt phòng này']);
            return;
        }

        $this->useLayout = false;

        $this->render('Booking/success', [
            'booking' => $booking,
            'paymentStatus' => $paymentStatus,
            'paymentMessage' => $paymentMessage
        ]);
    }

    public function paymentSuccess()
    {
        AuthMiddleware::requireAuth();
        $bookingId = $_GET['id'] ?? 0;

        $booking = $this->bookingModel->findById($bookingId);

        if (!$booking) {
            $this->render('error/404', ['message' => 'KhÃ´ng tÃ¬m tháº¥y Ä‘Æ¡n Ä‘áº·t phÃ²ng']);
            return;
        }

        if ((int) ($booking['user_id'] ?? 0) !== (int) Auth::id()) {
            $this->render('error/403', ['message' => 'Bạn không có quyền xem đơn đặt phòng này']);
            return;
        }

        $this->useLayout = false;
        $this->render('Booking/payment-success', [
            'booking' => $booking,
        ]);
    }

    public function paymentResult()
    {
        AuthMiddleware::requireAuth();
        $bookingId = $_GET['id'] ?? 0;
        $paymentStatus = $_GET['payment_status'] ?? 'error';
        $paymentMessage = $_GET['message'] ?? '';

        $booking = $this->bookingModel->findById($bookingId);

        if (!$booking) {
            $this->render('error/404', ['message' => 'KhÃ´ng tÃ¬m tháº¥y Ä‘Æ¡n Ä‘áº·t phÃ²ng']);
            return;
        }

        if ((int) ($booking['user_id'] ?? 0) !== (int) Auth::id()) {
            $this->render('error/403', ['message' => 'Bạn không có quyền xem đơn đặt phòng này']);
            return;
        }

        $this->useLayout = false;
        $this->render('Booking/payment-result', [
            'booking' => $booking,
            'paymentStatus' => $paymentStatus,
            'paymentMessage' => $paymentMessage,
        ]);
    }

    public function payment()
    {
        AuthMiddleware::requireAuth();
        $booking_id = $_GET['booking_id'] ?? 0;

        if (!$booking_id) {
            $_SESSION['error'] = 'Không tìm thấy mã đặt phòng';
            $this->redirect(url('/bookings'));
            return;
        }

        $booking = $this->bookingModel->getBookingWithDetails($booking_id);

        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy thông tin đặt phòng';
            $this->redirect(url('/bookings'));
            return;
        }

        // Kiểm tra quyền truy cập
        $userId = Auth::id();
        if ($booking['user_id'] != $userId) {
            $_SESSION['error'] = 'Bạn không có quyền xem trang này';
            $this->redirect(url('/bookings'));
            return;
        }

        $this->useLayout = false;
        $this->render('Payment/index', [
            'booking' => $booking,
            'bookingModel' => $this->bookingModel
        ]);
    }

    public function vnpay()
    {
        AuthMiddleware::requireAuth();
        $booking_id = $_POST['booking_id'] ?? $_GET['booking_id'] ?? 0;

        if (!$booking_id) {
            die('LỖI: không nhận được booking_id');
        }

        $booking = $this->bookingModel->findById($booking_id);

        if (!$booking) {
            die('LỖI: không tìm thấy booking');
        }

        if (!BookingStatus::isAwaitingPayment($booking['status'] ?? BookingStatus::PENDING_PAYMENT)) {
            $_SESSION['error'] = 'Đơn đặt phòng này không ở trạng thái chờ thanh toán.';
            $this->redirect(url('/bill?id=' . (int) $booking_id));
            return;
        }

        $amount = $booking['total_price'] ?? 0;

        if ($amount <= 0) {
            die('LỖI: số tiền không hợp lệ');
        }

        $url = \App\Services\VnpayService::createPayment($amount, $booking_id);
        $this->userService->logCustomActivity((int) Auth::id(), 'BOOKING_VNPAY_INIT', 'Khởi tạo thanh toán VNPay cho booking #' . (int) $booking_id);

        header("Location: $url");
        exit;
    }

    public function vnpayReturn()
    {
        $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
        $vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
        $vnp_Amount = $_GET['vnp_Amount'] ?? '';
        $vnp_TransactionNo = $_GET['vnp_TransactionNo'] ?? '';
        $validation = VnpayService::validateReturnData($_GET);

        $paymentStatus = 'error';
        $message = '';

        if ($validation['valid']) {
            $bookingId = explode('_', $vnp_TxnRef)[0];
            if ($vnp_ResponseCode == '00') {
                $paymentStatus = 'success';
                $message = 'Thanh toán thành công!';
                $this->bookingModel->updateStatus($bookingId, BookingStatus::CONFIRMED);
                $this->bookingModel->saveTransactionLog($bookingId, [
                    'transaction_no' => $vnp_TransactionNo,
                    'amount' => $vnp_Amount / 100,
                    'response_code' => $vnp_ResponseCode,
                    'payment_date' => date('Y-m-d H:i:s')
                ]);
            } else {
                $paymentStatus = 'failed';
                $errorMessages = [
                    '02' => 'Giao dịch không thành công do lỗi kỹ thuật',
                    '09' => 'Thẻ/TK không đủ số dư',
                    '11' => 'Hết thời gian chờ',
                    '24' => 'Hủy giao dịch'
                ];
                $message = $errorMessages[$vnp_ResponseCode] ?? 'Thanh toán thất bại. Mã lỗi: ' . $vnp_ResponseCode;
                $this->bookingModel->updateStatus($bookingId, BookingStatus::PENDING_PAYMENT);
                $this->bookingModel->saveTransactionLog($bookingId, [
                    'response_code' => $vnp_ResponseCode,
                    'error_message' => $message,
                    'payment_date' => date('Y-m-d H:i:s')
                ], 'failed');
            }
        } else {
            $paymentStatus = 'error';
            $message = 'Chữ ký không hợp lệ!';
        }

        if ($paymentStatus === 'error') {
            $this->writeVnpayDebugLog([
                'type' => 'invalid_signature',
                'booking_id' => $vnp_TxnRef,
                'response_code' => $vnp_ResponseCode,
                'received_hash' => $validation['received_hash'],
                'calculated_hash' => $validation['calculated_hash'],
                'hash_data' => $validation['hash_data'],
                'query' => $_GET,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $redirectUrl = url('/booking/payment-result?id=' . $vnp_TxnRef . '&payment_status=' . $paymentStatus . '&message=' . urlencode($message));
        header('Location: ' . $redirectUrl);
        exit;
    }

    public function momo()
    {
        AuthMiddleware::requireAuth();
        $bookingId = $_GET['booking_id'] ?? $_POST['booking_id'] ?? 0;

        if (!$bookingId) {
            die('LỖI: không nhận được booking_id');
        }

        $booking = $this->bookingModel->findById($bookingId);
        if (!$booking) {
            die('LỖI: không tìm thấy booking');
        }

        $amount = (float)($booking['total_price'] ?? 0);
        $orderInfo = "Thanh toan don hang #BK-" . $bookingId;
        
        $result = $this->momoService->createPaymentUrl((int)$bookingId, $amount, $orderInfo);
        
        if (!($result['success'] ?? false) || empty($result['payUrl'])) {
            $error = $result['error'] ?? 'Không khởi tạo được thanh toán MoMo';
            $this->redirect(url('/booking/success?id=' . (int) $bookingId . '&payment_status=error&message=' . urlencode($error)));
            return;
        }

        $this->userService->logCustomActivity((int) Auth::id(), 'BOOKING_MOMO_INIT', 'Khởi tạo thanh toán MoMo cho booking #' . (int) $bookingId);
        
        header("Location: " . $result['payUrl']);
        exit;
    }

    public function momoReturn()
    {
        AuthMiddleware::requireAuth();
        $payload = $_GET;
        $isValid = $this->momoService->verifySignature($payload);
        
        $resultCode = (int)($payload['resultCode'] ?? -1);
        $bookingId = (int)base64_decode((string)($payload['extraData'] ?? ''));
        
        $paymentStatus = 'error';
        $message = $payload['message'] ?? 'Thanh toán thất bại';

        if ($isValid) {
            if ($resultCode === 0) {
                $paymentStatus = 'success';
                $message = 'Thanh toán MoMo thành công!';
                $this->bookingModel->updateStatus($bookingId, BookingStatus::CONFIRMED);
                $this->bookingModel->saveTransactionLog($bookingId, [
                    'transaction_no' => $payload['transId'] ?? '',
                    'amount' => (float)$payload['amount'],
                    'response_code' => (string)$resultCode,
                    'payment_date' => date('Y-m-d H:i:s'),
                    'payment_method' => 'momo'
                ]);
            } else {
                $paymentStatus = 'failed';
            }
        } else {
            $paymentStatus = 'error';
            $message = 'Chữ ký không hợp lệ!';
        }

        $redirectUrl = url('/booking/payment-result?id=' . $bookingId . '&payment_status=' . $paymentStatus . '&message=' . urlencode($message));
        header('Location: ' . $redirectUrl);
        exit;
    }

    public function momoNotify()
    {
        $rawPayload = file_get_contents('php://input');
        $payload = json_decode($rawPayload, true);
        
        if (!$payload || !$this->momoService->verifySignature($payload)) {
            http_response_code(400);
            echo "Invalid Signature";
            return;
        }

        $resultCode = (int)($payload['resultCode'] ?? -1);
        $bookingId = (int)base64_decode((string)($payload['extraData'] ?? ''));

        if ($resultCode === 0) {
            // Update booking status if not already confirmed
            $booking = $this->bookingModel->findById($bookingId);
            if ($booking && $booking['status'] !== BookingStatus::CONFIRMED) {
                $this->bookingModel->updateStatus($bookingId, BookingStatus::CONFIRMED);
                $this->bookingModel->saveTransactionLog($bookingId, [
                    'transaction_no' => $payload['transId'] ?? '',
                    'amount' => (float)$payload['amount'],
                    'provider' => 'momo',
                    'ipn' => true
                ]);
            }
        }

        http_response_code(204); // Success, no content
    }

    public function checkPaymentStatus()
    {
        header('Content-Type: application/json');

        $bookingId = $_GET['id'] ?? 0;

        if (!$bookingId) {
            echo json_encode(['status' => 'error', 'message' => 'Missing booking ID']);
            return;
        }

        $booking = $this->bookingModel->findById($bookingId);

        if (!$booking) {
            echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
            return;
        }

        $isPaid = in_array($booking['status'], [BookingStatus::CONFIRMED, BookingStatus::COMPLETED]);

        echo json_encode([
            'status' => $isPaid ? 'paid' : $booking['status'],
            'booking_id' => $bookingId,
            'message' => $isPaid ? 'Payment completed' : 'Payment pending'
        ]);
    }

    public function payLater()
    {
        AuthMiddleware::requireAuth();
        $booking_id = (int)($_POST['booking_id'] ?? 0);
        if (!$booking_id) {
            $_SESSION['error'] = 'Không tìm thấy đơn đặt phòng';
            $this->redirect(url('/bookings'));
            return;
        }

        $booking = $this->bookingModel->findById($booking_id);
        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy thông tin đặt phòng';
            $this->redirect(url('/bookings'));
            return;
        }

        // Cập nhật thành CONFIRMED
        $this->bookingModel->updateStatus($booking_id, BookingStatus::CONFIRMED);
        
        $this->bookingModel->saveTransactionLog($booking_id, [
            'payment_method' => 'cod',
            'notes' => 'Chọn thanh toán sau'
        ]);
        $this->userService->logCustomActivity((int) Auth::id(), 'BOOKING_PAY_LATER', 'Chọn thanh toán sau cho booking #' . $booking_id);

        $_SESSION['success'] = 'Xác nhận đặt phòng thành công. Cảm ơn quý khách!';
        $this->redirect(url('/'));
    }

    private function writeVnpayDebugLog(array $payload): void
    {
        $logDir = BASE_PATH . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }

        $logFile = $logDir . DIRECTORY_SEPARATOR . 'vnpay_debug.log';
        @file_put_contents(
            $logFile,
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
    }

    public function index(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect(url('/login'));
            return;
        }

        $userId = $_SESSION['user']['id'];
        $lastId = (int) $this->input('cursor', 0);
        $page = $this->bookingModel->getCursorPageByUser((int) $userId, $lastId, 10);
        
        $this->useLayout = true;
        $this->render('Booking/index', [
            'title' => 'Đơn đặt phòng của tôi',
            'bookings' => $page['items'],
            'nextId' => $page['next_id'] ?? null,
            'hasMore' => $page['has_more'] ?? false,
        ]);
    }

    public function bill()
    {
        $bookingId = $_GET['id'] ?? 0;

        if (!$bookingId) {
            $this->render('error/404', ['message' => 'Không tìm thấy mã hóa đơn']);
            return;
        }

        $booking = $this->bookingModel->getBookingWithDetails($bookingId);

        if (!$booking) {
            $this->render('error/404', ['message' => 'Không tìm thấy thông tin hóa đơn']);
            return;
        }

        $userId = Auth::id();
        $isAdmin = Auth::canAccessAdmin();

        if (!$isAdmin && $booking['user_id'] != $userId) {
            $this->render('error/403', ['message' => 'Bạn không có quyền xem hóa đơn này']);
            return;
        }

        $invoiceModel = new Invoice();
        $invoice = $invoiceModel->findLatestByBookingId((int) $bookingId);
        $invoiceItems = [];
        $paymentTransactions = [];
        if ($invoice) {
            $pdo = $this->bookingModel->getPdo();
            $stmtItems = $pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC");
            $stmtItems->execute([(int) $invoice['id']]);
            $invoiceItems = $stmtItems->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            $paymentTransactions = (new PaymentTransaction())->listByInvoiceId((int) $invoice['id']);
        }

        $this->render('Booking/bill', [
            'booking' => $booking,
            'invoice' => $invoice,
            'invoiceItems' => $invoiceItems,
            'paymentTransactions' => $paymentTransactions,
        ]);
    }

    // Hủy đặt phòng
    public function cancel()
    {
        AuthMiddleware::requireAuth();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

        if (!$id) {
            $_SESSION['error'] = 'Không tìm thấy mã đặt phòng';
            $this->redirect(url('/bookings'));
            return;
        }

        $booking = $this->bookingModel->find($id);

        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy đơn đặt phòng';
            $this->redirect(url('/bookings'));
            return;
        }

        if ($booking['user_id'] != Auth::id()) {
            $_SESSION['error'] = 'Bạn không có quyền hủy đơn đặt phòng này';
            $this->redirect(url('/bookings'));
            return;
        }

        // Cho phép hủy các trạng thái pending_payment, confirmed
        $normalizedStatus = strtolower((string) ($booking['booking_status'] ?? $booking['status'] ?? ''));
        $cancellableStatuses = ['draft', 'pending_payment', 'confirmed', 'checked_in'];
        if (!in_array($normalizedStatus, $cancellableStatuses, true)) {
            $_SESSION['error'] = 'Đơn đặt phòng này không thể hủy';
            $this->redirect(url('/bookings'));
            return;
        }

        $result = $this->bookingService->updateStatus($id, BookingStatus::CANCELLED);

        if ($result) {
            $this->userService->logCustomActivity((int) Auth::id(), 'BOOKING_CANCELLED', 'Hủy booking #' . $id);
            $_SESSION['success'] = 'Hủy đặt phòng thành công';
        } else {
            $_SESSION['error'] = 'Hủy đặt phòng thất bại';
        }

        $this->redirect(url('/bookings'));
    }

    // Chỉnh sửa đặt phòng (thay đổi ngày) - ĐÃ FIX
    public function edit()
    {
        AuthMiddleware::requireAuth();

        $id = (int)($_GET['id'] ?? 0);

        if (!$id) {
            $_SESSION['error'] = 'Không tìm thấy mã đặt phòng';
            $this->redirect(url('/bookings'));
            return;
        }

        $booking = $this->bookingModel->getBookingWithDetails($id);

        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy thông tin đặt phòng';
            $this->redirect(url('/bookings'));
            return;
        }

        if ($booking['user_id'] != Auth::id()) {
            $_SESSION['error'] = 'Bạn không có quyền chỉnh sửa đơn đặt phòng này';
            $this->redirect(url('/bookings'));
            return;
        }

        // Chỉ cho phép chỉnh sửa các đơn đang chờ thanh toán
        $normalizedStatus = strtolower((string) ($booking['booking_status'] ?? $booking['status'] ?? ''));
        $editableStatuses = ['draft', 'pending_payment', 'confirmed'];
        if (!in_array($normalizedStatus, $editableStatuses, true)) {
            $_SESSION['error'] = 'Đơn đặt phòng này không thể chỉnh sửa';
            $this->redirect(url('/bookings'));
            return;
        }

        // Lấy thông tin phòng để hiển thị form chỉnh sửa
        $roomModel = new \App\Models\Room();
        $room = null;

        // Lấy room_id từ booking_details thay vì từ booking
        try {
            $sql = "SELECT room_id FROM booking_details WHERE booking_id = ? LIMIT 1";
            $stmt = $this->bookingModel->getPdo()->prepare($sql);
            $stmt->execute([$id]);
            $detail = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($detail && !empty($detail['room_id'])) {
                $room = $roomModel->findById($detail['room_id']);
            }
        } catch (\Exception $e) {
            // Nếu không lấy được, tiếp tục
        }

        $roomTypeModel = new \App\Models\RoomType();
        $roomType = $roomTypeModel->findById($booking['room_type_id'] ?? 0);

        $this->render('Booking/edit', [
            'booking' => $booking,
            'room' => $room,
            'roomType' => $roomType
        ]);
    }

    // Cập nhật đặt phòng
    public function update()
    {
        AuthMiddleware::requireAuth();

        if (!$this->isPost()) {
            $this->redirect(url('/bookings'));
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $checkIn = $_POST['check_in'] ?? '';
        $checkOut = $_POST['check_out'] ?? '';

        if (!$id || !$checkIn || !$checkOut) {
            $_SESSION['error'] = 'Thiếu thông tin cập nhật';
            $this->redirect(url('/bookings'));
            return;
        }

        $booking = $this->bookingModel->find($id);

        if (!$booking || $booking['user_id'] != Auth::id()) {
            $_SESSION['error'] = 'Không tìm thấy đơn đặt phòng';
            $this->redirect(url('/bookings'));
            return;
        }

        // Kiểm tra ngày hợp lệ
        if (strtotime($checkOut) <= strtotime($checkIn)) {
            $_SESSION['error'] = 'Ngày trả phòng phải sau ngày nhận phòng';
            $this->redirect(url('/booking/edit?id=' . $id));
            return;
        }

        // Cập nhật booking
        $result = $this->bookingService->updateBookingDates($id, $checkIn, $checkOut);

        if ($result) {
            $this->userService->logCustomActivity((int) Auth::id(), 'BOOKING_UPDATED', 'Cập nhật ngày cho booking #' . $id);
            $_SESSION['success'] = 'Cập nhật đặt phòng thành công';
            $this->redirect(url('/bill?id=' . $id));
        } else {
            $_SESSION['error'] = 'Cập nhật thất bại, phòng có thể đã được đặt';
            $this->redirect(url('/booking/edit?id=' . $id));
        }
    }

    /**
     * View booking details for user (check status updates)
     * Route: GET /bookings/{id}/view
     */
    public function viewDetails(int $id): void
    {
        AuthMiddleware::requireAuth();

        $userId = Auth::id();
        $booking = $this->bookingModel->findById($id);

        // Verify ownership
        if (!$booking || $booking['user_id'] != $userId) {
            $_SESSION['error'] = 'Không có quyền xem chi tiết đặt phòng này';
            $this->redirect(url('/'));
            return;
        }

        // Get status config
        $statusConfig = require BASE_PATH . '/Config/booking_status.php';
        $currentStatus = strtolower($booking['status'] ?? 'pending_payment');

        // Get notifications for this booking
        $notificationModel = new \App\Models\Notification();
        try {
            $notifications = $this->bookingModel->getTimeline($id);
        } catch (\Exception $e) {
            $notifications = [];
        }

        $this->useLayout = true;
        $this->render('bookings/user-detail', [
            'title' => 'Chi tiết đặt phòng #' . $id,
            'pageTitle' => 'Chi Tiết Đặt Phòng #' . $id,
            'booking' => $booking,
            'statusConfig' => $statusConfig,
            'currentStatus' => $currentStatus,
            'notifications' => $notifications,
        ]);
    }
}

