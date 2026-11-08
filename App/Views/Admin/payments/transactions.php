<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Đối soát giao dịch thanh toán</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Payment Callback Reconciliation & Forensics</p>
            </div>
        </div>
        <div class="px-5 py-2.5 bg-slate-50 border border-slate-100 rounded-2xl">
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic group-hover:text-indigo-600 transition-colors italic uppercase tracking-widest leading-relaxed">Financial integrity verified</span>
        </div>
    </div>

    <!-- Analytics Snapshot -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
        <div class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-2xl shadow-indigo-100/50 flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500">
            <div class="w-16 h-16 rounded-[1.5rem] bg-white/10 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest italic mb-1">Tổng giao dịch</p>
                <h3 class="text-3xl font-black text-white tracking-tighter italic leading-none"><?= (int) $summary['total'] ?></h3>
            </div>
        </div>
        
        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500">
            <div class="w-16 h-16 rounded-[1.5rem] bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Thành công</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic leading-none text-emerald-600"><?= (int) $summary['success'] ?></h3>
                    <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest italic">Confirmed</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500">
            <div class="w-16 h-16 rounded-[1.5rem] bg-rose-50 text-rose-600 flex items-center justify-center text-2xl group-hover:bg-rose-600 group-hover:text-white transition-all duration-500">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Thất bại/Lỗi</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic leading-none text-rose-600"><?= (int) $summary['failed'] ?></h3>
                    <span class="text-[10px] font-black text-rose-400 uppercase tracking-widest italic">Rejected</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <!-- Advanced Filter Suite -->
        <div class="p-10 bg-slate-50/20 border-b border-slate-50">
            <form method="GET" action="<?= \App\Core\url('/admin/payments/transactions') ?>" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                <div class="md:col-span-3 space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic underline underline-offset-4 decoration-indigo-200">Cổng thanh toán</label>
                    <input type="text" name="gateway" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700 shadow-inner text-[11px] uppercase tracking-tight" placeholder="VD: momo, vnpay..." value="<?= htmlspecialchars((string) ($filters['gateway'] ?? '')) ?>">
                </div>

                <div class="md:col-span-2 space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic underline underline-offset-4 decoration-indigo-200">Trạng thái</label>
                    <input type="text" name="status" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700 shadow-inner text-[11px] uppercase tracking-tight" placeholder="VD: PAID, FAILED..." value="<?= htmlspecialchars((string) ($filters['status'] ?? '')) ?>">
                </div>

                <div class="md:col-span-4 space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block italic underline underline-offset-4 decoration-indigo-200">Tìm kiếm từ khóa</label>
                    <input type="text" name="keyword" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-700 shadow-inner text-[11px]" placeholder="Mã giao dịch / Mã hóa đơn..." value="<?= htmlspecialchars((string) ($filters['keyword'] ?? '')) ?>">
                </div>

                <div class="md:col-span-3 flex gap-3">
                    <button type="submit" class="flex-1 bg-slate-900 text-white font-black py-4 rounded-2xl hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3 active:scale-95 text-[10px] uppercase tracking-widest italic">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i> Lọc
                    </button>
                    <button type="submit" name="export" value="csv" class="px-5 py-4 bg-white border border-slate-200 text-slate-400 font-black rounded-2xl hover:bg-slate-50 transition-all flex items-center justify-center group/export" title="Export CSV">
                        <i class="fa-solid fa-file-csv group-hover:text-emerald-500"></i>
                    </button>
                    <a href="<?= \App\Core\url('/admin/payments/transactions') ?>" class="px-5 py-4 bg-white border border-slate-200 text-slate-400 font-black rounded-2xl hover:bg-slate-50 transition-all flex items-center justify-center" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="pl-10 pr-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Identity</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Gateway/Provider</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">External Transaction Reference</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center text-center">Invoice/Booking</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-right">Value Amount</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Status</th>
                        <th class="pl-6 pr-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Timeline</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" class="py-32 text-center text-center">
                                <div class="flex flex-col items-center gap-6 text-center">
                                    <div class="w-24 h-24 bg-slate-50 border border-slate-50 rounded-[2rem] flex items-center justify-center text-slate-100 text-4xl shadow-inner animate-pulse">
                                        <i class="fa-solid fa-magnifying-glass-dollar text-slate-200"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] italic">No transaction records detected</p>
                                        <p class="text-slate-300 text-[9px] font-bold italic uppercase tracking-widest">Database reconciliation yielded zero results</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $txn): ?>
                            <?php $statusMeta = \App\Core\StatusLabel::payment((string) ($txn['status'] ?? '')); ?>
                            <tr class="hover:bg-slate-50/50 transition-all duration-300 group/row">
                                <td class="pl-10 pr-6 py-8">
                                    <span class="text-sm font-black text-slate-900 uppercase italic">#<?= (int) ($txn['id'] ?? 0) ?></span>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 font-black text-[10px] italic">
                                            <?= strtoupper(substr($txn['gateway'] ?? '?', 0, 2)) ?>
                                        </div>
                                        <span class="text-xs font-black text-slate-800 uppercase italic tracking-tight"><?= htmlspecialchars((string) ($txn['gateway'] ?? 'UNKNOWN')) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-8">
                                    <div class="max-w-[200px] overflow-hidden">
                                        <span class="text-xs font-black text-indigo-400 uppercase italic tracking-widest break-all select-all leading-relaxed" title="<?= htmlspecialchars((string) ($txn['external_txn_id'] ?? 'N/A')) ?>">
                                            <?= htmlspecialchars((string) ($txn['external_txn_id'] ?? 'NULL_PTR')) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-center text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="px-3 py-1 bg-slate-50 text-slate-500 rounded-lg text-[9px] font-black border border-slate-100 uppercase tracking-widest italic group-hover/row:bg-white transition-colors">INV-<?= (int) ($txn['invoice_id'] ?? 0) ?></span>
                                        <?php if(!empty($txn['booking_id'])): ?>
                                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[9px] font-black border border-indigo-100 uppercase tracking-widest italic group-hover/row:bg-white transition-colors">BKG-<?= (int) $txn['booking_id'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-right">
                                    <span class="text-base font-black text-slate-900 italic tracking-tighter select-all"><?= number_format((float) ($txn['amount'] ?? 0), 0, ',', '.') ?>₫</span>
                                </td>
                                <td class="px-6 py-8 text-center text-center">
                                    <?php 
                                        $s = strtolower((string) ($txn['status'] ?? ''));
                                        $sClass = match($s) {
                                            'paid', 'success' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'failed', 'error' => 'bg-rose-50 text-rose-700 border-rose-100',
                                            default => 'bg-slate-50 text-slate-400 border-slate-100'
                                        };
                                    ?>
                                    <span class="inline-flex items-center px-5 py-2 rounded-2xl border text-[9px] font-black uppercase tracking-widest <?= $sClass ?> italic shadow-sm">
                                        <?= strtoupper($statusMeta['label']) ?>
                                    </span>
                                </td>
                                <td class="pl-6 pr-10 py-8 text-right">
                                    <div class="flex flex-col items-end">
                                        <span class="text-[10px] font-black text-slate-900 uppercase italic tracking-widest"><?= !empty($txn['paid_at']) ? date('H:i:s', strtotime($txn['paid_at'])) : date('H:i:s', strtotime($txn['created_at'])) ?></span>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1 italic"><?= !empty($txn['paid_at']) ? date('d-m-Y', strtotime($txn['paid_at'])) : date('d-m-Y', strtotime($txn['created_at'])) ?></span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

