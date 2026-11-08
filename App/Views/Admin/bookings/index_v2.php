<?php

use App\Models\Booking;

$bookingLabels = Booking::bookingStatusOptions();
$paymentLabels = Booking::paymentStatusOptions();
$bookings = $bookings ?? [];

$bookingBadgeClasses = [
    Booking::STATUS_DRAFT => 'badge-secondary',
    Booking::STATUS_PENDING_PAYMENT => 'badge-warning',
    Booking::STATUS_CONFIRMED => 'badge-info',
    Booking::STATUS_CHECKED_IN => 'badge-primary',
    Booking::STATUS_CHECKED_OUT => 'badge-info',
    Booking::STATUS_COMPLETED => 'badge-success',
    Booking::STATUS_CANCELLED => 'badge-danger',
    Booking::STATUS_EXPIRED => 'badge-dark',
    Booking::STATUS_NO_SHOW => 'badge-secondary',
];

$paymentBadgeClasses = [
    Booking::PAYMENT_UNPAID => 'badge-secondary',
    Booking::PAYMENT_DEPOSIT_PENDING => 'badge-warning',
    Booking::PAYMENT_PARTIAL_PAID => 'badge-info',
    Booking::PAYMENT_PAID => 'badge-success',
    Booking::PAYMENT_OVERDUE => 'badge-danger',
    Booking::PAYMENT_REFUNDING => 'badge-warning',
    Booking::PAYMENT_REFUNDED => 'badge-info',
    Booking::PAYMENT_FAILED => 'badge-danger',
];
?>

<div class="admin-card" style="margin-bottom: 20px;">
    <div class="admin-card-body" style="display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div>
            <div style="font-size: 0.85rem; color: #64748b;">Đơn đặt phòng trên trang</div>
            <div style="font-size: 1.8rem; font-weight: 700; color: #0f172a;"><?= count($bookings) ?></div>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: #64748b;">Chờ thanh toán</div>
            <div style="font-size: 1.8rem; font-weight: 700; color: #b45309;"><?= count(array_filter($bookings, fn ($booking) => Booking::normalizeBookingStatus($booking['booking_status'] ?? $booking['status'] ?? null) === Booking::STATUS_PENDING_PAYMENT)) ?></div>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: #64748b;">Đã xác nhận/Đang ở</div>
            <div style="font-size: 1.8rem; font-weight: 700; color: #1d4ed8;"><?= count(array_filter($bookings, function ($booking) {
                $status = Booking::normalizeBookingStatus($booking['booking_status'] ?? $booking['status'] ?? null);
                return in_array($status, [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN, Booking::STATUS_CHECKED_OUT], true);
            })) ?></div>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: #64748b;">Đã hoàn thành</div>
            <div style="font-size: 1.8rem; font-weight: 700; color: #15803d;"><?= count(array_filter($bookings, fn ($booking) => Booking::normalizeBookingStatus($booking['booking_status'] ?? $booking['status'] ?? null) === Booking::STATUS_COMPLETED)) ?></div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 class="admin-card-title">Quản lý vận hành đặt phòng</h2>
            <p style="margin: 6px 0 0; color: #64748b;">Vòng đời đặt phòng và trạng thái thanh toán được hiển thị riêng biệt để quản lý chính xác.</p>
        </div>
    </div>

    <div style="padding: 15px; border-bottom: 1px solid #e2e8f0; background: #f8fbff;">
        <form method="GET" action="<?= \App\Core\url('/admin/bookings') ?>" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 0.85rem; color: #475569; margin-bottom: 5px;">Từ khóa</label>
                <input type="text" name="keyword" value="<?= htmlspecialchars((string) ($filters['keyword'] ?? '')) ?>" class="admin-form-control" style="width: 250px; padding: 8px;" placeholder="Tên khách, email, SĐT...">
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; color: #475569; margin-bottom: 5px;">Lọc trạng thái</label>
                <select name="status" class="admin-form-control" style="width: 220px; padding: 8px;">
                    <option value="">Tất cả</option>
                    <?php foreach ($bookingLabels as $statusCode => $statusText): ?>
                        <option value="<?= htmlspecialchars((string) $statusCode) ?>" <?= ($filters['status'] ?? '') === $statusCode ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $statusText) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; color: #475569; margin-bottom: 5px;">Ngày lưu trú</label>
                <input type="date" name="date" value="<?= htmlspecialchars((string) ($filters['date'] ?? '')) ?>" class="admin-form-control" style="width: 180px; padding: 8px;">
            </div>
            <div>
                <button type="submit" class="admin-btn admin-btn-primary" style="padding: 8px 15px; height: 38px;">
                    <i class="fa-solid fa-filter"></i> Lọc
                </button>
                <a href="<?= \App\Core\url('/admin/bookings') ?>" class="admin-btn admin-btn-outline" style="padding: 8px 15px; height: 38px;">Đặt lại</a>
            </div>
        </form>
    </div>

    <div class="admin-card-body" style="padding: 0;">
        <?php if (!empty($_GET['error'])): ?>
            <div style="margin: 15px; padding: 12px; background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c;">
                <?= htmlspecialchars((string) $_GET['error']) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_GET['checkout_success'])): ?>
            <div style="margin: 15px; padding: 12px; background: #dcfce7; border-left: 4px solid #22c55e; color: #15803d;">
                Thanh toán và hoàn tất (Checkout) thành công.
            </div>
        <?php endif; ?>

        <div class="admin-table-wrap">
            <table class="admin-table admin-table-min">
                <thead>
                    <tr>
                        <th>Mã đặt phòng</th>
                        <th>Khách hàng</th>
                        <th>Thời gian</th>
                        <th>Trạng thái đặt</th>
                        <th>Thanh toán</th>
                        <th>Tổng tiền</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $booking): ?>
                            <?php
                            $bookingStatus = Booking::normalizeBookingStatus($booking['booking_status'] ?? $booking['status'] ?? null);
                            $paymentStatus = Booking::normalizePaymentStatus($booking['payment_status'] ?? null, $booking['booking_status'] ?? $booking['status'] ?? null);
                            ?>
                            <tr>
                                <td>
                                    <strong>#<?= (int) ($booking['id'] ?? 0) ?></strong><br>
                                    <span style="font-size: 0.85rem; color: #64748b;"><?= htmlspecialchars((string) ($booking['booking_code'] ?? '')) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars((string) ($booking['guest_name'] ?? 'N/A')) ?></strong><br>
                                    <span style="font-size: 0.85rem; color: #64748b;"><?= htmlspecialchars((string) ($booking['email'] ?? $booking['phone'] ?? '')) ?></span>
                                </td>
                                <td>
                                    <?= !empty($booking['check_in']) ? date('d/m/Y', strtotime((string) $booking['check_in'])) : '-' ?><br>
                                    <span style="font-size: 0.85rem; color: #64748b;"><?= !empty($booking['check_out']) ? date('d/m/Y', strtotime((string) $booking['check_out'])) : '-' ?></span>
                                </td>
                                <td><span class="badge <?= $bookingBadgeClasses[$bookingStatus] ?? 'badge-secondary' ?>"><?= htmlspecialchars($bookingLabels[$bookingStatus] ?? ucfirst(str_replace('_', ' ', $bookingStatus))) ?></span></td>
                                <td><span class="badge <?= $paymentBadgeClasses[$paymentStatus] ?? 'badge-secondary' ?>"><?= htmlspecialchars($paymentLabels[$paymentStatus] ?? ucfirst(str_replace('_', ' ', $paymentStatus))) ?></span></td>
                                <td style="font-weight: 700; color: #1d4ed8;"><?= number_format((float) ($booking['total_price'] ?? 0), 0, ',', '.') ?>đ</td>
                                <td>
                                    <div class="action-btns" style="display: flex; gap: 8px;">
                                        <a href="<?= \App\Core\url('/admin/bookings/' . (int) $booking['id']) ?>" class="action-btn action-btn-view" title="Chi tiết"><i class="fa-solid fa-eye"></i></a>
                                        <?php if ($paymentStatus === Booking::PAYMENT_PAID && !in_array($bookingStatus, [Booking::STATUS_CHECKED_OUT, Booking::STATUS_COMPLETED])): ?>
                                            <form action="<?= \App\Core\url('/admin/bookings/' . (int) $booking['id'] . '/checkout') ?>" method="POST" style="margin: 0;" onsubmit="return confirm('Xác nhận hoàn tất thanh toán và trả phòng (Checkout)?');">
                                                <button type="submit" class="action-btn" title="Checkout" style="background: var(--admin-primary); color: #fff; border: none;">
                                                    <i class="fa-solid fa-sign-out-alt"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($bookingStatus === Booking::STATUS_COMPLETED): ?>
                                            <a href="<?= \App\Core\url('/admin/bookings/' . (int) $booking['id'] . '/invoice-pdf') ?>" target="_blank" class="action-btn" title="Xuất hóa đơn PDF" style="background: #ef4444; color: #fff;">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 32px; color: #64748b;">Không tìm thấy kết quả nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$queryString = '';
if (!empty($filters)) {
    foreach ($filters as $k => $v) {
        if ($v) $queryString .= '&' . $k . '=' . urlencode($v);
    }
}
?>

<div class="admin-pagination" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 20px;">
    <?php if ($pageNumber > 1): ?>
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/bookings?page=' . ($pageNumber - 1) . $queryString) ?>" style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 6px;">&laquo; Trước</a>
    <?php endif; ?>
    
    <span style="font-weight: 600; color: #475569;">Trang <?= $pageNumber ?></span>
    
    <?php if ($hasMore): ?>
        <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/bookings?page=' . ($pageNumber + 1) . '&cursor=' . $nextCursor . $queryString) ?>" style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 6px;">Sau &raquo;</a>
    <?php endif; ?>
</div>

