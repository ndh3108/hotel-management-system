<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Quản trị Vai trò (Roles)</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Identity Hierarchy & Access Levels</p>
            </div>
        </div>
        <a href="<?= \App\Core\url('/admin/roles/create') ?>" class="group flex items-center gap-3 px-8 py-4 bg-slate-900 text-white rounded-2xl hover:bg-indigo-600 transition-all duration-300 shadow-xl shadow-slate-200 active:scale-95" data-permission="USER_MANAGE">
            <span class="text-[10px] font-black uppercase tracking-widest italic group-hover:translate-x-1 transition-transform">Thêm Vai trò mới</span>
            <i class="fa-solid fa-plus-circle text-lg group-hover:rotate-90 transition-transform"></i>
        </a>
    </div>

    <!-- Roles Table -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="pl-10 pr-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Identity</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Tên vai trò</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Mã Code</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Độ ưu tiên</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Trạng thái</th>
                        <th class="pl-6 pr-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Quản trị</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <tr class="hover:bg-slate-50/50 transition-all duration-300 group/row">
                                <td class="pl-10 pr-6 py-8">
                                    <span class="text-sm font-black text-slate-900 italic">#<?= (int) ($role['id'] ?? 0) ?></span>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center text-[10px] font-black italic">
                                            <?= strtoupper(substr($role['name'] ?? 'R', 0, 1)) ?>
                                        </div>
                                        <span class="text-sm font-black text-slate-800 uppercase italic leading-tight"><?= htmlspecialchars((string) ($role['name'] ?? '')) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-8">
                                    <span class="px-3 py-1 bg-slate-50 text-slate-400 rounded-lg text-[9px] font-black border border-slate-100 uppercase tracking-widest italic group-hover/row:bg-white transition-colors">
                                        <?= htmlspecialchars((string) ($role['code'] ?? $role['name'] ?? '')) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <span class="text-xs font-black text-slate-400 italic">Level <?= (int) ($role['sort_order'] ?? 0) ?></span>
                                </td>
                                <td class="px-6 py-8 text-center text-center">
                                    <?php
                                        $isActive = (int) ($role['is_active'] ?? 1) === 1;
                                        $sClass = $isActive ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100';
                                        $sText = $isActive ? 'ACTIVE' : 'OFF';
                                    ?>
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-2xl border text-[9px] font-black uppercase tracking-widest <?= $sClass ?> italic transition-transform group-hover/row:scale-110">
                                        <?= $sText ?>
                                    </span>
                                </td>
                                <td class="pl-6 pr-10 py-8 text-right">
                                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover/row:opacity-100 transition-all transform translate-x-4 group-hover/row:translate-x-0">
                                        <a href="<?= \App\Core\url('/admin/roles/' . (int) $role['id'] . '/edit') ?>" class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-700 transition-all hover:rotate-12 active:scale-90 shadow-lg shadow-slate-100" title="Chỉnh sửa" data-permission="USER_MANAGE">
                                            <i class="fa-solid fa-pen-nib text-xs"></i>
                                        </a>
                                        <form action="<?= \App\Core\url('/admin/roles/' . (int) $role['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('❗ Xác nhận xóa VAI TRÒ này? Thao tác không thể hoàn tác.');">
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
                                        <i class="fa-solid fa-users-viewfinder text-slate-200"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] italic">No roles defined</p>
                                        <p class="text-slate-300 text-[9px] font-bold italic uppercase tracking-widest">Hierarchy matrix is undefined</p>
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


