# Test & Verification Suite

Các script trong thư mục `tests/` giúp bạn kiểm chứng các yêu cầu Enterprise:

- Pessimistic locking & race-condition
- Overlap logic (De Morgan, back-to-back)
- Checkout + Voucher + Loyalty + PDF

> Lưu ý: Các script này **chạy trực tiếp trên DB thật trong XAMPP**. Chỉ dùng trên máy dev/đồ án, không dùng trên môi trường production.

## 1. Kiểm tra 5 test case Overlap

File: `tests/overlap_scenarios.php`

Chức năng:

- Seed một booking gốc cho 1 phòng.
- Gọi `BookingService::isRoomAvailable()` với 5 kịch bản:
  - Back-to-back trước (OK)
  - Back-to-back sau (OK)
  - Chồng lấn hoàn toàn bên trong (BLOCK)
  - Chồng lấn đầu (BLOCK)
  - Chồng lấn cuối (BLOCK)

Cách chạy:

```bash
cd c:/xampp/htdocs/DoAn
php tests/overlap_scenarios.php
```

Kỳ vọng: tất cả dòng `PASS` như đã mô tả.

## 2. Giả lập race-condition 2 request song song

File: `tests/race_condition_booking.py`

Chức năng:

- Dùng `requests` + `threading` gửi **2 POST /booking/store** gần như đồng thời.
- Dùng cùng `room_id` + `check_in/check_out`, với session cookie của 1 user đã đăng nhập.

Chuẩn bị:

1. Cài Python packages:

   ```bash
   pip install requests
   ```

2. Mở trình duyệt, đăng nhập vào website, copy giá trị `PHPSESSID` từ cookie.
3. Sửa file `tests/race_condition_booking.py`:
   - `SESSION_COOKIE = "PHPSESSID=GIÁ_TRỊ_CỦA_BẠN"`
   - Cập nhật `ROOM_ID`, `CHECK_IN`, `CHECK_OUT` nếu cần.

Chạy:

```bash
cd c:/xampp/htdocs/DoAn
python tests/race_condition_booking.py
```

Kỳ vọng:

- Tối đa **1 request** tạo booking thành công.
- Request còn lại phải trả về lỗi (overlap / room_unavailable) nhờ transaction + `SELECT ... FOR UPDATE`.

## 3. E2E: Booking → Checkout → Invoice + Loyalty + PDF

File: `tests/e2e_checkout_voucher.php`

Chức năng:

- Gọi `BookingService::createBookingFromRequest()` để tạo booking mới (ổn định, không qua HTTP).
- Gọi `CheckoutService::checkoutBooking()`:
  - Tạo `invoices` (1 bản duy nhất, idempotent).
  - Cập nhật `bookings.status` → `Checked-out`.
  - Cập nhật `rooms.status` → `Cleaning`.
  - Ghi điểm loyalty `point_history` + cập nhật `users.current_points/total_points_earned/membership_id`.
- Gọi `PdfInvoiceService::streamByBookingId()` để render hóa đơn (PDF nếu đã cài dompdf, ngược lại HTML fallback).

Chuẩn bị:

1. Đảm bảo DB đã chạy migrations (bao gồm `migration_v2.sql`).
2. Mở file `tests/e2e_checkout_voucher.php` và chỉnh:
   - `$testUserId` → ID user thật trong bảng `users`.
   - `$testRoomId` → ID phòng hợp lệ trong bảng `rooms`.
   - `$testVoucherId` (tuỳ chọn) → ID voucher đang `is_active=1` (hoặc để `null` nếu không test voucher).
   - `$pointsToRedeem` (tuỳ chọn) → Số điểm muốn dùng để cấn trừ, nếu user có `current_points`.

Chạy:

```bash
cd c:/xampp/htdocs/DoAn
php tests/e2e_checkout_voucher.php
```

Kỳ vọng:

- `[OK] Booking created...`
- `[OK] Checkout done. Invoice ID=...`
- `[OK] Invoice loaded. final_total=...`
- `[OK] PdfInvoiceService::streamByBookingId executed without fatal error.`

Sau đó, bạn có thể:

- Kiểm tra bảng `invoices`, `point_history`, `users` để xác nhận số liệu.
- Mở route admin `/admin/bookings/{id}/invoice-pdf` để xem file PDF/HTML được render trong trình duyệt.

