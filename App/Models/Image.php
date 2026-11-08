<?php

namespace App\Models;

use App\Core\Model;

class Image extends Model
{
    protected string $table = 'images';

    protected array $fillable = [
        'entity_type',
        'entity_id',
        'image_url',
        'public_id',
        'is_primary',
    ];

    public function setPrimary(string $entityType, int $entityId, int $imageId): bool
    {
        try {
            $this->pdo->beginTransaction();

            // Clear current primary
            $sqlClear = "UPDATE `{$this->table}` SET `is_primary` = 0 WHERE `entity_type` = ? AND `entity_id` = ?";
            $this->pdo->prepare($sqlClear)->execute([$entityType, $entityId]);

            // Set new primary
            $sqlSet = "UPDATE `{$this->table}` SET `is_primary` = 1 WHERE `id` = ? AND `entity_type` = ? AND `entity_id` = ?";
            $this->pdo->prepare($sqlSet)->execute([$imageId, $entityType, $entityId]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function getByEntity(string $entityType, int $entityId): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `entity_type` = ? AND `entity_id` = ? ORDER BY `is_primary` DESC, `id` ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$entityType, $entityId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function countByEntity(string $entityType, int $entityId): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `entity_type` = ? AND `entity_id` = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$entityType, $entityId]);
        return (int) $stmt->fetchColumn();
    }

    public function deleteByEntity(string $entityType, int $entityId): bool
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `entity_type` = ? AND `entity_id` = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$entityType, $entityId]);
    }

    public function getFirstByEntity(string $entityType, int $entityId): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `entity_type` = ? AND `entity_id` = ? ORDER BY `id` ASC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$entityType, $entityId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}

