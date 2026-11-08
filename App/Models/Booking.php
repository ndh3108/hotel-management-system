<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/** Model Booking - Bảng bookings. CRUD: getAll, findById, create, update, delete. */
class Booking extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CHECKED_IN = 'checked_in';
    public const STATUS_CHECKED_OUT = 'checked_out';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_NO_SHOW = 'no_show';

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_DEPOSIT_PENDING = 'deposit_pending';
    public const PAYMENT_PARTIAL_PAID = 'partial_paid';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_OVERDUE = 'overdue';
    public const PAYMENT_REFUNDING = 'refunding';
    public const PAYMENT_REFUNDED = 'refunded';
    public const PAYMENT_FAILED = 'failed';

    protected string $table = 'bookings';

    protected array $fillable = [
        'user_id',
        'room_type_id',
        'room_qty',
        'booking_code',
        'voucher_id',
        'total_initial_price',
        'status',
        'booking_status',
        'payment_status',
        'expired_at',
        'guest_token',
        'idempotency_key',
    ];

    public static function bookingStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_PAYMENT => 'Pending payment',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CHECKED_IN => 'Checked in',
            self::STATUS_CHECKED_OUT => 'Checked out',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_NO_SHOW => 'No show',
        ];
    }

    public static function paymentStatusOptions(): array
    {
        return [
            self::PAYMENT_UNPAID => 'Unpaid',
            self::PAYMENT_DEPOSIT_PENDING => 'Deposit pending',
            self::PAYMENT_PARTIAL_PAID => 'Partial paid',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_OVERDUE => 'Overdue',
            self::PAYMENT_REFUNDING => 'Refunding',
            self::PAYMENT_REFUNDED => 'Refunded',
            self::PAYMENT_FAILED => 'Failed',
        ];
    }

    public static function normalizeBookingStatus(?string $status): string
    {
        $normalized = strtolower(trim((string) $status));

        return match ($normalized) {
            'draft' => self::STATUS_DRAFT,
            'pending', 'processing', 'pending_payment' => self::STATUS_PENDING_PAYMENT,
            'paid', 'confirmed' => self::STATUS_CONFIRMED,
            'checked_in' => self::STATUS_CHECKED_IN,
            'checked_out' => self::STATUS_CHECKED_OUT,
            'completed' => self::STATUS_COMPLETED,
            'cancelled' => self::STATUS_CANCELLED,
            'expired' => self::STATUS_EXPIRED,
            'no_show' => self::STATUS_NO_SHOW,
            default => self::STATUS_PENDING_PAYMENT,
        };
    }

    public static function normalizePaymentStatus(?string $status, ?string $bookingStatus = null): string
    {
        $normalized = strtolower(trim((string) $status));

        if ($normalized !== '') {
            return match ($normalized) {
                'unpaid' => self::PAYMENT_UNPAID,
                'deposit_pending' => self::PAYMENT_DEPOSIT_PENDING,
                'partial_paid' => self::PAYMENT_PARTIAL_PAID,
                'paid' => self::PAYMENT_PAID,
                'overdue' => self::PAYMENT_OVERDUE,
                'refunding' => self::PAYMENT_REFUNDING,
                'refunded' => self::PAYMENT_REFUNDED,
                'failed' => self::PAYMENT_FAILED,
                default => self::PAYMENT_UNPAID,
            };
        }

        $bookingStatus = self::normalizeBookingStatus($bookingStatus);

        return match ($bookingStatus) {
            self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN, self::STATUS_CHECKED_OUT, self::STATUS_COMPLETED => self::PAYMENT_PAID,
            self::STATUS_CANCELLED => self::PAYMENT_FAILED,
            self::STATUS_EXPIRED, self::STATUS_NO_SHOW => self::PAYMENT_OVERDUE,
            default => self::PAYMENT_UNPAID,
        };
    }
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
    /**
     * Get recent bookings with user and room info
     */
    public function getRecent(int $limit = 5): array
    {
        $sql = "SELECT b.*, bd.check_in_date as check_in, bd.check_out_date as check_out, bd.price_per_night,
                       u.full_name as guest_name,
                       r.room_number,
                       b.total_initial_price as total_price
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                LEFT JOIN booking_details bd ON bd.booking_id = b.id
                LEFT JOIN rooms r ON bd.room_id = r.id
                ORDER BY b.created_at DESC
                LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get all bookings with user info
     */
    public function getAllWithDetails(): array
    {
        $sql = "SELECT b.*, bd.check_in_date as check_in, bd.check_out_date as check_out, 
                       b.total_initial_price as total_price,
                       u.full_name as guest_name, u.email, u.phone
                FROM bookings b
                LEFT JOIN booking_details bd ON bd.booking_id = b.id
                LEFT JOIN users u ON b.user_id = u.id
                ORDER BY b.created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getCursorPageWithDetails(int $lastId = 0, int $limit = 15): array
    {
        $sql = "SELECT b.*, bd.check_in_date as check_in, bd.check_out_date as check_out, 
                       b.total_initial_price as total_price,
                       u.full_name as guest_name, u.email, u.phone
                FROM bookings b
                LEFT JOIN booking_details bd ON bd.booking_id = b.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.id > :last_id
                ORDER BY b.id ASC
                LIMIT :limit_rows";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':last_id', $lastId, PDO::PARAM_INT);
        $stmt->bindValue(':limit_rows', $limit + 1, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }

    public function getPaginatedWithDetails(int $cursor = 0, int $limit = 10, array $filters = []): array
    {
        $limit = max(1, $limit);

        $whereClause = "WHERE 1=1";
        $params = [];

        if ($cursor > 0) {
            $whereClause .= " AND b.id < :cursor";
            $params[':cursor'] = $cursor;
        }

        if (!empty($filters['status'])) {
            $whereClause .= " AND COALESCE(NULLIF(b.booking_status, ''), b.status) = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['date'])) {
            $whereClause .= " AND bd.check_in_date <= :date AND bd.check_out_date >= :date";
            $params[':date'] = $filters['date'];
        }

        if (!empty($filters['keyword'])) {
            $whereClause .= " AND (u.full_name LIKE :keyword OR u.email LIKE :keyword OR u.phone LIKE :keyword)";
            $params[':keyword'] = '%' . $filters['keyword'] . '%';
        }

        $sql = "SELECT b.*, bd.check_in_date as check_in, bd.check_out_date as check_out, 
                       b.total_initial_price as total_price,
                       u.full_name as guest_name, u.email, u.phone
                FROM bookings b
                LEFT JOIN booking_details bd ON bd.booking_id = b.id
                LEFT JOIN users u ON b.user_id = u.id
                $whereClause
                ORDER BY b.id DESC
                LIMIT :limit_rows";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit_rows', $limit + 1, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }

    /**
     * Get booking detail by ID
     */
    public function getDetailById(int $id): ?array
    {
        $sql = "SELECT b.*, bd.check_in_date as check_in, bd.check_out_date as check_out, 
                       b.total_initial_price as total_price,
                       u.full_name as guest_name, u.email, u.phone,
                       bd.room_id, bd.price_per_night as booking_price,
                       r.room_number, r.room_type_id,
                       rt.name as room_type_name
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                LEFT JOIN booking_details bd ON bd.booking_id = b.id
                LEFT JOIN rooms r ON bd.room_id = r.id
                LEFT JOIN room_types rt ON r.room_type_id = rt.id
                WHERE b.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        return $booking ?: null;
    }
    
    /**
     * Get booking with full details (phòng, người dùng) - Dùng cho hóa đơn
     */
    public function getBookingWithDetails($id): ?array
    {
        $sql = "SELECT 
                    b.id,
                    b.user_id,
                    b.status,
                    b.total_initial_price as total_price,
                    b.created_at,
                    bd.check_in_date as check_in,
                    bd.check_out_date as check_out,
                    bd.price_per_night,
                    u.full_name as guest_name,
                    u.email as guest_email,
                    u.phone as guest_phone,
                    r.room_number,
                    r.room_type_id,
                    rt.name as room_type,
                    rt.description as room_description
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                LEFT JOIN booking_details bd ON bd.booking_id = b.id
                LEFT JOIN rooms r ON bd.room_id = r.id
                LEFT JOIN room_types rt ON r.room_type_id = rt.id
                WHERE b.id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Nếu không có price_per_night, thử lấy từ bảng room_prices nếu có
        if ($result && empty($result['price_per_night'])) {
            // Thử lấy giá từ bảng room_prices nếu có
            try {
                $sqlPrice = "SELECT price FROM room_prices WHERE room_type_id = ? AND ? BETWEEN start_date AND end_date";
                $stmtPrice = $this->pdo->prepare($sqlPrice);
                $checkIn = $result['check_in'] ?? date('Y-m-d');
                $stmtPrice->execute([$result['room_type_id'], $checkIn]);
                $priceData = $stmtPrice->fetch(PDO::FETCH_ASSOC);
                if ($priceData) {
                    $result['price_per_night'] = $priceData['price'];
                }
            } catch (\Exception $e) {
                // Bảng room_prices có thể không tồn tại
            }
            
            // Nếu vẫn không có, thử lấy giá mặc định từ cấu hình hoặc để 0
            if (empty($result['price_per_night'])) {
                $result['price_per_night'] = 0;
            }
        }
        
        return $result ?: null;
    }
    
    /**
     * Find booking by ID (alias cho find)
     */
    public function findById($id): ?array
    {
        $sql = "SELECT b.*, b.total_initial_price as total_price FROM bookings b WHERE b.id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function find($id)
    {
        return $this->findById($id);
    }
    
    /**
     * Cập nhật trạng thái booking - ĐÃ FIX: bỏ updated_at
     */
    public function updateStatus($id, $status): bool
    {
        // Không sử dụng updated_at vì cột này không tồn tại
        $sql = "UPDATE bookings SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id' => $id
        ]);
    }
    
    /**
     * Lưu log giao dịch thanh toán
     */
    public function saveTransactionLog($bookingId, $data, $type = 'success'): bool
    {
        // Kiểm tra bảng booking_payment_logs tồn tại chưa
        try {
            $sql = "CREATE TABLE IF NOT EXISTS booking_payment_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                booking_id INT NOT NULL,
                transaction_data TEXT,
                log_type VARCHAR(50) DEFAULT 'success',
                created_at DATETIME,
                FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
            )";
            $this->pdo->exec($sql);
        } catch (\Exception $e) {
            // Bảng có thể đã tồn tại
        }
        
        $sql = "INSERT INTO booking_payment_logs (booking_id, transaction_data, log_type, created_at) 
                VALUES (:booking_id, :transaction_data, :log_type, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'booking_id' => $bookingId,
            'transaction_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'log_type' => $type
        ]);
    }
    
    /**
     * Tạo booking mới - ĐÃ FIX: bỏ updated_at nếu không tồn tại
     */
    public function createBooking($data): int|false
    {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        $fields['created_at'] = date('Y-m-d H:i:s');
        // Không thêm updated_at vì cột không tồn tại
        
        $columns = implode(', ', array_keys($fields));
        $placeholders = ':' . implode(', :', array_keys($fields));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        
        if ($stmt->execute($fields)) {
            return (int) $this->pdo->lastInsertId();
        }
        
        return false;
    }
    
    public function getByUser($userId)
    {
        $sql = "
            SELECT 
                b.id,
                MAX(bd.check_in_date) as check_in,
                MAX(bd.check_out_date) as check_out,
                b.status,
                b.booking_status,
                b.payment_status,
                GROUP_CONCAT(rd.room_number SEPARATOR ', ') as room_number,
                GROUP_CONCAT(DISTINCT rt.name SEPARATOR ', ') AS room_type,
                b.total_initial_price as total_price,
                b.room_qty
            FROM bookings b
            LEFT JOIN booking_details bd 
                ON bd.booking_id = b.id
            LEFT JOIN rooms rd 
                ON rd.id = bd.room_id
            LEFT JOIN room_types rt 
                ON rt.id = rd.room_type_id
            WHERE b.user_id = ?
            GROUP BY b.id
            ORDER BY b.id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCursorPageByUser(int $userId, int $lastId = 0, int $limit = 10): array
    {
        $idComparison = $lastId > 0 ? "AND b.id < :last_id" : "";
        
        $sql = "
            SELECT 
                b.id,
                MAX(bd.check_in_date) as check_in,
                MAX(bd.check_out_date) as check_out,
                b.status,
                b.booking_status,
                b.payment_status,
                GROUP_CONCAT(rd.room_number SEPARATOR ', ') as room_number,
                GROUP_CONCAT(DISTINCT rt.name SEPARATOR ', ') AS room_type,
                b.total_initial_price as total_price,
                b.room_qty,
                b.created_at
            FROM bookings b
            LEFT JOIN booking_details bd 
                ON bd.booking_id = b.id
            LEFT JOIN rooms rd 
                ON rd.id = bd.room_id
            LEFT JOIN room_types rt 
                ON rt.id = rd.room_type_id
            WHERE b.user_id = :user_id
              $idComparison
            GROUP BY b.id
            ORDER BY b.id DESC
            LIMIT :limit_rows
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        if ($lastId > 0) {
            $stmt->bindValue(':last_id', $lastId, \PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit_rows', $limit + 1, \PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [], $limit);
    }

    public function getPaginatedByUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = :user_id");
        $countStmt->execute(['user_id' => $userId]);
        $totalItems = (int) $countStmt->fetchColumn();

        $sql = "
            SELECT 
                b.id,
                MAX(bd.check_in_date) as check_in,
                MAX(bd.check_out_date) as check_out,
                b.status,
                b.booking_status,
                b.payment_status,
                GROUP_CONCAT(rd.room_number SEPARATOR ', ') as room_number,
                GROUP_CONCAT(DISTINCT rt.name SEPARATOR ', ') AS room_type,
                b.total_initial_price as total_price,
                b.room_qty
            FROM bookings b
            LEFT JOIN booking_details bd 
                ON bd.booking_id = b.id
            LEFT JOIN rooms rd 
                ON rd.id = bd.room_id
            LEFT JOIN room_types rt 
                ON rt.id = rd.room_type_id
            WHERE b.user_id = :user_id
            GROUP BY b.id
            ORDER BY b.id ASC
            LIMIT :limit_rows OFFSET :offset_rows
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit_rows', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset_rows', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildPageResult($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $page, $perPage, $totalItems);
    }
    
    public function getDetailBill($id)
    {
        $sql = "
            SELECT 
                b.id,
                MAX(bd.check_in_date) as check_in,
                MAX(bd.check_out_date) as check_out,
                b.total_initial_price as total_price,
                b.status,
                GROUP_CONCAT(rd.room_number SEPARATOR ', ') as room_number,
                GROUP_CONCAT(DISTINCT rt.name SEPARATOR ', ') AS room_type
            FROM bookings b
            LEFT JOIN booking_details bd ON bd.booking_id = b.id
            LEFT JOIN rooms rd ON rd.id = bd.room_id
            LEFT JOIN room_types rt ON rt.id = rd.room_type_id
            WHERE b.id = ?
            GROUP BY b.id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getActiveBookingsByRoom($roomId)
    {
        $sql = "
        SELECT 
            bd.check_in_date as check_in,
            bd.check_out_date as check_out
        FROM bookings b
        INNER JOIN booking_details bd 
            ON bd.booking_id = b.id
        WHERE bd.room_id = :room_id
        AND COALESCE(NULLIF(b.booking_status, ''), b.status) IN (
            'pending_payment','confirmed','checked_in','checked_out'
        )
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['room_id' => $roomId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy tổng số tiền của booking
     */
    public function getTotalPrice($bookingId)
    {
        $sql = "SELECT total_initial_price as total_price FROM bookings WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$bookingId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_price'] ?? 0;
    }

    public function getTimeline(int $bookingId): array
    {
        $timeline = [];

        $stmtLogs = $this->pdo->prepare("
            SELECT created_at, action, details
            FROM booking_logs
            WHERE booking_id = ?
            ORDER BY created_at ASC, id ASC
        ");
        $stmtLogs->execute([$bookingId]);
        foreach ($stmtLogs->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
            $timeline[] = [
                'created_at' => $row['created_at'],
                'old_status' => null,
                'new_status' => $row['action'],
                'message' => $row['details'] ?? '',
                'source' => 'booking_log',
            ];
        }

        $stmtNotifications = $this->pdo->prepare("
            SELECT created_at, old_status, new_status, message
            FROM notifications
            WHERE booking_id = ?
            ORDER BY created_at ASC, id ASC
        ");
        $stmtNotifications->execute([$bookingId]);
        foreach ($stmtNotifications->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
            $timeline[] = [
                'created_at' => $row['created_at'],
                'old_status' => $row['old_status'] ?? null,
                'new_status' => $row['new_status'] ?? null,
                'message' => $row['message'] ?? '',
                'source' => 'notification',
            ];
        }

        usort($timeline, static fn(array $a, array $b) => strcmp((string) ($a['created_at'] ?? ''), (string) ($b['created_at'] ?? '')));
        return $timeline;
    }
}

