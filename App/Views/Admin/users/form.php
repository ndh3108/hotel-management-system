<?php 
$userProfile = $userProfile ?? []; 
$userRoles = $userRoles ?? []; 
?>

<div class="max-w-[1200px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid <?= $user ? 'fa-user-pen' : 'fa-user-plus' ?>"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic"><?= $user ? 'Hiệu chỉnh' : 'Khởi tạo' ?> Người dùng</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic"><?= $user ? 'Update Identity Signature' : 'Deploy New System Identity' ?></p>
            </div>
        </div>
        <a href="<?= \App\Core\url('/admin/users') ?>" class="px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-500 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all flex items-center gap-2 shadow-sm active:scale-95 text-xs uppercase tracking-widest italic">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-12">
            <form action="<?= \App\Core\url($user ? '/admin/users/' . $user['id'] . '/update' : '/admin/users/store') ?>" method="POST" class="space-y-10">
                <?= csrf_field() ?>
                
                <!-- Primary Information Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Họ và Tên thành viên <span class="text-indigo-500">*</span></label>
                        <div class="relative group">
                            <i class="fa-solid fa-id-card absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="text" name="full_name" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner"
                                value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required placeholder="VD: NGUYEN VAN A">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Địa chỉ Email xác thực <span class="text-indigo-500">*</span></label>
                        <div class="relative group">
                            <i class="fa-solid fa-envelope absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="email" name="email" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner"
                                value="<?= htmlspecialchars($user['email'] ?? '') ?>" required placeholder="user@enterprise.com">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Mật mã bảo mật <?= $user ? '<span class="text-amber-500">(Bỏ trống nếu không đổi)</span>' : '<span class="text-indigo-500">*</span>' ?></label>
                        <div class="relative group">
                            <i class="fa-solid fa-key absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="password" name="password" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner"
                                <?= !$user ? 'required' : '' ?> placeholder="••••••••••••">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Số điện thoại liên lạc</label>
                        <div class="relative group">
                            <i class="fa-solid fa-phone absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="text" name="phone" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner"
                                value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="0901xxxxxx">
                        </div>
                    </div>
                </div>

                <!-- Roles & Privileges Section -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-50 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-sm">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] italic">Ma trận Phân quyền & Vai trò</h4>
                        </div>
                        <span class="px-4 py-1 bg-slate-50 rounded-full text-[9px] font-black text-slate-400 uppercase tracking-widest italic border border-slate-100 italic uppercase tracking-widest leading-relaxed">System ACL Cluster</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/30 p-8 rounded-[2.5rem] border border-slate-100 shadow-inner max-h-[400px] overflow-y-auto custom-scrollbar">
                        <?php foreach ($roles as $role): ?>
                            <?php 
                                $isChecked = isset($userRoles[$role['id']]) || (!$user && $role['id']==3); 
                                $expiryDate = $userRoles[$role['id']] ?? '';
                            ?>
                            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-4 group hover:border-indigo-200 transition-all">
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center gap-4 cursor-pointer group/label">
                                        <div class="relative">
                                            <input type="checkbox" name="roles[]" value="<?= $role['id'] ?>" <?= $isChecked ? 'checked' : '' ?> class="sr-only peer">
                                            <div class="w-6 h-6 rounded-lg border-2 border-slate-200 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center group-hover/label:border-indigo-300">
                                                <i class="fa-solid fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                            </div>
                                        </div>
                                        <span class="text-[11px] font-black text-slate-800 uppercase italic group-hover/label:text-indigo-600 transition-colors"><?= htmlspecialchars($role['name']) ?></span>
                                    </label>
                                    <div class="px-3 py-1 bg-slate-50 text-slate-400 text-[8px] font-black uppercase rounded-lg border border-slate-100 tracking-widest italic"><?= htmlspecialchars($role['code'] ?? 'ROLE') ?></div>
                                </div>
                                <div class="flex items-center gap-3 pt-4 border-t border-slate-50">
                                    <span class="text-[9px] font-black text-slate-400 italic uppercase">Hết hạn:</span>
                                    <input type="date" name="role_expiries[<?= $role['id'] ?>]" 
                                           class="flex-1 bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-[10px] font-black text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-100 transition-all italic tracking-widest"
                                           value="<?= htmlspecialchars($expiryDate) ?>" title="Để trống nếu quyền vĩnh viễn">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Status & Notes -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Trạng thái định danh</label>
                        <select name="status" class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm italic uppercase tracking-tight shadow-inner appearance-none">
                            <?php $statusValue = (string) ($user['status'] ?? '1'); ?>
                            <option value="1" <?= ($statusValue === '1' || $statusValue === 'active') ? 'selected' : '' ?>>ACTIVE (Hoạt động)</option>
                            <option value="0" <?= ($statusValue === '0' || $statusValue === 'inactive') ? 'selected' : '' ?>>LOCKED (Bị khóa)</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Ghi chú vận hành (Nội bộ)</label>
                        <textarea name="admin_note" rows="1" class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-medium text-slate-600 text-sm leading-relaxed shadow-inner placeholder:italic" placeholder="Thông tinh ghi chú mật cho quản trị viên..."><?= htmlspecialchars($userProfile['admin_note'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-10 border-t border-slate-50 flex items-center justify-end gap-6">
                    <button type="reset" class="text-[10px] font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-colors italic">Làm mới Form</button>
                    <button type="submit" class="group flex items-center gap-4 px-12 py-5 bg-slate-900 text-white rounded-[2rem] hover:bg-indigo-600 transition-all duration-300 shadow-2xl shadow-indigo-100 active:scale-95">
                        <span class="text-[11px] font-black uppercase tracking-[0.2em] italic group-hover:translate-x-1 transition-transform"><?= $user ? 'Cập nhật định danh' : 'Đưa vào vận hành' ?></span>
                        <i class="fa-solid fa-rocket text-lg group-hover:rotate-12 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.02); }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>

