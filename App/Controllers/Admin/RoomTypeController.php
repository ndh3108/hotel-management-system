<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\RoomType;
use PDO;

class RoomTypeController extends Controller
{
    private RoomType $roomTypeModel;
    private \App\Models\RoomImage $roomImageModel;
    private \App\Services\EntityImageService $entityImageService;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->roomTypeModel = new RoomType();
        $this->roomImageModel = new \App\Models\RoomImage();
        $this->entityImageService = new \App\Services\EntityImageService();
    }

    public function index(): void
    {
        $cursor = (int) $this->input('cursor', 0);
        $pageNumber = max(1, (int) $this->input('page', 1));
        
        $page = $this->roomTypeModel->getPaginatedWithImages($cursor, 10);
        
        $this->useLayout = true;
        $this->render('Admin/room-type/index_v2', [
            'title' => 'Quản lý loại phòng - Admin',
            'pageTitle' => 'Quản Lý Loại Phòng',
            'currentPage' => 'room-type',
            'roomTypes' => $page['items'],
            'hasMore' => $page['has_more'],
            'nextCursor' => $page['next_last_id'],
            'pageNumber' => $pageNumber,
        ]);
    }

    public function create(): void
    {
        $stmtAmenities = $this->roomTypeModel->getConnection()->query("SELECT * FROM amenities");
        $amenities = $stmtAmenities->fetchAll(PDO::FETCH_ASSOC);

        $stmtItems = $this->roomTypeModel->getConnection()->query("SELECT id, name FROM inventory_items ORDER BY name ASC");
        $allInventory = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        $this->useLayout = true;
        $this->render('Admin/room-type/form_v2', [
            'title' => 'Thêm loại phòng - Admin',
            'pageTitle' => 'Thêm Loại Phòng Mới',
            'currentPage' => 'room-type',
            'roomType' => null,
            'amenities' => $amenities,
            'selectedAmenities' => [],
            'allInventory' => $allInventory,
            'selectedSupplies' => [],
        ]);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    /**
     * Tạo slug từ chuỗi tên: chuyển về lowercase, bỏ dấu tiếng Việt,
     * thay khoảng trắng bằng dấu gạch ngang, loại ký tự đặc biệt.
     */
    private function generateSlug(string $name): string
    {
        $slug = mb_strtolower(trim($name), 'UTF-8');

        // Bảng chuyển đổi ký tự tiếng Việt có dấu → không dấu
        $map = [
            'à'=>'a','á'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a',
            'ă'=>'a','ằ'=>'a','ắ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a',
            'â'=>'a','ầ'=>'a','ấ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
            'đ'=>'d',
            'è'=>'e','é'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e',
            'ê'=>'e','ề'=>'e','ế'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
            'ì'=>'i','í'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
            'ò'=>'o','ó'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o',
            'ô'=>'o','ồ'=>'o','ố'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o',
            'ơ'=>'o','ờ'=>'o','ớ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
            'ù'=>'u','ú'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u',
            'ư'=>'u','ừ'=>'u','ứ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
            'ỳ'=>'y','ý'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
        ];
        $slug = strtr($slug, $map);

        // Chỉ giữ chữ cái latin, số và dấu gạch ngang
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', trim($slug, '-'));

        return $slug ?: 'room-type-' . time();
    }

    /**
     * Đảm bảo slug hợp lệ: dùng giá trị người dùng nhập nếu có,
     * nếu không thì tự tạo từ name.
     */
    private function resolveSlug(string $rawSlug, string $name): string
    {
        $slug = trim($rawSlug);
        // Chuẩn hoá slug người dùng nhập (lowercase, chỉ a-z0-9-)
        if ($slug !== '') {
            $slug = strtolower($slug);
            $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
            $slug = preg_replace('/-+/', '-', trim($slug, '-'));
        }
        return $slug !== '' ? $slug : $this->generateSlug($name);
    }

    // ---------------------------------------------------------------
    // CRUD Actions
    // ---------------------------------------------------------------

    public function store(): void
    {
        try {
            $name = trim((string) $this->input('name'));
            if (empty($name)) {
                $_SESSION['error'] = 'Tên loại phòng không được để trống.';
                $this->redirect(\App\Core\url('/admin/room-type/create'));
                return;
            }

            $data = [
                'name' => $name,
                'slug' => $this->resolveSlug((string) $this->input('slug'), $name),
                'description' => trim((string) $this->input('description')),
                'capacity_adults' => (int) $this->input('capacity_adults', 1),
                'capacity_children' => (int) $this->input('capacity_children', 0),
                'base_price' => (float) $this->input('base_price', 0),
                'area_sqm' => (float) $this->input('area_sqm', 0),
                'max_occupancy' => (int) $this->input('max_occupancy', 0),
                'bed_type' => trim((string) $this->input('bed_type', '')),
                'room_size' => trim((string) $this->input('room_size', '')),
                'amenities_default' => json_encode(array_values(array_map('intval', $_POST['amenities'] ?? [])), JSON_UNESCAPED_UNICODE),
                'refund_policy' => trim((string) $this->input('refund_policy', '')),
                'min_stay' => max(1, (int) $this->input('min_stay', 1)),
                'max_stay' => max(1, (int) $this->input('max_stay', 30)),
                'seasonal_pricing_rule' => trim((string) $this->input('seasonal_pricing_rule', '')),
                'check_in_time' => trim((string) $this->input('check_in_time', '14:00')),
                'check_out_time' => trim((string) $this->input('check_out_time', '12:00')),
                'policy_check_in' => trim((string) $this->input('policy_check_in')),
                'policy_children' => trim((string) $this->input('policy_children')),
                'policy_cancel' => trim((string) $this->input('policy_cancel')),
                'is_active' => $this->input('is_active') ? 1 : 0,
            ];
            $id = $this->roomTypeModel->create($data);

            if ($id) {
                // Save Supplies (N-N)
                $supplyIds = $_POST['supplies'] ?? [];
                if (is_array($supplyIds)) {
                    $supplyData = [];
                    foreach ($supplyIds as $sid) {
                        $supplyData[] = ['inventory_item_id' => $sid, 'quantity' => 1];
                    }
                    $this->roomTypeModel->syncSupplies((int)$id, $supplyData);
                }

                // Save Amenities (N-N)
                $amenityIds = $_POST['amenities'] ?? [];
                $this->roomTypeModel->syncAmenities((int) $id, is_array($amenityIds) ? $amenityIds : []);

                // Handle Images
                if (!empty($_FILES['images']['tmp_name'][0])) {
                    $uploadResult = $this->entityImageService->uploadMany('room_type', (int) $id, $_FILES['images']);
                    if (!$uploadResult['success']) {
                        $_SESSION['warning'] = 'Dữ liệu đã tạo nhưng một số ảnh lỗi: ' . ($uploadResult['message'] ?? 'Lỗi không xác định');
                    }
                }

                $this->entityImageService->syncYoutubeVideo('room_type', (int) $id, (string) $this->input('youtube_url', ''));
                $_SESSION['success'] = 'Thêm loại phòng "' . $name . '" thành công!';
                $this->redirect(\App\Core\url('/admin/room-type'));
            } else {
                $_SESSION['error'] = 'Thêm loại phòng thất bại. Vui lòng thử lại.';
                $this->redirect(\App\Core\url('/admin/room-type/create'));
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
            $this->redirect(\App\Core\url('/admin/room-type/create'));
        }
    }

    public function edit(int $id): void
    {
        $roomType = $this->roomTypeModel->getFullDetail($id);

        // Fetch all amenities for the checklist
        $stmtAmenities = $this->roomTypeModel->getConnection()->query("SELECT * FROM amenities");
        $allAmenities = $stmtAmenities->fetchAll(PDO::FETCH_ASSOC);

        // Map existing selected amenity IDs
        $selectedAmenities = $this->roomTypeModel->getSelectedAmenityIds($id);

        $stmtItems = $this->roomTypeModel->getConnection()->query("SELECT id, name FROM inventory_items ORDER BY name ASC");
        $allInventory = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        
        $supplies = $this->roomTypeModel->getSupplies($id);
        $selectedSupplies = array_column($supplies, 'id');

        $this->useLayout = true;
        $this->render('Admin/room-type/form_v2', [
            'title' => 'Sửa loại phòng - Admin',
            'pageTitle' => 'Chỉnh Sửa Loại Phòng',
            'currentPage' => 'room-type',
            'roomType' => $roomType,
            'amenities' => $allAmenities,
            'selectedAmenities' => $selectedAmenities,
            'allInventory' => $allInventory,
            'selectedSupplies' => $selectedSupplies,
        ]);
    }

    public function update(int $id): void
    {
        try {
            $roomType = $this->roomTypeModel->findById($id);
            if (!$roomType) {
                $_SESSION['error'] = 'Loại phòng không tồn tại.';
                $this->redirect(\App\Core\url('/admin/room-type'));
                return;
            }

            $name = trim((string) $this->input('name'));
            if (empty($name)) {
                $_SESSION['error'] = 'Tên loại phòng không được để trống.';
                $this->redirect(\App\Core\url('/admin/room-type/' . $id . '/edit'));
                return;
            }

            $data = [
                'name' => $name,
                'slug' => $this->resolveSlug((string) $this->input('slug'), $name),
                'description' => trim((string) $this->input('description')),
                'capacity_adults' => (int) $this->input('capacity_adults', 1),
                'capacity_children' => (int) $this->input('capacity_children', 0),
                'base_price' => (float) $this->input('base_price', 0),
                'area_sqm' => (float) $this->input('area_sqm', 0),
                'max_occupancy' => (int) $this->input('max_occupancy', 0),
                'bed_type' => trim((string) $this->input('bed_type', '')),
                'room_size' => trim((string) $this->input('room_size', '')),
                'amenities_default' => json_encode(array_values(array_map('intval', $_POST['amenities'] ?? [])), JSON_UNESCAPED_UNICODE),
                'refund_policy' => trim((string) $this->input('refund_policy', '')),
                'min_stay' => max(1, (int) $this->input('min_stay', 1)),
                'max_stay' => max(1, (int) $this->input('max_stay', 30)),
                'seasonal_pricing_rule' => trim((string) $this->input('seasonal_pricing_rule', '')),
                'check_in_time' => trim((string) $this->input('check_in_time', '14:00')),
                'check_out_time' => trim((string) $this->input('check_out_time', '12:00')),
                'policy_check_in' => trim((string) $this->input('policy_check_in')),
                'policy_children' => trim((string) $this->input('policy_children')),
                'policy_cancel' => trim((string) $this->input('policy_cancel')),
                'is_active' => $this->input('is_active') ? 1 : 0,
            ];
            
            if ($this->roomTypeModel->update($id, $data)) {
                // Sync Supplies (N-N)
                $supplyIds = $_POST['supplies'] ?? [];
                if (is_array($supplyIds)) {
                    $supplyData = [];
                    foreach ($supplyIds as $sid) {
                        $supplyData[] = ['inventory_item_id' => $sid, 'quantity' => 1];
                    }
                    $this->roomTypeModel->syncSupplies($id, $supplyData);
                }

                // Sync Amenities (N-N)
                $amenityIds = $_POST['amenities'] ?? [];
                $this->roomTypeModel->syncAmenities($id, is_array($amenityIds) ? $amenityIds : []);

                // Handle Images
                if (!empty($_FILES['images']['tmp_name'][0])) {
                    $uploadResult = $this->entityImageService->uploadMany('room_type', $id, $_FILES['images']);
                    if (!$uploadResult['success']) {
                        $_SESSION['warning'] = 'Cập nhật thành công nhưng một số ảnh lỗi: ' . ($uploadResult['message'] ?? 'Lỗi không xác định');
                    }
                }

                $this->entityImageService->syncYoutubeVideo('room_type', $id, (string) $this->input('youtube_url', ''));

                $_SESSION['success'] = 'Cập nhật loại phòng thành công!';
                $this->redirect(\App\Core\url('/admin/room-type'));
            } else {
                $_SESSION['error'] = 'Cập nhật thất bại. Vui lòng kiểm tra lại dữ liệu.';
                $this->redirect(\App\Core\url('/admin/room-type/' . $id . '/edit'));
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
            $this->redirect(\App\Core\url('/admin/room-type/' . $id . '/edit'));
        }
    }

    public function deleteImage(int $id, int $imageId): void
    {
        $source = $_GET['source'] ?? 'images';
        if ($source === 'room_images') {
            $pdo = $this->roomTypeModel->getConnection();
            $pdo->prepare("DELETE FROM room_images WHERE id = ?")->execute([$imageId]);
        } else {
            $this->entityImageService->deleteImage($imageId, 'room_type', $id);
        }
        $this->redirect(\App\Core\url('/admin/room-type/' . $id . '/edit'));
    }

    public function setPrimaryImage(int $id, int $imageId): void
    {
        $imageModel = new \App\Models\Image();
        if ($imageModel->setPrimary('room_type', $id, $imageId)) {
            $_SESSION['success'] = 'Đã đặt ảnh chính thành công!';
        } else {
            $_SESSION['error'] = 'Không thể đặt ảnh chính. Vui lòng thử lại.';
        }
        $this->redirect(\App\Core\url('/admin/room-type/' . $id . '/edit'));
    }

    public function destroy(int $id): void
    {
        $this->entityImageService->deleteAllForEntity('room_type', $id);
        $this->entityImageService->syncYoutubeVideo('room_type', $id, '');
        $this->roomTypeModel->delete($id);
        $this->redirect(\App\Core\url('/admin/room-type'));
    }
}

