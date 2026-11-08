<?php
use App\Models\Booking;

$bookingStatus = Booking::normalizeBookingStatus($booking['booking_status'] ?? $booking['status'] ?? null);
$paymentStatus = Booking::normalizePaymentStatus($booking['payment_status'] ?? null, $bookingStatus);
$statusConfig = require BASE_PATH . '/Config/booking_status.php';
?>

<?php if (!empty($booking)): ?>
<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20">
    <!-- Action Header -->
    <div class="flex items-center justify-between gap-6 px-4">
        <div class="flex items-center gap-4">
            <a href="<?= \App\Core\url('/admin/bookings') ?>" class="w-12 h-12 flex items-center justify-center bg-white border border-slate-200 rounded-2xl text-slate-400 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-95">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-100">BKG-#<?= $booking['id'] ?></span>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">Quản trị hồ sơ lưu trú</h3>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1.5 px-1">Khởi tạo: <?= date('d M, Y H:i', strtotime($booking['created_at'] ?? 'now')) ?></p>
            </div>
        </div>

        <div class="flex items-center gap-3">
             <?php if ($bookingStatus === Booking::STATUS_CHECKED_OUT || $bookingStatus === Booking::STATUS_COMPLETED): ?>
                <form action="<?= \App\Core\url('/admin/bookings/' . $booking['id'] . '/checkout') ?>" method="POST" onsubmit="return confirm('Xác nhận hoàn tất thanh toán và xuất hoá đơn?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="px-6 py-3.5 bg-emerald-600 text-white text-xs font-black rounded-2xl hover:bg-emerald-500 transition-all shadow-2xl shadow-emerald-100 flex items-center gap-3 uppercase tracking-widest active:scale-95">
                        <i class="fa-solid fa-check-double text-sm"></i> Quyết toán tài chính
                    </button>
                </form>
            <?php endif; ?>
            
            <a href="<?= \App\Core\url('/admin/bookings/' . $booking['id'] . '/bill') ?>" target="_blank" class="px-6 py-3.5 bg-indigo-600 text-white text-xs font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-lg flex items-center gap-3 uppercase tracking-widest active:scale-95">
                <i class="fa-solid fa-eye text-sm"></i> Xem hóa đơn
            </a>

            <a href="<?= \App\Core\url('/admin/bookings/' . $booking['id'] . '/invoice-pdf') ?>" target="_blank" class="px-6 py-3.5 bg-white border border-slate-200 text-slate-600 text-xs font-black rounded-2xl hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm flex items-center gap-3 uppercase tracking-widest active:scale-95 group">
                <i class="fa-solid fa-file-pdf text-rose-500 group-hover:text-white transition-colors text-sm"></i> Xuất Invoice PDF
            </a>
        </div>
    </div>

    <!-- Notifications Hub -->
    <?php if (isset($_GET['checkout_success']) || isset($_GET['status_updated']) || isset($_GET['error'])): ?>
        <div class="px-4 space-y-4">
            <?php if (isset($_GET['checkout_success'])): ?>
                <div class="p-6 bg-emerald-50 border border-emerald-100 rounded-3xl flex items-center gap-4 text-emerald-700 animate-slide-down">
                    <div class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-emerald-100">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Hệ thống tài chính</p>
                        <p class="font-bold text-sm uppercase">Quyết toán thành công! Invoice đã được tạo và gửi bản in.</p>
                    </div>
                    <script>setTimeout(() => window.open("<?= \App\Core\url('/admin/bookings/' . $booking['id'] . '/invoice-pdf') ?>", "_blank"), 500);</script>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['status_updated'])): ?>
                <div class="p-6 bg-indigo-50 border border-indigo-100 rounded-3xl flex items-center gap-4 text-indigo-700 animate-slide-down">
                    <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-indigo-100">
                        <i class="fa-solid fa-circle-info text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-400">Trạng thái vận hành</p>
                        <p class="font-bold text-sm uppercase">Trạng thái đặt phòng đã được đồng bộ trên toàn hệ thống.</p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="p-6 bg-rose-50 border border-rose-100 rounded-3xl flex items-center gap-4 text-rose-700 animate-slide-down">
                    <div class="w-10 h-10 bg-rose-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-rose-100">
                        <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-rose-400">Lỗi thực thi</p>
                        <p class="font-bold text-sm uppercase"><?= htmlspecialchars($_GET['error']) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 px-4">
        <!-- Left Column: Core Data (8/12) -->
        <div class="lg:col-span-8 space-y-10">
            <!-- Guest & Stay Profile Card -->
            <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden group">
                <div class="p-10 border-b border-slate-50 flex items-center gap-8 bg-slate-50/20">
                    <div class="w-20 h-20 rounded-[2rem] bg-indigo-600 text-white flex items-center justify-center text-3xl shadow-xl shadow-indigo-200 group-hover:scale-105 transition-transform duration-500 italic font-black">
                        <?= strtoupper(substr($booking['guest_name'] ?? 'G', 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-1 italic">Hồ sơ khách hàng</p>
                        <h3 class="text-3xl font-black text-slate-800 uppercase tracking-tighter italic leading-none mb-2"><?= htmlspecialchars($booking['guest_name'] ?? 'Guest Profile') ?></h3>
                        <div class="flex items-center gap-6">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 italic">
                                <i class="fa-solid fa-envelope text-indigo-400 text-xs"></i>
                                <?= htmlspecialchars($booking['email'] ?? 'N/A') ?>
                            </span>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 italic">
                                <i class="fa-solid fa-phone text-indigo-400 text-xs"></i>
                                <?= htmlspecialchars($booking['phone'] ?? 'N/A') ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div class="space-y-8">
                        <div>
                            <div class="flex items-center gap-2 mb-6">
                                <span class="w-6 h-1 bg-indigo-500 rounded-full"></span>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Timeline Lưu trú</p>
                            </div>
                            <div class="flex items-center gap-8 relative">
                                <div class="flex-1 space-y-2">
                                    <div class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-md text-[9px] font-black uppercase tracking-widest w-fit mb-2">Check-in</div>
                                    <div class="text-2xl font-black text-slate-800 tracking-tighter italic leading-none mb-1"><?= date('d F, Y', strtotime($booking['check_in'] ?? 'now')) ?></div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Từ 14:00 AM</div>
                                </div>
                                <div class="w-12 h-12 rounded-full border border-slate-100 flex items-center justify-center text-slate-200 bg-slate-50 shadow-inner shrink-0 group-hover:rotate-12 transition-transform duration-500">
                                    <i class="fa-solid fa-arrow-right-long"></i>
                                </div>
                                <div class="flex-1 space-y-2 text-right">
                                    <div class="px-3 py-1 bg-rose-50 text-rose-600 rounded-md text-[9px] font-black uppercase tracking-widest w-fit ml-auto mb-2">Check-out</div>
                                    <div class="text-2xl font-black text-slate-800 tracking-tighter italic leading-none mb-1 text-rose-600"><?= date('d F, Y', strtotime($booking['check_out'] ?? 'now')) ?></div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Trước 12:00 PM</div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-8 border-t border-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-inner italic font-black">
                                    #
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Passport / ID</p>
                                    <p class="font-black text-slate-800 uppercase tracking-tight text-sm"><?= htmlspecialchars($booking['id_card'] ?? 'CHƯA CẬP NHẬT') ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Quốc tịch</p>
                                <p class="font-black text-slate-800 uppercase tracking-tight text-sm"><?= htmlspecialchars($booking['nationality'] ?? 'VIETNAM') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="flex items-center gap-2 mb-6">
                            <span class="w-6 h-1 bg-indigo-900 rounded-full"></span>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Cấu hình phòng</p>
                        </div>
                        <div class="p-8 rounded-[2.5rem] bg-indigo-900 shadow-2xl shadow-indigo-200 relative overflow-hidden group/room">
                            <!-- Subtle backdrop icon -->
                            <i class="fa-solid fa-bed absolute -right-4 -bottom-4 opacity-5 text-8xl text-white rotate-12 group-hover/room:scale-125 transition-transform duration-1000"></i>
                            
                            <div class="relative z-10 space-y-6">
                                <div class="flex items-center justify-between">
                                    <span class="px-4 py-1.5 bg-white text-indigo-900 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl">Phòng <?= htmlspecialchars($booking['room_number'] ?? 'N/A') ?></span>
                                    <span class="text-[10px] font-black text-white/60 uppercase tracking-[0.2em] italic"><?= htmlspecialchars($booking['room_type_name'] ?? 'DELUXE ROOM') ?></span>
                                </div>
                                <div class="pt-2">
                                    <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-1 italic">Giá thuê niêm yết</p>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-4xl font-black text-white italic tracking-tighter"><?= number_format($booking['total_price'] ?? 0, 0, ',', '.') ?>₫</span>
                                        <span class="text-[10px] font-black text-white/40 uppercase tracking-widest">Base Rate Total</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 text-white/60">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-user-friends text-[10px]"></i>
                                        <span class="text-[9px] font-black uppercase tracking-widest"><?= $booking['adults'] ?? 2 ?> PX</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-child text-[10px]"></i>
                                        <span class="text-[9px] font-black uppercase tracking-widest"><?= $booking['children'] ?? 0 ?> PX</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Breakdown / Ledger Card -->
            <?php if (!empty($invoice)): ?>
                <div class="bg-slate-900 rounded-[3rem] shadow-2xl p-10 md:p-12 text-white relative overflow-hidden group">
                    <div class="absolute -right-20 -top-20 opacity-5 group-hover:scale-110 transition-transform duration-1000 rotate-12">
                        <i class="fa-solid fa-receipt text-[25rem]"></i>
                    </div>
                    <div class="relative z-10 space-y-12">
                        <div class="flex items-center justify-between border-b border-white/5 pb-8">
                            <div>
                                <h3 class="text-2xl font-black uppercase tracking-tighter italic">Bảng kê chi tiết quyết toán</h3>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Hạch toán Invoice #<?= $invoice['id'] ?? 'DRAFT' ?>
                                </p>
                            </div>
                            <div class="px-6 py-3 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-xl">
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-400 italic">Settle Balance Dashboard</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                            <div class="space-y-2">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">Giá tiền phòng</p>
                                <p class="text-2xl font-black tracking-tighter italic"><?= number_format((float) ($invoice['room_amount'] ?? 0), 0, ',', '.') ?>₫</p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">Dịch vụ bổ trợ</p>
                                <p class="text-2xl font-black tracking-tighter italic text-indigo-400"><?= number_format((float) ($invoice['service_amount'] ?? 0), 0, ',', '.') ?>₫</p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">Hư tổn & Phạt</p>
                                <p class="text-2xl font-black tracking-tighter italic text-rose-500">+<?= number_format((float) (($invoice['late_fee'] ?? 0) + ($invoice['damage_fee'] ?? 0)), 0, ',', '.') ?>₫</p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">Ưu đãi giảm giá</p>
                                <p class="text-2xl font-black tracking-tighter italic text-emerald-400">-<?= number_format((float) ($invoice['discount_amount'] ?? 0), 0, ',', '.') ?>₫</p>
                            </div>
                        </div>

                        <div class="pt-12 border-t border-white/5 flex flex-col md:flex-row items-end justify-between gap-8">
                            <div class="flex items-center gap-12 bg-white/5 p-8 rounded-[2rem] border border-white/5 shadow-inner">
                                <div>
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 italic">Đã trả trước (Deposit)</p>
                                    <p class="text-2xl font-black text-indigo-400 italic tracking-tighter"><?= number_format((float) ($invoice['prepaid_amount'] ?? 0), 0, ',', '.') ?>₫</p>
                                </div>
                                <div class="text-slate-700">
                                    <i class="fa-solid fa-minus"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 italic text-rose-400">Balance Due</p>
                                    <p class="text-3xl font-black text-rose-500 italic tracking-tighter"><?= number_format((float) ($invoice['balance_due'] ?? 0), 0, ',', '.') ?>₫</p>
                                </div>
                            </div>
                            <div class="text-right pb-4">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 italic">Tổng giá trị thanh toán</p>
                                <p class="text-6xl font-black text-white italic tracking-tighter"><?= number_format((float) ($invoice['final_total'] ?? 0), 0, ',', '.') ?>₫</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Control & Operational Hub (4/12) -->
        <div class="lg:col-span-4 space-y-10">
            <!-- Global Status Control -->
            <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-10 space-y-10 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-2 h-full bg-indigo-600 transition-all duration-700 group-hover:w-4"></div>
                <div>
                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter italic">Status Dispatch</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1 italic">Trung tâm điều phối booking</p>
                </div>

                <div class="space-y-8">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Trạng thái vận hành hiện tại</p>
                        <?php
                            $badgeColor = $statusConfig[$bookingStatus]['color'] ?? 'slate';
                            $badgeClass = match($badgeColor) {
                                'success' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'warning' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'danger'  => 'bg-rose-50 text-rose-700 border-rose-100',
                                'info'    => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                default   => 'bg-slate-50 text-slate-600 border-slate-200'
                            };
                        ?>
                        <div class="flex flex-wrap gap-3">
                            <span class="inline-flex items-center px-6 py-2.5 rounded-2xl border text-[10px] font-black uppercase tracking-widest shadow-sm <?= $badgeClass ?>">
                                <span class="w-2 h-2 rounded-full mr-3 bg-current animate-pulse"></span>
                                <?= htmlspecialchars($statusConfig[$bookingStatus]['label'] ?? $bookingStatus) ?>
                            </span>
                            <span class="px-5 py-2.5 rounded-2xl bg-slate-50 text-slate-500 border border-slate-100 text-[10px] font-black uppercase tracking-widest italic">
                                PMT: <?= htmlspecialchars(Booking::paymentStatusOptions()[$paymentStatus] ?? $paymentStatus) ?>
                            </span>
                        </div>
                    </div>

                    <form action="<?= \App\Core\url('/admin/bookings/' . $booking['id'] . '/status') ?>" method="POST" class="space-y-6 pt-10 border-t border-slate-50">
                        <?= csrf_field() ?>
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic ml-1 underline underline-offset-4 decoration-indigo-200">Luân chuyển trạng thái</label>
                            <div class="relative">
                                <select name="status" class="w-full appearance-none px-6 py-4 rounded-[1.5rem] border border-slate-100 bg-slate-50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner cursor-pointer" required>
                                    <option value="">-- CHỌN TRẠNG THÁI TIẾP --</option>
                                    <?php
                                    $allowedTransitions = $statusConfig['transitions'][$bookingStatus] ?? [];
                                    foreach ($allowedTransitions as $nextStatus) {
                                        $nextLabel = htmlspecialchars($statusConfig[$nextStatus]['label'] ?? ucfirst($nextStatus));
                                        echo "<option value=\"" . htmlspecialchars($nextStatus) . "\">→ " . strtoupper($nextLabel) . " (SYNC)</option>";
                                    }
                                    ?>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 px-2">
                            <input type="checkbox" name="notify_user" id="notify_user" value="1" checked class="w-5 h-5 rounded-lg border-slate-200 text-indigo-600 focus:ring-indigo-500">
                            <label for="notify_user" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Auto notify guest via system mail</label>
                        </div>
                        <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-slate-900 text-white font-black rounded-[1.5rem] transition-all shadow-2xl shadow-indigo-100 active:scale-95 text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                             <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                             Confirm Transition
                        </button>
                    </form>

                    <!-- Quick Action Buttons -->
                    <div class="space-y-4 pt-10 border-t border-slate-50">
                        <?php if ($bookingStatus === Booking::STATUS_CONFIRMED): ?>
                            <form action="<?= \App\Core\url('/admin/bookings/' . $booking['id'] . '/check-in') ?>" method="POST">
                                <?= csrf_field() ?>
                                <button type="submit" class="w-full py-5 bg-emerald-50 text-emerald-700 border border-emerald-100 font-black rounded-[1.5rem] hover:bg-emerald-600 hover:text-white transition-all text-[11px] uppercase tracking-widest shadow-sm active:scale-95 group/check">
                                    <i class="fa-solid fa-door-open mr-2 group-hover/check:-translate-x-1 transition-transform"></i> Bắt đầu Check-in
                                </button>
                            </form>
                        <?php endif; ?>
                        <?php if ($bookingStatus === Booking::STATUS_CHECKED_IN): ?>
                            <form action="<?= \App\Core\url('/admin/bookings/' . $booking['id'] . '/check-out') ?>" method="POST">
                                <?= csrf_field() ?>
                                <button type="submit" class="w-full py-5 bg-rose-50 text-rose-700 border border-rose-100 font-black rounded-[1.5rem] hover:bg-rose-600 hover:text-white transition-all text-[11px] uppercase tracking-widest shadow-sm active:scale-95 group/out">
                                    <i class="fa-solid fa-door-closed mr-2 group-hover/out:translate-x-1 transition-transform"></i> Thực hiện Check-out
                                </button>
                            </form>
                        <?php endif; ?>
                        <?php if ($bookingStatus === Booking::STATUS_CHECKED_OUT): ?>
                            <form action="<?= \App\Core\url('/admin/bookings/' . $booking['id'] . '/complete') ?>" method="POST">
                                <?= csrf_field() ?>
                                <button type="submit" class="w-full py-5 bg-indigo-900 text-white font-black rounded-[1.5rem] transition-all text-[11px] uppercase tracking-widest shadow-2xl shadow-indigo-200 active:scale-95">
                                    <i class="fa-solid fa-check-circle mr-2 text-amber-400"></i> Hoàn tất hồ sơ
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Operational Support: Housekeeping & Inventory -->
            <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-10 space-y-8 group/hk">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter italic">Buồng phòng & Vật tư</h3>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1 italic">Quản trị hư tổn & tiêu hao</p>
                    </div>
                    <div class="w-12 h-12 rounded-[1.5rem] bg-amber-50 text-amber-500 flex items-center justify-center shadow-inner group-hover/hk:rotate-12 transition-transform">
                        <i class="fa-solid fa-broom text-xl"></i>
                    </div>
                </div>

                <div class="space-y-6">
                    <?php if (!in_array($bookingStatus, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED], true)): ?>
                        <button type="button" class="w-full py-4 border-2 border-dashed border-slate-200 rounded-[1.5rem] text-slate-400 font-black text-[10px] uppercase tracking-widest hover:border-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all flex items-center justify-center gap-3 active:scale-95" 
                                data-bs-toggle="modal" data-bs-target="#inventoryCheckModal">
                            <i class="fa-solid fa-plus text-xs"></i> Lập báo cáo hư tổn
                        </button>
                    <?php endif; ?>

                    <div class="space-y-4">
                         <?php if (!empty($lossAndDamages)): ?>
                            <?php foreach ($lossAndDamages as $ld): ?>
                                <div class="p-5 bg-slate-50/50 rounded-2xl border border-slate-100 flex flex-col gap-3 hover:bg-rose-50 transition-all group/ld">
                                    <div class="flex items-center justify-between">
                                        <h5 class="text-[11px] font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <?= htmlspecialchars($ld['item_name'] ?? 'VẬT TƯ') ?>
                                        </h5>
                                        <span class="text-sm font-black text-rose-600 italic"><?= number_format((float)($ld['price'] * $ld['quantity']), 0, ',', '.') ?>₫</span>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                                        <div class="flex gap-4">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">SL: <?= (int)$ld['quantity'] ?></span>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic border-l border-slate-200 pl-4"><?= date('d/m/y', strtotime($ld['created_at'])) ?></span>
                                        </div>
                                        <i class="fa-solid fa-triangle-exclamation text-rose-200 text-xs opacity-0 group-hover/ld:opacity-100 transition-opacity"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-10 text-center bg-slate-50 border border-dashed border-slate-100 rounded-[2.5rem]">
                                <i class="fa-solid fa-shield-check text-slate-100 text-5xl mb-4"></i>
                                <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] italic">No operational issues reported</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Checklist - High Fidelity Operational Suite -->
<div class="modal fade" id="inventoryCheckModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-[3rem] border-none shadow-2xl overflow-hidden animate-fade-in">
            <form action="<?= \App\Core\url('/admin/bookings/' . $booking['id'] . '/damage') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="bg-slate-900 px-12 py-12 text-white relative">
                    <div class="absolute -right-10 -top-10 text-white opacity-5 pointer-events-none rotate-12">
                        <i class="fa-solid fa-clipboard-list text-[15rem]"></i>
                    </div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-black uppercase tracking-tighter italic leading-none mb-4">Operational Damage Audit</h2>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest flex items-center gap-2">
                                <i class="fa-solid fa-door-closed text-amber-500"></i>
                                Phòng <?= htmlspecialchars($booking['room_number'] ?? 'N/A') ?> — Kê khai hạch toán hư tổn
                            </p>
                        </div>
                        <button type="button" class="w-12 h-12 bg-white/5 hover:bg-white/10 rounded-2xl flex items-center justify-center transition-all" data-bs-dismiss="modal">
                            <i class="fa-solid fa-times text-slate-400"></i>
                        </button>
                    </div>
                </div>
                
                <div class="px-12 py-12 space-y-8 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div class="p-6 bg-indigo-50 border border-indigo-100 rounded-[1.5rem] flex items-start gap-4 shadow-inner">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-indigo-100">
                            <i class="fa-solid fa-lightbulb"></i>
                        </div>
                        <div class="text-[11px] text-indigo-900 leading-loose font-black uppercase tracking-wide py-1">
                            Vui lòng nhập số lượng vật tư thực tế bị mất hoặc hư hại nặng. Hệ thống sẽ hạch toán phí bồi thường trực tiếp vào Final Balance của booking này.
                        </div>
                    </div>

                    <?php if (!empty($inventoryList)): ?>
                        <div class="grid grid-cols-1 gap-4" id="damageInputContainer">
                            <?php foreach ($inventoryList as $inv): 
                                $itemId = $inv['item_id'] ?? $inv['id'];
                                $price = $inv['price_at_booking'] ?? $inv['price_override'] ?? $inv['default_price'] ?? 0;
                            ?>
                                <div class="p-6 bg-white border border-slate-100 rounded-[2rem] flex flex-col md:flex-row items-center gap-8 hover:shadow-xl hover:shadow-slate-100 transition-all duration-300">
                                    <div class="flex-1 space-y-2">
                                        <div class="text-sm font-black text-slate-800 uppercase tracking-tight italic"><?= htmlspecialchars($inv['item_name']) ?></div>
                                        <div class="flex items-center gap-4">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-2 py-0.5 rounded italic">Định mức: <?= $inv['quantity'] ?? 1 ?></span>
                                            <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest italic border-l border-slate-100 pl-4">Cost: <?= number_format($price, 0, ',', '.') ?>₫/item</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-5 w-full md:w-auto shrink-0">
                                        <div class="w-24">
                                            <input type="number" name="items[<?= $itemId ?>][lost]" 
                                                   class="damage-qty w-full px-4 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-center font-black text-slate-800 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none" 
                                                   min="0" value="0" data-price="<?= $price ?>" onchange="calculateDamageTotal()">
                                        </div>
                                        <div class="flex-1 md:w-72">
                                            <input type="text" name="items[<?= $itemId ?>][notes]" 
                                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-600 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none" placeholder="DIỄN GIẢI HIỆN TRẠNG / LÝ DO...">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="py-24 text-center">
                            <i class="fa-solid fa-database text-slate-100 text-6xl mb-6"></i>
                            <p class="text-slate-300 font-black uppercase tracking-[0.2em] italic text-xs">Chưa có cấu hình định mức vật tư cho hạng phòng này.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="px-12 py-10 bg-slate-50 flex items-center justify-between border-t border-slate-100">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic underline underline-offset-4 decoration-rose-200">Tổng phí phát sinh dự kiến</p>
                        <h4 class="text-4xl font-black text-rose-600 italic tracking-tighter" id="liveDamageTotal">0₫</h4>
                    </div>
                    <div class="flex gap-4">
                        <button type="button" class="px-10 py-5 bg-white border border-slate-200 text-slate-500 font-black rounded-3xl hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all text-[10px] uppercase tracking-widest active:scale-95 shadow-sm" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="px-10 py-5 bg-indigo-600 text-white font-black rounded-3xl hover:bg-slate-900 transition-all shadow-2xl shadow-indigo-100 text-[10px] uppercase tracking-widest flex items-center gap-3 active:scale-95">
                            <i class="fa-solid fa-save text-amber-400 text-xs"></i> Cấu tạo báo cáo
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function calculateDamageTotal() {
        let total = 0;
        document.querySelectorAll('.damage-qty').forEach(input => {
            const qty = parseInt(input.value) || 0;
            const price = parseFloat(input.dataset.price) || 0;
            total += qty * price;
        });
        document.getElementById('liveDamageTotal').innerText = new Intl.NumberFormat('vi-VN').format(total) + '₫';
    }
</script>


<?php else: ?>
    <div class="py-40 text-center bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 italic">
        <i class="fa-solid fa-file-circle-exclamation text-slate-100 text-9xl mb-8 opacity-20"></i>
        <h3 class="text-2xl font-black text-slate-300 uppercase tracking-widest">Hồ sơ không tồn tại</h3>
        <p class="text-slate-400 mt-2 font-bold uppercase text-[10px] tracking-[0.3em]">Hệ thống không tìm thấy thông tin đặt phòng yêu cầu</p>
    </div>
<?php endif; ?>
