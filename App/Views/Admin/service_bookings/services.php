<?php
$services = $services ?? [];
$totalServices = count($services);
$avgPrice = $totalServices > 0 ? (array_sum(array_map(fn($s) => (float)($s['price_per_hour'] ?? 0), $services)) / $totalServices) : 0;
?>

<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-indigo-100 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-list-ul"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Danh mục dịch vụ lưu trú</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Hotel Service Catalog & Rate Management</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= \App\Core\url('/admin/service-bookings') ?>" class="px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-400 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all flex items-center gap-3 shadow-sm text-[10px] uppercase tracking-widest active:scale-95">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
            <a href="<?= \App\Core\url('/admin/services/create') ?>" class="px-6 py-3.5 rounded-2xl bg-indigo-600 text-white font-black hover:bg-indigo-500 transition-all flex items-center gap-3 shadow-2xl shadow-indigo-100 text-[10px] uppercase tracking-widest active:scale-95">
                <i class="fa-solid fa-plus text-xs"></i> Khởi tạo dịch vụ
            </a>
        </div>
    </div>

    <!-- Analytics Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500">
            <div class="w-16 h-16 rounded-[1.5rem] bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500 shadow-sm shadow-indigo-50">
                <i class="fa-solid fa-hand-sparkles"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Catalog Size</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic leading-none"><?= (int)$totalServices ?></h3>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Services</span>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500">
            <div class="w-16 h-16 rounded-[1.5rem] bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500 shadow-sm shadow-emerald-50">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Base Avg Rate</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic leading-none"><?= number_format((float)$avgPrice, 0, ',', '.') ?>₫</h3>
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest italic">/ Hr</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500">
            <div class="w-16 h-16 rounded-[1.5rem] bg-sky-50 text-sky-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-sky-600 group-hover:text-white transition-all duration-500 shadow-sm shadow-sky-50">
                <i class="fa-solid fa-shapes"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Active Clusters</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic leading-none"><?= count(array_unique(array_column($services, 'type_name'))) ?></h3>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Types</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Catalog Table Section -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-10 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Service Definition</th>
                        <th class="px-6 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Category</th>
                        <th class="px-6 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Operational Specs</th>
                        <th class="px-6 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Rate / Hr</th>
                        <th class="px-6 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Dispatch Status</th>
                        <th class="px-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Management</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                <?php if (empty($services)): ?>
                    <tr>
                        <td colspan="6" class="px-10 py-32 text-center">
                            <div class="w-24 h-24 bg-slate-50 text-slate-100 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-inner animate-pulse">
                                <i class="fa-solid fa-layer-group text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-black text-slate-300 uppercase tracking-widest italic">No catalog entries detected</h3>
                            <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-widest font-black italic">Start by seeding your service infrastructure</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($services as $service): ?>
                    <tr class="hover:bg-slate-50/50 transition-all duration-300 group/row">
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 text-slate-400 flex items-center justify-center font-black text-xs shadow-sm group-hover/row:scale-110 group-hover/row:bg-indigo-600 group-hover/row:text-white transition-all duration-500 italic">
                                    SVC-<?= str_pad((string)$service['id'], 3, '0', STR_PAD_LEFT) ?>
                                </div>
                                <div>
                                    <span class="font-black text-slate-900 uppercase text-sm tracking-tight italic block mb-1"><?= htmlspecialchars((string)$service['name']) ?></span>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Operational Unit</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-8">
                            <span class="px-4 py-1.5 bg-indigo-50 text-indigo-700 rounded-xl text-[9px] font-black border border-indigo-100 uppercase tracking-widest italic">
                                <?= htmlspecialchars((string)($service['type_name'] ?? 'DEFAULT')) ?>
                            </span>
                        </td>
                        <td class="px-6 py-8">
                            <div class="flex items-center gap-3 text-[10px] font-black text-slate-500 uppercase tracking-widest italic">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-300 group-hover/row:bg-white transition-colors">
                                    <i class="fa-solid fa-user-friends"></i>
                                </div>
                                <span>CAP: <?= (int)($service['capacity'] ?? 1) ?> PX</span>
                            </div>
                        </td>
                        <td class="px-6 py-8 text-right">
                            <div class="space-y-1">
                                <span class="text-lg font-black text-slate-800 tracking-tighter italic"><?= number_format((float)($service['price_per_hour'] ?? 0), 0, ',', '.') ?>₫</span>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">/ Time Unit</p>
                            </div>
                        </td>
                        <td class="px-6 py-8 text-center text-center">
                            <?php 
                                $status = strtolower((string)($service['status'] ?? 'active'));
                                $statusClass = $status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-100 shadow-sm shadow-emerald-50' : 'bg-slate-50 text-slate-500 border-slate-100';
                            ?>
                            <span class="inline-flex items-center px-5 py-2 rounded-2xl border text-[9px] font-black uppercase tracking-widest <?= $statusClass ?> italic">
                                <span class="w-1.5 h-1.5 rounded-full mr-2.5 bg-current <?= $status === 'active' ? 'animate-pulse' : 'opacity-30' ?>"></span>
                                <?= strtoupper($status) ?>
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="flex items-center justify-end gap-3 opacity-0 group-hover/row:opacity-100 transition-all duration-300 translate-x-4 group-hover/row:translate-x-0">
                                <a href="<?= \App\Core\url('/admin/services/' . (int)$service['id'] . '/edit') ?>" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-95 group/edit">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="<?= \App\Core\url('/admin/services/' . (int)$service['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Xác nhận xóa bỏ dịch vụ này khỏi hệ thống? Thao tác này không thể hoàn tác.');">
                                    <button type="submit" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm active:scale-95 group/del">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
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

    <!-- Pagination & Catalog Manifest -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="flex items-center justify-between bg-white px-10 py-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/20 border border-slate-100">
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Catalog Manifest Page <span class="text-indigo-600 ml-2">#<?= $pageNumber ?></span></p>
        </div>
        <div class="flex items-center gap-3">
            <?php if ($pageNumber > 1): ?>
                <a href="<?= \App\Core\url('/admin/service-bookings/services?page=' . ($pageNumber - 1)) ?>" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-95">
                    <i class="fa-solid fa-angle-left text-xs"></i>
                </a>
            <?php endif; ?>
            
            <div class="px-6 py-3 bg-slate-50 border border-slate-100 rounded-2xl font-black text-[11px] uppercase tracking-widest text-slate-900 italic shadow-inner">
                Current Manifest: <?= $pageNumber ?>
            </div>
            
            <?php if ($hasMore): ?>
                <a href="<?= \App\Core\url('/admin/service-bookings/services?page=' . ($pageNumber + 1) . '&cursor=' . $nextCursor) ?>" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-95">
                    <i class="fa-solid fa-angle-right text-xs"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

