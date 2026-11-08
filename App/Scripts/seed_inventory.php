<?php
require_once __DIR__ . '/../../App/Core/Database.php';

use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

$items = [
    ['Chăn bông cao cấp', 'Bedding', 'Cái', 850000, 50],
    ['Gối lông vũ', 'Bedding', 'Cái', 350000, 100],
    ['Drap trải giường (King)', 'Bedding', 'Cái', 450000, 60],
    ['Vỏ gối cotton', 'Bedding', 'Cái', 80000, 200],
    ['Khăn tắm lớn', 'Towels', 'Cái', 120000, 150],
    ['Khăn tay', 'Towels', 'Cái', 45000, 300],
    ['Thảm chân nhà tắm', 'Towels', 'Cái', 65000, 100],
    ['Máy sấy tóc Panasonic', 'Electrical', 'Cái', 550000, 40],
    ['Ấm đun nước siêu tốc', 'Electrical', 'Cái', 400000, 40],
    ['Điều khiển điều hòa', 'Electrical', 'Cái', 150000, 45],
    ['Bàn chải đánh răng', 'Toiletries', 'Bộ', 15000, 500],
    ['Kem đánh răng (tuýp nhỏ)', 'Toiletries', 'Tuýp', 12000, 500],
    ['Xà phòng cục', 'Toiletries', 'Cái', 10000, 400],
    ['Dầu gội (chai 30ml)', 'Toiletries', 'Chai', 18000, 600],
    ['Sữa tắm (chai 30ml)', 'Toiletries', 'Chai', 18000, 600],
    ['Nước suối Aquafina 500ml', 'Mini-bar', 'Chai', 15000, 1000],
    ['Coca Cola lon', 'Mini-bar', 'Lon', 25000, 200],
    ['Pepsi lon', 'Mini-bar', 'Lon', 25000, 200],
    ['Snack khoai tây Lay\'s', 'Mini-bar', 'Gói', 30000, 150],
    ['Ly thủy tinh cao cấp', 'Dining', 'Cái', 45000, 120],
    ['Thìa inox', 'Dining', 'Cái', 15000, 100],
];

foreach ($items as $item) {
    list($name, $category, $unit, $price, $stock) = $item;
    
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM inventory_items WHERE name = ?");
    $stmt->execute([$name]);
    if (!$stmt->fetch()) {
        $sql = "INSERT INTO inventory_items (name, category, unit, default_price, stock_quantity, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $pdo->prepare($sql)->execute([$name, $category, $unit, $price, $stock]);
        echo "Inserted: $name\n";
    } else {
        echo "Skipped (Exists): $name\n";
    }
}

