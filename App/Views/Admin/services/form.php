<?php
$service = $service ?? null;
$serviceTypes = $serviceTypes ?? [];
$isEdit = !empty($service);
?>

<div class="max-w-[1400px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid <?= $isEdit ? 'fa-wand-magic-sparkles' : 'fa-plus-circle' ?>"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic"><?= $isEdit ? 'Hiệu chỉnh dịch vụ' : 'Khởi tạo dịch vụ' ?></h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Definining Service Resource Characteristics</p>
            </div>
        </div>
        <a href="<?= \App\Core\url('/admin/services') ?>" class="px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-500 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all flex items-center gap-2 shadow-sm active:scale-95 text-xs uppercase tracking-widest italic">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <form action="<?= \App\Core\url($isEdit ? '/admin/services/' . (int) $service['id'] . '/update' : '/admin/services/store') ?>" method="POST" enctype="multipart/form-data" class="space-y-10">
        <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
            <!-- Left Column: Primary Config (8/12) -->
            <div class="xl:col-span-8 space-y-10">
                <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <div class="p-12 space-y-10">
                        <!-- Main Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Tên Dịch Vụ / Không gian <span class="text-indigo-500">*</span></label>
                                <div class="relative group">
                                    <i class="fa-solid fa-signature absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                                    <input type="text" name="name" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-900 text-lg uppercase tracking-tight shadow-inner" 
                                        value="<?= htmlspecialchars((string) ($service['name'] ?? '')) ?>" placeholder="VD: Grand Ballroom, Meeting Room A..." required>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Phân loại dịch vụ <span class="text-indigo-500">*</span></label>
                                <div class="relative group">
                                    <select name="service_type_id" class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-[11px] uppercase tracking-widest shadow-inner appearance-none cursor-pointer" required>
                                        <option value="">-- Chọn loại --</option>
                                        <?php foreach($serviceTypes as $st): ?>
                                            <option value="<?= $st['id'] ?>" <?= (($service['service_type_id'] ?? '') == $st['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string) $st['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <i class="fa-solid fa-layer-group absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 text-[10px]"></i>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Sức chứa tối đa (Pax) <span class="text-indigo-500">*</span></label>
                                <div class="relative group">
                                    <i class="fa-solid fa-users absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                                    <input type="number" name="capacity" class="w-full pl-14 pr-20 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-900 text-lg shadow-inner" 
                                        value="<?= (int) ($service['capacity'] ?? 1) ?>" min="1" required>
                                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase tracking-widest italic group-focus-within:text-indigo-400 transition-colors">People</span>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Đơn giá thuê / Giờ <span class="text-indigo-500">*</span></label>
                                <div class="relative group">
                                    <i class="fa-solid fa-coins absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                                    <input type="number" name="price_per_hour" class="w-full pl-14 pr-20 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-indigo-600 text-xl shadow-inner" 
                                        value="<?= (float) ($service['price_per_hour'] ?? 0) ?>" min="0" step="1000" required>
                                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase tracking-widest italic group-focus-within:text-indigo-400 transition-colors">VNĐ</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Mô tả đặc tính & Trang thiết bị</label>
                            <div class="relative group">
                                <i class="fa-solid fa-pen-fancy absolute left-6 top-6 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                                <textarea name="description" class="w-full pl-14 pr-6 py-6 rounded-[2.5rem] border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none min-h-[200px] text-slate-700 text-sm font-medium leading-relaxed italic shadow-inner" 
                                    placeholder="Mô tả về không gian, trang thiết bị âm thanh, ánh sáng, view hoặc các dịch vụ đi kèm..."><?= htmlspecialchars((string) ($service['description'] ?? '')) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gallery Section -->
                <?php if ($isEdit && !empty($service['images'])): ?>
                    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                        <div class="p-12 space-y-10">
                            <div class="flex items-center justify-between border-b border-slate-50 pb-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center text-lg shadow-xl shadow-slate-100">
                                        <i class="fa-solid fa-images"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">Bộ sưu tập hình ảnh</h3>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1 italic">Managing Public Media Assets</p>
                                    </div>
                                </div>
                                <div class="px-5 py-2 bg-slate-50 rounded-xl border border-slate-100 italic text-[10px] font-black text-indigo-500 uppercase tracking-widest">
                                    <?= count($service['images']) ?> Files Synchronized
                                </div>
                            </div>

                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-8">
                                <?php foreach ($service['images'] as $img): ?>
                                    <div class="relative group aspect-[4/3] rounded-[2rem] overflow-hidden bg-slate-50 border border-slate-100 shadow-lg shadow-slate-100/50 transition-all duration-500 hover:shadow-2xl hover:shadow-indigo-100">
                                        <img src="<?= htmlspecialchars((string) $img['image_url']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-4">
                                            <a href="<?= htmlspecialchars((string) $img['image_url']) ?>" target="_blank" class="w-12 h-12 bg-white text-slate-900 rounded-2xl flex items-center justify-center hover:scale-110 transition-all active:scale-90 shadow-2xl">
                                                <i class="fa-solid fa-expand text-sm"></i>
                                            </a>
                                            <form action="<?= \App\Core\url('/admin/services/' . (int) $service['id'] . '/images/' . (int) $img['id'] . '/delete') ?>" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Toàn bộ dữ liệu của hình ảnh này trên Cloudinary sẽ bị xóa. Tiếp tục?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="w-12 h-12 bg-rose-600 text-white rounded-2xl flex items-center justify-center hover:scale-110 transition-all active:scale-90 shadow-2xl">
                                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="absolute bottom-4 left-4 right-4 translate-y-10 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                                            <div class="px-3 py-1.5 bg-black/40 backdrop-blur-md rounded-lg text-white/80 text-[8px] font-black uppercase tracking-widest text-center truncate">
                                                IMG_<?= str_pad((string)$img['id'], 5, '0', STR_PAD_LEFT) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Operational Stats (4/12) -->
            <div class="xl:col-span-4 space-y-10 text-center">
                <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden sticky top-6">
                    <div class="p-10 space-y-8">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Trạng thái vận hành</label>
                            <div class="relative group">
                                <select name="status" class="w-full px-6 py-5 rounded-[1.5rem] border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-indigo-700 text-[11px] uppercase tracking-widest shadow-inner appearance-none cursor-pointer">
                                    <option value="AVAILABLE" <?= (($service['status'] ?? '') === 'AVAILABLE') ? 'selected' : '' ?>>SẴN SÀNG (LIVE)</option>
                                    <option value="MAINTENANCE" <?= (($service['status'] ?? '') === 'MAINTENANCE') ? 'selected' : '' ?>>BẢO TRÌ (MNT)</option>
                                </select>
                                <i class="fa-solid fa-circle-dot absolute right-6 top-1/2 -translate-y-1/2 text-indigo-400 text-[10px] animate-pulse"></i>
                            </div>
                        </div>

                        <!-- Image Uploader -->
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Cập nhật hình ảnh (Cloudinary)</label>
                            <div class="relative h-60 rounded-[3rem] border-2 border-dashed border-slate-200 bg-slate-50/50 hover:bg-white hover:border-indigo-400 transition-all flex flex-col items-center justify-center text-center p-8 group overflow-hidden">
                                <div class="absolute inset-x-0 bottom-0 h-1 bg-slate-100 group-hover:bg-indigo-500 transition-all duration-1000 origin-left scale-x-0 group-hover:scale-x-100"></div>
                                <i class="fa-solid fa-cloud-arrow-up text-5xl text-slate-200 group-hover:text-indigo-500 group-hover:-translate-y-2 transition-all duration-500 mb-4"></i>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] leading-loose">Kéo thả tệp tin<br><span class="text-slate-300">Max size 20MB</span></p>
                                <input type="file" name="images[]" class="absolute inset-0 opacity-0 cursor-pointer" multiple accept="image/*">
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-50 space-y-4">
                            <button type="submit" class="w-full group flex items-center justify-center gap-4 py-5 bg-slate-900 text-white rounded-[1.5rem] hover:bg-indigo-600 transition-all duration-300 shadow-2xl shadow-indigo-100 active:scale-95">
                                <span class="text-[11px] font-black uppercase tracking-[0.2em] italic group-hover:translate-x-1 transition-transform"><?= $isEdit ? 'Lưu thay đổi' : 'Khởi tạo ngay' ?></span>
                                <i class="fa-solid fa-save text-lg group-hover:rotate-12 transition-transform"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <?php if (!empty($_SESSION['success'])): ?>
                    <div class="p-6 bg-emerald-900 rounded-[2.5rem] shadow-2xl shadow-emerald-100/50 flex items-center gap-4 text-white animate-slide-up">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-emerald-400 backdrop-blur-md">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest italic">Success</p>
                            <p class="text-[10px] font-black uppercase"><?= htmlspecialchars((string) $_SESSION['success']) ?></p>
                        </div>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

