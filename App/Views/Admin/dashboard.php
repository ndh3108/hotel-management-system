<div class="space-y-8 animate-fade-in pb-12">
    <!-- Quick Analytics Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue Card -->
        <div class="bg-indigo-900 p-6 rounded-3xl shadow-xl shadow-indigo-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform rotate-12">
                <i class="fa-solid fa-sack-dollar text-8xl text-white"></i>
            </div>
            <p class="text-xs font-black text-indigo-300 uppercase tracking-widest mb-1">Tổng doanh thu</p>
            <h3 class="text-3xl font-black text-white"><?= number_format((float) ($overview['revenue'] ?? 0), 0, ',', '.') ?>₫</h3>
            <p class="text-xs font-medium text-indigo-400 mt-4">Giao dịch đã hoàn thành</p>
        </div>

        <!-- Bookings Card -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12">
                <i class="fa-solid fa-calendar-check text-8xl text-indigo-600"></i>
            </div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Lượt đặt phòng</p>
            <h3 class="text-3xl font-black text-slate-900"><?= number_format((int) ($overview['bookings'] ?? 0)) ?></h3>
            <p class="text-xs font-medium text-slate-400 mt-4">Bookings in period</p>
        </div>

        <!-- Occupancy Card -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12">
                <i class="fa-solid fa-bed text-8xl text-emerald-600"></i>
            </div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Phòng đang ở</p>
            <h3 class="text-3xl font-black text-slate-900"><?= number_format((int) ($overview['occupied_rooms'] ?? 0)) ?></h3>
            <p class="text-xs font-medium text-slate-400 mt-4">Current occupancy</p>
        </div>

        <!-- Customer Card -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12">
                <i class="fa-solid fa-users text-8xl text-sky-600"></i>
            </div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Tổng khách hàng</p>
            <h3 class="text-3xl font-black text-slate-900"><?= number_format((int) ($overview['total_users'] ?? 0)) ?></h3>
            <p class="text-xs font-medium text-slate-400 mt-4">Active guest profiles</p>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex items-center gap-4 shadow-sm hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-star-half-stroke"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Đánh giá chờ duyêt</p>
                <h4 class="text-xl font-black text-slate-800"><?= number_format((int) ($overview['pending_reviews'] ?? 0)) ?></h4>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex items-center gap-4 shadow-sm hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-credit-card"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Thanh toán hoàn tất</p>
                <h4 class="text-xl font-black text-slate-800"><?= number_format((int) ($overview['total_payments'] ?? 0)) ?></h4>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex items-center gap-4 shadow-sm hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-medal"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Thành viên Loyalty</p>
                <h4 class="text-xl font-black text-slate-800"><?= number_format((int) ($overview['loyalty_members'] ?? 0)) ?></h4>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Doanh thu theo ngày</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Revenue trends</p>
                </div>
            </div>
            <div class="relative h-[250px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Lượt đặt theo ngày</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Booking volume</p>
                </div>
            </div>
            <div class="relative h-[250px]">
                <canvas id="bookingChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Latest Data -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        <!-- Recent Bookings -->
        <div class="xl:col-span-8 bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Đặt phòng mới nhất</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Recent activity log</p>
                </div>
                <a href="<?= \App\Core\url('/admin/bookings') ?>" class="px-5 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200 transition-all uppercase tracking-widest">Tất cả</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-50">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Booking ID</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Khách hàng</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Phòng</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Trạng thái</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (!empty($recentBookings)): ?>
                            <?php foreach ($recentBookings as $booking): ?>
                                <?php
                                $status = strtolower((string) ($booking['status'] ?? 'pending'));
                                $badgeClass = match ($status) {
                                    'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'pending'   => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'cancelled' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    'completed' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                    default     => 'bg-slate-50 text-slate-600 border-slate-100'
                                };
                                ?>
                                <tr class="hover:bg-slate-50/50 transition-all">
                                    <td class="px-8 py-4 text-sm font-black text-slate-900">#<?= (int) ($booking['id'] ?? 0) ?></td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-slate-700"><?= htmlspecialchars((string) ($booking['guest_name'] ?? 'N/A')) ?></p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-600">ROOM <?= htmlspecialchars((string) ($booking['room_number'] ?? 'N/A')) ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full border text-[10px] font-black uppercase tracking-widest <?= $badgeClass ?>"><?= htmlspecialchars((string) ($booking['status'] ?? 'pending')) ?></span>
                                    </td>
                                    <td class="px-8 py-4 text-right">
                                        <span class="text-sm font-black text-indigo-600"><?= number_format((float) ($booking['total_price'] ?? 0), 0, ',', '.') ?>₫</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center text-slate-400 font-bold uppercase tracking-widest text-xs">Chưa có Booking nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Customers -->
        <div class="xl:col-span-4 bg-slate-900 rounded-[2rem] shadow-xl p-8 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <i class="fa-solid fa-user-plus text-6xl"></i>
            </div>
            <h3 class="text-lg font-black uppercase tracking-tight mb-6">Khách hàng mới nhất</h3>
            <div class="space-y-4 relative z-10">
                <?php if (!empty($recentUsers)): ?>
                    <?php foreach ($recentUsers as $user): ?>
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/10 transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-black">
                                    <?= strtoupper(substr($user['full_name'] ?? 'K', 0, 1)) ?>
                                </div>
                                <div class="flex-1 min-width-0">
                                    <div class="font-bold text-sm truncate"><?= htmlspecialchars((string) ($user['full_name'] ?? 'Khách hàng')) ?></div>
                                    <div class="text-[10px] text-slate-400 truncate"><?= htmlspecialchars((string) ($user['email'] ?? '')) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-slate-500 font-bold uppercase tracking-widest text-xs py-8">Chưa có khách học nào</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (() => {
        const revenueData = <?= json_encode($revenueChart ?? []) ?>;
        const bookingData = <?= json_encode($bookingChart ?? []) ?>;

        const revenueLabels = revenueData.map(item => item.chart_date);
        const revenueTotals = revenueData.map(item => Number(item.total || 0));

        const bookingLabels = bookingData.map(item => item.chart_date);
        const bookingTotals = bookingData.map(item => Number(item.total || 0));

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10, weight: '700' } } },
                x: { grid: { display: false }, ticks: { font: { size: 10, weight: '700' } } }
            }
        };

        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: revenueLabels,
                    datasets: [{
                        label: 'Doanh thu',
                        data: revenueTotals,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#4f46e5'
                    }]
                },
                options: chartOptions
            });
        }

        const bookingCtx = document.getElementById('bookingChart');
        if (bookingCtx) {
            new Chart(bookingCtx, {
                type: 'bar',
                data: {
                    labels: bookingLabels,
                    datasets: [{
                        label: 'Booking',
                        data: bookingTotals,
                        backgroundColor: '#10b981',
                        borderRadius: 8,
                        hoverBackgroundColor: '#059669'
                    }]
                },
                options: chartOptions
            });
        }
    })();
</script>

