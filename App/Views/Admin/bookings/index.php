<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-indigo-100 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Quản trị vận hành đặt phòng</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Guest Reservation Ledger & Revenue Tracking</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
             <div class="px-5 py-2.5 bg-slate-50 border border-slate-100 rounded-2xl">
                 <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic group-hover:text-indigo-600 transition-colors">Hệ thống đồng bộ lúc: <?= date('H:i:s') ?></span>
             </div>
        </div>
    </div>

    <!-- Analytics Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500">
            <div class="w-16 h-16 rounded-[1.5rem] bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500 shadow-sm shadow-indigo-50">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Tổng lượt đặt</p>
                <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic leading-none"><?= number_format(count($bookings)) ?></h3>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500">
            <div class="w-16 h-16 rounded-[1.5rem] bg-amber-50 text-amber-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all duration-500 shadow-sm shadow-amber-50">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Chờ thanh toán</p>
                <h3 class="text-3xl font-black text-amber-600 tracking-tighter italic leading-none">0</h3>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500">
            <div class="w-16 h-16 rounded-[1.5rem] bg-sky-50 text-sky-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-sky-600 group-hover:text-white transition-all duration-500 shadow-sm shadow-sky-50">
                <i class="fa-solid fa-house-chimney-user"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Đang cư trú</p>
                <h3 class="text-3xl font-black text-indigo-600 tracking-tighter italic leading-none">0</h3>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500">
            <div class="w-16 h-16 rounded-[1.5rem] bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500 shadow-sm shadow-emerald-50">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Hoàn thành</p>
                <h3 class="text-3xl font-black text-emerald-600 tracking-tighter italic leading-none">0</h3>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <!-- Advanced Filter Suite -->
        <div class="p-10 bg-slate-50/20 border-b border-slate-50">
            <form method="GET" action="<?= \App\Core\url('/admin/bookings') ?>" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end">
                <div class="lg:col-span-4 space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic underline underline-offset-4 decoration-indigo-200">Truy vấn định danh</label>
                    <div class="relative">
                        <input type="text" name="keyword" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" 
                               class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-bold text-slate-700 shadow-inner"
                               placeholder="Tên khách, Email, Mã đặt phòng...">
                        <i class="fa-solid fa-magnifying-glass absolute right-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    </div>
                </div>
                <div class="lg:col-span-3 space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic underline underline-offset-4 decoration-indigo-200">Trạng thái vận hành</label>
                    <div class="relative">
                        <select name="status" class="w-full appearance-none px-6 py-4 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-[11px] uppercase tracking-tight shadow-inner cursor-pointer">
                            <option value="">TẤT CẢ TRẠNG THÁI</option>
                            <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>PENDING (CHỜ XÁC NHẬN)</option>
                            <option value="paid" <?= ($filters['status'] ?? '') === 'paid' ? 'selected' : '' ?>>PAID (ĐÃ THANH TOÁN)</option>
                            <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>COMPLETED (HOÀN TẤT)</option>
                            <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>CANCELLED (HỦY BỎ)</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 text-[10px]"></i>
                    </div>
                </div>
                <div class="lg:col-span-3 space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic underline underline-offset-4 decoration-indigo-200">Ngày lưu trú</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($filters['date'] ?? '') ?>" 
                           class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700 shadow-inner">
                </div>
                <div class="lg:col-span-2 flex gap-3">
                    <button type="submit" class="flex-1 bg-slate-900 text-white font-black py-4 rounded-2xl hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3 active:scale-95 text-[10px] uppercase tracking-widest italic group">
                        <i class="fa-solid fa-filter text-xs group-hover:rotate-180 transition-transform duration-500"></i> Dispatch
                    </button>
                    <a href="<?= \App\Core\url('/admin/bookings') ?>" class="px-5 py-4 bg-white border border-slate-200 text-slate-400 font-black rounded-2xl hover:bg-slate-50 transition-all flex items-center justify-center" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Ledger Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="pl-10 pr-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Resv Identity</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Guest Profile</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Timeline</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Resv Status</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Payment</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-right">Accounting Value</th>
                        <th class="pl-6 pr-10 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Operational Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $booking): ?>
                            <?php
                            $status = strtolower($booking['status'] ?? 'pending');
                            $statusBadge = match($status) {
                                'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'paid' => 'bg-sky-50 text-sky-700 border-sky-100',
                                'completed' => 'bg-indigo-50 text-indigo-700 border-indigo-100 shadow-sm shadow-indigo-50',
                                'cancelled' => 'bg-slate-50 text-slate-500 border-slate-100',
                                default => 'bg-slate-50 text-slate-700 border-slate-100'
                            };
                            ?>
                            <tr class="hover:bg-slate-50/50 transition-all duration-300 group/row">
                                <td class="pl-10 pr-6 py-8">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-900 uppercase italic">#<?= (int) $booking['id'] ?></span>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1 italic opacity-60 group-hover/row:opacity-100 transition-opacity">BKG-MDS7-<?= (int)$booking['id'] ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black group-hover/row:scale-110 transition-transform italic">
                                            <?= strtoupper(substr($booking['guest_name'] ?? 'G', 0, 1)) ?>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-slate-800 uppercase italic leading-tight mb-1"><?= htmlspecialchars($booking['guest_name'] ?? 'Guest Profile') ?></span>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic"><?= htmlspecialchars($booking['email'] ?? 'admin@hotel.com') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center text-[10px] font-black text-slate-500 italic">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="text-slate-800"><?= date('d F', strtotime($booking['check_in'] ?? 'now')) ?></div>
                                        <i class="fa-solid fa-arrow-right text-[8px] text-slate-300 group-hover/row:translate-x-1 transition-transform"></i>
                                        <div class="text-slate-400"><?= date('d F', strtotime($booking['check_out'] ?? 'now')) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <div class="flex justify-center">
                                        <span class="px-5 py-2 rounded-2xl border text-[9px] font-black uppercase tracking-widest <?= $statusBadge ?> italic">
                                            <span class="w-1.5 h-1.5 rounded-full mr-2.5 bg-current animate-pulse opacity-70"></span>
                                            <?= htmlspecialchars(strtoupper($status)) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <div class="flex justify-center">
                                        <span class="px-5 py-2 rounded-2xl border text-[9px] font-black uppercase tracking-widest <?= $status === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-100 shadow-sm shadow-emerald-50' : 'bg-slate-50 text-slate-400 border-slate-100' ?> italic">
                                            <?= $status === 'paid' ? 'PAID' : 'UNPAID' ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-right">
                                    <div class="flex flex-col">
                                        <span class="text-base font-black text-indigo-600 italic tracking-tighter"><?= number_format((float) ($booking['total_price'] ?? 0), 0, ',', '.') ?>₫</span>
                                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Accounting Total</span>
                                    </div>
                                </td>
                                <td class="pl-6 pr-10 py-8">
                                    <div class="flex items-center justify-center gap-3 opacity-0 group-hover/row:opacity-100 transition-all duration-300 translate-x-4 group-hover/row:translate-x-0">
                                        <a href="<?= \App\Core\url('/admin/bookings/' . $booking['id']) ?>" 
                                           class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 text-slate-400 rounded-2xl hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-95" title="View Detailed Record">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <a href="<?= \App\Core\url('/admin/bookings/' . $booking['id']) ?>" class="w-10 h-10 flex items-center justify-center bg-indigo-600 text-white rounded-2xl hover:bg-indigo-500 transition-all shadow-xl shadow-indigo-100 active:scale-95" title="Operational Dispatch">
                                            <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-32 text-center">
                                <div class="flex flex-col items-center gap-6">
                                    <div class="w-24 h-24 bg-slate-50 border border-slate-50 rounded-[2rem] flex items-center justify-center text-slate-100 text-4xl shadow-inner animate-pulse">
                                        <i class="fa-solid fa-calendar-xmark"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] italic">No active reservations detected</p>
                                        <p class="text-slate-300 text-[9px] font-bold italic uppercase tracking-widest">Adjust filters or await incoming traffic</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Ledger Manifest (Pagination) -->
        <?php if (($totalPages ?? 1) > 1): ?>
            <div class="p-10 bg-slate-50/20 border-t border-slate-50 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Ledger Manifest Page <span class="text-indigo-600 ml-2">#<?= (int) ($pageNumber ?? 1) ?> / <?= (int) ($totalPages ?? 1) ?></span></p>
                </div>
                <div class="flex gap-3">
                    <a href="<?= \App\Core\url('/admin/bookings?page=' . max(1, ($pageNumber ?? 1) - 1)) ?>" 
                       class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-[10px] font-black text-slate-400 uppercase tracking-widest hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm italic active:scale-95 <?= ($pageNumber ?? 1) <= 1 ? 'opacity-30 pointer-events-none' : '' ?>">
                        <i class="fa-solid fa-angle-left mr-2"></i> Previous
                    </a>
                    <a href="<?= \App\Core\url('/admin/bookings?page=' . min((int) ($totalPages ?? 1), ($pageNumber ?? 1) + 1)) ?>" 
                       class="px-6 py-3 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-2xl shadow-indigo-100 italic active:scale-95 <?= ($pageNumber ?? 1) >= ($totalPages ?? 1) ? 'opacity-30 pointer-events-none' : '' ?>">
                        Next <i class="fa-solid fa-angle-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

