<div class="max-w-[1600px] mx-auto space-y-10 animate-fade-in pb-20 px-4">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 italic font-black group/header hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-gamepad"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic">Ma trận Phân quyền Hệ thống</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Role-Based Access Control Architecture</p>
            </div>
        </div>
        <div class="flex items-center gap-4 flex-wrap">
            <a href="<?= \App\Core\url('/admin/roles') ?>" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest italic text-slate-500 hover:text-indigo-600 hover:border-indigo-100 hover:bg-indigo-50/30 transition-all flex items-center gap-3 shadow-sm active:scale-95">
                <i class="fa-solid fa-user-tag text-xs"></i> Vai trò (<?= (int) ($roleCount ?? 0) ?>)
            </a>
            <a href="<?= \App\Core\url('/admin/permissions') ?>" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest italic text-slate-500 hover:text-indigo-600 hover:border-indigo-100 hover:bg-indigo-50/30 transition-all flex items-center gap-3 shadow-sm active:scale-95">
                <i class="fa-solid fa-key text-xs"></i> Quyền (<?= (int) ($permissionCount ?? 0) ?>)
            </a>
            <button type="button" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic hover:bg-slate-900 transition-all shadow-xl shadow-indigo-100 flex items-center gap-3 active:scale-95" onclick="loadMatrix()">
                <i class="fa-solid fa-rotate text-xs"></i> Tải lại Ma trận
            </button>
        </div>
    </div>

    <!-- Matrix Container Card -->
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden relative min-h-[400px]">
        <div id="matrix-container" class="overflow-x-auto">
            <!-- Matrix will be rendered here by JS -->
            <div class="flex flex-col items-center justify-center gap-6 py-32 text-center">
                <div class="w-20 h-20 bg-slate-50 border border-slate-50 rounded-[2rem] flex items-center justify-center text-slate-300 text-3xl animate-spin">
                    <i class="fa-solid fa-circle-notch"></i>
                </div>
                <div class="space-y-2">
                    <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[11px] italic">Khởi tạo Ma trận Quyền hạn...</p>
                    <p class="text-slate-300 text-[9px] font-bold italic uppercase tracking-widest">Building RBAC Grid from database hooks</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function loadMatrix() {
        const container = document.getElementById('matrix-container');
        container.innerHTML = `<div class="flex flex-col items-center justify-center gap-6 py-32 text-center"><div class="w-20 h-20 bg-slate-50 border border-slate-50 rounded-[2rem] flex items-center justify-center text-slate-300 text-3xl animate-spin"><i class="fa-solid fa-circle-notch"></i></div><div class="space-y-2"><p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[11px] italic font-black">Syncing access tokens...</p></div></div>`;

        fetch('<?= \App\Core\url('/api/permissions') ?>')
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    container.innerHTML = `<div class="p-20 text-center text-rose-600 font-black italic uppercase tracking-widest text-xs">Critical Error: ${data.message}</div>`;
                    return;
                }
                renderMatrix(data.roles, data.permissions, data.matrix);
            })
            .catch(err => {
                container.innerHTML = `<div class="p-20 text-center text-rose-600 font-black italic uppercase tracking-widest text-xs">Network Connection Failure: ${err.message}</div>`;
            });
    }

    function renderMatrix(roles, permissions, matrix) {
        let html = '<table class="w-full text-left border-collapse min-w-[800px]"><thead><tr class="bg-indigo-900 px-6 py-8"><th class="pl-10 pr-6 py-8 text-[11px] font-black text-white italic uppercase tracking-[0.2em] bg-indigo-900 border-r border-white/5">Quyền hạn / Chức năng</th>';
        
        roles.forEach(role => {
            html += `<th class="px-6 py-8 text-[10px] font-black text-white italic text-center uppercase tracking-[0.2em] bg-indigo-950/50 backdrop-blur-md">${escapeHtml(role.name)}</th>`;
        });
        html += '</tr></thead><tbody class="divide-y divide-slate-100">';

        const grouped = {};
        permissions.forEach(p => {
            const module = p.block_module || p.module || 'Cấu hình Hệ thống';
            if (!grouped[module]) grouped[module] = [];
            grouped[module].push(p);
        });

        for (const [module, perms] of Object.entries(grouped)) {
            html += `<tr class="bg-slate-50/80"><td colspan="${roles.length + 1}" class="pl-10 py-5"><div class="flex items-center gap-3"><div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-indigo-500 text-[10px]"><i class="fa-solid fa-cube"></i></div><span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest italic leading-none">${escapeHtml(module)} cluster</span></div></td></tr>`;
            
            perms.forEach(perm => {
                html += `<tr class="hover:bg-indigo-50/20 transition-all duration-300 group/row">`;
                html += `<td class="pl-10 pr-6 py-6 border-r border-slate-50 flex flex-col gap-1 justify-center"><span class="text-[11px] font-black text-slate-800 uppercase italic leading-tight group-hover/row:text-indigo-600 transition-colors">${escapeHtml(perm.name)}</span><span class="text-[8px] font-black text-slate-300 uppercase italic tracking-widest">${escapeHtml(perm.code)}</span></td>`;
                
                roles.forEach(role => {
                    const hasPerm = matrix[role.id] && matrix[role.id].includes(perm.id);
                    const isAdmin = role.name === 'Admin';
                    const checked = isAdmin || hasPerm ? 'checked' : '';
                    const disabledAttr = isAdmin ? 'disabled' : '';
                    
                    html += `<td class="px-6 py-6 text-center">
                        <div class="flex items-center justify-center">
                            <label class="relative inline-flex items-center cursor-pointer group/label">
                                <input type="checkbox" 
                                       onchange="togglePermission(${role.id}, ${perm.id}, this.checked)"
                                       ${checked} ${disabledAttr} 
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 ${isAdmin ? 'opacity-50 cursor-not-allowed' : ''}"></div>
                                ${isAdmin ? '<div class="absolute -top-1 -right-1 w-3 h-3 bg-indigo-500 text-white rounded-full flex items-center justify-center border-2 border-white shadow-sm ring-white ring-2"><i class="fa-solid fa-lock text-[5px]"></i></div>' : ''}
                            </label>
                        </div>
                    </td>`;
                });
                html += `</tr>`;
            });
        }

        html += '</tbody></table>';
        container.innerHTML = html;
        window.CURRENT_MATRIX_DATA = { roles, permissions, matrix };
    }

    function togglePermission(roleId, permissionId, isChecked) {
        const matrix = window.CURRENT_MATRIX_DATA.matrix;
        if (!matrix[roleId]) matrix[roleId] = [];
        
        if (isChecked) {
            if (!matrix[roleId].includes(permissionId)) matrix[roleId].push(permissionId);
        } else {
            matrix[roleId] = matrix[roleId].filter(id => id !== permissionId);
        }

        fetch('<?= \App\Core\url('/api/permissions-to-role') ?>', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ role_id: roleId, permission_ids: matrix[roleId] })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('RBAC Integrity Verified');
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
                loadMatrix();
            }
        })
        .catch(err => {
            alert('Lỗi kết nối database: ' + err.message);
            loadMatrix();
        });
    }

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe.toString()
             .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    document.addEventListener('DOMContentLoaded', loadMatrix);
</script>

