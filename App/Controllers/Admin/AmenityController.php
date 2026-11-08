<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class AmenityController extends Controller
{
    private PDO $pdo;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index(): void
    {
        $pageNumber = max(1, (int) $this->input('page', 1));
        $perPage = 15;
        $offset = ($pageNumber - 1) * $perPage;
        $totalItems = (int) $this->pdo->query("SELECT COUNT(*) FROM amenities")->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT * FROM amenities ORDER BY id ASC LIMIT :limit_rows OFFSET :offset_rows");
        $stmt->bindValue(':limit_rows', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset_rows', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $this->useLayout = true;
        $this->render('Admin/amenities/index', [
            'title' => 'Quản lý tiện nghi - Admin',
            'pageTitle' => 'Quản Lý Tiện Nghi',
            'currentPage' => 'amenities',
            'amenities' => $rows,
            'pageNumber' => $pageNumber,
            'totalPages' => $totalPages,
        ]);
    }

    public function create(): void
    {
        $this->useLayout = true;
        $this->render('Admin/amenities/form', [
            'title' => 'Thêm tiện nghi - Admin',
            'pageTitle' => 'Thêm Tiện Nghi Mới',
            'currentPage' => 'amenities',
            'amenity' => null,
        ]);
    }

    public function store(): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO amenities (name, icon_url) VALUES (?, ?)");
        $stmt->execute([$this->input('name'), $this->input('icon_url')]);
        $this->redirect(\App\Core\url('/admin/amenities'));
    }

    public function edit(int $id): void
    {
        $stmt = $this->pdo->prepare("SELECT * FROM amenities WHERE id = ?");
        $stmt->execute([$id]);
        $amenity = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->useLayout = true;
        $this->render('Admin/amenities/form', [
            'title' => 'Sửa tiện nghi - Admin',
            'pageTitle' => 'Chỉnh Sửa Tiện Nghi',
            'currentPage' => 'amenities',
            'amenity' => $amenity,
        ]);
    }

    public function update(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE amenities SET name = ?, icon_url = ? WHERE id = ?");
        $stmt->execute([$this->input('name'), $this->input('icon_url'), $id]);
        $this->redirect(\App\Core\url('/admin/amenities'));
    }

    public function destroy(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM amenities WHERE id = ?");
        $stmt->execute([$id]);
        $this->redirect(\App\Core\url('/admin/amenities'));
    }
}

