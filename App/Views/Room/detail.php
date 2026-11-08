<div class="container" style="padding-top: 100px; max-width: 1100px;">
    <div class="nav" style="margin-bottom: 30px;">
        <a href="/rooms/search?check_in=<?= urlencode($checkIn) ?>&check_out=<?= urlencode($checkOut) ?>"
            style="color: var(--primary); font-weight: 500;">← Quay lại tìm kiếm</a>
    </div>

    <div
        style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 40px; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <!-- Left: Images -->
        <div class="room-gallery">
            <div style="border-radius: 15px; overflow: hidden; margin-bottom: 20px; height: 400px;">
                <?php
                $primaryImage = \App\Core\url('assets/image/image.png');
                if (!empty($roomType['images'][0]['image_url'])) {
                    $primaryImage = \App\Core\url((string) $roomType['images'][0]['image_url']);
                }
                ?>
                <img src="<?= htmlspecialchars($primaryImage) ?>" alt="<?= htmlspecialchars($roomType['name']) ?>"
                    style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <?php if (!empty($roomType['images']) && count($roomType['images']) > 1): ?>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                    <?php foreach ($roomType['images'] as $img): ?>
                        <div style="height: 80px; border-radius: 8px; overflow: hidden; cursor: pointer;">
                            <img src="<?= htmlspecialchars(\App\Core\url($img['image_url'] ?? '')) ?>"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Info & Booking -->
        <div class="room-info">
            <span style="color: var(--primary); font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Phòng
                <?= htmlspecialchars($room['room_number']) ?></span>
            <h1 style="font-size: 2.5rem; margin: 10px 0 20px;"><?= htmlspecialchars($roomType['name']) ?></h1>

            <div class="room-amenities" style="margin-bottom: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <span><i class="fa-solid fa-users"></i> <strong>Sức chứa:</strong> <?= $roomType['capacity_adults'] + ($roomType['capacity_children'] ?? 0) ?>
                        người</span>
                    <span><i class="fa-solid fa-expand"></i> <strong>Diện tích:</strong>
                        <?= $roomType['area_sqm'] ?>m²</span>
                </div>
            </div>

            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 15px;">Mô tả phòng:</h4>
                <p style="color: #666; line-height: 1.6;"><?= htmlspecialchars($roomType['description'] ?? '') ?></p>
            </div>

            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 15px;">Tiện nghi phòng:</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <?php if (!empty($roomType['amenities'])): ?>
                        <?php foreach ($roomType['amenities'] as $amenity): ?>
                            <span
                                style="background: #f8f9fa; padding: 8px 15px; border-radius: 20px; font-size: 0.9rem; border: 1px solid #eee; color: #475569; font-weight: 500;">
                                <i class="fa-solid fa-check text-success" style="font-size: 0.7rem; margin-right: 5px;"></i>
                                <?= htmlspecialchars($amenity['name']) ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 15px;">Vật tư & Trang thiết bị:</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <?php if (!empty($roomType['supplies'])): ?>
                        <?php foreach ($roomType['supplies'] as $supply): ?>
                            <div style="font-size: 0.9rem; color: #64748b; display: flex; align-items: center; gap: 10px;">
                                <i class="fa-solid fa-box" style="font-size: 0.7rem; color: var(--primary);"></i>
                                <span><?= htmlspecialchars($supply['name']) ?> (x<?= $supply['quantity'] ?>)</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div style="background: #fdf8f4; padding: 25px; border-radius: 15px; border: 1px solid #ffe8d6;">
                <div class="room-price" style="margin-bottom: 20px; display: flex; align-items: baseline; gap: 5px;">
                    <span class="amount"
                        style="font-size: 2rem;"><?= number_format($roomType['base_price'], 0, ',', '.') ?>đ</span>
                    <span class="period">/ đêm</span>
                </div>

                <?php
                // Tính số đêm và tổng tiền
                $nights = 1;
                if ($checkIn && $checkOut) {
                    $nights = max(1, (strtotime($checkOut) - strtotime($checkIn)) / 86400);
                }
                $totalPrice = $roomType['base_price'] * $nights;
                ?>
                
                <button type="button" class="btn btn-primary" id="show-booking-modal"
                    style="width: 100%; padding: 15px; font-size: 1.1rem; border-radius: 10px; cursor: pointer;">
                    Tiếp Tục Đặt Phòng
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Popup Xác Nhận Đặt Phòng -->
<div id="booking-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 32px; max-width: 500px; width: 90%; padding: 40px; text-align: center; animation: modalSlideIn 0.4s ease;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
            <i class="fa-solid fa-circle-check" style="font-size: 40px; color: white;"></i>
        </div>
        <h2 style="font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">Xác nhận đặt phòng</h2>
        <p style="color: #64748b; line-height: 1.6; margin-bottom: 24px;">Vui lòng kiểm tra lại thông tin đặt phòng của bạn</p>
        
        <div id="booking-details" style="background: #f8fafc; border-radius: 16px; padding: 20px; margin: 20px 0; text-align: left;">
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                <span><i class="fa-solid fa-door-open"></i> Phòng:</span>
                <strong><?= htmlspecialchars($room['room_number']) ?> - <?= htmlspecialchars($roomType['name']) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                <span><i class="fa-solid fa-calendar-check"></i> Nhận phòng:</span>
                <strong><?= date('d/m/Y', strtotime($checkIn)) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                <span><i class="fa-solid fa-calendar-times"></i> Trả phòng:</span>
                <strong><?= date('d/m/Y', strtotime($checkOut)) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                <span><i class="fa-solid fa-moon"></i> Số đêm:</span>
                <strong><?= $nights ?> đêm</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                <span><i class="fa-solid fa-money-bill-wave"></i> Tổng tiền:</span>
                <strong style="color: #f59e0b; font-size: 18px;"><?= number_format($totalPrice, 0, ',', '.') ?> VND</strong>
            </div>
        </div>
        
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button id="modal-cancel-btn" style="padding: 12px 24px; border-radius: 40px; font-weight: 600; cursor: pointer; border: none; background: #e2e8f0; color: #475569; transition: all 0.3s;">
                <i class="fa-solid fa-xmark"></i> Hủy
            </button>
            <button id="modal-confirm-btn" style="padding: 12px 24px; border-radius: 40px; font-weight: 600; cursor: pointer; border: none; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; transition: all 0.3s;">
                <i class="fa-solid fa-check"></i> Xác nhận đặt phòng
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #modal-cancel-btn:hover, #modal-confirm-btn:hover {
        transform: translateY(-2px);
    }
    
    #modal-confirm-btn:hover {
        box-shadow: 0 5px 15px rgba(59,130,246,0.4);
    }
    
    #modal-cancel-btn:hover {
        background: #cbd5e1;
    }
</style>

<script>
    const modal = document.getElementById('booking-modal');
    const showBtn = document.getElementById('show-booking-modal');
    const cancelBtn = document.getElementById('modal-cancel-btn');
    const confirmBtn = document.getElementById('modal-confirm-btn');
    
    // Show modal
    showBtn.addEventListener('click', function() {
        modal.style.display = 'flex';
    });
    
    // Hide modal
    function hideModal() {
        modal.style.display = 'none';
    }
    
    cancelBtn.addEventListener('click', hideModal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });
    
    // Confirm booking
    confirmBtn.addEventListener('click', function() {
        // Disable button and show loading
        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = '<span style="display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: white; animation: spin 0.8s linear infinite;"></span> Đang xử lý...';
        confirmBtn.disabled = true;
        
        // Create form data
        const formData = new FormData();
        formData.append('room_id', '<?= $room['id'] ?>');
        formData.append('check_in', '<?= $checkIn ?>');
        formData.append('check_out', '<?= $checkOut ?>');
        
        // Submit booking
        fetch('<?= \App\Core\url('booking/store') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to success page
                window.location.href = '<?= \App\Core\url('booking/success') ?>?id=' + data.booking_id;
            } else {
                alert(data.error || 'Có lỗi xảy ra. Vui lòng thử lại!');
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại!');
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        });
    });
    
    // Add spin animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
</script>

