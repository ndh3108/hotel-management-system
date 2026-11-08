<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use function App\Core\url;
use App\Core\Session;

class ArticleController extends Controller
{
    private Article $articleModel;
    private ArticleCategory $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
        $this->articleModel = new Article();
        $this->categoryModel = new ArticleCategory();
    }

    public function index(): void
    {
        $cursor = (int) $this->input('cursor', 0);
        $pageNumber = max(1, (int) $this->input('page', 1));
        
        $page = $this->articleModel->getPaginatedWithAuthorAndCategory($cursor, 10);
        
        $this->useLayout = true;
        $this->render('Admin/articles/index', [
            'title' => 'Quản lý Bài viết - Admin',
            'pageTitle' => 'Quản Lý Bài Viết',
            'currentPage' => 'articles',
            'articles' => $page['items'],
            'hasMore' => $page['has_more'],
            'nextCursor' => $page['next_last_id'],
            'pageNumber' => $pageNumber,
        ]);
    }

    public function create(): void
    {
        $categories = $this->categoryModel->getAll();
        $this->useLayout = true;
        $this->render('Admin/articles/create', [
            'title' => 'Thêm bài viết mới',
            'pageTitle' => 'Thêm Bài Viết Mới',
            'currentPage' => 'articles',
            'categories' => $categories
        ]);
    }

    public function store(): void
    {
        $title = trim($this->input('title'));
        $content = trim($this->input('content'));
        
        if (empty($title) || empty($content)) {
            $this->redirect(url('/admin/articles/create?error=missing_data'));
            return;
        }

        if (session_status() === PHP_SESSION_NONE) session_start();
        $authorId = $_SESSION['user']['id'] ?? 1;

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        $this->articleModel->create([
            'category_id' => $this->input('category_id') ?: null,
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => trim($this->input('excerpt')),
            'status' => $this->input('status') ?: 'Draft',
            'author_id' => $authorId
        ]);

        $this->redirect(url('/admin/articles?success=created'));
    }

    public function edit(int $id): void
    {
        $article = $this->articleModel->findById($id);
        if (!$article) {
            $this->redirect(url('/admin/articles'));
            return;
        }

        $categories = $this->categoryModel->getAll();
        $this->useLayout = true;
        $this->render('Admin/articles/edit', [
            'title' => 'Sửa bài viết',
            'pageTitle' => 'Chỉnh Sửa Bài Viết',
            'currentPage' => 'articles',
            'article' => $article,
            'categories' => $categories
        ]);
    }

    public function update(int $id): void
    {
        $title = trim($this->input('title'));
        if (empty($title)) {
            $this->redirect(url("/admin/articles/{$id}/edit?error=missing_data"));
            return;
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        $this->articleModel->update($id, [
            'category_id' => $this->input('category_id') ?: null,
            'title' => $title,
            'slug' => $slug,
            'content' => trim($this->input('content')),
            'excerpt' => trim($this->input('excerpt')),
            'status' => $this->input('status') ?: 'Draft',
        ]);

        $this->redirect(url('/admin/articles?success=updated'));
    }

    public function destroy(int $id): void
    {
        $this->articleModel->delete($id);
        $this->redirect(url('/admin/articles?success=deleted'));
    }
}

