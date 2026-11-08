<?php

namespace App\Services;

use App\Core\BookingStatus;
use App\Core\Database;
use PDO;

class DashboardService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getOverview(?string $dateFrom = null, ?string $dateTo = null): array
    {
        [$dateFrom, $dateTo] = $this->normalizeDateRange($dateFrom, $dateTo);

        $revenueStmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(total_initial_price), 0)
            FROM bookings
            WHERE booking_status = :status
              AND DATE(created_at) BETWEEN :date_from AND :date_to
        ");
        $revenueStmt->execute([
            'status' => BookingStatus::COMPLETED,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        $bookingStmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM bookings
            WHERE DATE(created_at) BETWEEN :date_from AND :date_to
        ");
        $bookingStmt->execute([
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        return [
            'revenue' => (float) $revenueStmt->fetchColumn(),
            'bookings' => (int) $bookingStmt->fetchColumn(),
            'occupied_rooms' => (int) $this->pdo->query("SELECT COUNT(*) FROM rooms WHERE status = 'Occupied'")->fetchColumn(),
            'total_users' => (int) $this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'pending_reviews' => (int) $this->pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'Pending'")->fetchColumn(),
            'total_payments' => (int) $this->pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'Completed'")->fetchColumn(),
            'loyalty_members' => (int) $this->pdo->query("SELECT COUNT(*) FROM loyalty_memberships")->fetchColumn(),
        ];
    }

    public function revenueByDate(?string $dateFrom = null, ?string $dateTo = null): array
    {
        [$dateFrom, $dateTo] = $this->normalizeDateRange($dateFrom, $dateTo);

        $stmt = $this->pdo->prepare("
            SELECT DATE(created_at) AS chart_date, COALESCE(SUM(total_initial_price), 0) AS total
            FROM bookings
            WHERE booking_status = :status
              AND DATE(created_at) BETWEEN :date_from AND :date_to
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ");
        $stmt->execute([
            'status' => BookingStatus::COMPLETED,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function bookingsByDate(?string $dateFrom = null, ?string $dateTo = null): array
    {
        [$dateFrom, $dateTo] = $this->normalizeDateRange($dateFrom, $dateTo);

        $stmt = $this->pdo->prepare("
            SELECT DATE(created_at) AS chart_date, COUNT(*) AS total
            FROM bookings
            WHERE DATE(created_at) BETWEEN :date_from AND :date_to
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ");
        $stmt->execute([
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function latestBookings(int $limit = 8): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                b.id,
                b.booking_status,
                b.total_initial_price AS total_price,
                b.created_at,
                u.full_name AS guest_name,
                bd.check_in_date AS check_in,
                bd.check_out_date AS check_out,
                r.room_number
            FROM bookings b
            LEFT JOIN users u ON u.id = b.user_id
            LEFT JOIN booking_details bd ON bd.booking_id = b.id
            LEFT JOIN rooms r ON r.id = bd.room_id
            ORDER BY b.id DESC
            LIMIT :limit_rows
        ");
        $stmt->bindValue(':limit_rows', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function latestUsers(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, full_name, email, phone, status
            FROM users
            ORDER BY id DESC
            LIMIT :limit_rows
        ");
        $stmt->bindValue(':limit_rows', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function normalizeDateRange(?string $dateFrom, ?string $dateTo): array
    {
        $dateTo = $dateTo && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo) ? $dateTo : date('Y-m-d');
        $dateFrom = $dateFrom && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)
            ? $dateFrom
            : date('Y-m-d', strtotime($dateTo . ' -29 days'));

        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        return [$dateFrom, $dateTo];
    }
}

