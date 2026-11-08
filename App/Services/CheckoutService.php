<?php

namespace App\Services;

use App\Core\Database;
use App\Core\BookingStatus;
use App\Services\VoucherService;
use App\Services\LoyaltyService;
use App\Services\InvoiceCalculatorService;
use App\Domain\Services\AuditLogService;
use PDO;

class CheckoutService
{
    private PDO $pdo;
    private AuditLogService $audit;
    private VoucherService $voucherService;
    private LoyaltyService $loyaltyService;
    private InvoiceCalculatorService $invoiceCalculator;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->audit = new AuditLogService();
        $this->voucherService = new VoucherService();
        $this->loyaltyService = new LoyaltyService();
        $this->invoiceCalculator = new InvoiceCalculatorService();
    }

    public function checkoutBooking(int $bookingId): array
    {
        $this->pdo->beginTransaction();
        try {
            $stmtBooking = $this->pdo->prepare("
                SELECT b.*, bd.check_in_date AS check_in, bd.check_out_date AS check_out, bd.room_id, bd.price_per_night
                FROM bookings b
                LEFT JOIN booking_details bd ON bd.booking_id = b.id
                WHERE b.id = ?
                FOR UPDATE
            ");
            $stmtBooking->execute([$bookingId]);
            $booking = $stmtBooking->fetch(PDO::FETCH_ASSOC);
            if (!$booking) {
                throw new \RuntimeException('Booking not found');
            }

            $stmtExistingInvoice = $this->pdo->prepare("SELECT id FROM invoices WHERE booking_id = ? LIMIT 1");
            $stmtExistingInvoice->execute([$bookingId]);
            $existingInvoice = $stmtExistingInvoice->fetch(PDO::FETCH_ASSOC);
            if ($existingInvoice) {
                $this->pdo->commit();
                return ['success' => true, 'invoice_id' => (int)$existingInvoice['id'], 'idempotent' => true];
            }

            $nights = 1.0;
            if (!empty($booking['check_in']) && !empty($booking['check_out'])) {
                $nights = max(1.0, (strtotime($booking['check_out']) - strtotime($booking['check_in'])) / 86400);
            }
            $roomTotal = ((float)($booking['price_per_night'] ?? 0.0)) * $nights;

            // Tính phí trễ check-out
            $lateCheckoutFee = 0.0;
            if (!empty($booking['check_out'])) {
                $checkoutDay = date('Y-m-d', strtotime($booking['check_out']));
                $deadlineTimestamp = strtotime($checkoutDay . ' 12:00:00');
                $nowTimestamp = time();
                
                if ($nowTimestamp > $deadlineTimestamp) {
                    $lateHour = (int)date('H', $nowTimestamp);
                    
                    // Fetch configured rules from late_checkout_rules
                    $stmtRules = $this->pdo->query("SELECT from_hour, to_hour, percent FROM late_checkout_rules ORDER BY from_hour ASC");
                    $rules = $stmtRules->fetchAll(PDO::FETCH_ASSOC);
                    
                    $penaltyPercent = 0;
                    foreach ($rules as $rule) {
                        $from = (int)$rule['from_hour'];
                        $to = !empty($rule['to_hour']) ? (int)$rule['to_hour'] : 24;
                        if ($lateHour >= $from && $lateHour < $to) {
                            $penaltyPercent = (int)$rule['percent'];
                            break;
                        }
                    }
                    if ($penaltyPercent > 0) {
                        $basePricePerNight = (float)($booking['price_per_night'] ?? 0.0);
                        $lateCheckoutFee = $basePricePerNight * ($penaltyPercent / 100);
                    }
                }
            }

            $stmtSrv = $this->pdo->prepare("SELECT SUM(total_amount) as srv_total FROM order_services WHERE booking_detail_id IN (SELECT id FROM booking_details WHERE booking_id = ?)");
            $stmtSrv->execute([$bookingId]);
            $legacyServiceTotal = (float)($stmtSrv->fetchColumn() ?: 0.0);

            $stmtServiceBookings = $this->pdo->prepare("SELECT SUM(total_price) FROM service_bookings WHERE booking_id = ? AND status != 'CANCELLED'");
            $stmtServiceBookings->execute([$bookingId]);
            $resourceServiceTotal = (float)($stmtServiceBookings->fetchColumn() ?: 0.0);

            $serviceTotal = $legacyServiceTotal + $resourceServiceTotal;

            // 3. Tính phí hư tổn (Inventory Damage) - Production Grade logic
            $stmtDamage = $this->pdo->prepare("
                SELECT SUM(price_at_approval * quantity) AS damage_total
                FROM inventory_damage 
                WHERE booking_id = ? 
                  AND status = 'Approved' 
                  AND is_billed = 0
                FOR UPDATE
            ");
            $stmtDamage->execute([$bookingId]);
            $damageTotal = (float)($stmtDamage->fetchColumn() ?: 0.0);

            // 4. Voucher & Discount
            $voucherDiscount = $this->resolveVoucherDiscount((int)($booking['voucher_id'] ?? 0), $roomTotal + $serviceTotal + $damageTotal + $lateCheckoutFee);
            $prepaidAmount = in_array(strtolower((string)($booking['payment_status'] ?? '')), ['paid', 'partial_paid'], true) ? (float)($booking['total_initial_price'] ?? 0.0) : 0.0;
            
            // 5. Tính toán Invoice cuối cùng
            $calculatedInvoice = $this->invoiceCalculator->calculate(
                $booking,
                $roomTotal,
                $serviceTotal,
                0.0, // addon_amount
                $lateCheckoutFee,
                $damageTotal,
                $voucherDiscount,
                $prepaidAmount
            );

            // 6. Tạo Invoice record
            $stmtInv = $this->pdo->prepare("
                INSERT INTO invoices 
                (booking_id, room_amount, service_amount, addon_amount, late_fee, damage_fee, discount_amount, prepaid_amount, final_total, balance_due, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtInv->execute([
                $bookingId,
                $calculatedInvoice['room_amount'],
                $calculatedInvoice['service_amount'],
                $calculatedInvoice['addon_amount'],
                $calculatedInvoice['late_fee'],
                $calculatedInvoice['damage_fee'],
                $calculatedInvoice['discount_amount'],
                $calculatedInvoice['prepaid_amount'],
                $calculatedInvoice['final_total'],
                $calculatedInvoice['balance_due'],
                ucfirst($calculatedInvoice['status']),
            ]);
            $invoiceId = (int)$this->pdo->lastInsertId();

            // 7. Cập nhật trạng thái Vật tư hư hại -> Billed (Tránh cộng dồn lần sau)
            $stmtUpdateDamage = $this->pdo->prepare("
                UPDATE inventory_damage 
                SET is_billed = 1, status = 'Billed'
                WHERE booking_id = ? 
                  AND status = 'Approved' 
                  AND is_billed = 0
            ");
            $stmtUpdateDamage->execute([$bookingId]);
            $this->audit->logAction('CREATE', 'invoices', $invoiceId, null, [
                'booking_id' => $bookingId,
                'final_total' => $calculatedInvoice['final_total'],
                'status' => ucfirst($calculatedInvoice['status']),
            ]);

            $this->loyaltyService->earnOnInvoice((int)$booking['user_id'], $invoiceId, $calculatedInvoice['final_total']);
            if (!empty($booking['voucher_id'])) {
                $this->voucherService->recordUsage((int)$booking['user_id'], (int)$booking['voucher_id'], $bookingId);
            }

            $stmtUpdateBooking = $this->pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
            $stmtUpdateBooking->execute([BookingStatus::COMPLETED, $bookingId]);
            $this->audit->logAction('UPDATE', 'bookings', $bookingId, ['status' => $booking['status'] ?? null], ['status' => BookingStatus::COMPLETED]);

            if (!empty($booking['room_id'])) {
                $stmtUpdateRoom = $this->pdo->prepare("UPDATE rooms SET status = 'Cleaning' WHERE id = ?");
                $stmtUpdateRoom->execute([(int)$booking['room_id']]);
                $this->audit->logAction('UPDATE', 'rooms', (int)$booking['room_id'], null, ['status' => 'Cleaning']);
            }

            $this->pdo->commit();
            return ['success' => true, 'invoice_id' => $invoiceId, 'idempotent' => false];
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function resolveVoucherDiscount(int $voucherId, float $grossTotal): float
    {
        if ($voucherId <= 0) {
            return 0.0;
        }
        $stmt = $this->pdo->prepare("SELECT type, value, discount_type, discount_value, max_discount, is_active FROM vouchers WHERE id = ? LIMIT 1");
        $stmt->execute([$voucherId]);
        $voucher = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$voucher || (int)($voucher['is_active'] ?? 0) !== 1) {
            return 0.0;
        }
        $voucherType = strtolower((string)($voucher['discount_type'] ?? $voucher['type'] ?? ''));
        $voucherValue = isset($voucher['discount_value']) ? (float)$voucher['discount_value'] : (float)($voucher['value'] ?? 0);
        if ($voucherType === 'percent' || $voucherType === 'percentage') {
            $discount = $grossTotal * ($voucherValue / 100);
        } else {
            $discount = $voucherValue;
        }
        if (!empty($voucher['max_discount'])) {
            $discount = min($discount, (float)$voucher['max_discount']);
        }
        return max(0.0, min($discount, $grossTotal));
    }

}

