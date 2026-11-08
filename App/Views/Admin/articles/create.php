<?php
$categories = $categories ?? [];
?>

<div class="max-w-[1400px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-feather-pointed"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Biên tập bản tin</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Creating System Media & Professional Content</p>
            </div>
        </div>
        <a href="<?= \App\Core\url('/admin/articles') ?>" class="px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-500 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all flex items-center gap-2 shadow-sm active:scale-95 text-xs uppercase tracking-widest italic">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <form action="<?= \App\Core\url('/admin/articles/store') ?>" method="POST" class="space-y-10">
        <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
            <!-- Left Column: Content Editor (8/12) -->
            <div class="xl:col-span-8 space-y-10">
                <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <div class="p-12 space-y-10">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Tiêu đề bài viết bản sắc <span class="text-indigo-500">*</span></label>
                            <div class="relative group">
                                <i class="fa-solid fa-heading absolute left-6 top-7 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                                <input type="text" name="title" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-900 text-xl tracking-tight shadow-inner" 
                                    placeholder="VD: Xu hướng thiết kế nội thất khách sạn 2024..." required>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Tóm lược nội dung (Excerpt)</label>
                            <div class="relative group">
                                <i class="fa-solid fa-quote-left absolute left-6 top-6 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                                <textarea name="excerpt" class="w-full pl-14 pr-6 py-5 rounded-[2rem] border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none min-h-[120px] text-slate-600 text-sm font-medium leading-relaxed italic shadow-inner" 
                                    placeholder="Nhập một đoạn mô tả ngắn về bài viết để hiển thị trên danh sách tin tức..."></textarea>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Khung nội dung chi tiết <span class="text-indigo-500">*</span></label>
                            <textarea name="content" class="w-full px-8 py-8 rounded-[2.5rem] border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none min-h-[500px] text-slate-800 text-base font-medium leading-loose shadow-inner" 
                                placeholder="Bắt đầu viết nội dung của bạn tại đây..." required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Metadata & Actions (4/12) -->
            <div class="xl:col-span-4 space-y-10">
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden sticky top-6">
                    <div class="p-10 space-y-10">
                        <div class="flex items-center gap-3 border-b border-slate-50 pb-6">
                            <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm shadow-lg shadow-slate-100">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] italic">Cấu hình xuất bản</h4>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Chế độ hiển thị</label>
                            <div class="relative group">
                                <select name="status" class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-[11px] uppercase tracking-widest shadow-inner appearance-none cursor-pointer">
                                    <option value="Draft">DRAFT (Bản nháp)</option>
                                    <option value="Published">PUBLISHED (Xuất bản)</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 text-[10px]"></i>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Chuyên mục lưu trữ</label>
                            <div class="relative group">
                                <select name="category_id" class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-[11px] uppercase tracking-widest shadow-inner appearance-none cursor-pointer">
                                    <option value="">-- Chọn chuyên mục --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fa-solid fa-layer-group absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 text-[10px]"></i>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-50 space-y-4">
                            <button type="submit" class="w-full group flex items-center justify-center gap-4 py-5 bg-slate-900 text-white rounded-[1.5rem] hover:bg-indigo-600 transition-all duration-300 shadow-2xl shadow-indigo-100 active:scale-95">
                                <span class="text-[11px] font-black uppercase tracking-[0.2em] italic group-hover:translate-x-1 transition-transform">Lưu bản thảo</span>
                                <i class="fa-solid fa-cloud-arrow-up text-lg group-hover:rotate-12 transition-transform"></i>
                            </button>

                <!-- Notifications -->
                <?php if (!empty($_GET['error'])): ?>
                    <div class="p-6 bg-rose-50 border border-rose-100 rounded-[1.5rem] flex items-center gap-4 animate-slide-down">
                        <div class="w-10 h-10 bg-rose-500 text-white rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-exclamation"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest">Lỗi nội dung</p>
                            <p class="text-xs font-bold text-rose-700">Vui lòng nhập đầy đủ Tiêu đề và Nội dung bài viết!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>


