<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .method-item { cursor: pointer; border: 2px solid #eee; transition: all 0.3s; }
        .method-item.selected { border-color: #0d6efd; background-color: #f8f9ff; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0" style="border-radius: 15px;">
                <div class="card-header bg-primary text-white text-center py-3" style="border-radius: 15px 15px 0 0;">
                    <h4 class="mb-0">Thanh toán đơn hàng #<?= $booking['id'] ?></h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <span class="text-muted">Tổng số tiền cần thanh toán</span>
                        <h2 class="text-danger fw-bold"><?= number_format($booking['total_price']) ?> VND</h2>
                    </div>

                    <h6>Chọn phương thức thanh toán:</h6>
                    <div class="list-group mb-4">
                        <div class="list-group-item method-item p-3 mb-2 rounded selected" onclick="selectMethod('vnpay', this)">
                            <input class="form-check-input me-2" type="radio" name="pay_method" value="vnpay" checked>
                            <strong>Thanh toán qua VNPay</strong>
                        </div>
                        <div class="list-group-item method-item p-3 rounded" onclick="selectMethod('cod', this)">
                            <input class="form-check-input me-2" type="radio" name="pay_method" value="cod">
                            <strong>Thanh toán tại quầy (COD)</strong>
                        </div>
                    </div>

                    <button id="btn-process" onclick="handlePayment()" class="btn btn-primary btn-lg w-100 fw-bold">
                        Xác nhận thanh toán
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentMethod = 'vnpay';

    function selectMethod(method, element) {
        currentMethod = method;
        document.querySelectorAll('.method-item').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        element.querySelector('input').checked = true;
    }

    function handlePayment() {
    const baseUrl = '<?= rtrim(APP_URL, '/') ?>';
    const bookingId = '<?= $booking['id'] ?>';

        if (currentMethod === 'vnpay') {
            window.location.href = baseUrl + '/payment/vnpay?booking_id=' + bookingId;
        } else {
            // QUAN TRỌNG: Phải có tham số payment_method=cod ở cuối URL
            if (confirm('Xác nhận thanh toán tại khách sạn?')) {
                window.location.href = baseUrl + '/booking/success?id=' + bookingId + '&payment_method=cod';
            }
        }
    }
</script>
</body>
</html>
