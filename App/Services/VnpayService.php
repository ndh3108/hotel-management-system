<?php

namespace App\Services;

class VnpayService
{
    public const TMN_CODE = '1BCREEB2';
    public const HASH_SECRET = '7ISEFZCUQRIMKTF83EWROFWJ5V1POC37';
    public const PAYMENT_URL = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';

    public static function getReturnUrl(): string
    {
        $appUrl = defined('APP_URL') ? rtrim((string) APP_URL, '/') : '';
        if ($appUrl !== '') {
            return $appUrl . '/payment/vnpay-return';
        }

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . $host . '/doan/public/payment/vnpay-return';
    }

    public static function buildHashData(array $data): string
    {
        ksort($data);
        $hashData = [];
        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '') {
                $hashData[] = urlencode((string) $key) . '=' . urlencode((string) $value);
            }
        }

        return implode('&', $hashData);
    }

    public static function makeSecureHash(array $data): string
    {
        return hash_hmac('sha512', self::buildHashData($data), self::HASH_SECRET);
    }

    public static function buildQueryString(array $data): string
    {
        ksort($data);
        $query = [];
        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '') {
                $query[] = urlencode((string) $key) . '=' . urlencode((string) $value);
            }
        }

        return implode('&', $query);
    }

    public static function validateReturnData(array $query): array
    {
        $receivedHash = trim((string) ($query['vnp_SecureHash'] ?? ''));
        $inputData = [];

        foreach ($query as $key => $value) {
            if (strpos((string) $key, 'vnp_') !== 0) {
                continue;
            }

            if ($key !== 'vnp_SecureHash' && $key !== 'vnp_SecureHashType') {
                $inputData[$key] = $value;
            }
        }

        $calculatedHash = self::makeSecureHash($inputData);
        $isValid = $receivedHash !== '' && hash_equals(strtolower($calculatedHash), strtolower($receivedHash));

        return [
            'valid' => $isValid,
            'received_hash' => $receivedHash,
            'calculated_hash' => $calculatedHash,
            'input_data' => $inputData,
            'hash_data' => self::buildHashData($inputData),
        ];
    }

    public static function createPayment($amount, $orderId, $returnUrl = null)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $amount = (int)$amount;
        if ($amount < 5000) $amount = 5000;
        if ($amount >= 1000000000) $amount = 999999999;

        $data = [
            "vnp_Version" => "2.1.0",
            "vnp_Command" => "pay",
            "vnp_TmnCode" => self::TMN_CODE,
            "vnp_Amount" => $amount * 100,
            "vnp_CurrCode" => "VND",
            "vnp_TxnRef" => $orderId . '_' . time(),
            "vnp_OrderInfo" => "Thanh toan booking " . $orderId,
            "vnp_OrderType" => "billpayment",
            "vnp_Locale" => "vn",
            "vnp_ReturnUrl" => $returnUrl ?: self::getReturnUrl(),
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_ExpireDate" => date('YmdHis', strtotime('+15 minutes')),
            "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ];

        if ($data['vnp_IpAddr'] === '::1') {
            $data['vnp_IpAddr'] = '127.0.0.1';
        }

        $vnpSecureHash = self::makeSecureHash($data);
        $query = self::buildQueryString($data);

        return self::PAYMENT_URL
            . '?'
            . $query
            . '&vnp_SecureHash='
            . $vnpSecureHash;
    }
}

