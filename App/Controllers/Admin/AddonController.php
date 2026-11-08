<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Addon;

class AddonController extends Controller
{
    private Addon $addonModel;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->addonModel = new Addon();
    }

    public function index(): void
    {
        $services = $this->addonModel->getAll();
        $this->useLayout = true;
        $this->render('Admin/services/index', [
            'title' => 'Quản lý dịch vụ - Admin',
            'pageTitle' => 'Quản Lý Dịch Vụ',
            'currentPage' => 'services',
            'services' => $services,
        ]);
    }

    public function create(): void
    {
        $this->useLayout = true;
        $this->render('Admin/services/form', [
            'title' => 'Thêm dịch vụ - Admin',
            'pageTitle' => 'Thêm Dịch Vụ Mới',
            'currentPage' => 'services',
            'service' => null,
        ]);
    }

    public function store(): void
    {
        $data = [
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'price' => $this->input('price'),
            'unit' => $this->input('unit'),
        ];
        $this->addonModel->create($data);
        $this->redirect(\App\Core\url('/admin/addons'));
    }

    public function edit(int $id): void
    {
        $service = $this->addonModel->findById($id);
        $this->useLayout = true;
        $this->render('Admin/services/form', [
            'title' => 'Sửa dịch vụ - Admin',
            'pageTitle' => 'Chỉnh Sửa Dịch Vụ',
            'currentPage' => 'services',
            'service' => $service,
        ]);
    }

    public function update(int $id): void
    {
        $data = [
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'price' => $this->input('price'),
            'unit' => $this->input('unit'),
        ];
        $this->addonModel->update($id, $data);
        $this->redirect(\App\Core\url('/admin/addons'));
    }

    public function destroy(int $id): void
    {
        $this->addonModel->delete($id);
        $this->redirect(\App\Core\url('/admin/addons'));
    }
}

