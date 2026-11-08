<?php

namespace App\Core;

/**
 * BookingException
 * - Chuẩn hoá validate + error handling cho booking core.
 * - Service ném exception với mã lỗi, Controller chỉ cần bắt và hiển thị.
 */
class BookingException extends \RuntimeException
{
    private string $errorCode;

    public function __construct(string $errorCode, string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->errorCode = $errorCode;
        parent::__construct($message !== '' ? $message : $errorCode, $code, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}

