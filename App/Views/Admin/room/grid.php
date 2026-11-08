<div class="space-y-8 animate-fade-in pb-20">
    <!-- Filters & Actions -->
    <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6 overflow-x-auto pb-2 md:pb-0">
            <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 rounded-xl border border-emerald-100 shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">Sẵn sàng</span>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-rose-50 rounded-xl border border-rose-100 shrink-0">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span class="text-[10px] font-black text-rose-700 uppercase tracking-widest">Đang ở</span>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-amber-50 rounded-xl border border-amber-100 shrink-0">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="text-[10px] font-black text-amber-700 uppercase tracking-widest">Đang dọn</span>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-xl border border-slate-200 shrink-0">
                <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                <span class="text-[10px] font-black text-slate-700 uppercase tracking-widest">Bảo trì</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative">
                <select id="filterFloor" class="pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-black text-slate-600 appearance-none outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer min-w-[140px]">
                    <option value="">Tất cả tầng</option>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
            </div>
            <div class="relative">
                <select id="filterStatus" class="pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-black text-slate-600 appearance-none outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer min-w-[160px]">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Available">Sẵn sàng</option>
                    <option value="Occupied">Đang ở</option>
                    <option value="Cleaning">Đang dọn dẹp</option>
                    <option value="Maintenance">Bảo trì</option>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
            </div>
        </div>
    </div>

    <!-- Room Grid Container -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6" id="roomGridContainer">
        <!-- Loading State -->
        <div class="col-span-full py-20 flex flex-col items-center gap-4" id="gridLoading">
            <div class="w-12 h-12 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Đang tải dữ liệu phòng...</p>
        </div>
    </div>
</div>

<!-- Premium Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-[2.5rem] border-none shadow-2xl overflow-hidden">
            <div class="bg-indigo-600 p-8 text-white relative">
                <div class="absolute right-8 top-8 opacity-20 rotate-12">
                    <i class="fa-solid fa-door-closed text-6xl"></i>
                </div>
                <h3 class="text-xl font-black uppercase tracking-tight" id="statusModalTitle">Cập nhật trạng thái</h3>
                <p class="text-[10px] font-bold text-indigo-100 uppercase tracking-widest mt-1">Vận hành buồng phòng & bảo trì</p>
            </div>
            <div class="p-8">
                <form id="statusForm" class="space-y-6">
                    <input type="hidden" id="modalRoomId" />
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Trạng thái mới</label>
                        <select id="modalNewStatus" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold text-slate-700 appearance-none cursor-pointer" required>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Ghi chú (Tùy chọn)</label>
                        <textarea id="modalNotes" class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold text-slate-700 min-h-[100px]" placeholder="Nhập lý do thay đổi..."></textarea>
                    </div>
                </form>
            </div>
            <div class="p-8 bg-slate-50 flex items-center justify-end gap-3">
                <button type="button" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-xs uppercase tracking-widest" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="px-6 py-3 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-500 transition-all shadow-xl shadow-indigo-100 text-xs uppercase tracking-widest" id="btnSaveStatus">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.userRole = <?= json_encode($userRole) ?>;
    window.apiUrl = "<?= \App\Core\url('/api/rooms') ?>";
</script>
<script src="<?= \App\Core\url('/assets/js/room-grid.js') ?>"></script>


