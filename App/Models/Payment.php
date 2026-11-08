<?php

namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected string $table = 'payments';

    protected array $fillable = [
        'invoice_id',
        'method',
        'amount_paid',
        'status',
        'payment_date',
        'paid_at',
    ];

    public function create($data)
    {
        $sql = "INSERT INTO payments
        (invoice_id, method, amount_paid, status, payment_date)
        VALUES
        (:invoice_id, :method, :amount_paid, :status, NOW())";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function updateStatus($invoice_id, $status)
    {
        $sql = "UPDATE payments 
                SET status = :status, paid_at = NOW()
                WHERE invoice_id = :invoice_id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'invoice_id' => $invoice_id
        ]);
    }
    public function all()
    {
        $sql = "SELECT * FROM payments ORDER BY payment_date DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCursorPage(int $lastId = 0, int $limit = 15): array
    {
        $sql = "SELECT *
                FROM payments
                WHERE id > :last_id
                ORDER BY id ASC
                LIMIT :limit_rows";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':last_id', $lastId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit_rows', $limit + 1, \PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [], $limit);
    }

    /**
     * Offset pagination cho màn Revenue.
     */
    public function getPaginated(int $page = 1, int $perPage = 12): array
    {
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) FROM payments";
        $totalItems = (int) $this->pdo->query($countSql)->fetchColumn();

        $sql = "
            SELECT *
            FROM payments
            ORDER BY id DESC
            LIMIT :limit_rows OFFSET :offset_rows
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit_rows', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset_rows', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildPageResult($stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [], $page, $perPage, $totalItems);
    }
}

