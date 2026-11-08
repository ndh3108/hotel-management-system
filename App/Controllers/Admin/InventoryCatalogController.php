<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\InventoryItem;
use App\Core\Auth;

class InventoryCatalogController extends Controller
{
    private $itemModel;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('MANAGE_INVENTORY');
        $this->viewPath = BASE_PATH . '/App/Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->itemModel = new \App\Models\InventoryItem();
    }

    public function index(): void
    {
        $cursor = (int) $this->input('cursor', 0);
        $pageNumber = max(1, (int) $this->input('page', 1));
        $limit = 10;
        
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $params = [];
        $cursorSql = "";
        if ($cursor > 0) {
            $cursorSql = " AND id < ? ";
            $params[] = $cursor;
        }

        $sql = "SELECT * FROM inventory_items WHERE deleted_at IS NULL $cursorSql ORDER BY id DESC LIMIT " . ($limit + 1);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $hasMore = false;
        $nextCursor = 0;
        if (count($items) > $limit) {
            $hasMore = true;
            array_pop($items);
            $nextCursor = end($items)['id'];
        }
        
        $this->useLayout = true;
        $this->render('admin/inventory/catalog/index', [
            'title' => 'Danh mục vật tư - Admin',
            'pageTitle' => 'Quản Lý Danh Mục Vật Tư',
            'currentPage' => 'inventory_catalog',
            'items' => $items,
            'hasMore' => $hasMore,
            'nextCursor' => $nextCursor,
            'pageNumber' => $pageNumber
        ]);
    }

    public function create(): void
    {
        $this->useLayout = true;
        $this->render('admin/inventory/catalog/form', [
            'title' => 'Thêm vật tư - Admin',
            'pageTitle' => 'Thêm Vật Tư Mới',
            'currentPage' => 'inventory_catalog',
            'item' => null
        ]);
    }

    public function store(): void
    {
        $data = [
            'name' => $this->input('name'),
            'category' => $this->input('category'),
            'unit' => $this->input('unit'),
            'default_price' => (float)$this->input('default_price', 0),
            'stock_quantity' => (int)$this->input('stock_quantity', 0)
        ];

        if ($this->itemModel->create($data)) {
            $this->redirect(\App\Core\url('/admin/inventory/catalog?success=1'));
        } else {
            $this->redirect(\App\Core\url('/admin/inventory/catalog/create?error=1'));
        }
    }

    public function edit(int $id): void
    {
        $item = $this->itemModel->findById($id);
        if (!$item) {
            $this->redirect(\App\Core\url('/admin/inventory/catalog'));
            return;
        }

        $this->useLayout = true;
        $this->render('admin/inventory/catalog/form', [
            'title' => 'Sửa vật tư - Admin',
            'pageTitle' => 'Cập Nhật Thông Tin Vật Tư',
            'currentPage' => 'inventory_catalog',
            'item' => $item
        ]);
    }

    public function update(int $id): void
    {
        $data = [
            'name' => $this->input('name'),
            'category' => $this->input('category'),
            'unit' => $this->input('unit'),
            'default_price' => (float)$this->input('default_price', 0),
            'stock_quantity' => (int)$this->input('stock_quantity', 0)
        ];

        if ($this->itemModel->update($id, $data)) {
            $this->redirect(\App\Core\url('/admin/inventory/catalog?success=1'));
        } else {
            $this->redirect(\App\Core\url('/admin/inventory/catalog/edit/' . $id . '?error=1'));
        }
    }

    public function delete(int $id): void
    {
        // Simple soft delete if exists, or hard delete
        if ($this->itemModel->delete($id)) {
            $this->redirect(\App\Core\url('/admin/inventory/catalog?deleted=1'));
        }
    }
}

