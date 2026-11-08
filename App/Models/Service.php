<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Service extends Model
{
    protected string $table = 'services';

    protected array $fillable = [
        'service_type_id',
        'name',
        'capacity',
        'price_per_hour',
        'description',
        'status',
    ];

    public function getWithTypeName(): array
    {
        $sql = "SELECT s.*, st.name as type_name,
                       (SELECT image_url FROM images i WHERE i.entity_type = 'service' AND i.entity_id = s.id ORDER BY i.id ASC LIMIT 1) as image_url
                FROM services s 
                LEFT JOIN service_types st ON s.service_type_id = st.id
                ORDER BY s.id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCursorPageWithTypeName(int $lastId = 0, int $limit = 12, bool $availableOnly = false): array
    {
        $sql = "SELECT s.*, st.name as type_name,
                       (SELECT image_url FROM images i WHERE i.entity_type = 'service' AND i.entity_id = s.id ORDER BY i.id ASC LIMIT 1) as image_url
                FROM services s 
                LEFT JOIN service_types st ON s.service_type_id = st.id
                WHERE s.id > :last_id";

        if ($availableOnly) {
            $sql .= " AND s.status = 'AVAILABLE'";
        }

        $sql .= " ORDER BY s.id ASC LIMIT :limit_rows";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':last_id', $lastId, PDO::PARAM_INT);
        $stmt->bindValue(':limit_rows', $limit + 1, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }

    public function getPaginatedWithTypeName(int $cursor = 0, int $limit = 10, bool $availableOnly = false): array
    {
        $limit = max(1, $limit);
        $fetchLimit = $limit + 1;

        $sql = "SELECT s.*, st.name as type_name,
                       (SELECT image_url FROM images i WHERE i.entity_type = 'service' AND i.entity_id = s.id ORDER BY i.id ASC LIMIT 1) as image_url
                FROM services s 
                LEFT JOIN service_types st ON s.service_type_id = st.id";

        $whereClauses = [];
        $params = [];

        if ($cursor > 0) {
            $whereClauses[] = "s.id > :cursor";
            $params[':cursor'] = $cursor;
        }

        if ($availableOnly) {
            $whereClauses[] = "s.status = 'AVAILABLE'";
        }

        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $sql .= " ORDER BY s.id ASC LIMIT :limit_rows";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit_rows', $fetchLimit, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }

    public function getAvailableWithTypeName(): array
    {
        $sql = "SELECT s.*, st.name as type_name,
                       (SELECT image_url FROM images i WHERE i.entity_type = 'service' AND i.entity_id = s.id ORDER BY i.id ASC LIMIT 1) as image_url
                FROM services s
                LEFT JOIN service_types st ON s.service_type_id = st.id
                WHERE s.status = 'AVAILABLE'
                ORDER BY s.id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findWithTypeName(int $id): ?array
    {
        $sql = "SELECT s.*, st.name as type_name,
                       (SELECT image_url FROM images i WHERE i.entity_type = 'service' AND i.entity_id = s.id ORDER BY i.id ASC LIMIT 1) as image_url
                FROM services s
                LEFT JOIN service_types st ON s.service_type_id = st.id
                WHERE s.id = ?
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}

