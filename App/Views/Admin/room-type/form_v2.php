<?php
$selectedAmenities = array_map('intval', $selectedAmenities ?? []);
?>

<div class="max-w-[1400px] mx-auto space-y-8 animate-fade-in pb-20">
    <!-- Notifications -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center gap-3 shadow-sm animate-slide-down">
            <i class="fa-solid fa-circle-check"></i>
            <span class="font-bold"><?= htmlspecialchars((string)$_SESSION['success']) ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="p-5 rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 flex items-center gap-3 shadow-sm animate-slide-down">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span class="font-bold"><?= htmlspecialchars((string)$_SESSION['error']) ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="<?= \App\Core\url($roomType ? '/admin/room-type/' . (int) $roomType['id'] . '/update' : '/admin/room-type/store') ?>" method="POST" enctype="multipart/form-data" id="roomTypeFormV2">
        <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
            
            <!-- Main Configuration (8/12) -->
            <div class="xl:col-span-8 space-y-10">
                
                <!-- Section 1: Basic Identity -->
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <div class="p-10 space-y-10">
                        <div class="flex items-center gap-4 border-b border-slate-50 pb-8">
                            <a href="<?= \App\Core\url('/admin/room-type') ?>" class="w-12 h-12 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-500 rounded-2xl transition-all active:scale-90">
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight"><?= $roomType ? 'Cấu hình chi tiết' : 'Khởi tạo hạng phòng' ?></h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Định danh và thiết lập giá trị cơ sở</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tên hạng phòng <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-black text-slate-800 text-lg placeholder:font-medium" value="<?= htmlspecialchars((string) ($roomType['name'] ?? '')) ?>" placeholder="VD: Deluxe King Suite" required>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Đường dẫn thân thiện (Slug)</label>
                                <input type="text" name="slug" class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none text-slate-500 bg-slate-50 font-bold" value="<?= htmlspecialchars((string) ($roomType['slug'] ?? '')) ?>" placeholder="Tự động tạo từ tên nếu để trống">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Giá cơ bản / Đêm <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="base_price" class="w-full pl-6 pr-16 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-black text-indigo-600 text-2xl" min="0" step="1000" value="<?= htmlspecialchars((string) ($roomType['base_price'] ?? '0')) ?>" required>
                                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 font-black text-[10px] tracking-widest">VNĐ</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kích thước hiển thị</label>
                                <input type="text" name="room_size" class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none text-slate-700 font-black" value="<?= htmlspecialchars((string) ($roomType['room_size'] ?? '')) ?>" placeholder="VD: 35 m²">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Diện tích quy chuẩn</label>
                                <div class="relative">
                                    <input type="number" name="area_sqm" class="w-full pl-6 pr-12 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-black text-slate-700" min="0" step="0.5" value="<?= htmlspecialchars((string) ($roomType['area_sqm'] ?? '')) ?>">
                                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 font-black text-[10px] tracking-widest">M²</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mô tả giới thiệu</label>
                            <textarea name="description" class="w-full px-6 py-5 rounded-3xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none min-h-[120px] text-slate-700 leading-relaxed font-medium placeholder:font-normal" placeholder="Mô tả các đặc điểm nổi bật của hạng phòng này..."><?= htmlspecialchars((string) ($roomType['description'] ?? '')) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Capacity & Beds -->
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-10 space-y-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-500 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Quy chuẩn lưu trú</h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Sức chứa tối đa và cấu hình giường</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Người lớn</label>
                            <input type="number" name="capacity_adults" class="w-full px-6 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800" min="1" value="<?= (int) ($roomType['capacity_adults'] ?? 1) ?>" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Trẻ em</label>
                            <input type="number" name="capacity_children" class="w-full px-6 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800" min="0" value="<?= (int) ($roomType['capacity_children'] ?? 0) ?>">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tổng khách tối đa</label>
                            <input type="number" name="max_occupancy" class="w-full px-6 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-indigo-600" min="0" value="<?= (int) ($roomType['max_occupancy'] ?? 0) ?>">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Loại giường</label>
                            <input type="text" name="bed_type" class="w-full px-6 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all outline-none font-black text-slate-800 placeholder:font-normal" value="<?= htmlspecialchars((string) ($roomType['bed_type'] ?? '')) ?>" placeholder="VD: 1 King, 2 Twin...">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Professional Policies -->
                <div class="bg-slate-900 rounded-[2.5rem] shadow-2xl shadow-indigo-900/20 overflow-hidden text-white border-none min-h-[400px] flex flex-col">
                    <div class="p-10 border-b border-white/5 flex items-center gap-4">
                        <div class="w-10 h-10 bg-white/10 text-indigo-300 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black uppercase tracking-tight">Chính sách vận hành</h3>
                            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mt-0.5">Tiêu chuẩn nhận phòng & hoàn hủy chuyên nghiệp</p>
                        </div>
                    </div>
                    
                    <div class="p-10 flex-1 grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-8">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black uppercase text-indigo-300 tracking-widest ml-1">Giờ nhận phòng</label>
                                    <input type="text" name="check_in_time" class="w-full px-6 py-4 rounded-2xl bg-white/5 border border-white/10 focus:bg-white/10 focus:border-indigo-500 transition-all outline-none font-black text-white" value="<?= htmlspecialchars((string) ($roomType['check_in_time'] ?? '14:00')) ?>">
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black uppercase text-indigo-300 tracking-widest ml-1">Giờ trả phòng</label>
                                    <input type="text" name="check_out_time" class="w-full px-6 py-4 rounded-2xl bg-white/5 border border-white/10 focus:bg-white/10 focus:border-indigo-500 transition-all outline-none font-black text-white" value="<?= htmlspecialchars((string) ($roomType['check_out_time'] ?? '12:00')) ?>">
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-indigo-300 tracking-widest ml-1">Chính sách Check-in / Check-out</label>
                                <textarea name="policy_check_in" class="w-full px-6 py-4 rounded-3xl bg-white/5 border border-white/10 focus:bg-white/10 focus:border-indigo-500 transition-all outline-none min-h-[100px] text-sm leading-relaxed" placeholder="Lưu ý về nhận phòng sớm hoặc trả phòng muộn..."><?= htmlspecialchars((string) ($roomType['policy_check_in'] ?? '')) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="space-y-8">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black uppercase text-indigo-300 tracking-widest ml-1">Số đêm tối thiểu</label>
                                    <input type="number" name="min_stay" class="w-full px-6 py-4 rounded-2xl bg-white/5 border border-white/10 focus:bg-white/10 focus:border-indigo-500 transition-all outline-none font-black text-white text-center" min="1" value="<?= (int) ($roomType['min_stay'] ?? 1) ?>">
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black uppercase text-indigo-300 tracking-widest ml-1">Số đêm tối đa</label>
                                    <input type="number" name="max_stay" class="w-full px-6 py-4 rounded-2xl bg-white/5 border border-white/10 focus:bg-white/10 focus:border-indigo-500 transition-all outline-none font-black text-white text-center" min="1" value="<?= (int) ($roomType['max_stay'] ?? 30) ?>">
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-indigo-300 tracking-widest ml-1">Chính sách Hủy phòng & Hoàn tiền</label>
                                <textarea name="policy_cancel" class="w-full px-6 py-4 rounded-3xl bg-white/5 border border-white/10 focus:bg-white/10 focus:border-indigo-500 transition-all outline-none min-h-[100px] text-sm leading-relaxed" placeholder="Các điều kiện về hủy phòng và hoàn tiền..."><?= htmlspecialchars((string) ($roomType['policy_cancel'] ?? '')) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Amenities & Inventory -->
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-10 space-y-12">
                    <div>
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-concierge-bell"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Tiện nghi & Dịch vụ đi kèm</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Các tiện ích tiêu chuẩn cho khách hàng</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                            <?php if (!empty($amenities)): ?>
                                <?php foreach ($amenities as $amenity): ?>
                                    <?php $amenityId = (int) ($amenity['id'] ?? 0); ?>
                                    <?php $isSelected = in_array($amenityId, $selectedAmenities, true); ?>
                                    <label class="flex items-center gap-3 p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-indigo-200 group <?= $isSelected ? 'bg-indigo-50 border-indigo-600 shadow-md shadow-indigo-100/50' : 'bg-white border-slate-100' ?>">
                                        <div class="relative flex items-center justify-center w-6 h-6 shrink-0">
                                            <input type="checkbox" name="amenities[]" value="<?= $amenityId ?>" 
                                                   class="w-6 h-6 rounded-md border-slate-300 text-indigo-600 focus:ring-indigo-500/20 transition-all cursor-pointer"
                                                   <?= $isSelected ? 'checked' : '' ?>>
                                        </div>
                                        <span class="text-xs font-black uppercase tracking-widest <?= $isSelected ? 'text-indigo-900' : 'text-slate-500 group-hover:text-slate-900' ?>">
                                            <?= htmlspecialchars((string) ($amenity['name'] ?? 'Amenity')) ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Trang bị & Vật tư quy định</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Danh mục vật tư cơ sở cần kiểm soát</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-slate-50 p-8 rounded-[2rem] max-h-[350px] overflow-y-auto thin-scrollbar border border-slate-100">
                            <?php if (!empty($allInventory)): ?>
                                <?php foreach ($allInventory as $inv): ?>
                                    <?php $invId = (int) ($inv['id'] ?? 0); ?>
                                    <?php $isSelInv = in_array($invId, $selectedSupplies ?? [], true); ?>
                                    <label class="flex items-center gap-3 p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-amber-200 group <?= $isSelInv ? 'bg-amber-50 border-amber-500 shadow-md shadow-amber-100/50' : 'bg-white border-slate-100' ?>">
                                        <div class="relative flex items-center justify-center w-6 h-6 shrink-0">
                                            <input type="checkbox" name="supplies[]" value="<?= $invId ?>" 
                                                   class="w-6 h-6 rounded-md border-slate-300 text-amber-600 focus:ring-amber-500/20 transition-all cursor-pointer"
                                                   <?= $isSelInv ? 'checked' : '' ?>>
                                        </div>
                                        <span class="text-xs font-black uppercase tracking-widest <?= $isSelInv ? 'text-amber-900' : 'text-slate-500 group-hover:text-slate-900' ?>">
                                            <?= htmlspecialchars((string) ($inv['name'] ?? 'Item')) ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="col-span-full py-10 text-center text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">Kho vật tư hiện đang trống</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Actions & Media (4/12) -->
            <div class="xl:col-span-4 space-y-10">
                
                <!-- Card: Status & Actions -->
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100 p-10 space-y-8 sticky top-6">
                    <div class="flex items-center justify-between border-b border-slate-50 pb-6">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Trạng thái</h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Hiển thị công khai</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" <?= (!empty($roomType['is_active']) ? 'checked' : '') ?>>
                            <div class="w-14 h-8 bg-slate-100 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-200 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600 shadow-inner"></div>
                        </label>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Video giới thiệu (YouTube)</label>
                        <div class="relative group">
                            <input type="url" name="youtube_url" class="w-full pl-12 pr-6 py-4 rounded-2xl border border-slate-200 focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all outline-none font-bold text-slate-600 text-sm" value="<?= htmlspecialchars((string) ($roomType['video_url'] ?? '')) ?>" placeholder="Link dán tại đây...">
                            <i class="fa-brands fa-youtube absolute left-4 top-1/2 -translate-y-1/2 text-rose-500 text-xl opacity-80 group-focus-within:opacity-100 transition-opacity"></i>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 pt-4">
                        <button type="submit" id="roomTypeSubmitBtn" class="w-full py-5 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-[1.5rem] transition-all shadow-2xl shadow-indigo-200 flex items-center justify-center gap-3 active:scale-95 disabled:opacity-50 uppercase text-xs tracking-widest">
                            <i class="fa-solid fa-save"></i>
                            <?= $roomType ? 'Lưu thay đổi' : 'Khởi tạo ngay' ?>
                        </button>
                        <a href="<?= \App\Core\url('/admin/room-type') ?>" class="w-full py-4 bg-slate-50 hover:bg-slate-100 text-slate-500 font-black rounded-[1.5rem] transition-all text-center uppercase text-[10px] tracking-widest border border-slate-100">Hủy cấu hình</a>
                    </div>
                </div>

                <!-- Card: Visual Gallery -->
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
                    <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-camera-retro"></i>
                            </div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Bộ sưu tập ảnh</h3>
                        </div>
                        <span class="text-[9px] font-black px-2.5 py-1 bg-slate-100 rounded-lg text-slate-500 uppercase"><?= count($roomType['images'] ?? []) ?> FILE</span>
                    </div>
                    <div class="p-8 space-y-8">
                        <div class="p-8 rounded-[2rem] bg-indigo-50/30 border-2 border-dashed border-indigo-100 hover:border-indigo-300 transition-all text-center group relative cursor-pointer overflow-hidden">
                            <input type="file" name="images[]" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*" multiple>
                            <div class="space-y-3">
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center mx-auto text-indigo-500 shadow-sm group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                                <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest">Thêm hình ảnh chất lượng cao</p>
                            </div>
                            <div class="absolute inset-x-0 bottom-0 h-1 bg-indigo-500 scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
                        </div>

                        <?php if (!empty($roomType['images'])): ?>
                            <div class="grid grid-cols-2 gap-4">
                                <?php foreach ($roomType['images'] as $image): ?>
                                    <?php $isPrimary = (bool)($image['is_primary'] ?? false); ?>
                                    <div class="relative group aspect-[4/3] rounded-2xl overflow-hidden border-2 transition-all hover:shadow-lg <?= $isPrimary ? 'border-amber-400 shadow-md shadow-amber-50' : 'border-slate-100' ?>">
                                        <img src="<?= htmlspecialchars((string) ($image['image_url'] ?? $image['image_path'] ?? '')) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                        
                                        <?php if ($isPrimary): ?>
                                            <div class="absolute top-3 left-3 px-2 py-1 bg-amber-500 text-white text-[8px] font-black uppercase rounded-lg shadow-lg z-10">
                                                Ảnh chính
                                            </div>
                                        <?php endif; ?>

                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center p-4 gap-3">
                                            <?php if (!$isPrimary): ?>
                                                <button type="button" 
                                                        onclick="document.getElementById('set-primary-<?= $image['id'] ?>').submit();"
                                                        class="w-8 h-8 rounded-xl bg-white/20 hover:bg-amber-500 text-white flex items-center justify-center backdrop-blur-md transition-all shadow-lg active:scale-90"
                                                        title="Đặt làm ảnh chính">
                                                    <i class="fa-solid fa-star text-[10px]"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <button type="button" 
                                                    onclick="if(confirm('Gỡ bỏ hình ảnh này khỏi bộ sưu tập?')){document.getElementById('delete-img-<?= $image['id'] ?>').submit();}"
                                                    class="w-8 h-8 rounded-xl bg-white/20 hover:bg-rose-500 text-white flex items-center justify-center backdrop-blur-md transition-all shadow-lg active:scale-90"
                                                    title="Xóa ảnh">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Hidden Removal Processes -->
    <?php if (!empty($roomType['images'])): ?>
        <?php foreach ($roomType['images'] as $image): ?>
            <form id="delete-img-<?= $image['id'] ?>" 
                    action="<?= \App\Core\url('/admin/room-type/' . (int) ($roomType['id'] ?? 0) . '/images/' . (int) $image['id'] . '/delete' . ((isset($image['source']) && $image['source'] === 'room_images') ? '?source=room_images' : '')) ?>" 
                    method="POST" style="display: none;">
                <?= csrf_field() ?>
            </form>
            <form id="set-primary-<?= $image['id'] ?>" 
                    action="<?= \App\Core\url('/admin/room-type/' . (int) ($roomType['id'] ?? 0) . '/images/' . (int) $image['id'] . '/set-primary') ?>" 
                    method="POST" style="display: none;">
                <?= csrf_field() ?>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
    .thin-scrollbar::-webkit-scrollbar { width: 4px; }
    .thin-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .thin-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .thin-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('roomTypeFormV2');
    const submitBtn = document.getElementById('roomTypeSubmitBtn');
    if (!form || !submitBtn) return;

    form.onsubmit = function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Đang chuẩn bị dữ liệu...';
    };
});
</script>

