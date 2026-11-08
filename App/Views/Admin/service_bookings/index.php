<?php 
use App\Core\StatusLabel; 

$totalCount = count($bookings ?? []);
$requestedCount = count(array_filter($bookings ?? [], fn($b) => ($b['status'] ?? '') === 'requested'));
$activeCount = count(array_filter($bookings ?? [], fn($b) => ($b['status'] ?? '') === 'active'));
$completedCount = count(array_filter($bookings ?? [], fn($b) => ($b['status'] ?? '') === 'completed'));
?>

<div class="space-y-10 animate-fade-in pb-20">
    <!-- Action Header -->
    <div class="flex items-center justify-between gap-6 px-4">
        <div class="flex items-center gap-3 px-4 py-2 bg-indigo-50 rounded-2xl border border-indigo-100">
            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest leading-none">Điều hành dịch vụ phụ trợ</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= \App\Core\url('/admin/service-bookings/services') ?>" class="px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-700 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all flex items-center gap-2 shadow-sm active:scale-95 text-xs uppercase tracking-widest">
                <i class="fa-solid fa-list-ul"></i> Danh mục dịch vụ
            </a>
        </div>
    </div>

    <!-- Premium Stats Dashboard -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500">
                <i class="fa-solid fa-clipboard-list text-8xl text-slate-900"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tổng lượt yêu cầu</p>
            <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic"><?= number_format($totalCount) ?></h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Toàn bộ hệ thống</span>
            </div>
        </div>
        
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform rotate-12 duration-500 text-amber-500">
                <i class="fa-solid fa-bell text-8xl"></i>
            </div>
            <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-2">Mới / Đang chờ xử lý</p>
            <h3 class="text-3xl font-black text-amber-600 tracking-tighter italic"><?= number_format($requestedCount) ?></h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span class="text-[9px] font-black text-amber-500 uppercase tracking-widest italic">Action Required</span>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform rotate-12 duration-500 text-emerald-500">
                <i class="fa-solid fa-person-running text-8xl"></i>
            </div>
            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-2">Đang trong tiến trình</p>
            <h3 class="text-3xl font-black text-emerald-600 tracking-tighter italic"><?= number_format($activeCount) ?></h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">In Progress</span>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform rotate-12 duration-500 text-indigo-500">
                <i class="fa-solid fa-check-double text-8xl"></i>
            </div>
            <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2">Đã hoàn thành</p>
            <h3 class="text-3xl font-black text-indigo-600 tracking-tighter italic"><?= number_format($completedCount) ?></h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                <span class="text-[9px] font-black text-indigo-500 uppercase tracking-widest">Archived Logs</span>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-10 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Mã đơn / Khách hàng</th>
                        <th class="px-6 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Nội dung dịch vụ</th>
                        <th class="px-6 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Thời gian thực thi</th>
                        <th class="px-6 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Thành tiền</th>
                        <th class="px-6 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Trạng thái</th>
                        <th class="px-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="6" class="px-10 py-32 text-center">
                            <div class="w-24 h-24 bg-slate-50 text-slate-200 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-inner border border-slate-100">
                                <i class="fa-solid fa-concierge-bell text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-widest">Chưa có yêu cầu nào</h3>
                            <p class="text-xs text-slate-400 mt-2 font-bold uppercase tracking-wide">Các yêu cầu từ khách lưu trú sẽ xuất hiện tại đây</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $booking): ?>
                    <tr class="hover:bg-slate-50/80 transition-all group duration-300">
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-5">
                                <div class="w-12 h-12 rounded-[1.2rem] bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xs shadow-inner border border-indigo-100 group-hover:scale-110 transition-transform">
                                    #<?= (int)$booking['id'] ?>
                                </div>
                                <div>
                                    <p class="font-black text-slate-900 uppercase text-sm tracking-tight italic"><?= htmlspecialchars((string)($booking['guest_name'] ?? 'Guest #' . $booking['user_id'])) ?></p>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic"><?= !empty($booking['booking_id']) ? 'RES-' . str_pad((string)$booking['booking_id'], 5, '0', STR_PAD_LEFT) : 'WALK-IN SERVICE' ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-8">
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-10 bg-indigo-50 group-hover:bg-indigo-500 transition-colors rounded-full"></span>
                                <p class="font-black text-slate-800 uppercase tracking-tight text-sm"><?= htmlspecialchars((string)($booking['service_name'] ?? 'Service #' . $booking['service_id'])) ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-8">
                            <div class="text-xs font-black text-slate-600 flex items-center gap-2 uppercase tracking-tight">
                                <i class="fa-solid fa-clock text-indigo-400"></i>
                                <span><?= date('d/m/Y H:i', strtotime((string)$booking['start_time'])) ?></span>
                            </div>
                            <p class="text-[9px] font-black text-slate-400 mt-1 uppercase tracking-[0.2em] italic">Dự kiến đến: <?= date('H:i', strtotime((string)$booking['end_time'])) ?></p>
                        </td>
                        <td class="px-6 py-8 text-right">
                            <span class="text-base font-black text-indigo-600 italic"><?= number_format((float)$booking['total_price'], 0, ',', '.') ?>₫</span>
                        </td>
                        <td class="px-6 py-8 text-center">
                            <?php 
                                $statusMeta = StatusLabel::serviceBooking((string) ($booking['status'] ?? '')); 
                                $badgeClass = match(strtolower((string)($booking['status'] ?? ''))) {
                                    'requested' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'reserved'  => 'bg-sky-50 text-sky-700 border-sky-100',
                                    'active'    => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'completed' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                    'cancelled', 'expired' => 'bg-slate-50 text-slate-500 border-slate-100',
                                    default => 'bg-slate-50 text-slate-700 border-slate-100'
                                };
                            ?>
                            <span class="inline-flex items-center px-5 py-2 rounded-full border text-[9px] font-black uppercase tracking-[0.2em] <?= $badgeClass ?> shadow-sm">
                                <?= htmlspecialchars((string) $statusMeta['label']) ?>
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <a href="<?= \App\Core\url('/admin/service-bookings/' . $booking['id']) ?>" class="px-6 py-3 rounded-2xl bg-white border border-slate-200 text-slate-600 font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-95">
                                Chi tiết <i class="fa-solid fa-chevron-right ml-2 text-[8px]"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="flex items-center justify-between bg-white px-10 py-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Hiển thị <span class="text-slate-900"><?= count($bookings) ?></span> bản ghi vận hành</p>
        <div class="flex items-center gap-2">
            <?php if ($pageNumber > 1): ?>
                <a href="<?= \App\Core\url('/admin/service-bookings?page=' . ($pageNumber - 1)) ?>" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-sm active:scale-90">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>
            <?php endif; ?>
            
            <span class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black text-sm border-none shadow-lg shadow-indigo-100">
                <?= $pageNumber ?>
            </span>
            
            <?php if ($hasMore): ?>
                <a href="<?= \App\Core\url('/admin/service-bookings?page=' . ($pageNumber + 1) . '&cursor=' . $nextCursor) ?>" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-sm active:scale-90">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>


