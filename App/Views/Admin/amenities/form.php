<?php
$amenity = $amenity ?? null;
$isEdit = !empty($amenity);
?>

<div class="max-w-[1000px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid <?= $isEdit ? 'fa-wand-sparkles' : 'fa-plus-circle' ?>"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic"><?= $isEdit ? 'Hiệu chỉnh tiện nghi' : 'Khởi tạo tiện nghi' ?></h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Definining System Resource Characteristics</p>
            </div>
        </div>
        <a href="<?= \App\Core\url('/admin/amenities') ?>" class="px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-500 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all flex items-center gap-2 shadow-sm active:scale-95 text-xs uppercase tracking-widest italic">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-12">
            <form action="<?= \App\Core\url($isEdit ? '/admin/amenities/' . $amenity['id'] . '/update' : '/admin/amenities/store') ?>" method="POST" class="space-y-12">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                    <!-- Left: Basic Info -->
                    <div class="md:col-span-12 space-y-8">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Tên Tiện Nghi / Trang thiết bị <span class="text-indigo-500">*</span></label>
                            <div class="relative group">
                                <i class="fa-solid fa-tag absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                                <input type="text" name="name" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-lg uppercase tracking-tight shadow-inner" 
                                    value="<?= htmlspecialchars($amenity['name'] ?? '') ?>" placeholder="VD: Wi-Fi Miễn Phí, Hồ Bơi Vô Cực..." required>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Định danh Biểu tượng (Font Awesome)</label>
                            <div class="flex items-start gap-6">
                                <div class="w-24 h-24 rounded-[2rem] bg-slate-900 text-white flex items-center justify-center text-3xl shadow-2xl shadow-indigo-100 transition-transform duration-500 hover:rotate-6 shrink-0" id="icon-preview">
                                    <i class="<?= htmlspecialchars($amenity['icon_url'] ?? 'fa-solid fa-check') ?>"></i>
                                </div>
                                <div class="flex-1 space-y-4">
                                    <div class="relative group">
                                        <i class="fa-solid fa-code absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                                        <input type="text" name="icon_url" id="icon-input" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-600 text-sm italic shadow-inner" 
                                            value="<?= htmlspecialchars($amenity['icon_url'] ?? 'fa-solid fa-check') ?>" placeholder="VD: fa-solid fa-wifi">
                                    </div>
                                    <div class="flex items-center gap-3 px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl">
                                        <i class="fa-solid fa-circle-info text-indigo-400 text-[10px]"></i>
                                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic">Tra cứu mã biểu tượng tại <a href="https://fontawesome.com/icons" target="_blank" class="text-indigo-600 hover:underline">FontAwesome.com</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Action Section -->
                <div class="pt-10 border-t border-slate-50 flex items-center justify-end gap-6 text-center">
                    <button type="reset" class="text-[10px] font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-colors italic">Làm mới dữ liệu</button>
                    <button type="submit" class="group flex items-center gap-4 px-12 py-5 bg-slate-900 text-white rounded-[2rem] hover:bg-indigo-600 transition-all duration-300 shadow-2xl shadow-indigo-100 active:scale-95">
                        <span class="text-[11px] font-black uppercase tracking-[0.2em] italic group-hover:translate-x-1 transition-transform"><?= $isEdit ? 'Cập nhật tiện nghi' : 'Thiết lập ngay' ?></span>
                        <i class="fa-solid fa-save text-lg group-hover:rotate-12 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('icon-input').addEventListener('input', function(e) {
    const iconClass = e.target.value || 'fa-solid fa-check';
    const preview = document.getElementById('icon-preview');
    preview.innerHTML = `<i class="${iconClass}"></i>`;
});
</script>

