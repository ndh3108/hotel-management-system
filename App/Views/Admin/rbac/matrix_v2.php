<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:20px;">
    <div class="admin-card">
        <div class="admin-card-body">
            <div class="label">Roles</div>
            <div class="value"><?= (int) ($roleCount ?? 0) ?></div>
            <small><?= (int) ($activeRoleCount ?? 0) ?> đang hoạt động</small>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <div class="label">Permissions</div>
            <div class="value"><?= (int) ($permissionCount ?? 0) ?></div>
            <small><?= (int) ($activePermissionCount ?? 0) ?> đang hoạt động</small>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <div class="label">System Roles</div>
            <div class="value system"><?= (int) ($systemRoleCount ?? 0) ?></div>
            <small>Không thể xóa</small>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h2>Ma trận phân quyền</h2>

        <div class="actions">
            <a href="<?= \App\Core\url('/admin/roles') ?>" class="btn">Vai trò</a>
            <a href="<?= \App\Core\url('/admin/permissions') ?>" class="btn">Permission</a>
            <button onclick="loadMatrix()" class="btn primary">Tải lại</button>
        </div>
    </div>

    <div class="admin-card-body" id="matrix-container">
        <p class="loading">Đang tải...</p>
    </div>
</div>

<script>
    let CURRENT_MATRIX = {};

    function loadMatrix() {
        const container = document.getElementById('matrix-container');
        container.innerHTML = `<p class="loading">Đang tải...</p>`;

        fetch('<?= \App\Core\url('/api/permissions') ?>')
            .then(res => res.json())
            .then(data => {
                if (!data.success) throw new Error(data.message);

                CURRENT_MATRIX = data.matrix || {};
                renderMatrix(data.roles, data.permissions);
            })
            .catch(err => {
                container.innerHTML = `<p class="error">${err.message}</p>`;
            });
    }

    function renderMatrix(roles, permissions) {
        const grouped = {};

        permissions.forEach(p => {
            const key = p.module || 'General';
            if (!grouped[key]) grouped[key] = [];
            grouped[key].push(p);
        });

        let html = `<table class="admin-table">`;

        // HEADER
        html += `<thead><tr>
        <th>Permission</th>`;

        roles.forEach(role => {
            html += `
        <th>
            <div>
                <strong>${role.name}</strong>
                <small>${role.code}</small>
            </div>
        </th>`;
        });

        html += `</tr></thead><tbody>`;

        // BODY
        Object.entries(grouped).forEach(([module, perms]) => {

            html += `<tr class="module-row">
            <td colspan="${roles.length + 1}">${module}</td>
        </tr>`;

            perms.forEach(p => {
                html += `<tr>
                <td>
                    <strong>${p.name}</strong><br>
                    <small>${p.code}</small>
                </td>`;

                roles.forEach(role => {
                    const checked = CURRENT_MATRIX[role.id]?.includes(p.id);

                    // ✅ FIX QUAN TRỌNG
                    const disabled =
                        (role.code === 'super_admin') || // chỉ khóa super_admin
                        (role.is_active == 0) ||
                        (p.is_active == 0);

                    html += `
                <td class="text-center">
                    <input type="checkbox"
                        ${checked ? 'checked' : ''}
                        ${disabled ? 'disabled' : ''}
                        onchange="updatePermission(this, ${role.id}, ${p.id})">
                </td>`;
                });

                html += `</tr>`;
            });
        });

        html += `</tbody></table>`;

        document.getElementById('matrix-container').innerHTML = html;
    }

    function updatePermission(el, roleId, permissionId) {
        const checked = el.checked;

        // backup
        const old = JSON.parse(JSON.stringify(CURRENT_MATRIX));

        if (!CURRENT_MATRIX[roleId]) CURRENT_MATRIX[roleId] = [];

        if (checked) {
            CURRENT_MATRIX[roleId].push(permissionId);
        } else {
            CURRENT_MATRIX[roleId] =
                CURRENT_MATRIX[roleId].filter(id => id !== permissionId);
        }

        el.disabled = true;

        fetch('<?= \App\Core\url('/api/permissions-to-role') ?>', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    role_id: roleId,
                    permission_ids: CURRENT_MATRIX[roleId]
                })
            })
            .then(res => res.json())
            .then(data => {
                el.disabled = false;

                if (!data.success) {
                    throw new Error(data.message);
                }
            })
            .catch(err => {
                alert("Lỗi: " + err.message);

                // rollback
                CURRENT_MATRIX = old;
                el.checked = !checked;
                el.disabled = false;
            });
    }

    document.addEventListener('DOMContentLoaded', loadMatrix);
</script>

<style>
    .label {
        font-size: 12px;
        color: #888
    }

    .value {
        font-size: 26px;
        font-weight: bold;
        color: #2563eb
    }

    .value.system {
        color: #0f766e
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table th,
    .admin-table td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .module-row {
        background: #f1f5f9;
        font-weight: bold;
    }

    .text-center {
        text-align: center;
    }

    .loading {
        text-align: center;
        padding: 20px;
    }

    .error {
        color: red;
        padding: 20px;
    }

    .btn {
        padding: 6px 10px;
        border: 1px solid #ccc;
        cursor: pointer;
    }

    .btn.primary {
        background: #2563eb;
        color: #fff;
    }
</style>
