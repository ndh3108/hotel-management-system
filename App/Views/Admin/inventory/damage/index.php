<div class="admin-card">
    <div class="admin-card-header" style="flex-direction: column; align-items: flex-start; gap: 15px;">
        <h2 class="admin-card-title"><?= htmlspecialchars($pageTitle) ?></h2>
        <div class="admin-filter-tabs" style="display: flex; gap: 8px; flex-wrap: wrap;">
            <?php 
                $tabs = [
                    'Reported' => 'Đang chờ duyệt', 
                    'Approved' => 'Đã duyệt (Chờ đóng tiền)', 
                    'Paid'     => 'Đã đóng tiền (Chờ thay thế)',
                    'Replaced' => 'Đã thay thế/Hoàn tất',
                    'Rejected' => 'Đã từ chối'
                ];
                foreach($tabs as $key => $label):
            ?>
                <a href="<?= \App\Core\url('/admin/inventory/damage?status='.$key) ?>" 
                   class="admin-btn <?= $currentStatus === $key ? 'admin-btn-primary' : 'admin-btn-outline' ?>" 
                   style="font-size: 0.8rem; padding: 6px 12px; border-radius: 20px;">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="admin-card-body">
        <?php if (isset($_GET['success'])): ?>
            <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;">
                <i class="fa-solid fa-circle-check"></i> Cập nhật trạng thái thành công!
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['msg'])): ?>
            <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; background: #e0f2fe; color: #075985; border: 1px solid #bae6fd;">
                <i class="fa-solid fa-info-circle"></i> <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <div class="admin-table-wrap">
            <table class="admin-table admin-table-min">
                <thead>
                    <tr>
                        <th style="width: 130px;">Thời gian</th>
                        <th style="width: 100px;">Phòng</th>
                        <th>Khách hàng & Đơn đặt</th>
                        <th>Vật tư hỏng hóc</th>
                        <th>Thành tiền</th>
                        <th style="width: 150px;">Người báo cáo</th>
                        <th style="width: 180px; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($damages)): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 60px; color: #94a3b8;">
                            <i class="fa-solid fa-clipboard-check" style="font-size: 3rem; display: block; margin-bottom: 15px; opacity: 0.3;"></i>
                            Không có dữ liệu báo cáo ở trạng thái này.
                        </td></tr>
                    <?php else: foreach ($damages as $d): ?>
                        <tr>
                            <td>
                                <div style="font-size: 0.85rem; font-weight: 500;"><?= date('d/m/Y', strtotime($d['created_at'])) ?></div>
                                <div style="font-size: 0.75rem; color: #64748b;"><?= date('H:i', strtotime($d['created_at'])) ?></div>
                            </td>
                            <td>
                                <span style="display: inline-block; padding: 4px 10px; background: #eff6ff; color: #1e40af; border-radius: 6px; font-weight: 700; border: 1px solid #dbeafe;">
                                    #<?= htmlspecialchars($d['room_number']) ?>
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($d['customer_name'] ?? 'Vãng lai') ?></div>
                                <small style="color: #64748b; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">Đơn #<?= (int)$d['booking_id'] ?></small>
                            </td>
                            <td>
                                <div style="font-weight: 600;"><?= htmlspecialchars($d['item_name_at_report']) ?></div>
                                <div style="color: #64748b; font-size: 0.85rem;">Số lượng: <b style="color: #0f172a;"><?= (int)$d['quantity'] ?></b></div>
                            </td>
                            <td>
                                <div style="color: #ef4444; font-weight: 800; font-size: 1rem;">
                                    <?= number_format($d['price'] * $d['quantity'], 0, ',', '.') ?>₫
                                </div>
                                <small style="display: block; color: #94a3b8; font-size: 0.7rem;">Đơn giá: <?= number_format($d['price'], 0, ',', '.') ?>₫</small>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700;">
                                        <?= strtoupper(substr($d['reported_by_name'] ?? 'S', 0, 1)) ?>
                                    </div>
                                    <span style="font-size: 0.85rem; color: #475569;"><?= htmlspecialchars($d['reported_by_name'] ?? 'Hệ thống') ?></span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <?php if ($currentStatus === 'Reported'): ?>
                                        <form action="<?= \App\Core\url('/admin/inventory/damage/approve/'.$d['id']) ?>" method="POST">
                                            <button type="submit" class="admin-btn admin-btn-primary" style="padding: 6px 12px; font-size: 0.75rem;">
                                                <i class="fa-solid fa-check"></i> Duyệt
                                            </button>
                                        </form>
                                        <form action="<?= \App\Core\url('/admin/inventory/damage/reject/'.$d['id']) ?>" method="POST">
                                            <button type="submit" class="admin-btn admin-btn-outline" style="padding: 6px 12px; font-size: 0.75rem; color: #ef4444; border-color: #fee2e2;">
                                                <i class="fa-solid fa-xmark"></i> Bỏ qua
                                            </button>
                                        </form>
                                    <?php elseif ($currentStatus === 'Approved'): ?>
                                        <form action="<?= \App\Core\url('/admin/inventory/damage/pay/'.$d['id']) ?>" method="POST">
                                            <button type="submit" class="admin-btn admin-btn-success" style="padding: 6px 12px; font-size: 0.75rem; background: #10b981; border-color: #10b981; color: white;">
                                                <i class="fa-solid fa-hand-holding-dollar"></i> Đã thu tiền
                                            </button>
                                        </form>
                                    <?php elseif ($currentStatus === 'Paid'): ?>
                                        <form action="<?= \App\Core\url('/admin/inventory/damage/replace/'.$d['id']) ?>" method="POST" onsubmit="return confirm('Hệ thống sẽ trừ số lượng trong kho và cập nhật trạng thái phòng. Tiếp tục?')">
                                            <button type="submit" class="admin-btn admin-btn-primary" style="padding: 6px 12px; font-size: 0.75rem; background: #6366f1; border-color: #6366f1;">
                                                <i class="fa-solid fa-box-open"></i> Xuất kho / Thay thế
                                            </button>
                                        </form>
                                    <?php elseif ($currentStatus === 'Replaced'): ?>
                                        <span style="color: #059669; font-weight: 700; font-size: 0.8rem;">
                                            <i class="fa-solid fa-circle-check"></i> Hoàn tất
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= $currentStatus ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 20px;">
            <?php if ($pageNumber > 1): ?>
                <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/inventory/damage?status='.$currentStatus.'&page=' . ($pageNumber - 1)) ?>" style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 6px; color: #475569; text-decoration: none;">&laquo; Trước</a>
            <?php endif; ?>
            
            <span style="font-weight: 600; color: #475569;">Trang <?= $pageNumber ?></span>
            
            <?php if ($hasMore): ?>
                <a class="admin-pagination-link" href="<?= \App\Core\url('/admin/inventory/damage?status='.$currentStatus.'&page=' . ($pageNumber + 1) . '&cursor=' . $nextCursor) ?>" style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 6px; color: #475569; text-decoration: none;">Sau &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
    </div>
</div>

