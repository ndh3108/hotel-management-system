<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-amber-500 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-amber-100 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-crown"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Quản lý khách hàng thân thiết</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Loyalty Elite Program & Tier Management</p>
            </div>
        </div>
        <div class="px-5 py-2.5 bg-amber-50 border border-amber-100 rounded-2xl">
            <span class="text-[10px] font-black text-amber-600 uppercase tracking-widest italic animate-pulse">Monitoring Active Membership Rewards</span>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="pl-10 pr-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Thành viên</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Hạng hội viên</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Tổng tích lũy</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Điểm hiện tại</th>
                        <th class="pl-6 pr-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Chiết khấu (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="5" class="py-32 text-center">
                                <div class="flex flex-col items-center gap-6">
                                    <div class="w-24 h-24 bg-slate-50 border border-slate-50 rounded-[2rem] flex items-center justify-center text-slate-100 text-4xl shadow-inner animate-pulse">
                                        <i class="fa-solid fa-users-slash"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] italic">No loyalty members found</p>
                                        <p class="text-slate-300 text-[9px] font-bold italic uppercase tracking-widest">Active membership database is empty</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <tr class="hover:bg-amber-50/20 transition-all duration-300 group/row">
                                <td class="pl-10 pr-6 py-8">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center font-black italic">
                                            <?= strtoupper(substr($row['full_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-slate-900 uppercase italic leading-tight mb-1"><?= htmlspecialchars((string) ($row['full_name'] ?? 'Unknown User')) ?></span>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic group-hover/row:text-indigo-400 transition-colors">ID: #<?= (int) ($row['user_id'] ?? 0) ?> • <?= htmlspecialchars((string) ($row['email'] ?? '')) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center text-center">
                                    <?php
                                    $tier = strtoupper((string) ($row['tier'] ?? 'BRONZE'));
                                    $tClass = match($tier) {
                                        'PLATINUM' => 'bg-indigo-900 text-white border-none shadow-lg shadow-indigo-100',
                                        'GOLD' => 'bg-amber-500 text-white border-none shadow-lg shadow-amber-100',
                                        'SILVER' => 'bg-slate-200 text-slate-700 border-none',
                                        default => 'bg-orange-100 text-orange-700 border-orange-200'
                                    };
                                    ?>
                                    <span class="inline-flex items-center px-5 py-2 rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] <?= $tClass ?> italic transition-transform group-hover/row:scale-110">
                                        <i class="fa-solid fa-medal mr-2"></i> <?= $tier ?>
                                    </span>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-black text-slate-800 italic"><?= number_format((float) ($row['total_points'] ?? $row['points'] ?? 0), 0, ',', '.') ?></span>
                                        <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest mt-1 italic">Lifetime Pts</span>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-black text-indigo-600 italic"><?= number_format((float) ($row['current_points'] ?? 0), 0, ',', '.') ?></span>
                                        <span class="text-[8px] font-black text-indigo-300 uppercase tracking-widest mt-1 italic">Wallet Balance</span>
                                    </div>
                                </td>
                                <td class="pl-6 pr-10 py-8 text-right">
                                    <div class="flex flex-col items-end">
                                        <span class="text-lg font-black text-emerald-600 italic tracking-tighter"><?= number_format((float) ($row['discount_percent'] ?? 0), 2) ?>%</span>
                                        <span class="text-[8px] font-black text-emerald-400 uppercase tracking-widest italic">Tier Benefit</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Program Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="p-8 bg-slate-50 border border-slate-100 rounded-[2.5rem] flex items-center gap-6 group hover:bg-white hover:shadow-2xl transition-all duration-500">
            <div class="w-16 h-16 rounded-[1.5rem] bg-indigo-600 text-white flex items-center justify-center text-2xl group-hover:rotate-12 transition-transform">
                <i class="fa-solid fa-gift"></i>
            </div>
            <div>
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight italic mb-1">Cơ chế tích lũy</h4>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-relaxed italic">Mỗi <span class="text-indigo-600">100.000₫</span> chi tiêu tương ứng với <span class="text-indigo-600">1 điểm</span> thưởng. Điểm dùng để nâng hạng và quy đổi ưu đãi.</p>
            </div>
        </div>
        <div class="p-8 bg-slate-50 border border-slate-100 rounded-[2.5rem] flex items-center gap-6 group hover:bg-white hover:shadow-2xl transition-all duration-500">
            <div class="w-16 h-16 rounded-[1.5rem] bg-emerald-600 text-white flex items-center justify-center text-2xl group-hover:rotate-12 transition-transform">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight italic mb-1">Đặc quyền hội viên</h4>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-relaxed italic">Giảm giá trực tiếp tối đa <span class="text-emerald-600">10%</span> tổng giá trị đơn đặt phòng cho hạng <span class="text-emerald-600">Platinum</span>.</p>
            </div>
        </div>
    </div>
</div>

