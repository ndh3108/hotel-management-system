<?php
$roomTypes = $roomTypes ?? [];
$totalTypes = count($roomTypes);
$avgPrice = $totalTypes > 0 ? (array_sum(array_map(fn ($rt) => (float) ($rt['base_price'] ?? 0), $roomTypes)) / $totalTypes) : 0;
$policyReady = count(array_filter($roomTypes, fn ($rt) => !empty($rt['policy_cancel']) || !empty($rt['policy_check_in'])));
?>

<div class="space-y-8 animate-fade-in pb-20">
    <!-- Action Header -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-3 px-4 py-2 bg-indigo-50 rounded-2xl border border-indigo-100">
            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest leading-none"><?= $totalTypes ?> phân loại hạng phòng</span>
        </div>
        <a href="<?= \App\Core\url('/admin/room-type/create') ?>" class="px-6 py-3.5 rounded-2xl bg-indigo-600 text-white font-black hover:bg-indigo-500 transition-all flex items-center gap-2 shadow-xl shadow-indigo-100 active:scale-95 text-xs uppercase tracking-widest">
            <i class="fa-solid fa-plus"></i> Thiết lập hạng phòng
        </a>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-2xl shadow-indigo-200 relative overflow-hidden group border-none">
            <div class="absolute -right-6 -bottom-6 opacity-20 group-hover:scale-110 transition-transform rotate-12 duration-500">
                <i class="fa-solid fa-hotel text-9xl text-white"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2 px-1">Danh mục hiện có</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-5xl font-black text-white leading-none"><?= number_format($totalTypes) ?></h3>
                    <span class="text-xs font-bold text-indigo-400 uppercase tracking-widest">Hạng phòng</span>
                </div>
                <div class="mt-6 flex items-center gap-2">
                    <span class="px-2 py-1 bg-white/10 rounded-lg text-[9px] font-black text-white uppercase border border-white/10">Vận hành Active</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500">
                <i class="fa-solid fa-tag text-9xl text-slate-900"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Giá cơ sở trung bình</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-black text-slate-900 leading-none"><?= number_format($avgPrice, 0, ',', '.') ?></h3>
                    <span class="text-xs font-bold text-slate-400 tracking-widest uppercase">VNĐ / Đêm</span>
                </div>
                <div class="mt-6 flex items-center gap-2">
                    <span class="px-2 py-1 bg-slate-100 rounded-lg text-[9px] font-black text-slate-500 uppercase border border-slate-200">Thị trường High-end</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500">
                <i class="fa-solid fa-file-shield text-9xl text-slate-900"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Chính sách vận hành</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-black text-slate-900 leading-none"><?= number_format($policyReady) ?></h3>
                    <span class="text-xs font-bold text-slate-400 tracking-widest uppercase">/ <?= $totalTypes ?> Đã cấu hình</span>
                </div>
                <div class="mt-6 flex items-center gap-2">
                    <span class="px-2 py-1 bg-slate-100 rounded-lg text-[9px] font-black text-slate-500 uppercase border border-slate-200">Tuân thủ tiêu chuẩn 5 sao</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main List Section -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-10 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-slate-50/20">
            <div>
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Chi tiết danh mục</h2>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Quản lý cấu hình, giá và dịch vụ đi kèm</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" placeholder="Tìm hạng phòng..." class="pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all w-64 uppercase tracking-wider">
                    <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-[10px]"></i>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 bg-slate-50/50">
                        <th class="px-10 py-6 text-left">Phân loại & Định danh</th>
                        <th class="px-6 py-6 text-left">Tiêu chuẩn & Diện tích</th>
                        <th class="px-6 py-6 text-left">Chính sách vận hành</th>
                        <th class="px-6 py-6 text-right">Đơn giá cơ sở</th>
                        <th class="px-10 py-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (!empty($roomTypes)): ?>
                        <?php foreach ($roomTypes as $roomType): ?>
                            <tr class="hover:bg-slate-50/80 transition-all group duration-300">
                                <td class="px-10 py-8">
                                    <div class="flex items-center gap-6">
                                        <div class="w-28 h-20 rounded-2xl overflow-hidden border border-slate-200 shadow-sm relative group-hover:scale-105 transition-all duration-500">
                                            <?php if (!empty($roomType['image_url'])): ?>
                                                <img src="<?= htmlspecialchars(\App\Core\url((string) $roomType['image_url'])) ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full bg-slate-50 text-slate-200 flex items-center justify-center">
                                                    <i class="fa-solid fa-image text-2xl"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900 text-lg uppercase tracking-tight leading-tight"><?= htmlspecialchars((string) ($roomType['name'] ?? '')) ?></p>
                                            <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mt-1.5 flex items-center gap-2">
                                                <span class="w-3 h-[1px] bg-indigo-200"></span>
                                                <?= htmlspecialchars((string) ($roomType['slug'] ?? '-')) ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="space-y-4">
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-xl text-[10px] font-black border border-indigo-100 uppercase tracking-widest">
                                                <i class="fa-solid fa-user-friends mr-1.5 opacity-50"></i> <?= (int) ($roomType['capacity_adults'] ?? 0) ?> NGƯỜI LỚN
                                            </span>
                                            <span class="px-3 py-1.5 bg-rose-50 text-rose-700 rounded-xl text-[10px] font-black border border-rose-100 uppercase tracking-widest">
                                                <i class="fa-solid fa-child mr-1.5 opacity-50"></i> <?= (int) ($roomType['capacity_children'] ?? 0) ?> TRẺ EM
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                            <i class="fa-solid fa-ruler-combined text-slate-200"></i>
                                            <span>Diện tích: <span class="text-slate-700 font-black"><?= htmlspecialchars((string)($roomType['room_size'] ?: ($roomType['area_sqm'] . ' m²'))) ?></span></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-8 max-w-[300px]">
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-50 text-slate-600 text-[10px] font-black border border-slate-100 uppercase tracking-widest">
                                            <i class="fa-solid fa-bed text-slate-400"></i> <?= htmlspecialchars((string) ($roomType['bed_type'] ?: 'Standard')) ?>
                                        </div>
                                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-50 text-slate-600 text-[10px] font-black border border-slate-100 uppercase tracking-widest">
                                            <i class="fa-solid fa-calendar-check text-slate-400"></i> <?= (int) ($roomType['min_stay'] ?? 1) ?>-<?= (int) ($roomType['max_stay'] ?? 30) ?> ĐÊM
                                        </div>
                                    </div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-relaxed line-clamp-2 px-1">
                                        <?= htmlspecialchars((string) ($roomType['refund_policy'] ?: 'Chưa thiết lập chính sách hoàn hủy')) ?>
                                    </p>
                                </td>
                                <td class="px-6 py-8 text-right">
                                    <div class="text-2xl font-black text-indigo-600"><?= number_format((float) ($roomType['base_price'] ?? 0), 0, ',', '.') ?><span class="text-xs ml-1 font-bold">₫</span></div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Giá chuẩn / Đêm</p>
                                </td>
                                <td class="px-10 py-8 text-right">
                                    <div class="flex items-center justify-end gap-3 translate-x-2 group-hover:translate-x-0 transition-transform">
                                        <a href="<?= \App\Core\url('/admin/room-type/' . (int) $roomType['id'] . '/edit') ?>" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm active:scale-90">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </a>
                                        <form action="<?= \App\Core\url('/admin/room-type/' . (int) $roomType['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Toàn bộ cấu hình hạng phòng này sẽ bị xóa vĩnh viễn. Bạn chắc chắn chứ?');">
                                            <button type="submit" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm active:scale-90">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-10 py-32 text-center bg-slate-50/30">
                                <div class="w-24 h-24 bg-white shadow-xl shadow-slate-200/50 text-slate-200 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 animate-bounce transition-all">
                                    <i class="fa-solid fa-layer-group text-4xl opacity-20"></i>
                                </div>
                                <h3 class="text-2xl font-black text-slate-300 uppercase tracking-widest">Danh sách trống</h3>
                                <p class="text-slate-400 mt-2 font-bold uppercase text-[10px] tracking-[0.3em]">Hệ thống chưa ghi nhận cấu hình hạng phòng</p>
                                <a href="<?= \App\Core\url('/admin/room-type/create') ?>" class="inline-flex items-center gap-3 mt-10 px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase text-xs hover:bg-indigo-500 transition-all shadow-xl shadow-indigo-100">
                                    <i class="fa-solid fa-plus"></i> Khởi tạo ngay hạng phòng đầu tiên
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="flex items-center justify-between bg-white px-10 py-8 rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Trang <span class="text-indigo-600"><?= (int)$pageNumber ?></span> / <?= (int)$totalPages ?></p>
            <div class="flex items-center gap-3">
                <?php if ($pageNumber > 1): ?>
                    <a href="<?= \App\Core\url('/admin/room-type?page=' . ($pageNumber - 1)) ?>" class="px-8 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-600 font-black hover:bg-slate-50 transition-all transform active:scale-95 uppercase text-[10px] tracking-widest flex items-center gap-3">
                        <i class="fa-solid fa-chevron-left text-[8px]"></i> Trước
                    </a>
                <?php endif; ?>
                
                <?php if ($hasMore): ?>
                    <a href="<?= \App\Core\url('/admin/room-type?page=' . ($pageNumber + 1) . '&cursor=' . ($nextCursor ?? '')) ?>" class="px-8 py-3.5 rounded-2xl bg-indigo-600 text-white font-black hover:bg-indigo-500 transition-all transform active:scale-95 uppercase text-[10px] tracking-widest flex items-center gap-3 shadow-xl shadow-indigo-100">
                        Tiếp theo <i class="fa-solid fa-chevron-right text-[8px]"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
