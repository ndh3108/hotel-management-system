<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Room;
use App\Models\InventoryDamage;
use App\Services\DamageService;
use App\Core\Auth;

class HousekeepingController extends Controller
{
    private $roomModel;
    private $damageService;

    public function __construct()
    {
        parent::__construct();
        // Specific role check for production safety
        if (!Auth::hasRole('Housekeeping') && !Auth::hasRole('Admin') && !Auth::hasRole('SuperAdmin')) {
            $this->redirect(\App\Core\url('/admin/dashboard?error=access_denied'));
            exit;
        }
        $this->viewPath = BASE_PATH . '/App/Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->roomModel = new Room();
        $this->damageService = new DamageService();
    }

    public function index(): void
    {
        $rooms = $this->roomModel->getAllWithType();
        
        $this->useLayout = true;
        $this->render('admin/housekeeping/index', [
            'title' => 'Housekeeping Dashboard',
            'pageTitle' => 'Bảng Điều Khiển Buồng Phòng',
            'currentPage' => 'housekeeping',
            'rooms' => $rooms
        ]);
    }

    public function manageRoom(int $id): void
    {
        $room = $this->roomModel->findById($id);
        if (!$room) {
            $this->redirect(\App\Core\url('/admin/housekeeping'));
            return;
        }

        // Get active booking detail for this room to link damage
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $sql = "
            SELECT bd.id as booking_detail_id, b.id as booking_id, u.full_name as guest_name
            FROM booking_details bd
            JOIN bookings b ON b.id = bd.booking_id
            LEFT JOIN users u ON u.id = b.user_id
            WHERE bd.room_id = ? AND b.booking_status IN ('checked_in', 'confirmed')
            ORDER BY bd.id DESC LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $activeBooking = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Get inventory assigned to this room/type
        $invSql = "
            SELECT ri.*, ii.name as item_name, ii.default_price
            FROM room_inventory ri
            JOIN inventory_items ii ON ii.id = ri.item_id
            WHERE (ri.room_id = ? OR (ri.room_type_id = ? AND ri.room_id IS NULL))
            AND ri.is_active = 1
        ";
        $stmtInv = $pdo->prepare($invSql);
        $stmtInv->execute([$id, $room['room_type_id']]);
        $inventory = $stmtInv->fetchAll(\PDO::FETCH_ASSOC);

        $this->useLayout = true;
        $this->render('admin/housekeeping/manage_room', [
            'title' => 'Quản lý phòng - Housekeeping',
            'pageTitle' => 'Phòng ' . $room['room_number'],
            'currentPage' => 'housekeeping',
            'room' => $room,
            'activeBooking' => $activeBooking,
            'inventory' => $inventory
        ]);
    }

    public function reportDamage(): void
    {
        $roomId = (int)$this->input('room_id');
        $bookingDetailId = $this->input('booking_detail_id') ? (int)$this->input('booking_detail_id') : null;
        $itemId = (int)$this->input('item_id');
        $quantity = (int)$this->input('quantity', 1);
        $notes = $this->input('notes', '');
        $userId = $_SESSION['user']['id'] ?? 1;
        $nextStatus = $this->input('next_status');

        if ($this->damageService->reportDamage($bookingDetailId, $itemId, $quantity, $userId, $notes)) {
            if ($nextStatus && in_array($nextStatus, [Room::STATUS_MAINTENANCE, Room::STATUS_CLEANING, Room::STATUS_AVAILABLE])) {
                $this->roomModel->update($roomId, ['status' => $nextStatus]);
            }
            $this->redirect(\App\Core\url('/admin/housekeeping/manage/' . $roomId . '?success=reported'));
        } else {
            $this->redirect(\App\Core\url('/admin/housekeeping/manage/' . $roomId . '?error=report_failed'));
        }
    }

    public function quickReplace(): void
    {
        $roomId = (int)$this->input('room_id');
        $bookingDetailId = $this->input('booking_detail_id') ? (int)$this->input('booking_detail_id') : null;
        $itemId = (int)$this->input('item_id');
        $quantity = (int)$this->input('quantity', 1);
        $userId = $_SESSION['user']['id'] ?? 1;

        // 1. Report it first
        $notes = "Thay thế ngay lập tức từ bảng điều khiển buồng phòng.";
        $damageId = $this->damageService->reportDamage($bookingDetailId, $itemId, $quantity, $userId, $notes);

        if ($damageId) {
            // 2. Perform replacement from stock
            $result = $this->damageService->replaceFromStock($damageId, $userId);
            
            if ($result['success']) {
                $this->redirect(\App\Core\url('/admin/housekeeping/manage/' . $roomId . '?success=replaced'));
            } else {
                $this->redirect(\App\Core\url('/admin/housekeeping/manage/' . $roomId . '?error=' . urlencode($result['message'])));
            }
        } else {
            $this->redirect(\App\Core\url('/admin/housekeeping/manage/' . $roomId . '?error=report_failed'));
        }
    }

    public function updateRoomStatus(int $id): void
    {
        $status = $this->input('status');
        if ($this->roomModel->update($id, ['status' => $status])) {
            if ($this->input('ajax')) {
                echo json_encode(['success' => true]);
                return;
            }
            $this->redirect(\App\Core\url('/admin/housekeeping/manage/' . $id . '?success=status_updated'));
        }
    }

    public function updateItemStatus(): void
    {
        $roomInventoryId = (int)$this->input('room_inventory_id');
        $status = $this->input('status'); // Available, Lost, Damaged
        $roomId = (int)$this->input('room_id');
        $userId = Auth::user()['id'] ?? 1;

        $pdo = \App\Core\Database::getInstance()->getConnection();
        
        // 1. Get current item details
        $sql = "SELECT ri.*, ii.name as item_name, ii.default_price FROM room_inventory ri JOIN inventory_items ii ON ii.id = ri.item_id WHERE ri.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$roomInventoryId]);
        $ritem = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$ritem) {
            $this->redirect(\App\Core\url('/admin/housekeeping/manage/' . $roomId . '?error=item_not_found'));
            return;
        }

        // 2. Update status in room_inventory
        $updSql = "UPDATE room_inventory SET status = ? WHERE id = ?";
        $pdo->prepare($updSql)->execute([$status, $roomInventoryId]);

        // 3. If Lost or Damaged, and it wasn't already that status, trigger reporting
        if (($status === 'Lost' || $status === 'Damaged') && $ritem['status'] === 'Available') {
            // Find active booking to bill
            $bookingSql = "SELECT bd.id FROM booking_details bd JOIN bookings b ON b.id = bd.booking_id WHERE bd.room_id = ? AND b.booking_status IN ('checked_in', 'confirmed') ORDER BY bd.id DESC LIMIT 1";
            $stmtBooking = $pdo->prepare($bookingSql);
            $stmtBooking->execute([$roomId]);
            $bookingDetailId = $stmtBooking->fetchColumn();

            if ($bookingDetailId) {
                $notes = "Tự động ghi nhận khi nhân viên dọn phòng báo vật tư ở trạng thái: " . $status;
                $this->damageService->reportDamage((int)$bookingDetailId, (int)$ritem['item_id'], (int)$ritem['quantity'], $userId, $notes);
            }
        }

        $this->redirect(\App\Core\url('/admin/housekeeping/manage/' . $roomId . '?success=item_status_updated'));
    }
}

