<?php
$services = $services ?? [];
$totalServices = count($services);
$totalCapacity = array_sum(array_column($services, 'capacity'));
$avgPrice = $totalServices > 0 ? array_sum(array_column($services, 'price_per_hour')) / $totalServices : 0;
$availableCount = count(array_filter($services, fn($s) => ($s['status'] ?? 'AVAILABLE') === 'AVAILABLE'));
?>

<div class="space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-bell-concierge"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Cơ sở vật chất & Dịch vụ</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Event space, catering & hospitality services</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center gap-3 px-4 py-2 bg-indigo-50 rounded-2xl border border-indigo-100">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest leading-none"><?= $totalServices ?> Active Services</span>
            </div>
            <a href="<?= \App\Core\url('/admin/services/create') ?>" class="px-8 py-4 rounded-2xl bg-slate-900 text-white font-black hover:bg-indigo-600 transition-all flex items-center gap-3 shadow-2xl shadow-indigo-100 active:scale-95 text-xs uppercase tracking-widest italic">
                <i class="fa-solid fa-plus-circle text-lg"></i> Thi công dịch vụ
            </a>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="bg-slate-900 p-8 rounded-[3rem] shadow-2xl shadow-slate-200/50 relative overflow-hidden group border-none">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform rotate-12 duration-500 text-white font-black text-8xl">
                <i class="fa-solid fa-building-circle-check"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2 italic">Dịch vụ đang chạy</p>
                <h3 class="text-4xl font-black text-white leading-none italic"><?= number_format($availableCount) ?></h3>
                <p class="text-[9px] font-black text-indigo-400/50 uppercase tracking-widest mt-6 italic">Active Assets</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-slate-900 text-8xl">
                <i class="fa-solid fa-users-viewfinder"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Tổng sức chứa</p>
                <h3 class="text-4xl font-black text-slate-900 leading-none italic"><?= number_format($totalCapacity) ?></h3>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-6 italic">Pax Capacity</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-slate-900 text-8xl">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Giá trung bình</p>
                <h3 class="text-4xl font-black text-slate-900 leading-none italic"><?= number_format($avgPrice / 1000, 0) ?>K</h3>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-6 italic">VNĐ / Hour</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-rose-600 text-8xl">
                <i class="fa-solid fa-wrench"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Cần bảo trì</p>
                <h3 class="text-4xl font-black text-rose-600 leading-none italic"><?= $totalServices - $availableCount ?></h3>
                <p class="text-[9px] font-black text-rose-500/50 uppercase tracking-widest mt-6 italic">Maintenance Required</p>
            </div>
        </div>
    </div>

    <!-- Services Table -->
    <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-12 border-b border-slate-50 flex flex-col lg:flex-row lg:items-center justify-between gap-8 bg-slate-50/20">
            <div>
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">Danh mục vận hành dịch vụ</h2>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2 italic">Quản lý không gian sự kiện, hội nghị và cơ sở vật chất phòng đặc thù</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="mx-12 mt-8 p-6 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-[2rem] text-xs font-black flex items-center gap-4 animate-slide-down">
                    <div class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-emerald-100">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-widest opacity-60">System Notification</p>
                        <p class="uppercase italic"><?= htmlspecialchars((string) $_SESSION['success']) ?></p>
                    </div>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/30">
                        <th class="px-12 py-8 text-left italic">Hình ảnh & Định danh</th>
                        <th class="px-8 py-8 text-left italic">Phân loại & Đặc tính</th>
                        <th class="px-8 py-8 text-center italic">Sức chứa</th>
                        <th class="px-8 py-8 text-right italic">Đơn giá / Giờ</th>
                        <th class="px-8 py-8 text-center italic">Vận hành</th>
                        <th class="px-12 py-8 text-right italic">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($services)): ?>
                        <tr>
                            <td colspan="6" class="px-12 py-40 text-center bg-slate-50/30">
                                <div class="w-24 h-24 bg-slate-100 rounded-[2.5rem] flex items-center justify-center text-slate-300 mx-auto mb-8 shadow-inner italic font-black text-4xl">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>
                                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-widest italic">Kho dữ liệu trống</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-3">Hệ thống chưa ghi nhận cấu hình dịch vụ nào</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($services as $s): ?>
                            <tr class="hover:bg-slate-50/80 transition-all group duration-300">
                                <td class="px-12 py-8">
                                    <div class="flex items-center gap-6">
                                        <div class="relative">
                                            <?php if (!empty($s['image_url'])): ?>
                                                <img src="<?= htmlspecialchars((string) $s['image_url']) ?>" alt="Service" class="w-24 h-20 object-cover rounded-[2rem] shadow-2xl shadow-slate-200 group-hover:scale-110 group-hover:rotate-2 transition-all duration-700">
                                            <?php else: ?>
                                                <div class="w-24 h-20 bg-slate-100 rounded-[2rem] flex items-center justify-center text-slate-300 border border-slate-200 border-dashed group-hover:scale-110 transition-transform duration-500">
                                                    <i class="fa-solid fa-mountain-sun text-2xl"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900 text-lg uppercase tracking-tight leading-tight italic"><?= htmlspecialchars((string) $s['name']) ?></p>
                                            <p class="text-[9px] font-black text-indigo-500 uppercase tracking-[0.3em] mt-2 flex items-center gap-2 italic">
                                                <span class="w-4 h-[1px] bg-indigo-200"></span>
                                                SRV-<?= str_pad((string)$s['id'], 3, '0', STR_PAD_LEFT) ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-8">
                                    <div class="space-y-2">
                                        <span class="inline-block px-4 py-1.5 rounded-xl bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest italic"><?= htmlspecialchars((string) ($s['type_name'] ?? 'General')) ?></span>
                                        <p class="text-[10px] font-medium text-slate-400 line-clamp-1 max-w-[250px] italic"><?= htmlspecialchars((string) ($s['description'] ?? 'Chưa cập nhật mô tả đặc tính...')) ?></p>
                                    </div>
                                </td>
                                <td class="px-8 py-8 text-center">
                                    <div class="inline-flex items-center gap-3 px-4 py-2 rounded-2xl bg-white border border-slate-100 shadow-sm">
                                        <i class="fa-solid fa-users text-indigo-400 text-xs"></i>
                                        <span class="text-sm font-black text-slate-900 italic"><?= (int) $s['capacity'] ?></span>
                                    </div>
                                </td>
                                <td class="px-8 py-8 text-right">
                                    <div class="font-black text-slate-900 text-xl tracking-tighter italic">
                                        <?= number_format((float) $s['price_per_hour'], 0, ',', '.') ?> <span class="text-[9px] text-slate-400 uppercase tracking-widest ml-1 font-black">VND/H</span>
                                    </div>
                                </td>
                                <td class="px-8 py-8 text-center">
                                    <?php 
                                        $isAvailable = ($s['status'] ?? 'AVAILABLE') === 'AVAILABLE';
                                        $sClass = $isAvailable ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100';
                                    ?>
                                    <span class="inline-flex items-center px-5 py-2.5 rounded-full border text-[9px] font-black uppercase tracking-widest italic <?= $sClass ?>">
                                        <span class="w-2 h-2 rounded-full mr-2.5 bg-current <?= $isAvailable ? 'animate-pulse' : '' ?>"></span>
                                        <?= $isAvailable ? 'Sẵn sàng' : 'Bảo trì' ?>
                                    </span>
                                </td>
                                <td class="px-12 py-8 text-right">
                                    <div class="flex items-center justify-end gap-4 translate-x-4 group-hover:translate-x-0 transition-all duration-500">
                                        <a href="<?= \App\Core\url('/admin/services/' . (int) $s['id'] . '/edit') ?>" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-90">
                                            <i class="fa-solid fa-pen-nib text-sm"></i>
                                        </a>
                                        <form action="<?= \App\Core\url('/admin/services/' . (int) $s['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Toàn bộ cấu hình và hình ảnh trên Cloudinary sẽ bị xóa vĩnh viễn. Tiếp tục?');" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm active:scale-90 text-sm">
                                                <i class="fa-solid fa-trash-can"></i>
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
    <div class="flex items-center justify-between bg-white px-12 py-10 rounded-[3.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] italic">
            Hiển thị trang <span class="text-slate-900 px-2 py-1 bg-slate-50 border border-slate-100 rounded-lg mx-2"><?= (int)($pageNumber ?? 1) ?></span> quy trình dịch vụ
        </p>
        <div class="flex items-center gap-4">
            <?php if ($pageNumber > 1): ?>
                <a href="<?= \App\Core\url('/admin/services?page=' . ($pageNumber - 1)) ?>" class="px-10 py-4 rounded-2xl bg-white border border-slate-200 text-slate-600 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all transform active:scale-95 uppercase text-[10px] tracking-widest italic flex items-center gap-4">
                    <i class="fa-solid fa-arrow-left-long"></i> Quay lại
                </a>
            <?php endif; ?>
            
            <?php if ($hasMore ?? false): ?>
                <a href="<?= \App\Core\url('/admin/services?page=' . ($pageNumber + 1) . '&cursor=' . ($nextCursor ?? '')) ?>" class="px-10 py-4 rounded-2xl bg-slate-900 text-white font-black hover:bg-indigo-600 transition-all transform active:scale-95 uppercase text-[10px] tracking-widest italic flex items-center gap-4 shadow-2xl shadow-slate-200">
                    Kế tiếp <i class="fa-solid fa-arrow-right-long"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
