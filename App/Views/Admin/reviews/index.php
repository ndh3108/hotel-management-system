<?php 
$reviews = $reviews ?? []; 
$totalReviews = count($reviews);
$avgRating = $totalReviews > 0 ? array_sum(array_column($reviews, 'rating')) / $totalReviews : 0;
$pendingCount = count(array_filter($reviews, fn($r) => ($r['status'] ?? 'Pending') === 'Pending'));
?>

<div class="space-y-8 animate-fade-in pb-20">
    <!-- Action Header -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-3 px-4 py-2 bg-indigo-50 rounded-2xl border border-indigo-100">
            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest leading-none"><?= $totalReviews ?> phản hồi khách hàng</span>
        </div>
        <form method="GET" action="<?= \App\Core\url('/admin/reviews') ?>" class="flex items-center gap-3">
            <div class="relative">
                <select name="status" class="appearance-none pl-6 pr-12 py-3 bg-white border border-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest outline-none focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all text-slate-700">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Pending" <?= ($currentStatus ?? '') === 'Pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                    <option value="Approved" <?= ($currentStatus ?? '') === 'Approved' ? 'selected' : '' ?>>Đã duyệt</option>
                    <option value="Rejected" <?= ($currentStatus ?? '') === 'Rejected' ? 'selected' : '' ?>>Đã ẩn</option>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 text-[8px] pointer-events-none"></i>
            </div>
            <button type="submit" class="px-6 py-3 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-slate-200">
                Lọc
            </button>
            <button type="submit" name="export" value="csv" class="w-12 h-12 flex items-center justify-center bg-white border border-slate-200 text-slate-500 rounded-2xl hover:bg-slate-50 transition-all active:scale-95 shadow-sm">
                <i class="fa-solid fa-file-csv"></i>
            </button>
        </form>
    </div>

    <!-- Review Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-indigo-900 p-6 rounded-[2.5rem] shadow-xl shadow-indigo-100 relative overflow-hidden group border-none">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform rotate-12 duration-500 text-white font-black text-7xl">
                <i class="fa-solid fa-star-half-stroke"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-1">Điểm đánh giá trung bình</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-white leading-none"><?= number_format($avgRating, 1) ?></h3>
                    <div class="flex text-amber-400 text-[10px]">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <i class="<?= $i <= floor($avgRating) ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                        <?php endfor; ?>
                    </div>
                </div>
                <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-4">System Average Rating</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-indigo-600">
                <i class="fa-solid fa-clock-rotate-left text-7xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Cần xử lý</p>
                <h3 class="text-3xl font-black text-indigo-600 leading-none"><?= $pendingCount ?></h3>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-4">Pending Approval</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group md:col-span-2">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-indigo-600">
                <i class="fa-solid fa-comments text-7xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Tỷ lệ hài lòng</p>
                <div class="flex items-center gap-6 mt-2">
                    <h3 class="text-3xl font-black text-slate-900 leading-none"><?= $totalReviews > 0 ? round(($totalReviews - $pendingCount)/$totalReviews * 100) : 0 ?>%</h3>
                    <div class="flex-1 h-3 bg-slate-50 rounded-full overflow-hidden border border-slate-100">
                        <div class="h-full bg-indigo-600 rounded-full" style="width: <?= $totalReviews > 0 ? round(($totalReviews - $pendingCount)/$totalReviews * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-4">Customer Satisfaction Index</p>
            </div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-10 border-b border-slate-50 bg-slate-50/20">
            <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Duyệt đánh giá của khách hàng</h2>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Kiểm duyệt nội dung và quản lý hiển thị phản hồi trên trang chủ</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/50">
                        <th class="px-10 py-6 text-left">Định danh Review</th>
                        <th class="px-6 py-6 text-left">Khách hàng / Phòng</th>
                        <th class="px-6 py-6 text-center">Xếp hạng</th>
                        <th class="px-6 py-6 text-left">Nội dung phản hồi</th>
                        <th class="px-6 py-6 text-center">Trạng thái</th>
                        <th class="px-10 py-6 text-right">Moderation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($reviews)): ?>
                        <tr>
                            <td colspan="6" class="px-10 py-32 text-center bg-slate-50/50 text-slate-300">
                                <i class="fa-solid fa-comment-slash text-5xl opacity-20 block mb-6 mx-auto"></i>
                                <h3 class="text-xl font-black uppercase tracking-widest">Không có dữ liệu</h3>
                                <p class="text-[10px] font-bold uppercase tracking-widest mt-2">Dữ liệu đánh giá hiện đang trống</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reviews as $r): ?>
                            <tr class="hover:bg-slate-50/80 transition-all group duration-300">
                                <td class="px-10 py-6">
                                    <span class="text-[11px] font-black text-slate-300 group-hover:text-indigo-400 transition-colors">REV-<?= str_pad((string)($r['id'] ?? 0), 4, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="px-6 py-6">
                                    <p class="font-black text-slate-900 text-sm uppercase leading-tight"><?= htmlspecialchars((string)($r['user_name'] ?? 'Khách lẻ')) ?></p>
                                    <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mt-1 italic flex items-center gap-1">
                                        <i class="fa-solid fa-bed text-[8px]"></i> Room <?= htmlspecialchars((string)($r['room_number'] ?? 'N/A')) ?>
                                    </p>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <div class="inline-flex flex-col items-center gap-1.5">
                                        <span class="text-sm font-black text-slate-700"><?= (int)($r['rating'] ?? 0) ?>/5</span>
                                        <div class="flex text-amber-400 text-[8px] gap-0.5">
                                            <?php for($i=1; $i<=5; $i++): ?>
                                                <i class="<?= $i <= (int)($r['rating'] ?? 0) ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="max-w-[300px]">
                                        <p class="text-xs text-slate-600 font-medium leading-relaxed italic line-clamp-2 group-hover:line-clamp-none transition-all duration-500">"<?= htmlspecialchars((string)($r['comment'] ?? '')) ?>"</p>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-center font-black">
                                    <?php 
                                        $status = $r['status'] ?? 'Pending';
                                        $sClass = match($status) {
                                            'Approved' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'Rejected' => 'bg-rose-50 text-rose-600 border-rose-100',
                                            default => 'bg-amber-50 text-amber-600 border-amber-100'
                                        };
                                        $sLabel = match($status) {
                                            'Approved' => 'Đã duyệt',
                                            'Rejected' => 'Đã ẩn',
                                            default => 'Chờ duyệt'
                                        };
                                    ?>
                                    <span class="inline-flex items-center px-4 py-2 rounded-full border text-[9px] uppercase tracking-widest <?= $sClass ?>">
                                        <span class="w-1 h-1 rounded-full mr-2 bg-current <?= $status === 'Pending' ? 'animate-pulse' : '' ?>"></span>
                                        <?= $sLabel ?>
                                    </span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <form method="POST" action="<?= \App\Core\url('/admin/reviews/' . (int)$r['id'] . '/moderate') ?>" class="flex items-center justify-end gap-2 group-hover:translate-x-0 translate-x-2 transition-transform">
                                        <?= csrf_field() ?>
                                        <div class="relative">
                                            <select name="status" class="appearance-none pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-100 border-dashed rounded-xl text-[9px] font-black uppercase tracking-widest outline-none focus:bg-white focus:border-indigo-400 hover:border-slate-300 transition-all text-slate-700 active:scale-95">
                                                <option value="Pending" <?= (($r['status'] ?? 'Pending') === 'Pending') ? 'selected' : '' ?>>Chờ duyệt</option>
                                                <option value="Approved" <?= (($r['status'] ?? '') === 'Approved') ? 'selected' : '' ?>>Phê duyệt</option>
                                                <option value="Rejected" <?= (($r['status'] ?? '') === 'Rejected') ? 'selected' : '' ?>>Từ chối</option>
                                            </select>
                                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[6px] pointer-events-none"></i>
                                        </div>
                                        <button type="submit" class="w-10 h-10 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-100 hover:bg-slate-900 transition-all active:scale-90 flex items-center justify-center">
                                            <i class="fa-solid fa-save text-[10px]"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


