<div class="max-w-[1400px] mx-auto space-y-10 animate-fade-in pb-20">
    <!-- Notifications -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center gap-3 shadow-sm animate-slide-down">
            <i class="fa-solid fa-circle-check"></i>
            <span class="font-bold"><?= htmlspecialchars((string)$_SESSION['success']) ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="p-5 rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 flex items-center gap-3 shadow-sm animate-slide-down">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span class="font-bold"><?= htmlspecialchars((string)$_SESSION['error']) ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="<?= \App\Core\url($room ? '/admin/room/' . (int) $room['id'] . '/update' : '/admin/room/store') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
            
            <!-- Left Column: Core Configuration (8/12) -->
            <div class="xl:col-span-8 space-y-10">
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <div class="p-10 space-y-10">
                        <div class="flex items-center gap-4 border-b border-slate-50 pb-8">
                            <a href="<?= \App\Core\url('/admin/room') ?>" class="w-12 h-12 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-500 rounded-2xl transition-all active:scale-90 shadow-sm border border-slate-100">
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight"><?= $room ? 'Hiệu chỉnh phòng' : 'Khởi tạo phòng mới' ?></h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Thiết lập định danh và cấu hình vận hành cơ sở</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Số phòng định danh <span class="text-rose-500">*</span></label>
                                <input type="text" name="room_number" class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-black text-slate-800 text-lg placeholder:font-medium" value="<?= htmlspecialchars((string) ($room['room_number'] ?? '')) ?>" placeholder="VD: 101, A-202..." required>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Hạng phòng áp dụng <span class="text-rose-500">*</span></label>
                                <select name="room_type_id" class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none bg-white font-black text-slate-700 uppercase text-xs tracking-widest" required>
                                    <option value="">-- Lựa chọn hạng phòng --</option>
                                    <?php foreach ($roomTypes as $roomType): ?>
                                        <option value="<?= (int) $roomType['id'] ?>" <?= (int) ($room['room_type_id'] ?? 0) === (int) $roomType['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars((string) $roomType['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Vị trí / Phân khu</label>
                                <input type="text" name="location" class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none text-slate-700 font-bold" value="<?= htmlspecialchars((string) ($room['location'] ?? '')) ?>" placeholder="VD: Beachfront Wing, Tòa A">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Số tầng</label>
                                <div class="relative group">
                                    <input type="number" name="floor" class="w-full pl-6 pr-16 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-black text-slate-800" min="1" value="<?= htmlspecialchars((string) ($room['floor'] ?? '')) ?>">
                                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 font-black text-[10px] tracking-widest group-focus-within:text-indigo-400 transition-colors">TẦNG</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Ghi chú vận hành & Buồng phòng</label>
                            <textarea name="notes" class="w-full px-6 py-5 rounded-[2rem] border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none min-h-[120px] text-slate-700 leading-relaxed font-medium placeholder:font-normal" placeholder="Thông tin đặc biệt cho nhân viên buồng phòng hoặc lưu ý về căn phòng..."><?= htmlspecialchars((string) ($room['notes'] ?? '')) ?></textarea>
                        </div>

                        <div class="space-y-8 pt-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-50 text-indigo-500 rounded-xl flex items-center justify-center">
                                    <i class="fa-solid fa-images"></i>
                                </div>
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Bộ sưu tập hình ảnh thực tế</h3>
                            </div>
                            
                            <div class="p-10 rounded-[2.5rem] bg-slate-50 border-2 border-dashed border-slate-200 hover:border-indigo-400 transition-all group text-center space-y-4 relative cursor-pointer overflow-hidden">
                                <input type="file" name="images[]" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*" multiple>
                                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto text-indigo-500 group-hover:scale-110 transition-transform relative z-0">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                                </div>
                                <div class="relative z-0">
                                    <h4 class="font-black text-slate-800 text-sm uppercase tracking-widest">Tải lên hình ảnh phòng</h4>
                                    <p class="text-[10px] text-slate-400 mt-1 font-bold uppercase tracking-widest">JPG, PNG • Tự động tối ưu hóa Cloudinary</p>
                                </div>
                            </div>

                            <?php if (!empty($room['images'])): ?>
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                                    <?php foreach ($room['images'] as $image): ?>
                                        <div class="relative group aspect-square rounded-[2rem] overflow-hidden border border-slate-200 shadow-sm bg-slate-100">
                                            <img src="<?= htmlspecialchars((string) ($image['image_url'])) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                            <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <button type="button" 
                                                        onclick="if(confirm('Xóa vĩnh viễn hình ảnh này khỏi hệ thống?')){document.getElementById('delete-img-<?= $image['id'] ?>').submit();}"
                                                        class="w-12 h-12 rounded-2xl bg-white/20 hover:bg-rose-500 text-white flex items-center justify-center backdrop-blur-md transition-all shadow-lg active:scale-95">
                                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Operational Status & Features (4/12) -->
            <div class="xl:col-span-4 space-y-10">
                
                <!-- Status & Action Card -->
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden sticky top-6">
                    <div class="p-10 space-y-8">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Trạng thái vận hành hiện tại</label>
                            <select name="status" class="w-full px-6 py-5 rounded-[1.5rem] border border-slate-100 bg-indigo-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-indigo-700 text-lg uppercase tracking-tight shadow-inner">
                                <?php foreach ($statusOptions as $value => $label): ?>
                                    <option value="<?= htmlspecialchars((string) $value) ?>" <?= ($room['status'] ?? '') === $value ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string) $label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Đặc điểm nổi bật (Features)</label>
                            <div class="space-y-3 bg-slate-50/50 p-6 rounded-[2rem] border border-slate-100 max-h-[300px] overflow-y-auto thin-scrollbar">
                                <?php foreach ($allFeatures as $feature): ?>
                                    <label class="flex items-center gap-4 p-4 rounded-2xl bg-white shadow-sm hover:shadow-md transition-all cursor-pointer group border border-transparent hover:border-indigo-100">
                                        <input type="checkbox" name="features[]" value="<?= $feature['id'] ?>" 
                                               class="w-5 h-5 rounded-md border-slate-200 text-indigo-600 focus:ring-indigo-500 transition-all"
                                               <?= in_array($feature['id'], $room['feature_ids'] ?? []) ? 'checked' : '' ?>>
                                        <span class="text-xs font-black text-slate-600 group-hover:text-slate-900 transition-colors flex items-center gap-3 uppercase tracking-wider">
                                            <i class="<?= htmlspecialchars($feature['icon'] ?? 'fa-solid fa-tag') ?> text-slate-300 group-hover:text-indigo-500 transition-colors w-5 text-sm"></i>
                                            <?= htmlspecialchars($feature['name']) ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4 pt-4 border-t border-slate-50">
                            <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-[1.5rem] transition-all shadow-2xl shadow-indigo-200 flex items-center justify-center gap-3 active:scale-95 uppercase text-xs tracking-widest leading-none">
                                <i class="fa-solid fa-save text-sm"></i>
                                <?= $room ? 'Cập nhật phòng' : 'Tạo mới phòng' ?>
                            </button>
                            <a href="<?= \App\Core\url('/admin/room') ?>" class="w-full py-4 bg-slate-50 hover:bg-slate-100 text-slate-500 font-black rounded-[1.5rem] transition-all text-center uppercase text-[10px] tracking-widest border border-slate-100">Hủy cấu hình</a>
                        </div>
                    </div>
                </div>

                <!-- Maintenance History Card -->
                <?php if ($room): ?>
                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
                        <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>
                                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Nhật ký bảo trì</h3>
                                </div>
                                <span class="px-2.5 py-1 bg-white rounded-lg text-[9px] font-black text-slate-400 uppercase tracking-widest shadow-sm"><?= count($room['maintenance_history'] ?? []) ?> DATA</span>
                            </div>
                        </div>
                        <div class="p-0">
                            <?php if (empty($room['maintenance_history'])): ?>
                                <div class="p-16 text-center text-slate-400 space-y-4">
                                    <i class="fa-solid fa-hammer text-4xl opacity-10"></i>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Chưa có dữ liệu</p>
                                </div>
                            <?php else: ?>
                                <div class="divide-y divide-slate-50 max-h-[400px] overflow-y-auto thin-scrollbar">
                                    <?php foreach ($room['maintenance_history'] as $record): 
                                        $pClass = match($record['priority']) { 
                                            'High' => 'bg-rose-500', 
                                            'Medium' => 'bg-amber-500', 
                                            default => 'bg-indigo-500' 
                                        };
                                        $pTextClass = match($record['priority']) { 
                                            'High' => 'text-rose-600 bg-rose-50 border-rose-100', 
                                            'Medium' => 'text-amber-600 bg-amber-50 border-amber-100', 
                                            default => 'text-indigo-600 bg-indigo-50 border-indigo-100' 
                                        };
                                    ?>
                                        <div class="p-6 hover:bg-slate-50 transition-all relative group cursor-default">
                                            <div class="absolute left-0 top-6 bottom-6 w-1 <?= $pClass ?> rounded-r-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                            <div class="flex items-start justify-between mb-3">
                                                <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest px-1"><?= date('d/m/Y', strtotime($record['maintenance_date'])) ?></span>
                                                <span class="text-[8px] font-black uppercase px-2.5 py-1 rounded-lg border <?= $pTextClass ?> tracking-widest leading-none"><?= $record['priority'] ?></span>
                                            </div>
                                            <h4 class="text-xs font-black text-slate-800 leading-relaxed uppercase tracking-tight"><?= htmlspecialchars($record['description']) ?></h4>
                                            <div class="mt-4 flex items-center justify-between border-t border-slate-50 pt-3">
                                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest flex items-center gap-1.5">
                                                    <i class="fa-solid fa-user-gear text-slate-300"></i>
                                                    <?= htmlspecialchars($record['performer_name'] ?? 'Hệ thống') ?>
                                                </p>
                                                <i class="fa-solid fa-chevron-right text-[8px] text-slate-200"></i>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <!-- Hidden Deletion Forms -->
    <?php if ($room && !empty($room['images'])): ?>
        <?php foreach ($room['images'] as $image): ?>
            <form id="delete-img-<?= $image['id'] ?>" 
                  action="<?= \App\Core\url('/admin/room/' . (int) $room['id'] . '/images/' . (int) $image['id'] . '/delete') ?>" 
                  method="POST" style="display: none;">
                <?= csrf_field() ?>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
    .thin-scrollbar::-webkit-scrollbar { width: 4px; }
    .thin-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .thin-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .thin-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
ansparent; }
    .thin-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .thin-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>


