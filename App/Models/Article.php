<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Article extends Model
{
    protected string $table = 'articles';

    protected array $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'status',
        'author_id'
    ];

    public function getAllWithAuthorAndCategory(): array
    {
        $sql = "SELECT a.*, ac.name as category_name, u.full_name as author_name 
                FROM {$this->table} a
                LEFT JOIN article_categories ac ON a.category_id = ac.id
                LEFT JOIN users u ON a.author_id = u.id
                ORDER BY a.created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getCursorPageWithAuthorAndCategory(int $lastId = 0, int $limit = 15): array
    {
        $sql = "SELECT a.*, ac.name as category_name, u.full_name as author_name 
                FROM {$this->table} a
                LEFT JOIN article_categories ac ON a.category_id = ac.id
                LEFT JOIN users u ON a.author_id = u.id
                WHERE a.id > :last_id
                ORDER BY a.id ASC
                LIMIT :limit_rows";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':last_id', $lastId, PDO::PARAM_INT);
        $stmt->bindValue(':limit_rows', $limit + 1, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }

    public function getPaginatedWithAuthorAndCategory(int $cursor = 0, int $limit = 10): array
    {
        $limit = max(1, $limit);
        $fetchLimit = $limit + 1;

        $sql = "SELECT a.*, ac.name as category_name, u.full_name as author_name 
                FROM {$this->table} a
                LEFT JOIN article_categories ac ON a.category_id = ac.id
                LEFT JOIN users u ON a.author_id = u.id";

        if ($cursor > 0) {
            $sql .= " WHERE a.id > :cursor";
        }

        $sql .= " ORDER BY a.id ASC LIMIT :limit_rows";

        $stmt = $this->pdo->prepare($sql);
        if ($cursor > 0) {
            $stmt->bindValue(':cursor', $cursor, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit_rows', $fetchLimit, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }
}

