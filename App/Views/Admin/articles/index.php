<?php
$articles = $articles ?? [];
$totalArticles = count($articles);
$publishedCount = count(array_filter($articles, fn($a) => ($a['status'] ?? '') === 'Published'));
?>

<div class="space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-feather-pointed"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Tạp chí & Kiến thức</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Editorial Content, Travel Guides & Insights</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center gap-3 px-4 py-2 bg-indigo-50 rounded-2xl border border-indigo-100">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest leading-none"><?= $totalArticles ?> Assets Synced</span>
            </div>
            <a href="<?= \App\Core\url('/admin/articles/create') ?>" class="px-8 py-4 rounded-2xl bg-slate-900 text-white font-black hover:bg-indigo-600 transition-all flex items-center gap-3 shadow-2xl shadow-indigo-100 active:scale-95 text-xs uppercase tracking-widest italic">
                <i class="fa-solid fa-plus-circle text-lg"></i> Soạn thảo nội dung
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="bg-slate-900 p-8 rounded-[3rem] shadow-2xl shadow-slate-200/50 relative overflow-hidden group border-none">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform rotate-12 duration-500 text-white font-black text-8xl">
                <i class="fa-solid fa-globe"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2 italic">Đã xuất bản</p>
                <h3 class="text-4xl font-black text-white leading-none italic"><?= number_format($publishedCount) ?></h3>
                <p class="text-[9px] font-black text-indigo-400/50 uppercase tracking-widest mt-6 italic">Live on Site</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-slate-900 text-8xl">
                <i class="fa-solid fa-pencil"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Bản nháp</p>
                <h3 class="text-4xl font-black text-slate-900 leading-none italic"><?= number_format($totalArticles - $publishedCount) ?></h3>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-6 italic">Draft Mode</p>
            </div>
        </div>

        <!-- Optional Placeholder for extra stats -->
        <div class="md:col-span-2 bg-indigo-50/50 p-8 rounded-[3rem] border border-indigo-100/50 flex items-center justify-between group">
            <div>
                <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-2 italic">Content Strategy Update</h4>
                <p class="text-[10px] text-slate-400 font-medium italic uppercase tracking-widest leading-loose">Toàn bộ nội dung được tối ưu SEO <br> và đồng bộ hiển thị đa nền tảng.</p>
            </div>
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-xl shadow-indigo-100 group-hover:rotate-12 transition-transform">
                <i class="fa-solid fa-chart-line text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-12 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
            <div>
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">Danh mục tài nguyên biên tập</h2>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2 italic">Quản lý các bài viết chuyên môn, tin tức và cảm hứng du lịch</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/30">
                        <th class="px-12 py-8 text-left italic">Bài viết & Nội dung</th>
                        <th class="px-8 py-8 text-left italic">Chuyên mục</th>
                        <th class="px-8 py-8 text-center italic">Trạng thái</th>
                        <th class="px-8 py-8 text-left italic">Timeline</th>
                        <th class="px-12 py-8 text-right italic">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($articles)): ?>
                        <tr>
                            <td colspan="5" class="px-12 py-40 text-center bg-slate-50/30">
                                <div class="w-24 h-24 bg-slate-100 rounded-[2.5rem] flex items-center justify-center text-slate-300 mx-auto mb-8 shadow-inner italic font-black text-4xl">
                                    <i class="fa-solid fa-scroll"></i>
                                </div>
                                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-widest italic">Chưa có bài viết</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-3 italic">Bắt đầu hành trình biên tập của bạn ngay hôm nay</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($articles as $a): ?>
                            <tr class="hover:bg-slate-50/80 transition-all group duration-300">
                                <td class="px-12 py-8">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center text-slate-300 group-hover:bg-slate-900 group-hover:text-white transition-all shadow-inner group-hover:shadow-xl group-hover:rotate-6">
                                            <i class="fa-solid fa-file-lines text-lg"></i>
                                        </div>
                                        <div class="max-w-[400px]">
                                            <p class="font-black text-slate-900 text-lg uppercase tracking-tight leading-tight italic truncate"><?= htmlspecialchars($a['title']) ?></p>
                                            <p class="text-[9px] font-black text-indigo-500 uppercase tracking-[0.3em] mt-2 flex items-center gap-2 italic">
                                                <span class="w-4 h-[1px] bg-indigo-200"></span>
                                                UID: <?= str_pad((string)$a['id'], 3, '0', STR_PAD_LEFT) ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-8">
                                    <span class="inline-block px-4 py-1.5 rounded-xl bg-slate-50 border border-slate-100 text-slate-600 text-[9px] font-black uppercase tracking-widest italic"><?= htmlspecialchars($a['category_name'] ?? 'General') ?></span>
                                </td>
                                <td class="px-8 py-8 text-center">
                                    <?php 
                                        $isPublished = ($a['status'] ?? '') === 'Published';
                                        $sClass = $isPublished ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100';
                                    ?>
                                    <span class="inline-flex items-center px-4 py-2 rounded-full border text-[9px] font-black uppercase tracking-widest italic <?= $sClass ?>">
                                        <span class="w-1.5 h-1.5 rounded-full mr-2 bg-current <?= $isPublished ? 'animate-pulse' : '' ?>"></span>
                                        <?= $isPublished ? 'Live' : 'Draft' ?>
                                    </span>
                                </td>
                                <td class="px-8 py-8">
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic"><?= date('M d, Y', strtotime($a['created_at'])) ?></p>
                                        <p class="text-[8px] font-medium text-slate-300 uppercase tracking-widest italic"><?= date('H:i A', strtotime($a['created_at'])) ?></p>
                                    </div>
                                </td>
                                <td class="px-12 py-8 text-right">
                                    <div class="flex items-center justify-end gap-3 translate-x-4 group-hover:translate-x-0 transition-all duration-500">
                                        <a href="<?= \App\Core\url('/admin/articles/' . (int) $a['id'] . '/edit') ?>" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm active:scale-90">
                                            <i class="fa-solid fa-pen-nib text-sm"></i>
                                        </a>
                                        <form action="<?= \App\Core\url('/admin/articles/' . (int) $a['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Toàn bộ nội dung bài viết sẽ bị xóa vĩnh viễn. Tiếp tục?');" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm active:scale-90">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between bg-white px-12 py-10 rounded-[3.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] italic">
            Hiển thị trang <span class="text-slate-900 px-2 py-1 bg-slate-50 border border-slate-100 rounded-lg mx-2"><?= (int)($pageNumber ?? 1) ?></span> tài liệu lưu trữ
        </p>
        <div class="flex items-center gap-4">
            <?php if ($pageNumber > 1): ?>
                <a href="<?= \App\Core\url('/admin/articles?page=' . ($pageNumber - 1)) ?>" class="px-8 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-600 font-black hover:bg-slate-50 transition-all transform active:scale-95 uppercase text-[10px] tracking-widest flex items-center gap-3">
                    <i class="fa-solid fa-chevron-left text-[8px]"></i> Trước
                </a>
            <?php endif; ?>
            
            <?php if ($hasMore): ?>
                <a href="<?= \App\Core\url('/admin/articles?page=' . ($pageNumber + 1) . '&cursor=' . $nextCursor) ?>" class="px-8 py-3.5 rounded-[1.25rem] bg-slate-900 text-white font-black hover:bg-slate-800 transition-all transform active:scale-95 uppercase text-[10px] tracking-widest flex items-center gap-3 shadow-xl shadow-slate-900/20">
                    Kế tiếp <i class="fa-solid fa-chevron-right text-[8px]"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>


