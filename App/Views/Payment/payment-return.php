<?php
// public/payment/payment-return.php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\BookingStatus;
use App\Models\Booking;

// Lấy dữ liệu từ VNPay trả về
$vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
$vnp_TxnRef = $_GET['vnp_TxnRef'] ?? ''; // Mã booking
$vnp_Amount = $_GET['vnp_Amount'] ?? '';
$vnp_TransactionNo = $_GET['vnp_TransactionNo'] ?? '';
$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';

// Xóa các tham số không cần thiết để kiểm tra chữ ký
$inputData = array();
foreach ($_GET as $key => $value) {
    if ($key != 'vnp_SecureHash' && $key != 'vnp_SecureHashType') {
        $inputData[$key] = $value;
    }
}

// Sắp xếp theo key
ksort($inputData);
$hashData = '';
foreach ($inputData as $key => $value) {
    $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
}
$hashData = ltrim($hashData, '&');

// Kiểm tra chữ ký
$vnp_HashSecret = "FL58OYC4XPHTHXPORCSC2HA0761M4SEW";
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

$bookingModel = new Booking();
$booking = $bookingModel->findById($vnp_TxnRef);

$paymentStatus = 'error';
$message = '';

// Xác thực chữ ký
if ($secureHash === $vnp_SecureHash) {
    if ($vnp_ResponseCode == '00') {
        // Thanh toán thành công
        $paymentStatus = 'success';
        $message = 'Thanh toán thành công!';
        
        // Cập nhật trạng thái booking
        $bookingModel->updateStatus($vnp_TxnRef, BookingStatus::CONFIRMED);
        
        // Ghi log giao dịch
        $bookingModel->saveTransactionLog($vnp_TxnRef, [
            'transaction_no' => $vnp_TransactionNo,
            'amount' => $vnp_Amount / 100,
            'response_code' => $vnp_ResponseCode,
            'payment_date' => date('Y-m-d H:i:s')
        ]);
        
    } else {
        // Thanh toán thất bại
        $paymentStatus = 'failed';
        
        $errorMessages = [
            '02' => 'Giao dịch không thành công do lỗi kỹ thuật',
            '04' => 'Giao dịch không thành công do giao dịch đã tồn tại',
            '05' => 'Giao dịch không thành công do sai thông tin',
            '06' => 'Giao dịch không thành công do sai mật khẩu',
            '07' => 'Giao dịch không thành công do nghi ngờ gian lận',
            '09' => 'Giao dịch không thành công do thẻ/TK không đủ số dư',
            '10' => 'Giao dịch không thành công do không đúng định dạng',
            '11' => 'Giao dịch không thành công do hết thời gian chờ',
            '12' => 'Giao dịch không thành công do thẻ/TK bị khóa',
            '13' => 'Giao dịch không thành công do sai OTP',
            '24' => 'Giao dịch không thành công do hủy giao dịch'
        ];
        
        $message = $errorMessages[$vnp_ResponseCode] ?? 'Thanh toán thất bại. Mã lỗi: ' . $vnp_ResponseCode;
        
        // Cập nhật trạng thái booking thành PENDING_PAYMENT lại
        $bookingModel->updateStatus($vnp_TxnRef, BookingStatus::PENDING_PAYMENT);
        
        // Ghi log thất bại
        $bookingModel->saveTransactionLog($vnp_TxnRef, [
            'response_code' => $vnp_ResponseCode,
            'error_message' => $message,
            'payment_date' => date('Y-m-d H:i:s')
        ], 'failed');
    }
} else {
    // Chữ ký không hợp lệ
    $paymentStatus = 'error';
    $message = 'Chữ ký không hợp lệ. Giao dịch có thể đã bị giả mạo!';
    
    // Ghi log lỗi bảo mật
    $bookingModel->saveTransactionLog($vnp_TxnRef, [
        'error' => 'Invalid signature',
        'received_hash' => $vnp_SecureHash,
        'calculated_hash' => $secureHash,
        'timestamp' => date('Y-m-d H:i:s')
    ], 'security_error');
}

// Chuyển hướng về success page trong thư mục Booking
$redirectUrl = '/DoAn/public/index.php?controller=booking&action=success&id=' . $vnp_TxnRef . '&payment_status=' . $paymentStatus . '&message=' . urlencode($message);
header('Location: ' . $redirectUrl);
exit;
