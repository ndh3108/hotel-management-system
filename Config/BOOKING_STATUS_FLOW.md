# Booking Status Flow

Trạng thái đặt phòng lấy từ bảng `bookings`, cột `status` (enum: pending, confirmed, cancelled).

## Luồng trạng thái

1. **pending (Chờ thanh toán)**  
   - Booking vừa tạo.  
   - Chưa thanh toán.  
   - Phục vụ thanh toán: user thực hiện thanh toán (Momo/COD) → chuyển sang `confirmed`.

2. **confirmed (Đã xác nhận)**  
   - Đã thanh toán thành công.  
   - Có thể cập nhật `payment_menthod`, `payment_at` khi chuyển từ pending.

3. **cancelled (Đã hủy)**  
   - Đặt phòng đã hủy.  
   - Có thể chuyển từ `pending` (trước khi thanh toán).

## Chuyển trạng thái hợp lệ

- `pending` → `confirmed` (sau thanh toán)  
- `pending` → `cancelled` (hủy đặt phòng)

## Cấu hình

- **Config/booking_status.php**: label, mô tả, `transitions`, `blocks_room`, `default`.  
- **App\Core\BookingStatus**: hằng số (PENDING, CONFIRMED, CANCELLED), `defaultStatus()`, `label()`, `canTransitionTo()`, `statusesThatBlockRoom()`.

## Phục vụ thanh toán

- Tạo booking mới: `status = default` (pending).  
- Khi thanh toán thành công: gọi `BookingService::updateStatus($bookingId, BookingStatus::CONFIRMED, 'momo', date('Y-m-d H:i:s'))`.  
- Chỉ chuyển được khi `BookingStatus::canTransitionTo($current, $new)` trả về true.
