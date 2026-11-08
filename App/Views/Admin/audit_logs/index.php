<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-fingerprint"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Nhật ký truy vết hệ thống</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Security Audit Log & Forensic Analysis</p>
            </div>
        </div>
        <div class="px-5 py-2.5 bg-slate-50 border border-slate-100 rounded-2xl">
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">Monitoring Active Session Data</span>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <!-- Advanced Filter Suite -->
        <div class="p-10 bg-slate-50/20 border-b border-slate-50">
            <form method="GET" action="<?= \App\Core\url('/admin/audit-logs') ?>" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                <div class="md:col-span-3 space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic underline underline-offset-4 decoration-indigo-200">Nhân viên thực hiện</label>
                    <select name="user_id" class="w-full appearance-none px-6 py-4 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-[11px] uppercase tracking-tight shadow-inner cursor-pointer">
                        <option value="">TẤT CẢ NHÂN VIÊN</option>
                        <?php foreach($users ?? [] as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['full_name']) ?> (#<?= $u['id'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="md:col-span-2 space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic underline underline-offset-4 decoration-indigo-200">Loại hành động</label>
                    <select name="action" class="w-full appearance-none px-6 py-4 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-[11px] uppercase tracking-tight shadow-inner cursor-pointer">
                        <option value="">TẤT CẢ HÀNH ĐỘNG</option>
                        <option value="CREATE" <?= ($filters['action'] ?? '') == 'CREATE' ? 'selected' : '' ?>>THÊM MỚI (CREATE)</option>
                        <option value="UPDATE" <?= ($filters['action'] ?? '') == 'UPDATE' ? 'selected' : '' ?>>CẬP NHẬT (UPDATE)</option>
                        <option value="DELETE" <?= ($filters['action'] ?? '') == 'DELETE' ? 'selected' : '' ?>>XÓA BỎ (DELETE)</option>
                    </select>
                </div>

                <div class="md:col-span-2 space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic underline underline-offset-4 decoration-indigo-200">Thực thể (Bảng)</label>
                    <input type="text" name="table_name" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700 shadow-inner text-[11px]" placeholder="VD: rooms, bookings..." value="<?= htmlspecialchars($filters['table_name'] ?? '') ?>">
                </div>

                <div class="md:col-span-3 space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic underline underline-offset-4 decoration-indigo-200">Khoảng thời gian</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="start_date" class="flex-1 px-4 py-3.5 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700 shadow-inner text-[10px]" value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
                        <span class="text-slate-300"><i class="fa-solid fa-arrow-right-long"></i></span>
                        <input type="date" name="end_date" class="flex-1 px-4 py-3.5 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700 shadow-inner text-[10px]" value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
                    </div>
                </div>

                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="flex-1 bg-slate-900 text-white font-black py-4 rounded-2xl hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3 active:scale-95 text-[10px] uppercase tracking-widest italic">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i> Lọc
                    </button>
                    <a href="<?= \App\Core\url('/admin/audit-logs') ?>" class="px-5 py-4 bg-white border border-slate-200 text-slate-400 font-black rounded-2xl hover:bg-slate-50 transition-all flex items-center justify-center" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Audit Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="pl-10 pr-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Identity</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">User Actor</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Action/Target</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Change Manifest (Old/New)</th>
                        <th class="pl-6 pr-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Vector Metadata</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-slate-50/50 transition-all duration-300 group/row">
                                <td class="pl-10 pr-6 py-8">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-900 uppercase italic">#<?= (int) $log['id'] ?></span>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1 italic"><?= !empty($log['created_at']) ? date('d/m/Y H:i:s', strtotime($log['created_at'])) : '-' ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center font-black italic">
                                            <?= strtoupper(substr($log['user_name'] ?? 'S', 0, 1)) ?>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-slate-800 uppercase italic leading-tight mb-1"><?= htmlspecialchars($log['user_name'] ?? 'System Process') ?></span>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">UID-<?= htmlspecialchars((string) ($log['user_id'] ?? 'SYS')) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <?php
                                        $action = strtoupper((string) ($log['action'] ?? 'N/A'));
                                        $aClass = match($action) {
                                            'CREATE' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'UPDATE' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'DELETE' => 'bg-rose-50 text-rose-700 border-rose-100',
                                            default => 'bg-slate-50 text-slate-500 border-slate-100'
                                        };
                                        ?>
                                        <span class="px-5 py-1.5 rounded-xl border text-[9px] font-black uppercase tracking-widest <?= $aClass ?> italic">
                                            <?= $action ?>
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black text-slate-600 uppercase italic">TBL: <?= htmlspecialchars((string) ($log['table_name'] ?? '')) ?></span>
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <span class="text-[10px] font-black text-indigo-600 italic">REC: <?= !empty($log['record_id']) ? (int) $log['record_id'] : '-' ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="flex flex-col gap-3 max-w-[400px]">
                                        <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100 group/old select-all">
                                            <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest mb-2 italic">Before State</p>
                                            <div class="text-[10px] text-slate-500 font-mono overflow-auto max-h-[80px] custom-scrollbar">
                                                <?php if (!empty($log['old_value'])): ?>
                                                    <?= htmlspecialchars(json_encode(json_decode($log['old_value']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?>
                                                <?php else: ?>
                                                    <span class="italic text-slate-300">NULL (INITIALIZATION)</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="p-4 bg-indigo-50/30 rounded-2xl border border-indigo-100/50 group/new select-all">
                                            <p class="text-[8px] font-black text-indigo-300 uppercase tracking-widest mb-2 italic">After State</p>
                                            <div class="text-[10px] text-indigo-700 font-mono overflow-auto max-h-[80px] custom-scrollbar">
                                                <?php if (!empty($log['new_value'])): ?>
                                                    <?= htmlspecialchars(json_encode(json_decode($log['new_value']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?>
                                                <?php else: ?>
                                                    <span class="italic text-slate-300">NULL (REMOVAL)</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="pl-6 pr-10 py-8 text-right">
                                    <div class="flex flex-col items-end gap-1">
                                        <div class="flex items-center gap-2 text-[10px] font-black text-slate-800 italic">
                                            <i class="fa-solid fa-network-wired text-indigo-300"></i>
                                            <?= htmlspecialchars((string) ($log['ip_address'] ?? '0.0.0.0')) ?>
                                        </div>
                                        <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic max-w-[150px] truncate" title="<?= htmlspecialchars((string) ($log['user_agent'] ?? 'Unknown Agent')) ?>">
                                            <?= htmlspecialchars((string) ($log['user_agent'] ?? 'Terminal/Unknown')) ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-32 text-center">
                                <div class="flex flex-col items-center gap-6">
                                    <div class="w-24 h-24 bg-slate-50 border border-slate-50 rounded-[2rem] flex items-center justify-center text-slate-100 text-4xl shadow-inner animate-pulse">
                                        <i class="fa-solid fa-file-shield"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] italic">No forensic logs detected</p>
                                        <p class="text-slate-300 text-[9px] font-bold italic uppercase tracking-widest">Adjust filters to broaden searches</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (!empty($nextCursorId)): ?>
            <div class="p-10 bg-slate-50/20 border-t border-slate-50 flex items-center justify-center">
                <?php $query = array_filter($filters); $query['cursor_id'] = (int) $nextCursorId; ?>
                <a href="<?= \App\Core\url('/admin/audit-logs?' . http_build_query($query)) ?>" class="px-12 py-4 bg-slate-900 text-white rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-2xl shadow-slate-200 italic active:scale-95 flex items-center gap-3 group">
                    <i class="fa-solid fa-clock-rotate-left text-xs group-hover:-rotate-90 transition-transform duration-500"></i> Xem nhật ký cũ hơn
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.05);
        border-radius: 10px;
    }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.1);
    }
</style>
