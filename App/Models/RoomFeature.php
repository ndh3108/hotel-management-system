<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class RoomFeature extends Model
{
    protected string $table = 'room_features';

    protected array $fillable = [
        'name',
        'icon',
        'is_active'
    ];

    public function getAllActive(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getForRoom(int $roomId): array
    {
        $sql = "
            SELECT rf.*
            FROM {$this->table} rf
            JOIN room_feature_assignments rfa ON rfa.feature_id = rf.id
            WHERE rfa.room_id = ? AND rf.is_active = 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function syncForRoom(int $roomId, array $featureIds): void
    {
        $this->pdo->beginTransaction();
        try {
            $stmtDel = $this->pdo->prepare("DELETE FROM room_feature_assignments WHERE room_id = ?");
            $stmtDel->execute([$roomId]);

            if (!empty($featureIds)) {
                $stmtIns = $this->pdo->prepare("INSERT INTO room_feature_assignments (room_id, feature_id) VALUES (?, ?)");
                foreach ($featureIds as $fid) {
                    $stmtIns->execute([$roomId, $fid]);
                }
            }
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}

