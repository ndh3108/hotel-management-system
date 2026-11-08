<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\RoomInventory;
use App\Models\InventoryItem;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use App\Core\Database;

class InventoryController extends Controller
{
    private RoomInventory $inventoryModel;
    private InventoryItem $itemModel;
    private RoomType $roomTypeModel;
    private AvailabilityService $availabilityService;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('MANAGE_INVENTORY');
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->inventoryModel = new RoomInventory();
        $this->itemModel = new InventoryItem();
        $this->roomTypeModel = new RoomType();
        $this->availabilityService = new AvailabilityService();
    }

    public function index(): void
    {
        $cursor = (int) $this->input('cursor', 0);
        $pageNumber = max(1, (int) $this->input('page', 1));
        
        $page = $this->inventoryModel->getPaginatedWithRoomType($cursor, 10);
        $date = (string) $this->input('date', date('Y-m-d'));

        $this->useLayout = true;
        $this->render('admin/inventory/index', [
            'title' => 'Định mức vật tư - Admin',
            'pageTitle' => 'Định Mức Vật Tư (Theo Phòng/Loại)',
            'currentPage' => 'inventory',
            'inventories' => $page['items'],
            'hasMore' => $page['has_more'],
            'nextCursor' => $page['next_last_id'],
            'pageNumber' => $pageNumber,
        ]);
    }

    public function create(): void
    {
        $roomTypes = $this->roomTypeModel->getAll();
        $catalogItems = $this->itemModel->getAllActive();
        
        $pdo = Database::getInstance()->getConnection();
        $rooms = $pdo->query("SELECT id, room_number FROM rooms WHERE status != 'Maintenance' ORDER BY room_number ASC")->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $this->useLayout = true;
        $this->render('Admin/inventory/form', [
            'title' => 'Thêm định mức vật tư - Admin',
            'pageTitle' => 'Thêm Định Mức Vật Tư',
            'currentPage' => 'inventory',
            'roomTypes' => $roomTypes,
            'rooms' => $rooms,
            'catalogItems' => $catalogItems,
            'inventory' => null,
        ]);
    }

    public function store(): void
    {
        $data = [
            'room_type_id' => $this->input('room_type_id') ?: null,
            'room_id' => $this->input('room_id') ?: null,
            'item_id' => $this->input('item_id'),
            'quantity' => $this->input('quantity') ?: 1,
            'price_override' => $this->input('price_override') ?: null,
            'is_active' => 1
        ];
        $this->inventoryModel->create($data);
        $this->redirect(\App\Core\url('/admin/inventory'));
    }

    public function edit(int $id): void
    {
        $inventory = $this->inventoryModel->findById($id);
        if (!$inventory) {
            $this->redirect(\App\Core\url('/admin/inventory'));
            return;
        }

        $roomTypes = $this->roomTypeModel->getAll();
        $catalogItems = $this->itemModel->getAllActive();
        
        $pdo = Database::getInstance()->getConnection();
        $rooms = $pdo->query("SELECT id, room_number FROM rooms WHERE status != 'Maintenance' ORDER BY room_number ASC")->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $this->useLayout = true;
        $this->render('Admin/inventory/form', [
            'title' => 'Sửa định mức vật tư - Admin',
            'pageTitle' => 'Sửa Định Mức Vật Tư',
            'currentPage' => 'inventory',
            'roomTypes' => $roomTypes,
            'rooms' => $rooms,
            'catalogItems' => $catalogItems,
            'inventory' => $inventory,
        ]);
    }

    public function update(int $id): void
    {
        $data = [
            'room_type_id' => $this->input('room_type_id') ?: null,
            'room_id' => $this->input('room_id') ?: null,
            'item_id' => $this->input('item_id'),
            'quantity' => $this->input('quantity') ?: 1,
            'price_override' => $this->input('price_override') ?: null,
            'is_active' => $this->input('is_active', 1),
        ];
        $this->inventoryModel->update($id, $data);
        $this->redirect(\App\Core\url('/admin/inventory'));
    }

    public function destroy(int $id): void
    {
        $this->inventoryModel->delete($id);
        $this->redirect(\App\Core\url('/admin/inventory'));
    }
}

