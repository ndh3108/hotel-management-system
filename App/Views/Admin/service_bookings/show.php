<?php use App\Core\StatusLabel; ?>

<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header & Breadcrumbs -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="<?= \App\Core\url('/admin/service-bookings') ?>" class="w-12 h-12 flex items-center justify-center bg-white border border-slate-200 rounded-2xl text-slate-400 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-95">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-100">SVC-#<?= (int)$booking['id'] ?></span>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">Chi tiết yêu cầu dịch vụ</h3>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1.5 px-1 italic">Service Dispatch Ledger • <?= date('d M, Y H:i', strtotime((string)$booking['created_at'])) ?></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
             <a href="<?= \App\Core\url('/admin/service-bookings/' . (int)$booking['id'] . '/bill') ?>" target="_blank" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl hover:bg-indigo-700 transition-all shadow-lg active:scale-95 flex items-center gap-3 font-black text-[10px] uppercase tracking-widest italic">
                 <i class="fa-solid fa-eye"></i>
                 Xem hóa đơn
             </a>
             <a href="<?= \App\Core\url('/admin/service-bookings/' . (int)$booking['id'] . '/invoice-pdf') ?>" target="_blank" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-slate-600 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-95 flex items-center gap-3 font-black text-[10px] uppercase tracking-widest italic group">
                 <i class="fa-solid fa-file-pdf text-rose-500 group-hover:text-white transition-colors"></i>
                 Xuất Invoice PDF
             </a>
             <div class="px-5 py-2.5 bg-slate-50 border border-slate-100 rounded-2xl">
                 <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">Hệ thống ghi nhận lúc: <?= date('H:i:s', strtotime((string)$booking['created_at'])) ?></span>
             </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
        <!-- Main Content Area (8/12) -->
        <div class="xl:col-span-8 space-y-10">
            <!-- Order & Guest Profile -->
            <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden group">
                <div class="p-10 border-b border-slate-50 flex items-center gap-8 bg-slate-50/20">
                    <div class="w-20 h-20 rounded-[2rem] bg-indigo-600 text-white flex items-center justify-center text-3xl shadow-xl shadow-indigo-200 group-hover:rotate-6 transition-transform duration-500">
                        <i class="fa-solid fa-user-tag italic"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-1 italic">Hồ sơ khách hàng sử dụng</p>
                        <h3 class="text-3xl font-black text-slate-800 uppercase tracking-tighter italic leading-none mb-3"><?= htmlspecialchars((string)($booking['guest_name'] ?? 'Guest Profile')) ?></h3>
                        <div class="flex items-center gap-8">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 italic">
                                <i class="fa-solid fa-phone text-indigo-400"></i>
                                <?= htmlspecialchars((string)($booking['guest_phone'] ?? 'Contact Missing')) ?>
                            </span>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 italic">
                                <i class="fa-solid fa-envelope text-indigo-400"></i>
                                <?= htmlspecialchars((string)($booking['guest_email'] ?? 'No Email')) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div class="space-y-10">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-1 bg-indigo-500 rounded-full"></span>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Thông tin dịch vụ</p>
                            </div>
                            <div class="p-8 rounded-[2.5rem] bg-slate-50 border border-slate-100 group/item hover:bg-white hover:shadow-2xl hover:shadow-slate-100 transition-all duration-500">
                                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-indigo-600 mb-4 group-hover/item:scale-110 transition-transform">
                                    <i class="fa-solid fa-concierge-bell"></i>
                                </div>
                                <h4 class="text-xl font-black text-slate-800 uppercase tracking-tight italic mb-1"><?= htmlspecialchars((string)($booking['service_name'] ?? 'Service Name')) ?></h4>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Hệ thống dịch vụ giờ lẻ (Hourly)</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-1 bg-indigo-500 rounded-full"></span>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Timeline thực thi</p>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <div class="flex items-center gap-6 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-emerald-500 shadow-sm shrink-0">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Thời gian bắt đầu</p>
                                        <p class="text-sm font-black text-slate-800 uppercase italic"><?= date('H:i • d F, Y', strtotime((string)$booking['check_in'])) ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-rose-500 shadow-sm shrink-0">
                                        <i class="fa-solid fa-hourglass-end"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Thời gian hoàn tất</p>
                                        <p class="text-sm font-black text-slate-800 uppercase italic"><?= date('H:i • d F, Y', strtotime((string)$booking['check_out'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-10">
                         <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-1 bg-indigo-900 rounded-full"></span>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Giá trị hạch toán</p>
                            </div>
                            <div class="p-8 rounded-[2.5rem] bg-indigo-900 shadow-2xl shadow-indigo-100 relative overflow-hidden group/price">
                                <i class="fa-solid fa-receipt absolute -right-6 -bottom-6 text-9xl text-white opacity-5 rotate-12 group-hover/price:scale-125 transition-transform duration-700"></i>
                                <div class="relative z-10 space-y-6">
                                    <div class="flex items-center justify-between">
                                        <span class="px-4 py-1.5 bg-indigo-800 text-indigo-300 rounded-xl text-[10px] font-black uppercase tracking-widest">Hóa đơn dịch vụ</span>
                                        <span class="text-[10px] font-black text-white/40 uppercase tracking-widest italic">Unit: <?= (int)($booking['quantity'] ?? 1) ?> PX</span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1 italic">Tổng giá trị thanh toán</p>
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-4xl font-black text-white italic tracking-tighter"><?= number_format((float)($booking['total_price'] ?? 0), 0, ',', '.') ?>₫</span>
                                            <span class="text-[10px] font-black text-white/30 uppercase tracking-widest italic">Final Price</span>
                                        </div>
                                    </div>
                                    <div class="pt-4 border-t border-white/10">
                                        <p class="text-[9px] font-black text-white/40 uppercase tracking-widest italic mb-1">Đơn giá niêm yết</p>
                                        <p class="text-sm font-black text-indigo-300 italic"><?= number_format((float)($booking['price_per_hour'] ?? 0), 0, ',', '.') ?>₫ <span class="text-[10px] font-bold text-white/20">/ Hr per Unit</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Special Instructions -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-1 bg-slate-200 rounded-full"></span>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Ghi chú vận hành</p>
                            </div>
                            <div class="p-8 rounded-[2rem] bg-slate-50 border border-slate-100 italic min-h-[160px]">
                                <i class="fa-solid fa-quote-left text-slate-200 text-2xl mb-4 block"></i>
                                <p class="text-slate-500 text-sm leading-loose">
                                    <?= !empty($booking['notes']) ? htmlspecialchars((string)$booking['notes']) : "Không có yêu cầu đặc biệt từ khách hàng. Vui lòng chuẩn bị dịch vụ đúng thời gian đã hẹn và đảm bảo chất lượng tiêu chuẩn." ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions (4/12) -->
        <div class="xl:col-span-4 space-y-10">
            <!-- Global Status Dispatch -->
            <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-10 space-y-10 relative overflow-hidden group/status">
                <div class="absolute top-0 right-0 w-2 h-full bg-indigo-600 transition-all duration-700 group-hover/status:w-4"></div>
                <div>
                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter italic">Status Dispatch</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1 italic">Trung tâm vận hành dịch vụ</p>
                </div>

                <div class="space-y-8">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Trạng thái hiện tại</p>
                        <?php 
                            $statusMeta = \App\Core\StatusLabel::serviceBooking((string) ($booking['status'] ?? '')); 
                            $badgeClass = match(strtolower((string)($booking['status'] ?? ''))) {
                                'requested' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'reserved'  => 'bg-sky-50 text-sky-700 border-sky-100',
                                'active'    => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'completed' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                'cancelled', 'expired' => 'bg-slate-50 text-slate-500 border-slate-100',
                                default => 'bg-slate-50 text-slate-700 border-slate-100'
                            };
                        ?>
                        <span class="inline-flex items-center px-6 py-3 rounded-2xl border text-[10px] font-black uppercase tracking-widest shadow-sm <?= $badgeClass ?>">
                            <span class="w-2 h-2 rounded-full mr-3 bg-current animate-pulse"></span>
                            <?= htmlspecialchars((string) $statusMeta['label']) ?>
                        </span>
                    </div>

                    <form action="<?= \App\Core\url('/admin/service-bookings/' . (int)$booking['id'] . '/status') ?>" method="POST" class="space-y-6 pt-10 border-t border-slate-50">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic underline underline-offset-4 decoration-indigo-200">Luân chuyển trạng thái</label>
                            <div class="relative">
                                <select name="status" class="w-full appearance-none px-6 py-4 rounded-[1.5rem] border border-slate-100 bg-slate-50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner cursor-pointer" required>
                                    <option value="requested" <?= strtolower((string)($booking['status'] ?? '')) === 'requested' ? 'selected' : '' ?>>REQUESTED (YÊU CẦU)</option>
                                    <option value="reserved" <?= strtolower((string)($booking['status'] ?? '')) === 'reserved' ? 'selected' : '' ?>>RESERVED (ĐÃ ĐẶT)</option>
                                    <option value="active" <?= strtolower((string)($booking['status'] ?? '')) === 'active' ? 'selected' : '' ?>>ACTIVE (ĐANG SỬ DỤNG)</option>
                                    <option value="completed" <?= strtolower((string)($booking['status'] ?? '')) === 'completed' ? 'selected' : '' ?>>COMPLETED (HOÀN TẤT)</option>
                                    <option value="cancelled" <?= strtolower((string)($booking['status'] ?? '')) === 'cancelled' ? 'selected' : '' ?>>CANCELLED (HỦY BỎ)</option>
                                    <option value="expired" <?= strtolower((string)($booking['status'] ?? '')) === 'expired' ? 'selected' : '' ?>>EXPIRED (HẾT HẠN)</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                            </div>
                        </div>
                        <button type="submit" class="w-full py-5 bg-indigo-600 text-white font-black rounded-[1.5rem] hover:bg-slate-900 transition-all shadow-2xl shadow-indigo-100 flex items-center justify-center gap-3 active:scale-95 uppercase text-xs tracking-widest italic group">
                            <i class="fa-solid fa-rotate text-sm group-hover:rotate-180 transition-transform duration-700"></i> Synchronize Lifecycle
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Contextual Actions -->
            <div class="bg-slate-900 rounded-[3rem] p-10 text-white space-y-6 relative overflow-hidden">
                <i class="fa-solid fa-headset absolute -right-10 -bottom-10 text-9xl text-white opacity-5 rotate-12"></i>
                <h4 class="text-xs font-black uppercase tracking-widest text-slate-500 flex items-center gap-2 italic">
                    <i class="fa-solid fa-bolt text-indigo-400"></i> Hỗ trợ vận hành nhanh
                </h4>
                <div class="space-y-4 relative z-10">
                    <a href="tel:<?= htmlspecialchars((string)($booking['guest_phone'] ?? '')) ?>" class="flex items-center justify-between p-6 bg-white/5 hover:bg-white/10 rounded-[1.5rem] transition-all border border-white/5 group">
                        <div class="space-y-1">
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Call Primary Guest</p>
                            <p class="text-xs font-bold text-white uppercase italic"><?= htmlspecialchars((string)($booking['guest_phone'] ?? 'N/A')) ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-900 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-phone text-xs"></i>
                        </div>
                    </a>
                     <a href="mailto:<?= htmlspecialchars((string)($booking['guest_email'] ?? '')) ?>" class="flex items-center justify-between p-6 bg-white/5 hover:bg-white/10 rounded-[1.5rem] transition-all border border-white/5 group">
                         <div class="space-y-1">
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Send Direct Quote</p>
                            <p class="text-xs font-bold text-white uppercase italic max-w-[150px] truncate"><?= htmlspecialchars((string)($booking['guest_email'] ?? 'N/A')) ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-white/10 text-indigo-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

