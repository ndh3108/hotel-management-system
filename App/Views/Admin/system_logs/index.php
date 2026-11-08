<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-terminal"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Hệ thống Nhật ký Vận hành (System Logs)</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Real-time Trace Identity & Security Forensics</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
             <div class="px-5 py-2.5 bg-slate-50 border border-slate-100 rounded-2xl">
                 <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic group-hover:text-indigo-600 transition-colors italic uppercase tracking-widest leading-relaxed">Status Tracking: Active Monitoring</span>
             </div>
        </div>
    </div>

    <!-- Analytics Dashboard (Mini) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center gap-4 group hover:translate-y-[-2px] transition-all duration-500">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Live Traces</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-xl font-black text-slate-900 italic leading-none"><?= count($logs) ?></h3>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center gap-4 group hover:translate-y-[-2px] transition-all duration-500">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl group-hover:bg-rose-600 group-hover:text-white transition-all duration-500">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Error Frequency</p>
                <h3 class="text-xl font-black text-rose-600 italic leading-none">
                    <?php 
                        $errCount = array_reduce($logs, function($carry, $item) {
                            return $carry + (in_array(($item['severity'] ?? ''), ['ERROR', 'CRITICAL']) ? 1 : 0);
                        }, 0);
                        echo $errCount;
                    ?>
                </h3>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <!-- Filter Suite -->
        <div class="p-10 bg-slate-50/20 border-b border-slate-50">
            <form method="GET" action="<?= \App\Core\url('/admin/system-logs') ?>" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                <div class="md:col-span-3 space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic">Phân hệ (Module)</label>
                    <select name="resource_type" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700 shadow-inner text-[11px] uppercase tracking-tight">
                        <option value="">Tất cả module</option>
                        <option value="Auth" <?= ($filters['resource_type'] ?? '') === 'Auth' ? 'selected' : '' ?>>Auth Cluster</option>
                        <option value="Payment" <?= ($filters['resource_type'] ?? '') === 'Payment' ? 'selected' : '' ?>>Payment Gateway</option>
                        <option value="Booking" <?= ($filters['resource_type'] ?? '') === 'Booking' ? 'selected' : '' ?>>Room Bookings</option>
                        <option value="System" <?= ($filters['resource_type'] ?? '') === 'System' ? 'selected' : '' ?>>Global System</option>
                    </select>
                </div>

                <div class="md:col-span-3 space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic">Mức độ nghiêm trọng</label>
                    <select name="severity" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700 shadow-inner text-[11px] uppercase tracking-tight">
                        <option value="">Tất cả mức độ</option>
                        <option value="INFO" <?= ($filters['severity'] ?? '') === 'INFO' ? 'selected' : '' ?>>Level: INFO</option>
                        <option value="WARNING" <?= ($filters['severity'] ?? '') === 'WARNING' ? 'selected' : '' ?>>Level: WARNING</option>
                        <option value="ERROR" <?= ($filters['severity'] ?? '') === 'ERROR' ? 'selected' : '' ?>>Level: ERROR</option>
                        <option value="CRITICAL" <?= ($filters['severity'] ?? '') === 'CRITICAL' ? 'selected' : '' ?>>Level: CRITICAL</option>
                    </select>
                </div>

                <div class="md:col-span-6 flex gap-3">
                    <button type="submit" class="flex-1 bg-slate-900 text-white font-black py-4 rounded-2xl hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3 active:scale-95 text-[10px] uppercase tracking-widest italic">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i> Lọc dữ liệu Forensic
                    </button>
                    <button type="submit" name="export" value="csv" class="px-5 py-4 bg-white border border-slate-200 text-slate-400 font-black rounded-2xl hover:bg-slate-50 transition-all flex items-center justify-center group/export" title="Xuất CSV">
                        <i class="fa-solid fa-file-csv group-hover:text-emerald-500"></i>
                    </button>
                    <a href="<?= \App\Core\url('/admin/system-logs') ?>" class="px-5 py-4 bg-white border border-slate-200 text-slate-400 font-black rounded-2xl hover:bg-slate-50 transition-all flex items-center justify-center" title="Làm mới">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="pl-10 pr-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Trace Identity</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Actor / Protocol</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Module context</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Status / Severity</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">IP Address</th>
                        <th class="pl-6 pr-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Operation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="py-32 text-center text-center">
                                <div class="flex flex-col items-center gap-6">
                                    <div class="w-24 h-24 bg-slate-50 border border-slate-50 rounded-[2rem] flex items-center justify-center text-slate-100 text-4xl shadow-inner animate-pulse">
                                        <i class="fa-solid fa-database text-slate-200"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] italic">No forensic trails detected</p>
                                        <p class="text-slate-300 text-[9px] font-bold italic uppercase tracking-widest">Global system logging yielded zero live results</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $l): ?>
                            <tr class="hover:bg-slate-50/50 transition-all duration-300 group/row">
                                <td class="pl-10 pr-6 py-8">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic group-hover/row:text-indigo-600 transition-colors">TRC-<?= htmlspecialchars((string)($l['trace_id'] ?? '')) ?></span>
                                        <span class="text-[10px] font-black text-slate-800 uppercase italic"><?= date('H:i:s d-m-Y', strtotime((string)$l['created_at'])) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-black text-slate-900 uppercase italic"><?= htmlspecialchars((string)($l['actor_name'] ?? 'System Process')) ?></span>
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic">ACL: <?= htmlspecialchars((string)($l['role'] ?? 'SYSTEM_INTERNAL')) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="px-3 py-1 bg-slate-50 text-slate-500 rounded-lg text-[9px] font-black border border-slate-100 uppercase tracking-widest italic"><?= htmlspecialchars((string)($l['resource_type'] ?? 'N/A')) ?></span>
                                        <span class="text-[8px] font-black text-slate-300 mt-1 uppercase">ID: <?= htmlspecialchars((string)($l['resource_id'] ?? 'N/A')) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <?php 
                                            $s = strtoupper((string)($l['status'] ?? ''));
                                            $sClass = ($s === 'SUCCESS') ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100';
                                            
                                            $sev = strtoupper((string)($l['severity'] ?? ''));
                                            $sevClass = in_array($sev, ['ERROR', 'CRITICAL']) ? 'bg-rose-900 text-white' : 'bg-indigo-50 text-indigo-600 border-indigo-100';
                                        ?>
                                        <span class="inline-flex items-center px-4 py-1 rounded-xl border text-[8px] font-black tracking-[0.2em] <?= $sClass ?> italic shadow-sm"><?= $s ?></span>
                                        <span class="inline-flex items-center px-4 py-1 rounded-xl border text-[8px] font-black tracking-[0.2em] <?= $sevClass ?> italic shadow-sm"><?= $sev ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic select-all"><?= htmlspecialchars((string)($l['ip'] ?? '0.0.0.0')) ?></span>
                                </td>
                                <td class="pl-6 pr-10 py-8 text-center">
                                    <button class="px-6 py-2.5 bg-slate-900 text-white rounded-2xl text-[9px] font-black uppercase tracking-widest italic group-hover/row:bg-indigo-600 transition-all shadow-lg shadow-slate-200 active:scale-95" onclick="viewLogDetail(<?= (int)$l['id'] ?>)">
                                        Forensic Analysis
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Log Details -->
<div id="logDetailModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" onclick="closeLogModal()"></div>
    <div class="relative bg-white w-full max-w-5xl rounded-[3rem] shadow-2xl overflow-hidden animate-scale-up border border-white/20">
        <div class="p-10 border-b border-slate-50 bg-slate-50/20 flex items-center justify-between">
            <div>
                <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">Forensic State Analysis</h4>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Comparing Object State Transformations</p>
            </div>
            <button onclick="closeLogModal()" class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 hover:rotate-90 transition-all font-black text-2xl">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="p-10 space-y-10 max-h-[70vh] overflow-y-auto custom-scrollbar">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Existing State (Before)</span>
                        <div class="px-3 py-1 bg-slate-50 rounded-lg border border-slate-100 text-[8px] font-black text-slate-400 uppercase italic italic uppercase tracking-widest leading-relaxed italic uppercase tracking-widest leading-relaxed italic uppercase tracking-widest leading-relaxed">Snapshot A</div>
                    </div>
                    <div class="bg-slate-900 rounded-[2rem] p-8 shadow-inner overflow-hidden relative">
                        <pre id="logBefore" class="text-indigo-300 text-[11px] font-mono leading-relaxed overflow-x-auto whitespace-pre-wrap select-all"></pre>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">Transformed State (After)</span>
                        <div class="px-3 py-1 bg-indigo-50 rounded-lg border border-indigo-100 text-[8px] font-black text-indigo-400 uppercase italic">Snapshot B</div>
                    </div>
                    <div class="bg-slate-900 rounded-[2rem] p-8 shadow-inner overflow-hidden relative">
                        <pre id="logAfter" class="text-emerald-400 text-[11px] font-mono leading-relaxed overflow-x-auto whitespace-pre-wrap select-all"></pre>
                    </div>
                </div>
            </div>
            <div class="p-8 bg-slate-50 border border-slate-100 rounded-[2rem] flex items-center gap-6 group hover:translate-x-2 transition-transform duration-500">
                <div class="w-16 h-16 rounded-[1.5rem] bg-indigo-600 text-white flex items-center justify-center text-2xl shadow-xl shadow-indigo-100">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
                <div>
                    <h5 class="text-sm font-black text-slate-900 uppercase tracking-tight italic mb-1 uppercase tracking-tight italic mb-1 uppercase tracking-tight italic mb-1 uppercase tracking-tight italic mb-1 uppercase tracking-tight italic mb-1 uppercase tracking-tight italic mb-1 uppercase tracking-tight italic mb-1 uppercase tracking-tight italic mb-1 font-black text-slate-900 uppercase tracking-tight italic mb-1">User Agent Identity Signature</h5>
                    <p id="logUserAgent" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] leading-relaxed italic break-all"></p>
                </div>
            </div>
        </div>
        <div class="p-10 bg-slate-50/50 border-t border-slate-50 flex justify-end">
            <button onclick="closeLogModal()" class="px-10 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic hover:bg-slate-700 transition-all shadow-xl shadow-slate-200">Acknowledge Report</button>
        </div>
    </div>
</div>

<script>
function closeLogModal() {
    const modal = document.getElementById('logDetailModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

function viewLogDetail(id) {
    fetch('<?= \App\Core\url('/admin/system-logs/detail?id=') ?>' + id)
        .then(res => res.json())
        .then(data => {
            try {
                let beforeStr = data.before_json ? JSON.stringify(JSON.parse(data.before_json), null, 2) : '// No predecessor state captured';
                let afterStr = data.after_json ? JSON.stringify(JSON.parse(data.after_json), null, 2) : '// Terminal state reached';
                document.getElementById('logBefore').textContent = beforeStr;
                document.getElementById('logAfter').textContent = afterStr;
                document.getElementById('logUserAgent').textContent = data.user_agent || '// Identity signature unknown';
                
                const modal = document.getElementById('logDetailModal');
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } catch(e) {
                console.error("JSON Parse Error", e);
                // Fallback for non-JSON content
                document.getElementById('logBefore').textContent = data.before_json || '// NULL';
                document.getElementById('logAfter').textContent = data.after_json || '// NULL';
                document.getElementById('logUserAgent').textContent = data.user_agent || '// N/A';
                document.getElementById('logDetailModal').classList.remove('hidden');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi khi tải chi tiết log từ hệ thống database.');
        });
}
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.02); }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

@keyframes scaleUp {
    from { opacity: 0; transform: scale(0.9) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-scale-up { animation: scaleUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>

