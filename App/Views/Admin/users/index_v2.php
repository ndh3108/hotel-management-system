<?php
$users = $users ?? [];
$totalUsers = count($users);
$activeUsers = count(array_filter($users, fn($u) => ($u['status'] ?? 1) == 1 || ($u['status'] ?? '') === 'active'));
$lockedUsers = $totalUsers - $activeUsers;
$verifiedUsers = count(array_filter($users, fn($u) => !empty($u['email_verified_at'])));
?>

<div class="space-y-8 animate-fade-in pb-20">
    <!-- Action Header -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-3 px-4 py-2 bg-indigo-50 rounded-2xl border border-indigo-100">
            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest leading-none"><?= $totalUsers ?> tài khoản hệ thống</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= \App\Core\url('/admin/users/create') ?>" class="px-6 py-3.5 rounded-2xl bg-indigo-600 text-white font-black hover:bg-indigo-500 transition-all flex items-center gap-2 shadow-xl shadow-indigo-100 active:scale-95 text-xs uppercase tracking-widest">
                <i class="fa-solid fa-user-plus"></i> Tạo tài khoản
            </a>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-indigo-900 p-6 rounded-[2.5rem] shadow-xl shadow-indigo-100 relative overflow-hidden group border-none">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform rotate-12 duration-500 text-white">
                <i class="fa-solid fa-users text-7xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-1">Tổng thành viên</p>
                <h3 class="text-3xl font-black text-white leading-none"><?= number_format($totalUsers) ?></h3>
                <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-4">System Identity</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-emerald-600">
                <i class="fa-solid fa-user-check text-7xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Đang hoạt động</p>
                <h3 class="text-3xl font-black text-emerald-600 leading-none"><?= number_format($activeUsers) ?></h3>
                <p class="text-[9px] font-black text-emerald-500/50 uppercase tracking-widest mt-4">Authorized Access</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-rose-600">
                <i class="fa-solid fa-user-lock text-7xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Tài khoản bị khóa</p>
                <h3 class="text-3xl font-black text-rose-600 leading-none"><?= number_format($lockedUsers) ?></h3>
                <p class="text-[9px] font-black text-rose-500/50 uppercase tracking-widest mt-4">Restricted Access</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group text-slate-400">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-indigo-600">
                <i class="fa-solid fa-envelope-circle-check text-7xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Đã xác minh Email</p>
                <h3 class="text-3xl font-black text-indigo-600 leading-none"><?= number_format($verifiedUsers) ?></h3>
                <p class="text-[9px] font-black text-indigo-500/50 uppercase tracking-widest mt-4">Verified Profiles</p>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-10 border-b border-slate-50 flex flex-col lg:flex-row lg:items-center justify-between gap-8 bg-slate-50/20">
            <div>
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Cơ sở dữ liệu người dùng</h2>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Danh sách định danh và phân quyền truy cập hệ thống</p>
            </div>
            
            <form method="GET" action="<?= \App\Core\url('/admin/users') ?>" class="flex flex-wrap items-center gap-4">
                <select name="role_id" class="px-6 py-3.5 rounded-2xl border border-slate-200 bg-white text-xs font-black outline-none focus:ring-8 focus:ring-indigo-500/5 transition-all uppercase tracking-widest" onchange="this.form.submit()">
                    <option value="">Tất cả vai trò</option>
                    <?php foreach ($roles ?? [] as $role): ?>
                        <option value="<?= (int) $role['id'] ?>" <?= (isset($selectedRole) && (int) $selectedRole === (int) $role['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $role['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="relative min-w-[280px]">
                    <input type="text" name="search" value="<?= htmlspecialchars((string)($_GET['search'] ?? '')) ?>" placeholder="Tìm tên, email..." class="w-full pl-10 pr-4 py-3.5 rounded-2xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all uppercase tracking-wider">
                    <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-[10px]"></i>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/50">
                        <th class="px-10 py-6 text-left">Định danh & Hồ sơ</th>
                        <th class="px-6 py-6 text-left">Liên hệ & Bảo mật</th>
                        <th class="px-6 py-6 text-left">Phân quyền vai trò</th>
                        <th class="px-6 py-6 text-center">Trạng thái</th>
                        <th class="px-6 py-6 text-center">Hoàn thiện</th>
                        <th class="px-10 py-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <?php
                                $status = $user['status'] ?? 1;
                                $roleAssignments = $user['role_assignments'] ?? [];
                            ?>
                            <tr class="hover:bg-slate-50/80 transition-all group duration-300">
                                <td class="px-10 py-8">
                                    <div class="flex items-center gap-6">
                                        <div class="w-16 h-16 rounded-[1.5rem] bg-indigo-900 text-white flex items-center justify-center font-black text-xl shadow-xl shadow-indigo-100 group-hover:scale-110 transition-transform duration-500 relative">
                                            <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                            <div class="absolute -right-1 -top-1 w-5 h-5 rounded-lg bg-emerald-500 border-2 border-white"></div>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900 text-lg uppercase tracking-tight leading-tight"><?= htmlspecialchars((string) $user['full_name']) ?></p>
                                            <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mt-1.5 flex items-center gap-2">
                                                <span class="w-3 h-[1px] bg-indigo-200"></span>
                                                #<?= str_pad((string)$user['id'], 5, '0', STR_PAD_LEFT) ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2 text-[10px] font-black text-slate-700 uppercase tracking-widest">
                                            <i class="fa-solid fa-envelope text-slate-300 w-4"></i>
                                            <?= htmlspecialchars((string) $user['email']) ?>
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            <i class="fa-solid fa-phone text-slate-300 w-4"></i>
                                            <?= htmlspecialchars((string) ($user['phone'] ?? '-')) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="flex flex-wrap gap-2 max-w-[280px]">
                                        <?php if (!empty($roleAssignments)): ?>
                                            <?php foreach (array_slice($roleAssignments, 0, 3) as $assignment): ?>
                                                <span class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100 text-[9px] font-black uppercase tracking-widest">
                                                    <?= htmlspecialchars((string)$assignment['name']) ?>
                                                </span>
                                            <?php endforeach; ?>
                                            <?php if (count($roleAssignments) > 3): ?>
                                                <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-500 text-[9px] font-black uppercase">+<?= count($roleAssignments) - 3 ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 text-[9px] font-black uppercase tracking-widest italic">Chưa cấp quyền</span>
                                        <?php houses ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <?php 
                                        $isActive = ($status == 1 || $status === 'active');
                                        $sClass = $isActive ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100';
                                    ?>
                                    <span class="inline-flex items-center px-4 py-2 rounded-full border text-[9px] font-black uppercase tracking-widest <?= $sClass ?>">
                                        <span class="w-1.5 h-1.5 rounded-full mr-2 bg-current animate-pulse"></span>
                                        <?= $isActive ? 'Hoạt động' : 'Bị khóa' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-100">
                                        <span class="w-8 text-[11px] font-black text-indigo-600"><?= (int) ($user['profile_completion_percent'] ?? 0) ?>%</span>
                                        <div class="w-12 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-500 rounded-full" style="width: <?= (int) ($user['profile_completion_percent'] ?? 0) ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-8 text-right">
                                    <div class="flex items-center justify-end gap-3 translate-x-2 group-hover:translate-x-0 transition-transform">
                                        <a href="<?= \App\Core\url('/admin/users/' . (int) $user['id'] . '/edit') ?>" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-95">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </a>
                                        <form action="<?= \App\Core\url('/admin/users/' . (int) $user['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Toàn bộ quyền hạn và lịch sử người dùng này sẽ bị hủy bỏ. Tiếp tục?');">
                                            <button type="submit" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm active:scale-95 text-xs">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-10 py-32 text-center bg-slate-50/50 text-slate-300">
                                <i class="fa-solid fa-users-slash text-5xl opacity-20 block mb-6 mx-auto"></i>
                                <h3 class="text-xl font-black uppercase tracking-widest">Danh sách trống</h3>
                                <p class="text-[10px] font-bold uppercase tracking-widest mt-2">Hệ thống chưa ghi nhận tài khoản nhân sự / khách hàng</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php 
    $baseQuery = []; 
    if (!empty($selectedRole)) { $baseQuery['role_id'] = (int) $selectedRole; }
    $queryString = !empty($baseQuery) ? '&' . http_build_query($baseQuery) : '';
    ?>

    <div class="flex items-center justify-between bg-white px-10 py-8 rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
            Hiển thị trang <span class="text-indigo-600"><?= (int)($pageNumber ?? 1) ?></span> tài khoản trực tuyến
        </p>
        <div class="flex items-center gap-3">
            <?php if ($pageNumber > 1): ?>
                <a href="<?= \App\Core\url('/admin/users?page=' . ($pageNumber - 1) . $queryString) ?>" class="px-8 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-600 font-black hover:bg-slate-50 transition-all transform active:scale-95 uppercase text-[10px] tracking-widest flex items-center gap-3">
                    <i class="fa-solid fa-chevron-left text-[8px]"></i> Trước
                </a>
            <?php endif; ?>
            
            <?php if ($hasMore): ?>
                <a href="<?= \App\Core\url('/admin/users?page=' . ($pageNumber + 1) . '&cursor=' . $nextCursor . $queryString) ?>" class="px-8 py-3.5 rounded-[1.25rem] bg-slate-900 text-white font-black hover:bg-slate-800 transition-all transform active:scale-95 uppercase text-[10px] tracking-widest flex items-center gap-3 shadow-xl shadow-slate-900/20">
                    Kế tiếp <i class="fa-solid fa-chevron-right text-[8px]"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>


