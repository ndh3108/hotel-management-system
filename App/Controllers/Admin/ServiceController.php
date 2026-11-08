<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Service;
use App\Models\ServiceType;
use App\Services\EntityImageService;
use PDO;

class ServiceController extends Controller
{
    private Service $serviceModel;
    private ServiceType $serviceTypeModel;
    private EntityImageService $entityImageService;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        
        $this->serviceModel = new Service();
        $this->serviceTypeModel = new ServiceType();
        $this->entityImageService = new EntityImageService();
    }

    public function index(): void
    {
        $cursor = (int) $this->input('cursor', 0);
        $pageNumber = max(1, (int) $this->input('page', 1));
        
        $page = $this->serviceModel->getPaginatedWithTypeName($cursor, 10);
        
        $this->useLayout = true;
        $this->render('Admin/services/index', [
            'title' => 'Quản lý Dịch vụ - Admin',
            'pageTitle' => 'Quản Lý Dịch Vụ',
            'currentPage' => 'services',
            'services' => $page['items'],
            'hasMore' => $page['has_more'],
            'nextCursor' => $page['next_last_id'],
            'pageNumber' => $pageNumber,
        ]);
    }

    public function create(): void
    {
        $serviceTypes = $this->serviceTypeModel->getAll();

        $this->useLayout = true;
        $this->render('Admin/services/form', [
            'title' => 'Thêm Dịch vụ - Admin',
            'pageTitle' => 'Thêm Dịch Vụ Mới',
            'currentPage' => 'services',
            'service' => null,
            'serviceTypes' => $serviceTypes,
        ]);
    }

    public function store(): void
    {
        try {
            $name = trim((string) $this->input('name'));
            $price = (float) $this->input('price_per_hour', 0);
            $capacity = (int) $this->input('capacity', 1);

            if (empty($name)) {
                $_SESSION['error'] = 'Tên dịch vụ không được để trống.';
                $this->redirect(\App\Core\url('/admin/services/create'));
                return;
            }

            if ($price < 0 || $capacity < 1) {
                $_SESSION['error'] = 'Giá và sức chứa phải là số dương.';
                $this->redirect(\App\Core\url('/admin/services/create'));
                return;
            }

            $data = [
                'name' => $name,
                'service_type_id' => (int) $this->input('service_type_id') ?: null,
                'capacity' => $capacity,
                'price_per_hour' => $price,
                'description' => trim((string) $this->input('description')),
                'status' => $this->input('status', 'AVAILABLE'),
            ];
            
            $id = $this->serviceModel->create($data);

            if ($id && !empty($_FILES['images']['tmp_name'][0])) {
                $uploadResult = $this->entityImageService->uploadMany('service', (int) $id, $_FILES['images']);
                if (!$uploadResult['success']) {
                    $_SESSION['warning'] = 'Dịch vụ đã tạo nhưng một số ảnh gặp lỗi: ' . implode(', ', $uploadResult['errors']);
                }
            }

            $_SESSION['success'] = 'Thêm dịch vụ "' . $name . '" thành công.';
            $this->redirect(\App\Core\url('/admin/services'));
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
            $this->redirect(\App\Core\url('/admin/services/create'));
        }
    }

    public function edit(int $id): void
    {
        $service = $this->serviceModel->findById($id);
        if (!$service) {
            $_SESSION['error'] = 'Không tìm thấy dịch vụ.';
            $this->redirect(\App\Core\url('/admin/services'));
            return;
        }

        // Get images
        $service['images'] = $this->entityImageService->getImages('service', $id);
        
        $serviceTypes = $this->serviceTypeModel->getAll();

        $this->useLayout = true;
        $this->render('Admin/services/form', [
            'title' => 'Sửa Dịch vụ - Admin',
            'pageTitle' => 'Chỉnh Sửa Dịch Vụ',
            'currentPage' => 'services',
            'service' => $service,
            'serviceTypes' => $serviceTypes,
        ]);
    }

    public function update(int $id): void
    {
        try {
            $service = $this->serviceModel->findById($id);
            if (!$service) {
                $_SESSION['error'] = 'Dịch vụ không tồn tại.';
                $this->redirect(\App\Core\url('/admin/services'));
                return;
            }

            $name = trim((string) $this->input('name'));
            if (empty($name)) {
                $_SESSION['error'] = 'Tên không được để trống.';
                $this->redirect(\App\Core\url('/admin/services/' . $id . '/edit'));
                return;
            }

            $data = [
                'name' => $name,
                'service_type_id' => (int) $this->input('service_type_id') ?: null,
                'capacity' => (int) $this->input('capacity', 1),
                'price_per_hour' => (float) $this->input('price_per_hour', 0),
                'description' => trim((string) $this->input('description')),
                'status' => $this->input('status', 'AVAILABLE'),
            ];

            $this->serviceModel->update($id, $data);

            // Upload additional images
            if (!empty($_FILES['images']['tmp_name'][0])) {
                $uploadResult = $this->entityImageService->uploadMany('service', (int) $id, $_FILES['images']);
                if (!$uploadResult['success'] && !empty($uploadResult['errors'])) {
                    $_SESSION['warning'] = 'Thông tin đã cập nhật nhưng một số ảnh lỗi: ' . implode(', ', $uploadResult['errors']);
                }
            }

            $_SESSION['success'] = 'Cập nhật dịch vụ thành công.';
            $this->redirect(\App\Core\url('/admin/services'));
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Lỗi cập nhật: ' . $e->getMessage();
            $this->redirect(\App\Core\url('/admin/services/' . $id . '/edit'));
        }
    }

    public function destroy(int $id): void
    {
        try {
            // Xóa ảnh trên Cloudinary và trong DB images
            $this->entityImageService->deleteAllForEntity('service', $id);
            
            // Xóa dịch vụ
            $this->serviceModel->delete($id);
            
            $_SESSION['success'] = 'Xóa dịch vụ thành công.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        $this->redirect(\App\Core\url('/admin/services'));
    }

    public function deleteImage(int $id, int $imageId): void
    {
        $this->entityImageService->deleteImage($imageId, 'service', $id);
        $this->redirect(\App\Core\url('/admin/services/' . $id . '/edit'));
    }
}

