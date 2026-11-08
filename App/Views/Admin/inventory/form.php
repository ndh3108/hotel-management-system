<div class="max-w-[1200px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid <?= $inventory ? 'fa-boxes-packing' : 'fa-folder-plus' ?>"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic"><?= $inventory ? 'Hiệu chỉnh' : 'Thiết lập' ?> Định mức Vật tư</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic"><?= $inventory ? 'Refining Operational Inventory' : 'Deploying New Inventory Asset' ?></p>
            </div>
        </div>
        <a href="<?= \App\Core\url('/admin/inventory') ?>" class="px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-500 font-black hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all flex items-center gap-2 shadow-sm active:scale-95 text-xs uppercase tracking-widest italic">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-12">
            <form action="<?= $inventory ? \App\Core\url('/admin/inventory/' . $inventory['id'] . '/update') : \App\Core\url('/admin/inventory/store') ?>" method="POST" class="space-y-12">
                <?= csrf_field() ?>
                
                <!-- Core Configuration Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Chọn vật tư định danh <span class="text-indigo-500">*</span></label>
                        <div class="relative group">
                            <i class="fa-solid fa-box-open absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <select name="item_id" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-inner appearance-none" required>
                                <option value="">-- Chọn vật tư --</option>
                                <?php foreach ($catalogItems as $item): ?>
                                    <option value="<?= $item['id'] ?>" <?= ($inventory && $inventory['item_id'] == $item['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($item['name']) ?> (Gốc: <?= number_format($item['default_price'], 0, ',', '.') ?>đ)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Số lượng định biên <span class="text-indigo-500">*</span></label>
                        <div class="relative group">
                            <i class="fa-solid fa-layer-group absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="number" name="quantity" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm shadow-inner"
                                value="<?= $inventory['quantity'] ?? 1 ?>" min="1" required>
                        </div>
                    </div>
                </div>

                <!-- Allocation Strategy -->
                <div class="space-y-8 bg-slate-50/30 p-10 rounded-[3rem] border border-slate-100 shadow-inner">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-6">
                        <div class="w-10 h-10 rounded-xl bg-white text-indigo-600 flex items-center justify-center text-sm shadow-sm border border-slate-100">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] italic">Chiến lược phân bổ hạ tầng</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-6">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Loại hình đối tượng</label>
                            <div class="flex items-center gap-6">
                                <label class="flex-1 relative cursor-pointer group">
                                    <input type="radio" name="assign_type" value="type" <?= (!$inventory || $inventory['room_type_id']) ? 'checked' : '' ?> onclick="toggleAssign(this.value)" class="sr-only peer">
                                    <div class="w-full py-4 px-6 bg-white border border-slate-100 rounded-2xl text-center peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all shadow-sm group-hover:shadow-md">
                                        <span class="text-[10px] font-black text-slate-400 peer-checked:text-indigo-700 uppercase tracking-widest italic group-hover:text-slate-600">Theo Loại Phòng</span>
                                    </div>
                                </label>
                                <label class="flex-1 relative cursor-pointer group">
                                    <input type="radio" name="assign_type" value="room" <?= ($inventory && $inventory['room_id']) ? 'checked' : '' ?> onclick="toggleAssign(this.value)" class="sr-only peer">
                                    <div class="w-full py-4 px-6 bg-white border border-slate-100 rounded-2xl text-center peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all shadow-sm group-hover:shadow-md">
                                        <span class="text-[10px] font-black text-slate-400 peer-checked:text-indigo-700 uppercase tracking-widest italic group-hover:text-slate-600">Theo Phòng Cụ Thể</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div id="assign_type_select" class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic" id="assign_label">Đích danh hệ thống</label>
                            <div class="relative group">
                                <i class="fa-solid fa-crosshairs absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-indigo-600 transition-colors"></i>
                                <select name="room_type_id" id="room_type_select" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-sm appearance-none" <?= ($inventory && $inventory['room_id']) ? 'disabled' : '' ?>>
                                    <option value="">-- Chọn loại phòng --</option>
                                    <?php foreach ($roomTypes as $rt): ?>
                                        <option value="<?= $rt['id'] ?>" <?= ($inventory && $inventory['room_type_id'] == $rt['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($rt['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <select name="room_id" id="room_select" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm uppercase tracking-tight shadow-sm appearance-none" style="display:none;" <?= (!$inventory || $inventory['room_type_id']) ? 'disabled' : '' ?>>
                                    <option value="">-- Chọn phòng --</option>
                                    <?php foreach ($rooms as $r): ?>
                                        <option value="<?= $r['id'] ?>" <?= ($inventory && $inventory['room_id'] == $r['id']) ? 'selected' : '' ?>>
                                            PHÒNG <?= htmlspecialchars($r['room_number']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Điều chỉnh giá trị (Override Price)</label>
                        <div class="relative group">
                            <i class="fa-solid fa-coins absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs group-focus-within:text-emerald-600 transition-colors"></i>
                            <input type="number" name="price_override" class="w-full pl-14 pr-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm shadow-inner" 
                                value="<?= $inventory['price_override'] ?? '' ?>" placeholder="Để trống nếu sử dụng giá niêm yết">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Trạng thái định biên</label>
                        <select name="is_active" class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50/50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 text-sm italic uppercase tracking-tight shadow-inner appearance-none">
                            <option value="1" <?= (!$inventory || $inventory['is_active']) ? 'selected' : '' ?>>ACTIVE (Sẵn sàng)</option>
                            <option value="0" <?= ($inventory && !$inventory['is_active']) ? 'selected' : '' ?>>INACTIVE (Vô hiệu)</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Action Section -->
                <div class="pt-10 border-t border-slate-50 flex items-center justify-end gap-6 text-center">
                    <button type="reset" class="text-[10px] font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-colors italic">Làm mới dữ liệu</button>
                    <button type="submit" class="group flex items-center gap-4 px-12 py-5 bg-slate-900 text-white rounded-[2rem] hover:bg-indigo-600 transition-all duration-300 shadow-2xl shadow-indigo-100 active:scale-95">
                        <span class="text-[11px] font-black uppercase tracking-[0.2em] italic group-hover:translate-x-1 transition-transform"><?= $inventory ? 'Cập nhật định mức' : 'Xác lập định mức mới' ?></span>
                        <i class="fa-solid fa-save text-lg group-hover:rotate-12 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleAssign(val) {
    const rtSelect = document.getElementById('room_type_select');
    const rSelect = document.getElementById('room_select');
    const label = document.getElementById('assign_label');

    if (val === 'room') {
        rtSelect.style.display = 'none';
        rtSelect.disabled = true;
        rSelect.style.display = 'block';
        rSelect.disabled = false;
        label.innerText = 'Đích danh: PHÒNG CỤ THỂ';
    } else {
        rtSelect.style.display = 'block';
        rtSelect.disabled = false;
        rSelect.style.display = 'none';
        rSelect.disabled = true;
        label.innerText = 'Đích danh: LOẠI PHÒNG';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const checked = document.querySelector('input[name="assign_type"]:checked');
    if(checked) toggleAssign(checked.value);
});
</script>

