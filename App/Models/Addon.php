<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/** Model Addon - Bảng addons. */
class Addon extends Model
{
    protected string $table = 'addons';

    protected array $fillable = [
        'name',
        'price',
        'description',
        'unit',
    ];

    /**
     * Dịch vụ addon (đi kèm loại phòng khi hiển thị)
     */
    public function getAddons(): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getCursorPage(int $lastId = 0, int $limit = 15): array
    {
        $sql = "SELECT *
                FROM `{$this->table}`
                WHERE id > :last_id
                ORDER BY id ASC
                LIMIT :limit_rows";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':last_id', $lastId, PDO::PARAM_INT);
        $stmt->bindValue(':limit_rows', $limit + 1, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }
}

