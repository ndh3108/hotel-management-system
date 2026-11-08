<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-indigo-100 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Thiết lập định mức vật tư</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Inventory Quota & Loss Policy Management</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= \App\Core\url('/admin/inventory/create') ?>" class="px-6 py-3.5 rounded-2xl bg-indigo-600 text-white font-black hover:bg-indigo-500 transition-all flex items-center gap-3 shadow-2xl shadow-indigo-100 text-[10px] uppercase tracking-widest active:scale-95 group">
                <i class="fa-solid fa-plus-circle text-xs group-hover:rotate-90 transition-transform"></i> Khởi tạo định mức
            </a>
        </div>
    </div>

    <!-- Operational Insights -->
    <div class="bg-indigo-50/50 border border-indigo-100 p-8 rounded-[2.5rem] flex items-center gap-6 group hover:bg-white hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-500">
        <div class="w-16 h-16 rounded-[1.5rem] bg-indigo-600 text-white flex items-center justify-center text-2xl shadow-xl shadow-indigo-100 group-hover:rotate-12 transition-transform">
            <i class="fa-solid fa-circle-info"></i>
        </div>
        <div>
            <h4 class="text-sm font-black text-indigo-900 uppercase tracking-tight italic mb-1">Cơ chế áp dụng định mức</h4>
            <p class="text-[11px] font-black text-indigo-700/60 leading-relaxed uppercase tracking-widest italic">
                Định mức vật tư xác định số lượng mặc định và giá đền bù cho từng phòng. 
                <span class="text-indigo-900">Thiết lập Phòng cụ thể sẽ được ưu tiên cao hơn thiết lập theo Loại phòng.</span>
            </p>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="pl-10 pr-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Identity</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Vật tư & Base Rate</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Đối tượng</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Định mức</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-right">Giá đền bù</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Trạng thái</th>
                        <th class="pl-6 pr-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (!empty($inventories)): ?>
                        <?php foreach ($inventories as $inv): ?>
                            <tr class="hover:bg-slate-50/50 transition-all duration-300 group/row">
                                <td class="pl-10 pr-6 py-8">
                                    <span class="text-sm font-black text-slate-900 uppercase italic">#<?= $inv['id'] ?></span>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-800 uppercase italic leading-tight mb-1"><?= htmlspecialchars($inv['item_name']) ?></span>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">BASE: <?= number_format($inv['default_price'], 0, ',', '.') ?>₫</span>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center text-[10px] font-black italic">
                                    <div class="flex justify-center">
                                        <?php if($inv['room_number']): ?>
                                            <span class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl border border-indigo-100 uppercase tracking-widest animate-fade-in group-hover/row:scale-110 transition-transform">
                                                PHÒNG <?= htmlspecialchars($inv['room_number']) ?>
                                            </span>
                                        <?php elseif($inv['room_type_name']): ?>
                                            <span class="px-4 py-2 bg-slate-50 text-slate-500 rounded-xl border border-slate-100 uppercase tracking-widest">
                                                LOẠI: <?= htmlspecialchars($inv['room_type_name']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-slate-300 italic opacity-50 uppercase tracking-widest">Global Default</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <span class="inline-flex items-center px-5 py-2 rounded-2xl bg-slate-900 text-white text-[11px] font-black italic shadow-xl shadow-slate-200">
                                        <?= $inv['quantity'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-8 text-right">
                                    <?php if($inv['price_override']): ?>
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="text-base font-black text-rose-600 italic tracking-tighter"><?= number_format($inv['price_override'], 0, ',', '.') ?>₫</span>
                                            <span class="text-[8px] font-black text-rose-400 uppercase tracking-widest italic flex items-center gap-1">
                                                <i class="fa-solid fa-triangle-exclamation"></i> OVERRIDDEN
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex flex-col items-end">
                                            <span class="text-base font-black text-emerald-600 italic tracking-tighter"><?= number_format($inv['default_price'], 0, ',', '.') ?>₫</span>
                                            <span class="text-[8px] font-black text-emerald-400 uppercase tracking-widest italic">STANDARD RATE</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-8 text-center text-center">
                                    <?php if($inv['is_active']): ?>
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-100 text-[9px] font-black uppercase tracking-widest italic animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full mr-2 bg-emerald-500"></span> ACTIVE
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-2xl bg-slate-50 text-slate-400 border border-slate-100 text-[9px] font-black uppercase tracking-widest italic">
                                            DISABLED
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="pl-6 pr-10 py-8 text-right">
                                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover/row:opacity-100 transition-all duration-300 translate-x-4 group-hover/row:translate-x-0">
                                        <a href="<?= \App\Core\url('/admin/inventory/' . $inv['id'] . '/edit') ?>" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 text-slate-400 rounded-2xl hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-95 group/edit">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <form action="<?= \App\Core\url('/admin/inventory/' . $inv['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Xác nhận xóa bỏ định mức vật tư này? Thao tác này không thể hoàn tác.');">
                                            <button type="submit" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 text-slate-400 rounded-2xl hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm active:scale-95 group/del">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-32 text-center text-center">
                                <div class="flex flex-col items-center gap-6">
                                    <div class="w-24 h-24 bg-slate-50 border border-slate-50 rounded-[2rem] flex items-center justify-center text-slate-100 text-4xl shadow-inner animate-pulse">
                                        <i class="fa-solid fa-box-open"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] italic">No inventory quotas defined</p>
                                        <p class="text-slate-300 text-[9px] font-bold italic uppercase tracking-widest">Click initialize to start configuration</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Suite -->
    <div class="flex items-center justify-between bg-white px-10 py-8 rounded-[3rem] shadow-2xl shadow-slate-200/20 border border-slate-100">
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Inventory Page <span class="text-indigo-600 ml-2">#<?= $pageNumber ?></span></p>
        </div>
        <div class="flex items-center gap-3">
            <?php if ($pageNumber > 1): ?>
                <a class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-95" href="<?= \App\Core\url('/admin/inventory?page=' . ($pageNumber - 1)) ?>">
                    <i class="fa-solid fa-angle-left text-xs"></i>
                </a>
            <?php endif; ?>
            
            <div class="px-8 py-3 bg-slate-50 border border-slate-100 rounded-2xl font-black text-[11px] uppercase tracking-widest text-slate-900 italic shadow-inner">
                Trang <?= $pageNumber ?>
            </div>
            
            <?php if ($hasMore): ?>
                <a class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-95" href="<?= \App\Core\url('/admin/inventory?page=' . ($pageNumber + 1) . '&cursor=' . $nextCursor) ?>">
                    <i class="fa-solid fa-angle-right text-xs"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

