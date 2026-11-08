<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\InventoryDamage;
use App\Services\DamageService;
use App\Core\Auth;

class DamageManagementController extends Controller
{
    private $damageModel;
    private $damageService;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('MANAGE_INVENTORY');
        $this->viewPath = BASE_PATH . '/App/Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->damageModel = new InventoryDamage();
        $this->damageService = new DamageService();
    }

    public function index(): void
    {
        $status = $this->input('status', 'Reported');
        $cursor = (int) $this->input('cursor', 0);
        $pageNumber = max(1, (int) $this->input('page', 1));
        
        $pdo = \App\Core\Database::getInstance()->getConnection();
        
        $limit = 10;
        $params = [$status];
        $cursorSql = "";
        if ($cursor > 0) {
            $cursorSql = " AND idmg.id < ? ";
            $params[] = $cursor;
        }

        $sql = "
            SELECT idmg.*, r.room_number, u.full_name as reported_by_name, 
                   c.full_name as customer_name, b.id as booking_id
            FROM inventory_damage idmg
            LEFT JOIN rooms r ON r.id = idmg.room_id
            LEFT JOIN users u ON u.id = idmg.reported_by
            JOIN bookings b ON b.id = idmg.booking_id
            LEFT JOIN users c ON c.id = b.user_id
            WHERE idmg.status = ?
            $cursorSql
            ORDER BY idmg.id DESC
            LIMIT " . ($limit + 1) . "
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $damages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $hasMore = false;
        $nextCursor = 0;
        if (count($damages) > $limit) {
            $hasMore = true;
            array_pop($damages);
            $nextCursor = end($damages)['id'];
        }

        $this->useLayout = true;
        $this->render('admin/inventory/damage/index', [
            'title' => 'Quản lý hỏng hóc - Admin',
            'pageTitle' => 'Quản Lý Báo Cáo Hỏng Hóc',
            'currentPage' => 'inventory_damage',
            'damages' => $damages,
            'currentStatus' => $status,
            'hasMore' => $hasMore,
            'nextCursor' => $nextCursor,
            'pageNumber' => $pageNumber
        ]);
    }

    public function approve(int $id): void
    {
        $adminId = $_SESSION['user']['id'] ?? 1;
        $price = $this->input('override_price') ? (float)$this->input('override_price') : null;

        if ($this->damageService->approveDamage($id, $adminId, $price)) {
            $this->redirect(\App\Core\url('/admin/inventory/damage?status=Approved&success=1'));
        } else {
            $this->redirect(\App\Core\url('/admin/inventory/damage?error=approve_failed'));
        }
    }

    public function pay(int $id): void
    {
        if ($this->damageService->markAsPaid($id)) {
            $this->redirect(\App\Core\url('/admin/inventory/damage?status=Paid&success=1'));
        } else {
            $this->redirect(\App\Core\url('/admin/inventory/damage?error=payment_failed'));
        }
    }

    public function replace(int $id): void
    {
        $adminId = $_SESSION['user']['id'] ?? 1;
        $result = $this->damageService->replaceFromStock($id, $adminId);
        
        if ($result['success']) {
            $this->redirect(\App\Core\url('/admin/inventory/damage?status=Replaced&success=1&msg=' . urlencode($result['message'])));
        } else {
            $this->redirect(\App\Core\url('/admin/inventory/damage?error=replace_failed&msg=' . urlencode($result['message'])));
        }
    }

    public function reject(int $id): void
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE inventory_damage SET status = 'Rejected' WHERE id = ? AND status = 'Reported'");
        if ($stmt->execute([$id])) {
            $this->redirect(\App\Core\url('/admin/inventory/damage?status=Rejected&success=1'));
        } else {
            $this->redirect(\App\Core\url('/admin/inventory/damage?error=reject_failed'));
        }
    }
}

