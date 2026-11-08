<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class LoyaltyController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/admin-layout.php';
    }

    public function index(): void
    {
        $this->authorize('REPORT_VIEW');
        $pdo = Database::getInstance()->getConnection();
        $rows = $pdo->query("
            SELECT u.id AS user_id, u.full_name, u.email, u.points,
                   lm.tier, lm.total_points, lm.current_points,
                   m.discount_percent
            FROM users u
            LEFT JOIN loyalty_memberships lm ON lm.user_id = u.id
            LEFT JOIN memberships m ON m.tier_name = lm.tier
            ORDER BY COALESCE(lm.total_points, u.points) DESC, u.id DESC
            LIMIT 300
        ")->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $this->useLayout = true;
        $this->render('Admin/loyalty/index', [
            'title' => 'Loyalty management - Admin',
            'pageTitle' => 'Loyalty',
            'currentPage' => 'loyalty',
            'rows' => $rows,
        ]);
    }
}

