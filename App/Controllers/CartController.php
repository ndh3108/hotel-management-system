<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\AuthMiddleware;
use App\Services\UserService;

class CartController extends Controller
{
    private UserService $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
        // Khởi tạo session cart nếu chưa có
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    // Hiển thị giỏ hàng
    public function index()
    {
        AuthMiddleware::requireAuth();
        
        $this->render('Cart/index', [
            'cartItems' => $_SESSION['cart']
        ]);
    }
    
    // Thêm phòng vào giỏ hàng
    public function add()
    {
        if (!Auth::check()) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'error' => 'Vui lòng đăng nhập']);
                exit;
            }
            $this->redirect('/login');
            return;
        }
        AuthMiddleware::requireAuth();
        
        $isAjax = $this->isAjax();
        
        $roomId = $_POST['room_id'] ?? $_GET['room_id'] ?? 0;
        $checkIn = $_POST['check_in'] ?? '';
        $checkOut = $_POST['check_out'] ?? '';
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if (!$roomId || !$checkIn || !$checkOut) {
            if ($isAjax) {
                echo json_encode(['success' => false, 'error' => 'Thiếu thông tin đặt phòng']);
                exit;
            }
            $this->redirect('/rooms');
            return;
        }
        
        // Lấy thông tin phòng từ database
        $roomModel = new \App\Models\Room();
        $roomTypeModel = new \App\Models\RoomType();
        
        $room = $roomModel->findById($roomId);
        if (!$room) {
            if ($isAjax) {
                echo json_encode(['success' => false, 'error' => 'Không tìm thấy phòng']);
                exit;
            }
            $this->redirect('/rooms');
            return;
        }
        
        $roomType = $roomTypeModel->findById($room['room_type_id']);
        
        $nights = ceil((strtotime($checkOut) - strtotime($checkIn)) / (60 * 60 * 24));
        $pricePerNight = $roomType['base_price'] ?? 0;
        $totalPrice = $pricePerNight * $nights * $quantity;
        
        // Kiểm tra phòng đã tồn tại trong giỏ chưa
        $existingIndex = -1;
        foreach ($_SESSION['cart'] as $index => $item) {
            if ($item['room_id'] == $roomId && 
                $item['check_in'] == $checkIn && 
                $item['check_out'] == $checkOut) {
                $existingIndex = $index;
                break;
            }
        }
        
        if ($existingIndex >= 0) {
            // Tăng số lượng
            $_SESSION['cart'][$existingIndex]['quantity'] += $quantity;
            $_SESSION['cart'][$existingIndex]['total_price'] = 
                $_SESSION['cart'][$existingIndex]['price_per_night'] * 
                $_SESSION['cart'][$existingIndex]['nights'] * 
                $_SESSION['cart'][$existingIndex]['quantity'];
        } else {
            // Thêm mới
            $_SESSION['cart'][] = [
                'id' => uniqid(),
                'room_id' => $roomId,
                'room_number' => $room['room_number'],
                'room_name' => $roomType['name'],
                'image' => $roomType['image_url'] ?? null,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'nights' => $nights,
                'price_per_night' => $pricePerNight,
                'quantity' => $quantity,
                'total_price' => $totalPrice
            ];
        }

        $this->userService->logCustomActivity((int) Auth::id(), 'CART_UPDATED', 'Thêm phòng vào giỏ hàng');
        
        if ($isAjax) {
            echo json_encode([
                'success' => true,
                'cart_count' => count($_SESSION['cart']),
                'message' => 'Đã thêm phòng vào giỏ hàng'
            ]);
            exit;
        }
        
        $this->redirect('/cart');
    }
    
    // Cập nhật số lượng
    public function update()
    {
        AuthMiddleware::requireAuth();
        
        $input = json_decode(file_get_contents('php://input'), true);
        $index = $input['index'] ?? -1;
        $quantity = (int)($input['quantity'] ?? 1);
        
        if ($index >= 0 && isset($_SESSION['cart'][$index])) {
            $_SESSION['cart'][$index]['quantity'] = max(1, min(10, $quantity));
            $_SESSION['cart'][$index]['total_price'] = 
                $_SESSION['cart'][$index]['price_per_night'] * 
                $_SESSION['cart'][$index]['nights'] * 
                $_SESSION['cart'][$index]['quantity'];
            
            // Tính lại tổng giỏ hàng
            $subtotal = 0;
            foreach ($_SESSION['cart'] as $item) {
                $subtotal += $item['total_price'];
            }
            
            echo json_encode([
                'success' => true,
                'item_total' => number_format($_SESSION['cart'][$index]['total_price']),
                'subtotal' => number_format($subtotal),
                'total' => number_format($subtotal)
            ]);
            $this->userService->logCustomActivity((int) Auth::id(), 'CART_UPDATED', 'Cập nhật số lượng trong giỏ hàng');
            exit;
        }
        
        echo json_encode(['success' => false, 'error' => 'Không tìm thấy sản phẩm']);
        exit;
    }
    
    // Xóa phòng khỏi giỏ hàng
    public function remove()
    {
        AuthMiddleware::requireAuth();
        
        $input = json_decode(file_get_contents('php://input'), true);
        $index = $input['index'] ?? -1;
        
        if ($index >= 0 && isset($_SESSION['cart'][$index])) {
            array_splice($_SESSION['cart'], $index, 1);
            $this->userService->logCustomActivity((int) Auth::id(), 'CART_UPDATED', 'Xóa phòng khỏi giỏ hàng');
            echo json_encode(['success' => true]);
            exit;
        }
        
        echo json_encode(['success' => false, 'error' => 'Không tìm thấy sản phẩm']);
        exit;
    }
    
    // Thanh toán
    public function checkout()
    {
        if (!Auth::check()) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'error' => 'Vui lòng đăng nhập']);
                exit;
            }
            $this->redirect('/login');
            return;
        }
        AuthMiddleware::requireAuth();
        
        if (empty($_SESSION['cart'])) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'error' => 'Giỏ hàng trống']);
                exit;
            }
            $this->redirect('/cart');
            return;
        }
        
        // Chuyển dữ liệu giỏ hàng sang session để dùng trong form đặt phòng
        $_SESSION['checkout_data'] = $_SESSION['cart'];
        $this->userService->logCustomActivity((int) Auth::id(), 'CART_CHECKOUT_STARTED', 'Bắt đầu checkout từ giỏ hàng');
        
        if ($this->isAjax()) {
            echo json_encode(['success' => true]);
            exit;
        }
        
        $this->redirect('/booking/form?cart=1');
    }
    
    private function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
