<?php

require_once __DIR__ . '/App/Core/Model.php';
require_once __DIR__ . '/App/Models/RoomType.php';

// Simple setup for the script
define('BASE_PATH', __DIR__);
spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) require $file;
});

$pdo = new PDO('mysql:host=localhost;dbname=hotel', 'root', '');
$roomTypeModel = new \App\Models\RoomType();

// Get some inventory items
$stmt = $pdo->query("SELECT id FROM inventory_items LIMIT 10");
$itemIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

$roomTypes = $pdo->query("SELECT id, name FROM room_types")->fetchAll(PDO::FETCH_ASSOC);

foreach ($roomTypes as $rt) {
    // Update policies
    $pdo->prepare("UPDATE room_types SET 
        check_in_time = '14:00 - 23:00',
        check_out_time = '12:00',
        policy_check_in = 'Quý khách có thể yêu cầu nhận phòng sớm (tùy tình trạng phòng). Phí nhận phòng trước 09:00 là 100% giá phòng; từ 09:00 - 12:00 là 50% giá phòng.',
        policy_children = 'Trẻ em dưới 6 tuổi: Miễn phí nếu dùng chung giường với cha mẹ. Trẻ em từ 6-11 tuổi: Phụ thu ăn sáng 250.000 VNĐ. Trẻ em từ 12 tuổi trở lên: Tính như người lớn.',
        policy_cancel = 'Hủy phòng trước 72 giờ: Miễn phí. Hủy phòng trong vòng 48 giờ: Phạt 50% tổng giá trị đặt phòng. Hủy phòng trong vòng 24 giờ hoặc không đến: Phạt 100%.'
        WHERE id = ?
    ")->execute([$rt['id']]);

    // Link inventory (random 3-5 items)
    $stmtDel = $pdo->prepare("DELETE FROM room_type_inventory WHERE room_type_id = ?");
    $stmtDel->execute([$rt['id']]);

    $count = min(count($itemIds), rand(3, 5));
    if ($count > 0) {
        $selected = $count > 1 ? (array)array_rand(array_flip($itemIds), $count) : [$itemIds[0]];
        foreach ($selected as $itemId) {
            $pdo->prepare("INSERT INTO room_type_inventory (room_type_id, inventory_item_id, quantity) VALUES (?, ?, ?)")
                ->execute([$rt['id'], $itemId, 1]);
        }
    }
}

echo "Successfully seeded policies and inventory for " . count($roomTypes) . " room types.\n";

