<?php

namespace App\Services;

use App\Core\Database;
use App\Core\BookingStatus;
use App\Core\BookingException;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\VoucherService;
use App\Services\LoyaltyService;
use App\Services\InventoryLedgerService;
use App\Services\AvailabilityService;
use App\Domain\Services\LoggerService;
use App\Domain\Services\NotificationService;
use App\Domain\Services\RoomStateService;
use PDO;
use Exception;

class BookingService
{
    private PDO $pdo;
    private Booking $bookingModel;
    private BookingDetail $bookingDetailModel;
    private Room $roomModel;
    private RoomType $roomTypeModel;
    private VoucherService $voucherService;
    private InventoryLedgerService $inventoryLedgerService;
    private AvailabilityService $availabilityService;
    private InventoryService $inventoryService;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->bookingModel = new Booking();
        $this->bookingDetailModel = new BookingDetail();
        $this->roomModel = new Room();
        $this->roomTypeModel = new RoomType();
        $this->voucherService = new VoucherService();
        $this->inventoryLedgerService = new InventoryLedgerService();
        $this->availabilityService = new AvailabilityService();
        $this->inventoryService = new InventoryService();
    }


    /**
     * Check if a room has overlapping bookings
     */
    public function hasOverlappingBookings(
        int $roomId,
        string $checkIn,
        string $checkOut,
        ?int $excludeBookingId = null
    ): bool {
        $blockStatuses = BookingStatus::statusesThatBlockRoom();
        $statusPlaceholders = implode(',', array_fill(0, count($blockStatuses), '?'));
        $sql = "
            SELECT COUNT(*) as cnt FROM bookings b
            JOIN booking_details bd ON b.id = bd.booking_id
            WHERE bd.room_id = ?
            AND b.status IN ($statusPlaceholders)
            AND bd.check_in_date < ?
            AND bd.check_out_date > ?
        ";
        $queryParams = array_merge([$roomId], $blockStatuses, [$checkOut, $checkIn]);
        if ($excludeBookingId !== null) {
            $sql .= " AND b.id != ?";
            $queryParams[] = $excludeBookingId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($queryParams);
        $resultRow = $stmt->fetch(PDO::FETCH_ASSOC);
        return ((int) ($resultRow['cnt'] ?? 0)) > 0;
    }

    /**
     * Get list of overlapping bookings
     */
    public function getOverlappingBookings(
        int $roomId,
        string $checkIn,
        string $checkOut,
        ?int $excludeBookingId = null,
        bool $forUpdate = false
    ): array {
        $blockStatuses = BookingStatus::statusesThatBlockRoom();
        $statusPlaceholders = implode(',', array_fill(0, count($blockStatuses), '?'));
        $sql = "
            SELECT b.*, bd.room_id, bd.price_per_night
            FROM bookings b
            JOIN booking_details bd ON b.id = bd.booking_id
            WHERE bd.room_id = ?
            AND b.status IN ($statusPlaceholders)
            AND bd.check_in_date < ?
            AND bd.check_out_date > ?
        ";
        if ($excludeBookingId !== null) {
            $sql .= " AND b.id != ?";
        }
        if ($forUpdate) {
            $sql .= " FOR UPDATE";
        }
        $queryParams = array_merge([$roomId], $blockStatuses, [$checkOut, $checkIn]);
        if ($excludeBookingId !== null) {
            $queryParams[] = $excludeBookingId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($queryParams);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function isRoomAvailable(int $roomId, string $checkIn, string $checkOut, ?int $excludeBookingId = null): bool
    {
        return !$this->hasOverlappingBookings($roomId, $checkIn, $checkOut, $excludeBookingId);
    }

    private function parseDateTimeInput(string $dateTimeString): array
    {
        $dateTimeString = trim($dateTimeString);
        if ($dateTimeString === '') {
            return ['ok' => false, 'db' => '', 'local' => ''];
        }

        $formats = ['Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d'];
        foreach ($formats as $format) {
            $dateTime = \DateTimeImmutable::createFromFormat($format, $dateTimeString);
            if ($dateTime instanceof \DateTimeImmutable) {
                return [
                    'ok' => true,
                    'db' => $dateTime->format('Y-m-d H:i:s'),
                    'local' => $dateTime->format('Y-m-d\TH:i'),
                ];
            }
        }

        $timestamp = strtotime($dateTimeString);
        if ($timestamp === false) {
            return ['ok' => false, 'db' => '', 'local' => ''];
        }
        $dateTime = (new \DateTimeImmutable())->setTimestamp($timestamp);
        return [
            'ok' => true,
            'db' => $dateTime->format('Y-m-d H:i:s'),
            'local' => $dateTime->format('Y-m-d\TH:i'),
        ];
    }


    public function validateCreateInput(int $userId, int $roomId, string $checkIn, string $checkOut): array
    {
        $checkInParsed = $this->parseDateTimeInput($checkIn);
        $checkOutParsed = $this->parseDateTimeInput($checkOut);
        if (!$checkInParsed['ok'] || !$checkOutParsed['ok']) {
            throw new BookingException('missing', 'Missing or invalid datetime input');
        }
        if ($checkInParsed['db'] >= $checkOutParsed['db']) {
            throw new BookingException('dates', 'Check-in must be before check-out');
        }
        if ($userId <= 0) {
            throw new BookingException('unauthorized', 'User must be logged in to create booking');
        }
        $roomData = $this->roomModel->findById($roomId);
        if (!$roomData) {
            throw new BookingException('room_not_found', 'Room not found');
        }
        $roomTypeData = $this->roomTypeModel->findById($roomData['room_type_id'] ?? 0);
        $basePrice = isset($roomTypeData['base_price']) ? (float) $roomTypeData['base_price'] : 0;
        $totalSeconds = max(1, strtotime($checkOutParsed['db']) - strtotime($checkInParsed['db']));
        $totalNights = max(1, (int) ceil($totalSeconds / 86400));
        $calculatedTotalPrice = $basePrice * $totalNights;
        
        return [
            'user_id' => $userId,
            'room_id' => $roomId,
            'check_in' => $checkInParsed['db'],
            'check_out' => $checkOutParsed['db'],
            'total_price' => $calculatedTotalPrice,
            'check_in_local' => $checkInParsed['local'],
            'check_out_local' => $checkOutParsed['local'],
        ];
    }


    public function createBookingFromRequest(array $bookingRequest)
    {
        $userId = (int)($bookingRequest['user_id'] ?? 0);
        $roomIds = [];
        if (!empty($bookingRequest['room_ids']) && is_array($bookingRequest['room_ids'])) {
            $roomIds = array_values(array_unique(array_map('intval', $bookingRequest['room_ids'])));
        } elseif (!empty($bookingRequest['room_id'])) {
            $roomIds = [(int)$bookingRequest['room_id']];
        }
        $checkInString = (string)($bookingRequest['check_in'] ?? '');
        $checkOutString = (string)($bookingRequest['check_out'] ?? '');

        if ($userId <= 0 || empty($roomIds)) {
            return ['success' => false, 'error' => 'Thiếu thông tin đặt phòng'];
        }

        $checkInParsed = $this->parseDateTimeInput($checkInString);
        $checkOutParsed = $this->parseDateTimeInput($checkOutString);
        if (!$checkInParsed['ok'] || !$checkOutParsed['ok'] || $checkInParsed['db'] >= $checkOutParsed['db']) {
            return ['success' => false, 'error' => 'Khoảng thời gian không hợp lệ'];
        }

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $this->pdo->beginTransaction();

                sort($roomIds);
                foreach ($roomIds as $lockedRoomId) {
                    $stmtLockRoom = $this->pdo->prepare("SELECT id, status FROM rooms WHERE id = ? FOR UPDATE");
                    $stmtLockRoom->execute([$lockedRoomId]);
                    $lockedRoomData = $stmtLockRoom->fetch(PDO::FETCH_ASSOC);
                    if (!$lockedRoomData) {
                        throw new BookingException('room_not_found', 'Phòng không tồn tại');
                    }

                    $overlappingBookings = $this->getOverlappingBookings(
                        $lockedRoomId,
                        $checkInParsed['db'],
                        $checkOutParsed['db'],
                        null,
                        true
                    );
                    if (!empty($overlappingBookings)) {
                        throw new BookingException('room_unavailable', 'Một hoặc nhiều phòng đã được đặt trong khoảng thời gian này');
                    }
                }

                $bookingCode = "BKG-" . time() . "-" . rand(1000, 9999);
                $bookingStatusLabel = BookingStatus::defaultStatus();
                $expirationDate = null;
                $paymentMethod = $bookingRequest['payment_method'] ?? 'offline';
                
                if ($paymentMethod === 'vnpay' || $paymentMethod === 'online') {
                    $bookingStatusLabel = BookingStatus::PENDING_PAYMENT;
                    $expirationDate = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                }

                $nightsCount = max(1, (strtotime($checkOutParsed['db']) - strtotime($checkInParsed['db'])) / 86400);
                $initialBaseTotal = 0.0;
                $roomPricesMap = [];
                $targetRoomTypeId = null;

                foreach ($roomIds as $roomId) {
                    $roomDataForPrice = $this->roomModel->findById($roomId);
                    if (!$roomDataForPrice) {
                        throw new BookingException('room_not_found', 'Phòng không tồn tại');
                    }
                    $roomTypeDataForPrice = $this->roomTypeModel->findById((int)($roomDataForPrice['room_type_id'] ?? 0));
                    $nightPrice = isset($roomTypeDataForPrice['base_price']) ? (float)$roomTypeDataForPrice['base_price'] : 0.0;
                    $initialBaseTotal += $nightPrice * $nightsCount;
                    $roomPricesMap[$roomId] = $nightPrice;
                    $targetRoomTypeId = $roomDataForPrice['room_type_id'];
                }

                $voucherId = !empty($bookingRequest['voucher_id']) ? (int)$bookingRequest['voucher_id'] : null;
                $voucherDiscountAmount = 0.0;
                if ($voucherId) {
                    $voucherValidation = $this->voucherService->validateCode(
                        $bookingRequest['voucher_code'] ?? '',
                        $userId,
                        $initialBaseTotal,
                        $targetRoomTypeId
                    );
                    if ($voucherValidation['valid']) {
                        $voucherDiscountAmount = (float)$voucherValidation['discount'];
                        $voucherId = (int)$voucherValidation['voucher_id'];
                    } else {
                        $voucherId = null;
                    }
                }

                $pointsDiscountResult = (new LoyaltyService())->calculateRedeemDiscount(
                    $userId,
                    (int)($bookingRequest['points_to_redeem'] ?? 0),
                    $initialBaseTotal - $voucherDiscountAmount
                );
                $pointsDiscountAmount = $pointsDiscountResult['discount'] ?? 0.0;
                $finalBookingTotal = max(0.0, $initialBaseTotal - $voucherDiscountAmount - $pointsDiscountAmount);

                $stmtInsertBooking = $this->pdo->prepare("
                    INSERT INTO bookings 
                    (user_id, room_qty, booking_code, total_initial_price, status, booking_status, payment_status, voucher_id, expired_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $paymentStatusValue = ($bookingStatusLabel === BookingStatus::PENDING_PAYMENT) ? Booking::PAYMENT_UNPAID : Booking::PAYMENT_PAID;

                $stmtInsertBooking->execute([
                    $userId,
                    count($roomIds),
                    $bookingCode,
                    $finalBookingTotal,
                    $bookingStatusLabel,
                    $bookingStatusLabel,
                    $paymentStatusValue,
                    $voucherId,
                    $expirationDate
                ]);

                $newBookingId = (int)$this->pdo->lastInsertId();

                $stmtInsertDetail = $this->pdo->prepare("
                    INSERT INTO booking_details 
                    (booking_id, room_id, check_in_date, check_out_date, price_per_night)
                    VALUES (?, ?, ?, ?, ?)
                ");
                
                $createdDetailIds = [];
                foreach ($roomIds as $roomIdInList) {
                    $stmtInsertDetail->execute([
                        $newBookingId,
                        $roomIdInList,
                        $checkInParsed['db'],
                        $checkOutParsed['db'],
                        $roomPricesMap[$roomIdInList],
                    ]);
                    $createdDetailIds[] = (int)$this->pdo->lastInsertId();
                }

                if (!empty($bookingRequest['addons']) && is_array($bookingRequest['addons'])) {
                    $stmtInsertOrderService = $this->pdo->prepare("INSERT INTO order_services (booking_detail_id, total_amount) VALUES (?, 0)");
                    $stmtInsertOrderServiceDetail = $this->pdo->prepare("INSERT INTO order_service_details (order_service_id, service_id, quantity, price_at_order) VALUES (?, ?, ?, ?)");
                    $stmtFetchServicePrice = $this->pdo->prepare("SELECT price FROM addons WHERE id = ?");

                    foreach ($createdDetailIds as $detailId) {
                        $stmtInsertOrderService->execute([$detailId]);
                        $newOrderServiceId = (int)$this->pdo->lastInsertId();
                        $cumulativeAddonTotal = 0.0;
                        foreach ($bookingRequest['addons'] as $addonEntry) {
                            $addonServiceId = (int)($addonEntry['service_id'] ?? 0);
                            $addonQuantity = max(1, (int)($addonEntry['quantity'] ?? 1));
                            $stmtFetchServicePrice->execute([$addonServiceId]);
                            $serviceData = $stmtFetchServicePrice->fetch(PDO::FETCH_ASSOC);
                            if ($serviceData) {
                                $currentPrice = (float)$serviceData['price'];
                                $stmtInsertOrderServiceDetail->execute([$newOrderServiceId, $addonServiceId, $addonQuantity, $currentPrice]);
                                $cumulativeAddonTotal += ($currentPrice * $addonQuantity);
                            }
                        }
                        $stmtUpdateOrderServiceTotal = $this->pdo->prepare("UPDATE order_services SET total_amount = ? WHERE id = ?");
                        $stmtUpdateOrderServiceTotal->execute([$cumulativeAddonTotal, $newOrderServiceId]);
                    }
                }

                $this->pdo->commit();

                LoggerService::info("Booking created", ['booking_id' => $newBookingId, 'user_id' => $userId, 'rooms' => $roomIds]);
                
                $stmtInsertLog = $this->pdo->prepare("INSERT INTO booking_logs (booking_id, action, details) VALUES (?, ?, ?)");
                $stmtInsertLog->execute([$newBookingId, 'CREATE', "Rooms booked: " . implode(',', $roomIds)]);

                return [
                    'success' => true,
                    'booking_id' => $newBookingId
                ];
            } catch (\Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                LoggerService::error("Booking creation failed", ['attempt' => $attempt + 1, 'error' => $e->getMessage()]);
                if ($attempt == 2) {
                    return [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
                sleep(1);
            }
        }
        return ['success' => false, 'error' => 'System error. Please try again.'];
    }


    public function updateStatus($bookingId, $targetStatusLabel, $shouldNotifyUser = true)
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $this->pdo->beginTransaction();

                $stmtFetchInitialBooking = $this->pdo->prepare("SELECT * FROM bookings WHERE id = ? FOR UPDATE");
                $stmtFetchInitialBooking->execute([(int) $bookingId]);
                $originalBookingData = $stmtFetchInitialBooking->fetch(PDO::FETCH_ASSOC);
                if (!$originalBookingData) {
                    $this->pdo->rollBack();
                    return false;
                }

                $currentStatusNormalized = Booking::normalizeBookingStatus($originalBookingData['booking_status'] ?? $originalBookingData['status'] ?? null);
                $targetStatusNormalized = Booking::normalizeBookingStatus((string) $targetStatusLabel);
                if (!BookingStatus::isValid($targetStatusNormalized) || ($currentStatusNormalized !== $targetStatusNormalized && !BookingStatus::canTransitionTo($currentStatusNormalized, $targetStatusNormalized))) {
                    $this->pdo->rollBack();
                    return false;
                }

                $stmtFetchAffectedRooms = $this->pdo->prepare("SELECT room_id FROM booking_details WHERE booking_id = ? ORDER BY room_id ASC");
                $stmtFetchAffectedRooms->execute([(int) $bookingId]);
                $affectedRoomIds = array_values(array_filter(array_map('intval', $stmtFetchAffectedRooms->fetchAll(PDO::FETCH_COLUMN) ?: []), static fn($id) => $id > 0));
                foreach ($affectedRoomIds as $roomIdInLoop) {
                    $stmtLockTargetRoom = $this->pdo->prepare("SELECT id FROM rooms WHERE id = ? FOR UPDATE");
                    $stmtLockTargetRoom->execute([$roomIdInLoop]);
                }

                $calculatedPaymentStatus = Booking::normalizePaymentStatus($originalBookingData['payment_status'] ?? null, $targetStatusNormalized);
                if (in_array($targetStatusNormalized, [Booking::STATUS_PENDING_PAYMENT, Booking::STATUS_DRAFT], true)) {
                    $calculatedPaymentStatus = Booking::PAYMENT_UNPAID;
                } elseif ($targetStatusNormalized === Booking::STATUS_CANCELLED && $calculatedPaymentStatus === Booking::PAYMENT_PAID) {
                    $calculatedPaymentStatus = Booking::PAYMENT_REFUNDING;
                }

                $updateSuccess = $this->bookingModel->update((int) $bookingId, [
                    'status' => $targetStatusNormalized,
                    'booking_status' => $targetStatusNormalized,
                    'payment_status' => $calculatedPaymentStatus,
                ]);
                if (!$updateSuccess) {
                    throw new Exception('Cannot update booking status');
                }

                $nextRoomStatus = match ($targetStatusNormalized) {
                    Booking::STATUS_CHECKED_IN, Booking::STATUS_CHECKED_OUT => Room::STATUS_OCCUPIED,
                    Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETED, Booking::STATUS_EXPIRED, Booking::STATUS_NO_SHOW => Room::STATUS_CLEANING,
                    default => Room::STATUS_RESERVED,
                };
                
                foreach ($affectedRoomIds as $roomIdToUpdate) {
                    $this->roomModel->updateStatus($roomIdToUpdate, $nextRoomStatus, (int) ($originalBookingData['user_id'] ?? 0), 'Sync from booking #' . $bookingId . ' -> ' . $targetStatusNormalized);
                    
                    if ($targetStatusNormalized === Booking::STATUS_CHECKED_IN) {
                        $stmtFetchDetailId = $this->pdo->prepare("SELECT id FROM booking_details WHERE booking_id = ? AND room_id = ?");
                        $stmtFetchDetailId->execute([$bookingId, $roomIdToUpdate]);
                        $foundDetailId = $stmtFetchDetailId->fetchColumn();
                        if ($foundDetailId) {
                            $this->inventoryService->createSnapshot((int)$foundDetailId);
                        }
                    }

                    $this->inventoryLedgerService->record(
                        $roomIdToUpdate,
                        match ($targetStatusNormalized) {
                            Booking::STATUS_CHECKED_IN => 'occupy',
                            Booking::STATUS_CANCELLED, Booking::STATUS_EXPIRED, Booking::STATUS_NO_SHOW => 'release',
                            Booking::STATUS_COMPLETED, Booking::STATUS_CHECKED_OUT => 'clean',
                            default => 'reserve',
                        },
                        (int) $bookingId,
                        null,
                        (int) ($originalBookingData['user_id'] ?? 0),
                        'Auto ledger from booking status change'
                    );
                }

                $this->availabilityService->persistProjection(date('Y-m-d'));
                $this->pdo->commit();

                LoggerService::info("Booking status updated", ['booking_id' => $bookingId, 'old_status' => $currentStatusNormalized, 'new_status' => $targetStatusNormalized]);

                if ($shouldNotifyUser) {
                    $this->sendStatusChangeNotification($originalBookingData, $currentStatusNormalized, $targetStatusNormalized);
                }
                return true;
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                LoggerService::error("Status update retry failed", ['booking_id' => $bookingId, 'attempt' => $attempt + 1, 'error' => $e->getMessage()]);
                if ($attempt === 2) {
                    return false;
                }
                usleep(500000);
            }
        }

        return false;
    }

    public function checkIn(int $bookingId, bool $shouldNotifyUser = true): bool
    {
        return $this->updateStatus($bookingId, Booking::STATUS_CHECKED_IN, $shouldNotifyUser);
    }

    public function checkOut(int $bookingId, bool $shouldNotifyUser = true): bool
    {
        return $this->updateStatus($bookingId, Booking::STATUS_CHECKED_OUT, $shouldNotifyUser);
    }

    public function completeBooking(int $bookingId, bool $shouldNotifyUser = true): bool
    {
        return $this->updateStatus($bookingId, Booking::STATUS_COMPLETED, $shouldNotifyUser);
    }

    private function sendStatusChangeNotification($bookingData, $oldStatusLabel, $newStatusLabel)
    {
        try {
            $fullBookingDetails = $this->bookingModel->getDetailById($bookingData['id']);
            if (!$fullBookingDetails) return;

            $notificationService = new NotificationService();
            $notificationResult = $notificationService->notifyStatusChange(
                booking: $fullBookingDetails,
                oldStatus: $oldStatusLabel,
                newStatus: $newStatusLabel,
                sendEmail: true,
                createInApp: true
            );

            if ($notificationResult['success']) {
                LoggerService::info("Notification sent", ['booking_id' => $bookingData['id'], 'new_status' => $newStatusLabel]);
            } else {
                LoggerService::error("Notification failed", ['booking_id' => $bookingData['id'], 'error' => $notificationResult['message']]);
            }
        } catch (Exception $e) {
            LoggerService::error("Unexpected notification error", ['booking_id' => $bookingData['id'], 'error' => $e->getMessage()]);
        }
    }

    public function updateBookingDates(int $bookingId, string $requestedCheckIn, string $requestedCheckOut): bool
    {
        try {
            $existingBookingData = $this->bookingModel->findById($bookingId);
            if (!$existingBookingData) return false;

            $updatedNightsCount = ceil((strtotime($requestedCheckOut) - strtotime($requestedCheckIn)) / (60 * 60 * 24));
            $associatedRoomTypeData = $this->roomTypeModel->findById($existingBookingData['room_type_id']);
            $nightPriceRate = $associatedRoomTypeData['base_price'] ?? 0;
            $newRecalculatedTotal = $nightPriceRate * $updatedNightsCount * ($existingBookingData['room_qty'] ?? 1);

            $stmtUpdateTotal = $this->pdo->prepare("UPDATE bookings SET total_initial_price = :total_price WHERE id = :id");
            $stmtUpdateTotal->execute(['total_price' => $newRecalculatedTotal, 'id' => $bookingId]);

            $stmtUpdateDatesDetail = $this->pdo->prepare("UPDATE booking_details SET check_in_date = :check_in, check_out_date = :check_out WHERE booking_id = :booking_id");
            $stmtUpdateDatesDetail->execute([
                'check_in' => $requestedCheckIn,
                'check_out' => $requestedCheckOut,
                'booking_id' => $bookingId
            ]);

            LoggerService::info("Booking dates updated", ['booking_id' => $bookingId, 'new_total' => $newRecalculatedTotal]);
            return true;
        } catch (Exception $e) {
            LoggerService::error("Update booking dates failed", ['booking_id' => $bookingId, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function getReviewableBookingForRoomType(int $userId, int $roomTypeId): ?array
    {
        if ($userId <= 0 || $roomTypeId <= 0) return null;

        $sql = "
            SELECT
                b.id AS booking_id,
                bd.room_id,
                bd.check_out_date
            FROM bookings b
            INNER JOIN booking_details bd ON bd.booking_id = b.id
            INNER JOIN rooms r ON r.id = bd.room_id
            LEFT JOIN reviews rv ON rv.booking_id = b.id
            WHERE b.user_id = :user_id
              AND r.room_type_id = :room_type_id
              AND b.status = :status
              AND rv.id IS NULL
            ORDER BY bd.check_out_date DESC, b.id DESC
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'room_type_id' => $roomTypeId,
            'status' => BookingStatus::COMPLETED,
        ]);

        $foundBookingRow = $stmt->fetch(PDO::FETCH_ASSOC);
        return $foundBookingRow ?: null;
    }

    public function canUserReviewRoomType(int $userId, int $roomTypeId): bool
    {
        return $this->getReviewableBookingForRoomType($userId, $roomTypeId) !== null;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}


