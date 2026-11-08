<?php
$f1 = "App/Views/Services/success.php";
$c1 = file_get_contents($f1);
$c1 = str_replace(["&lt;?php", "?&gt;"], ["<?php", "?>"], $c1);
$r = [
    "Đặt Phòng" => "Đặt Dịch Vụ",
    "đặt phòng" => "đặt dịch vụ",
    "Nhận phòng" => "Bắt đầu",
    "Trả phòng" => "Kết thúc",
    "Loại phòng" => "Dịch vụ",
    "Số phòng" => "Số lượng",
    "Số đêm" => "Thời lượng",
    "/payment/vnpay" => "/services/payment/vnpay",
    "/payment/pay-later" => "/services/payment/pay-later",
    "/bill?id=" => "/services/booking/bill?id=",
    "['room_type']" => "['service_name']",
    "['room_type_name']" => "['service_name']",
    "['room_number']" => "['quantity']",
    '$nights = ceil((strtotime($booking[\'check_out\']) - strtotime($booking[\'check_in\'])) / (60 * 60 * 24));' => 
    '$nights = max(1, ceil((strtotime($booking["check_out"]) - strtotime($booking["check_in"])) / 3600));',
    "đêm" => "giờ"
];
foreach($r as $k => $v) $c1 = str_replace($k, $v, $c1);
file_put_contents($f1, $c1);

$f2 = "App/Views/Services/bill.php";
$c2 = file_get_contents($f2);
$r2 = [
    "Hóa Đơn Đặt Phòng" => "Hóa Đơn Đặt Dịch Vụ",
    "Mã Đặt Phòng" => "Mã Dịch Vụ",
    "đặt phòng" => "đặt dịch vụ",
    "Nhận phòng" => "Bắt đầu",
    "Trả phòng" => "Kết thúc",
    "Số đêm" => "Thời lượng",
    "Loại phòng" => "Tên dịch vụ",
    "Số phòng" => "Số lượng",
    "['room_type']" => "['service_name']",
    "['room_type_name']" => "['service_name']",
    "['room_description']" => "['service_name']",
    "['room_number']" => "['quantity']",
    "Giá phòng" => "Giá dịch vụ",
    "['price_per_night']" => "['price_per_hour']",
    '$nights = ceil((strtotime($booking[\'check_out\']) - strtotime($booking[\'check_in\'])) / (60 * 60 * 24));' => 
    '$nights = max(1, ceil((strtotime($booking["check_out"]) - strtotime($booking["check_in"])) / 3600));',
    "đêm" => "giờ"
];
foreach($r2 as $k => $v) $c2 = str_replace($k, $v, $c2);
file_put_contents($f2, $c2);

