<div class="max-w-[1200px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid <?= $roomType ? 'fa-pen-to-square' : 'fa-folder-plus' ?>"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic"><?= $roomType ? 'Cập nhật' : 'Khởi tạo' ?> Danh mục Phòng</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic"><?= $roomType ? 'Refining Architectural Standards' : 'Deploying New Structural Catalog' ?></p>
            </div>
        </div>
        <a href="<?= \App\Core\url('/admin/room-type') ?>" class="px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-500 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all flex items-center gap-2 shadow-sm active:scale-95 text-xs uppercase tracking-widest italic">
            <i class="fa-solid fa-arrow-left"></i> Quay lại Danh mục
        </a>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-12">
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="mb-10 p-6 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 animate-slide-down">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-emerald-500 shadow-sm border border-emerald-100">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest italic"><?= htmlspecialchars((string) $_SESSION['success']) ?></p>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="mb-10 p-6 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-4 animate-shake">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-rose-500 shadow-sm border border-rose-100">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>
                    <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest italic"><?= htmlspecialchars((string) $_SESSION['error']) ?></p>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="<?= \App\Core\url($roomType ? '/admin/room-type/' . (int)$roomType['id'] . '/update' : '/admin/room-type/store') ?>"
                method="POST" enctype="multipart/form-data" id="roomTypeForm" class="space-y-12">
                <?= csrf_field() ?>

                <!-- Core Identity Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Tên danh mục phòng <span class="text-indigo-500">*</span></label>
                        <div class="relative group">
                            <i class="fa-solid fa-signature absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="text" name="name" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner"
                                value="<?= htmlspecialchars($roomType['name'] ?? '') ?>" required placeholder="VD: LUXURY SUITE, DELUXE TWIN...">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Định danh URL (Slug)</label>
                        <div class="relative group border-none">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-[10px] font-black uppercase tracking-widest">SLUG/</div>
                            <input type="text" name="slug" class="w-full pl-20 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-500 text-sm italic shadow-inner"
                                value="<?= htmlspecialchars($roomType['slug'] ?? '') ?>" placeholder="Tự động tạo nếu để trống">
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Mô tả chi tiết trải nghiệm</label>
                    <textarea name="description" rows="4" class="w-full px-8 py-6 rounded-[2.5rem] border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-medium text-slate-600 text-sm leading-relaxed shadow-inner placeholder:italic italic uppercase tracking-widest leading-relaxed italic uppercase tracking-widest leading-relaxed italic uppercase tracking-widest leading-relaxed" placeholder="Ghi chú chi tiết về không gian, phong cách và trải nghiệm..."><?= htmlspecialchars($roomType['description'] ?? '') ?></textarea>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Liên kết Video Perspective (YouTube)</label>
                    <div class="relative group">
                        <i class="fa-brands fa-youtube absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-sm group-focus-within:text-rose-600 transition-colors"></i>
                        <input type="url" name="youtube_url" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-500 text-xs italic shadow-inner placeholder:not-italic"
                            value="<?= htmlspecialchars($roomType['video_url'] ?? '') ?>"
                            placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                </div>

                <!-- Structural Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Sức chứa Người lớn <span class="text-indigo-500">*</span></label>
                        <input type="number" name="capacity_adults" class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-center text-slate-800 text-lg shadow-inner"
                            value="<?= (int)($roomType['capacity_adults'] ?? 2) ?>" min="1" required>
                    </div>
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Sức chứa Trẻ em <span class="text-indigo-500">*</span></label>
                        <input type="number" name="capacity_children" class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-center text-slate-800 text-lg shadow-inner"
                            value="<?= (int)($roomType['capacity_children'] ?? 0) ?>" min="0" required>
                    </div>
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Diện tích (m²)</label>
                        <input type="number" name="area_sqm" class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-center text-slate-800 text-lg shadow-inner"
                            value="<?= htmlspecialchars((string)($roomType['area_sqm'] ?? '')) ?>" min="1" step="0.5">
                    </div>
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Giá niêm yết/Đêm <span class="text-emerald-500">*</span></label>
                        <div class="relative group">
                            <input type="number" name="base_price" class="w-full pr-14 pl-6 py-5 rounded-2xl border border-slate-100 bg-emerald-50/30 focus:bg-white focus:ring-8 focus:ring-emerald-500/5 transition-all outline-none font-black text-center text-emerald-600 text-lg shadow-inner border border-emerald-100 italic uppercase tracking-widest leading-relaxed"
                                value="<?= htmlspecialchars((string)($roomType['base_price'] ?? '')) ?>" min="0" step="1000" required>
                            <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-emerald-400 uppercase">VNĐ</span>
                        </div>
                    </div>
                </div>

                <!-- Amenities Selection Section -->
                <div class="space-y-8">
                    <div class="flex items-center gap-3 border-b border-slate-50 pb-6">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-sm">
                            <i class="fa-solid fa-puzzle-piece"></i>
                        </div>
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] italic">Danh mục Tiện ích & Trang thiết bị</h4>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <?php if (!empty($amenities)): ?>
                            <?php foreach ($amenities as $am): ?>
                                <label class="relative group cursor-pointer">
                                    <input type="checkbox" name="amenities[]" value="<?= (int)$am['id'] ?>"
                                        <?= (isset($selectedAmenities) && in_array((int)$am['id'], $selectedAmenities)) ? 'checked' : '' ?> class="sr-only peer">
                                    <div class="p-4 bg-white border border-slate-100 rounded-2xl shadow-sm peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all flex flex-col items-center gap-3 group-hover:shadow-md h-full justify-center text-center">
                                        <div class="text-xl text-slate-300 peer-checked:text-indigo-600 transition-colors">
                                            <i class="<?= htmlspecialchars($am['icon_url'] ?? 'fa-solid fa-check') ?>"></i>
                                        </div>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest peer-checked:text-indigo-700 transition-colors italic"><?= htmlspecialchars($am['name']) ?></span>
                                    </div>
                                    <div class="absolute -right-1 -top-1 w-5 h-5 bg-indigo-600 text-white rounded-lg flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity shadow-lg">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-full py-10 bg-slate-50 border border-slate-100 rounded-3xl text-center">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic italic uppercase tracking-widest leading-relaxed italic uppercase tracking-widest leading-relaxed italic uppercase tracking-widest leading-relaxed">Hệ thống chưa ghi nhận các tiện ích phục vụ.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Media Assets Section -->
                <div class="space-y-8 bg-slate-50/30 p-10 rounded-[3rem] border border-slate-100 shadow-inner">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white text-indigo-600 flex items-center justify-center text-sm shadow-sm border border-slate-100">
                                <i class="fa-solid fa-images"></i>
                            </div>
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] italic">Thư viện Perspective</h4>
                        </div>
                        <label class="cursor-pointer group flex items-center gap-3 px-6 py-3 bg-slate-900 text-white rounded-2xl hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 active:scale-95">
                            <span class="text-[10px] font-black uppercase tracking-widest italic group-hover:translate-x-1 transition-transform">Nạp dữ liệu hình ảnh</span>
                            <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                            <input type="file" name="images[]" class="hidden" accept="image/*" multiple>
                        </label>
                    </div>

                    <?php if (!empty($roomType['images'])): ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                            <?php foreach ($roomType['images'] as $img): ?>
                                <div class="relative aspect-square rounded-3xl overflow-hidden group/img shadow-xl border border-white">
                                    <img src="<?= htmlspecialchars($img['image_url'] ?? $img['image_path'] ?? '') ?>" alt="Gallery Asset"
                                         class="w-full h-full object-cover group-hover/img:scale-110 transition-transform duration-1000">
                                    
                                    <?php if (!empty($img['is_primary'])): ?>
                                        <div class="absolute top-4 left-4 px-3 py-1 bg-indigo-600 text-white text-[8px] font-black uppercase rounded-lg shadow-lg tracking-widest italic border border-white/20 z-10">Thumbnail</div>
                                    <?php endif; ?>

                                    <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px] z-20">
                                        <?php if (!empty($img['id'])): ?>
                                            <button type="button" 
                                               onclick="if(confirm('Xác nhận triệt tiêu hình ảnh này khỏi kho tư liệu?')) { 
                                                   const f = document.createElement('form');
                                                   f.method = 'POST';
                                                   f.action = '<?= \App\Core\url('/admin/room-type/' . (int)($roomType['id'] ?? 0) . '/images/' . (int) $img['id'] . '/delete' . ((isset($img['source']) && $img['source'] === 'room_images') ? '?source=room_images' : '')) ?>';
                                                   document.body.appendChild(f);
                                                   f.submit();
                                               }"
                                               class="w-12 h-12 bg-white text-rose-500 rounded-2xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-2xl active:scale-90" title="Delete Insight">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="py-24 text-center flex flex-col items-center gap-6">
                            <div class="w-24 h-24 bg-white border border-slate-100 rounded-[2rem] flex items-center justify-center text-slate-100 text-4xl shadow-inner animate-pulse">
                                <i class="fa-solid fa-panorama"></i>
                            </div>
                            <div class="space-y-2">
                                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest italic">Visual Identity Gap</p>
                                <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest leading-relaxed">No high-fidelity assets have been cataloged for this structure.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Submission Logic -->
                <div class="pt-10 border-t border-slate-50 flex items-center justify-end gap-6 text-center">
                    <button type="reset" class="text-[10px] font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-colors italic">Xóa dữ liệu nháp</button>
                    <button type="submit" id="submitBtn" class="group relative flex items-center gap-4 px-16 py-6 bg-slate-900 text-white rounded-[2.5rem] hover:bg-indigo-600 transition-all duration-300 shadow-2xl shadow-indigo-100 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="text-[11px] font-black uppercase tracking-[0.3em] italic group-hover:translate-x-1 transition-transform"><?= $roomType ? 'Lưu trữ Thay đổi' : 'Công bố Danh mục' ?></span>
                        <i class="fa-solid fa-satellite-dish text-lg group-hover:rotate-12 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('roomTypeForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        const originalHTML = submitBtn.innerHTML;
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="text-[11px] font-black uppercase tracking-widest animate-pulse italic">Đang mã hóa dữ liệu...</span><i class="fa-solid fa-spinner fa-spin text-lg"></i>';
            
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                }
            }, 30000);
        });
    }
});
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('roomTypeForm');
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn ? submitBtn.innerHTML : '';
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
            
            // Optional: add a timeout to re-enable if request takes too long
            setTimeout(function() {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }, 30000);
            
            return true;
        });
    }
});
</script>


