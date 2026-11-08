<?php
$userProfile = $userProfile ?? [];
$userRoles = $userRoles ?? [];
$activities = $activities ?? [];
$sessions = $sessions ?? [];
$isEdit = !empty($user);
$statusValue = (string) ($user['status'] ?? '1');
?>

<div class="max-w-[1400px] mx-auto space-y-10 animate-fade-in pb-20">
    <!-- Action Metrics Header -->
    <?php if ($isEdit): ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-indigo-900 p-6 rounded-[2.5rem] shadow-xl shadow-indigo-100 relative overflow-hidden group border-none">
                <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform rotate-12 duration-500 text-white font-black text-7xl">
                    <?= (int) ($user['profile_completion_percent'] ?? 0) ?>%
                </div>
                <div class="relative z-10">
                    <p class="text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-1">Mức độ hoàn thiện hồ sơ</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-white leading-none"><?= (int) ($user['profile_completion_percent'] ?? 0) ?>%</h3>
                    </div>
                    <div class="mt-4 w-full h-1 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-white rounded-full transition-all duration-1000" style="width: <?= (int) ($user['profile_completion_percent'] ?? 0) ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-emerald-600">
                    <i class="fa-solid fa-envelope-circle-check text-6xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Xác minh danh tính</p>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full <?= !empty($user['email_verified_at']) ? 'bg-emerald-500' : 'bg-amber-500' ?>"></span>
                            <span class="text-[10px] font-black uppercase tracking-widest <?= !empty($user['email_verified_at']) ? 'text-emerald-600' : 'text-amber-600' ?>">Email: <?= !empty($user['email_verified_at']) ? 'Hợp lệ' : 'Chờ xác thực' ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full <?= !empty($user['phone_verified_at']) ? 'bg-emerald-500' : 'bg-slate-300' ?>"></span>
                            <span class="text-[10px] font-black uppercase tracking-widest <?= !empty($user['phone_verified_at']) ? 'text-emerald-600' : 'text-slate-400' ?>">SĐT: <?= !empty($user['phone_verified_at']) ? 'Hợp lệ' : 'Chưa cập nhật' ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-indigo-600">
                    <i class="fa-solid fa-clock-rotate-left text-6xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Truy cập cuối cùng</p>
                    <h3 class="text-lg font-black text-slate-800 leading-tight"><?= htmlspecialchars((string) ($user['last_login_at'] ?? 'Chưa ghi nhận')) ?></h3>
                    <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mt-2"><?= htmlspecialchars((string) ($user['last_login_ip'] ?? '0.0.0.0')) ?></p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-slate-900">
                    <i class="fa-solid fa-fingerprint text-6xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Định danh hệ thống</p>
                    <h3 class="text-xl font-black text-slate-800 leading-none">#<?= str_pad((string)($user['id'] ?? 0), 6, '0', STR_PAD_LEFT) ?></h3>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-4">UUID: <?= substr(md5((string)($user['id'] ?? 0)), 0, 12) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form action="<?= \App\Core\url($isEdit ? '/admin/users/' . (int) $user['id'] . '/update' : '/admin/users/store') ?>" method="POST">
        <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
            <!-- Left Column: Primary Data (8/12) -->
            <div class="xl:col-span-8 space-y-10">
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <div class="p-10 space-y-10">
                        <div class="flex items-center gap-4 border-b border-slate-50 pb-8">
                            <a href="<?= \App\Core\url('/admin/users') ?>" class="w-12 h-12 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-500 rounded-2xl transition-all active:scale-90 shadow-sm border border-slate-100">
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight"><?= $isEdit ? 'Hiệu chỉnh tài khoản' : 'Khởi tạo nhân sự mới' ?></h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Cấu hình thông tin định danh và bảo mật tài khoản</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Họ và tên đầy đủ <span class="text-rose-500">*</span></label>
                                <input type="text" name="full_name" class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-black text-slate-800 text-lg placeholder:font-medium" value="<?= htmlspecialchars((string) ($user['full_name'] ?? '')) ?>" placeholder="VD: Nguyễn Văn A..." required>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Địa chỉ Email liên hệ <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-black text-slate-800 text-lg" value="<?= htmlspecialchars((string) ($user['email'] ?? '')) ?>" placeholder="email@example.com" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mật khẩu truy cập <?= $isEdit ? '(để trống nếu giữ nguyên)' : '<span class="text-rose-500">*</span>' ?></label>
                                <div class="relative">
                                    <input type="password" name="password" class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-black text-slate-800" <?= $isEdit ? '' : 'required' ?> placeholder="••••••••">
                                    <i class="fa-solid fa-lock absolute right-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Số điện thoại</label>
                                <div class="relative">
                                    <input type="text" name="phone" class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-black text-slate-800" value="<?= htmlspecialchars((string) ($user['phone'] ?? '')) ?>" placeholder="09xx xxx xxx">
                                    <i class="fa-solid fa-phone absolute right-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-50 text-indigo-500 rounded-xl flex items-center justify-center">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Ma trận phân quyền (Multi-role)</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-3 max-h-[400px] overflow-y-auto pr-2 thin-scrollbar bg-slate-50/50 p-6 rounded-[2rem] border border-slate-100">
                                <?php foreach ($roles as $role): ?>
                                    <?php
                                        $isChecked = isset($userRoles[$role['id']]) || (!$isEdit && (int) $role['id'] === 3);
                                        $expiryDate = $userRoles[$role['id']] ?? '';
                                    ?>
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 rounded-2xl bg-white border border-transparent hover:border-indigo-100 hover:shadow-md transition-all group">
                                        <label class="flex items-center gap-4 cursor-pointer flex-1">
                                            <div class="relative">
                                                <input type="checkbox" name="roles[]" value="<?= (int) $role['id'] ?>" <?= $isChecked ? 'checked' : '' ?> class="w-6 h-6 rounded-lg border-2 border-slate-200 text-indigo-600 focus:ring-indigo-500 transition-all">
                                            </div>
                                            <div>
                                                <span class="text-xs font-black text-slate-700 group-hover:text-indigo-600 transition-colors uppercase tracking-widest"><?= htmlspecialchars((string) $role['name']) ?></span>
                                                <?php if ((int) ($role['is_system'] ?? 0) === 1): ?>
                                                    <span class="ml-2 px-1.5 py-0.5 rounded bg-slate-900 text-white text-[8px] font-black uppercase tracking-tighter">System</span>
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                        <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl group-focus-within:bg-indigo-50 transition-colors">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Hết hạn:</span>
                                            <input type="date" name="role_expiries[<?= (int) $role['id'] ?>]" class="bg-transparent border-none p-0 text-[10px] font-black text-slate-600 focus:ring-0 outline-none w-32" value="<?= htmlspecialchars((string) $expiryDate) ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Status & Operational Logs (4/12) -->
            <div class="xl:col-span-4 space-y-10">
                <!-- Status & Action Card -->
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden sticky top-6">
                    <div class="p-10 space-y-8">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Trạng thái vận hành tài khoản</label>
                            <select name="status" class="w-full px-6 py-5 rounded-[1.5rem] border border-slate-100 bg-indigo-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-indigo-700 text-lg uppercase tracking-tight shadow-inner">
                                <option value="1" <?= ($statusValue === '1' || $statusValue === 'active') ? 'selected' : '' ?>>ĐANG HOẠT ĐỘNG</option>
                                <option value="0" <?= ($statusValue === '0' || $statusValue === 'inactive') ? 'selected' : '' ?>>ĐANG BỊ KHÓA</option>
                            </select>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Ghi chú quản trị viên (Nội bộ)</label>
                            <textarea name="admin_note" class="w-full px-6 py-5 rounded-[2rem] border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none min-h-[100px] text-slate-700 text-xs font-medium leading-relaxed" placeholder="Lưu ý về kỷ luật, bảo mật hoặc thông tin nội bộ..."><?= htmlspecialchars((string) ($userProfile['admin_note'] ?? '')) ?></textarea>
                        </div>

                        <div class="flex flex-col gap-4 pt-4 border-t border-slate-50">
                            <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-[1.5rem] transition-all shadow-2xl shadow-indigo-200 flex items-center justify-center gap-3 active:scale-95 uppercase text-xs tracking-widest leading-none">
                                <i class="fa-solid fa-save text-sm"></i>
                                <?= $isEdit ? 'Cập nhật tài khoản' : 'Khởi tạo ngay' ?>
                            </button>
                            <a href="<?= \App\Core\url('/admin/users') ?>" class="w-full py-4 bg-slate-50 hover:bg-slate-100 text-slate-500 font-black rounded-[1.5rem] transition-all text-center uppercase text-[10px] tracking-widest border border-slate-100">Hủy bỏ</a>
                        </div>
                    </div>
                </div>

                <!-- Activity & Session Logs Suite -->
                <?php if ($isEdit): ?>
                    <div class="space-y-10">
                        <!-- Activity History -->
                        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
                            <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-bolt-lightning"></i>
                                    </div>
                                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Hoạt động gần đây</h3>
                                </div>
                            </div>
                            <div class="p-0">
                                <?php if (!empty($activities)): ?>
                                    <div class="divide-y divide-slate-50 max-h-[350px] overflow-y-auto thin-scrollbar px-2">
                                        <?php foreach ($activities as $activity): ?>
                                            <div class="p-6 hover:bg-slate-50 transition-all group cursor-default">
                                                <div class="flex items-start justify-between mb-2">
                                                    <span class="text-[9px] font-black uppercase text-indigo-500 tracking-widest"><?= htmlspecialchars((string) ($activity['action'] ?? 'LOG')) ?></span>
                                                    <span class="text-[8px] font-black uppercase text-slate-300 tracking-tighter"><?= date('H:i d/m/Y', strtotime($activity['created_at'])) ?></span>
                                                </div>
                                                <p class="text-[11px] font-bold text-slate-600 leading-relaxed"><?= htmlspecialchars((string) ($activity['description'] ?? '')) ?></p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="p-16 text-center text-slate-400 space-y-4">
                                        <i class="fa-solid fa-ghost text-4xl opacity-10"></i>
                                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Không có dấu vết</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Session History -->
                        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
                            <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-sky-50 text-sky-500 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-key"></i>
                                    </div>
                                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Phiên đăng nhập</h3>
                                </div>
                            </div>
                            <div class="p-0">
                                <?php if (!empty($sessions)): ?>
                                    <div class="divide-y divide-slate-50 max-h-[350px] overflow-y-auto thin-scrollbar px-2">
                                        <?php foreach ($sessions as $session): ?>
                                            <?php $isClosed = !empty($session['logged_out_at']); ?>
                                            <div class="p-6 hover:bg-slate-50 transition-all group relative">
                                                <div class="absolute left-0 top-6 bottom-6 w-1 <?= $isClosed ? 'bg-slate-200' : 'bg-emerald-500 animate-pulse' ?> rounded-r-full"></div>
                                                <div class="flex items-center justify-between mb-2 pl-2">
                                                    <span class="text-[10px] font-black text-slate-800 tracking-tight uppercase"><?= htmlspecialchars((string) ($session['ip_address'] ?? '0.0.0.0')) ?></span>
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-[8px] font-black uppercase tracking-widest <?= $isClosed ? 'text-slate-400' : 'text-emerald-600 bg-emerald-50 border border-emerald-100' ?>">
                                                        <?= $isClosed ? 'ĐÃ ĐÓNG' : 'TRỰC TUYẾN' ?>
                                                    </span>
                                                </div>
                                                <div class="flex flex-col gap-1 pl-2">
                                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Hoạt động: <span class="text-slate-600"><?= date('H:i d/m/Y', strtotime($session['last_activity_at'])) ?></span></p>
                                                    <?php if ($isClosed): ?>
                                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Kết thúc: <span class="text-slate-600"><?= date('H:i d/m/Y', strtotime($session['logged_out_at'])) ?></span></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="p-16 text-center text-slate-400 space-y-4">
                                        <i class="fa-solid fa-server text-4xl opacity-10"></i>
                                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Chưa ghi nhận</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<style>
    .thin-scrollbar::-webkit-scrollbar { width: 4px; }
    .thin-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .thin-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .thin-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>


