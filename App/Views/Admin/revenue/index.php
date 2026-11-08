<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-indigo-100 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Báo cáo doanh thu & Vận hành</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Financial Ledger & Performance Analytics</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
             <div class="px-5 py-2.5 bg-slate-50 border border-slate-100 rounded-2xl">
                 <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic group-hover:text-indigo-600 transition-colors">Dữ liệu tài chính cập nhật: <?= date('d/m/Y H:i') ?></span>
             </div>
        </div>
    </div>

    <!-- Analytics Dashboard -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-2xl shadow-indigo-100/50 border-none flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform rotate-12 duration-500 text-white text-8xl font-black">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div class="w-16 h-16 rounded-[1.5rem] bg-white/10 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-all duration-500 relative z-10">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest italic mb-1">Doanh thu hiện thời</p>
                <h3 class="text-3xl font-black text-white tracking-tighter italic leading-none"><?= \App\Core\money($totalRevenue) ?></h3>
            </div>
        </div>
        
        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-emerald-600 text-8xl font-black">
                <i class="fa-solid fa-hotel"></i>
            </div>
            <div class="w-16 h-16 rounded-[1.5rem] bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500 relative z-10">
                <i class="fa-solid fa-percent"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Tỷ lệ lấp đầy</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic leading-none"><?= number_format($occupancyRate, 2) ?>%</h3>
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest italic">Current</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:translate-y-[-4px] transition-all duration-500 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-sky-600 text-8xl font-black">
                <i class="fa-solid fa-diagram-project"></i>
            </div>
            <div class="w-16 h-16 rounded-[1.5rem] bg-sky-50 text-sky-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-sky-600 group-hover:text-white transition-all duration-500 relative z-10">
                <i class="fa-solid fa-sitemap"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Nguồn doanh thu</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic leading-none"><?= count($revenueBySource) ?></h3>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Clusters</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-10 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Diễn biến doanh thu</h4>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] italic mt-1">Daily Revenue Projection</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-300">
                    <i class="fa-solid fa-chart-area"></i>
                </div>
            </div>
            <div class="relative h-[300px]">
                <canvas id="revenueByDateChart"></canvas>
            </div>
        </div>
        
        <div class="bg-white p-10 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Cơ cấu nguồn thu</h4>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] italic mt-1">revenue segmentation by channel</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-300">
                    <i class="fa-solid fa-chart-bar"></i>
                </div>
            </div>
            <div class="relative h-[300px]">
                <canvas id="revenueHeatmapChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="space-y-10">
        <!-- Payments Ledger -->
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="p-10 border-b border-slate-50 bg-slate-50/20 flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-black text-slate-900 uppercase tracking-tight italic text-indigo-600">Nhật ký thanh toán</h4>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Real-time payment transaction log</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="pl-10 pr-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Identity</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Booking ID</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Mechanism</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-right">Value</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Status</th>
                            <th class="pl-6 pr-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($payments as $row): ?>
                            <tr class="hover:bg-slate-50/50 transition-all duration-300 group/row">
                                <td class="pl-10 pr-6 py-6">
                                    <span class="text-sm font-black text-slate-900 italic">#<?= (int) ($row['id'] ?? 0) ?></span>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black border border-indigo-100 uppercase tracking-widest italic font-bold">BKG-<?= (int) ($row['booking_id'] ?? 0) ?></span>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="text-xs font-black text-slate-600 uppercase tracking-tight italic"><?= htmlspecialchars((string) ($row['method'] ?? 'N/A')) ?></span>
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <span class="text-sm font-black text-slate-900 italic"><?= \App\Core\money((float) ($row['amount'] ?? 0)) ?></span>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <?php 
                                        $s = strtolower((string) ($row['status'] ?? ''));
                                        $sClass = in_array($s, ['success', 'paid']) ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-50 text-slate-400 border-slate-100';
                                    ?>
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-2xl border text-[9px] font-black uppercase tracking-widest <?= $sClass ?> italic">
                                        <?= strtoupper($s) ?>
                                    </span>
                                </td>
                                <td class="pl-6 pr-10 py-6 text-right">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] italic">
                                        <?= date('d/m/Y H:i', strtotime((string) ($row['paid_at'] ?? $row['created_at'] ?? 'now'))) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($payments)): ?>
                            <tr><td colspan="6" class="py-24 text-center italic text-slate-300 uppercase tracking-widest font-black text-[10px]">No payment records captured in current context</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($nextPaymentLastId)): ?>
                <div class="p-8 bg-slate-50/20 border-t border-slate-50 flex items-center justify-center">
                    <?php $paymentQuery = []; if (!empty($nextBookingLastId)) { $paymentQuery['booking_last_id'] = (int) $nextBookingLastId; } $paymentQuery['payment_last_id'] = (int) $nextPaymentLastId; ?>
                    <a href="<?= \App\Core\url('/admin/revenue?' . http_build_query($paymentQuery)) ?>" class="px-10 py-3.5 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 italic active:scale-95">Load More Transactions</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Revenue by Source -->
            <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-50 bg-slate-50/20">
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Source Allocation</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="pl-10 pr-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Origin Source</th>
                                <th class="pl-6 pr-10 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Cumulative Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($revenueBySource as $row): ?>
                                <tr class="hover:bg-slate-50/50 transition-all duration-300 group/row">
                                    <td class="pl-10 pr-6 py-6">
                                        <span class="text-sm font-black text-slate-800 uppercase italic"><?= htmlspecialchars((string) ($row['source'] ?? 'unknown')) ?></span>
                                    </td>
                                    <td class="pl-6 pr-10 py-6 text-right">
                                        <span class="text-base font-black text-indigo-600 italic tracking-tighter"><?= \App\Core\money((float) ($row['total'] ?? 0)) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Heatmap Log -->
            <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-50 bg-slate-50/20">
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Chronological Heatmap</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="pl-10 pr-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Deployment Date</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Channel</th>
                                <th class="pl-6 pr-10 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Projected Rev</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($revenueHeatmap as $row): ?>
                                <tr class="hover:bg-slate-50/50 transition-all duration-300 group/row">
                                    <td class="pl-10 pr-6 py-6">
                                        <span class="text-xs font-black text-slate-400 uppercase italic"><?= htmlspecialchars((string) ($row['d'] ?? '')) ?></span>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest italic"><?= htmlspecialchars((string) ($row['source'] ?? 'unknown')) ?></span>
                                    </td>
                                    <td class="pl-6 pr-10 py-6 text-right">
                                        <span class="text-sm font-black text-slate-800 italic"><?= \App\Core\money((float) ($row['total'] ?? 0)) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reservations Ledger -->
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="p-10 border-b border-slate-50 bg-slate-50/20">
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Inventory Reservation Manifest</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="pl-10 pr-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Resv Identity</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">User Context</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Operational Timeline</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-right">Ledger Value</th>
                            <th class="pl-6 pr-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($bookings as $row): ?>
                            <tr class="hover:bg-slate-50/50 transition-all duration-300 group/row">
                                <td class="pl-10 pr-6 py-8">
                                    <span class="text-sm font-black text-slate-900 uppercase italic">#<?= (int) ($row['id'] ?? 0) ?></span>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <span class="px-4 py-2 bg-slate-50 text-slate-400 rounded-xl border border-slate-100 text-[10px] font-black uppercase tracking-widest italic">ACT-<?= (int) ($row['user_id'] ?? 0) ?></span>
                                </td>
                                <td class="px-6 py-8 text-center">
                                    <div class="flex items-center justify-center gap-3 text-[10px] font-black italic">
                                        <span class="text-slate-800"><?= date('d M', strtotime((string)($row['check_in'] ?? 'now'))) ?></span>
                                        <i class="fa-solid fa-arrow-right text-[8px] text-slate-300"></i>
                                        <span class="text-slate-400"><?= date('d M', strtotime((string)($row['check_out'] ?? 'now'))) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-8 text-right">
                                    <span class="text-base font-black text-indigo-600 italic tracking-tighter"><?= \App\Core\money((float) ($row['total_price'] ?? 0)) ?></span>
                                </td>
                                <td class="pl-6 pr-10 py-8 text-center text-center">
                                    <span class="inline-flex items-center px-5 py-2 rounded-2xl bg-indigo-50 text-indigo-700 border border-indigo-100 text-[9px] font-black uppercase tracking-widest italic">
                                        <?= \App\Core\BookingStatus::label((string) ($row['status'] ?? '')) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($nextBookingLastId)): ?>
                <div class="p-8 bg-slate-50/20 border-t border-slate-50 flex items-center justify-center">
                    <?php $bookingQuery = []; if (!empty($nextPaymentLastId)) { $bookingQuery['payment_last_id'] = (int) $nextPaymentLastId; } $bookingQuery['booking_last_id'] = (int) $nextBookingLastId; ?>
                    <a href="<?= \App\Core\url('/admin/revenue?' . http_build_query($bookingQuery)) ?>" class="px-10 py-3.5 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 italic active:scale-95">Load More Bookings</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function() {
        // Shared Chart Configuration
        const chartDefaults = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                    ticks: { font: { family: 'Inter', size: 10, weight: '900' }, color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Inter', size: 10, weight: '900' }, color: '#94a3b8' }
                }
            }
        };

        const revenueData = <?= json_encode($revenueHeatmap) ?>;
        const byDate = {};
        revenueData.forEach(row => {
            const d = row.d;
            const amt = parseFloat(row.total || 0);
            if (!byDate[d]) byDate[d] = 0;
            byDate[d] += amt;
        });

        const dates = Object.keys(byDate).sort();
        const totals = dates.map(d => byDate[d]);

        const lineCanvas = document.getElementById('revenueByDateChart');
        if (lineCanvas) {
            const ctx1 = lineCanvas.getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Doanh thu',
                        data: totals,
                        borderColor: '#4f46e5',
                        backgroundColor: (context) => {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return null;
                            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradient.addColorStop(0, 'rgba(79, 70, 229, 0)');
                            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.1)');
                            return gradient;
                        },
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2
                    }]
                },
                options: chartDefaults
            });
        }

        const sources = Array.from(new Set(revenueData.map(r => r.source || 'unknown')));
        const datesAll = Array.from(new Set(revenueData.map(r => r.d))).sort();
        const datasets = sources.map((s, idx) => {
            const colors = ['#4f46e5', '#10b981', '#0ea5e9', '#f59e0b', '#8b5cf6'];
            const color = colors[idx % colors.length];
            return {
                label: s,
                data: datesAll.map(d => {
                    const found = revenueData.find(r => r.d === d && (r.source || 'unknown') === s);
                    return found ? parseFloat(found.total || 0) : 0;
                }),
                backgroundColor: color,
                borderRadius: 6,
                barThickness: 20
            };
        });

        const stackCanvas = document.getElementById('revenueHeatmapChart');
        if (stackCanvas) {
            const ctx2 = stackCanvas.getContext('2d');
            new Chart(ctx2, {
                type: 'bar',
                data: { labels: datesAll, datasets },
                options: {
                    ...chartDefaults,
                    plugins: { legend: { display: true, position: 'bottom', labels: { font: { family: 'Inter', size: 9, weight: '900' }, usePointStyle: true, padding: 20 } } },
                    scales: { 
                        x: { ...chartDefaults.scales.x, stacked: true }, 
                        y: { ...chartDefaults.scales.y, stacked: true } 
                    }
                }
            });
        }
    })();
</script>


