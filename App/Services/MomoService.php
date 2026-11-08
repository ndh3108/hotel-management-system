<?php

namespace App\Services;

class MomoService
{
    private array $config;

    public function __construct()
    {
        $this->config = require BASE_PATH . '/Config/momo.php';
    }

    /**
     * Create MoMo payment URL
     * 
     * @param int $bookingId
     * @param float $amount
     * @param string $orderInfo
     * @param string $prefix BK_ for rooms, SB_ for services
     * @param string|null $redirectUrl Optional override
     * @param string|null $ipnUrl Optional override
     * @return array
     */
    public function createPaymentUrl(int $bookingId, float $amount, string $orderInfo, string $prefix = 'BK_', ?string $redirectUrl = null, ?string $ipnUrl = null): array
    {
        // For Sandbox, use identical IDs and NO spaces in orderInfo
        $uniqueId = time() . $bookingId;
        $requestId = $uniqueId;
        $orderId = $uniqueId;
        
        // Use a strictly alphanumeric orderInfo to avoid encoding issues
        $orderInfo = "Booking" . $bookingId;
        
        $extraData = ""; // Handled by orderId for now
        
        $raw = [
            'partnerCode' => $this->config['PartnerCode'],
            'requestId' => $requestId,
            'amount' => (int) round($amount),
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl ?? $this->config['ReturnUrl'],
            'ipnUrl' => $ipnUrl ?? $this->config['NotifyUrl'],
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $this->config['RequestType'],
        ];

        $signatureRaw = "accessKey={$this->config['AccessKey']}&amount={$raw['amount']}&extraData={$raw['extraData']}&ipnUrl={$raw['ipnUrl']}&orderId={$raw['orderId']}&orderInfo={$raw['orderInfo']}&partnerCode={$raw['partnerCode']}&redirectUrl={$raw['redirectUrl']}&requestId={$raw['requestId']}&requestType={$raw['requestType']}";
        $raw['signature'] = hash_hmac('sha256', $signatureRaw, $this->config['SecretKey']);

        $this->log("Initiating MoMo payment for $prefix$bookingId. Amount: {$raw['amount']}");
        $this->log("Request Payload: " . json_encode($raw));

        $ch = curl_init($this->config['MomoApiUrl']);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($raw),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_CONNECTTIMEOUT => 30, // Timeout for the connection phase
            CURLOPT_TIMEOUT => 60,        // Timeout for the entire request
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || !$response) {
            $this->log("CURL Error: $error");
            return ['success' => false, 'error' => $error ?: 'MoMo V2 request failed'];
        }

        $this->log("MoMo Response: $response");
        $data = json_decode($response, true);
        
        if (!is_array($data) || (int) ($data['resultCode'] ?? -1) !== 0) {
            return [
                'success' => false, 
                'error' => $data['message'] ?? 'MoMo create payment error', 
                'payload' => $data
            ];
        }

        return ['success' => true, 'payUrl' => $data['payUrl'] ?? '', 'payload' => $data];
    }

    public function verifySignature(array $payload): bool
    {
        $signature = (string) ($payload['signature'] ?? '');
        if ($signature === '') return false;

        $raw = "accessKey={$this->config['AccessKey']}&amount={$payload['amount']}&extraData={$payload['extraData']}&message={$payload['message']}&orderId={$payload['orderId']}&orderInfo={$payload['orderInfo']}&orderType={$payload['orderType']}&partnerCode={$payload['partnerCode']}&payType={$payload['payType']}&requestId={$payload['requestId']}&responseTime={$payload['responseTime']}&resultCode={$payload['resultCode']}&transId={$payload['transId']}";
        $expected = hash_hmac('sha256', $raw, $this->config['SecretKey']);
        
        $isValid = hash_equals($expected, $signature);
        if (!$isValid) {
            $this->log("Signature Mismatch! Expected: $expected, Received: $signature");
        }
        
        return $isValid;
    }

    private function log(string $message): void
    {
        // Use BASE_PATH reliably or fallback to directory relative to this file
        $rootPath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        $logDir = $rootPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
        
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        
        $logFile = $logDir . DIRECTORY_SEPARATOR . 'momo.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
    }
}
