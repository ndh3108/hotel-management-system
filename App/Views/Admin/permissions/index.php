<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-indigo-100 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Danh mục Quyền hạn (Permissions)</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Security Access Control & RBAC Matrix</p>
            </div>
        </div>
        <a href="<?= \App\Core\url('/admin/permissions/create') ?>" class="group flex items-center gap-3 px-8 py-4 bg-slate-900 text-white rounded-2xl hover:bg-indigo-600 transition-all duration-300 shadow-xl shadow-slate-200 active:scale-95 group" data-permission="USER_MANAGE">
            <span class="text-[10px] font-black uppercase tracking-widest italic group-hover:translate-x-1 transition-transform">Khởi tạo Permission</span>
            <i class="fa-solid fa-plus-circle text-lg group-hover:rotate-90 transition-transform"></i>
        </a>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="pl-10 pr-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Identity</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Tên quyền hạn</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Mã Code</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Phân hệ (Module)</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Trạng thái</th>
                        <th class="pl-6 pr-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Quản trị</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (!empty($permissions)): ?>
                        <?php foreach ($permissions as $perm): ?>
                            <tr class="hover:bg-slate-50/50 transition-all duration-300 group/row">
                                <td class="pl-10 pr-6 py-8">
                                    <span class="text-sm font-black text-slate-900 italic">#<?= (int) ($perm['id'] ?? 0) ?></span>
                                </td>
                                <td class="px-6 py-8">
                                    <span class="text-sm font-black text-slate-800 uppercase italic leading-tight"><?= htmlspecialchars((string) ($perm['name'] ?? '')) ?></span>
                                </td>
                                <td class="px-6 py-8">
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[9px] font-black border border-indigo-100 uppercase tracking-widest italic group-hover/row:bg-white transition-colors">
                                        <?= htmlspecialchars((string) ($perm['code'] ?? $perm['name'] ?? '')) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-indigo-400"></div>
                                        <span class="text-xs font-black text-slate-500 uppercase tracking-tight italic"><?= htmlspecialchars((string) ($perm['module'] ?? $perm['block_module'] ?? 'SYSTEM')) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center text-center">
                                    <?php
                                        $isActive = (int) ($perm['is_active'] ?? 1) === 1;
                                        $sClass = $isActive ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100';
                                        $sText = $isActive ? 'ACTIVE' : 'DISABLED';
                                    ?>
                                    <span class="inline-flex items-center px-5 py-2 rounded-2xl border text-[9px] font-black uppercase tracking-widest <?= $sClass ?> italic shadow-sm transition-all group-hover/row:scale-105">
                                        <?= $sText ?>
                                    </span>
                                </td>
                                <td class="pl-6 pr-10 py-8 text-right">
                                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover/row:opacity-100 transition-all transform translate-x-4 group-hover/row:translate-x-0">
                                        <a href="<?= \App\Core\url('/admin/permissions/' . (int) $perm['id'] . '/edit') ?>" class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-indigo-600 transition-all hover:rotate-12 active:scale-90 shadow-lg shadow-slate-200" title="Chỉnh sửa" data-permission="USER_MANAGE">
                                            <i class="fa-solid fa-pen-nib text-xs"></i>
                                        </a>

                                        <form action="<?= \App\Core\url('/admin/permissions/' . (int) $perm['id'] . '/disable') ?>" method="POST" class="inline" onsubmit="return confirm('⚠️ Vô hiệu hóa permission này?');">
                                            <button type="submit" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100 transition-all hover:-rotate-12 active:scale-90" title="Tạm dừng" data-permission="USER_MANAGE">
                                                <i class="fa-solid fa-power-off text-xs"></i>
                                            </button>
                                        </form>

                                        <form action="<?= \App\Core\url('/admin/permissions/' . (int) $perm['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('❌ XÁC NHẬN XÓA PHẦN TỬ NÀY?');">
                                            <button type="submit" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all hover:scale-110 active:scale-90 shadow-xl shadow-rose-100" title="Xóa bỏ" data-permission="USER_MANAGE">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-32 text-center text-center">
                                <div class="flex flex-col items-center gap-6">
                                    <div class="w-24 h-24 bg-slate-50 border border-slate-50 rounded-[2rem] flex items-center justify-center text-slate-100 text-4xl shadow-inner animate-pulse">
                                        <i class="fa-solid fa-shield-slash text-slate-200"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] italic">No permissions established</p>
                                        <p class="text-slate-300 text-[9px] font-bold italic uppercase tracking-widest">Security database is decentralized</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


