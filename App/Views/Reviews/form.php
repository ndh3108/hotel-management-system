<?php
$room = $room ?? null;
$bookingId = $bookingId ?? 0;
// Lấy room_type_id từ biến $room hoặc từ biến $roomTypeId truyền từ Controller
$roomTypeId = $room['room_type_id'] ?? ($roomTypeId ?? 0);
$baseUrl = rtrim(APP_URL ?? '', '/');
$submitToken = md5(uniqid((string)rand(), true));
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh giá phòng - Booking Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .review-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .review-card {
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .review-header {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            padding: 30px 40px;
            color: white;
        }

        .review-header h1 {
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .review-header h1 i {
            color: #f59e0b;
        }

        .review-content {
            padding: 40px;
        }

        .room-info {
            background: #f8fafc;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }

        .room-info h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .rating-section {
            margin-bottom: 30px;
        }

        .rating-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #1e293b;
        }

        .stars {
            display: flex;
            /* Sửa lỗi dàn hàng dọc */
            gap: 15px;
            direction: rtl;
            justify-content: flex-end;
        }

        /* Tạo lưới 2 cột cho các mục đánh giá phụ */
        .rating-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .stars input {
            display: none;
        }

        .stars label {
            font-size: 28px;
            color: #cbd5e1;
            cursor: pointer;
            transition: all 0.2s;
        }

        .stars label:hover,
        .stars label:hover~label,
        .stars input:checked~label {
            color: #f59e0b;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
            resize: vertical;
        }

        textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .image-upload {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .image-upload:hover {
            border-color: #3b82f6;
            background: #f8fafc;
        }

        .image-upload input {
            display: none;
        }

        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .preview-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 40px;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }

        .error-message,
        .success-message {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .error-message {
            background: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .success-message {
            background: #d1fae5;
            color: #059669;
            border-left: 4px solid #059669;
        }

        @media (max-width: 768px) {

            .review-header,
            .review-content {
                padding: 24px;
            }

            .stars label {
                font-size: 24px;
            }
        }

        .text-danger {
            color: #ef4444;
        }

        .stars label i {
            pointer-events: none;
        }

        /* Fix icon khi checked */
        .stars input:checked~label i {
            font-weight: 900;
            /* FontAwesome solid */
        }
    </style>
</head>

<body>
    <div class="review-container">
        <div class="review-card">
            <div class="review-header">
                <h1>
                    <i class="fa-solid fa-star"></i>
                    Đánh giá trải nghiệm
                </h1>
                <p>Cảm ơn bạn đã tin tưởng dịch vụ của chúng tôi</p>
            </div>

            <div class="review-content">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="error-message">
                        <i class="fa-solid fa-circle-exclamation"></i> <?= $_SESSION['error'] ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="room-info">
                    <h3>Phòng: <?= htmlspecialchars($room['room_number'] ?? 'N/A') ?></h3>
                    <p style="color: #64748b; font-size: 15px;">
                        Loại phòng: <strong><?= htmlspecialchars($room['name'] ?? $room['room_type_name'] ?? 'Chưa xác định') ?></strong>
                    </p>
                </div>

                <form method="POST" action="<?= $baseUrl ?>/review/store" enctype="multipart/form-data">
                    <input type="hidden" name="booking_id" value="<?= $bookingId ?>">
                    <input type="hidden" name="room_id" value="<?= $room['id'] ?? 0 ?>">
                    <input type="hidden" name="room_type_id" value="<?= $roomTypeId ?>">
                    <input type="hidden" name="submit_token" value="<?= htmlspecialchars($submitToken ?? '') ?>">

                    <div class="rating-section">
                        <div class="rating-title">Đánh giá chung của bạn <span class="text-danger">*</span></div>
                        <div class="stars">
                            <input type="radio" name="rating" value="5" id="star5" required>
                            <label for="star5" title="Rất tốt"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" name="rating" value="4" id="star4">
                            <label for="star4" title="Tốt"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" name="rating" value="3" id="star3">
                            <label for="star3" title="Bình thường"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" name="rating" value="2" id="star2">
                            <label for="star2" title="Tệ"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" name="rating" value="1" id="star1">
                            <label for="star1" title="Rất tệ"><i class="fa-solid fa-star"></i></label>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="rating-section">
                            <div class="rating-title">Cơ sở vật chất <span class="text-danger">*</span></div>
                            <div class="stars">
                                <input type="radio" name="rating_room" value="5" id="room5" required>
                                <label for="room5"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_room" value="4" id="room4">
                                <label for="room4"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_room" value="3" id="room3">
                                <label for="room3"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_room" value="2" id="room2">
                                <label for="room2"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_room" value="1" id="room1">
                                <label for="room1"><i class="fa-solid fa-star"></i></label>
                            </div>
                        </div>

                        <div class="rating-section">
                            <div class="rating-title">Dịch vụ khách hàng <span class="text-danger">*</span></div>
                            <div class="stars">
                                <input type="radio" name="rating_service" value="5" id="serv5" required>
                                <label for="serv5"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_service" value="4" id="serv4">
                                <label for="serv4"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_service" value="3" id="serv3">
                                <label for="serv3"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_service" value="2" id="serv2">
                                <label for="serv2"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_service" value="1" id="serv1">
                                <label for="serv1"><i class="fa-solid fa-star"></i></label>
                            </div>
                        </div>

                        <div class="rating-section">
                            <div class="rating-title">Vệ sinh phòng <span class="text-danger">*</span></div>
                            <div class="stars">
                                <input type="radio" name="rating_cleanliness" value="5" id="clean5" required>
                                <label for="clean5"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_cleanliness" value="4" id="clean4">
                                <label for="clean4"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_cleanliness" value="3" id="clean3">
                                <label for="clean3"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_cleanliness" value="2" id="clean2">
                                <label for="clean2"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_cleanliness" value="1" id="clean1">
                                <label for="clean1"><i class="fa-solid fa-star"></i></label>
                            </div>
                        </div>

                        <div class="rating-section">
                            <div class="rating-title">Vị trí thuận tiện <span class="text-danger">*</span></div>
                            <div class="stars">
                                <input type="radio" name="rating_location" value="5" id="loc5" required>
                                <label for="loc5"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_location" value="4" id="loc4">
                                <label for="loc4"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_location" value="3" id="loc3">
                                <label for="loc3"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_location" value="2" id="loc2">
                                <label for="loc2"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" name="rating_location" value="1" id="loc1">
                                <label for="loc1"><i class="fa-solid fa-star"></i></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Chia sẻ chi tiết <span class="text-danger">*</span></label>
                        <textarea name="content" rows="4" placeholder="Điều gì làm bạn hài lòng hoặc chưa hài lòng?" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Hình ảnh thực tế (Tối đa 5 ảnh)</label>
                        <div class="image-upload" onclick="document.getElementById('image-input').click()">
                            <i class="fa-solid fa-camera" style="font-size: 32px; color: #6366f1; margin-bottom: 10px;"></i>
                            <p>Chọn ảnh từ thiết bị</p>
                            <input type="file" id="image-input" name="images[]" multiple accept="image/*" style="display: none;">
                        </div>
                        <div id="image-preview" class="image-preview"></div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-paper-plane"></i> Gửi đánh giá ngay
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Preview ảnh trước khi upload
        document.getElementById('image-input').addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';

            const files = Array.from(e.target.files).slice(0, 5);
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.style.position = 'relative';
                        div.innerHTML = `<img src="${e.target.result}" class="preview-img">`;
                        preview.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });

        // Một chút hiệu ứng tương tác cho các ngôi sao
        document.querySelectorAll('.stars label').forEach(label => {
            label.addEventListener('click', function() {
                const parent = this.parentElement;
                const stars = parent.querySelectorAll('i');
                // Hiệu ứng nhẹ khi chọn
            });
        });
    </script>
</body>

</html>
