<?php
$error = $error ?? $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Booking Hotel | Admin Portal Authentication') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>
<body class="h-full bg-[#0a0a0c] flex items-center justify-center p-6 selection:bg-indigo-500 selection:text-white relative overflow-hidden">
    <!-- Abstract Background Deco -->
    <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-indigo-600/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-900/20 blur-[120px] rounded-full pointer-events-none"></div>
    
    <div class="w-full max-w-[480px] space-y-10 animate-fade-in relative z-10">
        <!-- Brand identity -->
        <div class="text-center space-y-4">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-600 rounded-[2.5rem] text-white text-3xl shadow-2xl shadow-indigo-600/20 mb-4 hover:rotate-12 transition-transform duration-500 italic font-black">
                <i class="fa-solid fa-cube"></i>
            </div>
            <h1 class="text-4xl font-black text-white font-outfit uppercase tracking-tighter italic">Cổng Quản Trị Hệ Thống</h1>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em] italic">Enterprise Intelligence & Management Authority</p>
        </div>

        <div class="bg-white/5 backdrop-blur-2xl p-12 rounded-[3.5rem] border border-white/10 shadow-2xl space-y-8">
            <div class="space-y-2">
                <h2 class="text-sm font-black text-indigo-400 uppercase tracking-widest italic">Xác thực danh tính</h2>
                <p class="text-slate-400 text-[11px] font-medium leading-relaxed italic">Vui lòng cung cấp thông tin quản trị viên để tiếp tục vào hệ thống điều hành.</p>
            </div>

            <?php if ($error): ?>
                <div class="p-5 bg-rose-500/10 border border-rose-500/20 rounded-2xl flex items-center gap-4 animate-shake">
                    <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest italic">
                        <?= $error === 'empty' ? 'Vui lòng điền đầy đủ dữ liệu' : 'Thông tin đăng nhập không chính xác' ?>
                    </p>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= \App\Core\url('/admin/login') ?>" class="space-y-6">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block italic ml-1 underline underline-offset-4 decoration-indigo-500/30">Email Công vụ</label>
                    <div class="relative group">
                        <i class="fa-solid fa-envelope absolute left-6 top-1/2 -translate-y-1/2 text-slate-600 text-xs group-focus-within:text-indigo-500 transition-colors"></i>
                        <input type="email" name="email" required 
                               class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-14 pr-6 text-white text-sm font-black outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder:text-slate-700"
                               placeholder="admin@hotel.enterprise.com">
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block italic ml-1 underline underline-offset-4 decoration-indigo-500/30">Mật mã bảo mật</label>
                    <div class="relative group">
                        <i class="fa-solid fa-key absolute left-6 top-1/2 -translate-y-1/2 text-slate-600 text-xs group-focus-within:text-indigo-500 transition-colors"></i>
                        <input type="password" name="password" required 
                               class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-14 pr-6 text-white text-sm font-black outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder:text-slate-700"
                               placeholder="••••••••••••">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-black py-5 rounded-[2rem] shadow-xl shadow-indigo-600/20 transition-all active:scale-[0.98] flex items-center justify-center gap-3 uppercase tracking-widest text-[11px] italic">
                        Tiến vào hệ thống <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center">
            <p class="text-[9px] font-black text-slate-600 uppercase tracking-[0.2em] italic">© <?= date('Y') ?> Advanced Hotel Management Ecosystem — Technical Ops</p>
        </div>
    </div>
</body>
</html>

