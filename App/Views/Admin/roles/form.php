<?php
$role = $role ?? [];
$permissionMatrix = $permissionMatrix ?? [];
$isEdit = !empty($role);
$selectedPermissionIds = [];
foreach (($permissionMatrix ?? []) as $modulePerms) {
    foreach ($modulePerms as $perm) {
        if (!empty($perm['checked'])) {
            $selectedPermissionIds[] = (int) ($perm['id'] ?? 0);
        }
    }
}
$selectedPermissionIds = array_values(array_unique($selectedPermissionIds));
?>

<div class="max-w-[1200px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid <?= $isEdit ? 'fa-user-gear' : 'fa-user-shield' ?>"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic"><?= $isEdit ? 'Hiệu chỉnh' : 'Thiết lập' ?> Vai trò</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic"><?= $isEdit ? 'Refining Access Authority' : 'Defining New Security Profile' ?></p>
            </div>
        </div>
        <a href="<?= \App\Core\url('/admin/roles') ?>" class="px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-500 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all flex items-center gap-2 shadow-sm active:scale-95 text-xs uppercase tracking-widest italic">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-12">
            <form action="<?= \App\Core\url($isEdit ? '/admin/roles/' . (int) $role['id'] . '/update' : '/admin/roles/store') ?>" method="POST" class="space-y-10">
                <?= csrf_field() ?>
                
                <!-- Role Definition Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Tên định danh vai trò <span class="text-indigo-500">*</span></label>
                        <div class="relative group">
                            <i class="fa-solid fa-tag absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="text" name="name" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner"
                                value="<?= htmlspecialchars((string) ($role['name'] ?? '')) ?>" required placeholder="VD: ADMIN CAO CẤP, KẾ TOÁN...">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Mã nghiệp vụ (Role Code)</label>
                        <div class="relative group border-none">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-[10px] font-black uppercase tracking-widest">CODE/</div>
                            <input type="text" name="code" class="w-full pl-20 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-500 text-sm italic shadow-inner"
                                value="<?= htmlspecialchars((string) ($role['code'] ?? '')) ?>" placeholder="Tự động tạo nếu để trống">
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Mô tả đặc quyền & Trách nhiệm</label>
                    <textarea name="description" rows="3" class="w-full px-8 py-6 rounded-[2.5rem] border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-medium text-slate-600 text-sm leading-relaxed shadow-inner placeholder:italic italic uppercase tracking-widest leading-relaxed" placeholder="Ghi chú về phạm vi ảnh hưởng của vai trò này trong hệ thống..."><?= htmlspecialchars((string) ($role['description'] ?? '')) ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Trạng thái vận hành</label>
                        <select name="is_active" class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm italic uppercase tracking-tight shadow-inner appearance-none">
                            <option value="1" <?= ((string)($role['is_active'] ?? '1') === '1' || (int)($role['is_active'] ?? 1) === 1) ? 'selected' : '' ?>>ACTIVE (Hoạt động)</option>
                            <option value="0" <?= ((string)($role['is_active'] ?? '') === '0' || (int)($role['is_active'] ?? 1) === 0) ? 'selected' : '' ?>>DISABLED (Tam tắt)</option>
                        </select>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Thứ tự ưu tiên hiển thị</label>
                        <input type="number" name="sort_order" class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm shadow-inner" 
                            value="<?= (int) ($role['sort_order'] ?? 0) ?>">
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50 flex items-center justify-end gap-6">
                    <button type="reset" class="text-[10px] font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-colors italic">Làm mới Form</button>
                    <button type="submit" class="group flex items-center gap-4 px-12 py-5 bg-slate-900 text-white rounded-[2rem] hover:bg-indigo-600 transition-all duration-300 shadow-2xl shadow-indigo-100 active:scale-95">
                        <span class="text-[11px] font-black uppercase tracking-[0.2em] italic group-hover:translate-x-1 transition-transform"><?= $isEdit ? 'Cập nhật định danh' : 'Đưa vào vận hành' ?></span>
                        <i class="fa-solid <?= $isEdit ? 'fa-save' : 'fa-rocket' ?> text-lg group-hover:rotate-12 transition-transform"></i>
                    </button>
                </div>
            </form>

            <?php if ($isEdit): ?>
                <div class="mt-20 pt-20 border-t border-slate-100 space-y-12">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-slate-900 uppercase tracking-tight italic">Phân quyền chi tiết (ACL Matrix)</h4>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1 italic">Granular Access Control Policies</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 px-4 py-2 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Live Security Synchronization</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <?php if (!empty($permissionMatrix)): ?>
                            <?php foreach ($permissionMatrix as $module => $perms): ?>
                                <div class="bg-slate-50/50 p-8 rounded-[2.5rem] border border-slate-100 space-y-6">
                                    <div class="flex items-center gap-3 px-3">
                                        <div class="w-2 h-6 bg-indigo-500 rounded-full"></div>
                                        <h5 class="text-[11px] font-black text-slate-900 uppercase tracking-[0.2em] italic"><?= htmlspecialchars((string) $module) ?></h5>
                                    </div>
                                    <div class="grid grid-cols-1 gap-3">
                                        <?php foreach ($perms as $perm): ?>
                                            <?php $pid = (int) ($perm['id'] ?? 0); ?>
                                            <label class="group flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:border-indigo-300 transition-all cursor-pointer relative overflow-hidden">
                                                <div class="flex items-center gap-4 relative z-10">
                                                    <div class="relative">
                                                        <input type="checkbox" data-permission-id="<?= $pid ?>" <?= !empty($perm['checked']) ? 'checked' : '' ?> class="sr-only peer">
                                                        <div class="w-6 h-6 rounded-lg border-2 border-slate-100 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center">
                                                            <i class="fa-solid fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                                        </div>
                                                    </div>
                                                    <span class="text-[11px] font-black text-slate-600 uppercase group-hover:text-slate-900 transition-colors italic"><?= htmlspecialchars((string) ($perm['name'] ?? ($perm['code'] ?? ''))) ?></span>
                                                </div>
                                                <div class="text-[9px] font-black text-slate-300 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity italic">Sync on change</div>
                                                <div class="absolute right-0 bottom-0 opacity-0 group-hover:opacity-5 transition-opacity italic">
                                                    <i class="fa-solid fa-shield-halved text-4xl rotate-12"></i>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-full py-32 text-center flex flex-col items-center gap-6">
                                <div class="w-20 h-20 bg-slate-50 border border-slate-100 rounded-[2rem] flex items-center justify-center text-slate-200 text-3xl">
                                    <i class="fa-solid fa-shield-slash"></i>
                                </div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] italic uppercase tracking-widest leading-relaxed">Không tìm thấy kho nghiệp vụ quyền hạn tương ứng.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($isEdit): ?>
<script>
    const ROLE_ID = <?= (int) ($role['id'] ?? 0) ?>;
    const SELECTED_PERMISSION_IDS = new Set(<?= json_encode($selectedPermissionIds) ?>);

    async function updateRolePermissions(permissionIds) {
        try {
            const res = await fetch('<?= \App\Core\url('/api/permissions-to-role') ?>', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ role_id: ROLE_ID, permission_ids: permissionIds }),
            });

            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) {
                alert(data.message || 'Hệ thống từ chối cập nhật đặc quyền trực tuyến.');
                return false;
            }
            return true;
        } catch (e) {
            alert('Lỗi kết nối hạ tầng bảo mật.');
            return false;
        }
    }

    document.addEventListener('change', async (e) => {
        const target = e.target;
        if (!target || !target.matches('input[type="checkbox"][data-permission-id]')) return;

        const permissionId = parseInt(target.dataset.permissionId, 10);
        if (Number.isNaN(permissionId)) return;

        // Visual feedback
        const parentLabel = target.closest('label');
        parentLabel.classList.add('opacity-50', 'pointer-events-none', 'grayscale');

        if (target.checked) {
            SELECTED_PERMISSION_IDS.add(permissionId);
        } else {
            SELECTED_PERMISSION_IDS.delete(permissionId);
        }

        const success = await updateRolePermissions(Array.from(SELECTED_PERMISSION_IDS));
        
        if (!success) {
            target.checked = !target.checked; // Rollback
            if (target.checked) SELECTED_PERMISSION_IDS.add(permissionId);
            else SELECTED_PERMISSION_IDS.delete(permissionId);
        }

        parentLabel.classList.remove('opacity-50', 'pointer-events-none', 'grayscale');
    });
</script>
<?php endif; ?>


