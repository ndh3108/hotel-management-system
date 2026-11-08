<?php
use App\Models\Room;

$statusLabels = Room::statusOptions();
$roomTypes = $roomTypes ?? [];
$filters = $filters ?? [];
$popularRoomTypes = $popularRoomTypes ?? [];

// Group statuses for better UX
$statusThemes = [
    Room::STATUS_AVAILABLE      => 'bg-emerald-50 text-emerald-700 border-emerald-100 ring-emerald-500/20',
    Room::STATUS_RESERVED       => 'bg-amber-50 text-amber-700 border-amber-100 ring-amber-500/20',
    Room::STATUS_OCCUPIED       => 'bg-rose-50 text-rose-700 border-rose-100 ring-rose-500/20',
    Room::STATUS_CLEANING       => 'bg-sky-50 text-sky-700 border-sky-100 ring-sky-500/20',
    Room::STATUS_MAINTENANCE    => 'bg-slate-100 text-slate-600 border-slate-200 ring-slate-400/20',
    Room::STATUS_BLOCKED        => 'bg-slate-100 text-slate-600 border-slate-200 ring-slate-400/20',
    Room::STATUS_OUT_OF_SERVICE => 'bg-slate-800 text-slate-200 border-slate-700 ring-slate-500/20',
    Room::STATUS_DAMAGED        => 'bg-red-100 text-red-800 border-red-200 ring-red-500/20',
];

$roomCount = count($rooms ?? []);
$availableRooms = count(array_filter($rooms ?? [], fn ($room) => ($room['display_status'] ?? Room::normalizeStatus((string)($room['status'] ?? ''))) === Room::STATUS_AVAILABLE));
$occupiedRooms = count(array_filter($rooms ?? [], fn ($room) => ($room['display_status'] ?? Room::normalizeStatus((string)($room['status'] ?? ''))) === Room::STATUS_OCCUPIED));
$otherRooms = $roomCount - $availableRooms - $occupiedRooms;
?>

<div class="space-y-8 animate-fade-in pb-20">
    <!-- Action Header -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-3 px-4 py-2 bg-indigo-50 rounded-2xl border border-indigo-100">
            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest leading-none"><?= $roomCount ?> căn hộ lưu trú</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= \App\Core\url('/admin/room/grid') ?>" class="px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-600 font-black hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm active:scale-95 text-xs uppercase tracking-widest">
                <i class="fa-solid fa-grid-2"></i> Sơ đồ phòng
            </a>
            <a href="<?= \App\Core\url('/admin/room/create') ?>" class="px-6 py-3.5 rounded-2xl bg-indigo-600 text-white font-black hover:bg-indigo-500 transition-all flex items-center gap-2 shadow-xl shadow-indigo-100 active:scale-95 text-xs uppercase tracking-widest">
                <i class="fa-solid fa-plus"></i> Khởi tạo phòng
            </a>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-indigo-900 p-6 rounded-[2.5rem] shadow-xl shadow-indigo-100 relative overflow-hidden group border-none">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform rotate-12 duration-500">
                <i class="fa-solid fa-door-open text-7xl text-white"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-1">Tổng quy mô</p>
                <h3 class="text-3xl font-black text-white leading-none"><?= number_format($roomCount) ?></h3>
                <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-4">Inventory Units</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-emerald-600">
                <i class="fa-solid fa-check-circle text-7xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Đang sẵn sàng</p>
                <h3 class="text-3xl font-black text-emerald-600 leading-none"><?= number_format($availableRooms) ?></h3>
                <p class="text-[9px] font-black text-emerald-500/50 uppercase tracking-widest mt-4">Ready for check-in</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-rose-600">
                <i class="fa-solid fa-user-lock text-7xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Đang có khách</p>
                <h3 class="text-3xl font-black text-rose-600 leading-none"><?= number_format($occupiedRooms) ?></h3>
                <p class="text-[9px] font-black text-rose-500/50 uppercase tracking-widest mt-4">Revenue Engine</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group text-slate-400">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-slate-900">
                <i class="fa-solid fa-screwdriver-wrench text-7xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Tình trạng khác</p>
                <h3 class="text-3xl font-black text-slate-700 leading-none"><?= number_format($otherRooms) ?></h3>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-4">Clean & Maintenance</p>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white p-10 rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100">
        <form method="GET" action="<?= \App\Core\url('/admin/room') ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-6 items-end">
            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Lọc theo hạng phòng</label>
                <select name="room_type_id" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700 text-xs uppercase tracking-widest">
                    <option value="">Tất cả hạng phòng</option>
                    <?php foreach ($roomTypes as $type): ?>
                        <option value="<?= (int) $type['id'] ?>" <?= (string) ($filters['room_type_id'] ?? '') === (string) ($type['id'] ?? '') ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) ($type['name'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Trạng thái vận hành</label>
                <select name="status" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700 text-xs uppercase tracking-widest">
                    <option value="">Mọi trạng thái</option>
                    <?php foreach ($statusLabels as $statusValue => $statusLabel): ?>
                        <option value="<?= htmlspecialchars((string) $statusValue) ?>" <?= (string) ($filters['status'] ?? '') === (string) $statusValue ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $statusLabel) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Giá cơ bản (Min)</label>
                <input type="number" name="min_price" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700" value="<?= htmlspecialchars((string) ($filters['min_price'] ?? '')) ?>" placeholder="Min ₫">
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Giá cơ bản (Max)</label>
                <input type="number" name="max_price" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700" value="<?= htmlspecialchars((string) ($filters['max_price'] ?? '')) ?>" placeholder="Max ₫">
            </div>

            <div class="xl:col-span-2 flex items-center gap-4">
                <button type="submit" class="flex-1 bg-slate-900 text-white font-black py-4 rounded-2xl hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3 active:scale-95 uppercase text-xs tracking-widest">
                    <i class="fa-solid fa-magnifying-glass"></i> Phân tích
                </button>
                <a href="<?= \App\Core\url('/admin/room') ?>" class="px-6 py-4 rounded-2xl bg-slate-100 text-slate-500 font-black hover:bg-slate-200 transition-all uppercase text-[10px] tracking-widest">Xóa lọc</a>
            </div>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/50">
                        <th class="px-10 py-6 text-left italic">Phòng & Hình ảnh</th>
                        <th class="px-6 py-6 text-left italic">Hạng & Phân loại</th>
                        <th class="px-6 py-6 text-left italic">Vị trí thực tế</th>
                        <th class="px-6 py-6 text-left italic">Trạng thái</th>
                        <th class="px-6 py-6 text-right italic">Giá cơ sở</th>
                        <th class="px-10 py-6 text-right italic">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (!empty($rooms)): ?>
                        <?php foreach ($rooms as $room): ?>
                            <?php 
                                $status = $room['display_status'] ?? Room::normalizeStatus((string) ($room['status'] ?? '')); 
                                $theme = $statusThemes[$status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                            ?>
                            <tr class="hover:bg-slate-50/80 transition-all group duration-300">
                                <td class="px-10 py-8">
                                    <div class="flex items-center gap-6">
                                        <div class="w-24 h-20 rounded-2xl overflow-hidden border border-slate-200 shadow-sm relative group-hover:scale-105 transition-all duration-500 bg-slate-50">
                                            <?php if (!empty($room['image_url'])): ?>
                                                <img src="<?= htmlspecialchars(\App\Core\url((string) $room['image_url'])) ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-200 gap-1">
                                                    <i class="fa-solid fa-camera-retro text-xl"></i>
                                                    <span class="text-[8px] font-black uppercase italic">No Photo</span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="absolute inset-x-0 bottom-0 py-1.5 bg-slate-900/40 backdrop-blur-sm text-center">
                                                <span class="text-[9px] font-black text-white italic tracking-tighter">P.<?= htmlspecialchars((string) ($room['room_number'] ?? '')) ?></span>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900 text-lg uppercase tracking-tight leading-tight italic">Phòng <?= htmlspecialchars((string) ($room['room_number'] ?? '')) ?></p>
                                            <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mt-1.5 flex items-center gap-2 italic">
                                                <span class="w-3 h-[1px] bg-indigo-200"></span>
                                                ID: #<?= str_pad((string)$room['id'], 5, '0', STR_PAD_LEFT) ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="space-y-1">
                                        <span class="inline-block px-3 py-1.5 bg-slate-900 text-white rounded-lg text-[9px] font-black uppercase tracking-widest italic"><?= htmlspecialchars((string) ($room['room_type_name'] ?? '-')) ?></span>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic px-1">Classification Unit</p>
                                    </div>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-1.5 text-[10px] font-black text-slate-900 uppercase tracking-widest italic">
                                            <i class="fa-solid fa-layer-group text-slate-300 w-4"></i>
                                            <span>Tầng <?= $room['floor'] !== null ? (int) $room['floor'] : '-' ?></span>
                                        </div>
                                        <?php if (!empty($room['location'])): ?>
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1 ml-5 italic">
                                                <?= htmlspecialchars((string) $room['location']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-8">
                                    <span class="inline-flex items-center px-4 py-2 rounded-full border text-[9px] font-black uppercase tracking-widest italic shadow-sm transition-all <?= $theme ?>">
                                        <span class="w-1.5 h-1.5 rounded-full mr-2.5 bg-current opacity-70 <?= in_array($status, [Room::STATUS_CLEANING, Room::STATUS_RESERVED]) ? 'animate-pulse' : '' ?>"></span>
                                        <?= htmlspecialchars($statusLabels[$status] ?? ucfirst(str_replace('_', ' ', (string) $status))) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-8 text-right">
                                    <div class="text-xl font-black text-indigo-600 italic tracking-tighter"><?= number_format((float) ($room['base_price'] ?? 0), 0, ',', '.') ?><span class="text-[9px] ml-1 font-bold opacity-50 uppercase tracking-widest">₫</span></div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1 italic">Base Rate / Night</p>
                                </td>
                                <td class="px-10 py-8 text-right">
                                    <div class="flex items-center justify-end gap-3 translate-x-2 group-hover:translate-x-0 transition-transform duration-500">
                                        <a href="<?= \App\Core\url('/admin/room/' . (int) $room['id'] . '/edit') ?>" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-90">
                                            <i class="fa-solid fa-pen-nib text-sm"></i>
                                        </a>
                                        <form action="<?= \App\Core\url('/admin/room/' . (int) $room['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Mọi dữ liệu về căn phòng này sẽ bị xóa vĩnh viễn. Bạn chắc chắn chứ?');">
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
                            <td colspan="6" class="px-10 py-32 text-center bg-slate-50/50">
                                <div class="w-24 h-24 bg-white shadow-xl shadow-slate-200/50 text-slate-200 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 animate-bounce transition-all">
                                    <i class="fa-solid fa-door-closed text-4xl"></i>
                                </div>
                                <h3 class="text-2xl font-black text-slate-300 uppercase tracking-widest">Kho phòng trống</h3>
                                <p class="text-slate-400 mt-2 font-bold uppercase text-[10px] tracking-[0.3em]">Hệ thống chưa ghi nhận căn hộ phù hợp</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if (($totalPages ?? 1) > 1): ?>
        <div class="flex items-center justify-between bg-white px-10 py-8 rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
                Trang <span class="text-indigo-600"><?= (int)($pageNumber ?? 1) ?></span> / <?= (int)($totalPages ?? 1) ?>
            </p>
            <div class="flex items-center gap-3">
                <a href="<?= \App\Core\url('/admin/room?page=1') ?>" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-50 transition-all active:scale-90 shadow-sm">
                    <i class="fa-solid fa-angles-left text-[10px]"></i>
                </a>
                <a href="<?= \App\Core\url('/admin/room?page=' . max(1, ($pageNumber ?? 1) - 1)) ?>" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-50 transition-all active:scale-90 shadow-sm">
                    <i class="fa-solid fa-angle-left text-[10px]"></i>
                </a>
                
                <div class="flex items-center gap-2">
                    <?php foreach (\App\Core\pagination_range((int) ($pageNumber ?? 1), (int) ($totalPages ?? 1)) as $pageItem): ?>
                        <?php if ($pageItem === '...'): ?>
                            <span class="px-2 font-black text-slate-300">...</span>
                        <?php else: ?>
                            <a href="<?= \App\Core\url('/admin/room?page=' . (int) $pageItem) ?>" class="w-11 h-11 rounded-2xl flex items-center justify-center font-black transition-all text-xs <?= (int) $pageItem === (int) ($pageNumber ?? 1) ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-100' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                                <?= (int) $pageItem ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <a href="<?= \App\Core\url('/admin/room?page=' . min((int) ($totalPages ?? 1), (int) ($pageNumber ?? 1) + 1)) ?>" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-50 transition-all active:scale-90 shadow-sm">
                    <i class="fa-solid fa-angle-right text-[10px]"></i>
                </a>
                <a href="<?= \App\Core\url('/admin/room?page=' . (int) ($totalPages ?? 1)) ?>" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-50 transition-all active:scale-90 shadow-sm">
                    <i class="fa-solid fa-angles-right text-[10px]"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

