<?php
$permission = $permission ?? [];
$isEdit = !empty($permission);
?>

<div class="max-w-[1200px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid <?= $isEdit ? 'fa-key-skeleton' : 'fa-plus-circle' ?>"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic"><?= $isEdit ? 'Hiệu chỉnh' : 'Khởi tạo' ?> Quyền hạn (ACL Item)</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic"><?= $isEdit ? 'Update Granular Access Policy' : 'Deploy New Security Permission' ?></p>
            </div>
        </div>
        <a href="<?= \App\Core\url('/admin/permissions') ?>" class="px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-500 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all flex items-center gap-2 shadow-sm active:scale-95 text-xs uppercase tracking-widest italic">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-12">
            <form action="<?= \App\Core\url($isEdit ? '/admin/permissions/' . (int)$permission['id'] . '/update' : '/admin/permissions/store') ?>" method="POST" class="space-y-10">
                <?= csrf_field() ?>
                
                <!-- core configuration -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Tên quyền hạn <span class="text-indigo-500">*</span></label>
                        <div class="relative group">
                            <i class="fa-solid fa-signature absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="text" name="name" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner"
                                value="<?= htmlspecialchars((string) ($permission['name'] ?? '')) ?>" required placeholder="VD: XEM_BAO_CAO, QUAN_LY_PHONG...">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Mã kỹ thuật (Permission Code)</label>
                        <div class="relative group border-none">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-[10px] font-black uppercase tracking-widest">CODE/</div>
                            <input type="text" name="code" class="w-full pl-20 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-500 text-sm italic shadow-inner"
                                value="<?= htmlspecialchars((string) ($permission['code'] ?? '')) ?>" placeholder="Tự động tạo nếu để trống">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Module / Phân hệ nghiệp vụ</label>
                        <div class="relative group">
                            <i class="fa-solid fa-cubes absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="text" name="module" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner"
                                value="<?= htmlspecialchars((string) ($permission['module'] ?? ($permission['block_module'] ?? ''))) ?>" placeholder="VD: BOOKING, USER, SYSTEM...">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Trạng thái định mức</label>
                        <select name="is_active" class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm italic uppercase tracking-tight shadow-inner appearance-none">
                            <?php $activeVal = (string) ($permission['is_active'] ?? 1); ?>
                            <option value="1" <?= $activeVal === '1' ? 'selected' : '' ?>>ACTIVE (Kích hoạt)</option>
                            <option value="0" <?= $activeVal === '0' ? 'selected' : '' ?>>DISABLED (Tam tắt)</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Giải trình phạm vi tác động (Description)</label>
                    <textarea name="description" rows="3" class="w-full px-8 py-6 rounded-[2.5rem] border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-medium text-slate-600 text-sm leading-relaxed shadow-inner placeholder:italic italic uppercase tracking-widest leading-relaxed" placeholder="Ghi chú chi tiết về quyền hạn này..."><?= htmlspecialchars((string) ($permission['description'] ?? '')) ?></textarea>
                </div>

                <!-- Submit Action -->
                <div class="pt-6 border-t border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <?php if ($isEdit): ?>
                        <div class="flex items-center gap-4">
                            <form action="<?= \App\Core\url('/admin/permissions/' . (int)$permission['id'] . '/disable') ?>" method="POST" onsubmit="return confirm('Vô hiệu hóa quyền hạn này?');">
                                <button type="submit" class="px-6 py-4 rounded-2xl bg-slate-50 text-slate-400 font-black hover:bg-amber-50 hover:text-amber-600 transition-all flex items-center gap-2 border border-slate-100 italic uppercase tracking-widest text-[9px]">
                                    <i class="fa-solid fa-ban"></i> Tạm tắt
                                </button>
                            </form>
                            <form action="<?= \App\Core\url('/admin/permissions/' . (int)$permission['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Truy hồi và xóa bỏ quyền hạn này? Thao tác không thể hoàn tác.');">
                                <button type="submit" class="px-6 py-4 rounded-2xl bg-slate-50 text-slate-400 font-black hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center gap-2 border border-slate-100 italic uppercase tracking-widest text-[9px]">
                                    <i class="fa-solid fa-trash"></i> Xóa bỏ tài nguyên
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>

                    <div class="flex items-center gap-6">
                        <button type="reset" class="text-[10px] font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-colors italic">Làm mới Form</button>
                        <button type="submit" class="group flex items-center gap-4 px-12 py-5 bg-slate-900 text-white rounded-[2rem] hover:bg-indigo-600 transition-all duration-300 shadow-2xl shadow-indigo-100 active:scale-95">
                            <span class="text-[11px] font-black uppercase tracking-[0.2em] italic group-hover:translate-x-1 transition-transform"><?= $isEdit ? 'Cập nhật cấu trúc' : 'Phát hành quyền hạn' ?></span>
                            <i class="fa-solid <?= $isEdit ? 'fa-save' : 'fa-rocket' ?> text-lg group-hover:rotate-12 transition-transform"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


