<div class="max-w-[1200px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid <?= $room ? 'fa-door-open' : 'fa-plus-circle' ?>"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic"><?= $room ? 'Chỉnh sửa' : 'Khởi tạo' ?> Thông tin Phòng</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic"><?= $room ? 'Update Room Intelligence' : 'Deploy New Room Asset' ?></p>
            </div>
        </div>
        <a href="<?= \App\Core\url('/admin/room') ?>" class="px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-500 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all flex items-center gap-2 shadow-sm active:scale-95 text-xs uppercase tracking-widest italic">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-12">
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="mb-10 p-6 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-4 animate-shake">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-rose-500 shadow-sm border border-rose-100">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>
                    <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest italic"><?= htmlspecialchars((string) $_SESSION['error']) ?></p>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="<?= \App\Core\url($room ? '/admin/room/' . $room['id'] . '/update' : '/admin/room/store') ?>" 
                  method="POST" enctype="multipart/form-data" class="space-y-12">
                <?= csrf_field() ?>
                
                <!-- Core Configuration Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Mã số định danh phòng <span class="text-indigo-500">*</span></label>
                        <div class="relative group">
                            <i class="fa-solid fa-hashtag absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="text" name="room_number" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner"
                                value="<?= htmlspecialchars($room['room_number'] ?? '') ?>" required placeholder="VD: 101, 102...">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Xếp loại danh mục <span class="text-indigo-500">*</span></label>
                        <div class="relative group">
                            <i class="fa-solid fa-bed absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <select name="room_type_id" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner appearance-none" required>
                                <option value="">-- Chọn loại phòng --</option>
                                <?php foreach ($roomTypes as $rt): ?>
                                    <option value="<?= $rt['id'] ?>" <?= ($room['room_type_id'] ?? '') == $rt['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($rt['name']) ?> — <?= number_format($rt['base_price'], 0, ',', '.') ?>đ
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Cấp độ tầng (Floor)</label>
                        <div class="relative group">
                            <i class="fa-solid fa-layer-group absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="number" name="floor" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner" 
                                value="<?= $room['floor'] ?? '' ?>" min="1">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Trạng thái vận hành</label>
                        <div class="relative group">
                            <i class="fa-solid fa-signal absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <select name="status" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner appearance-none">
                                <option value="Available" <?= ($room['status'] ?? 'Available') === 'Available' ? 'selected' : '' ?>>Hoạt động (Available)</option>
                                <option value="Occupied" <?= ($room['status'] ?? '') === 'Occupied' ? 'selected' : '' ?>>Đang sử dụng (Occupied)</option>
                                <option value="Cleaning" <?= ($room['status'] ?? '') === 'Cleaning' ? 'selected' : '' ?>>Đang dọn phòng (Cleaning)</option>
                                <option value="Maintenance" <?= ($room['status'] ?? '') === 'Maintenance' ? 'selected' : '' ?>>Bảo trì hệ thống (Maintenance)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Media Assets Section -->
                <div class="space-y-8 bg-slate-50/30 p-10 rounded-[3rem] border border-slate-100 shadow-inner">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white text-indigo-600 flex items-center justify-center text-sm shadow-sm border border-slate-100">
                                <i class="fa-solid fa-images"></i>
                            </div>
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] italic">Kho tư liệu hình ảnh</h4>
                        </div>
                        <label class="cursor-pointer group flex items-center gap-3 px-6 py-3 bg-slate-900 text-white rounded-2xl hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 active:scale-95">
                            <span class="text-[10px] font-black uppercase tracking-widest italic group-hover:translate-x-1 transition-transform">Thêm hình ảnh mới</span>
                            <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                            <input type="file" name="images[]" class="hidden" accept="image/*" multiple>
                        </label>
                    </div>

                    <?php if (!empty($room['images'])): ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                            <?php foreach ($room['images'] as $img): ?>
                                <div class="relative aspect-square rounded-3xl overflow-hidden group/img shadow-lg border border-white">
                                    <img src="<?= htmlspecialchars($img['image_url'] ?? '') ?>" alt="Room Perspective"
                                         class="w-full h-full object-cover group-hover/img:scale-110 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                        <button type="button" 
                                               onclick="if(confirm('Xác nhận xóa bỏ hình ảnh này?')) { 
                                                   fetch('<?= \App\Core\url('/admin/room/' . ($room['id'] ?? 0) . '/images/' . (int) $img['id'] . '/delete') ?>', {method: 'POST'})
                                                   .then(res => window.location.reload()); 
                                               }"
                                               class="w-12 h-12 bg-white text-rose-500 rounded-2xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-2xl active:scale-90" title="Delete Insight">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="py-20 text-center flex flex-col items-center gap-4">
                            <div class="w-20 h-20 bg-white border border-slate-100 rounded-[2rem] flex items-center justify-center text-slate-100 text-3xl shadow-inner">
                                <i class="fa-solid fa-mountain-sun"></i>
                            </div>
                            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic">Chưa có tư liệu hình ảnh trực quan</p>
                        </div>
                    <?php endif; ?>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest text-center mt-6 italic opacity-60 italic uppercase tracking-widest leading-relaxed">Accepted formats: High fidelity JPEG, PNG. Max 5MB per asset.</p>
                </div>

                <!-- Submit Action Section -->
                <div class="pt-10 border-t border-slate-50 flex items-center justify-end gap-6">
                    <button type="reset" class="text-[10px] font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-colors italic">Làm mới dữ liệu</button>
                    <button type="submit" class="group flex items-center gap-4 px-12 py-5 bg-slate-900 text-white rounded-[2rem] hover:bg-indigo-600 transition-all duration-300 shadow-2xl shadow-indigo-100 active:scale-95">
                        <span class="text-[11px] font-black uppercase tracking-[0.2em] italic group-hover:translate-x-1 transition-transform"><?= $room ? 'Cập nhật tài sản' : 'Khai thác tài sản mới' ?></span>
                        <i class="fa-solid fa-save text-lg group-hover:rotate-12 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


