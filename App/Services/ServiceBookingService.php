<?php

namespace App\Services;

use App\Core\Database;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use PDO;
use Exception;

class ServiceBookingService
{
    private PDO $pdo;
    private Service $serviceModel;
    private ServiceBooking $serviceBookingModel;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->serviceModel = new Service();
        $this->serviceBookingModel = new ServiceBooking();
    }

    private function parseDateTimeInput(string $value): array
    {
        $value = trim($value);
        if ($value === '') {
            return ['ok' => false, 'db' => '', 'local' => ''];
        }

        $formats = ['Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d'];
        foreach ($formats as $fmt) {
            $dt = \DateTimeImmutable::createFromFormat($fmt, $value);
            if ($dt instanceof \DateTimeImmutable) {
                return [
                    'ok' => true,
                    'db' => $dt->format('Y-m-d H:i:s'),
                    'local' => $dt->format('Y-m-d\TH:i'),
                ];
            }
        }

        $ts = strtotime($value);
        if ($ts === false) {
            return ['ok' => false, 'db' => '', 'local' => ''];
        }
        $dt = (new \DateTimeImmutable())->setTimestamp($ts);
        return [
            'ok' => true,
            'db' => $dt->format('Y-m-d H:i:s'),
            'local' => $dt->format('Y-m-d\TH:i'),
        ];
    }
    public function checkAvailability(int $serviceId, string $startTime, string $endTime, ?int $excludeBookingId = null, int $quantity = 1): array
    {
        $startTime = $this->normalizeDateTime($startTime);
        $endTime = $this->normalizeDateTime($endTime);
        
        $stmtCapacity = $this->pdo->prepare("SELECT capacity, status FROM services WHERE id = ?");
        $stmtCapacity->execute([$serviceId]);
        $service = $stmtCapacity->fetch(PDO::FETCH_ASSOC);

        if (!$service || $service['status'] !== 'AVAILABLE') {
            return ['available' => false, 'error' => 'Dịch vụ đang tạm ngưng'];
        }

        $capacity = (int) $service['capacity'];

        $sql = "SELECT SUM(quantity) as engaged_qty 
                FROM service_bookings 
                WHERE service_id = ? 
                AND LOWER(status) IN ('requested', 'reserved', 'active')
                AND start_time < ? 
                AND end_time > ?";
        
        $params = [$serviceId, $endTime, $startTime];
        if ($excludeBookingId) {
            $sql .= " AND id != ?";
            $params[] = $excludeBookingId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $engagedQty = (int) ($result['engaged_qty'] ?? 0);
        $availableCapacity = max(0, $capacity - $engagedQty);

        if ($availableCapacity >= $quantity) {
             return ['available' => true];
        }

        return ['available' => false, 'error' => 'Dịch vụ chỉ còn ' . $availableCapacity . ' chỗ'];
    }

    /**
     * Calculate the total price based on duration and price per hour.
     */
    public function calculatePrice(array $service, string $startTime, string $endTime): float
    {
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $hours = max(1, ceil(($end - $start) / 3600));
        return (float)$service['price_per_hour'] * $hours;
    }

    public function createBooking(array $data): array
    {
        $userId = (int)($data['user_id'] ?? 0);
        $serviceId = (int)($data['service_id'] ?? 0);
        $quantity = (int)($data['quantity'] ?? 1);
        $quantity = max(1, $quantity);

        $checkInRaw = (string)($data['start_time'] ?? '');
        $checkOutRaw = (string)($data['end_time'] ?? '');

        if ($userId <= 0 || $serviceId <= 0) {
            return ['success' => false, 'error' => 'Thiếu thông tin đặt dịch vụ'];
        }

        $in = $this->parseDateTimeInput($checkInRaw);
        $out = $this->parseDateTimeInput($checkOutRaw);
        if (!$in['ok'] || !$out['ok'] || $in['db'] >= $out['db']) {
            return ['success' => false, 'error' => 'Khoảng thời gian không hợp lệ'];
        }

        // Retry mechanism for Race Condition resolution
        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $this->pdo->beginTransaction();

                // 1. Pessimistic Lock on the Service
                $stmtLock = $this->pdo->prepare("SELECT id, name, capacity, price_per_hour, status FROM services WHERE id = ? FOR UPDATE");
                $stmtLock->execute([$serviceId]);
                $serviceInfo = $stmtLock->fetch(PDO::FETCH_ASSOC);

                if (!$serviceInfo) {
                    throw new Exception('Dịch vụ không tồn tại');
                }

                if ($serviceInfo['status'] !== 'AVAILABLE') {
                    throw new Exception('Dịch vụ đang tạm ngưng hoạt động');
                }

                $capacity = (int) $serviceInfo['capacity'];
                $pricePerHour = (float) $serviceInfo['price_per_hour'];

                // 2. Overlap Check (Sum of assigned quantities in the overlapping range)
                $sqlOverlap = "
                    SELECT SUM(quantity) as engaged_qty 
                    FROM service_bookings 
                    WHERE service_id = ? 
                    AND LOWER(status) IN ('requested', 'reserved', 'active')
                    AND start_time < ? 
                    AND end_time > ?
                    FOR UPDATE
                ";
                $stmtOverlap = $this->pdo->prepare($sqlOverlap);
                $stmtOverlap->execute([$serviceId, $out['db'], $in['db']]);
                $row = $stmtOverlap->fetch(PDO::FETCH_ASSOC);
                $engagedQty = (int) ($row['engaged_qty'] ?? 0);

                // 3. Reject if saturated
                if (($engagedQty + $quantity) > $capacity) {
                    throw new Exception("Dịch vụ đã hết sức chứa trong khung giờ này (còn dư " . max(0, $capacity - $engagedQty) . " chỗ)");
                }

                // Calculate price
                $seconds = strtotime($out['db']) - strtotime($in['db']);
                $hours = ceil($seconds / 3600);
                $totalPrice = $hours * $pricePerHour * $quantity;

                $isStandalone = empty($data['booking_id']);
                $startStatus = $isStandalone ? 'requested' : 'reserved';

                // 4. Insert booking
                $sqlInsert = "
                    INSERT INTO service_bookings 
                    (user_id, service_id, start_time, end_time, quantity, total_price, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ";
                $stmtInsert = $this->pdo->prepare($sqlInsert);
                $stmtInsert->execute([
                    $userId,
                    $serviceId,
                    $in['db'],
                    $out['db'],
                    $quantity,
                    $totalPrice,
                    $startStatus
                ]);

                $bookingId = (int) $this->pdo->lastInsertId();

                // 5. Hybrid Flow: Handle Invoicing
                if (!empty($data['booking_id'])) {
                    $this->attachToExistingInvoice($data['booking_id'], $bookingId, $totalPrice, $serviceInfo['name']);
                } else {
                    $this->createStandaloneInvoice($bookingId, $totalPrice, $serviceInfo['name'], $userId);
                }

                $this->pdo->commit();

                return [
                    'success' => true,
                    'booking_id' => $bookingId,
                    'total_price' => $totalPrice
                ];

            } catch (\Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                
                if ($attempt === 2 || strpos($e->getMessage(), 'Dịch vụ') !== false) {
                    // Do not retry on explicit business logic exceptions
                    return [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
                usleep(500000); 
            }
        }
        
        return ['success' => false, 'error' => 'Hệ thống đang quá tải, vui lòng thử lại sau.'];
    }

    private function attachToExistingInvoice(int $roomBookingId, int $serviceBookingId, float $amount, string $serviceName)
    {
        // Find existing invoice for this room booking
        $sql = "SELECT id FROM invoices WHERE booking_id = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomBookingId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($invoice) {
            $invoiceId = $invoice['id'];
        } else {
            // Should theoretically exist if check-in happened, but handle just in case
            $invoiceId = $this->createNewInvoice($roomBookingId, 0, $amount, $amount);
        }

        // Add item to invoice_items
        $this->addInvoiceItem($invoiceId, 'SERVICE', $serviceBookingId, $amount, "Dịch vụ riêng: $serviceName");

        // Update invoice total
        $sql = "UPDATE invoices SET total_service_amount = total_service_amount + ?, final_total = final_total + ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$amount, $amount, $invoiceId]);
    }

    private function createStandaloneInvoice(int $serviceBookingId, float $amount, string $serviceName, int $userId)
    {
        // Create new invoice without room booking id
        $invoiceId = $this->createNewInvoice(null, 0, $amount, $amount);

        // Add item to invoice_items
        $this->addInvoiceItem($invoiceId, 'SERVICE', $serviceBookingId, $amount, "Dịch vụ riêng (khách ngoài): $serviceName");
    }

    private function createNewInvoice(?int $bookingId, float $roomAmount, float $serviceAmount, float $finalTotal): int
    {
        $sql = "INSERT INTO invoices (booking_id, total_room_amount, total_service_amount, final_total, status) 
                VALUES (?, ?, ?, ?, 'Unpaid')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$bookingId, $roomAmount, $serviceAmount, $finalTotal]);
        return (int)$this->pdo->lastInsertId();
    }

    private function addInvoiceItem(int $invoiceId, string $type, int $refId, float $amount, string $notes)
    {
        $sql = "INSERT INTO invoice_items (invoice_id, type, reference_id, amount, notes) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$invoiceId, $type, $refId, $amount, $notes]);
    }

    private function normalizeDateTime(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        $value = str_replace('T', ' ', $value);
        if (strlen($value) === 16) {
            $value .= ':00';
        }
        return $value;
    }
}

