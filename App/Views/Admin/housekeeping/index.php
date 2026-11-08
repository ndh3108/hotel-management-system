<?php
$rooms = $rooms ?? [];
$cleaningCount = count(array_filter($rooms, fn($r) => ($r['status'] ?? '') === 'CLEANING'));
$maintenanceCount = count(array_filter($rooms, fn($r) => ($r['status'] ?? '') === 'MAINTENANCE'));
$availableCount = count(array_filter($rooms, fn($r) => ($r['status'] ?? '') === 'AVAILABLE'));
?>

<div class="space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-broom"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Điều hành Buồng phòng</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Real-time Facility Management & Audit System</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="px-6 py-3 bg-white border border-slate-200 rounded-2xl flex items-center gap-3 shadow-sm group">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-black text-slate-900 uppercase tracking-widest italic" id="lastUpdated">Cập nhật: <?= date('H:i') ?></span>
            </div>
        </div>
    </div>

    <!-- Operational Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="bg-slate-900 p-8 rounded-[3rem] shadow-2xl shadow-slate-200/50 relative overflow-hidden group border-none">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform rotate-12 duration-500 text-white font-black text-8xl">
                <i class="fa-solid fa-house-circle-check"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2 italic">Sẵn sàng</p>
                <h3 class="text-4xl font-black text-white leading-none italic" id="availableCount"><?= $availableCount ?></h3>
                <p class="text-[9px] font-black text-indigo-400/50 uppercase tracking-widest mt-6 italic">Rooms Available</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-amber-500 text-8xl">
                <i class="fa-solid fa-soap"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Đang dọn</p>
                <h3 class="text-4xl font-black text-amber-500 leading-none italic" id="cleaningCount"><?= $cleaningCount ?></h3>
                <p class="text-[9px] font-black text-amber-400 uppercase tracking-widest mt-6 italic">Cleaning Phase</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform rotate-12 duration-500 text-rose-500 text-8xl">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Bảo trì</p>
                <h3 class="text-4xl font-black text-rose-500 leading-none italic" id="maintenanceCount"><?= $maintenanceCount ?></h3>
                <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest mt-6 italic">Equipment Failure</p>
            </div>
        </div>

        <div class="bg-indigo-50/50 p-8 rounded-[3rem] border border-indigo-100/50 relative overflow-hidden group flex items-center justify-center text-center">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-2 italic">Hiệu suất vận hành</p>
                <h3 class="text-2xl font-black text-slate-900 leading-none italic" id="efficiencyText"><?= ($availableCount > 0) ? round(($availableCount / count($rooms)) * 100) : 0 ?>%</h3>
                <div class="mt-4 w-32 h-1 bg-slate-100 rounded-full mx-auto overflow-hidden">
                    <div class="h-full bg-indigo-500 transition-all duration-1000" id="efficiencyBar" style="width: <?= ($availableCount > 0) ? ($availableCount / count($rooms)) * 100 : 0 ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Monitoring Grid -->
    <div id="housekeepingGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-8">
        <?php foreach ($rooms as $room): ?>
            <?php 
                $status = $room['display_status'] ?? 'AVAILABLE';
                $theme = match(strtoupper($status)) {
                    'AVAILABLE' => ['bg' => 'bg-white', 'color' => 'text-slate-900', 'accent' => 'bg-emerald-500', 'label' => 'SẴN SÀNG', 'border' => 'border-slate-100', 'icon' => 'fa-door-open'],
                    'CLEANING' => ['bg' => 'bg-amber-50/30', 'color' => 'text-amber-600', 'accent' => 'bg-amber-500', 'label' => 'ĐANG DỌN', 'border' => 'border-amber-100', 'icon' => 'fa-broom-wide'],
                    'MAINTENANCE' => ['bg' => 'bg-rose-50/30', 'color' => 'text-rose-600', 'accent' => 'bg-rose-500', 'label' => 'BẢO TRÌ', 'border' => 'border-rose-100', 'icon' => 'fa-faucet-drip'],
                    'OCCUPIED' => ['bg' => 'bg-indigo-50/30', 'color' => 'text-indigo-600', 'accent' => 'bg-indigo-500', 'label' => 'ĐANG Ở', 'border' => 'border-indigo-100', 'icon' => 'fa-user-check'],
                    default => ['bg' => 'bg-white', 'color' => 'text-slate-900', 'accent' => 'bg-slate-500', 'label' => 'KHÔNG XÁC ĐỊNH', 'border' => 'border-slate-100', 'icon' => 'fa-question'],
                };
            ?>
            
            <div class="room-card-wrapper" data-room-id="<?= $room['id'] ?>">
                <a href="<?= \App\Core\url('/admin/housekeeping/manage/' . (int) $room['id']) ?>" 
                   class="group relative <?= $theme['bg'] ?> p-8 rounded-[3rem] border <?= $theme['border'] ?> shadow-2xl shadow-slate-200/50 hover:shadow-indigo-100 hover:border-indigo-200 transition-all duration-500 overflow-hidden flex flex-col items-center text-center h-full">
                   
                    <div class="absolute top-6 right-8 flex items-center gap-2">
                        <span class="status-dot w-1.5 h-1.5 rounded-full <?= $theme['accent'] ?> <?= in_array(strtoupper($status), ['CLEANING', 'MAINTENANCE']) ? 'animate-pulse' : '' ?>"></span>
                        <span class="status-label text-[8px] font-black <?= $theme['color'] ?> uppercase tracking-widest italic"><?= $theme['label'] ?></span>
                    </div>

                    <div class="room-icon-box w-20 h-20 rounded-[2.5rem] bg-white shadow-inner border border-slate-50 flex items-center justify-center <?= $theme['color'] ?> text-3xl mb-6 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-500">
                        <i class="fa-solid <?= $theme['icon'] ?>"></i>
                    </div>

                    <h4 class="text-2xl font-black text-slate-900 uppercase italic tracking-tight mb-1">P.<?= htmlspecialchars($room['room_number']) ?></h4>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-6"><?= htmlspecialchars($room['room_type_name']) ?></p>

                    <div class="mt-auto w-full pt-6 border-t border-slate-50/50 flex items-center justify-between">
                        <div class="flex flex-col items-start">
                            <span class="text-[8px] font-black text-slate-300 uppercase tracking-[0.2em] italic mb-1">Protocol</span>
                            <span class="text-[9px] font-black text-slate-500 uppercase italic">AUDIT REQUIRED</span>
                        </div>
                        <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center text-xs shadow-lg shadow-slate-200 group-hover:bg-indigo-600 transition-colors">
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiUrl = "<?= \App\Core\url('/api/rooms') ?>";
    const lastUpdatedEl = document.getElementById('lastUpdated');

    function updateHousekeepingStatus() {
        fetch(apiUrl)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    let available = 0, cleaning = 0, maintenance = 0;
                    
                    res.data.forEach(room => {
                        const status = (room.display_status || room.status || 'AVAILABLE').toUpperCase();
                        if (status === 'AVAILABLE') available++;
                        else if (status === 'CLEANING') cleaning++;
                        else if (status === 'MAINTENANCE') maintenance++;

                        const wrapper = document.querySelector(`.room-card-wrapper[data-room-id="${room.id}"]`);
                        if (wrapper) {
                            const theme = getTheme(status);
                            const card = wrapper.querySelector('a');
                            const iconBox = wrapper.querySelector('.room-icon-box');
                            const statusLabel = wrapper.querySelector('.status-label');
                            const dot = wrapper.querySelector('.status-dot');
                            const icon = iconBox.querySelector('i');

                            // Update Card Theme
                            card.className = `group relative ${theme.bg} p-8 rounded-[3rem] border ${theme.border} shadow-2xl shadow-slate-200/50 hover:shadow-indigo-100 hover:border-indigo-200 transition-all duration-500 overflow-hidden flex flex-col items-center text-center h-full`;
                            
                            // Update Icon Box
                            iconBox.className = `room-icon-box w-20 h-20 rounded-[2.5rem] bg-white shadow-inner border border-slate-50 flex items-center justify-center ${theme.color} text-3xl mb-6 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-500`;
                            icon.className = `fa-solid ${theme.icon}`;

                            // Update Label & Dot
                            statusLabel.className = `status-label text-[8px] font-black ${theme.color} uppercase tracking-widest italic`;
                            statusLabel.textContent = theme.label;
                            
                            dot.className = `status-dot w-1.5 h-1.5 rounded-full ${theme.accent} ${['CLEANING', 'MAINTENANCE'].includes(status) ? 'animate-pulse' : ''}`;
                        }
                    });
                    
                    // Update Stats
                    document.getElementById('availableCount').textContent = available;
                    document.getElementById('cleaningCount').textContent = cleaning;
                    document.getElementById('maintenanceCount').textContent = maintenance;
                    
                    const total = res.data.length;
                    const efficiency = total > 0 ? Math.round((available / total) * 100) : 0;
                    document.getElementById('efficiencyText').textContent = `${efficiency}%`;
                    document.getElementById('efficiencyBar').style.width = `${efficiency}%`;

                    const now = new Date();
                    lastUpdatedEl.textContent = `Cập nhật: ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
                }
            })
            .catch(err => console.error('Housekeeping Sync Error:', err));
    }

    function getTheme(status) {
        const themes = {
            'AVAILABLE': { bg: 'bg-white', color: 'text-slate-900', accent: 'bg-emerald-500', label: 'SẴN SÀNG', border: 'border-slate-100', icon: 'fa-door-open' },
            'CLEANING': { bg: 'bg-amber-50/30', color: 'text-amber-600', accent: 'bg-amber-500', label: 'ĐANG DỌN', border: 'border-amber-100', icon: 'fa-broom-wide' },
            'MAINTENANCE': { bg: 'bg-rose-50/30', color: 'text-rose-600', accent: 'bg-rose-500', label: 'BẢO TRÌ', border: 'border-rose-100', icon: 'fa-faucet-drip' },
            'OCCUPIED': { bg: 'bg-indigo-50/30', color: 'text-indigo-600', accent: 'bg-indigo-500', label: 'ĐANG Ở', border: 'border-indigo-100', icon: 'fa-user-check' }
        };
        return themes[status] || themes['AVAILABLE'];
    }

    setInterval(updateHousekeepingStatus, 5000);
});
</script>
