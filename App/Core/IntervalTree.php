<?php

namespace App\Core;

/**
 * Interval Tree - Cấu trúc dữ liệu cho kiểm tra overlap hiệu quả O(log n).
 * Sử dụng khi cần scale với nhiều booking trong bộ nhớ.
 * Overlap: [start1, end1) giao [start2, end2) khi start1 < end2 AND end1 > start2.
 */
class IntervalTree
{
    private ?IntervalNode $root = null;
    private int $count = 0;

    /**
     * Thêm interval [start, end). Trả về false nếu overlap với interval đã có.
     */
    public function insert(string $start, string $end, $data = null): bool
    {
        if ($this->hasOverlap($start, $end)) {
            return false;
        }
        $this->root = $this->insertNode($this->root, $start, $end, $data);
        $this->count++;
        return true;
    }

    private function insertNode(?IntervalNode $node, string $start, string $end, $data): IntervalNode
    {
        if ($node === null) {
            return new IntervalNode($start, $end, $data);
        }
        $cmp = strcmp($start, $node->start);
        if ($cmp < 0) {
            $node->left = $this->insertNode($node->left, $start, $end, $data);
        } else {
            $node->right = $this->insertNode($node->right, $start, $end, $data);
        }
        $node->maxEnd = max($node->maxEnd ?? $node->end, $end);
        return $node;
    }

    /**
     * Kiểm tra có overlap với [start, end) không.
     * Overlap khi: existing.start < new.end AND existing.end > new.start
     */
    public function hasOverlap(string $start, string $end): bool
    {
        return $this->findOverlap($this->root, $start, $end) !== null;
    }

    private function findOverlap(?IntervalNode $node, string $start, string $end): ?IntervalNode
    {
        if ($node === null) {
            return null;
        }
        if ($node->start < $end && $node->end > $start) {
            return $node;
        }
        if ($node->left !== null && ($node->left->maxEnd ?? $node->left->end) > $start) {
            $found = $this->findOverlap($node->left, $start, $end);
            if ($found !== null) {
                return $found;
            }
        }
        return $this->findOverlap($node->right, $start, $end);
    }

    public function size(): int
    {
        return $this->count;
    }

    /**
     * Build tree từ mảng intervals [['start'=>..., 'end'=>...], ...]
     */
    public static function fromIntervals(array $intervals): self
    {
        $tree = new self();
        usort($intervals, fn($a, $b) => strcmp($a['start'] ?? '', $b['start'] ?? ''));
        foreach ($intervals as $iv) {
            $tree->insert($iv['start'] ?? '', $iv['end'] ?? '', $iv);
        }
        return $tree;
    }
}

class IntervalNode
{
    public string $start;
    public string $end;
    public $data;
    public ?IntervalNode $left = null;
    public ?IntervalNode $right = null;
    public ?string $maxEnd = null;

    public function __construct(string $start, string $end, $data = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->data = $data;
        $this->maxEnd = $end;
    }
}

