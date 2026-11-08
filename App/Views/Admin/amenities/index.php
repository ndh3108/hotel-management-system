<?php
$amenities = $amenities ?? [];
$totalAmenities = count($amenities);
?>

<div class="space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-gem"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Tiện ích & Đặc quyền</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Managing Premium Hotel Assets & Guest Perks</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center gap-3 px-4 py-2 bg-indigo-50 rounded-2xl border border-indigo-100">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest leading-none"><?= $totalAmenities ?> Registered Amenities</span>
            </div>
            <a href="<?= \App\Core\url('/admin/amenities/create') ?>" class="px-8 py-4 rounded-2xl bg-slate-900 text-white font-black hover:bg-indigo-600 transition-all flex items-center gap-3 shadow-2xl shadow-indigo-100 active:scale-95 text-xs uppercase tracking-widest italic">
                <i class="fa-solid fa-plus-circle text-lg"></i> Thiết lập tiện ích
            </a>
        </div>
    </div>

    <!-- Features Overview Stats (Optional small banners) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:border-indigo-200 transition-all duration-500">
            <div class="w-16 h-16 bg-slate-50 text-slate-900 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-slate-900 group-hover:text-white transition-all shadow-inner border border-slate-50">
                <i class="fa-solid fa-shield-heart"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Status Active</p>
                <h4 class="text-lg font-black text-slate-900 uppercase italic">Kiểm soát 100%</h4>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:border-indigo-200 transition-all duration-500">
            <div class="w-16 h-16 bg-slate-50 text-slate-900 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-inner border border-slate-50">
                <i class="fa-solid fa-icons"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Visual Library</p>
                <h4 class="text-lg font-black text-slate-900 uppercase italic">Iconography Kit</h4>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:border-indigo-200 transition-all duration-500">
            <div class="w-16 h-16 bg-slate-50 text-slate-900 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-inner border border-slate-50">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Categorization</p>
                <h4 class="text-lg font-black text-slate-900 uppercase italic">Đồng bộ Room Type</h4>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-12 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
            <div>
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">Danh mục tiện ích chuẩn</h2>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2 italic">Quản lý các trang thiết bị và dịch vụ đi kèm trong phòng nghỉ</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/30">
                        <th class="px-12 py-8 text-left italic">Thông tin tiện ích</th>
                        <th class="px-8 py-8 text-left italic">Icon định danh</th>
                        <th class="px-8 py-8 text-left italic">Mô tả đặc tính</th>
                        <th class="px-12 py-8 text-right italic">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($amenities)): ?>
                        <tr>
                            <td colspan="4" class="px-12 py-40 text-center bg-slate-50/30">
                                <div class="w-24 h-24 bg-slate-100 rounded-[2.5rem] flex items-center justify-center text-slate-300 mx-auto mb-8 shadow-inner italic font-black text-4xl">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>
                                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-widest italic">Kho dữ liệu trống</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-3 italic">Hệ thống chưa ghi nhận tiện ích nào</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($amenities as $a): ?>
                            <tr class="hover:bg-slate-50/80 transition-all group duration-300">
                                <td class="px-12 py-8">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-xl shadow-slate-200 group-hover:rotate-12 transition-transform duration-500 italic">
                                            <i class="fa-solid <?= htmlspecialchars($a['icon_url'] ?? 'fa-sparkles') ?> text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900 text-lg uppercase tracking-tight leading-tight italic"><?= htmlspecialchars($a['name']) ?></p>
                                            <p class="text-[9px] font-black text-indigo-500 uppercase tracking-[0.3em] mt-2 flex items-center gap-2 italic">
                                                <span class="w-4 h-[1px] bg-indigo-200"></span>
                                                AMN-<?= str_pad((string)$a['id'], 3, '0', STR_PAD_LEFT) ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-8">
                                    <div class="px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl inline-flex items-center gap-3 group-hover:bg-white group-hover:border-indigo-200 transition-all">
                                        <code class="text-[10px] font-black text-slate-600"><?= htmlspecialchars($a['icon_url'] ?? 'fa-solid fa-check') ?></code>
                                        <i class="fa-solid fa-code text-slate-300 text-[8px]"></i>
                                    </div>
                                </td>
                                <td class="px-8 py-8">
                                    <p class="text-[10px] font-medium text-slate-400 line-clamp-1 max-w-[300px] italic"><?= htmlspecialchars($a['description'] ?? 'Không có mô tả chi tiết cho tiện ích này...') ?></p>
                                </td>
                                <td class="px-12 py-8 text-right">
                                    <div class="flex items-center justify-end gap-3 translate-x-4 group-hover:translate-x-0 transition-all duration-500">
                                        <a href="<?= \App\Core\url('/admin/amenities/' . (int) $a['id'] . '/edit') ?>" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-90">
                                            <i class="fa-solid fa-pen-nib text-sm"></i>
                                        </a>
                                        <form action="<?= \App\Core\url('/admin/amenities/' . (int) $a['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Toàn bộ liên kết với các loại phòng sẽ bị xóa. Tiếp tục?');" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm active:scale-90">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if (($totalPages ?? 1) > 1): ?>
        <div class="flex items-center justify-between bg-white px-12 py-10 rounded-[3.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] italic">
                Hiển thị <span class="text-slate-900 px-2 py-1 bg-slate-50 border border-slate-100 rounded-lg mx-2"><?= (int)($pageNumber ?? 1) ?></span> / <?= (int)($totalPages ?? 1) ?> trang
            </p>
                <a href="<?= \App\Core\url('/admin/amenities?page=1') ?>" class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-900 hover:text-white transition-all text-xs">
                    <i class="fa-solid fa-angles-left"></i>
                </a>
                
                <?php foreach (\App\Core\pagination_range((int) ($pageNumber ?? 1), (int) ($totalPages ?? 1)) as $pageItem): ?>
                    <?php if ($pageItem === '...'): ?>
                        <span class="w-10 h-10 flex items-center justify-center text-slate-300 font-bold">...</span>
                    <?php else: ?>
                        <a href="<?= \App\Core\url('/admin/amenities?page=' . (int) $pageItem) ?>" class="w-10 h-10 rounded-xl font-black text-[11px] flex items-center justify-center transition-all <?= (int) $pageItem === (int) ($pageNumber ?? 1) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white border border-slate-100 text-slate-400 hover:bg-slate-50' ?>">
                            <?= (int) $pageItem ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>

                <a href="<?= \App\Core\url('/admin/amenities?page=' . (int) ($totalPages ?? 1)) ?>" class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-900 hover:text-white transition-all text-xs">
                    <i class="fa-solid fa-angles-right"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>


