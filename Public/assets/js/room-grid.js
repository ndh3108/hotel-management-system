document.addEventListener('DOMContentLoaded', function () {
    const gridContainer = document.getElementById('roomGridContainer');
    const loading = document.getElementById('gridLoading');
    const filterFloor = document.getElementById('filterFloor');
    const filterStatus = document.getElementById('filterStatus');
    
    const statusModalElement = document.getElementById('statusModal');
    const statusModal = statusModalElement ? new bootstrap.Modal(statusModalElement) : null;
    const modalRoomId = document.getElementById('modalRoomId');
    const modalNewStatus = document.getElementById('modalNewStatus');
    const modalNotes = document.getElementById('modalNotes');
    const btnSaveStatus = document.getElementById('btnSaveStatus');
    const statusModalTitle = document.getElementById('statusModalTitle');

    let allRooms = [];

    const statusMap = {
        'Available': { label: 'Sẵn sàng', color: 'emerald', icon: 'fa-check-circle' },
        'Occupied': { label: 'Đang ở', color: 'rose', icon: 'fa-user-check' },
        'Cleaning': { label: 'Đang dọn', color: 'amber', icon: 'fa-broom' },
        'Maintenance': { label: 'Bảo trì', color: 'slate', icon: 'fa-tools' }
    };

    const stateRules = {
        'Manager': {
            'Available': ['Occupied', 'Maintenance', 'Cleaning'],
            'Occupied': ['Cleaning', 'Available'],
            'Cleaning': ['Available', 'Maintenance'],
            'Maintenance': ['Available', 'Cleaning']
        },
        'Receptionist': {
            'Available': ['Occupied'],
            'Occupied': ['Cleaning']
        },
        'Housekeeping': {
            'Cleaning': ['Available']
        }
    };

    function getAllowedTransitions(currentStatus, role) {
        if (!stateRules[role]) return [];
        return stateRules[role][currentStatus] || [];
    }

    function fetchRooms() {
        if (!window.apiUrl) return;
        fetch(window.apiUrl)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    allRooms = res.data;
                    populateFloors();
                    renderGrid();
                }
            })
            .catch(err => console.error('Fetch error:', err))
            .finally(() => {
                if (loading) loading.style.display = 'none';
            });
    }

    function populateFloors() {
        const floors = new Set(allRooms.map(r => r.floor).filter(f => f));
        const currentFloor = filterFloor.value;
        filterFloor.innerHTML = '<option value="">Tất cả tầng</option>';
        [...floors].sort((a,b)=>a-b).forEach(f => {
            const opt = document.createElement('option');
            opt.value = f;
            opt.textContent = 'Tầng ' + f;
            if (currentFloor == f) opt.selected = true;
            filterFloor.appendChild(opt);
        });
    }

    function renderGrid() {
        gridContainer.innerHTML = '';
        let filtered = allRooms;
        
        if (filterFloor.value) filtered = filtered.filter(r => r.floor == filterFloor.value);
        if (filterStatus.value) filtered = filtered.filter(r => r.status === filterStatus.value);

        if (filtered.length === 0) {
            gridContainer.innerHTML = `
                <div class="col-span-full py-20 text-center">
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Không tìm thấy phòng phù hợp</p>
                </div>
            `;
            return;
        }

        filtered.forEach(room => {
            const allowed = getAllowedTransitions(room.status, window.userRole);
            const ui = statusMap[room.status] || { label: room.status, color: 'slate', icon: 'fa-question-circle' };
            
            const card = document.createElement('div');
            card.className = "group animate-scale-in";
            
            let actionBtn = '';
            if (allowed.length > 0) {
                actionBtn = `
                    <button class="btn-change-status mt-4 w-full py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-[10px] font-black uppercase tracking-widest text-white transition-all backdrop-blur-sm" 
                            data-id="${room.id}" data-status="${room.status}" data-number="${room.room_number}">
                        Cập nhật
                    </button>
                `;
            } else {
                actionBtn = `
                    <div class="mt-4 w-full py-2.5 bg-black/5 border border-black/5 rounded-xl text-[10px] font-black uppercase tracking-widest text-white/40 text-center backdrop-blur-sm">
                        Đang khóa
                    </div>
                `;
            }

            let alertHtml = '';
            if (room.status === 'Maintenance' && room.maintenance_since) {
                const maintenanceDate = new Date(room.maintenance_since.replace(' ', 'T'));
                if ((new Date() - maintenanceDate) / 36e5 > 24) {
                    alertHtml = `<div class="mt-3 px-3 py-1.5 bg-rose-500/20 border border-rose-500/30 rounded-lg text-[9px] font-black text-rose-100 uppercase animate-pulse flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i> Trễ bảo trì
                    </div>`;
                }
            }

            card.innerHTML = `
                <div class="relative p-6 rounded-[2rem] bg-${ui.color}-600 shadow-xl shadow-${ui.color}-100 overflow-hidden group-hover:scale-[1.02] transition-all duration-300">
                    <div class="absolute -right-4 -top-4 text-white/10 text-7xl rotate-12 group-hover:scale-110 transition-transform">
                        <i class="fa-solid ${ui.icon}"></i>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[10px] font-black text-white/60 uppercase tracking-widest">Phòng ${room.room_number}</span>
                            <span class="px-2 py-1 bg-white/20 rounded-lg text-[9px] font-black text-white uppercase">Tầng ${room.floor}</span>
                        </div>
                        
                        <h4 class="text-white font-black text-sm truncate uppercase tracking-tight mb-1">${room.room_type_name || 'Hạng phòng'}</h4>
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            <span class="text-[10px] font-black text-white/80 uppercase tracking-widest">${ui.label}</span>
                        </div>
                        
                        ${alertHtml}
                        ${actionBtn}
                    </div>
                </div>
            `;
            gridContainer.appendChild(card);
        });

        gridContainer.querySelectorAll('.btn-change-status').forEach(btn => {
            btn.onclick = function() {
                openModal(this.dataset.id, this.dataset.status, this.dataset.number);
            };
        });
    }

    function openModal(id, status, roomNum) {
        if (!statusModal) return;
        modalRoomId.value = id;
        statusModalTitle.textContent = `Phòng ${roomNum}: Cập nhật`;
        modalNotes.value = '';
        
        const allowed = getAllowedTransitions(status, window.userRole);
        modalNewStatus.innerHTML = '<option value="">-- Chọn trạng thái mới --</option>';
        allowed.forEach(st => {
            const opt = document.createElement('option');
            opt.value = st;
            opt.textContent = (statusMap[st]?.label || st).toUpperCase();
            modalNewStatus.appendChild(opt);
        });
        statusModal.show();
    }

    if (btnSaveStatus) {
        btnSaveStatus.onclick = function() {
            const id = modalRoomId.value;
            const newSt = modalNewStatus.value;
            const notes = modalNotes.value;
            if (!newSt) return;

            btnSaveStatus.disabled = true;
            btnSaveStatus.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...';

            fetch(`${window.apiUrl}/${id}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newSt, notes: notes })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    statusModal.hide();
                    fetchRooms();
                } else {
                    alert('Lỗi: ' + res.message);
                }
            })
            .catch(err => alert('Không thể kết nối máy chủ'))
            .finally(() => {
                btnSaveStatus.disabled = false;
                btnSaveStatus.textContent = 'Lưu thay đổi';
            });
        };
    }

    filterFloor.onchange = renderGrid;
    filterStatus.onchange = renderGrid;

    fetchRooms();
    setInterval(fetchRooms, 5000); // 5s refresh
});

