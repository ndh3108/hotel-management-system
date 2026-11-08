<?php
$room = $room ?? [];
$activeBooking = $activeBooking ?? null;
$inventory = $inventory ?? [];
?>

<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Alerts -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="p-6 bg-emerald-50 border border-emerald-100 rounded-[2rem] flex items-center gap-4 animate-slide-down">
            <div class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-emerald-200">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest italic mb-1">Hệ thống ghi nhận</p>
                <p class="font-black text-emerald-900 uppercase tracking-tight italic"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="p-6 bg-rose-50 border border-rose-100 rounded-[2rem] flex items-center gap-4 animate-slide-down">
            <div class="w-12 h-12 bg-rose-500 text-white rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-rose-200">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest italic mb-1">Lỗi hệ thống</p>
                <p class="font-black text-rose-900 uppercase tracking-tight italic"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php 
    // Handle GET success/error for quick feedback
    $getSuccess = $_GET['success'] ?? null;
    $getErr = $_GET['error'] ?? null;
    if ($getSuccess):
        $msg = match($getSuccess) {
            'reported' => 'Báo cáo hư tổn đã được ghi nhận thành công.',
            'replaced' => 'Vật tư đã được thay thế mới từ kho và cập nhật số lượng.',
            'status_updated' => 'Trạng thái phòng đã được cập nhật.',
            default => 'Thao tác thành công.'
        };
    ?>
        <div class="p-6 bg-indigo-50 border border-indigo-100 rounded-[2rem] flex items-center gap-4 animate-slide-down">
            <div class="w-12 h-12 bg-indigo-500 text-white rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-indigo-200">
                <i class="fa-solid fa-circle-info"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic mb-1">Thông báo</p>
                <p class="font-black text-indigo-900 uppercase tracking-tight italic"><?= htmlspecialchars($msg) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
        <!-- Main Content: Reporting (8/12) -->
        <div class="xl:col-span-8 space-y-10">
            <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <!-- Card Header -->
                <div class="p-12 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-8 bg-slate-50/20">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-[2rem] bg-slate-900 border-4 border-slate-800 flex flex-col items-center justify-center text-white font-black shadow-2xl shadow-slate-200 italic group/header hover:rotate-12 transition-transform duration-500">
                            <span class="text-[10px] leading-none opacity-50 mb-1">ROOM</span>
                            <span class="text-3xl leading-none"><?= htmlspecialchars($room['room_number']) ?></span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Kiểm tra vật tư & Hiện trạng</h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-2 italic"><?= htmlspecialchars($room['room_type_name']) ?> — Facility Audit Protocol</p>
                        </div>
                    </div>
                    <a href="<?= \App\Core\url('/admin/housekeeping') ?>" class="px-8 py-4 rounded-2xl bg-white border border-slate-200 text-slate-500 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all flex items-center gap-3 shadow-sm active:scale-95 text-xs uppercase tracking-widest italic">
                        <i class="fa-solid fa-arrow-left-long"></i> Bảng điều hành
                    </a>
                </div>

                <div class="p-12">
                    <!-- Guest Context Banner -->
                    <?php if (!$activeBooking): ?>
                        <div class="p-10 bg-slate-50 border border-slate-100 border-dashed rounded-[3rem] flex items-center gap-8 mb-12 group hover:bg-slate-100/50 transition-all">
                            <div class="w-16 h-16 bg-white text-slate-300 rounded-2xl flex items-center justify-center text-2xl shadow-inner border border-slate-100 group-hover:scale-110 group-hover:text-amber-500 transition-all">
                                <i class="fa-solid fa-hotel"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-2 italic">Operating Context: Vacant State</p>
                                <h4 class="text-lg font-black text-slate-900 uppercase leading-none italic">Sẵn sàng vận hành (Trống)</h4>
                                <p class="text-[10px] font-bold text-slate-400/60 uppercase tracking-widest mt-3 italic">Báo cáo sẽ được ghi nhận là bảo trì định kỳ, không phát sinh chi phí lưu trú.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="p-12 bg-slate-900 rounded-[3.5rem] shadow-2xl shadow-slate-200 flex flex-col md:flex-row md:items-center gap-10 mb-12 relative overflow-hidden group/guest">
                            <div class="absolute -right-10 -bottom-10 opacity-5 group-hover/guest:scale-110 transition-transform rotate-12 duration-1000 text-white font-black text-[12rem]">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div class="w-24 h-24 bg-white/5 backdrop-blur-3xl text-white rounded-[2.5rem] flex items-center justify-center text-4xl shadow-2xl border border-white/10 z-10 shrink-0 group-hover/guest:rotate-6 transition-transform">
                                <i class="fa-solid fa-passport"></i>
                            </div>
                            <div class="relative z-10 flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.3em] italic">Đang lưu trú — Occupied</span>
                                </div>
                                <h3 class="text-4xl font-black text-white uppercase tracking-tight mb-2 italic"><?= htmlspecialchars($activeBooking['guest_name']) ?></h3>
                                <div class="flex flex-wrap items-center gap-6 mt-4">
                                    <div class="flex items-center gap-2 px-4 py-2 bg-white/5 rounded-xl border border-white/10">
                                        <i class="fa-solid fa-calendar-check text-indigo-400 text-[10px]"></i>
                                        <span class="text-[9px] font-black text-white/70 uppercase tracking-widest italic">Out: <?= date('d/m/Y', strtotime($activeBooking['checkout_date'] ?? 'now')) ?></span>
                                    </div>
                                    <div class="flex items-center gap-2 px-4 py-2 bg-indigo-500/10 rounded-xl border border-indigo-500/20">
                                        <i class="fa-solid fa-file-invoice-dollar text-indigo-400 text-[10px]"></i>
                                        <span class="text-[9px] font-black text-indigo-300 uppercase tracking-widest italic">Áp phí hư tổn khách hàng</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Inventory Section -->
                    <div class="flex items-center justify-between mb-10 border-b border-slate-50 pb-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-300">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">Danh mục vật tư tiêu chuẩn</h3>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1 italic">Total <?= count($inventory) ?> Asset Classifications</p>
                            </div>
                        </div>
                        <div class="px-5 py-2.5 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest italic leading-none">Ready for Audit</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <?php foreach ($inventory as $item): ?>
                            <?php 
                                $status = $item['status'] ?? 'Available';
                                $statusClasses = match($status) {
                                    'Lost' => 'border-rose-200 bg-rose-50 text-rose-600',
                                    'Damaged' => 'border-amber-200 bg-amber-50 text-amber-600',
                                    default => 'border-emerald-200 bg-emerald-50 text-emerald-600'
                                };
                                $statusLabel = match($status) {
                                    'Lost' => 'Mất mát / Thất lạc',
                                    'Damaged' => 'Bị hư hỏng',
                                    default => 'Sẵn sàng / Sẵn có'
                                };
                            ?>
                            <div class="p-10 border border-slate-100 bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 flex flex-col md:flex-row md:items-center justify-between group hover:border-indigo-200 hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-500 relative overflow-hidden gap-8">
                                <div class="flex gap-6 items-center flex-1">
                                    <div class="w-16 h-16 rounded-[1.5rem] bg-slate-50 flex items-center justify-center text-slate-300 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 text-2xl shadow-inner border border-slate-100 group-hover:shadow-xl group-hover:rotate-6">
                                        <i class="fa-solid <?= $item['icon_class'] ?? 'fa-box-archive' ?>"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-black text-slate-900 group-hover:text-indigo-600 transition-colors uppercase tracking-tight text-lg italic leading-tight mb-2"><?= htmlspecialchars($item['item_name'] ?? 'Undefined Asset') ?></p>
                                        <div class="flex items-center gap-4 mt-2">
                                            <div class="flex items-center gap-2 px-3 py-1 bg-slate-50 rounded-lg border border-slate-100">
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic">Quantity</span>
                                                <span class="text-[10px] font-black text-slate-900 italic"><?= $item['quantity'] ?></span>
                                            </div>
                                            <div class="px-3 py-1 border rounded-lg text-[8px] font-black uppercase tracking-widest <?= $statusClasses ?>">
                                                <?= $statusLabel ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-6">
                                    <!-- Status Toggles -->
                                    <form action="<?= \App\Core\url('/admin/housekeeping/update-item-status') ?>" method="POST" class="flex items-center bg-slate-50 p-2 rounded-2xl border border-slate-100">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="room_inventory_id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                                        
                                        <button type="submit" name="status" value="Available" title="Đánh dấu: Sẵn sàng"
                                                class="w-10 h-10 rounded-xl flex items-center justify-center transition-all <?= $status === 'Available' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-200' : 'text-slate-400 hover:bg-white hover:text-emerald-500' ?>">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                        <button type="submit" name="status" value="Lost" title="Đánh dấu: Mất"
                                                class="w-10 h-10 rounded-xl flex items-center justify-center transition-all <?= $status === 'Lost' ? 'bg-rose-500 text-white shadow-lg shadow-rose-200' : 'text-slate-400 hover:bg-white hover:text-rose-500' ?>">
                                            <i class="fa-solid fa-ghost"></i>
                                        </button>
                                        <button type="submit" name="status" value="Damaged" title="Đánh dấu: Hỏng"
                                                class="w-10 h-10 rounded-xl flex items-center justify-center transition-all <?= $status === 'Damaged' ? 'bg-amber-500 text-white shadow-lg shadow-amber-200' : 'text-slate-400 hover:bg-white hover:text-amber-500' ?>">
                                            <i class="fa-solid fa-heart-crack"></i>
                                        </button>
                                    </form>

                                    <div class="h-10 w-[1px] bg-slate-100 hidden md:block"></div>

                                    <div class="flex gap-3">
                                        <button onclick="openReportModal(<?= $item['item_id'] ?>, '<?= htmlspecialchars($item['item_name']) ?>')" 
                                                title="Báo cáo hư tổn chi tiết"
                                                class="w-12 h-12 bg-rose-50 text-rose-500 rounded-2xl border border-rose-100 flex items-center justify-center hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-lg shadow-rose-100/50 active:scale-90 group-hover:scale-105">
                                            <i class="fa-solid fa-triangle-exclamation text-base"></i>
                                        </button>
                                        <form action="<?= \App\Core\url('/admin/housekeeping/quick-replace') ?>" method="POST" onsubmit="return confirm('Xác nhận thay thế vật tư này từ kho và trừ số lượng tồn kho?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                                            <input type="hidden" name="booking_detail_id" value="<?= $activeBooking['booking_detail_id'] ?? '' ?>">
                                            <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" title="Thay thế mới từ kho"
                                                    class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl border border-emerald-100 flex items-center justify-center hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all shadow-lg shadow-emerald-100/50 active:scale-90 group-hover:scale-105">
                                                <i class="fa-solid fa-screwdriver-wrench text-base"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Decorative grid pattern -->
                                <div class="absolute -right-4 -top-4 w-12 h-12 bg-indigo-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity blur-2xl"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Room Controls & Stats (4/12) -->
        <div class="xl:col-span-4 space-y-10">
            <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden relative group/stats">
                    <div id="statusUpdateFeedback" class="hidden animate-slide-down p-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-center shadow-inner"></div>
                    
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cập nhật nhanh trạng thái</label>
                        <div class="relative">
                            <select id="roomStatusSelect" class="w-full appearance-none px-6 py-5 rounded-[1.5rem] border border-slate-100 bg-slate-50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-lg uppercase tracking-tight shadow-inner">
                                <?php foreach(\App\Models\Room::statusOptions() as $val => $lbl): ?>
                                    <option value="<?= $val ?>" <?= $room['display_status'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <button type="button" onclick="updateRoomStatus(<?= $room['id'] ?>)" id="btnUpdateStatus" class="w-full py-5 bg-slate-900 hover:bg-slate-800 text-white font-black rounded-[1.5rem] transition-all shadow-2xl shadow-slate-200 flex items-center justify-center gap-3 active:scale-95 uppercase text-xs tracking-widest leading-none">
                        <i class="fa-solid fa-sync text-sm"></i>
                        Cập nhật ngay tắp lự
                    </button>
                    
                    <div class="pt-8 border-t border-slate-50 space-y-6">
                        <div class="flex items-center gap-5 group">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-lg shadow-inner group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-map-pin"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Định vị</p>
                                <p class="font-black text-slate-900 uppercase tracking-tight group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($room['location'] ?? 'Chưa xác định') ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-5 group">
                            <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center text-lg shadow-inner group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Độ cao</p>
                                <p class="font-black text-slate-900 uppercase tracking-tight group-hover:text-indigo-600 transition-colors italic">Tầng <?= $room['floor'] ?> / Building <?= htmlspecialchars($room['building'] ?? 'A') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Support Contact -->
            <div class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-2xl shadow-indigo-100 text-center space-y-4 relative overflow-hidden group">
                <div class="absolute inset-0 opacity-0 group-hover:opacity-5 transition-opacity duration-700 bg-[radial-gradient(circle_at_center,_white_1px,_transparent_1px)] bg-[length:10px_10px]"></div>
                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest italic opacity-60">Hỗ trợ kỹ thuật?</p>
                <a href="tel:1900" class="block text-lg font-black text-white hover:text-amber-400 transition-colors uppercase tracking-widest">
                    Hotline kỹ thuật <i class="fa-solid fa-phone text-xs ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div id="reportModal" class="hidden fixed inset-0 bg-slate-900/60 z-[9991] items-center justify-center backdrop-blur-md animate-fade-in">
    <div class="bg-white w-[500px] rounded-[3rem] p-12 shadow-2xl shadow-slate-900/40 relative overflow-hidden animate-slide-down">
        <!-- Modal Branding -->
        <div class="absolute -right-4 -top-4 opacity-5 pointer-events-none rotate-12 transition-transform duration-700 hover:rotate-0 text-slate-900">
            <i class="fa-solid fa-triangle-exclamation text-9xl"></i>
        </div>

        <div class="mb-10 text-center relative z-10">
            <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-3xl flex items-center justify-center mx-auto text-3xl shadow-inner border border-rose-100 mb-6 group-hover:rotate-12 transition-transform">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <h3 id="modalItemName" class="text-2xl font-black text-slate-900 uppercase tracking-tighter italic">Ghi nhận hư tổn</h3>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2 border-t border-slate-50 pt-4 flex items-center justify-center gap-2 max-w-[300px] mx-auto leading-relaxed">
                <i class="fa-solid fa-info-circle text-indigo-400"></i>
                Vui lòng nhập chi tiết hư tổn của vật tư này để bộ phận quản trị xử lý.
            </p>
        </div>
        
        <form action="<?= \App\Core\url('/admin/housekeeping/report-damage') ?>" method="POST" class="space-y-8 relative z-10">
            <?= csrf_field() ?>
            <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
            <input type="hidden" name="booking_detail_id" value="<?= $activeBooking['booking_detail_id'] ?? '' ?>">
            <input type="hidden" name="item_id" id="modalItemId">
            
            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Số lượng hư hại / Mất mát <span class="text-rose-500 font-bold">*</span></label>
                <div class="relative">
                    <i class="fa-solid fa-clipboard-list absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs italic"></i>
                    <input type="number" name="quantity" class="w-full pl-14 pr-6 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-black text-slate-800 text-lg placeholder:font-medium" value="1" min="1" required>
                </div>
            </div>

            <div class="space-y-3 relative">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic group-hover:text-indigo-400 transition-colors">Ghi chú chi tiết hiện trạng</label>
                <textarea name="notes" class="w-full px-6 py-5 rounded-[2rem] border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none min-h-[120px] text-slate-600 text-sm font-medium leading-relaxed resize-none" placeholder="VD: Bị nứt bề mặt, mất do khách làm đổ, cần sửa chữa gấp..." required></textarea>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Trạng thái phòng sau báo cáo</label>
                <div class="relative">
                    <select name="next_status" class="w-full appearance-none pl-6 pr-12 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-widest">
                        <option value="">Không thay đổi (Giữ nguyên)</option>
                        <option value="<?= \App\Models\Room::STATUS_MAINTENANCE ?>">Bảo trì (Maintenance)</option>
                        <option value="<?= \App\Models\Room::STATUS_CLEANING ?>">Đang dọn dẹp (Cleaning)</option>
                        <option value="<?= \App\Models\Room::STATUS_AVAILABLE ?>">Sẵn sàng (Available)</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            <div class="flex items-center gap-5 pt-4">
                <button type="button" onclick="closeReportModal()" class="flex-1 py-5 bg-slate-50 hover:bg-slate-100 text-slate-500 font-black rounded-3xl transition-all text-center uppercase text-[10px] tracking-widest border border-slate-100 active:scale-95">Hủy bỏ</button>
                <button type="submit" class="flex-[2] py-5 bg-rose-600 hover:bg-rose-500 text-white font-black rounded-3xl transition-all shadow-2xl shadow-rose-200 flex items-center justify-center gap-3 active:scale-95 uppercase text-[11px] tracking-widest leading-none">
                    Gửi báo cáo <i class="fa-solid fa-paper-plane text-[10px]"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openReportModal(id, name) {
    document.getElementById('modalItemId').value = id;
    document.getElementById('modalItemName').innerText = 'Hư tổn: ' + name;
    const modal = document.getElementById('reportModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeReportModal() {
    const modal = document.getElementById('reportModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function updateRoomStatus(roomId) {
    const status = document.getElementById('roomStatusSelect').value;
    const btn = document.getElementById('btnUpdateStatus');
    const feedback = document.getElementById('statusUpdateFeedback');
    
    btn.disabled = true;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-sm"></i> Đang cập nhật...';
    
    const formData = new FormData();
    formData.append('status', status);
    formData.append('ajax', '1');
    
    fetch(`<?= \App\Core\url('/admin/housekeeping/update-status/') ?>${roomId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        
        feedback.classList.remove('hidden');
        feedback.classList.add('bg-emerald-50', 'text-emerald-600', 'border', 'border-emerald-100');
        feedback.innerHTML = '<i class="fa-solid fa-check-circle mr-2"></i> Trạng thái đã được cập nhật thành công!';
        
        setTimeout(() => {
            feedback.classList.add('hidden');
        }, 3000);
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        alert('Có lỗi xảy ra khi cập nhật trạng thái hệ thống.');
    });
}

// Close modal on click outside
window.onclick = function(event) {
    const modal = document.getElementById('reportModal');
    if (event.target == modal) closeReportModal();
}
</script>


