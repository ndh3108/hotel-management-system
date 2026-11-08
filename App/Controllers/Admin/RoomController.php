<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\RoomService;

class RoomController extends Controller
{
    private Room $roomModel;
    private RoomType $roomTypeModel;
    private RoomService $roomService;
    private \App\Services\EntityImageService $entityImageService;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->roomModel = new Room();
        $this->roomTypeModel = new RoomType();
        $this->roomService = new RoomService();
        $this->entityImageService = new \App\Services\EntityImageService();
    }

    public function index(): void
    {
        $pageNumber = max(1, (int) $this->input('page', 1));
        $filters = [
            'room_type_id' => $this->input('room_type_id', ''),
            'status' => $this->input('status', ''),
            'check_in' => $this->input('check_in', ''),
            'check_out' => $this->input('check_out', ''),
            'min_price' => $this->input('min_price', ''),
            'max_price' => $this->input('max_price', ''),
        ];
        $page = $this->roomService->searchRooms($filters, $pageNumber, 15);

        $this->useLayout = true;
        $this->render('Admin/room/index_v2', [
            'title' => 'Quản lý phòng - Admin',
            'pageTitle' => 'Quản Lý Phòng',
            'currentPage' => 'room',
            'rooms' => $page['items'],
            'roomTypes' => $this->roomService->getRoomTypeOptions(),
            'filters' => $filters,
            'popularRoomTypes' => $this->roomService->getPopularRoomTypes(),
            'pageNumber' => $page['current_page'],
            'totalPages' => $page['total_pages'],
        ]);
    }

    public function grid(): void
    {
        $this->useLayout = true;
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userRole = $_SESSION['user']['role_name'] ?? $_SESSION['role_name'] ?? \App\Domain\Services\RoomStateService::ROLE_MANAGER;

        $this->render('Admin/room/grid', [
            'title' => 'Room Grid - Admin',
            'pageTitle' => 'Room Status Dashboard',
            'currentPage' => 'room_grid',
            'userRole' => $userRole
        ]);
    }

    public function create(): void
    {
        $roomTypes = $this->roomTypeModel->getAll();
        $featureModel = new \App\Models\RoomFeature();
        $allFeatures = $featureModel->getAllActive();

        $this->useLayout = true;
        $this->render('Admin/room/form_v2', [
            'title' => 'Thêm phòng - Admin',
            'pageTitle' => 'Thêm Phòng Mới',
            'currentPage' => 'room',
            'roomTypes' => $roomTypes,
            'allFeatures' => $allFeatures,
            'room' => null,
            'statusOptions' => Room::statusOptions(),
        ]);
    }

    public function store(): void
    {
        try {
            $roomNumber = trim((string)$this->input('room_number'));
            $roomTypeId = (int)$this->input('room_type_id');

            if (empty($roomNumber)) {
                $_SESSION['error'] = 'Số phòng không được để trống.';
                $this->redirect(\App\Core\url('/admin/room/create'));
                return;
            }

            if (!$roomTypeId) {
                $_SESSION['error'] = 'Vui lòng chọn loại phòng.';
                $this->redirect(\App\Core\url('/admin/room/create'));
                return;
            }

            $data = [
                'room_type_id' => $roomTypeId,
                'room_number' => $roomNumber,
                'floor' => $this->input('floor') !== '' ? (int)$this->input('floor') : null,
                'location' => trim((string)$this->input('location')),
                'status' => Room::normalizeStatus((string)($this->input('status') ?? Room::STATUS_AVAILABLE)),
                'notes' => trim((string)$this->input('notes', '')),
            ];

            $id = $this->roomModel->create($data);
            if ($id) {
                // Sync features
                $featureIds = $this->input('features') ?? [];
                $featureModel = new \App\Models\RoomFeature();
                $featureModel->syncForRoom((int)$id, $featureIds);

                // Handle images
                if (!empty($_FILES['images']['tmp_name'][0])) {
                    $uploadResult = $this->entityImageService->uploadMany('room', (int)$id, $_FILES['images']);
                    if (!$uploadResult['success']) {
                        $_SESSION['warning'] = 'Phòng đã tạo nhưng một số ảnh lỗi: ' . ($uploadResult['message'] ?? 'Lỗi không xác định');
                    }
                }
                $_SESSION['success'] = 'Thêm phòng #' . $roomNumber . ' thành công!';
                $this->redirect(\App\Core\url('/admin/room'));
            } else {
                $_SESSION['error'] = 'Thêm phòng thất bại. Có thể số phòng đã tồn tại.';
                $this->redirect(\App\Core\url('/admin/room/create'));
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
            $this->redirect(\App\Core\url('/admin/room/create'));
        }
    }

    public function edit(int $id): void
    {
        $room = $this->roomModel->findById($id);
        if ($room) {
            $room['images'] = $this->entityImageService->getImages('room', $id);
            $room['feature_ids'] = array_column((new \App\Models\RoomFeature())->getForRoom($id), 'id');
            $room['maintenance_history'] = (new \App\Models\RoomMaintenance())->getByRoom($id);
        }
        
        $roomTypes = $this->roomTypeModel->getAll();
        $featureModel = new \App\Models\RoomFeature();
        $allFeatures = $featureModel->getAllActive();

        $this->useLayout = true;
        $this->render('Admin/room/form_v2', [
            'title' => 'Sửa phòng - Admin',
            'pageTitle' => 'Chỉnh Sửa Phòng',
            'currentPage' => 'room',
            'roomTypes' => $roomTypes,
            'allFeatures' => $allFeatures,
            'room' => $room,
            'statusOptions' => Room::statusOptions(),
        ]);
    }

    public function update(int $id): void
    {
        try {
            $room = $this->roomModel->findById($id);
            if (!$room) {
                $_SESSION['error'] = 'Phòng không tồn tại.';
                $this->redirect(\App\Core\url('/admin/room'));
                return;
            }

            $roomNumber = trim((string)$this->input('room_number'));
            $roomTypeId = (int)$this->input('room_type_id');

            if (empty($roomNumber)) {
                $_SESSION['error'] = 'Số phòng không được để trống.';
                $this->redirect(\App\Core\url('/admin/room/' . $id . '/edit'));
                return;
            }

            $data = [
                'room_type_id' => $roomTypeId,
                'room_number' => $roomNumber,
                'floor' => $this->input('floor') !== '' ? (int)$this->input('floor') : null,
                'location' => trim((string)$this->input('location')),
                'status' => Room::normalizeStatus((string)($this->input('status') ?? Room::STATUS_AVAILABLE)),
                'notes' => trim((string)$this->input('notes', '')),
            ];

            if ($this->roomModel->update($id, $data)) {
                // Sync features
                $featureIds = $this->input('features') ?? [];
                $featureModel = new \App\Models\RoomFeature();
                $featureModel->syncForRoom($id, $featureIds);

                // Handle images
                if (!empty($_FILES['images']['tmp_name'][0])) {
                    $uploadResult = $this->entityImageService->uploadMany('room', $id, $_FILES['images']);
                    if (!$uploadResult['success']) {
                        $_SESSION['warning'] = 'Dữ liệu đã lưu nhưng một số ảnh lỗi: ' . ($uploadResult['message'] ?? 'Lỗi không xác định');
                    }
                }
                $_SESSION['success'] = 'Cập nhật phòng #' . $roomNumber . ' thành công!';
                $this->redirect(\App\Core\url('/admin/room'));
            } else {
                $_SESSION['error'] = 'Cập nhật thất bại. Vui lòng kiểm tra lại dữ liệu.';
                $this->redirect(\App\Core\url('/admin/room/' . $id . '/edit'));
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
            $this->redirect(\App\Core\url('/admin/room/' . $id . '/edit'));
        }
    }

    public function deleteImage(int $id, int $imageId): void
    {
        $this->entityImageService->deleteImage($imageId, 'room', $id);
        $this->redirect(\App\Core\url('/admin/room/' . $id . '/edit'));
    }

    public function destroy(int $id): void
    {
        // Kiểm tra xem phòng có đang được dùng trong booking nào không
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM booking_details WHERE room_id = ?");
        $stmt->execute([$id]);
        $hasBookings = (int)$stmt->fetchColumn() > 0;

        if ($hasBookings) {
            $_SESSION['error'] = 'Không thể xóa phòng này vì đang tồn tại lịch sử đặt phòng liên quan. Vui lòng chuyển trạng thái phòng sang "Maintenance" thay vì xóa.';
            $this->redirect(\App\Core\url('/admin/room'));
            return;
        }

        $this->entityImageService->deleteAllForEntity('room', $id);
        $this->roomModel->delete($id);
        $this->redirect(\App\Core\url('/admin/room'));
    }
}

